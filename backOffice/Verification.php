<!DOCTYPE html>

<?php

//Charger les fonctions de connexion dans un autre fichier 
require('Fonctions.php');

//Connexion
$connexion= connexionMysql();
if ($connexion==null)
    //Redirection 
        ;
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

<html>
    <head>
        <meta charset="UTF-8">
        <title>Enregistrement</title>
    </head>
    <body>
        <h1> Résultat de l'enregistrement </h1>
        
        <?php
        
        $verif = VerificationMat($connexion, $matricule);
        
        // Vérification de la connexion 
        if ($connexion != null)
            {
            // D'abord, on vérifie que les 2 mots de passe rentrés par le technicien sont identiques
            if ($mot_de_passe === $_POST['conf'])
                {
                // Ensuite, on vérifie l'unicité de la matricule  
                    if ($verif == "Unique")
                        {
                        //Si les 2 conditions précédentes sont vérifiées, on procède a l'enregistrement

                        // On insère dans la table technicien de la base le nouveau technicien qui vient de s'inscrire 

                        // Requête paramétrée.
			$insertTech ="INSERT INTO technicien (Matricule, NomT, PrenomT, MdpT) VALUES(?,?,?,?)"; 
								
			// Préparation de la requête.
			$requete = mysqli_prepare($connexion, $insertTech);
			mysqli_stmt_bind_param($requete, "ssss",$matricule,$nom,$prenom,$mot_de_passe);
			
			// Exécution de la requête.
			$result = mysqli_stmt_execute($requete);
                        
                        if ($result != null) //L'enregistrement s'est fait avec succès
				{
				//echo("<p><b>Ordre SQL :</b> $insertTech</p>");
				//echo(mysqli_affected_rows($connexion) . " client(s) ajout&eacute;(s)");
                            echo ("Votre enregistrement s'est fait avec succès");
                            echo ("<p>". "<a href='ConnexionTech.php'>". "Connectez vous maintenant!"."</p>"."</a>" );
                                 //$message= 'Votre connef';       
                                 //header('Refresh:5;url=ConnexionTech.php?id=2');
                                 //echo $message;
				}
			else
				{
				echo("Une erreur technique est survenue, veuillez recommencer ultérieurement." . mysqli_error($connexion));
				}
                                
                        }
                        
                    else
                        // Sinon, on est dans le cas où les 2mdps sont identiques mais la matricule est déjà attribuée à un technicien
                        // Dans ce  dernier cas , on le redirige vers la page d'inscription 
                        {
                            $msg_erreur = msg_1;
                            header("Location:InscriptionTech.php?msg_erreur=$msg_erreur");
                        }
                }
            else
                // Les 2 mot de passe sont différents 
                {
                $msg_erreur_mdp = msg_2;
                header("Location:InscriptionTech.php?msg_erreur_mdp=$msg_erreur_mdp");
                }
            }
        ?>
        
    </body>
</html>
