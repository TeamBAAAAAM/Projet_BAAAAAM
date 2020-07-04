<?php
    require_once("../fonctions.php");
    $link = connecterBD();

    if(isset($_POST["download"]) && $_POST["download"] == "OK") {
        sauvegarderFichierCSVLocal($link);
    }
?>