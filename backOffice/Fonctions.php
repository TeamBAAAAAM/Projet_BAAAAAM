<?php

// Informations de connexion 
define ("ID_MYSQL","root");
define ("BD_MYSQL","cpam");
define ("SERVER_MYSQL","localhost");     
        
// Fonction de connexion 
function connexionMysql()
    {
    //Connexion Mysql
    $connexion= mysqli_connect(SERVER_MYSQL, ID_MYSQL);
    if ($connexion==null)
    {
        echo("<p>La connexion au serveur est impossible </p>");
        return null;
    }
    else
        {
        if (mysqli_select_db($connexion,BD_MYSQL)==null)
            {
            echo("<p> Vérifier que la base de données est bien sur MariaDB </p>");
            return null;
            }
        }
    return $connexion;
    }

    
//Vérification de l'unicité de la matricule 
function VerificationMat ($connexion, $matricule)
{
    $requete = "SELECT * FROM technicien WHERE Matricule='$matricule'";
    $curseur = mysqli_query($connexion, $requete);
    
    if ($curseur!=null)
    {
        if (mysqli_num_rows($curseur)==0)
        {
            return "Unique" ;
        }
        else
        {
            $ligne = mysqli_fetch_array($curseur);
            echo "La matricule" . $ligne["Matricule"] . "est déjà attribuée" ;
        }
    }
    return "Erreur de vérification du Matricule" ;      
}
?>

