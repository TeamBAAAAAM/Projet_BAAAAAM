<?php
    /*--------------------------------------------------------------------------------------
        FICHIER INTERMÉDIAIRE SERVANT DE PASSERELLE POUR LE TÉLÉCHARGEMENT D'UN JUSTIFICATIF
    --------------------------------------------------------------------------------------*/
	session_start();
    
    // Importation des fonctions PHP
    require_once("../fonctions.php");
	
    // Connexion à la BD
	$link = connecterBD();

    if(isset($_SESSION["Assure"]) && isset($_SESSION["RefD"])) {
        // Connexion au serveur FTP
        $ftp_stream = connecterServeurFTP();

        // Activation du mode passif
        ftp_pasv($ftp_stream, true);

        // Formats autorisés (image) (penser à prendre en compte le format PDF)
        $format_image = ["jpg", "jpeg", "png", "bmp", "tif", "tiff"];
        $cheminFichier = urldecode($_GET["filepath"]);

        $nomFichier = strrchr($cheminFichier, '/');
        $nomFichier = substr($nomFichier, 1);
        $extension = strrchr($cheminFichier, '.');
        $extension = substr($extension, 1);

        // Paramétrage du type et de la taille du fichier
        if(in_array($extension, $format_image)) {
            if($extension == "jpg") $type = "image/jpeg";
            else if($extension == "tif") $type = "image/tiff";
            else $type = "image/".$extension;
        }
        else if ($extension == "pdf") {
            $type = "application/".$extension;
        }

        $size = ftp_size($ftp_stream, $cheminFichier);

        header("Content-Transfer-Encoding: binary");
        header("Content-Type: $type");
        header("Content-Length: ".$size);
        
        if(isset($_GET["type"])) {
            if($_GET["type"] == "view")
                header("Content-Disposition: inline");
            else if($_GET["type"] == "download")
                header("Content-Disposition: attachment; filename=$nomFichier");

            // Affichage du fichier dans le navagateur
            $link = cheminVersServeurFTP().$cheminFichier;
            $file = file_get_contents($link);
        
            echo $file;
        }
        else {  
            echo ("Ce lien n'est pas valide !");
        }
            
        //Fermeture de la connexion au serveur FTP
        ftp_close($ftp_stream);
    }
    else {
        echo ("Vous n'avez pas le droit d'accéder à ce fichier !");
    }
?>