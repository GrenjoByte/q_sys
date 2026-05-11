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
	private function broadcast_update()
	{
		// Touch a lightweight timestamp table to signal state change
		$this->db->query(
			"INSERT INTO sse_state (state_key, updated_at) 
			VALUES ('queue', NOW()) 
			ON DUPLICATE KEY UPDATE updated_at = NOW()"
		);
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

		$transaction_class    = $_POST['transaction_class'];
		$priority_level       = $_POST['priority_level'];
		$transaction_schedule = $_POST['transaction_schedule'];
		$first_name           = $_POST['first_name'];
		$middle_name          = $_POST['middle_name'] ?? null;
		$last_name            = $_POST['last_name'];
		$gender               = $_POST['gender'];
		$birthdate            = $_POST['birthdate'];
		$transaction_type     = $_POST['transaction_type'];

		// Check if a client with same name and birthdate already has a transaction today
		$existing = $this->db->query(
		    "SELECT 
		         td.transaction_data_id,
		         td.transaction_sequence,
		         td.transaction_class,
		         td.priority_level,
		         td.transaction_status
		     FROM transaction_data td
		     JOIN client_data cd ON cd.client_data_id = td.client_data_id
		     WHERE 
		         cd.first_name               = ?
		         AND cd.last_name            = ?
		         AND cd.birthdate            = ?
		         AND td.transaction_schedule = ?
		         AND td.transaction_status   NOT IN (1)
		     ORDER BY td.transaction_data_id DESC
		     LIMIT 1",
		    [$first_name, $last_name, $birthdate, $transaction_schedule]
		)->row();

		// In insert_new_transaction duplicate check, add transaction_data_id to response:
		if ($existing) {
		    $priority_prefix = $existing->priority_level == 1 ? 'P' : 'R';
		    $class_map       = [
		        1 => ['prefix' => 'R'],
		        2 => ['prefix' => 'U'],
		        3 => ['prefix' => 'P'],
		        4 => ['prefix' => 'I']
		    ];
		    $type_prefix   = $class_map[$existing->transaction_class]['prefix'] ?? 'R';
		    $sequence      = str_pad($existing->transaction_sequence, 3, '0', STR_PAD_LEFT);
		    $existing_code = "{$priority_prefix}:{$type_prefix}-{$sequence}";

		    echo json_encode([
		        'status'              => 'duplicate',
		        'message'             => 'You already have an existing appointment today.',
		        'transaction_code'    => $existing_code,
		        'transaction_data_id' => $existing->transaction_data_id
		    ]);
		    exit;
		}

		$params = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $transaction_schedule;

		$this->db->trans_start();

		$sql = "SELECT 
					MAX(CAST(transaction_sequence AS UNSIGNED)) AS current_sequence
				FROM transaction_data
				WHERE 
					transaction_class    = ?
					AND priority_level   = ?
					AND transaction_schedule = ?
				FOR UPDATE
		";
		$query = $this->db->query($sql, $params);

		$row              = $query->row();
		$current_sequence = $row->current_sequence ?? 0;

		$transaction_sequence = str_pad($current_sequence + 1, 3, '0', STR_PAD_LEFT);

		$sql = "INSERT INTO client_data 
					(first_name, middle_name, last_name, gender, birthdate)
				VALUES (?, ?, ?, ?, ?)";
		$this->db->query($sql, [
			$first_name,
			$middle_name,
			$last_name,
			$gender,
			$birthdate,
		]);

		$client_data_id = $this->db->insert_id();

		$sql = "INSERT INTO transaction_data 
					(client_data_id,
					transaction_type,
					transaction_class,
					transaction_sequence,
					priority_level,
					transaction_schedule,
					transaction_status,
					table_id)
				VALUES (?, ?, ?, ?, ?, ?, ?, NULL)";
		$this->db->query($sql, [
			$client_data_id,
			$transaction_type,
			$transaction_class,
			$transaction_sequence,
			$priority_level,
			$transaction_schedule,
			0,
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'success' => false,
				'message' => 'Transaction failed. Please try again.'
			]);
			return;
		}

		$this->broadcast_update();

		echo json_encode([
			'success'              => true,
			'message'              => 'Transaction created successfully.',
			'client_data_id'       => $client_data_id,
			'transaction_sequence' => $transaction_sequence
		]);
	}
	public function update_transaction()
	{
	    header('Content-Type: application/json');

	    $transaction_data_id  = $_POST['transaction_data_id'];
	    $transaction_class    = $_POST['transaction_class'];
	    $priority_level       = $_POST['priority_level'];
	    $transaction_schedule = $_POST['transaction_schedule'];

	    // Only allow update if transaction is still pending
	    $check = $this->db->query(
	        "SELECT transaction_data_id, transaction_status
	         FROM transaction_data
	         WHERE transaction_data_id = ? AND transaction_status = 0
	         LIMIT 1",
	        [$transaction_data_id]
	    )->row();

	    if (!$check) {
	        echo json_encode([
	            'success' => false,
	            'message' => 'This transaction can no longer be updated.'
	        ]);
	        exit;
	    }

	    $this->db->query(
	        "UPDATE transaction_data
	         SET 
	             transaction_class    = ?,
	             priority_level       = ?,
	             transaction_schedule = ?
	         WHERE transaction_data_id = ?",
	        [$transaction_class, $priority_level, $transaction_schedule, $transaction_data_id]
	    );

	    if ($this->db->affected_rows() > 0) {
	        $this->broadcast_update();
	        echo json_encode([
	            'success' => true,
	            'message' => 'Transaction updated successfully.'
	        ]);
	    } else {
	        echo json_encode([
	            'success' => false,
	            'message' => 'No changes were made.'
	        ]);
	    }
		$this->broadcast_update();
	    exit;
	}
	public function load_current_serving()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'           => 'empty',
				'current_sequence' => null,
				'full_name'        => null
			]);
			exit;
		}

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;
		$params[] = $table->table_id;

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
					AND td.table_id             = ?
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
					AND td.transaction_status = 0
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
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No table assigned to you.'
			]);
			exit;
		}

		// Check if there is an active transaction tied to this table
		$active = $this->db->query(
			"SELECT transaction_data_id FROM transaction_data
			WHERE 
				transaction_class        = ?
				AND priority_level       = ?
				AND transaction_schedule = ?
				AND transaction_status   = 2
				AND table_id             = ?
			LIMIT 1",
			[$transaction_class, $priority_level, $current_date, $table->table_id]
		)->row();

		if (!$active) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No active transaction found for your table.'
			]);
			exit;
		}

		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 1
			WHERE transaction_data_id = ?",
			[$active->transaction_data_id]
		);

		if ($this->db->affected_rows() > 0) {
			echo json_encode([
				'status'  => 'success',
				'message' => 'Transaction completed successfully.'
			]);
		} else {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to complete transaction. Please try again.'
			]);
		}
		$this->broadcast_update();
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

		// Block if this table is already serving a client
		$active = $this->db->query(
			"SELECT transaction_data_id FROM transaction_data
			WHERE table_id = ? AND transaction_status = 2 LIMIT 1",
			[$table_id]
		)->row();

		if ($active) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Please complete the current transaction before serving a new client.'
			]);
			exit;
		}

		$this->db->trans_start();

		$exclude_id   = $_POST['exclude_id']   ?? null;
		$min_sequence = $_POST['min_sequence'] ?? null;

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$exclude_clause  = '';
		$sequence_clause = '';

		if ($exclude_id) {
			$exclude_clause = 'AND td.transaction_data_id != ?';
			$params[]       = $exclude_id;
		}

		if ($min_sequence) {
			$sequence_clause = 'AND CAST(td.transaction_sequence AS UNSIGNED) > ?';
			$params[]        = (int) $min_sequence;
		}

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
					AND td.table_id             IS NULL
					{$exclude_clause}
					{$sequence_clause}
				ORDER BY CAST(td.transaction_sequence AS UNSIGNED) ASC
				LIMIT 1
				FOR UPDATE
		";
		$query = $this->db->query($sql, $params);
		$row   = $query->row();

		// If no client found ahead in queue, wrap around to start
		if (!$row && $min_sequence) {
			$wrap_params   = [];
			$wrap_params[] = $transaction_class;
			$wrap_params[] = $priority_level;
			$wrap_params[] = $current_date;

			$wrap_exclude = '';
			if ($exclude_id) {
				$wrap_exclude   = 'AND td.transaction_data_id != ?';
				$wrap_params[]  = $exclude_id;
			}

			$wrap_sql = "SELECT 
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
							AND td.table_id             IS NULL
							{$wrap_exclude}
						ORDER BY CAST(td.transaction_sequence AS UNSIGNED) ASC
						LIMIT 1
						FOR UPDATE
			";
			$row = $this->db->query($wrap_sql, $wrap_params)->row();
		}

		if (!$row) {
			$this->db->trans_rollback();
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No clients available in queue.'
			]);
			exit;
		}

		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 2, table_id = ?
			WHERE transaction_data_id = ?",
			[$table_id, $row->transaction_data_id]
		);

		$this->db->query(
			"UPDATE table_data SET table_status = 2, user_id = ? WHERE table_id = ?",
			[$user_id, $table_id]
		);

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
		$this->broadcast_update();
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
		$this->broadcast_update();
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

	public function save_control_preferences()
	{
		header('Content-Type: application/json');

		$user_id          = $this->session->userdata('user_id');
		$priority_level   = $_POST['priority_level'];
		$transaction_class = $_POST['transaction_class'];
		$counter_window   = $_POST['counter_window'];
		$table_id         = $_POST['table_id'] ?? null;

		// Check if preferences already exist for this user
		$existing = $this->db->query(
			"SELECT user_id FROM user_preferences WHERE user_id = ? LIMIT 1",
			[$user_id]
		)->row();

		if ($existing) {
			$this->db->query(
				"UPDATE user_preferences 
				SET priority_level    = ?,
					transaction_class = ?,
					counter_window    = ?,
					table_id          = ?
				WHERE user_id = ?",
				[$priority_level, $transaction_class, $counter_window, $table_id, $user_id]
			);
		} else {
			$this->db->query(
				"INSERT INTO user_preferences (user_id, priority_level, transaction_class, counter_window, table_id)
				VALUES (?, ?, ?, ?, ?)",
				[$user_id, $priority_level, $transaction_class, $counter_window, $table_id]
			);
		}

		echo json_encode(['status' => 'success']);
		exit;
	}

	public function load_control_preferences()
	{
		header('Content-Type: application/json');

		$user_id = $this->session->userdata('user_id');

		$row = $this->db->query(
			"SELECT priority_level, transaction_class, counter_window, table_id
			FROM user_preferences
			WHERE user_id = ? LIMIT 1",
			[$user_id]
		)->row();

		if (!$row) {
			echo json_encode(['status' => 'empty']);
			exit;
		}

		echo json_encode([
			'status'           => 'success',
			'priority_level'   => $row->priority_level,
			'transaction_class'=> $row->transaction_class,
			'counter_window'   => $row->counter_window,
			'table_id'         => $row->table_id
		]);
		exit;
	}
	public function skip_current_serving()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No table assigned to you.'
			]);
			exit;
		}

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;
		$params[] = $table->table_id;

		// Get the currently serving transaction for this table
		$current = $this->db->query(
			"SELECT transaction_data_id, transaction_sequence
			FROM transaction_data
			WHERE 
				transaction_class        = ?
				AND priority_level       = ?
				AND transaction_schedule = ?
				AND transaction_status   = 2
				AND table_id             = ?
			ORDER BY CAST(transaction_sequence AS UNSIGNED) ASC
			LIMIT 1",
			$params
		)->row();

		if (!$current) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No active transaction to skip.'
			]);
			exit;
		}

		// Set status to 0 (pending) and clear table_id
		$this->db->query(
			"UPDATE transaction_data
			SET 
				transaction_status = 0,
				table_id           = NULL
			WHERE transaction_data_id = ?",
			[$current->transaction_data_id]
		);

		if ($this->db->affected_rows() > 0) {
			echo json_encode([
				'status'                   => 'success',
				'message'                  => 'Client skipped successfully.',
				'skipped_transaction_id'   => $current->transaction_data_id,
				'skipped_sequence'         => (int) $current->transaction_sequence
			]);
		} else {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to skip transaction. Please try again.'
			]);
		}
		$this->broadcast_update();
		exit;
	}
	public function unserve_client()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No table assigned to you.'
			]);
			exit;
		}

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;
		$params[] = $table->table_id;

		// Get the currently serving transaction for this table
		$current = $this->db->query(
			"SELECT transaction_data_id
			FROM transaction_data
			WHERE 
				transaction_class        = ?
				AND priority_level       = ?
				AND transaction_schedule = ?
				AND transaction_status   = 2
				AND table_id             = ?
			ORDER BY CAST(transaction_sequence AS UNSIGNED) ASC
			LIMIT 1",
			$params
		)->row();

		if (!$current) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No active transaction to unserve.'
			]);
			exit;
		}

		// Reset back to queue
		$this->db->query(
			"UPDATE transaction_data
			SET 
				transaction_status = 0,
				table_id           = NULL
			WHERE transaction_data_id = ?",
			[$current->transaction_data_id]
		);

		if ($this->db->affected_rows() > 0) {
			echo json_encode([
				'status'  => 'success',
				'message' => 'Client returned to queue.'
			]);
		} else {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to unserve client. Please try again.'
			]);
		}
		$this->broadcast_update();
		exit;
	}
	
	public function serve_previous()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No table assigned to you.'
			]);
			exit;
		}

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;
		$params[] = $table->table_id;

		// Get currently serving transaction
		$current = $this->db->query(
			"SELECT transaction_data_id, transaction_sequence
			FROM transaction_data
			WHERE 
				transaction_class        = ?
				AND priority_level       = ?
				AND transaction_schedule = ?
				AND transaction_status   = 2
				AND table_id             = ?
			ORDER BY CAST(transaction_sequence AS UNSIGNED) ASC
			LIMIT 1",
			$params
		)->row();

		if (!$current) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No active transaction to go back from.'
			]);
			exit;
		}

		// Reset current serving back to queue
		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 0, table_id = NULL
			WHERE transaction_data_id = ?",
			[$current->transaction_data_id]
		);

		echo json_encode([
			'status'                 => 'success',
			'message'                => 'Going back to previous client.',
			'current_transaction_id' => $current->transaction_data_id,
			'current_sequence'       => (int) $current->transaction_sequence
		]);
		$this->broadcast_update();
		exit;
	}
	public function grab_previous()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$table_id          = $_POST['table_id'];
		$user_id           = $this->session->userdata('user_id');
		$current_date      = date('Y-m-d');
		$exclude_id        = $_POST['exclude_id']   ?? null;
		$max_sequence      = $_POST['max_sequence'] ?? null;

		// Block if this table is already serving a client
		$active = $this->db->query(
			"SELECT transaction_data_id FROM transaction_data
			WHERE table_id = ? AND transaction_status = 2 LIMIT 1",
			[$table_id]
		)->row();

		if ($active) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Please complete the current transaction before going back.'
			]);
			exit;
		}

		$this->db->trans_start();

		$params   = [];
		$params[] = $transaction_class;
		$params[] = $priority_level;
		$params[] = $current_date;

		$exclude_clause  = '';
		$sequence_clause = '';

		if ($exclude_id) {
			$exclude_clause = 'AND td.transaction_data_id != ?';
			$params[]       = $exclude_id;
		}

		if ($max_sequence) {
			$sequence_clause = 'AND CAST(td.transaction_sequence AS UNSIGNED) < ?';
			$params[]        = (int) $max_sequence;
		}

		// Grab the client with the highest sequence below current (previous)
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
					AND td.table_id             IS NULL
					{$exclude_clause}
					{$sequence_clause}
				ORDER BY CAST(td.transaction_sequence AS UNSIGNED) DESC
				LIMIT 1
				FOR UPDATE
		";
		$query = $this->db->query($sql, $params);
		$row   = $query->row();

		// Wrap around to highest sequence if no previous found
		if (!$row && $max_sequence) {
			$wrap_params   = [];
			$wrap_params[] = $transaction_class;
			$wrap_params[] = $priority_level;
			$wrap_params[] = $current_date;

			$wrap_exclude = '';
			if ($exclude_id) {
				$wrap_exclude  = 'AND td.transaction_data_id != ?';
				$wrap_params[] = $exclude_id;
			}

			$wrap_sql = "SELECT 
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
							AND td.table_id             IS NULL
							{$wrap_exclude}
						ORDER BY CAST(td.transaction_sequence AS UNSIGNED) DESC
						LIMIT 1
						FOR UPDATE
			";
			$row = $this->db->query($wrap_sql, $wrap_params)->row();
		}

		if (!$row) {
			$this->db->trans_rollback();
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No previous client available in queue.'
			]);
			exit;
		}

		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 2, table_id = ?
			WHERE transaction_data_id = ?",
			[$table_id, $row->transaction_data_id]
		);

		$this->db->query(
			"UPDATE table_data SET table_status = 2, user_id = ? WHERE table_id = ?",
			[$user_id, $table_id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to go back. Please try again.'
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
			'message'           => 'Now serving previous client.',
			'current_sequence'  => str_pad($row->transaction_sequence, 3, '0', STR_PAD_LEFT),
			'transaction_class' => $row->transaction_class,
			'priority_level'    => $row->priority_level,
			'full_name'         => $full_name
		]);
		$this->broadcast_update();
		exit;
	}
	public function refresh_serving()
	{
		header('Content-Type: application/json');
		date_default_timezone_set('Asia/Manila');

		$transaction_class = $_POST['transaction_class'];
		$priority_level    = $_POST['priority_level'];
		$current_date      = date('Y-m-d');
		$user_id           = $this->session->userdata('user_id');

		// Get the table assigned to this user
		$table = $this->db->query(
			"SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
			[$user_id]
		)->row();

		if (!$table) {
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No table assigned to you.'
			]);
			exit;
		}

		$this->db->trans_start();

		// Reset current serving back to queue if any
		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 0, table_id = NULL
			WHERE 
				transaction_status = 2
				AND table_id       = ?",
			[$table->table_id]
		);

		// Grab the lowest sequence unserved client
		$row = $this->db->query(
			"SELECT 
				td.transaction_data_id,
				td.transaction_sequence,
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
				AND td.table_id             IS NULL
			ORDER BY CAST(td.transaction_sequence AS UNSIGNED) ASC
			LIMIT 1
			FOR UPDATE",
			[$transaction_class, $priority_level, $current_date]
		)->row();

		if (!$row) {
			$this->db->trans_rollback();
			echo json_encode([
				'status'  => 'empty',
				'message' => 'No clients available in queue.'
			]);
			exit;
		}

		// Assign to this table
		$this->db->query(
			"UPDATE transaction_data
			SET transaction_status = 2, table_id = ?
			WHERE transaction_data_id = ?",
			[$table->table_id, $row->transaction_data_id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to refresh. Please try again.'
			]);
			exit;
		}

		echo json_encode([
			'status'  => 'success',
			'message' => 'Refreshed to lowest unserved client.'
		]);
		$this->broadcast_update();
		exit;
	}
	public function sse_stream()
	{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		header('X-Accel-Buffering: no');
		header('Connection: keep-alive');

		if (ob_get_level()) ob_end_clean();

		session_write_close(); // release session lock so other requests aren't blocked

		date_default_timezone_set('Asia/Manila');

		$last_state = null;

		while (true) {
			$row = $this->db->query(
				"SELECT updated_at FROM sse_state WHERE state_key = 'queue' LIMIT 1"
			)->row();

			$current_state = $row->updated_at ?? null;

			if ($current_state !== $last_state) {
				$last_state = $current_state;
				echo "event: queue_update\n";
				echo "data: " . json_encode(['timestamp' => $current_state]) . "\n\n";
				flush();
			}

			if (connection_aborted()) break;

			sleep(1);
		}
	}
	public function clear_table()
	{
	    header('Content-Type: application/json');
	    date_default_timezone_set('Asia/Manila');

	    $user_id = $this->session->userdata('user_id');

	    // Get the table assigned to this user
	    $table = $this->db->query(
	        "SELECT table_id FROM table_data WHERE user_id = ? AND table_status = 2 LIMIT 1",
	        [$user_id]
	    )->row();

	    if (!$table) {
	        echo json_encode([
	            'status'  => 'empty',
	            'message' => 'No table currently assigned to you.'
	        ]);
	        exit;
	    }

	    $this->db->trans_start();

	    // Return any active transaction on this table back to queue
	    $this->db->query(
	        "UPDATE transaction_data
	         SET transaction_status = 0, table_id = NULL
	         WHERE table_id = ? AND transaction_status = 2",
	        [$table->table_id]
	    );

	    // Release the table
	    $this->db->query(
	        "UPDATE table_data
	         SET table_status = 1, user_id = NULL
	         WHERE table_id = ?",
	        [$table->table_id]
	    );

	    $this->db->trans_complete();

	    if ($this->db->trans_status() === false) {
	        echo json_encode([
	            'status'  => 'error',
	            'message' => 'Failed to clear table. Please try again.'
	        ]);
	        exit;
	    }

	    $this->broadcast_update();

	    echo json_encode([
	        'status'  => 'success',
	        'message' => 'Table cleared successfully.'
	    ]);
	    exit;
	}
	public function load_all_tables_serving()
	{
	    header('Content-Type: application/json');
	    date_default_timezone_set('Asia/Manila');

	    $current_date = date('Y-m-d');

	    $sql = "SELECT 
	                t.table_id,
	                t.table_value,
	                t.table_status,
	                t.user_id,
	                td.transaction_sequence AS current_sequence,
	                td.transaction_class,
	                td.priority_level,
	                cd.first_name,
	                cd.middle_name,
	                cd.last_name
	            FROM table_data t
	            LEFT JOIN transaction_data td ON (
	                td.table_id             = t.table_id
	                AND td.transaction_status   = 2
	                AND td.transaction_schedule = ?
	            )
	            LEFT JOIN client_data cd ON cd.client_data_id = td.client_data_id
	            ORDER BY t.table_id ASC
	    ";
	    $query = $this->db->query($sql, [$current_date]);
	    $rows  = $query->result();

	    if (empty($rows)) {
	        echo json_encode([
	            'status' => 'empty',
	            'tables' => []
	        ]);
	        exit;
	    }

	    $tables = array_map(function($row) {
	        $full_name = null;
	        if ($row->first_name) {
	            $full_name = trim(implode(' ', array_filter([
	                $row->first_name,
	                $row->middle_name,
	                $row->last_name
	            ])));
	        }

	        return [
	            'table_id'           => $row->table_id,
	            'table_value'        => $row->table_value,
	            'table_status'       => $row->table_status,
	            'current_sequence'   => $row->current_sequence,
	            'transaction_class'  => $row->transaction_class,
	            'priority_level'     => $row->priority_level,
	            'full_name'          => $full_name
	        ];
	    }, $rows);

	    echo json_encode([
	        'status' => 'success',
	        'tables' => $tables
	    ]);
	    exit;
	}
}