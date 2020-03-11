var pj = ["salarie", "interim", "cesu", "pole-emploi", "pole-emploiC", "intermit", "independant", "art-aut"];
var msg_salarie = "Uniquement si vous ne faîte pas partie des autres cas.";
var msg_interim = "A titre d'exemple, peuvent ";
var msg_cesu = "";
var msg_pole_emploi = "";
var msg_pole_emploiC = "";
var msg_intermit = "";
var msg_independant = "";
var msg_art_aut = "";

//Fonction pour gérer les messages des statuts
function showInfo_function(currentPJ) {
	switch (currentPJ) {
		case "salarie":
			$("#message").html(msg_salarie);
			break;
		case "interim":
			$("#message").html(msg_interim);
			break;
		case "cesu":
			$("#message").html(msg_cesu);
			break;
		case "pole-emploi":
			$("#message").html(msg_pole_emploi);
			break;
		case "pole-emploiC":
			$("#message").html(msg_pole_emploiC);
			break;
		case "intermit":
			$("#message").html(msg_intermit);
			break;
		case "independant":
			$("#message").html(msg_independant);
			break;
		case "art-aut":
			$("#message").html(msg_art_aut);
			break;
	}

	$("#info-status").show();
}

function hideInfo() {
	$("#info-status").hide();
}

//Affiche un message détaillant un statut lors du survol d'un bouton
function showInfo() {
	for(i = 0; i < pj.length; i++) {
		$("#" + pj[i]).hover(showInfo_function(pj[i]), hideInfo());
	}
}

//Scroll vers le div d'identifiants "id"
function goToByScroll(id, duration) {
    // Remove "link" from the ID
    id = id.replace("link", "");
    // Scroll
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, duration);
}

//Rafraichissement du formulaire
function showForm() {
	//Si les éléments de l'état civil n'est pas affiché
	if ($("#form_panel > div.container:first-child").is(":visible") == false) {
		setStatusToTheLeft(); //On place le menu des boutons du haut à gauche
		$("#form_panel").show(); //On affiche le formulaire
	}
	goToByScroll('form_panel', 1000); //On scroll sur le formulaire
}

//Place les boutons de "statuts sociaux" sur la gauche
function setStatusToTheLeft() {
	$("#status").css("display: float");
}

//Désactivation de l'évènement clique pour un objet dont le
//sélecteur est entré en paramètre
function isUnselected(selector) {
	var classList = $(selector).attr('class').split(/\s+/);
	
	for(i = 0; i < classList.length; i++) {
		if(classList[i] == "unselected") return true;
	}
	
	return false;
}

//Cache tous les justificatifs
function hideAllPJ() {
	for(i = 0; i < pj.length ; i++) {
		$("." + pj[i]).hide();
	}
}

//Création d'une fonction événementielle déclencher du clique
//sur l'un des bouton $("#" + pj[i])
function click_function(event){
	var currentPJ = event.data.arg1;

	if(isUnselected("#" + currentPJ)){
		showForm();
		
		hideAllPJ();
		$("#form_panel > div.panel-heading").text($("#" + currentPJ).text());
		$("." + currentPJ).show(1000);
		$(".selected").toggleClass("unselected selected");
		$("#" + currentPJ).toggleClass("unselected selected");

		if(	  (currentPJ == "interim")
			| (currentPJ == "art-aut")
			| (currentPJ == "independant")
			| (currentPJ == "cesu")
			| (currentPJ == "independant")
			| (currentPJ == "independant")
		){
			//Changement de texte
			$("#nb_BS").text("12");	//12 Bulletins de salaire
			$("#seuil_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
		}
		else if(currentPJ == "pole-emploi"){
			//Changement de texte
			$("#nb_BS").text("4");	//4 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
		else if(currentPJ == "pole-emploiC"){
			//Changement de texte
			$("#nb_BS").text("3");	//4 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
	}
 }

//Affichage des zones de dépot des PJ en fonction
//de la catégorie choisie via le nom des classes
$(document).ready(function(){
	showInfo();
	$("#form_panel").hide(); //Le formulaire est masqué
	
	for(i = 0 ; i < pj.length ; i++) {
		var currentPJ = pj[i];
		$("#" + currentPJ).addClass("unselected"); //Désélection de tous les boutons
		$("#" + currentPJ).click({arg1: currentPJ}, click_function);
	}
});