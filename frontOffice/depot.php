<?php
    session_start();                    // Démarrage de la session
    require_once("../fonctions.php");   // Récupération des fonctions

    /* Suppression des données de session du dernier dépôt (page d'enregistrement) */
    if(isset($_SESSION["MessageAssure"])
    && isset($_SESSION["MessageDossier"])
    && isset($_SESSION["MessageFichiers"])) {
        if(isset($_SESSION["MessageAssure"])) {unset($_SESSION["MessageAssure"]);}
        if(isset($_SESSION["MessageDossier"])) {unset($_SESSION["MessageDossier"]);}
        if(isset($_SESSION["MessageFichiers"])) {unset($_SESSION["MessageFichiers"]);}
        if(isset($_SESSION["RefD"])) {unset($_SESSION["RefD"]);}
    }

    /* S'il s'agit  d'une supression de session */
    if(isset($_GET["delete_session"])) {
        if(isset($_SESSION["Assure"])) unset($_SESSION["Assure"]);      
        if(isset($_SESSION["RefD"])) unset($_SESSION["RefD"]);
        redirigerVers('depot.php'); // Suppresion des valeurs du POST et du GET
    }

    /* Récupération de la connexion à la base de données */
    $link = connecterBD();

    /* Initialisation des variables */
    $repost = False;                // Ceci est un premier dépôt
    $repost_ok = False;             // Ceci n'est pas une demande d'authentification

    $msg_error_nir = False;         // Il n'y a pas de message d'erreur pour le NIR
    $msg_error_ref = False;         // Il n'y a pas de message d'erreur pour la référence du dossier
    $msg_error_nir_ref = False;     // Il n'y a pas de correspondance

    /* Récupération des valeurs contnus dans l'URL */
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
            $repost = True;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_nir"])) {
            $msg_error_nir = True;
            $repost = True;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_ref"])) {
            $msg_error_ref = True;
            $repost = True;  // Ceci n'est pas un premier dépôt
        }
        if(isset($_GET["msg_error_nir_ref"])) {
            $msg_error_nir_ref = True;
            $repost = True;  // Ceci n'est pas un premier dépôt
        }
    }

    if(isset($_POST["nir"])) {
        //Vérification de la correspondance entre le NIR et la référence du dossier
        if(estAssocie($_POST["nir"], $_POST["refD"], $link)) {
            $_SESSION["Assure"] = chercherAssureAvecNIR($_POST["nir"], $link);      
            $_SESSION["RefD"] = $_POST["refD"];
            $_SESSION["Assure"]["DateAM"] = chercherDossierAvecREF($_POST["refD"], $link)["DateAM"];
            redirigerVers('depot.php'); // Suppresion des valeurs du POST
        }     
        else {           
            if(!assureExiste($_POST["nir"], $link)) $msg .= "RefD=".$_POST["refD"]."&msg_error_nir=1";
            if(!dossierExiste($_POST["refD"], $link)) {
                if($msg != "") $msg .= "&";
                $msg = "msg_error_ref=1";
            }
            else {
                if($msg != "") $msg .= "&msg_error_nir_ref=1";
                else $msg .= "RefD=".$_POST["refD"]."&msg_error_nir_ref=1";
            }
        }

        redirigerVers('depot.php?'.$msg); // Passage des varaibles par la méthode GET
    }

    if(isset($_SESSION["Assure"]) && isset($_SESSION["RefD"])) {
        $NirAssure = $_SESSION["Assure"]["NirA"];
        $ReferenceDossier = $_SESSION["RefD"];
        $NomAssure = $_SESSION["Assure"]["NomA"];
        $PrenomAssure = $_SESSION["Assure"]["PrenomA"];
        $TelephoneAssure = $_SESSION["Assure"]["TelA"];
        $MailAssure = $_SESSION["Assure"]["MailA"];
        $DateAM = $_SESSION["Assure"]["DateAM"];
        $repost_ok = True;
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

        <!-- AFFICHAGE DU FORMULAIRE SI REFERENCE DE DOSSIER EXISTANTE -->
        <?php if ($repost || $repost_ok) : ?>
        <script>
            $(document).ready(function(){
                $("#form_panel").show();
            });
        </script>
        <?php endif ?>

        <title>PJPE - Dépôt des documents</title>
    </head>
    <body>
		<nav class="navbar navbar-default header welcome">
			<div class="container">
				<div class="navbar-header">
					<a href="../index.html"><h1>PJPE - Dépôt des documents</h1></a>
				</div>
			</div>
        </nav>
        
    <?php if (!$repost && !$repost_ok) : ?>
        <div class="container text-center" id="status">
			<div class="row">
				<div id="interim" class="col-sm-3 btn-status">
					<h2>Je suis interimaire et/ou j'ai un emploi saisonnier</h2>
				</div>
				<div id="cesu" class="col-sm-3 btn-status">
					<h2>Je suis indemnisé·e par CESU / PAJEMPLOI ou je suis assistant·e maternel·le</h2>
				</div>
				<div id="pole-emploi" class="col-sm-3 btn-status">
					<h2>Je suis indemnisé·e par Pôle Emploi</h2>
				</div>
				<div id="pole-emploiC" class="col-sm-3 btn-status">
					<h2>J'exerce une activité salariée avec un complément Pôle Emploi</h2>
				</div>
			</div>
			<div class="row">
				<div id="independant" class="col-sm-3 btn-status">
					<h2>Je suis travailleur indépendant et j'attends un enfant</h2>
				</div>
				<div id="intermit" class="col-sm-3 btn-status">
                    <h2>Je suis intermittent·e du spectacle</h2>
                </div>
				<div id="art-aut" class="col-sm-3 btn-status">
                    <h2>Je suis artiste auteur</h2>
                </div>
				<div id="salarie" class="col-sm-3 btn-status">
					<h2>Je suis salarié·e</h2>
				</div>
            </div>                
        </div>
    <?php endif ?>

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
                if($repost && !$msg_error_nir_ref && !$msg_error_nir && !$msg_error_ref) {
                    $title = "Veuillez saisir votre NIR";
                    $body = "Dans le but de vous authentifier, merci de saisir votre NIR";

                    if(isset($_GET["RefD"]) && $_GET["RefD"] == "")
                        $title .= ", ainsi que la référence du dossier qui vous a été délivrée.";
                        $body .= " et la référence de votre dossier";

                    $body .= " dans le champ précu à cet effet.";

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
                <div class="panel-heading">Formulaire d'envoi</div>
                <div class="panel-body">

                <?php if (!$repost) : ?>
                    <form enctype="multipart/form-data" method="POST" action="enregistrement.php">
                <?php else : ?>
                    <form method="POST" action="depot.php">
                <?php endif ?>
                        <div id="lien_ameli" class="container" style="display: none;">
                            <h3><span class="glyphicon glyphicon-link"></span>Merci de vous rendre sur ameli.fr</h3>
                            <p>Vous y trouverez la liste des feuillets nécessaires pour le traitement de votre demande.</p>
                            <a href="https://www.ameli.fr/haute-garonne/assure/remboursements/indemnites-journalieres/conge-maternite#text_85208" target="_blank" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-share-alt"></span>Aller sur ameli.fr</a>
                            <a href="https://www.ameli.fr/content/carnet-de-maternite-pour-les-femmes-chef-dentreprise-les-demarches-maternite" target="_blank" class="btn btn-lg btn-danger">
                                <span class="glyphicon glyphicon-file"></span>Consulter le document PDF
                            </a>
                        </div>

                        <div class="container" id="etat-civil">
                            <h3>Identification :</h3>

                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="nir" class="control-label">N° Sécurité sociale <span class="champ_obligatoire">(*)</span> :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
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

                            <?php if ($repost || $repost_ok) : ?>
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
                            <?php endif ?>
                            </div>

                        <?php if (!$repost || $repost_ok) : ?>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="nom" class="control-label">Nom <span class="champ_obligatoire">(*)</span> :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="nom" type="text" class="form-control" name="nom" placeholder="Nom" 
                                        <?php if(isset($NomAssure)) echo "value='$NomAssure'" ?> required>
                                    </div>
                                </div>        
                                <div class="col-sm-4">                
                                    <label for="prenom" class="control-label">Prénom <span class="champ_obligatoire">(*)</span> :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="prenom" type="text" class="form-control" name="prenom" placeholder="Prénom" 
                                        <?php if(isset($PrenomAssure)) echo "value='$PrenomAssure'" ?> required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="email" class="control-label">Adresse mail : </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                        <input id="email" type="email" class="form-control" name="email" placeholder="xyz@exemple.com"
                                        <?php if(isset($MailAssure)) echo "value='$MailAssure'" ?>>
                                    </div>
                                    <span class="note">La CPAM de la Haute-Garonne s'engage à ne pas utiliser votre adresse email à des fins commerciales.</span>
                                </div>
                                <div class="col-sm-4">
                                    <label for="tel" class="control-label">Numéro de téléphone : </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-phone-alt"></i></span>
                                        <input id="tel" type="tel" class="form-control" name="tel" placeholder="0#########"
                                        <?php if(isset($TelephoneAssure)) echo "value='$TelephoneAssure'" ?> >
                                    </div>
                                    <span class="note">La CPAM de la Haute-Garonne s'engage à ne pas utiliser votre numéro de téléphone à des fins commerciales.</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">                            
                                    <label for="date_arret" class="control-label">Je n'exerce plus d'activité depuis le : <span class="champ_obligatoire">(*)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        <input type="date" id="date_arret" class="form-control" name="date_arret" 
                                            <?php if(isset($DateAM)) echo "value='$DateAM' readonly" ?> required>
                                    </div>
                                </div>
                            </div>

                        <?php endif ?>
                        </div>

                        <div class="container" id="pj">
                        <?php if (!$repost || $repost_ok) : ?>
                            <h3>Pièces justificatives à déposer:</h3>
                            <div class="row pj salarie">
                                <div class="col-sm-12">
                                    <label for="ATT_SAL">Attestation de salaire délivrée par votre employeur <span class="champ_obligatoire">(*)</span> :</label>
                                    <input type="file" id="ATT_SAL" name="ATT_SAL[]" multiple>
                                </div>
                            </div>
                            <div class="row pj interim cesu pole-emploi pole-emploiC">
                                <div class="col-sm-12">
                                    <label for="BS">Les bulletins de salaire des <span id="nb_BS">12</span> mois précédant <span id="seuil_BS">l'arrêt de travail</span>  (de tous vos employeurs) <span class="champ_obligatoire">(*)</span> : </label>
                                    <input type="file" id="BS" name="BS[]" multiple>
                                </div>
                            </div>
                            <div class="row pj intermit">
                                <div class="col-sm-12">
                                    <label for="CACHET_GUSO">Cachet du GUSO <span class="champ_obligatoire">(*)</span> : </label>
                                    <input type="file" id="CACHET_GUSO" name="JUSTIF_SAL[]" multiple>
                                </div>
                            </div>
                            <div class="row pj art-aut">
                                <div class="col-sm-12">
                                    <label for="DOC_AGESSA">Imprimé délivré par AGESSA <span class="champ_obligatoire">(*)</span> : </label>
                                    <input type="file" id="DOC_AGESSA" name="PJ_IJ[]" multiple>
                                </div>
                            </div>
                            
                            
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-sm-12">
                                    <input type="checkbox" required> En cochant cette case, <span style="font-style: italic;">je certifie sur l'honneur l'exactitude des renseignements fournis. <span class="champ_obligatoire">(*)</span> </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="checkbox" required> En cochant cette case, <span style="font-style: italic;">je reconnais avoir pris connaissance et j'accepte les <a target="_blank" href="../documentation-juridique/cgu.html">conditions générales d'utilisation</a> du site. <span class="champ_obligatoire">(*)</span> </span>
                                </div>
                            </div>

                            <div id="champ_obligatoire" class="champ_obligatoire">                    
                                <p>(*) : Champs obligatoires</p>
                            </div>
                        <?php endif ?>

                            
                            <div class="row text-center" style="margin-top: 20px;">
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                    <?php if($repost_ok || $repost) : ?>
                                        <span class="glyphicon glyphicon-lock"></span>Valider
                                    <?php else : ?>
                                        <span class="glyphicon glyphicon-send"></span>Envoyer
                                    <?php endif ?>
                                    </button>
                                </div>
                            <!-- Affichage d'un bouton de retour s'il y a un message d'erreur -->
                            <?php if($repost_ok || $repost) : ?>
                                <div class="col-sm-4">
                                    <a href="depot.php?delete_session=1" class="btn btn-default btn-lg">
                                        <span class="glyphicon glyphicon-new-window"></span>
                                        Effectuer un nouveau dépot
                                    </a>
                                </div>
                            <?php else : ?>                                
                                <div class="col-sm-4">
                                    <a href="depot.php?RefD" class="btn btn-default btn-lg">
                                        <span class="glyphicon glyphicon-folder-open"></span>
                                        J'ai déjà effectué un premier envoi
                                    </a>
                                </div>
                            <?php endif ?>
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