<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('User', '', TRUE);
		$this->serverApiKey = "4gh0bGp0VjU2aE1VKM23BHNkNaR212";
	}

	function users_get(){
		$headers = apache_request_headers();
		$apiKey = (!isset($headers['api-key'])) ? '' : $headers['api-key'];
		if($this->serverApiKey != $apiKey){
			$message = 'Invalid api key';
		}
		else{
			$status = 0;
			$message = '';
			$email = $this->input->get('email',TRUE);
			$password = $this->input->get('password',TRUE);
	
			if(empty($email) || $email == null){	
				$message = "Ingresa el nombre de usuario";
			 }
			else if(empty($password)){	
				$message = "Ingresa la contraseña";
			 }
			$user = $this->User->doLogin($email, $password);
			if(count($user) > 0){
				$status = 0;
				$message = 'Usuario valido';
			}
			else{
				$message = "Usuario y/o contraseña incorrectos";
			}
		}

		return $this->output
		->set_content_type('application/json')
		->set_status_header(200)
		->set_output(json_encode(array(
			'status' => $status,
			'message' => $message,
		)));
    }

	public function users_post(){
		$status = 0;
		$message = '';

		$headers = apache_request_headers();
		$apiKey = (!isset($headers['api-key'])) ? '' : $headers['api-key'];
		if($this->serverApiKey != $apiKey){
			$message = 'Invalid api key';
		}
		else{
			$name = $this->input->post('name',TRUE);
			$email = $this->input->post('email',TRUE);
			$password = $this->input->post('password',TRUE);
			$confirmPassword = $this->input->post('confirm_password',TRUE);

			if(empty($name)){	
				$message = "Debes indicar el nombre";
			}
			else if(empty($email)){	
				$message = "Debes indicar tu e-mail";
			}
			else if(empty($password) || empty($confirmPassword)){	
				$message = "Debes indicar las contraseñas";
			}
			else if(strlen($password) < 5){	
				$message = "La contraseña debe tener más de 5 caracteres";
			}
			else if(!preg_match( "/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/", $email)){
				$message = "Tu e-mail es incorrecto";
			} 
			else if (md5($password) != md5($confirmPassword) ) {
				$message = "La contraseña de cofirmación no coincide";
			}
			else if(count($this->User->getUserByEmail($email)) > 0) {
				$message = "Su e-mail ya se encuentra registrado";
			}
			else{
				$data = array(
					"name" 			=> $name,
					"email"			=> $email,
					"password" 		=> md5($password),
					"status"		=> "Activo",
					"activo" 		=> 1,
					"created_at"	=> date('Y-m-d H:i:s'),
				);
				$this->User->create($data);

				if (empty($db_error)) {
					$status = 1;
					$message = 'El usuario se ha registrado correctamente';
				}
				else{
					$message = 'Tuvimos un problema, inténtalo nuevamente.';
				}
			}
		}
		return $this->output
		->set_content_type('application/json')
		->set_status_header(200)
		->set_output(json_encode(array(
			'status' => $status,
			'message' => $message,
		)));
	}
}
