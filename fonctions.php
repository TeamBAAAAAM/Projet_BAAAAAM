<?php

//Variables de connexion 
define("USER", "root");
define("PWD_MYSQL", "root");
define("BD_MYSQL", "bd_cpam");
define("HOST", "localhost");
define("PORT", "3306");

// Connexion BD
function connexionMySQL() {
    //$cres = mysqli_connect(SERVER_MYSQL, ID_MYSQL, PWD_MYSQL, BD_MYSQL);
    $cres = mysqli_connect(HOST, USER, PWD_MYSQL, BD_MYSQL, PORT);
    if ($cres == NULL) {
        //echo("<p>Connexion impossible</p>");
        return NULL;
    }

    if (mysqli_select_db($cres, BD_MYSQL) == NULL) {
        //echo("<p>Problème de base de données</p>");
        return NULL;
    }
    //echo("<p>Connexion réussi</p>");

    return $cres;
}

function CharactereAleatoire() {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $listeChar[rand(0, strlen($listeChar)-1)];
}

//Genère une référence
function GenererReference($nbChar) {
    $ref = "";
    for($i = 0 ; $i < $nbChar ; $i++) {
        $ref .= CharactereAleatoire();
    }
    return $ref;
}

//Vérifie si un assuré est déjà enregitré
function AssureExiste($NirA) {
    $link = connexionMySQL();    
    $query = "SELECT * FROM Assure WHERE NirA = '".$NirA."'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}

//Renvoie les informations d'un assuré via son NIR sous la forme d'une liste
function ChercherAssureAvecNIR($NirA) {
    $link = connexionMySQL();    
    $query = "SELECT * FROM Assure WHERE NirA = '".$NirA."'";
    $result = mysqli_query($link, $query);
    $ligne = mysqli_fetch_array($result);
    
    return $ligne;
}

//Vérifie si la référence donnée en paramètre n'est pas déjà utilisé
function DossireExiste($RefD) {
    $link = connexionMySQL();    
    $query = "SELECT RefD FROM Dossier WHERE RefD <> '".$RefD."'";
    $result = mysqli_query($link, $query);
    return (mysqli_fetch_array($result) != NULL);
}

//Ajoute un assuré à la BD (Retourne False si l'assuré est déjà enregistré
//True sinon
function AjouterAssure($NirA, $NomA, $PrenomA, $TelA, $MailA) {
    if(AssureExiste($NirA)) {
        return False;
    }
    else {
        $link = connexionMySQL();
        
        $keys = ""; $values = "";
        if($NirA != NULL){$keys .= "NirA, "; $values .= "'".$NirA."', ";}
        if($NomA != NULL){$keys .= "NomA, "; $values .= "'".$NomA."', ";}
        if($PrenomA != NULL){$keys .= "PrenomA, "; $values .= "'".$PrenomA."', ";}
        if($TelA != NULL){$keys .= "TelA, "; $values .= "'".$TelA."', ";}
        if($MailA != NULL){$keys .= "MailA, "; $values .= "'".$MailA."', ";}
        
        //Suppression du dernier caractère pour les clés
        $keys = substr($keys, 0, strlen($keys) - 2);
        //Suppression du dernier caractère pour les valeurs
        $values = substr($values, 0, strlen($values) - 2);
        
        $query = "INSERT INTO assure(".$keys.") VALUES (".$values.")";
        
        $result = mysqli_query($link, $query);
        return ($result != NULL);
    }
}

function EnregistrerDossier($DateA, $CodeA) {
    $RefD = GenererReference(8);
    
    //Recherche d'une référence unique
    while(DossireExiste($RefD)){        
        $RefD = GenererReference(8);
    }
        
    $keys = ""; $values = "";
    $keys .= "StatutD, "; $values .= "'À traiter', ";
    $keys .= "RefD, "; $values .= "'".$RefD."', ";
    $keys .= "DateA, "; $values .= "'".$DateA."', ";
    $keys .= "CodeA, "; $values .= "'".$CodeA."', ";

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


// FONCTIONS POUR RECAPITULATIF

// nombre de dossiers recus
function nbDossiersRecus($link) {
    $query = "Select count(*) AS nbDossiersRecus From dossier d Where d.DateD = CURDATE()";
    
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

?>
