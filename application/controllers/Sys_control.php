<?php
class Sys_control extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$allowed = ['login', 'attempt_login'];

        if (!in_array($this->router->fetch_method(), $allowed)) {
            if (!$this->session->userdata('active_user')) {
                // redirect('sys_control/login');
                // exit;
            }
        }
		$_SERVER['warning_message'] = "<br><h1 align='center' style='color: red;'>System Administrator Data Compromised!<br>Please contact the Developer!</h1>";
	}
	public function load_system_datetime()
	{
		$this->load->model('sys_model');
		if ($this->sys_model->admin_security_check() == TRUE) {
			if ((isset($_SESSION['534X39a']) AND isset($_SESSION['kJaW31i'])) AND (!is_null($_SESSION['534X39a']) AND !is_null($_SESSION['kJaW31i']))) {
				// is logged in
				$this->load->model('sys_model');
				$this->sys_model->load_system_datetime();
			}
			else {
				header('Location: '.base_url().'i.php/sys_control/login');
				die();
			}
		}
		else {
			echo $_SERVER['warning_message'];
			die();
		}
	}
	public function login()
	{
		$this->session->sess_destroy();
		$this->load->view('login');	
	}
	public function attempt_login()
	{
		$this->load->model('sys_model');	
		$this->sys_model->attempt_login();
	}
	public function queuing_window()
	{
		$this->load->view('queuing_window.html');	
	}
	public function styles()
	{
		$this->load->view('styles.css');	
	}
}