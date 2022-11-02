<?php

class Controller {

    private $model_path     = '';
    private $model_class    = '';
    public $input;
    
    public function __construct(){
        
    }

    //Request Model
    public function RequestModel($tModuleName, $tModelName){

        $aClass     = explode("/",$tModelName);
        $tClassName = $aClass[1];

        $this->model_path  = 'application/modules/'.$tModuleName.'/models/'.$tModelName.'.php';
        $this->model_class = $tClassName;

        include($this->model_path);
        $this->$tClassName = new $this->model_class;

        return $this;
        
    }

    //Request View
    public function RequestView($tModuleName, $tViewName, $aData = array()){

        ob_start();
        if(isset($aData)){
            extract($aData);
        }

        include('autoload.php');
        include('route.php');
        include('application/modules/'.$tModuleName.'/views/'.$tViewName.'.php');
        $buffer = ob_get_contents();
        @ob_end_clean();
        return $buffer;

    }

    //Pack Data ไม่นับ connection for method GET
    public function PackDatatoarray($tParamter,$tCallType){

        if($tCallType == 'WIN'){
            $aResult        = array();
            $tParameter     = base64_decode($tParamter);
            $aResultDecode  = json_decode($tParameter,TRUE);
            // echo "<pre>";
            // print_r($aResult);
            // echo "</pre>";
            array_push($aResult,array('Username' => $aResultDecode[1]['Username']));
            // $aData          = explode(",", $tParameter); 
            // for($i=4; $i<count($aData); $i++){
            //     $tResult    = str_replace(array('{', '"', '”', '“', '}', ']'),'',$aData[$i]);
            //     $aPackdata  = explode(":", $tResult); 
            //     array_push($aResult,array($aPackdata[0] => $aPackdata[1]));
            // }
            $aDataresult    = $aResult;
        }else{
            $aDataresult    = $tParamter;
        }
        return $aDataresult;
    }

    //Pack Data text ทั้งหมด 
    public function PackData($tParamter,$tCallType){
        if($tCallType == 'WIN'){
            $aDataresult    = '';
            $tParameter     = base64_decode($tParamter);
            $aData          = explode(",", $tParameter); 
            for($i=4; $i<count($aData); $i++){
                $tResult        = str_replace(array('{', '”', '“', '}', ']'),'',$aData[$i]);
                $aDataresult    .= $tResult . ',';
                if($i == count($aData) - 1){
                    $aDataresult = substr($aDataresult,0,-1);
                }
            }
        }else{
            $aDataresult    = $tParamter;
        }
        return $aDataresult;
    }

    //Config Database
    public function Configdatabase($tParamter,$tCallType){
        if($tCallType == 'WIN'){
            // echo "<pre>"; 
            // print_r($tParamter); echo "<br>";
            $tParameter = base64_decode($tParamter);
            // print_r(json_decode($tParameter,TRUE));  echo "<br>";
            // $aResult    = explode('","',$tParameter);

            $aResult = json_decode($tParameter,TRUE);

            // print_r($aResult);  echo "<br>";
            //server
            // $tDBServer      = str_replace(array('{', '”', '“', '}', ']', '[', '"'),'', $aResult[0]);
            // $tDBServer      = explode(':',$tDBServer);
            
            //User
            // $tDBUser      = str_replace(array('{', '”', '“', '}', ']', '[', '"'),'', $aResult[1]);
            // $tDBUser      = explode(':',$tDBUser);

            //Password
            // $tDBPassword      = str_replace(array('{', '”', '“', '}', ']', '[', '"'),'', $aResult[2]);
            // $tDBPassword      = explode(':',$tDBPassword);

            //DBName
            // $tDBName      = str_replace(array('{', '”', '“', '}', ']', '[', '"'),'', $aResult[3]);
            // $tDBName      = explode(':',$tDBName);

            // echo "</pre>";

            $tConnection        = '';
            $tConnection        .= "defined('tServername') or define('tServername','".$aResult[0]['server']."');\r\n";
            $tConnection        .= "defined('tUsername') or  define('tUsername','".$aResult[0]['User']."');\r\n";
            $tConnection        .= "defined('tPassword') or  define('tPassword','".$aResult[0]['Password']."');\r\n";
            $tConnection        .= "defined('tDBName') or define('tDBName','".$aResult[0]['db']."');\r\n";
            $tConnection        .= "defined('tTypeConnect') or  define('tTypeConnect','SQLSRV');\r\n";
            
            $filenames 		    = "application\config\database.php";
            $ourFileNames 	    = $filenames;
            $ourFileHandle      = fopen($ourFileNames, 'w');
            $written            = "";
            $written            .= "<?php" . "\r\n";
            $written            .= $tConnection;
            $written            .= "?>";
            fwrite($ourFileHandle,$written);
            fclose($ourFileHandle);
        }
    }

}

?>