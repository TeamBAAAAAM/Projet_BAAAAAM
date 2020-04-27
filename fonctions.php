<?php
/******************************************************************/

/*   FICHIER CONTENANT LES FONCTIONS PHP UTILISÉES POUR LE SITE   */

/******************************************************************/

// Rangées en 3 groupes de fonctions : générales, pour front office et pour back office
// Puis par ordre alphabétique

/*------------------------------------------------------------------
 	VARIABLES GLOBALES DE CONNEXION À LA BASE DE DONNÉES
------------------------------------------------------------------*/

define("HOST", "localhost");    // Nom du host
define("USER", "root");         // Nom d'utilisateur
define("PWD_MYSQL", "");        // Mot de passe
define("BD_MYSQL", "bd_cpam");  // Nom de la base de données
define("PORT", "3306");         // Nom du port de connexion

/*------------------------------------------------------------------
 	VARIABLE GLOBALE DU CHEMIN VERS L'ESPACE DE STOCKAGE DES PIECES
------------------------------------------------------------------*/

define("STORAGE_PATH", "piecesJustificatives");     // N.B. : À partir de la racine

/*------------------------------------------------------------------
 	VARIABLES GLOBALES POUR GÉNÉRER LE MESSAGE DE DEMANDE DE PIECES
------------------------------------------------------------------*/

define("MAIL_REQUEST_SUBJECT", "PJPE - Demande de pièces justificatives");         // Objet du message
define("DEPOSITE_LINK", "http://".$_SERVER['HTTP_HOST']."/frontOffice/depot.php"); // Lien vers le formulaire de dépôt
define("FOOTER_EMAIL", "Merci de ne pas répondre à ce message.");                  // Message du footer


/*------------------------------------------------------------------
 	FONCTIONS GÉNÉRALES
------------------------------------------------------------------*/

/* Connecte à la base de données */
function connecterBD() {
    // Connexion à la base données avec une lecture encodée en UTF-8
    // (NB : Renseigner les variables de connexion plus haut)
    $link = mysqli_connect(HOST, USER, PWD_MYSQL, BD_MYSQL, PORT);
    mysqli_query($link, 'SET NAMES utf8');

    // Vérification de la connexion
    if ($link == NULL) { // Si la connexion a échoué
        echo "Erreur : Impossible de se connecter à MySQL." . "<br>";
        echo "Errno de débogage : " . mysqli_connect_errno() . "<br>";
        echo "Erreur de débogage : " . mysqli_connect_error() . "<br>";
        exit;
    } else {
        if (mysqli_select_db($link, BD_MYSQL) == null) {
            echo ("<p> Vérifier que la base de données est bien sur MariaDB </p>");
            return null;
        }
    }

    return $link;
}

/* Génère un message de titre '$title', de contenu '$body', de glyphicon '$icon' et ayant un type Boostrap */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function genererMessage($title, $body, $icon, $type) {
    echo "
        <div class='alert alert-$type'>
            <h3>
                <strong class='alert-title'>
                    <span class='glyphicon glyphicon-$icon'></span>$title
                </strong>
            </h3>
            <p>
                $body
            </p>
        </div>
    ";
}

/* Redirige vers la page '$nomPage' */
function redirigerVers($nomPage) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$nomPage");
    
    exit;
}


/*------------------------------------------------------------------
 	FONCTIONS : FRONT OFFICE (INTERFACE ASSURE)
------------------------------------------------------------------*/

/* Vérifie si '$nir' correspond au NIR d'un assuré déjà enregistré dans la base de données */
/* => [Vrai si le NIR est reconnu, Faux sinon] */
function assureExiste($nir, $link) {
    $query = "SELECT * FROM Assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Renvoie les informations de l'assuré ayant le NIR '$nir' sous la forme d'une liste */
/* => [Objet de type array si l'assuré est déjà enregistré, NULL sinon] */
function chercherAssureAvecNIR($nir, $link) {
    $query = "SELECT * FROM Assure WHERE NirA = '$nir'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations d'un dossier ayant pour référence '$ref' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherDossierAvecREF($ref, $link) {
    $query = "SELECT * FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie les informations correspondant au mnémonique '$mnemonique' */
/* => [Objet de type array si le mnémonique existe, NULL sinon] */
function chercherObjetMnemoAvecMnemo($mnemonique, $link) {
    $query = "SELECT * FROM Listemnemonique "
            ."WHERE Mnemonique = '$mnemonique'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la référence du dossier ayant pour code '$codeDossier' sous la forme d'une liste */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherREFAvecCodeD($codeDossier, $link) {
    $query = "SELECT RefD FROM Assure A, Dossier D "
            ."WHERE A.CodeA = D.CodeA AND CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Crée le répertoire ayant pour nom '$ref' à l'emplacement 'STORAGE_PATH/$nirA' (cf. haut de page) */
/* => [Vrai si le répertoire de l'assuré a bien été créé, Faux sinon] */
function creerRepertoireAM($ref, $nir) {
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $nir . "/" . $ref;
    return mkdir($dirname);
}

/* Crée un répertoire ayant pour nom '$nir' à l'emplacement 'STORAGE_PATH' (cf. haut de page) */
/* => [Vrai si le répertoire de l'assuré a bien été créé, Faux sinon] */
function creerRepertoireNIR($nir) {
    $dirname = dirname("../" . STORAGE_PATH) . "/" . basename("../" . STORAGE_PATH) . "/" . $nir;
    return mkdir($dirname);
}

/* Vérifie si le dossier de référence '$ref' existe déjà dans la base de données */
/* => [Vrai si la référence de dossier est reconnue, Faux sinon] */
function dossierExiste($ref, $link) {
    $query = "SELECT RefD FROM Dossier WHERE RefD = '$ref'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Enregistre les données d'un assuré dans la base de données */
/* => [Vrai si les données de l'assuré ont bien été enregistrées, Faux sinon] */
function enregistrerAssure($nir, $nom, $prenom, $tel, $mail, $link) {
    $keys = ""; $values = "";
    if ($nir != NULL) {
        $keys .= "NirA, "; $values .= "'$nir', ";
    }
    if ($nom != NULL) {
        $keys .= "NomA, "; $values .= "'$nom', ";
    }
    if ($prenom != NULL) {
        $keys .= "PrenomA, "; $values .= "'$prenom', ";
    }
    if ($tel != NULL) {
        $tel = explode(" ", $tel); 
        $tel = implode($tel);
        $keys .= "TelA, "; $values .= "'$tel', ";
    }
    if ($mail != NULL) {
        $keys .= "MailA, "; $values .= "'$mail', ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO assure($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Enregistre les informations concernant un nouveau dossier */
/* => [Vrai si les informations du dossier ont bien été enregistrées, Faux sinon] */
function enregistrerDossier($codeAssure, $dateAM, $ref, $link) {
    $keys = ""; $values = "";
    $keys .= "RefD, "; $values .= "'" . $ref . "', ";
    $keys .= "DateAM, "; $values .= "'" . $dateAM . "', ";
    $keys .= "CodeA, "; $values .= $codeAssure . ", ";

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO dossier($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Enregistre les informations concernant un nouveau fichier */
/* => [Vrai si les informations du fichier ont bien été enregistrées, Faux sinon] */
function enregistrerFichier($cheminJustificatif, $codeDossier, $codeAssure, $codeMnemonique, $link) {
    $keys = ""; $values = "";
    if ($cheminJustificatif != NULL) {
        $keys .= "CheminJ, "; $values .= "'" . $cheminJustificatif . "', ";
    }
    if ($codeDossier != NULL) {
        $keys .= "CodeD, "; $values .= $codeDossier . ", ";
    }
    if ($codeAssure != NULL) {
        $keys .= "CodeA, "; $values .= $codeAssure . ", ";
    }
    if ($codeMnemonique != NULL) {
        $keys .= "CodeM, "; $values .= "'" . $codeMnemonique . "', ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO justificatif($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Enregistre les fichiers de '$listeFichiers' à l'emplacement 'STORAGE_PATH/$nirA/$refD' */
/* => [Liste(A : Booléen, B : Chaîne de caractères, C : Chaîne de caractères)]  */
/*      => A = Vrai si l'enregistrement a réussi, Faux sinon                    */
/*      => B = Nom du fichier téléchargé                                        */
/*      => C = Mnémonique complet affilié au fichier                            */
function enregistrerFichiers($listeFichiers, $ref, $nir, $link) {
    $resultats = array();
    foreach ($listeFichiers as $key => $fichier) {
        $j = 1;
        for ($i = 0; $i < count($fichier['name']); $i++) {
            if ($fichier['name'][$i] != "") {
                $file = basename($fichier['name'][$i]);

                $target_dir = "../" . STORAGE_PATH . "/" . $nir . "/" . $ref;
                $ext = strtolower(pathinfo($file)['extension']);

                $cheminJustificatif = "$target_dir/$key" . "_$j.$ext";
                $codeAssure = chercherAssureAvecNIR($nir, $link)["CodeA"];
                $codeDossier = chercherDossierAvecREF($ref, $link)["CodeD"];
                $mnemonique = chercherObjetMnemoAvecMnemo($key, $link);
                $designation = $mnemonique["Designation"] . " No. " . $j;
                if (!fichierExiste($link, $cheminJustificatif)){
                    if (enregistrerFichier($cheminJustificatif, $codeDossier, $codeAssure, $mnemonique["CodeM"], $link)) {
                        if (move_uploaded_file($fichier['tmp_name'][$i], $cheminJustificatif)) {
                            $resultats[] = array(TRUE, $file, $designation);
                            $j++;
                        } else {
                            $resultats[] = array(FALSE, $file, $designation);
                        }
                    } else {
                        $resultats[] = array(FALSE, $file, $designation);
                    }
                }
            }
        }
    }
    return $resultats;
}

/* Vérifie si la référence d'un dossier '$ref' est bien associée à un NIR '$nir' */
/* => [Vrai s'il y a bien une correspondance entre le NIR '$nirA' et la référence '$redD', Faux sinon] */
function estAssocie($nir, $ref, $link) {
    $query = "SELECT a.* "
            ."FROM Assure a, Dossier d  "
            ."WHERE a.NirA = '$nir' "
            ."AND d.CodeA = a.CodeA "
            ."AND d.RefD = '$ref'" ;
    $result = mysqli_query($link, $query);
        
    return (mysqli_fetch_array($result) != NULL);
}

/* Genère une référence unique de taille '$nbChar' d'un dossier */
/* => [Chaine de caractères de taille '$nbChar'] */
function genererReferenceDossier($nbChar, $link) {
    $listeChar = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    do {
        $ref = "";
        for($i = 0 ; $i < $nbChar ; $i++) {
            $ref .= $listeChar[rand(0, strlen($listeChar) - 1)]; //Sélection d'un caractère aléatoire
        }
    } while(dossierExiste($ref, $link));

    return $ref;
}

/* Vérifie si le chemin $chemin exite déjà dans la base de données */
/* => [Vrai si le chemin est déjà enregistré, Faux sinon] */
function fichierExiste($link, $chemin){
    $query = "SELECT CheminJ FROM justificatif WHERE CheminJ = '$chemin'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}


/*------------------------------------------------------------------
 	FONCTIONS : BACK OFFICE (INTERFACE TECHNICIEN)
------------------------------------------------------------------*/

/* Vérifie que le technicien de matricule '$matricule' possède bien le mot de passe '$mdpt' */
/* => [Vrai si le technicien est bien authentifié, Faux sinon] */
function authentifierTechnicien($link, $matricule, $mdpT) {
    $query = "SELECT Matricule, MdpT "
            ."FROM Technicien T "
            ."WHERE Matricule = '$matricule' "
            ."AND MdpT = '$mdpT'";
    $result = mysqli_query($link, $query);

    return (mysqli_fetch_array($result) != NULL);
}

/* Change le statut du dossier de code '$codeDossier' en '$statut' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function changerStatutDossier($link, $codeDossier, $statut) {
    $query = "UPDATE dossier SET StatutD = '$statut' WHERE CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie les informations d'un dossier en cours de traitement ou traité ayant pour code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function chercherDossierTraiteAvecCodeD($codeDossier, $link) {
    $query = "SELECT * FROM Assure A, Dossier D, Traiter Tr, Technicien T "
            ."WHERE A.CodeA = D.CodeA AND D.CodeD = $codeDossier "
            ."AND D.CodeD = Tr.CodeD AND T.CodeT = Tr.CodeT "
            ."AND Tr.DateTraiterD = (SELECT MAX(DateTraiterD) "
                                    ."FROM Traiter "
                                    ."WHERE CodeD = $codeDossier)";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la classe CSS correspondante pour chaque bouton du fichier traiter.php           */
/* => Effectue seulement un affichage (pas de valeur de retour)                             */
/* => Active ou désactive un bouton permettant de modifier le statut d'un dossier           */
/* => $sessionValue correspond au statut du dossier en cours ($_SESSION['statut'])          */
/* => $buttonValue est soit 'En cours', soit 'Classé sans suite' ou bien 'Terminé'          */
/* => $codeT_dossier est le code du technicien qui est actuellement connecté                */
/* => Selon si le dossier est dans sa corbeille ou pas, il pourra ou non modifier           */
/* => le statut du dossier courant                                                          */
function classBoutonTraiter($sessionValue, $buttonValue, $codeT_dossier, $codeT_courant) {
    switch($sessionValue) {
        case "En cours":
            if($codeT_dossier == $codeT_courant) {
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn btn-default";
                        break;
                    case "Terminé":
                        echo "btn btn-default";
                        break;
                }
            }
            else { // désactiver les boutons si en cours de traitement par un autre technicien
                switch($buttonValue) {
                    case "En cours":
                        echo "btn btn-primary disabled";
                        break;
                    case "Classé sans suite":
                        echo "btn btn-default disabled";
                        break;
                    case "Terminé":
                        echo "btn btn-default disabled";
                        break;
                }
            }
            break;
        case "Classé sans suite":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn btn-default disabled";
                    break;
                case "Classé sans suite":
                    echo "btn btn-danger disabled";
                    break;
                case "Terminé":
                    echo "btn btn-default disabled";
                    break;
            }
            break;
        case "Terminé":
            switch ($buttonValue) {
                case "En cours":
                    echo "btn btn-default disabled";
                    break;
                case "Classé sans suite":
                    echo "btn btn-default disabled";
                    break;
                case "Terminé":
                    echo "btn btn-success disabled";
                    break;
            }
            break;
    }
}

/* Renvoie les informations du technicien ayant le matricule '$matricule' sous la forme d'une liste */
/* => [Objet de type array si le technicien est déjà enregistré, NULL sinon] */
function donneesTechnicien($link, $matricule) {
    $query = "SELECT CodeT, NomT, PrenomT "
            ."FROM technicien t "
            ."WHERE t.Matricule = '$matricule'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la liste complète des dossiers contenus dans la BD */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function dossiersCorbeilleGenerale($link) {
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD "
            ."FROM dossier d, assure a "
            ."WHERE d.CodeA = a.CodeA "
            ."ORDER BY d.DateD";
    $result = mysqli_query($link, $query);
    
    return $result;
}

/* Renvoie la liste des dossiers en cours de traitement par le technicien de code '$codeTechnicien' */
/* => [Objet de type array si le technicien a des dossiers dans sa corbeille, NULL sinon] */
function dossiersCorbeilleTechnicien($link, $codeTechnicien) {
    $query = "SELECT d.CodeD, d.DateD, d.RefD, a.NirA, d.StatutD, t.Matricule, tr.DateTraiterD "
            ."FROM dossier d, assure a, technicien t, traiter tr "
            ."WHERE d.CodeA = a.CodeA "
            ."AND d.CodeD = tr.CodeD "
            ."AND t.CodeT = tr.CodeT "
            ."AND t.CodeT = '$codeTechnicien' "
            ."AND d.StatutD = 'En cours' ";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Enregistre le contenu d'un mail envoyé à l'assuré de code '$codeAssure' par le technicien de code '$codeTechnicien'*/
/* => [Vrai si l'enregistrement a bien été effectué, Faux sinon] */
function enregistrerMessageAssure($codeAssure, $codeTechnicien, $contenu, $link) {
    $keys = ""; $values = "";
    if($codeAssure != NULL) {$keys .= "CodeA, "; $values .= $codeAssure.", ";}
    if($codeTechnicien != NULL) {$keys .= "CodeT, "; $values .= $codeTechnicien.", ";}
    if($contenu != NULL) {$keys .= "Contenu, "; $values .= "'$contenu', ";}

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO message($keys) VALUES ($values)";

    return mysqli_query($link, $query);
}

/* Envoie un mail de confirmation d'enregistrement de données à l'adresse '$mail' */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function envoyerMailConfirmationEnregistrement($mail, $ref) {
    $subject = "PJPE - Confirmation d'enregistrement";
    $txt = "Votre référence dossier est le $ref.";

    return mail($mail, $subject, $txt);
}

/* Envoie un mail de sujet '$subject' et de contenu '$txt' à l'adresse '$mail' */
/* => [Vrai si le message a bien été envoyé, Faux sinon] */
function envoyerMailDemandePJ($mail, $subject, $txt) {
    return mail($mail, $subject, $txt);
}

/* Extrait les informations d'un message pour les renvoyer sous forme d'une liste */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
/* => [ainsi que la référence du dossier]                                                           */
/* => Ne fonctionne que sur les messages générés automatiquement                                     */
function extraireMessage($contenu) {
    //Position de l'adresse email
    $deb = strpos($contenu, "À : ") + strlen("À : ");
    $fin = strpos($contenu, "Objet : ");
    $mail = substr($contenu, $deb, $fin - $deb);

    //Position de la référence du dossier
    $deb = strpos($contenu, "?RefD=") + strlen("?RefD=");
    $fin = $deb + 8; // 8 = Nb char référence
    $ref = substr($contenu, $deb, $fin - $deb);

    //Position de l'objet
    $deb = strpos($contenu, "Objet : ") + strlen("Objet : ");
    $fin = strpos($contenu, "Message : ");
    $objet = substr($contenu, $deb, $fin - $deb);

    //Position du contenu du message
    $deb = strpos($contenu, "Message : ") + strlen("Message : ");
    $fin = strlen($contenu);
    $texte = explode("\n", substr($contenu, $deb, $fin - $deb));
    $texte = implode("<br>",$texte);

    return [$mail, $objet, $texte, $ref];
}

/* Retire le dossier de code '$codeDossier' de la liste des dossiers traités */
/* => [Vrai si le retrait a bien été effectué, Faux sinon] */
function libererDossier($link, $codeDossier) {
    $query = "DELETE FROM Traiter WHERE CodeD = $codeDossier";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Renvoie tous les messages envoyés à l'assuré de code '$codeA' */
/* => [Objet de type array contenant l'adresse email de l'assuré, l'objet et le contenu du message] */
// Liste des messages adressés à un assuré
function listeMessages($codeAssure, $link) {
    $query = "SELECT DateEnvoiM, Contenu, T.Matricule "
            ."FROM Message M, Assure A, Technicien T "
            ."WHERE A.CodeA = M.CodeA "
            ."AND A.CodeA = $codeAssure "
            ."AND T.CodeT = M.CodeT "
            ."ORDER BY DateEnvoiM DESC";
 
    return mysqli_query($link, $query);
}

/* Renvoie le nombre de dossiers restant à traiter au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiter($link) {
    $query = "SELECT COUNT(*) AS nbDossiersAtraiter "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter' "
            ."AND DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre total de dossiers restant à traiter */
/* => [Entier nul ou posiitf] */
function nbDossiersATraiterTotal($link) {
    $query = "SELECT COUNT(*) AS nbDossiersAtraiterTotal "
            ."FROM dossier d "
            ."WHERE d.StatutD = 'À traiter'";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers classés sans suite au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersClasses($link) {
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersClasses "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Classé sans suite' "
            ."And DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers reçus au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersRecus($link) {
    $query = "SELECT COUNT(*) AS nbDossiersRecus "
            ."FROM dossier d "
            ."WHERE DATE(d.DateD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie le nombre de dossiers classés comme 'Terminé' au cours de la journée */
/* => [Entier nul ou posiitf] */
function nbDossiersTermines($link) {
    $query = "SELECT COUNT(DISTINCT d.CodeD) AS nbDossiersTermines "
            ."FROM dossier d, traiter t "
            ."WHERE d.CodeD = t.CodeD "
            ."AND d.StatutD = 'Terminé' "
            ."AND DATE(t.DateTraiterD) = CURDATE()";
    $result = mysqli_query($link, $query);

    return mysqli_fetch_array($result);
}

/* Renvoie la liste des fichiers du dossier de code '$codeDossier' */
/* => [Objet de type array si le dossier existe, NULL sinon] */
function recupererJustificatifs($link, $codeDossier) {
    $query = "SELECT CheminJ, Mnemonique "
            ."FROM justificatif j, listemnemonique l "
            ."WHERE j.CodeM = l.CodeM "
            ."AND j.CodeD = '$codeDossier'";
    $result = mysqli_query($link, $query);

    return $result;
}

/* Affilie le dossier de code '$codeDossier' au technicien de code '$codeTechnicien' et change son statut en '$statut' */
/* => [Vrai si le changement de statut a bien été effectué, Faux sinon] */
function traiterDossier($codeTechnicien, $codeDossier, $statut, $link) {
    $keys = ""; $values = "";
    if ($codeTechnicien != NULL) {
        $keys .= "CodeT, "; $values .= $codeTechnicien . ", ";
    }
    if ($codeDossier != NULL) {
        $keys .= "CodeD, "; $values .= $codeDossier . ", ";
    }

    //Suppression du dernier caractère pour les clés
    $keys = substr($keys, 0, strlen($keys) - 2);
    //Suppression du dernier caractère pour les valeurs
    $values = substr($values, 0, strlen($values) - 2);

    $query = "INSERT INTO traiter($keys) VALUES ($values)";

    if(mysqli_query($link, $query)) {
        if(!changerStatutDossier($link, $codeDossier, $statut)){
            echo "<div class='alert alert-danger'><strong>Alerte !".
            "</strong> Erreur dans le changement du statut du dossier !</div>";
            return False;
        }
        else return True;
    }
    else {
        echo "<div class='alert alert-danger'><strong>Alerte !".
            "</strong> Erreur dans l'insertion dans traiter !</div>";
            return False;
        return False;
    }
}

/* Vérifie l'unicité du matricule '$matricule' */
/* => ["Unique" si c'est vrai, ... ???] */
function verifierMatricule($link, $matricule) {
    $query = "SELECT * FROM technicien WHERE Matricule='$matricule'";
    $curseur = mysqli_query($link, $query);

    if ($curseur != null) {
        if (mysqli_num_rows($curseur) == 0) {
            return "Unique";
        } else {
            $ligne = mysqli_fetch_array($curseur);
            echo "Le matricule" . $ligne["Matricule"] . "est déjà attribué";
        }
    }
    return "Erreur de vérification du Matricule";
}

?>
