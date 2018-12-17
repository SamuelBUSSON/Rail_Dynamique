<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Palettes_model extends CI_Model
{

	protected $table = 'palettes';

	public function ajouter_palette( $commentaire, $pos_largeur, $pos_longueur, $pos_hauteur)
	{
		$value = array(
									'commentaire' => $commentaire,
									'pos_largeur' => $pos_largeur,
									'pos_longueur' => $pos_longueur,
									'pos_hauteur' => $pos_hauteur);

		$id_palette = $this->db->insert($this->table, $value);
		return $this->db->insert_id();

	}

	public function countPalParRanger($hauteur, $colonne){
		$query = $this->db->query("SELECT count(*) as 'numbPal' FROM `palettes` WHERE `pos_largeur` = $colonne AND `pos_hauteur` = $hauteur");
		return $query->row();
	}

	public function get_palettes(){
		return
		$this->db
		->select("*")
		->from($this->table)
		->get()
		->result();
	}

	public function get_palettesByHauteur($hauteur){
		return
		$this->db
		->select("*")
		->from($this->table)
		->where("pos_hauteur = $hauteur")
		->get()
		->result();
	}

	public function getPaletteByEtageAndPos($etage, $positon){
		return
		$this->db
		->select("*")
		->from($this->table)
		->where('pos_hauteur', $etage)
		->where('pos_largeur', $positon)
		->get()
		->result();
	}

	public function add1ToPalette($etage, $colonne){
		$sql = "UPDATE `palettes` SET `pos_longueur` = `pos_longueur`+1 WHERE `pos_largeur` = $colonne AND `pos_hauteur` = $etage";
		return $this->db->query($sql);
	}

	public function add1ToPaletteUnderPosition($etage, $colonne, $position){
		$sql = "UPDATE `palettes` SET `pos_longueur` = `pos_longueur`+1 WHERE `pos_largeur` = $colonne AND `pos_hauteur` = $etage AND `pos_longueur` < $position";
		return $this->db->query($sql);
	}

	public function getMinPaletteByEtageAndPos($etage, $positon){
		$sql = "SELECT * FROM `palettes` WHERE `pos_largeur` = ? AND `pos_hauteur` = ? AND  `pos_longueur` in (select min(`pos_longueur`) FROM `palettes`)";
		return $this->db->query($sql, array($positon, $etage))->row();
	}

	public function getMaxPaletteByEtageAndPos($etage, $positon){
		$sql = "SELECT * FROM `palettes` WHERE `pos_largeur` = ? AND `pos_hauteur` = ? AND  `pos_longueur` in (select max(`pos_longueur`) FROM `palettes`)";
		return $this->db->query($sql, array($positon, $etage))->row();
	}

	public function changeProfondeur($newProfondeur, $idPalette){
		$data = array(
		        'pos_longueur' => $newProfondeur
		);

		$this->db->where('id_palette', $idPalette);
		$this->db->update($this->table, $data);
	}

	public function changePosition($newProfondeur, $newEtage , $newColonne, $idPalette){
		$data = array(
						'pos_largeur' => $newColonne,
						'pos_longueur' => $newProfondeur,
						'pos_hauteur' => $newEtage,
		);
		$this->db->where('id_palette', $idPalette);
		$this->db->update($this->table, $data);
	}

	public function setSearch($idPalette){
		$data = array(
						'getSearch' => 1
		);

		$this->db->where('id_palette', $idPalette);
		$this->db->update($this->table, $data);
	}

	public function setSearchForAll(){
		$data = array(
						'getSearch' => 0
		);
		$this->db->update($this->table, $data);
	}


	public function deleteByIdPalette($idPal){
		$this->db->where('id_palette', $idPal);
		$this->db->delete($this->table);
	}

	public function get_palette($id){
		return
		$this->db
		->select("*")
		->from($this->table)
		->where("id_palette = $id")
		->get()
		->row();
	}


}
