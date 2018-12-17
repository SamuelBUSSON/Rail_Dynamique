
//Pour avoir l'url du site
function base_url(){
	var getUrl = window.location;
	return getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
}

//Rafraichit le tableau
function refreshTable(){
	//L'url pour avoir le tableau
	urlTableau = base_url()+"/index.php/Welcome/getTableau/"+$("#numEtage").text();
	//Requete ajax
	$.ajax({
		type: 'GET',
		url : urlTableau,
		success: function(data) {
			//On affiche le tableau comme il se doit
			$('#main_table').html(data);
			$('.tooltip').tooltip();
		}
	});
}

//Fonction pour afficher les fleches pour changer d'étage
function displayStairs(){
	//Afficher ou non selon si c'est 0 1 2
	switch ($("#numEtage").text()) {
		case "0":
			$('#upStairs').show();
			$('#downStairs').hide();
			break;
		case "1":
			$('#upStairs').show();
			$('#downStairs').show();
			break;
		case "2":
			$('#upStairs').hide();
			$('#downStairs').show();
			break;
	}
}

//Quand la page est chargée
$(document).ready(function(){
	//On affiche les fleches comme il faut
	displayStairs();
	//On affiche le tableau
	refreshTable();
	//On initialise les selects
	$('select').formSelect();
	//Le nombre de produits quand on veut ajouter une palette
	var compteurProduits = 1;
	//La variable qui permet de savoir sur quelle colonne on a cliqué
	var colonne = -1;
	//L'id de la palette a transferer
	var idPalTransfert = -1;

	var paletteToDelete = -1;

	$('.delete').on("click", function(){
		searchUrl = base_url()+"/index.php/Welcome/deletePalette/"+paletteToDelete;
		$.ajax({
			type: 'GET',
			url : searchUrl,
			success: function(data) {
				refreshTable();
				$('#modalPal').modal('close');
			}
		});
	});

	//Si on clique sur la fleche qui monte
	$('#upStairs').on("click", function(){
		//Alors l'étage change
		i = $("#numEtage").text();
		i++;
		$("#numEtage").text(i);
		//On affiche les fleches
		displayStairs();
		//On rafraichit le tableau
		refreshTable();
	});
	//Meme principe qu'au dessus mais a l'opposé
	$('#downStairs').on("click", function(){
		i = $("#numEtage").text();
		i--;
		$("#numEtage").text(i);
		displayStairs();
		refreshTable();

	});

	//Les elements qui ont pour class modal sont des modal
	$('.modal').modal();

	// $('select').formSelect();


	//Quand on clique sur un élément qui a la class remPalette c-à-d le signe moins
		$(document).delegate(".remPalette","click", function(){
			//On récupère la position/colonne
			position = $(this).data('pos');
			//L'étage courant
			etage = $("#numEtage").text();
			//Création de l'url
			searchUrl = base_url()+"/index.php/Welcome/remPalette/"+etage+"/"+position;
			$.ajax({
				url : searchUrl,
				success: function(data) {
					//On affiche le résultat du serveur
					if(data != ""){
						toastr.error(data, '');
					}
					//On refresh le tableau
					refreshTable();
					compteurProduits = 1;
				}
			});
		});

		//Quand on clique sur un +
	$(document).delegate(".addPalette","click", function(){
		//On ouvre le modal associé
		$('#modalAdd').modal('open');
		//Il n'y a qu'un seul produit
		$('#nouveauxProduits').html('');

		//Le nombre de produit passe a 1
		compteurProduits = 1;
		//Et la colonne correspondante
		colonne = $(this).data('pos');
	});

	//Après aboir appuyé sur une touche quand on clique sur l'input liée a la recherche
	$('#search').keydown( function(e) {
		//On récupère la valeur de la touche
    var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		//Ce que l'utilisateur a inscrit dans l'input
		value = $(this).val();
		//Si la touche est 'ENTREE'
    if(key == 13) {
			//L'url correspondante
			searchUrl = base_url()+"/index.php/Welcome/search/";
			//Requete ajax on envoie la requete en post
			$.ajax({
				type : "POST",
				url : searchUrl,
				async : false,
				data : {'requete' : value},
				success: function(data) {
					//On affiche combien de palette ont été trouvées
					toastr.success(data+' palette(s) trouvée(s)', '');
					//On rafraichit la table car cela va colorier les cases trouvées
					refreshTable();
				}
			});
    }
});

	//Quand on veut ajoute rplsu d'un produit sur une palette
	//Après avoir cliqué sur le +
	$(document).delegate(".addProduit", "click", function(){
		//On ajoute au formulaire les champs pour ajouter un produit
		$('#nouveauxProduits').append(`
			</br></br></br></br><hr class='style18'>
			<h5>Produit N°${compteurProduits}</h5>
			<div class='row'>
			<div class="input-field col s4">
			<label for="nom_produit${compteurProduits}"> Nom du produit :
			<input autocomplete="off" type="text" name='nom_produit${compteurProduits}' id="nom_produit${compteurProduits}" placeholder="SPB 30%">
			</label>
			</div>
			<div class="input-field col s4">
			<label for="code_produit${compteurProduits}"> Code du produit :
			<input autocomplete="off" type="text" name='code_produit${compteurProduits}' id="code_produit${compteurProduits}" placeholder="280.06">
			</label>
			</div>
			<div class="input-field col s4">
			<label for="ue${compteurProduits}"> UE du produit :
			<input autocomplete="off" type="text" name='ue${compteurProduits}' id="ue${compteurProduits}" >
			</label>
			</div>
			</div>
			<!--
			<br><br><br>
			<div class="row">
			<div class="input-field col s3">
			<label for="lot${compteurProduits}"> Lot :
			<input type="number" name='lot${compteurProduits}' id="lot${compteurProduits}" >
			</label>
			</div>
			<div class="input-field col s3">
			<label for="qte${compteurProduits}"> Quantité produit :
			<input type="number" name='qte${compteurProduits}' id="qte${compteurProduits}" >
			</label>
			</div>
			<div class="input-field col s3">
			<label for="date_fab${compteurProduits}"> Date de fabrication :
			<input class='datepicker' name='date_fab${compteurProduits}' id="date_fab${compteurProduits}" >
			</label>
			</div>
			<div class="input-field col s3">
			<label for="dlc${compteurProduits}"> DLC :
			<input class='datepicker' name='dlc${compteurProduits}' id="dlc${compteurProduits}" >
			</label>
			</div>-->
			</div>
			`);
			//Il y'a alors un nouveau produit
			compteurProduits++;
		});


		//Quand on clique sur la fleche sur fond gris
		$(document).delegate(".transfert", "click", function(){
			//On ouvre la fenetre
			idPalTransfert = $(this).data("idpal");
			$("#modalTransfert").modal("open");
		});

		//Pour finaliser le transfert
		$(document).delegate(".ok_transfert", "click", function(){
			if(!$('#trans_etage').val()){
				toastr.error('Veuillez choisir un étage', '');
			}else{
				if($('#colonneTransfert').val() < 1 || $('#colonneTransfert').val() > 14){
					toastr.error('Veuillez choisir une colonne entre 1 et 14', '');
				}else{
					//La fonction pour effectuée la rotation
					searchUrl = base_url()+"/index.php/Welcome/transfertPalette/"+idPalTransfert+"/"+$('#colonneTransfert').val()+"/"+$('#trans_etage').val();
					$.ajax({
						type: 'GET',
						url : searchUrl,
						success: function(data) {
							console.log(data);
							$("#modalTransfert").modal("close");
							refreshTable();
						}
					});
				}
			}
		});

		//Après avoir saisit tout les produits on peut ajouter la palette a la base de données
		$(document).delegate(".addPaletteToDb", "click", function(){
			searchUrl = base_url()+"/index.php/Welcome/addPalette/"+$("#numEtage").text()+"/"+(compteurProduits)+"/"+colonne;
			$.ajax({
				type: 'POST',
				url : searchUrl,
				//On serialize les données du formulaire
				data : $('#formAjoutPalette').serialize(),
				success: function(data) {
					//Le compteur de produit repasse alros a 1
					compteurProduits = 1;
					refreshTable();
					if(data != ""){
						toastr.error(data, '');
					}
				}
			});
		});

		//Quand on clique sur le bouton rotation
		$(document).delegate(".rotation", "click", function(){
			//La fonction pour effectuée la rotation
			searchUrl = base_url()+"/index.php/Welcome/rotation/"+$("#numEtage").text()+"/"+$(this).data('pos');
			$.ajax({
				type: 'GET',
				url : searchUrl,
				success: function(data) {
					refreshTable();
				}
			});

		});

		//Quand on clique sur la palette
		$(document).delegate(".palette", "click", function(){
			//On récupère l'id de la palette
			idPalette = $(this).data("idpal");
			paletteToDelete = idPalette;
			//On affiche le modal
			$('#modalPal').modal('open');
			//Fonction ajax pour affichée en bonne et due forme le contenue de la fenetre modal
			searchUrl = base_url()+"/index.php/Welcome/getPalette/"+idPalette;
			$.ajax({
				type: 'GET',
				url : searchUrl,
				success: function(data) {
					$('#modal_container').html('');
					$('#modal_container').html(data);
				}
			});
		});
	});
