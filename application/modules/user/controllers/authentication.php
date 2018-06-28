<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("user_model");
	}

	public function index()
	{
		$email = $this->input->post('email');
		$password = sha1(md5(base64_encode($this->input->post('password'))));

		$user = $this->user_model->check($email, $password);

		if($user) {
			$this->session->set_userdata("user", $user);
			return $this->output
						->set_content_type('application/json')
						->set_output(json_encode(array('success' => 'Login realizado com sucesso!')));
		}else{			
			return $this->output
						->set_status_header(401)
						->set_content_type('application/json')
						->set_output(json_encode(array('error' => 'Usuário ou senha incorretos!')));
		}
	}

	public function add()
	{
		$name = trim($this->input->post('name'));
		$email = trim($this->input->post('email'));
		$password = $this->input->post('password');
		$nivel = trim($this->input->post('nivel'));

		$user = $this->user_model->check_email($email);

		if($user) {			
			return $this->output
						->set_status_header(409)
						->set_content_type('application/json')
						->set_output(json_encode(array('error' => 'Usuário ja cadastrado!')));
		}

		if(empty($name) || empty($email) || empty($password) || empty($nivel)) {
			return $this->output
							->set_status_header(409)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Preencha os dados corretamente!')));
		}else{
			if($this->user_model->add($name, $email, sha1(md5(base64_encode($password))), $nivel)) {
				return $this->output
							->set_content_type('application/json')
							->set_output(json_encode(array('success' => 'Cadastrado com sucesso!')));
			}else{
				return $this->output
							->set_status_header(409)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Erro ao cadastrar!')));
			}
		}
	}
}
