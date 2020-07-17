<?php
    session_start();
    require_once("../fonctions.php");    
    //Connexion à la BD
    $link = connecterBD();

    if(isset($_POST["mnemonique"]) and isset($_POST["designationM"])) {    
        $mnemonique = $_POST['mnemonique'];
        $designationM = $_POST['designationM'];
        
        if(isset($_POST['modif'])) {
            $id_mnemonique = $_POST['idM'];
            // Mise à jour de la catégorie
            $query = "UPDATE listemnemonique SET Mnemonique='$mnemonique', Designation='$designationM' WHERE CodeM=$id_mnemonique";
            $result = mysqli_query($link, $query);
            // echo $query;
        }
        else {
            $query = "INSERT INTO listemnemonique "
                . "(Mnemonique, Designation) "
                . "VALUES ('$mnemonique', '$designationM')";
            
            $result = mysqli_query($link, $query);
            // echo $query;
        }

        // Redirection finale
        if($result) // Si l'enregistrement s'est bien déroulé
            if(!isset($_GET["modifier"])) // Si c'est un ajout
                header("Location:accueil_mnemonique.php?action=creer&msg=Success");
            else // Si c'est une modification
                header("Location:accueil_mnemonique.php?action=modifier&msg=Success");
        else
            if(!isset($_GET["modifier"])) // Si c'est un ajout
                header("Location:creation_mnemonique.php?msg=Failure");
            else // Si c'est une modification
                header("Location:modifier_mnemonique.php?id=$id_mnemonique&msg=Failure");
    }
?>


