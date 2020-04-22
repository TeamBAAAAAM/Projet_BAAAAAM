<?php

//Charger les fonctions de connexion dans un autre fichier 
require('../fonctions.php');

//Connexion
$connexion= connexionMysql();

// Récupération du matricule 
$matricule= $_POST['matricule'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$mot_de_passe = $_POST['mdp'];


// On met en session les variables déjà saisie par le technicien 
session_start();

$_SESSION['mat'] = $matricule;
$_SESSION['nom'] = $nom;
$_SESSION['prenom'] = $prenom;
$_SESSION['mdp']=$mot_de_passe;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="style.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <title>Confirmation de l'enregistrement</title>
    </head>
    <body>
        <div id="confirmation_inscription" class="container-fluid">
            <h1><span class='glyphicon glyphicon-floppy-saved'></span>Résultat de l'enregistrement</h1>
            
            <?php
                $verif = VerificationMat($connexion, $matricule);
                
                // Vérification de la connexion 
                if ($connexion != null) {
                    // D'abord, on vérifie que les 2 mots de passe rentrés par le technicien sont identiques
                    if (!($mot_de_passe === $_POST['conf'])) {
                        // Les 2 mot de passe sont différents 
                        $msg_erreur_mdp = msg_2;
                    }

                    // Ensuite, on vérifie l'unicité de la matricule  
                    if ($verif == "Unique") {
                        //Si les 2 conditions précédentes sont vérifiées, on procède a l'enregistrement
                        // On insère dans la table technicien de la base le nouveau technicien qui vient de s'inscrire 
                        // Requête paramétrée.
                        $insertTech ="INSERT INTO technicien (Matricule, NomT, PrenomT, MdpT) VALUES(?,?,?,?)"; 
                                            
                        // Préparation de la requête.
                        $requete = mysqli_prepare($connexion, $insertTech);
                        mysqli_stmt_bind_param($requete, "ssss",$matricule,$nom,$prenom,$mot_de_passe);
                        
                        // Exécution de la requête.
                        $result = mysqli_stmt_execute($requete);
                                    
                        if ($result != null) {//L'enregistrement s'est fait avec succès 
                            //echo("<p><b>Ordre SQL :</b> $insertTech</p>");
                            //echo(mysqli_affected_rows($connexion) . " client(s) ajout&eacute;(s)");
                            echo("
                                <div class='alert alert-success'>
                                    <h5>
                                        <span class='glyphicon glyphicon-ok'></span>
                                        <strong>Votre enregistrement s'est effectué avec succès !</strong>
                                    </h5>
                                    <p>
                                        <a href='se_connecter.php' class='btn btn-primary' role='button'>
                                            <span class='glyphicon glyphicon-log-in'></span>
                                            Connectez vous maintenant
                                        </a>
                                    </p>
                                </div>
                            ");
                            //$message= 'Votre connef';       
                            //header('Refresh:5;url=se_connecter.php?id=2');
                            //echo $message;
                        }
                        else {
                            echo("
                                <div class='alert alert-danger'>
                                    <h5><strong>Une erreur technique est survenue !</strong></h5>
                                    <p>
                                        Veuillez recommencer ultérieurement. ".mysqli_error($connexion)."
                                    </p>
                                </div>
                            ");
                        }       
                    }    
                    else {
                        // Sinon, on est dans le cas où les 2mdps sont identiques mais la matricule est déjà attribuée à un technicien
                        // Dans ce  dernier cas , on le redirige vers la page d'inscription 
                        $msg_erreur = msg_1;
                    }

                    if(isset($msg_erreur) && isset($msg_erreur_mdp)) {
                        header("Location:inscription.php?msg_erreur_mdp=$msg_erreur_mdp&msg_erreur=$msg_erreur");
                    }
                    else if(isset($msg_erreur_mdp)) {
                        header("Location:inscription.php?msg_erreur_mdp=$msg_erreur_mdp");
                    }
                    else if(isset($msg_erreur)) {
                        header("Location:inscription.php?msg_erreur=$msg_erreur");
                    }
                }
            ?>
        </div>
    </body>
</html>
