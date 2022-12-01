<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class mMQPDTADJSTKCHK extends CI_Model {

    public $nCostMaxLen = 999999999999;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function FSxMMSQPASWriteLog($ptLogMsg){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptLogMsg."\n";
        $tFileName   = APPPATH.'logs/LogBackPrc_'.date('Ymd').'.txt';
        file_put_contents($tFileName,$tLogData,FILE_APPEND);
    }

    // Last Update: Napat(Jame) 14/10/2022 เพิ่มการดึง FDIuhDocDate ไปใช้งานใน STEP Zip File
    public function FSaMRABPASGetDataHD($paData){
        $this->db->where('FTBchCode',$paData['pnBchCode']);
        $this->db->where('FTIuhDocNo',$paData['ptDocNo']);
        $this->db->select("FTIuhAdjType, FTCstCode, FDIuhDocDate, FTIuhRefTaxLoss");
        $this->db->from('TCNTPdtChkHD');
        $oQuery = $this->db->get();
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMRABPASGetDataHD] ".$this->db->error()['message']
            );
        }else{
            if($oQuery->num_rows() > 0){
                $aRetrun = array(
                    'aResult'       => $oQuery->result_array()[0],
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSaMRABPASGetDataHD] ".$paData['ptDocNo']
                );
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 00000,
                    'aMessageError' => "[FSaMRABPASGetDataHD] ไม่พบเอกสารเลขที่ ".$paData['ptDocNo']
                );
            }
        }
        return $aRetrun;
    }

    //ตรวจสอบว่าใบย่อยมีดึงมาจาก HQ ไหม
    public function FSaMRABPASCheckDataFromHQ($paData){
        $tSQL = "   SELECT TOP 1 
                        FTCstCode 
                    FROM TCNTPdtChkHD WITH(NOLOCK)
                    WHERE 1=1
                    AND FTIuhDocRef = '$paData[ptDocNo]'
                    AND FTBchCode = '$paData[pnBchCode]'
                    AND FTIuhDocType = '1'
                    AND ( ISNULL(FTCstCode,'') = '' OR FTCstCode = 'CFM-HQ' ) --Comsheet 2020-129 Edit By Napat(Jame) 02/03/63
        ";
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMRABPASCheckDataFromHQ] ".$this->db->error()['message']
            );
        }else{
            if($oQuery->num_rows() > 0){
                $aRetrun = array(
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSaMRABPASCheckDataFromHQ] พบเอกสาร HQ FTIuhDocRef=".$paData['ptDocNo']
                );
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 00000,
                    'aMessageError' => "[FSaMRABPASCheckDataFromHQ] ไม่พบเอกสาร HQ"
                );
            }
        }
        $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;
    }

    //ค้นหาสินค้าทั้งหมดที่ไม่ได้อยู่ใน DT(ใบรวม) และนำไปปรับสต๊อก ให้เป็น 0
    public function FSaMRABPASGetDataWithOutDT($paData,$pnTpyeFilter){

        $tWhereFilter = "";
        if($pnTpyeFilter == 1){
            $tWhereFilter .= " AND A.FCAdjValue != 0 ";
        }else{
            $tWhereFilter .= " AND A.FCAdjValue = 0 ";
        }

        $tSQL0 = "  SELECT DISTINCT
                        '$paData[FTIuhDocNo]'               AS FTIuhDocNo, 
                        CONVERT(VARCHAR(10),GETDATE(),121)  AS FDIuhDocDate,
                        '001'                               AS FTWahCode,
                        A.*,
                        CASE 
                            WHEN A.FCAdjValue > 0 THEN '0' --ปรับลด
                            WHEN A.FCAdjValue < 0 THEN '9' --ปรับเพิ่ม
                            -- ELSE '6' --ไม่ปรับสต๊อก แต่ไปปรับ QtyRet,QtyNow ให้เป็น 0
                        END AS tDocType
                    FROM (
                        SELECT 
                            STK.FTPdtStkCode        AS FTIudStkCode,
                            STK.FTPdtStkCode        AS FTPdtStkCode,
                            (
                                ISNULL(STK.FCQtyME,0) - ISNULL(STK.FCQtySale,0) + ISNULL(STK.FCQtyReturn,0) + ISNULL(STK.FCQtyPR,0)-
                                ISNULL(STK.FCQtyPC,0) - ISNULL(STK.FCQtyTC,0)   - ISNULL(STK.FCQtyTO,0)     + ISNULL(STK.FCQtyTR,0)+
                                ISNULL(STK.FCQtyAI,0) - ISNULL(STK.FCQtyAO,0)
                            ) AS FCAdjValue
                        FROM (
                            SELECT
                                STKL.FTBchCode,
                                STKL.FTPdtStkCode,
                                SUM(CASE WHEN STKL.FTStkType = '0' AND SUBSTRING(FTStkDocNo,1,2)='ME' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyME,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPR,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTR,
                                SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAI,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPC,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTC,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTO,
                                SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAO,
                                SUM(CASE WHEN STKL.FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S'  THEN STKL.FCStkQty ELSE 0 END) AS FCQtySale,
                                SUM(CASE WHEN STKL.FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R'  THEN STKL.FCStkQty ELSE 0 END) AS FCQtyReturn
                            FROM TCNTPdtStkCard STKL WITH(NOLOCK)
                            INNER JOIN (
                                SELECT DISTINCT
                                    P.FTPdtStkCode
                                FROM TCNMPdt P WITH(NOLOCK)
                                LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtStkCode = DT.FTIudStkCode AND DT.FTIuhDocNo = '$paData[FTIuhDocNo]' AND DT.FTIuhDocType = '2'
                                WHERE P.FTPdtStaActive = '1'
                                AND P.FTPdtType = '1'
                                AND P.FTPdtStaAudit IN ('1','2')
                                AND DT.FTIudStkCode IS NULL
                                GROUP BY P.FTPdtStkCode
                            ) PDT ON PDT.FTPdtStkCode = STKL.FTPdtStkCode
                            WHERE 1=1
                            AND STKL.FTBchCode = '$paData[FTBchCode]'
                            AND CONVERT(VARCHAR(7),STKL.FDStkDate,121) = CONVERT(VARCHAR(7),GETDATE(),121)
                            GROUP BY STKL.FTBchCode,STKL.FTPdtStkCode
                        ) STK
                    ) A
                    WHERE 1=1
                    $tWhereFilter
        ";
        $oQuery0  = $this->db->query($tSQL0);
        // $aResult0  = $oQuery0->result_array();
        if($this->db->trans_status() === FALSE){
            $aDataReturn    = array(
                'aItems'       => array(),
                'nRows'        => 0,
                'tCode'        => '800',
                'tDesc'        => '[FSaMRABPASGetDataWithOutDT] '.$this->db->error()['message']
            );
        }else{
            if($oQuery0->num_rows() > 0) {
                $aDataReturn    = array(
                    // 'tSQL'         => $tSQL,
                    'aItems'       => $oQuery0->result_array(),
                    'nRows'        => $oQuery0->num_rows(),
                    'tCode'        => '1',
                    'tDesc'        => '[FSaMRABPASGetDataWithOutDT] พบสินค้าไม่อยู่ใน DT = '.$oQuery0->num_rows().' รายการ'
                );
            }else{
                $aDataReturn    = array(
                    // 'tSQL'         => $tSQL,
                    'aItems'       => array(),
                    'nRows'        => 0,
                    'tCode'        => '800',
                    'tDesc'        => '[FSaMRABPASGetDataWithOutDT] ไม่พบสินค้า',
                );
            }
        }
        $this->FSxMMSQPASWriteLog($aDataReturn['tDesc']);
        return $aDataReturn;
    }

    //ค้นหาเคลื่อนไหวสินค้าในสต๊อกการ์ด และนำสินค้าที่ไม่มีการเคลื่อนไหว ไปปรับ QtyRet,QtyNow = 0 ป้องกันปัญหา ตัวเลขเพี้ยน
    public function FSaMRABPASUpdPdtWithOutDT($aPackData){
        // $tSQL = "   UPDATE TCNMPdt WITH(ROWLOCK)
        //             SET
        //                 TCNMPdt.FCPdtQtyRet = 0,
        //                 TCNMPdt.FCPdtQtyNow = 0
        //             FROM TCNMPdt P 
        //             INNER JOIN (
        //                 SELECT 
        //                     A.FTPdtStkCode,
        //                     A.FCAdjValue
        //                 FROM (
        //                     SELECT 
        //                         STK.FTPdtStkCode        AS FTPdtStkCode,
        //                         (
        //                             ISNULL(STK.FCQtyME,0) - ISNULL(STK.FCQtySale,0) + ISNULL(STK.FCQtyReturn,0) + ISNULL(STK.FCQtyPR,0)-
        //                             ISNULL(STK.FCQtyPC,0) - ISNULL(STK.FCQtyTC,0)   - ISNULL(STK.FCQtyTO,0)     + ISNULL(STK.FCQtyTR,0)+
        //                             ISNULL(STK.FCQtyAI,0) - ISNULL(STK.FCQtyAO,0)
        //                         ) AS FCAdjValue
        //                     FROM (
        //                         SELECT
        //                             STKL.FTBchCode,
        //                             STKL.FTPdtStkCode,
        //                             SUM(CASE WHEN STKL.FTStkType = '0' AND SUBSTRING(FTStkDocNo,1,2)='ME' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyME,
        //                             SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPR,
        //                             SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTR,
        //                             SUM(CASE WHEN STKL.FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAI,
        //                             SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyPC,
        //                             SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTC,
        //                             SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyTO,
        //                             SUM(CASE WHEN STKL.FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN STKL.FCStkQty ELSE 0 END) AS FCQtyAO,
        //                             SUM(CASE WHEN STKL.FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S'  THEN STKL.FCStkQty ELSE 0 END) AS FCQtySale,
        //                             SUM(CASE WHEN STKL.FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R'  THEN STKL.FCStkQty ELSE 0 END) AS FCQtyReturn
        //                         FROM TCNTPdtStkCard STKL WITH(NOLOCK)
        //                         INNER JOIN (
        //                             SELECT DISTINCT
        //                                 P.FTPdtStkCode
        //                             FROM TCNMPdt P WITH(NOLOCK)
        //                             LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtStkCode = DT.FTIudStkCode 
        //                                 AND DT.FTIuhDocNo = '$aPackData[FTIuhDocNo]' 
        //                                 AND DT.FTIuhDocType = '2'
        //                             WHERE 1=1
        //                             AND P.FTPdtStaActive = '1'
        //                             AND P.FTPdtType = '1'
        //                             AND P.FTPdtStaAudit IN ('1','2')
        //                             AND DT.FTIudStkCode IS NULL
        //                             GROUP BY P.FTPdtStkCode
        //                         ) PDT ON PDT.FTPdtStkCode = STKL.FTPdtStkCode
        //                         WHERE 1=1
        //                         AND STKL.FTBchCode = '$aPackData[FTBchCode]'
        //                         AND CONVERT(VARCHAR(7),STKL.FDStkDate,121) = CONVERT(VARCHAR(7),GETDATE(),121)
        //                         GROUP BY STKL.FTBchCode,STKL.FTPdtStkCode
        //                     ) STK
        //                 ) A
        //                 WHERE 1=1
        //                 AND A.FCAdjValue = 0
        //             ) B ON B.FTPdtStkCode = P.FTPdtStkCode
        // ";
        $tSQL = "   UPDATE TCNMPdt WITH(ROWLOCK)
                    SET
                        TCNMPdt.FCPdtQtyRet = 0,
                        TCNMPdt.FCPdtQtyNow = 0,
                        TCNMPdt.FDDateUpd   = CONVERT(VARCHAR(10),GETDATE(),121),
                        TCNMPdt.FTTimeUpd   = CONVERT(VARCHAR(8),GETDATE(),24),
                        TCNMPdt.FTWhoUpd    = '$aPackData[FTWhoUpd]'
                    FROM TCNMPdt P
                    LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtStkCode = DT.FTIudStkCode AND DT.FTIuhDocNo = '$aPackData[FTIuhDocNo]' AND DT.FTIuhDocType = '2'
                    WHERE DT.FTIuhDocNo IS NULL
                    AND P.FTPdtStaActive = '1'
                    AND P.FTPdtType = '1'
                    AND P.FTPdtStaAudit IN ('1','2')
        ";
        $this->db->query($tSQL);

        $tSQL  = "  INSERT INTO TCNTPdtInWha (FTWahCode, FTPdtCode, FTPtdStkCode, FCWahQty, FDDateUpd, FTTimeUpd,FTWhoUpd, FDDateIns, FTTimeIns, FTWhoIns) ";
        $tSQL .= "  SELECT DISTINCT 
                        '001', 
                        P.FTPdtStkCode AS FTPdtCode, 
                        P.FTPdtStkCode,
                        0,
                        CONVERT(VARCHAR(10),GETDATE(),121),
                        CONVERT(VARCHAR(8),GETDATE(),24),
                        '$aPackData[FTWhoUpd]',
                        CONVERT(VARCHAR(10),GETDATE(),121),
                        CONVERT(VARCHAR(8),GETDATE(),24),
                        '$aPackData[FTWhoUpd]'
                    FROM TCNMPdt P WITH(NOLOCK)
                    LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtStkCode = DT.FTIudStkCode AND DT.FTIuhDocNo = '$aPackData[FTIuhDocNo]' AND DT.FTIuhDocType = '2'
                    LEFT JOIN TCNTPdtInWha W WITH(NOLOCK) ON P.FTPdtStkCode = W.FTPtdStkCode AND W.FTWahCode = '001'
                    WHERE W.FTPtdStkCode IS NULL
                    AND DT.FTIuhDocNo IS NULL
                    AND P.FTPdtStaActive = '1'
                    AND P.FTPdtType = '1'
                    AND P.FTPdtStaAudit IN ('1','2')
        ";
        $this->db->query($tSQL);

        $tSQL = "   UPDATE TCNTPdtInWha WITH(ROWLOCK)
                    SET
                        TCNTPdtInWha.FCWahQty    = 0,
                        TCNTPdtInWha.FDDateUpd   = CONVERT(VARCHAR(10),GETDATE(),121),
                        TCNTPdtInWha.FTTimeUpd   = CONVERT(VARCHAR(8),GETDATE(),24),
                        TCNTPdtInWha.FTWhoUpd    = '$aPackData[FTWhoUpd]'
                    FROM TCNMPdt P
                    LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtStkCode = DT.FTIudStkCode AND DT.FTIuhDocNo = '$aPackData[FTIuhDocNo]' AND DT.FTIuhDocType = '2'
                    WHERE DT.FTIuhDocNo IS NULL
                    AND P.FTPdtStaActive = '1'
                    AND P.FTPdtType = '1'
                    AND P.FTPdtStaAudit IN ('1','2')
                    AND TCNTPdtInWha.FTWahCode = '001'
                    AND TCNTPdtInWha.FTPtdStkCode = P.FTPdtStkCode
        ";
        $this->db->query($tSQL);
        
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSaMRABPASUpdPdtWithOutDT] ".$this->db->error()['message']
            );
        }
        else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSaMRABPASUpdPdtWithOutDT] อัพเดทสินค้าไม่มีการเคลื่อนไหว QtyRet,QtyNow = 0"
            );
        }
        $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;
    }

    public function FStMRABPASGenAMDocNo($tTablename,$tFiledDocno,$tFiledDocType){
        $tSQL1 = "SELECT 
                    TOP 1 *,
                    (SELECT TOP 1 FTBchCode FROM TCNMComp WITH(NOLOCK)) AS FTBchCode
                 FROM 
                    TSysAuto WITH (NOLOCK)
                 WHERE 
                    ((FTSatTblName = '$tTablename') 
                    AND (FTSatFedCode='$tFiledDocno')) 
                    AND (FTSatStaDocType IN ('$tFiledDocType'))";
        $oQuery1  = $this->db->query($tSQL1);
        $aResult  = $oQuery1->result_array();

        if($oQuery1->num_rows() > 0){
            $tBCH               = $aResult[0]['FTBchCode'];
            $tFormat            = $aResult[0]['FTSatUsrFmtAll'];
            $tResultFormat      = str_replace('BCH', $tBCH , $tFormat);
            $tResultFormat      = str_replace('YY', date("y") , $tResultFormat);
            $tResultFormat      = str_replace('MM', date("m") , $tResultFormat);

            $aValueFormatKey    = explode("-",$tResultFormat);
            $tValueFormatKey    = substr($aValueFormatKey[0],0,8);

            $tCheckcondition    = strstr($tResultFormat,"#");
            $nDigitformat       = strlen($tCheckcondition);
            $nDigitformat       = '%0'.$nDigitformat.'d';

            $tSQL2 = "SELECT 
                        MAX($tFiledDocno) As $tFiledDocno 
                    FROM 
                        $tTablename WITH (NOLOCK)
                    WHERE 
                        Len($tFiledDocno)=16 
                        AND Left($tFiledDocno,8)='$tValueFormatKey' 
                        AND (IsNumeric(Right($tFiledDocno,5))=1) 
                        AND FTPthDocType IN ('$tFiledDocType')";

            $oQuery2         = $this->db->query($tSQL2);
            $aResultNumber   = $oQuery2->result_array();

            if($oQuery2->num_rows() > 0 && $aResultNumber[0][$tFiledDocno] <> ""){
                $tValue         = explode("-",$aResultNumber[0][$tFiledDocno]);
                $tNumberDocno   = $tValue[1] + 1;
                $nNumber        = sprintf($nDigitformat,$tNumberDocno);
                $tNumberDocno   = $nNumber;
            }else{
                $tNumberDocno   = sprintf($nDigitformat,1);
            }

            $tResultFormat      = str_replace('#####', $tNumberDocno , $tResultFormat);
            return $tResultFormat;
        }else{
            return false;
        }
    }

    public function FSaMRABPASGetDataPdtChkDT($paData){
        $tSQL = "SELECT
                    CHK.FTIuhDocNo,
                    CHK.FDIuhDocDate,
                    CHK.FTWahCode,
                    CHK.FTIudStkCode,
                    -- CHK.FTPunName,
                    CHK.FTIudStkCode AS FTPdtStkCode,
                    CHK.FCIudQtyDiff AS FCAdjValue,
                    CASE 
                        WHEN CHK.FCIudQtyDiff > 0 THEN '9' 
                        WHEN CHK.FCIudQtyDiff < 0 THEN '0'
                        ELSE NULL
                    END AS tDocType
                 FROM 
                    TCNTPdtChkDT CHK WITH (NOLOCK)
                 WHERE FTIuhDocNo      = '$paData[FTIuhDocNo]'
                   AND FTIuhDocType    = '$paData[FTIuhDocType]'
                   AND FCIudQtyDiff	  != 0
                 ORDER BY FNIudSeqNo ASC";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
                'aItems'       => $oQuery->result_array(),
                'nRows'        => $oQuery->num_rows(),
                'tCode'        => '1',
                'tDesc'        => '[FSaMRABPASGetDataPdtChkDT] สินค้าปรับสต๊อก = '.number_format($oQuery->num_rows()).' รายการ'
            );
        }else{
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
                'nRows'        => 0,
				'tCode'        => '800',
				'tDesc'        => '[FSaMRABPASGetDataPdtChkDT] ไม่มีสินค้าปรับสต๊อก',
			);
        }
        $this->FSxMMSQPASWriteLog($aDataReturn['tDesc']);
        return $aDataReturn;
    }

    //default price level
    public function FSaMRABPASGetDefPriLvl(){
        $tSQL = "SELECT
                    CASE WHEN RET.FTSysUsrValue='' THEN RET.FTSysDefValue ELSE RET.FTSysUsrValue END AS PdtPriLevRet,
                    CASE WHEN WHS.FTSysUsrValue='' THEN WHS.FTSysDefValue ELSE WHS.FTSysUsrValue END AS PdtPriLevWhs
                 FROM TSysConfig RET WITH (NOLOCK), TSysConfig WHS WITH (NOLOCK)
                 WHERE (RET.FTSysCode='PPriDef') AND (WHS.FTSysCode='SPriDef')";
        $oQuery = $this->db->query($tSQL); 
        if ($oQuery->num_rows() > 0) {
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
                'aItems'       => $oQuery->result_array()[0],
                'tCode'        => '1',
                'tDesc'        => '[FSaMRABPASGetDefPriLvl] PdtPriLevRet='.$oQuery->result_array()[0]['PdtPriLevRet'].',PdtPriLevWhs='.$oQuery->result_array()[0]['PdtPriLevWhs']
            );
        }else{
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
				'tCode'        => '800',
				'tDesc'        => '[FSaMRABPASGetDefPriLvl] ไม่พบ TSysConfig (RET.FTSysCode=PPriDef) AND (WHS.FTSysCode=SPriDef)'
			);
        }
        $this->FSxMMSQPASWriteLog($aDataReturn['tDesc']);
        return $aDataReturn;
    }

    public function FScMRABPASSUMcVatInEx($pbIsVatEx, $pcTotal, $pcVatRate){
        if($pbIsVatEx){
            $cSumVatTotal   = $pcTotal * ((100 + $pcVatRate) / 100);
            $cReturn        = $cSumVatTotal - $pcTotal;
        }else{
            $cSumVatTotal   = ($pcTotal * 100) / (100 + $pcVatRate);
            $cReturn        = $pcTotal - $cSumVatTotal;
        }
        return $cReturn;
    }

    public function FScMRABPASSUMcGrandAfterVat($pbIsVatEx, $pcTotal, $pcVatRate){
        if($pbIsVatEx){
            $cSumVatTotal = $pcTotal * ((100 + $pcVatRate) / 100);
        }else{
            $cSumVatTotal = ($pcTotal * 100) / (100 + $pcVatRate);
        }
        return $cSumVatTotal;
    }

    public function FSaMRABPASGetVatRate(){
        $tSQL = "SELECT FTVatCode,FCVatRate FROM TCNMVatRate WITH (NOLOCK) WHERE FTVatCode = (SELECT TOP 1 FTVatCode FROM TCNMComp WITH(NOLOCK))";
        $oQuery = $this->db->query($tSQL); 
        if ($oQuery->num_rows() > 0) {
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
                'aItems'       => $oQuery->result_array()[0],
                'tCode'        => '1',
                'tDesc'        => '[FSaMRABPASGetVatRate] FTVatCode='.$oQuery->result_array()[0]['FTVatCode'].',FCVatRate='.$oQuery->result_array()[0]['FCVatRate']
            );
        }else{
            $aDataReturn    = array(
                // 'tSQL'         => $tSQL,
				'tCode'        => '800',
				'tDesc'        => '[FSaMRABPASGetVatRate] ไม่พบ FTVatCode (TCNMComp => TCNMVatRate)',
			);
        }
        $this->FSxMMSQPASWriteLog($aDataReturn['tDesc']);
        return $aDataReturn;
    }

    //ไม่รวมภาษีมูลค่าเพิ่ม
    public function FSnMRABPASGetNonVat($ptDocNo){
        $tSQL = "SELECT
                    SUM(DT.FCIudSetPrice) AS nNonVat
                 FROM 
                    TCNTPdtChkDT DT WITH (NOLOCK)
                 INNER JOIN TCNMPdt PDT ON DT.FTPdtCode = PDT.FTPdtCode
                 WHERE 
                    PDT.FTPdtVatType = '2' 
                    AND DT.FTIuhDocNo='$ptDocNo'";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $nResult = $oQuery->result_array()[0]['nNonVat'];
        }else{
            $nResult = 0;
        }
        return $nResult;
    }

    public function FSaMRABPASGenAdjDT($paData){
        $cQty       = abs(round($paData['aData']['FCAdjValue'],2));
        $cVatRate   = $paData['cVatRate'];
        $tWahCode   = $paData['aData']['FTWahCode'];
        $tStkCode   = $paData['aData']['FTIudStkCode'];
        if($paData['tDocType'] == '9'){     // IN
            $tWhFrm = "";
            $tWhTo  = $tWahCode;
        }else{                              // OUT
            $tWhFrm = $tWahCode;
            $tWhTo  = "";
        }

        $tPdtPriLevRet = 'TCNMPdtBar.FCPdtRetPri'.$paData['aDefPriLvl']['PdtPriLevRet'];
        $tSQL1 = "SELECT TOP 1 
                    TCNMPdt.FTPdtCode,
                    TCNMPdt.FTPunCode,
                    TCNMPdt.FCPdtCostStd,
                    TCNMPdt.FCPdtCostAvg,
                    TCNMPdt.FTPdtName,
                    TCNMPdt.FTPdtStkCode,
                    TCNMPdt.FCPdtStkFac,
                    TCNMPdt.FTPdtVatType,
                    TCNMPdt.FTPdtSaleType,
                    TCNMPdt.FTPgpChain,
                    TCNMPdt.FTSplCode,
                    TCNMPdtBar.FTPdtBarCode,
                    TCNMPdtUnit.FTPunName,
                    $tPdtPriLevRet AS FCSalesPri
                FROM TCNMPdt WITH (NOLOCK),TCNMPdtBar WITH (NOLOCK),TCNMPdtUnit WITH(NOLOCK)
                WHERE TCNMPdt.FTPdtStkCode = '$tStkCode'
                AND TCNMPdt.FCPdtStkFac = 1
                AND TCNMPdt.FTPdtCode = TCNMPdtBar.FTPdtCode
                AND TCNMPdt.FTPunCode = TCNMPdtUnit.FTPunCode
        ";
        $oQuery1  = $this->db->query($tSQL1);
        if ($oQuery1->num_rows() > 0){
            $aResult1       = $oQuery1->result_array()[0];
            $nPdtFactor     = $aResult1['FCPdtStkFac'];
            $tPdtUnitCode   = $aResult1['FTPunCode'];
            $cCostStd       = round($aResult1['FCPdtCostStd'],2);
            $cSetPri        = round($aResult1['FCPdtCostStd'],2);
            $cSalesPri      = round($aResult1['FCSalesPri'],2);
            $cCostAvg       = round($aResult1['FCPdtCostAvg'],2);
            // if($cCostAvg > $paData['tCostMaxLen'] || $cCostAvg < ($paData['tCostMaxLen'] * -1)){
            if($cCostAvg > $this->nCostMaxLen || $cCostAvg < ($this->nCostMaxLen * -1)){
                $cCostAvg = $cCostStd;
            }
            // $tPdtUnitName   = $paData['aData']['FTPunName'];

            // ตรวจสอบ ภาษี
            if($aResult1['FTPdtVatType'] == '1'){ // มีภาษี
                if($paData['bIsVatExclude']){
                    $cVat       = round($this->FScMRABPASSUMcVatInEx(TRUE, $cCostStd * $cQty, $cVatRate),2); //ยอดรวมภาษี Ex=>Net * ((VatR+100)/100),In=>Net*VatR/(VatR+100)
                    $cVatable   = round(($cCostStd * $cQty),2);
                }else{
                    $cVat       = round($this->FScMRABPASSUMcVatInEx(FALSE, $cCostStd * $cQty, $cVatRate),2); //ยอดรวมภาษี Ex=>Net * ((VatR+100)/100),In=>Net*VatR/(VatR+100)
                    $cVatable   = round((($cCostStd * $cQty) - $cVat),2);
                }
                //หาราคาซื้อ/หน่วย
                $cCostEx        = @round((($cVatable / $cQty) > 0 ? ($cVatable / $cQty) : 0),2);
                $cCostIn        = round($this->FScMRABPASSUMcGrandAfterVat(TRUE, $cCostEx, $cVatRate),2); //Include Vat
            }else{ // ไม่มีภาษี
                $cVat           = 0;
                $cVatable       = round(($cCostStd * $cQty),2); //ยอดรวมก่อนภาษี Ex=>Net ,In=>Net-Vat
                //หาราคาซื้อ/หน่วย
                $cCostEx        = @round((($cVatable / $cQty) > 0 ? ($cVatable / $cQty) : 0),2);
                $cCostIn        = @round((($cVatable / $cQty) > 0 ? ($cVatable / $cQty) : 0),2);
            }

            // print_r($aResult1);

            $aDataInsert = array(
                'FTBchCode'                 => $paData['tBchCode'],
                'FTPthDocNo'                => $paData['tDocNoIn'], 
                'FNPtdSeqNo'                => $paData['nSeq'], 
                'FTPdtCode'                 => $aResult1['FTPdtCode'],
                'FTPdtName'                 => $aResult1['FTPdtName'], 
                'FTPthDocType'              => $paData['tDocType'], 
                'FDPthDocDate'              => $paData['aData']['FDIuhDocDate'], 
                'FTPthVATInOrEx'            => ($paData['bIsVatExclude'] ? "2" : "1"),  
                'FTPtdBarCode'              => $aResult1['FTPdtBarCode'],
                'FTPtdStkCode'              => $aResult1['FTPdtStkCode'], 
                'FCPtdStkFac'               => $aResult1['FCPdtStkFac'],
                'FTPtdVatType'              => $aResult1['FTPdtVatType'],  
                'FTPtdSaleType'             => $aResult1['FTPdtSaleType'], 
                'FTPgpChain'                => $aResult1['FTPgpChain'], 
                'FTSrnCode'                 => '', 
                'FTPmhCode'                 => '',
                'FTPtdApOrAr'               => $aResult1['FTSplCode'],  
                'FNPtdPdtLevel'             => 0,
                'FTPtdPdtParent'            => $aResult1['FTPdtCode'],  
                'FTPunCode'                 => $tPdtUnitCode, 
                'FTPtdUnitName'             => $aResult1['FTPunName'], //$tPdtUnitName 
                'FCPtdFactor'               => $nPdtFactor,
                'FCPtdSalePrice'            => $cSalesPri, 
                'FCPtdQty'                  => $cQty,
                'FCPtdSetPrice'             => $cSetPri, 
                'FCPtdB4DisChg'             => $cCostStd,
                'FTPtdDisChgTxt'            => '0', 
                'FCPtdDis'                  => 0,
                'FCPtdNet'                  => $cQty * $cCostStd, 
                'FCPtdVat'                  => $cVat,
                'FCPtdVatable'              => $cVatable, 
                'FCPtdQtyAll'               => $cQty,
                'FCPtdCostIn'               => $cCostIn, 
                'FCPtdCostEx'               => $cCostEx,
                'FTPtdStaPdt'               => '1',
                'FTPtdStaRfd'               => '1', 
                'FCPtdChg'                  => 0,
                'FNPthSign'                 => 0,
                'FNPtdStaRef'               => 0, 
                'FCPtdCost'                 => ($paData['bIsVatExclude'] ? $cCostEx : $cCostIn),
                'FTWahCode'                 => $tWahCode,
                'FTPthWhFrm'                => $tWhFrm,
                'FTPthWhTo'                 => $tWhTo,
                'FDDateUpd'                 => date('Y-m-d'),
                'FTTimeUpd'                 => date('H:i:s'),
                'FTWhoUpd'                  => $paData['tUsrName'],
                'FDDateIns'                 => date('Y-m-d'),
                'FTTimeIns'                 => date('H:i:s'),
                'FTWhoIns'                  => $paData['tUsrName']
            );
            // print_r($aDataInsert);
            $this->db->insert('TCNTPdtTnfDT', $aDataInsert);
            if($this->db->trans_status() === FALSE){
                $aRetrun = array(
                    'nStaReturn'    => 99,
                    'aMessageError' => "[FSaMRABPASGenAdjDT] ".$this->db->error()['message']
                );
                $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSaMRABPASGenAdjDT] TCNTPdtTnfDT(".$paData['tDocNoIn'].") เพิ่มสินค้า FTPdtStkCode=".$tStkCode
                );
            }
        }else{
            $aRetrun    = array(
				'nStaReturn'    => 800,
                'aMessageError' => "[FSaMRABPASGenAdjDT] ไม่พบสินค้าใน TCNMPdt WHERE FTPdtStkCode=".$tStkCode
            );
            // $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        }
        return $aRetrun;
    }

    public function FSaMRABPASGenAdjHD($paData){
        //ตรวจสอบว่าสร้าง HD แล้วหรือยัง ?
        $tSQLDup       = "SELECT FTPthDocNo FROM TCNTPdtTnfHD WITH (NOLOCK) WHERE FTPthDocNo='".$paData['tDocNoIn']."'";
        $oQueryDup     = $this->db->query($tSQLDup);
        if($oQueryDup->num_rows() > 0){
            $aRetrun    = array(
                'nStaReturn'    => 800,
                'aMessageError' => array()
            );
        }else{
            $cGrand = 0;
            //sum from DT
            $tSQL = "SELECT 
                        Sum(FCPtdB4DisChg)      AS FCPtdSumB4DisChg, 
                        Sum(FCPtdDis)           AS FCPtdSumDis,
                        Sum(FCPtdChg)           AS FCPtdSumChg, 
                        Sum(FCPtdNet)           AS FCPtdSumNet, 
                        Sum(FCPtdVat)           AS FCPtdSumVat,
                        Sum(FCPtdVatable)       AS FCPtdSumVatable
                    FROM TCNTPdtTnfDT WITH (NOLOCK)
                    WHERE (FTPthDocNo='".$paData['tDocNoIn']."')";
            $oQuery     = $this->db->query($tSQL);
            $aResult    = $oQuery->result_array()[0];
            if ($oQuery->num_rows() > 0){
                $cSumNet        = round($aResult['FCPtdSumNet'],2); //ยอดรวม หลังลด/ชาร์จ
                $cSumB4DisChg   = round($aResult['FCPtdSumB4DisChg'],2); //ยอดรวม ก่อนลด/ชาร์จ (Total - NonVat)
                $cSumDis        = round($aResult['FCPtdSumDis'],2); //ยอดรวม ส่วนลด
                $cSumChg        = round($aResult['FCPtdSumChg'],2); //ยอดรวม ส่วนชาร์จ
                $cSumVat        = round($aResult['FCPtdSumVat'],2); //ยอดรวม ภาษี Ex=>FCPthAftDisChg * FCPthVATRate
                $cSumVatable    = round($aResult['FCPtdSumVatable'],2); //ยอดรวม ก่อนภาษี (Ex=FCPthAftDisChg)
            }
            $cGrand = round($cSumNet + $cSumVat,2);
            if($paData['nNonVat'] == 0){
                $cNonVat = $cSumVatable;
            }else{
                $cNonVat = 0;
            }

            if($paData['tDocType'] == "7" || $paData['tDocType'] == "9"){           //In
                $tWhFrm = "";
                $tWhTo  = "001";
            }else if($paData['tDocType'] == "8" || $paData['tDocType'] == "0"){     //Out
                $tWhFrm = "001";
                $tWhTo  = "";
            }
            $aDataInsert = array(
                'FTBchCode'                 => $paData['tBchCode'],
                'FTPthDocNo'                => $paData['tDocNoIn'],
                'FTPthDocType'              => $paData['tDocType'],
                'FDPthDocDate'              => date('Y-m-d'),
                'FTPthDocTime'              => date('H:i:s'),
                'FTPthVATInOrEx'            => '1',
                'FTDptCode'                 => '',
                'FTDepName'                 => '',
                'FTUsrCode'                 => $paData['tUsrCode'],
                'FTUsrName'                 => $paData['tUsrName'],
                'FTPthWhFrm'                => $tWhFrm,
                'FTPthWhTo'                 => $tWhTo,
                'FTPthType'                 => '3',
                'FTPthOther'                => '',
                'FTPrdCode'                 => '',
                'FTWahCode'                 => '001',
                'FTPthApvCode'              => '',
                'FTPthRefExt'               => $paData['aData']['FTIuhDocNo'],
                'FDPthRefExtDate'           => $paData['aData']['FDIuhDocDate'],
                'FDPthRefIntDate'           => date('Y-m-d'),
                'FDPthTnfDate'              => date('Y-m-d'),
                'FDPthBillDue'              => date('Y-m-d'),
                'FCPthVATRate'              => $paData['cVatRate'],
                'FTVATCode'                 => $paData['tVatCode'],
                'FCPthTotal'                => $cGrand,
                'FCPthNonVat'               => $cNonVat,
                'FCPthB4DisChg'             => $cSumB4DisChg,
                'FCPthDis'                  => $cSumDis,
                'FCPthChg'                  => $cSumChg,
                'FCPthAftDisChg'            => $cSumNet,
                'FCPthVat'                  => $cSumVat,
                'FCPthVatable'              => $cSumVatable,
                'FCPthGrand'                => $cGrand,
                'FCPthRnd'                  => 0,
                'FCPthWpTax'                => 0,
                'FCPthReceive'              => 0,
                'FTPthGndText'              => '',
                'FCPthLeft'                 => 0,
                'FTPthStaPaid'              => '1',
                'FTPthStaRefund'            => '1',
                'FTPthStaType'              => '1',
                'FTPthStaDoc'               => '1',
                'FCPthCcyExg'               => 0,
                'FTPthRmk'                  => '',
                'FNPthSign'                 => 0,
                'FTPthCshOrCrd'             => '1',
                'FCPthPaid'                 => 0,
                'FTPthDstPaid'              => '1',
                'FNPthStaDocAct'            => 1,
                'FNPthStaRef'               => 0,
                'FTPthStaVatSend'           => '1',
                'FDDateUpd'                 => date('Y-m-d'),
                'FTTimeUpd'                 => date('H:i:s'),
                'FTWhoUpd'                  => $paData['tUsrName'],
                'FDDateIns'                 => date('Y-m-d'),
                'FTTimeIns'                 => date('H:i:s'),
                'FTWhoIns'                  => $paData['tUsrName']
            );
            $this->db->insert('TCNTPdtTnfHD', $aDataInsert);
            if($this->db->trans_status() === FALSE){
                $aRetrun = array(
                    'nStaReturn'    => 99,
                    'aMessageError' => "[FSaMRABPASGenAdjHD] ".$this->db->error()['message']
                );
                $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSaMRABPASGenAdjHD] สร้าง TCNTPdtTnfHD=".$paData['tDocNoIn']
                );
                $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            }
        }
        return $aRetrun;
    }

    public function FSaMRABPASUpdateApproveHD($paDataApv){
        // $aDataUpdDT = array(
        //     'FTIudStaPrc'       => '1',
        //     'FDDateUpd'         => date('Y-m-d'),
        //     'FTTimeUpd'         => date('H:i:s'),
        //     'FTWhoUpd'          => $paDataApv['tUsrName']
        // );
        // $this->db->where('FTIuhDocNo', $paDataApv['tDocNo']);
        // $this->db->where('FTBchCode', $paDataApv['tBchCode']);
        // $this->db->update('TCNTPdtChkDT', $aDataUpdDT);

        // tSql = "UPDATE TCNTPdtChkHD
        // SET FTIuhStaPrcDoc='1'"
        // tSql = tSql & ",FTIuhApvCode='" & tVB_CNUserAlwC & "'"
        // tSql = tSql & "," & cUT.UT_SQLtLastUpd(tVB_CNUserName)
        // tSql = tSql & " WHERE FTIuhDocNo='" & Trim(tDocNo) & "'"

        $aDataUpdHD = array(
            'FTIuhStaPrcDoc'    => '1',
            'FTIuhApvCode'      => $paDataApv['tUsrCode'],
            'FDDateUpd'         => date('Y-m-d'),
            'FTTimeUpd'         => date('H:i:s'),
            'FTWhoUpd'          => $paDataApv['tUsrName']
        );
        // $this->db->where('FTIuhDocRef', $paDataApv['tDocNo']);
        $this->db->where('FTIuhDocNo', $paDataApv['tDocNo']);
        $this->db->update('TCNTPdtChkHD', $aDataUpdHD);
        if($this->db->affected_rows() > 0){
            $aDataReturn    = array(
                'tCode'        => '1',
                'tDesc'        => '[FSaMRABPASUpdateApproveHD] อนุมัติเอกสาร '.$paDataApv['tDocNo']
            );
        }else{
            $aDataReturn    = array(
                'tCode'        => '800',
                'tDesc'        => '[FSaMRABPASUpdateApproveHD] อนุมัติเอกสารไม่สำเร็จ'
            );
        }
        $this->FSxMMSQPASWriteLog($aDataReturn['tDesc']);
        return $aDataReturn;
    }

    public function FSxMRABPASUpdateJobDaily($paDataUpd){
        $tSQL = "UPDATE 
                    TCNJobDaily WITH (ROWLOCK)
                 SET 
                    FTUsrCode           = '$paDataUpd[ptUsrCode]',
                    FTUsrName           = '$paDataUpd[ptUsrName]',
                    FDJobDocDate        = CONVERT(VARCHAR(10),GETDATE(),121),
                    FTJobDocTime        = CONVERT(VARCHAR(10),GETDATE(),24),
                    FTJobStaPrc         = '1'
                WHERE 
                    ((FDJobDate)        = CONVERT(DATETIME,(SELECT FDIuhDocDate FROM TCNTPdtChkHD WHERE FTIuhDocNo = '$paDataUpd[ptDocNo]'))) AND 
                    FTJobCode           = 'TCNPDTCHK'";
        $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASUpdateJobDaily] ".$this->db->error()['message']
            );
        }else{
            if($this->db->affected_rows() > 0){
                $aRetrun = array(
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSxMRABPASUpdateJobDaily] อัพเดท TCNJobDaily FTJobCode=TCNPDTCHK"
                );
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSxMRABPASUpdateJobDaily] ไม่พบรายการอัพเดท"
                );
            }
        }
        $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;
    }

    public function FSxMRABPASPdtChkDTCut($paData){
        $nCountInsert = 0;

        //ลบ TmpTable เดิมก่อน
        $tSQL0 = "IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'TMPTPdtChkDT') AND type in (N'U')) BEGIN DROP TABLE TMPTPdtChkDT END";
        $this->db->query($tSQL0);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        //สร้าง TmpTable ดึงสินค้าตรวจนับย่อย มาตั้งต้น
        $tSQL1 = "SELECT * INTO TMPTPdtChkDT FROM TCNTPdtChkDT WITH(NOLOCK) WHERE FTIuhDocNo IN (SELECT FTIuhDocNo FROM TCNTPdtChkHD WITH(NOLOCK) WHERE FTIuhDocType='1' AND FTIuhDocRef='$paData[ptDocNo]'AND FTBchCode='$paData[pnBchCode]') ORDER BY FNIudSeqNo";
        $this->db->query($tSQL1);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        //ลบสินค้าตรวจนับ ด้วยสินค้าตรวจนับรวม
        $tSQL2 = "DELETE TMPTPdtChkDT WHERE FTPdtCode IN (SELECT FTPdtCode FROM TCNTPdtChkDT WITH(NOLOCK) WHERE FTIuhDocType='2' AND FTIuhDocNo='$paData[ptDocNo]' AND FTBchCode='$paData[pnBchCode]')";
        $this->db->query($tSQL2);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        // Fixed Comsheet 2020-455 อัพเดทตาราง Temp FTIudChkUser = 'FamilyMartGit' ก่อนไป Insert TCNTPdtChkDTCut เพื่อแยกกับ "FTH Import"
        $tSQL6 = "UPDATE TMPTPdtChkDT SET FTIudChkUser = 'FamilyMartGit' ";
        $this->db->query($tSQL6);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        // Fixed Comsheet 2020-455 เพิ่ม AND FTIudChkUser = 'FamilyMartGit'
        $tSQL5 = "  DELETE FROM TCNTPdtChkDTCut WITH(ROWLOCK)
                    WHERE FTIuhDocNo IN (SELECT TOP 1 FTIuhDocNo FROM TCNTPdtChkHD WITH(NOLOCK) WHERE FTIuhDocType = '1' AND FTIuhDocRef='$paData[ptDocNo]' AND FTBchCode='$paData[pnBchCode]') 
                     AND FTIudChkUser = 'FamilyMartGit' ";
        $this->db->query($tSQL5);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        //เพิ่มสินค้าที่ไม่มีในรายการ
        $tSQL3 = "INSERT INTO TCNTPdtChkDTCut SELECT * FROM TMPTPdtChkDT WITH(NOLOCK) ORDER BY FNIudSeqNo";
        $this->db->query($tSQL3);
        // $nCountInsert = $this->db->affected_rows();
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] เพิ่มสินค้า TCNTPdtChkDTCut = ".$this->db->affected_rows()." รายการ"
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        }

        // RQ-12 Napat(Jame) 27/10/2022 อัพเดทรหัสเหตุผล สินค้าที่ไม่มีในรายการ
        $tSQL3 = "UPDATE TCNTPdtChkDTCut SET FTPszCode = '005' WHERE FTIuhDocNo IN (SELECT FTIuhDocNo FROM TCNTPdtChkHD WITH(NOLOCK) WHERE FTIuhDocType='1' AND FTIuhDocRef='$paData[ptDocNo]'AND FTBchCode='$paData[pnBchCode]') AND ISNULL(FTPszCode,'') = '' ";
        $this->db->query($tSQL3);
        // $nCountInsert = $this->db->affected_rows();
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] อัพเดทรหัสเหตุผล 005"
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        }

        //ลบ TmpTable เลย
        $tSQL4 = "IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'TMPTPdtChkDT') AND type in (N'U')) BEGIN DROP TABLE TMPTPdtChkDT END";
        $this->db->query($tSQL4);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtChkDTCut] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            return $aRetrun;
        }

        // $aRetrun = array(
        //     'nStaReturn'    => 1,
        //     'aMessageError' => "[FSxMRABPASPdtChkDTCut] เพิ่มสินค้า TCNTPdtChkDTCut = ".$nCountInsert." รายการ"
        // );
        // $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;

    }

    // public function FSaMRABPASUpdateStaPrc($paDataUpd){        
    //     $aDataUpdDT = array(
    //         'FTIudStaPrc'       => '1',
    //         'FDDateUpd'         => date('Y-m-d'),
    //         'FTTimeUpd'         => date('H:i:s'),
    //         'FTWhoUpd'          => $paDataApv['tUsrName']
    //     );
    //     $this->db->where('FTIuhDocNo', $paDataUpd['tDocNo']);
    //     $this->db->where('FTBchCode', $paDataUpd['tBchCode']);
    //     $this->db->update('TCNTPdtChkDT', $aDataUpdDT);

    //     $aDataUpdHD = array(
    //         'FTIuhStaPrcDoc'    => '1',
    //         'FTIuhApvCode'      => $paDataApv['tUsrCode'],
    //         'FDDateUpd'         => date('Y-m-d'),
    //         'FTTimeUpd'         => date('H:i:s'),
    //         'FTWhoUpd'          => $paDataApv['tUsrName']
    //     );
    //     $this->db->where('FTIuhDocRef', $paDataUpd['tDocNo']);
    //     $this->db->update('TCNTPdtChkHD', $aDataUpdHD);
    //     if($this->db->affected_rows() > 0){
    //         $aDataReturn    = array(
    //             'tCode'        => '1',
    //             'tDesc'        => 'success'
    //         );
    //     }else{
    //         $aDataReturn    = array(
    //             'tCode'        => '800',
    //             'tDesc'        => 'error'
    //         );
    //     }
    //     return $aDataReturn;
    // }

    public function FSbMRABPASSumPdt2Temp($ptShortTbl,$ptDocNo){
        $tTblDTOrg  = "TCNTPdtTnfDT";
        $tQtyKey    = "FCPtdQtyAll"; //จำนวนรับเข้า (คูณ factor แล้ว)
        $tCostKey   = "FCPtdCostEx"; //ต้นทุนรับเข้า (แยกนอก)
        $tComName   = gethostname();
        switch($ptShortTbl){
            case "IN":      //ซื้อ/รับเข้า
                $tAmtKey    = "FCPtdVatable";                   //มูลค่าขาย  (ก่อนภาษี)   ซื้อ(Vatable)
                $tWhaH      = "FTPthWhTo";
                break;
            case "OUT":     //ขาย/เบิกออก
                $tAmtKey    = "(".$tQtyKey."*".$tCostKey.")";   //มูลค่าขาย  (ก่อนภาษี)  ขาย(QtyAll*CostKey)
                $tWhaH      = "FTPthWhFrm";
                break;
        }
        //ลบข้อมูลใน ตารางของเครื่องนั้นก่อนเอาลงไปใหม่
        $this->db->delete('TCNTmpPrcDT', array('FTComName' => $tComName));

        //process into table temp
        $tSQL = "INSERT INTO TCNTmpPrcDT(FCPtdQtyAll,FCPtdNet,FCPtdVat,FCPtdVatable,
                FTComName,FTPthDocNo,FTPdtCode,FTPtdStkCode,FTPthDocType,FCPtdCostIn,FCPtdCostEx,
                FTPtdStaPrcStk,FNPthSign,FTWahCode,FDPthDocDate)";
        $tSQL .= "SELECT 
                    SUM(FCPtdQtyAll)        AS FCPtdQtyAll, 
                    AVG(FCPtdSetPrice)      AS FCPtdNet,
                    SUM(FCPtdVat)           AS FCPtdVat,
                    SUM(FCPtdVatable)       AS FCPtdVatable,
                    '$tComName'             AS FTComName, 
                    FTPthDocNo              AS FTPthDocNo,
                    FTPtdStkCode            AS FTPdtCode,
                    FTPtdStkCode            AS FTPtdStkCode,
                    FTPthDocType            AS FTPthDocType,
                    AVG(FCPtdCostIn)        AS FCPtdCostIn, 
                    AVG(FCPtdCostEx)        AS FCPtdCostEx,
                    FTPtdStaPrcStk          AS FTPtdStaPrcStk, 
                    FNPthSign               AS FNPthSign,
                    $tWhaH                  AS FTWahCode,
                    FDPthDocDate            As FDPthDocDate
                  FROM 
                    TCNTPdtTnfDT WITH (NOLOCK)
                  WHERE 
                    FTPthDocNo IN('$ptDocNo')
                  GROUP BY 
                    FTPthDocNo,FTPthDocType,FTPtdStkCode,
                    FTPtdStaPrcStk,FNPthSign,$tWhaH,FDPthDocDate";
        $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $this->FSxMMSQPASWriteLog("[FSbMRABPASSumPdt2Temp] ".$this->db->error()['message']);
            // print_r($this->db->error());
            $this->db->trans_rollback();
            return false;
        }else{
            if($this->db->affected_rows() > 0){
                $this->FSxMMSQPASWriteLog("[FSbMRABPASSumPdt2Temp] เพิ่มสินค้า TCNTPdtTnfDT=".$ptDocNo." = ".$this->db->affected_rows()." รายการ");
                return true;
            }else{
                $this->FSxMMSQPASWriteLog("[FSbMRABPASSumPdt2Temp] ไม่พบสินค้า TCNTPdtTnfDT FTPthDocNo=".$ptDocNo);
                // print_r($this->db->error());
                return false;
            }
        }
    }

    public function FSbMRABPASPrcStk($ptShortTbl,$ptDocNo,$ptWahCode,$paData,$pnAdjType){//$pbStartTrans,$pbSetPrc
        switch($ptShortTbl){
            case "IN":
                $tSign          = "+";
                $tStaStkType    = "5";
                // $bCalNewCost    = True;
                // $tFedQty        = "FCStkQtyIn";
                break;
            case "OUT":
                $tSign          = "-";
                $tStaStkType    = "5";
                // $tFedQty        = "FCStkQtyOut";
                break;
        }
        
        $tComName   = gethostname();
        if($pnAdjType == 1){
            $tSQL0 = "  UPDATE 
                            TCNMPdt WITH (ROWLOCK)
                        SET 
                            FCPdtQtyRet  = (FCPdtQtyRet ".$tSign." FCPtdQtyAll),
                            FCPdtQtyNow  = (FCPdtQtyRet ".$tSign." FCPtdQtyAll),
                            FCPdtCostAmt = (FCPdtCostAvg * (FCPdtQtyRet + FCPdtQtyWhs)) ".$tSign." (FCPtdQtyAll * FCPdtCostAvg),
                            FDDateUpd    = CONVERT(VARCHAR(10),GETDATE(),121),
                            FTTimeUpd    = CONVERT(VARCHAR(8),GETDATE(),8),
                            FTWhoUpd     = '$paData[ptUsrName]'
                        FROM 
                            TCNMPdt, TCNTmpPrcDT 
                        WHERE 
                            (TCNMPdt.FTPdtStkCode=TCNTmpPrcDT.FTPtdStkCode) AND 
                            (FTPdtStkControl='1') AND 
                            (FCPdtStkFac>='1') AND 
                            (FTPtdStaPrcStk='' OR FTPtdStaPrcStk IS NULL) AND 
                            (FTPthDocNo='$ptDocNo') AND 
                            (FTComName='$tComName') AND 
                            (TCNTmpPrcDT.FTWahCode='001')
            "; // OR TCNTmpPrcDT.FTWahCode='002'
        }else{
            $tSQL0 = "  UPDATE 
                            TCNMPdt WITH (ROWLOCK)
                        SET 
                            FCPdtQtyRet  = 0,
                            FCPdtQtyNow  = 0,
                            FDDateUpd    = CONVERT(VARCHAR(10),GETDATE(),121),
                            FTTimeUpd    = CONVERT(VARCHAR(8),GETDATE(),8),
                            FTWhoUpd     = '$paData[ptUsrName]'
                        FROM 
                            TCNMPdt, TCNTmpPrcDT 
                        WHERE 
                            (TCNMPdt.FTPdtStkCode=TCNTmpPrcDT.FTPtdStkCode) AND 
                            (FTPdtStkControl='1') AND 
                            (FCPdtStkFac>='1') AND 
                            (FTPtdStaPrcStk='' OR FTPtdStaPrcStk IS NULL) AND 
                            (FTPthDocNo='$ptDocNo') AND 
                            (FTComName='$tComName') AND 
                            (TCNTmpPrcDT.FTWahCode='001')
            ";
        }
        $this->db->query($tSQL0);

        // $tTypeAdj = ($pnAdjType == 1 ? '' : '');
        $this->FSxMMSQPASWriteLog("[FSbMRABPASPrcStk] อัพเดท FCPdtQtyRet,FCPdtQtyNow,FCPdtCostAmt = ".$this->db->affected_rows()." รายการ");

        $this->FSxMRABPASPrcWhs2up($paData,$ptDocNo,$tSign);
        $this->FSxMRABPASStockCard("TCNTmpPrcDT", "Pth", $ptDocNo, $tStaStkType, $paData, $tSign);

        //update process status of tTblDTTmp to '1'
        $tSQL1 = "UPDATE 
                    TCNTPdtTnfDT WITH (ROWLOCK)
                  SET 
                    FTPtdStaPrcStk='1'
                  WHERE 
                    (FTPtdStaPrcStk='' OR FTPtdStaPrcStk IS NULL) AND 
                    FTPthDocNo = '$ptDocNo'";
        $this->db->query($tSQL1);
        $this->FSxMMSQPASWriteLog("[FSbMRABPASPrcStk] อัพเดท TCNTPdtTnfDT(".$ptDocNo.") FTPtdStaPrcStk=1");
        return true;
    }

    public function FSxMRABPASPrcWhs2up($paData,$ptDocNo,$ptSign){
        $tWho       = $paData['ptUsrName'];
        $tComName   = gethostname();
        $tSQL  = "  INSERT INTO TCNTPdtInWha (FTWahCode, FTPdtCode, FTPtdStkCode, FCWahQty, FDDateUpd, FTTimeUpd,FTWhoUpd, FDDateIns, FTTimeIns, FTWhoIns) ";
        $tSQL .= "  SELECT DISTINCT 
                        TCNTmpPrcDT.FTWahCode, 
                        TCNTmpPrcDT.FTPdtCode, 
                        TCNTmpPrcDT.FTPtdStkCode,
                        0,
                        CONVERT(VARCHAR,GETDATE(),23),
                        CONVERT(VARCHAR,GETDATE(),24),
                        '$tWho',
                        CONVERT(VARCHAR,GETDATE(),23),
                        CONVERT(VARCHAR,GETDATE(),24),
                        '$tWho'
                    FROM 
                        TCNTmpPrcDT WITH (NOLOCK)
                    LEFT JOIN TCNTPdtInWha ON (TCNTmpPrcDT.FTPtdStkCode = TCNTPdtInWha.FTPtdStkCode) AND (TCNTmpPrcDT.FTWahCode = TCNTPdtInWha.FTWahCode)
                    WHERE ((TCNTPdtInWha.FTWahCode='') Or (TCNTPdtInWha.FTWahCode Is Null))
                      AND (TCNTmpPrcDT.FTPthDocNo='$ptDocNo')
                      AND (TCNTmpPrcDT.FTComName='$tComName')";
        $this->db->query($tSQL);
        $this->FSxMMSQPASWriteLog("[FSxMRABPASPrcWhs2up] INSERT TCNTPdtInWha FROM TCNTmpPrcDT = ".$this->db->affected_rows()." รายการ");

        $tSQL1 = "UPDATE 
                    TCNTPdtInWha WITH (ROWLOCK)
                  SET 
                    TCNTPdtInWha.FCWahQty   = TCNTPdtInWha.FCWahQty $ptSign TCNTmpPrcDT.FCPtdQtyAll,
                    TCNTPdtInWha.FDDateUpd  = CONVERT(VARCHAR(10),GETDATE(),121),
                    TCNTPdtInWha.FTTimeUpd  = CONVERT(VARCHAR(8),GETDATE(),8),
                    TCNTPdtInWha.FTWhoUpd   = '$tWho'
                  FROM 
                    TCNTPdtInWha,TCNTmpPrcDT
                  WHERE 
                    (TCNTPdtInWha.FTPtdStkCode  = TCNTmpPrcDT.FTPtdStkCode) AND 
                    (TCNTmpPrcDT.FTWahCode      = TCNTPdtInWha.FTWahCode) AND 
                    (TCNTmpPrcDT.FTComName      = '$tComName')";
        $this->db->query($tSQL1);
        $this->FSxMMSQPASWriteLog("[FSxMRABPASPrcWhs2up] UPDATE TCNTPdtInWha FCWahQty = ".$this->db->affected_rows()." รายการ");
    }

    public function FSxMRABPASStockCard($ptTableTmp,$ptMidHD,$ptDocNo,$ptFedType,$paData,$ptSign){
        $tWho       = $paData['ptUsrName'];
        $tBchCode   = $paData['pnBchCode'];
        $tComName   = gethostname();
        $tMidDT     = substr($ptMidHD,0,2)."d";
        $tSQL  = "INSERT INTO TCNTPdtStkCard (FTBchCode,FTStkDocNo,FTStkType,FTPdtStkCode, FCStkQty, FTWahCode, FDStkDate, FCStkSetPrice, FCStkCostIn,FCStkCostEx, FDDateUpd, FTTimeUpd,FTWhoUpd, FDDateIns, FTTimeIns, FTWhoIns)";
        $tSQL .= "SELECT  
                    '$tBchCode'                                             AS FTBchCode,
                    '$ptDocNo'                                              AS FTStkDocNo,
                    '$ptFedType'                                            AS FTStkType,
                    ".$ptTableTmp.".FT".$tMidDT."StkCode                    AS FTPdtStkCode,";
        if($ptSign == "-"){
            $tSQL .= "(".$ptTableTmp.".FC".$tMidDT."QtyAll*(-1))            AS FCStkQty,";
        }else{
            $tSQL .= $ptTableTmp.".FC".$tMidDT."QtyAll                      AS FCStkQty,";
        }
        $tSQL .=    $ptTableTmp.".FTWahCode                                 AS FTWahCode,";
        $tSQL .= "  CONVERT(VARCHAR,GETDATE(),23)                      AS FDStkDate,";

        if($ptSign == "-"){
            $tSQL .= "(".$ptTableTmp.".FC".$tMidDT."Net*(-1))               AS FCStkSetPrice,
                      (".$ptTableTmp.".FC".$tMidDT."CostIn*(-1))            AS FCStkCostIn,
                      (".$ptTableTmp.".FC".$tMidDT."CostEx*(-1))            AS FCStkCostEx,";
        }else{
            $tSQL .= $ptTableTmp.".FC".$tMidDT."Net                         AS FCStkSetPrice,
                     ".$ptTableTmp.".FC".$tMidDT."CostIn                    AS FCStkCostIn,
                     ".$ptTableTmp.".FC".$tMidDT."CostEx                    AS FCStkCostEx,";
        }
        $tSQL .= " CONVERT(VARCHAR,GETDATE(),23)                            AS FDDateUpd,
                   CONVERT(VARCHAR,GETDATE(),24)                            AS FTTimeUpd,
                   '$tWho'                                                  AS FTWhoUpd,
                   CONVERT(VARCHAR,GETDATE(),23)                            AS FDDateIns,
                   CONVERT(VARCHAR,GETDATE(),24)                            AS FTTimeIns,
                   '$tWho'                                                  AS FTWhoIns
                 FROM ".$ptTableTmp." WITH (NOLOCK)
                 LEFT JOIN TCNTPdtStkCard ON TCNTPdtStkCard.FTWahCode= ".$ptTableTmp.".FTWahCode
                    AND FT".$tMidDT."StkCode = FTPdtStkCode AND FTBchCode ='".$tBchCode."'
                    AND FTStkDocNo='".$ptDocNo."' AND FTStkType='".$ptFedType."'
                 WHERE (TCNTPdtStkCard.FDStkDate Is Null)
                 AND (FTComName='".$tComName."')";
        $this->db->query($tSQL);
        $this->FSxMMSQPASWriteLog("[FSxMRABPASPrcWhs2up] INSERT TCNTPdtStkCard SELECT ".$ptTableTmp." = ".$this->db->affected_rows()." รายการ");

        // If ptFedType = "1" Then Call SP_PRCxCostOverDigit(ptTableTmp, ptMidHD, ptDocNo, ptFedType, ptBchCode)
    }

    public function FSbMRABPASUpdStkAvg($ptBchCode,$ptDocNo){
        $tSQL = "SELECT TCNTPdtStkCard.FTPdtStkCode AS FTStkCode,TCNMPdt.FCPdtCostAvg AS FCPdtCostAvg,TCNMPdt.FCPdtCostStd AS FCPdtCostStd
                 FROM TCNTPdtStkCard WITH (NOLOCK) INNER JOIN TCNMPdt ON TCNTPdtStkCard.FTPdtStkCode = TCNMPdt.FTPdtStkCode
                 WHERE (TCNTPdtStkCard.FTBchCode='".$ptBchCode."') 
                 AND (TCNMPdt.FCPdtStkFac)=1 AND (TCNMPdt.FTPdtStkControl)='1' 
                 AND (TCNTPdtStkCard.FTStkDocNo)='".$ptDocNo."'";
        $oQuery = $this->db->query($tSQL);
        if($this->db->affected_rows() > 0){
            foreach($oQuery->result_array() AS $nKey => $tValue){
                $cCostAvg = $tValue['FCPdtCostAvg'];
                if($cCostAvg > $this->nCostMaxLen || $cCostAvg < ($this->nCostMaxLen * -1)){
                    $cCostAvg = $tValue['FCPdtCostStd'];
                }
                $this->db->where('FTStkDocNo', $ptDocNo);
                $this->db->where('FTPdtStkCode', $tValue['FTStkCode']);
                $this->db->where('FTBchCode', $ptBchCode);
                $this->db->update('TCNTPdtStkCard', array(
                    'FCStkCostAvg'  => $cCostAvg
                ));
            }
        }
        return true;
    }

    //Center
    //เช็คสินค้าในเอกสารต่างๆ เพื่อทำการ Update วันที่เคลื่อนไหวล่าสุด
    public function FSxMRABPASPdtInDocActUpd($paData){ //$pnDocType
        $tSQL = "UPDATE 
                    TCNMPdt 
                SET 
                    FDPdtLastAct        = CONVERT(VARCHAR,GETDATE(),23), 
                    FTPdtStaActive      = '1',
                    FDDateUpd           = CONVERT(VARCHAR,GETDATE(),23),
                    FTTimeUpd           = CONVERT(VARCHAR,GETDATE(),24),
                    FTWhoUpd            = '$paData[ptUsrName]'
                WHERE 
                    FTPdtStkCode IN (SELECT DISTINCT FTIudStkCode FROM TCNTPdtChkDT DT INNER JOIN TCNTPdtChkHD HD ON DT.FTIuhDocNo = HD.FTIuhDocNo WHERE HD.FTIuhDocNo = '$paData[ptDocNo]'
                        AND HD.FTIuhDocType IN ('2') AND DT.FCIudUnitC1 > 0)";
        $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASPdtInDocActUpd] ".$this->db->error()['message']
            );
        }else{
            $this->FSxMRABPASPdtStaActiveUpd();
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxMRABPASPdtInDocActUpd] อัพเดทวันที่เคลื่อนไหวล่าสุด = ".$this->db->affected_rows()." รายการ"
            );
        }
        $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;
    }

    //Center
    //update สถานะสินค้าที่ไม่มียอดคงเหลือ นานกว่าวันที่กำหนด
    public function FSxMRABPASPdtStaActiveUpd(){
        $this->db->select('FTSysUsrValue');
        $this->db->from('TSysConfig');
        $this->db->where('FTSysCode','ADayPdtAct');
        $oQuery = $this->db->get();
        $nDayPdtAct = $oQuery->result_array()[0]['FTSysUsrValue'];
        if($nDayPdtAct <> "0"){
            $tSQL = "UPDATE 
                        TCNMPdt 
                     SET 
                        FTPdtStaActive = '2'
                     WHERE 
                        FDPdtLastAct    < (SELECT CONVERT(VARCHAR(10),DATEADD(day, -$nDayPdtAct, GETDATE()),121)) AND 
                        FTPdtStaActive  = '1' AND 
                        FCPdtQtyRet     < 1 AND 
                        FTPdtPmtType    = '2'";
            $this->db->query($tSQL);
            $this->FSxMMSQPASWriteLog("[FSxMRABPASPdtStaActiveUpd] อัพเดทสถานะสินค้าที่ไม่มียอดคงเหลือ = ".$this->db->affected_rows()." รายการ");
        }
    }

    public function FSxMRABPASUpdatePdtChkHDandDT($paData){
        $tSQL0 = "UPDATE 
                    TCNTPdtChkDT WITH (ROWLOCK) 
                 SET 
                    FTIudStaPrc         = '1',
                    FDDateUpd           = CONVERT(VARCHAR,GETDATE(),23),
                    FTTimeUpd           = CONVERT(VARCHAR,GETDATE(),24),
                    FTWhoUpd            = '$paData[ptUsrName]'
                 WHERE 
                    FTIuhDocNo          = '$paData[ptDocNo]' AND 
                    FTBchCode          = '$paData[pnBchCode]'";
        $this->db->query($tSQL0);
        
        $tSQL1 = "UPDATE 
                    TCNTPdtChkHD WITH (ROWLOCK)
                  SET 
                    FTIuhStaPrcDoc      = '1',
                    FTIuhApvCode        = '$paData[ptUsrCode]',
                    FDDateUpd           = CONVERT(VARCHAR,GETDATE(),23),
                    FTTimeUpd           = CONVERT(VARCHAR,GETDATE(),24),
                    FTWhoUpd            = '$paData[ptUsrName]'
                  WHERE 
                    FTIuhDocRef         = '$paData[ptDocNo]'";
        $this->db->query($tSQL1);

        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASUpdatePdtChkHDandDT] ".$this->db->error()['message']
            );
        }else{
            $aRetrun = array(
                'nStaReturn'    => 1,
                'aMessageError' => "[FSxMRABPASUpdatePdtChkHDandDT] อนุมัติเอกสาร HD,DT = ".$paData['ptDocNo']
            );
        }
        $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
        return $aRetrun;
    }

    // public function FSxMRABPASPrcPdtBIDoc($ptDocNo){
    //     $nDecAmtForSav  = 4; //จำนวนทศนิยม ของมูลค่าสินค้า (Amount For Save)

    //     //FCTbiAmtCLoss                             Cut Loss
    //     $tSQL0 = "UPDATE 
    //                 TCNTBI 
    //             SET 
    //                 FCTbiAmtCLoss = ISNULL(FCTbiAmtCLoss,0) + CAST(ISNULL((SELECT 
    //                                                                             SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtCLoss
    //                                                                         FROM 
    //                                                                             TCNTPdtTnfDT DT 
    //                                                                         INNER JOIN  TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode 
    //                                                                         INNER JOIN TCNTPdtTnfHD HD ON HD.FTPthDocNo=DT.FTPthDocNo
    //                                                                         WHERE 
    //                                                                             HD.FTCutCode='002' AND 
    //                                                                             DT.FTPthDocType='6' AND 
    //                                                                             TCNTBI.FTPgpChain = P.FTPgpChain AND 
    //                                                                             P.FTPdtPmtType = '2' AND 
    //                                                                             P.FTPdtStaActive = '1' AND 
    //                                                                             DT.FTPthDocNo = '$ptDocNo' ),0) AS DECIMAL(20,$nDecAmtForSav))
    //             WHERE  FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL0;
    //     $this->db->query($tSQL0);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }

    //     //FCTbiAmtELoss                             Expired Loss
    //     $tSQL1 = "UPDATE 
    //                 TCNTBI 
    //               SET 
    //                 FCTbiAmtELoss = ISNULL(FCTbiAmtELoss,0)+CAST(ISNULL((SELECT 
    //                                                                         SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtELoss
    //                                                                      FROM TCNTPdtTnfDT DT 
    //                                                                      INNER JOIN TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode
    //                                                                      INNER JOIN TCNTPdtTnfHD HD ON HD.FTPthDocNo=DT.FTPthDocNo
    //                                                                      WHERE 
    //                                                                         HD.FTCutCode='001' AND 
    //                                                                         DT.FTPthDocType='6' AND 
    //                                                                         TCNTBI.FTPgpChain = P.FTPgpChain AND 
    //                                                                         P.FTPdtPmtType = '2' AND 
    //                                                                         P.FTPdtStaActive = '1' AND 
    //                                                                         DT.FTPthDocNo = '$ptDocNo'),0) AS DECIMAL(20,$nDecAmtForSav))
    //               WHERE FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL1;
    //     $this->db->query($tSQL1);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }
        
    //     //FCTbiAmtTnfIn                             Tranfer IN
    //     $tSQL2 = "UPDATE 
    //                 TCNTBI 
    //               SET 
    //                 FCTbiAmtTnfIn = ISNULL(FCTbiAmtTnfIn,0)+CAST(ISNULL((SELECT 
    //                                                                         SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtTnfIn
    //                                                                      FROM TCNTPdtTnfDT DT 
    //                                                                      INNER JOIN TCNTPdtTnfHD HD ON DT.FTPthDocNo = HD.FTPthDocNo 
    //                                                                      INNER JOIN  TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode 
    //                                                                      WHERE 
    //                                                                         DT.FTPthDocType     = '4' AND 
    //                                                                         TCNTBI.FTPgpChain   = P.FTPgpChain AND 
    //                                                                         P.FTPdtPmtType      = '2' AND 
    //                                                                         P.FTPdtStaActive    = '1' AND 
    //                                                                         DT.FTPthDocNo       = '$ptDocNo'),0) AS DECIMAL(20,$nDecAmtForSav))
    //               WHERE 
    //                 FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL2;
    //     $this->db->query($tSQL2);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }
        
    //     //FCTbiAmtTnfOut                            Tranfer OUT
    //     $tSQL3 = "UPDATE 
    //                 TCNTBI 
    //               SET 
    //                 FCTbiAmtTnfOut =  ISNULL(FCTbiAmtTnfOut,0)+CAST(ISNULL((SELECT 
    //                                                                             SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtTnfOut
    //                                                                         FROM 
    //                                                                             TCNTPdtTnfDT DT 
    //                                                                         INNER JOIN TCNTPdtTnfHD HD ON DT.FTPthDocNo = HD.FTPthDocNo 
    //                                                                         INNER JOIN  TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode 
    //                                                                         WHERE DT.FTPthDocType       = '5' 
    //                                                                             AND TCNTBI.FTPgpChain   = P.FTPgpChain 
    //                                                                             AND P.FTPdtPmtType      = '2' 
    //                                                                             AND P.FTPdtStaActive    = '1' 
    //                                                                             AND DT.FTPthDocNo       = '$ptDocNo'),0) AS DECIMAL(20,$nDecAmtForSav))
    //               WHERE 
    //                 FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL3;
    //     $this->db->query($tSQL3);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }
        
    //     //FCTbiAmtAbjUstIn                          Abjust IN
    //     $tSQL4 = "UPDATE 
    //                 TCNTBI 
    //               SET
    //                 FCTbiAmtAdjUstIn = ISNULL(FCTbiAmtAdjUstIn,0)+CAST(ISNULL(( SELECT 
    //                                                                                 SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtAdjUstIn 
    //                                                                             FROM TCNTPdtTnfDT DT 
    //                                                                             INNER JOIN TCNTPdtTnfHD HD ON DT.FTPthDocNo = HD.FTPthDocNo 
    //                                                                             INNER JOIN  TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode 
    //                                                                             WHERE (DT.FTPthDocType='9' or DT.FTPthDocType='7') 
    //                                                                                 AND TCNTBI.FTPgpChain   = P.FTPgpChain 
    //                                                                                 AND P.FTPdtPmtType      = '2' 
    //                                                                                 AND P.FTPdtStaActive    = '1' 
    //                                                                                 AND DT.FTPthDocNo       = '$ptDocNo'),0) AS DECIMAL(20,$nDecAmtForSav))
    //             WHERE 
    //                 FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL4;
    //     $this->db->query($tSQL4);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }
        
    //     //FCTbiAmtAbjUstOut                         Abjust OUT
    //     $tSQL5 = "UPDATE 
    //                 TCNTBI 
    //               SET
    //                 FCTbiAmtAdjUstOut =  ISNULL(FCTbiAmtAdjUstOut,0)+CAST(ISNULL((SELECT 
    //                                                                                 SUM(ISNULL(DT.FCPtdSalePrice,0) * ISNULL(DT.FCPtdQtyAll,0)) AS FCTbiAmtAdjUstOut
    //                                                                               FROM TCNTPdtTnfDT DT
    //                                                                               INNER JOIN TCNTPdtTnfHD HD ON DT.FTPthDocNo = HD.FTPthDocNo
    //                                                                               INNER JOIN  TCNMPdt P ON P.FTPdtCode = DT.FTPdtCode 
    //                                                                               WHERE (DT.FTPthDocType='0' or DT.FTPthDocType='8')
    //                                                                                 AND TCNTBI.FTPgpChain   = P.FTPgpChain 
    //                                                                                 AND P.FTPdtPmtType      = '2' 
    //                                                                                 AND P.FTPdtStaActive    = '1' 
    //                                                                                 AND DT.FTPthDocNo       = '$ptDocNo'),0) AS DECIMAL(20,$nDecAmtForSav))
    //               WHERE FDDocDate=CONVERT(VARCHAR,GETDATE(),23)";
    //     // echo $tSQL5;
    //     $this->db->query($tSQL5);
    //     if($this->db->trans_status() === FALSE){
    //         $aRetrun = array(
    //             'nStaReturn'    => 99,
    //             'aMessageError' => $this->db->error()
    //         );
    //         return $aRetrun;
    //     }

    //     $aRetrun = array(
    //         'nStaReturn'    => 1,
    //         'aMessageError' => array()
    //     );
    //     return $aRetrun;
    // }

    // public function FSxMRABPASUpdHQDiff($paData){
    //     $tSQL = "UPDATE DT
    //                 SET FTClrName =(ISNULL(DT.FCIudUnitC1,0) - ISNULL(SNAP.FCPdtBalFwQty,0) + ISNULL(STK.FCQtySale,0)-ISNULL(STK.FCQtyReturn,0)-ISNULL(STK.FCQtyPR,0)+
    //                                 ISNULL(STK.FCQtyPC,0)+ISNULL(STK.FCQtyTC,0)+ISNULL(STK.FCQtyTO,0)-ISNULL(STK.FCQtyTR,0)-
    //                                 ISNULL(STK.FCQtyAI,0)+ISNULL(STK.FCQtyAO,0))
    //                 FROM TCNTPdtChkDT DT WITH(NOLOCK)
    //                 LEFT JOIN TCNTInvSnapShort SNAP WITH(NOLOCK) ON DT.FTBchCode = SNAP.FTBchCode AND DT.FTIudStkCode = SNAP.FTPdtStkCode                                        
    //                 LEFT JOIN
    //                     (SELECT FTBchCode,FTPdtStkCode,
    //                     SUM(CASE WHEN FTStkType = '3' AND SUBSTRING(FTStkDocNo,1,1)='S' THEN FCStkQty ELSE 0 END) AS FCQtySale,
    //                     SUM(CASE WHEN FTStkType = '4' AND SUBSTRING(FTStkDocNo,1,1)='R' THEN FCStkQty ELSE 0 END) AS FCQtyReturn,
    //                     SUM(CASE WHEN FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='PR' THEN FCStkQty ELSE 0 END) AS FCQtyPR,
    //                     SUM(CASE WHEN FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='PC' THEN FCStkQty ELSE 0 END) AS FCQtyPC,
    //                     SUM(CASE WHEN FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TC' THEN FCStkQty ELSE 0 END) AS FCQtyTC,
    //                     SUM(CASE WHEN FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='TO' THEN FCStkQty ELSE 0 END) AS FCQtyTO,
    //                     SUM(CASE WHEN FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='TR' THEN FCStkQty ELSE 0 END) AS FCQtyTR,
    //                     SUM(CASE WHEN FTStkType = '1' AND SUBSTRING(FTStkDocNo,1,2)='AI' THEN FCStkQty ELSE 0 END) AS FCQtyAI,
    //                     SUM(CASE WHEN FTStkType = '2' AND SUBSTRING(FTStkDocNo,1,2)='AO' THEN FCStkQty ELSE 0 END) AS FCQtyAO
    //                     FROM TCNTPdtStkCard WITH(NOLOCK)
    //                     WHERE FDStkDate = (SELECT FDIuhDocDate FROM TCNTPdtChkHD WHERE FTBchCode='$paData[pnBchCode]' AND FTIuhDocNo='$paData[ptDocNo]')
    //                     GROUP BY FTBchCode,FTPdtStkCode) STK ON STK.FTBchCode = SNAP.FTBchCode AND STK.FTPdtStkCode = SNAP.FTPdtStkCode
    //             WHERE 1=1 
    //                 AND DT.FTBchCode = '$paData[pnBchCode]' 
    //                 AND DT.FTIuhDocNo = '$paData[ptDocNo]' 
    //                 AND SNAP.FTPdtFlgStkUpd = 'N' AND DT.FDIudChkDate = SNAP.FDPdtBalFwDate -- เพิ่มใหม่ comsheet 2019 302
    //                 AND ISNULL(SNAP.FTPdtStaPrcDoc,'') = '' -- เพิ่มใหม่ 26-12-2019 (kook)
    //     ";
    //     $oQuery = $this->db->query($tSQL);


    //     // เพิ่มใหม่ 26-12-2019 (kook)
    //     $tSQL_Upd_SnapShort = " UPDATE SS 
    //                                 SET FTPdtStaPrcDoc = '1' 
    //                             FROM TCNTInvSnapShort SS WITH(NOLOCK)
    //                             LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON SS.FTBchCode = DT.FTBchCode AND SS.FTPdtStkCode = DT.FTIudStkCode  
    //                             WHERE 1=1
    //                                 AND DT.FTBchCode        = '$paData[pnBchCode]' 
    //                                 AND DT.FTIuhDocNo       = '$paData[ptDocNo]' 
    //                                 AND SS.FTPdtFlgStkUpd   = 'N' 
    //                                 AND CONVERT(VARCHAR(10),SS.FDPdtBalFwDate,121)   = CONVERT(VARCHAR(10),DT.FDIudChkDate,121)
    //                                 AND ISNULL(SS.FTPdtStaPrcDoc,'') = ''
    //     ";
    //     $oQuery = $this->db->query($tSQL_Upd_SnapShort);

    // }

    // Create By : Napat(Jame) 20/10/2022 RQ-12 เรียกสโตล STP_PRCxUpdatePdtChkDT
    public function FSxMRABPASCallStoredUpdatePdtChkDT($paData){
        $tBchCode   = $paData['pnBchCode'];
        $tDocNo     = $paData['ptDocNo'];

        $this->FSxMMSQPASWriteLog("[FSxMRABPASCallStoredUpdatePdtChkDT] START CALL STP_PRCxUpdatePdtChkDT");

        //ลบ TmpTable เดิมก่อน
        $tCallStore = " {CALL STP_PRCxUpdatePdtChkDT(?,?,?,?)} ";
        $aDataStore = array(
            'ptBchCode'     => $tBchCode,
            'ptFTIuhDocNo'  => $tDocNo,
            'pnResult'      => 0,
            'ptResultLog'   => '',
        );
        $this->FSxMMSQPASWriteLog("[FSxMRABPASCallStoredUpdatePdtChkDT] Parameters: ".json_encode($aDataStore));
        $oReturn = $this->db->query($tCallStore, $aDataStore);
        $this->FSxMMSQPASWriteLog("[FSxMRABPASCallStoredUpdatePdtChkDT] ".json_encode($oReturn));

        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASCallStoredUpdatePdtChkDT] ".$this->db->error()['message']
            );
            $this->FSxMMSQPASWriteLog($aRetrun['aMessageError']);
            // return $aRetrun;
        }
    }

    // Create By : Napat(Jame) 20/10/2022 RQ-12 เพิ่มการดึงวันที่บันทึกเอกสารใบย่อย
    public function FSxMRABPASGetMaxDocDateSubHD($paData){
        $tSQL = " SELECT MAX(CONVERT(VARCHAR(10),FDIuhDocDate,121)) AS FDIuhDocDate FROM TCNTPdtChkHD WITH(NOLOCK) WHERE FTIuhDocType = '1' AND FTIuhDocRef = '".$paData['ptDocNo']."' ";
        $oQuery = $this->db->query($tSQL);
        if($this->db->trans_status() === FALSE){
            $aRetrun = array(
                'nStaReturn'    => 99,
                'aMessageError' => "[FSxMRABPASGetMaxDocDateSubHD] ".$this->db->error()['message']
            );
        }else{
            if($oQuery->num_rows() > 0){
                $aRetrun = array(
                    'aResult'       => $oQuery->row_array(),
                    'nStaReturn'    => 1,
                    'aMessageError' => "[FSxMRABPASGetMaxDocDateSubHD] ".$oQuery->row_array()['FDIuhDocDate']
                );
            }else{
                $aRetrun = array(
                    'nStaReturn'    => 800,
                    'aMessageError' => "[FSxMRABPASGetMaxDocDateSubHD] ไม่พบวันที่เอกสารใบย่อย ".$paData['ptDocNo']
                );
            }
        }
        return $aRetrun;
    }

    // Create By : Napat(Jame) 21/11/2022
    public function FSaMRABPASChkTSysSQL(){
        $tSQL = " SELECT FTSqlCmd AS FTPrcSta FROM TSysSQL WITH(NOLOCK) WHERE FTSqlCode = 'AdaAutoPrc' AND FNSqlSeq = 1 ";
        $oQuery = $this->db->query($tSQL);
        $aRetrun = array(
            'aResult'       => $oQuery->row_array()['FTPrcSta'],
            'nStaReturn'    => 1,
            'aMessageError' => "[FSaMRABPASChkTSysSQL] ".$oQuery->row_array()['FTPrcSta']
        );
        return $aRetrun;
    }

    // Create By : Napat(Jame) 01/12/2022
    public function FSxMRABPASUpdExpFullCnt($paData){
        /*เลขที่เอกสารตรวจนับใบรวม ที่ได้จากการอนุมัติ*/
        $tDocNo = $paData['ptDocNo'];
        $tSQL = "   UPDATE TSysSQL WITH(ROWLOCK)
                    SET FTSqlCmd = '".$tDocNo."'
                    WHERE (FTSqlCode = 'ExpFullCnt' AND FNSqlSeq = 1) ";
        $this->db->query($tSQL);
    }
}
