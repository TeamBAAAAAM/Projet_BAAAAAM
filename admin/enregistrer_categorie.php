<?php
    session_start();
    require_once("../fonctions.php");    
    
    //Connexion à la BD
    $link = connecterBD();
    
    if(isset($_POST["nomC"]) and isset($_POST["designationC"])){
        //Enregistrement dans la table catégorie
        $nomC = $_POST['nomC'];
        $designationC = $_POST['designationC'];
    
        if(isset($_POST['modif'])) {
            $id_categorie = $_POST['idC'];
            // Mise à jour de la catégorie
            $query = "UPDATE categorie SET NomC='$nomC', DesignationC='$designationC' WHERE CodeC=$id_categorie";
            $result = mysqli_query($link, $query);
            // echo $query;

            // Suppression des anciennes mnémoniques avant ajout des nouvelles
            $query = "DELETE FROM concerner WHERE CodeC=$id_categorie";
            $result = mysqli_query($link, $query);
            // echo $query;
        } else {
            $query = "INSERT INTO categorie(NomC, DesignationC, StatutC)";
            $query .= "VALUES('$nomC', '$designationC', 'Actif')";
            $result = mysqli_query($link, $query);
            // echo $query;

            // Requete pour récuperer l'id de la catégorie qui vient d'etre enregistrée
            $query = "SELECT CodeC FROM categorie ORDER BY CodeC DESC LIMIT 1 ";
            $id_categorie = null;
            
            $result = mysqli_query($link, $query);            
            // echo $query;
            
            if ($result != NULL) {
                $rows = mysqli_num_rows($result);
                for ($i = 0; $i < $rows; $i++) {
                    $donnees = mysqli_fetch_array($result);
                }
                
                $id_categorie = $donnees['CodeC'];
            }
        }
        
        //S'il y a des mnémoniques à ajouter
        if(isset($_POST["mnemonique"]) && isset($_POST["label"])) {
            // Liste (stocké dans un tableau array) des id des mnémoniques qui sont cochés plus leurs label
            $valeur_mnemonique = $_POST["mnemonique"];
                        
            foreach($_POST["label"] as $value){    
                if($value != ''){
                    $valeur_label[] = $value;
                }
            }

            // var_dump($valeur_mnemonique);
            // var_dump($valeur_label);

            $i = 0; $j = 0;
            while ($i < sizeof($valeur_mnemonique) and $j < sizeof($valeur_label)) {
                $query = "INSERT INTO concerner "
                    . "(CodeC, CodeM, Label) "
                    . "VALUES ($id_categorie, "
                    .$valeur_mnemonique[$i].", \""
                    .htmlentities($valeur_label[$j])."\")";
                $result = $result && mysqli_query($link, $query);

                $i++; $j++;
            }

            echo $query;
        }
        
        // Redirection finale
        if($result) // Si l'enregistrement s'est bien déroulé
            if(!isset($_GET["modifier"])) // Si c'est un ajout
                header("Location:accueil_categorie.php?action=creer&msg=Success");
            else // Si c'est une modification
                header("Location:accueil_categorie.php?action=modifier&msg=Success");
        else
            if(!isset($_GET["modifier"])) // Si c'est un ajout
                header("Location:creation_categorie.php?msg=Failure");
            else // Si c'est une modification
                header("Location:modifier_categorie.php?id=$id_categorie&msg=Failure");
    }
?>