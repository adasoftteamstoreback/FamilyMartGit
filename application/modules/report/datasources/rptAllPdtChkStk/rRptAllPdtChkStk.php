<?php
require "application\libraries\koolreport\autoload.php";

use \koolreport\processes\CalculatedColumn;

class rRptAllPdtChkStk extends \koolreport\KoolReport {
    use \koolreport\clients\jQuery;
    // use \koolreport\clients\Bootstrap;
    use \koolreport\export\Exportable;

    public function settings(){

        $aDataReport = $this->params["aDataReturn"];

        if(isset($aDataReport['tCode']) && $aDataReport['tCode'] == '1'){
            $aDataKoolReport    = $aDataReport['aItems'];
        }else{
            $aDataKoolReport    = array();
        }

        return array(
            "dataSources"   =>array(
                "DataReport"    =>array(
                    "class"         =>  "\koolreport\datasources\ArrayDataSource",
                    "data"          =>  $aDataKoolReport,
                    "dataFormat"    =>  "associate"
                )
            )
        );
    }


    protected function setup(){
        $this->src('DataReport')
        // ->pipe(new CalculatedColumn(array(
        //     'FCXshValue' => "{FCXshGrand}+{FCXshTotalAfDisChgNV}-{FCXshVat}",
        // )))
        ->pipe($this->dataStore('RptAllPdtChkStk'));

    }

}