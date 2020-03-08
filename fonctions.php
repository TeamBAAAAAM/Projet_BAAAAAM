<?php

//Variables de connexion 
define("ID_MYSQL", "root");
define("PWD_MYSQL", "");
define("BD_MYSQL", "arrets");
define("SERVER_MYSQL", "");


// Connexion BD
function connexionMySQL() {
    $cres = mysqli_connect(SERVER_MYSQL, ID_MYSQL, PWD_MYSQL, BD_MYSQL);
    if ($cres == NULL) {
        echo("<p>Connexion impossible</p>");
        return NULL;
    } else {
        if (mysqli_select_db($cres, BD_MYSQL) == NULL) {
            echo("<p>Problème de base de données</p>");
            return NULL;
        }
    }
    return $cres;
}
// essaie de changement 
?>
