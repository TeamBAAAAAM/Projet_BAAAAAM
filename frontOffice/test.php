<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
    require("fonctions.php");
    $cnx = connexionMySQL();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        if($cnx != NULL)
            echo '<p>Connexion réussie !</p>';
        ?>
        
        <a href="index.html">Retour</a>
    </body>
</html>
