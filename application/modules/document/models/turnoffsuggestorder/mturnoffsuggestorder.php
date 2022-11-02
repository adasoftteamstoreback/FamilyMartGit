<?php

class mturnoffsuggestorder extends Database{

    
    public function __construct(){
        parent::__construct();
    }

    //เขียนไฟล์ : หน้าจอปิดคำสั่งซื้อที่แนะนำ
    public function FSxWriteLogByPage($ptInfomation){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptInfomation."\n";
        $tFileName   = 'application/logs/Log_'.'TOS_'.date('Ymd').'.txt';
        $file = fopen("$tFileName","a+");
        fwrite($file,$tLogData);
        fclose($file);
    }

    //Insert PDT to [temp]
    public function FSxMTSOInsertPDT($ptParameter,$tDocumentID,$ptType){

        try {
            $aGetBranch     = getBranch();
            if($tDocumentID == '' || $tDocumentID == 'null' || $tDocumentID == null){
                $tFormatCode    = generateCode('TCNTPdtSuggestHD','FTPthDocNo');
            }else{
                $tFormatCode    = $tDocumentID;
            }
            // //insert DT
            $tDatabaseDT    = "TSPdtSuggestDT";
            if(!empty($ptParameter)){

                //SELECT เอา seq มาก่อน
                $tSQLseq        = "SELECT TOP 1 [FNPtdSeqNo] FROM TSPdtSuggestDT order by FNPtdSeqNo DESC";
                $tResultseq     = $this->DB_SELECT($tSQLseq);
                if(empty($tResultseq)){
                    $nSeq = 0;
                }else{
                    $nSeq = $tResultseq[0]['FNPtdSeqNo'];
                }

                if($ptType == 'PDT'){
                    //LOOP Insert for browser :: PDT
                    for($i=0; $i<count($ptParameter); $i++){
                        
                        $tCheckPDT = $this->FSxMTSOCheckProduct($ptParameter[$i]['FTPdtCode']);
                        if(empty($tCheckPDT)){
                            $tStartDate = date('Y-m-d');
                            $tEndDate   = date('Y-m-d');
                        }else{
                            $tStartDate = date('Y-m-d',strtotime($tCheckPDT[0]['FDPdtEnddate'].'+1 day'));
                            $tEndDate   = date('Y-m-d',strtotime($tCheckPDT[0]['FDPdtEnddate'].'+2 day'));
                        }

                        $nSeq =  $nSeq + 1;
                        $aDataInsertDT  = array(
                            'FTBchCode'         =>  $aGetBranch['FTBchCode'],
                            'FTPthDocNo'        =>  $tFormatCode,
                            'FNPtdSeqNo'        =>  $nSeq,
                            'FTPdtCode'         =>  $ptParameter[$i]['FTPdtCode'],
                            'FTPdtName'         =>  $ptParameter[$i]['FTPdtName'],
                            'FTPdtBarCode'      =>  $ptParameter[$i]['FTPdtBarCode'],
                            'FDPdtStartdate'    =>  $tStartDate,
                            'FDPdtEnddate'      =>  $tEndDate,
                            'FDDateIns'         =>  date('Y-m-d'),
                            'FTTimeIns'         =>  date('H:i:s'),
                            'FTWhoIns'          =>  $_SESSION["SesUsername"],
							
							'FDDateUpd'			=> date('Y-m-d'),
							'FTTimeUpd'			=> date('H:i:s'),
							'FTWhoUpd'			=> $_SESSION["SesUsername"]
                        );
                        $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                    }
                }else if($ptType == 'PDTBarcode'){
                    //LOOP Insert for input :: Barcode or PDT
                    $tSQL = "SELECT TOP 1 
                                TCNMPdt.FTPdtCode, 
                                TCNMPdt.FTPdtName,
                                TCNMPdtBar.FTPdtBarCode ,
                                TCNMPdt.FTPdtStkCode 
                            FROM TCNMPdt,TCNMPdtBar WITH (NOLOCK) 
                            WHERE (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                            AND (TCNMPdt.FTPdtCode='$ptParameter' OR FTPdtBarCode='$ptParameter') 
                            /*AND FTPdtStaAudit =1 */
                            AND FTPdtStkCode = (
                                SELECT TOP 1 PDT.FTPdtStkCode  FROM TCNMPdt PDT
                                INNER JOIN ( SELECT TOP 1 PDT.FTPdtStkCode 
                                            FROM TCNMPdt PDT 
                                            INNER JOIN TCNMPdtBar PDB
                                            ON PDT.FTPdtCode = PDB.FTPdtCode
                                            WHERE PDT.FTPdtCode = '$ptParameter' OR PDB.FTPdtBarCode = '$ptParameter' ) BAR
                                            ON PDT.FTPdtStkCode  = BAR.FTPdtStkCode 
                                            AND PDT.FTPdtStaAlwBuy = 1 )
                            AND FTPdtStaActive ='1' 
                            /*AND TCNMPdt.FTPDTStaAlwBuy = '1'*/
                            AND FTPdtType IN ('1','4') 
                            AND (TCNMPdt.FTPdtStaSet IN('1','2','3')) 
                            AND FDPdtPriAffect <= GETDATE() 
                            ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode ";
                    $oQuery = $this->DB_SELECT($tSQL);
                    if (!empty($oQuery)) {
                        $tPDTSTKCode = $oQuery[0]['FTPdtStkCode'];
                        $tSQLAlwBuy = "SELECT 
                                        PDT.FTPdtCode, 
                                        PDT.FTPdtName,
                                        PDB.FTPdtBarCode
                                        FROM TCNMPdt PDT 
                                        INNER JOIN TCNMPdtBar PDB ON PDT.FTPdtCode = PDB.FTPdtCode
                                        WHERE PDT.FTPdtStkCode = '$tPDTSTKCode'
                                        AND PDT.FTPdtStaAlwBuy = 1";
                        $oQueryAlwBuy = $this->DB_SELECT($tSQLAlwBuy);

                        $tCheckPDTBarcode = $this->FSxMTSOCheckProduct($oQueryAlwBuy[0]['FTPdtCode']);
                        if(empty($tCheckPDTBarcode)){
                            $tStartDateBarcode = date('Y-m-d');
                            $tEndDateBarcode   = date('Y-m-d');
                        }else{
                            return array('DataDuplicate',$oQueryAlwBuy[0]['FTPdtCode']);
                            exit;
                            $tStartDateBarcode = date('Y-m-d',strtotime($tCheckPDTBarcode[0]['FDPdtEnddate'].'+1 day'));
                            $tEndDateBarcode   = date('Y-m-d',strtotime($tCheckPDTBarcode[0]['FDPdtEnddate'].'+2 day'));
                        }

                        $nSeq =  $nSeq + 1;
                        $aDataInsertDT  = array(
                            'FTBchCode'         => $aGetBranch['FTBchCode'],
                            'FTPthDocNo'        => $tFormatCode,
                            'FNPtdSeqNo'        => $nSeq,
                            'FTPdtCode'         => $oQueryAlwBuy[0]['FTPdtCode'],
                            'FTPdtName'         => $oQueryAlwBuy[0]['FTPdtName'],
                            'FTPdtBarCode'      => $oQueryAlwBuy[0]['FTPdtBarCode'],
                            'FDPdtStartdate'    => $tStartDateBarcode,
                            'FDPdtEnddate'      => $tEndDateBarcode,
                            'FDDateIns'         => date('Y-m-d'),
                            'FTTimeIns'         => date('H:i:s'),
                            'FTWhoIns'          => $_SESSION["SesUsername"],
							'FDDateUpd'			=> date('Y-m-d'),
							'FTTimeUpd'			=> date('H:i:s'),
							'FTWhoUpd'			=> $_SESSION["SesUsername"]
                        );
                        $tResult    = $this->DB_INSERT($tDatabaseDT,$aDataInsertDT);
                    }else{
                        return array('nodata');
                    }
                }

                if($tResult == 'success'){

                    //ค้นหาเอกสารก่อน ถ้าไม่มี ต้อง insert HD ด้วย
                    $tFindDocument = "SELECT HD.FTPthDocNo FROM TCNTPdtSuggestHD HD 
                                    WHERE HD.FTPthDocNo = '$tFormatCode' ";
                    $oFindDocument = $this->DB_SELECT($tFindDocument);
                    if(empty($oFindDocument)){
                        $_SESSION['TurnoffFirtInsert'] = 'TurnoffFirtInsert';
                        //ถ้าไม่มีเอกสารต้องสร้าง HD ด้วย
                        $tDatabase    = "TCNTPdtSuggestHD";
                        $aDataInsert  = array(
                            'FTBchCode'         => $aGetBranch['FTBchCode'],
                            'FTPthDocNo'        => $tFormatCode,
                            'FTPthDocType'      => '1',
                            'FDPthDocDate'      => date('Y-m-d'),
                            'FTPthDocTime'      => date('H:i:s'),
                            'FTPthApvCode'      => null,
                            'FTPthStaDoc'       => '1',
                            'FTPthStaPrcDoc'    => null,    
                            'FDDateIns'         => date('Y-m-d'),
                            'FTTimeIns'         => date('H:i:s'),
                            'FTWhoIns'          =>  $_SESSION["SesUsername"],
                            'FDDateUpd'			=> date('Y-m-d'),
                            'FTTimeUpd'			=> date('H:i:s'),
                            'FTWhoUpd'			=> $_SESSION["SesUsername"],
                            'FTPthApvCode'		=> $_SESSION["SesUsercode"]
                        );
                        $this->DB_INSERT($tDatabase,$aDataInsert);
                    }

                    $this->FSxMTSOSaveBeforeApprove($tFormatCode);
                    return array('success');
                }else{
                    return $tResult;
                }
            }else{
                return array('nodata');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage($e->getMessage());
        }
        
    }

    //Move DT To Temp
    public function FStMTSOInsertDTForSelectTemp(){
        //move จาก temp to DT ทุกครั้งที่สร้าง
        $tSQL = 'INSERT INTO TCNTPdtSuggestDT (
            FTBchCode,
            FTPthDocNo,
            FNPtdSeqNo,
            FTPdtCode,
            FTPdtName,
            FDPdtStartdate,
            FDPdtEnddate,
            FDDateUpd,
            FTTimeUpd,
            FTWhoUpd,
            FDDateIns,
            FTTimeIns,
            FTWhoIns
        )
        SELECT 
            FTBchCode,
            FTPthDocNo,
            FNPtdSeqNo,
            FTPdtCode,
            FTPdtName,
            FDPdtStartdate,
            FDPdtEnddate,
            FDDateUpd,
            FTTimeUpd,
            FTWhoUpd,
            FDDateIns,
            FTTimeIns,
            FTWhoIns
        FROM
        TSPdtSuggestDT';
        $tResult    = $this->DB_EXECUTE($tSQL);
    }

    //Select [temp] DT
    public function FSxMTSOSelectPDT($paData){
        try {
            $aGetBranch     = getBranch();
            $tFormatCode    = generateCode('TCNTPdtSuggestHD','FTPthDocNo');
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);


            //Sort by column
            if($paData['tSortBycolumn'][0] == ''){
                $tOrderBy =  'FNPtdSeqNo ASC';
            }else{
                $tOrderBy =  $paData['tSortBycolumn'][0] . ' ' . $paData['tSortBycolumn'][1];
            }

            $tSearchList = $paData['tSearchAll'];

            $tSQL           = "SELECT c.* FROM( SELECT  ROW_NUMBER() OVER(ORDER BY ";
            $tSQL           .= " $tOrderBy ";
            $tSQL           .= ") AS rtRowID,* FROM";
            $tSQL           .= "(SELECT 
                                    DISTINCT SDT.FTBchCode , 
                                    SDT.FTPthDocNo , 
                                    SDT.FNPtdSeqNo ,
                                    SDT.FTPdtCode ,
                                    SDT.FTPdtName ,
                                    SDT.FTPdtBarCode ,
                                    CONVERT(varchar(10),SDT.FDPdtStartdate,121) AS FDPdtStartdate,
                                    CONVERT(varchar(10),SDT.FDPdtEnddate,121) AS FDPdtEnddate ";
            $tSQL           .= " FROM [TSPdtSuggestDT] SDT WITH (NOLOCK)  
                                 LEFT JOIN TCNMPdtBar CBAR ON SDT.FTPdtCode = CBAR.FTPdtCode
                                 WHERE 1=1 ";
            
			//if else เช็ค mssql หรือ sqlsrv
			$tTextName = $this->ConvertTIS620($tSearchList);
            if ($tSearchList != ''){
                $tSQL .= " AND (SDT.FTBchCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPdtBarCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPthDocNo LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPdtCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPdtName LIKE '%$tTextName%' ";
                $tSQL .= " OR SDT.FDPdtStartdate LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FDPdtEnddate LIKE '%$tSearchList%')";
            }
            
            $tSQL           .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $oQuery         = $this->DB_SELECT($tSQL);

            //echo $tSQL;

            if (!empty($oQuery) > 0) {
                $oList      = $oQuery;
                $aFoundRow  = $this->FSnMTSOGetPageAll($tSearchList);
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

    //Select [temp] count DT
    public function FSnMTSOGetPageAll($ptSearchList){
        
        $tSQL = "SELECT COUNT (SDT.FTPthDocNo) AS counts
                 FROM TSPdtSuggestDT SDT
                 WHERE 1=1 ";
				 
        //if else เช็ค mssql หรือ sqlsrv
		$tTextName = $this->ConvertTIS620($ptSearchList);
        if($ptSearchList != ''){
            $tSQL .= " AND (SDT.FTBchCode LIKE '%$ptSearchList%'";
            $tSQL .= " OR SDT.FTPthDocNo LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FTPdtCode LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FTPdtName LIKE '%$tTextName%' ";
            $tSQL .= " OR SDT.FDPdtStartdate LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FDPdtEnddate LIKE '%$ptSearchList%')";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Select [master] HD
    public function FSxMTSOSelectPDTHD($paData){
        try {
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $tSQL           = "SELECT c.* FROM(
                                SELECT  ROW_NUMBER() OVER(ORDER BY FTPthDocNo DESC) AS rtRowID,* FROM
                                (SELECT 
                                    SDT.FTBchCode,
                                    SDT.FTPthDocNo,
                                    SDT.FTPthDocType,
									CONVERT(varchar(10),SDT.FDPthDocDate,121) AS FDPthDocDate,
                                    SDT.FTPthDocTime,
                                    SDT.FTPthApvCode,
                                    SDT.FTPthStaDoc,
                                    SDT.FTPthStaPrcDoc,
                                    SDT.FDDateUpd,
                                    SDT.FTTimeUpd,
                                    SDT.FTWhoUpd,
                                    SDT.FDDateIns,
                                    SDT.FTTimeIns,
                                    SDT.FTWhoIns
                                    FROM [TCNTPdtSuggestHD] SDT WHERE 1=1  AND SDT.FTBchCode=(SELECT FTBchCode FROM TCNMComp (NOLOCK)) ";
            $tSQL           .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";

            $oQuery         = $this->DB_SELECT($tSQL);
            if (!empty($oQuery) > 0) {
                $oList      = $oQuery;
                $tSearchList = '';
                $aFoundRow  = $this->FSnMTSOGetPageAllHD($tSearchList);
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

    //Select [master] count HD
    public function FSnMTSOGetPageAllHD(){
        $tSQL = "SELECT COUNT (SDT.FTBchCode) AS counts
            FROM TCNTPdtSuggestHD SDT
            WHERE 1=1 AND SDT.FTBchCode=(SELECT FTBchCode FROM TCNMComp (NOLOCK))  ";

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Select [master] DT
    public function FSxMTSOSelectMasterPDT($paData){
        try {
            $tDocumentNo    = $paData['tDocumentID'];
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $tSQL           = "SELECT c.* FROM(
                                SELECT  ROW_NUMBER() OVER(ORDER BY FTBchCode ASC) AS rtRowID,* FROM
                                (SELECT 
                                    SDT.FTBchCode,
                                    SDT.FTPthDocNo,
                                    SDT.FNPtdSeqNo,
                                    SDT.FTPdtCode,
                                    SDT.FTPdtName,
                                    SDT.FDPdtStartdate,
                                    SDT.FDPdtEnddate,
                                    BSDT.FTPdtBarCode
                                    FROM [TCNTPdtSuggestDT] SDT 
                                    LEFT JOIN [TCNMPdtBar] BSDT ON SDT.FTPdtCode = BSDT.FTPdtCode
                                    WHERE 1=1 
                                    AND SDT.FTPthDocNo = '$tDocumentNo' ";
            
            $tSearchList = $paData['tSearchAll'];
            if ($tSearchList != null || $tSearchList != ''){
                $tSQL .= " AND (SDT.FTBchCode LIKE '%$tSearchList%'";
                $tSQL .= " OR SDT.FTPthDocNo LIKE '%$tSearchList%' ";
                $tSQL .= " OR BSDT.FTPdtBarCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPdtCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FTPdtName LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FDPdtStartdate LIKE '%$tSearchList%' ";
                $tSQL .= " OR SDT.FDPdtEnddate LIKE '%$tSearchList%')";
            }
            
            $tSQL           .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $oQuery         = $this->DB_SELECT($tSQL);
            if (!empty($oQuery) > 0) {
                $oList      = $oQuery;
                $aFoundRow  = $this->FSnMTSOGetPageAllMasterDT($tSearchList,$tDocumentNo);
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

    //Select [master] count DT
    public function FSnMTSOGetPageAllMasterDT($ptSearchList,$tDocumentNo){
        $tSQL = "SELECT COUNT (SDT.FTPthDocNo) AS counts
                    FROM TCNTPdtSuggestDT SDT
                    LEFT JOIN [TCNMPdtBar] BSDT ON SDT.FTPdtCode = BSDT.FTPdtCode
                    WHERE 1=1 AND SDT.FTPthDocNo = '$tDocumentNo' ";
        
        if($ptSearchList != null || $ptSearchList != ''){
            $tSQL .= " AND (SDT.FTBchCode LIKE '%$ptSearchList%'";
            $tSQL .= " OR SDT.FTPthDocNo LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FTPdtCode LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FTPdtName LIKE '%$ptSearchList%' ";
            $tSQL .= " OR BSDT.FTPdtBarCode LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FDPdtStartdate LIKE '%$ptSearchList%' ";
            $tSQL .= " OR SDT.FDPdtEnddate LIKE '%$ptSearchList%')";
        }

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Select Check count in [temp]
    public function FSxMTSOSelectCheckTemp(){
        $tFiled     = "FTPthDocNo";
        $tTable     = "TSPdtSuggestDT";
        $tResult    = $this->DB_SELECTCOUNTBYCOLUMN($tFiled,$tTable);
        return $tResult;
    }

    //Delete [temp]
    public function FSxMTSODeletePDT($paData){
        try {
            $pnSeq      = $paData['FNPtdSeqNo'];
            $pnPdtcode  = $paData['FTPdtCode'];
            $tDatabase          = "TSPdtSuggestDT";
            $aDataDeleteWHERE   = array(
                'FNPtdSeqNo'    => $pnSeq ,
                'FTPdtCode'     => $pnPdtcode 
            );
            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
           
            //Update sequence
            $tUpdateSql = 'UPDATE TSPdtSuggestDT 
                            SET FNPtdSeqNo = SeqNew.rtRowID
                            FROM (
                                SELECT c.* FROM( 
                                        SELECT  ROW_NUMBER() OVER(ORDER BY FNPtdSeqNo) AS rtRowID, FNPtdSeqNo , FTPdtCode FROM TSPdtSuggestDT 
                                    ) as c 
                                ) SeqNew
                            WHERE 
                                SeqNew.FNPtdSeqNo = TSPdtSuggestDT.FNPtdSeqNo';
            $this->DB_EXECUTE($tUpdateSql);

            $this->FSxWriteLogByPage("[FSxMTSODeletePDT] ลบข้อมูลสินค้า : ".$pnPdtcode." และมีการเรียง Seq ใหม่");
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Delete [master]
    public function FSxMTSODeletePDTinMasterDT($paData){
        try {
            $pnSeq              = $paData['FNPtdSeqNo'];
            $pnPdtcode          = $paData['FTPdtCode'];
            $ptDocno            = $paData['FTPthDocNo'];
            $tDatabase          = "TCNTPdtSuggestDT";
            $aDataDeleteWHERE   = array(
                'FNPtdSeqNo'    => $pnSeq ,
                'FTPdtCode'     => $pnPdtcode ,
                'FTPthDocNo'    => $ptDocno
            );
            $bConfirm           = true;
            $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Save
    public function FSxMTSOSavePDT($tDocumentID){
        try {
            $aGetBranch     = getBranch();
            $tFormatCode    = generateCode('TCNTPdtSuggestHD','FTPthDocNo');

            //ลบจาก master ก่อน
            if($tDocumentID == '' || $tDocumentID == null || $tDocumentID == 'null'){
                $tFormatCode        = $tFormatCode;
            }else{
                $tFormatCode        = $tDocumentID;
                $tDatabase          = "TCNTPdtSuggestDT";
                $aDataDeleteWHERE   = array(
                    'FTPthDocNo'    => $tDocumentID
                );
                $bConfirm           = true;
                $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            }
            
            if($tFormatCode == 'SGBCHYYMM-######'){
                exit;
            }
           
            //Insert HD
			$tSQL               = "SELECT TOP 1 FTPthDocNo FROM TCNTPdtSuggestHD WHERE FTPthDocNo = '$tFormatCode' ";
			$tResultcheck      = $this->DB_SELECT($tSQL);
			if(empty($tResultcheck)){
				$tDatabase    = "TCNTPdtSuggestHD";
				$aDataInsert  = array(
					'FTBchCode'         => $aGetBranch['FTBchCode'],
					'FTPthDocNo'        => $tFormatCode,
					'FTPthDocType'      => '1',
					'FDPthDocDate'      => date('Y-m-d'),
					'FTPthDocTime'      => date('H:i:s'),
					'FTPthApvCode'      => null,
					'FTPthStaDoc'       => '1',
					'FTPthStaPrcDoc'    => null,    
					'FDDateIns'         => date('Y-m-d'),
					'FTTimeIns'         => date('H:i:s'),
					'FTWhoIns'          =>  $_SESSION["SesUsername"],
					'FDDateUpd'			=> date('Y-m-d'),
					'FTTimeUpd'			=> date('H:i:s'),
					'FTWhoUpd'			=> $_SESSION["SesUsername"],
					'FTPthApvCode'		=> $_SESSION["SesUsercode"]
				);
				$this->DB_INSERT($tDatabase,$aDataInsert);
			}
			
            //Insert DT
            $tSQL = 'INSERT INTO TCNTPdtSuggestDT (
                    FTBchCode,
                    FTPthDocNo,
                    FNPtdSeqNo,
                    FTPdtCode,
                    FTPdtName,
                    FDPdtStartdate,
                    FDPdtEnddate,
                    FDDateUpd,
                    FTTimeUpd,
                    FTWhoUpd,
                    FDDateIns,
                    FTTimeIns,
                    FTWhoIns
                )
            SELECT 
                    FTBchCode,
                    FTPthDocNo,
                    FNPtdSeqNo,
                    FTPdtCode,
                    FTPdtName,
                    FDPdtStartdate,
                    FDPdtEnddate,
                    FDDateUpd,
                    FTTimeUpd,
                    FTWhoUpd,
                    FDDateIns,
                    FTTimeIns,
                    FTWhoIns
            FROM
                TSPdtSuggestDT';
            $tResult    = $this->DB_EXECUTE($tSQL);


            /*$tDatabase          = "TSPdtSuggestDT";
            $aDataDeleteWHERE   = array(
                'FTBchCode'     => $aGetBranch['FTBchCode'],
                'FTPthDocNo'    => $tFormatCode
            );
            $bConfirm           = true;*/
            //$tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);
            
            return $tFormatCode;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Delete Temp
    public function FSxMTSODeleteTemp(){
        $tSQL       = 'DELETE FROM TSPdtSuggestDT';
        $tResult    = $this->DB_EXECUTE($tSQL);
    }

    //Select Doc HD 1 record none approve
    public function FSxMTSOSelectNoneApprove(){
        $tSQL = "SELECT TOP 1 FTPthDocNo FROM TCNTPdtSuggestHD where FTPthStaPrcDoc = '' order by FTPthDocNo DESC";
        $oQuery = $this->DB_SELECT($tSQL);
        if(empty($oQuery)){
            return 'false';
        }else{
            return $oQuery[0]['FTPthDocNo'];
        }
    }

    //Move master to temp
    public function FSxMTSOMoveMastertoTemp($paData){
        try {
            //Insert Master to Temp
            $tDocno = $paData['tDocumentID'];
            $tSQL = "INSERT INTO TSPdtSuggestDT (
                    FTBchCode,
                    FTPthDocNo,
                    FNPtdSeqNo,
                    FTPdtCode,
                    FTPdtName,
                    FDPdtStartdate,
                    FDPdtEnddate,
                    FDDateUpd,
                    FTTimeUpd,
                    FTWhoUpd,
                    FDDateIns,
                    FTTimeIns,
                    FTWhoIns,
                    FTPdtBarCode
                )
            SELECT 
                    DT.FTBchCode,
                    DT.FTPthDocNo,
                    DT.FNPtdSeqNo,
                    DT.FTPdtCode,
                    DT.FTPdtName,
                    DT.FDPdtStartdate,
                    DT.FDPdtEnddate,
                    DT.FDDateUpd,
                    DT.FTTimeUpd,
                    DT.FTWhoUpd,
                    DT.FDDateIns,
                    DT.FTTimeIns,
                    DT.FTWhoIns,
                    (SELECT TOP 1 FTPdtBarCode FROM TCNMPdtBar BAR WITH (NOLOCK) WHERE BAR.FTPdtCode = DT.FTPdtCode)
            FROM
                TCNTPdtSuggestDT DT
            WHERE FTPthDocNo = '$tDocno' ";
            $tResult    = $this->DB_EXECUTE($tSQL);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    //Approve
    public function FSxMTSOApprove($tDocumentID){
        $tDatabase    = "TCNTPdtSuggestHD";
        $aDataUpdate  = array(
            'FTPthStaPrcDoc' => '1'
        );
        $aDataWhere = array(
            'FTPthDocNo'     => $tDocumentID
        );
        $tResult    = $this->DB_UPDATEWHERE($tDatabase,$aDataUpdate,$aDataWhere);
        $this->FSxWriteLogByPage("[FSxMTSOApprove] อนุมัติเอกสาร FTPthStaPrcDoc = 1 เลขที่เอกสาร : ".$tDocumentID);

    }

    //Select HD เอา documentID
    public function FSxMTSOSelectHD($tDocumentID){
        $tSQL = "SELECT CONVERT(VARCHAR(10),FDPthDocDate,103) as AFDPthDocDate , * FROM TCNTPdtSuggestHD SDT WHERE 1=1 
                AND SDT.FTPthDocNo = '$tDocumentID' ";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Update in line
    public function FSxMTSOUpdateInLinePDT($paData){
        try {
            $nSeq           = $paData['nSeq'];
            $nPDTCode       = $paData['nPDTCode'];
            $tStartDate     = $paData['tStartDate'];
            $tEndDate       = $paData['tEndDate'];
            $tBarCode       = $paData['nBarCode'];
            //LOOP Insert for input :: Barcode or PDT
            $tSQL = "SELECT TOP 1 
                        TCNMPdt.FTPdtCode, 
                        TCNMPdt.FTPdtName,
                        TCNMPdtBar.FTPdtBarCode ,
                        TCNMPdt.FTPdtStkCode 
                    FROM TCNMPdt,TCNMPdtBar WITH (NOLOCK) 
                    WHERE (TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode) 
                    AND (TCNMPdt.FTPdtCode='$nPDTCode' OR FTPdtBarCode='$nPDTCode') 
                    --AND FTPdtStaAudit ='1' 
                    AND FTPdtStkCode = (
                        SELECT TOP 1 PDT.FTPdtStkCode  FROM TCNMPdt PDT
                        INNER JOIN ( SELECT TOP 1 PDT.FTPdtStkCode 
                                    FROM TCNMPdt PDT 
                                    INNER JOIN TCNMPdtBar PDB
                                    ON PDT.FTPdtCode = PDB.FTPdtCode
                                    WHERE PDT.FTPdtCode = '$nPDTCode' OR PDB.FTPdtBarCode = '$nPDTCode' ) BAR
                                    ON PDT.FTPdtStkCode  = BAR.FTPdtStkCode 
                                    AND PDT.FTPdtStaAlwBuy = 1 )
                    AND FTPdtStaActive ='1' 
                    AND FTPdtType IN ('1','4') 
                    AND (TCNMPdt.FTPdtStaSet IN('1','2','3')) 
                    AND FDPdtPriAffect <= GETDATE() 
                    ORDER BY TCNMPdtBar.FDPdtPriAffect DESC , TCNMPdtBar.FTPdtCode ";
            $oQuery = $this->DB_SELECT($tSQL);

            if (!empty($oQuery)) {
                $tDatabase    = "TSPdtSuggestDT";
                $aDataWhere = array(
                    'FNPtdSeqNo'     => $nSeq 
                );

                $tPDTSTKCode = $oQuery[0]['FTPdtStkCode'];
                $tSQLAlwBuy = "SELECT 
                                PDT.FTPdtCode, 
                                PDT.FTPdtName,
                                PDB.FTPdtBarCode
                                FROM TCNMPdt PDT 
                                INNER JOIN TCNMPdtBar PDB ON PDT.FTPdtCode = PDB.FTPdtCode
                                WHERE PDT.FTPdtStkCode = '$tPDTSTKCode'
                                -- AND (PDT.FTPdtCode = '$nPDTCode' 
                                -- OR PDB.FTPdtBarCode = '$nPDTCode')
                                AND PDT.FTPdtStaAlwBuy = 1";

                $oQueryAlwBuy = $this->DB_SELECT($tSQLAlwBuy);
                $tFormatNewStartDate    = explode("-",$tStartDate); 
                $tFormatNewEndDate      = explode("-",$tEndDate); 
               
                //$tFormatNewStartDate[1].'-'.$tFormatNewStartDate[0].'-'.$tFormatNewStartDate[2]
                //$tFormatNewEndDate[1].'-'.$tFormatNewEndDate[0].'-'.$tFormatNewEndDate[2],

                $aDataUpdate = array(
                    'FTPdtCode'     =>  $oQueryAlwBuy[0]['FTPdtCode'],
                    'FTPdtBarCode'  =>  $oQueryAlwBuy[0]['FTPdtBarCode'],
                    'FTPdtName'     =>  $oQueryAlwBuy[0]['FTPdtName'],
                    'FDPdtStartdate'=>  $tFormatNewStartDate[1].'-'.$tFormatNewStartDate[0].'-'.$tFormatNewStartDate[2],
                    'FDPdtEnddate'  =>  $tFormatNewEndDate[1].'-'.$tFormatNewEndDate[0].'-'.$tFormatNewEndDate[2],
                    'FDDateUpd'     =>  date('Y-m-d'),
                    'FTTimeUpd'     =>  date('H:i:s'),
                    'FTWhoUpd'      =>  $_SESSION["SesUsername"]
                );

                if($tFormatNewStartDate[0] > 31){
                    return false;
                }else{
                    $tResult    = $this->DB_UPDATEWHERE($tDatabase,$aDataUpdate,$aDataWhere);
                }

                $aReturnview = array(
                    'FTPdtBarCode'  =>  $oQueryAlwBuy[0]['FTPdtBarCode'],
                    'FTPdtCode'     =>  $oQueryAlwBuy[0]['FTPdtCode'],
                    'FTPdtName'     =>  $oQueryAlwBuy[0]['FTPdtName'],
                    'FDPdtStartdate'=>  $tStartDate,
                    'FDPdtEnddate'  =>  $tEndDate,
                );
                return $aReturnview;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMTSOUpdateCaseCanceldocument] ".$e->getMessage());
        }
    }

    //Update case cancel document
    public function FSxMTSOUpdateCaseCanceldocument($ptDocno){
        try {
            $tDatabase    = "TCNTPdtSuggestHD";
            $aDataWhere = array(
                'FTPthDocNo'     => $ptDocno 
            );
            $aDataUpdate = array(
                'FTPthStaPrcDoc'   => 3,
                'FDDateUpd'     => date('Y-m-d'),
                'FTTimeUpd'     => date('H:i:s'),
                'FTWhoUpd'      =>  $_SESSION["SesUsername"]
            );

            $tResult    = $this->DB_UPDATEWHERE($tDatabase,$aDataUpdate,$aDataWhere);
            $this->FSxWriteLogByPage("[FSxMTSOUpdateCaseCanceldocument] อัพเดทเอกสารให้มีสถานะยกเลิก FTPthStaPrcDoc = '3' เลขที่เอกสาร : ".$ptDocno);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMTSOUpdateCaseCanceldocument] ".$e->getMessage());
        }
    }

    //Update case rabbit fail
    public function FSxMTSOUpdateApproveRabbitFail($ptDocno){
        try {
            $tDatabase    = "TCNTPdtSuggestHD";
            $aDataWhere = array(
                'FTPthDocNo'     => $ptDocno 
            );
            $aDataUpdate = array(
                'FTPthStaPrcDoc'=> '',
                'FDDateUpd'     => date('Y-m-d'),
                'FTTimeUpd'     => date('H:i:s'),
                'FTWhoUpd'      => $_SESSION["SesUsername"]
            );

            $tResult    = $this->DB_UPDATEWHERE($tDatabase,$aDataUpdate,$aDataWhere);
            $this->FSxWriteLogByPage("[FSxMTSOUpdateApproveRabbitFail] อนุมัติไม่สำเร็จ กลับมาอัพเดทเอกสารให้มีสถานะไม่ใช้งาน FTPthStaPrcDoc = '' เลขที่เอกสาร : ".$ptDocno);
            return $tResult;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMTSOUpdateApproveRabbitFail] ".$e->getMessage());
        }
    }

    //Check date Duplicate
    public function FSxMTSOCheckdateDuplicate(){
        try {
            $tSQL = "SELECT FTPdtCode , CONVERT(varchar(10),SDT.FDPdtStartdate,121) as FDPdtStartdate , CONVERT(varchar(10),SDT.FDPdtEnddate,121) as FDPdtEnddate FROM TSPdtSuggestDT SDT ";
            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->FSxWriteLogByPage("[FSxMTSOCheckdateDuplicate] ".$e->getMessage());
        }
    }

    //Update HD cancle - and remove temp
    public function FSxMTSOSelectUpdateAndDeleteTemp(){
        //Select มาก่อน
        /*$tSQL = "SELECT TOP 1 FTPthDocNo FROM TCNTPdtSuggestHD order by FTPthDocNo DESC";
        $oQuery = $this->DB_SELECT($tSQL);*/

        //Update ฟิวส์ เป็น 3 cancle
        /*$tDatabase    = "TCNTPdtSuggestHD";
        $aDataWhere = array(
            'FTPthDocNo'     => $oQuery[0]['FTPthDocNo']
        );
        $aDataUpdate = array(
            'FTPthStaPrcDoc'    => 3,
            'FDDateUpd'         => date('Y-m-d'),
            'FTTimeUpd'         => date('H:i:s'),
            'FTWhoUpd'          =>  $_SESSION["SesUsername"]
        );
        $tResult    = $this->DB_UPDATEWHERE($tDatabase,$aDataUpdate,$aDataWhere);*/

        //Delete Temp
        //$this->FSxMTSODeleteTemp();

    }

    //Check Data Duplicate in Temp (ห้ามข้อมูลซ้ำ วันที่ ซ้ำก็ห้าม)
    public function FSxMTSOCheckDataDuplicate($paData){
        $nSeq           = $paData['nSeq'];
        $nPDTCode       = $paData['nPDTCode'];
        $tStartDate     = $paData['tStartDate'];
        $tEndDate       = $paData['tEndDate'];

        $tFormatNewStartDate    = explode("-",$tStartDate); 
        $tFormatNewEndDate      = explode("-",$tEndDate); 

        $tNewStartDate = $tFormatNewStartDate[2].'-'.$tFormatNewStartDate[1].'-'.$tFormatNewStartDate[0];
        $tNewEndDate   = $tFormatNewEndDate[2].'-'.$tFormatNewEndDate[1].'-'.$tFormatNewEndDate[0];

        $tSQLAlwBuy = "SELECT FTPdtCode,FTPdtStkCode FROM TCNMPdt 
                            WHERE FTPdtStkCode = (
                                SELECT TOP 1 PDT.FTPdtStkCode  FROM TCNMPdt PDT
                                INNER JOIN ( SELECT TOP 1 PDT.FTPdtStkCode 
                                            FROM TCNMPdt PDT 
                                    INNER JOIN TCNMPdtBar PDB
                                    ON PDT.FTPdtCode = PDB.FTPdtCode
                                    WHERE PDT.FTPdtCode = '$nPDTCode' OR PDB.FTPdtBarCode = '$nPDTCode' ) BAR
                                    ON PDT.FTPdtStkCode  = BAR.FTPdtStkCode 
                                    AND PDT.FTPdtStaAlwBuy = 1 ) 
                                AND TCNMPdt.FTPdtStaAlwBuy = 1";
        $oQueryAlwBuy   = $this->DB_SELECT($tSQLAlwBuy);

        if (!empty($oQueryAlwBuy)) {
            $nPDTCode       = $oQueryAlwBuy[0]['FTPdtCode'];
            $tSQL = "SELECT * FROM TSPdtSuggestDT 
                        WHERE (CONVERT(VARCHAR(10),FDPdtStartdate,121) = '$tNewStartDate' 
                        OR CONVERT(VARCHAR(10),FDPdtEnddate,121) = '$tNewStartDate' 
                        OR CONVERT(VARCHAR(10),FDPdtStartdate,121) BETWEEN '$tNewStartDate' AND '$tNewEndDate'
                        
                        OR CONVERT(VARCHAR(10),FDPdtStartdate,121) = '$tNewEndDate'
                        OR CONVERT(VARCHAR(10),FDPdtEnddate,121) = '$tNewEndDate'
                        OR CONVERT(VARCHAR(10),FDPdtEnddate,121) BETWEEN '$tNewStartDate' AND '$tNewEndDate' )
                        AND FTPdtCode = '$nPDTCode' AND FNPtdSeqNo != '$nSeq' ";

            $oQuery = $this->DB_SELECT($tSQL);
            if (!empty($oQuery)) {
                return $oQuery;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //Check Product
    public function FSxMTSOCheckProduct($pnCode){
        $tSQL = "SELECT TOP 1 CONVERT(VARCHAR(10),FDPdtStartdate,121) as FDPdtStartdate , CONVERT(VARCHAR(10),FDPdtEnddate,121) as FDPdtEnddate
                FROM TSPdtSuggestDT WHERE FTPdtCode = '$pnCode' ORDER BY CONVERT(VARCHAR(10),FDPdtEnddate,121) DESC";
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery;
        }else{
            return false;
        }
    }

    //Save ก่อนอนุมัติ
    public function FSxMTSOSaveBeforeApprove($tDocumentID){
        //Insert DT
        $tDatabase          = "TCNTPdtSuggestDT";
        $aDataDeleteWHERE   = array(
            'FTPthDocNo'    => $tDocumentID
        );
        $bConfirm           = true;
        $tResult            = $this->DB_DELETE($tDatabase,$aDataDeleteWHERE,$bConfirm);

        $tSQL = 'INSERT INTO TCNTPdtSuggestDT (
            FTBchCode,
            FTPthDocNo,
            FNPtdSeqNo,
            FTPdtCode,
            FTPdtName,
            FDPdtStartdate,
            FDPdtEnddate,
            FDDateUpd,
            FTTimeUpd,
            FTWhoUpd,
            FDDateIns,
            FTTimeIns,
            FTWhoIns
        )
        SELECT 
            FTBchCode,
            FTPthDocNo,
            FNPtdSeqNo,
            FTPdtCode,
            FTPdtName,
            FDPdtStartdate,
            FDPdtEnddate,
            FDDateUpd,
            FTTimeUpd,
            FTWhoUpd,
            FDDateIns,
            FTTimeIns,
            FTWhoIns
        FROM
        TSPdtSuggestDT';
        $tResult    = $this->DB_EXECUTE($tSQL);
        return $tResult;
        $this->FSxWriteLogByPage("[FSxMTSOSaveBeforeApprove] บันทึก");

    }
    

}

?>