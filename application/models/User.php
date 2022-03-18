<?php
class User extends CI_Model{

	function create($data){
		$this->db->insert('users', $data);
	}

    function getUserByEmail($email) {
        $this->db->where("email", $email);
		return $this->db->get('users')->row();

	} 

    function doLogin($email, $password) {
		$this->db->where('email', $email);
		$this->db->where('password', md5($password));
		return $this->db->get('users')->row();
	}

}