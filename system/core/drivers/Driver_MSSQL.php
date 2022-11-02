<?php

	class Driver_database {

		//return $query_result->last_insert_id;
		//mssql_min_error_severity(1);
		//mssql_min_message_severity(11);

		public  $tServername; 
		public	$tUsername;
		public	$tPassword;
		public	$tDBName;

		public function __construct(){
			// include('application/config/database.php');
			// $this->tServername 		= $tServername; 
			// $this->tUsername 		= $tUsername;
			// $this->tPassword		= $tPassword;
			// $this->tDBName			= $tDBName;
		}

		//CONNECT Database
		public function DB_CONNECTBASE(){
			include('application/config/database.php');

			$this->tServername 		= tServername; 
			$this->tUsername 		= tUsername;
			$this->tPassword		= tPassword;
			$this->tDBName			= tDBName;


			//Connect SQLSRV
			$serverName = $this->tServername; 
			$connectionInfo = array( 
				"Database"		=> $this->tDBName, 
				"UID"			=> $this->tUsername, 
				"PWD"			=> $this->tPassword
			);
			

			$conn  = mssql_connect($serverName,$connectionInfo["UID"],$connectionInfo["PWD"]);
			$condb = mssql_select_db($connectionInfo["Database"]);

			if($conn) {
				return $conn;
				//echo "Connection established.<br />";
			}else{
				echo '<pre>';
				die( print_r( mssql_get_last_message() ) );
				echo '</pre>';
			}
		}		

		//SELECT
		public function DB_SELECT($tSQL){
			$oConn = $this->DB_CONNECTBASE();
			$tQuery = mssql_query($tSQL, $oConn);
			if($tQuery === false) {
				return mssql_get_last_message(); //Error
			}else{
				$aResult  = array();
				$aResult1  = array();
				$rows 		= array();
				
				while($row = mssql_fetch_array($tQuery, MSSQL_ASSOC)){
					array_push($aResult, $row);
				}
				
				foreach($aResult as $datas ) {
					$rows[] = $this->MSSQLEncodeTH($datas);//array_map('utf8_encode', $row);
				}
				return $rows;
			}
		}
		
		//convert TH
		public function ConvertUTF8($value){
			//RETURN $value;
			return iconv('tis-620','utf-8',$value);
		}
		
		//convert TIS-620
		public function ConvertTIS620($value){
			//return $value;
			return iconv('utf-8','tis-620',$value);
		}

		//convert TH
		public function MSSQLEncodeTH($arr){
			$rows = array();
			foreach ($arr as $key => $value) {
				$rows[$key] = $this->ConvertUTF8($value);
			}
			return $rows;
		}

		//SELECT COUNT
		public function DB_SELECTCOUNT($tSQL){
			$oConn 		= $this->DB_CONNECTBASE();

			// $params 	= array();
			// $options 	= array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
			$tQuery 	= mssql_query($tSQL, $oConn);
			$nResult	= mssql_num_rows($tQuery);
			if($nResult == false){
				return 0;
			}else{
				return $nResult;
			}
		}

		//SELECT COUNT BY COLUMN
		public function DB_SELECTCOUNTBYCOLUMN($tFiled,$tTable){
			$oConn 		= $this->DB_CONNECTBASE();

			$tSQL 		= "SELECT COUNT($tFiled) FROM $tTable";
			$tQuery 	= mssql_query($tSQL, $oConn);
			if($tQuery == false){
				return mssql_get_last_message();
			}else{
				$row = mssql_fetch_array($tQuery, MSSQL_NUM);
				return $row[0];
			}
		}

		//UPDATE
		public function DB_UPDATEWHERE($tDatabase,$aData,$aDatawhere){
			$oConn 		= $this->DB_CONNECTBASE();

			if(empty($aData)){
				return 607;
				exit;
			}else{
				$tUpdateINTO = '';
				for($i=1;$i<=count($aData);$i++){
					$tUpdateINTO .= key($aData) . " = '" . $aData[key($aData)] ."' ,";
					next($aData);
					if($i == count($aData)){
						$tUpdateINTO = substr($tUpdateINTO, 0, -1);
					}
				}
			}

			if(empty($aDatawhere)){
				return 607;
				exit;
			}else{
				$tUpdateWHERE = '';
				for($i=1;$i<=count($aDatawhere);$i++){
					$tUpdateWHERE .= key($aDatawhere) . " = '" . $aDatawhere[key($aDatawhere)] ."' ";
					next($aDatawhere);
					if($i == count($aDatawhere)){
						$tUpdateWHERE = substr($tUpdateWHERE, 0, -1);
					}
				}
			}

			if(count($aDatawhere) == 0){
				$tSQL = "UPDATE ".$tDatabase." SET ".$tUpdateINTO." ";

			}else{
				$tUpdateINTO = $this->ConvertTIS620($tUpdateINTO);
				$tSQL = "UPDATE ".$tDatabase." SET ".$tUpdateINTO." WHERE ".$tUpdateWHERE." ";
			}
	
			$tResult = mssql_query($tSQL, $oConn);
			if( $tResult === false ) {
				echo '<pre>';
				die( mssql_get_last_message() );
				echo '</pre>';
			}else{
				return 'success';
			}
		}

		//INSERT
		public function DB_INSERT($tDatabase,$aData){
			
			$oConn 			= $this->DB_CONNECTBASE();
			if(empty($aData)){ //array ไม่มีค่า
				return 607;
			}else{
				$tInsertINTO = '';
				
				for($i=1;$i<=count($aData);$i++){
					$tInsertINTO .= key($aData) . ',';
					next($aData);
					if($i == count($aData)){
						$tInsertINTO = substr($tInsertINTO, 0, -1);
					}
				}
				
				$tInsertValue = implode("','", $aData);
				$tInsertValue = $this->ConvertTIS620($tInsertValue);
				$tSQL = "INSERT INTO ".$tDatabase." (".$tInsertINTO.") VALUES ('". $tInsertValue . "')";
				
				/*$tTextConvert = iconv('tis-620','utf-8',$tSQL);
				echo $tTextConvert;*/
				$tResult = mssql_query($tSQL, $oConn);
				if( $tResult === false ) {
					return mssql_get_last_message();
				}else{
					return 'success';
				}
			}
		}

		//DELETE
		public function DB_DELETE($tDatabase,$aData,$bConfirm){
			$oConn 		= $this->DB_CONNECTBASE();
				
			if(empty($aData)){ //array ไม่มีค่า
				
				// if($bConfirm == true){
				// 	$tSQL = "DELETE FROM ".$tDatabase." ";
				// }else{
				// 	return 222;
				// }
				return 607;
			}else{
				$tDELETE = '';
				for($i=1;$i<=count($aData);$i++){
					$tDELETE .= key($aData) . " = '" . $aData[key($aData)] . "' AND ";
					next($aData);
					if($i == count($aData)){
						$tDELETE = substr($tDELETE, 0, -4);
					}
				}

				if(count($aData) == 0){
					if($bConfirm == true){
						$tSQL = "DELETE FROM ".$tDatabase." ";
					}else{
						return 222;
					}
					
				}else{
					$tSQL = "DELETE FROM ".$tDatabase." WHERE ".$tDELETE."";
				}

				$tResult = mssql_query($tSQL, $oConn);
				if( $tResult === false ) {
					echo '<pre>';
					die( mssql_get_last_message() );
					echo '</pre>';
				}else{
					return 'success';
				}
			}
		}

		//EXECUTE
		public function DB_EXECUTE($tSQL){
			$oConn 		= $this->DB_CONNECTBASE();

			$tQuery 	= mssql_query($tSQL, $oConn);
			if($tQuery === false) {
				return mssql_get_last_message();
			}else{
				return 'success';
			}
		}

		//CONDITION
		public function DB_SELECTCONDITION($aData){
			$oConn 			= $this->DB_CONNECTBASE();

			if(isset($aData['JOIN'])){
				//มี join
				$tJOINTYPE 	= $aData['JOIN']['TYPE'];
				$tJOINTable = $aData['JOIN']['TABLE'];
				$tJOINOn 	= $aData['JOIN']['ON'];
			}else{
				//ไม่มี join
			}

			if(empty($aData['TABLE'])){
				return 101;
				exit;
			}else{
				$tTABLE = $aData['TABLE'];
			}

			if(empty($aData['FIELD'])){
				$tFIELD = ' * ';
			}else{
				$tFIELD = $aData['FIELD'];
			}

			if(empty($aData['WHERE'])){
				$tWHERE = ' ';
			}else{
				$tWHERE = ' WHERE ' . $aData['WHERE'];
			}

			if(empty($aData['TOP'])){
				$nTOP = ' ';
			}else{
				$nTOP = 'TOP('.$aData['TOP'].')';
			}

			if(empty($aData['ORDERBY'])){
				$tORDERBY = ' ';
			}else{
				$tORDERBY = 'ORDER BY ' . $aData['ORDERBY'];
			}

			$tSQL = 'SELECT ' . $nTOP . $tFIELD . ' FROM ' . $tTABLE . $tWHERE . $tORDERBY;
			$tQuery = mssql_query($tSQL, $oConn);
			if($tQuery === false) {
				return mssql_get_last_message();
			}else{
				$aResult 	= array();
				while($row = mssql_fetch_array($tQuery, MSSQL_ASSOC)){
					array_push($aResult,$row);
				}
				return $aResult;
			}
		}
		
	}

?>
