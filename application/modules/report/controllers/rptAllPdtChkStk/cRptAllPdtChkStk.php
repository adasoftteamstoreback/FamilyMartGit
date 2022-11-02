<?php

// defined('BASEPATH') or exit('No direct script access allowed');

// include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel.php';
// include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/IOFactory.php';
// include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

date_default_timezone_set("Asia/Bangkok");

class cRptAllPdtChkStk extends Controller {

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
        $this->RequestModel('report','rptAllPdtChkStk/mRptAllPdtChkStk');
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
     * Creator: 13/04/2020 Napat(Jame)
     * LastUpdate: -
     * Return: -
     * ReturnType: -
     */
    public function index($tParameter,$tCallType) {

        $tAllparameter              = $this->PackDatatoarray($tParameter,$tCallType);
        // $this->aCompanyInfo         = FCNaGetCompanyInfo($_SESSION["SesFTCmpCode"]);
        $this->tRptRoute            = 'rptAllPdtChkStk';
        $this->tParameter           = $tParameter;
        $this->tCallType            = $tCallType;

        $_SESSION['FTUsrCode']  = $tAllparameter[0]['Username'];
        
        $this->FSvCCallRptViewBeforePrint();
        
    }
    
    /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 13/04/2020 Napat(Jame)
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint() {

        $this->aText = [
            // Header Report
            'tTitleReport'              => language('report/report', 'tRptTitleAllPdtChkStk'),

            // Table Report
            'tRptPdtCode'               => language('report/report', 'tRptPdtCode'),
            'tRptPdtName'               => language('report/report', 'tRptPdtName'),
            'tRptPunName'               => language('report/report', 'tRptPunName'),
            'tRptSalePrice'             => language('report/report', 'tRptSalePrice'),
            'tRptDivision'              => language('report/report', 'tRptDivision'),
            'tRptDepartment'            => language('report/report', 'tRptDepartment'),
            'tRptCategory'              => language('report/report', 'tRptCategory'),
            'tRptSubCategory'           => language('report/report', 'tRptSubCategory'),
            'tRptCountUnit'             => language('report/report', 'tRptCountUnit'),
            'tRptNotFoundData'          => language('report/report', 'tRptNotFoundData'),
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
        $aDataReport        = $this->mRptAllPdtChkStk->FSaMGetDataReport($aPackGetData); //Get Data
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
        $tViewDataTable     = $this->RequestView('report','rptAllPdtChkStk/wRptAllPdtChkStkHtml', $aDataView); // Get View DataTable
        $aDataView['tViewDataTable'] = $tViewDataTable;

        echo $this->RequestView('report','report/wReportViewer', $aDataView);

        // $nStaPrintPDF           = $this->input->post('nStaPrintPDF');
        // $nPageCurrent           = $this->input->post('ohdRptCurrentPage');
        // $this->tRptDocNo        = $this->input->post('ohdRptDocNo');
        // $this->aCompanyInfo     = FCNaGetCompanyInfo($this->input->post('ohdRptCompCode'));
        // $aPackGetData = array(
        //     'tDocNo'            => $this->tRptDocNo,
        //     'nPageCurrent'      => ($nPageCurrent == 'null' ? 1 : $nPageCurrent),
        //     'nPerPage'          => 100,//$this->nPerPage
        //     'nStaPrintPDF'      => ( $nStaPrintPDF == 'null' ? 0 : 1 ),
        // );

        // // Get Data Report
        // $aDataReport        = $this->mRptAllPdtChkStk->FSaMGetDataReport($aPackGetData); //Get Data
        // $tViewRenderKool    = $this->FSvCRenderKoolReportHtml($aDataReport,$aPackGetData['nStaPrintPDF']); // Draw Table

        // $aDataView = array(
        //     'tTitleReport'      => $this->aText['tTitleReport'],
        //     'tRptDocNo'         => $this->tRptDocNo,
        //     'tRptCompCode'      => $this->input->post('ohdRptCompCode'),
        //     'tRptRoute'         => $this->tRptRoute,
        //     'tViewDataTable'    => $tViewRenderKool,
        //     'aDataReport'       => $aDataReport,
        //     'aCompanyInfo'      => $this->aCompanyInfo,
        //     'aDataTextRef'      => $this->aText,
        //     'tParameter'        => $this->tParameter,
        //     'tCallType'         => $this->tCallType
        // );

        // echo $this->RequestView('report','report/wReportViewer', $aDataView);
        
    }
    
    /**
     * Functionality: Call Table Kool Report
     * Parameters:  Function Parameter
     * Creator: 13/04/2020 Napat(Jame)
     * LastUpdate: -
     * Return: View Kool Report
     * ReturnType: View
     */
    // public function FSvCRenderKoolReportHtml($paDataReport,$nStaPrintPDF) {
    //     // Ref File Kool Report
    //     require_once 'application\modules\report\datasources\RptAllPdtChkStk\rRptAllPdtChkStk.php';

    //     // Set Parameter To Report
    //     $oRptAllPdtChkStkHtml = new rRptAllPdtChkStk(array(
    //         // 'aDataFilter' => $paDataFilter,
    //         'tRptDocNo'         => $this->tRptDocNo,
    //         'nCurrentPage'      => $paDataReport['nCurrentPage'],
    //         'nAllPage'          => $paDataReport['nAllPage'],
    //         'aDataTextRef'      => $this->aText,
    //         'aDataReturn'       => $paDataReport,
    //         'aCompanyInfo'      => $this->aCompanyInfo,
    //         'nStaPrintPDF'      => $nStaPrintPDF
    //     ));

    //     $oRptAllPdtChkStkHtml->run();
    //     $tHtmlViewReport = $oRptAllPdtChkStkHtml->render('wRptAllPdtChkStkHtml', true);
    //     return $tHtmlViewReport;
    // }

    // public function FSxCCallRptPrintPDF(){
    //     require_once 'application/libraries/mPDF/vendor/autoload.php';

    //     // แปลง html view ให้เป็นรูปก่อน 
	// 	$base64Image        = $_REQUEST['base64Image'];
	// 	$image_name         = $this->input->post('image_name');
	// 	$image_name_save    = $image_name.".png";
	// 	file_put_contents("application/modules/report/assets/exportpdf/".$image_name_save, base64_decode(str_replace('data:image/png;base64,','',$base64Image)));
		
	// 	// นำรูปมา save เป็น pdf ด้วย mPDf
	// 	$image              = "application/modules/report/assets/exportpdf/".$image_name_save;
	// 	$html               = '<img src="'.$image.'" width="100%" height="100%">';
	// 	$mpdf               = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']); 
	// 	$mpdf->WriteHTML($html);
	// 	$pdf_name           = $image_name.".pdf";
	// 	$mpdf->Output("application/modules/report/assets/exportpdf/".$pdf_name,"F"); //F
	// 	echo "application/modules/report/assets/exportpdf/".$pdf_name;
    // }

}

































