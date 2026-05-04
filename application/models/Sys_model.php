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
	    $username = $this->input->post('username', TRUE);
	    $password = $this->input->post('password', TRUE);

	    $sql = "SELECT user_id, username, password FROM user_accounts WHERE username = ?";
	    $query = $this->db->query($sql, [$username]);
	    $row = $query->row();

	    if ($row && $password === $row->password) {
	        $user_id = $row->user_id;

	        $sql = "SELECT user_type ,last_name, gender FROM user_info WHERE user_id = ?";
	        $query = $this->db->query($sql, [$user_id]);
	        $row = $query->row();

	        if ($row) {
	        	$_SESSION['active_user'] = $user_id;
	            $attempt_response = array(
	                'status' => 'success',
	                'user_type' => $row->user_type,
	                'last_name' => $row->last_name,
	                'gender' => $row->gender
	            );
	        } else {
	            $attempt_response = array(
	                'status' => 'error',
	                'user_type' => '',
	                'last_name' => '',
	                'gender' => 'failed'
	            );
	        }
	    } else {
	        $attempt_response = array(
	            'status' => 'error',
                'user_type' => '',
                'last_name' => '',
	            'gender' => 'Invalid username or password'
	        );
	    }

	    header('Content-Type: application/json');
	    echo json_encode($attempt_response);
	    exit;
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
					cd.first_name,
					cd.middle_name,
					cd.last_name
				FROM transaction_data td
				JOIN client_data cd ON cd.client_data_id = td.client_data_id
				WHERE 
					td.transaction_class     = ?
					AND td.priority_level    = ?
					AND td.transaction_schedule = ?
					AND td.transaction_status   = 0
				GROUP BY cd.first_name, cd.middle_name, cd.last_name
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
				'status'           => 'success',
				'current_sequence' => str_pad($current_sequence, 3, '0', STR_PAD_LEFT),
				'full_name'        => $full_name
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
}