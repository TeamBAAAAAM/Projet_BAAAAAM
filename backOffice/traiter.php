<?php 
	session_start();
	require_once("../fonctions.php");
	
    // Connexion à la BD
	$link = connecterBD();

	// Récupération des données du technicien connecté
	if(isset($_SESSION["matricule"])){
		$matricule = $_SESSION["matricule"];
		$codeT = $_SESSION["codeT"];
		$nomT = $_SESSION["nomT"];
		$prenomT = $_SESSION["prenomT"];
	} else { // Redirection sinon
		redirigerVers("index.php");
	}

	// Récupération des données du dossier en cours de traitement
	if(isset($_GET["statut"])) { // Nouveau statut du dossier 

		// Mise en session du code du dossier
		if(isset($_GET["codeD"])) $_SESSION["codeDossier"] = $_GET["codeD"];
		// Mise à jour de la BD selon le changement de statut demandé
		traiterDossier($codeT, $_SESSION["codeDossier"], $_GET["statut"], $link);

		// Sortie d'un dossier de la corbeille d'un technicien
		if($_GET["statut"] == "À traiter") {
			libererDossier($link, $_SESSION["codeDossier"]);
			redirigerVers("corbeille_generale.php");
		}
		else if($_GET["statut"] != "En cours") {			
			$dossier = chercherDossierTraiteAvecCodeD($_SESSION["codeDossier"], $link);
			envoyerMailConfirmationTraitement(
				$dossier["PrenomA"],
				$dossier["NomA"], 
				$dossier["RefD"],
				$dossier["MailA"],
				$_GET["statut"]
			);
		}
		// Redirection pour supprimer les variables transmises dans l'URL
		redirigerVers("traiter.php");

	} else if(isset($_GET["codeD"])) {
		$_SESSION["codeDossier"] = $_GET["codeD"];
		// Redirection pour supprimer les variables transmises dans l'URL
		redirigerVers("traiter.php");
	}

	// Récupération des données de la PJ
	if(isset($_GET["statutPJ"]) && isset($_GET["codePJ"])) {
		if($_GET["statutPJ"] == "NULL") {
			changerStatutPJ($link, $_GET["codePJ"], $_GET["statutPJ"], Null);
		}
		else changerStatutPJ($link, $_GET["codePJ"], $_GET["statutPJ"], $codeT);
		// Redirection pour supprimer les variables transmises dans l'URL
		redirigerVers("traiter.php");
	}
	else if(isset($_GET["statutPJ"]) || isset($_GET["codePJ"])) {
		// Redirection pour supprimer les variables transmises dans l'URL
		redirigerVers("traiter.php");
	}
		
	// Redirection s'il n'y a pas de code dossier pour restreindre l'accès à la page traiter.php
	if(!isset($_SESSION["codeDossier"])) {	
		redirigerVers("accueil.php");
	}

	// Données du dossier et de l'assuré
	$dossier = chercherDossierTraiteAvecCodeD($_SESSION["codeDossier"], $link);
	$refDossier = $dossier["RefD"];
	$codeDossier = $dossier["CodeD"];
	$dateReception = strtotime($dossier["DateD"]);
	$statutDossier = $dossier["StatutD"];
	$codeAssure = $dossier["CodeA"];
	$nirAssure = $dossier["NirA"];
	$nomAssure = $dossier["NomA"];
	$prenomAssure = $dossier["PrenomA"];
	$telephoneAssure = $dossier["TelA"];
	$mailAssure = $dossier["MailA"];
	$dateArretMaladie = strtotime($dossier["DateAM"]);
	$dateTraite = strtotime($dossier["DateTraiterD"]);
	// Données du technicien en charge du dossier
	$codeT_dossier = $dossier["CodeT"]; 
	$matricule_dossier = $dossier["Matricule"];
	$nomT_dossier = $dossier["NomT"];
	$prenomT_dossier = $dossier["PrenomT"];

	// Récupération des messages envoyés à l'assuré
	$messagesAssure = listeMessages($codeAssure, $link);
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

        <title>PJPE - Traitement</title>
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
						<li><a href="accueil.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
						<li><a href="corbeille_generale.php"><span class="glyphicon glyphicon-list-alt"></span> Corbeille générale</a></li>
						<li><a href="ma_corbeille.php"><span class="glyphicon glyphicon-inbox"></span> Ma Corbeille</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right dropdown">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<?php echo("$prenomT $nomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li role="presentation"><a role="menuitem" href="index.php?logout"><span class="glyphicon glyphicon-log-out"></span>Se déconnecter</a></li>
							</ul>
						</li>						
					</ul>
				</div>
			</div>
		</nav>		

		<div class="container">		
			<?php
				if(isset($_POST['email'])) {
					if(envoyerMailDemandePJ($mailAssure, $refDossier, implode("",$_POST['mail_text']))) {
						GenererMessage (
							"Mail envoyé !",
							"Votre message a bien été envoyé.",
							"envelope",
							"success"
						);

						$contenu = "À : $mailAssure\n";
						$contenu .= "Objet : ".MAIL_REQUEST_SUBJECT." [REF. ".$refDossier."]"."\n";
						$contenu .= "Message : ".implode("",$_POST['mail_text']);
						$contenu = explode("'", $contenu);
						$contenu = implode("\\'", $contenu);

						if(enregistrerMessageAssure($codeAssure, $codeT, $contenu, $link)) {						
							GenererMessage (
								"Mail enregistré !",
								"Votre message a bien été enregistré.",
								"saved",
								"success"
							);
							
							//Reconnexion à la BD en cas de réussite de l'enregistrement
							mysqli_close($link);
							$link = connecterBD();

							//Récupération des messages de l'assuré
							$messagesAssure = listeMessages($codeAssure, $link);
						}
						else {						
							GenererMessage (
								"Erreur lors de l'enregistrement !",
								"Votre message n'a pas pu être enregistré !",
								"remove",
								"danger"
							);
						}
					}
					else {				
						GenererMessage (
							"Erreur lors de l'envoi !",
							"Votre message n'a pas pu être envoyé !",
							"remove",
							"danger"
						);
					}
					// Suppression après envoi
					unset($_POST['subject']); 
					unset($_POST['mail_text']);
					unset($_POST['email']);
				}
			?>

			<div class="row container">
				<div id="panel-dossier" class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<!-- Affichage des informations relatives au dossier -->
								<div class="col-xs-12">
									<h3><span class="glyphicon glyphicon-folder-open"></span> DOSSIER No : <?php echo $refDossier;?></h3>
									<h5>Date de réception :  <?php echo date("d/m/Y", $dateReception);?></h5>
									<h5>Suivi par :  <?php echo "$prenomT_dossier $nomT_dossier ($matricule_dossier)";?></h5>
									<h5><?php if ($statutDossier != "En cours") echo "Traité le :  ".date("d/m/Y H:i", $dateTraite); else echo "Depuis le :  ".date("d/m/Y H:i", $dateTraite); ?></h5>
								</div>
								<div class="col-lg-12 btn-group btn-group-justified" role="group">
									<!-- Pour sortir un dossier de la corbeille d'un technicien -->
									<a href="traiter.php?statut=À%20traiter" class="btn btn-default<?php if(!($statutDossier == "En cours")) {echo(" disabled");}?>" role="button">
										<span class="glyphicon glyphicon-minus-sign"></span>Remettre à traiter</a>
									<!-- Pour un dossier En cours -->
									<a href="traiter.php?statut=En%20cours"
										class="<?php classBoutonTraiterDossier($statutDossier, "En cours", $codeT_dossier, $codeT);?>"
										role="button"><span class="glyphicon glyphicon-hourglass"></span>En cours</a>
								</div>	
								<div class="col-lg-12 btn-group btn-group-justified" role="group">
									<!-- Pour mettre le dossier à Classé sans suite -->
									<a href="traiter.php?statut=Classé%20sans%20suite"
										class="
											<?php classBoutonTraiterDossier($statutDossier, "Classé sans suite", $codeT_dossier, $codeT);?>
											<?php if(nbPJsSelonStatut($link, "NULL", $codeDossier) > 0) echo " disabled";?>
										" 
										role="button"><span class="glyphicon glyphicon-remove"></span>Classé sans suite</a>
									<!-- Pour mettre le dossier à Terminé -->
									<a href="traiter.php?statut=Terminé"
										class="
											<?php classBoutonTraiterDossier($statutDossier, "Terminé", $codeT_dossier, $codeT);?>
											<?php if(nbPJsSelonStatut($link, "NULL", $codeDossier) > 0) echo " disabled";?>
										"
										role="button"><span class="glyphicon glyphicon-ok"></span>Terminé</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="panel-assure" class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12">
									<h3><span class="glyphicon glyphicon glyphicon-user"></span> NIR : <?php echo $nirAssure;?></h3>
									<h5><?php echo "Assuré : $prenomAssure $nomAssure";?></h5>
									<h5>En arrêt de travail depuis le : <?php echo date("d/m/Y", $dateArretMaladie);?></h5>
									<h5>
										<?php
											if($telephoneAssure != "") echo "Tel : $telephoneAssure";
											else echo "Tel : N/A";
										?>
										
										<?php
											if($mailAssure != "") echo "Email : $mailAssure";
											else echo "Email : N/A";
										?>
									</h5>
								</div>
								<div class="col-xs-12 btn-group btn-group-vertical">
									<!-- Seul le technicien en charge du dossier peut envoyer un mail -->
									<button type="button" class="btn btn-default<?php if($codeT != $codeT_dossier) echo " disabled";?>" 
									data-toggle="modal" data-target="#myModal">
										<span class="glyphicon glyphicon-send"></span>Envoyer un mail à l'assuré
									</button>
									<!-- Bouton actif seulement s'il existe des messages enregistrés -->
									<button type="button" class="btn btn-default<?php if($messagesAssure == null) echo " disabled";?>" 
									data-toggle="modal" data-target="#myModal2">
										<span class="glyphicon glyphicon-th-list"></span>Historique des messages
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal pour l'affichage de la liste des messages -->
		<div id="myModal2" class="modal fade" role="dialog">
			<div class="modal-dialog modal-lg">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>
						<h4 class="modal-title"><span class="glyphicon glyphicon-th-list"></span>Historique des messages</h4>
					</div>
					<div class="modal-body">
						<?php
							// Affichage de la liste des mails envoyés à l'assuré
							$message = mysqli_fetch_array($messagesAssure);
							$i = 1;

							if($message == null) {
								genererMessage(
									"Vide",
									"Aucun message enregistré n'est affilié à cet assuré !",
									"floppy-disk",
									"warning"
								);
							}
							while ($message != null) {
								$contenuMessage = extraireMessage($message["Contenu"]);
								echo '
									<div class="btn btn-primary btn-block" 
										onclick=\'$("#m'.$i.'").toggle(500); $("#m'.$i.'-title").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");\'>
										<div class="row" style="text-align: left;">
											<div class="col-lg-8">
												<span id="m'.$i.'-title" class="glyphicon glyphicon-chevron-right"></span>'.$contenuMessage[1].'
											</div>
											<div class="col-lg-2" style="text-align: right;">
												<span class="glyphicon glyphicon-barcode"></span>'.$message["Matricule"]."
												| <span class='glyphicon glyphicon-time'></span>".date("d/m/Y H:i:s", strtotime($message["DateEnvoiM"])).'
											</div>
										</div>
									</div> 
									<div id="m'.$i.'" class="panel panel-info" style="display: none; margin-bottom: 0px;">
										<div class="panel-heading">
											<h5>À : '.$contenuMessage[0].'</h5>
										</div>
										<div class="panel-body">'.$contenuMessage[2].'</div>
									</div>
								';
								$message = mysqli_fetch_array($messagesAssure);
								$i++;
							}
						?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
					</div>
				</div>
			</div>
		</div>

		<form method="POST" action="traiter.php" class="modal fade" id="myModal">
			<div class="modal-dialog">						
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>
						<h4><span class="glyphicon glyphicon-envelope"></span>Envoyer un mail à l'assuré</h4>
						<div class="input-group">
							<span class="input-group-addon">À : </span>
							<input id="email" type="text" class="form-control" name="email" value="<?php echo $mailAssure;?>" readonly>
						</div>			
						<div class="input-group">
							<span class="input-group-addon">Objet : </span>
							<input id="subject" type="text" class="form-control" name="subject" placeholder="Mettre le sujet de votre email ici ..."
							value="<?php echo MAIL_REQUEST_SUBJECT." [REF. $refDossier]";?>">
						</div>									
					</div>
					<div class="modal-body row" style="margin: 10px;">
						<?php $msgA = genererMessageAssure($nomAssure, $prenomAssure, $refDossier);?>
						<input type="hidden" name="mail_text[]" rows="9" class="col-sm-12" style="resize: none;" value="<?php echo $msgA[0];?>" readonly>
						<div><?php echo $msgA[0];?></div>
						<textarea name="mail_text[]" rows="10" placeholder="Tapez vos commentaires ici ..." class="col-sm-12" style="resize: none; margin-bottom: 10px"></textarea>
						<input type="hidden" name="mail_text[]" rows="5" class="col-sm-12" style="resize: none;" value="<?php echo $msgA[1];?>" readonly>
						<div><?php echo $msgA[1];?></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-send"></span>Envoyer</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
					</div>
				</div>
			</div>						
		</form>

		<div class="container">
			<div class="row container">
				<div id="panel-pjs" class="col-sm-4">
					<div class= "panel panel-default">
						<div class="panel-heading text-center">
							<h4><span class="glyphicon glyphicon-duplicate"></span>Liste des pièces justificatives</h4>
							<h4 class="row">
								<div class="col-sm-3" title="Nombre de pièces valides">
									<span class="glyphicon glyphicon-ok"></span>
									<?php echo nbPJsSelonStatut($link, "Valide", $codeDossier);?>
								</div>
								<div class="col-sm-3" title="Nombre de pièces invalides">
									<span class="glyphicon glyphicon-remove"></span>
									<?php echo nbPJsSelonStatut($link, "Invalide", $codeDossier);?>
								</div>
								<div class="col-sm-3" title="Nombre de pièces non-contrôlées">
									<span class="glyphicon glyphicon-file"></span>
									<?php echo nbPJsSelonStatut($link, "NULL", $codeDossier);?>
								</div>
								<div class="col-sm-3" title="Nombre de pièces totales">
									<span class="glyphicon glyphicon-list"></span>
									<?php echo nbPJsAvecCodeDossier($link, $codeDossier);?>
								</div>
							</h4>
						</div>
						<ul class="panel-body list-group">
						<?php
							// Récupération des justificatifs envoyés
							$result = recupererJustificatifs($link, $codeDossier);
							if ($result != NULL)
								$rows = mysqli_num_rows($result);
							else $rows = 0;
							for ($i = 0; $i < $rows; $i++){
								// Récupération du chemin de chaque fichier
								$justificatif = mysqli_fetch_array($result);
								$cheminFichier = "apercu.php?filepath=".$justificatif["CheminJ"];
								$nomFichier = strrchr($cheminFichier, '/');
								$nomFichier = substr($nomFichier, 1);
								$extension = strrchr($cheminFichier, '.');
								$extension = substr($extension, 1);
								echo "
								<li class='list-group-item'>
									<div class='row'>
										<div class='col-sm-2 image' onClick='updateViewer(\"$cheminFichier\");'>
											<img alt='icon $extension' class='icon' src='../img/icons/$extension-icon.png'>
										</div>
										<div class='col-sm-6 text' onClick='updateViewer(\"$cheminFichier\");'>
											<h5>$nomFichier</h5>
										</div>
										<div class='col-md-1 button'>
											<a href='traiter.php?statutPJ=Valide&codePJ=".$justificatif['CodeJ']."' class='"
												.classBoutonTraiterPJ(
													$justificatif["StatutJ"],
													"Valide",
													$codeT_dossier,
													$codeT,
													$statutDossier
												)
												."' role='button' title='Valider ce fichier'>
												<span class='glyphicon glyphicon-ok'></span>
											</a>
										</div>
										<div class='col-md-1 button'>
											<a href='traiter.php?statutPJ=Invalide&codePJ=".$justificatif['CodeJ']."' class='"
												.classBoutonTraiterPJ(
													$justificatif["StatutJ"],
													"Invalide",
													$codeT_dossier,
													$codeT,
													$statutDossier
												)
												."' role='button' title='Invalider ce fichier'>
												<span class='glyphicon glyphicon-remove'></span>
											</a>
										</div>
										<div class='col-md-1 button'>
											<a href='traiter.php?statutPJ=NULL&codePJ=".$justificatif['CodeJ']."' class='"
												.classBoutonTraiterPJ(
													$justificatif["StatutJ"],
													Null,
													$codeT_dossier,
													$codeT,
													$statutDossier
												)
												."' role='button' title='Retirer le statut de ce fichier'>
												<span class='glyphicon glyphicon-erase'></span>
											</a>
										</div>
									</div>
									<div class='row'>
										<div class='col-sm-12 matricule' onClick='updateViewer(\"$cheminFichier\");'>
								";

								if($justificatif['Matricule'] != Null) {
									echo "Contrôlée par : ".$justificatif['Matricule'];
								}
								else {
									echo "- Non contrôlée -";
								}

								echo "		</div>
										</div>
									</li>
								";
							}
						?>
						</ul>
					</div>
				</div>
				<div id="panel-apercu" class="col-sm-8">
					<div class= "panel panel-default">
						<div class="panel-heading text-center">
							<h4><span class="glyphicon glyphicon-picture"></span>Aperçu</h4>
							<h4 class="row">
								<div class="col-sm-12">
									<span id="nom-fichier-icon-apercu" class="glyphicon glyphicon-file"></span>
									<span id="nom-fichier-apercu">Sélectionnez un fichier dans la liste ...</span>
								</div>
							</h4>
						</div>
						<div class="panel-body">
							<iframe id="apercu" class="container-fluid" onload="gestionTailleApercu()">
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>