<?php 
	session_start();
    require("../fonctions.php");
    // Connexion à la BD
    $link = connexionMySQL();
	
	// Récupération des données du technicien
	if(isset($_SESSION["matricule"])){
		$matricule = $_SESSION["matricule"];
		$codeT = $_SESSION["codeT"];
		$nomT = $_SESSION["nomT"];
		$prenomT = $_SESSION["prenomT"];
	}

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

        <title>PJPE - Ma Corbeille</title>
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
			<div class="row">
				<div class="col-xs-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i>Recherche un élément</span>
						<input id="recherche" type="text" class="form-control" placeholder="Date de réception, Référence du dossier, NIR, Statut ...">
					</div>
				</div>
			</div>			
			<div class="row">
				<div class="col-xs-4">
					<div class="input-group input-date">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i>Début</span>
						<input id="date_debut" type="date" class="form-control">
					</div>
				</div>
				<div class="col-xs-4">
					<div class="input-group input-date">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i>Fin</span>
						<input id="date_fin" type="date" class="form-control">
					</div>
				</div>
				<div class="col-xs-4">
					<div class="input-group">
						<span class="input-group-addon">Statut</span>
						<select  class="form-control" id="statut">
							<option>À traiter</option>
							<option>En cours</option>
							<option>Classé sans suite</option>
							<option>Terminé</option>
						</select>
					</div>
				</div>
			</div>
		
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Date de réception</th>						
						<th>N° de demande</th>
						<th>NIR</th>
						<th>Statut</th>
					</tr>    
				</thead>
				<tbody id="data-list">
				<?php
					//$reponse = $bdd->query('SELECT d.DATED, d.REFD, a.NIRA  FROM traiter t, dossier d, assure a where t.CODED=d.CODED and d.CODEA=a.CODEA  ');
					//$reponse = DossiersCorbeilleTechnicien($link);
					/* while ($donnees = $reponse->fetch())
					{
						echo ("<tr><td>".$donnees['DateD']."</td>
									<td>".$donnees['RefD']."</td>
									<td>".$donnees['NirA']."</td> 
									<td><button type='button' class='btn btn-info'><span class='glyphicon glyphicon-plus'></span></button></td></tr>");
					}
					$reponse->closeCursor(); */
					$result = DossiersCorbeilleTechnicien($link);	
					if ($result != NULL)
						$rows = mysqli_num_rows($result);
					else $rows = 0;
                    for ($i = 0; $i < $rows; $i++){
						$donnees = mysqli_fetch_array($result);
						echo ("<tr><td>".$donnees['DateD']."</td>
									<td>".$donnees['RefD']."</td>
									<td>".$donnees['NirA']."</td>
									<td>".$donnees['StatutD']."</td>
									<td><a href='traiter.php?codeD=".$donnees['CodeD']."' class='btn btn-warning' role='button'>
								<span class='glyphicon glyphicon-search'></span></a>
							</tr></td>");
					}
				?>
				</tbody>
			</table>
		</div>
	</body>	
</html>