<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class mPurreqcn extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function FSaCGetColumNamePurreqcn(){
        $tSQL = "SELECT *
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'TACTPrHD' ";
        $oQuery  = $this->db->query($tSQL);
        $aResult  = $oQuery->result_array();
        return $aResult;

    }
}

?>