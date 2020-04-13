<?php 
	session_start();
    require("../fonctions.php");
    // Connexion à la BD
    $link = connexionMySQL();
    if ($link == NULL){
        //Redirection
	}
	
	// Récupération des données du technicien
	// $matricule = $_POST["matricule"];	
	// $result = getTechnicienData($link, $matricule);
	// $ligne = mysqli_fetch_array($result);
	// $codeT = $ligne["CodeTech"];
	// $nomT = $ligne["NomT"];
	// $prenomT = $ligne["PrenomT"];

	// test
	$matricule = "12345";
	$codeT = "11111";
	$nomT = "Doe"; 
	$prenomT = "John";

	//Mise en session	
	$_SESSION["matricule"] = $matricule;	
	$_SESSION["codeT"] = $codeT;
	$_SESSION["nomT"] = $nomT;
	$_SESSION["prenomT"] = $prenomT;

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
					<!-- <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>                        
					</button> -->
					<h1>PJPE</h1>
				</div><!-- 
				<div class="" id="myNavbar">						
					<div class="nav navbar-nav navbar-right dropdown">
						<button class="btn btn-default dropdown-toggle"
							type="button" id="menu1" data-toggle="dropdown">
							<h4><--?php echo("$nomT $prenomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span></h4>
						</button>								
						<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
							<li role="presentation"><a role="menuitem" href="#">Profil</a></li>
							<li role="presentation" class="divider"></li>
							<li role="presentation"><a role="menuitem" href="#">Déconnexion</a></li>
						</ul>
					</div>
				</div> -->
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
						<li class="active"><a href="accueil.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
						<li><a href="corbeille_generale.php"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
						<li><a href="ma_corbeille.php"><span class="glyphicon glyphicon-folder-open"></span> Ma Corbeille</a></li>
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
			<div class="row container-accueil">
				<div class="col-sm-6 ">          
					<table class="table table-striped police">
						<thead class="titre"> 
							<tr class="titre"><th><span class="glyphicon glyphicon-calendar"></span> Aujourd'hui  <?php setlocale(LC_TIME, "fr_FR"); echo ("<small>".strftime("(%a. %d-%m-%Y)")."</small>")?></th>
							<th><h4></h4></th></tr>
						</thead>
						<tbody id="data-list">
							<tr>
								<td><span class="glyphicon glyphicon-download"></span> Dossiers reçus </td>
								<td>
									<?php 
										$result = nbDossiersRecus($link);
										echo $result["nbDossiersRecus"];
									?>
								</td>
							</tr>
							<tr>
								<td><span class="glyphicon glyphicon-pencil"></span> Dossiers à traiter</td>
								<td>
									<?php 
										$result = nbDossiersATraiter($link);
										echo $result["nbDossiersAtraiter"];
									?>
								</td>
							</tr>
							<tr>
								<td><span class="glyphicon glyphicon-alert"></span> Dossiers classés sans suite</td>
								<td>
									<?php 
										$result = nbDossiersClasses($link);
										echo $result["nbDossiersClasses"];
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-sm-6">         
					<table class="table table-striped police">
						<thead>
							<tr class="titre"><th><span class="glyphicon glyphicon-edit"></span> Nombre total de dossiers à traiter</th></tr>
						</thead>
						<tbody id="data-list">
							<tr>							
								<td class="text-center">
									<?php 
										$result = nbDossiersATraiterTotal($link); 
										echo $result["nbDossiersAtraiterTotal"];
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>	
</html>