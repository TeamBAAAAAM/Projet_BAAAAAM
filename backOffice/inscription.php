<?php

//Charger les fonctions de connexion dans un autre fichier 
require('../fonctions.php');

//Connexion
$connexion= connecterBD();

// Démerrage de la session 
session_start();

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

        <!-- SCRIPT JAVASCRIPT (JQUERY / BOOTSTRAP 3.4.1 / SCRIPT LOCAL) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="script.js"></script>

        <title>Inscription</title>
    </head>
    <body id="body_sign">
        <nav class="navbar navbar-default header">
            <div class="container">
                <div class="navbar-header">
                    <a href="se_connecter.php"><h1>PJPE - Inscription</h1></a>
                </div>
            </div>
        </nav>

        <div id="inscription" class="container container_sign">        
            <form action="confirmation.php" method='POST'>
                <div class="form-group">
                    <h2>
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                        Enregistrez-vous en quelques clics et commencez vos traitements !
                    </h2>
                    <h4>Veuillez renseigner les informations suivantes :</h4>
                    
                    <label for="mat">Matricule <span class="champ_obligatoire">(*)</span> :</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
                        <input id="mat" type="text" class="form-control" name="matricule" placeholder="# ## ##" 
                            value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['matricule']);}?>"
                            onKeyUp="checkFormatMatricule('# ## ##')" 
                            required>
                    </div>

                    <?php
                    if (isset($_GET["msg_erreur"])) {
                        //Si le matricule est déjà enregistré
                        if ($_GET["msg_erreur"] == "msg_1") {
                            genererMessage(
                                "Le matricule saisi existe déjà dans la base de données !",
                                "Cela signifie que vous êtes déjà inscrit, auquel cas vous pouvez vous 
                                <a href='se_connecter.php' class='btn btn-primary' role='button'>
                                    <span class='glyphicon glyphicon-log-in'></span>
                                    Connecter directement
                                </a>, 
                                ou alors que ce matricule est déjà attribué à un autre technicien, 
                                veuillez alors vérifier votre saisie.",
                                "remove",
                                "danger"
                            );
                        }
                    }
                    ?>
                </div>     
                
                <div class="form-group">
                    <label for="nom">Nom <span class="champ_obligatoire">(*)</span> :</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="nom" type="text" class="form-control" name="nom" placeholder="Votre Nom"
                            value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['nom']);}?>" 
                            required
                        >
                    </div>
                </div> 
                
                <div class="form-group">
                    <label for="prenom">Prénom <span class="champ_obligatoire">(*)</span> :</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="prenom" type="text" class="form-control" name="prenom" placeholder="Votre prénom" 
                            value ="<?php if ((isset($_GET['msg_erreur'])) or (isset($_GET['msg_erreur_mdp']))) {echo($_SESSION['prenom']);}?>" 
                            required
                        >
                    </div>
                </div> 
                
                <div class="form-group">
                    <label for="mdp">Mot de passe <span class="champ_obligatoire">(*)</span> :</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="mdp" type="password" class="form-control" name="mdp" required>
                    </div>
                </div> 
                
                <div class="form-group">
                    <label for="confmdp">Confirmation mot de passe <span class="champ_obligatoire">(*)</span> :</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="confmdp" type="password" class="form-control" name="conf" required>
                    </div>
                <?php
                    if (isset($_GET["msg_erreur_mdp"])) {
                        // S'il les deux mot de passe ne correspondent pas
                        if ($_GET["msg_erreur_mdp"] == "msg_2") {
                            genererMessage(
                                "Les 2 mots de passe ne sont pas identiques !",
                                "Veuillez vérifier votre saisie.",
                                "remove",
                                "danger"
                            );
                        }
                    }
                ?>
                </div> 
                    
                <div class="certif container">
                    <input type="checkbox" name="vrai" required id="honneur"> Je certifie sur l'honneur que ces informations sont exactes.
                    <span class="champ_obligatoire">(*)</span>
                </div>
            
                <div class="champ_obligatoire">(*) : Champs obligatoires</div>

                <button type="submit" class="btn btn-lg btn-primary">
                    <span class="glyphicon glyphicon-floppy-open"></span> S'enregistrer
                </button>
                
                <a href="se_connecter.php" class="btn btn-lg btn-danger float-right" onclick="confirmationAnnulation(event)">
                    <span class='glyphicon glyphicon-remove'></span> Annuler
                </a>
            </form>
        </div>
    </body>
</html>