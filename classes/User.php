<?php
	class User {
		private $_db,
				$_data,
				$_sessionName,
				$_isLoggedIn;

		public function __construct($user = null) {
			$this->_db = DB::getInstance();
			$this->_sessionName = config::get('session/session_name');
			
		}

		public function create($fields = array()) {
			if(!$this->_db->insert('blog_users', $fields)) {
				throw new Exception('There was a problem creating an account.');
			}
		}
		public function csv_extraction($path = null){
			$file = fopen($path, 'r');
			while (($line = fgetcsv($file)) !== FALSE){
					//$line is an array of the csv elements					
					$data_return[] = $line;
				}
			fclose($file);
			return $data_return;
		}	
	}
?>