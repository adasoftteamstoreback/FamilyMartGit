<?php

class cCommon extends Controller{

    public function __construct(){
        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }
    }

    public function index($tParameter,$tCallType){
        
        $aArrayHead = array(
            'tParameter'    => $this->PackDatatoarray($tParameter,$tCallType),
        );
        echo $this->RequestView('common','mainpage/wHeader', $aArrayHead);

        $aArrayContent = array(
            'tModulename'   => 'common',
            'tParameter'    => $tParameter,
            'tCallType'     => $tCallType
        );
        echo $this->RequestView('common','mainpage/wContent',$aArrayContent);

        $aArrayFooter = array();
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);
    }

    public function FSxCOMContentMain(){
        echo 'content common';
    }

    // public function test(){
    //     session_destroy();
    // }

    public function FSxCCOMCallBGPHP(){
        // exec("C:/inetpub/wwwroot/FamilyMartGit/BackgroundProcess/application/third_party/AutoRunBgPHP[1.0.0.1][001].exe", $output, $return_var);
        // echo 'run';
        // $output=null;
        // $retval=null;
        exec('C:/inetpub/wwwroot/FamilyMartGit/BackgroundProcess/application/third_party/AutoRunBgPHP.exe');
        // echo "Returned with status $retval and output:\n";
        // print_r($output);
        // $WshShell = new COM("WScript.Shell");
        // $oExec = $WshShell->Run("BackgroundProcess/application/third_party/AutoRunBgPHP[1.0.0.1][001].exe", 7, false);
    }

}

?>