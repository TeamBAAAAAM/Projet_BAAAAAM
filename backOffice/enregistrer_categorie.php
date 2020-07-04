<?php
    session_start();
    require_once("../fonctions.php");    
    //Connexion à la BD
    $link = connecterBD();

if(isset($_POST["nomC"]) and isset($_POST["designationC"]) and isset($_POST["mnemonique"]) and isset($_POST["labelC"])){
     
     
    $nomC = $_POST['nomC'];
    $designationC = $_POST['designationC'];
    //$statutC = $_POST['statutC'];
    
     $query = 'INSERT INTO categorie
   (NomC, DesignationC, StatutC)
VALUES
   ("'.$nomC.'", "'.$designationC.'", "actif");';
    
    mysqli_query($link, $query);
    
    
     
    header('Location: creation_categorie.php?msg=Success');
    exit();
    
}
?>


