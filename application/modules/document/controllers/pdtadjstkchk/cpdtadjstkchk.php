<?php

class comnPdtAdjStkChkNew extends Controller {

    public $tBchCode;

    public function __construct(){
        parent::__construct();

        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('document','pdtadjstkchk/mpdtadjstkchk');

        $this->tBchCode = getBranch()['FTBchCode'];
        $this->input    = new Input();

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
            'tModulename'   => 'omnPdtAdjStkChkNew',
            'tParameter'    => $this->PackData($tParameter,$tCallType),
            'tCallType'     => $tCallType,
            'tCNParameter'  => $tParameter,
            'tCNCallType'   => $tCallType
        );

        //Setting RabbitMQ
        $this->mgeneral->ConfigRabbitMQ();

        //Setting Permission
        $this->mgeneral->SettingPermission($aArrayContent['tModulename']);

        // $this->mturnoffsuggestorder->FSxMTSODeleteTemp();
        echo $this->RequestView('common','mainpage/wContent',$aArrayContent);
        
        $aArrayFooter = array(
            'tModules'  => 'document',
            'tFeatures' => 'pdtadjstkchk'
        );
        echo $this->RequestView('common','mainpage/wFooter', $aArrayFooter);
        
    }

    //content
    public function FSxCPASContentMain(){
        $tCallType  = $this->input->post('tCallType');
        $tParameter = $this->input->post('tParamter');

        if($tCallType == 'null'){ $tCallType = 'WEB';
        }else if(isset($tCallType)){ $tCallType = $tCallType;
        }else{ $tCallType = 'WEB'; }

        if($tParameter == null || $tParameter == ''){ $aDataresult    = 'null';
        }else{ $aDataresult    = $tParameter; }

        $this->mpdtadjstkchk->FSxMPASClearTempDT(); // เคลียร์สินค้าที่ไม่มีเลขที่เอกสารทั้งหมด
        $aArrayContent = array(
            'tModulename'   => 'omnPdtAdjStkChkNew',
            'tParameter'    => $aDataresult
        );
        echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchk',$aArrayContent);
    }

    public function FSxCPASCallPageMain(){
        // $aBranch    = getBranch();
        $aDataSerach = array(
            'FTIuhDocNo'        => $this->input->post('FTIuhDocNo'),
            'FTBchCode'         => $this->tBchCode,
        );
        $this->mpdtadjstkchk->FSxMPASUpdQtyC1($aDataSerach); //อัพเดท FCIudQtyC1 ถ้าหากสินค้ามาจาก แฮนเฮว (CFM-POS ComSheet-2020-014)
        $aGetDataHD = $this->mpdtadjstkchk->FSaMPASGetDataHD($aDataSerach);
        $aGetLoc    = $this->mpdtadjstkchk->FSxMPASGetLocation();
        $aWaHouse   = $this->mpdtadjstkchk->FSxMPASGetWaHouse();
        $aArrayContent = array(
            'nTypePage'         => $this->input->post('pnTypePage'),
            'aGetDataHD'        => $aGetDataHD,
            'aGetLoc'           => $aGetLoc,
            'ptWahCode'         => $aWaHouse['aItems'][0]['FTWahCode']
        );
        $aDataReturn = array(
            'tQuery'            => $aGetDataHD,
            'tHTML'             => $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkMain',$aArrayContent)
        );
        echo json_encode($aDataReturn);
        // echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkMain',$aArrayContent);
    }

    //
    public function FSxCPASCallDataTable(){
        $nTypePage      = $this->input->post('pnTypePage');
        $tStaPrcDoc     = $this->input->post('ptStaPrcDoc');
        // $aDataWhereSplCode = array(
        //     'FTIuhDocNo'      => $this->input->post('FTIuhDocNo')
        // );
        // $aDataSplCode = $this->mpdtadjstkchk->FSaMPASCheckConfirmCode($aDataWhereSplCode);

        // echo $tStaPrcDoc;exit;

        // ใบรวมให้ไปเช็ค เคลื่อนไหวหลังตรวจนับ และ อัพเดท คงเหลือกับปรับ(+,-)
        if( $nTypePage != "1" && $tStaPrcDoc != "1" ){
            $aDataAfterCount = array(
                'tBchCode'          => $this->tBchCode,
                'FTIuhDocNo'        => $this->input->post('FTIuhDocNo')
                // 'nRow'              => 20,
                // 'nPage'             => $this->input->post('nPageCurrent')
            );
            $this->mpdtadjstkchk->FSaMPASUpdFromStockCard($aDataAfterCount); // อัพเดท คงเหลือ, ปรับ(+,-), เคลื่อนไหวหลังตรวจนับ 
            $this->mpdtadjstkchk->FSxMPASUpdWahQty($aDataAfterCount);        // อัพเดท จำนวนปัจจุบัน จาก TCNMPdt.FCPdtQtyRet
            // $aGetDataAfterCount = $this->mpdtadjstkchk->FSaMPASGetDataAfterCount($aDataAfterCount); // เคลื่อนไหวหลังตรวจนับ
        }
        // else{
        //     $aGetDataAfterCount = array(
        //         'aItems'        => array(),
        //         'nStaQuery'     => 99,
        //         'tStaMessage'   => 'not found data',
        //     );
        // }
        
        $aDataSearch = array(
            'tBchCode'          => $this->tBchCode,
            'FTIuhDocNo'        => $this->input->post('FTIuhDocNo'),
            'nRow'              => 20,
            'nPage'             => $this->input->post('nPageCurrent'),
            'nTypePage'         => $nTypePage
        );
        $aGetDataTable = $this->mpdtadjstkchk->FSxMPASGetDataTable($aDataSearch); // เอกสารตรวจนับสินค้า 

        $aArrayContent = array(
            'nTypePage'         => $nTypePage,
            'aDataTable'        => $aGetDataTable,
            // 'aDataAfterCount'   => $aGetDataAfterCount,
            'nPage'             => $aDataSearch['nPage'],
            'bChkDateDT'        => $this->mpdtadjstkchk->FSbMPASEventChkDateDT($aDataSearch)
        );
        echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkDataTable',$aArrayContent);
    }

    //เรียกตารางสินค้าไม่มีในระบบ
    public function FSxCPASCallDataPdtWithOutSystemTable(){
        $nTypePage = $this->input->post('pnTypePage');

        $aDataSearch = array(
            'tBchCode'          => $this->tBchCode,
            'FTIuhDocNo'        => $this->input->post('FTIuhDocNo'),
            'nRow'              => 20,
            'nPage'             => $this->input->post('nPageCurrent'),
            'nTypePage'         => $nTypePage,
            'tPassword'         => $this->input->post('ptPassword')
        );
        $aGetDataPdtWithOutSystemTable = $this->mpdtadjstkchk->FSxMPASGetDataPdtWithOutSystemTable($aDataSearch); // สินค้าไม่มีในระบบ

        $aArrayContent = array(
            'nTypePage'         => $nTypePage,
            'aDataTable'        => $aGetDataPdtWithOutSystemTable,
            'nPage'             => $aDataSearch['nPage']
        );
        echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkDataPdtWithOutSystemTable',$aArrayContent);
    }

    public function FSxCPASAddProduct(){
        $aPlcCode = $_POST['paPlcCode'];
        $aStaAdd = [];
        for($i=0;$i<count($aPlcCode);$i++){
            $aAddPdt = array(
                'ptDocNo'       => $this->input->post('ptDocNo'),
                'ptDocDate'     => $this->input->post('ptDocDate'),
                'ptTab'         => $this->input->post('ptTab'),
                'ptFromCode'    => $this->input->post('ptFromCode'),
                'ptToCode'      => $this->input->post('ptToCode'),
                'ptGrpCode'     => $this->input->post('ptGrpCode'),
                'ptLocCode'     => $this->input->post('ptLocCode'),
                'ptPlcCode'     => $aPlcCode[$i]
            );
            //ตรวจเช็คสินค้าซ้ำ และ location ซ้ำ
            $aReturnChkPrdDup   = $this->mpdtadjstkchk->FSxMPASChkPrdDuplicate($aAddPdt);
            array_push($aStaAdd,$aReturnChkPrdDup['nStaQuery']); //เพิ่ม status การ insert data
            if($aReturnChkPrdDup['nStaQuery'] == 1){ //ถ้าไม่พบสินค้าซ้ำให้ทำการเพิ่มสินค้าใหม่เข้าไปได้
                $aReturnPdt         = $this->mpdtadjstkchk->FSxMPASAddProduct($aAddPdt);
                array_push($aStaAdd,$aReturnPdt['nStaQuery']); //เพิ่ม status การ insert data
            }
        }

    //    print_r($aStaAdd);
    //    exit;

        //ตรวจสอบว่าในการ loop insert มีครั้งไหนพบ error หรือไม่ ?
        if(in_array(99, $aStaAdd)){
            $aDataReturn = array(
                // 'aReturnChkPrdDup'  => $aReturnChkPrdDup,
                // 'aStaAdd'       => $aStaAdd,
                'tSQL'          => $aReturnChkPrdDup,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'ไม่พบสินค้า',
            );
        }else if(in_array(88, $aStaAdd)){
            $aDataReturn = array(
                // 'aStaAdd'       => $aStaAdd,
                'tSQL'          => $aReturnPdt,
                'nStaQuery'     => 88,
                'tStaMessage'   => 'มีสินค้าใน DT แล้ว',
            );
        }else{ 
            $aDataReturn = array(
                // 'aStaAdd'       => $aStaAdd,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'เพิ่มสินค้าเรียบร้อย',
            );
        }
        echo json_encode($aDataReturn);
    }

    public function FSxCPASChangeProduct(){
        $aChangePdt = array(
            'ptDocNo'       => $this->input->post('ptDocNo'),
            'pnSeq'         => $this->input->post('pnSeq'),
            'ptPdtCode'     => $this->input->post('ptPdtCode')
        );
        $aReturnPdt = $this->mpdtadjstkchk->FSxMPASChangeProduct($aChangePdt);
        echo json_encode($aReturnPdt);
    }

    public function FSxCPASCheckDateTime($ptDocNo){
        $tDocNo = $this->input->post('ptDocNo');
        $aChkDateTime = $this->mpdtadjstkchk->FSaMPASCheckDateTime($tDocNo);
        // if($bChk === false){
        //     // $aUpdDateTime = $this->mpdtadjstkchk->FSaMPASUpdateDateTime($tDocNo);
        //     $aReturn = array(
        //         'nStaQuery'     => $aUpdDateTime['nStaQuery'],
        //     );
        // }else{
        //     $aReturn = array(
        //         'nStaQuery'     => 99,
        //     );
        // }
        echo json_encode($aChkDateTime);
    }

    public function FSxCPASUpdateDateTime($ptDocNo){
        $aDataUpd = array(
            'tBchCode'          => $this->tBchCode,
            'tDocNo'            => $this->input->post('ptDocNo')
        );

        if( $aDataUpd['tDocNo'] != "" ){
            $this->mpdtadjstkchk->FSxMPASUpdateConfirmCode($aDataUpd);
        }

        $aUpdDateTime = $this->mpdtadjstkchk->FSaMPASUpdateDateTime($aDataUpd);
        echo json_encode($aUpdDateTime);
    }

    // public function FSxCPASAddConfirmCode(){
    //     $aDataList = array(
    //         'tDocNo'        => $this->input->post('ptDocNo'),
    //         'tConfirmCode'  => $this->input->post('ptConfirmCode')
    //     );
    //     $aAddConCode = $this->mpdtadjstkchk->FSaMPASAddConfirmCode($aDataList);
    //     echo json_encode($aAddConCode);
    // }

    //บันทึกเอกสาร HD
    public function FSxCPASAddEditHD(){
        $tDocNoChk   = $this->input->post('oetPASDocNo');
        $tTypePage   = $this->input->post('ptTypePage');

        $tRmk       = ($this->input->post('otaPASNote') == "" ? "NULL" : $this->input->post('otaPASNote'));
        $tHhdNumber = ($this->input->post('oetPASHHD') == "" ? "NULL" : $this->input->post('oetPASHHD'));
        $tAdjType   = ($this->input->post('ptAdjType') == 'true' ? "1" : "2");

        $tRefTaxOver = ($this->input->post('ptRefTaxOver') == "" ? "NULL" : $this->input->post('ptRefTaxOver'));
        $tCstCode    = ($this->input->post('ptRefTaxOver') == "" ? "NULL" : "CFM-HQ");

        if($tDocNoChk == ''){ //บันทึก และสร้างเลขที่เอกสาร
            $tDocNo     = generateCode('TCNTPdtChkHD','FTIuhDocNo');
            if($tTypePage == '3'){ //ใบรวมเอกสารตรวจนับสินค้า
                $aDataQuery = array(
                    'FTCstCode'             => $tCstCode,
                    'FTIuhRefTaxOver'       => $tRefTaxOver,
                    'FTBchCode'             => $this->tBchCode,
                    'FTIuhDocNo'            => $tDocNo,
                    'FTIuhDocType'          => '2',
                    'FTIuhAdjType'          => $tAdjType,
                    'FTIuhStaDoc'           => '1',
                    'FTIuhStaPrcDoc'        => '4',
                    'FNIuhStaDocAct'        => 1,
                    'FTIuhStaSavZero'       => '1',
                    'FDIuhDocDate'          => $this->input->post('oetPASDocDate'),
                    'FTIuhDocTime'          => date('H:i:s'),
                    'FTIuhHhdNumber'        => "NULL",
                    'FTWahCode'             => '001', //$this->input->post('oetPASWahCode')
                    'FTIuhRmk'              => $tRmk,
                    'FTSplCode'             => $this->input->post('ptInput'),//Confirm Code
                    'FTIuhDocRef'           => $tDocNo,
                    'FTDptCode'             => $_SESSION["SesUserDptCode"],
                    'FTUsrCode'             => $_SESSION["SesUsercode"],
                    'FDDateUpd'             => date('Y-m-d'),
                    'FTTimeUpd'             => date('H:i:s'),
                    'FTWhoUpd'              => $_SESSION["SesUsername"],
                    'FDDateIns'             => date('Y-m-d'),
                    'FTTimeIns'             => date('H:i:s'),
                    'FTWhoIns'              => $_SESSION["SesUsername"]
                    // 'FNLogStaExport'        => 1
                );
            }else{                 //ใบย่อยเอกสารตรวจนับสินค้า
                $aDataQuery = array(
                    'FTCstCode'             => $tCstCode,
                    'FTIuhRefTaxOver'       => $tRefTaxOver,
                    'FTBchCode'             => $this->tBchCode,
                    'FTIuhDocNo'            => $tDocNo,
                    'FTIuhDocType'          => $this->input->post('oetPASIuhDocType'),
                    'FTIuhAdjType'          => $tAdjType,
                    'FTIuhStaDoc'           => '1',
                    'FNIuhStaDocAct'        => 1,
                    'FTIuhStaSavZero'       => '1',
                    'FDIuhDocDate'          => $this->input->post('oetPASDocDate'),
                    'FTIuhDocTime'          => date('H:i:s'),
                    'FTIuhHhdNumber'        => $tHhdNumber,
                    'FTWahCode'             => '001', //$this->input->post('oetPASWahCode')
                    'FTIuhRmk'              => $tRmk,
                    'FTSplCode'             => $this->input->post('ptInput'),//Confirm Code
                    'FTDptCode'             => $_SESSION["SesUserDptCode"],
                    'FTUsrCode'             => $_SESSION["SesUsercode"],
                    'FDDateUpd'             => date('Y-m-d'),
                    'FTTimeUpd'             => date('H:i:s'),
                    'FTWhoUpd'              => $_SESSION["SesUsername"],
                    'FDDateIns'             => date('Y-m-d'),
                    'FTTimeIns'             => date('H:i:s'),
                    'FTWhoIns'              => $_SESSION["SesUsername"]
                    // 'FNLogStaExport'        => 1
                );
            }

            $aAddEditHD  = $this->mpdtadjstkchk->FSaMPASAddEditHD($aDataQuery);         // insert เอกสาร HD
            if( $aAddEditHD['nStaQuery'] == 1 ){
                $this->mpdtadjstkchk->FSaMPASUpdDocNoToDT($aDataQuery);                 // อัพเดทเลขที่เอกสารใน DT
                $this->mpdtadjstkchk->FSaMPASAddConfirmCode($aDataQuery);               // อัพเดทรหัสผ่าน
            }
            $aReturnAddEditHD = array(
                'FTIuhDocNo'    => $aDataQuery['FTIuhDocNo'],
                'aDataQuery'    => $aAddEditHD
            );

        }
        /*else{
            $aDataQuery = array(
                'FTBchCode'             => $this->tBchCode,
                'FTIuhDocNo'            => $tDocNoChk,
            );
            $this->mpdtadjstkchk->FSxMPASUpdateConfirmCode($aDataQuery);
            $aReturnAddEditHD = array(
                'FTIuhDocNo'    => $aDataQuery['FTIuhDocNo'],
                'aDataQuery'    => array(
                    'nStaQuery' => 1
                )
            );
        }*/

        if($tTypePage == '3'){
            $this->mpdtadjstkchk->FSaMPASUpdDocRefOfSubDoc($aDataQuery); // อัพเดท DocRef เอกสารย่อย
            $this->mpdtadjstkchk->FSxMPASDocStkNotExist($aDataQuery); // Update ลงข้อมูล TCNTPdtStkNotExist 
        }
        
        $this->mpdtadjstkchk->FSaMPASUpdDocNoToDTCut($aDataQuery); // อัพเดท DocNo ตาราง DTCut ตอนบันทึกใบย่อย
        
        
        echo json_encode($aReturnAddEditHD);
    }

    public function FSxCPASEditInLine(){
        if($this->input->post('ptTypePage') == "1"){
            $aDataQuery = array(
                'FTIuhDocNo'            => $this->input->post('ptDocNo'),
                'FTIuhDocType'          => '1',
                'FNIudSeqNo'            => $this->input->post('ptSeq'),
                'FCIudQtyC1'            => $this->input->post('ptVal'),
                'FTPlcCode'             => $this->input->post('ptPlcCode'),
                'FDIudChkDate'          => $this->input->post('pdDate'),
                'FTIudChkTime'          => $this->input->post('ptTime')
            );
            $aEditInLine = $this->mpdtadjstkchk->FSaMPASEditInLine($aDataQuery);
        }else{
            $aDataQuery = array(
                'FTIuhDocNo'            => $this->input->post('ptDocNo'),
                'FTIuhDocType'          => '2',
                'FNIudSeqNo'            => $this->input->post('ptSeq'),
                'FCIudQtyBal'           => $this->input->post('ptQtyBal')
            );
            $aEditInLine = $this->mpdtadjstkchk->FSaMPASEditInLine($aDataQuery);
        }
        echo json_encode($aEditInLine);
    }

    public function FSxCPASEditInLinePdtWithOutSystem(){
        $aDataQuery = array(
            'FTIuhDocNo'           => $this->input->post('ptDocRef'),
            'FTPdtBarCode'         => $this->input->post('ptBarCode'),
            'FTPlcCode'            => $this->input->post('ptPlcCode'),
            'FTPdtName'            => $this->input->post('ptPdtName'),
            'FCIudSetPrice'        => $this->input->post('pcSetPri'),
            'FCIudUnitC1'          => $this->input->post('pnUnitC1'),
            'FTWhoUpd'             => $_SESSION["SesUsername"]
        );
        echo json_encode($this->mpdtadjstkchk->FSaMPASEditInLinePdtWithOutSystem($aDataQuery));
    }

    public function FSxCPASCancelHD(){
        $aDataQuery = array(
            'FTIuhDocNo'            => $this->input->post('ptDocNo')
        );
        $aCancelHD = $this->mpdtadjstkchk->FSaMPASCancelHD($aDataQuery);
        echo json_encode($aCancelHD);
    }

    public function FSxCPASDelProduct(){
        $aDataWhere = array(
            'ptSeq'      => $this->input->post('ptSeq'),
            'ptDocNo'    => $this->input->post('ptDocNo'),
        );
        $aDelProduct = $this->mpdtadjstkchk->FSaMPASDelProduct($aDataWhere);
        $this->mpdtadjstkchk->FSaMPASSortProduct($aDataWhere);
        echo json_encode($aDelProduct);
    }

    public function FSxCPASCheckConfirmCode(){
        $aDataWhere = array(
            'FTIuhDocNo'      => $this->input->post('ptDocNo'),
            'FTSplCode'       => $this->input->post('ptPass'),
        );
        $aChkConCode = $this->mpdtadjstkchk->FSaMPASCheckConfirmCode($aDataWhere);
        echo json_encode($aChkConCode);
    }

    public function FSxCPASMergeSTK(){
        // $aBranch    = getBranch();
        $aDataWhere = array(
            'FTIuhDocNo'            => $this->input->post('ptDocNo'),
            'FTBchCode'             => $this->tBchCode,
            'tPassword'             => $this->input->post('ptPassword'),
            'bAdjType'              => $this->input->post('pbAdjType')
        );
        $aDataUpdHDDT = array(
            'FTIudStaPrc'           => '1',
            'FTIuhStaPrcDoc'        => '2',
            'FTIuhApvCode'          => $_SESSION["SesUsercode"],
            'FTWhoUpd'              => $_SESSION["SesUsername"]
        );
        $aPackDataInsert = array(
            'FTBchCode'             => $this->tBchCode,
            'bAdjType'              => $this->input->post('pbAdjType')
        );

        $this->mpdtadjstkchk->FSxPASBegin_Transaction();
        $this->mpdtadjstkchk->FSxMPASUpdQtyC1($aDataWhere); // อัพเดท FCIudQtyC1 ถ้าหากสินค้ามาจาก แฮนเฮว (เนื่องจาก import แล้วไม่ refresh หน้าจอ จึงต้องอัพเดท)
        $aUpdHDDT = $this->mpdtadjstkchk->FSaMPASUpdStaPrcHDAndDT($aDataWhere,$aDataUpdHDDT); // อัพเดท StaDoc และ StaPrcDoc ในตาราง HD DT
        if( $aUpdHDDT['nStaQuery'] == 1 ){
            $aCreateView        = $this->mpdtadjstkchk->FSaMPASCreateView($aDataWhere); // ลบ View และสร้าง View ใหม่
            $aInsTmp_ChkDT      = $this->mpdtadjstkchk->FSaMPASCreateTmpDTAndInsertINTO($aDataWhere); // ลบ Tmp_ChkDT สร้างใหม่ และย้ายสินค้าจาก View มาไว้ใน Tmp
            $aInsTmp_SleB4Audit = $this->mpdtadjstkchk->FSaMPASCreateTmpSleB4AuditAndInsertINTO($aDataWhere); // ลบ Tmp_SleB4Audit สร้างใหม่ และย้ายสินค้าจาก View มาไว้ใน Tmp_SleB4Audit
            
            $aTableSaleHD       = $this->mpdtadjstkchk->FSaMPASGetTableSaleHD(); //กรองเอา table(HD) ของการขายที่เกี่ยวข้อง
            $aSalesOfDay        = $this->mpdtadjstkchk->FSaMPASGetOpeningFirstSalesOfDay(); //หาเวลาของการเปิดรอบการขายอันแรกของวัน
            if( $aSalesOfDay['nStaQuery'] == 1 ){
                $tTime = $aSalesOfDay['aDataList']['FDIuhDocDatetime'];
            }else{
                $tTime = "";
            }
            if( $aTableSaleHD['nStaQuery'] == 1 ){
                foreach($aTableSaleHD['aDataList'] AS $tValue){
                    $this->mpdtadjstkchk->FSaMPASUpdateSalesB4Counting($tValue['TABLE_NAME'],$tTime); //อัพเดท ยอดขายก่อนนับ
                }
            }

            $this->mpdtadjstkchk->FSaMPASMoveSleB4ToTmpChkDT(); //อัพเดท ยอดขายก่อนนับเข้าตาราง Tmp_ChkDT
            $this->mpdtadjstkchk->FSaMPASUpdQtyDiffandQtyBal(); //อัพเดท QtyDiff และ QtyBal

            $this->mpdtadjstkchk->FSxMPASDelAllPdtChkDTForDocType2(); //ลบสินค้าเก่าที่ค้างอยู่ในตาราง
            $AddTmpChkDTToDT = $this->mpdtadjstkchk->FSxMPASAddTmpChkDTToDT($aPackDataInsert); //ย้ายสินค้าจาก Tmp_ChkDT ไปยัง TCNTPdtChkDT

            $this->mpdtadjstkchk->FSaMPASUpdSalesB4Count(); //Comsheet 2019 332

            // $aCheck = array(
            //     'aCreateView' => $aCreateView['nStaQuery'],
            //     'aInsTmp_ChkDT' => $aInsTmp_ChkDT['nStaQuery'],
            //     'aInsTmp_SleB4Audit' => $aInsTmp_SleB4Audit['nStaQuery'],
            //     'AddTmpChkDTToDT' => $AddTmpChkDTToDT['nStaQuery']
            // );
            // print_r($aCheck);
            // exit;

            if($aCreateView['nStaQuery'] == 1 && $aInsTmp_ChkDT['nStaQuery'] == 1 && $aInsTmp_SleB4Audit['nStaQuery'] == 1 && $AddTmpChkDTToDT['nStaQuery'] == 1){
                $this->mpdtadjstkchk->FSxPASCommit();
                $aReturn = array(
                    'nStaQuery'             => 1,
                    'tStaMessage'           => 'MergeSTK Success.',
                );
            }else{
                $this->mpdtadjstkchk->FSxPASRollBack();
                $aResultAll = array();
                array_push($aResultAll,$aCreateView['aResultAll'],$aInsTmp_ChkDT['aResultAll'],$aInsTmp_SleB4Audit['aResultAll'],$AddTmpChkDTToDT['aResultAll']);

                $aReturn = array(
                    'nStaQuery'             => 42000,
                    'tStaMessage'           => 'MergeSTK Error.',
                    'aResultAll'            => $aResultAll
                );
            }
        }else{
            $this->mpdtadjstkchk->FSxPASRollBack();
            $aReturn = array(
                'tSQL'          => $aUpdHDDT,
                'nStaQuery'     => $aUpdHDDT['nStaQuery'],
                'tStaMessage'   => $aUpdHDDT['tStaMessage']
            );
        }
        echo json_encode($aReturn);
    }

    public function FSxCPASApprove(){
        try{
            // FCNxMSMQCreateQueue($_POST['paParams']);
            echo "Success";
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function FSxCPASUpdStaExport(){
        try{
            $this->mpdtadjstkchk->FSxMPASUpdStaExport($this->input->post('ptDocNo'));
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function FSaCPASCallSearchHQ(){
        try{
            $aDataSearch = array(
                'tSearch'       => $this->input->post('ptSearch'),
                'FTBchCode'     => $this->tBchCode,
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent')
            );
            $aDataSearchHQ = $this->mpdtadjstkchk->FSaMPASGetDataSearchHQ($aDataSearch);
            
            $aDataReturn = array(
                'tSQL'          => $aDataSearchHQ['tSQL'],
                'tFirtItem'     => ($aDataSearchHQ['nStaQuery'] == 1 ? $aDataSearchHQ['aItems'][0]['FTPdtCylCntNo'] : "NULL"),
                'tHTML'         => $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchHQ',$aDataSearchHQ)
            );
            echo json_encode($aDataReturn);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function FSvCPASCallSearchHQList(){
        try{
            $aDataSearch = array(
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent'),
                'FTPdtCylCntNo' => ($this->input->post('ptPdtCylCntNo') == "NULL" ? "9999999999" : $this->input->post('ptPdtCylCntNo')),
                'FTBchCode'     => $this->tBchCode
            );
            $aDataSearchHQList = $this->mpdtadjstkchk->FSaMPASGetDataSearchHQList($aDataSearch);
            echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchHQ',$aDataSearchHQList);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function FSaCPASAddDataFromSearchHQ(){
        try{
            $aPlcCode           = $_POST['paPlcCode'];
            $aStaAdd            = [];
            $aDataSearch        = array(
                'FTPdtCylCntNo'     => $this->input->post('ptPdtCylCntNo'),
                'FTBchCode'         => $this->tBchCode
            );
            for($i=0;$i<count($aPlcCode);$i++){
                $aDataSearch['FTPlcCode'] = $aPlcCode[$i];
                $aReturnPdt = $this->mpdtadjstkchk->FSaMPASAddDataSearchHQ2DT($aDataSearch);
                array_push($aStaAdd,$aReturnPdt['nStaQuery']);
            }
            
            //ตรวจสอบว่าในการ loop insert มีครั้งไหนพบ error หรือไม่ ?
            if(in_array(99, $aStaAdd)){
                $aDataReturn = array(
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'SQL Error',
                );
            }else{
                $aChkDataHQ = $this->mpdtadjstkchk->FSaMPASChkDataSearchHQ($aDataSearch);
                if($aChkDataHQ['nStaQuery'] == 1){
                    $aDataReturn = array(
                        'aItems'        => $aChkDataHQ['aDataAudit'],
                        'nStaQuery'     => 2,
                        'tStaMessage'   => 'insert success',
                    );
                }else{
                    $aDataReturn = array(
                        'aItems'        => array(),
                        'nStaQuery'     => 1,
                        'tStaMessage'   => 'insert success',
                    );
                }
            }
            echo json_encode($aDataReturn);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function FSaCPASCallSearchHD(){
        try{
            $aDataSearch = array(
                'tSearch'       => $this->input->post('ptSearch'),
                'FTBchCode'     => $this->tBchCode,
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent')
            );
            $aDataSearchHD = $this->mpdtadjstkchk->FSaMPASGetDataSearchHD($aDataSearch);
            
            $aDataReturn = array(
                'tFirtItem'     => ($aDataSearchHD['nStaQuery'] == 1 ? $aDataSearchHD['aItems'][0]['FTIuhDocNo'] : "NULL"),
                'tHTML'         => $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearch',$aDataSearchHD)
            );
            echo json_encode($aDataReturn);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function FSvCPASCallSearchDT(){
        try{
            $aDataSearch = array(
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent'),
                'FTIuhDocNo'    => ($this->input->post('ptIuhDocNo') == "NULL" ? "9999999999" : $this->input->post('ptIuhDocNo')),
                'FTBchCode'     => $this->tBchCode
            );
            $aDataSearchDT = $this->mpdtadjstkchk->FSaMPASGetDataSearchDT($aDataSearch);
            echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearch',$aDataSearchDT);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    //ตรวจสอบยกยอดมาสิ้นเดือน
    public function FSaCPASCheckMonthEnd(){
        try{
            $aConditionData = array(
                'FTIuhDocNo'        => $this->input->post('FTIuhDocNo'),
                'FTBchCode'         => $this->tBchCode,
                'FTWhoUpd'          => $_SESSION["SesUsername"],
            );

            // Napat(Jame) 18/11/2022 Comsheet/2022-055 อัพเดทวันที่-เวลา เอกสารใบรวมเพื่อให้ระบบคำนวณยอด เคลื่อนไหวหลังตรวจนับใหม่ได้ถูกต้อง
            $this->mpdtadjstkchk->FSxMPASUpdDateTimeB4Apv($aConditionData);

            // Napat(Jame) 14/11/2022 Comsheet/2022-055
            $this->mpdtadjstkchk->FSaMPASUpdFromStockCard($aConditionData); // อัพเดท คงเหลือ, ปรับ(+,-), เคลื่อนไหวหลังตรวจนับ 
            $this->mpdtadjstkchk->FSxMPASUpdWahQty($aConditionData); // อัพเดท จำนวนปัจจุบัน จาก TCNMPdt.FCPdtQtyRet

            $aDataChkME = $this->mpdtadjstkchk->FSaMPASCheckMonthEnd();
            echo json_encode($aDataChkME);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    //ค้นหาสินค้า
    public function FSxCPASSearchProduct(){
        try{
            $tTabActive = $this->input->post('ptTabActive');
            $aDataSearch = array(
                'FTBchCode'         => $this->tBchCode,
                'FTIuhDocNo'        => $this->input->post('FTIuhDocNo'),
                'ptTextSearch'      => $this->input->post('FTIudBarCode'),
                'nPageType'         => $this->input->post('ptPageType'),
                'ptFilter'          => $this->input->post('ptFilter')
            );
            if($tTabActive == "PDTCHK"){
                $aGetData = $this->mpdtadjstkchk->FSaMPASSearchProduct($aDataSearch); // สินค้าตรวจนับ
            }else{
                $aGetData = $this->mpdtadjstkchk->FSaMPASSearchPdtWithOutSystem($aDataSearch); // สินค้าไม่มีในระบบ
            }
            echo json_encode($aGetData);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Function : ค้นหาเอกสาร D/O (HD)
    // Create By: Napat(Jame) 05/08/2020
    public function FSaCPASCallSearchDO(){
        try{
            $aDataSearch = array(
                'tDocNo'        => $this->input->post('ptDocNo'),
                'tSearch'       => $this->input->post('ptSearch'),
                'FTBchCode'     => $this->tBchCode,
                'nPage'         => $this->input->post('nPageCurrent'),
                'nDayDocRef'    => $this->mpdtadjstkchk->FSnMPASGetDayDocRef()
            );
            $aDataSearchDO = $this->mpdtadjstkchk->FSaMPASGetDataSearchDO($aDataSearch);
            
            $aDataReturn = array(
                'tSQL'          => $aDataSearchDO['tSQL'],
                'tFirtItem'     => ($aDataSearchDO['nStaQuery'] == 1 ? $aDataSearchDO['aItems'][0]['FTXihDocNo'] : "NULL"),
                'tHTML'         => $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchDocRef',$aDataSearchDO)
            );
            echo json_encode($aDataReturn);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Function : ค้นหาเอกสาร D/O (DT)
    // Create By: Napat(Jame) 05/08/2020
    public function FSvCPASCallSearchDOList(){
        try{
            $aDataSearch = array(
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent'),
                'FTXihDocNo'    => ($this->input->post('ptDocNo') == "NULL" ? "" : $this->input->post('ptDocNo')),
                'FTBchCode'     => $this->tBchCode
            );
            $aDataSearchDOList = $this->mpdtadjstkchk->FSaMPASGetDataSearchDOList($aDataSearch);
            echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchDocRef',$aDataSearchDOList);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Function : ค้นหาเอกสารอ้างอิง Auto Receive (HD)
    // Create By: Napat(Jame) 07/08/2020
    public function FSaCPASCallSearchAutoReceive(){
        try{
            $aDataSearch = array(
                'tDocNo'        => $this->input->post('ptDocNo'),
                'tSearch'       => $this->input->post('ptSearch'),
                'FTBchCode'     => $this->tBchCode,
                'nPage'         => $this->input->post('nPageCurrent'),
                'nDayDocRef'    => $this->mpdtadjstkchk->FSnMPASGetDayDocRef()
            );
            $aDataSearchAutoReceive = $this->mpdtadjstkchk->FSaMPASGetDataSearchAutoReceive($aDataSearch);
            
            $aDataReturn = array(
                'tSQL'          => $aDataSearchAutoReceive['tSQL'],
                'tFirtItem'     => ($aDataSearchAutoReceive['nStaQuery'] == 1 ? $aDataSearchAutoReceive['aItems'][0]['FTXihDocNo'] : "NULL"),
                'tHTML'         => $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchDocRef',$aDataSearchAutoReceive)
            );
            echo json_encode($aDataReturn);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Function : ค้นหาเอกสารอ้างอิง Auto Receive (DT)
    // Create By: Napat(Jame) 07/08/2020
    public function FSvCPASCallSearchAutoReceiveList(){
        try{
            $aDataSearch = array(
                'nRow'          => 5,
                'nPage'         => $this->input->post('nPageCurrent'),
                'FTXihDocNo'    => ($this->input->post('ptDocNo') == "NULL" ? "" : $this->input->post('ptDocNo')),
                'FTBchCode'     => $this->tBchCode
            );
            $aDataSearchDOList = $this->mpdtadjstkchk->FSaMPASGetDataSearchAutoReceiveList($aDataSearch);
            echo $this->RequestView('document','pdtadjstkchk/wpdtadjstkchkSearchDocRef',$aDataSearchDOList);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Function : เพิ่มการอ้างอิงเอกสาร D/O
    // Create By: Napat(Jame) 06/08/2020
    public function FSaCPASEventAddUpdDocRef(){
        try{
            $this->mpdtadjstkchk->FSxPASBegin_Transaction();
            $aDataRef = array(
                'tDocNo'            => $this->input->post('ptDocNo'),
                'tDocRef'           => $this->input->post('ptDocRef'),
                'nType'             => $this->input->post('pnRefType'), // 1 = D/O , 2 = Auto Receive
                'FTBchCode'         => $this->tBchCode,
                'tUserName'         => $_SESSION["SesUsername"]
            );
            $aAddRefDO = $this->mpdtadjstkchk->FSaMPASAddUpdDocRef($aDataRef);
            if( $aAddRefDO['nStaQuery'] == 1 ){
                $aCalQtyRef  = $this->mpdtadjstkchk->FSaMPASCalculateQtyRef($aDataRef);
                $aDataReturn = $aCalQtyRef;
            }else{
                $aDataReturn = $aAddRefDO;
            }

            // ตรวจว่าพบ Error ใน Modal หรือป่าว ถ้าพบ Error Query ให้ roll back ถ้าไม่มีให้ commit
            if( $aDataReturn['nStaQuery'] != 1 ){
                $this->mpdtadjstkchk->FSxPASRollBack();
            }else{
                $this->mpdtadjstkchk->FSxPASCommit();
            }

            echo json_encode($aDataReturn);
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}