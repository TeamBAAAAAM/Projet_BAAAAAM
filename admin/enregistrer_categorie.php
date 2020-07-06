<?php
    session_start();
    require_once("../fonctions.php");    
    //Connexion à la BD
    $link = connecterBD();
    
    if(isset($_POST["nomC"]) and isset($_POST["designationC"]) and isset($_POST["mnemonique"])){
        //Enregistrement dans la table catégorie
        $nomC = $_POST['nomC'];
        $designationC = $_POST['designationC'];
    
        //if(isset($_SESSION['modif'])){
        //   $query = "UPDATE categorie SET NomC=".$nomC.", DesignationC=".$designationC." WHERE CodeC=".$idC;
        //}else{
        $query = "INSERT INTO categorie(NomC, DesignationC, StatutC)";
        $query .= "VALUES('$nomC', '$designationC', 'Actif')";
        //} 
        echo $query;
        mysqli_query($link, $query);
        //header('Location: creation_categorie.php?msg=Success');
        exit();
    }

    
    //On va faire une requete pour recuperer l'id de la catégorie qui vient d'etre enregistré
   /* $query_id_categorie = "SELECT CodeC FROM categorie ORDER BY CodeC DESC LIMIT 1 ";
    $id_categorie = null;
    
    $result = mysqli_query($link, $query_id_categorie);
    
    if ($result != NULL){
        $rows = mysqli_num_rows($result);
        for ($i = 0; $i < $rows; $i++) {
            $donnees = mysqli_fetch_array($result);
        }
        
        $id_categorie = $donnees['CodeC'];
    }
    *\
    
    //On recupère la liste (stocké dans un tableau array) des id des mnémoniques qui sont cochés plus leurs label
   /* foreach ($_POST["mnemonique"] as $valeur) {
        
        echo $valeur."<br>"." ";
        
    }
    
    foreach ($_POST["label"] as $valeur) {
        
        echo $valeur."<br>";
        
   }
    */
    
    //Alimente la table concerne avec les données correspondantes
    
    
    
    
    
    
    //Redirection finale
    
    




