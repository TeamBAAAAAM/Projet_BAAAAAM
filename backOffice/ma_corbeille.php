<?php 
	session_start();
    require("../fonctions.php");
    // Connexion à la BD
    $link = connexionMySQL();
    if ($link == NULL){
        //Redirection
	}
	
	// test
	$matricule = "12345";
	$codeT = "11111";
	$nomT = "Doe"; 
	$prenomT = "John";
	
	/* // Récupération des données du technicien
	$matricule = $_SESSION["matricule"];	
	$technicien = getTechnicienData($link, $matricule);
	$codeT = $technicien["CodeTech"];
	$nomT = $technicien["NomT"];
	$prenomT = $technicien["PrenomT"];*/

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="style.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		<script src="script.js"></script>
		
		<script>
			$(document).ready(function(){
			  $("#research").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#data-list tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				});
			  });
			});
		</script>

        <title>PJPE - Réception des documents</title>
	</head>
	<body>
		<nav class="navbar navbar-default header">
			<div class="container">
				<div class="navbar-header">
					<h1>PJPE</h1>
				</div>
			</div>
		</nav>

		<nav class="navbar navbar-inverse navbar-static-top police">
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
						<li><a href="accueil.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
						<li><a href="corbeille_generale.php"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
						<li class="active"><a href="ma_corbeille.php"><span class="glyphicon glyphicon-folder-open"></span> Ma Corbeille</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right dropdown">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<?php echo("$prenomT $nomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
							</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								<li role="presentation"><a role="menuitem" href="#">Profil</a></li>
								<li role="presentation" class="divider"></li>
								<li role="presentation"><a role="menuitem" href="#">Se déconnecter</a></li>
							</ul>
						</li>						
					</ul>
				</div>
			</div>
		</nav>
		
		<div class="container">
		<!DOCTYPE html>
<?php
// Connexion base de données
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=bd_cpam;charset=utf8', 'root', 'root');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
?>
<html>
    <head>
        <meta charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<script src="script.js"></script>
        
        <script>
			$(document).ready(function(){
			  $("#research").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#data-list tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				});
			  });
			});
		</script>
        <title>Ma Corbeille </title>
    </head>
    <body>
   <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">BackOffice</a>
    </div>
    <ul class="nav navbar-nav">
        
      <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Menu <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="#">Accueil</a></li>
          <li><a href="#">Corbeille générale</a></li>
          <li><a href="#">Ma corbeille</a></li>
        </ul>
      </li>
      <li ><a href="#">Accueil</a></li>
      <li ><a href="#">Corbeille générale</a></li>
      <li class="active"><a href="#">Ma corbeille</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
                <li> <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle"
                            type="button" id="menu1" data-toggle="dropdown">Nom Prénom</button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                            <li role="presentation"><a role="menuitem" href="#">Profil</a></li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation"><a role="menuitem" href="#">Déconnexion</a></li>
                    </ul>
                 <img src="avatar.png" class="img-circle" alt="Avatar Image" width="25" height="25">  </li>
    </ul>
    </div>       
</nav>
    <div class="container">
			<input class="form-control" id="research" type="text" placeholder="Rechercher ...">
    
    <table class="table table-striped">
     <thead>
    <tr>
    <th>Date récep</th>
    
    <th>N° de demande</th>
    <th>Nir</th>
    <th></th>
    </tr>    
    </thead> 
     <?php
        $reponse = $bdd->query('SELECT d.DATED, d.REFD, a.NIRA  FROM traiter t, dossier d, assure a '
                . 'where t.CODED=d.CODED and d.CODEA=a.CODEA  ');

while ($donnees = $reponse->fetch())
{
	echo ("<tr><td>".$donnees['DATED']."</td>
                  <td>".$donnees['REFD']."</td>
                  <td>".$donnees['NIRA']."</td> 
                  <td><button type='button' class='btn btn-info'><span class='glyphicon glyphicon-plus'></span></button></td> 
                   
                </tr>");
}

$reponse->closeCursor();

     ?>
</table>


	
		</div>
	</body>	
</html>