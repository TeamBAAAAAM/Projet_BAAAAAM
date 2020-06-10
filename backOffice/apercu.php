<?php
    /*--------------------------------------------------------------------------------------
        FICHIER INTERMÉDIAIRE SERVANT DE PASSERELLE POUR LA LECTURE D'UN JUSTIFIACATIF
    --------------------------------------------------------------------------------------*/
    
    // Importation des fonctions PHP
    require_once("../fonctions.php");

	// Connexion au serveur FTP
    $ftp_stream = connecterServeurFTP();

    // Activation du mode passif
    ftp_pasv($ftp_stream, true);

    // Formats autorisés
    $format_image = ["jpg", "jpeg", "png", "bmp", "tif", "tiff"];
    $cheminFichier = urldecode($_GET["filepath"]);

    $extension = strrchr($cheminFichier, '.');
    $extension = substr($extension, 1);

    // Paramétrage du type et de la taille du fichier
    if(in_array($extension, $format_image)) {
        if($extension == "jpg") $type = "image/jpeg";
        else if($extension == "tif") $type = "image/tiff";
        else $type = "image/" + $extension;
    }
    else if ($extension == "pdf") {
        $type = "application/" + $extension;
    }

    $size = ftp_size($ftp_stream, $cheminFichier);

    header("Content-Transfer-Encoding: binary");
    header("Content-Type: $type");
    header("Content-Length: ".$size);
    header("Content-Disposition: inline");
    
    // Affichage du fichier dans le navagateur
    $link = "ftp://".FTP_USER.":".FTP_PWD."@".FTP_HOST.":21/".$cheminFichier;
    $file = file_get_contents($link);

    echo $file;
?>

