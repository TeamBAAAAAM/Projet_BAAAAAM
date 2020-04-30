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
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
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
    <body id="body_sign">
        <div id="confirmation_inscription" class="container container_sign">
            <h1><span class='glyphicon glyphicon-floppy-saved'></span>Résultat de l'enregistrement</h1>
            
            <?php
                // Authentification du technitien
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
                                    
                        if ($result != null) {// L'enregistrement s'est effectué avec succès                            
                            genererMessage(
                                "Votre enregistrement s'est effectué avec succès !",
                                "<p>
                                    <a href='se_connecter.php' class='btn btn btn-primary' role='button'>
                                        <span class='glyphicon glyphicon-log-in'></span>
                                        Connectez vous maintenant
                                    </a>
                                 </p>
                                ",
                                "ok",
                                "success"
                            );
                        }
                        else {
                            genererMessage(
                                "Une erreur technique est survenue !",
                                "Veuillez recommencer ultérieurement. ".mysqli_error($connexion),
                                "remove",
                                "danger"
                            );
                        }       
                    }    
                    else {
                        // Sinon, on est dans le cas où les 2 mots de passes sont identiques 
                        // mais le matricule est déjà attribué à un technicien,
                        // dans ce  dernier cas, on le redirige vers la page d'inscription 
                        $msg_erreur = 'msg_1';
                    }

                    if(isset($msg_erreur) && isset($msg_erreur_mdp)) {
                        redirigerVers("inscription.php?msg_erreur_mdp=$msg_erreur_mdp&msg_erreur=$msg_erreur");
                    }
                    else if(isset($msg_erreur_mdp)) {
                        redirigerVers("inscription.php?msg_erreur_mdp=$msg_erreur_mdp");
                    }
                    else if(isset($msg_erreur)) {
                        redirigerVers("inscription.php?msg_erreur=$msg_erreur");
                    }
                }
            ?>
        </div>
    </body>
</html>
