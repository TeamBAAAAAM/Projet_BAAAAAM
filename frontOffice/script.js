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
function goToByScroll(id) {
    // Remove "link" from the ID
    id = id.replace("link", "");
    // Scroll
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, 1000);
}

//Rafraichissement du formulaire
function refreshForm() {
	//Masquage du foomulaire
	$("#form").hide(0);
	$("#form").show(1000);
	goToByScroll('form');
}

//Affichage des zones de dépot des PJ en fonction
//de la catégorie choisie via le nom des classes
$(document).ready(function(){
	//Masquage du formulaire au chargement de la page
	
	$("#salarie").click(function(){
		refreshForm();
		
		$(".interim").hide();
		$(".cesu").hide();
		$(".pole-emploi").hide();
		$(".pole-emploiC").hide();
		$(".intermit").hide();
		$(".salarie").show();
		$(".selected").toggleClass("unselected selected");
		$("#salarie").toggleClass("unselected selected");
	});
	$("#interim").click(function(){
		refreshForm();
		
		$(".salarie").hide();
		$(".cesu").hide();
		$(".pole-emploi").hide();
		$(".pole-emploiC").hide();
		$(".intermit").hide();
		$(".interim").show();
		$(".selected").toggleClass("unselected selected");
		$("#interim").toggleClass("unselected selected");
		
		//Changement de texte
		$("#nb_BS").text("12");	//12 Bulletins de salaire
		$("#max_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
	});
	$("#cesu").click(function(){
		refreshForm();
		
		$(".salarie").hide();
		$(".interim").hide();
		$(".pole-emploi").hide();
		$(".pole-emploiC").hide();
		$(".intermit").hide();
		$(".cesu").show();
		$(".selected").toggleClass("unselected selected");
		$("#cesu").toggleClass("unselected selected");
		
		//Changement de texte
		$("#nb_BS").text("12");	//12 Bulletins de salaire
		$("#max_BS").text("l'arrêt de travail"); //Autres cas que celui de Pôle Emploi
	});
	$("#pole-emploi").click(function(){
		refreshForm();
		
		$(".salarie").hide();
		$(".interim").hide();
		$(".cesu").hide();
		$(".pole-emploiC").hide();
		$(".intermit").hide();
		$(".pole-emploi").show();
		$(".selected").toggleClass("unselected selected");
		$("#pole-emploi").toggleClass("unselected selected");
		
		//Changement de texte
		$("#nb_BS").text("4");	//4 Bulletins de salaire
		$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
	});
	$("#pole-emploiC").click(function(){
		refreshForm();
		
		$(".salarie").hide();
		$(".interim").hide();
		$(".cesu").hide();
		$(".pole-emploi").hide();
		$(".intermit").hide();
		$(".pole-emploiC").show();
		$(".selected").toggleClass("unselected selected");
		$("#pole-emploiC").toggleClass("unselected selected");
		
		//Changement de texte
		$("#nb_BS").text("3");	//3 Bulletins de salaire
		$("#max_BS").text("l'inscription à Pôle Emploi"); //Cas à Pôle Emploi
	});
	$("#intermit").click(function(){
		refreshForm();
		
		$(".salarie").hide();
		$(".interim").hide();
		$(".cesu").hide();
		$(".pole-emploi").hide();
		$(".pole-emploiC").hide();
		$(".intermit").show();
		$(".selected").toggleClass("unselected selected");
		$("#intermit").toggleClass("unselected selected");
	});
});