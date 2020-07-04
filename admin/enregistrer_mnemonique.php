<?php
    session_start();
    require_once("../fonctions.php");    
    //Connexion à la BD
    $link = connecterBD();

if(isset($_POST["mnemonique"]) and isset($_POST["designationM"])){
     
     
    $mnemonique = $_POST['mnemonique'];
    $designationM = $_POST['designationM'];
   
    
     $query = 'INSERT INTO listemnemonique
   (Mnemonique, Designation)
VALUES
   ("'.$mnemonique.'", "'.$designationM.'");';
    
    mysqli_query($link, $query);
    
    
     
    header('Location: creation_mnemonique.php?msg=Success');
    exit();
    
}
?>


