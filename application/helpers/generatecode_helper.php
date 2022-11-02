<?php

    function generateCode($tTablename,$tFiledDocno){

        $DB         = new Driver_database();

        //พวก format ของเรื่องต่างๆ
        if($tTablename == 'TACTPtHD'){ //ใบขอลดหนี้ (PC)
            $tConcat  = " AND FTSatDefChar='PC' ";
        }else{ //เรื่องทั่วไป - turn off suggest : ordering screen
            $tConcat  = "";
        }

        $tSQL       = "SELECT TOP 1 FTSatUsrFmtAll FROM TSysAuto";
        $tSQL       .= " WHERE FTSatTblName = '$tTablename' ";
        $tSQL       .= $tConcat;
       
        $tResult    = $DB->DB_SELECT($tSQL);

        $aGetBranch         = getBranch();
        $tBCH               = $aGetBranch['FTBchCode'];
        $tFormat            = $tResult[0]['FTSatUsrFmtAll'];
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

        //$tSQL               = "SELECT TOP 1 $tFiledDocno FROM $tTablename WHERE $tTablename.FTBchCode = (SELECT FTBchCode FROM TCNMComp (NOLOCK)) ORDER BY $tFiledDocno DESC ";
        $tResultNumber      = $DB->DB_SELECT($tSQL);

        if($tResultNumber[0][$tFiledDocno] == '' || $tResultNumber[0][$tFiledDocno] == null){
            $tNumberDocno   = sprintf($nDigitformat,1);
        }else{
            $tValue         = explode("-",$tResultNumber[0][$tFiledDocno]);
            $tNumberDocno   = $tValue[1] + 1;
            $nNumber        = sprintf($nDigitformat,$tNumberDocno);
            $tNumberDocno   = $nNumber;
        }

        $tResultFormat      = str_replace('#####', $tNumberDocno , $tResultFormat);
        
        return $tResultFormat;
    }

?>