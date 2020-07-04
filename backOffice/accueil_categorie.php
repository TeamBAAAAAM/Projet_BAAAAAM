
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
						<li class="active"><a href=""><span class="glyphicon glyphicon-home"></span> Home</a></li>
                                                <li><a href="creation_categorie.php"><span class="glyphicon glyphicon-list-alt"></span>Nouvelle catégorie</a></li>
						<li><a href=""><span class="glyphicon glyphicon-inbox"></span> Modifier une catégorie</a></li>
                                                <li><a href=""><span class="glyphicon glyphicon-inbox"></span> Gèrer une catégorie</a></li>
					</ul>
                                </div>
                        </div>
        </nav>
        <div class="container">
            <div>
                <h2>Catégories Actives</h2>
            <table class="table table-striped">
				<thead>
					<tr>
						<th>Catégorie</th>						
						<th>Désignation</th>
						
					</tr>
				</thead>
				<tbody >
				<?php
					
					$result = categorieActif($link);
					
					if ($result != NULL) 
						$rows = mysqli_num_rows($result);
					else $rows = 0;
                                                                           
					for ($i = 0; $i < $rows ; $i++){
						$donnees = mysqli_fetch_array($result);
						echo ("<tr>
									<td>".$donnees['NomC']."</td>
									<td>".$donnees['DesignationC']."</td>
						     </tr>");
					}
				?>
				</tbody>
			</table>
        </div>
        <div>
            <h2>Catégories Inactives</h2>
            <table class="table table-striped">
				<thead>
					<tr>
						<th>Catégorie</th>						
						<th>Désignation</th>
						
					</tr>
				</thead>
				<tbody id="data-list">
				<?php
					
					$result = categorieInactif($link);
					
					if ($result != NULL) 
						$rows = mysqli_num_rows($result);
					else $rows = 0;

					for ($i = 0; $i < $rows ; $i++){
						$donnees = mysqli_fetch_array($result);
						echo ("<tr>
									<td>".$donnees['NomC']."</td>
									<td>".$donnees['DesignationC']."</td>
						     </tr>");
					}
				?>
				</tbody>
			</table>
        </div>
        </div>
        <?php
        // put your code here
        ?>
    </body>
</html>