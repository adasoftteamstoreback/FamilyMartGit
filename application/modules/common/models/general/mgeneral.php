<?php

class mgeneral extends Database {

    public function __construct(){
        parent::__construct();
    }

    //Get Detail Profile
    public function FSaMCOMGetDetailProfile($pnCodeuser){
        try {

            $tSQL       = "SELECT TOP 1 
                           	TSysUser.FTUsrCode,
                            TSysUser.FTUsrName,
                            TSysUser.FTDptCode,
                            TCNMDepart.FTDptName as FTDptName
                            FROM TSysUser
                            LEFT JOIN TCNMDepart ON TCNMDepart.FTDptCode = TSysUser.FTDptCode ";
            $tSQL       .= " WHERE TSysUser.FTUsrCode = '$pnCodeuser'";
            $tResult     = $this->DB_SELECT($tSQL);
            if(empty($tResult)){
                return 'emptydata';
            }else{
                //Get Branch
                $tSQLBCH                    = "SELECT TOP 1 FTBchCode , FTCmpBranch , FTCmpCode FROM TCNMComp";
                $tResultBCH                 = $this->DB_SELECT($tSQLBCH);
                $_SESSION["SesBchCode"]     = $tResultBCH[0]['FTBchCode'];
                $_SESSION["SesUsercode"]    = $tResult[0]['FTUsrCode'];
                $_SESSION["SesUsername"]    = $tResult[0]['FTUsrName'];
                $_SESSION["SesFTCmpCode"]   = $tResultBCH[0]['FTCmpCode'];
                $_SESSION["SesUserDptCode"] = $tResult[0]['FTDptCode'];
                $_SESSION["SesUserDptName"] = $tResult[0]['FTDptName'];
                return $tResult;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Config RabbitMQ
    public function ConfigRabbitMQ(){
        try {
            $tSQL       = "SELECT FTSysUsrValue,FTSysNameEng FROM TSysConfig";
            $tSQL       .= " WHERE FTSysCode = 'RABBITMQ'";
            $tResult     = $this->DB_SELECT($tSQL);
            if(empty($tResult)){
                return 'emptydata';
            }else{
                $tTextRabbit = '';
                foreach($tResult as $key=>$tValue){
                    $nNumber = $key+1;
                    $tTextRabbit .= '$'."config['RabbitMQ'][".$nNumber."]  = '" . $tValue['FTSysUsrValue'] . "';\r\n";
                }
                
                $filenames 		    = "application/config/rabbitmq.php";
                $ourFileNames 	    = $filenames;
                $ourFileHandle     = fopen($ourFileNames, 'w');
                $written            = "";
                $written            .= "<?php" . "\r\n";
                $written            .= $tTextRabbit;
                $written            .= "?>";
                fwrite($ourFileHandle,$written);
                fclose($ourFileHandle);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Setting permission
    public function SettingPermission($ptNameModule){
        try {
            $nUsercode  = $_SESSION['FTUsrCode'];
            $tSQL       = "SELECT  FTMnuName , FTSumFull , FTSumRead , FTSumAdd , FTSumEdit , FTSumDelete , FTSumCancel , FTSumAppv , FTSumPrint ";
            $tSQL       .= " FROM TSysUserMenu (NOLOCK)";
            $tSQL       .= " WHERE FTUsrCode = '$nUsercode' AND ";
            $tSQL       .= " FTMnuName = '$ptNameModule' ";
            $tResult     = $this->DB_SELECT($tSQL);
            if(empty($tResult)){
                $_SESSION['FTSumFull']      = 0;
                $_SESSION['FTSumRead']      = 0;
                $_SESSION['FTSumAdd']       = 0;
                $_SESSION['FTSumEdit']      = 0;
                $_SESSION['FTSumDelete']    = 0;
                $_SESSION['FTSumCancel']    = 0;
                $_SESSION['FTSumAppv']      = 0;
                $_SESSION['FTSumPrint']     = 0;
            }else{
                $_SESSION['FTSumFull']      = $tResult[0]['FTSumFull'];
                $_SESSION['FTSumRead']      = $tResult[0]['FTSumRead'];
                $_SESSION['FTSumAdd']       = $tResult[0]['FTSumAdd'];
                $_SESSION['FTSumEdit']      = $tResult[0]['FTSumEdit'];
                $_SESSION['FTSumDelete']    = $tResult[0]['FTSumDelete'];
                $_SESSION['FTSumCancel']    = $tResult[0]['FTSumCancel'];
                $_SESSION['FTSumAppv']      = $tResult[0]['FTSumAppv'];
                $_SESSION['FTSumPrint']     = $tResult[0]['FTSumPrint'];
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Path Logo image
    public function FSaMCOMGetPathLogoImage(){
        try {
            $tSQL       = "SELECT FTSysUsrValue FROM TSysConfig (NOLOCK) ";
            $tSQL       .= " WHERE FTSysCode='CompLogo' ";
            $tResult     = $this->DB_SELECT($tSQL);
            if(empty($tResult)){
                return 'notfound';
            }else{
                return $tResult[0]['FTSysUsrValue'];
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}

?>