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
        echo("<p>Connexion impossible</p>");
        return NULL;
    } else {
        if (mysqli_select_db($cres, BD_MYSQL) == NULL) {
            echo("<p>Problème de base de données</p>");
            return NULL;
        }
    }
        
    //echo("<p>Connexion réussie</p>");
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


// FONCTIONS POUR RECAPITULATIF

// Nombre de dossiers recus à la date courante
function nbDossiersRecus($link) {
    $query = "Select count(*) AS nbDossiersRecus From dossier d Where d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return $result;
}
// Nombre de dossiers restant à traiter au total
function nbDossiersATraiterTotal($link) {
    $query = "Select count(*) as nbDossiersAtraiterTotal From dossier d Where d.StatutD = 'À traiter'";    
    $result = mysqli_query($link, $query);    
    return $result;
}

// Nombre de dossiers restant à traiter à la date courante
function nbDossiersATraiter($link) {
    $query = "Select count(*) as nbDossiersAtraiter From dossier d Where d.StatutD = 'À traiter' And d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return $result;
}

// Nombre de dossiers classés sans suite à la date courante
function nbDossiersClasses($link) {
    $query = "Select count(*) as nbDossiersClasses From dossier d Where d.StatutD = 'Classé sans suite' And d.DateD = CURDATE()";    
    $result = mysqli_query($link, $query);    
    return $result;
}

// FONCTIONS POUR TECHNICIEN

function getTechnicienData($link, $matricule) {
    $query = "Select CodeTech, NomT, PrenomT From technicien t Where t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);    
    return $result;
}

?>
