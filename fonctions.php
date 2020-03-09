<?php

//Variables de connexion 
define("USER", "root");
define("PWD_MYSQL", "root");
define("BD_MYSQL", "arrets");
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
    return $cres;
}
// essaie de changement 
//2e essaie
//Axel
?>
