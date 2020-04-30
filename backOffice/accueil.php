<?php
	session_start();
	require("../fonctions.php");
	// Format des dates en français
	setlocale(LC_TIME, "fr_FR");

	// Connexion à la BD
	$link = connecterBD();

	// Récupération des données du technicien après connexion
	if (isset($_POST["matricule"]) && isset($_POST["mdp"])) {
		//Vérification des identifiants
		if (!authentifierTechnicien($link, $_POST["matricule"], $_POST["mdp"])) {
			redirigerVers("se_connecter.php?msg_erreur=msg_3");
		}

		//Récupération des données du technicien
		$matricule = $_POST["matricule"];
		$technicien = donneesTechnicien($link, $matricule);
		$codeT = $technicien["CodeT"];
		$nomT = $technicien["NomT"];
		$prenomT = $technicien["PrenomT"];

		//Mise en session	
		$_SESSION["matricule"] = $matricule;
		$_SESSION["codeT"] = $codeT;
		$_SESSION["nomT"] = $nomT;
		$_SESSION["prenomT"] = $prenomT;
	} else {
		if (isset($_SESSION["matricule"])) { // S'il est déjà connecté
			$matricule = $_SESSION["matricule"];
			$codeT = $_SESSION["codeT"];
			$nomT = $_SESSION["nomT"];
			$prenomT = $_SESSION["prenomT"];
		} else { // Sinon, il est redirigé vers la page de connexion
			redirigerVers("se_connecter.php");
		}
	}	
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
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
						<li class="active"><a href="accueil.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
						<li><a href="corbeille_generale.php"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
						<li><a href="ma_corbeille.php"><span class="glyphicon glyphicon-inbox"></span> Ma Corbeille</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right dropdown">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<?php echo ("$prenomT $nomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
							</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								<li role="presentation"><a role="menuitem" href="se_connecter.php?logout"><span class="glyphicon glyphicon-log-out"></span>Se déconnecter</a></li>
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
						<tr class="titre">
							<th><span class="glyphicon glyphicon-calendar"></span> Aujourd'hui <?php echo ("<small>" . strftime("(%a %d-%m-%Y)") . "</small>") ?></th>
							<th>
								<h4></h4>
							</th>
						</tr>
					</thead>
					<tbody id="data-list">
						<tr>
							<td><span class="glyphicon glyphicon-download"></span> Dossiers reçus </td>
							<td>
								<?php echo nbDossiersRecus($link)["nbDossiersRecus"]; ?>
							</td>
						</tr>
						<tr>
							<td><span class="glyphicon glyphicon-pencil"></span> Dossiers à traiter</td>
							<td>
								<?php echo nbDossiersATraiter($link)["nbDossiersAtraiter"]; ?>
							</td>
						</tr>
						<tr>
							<td><span class="glyphicon glyphicon-alert"></span> Dossiers classés sans suite</td>
							<td>
								<?php echo nbDossiersClasses($link)["nbDossiersClasses"]; ?>
							</td>
						</tr>
						<tr>
							<td><span class="glyphicon glyphicon-ok"></span> Dossiers terminés</td>
							<td>
								<?php echo nbDossiersTermines($link)["nbDossiersTermines"]; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-sm-6">
				<table class="table table-striped police">
					<thead>
						<tr class="titre">
							<th><span class="glyphicon glyphicon-edit"></span> Nombre total de dossiers à traiter</th>
						</tr>
					</thead>
					<tbody id="data-list">
						<tr>
							<td class="text-center">
								<?php echo nbDossiersATraiterTotal($link)["nbDossiersAtraiterTotal"]; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>