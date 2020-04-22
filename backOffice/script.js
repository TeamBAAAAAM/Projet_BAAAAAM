$(document).ready(function(){
    $("#panel-pjs li").click(clickOnPjsLi($(this)));
    
    //Initialisation des écouteurs pour la recherche
    $("#recherche").on("keyup", function() {TrierTableau()});
    $("#statut").change(function() {TrierTableau()});
    $("#date_debut").change(function() {TrierTableau()});
    $("#date_fin").change(function() {TrierTableau()});
    $("#mois_nir").change(function() {TrierTableau()});

    $(".alert").hide();
    $(".alert").show(1500);
});

//Vérifie et corrige le format du NIR
function checkFormatMatricule(format) {
	formatNIR = format;

	let caret = document.getElementById("mat").selectionStart;
	var str = $("#mat").val().toUpperCase();

	//Suppression des valeurs invalides
	var pattern = /[0-9]|(A)|(B)|\s/g; //Prendre en compte le cas de la Corse (2A ou 2B)	
	var match = str.match(pattern);
	if(match != null) {
		str = match.join("");
		var deb = str.substr(0, caret);
		var fin = str.substr(caret);
		
		for(i = 0 ; i < caret ; i++) {
			if(format.charAt(i) ==  " " && str.charAt(i) != " ") {
				deb = deb.substr(0, i) + " " + deb.substr(i);
				caret++;
			}
		}
	
		//Si le curseur est dans la chaine de caractères
		if(caret < str.length - 1) {
			for(i = caret ; i < format.length ; i++) {
				if(format.charAt(i) ==  " " && fin.charAt(i - caret) != " ") {
					fin = fin.substr(0, i - caret) + " " + fin.substr(i - caret);
				}
				if(format.charAt(i) ==  "#" && fin.charAt(i - caret) == " ") {			
					fin = fin.substr(0, i - caret) + fin.substr(i - caret + 1);
				}
			}
		}
	
		str = deb + fin;
		//Si le nombre de caractères courants dépasse celui du nombre autorisés
		if(str.length > format.length) str = str.substr(0, format.length);
	
		$("#mat").val(str);
		document.getElementById("mat").setSelectionRange(caret, caret);
	}
}

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

function DateToNumber(date) {
    var number = date.substring(0, 4);
    number += date.substring(5, 7);
    number += date.substring(8, 10);

    return number;
}

function TrierTableau() {
    TrierListe($("#recherche").val(), $("#date_debut").val(),
        $("#date_fin").val(), $("#statut").val(), $("#mois_nir").val());
}

function TrierListe(texte, dateDebut, dateFin, statut, moisNir) {
    var lignes = document.getElementById("data-list").getElementsByTagName("tr");
    if(dateDebut != "") dateDebut = DateToNumber(dateDebut);
    if(dateFin != "") dateFin = DateToNumber(dateFin);

    for(i = 0 ; i < lignes.length ; i++) {
        var colonnes = lignes[i].getElementsByTagName("td");
        var dateCourante = DateToNumber(colonnes[0].innerHTML);
        var statutCourant = colonnes[3].innerHTML;
        var moisNirCourant = NaissanceAssure(colonnes[2].innerHTML)[0];

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

        if(moisNir != "") {
            if(moisNir != moisNirCourant) {  
                lignes[i].style.display = 'none';
            }
        }
    }
}

// Extraction du mois et de l'année de naissance d'un assuré à partir du NIR
function NaissanceAssure($nir){
    $annee = $nir.substring(2, 4);
    $mois = $nir.substring(5, 7);
    return Array($mois, $annee);
}

// Mise à jour du message pré-rempli pour demander des pièces à un assuré
function MAJMessageAssure(DEPOSITE_LINK, FOOTER_EMAIL, RefD, CodeJ) {
    var Raisons = [$("#cb1").prop("checked"), $("#cb2").prop("checked"), $("#cb3").prop("checked")];
    $("#mail_text").val(EcrireMessageAssure(DEPOSITE_LINK, FOOTER_EMAIL, RefD, Raisons, CodeJ));
}

// Génère et renvoie un message pré-rempli pour demander des pièces à un assuré
function EcrireMessageAssure(DEPOSITE_LINK, FOOTER_EMAIL, RefD, Raisons, CodeJ) {
    var message = "Bonjour,\n\n"
    message += "\tNous souhaiterions vous informer que lors de votre ";
    message += "dernier dépôt, certaines pièces justificatives affiliées au dossier ";
    message += "de référence " + RefD + " semblent ";
    if(!Raisons[0] && !Raisons[1] && !Raisons[2]) {message += "[Mettre les erreurs relevées ici] ";}
    if(Raisons[0] && (Raisons[1] || Raisons[2])) message += "manquantes et ";
    else if(Raisons[0]) message += "manquantes";
    if(Raisons[1] && Raisons[2]) message += "illisibles et ";
    else if(Raisons[1]) message += "illisibles";
    if(Raisons[2]) message += "invalides";
    message += ".\n\nMerci de vous rendre à l'adresse suivante afin de déposer les documents demandés :\n\n\t";
    message += "<a href='" + DEPOSITE_LINK + "?RefD=" + RefD + "' target='_blank'>";
    message += DEPOSITE_LINK + "?RefD=" + RefD + "</a>";
    for(i = 0 ; i < CodeJ ; i++) {
        message += "&CodeJ_" + i + "=" + CodeJ[i];
    }
    message += "\n\n\tBien cordialement,\n\n";
    message += "La CPAM de la Haute-Garonne";
    if(FOOTER_EMAIL != "") message +="\n\n<hr>" + FOOTER_EMAIL;

    return message;
}