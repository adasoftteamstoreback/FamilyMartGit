<?php

    function language($ptPathFile, $ptLangName){

        include('autoload.php');
        $aPathFile      = explode("/",$ptPathFile);
        $tMenuName      = $aPathFile[1];

        $DB             = new Driver_database();
        $FTUsrCode      = $_SESSION['FTUsrCode'];
        $tSQL           = "SELECT FTUsrLng FROM TSysUser (NOLOCK) WHERE FTUsrCode = '$FTUsrCode'";
        $tResult        = $DB->DB_SELECT($tSQL);

        //เดียวอ่านภาษาจาก session ถ้าไม่มีอ่านจาก config 
        if(isset($tResult[0]['FTUsrLng'])){
            switch($tResult[0]['FTUsrLng']){
                case 0:
                    $tLang = 'en';
                    break;
                case 1;
                    $tLang = 'th';
                    break;
            }
            $tLangType = $tLang;
        }else{
            $tLangType = $config['language'];
        }
        include('application/language/' .$tLangType . '/' . $ptPathFile . '/' . $tMenuName . '_lang.php');

        if(@$lang[$ptLangName] == ''){
            $lang[$ptLangName] = $ptLangName;
        }
        
        return $lang[$ptLangName];
        
    }

    

?>