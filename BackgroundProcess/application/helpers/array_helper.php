<?php
    /**
     * Functionality : Function Sort Data In Array
     * Parameters : Ajax and Function Parameter
     * Creator : 02/11/2018 Piya(tiger)
     * Last Modified : 06/12/2018 Wasin(Yoshi)
     * Return : Array Data Success Sort Data
     * Return Type : Array
    */
    function FCNaArrayMultiSort($paArray, $paCols){
        $aColarr = array();
        foreach ($paCols as $nCol => $nOrder) {
            $aColarr[$nCol] = array();
            foreach ($paArray as $nK => $nRow) { $aColarr[$nCol]['_'.$nK] = strtolower($nRow[$nCol]); }
        }
        $tEval = 'array_multisort(';
        foreach ($paCols as $nCol => $nOrder) {
            $tEval .= '$aColarr[\''.$nCol.'\'],'.$nOrder.',';
        }
        $tEval = substr($tEval,0,-1).');';
        eval($tEval);
        $aRet = array();
        foreach ($aColarr as $nCol => $aArr) {
            foreach ($aArr as $nK => $tV) {
                $nK = substr($nK,1);
                if (!isset($aRet[$nK])) $aRet[$nK] = $paArray[$nK];
                $aRet[$nK][$nCol] = $paArray[$nK][$nCol];
            }
        }
        return $aRet;
    }

    /**
     * Functionality : Function Check Data Null All In Array
     * Parameters : Function Parameter
     * Creator : 06/12/2018 Wasin(Yoshi)
     * Last Modified : 
     * Return : Check Data Null In Array
     * Return Type : Array
    */
    function FCNaChkDataNullInRowArray($paArray){
        $aDataReturn = array();
        foreach($paArray as $nKeyData => $aRowData){
            $nCountArrayData    =  count($aRowData);
            $nDataIsNull        = "";
            foreach($aRowData as $nKey => $aRow){
                if($aRow == ""){
                    $nDataIsNull++;
                }
            }
            if($nCountArrayData != $nDataIsNull){
                $aDataChkNull = array(
                    'aItemsData'    => $aRowData
                );
                array_push($aDataReturn,$aDataChkNull['aItemsData']);
            }
        }
        return $aDataReturn;
    }

    /**
    * Functionality : Function Check Special Characters
    * Parameters : Function Parameter
    * Creator : 06/12/2018 Wasin(Yoshi)
    * Last Modified : 
    * Return : Number 
    * Return Type : number
    */
    function FCNaChkSpecialCharactersInText($ptTextCheck){
        $tPattern = '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
        if(preg_match($tPattern, $ptTextCheck)){
            return 2;   // Found Charecter In Text
        }else{
            return 1;   // Not Found Charecter In Text
        }
    }

?>
