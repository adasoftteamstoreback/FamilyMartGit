<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class mRptAllPdtStkCheckBylocation extends Database {

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 21/04/2020 Napat(Jame)
     * Last Modified : 14/05/2020 Napat(Jame) ลบ INNER JOIN TCNTPdtInWha ออก และเปลี่ยน INNER JOIN ทุกตัวให้กลายเป็น LEFT JOIN แทน
     * Return : data
     * Return Type: Array
     */
    public function FSaMGetDataReport($paPackData) {
        $aRowLen    = FCNaHCallLenData($paPackData['nPerPage'],$paPackData['nPageCurrent']);
        $tSQL = "";
        if($paPackData['nStaPrintPDF'] == 0){
            $tSQL .= "SELECT
                        L.*
                        FROM (
                     ";
        }

        $tSQL .= "  SELECT
                        ROW_NUMBER() OVER( 
                            ORDER BY DT.FTPlcCode ASC,DT.FTIudBarCode ASC
                        ) AS RowID,
                        ROW_NUMBER() OVER( 
                            PARTITION BY DT.FTPlcCode ORDER BY DT.FTPlcCode ASC
                        ) AS RowPlcCode,
                        COUNT(DT.FTPlcCode) OVER (
                            PARTITION BY DT.FTPlcCode
                        ) AS CountPlcCode,
                        
                        WAH.FTWahName,
                        DT.FTPdtCode,
                        PDT.FTPdtStaActive,
                        PDT.FTPdtType,
                        HD.FTIuhStaDoc,
                        HD.FTBchCode,
                        DT.FCIudQtyC1,
                        HD.FTIuhDocNo,
                        BAR.FCPdtRetPri1,
                        ( FCIudQtyC1 * FCPdtRetPri1 ) AS FCSumPdtRetPri1,
                        DT.FTPlcCode,
                        DT.FTIudBarCode,
                        PDT.FTPdtName,
                        CONVERT ( VARCHAR ( 10 ), HD.FDIuhDocDate, 103 ) AS FDIuhDocDate,
                        HD.FTIuhDocType,
                        DT.FNIudSeqNo,
                        DT.FTIudChkTime,
                        BCH.FTBchName,
                       
                        SUM(DT.FCIudQtyC1) OVER (
                            PARTITION BY DT.FTPlcCode
                        ) AS FCSumAllQtyC1,

                        SUM(FCIudQtyC1 * FCPdtRetPri1) OVER (
                            PARTITION BY DT.FTPlcCode
                        ) AS FCSumAllPdtRetPri1
                        
                    FROM TCNTPdtChkHD AS HD
                    INNER JOIN TCNTPdtChkDT DT  ON HD.FTBchCode = DT.FTBchCode  AND HD.FTIuhDocNo   = DT.FTIuhDocNo
                    LEFT JOIN TCNMPdt       PDT ON DT.FTPdtCode = PDT.FTPdtCode AND DT.FTIudStkCode = PDT.FTPdtStkCode
                    LEFT JOIN TCNMPdtBar    BAR ON DT.FTPdtCode = BAR.FTPdtCode AND DT.FTIudBarCode = BAR.FTPdtBarCode
                    LEFT JOIN TCNMWaHouse   WAH ON HD.FTWahCode = WAH.FTWahCode
                    LEFT JOIN TCNMBranch    BCH ON HD.FTBchCode = BCH.FTBchCode
                    WHERE HD.FTIuhDocNo ='$paPackData[tDocNo]'
                 ";
        if($paPackData['nStaPrintPDF'] == 0){
            $tSQL .= "
                ) AS L
            WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
            ";
        }
        // echo $tSQL;
        // exit;
        $aDataList = $this->DB_SELECT($tSQL);
        if(count($aDataList) > 0){
            $aFoundRow      = $this->FSaMPASGetPageList($paPackData);
            $nFoundRow      = $aFoundRow['counts'];
            if($paPackData['nStaPrintPDF'] == 0){
                $nPageAll       = ceil($nFoundRow/$paPackData['nPerPage']);
                $nPageCurrent   = $paPackData['nPageCurrent'];
            }else{
                $nPageAll       = 1;
                $nPageCurrent   = 1;
            }
            
            $aDataResult = array(
                'aItems'           => $aDataList,
                'nAllRow'          => $nFoundRow,
                'nCurrentPage'     => $nPageCurrent,
                'nAllPage'         => $nPageAll,
                'tCode'            => '1',
                'tDesc'            => 'success',
            );
        }else{
            $aDataResult = array(
                'nAllRow'          => 0,
                'nCurrentPage'     => 1,
                "nAllPage"         => 0,
                'tCode'            => '800',
                'tDesc'            => 'data not found',
            );
        }
        return $aDataResult;

    }

    /**
     * Functionality: Get Page Data Report
     * Parameters:  Function Parameter
     * Creator: 21/04/2020 Napat(Jame)
     * Last Modified : -
     * Return : count data
     * Return Type: Array
     */
    public function FSaMPASGetPageList($paPackData){

        $tSQL = "   SELECT
                        COUNT(DT.FTPdtCode) AS counts
                    FROM TCNTPdtChkHD AS HD
                        INNER JOIN TCNTPdtChkDT AS DT ON HD.FTBchCode = DT.FTBchCode 
                        AND HD.FTIuhDocNo = DT.FTIuhDocNo
                        INNER JOIN TCNMPdt AS PDT ON DT.FTPdtCode = PDT.FTPdtCode
                        INNER JOIN TCNMPdtBar AS BAR ON DT.FTPdtCode = BAR.FTPdtCode 
                        AND DT.FTIudBarCode = BAR.FTPdtBarCode
                        INNER JOIN TCNTPdtInWha AS PDTWa ON PDT.FTPdtStkCode = PDTWa.FTPtdStkCode
                        INNER JOIN TCNMWaHouse AS WAH ON PDTWa.FTWahCode = WAH.FTWahCode
                        LEFT JOIN TCNMBranch AS BCH ON HD.FTBchCode = BCH.FTBchCode
                        WHERE HD.FTIuhDocNo ='$paPackData[tDocNo]'
                ";

        $oQuery = $this->DB_SELECT($tSQL);

        // echo $tSQL;
        // exit;

        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

}

