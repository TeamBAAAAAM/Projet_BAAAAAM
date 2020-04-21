<?php
    session_start();
    require_once("fonctions.php");
?>
<!DOCTYPE html>


<html lang="en">
	<head>
		<title>PJPE - Accueil</title>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
                <script src="frontOffice/script.js"></script>
		<script>
			$(document).ready(function(){
                         
			  $("#research").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#data-list tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			  });
			});
		</script>
              
	</head>
	<body>
		<nav class="navbar navbar-default header welcome">
			<div class="container">
				<div class="navbar-header">
					<h1>Bienvenue sur PJPE</h1>
				</div>
			</div>
		</nav>
		<div class="container-fluid text-center">
			<div class="row">
				<div id="send_button" class="col-sm-3">
					<a class="btn btn-lg" href="frontOffice/depot.php">J'envoie mes justificatifs maintenant</a>
                                </div>
                        </div>
                    <div class="row">
                                <div id="salarie" class="col-sm-3 btn btn-status send_button">
                                   <h2>Je compléte mon dossier</h2>
				</div>
			</div>
		</div>
            <div class="container">
            <div class="panel panel-default" id="form_panel">
                <div class="panel-heading">Formulaire d'envoi</div>
                <div class="panel-body">
                 <form enctype="multipart/form-data" method="POST" action="depot.php"> 
                 <div class="col-sm-4">
                                    <label for="nir" class="control-label">N° Sécurité sociale (*) :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
                                        <input id="nir" type="text" class="form-control" name="nir"
                                            pattern="^[0-9]( [0-9]{2}){3}( [0-9]{3}){2}$"
                                            placeholder="# ## ## ## ### ###"
                                            onKeyUp='checkFormatNir("# ## ## ## ### ###");'
                                            value="<?=isset($data["nir"]) ? $data["nir"] : "" ?>"
                                            required
                                        >
                                    </div>
                </div>
                <div class="col-sm-6">
                                    <label for="nom" class="control-label">Référence du dossier en cours :</label>    
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="	glyphicon glyphicon-folder-close"></i></span>
                                                <input onKeyUp="checkFormatRefD();" id="refD" type="text" class="form-control" name="refD" placeholder="8 caractères alphanumériques" pattern="^[a-zA-Z0-9]{8}$">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button id="checkref" type="button" class="btn btn-primary" onClick="verifierRef();">
                                                <strong>&#128272;</strong> Vérifier
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <span class="note">
                                                À ne remplir uniquement que si vous avez déjà envoyé des justificatifs via ce formulaire.
                                                Il vous a été délivré lors de la confirmation de la prise en charge de votre demande.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                               </form>
                            </div>
                        </div>
                    </div>
                
		<footer class="container-fluid text-center">
			<div class="row">
				<div class="col-sm-3">
					<div class="title_footer">Présentation</div>
					<div class="row">
						<p>Ajouter une description ici.</p>
					</div>
				</div>
				<div id="links" class="col-sm-3">
					<div class="title_footer">Liens utiles</div>
					<div class="row">
						<div class="col-sm-12">
							<a class="col-sm-12" href="https://www.ameli.fr/haute-garonne" target="_blank">
								Site ameli - Haute Garonne
							</a>
						</div>
						<div class="col-sm-12">
							<a class="col-sm-12" href="https://assure.ameli.fr/PortailAS/appmanager/PortailAS/assure?_somtc=true" target="_blank">
								Mon compte ameli
							</a>
						</div>
						<div class="col-sm-12">
							<a class="col-sm-12" href="https://www.oups.gouv.fr/">
								<img src="img/logo_oups.svg" alt="Logo oups.gouv.fr">
							</a>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="title_footer">Contact</div>
					<div id="contact" class="row">
						<iframe class="col-sm-5" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d23113.985644999426!2d1.4384851395507818!3d43.601373400000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebc91ae4a5ba3%3A0x5d4ac376bccc8d50!2sCPAM%20de%20la%20Haute-Garonne!5e0!3m2!1sfr!2sfr!4v1580058891942!5m2!1sfr!2sfr" style="border:0;" allowfullscreen=""></iframe>	
						<div class="col-sm-7 text-left">
							<div class="col-sm-12">Caisse Primaire d'Assurance Maladie</div>
							<div class="col-sm-12">3, Boulevard du Professeur Léopold Escande</div>	
							<div class="col-sm-12">Haute-Garonne (31) - 31093 Toulouse</div>													
							<div class="col-sm-12">	
								<img src="img/num_tel.png" alt="36 46">
							</div>																										
							<div class="col-sm-12">Ouvert du lundi au vendredi de 08h00 à 17h00</div>														
							<div class="col-sm-12">Fermé le samedi et le dimanche</div>
						</div>
					</div>
				</div>
			</div>
			<div id="juridique" class="row">				
				<div class="col-sm-3">
					<a href="documentation-juridique/cgu.html" target="_blank">Conditions Générales d'Utilisation</a>
				</div>				
				<div class="col-sm-3">
					<a href="documentation-juridique/politiquedp.html" target="_blank">Politique de traitement des données personnelles</a>
				</div>				
				<div class="col-sm-3">
					<a href="documentation-juridique/politiquecookies.html" target="_blank">Politique de traitement des cookies</a>
				</div>				
				<div class="col-sm-3">
					<a href="documentation-juridique/mentionslegales.html" target="_blank">Mentions Légales</a>
				</div>
			</div>
			<div id="copyright" class="row">© 2020 Copyright - Tous droits réservés : Team BAAAAAM</div>
        </footer>
	</body>	
</html>