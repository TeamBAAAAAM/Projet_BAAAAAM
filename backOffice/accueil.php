<?php 
	session_start();
    require("../fonctions.php");
    // Connexion à la BD
    $link = connexionMySQL();
    if ($link == NULL){
        //Redirection
	}
	
	// Récupération des données du technicien
	$matricule = $_POST["matricule"];	
	$res = getTechnicienData($link, $matricule);
	$ligne = mysqli_fetch_array($res);
	$codeT = $ligne["CodeTech"];
	$nomT = $ligne["NomT"];
	$prenomT = $ligne["PrenomT"];

	// test
	$matricule = "12345";
	$codeT = "Code";
	$nomT = "Nom"; 
	$prenomT = "Prénom";

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

        <title>BAAAAAM - Réception des documents</title>
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
					<h1>Back Office</h1>
				</div><!-- 
				<div class="" id="myNavbar">						
					<div class="nav navbar-nav navbar-right dropdown">
						<button class="btn btn-default dropdown-toggle"
							type="button" id="menu1" data-toggle="dropdown">
							<h4><?php echo("$nomT $prenomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span></h4>
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

		<nav class="navbar navbar-inverse navbar-static-top">
			<h4>
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
							<li><a href="#"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
							<li><a href="#"><span class="glyphicon glyphicon-folder-open"></span> Ma Corbeille</a></li>
						</ul>

						<ul class="nav navbar-nav navbar-right dropdown">
							<li class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<?php echo("$nomT $prenomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
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
			</h4>
		</nav>
		
		<div class="container">
			<div class="table-responsive">          
				<table id="data-table" class="table table-hover">
					<thead>
						<tr>
							<th>Aujourd'hui</th>
						</tr>
					</thead>
					<tbody id="data-list">
						<tr>
							<td>Dossiers reçus aujourd'hui</td>
                                <td>
                                    <?php 
                                        $res = nbDossiersRecus($link);                                                                
                                        $ligne = mysqli_fetch_array($res);
                                        echo $ligne["nbDossiersRecus"];
                                    ?>
                                </td>
						</tr>
						<tr>
							<td>Dossiers restant à traiter</td>
								<td>
									<?php 
										$res = nbDossiersATraiter($link);
										$ligne = mysqli_fetch_array($res);
										echo $ligne["nbDossiersAtraiter"];
									?>
								</td>
						</tr>
						<tr>
							<td>Dossiers classés sans suite aujourd'hui</td>
								<td>
									<?php 
										$res = nbDossiersClasses($link);
										$ligne = mysqli_fetch_array($res);
										echo $ligne["nbDossiersClasses"];
									?>
								</td>
						</tr>
						<tr>
							<td>Autre</td>
							<td>15</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- <div id="pagination" class="container">
			<div class="row">
				<div class="col-sm-12">
					<ul class="pagination">
						<li class="active"><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li><a href="#">6</a></li>
						<li><a href="#">7</a></li>
						<li><a href="#">8</a></li>
						<li><a href="#">9</a></li>
						<li><a href="#">10</a></li>
					</ul>
				</div>
			</div>
		</div> -->


	</body>	
</html>