<?php
    require_once("fonctions.php");
    $link = connecterBD();
    genererFichierCSV($link);
?>