<?php

//Charger les fonctions de connexion dans un autre fichier 
require('../fonctions.php');

//Connexion à la base de données
$connexion= connexionMySQL();

// On démarre la session
session_start();

//Déconnexion
session_destroy();


?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
        <!-- importer le fichier de style -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="styleTech.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		<script src="script.js"></script>
		
		<script>
			$(document).ready(function(){
			  $("#research").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#data-list tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				});
			  });
			});
		</script>

        <title>PJPE - Connexion</title>
	</head>
	<body>
        <div id="container">
            <!-- zone de connexion -->
            
            <form action="accueil.php" method="POST">
                
                <h1>Connectez-vous!</h1>
                
                <label><b>Matricule (*):</b></label>
                <input type="text" placeholder="Veuillez renseigner votre matricule" 
                       name="matricule" required>
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
                    
                    <a id="inscrire" href='inscription.php'> Pas enregistré ? </a>             
                    <a id="oubli" href=''> Mot de passe oublié ? </a>
                    
                </div>
            </form>
        </div>
    </body>
</html>