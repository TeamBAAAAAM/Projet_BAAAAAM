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