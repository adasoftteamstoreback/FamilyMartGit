<?php

class comnPurReqCNNew extends Controller{
    
    public function __construct(){
        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('document','purreqcn/mpurreqcn');
        $this->input = new Input();
        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }
    }

    //เขียนไฟล์ : หน้าจอใบขอลดหนี้
    public function FSxWriteLogByPage($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'PRQ_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

    public function index($tParameter,$tCallType){
        //set database
        //$this->Configdatabase($tParameter,$tCallType);

        $tAllparameter = $this->PackDatatoarray($tParameter,$tCallType);
        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        $aArrayHead = array(
            'tParameter'        => $tAllparameter,
            'tUseraccount'      => $this->mgeneral->FSaMCOMGetDetailProfile($tAllparameter[0]['Username']),
            'tPathLogoimage'    => $this->mgeneral->FSaMCOMGetPathLogoImage()
        );
        echo $this->RequestView('common','mainpage/wHeader', $aArrayHead);

        $aArrayContent = array(
            'tModulename'   => 'omnPurReqCNNew',
            'tParameter'    => $this->PackData($tParameter,$tCallType),
            'tCallType'     => $tCallType,
            'tCNParameter'  => $tParameter,
            'tCNCallType'   => $tCallType
        );
       
        echo $this->RequestView('common','mainpage/wContent',$aArrayContent);
        
        $aArrayFooter = array(
            'tModules'  => 'document',
            'tFeatures' => 'purreqcn'
        );
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);

        //Setting RabbitMQ
        $this->mgeneral->ConfigRabbitMQ();

        //Setting Permission
        $this->mgeneral->SettingPermission($aArrayContent['tModulename']);

        $this->FSxWriteLogByPage('====================================== START ======================================');
    }

    //content step 1 เลือกก่อน ลดหนี้ตามรอบ หรือ ตามสาขา
    public function FSxCPURContentMain(){
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
        echo $this->RequestView('document','purreqcn/wpurreqcnRoundBranch',$aArrayContent);
    }

    //Get ประเภทผู้จำหน่าย
    public function FSxCPURGettypesupplier(){
        $aResultTypeSup = $this->mpurreqcn->FSaMPURGetTypeSupplier();
        echo json_encode($aResultTypeSup);
    }

    //Get ผู้จำหน่าย
    public function FSxCPURGetsupplier(){
        $pnSupCode      = $this->input->post('pnSupCode');
        $tSearchAll     = $this->input->post('tSearchAll');
        $nPage          = $this->input->post('nPage');

        $aData  = array(
            'nPage'         => $nPage,
            'nRow'          => 10,
            'tSearchAll'    => trim($tSearchAll),
            'pnSupCode'     => $pnSupCode
        );

        $aResultSup = $this->mpurreqcn->FSaMPURGetSupplier($aData);

        $aGenTable  = array(
            'aDataList'     => $aResultSup,
            'nPage'         => $nPage,
            'tSearchAll'    => $tSearchAll,
            'nSupCode'      => $pnSupCode
        );
        echo $this->RequestView('document','purreqcn/wpurreqcnTableSupplier',$aGenTable);     
    }

    //Draw Mainpage หน้าหลัก
    public function FSxCPURContentMainpage(){
        $pnSupCode          = $this->input->post('pnSupCode');
        $pnTypeSupCode      = $this->input->post('pnTypeSupCode');
        $ptRoundBranch      = $this->input->post('ptRoundBranch');
        $tDocno             = '';

        //กรณีมีสินค้าใน DT แต่ไม่มีเอกสารจริงใน HD
        $this->mpurreqcn->FSaMPURCheckPDTinHD();

        //เช็คก่อนว่าเอกสารสมบุรณ์ไหม
        $aCheckDocument     = $this->mpurreqcn->FSaMPURCheckDocument($ptRoundBranch);
        if(empty($aCheckDocument)){
            //ไม่มีเอกสารค้าง
            $tDocumentComplete  = 'complete';
        }else{
            //พบเอกสารค้าง
            $pnSupCode          = $aCheckDocument[0]['FTSplCode'];
            $pnTypeSupCode      = $aCheckDocument[0]['FTStyCode'];
            $ptRoundBranch      = $ptRoundBranch;
            $tDocno             = $aCheckDocument[0]['FTXrhDocNo']; 
            $tDocumentComplete  = 'notcomplete';
        }

        //Get HD มา
        if($tDocno == ''){
            $aPackHD = array();
        }else if($tDocno != ''){
            $aPackHD = $this->mpurreqcn->FSaMPURGetHD($tDocno);
        }

        $aDetailSup         = $this->mpurreqcn->FSaMPURGetDetailSupplier($pnSupCode,$pnTypeSupCode);
        $aGetConfig         = $this->mpurreqcn->FSaMPURGetConfigReason(); //Create By Napat(Jame) 12/03/63

        $aArrayContent = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'tDocno'            => $tDocno,
            'aDetailSup'        => $aDetailSup,
            'aPackHD'           => $aPackHD,
            'tDocumentComplete' => $tDocumentComplete,
            'aGetConfig'        => $aGetConfig
        );

        if($ptRoundBranch == 'PUR1'){
            $this->FSxWriteLogByPage('[FSxCPURContentMainpage] โหลดหน้าจอ ขอคืนสินค้าตามรอบ');
        }else{
            $this->FSxWriteLogByPage('[FSxCPURContentMainpage] โหลดหน้าจอ ขอคืนสินค้าตามสาขา รหัสประเภทผู้จำหน่าย : ' . $pnTypeSupCode . ' รหัสผู้จำหน่าย : ' . $pnSupCode);
        }

        if($tDocumentComplete  == 'notcomplete'){
            $this->FSxWriteLogByPage('[FSxCPURContentMainpage] เจอเอกสารที่ยังไม่สมบูรณ์ดึงออกมาใช้ : ' . $tDocno);
        }

        return $this->RequestView('document','purreqcn/wpurreqcn',$aArrayContent);     
    }

    //Select PDT เลือกสินค้า
    public function FSxCPURSelectPDT(){
        $pnSupCode          = $this->input->post('pnSupCode');
        $pnTypeSupCode      = $this->input->post('pnTypeSupCode');
        $ptRoundBranch      = $this->input->post('ptRoundBranch');
        $tDocumentID        = $this->input->post('ptDocumentID');
        $nPage              = $this->input->post('nPageCurrent');

        $aData  = array(
            'nPage'             => $nPage,
            'nRow'              => 10,
            'tDocumentID'       => $tDocumentID
        );
        $aDetailPDT             = $this->mpurreqcn->FSaMPURGetProduct($aData);
        $aGenTable  = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'aDataList'         => $aDetailPDT,
            'nPage'             => $nPage,
        );
        echo $this->RequestView('document','purreqcn/wpurreqcnTableProduct',$aGenTable);
    }

    //Insert PDT[DT] Case PDT
    public function FSxCPURInsertPDT(){
        $tParameter     = $_POST['tParamter'];
        $tDocumentID    = $this->input->post('tDocumentID');
        $nSeq           = $this->input->post('nSeq');
        $tTypeVat       = $this->input->post('tTypeVat');
        $nValueVat      = $this->input->post('nValueVat');
        $tSPLCode       = $this->input->post('tSPLCode');
        $tTypeSPL       = $this->input->post('tTypeSPL');
        $tDocumentDate  = $this->input->post('dDocDate');
        $dDocDate       = date_create(str_replace("/","-",$tDocumentDate));

        if($tTypeVat == 'VE'){
            $tTypeVat = 1; //รวมใน
        }else if($tTypeVat == 'VI'){
            $tTypeVat = 2;  //แยกนอก
        }else{
            $tTypeVat = 1;
        }

        $aPackDataInsert = array(
            'tTypeInsertDT'     => 'PDT',
            'tSPLCode'          => $tSPLCode,
            'tSelectTypeVat'    => $tTypeVat,
            'nSelectValueVat'   => $nValueVat,
            'tTypeRoundBranch'  => $tTypeSPL,
            'dDocDate'          => date_format($dDocDate,"Y-m-d")
        );

        if($nSeq == '' || $nSeq == 'null' ){
            //INSERT
            if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
                $aFormatCode    = explode("PE",$tFormatCode);
                $tFormatCode    = 'PE0' . $aFormatCode[1];
            }else{
                $tFormatCode = $tDocumentID;
            }
            $tResult   = $this->mpurreqcn->FSxMPURInsertPDT($tParameter,$tDocumentID,$aPackDataInsert,'');
        }else if($nSeq != ''){
            //EDIT
            $tResult        = $this->mpurreqcn->FSxMPURInsertPDT($tParameter,$tDocumentID,$aPackDataInsert,$nSeq);
            $tFormatCode    = $tDocumentID;
        }   
        
        $aPackData = array(
            'tResult'         => $tResult,
            'tFormatCode'     => $tFormatCode
        );
        echo json_encode($aPackData);

        $this->FSxWriteLogByPage('[FSxCPURInsertPDT] เพิ่มข้อมูลสินค้าแบบเลือก : ' .$tParameter[0]['FTPdtName'] . '(' .$tParameter[0]['FTPdtCode']. ')');
    }

    //Insert PDT[DT] Case barcode
    public function FSxCPURInsertPDTBarcode(){
        $tPDTCodeorBarcode      = $this->input->post('tPDTCodeorBarcode');
        $tDocumentID            = $this->input->post('tDocumentID');
        $nSPLCode               = $this->input->post('nSPLCode');
        $nVat                   = $this->input->post('nVat');
        $tStyCode               = $this->input->post('tStyCode');

        if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
            $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
            $aFormatCode    = explode("PE",$tFormatCode);
            $tFormatCode    = 'PE0' . $aFormatCode[1];
        }else{
            $tFormatCode = $tDocumentID;
        }

        //Insert
        $tResult   = $this->mpurreqcn->FSxMPURInsertPDTCaseBarcode($tPDTCodeorBarcode,$tDocumentID,$nSPLCode,$nVat,null,$tStyCode);
        
        $aPackData = array(
            'tResult'         => $tResult,
            'tFormatCode'     => $tFormatCode
        );

        if($aPackData['tResult'] != '' || false){
            $this->FSxWriteLogByPage('[FSxCPURInsertPDTBarcode] เพิ่มข้อมูลสินค้าแบบคีย์ รหัสสินค้า : ' .$tPDTCodeorBarcode);
        }

        echo json_encode($aPackData);
    }

    //Save
    public function FSxCPURSave(){
        $tDocumentNumber    = $this->input->post('tDocumentNumber');
        $tReason            = $this->input->post('tReason');
        $tNumPO             = $this->input->post('tNumPO');
        $tNumberSend        = $this->input->post('tNumberSend');
        $tDateSend          = $this->FStCPURExplodeDate($this->input->post('tDateSend'));
        $tDocNumber	        = $this->input->post('tDocNumber');
		$tDocDate	        = $this->FStCPURExplodeDate($this->input->post('tDocDate'));
        $tDocTime	        = $this->input->post('tDocTime');
        $tDocDateReturn     = $this->FStCPURExplodeDate($this->input->post('tDocDateReturn'));
        $tTextCalculate     = $this->input->post('tTextCalculate');
        $nCalResult         = $this->input->post('nCalResult');
        $nCalDiscount       = $this->input->post('nCalDiscount');
        $tTextCalDiscount   = $this->input->post('tTextCalDiscount');
        $nCalBeforeDiscount = $this->input->post('nCalBeforeDiscount');
        $nCalVat            = $this->input->post('nCalVat');
        $nCalNet            = $this->input->post('nCalNet');
        $tStyCode           = $this->input->post('tStyCode');
        $tSplCode           = $this->input->post('tSplCode');
        $tVatCode           = $this->input->post('tVatCode');
        $nVatValue          = $this->input->post('nVatValue');
        $tReasonTextArea    = $this->input->post('tReasonTextArea');
        $tTypeRoundBranch   = $this->input->post('tTypeRoundBranch');
        $tDocumentDate      = $this->input->post('dDocDate');
        $dDocDate           = date_create(str_replace("/","-",$tDocumentDate));
        $aPackData  = array(
            'tSplCode'              => $tSplCode,
            'tStyCode'              => $tStyCode,
            'tDocumentNumber'       => $tDocumentNumber,
            'tReason'               => $tReason,
            'tNumPO'                => $tNumPO,
            'tNumberSend'           => $tNumberSend,
            'tDateSend'             => $tDateSend,
            'tDocNumber'	        => $tDocNumber,
            'tDocDate'	            => $tDocDate,
            'tDocTime'	            => $tDocTime,
            'tDocDateReturn'        => $tDocDateReturn,
            'tTextCalculate'        => $tTextCalculate,
            'nCalResult'            => $nCalResult,
            'nCalDiscount'          => $nCalDiscount,
            'tTextCalDiscount'      => $tTextCalDiscount,
            'nCalBeforeDiscount'    => $nCalBeforeDiscount,
            'nCalVat'               => $nCalVat,
            'nCalNet'               => $nCalNet,
            'tVatCode'              => $tVatCode,
            'nVatValue'             => $nVatValue,
            'tReasonTextArea'       => $tReasonTextArea,
            'tTypeRoundBranch'      => $tTypeRoundBranch,
            'dDocDate'              => date_format($dDocDate,"Y-m-d")
        );
        $aInsertHD  = $this->mpurreqcn->FSaMPURInsertHD($aPackData);
        echo $aInsertHD; 

        $this->FSxWriteLogByPage('[FSxCPURSave] บันทึกข้อมูล : ' .$tDocumentNumber);

    }

    //กดยกเลิกเอกสาร
    public function FSxCPURCancelDocument(){
        $tDocumentNumber    = $this->input->post('tDocumentNumber');
        $tCancel            = $this->mpurreqcn->FSaMPURCancelDocument($tDocumentNumber);
        $this->FSxWriteLogByPage('[FSxCPURCancelDocument] ยกเลิกเอกสาร : ' .$tDocumentNumber);
        return $tCancel;
    }

    //Delete
    public function FSxCPURDelete(){
        $ptDocumentNo   = $this->input->post('ptDocumentNo');
        $pnSeq          = $this->input->post('pnSeq');
        $pnProductcode  = $this->input->post('pnProductcode');
        $pnBchCode      = $this->input->post('pnBchCode');

        $aDataDeleteWHERE   = array(
            'FTBchCode'     => $pnBchCode ,
            'FTXrhDocNo'    => $ptDocumentNo ,
            'FNXrdSeqNo'    => $pnSeq ,
            'FTPdtCode'     => $pnProductcode
        );

        $tResult = $this->mpurreqcn->FSxMPURDeletePDT($aDataDeleteWHERE);

        $this->FSxWriteLogByPage('[FSxCPURDelete] ลบ สินค้ารหัส : ' .$pnProductcode);
        echo $tResult;
    }

    //Update Edit inline 
    public function FSxCPUREditinline(){
        $nSeq       = $this->input->post('nSeq');
        $tDoc       = $this->input->post('tDoc');
        $nValue     = $this->input->post('nValue');
        $nB4DisChg  = $this->input->post('nB4DisChg');
        $nVatRate   = $this->input->post('nVatRate');
        $tTypeEdit  = $this->input->post('tTypeEdit');
        $tPdt       = $this->input->post('tPdt');

        if($tTypeEdit == 'QTY'){
            $aPackDate  = array(
                'FNXrdSeqNo'    => $nSeq,
                'FTXrhDocNo'    => $tDoc,
                'FCXrdQty'      => $nValue,
                'FCXrdB4DisChg' => $nB4DisChg,
                'nVatRate'      => $nVatRate,
                'FTPdtCode'     => $tPdt
            );
            
            if(!$this->mpurreqcn->FSaMPURChkPdtQtyRet($aPackDate)){
                $aDetailUpdate = $this->mpurreqcn->FSaMPURUpdate($aPackDate,1);
                $this->FSxWriteLogByPage('[FSxCPUREditinline] แก้ไขจำนวน : ' .$nValue);
                echo $aDetailUpdate;
            }else{
                $nValueQty = $this->mpurreqcn->FSaMPURChkPdtQtyRet($aPackDate);
                if(floatval($nValueQty[0]['FCPdtQtyRet']) <= 0){
                    echo 'LESSQTY';
                    $this->mpurreqcn->FSaMPURUpdate($aPackDate,2);
                    $this->FSxWriteLogByPage('[FSxCPUREditinline] แก้ไขจำนวน <= 0');
                }else{
                    echo 'PdtQtyRet';
                    $this->FSxWriteLogByPage('[FSxCPUREditinline] แก้ไขจำนวน > 0');
                }
            }
        }else if($tTypeEdit == 'PDT'){
            $aPackDate  = array(
                'tSPL'          => $this->input->post('tSPL'),
                'NewPDT'        => $this->input->post('tNewPDTCode'),
                'FNXrdSeqNo'    => $nSeq,
                'FTXrhDocNo'    => $tDoc,
                'FCXrdQty'      => $nValue,
                'FCXrdB4DisChg' => $nB4DisChg,
                'nVatRate'      => $nVatRate,
                'tStyCode'      => $this->input->post('tStyCode')
            );

            $aDetailUpdate = $this->mpurreqcn->FSaMPURUpdatePDT($aPackDate);
            $this->FSxWriteLogByPage('[FSxCPUREditinline] แก้ไขรหัสสินค้า ' . $this->input->post('tNewPDTCode'));
            echo $aDetailUpdate;
        }
    }

    //Calculate
    public function FSxCPURCalculate(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $aDetail        = $this->mpurreqcn->FSaMPURCalculate($tDocumentID);
        $aPackData      = array(
            'nTotal' => $aDetail[0]['nTotal']
        );
        $aPackData = json_encode($aPackData);
        return $aPackData;
    }

    //Explode Date วันที่ ใช้สำหรับ insert ใน database
    public function FStCPURExplodeDate($pdDate){
        $tDate = $pdDate;
        $aDate = explode("/",$tDate);
        $aDate =  $aDate[2].'-'.$aDate[1].'-'.$aDate[0];
        return $aDate;
    }

    //Approve อนุมัติ
    public function FSxCPURApprove(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $tType          = $this->input->post('tType');
        $tRefDocument   = $this->input->post('tRefDocument');

        // ตรวจสอบว่าสินค้าทุกตัวมี SplCode จริงหรือไม่ ? Comsheet 2020-243
        // Create By Napat(Jame) 01/06/2020
        $aChkSplCodeInDT = $this->mpurreqcn->FSaMPURChkSplCodeInDT($tDocumentID);
        if($aChkSplCodeInDT['nStaReturn'] == 99){
            //Approve ให้เรียบร้อยก่อน เเล้วค่อย ย้ายอีกชุด
            $aUpdate = array(
                'tDocumentID'     => $tDocumentID,
                'tType'           => $tType,
                'tRefDocument'    => $tRefDocument
            );
            $aUpdateApprove = $this->mpurreqcn->FSaMPURApprove($aUpdate);

            $aCheckByVatCode = $this->mpurreqcn->FSaMPURCheckPDTByVatCode($tDocumentID);
            if(empty($aCheckByVatCode)){
                //คือมี VATCODE ตัวเดียว
                //คำนวณ prorate
                FCNaHCalculateProrate('TACTPrDT',$tDocumentID);
                $tReturnDocument = $tDocumentID;
                $this->FSxWriteLogByPage('[FSxCPURApprove] คำนวณ prorate ');
            }else{
                //คือมี VATCODE มากกว่าหนึ่ง
                $tReturnDocument = $tDocumentID;
                for($j=1; $j<count($aCheckByVatCode); $j++){
                    $tReturnDoc = $this->mpurreqcn->FSaMPURSelectintoHDDT($tDocumentID,$aCheckByVatCode[$j]);
                    $tReturnDocument .= ','. $tReturnDoc;
                }
                $this->mpurreqcn->FSaMPURDeleteHDDT($tDocumentID,$aCheckByVatCode[0]);
            }

            // Comsheet/2022-036
            // Napat(Jame) 07/09/2022 อัพเดทข้อมูล Spl หลังจาก Split เสร็จ
            $this->mpurreqcn->FSaMPURUpdateHDSpl($tReturnDocument);

            $aDataReturn = array(
                'nStaReturn'            => 1,
                'tMsgReturn'            => 'Approve Success',
                'tReturnDocument'       => $tReturnDocument,
                'aPdtNotHaveSplCode'    => array()
            );
            $this->FSxWriteLogByPage('[FSxCPURApprove] อนุมัติสำเร็จ ');
        }else{
            $aDataReturn = array(
                'nStaReturn'            => 99,
                'tMsgReturn'            => 'Found Data Product Not Have FTSplCode',
                'tReturnDocument'       => '',
                'aPdtNotHaveSplCode'    => $aChkSplCodeInDT['aDataReturn']
            );

            $this->FSxWriteLogByPage('[FSxCPURApprove] เจอสินค้าที่ไม่ได้ผูกกับผู้จำหน่าย ');
        }
        echo json_encode($aDataReturn);
    }

    //เลือกเอกสาร หรือ หลังจาก approve
    public function FSxCPURSelectAfter(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $aPackHD        = $this->mpurreqcn->FSaMPURGetHD($tDocumentID);
        $pnSupCode      = $aPackHD[0]['FTSplCode'];
        $pnTypeSupCode  = $aPackHD[0]['FTStyCode'];
        $ptFTXrhDocType = $aPackHD[0]['FTXrhDocType'];
        if($ptFTXrhDocType == 5){
            $ptRoundBranch = 'PUR1';
        }else{
            $ptRoundBranch = 'PUR2';
        }
        $aDetailSup     = $this->mpurreqcn->FSaMPURGetDetailSupplier($pnSupCode,$pnTypeSupCode);
        $aGetConfig     = $this->mpurreqcn->FSaMPURGetConfigReason(); //Create By Napat(Jame) 25/03/63 เพิ่มใหม่

        $aArrayContent  = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'tDocno'            => $tDocumentID,
            'aDetailSup'        => $aDetailSup,
            'aPackHD'           => $aPackHD, 
            'aGetConfig'        => $aGetConfig,
            'tDocumentComplete' => 'complete'
        );
        return $this->RequestView('document','purreqcn/wpurreqcn',$aArrayContent);    
    }

    //กดค้นหาเอกสาร 
    public function FSxCPURListDocument(){
        $nPage                  = $this->input->post('nPageCurrent');
        $tTextSearchPURReq      = $this->input->post('tTextSearchPURReq'); 

        $nRowTable = 5;
        $aData  = array(
            'nPage'                 => $nPage,
            'nRow'                  => $nRowTable,
            'tTextSearchPURReq'     => $tTextSearchPURReq
        );
        $aResList = $this->mpurreqcn->FSxMPURListSearchSelectHD($aData);

        $aGenTable  = array(
            'aDataList'                 => $aResList,
            'nPage'                     => $nPage,
            'ptNameroute'               => $this->input->post('ptRoute')
        );
        echo $this->RequestView('document','purreqcn/wpurreqcnSearchList',$aGenTable);
    }

    //----------------------------------- ตามรอบ -----------------------------------//
    //Get ใบรับของ/ใบซื้อ
    public function FSxCPURGetDocument(){
        $aDetailPu = $this->mpurreqcn->FSaMPURGetDetailPu();
        echo json_encode($aDetailPu);
    }

    //Get สินค้าใน ใบรับของ/ใบซื้อ
    public function FSxCPURGetPDTByDocument(){
        $ptDocumentNumber   = $this->input->post('ptDocumentNumber');
        $aDetailPDTPu       = $this->mpurreqcn->FSaMPURGetDetailPDTByDocument($ptDocumentNumber);
        echo json_encode($aDetailPDTPu);
    }

    //Insert สินค้า
    public function FSxCPURInsertPDTByPUR1(){
        $ptPackData        = $this->input->post('tPackData');
        $tDocumentID       = $this->input->post('tDocumentID');
        $tDocumentPN       = $this->input->post('tDocumentPN');
        $tTypeDelTemp      = $this->input->post('tTypeDelTemp');
        $ptPackData        = substr($ptPackData, 0, -1);
        $aPDT              = explode(",",$ptPackData);
        $aDataNotReturn    = array();
        $nSeq              = 0;

        // Create By : Napat(Jame) 28/04/2020
        // Comsheet 2020-210
        if($tTypeDelTemp == '1'){
            $this->mpurreqcn->FSxMPURDeleteAllPDT($tDocumentID);
        }else if($tTypeDelTemp == '2'){
            $nSeq = $this->mpurreqcn->FSnMPURGetLastSeqDT($tDocumentID);
        }

        for($i=0; $i<count($aPDT); $i++){
            $aResultInsert = $this->mpurreqcn->FSaMPURInsertPDTByPUR1($tDocumentPN,$tDocumentID,$aPDT[$i],$nSeq);

            // Create By Jame 27/04/2020
            // ComSheet 2020-213
            // ถ้าเจอสินค้าที่ไม่อนุญาติให้คืน เก็บใส่ Array เพื่อนำไปแสดง
            if($aResultInsert[6] == '2'){
                $aPdtNotReturn = array(
                    'FTPdtBarCode'  => $aResultInsert[4],
                    'FTPdtName'     => $aResultInsert[5]
                );
                array_push($aDataNotReturn,$aPdtNotReturn);
            }else{
                $nSeq++;
            }
        }

        $aHDPn         = $this->mpurreqcn->FSaMPURGetDetailPDTByDocument($tDocumentPN);
        $aDetailSup    = $this->mpurreqcn->FSaMPURGetDetailSupplier($aResultInsert[1],$aResultInsert[2]);
        $aArrayContent = array(
            'tDocDate'          => $aResultInsert[3],
            'tDocno'            => $aResultInsert[0],
            'aDetailSup'        => $aDetailSup,
            'aHDPn'             => $aHDPn,
            'aDataNotReturn'    => $aDataNotReturn
        );

        $this->FSxWriteLogByPage('[FSxCPURInsertPDTByPUR1] เลือกเอกสารตามรอบ อ้างอิงจากเอกสารที่ส่ง : ' . $tDocumentID);

        return json_encode($aArrayContent);
    }

    //---------------------------------- Rabbit fail -------------------------------//
    public function FSxCPURCaseProcessFail(){
        $ptDocno    = $this->input->post('ptDocno');
        $aUpdataHD  = $this->mpurreqcn->FSaMPURRabbitFailUpdateFlag($ptDocno);
        $this->FSxWriteLogByPage('[FSxCPURCaseProcessFail] อนุมัติไม่สำเร็จ : ' . $ptDocno);
    }

}

?>