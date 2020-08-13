
<?php
require("../fonctions.php");
	// Format des dates en français
	setlocale(LC_TIME, "fr_FR");

	// Connexion à la BD
	$link = connecterBD();
?>
<!DOCTYPE html>
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
               
        <title>PJPE - Administrateur</title>
    </head>
    <body>
        <nav class="navbar navbar-default header">
			<div class="container">
				<div class="navbar-header">
					<h1>PJPE - Administrateur</h1>
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
                        <li><a href="accueil_categorie.php"><span class="glyphicon glyphicon-home"></span> Gestion Catégorie </a></li>
                        <li class="active"><a href="accueil_mnemonique.php"><span class="glyphicon glyphicon-list-alt"></span>Gestion Mnémonique</a></li>
                        <li><a href="export_csv.php"><span class="glyphicon glyphicon-list-alt"></span>Export CSV</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
			<?php
				if(isset($_GET['msg']) && $_GET['msg'] == "Success") {
					if(isset($_GET['action']) && $_GET['action'] == "creer") {
						genererMessage(
							"Ajout de mnémonique",
							"Succès lors de l'ajout !",
							"check",
							"success"
						);				
					}		
					else if(isset($_GET['action']) && $_GET['action'] == "modifier") {
						genererMessage(
							"Modification de mnémonique",
							"Modification effectuée avec succès !",
							"check",
							"success"
						);
					}
				}
            ?> 
            <div>
				<div>
                	<a href='creation_mnemonique.php' class="btn btn-default" role="button">
						<i class='glyphicon glyphicon-plus'></i> Nouvelle Mnémonique
					</a>
				</div>
                <h2>Liste Mnénomiques</h2>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Mnémonique</th>						
							<th>Désignation</th>
							<th></th>
						</tr>
					</thead>
					<tbody >					
				<?php					
					$result = listeMnemoniques($link);
					
					if ($result != NULL) 
						$rows = mysqli_num_rows($result);
					else $rows = 0;
                                                                           
					for ($i = 0; $i < $rows ; $i++){
						$donnees = mysqli_fetch_array($result);
                                              
						echo ("<tr>
									<td>".$donnees['Mnemonique']."</td>
									<td>".$donnees['Designation']."</td>
									<td><a href='modifier_mnemonique.php?id=".$donnees['CodeM']."'><i class='glyphicon glyphicon-pencil'></i></a></td>
								</tr>"
							);
						}
					?>
					</tbody>
				</table>
			</div>       
        </div>
    </body>
</html>
