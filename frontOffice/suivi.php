<?php
    session_start();
	require_once("../fonctions.php");

    if(isset($_GET["delete_session"])) {
        if(isset($_SESSION["Assure"])) unset($_SESSION["Assure"]);      
        if(isset($_SESSION["RefD"])) unset($_SESSION["RefD"]);
        redirigerVers('suivi.php'); // Suppresion des valeurs du POST
	}
		
	$link = connecterBD();
	$post_ok = False; // Ceci n'est pas une demande d'authentification
	
	$msg_error_nir = False; // Il n'y a pas de message d'erreur pour le NIR
    $msg_error_ref = False; // Il n'y a pas de message d'erreur pour la référence du dossier
	$msg_error_nir_ref = False; // Il n'y a pas de correspondance
	
	if(isset($_GET)) {
        if(isset($_GET["RefD"])) {
            if($_GET["RefD"] != "") {
                $ReferenceDossier = $_GET["RefD"];
                if(isset($_SESSION["Assure"])) unset($_SESSION["Assure"]);
                if(isset($_SESSION["RefD"])) unset($_SESSION["RefD"]);
                if(!dossierExiste($_GET["RefD"], $link)) {
                    redirigerVers('depot.php?msg_error_ref=1'); // Passage des varaibles par la méthode GET
                }
            }
            $post_ok = False;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_nir"])) {
            $msg_error_nir = True;
            $post_ok = False;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_ref"])) {
            $msg_error_ref = True;
            $post_ok = False;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_nir_ref"])) {
            $msg_error_nir_ref = True;
            $post_ok = False;  // Ceci n'est pas un premier dépôt
        }
	}
	
	if(isset($_POST["nir"])) {
        //Vérification de la correspondance entre le NIR et la référence du dossier
        if(estAssocie($_POST["nir"], $_POST["refD"], $link)) {
            $_SESSION["Assure"] = chercherAssureAvecNIR($_POST["nir"], $link);      
			$_SESSION["RefD"] = $_POST["refD"];
			$tmp = chercherDossierAvecREF($_POST["refD"], $link);
			
            $_SESSION["Assure"]["DateD"] = $tmp["DateD"];
            $_SESSION["Assure"]["StatutD"] = $tmp["StatutD"];
            $_SESSION["Assure"]["DateTraiterD"] = $tmp["DateAM"];
			$_SESSION["Assure"]["DateAM"] = $tmp["DateAM"];
			
            redirigerVers('suivi.php'); // Suppresion des valeurs du POST
        }
        else {
            if(!assureExiste($_POST["nir"], $link)) $msg = "RefD=".$_POST["refD"]."&msg_error_nir=1";
            if(!dossierExiste($_POST["refD"], $link)) {
                if($msg != "") $msg .= "&";
                $msg = "msg_error_ref=1";
            }
            else {
                if($msg != "") $msg .= "&msg_error_nir_ref=1";
                else $msg .= "RefD=".$_POST["refD"]."&msg_error_nir_ref=1";
            }
		}

        redirigerVers('suivi.php?'.$msg); // Passage des variables par la méthode GET
	}

	if(isset($_SESSION["Assure"])) {
		// Pour mettre les jours données en cas de rafraîchissement de la page  
		$tmp = ChercherDossierAvecREF($_SESSION["RefD"], $link);
		
		$_SESSION["Assure"]["DateD"] = $tmp["DateD"];
		$_SESSION["Assure"]["StatutD"] = $tmp["StatutD"];
		$_SESSION["Assure"]["DateTraiterD"] = $tmp["DateAM"];
		$_SESSION["Assure"]["DateAM"] = $tmp["DateAM"];

        $NirAssure = $_SESSION["Assure"]["NirA"];
        $ReferenceDossier = $_SESSION["RefD"];
        $NomAssure = $_SESSION["Assure"]["NomA"];
        $PrenomAssure = $_SESSION["Assure"]["PrenomA"];
        $TelephoneAssure = $_SESSION["Assure"]["TelA"];
		$MailAssure = $_SESSION["Assure"]["MailA"];
		$DateD = strtotime($_SESSION["Assure"]["DateD"]);
		$StatutD = $_SESSION["Assure"]["StatutD"];
		$DateTraiterD = strtotime($_SESSION["Assure"]["DateTraiterD"]);
        $DateAM = strtotime($_SESSION["Assure"]["DateAM"]);
        $post_ok = True;
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
		<script>
        	<?php if ($post_ok) : ?>
				$(document).ready(function(){
					$("#form_panel").remove(); //Le formulaire est affiché
				});
			<?php else : ?>
				$(document).ready(function(){
					$("#form_panel").show(); //Le formulaire est affiché
				});
			<?php endif ?>
		</script>
		<title>PJPE - Suivi de dossier</title>
	</head>
	<body>
		<!-- HEADER -->
		<nav class="navbar navbar-default header">
			<div class="container">
				<div class="navbar-header">
					<h1>PJPE - Suivi de dossier</h1>
				</div>
			</div>
		</nav>

		<div class="container-fluid">
            <!-- Message en cas d'erreur d'authentification -->
            <?php
                if($msg_error_nir_ref) {
                    GenererMessage (
                        "Échec lors de l'authentification !",
                        "Ces identifiants sont invalides !",
                        "remove",
                        "danger"
                    );
                }
            ?>
                
            <!-- Message en cas de référence de dossier valide -->                
            <?php
                if(!$post_ok && !$msg_error_nir_ref && !$msg_error_nir && !$msg_error_ref) {
                    $title = "Veuillez saisir votre NIR";
                    $body = "Dans le but de vous authentifier, merci de saisir votre NIR";

                    if(isset($_GET["RefD"]) && $_GET["RefD"] == "")
                        $title .= ", ainsi que la référence du dossier qui vous a été délivrée.";
                        $body .= " et la référence de votre dossier";

                    $body .= " dans le champ prévu à cet effet.";

                    GenererMessage (
                        $title,
                        $body,
                        "user",
                        "info"
                    );
                }
            ?>

            <!-- Message en cas d'erreur de NIR inconnu -->
            <?php 
                if ($msg_error_nir) {
                    GenererMessage (
                        "NIR non enregistré !",
                        "Il semblerait que ce NIR ne soit affilié à aucun dossier.",
                        "remove",
                        "danger"
                    );
                }
            ?>

            <!-- Message en cas d'erreur de référence inconnue -->
            <?php 
                if ($msg_error_ref) {
                    GenererMessage (
                        "Référence invalide !",
                        "Ce lien ne permet pas de référencer un dossier enregistré !",
                        "link",
                        "warning"
                    );
                }
            ?>
		</div>
		
        <div class="container">
            <div class="panel panel-default" id="form_panel">
                <div class="panel-heading">Formulaire de suivi</div>
                <div class="panel-body">
					<form method="POST" action="suivi.php">
						<div class="container" id="etat-civil">
							<h3>Identification :</h3>

							<div class="row">
								<div class="col-sm-4">
									<label for="nir" class="control-label">N° Sécurité sociale <span class="champ_obligatoire">(*)</span> :</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="	glyphicon glyphicon-barcode"></i></span>
										<input id="nir" type="text" class="form-control" name="nir"
											pattern="^[0-9]( [0-9]{2}){3}( [0-9]{3}){2}$"
											placeholder="# ## ## ## ### ###"
											onKeyUp='checkFormatNir("# ## ## ## ### ###");'
											<?php if(isset($NirAssure)) echo "value='$NirAssure' readonly " ?>
											required
										>
									</div>
								</div>
								<div class="col-sm-2">
									<button type="button" class="btn btn-light" id="btn-modal"  data-toggle="modal" data-target=".bs-example-modal-sm" title="Où puis-je trouver mon numéro de sécurité sociale ?">?</button>
									<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
												<div class="modal-header head_modal_title">
													<h1>Où puis-je trouver mon numéro de sécurité sociale ?</h1>
												</div>
											</div>
											<div class="modal-content">
												<div class="modal-header">
													<h2>Sur ma Carte Vitale</h2>
												</div>
												<div class="modal-body">
													<p>Votre numéro de sécurité sociale ou NIR figure sur la face recto de votre carte vitale (ici encadré en rouge).</p>
													<img src="../img/photo-carte-secu.png" alt="Image de la carte Vitale">
												</div>
											</div>
											<div class="modal-content">
												<div class="modal-header">
													<h2>Sur mon attestation de sécurité sociale</h2>
												</div>
												<div class="modal-body">
													<p>
														Votre numéro de sécurité sociale est également inscrit sur votre attestation de droits que vous pouvez obtenir de votre compte ameli.
														Ce document contient les mêmes informations que votre carte Vitale.
													</p>
												</div>
											</div>
											<div class="modal-content">
												<div class="modal-header">
													<h2>Sur mon bulletin de salaire</h2>
												</div>
												<div class="modal-body">
													<p>
														Si vous avez déjà travaillé en France, votre numéro de sécurité sociale est également inscrit sur tous vos bulletins de salaire.
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<label for="nom" class="control-label">Référence du dossier en cours :</label>    
									<div class="row">
										<div class="col-sm-6">
											<div class="input-group">
												<span class="input-group-addon"><i class="	glyphicon glyphicon-folder-close"></i></span>
												<input onKeyUp="checkFormatRefD();" id="refD" type="text" class="form-control" 
													name="refD" placeholder="8 caractères alphanumériques" pattern="^[a-zA-Z0-9]{8}$"                                                    
													<?php
														if(isset($ReferenceDossier)){ echo "value='$ReferenceDossier' readonly";}
													?>
													required
												>
											</div>          
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
							</div>
						</div>
						<div class="row text-center" style="margin-top: 20px;">
							<div class="col-sm-4">
								<button type="submit" class="btn btn-primary btn-lg">
									<span class="glyphicon glyphicon-lock"></span>Valider
								</button>
							</div>
							<div class="col-sm-4">
								<a href="../" class="btn btn-danger btn-lg">
									<span class="glyphicon glyphicon-new-window"></span>
									Retour à l'accueil
								</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<?php if($post_ok) : ?>
			<div class="container">
				<div class="row container">
					<div id="panel-assure" class="col-lg-6">
						<div class="row">
							<div class="col-xs-12">
								<h3><span class="glyphicon glyphicon glyphicon-user"></span> NIR : <?php echo $NirAssure;?></h3>
								<h5><?php echo "Assuré : $PrenomAssure $NomAssure";?></h5>
								<h5>En arrêt de travail depuis le : <?php echo date("d/m/Y", $DateAM);?></h5>
								<h5>
									<?php
										if($TelephoneAssure != "") echo "Tel : $TelephoneAssure";
										else echo "Tel : N/A";
									?>
									/
									<?php
										if($MailAssure != "") echo "Email : $MailAssure";
										else echo "N/A";
									?>
								</h5>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="suivi" class="container">
				<?php if($StatutD == "À traiter") : ?>

					<?php
							GenererMessage (
								"Dossier No. $ReferenceDossier",
								"Votre dossier a bien été réceptionné par nos services !",
								"inbox",
								"warning"
							);
					?>
									
					<div class="bar-icons warning">
						<span class="icon glyphicon glyphicon-download-alt"></span>
						<span class="icon glyphicon glyphicon-inbox"></span>
						<span class="icon glyphicon glyphicon-hourglass disabled"></span>
						<span class="icon glyphicon glyphicon-edit disabled"></span>
					</div>

					<div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-warning active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:36.80%">
							Dossier réceptionné
						</div>
					</div>

				<?php elseif($StatutD == "En cours") :?>
				
					<?php
							GenererMessage (
								"Dossier No. $ReferenceDossier",
								"Votre dossier est en cours de traitement par un de nos agents !",
								"hourglass",
								"info"
							);
					?>

					<div class="bar-icons info">
						<span class="icon glyphicon glyphicon-download-alt"></span>
						<span class="icon glyphicon glyphicon-inbox"></span>
						<span class="icon glyphicon glyphicon-hourglass"></span>
						<span class="icon glyphicon glyphicon-edit disabled"></span>
					</div>

					<div class="progress">
						<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:66.66%">
							Dossier en cours de traitement
						</div>
					</div>

				<?php elseif($StatutD == "Classé sans suite") :?>

					<?php
							GenererMessage (
								"Dossier No. $ReferenceDossier",
								"Les documents que nous avons reçus ne nous permettent pas de procéder au versement d'indemnités journalières.",
								"remove",
								"danger"
							);
					?>

					<div class="bar-icons danger">
						<span class="icon glyphicon glyphicon-download-alt"></span>
						<span class="icon glyphicon glyphicon-inbox"></span>
						<span class="icon glyphicon glyphicon-hourglass"></span>
						<span class="icon glyphicon glyphicon-remove"></span>
					</div>

					<div class="progress">
						<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">
							Dossier mis en attente
						</div>
					</div>

				<?php else : ?>

					<?php
							GenererMessage (
								"Dossier No. $ReferenceDossier",
								"Votre dossier est valide et a bien été traité ! Vous recevrez bientôt vos indemnités journalières.",
								"ok",
								"success"
							);
					?>

					<div class="bar-icons success">
						<span class="icon glyphicon glyphicon-download-alt"></span>
						<span class="icon glyphicon glyphicon-inbox"></span>
						<span class="icon glyphicon glyphicon-hourglass"></span>
						<span class="icon glyphicon glyphicon-ok"></span>
					</div>

					<div class="progress">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">
							Dossier validé
						</div>
					</div>

				<?php endif ?>

					<div class="row text-center" style="margin-top: 20px;">
						<div class="col-sm-4">
							<a href="suivi.php?delete_session" class="btn btn-default btn-lg">
								<span class="glyphicon glyphicon-new-window"></span>
								Consulter un autre dossier
							</a>
						</div>
						<?php if($StatutD != "Terminé") :?>
						<div class="col-sm-4">
							<a href="depot.php" class="btn btn-default btn-lg">
								<span class="glyphicon glyphicon-new-window"></span>
								Renvoyer des justificatifs
							</a>
						</div>
						<?php endif ?>					
					</div>
			</div>
		<?php endif ?>

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
								<img src="../img/logo_oups.svg" alt="Logo oups.gouv.fr">
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
								<img src="../img/num_tel.png" alt="36 46">
							</div>																										
							<div class="col-sm-12">Ouvert du lundi au vendredi de 08h00 à 17h00</div>														
							<div class="col-sm-12">Fermé le samedi et le dimanche</div>
						</div>
					</div>
				</div>
			</div>
			<div id="juridique" class="row">				
				<div class="col-sm-3">
					<a href="../documentation-juridique/cgu.html" target="_blank">Conditions Générales d'Utilisation</a>
				</div>				
				<div class="col-sm-3">
					<a href="../documentation-juridique/politiquedp.html" target="_blank">Politique de traitement des données personnelles</a>
				</div>				
				<div class="col-sm-3">
					<a href="../documentation-juridique/politiquecookies.html" target="_blank">Politique de traitement des cookies</a>
				</div>				
				<div class="col-sm-3">
					<a href="../documentation-juridique/mentionslegales.html" target="_blank">Mentions Légales</a>
				</div>
			</div>
			<div id="copyright" class="row">© 2020 Copyright - Tous droits réservés : Team BAAAAAM</div>
        </footer>
		
    </body>
</html>