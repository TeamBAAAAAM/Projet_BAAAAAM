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
    $("#recherche").on("keyup", function() {
        const ecouteurs = [$("#recherche"), $("#date_debut"), $("#date_fin"), $("#statut")];

        for(i = 0 ; i < ecouteurs.length ; i++) {
            var value = ecouteurs[i].val().toLowerCase();
            if(value != "") {
                $("#data-list tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            }
        }
    });
    $("#date_debut").change(function() {
        const ecouteurs = [$("#recherche"), $("#date_debut"), $("#date_fin"), $("#statut")];

        for(i = 0 ; i < ecouteurs.length ; i++) {
            var value = ecouteurs[i].val().toLowerCase();
            if(value != "") {
                $("#data-list tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            }
        }
    });
    $("#date_fin").change(function() {
        const ecouteurs = [$("#recherche"), $("#date_debut"), $("#date_fin"), $("#statut")];

        for(i = 0 ; i < ecouteurs.length ; i++) {
            var value = ecouteurs[i].val().toLowerCase();
            if(value != "") {
                $("#data-list tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            }
        }
    });
    $("#statut").change(function() {
        const ecouteurs = [$("#recherche"), $("#date_debut"), $("#date_fin"), $("#statut")];

        for(i = 0 ; i < ecouteurs.length ; i++) {
            var value = ecouteurs[i].val().toLowerCase();
            if(value != "") {
                $("#data-list tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            }
        }
    });
});