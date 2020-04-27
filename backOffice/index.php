<?php
    //Charger les fonctions de connexion dans un autre fichier 
    require('../fonctions.php');

    //Connexion à la base de données
    $connexion= connecterBD();

    // On démarre la session
    session_start();

    //Déconnexion
    $_SESSION = array();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
	<head>		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
        <!-- importer le fichier de style -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
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
        <div id="container" class="container">            
            <form action="accueil.php" method="POST">                
                <h2><span class="glyphicon glyphicon-log-in"></span> Connectez-vous !</h2>
                
                <label>
                    <strong>
                        Matricule <span class="champ_obligatoire">(*)</span> :
                    </strong>
                </label>
                <input type="text" placeholder="Veuillez renseigner votre matricule" 
                    name="matricule" required>

                <?php
		            if (isset($_GET["msg_erreur"])) {
                        if ($_GET["msg_erreur"]== "msg_3") {
                            echo("
                                <div class='alert alert-danger'>
                                    <h5>
                                        <span class='glyphicon glyphicon-remove'></span>
                                        <strong>Identification impossible !</strong>
                                    </h5>
                                    <p>
                                        La matricule et/ou le mot de passe sont incorrects !
                                    </p>
                                </div>
                            ");
                        }
                    }
                ?>
                    
                <label>
                    <strong>
                        Mot de passe <span class="champ_obligatoire">(*)</span> :
                    </strong>
                </label>
                <input type="password" name="mdp" placeholder="Tapez votre mot de passe ici" required>

                <input type="submit" value='Accèder aux dossiers'>
                
                <div class="champ_obligatoire container">                    
                    <p>(*) : Champs obligatoires</p>
                </div>

                <div class="inscription">                    
                    <a id="inscrire" href='inscription.php'> Pas enregistré ? </a>             
                    <a id="oubli" href=''> Mot de passe oublié ? </a>
                </div>

            </form>
        </div>
    </body>
</html>