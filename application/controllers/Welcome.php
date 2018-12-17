<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {


	/*
		Constructuer, on charge les difféents modeles
	*/
	function __construct(){
		parent::__construct();
		$this->load->model("Rail_model");
		$this->load->model("Palettes_model");
		$this->load->model("Produits_model");
		$this->load->helper("assets");
	}

	//L'index est définie par la page welcome_message, Oui j'ai eu la flemme de changer
	public function index()
	{
		$this->load->view('welcome_message');
	}

	/**
	* @brief Récupère les différents éléments correspondant à une palette
	* @param idPalette L'id de la palette
	*
	*/
	public function getPalette($idPalette){
		//On récupère les produits de la palette
		$produitsPalette = $this->Produits_model->get_produitByPalette($idPalette);
		//Pour chaque produit on affiche les carac
		foreach ($produitsPalette as $produit) {
			echo "<h4>Produit : $produit->nom_produit</h4>";
			echo "<hr >
			<!--<b>Quantité :</b> $produit->qte </br> !-->
			<b>Ue :</b> $produit->ue </br>
			<!--<b>Lot :</b> $produit->lot </br>!-->
			<!--<b>Date de Fabrication :</b> $produit->date_fabrication </br>!-->
			<!--<b>DLC :</b> $produit->dlc </br>!-->
			<b>Code Produit :</b> $produit->code_produit
			<hr class='style18'>";
		}//foreach ($produitsPalette as $produit)
	}
	/**
	* @brief Fait une rotation des palette, la palette la plus en bas passe en haut et les autres descendent
	* @param etage L'etage ou faire la rotation
	* @param position La position/colonne
	*
	*/
	public function rotation($etage, $position){
		//On récupère les palettes de cette colonnes
		$palettes = $this->Palettes_model->getPaletteByEtageAndPos($etage, $position);
		//La palette avec la position la plus basse (la plus loin dans le rail dynamique)
		$paletteMin = $this->Palettes_model->getMinPaletteByEtageAndPos($etage,$position);

		//On incrémente la postion des palettes de 1
		foreach ($palettes as $palette) {
			$this->Palettes_model->changeProfondeur($palette->pos_longueur+1 ,$palette->id_palette);
		}
		//La palette la plus proche dans le rail, celle qu'on prend en premier
		$paletteMax = $this->Palettes_model->getMaxPaletteByEtageAndPos($etage,$position);
		//Et on place cette palette a l'ancienne place de la palette la plus loin
		$this->Palettes_model->changeProfondeur($paletteMin->pos_longueur, $paletteMax->id_palette);

	}

	/** AJAX
	* @brief Colorie les palettes selon la recherche effectuée
	* @param requete Une donnée envoyée en post correspodnant a la requete
	*/
	public function search(){
		//Search est un attribut dans la BD on le passe a 0 pour toutes les palettes
		$this->Palettes_model->setSearchForAll();
		//LA requete peut être composée de différents éléments séparées par une virgule
		$str = explode(";", $this->input->post('requete'));
		//Le compteur du nombre de palette trouvée
		$countFound = 0;
		//Boucle sur le tableau
		foreach ($str as $requete) {
			//On supprime les espaves inutiles
			$requete = trim($requete);
			//Si la requete est numérique et composée de 8 caractère elle coresspond à un UE
			if(strlen($requete) == 8 && is_numeric($requete)){
				//On cherche le produit par rapport a l'ue
				$produit = $this->Produits_model->get_produitByUE($requete);
				//Si un produit existe
				if($produit){
					//La palette devient cherchée
					$this->Palettes_model->setSearch($produit->fk_palette);
					//Et une palette a été trouvée donc on incrémente le compteur
					$countFound++;
				}
				//Sinon
			}else{
				//Si la requete comporte un point est est numérique elle correspond au code produit
				if (strpos($requete, '.') !== false && is_numeric($requete)) {
					//On créé un tableau d'id
					$listeId = array();
					//On cherche les produits possédant ce code
					$produits = $this->Produits_model->get_produitByCode($requete);
					//Pour tout les produits on ajoute l'id dans le tableau
					foreach ($produits as $produit) {
						array_push($listeId, $produit->fk_palette);
					}
					//On enleve les doublons
					$listeIdFinal = array_unique($listeId);
					//Et les palettes devienent cherchées
					foreach ($listeIdFinal as $id) {
						$countFound++;
						$this->Palettes_model->setSearch($id);
					}
					//Dernier cas, la recherche par nom
				}else{
					if($requete != ""){
						//Meme principe
						$listeId = array();
						//Cette fois on recherche par le nom du produit
						$produits = $this->Produits_model->getByNomProduit($requete);
						//Meme principe
						foreach ($produits as $produit) {
							array_push($listeId, $produit->fk_palette);
						}
						//Meme principe
						$listeIdFinal = array_unique($listeId);
						//Meme principe
						foreach ($listeIdFinal as $id) {
							$countFound++;
							$this->Palettes_model->setSearch($id);
						}
					}
				}
			}
		}
		//On echo le nombre de palettes trouvées pour l'afficher
		echo $countFound;
	}

	/**
	* @brief Affiche le tableau
	* @param hauteur L'etage que l'on souhaite afficher
	*
	*/
	public function getTableau($hauteur){
		//On cherche le rail meme si il sert a rien dans le projet
		$rail = $this->Rail_model->get_rail();
		//On recup les palettes liées a l'etage
		$palettes = $this->Palettes_model->get_palettesByHauteur($hauteur);

		//On commence le tableau
		echo "<tr>";
		//On boulce pour tout bien afficher
		for ($largeur=0; $largeur < $rail->largeur; $largeur++) {
			//On affiche les + qui serviront a ajouter une palette sur la ligne et l'étage
			echo "<td align='center' data-pos='$largeur'> <div style='width : 50px;' class='btn addPalette' data-pos='$largeur'>+</div>  </td>";
		}
		//On ferme la ligne
		echo "</tr>";

		//On commence le tableau de palette
		for ($i=0; $i < $rail->longueur; $i++) {
			echo "<tr>";
			for ($j=0; $j < $rail->largeur; $j++) {
				//Booléen pour savoir si la palette doit etre affichée ou non
				$test = 0;
				//Pour chaque palette
				foreach ($palettes as $palette) {
					//Si la palette correspond a la case i et j alors on va pouvoir l'afficher
					// NOTE Une requete serait peut etre plus rapide que de boucler
					if($palette->pos_longueur == $i && $palette->pos_largeur == $j){
						//La palette est alors affichées
						$test = 1;
						//La palette est sauvegardée pour plus tard
						$palette_save = $palette;
						//Les produits liées a la palette
						$produitsLies = $this->Produits_model->get_produitByPalette($palette->id_palette);
						//La string affichée en dessous de l'icone de palette
						$str = "";
						$title = "";
						//On boucle sur les produits
						foreach ($produitsLies as $produit) {
							$str .= "$produit->ue </br>";
							$title .= "$produit->nom_produit | $produit->code_produit</br>";
						}
						//Si la palette est totu en bas alors on affiche la fleche de transfert
						if($palette->pos_longueur == 10){
							$redoIcon =	'<span data-idpal='.$palette->id_palette.' class="btn grey transfert waves-effect waves-light" style="line-height: 12px;  padding : 0px ; position : absolute ; top : 1px ; right : 1px ; height: 20px;"><i class="material-icons">redo</i></span>';
						}else{
							//Sinon on affiche rien
							$redoIcon = "";
						}

					}
				}
				//Si il n'ya pas de palette on affiche une palette vide
				if($test == 0){
					echo "<td align='center' style='border : 1px solid black'></td>";
				}else{
					//Si la palette est une palette qui a été cherchée alors on colorie le fond
					if($palette_save->getSearch == 1 ){
						$styleAdd = 'background-color : #ff7043';
						//Sinon rien du tout
					}else{
						$styleAdd = "";
					}
					//On affiche la case comme il se doit
					echo "<td align='center' data-position='left' data-tooltip='$title' class='tooltip' data-id='$palette_save->id_palette' style='border : 1px solid black ; position : relative ; $styleAdd'><span class='palette' data-idpal='$palette_save->id_palette'>".img("pallete.png")."$str</span> $redoIcon</td>";
				}

			}
			echo "</tr>";
		}
		//La dernière ligne du tableau
		echo "<tr>";
		//Qui fait la largeur du tableau
		for ($largeur=0; $largeur < $rail->largeur; $largeur++) {
			//La case contient le 'moins' et le 'rotation' voir les actions dans le code JS
			echo "<td align='center' style='position : relative' data-pos='$largeur'>
			<span class='center floatPosH'>".($largeur+1)."</span>
			<div data-pos='$largeur' style='width : 50px;'  class='btn red remPalette'>-</div>
			<div data-pos='$largeur' style='margin-top : 10px ; width : 50px;'  class='btn blue rotation'><i class='material-icons'>cached</i></div>
			</td>";
		}
		//Fin de la ligne
		echo "</tr>";
	}

	/**
	* @brief Supprime la palette la plus en bas d'une colonne et descend les autres
	* @param etage L'etage ou faire la rotation
	* @param position La position/colonne
	*/
	public function remPalette($etage, $position){
		//Les palettes de l'étage
		$palettes = $this->Palettes_model->getPaletteByEtageAndPos($etage, $position);
		//La profondeur
		$posProfondeur = -1;
		//Pour chaque palette
		//NOTE Une requete SQL serait plus rapide
		foreach ($palettes as $palette) {
			//Si sa position est supérieur a la position la plus basse
			if($palette->pos_longueur > $posProfondeur){
				//Alors l'id de la palette change
				$idPalette = $palette->id_palette;
				//La nouvelel profondeur aussi
				$posProfondeur = $palette->pos_longueur;
			}
		}
		//Si la profonfdeur est toujours la meme
		if($posProfondeur == -1 ){
			echo "Erreur pas de palette dans cette colonne";
		}else{
			//On supprime les produits liées a la palette
			$this->Produits_model->deleteByIdPalette($idPalette);
			//Puis la palette
			$this->Palettes_model->deleteByIdPalette($idPalette);
			//On récupère toutes les palettes de la colonne et on les descend d'une case
			$this->Palettes_model->add1ToPalette($etage, $position);

		}
	}

	public function deletePalette($idPalette){
		$palToMove = $this->Palettes_model->get_palette($idPalette);
		$this->Palettes_model->add1ToPaletteUnderPosition($palToMove->pos_hauteur, $palToMove->pos_largeur, $palToMove->pos_longueur);
		//On supprime les produits liées a la palette
		$this->Produits_model->deleteByIdPalette($idPalette);
		//Puis la palette
		$this->Palettes_model->deleteByIdPalette($idPalette);
	}

	/**
	* @brief Ajoute une palette a une certaine postion et etage dans le rail
	* @param hauteur L'etage ou faire la rotation
	* @param colonne La position/colonne
	* @param nbrProduits Le nombdre de prouits liés a la palette
	*/
	public function addPalette($hauteur, $nbrProduits, $colonne){
		//On compte le nombre de palette dans cette colonne
		$numbPalette = $this->Palettes_model->countPalParRanger($hauteur, $colonne);
		//On recupere la profondeur
		$profondeur = 10 - $numbPalette->numbPal;
		//Si la profondeur est inferieur a 0 alors on ne peut plus ajouter de palette
		if($profondeur < 0 ){
			echo "Impossible de mettre plus de palettes sur cette colonne";
			return 0;
		}else{
			//On ajoute la palette au rail avec la postion
			$idPalette = $this->Palettes_model->ajouter_palette( "", $colonne, $profondeur, $hauteur);
			//On boucle sur le nombre de produits
			for ($compteur = 0; $compteur < $nbrProduits ; $compteur++) {
				//On recupère les données envoyées en post
				$nom_produit = $this->input->post('nom_produit'.$compteur);
				$code_produit = $this->input->post('code_produit'.$compteur);
				$ue = $this->input->post('ue'.$compteur);
				//$lot = $this->input->post('lot'.$compteur);
				//$qte = $this->input->post('qte'.$compteur);
				//$date_fabrication = $this->input->post('date_fab'.$compteur);
				//$dlc = $this->input->post('dlc'.$compteur);

				//Et on ajoute les produits a la palette
				$this->Produits_model->ajouter_produit($nom_produit, $code_produit, $ue, "","", "", "", intval($idPalette));
			}
		}
	}

	public function transfertPalette($idPalette, $colonne, $etage){
		$colonne = $colonne - 1;
		$numbPalette = $this->Palettes_model->countPalParRanger($etage, $colonne);
		if($numbPalette->numbPal == 11){
			echo 'Erreur il y a deja 11 palettes dans cette colonne';
		}else{
			$profondeur = 10 - $numbPalette->numbPal;
			$palToMove = $this->Palettes_model->get_palette($idPalette);
			$this->Palettes_model->changePosition($profondeur, $etage , $colonne, $idPalette);
			$this->Palettes_model->add1ToPalette($palToMove->pos_hauteur, $palToMove->pos_largeur);
		}
	}

}
