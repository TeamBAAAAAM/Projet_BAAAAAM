<?php

//Variables de connexion 
define("ID_MYSQL", "21900900");
define("PWD_MYSQL", "01581D");
define("BD_MYSQL", "db_21900900_2");
define("SERVER_MYSQL", "etu-web2");


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
//2e essaie
//Axel
?>