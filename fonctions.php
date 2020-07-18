<?php
/******************************************************************/

/*   FICHIER CONTENANT LES FONCTIONS PHP UTILISÉES POUR LE SITE   */

/******************************************************************/
// Rangées en 4 groupes de fonctions : générales, pour front office, pour back office et pour admin
// Puis par ordre alphabétique

/* Chargement du Composer */
require 'vendor/autoload.php';

/*------------------------------------------------------------------
 	IMPORTATION DES CLASSES PHPMailer
------------------------------------------------------------------*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/*------------------------------------------------------------------
 	IMPORTATION DES VARIABLES D'ENVIRONNEMENT DU FICHIER '.env'
------------------------------------------------------------------*/

/* Les variables de ce fichiers ne sont disponibles que depuis ce fichier PHP */
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/*------------------------------------------------------------------
 	VARIABLES GLOBALES POUR L'ENVOI DES MAILS
------------------------------------------------------------------*/

/* Avant de pouvoir envoyer des mails, il est nécéssaire d'effectuer les actions
précisées dans le fichier README.md > Section "Initialisation de SENDGRID" */
define("MAIL_REQUEST_SUBJECT", "PJPE - Demande de pièces justificatives");         // Objet du message de demandes de pièces
define("MAIL_CONFIRM_SUBJECT", "PJPE - Confirmation d'enregistrement");            // Objet du message de confirmation de réception
define("MAIL_CONFIRM_TREATMENT", "PJPE - Confirmation de traitement");             // Objet du message de confirmation de traitement
define("DEPOSITE_LINK", "http://".$_SERVER['HTTP_HOST']."/frontOffice/depot.php"); // Lien vers le formulaire de dépôt
define("FOOTER_EMAIL", 
    "Ceci est un message automatique. Merci de ne pas y répondre.");               // Message du footer

/*------------------------------------------------------------------
 	FONCTIONS GÉNÉRALES
------------------------------------------------------------------*/

/* Renvoie le chemin permettant d'accéder au serveur FTP */
function cheminVersServeurFTP() {
    $chemin = "ftp://".getenv("FTP_USER").":";
    $chemin .= getenv("FTP_PWD")."@";
    $chemin .= getenv("FTP_HOST").":";
    $chemin .= getenv("FTP_PORT")."/";

    return $chemin;
}

/* Connecte au serveur FTP pour la sauvegarde des pièces */
function connecterServeurFTP() {    
    // Mise en place d'une connexion basique
    $ftp_stream = ftp_connect(
        getenv("FTP_HOST")) or 
        die("Erreur : Impossible de se connecter à ".getenv("FTP_HOST")." !<br>"); 

    //Tentative d'identification
    if(ftp_login($ftp_stream, getenv("FTP_USER"), getenv("FTP_PWD"))) {
        //echo "Connecté en tant que ".getent("FTP_USER")."@".FTP_HOST." ...<br>";
    
        // Activation du mode passif
        ftp_pasv($ftp_stream, true);

        return $ftp_stream;
    }
    else {            
        echo "Erreur lors de l'identification !<br>";
        echo "Connexion impossible en tant que ".getenv("FTP_USER")." ...<br>";
        return NULL;
    }
}

/* Connecte au serveur FTP pour la sauvegarde des fichiers CSV */
function connecterServeurFTP_CSV() {    
    // Mise en place d'une connexion basique
    $ftp_stream = ftp_connect(
        getenv("CSV_FTP_HOST")) or 
        die("Erreur : Impossible de se connecter à ".getenv("CSV_FTP_HOST")." !<br>"); 

    //Tentative d'identification
    if(ftp_login($ftp_stream, getenv("CSV_FTP_USER"), getenv("CSV_FTP_PWD"))) {
        //echo "Connecté en tant que ".getent("FTP_USER")."@".FTP_HOST." ...<br>";
    
        // Activation du mode passif
        ftp_pasv($ftp_stream, true);

        return $ftp_stream;
    }
    else {            
        echo "Erreur lors de l'identification !<br>";
        echo "Connexion impossible en tant que ".getenv("CSV_FTP_USER")." ...<br>";
        return NULL;
    }
}

/* Connecte à la base de données */
function connecterBD() {
    // Connexion à la base données avec une lecture encodée en UTF-8
    // (NB : Renseigner les variables de connexion plus haut)
    $link = mysqli_connect(
        getenv('MYSQL_HOST'),
        getenv('MYSQL_USER'),
        getenv('MYSQL_PWD'),
        getenv('MYSQL_BD'),
        getenv('MYSQL_PORT')
    );

    mysqli_query($link, 'SET NAMES utf8');

    // Vérification de la connexion
    if ($link == NULL) { // Si la connexion a échoué
        echo "Erreur : Impossible de se connecter à MySQL." . "<br>";
        echo "Errno de débogage : " . mysqli_connect_errno() . "<br>";
        echo "Erreur de débogage : " . mysqli_connect_error() . "<br>";
        exit;
    } else {
        if (mysqli_select_db($link, getenv('MYSQL_BD')) == null) {
            echo ("<p> Vérifier que la base de données est bien sur MariaDB </p>");
            return null;
        }
    }

    return $link;
}

/* Fonction qui supprime les accents d'une chaine de caractères */
 function retirerAccents($sentence) {
    $res = $sentence;
    $res = preg_replace('#Ç#', 'C', $res);
    $res = preg_replace('#ç#', 'c', $res);
    $res = preg_replace('#è|é|ê|ë#', 'e', $res);
    $res = preg_replace('#È|É|Ê|Ë#', 'E', $res);
    $res = preg_replace('#à|á|â|ã|ä|å#', 'a', $res);
    $res = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $res);
    $res = preg_replace('#ì|í|î|ï#', 'i', $res);
    $res = preg_replace('#Ì|Í|Î|Ï#', 'I', $res);
    $res = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $res);
    $res = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $res);
    $res = preg_replace('#ù|ú|û|ü#', 'u', $res);
    $res = preg_replace('#Ù|Ú|Û|Ü#', 'U', $res);
    $res = preg_replace('#ý|ÿ#', 'y', $res);
    $res = preg_replace('#Ý#', 'Y', $res);
     
    return ($res);
}

/* Génère le fichier CSV pour l'insertion dans DIADEME */
function genererFichierInjectionCSV($link) {    
    $fichier = tmpfile();

    // Ajout de l'entête
    $header = getenv("CSV_INJECTION_HEADER");
    fputs($fichier, utf8_decode($header."\n"));
    
    $header = retirerAccents(strtolower($header));
    $result = recupererDossierInjection($link);
    do {
        $tuple = mysqli_fetch_array($result);
        if($tuple != NULL) {
            $ligne = "";
            if(strpos($header, "addict") !== Null)
                //$ligne .= getenv("STORAGE_PATH")."/".$tuple["NirA"]."/".$tuple["RefD"].";";
                $ligne .= ";";
            if(strpos($header, "processus") !== Null)
                $ligne .= "IJ;";
            if(strpos($header, "archivage") !== Null)
                $ligne .= "OUI;";
            if(strpos($header, "date reception") !== Null)
                $ligne .= substr($tuple["DateD"], 0, 10).";";
            if(strpos($header, "index metier") !== Null)
                $ligne .= "vide;";
            if(strpos($header, "date evenement") !== Null)
                $ligne .= ";";
            if(strpos($header, "commentaire") !== Null)
                $ligne .= "Référence dossier PJPE : ".$tuple["RefD"].";";
            if(strpos($header, "docporteur") !== Null)
                $ligne .= $tuple["Mnemonique"].";";
            $ligne = substr($ligne, 0, strlen($ligne) - 1); // Suppression du dernier point virgule
            fputs($fichier, utf8_decode($ligne."\n")); // On écrit le tuple dans le fichier CSV
        }
    } while($tuple != NULL);

    return $fichier;
}

/* Génère le fichier CSV pour le listing des dossiers "À traité" et "En cours" */
function genererListeDossiersCSV($link) {    
    $fichier = tmpfile();

    $result = recupererListeDossiers($link);

    $tuple = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if($tuple != NULL) {
        // Ajout de l'entête
        $header = array_keys($tuple);
        $ligne = implode(";", $header);
        fputs($fichier, utf8_decode($ligne."\n")); // On écrit le tuple dans le fichier CSV 

        do {
            $ligne = implode(";", $tuple);
            fputs($fichier, utf8_decode($ligne."\n")); // On écrit le tuple dans le fichier CSV            
            $tuple = mysqli_fetch_array($result, MYSQLI_NUM);
        } while($tuple != NULL);
    }

    return $fichier;
}

/* Sauvegarde le fichier d'injection dans DIADEME sur l'espace du serveur */
function sauvegarderFichierInjectionCSVServeur($ftp_stream, $link) {
    $fichier = genererFichierInjectionCSV($link);
    $nomFichier = stream_get_meta_data($fichier)['uri'];

    // Si le dossier n'existe pas
    if(!is_dir(cheminVersServeurFTP()."/".getenv("CSV_INJECTION_FILE_PATH"))) {
        ftp_mkdir($ftp_stream, getenv("CSV_INJECTION_FILE_PATH")); // Création du dossier
    }
    $path = getenv("CSV_INJECTION_FILE_PATH")."/".getenv("CSV_INJECTION_NAME_FILE");
    $result = ftp_put($ftp_stream, $path, $nomFichier, FTP_BINARY);

    fclose($fichier);

    return $result;
}

/* Télécharge le fichier d'injection dans DIADEME en local */
function telechargererFichierInjectionCSVLocal($link) {    
    $fichier = genererFichierInjectionCSV($link);
    $nomFichier = stream_get_meta_data($fichier)['uri'];

    header("Content-Type: application/octet-stream");
    header("Content-Length: ".filesize($nomFichier));
    header("Content-Disposition: attachment; filename=".getenv("CSV_INJECTION_NAME_FILE"));

    readfile($nomFichier);
}

/* Sauvegarder un fichier CSV sur le serveur contenant la liste des dossiers "En cours" et "À traiter" */
function sauvegarderListeDossiersCSVServeur($ftp_stream, $link) {
    $fichier = genererListeDossiersCSV($link);
    $nomFichier = stream_get_meta_data($fichier)['uri'];

    // Si le dossier n'existe pas
    if(!is_dir(cheminVersServeurFTP()."/".getenv("CSV_FOLDERS_FILE_PATH"))) {
        ftp_mkdir($ftp_stream, getenv("CSV_FOLDERS_FILE_PATH")); // Création du dossier
    }
    $path = getenv("CSV_FOLDERS_FILE_PATH")."/".getenv("CSV_FOLDERS_NAME_FILE");
    $result = ftp_put($ftp_stream, $path, $nomFichier, FTP_BINARY);

    fclose($fichier);

    return $result;
}

/* Télécharge un fichier CSV en local contenant la liste des dossiers "En cours" et "À traiter" */
function telechargerListeDossiersCSVLocal($link) {    
    $fichier = genererListeDossiersCSV($link);
    $nomFichier = stream_get_meta_data($fichier)['uri'];

    header("Content-Type: application/octet-stream");
    header("Content-Length: ".filesize($nomFichier));
    header("Content-Disposition: attachment; filename=".getenv("CSV_FOLDERS_NAME_FILE"));

    readfile($nomFichier);
}

/* Génère un lien pour le suivi du dossier de référence '$ref' */
/* => Chaine de caractère */
function genererLienSuivi($ref) {
    $host = $_SERVER['HTTP_HOST'];
    
    return "http://$host/frontOffice/suivi.php?RefD=$ref";
}

/* Affiche un message de titre '$title', de contenu '$body', de glyphicon '$icon' et ayant un type Boostrap */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function genererMessage($title, $body, $icon, $type) {
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

/* Retourne les données pour le fichier de sauvegarde */
function recupererDossierInjection($link) {
    $query = "SELECT DISTINCT RefD, NirA, DateD, Mnemonique "
            ."FROM dossier d, justificatif j, listemnemonique m, assure a "
            ."WHERE d.StatutD = 'Terminé' "            
            ."AND j.CodeD = d.CodeD "            
            ."AND m.CodeM = j.CodeM "            
            ."AND a.CodeA = d.CodeA ";

    return mysqli_query($link, $query);
}

/* Retourne la liste des dossiers "à traiter" et "en cours" dans la BD */
function recupererListeDossiers($link) {
    $query = "SELECT a.CodeA, NirA, NomA, PrenomA, TelA, "
            ."MailA, CodeD, StatutD, DateD, RefD, DateAM "
            ."FROM assure a, dossier d "
            ."WHERE (d.StatutD = 'À traiter' "
            ."OR d.StatutD = 'En cours') "
            ."AND d.CodeA = a.CodeA ";

    return mysqli_query($link, $query);
}

/* Redirige vers la page '$nomPage' */
function redirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    
    exit;
}

/* Redirige vers la page de connexion technicien pour qu'il s'identifie */
function demandeDeConnexion() {
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') 
    === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
    $host = $_SERVER['HTTP_HOST'];
    $url = $protocol . '://' . $host . $script . '?' . $params;
    
    redirigerVers("index.php?redirect=$url");
}

/*------------------------------------------------------------------
 	FONCTIONS : FRONT OFFICE (INTERFACE ASSURE)
------------------------------------------------------------------*/

/* Renvoie les catégories d'assuré actives et les types de documents (mnémoniques) leur étant associés */
/* => [Objet de type array si succès, NULL sinon] */
function categoriesActivesAvecMnemoniques($link) {
    $query = "SELECT ca.CodeC, ca.NomC, ca.DesignationC, lm.CodeM, lm.Mnemonique, lm.Designation, cc.Label 
    FROM categorie ca, listemnemonique lm, concerner cc 
    WHERE ca.StatutC = 'Actif' AND cc.CodeC = ca.CodeC AND cc.CodeM = lm.CodeM 
    UNION 
    SELECT ca.CodeC, ca.NomC as nomCategorie, ca.DesignationC, null, null, null, null 
    FROM categorie ca WHERE ca.StatutC = 'Actif' 
    AND ca.CodeC NOT IN (SELECT CodeC FROM concerner) 
    ORDER BY CodeC";

    $result = mysqli_query($link, $query);

    return $result;
}

/* Vérifie si '$nir' correspond au NIR d'un assuré déjà enregistré dans la base de données */
/* => [Vrai si le NIR est reconnu, Faux sinon] */
function assureExiste($nir, $link) {
    $query = "SELECT * FROM assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Renvoie les informations de l'assuré ayant le NIR '$nir' sous la forme d'une liste */
/* => [Objet de type array si l'assuré est déjà enregistré, NULL sinon] */
function chercherAssureAvecNIR($nir, $link) {
    $query = "SELECT * FROM assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier ayant pour référence '$ref' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherDossierAvecREF($ref, $link) {
    $query = "SELECT * FROM assure A, dossier D "
            ."WHERE A.CodeA = D.CodeA AND RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations correspondant au mnémonique '$mnemonique' */
/* => [Objet de type array si le mnémonique existe, NULL sinon] */
function chercherObjetMnemoAvecMnemo($mnemonique, $link) {
    $query = "SELECT * FROM listemnemonique "
            ."WHERE Mnemonique = '$mnemonique'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la référence du dossier ayant pour code '$codeDossier' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherREFAvecCodeD($codeDossier, $link) {
    $query = "SELECT RefD FROM assure A, dossier D "
            ."WHERE A.CodeA = D.CodeA AND CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de justificatifs d'un dossier ayant le mnémonique $mnemo */
/* => [Entier positif] */
function compterPJDansDossierAvecMnemo($codeAssure, $codeDossier, $codeMnemonique, $link) {   
    $query = "SELECT COUNT(*) AS NombreJustificatif "
            ."FROM assure A, dossier D, justificatif J, listeMnemonique M "
            ."WHERE A.CodeA = D.CodeA AND D.CodeD = J.CodeD AND M.CodeM = J.CodeM "
            ."AND A.CodeA = $codeAssure AND D.CodeD = $codeDossier AND J.CodeM = $codeMnemonique";
    $result = mysqli_query($link, $query);
    
    return mysqli_fetch_array($result)["NombreJustificatif"];
}

/* Crée le répertoire ayant pour nom '$ref' à l'emplacement ' getenv("STORAGE_PATH")/$nirA' (cf. haut de page) */
/* => [Vrai si le répertoire de l'assuré a bien été créé, Faux sinon] */
function creerRepertoireAM($ftp_stream, $ref, $nir) {
    $dirname =  getenv("STORAGE_PATH"). "/$nir/$ref";
    return ftp_mkdir($ftp_stream, $dirname);
}

/* Crée un répertoire ayant pour nom '$nir' à l'emplacement ' getenv("STORAGE_PATH")' (cf. haut de page) */
/* => [Vrai si le répertoire de l'assuré a bien été créé, Faux sinon] */
function creerRepertoireNIR($ftp_stream, $nir) {
    $dirname =  getenv("STORAGE_PATH"). "/$nir";
    return ftp_mkdir($ftp_stream, $dirname);
}

/* Vérifie si le dossier de référence '$ref' existe déjà dans la base de données */
/* => [Vrai si la référence de dossier est reconnue, Faux sinon] */
function dossierExiste($ref, $link) {
    $query = "SELECT RefD FROM dossier WHERE RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Enregistre les données d'un assuré dans la base de données */
/* => [Vrai si les données de l'assuré ont bien été enregistrées, Faux sinon] */
function enregistrerAssure($nir, $nom, $prenom, $tel, $mail, $link) {
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
function enregistrerDossier($codeAssure, $dateAM, $ref, $link) {
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

/* Enregistre les informations concernant un nouveau fichier */
/* => [Vrai si les informations du fichier ont bien été enregistrées, Faux sinon] */
function enregistrerFichier($cheminJustificatif, $codeDossier, $codeMnemonique, $link) {
    $keys = ""; $values = "";
    if ($cheminJustificatif != NULL) {
        $keys .= "CheminJ, "; $values .= "'" . $cheminJustificatif . "', ";
    }
    if ($codeDossier != NULL) {
        $keys .= "CodeD, "; $values .= $codeDossier . ", ";
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

/* Enregistre les fichiers de '$listeFichiers' à l'emplacement ' getenv("STORAGE_PATH")/$nirA/$refD' */
/* => [Liste(A : Booléen, B : Chaîne de caractères, C : Chaîne de caractères)]  */
/*      => A = Vrai si l'enregistrement a réussi, Faux sinon                    */
/*      => B = Nom du fichier téléchargé                                        */
/*      => C = Mnémonique complet affilié au fichier                            */
function enregistrerFichiers($ftp_stream, $listeFichiers, 
    $codeAssure, $nir, $codeDossier, $ref, $link) {
    $resultats = array();

    foreach ($listeFichiers as $key => $fichier) {
        $mnemonique = chercherObjetMnemoAvecMnemo($key, $link);

        $j = compterPJDansDossierAvecMnemo(
            $codeAssure, $codeDossier, $mnemonique["CodeM"], $link
        ) + 1;

        for ($i = 0; $i < count($fichier['name']); $i++) {
            if ($fichier['name'][$i] != "") {
                $file = basename($fichier['name'][$i]);

                $target_dir =  getenv("STORAGE_PATH");
                $ext = strtolower(pathinfo($file)['extension']);
                $cheminJustificatif = "$target_dir/";
                $cheminJustificatif .= implode("", explode(" ", $nir));
                $cheminJustificatif .= "_".$key."_IJ_($ref-$j).$ext";

                $designation = $mnemonique["Designation"] . " No. " . $j;

                if (enregistrerFichier($cheminJustificatif, $codeDossier, $mnemonique["CodeM"], $link)) {
                    if (ftp_put($ftp_stream, $cheminJustificatif, $fichier['tmp_name'][$i], FTP_BINARY)) {
                        $resultats[] = array(TRUE, $file, $designation);
                    } else {
                        $resultats[] = array(FALSE, $file, $designation);
                    }
                } else {
                    $resultats[] = array(FALSE, $file, $designation);
                }
                $j++;
            }
        }
    }
    return $resultats;
}

/* Vérifie si la référence d'un dossier '$ref' est bien associée à un NIR '$nir' */
/* => [Vrai s'il y a bien une correspondance entre le NIR '$nirA' et la référence '$redD', Faux sinon] */
function estAssocie($nir, $ref, $link) {
    $query = "SELECT a.* "
            ."FROM assure a, dossier d  "
            ."WHERE a.NirA = '$nir' "
            ."AND d.CodeA = a.CodeA "
            ."AND d.RefD = '$ref'" ;
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

/* Genère une référence unique de taille '$nbChar' d'un dossier */
/* => [Chaine de caractères de taille '$nbChar'] */
function genererReferenceDossier($nbChar, $link) {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= $listeChar[rand(0, strlen($listeChar) - 1)]; //Sélection d'un caractère aléatoire
        }
    } while(dossierExiste($ref, $link));

    return $ref;
}

/* Vérifie si le chemin $chemin exite déjà dans la base de données */
/* => [Vrai si le chemin est déjà enregistré, Faux sinon] */
function fichierExiste($link, $chemin){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '$chemin'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Supprime physiquement le fichier de chemin '$cheminJustificatif' */
/* => [True si le fichier a été supprimer, False Sinon  */
function supprimerFichierPhysique($ftp_stream, $cheminJustificatif) {
    if (ftp_delete($ftp_stream, $cheminJustificatif)) return True ;
    else return False;
}

/* Supprime le fichier de chemin '$cheminJustificatif' de la BD*/
/* => [True si le fichier a été supprimer, False Sinon  */
function supprimerFichierBD($codeJ, $link) {
    $query = "DELETE FROM justificatif "
            ."WHERE CodeJ = $codeJ";

    return mysqli_query($link, $query);
}

/* Met à jour la ligne de données du justificatif de code '$codeJ' dans la BD*/
/* => [True si la ligne a bien été mise à jour, False Sinon  */
function majJustificatifBD($codeJ, $nouveauCheminJ, $link) {
    $query = "UPDATE justificatif "
            ."SET CheminJ = '$nouveauCheminJ', CodeT = NULL, StatutJ = NULL "
            ."WHERE CodeJ = $codeJ";

    return mysqli_query($link, $query);
}

/* Supprime le fichier de chemin $cheminJ1 puis enregistre le fichier de chemin $cheminJ2 */
/* => [True si le fichier a été supprimer, False Sinon  */
function majFichier($ftp_stream, $fichier, $justificatif, $nouveauCheminJ, $link) {
    $codeJ = $justificatif["CodeJ"];
    $codeD = $justificatif["CodeD"];
    $ancienCheminJ = $justificatif["CheminJ"];

    // Suppression physique du fichier de chemin $ancienCheminJ
    if(supprimerFichierPhysique($ftp_stream, $ancienCheminJ)) {
        // Mise à jour du justificatif de code $codeJ
        if(majJustificatifBD($codeJ, $nouveauCheminJ, $link)) {
            //Enregistrement physique du fichier de chemin $cheminJ2
            if(ftp_put($ftp_stream, $nouveauCheminJ, $fichier['tmp_name'], FTP_BINARY)) {
                return True;
            } else {echo "Échec lors de l'ajout physique !";}
        } else {echo "Échec lors de la mise à jour dans la BD !";}
    } else {echo "Échec lors de la suppression physique !";}

    return False;
}

/* Écrase et remplace tous les fichiers de la liste $listeFichiers dans  */
function majFichiers($ftp_stream, $listeFichiers, $codeA, $nirA, $codeD, $refD, $link) {
    $resultats = array();

    foreach ($listeFichiers as $key => $fichier) {
        if(is_int($key)) { // Si le justificatif a été trouvé
            $justificatif = recupererJustificatif($link, $key);
            $ancienCheminJ = $justificatif["CheminJ"];
            $designation = $justificatif["Designation"];
            $nirA = $justificatif["NirA"];
            $refD = $justificatif["RefD"];
    
            $file = basename($fichier['name']);
            
            // Récupération du nom du fichier sans son extension
            $nomFichier = explode(".", basename($ancienCheminJ))[0]; // Le nom du fichier sans l'extension
            $j = substr($nomFichier, strrpos($nomFichier, "_") + 1); // Le numéro du fichier

            $target_dir =  getenv("STORAGE_PATH") . "/$nirA/$refD";
            $ext = strtolower(pathinfo($file)['extension']);
            $nouveauCheminJ = "$target_dir/$nomFichier.$ext";
    
            $designation = "Document mis à jour : $designation No. $j";
            
            if(majFichier($ftp_stream, $fichier, $justificatif, $nouveauCheminJ, $link)) {
                if (ftp_put($ftp_stream, $nouveauCheminJ, $fichier['tmp_name'], FTP_BINARY)) {
                    $resultats[] = array(TRUE, $file, $designation);
                } else {
                    $resultats[] = array(FALSE, $file, $designation);
                }
            } else {
                $resultats[] = array(FALSE, $file, $designation);
            }
        }
        else { // Sinon, on enregistre le fichier
            $mnemonique = chercherObjetMnemoAvecMnemo($key, $link);

            $j = compterPJDansDossierAvecMnemo(
                $codeA, $codeD, $mnemonique["CodeM"], $link
            ) + 1;

            for ($i = 0; $i < count($fichier['name']); $i++) {
                if ($fichier['name'][$i] != "") {
                    $file = basename($fichier['name'][$i]);

                    $target_dir =  getenv("STORAGE_PATH") . "/$nirA/$refD";
                    $ext = strtolower(pathinfo($file)['extension']);
                    $cheminJustificatif = "$target_dir/$key" . "_$j.$ext";

                    $designation = "Document sauvegardé : ".$mnemonique["Designation"]." No. $j";
                    
                    if (enregistrerFichier($cheminJustificatif, $codeD, $mnemonique["CodeM"], $link)) {
                        if (ftp_put($ftp_stream, $cheminJustificatif, $fichier['tmp_name'][$i], FTP_BINARY)) {
                            $resultats[] = array(TRUE, $file, $designation);
                        } else {
                            $resultats[] = array(FALSE, $file, $designation);
                        }
                    } else {
                        $resultats[] = array(FALSE, $file, $designation);
                    }
                    $j++;
                }
            }
        }
    }
        

    return $resultats;
}

/*------------------------------------------------------------------
 	FONCTIONS : BACK OFFICE (INTERFACE TECHNICIEN)
------------------------------------------------------------------*/

/* Récupère le mot de passe haché d'un technicien pour authentification */
/* => [Le mot de passe si le matricule du technicien existe, NULL sinon] */
function authentifierTechnicien($link, $matricule) {
    $query = "SELECT Matricule, MdpT "
            ."FROM technicien "
            ."WHERE Matricule = '$matricule' ";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Change le statut du dossier de code '$codeDossier' en '$statut' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function changerStatutDossier($link, $codeDossier, $statut) {
    $query = "UPDATE dossier SET StatutD = '$statut' WHERE CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Change le statut d'un justificatif de code '$codeJustificatif' en '$statut' */
/* et affecte ou retirer le code d'un technicien de code '$codeTechnicien' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function changerStatutPJ($link, $codeJustificatif, $statut, $codeTechnicien) {
    $query = "UPDATE justificatif ";
    if($statut == "NULL") $query .= "SET StatutJ = NULL, ";
    else $query .= "SET StatutJ = '$statut', ";
    if($codeTechnicien == Null) $query .= "CodeT = NULL ";
    else $query .= "CodeT = $codeTechnicien ";
    $query .= "WHERE CodeJ = $codeJustificatif";
    
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie les informations d'un dossier en cours de traitement ou traité ayant pour code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherDossierTraiteAvecCodeD($codeDossier, $link) {
    $query = "SELECT * FROM assure A, dossier D, traiter Tr, technicien T "
            ."WHERE A.CodeA = D.CodeA AND D.CodeD = $codeDossier "
            ."AND D.CodeD = Tr.CodeD AND T.CodeT = Tr.CodeT "
            ."AND Tr.DateTraiterD = (SELECT MAX(DateTraiterD) "
                                    ."FROM traiter "
                                    ."WHERE CodeD = $codeDossier)";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Affiche la classe CSS correspondante pour chaque bouton de traitement de dossier du      */
/* du fichier traiter.php                                                                   */
/* => Effectue seulement un affichage (pas de valeur de retour)                             */
/* => Active ou désactive un bouton permettant de modifier le statut d'un dossier           */
/* => $sessionValue correspond au statut du dossier en cours ($_SESSION['statut'])          */
/* => $buttonValue est soit 'En cours', soit 'Classé sans suite' ou bien 'Terminé'          */
/* => $codeT_dossier est le code du technicien qui est actuellement connecté                */
/* => Selon si le dossier est dans sa corbeille ou pas, il pourra ou non modifier           */
/* => le statut du dossier courant                                                          */
function classBoutonTraiterDossier($sessionValue, $buttonValue, $codeT_dossier, $codeT_courant) {
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

/* Renvoie la classe CSS correspondante pour chaque bouton de traitement de PJ du           */
/* du fichier traiter.php                                                                   */
/* => Active ou désactive un bouton permettant de modifier le statut d'une PJ               */
/* => $sessionValue correspond au statut du PJ courant                                      */
/* => $buttonValue est soit "NULL", soit 'Valide' ou bien 'Invalide'                        */
/* => $codeT_dossier est le code du technicien qui est actuellement connecté                */
/* => Selon si le dossier est dans sa corbeille ou pas, il pourra ou non modifier           */
/* => le statut du dossier courant                                                          */
function classBoutonTraiterPJ($sessionValue, $buttonValue, $codeT_dossier, $codeT_courant, $statutDossier) {
    if($statutDossier == "Terminé" || $statutDossier == "Classé sans suite") $text = "disabled";
    else $text = "";
    switch($sessionValue) {
        case NULL:
            if($codeT_dossier == $codeT_courant) {
                switch($buttonValue) {
                    case NULL:
                        return "btn btn-warning disabled";
                    case "Valide":
                        return "btn btn-default $text";
                    case "Invalide":
                        return "btn btn-default $text";
                }
            }
            else { // désactiver les boutons si en cours de traitement par un autre technicien
                switch($buttonValue) {
                    case NULL:
                        return "btn btn-warning disabled";
                    case "Valide":
                        return "btn btn-default disabled";
                    case "Invalide":
                        return "btn btn-default disabled";
                }
            }
            break;
        case "Valide":
            if($codeT_dossier == $codeT_courant) {
                switch ($buttonValue) {
                    case NULL:
                        return "btn btn-default $text";
                    case "Valide":
                        return "btn btn-success disabled";
                    case "Invalide":
                        return "btn btn-default disabled";
                }
            }
            else {
                switch ($buttonValue) {
                    case NULL:
                        return "btn btn-default disabled";
                    case "Valide":
                        return "btn btn-success disabled";
                    case "Invalide":
                        return "btn btn-default disabled";
                }
            }
            break;
        case "Invalide":
            if($codeT_dossier == $codeT_courant) {
                switch ($buttonValue) {
                    case NULL:
                        return "btn btn-default $text";
                    case "Valide":
                        return "btn btn-default disabled";
                    case "Invalide":
                        return "btn btn-danger disabled";
                }
            }
            else {                
                switch ($buttonValue) {
                    case NULL:
                        return "btn btn-default disabled";
                    case "Valide":
                        return "btn btn-default disabled";
                    case "Invalide":
                        return "btn btn-danger disabled";
                }
            }
            break;
    }
}

/* Renvoie les informations du technicien ayant le matricule '$matricule' sous la forme d'une liste */
/* => [Objet de type array si le technicien est déjà enregistré, NULL sinon] */
function donneesTechnicien($link, $matricule) {
    $query = "SELECT CodeT, NomT, PrenomT "
            ."FROM technicien t "
            ."WHERE t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la liste complète des dossiers contenus dans la BD */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function dossiersCorbeilleGenerale($link) {
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD "
            ."FROM dossier d, assure a "
            ."WHERE d.CodeA = a.CodeA "
            ."ORDER BY d.DateD";
    $result = mysqli_query($link, $query);
    
    return $result;
}

/* Renvoie la liste des dossiers en cours de traitement par le technicien de code '$codeTechnicien' */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function dossiersCorbeilleTechnicien($link, $codeTechnicien) {
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

/* Génère et renvoie un message pré-rempli pour demander des pièces à un assuré */
/* Séparation nécessaire pour l'insertion des commentaires du technicien */
/* et empêcher la modification des données principales */
/* => [Objet de type array de 2 éléments : (0 : partie du haut, 1 : partie du bas) ] */
function genererMessageAssure($nomAssure, $prenomAssure, $refDossier) {
    $haut  = "<!DOCTYPE html>";
    $haut .= "<html lang='fr'>";
    $haut .= "   <head>";
    $haut .= "        <meta charset='utf-8'>";
    $haut .= "        <meta name='viewport' content='width=device-width, initial-scale=1'>";
    $haut .= "        <style>";
    $haut .= "           h3 {margin-bottom: 25px; font-style: italic;}";
    $haut .= "           p {margin-bottom: 10px;}";
    $haut .= "           span.esp {margin-right: 20px;}";
    $haut .= "        </style>";
    $haut .= "    </head>"; 
    $haut .= "    <body>";

    $haut .= "    <h3>Bonjour $prenomAssure $nomAssure,</h3>";

    $haut .= "    <p>";
    $haut .= "      <span class='esp'></span>Nous souhaiterions vous informer que lors de votre ";
    $haut .= "      dernier dépôt, certaines pièces justificatives affiliées au dossier ";
    $haut .= "      de référence ".$refDossier." semblent invalides et/ou manquantes.";
    $haut .= "    </p>";
    $haut .= "    <p id='comment'>";

    /****** LE COMMENTAIRE DU TECHNICIEN SERA PLACÉ ICI ******/

    $bas = "      </p>";
    $bas .= "     <p><span class='esp'></span><em>Merci de cliquer sur le lien suivant afin de déposer les documents demandés :</em><br>";
    $bas .= "     <span class='esp'></span><a href='".DEPOSITE_LINK."?RefD=$refDossier' target='_blank'>".DEPOSITE_LINK."?RefD=$refDossier</a></p>";
    $bas .= "     <p><span class='esp'></span><strong>NB : Vous aurez besoin de votre numéro de sécurité sociale pour vous authentifier.</strong></p>";
  
    $bas .= "     <p><span class='esp'></span>Bien cordialement,</p>";
    $bas .= "     <h4 style='margin-top: 30px; font-style: italic;'>- La CPAM de la Haute-Garonne -</h4>";
    if(FOOTER_EMAIL != "") $bas .="<hr><em>".FOOTER_EMAIL."</em>";

    $bas .= '</body></html>';

    return [$haut, $bas];
}

/* Enregistre le contenu d'un mail envoyé à l'assuré de code '$codeAssure' par le technicien de code '$codeTechnicien'*/
/* => [Vrai si l'enregistrement a bien été effectué, Faux sinon] */
function enregistrerMessageAssure($codeAssure, $codeTechnicien, $contenu, $link) {
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

/* Fonction qui envoi la liste des dossiers restants à traiter par mail */
/* Ce mail est à renseigner dans le fichier ".env" */
function envoyerMailFichierInjectionCSV($ftp_stream, $link) {
    $to = getenv("CSV_SENDMAIL_TO");
    $subject = "Fichier CSV pour injection dans DIADEME";
    $content = "<h3>- Site PJPE -</h3>";
    $content .= "<p>";
    $content .= "<em>Ci-joint, le fichier CSV pour l'injection des justificatifs dans DIADEME.</em>";
    $content .= "</p><hr>";
    $content .= "Message automatique, merci de ne pas y répondre.";
    $type = "text/html";

    // Récupération du fichier CSV en fichier temporaire
    $tmpfile = genererFichierInjectionCSV($link);
    if($tmpfile != Null) {
        $attachement = stream_get_meta_data($tmpfile)['uri'];
        $nameFile = getenv("CSV_INJECTION_NAME_FILE");
        $res = envoyerMail($to, $subject, $content, $type, $attachement, $nameFile);
        fclose($tmpfile);
        return $res;
    }
    else return False;
}

/* Fonction qui envoi la liste des dossiers restants à traiter par mail */
/* Ce mail est à renseigner dans le fichier ".env" */
function envoyerMailFichierDossiersCSV($ftp_stream, $link) {
    $to = getenv("CSV_SENDMAIL_TO");
    $subject = "Fichier CSV pour le listage des dossiers restants à traiter";
    $content = "<h3>- Site PJPE -</h3>";
    $content .= "<p>";
    $content .= "<em>Ci-joint, le fichier CSV contenant la liste des fichiers ";
    $content .= "restant à traiter et en cours de traitement.</em>";
    $content .= "</p><hr>";
    $content .= "Message automatique, merci de ne pas y répondre.";
    $type = "text/html";

    // Récupération du fichier CSV en fichier temporaire
    $tmpfile = genererListeDossiersCSV($link);
    if($tmpfile != Null) {
        $attachement = stream_get_meta_data($tmpfile)['uri'];
        $nameFile = getenv("CSV_FOLDERS_NAME_FILE");
        $res = envoyerMail($to, $subject, $content, $type, $attachement, $nameFile);
        fclose($tmpfile);
        return $res;
    }
    else return False;
}

/* Envoie un mail */
/* 'SENDER_EMAIL_ADDRESS' : Adresse de l'expéditeur (variable globale tout en haut) */
/* '$to'                  : Adresse de destination                                  */
/* '$subject'             : Sujet du mail                                           */
/* '$content'             : Contentu du mail                                        */
/* '$type'                : Type de mail                                            */
/* Valeurs possibles de $type : => 'text/html'  : Message de type HTML (par défaut) */
/*                              => 'text/plain' : Message standard                  */
/* => [Vrai si le message a bien été envoyé, Faux sinon]                            */
function envoyerMail($to, $subject, $content, $type, $attachement = Null, $nameFile = Null) {
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = getenv("SMTP_HOST");                    // Set the SMTP server to send through
        $mail->SMTPAuth   = True;                                   // Enable SMTP authentication
        $mail->Username   = getenv("SMTP_USER");                    // SMTP username
        $mail->Password   = getenv("SMTP_PWD");                     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = getenv("SMTP_PORT");                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom(getenv("SENDMAIL_FROM"), getenv("SENDMAIL_NAME"));
        $mail->addAddress($to);                                 // Add a recipient
        //$mail->addAddress('ellen@example.com', 'name');       // Add a recipient with name
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        // Attachments
        if($nameFile != Null) $mail->addAttachment($attachement, $nameFile);        
        else if($attachement != Null) $mail->addAttachment($attachement);

        // Content
        if($type == "text/html") {        
            $mail->isHTML(True);                                // $Content in HTML
        }
        else {
            $mail->isHTML(False);                               // $Content not in HTML
        }

        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = $content;

        $mail->send();
        
        return True;
    } catch (Exception $e) {
        return False;
    }
}

/* Envoie un mail de confirmation d'enregistrement de données à l'adresse '$to' */
/* Pour plus d'informations, consulter la fonction 'envoyerMail()'                */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function envoyerMailConfirmationEnregistrement($prenomAssure, $nomAssure, $to) {
    $content  = '<!DOCTYPE html>';
    $content .= '<html lang="fr">';
    $content .= '   <head>';
    $content .= '        <meta charset="utf-8">';
    $content .= '        <meta name="viewport" content="width=device-width, initial-scale=1">';
    $content .= '        <style>';
    $content .= '           h3 {margin-bottom: 25px; font-style: italic;}';
    $content .= '           p {margin-bottom: 10px;}';
    $content .= '           span.esp {margin-right: 20px;}';
    $content .= '        </style>';
    $content .= '    </head>'; 
    $content .= '    <body>';

    $content .= "<h3>Bonjour $prenomAssure $nomAssure,</h3>";
    $content .= "<p><span class='esp'></span>Suite à votre envoi via la plateforme PJPE, ";
    $content .= "nous vous confirmons que vos documents ont bien été réceptionnés ";
    $content .= "par nos services.</p>";

    $content .= "<p><span class='esp'></span>Il est possible que soyez notifié par mail ";
    $content .= "en cas d'irrecevabilité d'une de vos pièces justificatives.</p>";

    $content .= "<p><span class='esp'></span>C'est pourquoi, afin d'optimiser le traitement ";
    $content .= "de votre demande, et donc du délai de versement de vos indemnités, ";
    $content .= "vous êtes invités à consulter régulièrement votre messagerie ";
    $content .= "électronique.</p>";

    $content .= "<p><span class='esp'></span>Ci-dessous, vous trouverez un lien menant à une page vous permettant ";
    $content .= "de consulter l'état d'avancement de votre demande :</p>";

    // Génération du lien de suivi
    $content .= "<span class='esp'></span><a href='".genererLienSuivi($_SESSION["RefD"])."'>".genererLienSuivi($_SESSION["RefD"])."</a><br>";
    $content .= "<p><span class='esp'></span><strong>NB : Vous aurez besoin de votre numéro de sécurité sociale (NIR) pour vous authentifier.</strong></p>";
    
    $content .= "<p><span class='esp'></span>Ci-dessous, vous trouverez les informations relatives à votre ";
    $content .= "dossier :</p>";
    $content .= "<span class='esp'></span>Référence de dossier : <strong>".$_SESSION["RefD"]."</strong><br>";

    $content .= "<p><span class='esp'></span>En vous souhaitant bonne réception,</p>";

    $content .= "<p><span class='esp'></span>Bien cordialement,</p>";
    $content .= "<h4 style='margin-top: 30px; font-style: italic;'>- La CPAM de la Haute-Garonne -</h4>";

    $content .= '</body></html>';

    return envoyerMail($to, MAIL_CONFIRM_SUBJECT, $content, "text/html");
}

/* Envoie un mail de confirmation d'enregistrement de données à l'adresse '$to' */
/* Pour plus d'informations, consulter la fonction 'envoyerMail()'                */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function envoyerMailConfirmationTraitement($prenomAssure, $nomAssure, $refDossier, $to, $statut) {
    $content  = '<!DOCTYPE html>';
    $content .= '<html lang="fr">';
    $content .= '   <head>';
    $content .= '        <meta charset="utf-8">';
    $content .= '        <meta name="viewport" content="width=device-width, initial-scale=1">';
    $content .= '        <style>';
    $content .= '           h3 {margin-bottom: 25px; font-style: italic;}';
    $content .= '           p {margin-bottom: 10px;}';
    $content .= '           span.esp {margin-right: 20px;}';
    $content .= '        </style>';
    $content .= '    </head>'; 
    $content .= '    <body>';

    $content .= "<h3>Bonjour $prenomAssure $nomAssure,</h3>";

    if($statut == "Classé sans suite") {
        $content .= "<p><span class='esp'></span>Suite à votre envoi via la plateforme PJPE, ";
        $content .= "nous vous informons que le traitement de votre dossier (Référence : ";
        $content .= "$refDossier) ne nous permet pas de donner une suite favorable. ";
        $content .= "Les justificatifs que vous avez fournis ne permettent pas ";
        $content .= "d'aboutir au versement de vos indemnités journalières.</p>";
    }
    else {
        $content .= "<p><span class='esp'></span>Suite à votre envoi via la plateforme PJPE, ";
        $content .= "nous avons le plaisir de vous informer que votre dossier (Référence : $refDossier) ";
        $content .= "est complet et a été traité par nos services. Vous recevrez vos indemnités journalières ";
        $content .= "dans les prochains jours.</p>";
    }

    $content .= "<p><span class='esp'></span>Il vous est toujours possible ";
    $content .= "de consulter le suivi du traitement de votre dossier via le lien ci-dessous :</p>";

    // Génération du lien de suivi
    $content .= "<span class='esp'></span><a href='".genererLienSuivi($refDossier)."'>".genererLienSuivi($refDossier)."</a><br>";
    $content .= "<p><span class='esp'></span><strong>NB : Vous aurez besoin de votre numéro de sécurité sociale (NIR) pour vous authentifier.</strong></p>";
    
    $content .= "<p><span class='esp'></span>Bien cordialement,</p>";
    $content .= "<h4 style='margin-top: 30px; font-style: italic;'>- La CPAM de la Haute-Garonne -</h4>";

    $content .= '</body></html>';

    return envoyerMail($to, MAIL_CONFIRM_TREATMENT, $content, "text/html");
}

/* Envoie un mail de sujet '$subject' et de contenu '$txt' à l'adresse '$mail' */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function envoyerMailDemandePJ($mail, $refD, $content) {
    return envoyerMail($mail, MAIL_REQUEST_SUBJECT." [REF. $refD]", $content, "text/html");
}

/* Extrait les informations d'un message pour les renvoyer sous forme d'une liste */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
/* => [ainsi que la référence du dossier]                                                           */
/* => Ne fonctionne que sur les messages générés automatiquement                                     */
function extraireMessage($contenu) {
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

/* Retire le dossier de code '$codeDossier' de la liste des dossiers traités */
/* => [Vrai si le retrait a bien été effectué, Faux sinon] */
function libererDossier($link, $codeDossier) {
    $query = "DELETE FROM traiter WHERE CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie tous les messages envoyés à l'assuré de code '$codeA' */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
// Liste des messages adressés à un assuré
function listeMessages($codeAssure, $link) {
    $query = "SELECT DateEnvoiM, Contenu, T.Matricule "
            ."FROM message M, assure A, technicien T "
            ."WHERE A.CodeA = M.CodeA "
            ."AND A.CodeA = $codeAssure "
            ."AND T.CodeT = M.CodeT "
            ."ORDER BY DateEnvoiM DESC";
 
    return mysqli_query($link, $query);
}

/* Renvoie le nombre de dossiers restant à traiter au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiter($link) {
    $query = "SELECT COUNT(*) AS nbDossiersAtraiter "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter' "
            ."AND DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre total de dossiers restant à traiter */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiterTotal($link) {
    $query = "SELECT COUNT(*) AS nbDossiersAtraiterTotal "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers classés sans suite au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersClasses($link) {
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersClasses "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Classé sans suite' "
            ."And DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers reçus au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersRecus($link) {
    $query = "SELECT COUNT(*) AS nbDossiersRecus "
            ."FROM dossier d "
            ."WHERE DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers classés comme 'Terminé' au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersTermines($link) {
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersTermines "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Terminé' "
            ."AND DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la liste des fichiers du dossier de code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function recupererJustificatif($link, $codeJ) {
    $query = "SELECT * "
            ."FROM justificatif j, listemnemonique l, dossier d, assure a "
            ."WHERE j.CodeJ = $codeJ "
            ."AND j.CodeD = d.codeD "
            ."AND j.CodeM = l.codeM "
            ."AND d.CodeA = a.codeA ";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la liste des fichiers du dossier de code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function recupererJustificatifs($link, $codeDossier) {
    $query = "SELECT CodeJ, CheminJ, Mnemonique, StatutJ, CodeT AS Matricule "
            ."FROM justificatif j, listemnemonique l "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeT IS NULL "
            ."AND j.CodeD = '$codeDossier'"
            ." UNION "
            ."SELECT CodeJ, CheminJ, Mnemonique, StatutJ, Matricule "
            ."FROM justificatif j, listemnemonique l, technicien t "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeT = t.CodeT "
            ."AND j.CodeD = '$codeDossier' "
            ."ORDER BY StatutJ, CodeJ";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie la liste des fichiers du dossier de code '$codeDossier' ayant pour statut $statutPJ */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function recupererJustificatifsAvecStatutJ($link, $codeDossier, $statutPJ) {
    $query = "SELECT CodeJ, CheminJ, Mnemonique, Designation, Matricule "
            ."FROM justificatif j, listemnemonique l, technicien t "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeT = t.CodeT "
            ."AND j.CodeD = '$codeDossier' "
            ."AND j.StatutJ = '$statutPJ'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie la liste de toutes les mnémoniques de la BD */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function recupererMnemoniques($link) {
    $query = "SELECT * FROM listemnemonique";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie le nombre de justificatifs correspondant au statut $statutJ */
/* => [Entier nul ou posiitf] */
function nbPJsSelonStatut($link, $statutJ, $codeDossier) {
    $query = "SELECT COUNT(*) AS num "
            ."FROM justificatif "
            ."WHERE CodeD = $codeDossier ";
    if ($statutJ == "NULL") $query .= "AND StatutJ IS NULL";
    else $query .= "AND StatutJ = '$statutJ'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result)['num'];
}

/* Renvoie le nombre de justificatifs correspondant au statut $statutJ */
/* => [Entier nul ou posiitf] */
function nbPJsAvecCodeDossier($link, $codeDossier) {
    $query = "SELECT COUNT(*) AS num "
            ."FROM justificatif "
            ."WHERE CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result)['num'];
}

/* Affilie le dossier de code '$codeDossier' au technicien de code '$codeTechnicien' et change son statut en '$statut' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function traiterDossier($codeTechnicien, $codeDossier, $statut, $link) {
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
        if(!changerStatutDossier($link, $codeDossier, $statut)){
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

/* Vérifie l'unicité du matricule '$matricule' */
/* => ["Unique" si c'est vrai, message d'erreur sinon] */
function verifierMatricule($link, $matricule) {
    $query = "SELECT * FROM technicien WHERE Matricule='$matricule'";
    $curseur = mysqli_query($link, $query);

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


/*------------------------------------------------------------------
 	FONCTIONS : ADMIN (INTERFACE ADMINISTRATEUR)
------------------------------------------------------------------*/

/* Retourne les catégories actives */
/* => [Objet de type array si existe, NULL sinon] */
function categoriesActives($link) {
    $query = "SELECT * "
            ."FROM categorie c "
            ."WHERE c.StatutC = 'Actif'";

    return mysqli_query($link, $query) ;
}

/* Retourne les catégories inactives */
/* => [Objet de type array si existe, NULL sinon] */
function categoriesInactives($link) {
    $query = "SELECT * "
            ."FROM categorie c "
            ."WHERE c.StatutC = 'Inactif'";
  
    return mysqli_query($link, $query);
}

/* Retourne la liste des mnémoniques existants */
/* => [Objet de type array si existe, NULL sinon] */
function listeMnemoniques($link) {
    $query = "SELECT *  "
            ."FROM listemnemonique ";
            
    return mysqli_query($link, $query);  
}

/* Retourne la liste des mnémoniques avec la catégorie */
function listeMnemoniqueAvecCodeC($link, $codeC) {
    $query = "SELECT c.CodeM, Mnemonique, Designation, Label "
            ."FROM concerner c, listemnemonique l "
            ."WHERE CodeC = $codeC "
            ."AND c.CodeM = l.CodeM "
            ."  UNION "
            ."SELECT CodeM, Mnemonique, Designation, NULL "
            ."FROM listemnemonique "
            ."WHERE CodeM NOT IN ("
            ."  SELECT CodeM "
            ."  FROM concerner "
            ."  WHERE CodeC = $codeC);";

    return mysqli_query($link, $query);  
}
?>
