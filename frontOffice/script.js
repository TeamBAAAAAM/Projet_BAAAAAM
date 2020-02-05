var pj = ["salarie", "interim", "cesu", "pole-emploi",
		  "pole-emploiC", "intermit", "independant", "art-aut"];

//Au lancement de la page
$(document).ready(function(){
	$("#form_panel").hide(); //Le formulaire est masqué
	
	//Désélection de tous les boutons
	for(i = 0; i < pj.length ; i++) {
		$("#" + pj[i]).addClass("unselected");
	}
});

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
		$("#" + pj[i]).hide();
	}
}

function majPJ(pj) {
	if(isUnselected("#" + pj)){
		showForm();
		
		hideAllPJ();
		$("." + pj).show(1000);
		$(".selected").toggleClass("unselected selected");
		$("#" + pj).toggleClass("unselected selected");

		if((pj == "interim")
		|| (pj == "art-aut")
		|| (pj == "independant")
		|| (pj == "cesu")
		|| (pj == "independant")
		|| (pj == "independant")
		){
			//Changement de texte
			$("#nb_BS").text("12");	//12 Bulletins de salaire
			$("#seuil_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
		}
		else if(pj == "pole-emploi"){
			//Changement de texte
			$("#nb_BS").text("4");	//4 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
		else if(pj == "pole-emploiC"){
			//Changement de texte
			$("#nb_BS").text("3");	//4 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
	}
}

//Affichage des zones de dépot des PJ en fonction
//de la catégorie choisie via le nom des classes
$(document).ready(function(){
	//Masquage du formulaire au chargement de la page
	for(i = 0 ; i < pj.length ; i++) {
		$("#" + pj[i]).click(majPJ(pj[i]));
	}
});