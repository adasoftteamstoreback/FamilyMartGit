<?php

class mpurcn extends Database{

    public function __construct(){
        parent::__construct();
    }

    //Get Type Supplier
    public function FSaMPURGetTypeSupplier(){
        try {
            $tSQL = "SELECT FTStyCode,FTStyName 
                     FROM TCNMSplType 
                     WHERE ((TCNMSplType.FTStyCode)<>'0') ORDER BY FTStyCode";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
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
                        FROM TCNMSpl 
                        WHERE 1=1 AND FTStyCode = '$pnCode' ";

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
        }
    }

    //Get page Supplier
    public function FSnMPURGetTypePageAll($paData){
        $pnCode     = $paData['pnSupCode'];
        $tSQL       = "SELECT COUNT (FTSplCode) AS counts
                        FROM TCNMSpl
                        WHERE 1=1 AND FTStyCode = '$pnCode' ";
                
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
        $tSQL       = "SELECT TOP 1
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
                        FROM TCNMSpl SPL
                        LEFT JOIN TCNMDistrict DST on SPL.FTDstCode = DST.FTDstCode 
                        LEFT JOIN TCNMProvince PRV on SPL.FTPvnCode = PRV.FTPvnCode 
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
            $tSQL    .= " FNXtdSeqNo ";
            $tSQL    .= ") AS rtRowID,* FROM";
            $tSQL    .= "( SELECT 
                        DT.FTBchCode,
                        DT.FTXthDocNo,
                        DT.FNXtdSeqNo,
                        DT.FTPdtCode,
                        DT.FTPdtName,
                        DT.FTXtdBarCode,
                        DT.FTXtdApOrAr,
                        DT.FTXthDocType,
                        DT.FTXtdUnitName,
                        DT.FCXtdQty,
                        DT.FCXtdSalePrice,
                        DT.FCXtdSetPrice,
                        DT.FCXtdNet,
                        DT.FTPunCode,
                        PU.FTPunName,
                        HD.FTXthStaPrcDoc,
                        HD.FTXthStaDoc
                        FROM TACTPtDT DT
                        LEFT JOIN TCNMPdtUnit PU (NOLOCK) ON DT.FTPunCode = PU.FTPunCode
                        LEFT JOIN TACTPtHD HD (NOLOCK) ON DT.FTXthDocNo = HD.FTXthDocNo
                        WHERE 1=1 AND DT.FTXthDocNo = '$pnCode' ";
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
        }
    }

    //Get page Product [DT]
    public function FSnMPURGetProductPageAll($paData){
        $pnCode     = $paData['tDocumentID'];
        $tSQL       = "SELECT COUNT (FTPdtCode) AS counts
                        FROM TACTPtDT
                        WHERE 1=1 AND FTXthDocNo = '$pnCode' ";
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
                $tDatabase          = "TACTPtDT";
                $aDataDeleteWHERE   = array(
                    'FNXtdSeqNo'    => $nParamSeq ,
                    'FTXthDocNo'    => $tDocumentID 
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            }

            $aGetBranch     = getBranch();
            if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
                $aFormatCode    = explode("PC",$tFormatCode);
                $tFormatCode    = 'PC0' . $aFormatCode[1];
            }else{
                $tFormatCode    = $tDocumentID;
            }
            
            //insert DT
            $tDatabaseDT    = "TACTPtDT";
            if(!empty($ptParameter)){

                //SELECT เอา seq มาก่อน
                $tSQLseq        = "SELECT TOP 1 [FNXtdSeqNo] FROM TACTPtDT WHERE FTXthDocNo = '$tFormatCode' order by FNXtdSeqNo DESC";
                $tResultseq     = $this->DB_SELECT($tSQLseq);
                if(empty($tResultseq)){
                    $nSeq = 0;
                }else{
                    $nSeq = $tResultseq[0]['FNXtdSeqNo'];
                }

                //SELECT จาก SPL
                $tSPLCode       = $aPackDataInsert['tSPLCode'];
                $tSQLspl        = "SELECT TOP 1 FTSplVATInOrEx , FTAccCode FROM TCNMSpl WHERE FTSplCode = '$tSPLCode'";
                $tResultspl     = $this->DB_SELECT($tSQLspl);
                $nVatSpl        = $tResultspl[0]['FTSplVATInOrEx'];
                $tFTAccCode     = $tResultspl[0]['FTAccCode'];

                //ประเภทผู้จำหน่าย
                $tRoundBranch   = $aPackDataInsert['tTypeRoundBranch'];
                $FTXthDocType   = 5;
                
                //รหัสคลังตัดสต็อก
                $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WHERE FTSysCode = 'AWahWhsPur'";
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
                            $FCXtdVat = (1 * $ptParameter[$i]['FCPdtCostStd']) - ((1 * $ptParameter[$i]['FCPdtCostStd'] * 100)/(100+ $aPackDataInsert['nSelectValueVat']));
                            if($FCXtdVat != 0){
                                $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdVat = 0;
                            }

                            //VatTable
                            $FCXtdVatable = (1 * $ptParameter[$i]['FCPdtCostStd']) -  $FCXtdVat;
                            if($FCXtdVatable != 0){
                                $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdVatable = 0;
                            }

                            //ราคาต้นทุน
                            $FCXtdCostIn = 1 * $ptParameter[$i]['FCPdtCostStd'];
                            if($FCXtdCostIn != 0){
                                $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdCostIn = 0;
                            }

                            //ราคาแยกนอก
                            $FCXtdCostEx =  $FCXtdVatable;

                        }else{ //แยกนอก

                            //Vat
                            $FCXtdVat = (1 * $ptParameter[$i]['FCPdtCostStd']) * $aPackDataInsert['nSelectValueVat'] / 100;
                            if($FCXtdVat != 0){
                                $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdVat = 0;
                            }

                            //VatTable
                            $FCXtdVatable = 1 * $ptParameter[$i]['FCPdtCostStd'];
                            if($FCXtdVatable != 0){
                                $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdVatable = 0;
                            }

                            //ราคาต้นทุน
                            $FCXtdCostIn = (1 * $ptParameter[$i]['FCPdtCostStd']) + $FCXtdVat;
                            if($FCXtdCostIn != 0){
                                $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                            }else{
                                $FCXtdCostIn = 0;
                            }

                            //ราคาแยกนอก
                            $FCXtdCostEx = $ptParameter[$i]['FCPdtCostStd'];
                        }

                        //ภาษีสรรพสามิต
                        $cUnitPrice     = $ptParameter[$i]['FCPdtCostStd'] / $ptParameter[$i]['FCPdtStkFac'];
                        if($ptParameter[$i]['FCPdtLawControl'] == 0){
                            $FCXtdExcDuty = 0;
                        }else{
                            $FCXtdExcDuty = ($ptParameter[$i]['FCPdtLawControl'] - ($ptParameter[$i]['FCPdtLawControl'] * 100 ) / (100 + $aPackDataInsert['nSelectValueVat'])) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $aPackDataInsert['nSelectValueVat']));
                            $FCXtdExcDuty = round($FCXtdExcDuty,2);
                        }

                        $aDataInsertDT  = array(
                            'FTBchCode'             => $aGetBranch['FTBchCode'],
                            'FTXthDocNo'            => $tFormatCode,
                            'FNXtdSeqNo'            => $nSeq,
                            'FTPdtCode'             => $ptParameter[$i]['FTPdtCode'],
                            'FTPdtName'             => $ptParameter[$i]['FTPdtName'],
                            'FTXthDocType'          => $FTXthDocType,
                            'FDXthDocDate'          => $aPackDataInsert['dDocDate'], /*date('Y-m-d')*/
                            'FTXthVATInOrEx'        => $nVatSpl,
                            'FTXtdBarCode'          => $ptParameter[$i]['FTPdtBarCode'], 
                            'FTXtdStkCode'          => $ptParameter[$i]['FTPdtStkCode'],
                            'FCXtdStkFac'           => $ptParameter[$i]['FCPdtStkFac'],
                            'FTXtdVatType'          => $ptParameter[$i]['FTPdtVatType'],
                            'FTXtdSaleType'         => $ptParameter[$i]['FTPdtSaleType'],
                            'FTPgpChain'            => $ptParameter[$i]['FTPgpChain'],   
                            'FTSrnCode'             => 'NULL',
                            'FTPmhCode'             => 'NULL',
                            'FTPmhType'             => 'NULL', 
                            'FTPunCode'             => $ptParameter[$i]['FTPunCode'],      
                            'FTXtdUnitName'         => $ptParameter[$i]['FTPunName'],
                            'FCXtdFactor'           => $ptParameter[$i]['FCPdtStkFac'],
                            'FCXtdSalePrice'        => $ptParameter[$i]['FCPdtRetPri1'],
                            'FCXtdQty'              => 0,
                            'FCXtdSetPrice'         => $ptParameter[$i]['FCPdtCostStd'],
                            'FCXtdB4DisChg'         => 1 * $ptParameter[$i]['FCPdtCostStd'],
                            'FTXtdDisChgTxt'        => '',
                            'FCXtdDis'              => '',
                            'FCXtdChg'              => '',
                            'FCXtdNet'              => 1 * $ptParameter[$i]['FCPdtCostStd'],
                            'FCXtdVat'              => $FCXtdVat,
                            'FCXtdVatable'          => $FCXtdVatable,
                            'FCXtdQtyAll'           => 1,
                            'FCXtdCostIn'           => $FCXtdCostIn,
                            'FCXtdCostEx'           => $FCXtdCostEx,
                            'FTXtdStaPdt'           => 1,
                            'FTXtdStaRfd'           => 1,
                            'FTXtdStaPrcStk'        => 'NULL',      
                            'FNXthSign'             => 0,
                            'FTAccCode'             => $tFTAccCode,
                            'FNXtdPdtLevel'         => 0,     
                            'FTXtdPdtParent'        => $ptParameter[$i]['FTPdtCode'],
                            'FTXtdApOrAr'           => $ptParameter[$i]['FTSplCode'],
                            'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                            'FNXtdStaRef'           => 0,
                            'FCXtdQtySet'           => 'NULL', 
                            'FTPdtStaSet'           => $ptParameter[$i]['FTPdtStaSet'],
                            'FDXtdExpired'          => 'NULL',  
                            'FTXtdLotNo'            => 1,
                            'FCXtdQtyLef'           => 1,
                            'FCXtdQtyRfn'           => 'NULL', 
                            'FTXthStaVatSend'       => 1,
                            'FTPdtArticle'          => $ptParameter[$i]['FTPdtArticle'],
                            'FTDcsCode'             => $ptParameter[$i]['FTDcsCode'],
                            'FTPszCode'             => $ptParameter[$i]['FTPszCode'],
                            'FTClrCode'             => $ptParameter[$i]['FTClrCode'],
                            'FTPszName'             => 'NULL', 
                            'FTClrName'             => 'NULL', 
                            'FCPdtLeftPO'           => 'NULL', 
                            'FTCpnCode'             => 'NULL', 
                            'FCXtdQtySale'          => 'NULL', 
                            'FCXtdQtyRet'           => 'NULL', 
                            'FCXtdQtyCN'            => 'NULL', 
                            'FCXtdQtyAvi'           => 'NULL', 
                            'FCXtdQtySgg'           => 'NULL', 
                            'FTXthBchFrm'           => 'NULL',       
                            'FTXthBchTo'            => 'NULL',       
                            'FTXthWahFrm'           => 'NULL', 
                            'FTXthWahTo'            => 'NULL', 
                            'FCXthDiscGP1'          => 'NULL', 
                            'FCXthDiscGP2'          => 'NULL', 
                            'FCXtdB4VatAfGP1'       => 'NULL', 
                            'FCXtdB4VatAfGP2'       => 'NULL', 
                            'FCXtdDisShp'           => 'NULL', 
                            'FCXtdShrDisShp'        => 'NULL', 
                            'FTXtdTaxInv'           => 'NULL', 
                            'FTPdtNoDis'            => $ptParameter[$i]['FTPdtNoDis'],
                            'FCXtdDisAvg'           => 'NULL', 
                            'FCXtdFootAvg'          => 'NULL', 
                            'FCXtdRePackAvg'        => 'NULL', 
                            'FCPdtLawControl'       => $ptParameter[$i]['FCPdtLawControl'],
                            'FCXtdExcDuty'          => $FCXtdExcDuty,
                            'FTPdtSaleType'         => $ptParameter[$i]['FTPdtSaleType'],
                            'FCPdtMax'              => 'NULL', 
                            'FDPdtOrdStart'         => 'NULL', 
                            'FDPdtOrdStop'          => 'NULL', 
                            'FTXtdPdtKey'           => 'NULL', 
                            'FTPmhDocNoBill'        => 'NULL', 
                            'FTXtdPmhCpnDocNo'      => 'NULL', 
                            'FCXtdPmhCpnGetQty'     => 'NULL', 
                            'FCXtdPmhCpnValue'      => 'NULL', 
                            'FCXtdDisGP'            => 'NULL', 
                            'FCXtdPmtQtyGet'        => 'NULL',       
                            'FDDateUpd'             => date('Y-m-d'),
                            'FTTimeUpd'             => date('H:i:s'),
                            'FTWhoUpd'              => $_SESSION["SesUsername"],
                            'FDDateIns'             => date('Y-m-d'),
                            'FTTimeIns'             => date('H:i:s'),
                            'FTWhoIns'              => $_SESSION["SesUsername"]
                        );
                        $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                        $this->FSxMPCNWriteLog('[FSxMPURInsertPDT] เพิ่มสินค้าจาก Browse : '.$ptParameter[$i]['FTPdtName'].'('.$ptParameter[$i]['FTPdtBarCode'].')');
                    }
                }
                
                if($tResult == 'success'){
                    return 'success';
                }else{
                    $this->FSxMPCNWriteLog('[FSxMPURInsertPDT] '.$tResult);
                    return $tResult;
                }
            }else{
                return 'nodata';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
    }

    //Insert DT Case Barcode
    public function FSxMPURInsertPDTCaseBarcode($tPDTCodeorBarcode,$tDocumentID,$nSPLCode,$nVat,$tStyCode){
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
                        FROM TCNMPdt
						    LEFT JOIN TCNMPdtBar ON TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode
                        WHERE (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                        AND (TCNMPdt.FTPdtCode='$tPDTCodeorBarcode' OR FTPdtBarCode='$tPDTCodeorBarcode') 
                        AND TCNMPdt.FTStyCode = '$tStyCode'
                        --AND TCNMPdt.FTSplCode = '$nSPLCode'
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
                    $this->FSxMPCNWriteLog('[FSxMPURInsertPDTCaseBarcode] เพิ่มสินค้าจากการสแกนบาร์โค้ด : '.$oQuery[0]['FTPdtName'].'('.$oQuery[0]['FTPdtBarCode'].')');
                }else{
                    $this->FSxMPCNWriteLog('[FSxMPURInsertPDTCaseBarcode] เพิ่มสินค้าจากการสแกนบาร์โค้ด : '.$oQuery[0]['FTPdtName'].'('.$oQuery[0]['FTPdtBarCode'].') ล้มเหลว');
                    return 'DataDuplicate';
                    exit;
                }

                //get สาขา
                $aGetBranch   = getBranch();
                $tDatabaseDT  = "TACTPtDT";

                //เลขที่เอกสาร
                if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                    $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
                    $aFormatCode    = explode("PC",$tFormatCode);
                    $tFormatCode    = 'PC0' . $aFormatCode[1];
                }else{
                    $tFormatCode    = $tDocumentID;
                }
            
                //SELECT จาก SPL
                $tSQLspl        = "SELECT TOP 1 FTSplVATInOrEx , FTAccCode FROM TCNMSpl WHERE FTSplCode = '$nSPLCode'";
                $tResultspl     = $this->DB_SELECT($tSQLspl);
                $nVatSpl        = $tResultspl[0]['FTSplVATInOrEx'];
                $tFTAccCode     = $tResultspl[0]['FTAccCode'];

                //SELECT เอา seq มาก่อน
                $tSQLseq        = "SELECT TOP 1 [FNXtdSeqNo] FROM TACTPtDT WHERE FTXthDocNo = '$tDocumentID' order by FNXtdSeqNo DESC";
                $tResultseq     = $this->DB_SELECT($tSQLseq);
                if(empty($tResultseq)){
                    $nSeq = 1;
                }else{
                    $nSeq = $tResultseq[0]['FNXtdSeqNo'] + 1;
                }

                //รหัสคลังตัดสต็อก
                $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WHERE FTSysCode = 'AWahWhsPur'";
                $oWahCode       = $this->DB_SELECT($tSQLWahCode);

                if($nVatSpl == 1){ //รวมใน

                    //Vat
                    $FCXtdVat = (1 * $oQuery[0]['FCPdtCostStd']) - ((1 * $oQuery[0]['FCPdtCostStd'] * 100)/(100+ $nVat));
                    if($FCXtdVat != 0){
                        $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdVat = 0;
                    }

                    //VatTable
                    $FCXtdVatable = (1 * $oQuery[0]['FCPdtCostStd']) -  $FCXtdVat;
                    if($FCXtdVatable != 0){
                        $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdVatable = 0;
                    }

                    //ราคาต้นทุน
                    $FCXtdCostIn = 1 * $oQuery[0]['FCPdtCostStd'];
                    if($FCXtdCostIn != 0){
                        $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdCostIn = 0;
                    }

                    //ราคาแยกนอก
                    $FCXtdCostEx =  $FCXtdVatable;

                }else{ //แยกนอก

                    //Vat
                    $FCXtdVat = (1 * $oQuery[0]['FCPdtCostStd']) * $nVat / 100;
                    if($FCXtdVat != 0){
                        $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdVat = 0;
                    }

                    //VatTable
                    $FCXtdVatable = 1 * $oQuery[0]['FCPdtCostStd'];
                    if($FCXtdVatable != 0){
                        $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdVatable = 0;
                    }

                    //ราคาต้นทุน
                    $FCXtdCostIn = (1 * $oQuery[0]['FCPdtCostStd']) + $FCXtdVat;
                    if($FCXtdCostIn != 0){
                        $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                    }else{
                        $FCXtdCostIn = 0;
                    }

                    //ราคาแยกนอก
                    $FCXtdCostEx = $oQuery[0]['FCPdtCostStd'];
                }

                //ภาษีสรรพสามิต
                $cUnitPrice     = $oQuery[0]['FCPdtCostStd'] / $oQuery[0]['FCPdtStkFac'];
                if($oQuery[0]['FCPdtLawControl'] == 0){
                    $FCXtdExcDuty = 0;
                }else{
                    $FCXtdExcDuty = ($oQuery[0]['FCPdtLawControl'] - ($oQuery[0]['FCPdtLawControl'] * 100 ) / (100 + $nVat)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $nVat));
                    $FCXtdExcDuty = round($FCXtdExcDuty,2);
                }

                $aDataInsertDT  = array(
                    'FTBchCode'             => $aGetBranch['FTBchCode'],
                    'FTXthDocNo'            => $tFormatCode,
                    'FNXtdSeqNo'            => $nSeq,
                    'FTPdtCode'             => $oQuery[0]['FTPdtCode'],
                    'FTPdtName'             => $oQuery[0]['FTPdtName'],
                    'FTXthDocType'          => 5,
                    'FDXthDocDate'          => date('Y-m-d'),
                    'FTXthVATInOrEx'        => $nVatSpl,
                    'FTXtdBarCode'          => $oQuery[0]['FTPdtBarCode'], 
                    'FTXtdStkCode'          => $oQuery[0]['FTPdtStkCode'],
                    'FCXtdStkFac'           => $oQuery[0]['FCPdtStkFac'],
                    'FTXtdVatType'          => $oQuery[0]['FTPdtVatType'],
                    'FTXtdSaleType'         => $oQuery[0]['FTPdtSaleType'],
                    'FTPgpChain'            => $oQuery[0]['FTPgpChain'],   
                    'FTSrnCode'             => 'NULL',
                    'FTPmhCode'             => 'NULL',
                    'FTPmhType'             => 'NULL', 
                    'FTPunCode'             => $oQuery[0]['FTPunCode'],      
                    'FTXtdUnitName'         => $oQuery[0]['FTPunName'],
                    'FCXtdFactor'           => $oQuery[0]['FCPdtStkFac'],
                    'FCXtdSalePrice'        => $oQuery[0]['FCPdtRetPri1'],
                    'FCXtdQty'              => 0,
                    'FCXtdSetPrice'         => $oQuery[0]['FCPdtCostStd'],
                    'FCXtdB4DisChg'         => 1 * $oQuery[0]['FCPdtCostStd'],
                    'FTXtdDisChgTxt'        => '',
                    'FCXtdDis'              => '',
                    'FCXtdChg'              => '',
                    'FCXtdNet'              => 1 * $oQuery[0]['FCPdtCostStd'],
                    'FCXtdVat'              => $FCXtdVat,
                    'FCXtdVatable'          => $FCXtdVatable,
                    'FCXtdQtyAll'           => 1,
                    'FCXtdCostIn'           => $FCXtdCostIn,
                    'FCXtdCostEx'           => $FCXtdCostEx,
                    'FTXtdStaPdt'           => 1,
                    'FTXtdStaRfd'           => 1,
                    'FTXtdStaPrcStk'        => 'NULL',      
                    'FNXthSign'             => 0,
                    'FTAccCode'             => $tFTAccCode,
                    'FNXtdPdtLevel'         => 0,     
                    'FTXtdPdtParent'        => $oQuery[0]['FTPdtCode'],
                    'FTXtdApOrAr'           => $oQuery[0]['FTSplCode'],
                    'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                    'FNXtdStaRef'           => 0,
                    'FCXtdQtySet'           => 'NULL', 
                    'FTPdtStaSet'           => $oQuery[0]['FTPdtStaSet'],
                    'FDXtdExpired'          => 'NULL',  
                    'FTXtdLotNo'            => 1,
                    'FCXtdQtyLef'           => 1,
                    'FCXtdQtyRfn'           => 'NULL', 
                    'FTXthStaVatSend'       => 1,
                    'FTPdtArticle'          => $oQuery[0]['FTPdtArticle'],
                    'FTDcsCode'             => $oQuery[0]['FTDcsCode'],
                    'FTPszCode'             => $oQuery[0]['FTPszCode'],
                    'FTClrCode'             => $oQuery[0]['FTClrCode'],
                    'FTPszName'             => 0, 
                    'FTClrName'             => 0, 
                    'FCPdtLeftPO'           => 'NULL', 
                    'FTCpnCode'             => 'NULL', 
                    'FCXtdQtySale'          => 'NULL', 
                    'FCXtdQtyRet'           => 'NULL', 
                    'FCXtdQtyCN'            => 'NULL', 
                    'FCXtdQtyAvi'           => 'NULL', 
                    'FCXtdQtySgg'           => 'NULL', 
                    'FTXthBchFrm'           => 'NULL',       
                    'FTXthBchTo'            => 'NULL',       
                    'FTXthWahFrm'           => 'NULL', 
                    'FTXthWahTo'            => 'NULL', 
                    'FCXthDiscGP1'          => 'NULL', 
                    'FCXthDiscGP2'          => 'NULL', 
                    'FCXtdB4VatAfGP1'       => 'NULL', 
                    'FCXtdB4VatAfGP2'       => 'NULL', 
                    'FCXtdDisShp'           => 'NULL', 
                    'FCXtdShrDisShp'        => 'NULL', 
                    'FTXtdTaxInv'           => 'NULL', 
                    'FTPdtNoDis'            => $oQuery[0]['FTPdtNoDis'],
                    'FCXtdDisAvg'           => 'NULL', 
                    'FCXtdFootAvg'          => 'NULL', 
                    'FCXtdRePackAvg'        => 'NULL', 
                    'FCPdtLawControl'       => $oQuery[0]['FCPdtLawControl'],
                    'FCXtdExcDuty'          => $FCXtdExcDuty,
                    'FTPdtSaleType'         => $oQuery[0]['FTPdtSaleType'],
                    'FCPdtMax'              => 'NULL', 
                    'FDPdtOrdStart'         => 'NULL', 
                    'FDPdtOrdStop'          => 'NULL', 
                    'FTXtdPdtKey'           => 'NULL', 
                    'FTPmhDocNoBill'        => 'NULL', 
                    'FTXtdPmhCpnDocNo'      => 'NULL', 
                    'FCXtdPmhCpnGetQty'     => 'NULL', 
                    'FCXtdPmhCpnValue'      => 'NULL', 
                    'FCXtdDisGP'            => 'NULL', 
                    'FCXtdPmtQtyGet'        => 'NULL',       
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
        }
    }

    //Check Product ห้ามค่าซ้ำ ในกรณี เพื่มสินค้าแบบ คีย์เอง
    public function FSxMPURCheckProduct($pnCode,$ptDocument){
        $tSQL = "SELECT TOP 1 FTXthDocNo FROM TACTPtDT WHERE FTPdtCode = '$pnCode' AND FTXthDocNo = '$ptDocument' ";
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
                    HD.FTXthDocNo ,
                    HD.FTXthDisChgTxt ,
                    CONVERT(VARCHAR(10),HD.FDXthDocDate,103) as FDXthDocDate ,
                    HD.FCXthDis ,
                    HD.FTWhoIns ,
                    HD.FTXthRmk ,
                    REA.FTCutName ,
                    REA.FTCutCode ,
                    HD.FTXthStaDoc , 
                    HD.FTXthStaPrcDoc , 
                    HD.FTEdiDocNo ,
                    HD.FCXthVATRate , 
                    HD.FTVatCode ,
                    HD.FTStyCode ,
                    HD.FTSplCode ,
                    HD.FTXthDocType ,
                    CONVERT(VARCHAR(10),HD.FDEdiDate,103) as FDEdiDate ,
                    HD.FTEdiTime ,
                    CONVERT(VARCHAR(10),HD.FDXthBchReturn,103) as FDXthBchReturn ,
                    HD.FTXthRefExt,
                    CONVERT(VARCHAR(10), HD.FDXthRefExtDate,103) as FDXthRefExtDate,
                    CONVERT(VARCHAR(10), HD.FDDateIns ,20) as FDDateIns,
                    CONVERT(VARCHAR(10), HD.FDDateUpd ,103) AS FDDateUpd
                FROM TACTPtHD HD
                LEFT JOIN TCNMCutOff REA ON HD.FTSpnCode = REA.FTCutCode";
        if($pnDocumentNo == ''){
            $tSQL .= "";
        }else{
            $tSQL .= " WHERE HD.FTXthDocNo = '$pnDocumentNo' ";
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
            // $tDatabase          = "TACTPtHD";
            // $aDataDeleteWHERE   = array(
            //     'FTBchCode'    => $aGetBranch['FTBchCode'] ,
            //     'FTXthDocNo'   => $paData['tDocumentNumber'] 
            // );
            // $bConfirm           = true;
            // $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            $tSQLChkData     = " SELECT FTXthDocNo FROM TACTPtHD WITH(NOLOCK) WHERE FTXthDocNo = '".$paData['tDocumentNumber']."' AND FTBchCode = '".$aGetBranch['FTBchCode']."' ";
            $tUpdOrInsResult = $this->DB_SELECT($tSQLChkData);

            $aDetailSPL = $this->FSaMPURGetDetailSupplier($paData['tSplCode'],0);
            
            if($aDetailSPL[0]['FTSplVATInOrEx'] == 1){ //รวมใน
                $FCXthVat = str_replace(",","",$paData['nCalResult']) * str_replace(",","",$paData['nVatValue']) / 100;
            }else{ //แยกนอก
                $FCXthVat = str_replace(",","",$paData['nCalBeforeDiscount']) * str_replace(",","",$paData['nVatValue']) / 100;
            }

            //วันที่ + กับ credit term
            $nCreditTerm    = '+' . $aDetailSPL[0]['FNSplCrTerm'] . ' days';
            $dCurrent       = date('Y-m-d');
            $dFDXthDueDate  = date('Y-m-d', strtotime($nCreditTerm, strtotime($dCurrent)));

            //รหัสงวดบัญชี
            $tSQLPrdCode    = "SELECT TOP 1 FTPrdCode FROM TCNMAcPrd WHERE CONVERT(VARCHAR(2),GETDATE(),101) BETWEEN CONVERT(VARCHAR(2),FDPrdStart,101) AND CONVERT(VARCHAR(2),FDPrdEnd,101)";
            $oPrdCode       = $this->DB_SELECT($tSQLPrdCode);

            //รหัสคลังตัดสต็อก
            $tSQLWahCode    = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig WHERE FTSysCode = 'AWahWhsPur'";
            $oWahCode       = $this->DB_SELECT($tSQLWahCode);

            //ประเภทผู้จำหน่าย
            $tRoundBranch   = $paData['tTypeRoundBranch'];
            $FTXthDocType   = 5;
            $FTXthRefExt    = $paData['tNumberSend'];
            $FDXthRefExtDate= $paData['tDateSend'];
            $FDXthTnfDate   = $paData['tDateSend'];
            $FDXthBillDue   = $paData['tDateSend'];  

            //Cash or card
            if($aDetailSPL[0]['FNSplCrTerm'] == '' || $aDetailSPL[0]['FNSplCrTerm'] == null || $aDetailSPL[0]['FNSplCrTerm'] == 0){
                $FTXthCshOrCrd = 1;
            }else{
                $FTXthCshOrCrd = 2;
            }

            //สูตรหาEDI
            //Format:  XXXXXYYMMDDTZZRC
            $nX         = '0'.$aGetBranch['FTBchCode'];
            $nY         = date('y');
            $nM         = date('m');
            $nD         = date('d');
            $nT         = 1;
            $tEDI       = "SELECT TOP 1 FTEdiDocNo FROM TACTPtHD WHERE FTEdiDocNo != '' order by FTXthDocNo DESC";
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
            
            //Insert
            $tDatabase    = "TACTPtHD";
            if( empty($tUpdOrInsResult) ){
                $aDataInsert  = array(
                    'FTBchCode'         => $aGetBranch['FTBchCode'],
                    'FTXthDocNo'        => $paData['tDocumentNumber'],
                    'FTXthDocType'      => $FTXthDocType, //ประเภทลดหนี้
                    'FDXthDocDate'      => $paData['dDocDate'], /*date('Y-m-d')*/
                    'FTXthDocTime'      => date('H:i:s'),
                    'FTXthVATInOrEx'    => $aDetailSPL[0]['FTSplVATInOrEx'],
                    'FTStyCode'         => $paData['tStyCode'],
                    'FTDptCode'         => $_SESSION["SesUserDptCode"],
                    'FTUsrCode'         => $_SESSION["FTUsrCode"],
                    'FTSplCode'         => $paData['tSplCode'],
                    'FTCstCode'         => $paData['tSplCode'],
                    'FTAreCode'         => $aDetailSPL[0]['FTAreCode'],
                    'FTSpnCode'         => $paData['tReason'],
                    'FTPrdCode'         => $oPrdCode[0]['FTPrdCode'],
                    'FTWahCode'         => $oWahCode[0]['FTSysUsrValue'],
                    'FTXthApvCode'      => $_SESSION["FTUsrCode"],
                    'FTShpCode'         => $aDetailSPL[0]['FTShpCode'],
                    'FNCspCode'         => 0,
                    'FNXthCrTerm'       => $aDetailSPL[0]['FNSplCrTerm'],
                    'FDXthDueDate'      => $dFDXthDueDate,
                    'FTXthRefExt'       => $FTXthRefExt,
                    'FDXthRefExtDate'   => $FDXthRefExtDate,
                    'FTXthRefInt'       => 'NULL',
                    'FDXthRefIntDate'   => $FDXthRefExtDate,
                    'FTXthRefAE'        => 'NULL',
                    'FDXthTnfDate'      => $FDXthTnfDate,
                    'FDXthBillDue'      => $FDXthBillDue,
                    'FTXthCtrName'      => 'NULL',
                    'FNXthDocPrint'     => 0,
                    'FCXthVATRate'      => $paData['nVatValue'],
                    'FTVatCode'         => $paData['tVatCode'],
                    'FCXthTotal'        => str_replace(",","",$paData['nCalResult']),
                    'FCXthTotalExcise'  => str_replace(",","",$paData['nCalResult']),
                    'FCXthVatExcise'    => 0, //ยัง
                    'FCXthNonVat'       => 0, //ยัง
                    'FCXthB4DisChg'     => str_replace(",","",$paData['nCalResult']),
                    'FTXthDisChgTxt'    => $paData['tTextCalDiscount'],
                    'FCXthDis'          => str_replace(",","",$paData['nCalDiscount']),
                    'FCXthChg'          => 0,
                    'FCXthAftDisChg'    => str_replace(",","",$paData['nCalBeforeDiscount']),
                    'FCXthVat'          => str_replace(",","",$FCXthVat),
                    'FCXthVatable'      => str_replace(",","",$paData['nCalBeforeDiscount']),
                    'FCXthGrand'        => str_replace(",","",$paData['nCalBeforeDiscount']),
                    'FCXthRnd'          => 0,
                    'FCXthWpTax'        => 0,
                    'FCXthReceive'      => str_replace(",","",$paData['nCalNet']),
                    'FCXthChn'          => 0,
                    'FTXthGndText'      => $paData['tTextCalculate'],
                    'FCXthLeft'         => str_replace(",","",$paData['nCalBeforeDiscount']),
                    'FCXthMnyCsh'       => 0,
                    'FCXthMnyChq'       => 0,
                    'FCXthMnyCrd'       => 0,
                    'FCXthMnyCtf'       => 0,
                    'FCXthMnyCpn'       => 0,
                    'FCXthMnyCls'       => 0,
                    'FCXthMnyCxx'       => 1,
                    'FCXthGndCN'        => 0,
                    'FCXthGndDN'        => 0,
                    'FCXthGndAE'        => 0,
                    'FCXthGndTH'        => 0,
                    'FTXthStaPaid'      => 1,
                    'FTXthStaRefund'    => 1,
                    'FTXthStaType'      => 2,
                    'FTXthStaDoc'       => 1,
                    'FTXthStaPrcDoc'    => '',
                    'FTXthStaPrcSpn'    => 'NULL',
                    'FTXthStaPrcCst'    => 'NULL',
                    'FTXthStaPrcGL'     => 'NULL',
                    'FTXthStaPost'      => 'NULL',
                    'FTPjcCode'         => 'NULL',
                    'FTAloCode'         => 'NULL',
                    'FTCcyCode'         => 'NULL',
                    'FCXthCcyExg'       => 0,
                    'FTPosCode'         => 'NULL',
                    'FTXthPosCN'        => 'NULL',
                    'FTLogCode'         => 'NULL',
                    'FTXthRmk'          => $paData['tReasonTextArea'],
                    'FNXthSign'         => 0,
                    'FTXthCshOrCrd'     => $FTXthCshOrCrd,
                    'FCXthPaid'         => 0,
                    'FTXthDstPaid'      => $aDetailSPL[0]['FTSplTspPaid'],
                    'FTXbhDocNo'        => 'NULL',
                    'FTXphDocNo'        => 'NULL',
                    'FNXthStaDocAct'    => 1,
                    'FNXthStaRef'       => 0,
                    'FTXthUsrEnter'     => 'NULL',
                    'FTXthUsrPacker'    => 'NULL',
                    'FTXthUsrChecker'   => 'NULL',
                    'FTXthUsrSender'    => 'NULL',
                    'FTXthTnfID'        => 'NULL',
                    'FTXthVehID'        => 'NULL',
                    'FTDocControl'      => 'NULL',
                    'FTXthStaPrcStk'    => 'NULL',
                    'FTXthStaPrcLef'    => 'NULL',
                    'FTXthStaVatType'   => 2,
                    'FTXthStaVatSend'   => 1,
                    'FTXthStaVatUpld'   => 'NULL',
                    'FTXthDocVatFull'   => 'NULL',
                    'FTXqhDocNoRef'     => 'NULL',
                    'FTXthRefSaleTax'   => 'NULL',
                    'FTCstStaClose'     => 'NULL',
                    'FTXthBchFrm'       => 'NULL',
                    'FTXthBchTo'        => 'NULL',
                    'FTXthWahFrm'       => 'NULL',
                    'FTXthWahTo'        => 'NULL',
                    'FTXthCstName'      =>  $aDetailSPL[0]['FTSplName'],
                    'FTCstAddrInv'      =>  $aDetailSPL[0]['FTSplAddr'] . ' ถ.' . $aDetailSPL[0]['FTSplStreet'] . ' ต.' . $aDetailSPL[0]['FTSplDistrict'],
                    'FTCstStreetInv'    => 'NULL',
                    'FTCsttrictInv'     => 'NULL',
                    'FTDstCodeInv'      => 'NULL',
                    'FTPvnCodeInv'      => 'NULL',
                    'FTCstPostCodeInv'  => 'NULL',
                    'FCXthDiscGP1'      => 'NULL',
                    'FCXthDiscGP2'      => 'NULL',
                    'FCXthB4VatAfGP1'   => 'NULL',
                    'FCXthB4VatAfGP2'   => 'NULL',
                    'FTXthDocRefMin'    => 'NULL',
                    'FTXthDocRefMax'    => 'NULL',
                    'FTXthStaJob'       => 'NULL',
                    'FDEdiDate'         => ($paData['tDocDate'] == 'null') ? 'NULL' : $paData['tDocDate'], 
                    'FTEdiTime'         => ($paData['tDocTime'] == 'null') ? date('H:i') : $paData['tDocTime'],
                    'FTEdiDocNo'        => $FTEdiDocNo,
                    'FTEdiStaRcvAuto'   => 1,
                    'FDXthBchAffect'    => 'NULL',
                    'FDXthBchExpired'   => 'NULL',
                    'FDXthBchReturn'    => ($paData['tDocDateReturn'] == 'null') ? 'NULL' : $paData['tDocDateReturn'],
                    'FNLogStaExport'    => 'NULL',
                    'FTPmhDocNoBill'    => 'NULL',
                    'FCXthDisPmt'       => 'NULL',
                    'FTXthCpnCodeRef'   => 'NULL',
                    'FCXthCpnRcv'       => 'NULL',
                    'FCXthRndMnyChg'    => 'NULL',
                    'FTXthStaSavZero'   => 0,
                    'FDDateUpd'         => date('Y-m-d'),
                    'FTTimeUpd'         => date('H:i:s'),
                    'FTWhoUpd'          => $_SESSION["SesUsername"],
                    'FDDateIns'         => date('Y-m-d'),
                    'FTTimeIns'         => date('H:i:s'),
                    'FTWhoIns'          => $_SESSION["SesUsername"]
                );
                echo $this->DB_INSERT($tDatabase,$aDataInsert);
            }else{
                $FTCstAddrInv = $aDetailSPL[0]['FTSplAddr'] . ' ถ.' . $aDetailSPL[0]['FTSplStreet'] . ' ต.' . $aDetailSPL[0]['FTSplDistrict'];
                $FDEdiDate = ($paData['tDocDate'] == 'null') ? 'NULL' : $paData['tDocDate'];
                $FTEdiTime = ($paData['tDocTime'] == 'null') ? date('H:i') : $paData['tDocTime'];
                $FDXthBchReturn = ($paData['tDocDateReturn'] == 'null') ? 'NULL' : $paData['tDocDateReturn'];

                $tUpdateSql = " UPDATE ".$tDatabase." 
                                SET 
                                    FTXthVATInOrEx    = '".$aDetailSPL[0]['FTSplVATInOrEx']."',
                                    FTStyCode         = '".$paData['tStyCode']."',
                                    FTDptCode         = '".$_SESSION["SesUserDptCode"]."',
                                    FTUsrCode         = '".$_SESSION["FTUsrCode"]."',
                                    FTSplCode         = '".$paData['tSplCode']."',
                                    FTCstCode         = '".$paData['tSplCode']."',
                                    FTAreCode         = '".$aDetailSPL[0]['FTAreCode']."',
                                    FTSpnCode         = '".$paData['tReason']."',
                                    FTPrdCode         = '".$oPrdCode[0]['FTPrdCode']."',
                                    FTWahCode         = '".$oWahCode[0]['FTSysUsrValue']."',
                                    FTXthApvCode      = '".$_SESSION["FTUsrCode"]."',
                                    FTShpCode         = '".$aDetailSPL[0]['FTShpCode']."',
                                    FNXthCrTerm       = '".$aDetailSPL[0]['FNSplCrTerm']."',
                                    FDXthDueDate      = '".$dFDXthDueDate."',
                                    FTXthRefExt       = '".$FTXthRefExt."',
                                    FDXthRefExtDate   = '".$FDXthRefExtDate."',
                                    FDXthRefIntDate   = '".$FDXthRefExtDate."',
                                    FDXthTnfDate      = '".$FDXthTnfDate."',
                                    FDXthBillDue      = '".$FDXthBillDue."',
                                    FCXthVATRate      = '".$paData['nVatValue']."',
                                    FTVatCode         = '".$paData['tVatCode']."',
                                    FCXthTotal        = '".str_replace(",","",$paData['nCalResult'])."',
                                    FCXthTotalExcise  = '".str_replace(",","",$paData['nCalResult'])."',
                                    FCXthB4DisChg     = '".str_replace(",","",$paData['nCalResult'])."',
                                    FTXthDisChgTxt    = '".$paData['tTextCalDiscount']."',
                                    FCXthDis          = '".str_replace(",","",$paData['nCalDiscount'])."',
                                    FCXthAftDisChg    = '".str_replace(",","",$paData['nCalBeforeDiscount'])."',
                                    FCXthVat          = '".str_replace(",","",$FCXthVat)."',
                                    FCXthVatable      = '".str_replace(",","",$paData['nCalBeforeDiscount'])."',
                                    FCXthGrand        = '".str_replace(",","",$paData['nCalBeforeDiscount'])."',
                                    FCXthReceive      = '".str_replace(",","",$paData['nCalNet'])."',
                                    FTXthGndText      = '".$paData['tTextCalculate']."',
                                    FCXthLeft         = '".str_replace(",","",$paData['nCalBeforeDiscount'])."',
                                    FTXthRmk          = '".$paData['tReasonTextArea']."',
                                    FTXthCshOrCrd     = '".$FTXthCshOrCrd."',
                                    FTXthDstPaid      = '".$aDetailSPL[0]['FTSplTspPaid']."',
                                    FTXthCstName      = '".$aDetailSPL[0]['FTSplName']."',
                                    FTCstAddrInv      = '".$FTCstAddrInv."',
                                    FDEdiDate         = '".$FDEdiDate."', 
                                    FTEdiTime         = '".$FTEdiTime."',
                                    FTEdiDocNo        = '".$FTEdiDocNo."',
                                    FDXthBchReturn    = '".$FDXthBchReturn."',
                                    FDDateUpd         = '".date('Y-m-d')."',
                                    FTTimeUpd         = '".date('H:i:s')."',
                                    FTWhoUpd          = '".$_SESSION["SesUsername"]."'
                                WHERE FTXthDocNo = '".$paData['tDocumentNumber']."' 
                                  AND FTBchCode  = '".$aGetBranch['FTBchCode']."'
                              ";
                print_r($this->DB_EXECUTE($tUpdateSql));
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Delete Item DT 
    public function FSxMPURDeletePDT($paData){
        try {
            $pnSeq      = $paData['FNXtdSeqNo'];
            $pnPdtcode  = $paData['FTPdtCode'];
            $ptDocNo    = $paData['FTXthDocNo'];
            $ptBchCode  = $paData['FTBchCode'];

            $tDatabase          = "TACTPtDT";
            $aDataDeleteWHERE   = array(
                'FNXtdSeqNo'    => $pnSeq ,
                'FTPdtCode'     => $pnPdtcode , 
                'FTXthDocNo'    => $ptDocNo 
            );

            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
           
            //Update sequence
            $tUpdateSql = "UPDATE TACTPtDT 
                            SET FNXtdSeqNo = SeqNew.rtRowID
                            FROM (
                                SELECT c.* FROM( 
                                        SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo) AS rtRowID, FNXtdSeqNo , FTPdtCode FROM TACTPtDT 
                                        WHERE FTXthDocNo = '$ptDocNo'
                                    ) as c 
                                ) SeqNew
                            WHERE 
                                SeqNew.FNXtdSeqNo = TACTPtDT.FNXtdSeqNo";
            $this->DB_EXECUTE($tUpdateSql);

            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Check Document ถ้าเอกสารยังไม่สมบุรณ์ต้องดึงออกมาใช้งานต่อ
    public function FSaMPURCheckDocument($ptRoundBranch){
        $FTXthDocType = 5;
        $tSQL = "SELECT TOP 1 
                    FTXthDocNo , 
                    FTSplCode , 
                    FTStyCode , 
                    FTXthStaDoc , 
                    FTXthStaPrcDoc ,
                    FTXthRefExt
                FROM TACTPtHD WHERE FTXthStaDoc = 1 AND FTXthDocType = '$FTXthDocType' AND (FTXthStaPrcDoc = '' OR FTXthStaPrcDoc = null OR FTXthStaPrcDoc IS NULL ) ORDER BY FTXthDocNo ASC";
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
                $tQty           = $paData['FCXtdQty'];
            }else{
                $tQty           = 0;
            }
            $dUpd           = date('Y-m-d');
            $tTime          = date('H:i:s');
            $tWho           = $_SESSION["SesUsername"];
            $tSeq           = $paData['FNXtdSeqNo'];
            $tDoc           = $paData['FTXthDocNo'];
            $FCXtdB4DisChg  = $paData['FCXtdB4DisChg'];
            $nVatRate       = $paData['nVatRate'];


            $tSQL   = "SELECT TOP 1  FCXtdSetPrice , FCXtdStkFac , FCPdtLawControl , FTXthVATInOrEx FROM TACTPtDT 
                        WHERE FNXtdSeqNo = '$tSeq' AND FTXthDocNo = '$tDoc' ";
            $oQuery = $this->DB_SELECT($tSQL);
            $nNewNet = $oQuery[0]['FCXtdSetPrice'] * $tQty;

            if($oQuery[0]['FTXthVATInOrEx'] == 1){ //รวมใน
                //Vat
                $FCXtdVat = $nNewNet - (($nNewNet * 100)/(100+ $nVatRate));
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
            }else if($oQuery[0]['FTXthVATInOrEx'] == 2){ //แยกนอก
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
            $cUnitPrice     = $oQuery[0]['FCXtdSetPrice'] / $oQuery[0]['FCXtdStkFac'];
            if($oQuery[0]['FCPdtLawControl'] == 0){
                $FCXtdExcDuty = 0;
            }else{
                $FCXtdExcDuty = ($oQuery[0]['FCPdtLawControl'] - ($oQuery[0]['FCPdtLawControl'] * 100 ) / (100 + $nVatRate)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $nVatRate));
                $FCXtdExcDuty = round($FCXtdExcDuty,2) * $tQty;
            }

            $tSQLUpdate   = " UPDATE TACTPtDT SET 
                        FCXtdQty = '$tQty',
                        FCXtdQtyAll = '$tQty',
                        FCXtdQtyLef = '$tQty',
                        FCXtdNet = '$nNewNet',
                        FCXtdVat = '$FCXtdVat',
                        FCXtdVatable = '$FCXtdVatable',
                        FCXtdB4DisChg = '$FCXtdB4DisChg',
                        FCXtdExcDuty = '$FCXtdExcDuty',
                        FDDateUpd = '$dUpd',
                        FTTimeUpd = '$tTime',
                        FTWhoUpd = '$tWho'
                        WHERE FNXtdSeqNo = '$tSeq' AND 
                        FTXthDocNo = '$tDoc' ";
            $tResult    = $this->DB_EXECUTE($tSQLUpdate);
            if($ptTypeUpdate == 1){
                return $tResult;
            }else{
                return;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Check Product Qty Ret
    public function FSaMPURChkPdtQtyRet($paData){

        $nQty = intval($paData['FCXtdQty']);
        $tSQL   = " SELECT FTSysUsrValue FROM TSysConfig WHERE FTSysCode='AChkCN' AND FTSysSeq= '001' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if($oQuery[0]['FTSysUsrValue'] != '0'){
            $tSQL   = " SELECT 
                            FCPdtQtyRet 
                        FROM TCNMPdt WITH(NOLOCK) 
                        WHERE FTPdtCode = '$paData[FTPdtCode]'
                        AND FCPdtQtyRet < ( $nQty * FCPdtStkFac )
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

    //Calcualte
    public function FSaMPURCalculate($tDocumentID){
        $tSQL = "SELECT SUM(FCXtdNet) as nTotal FROM TACTPtDT WHERE FTXthDocNo = $tDocumentID ";
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
            $tSQLCheckHD = "SELECT TOP 1 FTXthDocNo FROM TACTPtHD 
                            WHERE FTXthDocNo = '$tDocumentID' ";
            $oQueryHD = $this->DB_SELECT($tSQLCheckHD);
            if (!empty($oQueryHD)) {
                $dUpd         = date('Y-m-d');
                $tTime        = date('H:i:s');
                $tWho         = $_SESSION["SesUsername"];
                $tSQLUpdate   = " UPDATE TACTPtHD SET 
                            FTXthStaDoc = '3',
                            FTXthStaPrcDoc = '3',
                            FTEdiDocno = '',
                            FTEdiStaRcvAuto = '3',
                            FDDateUpd = '$dUpd',
                            FTTimeUpd = '$tTime',
                            FTWhoUpd = '$tWho'
                            WHERE FTXthDocNo = '$tDocumentID' ";
                $tResult    = $this->DB_EXECUTE($tSQLUpdate);
                return $tResult;
            }else{
                $tDatabase          = "TACTPtDT";
                $aDataDeleteWHERE   = array(
                    'FTXthDocNo'    => $tDocumentID 
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
                return $tResult;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Select [Document] HD
    public function FSxMPURListSearchSelectHD($paData){
        try {
            $aGetBranch     = getBranch();
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);

            $tSQL           = "SELECT c.* FROM( SELECT  ROW_NUMBER() OVER(ORDER BY ";
            $tSQL           .= " FTXthDocNo DESC ";
            $tSQL           .= ") AS rtRowID,* FROM";
            $tSQL           .= "(SELECT 
                                    HD.FTXthDocNo ,
                                    HD.FTXthDisChgTxt ,
                                    HD.FCXthDis ,
                                    HD.FTWhoIns ,
                                    HD.FTXthRmk ,
                                    HD.FTXthStaDoc , 
                                    HD.FTXthStaPrcDoc , 
                                    HD.FTEdiDocNo ,
                                    HD.FCXthVATRate , 
                                    HD.FTVatCode ,
                                    HD.FTStyCode ,
                                    HD.FTSplCode ,
                                    HD.FTXthDocType ,
                                    CONVERT(VARCHAR(10),HD.FDEdiDate,103) as FDEdiDate ,
                                    HD.FTEdiTime ,
                                    CONVERT(VARCHAR(10),HD.FDXthBchReturn,103) as FDXthBchReturn ,
                                    HD.FTXthRefExt,
                                    CONVERT(VARCHAR(10), HD.FDXthRefExtDate,103) as FDXthRefExtDate,
                                    CONVERT(VARCHAR(10), HD.FDDateIns ,121) as FDDateIns,
                                    HD.FTXthDocTime";
            $tSQL           .= " FROM TACTPtHD HD WITH (NOLOCK)  
                                 WHERE 1=1 ";
            
            $tTextSearchPUR = $paData['tTextSearchPUR'];
            if($tTextSearchPUR != '' || $tTextSearchPUR != null){
                $tSQL           .= " AND FTXthDocNo LIKE '%$tTextSearchPUR%' ";
                $tSQL           .= " OR CONVERT(VARCHAR(10), HD.FDDateIns ,20) = '$tTextSearchPUR' ";
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
        }
    }

    //Select [Document] count DT
    public function FSnMPURListSearchGetPageAll($paData){
        
        $tSQL = "SELECT COUNT (HD.FTXthDocNo) AS counts
                 FROM TACTPtHD HD
                 WHERE 1=1 ";

        $tTextSearchPUR = $paData['tTextSearchPUR'];
        if($tTextSearchPUR != '' || $tTextSearchPUR != null){
            $tSQL   .= " AND FTXthDocNo LIKE '%$tTextSearchPUR%' ";
            $tSQL   .= " OR CONVERT(VARCHAR(10), HD.FDDateIns ,20) = '$tTextSearchPUR' ";
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
        $tSQL = "SELECT DT.FTXthDocNo FROM (SELECT TOP 1 DT.FTXthDocNo from TACTPtDT DT ORDER BY DT.FTXthDocNo DESC ) AS DT 
                 INNER JOIN TACTPtHD HD ON DT.FTXthDocNo = HD.FTXthDocNo";
        $oQuery = $this->DB_SELECT($tSQL);
        if (empty($oQuery)) {
            //Delete DT order by DESC
            $tSQLDocno       = "SELECT TOP 1 DT.FTXthDocNo from TACTPtDT DT ORDER BY DT.FTXthDocNo DESC";
            $oQueryDocno     = $this->DB_SELECT($tSQLDocno);
            if(empty($oQueryDocno)){

            }else{
                $tDatabase          = "TACTPtDT";
                $aDataDeleteWHERE   = array(
                    'FTXthDocNo'    => $oQueryDocno[0]['FTXthDocNo']
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            }
        }else{
            return true;
        }
    }

    //ตรวจสอบเอกสารว่ามีการแยก VATCODE
    public function FSaMPURCheckDocSplit($ptDocument){
        $tSQL = "SELECT MAIN.FTXthDocNo , MAIN.FTTimeIns , CONVERT(VARCHAR(10),MAIN.FDDateIns,103) as FDDateIns FROM TACTPtHD MAIN WHERE MAIN.FTXthDocNo = '$ptDocument'";
        $oQuery = $this->DB_SELECT($tSQL);
        
        $tTimeIns = $oQuery[0]['FTTimeIns'];
        $dDateIns = $oQuery[0]['FDDateIns'];

        $tSQLFindDoc    = "SELECT MAIN.FTXthDocNo FROM TACTPtHD MAIN WHERE MAIN.FTTimeIns = '$tTimeIns' AND CONVERT(VARCHAR(10),MAIN.FDDateIns,103) = '$dDateIns' ";
        $oQueryFindDoc  = $this->DB_SELECT($tSQLFindDoc);
        return $oQueryFindDoc;
    }

    //----------------------------------- ตามรอบ -----------------------------------//
    //Select เอกสารที่ส่ง ใบ Pr (ใบขอลดหนี้)
    public function FSaMPURGetDetailPu($tTypeRoundorBranch,$tTypeSup,$tSearch,$tColumSearch){
        try {
            $tDptCode       = $_SESSION["SesUserDptCode"];
            $aGetBranch     = getBranch();
            $tGetBranch     = $aGetBranch['FTBchCode'];
            $tFilterSearch  = '';

            if($tTypeRoundorBranch == 'PUR1'){
                $tTypeRoundorBranch = 5;
            }else if($tTypeRoundorBranch == 'PUR2'){
                $tTypeRoundorBranch = 6;
            }

            
            if(isset($tSearch) && !empty($tSearch)){
                if(isset($tColumSearch) && !empty($tColumSearch)){
                    switch($tColumSearch){
                        case 'FTXrhDocNo':
                            $tFilterSearch = " AND TACTPrHD.FTXrhDocNo LIKE '%$tSearch%' ";
                            break;
                        case 'FDXrhDocDate':
                            $tFilterSearch = " AND TACTPrHD.FDXrhDocDate = '$tSearch' ";
                            break;
                        case 'FTSplName':
                            $tFilterSearch = " AND TCNMSpl.FTSplName LIKE '%$tSearch%' ";
                            break;
                        case 'FTSplCode':
                            $tFilterSearch = " AND TCNMSpl.FTSplCode LIKE '%$tSearch%' ";
                            break;
                        case 'FTStyCode':
                            $tFilterSearch = " AND TCNMSpl.FTStyCode LIKE '%$tSearch%' ";
                            break;
                        case 'FDXrhBchReturn':
                            $tFilterSearch = " AND TACTPrHD.FDXrhBchReturn = '$tSearch' ";
                            break;
                    }
                }
            }

            $tSQL = " SELECT  TOP  3000 TACTPrHD.FNXrhStaRef  AS FBStaRef,
                        TACTPrHD.FTBchCode,
                        TACTPrHD.FTXrhDocNo,
                        CONVERT(VARCHAR(10),TACTPrHD.FDXrhDocDate,103) as FDXrhDocDate,
                        TCNMSpl.FTSplName,
                        TCNMSpl.FTSplCode,
                        TACTPrHD.FTStyCode,
                        TCNMSplType.FTStyName,
                        CONVERT(VARCHAR(10),TACTPrHD.FDXrhBchReturn,103) as FDXrhBchReturn 
                        FROM ((TACTPrHD 
                        INNER JOIN TCNMDepart ON TACTPrHD.FTDptCode = TCNMDepart.FTDptCode)  
                        INNER JOIN TCNMSpl ON TACTPrHD.FTSplCode = TCNMSpl.FTSplCode)
                        LEFT JOIN TCNMSplType ON TACTPrHD.FTStyCode = TCNMSplType.FTStyCode  
                        INNER JOIN TSysUser ON TACTPrHD.FTUsrCode = TSysUser.FTUsrCode 
                        WHERE (TACTPrHD.FTXrhDocType IN ('$tTypeRoundorBranch')) AND (TACTPrHD.FTXrhStaPrcDoc='1') 
                        $tFilterSearch
                        AND (TACTPrHD.FNXrhStaRef<>'2') 
                        AND (TACTPrHD.FTStyCode='$tTypeSup') 
                        AND (FTXrhStaPrcDoc='1') 
                        AND (FNXrhStaRef = 0 ) 
                        AND (FNXrhStaDocAct  = 1)
                        AND (FTXrhStaDoc  = '1') 
                        AND (TACTPrHD.FTDptCode = '$tDptCode' ) 
                        ORDER BY  FDXrhDocDate DESC , TACTPrHD.FTXrhDocNo DESC ";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Select สินค้าในเอกสารที่ส่ง Pr (ใบขอลดหนี้)
    public function FSaMPURGetDetailPDTByDocument($ptDocumentNumber){
        try {
            $tFTXnhDocNo    = $ptDocumentNumber;
            $aGetBranch     = getBranch();
            $tGetBranch     = $aGetBranch['FTBchCode'];
            $tSQL = " SELECT  0 AS FXSeqNo,
                        PrDT.FNXrdSeqNo,
                        PrDT.FTPdtCode,
                        PrDT.FTPdtName,
                        PrDT.FTXrdBarCode,
                        PrDT.FTSrnCode,
                        PrDT.FTXrdUnitName,
                        PrDT.FCXrdQty,
                        PrDT.FCXrdSalePrice,
                        PrDT.FCXrdSetPrice,
                        PrDT.FTXrdDisChgTxt,
                        PrDT.FCXrdNet,
                        PrHD.FCXrhVATRate,
                        PrHD.FTVatCode,
                        PrHD.FTXrhVATInOrEx,
                        PrHD.FTStyCode,
                        PrHD.FTSplCode,
                        PrHD.FTCstCode,
                        PrHD.FTAreCode,
                        PrHD.FTXrhRmk,
                        PrHD.FTSpnCode,
                        REASON.FTCutName,
                        PrHD.FTPrdCode,
                        PrHD.FTWahCode
                        FROM TACTPrDT PrDT
                        LEFT JOIN TACTPrHD PrHD ON PrDT.FTXrhDocNo = PrHD.FTXrhDocNo
                        LEFT JOIN TCNMCutOff REASON ON PrHD.FTSpnCode = REASON.FTCutCode
                        WHERE  PrDT.FTBchCode ='$tGetBranch' AND 
                        PrDT.FTXrhDocNo = '$tFTXnhDocNo' 
                        ORDER BY PrDT.FNXrdSeqNo";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Insert PUR1 ลง Pt 
    public function FSaMPURInsertPDTByPUR1($tDocumentPN,$tFormatCode,$pnPDTCode,$pnKey,$tDocPNCurrent){
        try {
            $aGetBranch     = getBranch();
            // if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
            //     $tFormatCode    = generateCode('TACTPtHD','FTXthDocNo');
            //     $aFormatCode    = explode("PC",$tFormatCode);
            //     $tFormatCode    = 'PC0' . $aFormatCode[1];
            //     $this->FSxMPCNWriteLog('[FSaMPURInsertPDTByPUR1] (1) สร้างเอกสารหมายเลข '.$tFormatCode);
            // }else{
            //     $tFormatCode    = $tDocumentID;
            // }

            //Insert DT
            $tDatabaseDT    = "TACTPtDT";
            //SELECT เอา seq มาก่อน
            $tSQLseq        = "SELECT TOP 1 [FNXtdSeqNo] FROM TACTPtDT WHERE FTXthDocNo = '$tFormatCode' order by FNXtdSeqNo DESC";
            $tResultseq     = $this->DB_SELECT($tSQLseq);
            if(empty($tResultseq)){
                $nSeq = 0;
            }else{
                $nSeq = $tResultseq[0]['FNXtdSeqNo'];
            }

            //SELECT จาก SPL
            $tSQLspl        = "SELECT PR.FTXrhDocNo , 
                                PR.FDXrhDocDate , 
                                PR.FTXrhDocTime , 
                                PR.FTStyCode , 
                                PR.FTDptCode , 
                                PR.FTSplCode ,
                                PR.FCXrhVATRate ,
                                PR.FTVatCode ,
                                SPL.FTSplVATInOrEx ,
                                SPL.FTAccCode ,
                                CONVERT(VARCHAR(10),PR.FDXrhDocDate,103) as FDXrhDocDate ,
                                CONVERT(VARCHAR(10),PR.FDXrhBchReturn,103) as FDXrhBchReturn 
                                FROM TACTPrHD PR
                                LEFT JOIN TCNMSpl SPL ON SPL.FTSplCode = PR.FTSplCode
                                WHERE PR.FTXrhDocNo = '$tDocPNCurrent' ";
            $tResultspl     = $this->DB_SELECT($tSQLspl);
            $nVatSpl        = $tResultspl[0]['FTSplVATInOrEx'];
            $tFTAccCode     = $tResultspl[0]['FTAccCode'];
            $tSPLCode       = $tResultspl[0]['FTSplCode'];
            $tSPLType       = $tResultspl[0]['FTStyCode'];
            $tPNVatCode     = $tResultspl[0]['FTVatCode'];
            $tPNVatRate     = $tResultspl[0]['FCXrhVATRate'];
            $tFDXnhDocDate  = $tResultspl[0]['FDXrhDocDate'];
            $tFDXnhBchReturn= $tResultspl[0]['FDXrhBchReturn'];

            //ประเภทผู้จำหน่าย
            $FTXthDocType   = 5;

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
                            TACTPrDT.FCXrdQty,
                            TCNMPdt.FTPdtStaReturn
                            FROM TCNMPdt
                            LEFT JOIN TCNMPdtBar On TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode 
                            LEFT JOIN TACTPtDT ON TACTPtDT.FTPdtCode = TCNMPdt.FTPdtCode 
                            LEFT JOIN TCNMPdtUnit (NOLOCK) ON TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode 
                            LEFT JOIN TACTPrDT ON TCNMPdt.FTPdtCode = TACTPrDT.FTPdtCode
                            WHERE 1=1 
                            AND TCNMPdt.FTPdtType IN('1','4') 
                            AND TCNMPdt.FTPdtStaSet IN('1','2','3')
                            --AND TCNMPdt.FTPdtStaReturn IN('1')
                            AND TCNMPdt.FTPdtCode = '$pnPDTCode'
                            AND TACTPrDT.FTXrhDocNo = '$tDocumentPN'  ";
            $oPDTCode  = $this->DB_SELECT($tSQLPDT);
            
            if(!empty($oPDTCode) && $oPDTCode[0]['FTPdtStaReturn'] == '1'){
                //LOOP Insert for browser :: PDT
                for($i=0; $i<count($oPDTCode); $i++){
                
                    if($nVatSpl == 1){ //รวมใน

                        //Vat
                        $FCXtdVat = (1 * $oPDTCode[$i]['FCPdtCostStd']) - ((1 * $oPDTCode[$i]['FCPdtCostStd'] * 100)/(100+ $tPNVatRate));
                        if($FCXtdVat != 0){
                            $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdVat = 0;
                        }

                        //VatTable
                        $FCXtdVatable = (1 * $oPDTCode[$i]['FCPdtCostStd']) -  $FCXtdVat;
                        if($FCXtdVatable != 0){
                            $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdVatable = 0;
                        }

                        //ราคาต้นทุน
                        $FCXtdCostIn = 1 * $oPDTCode[$i]['FCPdtCostStd'];
                        if($FCXtdCostIn != 0){
                            $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdCostIn = 0;
                        }

                        //ราคาแยกนอก
                        $FCXtdCostEx =  $FCXtdVatable;

                    }else{ //แยกนอก

                        //Vat
                        $FCXtdVat = (1 * $oPDTCode[$i]['FCPdtCostStd']) * $tPNVatRate / 100;
                        if($FCXtdVat != 0){
                            $FCXtdVat = round($FCXtdVat, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdVat = 0;
                        }

                        //VatTable
                        $FCXtdVatable = 1 * $oPDTCode[$i]['FCPdtCostStd'];
                        if($FCXtdVatable != 0){
                            $FCXtdVatable = round($FCXtdVatable, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdVatable = 0;
                        }

                        //ราคาต้นทุน
                        $FCXtdCostIn = (1 * $oPDTCode[$i]['FCPdtCostStd']) + $FCXtdVat;
                        if($FCXtdCostIn != 0){
                            $FCXtdCostIn = round($FCXtdCostIn, 2 , PHP_ROUND_HALF_UP);
                        }else{
                            $FCXtdCostIn = 0;
                        }

                        //ราคาแยกนอก
                        $FCXtdCostEx = $oPDTCode[$i]['FCPdtCostStd'];
                    }

                    //ภาษีสรรพสามิต
                    $cUnitPrice     = $oPDTCode[$i]['FCPdtCostStd'] / $oPDTCode[$i]['FCPdtStkFac'];
                    if($oPDTCode[$i]['FCPdtLawControl'] == 0){
                        $FCXtdExcDuty = 0;
                    }else{
                        $FCXtdExcDuty = ($oPDTCode[$i]['FCPdtLawControl'] - ($oPDTCode[$i]['FCPdtLawControl'] * 100 ) / (100 + $tPNVatRate)) - ( $cUnitPrice - ($cUnitPrice * 100)/(100 + $tPNVatRate));
                        $FCXtdExcDuty = round($FCXtdExcDuty,2);
                    }

                    $aDataInsertDT  = array(
                        'FTBchCode'             => $aGetBranch['FTBchCode'],
                        'FTXthDocNo'            => $tFormatCode,
                        'FNXtdSeqNo'            => $nSeq+1,
                        'FTPdtCode'             => $oPDTCode[$i]['FTPdtCode'],
                        'FTPdtName'             => $oPDTCode[$i]['FTPdtName'],
                        'FTXthDocType'          => $FTXthDocType,
                        'FDXthDocDate'          => date('Y-m-d'),
                        'FTXthVATInOrEx'        => $nVatSpl,
                        'FTXtdBarCode'          => $oPDTCode[$i]['FTPdtBarCode'], 
                        'FTXtdStkCode'          => $oPDTCode[$i]['FTPdtStkCode'],
                        'FCXtdStkFac'           => $oPDTCode[$i]['FCPdtStkFac'],
                        'FTXtdVatType'          => $oPDTCode[$i]['FTPdtVatType'],
                        'FTXtdSaleType'         => $oPDTCode[$i]['FTPdtSaleType'],
                        'FTPgpChain'            => $oPDTCode[$i]['FTPgpChain'],   
                        'FTSrnCode'             => 'NULL',
                        'FTPmhCode'             => 'NULL',
                        'FTPmhType'             => 'NULL', 
                        'FTPunCode'             => $oPDTCode[$i]['FTPunCode'],      
                        'FTXtdUnitName'         => $oPDTCode[$i]['FTPunName'],
                        'FCXtdFactor'           => $oPDTCode[$i]['FCPdtStkFac'],
                        'FCXtdSalePrice'        => $oPDTCode[$i]['FCPdtRetPri1'],
                        'FCXtdQty'              => $oPDTCode[$i]['FCXrdQty'] ,
                        'FCXtdSetPrice'         => $oPDTCode[$i]['FCPdtCostStd'],
                        'FCXtdB4DisChg'         => $oPDTCode[$i]['FCXrdQty'] * $oPDTCode[$i]['FCPdtCostStd'],
                        'FTXtdDisChgTxt'        => '',
                        'FCXtdDis'              => '',
                        'FCXtdChg'              => '',
                        'FCXtdNet'              => $oPDTCode[$i]['FCXrdQty'] * $oPDTCode[$i]['FCPdtCostStd'],
                        'FCXtdVat'              => $FCXtdVat,
                        'FCXtdVatable'          => $FCXtdVatable,
                        'FCXtdQtyAll'           => $oPDTCode[$i]['FCXrdQty'],
                        'FCXtdCostIn'           => $FCXtdCostIn,
                        'FCXtdCostEx'           => $FCXtdCostEx,
                        'FTXtdStaPdt'           => 1,
                        'FTXtdStaRfd'           => 1,
                        'FTXtdStaPrcStk'        => 'NULL',      
                        'FNXthSign'             => 0,
                        'FTAccCode'             => $tFTAccCode,
                        'FNXtdPdtLevel'         => 0,     
                        'FTXtdPdtParent'        => $oPDTCode[$i]['FTPdtCode'],
                        'FTXtdApOrAr'           => $oPDTCode[$i]['FTSplCode'],
                        'FTWahCode'             => $oWahCode[0]['FTSysUsrValue'],
                        'FNXtdStaRef'           => 0,
                        'FCXtdQtySet'           => 'NULL', 
                        'FTPdtStaSet'           => $oPDTCode[$i]['FTPdtStaSet'],
                        'FDXtdExpired'          => 'NULL',  
                        'FTXtdLotNo'            => 1,
                        'FCXtdQtyLef'           => $oPDTCode[$i]['FCXrdQty'],
                        'FCXtdQtyRfn'           => 'NULL', 
                        'FTXthStaVatSend'       => 1,
                        'FTPdtArticle'          => $oPDTCode[$i]['FTPdtArticle'],
                        'FTDcsCode'             => $oPDTCode[$i]['FTDcsCode'],
                        'FTPszCode'             => $oPDTCode[$i]['FTPszCode'],
                        'FTClrCode'             => $oPDTCode[$i]['FTClrCode'],
                        'FTPszName'             => 0, 
                        'FTClrName'             => 0, 
                        'FCPdtLeftPO'           => 'NULL', 
                        'FTCpnCode'             => 'NULL', 
                        'FCXtdQtySale'          => 'NULL', 
                        'FCXtdQtyRet'           => 'NULL', 
                        'FCXtdQtyCN'            => 'NULL', 
                        'FCXtdQtyAvi'           => 'NULL', 
                        'FCXtdQtySgg'           => 'NULL', 
                        'FTXthBchFrm'           => 'NULL',       
                        'FTXthBchTo'            => 'NULL',       
                        'FTXthWahFrm'           => 'NULL', 
                        'FTXthWahTo'            => 'NULL', 
                        'FCXthDiscGP1'          => 'NULL', 
                        'FCXthDiscGP2'          => 'NULL', 
                        'FCXtdB4VatAfGP1'       => 'NULL', 
                        'FCXtdB4VatAfGP2'       => 'NULL', 
                        'FCXtdDisShp'           => 'NULL', 
                        'FCXtdShrDisShp'        => 'NULL', 
                        'FTXtdTaxInv'           => 'NULL', 
                        'FTPdtNoDis'            => $oPDTCode[$i]['FTPdtNoDis'],
                        'FCXtdDisAvg'           => 'NULL', 
                        'FCXtdFootAvg'          => 'NULL', 
                        'FCXtdRePackAvg'        => 'NULL', 
                        'FCPdtLawControl'       => $oPDTCode[$i]['FCPdtLawControl'],
                        'FCXtdExcDuty'          => $FCXtdExcDuty,
                        'FTPdtSaleType'         => $oPDTCode[$i]['FTPdtSaleType'],
                        'FCPdtMax'              => 'NULL', 
                        'FDPdtOrdStart'         => 'NULL', 
                        'FDPdtOrdStop'          => 'NULL', 
                        'FTXtdPdtKey'           => 'NULL', 
                        'FTPmhDocNoBill'        => 'NULL', 
                        'FTXtdPmhCpnDocNo'      => 'NULL', 
                        'FCXtdPmhCpnGetQty'     => 'NULL', 
                        'FCXtdPmhCpnValue'      => 'NULL', 
                        'FCXtdDisGP'            => 'NULL', 
                        'FCXtdPmtQtyGet'        => 'NULL',       
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
            $aReturnData = [$tFormatCode,$tSPLCode,$tSPLType,$tFDXnhDocDate,$oPDTCode[0]['FTPdtBarCode'],$oPDTCode[0]['FTPdtName'],$oPDTCode[0]['FTPdtStaReturn']]; //$tResult,$tSQLPDT
            return $aReturnData;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Delete Item DT 
    public function FSaMPURDeletePDTByPUR1($tDocumentID){
        try {
            $tDatabase          = "TACTPtDT";
            $aDataDeleteWHERE   = array(
                'FTXthDocNo'    => $tDocumentID 
            );

            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Create By Napat(Jame) 12/03/63
    //Function Get Reason Code in Config
    public function FSaMPURGetConfigReason(){
        try {
            $tSQL = "   SELECT TOP 1
                            CASE WHEN ISNULL(FTSysUsrValue,'') = '' THEN FTSysDefValue ELSE FTSysUsrValue END AS FTSysValue
                        FROM TSysConfig WITH(NOLOCK) 
                        WHERE FTSysCode = 'AlwPcDoc'
                    ";

            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery[0];
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    // Create By : Napat(Jame) 2020-06-30
    // เขียนไฟล์ Log : หน้าจอใบลดหนี้
    public function FSxMPCNWriteLog($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'PCN_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

}

?>