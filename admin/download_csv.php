<?php
    require_once("../fonctions.php");
    $link = connecterBD();

    if(isset($_POST["injection_file_download"]) && $_POST["injection_file_download"] == "OK") {
        telechargererFichierInjectionCSVLocal($link);
    }
    else if(isset($_POST["list_folders_download"]) && $_POST["list_folders_download"] == "OK") {
        telechargerListeDossiersCSVLocal($link);
    }
?>