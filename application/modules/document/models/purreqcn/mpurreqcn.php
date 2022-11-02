<?php

class mpurreqcn extends Database{

    public function __construct(){
        parent::__construct();
    }

    //เขียนไฟล์ : หน้าจอใบขอลดหนี้
    public function FSxWriteLogByPage($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'PRQ_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

    //Get Type Supplier
    public function FSaMPURGetTypeSupplier(){
        try {
            $tSQL = "SELECT FTStyCode,FTStyName 
                     FROM TCNMSplType WITH(NOLOCK)
                     WHERE ((TCNMSplType.FTStyCode)<>'0') ORDER BY FTStyCode";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetTypeSupplier] ".$e->getMessage());
        }
    }

    //Get Supplier 
    public function FSaMPURGetSupplier($paData){
        try {   

            $pnCode  = $paData['pnSupCode'];
            $aRowLen = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $tSQL    = "SELECT c.* FROM( SELECT  ROW_NUMBER() OVER(ORDER BY ";
            $tSQL    .= " FTSplCode ";
            $tSQL    .= ") AS rtRowID,* FROM";
            $tSQL    .= "( SELECT 
                        FTSplCode,
                        FTSplName,
                        FTStyCode 
                        FROM TCNMSpl WITH(NOLOCK)
                        WHERE FTStyCode = '$pnCode' ";

            $tSearchList = $paData['tSearchAll'];
            if ($tSearchList != ''){
                $tSQL .= " AND (FTSplCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR FTSplName LIKE '%$tSearchList%' )";
            }

            $tSQL   .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery) > 0) {
                $oList      = $oQuery;
                $aFoundRow  = $this->FSnMPURGetTypePageAll($paData);
                $nFoundRow  = $aFoundRow[0]['counts'];
                $nPageAll   = ceil($nFoundRow/$paData['nRow']); 
                    $aResult    = array(
                        'raItems'           => $oList,
                        'rnAllRow'          => $nFoundRow,
                        'rnCurrentPage'     => $paData['nPage'],
                        "rnAllPage"         => $nPageAll, 
                        'rtCode'            => '1',
                        'rtDesc'            => 'success',
                    );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }else{
                $aResult = array(
                    'rnAllRow'              => 0,
                    'rnCurrentPage'         => $paData['nPage'],
                    "rnAllPage"             => 0,
                    'rtCode'                => '800',
                    'rtDesc'                => 'data not found',
                );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }
            return $aResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetSupplier] ".$e->getMessage());
        }
    }

    //Get page Supplier
    public function FSnMPURGetTypePageAll($paData){
        $pnCode     = $paData['pnSupCode'];
        $tSQL       = "SELECT COUNT (FTSplCode) AS counts
                        FROM TCNMSpl WITH(NOLOCK)
                        WHERE FTStyCode = '$pnCode' ";
                
        $tSearchList = $paData['tSearchAll'];
        if ($tSearchList != ''){
            $tSQL .= " AND (FTSplCode LIKE '%$tSearchList%' ";
            $tSQL .= " OR FTSplName LIKE '%$tSearchList%' )";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Get Detail Suplier
    public function FSaMPURGetDetailSupplier($pnCodeSup,$pnTypeSup){
        $tSQL       = "SELECT 
                        SPL.FTSplCode , 
                        SPL.FTSplName , 
                        SPL.FTSplAddr , 
                        SPL.FTSplStreet , 
                        SPL.FTSplDistrict , 
                        SPL.FTDstCode , 
                        SPL.FTPvnCode , 
                        SPL.FTSplTel , 
                        SPL.FTSplFax , 
                        SPL.FTSpnCode ,
                        SPL.FTSplVATInOrEx ,
                        SPL.FNSplCrTerm,
                        SPL.FTShpCode ,
                        SPL.FTSgpCode ,
                        SPL.FTStyCode ,
                        SPL.FTAreCode ,
                        PRV.FTPvnName ,
                        DST.FTDstName ,
                        SPL.FTSplTspPaid 
                        FROM TCNMSpl SPL WITH(NOLOCK)
                        LEFT JOIN TCNMDistrict DST WITH(NOLOCK) on SPL.FTDstCode = DST.FTDstCode 
                        LEFT JOIN TCNMProvince PRV WITH(NOLOCK) on SPL.FTPvnCode = PRV.FTPvnCode 
                    WHERE SPL.FTSplCode = '$pnCodeSup' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Get Product [DT]
    public function FSaMPURGetProduct($paData){
        try {   
            $pnCode  = $paData['tDocumentID'];
            $aRowLen = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $tSQL    = "SELECT c.* FROM( SELECT  ROW_NUMBER() OVER(ORDER BY ";
            $tSQL    .= " FNXrdSeqNo ";
            $tSQL    .= ") AS rtRowID,* FROM";
            $tSQL    .= "( SELECT 
                        DT.FTBchCode,
                        DT.FTXrhDocNo,
                        DT.FNXrdSeqNo,
                        DT.FTPdtCode,
                        DT.FTPdtName,
                        DT.FTXrdBarCode,
                        DT.FTXrdApOrAr,
                        DT.FTXrhDocType,
                        DT.FTXrdUnitName,
                        DT.FCXrdQty,
                        DT.FCXrdSalePrice,
                        DT.FCXrdSetPrice,
                        DT.FCXrdNet,
                        DT.FTPunCode,
                        PU.FTPunName,
                        HD.FTXrhStaPrcDoc
                        FROM TACTPrDT DT WITH(NOLOCK)
                        LEFT JOIN TCNMPdtUnit PU WITH(NOLOCK) ON DT.FTPunCode = PU.FTPunCode
                        LEFT JOIN TACTPrHD HD WITH(NOLOCK) ON DT.FTXrhDocNo = HD.FTXrhDocNo
                        WHERE DT.FTXrhDocNo = '$pnCode' ";
            $tSQL   .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery) > 0) {
                    $oList      = $oQuery;
                    $aFoundRow  = $this->FSnMPURGetProductPageAll($paData);
                    $nFoundRow  = $aFoundRow[0]['counts'];
                    $nPageAll   = ceil($nFoundRow/$paData['nRow']); 
                    $aResult    = array(
                        'raItems'           => $oList,
                        'rnAllRow'          => $nFoundRow,
                        'rnCurrentPage'     => $paData['nPage'],
                        "rnAllPage"         => $nPageAll, 
                        'rtCode'            => '1',
                        'rtDesc'            => 'success',
                    );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }else{
                $aResult = array(
                    'rnAllRow'              => 0,
                    'rnCurrentPage'         => $paData['nPage'],
                    "rnAllPage"             => 0,
                    'rtCode'                => '800',
                    'rtDesc'                => 'data not found',
                );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }
            return $aResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetProduct] ".$e->getMessage());
        }
    }

    //Get page Product [DT]
    public function FSnMPURGetProductPageAll($paData){
        $pnCode     = $paData['tDocumentID'];
        $tSQL       = "SELECT COUNT (FTPdtCode) AS counts
                        FROM TACTPrDT WITH(NOLOCK)
                        WHERE FTXrhDocNo = '$pnCode' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Insert DT Case Browse Modal
    public function FSxMPURInsertPDT($ptParameter,$tDocumentID,$aPackDataInsert,$nParamSeq){
        try {
            //เข้ามาแบบ edit ต้องลบข้อมูลก่อน nParamSeq จะมีค่า
            if($nParamSeq != '' || $nParamSeq != null){
                $tDatabase          = "TACTPrDT";
                $aDataDeleteWHERE   = array(
                    'FNXrdSeqNo'    => $nParamSeq ,
                    'FTXrhDocNo'    => $tDocumentID 
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            }

            $aGetBranch     = getBranch();
            if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
                $aFormatCode    = explode("PE",$tFormatCode);
                $tFormatCode    = 'PE0' . $aFormatCode[1];
            }else{
                $tFormatCode    = $tDocumentID;
            }
            
            //insert DT
            $tDatabaseDT    = "TACTPrDT";
            if(!empty($ptParameter)){

                //SELECT เอา seq มาก่อน
                $tSQLseq        = "SELECT TOP 1 [FNXrdSeqNo] FROM TACTPrDT WITH(NOLOCK) WHERE FTXrhDocNo = '$tFormatCode' order by FNXrdSeqNo DESC";
                $tResultseq     = $this->DB_SELECT($tSQLseq);
                if(empty($tResultseq)){
                    $nSeq = 0;
                }else{
                    $nSeq = $tResultseq[0]['FNXrdSeqNo'];
                }

                //SELECT จาก SPL
                $tSPLCode       = $aPackDataInsert['tSPLCode'];
                $tSQLspl        = "SELECT TOP 1 FTSplVATInOrEx , FTAccCode FROM TCNMSpl WITH(NOLOCK) WHERE FTSplCode = '$tSPLCode'";
                $tResultspl     = $this->DB_SELECT($tSQLspl);
                $nVatSpl        = $tResultspl[0]['FTSplVATInOrEx'];
                $tFTAccCode     = $tResultspl[0]['FTAccCode'];

                //ประเภทผู้จำหน่าย
                $tRoundBranch   = $aPackDataInsert['tTypeRoundBranch'];
                if($tRoundBranch == 'PUR1'){ //ตามรอบ
                    $FTXrhDocType   = 5;
                }else if($tRoundBranch == 'PUR2'){ //ตามสาขา
                    $FTXrhDocType   = 6;
                }

                //รหัสคลังตัดสต็อก
                $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WITH(NOLOCK) WHERE FTSysCode = 'AWahWhsPur'";
                $oWahCode       = $this->DB_SELECT($tSQLWahCode);
                
                if($aPackDataInsert['tTypeInsertDT'] == 'PDT'){
                    //LOOP Insert for browser :: PDT
                    for($i=0; $i<count($ptParameter); $i++){
                        if($nParamSeq == '' || $nParamSeq == null){
                            $nSeq =  $nSeq + 1;
                        }else{
                            $nSeq =  $nParamSeq;
                        }

                        if($nVatSpl == 1){ //รวมใน

                            //Vat
                            $FCXrdVat = (1 * $ptParameter[$i]['FCPdtCostStd']) - ((1 * $ptParameter[$i]['FCPdtCostStd'] * 100)/(100+ $aPackDataInsert['nSelectValueVat']));
                            if($FCXrdVat != 0){
                                $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdVat = 0;
                            }

                            //VatTable
                            $FCXrdVatable = (1 * $ptParameter[$i]['FCPdtCostStd']) -  $FCXrdVat;
                            if($FCXrdVatable != 0){
                                $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdVatable = 0;
                            }

                            //ราคาต้นทุน
                            $FCXrdCostIn = 1 * $ptParameter[$i]['FCPdtCostStd'];
                            if($FCXrdCostIn != 0){
                                $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdCostIn = 0;
                            }

                            //ราคาแยกนอก
                            $FCXrdCostEx =  $FCXrdVatable;

                        }else{ //แยกนอก

                            //Vat
                            $FCXrdVat = (1 * $ptParameter[$i]['FCPdtCostStd']) * $aPackDataInsert['nSelectValueVat'] / 100;
                            if($FCXrdVat != 0){
                                $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdVat = 0;
                            }

                            //VatTable
                            $FCXrdVatable = 1 * $ptParameter[$i]['FCPdtCostStd'];
                            if($FCXrdVatable != 0){
                                $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdVatable = 0;
                            }

                            //ราคาต้นทุน
                            $FCXrdCostIn = (1 * $ptParameter[$i]['FCPdtCostStd']) + $FCXrdVat;
                            if($FCXrdCostIn != 0){
                                $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXrdCostIn = 0;
                            }

                            //ราคาแยกนอก
                            $FCXrdCostEx = $ptParameter[$i]['FCPdtCostStd'];
                        }

                        //ภาษีสรรพสามิต
                        $cUnitPrice     = $ptParameter[$i]['FCPdtCostStd'] / $ptParameter[$i]['FCPdtStkFac'];
                        if($ptParameter[$i]['FCPdtLawControl'] == 0){
                            $FCXrdExcDuty = 0;
                        }else{
                            $FCXrdExcDuty = ($ptParameter[$i]['FCPdtLawControl'] - ($ptParameter[$i]['FCPdtLawControl'] * 100 ) / (100 + $aPackDataInsert['nSelectValueVat'])) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $aPackDataInsert['nSelectValueVat']));
                            $FCXrdExcDuty = round($FCXrdExcDuty,2);
                        }

                        $aDataInsertDT  = array(
                            'FTBchCode'             => $aGetBranch['FTBchCode'],
                            'FTXrhDocNo'            => $tFormatCode,
                            'FNXrdSeqNo'            => $nSeq,
                            'FTPdtCode'             => $ptParameter[$i]['FTPdtCode'],
                            'FTPdtName'             => $ptParameter[$i]['FTPdtName'],
                            'FTXrhDocType'          => $FTXrhDocType,
                            'FDXrhDocDate'          => $aPackDataInsert['dDocDate'], /*date('Y-m-d')*/
                            'FTXrhVATInOrEx'        => $nVatSpl,
                            'FTXrdBarCode'          => $ptParameter[$i]['FTPdtBarCode'], 
                            'FTXrdStkCode'          => $ptParameter[$i]['FTPdtStkCode'],
                            'FCXrdStkFac'           => $ptParameter[$i]['FCPdtStkFac'],
                            'FTXrdVatType'          => $ptParameter[$i]['FTPdtVatType'],
                            'FTXrdSaleType'         => $ptParameter[$i]['FTPdtSaleType'],
                            'FTPgpChain'            => $ptParameter[$i]['FTPgpChain'],   
                            'FTSrnCode'             => 'NULL',
                            'FTPmhCode'             => 'NULL',
                            'FTPmhType'             => 'NULL', 
                            'FTPunCode'             => $ptParameter[$i]['FTPunCode'],      
                            'FTXrdUnitName'         => $ptParameter[$i]['FTPunName'],
                            'FCXrdFactor'           => $ptParameter[$i]['FCPdtStkFac'],
                            'FCXrdSalePrice'        => $ptParameter[$i]['FCPdtRetPri1'],
                            'FCXrdQty'              => 0,
                            'FCXrdSetPrice'         => $ptParameter[$i]['FCPdtCostStd'],
                            'FCXrdB4DisChg'         => 1 * $ptParameter[$i]['FCPdtCostStd'],
                            'FTXrdDisChgTxt'        => '',
                            'FCXrdDis'              => '',
                            'FCXrdChg'              => '',
                            'FCXrdNet'              => 0, //1 * $ptParameter[$i]['FCPdtCostStd']
                            'FCXrdVat'              => $FCXrdVat,
                            'FCXrdVatable'          => $FCXrdVatable,
                            'FCXrdQtyAll'           => 1,
                            'FCXrdCostIn'           => $FCXrdCostIn,
                            'FCXrdCostEx'           => $FCXrdCostEx,
                            'FTXrdStaPdt'           => 1,
                            'FTXrdStaRfd'           => 1,
                            'FTXrdStaPrcStk'        => 1,      
                            'FNXrhSign'             => 0,
                            'FTAccCode'             => $tFTAccCode,
                            'FNXrdPdtLevel'         => 0,     
                            'FTXrdPdtParent'        => $ptParameter[$i]['FTPdtCode'],
                            'FTXrdApOrAr'           => $ptParameter[$i]['FTSplCode'],
                            'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                            'FNXrdStaRef'           => 0,
                            'FCXrdQtySet'           => 'NULL', 
                            'FTPdtStaSet'           => $ptParameter[$i]['FTPdtStaSet'],
                            'FDXrdExpired'          => 'NULL',  
                            'FTXrdLotNo'            => 1,
                            'FCXrdQtyLef'           => 1,
                            'FCXrdQtyRfn'           => 'NULL', 
                            'FTXrhStaVatSend'       => 1,
                            'FTPdtArticle'          => $ptParameter[$i]['FTPdtArticle'],
                            'FTDcsCode'             => $ptParameter[$i]['FTDcsCode'],
                            'FTPszCode'             => $ptParameter[$i]['FTPszCode'],
                            'FTClrCode'             => $ptParameter[$i]['FTClrCode'],
                            'FTPszName'             => 'NULL', 
                            'FTClrName'             => 'NULL', 
                            'FCPdtLeftPO'           => 'NULL', 
                            'FTCpnCode'             => 'NULL', 
                            'FCXrdQtySale'          => 'NULL', 
                            'FCXrdQtyRet'           => 'NULL', 
                            'FCXrdQtyCN'            => 'NULL', 
                            'FCXrdQtyAvi'           => 'NULL', 
                            'FCXrdQtySgg'           => 'NULL', 
                            'FTXrhBchFrm'           => 'NULL',       
                            'FTXrhBchTo'            => 'NULL',       
                            'FTXrhWahFrm'           => 'NULL', 
                            'FTXrhWahTo'            => 'NULL', 
                            'FCXrhDiscGP1'          => 'NULL', 
                            'FCXrhDiscGP2'          => 'NULL', 
                            'FCXrdB4VatAfGP1'       => 'NULL', 
                            'FCXrdB4VatAfGP2'       => 'NULL', 
                            'FCXrdDisShp'           => 'NULL', 
                            'FCXrdShrDisShp'        => 'NULL', 
                            'FTXrdTaxInv'           => 'NULL', 
                            'FTPdtNoDis'            => $ptParameter[$i]['FTPdtNoDis'],
                            'FCXrdDisAvg'           => 'NULL', 
                            'FCXrdFootAvg'          => 'NULL', 
                            'FCXrdRePackAvg'        => 'NULL', 
                            'FCPdtLawControl'       => $ptParameter[$i]['FCPdtLawControl'],
                            'FCXrdExcDuty'          => $FCXrdExcDuty,
                            'FTPdtSaleType'         => $ptParameter[$i]['FTPdtSaleType'],
                            'FCPdtMax'              => 'NULL', 
                            'FDPdtOrdStart'         => 'NULL', 
                            'FDPdtOrdStop'          => 'NULL', 
                            'FTXrdPdtKey'           => 'NULL', 
                            'FTPmhDocNoBill'        => 'NULL', 
                            'FTXrdPmhCpnDocNo'      => 'NULL', 
                            'FCXrdPmhCpnGetQty'     => 'NULL', 
                            'FCXrdPmhCpnValue'      => 'NULL', 
                            'FCXrdDisGP'            => 'NULL', 
                            'FCXrdPmtQtyGet'        => 'NULL',       
                            'FDDateUpd'             => date('Y-m-d'),
                            'FTTimeUpd'             => date('H:i:s'),
                            'FTWhoUpd'              => $_SESSION["SesUsername"],
                            'FDDateIns'             => date('Y-m-d'),
                            'FTTimeIns'             => date('H:i:s'),
                            'FTWhoIns'              => $_SESSION["SesUsername"]
                        );

                        $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                    }
                }
                

                if($tResult == 'success'){
                    return 'success';
                }else{
                    return $tResult;
                }
            }else{
                return 'nodata';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMPURInsertPDT] ".$e->getMessage());
        }
        
    }

    //Insert DT Case Barcode
    public function FSxMPURInsertPDTCaseBarcode($tPDTCodeorBarcode,$tDocumentID,$nSPLCode,$nVat,$nSeq = null,$tStyCode){
        try { 
            $tSQL = "SELECT TOP 1 
                        TCNMPdt.FTPdtCode, 
                        TCNMPdt.FTPdtName,
                        TCNMPdt.FTPunCode,
                        TCNMPdt.FTPdtNameOth,
                        TCNMPdt.FTPdtNameShort,
                        TCNMPdtBar.FCPdtRetPri1,
                        TCNMPdtUnit.FTPunName,
                        TCNMPdt.FTSplCode,
                        TCNMPdt.FCPdtCostStd,
                        TCNMPdt.FTPdtStkCode,
                        TCNMPdt.FCPdtStkFac,
                        TCNMPdt.FTPdtVatType,
                        TCNMPdt.FTPgpChain,
                        TCNMPdt.FTPdtSaleType,
                        TCNMPdt.FTPdtStaSet,
                        TCNMPdt.FTPdtArticle,
                        TCNMPdt.FTDcsCode,
                        TCNMPdt.FTPszCode,
                        TCNMPdt.FTClrCode,
                        TCNMPdt.FTPdtNoDis,
                        TCNMPdt.FCPdtLawControl,
                        TCNMPdtBar.FTPdtBarCode
                        FROM TCNMPdt WITH(NOLOCK)
                        LEFT JOIN TCNMPdtBar WITH(NOLOCK) ON TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode
                        LEFT JOIN TCNMPdtUnit WITH(NOLOCK) ON TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode
                        WHERE (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                        AND (TCNMPdt.FTPdtCode='$tPDTCodeorBarcode' OR FTPdtBarCode='$tPDTCodeorBarcode') 
                        AND TCNMPdt.FTStyCode = '$tStyCode'
                        -- AND TCNMPdt.FTSplCode = '$nSPLCode'
                        AND TCNMPdt.FTPdtStaActive ='1' 
                        --AND TCNMPdt.FTPDTStaAlwBuy = '1'
                        AND TCNMPdt.FTPdtType IN ('1','4') 
                        AND TCNMPdt.FTPdtStaSet IN('1','2','3')
                        AND TCNMPdt.FTPdtStaReturn = '1'
                        AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                        ORDER BY TCNMPdtBar.FDPdtPriAffect DESC";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                //check ก่อนว่าสินค้าซ้ำไหมในตาราง
                $tCheckPDTBarcode = $this->FSxMPURCheckProduct($oQuery[0]['FTPdtCode'],$tDocumentID);
                if(empty($tCheckPDTBarcode)){
                   
                }else{
                    return 'DataDuplicate';
                    exit;
                }

                //get สาขา
                $aGetBranch   = getBranch();
                $tDatabaseDT  = "TACTPrDT";

                //ถ้าส่ง seq มาเเล้วเช็คว่า สินค้าซ้ำ ไหม 
                if($nSeq == null || $nSeq == '' ){

                }else{
                    //ต้องลบสินค้า 
                    $tDatabase          = "TACTPrDT";
                    $aDataDeleteWHERE   = array(
                        'FTBchCode'    => $aGetBranch['FTBchCode'] ,
                        'FTXrhDocNo'   => $tDocumentID,
                        'FNXrdSeqNo'   => $nSeq
                    );
                    $bConfirm           = true;
                    $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
                }

                //เลขที่เอกสาร
                if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                    $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
                    $aFormatCode    = explode("PE",$tFormatCode);
                    $tFormatCode    = 'PE0' . $aFormatCode[1];
                }else{
                    $tFormatCode    = $tDocumentID;
                }
            
                //SELECT จาก SPL
                $tSQLspl        = "SELECT TOP 1 FTSplVATInOrEx , FTAccCode FROM TCNMSpl WITH(NOLOCK) WHERE FTSplCode = '$nSPLCode'";
                $tResultspl     = $this->DB_SELECT($tSQLspl);
                $nVatSpl        = $tResultspl[0]['FTSplVATInOrEx'];
                $tFTAccCode     = $tResultspl[0]['FTAccCode'];

                //SELECT เอา seq มาก่อน
                if($nSeq == null || $nSeq == '' ){
                    //เข้ามาแบบ insert
                    $tSQLseq        = "SELECT TOP 1 [FNXrdSeqNo] FROM TACTPrDT WITH(NOLOCK) WHERE FTXrhDocNo = '$tDocumentID' order by FNXrdSeqNo DESC";
                    $tResultseq     = $this->DB_SELECT($tSQLseq);
                    if(empty($tResultseq)){
                        $nSeq = 1;
                    }else{
                        $nSeq = $tResultseq[0]['FNXrdSeqNo'] + 1;
                    }
                }else{
                    //เข้ามาแบบ edit รหัสสินค้า
                    $nSeq = $nSeq;
                }
                //รหัสคลังตัดสต็อก
                $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WITH(NOLOCK) WHERE FTSysCode = 'AWahWhsPur'";
                $oWahCode       = $this->DB_SELECT($tSQLWahCode);

                if($nVatSpl == 1){ //รวมใน

                    //Vat
                    $FCXrdVat = (1 * $oQuery[0]['FCPdtCostStd']) - ((1 * $oQuery[0]['FCPdtCostStd'] * 100)/(100+ $nVat));
                    if($FCXrdVat != 0){
                        $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdVat = 0;
                    }

                    //VatTable
                    $FCXrdVatable = (1 * $oQuery[0]['FCPdtCostStd']) -  $FCXrdVat;
                    if($FCXrdVatable != 0){
                        $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdVatable = 0;
                    }

                    //ราคาต้นทุน
                    $FCXrdCostIn = 1 * $oQuery[0]['FCPdtCostStd'];
                    if($FCXrdCostIn != 0){
                        $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdCostIn = 0;
                    }

                    //ราคาแยกนอก
                    $FCXrdCostEx =  $FCXrdVatable;

                }else{ //แยกนอก

                    //Vat
                    $FCXrdVat = (1 * $oQuery[0]['FCPdtCostStd']) * $nVat / 100;
                    if($FCXrdVat != 0){
                        $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdVat = 0;
                    }

                    //VatTable
                    $FCXrdVatable = 1 * $oQuery[0]['FCPdtCostStd'];
                    if($FCXrdVatable != 0){
                        $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdVatable = 0;
                    }

                    //ราคาต้นทุน
                    $FCXrdCostIn = (1 * $oQuery[0]['FCPdtCostStd']) + $FCXrdVat;
                    if($FCXrdCostIn != 0){
                        $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXrdCostIn = 0;
                    }

                    //ราคาแยกนอก
                    $FCXrdCostEx = $oQuery[0]['FCPdtCostStd'];
                }

                //ภาษีสรรพสามิต
                $cUnitPrice     = $oQuery[0]['FCPdtCostStd'] / $oQuery[0]['FCPdtStkFac'];
                if($oQuery[0]['FCPdtLawControl'] == 0){
                    $FCXrdExcDuty = 0;
                }else{
                    $FCXrdExcDuty = ($oQuery[0]['FCPdtLawControl'] - ($oQuery[0]['FCPdtLawControl'] * 100 ) / (100 + $nVat)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $nVat));
                    $FCXrdExcDuty = round($FCXrdExcDuty,2);
                }

                $aDataInsertDT  = array(
                    'FTBchCode'             => $aGetBranch['FTBchCode'],
                    'FTXrhDocNo'            => $tFormatCode,
                    'FNXrdSeqNo'            => $nSeq,
                    'FTPdtCode'             => $oQuery[0]['FTPdtCode'],
                    'FTPdtName'             => $oQuery[0]['FTPdtName'],
                    'FTXrhDocType'          => 6,
                    'FDXrhDocDate'          => date('Y-m-d'),
                    'FTXrhVATInOrEx'        => $nVatSpl,
                    'FTXrdBarCode'          => $oQuery[0]['FTPdtBarCode'], 
                    'FTXrdStkCode'          => $oQuery[0]['FTPdtStkCode'],
                    'FCXrdStkFac'           => $oQuery[0]['FCPdtStkFac'],
                    'FTXrdVatType'          => $oQuery[0]['FTPdtVatType'],
                    'FTXrdSaleType'         => $oQuery[0]['FTPdtSaleType'],
                    'FTPgpChain'            => $oQuery[0]['FTPgpChain'],   
                    'FTSrnCode'             => 'NULL',
                    'FTPmhCode'             => 'NULL',
                    'FTPmhType'             => 'NULL', 
                    'FTPunCode'             => $oQuery[0]['FTPunCode'],      
                    'FTXrdUnitName'         => $oQuery[0]['FTPunName'],
                    'FCXrdFactor'           => $oQuery[0]['FCPdtStkFac'],
                    'FCXrdSalePrice'        => $oQuery[0]['FCPdtRetPri1'],
                    'FCXrdQty'              => 0,
                    'FCXrdSetPrice'         => $oQuery[0]['FCPdtCostStd'],
                    'FCXrdB4DisChg'         => 1 * $oQuery[0]['FCPdtCostStd'],
                    'FTXrdDisChgTxt'        => '',
                    'FCXrdDis'              => '',
                    'FCXrdChg'              => '',
                    'FCXrdNet'              => 0, //1 * $oQuery[0]['FCPdtCostStd']
                    'FCXrdVat'              => $FCXrdVat,
                    'FCXrdVatable'          => $FCXrdVatable,
                    'FCXrdQtyAll'           => 1,
                    'FCXrdCostIn'           => $FCXrdCostIn,
                    'FCXrdCostEx'           => $FCXrdCostEx,
                    'FTXrdStaPdt'           => 1,
                    'FTXrdStaRfd'           => 1,
                    'FTXrdStaPrcStk'        => 1,      
                    'FNXrhSign'             => 0,
                    'FTAccCode'             => $tFTAccCode,
                    'FNXrdPdtLevel'         => 0,     
                    'FTXrdPdtParent'        => $oQuery[0]['FTPdtCode'],
                    'FTXrdApOrAr'           => $oQuery[0]['FTSplCode'],
                    'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                    'FNXrdStaRef'           => 0,
                    'FCXrdQtySet'           => 'NULL', 
                    'FTPdtStaSet'           => $oQuery[0]['FTPdtStaSet'],
                    'FDXrdExpired'          => 'NULL',  
                    'FTXrdLotNo'            => 1,
                    'FCXrdQtyLef'           => 1,
                    'FCXrdQtyRfn'           => 'NULL', 
                    'FTXrhStaVatSend'       => 1,
                    'FTPdtArticle'          => $oQuery[0]['FTPdtArticle'],
                    'FTDcsCode'             => $oQuery[0]['FTDcsCode'],
                    'FTPszCode'             => $oQuery[0]['FTPszCode'],
                    'FTClrCode'             => $oQuery[0]['FTClrCode'],
                    'FTPszName'             => 'NULL', 
                    'FTClrName'             => 'NULL', 
                    'FCPdtLeftPO'           => 'NULL', 
                    'FTCpnCode'             => 'NULL', 
                    'FCXrdQtySale'          => 'NULL', 
                    'FCXrdQtyRet'           => 'NULL', 
                    'FCXrdQtyCN'            => 'NULL', 
                    'FCXrdQtyAvi'           => 'NULL', 
                    'FCXrdQtySgg'           => 'NULL', 
                    'FTXrhBchFrm'           => 'NULL',       
                    'FTXrhBchTo'            => 'NULL',       
                    'FTXrhWahFrm'           => 'NULL', 
                    'FTXrhWahTo'            => 'NULL', 
                    'FCXrhDiscGP1'          => 'NULL', 
                    'FCXrhDiscGP2'          => 'NULL', 
                    'FCXrdB4VatAfGP1'       => 'NULL', 
                    'FCXrdB4VatAfGP2'       => 'NULL', 
                    'FCXrdDisShp'           => 'NULL', 
                    'FCXrdShrDisShp'        => 'NULL', 
                    'FTXrdTaxInv'           => 'NULL', 
                    'FTPdtNoDis'            => $oQuery[0]['FTPdtNoDis'],
                    'FCXrdDisAvg'           => 'NULL', 
                    'FCXrdFootAvg'          => 'NULL', 
                    'FCXrdRePackAvg'        => 'NULL', 
                    'FCPdtLawControl'       => $oQuery[0]['FCPdtLawControl'],
                    'FCXrdExcDuty'          => $FCXrdExcDuty,
                    'FTPdtSaleType'         => $oQuery[0]['FTPdtSaleType'],
                    'FCPdtMax'              => 'NULL', 
                    'FDPdtOrdStart'         => 'NULL', 
                    'FDPdtOrdStop'          => 'NULL', 
                    'FTXrdPdtKey'           => 'NULL', 
                    'FTPmhDocNoBill'        => 'NULL', 
                    'FTXrdPmhCpnDocNo'      => 'NULL', 
                    'FCXrdPmhCpnGetQty'     => 'NULL', 
                    'FCXrdPmhCpnValue'      => 'NULL', 
                    'FCXrdDisGP'            => 'NULL', 
                    'FCXrdPmtQtyGet'        => 'NULL',       
                    'FDDateUpd'             => date('Y-m-d'),
                    'FTTimeUpd'             => date('H:i:s'),
                    'FTWhoUpd'              => $_SESSION["SesUsername"],
                    'FDDateIns'             => date('Y-m-d'),
                    'FTTimeIns'             => date('H:i:s'),
                    'FTWhoIns'              => $_SESSION["SesUsername"]
                );

                $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                return 'success';
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMPURInsertPDTCaseBarcode] ".$e->getMessage());
        }
    }

    //Check Product ห้ามค่าซ้ำ ในกรณี เพื่มสินค้าแบบ คีย์เอง
    public function FSxMPURCheckProduct($pnCode,$ptDocument){
        $tSQL = "SELECT TOP 1 FTXrhDocNo FROM TACTPrDT WITH(NOLOCK) WHERE FTPdtCode = '$pnCode' AND FTXrhDocNo = '$ptDocument' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Get HD
    public function FSaMPURGetHD($pnDocumentNo){
        $tSQL = "SELECT 
                    HD.FTXrhDocNo ,
                    HD.FTXrhDisChgTxt ,
                    HD.FCXrhDis ,
                    CONVERT(VARCHAR(10),HD.FDXrhDocDate,103) as FDXrhDocDate ,
                    HD.FTWhoIns ,
                    HD.FTXrhRmk ,
                    REA.FTCutName ,
                    REA.FTCutCode ,
                    HD.FTXrhStaDoc , 
                    HD.FTXrhStaPrcDoc , 
                    HD.FTEdiDocNo ,
                    HD.FCXrhVATRate , 
                    HD.FTVatCode ,
                    HD.FTStyCode ,
                    HD.FTSplCode ,
                    HD.FTXrhDocType ,
                    CONVERT(VARCHAR(10),HD.FDEdiDate,103) as FDEdiDate ,
                    HD.FTEdiTime ,
                    CONVERT(VARCHAR(10),HD.FDXrhBchReturn,103) as FDXrhBchReturn ,
                    HD.FTXrhRefExt,
                    CONVERT(VARCHAR(10), HD.FDXrhRefExtDate,103) as FDXrhRefExtDate,
                    CONVERT(VARCHAR(10), HD.FDDateIns ,20) as FDDateIns
                FROM TACTPrHD HD WITH(NOLOCK)
                LEFT JOIN TCNMCutOff REA WITH(NOLOCK) ON HD.FTSpnCode = REA.FTCutCode";
        if($pnDocumentNo == ''){
            $tSQL .= "";
        }else{
            $tSQL .= " WHERE HD.FTXrhDocNo = '$pnDocumentNo' ";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Insert HD
    public function FSaMPURInsertHD($paData){
        try {   
            $aGetBranch   = getBranch();

            //Delete ก่อน 
            // $tDatabase          = "TACTPrHD";
            // $aDataDeleteWHERE   = array(
            //     'FTBchCode'    => $aGetBranch['FTBchCode'] ,
            //     'FTXrhDocNo'   => $paData['tDocumentNumber'] 
            // );
            // $bConfirm           = true;
            // $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            $tSQLChkData     = " SELECT FTXrhDocNo FROM TACTPrHD WITH(NOLOCK) WHERE FTXrhDocNo = '".$paData['tDocumentNumber']."' AND FTBchCode = '".$aGetBranch['FTBchCode']."' ";
            $tUpdOrInsResult = $this->DB_SELECT($tSQLChkData);
            

            $aDetailSPL = $this->FSaMPURGetDetailSupplier($paData['tSplCode'],0);
            
            if($aDetailSPL[0]['FTSplVATInOrEx'] == 1){ //รวมใน
                $tRoundBranch = $paData['tTypeRoundBranch'];
                if($tRoundBranch == 'PUR1'){  //ตามรอบ
                    $FCXrdVatReq    = $paData['nCalVat'];
                    $FCXrhVatable   = str_replace(",","",$paData['nCalBeforeDiscount']) - str_replace(",","",$paData['nCalVat']);
                }else if($tRoundBranch == 'PUR2'){ //ตามสาขา
                    //เดียว prorate มันจะไป update ใหม่
                    $tDocNo         = $paData['tDocumentNumber'];
                    $tSQLSUMVat     = "SELECT SUM(FCXrdVat) AS FCXrdVatReq FROM TACTPrDT WITH(NOLOCK) WHERE FTXrhDocNo = '$tDocNo' ";
                    $oSUMVat        = $this->DB_SELECT($tSQLSUMVat);
                    $FCXrdVatReq    = $oSUMVat[0]['FCXrdVatReq'];
                    $FCXrhVatable   = str_replace(",","",$paData['nCalBeforeDiscount']) - str_replace(",","",$paData['nVatValue']);  
                }
            }else{ //แยกนอก
                //เดียว prorate มันจะไป update ใหม่
                $FCXrdVatReq    = str_replace(",","",$paData['nCalBeforeDiscount']) * str_replace(",","",$paData['nVatValue']) / 100;
                $FCXrhVatable   = 0;
            }

            //วันที่ + กับ credit term
            $nCreditTerm    = '+' . $aDetailSPL[0]['FNSplCrTerm'] . ' days';
            $dCurrent       = date('Y-m-d');
            $dFDXrhDueDate  = date('Y-m-d', strtotime($nCreditTerm, strtotime($dCurrent)));

            //รหัสงวดบัญชี
            $tSQLPrdCode    = "SELECT TOP 1 FTPrdCode FROM TCNMAcPrd WITH(NOLOCK) WHERE CONVERT(VARCHAR(2),GETDATE(),101) BETWEEN CONVERT(VARCHAR(2),FDPrdStart,101) AND CONVERT(VARCHAR(2),FDPrdEnd,101)";
            $oPrdCode       = $this->DB_SELECT($tSQLPrdCode);

            //รหัสคลังตัดสต็อก
            $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WITH(NOLOCK) WHERE FTSysCode = 'AWahWhsPur'";
            $oWahCode       = $this->DB_SELECT($tSQLWahCode);

            //ประเภทผู้จำหน่าย
            $tRoundBranch   = $paData['tTypeRoundBranch'];
            if($tRoundBranch == 'PUR1'){ //ตามรอบ
                $FTXrhDocType   = 5;
                $FTXrhRefExt    = $paData['tNumberSend'];
                $FDXrhRefExtDate= $paData['tDateSend'];
                $FDXrhTnfDate   = $paData['tDateSend'];
                $FDXrhBillDue   = $paData['tDateSend'];  

            }else if($tRoundBranch == 'PUR2'){ //ตามสาขา
                $FTXrhDocType   = 6;
                $FTXrhRefExt    = $paData['tNumberSend'];
                $FDXrhRefExtDate= $paData['tDateSend'];
                $FDXrhTnfDate   = date('Y-m-d');
                $FDXrhBillDue   = date('Y-m-d');
            }

            //Cash or card
            if($aDetailSPL[0]['FNSplCrTerm'] == '' || $aDetailSPL[0]['FNSplCrTerm'] == null || $aDetailSPL[0]['FNSplCrTerm'] == 0){
                $FTXrhCshOrCrd = 1;
            }else{
                $FTXrhCshOrCrd = 2;
            }

            //สูตรหาEDI
            //Format:  XXXXXYYMMDDTZZRC
            $nX         = '0'.$aGetBranch['FTBchCode'];
            $nY         = date('y');
            $nM         = date('m');
            $nD         = date('d');
            $nT         = 1;
            $tEDI       = "SELECT TOP 1 FTEdiDocNo FROM TACTPrHD WITH(NOLOCK) WHERE FTEdiDocNo != '' order by FTXrhDocNo DESC";
            $oEDI       = $this->DB_SELECT($tEDI);
            if(empty($oEDI)){
                $nRunning = '01';
            }else{
                $nBaseM     = substr($oEDI[0]['FTEdiDocNo'], -9, 2);
                $nNewBaseM  = intval($nBaseM);
                $nNewM      = intval($nM);

                $nBaseD     = substr($oEDI[0]['FTEdiDocNo'], -7, 2);
                $nNewBaseD  = intval($nBaseD);
                $nNewD      = intval($nD);
                //'nNewM : ' . $nNewM . ' nNewBaseM : ' . $nNewBaseM . ' nNewD : ' . $nNewD . ' nNewBaseD : ' . $nNewBaseD;
                
                if($nNewM == $nNewBaseM && $nNewD == $nNewBaseD ){ //ยังอยู่ในวันเดียวกัน และเดือนเดียวกัน
                    $nRunning = substr($oEDI[0]['FTEdiDocNo'], -4, 2);
                    $nRunning = intval($nRunning);
                    if(strlen($nRunning) == 1){
                        $nRunning = $nRunning + 1;
                        $nRunning = "0".$nRunning;
                    }else{
                        $nRunning = $nRunning;
                    }
                }else{ //ข้ามวันแล้ว
                    $nRunning = '01';
                }
            }
            $nZ         = $nRunning;
            $nR         = 1;
            $nC         = 0;
            $FTEdiDocNo = $nX.$nY.$nM.$nD.$nT.$nZ.$nR.$nC;
            
            $tDatabase    = "TACTPrHD";
            if( empty($tUpdOrInsResult) ){
                //Insert
                $aDataInsert  = array(
                    'FTBchCode'         => $aGetBranch['FTBchCode'],
                    'FTXrhDocNo'        => $paData['tDocumentNumber'],
                    'FTXrhDocType'      => $FTXrhDocType, //ประเภทลดหนี้
                    'FDXrhDocDate'      => $paData['dDocDate'], /*date('Y-m-d')*/
                    'FTXrhDocTime'      => date('H:i:s'),
                    'FTXrhVATInOrEx'    => $aDetailSPL[0]['FTSplVATInOrEx'],
                    'FTStyCode'         => $paData['tStyCode'],
                    'FTDptCode'         => $_SESSION["SesUserDptCode"],
                    'FTUsrCode'         => $_SESSION["FTUsrCode"],
                    'FTSplCode'         => $paData['tSplCode'],
                    'FTCstCode'         => $paData['tSplCode'],
                    'FTAreCode'         => $aDetailSPL[0]['FTAreCode'],
                    'FTSpnCode'         => $paData['tReason'],
                    'FTPrdCode'         => $oPrdCode[0]['FTPrdCode'],
                    'FTWahCode'         => $oWahCode[0]['FTSysUsrValue'],
                    'FTXrhApvCode'      => $_SESSION["FTUsrCode"],
                    'FTShpCode'         => $aDetailSPL[0]['FTShpCode'],
                    'FNCspCode'         => 0,
                    'FNXrhCrTerm'       => $aDetailSPL[0]['FNSplCrTerm'],
                    'FDXrhDueDate'      => $dFDXrhDueDate,
                    'FTXrhRefExt'       => $FTXrhRefExt,
                    'FDXrhRefExtDate'   => $FDXrhRefExtDate,
                    'FTXrhRefInt'       => 'NULL',
                    'FDXrhRefIntDate'   => $FDXrhRefExtDate,
                    'FTXrhRefAE'        => 'NULL',
                    'FDXrhTnfDate'      => $FDXrhTnfDate,
                    'FDXrhBillDue'      => $FDXrhBillDue,
                    'FTXrhCtrName'      => 'NULL',
                    'FNXrhDocPrint'     => 0,
                    'FCXrhVATRate'      => str_replace(",","",$paData['nVatValue']),
                    'FTVatCode'         => $paData['tVatCode'],
                    'FCXrhTotal'        => str_replace(",","",$paData['nCalResult']),
                    'FCXrhTotalExcise'  => str_replace(",","",$paData['nCalResult']),
                    'FCXrhVatExcise'    => 0, //ยัง
                    'FCXrhNonVat'       => 0, //ยัง
                    'FCXrhB4DisChg'     => str_replace(",","",$paData['nCalResult']), 
                    'FTXrhDisChgTxt'    => $paData['tTextCalDiscount'],
                    'FCXrhDis'          => str_replace(",","",$paData['nCalDiscount']),
                    'FCXrhChg'          => 0,
                    'FCXrhAftDisChg'    => str_replace(",","",$paData['nCalBeforeDiscount']),
                    'FCXrhVat'          => str_replace(",","",$FCXrdVatReq),
                    'FCXrhVatable'      => str_replace(",","",$FCXrhVatable),
                    'FCXrhGrand'        => str_replace(",","",$paData['nCalNet']),
                    'FCXrhRnd'          => 0,
                    'FCXrhWpTax'        => 0,
                    'FCXrhReceive'      => str_replace(",","",$paData['nCalNet']),
                    'FCXrhChn'          => 0,
                    'FTXrhGndText'      => $paData['tTextCalculate'],
                    'FCXrhLeft'         => str_replace(",","",$paData['nCalNet']),
                    'FCXrhMnyCsh'       => 0,
                    'FCXrhMnyChq'       => 0,
                    'FCXrhMnyCrd'       => 0,
                    'FCXrhMnyCtf'       => 0,
                    'FCXrhMnyCpn'       => 0,
                    'FCXrhMnyCls'       => 0,
                    'FCXrhMnyCxx'       => 1,
                    'FCXrhGndCN'        => 0,
                    'FCXrhGndDN'        => 0,
                    'FCXrhGndAE'        => 0,
                    'FCXrhGndTH'        => 0,
                    'FTXrhStaPaid'      => 1,
                    'FTXrhStaRefund'    => 1,
                    'FTXrhStaType'      => 2,
                    'FTXrhStaDoc'       => 1,
                    'FTXrhStaPrcDoc'    => '',
                    'FTXrhStaPrcSpn'    => 'NULL',
                    'FTXrhStaPrcCst'    => 'NULL',
                    'FTXrhStaPrcGL'     => 'NULL',
                    'FTXrhStaPost'      => 'NULL',
                    'FTPjcCode'         => 'NULL',
                    'FTAloCode'         => 'NULL',
                    'FTCcyCode'         => 'NULL',
                    'FCXrhCcyExg'       => 0,
                    'FTPosCode'         => 'NULL',
                    'FTXrhPosCN'        => 'NULL',
                    'FTLogCode'         => 'NULL',
                    'FTXrhRmk'          => $paData['tReasonTextArea'],
                    'FNXrhSign'         => 0,
                    'FTXrhCshOrCrd'     => $FTXrhCshOrCrd,
                    'FCXrhPaid'         => 0,
                    'FTXrhDstPaid'      => $aDetailSPL[0]['FTSplTspPaid'],
                    'FTXbhDocNo'        => 'NULL',
                    'FTXphDocNo'        => 'NULL',
                    'FNXrhStaDocAct'    => 1,
                    'FNXrhStaRef'       => 0,
                    'FTXrhUsrEnter'     => 'NULL',
                    'FTXrhUsrPacker'    => 'NULL',
                    'FTXrhUsrChecker'   => 'NULL',
                    'FTXrhUsrSender'    => 'NULL',
                    'FTXrhTnfID'        => 'NULL',
                    'FTXrhVehID'        => 'NULL',
                    'FTDocControl'      => 'NULL',
                    'FTXrhStaPrcStk'    => 'NULL',
                    'FTXrhStaPrcLef'    => 'NULL',
                    'FTXrhStaVatType'   => 2,
                    'FTXrhStaVatSend'   => 1,
                    'FTXrhStaVatUpld'   => 'NULL',
                    'FTXrhDocVatFull'   => 'NULL',
                    'FTXqhDocNoRef'     => 'NULL',
                    'FTXrhRefSaleTax'   => 'NULL',
                    'FTCstStaClose'     => 'NULL',
                    'FTXrhBchFrm'       => 'NULL',
                    'FTXrhBchTo'        => 'NULL',
                    'FTXrhWahFrm'       => 'NULL',
                    'FTXrhWahTo'        => 'NULL',
                    'FTXrhCstName'      =>  $aDetailSPL[0]['FTSplName'],
                    'FTCstAddrInv'      =>  $aDetailSPL[0]['FTSplAddr'] . ' ถ.' . $aDetailSPL[0]['FTSplStreet'] . ' ต.' . $aDetailSPL[0]['FTSplDistrict'],
                    'FTCstStreetInv'    => 'NULL',
                    'FTCsttrictInv'     => 'NULL',
                    'FTDstCodeInv'      => 'NULL',
                    'FTPvnCodeInv'      => 'NULL',
                    'FTCstPostCodeInv'  => 'NULL',
                    'FCXrhDiscGP1'      => 'NULL',
                    'FCXrhDiscGP2'      => 'NULL',
                    'FCXrhB4VatAfGP1'   => 'NULL',
                    'FCXrhB4VatAfGP2'   => 'NULL',
                    'FTXrhDocRefMin'    => 'NULL',
                    'FTXrhDocRefMax'    => 'NULL',
                    'FTXrhStaJob'       => 'NULL',
                    'FDEdiDate'         => ($paData['tDocDate'] == 'null') ? 'NULL' : $paData['tDocDate'], 
                    'FTEdiTime'         => ($paData['tDocTime'] == 'null') ? date('H:i') : $paData['tDocTime'],
                    'FTEdiDocNo'        => $FTEdiDocNo,
                    'FTEdiStaRcvAuto'   => 'NULL',
                    'FDXrhBchAffect'    => 'NULL',
                    'FDXrhBchExpired'   => 'NULL',
                    'FDXrhBchReturn'    => ($paData['tDocDateReturn'] == 'null') ? 'NULL' : $paData['tDocDateReturn'],
                    'FNLogStaExport'    => 'NULL',
                    'FTPmhDocNoBill'    => 'NULL',
                    'FCXrhDisPmt'       => 'NULL',
                    'FTXrhCpnCodeRef'   => 'NULL',
                    'FCXrhCpnRcv'       => 'NULL',
                    'FCXrhRndMnyChg'    => 'NULL',
                    'FTXrhStaSavZero'   => 0,
                    'FDDateUpd'         => date('Y-m-d'),
                    'FTTimeUpd'         => date('H:i:s'),
                    'FTWhoUpd'          => $_SESSION["SesUsername"],
                    'FDDateIns'         => date('Y-m-d'),
                    'FTTimeIns'         => date('H:i:s'),
                    'FTWhoIns'          => $_SESSION["SesUsername"]
                );
                echo $this->DB_INSERT($tDatabase,$aDataInsert);
            }else{

                $FDEdiDate      = ($paData['tDocDate'] == 'null') ? NULL : $paData['tDocDate'];
                $FTEdiTime      = ($paData['tDocTime'] == 'null') ? date('H:i') : $paData['tDocTime'];
                $FDXrhBchReturn = ($paData['tDocDateReturn'] == 'null') ? NULL : $paData['tDocDateReturn'];
                // Update
                $tUpdateSql = " UPDATE ".$tDatabase." 
                                SET 
                                    FTXrhVATInOrEx    = '".$aDetailSPL[0]['FTSplVATInOrEx']."',
                                    FTStyCode         = '".$paData['tStyCode']."',
                                    FTDptCode         = '".$_SESSION["SesUserDptCode"]."',
                                    FTUsrCode         = '".$_SESSION["FTUsrCode"]."',
                                    FTSplCode         = '".$paData['tSplCode']."',
                                    FTCstCode         = '".$paData['tSplCode']."',
                                    FTAreCode         = '".$aDetailSPL[0]['FTAreCode']."',
                                    FTSpnCode         = '".$paData['tReason']."',
                                    FTPrdCode         = '".$oPrdCode[0]['FTPrdCode']."',
                                    FTWahCode         = '".$oWahCode[0]['FTSysUsrValue']."',
                                    FTXrhApvCode      = '".$_SESSION["FTUsrCode"]."',
                                    FTShpCode         = '".$aDetailSPL[0]['FTShpCode']."',
                                    FNXrhCrTerm       = ".$aDetailSPL[0]['FNSplCrTerm'].",
                                    FDXrhDueDate      = '".$dFDXrhDueDate."',
                                    FTXrhRefExt       = '".$FTXrhRefExt."',
                                    FDXrhRefExtDate   = '".$FDXrhRefExtDate."',
                                    FDXrhRefIntDate   = '".$FDXrhRefExtDate."',
                                    FDXrhTnfDate      = '".$FDXrhTnfDate."',
                                    FDXrhBillDue      = '".$FDXrhBillDue."',
                                    FCXrhVATRate      = ".str_replace(",","",$paData['nVatValue']).",
                                    FTVatCode         = '".$paData['tVatCode']."',
                                    FCXrhTotal        = ".str_replace(",","",$paData['nCalResult']).",
                                    FCXrhTotalExcise  = ".str_replace(",","",$paData['nCalResult']).",
                                    FCXrhB4DisChg     = ".str_replace(",","",$paData['nCalResult']).", 
                                    FTXrhDisChgTxt    = '".$paData['tTextCalDiscount']."',
                                    FCXrhDis          = ".str_replace(",","",$paData['nCalDiscount']).",
                                    FCXrhAftDisChg    = ".str_replace(",","",$paData['nCalBeforeDiscount']).",
                                    FCXrhVat          = ".str_replace(",","",$FCXrdVatReq).",
                                    FCXrhVatable      = ".str_replace(",","",$FCXrhVatable).",
                                    FCXrhGrand        = ".str_replace(",","",$paData['nCalNet']).",
                                    FCXrhReceive      = ".str_replace(",","",$paData['nCalNet']).",
                                    FTXrhGndText      = '".$paData['tTextCalculate']."',
                                    FCXrhLeft         = ".str_replace(",","",$paData['nCalNet']).",
                                    FTXrhRmk          = '".$paData['tReasonTextArea']."',
                                    FTXrhCshOrCrd     = '".$FTXrhCshOrCrd."',
                                    FTXrhDstPaid      = '".$aDetailSPL[0]['FTSplTspPaid']."',
                                    FTXrhCstName      = '".$aDetailSPL[0]['FTSplName']."',
                                    FTCstAddrInv      = '".$aDetailSPL[0]['FTSplAddr']." ถ.".$aDetailSPL[0]['FTSplStreet']." ต.".$aDetailSPL[0]['FTSplDistrict']."',
                                    FDEdiDate         = '".$FDEdiDate."', 
                                    FTEdiTime         = '".$FTEdiTime."',
                                    FTEdiDocNo        = '".$FTEdiDocNo."',
                                    FDXrhBchReturn    = '".$FDXrhBchReturn."',
                                    FDDateUpd         = '".date('Y-m-d')."',
                                    FTTimeUpd         = '".date('H:i:s')."',
                                    FTWhoUpd          = '".$_SESSION["SesUsername"]."'
                                WHERE FTXrhDocNo = '".$paData['tDocumentNumber']."' 
                                  AND FTBchCode = '".$aGetBranch['FTBchCode']."'
                              ";
                echo $this->DB_EXECUTE($tUpdateSql);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURInsertHD] ".$e->getMessage());
        }
    }

    //Delete Item DT 
    public function FSxMPURDeletePDT($paData){
        try {
            $pnSeq      = $paData['FNXrdSeqNo'];
            $pnPdtcode  = $paData['FTPdtCode'];
            $ptDocNo    = $paData['FTXrhDocNo'];
            $ptBchCode  = $paData['FTBchCode'];

            $tDatabase          = "TACTPrDT";
            $aDataDeleteWHERE   = array(
                'FNXrdSeqNo'    => $pnSeq ,
                'FTPdtCode'     => $pnPdtcode , 
                'FTXrhDocNo'    => $ptDocNo 
            );

            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
           
            //Update sequence
            $tUpdateSql = "UPDATE TACTPrDT 
                            SET FNXrdSeqNo = SeqNew.rtRowID
                            FROM (
                                SELECT c.* FROM( 
                                        SELECT  ROW_NUMBER() OVER(ORDER BY FNXrdSeqNo) AS rtRowID, FNXrdSeqNo , FTPdtCode FROM TACTPrDT 
                                        WHERE FTXrhDocNo = '$ptDocNo'
                                    ) as c 
                                ) SeqNew
                            WHERE 
                                SeqNew.FNXrdSeqNo = TACTPrDT.FNXrdSeqNo";
            $this->DB_EXECUTE($tUpdateSql);

            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMPURDeletePDT] ".$e->getMessage());
        }
    }

    // Delete All Item DT
    // Create By : Napat(Jame) 28/04/2020 
    public function FSxMPURDeleteAllPDT($ptDocNo){
        try {
            $tDatabase          = "TACTPrDT";
            $aDataDeleteWHERE   = array(
                'FTXrhDocNo'    => $ptDocNo 
            );

            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);

            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMPURDeleteAllPDT] ".$e->getMessage());
        }
    }

    //Check Document ถ้าเอกสารยังไม่สมบุรณ์ต้องดึงออกมาใช้งานต่อ
    public function FSaMPURCheckDocument($ptRoundBranch){
        if($ptRoundBranch == 'PUR1'){
            $FTXrhDocType = 5;
        }else if($ptRoundBranch == 'PUR2'){
            $FTXrhDocType = 6;
        }

        $tSQL = "SELECT TOP 1 
                    FTXrhDocNo , 
                    FTSplCode , 
                    FTStyCode , 
                    FTXrhStaDoc , 
                    FTXrhStaPrcDoc 
                FROM TACTPrHD WHERE FTXrhStaDoc = 1 AND FTXrhDocType = '$FTXrhDocType' AND (FTXrhStaPrcDoc = '' OR FTXrhStaPrcDoc = null OR FTXrhStaPrcDoc IS NULL ) ORDER BY FTXrhDocNo ASC";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Update
    public function FSaMPURUpdate($paData,$ptTypeUpdate){
        try {

            if($ptTypeUpdate == 1){
                $tQty           = $paData['FCXrdQty'];
            }else{
                $tQty           = 0;
            }

            $dUpd           = date('Y-m-d');
            $tTime          = date('H:i:s');
            $tWho           = $_SESSION["SesUsername"];
            $tSeq           = $paData['FNXrdSeqNo'];
            $tDoc           = $paData['FTXrhDocNo'];
            $FCXrdB4DisChg  = $paData['FCXrdB4DisChg'];
            $nVatRate       = $paData['nVatRate'];


            $tSQL   = "SELECT TOP 1  FCXrdSalePrice , FCXrdSetPrice , FCXrdStkFac , FCPdtLawControl , FTXrhVATInOrEx FROM TACTPrDT 
                        WHERE FNXrdSeqNo = '$tSeq' AND FTXrhDocNo = '$tDoc' ";
            $oQuery = $this->DB_SELECT($tSQL);
            $nNewNet = $oQuery[0]['FCXrdSalePrice'] * $tQty;

            if($oQuery[0]['FTXrhVATInOrEx'] == 1){ //รวมใน
                //Vat
                $FCXtdVat = $nNewNet - (($nNewNet * 100)/(100 + $nVatRate));
                    if($FCXtdVat != 0){
                        $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdVat = 0;
                    }

                //VatTable
                $FCXtdVatable = $nNewNet - $FCXtdVat;
                if($FCXtdVatable != 0){
                    $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                }else{
                    $FCXtdVatable = 0;
                }
            }else if($oQuery[0]['FTXrhVATInOrEx'] == 2){ //แยกนอก
                //Vat
                $FCXtdVat = ($nNewNet) * $nVatRate / 100;
                if($FCXtdVat != 0){
                    $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                }else{
                    $FCXtdVat = 0;
                }

                //VatTable
                $FCXtdVatable = $nNewNet;
                if($FCXtdVatable != 0){
                    $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                }else{
                    $FCXtdVatable = 0;
                }
            }

            //ภาษีสรรพสามิต
            $cUnitPrice     = $oQuery[0]['FCXrdSetPrice'] / $oQuery[0]['FCXrdStkFac'];
            if($oQuery[0]['FCPdtLawControl'] == 0){
                $FCXrdExcDuty = 0;
            }else{
                $FCXrdExcDuty = ($oQuery[0]['FCPdtLawControl'] - ($oQuery[0]['FCPdtLawControl'] * 100 ) / (100 + $nVatRate)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $nVatRate));
                $FCXrdExcDuty = round($FCXrdExcDuty,2) * $tQty;
            }

            $tSQLUpdate   = " UPDATE TACTPrDT SET 
                        FCXrdQty = '$tQty',
                        FCXrdQtyAll = '$tQty',
                        FCXrdQtyLef = '$tQty',
                        FCXrdNet = '$nNewNet',
                        FCXrdVat = '$FCXtdVat',
                        FCXrdVatable = '$FCXtdVatable',
                        FCXrdB4DisChg = '$FCXrdB4DisChg',
                        FCXrdExcDuty = '$FCXrdExcDuty',
                        FDDateUpd = '$dUpd',
                        FTTimeUpd = '$tTime',
                        FTWhoUpd = '$tWho'
                        WHERE FNXrdSeqNo = '$tSeq' AND 
                        FTXrhDocNo = '$tDoc' ";
            $tResult    = $this->DB_EXECUTE($tSQLUpdate);
            if($ptTypeUpdate == 1){
                return $tResult;
            }else{
                return;
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURUpdate] ".$e->getMessage());
        }
    }

    //Check Product Qty Ret
    public function FSaMPURChkPdtQtyRet($paData){
        $tSQL   = " SELECT FTSysUsrValue FROM TSysConfig WHERE FTSysCode='AChkReqCN' AND FTSysSeq= '001' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if($oQuery[0]['FTSysUsrValue'] != '0'){
            $tSQL   = " SELECT 
                            FCPdtQtyRet 
                        FROM TCNMPdt WITH(NOLOCK) 
                        WHERE FTPdtCode = '$paData[FTPdtCode]'
                        AND FCPdtQtyRet < '$paData[FCXrdQty]'
            ";
            $oQuery = $this->DB_SELECT($tSQL);
            if(count($oQuery) > 0){
                return $oQuery;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //Update PDT
    public function FSaMPURUpdatePDT($paData){
        try {
            $tSeq           = $paData['FNXrdSeqNo'];
            $tSPL           = $paData['tSPL'];
            $tDoc           = $paData['FTXrhDocNo'];
            $nVatRate       = $paData['nVatRate'];
            $nNewPDT        = $paData['NewPDT'];
            $tStyCode       = $paData['tStyCode'];

            echo $this->FSxMPURInsertPDTCaseBarcode($nNewPDT,$tDoc,$tSPL,$nVatRate,$tSeq,$tStyCode);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURUpdatePDT] ".$e->getMessage());
        }
    }

    //Calcualte
    public function FSaMPURCalculate($tDocumentID){
        $tSQL = "SELECT SUM(FCXrdNet) as nTotal FROM TACTPrDT WHERE FTXrhDocNo = $tDocumentID ";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Cancel document
    public function FSaMPURCancelDocument($tDocumentID){
        try {
            //check ใน HD ก่อน ถ้ามีเอกสารนี้เเล้ว ยกเลิก จะต้องไปอัพเดท 
            //แต่ถ้า check เเล้ว ไม่มีเอกสารให้ทำการลบ DT
            $tSQLCheckHD = "SELECT TOP 1 FTXrhDocNo FROM TACTPrHD 
                            WHERE FTXrhDocNo = '$tDocumentID' ";
            $oQueryHD = $this->DB_SELECT($tSQLCheckHD);
            if (!empty($oQueryHD)) {
                $dUpd         = date('Y-m-d');
                $tTime        = date('H:i:s');
                $tWho         = $_SESSION["SesUsername"];
                $tSQLUpdate   = " UPDATE TACTPrHD SET 
                            FTXrhStaDoc = '3',
                            FTXrhStaPrcDoc = '3',
                            FTEdiDocno = '',
                            FTEdiStaRcvAuto = '3',
                            FDDateUpd = '$dUpd',
                            FTTimeUpd = '$tTime',
                            FTWhoUpd = '$tWho'
                            WHERE FTXrhDocNo = '$tDocumentID' ";
                $tResult    = $this->DB_EXECUTE($tSQLUpdate);
                return $tResult;
            }else{
                $tDatabase          = "TACTPrDT";
                $aDataDeleteWHERE   = array(
                    'FTXrhDocNo'    => $tDocumentID 
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
                return $tResult;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURCancelDocument] ".$e->getMessage());
        }
    }

    //Approve
    public function FSaMPURApprove($paData){
        try {
            $tDocumentID    = $paData['tDocumentID'];
            $tType          = $paData['tType'];
            $tRefDocument   = $paData['tRefDocument'];
            $dUpd           = date('Y-m-d');
            $tTime          = date('H:i:s');
            $tWho           = $_SESSION["SesUsername"];

            //ถ้าเป็น PUR1 หรือ ตามรอบ จะต้องไป update เอกสารที่ ref มาว่าถูกใช้งานเเล้ว
            if($tType == 'PUR1'){
                $tSQLUpdateRefDoc = " UPDATE TACTPnHD SET 
                            FNXnhStaRef = 2,
                            FDDateUpd = '$dUpd',
                            FTTimeUpd = '$tTime',
                            FTWhoUpd = '$tWho'
                            WHERE FTXnhDocNo = '$tRefDocument' ";
                $this->DB_EXECUTE($tSQLUpdateRefDoc);
            }

            $tSQLUpdate = " UPDATE TACTPrHD SET 
                        FNXrhStaDocAct = 1,
                        FTXrhStaPrcDoc = 1,
                        FDDateUpd = '$dUpd',
                        FTTimeUpd = '$tTime',
                        FTWhoUpd = '$tWho'
                        WHERE FTXrhDocNo = '$tDocumentID' ";
            $tResult    = $this->DB_EXECUTE($tSQLUpdate);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURApprove] ".$e->getMessage());
        }
    }

    //Select [Document] HD
    public function FSxMPURListSearchSelectHD($paData){
        try {
            $aGetBranch     = getBranch();
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);

            $tSQL           = "SELECT c.* FROM( SELECT  ROW_NUMBER() OVER(ORDER BY ";
            $tSQL           .= " FTXrhDocNo DESC ";
            $tSQL           .= ") AS rtRowID,* FROM";
            $tSQL           .= "(SELECT 
                                    HD.FTXrhDocNo ,
                                    HD.FTXrhDisChgTxt ,
                                    HD.FCXrhDis ,
                                    HD.FTWhoIns ,
                                    HD.FTXrhRmk ,
                                    HD.FTXrhStaDoc , 
                                    HD.FTXrhStaPrcDoc , 
                                    HD.FTEdiDocNo ,
                                    HD.FCXrhVATRate , 
                                    HD.FTVatCode ,
                                    HD.FTStyCode ,
                                    HD.FTSplCode ,
                                    HD.FTXrhDocType ,
                                    CONVERT(VARCHAR(10),HD.FDEdiDate,103) as FDEdiDate ,
                                    HD.FTEdiTime ,
                                    CONVERT(VARCHAR(10),HD.FDXrhBchReturn,103) as FDXrhBchReturn ,
                                    HD.FTXrhRefExt,
                                    CONVERT(VARCHAR(10), HD.FDXrhRefExtDate,103) as FDXrhRefExtDate,
                                    CONVERT(VARCHAR(10), HD.FDDateIns ,121) as FDDateIns,
                                    HD.FTXrhDocTime ";
            $tSQL           .= " FROM TACTPrHD HD WITH (NOLOCK)  
                                 WHERE 1=1 ";
            $tSQL           .= " AND HD.FTXrhStaDoc <> '6' ";

            $tTextSearchPURReq = $paData['tTextSearchPURReq'];
            if($tTextSearchPURReq != '' || $tTextSearchPURReq != null){
                $tSQL           .= " AND FTXrhDocNo LIKE '%$tTextSearchPURReq%' ";
                $tSQL           .= " OR CONVERT(VARCHAR(10), HD.FDDateIns ,20) = '$tTextSearchPURReq' ";
            }

            $tSQL           .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";

            $oQuery         = $this->DB_SELECT($tSQL);

            if (!empty($oQuery) > 0) {
                $oList      = $oQuery;
                $aFoundRow  = $this->FSnMPURListSearchGetPageAll($paData);
                $nFoundRow  = $aFoundRow[0]['counts'];
                $nPageAll   = ceil($nFoundRow/$paData['nRow']); 
                    $aResult    = array(
                        'raItems'           => $oList,
                        'rnAllRow'          => $nFoundRow,
                        'rnCurrentPage'     => $paData['nPage'],
                        "rnAllPage"         => $nPageAll, 
                        'rtCode'            => '1',
                        'rtDesc'            => 'success',
                    );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }else{
                $aResult = array(
                    'rnAllRow'              => 0,
                    'rnCurrentPage'         => $paData['nPage'],
                    "rnAllPage"             => 0,
                    'rtCode'                => '800',
                    'rtDesc'                => 'data not found',
                );
                $jResult = json_encode($aResult);
                $aResult = json_decode($jResult, true);
            }
			
            return $aResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMPURListSearchSelectHD] ".$e->getMessage());
        }
    }

    //Select [Document] count DT
    public function FSnMPURListSearchGetPageAll($paData){
        
        $tSQL  = "SELECT COUNT (HD.FTXrhDocNo) AS counts
                 FROM TACTPrHD HD
                 WHERE 1=1 ";
        $tSQL .= " AND HD.FTXrhStaDoc <> '6' ";

        $tTextSearchPURReq = $paData['tTextSearchPURReq'];
        if($tTextSearchPURReq != '' || $tTextSearchPURReq != null){
            $tSQL           .= " AND FTXrhDocNo LIKE '%$tTextSearchPURReq%' ";
            $tSQL           .= " OR CONVERT(VARCHAR(10), HD.FDDateIns ,20) = '$tTextSearchPURReq' ";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //เช็คสินค้าใน DT ก่อน ถ้ามันมี แต่ใน HD ไม่มีต้องลบ DT ทิ้ง
    public function FSaMPURCheckPDTinHD(){
        $tSQL = "SELECT DT.FTXrhDocNo FROM (SELECT TOP 1 DT.FTXrhDocNo from TACTPrDT DT ORDER BY DT.FTXrhDocNo DESC ) AS DT 
                 INNER JOIN TACTPrHD HD ON DT.FTXrhDocNo = HD.FTXrhDocNo";
        $oQuery = $this->DB_SELECT($tSQL);
        if (empty($oQuery)) {
            //Delete DT order by DESC
            $tSQLDocno       = "SELECT TOP 1 DT.FTXrhDocNo from TACTPrDT DT ORDER BY DT.FTXrhDocNo DESC";
            $oQueryDocno     = $this->DB_SELECT($tSQLDocno);
            if(empty($oQueryDocno)){

            }else{
                $tDatabase          = "TACTPrDT";
                $aDataDeleteWHERE   = array(
                    'FTXrhDocNo'    => $oQueryDocno[0]['FTXrhDocNo']
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            }
        }else{
            return true;
        }
    }

    //----------------------- ถ้าเอกสารมี VAT สินค้ามากกว่าหนึ่ง --------------------------//
    //check ว่าเอกสารนี้มีสินค้ากี่ VAT TYPE มันจะต้อง split ออก
    public function FSaMPURCheckPDTByVatCode($tDocumentID){
        $tSQL = "SELECT DT.FTPdtCode , COUNTPDT FROM TACTPrDT DT LEFT JOIN ( 
                    SELECT FTXrhDocNo, COUNT(FTPdtCode) AS COUNTPDT FROM TACTPrDT 
                    WHERE FTXrhDocNo ='$tDocumentID'
                    GROUP BY FTXrhDocNo
                ) AS CPDT ON CPDT.FTXrhDocNo = DT.FTXrhDocNo
                WHERE DT.FTXrhDocNo ='$tDocumentID'";
        $oQuery = $this->DB_SELECT($tSQL);
        if(empty($oQuery)){
            $tPDTCode = '';
        }else{
            $nCount = $oQuery[0]['COUNTPDT'];
            $tPDTCode = '';
            for($i=0; $i<$nCount; $i++){
                $tPDTCode .= "'".$oQuery[$i]['FTPdtCode'] . "',";
            }
            $tPDTCode = substr($tPDTCode,0,-1);
        }

        //มีข้อมูล
        if($tPDTCode == '' || $tPDTCode == null){
            $tSQLFindVatCode = '';
        }else{
            $tSQLFindVatCode = "SELECT ROW_NUMBER() OVER(ORDER BY FTSplCode  DESC) AS rtRowID , C.* FROM ( 
                                    SELECT  DISTINCT FTVatCode,FTSplCode
                                    FROM TCNMPdt
                                    WHERE FTPdtCode in($tPDTCode) 
                                ) C ORDER BY rtRowID DESC";
            $oQuery = $this->DB_SELECT($tSQLFindVatCode);
        }
        return $oQuery;
    }

    //ถ้าเอกสารนี้มี สินค้าที่ VAT มีมากกว่าหนึ่ง ต้อง SELECT Into
    public function FSaMPURSelectintoHDDT($tDocumentID,$nVatCode){
        //เลขที่เอกสารจะต้อง +1 เสมอ
        $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
        $aFormatCode    = explode("PE",$tFormatCode);
        $tFormatCode    = 'PE0' . $aFormatCode[1];
        $tSPL           = $nVatCode['FTSplCode'];
        $nVatCodeHD     = $nVatCode['FTVatCode'];

		
        //เพิ่ม HD
        $tSQLHD = "INSERT INTO TACTPrHD 
                    SELECT 
                        FTBchCode , '$tFormatCode' , FTXrhDocType , FDXrhDocDate ,
                        FTXrhDocTime , FTXrhVATInOrEx , FTStyCode , FTDptCode ,
                        FTUsrCode , '$tSPL' , FTCstCode , FTAreCode , FTSpnCode ,
                        FTPrdCode , FTWahCode , FTXrhApvCode , FTShpCode , FNCspCode ,
                        FNXrhCrTerm , FDXrhDueDate , FTXrhRefExt , FDXrhRefExtDate ,
                        FTXrhRefInt , FDXrhRefIntDate , FTXrhRefAE , FDXrhTnfDate ,
                        FDXrhBillDue , FTXrhCtrName , FNXrhDocPrint , FCXrhVATRate ,
                        '$nVatCodeHD' , FCXrhTotal , FCXrhTotalExcise , FCXrhVatExcise ,
                        FCXrhNonVat , FCXrhB4DisChg , FTXrhDisChgTxt , FCXrhDis , FCXrhChg ,
                        FCXrhAftDisChg , FCXrhVat , FCXrhVatable , FCXrhGrand ,
                        FCXrhRnd , FCXrhWpTax , FCXrhReceive , FCXrhChn ,
                        FTXrhGndText , FCXrhLeft , FCXrhMnyCsh , FCXrhMnyChq ,
                        FCXrhMnyCrd , FCXrhMnyCtf , FCXrhMnyCpn , FCXrhMnyCls ,
                        FCXrhMnyCxx , FCXrhGndCN , FCXrhGndDN , FCXrhGndAE ,
                        FCXrhGndTH , FTXrhStaPaid , FTXrhStaRefund , FTXrhStaType ,
                        FTXrhStaDoc , FTXrhStaPrcDoc , FTXrhStaPrcSpn , FTXrhStaPrcCst ,
                        FTXrhStaPrcGL , FTXrhStaPost , FTPjcCode , FTAloCode ,
                        FTCcyCode , FCXrhCcyExg , FTPosCode , FTXrhPosCN ,
                        FTLogCode , FTXrhRmk , FNXrhSign , FTXrhCshOrCrd , FCXrhPaid ,
                        FTXrhDstPaid , FTXbhDocNo , FTXphDocNo , FNXrhStaDocAct , 
                        FNXrhStaRef , FTXrhUsrEnter , FTXrhUsrPacker , FTXrhUsrChecker ,
                        FTXrhUsrSender , FTXrhTnfID , FTXrhVehID , FTDocControl ,
                        FTXrhStaPrcStk , FTXrhStaPrcLef , FTXrhStaVatType , FTXrhStaVatSend ,
                        FTXrhStaVatUpld , FTXrhDocVatFull , FTXqhDocNoRef , FTXrhRefSaleTax ,
                        FTCstStaClose , FTXrhBchFrm , FTXrhBchTo , FTXrhWahFrm ,
                        FTXrhWahTo , FTXrhCstName , FTCstAddrInv , FTCstStreetInv ,
                        FTCsttrictInv , FTDstCodeInv , FTPvnCodeInv , FTCstPostCodeInv ,
                        FCXrhDiscGP1 , FCXrhDiscGP2 , FCXrhB4VatAfGP1 , FCXrhB4VatAfGP2 ,
                        FTXrhDocRefMin , FTXrhDocRefMax , FTXrhStaJob , FDEdiDate ,
                        FTEdiTime , FTEdiDocNo ,  FTEdiStaRcvAuto , FDXrhBchAffect ,
                        FDXrhBchExpired , FDXrhBchReturn , FNLogStaExport , FTPmhDocNoBill ,
                        FCXrhDisPmt , FTXrhCpnCodeRef , FCXrhCpnRcv , FCXrhRndMnyChg ,
                        FTXrhStaSavZero , FDDateUpd , FTTimeUpd , FTWhoUpd ,
                        FDDateIns , FTTimeIns , FTWhoIns 
                    FROM TACTPrHD WITH(NOLOCK)
                    WHERE FTXrhDocNo = '$tDocumentID' ";
        $oQuery = $this->DB_SELECT($tSQLHD);

        //เพิ่ม DT
        $tSQLDT = "INSERT INTO TACTPrDT 
                    SELECT 
                        FTBchCode , '$tFormatCode' , FNXrdSeqNo , FTPdtCode ,
                        FTPdtName , FTXrhDocType , FDXrhDocDate , FTXrhVATInOrEx ,
                        FTXrdBarCode , FTXrdStkCode , FCXrdStkFac , FTXrdVatType ,
                        FTXrdSaleType , FTPgpChain , FTSrnCode , FTPmhCode ,
                        FTPmhType , FTPunCode , FTXrdUnitName , FCXrdFactor ,
                        FCXrdSalePrice , FCXrdQty , FCXrdSetPrice , FCXrdB4DisChg ,
                        FTXrdDisChgTxt , FCXrdDis , FCXrdChg , FCXrdNet ,
                        FCXrdVat , FCXrdVatable , FCXrdQtyAll , FCXrdCostIn ,
                        FCXrdCostEx , FTXrdStaPdt , FTXrdStaRfd , FTXrdStaPrcStk ,
                        FNXrhSign , FTAccCode , FNXrdPdtLevel , FTXrdPdtParent ,
                        '$tSPL' , FTWahCode , FNXrdStaRef , FCXrdQtySet ,
                        FTPdtStaSet , FDXrdExpired , FTXrdLotNo , FCXrdQtyLef ,
                        FCXrdQtyRfn , FTXrhStaVatSend , FTPdtArticle , FTDcsCode ,
                        FTPszCode , FTClrCode , FTPszName , FTClrName ,
                        FCPdtLeftPO , FTCpnCode , FCXrdQtySale , FCXrdQtyRet ,
                        FCXrdQtyCN , FCXrdQtyAvi , FCXrdQtySgg , FTXrhBchFrm ,
                        FTXrhBchTo , FTXrhWahFrm , FTXrhWahTo , FCXrhDiscGP1 ,
                        FCXrhDiscGP2 , FCXrdB4VatAfGP1 , FCXrdB4VatAfGP2 , FCXrdDisShp ,
                        FCXrdShrDisShp , FTXrdTaxInv , FTPdtNoDis , FCXrdDisAvg ,
                        FCXrdFootAvg , FCXrdRePackAvg , FCPdtLawControl , FCXrdExcDuty ,
                        FTPdtSaleType , FCPdtMax , FDPdtOrdStart , FDPdtOrdStop ,
                        FTXrdPdtKey , FTPmhDocNoBill , FTXrdPmhCpnDocNo , FCXrdPmhCpnGetQty ,
                        FCXrdPmhCpnValue , FCXrdDisGP , FCXrdPmtQtyGet , FDDateUpd ,
                        FTTimeUpd , FTWhoUpd , FDDateIns , FTTimeIns , FTWhoIns
                    FROM TACTPrDT WITH(NOLOCK)
                    WHERE FTXrhDocNo = '$tDocumentID' ";
        $oQuery = $this->DB_SELECT($tSQLDT);

        //ถ้าเป็นตัวเเรก มันจะไม่ต้อง split มัน
        $tDocumentForDelete = $tFormatCode;

        //ลบ DT ที่ไม่ใช่ VAT ตัวเองออก
        $this->FSaMPURDeleteHDDT($tFormatCode,$nVatCode);
        return $tFormatCode;
    }

    //ลบรายการใน DT ที่ไม่ใช่ VAT ตัวเองออก และรัน SEQ ใหม่
    public function FSaMPURDeleteHDDT($tDocumentID,$nVatCode){
        $nVat       = $nVatCode['FTVatCode'];
		$tSPL       = $nVatCode['FTSplCode'];

        //ลบ VAT และ SPL ที่ไม่ใช่ของตัวเองออก
        // $tDELVatNotMyself = "DELETE DT FROM TACTPrDT DT 
        //     LEFT JOIN TCNMPDT PDT ON DT.FTPDTCode = PDT.FTPDTCode
        //     WHERE PDT.FTVatCode NOT IN ('$nVat') AND PDT.FTSplCode NOT IN ('$tSPL') AND DT.FTXrhDocNo = '$tDocumentID' ";
        $tDELVatNotMyself = "  DELETE TACTPrDT FROM TCNMPdt P WITH(NOLOCK)
                                INNER JOIN TACTPrDT DT ON DT.FTPdtCode = P.FTPdtCode AND DT.FTXrdStkCode = P.FTPdtStkCode
                                WHERE (P.FTVatCode != '$nVat' OR P.FTSplCode != '$tSPL')
                                AND DT.FTXrhDocNo = '$tDocumentID' 
        ";
		$this->DB_EXECUTE($tDELVatNotMyself);
        
        //รัน SEQ ใหม่
        $tUpdateSql = "UPDATE TACTPrDT 
            SET FNXrdSeqNo = SeqNew.rtRowID
            FROM (
                SELECT c.* FROM( 
                        SELECT  ROW_NUMBER() OVER(ORDER BY FNXrdSeqNo) AS rtRowID, FNXrdSeqNo , FTPdtCode FROM TACTPrDT 
                        WHERE TACTPrDT.FTXrhDocNo = '$tDocumentID' 
                    ) as c 
                ) SeqNew
            WHERE 
                SeqNew.FNXrdSeqNo = TACTPrDT.FNXrdSeqNo AND SeqNew.FTPdtCode = TACTPrDT.FTPdtCode AND TACTPrDT.FTXrhDocNo = '$tDocumentID' ";
        $this->DB_EXECUTE($tUpdateSql);

        //Update 
		$tUpdateSpl = "UPDATE TACTPrHD SET FTSplCode = '$tSPL' WHERE FTXrhDocNo = '$tDocumentID' ";
        $this->DB_EXECUTE($tUpdateSpl);
        
        $this->FSxMPURCalculateHDForVatCode($tDocumentID,$nVatCode);
    }

    //คำนวณรายการใน HD ใหม่ เอาเฉพาะของ VAT CODE ตัวเอง
    public function FSxMPURCalculateHDForVatCode($tDocumentID,$nVatCode){
        //Calculate พวกค่าต่างๆ 
        $tSQLResultCal = "SELECT 
                            C.* ,
                            CASE
                                WHEN C.FTXrhVatInorEx = 1 THEN ROUND(C.FCXrhAftDisChg - C.FCXrhVat ,2)
                                WHEN C.FTXrhVatInorEx = 2 THEN ROUND(C.FCXrhAftDisChg,2)
                                ELSE 0 
                            END AS FCXrhVatable ,
                            C.FCXrhAftDisChg AS FCXrhGrand , 
                            ROUND((C.FCXrhAftDisChg - C.FCXrhVat) * 3 / 100 ,2) AS FCXrhWpTax ,
                            C.FCXrhAftDisChg - ROUND((C.FCXrhAftDisChg - C.FCXrhVat) * 3 / 100 ,2) AS FCXrhReceive,
                            C.FCXrhAftDisChg AS FCXrhLeft
                        FROM (
                            SELECT 
                                HD.FTXrhVatInorEx,
                                SUM(DT.FCXrdNet) AS FCXrhTotal , 
                                SUM(DT.FCXrdNet) AS FCXrhTotalExcise ,
                                SUM(DT.FCXrdNet) AS FCXrhB4DisChg ,
                                SUM(DT.FCXrdNet) - HD.FCXrhDis AS FCXrhAftDisChg,
                                CASE
                                    WHEN HD.FTXrhVatInorEx = 1 THEN ROUND((SUM(DT.FCXrdNet) - HD.FCXrhDis)  - (((SUM(DT.FCXrdNet) - HD.FCXrhDis) * 100) / ( 100 + HD.FCXrhVATRate)),2)
                                    WHEN HD.FTXrhVatInorEx = 2 THEN ROUND(((SUM(DT.FCXrdNet) - HD.FCXrhDis) * HD.FCXrhVATRate /100 ),2) 
                                    ELSE 0 
                                END AS FCXrhVat 
                            FROM TACTPrDT DT
                            LEFT JOIN TACTPrHD HD ON DT.FTXrhDocNo = HD.FTXrhDocNo
                            WHERE DT.FTXrhDocNo = '$tDocumentID'
                            GROUP BY HD.FCXrhDis , HD.FTXrhVatInorEx , HD.FCXrhVATRate
                        ) C ";
        $oSQLResultCal = $this->DB_SELECT($tSQLResultCal);  

        $FCXrhTotal         = $oSQLResultCal[0]['FCXrhTotal'];
        $FCXrhTotalExcise   = $oSQLResultCal[0]['FCXrhTotalExcise'];
        $FCXrhB4DisChg      = $oSQLResultCal[0]['FCXrhB4DisChg'];
        $FCXrhAftDisChg     = $oSQLResultCal[0]['FCXrhAftDisChg'];
        $FCXrhVat           = $oSQLResultCal[0]['FCXrhVat'];
        $FCXrhVatable       = $oSQLResultCal[0]['FCXrhVatable'];
        $FCXrhGrand         = $oSQLResultCal[0]['FCXrhGrand'];
        // $FCXrhWpTax         = $oSQLResultCal[0]['FCXrhWpTax'];
        $FCXrhWpTax         = 0;
        $FCXrhReceive       = $oSQLResultCal[0]['FCXrhReceive'];
        $FTXrhGndText       = $this->bahtText($FCXrhGrand);
        $FCXrhLeft          = $oSQLResultCal[0]['FCXrhLeft'];
        $tUpdateSql  = "UPDATE TACTPrHD
                        SET FCXrhTotal = '$FCXrhTotal', 
                            FCXrhTotalExcise = '$FCXrhTotalExcise',
                            FCXrhB4DisChg = '$FCXrhB4DisChg',
                            FCXrhAftDisChg = '$FCXrhAftDisChg',
                            FCXrhVat = '$FCXrhVat',
                            FCXrhVatable = '$FCXrhVatable',
                            FCXrhGrand = '$FCXrhGrand',
                            FCXrhWpTax = '$FCXrhWpTax',
                            FCXrhReceive = '$FCXrhReceive',
                            FTXrhGndText = '$FTXrhGndText',
                            FCXrhLeft = '$FCXrhLeft'
                        WHERE FTXrhDocNo = '$tDocumentID'";
        $this->DB_EXECUTE($tUpdateSql);

        //คำนวณ prorate
        FCNaHCalculateProrate('TACTPrDT',$tDocumentID);
    }

    //Convert ราคาเป็น คำ
    public function bahtText(float $amount){
        [$integer, $fraction] = explode('.', number_format(abs($amount), 2, '.', ''));

        $baht = $this->convert($integer);
        $satang = $this->convert($fraction);

        $output = $amount < 0 ? 'ลบ' : '';
        $output .= $baht ? $baht.'บาท' : '';
        $output .= $satang ? $satang.'สตางค์' : 'ถ้วน';

        return $baht.$satang === '' ? 'ศูนย์บาทถ้วน' : $output;
    }

    //Convert ราคาเป็น คำ
    public function convert(string $number){
        $values = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
        $places = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        $exceptions = ['หนึ่งสิบ' => 'สิบ', 'สองสิบ' => 'ยี่สิบ', 'สิบหนึ่ง' => 'สิบเอ็ด'];

        $output = '';

        foreach (str_split(strrev($number)) as $place => $value) {
            if ($place % 6 === 0 && $place > 0) {
                $output = $places[6].$output;
            }

            if ($value !== '0') {
                $output = $values[$value].$places[$place % 6].$output;
            }
        }

        foreach ($exceptions as $search => $replace) {
            $output = str_replace($search, $replace, $output);
        }
        return $output;
    } 

    //----------------------------------- ตามรอบ -----------------------------------//
    //Select เอกสารที่ส่ง ใบ Pn
    public function FSaMPURGetDetailPu(){
        try {
            $tDptCode       = $_SESSION["SesUserDptCode"];
            $aGetBranch     = getBranch();
            $tGetBranch     = $aGetBranch['FTBchCode'];
            $tSQL = " SELECT  TOP  3000 TACTPnHD.FNXnhStaRef AS FBStaRef,
                        TACTPnHD.FTBchCode,
                        TACTPnHD.FTXnhDocNo,
                        CONVERT(VARCHAR(10),TACTPnHD.FDXnhDocDate,103) as FDXnhDocDate ,
                        TCNMSpl.FTSplName,
                        TCNMSpl.FTSplCode,
                        TACTPnHD.FTStyCode,
                        CONVERT(VARCHAR(10),TACTPnHD.FDXnhBchReturn,103) as FDXnhBchReturn 
                        FROM ((TACTPnHD 
                        INNER JOIN TCNMDepart ON TACTPnHD.FTDptCode = TCNMDepart.FTDptCode)  
                        INNER JOIN TCNMSpl ON TACTPnHD.FTSplCode = TCNMSpl.FTSplCode)  
                        INNER JOIN TSysUser ON TACTPnHD.FTUsrCode = TSysUser.FTUsrCode  
                        WHERE (TACTPnHD.FTXnhDocType IN ('5')) AND (TACTPnHD.FTXnhStaPrcDoc='1') 
                        AND (TACTPnHD.FTBchCode= '$tGetBranch') 
                        AND (TACTPnHD.FNXnhStaRef<>'2') 
                        AND  ( FTXnhStaPrcDoc  = '1' ) 
                        AND  ( FNXnhStaRef = 0 ) AND  ( FNXnhStaDocAct  = 1 ) AND  ( FTXnhStaDoc  = '1' )
                        AND ( TACTPnHD.FTDptCode = '$tDptCode' ) 
                        ORDER BY  FDXnhDocDate DESC , TACTPnHD.FTXnhDocNo DESC";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetDetailPu] ".$e->getMessage());
        }
    }

    //Select สินค้าในเอกสารที่ส่ง Pn
    public function FSaMPURGetDetailPDTByDocument($ptDocumentNumber){
        try {
            $tFTXnhDocNo    = $ptDocumentNumber;
            $aGetBranch     = getBranch();
            $tGetBranch     = $aGetBranch['FTBchCode'];
            $tSQL = " SELECT  0 AS FXSeqNo,
                        PnDT.FNXndSeqNo,
                        PnDT.FTPdtCode,
                        PnDT.FTPdtName,
                        PnDT.FTXndBarCode,
                        PnDT.FTSrnCode,
                        PnDT.FTXndUnitName,
                        PnDT.FCXndQty,
                        PnDT.FCXndSetPrice,
                        PnDT.FTXndDisChgTxt,
                        PnDT.FCXndNet,
                        PnHD.FCXnhVATRate,
                        PnHD.FTVatCode,
                        PnHD.FTXnhVATInOrEx,
                        PnHD.FTStyCode,
                        PnHD.FTSplCode,
                        PnHD.FTCstCode,
                        PnHD.FTAreCode,
                        PnHD.FTSpnCode,
                        PnHD.FTPrdCode,
                        PnHD.FTWahCode
                        FROM TACTPnDT PnDT
                        LEFT JOIN TACTPnHD PnHD ON PnDT.FTXnhDocNo = PnHD.FTXnhDocNo
                        WHERE  PnDT.FTBchCode ='$tGetBranch' AND 
                        PnDT.FTXnhDocNo = '$tFTXnhDocNo' 
                        ORDER BY PnDT.FNXndSeqNo";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetDetailPDTByDocument] ".$e->getMessage());
        }
    }

    //Insert PUR1
    public function FSaMPURInsertPDTByPUR1($tDocumentPN,$tDocumentID,$pnPDTCode,$pnKey){
        try {
            $aGetBranch     = getBranch();
            if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                $tFormatCode    = generateCode('TACTPrHD','FTXrhDocNo');
                $aFormatCode    = explode("PE",$tFormatCode);
                $tFormatCode    = 'PE0' . $aFormatCode[1];
            }else{
                $tFormatCode    = $tDocumentID;
            }

            //Insert DT
            $tDatabaseDT    = "TACTPrDT";
            //SELECT เอา seq มาก่อน
            // $tSQLseq        = "SELECT TOP 1 [FNXrdSeqNo] FROM TACTPrDT WHERE FTXrhDocNo = '$tFormatCode' order by FNXrdSeqNo DESC";
            // $tResultseq     = $this->DB_SELECT($tSQLseq);
            // if(empty($tResultseq)){
            //     $nSeq = 0;
            // }else{
            //     $nSeq = $tResultseq[0]['FNXrdSeqNo'];
            // }

            //SELECT จาก SPL
            $tSQLspl        = "SELECT PN.FTXnhDocNo , 
                                PN.FDXnhDocDate , 
                                PN.FTXnhDocTime , 
                                PN.FTStyCode , 
                                PN.FTDptCode , 
                                PN.FTSplCode ,
                                PN.FCXnhVATRate ,
                                PN.FTVatCode ,
                                PN.FTXnhVATInOrEx ,
                                SPL.FTAccCode ,
                                CONVERT(VARCHAR(10),PN.FDXnhDocDate,103) as FDXnhDocDate ,
                                CONVERT(VARCHAR(10),PN.FDXnhBchReturn,103) as FDXnhBchReturn 
                                FROM TACTPnHD PN
                                LEFT JOIN TCNMSpl SPL ON SPL.FTSplCode = PN.FTSplCode
                                WHERE PN.FTXnhDocNo = '$tDocumentPN' ";
            $tResultspl     = $this->DB_SELECT($tSQLspl);
            $nVatSpl        = $tResultspl[0]['FTXnhVATInOrEx'];
            $tFTAccCode     = $tResultspl[0]['FTAccCode'];
            $tSPLCode       = $tResultspl[0]['FTSplCode'];
            $tSPLType       = $tResultspl[0]['FTStyCode'];
            $tPNVatCode     = $tResultspl[0]['FTVatCode'];
            $tPNVatRate     = $tResultspl[0]['FCXnhVATRate'];
            $tFDXnhDocDate  = $tResultspl[0]['FDXnhDocDate'];
            $tFDXnhBchReturn= $tResultspl[0]['FDXnhBchReturn'];

            //ประเภทผู้จำหน่าย
            $FTXrhDocType   = 5;

            //รหัสคลังตัดสต็อก
            $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WHERE FTSysCode = 'AWahWhsPur'";
            $oWahCode       = $this->DB_SELECT($tSQLWahCode);

            //รายละเอียดสินค้า
            $tSQLPDT    = "SELECT TOP 1
                            TCNMPdt.FTPdtCode,
                            TCNMPdtBar.FTPdtBarCode,
                            TCNMPdt.FTPdtName,
                            TCNMPdt.FTPdtNameOth,
                            TCNMPdt.FTPdtNameShort,
                            TCNMPdtBar.FCPdtRetPri1,
                            TCNMPdt.FTPunCode,
                            TCNMPdtUnit.FTPunName,
                            TCNMPdt.FTSplCode,
                            TCNMPdt.FCPdtCostStd,
                            TCNMPdt.FTPdtStkCode,
                            TCNMPdt.FCPdtStkFac,
                            TCNMPdt.FTPdtVatType,
                            TCNMPdt.FTPgpChain,
                            TCNMPdt.FTPdtSaleType,
                            TCNMPdt.FTPdtStaSet,
                            TCNMPdt.FTPdtArticle,
                            TCNMPdt.FTDcsCode,
                            TCNMPdt.FTPszCode,
                            TCNMPdt.FTClrCode,
                            TCNMPdt.FTPdtNoDis,
                            TCNMPdt.FCPdtLawControl,
                            TACTPnDT.FNXndSeqNo,
                            TACTPnDT.FCXndQty,
                            TCNMPdt.FTPdtStaReturn
                            FROM TCNMPdt
                            LEFT JOIN TCNMPdtBar On TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode 
                            LEFT JOIN TACTPrDT ON TACTPrDT.FTPdtCode = TCNMPdt.FTPdtCode 
                            LEFT JOIN TCNMPdtUnit (NOLOCK) ON TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode 
                            LEFT JOIN TACTPnDT ON TCNMPdt.FTPdtCode = TACTPnDT.FTPdtCode
                            WHERE 1=1 
                            AND TCNMPdt.FTPdtType IN('1','4') 
                            AND TCNMPdt.FTPdtStaSet IN('1','2','3')
                            --AND TCNMPdt.FTPdtStaReturn IN('1')
                            AND TCNMPdt.FTPdtCode = '$pnPDTCode' 
                            AND TACTPnDT.FTXnhDocNo = '$tDocumentPN' ";
            $oPDTCode  = $this->DB_SELECT($tSQLPDT);

            if(!empty($oPDTCode) && $oPDTCode[0]['FTPdtStaReturn'] == '1'){
                //LOOP Insert for browser :: PDT
                for($i=0; $i<count($oPDTCode); $i++){
                
                    if($nVatSpl == 1){ //รวมใน

                        //Vat อันนี้ยังไม่ชัวร์ ใน 206 มันไม่มีค่า แต่อยากมีค่าเปิดชุดนี้
                        $FCXrdVat = (1 * $oPDTCode[$i]['FCPdtCostStd']) - ((1 * $oPDTCode[$i]['FCPdtCostStd'] * 100)/(100+ $tPNVatRate));
                        if($FCXrdVat != 0){
                            $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdVat = 0;
                        }
                        //$FCXrdVat = 0;

                        //VatTable อันนี้ยังไม่ชัวร์ ใน 206 มันไม่มีค่า แต่อยากมีค่าเปิดชุดนี้
                        $FCXrdVatable = (1 * $oPDTCode[$i]['FCPdtCostStd']) -  $FCXrdVat;
                        if($FCXrdVatable != 0){
                            $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdVatable = 0;
                        }
                        //$FCXrdVatable = 0;

                        //ราคาต้นทุน
                        $FCXrdCostIn = 1 * $oPDTCode[$i]['FCPdtCostStd'];
                        if($FCXrdCostIn != 0){
                            $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdCostIn = 0;
                        }

                        //ราคาแยกนอก
                        $FCXrdCostEx =  $FCXrdVatable;

                    }else{ //แยกนอก

                        //Vat
                        $FCXrdVat = (1 * $oPDTCode[$i]['FCPdtCostStd']) * $tPNVatRate / 100;
                        if($FCXrdVat != 0){
                            $FCXrdVat = round($FCXrdVat, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdVat = 0;
                        }

                        //VatTable
                        $FCXrdVatable = 1 * $oPDTCode[$i]['FCPdtCostStd'];
                        if($FCXrdVatable != 0){
                            $FCXrdVatable = round($FCXrdVatable, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdVatable = 0;
                        }

                        //ราคาต้นทุน
                        $FCXrdCostIn = (1 * $oPDTCode[$i]['FCPdtCostStd']) + $FCXrdVat;
                        if($FCXrdCostIn != 0){
                            $FCXrdCostIn = round($FCXrdCostIn, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXrdCostIn = 0;
                        }

                        //ราคาแยกนอก
                        $FCXrdCostEx = $oPDTCode[$i]['FCPdtCostStd'];
                    }

                    //ภาษีสรรพสามิต
                    $cUnitPrice     = $oPDTCode[$i]['FCPdtCostStd'] / $oPDTCode[$i]['FCPdtStkFac'];
                    if($oPDTCode[$i]['FCPdtLawControl'] == 0){
                        $FCXrdExcDuty = 0;
                    }else{
                        $FCXrdExcDuty = ($oPDTCode[$i]['FCPdtLawControl'] - ($oPDTCode[$i]['FCPdtLawControl'] * 100 ) / (100 + $tPNVatRate)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $tPNVatRate));
                        $FCXrdExcDuty = round($FCXrdExcDuty,2);
                    }

                    $aDataInsertDT  = array(
                        'FTBchCode'             => $aGetBranch['FTBchCode'],
                        'FTXrhDocNo'            => $tFormatCode,
                        'FNXrdSeqNo'            => $pnKey+1,
                        'FTPdtCode'             => $oPDTCode[$i]['FTPdtCode'],
                        'FTPdtName'             => $oPDTCode[$i]['FTPdtName'],
                        'FTXrhDocType'          => $FTXrhDocType,
                        'FDXrhDocDate'          => date('Y-m-d'),
                        'FTXrhVATInOrEx'        => $nVatSpl,
                        'FTXrdBarCode'          => $oPDTCode[$i]['FTPdtBarCode'], 
                        'FTXrdStkCode'          => $oPDTCode[$i]['FTPdtStkCode'],
                        'FCXrdStkFac'           => $oPDTCode[$i]['FCPdtStkFac'],
                        'FTXrdVatType'          => $oPDTCode[$i]['FTPdtVatType'],
                        'FTXrdSaleType'         => $oPDTCode[$i]['FTPdtSaleType'],
                        'FTPgpChain'            => $oPDTCode[$i]['FTPgpChain'],   
                        'FTSrnCode'             => 'NULL',
                        'FTPmhCode'             => 'NULL',
                        'FTPmhType'             => 'NULL', 
                        'FTPunCode'             => $oPDTCode[$i]['FTPunCode'],      
                        'FTXrdUnitName'         => $oPDTCode[$i]['FTPunName'],
                        'FCXrdFactor'           => $oPDTCode[$i]['FCPdtStkFac'],
                        'FCXrdSalePrice'        => $oPDTCode[$i]['FCPdtRetPri1'],
                        'FCXrdQty'              => $oPDTCode[$i]['FCXndQty'],
                        'FCXrdSetPrice'         => $oPDTCode[$i]['FCPdtCostStd'],
                        'FCXrdB4DisChg'         => $oPDTCode[$i]['FCXndQty'] * $oPDTCode[$i]['FCPdtCostStd'],
                        'FTXrdDisChgTxt'        => '',
                        'FCXrdDis'              => '',
                        'FCXrdChg'              => '',
                        'FCXrdNet'              => $oPDTCode[$i]['FCXndQty'] * $oPDTCode[$i]['FCPdtRetPri1'], //เปลี่ยนจาก FCXrdSalePrice เป็น FCPdtRetPri1
                        'FCXrdVat'              => $FCXrdVat,
                        'FCXrdVatable'          => $FCXrdVatable,
                        'FCXrdQtyAll'           => 0,
                        'FCXrdCostIn'           => $FCXrdCostIn,
                        'FCXrdCostEx'           => $FCXrdCostEx,
                        'FTXrdStaPdt'           => 1,
                        'FTXrdStaRfd'           => 1,
                        'FTXrdStaPrcStk'        => 1,      
                        'FNXrhSign'             => 0,
                        'FTAccCode'             => $tFTAccCode,
                        'FNXrdPdtLevel'         => 0,     
                        'FTXrdPdtParent'        => $oPDTCode[$i]['FTPdtCode'],
                        'FTXrdApOrAr'           => $oPDTCode[$i]['FTSplCode'],
                        'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                        'FNXrdStaRef'           => 0,
                        'FCXrdQtySet'           => 'NULL', 
                        'FTPdtStaSet'           => $oPDTCode[$i]['FTPdtStaSet'],
                        'FDXrdExpired'          => 'NULL',  
                        'FTXrdLotNo'            => 1,
                        'FCXrdQtyLef'           => $oPDTCode[$i]['FCXndQty'],
                        'FCXrdQtyRfn'           => 'NULL', 
                        'FTXrhStaVatSend'       => 1,
                        'FTPdtArticle'          => $oPDTCode[$i]['FTPdtArticle'],
                        'FTDcsCode'             => $oPDTCode[$i]['FTDcsCode'],
                        'FTPszCode'             => $oPDTCode[$i]['FTPszCode'],
                        'FTClrCode'             => $oPDTCode[$i]['FTClrCode'],
                        'FTPszName'             => 'NULL', 
                        'FTClrName'             => 'NULL', 
                        'FCPdtLeftPO'           => 'NULL', 
                        'FTCpnCode'             => 'NULL', 
                        'FCXrdQtySale'          => 'NULL', 
                        'FCXrdQtyRet'           => 'NULL', 
                        'FCXrdQtyCN'            => 'NULL', 
                        'FCXrdQtyAvi'           => 'NULL', 
                        'FCXrdQtySgg'           => 'NULL', 
                        'FTXrhBchFrm'           => 'NULL',       
                        'FTXrhBchTo'            => 'NULL',       
                        'FTXrhWahFrm'           => 'NULL', 
                        'FTXrhWahTo'            => 'NULL', 
                        'FCXrhDiscGP1'          => 'NULL', 
                        'FCXrhDiscGP2'          => 'NULL', 
                        'FCXrdB4VatAfGP1'       => 'NULL', 
                        'FCXrdB4VatAfGP2'       => 'NULL', 
                        'FCXrdDisShp'           => 'NULL', 
                        'FCXrdShrDisShp'        => 'NULL', 
                        'FTXrdTaxInv'           => 'NULL', 
                        'FTPdtNoDis'            => $oPDTCode[$i]['FTPdtNoDis'],
                        'FCXrdDisAvg'           => 'NULL', 
                        'FCXrdFootAvg'          => 'NULL', 
                        'FCXrdRePackAvg'        => 'NULL', 
                        'FCPdtLawControl'       => $oPDTCode[$i]['FCPdtLawControl'],
                        'FCXrdExcDuty'          => $FCXrdExcDuty,
                        'FTPdtSaleType'         => $oPDTCode[$i]['FTPdtSaleType'],
                        'FCPdtMax'              => 'NULL', 
                        'FDPdtOrdStart'         => 'NULL', 
                        'FDPdtOrdStop'          => 'NULL', 
                        'FTXrdPdtKey'           => 'NULL', 
                        'FTPmhDocNoBill'        => 'NULL', 
                        'FTXrdPmhCpnDocNo'      => 'NULL', 
                        'FCXrdPmhCpnGetQty'     => 'NULL', 
                        'FCXrdPmhCpnValue'      => 'NULL', 
                        'FCXrdDisGP'            => 'NULL', 
                        'FCXrdPmtQtyGet'        => 'NULL',       
                        'FDDateUpd'             => date('Y-m-d'),
                        'FTTimeUpd'             => date('H:i:s'),
                        'FTWhoUpd'              => $_SESSION["SesUsername"],
                        'FDDateIns'             => date('Y-m-d'),
                        'FTTimeIns'             => date('H:i:s'),
                        'FTWhoIns'              => $_SESSION["SesUsername"]
                    );
                    $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                }
            }
            $aReturnData = [$tFormatCode,$tSPLCode,$tSPLType,$tFDXnhDocDate,$oPDTCode[0]['FTPdtBarCode'],$oPDTCode[0]['FTPdtName'],$oPDTCode[0]['FTPdtStaReturn']];
            return $aReturnData;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURInsertPDTByPUR1] ".$e->getMessage());
        }
    }

    //---------------------------------- Rabbit fail -------------------------------//
    public function FSaMPURRabbitFailUpdateFlag($tDocumentID){
        $dUpd           = date('Y-m-d');
        $tTime          = date('H:i:s');
        $tWho           = $_SESSION["SesUsername"];

        $tSQLUpdate = " UPDATE TACTPrHD SET 
            FNXrhStaDocAct = 1,
            FTXrhStaPrcDoc = '',
            FDDateUpd = '$dUpd',
            FTTimeUpd = '$tTime',
            FTWhoUpd = '$tWho'
            WHERE FTXrhDocNo = '$tDocumentID' ";
        $tResult    = $this->DB_EXECUTE($tSQLUpdate);
        return $tResult;
    }

    //Create By Napat(Jame) 12/03/63
    //Function Get Reason Code in Config
    public function FSaMPURGetConfigReason(){
        try {
            $tSQL = "   SELECT TOP 1
                            CASE WHEN ISNULL(FTSysUsrValue,'') = '' THEN FTSysDefValue ELSE FTSysUsrValue END AS FTSysValue
                        FROM TSysConfig WITH(NOLOCK) 
                        WHERE FTSysCode = 'AlwPeDoc'
                    ";

            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery[0];
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSaMPURGetConfigReason] ".$e->getMessage());
        }
    }

    // Create By : Napat(Jame) 28/04/63
    public function FSnMPURGetLastSeqDT($ptDocNo){
        try {
            $tSQL = "   SELECT 
                            MAX(FNXrdSeqNo) AS nLastSeq 
                        FROM TACTPrDT WITH(NOLOCK) 
                        WHERE FTXrhDocNo = '$ptDocNo'
                    ";

            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery[0]['nLastSeq'];
            }else{
                return 0;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSnMPURGetLastSeqDT] ".$e->getMessage());
        }
    }

    // ตรวจสอบว่าสินค้าทุกตัวมี SplCode จริงหรือไม่ ? Comsheet 2020-243
    // Create By : Napat(Jame) 01/06/63
    public function FSaMPURChkSplCodeInDT($ptDocNo){
        try {
            $tSQL = "   SELECT 
                            SPL.FTSplCode	AS FTSplCode,
                            PR.FTXrdBarCode AS FTPdtBarCode,
                            PR.FTPdtName	AS FTPdtName
                        FROM TCNMPdt PDT WITH(NOLOCK)
                        INNER JOIN TACTPrDT PR WITH(NOLOCK) ON PR.FTPdtCode = PDT.FTPdtCode AND PR.FTXrdStkCode = PDT.FTPdtStkCode
                        LEFT JOIN TCNMSpl SPL WITH(NOLOCK) ON PDT.FTSplCode = SPL.FTSplCode
                        WHERE PR.FTXrhDocNo = '$ptDocNo'
                            AND ISNULL(SPL.FTSplCode,'') = ''
                    ";

            $oQuery = $this->DB_SELECT($tSQL);
            if(!empty($oQuery)){
                $aReturn = array(
                    'nStaReturn'    => 1,
                    'tMsgReturn'    => 'Found Data',
                    'aDataReturn'   => $oQuery
                );
            }else{
                $aReturn = array(
                    'nStaReturn'    => 99,
                    'tMsgReturn'    => 'Not Found Data',
                    'aDataReturn'   => array()
                );
            }
        }catch(Exception $e) {
            $aReturn = array(
                'nStaReturn'    => 99,
                'tMsgReturn'    => $e->getMessage(),
                'aDataReturn'   => array()
            );
            $this->FSxWriteLogByPage("[FSnMPURGetLastSeqDT] ".$e->getMessage());
        }
        return $aReturn;
    }

    // Comsheet/2022-036
    // Napat(Jame) 07/09/2022 อัพเดทข้อมูล Spl หลังจาก Split เสร็จ
    public function FSaMPURUpdateHDSpl($ptMultiDocument){
        $tMultiDocument = "'".str_replace(",","','",$ptMultiDocument)."'";
        $tSQL = "   UPDATE HD
                    SET HD.FTCstCode = SPL.FTSplCode, HD.FTXrhCstName = SPL.FTSplName, 
                        HD.FTCstAddrInv = SPL.FTSplAddr+' ถ.'+SPL.FTSplStreet+' ต.'+SPL.FTSplDistrict,
                        HD.FTXrhDstPaid = SPL.FTSplTspPaid, HD.FTXrhVATInOrEx = SPL.FTSplVATInOrEx,
                        HD.FTAreCode = SPL.FTAreCode, HD.FTShpCode = SPL.FTShpCode, HD.FNXrhCrTerm = SPL.FNSplCrTerm
                    FROM TACTPrHD HD WITH(NOLOCK) 
                    INNER JOIN TCNMSpl SPL WITH(NOLOCK) ON HD.FTSplCode = SPL.FTSplCode
                    WHERE HD.FTXrhDocNo IN ($tMultiDocument) ";
        $this->DB_EXECUTE($tSQL);
    }

}

?>