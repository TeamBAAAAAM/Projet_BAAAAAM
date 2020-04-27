<?php
    session_start();
    require('../fonctions.php');

    //Connexion à la BD
    $connexion= connecterBD();

    // Récupération des données du formulaire d'inscription 
    $matricule= $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mot_de_passe = $_POST['mdp'];

    // Mise en session pour remplir automatiquement à la connexion
    $_SESSION['matricule'] = $matricule;

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

        <title>Confirmation de l'inscription</title>
    </head>
    <body>
        <div id="confirmation_inscription" class="container-fluid">
            <h1><span class='glyphicon glyphicon-floppy-saved'></span>Résultat de l'enregistrement</h1>
            
            <?php
                $verif = verifierMatricule($connexion, $matricule);
                
                // Vérification de la connexion 
                if ($connexion != null) {
                    // D'abord, on vérifie que les 2 mots de passe rentrés par le technicien sont identiques
                    if (!($mot_de_passe === $_POST['conf'])) {
                        // Les 2 mots de passe sont différents 
                        $msg_erreur_mdp = 'msg_2';
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
                        // Sinon, on est dans le cas où les 2mdps sont identiques mais le matricule est déjà attribué à un technicien
                        // Dans ce  dernier cas, on le redirige vers la page d'inscription 
                        $msg_erreur = 'msg_1';
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
