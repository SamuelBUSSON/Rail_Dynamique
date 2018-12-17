<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Produits_model extends CI_Model
{
  protected $table = 'produit';

  public function get_produits(){
    return
    $this->db
    ->select("*")
    ->from($this->table)
    ->get()
    ->result();
  }

  public function getByNomProduit($nom){
    $query = $this->db->query("SELECT * FROM `produit` WHERE `nom_produit` LIKE '%$nom%'");
    return $query->result();
  }

  public function get_produit($id){
    return
    $this->db
    ->select("*")
    ->from($this->table)
    ->where("id_produit = $id")
    ->get()
    ->row();
  }

  public function get_produitByPalette($id){
    return
    $this->db
    ->select("*")
    ->from($this->table)
    ->where("fk_palette = $id")
    ->get()
    ->result();
  }

  public function get_produitByUE($ue){
    return
    $this->db
    ->select("*")
    ->from($this->table)
    ->where("ue = $ue")
    ->get()
    ->row();
  }

  public function get_produitByCode($code){
    $query = $this->db->query("SELECT * FROM `produit` WHERE `code_produit` LIKE '%$code%'");
    return $query->result();
  }


  public function ajouter_produit( $nom_produit, $code_produit, $ue, $lot, $qte, $date_fabrication, $dlc, $fk_palette)
  {
    $value = array(
    'nom_produit' =>  $nom_produit,
    'code_produit' => $code_produit,
    'ue' => $ue,
    'lot' => $lot,
    'qte' => $qte,
    'date_fabrication' => $date_fabrication,
    'dlc' => $dlc,
    'fk_palette' => $fk_palette);

    $this->db->insert($this->table, $value);
    return $this->db->insert_id();
  }

  public function deleteByIdPalette($idPal){
    $this->db->where('fk_palette', $idPal);
    $this->db->delete($this->table);
  }

}
