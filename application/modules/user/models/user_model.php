<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

	public function check($email, $password)
	{
		$this->db->select('id, name, email, nivel');
		$this->db->where('email', $email);
		$this->db->where('password', $password);		
		$query = $this->db->get('users');
		return $query->result();
	}

	public function check_email($email)
	{
		$query = $this->db->get_where('users', array('email' => $email, 'active' => 1));
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

		return (($this->db->affected_rows() > 0) ? true : false);
	}

	public function edit($data)
	{
		$id =  $data['id'];
		unset($data['id']);
		$data['updated'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);
		$this->db->update('users', $data);

		return (($this->db->affected_rows() > 0) ? true : false);
	}

	public function details($id)
	{
		$this->db->select('id, nivel, name, email, active');
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->result();
	}

	public function new_password($id)
	{
		$password = bin2hex(openssl_random_pseudo_bytes(3));
		$data['password'] = sha1(md5(base64_encode($password)));
		$data['updated'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);
		$this->db->update('users', $data);

		return (($this->db->affected_rows() > 0) ? $password : false);
	}

	public function remove_active_session($user_id, $session_new)
	{
    $session_old = $this->db->select('session_id')
                    ->where('id', $user_id)
                    ->get('users')
                    ->row('session_id');

		if(!empty($session_old)) {
      $this->db->delete('ci_sessions', array('id' => $session_old)); 
		}

    $this->db->where('id', $user_id);
    $this->db->update('users', array('session_id' => $session_new));
	}
}
