<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class mMQPC extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        //$this->db->trans_begin();
    }

    public function FSxMMQPWriteLog($ptLogMsg){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptLogMsg."\n";
        $tFileName   = APPPATH.'logs/LogBackPrc_'.date('Ymd').'.txt';
        file_put_contents($tFileName,$tLogData,FILE_APPEND);
    }

    //Delete Temp
    public function FSxCMQPDeleteTemp(){
        $tComName   = gethostname();
        $tSQL       = " DELETE FROM TACTmpPrcDT  ";
        $tSQL      .= " WHERE FTComName = '$tComName' ";
        $oQuery     = $this->db->query($tSQL);
        $this->FSxMMQPWriteLog("[FSxCMQPDeleteTemp] Clear Temp");
    }

    //Move DT To Temp
    public function FSxCMQPMoveDTToTemp($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocCode  = $paPackData['ptDocNo'];
        }else{
            $tDocCode  = trim($tDocumentNumber);
        }

        $tWhoIns    = $paPackData['ptWhoIns'];
        $dCurrent   = date('Y-m-d');

        //ย้ายจาก DT ไป Temp
        /*$tSQL = " INSERT INTO TACTmpPrcDT (
                    FTComName , 
                    FTXidStkCode , 
                    FTXihDocType ,
                    FCXidQtyAll ,
                    FCXidNet ,
                    FCXidVat ,
                    FCXidVatable ,
                    FCXidCostIn ,
                    FCXidCostEx ,
                    FTPdtCode ,
                    FTXihDocNo ,
                    FTXidStaPrcStk ,
                    FNXihSign ,
                    FTWahCode ,
                    FDXihDocDate ,
                    FCXidStkFac 
                )
                SELECT 
                    '$tComName' AS FTComName,
                    FTXtdStkCode AS FTXidStkCode,
                    '5' AS FTXihDocType,
                    ROUND(SUM(FCXtdQtyAll),4) AS FCXidQtyAll,
                    ROUND(AVG(FCXtdSetPrice/FCXtdStkFac),4) AS FCXidNet,
                    ROUND(SUM(FCXtdVat),4) AS FCXidVat,
                    ROUND(SUM(FCXtdVatable),4) AS FCXidVatable,
                    ROUND(AVG(FCXtdCostIn),4) AS FCXidCostIn,
                    ROUND(AVG(FCXtdCostEx),4) AS FCXidCostEx,
                    FTPdtCode,
                    '$tDocCode' AS FTXihDocNo,
                    FTXtdStaPrcStk AS FTXidStaPrcStk,
                    FNXthSign AS FNXihSign,
                    FTWahCode AS FTWahCode,
                    FDXthDocDate AS FDXihDocDate,
                    NULL AS FCXidStkFac
                FROM TACTPtDT
                WHERE TACTPtDT.FTXthDocNo = '$tDocCode' AND FNXtdPdtLevel = 0 
                GROUP BY FTXthDocNo , FTXthDocType , FTXtdStkCode , FTXtdStaPrcStk , 
                         FNXthSign , FTWahCode , FDXthDocDate ,  FCXtdCostEx , FTPdtCode";*/
        $tSQL = "INSERT INTO TACTmpPrcDT (
                    FTComName , 
                    FTXidStkCode , 
                    FTXihDocType ,
                    FCXidQtyAll ,
                    FCXidNet ,
                    FCXidVat ,
                    FCXidVatable ,
                    FCXidCostIn ,
                    FCXidCostEx ,
                    FTPdtCode ,
                    FTXihDocNo ,
                    FTXidStaPrcStk ,
                    FNXihSign ,
                    FTWahCode ,
                    FDXihDocDate ,
                    FCXidStkFac 
                )
                SELECT
					DISTINCT
				    '$tComName' AS FTComName,
					G.FTXtdStkCode AS FTXidStkCode,
					'5' AS FTXihDocType,
					G.FCXidQtyAll,
					G.FCXidNet,
					G.FCXidVat,
					G.FCXidVatable,
					G.FCXidCostIn,
					G.FCXidCostEx,
					G.FTPdtCode,
                    '$tDocCode' AS FTXihDocNo,
                    D.FTXtdStaPrcStk AS FTXidStaPrcStk,
                    D.FNXthSign AS FNXihSign,
                    D.FTWahCode AS FTWahCode,
                    D.FDXthDocDate AS FDXihDocDate,
                    NULL AS FCXidStkFac
					FROM (
					SELECT
						MAX(FTPdtCode)											AS FTPdtCode,
						FTXtdStkCode,
						SUM(FCXtdStkFac * FCXtdQty)                             AS FCXidQtyAll,
						ROUND(AVG(FCXtdSetPrice/FCXtdStkFac),4) AS FCXidNet,
						ROUND(SUM(FCXtdVat),4)                                  AS FCXidVat,
						ROUND(SUM(FCXtdVatable),4)                              AS FCXidVatable,
						ROUND(AVG(FCXtdCostIn),4)                               AS FCXidCostIn,
						ROUND(AVG(FCXtdCostEx),4)                               AS FCXidCostEx
					FROM TACTPtDT
                    WHERE TACTPtDT.FTXthDocNo = '$tDocCode' AND FNXtdPdtLevel = 0
                    GROUP BY FTXtdStkCode
                ) G
				INNER JOIN TACTPtDT D ON D.FTXtdStkCode = G.FTXtdStkCode AND D.FTPdtCode = G.FTPdtCode
                WHERE D.FTXthDocNo = '$tDocCode'";
        $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxCMQPMoveDTToTemp] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxCMQPMoveDTToTemp] INSERT TACTmpPrcDT ".$tDocCode." = ".$this->db->affected_rows()." รายการ"
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }
        return $aRetrun;
    }

    //ปรับ PDT 
    public function FSxCMQPChangePDT($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocCode  = $paPackData['ptDocNo'];
        }else{
            $tDocCode  = trim($tDocumentNumber);
        }
        
        $tWhoIns    = $paPackData['ptWhoIns'];
        $dCurrent   = date('Y-m-d');

        //อัพเดท คงเหลือสินค้า ปลีก , ส่ง
        $tSql = "UPDATE TCNMPdt SET ";
        $tSql .= " FCPdtQtyRet=CASE WHEN (FTWahCode='001') THEN (FCPdtQtyRet - FCXidQtyAll) ELSE FCPdtQtyRet END ";   /* (FCXidQtyAll * PDT.FCPdtStkFac)*/
        $tSql .= ",FCPdtQtyNow=CASE WHEN (FTWahCode='001') THEN (FCPdtQtyNow - FCXidQtyAll) ELSE FCPdtQtyNow END ";
        $tSql .= ",FCPdtQtyWhs=CASE WHEN (FTWahCode='002') THEN (FCPdtQtyWhs - FCXidQtyAll) ELSE FCPdtQtyWhs END ";
        $tSql .= ",FCPdtCostAmt = Round(((FCPdtCostAvg*(FCPdtQtyRet-FCPdtQtyWhs)) - FCXidVatable),4 )" ;
        $tSql .= " FROM TCNMPdt, TACTmpPrcDT";

        /*$tSql .= " LEFT JOIN (
                        SELECT FCPdtStkFac,FTPdtCode FROM TCNMPdt WITH(NOLOCK)
                    ) PDT ON TACTmpPrcDT.FTPdtCode = PDT.FTPdtCode ";*/

        $tSql .= " WHERE (TCNMPdt.FCPdtStkFac >= 1)";
        $tSql .= " AND (TCNMPdt.FTPdtStkCode= TACTmpPrcDT.FTXidStkCode)";
        $tSql .= " AND (FTPdtStkControl='1')";
        $tSql .= " AND (TCNMPdt.FTPdtStaSet IN('1','2'))";
        $tSql .= " AND (FTXidStaPrcStk='' OR FTXidStaPrcStk IS NULL)";
        $tSql .= " AND FTComName='$tComName' ";
        $tSql .= " AND FTXihDocNo='$tDocCode' ";
        $oQuery = $this->db->query($tSql);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPChangePDT] ".$this->db->error()['message']);
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPChangePDT] ".$tDocCode." อัพเดทคงเหลือสินค้า ปลีก,ส่ง = ".$this->db->affected_rows()." รายการ");
        }

        //อัพเดท ให้สินค้ามันเคลื่อนไหว
        $tSQLPDT = "UPDATE TCNMPdt ";
        $tSQLPDT .= " SET FDPdtLastAct = '$dCurrent' ";
        $tSQLPDT .= ", FTPdtStaActive = '1' ";
        $tSQLPDT .= ", FTWhoUpd = '$tWhoIns' ";
        $tSQLPDT .= " WHERE FTPdtStkCode IN ( ";
        $tSQLPDT .= " SELECT DISTINCT FTXtdStkCode FROM TACTPtDT DT ";
        $tSQLPDT .= " INNER JOIN TACTPtHD HD ON DT.FTXthDocNo = HD.FTXthDocNo ";
        $tSQLPDT .= " WHERE HD.FTXthDocNo = '$tDocCode' ";
        $tSQLPDT .= " AND HD.FTXthDocType IN ('5','6') )";
        $oQuery = $this->db->query($tSQLPDT);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPChangePDT] ".$this->db->error()['message']);
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPChangePDT] ".$tDocCode." อัพเดทเคลื่อนไหวสินค้า = ".$this->db->affected_rows()." รายการ");
        }
    }

    //InWha
    public function FSxCMQPInWha($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocument  = $paPackData['ptDocNo'];
        }else{
            $tDocument  = trim($tDocumentNumber);
        }

        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];

        //สร้างสินค้าในคลังอื่นๆ
        $tSQL  = "INSERT INTO TCNTPdtInWha ( FTWahCode, FTPdtCode, FTPtdStkCode, FCWahQty, FDDateUpd, FTTimeUpd, ";
        $tSQL .= " FTWhoUpd, FDDateIns, FTTimeIns, FTWhoIns )";
        $tSQL .= " SELECT DISTINCT TACTmpPrcDT.FTWahCode, TACTmpPrcDT.FTPdtCode, TACTmpPrcDT.FTXidStkCode, ";
        $tSQL .= " 0, ";
        $tSQL .= " '$dCurrent','$tTime', ";
        $tSQL .= " '$tWhoIns' ,";
        $tSQL .= " '$dCurrent','$tTime', ";
        $tSQL .= " '$tWhoIns' ";
        $tSQL .= " FROM TACTmpPrcDT LEFT JOIN TCNTPdtInWha ";
        $tSQL .= " ON (TACTmpPrcDT.FTXidStkCode = TCNTPdtInWha.FTPtdStkCode) ";
        $tSQL .= " AND (TACTmpPrcDT.FTWahCode = TCNTPdtInWha.FTWahCode)";
        $tSQL .= " WHERE ((TCNTPdtInWha.FTWahCode='') Or (TCNTPdtInWha.FTWahCode Is Null))";
        $tSQL .= " AND (TACTmpPrcDT.FTXihDocNo='$tDocument') AND (TACTmpPrcDT.FTComName='$tComName')";
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxCMQPInWha] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPInWha] Insert TCNTPdtInWha = ".$this->db->affected_rows()." รายการ");
        }

        //ตัดสต้อกคลังอื่น    
		$tSQLUpd = "UPDATE TCNTPdtInWha";
		$tSQLUpd .= " SET TCNTPdtInWha.FCWahQty = TCNTPdtInWha.FCWahQty - TACTmpPrcDT.FCXidQtyAll , "; /*(TACTmpPrcDT.FCXidQtyAll * PSF.FCPdtStkFac)*/
		$tSQLUpd .= " TCNTPdtInWha.FDDateUpd = " . "'$dCurrent' , ";
		$tSQLUpd .= " TCNTPdtInWha.FTTimeUpd = " . "'$tTime' , ";
		$tSQLUpd .= " TCNTPdtInWha.FTWhoUpd = " . "'$tWhoIns' ";
        $tSQLUpd .= " FROM TCNTPdtInWha,TACTmpPrcDT";
        
/*$tSQLUpd .= " LEFT JOIN (
                         SELECT FCPdtStkFac,FTPdtCode FROM TCNMPdt WITH(NOLOCK)
                      ) PSF ON TACTmpPrcDT.FTPdtCode = PSF.FTPdtCode ";*/

        $tSQLUpd .= " WHERE (TCNTPdtInWha.FTPtdStkCode=TACTmpPrcDT.FTXidStkCode)";
        $tSQLUpd .= " AND (TACTmpPrcDT.FTWahCode = TCNTPdtInWha.FTWahCode)";
        $tSQLUpd .= " AND (TACTmpPrcDT.FTComName='$tComName')";
        $oQuery = $this->db->query($tSQLUpd);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxCMQPInWha] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPInWha] Update TCNTPdtInWha = ".$this->db->affected_rows()." รายการ");
        }

        $aRetrun = array(
            'nStaReturn'    => 1,
            'aMessageError' => "[FSxCMQPInWha] Insert/Update Success"
        );
        return $aRetrun;
    }

    //STKCard
    public function FSxCMQPSTKCard($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocument  = $paPackData['ptDocNo'];
        }else{
            $tDocument  = trim($tDocumentNumber);
        }

        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        //Insert Select ลง StkCard
        $tSQL  = "INSERT INTO TCNTPdtStkCard ( FTBchCode,FTStkDocNo,FTStkType,FTPdtStkCode";
        $tSQL .= ", FCStkQty, FTWahCode, FDStkDate";
        $tSQL .= ", FCStkSetPrice, FCStkCostIn, FCStkCostEx";
        $tSQL .= ", FDDateUpd, FTTimeUpd,FTWhoUpd, FDDateIns, FTTimeIns, FTWhoIns )";

        $tSQL .= "SELECT '$tBchCode' AS FTBchCode,";
        $tSQL .= " '$tDocument' AS FTStkDocNo, ";
        $tSQL .= " '2' AS FTStkType, ";
        $tSQL .= " FTXidStkCode AS FTPdtStkCode, ";
        $tSQL .= " FCXidQtyAll AS FCStkQty, "; /*PSF.FCPdtStkFac*/
        $tSQL .= " TACTmpPrcDT.FTWahCode AS FTWahCode, ";
        $tSQL .= " FDXihDocDate AS FDStkDate, ";
        $tSQL .= " FCXidNet AS FCStkSetPrice, ";
        $tSQL .= " FCXidCostIn AS FCStkCostIn, ";
        $tSQL .= " FCXidCostEx AS FCStkCostEx, ";
        $tSQL .= " '$dCurrent' AS FDDateUpd, ";
        $tSQL .= " '$tTime' AS FTTimeUpd, ";
        $tSQL .= " '$tWhoIns' AS FTWhoUpd, ";
        $tSQL .= " '$dCurrent' AS FDDateIns, ";
        $tSQL .= " '$tTime' AS FTTimeIns, ";
        $tSQL .= " '$tWhoIns' AS FTWhoIns ";
        $tSQL .= " FROM TACTmpPrcDT WITH(NOLOCK)";
        $tSQL .= " LEFT JOIN TCNTPdtStkCard ON TCNTPdtStkCard.FTWahCode= TACTmpPrcDT.FTWahCode";
        $tSQL .= " AND FTXidStkCode = FTPdtStkCode";
        $tSQL .= " AND FTBchCode = '$tBchCode' ";
        $tSQL .= " AND FTStkDocNo = '$tDocument' ";
        $tSQL .= " AND FTStkType = '2' ";
        
/*$tSQL .= " LEFT JOIN (
                        SELECT FCPdtStkFac,FTPdtCode FROM TCNMPdt WITH(NOLOCK)
                   ) PSF ON TACTmpPrcDT.FTPdtCode = PSF.FTPdtCode ";*/

        $tSQL .= " WHERE (TCNTPdtStkCard.FDStkDate Is Null) ";
        $tSQL .= " AND (FTComName='$tComName')";
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPSTKCard] ".$this->db->error()['message']);
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPSTKCard] Insert TCNTPdtStkCard = ".$this->db->affected_rows()." รายการ");
        }

        //Update Cost Avg  
        $tSQLUPD = "UPDATE TCNTPdtStkCard";
        $tSQLUPD .= " SET TCNTPdtStkCard.FCStkCostAvg = TCNMPdt.FCPdtCostAvg";
        $tSQLUPD .= " From TCNTPdtStkCard ";
        $tSQLUPD .= " INNER JOIN TCNMPdt AS TCNMPdt ";
        $tSQLUPD .= " ON TCNMPdt.FTPDTStkCode = TCNTPdtStkCard.FTPDTStkCode";
        $tSQLUPD .= " WHERE TCNTPdtStkCard.FTStkDocNo= '$tDocument' ";
        $tSQLUPD .= " AND TCNTPdtStkCard.FTBchCode = '$tBchCode' ";
        $oQuery = $this->db->query($tSQLUPD);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPSTKCard] ".$this->db->error()['message']);
        }else{
            $this->FSxMMQPWriteLog("[FSxCMQPSTKCard] Update Cost Avg = ".$this->db->affected_rows()." รายการ");
        }
    }

    //:::::::::::::::::::  Split เอกสาร กรณีมี Vatcode มากกว่าหนึ่ง ::::::::::::::::::: //

    //เช็คเอกสารว่ามี vat code มากกว่าหนึ่งไหม
    public function FSxCMQPCheckVatCodeinDocument($paPackData){
        $tComName   = gethostname();
        $tDocument  = $paPackData['ptDocNo'];
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        $tSQL = "SELECT DT.FTPdtCode , COUNTPDT FROM TACTPtDT DT LEFT JOIN ( 
                    SELECT FTXthDocNo, COUNT(FTPdtCode) AS COUNTPDT FROM TACTPtDT 
                    WHERE FTXthDocNo ='$tDocument'
                    GROUP BY FTXthDocNo
                ) AS CPDT ON CPDT.FTXthDocNo = DT.FTXthDocNo
                WHERE DT.FTXthDocNo ='$tDocument'";
        $oQuery = $this->db->query($tSQL);
        if(empty($oQuery)){
            $tPDTCode = '';
        }else{
            $aQuery = $oQuery->result_array();
            $nCount = $aQuery[0]['COUNTPDT'];
            $tPDTCode = '';
            for($i=0; $i<$nCount; $i++){
                $tPDTCode .= "'".$aQuery[$i]['FTPdtCode'] . "',";
            }
            $tPDTCode = substr($tPDTCode,0,-1);
        }

        //มีข้อมูล
        if($tPDTCode == '' || $tPDTCode == null){
            $tSQLFindVatCode = '';
        }else{
            $tSQLFindVatCode = "SELECT ROW_NUMBER() OVER(ORDER BY FTSplCode) AS rtRowID , C.* FROM ( 
                                    SELECT DISTINCT FTVatCode,FTSplCode
                                    FROM TCNMPdt
                                    WHERE FTPdtCode in($tPDTCode) 
                                ) C ORDER BY rtRowID DESC";
            $oQuery = $this->db->query($tSQLFindVatCode);
            $aQuery = $oQuery->result_array();
        }
        return $aQuery;
    }

    //สร้างรหัสเอกสาร
    public function generateCode($tTablename,$tFiledDocno,$tBchCode){

        $tSQL       = "SELECT TOP 1 FTSatUsrFmtAll FROM TSysAuto";
        $tSQL       .= " WHERE FTSatTblName = '$tTablename' ";
        $tSQL       .= " AND FTSatDefChar='PC' ";
        $oQuery     = $this->db->query($tSQL);
        $aQuery     = $oQuery->result_array();

        $tBCH               = $tBchCode;
        $tFormat            = $aQuery[0]['FTSatUsrFmtAll'];
        $tResultFormat      = str_replace('BCH', $tBCH , $tFormat);
        $tResultFormat      = str_replace('YY', date("y") , $tResultFormat);
        $tResultFormat      = str_replace('MM', date("m") , $tResultFormat);

        $tCheckcondition    = strstr($tResultFormat,"#");
        $nDigitformat       = strlen($tCheckcondition);
        $nDigitformat       = '%0'.$nDigitformat.'d';


        $tSQL  = "SELECT CASE 
                    WHEN (SELECT TOP 1 $tFiledDocno FROM $tTablename) IS NULL THEN '-00000' 
                    ELSE (SELECT TOP 1 $tFiledDocno FROM $tTablename WHERE $tTablename.FTBchCode = (SELECT TOP 1 FTBchCode FROM TCNMComp (NOLOCK))  ORDER BY $tFiledDocno DESC )
                END AS $tFiledDocno";
        $oQuery          = $this->db->query($tSQL);
        $aResultNumber   = $oQuery->result_array();

        if($aResultNumber[0][$tFiledDocno] == '' || $aResultNumber[0][$tFiledDocno] == null){
            $tNumberDocno   = sprintf($nDigitformat,1);
        }else{
            $tValue         = explode("-",$aResultNumber[0][$tFiledDocno]);
            $tNumberDocno   = $tValue[1] + 1;
            $nNumber        = sprintf($nDigitformat,$tNumberDocno);
            $tNumberDocno   = $nNumber;
        }

        $tResultFormat      = str_replace('#####', $tNumberDocno , $tResultFormat);
        return $tResultFormat;
    }

    //ถ้าเอกสารนี้มี สินค้าที่ VAT มีมากกว่าหนึ่ง ต้อง SELECT Into
    public function FSaMPURSelectintoHDDT($tDocumentID,$nVatCode,$paPackData){
        //เลขที่เอกสารจะต้อง +1 เสมอ
        $tBchCode       = $paPackData['pnBchCode'];
        $tFormatCode    = $this->generateCode('TACTPtHD','FTXthDocNo',$tBchCode);
        $aFormatCode    = explode("PC",$tFormatCode);
        $tFormatCode    = 'PC0' . $aFormatCode[1];
        $tSPL           = $nVatCode['FTSplCode'];
        $nVatCodeHD     = $nVatCode['FTVatCode'];

        //เพิ่ม HD
        $tSQLHD = " INSERT INTO TACTPtHD 
                    SELECT 
                        FTBchCode , '$tFormatCode' , FTXthDocType , FDXthDocDate ,
                        FTXthDocTime , FTXthVATInOrEx , FTStyCode , FTDptCode ,
                        FTUsrCode , '$tSPL' , FTCstCode , FTAreCode ,
                        FTSpnCode , FTPrdCode , FTWahCode , FTXthApvCode , 
                        FTShpCode , FNCspCode , FNXthCrTerm , FDXthDueDate ,
                        FTXthRefExt , FDXthRefExtDate , FTXthRefInt , FDXthRefIntDate ,
                        FTXthRefAE , FDXthTnfDate , FDXthBillDue , FTXthCtrName ,
                        FNXthDocPrint , FCXthVATRate , '$nVatCodeHD' , FCXthTotal ,
                        FCXthTotalExcise , FCXthVatExcise , FCXthNonVat , FCXthB4DisChg ,
                        FTXthDisChgTxt , FCXthDis , FCXthChg , FCXthAftDisChg ,
                        FCXthVat , FCXthVatable , FCXthGrand , FCXthRnd ,
                        '0' , FCXthReceive , FCXthChn , FTXthGndText ,
                        FCXthLeft , FCXthMnyCsh , FCXthMnyChq , FCXthMnyCrd ,
                        FCXthMnyCtf , FCXthMnyCpn , FCXthMnyCls , FCXthMnyCxx ,
                        FCXthGndCN , FCXthGndDN , FCXthGndAE , FCXthGndTH ,
                        FTXthStaPaid , FTXthStaRefund , FTXthStaType , FTXthStaDoc,
                        FTXthStaPrcDoc , FTXthStaPrcSpn , FTXthStaPrcCst , FTXthStaPrcGL,
                        FTXthStaPost , FTPjcCode , FTAloCode , FTCcyCode,
                        FCXthCcyExg , FTPosCode , FTXthPosCN , FTLogCode,
                        FTXthRmk , FNXthSign , FTXthCshOrCrd , FCXthPaid,
                        FTXthDstPaid ,FTXbhDocNo , FTXphDocNo ,FNXthStaDocAct,
                        FNXthStaRef ,FTXthUsrEnter , FTXthUsrPacker , FTXthUsrChecker,
                        FTXthUsrSender ,FTXthTnfID , FTXthVehID , FTDocControl,
                        FTXthStaPrcStk , FTXthStaPrcLef , FTXthStaVatType , FTXthStaVatSend,
                        FTXthStaVatUpld , FTXthDocVatFull , FTXqhDocNoRef , FTXthRefSaleTax,
                        FTCstStaClose , FTXthBchFrm , FTXthBchTo , FTXthWahFrm,
                        FTXthWahTo , FTXthCstName , FTCstAddrInv , FTCstStreetInv,
                        FTCsttrictInv , FTDstCodeInv , FTPvnCodeInv , FTCstPostCodeInv,
                        FCXthDiscGP1 , FCXthDiscGP2 , FCXthB4VatAfGP1 , FCXthB4VatAfGP2,
                        FTXthDocRefMin , FTXthDocRefMax , FTXthStaJob , FDEdiDate,
                        FTEdiTime , FTEdiDocNo , FTEdiStaRcvAuto , FDXthBchAffect,
                        FDXthBchExpired , FDXthBchReturn , FNLogStaExport , FTPmhDocNoBill,
                        FCXthDisPmt , FTXthCpnCodeRef , FCXthCpnRcv , FCXthRndMnyChg,
                        FTXthStaSavZero , FDDateUpd , FTTimeUpd , FTWhoUpd, 
                        FDDateIns , FTTimeIns , FTWhoIns
                    FROM TACTPtHD 
                    WHERE FTXthDocNo = '$tDocumentID' ";
        $oQuery = $this->db->query($tSQLHD);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMPURSelectintoHDDT] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $this->FSxMMQPWriteLog("[FSaMPURSelectintoHDDT] INSERT TACTPtHD ".$tDocumentID);
        }

        //เพิ่ม DT
        $tSQLDT = " INSERT INTO TACTPtDT 
                    SELECT 
                        FTBchCode , '$tFormatCode' , FNXtdSeqNo , FTPdtCode , 
                        FTPdtName , FTXthDocType , FDXthDocDate , FTXthVATInOrEx ,
                        FTXtdBarCode , FTXtdStkCode , FCXtdStkFac , FTXtdVatType ,
                        FTXtdSaleType , FTPgpChain , FTSrnCode , FTPmhCode ,
                        FTPmhType , FTPunCode , FTXtdUnitName , FCXtdFactor ,
                        FCXtdSalePrice , FCXtdQty , FCXtdSetPrice ,  FCXtdB4DisChg ,
                        FTXtdDisChgTxt , FCXtdDis , FCXtdChg , FCXtdNet ,
                        FCXtdVat , FCXtdVatable , FCXtdQtyAll , FCXtdCostIn ,
                        FCXtdCostEx , FTXtdStaPdt , FTXtdStaRfd , FTXtdStaPrcStk ,
                        FNXthSign , FTAccCode , FNXtdPdtLevel , FTXtdPdtParent ,
                        '$tSPL' , FTWahCode , FNXtdStaRef , FCXtdQtySet ,
                        FTPdtStaSet , FDXtdExpired , FTXtdLotNo , FCXtdQtyLef ,
                        FCXtdQtyRfn , FTXthStaVatSend , FTPdtArticle , FTDcsCode ,
                        FTPszCode , FTClrCode , FTPszName , FTClrName ,
                        FCPdtLeftPO , FTCpnCode , FCXtdQtySale , FCXtdQtyRet ,
                        FCXtdQtyCN , FCXtdQtyAvi , FCXtdQtySgg , FTXthBchFrm ,
                        FTXthBchTo , FTXthWahFrm , FTXthWahTo , FCXthDiscGP1 ,
                        FCXthDiscGP2 , FCXtdB4VatAfGP1 , FCXtdB4VatAfGP2 , FCXtdDisShp ,
                        FCXtdShrDisShp , FTXtdTaxInv , FTPdtNoDis , FCXtdDisAvg ,
                        FCXtdFootAvg , FCXtdRePackAvg , FCPdtLawControl , FCXtdExcDuty ,
                        FTPdtSaleType , FCPdtMax , FDPdtOrdStart , FDPdtOrdStop ,
                        FTXtdPdtKey , FTPmhDocNoBill , FTXtdPmhCpnDocNo , FCXtdPmhCpnGetQty ,
                        FCXtdPmhCpnValue , FCXtdDisGP , FCXtdPmtQtyGet , FDDateUpd ,
                        FTTimeUpd , FTWhoUpd , FDDateIns , FTTimeIns , FTWhoIns 
                    FROM TACTPtDT 
                    WHERE FTXthDocNo = '$tDocumentID' ";
        $oQuery = $this->db->query($tSQLDT);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMPURSelectintoHDDT] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $this->FSxMMQPWriteLog("[FSaMPURSelectintoHDDT] INSERT TACTPtDT ".$tDocumentID." = ".$this->db->affected_rows()." รายการ");
        }

        //ถ้าเป็นตัวเเรก มันจะไม่ต้อง split มัน
        // $tDocumentForDelete = $tFormatCode;

        //ลบ DT ที่ไม่ใช่ VAT ตัวเองออก
        $this->FSaMPURDeleteHDDT($tFormatCode,$nVatCode,$paPackData);

        $aRetrun = array(
            'nStaReturn'    => 1,
            'aMessageError' => "[FSaMPURSelectintoHDDT] ".$this->db->error()['message'],
            'tFormatCode'   => $tFormatCode
        );
        return $aRetrun;
    }

    //ลบรายการใน DT ที่ไม่ใช่ VAT ตัวเองออก และรัน SEQ ใหม่
    public function FSaMPURDeleteHDDT($tDocumentID,$nVatCode,$paPackData){
        $nVat   = $nVatCode['FTVatCode'];
        $tSPL   = $nVatCode['FTSplCode'];

        //ลบ VAT ที่ไม่ใช่ของตัวเองออก
        // $tDELVatNotMyself = "DELETE DT FROM TACTPtDT DT 
        //     LEFT JOIN TCNMPDT PDT ON DT.FTPDTCode = PDT.FTPDTCode 
        //     WHERE PDT.FTVatCode NOT IN ('$nVatCode') AND DT.FTXthDocNo = '$tDocumentID' ";
        // $this->db->query($tDELVatNotMyself);
        $tDELVatNotMyself = "   DELETE TACTPtDT FROM TCNMPdt P WITH(NOLOCK)
                                INNER JOIN TACTPtDT DT ON DT.FTPdtCode = P.FTPdtCode AND DT.FTXtdStkCode = P.FTPdtStkCode
                                WHERE (P.FTVatCode != '$nVat' OR P.FTSplCode != '$tSPL')
                                AND DT.FTXthDocNo = '$tDocumentID' ";
        $this->db->query($tDELVatNotMyself);
        
        //รัน SEQ ใหม่
        $tUpdateSql = " UPDATE TACTPtDT 
                        SET FNXtdSeqNo = SeqNew.rtRowID
                        FROM (
                            SELECT c.* FROM( 
                                    SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo) AS rtRowID, FNXtdSeqNo , FTPdtCode FROM TACTPtDT 
                                    WHERE TACTPtDT.FTXthDocNo = '$tDocumentID' 
                                ) as c 
                            ) SeqNew
                        WHERE 
                            SeqNew.FNXtdSeqNo = TACTPtDT.FNXtdSeqNo AND SeqNew.FTPdtCode = TACTPtDT.FTPdtCode AND TACTPtDT.FTXthDocNo = '$tDocumentID' ";
		$this->db->query($tUpdateSql);
		
		//Update 
		$tUpdateSpl = "UPDATE TACTPtHD SET FTSplCode = '$tSPL' WHERE FTXthDocNo = '$tDocumentID' ";
		$this->db->query($tUpdateSpl);

        $this->FSxMPURCalculateHDForVatCode($tDocumentID,$nVatCode);
    }

    //คำนวณรายการใน HD ใหม่ เอาเฉพาะของ VAT CODE ตัวเอง
    public function FSxMPURCalculateHDForVatCode($tDocumentID,$nVatCode){
        //Calculate พวกค่าต่างๆ 
        $tSQLResultCal = "SELECT 
                            C.* ,
                            CASE
                                WHEN C.FTXthVatInorEx = 1 THEN ROUND(C.FCXthAftDisChg - C.FCXthVat ,2)
                                WHEN C.FTXthVatInorEx = 2 THEN ROUND(C.FCXthAftDisChg,2)
                                ELSE 0 
                            END AS FCXthVatable ,
                            C.FCXthAftDisChg AS FCXthGrand , 
                            ROUND((C.FCXthAftDisChg - C.FCXthVat) * 3 / 100 ,2) AS FCXthWpTax ,
                            C.FCXthAftDisChg - ROUND((C.FCXthAftDisChg - C.FCXthVat) * 3 / 100 ,2) AS FCXthReceive,
                            C.FCXthAftDisChg AS FCXthLeft
                        FROM (
                            SELECT 
                                HD.FTXthVatInorEx,
                                SUM(DT.FCXtdNet) AS FCXthTotal , 
                                SUM(DT.FCXtdNet) AS FCXthTotalExcise ,
                                SUM(DT.FCXtdNet) AS FCXthB4DisChg ,
                                SUM(DT.FCXtdNet) - HD.FCXthDis AS FCXthAftDisChg,
                                CASE
                                    WHEN HD.FTXthVatInorEx = 1 THEN ROUND((SUM(DT.FCXtdNet) - HD.FCXthDis)  - (((SUM(DT.FCXtdNet) - HD.FCXthDis) * 100) / ( 100 + HD.FCXthVATRate)),2)
                                    WHEN HD.FTXthVatInorEx = 2 THEN ROUND(((SUM(DT.FCXtdNet) - HD.FCXthDis) * HD.FCXthVATRate /100 ),2) 
                                    ELSE 0 
                                END AS FCXthVat 
                            FROM TACTPtDT DT
                            LEFT JOIN TACTPtHD HD ON DT.FTXthDocNo = HD.FTXthDocNo
                            WHERE DT.FTXthDocNo = '$tDocumentID'
                            GROUP BY HD.FCXthDis , HD.FTXthVatInorEx , HD.FCXthVATRate
                        ) C ";
        $oSQLResultCal =  $this->db->query($tSQLResultCal);  
        $aSQLResultCal = $oSQLResultCal->result_array();

        $FCXthTotal         = $aSQLResultCal[0]['FCXthTotal'];
        $FCXthTotalExcise   = $aSQLResultCal[0]['FCXthTotalExcise'];
        $FCXthB4DisChg      = $aSQLResultCal[0]['FCXthB4DisChg'];
        $FCXthAftDisChg     = $aSQLResultCal[0]['FCXthAftDisChg'];
        $FCXthVat           = $aSQLResultCal[0]['FCXthVat'];
        $FCXthVatable       = $aSQLResultCal[0]['FCXthVatable'];
        $FCXthGrand         = $aSQLResultCal[0]['FCXthGrand'];
        // $FCXthWpTax         = $aSQLResultCal[0]['FCXthWpTax'];
        $FCXthWpTax         = 0;
        $FCXthReceive       = $aSQLResultCal[0]['FCXthReceive'];
        $FTXthGndText       = $this->bahtText($FCXthGrand);
        $FCXthLeft          = $aSQLResultCal[0]['FCXthLeft'];
        $tUpdateSql  = "UPDATE TACTPtHD
                        SET FCXthTotal = '$FCXthTotal', 
                            FCXthTotalExcise = '$FCXthTotalExcise',
                            FCXthB4DisChg = '$FCXthB4DisChg',
                            FCXthAftDisChg = '$FCXthAftDisChg',
                            FCXthVat = '$FCXthVat',
                            FCXthVatable = '$FCXthVatable',
                            FCXthGrand = '$FCXthGrand',
                            FCXthWpTax = '$FCXthWpTax',
                            FCXthReceive = '$FCXthReceive',
                            FTXthGndText = '$FTXthGndText',
                            FCXthLeft = '$FCXthLeft'
                        WHERE FTXthDocNo = '$tDocumentID'";
         $this->db->query($tUpdateSql);
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

    //::::::::::::::::::::::::::::::::::  END ::::::::::::::::::::::::::::::::::::: //

    //คำนวณ Prorate
    public function FSxCMQPProrate($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocument  = $paPackData['ptDocNo'];
        }else{
            $tDocument  = trim($tDocumentNumber);
        }
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        $tSQL    = "SELECT DT.FTXthDocNo , HD.FCXthDis FROM TACTPtDT DT
                    LEFT JOIN TACTPtHD HD ON DT.FTXthDocNo = HD.FTXthDocNo
                    WHERE DT.FTXthDocNo = '$tDocument' ";
        $oResultCheclDT = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $oResultCheclDT = $oResultCheclDT->result_array();
            $tSQLDT  = "SELECT 
                            DT.FTPdtCode,
                            DT.FNXtdSeqNo,
                            DT.FTXthDocNo,
                            DT.FCXtdNet,
                            DT.FTPdtNoDis,
                            DT.FTXthVATInOrEx,
                            DT.FCXtdVat ,
                            DT.FCXtdVatable ,
                            HD.FCXthVATRate,
                            HD.FCXthAftDisChg,
                            SDT.NETS
                        FROM TACTPtDT DT 
                        INNER JOIN (SELECT FTXthDocNo,SUM(FCXtdNet) AS NETS FROM TACTPtDT WHERE FTXthDocNo = '$tDocument' AND FTPdtNoDis = 2  GROUP BY FTXthDocNo ) SDT
                        ON SDT.FTXthDocNo = DT.FTXthDocNo 
                        LEFT JOIN TACTPtHD HD ON  HD.FTXthDocNo = DT.FTXthDocNo
                        WHERE DT.FTXthDocNo = '$tDocument' 
                        AND DT.FTPdtNoDis = 2 ";
            $oResultQueryDT = $this->db->query($tSQLDT);
            if($this->db->trans_status() === FALSE){
                $aRetrun = array(
                    'nStaReturn'    => 99,
                    'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
                );
                $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                return $aRetrun;
            }else{
                $oResultQueryDT = $oResultQueryDT->result_array();
                //ส่วนลดท้ายบิล 
                $nDiscount      = $oResultCheclDT[0]['FCXthDis'];
                $nDecimal       = 2;
                $aProrateByproduct = array();

                for($i=0; $i<count($oResultQueryDT); $i++){
                    //ต้องอนุญาติลดเท่านั้น FTPdtNoDis = 2
                    if($oResultQueryDT[$i]['FTPdtNoDis'] == 2){
                        // Create By Napat(Jame) 12/05/2020
                        // เคสด่วนถ้าเจอสินค้าที่มีค่า Net = 0 ไม่ต้องคำนวณ Prorate #เบื้องต้นให้ปิดแบบนี้ไปก่อน FTH แจ้งมา
                        if($oResultQueryDT[$i]['FCXtdNet'] != 0){
                            // if($oResultQueryDT[$i]['FCXtdNet'] == 0){
                                //ราคาสุทธิเป็น 0 (คือราคาหักส่วนลดรายการ)
                                // $nProrate       = round($nDiscount * 0,2);
                            // }else{
                            //ผลรวมทั้งหมดของราคาสุทธิ
                            if($oResultQueryDT[$i]['NETS'] == 0){
                                $nProrate       = round($nDiscount * $oResultQueryDT[$i]['FCXtdNet'],2);
                            }else{
                                $nProrate       = round($nDiscount * $oResultQueryDT[$i]['FCXtdNet']/$oResultQueryDT[$i]['NETS'],2);
                            }
                            // }

                            // $nProrate       = round($nDiscount * $oResultQueryDT[$i]['FCXtdNet']/$oResultQueryDT[$i]['NETS'],2);
                            if(strpos($nProrate,".") !== false){
                                //ถ้ามีทศนิยม
                                $aProrateDecimal = explode(".",$nProrate);
                                $nProrateDecimal = substr($aProrateDecimal[1],0,$nDecimal);
                                $nNewProrate = $aProrateDecimal[0].'.'.$nProrateDecimal;
                            }else{
                                //ถ้าไม่มีทศนิยม
                                $nNewProrate = substr($nProrate,0,$nDecimal);
                            }

                            $aNewArrayProduct = array(
                                'DocumentNumber'    => $oResultQueryDT[$i]['FTXthDocNo'],
                                'SeqNumber'         => $oResultQueryDT[$i]['FNXtdSeqNo'] , 
                                'ProductNumber'     => $oResultQueryDT[$i]['FTPdtCode'] ,
                                'Value'             => $nNewProrate ,
                                'FCXtdNet'          => $oResultQueryDT[$i]['FCXtdNet'] ,
                                'FTXthVATInOrEx'    => $oResultQueryDT[$i]['FTXthVATInOrEx'] ,
                                'FCXthVATRate'      => $oResultQueryDT[$i]['FCXthVATRate'],
                                'FCXtdVat'          => $oResultQueryDT[$i]['FCXtdVat'] , 
                                'FCXtdVatable'      => $oResultQueryDT[$i]['FCXtdVatable'],
                                'FCXthAftDisChg'    => $oResultQueryDT[$i]['FCXthAftDisChg']
                            );
                            array_push($aProrateByproduct,$aNewArrayProduct);
                        }
                    }else{
                        // $aDataReturn    =  array(
                        //     'rtCode'    => '800',
                        //     'rtDesc'    => 'Data Not Found',
                        // );
                        // return $aDataReturn;
                        // exit;
                        $aRetrun = array(
                            'nStaReturn'    => 99,
                            'aMessageError' => "[FSxCMQPProrate] 3.พบสินค้า FTPdtNoDis <> 2"
                        );
                        $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                        return $aRetrun;
                    }
                }

                //ผลรวม prorate มันจะยังไม่ครบจำนวนส่วนลด ต้องเอาไปหยอดตัวสุดท้าย
                $nSumProrate = 0;
                for($j=0; $j<count($aProrateByproduct); $j++){
                    $nSumProrate = $nSumProrate + $aProrateByproduct[$j]['Value'];
                    //ผลรวม prorate ที่เหลือต้องเอาไป + ตัวสุดท้าย
                    if($j == count($aProrateByproduct) - 1){
                        $nDifferenceProrate = $nDiscount - $nSumProrate;
                        $aProrateByproduct[$j]['Value'] = $aProrateByproduct[$j]['Value'] + $nDifferenceProrate;
                    }
                }

                //เอาค่ากลับไป update
                for($k=0; $k<count($aProrateByproduct); $k++){
                    $nProrate           = $aProrateByproduct[$k]['Value'];
                    $tDocumentNo        = $aProrateByproduct[$k]['DocumentNumber'];
                    $nSeqPDT            = $aProrateByproduct[$k]['SeqNumber'];
                    $tCodePDT           = $aProrateByproduct[$k]['ProductNumber']; 

                    //คิด VAT ใหม่
                    $FCXtdNet           = $aProrateByproduct[$k]['FCXtdNet'];
                    $FCXthVATRate       = $aProrateByproduct[$k]['FCXthVATRate'];
                    $FTXthVATInOrEx     = $aProrateByproduct[$k]['FTXthVATInOrEx'];
                    $FCXtdVat           = $aProrateByproduct[$k]['FCXtdVat'];
                    $FCXtdVatable       = $aProrateByproduct[$k]['FCXtdVatable'];
                    $FCXthAftDisChg     = $aProrateByproduct[$k]['FCXthAftDisChg'];

                    if($FTXthVATInOrEx == 2 || $FTXthVATInOrEx == '2'){
                        $nVatable = $FCXtdVatable - $nProrate; 
                        $nVat     = round($nVatable * $FCXthVATRate / 100, 2);
                    }else{
                        $nNetCalculate  = $FCXtdNet - $nProrate;
                        $nVat           = round($nNetCalculate * $FCXthVATRate / (100 + $FCXthVATRate), 2);
                        $nVatable       = round($nNetCalculate - $nVat,2);
                    }

                    //Update VAT , VATABLE ใน DT
                    $tUpdateProrate = "UPDATE TACTPtDT 
                                       SET 
                                             FCXtdFootAvg = '$nProrate' ,
                                             FCXtdVat = '$nVat',
                                             FCXtdVatable = '$nVatable',
                                             FTXtdStaPrcStk = 1
                                       WHERE FTXthDocNo = '$tDocumentNo' AND
                                             FNXtdSeqNo = '$nSeqPDT' AND
                                             FTPdtCode  = '$tCodePDT' ";
                    $oResultProrate = $this->db->query($tUpdateProrate);
                    if($this->db->trans_status() === FALSE){
                        $aRetrun = array(
                            'nStaReturn'    => 99,
                            'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
                        );
                        $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                        return $aRetrun;
                    }
                }

                //ส่วน  VAT , VATABLE ใน HD
                $FTXthVATInOrEx     = $oResultQueryDT[0]['FTXthVATInOrEx'];
                $tDocumentNo        = $oResultQueryDT[0]['FTXthDocNo']; // เปลี่ยนจากที่ดึง aProrateByproduct เป็น oResultQueryDT เผื่อมันเจอเคสที่ aProrateByproduct ไม่มีค่า
                if($FTXthVATInOrEx == 1 || $FTXthVATInOrEx == '1'){ //รวมใน
                    $tDocNo         = $tDocumentNo;
                    $tSQLSUMVat     = "SELECT SUM(FCXtdVat) AS FCXtdVatReq FROM TACTPtDT WHERE FTXthDocNo = '$tDocNo' ";
                    $oSUMVat        = $this->db->query($tSQLSUMVat);
                    if($this->db->trans_status() === FALSE){
                        $aRetrun = array(
                            'nStaReturn'    => 99,
                            'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
                        );
                        $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                        return $aRetrun;
                    }
                    $oSUMVat        = $oSUMVat->result_array();
                    $FCXtdVatReq    = $oSUMVat[0]['FCXtdVatReq'];
                    $FCXrhVatable   = $oResultQueryDT[0]['FCXthAftDisChg'] - $FCXtdVatReq; //$FCXthAftDisChg
                }else{ //แยกนอก
                    $tDocNo         = $tDocumentNo;
                    $tSQLSUMVat     = "SELECT SUM(FCXtdVat) AS FCXtdVatReq FROM TACTPtDT WHERE FTXthDocNo = '$tDocNo' ";
                    $oSUMVat        = $this->db->query($tSQLSUMVat);
                    if($this->db->trans_status() === FALSE){
                        $aRetrun = array(
                            'nStaReturn'    => 99,
                            'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
                        );
                        $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                        return $aRetrun;
                    }
                    $oSUMVat        = $oSUMVat->result_array();
                    $FCXtdVatReq    = $oSUMVat[0]['FCXtdVatReq'];
                    $FCXrhVatable   = $oResultQueryDT[0]['FCXthAftDisChg']; //$FCXthAftDisChg
                }

                //Update VAT , VATABLE ใน HD
                $tUpdateHD = "UPDATE TACTPtHD 
                                SET FTXthStaPrcStk  = 1,
                                    FCXthVat  = '$FCXtdVatReq',
                                    FCXthVatable  = '$FCXrhVatable'
                                WHERE FTXthDocNo = '$tDocumentNo' ";
                $this->db->query($tUpdateHD);
                if($this->db->trans_status() === FALSE){
                    $aRetrun = array(
                        'nStaReturn'    => 99,
                        'aMessageError' => "[FSxCMQPProrate] ".$this->db->error()['message']
                    );
                    $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                    return $aRetrun;
                }else{
                    $aRetrun = array(
                        'nStaReturn'    => 1,
                        'aMessageError' => "[FSxCMQPProrate] Prorate Success"
                    );
                    // $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
                    return $aRetrun;
                }

                //update 
                // return $oResultProrate;
            }
        }
    }

    //อนุมัติเอกสาร
    public function FSxCMQPApprove($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocument  = $paPackData['ptDocNo'];
        }else{
            $tDocument  = trim($tDocumentNumber);
        }
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        $tSQL = " UPDATE  TACTPtHD  ";
        $tSQL .= " SET FTXthStaDoc = 1 , ";
        $tSQL .= " FTXthStaPrcDoc = 1 , ";
        $tSQL .= " FNLogStaExport = 1  ";
        $tSQL .= " WHERE FTXthDocNo = '".$tDocument."' "; 
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPApprove] ".$this->db->error()['message']);
        }


        $tSQLSelectDocRef   = " SELECT  FTXthRefExt  ";
        $tSQLSelectDocRef   .= " FROM TACTPtHD ";
        $tSQLSelectDocRef   .= " WHERE FTXthDocNo = '".$tDocument."' "; 
        $oQueryDocRef       = $this->db->query($tSQLSelectDocRef);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMQPWriteLog("[FSxCMQPApprove] ".$this->db->error()['message']);
        }
        $oDocRef            = $oQueryDocRef->result_array();
        if(empty($oDocRef)){
            //แสดงว่าไม่ได้ ref เอกสารมา
        }else{
            $tRefDocument       = $oDocRef[0]['FTXthRefExt'];
            $tSQLUpdateRefDoc = " UPDATE TACTPrHD SET 
                    FNXrhStaRef = 2,
                    FDDateUpd = '$dCurrent',
                    FTTimeUpd = '$tTime',
                    FTWhoUpd = '$tWhoIns'
                    WHERE FTXrhDocNo = '$tRefDocument' ";
            $oQuery = $this->db->query($tSQLUpdateRefDoc);
            if($this->db->trans_status() === FALSE){
                $this->FSxMMQPWriteLog("[FSxCMQPApprove] ".$this->db->error()['message']);
            }
        }
    }

    //ปรับปฎิทินว่าทำงานเเล้ว
    public function FSxCMQPUpdateSTKDaily($paPackData){ //$tDocumentNumber
        // $tComName   = gethostname();
        // if($tDocumentNumber == null || $tDocumentNumber == 'null'){
        //     $tDocument  = $paPackData['ptDocNo'];
        // }else{
        //     $tDocument  = trim($tDocumentNumber);
        // }
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
		$tWhoIns    = $paPackData['ptWhoIns'];

		//CFM-POS ComSheet-2020-278 (supawat 18/06/2020)
		$tCodeIns   = $paPackData['ptUsrCode'];
        // $tBchCode   = $paPackData['pnBchCode'];

        $tSQL = "UPDATE TCNJobDaily";
        $tSQL .= " SET FTUsrCode='$tCodeIns'";
        $tSQL .= ",FTUsrName='$tWhoIns'";
        $tSQL .= ",FDJobDocDate='$dCurrent'";
        $tSQL .= ",FTJobDocTime='$tTime'";
        $tSQL .= ",FTJobStaPrc='1'";
        $tSQL .= " WHERE ((FDJobDate)=CONVERT(DateTime,'$dCurrent')) ";
        $tSQL .= " AND FTJobCode='TACPT'";
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxCMQPUpdateSTKDaily] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxCMQPUpdateSTKDaily] ปรับปฎิทินว่าทำงานเเล้ว"
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }
        return $aRetrun;
    }

    //ปรับบาลานซ์ออนแฮนด์
    public function FSxCMQPUpdateBalanceonHand($paPackData,$tDocumentNumber){
        $tComName   = gethostname();
        if($tDocumentNumber == null || $tDocumentNumber == 'null'){
            $tDocument  = $paPackData['ptDocNo'];
        }else{
            $tDocument  = trim($tDocumentNumber);
        }
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        $tSQL = "UPDATE TCNTBI SET ";
        $tSQL .= " FCTbiAmtRet = ISNULL(FCTbiAmtRet,0)+";
        $tSQL .= " CAST(";
        $tSQL .= " ISNULL((SELECT ISNULL(SUM((ISNULL(PtDT.FCXtdQtyAll,0) * ISNULL(PtDT.FCXtdSalePrice,0)) - ";
        $tSQL .= " ISNULL(PtDT.FCXtdDisAvg,0) - ISNULL(PtDT.FCXtdFootAvg,0) - ISNULL(PtDT.FCXtdRePackAvg,0)),0) AS FCTbiAmtRet ";
        $tSQL .= " FROM TACTPtDT PtDT ";
        $tSQL .= " INNER JOIN  TCNMPdt P ON P.FTPdtCode = PtDT.FTPdtCode ";
        $tSQL .= " INNER JOIN TACTPtHD HD ON PtDT.FTXthDocNo=HD.FTXthDocNo ";
        $tSQL .= " WHERE HD.FTXthStaDoc='1' ";
        $tSQL .= " AND PtDT.FTXthDocType IN ('5','6') AND TCNTBI.FTPgpChain = P.FTPgpChain ";
        $tSQL .= " AND P.FTPdtPmtType = '2' ";
        $tSQL .= " AND HD.FTXthStaPrcDoc = '1' AND HD.FTXthStaPrcStk = '1'";
        $tSQL .= " AND HD.FTXthStaPrcLef  IS NULL ";
        $tSQL .= " AND PtDT.FTXtdStaPrcStk = '1' ";
        $tSQL .= " AND PtDT.FTXthDocNo = '$tDocument'";
        $tSQL .= " AND P.FTPdtStaActive = '1' ";
        $tSQL .= " ),0) ";
        $tSQL .= " AS DECIMAL(20,4))";
        $tSQL .= " ,FCTbiAmtPdtMiss = ISNULL(FCTbiAmtPdtMiss,0)+";
        $tSQL .= " CAST(" ;
        $tSQL .= " ISNULL((SELECT ISNULL(SUM((ISNULL(PtDT.FCXtdQtyAll,0) * ISNULL(PtDT.FCXtdSalePrice,0)) - ";
        $tSQL .= " ISNULL(PtDT.FCXtdDisAvg,0) - ISNULL(PtDT.FCXtdFootAvg,0) - ISNULL(PtDT.FCXtdRePackAvg,0)),0) AS FCTbiAmtPdtMiss ";
        $tSQL .= " FROM TACTPtDT PtDT ";
        $tSQL .= " INNER JOIN  TCNMPdt P ON P.FTPdtCode = PtDT.FTPdtCode ";
        $tSQL .= " INNER JOIN TACTPtHD HD ON PtDT.FTXthDocNo=HD.FTXthDocNo ";
        $tSQL .= " WHERE HD.FTXthStaDoc='1' ";
        $tSQL .= " AND PtDT.FTXthDocType IN ('5','6') AND TCNTBI.FTPgpChain = P.FTPgpChain ";
        $tSQL .= " AND P.FTPdtPmtType = '2' ";
        $tSQL .= " AND HD.FTXthStaPrcDoc = '1' AND HD.FTXthStaPrcStk = '1'";
        $tSQL .= " AND HD.FTXthStaPrcLef = '1' ";
        $tSQL .= " AND PtDT.FTXtdStaPrcStk = '1' ";
        $tSQL .= " AND PtDT.FTXthDocNo = '$tDocument'";
        $tSQL .= " AND P.FTPdtStaActive = '1' ";
        $tSQL .= " ),0) ";
        $tSQL .= " AS DECIMAL(20,4))";
        $tSQL .= " WHERE FDDocDate = '$dCurrent' ";
        $oQuery = $this->db->query($tSQL);

        if($dCurrent == 1){
            $tSql  = "UPDATE TCNTBI ";
            $tSql .= "SET FCTbiAmtBuySumCost = CAST(FCTbiAmtBuyCost AS DECIMAL(20,4))";
            $tSql .= ",FCTbiAmtBuySumSal = CAST(FCTbiAmtBuySal AS DECIMAL(20,4))";
            $tSql .= ",FCTbiAmtSumSal = CAST(FCTbiAmtSal AS DECIMAL(20,4))";
            $tSql .= " WHERE  FDDocDate='$dCurrent' ";
            $oQuery = $this->db->query($tSql);
        }else{

            //สร้าง temp
            $this->FSxCMQPCreateTableBalanceonHand($paPackData);

            $tSql = "UPDATE TCNTBI ";
            $tSql .= " SET FCTbiAmtBuySumCost = ";
            $tSql .= " CAST(" ;
            $tSql .= " FCTbiAmtBuyCost + ISNULL ((SELECT SUM(ISNULL(FCTbiAmtBuySumCost,0)) AS FCTbiAmtBuySumCost";
            $tSql .= " From TmpBI ";
            $tSql .=  " WHERE  FDDocDate between '$dCurrent' AND '$dCurrent' ";
            $tSql .= " AND TmpBI.FTPgpChain = TCNTBI.FTPgpChain),0)";
            $tSql .= " AS DECIMAL(20,4))";
        
            $tSql .= ",FCTbiAmtBuySumSal = ";
            $tSql .= " CAST(" ;
            $tSql .= " FCTbiAmtBuySal + ISNULL ((SELECT SUM(ISNULL(FCTbiAmtBuySal,0)) AS FCTbiAmtBuySumSal";
            $tSql .= " From TmpBI  ";
            $tSql .= " WHERE  FDDocDate between '$dCurrent' AND '$dCurrent' ";
            $tSql .= " AND TmpBI.FTPgpChain = TCNTBI.FTPgpChain),0)";
            $tSql .= " AS DECIMAL(20,4))";
        
            $tSql .= ",FCTbiAmtSumSal = ";
            $tSql .= " CAST(" ;
            $tSql .= " FCTbiAmtSal + ISNULL ((SELECT SUM(ISNULL(FCTbiAmtSal,0)) AS FCTbiAmtSumSal";
            $tSql .=  " From TmpBi ";

            $tSql .=  " WHERE  FDDocDate between '$dCurrent' AND '$dCurrent' ";
            $tSql .=  " AND TmpBI.FTPgpChain =  TCNTBi.FTPgpChain),0)";
            $tSql .=  " AS DECIMAL(20,4))";
        
            $tSql .= " WHERE  FDDocDate='$dCurrent' ";
            $oQuery = $this->db->query($tSql);
            
            //ลบตาราง Temp
            $tSQLTmp = "IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_NAME = 'TmpBI' AND TABLE_SCHEMA = 'dbo' )";
            $tSQLTmp .= "DROP TABLE TmpBI";
            $oQueryTmp = $this->db->query($tSQLTmp);

            $tSql =  "UPDATE TCNTBi ";
            $tSql .= " SET FCTbiSumCost = ";
            $tSql .= " CAST(" ;
            $tSql .= " ISNULL(FCTbiAmtTakeCost,0) + ISNULL(FCTbiAmtBuyCost,0) - " ;
            $tSql .= " (ISNULL(FCTbiAmtSal,0) + ISNULL(FCTbiAmtCDD,0) - ISNULL(FCTbiAmtDis,0)) - ISNULL(FCTbiAmtRet,0) - ";
            $tSql .= " (ISNULL(FCTbiAmtELoss,0) + ISNULL(FCTbiAmtCLoss,0)) + ";
            $tSql .= " (ISNULL(FCTbiAmtMUp,0) + ISNULL(FCTbiAmtMDown,0))+ ";
            $tSql .= " ISNULL(FCTbiAmtTnfIn,0) - ISNULL(FCTbiAmtTnfOut,0) ";
            $tSql .= " - ISNULL(FCTbiAmtPdtMiss,0) ";
            $tSql .= " AS DECIMAL(20,4))";

            $tSql .= " ,FCTbiSumSal = ";
            $tSql .= " CAST(";
            $tSql .= " ISNULL(FCTbiAmtTakeSal,0) + ISNULL(FCTbiAmtBuySal,0) - ";
            $tSql .= " (ISNULL(FCTbiAmtSal,0) + ISNULL(FCTbiAmtCDD,0) - ISNULL(FCTbiAmtDis,0)) - ISNULL(FCTbiAmtRet,0) - ";
            $tSql .= " (ISNULL(FCTbiAmtELoss,0) + ISNULL(FCTbiAmtCLoss,0)) + ";
            $tSql .= " (ISNULL(FCTbiAmtMUp,0) + ISNULL(FCTbiAmtMDown,0))+ ";
            $tSql .= " ISNULL(FCTbiAmtTnfIn,0) - ISNULL(FCTbiAmtTnfOut,0) + ";
            $tSql .= " ISNULL(FCTbiAmtAdjUstIn,0) - ISNULL(FCTbiAmtAdjUstOut,0) ";
            $tSql .= " - ISNULL(FCTbiAmtPdtMiss,0) ";
            $tSql .= " AS DECIMAL(20,4))";
            
            $tSql .= ",FCTbiAmt = ";
            $tSql .= " CAST(";
            $tSql .= "ISNULL(FCTbiAmtSal,0) - ISNULL(FCTbiAmtDis,0) + ISNULL(FCTbiAmtCDD,0) ";
            $tSql .= "AS DECIMAL(20,4))";
            $tSql .= " WHERE  FDDocDate='$dCurrent' ";
            $oQuery = $this->db->query($tSql);
                    
        }

        // if ($this->db->trans_status() === FALSE){
        //     $this->db->trans_rollback();
        // }else{
        //     $this->db->trans_commit();
        // }
    }

    //สร้างตาราง BI
    public function  FSxCMQPCreateTableBalanceonHand($paPackData){
        $tComName   = gethostname();
        $tDocument  = $paPackData['ptDocNo'];
        $dCurrent   = date('Y-m-d');
        $tTime      = date('H:i:s');
        $tWhoIns    = $paPackData['ptWhoIns'];
        $tBchCode   = $paPackData['pnBchCode'];

        //ลบ Tmp BI
        $tSQLTmp = "IF EXISTS(SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_NAME = 'TmpBI' AND TABLE_SCHEMA = 'dbo' )";
        $tSQLTmp .= "DROP TABLE TmpBI";
        $oQueryTmp = $this->db->query($tSQLTmp);

        //สร้าง Tmp BI
        $tSQL = "CREATE TABLE [dbo].[TmpBI](";
        $tSQL .= " [FDDocDate] [datetime] NOT NULL,";
        $tSQL .= " [FTPgpChain] [varchar](30) COLLATE Thai_CI_AS NOT NULL,";
        $tSQL .= " [FTPgpLev1Chain] [varchar](3) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev1ChainDesc] [varchar](100) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev2Chain] [varchar](3) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev2ChainDesc] [varchar](100) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev3Chain] [varchar](3) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev3ChainDesc] [varchar](100) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FTPgpLev4ChainDesc] [varchar](100) COLLATE Thai_CI_AS NULL,";
        $tSQL .= " [FCTbiAmtMthCost] [float] NULL,";
        $tSQL .= " [FCTbiAmtMthSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtTakeCost] [float] NULL,";
        $tSQL .= " [FCTbiAmtTakeSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtBuyCost] [float] NULL,";
        $tSQL .= " [FCTbiAmtBuySumCost] [float] NULL,";
        $tSQL .= " [FCTbiAmtBuySal] [float] NULL,";
        $tSQL .= " [FCTbiAmtBuySumSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtCountSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtSumSal] [float] NULL,";
        $tSQL .= " [FCTbiAmtCDD] [float] NULL,";
        $tSQL .= " [FCTbiAmtDis] [float] NULL,";
        $tSQL .= " [FCTbiAmtChg] [float] NULL,";
        $tSQL .= " [FCTbiAmt] [float] NULL,";
        $tSQL .= " [FCTbiAmtRet] [float] NULL,";
        $tSQL .= " [FCTbiAmtELoss] [float] NULL,";
        $tSQL .= " [FCTbiAmtCLoss] [float] NULL,";
        $tSQL .= " [FCTbiAmtMUp] [float] NULL,";
        $tSQL .= " [FCTbiAmtMDown] [float] NULL,";
        $tSQL .= " [FCTbiAmtTnfIn] [float] NULL,";
        $tSQL .= " [FCTbiAmtTnfOut] [float] NULL,";
        $tSQL .= " [FCTbiAmtAdjUstIn] [float] NULL,";
        $tSQL .= " [FCTbiAmtAdjUstOut] [float] NULL,";
        $tSQL .= " [FCTbiSumCost] [float] NULL,";
        $tSQL .= " [FCTbiSumSal] [float] NULL,";
        $tSQL .= " [FNBiRptPage] [bigint] NULL, ";
        $tSQL .= " [FCTbiAmtPdtMiss] [float] NULL, " ;
        $tSQL .= " PRIMARY KEY CLUSTERED";
        $tSQL .= " (  [FDDocDate] ASC,    [FTPgpChain] Asc) ";
        $tSQL .= " WITH (PAD_INDEX  = OFF, ";
        $tSQL .= " STATISTICS_NORECOMPUTE  = OFF, ";
        $tSQL .= " IGNORE_DUP_KEY = OFF, ";
        $tSQL .= " ALLOW_ROW_LOCKS  = ON, ";
        $tSQL .= " ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY] ";
        $tSQL .= " ) ON [PRIMARY]";
        $oQuery = $this->db->query($tSQL);

        $tSQL = "INSERT INTO TmpBI SELECT * FROM TCNTBi ";
        $tSQL .= "WHERE FDDocDate BetWeen '$dCurrent'  AND '$dCurrent' ";
        $oQuery = $this->db->query($tSQL);
        //$this->db->trans_commit();
    }

     //---------------- Roll Back ----------------//
    public function FSxCMQPRollBack(){
        $this->db->trans_rollback();
    }

    // Create By : Napat(Jame) 26/04/2021
    // Fixed Issue Comsheet 2021-121 ถ้ามีสินค้าตัวเดียว Update FTSplCode, FTVatCode ให้กับ HD
    public function FSaMPURUpdSplVatInHD($ptDocumentID){
        $tSQL = "   UPDATE TACTPtHD WITH(ROWLOCK)
                    SET TACTPtHD.FTSplCode = DT.FTSplCode, TACTPtHD.FTVatCode = DT.FTVatCode
                    FROM (
                        SELECT PDT.* FROM TACTPtDT DT
                        LEFT JOIN (
                            SELECT FTPdtCode, FTPdtStkCode,FTSplCode,FTVatCode 
                            FROM TCNMPdt WITH(NOLOCK)
                        ) PDT ON DT.FTPdtCode = PDT.FTPdtCode AND DT.FTXtdStkCode = PDT.FTPdtStkCode
                        WHERE DT.FTXthDocNo = '$ptDocumentID'
                    ) DT
                    WHERE TACTPtHD.FTXthDocNo = '$ptDocumentID' 
                ";
        $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMPURUpdSplVatInHD] ".$this->db->error()['message']
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSaMPURUpdSplVatInHD] Update FTSplCode, FTVatCode ให้กับ HD ในกรณีที่มีสินค้าตัวเดียว"
            );
            $this->FSxMMQPWriteLog($aRetrun['aMessageError']);
        }
    }
}
