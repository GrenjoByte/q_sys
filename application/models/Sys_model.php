<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Sys_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$_SESSION['ghi8Asd8'] = 'grenjo8';
		$_SESSION['1jhA3xBg'] = 'Renzo';
		$_SESSION['87gBAi89'] = 'Ferreras';
		$_SESSION['HyA23jas'] = 'Advincula';
		$_SESSION['oljnAS78'] = '1998-03-21';
		date_default_timezone_set('Asia/Manila');
	}
	public function load_system_datetime()
	{
		date_default_timezone_set('Asia/Manila');
		$current_time = date("h:i A");
		$current_date = new DateTime(date('Y-m-d'));		
		$previous_month = clone $current_date;
		$previous_month->modify('-1 month');
		
		$current_date = $current_date->format('Y-m-d');
		$previous_month = $previous_month->format('Y-m-d');

		$datetime_data = array(
			'current_time' => $current_time,
			'current_date' => $current_date,
			'previous_month' => $previous_month
		);
		echo json_encode([$datetime_data]);
	}
	public function attempt_login()
	{
		$username = $_POST['username'];
		$password = $_POST['password'];

		$params   = [];
		$params[] = $username;

		$sql = "SELECT 
					ud.user_id,
					ud.user_name,
					ud.user_password,
					ud.user_type
				FROM user_data ud
				WHERE ud.user_name = ?
				LIMIT 1
		";
		$query = $this->db->query($sql, $params);
		$row   = $query->row();

		if (!$row) {
			return [
				'status'  => 'error',
				'message' => 'Invalid username or password.'
			];
		}

		if (!password_verify($password, $row->user_password)) {
			return [
				'status'  => 'error',
				'message' => 'Invalid username or password.'
			];
		}

		$redirect_map = [
			1 => base_url('i.php/sys_control/admin'),
			2 => base_url('i.php/sys_control/admin'),
			8 => base_url('i.php/sys_control/admin')
		];

		$redirect = $redirect_map[$row->user_type] ?? base_url('i.php/sys_control/admin');

		return [
			'logged_in' => TRUE,
			'status'    => 'success',
			'message'   => 'Login successful.',
			'redirect'  => $redirect,
			'user_id'   => (int) $row->user_id,
			'user_name' => $row->user_name,
			'user_type' => (int) $row->user_type
		];
	}

	public function insert_new_transaction()
	{
		header('Content-Type: application/json');

		// Transaction-related inputs
		$transaction_type     = $_POST['transaction_type'];
		$transaction_class    = $_POST['transaction_class'];
		$priority_level       = $_POST['priority_level'];
		$transaction_schedule = $_POST['transaction_schedule'];

		// Sequence query params
		$params = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $transaction_schedule;

		$this->db->trans_start();

		// Get the current max sequence for this group
		$sql = "SELECT 
					MAX(CAST(transaction_sequence AS UNSIGNED)) AS current_sequence
				FROM transaction_data
				WHERE
					transaction_class    = ?
					AND priority_level       = ?
					AND transaction_schedule = ?
				FOR UPDATE";
		$query = $this->db->query($sql, $params);

		$row              = $query->row();
		$current_sequence = $row->current_sequence ?? 0;

		$transaction_sequence = str_pad($current_sequence + 1, 3, '0', STR_PAD_LEFT);

		// Client inputs
		$first_name  = $_POST['first_name'];
		$middle_name = $_POST['middle_name'] ?? null;
		$last_name   = $_POST['last_name'];
		$gender      = $_POST['gender'];
		$birthdate   = $_POST['birthdate'];

		// Insert client
		$sql = "INSERT INTO client_data 
					(first_name, 
					middle_name, 
					last_name, 
					gender, 
					birthdate)
				VALUES (?, ?, ?, ?, ?)";
		$this->db->query($sql, [
			$first_name,
			$middle_name,
			$last_name,
			$gender,
			$birthdate,
		]);

		$client_data_id = $this->db->insert_id();

		// Insert transaction
		$sql = "INSERT INTO transaction_data 
					(client_data_id, 
					transaction_type, 
					transaction_class, 
					transaction_sequence, 
					priority_level, 
					transaction_schedule, 
					transaction_status)
				VALUES (?, ?, ?, ?, ?, ?, ?)";
		$this->db->query($sql, [
			$client_data_id,
			$transaction_type,
			$transaction_class,
			$transaction_sequence,
			$priority_level,
			$transaction_schedule,
			0, // default status
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'success' => false,
				'message' => 'Transaction failed. Please try again.',
			]);
			return;
		}

		echo json_encode([
			'success'              => true,
			'message'              => 'Transaction created successfully.',
			'client_data_id'       => $client_data_id,
			'transaction_sequence' => $transaction_sequence,
		]);
	}

	public function load_current_serving()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$sql = "SELECT 
					MIN(CAST(td.transaction_sequence AS UNSIGNED)) AS current_sequence,
					td.transaction_class,
					td.priority_level,
					cd.first_name,
					cd.middle_name,
					cd.last_name
				FROM transaction_data td
				JOIN client_data cd ON cd.client_data_id = td.client_data_id
				WHERE 
					td.transaction_class        = ?
					AND td.priority_level       = ?
					AND td.transaction_schedule = ?
					AND td.transaction_status   = 2
				GROUP BY td.transaction_class, td.priority_level, cd.first_name, cd.middle_name, cd.last_name
				ORDER BY MIN(CAST(td.transaction_sequence AS UNSIGNED))
				LIMIT 1
		";
		$query = $this->db->query($sql, $params);

		$row              = $query->row();
		$current_sequence = $row->current_sequence ?? null;

		if ($current_sequence === null) {
			echo json_encode([
				'status'           => 'empty',
				'current_sequence' => null,
				'full_name'        => null
			]);
		} else {
			$full_name = trim(implode(' ', array_filter([
				$row->first_name,
				$row->middle_name,
				$row->last_name
			])));

			echo json_encode([
				'status'            => 'success',
				'current_sequence'  => str_pad($current_sequence, 3, '0', STR_PAD_LEFT),
				'transaction_class' => $row->transaction_class,
				'priority_level'    => $row->priority_level,
				'full_name'         => $full_name
			]);
		}
		exit;
	}
	public function load_queue_list()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$sql = "SELECT 
					td.transaction_sequence,
					td.transaction_type,
					td.transaction_class,
					td.priority_level,
					cd.first_name,
					cd.middle_name,
					cd.last_name
				FROM transaction_data td
				JOIN client_data cd ON cd.client_data_id = td.client_data_id
				WHERE 
					td.transaction_class        = ?
					AND td.priority_level       = ?
					AND td.transaction_schedule = ?
					AND td.transaction_status   = 0
				ORDER BY CAST(td.transaction_sequence AS UNSIGNED) ASC
		";
		$query = $this->db->query($sql, $params);

		$rows = $query->result();

		if (empty($rows)) {
			echo json_encode([
				'status'     => 'empty',
				'queue_list' => []
			]);
		} else {
			echo json_encode([
				'status'     => 'success',
				'queue_list' => $rows
			]);
		}
		exit;
	}

	public function complete_current_serving()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$sql = "UPDATE transaction_data
				SET transaction_status = 1
				WHERE 
					transaction_class        = ?
					AND priority_level       = ?
					AND transaction_schedule = ?
					AND transaction_status   = 0
				ORDER BY CAST(transaction_sequence AS UNSIGNED) ASC
				LIMIT 1
		";
		$this->db->query($sql, $params);

		if ($this->db->affected_rows() > 0) {
			echo json_encode([
				'status'  => 'success',
				'message' => 'Transaction completed successfully.'
			]);
		} else {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No active transaction found.'
			]);
		}
		exit;
	}
	public function load_recent_transactions()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$sql = "SELECT 
					td.transaction_sequence,
					td.transaction_type,
					td.transaction_class,
					td.priority_level,
					cd.first_name,
					cd.middle_name,
					cd.last_name
				FROM transaction_data td
				JOIN client_data cd ON cd.client_data_id = td.client_data_id
				WHERE 
					td.transaction_class        = ?
					AND td.priority_level       = ?
					AND td.transaction_schedule = ?
					AND td.transaction_status   = 1
				ORDER BY CAST(td.transaction_sequence AS UNSIGNED) DESC
				LIMIT 10
		";
		$query = $this->db->query($sql, $params);

		$rows = $query->result();

		if (empty($rows)) {
			echo json_encode([
				'status'       => 'empty',
				'transactions' => []
			]);
		} else {
			echo json_encode([
				'status'       => 'success',
				'transactions' => $rows
			]);
		}
		exit;
	}
	public function load_table_options()
	{
		header('Content-Type: application/json');

		$user_id = $this->session->userdata('user_id');

		$params   = [];
		$params[] = $user_id;

		$sql = "SELECT 
					table_id,
					table_value,
					table_status,
					IF(user_id = ?, 1, 0) AS is_mine
				FROM table_data
				ORDER BY table_id ASC
		";
		$query = $this->db->query($sql, $params);

		$rows = $query->result();

		if (empty($rows)) {
			echo json_encode([
				'status' => 'empty',
				'tables' => []
			]);
		} else {
			echo json_encode([
				'status' => 'success',
				'tables' => $rows
			]);
		}
		exit;
	}
	public function grab_client()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$table_id          = $_POST['table_id'];
		$user_id           = $this->session->userdata('user_id');
		$current_date      = date('Y-m-d');

		$this->db->trans_start();

		// Get the next client in queue
		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$sql = "SELECT 
					td.transaction_data_id,
					td.transaction_sequence,
					td.transaction_type,
					td.transaction_class,
					td.priority_level,
					cd.first_name,
					cd.middle_name,
					cd.last_name
				FROM transaction_data td
				JOIN client_data cd ON cd.client_data_id = td.client_data_id
				WHERE 
					td.transaction_class        = ?
					AND td.priority_level       = ?
					AND td.transaction_schedule = ?
					AND td.transaction_status   = 0
				ORDER BY CAST(td.transaction_sequence AS UNSIGNED) ASC
				LIMIT 1
				FOR UPDATE
		";
		$query = $this->db->query($sql, $params);
		$row   = $query->row();

		if (!$row) {
			$this->db->trans_rollback();
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No clients in queue.'
			]);
			exit;
		}

		// Mark as serving (status = 2) and assign table
		$update_params   = [];
		$update_params[] = $table_id;
		$update_params[] = $row->transaction_data_id;

		$sql = "UPDATE transaction_data
				SET 
					transaction_status = 2,
					table_id           = ?
				WHERE transaction_data_id = ?
		";
		$this->db->query($sql, $update_params);

		// Tie the table to the current user
		$table_params   = [];
		$table_params[] = $user_id;
		$table_params[] = $table_id;

		$sql = "UPDATE table_data
				SET 
					table_status = 2,
					user_id      = ?
				WHERE table_id = ?
		";
		$this->db->query($sql, $table_params);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to grab client. Please try again.'
			]);
			exit;
		}

		$full_name = trim(implode(' ', array_filter([
			$row->first_name,
			$row->middle_name,
			$row->last_name
		])));

		echo json_encode([
			'status'            => 'success',
			'message'           => 'Client grabbed successfully.',
			'current_sequence'  => str_pad($row->transaction_sequence, 3, '0', STR_PAD_LEFT),
			'transaction_class' => $row->transaction_class,
			'priority_level'    => $row->priority_level,
			'full_name'         => $full_name
		]);
		exit;
	}

	public function set_table_status()
	{
		header('Content-Type: application/json');

		$table_id = $_POST['table_id'];
		$user_id  = $this->session->userdata('user_id');

		// Check if table is already taken by another user
		$check_params   = [];
		$check_params[] = $table_id;

		$check = $this->db->query(
			"SELECT user_id, table_status FROM table_data WHERE table_id = ? LIMIT 1",
			$check_params
		)->row();

		if ($check && $check->table_status == 2 && $check->user_id !== NULL && $check->user_id != $user_id) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'This table is currently in use by another user.'
			]);
			exit;
		}

		// Null out ALL rows owned by this user and reset their status to 1
		$this->db->query(
			"UPDATE table_data SET table_status = 1, user_id = NULL WHERE user_id = ?",
			[$user_id]
		);

		// Claim new table and set status to 2
		$this->db->query(
			"UPDATE table_data SET table_status = 2, user_id = ? WHERE table_id = ?",
			[$user_id, $table_id]
		);

		// Use row count instead of affected_rows to handle re-selecting the same table
		$verify = $this->db->query(
			"SELECT table_id FROM table_data WHERE table_id = ? AND user_id = ? AND table_status = 2 LIMIT 1",
			[$table_id, $user_id]
		)->row();

		if ($verify) {
			echo json_encode([
				'status'  => 'success',
				'message' => 'Table status updated.'
			]);
		} else {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to update table status.'
			]);
		}
		exit;
	}
	public function create_account()
	{
		header('Content-Type: application/json');

		$first_name  = $_POST['first_name'];
		$middle_name = $_POST['middle_name'] ?? '';
		$last_name   = $_POST['last_name'];
		$email       = $_POST['email']       ?? '';
		$username    = $_POST['username'];
		$password    = $_POST['password'];
		$user_type   = $_POST['user_type'];

		// Check if username already exists
		$check = $this->db->query(
			"SELECT user_id FROM user_data WHERE user_name = ? LIMIT 1",
			[$username]
		)->row();

		if ($check) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Username already exists.'
			]);
			exit;
		}

		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$this->db->trans_start();

		// Insert into user_info first — it holds the primary key
		$this->db->query(
			"INSERT INTO user_info (first_name, middle_name, last_name, email) VALUES (?, ?, ?, ?)",
			[$first_name, $middle_name, $last_name, $email]
		);

		$new_user_id = $this->db->insert_id();

		// Insert into user_data using the same user_id
		$this->db->query(
			"INSERT INTO user_data (user_id, user_name, user_password, user_type) VALUES (?, ?, ?, ?)",
			[$new_user_id, $username, $hashed_password, $user_type]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to create account. Please try again.'
			]);
			exit;
		}

		echo json_encode([
			'status'  => 'success',
			'message' => 'Account created successfully.'
		]);
		exit;
	}
}