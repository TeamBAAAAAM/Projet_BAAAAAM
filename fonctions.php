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
    }
    if(mysqli_select_db($link, BD_MYSQL) == NULL) {
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

//Vérifie si un assuré est déjà enregitré
function AssureExiste($NirA, $link) {
    $query = "SELECT * FROM Assure WHERE NirA = '".$NirA."'";
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

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

//Vérifie si la référence donnée en paramètre n'est pas déjà utilisé
function DossireExiste($RefD, $link) {    
    $query = "SELECT RefD FROM Dossier WHERE RefD = '".$RefD."'";
    $result = mysqli_query($link, $query);
    
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
                
                $target_dir = utf8_decode(STORAGE_PATH)."/".$NirA."/".$RefD;
                $path = pathinfo($file);
                $filename = utf8_decode($path['filename']);
                $ext = $path['extension'];

                $CheminJ = "$target_dir/$Key"."_$i.$ext";
                $CodeA = ChercherAssureAvecNIR($NirA, $link)["CodeA"];
                $CodeD = ChercherDossierAvecREF($RefD, $link)["CodeD"];
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
                }
            }   
        }
    }
    return $resultats;
}

/*          FONCTIONS POUR RECAPITULATIF        */

// Nombre de dossiers recus à la date courante
function nbDossiersRecus($link) {
    $query = "Select count(*) AS nbDossiersRecus From dossier d Where d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return mysqli_fetch_array($result);
}
// Nombre de dossiers restant à traiter au total
function nbDossiersATraiterTotal($link) {
    $query = "Select count(*) as nbDossiersAtraiterTotal From dossier d Where d.StatutD = 'À traiter'";    
    $result = mysqli_query($link, $query);    
    return mysqli_fetch_array($result);
}

// Nombre de dossiers restant à traiter à la date courante
function nbDossiersATraiter($link) {
    $query = "Select count(*) as nbDossiersAtraiter From dossier d Where d.StatutD = 'À traiter' And d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return mysqli_fetch_array($result);
}

// Nombre de dossiers classés sans suite à la date courante
function nbDossiersClasses($link) {
    $query = "Select count(*) as nbDossiersClasses From dossier d Where d.StatutD = 'Classé sans suite' And d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return mysqli_fetch_array($result);
}

/*      FONCTIONS POUR TECHNICIEN    */

function getTechnicienData($link, $matricule) {
    $query = "Select CodeTech, NomT, PrenomT From technicien t Where t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);    
    return mysqli_fetch_array($result);
}


// Redirection vers une page différente du même dossier
function RedirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    exit;
}

/*      FONCTIONS POUR LE TRAITEMENT  */

// Changement du statut d'un dossier
function ChangerStatutDossier($link, $codeDossier, $statut){
    $query = "UPDATE dossier SET StatutD = '$statut' Where CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);    
    return $result;
}

// Récupération des fichiers d'un dossier
function RecupererPJ($link, $codeDossier){
    $query = "SELECT CheminJ, Mnemonique FROM justificatif j, listemnemonique l" 
        ."WHERE j.CodeM = l.CodeM AND j.CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);    
    return $result;
}

?>