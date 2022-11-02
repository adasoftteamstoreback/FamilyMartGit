<?php

    function FCNvRptCompanyInfo($paCompanyInfo){

        $aDataText = [
            // Address Language
            'tRptAddrHouseNumber'       => language('report/report', 'tRptAddrHouseNumber'),
            'tRptAddrBuilding'          => language('report/report', 'tRptAddrBuilding'),
            'tRptAddrRoad'              => language('report/report', 'tRptAddrRoad'),
            'tRptAddrSoi'               => language('report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict'       => language('report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict'          => language('report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince'          => language('report/report', 'tRptAddrProvince'),
            'tRptAddrTel'               => language('report/report', 'tRptAddrTel'),
            'tRptAddrFax'               => language('report/report', 'tRptAddrFax'),
            'tRptAddrBranch'            => language('report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1'            => language('report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2'            => language('report/report', 'tRptAddV2Desc2')
        ];

        echo    "   <div class='xCNRptAddress'>
                        <div class='text-left'>
                            <label class='xCNRptCompany'>$paCompanyInfo[FTCmpName]</label>
                        </div>
                        
                        <div class='text-left'>
                            $aDataText[tRptAddrHouseNumber]  $paCompanyInfo[FTCmpAddr]
                            $aDataText[tRptAddrDistrict]  $paCompanyInfo[FTDstName]
                            $aDataText[tRptAddrProvince]  $paCompanyInfo[FTPvnName]
                            $paCompanyInfo[FTCmpPostCode]
                        </div>

                        <div class='text-left'>
                            $aDataText[tRptAddrTel] $paCompanyInfo[FTCmpTel] $aDataText[tRptAddrFax] $paCompanyInfo[FTCmpFax]
                        </div>

                        <div class='text-left'>
                            $aDataText[tRptAddrBranch] $paCompanyInfo[FTBchcode] : $paCompanyInfo[FTBchName]
                        </div>
                    </div>
                ";

    }

    function FCNvRptHeaderInfo($ptDocNo){

        $DB = new Driver_database();
        $tSQL = "SELECT
                    HD.FTIuhDocNo,
                    BCH.FTBchName,
                    CONVERT(VARCHAR(10),HD.FDIuhDocDate,103) AS FDIuhDocDate,
                    WAH.FTWahName
                FROM TCNTPdtChkHD HD WITH(NOLOCK)
                LEFT JOIN TCNMBranch BCH ON HD.FTBchCode = BCH.FTBchCode
                LEFT JOIN TCNMWahouse WAH ON HD.FTWahCode = WAH.FTWahCode
                WHERE HD.FTIuhDocNo='$ptDocNo'
        ";
        $aDataReport = $DB->DB_SELECT($tSQL);
        if (!empty($aDataReport)) {
            $FTIuhDocNo     = $aDataReport[0]['FTIuhDocNo'];
            $FTBchName      = $aDataReport[0]['FTBchName'];
            $FDIuhDocDate   = $aDataReport[0]['FDIuhDocDate'];
            $FTWahName      = $aDataReport[0]['FTWahName'];
        }else{
            $FTIuhDocNo     = "";
            $FTBchName      = "";
            $FDIuhDocDate   = "";
            $FTWahName      = "";
        }

        $aDataText = [
            // Header Report
            'tRptDatePrint'             => language('report/report', 'tRptDatePrint'),
            'tRptTimePrint'             => language('report/report', 'tRptTimePrint'),
            'tRptPrintHtml'             => language('report/report', 'tRptPrintHtml'),
            'tRptDocNo'                 => language('report/report', 'tRptDocNo'),
            'tRptDocDate'               => language('report/report', 'tRptDocDate'),
            'tRptStoreBranch'           => language('report/report', 'tRptStoreBranch'),
            'tRptStore'                 => language('report/report', 'tRptStore'),
        ];

        echo    "  <div class='row xCNRptHeaderInfo'>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'>
                            <div class='row'>
                                <div class='col-xs-5 col-sm-5 col-md-5 col-lg-5'><label>$aDataText[tRptDocNo] :</label></div>
                                <div class='col-xs-7 col-sm-7 col-md-7 col-lg-7'>$FTIuhDocNo</div>
                            </div>
                        </div>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'>
                            <div class='row'>
                                <div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'><label>$aDataText[tRptStoreBranch] :</label></div>
                                <div class='col-xs-9 col-sm-9 col-md-9 col-lg-9'>$FTBchName</div>
                            </div>
                        </div>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'></div>
                    </div>
                    <div class='row xCNRptHeaderInfo'>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'>
                            <div class='row'>
                                <div class='col-xs-5 col-sm-5 col-md-5 col-lg-5'><label>$aDataText[tRptDocDate] :</label></div>
                                <div class='col-xs-7 col-sm-7 col-md-7 col-lg-7'>$FDIuhDocDate</div>
                            </div>
                        </div>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'>
                            <div class='row'>
                                <div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'><label>$aDataText[tRptStore] :</label></div>
                                <div class='col-xs-9 col-sm-9 col-md-9 col-lg-9'>$FTWahName</div>
                            </div>
                        </div>
                        <div class='col-xs-4 col-sm-4 col-md-4 col-lg-4'>
                            <div class='text-right'>
                                $aDataText[tRptDatePrint] ".date('d/m/Y')." $aDataText[tRptTimePrint] ".date('H:i:s')."
                            </div>
                        </div>
                    </div>
                ";
    }