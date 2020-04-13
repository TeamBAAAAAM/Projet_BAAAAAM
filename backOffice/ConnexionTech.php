<?php

//Charger les fonctions de connexion dans un autre fichier 
require('Fonctions.php');

//Connexion
$connexion= connexionMysql();
if ($connexion==null)
    //Redirection 
        ;

// On démarre la session
session_start();

?>

<html>
    <head>
       <title>Connexion</title>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->

        <link rel="stylesheet" href="styleTech.css">
    </head>
    <body>
        <div id="container">
            <!-- zone de connexion -->
            
            <form action="AccueilTech.php" method="POST">
                
                <h1>Connectez-vous!</h1>
                
                <label><b>Matricule (*):</b></label>
                <input type="text" placeholder="Veuillez renseigner votre matricule" 
                       name="mat" required>
                <?php
		if (isset($_GET["msg_erreur"]))
                    {
                    if ($_GET["msg_erreur"]== "msg_3")
                    {
                    echo("<p style='color:red' class=\"msg_erreur\">"."<em>La matricule ou le mot de passe "
                        . "ne sont pas corrects</em> " . "</p>");
                    }
                    }
                ?>
                <br/>
                    
                <label><b>Mot de passe (*): </b></label>
                <input type="password" name="mdp" placeholder="Tapez votre mot de passe ici" required>
                
                <input type="submit" value='Accèder aux dossiers'>
                <br/>
                
                <div class="inscription">
                    
                    <a id="inscrire" href='InscriptionTech.php'> Pas enregistré ? </a>             
                    <a id="oubli" href=''> Mot de passe oublié ? </a>
                    
                </div>
            </form>
        </div>
    </body>
</html>