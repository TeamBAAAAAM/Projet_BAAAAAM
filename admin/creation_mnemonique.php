<?php
require("../fonctions.php");
// Format des dates en français
setlocale(LC_TIME, "fr_FR");

// Connexion à la BD
$link = connecterBD();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
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

        <title></title>
    </head>
     <?php
            if(isset($_GET['msg']) and $_GET['msg'] == "Success"){
                echo "<div class='alert alert-success' role='alert'>
  A simple success alert 
</div>";  
            }
        
        ?>
    <body>
        <nav class="navbar navbar-default header">
            <div class="container">
                <div class="navbar-header">
                    <h1>PJPE</h1>
                </div>
            </div>
        </nav>
        
       

        <nav class="navbar navbar-inverse navbar-static-top navbar-menu-police" data-spy="affix" data-offset-top="90">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar2">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar2">
                    <ul class="nav navbar-nav" id="menu">
                         <li ><a href="accueil_categorie.php"><span class="glyphicon glyphicon-home"></span> Gestion Catégorie </a></li>
                         <li class="active"><a href="accueil_mnemonique.php"><span class="glyphicon glyphicon-list-alt"></span>Gestion Mnémonique</a></li>
			
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="container">
            <div><a href='accueil_mnemonique.php'><i class='glyphicon glyphicon-arrow-left'></i></a></div>
            <form method="Post" action="enregistrer_mnemonique.php">
                <h1>Nouvelle Mnémonique</h1>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Mnémonique</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="mnemonique" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Désignation</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="designationM" required>
                    </div>
                </div>
                
   
                <div class="col-sm-4">   <button type="submit" class="btn btn-primary btn-lg"> <span class="glyphicon glyphicon-lock"></span>Valider</button>
                <button type="Reset" class="btn btn-default btn-lg"> <span class="glyphicon glyphicon-lock"></span>Annuler</button>
                
                </div>          
          </form>
    </div>
    
<?php
// put your code here
?>
</body>
</html>
