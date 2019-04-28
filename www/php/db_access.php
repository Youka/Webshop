<?php
	class DBConnection {
		// Connection handle
		private $con;
		
		// Ctor
		public function __construct() {
			// Credential
			$db_info = [
				'host' => 'localhost',
				'user' => 'root',
				'pw' => '',
				'db' => 'webshop'
			];
			// Setup connection
			$this->con = new mysqli($db_info['host'], $db_info['user'], $db_info['pw'], $db_info['db']);
			if($this->con->connect_errno)
				throw new Exception("Connection failed: " . $this->con->connect_error . " (" . $this->con->connect_errno . ")");
		}
		
		// Methods
		public function close() {
			$this->con->close();
		}
		
		public function execute() {
			// Method arguments
			$args_n = func_num_args();
			$args = func_get_args();
			// Simple
			if($args_n == 1)
				return $db_con->execute($args[0]);
			// Prepared
			else if($args_n > 2) {
				if($stmt = $this->con->prepare($args[0])) {
					call_user_func_array([$stmt, 'bind_param'], self::refValues(array_slice($args, 1)));
					$changes = $stmt->execute();
					$stmt->close();
					return $changes;
				}
			}
		}
		
		public function query() {
			// Method arguments
			$args_n = func_num_args();
			$args = func_get_args();
			// Simple
			if($args_n == 1) {
				if($rslt = $this->con->query($args[0])) {
					$data = $rslt->fetch_all(MYSQLI_ASSOC);
					$rslt->close();
					return $data;
				}
			// Prepared
			} else if($args_n > 2) {
				if($stmt = $this->con->prepare($args[0])) {
					call_user_func_array([$stmt, 'bind_param'], self::refValues(array_slice($args, 1)));
					if($stmt->execute() && ($rslt = $stmt->get_result())) {
						$data = $rslt->fetch_all(MYSQLI_ASSOC);
						$rslt->close();
						$stmt->close();
						return $data;
					}
					$stmt->close();
				}
			}
		}
		
		public function querySingle() {
			if(($data = call_user_func_array([$this, 'query'], func_get_args())) && count($data) == 1)
				return $data[0];
		}
		
		public function queryScalar() {
			if($data = call_user_func_array([$this, 'querySingle'], func_get_args()))
				foreach($data as $_ => $value)
					return $value;
		}
		
		// Helpers
		private static function refValues($arr){
			if(strnatcmp(phpversion(),'5.3') >= 0) { // Reference is required for PHP 5.3+
				$refs = array();
				foreach($arr as $key => $value)
					$refs[$key] = &$arr[$key];
				return $refs;
			}
			return $arr;
		}
	}
?>