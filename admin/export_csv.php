<?php
    require_once("../fonctions.php");
    $link = connecterBD();
    $ftp_stream = connecterServeurFTP();
    
    // Si c'est bien une requête POST pour générer le fichier CSV
    if(isset($_POST["injection_file_save"]) && $_POST["injection_file_save"] == "OK") {
        if(sauvegarderFichierInjectionCSVServeur($ftp_stream, $link)) {
            $injection_file_saved = True;
        }
        else {
            // Booléen pour génération du fichier CSV
            $injection_file_saved = False;
        }
    }

    // Si c'est bien une requête POST pour générer le fichier CSV
    if(isset($_POST["list_folders_save"]) && $_POST["list_folders_save"] == "OK") {
        if(sauvegarderListeDossiersCSVServeur($ftp_stream, $link)) {
            $list_folders_saved = True;
        }
        else {
            // Booléen pour génération du fichier CSV
            $list_folders_saved = False;
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
                        <li><a href="accueil_mnemonique.php"><span class="glyphicon glyphicon-list-alt"></span>Gestion Mnémonique</a></li>
                        <li class="active"><a href="export_csv.php"><span class="glyphicon glyphicon-list-alt"></span>Export CSV</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <h2>Export des données en fichier CSV</h2>
            <?php
                if(isset($injection_file_saved)) {
                    if($injection_file_saved) {
                        genererMessage(
                            "Sauvegarde du fichier d'injection dans DIADEME sur le serveur",
                            "Sauvegarde effectuée avec succès !",
                            "glyphicon glyphicon-cloud-download", 
                            "success"
                        );
                    }
                    else {
                        genererMessage(
                            "Sauvegarde du fichier d'injection dans DIADEME sur le serveur",
                            "Échec lors de la sauvegarde sur le serveur !",
                            "glyphicon glyphicon-cloud-download", 
                            "danger"
                        );
                    }
                }
                else if(isset($list_folders_saved)) {
                    if($list_folders_saved) {
                        genererMessage(
                            "Sauvegarde de la liste des dossiers restants à traiter sur le serveur",
                            "Sauvegarde effectuée avec succès !",
                            "glyphicon glyphicon-cloud-download", 
                            "success"
                        );
                    }
                    else {
                        genererMessage(
                            "Sauvegarde de la liste des dossiers restants à traiter sur le serveur",
                            "Échec lors de la sauvegarde sur le serveur !",
                            "glyphicon glyphicon-cloud-download", 
                            "danger"
                        );
                    }
                }
            ?>
            <div class="container">
                <div class="row">
                    <div class="panel panel-default col-sm-12">
                        <div class="panel-body">
                            <h3 class="col-sm-7">
                                <strong >
                                    <span class="glyphicon glyphicon-share"></span> Injection dans DIADEME
                                </strong>
                            </h3>
                            <div class="col-sm-2 row">
                                <form method="POST" action="export_csv.php">
                                    <input type="hidden" name="injection_file_save" value="OK">
                                    <button type="submit" class="btn btn-default btn-lg col-sm-12" title="Sauvegarder sur le serveur">
                                        <span class='glyphicon glyphicon-cloud-download'></span>
                                    </button>
                                </form>
                                <form method="POST" action="download_csv.php">
                                    <input type="hidden" name="injection_file_download" value="OK">
                                    <button type="submit" class="btn btn-default btn-lg col-sm-12" title="Télécharger en local">
                                        <span class='glyphicon glyphicon-download-alt'></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default col-sm-12">
                        <div class="panel-body">
                            <h3 class="col-sm-7">
                                <strong>
                                    <span class="glyphicon glyphicon-th-list"></span> Dossiers restants à traiter
                                </strong>
                            </h3>
                            <div class="col-sm-2 row">
                                <form method="POST" action="export_csv.php">
                                    <input type="hidden" name="list_folders_save" value="OK">
                                    <button type="submit" class="btn btn-default btn-lg col-sm-12" title="Sauvegarder sur le serveur">
                                        <span class='glyphicon glyphicon-cloud-download'></span>
                                    </button>
                                </form>
                                <form method="POST" action="download_csv.php">
                                    <input type="hidden" name="list_folders_download" value="OK">
                                    <button type="submit" class="btn btn-default btn-lg col-sm-12" title="Télécharger en local">
                                        <span class='glyphicon glyphicon-download-alt'></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>