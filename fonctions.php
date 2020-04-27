<?php
/******************************************************************/

/*   FICHIER CONTENANT LES FONCTIONS PHP UTILISÉES POUR LE SITE   */

/******************************************************************/


/*------------------------------------------------------------------
 	VARIABLES GLOBALES DE CONNEXION À LA BASE DE DONNÉES
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
 	VARIABLES GLOBALES POUR GÉNÉRER LE MESSAGE DE DEMANDE DE PIECES
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

/* Vérifie si la référence d'un dossier '$ref' est bien affiliée à un NIR '$nir' */
function NirRefExiste($nir, $ref, $link) {
    $query = "SELECT a.* "
            ."FROM Assure a, Dossier d  "
            ."WHERE a.NirA = '$nir' "
            ."AND d.CodeA = a.CodeA "
            ."AND d.RefD = '$ref'" ;
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

/* Renvoie un caractère aléatoire compris dans '$listeChar' */
function CaractereAleatoire() {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $listeChar[rand(0, strlen($listeChar) - 1)];
}

/* Genère une référence unique d'un dossier de taille '$nbChar' */
function GenererReferenceDossier($nbChar, $link) {
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= CaractereAleatoire(); //Sélection d'un caractère aléatoire
        }
    } while(DossierExiste($ref, $link));

    return $ref;
}

/* Renvoie les informations de l'assuré ayant le NIR '$nir' sous la forme d'une liste */
function ChercherAssureAvecNIR($nir, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier ayant pour référence '$ref' sous la forme d'une liste */
function ChercherDossierAvecREF($ref, $link)
{
    $query = "SELECT * FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier en cours de traitement ou traité ayant pour code '$codeDossier' */
function ChercherDossierTraiteAvecCodeD($codeDossier, $link) {
    $query = "SELECT * FROM Assure A, Dossier D, Traiter Tr, Technicien T "
            ."WHERE A.CodeA = D.CodeA AND D.CodeD = $codeDossier "
            ."AND D.CodeD = Tr.CodeD AND T.CodeT = Tr.CodeT "
            ."AND Tr.DateTraiterD = (SELECT MAX(DateTraiterD) "
                                    ."FROM Traiter "
                                    ."WHERE CodeD = $codeDossier)";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la référence du dossier ayant pour code '$codeDossier' sous la forme d'une liste */
function ChercherREFAvecCodeD($codeDossier, $link)
{
    $query = "SELECT RefD FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations correspondant au mnémonique '$mnemonique' */
function ChercherObjetMnemoAvecMnemo($mnemonique, $link)
{
    $query = "SELECT * FROM Listemnemonique "
            ."WHERE Mnemonique = '$mnemonique'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/*------------------------------------------------------------------
 	Vérification d'existence dans la BD
------------------------------------------------------------------*/

/* Vérifie si '$nir' correspond au NIR d'un assuré déjà enregistré dans la base de données */
/* => [Vrai si le NIR est reconnu, Faux sinon] */
function AssureExiste($nir, $link)
{
    $query = "SELECT * FROM Assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Vérifie si le dossier de référence $ref existe déjà dans la base de données */
/* => [Vrai si la référence de dossier est reconnue, Faux sinon] */
function DossierExiste($ref, $link)
{
    $query = "SELECT RefD FROM Dossier WHERE RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Vérifie si le chemin $chemin exite déjà dans la base de données */
/* => [Vrai si le chemin est déjà enregistré, Faux sinon] */
function FichierExiste($link, $chemin){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '$chemin'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Enregistre les données d'un assuré dans la base de données */
/* => [Vrai si les données de l'assuré ont bien été enregistrées, Faux sinon] */
function EnregistrerAssure($nir, $nom, $prenom, $tel, $mail, $link)
{
    $keys = ""; $values = "";
    if ($nir != NULL) {
        $keys .= "NirA, "; $values .= "'$nir', ";
    }
    if ($nom != NULL) {
        $keys .= "NomA, "; $values .= "'$nom', ";
    }
    if ($prenom != NULL) {
        $keys .= "PrenomA, "; $values .= "'$prenom', ";
    }
    if ($tel != NULL) {
        $tel = explode(" ", $tel); 
        $tel = implode($tel);
        $keys .= "TelA, "; $values .= "'$tel', ";
    }
    if ($mail != NULL) {
        $keys .= "MailA, "; $values .= "'$mail', ";
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
function EnregistrerDossier($codeAssure, $dateAM, $ref, $link)
{
    $keys = ""; $values = "";
    $keys .= "RefD, "; $values .= "'" . $ref . "', ";
    $keys .= "DateAM, "; $values .= "'" . $dateAM . "', ";
    $keys .= "CodeA, "; $values .= $codeAssure . ", ";

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO dossier($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Créé le dossier de l'assuré ayant pour NIR $nir  */
/* => [Vrai si les données de l'assuré ont bien été enregistrées, Faux sinon] */
//Créer le dossier d'un assuré dont le nom est son numéro NIR en local
function CreerDossierNIR($nir)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $nir;
    return mkdir($dirname);
}

//Créer le dossier de l'arrêt maladie d'un assuré en local
function CreerDossierAM($ref, $nir)
{
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $nir . "/" . $ref;
    return mkdir($dirname);
}

//Fonction qui enregistre les données d'un fichier dans la base de données
function EnregistrerFichier($cheminJustificatif, $codeDossier, $codeAssure, $codeMnemonique, $link)
{
    $keys = ""; $values = "";
    if ($cheminJustificatif != NULL) {
        $keys .= "CheminJ, "; $values .= "'" . $cheminJustificatif . "', ";
    }
    if ($codeDossier != NULL) {
        $keys .= "CodeD, "; $values .= $codeDossier . ", ";
    }
    if ($codeAssure != NULL) {
        $keys .= "CodeA, "; $values .= $codeAssure . ", ";
    }
    if ($codeMnemonique != NULL) {
        $keys .= "CodeM, "; $values .= "'" . $codeMnemonique . "', ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO justificatif($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

//Enregistre les fichiers contenus dans le dossier d'un assuré
//Renvoie une liste avec une ligne pour un fichier
//1er paramètre de type Booléen qui est TRUE si l'enregistrement a réussi, FALSE sinon
//2ème paramètre de type String qui correspond au nom du fichier téléchargé
//3ème paramètre correspond au mnémonique complet affilié au fichier
function EnregistrerFichiers($listeFichiers, $ref, $nir, $link)
{
    $resultats = array();
    foreach ($listeFichiers as $key => $fichier) {
        $j = 1;
        for ($i = 0; $i < count($fichier['name']); $i++) {
            if ($fichier['name'][$i] != "") {
                $file = basename($fichier['name'][$i]);

                $target_dir = "../" . STORAGE_PATH . "/" . $nir . "/" . $ref;
                $ext = strtolower(pathinfo($file)['extension']);

                $cheminJustificatif = "$target_dir/$key" . "_$j.$ext";
                $codeAssure = ChercherAssureAvecNIR($nir, $link)["CodeA"];
                $codeDossier = ChercherDossierAvecREF($ref, $link)["CodeD"];
                $mnemonique = ChercherObjetMnemoAvecMnemo($key, $link);
                $designation = $mnemonique["Designation"] . " No. " . $j;
                if (!FichierExiste($link, $cheminJustificatif)){
                    if (EnregistrerFichier($cheminJustificatif, $codeDossier, $codeAssure, $mnemonique["CodeM"], $link)) {
                        if (move_uploaded_file($fichier['tmp_name'][$i], $cheminJustificatif)) {
                            $resultats[] = array(TRUE, $file, $designation);
                            $j++;
                        } else {
                            $resultats[] = array(FALSE, $file, $designation);
                        }
                    } else {
                        $resultats[] = array(FALSE, $file, $designation);
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
    $query = "SELECT * FROM technicien WHERE Matricule='$matricule'";
    $curseur = mysqli_query($connexion, $query);

    if ($curseur != null) {
        if (mysqli_num_rows($curseur) == 0) {
            return "Unique";
        } else {
            $ligne = mysqli_fetch_array($curseur);
            echo "Le matricule" . $ligne["Matricule"] . "est déjà attribué";
        }
    }
    return "Erreur de vérification du Matricule";
}

/*          REQUETES POUR RECAPITULATIF             */

// Nombre de dossiers recus à la date courante
function nbDossiersRecus($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersRecus "
            ."FROM dossier d "
            ."WHERE DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

// Nombre de dossiers restant à traiter au total
function nbDossiersATraiterTotal($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersAtraiterTotal "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

// Nombre de dossiers restant à traiter à la date courante
function nbDossiersATraiter($link)
{
    $query = "SELECT COUNT(*) AS nbDossiersAtraiter "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter' "
            ."AND DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

// Nombre de dossiers classés sans suite à la date courante
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

// Nombre de dossiers terminés à la date courante
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

// Récupère les informations d'un technicien à partir du matricule
function DonneesTechnicien($link, $matricule) {
    $query = "SELECT CodeT, NomT, PrenomT "
            ."FROM technicien t "
            ."WHERE t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

// Vérifie les identifiants d'un technicien (VRAI si les données correspondent, FAUX sinon)
function AuthentifierTechnicien($link, $matricule, $mdpT) {
    $query = "SELECT Matricule, MdpT "
            ."FROM Technicien T "
            ."WHERE Matricule = '$matricule' "
            ."AND MdpT = '$mdpT'";
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
function TraiterDossier($codeTechnicien, $codeDossier, $statut, $link)
{
    $keys = ""; $values = "";
    if ($codeTechnicien != NULL) {
        $keys .= "CodeT, "; $values .= $codeTechnicien . ", ";
    }
    if ($codeDossier != NULL) {
        $keys .= "CodeD, "; $values .= $codeDossier . ", ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO traiter($keys) VALUES ($values)";

    if(mysqli_query($link, $query)) {
        if(!ChangerStatutDossier($link, $codeDossier, $statut)){
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

// Retire un dossier de la table "Traiter"
function LibererDossier($link, $codeDossier)
{
    $query = "DELETE FROM Traiter WHERE CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return $result;
}

// Récupération des fichiers d'un dossier
function RecupererPJ($link, $codeDossier)
{
    $query = "SELECT CheminJ, Mnemonique "
            ."FROM justificatif j, listemnemonique l "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeD = '$codeDossier'";
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

// Liste de tous les dossiers (ceux à traiter sont affichés par défaut)
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

// Liste des dossiers en cours de traitement par le technicien connecté
function DossiersCorbeilleTechnicien($link, $codeTechnicien) {
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD, t.Matricule, tr.DateTraiterD "
            ."FROM dossier d, assure a, technicien t, traiter tr "
            ."WHERE d.CodeA = a.CodeA "
            ."AND d.CodeD = tr.CodeD "
            ."AND t.CodeT = tr.CodeT "
            ."AND t.CodeT = '$codeTechnicien' "
            ."AND d.StatutD = 'En cours' ";
    $result = mysqli_query($link, $query);

    return $result;
}

// Envoie un mail de confirmation d'enregistrement
function EnvoyerMailConfirmationEnregistrement($mail, $ref)
{
    $subject = "PJPE - Confirmation d'enregistrement";
    $txt = "Votre référence dossier est le $ref.";

    return mail($mail, $subject, $txt);
}

// Envoie un mail de demande de PJs à l'assuré
function EnvoyerMailDemandePJs($mail, $subject, $txt) {
    return mail($mail, $subject, $txt);
}

// Enregistre le mail envoyé à un assuré
function EnregistrerMessageAssure($codeAssure, $codeTechnicien, $contenu, $link) {
    $keys = ""; $values = "";
    if($codeAssure != NULL) {$keys .= "CodeA, "; $values .= $codeAssure.", ";}
    if($codeTechnicien != NULL) {$keys .= "CodeT, "; $values .= $codeTechnicien.", ";}
    if($contenu != NULL) {$keys .= "Contenu, "; $values .= "'$contenu', ";}

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO message($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

// Liste des messages adressés à un assuré
function ListeMessages($codeAssure, $link) {
    $query = "SELECT DateEnvoiM, Contenu, T.Matricule "
            ."FROM Message M, Assure A, Technicien T "
            ."WHERE A.CodeA = M.CodeA "
            ."AND A.CodeA = $codeAssure "
            ."AND T.CodeT = M.CodeT "
            ."ORDER BY DateEnvoiM DESC";
 
    return mysqli_query($link, $query);
}

// Extrait l'adresse d'envoi, le sujet et le contenu d'un message envoyé à un assuré
function ExtraireMessage($contenu) {
    //Position de l'adresse email
    $deb = strpos($contenu, "À : ") + strlen("À : ");
    $fin = strpos($contenu, "Objet : ");
    $mail = substr($contenu, $deb, $fin - $deb);

    //Position de la référence du dossier
    $deb = strpos($contenu, "?RefD=") + strlen("?RefD=");
    $fin = $deb + 8; // 8 = Nb char référence
    $ref = substr($contenu, $deb, $fin - $deb);

    //Position de l'objet
    $deb = strpos($contenu, "Objet : ") + strlen("Objet : ");
    $fin = strpos($contenu, "Message : ");
    $objet = substr($contenu, $deb, $fin - $deb);

    //Position du contenu du message
    $deb = strpos($contenu, "Message : ") + strlen("Message : ");
    $fin = strlen($contenu);
    $texte = explode("\n", substr($contenu, $deb, $fin - $deb));
    $texte = implode("<br>",$texte);

    return [$mail, $objet, $texte, $ref];
}

// Créer un message
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
