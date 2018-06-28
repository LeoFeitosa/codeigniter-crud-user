<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->helper('email');
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

	public function add_edit()
	{
		$id = trim($this->input->post('id'));
		$name = trim($this->input->post('name'));
		$email = trim($this->input->post('email'));
		$password = $this->input->post('password');
		$nivel = trim($this->input->post('nivel'));

		if(empty($name) || empty($email) ||!valid_email($email) || empty($password) || empty($nivel)) {
			return $this->output
							->set_status_header(409)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Preencha os dados corretamente!')));
		}else{
			if($id) {
				$data = array_filter(array(
					'id' => $id,
					'name' => $name,
					'email' => $email,
					'password' => $password,
					'nivel' => $nivel
				));

				if($this->user_model->edit($data)) {
					return $this->output
								->set_content_type('application/json')
								->set_output(json_encode(array('success' => 'Atualizado com sucesso!')));
				}else{
					return $this->output
								->set_status_header(409)
								->set_content_type('application/json')
								->set_output(json_encode(array('error' => 'Erro ao atualizar!')));
				}
			}else{				
				$user = $this->user_model->check_email($email);

				if($user) {			
					return $this->output
								->set_status_header(409)
								->set_content_type('application/json')
								->set_output(json_encode(array('error' => 'Usuário ja cadastrado!')));
				}

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

	public function details($id)
	{
		$user = $this->user_model->details($id);
		if($user) {
			return $this->output
							->set_content_type('application/json')
							->set_output(json_encode(array('success' => $user)));
		}else{
			return $this->output
							->set_status_header(404)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Usuário não encontrado!')));
		}
	}

}
