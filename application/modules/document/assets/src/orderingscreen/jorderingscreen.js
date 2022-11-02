$('document').ready(function(){
    JSxODSCallPageMain();
    $('#obtODSLoadOrder').show();
    $('#obtODSSave').hide();
    $('#obtODSCancel').hide();
    $('#obtODSConfirmOrder').hide();
    $('#obtODSCopySGOQTY').hide();
    $('#obtODSNew').hide();

    var nStaDocSuccess  = parseInt($('#oetODSStaDocSuccess').val());
    var tText           = $('#oetODSTexttModalHeadDocComplete').val();
    if(nStaDocSuccess == 1){
        var aMessage  = {
            tHead   : tText,
            tDetail : tText,
            tType   : 2
        };
        JSxODSAlertMessage(aMessage);
    }
});

function JSxODSCallPageMain(ptDocNo,ptType){
    if(ptDocNo == "" || ptDocNo === undefined){ ptDocNo = ''; }
    if(ptType == "" || ptType === undefined){ ptType = ''; }

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSCallPageMain",
        data: {
            tDocNo: ptDocNo
        },
        cache: false,
        timeout: 0,
        success: function(tResult){
            $('#odvODSContentMain').html(tResult);
            var tDocNo = $('#oetODSDocNo').val();
            // if(ptType!=""){
            //     $('.nav-tabs a[href="#odvODSContentSUMMARY"]').tab('show');
            //     JSxODSDataTable('SUMMARY',tDocNo);
            //     $('#oetODSSelectSectionType').val('SUMMARY');
            // }else{
                JSxODSDataTable('',tDocNo,'');
            // }
            JSxODSControlButton();
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}


function JSxODSDataTable(ptSec,ptDocNo,pnPageCurrent,ptFromSec,ptSeqNo){
    var nLimitRecord = $('#osmODSLimitRecord option:selected').val();
    if(ptSec == "" || ptSec === undefined){ ptSec = "NEW"; }
    if(ptDocNo == "" || ptDocNo === undefined){ ptDocNo = ''; }
    if(pnPageCurrent == "" || pnPageCurrent === undefined){ pnPageCurrent = 1; }
    if(ptSeqNo == "" || ptSeqNo === undefined){ ptSeqNo = ''; }
    if(ptSec != "SUMMARY"){
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSDataTable",
            data: {
                paSortBycolumn  : [$('#ohdNameSort').val(),$('#ohdTypeSort').val()],
                tSection        : ptSec,
                tDocNo          : ptDocNo,
                nPageCurrent    : pnPageCurrent,
                ptFromSec       : ptFromSec,
                pnLimitRecord   : nLimitRecord
            },
            cache: false,
            async : true,
            timeout: 0,
            success: function(oResult){
                var aReturn = JSON.parse(oResult);
                // JSxODSDisableButtonSaveByOrdLot(aReturn['pnFoundOrdLot']);
                if(ptFromSec == "SUMMARY"){
                    $('#odvODSContentDetailSUMMARY').html(aReturn['ptGetDataSummary']);
                    $('#odvODSContentDetailSUMMARY_' + ptSec).html(aReturn['ptDataTable']);
                }else{
                    JSxODSControlCentent();
                    $('#odvODSContentDetail' + ptSec).html(aReturn['ptDataTable']);
                }
                // setTimeout(function(){
                    $('#odvODSScriptDataTable').html(aReturn['tScript']);//Insert Script DataTable
                // }, 100);
                JSxODSControlButton();

                if(ptSeqNo != ''){
                    setTimeout(function(){
                        $('.xCNTableTrClickActive').removeClass('xCNTableTrActive');
                        $('.xWODSTr' + ptSec + ptSeqNo).addClass('xCNTableTrActive');
                        $('#oetODSPdtOrdLot' + ptSec + ptSeqNo).focus();
                        $('#oetODSPdtOrdLot' + ptSec + ptSeqNo).select();
                    }, 100);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    }else{
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSDataTableSummary",
            data: {
                paSortBycolumn  : [$('#ohdNameSort').val(),$('#ohdTypeSort').val()],
                tSection        : ptSec,
                tDocNo          : ptDocNo,
                nPageCurrent    : pnPageCurrent,
                pnLimitRecord   : nLimitRecord
            },
            cache: false,
            async : true,
            timeout: 0,
            success: function(oResult){
                JSxODSControlCentent();
                var aReturn = JSON.parse(oResult);
                // JSxODSDisableButtonSaveByOrdLot(aReturn['pnFoundOrdLot']);
                $('#odvODSContentDetailSUMMARY').html(aReturn['tSum']);
                $('#odvODSContentDetailSUMMARY_NEW').html(aReturn['tNew']);
                $('#odvODSContentDetailSUMMARY_PROMOTION').html(aReturn['tPro']);
                $('#odvODSContentDetailSUMMARY_TOP1000').html(aReturn['tTop']);
                $('#odvODSContentDetailSUMMARY_OTHER').html(aReturn['tOth']);
                $('#odvODSContentDetailSUMMARY_ADDON').html(aReturn['tAdd']);

                //Insert Script DataTable
                // setTimeout(function(){
                    $('#odvODSScriptDataTable').html(aReturn['tScript']);
                // }, 100);
                JSxODSControlButton();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    }
}

//Check has Order Lot > 0
// function JSxODSDisableButtonSaveByOrdLot(nCountOrdLot){
//     console.log('JSxODSDisableButtonSaveByOrdLot: ' + nCountOrdLot);
    
//     $('#obtODSSave').show();
//     if(nCountOrdLot == 0){
//         $('#obtODSSave').hide();
//     }else{
//         $('#obtODSSave').show();
//     }
// }

function JSxODSAddDocTmpDT(){
    var dDateOrder = $('#oetODSOrderDate').val();
    if(dDateOrder == ""){
        dDateOrder = $('#oetODSOrderDate').data('oldval');
    }
    JSxContentLoader('show');
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSAddDocTmpDT",
        data: {
            pdDateOrder : dDateOrder,
        },
        cache: false,
        timeout: 0,
        success: function(oResult){
            var aReturn = JSON.parse(oResult);
            console.log(aReturn);
            
            switch(aReturn['nStaCheckInsertData']){
                case 77:
                    var aErrorMes = {
                        tHead       : 'แจ้งเตือน',
                        tDetail     : 'ไม่มีข้อมูลในฐานข้อมูล',
                        tType       : 2
                    };
                    JSxODSAlertMessage(aErrorMes);
                    break;
                case 99:
                    var tError      = aReturn['tReturnAddData']['tReturnInsert'];
                    var tErrDetail  = "";
                    for(var i=0;i<tError.length;i++){
                        tErrDetail += tError[i]['message']+"<br>";
                    }
                    var aErrorMes = {
                        tHead       : 'Error Query',
                        tDetail     : tErrDetail,
                        tType       : 2
                    };
                    JSxODSAlertMessage(aErrorMes);
                    break;
                default:
                    $('.nav-tabs a[href="#odvODSContentNEW"]').tab('show');
                    $('#odvODSContentMain').html(aReturn['tLoadViewMain']);
                    $('#obtODSCopySGOQTY').show();
                    $('#obtODSLoadOrder').hide();
                    $('#oetODSOrderDate').attr('disabled',true);
                    $('#oetODSCheckDataTemp').val(1);
                    JSxODSSaveHDDT();

                    // setTimeout(function(){ 
                        //$('#obtODSSave').show(); 
                    // }, 500);
                    break;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxODSSaveHDDT(){
    var tDocNo          = $('#oetODSDocNo').val();
    var tCurrentPage    = $('#oetODSCurrentPage').val();
    var tSection        = $('#oetODSSelectSectionType').val();
    var dOrderDate      = $('#oetODSOrderDate').val();

    if(tDocNo == "" || tDocNo === undefined){ tDocNoType = 1; }else{ tDocNoType = 2; } //1=Add , 2=Edit
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSAddEditHDDT",
        data: {
            nStaSave     : tDocNoType,
            ptDocNo      : tDocNo,
            pdOrderDate  : dOrderDate
        },
        cache: false,
        timeout: 0,
        success: function(oResult){
            var aReturn = JSON.parse(oResult);
            if(tDocNoType == 1){
                JSxODSCallPageMain(aReturn['FTXohDocNo'],'save');
                $('#obtODSCancel').show();
                $('#obtODSConfirmOrder').show();
                JSxContentLoader('hide');
                // $('.odvLoaderprogress').css('display','none');
                // JSxODSDataTable();
            }
            // else if(tDocNoType == 2){

            //     if(tSection == "SUMMARY"){
            //         JSxODSDataTable(tSection,tDocNo,'');
            //     }else{
            //         JSxODSDataTable(tSection,tDocNo,tCurrentPage);
            //     }

            //     // console.log('JSxODSDataTable: ' + tSection + ' + ' + tDocNo);

            // }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });

}

//อนุมัติเอกสาร
function JSxODSConfirmOrder(){
    $('#odvModalApprove').modal('show');
    $('.xCNBTNActionConfirmApprove').off('click');
    $('.xCNBTNActionConfirmApprove').on( "click", function( event ) {
        var tDocNo = $('#oetODSDocNo').val();

        //หาสำนักงานใหญ่ก่อนอนุมัติ
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSChkBchHQ",
            data: {
                ptDocNo : tDocNo,
            },
            cache: false,
            timeout: 0,
            success: function(oResult){
                var aReturn = JSON.parse(oResult);

                // ถ้ามีสำนักงานใหญ่ สามารถอนุมัติได้
                if(aReturn['aChkBchHQ']['nStaQuery'] == 1){
                    // ตรวจสอบผู้จำหน่าย
                    if(aReturn['aChkSplB4Apv']['nStaQuery'] == 99){
                        $.ajax({
                            type: "POST",
                            url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSConfirmOrder",
                            data: {
                                ptDocNo : tDocNo,
                            },
                            cache: false,
                            timeout: 0,
                            success: function(){
                                // $('#obtODSSave').hide();
                                // $('#oetODSCheckPOFlag').val(0);
                                // $('#odvModalApprove').modal('hide');
                                var paParams = {
                                    'MQApprove' : 'ORDER',
                                    'MQDelete'  : 'ORDERDEL',
                                    'params'    : {
                                        'ptDocNo'           : tDocNo,
                                        'ptRouteOther'      : $('#oetRabbitOrderingScreenUpdateApprove').val(),
                                        'ptRouteSuccess'    : $('#oetODSRouteSuccessApprove').val()
                                    },
                                };
                                SubcribeToRabbitMQ(paParams);

                                $('#osmConfirmRabbit').off('click');
                                $('#osmConfirmRabbit').on('click',function(){
                                    setTimeout(function(){ 
                                        //กดปุ่มแล้วให้โหลดหน้าใหม่ทันที 19-06-62 พี่หมากฮอส
                                        JSxODSCallPageMain();
                                        $('#obtODSCancel').hide();
                                        $('#obtODSCopySGOQTY').hide();
                                        $('#obtODSConfirmOrder').hide();
                                    }, 500);
                                });
                    
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                            }
                        });
                    }else{
                        // Comsheet 2020-194 ถ้าพบสินค้าไม่มีผู้จำหน่ายให้นำแสดงรายการ
                        var aItems = aReturn['aChkSplB4Apv']['aItems'];
                        var tHTML  = '';
                        for(var i=0;i<aItems.length;i++){
                            tHTML += aItems[i]['FTPdtBarCode'] + "&nbsp;&nbsp;&nbsp;" + aItems[i]['FTPdtName'] + "<br>";
                        }

                        var aMeg = {
                            tType     : 2,
                            tHead     : 'พบสินค้าไม่มีผู้จำหน่าย',
                            tDetail   : tHTML,
                        };
                        JSxODSAlertMessage(aMeg);
                    }
                }else{
                    var aMeg = {
                        tType     : 2,
                        tHead     : 'อนุมัติ',
                        tDetail   : 'ไม่พบข้อมูลรหัสสาขาสำนักงานใหญ่',
                    };
                    JSxODSAlertMessage(aMeg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
        
    });
    
}

function JSxODSEditInLine(pnSeq,ptSec,ptOldVal,ptIndex,paText,ptNextStep,ptFocusDir){
    var nOrderLot       = $('#oetODSPdtOrdLot' + ptSec + pnSeq).val();
    var tPdtBarCode     = $('#oetODSPdtOrdLot' + ptSec + pnSeq).data('barcode');
    var nPdtLotSize     = nOrderLot * $('#oetODSPdtLotSize' + ptSec + pnSeq).val();
    var tDocNo          = $('#oetODSDocNo').val();
    var tCurrentPage    = $('#oetODSCurrentPageInTab' + ptSec).val();
    var tSectionType    = $('#oetODSSelectSectionType').val();
    var tValTemp        = $('#oetODSPdtOrdLot' + ptSec + pnSeq).data('valtmp'); //24-10-62 Jame

    $('#oetODSStaEdit').val("1");

    // console.log('nOrderLot: ' + nOrderLot + ' ptOldVal: ' + ptOldVal + ' tValTemp: ' + tValTemp);
    // if(ptSec != "ADDON"){
    //     console.log('Not AddON');
    
        if( (String(nOrderLot) != String(tValTemp)) ){
            // console.log(1);
            if( (String(nOrderLot) != String(ptOldVal)) ){
                // console.log(2);
                // if(String(nOrderLot) == "" && String(ptOldVal) != ""){
                    // console.log(3);
                    if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
                    if(nOrderLot == "" || nOrderLot === undefined){ nPdtLotSize = 'NULL'; nOrderLot = 'NULL'; }
                    // if(ptOldVal == "" || ptOldVal === undefined){ ptOldVal = 'NULL'; }
                    // if(tValTemp == "" || tValTemp === undefined){ tValTemp = 'NULL'; }
                    if(ptNextStep == "" || ptNextStep === undefined){ ptNextStep = 'TRUE'; }
                    $.ajax({
                        type: "POST",
                        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateOrderLot",
                        data: {
                            nSeq        : pnSeq,
                            tPdtBarCode : tPdtBarCode,
                            nOrderLot   : nOrderLot,
                            nPdtLotSize : nPdtLotSize,
                            tSection    : ptSec,
                            tDocNo      : tDocNo,
                        },
                        cache: false,
                        timeout: 0,
                        success: function(oResult){
                            var aReturn = JSON.parse(oResult);
                            
                            if(aReturn['nSta'] == 1){
                                JSxODSDataTable(ptSec,tDocNo,tCurrentPage,tSectionType);
                                if(ptNextStep == 'TRUE'){
                                    setTimeout(function(){
                                        switch(ptFocusDir){
                                            case "UP":
                                                $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                                                break;
                                            case "DOWN":
                                                $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                                                break;
                                        }
                                    }, 300);
                                }
                                $('#obtODSSave').show();
                            }else{
                                JSxODSAlertMessage(paText);
                                $('#odvModalAlertMessage').off('hidden.bs.modal');
                                $('#odvModalAlertMessage').on('hidden.bs.modal', function(){
                                    $('.xWInputPdtOrdLot').eq(ptIndex).focus();
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                        }
                    });
                // }else{
                //     console.log(4);
                //     if(ptSec == "ADDON"){
                //         console.log('ADDON');
                //     }else if(ptNextStep == 'TRUE'){
                //         switch(ptFocusDir){
                //             case "UP":
                //                 $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                //                 break;
                //             case "DOWN":
                //                 $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                //                 break;
                //         }
                //     }
                // }
            }else{
                // console.log(5);
                if(ptSec == "ADDON"){
                    if( (String(nOrderLot) != String(ptOldVal)) ){
                        if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
                        if(nOrderLot == "" || nOrderLot === undefined){ nPdtLotSize = 'NULL'; nOrderLot = 'NULL'; }
                        if(ptNextStep == "" || ptNextStep === undefined){ ptNextStep = 'TRUE'; }
                        $.ajax({
                            type: "POST",
                            url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateOrderLot",
                            data: {
                                nSeq        : pnSeq,
                                tPdtBarCode : tPdtBarCode,
                                nOrderLot   : nOrderLot,
                                nPdtLotSize : nPdtLotSize,
                                tSection    : ptSec,
                                tDocNo      : tDocNo,
                            },
                            cache: false,
                            timeout: 0,
                            success: function(oResult){
                                var aReturn = JSON.parse(oResult);
                                
                                if(aReturn['nSta'] == 1){
                                    JSxODSDataTable(ptSec,tDocNo,tCurrentPage,tSectionType);
                                    if(ptNextStep == 'TRUE'){
                                        setTimeout(function(){
                                            switch(ptFocusDir){
                                                case "UP":
                                                    $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                                                    break;
                                                case "DOWN":
                                                    $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                                                    break;
                                            }
                                        }, 300);
                                    }
                                }else{
                                    JSxODSAlertMessage(paText);
                                    $('#odvModalAlertMessage').off('hidden.bs.modal');
                                    $('#odvModalAlertMessage').on('hidden.bs.modal', function(){
                                        $('.xWInputPdtOrdLot').eq(ptIndex).focus();
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                            }
                        });
                    }else{
                        if(ptNextStep == 'TRUE'){
                            switch(ptFocusDir){
                                case "UP":
                                    $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                                    break;
                                case "DOWN":
                                    $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                                    break;
                            }
                        }
                    }
                }else{
                    if(ptNextStep == 'TRUE'){
                        switch(ptFocusDir){
                            case "UP":
                                $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                                break;
                            case "DOWN":
                                $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                                break;
                        }
                    }
                }
                
            }
        }else{
            if(String(nOrderLot) == "" && String(ptOldVal) != ""){
                // console.log(6);
                if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
                if(nOrderLot == "" || nOrderLot === undefined){ nPdtLotSize = 'NULL'; nOrderLot = 'NULL'; }
                if(ptNextStep == "" || ptNextStep === undefined){ ptNextStep = 'TRUE'; }
                $.ajax({
                    type: "POST",
                    url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateOrderLot",
                    data: {
                        nSeq        : pnSeq,
                        tPdtBarCode : tPdtBarCode,
                        nOrderLot   : nOrderLot,
                        nPdtLotSize : nPdtLotSize,
                        tSection    : ptSec,
                        tDocNo      : tDocNo,
                    },
                    cache: false,
                    timeout: 0,
                    success: function(oResult){
                        var aReturn = JSON.parse(oResult);
                        
                        if(aReturn['nSta'] == 1){
                            JSxODSDataTable(ptSec,tDocNo,tCurrentPage,tSectionType);
                            if(ptNextStep == 'TRUE'){
                                setTimeout(function(){
                                    switch(ptFocusDir){
                                        case "UP":
                                            $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                                            break;
                                        case "DOWN":
                                            $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                                            break;
                                    }
                                }, 300);
                            }
                        }else{
                            JSxODSAlertMessage(paText);
                            $('#odvModalAlertMessage').off('hidden.bs.modal');
                            $('#odvModalAlertMessage').on('hidden.bs.modal', function(){
                                $('.xWInputPdtOrdLot').eq(ptIndex).focus();
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                    }
                });
            }else{
                // console.log(7);
                $('#oetODSPdtOrdLot' + ptSec + pnSeq).val('');
                if(ptNextStep == 'TRUE'){
                    switch(ptFocusDir){
                        case "UP":
                            $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
                            break;
                        case "DOWN":
                            $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
                            break;
                    }
                }
            }
        }
    // }else{
    //     console.log(7);
    // }
    //     console.log('AddON');
    // }

    // if(String(tValTemp) != ""){
    // console.log("nOrderLot: "+String(nOrderLot));
    // console.log("tValTemp: "+String(tValTemp));
    // }
    // if(String(tValTemp) != "" && (String(nOrderLot) == String(tValTemp))){
    //     $('#oetODSPdtOrdLot' + ptSec + pnSeq).val('');
    //     // return false;
    // }else{
    //     console.log("False" + String(nOrderLot) + " " + String(tValTemp));
    //     // return true;
    // }
    // console.log('nOrderLot: ' + String(nOrderLot) + "!=" + " ptVal: " + String(ptVal));
    // console.log("nOrderLot: " + ptVal + " tValTemp: " + tValTemp);

    

    

    // if( nOrderLot = "NULL" && ptSec == "ADDON"){
    //     console.log(1);
    //     $.ajax({
    //         type: "POST",
    //         url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateOrderLot",
    //         data: {
    //             nSeq        : pnSeq,
    //             tPdtBarCode : tPdtBarCode,
    //             nOrderLot   : nOrderLot,
    //             nPdtLotSize : nPdtLotSize,
    //             tSection    : ptSec,
    //             tDocNo      : tDocNo,
    //         },
    //         cache: false,
    //         timeout: 0,
    //         success: function(oResult){
    //             var aReturn = JSON.parse(oResult);
    //             // console.log(aReturn);
    //             if(aReturn['nSta'] == 1){
    //                 JSxODSDataTable(ptSec,tDocNo,tCurrentPage,tSectionType);
    //                 if(ptNextStep == 'TRUE'){
    //                     setTimeout(function(){
    //                         if(ptFocusDir == 'UP'){
    //                             $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
    //                         }else{
    //                             $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
    //                         }
    //                     }, 300);
    //                 }
    //             }else{
    //                 JSxODSAlertMessage(paText);
    //                 $('#odvModalAlertMessage').off('hidden.bs.modal');
    //                 $('#odvModalAlertMessage').on('hidden.bs.modal', function(){
    //                     $('.xWInputPdtOrdLot').eq(ptIndex).focus();
    //                 });
    //             }
    //         },
    //         error: function(jqXHR, textStatus, errorThrown) {
    //             console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
    //         }
    //     });
    // }



    // console.log('ptVal: ' + ptVal);
    // console.log('nOrderLot: ' + nOrderLot + " tValTemp: " + tValTemp);
    // if( (String(nOrderLot) != String(ptVal)) ){
    //     if((String(nOrderLot) == String(tValTemp)) || (String(nOrderLot) == "NULL" && String(tValTemp) != ""  && ptSec != "ADDON") || (String(nOrderLot) == "NULL" && String(ptVal) == "")){
    //         console.log(2);
    //         $('#oetODSPdtOrdLot' + ptSec + pnSeq).val('');
    //         if(ptNextStep == 'TRUE'){
    //             if(ptFocusDir == 'UP'){
    //                 $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
    //             }else{
    //                 $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
    //             }
    //         }
    //     }else{
    //         console.log(3);
    //         $.ajax({
    //             type: "POST",
    //             url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateOrderLot",
    //             data: {
    //                 nSeq        : pnSeq,
    //                 tPdtBarCode : tPdtBarCode,
    //                 nOrderLot   : nOrderLot,
    //                 nPdtLotSize : nPdtLotSize,
    //                 tSection    : ptSec,
    //                 tDocNo      : tDocNo,
    //             },
    //             cache: false,
    //             timeout: 0,
    //             success: function(oResult){
    //                 var aReturn = JSON.parse(oResult);
    //                 // console.log(aReturn);
    //                 if(aReturn['nSta'] == 1){
    //                     JSxODSDataTable(ptSec,tDocNo,tCurrentPage,tSectionType);
    //                     if(ptNextStep == 'TRUE'){
    //                         setTimeout(function(){
    //                             if(ptFocusDir == 'UP'){
    //                                 $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
    //                             }else{
    //                                 $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
    //                             }
    //                         }, 300);
    //                     }
    //                 }else{
    //                     JSxODSAlertMessage(paText);
    //                     $('#odvModalAlertMessage').off('hidden.bs.modal');
    //                     $('#odvModalAlertMessage').on('hidden.bs.modal', function(){
    //                         $('.xWInputPdtOrdLot').eq(ptIndex).focus();
    //                     });
    //                 }
    //             },
    //             error: function(jqXHR, textStatus, errorThrown) {
    //                 console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
    //             }
    //         });
    //     }
    // }else{
    //     console.log(4);
    //     if(ptNextStep == 'TRUE'){
    //         if(ptFocusDir == 'UP'){
    //             $('.xWInputPdtOrdLot').eq(ptIndex - 1).focus();
    //         }else{
    //             $('.xWInputPdtOrdLot').eq(ptIndex + 1).focus();
    //         }
    //     }
    // }
    
    $('#oetODSStaEdit').val("0");
}

function JSxODSDisplayInLine(pnPdtCode,pnType){
    if(pnType == 1){
        $('#odvODSPdtOrdLotText' + pnPdtCode).addClass('xCNHide');
        $('#odvODSPdtOrdLotInput' + pnPdtCode).removeClass('xCNHide');
        $('#oimODSIconReply' + pnPdtCode).removeClass('xCNHide');
        $('#oimODSIconSave' + pnPdtCode).removeClass('xCNHide');
        $('#oimODSIconEdit' + pnPdtCode).addClass('xCNHide');
    }else if(pnType == 0){
        $('#odvODSPdtOrdLotText' + pnPdtCode).removeClass('xCNHide');
        $('#odvODSPdtOrdLotInput' + pnPdtCode).addClass('xCNHide');
        $('#oimODSIconReply' + pnPdtCode).addClass('xCNHide');
        $('#oimODSIconSave' + pnPdtCode).addClass('xCNHide');
        $('#oimODSIconEdit' + pnPdtCode).removeClass('xCNHide');
    }
}

//Next previous
function JSvODSClickPage(ptPage,ptSec,ptFromSec){
    var tDocNo = $('#oetODSDocNo').val(); 
    var nPageCurrent = '';
    var nPageNew;

    nPageOld        = $('.xWPageOrderingScreen' + ptSec + ' .active').text(); // Get เลขก่อนหน้า
    nPageNew        = parseInt(nPageOld, 10) / 10 + 1; // +1 จำนวน
    nPageCurrent    = nPageNew;

    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            $('.xWBtnNext').addClass('disabled');
            // nPageOld = $('.xWPageOrderingScreen .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
            nPageCurrent = nPageNew;
            break;
        case 'previous': //กดปุ่ม Previous
            // nPageOld = $('.xWPageOrderingScreen .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }

    if(tDocNo == "" || tDocNo === undefined){
        JSxODSDataTable(ptSec,'',nPageCurrent,ptFromSec);//JSxODSDataTable(ptSec,ptDocNo,pnPageCurrent
    }else{
        JSxODSDataTable(ptSec,tDocNo,nPageCurrent,ptFromSec);
    }

    $('#oetODSCurrentPage').val(nPageCurrent);
    // console.log(ptFromSec);
}

function JSxODSClickPageSearch(ptPage){
    var nPageCurrent = '';
    var nPageNew;

    nPageOld        = $('.xWPageOrderingScreenSearch .active').text(); // Get เลขก่อนหน้า
    nPageNew        = parseInt(nPageOld, 10) / 10 + 1; // +1 จำนวน
    nPageCurrent    = nPageNew;

    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            $('.xWBtnNext').addClass('disabled');
            nPageOld = $('.xWPageOrderingScreenSearch .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
            nPageCurrent = nPageNew;
            break;
        case 'previous': //กดปุ่ม Previous
            nPageOld = $('.xWPageOrderingScreenSearch .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }
    JSxODSDataSearchList(nPageCurrent);
}

function JSxODSClickTab(ptSec){
    $('#oetODSSelectSectionType').val(ptSec);
    var tDocNo = $('#oetODSDocNo').val();

    if(tDocNo != ""){
        JSxODSDataTable(ptSec,tDocNo,'');
    }else{
        JSxODSDataTable(ptSec,'','');
    }
    // console.log('ClickTab DocNo: ' + tDocNo + ' ptSec: ' + ptSec);
	JSxODSControlButton();
}

function JSxODSControlButton(){
    var tDocNo          = $('#oetODSDocNo').val();
    var tSectionType    = $('#oetODSSelectSectionType').val();
    var nStaPrcDoc      = $('#oetODSStaPrcDoc').val();
    var nStaDoc         = $('#oetODSStaDoc').val();
    var nStaDataTemp    = $('#oetODSCheckDataTemp').val();
    // var nCountChkPOFlag = parseInt($('#oetODSCheckPOFlag').val());
    var nTotalSKU       = parseInt($('#oetODSTotalSKU').val());

    $('#obtODSNew').hide();

	if(tSectionType == "SUMMARY"){
        if(nTotalSKU > 0){
            $('#obtODSConfirmOrder').removeClass('xCNHide');
            $('#obtODSSave').show();
            
        }else{
            $('#obtODSConfirmOrder').addClass('xCNHide');
            $('#obtODSSave').hide();
        }
		$('#obtODSCopySGOQTY').addClass('xCNHide');
        $('#obtODSLoadOrder').addClass('xCNHide');
	}else{
		$('#obtODSConfirmOrder').addClass('xCNHide');
		$('#obtODSCopySGOQTY').removeClass('xCNHide');
		$('#obtODSLoadOrder').removeClass('xCNHide');
    }
    
    //กรณีกดปุ่มบันทึกแล้ว
    if(tDocNo != ""){
        // $('#obtODSSave').show();
        $('#obtODSConfirmOrder').show();
        $('#obtODSLoadOrder').hide();
        $('#obtODSCancel').show();
        $('#obtODSCopySGOQTY').show();
        $('#oetODSOrderDate').attr('disabled',true);
        $('#obtODSNew').hide();

        // if(nCountChkPOFlag > 0){
            // $('#obtODSSave').hide();
        // }
        // else{
        //     $('#obtODSSave').hide();
        // }
    }

    //กรณียกเลิก กรณีอนุมัติ
    if(nStaPrcDoc == 1 || nStaDoc == 3){
        var nCheckNotSubmit = $('#oetODSCheckNotSubmit').val();
        if(nCheckNotSubmit == 99){
            $('#obtODSNew').show().attr('disabled',false);
        }else{
            $('#obtODSNew').hide();
        }
        $('#obtODSLoadOrder').hide();
        $('#obtODSSave').hide();
        $('#obtODSCancel').hide();
        $('#obtODSConfirmOrder').hide();
        $('#obtODSCopySGOQTY').hide();
        $('#oetODSOrderDate').attr('disabled',true);
        $('.xWInputPdtOrdLot').attr('disabled',true);
        // $('.xWInputPdtOrdLot').addClass('xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
        $('.xCNInsertInputPDTorBarcode').attr('disabled',true);
        $('.xCNImageInsert').addClass('xCNBlockWhenApprove');
    }else{
        if(nStaDataTemp == 1){
            $('.xCNImageInsert').removeClass('xCNBlockWhenApprove');
            $('.xCNInsertInputPDTorBarcode').attr('disabled',false);
        }
    }
}

function JSxODSControlCentent(){
    // var tSectionType = $('#oetODSSelectSectionType').val();
	// if(tSectionType == "SUMMARY"){
    $('#odvODSContentDetailNEW').html('');
    $('#odvODSContentDetailPROMOTION').html('');
    $('#odvODSContentDetailTOP1000').html('');
    $('#odvODSContentDetailOTHER').html('');
    

    $('#odvODSContentDetailSUMMARY').html('');
    $('#odvODSContentDetailSUMMARY_NEW').html('');
    $('#odvODSContentDetailSUMMARY_PROMOTION').html('');
    $('#odvODSContentDetailSUMMARY_TOP1000').html('');
    $('#odvODSContentDetailSUMMARY_OTHER').html('');
	// }
}

function JSxODSCancelOrder(aMessage){
    var tDocNo = $('#oetODSDocNo').val();
    if(tDocNo != ""){
        JSxODSAlertMessage(aMessage);
        $('.xWODSConfirmAlertMessage').off("click");
        $('.xWODSConfirmAlertMessage').on("click", function(){
            $.ajax({
                type: "POST",
                url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdateStaDoc",
                data: {
                    pnStaDoc    : '3',
                    ptDocNo     : tDocNo
                },
                cache: false,
                timeout: 0,
                success: function(oResult){
                    JSxODSCallPageMain();
                    $('#obtODSCancel').hide();
                    $('#obtODSCopySGOQTY').hide();
                    $('#obtODSConfirmOrder').hide();
                    $('#obtODSSave').hide();
                    // JSxODSControlButton();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                }
            }); 
        });
    }
}

function JSxODSCopySGOQTY(){
    var tSectionType    = $('#oetODSSelectSectionType').val();
    var tDocNo          = $('#oetODSDocNo').val();

    if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSCopySGOQTY",
        data: {
            ptSec    : tSectionType,
            ptDocNo  : tDocNo
        },
        cache: false,
        timeout: 0,
        success: function(oResult){
            // var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
            JSxODSDataTable(tSectionType,tDocNo,'');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxODSDataSearchList(nPageCurrent){
    if(nPageCurrent == '' || nPageCurrent == null){ nPageCurrent = 1; }
    $.ajax({
        url     : "Content.php?route=omnOrderingScreen&func_method=FSxCODSSearchPOHD",
        data    : { 
            pnPageCurrent    : nPageCurrent
        },
        type    : 'POST',
        success : function(tResult){
            $('#odvModalBodyListSearch').html(tResult);
        }
    });
}

function JSxODSSelectDocument(elem,pnID){
    if(elem.getAttribute("data-dblclick") == null){
        elem.setAttribute("data-dblclick", 1);
        setTimeout(function () {
            if (elem.getAttribute("data-dblclick") == 1) {
                $('#otbTableOrderingScreenHD tbody tr').removeClass('xCNActiveRecord');
                $(elem).addClass('xCNActiveRecord');
                $('.xCNBTNActionListSearch').off("click");
                $('.xCNBTNActionListSearch').on("click", function( event ) {
                    JSxODSCallPageMain(pnID);
                    $('#odvModalListSearch').modal('hide');
                });
            }
            elem.removeAttribute("data-dblclick");
        }, 300);
    }else{
        elem.removeAttribute("data-dblclick");
        var tEvent = 'Doubleclick';
        JSxODSCallPageMain(pnID);
        $('#odvModalListSearch').modal('hide');
    }
}

function JSxODSCloseBrowser(ptType){
    $.ajax({
        url     : "Content.php?route=omnOrderingScreen&func_method=FSxCODSCloseBrowser",
        type    : 'POST',
        success : function(oResult){
            var aReturn = JSON.parse(oResult);
            
                // if(ptType == 'close'){ //เลิกใช้งานแล้ว
                //     if(aReturn['nStaQuery'] == 1){
                //         $('#odvModalCloseBrowser').modal('show');
                //         $('.xCNBTNClosebrowser').bind( "click", function( event ) {
                //             window.location.href = 'http://closekiosk';
                //         });
                //     }else{
                //         window.location.href = 'http://closekiosk';
                //     }
                // }
            if(ptType == 'search'){
                if(aReturn['nStaQuery'] == 1){
                    $('#odvModalCloseBrowser').modal('show');
                    $('.xCNBTNClosebrowser').off("click");
                    $('.xCNBTNClosebrowser').on("click",function( event ) {
                        $('#odvModalListSearch').modal('show');
                        JSxODSDataSearchList();
                        $('#odvModalCloseBrowser').modal('hide');
                    });
                }else{
                    $('#odvModalListSearch').modal('show');
                    JSxODSDataSearchList();
                }
            }
            
        }
    });
}

function JSxODSAddPdtBrowse(elem,ptKey){
    var aBrwDataPdt     = JSON.parse(elem);
    var tDocNo          = $('#oetODSDocNo').val();
    var tSectionFrom    = $('#oetODSSelectSectionType').val();
    var dOrderDate      = JStODSConvertFormatDate($('#oetODSOrderDate').val());
    var tSection        = 'ADDON';
    if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
    // console.log(aBrwDataPdt);
    JSxODSResetSortBy();//ตั้งค่าให้ Sort by เป็น Default ที่ Seq No. #Comsheet 2019 189
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSAddPdtOrder",
        data: {
            paPdt        : aBrwDataPdt[0],
            ptDocNo      : tDocNo,
            pdOrderDate  : dOrderDate
        },
        cache: false,
        timeout: 0,
        success: function(oResult){
            var aReturn = JSON.parse(oResult);
            if(aReturn['nStaQuery'] == 1){
                var nCountItem      = $('#otbTableOrderingScreen' + tSection + ' tbody tr').length;
                var nAllRow         = $('#oetODSAllRow' + tSection).val();
                var nSlotItem       = $('#oetODSRowTable' + tSection).val();
                var nAllSlot        = parseInt(nSlotItem) + 1;
                var nGoPage         = $('#oetODSPage' + tSection).val();
                var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                var nResultCountItem = parseInt(nCountItem) + 1;
                
                if(nResultCountItem == nAllSlot){
                    var nGoPage       = nPageTogo + 1;
                    JSxODSDataTable(tSection,tDocNo,nGoPage,tSectionFrom);
                }else{
                    JSxODSDataTable(tSection,tDocNo,nGoPage,tSectionFrom);
                }
                
                setTimeout(function () {
                    $('.xWTableODS' + tSection).scrollTop($('.xWTableODS' + tSection)[0].scrollHeight);
                }, 1000);
                setTimeout(function () {
                    var nCountItemLast    = $('#otbTableOrderingScreen' + tSection + ' tbody tr').length;
                    $('.xWPdtOrdLot' + tSection).eq(nCountItemLast-1).focus();
                }, 1000);
            }else if(aReturn['nStaQuery'] == 88){
                var tPdtName   = "("+aReturn['aItems']['FTPdtBarCode']+") "+aReturn['aItems']['FTPdtName'];
                var tMePdtDup  = 'มี xxx อยู่ที่กลุ่ม xxx อยู่แล้ว';
                var aMePdtDup1 = tMePdtDup.replace("xxx",tPdtName);
                // console.log(aReturn['aItems']);
                switch(aReturn['aItems']['FTPdtSecCode']){
                    case "NEW":
                        tSectionName = 'สินค้าใหม่';
                        break;
                    case "TOP1000":
                        tSectionName = 'สินค้า TOP 1000';
                        break;
                    case "OTHER":
                        tSectionName = 'สินค้าอื่นๆ';
                        break;
                    case "PROMOTION":
                        tSectionName = 'สินค้าโปรโมชั่น';
                        break;
                    default:
                        tSectionName = '';
                        break;
                }
                var aMePdtDup2 = aMePdtDup1.replace("xxx",tSectionName);
                var tDetail    = aMePdtDup2;

                var aMessage  = {
                    tHead   : 'ข้อมูลสินค้าซ้ำ',
                    tDetail : tDetail,
                    tType   : 3
                };
                JSxODSAlertMessage(aMessage);
                // $('#odvModalAlertMessage').off('hidden.bs.modal');
                // $('#odvModalAlertMessage').on('hidden.bs.modal', function () {
                $('.xWODSBtnOrderProduct').off('click');
                $('.xWODSBtnOrderProduct').on('click',function(){
                    JSxODSFindProducts(aReturn['aItems']);
                });
            }else{
                console.log(aReturn);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

async function JSxODSFindProducts(paPackData){
    var nLimitRecord    = $('#osmODSLimitRecord option:selected').val();
    var nPage           = Math.ceil(paPackData['nRowID'] / nLimitRecord);
    $('.nav-tabs a[href="#odvODSContent' + paPackData['FTPdtSecCode'] + '"]').tab('show');
    // console.log(paPackData['FTPdtSecCode']);
    if(paPackData['FTPdtSecCode'] == 'ADDON'){
        $tFromSec = 'SUMMARY';
    }else{
        $tFromSec = '';
    }
    JSxODSDataTable(paPackData['FTPdtSecCode'],paPackData['FTXohDocNo'],nPage,$tFromSec,paPackData['FNXdtSeqNo']);
}

function JSxODSAddPdtManual(aAlertMessage){
    var tSearchPdt    = $('#oetODSAddPdt').val();
    var tDocNo        = $('#oetODSDocNo').val();
    var dOrderDate    = JStODSConvertFormatDate($('#oetODSOrderDate').val());
    var tSectionFrom  = $('#oetODSSelectSectionType').val();
    var tSection      = 'ADDON';

    JSxODSResetSortBy();//ตั้งค่าให้ Sort by เป็น Default ที่ Seq No. #Comsheet 189
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSAddPdtManual",
        data: {
            ptDocNo      : tDocNo,
            ptSearchPdt  : tSearchPdt,
            pdOrderDate  : dOrderDate
        },
        cache: false,
        timeout: 0,
        success: function(oResult){
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
            if(aReturn['nStaQuery'] == 1){
                var nCountItem      = $('#otbTableOrderingScreen' + tSection + ' tbody tr').length;
                var nAllRow         = $('#oetODSAllRow' + tSection).val();
                var nSlotItem       = $('#oetODSRowTable' + tSection).val();
                var nAllSlot        = parseInt(nSlotItem) + 1;
                var nGoPage         = $('#oetODSPage' + tSection).val();
                var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                var nResultCountItem = parseInt(nCountItem) + 1;
    
                if(nResultCountItem == nAllSlot){
                    var nGoPage       = nPageTogo + 1;
                    JSxODSDataTable(tSection,tDocNo,nGoPage,tSectionFrom);
                }else{
                    JSxODSDataTable(tSection,tDocNo,nGoPage,tSectionFrom);
                }
    
                setTimeout(function () {
                    $('.xWTableODS' + tSection).scrollTop($('.xWTableODS' + tSection)[0].scrollHeight);
                }, 1000);
            }else if(aReturn['nStaQuery'] == 88){
                var tPdtName     = "("+aReturn['aItems']['FTPdtBarCode']+") "+aReturn['aItems']['FTPdtName'];
                var tMePdtDup    = aAlertMessage['tPdtDup']['tDetail'];
                var aMePdtDup1   = tMePdtDup.replace("xxx",tPdtName);

                // console.log(aReturn['aItems']);
                switch(aReturn['aItems']['FTPdtSecCode']){
                    case "NEW":
                        tSectionName = 'สินค้าใหม่';
                        break;
                    case "TOP1000":
                        tSectionName = 'สินค้า TOP 1000';
                        break;
                    case "OTHER":
                        tSectionName = 'สินค้าอื่นๆ';
                        break;
                    case "PROMOTION":
                        tSectionName = 'สินค้าโปรโมชั่น';
                        break;
                    default:
                        tSectionName = '';
                        break;
                }
                var aMePdtDup2 = aMePdtDup1.replace("xxx",tSectionName);
                var tDetail    = aMePdtDup2;

                var aMessage  = {
                    tHead   : aAlertMessage['tPdtDup']['tHead'],
                    tDetail : tDetail,
                    tType   : 3
                };
                JSxODSAlertMessage(aMessage);
                // $('#odvModalAlertMessage').off('hidden.bs.modal');
                // $('#odvModalAlertMessage').on('hidden.bs.modal', function () {
                $('.xWODSBtnOrderProduct').off('click');
                $('.xWODSBtnOrderProduct').on('click',function(){
                    JSxODSFindProducts(aReturn['aItems']);
                });
            }else{
                JSxODSAlertMessage(aAlertMessage['tNotFoundPdt']);
                $('#odvModalAlertMessage').off('hidden.bs.modal');
                $('#odvModalAlertMessage').on('hidden.bs.modal', function () {
                    $('#oetODSAddPdt').focus();
                    $('#oetODSAddPdt').select();
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

//ล้างค่า Sort By ให้เป็นค่าเริ่มต้น
function JSxODSResetSortBy(){
    $('#ohdNameSort').val('');
    $('#ohdTypeSort').val('ASC');
}

function JSxODSAlertMessage(aDataMesage){
    JSxContentLoader('hide');
    $('#odvModalAlertMessage').modal('show');
    $('.xWODSModalAlertMessageHead').html(aDataMesage['tHead']);
    $('.xWODSModalAlertMessageBody').html(aDataMesage['tDetail']);
    $('.xWODSBtnOrderProduct').hide();

    // tType 1 = Alert Confirm
    // tType 2 = Message Alert
    switch(aDataMesage['tType']){
        case 1:
            $('.xWODSConfirmAlertMessage').show();
            $('#odvModalAlertMessage').off('keyup');
            $('#odvModalAlertMessage').on('keyup', function(e){
                if(e.keyCode == 13){
                    $('.xWODSConfirmAlertMessage').click();
                }
            });
            break;
        case 2:
            $('.xWODSConfirmAlertMessage').hide();
            $('#odvModalAlertMessage').off('keyup');
            $('#odvModalAlertMessage').on('keyup', function(e){
                if (e.keyCode == 13) {
                    $('.xWODSCloseAlertMessage').click();
                }
            });
            break;
        case 3:
            $('.xWODSBtnOrderProduct').show();
            $('.xWODSConfirmAlertMessage').hide();
            $('#odvModalAlertMessage').off('keyup');
            $('#odvModalAlertMessage').on('keyup', function(e){
                if (e.keyCode == 13) {
                    $('.xWODSBtnOrderProduct').click();
                }
            });
            break;
    }
}

function JStODSConvertFormatDate(dDate){
    var dDate       = dDate;
    var dDateFormat = dDate.split("/");
    return dDateFormat[2]+"-"+dDateFormat[1]+"-"+dDateFormat[0];
}

// function JSxODSUpdOrdLotAndOrdPcsToNull(){
//     var tDocNo = $('#oetODSDocNo').val();
//     var tSec   = $('#oetODSSelectSectionType').val();
//     $.ajax({
//         type: "POST",
//         url: "Content.php?route=omnOrderingScreen&func_method=FSxCODSUpdOrdLotAndOrdPcsToNull",
//         data: {
//             ptDocNo      : tDocNo
//         },
//         cache: false,
//         timeout: 0,
//         success: function(){
//             // var aReturn = JSON.parse(oResult);
//             // if(aReturn['nStaQuery'] == 1){
//                 $('#oetODSCheckPOFlag').val(0);
//                 $('#obtODSSave').hide();
//                 JSxODSDataTable(tSec,tDocNo,1);
//             // }
//         },
//         error: function(jqXHR, textStatus, errorThrown) {
//             console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
//         }
//     });
// }