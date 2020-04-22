<?php

//Variables de connexion
define("HOST", "localhost");
define("USER", "root");
define("PWD_MYSQL", "");
define("BD_MYSQL", "bd_cpam");
define("PORT", "3306");

//Chemin vers l'espace où sont enregistrés les dossiers des dossiers
define("STORAGE_PATH",
       "C:/Users/axelt/Documents/4 - Professionnels/DCT_2019-2020/Pièces justificatives"
);

// Connexion BD
function connexionMySQL() {
    //$cres = mysqli_connect(SERVER_MYSQL, ID_MYSQL, PWD_MYSQL, BD_MYSQL);
    $link = mysqli_connect(HOST, USER, PWD_MYSQL, BD_MYSQL, PORT);
    
    /* Vérification de la connexion */
    if ($link == NULL) {
        echo "Erreur : Impossible de se connecter à MySQL."."<br>";
        echo "Errno de débogage : ".utf8_encode(mysqli_connect_errno())."<br>";
        echo "Erreur de débogage : ".utf8_encode(mysqli_connect_error())."<br>";
        exit;
<<<<<<< Updated upstream
    }
    if(mysqli_select_db($link, BD_MYSQL) == NULL) {
=======
    } else {
        if (mysqli_SELECT_db($link, BD_MYSQL) == null) {
            echo ("<p> Vérifier que la base de données est bien sur MariaDB </p>");
            return null;
        }
    }
    if (mysqli_SELECT_db($link, BD_MYSQL) == NULL) {
>>>>>>> Stashed changes
        echo "Erreur : Impossible de se connecter à la base de données.";
        exit;
    }
    
    return $link;
}

function CharactereAleatoire() {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $listeChar[rand(0, strlen($listeChar)-1)];
}

//Genère une référence valide pour un dossier
function GenererReferenceDossier($nbChar, $link) {    
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= CharactereAleatoire();
        }
    } while(DossireExiste($ref, $link));
    
    return $ref;
}

<<<<<<< Updated upstream
//Vérifie si un assuré est déjà enregitré
function AssureExiste($NirA, $link) {
    $query = "SELECT * FROM Assure WHERE NirA = '".$NirA."'";
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}
=======
>>>>>>> Stashed changes

//Renvoie les informations d'un assuré via son NIR sous la forme d'une liste
function ChercherAssureAvecNIR($NirA, $link) {
    $query = "SELECT * FROM Assure WHERE NirA = '".$NirA."'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Renvoie les informations d'un dossier via sa référence sous la forme d'une liste
function ChercherDossierAvecREF($RefD, $link) {
    $query = "SELECT * FROM Assure A, Dossier D ";
    $query .= "WHERE A.CodeA = D.CodeA AND RefD = '".$RefD."'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

//Retourne le code correspond au mnémonique entré en paramètre
function ChercherMnemoniqueAvecMnemonique($Mnemonique, $link) {
    $query = "SELECT * FROM Listemnemonique ";
    $query .= "WHERE Mnemonique = '".$Mnemonique."'";
    
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
function DossireExiste($RefD, $link) {    
    $query = "SELECT RefD FROM Dossier WHERE RefD = '".$RefD."'";
    $result = mysqli_query($link, $query);
<<<<<<< Updated upstream
    
=======
    return (mysqli_fetch_array($result) != NULL);
}

// Vérifie si un justificatif est dans la BD
function FichierExiste($link, $path){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '" . $path . "'";
    $result = mysqli_query($link, $query);
>>>>>>> Stashed changes
    return (mysqli_fetch_array($result) != NULL);
}

//Enregistre les données d'un assuré dans la BD
function EnregistrerAssure($NirA, $NomA, $PrenomA, $TelA, $MailA, $link) {
    $keys = ""; $values = "";
    if($NirA != NULL) {$keys .= "NirA, "; $values .= "'".$NirA."', ";}
    if($NomA != NULL) {$keys .= "NomA, "; $values .= "'".$NomA."', ";}
    if($PrenomA != NULL) {$keys .= "PrenomA, "; $values .= "'".$PrenomA."', ";}
    if($TelA != NULL) {
        $TelA = explode(" ", $TelA);
        $TelA = implode($TelA);
        $keys .= "TelA, "; $values .= "'".$TelA."', ";
    }
    if($MailA != NULL) {$keys .= "MailA, "; $values .= "'".$MailA."', ";}
    
    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);
    
    $query = "INSERT INTO assure(".$keys.") VALUES (".$values.")";

    return mysqli_query($link, $query);
}

//Enregistre un dossier puis renvoie True si la manoeuvre à réussie
//False sinon
function EnregistrerDossier($CodeA, $DateAM, $RefD, $link) { 
    $keys = ""; $values = "";
    $keys .= "RefD, "; $values .= "'".$RefD."', ";
    $keys .= "DateAM, "; $values .= "'".$DateAM."', ";
    $keys .= "CodeA, "; $values .= $CodeA.", ";

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO dossier(".$keys.") VALUES (".$values.")";

    return mysqli_query($link, $query);
}

//Créer le dossier d'un assuré dont le nom est son numéro NIR en local
function CreerDossierNIR($NirA) {
    $dirname = utf8_decode(STORAGE_PATH)."/".$NirA;
    return mkdir($dirname);
}

//Créer le dossier de l'arrêt-maladie d'un assuré en local
function CreerDossierAM($RefD, $NirA) {
    $dirname = utf8_decode(STORAGE_PATH)."/".$NirA."/".$RefD;
    return mkdir($dirname);
}

//Fonction qui enregistre les données d'un fichier dans la base de données
function EnregistrerFichier($CheminJ, $CodeD, $CodeA, $CodeM, $link) { 
    $keys = ""; $values = "";
    if($CheminJ != NULL) {$keys .= "CheminJ, "; $values .= "'".$CheminJ."', ";}
    if($CodeD != NULL) {$keys .= "CodeD, "; $values .= $CodeD.", ";}
    if($CodeA != NULL) {$keys .= "CodeA, "; $values .= $CodeA.", ";}
    if($CodeM != NULL) {$keys .= "CodeM, "; $values .= "'".$CodeM."', ";}

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO justificatif(".$keys.") VALUES (".$values.")";

    return mysqli_query($link, $query);
}

//Enregistre les fichiers contenus dans le dossier d'un assuré
//Revoit une liste avec un ligne pour un fichier
//1er paramètre de type Booléen qui est TRUE si l'enregistrement a réussi, FALSE sinon
//2ème paramètre de type String qui correspond au nom du fichier téléchargé
//3ème paramètre correspond au mnémonique complet affilié au fichier
function EnregistrerFichiers($ListeFichiers, $RefD, $NirA, $link) {
    $resultats = array();
    foreach($ListeFichiers as $Key => $Fichier) {        
        for($i = 0 ; $i < count($Fichier['name']) ; $i++) {
            if ($Fichier['name'][$i] != "") {
                $file = basename($Fichier['name'][$i]);
<<<<<<< Updated upstream
                
                $target_dir = utf8_decode(STORAGE_PATH)."/".$NirA."/".$RefD;
                $path = pathinfo($file);
                $filename = utf8_decode($path['filename']);
                $ext = $path['extension'];
=======

                $target_dir = "../" . STORAGE_PATH . "/" . $NirA . "/" . $RefD;
                $ext = pathinfo($file)['extension'];
>>>>>>> Stashed changes

                $CheminJ = "$target_dir/$Key"."_$i.$ext";
                $CodeA = ChercherAssureAvecNIR($NirA, $link)["CodeA"];
                $CodeD = ChercherDossierAvecREF($RefD, $link)["CodeD"];
<<<<<<< Updated upstream
                $Mnemonique = ChercherMnemoniqueAvecMnemonique($Key, $link);
                $Designation = $Mnemonique["Designation"] . " No. " . $i;
                
                if(EnregistrerFichier($CheminJ, $CodeD, $CodeA, $Mnemonique["CodeM"], $link)) {
                    if(move_uploaded_file(
                        $Fichier['tmp_name'][$i],
                        $CheminJ
                    )) {
                        $resultats[] = array(TRUE, $file, $Designation);
                    }
                    else {
                        $resultats[] = array(FALSE, $file, $Designation);
                    }
                }
                else {
                    $resultats[] = array(FALSE, $file, $Designation);
=======
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
>>>>>>> Stashed changes
                }
            }   
        }
    }
    return $resultats;
}

// FONCTIONS POUR RECAPITULATIF

<<<<<<< Updated upstream
// nombre de dossiers recus
function nbDossiersRecus($link) {
    $query = "Select count(*) AS nbDossiersRecus From dossier d Where d.DateD = CURDATE()";
    
=======


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
    $query = "SELECT count(*) AS nbDossiersClasses FROM dossier d WHERE d.StatutD = 'Classé sans suite' And DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

/*      FONCTIONS POUR TECHNICIEN    */

function DonneesTechnicien($link, $matricule)
{
    $query = "SELECT CodeT, NomT, PrenomT FROM technicien t WHERE t.Matricule = '$matricule'";
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
>>>>>>> Stashed changes
    $result = mysqli_query($link, $query);
    
    return $result;
}
// nombre de dossiers restant à traiter
function nbDossiersATraiter($link) {
    $query = "Select count(*) as nbDossiersAtraiter From dossier d Where d.StatutD = 'À traiter'";
    
    $result = mysqli_query($link, $query);
    
    return $result;
}

// nombre de dossiers classés sans suite
function nbDossiersClasses($link) {
    $query = "Select count(*) as nbDossiersClasses From dossier d Where d.StatutD = 'Classé sans suite' And d.DateD = CURDATE()";
    
    $result = mysqli_query($link, $query);
    
    return $result;
}

<<<<<<< Updated upstream
// Redirection vers une page différente du même dossier
function RedirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    exit;
=======
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
 
    return $result = mysqli_query($link, $query);
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
>>>>>>> Stashed changes
}
?>