<?php

//Charger les fonctions de connexion dans un autre fichier 
require('../fonctions.php');

//Connexion
$connexion= connexionMySQL();

// Démerrage de la session 
session_start();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Inscription</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="styleInsc.css">
        
    <body>
        <nav class="navbar navbar-default header welcome">
			<div class="container">
				<div class="navbar-header">
                                    <a href="se_connecter.php"><h1><strong>Bienvenue</strong></h1></a>
				</div>
			</div>
        </nav>
        <div class="Renseignement">
        
        <h2>Enregistrez-vous en quelques clics et commencez vos traitements.</h2>
        <h3>Veuillez renseigner les informations suivantes:</h3>
        
        <form action="confirmation.php" method='POST'>
        
        <div class="form-group">
            <label for="exampleInputEmail1">Matricule(*) :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="mat" type="text" class="form-control" name="matricule" placeholder="# ## #" 
                                value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['mat']);}?>"
                                required>
                        </div>
            <?php
		if (isset($_GET["msg_erreur"]))
                    {
                    if ($_GET["msg_erreur"]== "msg_1")
                    {
                    echo("<p style='color:red' class=\"msg_erreur\">"."<em>Le matricule saisi existe déjà dans la base de données. <br/>"
                        . "Cela signifie que vous êtes déjà inscrit dans la base,"
                        . " auquel cas <a href='index.php'> vous pouvez vous connecter directement, </a> "
                        . "ou alors que ce matricule est déjà attribué à un autre technicien, veuillez alors vérifier votre saisie.</em> " . "</p>");
                    }
                    }
            ?>
        </div>     
        
        <div class="form-group">
            <label for="exampleInputEmail1">Nom(*) :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="nom" type="text" class="form-control" name="nom" placeholder="Votre Nom"
                               value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['nom']);}?>" 
                               required>
                        </div>
        </div> 
        
        <div class="form-group">
            <label for="exampleInputEmail1">Prénom(*) :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="prenom" type="text" class="form-control" name="prenom" placeholder="Votre prénom" 
                              value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['prenom']);}?>" 
                              required>
                        </div>
        </div> 
        
        <div class="form-group">
            <label for="exampleInputEmail1">Mot de passe(*) :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="mdp" type="password" class="form-control" name="mdp" required>
                        </div>
        </div> 
        
        <div class="form-group">
            <label for="exampleInputEmail1">Confirmation mot de passe(*) :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="confmdp" type="password" class="form-control" name="conf" required>
                        </div>
        <?php
		if (isset($_GET["msg_erreur_mdp"]))
                    {
                    if ($_GET["msg_erreur_mdp"]== "msg_2")
                    {
                    echo("<p style='color:red' class=\"msg_erreur\">"."<em>Les 2 mots de passe ne sont pas identiques. "
                            . "<br/> Veuillez vérifier votre saisie." . "</p>");
                    }
                    }
        ?>
        </div> 
        
        <div id="champ_obligatoire" class="container">                    
             <p>(*) : Champs obligatoires</p>
        </div>
        
        <div class="certif">
            <input type="checkbox" name="vrai" required id="honneur" > Je certifie sur l'honneur que ces informations sont exactes.
        </div>
        
        <input type="submit" class="btn btn-primary" value="S'enregistrer">
        
        <a href="index.php" class="btn btn-primary" onclick="confirmation(event)" id="annulation">  Annuler</a>
        
        <script>
            function confirmation(event) {
                event.preventDefault();
                if(confirm("Etes vous sûr de vouloir tout annuler?")) {
                    window.location = event.target.href;
                }S
             }
        </script>
        
        </form>
        </div>
    </body>
</html>