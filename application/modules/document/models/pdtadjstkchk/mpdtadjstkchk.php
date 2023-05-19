<?php

class mpdtadjstkchk extends Database {

    public function __construct(){
        parent::__construct();
    }

    // Create By : Napat(Jame) 2020-06-26
    // เขียนไฟล์ Log : หน้าจอตรวจนับ
    public function FSxMPASWriteLog($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'ADJ_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

    //ดึงข้อมูล ที่เก็บ ไปแสดงที่ แท็บคลัง
    public function FSxMPASGetLocation(){
        $tSQL = "SELECT DISTINCT 
                    FTPlcCode,
                    FTPlcName 
                 FROM TCNMPdtLoc WITH (NOLOCK)";
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nStaQuery'         => 1,
                'tStaMessage'       => '[FSxMPASGetLocation] พบข้อมูลที่เก็บ '.count($aDataList).' รายการ',
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSxMPASGetLocation] ไม่พบข้อมูลในตาราง TCNMPdtLoc',
            );
        }
        return $aDataResult;
    }

    public function FSxMPASGetWaHouse(){
        $tSQL = "SELECT TOP 1 * FROM TCNMWaHouse WITH (NOLOCK) WHERE FTWahCode='001'";
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nStaQuery'         => 1,
                'tStaMessage'       => '[FSxMPASGetWaHouse] พบข้อมูลคลังสินค้า '.$aDataList[0]['FTWahCode'].' '.$aDataList[0]['FTWahName'],
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSxMPASGetWaHouse] ไม่พบข้อมูลคลังสินค้า FTWahCode = 001',
            );
        }
        return $aDataResult;
    }

    public function FSxMPASGetDataTable($paData){
        $aRowLen    = FCNaHCallLenData($paData['nRow'],$paData['nPage']);

        // $tWhereFilterJoin = "";
        $tWhereFilter     = "";

        if(isset($paData['tBchCode']) && !empty($paData['tBchCode'])){
            $tWhereFilter       .= " AND A.FTBchCode = '$paData[tBchCode]' ";
            // $tWhereFilterJoin   .= " AND DT.FTBchCode = '$paData[tBchCode]' ";
        }else{
            $tWhereFilter       .= " AND A.FTBchCode = '' ";
            // $tWhereFilterJoin   .= " AND DT.FTBchCode = '' ";
        }

        if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
            $tWhereFilter       .= " AND A.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
            // $tWhereFilterJoin   .= " AND DT.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocNo = '' ";
            // $tWhereFilterJoin   .= " AND DT.FTIuhDocNo = '' ";
        }

        if(isset($paData['nTypePage']) && $paData['nTypePage'] != "1"){
            $tWhereFilter       .= " AND A.FTIuhDocType = '2' ";
            // $tWhereFilterJoin   .= " AND DT.FTIuhDocType = '2' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocType = '1' ";
            // $tWhereFilterJoin   .= " AND DT.FTIuhDocType = '1' ";
        }

        $tSQL = "SELECT
                    L.*
                FROM (
                        SELECT
                            ROW_NUMBER() OVER(ORDER BY A.FNIudSeqNo ASC) AS RowID,
                            A.*
                        FROM TCNTPdtChkDT A WITH (NOLOCK)
                        WHERE 1=1 
                        $tWhereFilter
                    ) AS L
                WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
        ";
        // echo $tSQL;
        // exit;
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aFoundRow      = $this->FSaMPASGetPageList($paData);
            $nFoundRow      = $aFoundRow['counts'];
            $nPageAll       = ceil($nFoundRow/$paData['nRow']);
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nAllRow'           => $nFoundRow,
                'nAllPage'          => $nPageAll,
                'nCurrentPage'      => $paData['nPage'],
                'nStaQuery'         => 1,
                'tStaMessage'       => '[FSxMPASGetDataTable] พบข้อมูลสินค้า '.count($aDataList).' รายการ'
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSxMPASGetDataTable] ไม่พบข้อมูลสินค้า',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASGetPageList($paData){

        $tWhereFilter = "";

        if(isset($paData['tBchCode']) && !empty($paData['tBchCode'])){
            $tWhereFilter       .= " AND A.FTBchCode = '$paData[tBchCode]' ";
        }else{
            $tWhereFilter       .= " AND A.FTBchCode = '' ";
        }

        if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
            $tWhereFilter       .= " AND A.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocNo = '' ";
        }

        if(isset($paData['nTypePage']) && $paData['nTypePage'] != "1"){
            $tWhereFilter       .= " AND A.FTIuhDocType = '2' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocType = '1' ";
        }

        $tSQL = "   SELECT COUNT(A.FNIudSeqNo) AS counts
                    FROM TCNTPdtChkDT A WITH (NOLOCK)
                    WHERE 1=1
                    $tWhereFilter
        ";

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    public function FSxMPASGetDataPdtWithOutSystemTable($paData){
        $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
        $tWhereFilter   = "";

        if(isset($paData['nTypePage']) && $paData['nTypePage'] != "1"){
            if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
                //กรณีใบรวมมีเลขที่เอกสารแล้ว
                $tWhereFilter   .= " AND A.FTIuhDocNoType2 = '$paData[FTIuhDocNo]' ";
            }else{
                //กรณีใบรวมยังไม่มีเลขที่เอกสาร
                $tSQLGetDocNo = "   SELECT 
                                        A.FTIuhDocNo
                                    FROM 
                                        TCNTPdtChkHD A WITH(NOLOCK)
                                    WHERE 1=1
                                        AND ((A.FTWahCode='001') AND (A.FTIuhDocType='1') AND (A.FTIuhStaPrcDoc='2')) 
                                        AND ( ISNULL(A.FTCstCode,'') = '' OR A.FTCstCode = 'CFM-HQ' )
                                        AND (A.FTIuhStaDoc='1')
                                        AND (A.FTSplCode = '$paData[tPassword]')
                ";
                $tWhereFilter   .= " AND A.FTIuhDocNo IN ( $tSQLGetDocNo ) ";
            }
        }else{
            if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
                //กรณีใบย่อยที่มีเลขที่เอกสาร
                $tWhereFilter   .= " AND A.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
            }else{
                //กรณีใบย่อยที่ไม่มีเลขที่เอกสาร ให้ค้นหาเลขมั่วๆ ไม่ให้เจอข้อมูล
                $tWhereFilter   .= " AND A.FTIuhDocNo = '99' ";
            }
        }

        if(isset($paData['tBchCode']) && !empty($paData['tBchCode'])){
            $tWhereFilter   .= " AND A.FTBchCode = '$paData[tBchCode]' ";
        }else{
            $tWhereFilter   .= " AND A.FTBchCode = '' ";
        }

        $tSQL = "SELECT
                    L.*
                FROM (
                        SELECT
                            ROW_NUMBER() OVER(ORDER BY A.FTPdtBarCode ASC) AS RowID,
                            A.FTIuhDocNo,
                            A.FTPdtBarCode,
                            A.FTPdtName,
                            A.FCIudUnitC1,
                            A.FTPlcCode,
                            A.FCIudSetPrice,
                            CONVERT(VARCHAR(10),A.FDIudChkDate,121) AS FDIudChkDate,
                            A.FTIudChkTime
                        FROM TCNTPdtStkNotExist A WITH (NOLOCK)
                        WHERE 1=1
                        $tWhereFilter
                    ) AS L
                WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
        ";
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aFoundRow      = $this->FSaMPASGetPageListPdtWithOutSystem($paData);
            $nFoundRow      = $aFoundRow['counts'];
            $nPageAll       = ceil($nFoundRow/$paData['nRow']);
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nAllRow'           => $nFoundRow,
                'nAllPage'          => $nPageAll,
                'nCurrentPage'      => $paData['nPage'],
                'nStaQuery'         => 1,
                'tStaMessage'       => '[FSxMPASGetDataPdtWithOutSystemTable] พบสินค้าไม่อยู่ในระบบ '.count($aDataList).' รายการ'
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSxMPASGetDataPdtWithOutSystemTable] ไม่พบสินค้าไม่อยู่ในระบบ',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASGetPageListPdtWithOutSystem($paData){

        $tWhereFilter   = "";

        if(isset($paData['nTypePage']) && $paData['nTypePage'] != "1"){
            if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
                $tWhereFilter   .= " AND A.FTIuhDocNoType2 = '$paData[FTIuhDocNo]' ";
            }else{
                $tWhereFilter   .= " AND ISNULL(A.FTIuhDocNoType2,'') = '' ";
            }
        }else{
            if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
                $tWhereFilter   .= " AND A.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
            }else{
                $tWhereFilter   .= " AND A.FTIuhDocNo = '' ";
            }
        }

        if(isset($paData['tBchCode']) && !empty($paData['tBchCode'])){
            $tWhereFilter   .= " AND A.FTBchCode = '$paData[tBchCode]' ";
        }else{
            $tWhereFilter   .= " AND A.FTBchCode = '' ";
        }

        $tSQL = "   SELECT COUNT(A.FTPdtBarCode) AS counts
                    FROM TCNTPdtStkNotExist A WITH (NOLOCK)
                    WHERE 1=1 
                    $tWhereFilter
        ";

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    public function FSxMPASChangeProduct($paData){
        $tDocNo     = $paData['ptDocNo'];
        $nSeq       = $paData['pnSeq'];
        $tPdtCode   = $paData['ptPdtCode'];
        $aGetBranch = getBranch();

        $tDatabase          = "TCNTPdtChkDT";
        $aDataDeleteWHERE   = array(
            'FNIudSeqNo'    => $nSeq,
            'FTIuhDocNo'    => $tDocNo
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $tSQL  = "INSERT INTO TCNTPdtChkDT (FTBchCode,FTIuhDocNo,FTPlcCode,FTPdtCode,FTIudBarCode,FTPdtName,FTPunName,FCIudUnitC1,FCIudUnitC2,FCIudQtyC1,FCIudQtyC2,FCIudWahQty,FCIudQtyDiff,FCIudQtyBal,FNIudSeqNo,FTPunCode,FCIudUnitFact,FTIudStkCode,FCIudSetPrice,FCIudStkFac,FTPgpChain,FCIudCost,FTPdtSaleType,FTPdtNoDis,FDIudChkDate,FTIudChkTime)";
            $tSQL .= "SELECT 
                    '$aGetBranch[FTBchCode]'        AS FTBchCode,
                    '$tDocNo'                       AS FTIuhDocNo,
                    ''                              AS FTPlcCode,
                    TCNMPdt.FTPdtCode               AS FTPdtCode,
                    TCNMPdtBar.FTPdtBarCode         AS FTIudBarCode,
                    TCNMPdt.FTPdtName               AS FTPdtName,
                    TCNMPdtUnit.FTPunName           AS FTPunName,
                    0                               AS FCIudUnitC1, 
                    0                               AS FCIudUnitC2, 
                    0                               AS FCIudQtyC1, 
                    0                               AS FCIudQtyC2, 
                    ISNULL(TCNMPdt.FCPdtQtyRet,0)   AS FCIudWahQty, 
                    0                               AS FCIudQtyDiff, 
                    0                               AS FCIudQtyBal,
                    $nSeq                           AS FNIudSeqNo, 
                    TCNMPdt.FTPunCode               AS FTPunCode, 
                    ISNULL(TCNMPdt.FCPdtUnitFact,0) AS FCIudUnitFact, 
                    TCNMPdt.FTPdtStkCode            AS FTIudStkCode, 
                    ISNULL((SELECT TOP 1 FCPdtRetPri1 FROM TCNMPdtBar TBar2 WITH (NOLOCK) WHERE TBar2.FTPdtCode = TCNMPdtBar.FTPdtCode AND TBar2.FTPdtBarCode = TCNMPdtBar.FTPdtBarCode AND TBar2.FDPdtPriAffect <= GETDATE() ORDER BY TCNMPdtBar.FDPdtPriAffect DESC ),0) AS FCIudSetPrice, 
                    ISNULL(TCNMPdt.FCPdtStkFac,0)   AS FCIudStkFac,
                    TCNMPdt.FTPgpChain              AS FTPgpChain, 
                    ISNULL(TCNMPdt.FCPdtCostStd,0)  AS FCIudCost, 
                    FTPdtSaleType                   AS FTPdtSaleType, 
                    TCNMPdt.FTPdtStaReturn          AS FTPdtNoDis,
                    NULL                            AS FDIudChkDate,
                    NULL                            AS FTIudChkTime
                FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                WHERE TCNMPdt.FTPdtCode='$tPdtCode' AND (TCNMPdtBar.FTPdtBarCode <> '') 
                AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                AND TCNMPdt.FTPdtStaActive = '1' 
                AND TCNMPdtBar.FTPdtBarCode NOT IN (SELECT FTIudBarCode FROM TCNTPdtChkDT WHERE FTIuhDocNo='$tDocNo' GROUP BY FTIudBarCode)
                ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode";
            $tReturnInsert = $this->DB_EXECUTE($tSQL);
            if($tReturnInsert == 'success'){
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'Change Data Success',
                );
            }else{
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'Error (FSxMPASChangeProduct)',
                );
            }
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Error Delete TCNTPdtChkDT',
            );
        }
        return $aDataResult;
    }

    public function FSxMPASChkPrdDuplicate($paData){
        $tDocNo     = $paData['ptDocNo'];
        $tFromCode  = $paData['ptFromCode'];
        $tToCode    = $paData['ptToCode'];
        $tPlcCode   = $paData['ptPlcCode'];
        $tGrpCode   = $paData['ptGrpCode'];
        $tLocCode   = $paData['ptLocCode'];
        $aGetBranch = getBranch();

        switch($paData['ptTab']){
            case 'Pdt':
                $tSQL = "  SELECT *
                            FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                            WHERE (TCNMPdtBar.FTPdtBarCode BETWEEN '$tFromCode' AND '$tToCode')
                            AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            AND (TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode) AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' AND TCNMPdt.FTPdtStaAlwSale = '1'
                ";
                // $tSQL = " SELECT *
                //             FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                //             WHERE ((TCNMPdt.FTPdtCode  Between '$tFromCode' AND '$tToCode')) AND AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                //             AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                //             AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                //             AND TCNMPdt.FTPdtStaActive = '1' 
                // ";
                break;
            case 'PdtSup':
                $tSQL = "  SELECT *
                            FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK)
                            WHERE ((TCNMPdt.FTSplCode Between '$tFromCode' AND '$tToCode')) AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) AND TCNMPdt.FTPdtType 
                            IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                ";
                break;
            case 'User':
                $tSQL = "  SELECT *
                            FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                            WHERE ((TCNMPdt.FTUsrCode  Between '$tFromCode' AND '$tToCode')) AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() AND TCNMPdt.FTPdtStaActive = '1' 
                ";
                break;
            case 'Group':
                $tSQL = "  SELECT *
                            FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                            WHERE (TCNMPdt.FTPgpChain LIKE  '$tGrpCode%') AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) AND TCNMPdt.FTPdtType 
                            IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                ";
                break;
            case 'Location':
                $tSQL = "  SELECT *
                            FROM TCNTPdtLocSeq WITH (NOLOCK),TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK)  
                            WHERE (TCNTPdtLocSeq.FTPdtPlcCode = '$tLocCode') AND (TCNMPdtBar.FTPdtBarCode = TCNTPdtLocSeq.FTPdtBarCode) AND (TCNMPdtBar.FTPdtBarCode  <> '') AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                ";
                break;
            case 'PdtAndBar':
                $tSQL = "  SELECT *
                            FROM TCNMPdt WITH (NOLOCK),TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK) 
                            WHERE ( (TCNMPdt.FTPdtCode  Between '$tFromCode' AND '$tToCode') OR (TCNMPdtBar.FTPdtBarCode BETWEEN '$tFromCode' AND '$tToCode') )
                            AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            AND (TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode) AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' AND TCNMPdt.FTPdtStaAlwSale = '1'
                ";
                break;
        }
        // print_r($tSQL);
        // exit;
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    public function FSxMPASAddProduct($paData){
        $tDocNo     = $paData['ptDocNo'];
        $tDocDate   = $paData['ptDocDate'];
        $tFromCode  = $paData['ptFromCode'];
        $tToCode    = $paData['ptToCode'];
        $tPlcCode   = $paData['ptPlcCode'];
        $tGrpCode   = $paData['ptGrpCode'];
        $tLocCode   = $paData['ptLocCode'];
        $aGetBranch = getBranch();
        $tUser      = $_SESSION["SesUsername"];
        $dDate      = date('Y-m-d');
        $tTime      = date('H:i:s');


        //ค้นหา Seq ล่าสุด
        $tSQL_Seq   = "SELECT TOP 1 FNIudSeqNo,FTPszName FROM TCNTPdtChkDT WHERE FTIuhDocNo='$tDocNo' AND FTIuhDocType='1' AND FTBchCode='$aGetBranch[FTBchCode]' ORDER BY FNIudSeqNo DESC";
        $aDataList  = $this->DB_SELECT($tSQL_Seq);
        
        if(count($aDataList) > 0){
            $nSeq       = $aDataList[0]['FNIudSeqNo'];
            $tPassword  = $aDataList[0]['FTPszName'];
        }else{
            $nSeq       = 0;
            $tPassword  = '';
        }

        $tSQL  = "INSERT INTO TCNTPdtChkDT (FTBchCode,FTIuhDocNo,FDIuhDocDate,FTIuhDocType,FTPlcCode,FTPdtCode,FTIudBarCode,FTPdtName,FTPunName
                    ,FTWahCode,FCIudUnitC1,FCIudUnitC2,FCIudQtyC1,FCIudQtyC2,FCIudWahQty,FCIudQtyDiff,FCIudQtyBal,FNIudSeqNo,FTPunCode
                    ,FCIudUnitFact,FTIudStkCode,FCIudSetPrice,FCIudStkFac,FTPgpChain,FCIudCost,FTPdtSaleType,FTPdtNoDis,FDIudChkDate
                    ,FTIudChkTime,FCIudDIsAvg,FCIudFootAvg,FCIudRePackAvg,FTIudChkUser,FDDateUpd,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns,FTPszName)
                 ";
        $tSQL .= "SELECT 
                    '$aGetBranch[FTBchCode]'        AS FTBchCode,
                    '$tDocNo'                       AS FTIuhDocNo,
                    '$tDocDate'                     AS FDIuhDocDate,
                    '1'                             AS FTIuhDocType,
                    '$tPlcCode'                     AS FTPlcCode,
                    TCNMPdt.FTPdtCode               AS FTPdtCode,
                    TCNMPdtBar.FTPdtBarCode         AS FTIudBarCode,
                    TCNMPdt.FTPdtName               AS FTPdtName,
                    TCNMPdtUnit.FTPunName           AS FTPunName,
                    '001'                           AS FTWahCode,
                    0                               AS FCIudUnitC1, 
                    0                               AS FCIudUnitC2, 
                    0                               AS FCIudQtyC1, 
                    0                               AS FCIudQtyC2, 
                    ISNULL(TCNMPdt.FCPdtQtyRet,0)   AS FCIudWahQty, 
                    0                               AS FCIudQtyDiff, 
                    0                               AS FCIudQtyBal,
                    $nSeq + ROW_NUMBER() OVER(ORDER BY TCNMPdtBar.FDPdtPriAffect DESC) AS FNIudSeqNo,
                    --ISNULL((SELECT TOP 1 FNIudSeqNo FROM TCNTPdtChkDT WHERE FTIuhDocNo='$tDocNo' AND FTIuhDocType='1' AND FTBchCode='$aGetBranch[FTBchCode]' ORDER BY FNIudSeqNo DESC),0) + ROW_NUMBER() OVER(ORDER BY TCNMPdtBar.FDPdtPriAffect DESC) AS FNIudSeqNo, 
                    TCNMPdt.FTPunCode               AS FTPunCode, 
                    ISNULL(TCNMPdt.FCPdtUnitFact,0) AS FCIudUnitFact, 
                    TCNMPdt.FTPdtStkCode            AS FTIudStkCode, 
                    ISNULL((SELECT TOP 1 FCPdtRetPri1 FROM TCNMPdtBar TBar2 WITH (NOLOCK) WHERE TBar2.FTPdtCode = TCNMPdtBar.FTPdtCode AND TBar2.FTPdtBarCode = TCNMPdtBar.FTPdtBarCode AND TBar2.FDPdtPriAffect <= GETDATE() ORDER BY TCNMPdtBar.FDPdtPriAffect DESC ),0) AS FCIudSetPrice, 
                    ISNULL(TCNMPdt.FCPdtStkFac,0)   AS FCIudStkFac,
                    TCNMPdt.FTPgpChain              AS FTPgpChain, 
                    ISNULL(TCNMPdt.FCPdtCostStd,0)  AS FCIudCost, 
                    TCNMPdt.FTPdtSaleType           AS FTPdtSaleType, 
                    TCNMPdt.FTPdtStaReturn          AS FTPdtNoDis,
                    NULL                            AS FDIudChkDate,
                    NULL                            AS FTIudChkTime,
                    0                               AS FCIudDIsAvg,
                    0                               AS FCIudFootAvg,
                    0                               AS FCIudRePackAvg,
                    ''                              AS FTIudChkUser,
                    '$dDate'                        AS FDDateUpd,
                    '$tTime'                        AS FTTimeUpd,
                    '$tUser'                        AS FTWhoUpd,
                    '$dDate'                        AS FDDateIns,
                    '$tTime'                        AS FTTimeIns,
                    '$tUser'                        AS FTWhoIns,
                    '$tPassword'                    AS FTPszName
        ";

        //TCNMPdt.FTPdtType IN ('1','2','3','4','5','6','7')
        switch($paData['ptTab']){
            case 'Pdt':
                //,TCNMPdtUnit WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK)
                /*
                " AND (P.FTPdtType IN('1','4'))",
                " AND (P.FTPdtStaSet IN('1','2','3'))", 
                " AND P.FTPdtStaAudit = '1'",
                " AND P.FTPdtStaActive = '1'",
                " AND P.FTPdtStaAlwSale = '1' "*/
                $tSQL .= "
                            FROM TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode 
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE (TCNMPdtBar.FTPdtBarCode BETWEEN '$tFromCode' AND '$tToCode')
                            AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            --AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode)
                            AND TCNMPdt.FTPdtType IN ('1','4') 
                            AND TCNMPdt.FTPdtStaAudit = '1' 
                            AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            AND TCNMPdt.FTPdtStaAlwSale = '1'
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
            case 'PdtSup':
                $tSQL .= "
                            FROM TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode 
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE ((TCNMPdt.FTSplCode Between '$tFromCode' AND '$tToCode')) AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') 
                            AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
            case 'User':
                $tSQL .= "
                            FROM TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode  
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE ((TCNMPdt.FTUsrCode  Between '$tFromCode' AND '$tToCode')) 
                            AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') 
                            AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
            case 'Group':
                $tSQL .= "
                            FROM TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode  
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE (TCNMPdt.FTPgpChain LIKE  '$tGrpCode%') AND (TCNMPdtBar.FTPdtBarCode <> '') AND NOT (TCNMPdtBar.FTPdtBarCode  IS NULL) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtUnit.FTPunCode =  TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') 
                            AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
            case 'Location':
                $tSQL .= "
                            FROM TCNTPdtLocSeq WITH (NOLOCK),TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode  
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE (TCNTPdtLocSeq.FTPdtPlcCode = '$tLocCode') AND (TCNMPdtBar.FTPdtBarCode = TCNTPdtLocSeq.FTPdtBarCode) AND (TCNMPdtBar.FTPdtBarCode  <> '') 
                            AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) 
                            AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            ORDER BY TCNTPdtLocSeq.FCPdtPlcSeq , TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
            case 'PdtAndBar':
                $tSQL .= "
                            FROM TCNMPdt WITH (NOLOCK)
                            LEFT JOIN TCNMPdtBar ON TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                            LEFT JOIN TCNMPdtUnit ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode 
                            LEFT JOIN TCNTPdtChkDT WITH (NOLOCK) ON TCNMPdtBar.FTPdtBarCode = TCNTPdtChkDT.FTIudBarCode AND TCNTPdtChkDT.FTIuhDocNo='$tDocNo' AND TCNTPdtChkDT.FTBchCode='$aGetBranch[FTBchCode]' AND TCNTPdtChkDT.FTPlcCode='$tPlcCode' AND TCNTPdtChkDT.FTIuhDocType='1'
                            WHERE ( (TCNMPdt.FTPdtCode  Between '$tFromCode' AND '$tToCode') OR (TCNMPdtBar.FTPdtBarCode BETWEEN '$tFromCode' AND '$tToCode') )
                            AND NOT (TCNMPdtBar.FTPdtBarCode IS NULL) 
                            --AND (TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode) 
                            --AND (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            --AND (TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode)
                            AND TCNMPdt.FTPdtType IN ('1','4') AND TCNMPdt.FTPdtStaAudit = '1' AND TCNMPdtBar.FDPdtPriAffect <= GETDATE() 
                            AND TCNMPdt.FTPdtStaActive = '1' 
                            AND TCNTPdtChkDT.FTPlcCode IS NULL
                            AND TCNMPdt.FTPdtStaAlwSale = '1'
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode
                ";
                break;
        }
        $tReturnInsert = $this->DB_EXECUTE($tSQL);
        if($tReturnInsert == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'insert data success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 88,
                'tStaMessage'   => 'can not insert data',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASAddEditHD($paData){
        $tSQL = "UPDATE TCNTPdtChkHD WITH(ROWLOCK)
                 SET 
                    FDIuhDocDate          = '$paData[FDIuhDocDate]',
                    FTIuhHhdNumber        = '$paData[FTIuhHhdNumber]',
                    FTWahCode             = '$paData[FTWahCode]',
                    FTIuhRmk              = '$paData[FTIuhRmk]',
                    FDDateUpd             = '$paData[FDDateUpd]',
                    FTTimeUpd             = '$paData[FTTimeUpd]',
                    FTWhoUpd              = '$paData[FTWhoUpd]'
                 WHERE FTIuhDocNo         = '$paData[FTIuhDocNo]'
                   AND FTBchCode          = '$paData[FTBchCode]'
                ";
        $tReturnUpdate = $this->DB_EXECUTE($tSQL);
        if($tReturnUpdate == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Update TCNTPdtChkHD Success',
            );
        }else{
            $tDataInsert = $this->DB_INSERT('TCNTPdtChkHD',$paData);
            if($tDataInsert == 'success'){
                $aDataResult = array(
                    'nStaQuery'     => 1,
                    'tStaMessage'   => '[FSaMPASAddEditHD] สร้างเอกสารหมายเลข '.$paData['FTIuhDocNo']
                );
            }else{
                $aDataResult = array(
                    'tSQL'          => $tDataInsert,
                    'nStaQuery'     => 99,
                    'tStaMessage'   => '[FSaMPASAddEditHD] สร้างเอกสาร ล้มเหลว'
                );
            }
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSaMPASUpdDocNoToDT($paData){
        $tSQL = "UPDATE 
                    TCNTPdtChkDT WITH(ROWLOCK)
                 SET 
                    FTIuhDocNo      = '$paData[FTIuhDocNo]',
                    FDIuhDocDate    = '$paData[FDIuhDocDate]',
                    FDDateUpd       = '$paData[FDDateUpd]',
                    FTTimeUpd       = '$paData[FTTimeUpd]',
                    FTWhoUpd        = '$paData[FTWhoUpd]',
                    FDDateIns       = '$paData[FDDateIns]',
                    FTTimeIns       = '$paData[FTTimeIns]',
                    FTWhoIns        = '$paData[FTWhoIns]'
                 WHERE FTBchCode    = '$paData[FTBchCode]'
                   AND FTIuhDocNo     = ''
                   AND FTIuhDocType   = '$paData[FTIuhDocType]'
                ";
        $tReturnInsert = $this->DB_EXECUTE($tSQL);
        if($tReturnInsert == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Update DocNo to TCNTPdtChkDT Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not update DocNo (FSaMPASUpdDocNoToDT)',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASGetDataHD($paData){
        // ตรวจสอบเอกสารใบรวมไม่สมบูรณ์
        $tSQL = "SELECT 
                    TOP 1 * 
                FROM 
                    TCNTPdtChkHD WITH (NOLOCK) 
                WHERE 
                    ((FTIuhDocNo LIKE 'IU$paData[FTBchCode]%') AND (FTIuhDocType='2')) 
                    AND FTIuhStaDoc <> '3' 
                    AND FTIuhStaPrcDoc <> '1' 
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    AND FTBchCode = '$paData[FTBchCode]'
                ORDER BY FTIuhDocNo DESC
        ";
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){ // พบเอกสารใบรวมไม่สมบูรณ์
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList[0],
                'nStaQuery'         => 1,
                'nSetPageType'      => 2,
                'tStaMessage'       => 'พบเอกสารใบรวมไม่สมบูรณ์',
            );
        }else{
            // ตรวจสอบเอกสารใบย่อยไม่สมบูรณ์
            $tSQL = "   SELECT TOP 1 HD.*
                        FROM TCNTPdtChkHD HD WITH (NOLOCK)
                        INNER JOIN TCNTPdtChkDT DT WITH (NOLOCK) ON HD.FTIuhDocNo = DT.FTIuhDocNo
                        WHERE HD.FTIuhDocNo LIKE 'IU$paData[FTBchCode]%' 
                            AND HD.FTBchCode = '$paData[FTBchCode]'
                            AND HD.FTIuhDocType = '1' 
                            AND HD.FTIuhStaDoc = '1'
                            AND HD.FNIuhStaDocAct = 1
                            AND ISNULL(HD.FTIuhStaPrcDoc,'') = ''
                            AND ISNULL(DT.FDIudChkDate,'') = ''
                            AND ISNULL(DT.FTIudChkTime,'') = ''
                        ORDER BY FTIuhDocNo DESC
                    ";
            $aDataList = $this->DB_SELECT($tSQL);
            if( count($aDataList) > 0 && $paData['FTIuhDocNo'] == "" ){ // พบเอกสารใบย่อยไม่สมบูรณ์
                $aDataResult = array(
                    'tSQL'              => $tSQL,
                    'aItems'            => $aDataList[0],
                    'nStaQuery'         => 88,
                    'nSetPageType'      => 1,
                    'tStaMessage'       => 'พบเอกสารใบย่อยไม่สมบูรณ์',
                );
            }else{
                $tSQL = "   SELECT TOP 1 * 
                            FROM TCNTPdtChkHD WITH (NOLOCK)
                            WHERE FTIuhDocNo = '$paData[FTIuhDocNo]'
                            AND FTIuhStaDoc != '3' 
                        ";
                $aDataList = $this->DB_SELECT($tSQL);
                if(count($aDataList) > 0){
                    $aDataResult = array(
                        'tSQL'              => $tSQL,
                        'aItems'            => $aDataList[0],
                        'nStaQuery'         => 1,
                        'tStaMessage'       => 'Select Data from TCNTPdtChkHD Success',
                    );
                }else{
                    $aDataResult = array(
                        'tSQL'              => $tSQL,
                        'nStaQuery'         => 99,
                        'tStaMessage'       => 'error Can not select data from TCNTPdtChkHD (FSxMPASCheckHD)',
                    );
                }
            }
        }
        return $aDataResult;
    }

    public function FSaMPASDelProduct($paData){
        $tDatabase          = "TCNTPdtChkDT";
        $aDataDeleteWHERE   = array(
            'FNIudSeqNo'    => $paData['ptSeq'],
            'FTIuhDocNo'    => $paData['ptDocNo']
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMPASDelProduct] ลบสินค้า FNIudSeqNo = '.$paData['ptSeq'].' AND FTIuhDocNo = '.$paData['ptDocNo']
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMPASDelProduct] ลบสินค้า FNIudSeqNo = '.$paData['ptSeq'].' AND FTIuhDocNo = '.$paData['ptDocNo'].' ล้มเหลว',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASSortProduct($paData){
        $tSQL = "UPDATE TCNTPdtChkDT WITH(ROWLOCK)
                    SET FNIudSeqNo = x.NewSeq 
                 FROM TCNTPdtChkDT DT 
                    INNER JOIN (
                    SELECT 
                        ROW_NUMBER() OVER (ORDER BY FNIudSeqNo) AS NewSeq,
                        FNIudSeqNo AS FNIudSeqNo_x,
                        FTIuhDocNo AS FTIuhDocNo_x
                    FROM TCNTPdtChkDT AS y WHERE y.FTIuhDocNo='$paData[ptDocNo]'
                    ) x
                 ON DT.FTIuhDocNo  = x.FTIuhDocNo_x AND DT.FNIudSeqNo = x.FNIudSeqNo_x";
        $tReturnInsert = $this->DB_EXECUTE($tSQL);
        if($tReturnInsert == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Sort Seq Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not Sort Seq (FSaMPASSortProduct)',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASCancelHD($paData){
        $tSQL = "UPDATE 
                    TCNTPdtChkHD WITH(ROWLOCK) 
                 SET 
                    FTIuhStaDoc = '3' 
                 WHERE (FTIuhDocNo = '$paData[FTIuhDocNo]' OR FTIuhDocRef = '$paData[FTIuhDocNo]')
        ";
        $tReturnInsert = $this->DB_EXECUTE($tSQL);
        if($tReturnInsert == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMPASCancelHD] ยกเเลิกเอกสารหมายเลข '.$paData['FTIuhDocNo'],
            );
            $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMPASCancelHD] ยกเเลิกเอกสารหมายเลข '.$paData['FTIuhDocNo'].' ล้มเหลว',
            );
            $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        }
        return $aDataResult;
    }

    public function FSaMPASCheckDateTime($ptDocNo){
        $tSQL = "SELECT 
                    FTPdtCode 
                 FROM 
                    TCNTPdtChkDT WITH (NOLOCK)
                 WHERE ((FDIudChkDate='' OR FDIudChkDate IS NULL) OR (FTIudChkTime='' OR FTIudChkTime IS NULL))
                   AND FTIuhDocNo='$ptDocNo'";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASUpdateDateTime($paData){
        // $tSQL = "UPDATE TCNTPdtChkDT WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate = CONVERT(VARCHAR(10),GETDATE(),121),
        //                 FTIudChkTime = CONVERT(VARCHAR(8),GETDATE(),108)
        //         FROM TCNTPdtChkDT DT 
        //             INNER JOIN (
        //             SELECT 
        //                 FTIuhDocNo AS FTIuhDocNo
        //             FROM TCNTPdtChkDT AS y WHERE y.FTIuhDocNo='$ptDocNo'
        //             ) x
        //         ON DT.FTIuhDocNo = x.FTIuhDocNo";
        $tSQL = "UPDATE 
                    TCNTPdtChkDT WITH(ROWLOCK)
                 SET 
                    FDIudChkDate = CONVERT(VARCHAR,GETDATE(),23),
                    FTIudChkTime = CONVERT(VARCHAR,GETDATE(),24)
                 WHERE  FTIuhDocNo	= '$paData[tDocNo]'
                    AND FTBchCode	= '$paData[tBchCode]'
                    AND (FDIudChkDate IS NULL OR FTIudChkTime IS NULL)";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Update TCNTPdtChkDT Success',
            );
        }else{
            $aDataResult = array(
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not Update (FSaMPASUpdateDateTime)',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASAddConfirmCode($paData){
        $tSQL1 = "  UPDATE TCNTPdtChkHD WITH(ROWLOCK) 
                    SET FTSplCode = '$paData[FTSplCode]' 
                    WHERE FTIuhDocNo = '$paData[FTIuhDocNo]' 
                      AND FTBchCode = '$paData[FTBchCode]' 
                      AND ISNULL(FTSplCode,'') = '' 
                 ";
        $tResult1 = $this->DB_EXECUTE($tSQL1);

        $tSQL2 = "  UPDATE TCNTPdtChkDT WITH(ROWLOCK) 
                    SET FTPszName = '$paData[FTSplCode]' 
                    WHERE FTIuhDocNo = '$paData[FTIuhDocNo]' 
                      AND FTBchCode = '$paData[FTBchCode]' 
                      AND ISNULL(FTPszName,'') = '' 
                 ";
        $tResult2 = $this->DB_EXECUTE($tSQL2);

        $aDataResult = array(
            'tSQL1'         => $tSQL1,
            'tSQL2'         => $tSQL2,
            'nStaQuery'     => 1,
            'tStaMessage'   => '[FSaMPASAddConfirmCode] บันทึกรหัสยืนยัน '.$paData['FTSplCode']
        );
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSaMPASEditInLine($paData){
        $tSQL = "UPDATE TCNTPdtChkDT WITH(ROWLOCK) SET";
        if($paData['FTIuhDocType'] == "1"){
            $tSQL .= "  FCIudUnitC1     = $paData[FCIudQtyC1],
                        FCIudQtyC1      = ($paData[FCIudQtyC1] * FCIudStkFac),
                        FTPlcCode       = '$paData[FTPlcCode]',
                        FDIudChkDate    = CASE 
                                            WHEN FDIudChkDate IS NULL THEN CONVERT(VARCHAR,GETDATE(),23)
                                            ELSE '$paData[FDIudChkDate]'
                                          END,
                        FTIudChkTime    = CASE 
                                            WHEN FTIudChkTime IS NULL THEN CONVERT(VARCHAR,GETDATE(),24)
                                            ELSE '$paData[FTIudChkTime]'
                                          END
                    WHERE 1=1
                    AND FTIuhDocNo     = '$paData[FTIuhDocNo]'
                    AND FNIudSeqNo     = '$paData[FNIudSeqNo]'
                    AND FTIuhDocType   = '$paData[FTIuhDocType]'
            ";
        }else{
            $tSQL .= "  FCIudQtyBal     = $paData[FCIudQtyBal],
                        FCIudQtyDiff    = $paData[FCIudQtyBal] - FCIudWahQty
                    WHERE 1=1
                    AND FTIuhDocNo     = '$paData[FTIuhDocNo]'
                    AND FNIudSeqNo     = '$paData[FNIudSeqNo]'
                    AND FTIuhDocType   = '$paData[FTIuhDocType]'
            ";
        }
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Update Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not Update (FSaMPASEditInLine)',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASEditInLinePdtWithOutSystem($paData){
        $tSQL = "   UPDATE TCNTPdtStkNotExist WITH(ROWLOCK) SET
                        FTPdtName       = '$paData[FTPdtName]',
                        FCIudSetPrice   = $paData[FCIudSetPrice],
                        FCIudUnitC1     = $paData[FCIudUnitC1],
                        FDDateUpd       = CONVERT(VARCHAR(10),GETDATE(),121),
                        FTTimeUpd       = CONVERT(VARCHAR(8),GETDATE(),24),
                        FTWhoUpd        = '$paData[FTWhoUpd]'
                    WHERE 1=1
                    AND FTIuhDocNo      = '$paData[FTIuhDocNo]'
                    AND FTPdtBarCode    = '$paData[FTPdtBarCode]'
                    AND FTPlcCode       = '$paData[FTPlcCode]'
        ";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Update Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Can not Update',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASCheckConfirmCode($paData){
        $tSQL = "SELECT 
                    FTSplCode 
                 FROM 
                    TCNTPdtChkHD WITH (NOLOCK)
                 WHERE FTIuhDocNo  = '$paData[FTIuhDocNo]'";
        if(isset($paData['FTSplCode']) && $paData['FTSplCode'] != ""){
            $tSQL .= " AND FTSplCode   = '$paData[FTSplCode]'";
        }
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'aItems'        => $aDataList[0]['FTSplCode'],
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'aItems'        => '',
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASUpdStaPrcHDAndDT($paDataWhere,$paDataUpdHDDT){
        // $this->DB_BEGIN_TRANSACTION();
        $tSQL1 = "UPDATE TCNTPdtChkHD WITH(ROWLOCK)
                    SET 
                        FTIuhStaPrcDoc      = '$paDataUpdHDDT[FTIuhStaPrcDoc]',
                        FTIuhApvCode        = '$paDataUpdHDDT[FTIuhApvCode]',
                        FDDateUpd           = CONVERT(VARCHAR,GETDATE(),23),
                        FTTimeUpd           = CONVERT(VARCHAR,GETDATE(),24),
                        FTWhoUpd            = '$paDataUpdHDDT[FTWhoUpd]' 
                    WHERE 
                        FTIuhDocNo          = '$paDataWhere[FTIuhDocNo]' AND 
                        FTBchCode           = '$paDataWhere[FTBchCode]'";
        $tResult1 = $this->DB_EXECUTE($tSQL1);

        $tSQL2 = "UPDATE TCNTPdtChkDT WITH(ROWLOCK)
                    SET 
                        FTIudStaPrc         = '$paDataUpdHDDT[FTIudStaPrc]',
                        FDDateUpd           = CONVERT(VARCHAR,GETDATE(),23),
                        FTTimeUpd           = CONVERT(VARCHAR,GETDATE(),24),
                        FTWhoUpd            = '$paDataUpdHDDT[FTWhoUpd]' 
                    WHERE 
                        FTIuhDocNo          = '$paDataWhere[FTIuhDocNo]' AND 
                        FTBchCode           = '$paDataWhere[FTBchCode]'";
        $tResult2 = $this->DB_EXECUTE($tSQL2);
        if($tResult1 == 'success' && $tResult2 == 'success'){
            // $this->DB_COMMIT();
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASUpdStaPrcHDAndDT] อนุมัติเอกสาร '.$paDataWhere['FTIuhDocNo'],
            );
        }else{
            // $this->DB_ROLLBACK();
            $aDataResult = array(
                'tSQL1'          => $tSQL1,
                'tSQL2'          => $tSQL2,
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASUpdStaPrcHDAndDT] อนุมัติเอกสาร ล้มเหลว',
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSaMPASCreateView($paDataWhere){

        $tSQLAll    = "";
        $aResultAll = array();

        $tSQL1      = " IF EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = 'vChkDT') DROP VIEW vChkDT ";
        $tSQL1     .= " IF EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'vChkDT' AND TABLE_TYPE = 'BASE TABLE') DROP TABLE vChkDT ";
        $tSQLAll   .= $tSQL1;
        $tResult1   = $this->DB_EXECUTE($tSQL1);
        if($tResult1 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateView] (1) ลบ vChkDT');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateView] (1) '.$tResult1[0]['message']);
        }
        

        if(isset($paDataWhere['bAdjType']) && !empty($paDataWhere['bAdjType']) && $paDataWhere['bAdjType'] == 'true'){
            $tWhereAdjType = " AND (A.FTIuhAdjType = '1') ";
        }else{
            $tWhereAdjType = " AND (A.FTIuhAdjType = '2') ";
        }

        // Comsheet 2020-206 ดึง FTPdtName มาจาก master แทนเพราะเนื่องจาก แฮนเฮวทำการ Trim PdtName จะทำให้เกิด Dup
        // Comsheet 2020-241 ทำใบย่อยไปใบรวม การแก้ปัญหา insert นาน (โหลดค้าง) 16/06/2020
        $tSQL2 = "  CREATE VIEW vChkDT AS  
                    SELECT 
                        B.FCIudCost, B.FCIudDisAvg, B.FCIudFootAvg, B.FCIudQtyBal, B.FCIudQtyC1, 
                        B.FCIudQtyC2, B.FCIudQtyDiff, B.FCIudRePackAvg, B.FCIudSetPrice, B.FCIudStkFac, 
                        B.FCIudUnitC1, B.FCIudUnitC2, B.FCIudUnitFact, B.FCIudWahQty, B.FDDateIns, B.FDDateUpd, 
                        B.FDIudChkDate, B.FDIuhDocDate, B.FNIudSeqNo, B.FTBchCode, B.FTClrCode, B.FTClrName, 
                        B.FTDcsCode, B.FTIudBarCode, B.FTIudChkTime, B.FTIudChkUser, B.FTIudStaPrc, B.FTIudStkCode, 
                        B.FTIuhDocNo, B.FTIuhDocType, B.FTPdtArticle, B.FTPdtCode, B.FTPdtName, B.FTPdtNoDis, 
                        B.FTPdtSaleType, B.FTPgpChain, B.FTPlcCode, B.FTPszCode, B.FTPszName, B.FTPunCode, B.FTPunName, 
                        B.FTTimeIns, B.FTTimeUpd, B.FTWahCode, B.FTWhoIns, B.FTWhoUpd
                    FROM 
                        TCNTPdtChkHD A WITH (NOLOCK),
                        TCNTPdtChkDT B WITH (NOLOCK)
                    WHERE ((A.FTWahCode='001') AND (A.FTIuhDocType='1') AND (A.FTIuhStaPrcDoc='2')) 
                        AND ( ISNULL(A.FTCstCode,'') = '' OR A.FTCstCode = 'CFM-HQ' )
                        AND (B.FTIuhDocNo=A.FTIuhDocNo)
                        AND (A.FTIuhStaDoc='1')  
                        AND (B.FTPszName = '$paDataWhere[tPassword]')
                        $tWhereAdjType
        ";

        // echo $tSQL2;
        // exit;
        $tSQLAll .= $tSQL2;
        $tResult2 = $this->DB_EXECUTE($tSQL2);
        
        array_push($aResultAll,$tResult1,$tResult2);
        if($tResult2 == null){
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASCreateView] (2) สร้าง View vChkDT สำเร็จ',
                'aResultAll'     => array(),
            );
        }else{
            // $this->DB_ROLLBACK();
            $aDataResult = array(
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASCreateView] (2) '.$tResult2[0]['message'],
                'tSQLAll'        => $tSQLAll,
                'aResultAll'     => $aResultAll
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSaMPASCreateTmpDTAndInsertINTO(){

        $tSQLAll    = "";
        $aResultAll = array();

        $tSQL4    = " IF EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = 'vLstPdt') DROP VIEW vLstPdt ";
        $tSQL4   .= " IF EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'vLstPdt' AND TABLE_TYPE = 'BASE TABLE') DROP TABLE vLstPdt ";
        $tSQLAll .= $tSQL4;
        $tResult4 = $this->DB_EXECUTE($tSQL4);
        if($tResult4 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (1) ลบ vLstPdt');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (1) '.$tResult4[0]['message']);
        }

        // สร้าง view ก่อน  INSERT INTO Tmp_ChkDT เพื่อให้ได้สินค้า และ barcode 1 record ต่อ 1 สินค้า, ราคา
        // analyze by P'bump
        // Last Update : Napat(Jame) 26/10/2022 RQ-12 ถ้าเป็นสินค้าจาก HQ ไม่ดีดสินค้าออก
        $tSQL5 = "  CREATE VIEW vLstPdt AS
                    SELECT DISTINCT
                        P.FTPdtCode,P.FTPdtName,P.FTPdtStkCode,B.FTPdtBarCode,P.FTPunCode,U.FTPunName,P.FCPdtStkFac,P.FCPdtUnitFact,P.FCPdtQtyRet,P.FCPdtQtyNow
                        ,ISNULL((SELECT TOP 1 FCPdtRetPri1 FROM TCNMPdtBar B2 WITH(NOLOCK) WHERE B2.FTPdtCode =  P.FTPdtCode AND B2.FDPdtPriAffect <= GETDATE() ORDER BY B2.FDPdtPriAffect DESC),0) AS FCPdtRetPri1
                        ,P.FTPgpChain,ISNULL(P.FCPdtCostStd,0) as FCPdtCostStd,P.FTPdtSaleType, P.FTPdtStaReturn, V.FCIudRePackAvg
                    FROM TCNMPdt P WITH(NOLOCK)
                    INNER JOIN TCNMPdtBar B WITH(NOLOCK) ON P.FTPdtCode = B.FTPdtCode
                    INNER JOIN vChkDT     V WITH(NOLOCK) ON P.FTPdtCode = V.FTPdtCode AND P.FTPdtStkCode = V.FTIudStkCode AND B.FTPdtBarCode = V.FTIudBarCode
                    LEFT JOIN TCNMPdtUnit U WITH(NOLOCK) ON P.FTPunCode = U.FTPunCode
                    WHERE ( (V.FCIudRePackAvg = 1 AND P.FTPdtStaAlwSale IN ('1','2')) OR (V.FCIudRePackAvg = 0 AND P.FTPdtStaAlwSale = '1') ) ";
        $tSQLAll .= $tSQL5;
        $tResult5 = $this->DB_EXECUTE($tSQL5);
        if($tResult5 == null){
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASCreateTmpDTAndInsertINTO] (2) สร้าง View vLstPdt สำเร็จ'
            );
        }else{
            $aDataResult = array(
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASCreateTmpDTAndInsertINTO] (2) '.$tResult5[0]['message'],
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);

        $tSQL1 = "  IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'Tmp_ChkDT') and OBJECTPROPERTY(id, N'IsUserTable') = 1) DROP TABLE Tmp_ChkDT ";
        $tSQLAll .= $tSQL1;
        $tResult1 = $this->DB_EXECUTE($tSQL1);
        if($tResult1 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (3) DROP TABLE Tmp_ChkDT');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (3) '.$tResult1[0]['message']);
        }
        
        // array_push($aResultAll,$tResult1);

        $tSQL2 = " CREATE TABLE [dbo].[Tmp_ChkDT](  [FNIudSeqNo] [bigint] NULL,  [FTIudBarCode] [varchar](25) COLLATE Thai_CI_AS NOT NULL,  [FTPdtCode] [varchar](20) COLLATE Thai_CI_AS NOT NULL,  [FTPdtName] [varchar](100) COLLATE Thai_CI_AS NULL,  [FTPunCode] [varchar](5) COLLATE Thai_CI_AS NULL,  [FTPunName] [varchar](50) COLLATE Thai_CI_AS NULL,  [FTIudStkCode] [varchar](20) COLLATE Thai_CI_AS NULL,  [FCIudStkFac] [float] NULL,  [FTPgpChain] [varchar](30) COLLATE Thai_CI_AS NULL,  [FCIudUnitC1] [float] NULL,  [FCIudUnitC2] [float] NULL,  [FCIudUnitFact] [float] NULL,  [FCIudQtyC1] [float] NULL,  [FCIudQtyC2] [float] NULL,  [FCIudWahQty] [float] NULL,  [FCIudQtyDiff] [float] NULL,  [FCIudQtyBal] [bigint] NULL,  [FCIudCost] [float] NULL,  [FCIudSetPrice] [float] NULL,  [FDIudChkDate] [datetime] NULL,  [FTIudChkTime] [varchar](8) COLLATE Thai_CI_AS NULL,  [FTIudChkUser] [varchar](100) COLLATE Thai_CI_AS NULL,  [FTPdtSaleType] [varchar](1) COLLATE Thai_CI_AS NULL,  [FTPdtNoDis] [varchar](1) COLLATE Thai_CI_AS NULL,  [FTPlcCode] [varchar](10) COLLATE Thai_CI_AS NULL, [FCIudDisAvg] [float] NULL, [FCIudRePackAvg] [float] NULL, PRIMARY KEY CLUSTERED (  [FTIudBarCode] ASC,  [FTPdtCode] ASC  )WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF, FILLFACTOR = 70) ON [PRIMARY]  ) ON [PRIMARY]";
        $tSQLAll .= $tSQL2;
        $tResult2 = $this->DB_EXECUTE($tSQL2);
        if($tResult2 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (4) CREATE TABLE Tmp_ChkDT');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpDTAndInsertINTO] (4) '.$tResult2[0]['message']);
        }
        
        // array_push($aResultAll,$tResult2);

        $tSQL3 = "  INSERT INTO Tmp_ChkDT(FTIudStkCode, FTPdtCode, FTIudBarCode, FTPdtName, FTPunName, FCIudUnitC1, FCIudUnitC2 
                            , FCIudQtyC1, FCIudQtyC2, FCIudWahQty, FCIudQtyDiff, FCIudQtyBal, FNIudSeqNo, FTPunCode, FCIudUnitFact 
                            , FCIudSetPrice, FCIudStkFac, FTPgpChain, FCIudCost, FTPdtSaleType,FTPdtNoDis, FTPlcCode, FCIudRePackAvg)
                    SELECT DISTINCT
                        CHK.FTIudStkCode
                        ,P.FTPdtCode
                        ,P.FTPdtBarCode
                        ,P.FTPdtName
                        ,P.FTPunName
                        ,ISNULL(CHK.FCIudUnitC1,0) FCIudUnitC1, ISNULL(CHK.FCIudUnitC2,0) FCIudUnitC2 
                        ,ISNULL(CHK.FCIudQtyC1,0) FCIudQtyC1 ,ISNULL(CHK.FCIudQtyC2,0) FCIudQtyC2
                        ,ISNULL(P.FCPdtQtyRet,0) FCPdtQtyRet , ISNULL(CHK.FCIudQtyDiff,0) FCIudQtyDiff 
                        ,ISNULL(CHK.FCIudQtyBal,0) FCIudQtyBal
                        ,0 As FNIudSeqNo
                        ,P.FTPunCode
                        ,ISNULL(P.FCPdtUnitFact,0) as FCPdtUnitFact
                        ,ISNULL(P.FCPdtRetPri1,0) FCIudSetPrice
                        ,ISNULL(P.FCPdtStkFac,0) FCPdtStkFac
                        ,P.FTPgpChain, ISNULL(P.FCPdtCostStd,0) FCPdtCostStd , P.FTPdtSaleType, P.FTPdtStaReturn, '' AS FTPlcCode
                        ,P.FCIudRePackAvg
                    FROM(
                        SELECT
                            FTIudStkCode,
                            FTPdtCode,
                            MIN(FCIudStkFac) AS FCIudStkFac,
                            SUM(FCIudUnitC1) AS FCIudUnitC1,
                            SUM(FCIudUnitC2) AS FCIudUnitC2,
                            SUM(FCIudQtyC1) AS FCIudQtyC1,
                            SUM(FCIudQtyC2) AS FCIudQtyC2,
                            SUM(FCIudQtyDiff) AS FCIudQtyDiff,
                            SUM(FCIudQtyBal) AS FCIudQtyBal
                        FROM vChkDT WITH(NOLOCK)
                        GROUP BY FTPdtCode,FTIudStkCode
                    ) CHK
                    INNER JOIN vLstPdt P ON P.FTPdtStkCode = CHK.FTIudStkCode AND P.FTPdtCode = CHK.FTPdtCode
                 ";
        
        $tResult3 = $this->DB_EXECUTE($tSQL3);
        $tSQLAll .= $tSQL3;

        array_push($aResultAll,$tResult1,$tResult2,$tResult3);
        if($tResult3 == 'success'){
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASCreateTmpDTAndInsertINTO] (5) นำสินค้าจาก vChkDT,vLstPdt ลงตาราง Tmp_ChkDT',
                'aResultAll'     => array()
            );
        }else{
            $aDataResult = array(
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASCreateTmpDTAndInsertINTO] (5) '.$tResult3[0]['message'],
                'tSQLAll'        => $tSQLAll,
                'aResultAll'     => $aResultAll
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSaMPASCreateTmpSleB4AuditAndInsertINTO(){

        $tSQLAll    = "";
        $aResultAll = array();
        $tSQL1 = "  IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'Tmp_SleB4Audit') and OBJECTPROPERTY(id, N'IsUserTable') = 1) DROP TABLE Tmp_SleB4Audit ";
        $tSQLAll .= $tSQL1;
        $tResult1 = $this->DB_EXECUTE($tSQL1);
        if($tResult1 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (1) DROP TABLE Tmp_SleB4Audit');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (1) '.$tResult1[0]['message']);
        }

        $tSQL2 = "CREATE TABLE [dbo].Tmp_SleB4Audit(  [FTIudStkCode] [varchar](20) COLLATE Thai_CI_AS NULL,  [FCIudUnitC1] [float] NULL,  [FCIudUnitC2] [float] NULL,  [FDIudChkDate] [datetime] NULL,  [FTIudChkTime] [varchar](8) COLLATE Thai_CI_AS NULL,  [FTIudChkUser] [varchar](100) COLLATE Thai_CI_AS NULL,  [FTPdtSaleType] [varchar](1) COLLATE Thai_CI_AS NULL,   ) ON [PRIMARY]";
        $tSQLAll .= $tSQL2;
        $tResult2 = $this->DB_EXECUTE($tSQL2);
        if($tResult2 == null){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (2) CREATE TABLE Tmp_SleB4Audit');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (2) '.$tResult2[0]['message']);
        }
        

        // เอาสินค้าจาก vChkDT มา insert ใน Tmp_SleB4Audit
        $tSQL3 = " INSERT INTO Tmp_SleB4Audit(FTIudStkCode,FDIudChkDate,FTIudChkTime)
                    SELECT * FROM (
                        SELECT 
                            A.FTIudStkCode,
                            A.FDIudChkDate,
                            MAX(FTIudChkTime) AS FTIudChkTime 
                        FROM vChkDT B WITH (NOLOCK) , (SELECT FTIudStkCode,MAX(FDIudChkDate) AS FDIudChkDate FROM vChkDT WITH (NOLOCK) GROUP BY FTIudStkCode) A 
                        Where A.FTIudStkCode = B.FTIudStkCode 
                          AND A.FDIudChkDate = B.FDIudChkDate 
                        GROUP BY A.FTIudStkCode,A.FDIudChkDate
                    ) C
                    ORDER BY C.FTIudStkCode
        ";
        $tSQLAll .= $tSQL3;
        $tResult3 = $this->DB_EXECUTE($tSQL3);
        if($tResult3 == 'success'){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (3) นำสินค้าจาก vChkDT ลงตาราง Tmp_SleB4Audit');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (3) '.$tResult3[0]['message']);
        }
        // array_push($aResultAll,$tResult3);

        //UPDATE วันเวลาที่หลังร้าน
        // $tSQL4 = "  UPDATE
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate    = (SELECT MAX(FDIudChkDate) AS FDIudChkDate FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode AND (LEFT(FTPlcCode, 1) NOT IN (NULL,'','1'))), --CONVERT(VARCHAR, GETDATE(), 101),
        //                 FTIudChkTime    = (SELECT MAX(FTIudChkTime) AS FTIudChkTime FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode AND (LEFT(FTPlcCode, 1) NOT IN (NULL,'','1'))) --CONVERT(VARCHAR, GETDATE(), 108)
        //             WHERE 
        //                 FTIudStkCode NOT IN (SELECT DISTINCT FTIudStkCode FROM vChkDT WITH (NOLOCK) WHERE (LEFT(FTPlcCode, 1) IN (NULL,'','1')))
        // ";
        // $tResult4 = $this->DB_EXECUTE($tSQL4);
        // $tSQLAll .= $tSQL4;
        // array_push($aResultAll,$tResult4);

        // Last Update : 27/10/2020 Napat(Jame) CFM/2020 - 516 เปลี่ยนจากการหาวันที่-เวลาด้วย MAX() เป็น ORDER BY
        // UPDATE วันเวลาที่หน้าร้าน
        $tSQL5 = "  UPDATE Tmp_SleB4Audit WITH(ROWLOCK)
                    SET 
                        Tmp_SleB4Audit.FDIudChkDate = F.FDIudChkDate,
                        Tmp_SleB4Audit.FTIudChkTime = F.FTIudChkTime
                    FROM ( 
                        SELECT TOP 1
                            V.FTIudStkCode,
                            V.FDIudChkDate,
	                        V.FTIudChkTime
                            -- MAX(V.FDIudChkDate) AS FDIudChkDate,
                            -- MAX(V.FTIudChkTime) AS FTIudChkTime
                        FROM vChkDT V WITH(NOLOCK)
                        ORDER BY V.FDIudChkDate DESC, V.FTIudChkTime DESC
                        -- GROUP BY V.FTIudStkCode
                    ) F
                    WHERE F.FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode 
        ";
        // $tSQL5 = "  UPDATE Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate = VI.FDIudChkDate,
        //                 FTIudChkTime = VI.FTIudChkTime
        //             FROM vChkDT V WITH(NOLOCK)
        //             INNER JOIN (
        //                 SELECT
        //                     FTIudStkCode,
        //                     MAX(FDIudChkDate) AS FDIudChkDate,
        //                     MAX(FTIudChkTime) AS FTIudChkTime
        //                 FROM vChkDT WITH(NOLOCK)
        //                 GROUP BY FTIudStkCode
        //             ) VI ON VI.FTIudStkCode = V.FTIudStkCode
        //             WHERE Tmp_SleB4Audit.FTIudStkCode = V.FTIudStkCode
        // ";
        // $tSQL5 = "  UPDATE 
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate = (SELECT MAX(FDIudChkDate) AS FDIudChkDate FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode), --AND (LEFT(FTPlcCode, 1) IN (NULL,'','1'))
        //                 FTIudChkTime = (SELECT MAX(FTIudChkTime) AS FTIudChkTime FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode)  --AND (LEFT(FTPlcCode, 1) IN (NULL,'','1'))
        //             WHERE 
        //                 FTIudStkCode IN (SELECT DISTINCT FTIudStkCode FROM vChkDT WITH (NOLOCK)) --WHERE (LEFT(FTPlcCode, 1) IN (NULL,'','1'))
        // ";
        $tSQLAll .= $tSQL5;
        $tResult5 = $this->DB_EXECUTE($tSQL5);
        if($tResult5 == 'success'){
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (4) อัพเดท วันเวลาที่หน้าร้าน');
        }else{
            $this->FSxMPASWriteLog('[FSaMPASCreateTmpSleB4AuditAndInsertINTO] (4) '.$tResult5[0]['message']);
        }
        
        // array_push($aResultAll,$tResult5);

        // อัพเดทให้ข้อมูลใน Tmp_SleB4Audit เป็นวันที่-เวลา ล่าสุด
        // $tSQL4 = "  UPDATE 
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate    = CONVERT(VARCHAR, GETDATE(),101),
        //                 FTIudChkTime    = CONVERT(VARCHAR, GETDATE(),108)
        //             WHERE 
        //                 FTIudStkCode NOT IN (SELECT DISTINCT FTIudStkCode  FROM vChkDT WITH (NOLOCK) WHERE (LEFT(FTPlcCode, 1) IN (NULL,'','1')))";
        // $tResult4 = $this->DB_EXECUTE($tSQL4);

        // $tSQL5 = "  UPDATE 
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FDIudChkDate = (SELECT MAX(FDIudChkDate) AS FDIudChkDate FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode AND (LEFT(FTPlcCode, 1) IN (Null,'','1'))),
        //                 FTIudChkTime = (SELECT MAX(FTIudChkTime) AS FTIudChkTime FROM vChkDT WITH (NOLOCK) WHERE FTIudStkCode = Tmp_SleB4Audit.FTIudStkCode AND (LEFT(FTPlcCode, 1) IN (Null,'','1'))) 
        //             WHERE 
        //                 FTIudStkCode IN (SELECT DISTINCT FTIudStkCode FROM vChkDT WITH (NOLOCK) WHERE (LEFT(FTPlcCode, 1) IN (Null,'','1')))";
        // $tResult5 = $this->DB_EXECUTE($tSQL5);

        // if($tTmpTime = ""){
        //     $tDateTimeFormat = date("Y-m-d").' '.date("H:i:s");
        // }else{
        //     $tDateTimeFormat = $tDateTimeFormat;
        // }

        // อัพเดท UnitC2
        // $tSQL6 = "  UPDATE 
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FCIudUnitC2 = ISNULL(FCIudUnitC2,0) + ISNULL((SELECT 
        //                                                                 SUM(CASE WHEN DT.FTShdDocType = '1' THEN DT.FCSdtQtyAll ELSE -DT.FCSdtQtyAll END) AS FCSdtQtyAllSum 
        //                                                               FROM TPSTSalHD HD WITH (NOLOCK)
        //                                                               LEFT JOIN TPSTSalDT DT ON HD.FTShdDocNo = DT.FTShdDocNo  
        //                                                               WHERE HD.FTWahCode ='001' 
        //                                                               AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime) BETWEEN '$tDateTimeFormat' AND  Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime)
        //                                                               AND HD.FTShdDocType IN ('1','9')  
        //                                                               AND HD.FTShdStaDoc = '1'  
        //                                                               AND DT.FTSdtStaPdt <> '4'  
        //                                                               AND DT.FTSdtStkCode = Tmp_SleB4Audit.FTIudStkCode),0)";
        // $tResult6 = $this->DB_EXECUTE($tSQL6);

        // $tSQL7 = "  UPDATE 
        //                 Tmp_SleB4Audit WITH(ROWLOCK)
        //             SET 
        //                 FCIudUnitC1 = ISNULL(FCIudUnitC1,0) +  ISNULL((SELECT  
        //                                                                 SUM(CASE WHEN DT.FTShdDocType = '1' THEN DT.FCSdtQtyAll ELSE -DT.FCSdtQtyAll END) AS FCSdtQtyAllSum 
        //                                                                FROM TPSTSalHD HD WITH (NOLOCK) 
        //                                                                LEFT JOIN TPSTSalDT DT  ON HD.FTShdDocNo = DT.FTShdDocNo 
        //                                                                WHERE HD.FTWahCode ='001'  
        //                                                                AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime)  BETWEEN '$tDateTimeFormat' AND  Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime) 
        //                                                                AND HD.FTShdDocType IN ('1','9')  
        //                                                                AND HD.FTShdStaDoc = '1'  
        //                                                                AND DT.FTSdtStaPdt <> '4'  
        //                                                                AND ISNULL(DT.FTSdtStaPrcStk,'') = '1' 
        //                                                                AND DT.FTSdtStkCode = Tmp_SleB4Audit.FTIudStkCode),0) ";
        // $tResult7 = $this->DB_EXECUTE($tSQL7);
        
        array_push($aResultAll,$tResult1,$tResult2,$tResult3,$tResult5);

        //&& ($tResult4 == 'success' || $tResult4 == null)
        if( ($tResult3 == 'success' || $tResult3 == null) && 
            ($tResult5 == 'success' || $tResult5 == null) )
        {
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => 'Create Table Tmp_SleB4Audit Success',
                'aResultAll'     => array(),
            );

        }
        else
        {
            $aDataResult = array(
                'nStaQuery'      => 99,
                'tStaMessage'    => 'Error can not Create Table Tmp_SleB4Audit',
                'tSQLAll'        => $tSQLAll,
                'aResultAll'     => $aResultAll,
            );
        }
        return $aDataResult;
    }

    // กรองเอา table(HD) ของการขายที่เกี่ยวข้อง
    public function FSaMPASGetTableSaleHD(){
        $tSQL = "   SELECT 
                        DISTINCT TABLE_NAME 
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_NAME LIKE 'TSHD%' 
                        OR TABLE_NAME = 'TPSTSalHD'
                ";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'aDataList'     => $aDataList,
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    // หาเวลาของการเปิดรอบการขายอันแรกของวัน
    public function FSaMPASGetOpeningFirstSalesOfDay(){
        $tSQL = "SELECT TOP 1 
                    CONVERT(VARCHAR(10),(FDLogDSignIn + ' ' + FTLogTSignIn),121) AS FDIuhDocDatetime 
                 FROM TPSTLogIn WITH(NOLOCK)
                 WHERE FDLogDSignIn = CONVERT(VARCHAR(10),GETDATE(),121)
                ";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'aDataList'     => $aDataList[0],
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;  
    }

    // อัพเดทยอดขายก่อนนับ
    public function FSaMPASUpdateSalesB4Counting($ptTable,$ptTime){
        if($ptTable == "TPSTSalHD"){
            $tTblHD = "TPSTSalHD";
            $tTblDT = "TPSTSalDT";
        }else{
            $ptTable = substr($ptTable,4);
            $tSQL = "SELECT FTPosCode FROM TPSMPos WITH (NOLOCK) WHERE FTPosCode = '$ptTime'";
            $aDataList = $this->DB_SELECT($tSQL);
            if(count($aDataList) > 0){
                $tTblHD = "TSHD".$ptTable;
                $tTblDT = "TSDT".$ptTable;
            }else{
                $tTblHD = "";
                $tTblDT = "";
            }
        }

        if($tTblHD != ""){
            // อัพเดท UnitC2
            $tSQL1 = "  UPDATE Tmp_SleB4Audit WITH(ROWLOCK)
                        SET 
                            FCIudUnitC2 = ISNULL(FCIudUnitC2,0) + ISNULL((SELECT  
                                                                            SUM(CASE WHEN DT.FTShdDocType = '1' THEN DT.FCSdtQtyAll ELSE -DT.FCSdtQtyAll END) AS FCSdtQtyAllSum 
                                                                        FROM $tTblHD HD 
                                                                        LEFT JOIN $tTblDT DT ON HD.FTShdDocNo = DT.FTShdDocNo
                                                                        WHERE HD.FTWahCode ='001' ";
            if($ptTime == ""){
                $tSQL1 .= " AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime)  BETWEEN CONVERT(VARCHAR(10),GETDATE(),121) AND  Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime)";
            }else{
                $tSQL1 .= " AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime)  BETWEEN '$ptTime' AND Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime)";
            }
            $tSQL1 .= "     AND HD.FTShdDocType IN ('1','9')
                            AND HD.FTShdStaDoc = '1'
                            AND DT.FTSdtStaPdt <> '4'
                            AND DT.FTSdtStkCode = Tmp_SleB4Audit.FTIudStkCode),0)";
            $tResult1 = $this->DB_EXECUTE($tSQL1);

            // อัพเดท UnitC1
            $tSQL2 = "UPDATE Tmp_SleB4Audit WITH(ROWLOCK)
                    SET FCIudUnitC1 = ISNULL(FCIudUnitC1,0) + ISNULL((SELECT 
                                                                            SUM(CASE WHEN DT.FTShdDocType = '1' THEN DT.FCSdtQtyAll ELSE -DT.FCSdtQtyAll END) AS FCSdtQtyAllSum
                                                                        FROM $tTblHD HD 
                                                                        LEFT JOIN $tTblDT DT ON HD.FTShdDocNo = DT.FTShdDocNo
                                                                        WHERE HD.FTWahCode ='001'";
            if($ptTime == ""){
                $tSQL2 .= " AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime)  BETWEEN CONVERT(VARCHAR(10),GETDATE(),121) AND  Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime)";
            }else{
                $tSQL2 .= " AND ( CONVERT(DATETIME ,HD.FDShdDocDate + ' ' + HD.FTShdDocTime)  BETWEEN '$ptTime' AND Tmp_SleB4Audit.FDIudChkDate+' '+Tmp_SleB4Audit.FTIudChkTime)";
            }
            $tSQL2 .= " AND HD.FTShdDocType IN ('1','9')
                        AND HD.FTShdStaDoc = '1'
                        AND DT.FTSdtStaPdt <> '4'
                        AND ISNULL(DT.FTSdtStaPrcStk,'') = '1'
                        AND DT.FTSdtStkCode = Tmp_SleB4Audit.FTIudStkCode),0)";
            $tResult2 = $this->DB_EXECUTE($tSQL2);

            

            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASUpdateSalesB4Counting] อัพเดทยอดขายก่อนนับ จากเอกสารการขาย '.$tTblHD
            );
        }else{
            $aDataResult = array(
                'nStaQuery'      => 88,
                'tStaMessage'    => '[FSaMPASUpdateSalesB4Counting] ไม่พบเอกสารการขาย'
            );
        }
        return $aDataResult;
    }

    //อัพเดท ยอดขายก่อนนับเข้าตาราง Tmp_ChkDT
    public function FSaMPASMoveSleB4ToTmpChkDT(){
        $tSQL = "UPDATE 
                    Tmp_ChkDT WITH(ROWLOCK)
                  SET 
                    FCIudUnitC2     = ISNULL((SELECT FCIudUnitC2 FROM Tmp_SleB4Audit WHERE FTIudStkCode = Tmp_ChkDT.FTIudStkCode),FCIudUnitC2), 
                    FNIudSeqNo      = ISNULL((SELECT FCIudUnitC1 FROM Tmp_SleB4Audit WHERE FTIudStkCode = Tmp_ChkDT.FTIudStkCode),FNIudSeqNo),
                    FDIudChkDate    = ISNULL((SELECT FDIudChkDate FROM Tmp_SleB4Audit WHERE FTIudStkCode = Tmp_ChkDT.FTIudStkCode),FDIudChkDate),
                    FTIudChkTime    = ISNULL((SELECT FTIudChkTime FROM Tmp_SleB4Audit WHERE FTIudStkCode = Tmp_ChkDT.FTIudStkCode),FTIudChkTime)";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'           => $tSQL,
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASMoveSleB4ToTmpChkDT] อัพเดทยอดขายก่อนนับ จาก Tmp_SleB4Audit ลงตาราง Tmp_ChkDT'
            );
        }else{
            $aDataResult = array(
                'tSQL'           => $tSQL,
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASMoveSleB4ToTmpChkDT] '.$tResult[0]['message']
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    // อัพเดท QtyDiff และ QtyBal
    // Last Update: Napat(Jame) 15/10/2021 แก้ไขการคำนวณ FCIudQtyBal,FCIudQtyDiff
    public function FSaMPASUpdQtyDiffandQtyBal(){
        /*FCIudUnitC1     = FCIudQtyC1,*/
        /*FCIudQtyBal = FCIudUnitC1 + FCIudUnitC2 คิวรี่ของ V.4 */ 

        /*  คิวรี่ล่าสุดก่อนแก้ 14/10/2021
            FCIudQtyBal     = FCIudUnitC2 + FCIudQtyC1 
            FCIudQtyDiff    = (FCIudUnitC2 + FCIudQtyC1) - (FCIudWahQty + FNIudSeqNo),
        */
        
        $tSQL = "UPDATE 
                    Tmp_ChkDT WITH(ROWLOCK)
                 SET 
                    FCIudQtyBal     = FCIudUnitC1, 
                    FCIudQtyDiff    = FCIudUnitC1 - (FCIudWahQty + FNIudSeqNo),
                    FCIudWahQty     = (FCIudWahQty + FNIudSeqNo),
                    FCIudDisAvg     = FCIudQtyC1
                ";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'           => $tSQL,
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSaMPASUpdQtyDiffandQtyBal] อัพเดท FCIudQtyDiff, FCIudQtyBal, FCIudWahQty ตาราง Tmp_ChkDT',
            );
        }else{
            $aDataResult = array(
                'tSQL'           => $tSQL,
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSaMPASUpdQtyDiffandQtyBal] '.$tResult[0]['message']
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    public function FSxMPASAddTmpChkDTToDT($paPackData){

        $this->FSxMPASWriteLog('[FSxMPASAddTmpChkDTToDT] =============== START ===============');

        $tSQLAll    = "";
        $aResultAll = array();

        $tSQL  = "  INSERT INTO TCNTPdtChkDT (FTBchCode,FTIuhDocNo,FTIuhDocType,FNIudSeqNo,FTIudBarCode,FTPdtCode,
                    FTPdtName,FTPunCode,FTPunName,FTWahCode,FTIudStkCode,FCIudStkFac,FTPgpChain,FCIudUnitC1,FCIudUnitC2,
                    FCIudUnitFact,FCIudQtyC1,FCIudQtyC2,FCIudWahQty,FCIudQtyDiff,FCIudQtyBal,FCIudCost,FCIudSetPrice,
                    FDIudChkDate,FTIudChkTime,FTIudChkUser,FTPdtSaleType,FTPdtNoDis,FTPlcCode,FCIudDisAvg,FCIudRePackAvg) ";
        $tSQL .= "SELECT
                    '$paPackData[FTBchCode]'                    AS FTBchCode,
                    ''                                          AS FTIuhDocNo,
                    '2'                                         AS FTIuhDocType,
                    ROW_NUMBER() OVER (ORDER BY FTPdtCode ASC)  AS FNIudSeqNo,
                    FTIudBarCode,
                    FTPdtCode,
                    FTPdtName,
                    FTPunCode,
                    FTPunName,
                    '001'                                       AS FTWahCode,
                    FTIudStkCode,
                    FCIudStkFac,
                    FTPgpChain,
                    FCIudUnitC1,
                    FCIudUnitC2,
                    FCIudUnitFact,
                    FCIudQtyC1,
                    FCIudQtyC2,
                    FCIudWahQty,
        ";

        //Comsheet 2020-015
        //ตรวจสอบว่าให้ปรับยอดสต๊อก หรือไม่ ?
        if(isset($paPackData['bAdjType']) && !empty($paPackData['bAdjType']) && $paPackData['bAdjType'] == 'true'){
            $tSQL .= " FCIudQtyDiff, 
                       FCIudQtyBal, ";
        }else{
            $tSQL .= " 0                                        AS FCIudQtyDiff, 
                       FCIudWahQty                              AS FCIudQtyBal, ";
        }

        $tSQL .= "
                    FCIudCost,
                    FCIudSetPrice,
                    FDIudChkDate,
                    FTIudChkTime,
                    FTIudChkUser,
                    FTPdtSaleType,
                    FTPdtNoDis,
                    FTPlcCode,
                    FCIudDisAvg,
                    FCIudRePackAvg
                  FROM Tmp_ChkDT WITH (NOLOCK) ";

        $tResult = $this->DB_EXECUTE($tSQL);
        $tSQLAll .= $tSQL;
        array_push($aResultAll,$tResult);
        if($tResult == 'success'){
            $aDataResult = array(
                'nStaQuery'      => 1,
                'tStaMessage'    => '[FSxMPASAddTmpChkDTToDT] INSERT INTO TCNTPdtChkDT FROM Tmp_ChkDT SUCCESS',
                'aResultAll'     => array(),
            );
        }else{
            $aDataResult = array(
                'nStaQuery'      => 99,
                'tStaMessage'    => '[FSxMPASAddTmpChkDTToDT] '.$tResult[0]['message'],
                'tSQLAll'        => $tSQLAll,
                'aResultAll'     => $aResultAll,
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);

        $this->FSxMPASWriteLog('[FSxMPASAddTmpChkDTToDT] ================ END ================');
        return $aDataResult;
    }

    public function FSxPASBegin_Transaction(){
        $this->DB_BEGIN_TRANSACTION();
    }

    public function FSxPASCommit(){
        $this->DB_COMMIT();
    }

    public function FSxPASRollBack(){
        $this->DB_ROLLBACK();
    }

    public function FSxMPASDelAllPdtChkDTForDocType2(){
        $tDatabase          = "TCNTPdtChkDT";
        $aDataDeleteWHERE   = array(
            'FTIuhDocType'    => '2',
            'FTIuhDocNo'      => ''
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Delete Success',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Error Delete',
            );
        }
        return $aDataResult;
    }

    public function FSxMPASClearTempDT(){
        $tDatabase          = "TCNTPdtChkDT";
        $aDataDeleteWHERE   = array(
            'FTIuhDocNo'      => ''
        );
        $bConfirm           = true;
        $aDataList          = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
        if($aDataList == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Delete Success',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'Error Delete',
            );
        }
        return $aDataResult;
    }

    // อัพเดท DocType = 4
    public function FSaMPASUpdDocRefOfSubDoc($paData){

        // Napat(Jame) 20/10/2022 อัพเดทว่าเป็น CFM or TOP ในเอกสารใบรวม
        $tSQL1 = "  SELECT TOP 1 FTIuhRefTaxLoss 
                    FROM TCNTPdtChkHD WITH(NOLOCK)
                    WHERE FTIuhDocType='1' AND FTWahCode='001'
                    AND (FTIuhStaPrcDoc='2' OR FTIuhStaPrcDoc='3')
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    AND FTSplCode = '".$paData['FTSplCode']."'
                    ORDER BY FTIuhRefTaxLoss DESC ";
        $aResult1 = $this->DB_SELECT($tSQL1);
        if( isset($aResult1[0]['FTIuhRefTaxLoss']) && !empty($aResult1[0]['FTIuhRefTaxLoss']) ){
            $tSQL2 = "  UPDATE TCNTPdtChkHD WITH(ROWLOCK) 
                        SET FTIuhRefTaxLoss = '".$aResult1[0]['FTIuhRefTaxLoss']."' 
                        WHERE FTIuhDocNo = '".$paData['FTIuhDocNo']."' ";
            $this->DB_EXECUTE($tSQL2);
        }

        $tSQL = "UPDATE
                    TCNTPdtChkHD WITH(ROWLOCK)
                SET 
                    FTIuhDocRef             = '$paData[FTIuhDocNo]',
                    FTIuhStaPrcDoc          = '4'
                WHERE 
                    (FTIuhDocType='1')
                    AND (FTWahCode='001')
                    AND (FTIuhStaPrcDoc='2' OR FTIuhStaPrcDoc='3')
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    AND FTSplCode = '$paData[FTSplCode]' ";
        $this->DB_EXECUTE($tSQL);

        // if($tResult=='success'){
        //     $aDataResult = array(
        //         'tSQL'           => $tSQL,
        //         'nStaQuery'      => 1,
        //         'tStaMessage'    => 'Update Success',
        //     );
        // }else{
        //     $aDataResult = array(
        //         'tSQL'           => $tSQL,
        //         'nStaQuery'      => 99,
        //         'tStaMessage'    => 'Error Update',
        //     );
        // }
        // return $aDataResult;
    }

    //นำ Array ของใบย่อยเป็น Key ไปทำการ Update ลงข้อมูล TCNTPdtStkNotExist ที่ Field ใหม่(FTIuhDocNoType2) ด้วยเลขใบรวม
    public function FSxMPASDocStkNotExist($paData){
        $tSQL0 = "SELECT 
                    FTIuhDocNo 
                  FROM 
                    TCNTPdtChkHD WITH(NOLOCK)
                  WHERE FTWahCode = '001'
                    AND FTIuhDocType = '1'
                    AND FTIuhStaPrcDoc = '4'
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    AND FTSplCode = '$paData[FTSplCode]'
        ";
        $aDataList = $this->DB_SELECT($tSQL0);        
        if(count($aDataList) > 0){
            foreach($aDataList as $nKey => $tValue){
                $tSQL = "UPDATE 
                            TCNTPdtStkNotExist WITH(ROWLOCK)
                         SET 
                            FTIuhDocNoType2     = '$paData[FTIuhDocNo]',
                            FDDateUpd           = CONVERT(VARCHAR(10),GETDATE(),121),
                            FTTimeUpd           = CONVERT(VARCHAR(8),GETDATE(),24),
                            FTWhoUpd            = '$paData[FTWhoUpd]'
                         WHERE 
                            FTIuhDocNo = '$tValue[FTIuhDocNo]'
                ";
                $this->DB_EXECUTE($tSQL);
            }
        }
    }

    public function FSxMPASUpdStaExport($ptDocNo){
        $tSQL = "UPDATE
                    TCNTPdtChkHD WITH(ROWLOCK)
                SET 
                    FNLogStaExport = 0 
                WHERE 
                    FTIuhDocRef = '$ptDocNo'";
        $tResult = $this->DB_EXECUTE($tSQL);
        // if($tResult=='success'){
        //     $aDataResult = array(
        //         'nStaQuery'      => 1,
        //         'tStaMessage'    => 'Update Success',
        //     );
        // }else{
        //     $aDataResult = array(
        //         'nStaQuery'      => 99,
        //         'tStaMessage'    => 'Error Update',
        //     );
        // }
        // return $aDataResult;
    }

    public function FSaMPASGetPageDataSearchHQ($paDataWhere){
        $tSQL = "IF(NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TCNTStkCountItemList')) BEGIN SELECT 'Not Found Table' AS NFT END";
        $aResult = $this->DB_SELECT($tSQL);
        if(!isset($aResult[0]['NFT'])){
            $tSQL = "SELECT 
                        COUNT(TCNTStkCountItemList.FTPdtCylCntNo) AS counts
                    FROM 
                        TCNTStkCountItemList WITH(NOLOCK)
                    LEFT JOIN TCNTPdtChkHD ON TCNTStkCountItemList.FTPdtCylCntNo = TCNTPdtChkHD.FTIuhRefTaxOver AND TCNTPdtChkHD.FTIuhStaDoc <> '3'
                    WHERE TCNTStkCountItemList.FTBchCode = '$paDataWhere[FTBchCode]'
                      AND CONVERT(VARCHAR,TCNTStkCountItemList.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23)
                      AND TCNTPdtChkHD.FTIuhDocNo IS NULL
                    GROUP BY TCNTStkCountItemList.FTPdtCylCntNo,TCNTStkCountItemList.FDPdtStkChkDate
            ";
            $oQuery = $this->DB_SELECT($tSQL);
            if(!empty($oQuery)) {
                return count($oQuery);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function FSaMPASGetDataSearchHQ($paDataWhere){
        $tSQL = "IF(NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TCNTStkCountItemList')) BEGIN SELECT 'Not Found Table' AS NFT END";
        $aResult = $this->DB_SELECT($tSQL);
        if(!isset($aResult[0]['NFT'])){
            $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
            $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTPdtCylCntNo ASC) AS rtRowID , * FROM
                    (SELECT
                        TCNTStkCountItemList.FTPdtCylCntNo,
                        TCNTStkCountItemList.FDPdtStkChkDate
                    FROM TCNTStkCountItemList WITH(NOLOCK)
                    LEFT JOIN TCNTPdtChkHD ON TCNTStkCountItemList.FTPdtCylCntNo = TCNTPdtChkHD.FTIuhRefTaxOver AND TCNTPdtChkHD.FTIuhStaDoc <> '3'
                    WHERE TCNTStkCountItemList.FTBchCode = '$paDataWhere[FTBchCode]' 
                     AND CONVERT(VARCHAR,TCNTStkCountItemList.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23) 
                     AND TCNTPdtChkHD.FTIuhDocNo IS NULL 
            ";
            
            if($paDataWhere['tSearch'] != "NULL"){
                $tSQL .= " AND TCNTStkCountItemList.FTPdtCylCntNo     LIKE '%$paDataWhere[tSearch]%'";
            }

            $tSQL .= "GROUP BY TCNTStkCountItemList.FTPdtCylCntNo,TCNTStkCountItemList.FDPdtStkChkDate";
            $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $aDataList = $this->DB_SELECT($tSQL);        
            if(count($aDataList) > 0){
                $nFoundRow      = $this->FSaMPASGetPageDataSearchHQ($paDataWhere);
                $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'tType'         => '1',
                    'aItems'        => $aDataList,
                    'nPage'         => $paDataWhere['nPage'],
                    'nAllRow'       => $nFoundRow,
                    'nAllPage'      => $nPageAll,
                    'nCurrentPage'  => $paDataWhere['nPage'],
                    // 'tSQL'          => $tSQL,
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'found data',
                );
            }else{
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'tType'         => '1',
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'not found data',
                );
            }
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '1',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found table',
            );
        }
        return $aDataResult; 
    }

    public function FSaMPASGetPageDataSearchHQList($paDataWhere){
        $tSQL = "IF(NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TCNTStkCountItemList')) BEGIN SELECT 'Not Found Table' AS NFT END";
        $aResult = $this->DB_SELECT($tSQL);
        if(!isset($aResult[0]['NFT'])){
            $tSQL = "SELECT 
                        COUNT(TCNTStkCountItemList.FTPdtCylCntNo) AS counts
                    FROM 
                        TCNTStkCountItemList WITH(NOLOCK)
                    LEFT JOIN TCNTPdtChkHD ON TCNTStkCountItemList.FTPdtCylCntNo = TCNTPdtChkHD.FTIuhRefTaxOver AND TCNTPdtChkHD.FTIuhStaDoc <> '3'
                    WHERE TCNTStkCountItemList.FTBchCode = '$paDataWhere[FTBchCode]'
                    AND CONVERT(VARCHAR,TCNTStkCountItemList.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23)
                    AND TCNTStkCountItemList.FTPdtCylCntNo = '$paDataWhere[FTPdtCylCntNo]'
                    AND TCNTPdtChkHD.FTIuhDocNo IS NULL
            ";
            $oQuery = $this->DB_SELECT($tSQL);
            if(!empty($oQuery)) {
                return $oQuery[0];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function FSaMPASGetDataSearchHQList($paDataWhere){
        $tSQL = "IF(NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TCNTStkCountItemList')) BEGIN SELECT 'Not Found Table' AS NFT END";
        $aResult = $this->DB_SELECT($tSQL);
        if(!isset($aResult[0]['NFT'])){
            $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
            $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTPdtCode ASC) AS rtRowID , * FROM
                    (SELECT
                        TCNTStkCountItemList.FTPdtCode,
                        TCNTStkCountItemList.FTPdtBarCode,
                        TCNMPdt.FTPdtName
                    FROM 
                        TCNTStkCountItemList WITH(NOLOCK)
                    LEFT JOIN TCNMPdt ON TCNTStkCountItemList.FTPdtCode = TCNMPdt.FTPdtCode
                    LEFT JOIN TCNTPdtChkHD ON TCNTStkCountItemList.FTPdtCylCntNo = TCNTPdtChkHD.FTIuhRefTaxOver AND TCNTPdtChkHD.FTIuhStaDoc <> '3'
                    WHERE TCNTStkCountItemList.FTBchCode = '$paDataWhere[FTBchCode]'
                      AND CONVERT(VARCHAR,TCNTStkCountItemList.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23)
                      AND TCNTStkCountItemList.FTPdtCylCntNo = '$paDataWhere[FTPdtCylCntNo]'
                      AND TCNTPdtChkHD.FTIuhDocNo IS NULL
            ";     
            $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";   
            $aDataList = $this->DB_SELECT($tSQL);        
            if(count($aDataList) > 0){
                $aFoundRow      = $this->FSaMPASGetPageDataSearchHQList($paDataWhere);
                $nFoundRow      = $aFoundRow['counts'];
                $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'tType'         => '2',
                    'aItems'        => $aDataList,
                    'nPage'         => $paDataWhere['nPage'],
                    'nAllRow'       => $nFoundRow,
                    'nAllPage'      => $nPageAll,
                    'nCurrentPage'  => $paDataWhere['nPage'],
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'found data',
                );
            }else{
                $aDataResult = array(
                    'tSQL'          => $tSQL,
                    'tType'         => '2',
                    'nStaQuery'     => 99,
                    'tStaMessage'   => 'not found data',
                );
            }
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '2',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found table',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASAddDataSearchHQ2DT($aData){

        //ค้นหา Seq ล่าสุด
        $tSQL_Seq   = "SELECT TOP 1 FNIudSeqNo FROM TCNTPdtChkDT WITH(NOLOCK) WHERE FTIuhDocNo='' AND FTIuhDocType='1' AND FTBchCode='$aData[FTBchCode]' ORDER BY FNIudSeqNo DESC";
        $aDataList  = $this->DB_SELECT($tSQL_Seq);
        
        if(count($aDataList) > 0){
            $nSeq = $aDataList[0]['FNIudSeqNo'];
        }else{
            $nSeq = 0;
        }

        // Insert สินค้าจาก TCNTStkCountItemList ทั้งหมด
        // Last Update: Napat(Jame) 12/10/2022 RQ-12 ข้อมูลรายการสินค้าที่ตรวจนับที่ส่งมาจาก HQ (Item List) จะต้องไม่ถูกดีดออก
        // Last Update: Napat(Jame) 18/10/2022 RQ-12 กำหนดว่าสินค้ามาจาก HQ , FCIudRePackAvg : 1:เป็นสินค้าอ้างอิงจาก TCNTStkCountItemList (ค้นหา HQ) 0,NULL: สินค้าทั่วไป
        $tSQL  = "INSERT INTO TCNTPdtChkDT (FTBchCode,FTIuhDocNo,FTIuhDocType,FTPlcCode,FTPdtCode,FTIudBarCode,FTPdtName,FTPunName,FTWahCode,FCIudUnitC1,FCIudUnitC2,FCIudQtyC1,FCIudQtyC2,FCIudWahQty,FCIudQtyDiff,FCIudQtyBal,FNIudSeqNo,FTPunCode,FCIudUnitFact,FTIudStkCode,FCIudSetPrice,FCIudStkFac,FTPgpChain,FCIudCost,FTPdtSaleType,FTPdtNoDis,FDIudChkDate,FTIudChkTime,FCIudDIsAvg,FCIudFootAvg,FCIudRePackAvg,FTIudChkUser)";
        $tSQL .= "SELECT 
                    '$aData[FTBchCode]'             AS FTBchCode,
                    ''								AS FTIuhDocNo,
                    '1'                             AS FTIuhDocType,
                    '$aData[FTPlcCode]'             AS FTPlcCode,
                    ISNULL(TCNTStkCountItemList.FTPdtCode,'')               AS FTPdtCode,
                    ISNULL(TCNTStkCountItemList.FTPdtBarCode,'')         AS FTIudBarCode,
                    TCNMPdt.FTPdtName               AS FTPdtName,
                    TCNMPdtUnit.FTPunName           AS FTPunName,
                    '001'                           AS FTWahCode,
                    0                               AS FCIudUnitC1, 
                    0                               AS FCIudUnitC2, 
                    0                               AS FCIudQtyC1, 
                    0                               AS FCIudQtyC2, 
                    TCNMPdt.FCPdtQtyRet             AS FCIudWahQty, 
                    0                               AS FCIudQtyDiff, 
                    0                               AS FCIudQtyBal,
                    $nSeq + ROW_NUMBER() OVER(ORDER BY TCNMPdtBar.FDPdtPriAffect DESC) AS FNIudSeqNo, 
                    TCNMPdt.FTPunCode               AS FTPunCode, 
                    TCNMPdt.FCPdtUnitFact           AS FCIudUnitFact, 
                    TCNMPdt.FTPdtStkCode            AS FTIudStkCode, 
                    ISNULL((SELECT TOP 1 FCPdtRetPri1 FROM TCNMPdtBar TBar2 WITH(NOLOCK) WHERE TBar2.FTPdtCode = TCNMPdtBar.FTPdtCode AND TBar2.FTPdtBarCode = TCNMPdtBar.FTPdtBarCode AND TBar2.FDPdtPriAffect <= GETDATE() ORDER BY TCNMPdtBar.FDPdtPriAffect DESC ),0) AS FCIudSetPrice, 
                    TCNMPdt.FCPdtStkFac             AS FCIudStkFac,
                    TCNMPdt.FTPgpChain              AS FTPgpChain, 
                    TCNMPdt.FCPdtCostStd            AS FCIudCost, 
                    TCNMPdt.FTPdtSaleType           AS FTPdtSaleType, 
                    TCNMPdt.FTPdtStaReturn          AS FTPdtNoDis,
                    NULL                            AS FDIudChkDate,
                    NULL                            AS FTIudChkTime,
                    0                               AS FCIudDIsAvg,
                    0                               AS FCIudFootAvg,
                    1                               AS FCIudRePackAvg,
                    ''                              AS FTIudChkUser
                FROM TCNTStkCountItemList	WITH (NOLOCK)
                LEFT JOIN TCNMPdt			WITH (NOLOCK)	ON TCNTStkCountItemList.FTPdtCode	= TCNMPdt.FTPdtCode         
                LEFT JOIN TCNMPdtUnit		WITH (NOLOCK)	ON TCNMPdt.FTPunCode				= TCNMPdtUnit.FTPunCode
                LEFT JOIN TCNMPdtBar		WITH (NOLOCK)	ON TCNTStkCountItemList.FTPdtCode   = TCNMPdtBar.FTPdtCode AND TCNTStkCountItemList.FTPdtBarCode = TCNMPdtBar.FTPdtBarCode
                WHERE TCNTStkCountItemList.FTBchCode		= '$aData[FTBchCode]'
                  AND TCNTStkCountItemList.FTPdtCylCntNo	= '$aData[FTPdtCylCntNo]' 
                  AND CONVERT(VARCHAR,TCNTStkCountItemList.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23)
                ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode "; 
        // AND TCNMPdt.FTPdtStaAudit                 = '1'   --เอาเฉพาะสินค้าที่ตรวจนับได้ 
        // AND ISNULL(TCNMPdtBar.FTPdtBarCode,'')    != ''   --เอาเฉพาะสินค้าที่มีบารโค๊ด
        // AND ISNULL(TCNMPdtUnit.FTPunCode,'')      != ''   --เอาเฉพาะสินค้าที่มีหน่วย
        // AND TCNMPdt.FTPdtType                     <> '7'
        $tResult = $this->DB_EXECUTE($tSQL);

        // Create By : Napat(Jame) 21/09/2020 
        // เท่ากับ success คือ insert สำเร็จ
        // เท่ากับ NULL คือ insert สำเร็จ แต่ไม่มีสินค้า num_row = 0
        // Last Update: Napat(Jame) 14/10/2022 RQ-12 เพิ่มการดีดสินค้าที่ไม่ตรงเงื่อนไข และระบุรหัสเหตุผล ลงตาราง TCNTPdtChkDTCut
        if($tResult == 'success' || $tResult === NULL){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMPASAddDataSearchHQ2DT] ย้ายสินค้าจาก TCNTStkCountItemList ไปยัง TCNTPdtChkDT สำเร็จ',
            );
            $this->FSxMPASWriteLog($aDataResult['tStaMessage']);

            // เคลียร์ข้อมูลในตาราง TCNTPdtChkDTCut ของสาขาที่ใช้งานอยู่
            $tSQL1 = "  DELETE FROM TCNTPdtChkDTCut WITH(ROWLOCK)
                        WHERE FTBchCode = '".$aData['FTBchCode']."' AND ISNULL(FTIuhDocNo,'') = '' AND FTIudChkUser = 'FamilyMartGit' ";
            $this->DB_EXECUTE($tSQL1);
            $this->FSxMPASWriteLog('[FSaMPASAddDataSearchHQ2DT] เคลียร์ข้อมูลในตาราง TCNTPdtChkDTCut');

            // ย้ายสินค้าที่ไม่ตรงเงื่อนไข ไปตาราง TCNTPdtChkDTCut
            $tSQL2 = "  INSERT INTO TCNTPdtChkDTCut (FTBchCode,FTIuhDocNo,FTIuhDocType,FNIudSeqNo,FTIudBarCode,FTPdtCode,FTPdtName,FTPunCode,
                        FTPunName,FTWahCode,FDIuhDocDate,FTIudStkCode,FCIudStkFac,FTPgpChain,FCIudUnitC1,FCIudUnitC2,FCIudUnitFact,FCIudQtyC1,
                        FCIudQtyC2,FCIudWahQty,FCIudQtyDiff,FCIudQtyBal,FTIudStaPrc,FTPdtArticle,FTDcsCode,FTPszCode,FTClrCode,FTPszName,FTClrName,
                        FTPdtNoDis,FCIudDisAvg,FCIudFootAvg,FCIudRePackAvg,FCIudCost,FCIudSetPrice,FTPlcCode,FDIudChkDate,FTIudChkTime,FTIudChkUser,
                        FTPdtSaleType,FDDateUpd,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns)
                        SELECT DT.FTBchCode,DT.FTIuhDocNo,DT.FTIuhDocType,DT.FNIudSeqNo,DT.FTIudBarCode,DT.FTPdtCode,DT.FTPdtName,DT.FTPunCode,
                        DT.FTPunName,DT.FTWahCode,DT.FDIuhDocDate,DT.FTIudStkCode,DT.FCIudStkFac,DT.FTPgpChain,DT.FCIudUnitC1,DT.FCIudUnitC2,
                        DT.FCIudUnitFact,DT.FCIudQtyC1,DT.FCIudQtyC2,DT.FCIudWahQty,DT.FCIudQtyDiff,DT.FCIudQtyBal,DT.FTIudStaPrc,DT.FTPdtArticle,
                        DT.FTDcsCode,
                        CASE 
                            WHEN TCNMPdt.FTPdtCode IS NULL THEN '001' /*ไม่พบสินค้าในระบบ*/
                            WHEN TCNMPdtUnit.FTPunCode IS NULL THEN '002' /*ไม่พบหน่วยในระบบ*/
                            WHEN TCNMPdtBar.FTPdtBarCode IS NULL THEN '003' /*ไม่พบบาร์โค้ดระบบ*/
                            WHEN TCNMPdt.FTPdtType = '7' THEN '004' /*สินค้าฝากขาย*/
                        END AS FTPszCode
                        ,DT.FTClrCode,DT.FTPszName,DT.FTClrName,DT.FTPdtNoDis,DT.FCIudDisAvg,DT.FCIudFootAvg,DT.FCIudRePackAvg,
                        DT.FCIudCost,DT.FCIudSetPrice,DT.FTPlcCode,DT.FDIudChkDate,DT.FTIudChkTime,'FamilyMartGit' AS FTIudChkUser,DT.FTPdtSaleType,DT.FDDateUpd,
                        DT.FTTimeUpd,DT.FTWhoUpd,DT.FDDateIns,DT.FTTimeIns,DT.FTWhoIns
                        FROM TCNTPdtChkDT DT        WITH (NOLOCK) 
                        LEFT JOIN TCNMPdt			WITH (NOLOCK)	ON DT.FTPdtCode	= TCNMPdt.FTPdtCode         
                        LEFT JOIN TCNMPdtUnit		WITH (NOLOCK)	ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode
                        LEFT JOIN TCNMPdtBar		WITH (NOLOCK)	ON DT.FTPdtCode = TCNMPdtBar.FTPdtCode AND DT.FTIudBarCode = TCNMPdtBar.FTPdtBarCode
                        WHERE (ISNULL(TCNMPdtBar.FTPdtBarCode,'') = '' OR ISNULL(TCNMPdtUnit.FTPunCode,'')  = '' OR TCNMPdt.FTPdtType = '7')
                            AND DT.FTBchCode = '".$aData['FTBchCode']."'
                            AND ISNULL(DT.FTIuhDocNo,'') = ''
                        ORDER BY FNIudSeqNo";
            $this->DB_EXECUTE($tSQL2);
            $this->FSxMPASWriteLog('[FSaMPASAddDataSearchHQ2DT] ย้ายสินค้าจาก TCNTPdtChkDT ที่ไม่ตรงเงื่อนไขไปตาราง TCNTPdtChkDTCut');

            // ลบสินค้าที่ไม่ตรงเงื่อนไขในตาราง TMPTPdtChkDT
            $tSQL4 = "  DELETE DT
                        FROM TCNTPdtChkDT DT        WITH (NOLOCK) 
                        LEFT JOIN TCNMPdt			WITH (NOLOCK)	ON DT.FTPdtCode	= TCNMPdt.FTPdtCode         
                        LEFT JOIN TCNMPdtUnit		WITH (NOLOCK)	ON TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode
                        LEFT JOIN TCNMPdtBar		WITH (NOLOCK)	ON DT.FTPdtCode = TCNMPdtBar.FTPdtCode AND DT.FTIudBarCode = TCNMPdtBar.FTPdtBarCode
                        WHERE (ISNULL(TCNMPdtBar.FTPdtBarCode,'') = '' OR ISNULL(TCNMPdtUnit.FTPunCode,'')  = '' OR TCNMPdt.FTPdtType = '7')
                            AND DT.FTBchCode = '".$aData['FTBchCode']."'
                            AND ISNULL(DT.FTIuhDocNo,'') = ''";
            $this->DB_EXECUTE($tSQL4);
            $this->FSxMPASWriteLog('[FSaMPASAddDataSearchHQ2DT] ลบสินค้าที่ไม่ตรงเงื่อนไขในตาราง TMPTPdtChkDT');

        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMPASAddDataSearchHQ2DT] '.$tResult[0]['message']
            );
            $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        }
        return $aDataResult;
    }

    // Last Update : Napat(Jame) 12/10/2022 RQ-12 ไม่สนใจสถานะอนุญาตตรวจนับ
    public function FSaMPASChkDataSearchHQ($paData){

        $tSQL = "   SELECT
                        CIL.FTPdtCode,
                        CIL.FTPdtBarCode,
                        ISNULL(PDT.FTPdtName,'ไม่พบสินค้า') AS FTPdtName,
                        ISNULL(PDT.FTPdtStaAudit,'2') AS FTPdtStaAudit,

                        -- 1 = มี
                        -- 2 = ไม่มี
                        CASE
                            WHEN ISNULL(PDT.FTPdtCode,'') = ''
                            THEN '2'
                            ELSE '1'
                        END AS FTStaPdtCode,
                        CASE
                            WHEN ISNULL(BAR.FTPdtBarCode,'') = ''
                            THEN '2'
                            ELSE '1'
                        END AS FTStaBarCode,
                        CASE
                            WHEN ISNULL(UNT.FTPunCode,'') = ''
                            THEN '2'
                            ELSE '1'
                        END AS FTStaPunCode
                    FROM TCNTStkCountItemList CIL    WITH(NOLOCK) 
                    LEFT JOIN TCNMPdt         PDT    WITH(NOLOCK) ON CIL.FTPdtCode = PDT.FTPdtCode
                    LEFT JOIN TCNMPdtUnit     UNT	 WITH(NOLOCK) ON PDT.FTPunCode = UNT.FTPunCode
                    LEFT JOIN TCNMPdtBar      BAR    WITH(NOLOCK) ON CIL.FTPdtCode = BAR.FTPdtCode AND CIL.FTPdtBarCode = BAR.FTPdtBarCode
                    WHERE ( ISNULL(PDT.FTPdtCode,'') = '' OR ISNULL(BAR.FTPdtBarCode,'') = '' OR ISNULL(UNT.FTPunCode,'') = '' OR PDT.FTPdtType = '7' )
                      AND CIL.FTBchCode = '$paData[FTBchCode]'
                      AND CIL.FTPdtCylCntNo = '$paData[FTPdtCylCntNo]'
                      AND CONVERT(VARCHAR,CIL.FDPdtStkChkDate,23) = CONVERT(VARCHAR,GETDATE(),23)
                    ORDER BY PDT.FTPdtCode ASC, FTStaBarCode DESC, FTStaPunCode DESC ";
        // PDT.FTPdtStaAudit   = '2' OR
        // PDT.FTPdtStaAudit DESC,

        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){ //มีสินค้าที่ไม่สามารถตรวจนับได้ popup แจ้งเตือน user [2019-293]
            $aDataResult = array(
                'aDataAudit'    => $aDataList,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'aDataAudit'    => array(),
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    // Napat(Jame) 18/10/2022 เปิดใบย่อยที่ยังไม่สร้างใบรวมทั้งหมด
    public function FSaMPASGetDataSearchHD($paDataWhere){
        $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
        $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTIuhDocNo DESC) AS rtRowID ,  * FROM
                 (SELECT 
                    FTIuhDocNo,
                    FDIuhDocDate,
                    FTIuhStaPrcDoc,
                    FTIuhAdjType 
                  FROM 
                    TCNTPdtChkHD WITH(NOLOCK)
                  WHERE FTIuhDocType        = '1' 
                    AND FTIuhStaDoc         = '1' 
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    -- AND (FTIuhStaPrcDoc = '1' OR FTIuhStaPrcDoc = '' OR FTIuhStaPrcDoc IS NULL) 
                    AND (FTIuhDocRef = '' OR FTIuhDocRef IS NULL) 
                    AND (FTDptCode          = '001')";
        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= "AND FTIuhDocNo        LIKE '%$paDataWhere[tSearch]%'";
        }
        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aFoundRow      = $this->FSaMPASGetPageDataSearchHD($paDataWhere);
            $nFoundRow      = $aFoundRow['counts'];
            $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataResult = array(
                'tType'         => '1',
                'aItems'        => $aDataList,
                'nPage'         => $paDataWhere['nPage'],
                'nAllRow'       => $nFoundRow,
                'nAllPage'      => $nPageAll,
                'nCurrentPage'  => $paDataWhere['nPage'],
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tType'         => '1',
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASGetPageDataSearchHD($paDataWhere){
        $tSQL = "SELECT 
                    COUNT(FTIuhDocNo) AS counts
                 FROM 
                    TCNTPdtChkHD WITH(NOLOCK)
                 WHERE FTIuhDocType         = '1' 
                    AND FTIuhStaDoc         = '1' 
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' )
                    AND (FTIuhStaPrcDoc = '1' OR FTIuhStaPrcDoc = '' OR FTIuhStaPrcDoc IS NULL) 
                    AND (FTIuhDocRef = '' OR FTIuhDocRef IS NULL) 
                    AND (FTDptCode          = '001')";
        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= "AND FTIuhDocNo     LIKE '%$paDataWhere[tSearch]%'";
        }
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    public function FSaMPASGetDataSearchDT($paDataWhere){
        $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
        $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTIuhDocNo DESC) AS rtRowID , * FROM
                 (SELECT 
                    FTIuhDocNo,
                    FTPdtCode,
                    FTIudBarCode,
                    FTPdtName
                  FROM 
                    TCNTPdtChkDT WITH(NOLOCK)
                  WHERE FTIuhDocNo  = '$paDataWhere[FTIuhDocNo]'
                    AND FTBchCode   = '$paDataWhere[FTBchCode]'";
        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aFoundRow      = $this->FSaMPASGetPageDataSearchDT($paDataWhere);
            $nFoundRow      = $aFoundRow['counts'];
            $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataResult = array(
                'tType'         => '2',
                'aItems'        => $aDataList,
                'nPage'         => $paDataWhere['nPage'],
                'nAllRow'       => $nFoundRow,
                'nAllPage'      => $nPageAll,
                'nCurrentPage'  => $paDataWhere['nPage'],
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tType'         => '2',
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    public function FSaMPASGetPageDataSearchDT($paDataWhere){
        $tSQL = "SELECT 
                    COUNT(FTIuhDocNo) AS counts
                 FROM 
                    TCNTPdtChkDT WITH(NOLOCK)
                 WHERE FTIuhDocNo   = '$paDataWhere[FTIuhDocNo]'
                   AND FTBchCode    = '$paDataWhere[FTBchCode]'";
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

    //เคลื่อนไหวหลังตรวจนับ
    public function FSaMPASGetDataAfterCount($paData){
        $aRowLen    = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
        /*(-ISNULL(STK.FCQtySale,0)-ISNULL(STK.FCQtyReturn,0)-ISNULL(STK.FCQtyPR,0)+
            ISNULL(STK.FCQtyPC,0)+ISNULL(STK.FCQtyTC,0)+ISNULL(STK.FCQtyTO,0)-ISNULL(STK.FCQtyTR,0)-
            ISNULL(STK.FCQtyAI,0)+ISNULL(STK.FCQtyAO,0)) AS FCAfterCount*/
        $tSQL = "SELECT L.* FROM (
                        SELECT
                            ROW_NUMBER() OVER(ORDER BY DT.FNIudSeqNo ASC) AS RowID,
                            DT.FTBchCode,
                            DT.FTIudStkCode,
                            (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
                                ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
                                ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0)) AS FCAfterCount
                        FROM TCNTPdtChkDT DT
                        LEFT JOIN (
                            SELECT STKL.FTBchCode,STKL.FTPdtStkCode,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPR,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTR,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAI,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPC,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTC,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTO,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAO,
                                SUM(CASE WHEN STKL.FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S' THEN STKL.FCStkQty ELSE 0 END) AS FCQtySale,
                                SUM(CASE WHEN STKL.FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyReturn
                            FROM TCNTPdtStkCard STKL WITH(NOLOCK)
                            LEFT JOIN TCNTPdtChkDT DTL WITH(NOLOCK) ON DTL.FTIudStkCode = STKL.FTPdtStkCode AND DTL.FTBchCode = STKL.FTBchCode
                            WHERE 1=1
                            AND DTL.FTIuhDocNo = '$paData[FTIuhDocNo]'
                            AND STKL.FDDateIns BETWEEN CONVERT(VARCHAR(10),DTL.FDIudChkDate,121) AND CONVERT(VARCHAR(10),GETDATE(),121)
                            AND STKL.FTTimeIns BETWEEN CONVERT(VARCHAR(8),DTL.FTIudChkTime,8) AND CONVERT(VARCHAR(8),GETDATE(),8)
                            AND LEFT(FTStkDocNo,2) NOT IN ('TE','TD')
                            GROUP BY STKL.FTBchCode,STKL.FTPdtStkCode ) STK ON DT.FTIudStkCode  = STK.FTPdtStkCode AND DT.FTBchCode = STK.FTBchCode
                        WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]'
                ) AS L
		        WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
        ";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'aItems'        => $aDataList,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'aItems'        => array(),
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
        // $tSQL = "SELECT
        //             DT.FTIudStkCode,
        //             PSC.FCStkQty,
        //             PSC.FTStkType
        //         FROM TCNTPdtChkDT DT
        //         LEFT JOIN TCNTPdtStkCard PSC ON DT.FTIudStkCode = PSC.FTPdtStkCode 
        //                                         AND DT.FTBchCode = PSC.FTBchCode 
        //                                         AND PSC.FDDateIns BETWEEN CONVERT(VARCHAR(10),DT.FDIudChkDate,121) AND CONVERT(VARCHAR(10),GETDATE(),121)
        //                                         AND PSC.FTTimeIns BETWEEN CONVERT(VARCHAR(8),DT.FTIudChkTime,8) AND CONVERT(VARCHAR(8),GETDATE(),8)
        //         WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]' AND FTStkType <> 0
        // ";
        //  $aDataList = $this->DB_SELECT($tSQL);        
        //  if(count($aDataList) > 0){
        //      $aDataResult = array(
        //          'aItems'        => $aDataList,
        //          'nStaQuery'     => 1,
        //          'tStaMessage'   => 'found data',
        //      );
        //  }else{
        //      $aDataResult = array(
        //          'aItems'        => array(),
        //          'nStaQuery'     => 99,
        //          'tStaMessage'   => 'not found data',
        //      );
        //  }
        //  return $aDataResult;
    }

    // Last Update : Napat(Jame) 14/11/2022 Comsheet/2022-055 เปลี่ยนการ between date/time ด้วย getdate() เนื่องจากถ้าทำใบรวมข้ามวันจะไม่เห็นข้อมูล
    public function FSaMPASUpdFromStockCard($paData){
        //Type
        //1 or 4 = + (รับ-คืน)
        //2 or 3 = - (ขาย)
        $tSQL = "	UPDATE 
                        TCNTPdtChkDT
                    SET 
                        FCIudQtyBal   = ISNULL(DT.FCIudUnitC1,0) + (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
                            ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
                            ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0))
                        ,FCIudQtyDiff = ISNULL(DT.FCIudUnitC1,0) + (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
                            ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
                            ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0)) - ISNULL(FCIudWahQty,0)
                        ,FTClrName    = (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
                            ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
                            ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0))
                    FROM TCNTPdtChkDT DT WITH(ROWLOCK)
                    INNER JOIN (
                        SELECT 
                            DTL.FTIuhDocNo,
                            STKL.FTBchCode,
                            STKL.FTPdtStkCode,
                            SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPR,
                            SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTR,
                            SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAI,

                            SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPC,
                            SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTC,
                            SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTO,
                            SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAO,
                            SUM(CASE WHEN STKL.FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S' THEN STKL.FCStkQty ELSE 0 END) AS FCQtySale,

                            SUM(CASE WHEN STKL.FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyReturn
                        FROM TCNTPdtStkCard STKL WITH(NOLOCK)
                        INNER JOIN TCNTPdtChkDT DTL WITH(NOLOCK) ON DTL.FTIudStkCode = STKL.FTPdtStkCode AND DTL.FTBchCode = STKL.FTBchCode
                        WHERE DTL.FTIuhDocNo = '$paData[FTIuhDocNo]'
                        AND (CONVERT(varchar(8),STKL.FDDateIns,112) + STKL.FTTimeIns) BETWEEN CONVERT(VARCHAR(8),DTL.FDIudChkDate,112) + DTL.FTIudChkTime AND CONVERT(VARCHAR(8),DTL.FDDateUpd,112) + DTL.FTTimeUpd
                        AND (LEFT(FTStkDocNo,2) != 'TE' AND LEFT(FTStkDocNo,2) != 'TD')
                        GROUP BY DTL.FTIuhDocNo,STKL.FTBchCode,STKL.FTPdtStkCode
                    ) STK ON DT.FTIudStkCode = STK.FTPdtStkCode AND DT.FTBchCode = STK.FTBchCode AND DT.FTIuhDocNo = STK.FTIuhDocNo
                    WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]'
    ";
        // $tSQL = "UPDATE DT
        //             SET
        //                 DT.FCIudQtyBal  = ISNULL(DT.FCIudUnitC1,0) + (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
        //                                                 ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
        //                                                 ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0)),
        //                 DT.FCIudQtyDiff = ISNULL(DT.FCIudUnitC1,0) + (-ISNULL(STK.FCQtySale,0)+ISNULL(STK.FCQtyReturn,0)+ISNULL(STK.FCQtyPR,0)-
        //                                                 ISNULL(STK.FCQtyPC,0)-ISNULL(STK.FCQtyTC,0)-ISNULL(STK.FCQtyTO,0)+ISNULL(STK.FCQtyTR,0)+
        //                                                 ISNULL(STK.FCQtyAI,0)-ISNULL(STK.FCQtyAO,0)) - ISNULL(FCIudWahQty,0)
        //             FROM TCNTPdtChkDT DT
        //             LEFT JOIN (
        //                 SELECT 
        //                     ROW_NUMBER() OVER(ORDER BY DTL.FNIudSeqNo ASC) AS RowID,
        //                     STKL.FTBchCode,
        //                     STKL.FTPdtStkCode,
        //                     SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPR,
        //                     SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTR,
        //                     SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAI,

        //                     SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPC,
        //                     SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTC,
        //                     SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTO,
        //                     SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAO,
        //                     SUM(CASE WHEN STKL.FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S' THEN STKL.FCStkQty ELSE 0 END) AS FCQtySale,

        //                     SUM(CASE WHEN STKL.FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyReturn
        //                 FROM TCNTPdtStkCard STKL WITH(NOLOCK)
        //                 LEFT JOIN TCNTPdtChkDT DTL WITH(NOLOCK) ON DTL.FTIudStkCode = STKL.FTPdtStkCode AND DTL.FTBchCode = STKL.FTBchCode
        //                 WHERE 1=1
        //                 AND DTL.FTIuhDocNo = '$paData[FTIuhDocNo]'
        //                 AND STKL.FDDateIns BETWEEN CONVERT(VARCHAR(10),DTL.FDIudChkDate,121) AND CONVERT(VARCHAR(10),GETDATE(),121)
        //                 AND STKL.FTTimeIns BETWEEN CONVERT(VARCHAR(8),DTL.FTIudChkTime,8) AND CONVERT(VARCHAR(8),GETDATE(),8)
        //                 AND LEFT(FTStkDocNo,2) NOT IN ('TE','TD')
        //                 GROUP BY STKL.FTBchCode,STKL.FTPdtStkCode
        //             ) STK ON DT.FTIudStkCode  = STK.FTPdtStkCode AND DT.FTBchCode = STK.FTBchCode
        //             WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]'
        // ";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'update data success',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'nStaQuery'     => 99,
                'tStaMessage'   => 'can not update data',
            );
        }
        return $aDataResult;
    }

    //อัพเดท ยอดขายก่อนนับ
    public function FSaMPASUpdSalesB4Count(){
        $tSQL = "UPDATE TCNTPdtChkDT
                    SET 
                        FCIudUnitC2 = ISNULL(
                (
                    SELECT SUM(CASE
                                    WHEN FTStkType = 3
                                    THEN FCStkQty
                                    WHEN FTStkType = 4
                                    THEN -FCStkQty
                                    ELSE 0
                                END)
                    FROM TCNTPdtStkCard WITH(NOLOCK)
                    WHERE FTStkType IN(3, 4)
                    AND LEFT(FTStkDocNo, 1) IN('S', 'R')
                    AND FDStkDate    = FDIudChkDate
                    AND FTPdtStkCode = FTIudStkCode
                    AND FTTimeIns   <= FTIudChkTime
                ), 0)
                WHERE FTIuhDocNo=''
        ";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMPASUpdSalesB4Count] อัพเดทยอดขายก่อนนับจาก TCNTPdtStkCard',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMPASUpdSalesB4Count] '.$tResult[0]['message'],
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        return $aDataResult;
    }

    function FSaMPASCheckMonthEnd(){
        $tSQL = "   SELECT TOP 1
                        FTStkDocNo 
                    FROM TCNTPdtStkCard WITH(NOLOCK) 
                    WHERE LEFT(FTStkDocNo,2) = 'ME' 
                    AND CONVERT(VARCHAR(7),FDStkDate,121) = CONVERT(VARCHAR(7),GETDATE(),121)
        ";
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    //อัพเดท FCIudQtyC1 ถ้าหากสินค้ามาจาก แฮนเฮว (CFM-POS ComSheet-2020-014)
    function FSxMPASUpdQtyC1($paData){
        $tSQL = "   UPDATE TCNTPdtChkDT WITH(ROWLOCK) 
                    SET
                        FCIudQtyC1     = (FCIudUnitC1 * FCIudStkFac)
                    WHERE 1=1
                    AND FTIuhDocNo              = '$paData[FTIuhDocNo]'
                    AND FTBchCode               = '$paData[FTBchCode]'
                    AND FTIuhDocType            = 1
                    AND ISNULL(FTPdtArticle,'') <> '' --จับฟิวส์นี้เพราะมาจาก แฮนเฮว (Napat 06-02-2563)
                    AND ISNULL(FTDcsCode,'')    <> '' --จับฟิวส์นี้เพราะมาจาก แฮนเฮว (Napat 06-02-2563)
        ";
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => 'update data success',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => 'can not update data',
            );
        }
        return $aDataResult;
    }

    //ค้นหาสินค้าตรวจนับ
    function FSaMPASSearchProduct($paData){
        $tWhereFilter     = "";

        if(isset($paData['FTBchCode']) && !empty($paData['FTBchCode'])){
            $tWhereFilter       .= " AND A.FTBchCode = '$paData[FTBchCode]' ";
        }else{
            $tWhereFilter       .= " AND A.FTBchCode = '' ";
        }

        if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
            $tWhereFilter       .= " AND A.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocNo = '' ";
        }

        if(isset($paData['nPageType']) && $paData['nPageType'] != "1"){
            $tWhereFilter       .= " AND A.FTIuhDocType = '2' ";
        }else{
            $tWhereFilter       .= " AND A.FTIuhDocType = '1' ";
        }

        if(isset($paData['ptTextSearch']) && !empty($paData['ptTextSearch'])){ //ตรวจสอบว่ากรอกคำค้นหามาด้วยไหม
            if(isset($paData['ptFilter']) && !empty($paData['ptFilter'])){  //ตรวจสอบฟิวเตอร์ที่เลือก
                $tWhereFilter .= " AND A.".$paData['ptFilter']." LIKE '%".$paData['ptTextSearch']."%' ";
            }
        }

        $tSQL = "   SELECT
                        TOP 1
                        A.FNIudSeqNo AS RowIDItems
                    FROM TCNTPdtChkDT A WITH (NOLOCK)
                    WHERE 1=1 
                    $tWhereFilter
        ";

        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList[0],
                'nStaQuery'         => 1,
                'tStaMessage'       => 'Select Data from TCNTPdtChkDT Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => array(),
                'nStaQuery'         => 99,
                'tStaMessage'       => 'error Can not select data from TCNTPdtChkDT (FSxMPASGetDataTable)',
            );
        }
        return $aDataResult;
    }

    //ค้นหาสินค้าไม่มีในระบบ
    function FSaMPASSearchPdtWithOutSystem($paData){

        $tWhereFilter = "";

        if(isset($paData['FTIuhDocNo']) && !empty($paData['FTIuhDocNo'])){
            if(isset($paData['nPageType']) && $paData['nPageType'] == "1"){
                $tWhereFilter       .= " AND E.FTIuhDocNo = '$paData[FTIuhDocNo]' ";
            }else{
                $tWhereFilter       .= " AND E.FTIuhDocNoType2 = '$paData[FTIuhDocNo]' ";
            }
        }

        if(isset($paData['ptTextSearch']) && !empty($paData['ptTextSearch'])){ //ตรวจสอบว่ากรอกคำค้นหามาด้วยไหม
            if(isset($paData['ptFilter']) && !empty($paData['ptFilter'])){  //ตรวจสอบฟิวเตอร์ที่เลือก
                $tWhereFilter .= " AND L.".$paData['ptFilter']." LIKE '%".$paData['ptTextSearch']."%' ";
            }
        }

        $tSQL = "   SELECT TOP 1
                        L.RowID AS RowIDItems,
                        L.FTIuhDocNo,
                        L.FTPdtBarCode,
                        L.FTPlcCode
                    FROM TCNTPdtStkNotExist E
                    LEFT JOIN (
                            SELECT
                                ROW_NUMBER() OVER(ORDER BY A.FTPdtBarCode ASC) AS RowID,
                                A.*
                            FROM TCNTPdtStkNotExist A WITH (NOLOCK)
                        ) L ON E.FTIuhDocNo = L.FTIuhDocNo AND E.FTPdtBarCode = L.FTPdtBarCode AND E.FTPlcCode = L.FTPlcCode
                    WHERE 1=1
                    $tWhereFilter
        ";

        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => $aDataList[0],
                'nStaQuery'         => 1,
                'tStaMessage'       => 'Select Data from Success',
            );
        }else{
            $aDataResult = array(
                'tSQL'              => $tSQL,
                'aItems'            => array(),
                'nStaQuery'         => 99,
                'tStaMessage'       => 'error Can not select data',
            );
        }
        return $aDataResult;

    }

    // Function : ค้นหาเอกสาร D/O (HD)
    // Create By: Napat(Jame) 05/08/2020
    public function FSaMPASGetDataSearchDO($paDataWhere){

        // เตรียมตัวแปร
        $tDocNo         = $paDataWhere['tDocNo'];
        $tBchCode       = $paDataWhere['FTBchCode'];
        $nDayDocRef     = intval($paDataWhere['nDayDocRef']);

        // ค้นหาเลขที่เอกสาร D/O ที่ผูกกับเอกสารตรวจนับ
        $tTsysSQL = "   SELECT 
                            FTSqlCmd
                        FROM TSysSQL WITH(NOLOCK) 
                        WHERE FTSqlCode     = 'FamilyGit'
                            AND FTSqlApp    = '$tDocNo'
                            AND FNSqlUsage  = 1
                    ";
        $aTsysSQL = $this->DB_SELECT($tTsysSQL);  
        $tWhereDO  = "'";      
        if( count($aTsysSQL) > 0 ){
            $tWhereDO  .= str_replace(",","','",$aTsysSQL[0]['FTSqlCmd']);
        }
        $tWhereDO  .= "'";

        
        $tSQL = "   SELECT
                        PDHD.FTXihDocNo,
                        CONVERT(VARCHAR(10),PDHD.FDXihDocDate,103) AS FDXihDocDate,
                        PDHD.FTSplCode,
                        SPL.FTSplName,
                        PDHD.FTXihRefInt,
                        ISNULL(PIHD.FTXihDocNo,'-') AS FTDocRefIn,
                        CASE WHEN PDHD.FTXihDocNo IN ($tWhereDO) THEN 1 ELSE 0 END AS FNXihStaActive
                    FROM TACTPdHD PDHD WITH(NOLOCK)
                    LEFT JOIN TACTPiHD PIHD WITH(NOLOCK) ON PDHD.FTXihDocNo = PIHD.FTXihRefExt
                    INNER JOIN TCNMSpl SPL WITH(NOLOCK) ON PDHD.FTSplCode = SPL.FTSplCode
                    WHERE 1=1
                        AND CONVERT(VARCHAR(10),PDHD.FDXihDocDate,121) BETWEEN CONVERT(VARCHAR(10),DATEADD(DAY, -$nDayDocRef, GETDATE()),121) AND CONVERT(VARCHAR(10),GETDATE(),121)
                        AND PDHD.FTBchCode = '$tBchCode'
                        AND PDHD.FTXihDocType = '15'
                        AND PDHD.FTXihStaDoc = '1'
                        AND ISNULL(PDHD.FTXihRefExt,'') = ''
                        AND PDHD.FNXihStaRef IN (0,1,2)
                        AND ISNULL(PDHD.FTWhoUpd,'') <> 'AUTO'
                ";
        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= " AND PDHD.FTXihDocNo LIKE '%$paDataWhere[tSearch]%' ";
        }
        $tSQL .= " ORDER BY FDXihDocDate DESC , PDHD.FTXihDocNo DESC ";
        
        $aDataList = $this->DB_SELECT($tSQL);        
        $nFoundRow = $this->FSnMPASGetPageDataSearchDO($paDataWhere);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'aItems'        => $aDataList,
                'nAllRow'       => $nFoundRow,
                'tType'         => '1',
                'tDocType'      => 'D/O',
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '1',
                'tDocType'      => 'D/O',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult; 
    }

    // Function : ค้นหาจำนวนรายการเอกสาร D/O (HD) ทั้งหมด
    // Create By: Napat(Jame) 05/08/2020
    public function FSnMPASGetPageDataSearchDO($paDataWhere){
        $tSQL = "   SELECT COUNT(a.FTXihDocNo) AS counts
                    FROM  TACTPdHD a WITH(NOLOCK)
                    INNER JOIN TCNMSpl c WITH(NOLOCK) ON a.FTSplCode = c.FTSplCode
                    WHERE 1=1 AND a.FTBchCode = '$paDataWhere[FTBchCode]'
                ";

        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= " AND a.FTXihDocNo LIKE '%$paDataWhere[tSearch]%' ";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0]['counts'];
        }else{
            return 0;
        }
    }

    // Function : ค้นหาเอกสาร D/O (DT)
    // Create By: Napat(Jame) 05/08/2020
    public function FSaMPASGetDataSearchDOList($paDataWhere){
        $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
        $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTXidBarCode ASC) AS rtRowID , * FROM ( 
                    SELECT 
                        DT.FTXidBarCode,
                        DT.FTPdtName,
                        DT.FTXidUnitName,
                        DT.FCXidQtyAll
                    FROM TACTPdDT DT WITH(NOLOCK)
                    WHERE 1=1 
                        AND DT.FTXihDocNo   = '$paDataWhere[FTXihDocNo]'
                        AND DT.FTBchCode    = '$paDataWhere[FTBchCode]'
        ";     
        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";   
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $nFoundRow      = $this->FSaMPASGetPageDataSearchDOList($paDataWhere);
            $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '2',
                'tDocType'      => 'D/O',
                'aItems'        => $aDataList,
                'nPage'         => $paDataWhere['nPage'],
                'nAllRow'       => $nFoundRow,
                'nAllPage'      => $nPageAll,
                'nCurrentPage'  => $paDataWhere['nPage'],
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '2',
                'tDocType'      => 'D/O',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    // Function : ค้นหาจำนวนรายการเอกสาร D/O (DT) ทั้งหมด
    // Create By: Napat(Jame) 05/08/2020
    public function FSaMPASGetPageDataSearchDOList($paDataWhere){
        $tSQL = "   SELECT COUNT(DT.FTXidBarCode) AS counts
                    FROM  TACTPdDT DT WITH(NOLOCK)
                    WHERE 1=1 
                        AND DT.FTXihDocNo   = '$paDataWhere[FTXihDocNo]'
                        AND DT.FTBchCode    = '$paDataWhere[FTBchCode]'
                ";
        
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0]['counts'];
        }else{
            return 0;
        }
    }

    // Function : เพิ่มการอ้างอิงเอกสาร
    // Create By: Napat(Jame) 06/08/2020
    public function FSaMPASAddUpdDocRef($paDataRef){
        // เตรียมข้อมูล
        $nTypeDocRef = $paDataRef['nType'];
        $tDocRef     = str_replace("'","",$paDataRef['tDocRef']);
        $tDocNo      = $paDataRef['tDocNo'];
        $tUserName   = $paDataRef['tUserName'];

        // ถ้าไม่ได้เลือกรายการ ให้ทำการ Delete Record ออก
        if( $tDocRef == "" ){
            $tSQLDel    = " DELETE FROM TsysSQL WHERE FTSqlCode = 'FamilyGit' AND FTSqlApp = '$tDocNo' AND FNSqlUsage = $nTypeDocRef ";
            $this->DB_EXECUTE($tSQLDel);
            $aDataResult = array(
                'tSQL'          => $tSQLDel,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'Delete Success',
            );
        }else{
            // ให้วิ่งไปอัพเดทก่อน ถ้าอัพเดทไม่ผ่านแสดงว่ายังไม่มี record ก็ให้ไป else insert data
            $tSQLUpd    = " UPDATE TsysSQL SET FTSqlCmd = '$tDocRef' WHERE FTSqlCode = 'FamilyGit' AND FTSqlApp = '$tDocNo' AND FNSqlUsage = $nTypeDocRef ";
            $tReturnUpd = $this->DB_EXECUTE($tSQLUpd);
            if($tReturnUpd == 'success'){
                $aDataResult = array(
                    'tSQL'          => $tSQLUpd,
                    'nStaQuery'     => 1,
                    'tStaMessage'   => 'Update Data Success',
                );
            }else{
                // ไปหา Seq ล่าสุด
                $tSQLSeq = " SELECT MAX(FNSqlSeq) AS FNCounts FROM TsysSQL WITH(NOLOCK) WHERE FTSqlCode = 'FamilyGit' ";
                $oQuery  = $this->DB_SELECT($tSQLSeq);
                if( !empty($oQuery) ) {
                    $nSeq = $oQuery[0]['FNCounts'] + 1;     // ถ้าพบรายการให้นำมา +1
                }else{
                    $nSeq = 1;                              // ไม่พบรายการ ให้เริ่มต้นที่ 1
                }

                // เพิ่มรายการ
                $tSQL  = "INSERT INTO TsysSQL ( FTSqlCode,FNSqlSeq,FTSqlApp,FTSqlDesTha,FTSqlDesEng
                                            ,FTSqlCmd,FNSqlEdit,FNSqlUsage,FDDateUpd
                                            ,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns )
                        ";
                $tSQL .= "SELECT
                            'FamilyGit'                         AS FTSqlCode,
                            $nSeq                               AS FNSqlSeq,
                            '$tDocNo'                           AS FTSqlApp,
                            'รวมเอกสารตรวจนับ'                    AS FTSqlDesTha,
                            'Full Count'                        AS FTSqlDesEng,
                            '$tDocRef'                          AS FTSqlCmd,
                            0                                   AS FNSqlEdit,
                            $nTypeDocRef                        AS FNSqlUsage,
                            CONVERT(VARCHAR(10),GETDATE(),121)  AS FDDateUpd,
                            CONVERT(VARCHAR(8),GETDATE(),114)   AS FTTimeUpd,
                            '$tUserName'                        AS FTWhoUpd,
                            CONVERT(VARCHAR(10),GETDATE(),121)  AS FDDateIns,
                            CONVERT(VARCHAR(8),GETDATE(),114)   AS FTTimeIns,
                            '$tUserName'                        AS FTWhoIns
                        ";

                $tReturnInsert = $this->DB_EXECUTE($tSQL);
                if($tReturnInsert == 'success'){
                    $aDataResult = array(
                        'tSQL'          => $tSQL,
                        'nStaQuery'     => 1,
                        'tStaMessage'   => 'Insert Data Success',
                    );
                }else{
                    $aDataResult = array(
                        'tSQL'          => $tSQL,
                        'nStaQuery'     => 99,
                        'tStaMessage'   => $tReturnInsert[0]['message'],
                    );
                }
            }
        }
        return $aDataResult;
    }

    // Function : คำนวณเอกสารอ้างอิง D/O , Auto Receive และนำไปอัพเดทจำนวนนับ
    // Create By: Napat(Jame) 07/08/2020
    public function FSaMPASCalculateQtyRef($aDataWhere){
        // เตรียมข้อมูล
        $tDocNo = $aDataWhere['tDocNo'];

        // ค้นหาเอกสาร D/O
        $tSQL_DO    = " SELECT TOP 1 FTSqlCmd FROM TSysSQL WITH(NOLOCK) WHERE FTSqlCode = 'FamilyGit' AND FTSqlApp = '$tDocNo' AND FNSqlUsage = 1 ";
        $oQuery_DO  = $this->DB_SELECT($tSQL_DO);
        if( !empty($oQuery_DO) ) {
            $tDocDORef = str_replace(",","','",$oQuery_DO[0]['FTSqlCmd']);
        }else{
            $tDocDORef = "";
        }

        // ค้นหาเอกสาร Auto Receive
        $tSQL_AR    = " SELECT TOP 1 FTSqlCmd FROM TSysSQL WITH(NOLOCK) WHERE FTSqlCode = 'FamilyGit' AND FTSqlApp = '$tDocNo' AND FNSqlUsage = 2 ";
        $oQuery_AR  = $this->DB_SELECT($tSQL_AR);
        if( !empty($oQuery_AR) ) {
            $tDocARRef = str_replace(",","','",$oQuery_AR[0]['FTSqlCmd']);
        }else{
            $tDocARRef = "";
        }

        // DROP VIEW 
        $tSQL1      = " IF EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = 'vChkDocRef') DROP VIEW vChkDocRef ";
        $tResult1   = $this->DB_EXECUTE($tSQL1);

        // สร้าง View จากเอกสาร D/O และ Auto Receive
        $tSQLCreateView  = " CREATE VIEW vChkDocRef AS

                             SELECT FTXidStkCode,( FCXidQtyAll * (-1) ) as FCXidQtyAll
                             FROM TACTPdDT WITH(NOLOCK)
                             WHERE FTXihDocNo IN ('$tDocDORef')
                           
                             UNION ALL 

                             SELECT FTXidStkCode, FCXidQtyAll
                             FROM TACTPiDT WITH(NOLOCK)
                             WHERE FTXihDocNo IN ('$tDocARRef')
                           ";
                     
        $tReturnCreateView = $this->DB_EXECUTE($tSQLCreateView);
        if($tReturnCreateView == null){
            // FCIudDisAvg เป็นฟิวส์ดัดแปลงมาใช้ สำหรับเก็บค่า QtyC1 ต้นฉบับ
            // ปรับ +/- จำนวนนับ

            $tSQLUpdDef = " UPDATE TCNTPdtChkDT WITH(ROWLOCK)
                            SET 
                                FCIudQtyBal  = FCIudUnitC2 + FCIudDisAvg,
                                FCIudQtyDiff = (FCIudUnitC2 + FCIudDisAvg) - FCIudWahQty,
                                FCIudUnitC1  = FCIudDisAvg,
                                FCIudQtyC1   = FCIudStkFac * FCIudDisAvg
                            WHERE FTIuhDocNo = '$tDocNo'
                          ";
            $tReturnUpd = $this->DB_EXECUTE($tSQLUpdDef);
            if($tReturnUpd == 'success'){
                $tSQL = "   UPDATE TCNTPdtChkDT WITH(ROWLOCK)
                            SET 
                                FCIudQtyBal  = ( ISNULL(DT.FCIudUnitC2,0) + ISNULL(DT.FCIudDisAvg,0) ) + (ISNULL(S.FCXidQtyAll,0)),
                                FCIudQtyDiff = ( ISNULL(DT.FCIudUnitC2,0) + ISNULL(DT.FCIudDisAvg,0) + (ISNULL(S.FCXidQtyAll,0)) ) - ISNULL(DT.FCIudWahQty,0),
                                FCIudUnitC1  = ISNULL(DT.FCIudDisAvg,0) + (ISNULL(S.FCXidQtyAll,0)),
                                FCIudQtyC1   = ISNULL(DT.FCIudStkFac,0) * ( ISNULL(DT.FCIudDisAvg,0) + (ISNULL(S.FCXidQtyAll,0)) )
                            FROM TCNTPdtChkDT DT
                            INNER JOIN (
                                SELECT 
                                    FTXidStkCode        AS FTXidStkCode,
                                    SUM(FCXidQtyAll)    AS FCXidQtyAll
                                FROM vChkDocRef WITH(NOLOCK)
                                GROUP BY FTXidStkCode
                            ) S ON DT.FTIudStkCode = S.FTXidStkCode
                            WHERE DT.FTIuhDocNo = '$tDocNo' 
                        ";
                $tReturnUpd = $this->DB_EXECUTE($tSQL);
                if($tReturnUpd == 'success'){
                    $aDataResult = array(
                        'tSQL'          => $tSQL,
                        'nStaQuery'     => 1,
                        'tStaMessage'   => 'ปรับ +/- จำนวนนับ ตามเอกสารอ้างอิงสำเร็จ',
                    );
                }else{
                    // ถ้านำเอกสารอ้างอิงออกทั้งหมด ให้ปรับจำนวนนับ เป็นค่าที่กรอกมา โดยก่อนการ Ref นำค่าไปใส่ไว้ที่ FCIudDisAvg
                    $tReturnUpd = $this->DB_EXECUTE($tSQLUpdDef);
                    if($tReturnUpd == 'success'){
                        $aDataResult = array(
                            'tSQL'          => $tSQL,
                            'nStaQuery'     => 1,
                            'tStaMessage'   => 'อัพเดทจำนวนนับเป็นค่าเดิม',
                        );
                    }else{
                        $aDataResult = array(
                            'tSQL'          => $tSQL,
                            'nStaQuery'     => 99,
                            'tStaMessage'   => $tReturnUpd[0]['message'],
                        );
                    }
                }
            }else{
                $aDataResult = array(
                    'tSQL'          => $tSQLUpdDef,
                    'nStaQuery'     => 99,
                    'tStaMessage'   => $tReturnUpd[0]['message'],
                );
            }
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQLCreateView,
                'nStaQuery'     => 88,
                'tStaMessage'   => $tReturnCreateView[0]['message'],
            );
        }
        return $aDataResult;      
    }

    // Function : ค้นหาเอกสารอ้างอิง Auto Receive (HD)
    // Create By: Napat(Jame) 07/08/2020
    public function FSaMPASGetDataSearchAutoReceive($paDataWhere){

        // เตรียมตัวแปร
        $tDocNo         = $paDataWhere['tDocNo'];
        $tBchCode       = $paDataWhere['FTBchCode'];
        $nDayDocRef     = intval($paDataWhere['nDayDocRef']);

        // ค้นหาเลขที่เอกสาร Auto Receive ที่ผูกกับเอกสารตรวจนับ
        $tTsysSQL = "   SELECT 
                            FTSqlCmd
                        FROM TSysSQL WITH(NOLOCK) 
                        WHERE FTSqlCode     = 'FamilyGit'
                            AND FTSqlApp    = '$tDocNo'
                            AND FNSqlUsage  = 2
                    ";
        $aTsysSQL = $this->DB_SELECT($tTsysSQL);  
        $tWhereAutoReceive  = "'";      
        if( count($aTsysSQL) > 0 ){
            $tWhereAutoReceive  .= str_replace(",","','",$aTsysSQL[0]['FTSqlCmd']);
        }
        $tWhereAutoReceive  .= "'";

        $tSQL = "   SELECT
                        HD.FTXihDocNo,
                        CONVERT(VARCHAR(10),HD.FDXihDocDate,103) AS FDXihDocDate,
                        SPL.FTSplCode,
                        SPL.FTSplName,
                        CONVERT(VARCHAR(10),HD.FDXihDueDate,103) AS FDXihDueDate,
                        HD.FTXihRefInt                           AS FTDocRefIn,
                        CASE WHEN HD.FTXihDocNo IN ($tWhereAutoReceive) THEN 1 ELSE 0 END AS FNXihStaActive
                    FROM TACTPiHD HD
                    INNER JOIN TCNMSpl SPL ON HD.FTSplCode = SPL.FTSplCode 
                    WHERE 1=1
                        AND HD.FTBchCode = '$tBchCode'
                        AND HD.FTXihDocType IN ('1','2','3','4')
                        AND HD.FTXihStaDoc = '1' 
                        AND HD.FNXihStaDocAct = 1
                        AND HD.FTXihStaDoc = '1'
                        AND HD.FTDptCode = '001'
                        AND CONVERT(VARCHAR(10),HD.FDXihDocDate,121) BETWEEN CONVERT(VARCHAR(10),DATEADD(DAY, -$nDayDocRef, GETDATE()),121) AND CONVERT(VARCHAR(10),GETDATE(),121)
                ";
        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= " AND HD.FTXihDocNo LIKE '%$paDataWhere[tSearch]%' ";
        }
        $tSQL .= " ORDER BY HD.FDXihDocDate DESC , HD.FTXihDocNo DESC ";

        $aDataList = $this->DB_SELECT($tSQL);        
        $nFoundRow = $this->FSnMPASGetPageDataSearchAutoReceive($paDataWhere);
        if(count($aDataList) > 0){
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'aItems'        => $aDataList,
                'nAllRow'       => $nFoundRow,
                'tType'         => '1',
                'tDocType'      => 'Auto Receive',
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '1',
                'tDocType'      => 'Auto Receive',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult; 
    }

    // Function : ค้นหาจำนวนรายการเอกสาร Auto Receive (HD) ทั้งหมด
    // Create By: Napat(Jame) 07/08/2020
    public function FSnMPASGetPageDataSearchAutoReceive($paDataWhere){
        $tSQL = "   SELECT COUNT(a.FTXihDocNo) AS counts
                    FROM TACTPiHD a WITH(NOLOCK)
                    INNER JOIN TCNMSpl c WITH(NOLOCK) ON a.FTSplCode = c.FTSplCode
                    WHERE 1=1 AND a.FTBchCode = '$paDataWhere[FTBchCode]'
                ";
        if($paDataWhere['tSearch'] != "NULL"){
            $tSQL .= " AND a.FTXihDocNo LIKE '%$paDataWhere[tSearch]%' ";
        }
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0]['counts'];
        }else{
            return 0;
        }
    }

    // Function : ค้นหาเอกสาร Auto Receive (DT)
    // Create By: Napat(Jame) 07/08/2020
    public function FSaMPASGetDataSearchAutoReceiveList($paDataWhere){
        $aRowLen = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);
        $tSQL = "SELECT c.* FROM (SELECT ROW_NUMBER() OVER(ORDER BY FTXidBarCode ASC) AS rtRowID , * FROM ( 
                    SELECT 
                        DT.FTXidBarCode,
                        DT.FTPdtName,
                        DT.FTXidUnitName,
                        DT.FCXidQtyAll
                    FROM TACTPiDT DT WITH(NOLOCK)
                    WHERE 1=1 
                        AND DT.FTXihDocNo   = '$paDataWhere[FTXihDocNo]'
                        AND DT.FTBchCode    = '$paDataWhere[FTBchCode]'
        ";     
        $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";   
        $aDataList = $this->DB_SELECT($tSQL);        
        if(count($aDataList) > 0){
            $nFoundRow      = $this->FSnMPASGetPageDataSearchAutoReceiveList($paDataWhere);
            $nPageAll       = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '2',
                'tDocType'      => 'Auto Receive',
                'aItems'        => $aDataList,
                'nPage'         => $paDataWhere['nPage'],
                'nAllRow'       => $nFoundRow,
                'nAllPage'      => $nPageAll,
                'nCurrentPage'  => $paDataWhere['nPage'],
                'nStaQuery'     => 1,
                'tStaMessage'   => 'found data',
            );
        }else{
            $aDataResult = array(
                'tSQL'          => $tSQL,
                'tType'         => '2',
                'tDocType'      => 'Auto Receive',
                'nStaQuery'     => 99,
                'tStaMessage'   => 'not found data',
            );
        }
        return $aDataResult;
    }

    // Function : ค้นหาจำนวนรายการเอกสาร Auto Receive (DT) ทั้งหมด
    // Create By: Napat(Jame) 07/08/2020
    public function FSnMPASGetPageDataSearchAutoReceiveList($paDataWhere){
        $tSQL = "   SELECT COUNT(DT.FTXidBarCode) AS counts
                    FROM  TACTPiDT DT WITH(NOLOCK)
                    WHERE 1=1 
                        AND DT.FTXihDocNo   = '$paDataWhere[FTXihDocNo]'
                        AND DT.FTBchCode    = '$paDataWhere[FTBchCode]'
                ";
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0]['counts'];
        }else{
            return 0;
        }
    }

    // Function : หาค่าวันที่ย้อนหลัง ในการค้นหาเอกสารอ้างอิง
    // Create By: Napat(Jame) 10/08/2020
    public function FSnMPASGetDayDocRef(){
        $tSQL = "   SELECT TOP 1
                        CASE WHEN ISNULL(CF.FTSysUsrValue,'') = '' THEN  CF.FTSysDefValue ELSE CF.FTSysUsrValue END AS FTSysValue
                    FROM TSysConfig CF WITH(NOLOCK) 
                    WHERE CF.FTSysCode = 'ADayRefDO'
                ";
        $oQuery = $this->DB_SELECT($tSQL);
        if(!empty($oQuery)) {
            return $oQuery[0]['FTSysValue'];
        }else{
            return 0;
        }
    }

    // อัพเดท จำนวนปัจจุบัน จาก TCNMPdt.FCPdtQtyRet
    // Create By: Napat(Jame) 12/11/2020
    public function FSxMPASUpdWahQty($paData){
        $tSQL = "   UPDATE TCNTPdtChkDT
                    SET 
                        FCIudWahQty     = ISNULL(PDT.FCPdtQtyRet,0),
                        FCIudQtyDiff    = ISNULL(DT.FCIudQtyBal,0) - ISNULL(PDT.FCPdtQtyRet,0)
                    FROM TCNTPdtChkDT DT WITH(ROWLOCK)
                    INNER JOIN TCNMPdt PDT ON DT.FTIudStkCode = PDT.FTPdtStkCode AND DT.FTPdtCode = PDT.FTPdtCode
                    WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]'
                      AND ISNULL(DT.FCIudWahQty,0) != ISNULL(PDT.FCPdtQtyRet,0)
                ";
        $this->DB_EXECUTE($tSQL);
    }

    // Create By: Napat(Jame) 30/11/2020
    // return true = พบข้อมูลวันที่ยังไม่ได้ระบุ , false = ไม่พบข้อมูล
    public function FSbMPASEventChkDateDT($paData){
        $tSQL = "   SELECT 
                        FTPdtCode
                    FROM TCNTPdtChkDT DT WITH(NOLOCK) 
                    WHERE DT.FTIuhDocNo = '$paData[FTIuhDocNo]'
                        AND ISNULL(DT.FDIudChkDate,'') = ''
                        AND ISNULL(DT.FTIudChkTime,'') = ''
                ";
        $aDataList = $this->DB_SELECT($tSQL);        
        if( count($aDataList) > 0 ){
            return true;
        }else{
            return false;
        }
    }

    // Create By: Napat(Jame) 02/12/2020
    public function FSxMPASUpdateConfirmCode($paData){
        $tSQL = "   UPDATE TCNTPdtChkDT WITH(ROWLOCK) 
                    SET TCNTPdtChkDT.FTPszName = HD.FTSplCode
                    FROM TCNTPdtChkHD HD WITH(NOLOCK)
                    WHERE HD.FTIuhDocNo  = '$paData[tDocNo]' 
                      AND HD.FTBchCode   = '$paData[tBchCode]' 
                      AND ISNULL(TCNTPdtChkDT.FTPszName,'') = '' 
                      AND TCNTPdtChkDT.FTIuhDocNo = HD.FTIuhDocNo
                      AND TCNTPdtChkDT.FTBchCode = HD.FTBchCode
                ";
        $this->DB_EXECUTE($tSQL);
    }

    // Create By: Napat(Jame) 14/10/2022
    public function FSaMPASUpdDocNoToDTCut($paData){
        $tSQL = "UPDATE TCNTPdtChkDTCut WITH(ROWLOCK)
                 SET 
                    FTIuhDocNo      = '$paData[FTIuhDocNo]',
                    FDDateUpd       = '$paData[FDDateUpd]',
                    FTTimeUpd       = '$paData[FTTimeUpd]',
                    FTWhoUpd        = '$paData[FTWhoUpd]',
                    FDDateIns       = '$paData[FDDateIns]',
                    FTTimeIns       = '$paData[FTTimeIns]',
                    FTWhoIns        = '$paData[FTWhoIns]'
                 WHERE FTBchCode    = '$paData[FTBchCode]'
                   AND FTIuhDocNo     = '' ";
        $tReturnInsert = $this->DB_EXECUTE($tSQL);
        if($tReturnInsert == 'success'){
            $aDataResult = array(
                'nStaQuery'     => 1,
                'tStaMessage'   => '[FSaMPASUpdDocNoToDTCut] Update DocNo to TCNTPdtChkDTCut Success',
            );
        }else{
            $aDataResult = array(
                'nStaQuery'     => 99,
                'tStaMessage'   => '[FSaMPASUpdDocNoToDTCut] Can not update DocNo',
            );
        }
        return $aDataResult;
    }

    // Create By: Napat(Jame) 18/11/2022
    // อัพเดทวันที่-เวลา เอกสารใบรวมเพื่อให้ระบบคำนวณยอด เคลื่อนไหวหลังตรวจนับใหม่ได้ถูกต้อง
    public function FSxMPASUpdDateTimeB4Apv($paData){
        $tSQL1 = "  UPDATE TCNTPdtChkHD WITH(ROWLOCK)
                    SET FDDateUpd   = CONVERT(VARCHAR,GETDATE(),23),
                        FTTimeUpd   = CONVERT(VARCHAR,GETDATE(),24),
                        FTWhoUpd    = '$paData[FTWhoUpd]' 
                    WHERE FTIuhDocNo = '$paData[FTIuhDocNo]' AND FTBchCode = '$paData[FTBchCode]' ";
        $this->DB_EXECUTE($tSQL1);

        $tSQL2 = "  UPDATE TCNTPdtChkDT WITH(ROWLOCK)
                    SET FDDateUpd   = CONVERT(VARCHAR,GETDATE(),23),
                        FTTimeUpd   = CONVERT(VARCHAR,GETDATE(),24),
                        FTWhoUpd    = '$paData[FTWhoUpd]' 
                    WHERE FTIuhDocNo = '$paData[FTIuhDocNo]' AND FTBchCode = '$paData[FTBchCode]' ";
        $this->DB_EXECUTE($tSQL2);
    }

    // Function : เช็คว่าเอกสารนี้มี Gondola มากกว่า 1 location ไหม ?
    // Create By: Napat(Jame) 29/03/2023
    public function FSaMPASGetPdtReChkDT($paData){

        $tSQL  = " EXECUTE STP_PRCx_ReCHKSTK @ptFTIuhDocNo = '".$paData['FTIuhDocNo']."' ,@ptUserName = '".$paData['FTWhoUpd']."', @pnResult=0, @ptResultLog='' ";
        $tSQL .= " SELECT ROW_NUMBER() OVER(ORDER BY FNIudSeqNo) AS FNNewSeq,* FROM TCNTPdtReChkDT WHERE FTIuhDocNo = '".$paData['FTIuhDocNo']."' ";
        $aDataList = $this->DB_SELECT($tSQL);
        if( count($aDataList) > 0 ){
            $aDataResult = array(
                // 'tSQL'              => $tSQL,
                'aItems'            => $aDataList,
                'nStaQuery'         => 1,
                'tStaMessage'       => '[FSaMPASGetPdtReChkDT] พบสินค้ามีมากกว่า 1 Gondola จำนวน '.count($aDataList).' รายการ',
            );
        }else{
            $aDataResult = array(
                // 'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSaMPASGetPdtReChkDT] ไม่พบสินค้าที่มี Gondola มากกว่า 1',
            );
        }
        $this->FSxMPASWriteLog($aDataResult['tStaMessage']);
        // $this->FSxMPASWriteLog($tSQL);
        return $aDataResult;
    }

    // Function : แก้ไขจำนวนนับใหม่
    // Create By: Napat(Jame) 29/03/2023
    public function FSaMPASPdtReChkDTEditInLine($paData){
        $tSQL = "   UPDATE TCNTPdtReChkDT WITH(ROWLOCK) 
                    SET FCIudNewQty = $paData[FCIudNewQty]
                    WHERE FTIuhDocNo = '".$paData['FTIuhDocNo']."'
                      AND FNIudSeqNo = ".$paData['FNIudSeqNo'];
       
        $tResult = $this->DB_EXECUTE($tSQL);
        if($tResult == 'success'){
            $aDataResult = array(
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 1,
                'tStaMessage'   => 'PdtReChkDTEditInLine Success',
            );
        }else{
            $aDataResult = array(
                // 'tSQL'          => $tSQL,
                'nStaQuery'     => 900,
                'tStaMessage'   => 'PdtReChkDTEditInLine Fail',
            );
        }
        return $aDataResult;
    }

    // Function : อัพเดท Gondola จำนวนตรวจนับใหม่
    // Create By: Napat(Jame) 02/04/2023
    public function FSaMPASUpdGondolaToDT($paData,$tDocNoForReChkDT){
        $tSQL = "   UPDATE DT WITH(ROWLOCK)
                    SET DT.FCIudUnitC1      = ISNULL(RECHK.FCIudNewQty,0),
                        DT.FCIudQtyC1       = ISNULL(RECHK.FCIudNewQty,0) * ISNULL(DT.FCIudStkFac,0),
                        DT.FCIudUnitC2      = 0,
                        DT.FCIudQtyBal      = ISNULL(RECHK.FCIudNewQty,0) * ISNULL(DT.FCIudStkFac,0),
                        DT.FCIudQtyDiff     = ISNULL(RECHK.FCIudNewQty,0) * ISNULL(DT.FCIudStkFac,0) - DT.FCIudWahQty,
                        DT.FCIudDisAvg      = ISNULL(RECHK.FCIudNewQty,0) * ISNULL(DT.FCIudStkFac,0),
                        DT.FDIudChkDate     = CONVERT(DATE,GETDATE()),
                        DT.FTIudChkTime     = CONVERT(VARCHAR, GETDATE(), 108)
                    FROM TCNTPdtChkDT DT 
                    INNER JOIN TCNTPdtReChkDT RECHK ON DT.FTIudStkCode = RECHK.FTIudStkCode
                    WHERE DT.FTIuhDocNo    = '".$paData['FTIuhDocNo']."'
                      AND RECHK.FTIuhDocNo = '".$tDocNoForReChkDT."' ";
        $this->DB_EXECUTE($tSQL);
    }

    // Create By: Napat(Jame) 05/04/2023
    public function FSaMPASChkNewQty($paData){
        $tSQL = "   SELECT FTIudStkCode,FTPdtName FROM TCNTPdtReChkDT WITH(NOLOCK) 
                    WHERE FTIuhDocNo = '".$paData['FTIuhDocNo']."' 
                      AND FCIudNewQty IS NULL ";
        $aDataList = $this->DB_SELECT($tSQL);
        if( count($aDataList) > 0 ){
            $aDataResult = array(
                'aItems'            => $aDataList,
                // 'tSQL'              => $tSQL,
                'nStaQuery'         => 1,
                'tStaMessage'       => 'มีสินค้าที่ยังไม่ได้ระบุจำนวนนับ กรุณาระบุจำนวนนับให้ครบทุกรายการ',
            );
        }else{
            $aDataResult = array(
                'aItems'            => array(),
                // 'tSQL'              => $tSQL,
                'nStaQuery'         => 99,
                'tStaMessage'       => '[FSaMPASChkNewQty] สินค้า Gondola กำหนดจำนวนนับใหม่ทุกรายการแล้ว',
            );
        }
        return $aDataResult;
    }

}

?>