<?php

    $tROUTE_login                    = 'application/modules/common/controllers/clogin.php';
    $tROUTE_Browser                 = 'application/modules/common/controllers/cBrowser.php';

    $tROUTE_common                   = 'application/modules/common/controllers/cCommon.php';
    $tROUTE_common_content           = 'Content.php?route=common&func_method=FSxCOMContentMain';

    $tROUTE_purchase                 = 'application/modules/document/controllers/purchase/cpurchase.php';
    $tROUTE_purchase_content         = 'Content.php?route=purchase&func_method=FSxPURContentMain';

    $tROUTE_invoice                 = 'application/modules/document/controllers/invoice/cinvoice.php';
    $tROUTE_invoice_content         = 'Content.php?route=invoice&func_method=FSxINVContentMain';

    $tROUTE_demoquery               = 'application/modules/query/controllers/demoquery/cDemoquery.php';
    $tROUTE_demoquery_content       = 'Content.php?route=demoquery&func_method=FSxDemoContentMain';

    //turnoffsuggestorder create : supawat(11-04-2019)
    $tROUTE_omnTurnOffSuggest               = 'application/modules/document/controllers/turnoffsuggestorder/cturnoffsuggestorder.php';
    $tROUTE_omnTurnOffSuggest_content       = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOContentMain';
    $tROUTE_omnTurnOffSuggest_insert        = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOInsertIntoTableTemp';
    $tROUTE_omnTurnOffSuggest_insertBarcode = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOInsertIntoTableTempBarcode';
    $tROUTE_omnTurnOffSuggest_select        = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSelectPDTTempintoTable';
    $tROUTE_omnTurnOffSuggest_delete        = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSODeletePDTTempintoTable';
    $tROUTE_omnTurnOffSuggest_update        = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOUpdatePDTTempintoTable';
    $tROUTE_omnTurnOffSuggest_save          = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSave';
    $tROUTE_omnTurnOffSuggest_approve       = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOApprove';
    $tROUTE_omnTurnOffSuggest_newform       = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSONewform';
    $tROUTE_omnTurnOffSuggest_searchlist    = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSearchlist';
    $tROUTE_omnTurnOffSuggest_selectHD      = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSelectPDTHD';
    $tROUTE_omnTurnOffSuggest_rabbitfail    = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOUpdateApproveRabbitFail';
    $tROUTE_omnTurnOffSuggest_dataDuplicate = 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOCheckDateDuplicate';

    //orderingscreen create : napat(18-04-2019)
    $tROUTE_omnOrderingScreen               = 'application/modules/document/controllers/orderingscreen/corderingscreen.php';
    $tROUTE_omnOrderingScreen_content       = 'Content.php?route=omnOrderingScreen&func_method=FSxCODSContentMain';
    $tROUTE_omnOrderingScreen_rabbitfail    = 'Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateApproveRabbitFail';
    $tROUTE_omnOrderingScreen_rabbitSuccess = 'Content.php?route=omnOrderingScreen&func_method=FSxCODSRabbitSuccess';
    // $tROUTE_omnOrderingScreen_insert  = 'Content.php?route=omnOrderingScreen&func_method=FSxCODSInsertIntoTableTemp';

    //ใบขอลดหนี้ create : supawat(10-07-2019)
    $tROUTE_omnPurReqCNNew                  = 'application/modules/document/controllers/purreqcn/cpurreqcn.php';
    $tROUTE_omnPurReqCNNew_content          = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURContentMain';
    $tROUTE_omnPurReqCNNew_gettypesupplier  = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURGettypesupplier';
    $tROUTE_omnPurReqCNNew_getsupplier      = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURGetsupplier';
    $tROUTE_omnPurReqCNNew_mainpage         = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURContentMainpage';
    $tROUTE_omnPurReqCNNew_getdocument      = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURGetDocument';
    $tROUTE_omnPurReqCNNew_getpdtbydocument = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURGetPDTByDocument';
    $tROUTE_omnPurReqCNNew_insertpdt        = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURInsertPDT';
    $tROUTE_omnPurReqCNNew_insertBarcode    = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURInsertPDTBarcode';
    $tROUTE_omnPurReqCNNew_selectpdt        = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURSelectPDT';
    $tROUTE_omnPurReqCNNew_save             = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURSave';
    $tROUTE_omnPurReqCNNew_delete           = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURDelete';
    $tROUTE_omnPurReqCNNew_editinline       = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPUREditinline';
    $tROUTE_omnPurReqCNNew_calculate        = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURCalculate';
    $tROUTE_omnPurReqCNNew_cancelDoc        = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURCancelDocument';
    $tROUTE_omnPurReqCNNew_insertpdtByPUR1  = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURInsertPDTByPUR1';
    $tROUTE_omnPurReqCNNew_approve          = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURApprove';
    $tROUTE_omnPurReqCNNew_selectafter      = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURSelectAfter';
    $tROUTE_omnPurReqCNNew_listdocument     = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURListDocument';
    $tROUTE_omnPurReqCNNew_CaseProcessFail  = 'Content.php?route=omnPurReqCNNew&func_method=FSxCPURCaseProcessFail';

    //ใบลดหนี้ create : supawat(15-08-2019)
    $tROUTE_omnPurCNNew                  = 'application/modules/document/controllers/purcn/cpurcn.php';
    $tROUTE_omnPurCNNew_content          = 'Content.php?route=omnPurCNNew&func_method=FSxCPURContentMain';
    $tROUTE_omnPurCNNew_gettypesupplier  = 'Content.php?route=omnPurCNNew&func_method=FSxCPURGettypesupplier';
    $tROUTE_omnPurCNNew_getsupplier      = 'Content.php?route=omnPurCNNew&func_method=FSxCPURGetsupplier';
    $tROUTE_omnPurCNNew_mainpage         = 'Content.php?route=omnPurCNNew&func_method=FSxCPURContentMainpage';
    $tROUTE_omnPurCNNew_getdocument      = 'Content.php?route=omnPurCNNew&func_method=FSxCPURGetDocument';
    $tROUTE_omnPurCNNew_getpdtbydocument = 'Content.php?route=omnPurCNNew&func_method=FSxCPURGetPDTByDocument';
    $tROUTE_omnPurCNNew_insertpdt        = 'Content.php?route=omnPurCNNew&func_method=FSxCPURInsertPDT';
    $tROUTE_omnPurCNNew_insertBarcode    = 'Content.php?route=omnPurCNNew&func_method=FSxCPURInsertPDTBarcode';
    $tROUTE_omnPurCNNew_selectpdt        = 'Content.php?route=omnPurCNNew&func_method=FSxCPURSelectPDT';
    $tROUTE_omnPurCNNew_save             = 'Content.php?route=omnPurCNNew&func_method=FSxCPURSave';
    $tROUTE_omnPurCNNew_delete           = 'Content.php?route=omnPurCNNew&func_method=FSxCPURDelete';
    $tROUTE_omnPurCNNew_editinline       = 'Content.php?route=omnPurCNNew&func_method=FSxCPUREditinline';
    $tROUTE_omnPurCNNew_calculate        = 'Content.php?route=omnPurCNNew&func_method=FSxCPURCalculate';
    $tROUTE_omnPurCNNew_cancelDoc        = 'Content.php?route=omnPurCNNew&func_method=FSxCPURCancelDocument';
    $tROUTE_omnPurCNNew_insertpdtByPUR1  = 'Content.php?route=omnPurCNNew&func_method=FSxCPURInsertPDTByPUR1';
    $tROUTE_omnPurCNNew_selectafter      = 'Content.php?route=omnPurCNNew&func_method=FSxCPURSelectAfter';
    $tROUTE_omnPurCNNew_listdocument     = 'Content.php?route=omnPurCNNew&func_method=FSxCPURListDocument';
    $tROUTE_omnPurCNNew_CaseProcessFail  = 'Content.php?route=omnPurCNNew&func_method=FSxCPURCaseProcessFail';
    $tROUTE_omnPurCNNew_CheckDocSplit    = 'Content.php?route=omnPurCNNew&func_method=FSaCPURCheckDocSplit';

    //ใบตรวจนับสินค้า create : napat(12-07-2019)
    $tROUTE_omnPdtAdjStkChkNew              = 'application/modules/document/controllers/pdtadjstkchk/cpdtadjstkchk.php';
    $tROUTE_omnPdtAdjStkChkNew_content      = 'Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASContentMain';

    // Create 13/04/2020 Napat(Jame)
    // รายการสินค้าเพื่อการตรวจนับ
    $tROUTE_rptAllPdtChkStk                 = 'application/modules/report/controllers/rptAllPdtChkStk/cRptAllPdtChkStk.php';
    $tROUTE_rptAllPdtChkStk_content         = 'Content.php?route=rptAllPdtChkStk&func_method=FSvCCallRptViewBeforePrint';

    // Create 14/04/2020 Napat(Jame)
    // รายงานตรวจนับสินค่าใบรวม
    $tROUTE_rptAllPdtPhysicalChkStk         = 'application/modules/report/controllers/rptAllPdtPhysicalChkStk/cRptAllPdtPhysicalChkStk.php';
    $tROUTE_rptAllPdtPhysicalChkStk_content = 'Content.php?route=rptAllPdtPhysicalChkStk&func_method=FSvCCallRptViewBeforePrint';

    // Create 21/04/2020 Napat(Jame)
    // รายงานการตรวจนับสินค้า - แจกแจงตามกลุ่มสินค้า
    $tROUTE_rptAllPdtStkChecking            = 'application/modules/report/controllers/rptAllPdtStkChecking/cRptAllPdtStkChecking.php';
    $tROUTE_rptAllPdtStkChecking_content    = 'Content.php?route=rptAllPdtStkChecking&func_method=FSvCCallRptViewBeforePrint';
    
    // Create 22/04/2020 Witsarut (Bell)
    // รายงานแจกแจงตามสถานที่ตรวจนับ - แจกแจงตามสถานที่ตรวจนับ
    $tROUTE_rptAllPdtStkCheckBylocation          = 'application/modules/report/controllers/rptAllPdtStkCheckBylocation/cRptAllPdtStkCheckBylocation.php';
    $tROUTE_rptAllPdtStkCheckBylocation_content  = 'Content.php?route=rptAllPdtStkCheckBylocation&func_method=FSvCCallRptViewBeforePrint';

    // Create 22/04/2020 nonpawich(petch)
    // รายงานสินค้าที่ไม่แสดงในเอกสารตรวจนับ
    $tROUTE_rptAllPdtNotExist         = 'application/modules/report/controllers/rptAllPdtNotExist/cRptAllPdtNotExist.php';
    $tROUTE_rptAllPdtNotExist_content = 'Content.php?route=rptAllPdtNotExist&func_method=FSvCCallRptViewBeforePrint';

    // Create 22/04/2020 Napat(Jame)
    // รายการผลการตรวจนับสต็อกสินค้า
    $tROUTE_rptAllPdtChkStkDif              = 'application/modules/report/controllers/rptAllPdtChkStkDif/cRptAllPdtChkStkDif.php';
    $tROUTE_rptAllPdtChkStkDif_content      = 'Content.php?route=rptAllPdtChkStkDif&func_method=FSvCCallRptViewBeforePrint';
    
?>