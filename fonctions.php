<?php

//Variables de connexion
define("HOST", "localhost");
define("USER", "root");
define("PWD_MYSQL", "");
define("BD_MYSQL", "bd_cpam");
define("PORT", "3306");

//Chemin vers l'espace où sont enregistrés les dossiers des dossiers
//NB : À partir de la racine
define("STORAGE_PATH", "piecesJustificatives");

//Message pour l'assuré (généré en JavaScript)
define("MAIL_REQUEST_SUBJECT", "PJPE - Demande de pièces justificatives");
define("DEPOSITE_LINK", GenererLienDepot());
define("FOOTER_EMAIL", "Merci de ne pas répondre à ce message.");

/* ************************************************ */
/*              FONCTIONS GENERALES                 */
/* ************************************************ */


//  Connexion a la base de donnees
function connexionMySQL()
{
    //$cres = mysqli_connect(SERVER_MYSQL, ID_MYSQL, PWD_MYSQL, BD_MYSQL);
    $link = mysqli_connect(HOST, USER, PWD_MYSQL, BD_MYSQL, PORT);
    mysqli_query($link, 'SET NAMES utf8');

    /* Vérification de la connexion */
    if ($link == NULL) {
        echo "Erreur : Impossible de se connecter à MySQL." . "<br>";
        echo "Errno de débogage : " . mysqli_connect_errno() . "<br>";
        echo "Erreur de débogage : " . mysqli_connect_error() . "<br>";
        exit;
    } else {
        if (mysqli_select_db($link, BD_MYSQL) == null) {
            echo ("<p> Vérifier que la base de données est bien sur MariaDB </p>");
            return null;
        }
    }
    return $link;
}

// Redirection vers une page différente du même dossier
function RedirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    exit;
}

// Retourne le lien à la racine du site WEB
function GenererLienDepot() {
    $host = $_SERVER['HTTP_HOST'];
    return "http://".$_SERVER['HTTP_HOST']."/frontOffice/depot.php";
}

/* ************************************************ */
/*                  FRONT OFFICE                    */
/* ************************************************ */

//Vérifie si la référence d'un dossier est affilié au NIR passé en paramètre
function NirRefExiste($NirA, $RefD, $link) {
    $query = "SELECT a.* FROM Assure a, Dossier d  WHERE a.NirA = '".$NirA."' AND d.CodeA = a.CodeA AND d.RefD = '".$RefD."'" ;
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

// Renvoie un caractère aléatoire compris dans $listeChar
function CaractereAleatoire() {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $listeChar[rand(0, strlen($listeChar) - 1)];
}

//Genère une référence valide pour un dossier
function GenererReferenceDossier($nbChar, $link)
{
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= CaractereAleatoire();
        }
    } while(DossierExiste($ref, $link));

    return $ref;
}


//Renvoie les informations d'un assuré via son NIR sous la forme d'une liste
function ChercherAssureAvecNIR($NirA, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '" . $NirA . "'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Renvoie les informations d'un dossier via sa référence sous la forme d'une liste
function ChercherDossierAvecREF($RefD, $link)
{
    $query = "SELECT * FROM Assure A, Dossier D ";
    $query .= "WHERE A.CodeA = D.CodeA AND RefD = '" . $RefD . "'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Renvoie les informations d'un dossier traité ou en cours de traitemnet
function ChercherDossierTraiteAvecCodeD($CodeD, $link) {
    $query = "SELECT * FROM Assure A, Dossier D, Traiter Tr, Technicien T ";
    $query .= "WHERE A.CodeA = D.CodeA AND D.CodeD = ".$CodeD." ";
    $query .= "AND D.CodeD = Tr.CodeD AND T.CodeT = Tr.CodeT";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Renvoie les informations d'un dossier via sa référence sous la forme d'une liste
function ChercherREFAvecCodeD($CodeD, $link)
{
    $query = "SELECT RefD FROM Assure A, Dossier D ";
    $query .= "WHERE A.CodeA = D.CodeA AND CodeD = '" . $CodeD . "'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Retourne le code correspond au mnémonique entré en paramètre
function ChercherObjetMnemoAvecMnemo($Mnemonique, $link)
{
    $query = "SELECT * FROM Listemnemonique ";
    $query .= "WHERE Mnemonique = '" . $Mnemonique . "'";

    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/*      Vérification d'existence dans la BD        */

//Vérifie si un assuré est déjà enregistré
function AssureExiste($NirA, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '" . $NirA . "'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}

//Vérifie si la référence donnée en paramètre n'est pas déjà utilisé
function DossierExiste($RefD, $link)
{
    $query = "SELECT RefD FROM Dossier WHERE RefD = '" . $RefD . "'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}

// Vérifie si un justificatif est dans la BD
function FichierExiste($link, $path){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '" . $path . "'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}

//Enregistre les données d'un assuré dans la BD
function EnregistrerAssure($NirA, $NomA, $PrenomA, $TelA, $MailA, $link)
{
    $keys = "";
    $values = "";
    if ($NirA != NULL) {
        $keys .= "NirA, ";
        $values .= "'" . $NirA . "', ";
    }
    if ($NomA != NULL) {
        $keys .= "NomA, ";
        $values .= "'" . $NomA . "', ";
    }
    if ($PrenomA != NULL) {
        $keys .= "PrenomA, ";
        $values .= "'" . $PrenomA . "', ";
    }
    if ($TelA != NULL) {
        $TelA = explode(" ", $TelA);
        $TelA = implode($TelA);
        $keys .= "TelA, ";
        $values .= "'" . $TelA . "', ";
    }
    if ($MailA != NULL) {
        $keys .= "MailA, ";
        $values .= "'" . $MailA . "', ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO assure(" . $keys . ") VALUES (" . $values . ")";

    return mysqli_query($link, $query);
}

//Enregistre un dossier puis renvoie True si la manoeuvre a réussi
//False sinon
function EnregistrerDossier($CodeA, $DateAM, $RefD, $link)
{
    $keys = "";
    $values = "";
    $keys .= "RefD, ";
    $values .= "'" . $RefD . "', ";
    $keys .= "DateAM, ";
    $values .= "'" . $DateAM . "', ";
    $keys .= "CodeA, ";
    $values .= $CodeA . ", ";

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO dossier(" . $keys . ") VALUES (" . $values . ")";

    return mysqli_query($link, $query);
}

//Créer le dossier d'un assuré dont le nom est son numéro NIR en local
function CreerDossierNIR($NirA)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $NirA;
    return mkdir($dirname);
}

//Créer le dossier de l'arrêt maladie d'un assuré en local
function CreerDossierAM($RefD, $NirA)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $NirA . "/" . $RefD;
    return mkdir($dirname);
}

//Fonction qui enregistre les données d'un fichier dans la base de données
function EnregistrerFichier($CheminJ, $CodeD, $CodeA, $CodeM, $link)
{
    $keys = "";
    $values = "";
    if ($CheminJ != NULL) {
        $keys .= "CheminJ, ";
        $values .= "'" . $CheminJ . "', ";
    }
    if ($CodeD != NULL) {
        $keys .= "CodeD, ";
        $values .= $CodeD . ", ";
    }
    if ($CodeA != NULL) {
        $keys .= "CodeA, ";
        $values .= $CodeA . ", ";
    }
    if ($CodeM != NULL) {
        $keys .= "CodeM, ";
        $values .= "'" . $CodeM . "', ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO justificatif(" . $keys . ") VALUES (" . $values . ")";

    return mysqli_query($link, $query);
}

//Enregistre les fichiers contenus dans le dossier d'un assuré
//Renvoie une liste avec une ligne pour un fichier
//1er paramètre de type Booléen qui est TRUE si l'enregistrement a réussi, FALSE sinon
//2ème paramètre de type String qui correspond au nom du fichier téléchargé
//3ème paramètre correspond au mnémonique complet affilié au fichier
function EnregistrerFichiers($ListeFichiers, $RefD, $NirA, $link)
{
    $resultats = array();
    foreach ($ListeFichiers as $Key => $Fichier) {
        $j = 1;
        for ($i = 0; $i < count($Fichier['name']); $i++) {
            if ($Fichier['name'][$i] != "") {
                $file = basename($Fichier['name'][$i]);

                $target_dir = "../" . STORAGE_PATH . "/" . $NirA . "/" . $RefD;
                $ext = pathinfo($file)['extension'];

                $CheminJ = "$target_dir/$Key" . "_$j.$ext";
                $CodeA = ChercherAssureAvecNIR($NirA, $link)["CodeA"];
                $CodeD = ChercherDossierAvecREF($RefD, $link)["CodeD"];
                $Mnemonique = ChercherObjetMnemoAvecMnemo($Key, $link);
                $Designation = $Mnemonique["Designation"] . " No. " . $j;
                if (!FichierExiste($link, $CheminJ)){
                    if (EnregistrerFichier($CheminJ, $CodeD, $CodeA, $Mnemonique["CodeM"], $link)) {
                        if (move_uploaded_file($Fichier['tmp_name'][$i], $CheminJ)) {
                            $resultats[] = array(TRUE, $file, $Designation);
                            $j++;
                        } else {
                            $resultats[] = array(FALSE, $file, $Designation);
                        }
                    } else {
                        $resultats[] = array(FALSE, $file, $Designation);
                    }
                }
            }
        }
    }
    return $resultats;
}


/* ************************************************ */
/*                  BACK OFFICE                     */
/* ************************************************ */


/*          CONNEXION DU TECHNICIEN                 */

//Vérification de l'unicité du matricule 
function VerificationMat($connexion, $matricule)
{
    $requete = "SELECT * FROM technicien WHERE Matricule='$matricule'";
    $curseur = mysqli_query($connexion, $requete);

    if ($curseur != null) {
        if (mysqli_num_rows($curseur) == 0) {
            return "Unique";
        } else {
            $ligne = mysqli_fetch_array($curseur);
            echo "La matricule" . $ligne["Matricule"] . "est déjà attribuée";
        }
    }
    return "Erreur de vérification du Matricule";
}

/*          REQUETES POUR RECAPITULATIF             */

// Nombre de dossiers recus à la date courante
function nbDossiersRecus($link)
{
    $query = "SELECT count(*) AS nbDossiersRecus FROM dossier d WHERE DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}
// Nombre de dossiers restant à traiter au total
function nbDossiersATraiterTotal($link)
{
    $query = "SELECT count(*) AS nbDossiersAtraiterTotal FROM dossier d WHERE d.StatutD = 'À traiter'";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

// Nombre de dossiers restant à traiter à la date courante
function nbDossiersATraiter($link)
{
    $query = "SELECT count(*) AS nbDossiersAtraiter FROM dossier d WHERE d.StatutD = 'À traiter' And DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

// Nombre de dossiers classés sans suite à la date courante
function nbDossiersClasses($link)
{
    $query = "SELECT count(*) AS nbDossiersClasses FROM dossier d, traiter t WHERE d.CodeD = t.CodeD AND d.StatutD = 'Classé sans suite' And DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

// Nombre de dossiers terminés à la date courante
function nbDossiersTermines($link)
{
    $query = "SELECT count(*) AS nbDossiersTermines FROM dossier d, traiter t WHERE d.CodeD = t.CodeD AND d.StatutD = 'Terminé' And DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}
/*      FONCTIONS POUR TECHNICIEN    */

// Récupère les informations d'un technicien à partir du matricule
function DonneesTechnicien($link, $matricule) {
    $query = "Select CodeT, NomT, PrenomT From technicien t Where t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

// Vérifie les identifiants d'un technicien (VRAI si les données correspondent, FAUX sinon)
function AuthentifierTechnicien($link, $matricule, $mdpT) {
    $query = "SELECT Matricule, MdpT ";
    $query .= "FROM Technicien T ";
    $query .= "WHERE Matricule = '$matricule' ";
    $query .= "AND MdpT = '$mdpT'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}


/*      TRAITEMENT D'UN DOSSIER      */

// Changement du statut d'un dossier
function ChangerStatutDossier($link, $codeDossier, $statut)
{
    $query = "UPDATE dossier SET StatutD = '$statut' WHERE CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);
    return $result;
}

//Traite un dossier en indiquant dans la BD, le nom du technicien
//Et modifie le statut d'un dossier
function TraiterDossier($CodeT, $CodeD, $StatutD, $link)
{
    $keys = "";
    $values = "";
    if ($CodeT != NULL) {
        $keys .= "CodeT, ";
        $values .= $CodeT . ", ";
    }
    if ($CodeD != NULL) {
        $keys .= "CodeD, ";
        $values .= $CodeD . ", ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO traiter(".$keys.") VALUES (".$values.")";

    if(mysqli_query($link, $query)) {
        if(!ChangerStatutDossier($link, $CodeD, $StatutD)){
            echo "<div class='alert alert-danger'><strong>Alerte !".
            "</strong> Erreur dans le changement du statut du dossier !</div>";
            return False;
        }
        else return True;
    }
    else {
        echo "<div class='alert alert-danger'><strong>Alerte !".
            "</strong> Erreur dans l'insertion dans traiter !</div>";
            return False;
        return False;
    }
}

// Récupération des fichiers d'un dossier
function RecupererPJ($link, $codeDossier)
{
    $query = "SELECT CheminJ, Mnemonique FROM justificatif j, listemnemonique l "
        . "WHERE j.CodeM = l.CodeM AND j.CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);
    return $result;
}

//Active ou désactive un bouton permettant de modifier le statut d'un dossier
//Appelée dans la page 'traiter.php'
//$sessionValue = $_SESSION['statut'] (statut actuel)
//$buttonValue = ('En cours', 'Classé sans suite', 'Terminé')
//$codeT_dossier est le code du technicien qui est actuellement connecté
//Selon si le dossier est dans sa corbeille ou pas, il pourra ou ne pourra pas modifier
//Le statut du dossier courant
function ClassBoutonTraiter($sessionValue, $buttonValue, $codeT_dossier, $codeT_courant) {
    switch($sessionValue) {
        case "En cours":
            if($codeT_dossier == $codeT_courant) {
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn btn-primary";
                        break;
                    case "Terminé":
                        echo "btn btn-primary";
                        break;
                }
            }
            else { // désactiver les boutons si en cours de traitement par un autre technicien
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn disabled";
                        break;
                    case "Terminé":
                        echo "btn disabled";
                        break;
                }
            }
            break;
        case "Classé sans suite":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn disabled";
                    break;
                case "Classé sans suite":
                    echo "btn btn-danger disabled";
                    break;
                case "Terminé":
                    echo "btn disabled";
                    break;
            }
            break;
        case "Terminé":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn disabled";
                    break;
                case "Classé sans suite":
                    echo "btn disabled";
                    break;
                case "Terminé":
                    echo "btn btn-success disabled";
                    break;
            }
            break;
    }
}

/*          CORBEILLE GENERALE         */

// Liste de tous les dossiers (ceux à traiter sont affichés par défaut)
function DossiersCorbeilleGenerale($link)
{
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD  FROM dossier d, assure a WHERE d.CodeA = a.CodeA ORDER BY d.DateD";
    $result = mysqli_query($link, $query);
    return $result;
}


/*      CORBEILLE D'UN TECHNICIEN      */

// Liste des dossiers en cours de traitement par le technicien connecté
function DossiersCorbeilleTechnicien($link, $codeT) {
    //$query = 'SELECT d.DateD, d.RefD, a.NirA  FROM traiter t, dossier d, assure a WHERE t.CodeD=d.CodeD and d.CodeA=a.CodeA';

    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD, t.Matricule, tr.DateTraiterD ";
    $query .= "FROM dossier d, assure a, technicien t, traiter tr ";
    $query .= "WHERE d.CodeA = a.CodeA ";
    $query .= "AND d.CodeD = tr.CodeD ";
    $query .= "AND t.CodeT = tr.CodeT ";
    $query .= "AND t.CodeT = '$codeT' ";
    $query .= "AND d.StatutD = 'En cours' ";

    //$query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD  FROM dossier d, assure a WHERE d.CodeA = a.CodeA AND d.StatutD = 'En cours' $dossiers";

    $result = mysqli_query($link, $query);
    return $result;
}

// Envoie un mail de confirmation d'enregistrement
function EnvoyerMailConfirmationEnregistrement($mailA, $refD)
{
    $subject = "PJPE - Confirmation d'enregistrement";
    $txt = "Votre référence dossier est le $refD.";

    return mail($mailA, $subject, $txt);
}

// Envoie un mail de demande de PJs à l'assuré
function EnvoyerMailDemandePJs($mailA, $subject, $txt) {
    return mail($mailA, $subject, $txt);
}

// Enregistre le mail envoyé à un assuré
function EnregistrerMessageAssure($CodeA, $CodeT, $Contenu, $link) {
    $keys = ""; $values = "";
    if($CodeA != NULL) {$keys .= "CodeA, "; $values .= $CodeA.", ";}
    if($CodeT != NULL) {$keys .= "CodeT, "; $values .= $CodeT.", ";}
    if($Contenu != NULL) {$keys .= "Contenu, "; $values .= "'".$Contenu."', ";}

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO message(".$keys.") VALUES (".$values.")";

    return mysqli_query($link, $query);
}

// Liste des messages adressés à un assuré
function ListeMessages($CodeA, $link) {
    $query = "SELECT DateEnvoiM, Contenu, T.Matricule ";
    $query .= "FROM Message M, Assure A, Technicien T ";
    $query .= "WHERE A.CodeA = M.CodeA ";
    $query .= "AND A.CodeA = $CodeA ";
    $query .= "AND T.CodeT = M.CodeT ";
    $query .= "ORDER BY DateEnvoiM DESC";
 
    return mysqli_query($link, $query);
}

//Extrait l'adresse d'envoi, le sujet et le contenu d'un message envoyé à un assuré
function ExtraireMessage($Contenu) {
    //Position de l'adresse email
    $deb = strpos($Contenu, "À : ") + strlen("À : ");
    $fin = strpos($Contenu, "Objet : ");
    $mail = substr($Contenu, $deb, $fin - $deb);

    //Position de la référence du dossier
    $deb = strpos($Contenu, "?RefD=") + strlen("?RefD=");
    $fin = $deb + 8; // 8 = Nb char référence
    $refD = substr($Contenu, $deb, $fin - $deb);

    //Position de l'objet
    $deb = strpos($Contenu, "Objet : ") + strlen("Objet : ");
    $fin = strpos($Contenu, "Message : ");
    $objet = substr($Contenu, $deb, $fin - $deb);

    //Position du contenu du message
    $deb = strpos($Contenu, "Message : ") + strlen("Message : ");
    $fin = strlen($Contenu);
    $texte = explode("\n", substr($Contenu, $deb, $fin - $deb));
    $texte = implode("<br>",$texte);

    return [$mail, $objet, $texte, $refD];
}

// Renvoie la date de format aaaa-mm-jj hh:MM:ss en jj / mm / aaaa hh:MM:ss
function dateFR($date) {
    $annee = substr($date, 0, 4);
    $mois = substr($date, 5, 2);
    $jour = substr($date, 8, 2);
    $heure = substr($date, 11);

    return "$jour / $mois / $annee $heure";
}

?>
