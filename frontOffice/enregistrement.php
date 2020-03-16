<!DOCTYPE html>
<?php
    require_once("../fonctions.php");
?>
<html lang="fr">
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="style.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Message de confirmation</title>
    </head>    
    <body>
        <nav class="navbar navbar-default header welcome">
            <div class="container">
                <div class="navbar-header">
                    <a href="../index.html"><h1>Message de confirmation</h1></a>
                </div>
            </div>
        </nav>
        
        <div class="container-fluid">
            <div class="jumbotron">
                <h1>Confirmation de réception</h1>
                <div id="info-status" class="well well-lg">
                    <?php
                        if(
                            AjouterAssure(
                                $_POST["nir"],
                                $_POST["nom"],
                                $_POST["prenom"], 
                                $tel = $_POST["tel"], 
                                $email = $_POST["email"])){
                            echo("<h3>Vos informations ont été correctement enregistrées !</h3>");
                            if(isset($_POST["nir"])){echo("<p>NIR : ".$_POST["nir"]."</p>");}
                            if(isset($_POST["nom"])){echo("<p>Nom : ".$_POST["nom"]."</p>");}
                            if(isset($_POST["prenom"])){echo("<p>Prénom : ".$_POST["prenom"]."</p>");}
                            if(isset($_POST["tel"])){echo("<p>Tel : ".$_POST["tel"]."</p>");}
                            if(isset($_POST["email"])){echo("<p>Email : ".$_POST["email"]."</p>");}
                        }
                        
                        $assure = ChercherAssureAvecNIR($_POST["nir"]);
                        $result = EnregistrerDossier($_POST["date_arret"], $assure['CodeA']);
                        
                        echo($result[0]);
                        if($result[0]){
                            echo("<h3>Enregitrement de votre dossier : </h3>");
                            echo("<p>".$_POST["prenom"]." ".$_POST["nom"]." (".$_POST["nir"].")</p>");                            
                            echo("<p>Référence du dossier : ".$result[1]."</p>");                       
                            echo("<p>Date de réception : ".$result[2]."</p>");
                        }
                        
                        //$att_sal = $_FILES["ATT_SAL"];
                        //$bs = $_FILES["BS"];
                        //$justif_sal = $_FILES["JUSTIF_SAL"];
                        //$pj_ij = $_FILES["PJ_IJ"];
                        /*
                        echo($refD."\n".$nom."\n".$prenom."\n".$email."\n".$date_arret);
                        */         
                    ?>
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
                            <a href="#" target="_blank">Conditions Générales d'Utilisation</a>
                    </div>				
                    <div class="col-sm-3">
                            <a href="#" target="_blank">Politique de traitement des données personnelles</a>
                    </div>				
                    <div class="col-sm-3">
                            <a href="#" target="_blank">Politique de traitement des cookies</a>
                    </div>				
                    <div class="col-sm-3">
                            <a href="#" target="_blank">Mentions Légales</a>
                    </div>
            </div>
            <div id="copyright" class="row">© 2020 Copyright - Tous droits réservés : Team BAAAAAM</div>
        </footer>
    </body>
</html>
