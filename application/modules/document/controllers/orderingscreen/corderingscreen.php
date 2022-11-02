<?php
class comnOrderingScreen extends Controller {

    public function __construct(){
        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('document','orderingscreen/morderingscreen');

        $this->input = new Input();
        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }
    }

    public function index($tParameter,$tCallType){

        //set database
        // $this->Configdatabase($tParameter,$tCallType);

        $tAllparameter          = $this->PackDatatoarray($tParameter,$tCallType);
        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        $aArrayHead = array(
            'tParameter'        => $tAllparameter,
            'tPathLogoimage'    => $this->mgeneral->FSaMCOMGetPathLogoImage(),
            'tUseraccount'      => $this->mgeneral->FSaMCOMGetDetailProfile($tAllparameter[0]['Username'])
        );
        echo $this->RequestView('common','mainpage/wHeader', $aArrayHead);

        $aArrayContent = array(
            'tModulename'   => 'omnOrderingScreen',
            'tParameter'    => $this->PackData($tParameter,$tCallType),
            'tCallType'     => $tCallType,
            'tCNParameter'  => $tParameter,
            'tCNCallType'   => $tCallType
        );

        //ถ้า FTSysUsrValue ใน TSysConfig = 0 ให้โชว์เป็นค่าว่าง
        //แก้ไข 15/07/2019 วัฒน์
        $aChkSugQty = $this->morderingscreen->FSaMODSCheckSuggestQty();
        $_SESSION["tSysUsrValueOrderingScreen"] = $aChkSugQty['nValue'];

        // $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
        echo $this->RequestView('common','mainpage/wContent', $aArrayContent);

        $aArrayFooter = array(
            'tModules'  => 'document',
            'tFeatures' => 'orderingscreen'
        );
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);

        //Setting RabbitMQ
        $this->mgeneral->ConfigRabbitMQ();

        //Setting Permission
        $this->mgeneral->SettingPermission($aArrayContent['tModulename']);
    }

    //content
    public function FSxCODSContentMain(){
        
        $tCallType  = $this->input->post('tCallType');
        $tParameter = $this->input->post('tParamter');

        if($tCallType == 'null'){ $tCallType = 'WEB';
        }else if(isset($tCallType)){ $tCallType = $tCallType;
        }else{ $tCallType = 'WEB'; }

        if($tParameter == null || $tParameter == ''){ $aDataresult    = 'null';
        }else{ $aDataresult    = $tParameter; }

        //ลบข้อมูล HD DT ตามวันที่ TsysConfig
        // $this->morderingscreen->FSaMODSPurgeAuto();
        $aDataNotSubmit = $this->morderingscreen->FSaMODSCheckDataNotSubmitOrder();
        $aChkSugQty = $this->morderingscreen->FSaMODSCheckSuggestQty();
        $aArrayContent = array(
            'tModulename'       => 'omnOrderingScreen',
            'tParameter'        => $aDataresult,
            'tCallType'         => $tCallType,
            'nChkSugQty'        => $aChkSugQty['nValue'],
            'aDataNotSubmit'    => $aDataNotSubmit
        );
        echo $this->RequestView('document','orderingscreen/worderingscreen',$aArrayContent);
    }

    public function FSxCODSCallPageMain(){
        $tDocNo         = $this->input->post('tDocNo');
        $aCheckPOTime   = $this->morderingscreen->FSaMODSCheckPOTime();

        $tTime      = date('H:i:s');
        $dNewDate   = date('Y-m-d');
        $dDate      = date_create($dNewDate);
        if($tTime > $aCheckPOTime['aReturn']['FTSysUsrValue']){
            date_add($dDate, date_interval_create_from_date_string('1 days'));
            date_format($dDate, 'Y-m-d');
        }

        $aDataNotSubmit = $this->morderingscreen->FSaMODSCheckDataNotSubmitOrder();
        if($tDocNo == ""){
            if($aDataNotSubmit['nStaQuery'] == 1){
                $aChkPOFlag = $this->morderingscreen->FSaMODSCheckPOFlag($aDataNotSubmit['aReturn']['FTXohDocNo']);
                $aArrayContent = array(
                    'FTXohDocNo'        => $aDataNotSubmit['aReturn']['FTXohDocNo'],
                    'FTXohStaPrcDoc'    => $aDataNotSubmit['aReturn']['FTXohStaPrcDoc'],
                    'FTXohStaDoc'       => $aDataNotSubmit['aReturn']['FTXohStaDoc'],
                    'FDXohDocDate'      => date_format($aDataNotSubmit['aReturn']['FDXohDocDate'],'Y-m-d'),
                    'tDataNotSubmit'    => $aDataNotSubmit['nStaQuery']
                    // 'nChkPOFlag'        => $aChkPOFlag['nCount']
                );
            }else{
                $aArrayContent = array(
                    'FTXohDocNo'        => $tDocNo,
                    'FDXohDocDate'      => date_format($dDate,'Y-m-d'),
                    'tDataNotSubmit'    => 1
                    // 'nChkPOFlag'        => 0
                );
            }
        }else{
            $aDataHD    = $this->morderingscreen->FSaMODSGetDataHD($tDocNo);
            $aChkPOFlag = $this->morderingscreen->FSaMODSCheckPOFlag($tDocNo);
            if($aDataHD['nStaQuery'] == 1){
                $aArrayContent = array(
                    'FTXohDocNo'        => $aDataHD['aReturn']['FTXohDocNo'],
                    'FTXohStaPrcDoc'    => $aDataHD['aReturn']['FTXohStaPrcDoc'],
                    'FTXohStaDoc'       => $aDataHD['aReturn']['FTXohStaDoc'],
                    'FDXohDocDate'      => date_format($aDataHD['aReturn']['FDXohDocDate'],'Y-m-d'),
                    'tDataNotSubmit'    => $aDataNotSubmit['nStaQuery']
                    // 'nChkPOFlag'        => $aChkPOFlag['nCount']
                );
            }
        }

        $this->morderingscreen->FSxMODSDelDocTmpDT();

        echo $this->RequestView('document','orderingscreen/worderingscreenMain',$aArrayContent);
    }

    public function FSxCODSDataTable(){
        $tSection       = $this->input->post('tSection');
        $tDocNo         = $this->input->post('tDocNo');
        $nPage          = $this->input->post('nPageCurrent');
        $tFromSec       = $this->input->post('ptFromSec');
        $nLimitRecord   = $this->input->post('pnLimitRecord');
        $aSortBycolumn  = $_POST['paSortBycolumn'];
        
        if($tFromSec == "SUMMARY"){

            $aTotal     = $this->morderingscreen->FSaMODSGetOrderSKU(array(
                'tDocNo'    => $tDocNo,
                'tSection'  => ''
            ));
            $tGetDataSummary =  $this->RequestView('document','orderingscreen/worderingscreenDataTable', array(
                'tODSCurrentSecion'     => 'SUMMARY',
                'nTotalSKU'             => $aTotal['OrderSKU'],
                'nTotalAmount'          => $aTotal['SKUAmount'],
                'bOepnSource'           => false
            ));

            $aArrayQuery = array(
                'tSection'      => $tSection,
                'tDocNo'        => $tDocNo,
                'nPage'         => $nPage,
                'nRow'          => $nLimitRecord,
                'aSortBy'       => $aSortBycolumn,
                'tDisableRow'   => true
            );
        }else{
            $aArrayQuery = array(
                'tSection'      => $tSection,
                'tDocNo'        => $tDocNo,
                'nPage'         => $nPage,
                'nRow'          => $nLimitRecord,
                'aSortBy'       => $aSortBycolumn,
                'tDisableRow'   => false
            );
        }

        $aData          = $this->morderingscreen->FSxMODSDataList($aArrayQuery);
        $aFoundOrdLot   = $this->morderingscreen->FSaMODSCheckOrdLot($aArrayQuery);

        if($tFromSec == "SUMMARY"){
            $tDataTable =  $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'                => $tDocNo,
                'nPage'                 => $nPage,
                'aODSDataTable'         => $aData,
                'tODSCurrentSecion'     => $tSection,
                'tODSFromSec'           => $tFromSec,
                'nRowTable'             => $nLimitRecord,
                'bOepnSource'           => false
            ));
        }else{
            $tDataTable =  $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'                => $tDocNo,
                'nPage'                 => $nPage,
                'aODSDataTable'         => $aData,
                'tODSCurrentSecion'     => $tSection,
                'tODSFromSec'           => $tFromSec,
                'nRowTable'             => $nLimitRecord,
                'bOepnSource'           => true
            ));
        }

        $aReturn = array(
            'ptGetDataSummary'      => @$tGetDataSummary,
            'ptDataTable'           => $tDataTable,
            'pnFoundOrdLot'         => $aFoundOrdLot,
            'tSQL'                  => $aData,
            'tScript'               => $this->RequestView('document','orderingscreen/script/jorderingscreenDataTable')

        );

        echo json_encode($aReturn);
    }

    public function FSxCODSDataTableSummary(){
        $tSection       = $this->input->post('tSection');
        $tDocNo         = $this->input->post('tDocNo');
        $nPage          = $this->input->post('nPageCurrent');
        $nLimitRecord   = $this->input->post('pnLimitRecord');
        $aSortBycolumn  = $_POST['paSortBycolumn'];


        $aTotal = $this->morderingscreen->FSaMODSGetOrderSKU(array(
            'tDocNo'    => $tDocNo,
            'tSection'  => ''
        ));

        $tDataSummary = $this->RequestView('document','orderingscreen/worderingscreenDataTable', array(
            'tODSCurrentSecion'     => 'SUMMARY',
            'nTotalSKU'             => $aTotal['OrderSKU'],
            'nTotalAmount'          => $aTotal['SKUAmount'],
            'bOepnSource'           => false
        ));

        if($tSection == "NEW" || $tSection == "SUMMARY"){

            $aDataSecNEW = $this->morderingscreen->FSxMODSDataList(array(
                'aSortBy'           => $aSortBycolumn,
                'tDisableRow'       => true,
                'tSection'          => 'NEW',
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'nRow'              => $nLimitRecord
            ));

            $tDataSecNew = $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'aODSDataTable'     => $aDataSecNEW,
                'tODSCurrentSecion' => 'NEW',
                'tODSFromSec'       => 'SUMMARY',
                'nRowTable'         => $nLimitRecord,
                'bOepnSource'       => false
            ));

        }
        if($tSection == "PROMOTION" || $tSection == "SUMMARY"){

            $aDataSecPROMOTION = $this->morderingscreen->FSxMODSDataList(array(
                'aSortBy'           => $aSortBycolumn,
                'tDisableRow'       => true,
                'tSection'          => 'PROMOTION',
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'nRow'              => $nLimitRecord
            ));
            
            $tDataSecPro = $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'aODSDataTable'     => $aDataSecPROMOTION,
                'tODSCurrentSecion' => 'PROMOTION',
                'tODSFromSec'       => 'SUMMARY',
                'nRowTable'         => $nLimitRecord,
                'bOepnSource'       => false
            ));

        }
        if($tSection == "TOP1000" || $tSection == "SUMMARY"){

            $aDataSecTOP1000 = $this->morderingscreen->FSxMODSDataList(array(
                'aSortBy'           => $aSortBycolumn,
                'tDisableRow'       => true,
                'tSection'          => 'TOP1000',
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'nRow'              => $nLimitRecord
            ));

            $tDataSecTop = $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'aODSDataTable'     => $aDataSecTOP1000,
                'tODSCurrentSecion' => 'TOP1000',
                'tODSFromSec'       => 'SUMMARY',
                'nRowTable'         => $nLimitRecord,
                'bOepnSource'       => false
            ));

        }
        if($tSection == "OTHER" || $tSection == "SUMMARY"){

            $aDataSecOTHER = $this->morderingscreen->FSxMODSDataList(array(
                'aSortBy'           => $aSortBycolumn,
                'tDisableRow'       => true,
                'tSection'          => 'OTHER',
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'nRow'              => $nLimitRecord
            ));

            $tDataSecOth = $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'aODSDataTable'     => $aDataSecOTHER,
                'tODSCurrentSecion' => 'OTHER',
                'tODSFromSec'       => 'SUMMARY',
                'nRowTable'         => $nLimitRecord,
                'bOepnSource'       => false
            ));

        }
        if($tSection == "ADDON" || $tSection == "SUMMARY"){

            $aDataSecADDON = $this->morderingscreen->FSxMODSDataList(array(
                'aSortBy'           => $aSortBycolumn,
                'tDisableRow'       => true,
                'tSection'          => 'ADDON',
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'nRow'              => $nLimitRecord
            ));

            $tDataSecAdd = $this->RequestView('document','orderingscreen/worderingscreenDataTable',array(
                'tDocNo'            => $tDocNo,
                'nPage'             => $nPage,
                'aODSDataTable'     => $aDataSecADDON,
                'tODSCurrentSecion' => 'ADDON',
                'tODSFromSec'       => 'SUMMARY',
                'nRowTable'         => $nLimitRecord,
                'bOepnSource'       => true
            ));

        }
        $aArrayQuery    = array(
            'tDocNo' => $tDocNo
        );
        $aFoundOrdLot   = $this->morderingscreen->FSaMODSCheckOrdLot($aArrayQuery);

        $aArrayContent = array(
            'tSum'              => $tDataSummary,
            'tNew'              => $tDataSecNew,
            'tPro'              => $tDataSecPro,
            'tTop'              => $tDataSecTop,
            'tOth'              => $tDataSecOth,
            'tAdd'              => $tDataSecAdd,
            'pnFoundOrdLot'     => $aFoundOrdLot,
            'tScript'=> $this->RequestView('document','orderingscreen/script/jorderingscreenDataTable')
        );

        echo json_encode($aArrayContent);
        // print_r($aArrayContent);
    }

    public function FSxCODSAddDocTmpDT(){
        $dDateOrder  = $this->input->post('pdDateOrder');
        $tReplace    = str_replace('/', '-', $dDateOrder);
        $dDateFormat = date_format(date_create($tReplace),'Y-m-d');
        $dCurentDay  = date_format(date_create($tReplace),"w");

        $this->FSxCODSWriteLog('========== START LOAD ORDER ==========');
        $aChkSGO  = $this->morderingscreen->FSxMODSCheckSGOItem($dDateFormat);
        if($aChkSGO['nStaQuery'] == 99){ //ไม่พบสินค้าใน SGOItem
            $aReturn = array(
                'nStaCheckInsertData'   => 77,
                'tSQL'                  => $aChkSGO
            );
        }else{
            $aDataAddDT = array(
                'dDateCurrent'  => $dDateFormat,
                'dDateOrder'    => date_format(date_create($aChkSGO['dOrderDate']),'Y-m-d'),
                'dCurentDay'    => $dCurentDay,
                'dDate'         => date('Y-m-d'),
                'tTime'         => date('H:i:s'),
                'tUser'         => $_SESSION["SesUsercode"]
            );

            $aDelData = $this->morderingscreen->FSxMODSDelDocTmpDT();
            $aAddData = $this->morderingscreen->FSxMODSCallSP_FTHSGO($aDataAddDT);
            $aAddData = $this->morderingscreen->FSxMODSCallSTP_PRCxGetPdtPO1($aDataAddDT);
            $aAddData = $this->morderingscreen->FSxMODSCallSTP_PRCxGetPdtPO2($aDataAddDT);

            // $aAddData = $this->morderingscreen->FSxMODSAddDocTmpDT($aDataAddDT);
            // $aChkTmp  = $this->morderingscreen->FSaMODSSelectCheckTemp();
            // $aChkHD   = $this->morderingscreen->FSaMODSCheckDateOnHD($dDateFormat);

            if($aAddData['nStaQuery'] == 99){ //ไม่พบสินค้าใน Temp แสดงว่า Insert สินค้าไม่สำเร็จ
                if($aAddData['tReturnInsert'] != NULL){// โหลดออเดอร์ไม่ได้ พบข้อผิดพลาดใน Query
                    $aReturn = array(
                        'nStaCheckInsertData'   => 99,
                        'tReturnAddData'        => $aAddData
                    );
                    $this->FSxCODSWriteLog('[FSxCODSAddDocTmpDT] โหลดออเดอร์ไม่สำเร็จ พบข้อผิดพลาดใน Query');
                }else{// โหลดออเดอร์ และไม่มีสินค้า
                    $aArrayContent = array(
                        'FTXohDocNo'        => '',
                        'FDXohDocDate'      => $dDateFormat
                    );
                    $tLoadViewMain = $this->RequestView('document','orderingscreen/worderingscreenMain',$aArrayContent);
                    $aReturn = array(
                        'tLoadViewMain'         => $tLoadViewMain,
                        'nStaCheckInsertData'   => 88,
                        'tReturnAddData'        => $aAddData,
                        'tSQL_SGOITEM'          => $aChkSGO
                    );
                    $this->FSxCODSWriteLog('[FSxCODSAddDocTmpDT] โหลดออเดอร์สำเร็จ ไม่มีข้อมูลสินค้า');
                }
            }else{ //insert สินค้าสำเร็จ
                $aArrayContent = array(
                    'FTXohDocNo'        => '',
                    'FDXohDocDate'      => $dDateFormat
                );
                $tLoadViewMain = $this->RequestView('document','orderingscreen/worderingscreenMain',$aArrayContent);
                $aReturn = array(
                    'tLoadViewMain'         => $tLoadViewMain,
                    'nStaCheckInsertData'   => 1,
                    'tReturnAddData'        => $aAddData,
                    'tSQL_SGOITEM'          => $aChkSGO
                );
                $this->FSxCODSWriteLog('[FSxCODSAddDocTmpDT] โหลดออเดอร์สำเร็จ');
            }
        }
        echo json_encode($aReturn);
    }

    public function FSxCODSAddEditHDDT(){
        $nStaSave   = $this->input->post('nStaSave');
        $dOrderDate = $this->input->post('pdOrderDate');
        $tReplace    = str_replace('/', '-', $dOrderDate);
        $dDateFormat = date_format(date_create($tReplace),'Y-m-d');

        if($nStaSave == 1){ //1 Add
            $tDocNo     = generateCode('TSPoHD','FTXohDocNo');
            $aBranch    = getBranch();

            $aDataUpdateHD = array(
                'tDocNo'        => $tDocNo,
                'tBranch'       => $aBranch['FTBchCode'],
                'FDXohDocDate'  => $dDateFormat,
                'tDocType'      => '1',
                'tStaDoc'       => '1',
                'dDocDate'      => date('Y-m-d'),
                'tDocTime'      => date('H:i:s'),
                'tUser'         => $_SESSION["SesUsercode"]
            );

            $aB = $this->morderingscreen->FSxMODSAddEditHD($aDataUpdateHD);
            $aC = $this->morderingscreen->FSxMODSDTUpdateDocNo($aDataUpdateHD['tDocNo']);

            $aDataResult = array(
                'FTXohDocNo'    => $tDocNo,
                'nSta'          => '1',
                'tStaMessage'   => 'Add HD and Update DocNo Success'
            );
            $this->FSxCODSWriteLog('=========== END LOAD ORDER ===========');
        }else{ //2 Edit
            $tDocNo     = $this->input->post('ptDocNo');

            $aDataResult = array(
                'FTXohDocNo'    => $tDocNo,
                'nSta'          => '2',
                'tStaMessage'   => 'Edit Data Success'
            );
        }
        echo json_encode($aDataResult);
    }

    public function FSxCODSConfirmOrder(){

        $this->FSxCODSWriteLog('=========== START APPROVAL ===========');

        $tDocNo = $this->input->post('ptDocNo');
        $aDataUpdateHD = array(
            'tDocNo'        => $tDocNo,
            'nStaPrcDoc'    => '1',
        );
        $this->morderingscreen->FSaMODSUpdOrdLotAndOrdPcsToNull($aDataUpdateHD['tDocNo']); //ลบรายการ แนะนำ ที่ไม่ได้สั่งซื้อ
        $aUpdStaPrcDoc  = $this->morderingscreen->FSxMODSUpdateStaPrcDoc($aDataUpdateHD);
        $aDataResult = array(
            'ptDocNo'       => $tDocNo,
            'aQueryReturn'  => $aUpdStaPrcDoc,
            'nSta'          => '1',
            'tStaMessage'   => 'Success'
        );
        echo json_encode($aDataResult);
    }

    public function FSxCODSUpdateOrderLot(){
        $nOrderLot      = $this->input->post('nOrderLot');
        $tPdtBarCode    = $this->input->post('tPdtBarCode');
        $nSeq           = $this->input->post('nSeq');
        $tSection       = $this->input->post('tSection');
        $nPdtLotSize    = $this->input->post('nPdtLotSize');
        $tDocNo         = $this->input->post('tDocNo');

        $aDataUpdateDT = array(
            'nOrderLot'     => $nOrderLot,
            'nSeq'          => $nSeq,
            'tPdtBarCode'   => $tPdtBarCode,
            'nPdtLotSize'   => $nPdtLotSize,
            'tDocNo'        => $tDocNo,
            'tSec'          => $tSection
        );
        // var_dump($aDataUpdateDT);
        // exit;
        if($aDataUpdateDT['nOrderLot'] == "NULL" && $aDataUpdateDT['tSec'] == "ADDON"){
            $aDelDTByID = $this->morderingscreen->FSaMODSDelDTByID($aDataUpdateDT);
            $aDataResult = array(
                'nStaQuery'     => $aDelDTByID['nStaQuery'],
                'nSta'          => 1,
                'tStaMessage'   => 'Success',
            );
        }else{
            $aCheckPdtMax  = $this->morderingscreen->FSxMODSCheckPdtMax($aDataUpdateDT);
            if($aCheckPdtMax['nStaQuery'] == 1 || ($aDataUpdateDT['nOrderLot'] == "NULL" && $aDataUpdateDT['tSec'] != "ADDON")){
                $aUpdateOrdLot = $this->morderingscreen->FSxMODSUpdateOrderLot($aDataUpdateDT);
                $aDataResult = array(
                    'nStaQuery'     => $aUpdateOrdLot['nStaQuery'],
                    'nSta'          => 1,
                    'tStaMessage'   => 'Success',
                );
            }else{
                $aDataResult = array(
                    'nStaQuery'     => $aCheckPdtMax['nStaQuery'],
                    'nSta'          => 99,
                    'tStaMessage'   => 'OrderLOT > PdtMax'
                );
            }
        }
        echo json_encode($aDataResult);
    }

    public function FSxCODSUpdateStaDoc(){
        $nStaDoc = $this->input->post('pnStaDoc');
        $tDocNo  = $this->input->post('ptDocNo');
        $aDataUpdateDT = array(
            'nStaDoc'     => $nStaDoc,
            'tDocNo'      => $tDocNo
        );
        $aUpdate = $this->morderingscreen->FSxMODSUpdateStaDoc($aDataUpdateDT);

        $aDataResult = array(
            'nStaQuery'     => $aUpdate['nStaQuery'],
            'nSta'          => '1',
            'tStaMessage'   => 'Success'
        );
        echo json_encode($aDataResult);
    }

    public function FSxCODSCopySGOQTY(){
        $tSec       = $this->input->post('ptSec');
        $tDocNo     = $this->input->post('ptDocNo');
        $aUpdate    = $this->morderingscreen->FSxMODSCopySGOQTY(array(
            'ptSec'     => $tSec,
            'ptDocNo'   => $tDocNo
        ));

        $aDataResult = array(
            'tModelReturn'  => $aUpdate,
            'nSta'          => '1',
            'tStaMessage'   => 'Success'
        );
        echo json_encode($aDataResult);
    }

    public function FSxCODSAddPdtOrder(){
        $aPdt       = $_POST['paPdt'];
        $tDocNo     = $this->input->post('ptDocNo');
        $aChkOldSec = $this->morderingscreen->FSaMODSCheckOldSection($aPdt['FTPdtBarCode'],$tDocNo);
        if($aChkOldSec['nStaQuery'] == 1){
            // $FTPdtSecStatus = $aChkOldSec['aResult']['FTPdtSecCode'];
            $aDataResult = array(
                'aItems'          =>  $aChkOldSec['aResult'],
                // 'aItems'           => array(
                //     'nRowID'        => $aChkOldSec['aResult']['rtRowID'],
                //     'FTXohDocNo'    => $tDocNo,
                //     'FNXdtSeqNo'    => $aChkOldSec['aResult']['FNXdtSeqNo'],
                //     'FTPdtBarCode'  => $aPdt['FTPdtBarCode'],
                //     'FTPdtName'     => $aPdt['FTPdtName'],
                //     'FTPdtSecCode'  => $aChkOldSec['aResult']['FTPdtSecCode']
                // ),
                'nStaQuery'        =>  88,
                'tStaMessage'      =>  'Error'
            );
        }else{
            $FTPdtSecStatus = NULL;
            $aDataWhere = array(
                'FTXohDocNo'            => $tDocNo,
                'FTPdtSecCode'          => 'ADDON'
            );
            $aSeqPdt    = $this->morderingscreen->FSaMODSGetSeqLastDTAddon($aDataWhere);
            if($aSeqPdt['nStaQuery'] == 1){
                $tSeqNo = $aSeqPdt['aReturn']['FNXdtSeqNo'] + 1;
            }else{
                $tSeqNo = 1;
            }
            $aDataListDT = array(
                'FTXohDocNo'            => $tDocNo,
                'FNXdtSeqNo'            => $tSeqNo,
                'FTPdtSecCode'          => 'ADDON',
                'FTPdtCategory'         => $aPdt['CATEGORY'],
                'FTPdtSubCat'           => $aPdt['SUBCAT'],
                'FTPdtCode'             => $aPdt['FTPdtCode'],
                'FTPdtName'             => $aPdt['FTPdtName'],
                'FTPdtBarCode'          => $aPdt['FTPdtBarCode'],
                'FTPdtDelivery'         => $aPdt['FTStyName'],
                'FCPdtIntransit'        => $aPdt['IN_TRANSIT'],
                'FCPdtCost'             => $aPdt['FCPdtCostStd'],
                'FCPdtPrice'            => $aPdt['FCPdtRetPri1'],
                'FTPdtPromo'            => $aPdt['PROMO'],
                'FDDeliveryDate'        => $aPdt['DELIVERY_DATE'],
                'FCPdtStock'            => $aPdt['FCPdtQtyRet'],
                'FCPdtLotSize'          => $aPdt['FCPdtStkFac'],
                'FCPdtADS'              => $aPdt['ADS'],
                'FCPdtSGOQty'           => $aPdt['FCSugQty'],
                'FCPdtOrdLot'           => $aPdt['ORDER_LOT'],
                'FCPdtOrdPcs'           => $aPdt['ORDER_PCS'],
                'FTSplCode'             => $aPdt['FTSplCode'],
                'FTVatCode'             => $aPdt['FTSplViaRmk'],
                'FTPdtPOFlag'           => $aPdt['POFlag'],
                'FTPdtSecStatus'        => $FTPdtSecStatus,
                'FDDateUpd'             => date('Y-m-d'),
                'FTTimeUpd'             => date('H:i:s'),
                'FTWhoUpd'              => $_SESSION["SesUsercode"],
                'FDDateIns'             => date('Y-m-d'),
                'FTTimeIns'             => date('H:i:s'),
                'FTWhoIns'              => $_SESSION["SesUsercode"]
            );
            $aDataResult = $this->morderingscreen->FSaMODSAddPdtOrder($aDataListDT);
        }
        echo json_encode($aDataResult);
    }

    public function FSxCODSUpdateApproveRabbitFail(){
        $tDocNo          = $this->input->post('ptDocno');

        $this->FSxCODSWriteLog('[FSxCODSUpdateApproveRabbitFail] อนุมัติเอกสาร '.$tDocNo.' ไม่สำเร็จ');
        $this->FSxCODSWriteLog('=========== END APPROVAL ===========');

        $aUpdRabFail     = $this->morderingscreen->FSaMODSUpdateApproveRabbitFail($tDocNo);
        echo json_encode($aUpdRabFail);
    }

    public function FSxCODSSearchPOHD(){
        $nPageCurrent = $this->input->post('pnPageCurrent');
        $aData  = array(
            'nPage'         => $nPageCurrent,
            'nRow'          => 5,
            'tSearchAll'    => ''
        );
        $aResult        = $this->morderingscreen->FSaMODSSearchPOHD($aData);
        $aResultData    = array(
            // 'ptNameroute' => $this->input->post('ptNameroute'),
            'aDataSearch'   => $aResult,
            'nPage'         => $nPageCurrent,
            'tSearchAll'    => ''
        );
        echo $this->RequestView('document','orderingscreen/worderingscreenSearch',$aResultData);
    }

    public function FSxCODSCloseBrowser(){
        $tType = $this->input->post('ptType');
        $aSelChkTmp = $this->morderingscreen->FSaMODSSelectCheckTemp();
        echo json_encode($aSelChkTmp);
    }

    public function FSxCODSRabbitSuccess(){
        $tDocNo  = $this->input->post('ptDocno');

        $this->FSxCODSWriteLog('[FSxCODSRabbitSuccess] อนุมัติเอกสาร '.$tDocNo.' สำเร็จ');
        $this->FSxCODSWriteLog('=========== END APPROVAL ===========');
        
        $aResult = $this->morderingscreen->FSaMODSGetPOList($tDocNo);
        echo $this->RequestView('document','orderingscreen/worderingscreenPOList',$aResult);
    }

    public function FSxCODSAddPdtManual(){
        $tSearchPdt     = $this->input->post('ptSearchPdt');
        $tDocNo         = $this->input->post('ptDocNo');
        $dOrderDate     = $this->input->post('pdOrderDate');

        // $aChkOldSec = $this->morderingscreen->FSaMODSCheckOldSection($tSearchPdt,$tDocNo);
        // if($aChkOldSec['nStaQuery'] == 1){
        //     $FTPdtSecStatus = $aChkOldSec['aResult']['FTPdtSecCode'];
        // }else{
        //     $FTPdtSecStatus = NULL;
        // }

        // $aDataWhere = array(
        //     'FTXohDocNo'            => $tDocNo,
        //     'FTPdtSecCode'          => 'ADDON'
        // );
        // $aSeqPdt    = $this->morderingscreen->FSaMODSGetSeqLastDTAddon($aDataWhere);
        // if($aSeqPdt['nStaQuery'] == 1){
        //     $nSeqNo = $aSeqPdt['aReturn']['FNXdtSeqNo'] + 1;
        // }else{
        //     $nSeqNo = 1;
        // }

        $aDataInsert    = array(
            'ptSearchPdt'   => $tSearchPdt,
            'ptDocNo'       => $tDocNo,
            'pdOrderDate'   => $dOrderDate,
            'ptSection'     => 'ADDON',
            'pdCurentDay'   => date_format(date_create($dOrderDate),"w"),
            'pdCurrentDate' => date('Y-m-d'),
            'ptCurrentTime' => date('H:i:s'),
            'ptUser'        => $_SESSION["SesUsercode"]
        );
        
        // $aChkPdtFrmTmp  = $this->morderingscreen->FSaMODSCheckProductDuplicate($aDataInsert); //Chekc Products Duplicate
        $aChkPdtFrmTmp  = $this->morderingscreen->FSaMODSCheckOldSection($tSearchPdt,$tDocNo); // ใช้ฟังค์ชั่นร่วมกับเพิ่มสินค้าแบบ Browse
        if($aChkPdtFrmTmp['nStaQuery'] == 99){
            //ไม่ซ้ำให้ทำการเพิ่มสินค้าได้
            $aDataReturn = $this->morderingscreen->FSaMODSAddPdtManual($aDataInsert,$aChkPdtFrmTmp); //Insert Products
        }else{  
            //ซ้ำ
            $aDataReturn = array(
                'tSQL'      => $aChkPdtFrmTmp,
                'aItems'    => $aChkPdtFrmTmp['aResult'],
                'nStaQuery' => 88
            );
        }
        echo json_encode($aDataReturn);
    }

    // public function FSxCODSUpdOrdLotAndOrdPcsToNull(){
    //     $tDocNo         = $this->input->post('ptDocNo');
    //     $aChkPdtFrmTmp  = $this->morderingscreen->FSaMODSUpdOrdLotAndOrdPcsToNull($tDocNo);
    //     echo json_encode($aChkPdtFrmTmp);
    // }

    // เช็คว่ามีสาขาสำนักงานใหญ่ไหม ถ้าไม่มีไม่ให้อนุมัติ
    // Comsheet 2020-194 เพิ่มเช็คว่าสินค้ามีผู้จำหน่ายไหม ?
    public function FSxCODSChkBchHQ(){
        $tDocNo = $this->input->post('ptDocNo');
        $aDataReturn = array(
            'aChkBchHQ'       => $this->morderingscreen->FSaMODSChkBchHQ(),
            'aChkSplB4Apv'    => $this->morderingscreen->FSaMODSChkSplB4Apv($tDocNo)
        );
        echo json_encode($aDataReturn);
    }

    // Create By : Napat(Jame) 2020-06-29
    // เขียนไฟล์ Log : หน้าจอสั่งซื้อ
    public function FSxCODSWriteLog($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'ORS_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

}

?>