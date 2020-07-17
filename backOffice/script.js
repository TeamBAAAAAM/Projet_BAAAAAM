/******************************************************************/

/*   SCRIPT JS POUR LA GESTION DES PAGES CÔTÉ TECHNICIEN          */

/******************************************************************/


/*------------------------------------------------------------------
 	FONCTIONS GÉNÉRALES
------------------------------------------------------------------*/

/* Formats autorisés */
var format = ["jpg", "jpeg", "png", "bmp", "tif", "tiff", "pdf"];

/* Les éléments de la liste des PJs qui doivent réagir à des événements */
var event_elt_pjs = ["#panel-pjs div.image", "#panel-pjs div.text", "#panel-pjs div.matricule"];

$(document).ready(function() {
    //Initialisation des écouteurs pour la recherche
    $("#recherche").on("keyup", function() { TrierTableau() });
    $("#statut").change(function() { TrierTableau() });
    $("#date_debut").change(function() { TrierTableau() });
    $("#date_fin").change(function() { TrierTableau() });
    $("#mois_nir").change(function() { TrierTableau() });
    $("#nb_page").change(function() { TrierTableau() });

    // Gestion des messages
    $(".alert").hide();
    $(".alert").show(1500);

    // Affichage d'un bouton de suppression lors du survol
    $(".alert").hover(function() {
        // Création du bouton de suppression
        var elt = document.createElement("span");
        elt.id = "msg_close";
        elt.className = "glyphicon glyphicon-remove";
        $(this).find(".alert-title").append(elt);

        // Initialisation de l'évènement "clic"
        $("#msg_close").click(function() {
            // On cache le message parent le plus proche
            $(this).closest(".alert").hide(400, function() {
                $(this).remove();
            });
        });
    }, function() {
        // Sinon on le supprime
        $("#msg_close").remove();
    });

    // Désactivation de tous les boutons de classe disabled
    $(".disabled").attr("disabled", true);

    // Gestion du hover sur les éléments des pièces justificatives
    hoverListePJS();

    // Gestion du clique sur les éléments des pièces justificatives
    clickListePJS();
});

/* Vérifie et corrige la valeur du champ de saisie du matricule en fonction du format 'format' */
/* => Ne renvoie rien. NB : l'id du input text doit être 'mat' et le format de la formt '#### ####'*/
function checkFormatMatricule(format) {
    formatNIR = format;

    let caret = document.getElementById("mat").selectionStart;
    var str = $("#mat").val().toUpperCase();

    //Suppression des valeurs invalides
    var pattern = /[0-9]|(A)|(B)|\s/g; //Prendre en compte le cas de la Corse (2A ou 2B)	
    var match = str.match(pattern);
    if (match != null) {
        str = match.join("");
        var deb = str.substr(0, caret);
        var fin = str.substr(caret);

        for (i = 0; i < caret; i++) {
            if (format.charAt(i) == " " && str.charAt(i) != " ") {
                deb = deb.substr(0, i) + " " + deb.substr(i);
                caret++;
            }
        }

        //Si le curseur est dans la chaine de caractères
        if (caret < str.length - 1) {
            for (i = caret; i < format.length; i++) {
                if (format.charAt(i) == " " && fin.charAt(i - caret) != " ") {
                    fin = fin.substr(0, i - caret) + " " + fin.substr(i - caret);
                }
                if (format.charAt(i) == "#" && fin.charAt(i - caret) == " ") {
                    fin = fin.substr(0, i - caret) + fin.substr(i - caret + 1);
                }
            }
        }

        str = deb + fin;
        //Si le nombre de caractères courant dépasse celui du nombre autorisé
        if (str.length > format.length) str = str.substr(0, format.length);

        $("#mat").val(str);
        document.getElementById("mat").setSelectionRange(caret, caret);
    }
}

//Fonction qui gère modifie le lien vers l'aperçu
function updateViewer(path) {
    $("iframe#apercu").attr("src", path);
}

function DateToNumber(date) {
    var number = date.substring(0, 4);
    number += date.substring(5, 7);
    number += date.substring(8, 10);

    return number;
}

function TrierTableau() {
    TrierListe($("#recherche").val(), $("#date_debut").val(),
        $("#date_fin").val(), $("#statut").val(), $("#mois_nir").val());
    GenererPagination();
}

function TrierListe(texte, dateDebut, dateFin, statut, moisNir) {
    var lignes = document.getElementById("data-list").getElementsByTagName("tr");
    if (dateDebut != "") dateDebut = DateToNumber(dateDebut);
    if (dateFin != "") dateFin = DateToNumber(dateFin);

    for (i = 0; i < lignes.length; i++) {
        var colonnes = lignes[i].getElementsByTagName("td");
        var dateCourante = DateToNumber(colonnes[0].innerHTML);
        var statutCourant = colonnes[3].innerHTML;
        var moisNirCourant = NaissanceAssure(colonnes[2].innerHTML)[0];

        lignes[i].style = "";
        lignes[i].className = "valide";

        if (dateDebut != "" && dateFin != "") {
            if (!(dateDebut <= dateCourante && dateCourante <= dateFin)) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        } else if (dateDebut != "") {
            if (!(dateDebut <= dateCourante)) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        } else if (dateFin != "") {
            if (!(dateFin >= dateCourante)) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        }

        if (statut != "" && statut != "Tous") {
            if (statut != statutCourant) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        }

        if (texte != "") {
            texte = texte.toLowerCase();

            if (!(colonnes[0].innerHTML.toLowerCase().includes(texte.toLowerCase()) ||
                    colonnes[1].innerHTML.toLowerCase().includes(texte.toLowerCase()) ||
                    colonnes[2].innerHTML.toLowerCase().includes(texte.toLowerCase()) ||
                    colonnes[3].innerHTML.toLowerCase().includes(texte.toLowerCase()))) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        }

        if (moisNir != "") {
            if (moisNir != moisNirCourant) {
                lignes[i].style.display = 'none';
                lignes[i].className = "";
            }
        }

        console.log(i + " : " + lignes[i].style.display);
        console.log(i + " : " + lignes[i].className);
    }

    CliquePageBouton(1);
}

// Extraction du mois et de l'année de naissance d'un assuré à partir du NIR
function NaissanceAssure($nir) {
    $annee = $nir.substring(2, 4);
    $mois = $nir.substring(5, 7);
    return Array($mois, $annee);
}

//Génère automatiquement la pagination selon le nombre de ligne affiché
function GenererPagination() {
    nbLignesParPage = $("#nb_page").val();
    nbLignesTableau = $("#data-list tr.valide").length;

    nbPage = Math.ceil(nbLignesTableau / nbLignesParPage);
    html = "";

    for (i = 1; i <= nbPage; i++) {
        html += '<li';
        if (i == 1) html += ' class="active" '
        html += '><a id="page' + i + '" role="button" onClick="CliquePageBouton(' + i + ')">' + i + '</a></li>';
    }

    $(".pagination").html(html);
}

//Gère l'évènement lors du clic sur une page
function CliquePageBouton(numPage) {
    // Changement du bouton cliqué en classe "active"
    $("ul.pagination li").removeClass("active");
    $("ul.pagination li:nth-child(" + numPage + ")").addClass("active");

    nbLignesTableau = $("#data-list tr.valide").length;
    nbLignesParPage = $("#nb_page").val();

    nbPage = Math.ceil(nbLignesTableau / nbLignesParPage);
    fin = numPage * nbLignesParPage - 1;

    /*if(debut + nbLignesParPage > nbLignesTableau) fin = nbLignesTableau;
    else fin = debut + nbLignesParPage;*/

    debut = fin - nbLignesParPage + 1;

    for (i = 0; i < nbLignesTableau; i++) {
        element = $("#data-list tr.valide:eq(" + i + ")");
        if (i >= debut && i <= fin) {
            element.show();
        } else {
            element.hide();
        }
    }
}

/* Ouvre une boîte de dialogue pour vérifier l'annulation de saisie d'un formulaire */
/* => Mettre en valeur de l'attribut 'onClick' avec pour paramètre 'event' */
function confirmationAnnulation(event) {
    event.preventDefault(); // Pour empêcher d'être redirigé malgré une indication négative
    if (confirm("Êtes-vous bien sûr de vouloir annuler votre saisie ?")) {
        window.location = event.target.href; // Redirection vers le lien de l'attribut 'href'
    }
}

/* Gère la taille du contenu de la zone d'aperçu (script de traitement de fichier) */
function gestionTailleApercu() {
    try { // Pour retirer l'erreur du au chargment d'un document
        var iframe = document.getElementById("apercu");
        var elt = iframe.contentWindow.document.getElementsByTagName("body")[0];
        elt.firstChild.style.width = "100%";
    } catch (error) {} // Pas d'erreur à afficher
}

/* Gestion du hover des éléments de la liste des pièces justificatives  */
function hoverListePJS() {
    for (i in event_elt_pjs) {
        $(event_elt_pjs[i]).hover(function() {
            var parentNode = $(this).parent().parent();

            if (!parentNode.hasClass("pj-selected")) {
                parentNode.addClass("pj-hovering");
                parentNode.attr(
                    "title",
                    "Afficher ce fichier dans l'aperçu");
            }
        }, function() {
            var parentNode = $(this).parent().parent();
            parentNode.removeClass("pj-hovering");
            parentNode.attr("title", "");
        });
    }
}

//Fonction qui gère l'affichage d'un item de la liste des pièces justificatives
//lors d'un clic
function clickListePJS() {
    for (i in event_elt_pjs) {
        $(event_elt_pjs[i]).click(function() {
            var parentNode = $(this).parent().parent();
            var namefile = $(parentNode).find(".text").text();
            $("#panel-pjs li").removeClass("pj-selected");
            $("#panel-apercu").find("#nom-fichier-apercu").text(namefile);
            $(parentNode).removeClass("pj-hovering");
            $(parentNode).addClass("pj-selected");
        });
    }
}