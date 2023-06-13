<?php

date_default_timezone_set("Asia/Bangkok");
class cRptPdtReChkDT extends Controller {

    public $aText               = [];
    public $nPerPage            = 20;
    public $nDecimal            = 2;

    public $tParameter          = "";
    public $tCallType           = "";
    public $tRptDocNo           = "";
    public $tRptRoute           = "";

    public $aCompanyInfo        = [];

    public function __construct() {
        $this->RequestModel('report','rptPdtReChkDT/mRptPdtReChkDT');
        $this->input    = new Input();

        if(!isset($_SESSION)){ session_start(); }
        if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
            echo 'session_expired';
            exit;
        }

        parent::__construct();
    }

    /**
     * Functionality: ตั้งค่าตัวแปลเพื่อเรียกใช้งาน
     * Parameters:  ข้อมูลดาต้าเบส, ประเภทหน้าจอ
     * Creator: 31/03/2023 Napat(Jame)
     * LastUpdate: -
     * Return: -
     * ReturnType: -
     */
    public function index($tParameter,$tCallType) {

        $tAllparameter              = $this->PackDatatoarray($tParameter,$tCallType);
        $this->tRptRoute            = 'rptPdtReChkDT';
        $this->tParameter           = $tParameter;
        $this->tCallType            = $tCallType;
        $_SESSION['FTUsrCode']      = $tAllparameter[0]['Username'];
        
        $this->FSvCCallRptViewBeforePrint();
        
    }
    
    /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 31/03/2023 Napat(Jame)
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint() {

        $this->aText = [
            // Header Report
            'tTitleReport'              => language('report/report', 'tRptTitlePdtReChkDT'),

            // Table Report
            'tRptColumnNo'              => language('report/report', 'tRptColumnNo'),
            'tRptPdtBarCode'            => language('report/report', 'tRptPdtBarCode'),
            'tRptColumnPdtStkCode'      => language('report/report', 'tRptColumnPdtStkCode'),
            'tRptColumnPdtName'         => language('report/report', 'tRptColumnPdtName'),
            'tRptColumnGonQty'          => language('report/report', 'tRptColumnGonQty'),
            'tRptColumnGon1'            => language('report/report', 'tRptColumnGon1'),
            'tRptColumnGon2'            => language('report/report', 'tRptColumnGon2'),
            'tRptColumnGon3'            => language('report/report', 'tRptColumnGon3'),
            'tRptColumnGon4'            => language('report/report', 'tRptColumnGon4'),
            'tRptColumnGon5'            => language('report/report', 'tRptColumnGon5'),
            'tRptColumnTotalQty'        => language('report/report', 'tRptColumnTotalQty'),
            'tRptColumnNewQty'          => language('report/report', 'tRptColumnNewQty'),
        ];

        $nStaPrintPDF           = $this->input->post('nStaPrintPDF');
        $nPageCurrent           = $this->input->post('ohdRptCurrentPage');
        $this->tRptDocNo        = $this->input->post('ohdRptDocNo');
        $this->aCompanyInfo     = FCNaGetCompanyInfo($this->input->post('ohdRptCompCode'));
        $aPackGetData = array(
            'tDocNo'            => $this->tRptDocNo,
            'nPageCurrent'      => ($nPageCurrent == 'null' ? 1 : $nPageCurrent),
            'nPerPage'          => $this->nPerPage,
            'nStaPrintPDF'      => ( $nStaPrintPDF == 'null' ? 0 : 1 ),
        );

        // Get Data Report
        $aDataReport        = $this->mRptPdtReChkDT->FSaMGetDataReport($aPackGetData); // Get Data In Table
        $aDataView = array(
            'tTitleReport'      => $this->aText['tTitleReport'],
            'tRptDocNo'         => $this->tRptDocNo,
            'tRptCompCode'      => $this->input->post('ohdRptCompCode'),
            'tRptRoute'         => $this->tRptRoute,
            'aDataReport'       => $aDataReport,
            'aCompanyInfo'      => $this->aCompanyInfo,
            'aDataTextRef'      => $this->aText,
            'tParameter'        => $this->tParameter,
            'tCallType'         => $this->tCallType,
            'nDecimal'          => $this->nDecimal
        );
        $tViewDataTable     = $this->RequestView('report','rptPdtReChkDT/wRptPdtReChkDTHtml', $aDataView); // Get View DataTable
        $aDataView['tViewDataTable'] = $tViewDataTable;

        echo $this->RequestView('report','report/wReportViewer', $aDataView);
        
    }

}