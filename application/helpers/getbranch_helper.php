<?php

    function getBranch(){

        $DB         = new Driver_database();
        $tSQL       = "SELECT TOP 1 FTBchCode , FTCmpBranch FROM TCNMComp";
        $tResult    = $DB->DB_SELECT($tSQL);
        return $tResult[0];
        
    }

?>