<?php 
	session_start();
	require("../fonctions.php");
	
    // Connexion à la BD
    $link = connecterBD();
	
	// Récupération des données du technicien pour affichage s'il est connecté
	if(isset($_SESSION["matricule"])){
		$matricule = $_SESSION["matricule"];
		$codeT = $_SESSION["codeT"];
		$nomT = $_SESSION["nomT"];
		$prenomT = $_SESSION["prenomT"];
	} else { // Redirection sinon	
		demandeDeConnexion();
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

        <title>PJPE - Corbeille Générale</title>
	</head>
	<!-- TRI DU TABLEAU AU MOMENT DE L'OUVERTURE DE LA PAGE ET SELON LES FILTRES PAR DÉFAUT -->
	<body onload="TrierTableau();">
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
						<li><a href="accueil.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
						<li class="active"><a href="corbeille_generale.php"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
						<li><a href="ma_corbeille.php"><span class="glyphicon glyphicon-inbox"></span> Ma Corbeille</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right dropdown">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<?php echo("$prenomT $nomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
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
			<div id="container_filters" class="container-fluid">	
				<div class="row">
					<div class="col-lg-6">
						<label for="date_debut"><i class="glyphicon glyphicon-search"></i>Recherche un élément</label>
						<input id="recherche" type="text" class="form-control" name="msg" placeholder="Date de réception, Référence du dossier, NIR, Statut ...">
					</div>
					<div class="col-lg-3">
						<label for="date_debut"><i class="glyphicon glyphicon-calendar"></i>Date de réception (Début)</label>
						<input id="date_debut" type="date" class="form-control">
					</div>
					<div class="col-lg-3">
						<label for="date_fin"><i class="glyphicon glyphicon-calendar"></i>Date de réception (Fin)</label>
						<input id="date_fin" type="date" class="form-control">
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2">
						<label for="statut"><i class="glyphicon glyphicon-calendar"></i>Statut</label>
						<select  class="form-control" id="statut">
							<option>Tous</option>
							<option selected>À traiter</option>
							<option>En cours</option>
							<option>Classé sans suite</option>
							<option>Terminé</option>
						</select>
					</div>
					<div class="col-lg-3">
						<label for="mois_nir"><i class="glyphicon glyphicon-folder-open"></i>Mois de naissance</label>
						<select class="form-control" id="mois_nir">
							<option value="" selected>---</option>
							<option value="01">Janvier</option>
							<option value="02">Février</option>
							<option value="03">Mars</option>
							<option value="04">Avril</option>
							<option value="05">Mai</option>
							<option value="06">Juin</option>
							<option value="07">Juillet</option>
							<option value="08">Août</option>
							<option value="09">Septembre</option>
							<option value="10">Octobre</option>
							<option value="11">Novembre</option>
							<option value="12">Décembre</option>
						</select>
					</div>
					<div class="col-lg-2">
						<label for="nb_page"><i class="glyphicon glyphicon-list-alt"></i>Nombre de lignes</label>
						<select class="form-control" id="nb_page">
						<?php
							// Insertion des options de la liste déroulante
							// permettant de fixer le nombre de lignes par pages
							for($i = 1 ; $i <= 19 ; $i += 1) { // De 1 à 19 (en pas normal)
								if($i == 10) echo "<option value='$i' selected>$i</option>";
								else echo "<option value='$i'>$i</option>";
							}
							for($i = 20 ; $i <= 99 ; $i += 10) { // De 20 à 99 (en pas de 10)
								echo "<option value='$i'>$i</option>";
							}
							for($i = 100 ; $i <= 500 ; $i += 100) { // De 100 à 500 (en pas de 100)
								echo "<option value='$i'>$i</option>";
							}
						?>
						</select>
					</div>
				</div>
			</div>

			<table class="table table-striped">
				<thead>
					<tr>
						<th>Date de réception</th>						
						<th>Référence du dossier</th>
						<th>NIR</th>
						<th>Statut</th>
					</tr>    
				</thead>
				<tbody id="data-list">
				<?php
					// Affichage des informations des dossiers contenus dans la corbeille générale			
					$result = dossiersCorbeilleGenerale($link);
					$rows = mysqli_num_rows($result);

                    for ($i = 0; $i < $rows; $i++) { // Pour chaque dossier de la base de données
						$donnees = mysqli_fetch_array($result);

						// Affichage des informations d'un dossier
						echo("<tr><td>".date("d/m/Y", strtotime($donnees['DateD']))."</td>
							<td>".$donnees['RefD']."</td>
							<td>".$donnees['NirA']."</td>
							<td>".$donnees['StatutD']."</td>
							<td>");
						
						// Mise en forme du design et du lien du bouton permettant le traitement d'un dossier
						if($donnees['StatutD'] == "À traiter")  {
							$class =  "btn btn-success";
							$icon = "glyphicon-plus";
							$variables = "codeD=".$donnees['CodeD']."&statut=En cours"; // Le statut passera automatiquement à "En cours"
						} else { // S'il n'est pas "A traiter", le dossier est consultable
							$class =  "btn btn-primary";
							$icon = "glyphicon-search";
							$variables = "codeD=".$donnees['CodeD'];
						}						
						// Passage du code et/ou du statut du dossier par la méthode GET
						// pour gérer l'affichage sur traiter.php
						echo("<a href='traiter.php?$variables' class='$class' role='button'>
							<span class='glyphicon $icon'></span>
						</a>");
						echo "</td></tr>";
					}
				?>
				</tbody>
			</table>

			<div class="container text-center">
				<ul class="pagination"></ul>
			</div>
		</div>
	</body>	
</html>