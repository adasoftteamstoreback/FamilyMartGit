<?php 

    //parameter
    //$_GET['module']           : ชื่อ module
    //$_GET['func_method']      : ชื่อ method
    //http://localhost:8080/FamilymartGit/?route=invoice&call_type=winapp&param=[value1=50,value2=02,value3=003,value4=004]

    ini_set("memory_limit","-1");
    ini_set("max_execution_time", 0);
    include('system/core/Autoloader.php');
    include('system/core/Controller.php');
    include('route.php');

    new Autoloader;

    //มีพวก route
    if(isset($_GET['route'])){
        $tRoute     = 'tROUTE_'.$_GET['route'];
        $tClass     = 'c'.$_GET['route'];

        if(isset($$tRoute)){ //case : มี route
            if(isset($_GET['func_method'])){  //case : มี method
                $tMethod    = $_GET['func_method'];
            }else{ //case : ไม่มี method
                $tMethod    = 'index';
            }

            if(isset($_GET['Param'])){
                $tParameter = $_GET['Param'];
            }else{
                $tParameter = '';
            }

            if(isset($_GET['calltype'])){
                if($_GET['calltype'] != 'WIN'){
                    $tCallType = 'WEB';
                }else{
                    $tCallType = $_GET['calltype'];
                }
            }else{
                $tCallType = 'WEB';
            }

            require_once($$tRoute);
        }else{ //case : route ผิด
            $tRoute     = 'application/modules/error/controllers/errorlog/cerrorlog.php';
            $tClass     = 'cerrorlog';
            $tMethod    = 'index';
            $tCallType  = 'WEB';
            $tParameter = '';
            require_once($tRoute);
            //echo 'errorpage';
        }
    }else{  //case : ไม่มี route
        $tRoute     = 'application/modules/common/controllers/cCommon.php';
        $tClass     = 'cCommon';
        $tMethod    = 'index';
        $tCallType  = 'WEB';
        $tParameter = '';
        require_once($tRoute);
    }

    $oController = new Controller;
    $oController->Configdatabase($tParameter,$tCallType);
    include('system/core/Database.php');

    $oRoute = new $tClass;
    if(isset($tMethod)){
        $tView = $oRoute->$tMethod($tParameter,$tCallType);
        echo $tView;
    }


?>