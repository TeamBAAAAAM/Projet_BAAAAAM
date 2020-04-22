<?php
    session_start();

    require_once("../fonctions.php");
    
    //Ouverture de la connexion à la BD
    $link = connexionMySQL();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="style.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- Pour l'exportation en PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
        <script src="script.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PJPE - Enregistrement des documents</title>
    </head>    
    <body>
        <nav class="navbar navbar-default header welcome">
            <div class="container">
                <div class="navbar-header">
                    <a href="../index.html"><h1>PJPE</h1></a>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="jumbotron">
                <h1>Confirmation de réception</h1>
                
                <div class='alert alert-warning ignore'>
                    <h2>&#9888; Pensez à conserver cette confirmation</h2>
                    <p>Elle pourra vous être demandée ultérieurement pour vous identifier.</p>
                    <p>
                        <button type="button" class="btn btn-warning" onClick="imprimerPage();">
                            <strong>&#128438;</strong> Imprimer tout de suite
                        </button>
                    </p>
                </div>

                <hr>

                <div class="container-fluid">
                    <?php
                        if(empty($_POST) && empty($_GET)) RedirigerVers("depot.php");

                        // Si l'assuré n'existe pas déjà dans la BD
                        if(!AssureExiste($_POST["nir"], $link)) {
                            // Enregistrement de l'assuré dans la BD
                            if(!EnregistrerAssure($_POST["nir"], $_POST["nom"], $_POST["prenom"],  $_POST["tel"], $_POST["email"], $link)) { // Message d'échec
                                echo "<div class='alert alert-danger'><strong>Alerte !</strong> Échec de l'enregistrement de l'assuré !</div>";
                            } else {
                                //Création du dossier d'un assuré dont le nom est son NIR (en local)
                                if(!CreerDossierNIR($_POST["nir"])) { // Message d'échec
                                    echo "<div class='alert alert-danger'><strong>Alerte !</strong> Échec de la création du dossier du NIR de l'assuré' !</div>";
                                } else { // Message de réussite
                                    $_SESSION["MessageAssure"] = "
                                        <ul class='list-group'>
                                            <li class='list-group-item list-group-item-success'> 
                                                <h3>Enregistrement de vos informations</h3>
                                            </li>";                   
                                            
                                    if(isset($_POST["nir"])) {$_SESSION["MessageAssure"] .= "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-barcode'></span>NIR : ".$_POST["nir"]."
                                        </li>";
                                    }
                                    
                                    if(isset($_POST["nom"])) {$_SESSION["MessageAssure"] .= "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-user'></span>Nom : ".$_POST["nom"]."
                                        </li>";
                                    }
                                        
                                    if(isset($_POST["prenom"])) {$_SESSION["MessageAssure"] .= "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-user'></span>Prénom : ".$_POST["prenom"]."
                                        </li>";
                                    }
                                    
                                    if(isset($_POST["tel"]) && $_POST["tel"] != "") {
                                        $_SESSION["MessageAssure"] .= "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-envelope'></span>Tel : ".$_POST["tel"]."
                                        </li>";
                                    }
                                    if(isset($_POST["email"]) && $_POST["email"] != "") {
                                        $_SESSION["MessageAssure"] .= "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-phone-alt'></span>Email : ".$_POST["email"]."
                                        </li>";
                                    }
                                    $_SESSION["MessageAssure"] .=  "</ul>";
                                }
                            }                                            
                        } else { // Message d'information si l'assuré est déjà enregistré
                            $_SESSION["MessageAssure"] = "
                                    <ul class='list-group'>
                                        <li class='list-group-item list-group-item-success'> 
                                            <h3>Enregistrement de vos informations</h3>
                                        </li>
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-ok'></span>Vous avez déjà été enregistré.
                                        </li>
                                    </ul>";
                        }

                        // Récupération des données de l'assuré dans la BD
                        $assure = ChercherAssureAvecNIR($_POST["nir"], $link);

                        //Si une référence de dossier n'a pas encore été enregistré
                        if(!isset($_SESSION["RefD"])) {
                            $_SESSION["RefD"] = GenererReferenceDossier(8, $link);
                            // Enregistrement du dossier dans la BD
                            if(!EnregistrerDossier($assure["CodeA"], $_POST["date_arret"], $_SESSION["RefD"], $link)) { // Message d'échec
                                echo "<div class='alert alert-danger'><strong>Alerte !</strong> Échec de l'enregistrement du dossier dans la base de données !</div>";
                            } else {
                                //Création du dossier de l'arrêt maladie dont le nom est sa référence (en local)
                                if(!CreerDossierAM($_SESSION["RefD"], $assure["NirA"])) { // Message d'échec
                                    echo "<div class='alert alert-danger'><strong>Alerte !</strong> Échec lors de la création du dossier !</div>";
                                } else {
                                    // Récupération des données du dossier dans la BD
                                    $dossier = ChercherDossierAvecREF($_SESSION["RefD"], $link);
                                    // Message de réussite
                                    $_SESSION["MessageDossier"] = "
                                        <ul class='list-group'>
                                            <li class='list-group-item list-group-item-success'> 
                                                <h3>Enregistrement de votre dossier</h3>
                                            </li>
                                            <li class='list-group-item list-group-item-default'>
                                                <span class='glyphicon glyphicon-user'></span>". $dossier["PrenomA"]." ".$dossier["NomA"]."
                                                <span class='badge ignore'>
                                                    Affilié au NIR ".$dossier["NirA"]."
                                                </span>
                                            </li>
                                            <li class='list-group-item list-group-item-default'>                           
                                                <span class='glyphicon glyphicon-folder-close'></span>Référence du dossier : <strong>".$dossier["RefD"]."</strong>
                                                <span class='label label-warning label_enregistrement'>
                                                    &#9888; <strong>À conserver</strong>
                                                </span>
                                            </li>
                                            <li class='list-group-item list-group-item-default'>              
                                                <span class='glyphicon glyphicon-calendar'></span>Ce dossier a été créé le : <strong>".$dossier["DateD"]."</strong>
                                            </li>
                                        </ul>
                                    ";
                                }
                            }                           
                        } else { // Message d'information si le dossier existe déjà
                            $_SESSION["MessageDossier"] = "
                                        <ul class='list-group'>
                                            <li class='list-group-item list-group-item-success'> 
                                                <h3>Votre dossier d'arrêt maladie</h3>
                                            </li>
                                            <li class='list-group-item list-group-item-default'>
                                                <span class='glyphicon glyphicon-ok'></span> Votre dossier existe déjà. 
                                            </li>
                                        </ul>";
                        }
                        // Récupération des données du dossier dans la BD
                        $dossier = ChercherDossierAvecREF($_SESSION["RefD"], $link);
                            
                        if(isset($_SESSION["MessageAssure"])) {echo($_SESSION["MessageAssure"]);}
                        if(isset($_SESSION["MessageDossier"])) {echo($_SESSION["MessageDossier"]);}
                        
                        // Enregistrement des PJ
                        $resultats = EnregistrerFichiers($_FILES, $_SESSION["RefD"], $dossier["NirA"], $link);                        
                        if($resultats != null) { // Message de réussite
                            echo "
                            <ul class='list-group'>
                                <li class='list-group-item panel_header_fichier'>   
                                    <h3>Enregistrement de vos fichiers</h3>
                                </li>
                            ";

                            foreach($resultats as $resultat) {
                                if($resultat[0]) { //Si l'envoi a réussi
                                    echo "
                                        <li class='list-group-item list-group-item-default'>
                                                <span class='glyphicon glyphicon-save-file'></span>
                                                $resultat[1]
                                                <span class='label label-success label_enregistrement'>
                                                    &#10004; <strong>Enregistré</strong>
                                                </span>
                                                <span class='badge'>
                                                    $resultat[2]
                                                </span>
                                        </li>
                                    ";
                                } else {
                                    echo "
                                        <li class='list-group-item list-group-item-default'>
                                            <span class='glyphicon glyphicon-save-file'></span>
                                            $resultat[1] 
                                            <span class='label label-danger label_enregistrement'>
                                                &#10006; <strong>Échec</strong>
                                            </span>
                                            <span class='badge'>
                                                $resultat[2]
                                            </span>
                                        </li>
                                    ";
                                }
                            }       
                            echo "
                                </ul>
                            ";                 
                        }
                    ?>
                </div>
                <hr>
                <div class="container text-center ignore"> 
                    <a type="button" class="btn btn-info" target="_blank" href="<?php echo 'mailConfirmation.php?MailA='.($assure['MailA']);?>">
                        <strong>&#9993;</strong> Envoyer par mail
                    </a>               
                    <button type="button" class="btn btn-warning" onClick="imprimerPage();">
                        <strong>&#128438;</strong> Imprimer
                    </button>             
                    <a type="button" class="btn btn-danger" href="../">
                        <strong>&#9111;</strong> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
        
        <footer class="container-fluid text-center ignore">
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
                                <img src="../img/logo_oups.svg">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="title_footer">Contact</div>
                    <div class="row">
                        <iframe class="col-sm-5" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d23113.985644999426!2d1.4384851395507818!3d43.601373400000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebc91ae4a5ba3%3A0x5d4ac376bccc8d50!2sCPAM%20de%20la%20Haute-Garonne!5e0!3m2!1sfr!2sfr!4v1580058891942!5m2!1sfr!2sfr" style="border:0;" allowfullscreen=""></iframe>	
                        <div class="col-sm-7 text-left">
                            <div class="col-sm-12">Caisse Primaire d'Assurance Maladie</div>
                            <div class="col-sm-12">3, Boulevard du Professeur Léopold Escande</div>	
                            <div class="col-sm-12">Haute-Garonne (31) - 31093 Toulouse</div>													
                            <div class="col-sm-12">	
                                <img src="../img/num_tel.png" height="50px">
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

<?php    
    //Fermeture de la connexion à la BD
    mysqli_close($link);
?>