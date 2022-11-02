<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class mRptAllPdtPhysicalChkStk extends Database {

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 13/04/2020 Napat(Jame)
     * Last Modified : -
     * Return : data
     * Return Type: Array
     */
    public function FSaMGetDataReport($paPackData) {

        $aRowLen    = FCNaHCallLenData($paPackData['nPerPage'],$paPackData['nPageCurrent']);
        $tSQL = "";
        // if($paPackData['nStaPrintPDF'] == 0){
            $tSQL .= "SELECT
                        L.*
                        FROM (
                     ";
        // }

        $tSQL .= "  SELECT
                        ROW_NUMBER() OVER( 
                            ORDER BY  HD.FTIuhDocNo ASC,Pgp.FTPgpLev1Chain ASC,Pgp.FTPgpLev2Chain ASC,Pgp.FTPgpLev3Chain ASC,Pgp.FTPgpLev4Chain ASC 
                        ) AS RowID,

                        ROW_NUMBER() OVER( 
                            PARTITION BY Pgp.FTPgpLev1Chain ORDER BY Pgp.FTPgpLev1Chain ASC
                        ) AS RowLev1Chain,
                        COUNT(Pgp.FTPgpLev1Chain) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS CountLev1Chain,

                        ROW_NUMBER() OVER( 
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain ORDER BY Pgp.FTPgpLev2Chain ASC
                        ) AS RowLev2Chain,
                        COUNT(Pgp.FTPgpLev2Chain) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS CountLev2Chain,

                        ROW_NUMBER() OVER( 
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain ORDER BY Pgp.FTPgpLev3Chain ASC
                        ) AS RowLev3Chain,
                        COUNT(Pgp.FTPgpLev3Chain) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS CountLev3Chain,

                        ROW_NUMBER() OVER( 
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain ORDER BY Pgp.FTPgpLev4Chain ASC
                        ) AS RowLev4Chain,
                        COUNT(Pgp.FTPgpLev4Chain) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS CountLev4Chain,

                        DT.FTIudBarCode,
                        DT.FTPdtCode,
                        DT.FTPdtName,
                        DT.FCIudUnitC1,
                        DT.FCIudUnitC2,
                        DT.FCIudWahQty,
                        DT.FCIudQtyDiff,
                        DT.FCIudQtyBal,
                        DT.FCIudCost,
                        DT.FCIudSetPrice,
                        Wah.FTWahName,
                        HD.FTIuhDocNo,
                        HD.FTIuhDocType,
                        CONVERT ( VARCHAR ( 10 ), HD.FDDateUpd, 103 ) AS FDDateUpd,
                        CONVERT ( VARCHAR ( 10 ), HD.FDIuhDocDate, 103 ) AS FDIuhDocDate,
                        Pgp.FTPgpChainName,
                        Pgp.FTPgpLev1Chain,
                        Pgp.FTPgpLev1ChainDesc,
                        Pgp.FTPgpLev2Chain,
                        Pgp.FTPgpLev2ChainDesc,
                        Pgp.FTPgpLev3Chain,
                        Pgp.FTPgpLev3ChainDesc,
                        Pgp.FTPgpLev4Chain,
                        Pgp.FTPgpLev4ChainDesc,
                        Bch.FTBchName,

                        SUM(DT.FCIudWahQty) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllWahQty,
                        SUM(DT.FCIudUnitC2) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllUnitC2,
                        SUM(DT.FCIudUnitC1) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllUnitC1,
                        SUM(DT.FCIudSetPrice * DT.FCIudUnitC1) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllSaleValue,
                        SUM(DT.FCIudQtyDiff) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllQtyDiff,
                        SUM(DT.FCIudQtyDiff * DT.FCIudSetPrice) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllSaleValueDiff,
                        SUM(DT.FCIudQtyBal) OVER (
                            PARTITION BY HD.FTIuhDocNo
                        ) AS SumAllQtyBal,
                        
                        SUM(DT.FCIudWahQty) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1WahQty,
                        SUM(DT.FCIudUnitC2) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1UnitC2,
                        SUM(DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1UnitC1,
                        SUM(DT.FCIudSetPrice * DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1SaleValue,
                        SUM(DT.FCIudQtyDiff) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1QtyDiff,
                        SUM(DT.FCIudQtyDiff * DT.FCIudSetPrice) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1SaleValueDiff,
                        SUM(DT.FCIudQtyBal) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain
                        ) AS SumLev1QtyBal,

                        SUM(DT.FCIudWahQty) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2WahQty,
                        SUM(DT.FCIudUnitC2) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2UnitC2,
                        SUM(DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2UnitC1,
                        SUM(DT.FCIudSetPrice * DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2SaleValue,
                        SUM(DT.FCIudQtyDiff) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2QtyDiff,
                        SUM(DT.FCIudQtyDiff * DT.FCIudSetPrice) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2SaleValueDiff,
                        SUM(DT.FCIudQtyBal) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                        ) AS SumLev2QtyBal,

                        SUM(DT.FCIudWahQty) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3WahQty,
                        SUM(DT.FCIudUnitC2) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3UnitC2,
                        SUM(DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3UnitC1,
                        SUM(DT.FCIudSetPrice * DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3SaleValue,
                        SUM(DT.FCIudQtyDiff) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3QtyDiff,
                        SUM(DT.FCIudQtyDiff * DT.FCIudSetPrice) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3SaleValueDiff,
                        SUM(DT.FCIudQtyBal) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain
                        ) AS SumLev3QtyBal,

                        SUM(DT.FCIudWahQty) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4WahQty,
                        SUM(DT.FCIudUnitC2) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4UnitC2,
                        SUM(DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4UnitC1,
                        SUM(DT.FCIudSetPrice * DT.FCIudUnitC1) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4SaleValue,
                        SUM(DT.FCIudQtyDiff) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4QtyDiff,
                        SUM(DT.FCIudQtyDiff * DT.FCIudSetPrice) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4SaleValueDiff,
                        SUM(DT.FCIudQtyBal) OVER (
                            PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain,Pgp.FTPgpLev3Chain,Pgp.FTPgpLev4Chain
                        ) AS SumLev4QtyBal
                    FROM
                        ((((
                        TCNTPdtChkDT DT WITH ( NOLOCK )
                        INNER JOIN TCNTPdtChkHD HD WITH ( NOLOCK ) ON DT.FTBchCode = HD.FTBchCode 
                        AND DT.FTIuhDocNo = HD.FTIuhDocNo 
                        )
                        LEFT JOIN TCNMWaHouse Wah WITH ( NOLOCK ) ON DT.FTWahCode = Wah.FTWahCode 
                        )
                        LEFT OUTER JOIN TCNMPdtGrp Pgp WITH ( NOLOCK ) ON DT.FTPgpChain = Pgp.FTPgpChain 
                        )
                        LEFT JOIN TCNMBranch Bch WITH ( NOLOCK ) ON HD.FTBchCode = Bch.FTBchCode 
                        ) 
                    WHERE
                        HD.FTIuhDocType = '2' 
                        AND HD.FTIuhDocNo = '$paPackData[tDocNo]'
                 ";

        // if($paPackData['nStaPrintPDF'] == 0){
            $tSQL .= "
                        ) AS L
                    WHERE L.RowID > $aRowLen[0] AND L.RowID <= $aRowLen[1]
                    ";
        // }
        
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
     * Creator: 13/04/2020 Napat(Jame)
     * Last Modified : -
     * Return : count data
     * Return Type: Array
     */
    public function FSaMPASGetPageList($paPackData){

        $tSQL = "   SELECT
                        COUNT(DT.FTPdtCode) AS counts
                    FROM
                        ((((
                        TCNTPdtChkDT DT WITH ( NOLOCK )
                        INNER JOIN TCNTPdtChkHD HD WITH ( NOLOCK ) ON DT.FTBchCode = HD.FTBchCode 
                        AND DT.FTIuhDocNo = HD.FTIuhDocNo 
                        )
                        LEFT JOIN TCNMWaHouse Wah WITH ( NOLOCK ) ON DT.FTWahCode = Wah.FTWahCode 
                        )
                        LEFT OUTER JOIN TCNMPdtGrp Pgp WITH ( NOLOCK ) ON DT.FTPgpChain = Pgp.FTPgpChain 
                        )
                        LEFT JOIN TCNMBranch Bch WITH ( NOLOCK ) ON HD.FTBchCode = Bch.FTBchCode 
                        ) 
                    WHERE
                        HD.FTIuhDocType = '2' 
                        AND HD.FTIuhDocNo = '$paPackData[tDocNo]'
                ";

        $oQuery = $this->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

}

