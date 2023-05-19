<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class mRptPdtReChkDT extends Database {

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 01/04/2023 Napat(Jame)
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

        $tSQL .= "  SELECT DISTINCT
                        ROW_NUMBER() OVER(ORDER BY FTIudStkCode ASC) AS RowID,
                        *
                    FROM TCNTPdtReChkDT WITH(NOLOCK)
                    WHERE FTIuhDocNo = '".$paPackData['tDocNo']."'
                 ";
                        
        if($paPackData['nStaPrintPDF'] == 0){
            $tSQL .= "
                        ) AS L
                    WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
                    ";
        }

        // print_r($tSQL);
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
     * Creator: 01/04/2023 Napat(Jame)
     * Last Modified : -
     * Return : count data
     * Return Type: Array
     */
    public function FSaMPASGetPageList($paPackData){

        $tSQL = "   SELECT
                        COUNT(FTIudStkCode) AS counts
                    FROM TCNTPdtReChkDT WITH(NOLOCK)
                    WHERE FTIuhDocNo = '".$paPackData['tDocNo']."'
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

