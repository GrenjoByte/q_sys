<?php
class Sys_control extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$_SERVER['warning_message'] = "<br><h1 align='center' style='color: red;'>System Administrator Data Compromised!<br>Please contact the Developer!</h1>";
	}
	public function load_system_datetime()
	{
		$this->load->model('sys_model');
		$this->sys_model->load_system_datetime();
	}
	public function index()
	{
		$this->load->view('index');
	}
	public function login()
	{
		session_destroy();	
		$this->load->view('login.html');	
	}
	public function insert_new_transaction()
	{
		$this->load->model('sys_model');	
		$this->sys_model->insert_new_transaction();
	}
	public function queuing()
	{
		$this->load->view('queuing.html');	
	}
	public function appointment()
	{
		$this->load->view('appointment.html');	
	}
  	public function admin()
	{
		$this->load->view('admin.html');	
	}
	public function load_current_serving()
	{
		$this->load->model('sys_model');
		$this->sys_model->load_current_serving();
	}
	public function load_queue_list()
	{
		$this->load->model('sys_model');
		$this->sys_model->load_queue_list();
	}
	public function complete_current_serving()
	{
		$this->load->model('sys_model');
		$this->sys_model->complete_current_serving();
	}
}
