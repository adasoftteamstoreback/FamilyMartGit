<?php

class comnPurCNNew extends Controller{
    
    public function __construct(){
        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('document','purcn/mpurcn');
        $this->input = new Input();
        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }
    }

    public function index($tParameter,$tCallType){
        //set database
        $this->Configdatabase($tParameter,$tCallType);

        $tAllparameter = $this->PackDatatoarray($tParameter,$tCallType);
        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        $aArrayHead = array(
            'tParameter'        => $tAllparameter,
            'tUseraccount'      => $this->mgeneral->FSaMCOMGetDetailProfile($tAllparameter[0]['Username']),
            'tPathLogoimage'    => $this->mgeneral->FSaMCOMGetPathLogoImage()
        );
        echo $this->RequestView('common','mainpage/wHeader', $aArrayHead);

        $aArrayContent = array(
            'tModulename'   => 'omnPurCNNew',
            'tParameter'    => $this->PackData($tParameter,$tCallType),
            'tCallType'     => $tCallType,
            'tCNParameter'  => $tParameter,
            'tCNCallType'   => $tCallType
        );
       
        echo $this->RequestView('common','mainpage/wContent',$aArrayContent);
        $aArrayFooter = array(
            'tModules'  => 'document',
            'tFeatures' => 'purcn'
        );
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);

        //Setting RabbitMQ
        $this->mgeneral->ConfigRabbitMQ();

        //Setting Permission
        $this->mgeneral->SettingPermission($aArrayContent['tModulename']);
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
            'tModulename'   => 'omnPurCNNew',
            'tParameter'    => $aDataresult,
            'tCallType'     => $tCallType
        );
        echo $this->RequestView('document','purcn/wpurcnRoundBranch',$aArrayContent);
    }

    //Get ประเภทผู้จำหน่าย
    public function FSxCPURGettypesupplier(){
        $aResultTypeSup = $this->mpurcn->FSaMPURGetTypeSupplier();
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

        $aResultSup = $this->mpurcn->FSaMPURGetSupplier($aData);

        $aGenTable  = array(
            'aDataList'     => $aResultSup,
            'nPage'         => $nPage,
            'tSearchAll'    => $tSearchAll,
            'nSupCode'      => $pnSupCode
        );
        echo $this->RequestView('document','purcn/wpurcnTableSupplier',$aGenTable);     
    }

    //Draw Mainpage หน้าหลัก
    public function FSxCPURContentMainpage(){
        $this->FSxCPCNWriteLog('=============== START ===============');
        $pnSupCode          = $this->input->post('pnSupCode');
        $pnTypeSupCode      = $this->input->post('pnTypeSupCode');
        $ptRoundBranch      = $this->input->post('ptRoundBranch');
        $tDocno             = '';

        //กรณีมีสินค้าใน DT แต่ไม่มีเอกสารจริงใน HD
        $this->mpurcn->FSaMPURCheckPDTinHD();

        //เช็คก่อนว่าเอกสารสมบุรณ์ไหม
        $aCheckDocument     = $this->mpurcn->FSaMPURCheckDocument($ptRoundBranch);
        if(empty($aCheckDocument)){
            //ไม่มีเอกสารค้าง
            $tDocumentComplete  = 'complete';
            $bStaDocRef         = false;

            $this->FSxCPCNWriteLog('[FSxCPURContentMainpage] ผู้จำหน่าย '.$pnSupCode);
            $this->FSxCPCNWriteLog('[FSxCPURContentMainpage] ประเภทผู้จำหน่าย '.$pnTypeSupCode);
        }else{
            //พบเอกสารค้าง
            $pnSupCode          = $aCheckDocument[0]['FTSplCode'];
            $pnTypeSupCode      = $aCheckDocument[0]['FTStyCode'];
            $ptRoundBranch      = $ptRoundBranch;
            $tDocno             = $aCheckDocument[0]['FTXthDocNo']; 
            $tDocumentComplete  = 'notcomplete';

            $this->FSxCPCNWriteLog('[FSxCPURContentMainpage] พบเอกสาร '.$tDocno.' ไม่สมบูรณ์');
            $this->FSxCPCNWriteLog('[FSxCPURContentMainpage] ผู้จำหน่าย '.$pnSupCode);
            $this->FSxCPCNWriteLog('[FSxCPURContentMainpage] ประเภทผู้จำหน่าย '.$pnTypeSupCode);

            //เอกสาร HD นี้ ถูก ref มา
            if($aCheckDocument[0]['FTXthRefExt'] != '' || $aCheckDocument[0]['FTXthRefExt'] != null){
                //เอกสาร HD ชุดนี้ ref มา
                $bStaDocRef  = true;
            }else{
                $bStaDocRef = false;
            }
        }

        //Get HD มา
        if($tDocno == ''){
            $aPackHD = array();
        }else if($tDocno != ''){
            $aPackHD = $this->mpurcn->FSaMPURGetHD($tDocno);
        }

        $aDetailSup         = $this->mpurcn->FSaMPURGetDetailSupplier($pnSupCode,$pnTypeSupCode);
        $aGetConfig         = $this->mpurcn->FSaMPURGetConfigReason(); //Create By Napat(Jame) 12/03/63

        $aArrayContent = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'tDocno'            => $tDocno,
            'aDetailSup'        => $aDetailSup,
            'aPackHD'           => $aPackHD,
            'tDocumentComplete' => $tDocumentComplete,
            'bStaDocRef'        => $bStaDocRef,
            'aGetConfig'        => $aGetConfig
        );

        return $this->RequestView('document','purcn/wpurcn',$aArrayContent);     
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
        $aDetailPDT             = $this->mpurcn->FSaMPURGetProduct($aData);
        $aGenTable  = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'aDataList'         => $aDetailPDT,
            'nPage'             => $nPage,
        );
        echo $this->RequestView('document','purcn/wpurcnTableProduct',$aGenTable);
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
                $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
                $aFormatCode    = explode("PC",$tFormatCode);
                $tFormatCode    = 'PC0' . $aFormatCode[1];
            }else{
                $tFormatCode = $tDocumentID;
            }
            $tResult   = $this->mpurcn->FSxMPURInsertPDT($tParameter,$tDocumentID,$aPackDataInsert,'');
        }else if($nSeq != ''){
            //EDIT
            $tResult        = $this->mpurcn->FSxMPURInsertPDT($tParameter,$tDocumentID,$aPackDataInsert,$nSeq);
            $tFormatCode    = $tDocumentID;
        }   
        
        $aPackData = array(
            'tResult'         => $tResult,
            'tFormatCode'     => $tFormatCode
        );
        echo json_encode($aPackData);
    }

    //Insert PDT[DT] Case barcode
    public function FSxCPURInsertPDTBarcode(){
        $tPDTCodeorBarcode      = $this->input->post('tPDTCodeorBarcode');
        $tDocumentID            = $this->input->post('tDocumentID');
        $nSPLCode               = $this->input->post('nSPLCode');
        $nVat                   = $this->input->post('nVat');
        $tStyCode               = $this->input->post('tStyCode');

        if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
            $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
            $aFormatCode    = explode("PC",$tFormatCode);
            $tFormatCode    = 'PC0' . $aFormatCode[1];
        }else{
            $tFormatCode = $tDocumentID;
        }

        //Insert
        $tResult   = $this->mpurcn->FSxMPURInsertPDTCaseBarcode($tPDTCodeorBarcode,$tDocumentID,$nSPLCode,$nVat,$tStyCode);
        
        $aPackData = array(
            'tResult'         => $tResult,
            'tFormatCode'     => $tFormatCode
        );

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
        $aInsertHD  = $this->mpurcn->FSaMPURInsertHD($aPackData);
        $this->FSxCPCNWriteLog('[FSxCPURSave] บันทึกเอกสาร ปรับสถานะ FTXthStaDoc = 1');
        echo $aInsertHD;

        //คำนวณ prorate
        FCNaHCalculateProrate('TACTPrDT',$tDocumentNumber);
    }

    //กดยกเลิกเอกสาร
    public function FSxCPURCancelDocument(){
        $tDocumentNumber    = $this->input->post('tDocumentNumber');
        $tCancel            = $this->mpurcn->FSaMPURCancelDocument($tDocumentNumber);
        $this->FSxCPCNWriteLog('[FSxCPURCancelDocument] ยกเลิกเอกสาร '.$tDocumentNumber);
        return $tCancel;
    }

    //Delete
    public function FSxCPURDelete(){
        $ptDocumentNo   = $this->input->post('ptDocumentNo');
        $pnSeq          = $this->input->post('pnSeq');
        $pnProductcode  = $this->input->post('pnProductcode');
        $pnBchCode      = $this->input->post('pnBchCode');

        $aDataDeleteWHERE   = array(
            'FTBchCode'     => $pnBchCode,
            'FTXthDocNo'    => $ptDocumentNo,
            'FNXtdSeqNo'    => $pnSeq,
            'FTPdtCode'     => $pnProductcode
        );
        $this->FSxCPCNWriteLog('[FSxCPURDelete] ลบสินค้า '.$pnProductcode);

        $tResult = $this->mpurcn->FSxMPURDeletePDT($aDataDeleteWHERE);
        echo $tResult;
    }

    //Update Edit inline 
    public function FSxCPUREditinline(){
        $nSeq       = $this->input->post('nSeq');
        $tDoc       = $this->input->post('tDoc');
        $nValue     = $this->input->post('nValue');
        $nB4DisChg  = $this->input->post('nB4DisChg');
        $nVatRate   = $this->input->post('nVatRate');
        $aPackDate  = array(
            'FNXtdSeqNo'    => $nSeq,
            'FTXthDocNo'    => $tDoc,
            'FCXtdQty'      => $nValue,
            'FCXtdB4DisChg' => $nB4DisChg,
            'nVatRate'      => $nVatRate,
            'FTPdtCode'     => $this->input->post('tPdt')
        );

        if(!$this->mpurcn->FSaMPURChkPdtQtyRet($aPackDate)){
            $aDetailUpdate = $this->mpurcn->FSaMPURUpdate($aPackDate,1);
            echo $aDetailUpdate;
        }else{
            $nValueQty = $this->mpurcn->FSaMPURChkPdtQtyRet($aPackDate);
            if(floatval($nValueQty[0]['FCPdtQtyRet']) <= 0){
                echo 'LESSQTY';
                $this->mpurcn->FSaMPURUpdate($aPackDate,2);
            }else{
                echo 'PdtQtyRet';
            }
        }
    }

    //Calculate
    public function FSxCPURCalculate(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $aDetail        = $this->mpurcn->FSaMPURCalculate($tDocumentID);
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

    //เลือกเอกสาร หรือ หลังจาก approve
    public function FSxCPURSelectAfter(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $aPackHD        = $this->mpurcn->FSaMPURGetHD($tDocumentID);
        $pnSupCode      = $aPackHD[0]['FTSplCode'];
        $pnTypeSupCode  = $aPackHD[0]['FTStyCode'];
        $ptFTXthDocType = $aPackHD[0]['FTXthDocType'];

        if($ptFTXthDocType == 5){
            $ptRoundBranch = 'PUR2';
        }else{
            $ptRoundBranch = 'PUR1';
        }

        //เอกสาร HD นี้ ถูก ref มา
        if($aPackHD[0]['FTXthRefExt'] != '' || $aPackHD[0]['FTXthRefExt'] != null){
            //เอกสาร HD ชุดนี้ ref มา
            $bStaDocRef  = true;
        }else{
            $bStaDocRef = false;
        }

        $aDetailSup     = $this->mpurcn->FSaMPURGetDetailSupplier($pnSupCode,$pnTypeSupCode);
        $aGetConfig         = $this->mpurcn->FSaMPURGetConfigReason(); //Create By Napat(Jame) 25/03/63 เพิ่มใหม่
        $aArrayContent  = array(
            'pnSupCode'         => $pnSupCode,
            'pnTypeSupCode'     => $pnTypeSupCode,
            'ptRoundBranch'     => $ptRoundBranch,
            'tDocno'            => $tDocumentID,
            'aDetailSup'        => $aDetailSup,
            'aPackHD'           => $aPackHD,
            'tDocumentComplete' => 'complete',
            'bStaDocRef'        => $bStaDocRef,
            'aGetConfig'        => $aGetConfig
        );
        return $this->RequestView('document','purcn/wpurcn',$aArrayContent);    
    }

    //กดค้นหาเอกสาร 
    public function FSxCPURListDocument(){
        $nPage               = $this->input->post('nPageCurrent');
        $tTextSearchPUR      = $this->input->post('tTextSearchPUR'); 

        $nRowTable = 5;
        $aData  = array(
            'nPage'             => $nPage,
            'nRow'              => $nRowTable,
            'tTextSearchPUR'    => $tTextSearchPUR
        );
        $aResList = $this->mpurcn->FSxMPURListSearchSelectHD($aData);

        $aGenTable  = array(
            'aDataList'                 => $aResList,
            'nPage'                     => $nPage,
            'ptNameroute'               => $this->input->post('ptRoute')
        );
        echo $this->RequestView('document','purcn/wpurcnSearchList',$aGenTable);
    }

    //ตรวจสอบว่าเอกสารนี้มันมีการ แยกตาม vatcode ใหม่
    public function FSaCPURCheckDocSplit(){
        $tDocumentID    = $this->input->post('tDocumentID');
        $aResList       = $this->mpurcn->FSaMPURCheckDocSplit($tDocumentID);
        echo json_encode($aResList);
    }

    //----------------------------------- ตามรอบ -----------------------------------//
    //Get ใบขอคืนสินค้า
    public function FSxCPURGetDocument(){
        $tTypeRoundorBranch = $this->input->post('tTypeRoundorBranch');
        $tTypeSup           = $this->input->post('tTypeSpl');
        $tSearch            = $this->input->post('tSearch');
        $tColumSearch       = $this->input->post('tColumSearch');
        $aDetailPu = $this->mpurcn->FSaMPURGetDetailPu($tTypeRoundorBranch,$tTypeSup,$tSearch,$tColumSearch);
        echo json_encode($aDetailPu);
    }

    //Get สินค้าใน ใบขอคืนสินค้า
    public function FSxCPURGetPDTByDocument(){
        $ptDocumentNumber   = $this->input->post('ptDocumentNumber');
        $aDetailPDTPu       = $this->mpurcn->FSaMPURGetDetailPDTByDocument($ptDocumentNumber);
        echo json_encode($aDetailPDTPu);
    }

    //Insert สินค้า จาก ใบขอคืนสินค้า
    public function FSxCPURInsertPDTByPUR1(){
        $ptPackData        = $this->input->post('tPackData');
        $tDocumentID       = $this->input->post('tDocumentID');
        $tDocumentPr       = $this->input->post('tDocumentPN'); 
        $tDocPNCurrent     = $this->input->post('tDocPNCurrent');
        $tType             = $this->input->post('tType'); 
        $ptPackData        = substr($ptPackData, 0, -1);
        $aPDT              = explode(",",$ptPackData);
        $aDataNotReturn    = array();
        $nSeq              = 0;
        
        if($tType == "ALTER_DATA" || $tType == "CHANGE_SPL"){
            $this->FSxCPCNWriteLog('[FSxCPURInsertPDTByPUR1] Clear Temp TACTPtDT');
            $this->mpurcn->FSaMPURDeletePDTByPUR1($tDocumentID); 
        }

        if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
            $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
            $aFormatCode    = explode("PC",$tFormatCode);
            $tFormatCode    = 'PC0' . $aFormatCode[1];
            $this->FSxCPCNWriteLog('[FSxCPURInsertPDTByPUR1] สร้างเอกสารหมายเลข '.$tFormatCode);
        }else{
            $tFormatCode    = $tDocumentID;
        }

        for($i=0; $i<count($aPDT); $i++){
            $aResultInsert = $this->mpurcn->FSaMPURInsertPDTByPUR1($tDocumentPr,$tFormatCode,$aPDT[$i],$nSeq,$tDocPNCurrent);

            // Create By Jame 30/04/2020
            // ComSheet 2020-219
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

        $this->FSxCPCNWriteLog('[FSxCPURInsertPDTByPUR1] เพิ่มสินค้าจากใบขอคืนสินค้า '.$tDocumentPr.' จำนวน '.$nSeq.' รายการ');

        //เช็คว่าเปลี่ยน ผู้จำหน่าย ไหม ?
        $aHDPr         = $this->mpurcn->FSaMPURGetDetailPDTByDocument($tDocPNCurrent);
        $aDetailSup    = $this->mpurcn->FSaMPURGetDetailSupplier($aResultInsert[1],$aResultInsert[2]);
        if($tType == "CHANGE_SPL"){
            $this->FSxCPCNWriteLog('[FSxCPURInsertPDTByPUR1] เปลี่ยนผู้จำหน่าย '.$aDetailSup[0]['FTSplName'].'('.$aDetailSup[0]['FTSplCode'].')');
        }
 
        $aArrayContent = array(
            'tDocDate'          => $aResultInsert[3],
            'tDocno'            => $aResultInsert[0],
            'aDetailSup'        => $aDetailSup,
            'aHDPr'             => $aHDPr,
            'aDataNotReturn'    => $aDataNotReturn
        );

        return json_encode($aArrayContent);
    }

    //Roll Back จาก background process
    public function FSxCPURCaseProcessFail(){
        $tDocNo = $this->input->post('ptDocno');
        $this->FSxCPCNWriteLog('[FSxCPURCaseProcessFail] อนุมัติเอกสาร '.$tDocNo.' ไม่สำเร็จ');
        //sqlsrv_rollback( $conn );
    }

    // Create By : Napat(Jame) 2020-06-30
    // เขียนไฟล์ Log : หน้าจอใบลดหนี้
    public function FSxCPCNWriteLog($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'PCN_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

}

?>