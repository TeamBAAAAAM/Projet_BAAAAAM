<?php

//Variables de connexion 
define("USER", "root");
define("PWD_MYSQL", "");
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
    echo($query);
    $result1 = mysqli_query(connexionMySQL(), $query);
        
    $query = "SELECT DateA FROM Dossier WHERE RefD = '".$RefD."'";
    echo($query);
    $result2 = mysqli_query(connexionMySQL(), $query);
    $ligne = mysqli_fetch_array($result2);
    
    return array(($result1 != NULL), $RefD, $ligne['DateA']);
}

//Enregistre le dossier d'un assuré et son contenu
//dans un espace dédié du serveur
function RecevoirDossier($RefD, $NirA, $fichiers, $dossierCible) {
    foreach ($fichiers as $fichier) {
        if (($_FILES[$fichier]['name'] != "")){
            $target_dir = $DossierCible."/".$NirA."/".$RefD."/";
            $file = $_FILES[$fichier]['name'];
            $path = pathinfo($file);
            $filename = $path['filename'];
            $ext = $path['extension'];
            $temp_name = $_FILES[$fichier]['tmp_name'];
            $path_filename_ext = $target_dir.$filename.".".$ext;

            move_uploaded_file($temp_name, $path_filename_ext);
            echo "Fichier téléchargé avec succès !";
        }   
    }
}
?>
