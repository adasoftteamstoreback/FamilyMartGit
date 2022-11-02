<?php

class morderingscreen extends Database {
    
    public function __construct(){
        parent::__construct();
        ini_set("max_execution_time", 0);
    }

    public function FSxMODSDataList($paData){
        $tSection   = $paData['tSection'];
        $tDocNo     = $paData['tDocNo'];
        $aRowLen    = FCNaHCallLenData($paData['nRow'],$paData['nPage']);

        $tSQL  = "SELECT c.* FROM(
                    SELECT ROW_NUMBER() OVER(";

        if($paData['aSortBy'][0] == ""){
            if($tSection != "NEW" && $tSection != "ADDON" && $tSection != "PROMOTION"){
                $tSQL .= " ORDER BY FCPdtADS DESC";
            }else if($tSection == "PROMOTION"){
                $tSQL .= " ORDER BY PRO_Type,PRO_Mounth,PRO_Day ASC, FCPdtADS DESC"; //เรียงตามยอดขายเฉลี่ยต่อวันมากไปน้อยกรณีที่วันเริ่มโปรโมชั่นเป็นวันเดียวกัน Comsheet 2019 316
            }else{
                $tSQL .= " ORDER BY FDDateIns, FTTimeIns, FNXdtSeqNo ASC";
            }
        }else{
            if($paData['aSortBy'][0] == "FTPdtPromo"){
                $tSQL .= " ORDER BY PRO_Type ".$paData['aSortBy'][1].",PRO_Mounth ".$paData['aSortBy'][1].",PRO_Day ".$paData['aSortBy'][1].", FCPdtADS DESC";
            }else{
                $tSQL .= " ORDER BY " . $paData['aSortBy'][0] . " " . $paData['aSortBy'][1];
            }
        }
        
        $tSQL .= " ) AS rtRowID , * FROM
                    (SELECT  
                        CASE 
                            WHEN SUBSTRING(POD.FTPdtPromo,5,8) = 'ปัจจุบัน' THEN 1
                            WHEN SUBSTRING(POD.FTPdtPromo,5,8) = 'รอบถัดไป' THEN 2
                        END AS PRO_Type,
                        SUBSTRING(POD.FTPdtPromo,14,2) AS PRO_Day,
                        SUBSTRING(POD.FTPdtPromo,17,2) AS PRO_Mounth,
                        POD.FTXohDocNo,
                        POD.FNXdtSeqNo,
                        POD.FTPdtSecCode,
                        POD.FTPdtCategory,
                        POD.FTPdtSubCat,
                        POD.FTPdtCode,
                        POD.FTPdtName,
                        POD.FTPdtBarCode,
                        POD.FTPdtDelivery,
                        POD.FCPdtIntransit,
                        POD.FTPdtPromo,
                        POD.FDDeliveryDate,
                        POD.FCPdtStock,
                        POD.FCPdtLotSize,
                        POD.FCPdtADS,
                        POD.FCPdtSGOQty,
                        
                        CASE
                            WHEN POD.FTPdtPOFlag = '1' THEN NULL
                            ELSE POD.FCPdtOrdLot
                        END AS FCPdtOrdLot,

                        CASE
                            WHEN POD.FTPdtPOFlag = '1' THEN NULL
                            ELSE POD.FCPdtOrdPcs
                        END AS FCPdtOrdPcs,

                        POD.FCPdtOrdLot AS FCPdtOrdLot_Tmp,
                        POD.FCPdtOrdPcs AS FCPdtOrdPcs_Tmp,

                        POD.FTPdtPOFlag,
                        POD.FTPdtSecStatus,
                        POD.FDDateIns,
                        POD.FTTimeIns
                    FROM TSPoDT POD WITH (NOLOCK)
                    LEFT JOIN TCNMPdt P WITH(NOLOCK) ON POD.FTPdtCode = P.FTPdtCode
                    WHERE 1=1
                    AND P.FTPdtType <> '7'
        ";

        if($tDocNo != ""){
            $tSQL .= " AND POD.FTXohDocNo = '$tDocNo'";
        }else{
            $tSQL .= " AND POD.FTXohDocNo = ''";
        }

        if($tSection != "SUMMARY"){
            $tSQL .= " AND POD.FTPdtSecCode = '$tSection'";
        }

        $tDisableRow = $paData['tDisableRow'];
        if($tDisableRow == true){
            $tSQL .= " AND POD.FCPdtOrdLot IS NOT NULL";
            if($tSection != "ADDON"){
                $tSQL .= " AND POD.FTPdtPOFlag != '1'";
            }
        }

        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $aDataList = $this->DB_SELECT($tSQL);

        if(count($aDataList) > 0){
            $aGetData = array(
                'tDocNo'        => $tDocNo,
                'tSection'      => $tSection,
                'tDisableRow'   => $tDisableRow
            );
            // $aFoundOrdLot   = $this->FSaMODSGetOrdLot($aGetData);
            $aFoundRow      = $this->FSaMODSGetPageList($aGetData);
            $aOrderSKU      = $this->FSaMODSGetOrderSKU($aGetData);
            $nFoundRow      = $aFoundRow['counts'];
            $nPageAll       = ceil($nFoundRow/$paData['nRow']);
            // print_r($aOrderSKU[0]['OrderSKU']);
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aGetData'          => $aGetData,
                'raItems'           => $aDataList,
                'rnAllRow'          => $nFoundRow,
                // 'rnFoundOrdLot'     => $aFoundOrdLot['counts'],
                'rnOrderSKU'        => $aOrderSKU['OrderSKU'],
                'rnSKUAmount'       => $aOrderSKU['SKUAmount'],
                'rnCurrentPage'     => $paData['nPage'],
                "rnAllPage"         => $nPageAll,
                'nStaQuery'         => '1',
                'tStaMessage'       => 'Select Data from TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                // 'rnFoundOrdLot'     => 0,
                'raItems'           => $aDataList,
                'nStaQuery'         => '99',
                'tStaMessage'       => 'Select Data from TSPoDT Unsuccessful',
            );
        }

        return $aDataResult;

    }

    //Select count 
    public function FSaMODSGetPageList($paData){
        $tDocNo         = $paData['tDocNo'];
        $tSection       = $paData['tSection'];

        $tSQL           = " SELECT 
                                COUNT (POD.FTXohDocNo) AS counts
                            FROM TSPoDT POD WITH (NOLOCK)
                            WHERE 1=1 ";
        if($tDocNo != ""){
            $tSQL .= " AND FTXohDocNo = '$tDocNo'";
        }else{
            $tSQL .= " AND FTXohDocNo = ''";
        }

        if($tSection != ""){
            $tSQL .= " AND FTPdtSecCode = '$tSection'";
        }

        $tDisableRow = $paData['tDisableRow'];
        if($tDisableRow == true){
            $tSQL .= " AND FCPdtOrdLot IS NOT NULL";
            $tSQL .= " AND FTPdtPOFlag != '1'";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }

    }

    public function FSaMODSCheckOrdLot($paData){
        $tDocNo         = $paData['tDocNo'];
        $tSQL           = "SELECT COUNT(POD.FCPdtOrdLot) AS counts
                            FROM TSPoDT POD  WITH (NOLOCK)
                            WHERE POD.FCPdtOrdLot IS NOT NULL";
        
        if($tDocNo != ""){
            $tSQL .= " AND POD.FTXohDocNo = '$tDocNo'";
        }else{
            $tSQL .= " AND POD.FTXohDocNo = ''";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0]['counts'];
        }else{
            return false;
        }
    }

    public function FSaMODSGetOrderSKU($paData){
        $tDocNo         = $paData['tDocNo'];
        $tSection       = $paData['tSection'];
        $tSQL = "SELECT 
                    COUNT(POD.FTXohDocNo)                   AS OrderSKU, 
                    SUM(POD.FCPdtOrdLot * POD.FCPdtCost)    AS SKUAmount
                 FROM 
                    TSPoDT POD WITH (NOLOCK)
                 WHERE POD.FCPdtOrdLot >= 0 
                   AND POD.FCPdtOrdLot IS NOT NULL
                   AND POD.FTPdtPOFlag != 1";
        
        if($tDocNo != ""){
            $tSQL .= " AND FTXohDocNo = '$tDocNo'";
        }else{
            $tSQL .= " AND FTXohDocNo = ''";
        }

        if($tSection != ""){
            $tSQL .= " AND FTPdtSecCode = '$tSection'";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    public function FSxMODSGetNewPdtDay(){
        $tSQL  = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig (NOLOCK) WHERE FTSysCode='NewPdtDay'";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0]['FTSysUsrValue'];
        }else{
            return false;
        }
    }

    //STEP 1
    public function FSxMODSCallSP_FTHSGO($paData){
        $pOrderDate   = $paData['dDateCurrent'];

        $tSQL1 = "{CALL FTHSGO(?)}";
        $aParams1 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore1 = $this->DB_EXECUTE($tSQL1,$aParams1);
        if($tQueryReturnStore1 == NULL){
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore1,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSCallSP_FTHSGO] Call Stored FTHSGO'
            );
        }else{
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore1,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSCallSP_FTHSGO] '.$tQueryReturnStore1[0]['message']
            );
        }
        $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    //STEP 2
    public function FSxMODSCallSTP_PRCxGetPdtPO1($paData){
        $pOrderDate   = $paData['dDateCurrent'];

        $tSQL2 = "{CALL STP_PRCxGetPdtPO(?,?,?)}";
        $aParams2 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN),
            array('088', SQLSRV_PARAM_IN),
            array(1, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore2 = $this->DB_EXECUTE($tSQL2,$aParams2);
        if($tQueryReturnStore2 == NULL){
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore2,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSCallSTP_PRCxGetPdtPO1] Call Stored STP_PRCxGetPdtPO (Type 1)'
            );
        }else{
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore2,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSCallSTP_PRCxGetPdtPO1] '.$tQueryReturnStore2[0]['message']
            );
        }
        $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    //STEP 3
    public function FSxMODSCallSTP_PRCxGetPdtPO2($paData){
        $pOrderDate   = $paData['dDateCurrent'];

        $tSQL3 = "{CALL STP_PRCxGetPdtPO(?,?,?)}";
        $aParams3 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN),
            array('088', SQLSRV_PARAM_IN),
            array(0, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore3 = $this->DB_EXECUTE($tSQL3,$aParams3);

        if($tQueryReturnStore3 == NULL){
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore3,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSCallSTP_PRCxGetPdtPO2] Call Stored STP_PRCxGetPdtPO (Type 0)'
            );
        }else{
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore3,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSCallSTP_PRCxGetPdtPO2] '.$tQueryReturnStore2[0]['message']
            );
        }
        $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSxMODSAddDocTmpDT($paData){
        // ini_set("max_execution_time", 999);
        // $dDate          = $paData['dDate'];
        // $tTime          = $paData['tTime'];
        // $tUser          = $paData['tUser'];
        // $dCurentDay     = $paData['dCurentDay'];
        // $dDateOrder     = $paData['dDateOrder'];
        // $dDateCurOrd    = $paData['dDateCurrent'];

        // $aNewDate  = explode("-",$dDateCurOrd);
        // $tFormatDate  = $aNewDate[1] . '/' . $aNewDate[2] . '/' . $aNewDate[0];
        // $pOrderDate   = $tFormatDate;
        $pOrderDate   = $paData['dDateCurrent'];

        // print_r($dDateCurOrd);
        // print_r($pOrderDate);
        // print_r($aNewDate);
        // exit;


        $tSQL1 = "{CALL FTHSGO(?)}";
        $aParams1 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore1 = $this->DB_EXECUTE($tSQL1,$aParams1);

        $tSQL2 = "{CALL STP_PRCxGetPdtPO(?,?,?)}";
        $aParams2 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN),
            array('088', SQLSRV_PARAM_IN),
            array(1, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore2 = $this->DB_EXECUTE($tSQL2,$aParams2);

        $tSQL3 = "{CALL STP_PRCxGetPdtPO(?,?,?)}";
        $aParams3 = array( 
            array($pOrderDate, SQLSRV_PARAM_IN),
            array('088', SQLSRV_PARAM_IN),
            array(0, SQLSRV_PARAM_IN)
        );
        $tQueryReturnStore3 = $this->DB_EXECUTE($tSQL3,$aParams3);

        if($tQueryReturnStore2 == NULL){
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore2,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Add Data to TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                'tReturnInsert' => $tQueryReturnStore2,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not fround Data',
            );
        }
        return $aDataResult;

        // $tNewPdtDay = $this->FSxMODSGetNewPdtDay();

        // switch ($dCurentDay) {
        //     case 0:
        //         $tADS               = "ISNULL(W.FCSaleQtySun,0)";
        //         $tPdtOrd            = "P.FTPdtOrdSun = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDSun IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDSun, '$dDateCurOrd') END";
        //         break;
        //     case 1:
        //         $tADS               = "ISNULL(W.FCSaleQtyMon,0)";
        //         $tPdtOrd            = "P.FTPdtOrdMon = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDMon IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDMon, '$dDateCurOrd') END";
        //         break;
        //     case 2:
        //         $tADS               = "ISNULL(W.FCSaleQtyTue,0)";
        //         $tPdtOrd            = "P.FTPdtOrdTue = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDTue IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDTue, '$dDateCurOrd') END";
        //         break;
        //     case 3:
        //         $tADS               = "ISNULL(W.FCSaleQtyWed,0)";
        //         $tPdtOrd            = "P.FTPdtOrdWed = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDWed IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDWed, '$dDateCurOrd') END";
        //         break;
        //     case 4:
        //         $tADS               = "ISNULL(W.FCSaleQtyThu,0)";
        //         $tPdtOrd            = "P.FTPdtOrdThu = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDThu IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDThu, '$dDateCurOrd') END";
        //         break;
        //     case 5:
        //         $tADS               = "ISNULL(W.FCSaleQtyFri,0)";
        //         $tPdtOrd            = "P.FTPdtOrdFri = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDFri IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDFri, '$dDateCurOrd') END";
        //         break;
        //     case 6:
        //         $tADS               = "ISNULL(W.FCSaleQtySat,0)";
        //         $tPdtOrd            = "P.FTPdtOrdSat = '1'";
        //         $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDSat IS NULL THEN '$dDateCurOrd' ELSE DATEADD(day, S.FNLTDSat, '$dDateCurOrd') END";
        //         break;
        // }

        // $tSQL  = "INSERT INTO TSPoDT (FNXdtSeqNo,FTXohDocNo,FTPdtSecCode,FTPdtCategory,FTPdtSubCat,FTPdtCode,FTPdtName,FTPdtBarCode,FTPdtDelivery,FCPdtIntransit,FCPdtCost,FCPdtPrice,FTPdtPromo,FDDeliveryDate,FCPdtStock,FCPdtLotSize,FCPdtADS,FCPdtSGOQty,FCPdtOrdLot,FCPdtOrdPcs,FTSplCode,FTVatCode,FTPdtPOFlag,FDDateUpd,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns)";
        // $tSQL .= "SELECT ROW_NUMBER() OVER(ORDER BY S.PRODUCT_CODE) AS FNXdtSeqNo,* FROM (";
        // // $tSQL0 = "DROP VIEW [VTSPoDT]";
        // // $this->DB_EXECUTE($tSQL0);
        // // $tSQL  = "CREATE VIEW [VTSPoDT] AS (";
        // $tSQL .= "  SELECT ''                                                               AS FTXohDocNo,
        //                 CASE WHEN C.FTItemStatus='T' THEN 'TOP1000' WHEN C.FTItemStatus='O' THEN 'OTHER' END AS SECTION,
        //                 G.FTPgpName                                                         AS CATEGORY,
        //                 G1.FTPgpName                                                        AS SUBCAT,
        //                 P.FTPdtCode                                                         AS PRODUCT_CODE,
        //                 P.FTPdtName                                                         AS PRODUCT_NAME,
        //                 CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=C.FTPdtCode) ELSE B.FTPdtBarCode END AS BARCODE,
        //                 T.FTStyName                                                         AS DELIVERY_TYPE,
        //                 CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END         AS IN_TRANSIT,
        //                 P.FCPdtCostStd                                                      AS Cost,
        //                 B.FCPdtRetPri1                                                      AS PRICE,
        //                 ''                                                                  AS PROMO,
        //                 $tDELIVERY_DATE2                                                    AS DELIVERY_DATE,
        //                 P.FCPdtQtyRet                                                       AS STOCK,
        //                 FCPdtStkFac                                                         AS LOT_SIZE,
        //                 $tADS                                                               AS ADS,
        //                 CEILING(CASE WHEN C.FNSGOQTy >0 THEN C.FNSGOQty ELSE ISNULL(O.FCSugQty,0) END / FCPdtStkFac) AS SGO_QTY_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_PCS,
        //                 P.FTSplCode                                                         AS FTSplCode,
        //                 SPL.FTSplViaRmk                                                     AS FTVatCode,
        //                 CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
        //                 '$dDate'                                                            AS FDDateUpd,
        //                 '$tTime'                                                            AS FTTimeUpd,
        //                 '$tUser'                                                            AS FTWhoUpd,
        //                 '$dDate'                                                            AS FDDateIns,
        //                 '$tTime'                                                            AS FTTimeIns,
        //                 '$tUser'                                                            AS FTWhoIns
        //             FROM TCNTSGOItem C              WITH (NOLOCK)
        //             LEFT JOIN TCNMPdt P             WITH (NOLOCK) ON P.FTPdtCode=C.FTPdtCode
        //             LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode=P.FTPdtCode AND B.FTPdtBarCode = C.FTPdtBarCode
        //             LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND CONVERT(VARCHAR(10),A.FDOrderDate,121)='$dDateCurOrd'
        //             LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1'
        //             LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4'
        //             -- LEFT JOIN TCNTPdtPmtDT M        WITH (NOLOCK) ON M.FTPdtCode=C.FTPdtCode
        //             -- LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND H.FDPmhDStop>='$dDateCurOrd'
        //             LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode
        //             LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode
        //             LEFT JOIN TSysConfig F          WITH (NOLOCK) ON F.FTSysCode='SugOrder'
        //             WHERE CONVERT(VARCHAR(10),C.FDOrderDate,121) = '$dDateOrder' 
        //             AND P.FCPdtStkFac != 0
        //             AND P.FTPdtCode IS NOT NULL 
        //             AND P.FTPdtName NOT IN (SELECT FTPdtName FROM TCNTPdtPmtDT WHERE CONVERT(VARCHAR(10),FDPmhDStop,121)>='$dDateCurOrd') 
        //             AND CONVERT(VARCHAR(10),P.FDPdtSaleStart,121) < DATEADD(day, -$tNewPdtDay, '$dDateCurOrd') 
        //             AND $tPdtOrd
        //             AND P.FTPdtStaAlwBuy='1'
        //             AND T.FTStyName!='Result'

        //             -- AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN (SELECT MAX(FTPthDocNo) AS FTPthDocNo 
        //             -- FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND CONVERT(VARCHAR(10),FDPdtEnddate,121)>='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN 
        //             (SELECT MAX(FTPthDocNo) AS FTPthDocNo FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND '$dDateCurOrd' BETWEEN CONVERT(VARCHAR(10),FDPdtStartdate,121) AND CONVERT(VARCHAR(10),FDPdtEnddate,121))
        //             AND ((F.FTSysUsrValue='0' AND O.FCSugQty>0) OR F.FTSysUsrValue='1') AND (C.FTItemStatus<> '' OR C.FTItemStatus != null)

        //             UNION

        //             SELECT ''                                                               AS FTXohDocNo,
        //                 'PROMOTION'                                                         AS SECTION,
        //                 G.FTPgpName                                                         AS CATEGORY,
        //                 G1.FTPgpName                                                        AS SUBCAT,
        //                 P.FTPdtCode                                                         AS PRODUCT_CODE,
        //                 P.FTPdtName                                                         AS PRODUCT_NAME,
        //                 CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=P.FTPdtCode) ELSE B.FTPdtBarCode END AS BARCODE,
        //                 T.FTStyName                                                         AS DELIVERY_TYPE,
        //                 CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END         AS IN_TRANSIT,
        //                 P.FCPdtCostStd                                                      AS Cost,
        //                 B.FCPdtRetPri1                                                      AS PRICE,

        //                 CASE WHEN ('$dDateCurOrd' >= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN                                             
        //                 'โปร ปัจจุบัน ' +SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 WHEN ('$dDateCurOrd' <=  CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN 
        //                 'โปร รอบถัดไป '+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 ELSE '' END AS PROMO,

        //                 $tDELIVERY_DATE2                                                    AS DELIVERY_DATE,
        //                 P.FCPdtQtyRet                                                       AS STOCK,
        //                 FCPdtStkFac                                                         AS LOT_SIZE,
        //                 $tADS                                                               AS ADS,
        //                 CEILING(ISNULL(O.FCSugQty,0) / FCPdtStkFac)                         AS SGO_QTY_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_PCS,
        //                 P.FTSplCode                                                         AS FTSplCode,
        //                 SPL.FTSplViaRmk                                                     AS FTVatCode,
        //                 CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
        //                 '$dDate'                                                            AS FDDateUpd,
        //                 '$tTime'                                                            AS FTTimeUpd,
        //                 '$tUser'                                                            AS FTWhoUpd,
        //                 '$dDate'                                                            AS FDDateIns,
        //                 '$tTime'                                                            AS FTTimeIns,
        //                 '$tUser'                                                            AS FTWhoIns
        //             FROM TCNMPdt P                  WITH (NOLOCK) 
        //             LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND CONVERT(VARCHAR(10),A.FDOrderDate,121)='$dDateCurOrd'
        //             LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1'
        //             LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4'
        //             LEFT JOIN TCNTPdtPmtDT M        WITH (NOLOCK) ON M.FTPdtName=P.FTPdtName
        //             LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND CONVERT(VARCHAR(10),H.FDPmhDStop,121)>='$dDateCurOrd'
        //             LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode
        //             LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode
        //             LEFT JOIN TSysConfig F          WITH (NOLOCK) ON F.FTSysCode='SugOrder'
        //             WHERE CONVERT(VARCHAR(10),H.FDPmhDStop,121) >='$dDateCurOrd' 
        //             AND H.FTPmhClosed=0
        //             AND P.FCPdtStkFac != 0
        //             AND $tPdtOrd
        //             AND P.FTPdtStaAlwBuy='1' 
        //             AND T.FTStyName!='Result'

        //             AND CONVERT(VARCHAR(10),P.FDPdtSaleStart,121) < DATEADD(day, -$tNewPdtDay, '$dDateCurOrd')
        //             AND ('$dDateCurOrd'>=CONVERT(VARCHAR(10),FDPdtOrdStart,121) AND '$dDateCurOrd'<=CONVERT(VARCHAR(10),P.FDPdtOrdStop,121))
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTSGOItem WHERE CONVERT(VARCHAR(10),FDOrderDate,121)='$dDateCurOrd')
                    
        //             -- AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN (SELECT MAX(FTPthDocNo) AS FTPthDocNo 
        //             -- FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND CONVERT(VARCHAR(10),FDPdtEnddate,121)>='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN 
        //             (SELECT MAX(FTPthDocNo) AS FTPthDocNo FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND '$dDateCurOrd' BETWEEN CONVERT(VARCHAR(10),FDPdtStartdate,121) AND CONVERT(VARCHAR(10),FDPdtEnddate,121))
        //             AND ((F.FTSysUsrValue='0' AND O.FCSugQty>0) OR F.FTSysUsrValue='1')

        //             GROUP BY G.FTPgpName,G1.FTPgpName,P.FTPdtName,B.FTPdtBarCode,T.FTStyName,A.FC_T1,A.FC_T2,
        //             B.FCPdtRetPri1,S.FNLTDSUN,P.FCPdtQtyRet,O.FCSugQty,FCPdtStkFac,P.FCPdtCostStd,
        //             P.FTPdtCode,P.FTSplCode,SPL.FTSplViaRmk,S.FNLTDSun,S.FNLTDMon,S.FNLTDTue,S.FNLTDWed,
        //             S.FNLTDThu,S.FNLTDFri,S.FNLTDSat,W.FCSaleQtySun,W.FCSaleQtyMon,W.FCSaleQtyTue,W.FCSaleQtyWed,
        //             W.FCSaleQtyThu,W.FCSaleQtyFri,W.FCSaleQtySat,H.FDPmhDStart,H.FDPmhDStop

        //             UNION 

        //             SELECT ''                                                               AS FTXohDocNo,
        //                 'PROMOTION'                                                         AS SECTION,
        //                 G.FTPgpName                                                         AS CATEGORY,
        //                 G1.FTPgpName                                                        AS SUBCAT,
        //                 P.FTPdtCode                                                         AS PRODUCT_CODE,
        //                 P.FTPdtName                                                         AS PRODUCT_NAME,
        //                 CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=P.FTPdtCode) ELSE B.FTPdtBarCode END AS BARCODE,
        //                 T.FTStyName                                                         AS DELIVERY_TYPE,
        //                 CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END         AS IN_TRANSIT,
        //                 P.FCPdtCostStd                                                      AS Cost,
        //                 B.FCPdtRetPri1                                                      AS PRICE,

        //                 CASE WHEN ('$dDateCurOrd' >= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN                                             
        //                 'โปร ปัจจุบัน ' +SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 WHEN ('$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN 
        //                 'โปร รอบถัดไป '+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 ELSE '' END AS PROMO,

        //                 $tDELIVERY_DATE2                                                    AS DELIVERY_DATE,
        //                 P.FCPdtQtyRet                                                       AS STOCK,
        //                 FCPdtStkFac                                                         AS LOT_SIZE,
        //                 $tADS                                                               AS ADS,
        //                 CEILING(ISNULL(O.FCSugQty,0)  / FCPdtStkFac)                        AS SGO_QTY_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_PCS,
        //                 P.FTSplCode                                                         AS FTSplCode,
        //                 SPL.FTSplViaRmk                                                     AS FTVatCode,
        //                 CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
        //                 '$dDate'                                                            AS FDDateUpd,
        //                 '$tTime'                                                            AS FTTimeUpd,
        //                 '$tUser'                                                            AS FTWhoUpd,
        //                 '$dDate'                                                            AS FDDateIns,
        //                 '$tTime'                                                            AS FTTimeIns,
        //                 '$tUser'                                                            AS FTWhoIns
        //             FROM TCNMPdt P                  WITH (NOLOCK) 
        //             LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND CONVERT(VARCHAR(10),A.FDOrderDate,121)='$dDateCurOrd'
        //             LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1'
        //             LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4'
        //             LEFT JOIN TCNTPdtPmtCD M        WITH (NOLOCK) ON M.FTPdtName=P.FTPdtName AND M.FCPmcOrgPri=B.FCPdtRetPri1
        //             LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND CONVERT(VARCHAR(10),H.FDPmhDStop,121)>='$dDateCurOrd'
        //             LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode
        //             LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode
        //             LEFT JOIN TSysConfig F          WITH (NOLOCK) ON F.FTSysCode='SugOrder'
        //             WHERE CONVERT(VARCHAR(10),H.FDPmhDStop,121) >='$dDateCurOrd'
        //             AND H.FTPmhClosed=0
        //             AND P.FCPdtStkFac != 0
        //             AND $tPdtOrd
        //             AND P.FTPdtStaAlwBuy='1' 
        //             AND T.FTStyName!='Result'
        //             AND CONVERT(VARCHAR(10),P.FDPdtSaleStart,121) < DATEADD(day, -$tNewPdtDay, '$dDateCurOrd')
        //             AND ('$dDateCurOrd'>=CONVERT(VARCHAR(10),FDPdtOrdStart,121) AND '$dDateCurOrd'<=CONVERT(VARCHAR(10),P.FDPdtOrdStop,121))
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTSGOItem WHERE CONVERT(VARCHAR(10),FDOrderDate,121)='$dDateCurOrd')
                    
        //             -- AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN (SELECT MAX(FTPthDocNo) AS FTPthDocNo 
        //             -- FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND CONVERT(VARCHAR(10),FDPdtEnddate,121)>='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN 
        //             (SELECT MAX(FTPthDocNo) AS FTPthDocNo FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND '$dDateCurOrd' BETWEEN CONVERT(VARCHAR(10),FDPdtStartdate,121) AND CONVERT(VARCHAR(10),FDPdtEnddate,121))
        //             AND ((F.FTSysUsrValue='0' AND O.FCSugQty>0) OR F.FTSysUsrValue='1')

        //             GROUP BY G.FTPgpName,G1.FTPgpName,P.FTPdtName,B.FTPdtBarCode,T.FTStyName,A.FC_T1,A.FC_T2,
        //             B.FCPdtRetPri1,S.FNLTDSUN,P.FCPdtQtyRet,O.FCSugQty,FCPdtStkFac,P.FCPdtCostStd,
        //             P.FTPdtCode,P.FTSplCode,SPL.FTSplViaRmk,S.FNLTDSun,S.FNLTDMon,S.FNLTDTue,S.FNLTDWed,
        //             S.FNLTDThu,S.FNLTDFri,S.FNLTDSat,W.FCSaleQtySun,W.FCSaleQtyMon,W.FCSaleQtyTue,W.FCSaleQtyWed,
        //             W.FCSaleQtyThu,W.FCSaleQtyFri,W.FCSaleQtySat,H.FDPmhDStart,H.FDPmhDStop

        //             UNION 

        //             SELECT ''                                                               AS FTXohDocNo,
        //                 'PROMOTION'                                                         AS SECTION,
        //                 G.FTPgpName                                                         AS CATEGORY,
        //                 G1.FTPgpName                                                        AS SUBCAT,
        //                 P.FTPdtCode                                                         AS PRODUCT_CODE,
        //                 P.FTPdtName                                                         AS PRODUCT_NAME,
        //                 CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=P.FTPdtCode) ELSE B.FTPdtBarCode END AS BARCODE,
        //                 T.FTStyName                                                         AS DELIVERY_TYPE,
        //                 CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END         AS IN_TRANSIT,
        //                 P.FCPdtCostStd                                                      AS Cost,
        //                 B.FCPdtRetPri1                                                      AS PRICE,

        //                 CASE WHEN ('$dDateCurOrd' >= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN                                             
        //                 'โปร ปัจจุบัน ' +SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 WHEN ('$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN 
        //                 'โปร รอบถัดไป '+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 ELSE '' END AS PROMO,

        //                 $tDELIVERY_DATE2                                                    AS DELIVERY_DATE,
        //                 P.FCPdtQtyRet                                                       AS STOCK,
        //                 FCPdtStkFac                                                         AS LOT_SIZE,
        //                 $tADS                                                               AS ADS,
        //                 CEILING(ISNULL(O.FCSugQty,0) / FCPdtStkFac)                         AS SGO_QTY_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_PCS,
        //                 P.FTSplCode                                                         AS FTSplCode,
        //                 SPL.FTSplViaRmk                                                     AS FTVatCode,
        //                 CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
        //                 '$dDate'                                                            AS FDDateUpd,
        //                 '$tTime'                                                            AS FTTimeUpd,
        //                 '$tUser'                                                            AS FTWhoUpd,
        //                 '$dDate'                                                            AS FDDateIns,
        //                 '$tTime'                                                            AS FTTimeIns,
        //                 '$tUser'                                                            AS FTWhoIns
        //             FROM TCNMPdt P                  WITH (NOLOCK) 
        //             LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND CONVERT(VARCHAR(10),A.FDOrderDate,121)='$dDateCurOrd'
        //             LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1'
        //             LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4'
        //             LEFT JOIN TCNTPdtPmtCO M        WITH (NOLOCK) ON M.FTPdtName=P.FTPdtName
        //             LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND CONVERT(VARCHAR(10),H.FDPmhDStop,121)>='$dDateCurOrd'
        //             LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode
        //             LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode
        //             LEFT JOIN TSysConfig F          WITH (NOLOCK) ON F.FTSysCode='SugOrder'
        //             WHERE CONVERT(VARCHAR(10),H.FDPmhDStop,121) >='$dDateCurOrd' 
        //             AND H.FTPmhClosed=0
        //             AND P.FCPdtStkFac != 0
        //             AND $tPdtOrd
        //             AND P.FTPdtStaAlwBuy='1'
        //             AND T.FTStyName!='Result' 
        //             AND CONVERT(VARCHAR(10),P.FDPdtSaleStart,121) < DATEADD(day, -$tNewPdtDay, '$dDateCurOrd')
        //             AND ('$dDateCurOrd'>=CONVERT(VARCHAR(10),FDPdtOrdStart,121) AND '$dDateCurOrd'<=CONVERT(VARCHAR(10),P.FDPdtOrdStop,121))
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTSGOItem WHERE CONVERT(VARCHAR(10),FDOrderDate,121)='$dDateCurOrd')
                    
        //             -- AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN (SELECT MAX(FTPthDocNo) AS FTPthDocNo 
        //             -- FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND CONVERT(VARCHAR(10),FDPdtEnddate,121)>='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN 
        //             (SELECT MAX(FTPthDocNo) AS FTPthDocNo FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND '$dDateCurOrd' BETWEEN CONVERT(VARCHAR(10),FDPdtStartdate,121) AND CONVERT(VARCHAR(10),FDPdtEnddate,121))
        //             AND ((F.FTSysUsrValue='0' AND O.FCSugQty>0) OR F.FTSysUsrValue='1')
                    
        //             GROUP BY G.FTPgpName,G1.FTPgpName,P.FTPdtName,B.FTPdtBarCode,T.FTStyName,A.FC_T1,A.FC_T2,
        //             B.FCPdtRetPri1,S.FNLTDSUN,P.FCPdtQtyRet,O.FCSugQty,FCPdtStkFac,P.FCPdtCostStd,
        //             P.FTPdtCode,P.FTSplCode,SPL.FTSplViaRmk,S.FNLTDSun,S.FNLTDMon,S.FNLTDTue,S.FNLTDWed,
        //             S.FNLTDThu,S.FNLTDFri,S.FNLTDSat,W.FCSaleQtySun,W.FCSaleQtyMon,W.FCSaleQtyTue,W.FCSaleQtyWed,
        //             W.FCSaleQtyThu,W.FCSaleQtyFri,W.FCSaleQtySat,H.FDPmhDStart,H.FDPmhDStop

                   
        //             UNION

        //             SELECT ''                                                               AS FTXohDocNo,
        //                 'NEW'                                                               AS SECTION,
        //                 G.FTPgpName                                                         AS CATEGORY,
        //                 G1.FTPgpName                                                        AS SUBCAT,
        //                 P.FTPdtCode                                                         AS PRODUCT_CODE,
        //                 P.FTPdtName                                                         AS PRODUCT_NAME,
        //                 CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=P.FTPdtCode) ELSE B.FTPdtBarCode END AS BARCODE,
        //                 T.FTStyName                                                         AS DELIVERY_TYPE,
        //                 CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END         AS IN_TRANSIT,
        //                 P.FCPdtCostStd                                                      AS Cost,
        //                 B.FCPdtRetPri1                                                      AS PRICE,
                        
        //                 CASE WHEN ('$dDateCurOrd' >= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN                                             
        //                 'โปร ปัจจุบัน ' +SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 WHEN ('$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStart,121) AND '$dDateCurOrd' <= CONVERT(VARCHAR(10),H.FDPmhDStop,121)) THEN 
        //                 'โปร รอบถัดไป '+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) 
        //                 ELSE '' END AS PROMO,

        //                 $tDELIVERY_DATE2                                                    AS DELIVERY_DATE,
        //                 P.FCPdtQtyRet                                                       AS STOCK,
        //                 FCPdtStkFac                                                         AS LOT_SIZE,
        //                 $tADS                                                               AS ADS,
        //                 CEILING(ISNULL(O.FCSugQty,0) / FCPdtStkFac)                         AS SGO_QTY_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_LOT,
        //                 (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) AS ORDER_PCS,
        //                 P.FTSplCode                                                         AS FTSplCode,
        //                 SPL.FTSplViaRmk                                                     AS FTVatCode,
        //                 CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dDateCurOrd' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
        //                 '$dDate'                                                            AS FDDateUpd,
        //                 '$tTime'                                                            AS FTTimeUpd,
        //                 '$tUser'                                                            AS FTWhoUpd,
        //                 '$dDate'                                                            AS FDDateIns,
        //                 '$tTime'                                                            AS FTTimeIns,
        //                 '$tUser'                                                            AS FTWhoIns
        //             FROM TCNMPdt P                  WITH (NOLOCK) 
        //             LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode
        //             LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND CONVERT(VARCHAR(10),A.FDOrderDate,121)='$dDateCurOrd'
        //             LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode
        //             LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1'
        //             LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4'
        //             LEFT JOIN TCNTPdtPmtDT M        WITH (NOLOCK) ON M.FTPdtName=P.FTPdtName
        //             LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND CONVERT(VARCHAR(10),H.FDPmhDStop,121)>='$dDateCurOrd'
        //             LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode
        //             LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode
        //             LEFT JOIN TSysConfig F          WITH (NOLOCK) ON F.FTSysCode='SugOrder'
        //             WHERE P.FTPdtStaAlwBuy='1'
        //             AND P.FCPdtStkFac != 0
        //             AND T.FTStyName!='Result'
        //             -- AND (H.FTPmhDocNo IS NOT NULL OR H.FTPmhDocNo<>'')
        //             AND $tPdtOrd
        //             AND CONVERT(VARCHAR(10),P.FDPdtSaleStart,121) BETWEEN DATEADD(day, -$tNewPdtDay, '$dDateCurOrd') AND '$dDateCurOrd'
        //             AND ('$dDateCurOrd'>=CONVERT(VARCHAR(10),FDPdtOrdStart,121) AND '$dDateCurOrd'<=CONVERT(VARCHAR(10),P.FDPdtOrdStop,121))
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTSGOItem WHERE CONVERT(VARCHAR(10),FDOrderDate,121)='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtPmtDT)

        //             -- AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN (SELECT MAX(FTPthDocNo) AS FTPthDocNo 
        //             -- FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND CONVERT(VARCHAR(10),FDPdtEnddate,121)>='$dDateCurOrd')
        //             AND P.FTPdtCode NOT IN (SELECT FTPdtCode FROM TCNTPdtSuggestDT WHERE FTPthDocNo IN 
        //             (SELECT MAX(FTPthDocNo) AS FTPthDocNo FROM TCNTPdtSuggestDT GROUP BY FTPdtCode ) AND '$dDateCurOrd' BETWEEN CONVERT(VARCHAR(10),FDPdtStartdate,121) AND CONVERT(VARCHAR(10),FDPdtEnddate,121))
        //             AND ((F.FTSysUsrValue='0' AND O.FCSugQty>0) OR F.FTSysUsrValue='1')

        //             GROUP BY G.FTPgpName,G1.FTPgpName,P.FTPdtName,B.FTPdtBarCode,T.FTStyName,A.FC_T1,A.FC_T2,
        //             B.FCPdtRetPri1,S.FNLTDSUN,P.FCPdtQtyRet,O.FCSugQty,FCPdtStkFac,P.FCPdtCostStd,
        //             P.FTPdtCode,P.FTSplCode,SPL.FTSplViaRmk,S.FNLTDSun,S.FNLTDMon,S.FNLTDTue,S.FNLTDWed,
        //             S.FNLTDThu,S.FNLTDFri,S.FNLTDSat,W.FCSaleQtySun,W.FCSaleQtyMon,W.FCSaleQtyTue,W.FCSaleQtyWed,
        //             W.FCSaleQtyThu,W.FCSaleQtyFri,W.FCSaleQtySat,H.FDPmhDStart,H.FDPmhDStop) S";
        // $tReturnInsert = $this->DB_EXECUTE($tSQL);
        // if($tReturnInsert == 'success'){
        //     $aDataResult = array(
        //         // 'tSQL'          => $tSQL,
        //         'nStaQuery'     => 1,
        //         'tStaMessage'   => 'Add Data to TSPoDT Success',
        //     );
        // }else{
        //     $aDataResult = array(
        //         // 'tReturnInsert' => $tReturnInsert,
        //         // 'tSQL'          => $tSQL,
        //         'nStaQuery'     => 99,
        //         'tStaMessage'   => 'Not fround Data',
        //     );
        // }

        // if($tQueryReturnStore2 == 'success'){
    }

    public function FSxMODSDelDocTmpDT(){
        $tDatabase          = "TSPoDT";
        $aDataDeleteWHERE   = array(
            'FTXohDocNo'    => ''
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSDelDocTmpDT] Clear Temp'
            );
            // $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSDelDocTmpDT] '
            );
        }
        return $aDataResult;
    }

    public function FSxMODSAddEditHD($paData){
        $aDataInsert = array(
            'FTBchCode'         => $paData['tBranch'],
            'FTXohDocNo'        => $paData['tDocNo'],
            'FTXohDocType'      => $paData['tDocType'],
            'FDXohDocDate'      => $paData['FDXohDocDate'],
            'FTXohDocTime'      => $paData['tDocTime'],
            'FTXohStaDoc'       => $paData['tStaDoc'],
            'FDDateUpd'         => $paData['dDocDate'],
            'FTTimeUpd'         => $paData['tDocTime'],
            'FTWhoUpd'          => $paData['tUser'],
            'FDDateIns'         => $paData['dDocDate'],
            'FTTimeIns'         => $paData['tDocTime'],
            'FTWhoIns'          => $paData['tUser']
        );
        $aDataInsert = $this->DB_INSERT('TSPoHD',$aDataInsert);
        $aDataResult = array(
            'tQuerySta'     => $aDataInsert,
            'nStaQuery'      => '1',
            'tStaMessage'   => '[FSxMODSAddEditHD] สร้างเลขที่ใบสั่งซื้อ '.$paData['tDocNo']
        );
        $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSxMODSDTUpdateDocNo($ptDocNo){
        $tSQL = "UPDATE TSPoDT SET FTXohDocNo = '$ptDocNo' WHERE FTXohDocNo = ''";
        $tDataUpdate = $this->DB_EXECUTE($tSQL);
        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSDTUpdateDocNo] อัพเดทเลขที่ใบสั่งซื้อ ในตาราง TSPoDT'
            );
            $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Update FTXohDocNo from TSPoDT UnSuccess',
            );
        }
        return $aDataResult;
    }

    public function FSxMODSCheckPdtMax($paDataQuery){
        $tPdtBarCode    = $paDataQuery['tPdtBarCode'];
        $nOrderLot      = $paDataQuery['nOrderLot'];

        $tSQL = "SELECT 
                    TOP 1 FCPdtMax 
                FROM 
                    TCNMPdt
                LEFT JOIN TCNMPdtBar ON TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode
                WHERE 
                    TCNMPdtBar.FTPdtBarCode = '$tPdtBarCode' AND 
                    (TCNMPdt.FCPdtMax >= $nOrderLot OR (TCNMPdt.FCPdtMax IS NULL OR TCNMPdt.FCPdtMax=0))";
        $aData = $this->DB_SELECT($tSQL);
        if(count($aData) > 0){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data'
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data'
            );
        }
        return $aDataResult;
    }

    public function FSxMODSUpdateOrderLot($paDataQuery){
        $tSec           = $paDataQuery['tSec'];
        $nSeq           = $paDataQuery['nSeq'];
        $nOrderLot      = $paDataQuery['nOrderLot'];
        $nPdtLotSize    = $paDataQuery['nPdtLotSize'];
        $tDocNo         = $paDataQuery['tDocNo'];

        $tSQL = "UPDATE 
                    TSPoDT
                 SET 
                    FCPdtOrdLot     = $nOrderLot, 
                    FCPdtOrdPcs     = $nPdtLotSize,
                    FTPdtPOFlag     = CASE 
                                        WHEN FTPdtPOFlag = '1' THEN '2'
                                        ELSE FTPdtPOFlag
                                      END
                 WHERE FNXdtSeqNo       = $nSeq 
                   AND FTPdtSecCode     ='$tSec'";
        if($tDocNo != ""){
            $tSQL .= " AND FTXohDocNo = '$tDocNo'";
        }else{
            $tSQL .= " AND FTXohDocNo = ''";
        }
        $tDataUpdate = $this->DB_EXECUTE($tSQL);
        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Updated FCPdtOrdLot from TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Updated FCPdtOrdLot from TSPoDT UnSuccess',
            );
        }

        return $aDataResult;

    }

    public function FSxMODSUpdateStaDoc($paData){

        $nStaDoc        = $paData['nStaDoc'];
        $tDocNo         = $paData['tDocNo'];

        $tSQL           = "UPDATE TSPoHD SET FTXohStaDoc = '$nStaDoc',FTXohStaPrcDoc = '$nStaDoc' WHERE FTXohDocNo = '$tDocNo'";
        $tDataUpdate    = $this->DB_EXECUTE($tSQL);

        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSUpdateStaDoc] ยกเลิกเลขที่ใบสั่งซื้อ '.$tDocNo
            );
            $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Updated FTXohStaDoc from TSPoHD UnSuccess',
            );
        }

        return $aDataResult;

    }

    public function FSxMODSUpdateStaPrcDoc($paData){

        $tDocNo         = $paData['tDocNo'];
        $nStaPrcDoc     = $paData['nStaPrcDoc'];

        $tSQL           = "UPDATE TSPoHD SET FTXohStaPrcDoc = '$nStaPrcDoc' WHERE FTXohDocNo = '$tDocNo'";
        $tDataUpdate    = $this->DB_EXECUTE($tSQL);
        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSUpdateStaPrcDoc] อนุมัติเอกสาร '.$tDocNo
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSUpdateStaPrcDoc] '.$tDataUpdate[0]['message']
            );
        }
        $this->FSxMODSWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;

    }

    public function FSxMODSCopySGOQTY($paData){
        $tSecCode   = $paData['ptSec'];
        $tDocNo     = $paData['ptDocNo'];

        $tSQL       = "UPDATE TSPoDT SET FCPdtOrdLot = FCPdtSGOQty , FCPdtOrdPcs = (FCPdtSGOQty * FCPdtLotSize) WHERE FTPdtSecCode = '$tSecCode' AND FCPdtSGOQty > 0 AND FCPdtOrdLot IS NULL";//AND FCPdtOrdLot = 0

        if($tDocNo != ""){

            $tSQL  .= " AND FTXohDocNo = '$tDocNo'";

        }

        $tDataUpdate    = $this->DB_EXECUTE($tSQL);

        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'CopySGOQTY from TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'CopySGOQTY from TSPoDT Success',
            );
        }

        return $aDataResult;
    }

    public function FSaMODSAddPdtOrder($aDataInsertDT){
        $tDatabaseDT = "TSPoDT";
        $tResult     = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'             => $tResult,
                'nStaQuery'        =>  1,
                'tStaMessage'      =>  'Insert Success.'
            );
        }else{
            $aDataResult = array(
                'tSQL'             => $tResult,
                'nStaQuery'        =>  99,
                'tStaMessage'      =>  'Error can not insert data.'
            );
        }
        return $aDataResult;
    }

    public function FSaMODSGetSeqLastDTAddon($paDataWhere){
        $tSQL = "SELECT
                    TOP 1
                    PODT.FNXdtSeqNo AS FNXdtSeqNo
                FROM TSPoDT PODT
                WHERE FTXohDocNo='$paDataWhere[FTXohDocNo]' AND FTPdtSecCode='$paDataWhere[FTPdtSecCode]' ORDER BY FNXdtSeqNo DESC";
        $aDataDT = $this->DB_SELECT($tSQL);
        if(count($aDataDT) > 0){
            $aDataResult = array(
                'aReturn'       => $aDataDT[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataResult;
    }

    public function FSaMODSCheckDataNotSubmitOrder(){
        $tBchCode = getBranch()['FTBchCode'];
        $tSQL = "SELECT
                    TOP 1
                    POHD.FTXohDocNo 		AS FTXohDocNo,
                    POHD.FDXohDocDate 	    AS FDXohDocDate,
                    POHD.FTXohStaPrcDoc     AS FTXohStaPrcDoc,
                    POHD.FTXohStaDoc        AS FTXohStaDoc
                FROM 
                    TSPoHD POHD
                WHERE 
                    FTXohStaPrcDoc IS NULL AND 
                    (FTXohStaDoc != '3' OR FTXohStaPrcDoc = '') AND
                    FTBchCode = '$tBchCode'
                ORDER BY FTXohDocNo DESC";
        $aDataHD = $this->DB_SELECT($tSQL);

        if(count($aDataHD) > 0){
            $aDataResult = array(
                'aReturn'       => $aDataHD[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        
        return $aDataResult;

    }

    public function FSaMODSGetDataHD($ptDocNo){
        $tBchCode = getBranch()['FTBchCode'];
        $tSQL = "SELECT
                    TOP 1
                    POHD.FTXohDocNo 		AS FTXohDocNo,
                    POHD.FDXohDocDate 	    AS FDXohDocDate,
                    POHD.FTXohStaPrcDoc     AS FTXohStaPrcDoc,
                    POHD.FTXohStaDoc        AS FTXohStaDoc
                FROM 
                    TSPoHD POHD
                WHERE 
                    FTXohDocNo = '$ptDocNo' AND
                    FTBchCode = '$tBchCode'";
        $aDataHD = $this->DB_SELECT($tSQL);

        if(count($aDataHD) > 0){
            $aDataResult = array(
                'aReturn'       => $aDataHD[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        
        return $aDataResult;
        
    }

    public function FSaMODSUpdateApproveRabbitFail($ptDocNo){
        $tSQL           = "UPDATE TSPoHD SET FTXohStaPrcDoc = NULL WHERE FTXohDocNo = '$ptDocNo'";
        $tDataUpdate    = $this->DB_EXECUTE($tSQL);

        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Updated FTXohStaDoc from TSPoHD Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Updated FTXohStaDoc from TSPoHD UnSuccess',
            );
        }

        return $aDataResult;

    }

    public function FSaMODSSearchPOHD($paData){
        $aRowLen    = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
        $aBranch    = getBranch();
        $tBchCode   = $aBranch['FTBchCode'];
        $tSQL       = "SELECT c.* FROM(
                        SELECT ROW_NUMBER() OVER(ORDER BY FTXohDocNo DESC) AS rtRowID , * FROM
                            (SELECT  
                                POHD.FTXohDocNo,
                                POHD.FDXohDocDate,
                                POHD.FTXohStaDoc,
                                POHD.FTXohStaPrcDoc
                            FROM TSPoHD POHD WITH (NOLOCK)
                            WHERE FTBchCode='$tBchCode'";
        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $aDataList = $this->DB_SELECT($tSQL);

        if(count($aDataList) > 0){
            $aFoundRow  = $this->FSaMODSGetPageSearchList();
            $nFoundRow  = $aFoundRow['counts'];
            $nPageAll   = ceil($nFoundRow/$paData['nRow']);
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'rnAllRow'          => $nFoundRow,
                'rnCurrentPage'     => $paData['nPage'],
                "rnAllPage"         => $nPageAll,
                'nStaQuery'         => 1,
                'tStaMessage'       => 'Select Data from TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nStaQuery'         => 99,
                'tStaMessage'       => 'Select Data from TSPoDT Unsuccessful',
            );
        }

        return $aDataResult;

    }

    public function FSaMODSGetPageSearchList(){
        $tBchCode = getBranch()['FTBchCode'];
        $tSQL   = "SELECT COUNT(POHD.FTXohDocNo) AS counts
                    FROM TSPoHD POHD  WITH (NOLOCK)
                    WHERE FTBchCode = '$tBchCode'";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    public function FSaMODSSelectCheckTemp(){
        $tSQL   = "SELECT PODT.FTPdtCode
                    FROM TSPoDT PODT
                    WHERE PODT.FTXohDocNo = ''";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)){
            if(count($oQuery) > 0){
                $aReturn = array(
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 1
                );
            }
        }else{
            $aReturn = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99
            );
        }
        return $aReturn;

    }

    public function FSaMODSGetPOList($ptDocNo){
        $tSQL   = "SELECT PODT.FTXohRefPODocNo,
                          PODT.FTSplCode,
                          PODT.FTVatCode
                    FROM TSPoDT PODT
                    WHERE PODT.FTXohDocNo = '$ptDocNo'
                    GROUP BY PODT.FTXohRefPODocNo,PODT.FTSplCode,PODT.FTVatCode
                    HAVING COUNT(PODT.FTXohRefPODocNo) > 0";
        $aDataList = $this->DB_SELECT($tSQL);
        if (!empty($aDataList)){
            if(count($aDataList) > 0){
                $aReturn = array(
                    'aItems'        => $aDataList,
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 1
                );
            }
        }else{
            $aReturn = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99
            );
        }
        return $aReturn;

    }

    public function FSaMODSCheckDateOnHD($pdDateOrder){
        $tBchCode = getBranch()['FTBchCode'];
        $tSQL = "SELECT *
                 FROM 
                    TSPoHD POHD
                 WHERE 
                    FDXohDocDate = '$pdDateOrder' AND
                    FTBchCode = '$tBchCode'";
        $aDataHD = $this->DB_SELECT($tSQL);
        if(count($aDataHD) > 0){
            $aDataResult = array(
                // 'aReturn'       => $aDataHD[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataResult;
    }

    public function FSaMODSCheckPOTime(){

        $tSQL = "SELECT TOP 1 FTSysUsrValue
                 FROM TSysConfig
                 WHERE FTSysCode = 'POTime'";
        $aData = $this->DB_SELECT($tSQL);

        if(count($aData) > 0){
            $aDataResult = array(
                'aReturn'       => $aData[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        
        return $aDataResult;

    }

    public function FSaMODSAddPdtManual($paData,$aChkPdtFrmTmp){
        $tSection       = $paData['ptSection'];
        // $tSecStatus     = $paData['tSecStatus'];
        $tSearchPdt     = $paData['ptSearchPdt'];
        $dOrderDate     = $paData['pdOrderDate'];
        $tDocNo         = $paData['ptDocNo'];
        // $nSeqNo         = $paData['pnSeqNo'];
        $dCurentDay     = $paData['pdCurentDay'];
        $dCurrentDate   = $paData['pdCurrentDate'];
        $tCurrentTime   = $paData['ptCurrentTime'];
        $tUser          = $paData['ptUser'];

        //ค้นหาสินค้าด้วย FTPdtCode หรือ FTPdtBarCode
        $tSQL0 = " SELECT
                        TCNMPdt.FTPdtStkCode,
                        TCNMPdt.FTPdtStaAlwSale,
                        TCNMPdt.FTPdtStaAlwBuy,
                        TCNMPdt.FTPdtCode,
                        TCNMPdtBar.FTPdtBarCode
                    FROM TCNMPdt WITH(NOLOCK)
                    LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                    WHERE TCNMPdt.FTPdtCode = '$tSearchPdt' OR TCNMPdtBar.FTPdtBarCode  = '$tSearchPdt'
                    ORDER BY TCNMPdtBar.FDPdtPriAffect DESC";
        $aDataResult = $this->DB_SELECT($tSQL0);
        // print_r($aDataResult);
        // exit;
        
        if(count($aDataResult) > 0){//ถ้าพบรายการ
            if($aDataResult[0]['FTPdtStaAlwBuy'] == '1'){ //ตรวจสอบว่ารายการที่ค้นหามาสามารถสั่งซื้อได้ 1 = ได้ , 2 = ไม่ได้
                //สินค้าอนุญาติสั่งซื้อได้ให้นำรหัสสินค้า และรหัสบาร์โค๊ดไปทำงานต่อ
                $tPdtCode = $aDataResult[0]['FTPdtCode'];
                $tBarCode = $aDataResult[0]['FTPdtBarCode'];
            }else{
                // print_r($aDataResult);
                // exit;
                $tStkCode = $aDataResult[0]['FTPdtStkCode'];
                //สินค้าสั่งซื้อไม่ได้ ค้นหาสินค้าใหม่อีกรอบด้วย FTPdtStkCode และ FTPdtStaAlwBuy = 1 เพื่อค้นหาสินค้าที่สั่งซื้อได้ ที่ FTPdtStkCode เดียวกัน
                $tSQL1 = " SELECT 
                                P.FTPdtStkCode,
                                B.FDPdtPriAffect,
                                P.FTPdtStaAlwSale,
                                P.FTPdtStaAlwBuy,
                                P.FTPdtCode,
                                B.FTPdtBarCode
                            FROM TCNMPdt P 
                            LEFT JOIN TCNMPdtBar B ON P.FTPdtCode = B.FTPdtCode
                            WHERE P.FTPdtStkCode='$tStkCode'
                              AND P.FTPdtStaAlwBuy = '1'
                            ORDER BY B.FDPdtPriAffect DESC";
                $aDataResult1 = $this->DB_SELECT($tSQL1);
                if(count($aDataResult1) > 0){
                    $tPdtCode = $aDataResult1[0]['FTPdtCode'];
                    $tBarCode = $aDataResult1[0]['FTPdtBarCode'];
                }else{
                    $tPdtCode = '99999999999999999';
                    $tBarCode = '99999999999999999';
                }
            }

            switch ($dCurentDay) {
                case 0:
                    $tADS               = "ISNULL(W.FCSaleQtySun,0)";
                    $tPdtOrd            = "P.FTPdtOrdSun = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDSun IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDSun, '$dOrderDate') END";
                    break;
                case 1:
                    $tADS               = "ISNULL(W.FCSaleQtyMon,0)";
                    $tPdtOrd            = "P.FTPdtOrdMon = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDMon IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDMon, '$dOrderDate') END";
                    break;
                case 2:
                    $tADS               = "ISNULL(W.FCSaleQtyTue,0)";
                    $tPdtOrd            = "P.FTPdtOrdTue = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDTue IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDTue, '$dOrderDate') END";
                    break;
                case 3:
                    $tADS               = "ISNULL(W.FCSaleQtyWed,0)";
                    $tPdtOrd            = "P.FTPdtOrdWed = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDWed IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDWed, '$dOrderDate') END";
                    break;
                case 4:
                    $tADS               = "ISNULL(W.FCSaleQtyThu,0)";
                    $tPdtOrd            = "P.FTPdtOrdThu = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDThu IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDThu, '$dOrderDate') END";
                    break;
                case 5:
                    $tADS               = "ISNULL(W.FCSaleQtyFri,0)";
                    $tPdtOrd            = "P.FTPdtOrdFri = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDFri IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDFri, '$dOrderDate') END";
                    break;
                case 6:
                    $tADS               = "ISNULL(W.FCSaleQtySat,0)";
                    $tPdtOrd            = "P.FTPdtOrdSat = '1'";
                    $tDELIVERY_DATE2    = "CASE WHEN S.FNLTDSat IS NULL THEN '$dOrderDate' ELSE DATEADD(day, S.FNLTDSat, '$dOrderDate') END";
                    break;
            }
            //'$nSeqNo' AS FNXdtSeqNo,
            $tSQL  = "INSERT INTO TSPoDT (FTXohDocNo,FNXdtSeqNo,FTPdtSecCode,FTPdtCategory,FTPdtSubCat,FTPdtCode,FTPdtName,FTPdtBarCode,FTPdtDelivery,FCPdtIntransit,FCPdtCost,FCPdtPrice,FTPdtPromo,FDDeliveryDate,FCPdtStock,FCPdtLotSize,FCPdtADS,FCPdtSGOQty,FCPdtOrdLot,FCPdtOrdPcs,FTSplCode,FTVatCode,FTPdtPOFlag,FTPdtSecStatus,FDDateUpd,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns) ";
            $tSQL .= "SELECT TOP 1
                        '$tDocNo'                                       AS FTXohDocNo,
                        CASE 
                            WHEN (SELECT TOP 1 PODT.FNXdtSeqNo AS FNXdtSeqNo FROM TSPoDT PODT WHERE FTXohDocNo='$tDocNo' AND FTPdtSecCode='$tSection' ORDER BY FNXdtSeqNo DESC) IS NULL THEN 1
                            ELSE (SELECT TOP 1 PODT.FNXdtSeqNo AS FNXdtSeqNo FROM TSPoDT PODT WHERE FTXohDocNo='$tDocNo' AND FTPdtSecCode='$tSection' ORDER BY FNXdtSeqNo DESC) + 1 
                        END AS FNXdtSeqNo,
                        '$tSection'                                     AS FTPdtSecCode,
                        G.FTPgpName                                     AS FTPdtCategory,
                        G1.FTPgpName                                    AS FTPdtSubCat,
                        P.FTPdtCode                                     AS FTPdtCode,
                        P.FTPdtName                                     AS FTPdtName,
                        CASE WHEN B.FTPdtBarCode IS NULL THEN (SELECT FTPdtBarCode FROM TCNMPdtBar WHERE TCNMPdtBar.FTPdtCode=P.FTPdtCode) ELSE B.FTPdtBarCode END AS FTPdtBarCode,
                        T.FTStyName                                     AS FTPdtDelivery,
                        CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END AS FCPdtIntransit,
                        P.FCPdtCostStd                                  AS FCPdtCost,
                        B.FCPdtRetPri1                                  AS FCPdtPrice,
                        ''                                              AS PROMO,
                        $tDELIVERY_DATE2                                AS FDDeliveryDate,
                        P.FCPdtQtyRet                                   AS FCPdtStock,
                        P.FCPdtStkFac                                   AS FCPdtLotSize,
                        $tADS                                           AS FCPdtADS,
                        O.FCSugQty                                      AS FCPdtSGOQty,
                        -- CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE C.FDXohDocDate='$dOrderDate' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS ORDER_LOT,
                        -- CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE C.FDXohDocDate='$dOrderDate' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS ORDER_PCS,
                        -- ISNULL((SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dOrderDate' AND C.FTPdtName=P.FTPdtName),'0') AS ORDER_LOT,
                        -- ISNULL((SELECT DISTINCT(SUM(C.FCXodQtyAll)) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dOrderDate' AND C.FTPdtName=P.FTPdtName),'0') AS ORDER_PCS,
                        ISNULL((SELECT TOP 1 (C.FCXodQty) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dOrderDate' AND C.FTPdtName=P.FTPdtName ORDER BY C.FTXohDocNo DESC),'0') AS ORDER_LOT, --Version 1.0.0.7
                        ISNULL((SELECT TOP 1 (C.FCXodQtyAll) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='$dOrderDate' AND C.FTPdtName=P.FTPdtName ORDER BY C.FTXohDocNo DESC),'0') AS ORDER_PCS, --Version 1.0.0.7
                        P.FTSplCode                                     AS FTSplCode,
                        SPL.FTSplViaRmk                                 AS FTVatCode,
                        CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE C.FDXohDocDate='$dOrderDate' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS FTPdtPOFlag,
                        (SELECT TOP 1 FTPdtSecCode FROM TSPoDT WHERE FTPdtCode=P.FTPdtCode OR FTPdtBarCode=B.FTPdtBarCode AND FTXohDocNo='$tDocNo') AS FTPdtSecStatus,
                        '$dCurrentDate'                                 AS FDDateUpd,
                        '$tCurrentTime'                                 AS FTTimeUpd,
                        '$tUser'                                        AS FTWhoUpd,
                        '$dCurrentDate'                                 AS FDDateIns,
                        '$tCurrentTime'                                 AS FTTimeIns,
                        '$tUser'                                        AS FTWhoIns
                        FROM
                            TCNMPdt P WITH (NOLOCK)
                        LEFT JOIN TCNMPdtBar B          WITH (NOLOCK) ON B.FTPdtCode = P.FTPdtCode
                        LEFT JOIN TCNMSGOPara S         WITH (NOLOCK) ON S.FTPdtCode = P.FTPdtCode
                        LEFT JOIN TCNTSGOPara A         WITH (NOLOCK) ON A.FTStkCode = P.FTPdtStkCode AND A.FDOrderDate='$dOrderDate'
                        LEFT JOIN TCNMPdtSugOrd O       WITH (NOLOCK) ON O.FTPdtStkCode = P.FTPdtStkCode
                        LEFT JOIN TCNTHisSale4Week W    WITH (NOLOCK) ON W.FTPdtStkCode = P.FTPdtStkCode 
                        LEFT JOIN TCNMPdtGrp G          WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1' 
                        LEFT JOIN TCNMPdtGrp G1         WITH (NOLOCK) ON G1.FTPgpChain = P.FTPgpChain AND G1.FNPgpLevel='4' 
                        LEFT JOIN TCNTPdtPmtDT M        WITH (NOLOCK) ON M.FTPdtName = P.FTPdtName
                        LEFT JOIN TCNTPdtPmtHD H        WITH (NOLOCK) ON H.FTPmhDocNo = M.FTPmhDocNo AND H.FDPmhDStop>='$dOrderDate'
                        LEFT JOIN TCNMSplType T         WITH (NOLOCK) ON T.FTStyCode = P.FTStyCode 
                        LEFT JOIN TCNMSpl SPL           WITH (NOLOCK) ON SPL.FTSplCode = P.FTSplCode 
                        WHERE 
                            --P.FTPdtStkCode = (SELECT FTPdtStkCode FROM TCNMPdt WHERE (FTPdtCode = '$tSearchPdt' OR FTPdtCode = (SELECT TOP 1 FTPdtCode FROM TCNMPdtBar WHERE FTPdtBarCode = '$tSearchPdt')))
                            P.FTPdtStaAlwBuy = '1'
                            AND P.FCPdtStkFac != 0
                            AND ('$dOrderDate' >= FDPdtOrdStart AND '$dOrderDate' <= P.FDPdtOrdStop) 
                            AND $tPdtOrd
                            AND T.FTStyName!='Result'

                            AND P.FTPdtCode     = '$tPdtCode'
                            AND B.FTPdtBarCode  = '$tBarCode'
                        ORDER BY B.FDPdtPriAffect DESC";
            $tDataInsert = $this->DB_EXECUTE($tSQL);
            if($tDataInsert == 'success'){
                $aDataResult = array(
                    'aChkPdtFrmTmp' => $aChkPdtFrmTmp,
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'Insert Products to TSPoDT Success'
                );
            }else{
                $aDataResult = array(
                    'aChkPdtFrmTmp' => $aChkPdtFrmTmp,
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'Insert Products to TSPoDT UnSuccess'
                );
            }
        }else{
            $aDataResult = array(
                'tSQL0'         => $tSQL0,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }

        return $aDataResult;

    }

    public function FSaMODSCheckOldSection($ptSearch,$ptDocNo){

        // $tSQL = "SELECT c.* FROM(
        //             SELECT 
        //             ROW_NUMBER() OVER( ORDER BY
        //                 -- ORDER BY FTPdtPromo,FCPdtADS DESC
        //                 (CASE 
        //                     WHEN FTPdtSecCode = 'PROMOTION' 
        //                     THEN FTPdtPromo
        //                 END) DESC,
        //                 (CASE
        //                     WHEN FTPdtSecCode = 'PROMOTION' 
        //                     THEN FCPdtADS
        //                 END) DESC,

        //                 -- ORDER BY FDDateIns,FTTimeIns,FNXdtSeqNo ASC
        //                 (CASE
        //                     WHEN FTPdtSecCode = 'NEW' OR FTPdtSecCode = 'ADDON'
        //                     THEN FDDateIns
        //                 END) ASC,
        //                 (CASE
        //                     WHEN FTPdtSecCode = 'NEW' OR FTPdtSecCode = 'ADDON'
        //                     THEN FTTimeIns
        //                 END) ASC,
        //                 (CASE
        //                     WHEN FTPdtSecCode = 'NEW' OR FTPdtSecCode = 'ADDON'
        //                     THEN FNXdtSeqNo
        //                 END) ASC,

        //                 -- ORDER BY FCPdtADS DESC
        //                 (CASE
        //                     WHEN FTPdtSecCode = 'TOP1000'
        //                     THEN FCPdtADS
        //                 END) DESC
        //             ) AS nRowID,
        //                 * 
        //             FROM
        //             (SELECT
        //                 POD.FTXohDocNo,
        //                 POD.FTPdtCode,
        //                 POD.FTPdtName,
        //                 POD.FTPdtBarCode,
        //                 POD.FNXdtSeqNo,
        //                 POD.FTPdtSecCode,
        //                 POD.FCPdtADS,
        //                 POD.FTPdtPromo,
        //                 POD.FDDateIns,
        //                 POD.FTTimeIns
        //             FROM TSPoDT POD WITH (NOLOCK)
        //             LEFT JOIN (SELECT TOP 1 
        //                             FTXohDocNo,
        //                             FTPdtSecCode,
        //                             FNXdtSeqNo
        //                         FROM TSPoDT WITH(NOLOCK)
        //                         WHERE (FTPdtCode='$ptSearch' OR FTPdtBarCode='$ptSearch') 
        //                         AND FTXohDocNo = '$ptDocNo'
        //             ) SEC ON POD.FTXohDocNo = SEC.FTXohDocNo
        //             WHERE POD.FTXohDocNo = '$ptDocNo' AND POD.FTPdtSecCode=SEC.FTPdtSecCode) Base) AS c 
        //             WHERE c.FTPdtBarCode='$ptSearch'
        // ";

        //หา Section ของสินค้าที่ต้องการค้นหา
        $tSQL = "SELECT TOP 1 
                    DT.FTPdtSecCode
                FROM TSPoDT DT WITH(NOLOCK)
                LEFT JOIN TCNMPdt P ON DT.FTPdtCode = P.FTPdtCode
                WHERE 1=1
                AND (DT.FTPdtBarCode='$ptSearch' OR P.FTPdtStkCode = (SELECT FTPdtStkCode FROM TCNMPdt WHERE FTPdtCode='$ptSearch'))
                AND DT.FTXohDocNo='$ptDocNo'
        ";
        $aDataResult    = $this->DB_SELECT($tSQL);
        if(count($aDataResult) > 0){
            //ฟิวเตอร์ order by ตาม section
            $tSecPdt = $aDataResult[0]['FTPdtSecCode'];

            switch($tSecPdt){
                case "PROMOTION":
                    $tOrderBy = "ORDER BY FTPdtPromo,FCPdtADS DESC";
                break;
                case "NEW":
                    $tOrderBy = "ORDER BY FDDateIns,FTTimeIns,FNXdtSeqNo ASC";
                break;
                case "ADDON":
                    $tOrderBy = "ORDER BY FDDateIns,FTTimeIns,FNXdtSeqNo ASC";
                break;
                case "TOP1000":
                    $tOrderBy = "ORDER BY FCPdtADS DESC";
                break;
            }

            //หาว่าสินค้าอยู่ seq ที่เท่าไหร่
            $tSQL1 = "SELECT c.* FROM(
                        SELECT ROW_NUMBER() OVER( $tOrderBy ) AS nRowID , * FROM
                        (
                            SELECT  
                                POD.FTXohDocNo,
                                POD.FTPdtCode,
                                POD.FTPdtName,
                                POD.FTPdtBarCode,
                                POD.FNXdtSeqNo,
                                POD.FTPdtSecCode,
                                POD.FCPdtADS,
                                POD.FTPdtPromo,
                                POD.FDDateIns,
                                POD.FTTimeIns
                            FROM TSPoDT POD WITH (NOLOCK)
                            WHERE 1=1 
                            AND FTXohDocNo = '$ptDocNo' 
                            AND FTPdtSecCode = '$tSecPdt'
                        ) Base) AS c 
                        LEFT JOIN TCNMPdt P ON c.FTPdtCode = P.FTPdtCode
                        WHERE 1=1
                        AND (c.FTPdtBarCode='$ptSearch' OR P.FTPdtStkCode = (SELECT FTPdtStkCode FROM TCNMPdt WHERE FTPdtCode='$ptSearch'))
                      --WHERE 1=1
                      --(FTPdtBarCode='$ptSearch' OR FTPdtCode='$ptSearch')
                      --AND (FTPdtBarCode='$ptSearch' OR FTPdtStkCode = (SELECT FTPdtStkCode FROM TCNMPdt WHERE FTPdtCode='$ptSearch'))
            ";
            $aDataResult1 = $this->DB_SELECT($tSQL1);

            $aDataReturn = array(
                'aResult'       => $aDataResult1[0],
                'tSQL'          => $tSQL1,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataReturn = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataReturn;
    }

    public function FSaMODSCheckProductDuplicate($paData){
        $tSearch    = $paData['ptSearchPdt'];
        $tDocNo     = $paData['ptDocNo'];
        $tSQL           = "SELECT TOP 1 
                                -- (SELECT DT.FTPdtSecCode FROM TSPoDT DT WHERE DT.FTXohDocNo='$tDocNo' AND DT.FTPdtSecCode != 'ADDON' AND (FTPdtCode=(SELECT TOP 1 P1.FTPdtCode FROM TCNMPdt P1 WHERE P1.FTPdtStkCode=(SELECT P2.FTPdtStkCode FROM TCNMPdt P2 WHERE P2.FTPdtCode='$tSearch') AND P1.FTPdtStaAlwBuy='1') OR FTPdtBarCode='$tSearch')) AS TCNMPdtSpcBch,
                                FTPdtSecCode,
                                FTPdtName,
                                FTPdtBarCode
                            FROM 
                                TSPoDT
                            WHERE 
                                FTXohDocNo='$tDocNo' AND 
                                -- FCPdtOrdLot IS NOT NULL AND
                                (FTPdtCode=(SELECT TOP 1 P1.FTPdtCode FROM TCNMPdt P1 WHERE P1.FTPdtStkCode=(SELECT P2.FTPdtStkCode FROM TCNMPdt P2 WHERE P2.FTPdtCode='$tSearch') AND P1.FTPdtStaAlwBuy='1') OR FTPdtBarCode='$tSearch')";
        $aDataResult    = $this->DB_SELECT($tSQL);

        // FTPdtSecCode!='ADDON' AND
        if(count($aDataResult) > 0){
            $aDataReturn = array(
                // 'c'             => count($aDataResult),
                'aItems'        => $aDataResult,
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataReturn = array(
                // 'c'             => count($aDataResult),
                'aItems'        => $aDataResult,
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }

        return $aDataReturn;

    }

    public function FSxMODSCheckSGOItem($pdOrderDate){
        // $tSQL1           = "SELECT TOP 1 FDOrderDate FROM TCNTSGOItem (NOLOCK) WHERE FDOrderDate = '$pdOrderDate'";
        $tNewPdtDay = $this->FSxMODSGetNewPdtDay();
        $tSQL1 = "  SELECT TOP 1 
                    CASE 
                        WHEN T.FDOrderDate IS NOT NULL THEN CONVERT(VARCHAR(10),T.FDOrderDate,121) 
                        ELSE CONVERT(VARCHAR(10),GETDATE(),121) END AS FDOrderDate 
                    FROM 
                        TCNMPdt P WITH(NOLOCK)
                    LEFT JOIN TCNTSGOItem T WITH(NOLOCK) ON T.FTPdtCode=P.FTPdtCode 
                    LEFT JOIN TCNTPdtPmtDT M WITH(NOLOCK) ON M.FTPdtCode=P.FTPdtCode 
                    WHERE 
                        P.FTPdtStaAlwBuy='1' AND 
                        P.FTPdtStaActive='1' OR
                        (P.FDPdtSaleStart BETWEEN DATEADD(day, -$tNewPdtDay, '$pdOrderDate') AND '$pdOrderDate') OR 
                        (M.FDPmhDStop>='$pdOrderDate') OR 
                        T.FDOrderDate='$pdOrderDate'
                    ORDER BY T.FDOrderDate DESC";

        $aDataResult1    = $this->DB_SELECT($tSQL1);
        if(count($aDataResult1) > 0){
            $aDataReturn = array(
                'tSQL'          => $tSQL1,
                'dOrderDate'    => $aDataResult1[0]['FDOrderDate'],
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSxMODSCheckSGOItem] Order Date : '.$aDataResult1[0]['FDOrderDate']
            );
        }else{
            $aDataReturn = array(
                'tSQL'          => $tSQL1,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSxMODSCheckSGOItem] '.$aDataResult1[0]['message']
            );
            // $tSQL2           = "SELECT TOP 1 CONVERT(VARCHAR(10),FDOrderDate,121) AS FDOrderDate FROM TCNTSGOItem (NOLOCK) ORDER BY FDOrderDate DESC";
            // $aDataResult2    = $this->DB_SELECT($tSQL2);
            // if(count($aDataResult2) > 0){
            //     $aDataReturn = array(
            //         'tSQL'          => $tSQL2,
            //         'dOrderDate'    => $aDataResult2[0]['FDOrderDate'],
            //         'nStaQuery'     => 2,
            //         'tStaMessage'   => 'Found New Data',
            //     );
            // }else{
            //     $aDataReturn = array(
            //         'tSQL'          => $tSQL2,
            //         'dOrderDate'    => NULL,
            //         'nStaQuery'     => 99,
            //         'tStaMessage'   => 'Not Found Data',
            //     );
            // }
        }
        $this->FSxMODSWriteLog($aDataReturn['tStaMessage']);
        return $aDataReturn;

    }

    public function FSaMODSCheckSuggestQty(){
        $tSQL           = "SELECT TOP 1 FTSysUsrValue FROM TSysConfig (NOLOCK) WHERE FTSysCode='SugOrder'";
        $aDataResult    = $this->DB_SELECT($tSQL);

        if(count($aDataResult) > 0){
            $aDataReturn = array(
                'nValue'        => $aDataResult[0]['FTSysUsrValue'],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataReturn = array(
                'nValue'        => 0,
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataReturn;
    }

    public function FSaMODSPurgeAuto(){
        $tSQL           = " SELECT
                                FTXohDocNo
                            FROM 
                                TSPoHD
                            WHERE 
                                CONVERT(VARCHAR(10),(SELECT MAX(FDXohDocDate) FROM TSPoHD WHERE FTXohStaPrcDoc IS NOT NULL),121) >= CONVERT(VARCHAR(10),DATEADD(day, +CONVERT(INT,(SELECT FTSysUsrValue FROM TSysConfig WHERE FTSysCode = 'APurData')), FDXohDocDate),121)";
        $aDataResult    = $this->DB_SELECT($tSQL);
        if(count($aDataResult) > 0){
            foreach($aDataResult AS $tKey => $tValue){
                $aDataDeleteWHERE   = array(
                    'FTXohDocNo'    => $tValue['FTXohDocNo']
                );
                $bConfirm           = true;
                $aDelHD          = $this->DB_DELETE('TSPoHD',$aDataDeleteWHERE,$bConfirm);
                $aDelDT          = $this->DB_DELETE('TSPoDT',$aDataDeleteWHERE,$bConfirm);
            }

            if($aDelHD == 'success' && $aDelDT == 'success'){
                $aDataReturn = array(
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'Purge Auto Success',
                );
            }else{
                $aDataReturn = array(
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'Can not Delete',
                );
            }
        }else{
            $aDataReturn = array(
                'nValue'        => 0,
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataReturn;
    }

    public function FSaMODSDelDTByID($paData){
        $tDatabase          = "TSPoDT";
        if($paData['tDocNo'] != ""){
            $tDocNo = $paData['tDocNo'];
        }else{
            $tDocNo = '';
        }
        $aDataDeleteWHERE   = array(
            'FTXohDocNo'    => $tDocNo,
            'FNXdtSeqNo'    => $paData['nSeq'],
            'FTPdtSecCode'  => $paData['tSec']
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Delete TSPoDT Success',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not Delete TSPoDT',
            );
        }
        return $aDataResult;
    }

    //เช็คว่า PoFlag ที่เท่ากับ 1 มีฟิวส์ OrdLot กับ OrdPcs เท่ากับ NULL ทั้งหมดแล้วหรือไม่
    public function FSaMODSCheckPOFlag($ptDocNo){
        $tSQL           = " SELECT 
                                COUNT(FTPdtPOFlag) AS FTPdtPOFlag
                            FROM 
                                TSPoDT WITH(NOLOCK) 
                            WHERE FTPdtPOFlag   = '1' 
                            AND FTXohDocNo      = '$ptDocNo'
                            AND (FCPdtOrdLot!=NULL OR FCPdtOrdLot!='')
                            AND (FCPdtOrdPcs!=NULL OR FCPdtOrdPcs!='')";
        $aDataResult    = $this->DB_SELECT($tSQL);

        if(count($aDataResult) > 0){
            $aDataReturn = array(
                'nCount'        => $aDataResult[0]['FTPdtPOFlag'],
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataReturn = array(
                'nCount'        => 0,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataReturn;
    }

    public function FSaMODSUpdOrdLotAndOrdPcsToNull($ptDocNo){
        $tSQL       = "UPDATE 
                            TSPoDT WITH(ROWLOCK)
                       SET 
                            FCPdtOrdLot = NULL, 
                            FCPdtOrdPcs = NULL 
                       WHERE FTPdtPOFlag    = '1'
                         AND FTXohDocNo     = '$ptDocNo'
                         --AND (FCPdtOrdLot!=NULL OR FCPdtOrdLot!='')
                         --AND (FCPdtOrdPcs!=NULL OR FCPdtOrdPcs!='')";
        $tDataUpdate    = $this->DB_EXECUTE($tSQL);
        if($tDataUpdate == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMODSUpdOrdLotAndOrdPcsToNull] เคลียร์รายการแนะนำ ที่ไม่ได้สั่งซื้อ'
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMODSUpdOrdLotAndOrdPcsToNull] '.$tDataUpdate[0]['message']
            );
        }
        $this->FSxMODSWriteLog('[FSaMODSUpdOrdLotAndOrdPcsToNull] เคลียร์รายการแนะนำ ที่ไม่ได้สั่งซื้อ');
        return $aDataResult;
    }

    //หาสำนักงานใหญ่
    public function FSaMODSChkBchHQ(){
        $tSQL = "SELECT TOP 1
                    FTBchCode,
                    FTBchName
                 FROM TCNMBranch WITH(NOLOCK) 
                 WHERE FTBchHQ = '1'
        ";
        $aDataResult    = $this->DB_SELECT($tSQL);
        if(count($aDataResult) > 0){
            $aDataReturn = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMODSChkBchHQ] พบสำนักงานใหญ่ '.$aDataResult[0]['FTBchName']
            );
        }else{
            $aDataReturn = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMODSChkBchHQ] ไม่พบสำนักงานใหญ่',
            );
        }
        $this->FSxMODSWriteLog($aDataReturn['tStaMessage']);
        return $aDataReturn;
    }

    // Comsheet 2020-194 เช็คสินค้าใน DT ถ้าไม่มีผู้จำหน่ายไม่ให้อนุมัติ
    public function FSaMODSChkSupplierDT(){
        $tSQL = "SELECT 
                    FTBchCode 
                 FROM TCNMBranch WITH(NOLOCK) 
                 WHERE FTBchHQ='1'
        ";
        $aDataResult    = $this->DB_SELECT($tSQL);
        if(count($aDataResult) > 0){
            $aDataReturn = array(
                'aItems'        => $aDataResult,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Found Data',
            );
        }else{
            $aDataReturn = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Not Found Data',
            );
        }
        return $aDataReturn;
    }

    // Comsheet 2020-194
    // Create By Napat(Jame) 29/04/2020
    public function FSaMODSChkSplB4Apv($ptDocNo){
        $tSQL = "   SELECT 
                        TSPoDT.FTPdtBarCode,
                        TSPoDT.FTPdtName
                    FROM TSPoDT WITH(NOLOCK)
                    LEFT JOIN TCNMSpl ON TSPoDT.FTSplCode = TCNMSpl.FTSplCode
                    WHERE 1=1
                    AND TSPoDT.FTXohDocNo = '$ptDocNo'
                    AND ISNULL(TCNMSpl.FTSplCode,'') = ''
                    AND FCPdtOrdLot IS NOT NULL
                ";
        $aDataResult    = $this->DB_SELECT($tSQL);
        if(count($aDataResult) > 0){
            $aDataReturn = array(
                'aItems'        => $aDataResult,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMODSChkSplB4Apv] พบสินค้าไม่มีผู้จำหน่าย '.count($aDataResult).' รายการ'
            );
        }else{
            $aDataReturn = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMODSChkSplB4Apv] ไม่พบสินค้าที่ไม่มีผู้จำหน่าย'
            );
        }
        $this->FSxMODSWriteLog($aDataReturn['tStaMessage']);
        return $aDataReturn;
    }

    // Create By : Napat(Jame) 2020-06-29
    // เขียนไฟล์ Log : หน้าจอสั่งซื้อ
    public function FSxMODSWriteLog($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'ORS_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

}

?>