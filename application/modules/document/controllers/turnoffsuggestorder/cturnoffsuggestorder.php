<?php

class comnTurnOffSuggest extends Controller{

    
    public function __construct(){
        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('document','turnoffsuggestorder/mturnoffsuggestorder');
        $this->input = new Input();
        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }
    }

    //เขียนไฟล์ : หน้าจอปิดคำสั่งซื้อที่แนะนำ
    public function FSxWriteLogByPage($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'TOS_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

    public function index($tParameter,$tCallType){
        //set database
        // $this->Configdatabase($tParameter,$tCallType);

        $tAllparameter = $this->PackDatatoarray($tParameter,$tCallType);
        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        $_SESSION['TurnoffFirtInsert'] = '';
        $aArrayHead = array(
            'tParameter'        => $tAllparameter,
            'tUseraccount'      => $this->mgeneral->FSaMCOMGetDetailProfile($tAllparameter[0]['Username']),
            'tPathLogoimage'    => $this->mgeneral->FSaMCOMGetPathLogoImage()
        );
        echo $this->RequestView('common','mainpage/wHeader', $aArrayHead);

        $aArrayContent = array(
            'tModulename'   => 'omnTurnOffSuggest',
            'tParameter'    => $this->PackData($tParameter,$tCallType),
            'tCallType'     => $tCallType,
            'tCNParameter'  => $tParameter,
            'tCNCallType'   => $tCallType
        );
       
        echo $this->RequestView('common','mainpage/wContent',$aArrayContent);
        $aArrayFooter = array(
            'tModules'  => 'document',
            'tFeatures' => 'turnoffsuggestorder'
        );
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);

        //Setting RabbitMQ
        $this->mgeneral->ConfigRabbitMQ();

        //Setting Permission
        $this->mgeneral->SettingPermission($aArrayContent['tModulename']);

        $this->FSxWriteLogByPage('====================================== START ======================================');
    }

    //content
    public function FSxCTSOContentMain(){
        
        $tCallType  = $this->input->post('tCallType');
        $tParameter = $this->input->post('tParamter');

        if($tCallType == 'null'){ $tCallType = 'WEB';
        }else if(isset($tCallType)){ $tCallType = $tCallType;
        }else{ $tCallType = 'WEB'; }

        if($tParameter == null || $tParameter == ''){ $aDataresult    = 'null';
        }else{ $aDataresult    = $tParameter; }   

        $aArrayContent = array(
            'tModulename'   => 'omnTurnOffSuggest',
            'tParameter'    => $aDataresult,
            'tCallType'     => $tCallType
        );
        echo $this->RequestView('document','turnoffsuggestorder/wturnoffsuggestorder',$aArrayContent);

        $this->FSxWriteLogByPage('[FSxCTSOContentMain] โหลดหน้าจอ');
    }

    //Insert
    public function FSxCTSOInsertIntoTableTemp(){
        $tParameter     = $_POST['tParamter'];
        $tDocumentID    = $this->input->post('tDocumentID');

        $tResult   = $this->mturnoffsuggestorder->FSxMTSOInsertPDT($tParameter,$tDocumentID,'PDT');
        echo JSON_encode($tResult);

        $this->FSxWriteLogByPage('[FSxCTSOInsertIntoTableTemp] เพิ่มข้อมูลสินค้าแบบเลือก');
    }

    //Insert BY Barcode
    public function FSxCTSOInsertIntoTableTempBarcode(){
        $tPDTCodeorBarcode      = $this->input->post('tPDTCodeorBarcode');
        $tDocumentID            = $this->input->post('tDocumentID');
        //Insert
        $tResult   = $this->mturnoffsuggestorder->FSxMTSOInsertPDT($tPDTCodeorBarcode,$tDocumentID,'PDTBarcode');
        echo JSON_encode($tResult);

        if($tResult[0] != 'nodata'){
            $this->FSxWriteLogByPage('[FSxCTSOInsertIntoTableTemp] เพิ่มข้อมูลสินค้าแบบคีย์');
        }
    }

    //select order by staprcDoc == '' 
    public function FSXCTSOSelectTemporderByStaprcDoc(){
        $tDocumentCheckID = $this->mturnoffsuggestorder->FSxMTSOSelectNoneApprove();
        if($tDocumentCheckID != 'false'){
            $aDataMove  = array(
                'tDocumentID'   => $tDocumentCheckID
            );
            $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
            $this->mturnoffsuggestorder->FSxMTSOMoveMastertoTemp($aDataMove);
        }else{
            $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
        }
    }

    //Select Temp
    public function FSxCTSOSelectPDTTempintoTable(){
        $nPage          = $this->input->post('nPageCurrent');
        $tSearchAll     = $this->input->post('tSearchAll');
        $tDocumentID    = $this->input->post('tDocumentID');
        $tSortBycolumn  = $_POST['tSortBycolumn'];

        if($nPage == 'X99'){
            $this->FSXCTSOSelectTemporderByStaprcDoc();
            $nPage = 1;
        }

        $nLimitRecord = $this->input->post('nLimitRecord');

        $aData  = array(
            'nPage'         => $nPage,
            'nRow'          => $nLimitRecord,
            'tSearchAll'    => trim($tSearchAll),
            'tSortBycolumn' => $tSortBycolumn
        );
        $aResList = $this->mturnoffsuggestorder->FSxMTSOSelectPDT($aData);
        
        $tDocumentCheckID = $this->mturnoffsuggestorder->FSxMTSOSelectNoneApprove();
        if($tDocumentCheckID != 'false'){
            $tDocumentLast  = 'true';
            $tDocNoNew      =  $tDocumentCheckID;
        }else{
            $tDocumentLast  = 'false';
            $tDocNoNew      =  '';
        }

        if($tDocumentID == '' || $tDocumentID == null){ $tDocumentID = ''; }else{ $tDocumentID =  $tDocumentID; }
        $aGenTable  = array(
            'aDataList'                 => $aResList,
            'nPage'                     => $nPage,
            'tSearchAll'                => $tSearchAll,
            'tDocumentID'               => $tDocumentID,
            'aHD'                       => $this->mturnoffsuggestorder->FSxMTSOSelectHD($tDocumentID),
            'tFoundLastDatanoneapprove' => $tDocumentLast,
            'nRowTable'                 => $nLimitRecord,
            'tDocNoNew'                 => $tDocNoNew 
        );

        echo $this->RequestView('document','turnoffsuggestorder/wturnoffsuggestorderDataTable',$aGenTable);  
    }

    //Delete by record
    public function FSxCTSODeletePDTTempintoTable(){
        $ptDocumentNo   = $this->input->post('ptDocumentNo');
        $pnSeq          = $this->input->post('pnSeq');
        $pnProductcode  = $this->input->post('pnProductcode');

        $aDataDeleteWHERE   = array(
            'FNPtdSeqNo'    => $pnSeq ,
            'FTPdtCode'     => $pnProductcode,
            'FTPthDocNo'    => $ptDocumentNo
        );
        
        $tResult        =   $this->mturnoffsuggestorder->FSxMTSODeletePDT($aDataDeleteWHERE);
    }

    //Update
    public function FSxCTSOUpdatePDTTempintoTable(){
        $pnSeq          = $this->input->post('pnSeq');
        $nPDTCode       = $this->input->post('nPDTCode');
        $nBarCode       = $this->input->post('nBarCode');
        $tPDTName       = $this->input->post('tPDTName');
        $tStartDate     = $this->input->post('tStartDate');
        $tEndDate       = $this->input->post('tEndDate');

            $aData  = array(
                'nSeq'           => $pnSeq,
                'nPDTCode'       => $nPDTCode,
                'nBarCode'       => $nBarCode,
                'tPDTName'       => $tPDTName,
                'tStartDate'     => $tStartDate,
                'tEndDate'       => $tEndDate
            );
    
            $aCheckDataDuplicate = $this->mturnoffsuggestorder->FSxMTSOCheckDataDuplicate($aData);
            if(empty($aCheckDataDuplicate)){
                $aUpdate        = $this->mturnoffsuggestorder->FSxMTSOUpdateInLinePDT($aData);
                echo json_encode($aUpdate);
                $this->FSxWriteLogByPage("[FSxMTSOUpdateCaseCanceldocument] อัพเดทสินค้า : ".$nPDTCode. " วันที่ ".$tStartDate."-".$tEndDate);
            }else{
                $aResent = array('duplicate',$aCheckDataDuplicate);
                echo json_encode($aResent);
                $this->FSxWriteLogByPage('[FSxCTSOUpdatePDTTempintoTable] พบข้อมูลซ้ำไม่สามารถอัพเดทได้');
            }
    }

    //Event save 
    public function FSxCTSOSave(){
        $aDataCheck     = array();
        $tDocumentID    = $this->input->post('tDocumentID');
        $tCheckDate     = $this->mturnoffsuggestorder->FSxMTSOCheckdateDuplicate();
        $tStatusDup     = '';
        if(!empty($tCheckDate)){
            foreach($tCheckDate as $nKey=>$tValue){
                $tTextPush   = $tValue['FTPdtCode'] . '/' . $tValue['FDPdtStartdate'] . '/' . $tValue['FDPdtEnddate'];
                $tTextSearch = array_search($tTextPush,$aDataCheck);
                
                if($tTextSearch===false) {
                    array_push($aDataCheck,$tTextPush);
                }else{
                    $tStatusDup = 'Duplicate';
                    $this->FSxWriteLogByPage('[FSxCTSOSave] มีรหัสสินค้า / วันที่เริ่ม / วันที่สิ้นสุดซ้ำ' . $tTextPush);
                }
            }
        }

        if($tStatusDup == 'Duplicate'){
            echo 'Duplicate';
        }else{
            $tDocno = $this->mturnoffsuggestorder->FSxMTSOSavePDT($tDocumentID);
            echo $tDocno;
            $this->FSxWriteLogByPage('[FSxCTSOSave] สร้างเอกสาร / บันทึกเอกสาร สำเร็จ ' . $tDocno);
        }
    }
    
    //Event Approve
    public function FSxCTSOApprove(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $this->mturnoffsuggestorder->FSxMTSOApprove($tDocumentID);
        $this->mturnoffsuggestorder->FSxMTSODeleteTemp();

        $this->FSxWriteLogByPage('[FSxCTSOApprove] อนุมัติเอกสาร ' . $tDocumentID . 'สำเร็จ');
    }

    //Event New form
    public function FSxCTSONewform(){
        $ptType     = $this->input->post('type');
        $ptDocno    = $this->input->post('Docno');
        $nStaprcDoc = $this->input->post('nStaprcDoc');
        if($ptType == 'new'){
            $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
            $this->FSxWriteLogByPage('[FSxCTSONewform] สร้างเอกสารใหม่');
            // $tResult = $this->mturnoffsuggestorder->FSxMTSOSelectCheckTemp();
        }else if($ptType == 'cancel'){
            $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
            $this->mturnoffsuggestorder->FSxMTSOUpdateCaseCanceldocument($ptDocno);
            $this->FSxWriteLogByPage('[FSxCTSONewform] ยกเลิกเอกสาร');
            // $tResult = $this->mturnoffsuggestorder->FSxMTSOSelectCheckTemp();
        }else if($ptType == 'confirm'){
            $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
            // $tResult = $this->mturnoffsuggestorder->FSxMTSOSelectCheckTemp();
        }else if($ptType == 'close'){
            $tResult = $this->mturnoffsuggestorder->FSxMTSOSelectCheckTemp();
            if($tResult >= 1 && $nStaprcDoc == 0){
                echo 'Found';
            }else{
                echo 'False';
            }
        }else if($ptType == 'CloseWhenconfirm'){
            $this->mturnoffsuggestorder->FSxMTSOSelectUpdateAndDeleteTemp();
        }
    }

    //Event Show List Search ค้นหา + pageignation
    public function FSxCTSOSearchlist(){
        $tStatus            = '';
        $tType              = $this->input->post('tType');
        $nStaprcDoc         = $this->input->post('nStaprcDoc');
        $ptTypeCheckModal   = $this->input->post('ptTypeCheckModal');
        if($tType == 'Confirm'){
            $this->mturnoffsuggestorder->FSxMTSOSelectUpdateAndDeleteTemp();
            $tStatus = 'pass';
        }else if($tType == 'Main'){
            if($ptTypeCheckModal == 'false'){
                $tStatus = 'pass';
            }else{
                $tResult        = $this->mturnoffsuggestorder->FSxMTSOSelectCheckTemp();
                if($tResult >= 1 && $nStaprcDoc == 0){
                    echo 'Found';
                }else{
                    $tStatus = 'pass';
                }
            }
        }

        if($tStatus == 'pass'){
            $aData  = array(
                'nPage'         => $this->input->post('nPageCurrent'),
                'nRow'          => 5,
                'tSearchAll'    => ''
            );
            $tResult        = $this->mturnoffsuggestorder->FSxMTSOSelectPDTHD($aData);
            $aResultData    = array(
                'ptNameroute' => $this->input->post('ptNameroute'),
                'aDataList'   => $tResult,
                'nPage'       => $this->input->post('nPageCurrent'),
                'tSearchAll'  => ''
            );
            echo $this->RequestView('document','turnoffsuggestorder/wturnoffsuggestorderList',$aResultData);
        }
    }

    //Event Select จากปุ่มค้นหา
    public function FSxCTSOSelectPDTHD(){
        $tDocumentID        = $this->input->post('tDocumentID');
        $nPage              = $this->input->post('nPageCurrent');
        $tSearchAll         = $this->input->post('tSearchAll');
        $tSortBycolumn      = $_POST['tSortBycolumn'];

        $aDataMove  = array(
            'tDocumentID'   => $tDocumentID
        );
        $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
        $this->mturnoffsuggestorder->FSxMTSOMoveMastertoTemp($aDataMove);

        $nRowTable = 10;
        $aData  = array(
            'nPage'         => $nPage,
            'nRow'          => $nRowTable,
            'tSearchAll'    => trim($tSearchAll),
            'tSortBycolumn' => $tSortBycolumn
        );
        $aResList = $this->mturnoffsuggestorder->FSxMTSOSelectPDT($aData);

        //Check Document last 
        $tDocumentCheckID = $this->mturnoffsuggestorder->FSxMTSOSelectNoneApprove();
        if($tDocumentCheckID != 'false'){
           $tDocumentLast = 'true';
           $tDocNoNew      =  $tDocumentCheckID;
        }else{
            $tDocumentLast = 'false';
            $tDocNoNew      =  '';
        }

        $aGenTable  = array(
            'aDataList'                 => $aResList,
            'nPage'                     => $nPage,
            'tSearchAll'                => $tSearchAll,
            'tDocumentID'               => $tDocumentID,
            'aHD'                       => $this->mturnoffsuggestorder->FSxMTSOSelectHD($tDocumentID),
            'tFoundLastDatanoneapprove' => $tDocumentLast,
            'nRowTable'                 => $nRowTable,
            'tDocNoNew'                 => $tDocNoNew
        );
        echo $this->RequestView('document','turnoffsuggestorder/wturnoffsuggestorderDataTable',$aGenTable);
    }

    //Update Approve case rabbit fail
    public function FSxCTSOUpdateApproveRabbitFail(){
        $ptDocno = $this->input->post('ptDocno');
        $tResult  = $this->mturnoffsuggestorder->FSxMTSOUpdateApproveRabbitFail($ptDocno);
    }

    //Check Data Duplicate
    public function FSxCTSOCheckDateDuplicate(){
        /*$tCheckDataDuplicate    = $this->mturnoffsuggestorder->FSxMTSOCheckDataDuplicate();
        if($tCheckDataDuplicate != false){
            $aDataHTML = array('Duplicate',$tCheckDataDuplicate);
            echo json_encode($aDataHTML);
        }else{
            $aDataHTML = array('pass');
            echo json_encode($aDataHTML);
        }*/
    }

    //กดตกลง ต้องบันทึก ก่อนสร้างเอกสารใหม่
    public function FSxCTSOSaveForSearch(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $tCheckDate     = $this->mturnoffsuggestorder->FSxMTSOCheckdateDuplicate();
        $aDataCheck     = array();
        $tStatusDup     = '';
        if(!empty($tCheckDate)){
            foreach($tCheckDate as $nKey=>$tValue){
                $tTextPush   = $tValue['FTPdtCode'] . '/' . $tValue['FDPdtStartdate'] . '/' . $tValue['FDPdtEnddate'];
                $tTextSearch = array_search($tTextPush,$aDataCheck);
                
                if($tTextSearch===false) {
                    array_push($aDataCheck,$tTextPush);
                }else{
                    $tStatusDup = 'Duplicate';
                }
            }
        }
        
        if($tStatusDup == 'Duplicate'){
            echo 'Duplicate';
        }else{
            $tDocno = $this->mturnoffsuggestorder->FSxMTSOSavePDT($tDocumentID);
            echo $tDocno;
        }

    }

    //กดอนุมัติ เเต่ต้องบันทึกก่อน
    public function FSxCTSOSaveBeforeApprove(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $tDocno         = $this->mturnoffsuggestorder->FSxMTSOSaveBeforeApprove($tDocumentID);
    }
}

?>