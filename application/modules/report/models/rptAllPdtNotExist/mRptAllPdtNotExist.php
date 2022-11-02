<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class mRptAllPdtNotExist extends Database {

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator:  22/04/2020 Nonpawich (Petch)
     * Last Modified : -
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
                            ORDER BY PNE.FTPlcCode ASC , PNE.FTIuhHhdNumber ASC , PNE.FTPdtBarCode ASC 
                        ) AS RowID,
                        PNE.FTPdtBarCode,
                        PNE.FCIudSetPrice,
                        PNE.FCIudUnitC1,
                        PNE.FTPdtName,
                        PNE.FTPlcCode,
                        WAH.FTWahName,
                        CONVERT ( VARCHAR ( 10 ), PNE.FDIudChkDate, 103 ) AS FDIudChkDate,
                        PNE.FTIuhHhdNumber,
                        PNE.FTIuhDocNo,
                        PNE.FTWahCode ,
                        BCH.FTBchName,

                        SUM(PNE.FCIudUnitC1) OVER (
                            PARTITION BY PNE.FTIuhDocNo
                        ) AS SumAllFCIudUnitC1,
                        SUM(PNE.FCIudUnitC1 * PNE.FCIudSetPrice) OVER (
                            PARTITION BY PNE.FTIuhDocNo
                        ) AS SumAllSaleValue       

                    FROM TCNTPdtStkNotExist PNE WITH(NOLOCK)
                    LEFT OUTER JOIN TCNMWaHouse WAH ON PNE.FTWahCode = WAH.FTWahCode 
                    LEFT JOIN TCNMBranch BCH ON  PNE.FTBchCode = BCH.FTBchCode 
                    WHERE PNE.FTIuhDocNoType2 = '$paPackData[tDocNo]'
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
            $nPageAll       = ceil($nFoundRow/$paPackData['nPerPage']);
            $aDataResult = array(
                'aItems'           => $aDataList,
                'nAllRow'          => $nFoundRow,
                'nCurrentPage'     => $paPackData['nPageCurrent'],
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

    public function FSaMPASGetPageList($paPackData){

        $tSQL = "   SELECT
                        COUNT(PNE.FTIuhDocNo) AS counts
                    FROM TCNTPdtStkNotExist PNE WITH(NOLOCK)
                    LEFT OUTER JOIN TCNMWaHouse WAH ON PNE.FTWahCode = WAH.FTWahCode
                    LEFT JOIN TCNMBranch BCH ON  PNE.FTBchCode = BCH.FTBchCode 
                    WHERE PNE.FTIuhDocNoType2 = '$paPackData[tDocNo]'
        ";
        // echo $tSQL;
        // exit;
        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
        return $oQuery[0];
        }else{
        return false;
}
}


    }


  