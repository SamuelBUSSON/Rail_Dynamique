<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rail_model extends CI_Model
{
  protected $table = 'rail_dynamique';

  public function get_rail(){
    return
    $this->db
    ->select("*")
    ->from($this->table)
    ->get()
    ->row();
  }

}
