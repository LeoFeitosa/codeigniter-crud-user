<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');

		$this->config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'smtp.office365.com',
			'smtp_port' => 587,
			'smtp_user' => 'no-reply@universidadebrasil.edu.br',
			'smtp_pass' => 'Unibrasil@2017',
			'smtp_crypto' => 'tls',
			'mailtype' => 'html',
			'smtp_timeout' => '60',
			'charset' => 'iso-8859-1',
			'wordwrap' => TRUE,
			'newline' => "\r\n",
      'crlf' => "\r\n",
      'charset' => 'UTF-8',
		);
	}

	public function index()
	{
	}

	public function recover($recover_data)
	{
		$data['password'] = $recover_data['password'];
		$this->email->initialize($this->config);
		$this->email->from("no-reply@universidadebrasil.edu.br", 'Redip');
		$this->email->to($recover_data['email']);
		$this->email->subject('Recuperação de acesso');
		$this->email->message($this->load->view('recover-template', $data, TRUE));
		$this->email->attach(FCPATH . $path_file_ead);
		$this->email->attach(FCPATH . $path_file_presencial);

		if($this->email->send()) {
			return true;
		}
		else{
			return $this->email->print_debugger();
		}
	}
}
