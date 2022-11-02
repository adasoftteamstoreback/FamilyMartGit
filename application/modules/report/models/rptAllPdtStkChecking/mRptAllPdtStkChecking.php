<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class mRptAllPdtStkChecking extends Database {

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 21/04/2020 Napat(Jame)
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
                            ORDER BY A.FTPgpLev1Chain ASC, A.FTPgpLev2Chain ASC 
                        ) AS RowID,
                        ROW_NUMBER() OVER( 
                            PARTITION BY A.FTPgpLev1Chain ORDER BY A.FTPgpLev1Chain ASC
                        ) AS RowLev1Chain,
                        COUNT(A.FTPgpLev1Chain) OVER (
                            PARTITION BY A.FTPgpLev1Chain
                        ) AS CountLev1Chain,

                        ROW_NUMBER() OVER( 
                            PARTITION BY A.FTPgpLev1Chain,A.FTPgpLev2Chain ORDER BY A.FTPgpLev2Chain ASC
                        ) AS RowLev2Chain,
                        COUNT(A.FTPgpLev2Chain) OVER (
                            PARTITION BY A.FTPgpLev1Chain,A.FTPgpLev2Chain
                        ) AS CountLev2Chain,
                        A.*
                    FROM (
                        SELECT DISTINCT	
                            HD.FTIuhDocNo,
                            Bch.FTBchName,
                            Wah.FTWahName,
                            CONVERT ( VARCHAR ( 10 ), HD.FDIuhDocDate, 103 ) AS FDIuhDocDate,

                            -- Sum Category
                            SUM(DT.FCIudQtyC1) OVER (
                                PARTITION BY Pgp.FTPgpLev1Chain
                            ) AS SumLev1QtyC1,
                            SUM(Bar.FCPdtRetPri1 * DT.FCIudQtyC1) OVER (
                                PARTITION BY Pgp.FTPgpLev1Chain
                            ) AS SumLev1SaleValue,
                            SUM(DT.FCIudQtyC1) OVER (
                                PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                            ) AS SumLev2QtyC1,
                            SUM(Bar.FCPdtRetPri1 * DT.FCIudQtyC1) OVER (
                                PARTITION BY Pgp.FTPgpLev1Chain,Pgp.FTPgpLev2Chain
                            ) AS SumLev2SaleValue,

                            -- Sum Footer
                            SUM(DT.FCIudQtyC1) OVER (
                                PARTITION BY HD.FTIuhDocNo
                            ) AS SumAllLev1QtyC1,
                            SUM(Bar.FCPdtRetPri1 * DT.FCIudQtyC1) OVER (
                                PARTITION BY HD.FTIuhDocNo
                            ) AS SumAllLev1SaleValue,
                            SUM(DT.FCIudQtyC1) OVER (
                                PARTITION BY HD.FTIuhDocNo
                            ) AS SumAllLev2QtyC1,
                            SUM(Bar.FCPdtRetPri1 * DT.FCIudQtyC1) OVER (
                                PARTITION BY HD.FTIuhDocNo
                            ) AS SumAllLev2SaleValue,

                            Pgp.FTPgpLev1Chain,
                            Pgp.FTPgpLev1ChainDesc,
                            Pgp.FTPgpLev2Chain,
                            Pgp.FTPgpLev2ChainDesc
                        FROM
                            (((((
                            TCNTPdtChkDT DT WITH ( NOLOCK )
                            INNER JOIN TCNTPdtChkHD HD WITH ( NOLOCK ) ON DT.FTBchCode = HD.FTBchCode 
                            AND DT.FTIuhDocNo = HD.FTIuhDocNo 
                            )
                            INNER JOIN TCNMPdt Pdt WITH ( NOLOCK ) ON DT.FTPdtCode = Pdt.FTPdtCode 
                            )
                            INNER JOIN TCNMWaHouse Wah WITH ( NOLOCK ) ON DT.FTWahCode = Wah.FTWahCode 
                            )
                            INNER JOIN TCNMPdtBar Bar WITH ( NOLOCK ) ON DT.FTPdtCode = Bar.FTPdtCode 
                            AND DT.FTIudBarCode = Bar.FTPdtBarCode 
                            )
                            LEFT OUTER JOIN TCNMPdtGrp Pgp WITH ( NOLOCK ) ON DT.FTPgpChain = Pgp.FTPgpChain 
                            )
                            INNER JOIN TCNMBranch Bch WITH ( NOLOCK ) ON HD.FTBchCode = Bch.FTBchCode 
                        WHERE
                            ((
                            Pdt.FTPdtStaActive = '1' 
                            AND Pdt.FTPdtType IN ( '1', '2', '3', '4', '5' )) 
                            AND HD.FTIuhDocType = '1' 
                            AND HD.FTIuhDocNo = '$paPackData[tDocNo]' 
                            )
                    ) AS A
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
     * Creator: 21/04/2020 Napat(Jame)
     * Last Modified : -
     * Return : count data
     * Return Type: Array
     */
    public function FSaMPASGetPageList($paPackData){

        $tSQL = "   SELECT
                        COUNT(A.FTIuhDocNo) AS counts 
                    FROM (
                        SELECT DISTINCT
                            HD.FTIuhDocNo,
                            Bch.FTBchName,
                            Wah.FTWahName,
                            CONVERT ( VARCHAR ( 10 ), HD.FDIuhDocDate, 103 ) AS FDIuhDocDate,
                            -- Sum Category
                            SUM ( DT.FCIudQtyC1 ) OVER ( PARTITION BY Pgp.FTPgpLev1Chain ) AS SumLev1QtyC1,
                            SUM ( Bar.FCPdtRetPri1 * DT.FCIudQtyC1 ) OVER ( PARTITION BY Pgp.FTPgpLev1Chain ) AS SumLev1SaleValue,
                            SUM ( DT.FCIudQtyC1 ) OVER ( PARTITION BY Pgp.FTPgpLev1Chain, Pgp.FTPgpLev2Chain ) AS SumLev2QtyC1,
                            SUM ( Bar.FCPdtRetPri1 * DT.FCIudQtyC1 ) OVER ( PARTITION BY Pgp.FTPgpLev1Chain, Pgp.FTPgpLev2Chain ) AS SumLev2SaleValue,
                            -- Sum Footer
                            SUM ( DT.FCIudQtyC1 ) OVER ( PARTITION BY HD.FTIuhDocNo ) AS SumAllLev1QtyC1,
                            SUM ( Bar.FCPdtRetPri1 * DT.FCIudQtyC1 ) OVER ( PARTITION BY HD.FTIuhDocNo ) AS SumAllLev1SaleValue,
                            SUM ( DT.FCIudQtyC1 ) OVER ( PARTITION BY HD.FTIuhDocNo ) AS SumAllLev2QtyC1,
                            SUM ( Bar.FCPdtRetPri1 * DT.FCIudQtyC1 ) OVER ( PARTITION BY HD.FTIuhDocNo ) AS SumAllLev2SaleValue,
                            Pgp.FTPgpLev1Chain,
                            Pgp.FTPgpLev1ChainDesc,
                            Pgp.FTPgpLev2Chain,
                            Pgp.FTPgpLev2ChainDesc 
                        FROM
                            (((((
                            TCNTPdtChkDT DT WITH ( NOLOCK )
                            INNER JOIN TCNTPdtChkHD HD WITH ( NOLOCK ) ON DT.FTBchCode = HD.FTBchCode 
                            AND DT.FTIuhDocNo = HD.FTIuhDocNo 
                            )
                            INNER JOIN TCNMPdt Pdt WITH ( NOLOCK ) ON DT.FTPdtCode = Pdt.FTPdtCode 
                            )
                            INNER JOIN TCNMWaHouse Wah WITH ( NOLOCK ) ON DT.FTWahCode = Wah.FTWahCode 
                            )
                            INNER JOIN TCNMPdtBar Bar WITH ( NOLOCK ) ON DT.FTPdtCode = Bar.FTPdtCode 
                            AND DT.FTIudBarCode = Bar.FTPdtBarCode 
                            )
                            LEFT OUTER JOIN TCNMPdtGrp Pgp WITH ( NOLOCK ) ON DT.FTPgpChain = Pgp.FTPgpChain 
                            )
                            INNER JOIN TCNMBranch Bch WITH ( NOLOCK ) ON HD.FTBchCode = Bch.FTBchCode 
                        WHERE
                            ((
                            Pdt.FTPdtStaActive = '1' 
                            AND Pdt.FTPdtType IN ( '1', '2', '3', '4', '5' )) 
                            AND HD.FTIuhDocType = '1' 
                            AND HD.FTIuhDocNo = 'IU89982004-00009' 
                            ) 
                    ) AS A
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

