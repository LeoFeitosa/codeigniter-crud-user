<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

	public function check($email, $password)
	{
		$query = $this->db->get_where('users', array('email' => $email, 'password' => $password, 'active' => 1));
		return $query->result();
	}

	public function check_email($email)
	{
		$query = $this->db->get_where('users', array('email' => $email));
		return $query->result();
	}

	public function add($name, $email, $password, $nivel)
	{
		$data = array(
			'name' => $name,
			'email' => $email,
			'password' => $password,
			'nivel' => $nivel
		);

		$this->db->insert('users', $data);

		if($this->db->insert_id() > 0)
			return true;
		else
			return false;
	}
}
