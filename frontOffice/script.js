//Au lancement de la page
$(document).ready(function(){
	$("#form").hide(); //Le formulaire est masqué
	
	//Désélection de tous les boutons
	$("#salarie").addClass("unselected");
	$("#interim").addClass("unselected");
	$("#cesu").addClass("unselected");
	$("#pole-emploi").addClass("unselected");
	$("#pole-emploiC").addClass("unselected");
	$("#intermit").addClass("unselected");
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
	if ($("#form > div.container:first-child").is(":visible") == false) {
		setStatusToTheLeft(); //On place le menu des boutons du haut à gauche
		$("#form").show(); //On affiche le formulaire
	}
	goToByScroll('form', 1000); //On scroll sur le formulaire
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

//Affichage des zones de dépot des PJ en fonction
//de la catégorie choisie via le nom des classes
$(document).ready(function(){
	//Masquage du formulaire au chargement de la page
	$("#salarie").click(function(){
		if(isUnselected("#salarie")){
			showForm();
			
			$(".interim").hide();
			$(".cesu").hide();
			$(".pole-emploi").hide();
			$(".pole-emploiC").hide();
			$(".intermit").hide();
			$(".salarie").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#salarie").toggleClass("unselected selected");
		}
	});
	
	$("#interim").click(function(){
		if(isUnselected("#interim")){
			showForm();
			
			$(".salarie").hide();
			$(".cesu").hide();
			$(".pole-emploi").hide();
			$(".pole-emploiC").hide();
			$(".intermit").hide();
			$(".interim").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#interim").toggleClass("unselected selected");
			
			//Changement de texte
			$("#nb_BS").text("12");	//12 Bulletins de salaire
			$("#max_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
		}
	});
	
	$("#cesu").click(function(){
		if(isUnselected("#cesu")){
			showForm();
			
			$(".salarie").hide();
			$(".interim").hide();
			$(".pole-emploi").hide();
			$(".pole-emploiC").hide();
			$(".intermit").hide();
			$(".cesu").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#cesu").toggleClass("unselected selected");
			
			//Changement de texte
			$("#nb_BS").text("12");	//12 Bulletins de salaire
			$("#max_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
		}
	});
	
	$("#pole-emploi").click(function(){
		if(isUnselected("#pole-emploi")){
			showForm();
			
			$(".salarie").hide();
			$(".interim").hide();
			$(".cesu").hide();
			$(".pole-emploiC").hide();
			$(".intermit").hide();
			$(".pole-emploi").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#pole-emploi").toggleClass("unselected selected");
			
			//Changement de texte
			$("#nb_BS").text("4");	//4 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
	});
	
	$("#pole-emploiC").click(function(){		
		if(isUnselected("#pole-emploiC")){
			showForm();
			
			$(".salarie").hide();
			$(".interim").hide();
			$(".cesu").hide();
			$(".pole-emploi").hide();
			$(".intermit").hide();
			$(".pole-emploiC").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#pole-emploiC").toggleClass("unselected selected");
			
			//Changement de texte
			$("#nb_BS").text("3");	//3 Bulletins de salaire
			$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
		}
	});
	
	$("#intermit").click(function(){
		if(isUnselected("#intermit")){
			showForm();
			
			$(".salarie").hide();
			$(".interim").hide();
			$(".cesu").hide();
			$(".pole-emploi").hide();
			$(".pole-emploiC").hide();
			$(".intermit").show(1000);
			$(".selected").toggleClass("unselected selected");
			$("#intermit").toggleClass("unselected selected");
		}
	});
});