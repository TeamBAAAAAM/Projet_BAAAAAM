<?php 
	session_start();
    require_once("../fonctions.php");
    // Connexion à la BD
	$link = connexionMySQL();

	// Récupération des données du technicien connecté
	if(isset($_SESSION["matricule"])){
		$matricule = $_SESSION["matricule"];
		$codeT = $_SESSION["codeT"];
		$nomT = $_SESSION["nomT"];
		$prenomT = $_SESSION["prenomT"];
	}

	//Changement de statut si un statut est indiqué dans l'URL
	if(isset($_GET["statut"])) {
		if(isset($_GET["codeD"])) $_SESSION["codeDossier"] = $_GET["codeD"];
		TraiterDossier($codeT, $_SESSION["codeDossier"], $_GET["statut"], $link);
		//Suppression des variables transmises par la méthode GET
		RedirigerVers("traiter.php");
	}
	// Récupération des données du dossier en cours de traitement
	else if(isset($_GET["codeD"])) {
		$_SESSION["codeDossier"] = $_GET["codeD"];
		//Suppression des variables transmises par la méthode GET
		RedirigerVers("traiter.php");
	}
		
	//S'il n'y a pas de code dossier
	if(!isset($_SESSION["codeDossier"])) {	
		RedirigerVers("accueil.php");
	}

	//Variables du dossier et de l'assuré
	$dossier = ChercherDossierTraiteAvecCodeD($_SESSION["codeDossier"], $link);
	$refDossier = $dossier["RefD"];
	$codeDossier = $dossier["CodeD"];
	$dateReception = $dossier["DateD"];
	$statutDossier = $dossier["StatutD"];
	$codeAssure = $dossier["CodeA"];
	$nirAssure = $dossier["NirA"];
	$nomAssure = $dossier["NomA"];
	$prenomAssure = $dossier["PrenomA"];
	$telephoneAssure = $dossier["TelA"];
	$mailAssure = $dossier["MailA"];
	$dateArretMaladie = $dossier["DateAM"];
	$codeT_dossier = $dossier["CodeT"];
	$matricule_dossier = $dossier["Matricule"];
	$nomT_dossier = $dossier["NomT"];
	$prenomT_dossier = $dossier["PrenomT"];
	$dateTraite = $dossier["DateTraiterD"];

	//Récupération des messages de l'assuré
	$messagesAssure = ListeMessages($codeAssure, $link);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="style.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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

        <title>PJPE - Réception des documents</title>
	</head>
	<body onLoad="MAJMessageAssure('<?php echo DEPOSITE_LINK."', '".FOOTER_EMAIL;?>', '<?php echo $refDossier;?>', null);">
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
						<li><a href="ma_corbeille.php"><span class="glyphicon glyphicon-folder-open"></span> Ma Corbeille</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right dropdown">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<?php echo("$prenomT $nomT "); ?><span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-menu-down"></span>
							</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								<li role="presentation"><a role="menuitem" href="#">Profil</a></li>
								<li role="presentation" class="divider"></li>
								<li role="presentation"><a role="menuitem" href="index.php">Se déconnecter</a></li>
							</ul>
						</li>						
					</ul>
				</div>
			</div>
		</nav>
		
		<?php
			if(isset($_POST['email'])) {
				if(EnvoyerMailDemandePJs($mailAssure, $_POST['subject'], $_POST['mail_text'])) {
					echo '
						<div class="container -fluid alert alert-success text-center">
							<strong>
								<span class="glyphicon glyphicon-ok"></span>Mail envoyé !
							</strong> Votre message a bien été envoyé.
						</div>	
					';

					$contenu = "À : $mailAssure\n";
					$contenu .= "Objet : ".$_POST['subject']."\n";
					$contenu .= "Message : ".$_POST['mail_text'];
					$contenu = explode("'", $contenu);
					$contenu = implode("\\'", $contenu);

					if(EnregistrerMessageAssure($codeAssure, $codeT, $contenu, $link)) {
						echo '
							<div class="container -fluid alert alert-success text-center">
								<strong>
									<span class="glyphicon glyphicon-ok"></span>Mail enregistré !
								</strong> Votre message a bien été enregistré.
							</div>		
						';
						
						//Reconnexion à la BD en cas de réussite de l'enregistrement
						mysqli_close($link);
						$link = connexionMySQL();

						//Récupération des messages de l'assuré
						$messagesAssure = ListeMessages($codeAssure, $link);
					}
					else {
						echo '				  
							<div class="container -fluid alert alert-danger text-center">
								<strong>
									<span class="glyphicon glyphicon-remove"></span>Erreur lors de l\'enregistrement !
								</strong> Votre message n\'a pas pu être enregistré !	
							</div>				
						';
					}
				}
				else {
					echo '				  
						<div class="container -fluid alert alert-danger text-center">
							<strong>
								<span class="glyphicon glyphicon-remove"></span>Erreur lors de l\'envoi !
							</strong> Votre message n\'a pas pu être envoyé !
						</div>		
					';
				}

				unset($_POST['subject']); 
				unset($_POST['mail_text']);
				unset($_POST['email']);
			}
		?>

		<div class="container">
			<div class="row">
				<div id="panel-dossier" class="col-sm-6">
					<div class="container-fluid panel panel-default">
						<div class="panel-body">
							<h4>DOSSIER No <?php echo $refDossier;?></h4>
							<h5>Date de réception :  <?php echo $dateReception;?></h5>
							<h5>Suivi par :  <?php echo "$prenomT_dossier $nomT_dossier ($matricule_dossier)";?></h5>
							<?php if ($statutDossier != "En cours") echo "<h5>Traité le :   $dateTraite</h5>"; else echo "<h5>Depuis le :   $dateTraite</h5>"; ?>
						</div>
					</div>
				</div>
				<div id="panel-assure" class="col-sm-6">
					<div class="container-fluid panel panel-default">
						<div class="panel-body">
							<h4>NIR : <?php echo $nirAssure;?></h4>
							<h5><?php echo "$nomAssure $prenomAssure";?></h5>
							<h5>En arrêt de travail depuis le : <?php echo $dateArretMaladie;?></h5>
							<h5 style="margin: 8px 0px;">
								<?php
									if($telephoneAssure != "") {
										echo "<span style='margin-left: 0px;' class='glyphicon glyphicon-phone-alt'></span>$telephoneAssure / ";
									}
									if($mailAssure != "") {
										echo "<span style='margin-left: 0px;' class='glyphicon glyphicon-envelope'></span>$mailAssure</span>";
									}
								?>
							</h5>
						</div>
					</div>
				</div>
			<div>
			<div class="row">
				<div id="panel-statut" class="col-sm-6">
					<div class= "container-fluid panel panel-default">	
						<div class="panel-body">
							<div class="row" style="margin-bottom:0px;">
								<div class="col-sm-2 text-center">
									<span class="titre">Statut</span>
								</div>
								<div class="col-sm-10">
									<div class="btn-group btn-group-justified">
										<a href="traiter.php?statut=En cours"
											class="<?php ClassBoutonTraiter($statutDossier, "En cours", $codeT_dossier, $codeT);?>"
											role="button">En cours</a>
										<a href="traiter.php?statut=Classé sans suite"
											class="<?php ClassBoutonTraiter($statutDossier, "Classé sans suite", $codeT_dossier, $codeT);?>" 
											role="button">Classé sans suite</a>
										<a href="traiter.php?statut=Terminé"
											class="<?php ClassBoutonTraiter($statutDossier, "Terminé", $codeT_dossier, $codeT);?>"
											role="button">Terminé</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class= "container-fluid panel panel-default">	
						<div class="panel-body">
							<div class="row" style="margin-bottom:0px;">
								<div class="col-sm-12 btn-group btn-group-justified">
    								<div class="btn-group">
										<button type="button" class="btn btn-default<?php if($codeT != $codeT_dossier) echo " disabled";?>" 
										data-toggle="modal" data-target="#myModal">
											<span class="glyphicon glyphicon-send"></span> Envoyer un mail à l'assuré
										</button>
									</div>

    								<div class="btn-group">
										<button type="button" class="btn btn-default<?php if($messagesAssure == null) echo " disabled";?>" 
										data-toggle="modal" data-target="#myModal2"
											><span class="glyphicon glyphicon-th-list"></span> Consulter la liste des messages
										</button>
									</div>
								</div>
							</div>

							<!-- Modal pour l'affichage de la liste des messages -->
							<div id="myModal2" class="modal fade" role="dialog">
								<div class="modal-dialog modal-lg">

									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Consulter les messages envoyés</h4>
										</div>
										<div class="modal-body">
											<?php
												if($messagesAssure == null) {
													echo '
														<div class="alert alert-warning text-center">
															<strong>
																<span class="glyphicon glyphicon-floppy-disk"></span>Aucune correspondance !
															</strong> Aucun message enregistré n\'est affilié à cet·te assuré·e.
														</div>
													';
												}

												$message = mysqli_fetch_array($messagesAssure);
												$i = 1;
												while ($message != null) {
													$contenuMessage = ExtraireMessage($message["Contenu"]);
													echo '
														<button type="button"
															class="btn btn-primary btn-block" onclick=\'$("#m'.$i.'").toggle(500);\'>
															<div class="row" style="margin-bottom: 0px">
																<div class="col-lg-8" style="text-align: left;">
																	<h5><strong><span class="glyphicon glyphicon-chevron-right"></span>'.$contenuMessage[1].'</strong></h5>
																</div>
																<div class="col-lg-3">
																	<h5><span class="glyphicon glyphicon-barcode"></span>'.$message["Matricule"]." | <span class='glyphicon glyphicon-time'></span>".dateFR($message["DateEnvoiM"]).'</h5>
																</div>
															</div> 
														</button>
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
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<form method="POST" action="traiter.php" class="modal fade" id="myModal" role="dialog">
								<div class="modal-dialog">						
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4>Envoyer un mail à l'assuré</h4>
											<div class="container" style="padding-bottom: 10px">
												Type de demande : 
												<input id="cb1" onChange="MAJMessageAssure('<?php echo DEPOSITE_LINK."', '".FOOTER_EMAIL;?>', '<?php echo $refDossier;?>', null);" type="checkbox"> Pièces manquantes
												<input id="cb2" onChange="MAJMessageAssure('<?php echo DEPOSITE_LINK."', '".FOOTER_EMAIL;?>', '<?php echo $refDossier;?>', null);" type="checkbox"> Pièces illisibles
												<input id="cb3" onChange="MAJMessageAssure('<?php echo DEPOSITE_LINK."', '".FOOTER_EMAIL;?>', '<?php echo $refDossier;?>', null);" type="checkbox"> Pièces invalides
											</div>
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
										<div class="modal-body">
											<textarea id="mail_text" name="mail_text" rows="15"></textarea>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-info"><span class="glyphicon glyphicon-send"></span>Envoyer</button>
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>						
							</form>
						</div>
						
					</div>
					
				</div>
			</div>

			<div class="row">
				<div id="panel-pjs" class="col-sm-4">
					<div class= "panel panel-primary">
						<div class="panel-heading titre text-center">Liste des pièces justificatives</div>
						<ul class="panel-body list-group">
						<?php
							$result = RecupererPJ($link, $codeDossier);
							if ($result != NULL)
								$rows = mysqli_num_rows($result);
							else $rows = 0;
                            for ($i = 0; $i < $rows; $i++){
                                $justificatif = mysqli_fetch_array($result);
								$cheminFichier = $justificatif["CheminJ"];
                                $nomFichier = strrchr($cheminFichier, '/');
                                $nomFichier = substr($nomFichier, 1);
                                $extension = strrchr($cheminFichier, '.');
                                $extension = substr($extension, 1);
                                //$mnemonique = $justificatif["Mnemonique"];
                                echo("<li class='list-group-item' onClick='changePathViewer(\"$cheminFichier\")'><h5><img class='icon icon-$extension'>$nomFichier</h5></li>");
                            }
                        ?>
                        </ul>
					</div>
				</div>
				<div id="panel-apercu" class="col-sm-8">
					<div class= "panel panel-default">
						<div class="panel-heading titre text-center">Aperçu</div>
						<div class="panel-body">
							<embed id="apercu" class="panel-body">
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>	
</html>