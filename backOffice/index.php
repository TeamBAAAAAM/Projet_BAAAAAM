<?php
    session_start();
    require('../fonctions.php');

    //Connexion à la base de données
    $connexion= connecterBD();

    //Suppression des données en session après déconnexion
    if (isset($_GET["logout"])){
        session_destroy();
        $_SESSION = array();
    }
?>

<!DOCTYPE html>
<html lang="fr">
	<head>		
		<!-- ENCODAGE DE LA PAGE EN UTF-8 ET GESTION DE L'AFFICHAGE SUR MOBILE -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- FEUILLE DE STYLE CSS (BOOTSTRAP 3.4.1 / CSS LOCAL) -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <!--<link rel="stylesheet" href="styleTech.css">-->
		
		<!-- SCRIPT JAVASCRIPT (JQUERY / BOOTSTRAP 3.4.1 / SCRIPT LOCAL) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<script src="script.js"></script>

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
                <input id="mat" type="text" placeholder="Veuillez renseigner votre matricule" 
                    name="matricule" onKeyUp="checkFormatMatricule('# ## ##')" value="<?php if (isset($_SESSION["matricule"])) echo $_SESSION["matricule"]; ?>" required>

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