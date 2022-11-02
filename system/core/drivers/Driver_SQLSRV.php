<?php
	include('application/config/database.php');
	class Driver_database {

		public  $tServername; 
		public	$tUsername;
		public	$tPassword;
		public	$tDBName;

		public $oConmectSQL;

		public function __construct(){

			$this->DB_CONNECTBASE();
			
		}

		//CONNECT Database
		public function DB_CONNECTBASE(){

			$this->tServername 		= tServername; 
			$this->tUsername 		= tUsername;
			$this->tPassword		= tPassword;
			$this->tDBName			= tDBName;

			//Connect SQLSRV
			$serverName = $this->tServername; 
			$connectionInfo = array( 
				"Database"		=> $this->tDBName, 
				"UID"			=> $this->tUsername, 
				"PWD"			=> $this->tPassword,
				"CharacterSet" 	=> "UTF-8"
			);
			$conn = sqlsrv_connect($serverName,$connectionInfo);
			if($conn) {
				$this->oConmectSQL = $conn;
			}else{
				echo '<pre>';
				echo "ไม่สามารถเชื่อมต่อฐานข้อมูล " . $this->tDBName . "<br><br>";
				die( 
					print_r(sqlsrv_errors(), true)
				);
				echo '</pre>';
			}
		}	

		//SELECT
		public function DB_SELECT($tSQL){
			$oConn  = $this->oConmectSQL;
			$tQuery = sqlsrv_query($oConn,$tSQL);
			if($tQuery === false) {
				// return sqlsrv_errors()[0]['message'];
				return sqlsrv_errors();
			}else{
				$aResult 	= array();
				while($row = sqlsrv_fetch_array($tQuery, SQLSRV_FETCH_ASSOC)){
					array_push($aResult,$row);
				}
				return $aResult;
			}
		}

		//convert TH
		public function ConvertUTF8($value){
			return $value;
			//return iconv('tis-620','utf-8',$value);
		}
		
		//convert TIS-620
		public function ConvertTIS620($value){
			return $value;
			//return iconv('utf-8','tis-620',$value);
		}

		//SELECT COUNT
		public function DB_SELECTCOUNT($tSQL){
			$oConn 			= $this->oConmectSQL;

			$params 	= array();
			$options 	= array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
			$tQuery 	= sqlsrv_query($oConn,$tSQL,$params,$options);
			$nResult	= sqlsrv_num_rows($tQuery);
			if($nResult == false){
				return 0;
			}else{
				return $nResult;
			}
		}

		//SELECT COUNT BY COLUMN
		public function DB_SELECTCOUNTBYCOLUMN($tFiled,$tTable){
			$oConn 			= $this->oConmectSQL;

			$tSQL 		= "SELECT COUNT($tFiled) FROM $tTable";
			$tQuery 	= sqlsrv_query($oConn,$tSQL);
			if($tQuery == false){
				return sqlsrv_errors()[0]['code'];
			}else{
				$row = sqlsrv_fetch_array($tQuery);
				return $row[0];
			}
		}

		//UPDATE
		public function DB_UPDATEWHERE($tDatabase,$aData,$aDatawhere){
			$oConn 			= $this->oConmectSQL;

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
				$tSQL = "UPDATE ".$tDatabase." SET ".$tUpdateINTO." WHERE ".$tUpdateWHERE." ";
			}
			
			$tResult = sqlsrv_query($oConn,$tSQL);
			if( $tResult === false ) {
				echo '<pre>';
				die( sqlsrv_errors()[0]['message'] );
				echo '</pre>';
			}else{
				return 'success';
			}
		}

		//INSERT
		public function DB_INSERT($tDatabase,$aData){
			$oConn 			= $this->oConmectSQL;

			if(empty($aData)){ //array ไม่มีค่า
				return 607;
			}else{
				$tInsertINTO = '';
				for($i=1;$i<=count($aData);$i++){
					// print_r($aData);
					$tInsertINTO .= key($aData) . ',';
					next($aData);
					// print_r($aData);
					if($i == count($aData)){
						$tInsertINTO = substr($tInsertINTO, 0, -1);
					}
				}
				$tSQLValue = "";
				$nI = 0;
				foreach ($aData as $key => $value){
					if($nI<count($aData)-1){
						if($value=="NULL"){
							$tSQLValue .= $value.",";
						}else{
							$tSQLValue .= "'".$value."',";
						}
					}else{
						if($value=="NULL"){
							$tSQLValue .= $value;
						}else{
							$tSQLValue .= "'".$value."'";
						}
					}
					
					$nI++;
				}
				$tSQL = "INSERT INTO ".$tDatabase." (".$tInsertINTO.") VALUES (".$tSQLValue.")"; //'".implode("','", $aData) . "'
				$tResult = sqlsrv_query($oConn,$tSQL);
				if( $tResult === false ) {
					// return sqlsrv_errors()[0]['code'];
					return $tSQL;
				}else{
					return 'success';
					// return $tSQL;
				}
			}
		}

		//DELETE
		public function DB_DELETE($tDatabase,$aData,$bConfirm){
			$oConn 			= $this->oConmectSQL;
				
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

				$tResult = sqlsrv_query($oConn,$tSQL);
				if( $tResult === false ) {
					echo '<pre>';
					die( sqlsrv_errors()[0]['message'] );
					echo '</pre>';
				}else{
					return 'success';
				}
			}
		}

		//EXECUTE
		public function DB_EXECUTE($tSQL,$aParams = array()){
			$oConn 			= $this->oConmectSQL;
			$tQuery = sqlsrv_query($oConn,$tSQL,$aParams); //array("QueryTimeout" => 600) Sets the query timeout in seconds. By default, the driver will wait indefinitely for results.
			if($tQuery === false) {
				$returnExecute = sqlsrv_errors();
			}else{
				$rows_affected = sqlsrv_rows_affected($tQuery);
				if($rows_affected > 0){
					$returnExecute = 'success';
				}else{
					$returnExecute = sqlsrv_errors();
				}
			}
			return $returnExecute;
		}

		//CONDITION
		public function DB_SELECTCONDITION($aData){
			$oConn 			= $this->oConmectSQL;

			if(isset($aData['JOIN'])){
				//มี join
				$tJOINTYPE 	= $aData['JOIN']['TYPE'];
				$tJOINTable = $aData['JOIN']['TABLE'];
				$tJOINOn 	= $aData['JOIN']['ON'];
				$tPKJOIN 	= $aData['JOIN']['PKJOIN'];
				
				if($tJOINTYPE != 'inner join' && $tJOINTYPE != 'left join' && $tJOINTYPE != 'right join'){
					return 101;
					exit;
				}else{
					$tJOIN = 'pass';
				}
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

			if($tJOIN = 'pass'){
				$tExplode 	= Explode(',',$tFIELD);
				$tTextField 		= '';

				for($i=0; $i<count($tExplode); $i++){
					if($tExplode[$i] == $tJOINOn){
						$tTextField .= 'T2.'.$tExplode[$i]. ' , ';
					}else{
						$tTextField .= $tExplode[$i] .' , ';
					}

					if($i+1 == count($tExplode)){
						$tTextField = substr($tTextField, 0, -2);
					}
				}
				
				$tSQL = 'SELECT ' . $nTOP . $tTextField . ' FROM ' . $tTABLE;
				$tSQL .= " LEFT JOIN $tJOINTable AS T2 ON T2.$tJOINOn = $tTABLE.$tPKJOIN";
				$tSQL .= "$tWHERE $tORDERBY";
			}else{
				$tSQL = 'SELECT ' . $nTOP . $tFIELD . ' FROM ' . $tTABLE . $tWHERE . $tORDERBY;
			}

			$tQuery = sqlsrv_query($oConn,$tSQL);
			if($tQuery === false) {
				return sqlsrv_errors()[0]['code'];
			}else{
				$aResult 	= array();
				while($row = sqlsrv_fetch_array($tQuery, SQLSRV_FETCH_ASSOC)){
					array_push($aResult,$row);
				}
				return $aResult;
			}
		}

		public function DB_BEGIN_TRANSACTION(){
			$oConn 			= $this->oConmectSQL;
			sqlsrv_begin_transaction($oConn);
		}

		public function DB_COMMIT(){
			$oConn 			= $this->oConmectSQL;
			sqlsrv_commit($oConn);
		}

		public function DB_ROLLBACK(){
			$oConn 			= $this->oConmectSQL;
			sqlsrv_rollback($oConn);
		}

	}

?>
