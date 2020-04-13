<?php
// Connexion base de données
try
{
    //$bdd = new PDO('mysql:host=localhost;dbname=bd_cpam;charset=utf8', 'root', 'root');
    $bdd = new PDO('mysql:host=localhost;dbname=bd_cpam;charset=utf8', 'root', '');
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
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Menu    <span class="caret"></span></a>
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