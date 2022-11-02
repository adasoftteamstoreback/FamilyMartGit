<?php

    function FCNaGetCompanyInfo($ptCompCode){
        $DB = new Driver_database();

        $tSQL = "SELECT
                    Cmp.FTCmpCode,
                    Cmp.FTCmpName,
                    Cmp.FTCmpShop,
                    Cmp.FTCmpAddr,
                    Cmp.FTCmpDirector,
                    Cmp.FTBchcode,
                    Bch.FTBchName,
                    Cmp.FTCmpEmail,
                    Cmp.FTCmpFax,
                    Cmp.FTCmpRetInOrEx,
                    Cmp.FTCmpTel,
                    Cmp.FTVatCode,
                    Cmp.FTPvnCode,
                    Pvn.FTPvnName,
                    Cmp.FTDstCode,
                    Dst.FTDstName,
                    Dst.FTDstPost,
                    Cmp.FTCmpPostCode 
                FROM
                    (
                        SELECT
                            TCNMComp.FTCmpCode,
                            FTCmpName,
                            FTCmpShop,
                            FTCmpDirector,
                            FTCmpAddr,
                            FTCmpStreet,
                            FTBchcode,
                            FTPvnCode,
                            FTDstCode,
                            FTCmpEmail,
                            FTCmpFax,
                            FTCmpRetInOrEx,
                            FTCmpTel,
                            FTVatCode,
                            FTCmpPostCode 
                        FROM
                            TCNMComp WITH(NOLOCK) 
                        WHERE
                            FTCmpCode = '$ptCompCode'
                    ) Cmp
                    LEFT JOIN TCNMBranch Bch ON Cmp.FTBchCode = Bch.FTBchCode
                    LEFT JOIN TCNMProvince Pvn ON Cmp.FTPvnCode = Pvn.FTPvnCode
                    LEFT JOIN TCNMDistrict Dst ON Cmp.FTDstCode = Dst.FTDstCode
        ";

        $oQuery = $DB->DB_SELECT($tSQL);
        if (!empty($oQuery)) {
            return $oQuery[0];
        }else{
            return false;
        }
    }

?>