<?php

date_default_timezone_set("Asia/Bangkok");
class cRptAllPdtNotExist extends Controller {

    public $aText               = [];
    public $nPerPage            = 20;
    public $nDecimal            = 2;

    public $tParameter          = "";
    public $tCallType           = "";
    public $tRptDocNo           = "";
    public $tRptRoute           = "";

    public $aCompanyInfo        = [];

    public function __construct() {

        $this->RequestModel('common','general/mgeneral');
        $this->RequestModel('report','rptAllPdtNotExist/mRptAllPdtNotExist');
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
     * Creator: 22/04/2020 nonpawich (petch)
     * LastUpdate: -
     * Return: -
     * ReturnType: -
     */
    public function index($tParameter,$tCallType) {

        $tAllparameter              = $this->PackDatatoarray($tParameter,$tCallType);
        $this->tRptRoute            = 'rptAllPdtNotExist';
        $this->tParameter           = $tParameter;
        $this->tCallType            = $tCallType;

        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        
        $this->FSvCCallRptViewBeforePrint();
        
    }


     /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 22/04/2020 nonpawich (petch)
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint() {

        $this->aText = [
            // Header Report
            'tTitleReport'              => language('report/report', 'tRptTitleAllPdtNotExist'),

            // Table Report
            'tRptDocNo'                 => language('report/report', 'tRptDocNo'),
            'tRptPdtBarCode'            => language('report/report', 'tRptPdtBarCode'),
            'tRptPdtName'               => language('report/report', 'tRptPdtName'),
            'tRptPunName'               => language('report/report', 'tRptPunName'),
            'tRptSalePrice'             => language('report/report', 'tRptSalePrice'),
            'tRptHhdNo'                 => language('report/report', 'tRptHhdNo'),
            'tRptPdtLocation'           => language('report/report', 'tRptPdtLocation'),
            'tRptCountUnit'             => language('report/report', 'tRptCountUnit'),
            'tRptSaleValue'             => language('report/report', 'tRptSaleValue'),
            'tRptDateCount'             => language('report/report', 'tRptDateCount'),
            'tRptNotFoundData'          => language('report/report', 'tRptNotFoundData'),
            'tRptCost'                  => language('report/report', 'tRptCost'),
            'tRptTotal'                 => language('report/report', 'tRptTotal'),
            
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
        $aDataReport        = $this->mRptAllPdtNotExist->FSaMGetDataReport($aPackGetData); // Get Data In Table
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
        $tViewDataTable     = $this->RequestView('report','rptAllPdtNotExist/wRptAllPdtNotExistHtml', $aDataView); // Get View DataTable
        $aDataView['tViewDataTable'] = $tViewDataTable;

        echo $this->RequestView('report','report/wReportViewer', $aDataView);
        
    }

}