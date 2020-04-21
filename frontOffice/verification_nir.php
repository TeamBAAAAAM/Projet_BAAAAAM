<?php
 session_start();
require_once("../fonctions.php");
    
$link = connexionMySQL();

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

if(isset($_POST["nir"]) && NirRefExiste($_POST["nir"], $_POST["RefD"], $link)){
    
    
    $_SESSION["recuperation"] = ChercherAssureAvecNIR($_POST["nir"], $link);
     mysqli_close($link);
    header('Location: depot.php');  
}else{
    header('Location: ../index.html'); 
    mysqli_close($link);
}

