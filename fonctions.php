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
        echo("<p>Connexion impossible</p>");
        return NULL;
    } else {
        if (mysqli_select_db($cres, BD_MYSQL) == NULL) {
            echo("<p>Problème de base de données</p>");
            return NULL;
        }
    }
        
    echo("<p>Connexion réussi</p>");
    return $cres;
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
