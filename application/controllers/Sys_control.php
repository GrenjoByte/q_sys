<?php
class Sys_control extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$_SERVER['warning_message'] = "<br><h1 align='center' style='color: red;'>System Administrator Data Compromised!<br>Please contact the Developer!</h1>";
	}
	private function require_login()
	{
		if (!$this->session->userdata('logged_in')) {
			header('Location: ' . base_url('i.php/sys_control/login'));
			exit;
		}
	}
	public function load_system_datetime()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->load_system_datetime();
	}
	public function index()
	{
		$this->load->view('index');
	}
	public function login()
	{
		$this->load->view('login.html');	
	}
	public function attempt_login()
	{
		$this->load->model('sys_model');
		$data = $this->sys_model->attempt_login();

		if ($data['status'] === 'success') {
			$this->session->set_userdata([
				'logged_in' => true,
				'user_id'   => $data['user_id'],
				'user_name' => $data['user_name'],
			]);

			// TEMP DEBUG
			error_log('SESSION SET: ' . json_encode($this->session->userdata()));
		}

		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}

	public function logout()
	{
		$this->session->sess_destroy();
		header('Location: ' . base_url('i.php/sys_control/login'));
		exit;
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
		$this->require_login();
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
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->complete_current_serving();
	}
	public function load_recent_transactions()
	{
		$this->load->model('sys_model');
		$this->sys_model->load_recent_transactions();
	}
	public function load_table_options()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->load_table_options();
	}
	public function grab_client()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->grab_client();
	}
	public function set_table_status()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->set_table_status();
	}
	public function create_account()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->create_account();
	}
	public function save_control_preferences()
	{
	    $this->require_login();
	    $this->load->model('sys_model');
	    $this->sys_model->save_control_preferences();
	}

	public function load_control_preferences()
	{
	    $this->require_login();
	    $this->load->model('sys_model');
	    $this->sys_model->load_control_preferences();
	}
	public function skip_current_serving()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->skip_current_serving();
	}
	public function unserve_client()
{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->unserve_client();
	}
	public function serve_previous()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->serve_previous();
	}
	public function grab_previous()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->grab_previous();
	}
	public function refresh_serving()
	{
		$this->require_login();
		$this->load->model('sys_model');
		$this->sys_model->refresh_serving();
	}
	public function sse_stream()
	{
		$this->load->model('sys_model');
		$this->sys_model->sse_stream();
	}
	public function update_transaction()
	{
		$this->load->model('sys_model');
		$this->sys_model->update_transaction();
	}
	public function clear_table()
	{
	    $this->require_login();
	    $this->load->model('sys_model');
	    $this->sys_model->clear_table();
	}
	public function load_all_tables_serving()
	{
	    $this->load->model('sys_model');
	    $this->sys_model->load_all_tables_serving();
	}
	public function queue_board()
	{
		$this->load->view('queue_board.html');	
	}
}
