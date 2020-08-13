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

    $formPage = "accueil.php"; //La page ciblée lors de l'envoi du formulaire en temps normal

    //Si l'utilisateur a été redirigé vers la page de connexion
    if(isset($_GET["redirect"])) {
        $formPage = $_GET["redirect"];
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
        <!--<link rel="stylesheet" href="style.css">-->
        <link rel="stylesheet" href="style.css">
		
		<!-- SCRIPT JAVASCRIPT (JQUERY / BOOTSTRAP 3.4.1 / SCRIPT LOCAL) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<script src="script.js"></script>

        <title>PJPE - Connexion</title>
	</head>
	<body id="body_sign">
        <?php
            // Si déconnexion réussie
            if (empty($_SESSION) && isset($_GET['logout'])) {
                echo "<div class='container-fluid'>";
                genererMessage(
                    "Déconnexion réussie !",
                    "Vous avez été correctement déconnecté.",
                    "log-out",
                    "success"
                );
                echo "</div>";
            }
        ?>
        <div id="connexion" class="container container_sign">            
            <form action="<?php echo $formPage;?>" method="POST">                
                <h2><span class="glyphicon glyphicon-log-in"></span> Connectez-vous !</h2>
                
                
                <label><strong>Matricule <span class="champ_obligatoire">(*)</span> :</strong></label>
                <input id="mat" type="text" placeholder="Veuillez renseigner votre matricule" 
                    name="matricule" onKeyUp="checkFormatMatricule('# ## ##')" 
                    value="<?php if (isset($_SESSION["matricule"])) echo $_SESSION["matricule"]; ?>" required
                >

                
                <?php
		            if (isset($_GET["msg_erreur"])) {
                        if ($_GET["msg_erreur"]== "msg_3") {
                            genererMessage(
                                "Identification impossible !",
                                "Le matricule et/ou le mot de passe sont incorrects !",
                                "remove",
                                "danger"
                            );
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
                
                <!-- MENTION "CHAMPS OBLIGATOIRES" -->
                <div class="champ_obligatoire">(*) : Champs obligatoires</div>
                
                <!-- LIEN DU BAS -->
                <div class="inscription">                    
                    <a id="inscrire" href='inscription.php'>Pas enregistré ?</a>             
                    <!--a href='#' class="float-right" disabled>Mot de passe oublié ?</a-->
                </div>
            </form>
        </div>
    </body>
</html>