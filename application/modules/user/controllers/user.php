<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('user');
		$this->load->helper('email');
		$this->load->model("user_model");
		$this->load->library('form_validation');
	}

	public function index()
	{
		$email = $this->input->post('email');
		$password = sha1(md5(base64_encode($this->input->post('password'))));
		$session_id = session_id();
		
		if($user = $this->user_model->check($email, $password, $session_id)) {
			$this->session->set_userdata("user", $user);

			$this->user_model->remove_active_session($user[0]->id, session_id());

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

	public function logout()
	{
		if(is_logged()) {
			$this->session->unset_userdata('user'); 
			$this->session->sess_destroy();

			return $this->output
							->set_content_type('application/json')
							->set_output(json_encode(array('success' => 'Logout realizado com sucesso!')));
		}else{
			return $this->output
							->set_status_header(401)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Sessão inexistente!')));
		}
	}

	public function add_edit()
	{
		if(!is_logged()) {
			return $this->output
							->set_status_header(401)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Sessão inexistente!')));
		}

		$this->form_validation->set_rules('name', 'Nome', 'trim|required|min_length[5]|max_length[30]');
		$this->form_validation->set_rules('password', 'Senha', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('passconf', 'Confirmação de senha', 'trim|required|matches[password]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('nivel', 'Nivel', 'trim|required|max_length[1]');

		if($this->form_validation->run() == FALSE) {
			return $this->output
							->set_status_header(409)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => validation_errors())));
		}else{
			
			$id = trim($this->input->post('id'));
			$name = trim($this->input->post('name'));
			$email = trim($this->input->post('email'));
			$password = $this->input->post('password');
			$nivel = trim($this->input->post('nivel'));

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
		if(!is_logged()) {
			return $this->output
							->set_status_header(401)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Sessão inexistente!')));
		}

		if($user = $this->user_model->details($id)) {
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

	public function recover()
	{
		$email = trim($this->input->post('email'));
		if(valid_email($email)) {
			if($user = (array)$this->user_model->check_email($email)[0]) {

				if($new_password = $this->user_model->new_password($user['id'])) {
					$data = array(
						'password' => $new_password,
						'email' => $email
					);

					$send_email = modules::run('email/email/recover', $data);				

					if($send_email != true) {
						return $this->output
									->set_status_header(404)
									->set_content_type('application/json')
									->set_output(json_encode(array('error' => $send_email)));
					}else{
						return $this->output
									->set_status_header(404)
									->set_content_type('application/json')
									->set_output(json_encode(array('success' => 'Check seu email e siga as instruções para recuperar sua senha!')));
					}
				}else{
					return $this->output
									->set_status_header(404)
									->set_content_type('application/json')
									->set_output(json_encode(array('error' => 'Problema ao gerar nova senha!')));
				}
			}else{
				return $this->output
								->set_status_header(404)
								->set_content_type('application/json')
								->set_output(json_encode(array('error' => 'Cadastro não encontrado!')));
			}
		}else{
			return $this->output
							->set_status_header(404)
							->set_content_type('application/json')
							->set_output(json_encode(array('error' => 'Informe um email valido!')));
		}
	}
}
