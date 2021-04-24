<?php
	class DB {
		private static $_instance = null;

		private $_pdo, 
				$_query, 
				$_mysql,
				$_error = false, 
				$_results, 
				$_count = 0;

		private function __construct() {
			try {
				$this->_pdo 	= new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
				$this->_mysql 	= mysqli_connect(Config::get('mysql/host'), Config::get('mysql/username'), Config::get('mysql/password'), Config::get('mysql/db'));
				//echo 'success';
			} catch(PDOException $e) {
				die($e->getmessage());
			}
		}		

		public static function getInstance() {
			if(!isset(self::$_instance)) {
				self::$_instance = new DB();
			}
			return self::$_instance;
		}   
			
		public function insert($table, $fields = array()) {						
			
			$keys 	= array_values($fields[0]); // fetching columns that needed to be inserted into the database	
			$removed_first_element = array_shift($fields);			
			$values = array();
			$x = 1;		
			
			$stockResult 	= $this->countData('stock');			

			if(isset($stockResult['total']) && $stockResult['total'] <= 0){
				foreach($fields as $key => $field) {	
					if($x < count($fields)) {
						$d = $x++;
						$values[] = "($d,STR_TO_DATE('$field[1]', '%d-%m-%Y'),'$field[2]',$field[3])";
					}				
				}

				$inserted_data = implode(',', $values);
				$sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES {$inserted_data}";			
				
				if (mysqli_query($this->_mysql, $sql)){
					$Errordata = array("success"=>"Data Added Successfully");
					return $Errordata;
				}else{
					echo "Error: " . $sql . "<br>" . mysqli_error($this->_mysql);
				}
				return false;

			}else{							
				$Errordata = array("error"=>"Existing Datas Found Clear Data Before Proceeding");				
				return $Errordata;	
			}
		}	

		public function countData($table){
			$selectCount 	= "SELECT count(*) as total FROM $table";  // fetching existing stock list
			$countRows 		= mysqli_query($this->_mysql, $selectCount);
			$stockResult 	= $countRows->fetch_assoc();
			return $stockResult;
		}
		public function ClearData($table){
			$delete 	= "Delete FROM $table";  // Deleting existing stock list
			$countRows 	= mysqli_query($this->_mysql, $delete);			
			return $countRows;
		}
		public function distinctData($fieldName,$table){
			$distinct 		= "Select Distinct $fieldName from $table";  // fetch distinct stock names for dropdown
			$distinctRows 	= mysqli_query($this->_mysql, $distinct);
			//$distinctResult = $distinctRows->fetch_assoc();			
			return $distinctRows;
		}
		public function calculateRangeQuery($stockName,$dateFrom,$dateTo,$table){
			//echo $stockName.','.$dateFrom.','.$dateTo.','.$table;
			$stockName 		  	= trim($stockName, "*");
			$newArray			= array();
			$rangeCalculation = "Select * from $table where stock_name = '$stockName' AND date between STR_TO_DATE('$dateFrom','%m/%d/%Y') and STR_TO_DATE('$dateTo','%m/%d/%Y') ORDER BY date ASC";
			
			// $rangeCalculation 	= "SELECT (
			// 								select json_object('minStockPrice',price,'purchaseDate',date) as name from $table 
			// 								where stock_name = '$stockName' AND 
			// 								date between STR_TO_DATE('$dateFrom','%m/%d/%Y') and STR_TO_DATE('$dateTo','%m/%d/%Y') 
			// 								ORDER BY price ASC LIMIT 1
			// 							  )as lowStockPrice,
			// 							  (
			// 								select json_object('maxStockPrice',price,'purchaseDate',date) as stock from $table 
			// 								where stock_name = '$stockName' AND 
			// 								date between STR_TO_DATE('$dateFrom','%m/%d/%Y') and STR_TO_DATE('$dateTo','%m/%d/%Y') 
			// 								ORDER BY price DESC LIMIT 1
			// 							  )as highStockPrice";

			$dataCalculated   	= mysqli_query($this->_mysql, $rangeCalculation);	

			while($row = $dataCalculated->fetch_assoc()){ // fetching into newarray because needs to manipulate data in foreach
				$newArray[] = $row;
		   }		   
			return $newArray;
		}
	}
?>