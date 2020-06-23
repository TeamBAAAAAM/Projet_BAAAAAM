<?php
    /*--------------------------------------------------------------------------------------
        FICHIER INTERMÉDIAIRE SERVANT DE PASSERELLE POUR LA LECTURE D'UN JUSTIFIACATIF
    --------------------------------------------------------------------------------------*/
	session_start();
    
    // Importation des fonctions PHP
    require_once("../fonctions.php");
	
    // Connexion à la BD
	$link = connecterBD();

	// Récupération des données du technicien connecté
	if(!isset($_SESSION["matricule"])){
		demandeDeConnexion();
    }
    else {
        // Connexion au serveur FTP
        $ftp_stream = connecterServeurFTP();

        // Activation du mode passif
        ftp_pasv($ftp_stream, true);

        // Formats autorisés (image) (penser à prendre en compte le format PDF)
        $format_image = ["jpg", "jpeg", "png", "bmp", "tif", "tiff"];
        $cheminFichier = urldecode($_GET["filepath"]);

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
        header("Content-Disposition: inline");
        
        // Affichage du fichier dans le navagateur
        $link = cheminVersServeurFTP().$cheminFichier;
        $file = file_get_contents($link);

        echo $file;
        //echo $link."<br>";
        //echo $type."<br>";
        //echo $size."<br>";
        
        //Fermeture de la connexion au serveur FTP
        ftp_close($ftp_stream);
    }
?>