<?php
/******************************************************************/

/*   FICHIER CONTENANT LES FONCTIONS PHP UTILISÉES POUR LE SITE   */

/******************************************************************/


/*------------------------------------------------------------------
 	VARIABLE GLOBALE DE CONNEXION À LA BASE DE DONNÉES
------------------------------------------------------------------*/

define("HOST", "localhost");    // Nom du host
define("USER", "root");         // Nom d'utilisateur
define("PWD_MYSQL", "");        // Mot de passe
define("BD_MYSQL", "bd_cpam");  // Nom de la base de données
define("PORT", "3306");         // Nom du port de connexion

/*------------------------------------------------------------------
 	VARIABLE GLOBALE DU CHEMIN VERS L'ESPACE DE STOCKAGE DES PIECES
------------------------------------------------------------------*/

define("STORAGE_PATH", "piecesJustificatives");     // NB : À partir de la racine

/*------------------------------------------------------------------
 	VARIABLE GLOBALE POUR GÉNÉRER LE MESSAGE DE DEMANDE DE PIECES
------------------------------------------------------------------*/

define("MAIL_REQUEST_SUBJECT", "PJPE - Demande de pièces justificatives");         // Objet du message
define("DEPOSITE_LINK", "http://".$_SERVER['HTTP_HOST']."/frontOffice/depot.php"); // Lien vers le formulaire de dépôt
define("FOOTER_EMAIL", "Merci de ne pas répondre à ce message.");                  // Message du footer


/*------------------------------------------------------------------
 	FONCTIONS GÉNÉRALES
------------------------------------------------------------------*/

/* Connecte à la base de données */
function connexionMySQL() {
    // Connexion à la base données avec une lecture encodée en UTF-8
    // (NB : Renseigner les variables de connexion plus haut)
    $link = mysqli_connect(HOST, USER, PWD_MYSQL, BD_MYSQL, PORT);
    mysqli_query($link, 'SET NAMES utf8');

    // Vérification de la connexion
    if ($link == NULL) { // Si la connexion a échoué
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

/* Redirige vers la page '$nomPage' */
function RedirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    
    exit;
}


/*------------------------------------------------------------------
 	FONCTIONS : FRONT OFFICE
------------------------------------------------------------------*/

/* Vérifie si la référence d'un dossier '$refD' est bien affilié à un NIR '$nirA' */
/* => [Vrai s'il y a bien une correspondance entre le NIR '$nirA' et la référence '$redD', Faux sinon] */
function NirRefExiste($NirA, $RefD, $link) {
    $query = "SELECT a.* "
            ."FROM Assure a, Dossier d  "
            ."WHERE a.NirA = '$NirA' "
            ."AND d.CodeA = a.CodeA "
            ."AND d.RefD = '$RefD'" ;
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

/* Renvoie un caractère aléatoire compris dans '$listeChar' */
/* => [Caractère de la liste '$listeChar'] */
function CaractereAleatoire() {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $listeChar[rand(0, strlen($listeChar) - 1)];
}

/* Genère une référence unique de taille '$nbChar' d'un dossier */
/* => [Chaine de caractères de taille '$nbChar'] */
function GenererReferenceDossier($nbChar, $link) {
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= CaractereAleatoire(); //Sélection d'un caractère aléatoire
        }
    } while(DossierExiste($ref, $link));

    return $ref;
}

/* Renvoie les informations de l'assuré ayant le NIR '$nirA' sous la forme d'une liste */
/* => [Objet de type array si l'assuré est déjà enregistré, NULL sinon] */
function ChercherAssureAvecNIR($NirA, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '$NirA'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier ayant pour référence '$refD' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function ChercherDossierAvecREF($RefD, $link)
{
    $query = "SELECT * FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND RefD = '$RefD'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier en cours de traitement ou traité ayant pour code '$codeD' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function ChercherDossierTraiteAvecCodeD($CodeD, $link) {
    $query = "SELECT * FROM Assure A, Dossier D, Traiter Tr, Technicien T "
            ."WHERE A.CodeA = D.CodeA AND D.CodeD = $CodeD "
            ."AND D.CodeD = Tr.CodeD AND T.CodeT = Tr.CodeT "
            ."AND Tr.DateTraiterD = (SELECT MAX(DateTraiterD) "
                                    ."FROM Traiter "
                                    ."WHERE CodeD = $CodeD)";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la référence du dossier ayant pour code '$codeD' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function ChercherREFAvecCodeD($CodeD, $link)
{
    $query = "SELECT RefD FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND CodeD = $CodeD";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations correspondant au mnémonique '$mnemonique' */
/* => [Objet de type array si le mnémonique existe, NULL sinon] */
function ChercherObjetMnemoAvecMnemo($Mnemonique, $link)
{
    $query = "SELECT * FROM Listemnemonique "
            ."WHERE Mnemonique = '$Mnemonique'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/*------------------------------------------------------------------
 	Vérification d'existence dans la BD
------------------------------------------------------------------*/

/* Vérifie si '$nirA' correspond au NIR d'un assuré déjà enregistré dans la base de données */
/* => [Vrai si le NIR est reconnu, Faux sinon] */
function AssureExiste($NirA, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '$NirA'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Vérifie si le dossier de référence $refD existe déjà dans la base de données */
/* => [Vrai si la référence de dossier est reconnue, Faux sinon] */
function DossierExiste($RefD, $link)
{
    $query = "SELECT RefD FROM Dossier WHERE RefD = '$RefD'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Vérifie si le chemin $chemin exite déjà dans la base de données */
/* => [Vrai si le chemin est déjà enregistré, Faux sinon] */
function FichierExiste($link, $path){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '$path'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Enregistre les données d'un assuré dans la base de données */
/* => [Vrai si les données de l'assuré ont bien été enregistrées, Faux sinon] */
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

    $query = "INSERT INTO assure($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Enregistre les informations concernant un nouveau dossier */
/* => [Vrai si les informations du dossier ont bien été enregistrées, Faux sinon] */
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

    $query = "INSERT INTO dossier($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Créé un dossier ayant pour nom '$nirA' à l'emplacement 'STORAGE_PATH' (à renseigner tout en haut) */
/* => [Vrai si le dossier de l'assuré a bien été créé, Faux sinon] */
function CreerDossierNIR($NirA)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $NirA;
    return mkdir($dirname);
}

/* Créé le dossier ayant pour nom '$refD' à l'emplacement 'STORAGE_PATH/$nirA' (à renseigner tout en haut) */
/* => [Vrai si le dossier de l'assuré a bien été créé, Faux sinon] */
function CreerDossierAM($RefD, $NirA)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $NirA . "/" . $RefD;
    return mkdir($dirname);
}

/* Enregistre les informations concernant un nouveau fichier */
/* => [Vrai si les informations du fichier ont bien été enregistrées, Faux sinon] */
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

    $query = "INSERT INTO justificatif($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Enregistre les fichiers de '$ListeFichiers' à l'emplacement 'STORAGE_PATH/$nirA/$refD' */
/* => [Liste(A : Booléen, B : Chaîne de caractères, C : Chaîne de caractères)]  */
/*      => A = Vrai si l'enregistrement a réussi, Faux sinon                    */
/*      => B = Nom du fichier téléchargé                                        */
/*      => C = Mnémonique complet affilié au fichier                            */
function EnregistrerFichiers($ListeFichiers, $RefD, $NirA, $link)
{
    $resultats = array();
    foreach ($ListeFichiers as $Key => $Fichier) {
        $j = 1;
        for ($i = 0; $i < count($Fichier['name']); $i++) {
            if ($Fichier['name'][$i] != "") {
                $file = basename($Fichier['name'][$i]);

                $target_dir = "../" . STORAGE_PATH . "/" . $NirA . "/" . $RefD;
                $ext = strtolower(pathinfo($file)['extension']);

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


/*------------------------------------------------------------------
 	FONCTIONS : BACK OFFICE
------------------------------------------------------------------*/

/* Vérifie l'unicité du matricule $matricule */
/* => ["Unique" si c'est vrai, ... ???] */
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


/* Renvoie le nombre de dossiers reçus au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersRecus($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersRecus "
            ."FROM dossier d "
            ."WHERE DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers restant à traiter */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiterTotal($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersAtraiterTotal "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers restant à traiter au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiter($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersAtraiter "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter' "
            ."AND DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers classés sans suite au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersClasses($link)
{
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersClasses "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Classé sans suite' "
            ."And DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers terminés d'être traiter au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersTermines($link)
{
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersTermines "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Terminé' "
            ."AND DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/*      FONCTIONS POUR TECHNICIEN    */

/* Renvoie les informations du technicien ayant le matricule '$matricule' sous la forme d'une liste */
/* => [Objet de type array si le technicien est déjà enregistré, NULL sinon] */
function DonneesTechnicien($link, $matricule) {
    $query = "SELECT CodeT, NomT, PrenomT "
            ."FROM technicien t "
            ."WHERE t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Vérifie le technicien de matricule '$matricule' possède bien le mot de passe '$mdpt' */
/* => [Vrai si le technicien est bien authentifié, Faux sinon] */
function AuthentifierTechnicien($link, $matricule, $mdpT) {
    $query = "SELECT Matricule, MdpT "
            ."FROM Technicien T "
            ."WHERE Matricule = '$matricule' "
            ."AND MdpT = '$mdpT'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}


/*      TRAITEMENT D'UN DOSSIER      */

/* Change le statut du dossier de code '$codeDossier' en '$statut' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function ChangerStatutDossier($link, $codeDossier, $statut)
{
    $query = "UPDATE dossier SET StatutD = '$statut' WHERE CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Affilie le dossier de code '$codeD' au technicien de code '$codeT' et son statut en '$statutD' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
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

    $query = "INSERT INTO traiter($keys) VALUES ($values)";

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

/* Remet le dossier de code '$codeD' de la liste des dossiers traités */
/* => [Vrai si le retrait a bien été effectué, Faux sinon] */
function LibererDossier($link, $CodeD)
{
    $query = "DELETE FROM Traiter WHERE CodeD = $CodeD";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie la liste des fichiers du dossier de code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function RecupererPJ($link, $codeDossier)
{
    $query = "SELECT CheminJ, Mnemonique "
            ."FROM justificatif j, listemnemonique l "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie la classe CSS correspondante pour chaque bouton du fichier traiter.php           */
/* => Effectue seulement un affichage (pas de valeur de retour)                             */
/* => Active ou désactive un bouton permettant de modifier le statut d'un dossier           */
/* => $sessionValue correspond au statut du dossier en cours ($_SESSION['statut'])          */
/* => $buttonValue est soit 'En cours', soit 'Classé sans suite' ou bien 'Terminé'          */
/* => $codeT_dossier est le code du technicien qui est actuellement connecté                */
/* => Selon si le dossier est dans sa corbeille ou pas, il pourra ou ne pourra pas modifier */
/* => Le statut du dossier courant                                                          */
function ClassBoutonTraiter($sessionValue, $buttonValue, $codeT_dossier, $codeT_courant) {
    switch($sessionValue) {
        case "En cours":
            if($codeT_dossier == $codeT_courant) {
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn btn-default";
                        break;
                    case "Terminé":
                        echo "btn btn-default";
                        break;
                }
            }
            else { // désactiver les boutons si en cours de traitement par un autre technicien
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn btn-default disabled";
                        break;
                    case "Terminé":
                        echo "btn btn-default disabled";
                        break;
                }
            }
            break;
        case "Classé sans suite":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn btn-default disabled";
                    break;
                case "Classé sans suite":
                    echo "btn btn-danger disabled";
                    break;
                case "Terminé":
                    echo "btn btn-default disabled";
                    break;
            }
            break;
        case "Terminé":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn btn-default disabled";
                    break;
                case "Classé sans suite":
                    echo "btn btn-default disabled";
                    break;
                case "Terminé":
                    echo "btn btn-success disabled";
                    break;
            }
            break;
    }
}

/*          CORBEILLE GENERALE         */

/* Renvoie la liste complètes des dossiers à traiter et en cours de traitement */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function DossiersCorbeilleGenerale($link)
{
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD "
            ."FROM dossier d, assure a "
            ."WHERE d.CodeA = a.CodeA "
            ."ORDER BY d.DateD";
    $result = mysqli_query($link, $query);
    
    return $result;
}


/*      CORBEILLE D'UN TECHNICIEN      */

/* Renvoie la liste des dossiers en cours de traitement par le technicien de code '$codeT' */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function DossiersCorbeilleTechnicien($link, $codeT) {
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD, t.Matricule, tr.DateTraiterD "
            ."FROM dossier d, assure a, technicien t, traiter tr "
            ."WHERE d.CodeA = a.CodeA "
            ."AND d.CodeD = tr.CodeD "
            ."AND t.CodeT = tr.CodeT "
            ."AND t.CodeT = '$codeT' "
            ."AND d.StatutD = 'En cours' ";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Envoie un mail de confirmation d'enregistrement de données à l'adresse $mailA */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function EnvoyerMailConfirmationEnregistrement($mailA, $refD)
{
    $subject = "PJPE - Confirmation d'enregistrement";
    $txt = "Votre référence dossier est le $refD.";

    return mail($mailA, $subject, $txt);
}

/* Envoie un mail de sujet '$subject'* et de contenu '$txt' à l'adresse $mailA */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function EnvoyerMailDemandePJs($mailA, $subject, $txt) {
    return mail($mailA, $subject, $txt);
}

/* Enregistre le contenu d'un mail envoyé à l'assuré de code '$codeA' par le technicien de code '$codeT'*/
/* => [Vrai si l'neregitrement a bien été effectué, Faux sinon] */
function EnregistrerMessageAssure($CodeA, $CodeT, $Contenu, $link) {
    $keys = ""; $values = "";
    if($CodeA != NULL) {$keys .= "CodeA, "; $values .= $CodeA.", ";}
    if($CodeT != NULL) {$keys .= "CodeT, "; $values .= $CodeT.", ";}
    if($Contenu != NULL) {$keys .= "Contenu, "; $values .= "'".$Contenu."', ";}

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO message($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Renvoie les informations concernant tous les messages envoyés à l'assuré de code '$codeA' */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
// Liste des messages adressés à un assuré
function ListeMessages($CodeA, $link) {
    $query = "SELECT DateEnvoiM, Contenu, T.Matricule "
            ."FROM Message M, Assure A, Technicien T "
            ."WHERE A.CodeA = M.CodeA "
            ."AND A.CodeA = $CodeA "
            ."AND T.CodeT = M.CodeT "
            ."ORDER BY DateEnvoiM DESC";
 
    return mysqli_query($link, $query);
}

/* Extrait les informations d'un message pour les renvoyer sous forme d'une liste */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
/* => [ainsi que la référence du dossier]                                                           */
/* => Ne fonctionne que sur les message générés automatiquement                                     */
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


/* Génère un message de titre '$title', de contenu '$body', de glyphicon '$icon' et ayant un type Boostrap */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function GenererMessage($title, $body, $icon, $type) {
    echo "
        <div class='alert alert-$type'>
            <h3>
                <strong class='alert-title'>
                    <span class='glyphicon glyphicon-$icon'></span>$title
                </strong>
            </h3>
            <p>
                $body
            </p>
        </div>
    ";
}
?>
