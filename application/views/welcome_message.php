<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Rail Dynamique</title>


	<link rel="stylesheet" href="<?php echo css_url("materialize.min"); ?>">
	<link rel="stylesheet" href="<?php echo css_url("my_class"); ?>">
	<link rel="stylesheet" href="<?php echo css_url("toastr.min"); ?>">
	<link rel="stylesheet" href="<?php echo css_url("icons"); ?>">


</head>

<body style="background-color : #455a64 ; position : relative" class="toast-container" >


	<div class='etage'>
		<span class='btn green waves-effect reduceBtn' id='upStairs'><i class="material-icons">arrow_upward</i></span> </br></br>
		Etage <span id='numEtage'>0</span></br></br>
		<span class='btn green waves-effect reduceBtn' id='downStairs'><i class="material-icons">arrow_downward</i></span>
	</div>



	<nav style="background-color : #f4511e">
		<div class="nav-wrapper">
			<a href="#" class="brand-logo" style="padding-left : 10px">Rail Dynamique</a>
			<div class="input-field right" style="width : 45%; background-color : #e3400c ; padding : 10px">
				<input autocomplete="off" id="search" type="search" required>
				<label class="label-icon" for="search"><i class="material-icons">search</i></label>
				<i class="material-icons">close</i>
			</div>
		</div>
	</nav>



	<div class="container">
	</br>
	<table class="white-text" id='main_table'>

	</table>
</div>


<div id='modalPal' class='modal'>
	<div class="btn red waves-effect waves-light delete" style="position : absolute ; top : 5px; right : 5px;">SUPPRIMER LA PALETTE <i class="right material-icons">delete</i></div>
	<div class='modal-content' id='modal_container'>
	</div>
</div>

<div id='modalTransfert' class='modal allongeModal' height='1000px'>
	<div class='modal-content' id='modal_container'>
		<h4>Déplacement palette</h4>
		<hr/>
		<div class='row'>
			<div class="input-field col s6">
				<select id='trans_etage'>
					<option value="" disabled selected>Choisir l'étage</option>
					<option value="0">Etage 0</option>
					<option value="1">Etage 1</option>
					<option value="2">Etage 2</option>
				</select>
				<label>Quel étage ?</label>
			</div>
			<div class="input-field col s6">
				<input id='colonneTransfert' type="number" min="1" max="14">
				<label>Quel rangée ?</label>
			</div>
		</div>
		<div class="btn green waves-effect waves-light ok_transfert" style="width : 100%">Déplacer <i class="material-icons">check</i></div>
	</div>
</div>


<div id='modalAdd' class='modal'>
	<div class='modal-content ' id='modal_container'>
		<h4>Ajout d'une palette</h4>
		<hr>
		<form class='col s12' id='formAjoutPalette' method="post" >
			<div class='row'>
				<div class="input-field col s4">
					<label for="nom_produit0"> Nom du produit :
						<input type="text" autocomplete="off"  name='nom_produit0' id="nom_produit0" placeholder="SPB 30%">
					</label>
				</div>
				<div class="input-field col s4">
					<label for="code_produit0"> Code du produit :
						<input type="text" autocomplete="off" name='code_produit0' id="code_produit0" placeholder="280.06">
					</label>
				</div>
				<div class="input-field col s4">
					<label for="ue0"> UE du produit :
						<input type="number" autocomplete="off" name='ue0' id="ue0" >
					</label>
				</div>
			</div>
			<div id='nouveauxProduits'>
			</div>
			<!--<br><br><br>
			<div class="row">
			<div class="input-field col s3">
			<label for="lot0"> Lot :
			<input type="number" name='lot0' id="lot0" >
		</label>
	</div>
	<div class="input-field col s3">
	<label for="qte0"> Quantité produit :
	<input type="number" name='qte0' id="qte0" >
</label>
</div>
<div class="input-field col s3">
<label for="date_fab0"> Date de fabrication :
<input class='datepicker' name='date_fab0' id="date_fab0" >
</label>
</div>
<div class="input-field col s3">
<label for="dlc0"> DLC :
<input class='datepicker' name='dlc0' id="dlc0" >
</label>
</div> !-->
</div>


</form>
<br><br><br>
<div class="container">
	<div class="row">
		<div class="btn green addPaletteToDb col s5"> Ajouter la palette <i  class="material-icons">create</i></div>
		<div class="btn addProduit col s6 offset-s1"> Ajouter un produit   <i class="material-icons">add</i></div>
	</div>
</div>
</div>
</div>





<script src="<?php echo js_url('jquery'); ?>" charset="utf-8"></script>
<script src="<?php echo js_url("materialize.min"); ?>" charset="utf-8"></script>
<script src="<?php echo js_url("toastr.min"); ?>" charset="utf-8"></script>
<script src="<?php echo js_url("script"); ?>" charset="utf-8"></script>
</body>
</html>
