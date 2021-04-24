<?php
// clearing data requires manual click events
if(isset($_POST['class'])){
	try{	
		$conn 		= mysqli_connect($_POST['hostName'], $_POST['uname'], $_POST['pwd'], $_POST['database']);
		$delete 	= "Delete FROM stock";  // Deleting existing stock list
		$countRows 	= mysqli_query($conn, $delete);	
		$Errordata 	= array("success"=>"Data's Cleared Successfully");				
		echo json_encode($Errordata);
	}catch(Exception $error){
		die($error->getMessage());
	}
} 
	class Functions {
		private $_db,
				$_data,
				$_sessionName,
				$_isLoggedIn;

		public function __construct($user = null) {
			$this->_db = DB::getInstance();
			$this->_sessionName = config::get('session/session_name');			
		}

		public function create($fields = array()) {    // inserting csv into database
			$returnData = $this->_db->insert('stock', $fields);
			if(!$returnData) {
				throw new Exception('There was problem inserting Stock Records');
			}else{
				return $returnData;
			}
		}
		public function csv_extraction($path = null){   // read csv file for upload
			$file = fopen($path, 'r');
			while (($line = fgetcsv($file)) !== FALSE) {														
					$data_return[] = $line;
				}
			 fclose($file);
			 return $data_return;
		}	
		public function clearData(){						
			$data = $this->_db->ClearData('stock');     // clears data
			if($data){
				$Errordata = array("success"=>"Data's Cleared Successfully");				
				echo json_encode($Errordata);
			}	
		}
		public function checkEnableClearData(){
			$count = $this->_db->countData('stock');  // check count for  existing datas
			return $count;
		}
		public function distinctValues(){
			$count = $this->_db->distinctData('stock_name','stock');  // Select Distinct Stock values
			return $count;
		}
		public function calculateRange($stockName,$dateFrom,$dateTo){ // range calculation for what date he should have purchased and what date he should have sold
			$rangeCalculate = $this->_db->calculateRangeQuery($stockName,$dateFrom,$dateTo,'stock');	
			$newArray		= array();
			$maxPrice		= 0;
			$maxPriceDate 	= '';
			try{			
				$arrayCount = count($rangeCalculate);
				if($arrayCount > 0){

					foreach($rangeCalculate as $key=>$data){	
							if($data['price'] > $maxPrice){   // Find Max Price and its Purchased Date
								$maxPrice 		= $data['price'];
								$maxPriceDate 	= $data['date'];
							}
					}
					$purchasePrice  = $rangeCalculate[0]['price'];     // fetching purchased price and date
					$purchaseDate   = $rangeCalculate[0]['date'];

					$soldPrice 		= $rangeCalculate[$arrayCount-1]['price']; // fetching sold price and date
					$soldDate 		= $rangeCalculate[$arrayCount-1]['date'];

					$analysisPrice	= $maxPrice - $purchasePrice;     // stock analysis price

					if($purchasePrice > $soldPrice){   // calculate profit or loss with their amout differences
						$marketStatus 	= 'Loss';
						$totalAmount  	= $purchasePrice - $soldPrice;
					}else{
						$marketStatus 	= 'Profit';
						$totalAmount	= $soldPrice - $purchasePrice;
					}	
					
					$overAllStockData = array(        //  form array with relevant data
						"stockName"		=>  $stockName,
						"maxPrice" 		=> 	$maxPrice,
						"maxPriceDate" 	=> 	$maxPriceDate,
						"purchasePrice" => 	$purchasePrice,
						"purchaseDate" 	=> 	$purchaseDate,
						"soldPrice"		=> 	$soldPrice,
						"soldDate"		=> 	$soldDate,
						"marketStatus"	=>	$marketStatus,
						"totalAmount"	=>	$totalAmount,
						"analysisPrice"	=>  $analysisPrice
					);
					return $overAllStockData;

				}else{
					$noDataFound = array('msg' => 'No Data found for the given Date range');
					return $noDataFound;
				}
			}catch(Exception $error){
				die($error->getMessage());
			}
		}
	}
?>