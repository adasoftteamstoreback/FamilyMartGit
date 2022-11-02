<?php
    // require_once 'application/libraries/mPDF/vendor/autoload.php'; 
    // $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
    // ob_start();
?>
<!-- <script src="<?=$tBase_url?>application/libraries/html2canvas/html2canvas.min.js?v=<?php echo date("dmyhis"); ?>"></script> -->
<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/report/assets/css/localcss/ada.rptlayout.css?v=<?php echo date("dmyhis"); ?>">
<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap.css?v=<?php echo date("dmyhis"); ?>">

<nav id="odvNavMenuReport" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="xWRptNavGroup">
                    
                    <button type="button" id="obtPrintViewHtml" class="btn btn-primary xWBTNRptPrintPreview">
                        <?php echo language('report/report','tRptPrintHtml');?>
                    </button>
                    
                </div>
            </div>
            <div class="xCNFooterReport">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    
                        <?php if($aDataReport['tCode'] == '1'){ ?>
                        <div class="xWPageReport btn-toolbar pull-right" style="padding:10px 29px;">
                            <?php if($aDataReport['nCurrentPage'] == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                            <button onclick="JSvClickPageReport('first')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?> style="padding: 2px 10px; background-color: transparent;">
                                <span style="font-size: 15px !important; color: black; font-weight: bold;">First</span>
                            </button>
                            <button onclick="JSvClickPageReport('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?> style="padding: 2px 10px; background-color: transparent;">
                                <span style="font-size: 15px !important; color: black; font-weight: bold;"><</span>
                            </button>
                            <?php for($i=max($aDataReport['nCurrentPage']-2, 1); $i<=max(0, min($aDataReport['nAllPage'],$aDataReport['nCurrentPage']+2)); $i++){?>
                                <?php 
                                    if($aDataReport['nCurrentPage'] == $i){ 
                                        $tActive = 'active'; 
                                        $tDisPageNumber = 'disabled';
                                    }else{ 
                                        $tActive = '';
                                        $tDisPageNumber = '';
                                    }
                                ?>
                                <button onclick="JSvClickPageReport('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                            <?php } ?>
                            <?php if($aDataReport['nCurrentPage'] >= $aDataReport['nAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                            <button onclick="JSvClickPageReport('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?> style="padding: 2px 10px; background-color: transparent;">
                                <span style="font-size: 15px !important; color: black; font-weight: bold;">></span>
                            </button>
                            <button onclick="JSvClickPageReport('last')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?> style="padding: 2px 10px; background-color: transparent;">
                                <span style="font-size: 15px !important; color: black; font-weight: bold;">Last</span>
                            </button>    
                        </div>
                        <?php } ?>

                    </div>
                </div>             
            </div>
        </div>
    </div>
</nav>    

<div class="xWRptTitleSampleBeforePrint col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:70px;padding-left: 0px;padding-right: 0px;">
    <ol id="oliMenuNav" class="breadcrumb xCNBCMenu">
        <li id="oliRptTitle"><?php echo language('report/report','tRptViewer') ?></li>
    </ol>
</div>

<div id="odvMainContent">
    <div class="main-content">
        <div id="odvContentPageRptViewer" class="panel panel-headline"> 
            <div class="panel-body" style="padding: 0px;">
                <?php if(isset($tViewDataTable) && !empty($tViewDataTable)):?>
                    <?php echo $tViewDataTable;?>  
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<form id="ofmRptSubmitClickPage" method="post" target="_self">
    <input type="hidden" class="form-control" id="ohdRptDocNo" name="ohdRptDocNo" value="<?php echo $tRptDocNo;?>">
    <input type="hidden" class="form-control" id="ohdRptCompCode" name="ohdRptCompCode" value="<?php echo $tRptCompCode;?>">
    <input type="hidden" class="form-control" id="ohdRptCurrentPage" name="ohdRptCurrentPage" value="<?php echo $aDataReport['nCurrentPage'];?>">
</form>

<!-- Overlay Data Viewer -->
<!-- <div class="xCNOverlayLodingData" style="z-index: 7000;">
    <img src="<?php echo $tBase_url;?>application/modules/common/assets/images/ada.loading.gif" class="xWImgLoading">
    <div id="odvOverLayContentForLongTimeLoading" style="display: none;"><?php echo language('common/systems', 'tLodingDataReport'); ?></div>
</div> -->
        
<script type="text/javascript">

    $('document').ready(function(){
        document.title = '<?php echo $tTitleReport;?>';
    });

    //Next page by report
    function JSvClickPageReport(ptPage){
        var nAllPage = '<?=$aDataReport['nAllPage']?>';
        var nPageCurrent = '';
        switch (ptPage) {
            case 'next': //กดปุ่ม Next
                $('.xWBtnNext').addClass('disabled');
                nPageOld = $('.xWPageReport .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew;
                break;
            case 'previous': //กดปุ่ม Previous
                nPageOld = $('.xWPageReport .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew;
                break;
            case 'first': //กดปุ่ม First
                nPageCurrent = 1;
                break;
            case 'last': //กดปุ่ม Last
                nPageCurrent = nAllPage;
                break;    
            default:
                nPageCurrent = ptPage;
        }

        JCNvCallDataReportPageClick(nPageCurrent);
    }

    // Function Call Data Rpt
    function JCNvCallDataReportPageClick(pnPageCurrent){
        $('#ohdRptCurrentPage').val(pnPageCurrent);
        $('#ofmRptSubmitClickPage').attr('action','<?=$tBase_url?>?route=<?=$tRptRoute?>&calltype=<?=$tCallType?>&Param=<?=$tParameter?>');
        $('#ofmRptSubmitClickPage').submit();
        $('#ofmRptSubmitClickPage').attr('action','javascript:void(0)');
    }

    $('#obtPrintViewHtml').off('click')
    $('#obtPrintViewHtml').on('click',function(){

        // $("#ostPdf").css({ opacity: 1 });
        // html2canvas($('#ostPdf')[0], {
        //     scale:3
        // }).then(function(canvas) {
        //     console.log(canvas);
        //     var image = canvas.toDataURL('image/png');
        //     console.log(image);
        //     var d = new Date();
        //     var n = d.getTime();
        //     $.ajax({
        //         type: 'POST',
        //         url: "Content.php?route=<?=$tRptRoute?>&func_method=FSxCCallRptPrintPDF",
        //         data: {
        //             base64Image : image,
        //             image_name  : "pdf2"
        //         },
        //         success: function(image) {
        //             // var d = new Date();
        //             // var n = d.getTime();
        //             console.log(image);
        //             // window.location = image+"?t="+n;
        //         }
        //     });
        // });
        // html2canvas($('#ostPdf').get(0)).then( function (canvas) {
        //     console.log(canvas);
        //     var image = canvas.toDataURL('image/png');
        //     console.log(image);
        //     var d = new Date();
        //     var n = d.getTime();
        //     $.ajax({
        //         type: 'POST',
        //         url: "Content.php?route=<?=$tRptRoute?>&func_method=FSxCCallRptPrintPDF",
        //         data: {
        //             base64Image : image,
        //             image_name  : "pdf1"
        //         },
        //         success: function(image) {
        //             // var d = new Date();
        //             // var n = d.getTime();
        //             console.log(image);
        //             // window.location = image+"?t="+n;
        //         }
        //     });
        // });

        
        // html2canvas($('#ostPdf').get(0),{
        //     then: function(canvas) {

        //         console.log(canvas);

        //         var image = canvas.toDataURL('image/png');
        //         $.ajax({
        //             type: 'POST',
        //             url: "Content.php?route=<?=$tRptRoute?>&func_method=FSxCCallRptPrintPDF",
        //             data: {
        //                 base64Image:image,
        //                 image_name:"pdf"
        //             },
        //             success: function(image) {
        //                 var d = new Date();
        //                 var n = d.getTime();
        //                 console.log(image+"?t="+n);
        //                 // window.location = image+"?t="+n;
        //             }
        //         });

        //     }
        // });

        $.ajax({
            type: "POST",
            url: "Content.php?route=<?=$tRptRoute?>&func_method=FSvCCallRptViewBeforePrint",
            data: {
                nStaPrintPDF    : 1,
                ohdRptDocNo     : $('#ohdRptDocNo').val(),
                ohdRptCompCode  : $('#ohdRptCompCode').val()
            },
            cache: false,
            timeout: 0,
            async: false,
            success: function(oResult){

                var printWindow = window.open();
                printWindow.document.write('<title>'+$('#ohdReportTitle').val()+'</title>');  
                printWindow.document.write(oResult);
                printWindow.document.close();
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });

    });

</script>