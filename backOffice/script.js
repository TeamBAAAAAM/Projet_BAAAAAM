//Fonction qui gère modifie le lien vers l'aperçu
function changePathViewer(path) {
    $("#apercu").attr("src", path);
}

//Fonction qui gère l'affichage d'un item de la liste des pièces justificatives
//lors d'un clique
function clickOnPjsLi(node) {
    $("panel-pjs li").removeClass("onclick-pjs-li");
    node.addClass("onclick-pjs-li");
}

$(document).ready(function(){
    $("#panel-pjs li").click(clickOnPjsLi($(this)));
});

//Initialisation des écouteurs pour la recherche
$(document).ready(function(){
    $("#recherche").on("keyup", function() {TrierTableau()});
    $("#statut").change(function() {TrierTableau()});
    $("#date_debut").change(function() {TrierTableau()});
    $("#date_fin").change(function() {TrierTableau()});
});

function DateToNumber(date) {
    var number = date.substring(0, 4);
    number += date.substring(5, 7);
    number += date.substring(8, 10);

    return number;
}

function TrierTableau() {
    TrierListe($("#recherche").val(), $("#date_debut").val(), $("#date_fin").val(), $("#statut").val());
}

function TrierListe(texte, dateDebut, dateFin, statut) {
    var lignes = document.getElementById("data-list").getElementsByTagName("tr");
    if(dateDebut != "") dateDebut = DateToNumber(dateDebut);
    if(dateFin != "") dateFin = DateToNumber(dateFin);

    for(i = 0 ; i < lignes.length ; i++) {
        var colonnes = lignes[i].getElementsByTagName("td");
        var dateCourante = DateToNumber(colonnes[0].innerHTML);
        var statutCourant = colonnes[3].innerHTML;

        lignes[i].style.display = '';

        if(dateDebut != "" && dateFin != "") {
            if(!(dateDebut <= dateCourante && dateCourante <= dateFin)) {
                lignes[i].style.display = 'none';
            }
        }
        else if(dateDebut != "") {
            if(!(dateDebut <= dateCourante)) {  
                lignes[i].style.display = 'none';
            }
        }
        else if(dateFin != "") {
            if(!(dateFin >= dateCourante)) {  
                lignes[i].style.display = 'none';
            }
        }

        if(statut != "" && statut != "Tous") {            
            if(!(statut == statutCourant)) {
                lignes[i].style.display = 'none';
            }
        }

        if(texte != "") {
            texte = texte.toLowerCase();

            if(!(colonnes[0].innerHTML.toLowerCase().includes(texte.toLowerCase())
                || colonnes[1].innerHTML.toLowerCase().includes(texte.toLowerCase())
                || colonnes[2].innerHTML.toLowerCase().includes(texte.toLowerCase())
                || colonnes[3].innerHTML.toLowerCase().includes(texte.toLowerCase()))) {
                    lignes[i].style.display = 'none';
            }
        }
    }
}

// Extraction du mois et de l'année de naissance d'un assuré à partir du NIR
function NaissanceAssure($nir){
    $annee = $nir.substring(2, 4);
    $mois = $nir.substring(5, 7);
    return ($mois, $annee);
}