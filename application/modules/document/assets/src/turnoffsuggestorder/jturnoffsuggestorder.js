$('document').ready(function() {
    JSxSelectDataintoTable('X99');

	//Control button
	JSxControlButtonBar();

	$('#osmLimitRecord').change(function() {
		JSxSelectDataintoTable();
	});
});

//Select
function JSxSelectDataintoTable(nPageCurrent){
    var tNamerouteselect = $('#oetHiddenrouteSelect').val();
    var tDocumentID      = $('#ohdDocumentnoForSearch').val();
    var nLimitRecord     = $('#osmLimitRecord option:selected').val();
    if(nPageCurrent == '' || nPageCurrent == null){
        nPageCurrent = 1;
    }

    if(nPageCurrent == 'X99'){
        $('#ohdFlagSave').val('save');
    }

    $.ajax({
        url     : tNamerouteselect,
        data    : { 
            tSearchAll      : $('#oetSearchTSO').val(),
            nPageCurrent    : nPageCurrent,
            tDocumentID     : tDocumentID,
            nLimitRecord    : nLimitRecord,
            tSortBycolumn   : [$('#ohdNameSort').val(),$('#ohdTypeSort').val()],
        },
        type    : 'POST',
        success : function(result){
            JSxCheckSession(result);
            $('#odvContentTable').html(result);   

               
            //for search
            var nLenRecord = $('#otbTableSuggestorder tbody tr').hasClass('otrNoData');
            if(nLenRecord != true){
                $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
            }
            
        }
    });
}

//Search
function JSxSearchTSO(){
    var tDocumentID = $('#oetSearchTSO').val();
    JSxSelectDataintoTable(1);

}

//Next previous
function JSvClickPage(ptPage,ptDocumentNo) {
    var nPageCurrent = '';
    var nPageNew;
    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            $('.xWBtnNext').addClass('disabled');
            nPageOld = $('.xWPageTurnoffsuggestorder .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
            nPageCurrent = nPageNew;
            break;
        case 'previous': //กดปุ่ม Previous
            nPageOld = $('.xWPageTurnoffsuggestorder .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }

    JSxSelectDataintoTable(nPageCurrent);
    
}

//Select Master DT
function JSxMoveMasterToTemp(nPageCurrent){
    var tDocumentID = $('#ohdDocumentnoForSearch').val();
    var tNameroute  = $('#oetHiddenrouteSelectHD').val();
    if(nPageCurrent == '' || nPageCurrent == null){
        nPageCurrent = 1;
    }
    $.ajax({
        url     : tNameroute,
        data    : { 
            tDocumentID     : tDocumentID ,
            tSearchAll      : $('#oetSearchTSO').val(),
            nPageCurrent    : nPageCurrent ,
            tSortBycolumn   : [$('#ohdNameSort').val(),$('#ohdTypeSort').val()],
        },
        type    : 'POST',
        success : function(result){
            JSxCheckSession(result);
            setTimeout(function(){ 
                $('#odvContentTable').html(result);
            }, 500);

            setTimeout(function(){ 
                JSxContentLoader('hide');
            }, 1000);
        }
    });
}

//Delete
function JSvCallSuggestorderDelete(paPackdata){
    var aResultdata = JSON.stringify(paPackdata);
    var aResultdata = JSON.parse(aResultdata);
    $('#odvModalDelete').modal('show');
    $('#ospConfirmDeleteValue').text(aResultdata.nPDTCode + ' (' + aResultdata.nPDTName + ') ');

    $('#osmConfirmSingle').unbind().click(function(evt) {
        var nPageCurrent = aResultdata.nPageCurrent;
        $.ajax({
            url     : aResultdata.troutedelete,
            data    : { 
                ptDocumentNo    : aResultdata.tDocument,
                pnSeq           : aResultdata.nPDTSeq,
                pnProductcode   : aResultdata.nPDTCode 
            },
            type    : 'POST',
            success : function(result){
                $('#odvModalDelete').modal('hide');
                setTimeout(function(){ 
                    $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                    JSxSelectDataintoTable(nPageCurrent);
                    JSxCheckRecordInTable();
                }, 500);

               

            }
        });
    });
}

//ถ้าไม่มี record ไม่สามารถอนุมัตได้
function JSxCheckRecordInTable(){
    var tLength = $('#otbTableSuggestorder tbody tr').length;
    var tLength = tLength - 1;
    if(tLength == 0){
        $('.xCNBTNActionApprove').addClass('xCNBTNActionApproveDisable');
    }else{
        $('.xCNBTNActionApprove').removeClass('xCNBTNActionApproveDisable');
    }
}

//control BTN bar (hide/show)
function JSxControlButtonBar(pnEvent){

    switch(pnEvent) {
        case 'save':
            // $('.xCNBTNActionSave').hide();
            // $('.xCNBTNActionInsert').show();
            $('.xCNBTNActionApprove').show();
          break;
        case 'approve':
            $('.xCNBTNActionApprove').hide();
            $('.xCNBTNActionInsert').hide();
            $('.xCNBTNActionSave').show();
          break;
        case 'new':
            $('.xCNBTNActionApprove').hide();
            $('.xCNBTNActionInsert').hide();
            $('.xCNBTNActionSave').show();
          break;
        default:
            $('.xCNBTNActionApprove').hide();
            $('.xCNBTNActionInsert').hide();
      }

}

//BTN bar Save 
function JSxBTNSavePDT(ptNameroute){
    //Flag
    setTimeout(function(){
        
        $('#ohdFlagSave').val('save');
        $('.xCNBTNActionSave').addClass('xCNBTNActionSaveDisable');
        var tDocumentID      = $('#ospDocumentnoValue').text();

        $.ajax({
            url     : ptNameroute,
            data    : { 
                tDocumentID     : tDocumentID
            },
            type    : 'POST',
            success : function(tResult){
                if(tResult == '' || tResult == null){
    
                }else if(tResult == 'Duplicate'){
                    alert('ข้อมูลซ้ำ');
                }else{
                    //JSxControlButtonBar('save');
                    $('#ospDocumentnoValue').text(tResult);
                    $('#ohdDocumentnoForSearch').val(tResult);
                    JSxMoveMasterToTemp();
                }
            }
        });
    }, 500);
}

//BTN bar Approve
function JSxBTNApprovePDT(ptNameroute){
	$('#odvModalApprove').modal('show');
	
	$('.xCNBTNActionCancel').on( "click", function( event ) {
		$('#odvModalApprove').modal('hide');
	});
	
	$('.xCNBTNActionConfirmApprove').off('click');
    $('.xCNBTNActionConfirmApprove').on( "click", function( event ) {
        $('.xCNBTNActionConfirmApprove').off('click');
        var tDocumentID      = $('#ohdDocumentnoForSearch').val();
        //กด save สินค้าทุกครั้งก่อนอนุมัติ
        $.ajax({
            url     : 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSaveBeforeApprove',
            data    : { 
                tDocumentID     : tDocumentID
            },
            type    : 'POST',
            success : function(tResult){

                $.ajax({
                    url     : ptNameroute,
                    data    : { 
                        tDocumentID     : tDocumentID
                    },
                    type    : 'POST',
                    success : function(tResult){
                        JSxControlButtonBar('approve');  
                        var ohdDocumentno = $('#ohdDocumentnoForSearch').val();
                        console.log('รหัสเอกสาร : ' + ohdDocumentno);
                        var paParams = {
                            'MQApprove' : 'SUGORD',
                            'MQDelete'  : 'SUGORDDEL',
                            'params'    : {
                                'ptDocNo'       : $('#ohdDocumentnoForSearch').val(),
                                'ptRouteOther'  : $('#ohdRabbitSuggestUpdateApprove').val()
                            },
                        };
                        SubcribeToRabbitMQ(paParams);
                        // FSxCMNSetMsgInfoMessageDialog(0);
                        $('#odvModalApprove').modal('hide');
                        
                        //Edit By Jame(06/11/2562) เนื่องจากปุ่ม osmCloseRabbit มันเบิ้ล
                        $("#odvModalMQInfoMessage").on('hide.bs.modal', function(){
                            setTimeout(function(){ 
                                $('#oetHiddenrouteSelect').val();
                                $('#ohdDocumentnoForSearch').val();
                                //Remove sort
                                JSxRemoveValueSortByColumn();
                                JSxMoveMasterToTemp();
                                $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                            }, 500);
                        });
                        // $('#osmConfirmRabbit , #osmCloseRabbit').on( "click", function( event ) {
                        //     setTimeout(function(){ 
                        //         $('#oetHiddenrouteSelect').val();
                        //         $('#ohdDocumentnoForSearch').val();
                        //         //Remove sort
                        //         JSxRemoveValueSortByColumn();
                        //         JSxMoveMasterToTemp();
                        //         $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                        //     }, 500);
                        // });
                    }
                });

            }
        });

    });
}

//BTN bar New form
function JSxBTNNewPDT(ptNameroute , ptType){
    //ptType : cancel
    //ptType : new
    var tStatus = '';
    if(ptType == 'cancel'){
        $('#odvModalListCancle').modal('show');
        $('.xCNBTNActionDocumentCancle').unbind().click(function(evt) {
            $('#odvModalListCancle').modal('hide');
            tStatus = 'pass';
            JSxBTNNewForm(ptNameroute,ptType,tStatus);
            $('#ohdFlagSave').val('unsave');
        });
    }else{
        tStatus = 'pass';
        JSxBTNNewForm(ptNameroute,ptType,tStatus);
        $('#ohdFlagSave').val('unsave');
    }
}

//Function New Form
function JSxBTNNewForm(ptNameroute,ptType,tStatus){
    //Remove sort
    JSxRemoveValueSortByColumn();
    var ohdDocumentno = $('#ohdDocumentnoForSearch').val();
    if(tStatus == 'pass'){
        $.ajax({
            url     : ptNameroute,
            data    : { 'type' : ptType , 'Docno' : ohdDocumentno },
            type    : 'POST',
            success : function(result){
                setTimeout(function(){ 
                    if(ptType == 'new'){
                        $('#ospDocumentnoValue').text('SGBCHYYMM-######');
                        $('#ohdDocumentnoForSearch').val('');
                        $('.xCNBTNActionApprove').hide();
                        JSxSelectDataintoTable();
                        $('.xCNSuggestorderCancel').hide(); 
                    }else{
                        $('#ospDocumentnoValue').text('SGBCHYYMM-######');
                        $('#ohdDocumentnoForSearch').val('');
                        $('.xCNBTNActionApprove').hide();
                        JSxSelectDataintoTable();
                        $('.xCNSuggestorderCancel').hide(); 
                        $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                    }
                }, 500);
            }
        });
    }
}

//Close browser
function JSxClosebrowser(ptNameroute , ptType){
    $.ajax({
        url     : ptNameroute,
        data    : { 'type' : ptType ,  'nStaprcDoc' : $('#ohdStaprcDoc').val() },
        type    : 'POST',
        success : function(result){
            if(result == 'Found'){
                $('#odvModalCloseBrowser').modal('show');
                $('.xCNBTNClosebrowser').bind( "click", function( event ) {
                    /*$.ajax({
                        url     : ptNameroute,
                        data    : { 
                            'type'  : 'CloseWhenconfirm'
                        },
                        type    : 'POST',
                        success : function(tResult){*/
                            window.location.href = 'http://closekiosk';
                        /*}
                    });*/
                });
            }else{
                window.location.href = 'http://closekiosk';
            }
        }
    });
}

//List Search
function JSxBTNListSearch(ptNameroute,nPageCurrent,ptTypeCheckModal){

    var tFlagSave = $('#ohdFlagSave').val();
    if(tFlagSave == 'save'){
        if(nPageCurrent == '' || nPageCurrent == null){ nPageCurrent = 1 }
        $.ajax({
            url     : ptNameroute,
            data    : { 
                ptNameroute     : ptNameroute,
                nPageCurrent    : nPageCurrent,
                tType           : 'Confirm'
            },
            type    : 'POST',
            success : function(tResult){
                $('#odvModalNewform').modal('hide');
                $('#odvModalListSearch').modal('show');
                $('#odvModalBodyListSearch').html(tResult);
            }
        });
    }else{
        if(nPageCurrent == '' || nPageCurrent == null){ nPageCurrent = 1 }
        $.ajax({
            url     : ptNameroute,
            data    : { 
                ptNameroute     : ptNameroute,
                nPageCurrent    : nPageCurrent,
                tType           : 'Main',
                nStaprcDoc      : $('#ohdStaprcDoc').val(),
                ptTypeCheckModal : ptTypeCheckModal
            },
            type    : 'POST',
            success : function(tResult){
                if(tResult == 'Found'){
                    $('#odvModalNewform').modal('show');
                    $('.xCNBTNDeleteTempNewFrom').bind( "click", function( event ) {
                        $.ajax({
                            url     : ptNameroute,
                            data    : { 
                                ptNameroute     : ptNameroute,
                                nPageCurrent    : nPageCurrent,
                                tType           : 'Confirm'
                            },
                            type    : 'POST',
                            success : function(tResult){
                                $('#odvModalNewform').modal('hide');
                                setTimeout(function(){ 
                                    $('#odvModalListSearch').modal('show');
                                    $('#odvModalBodyListSearch').html(tResult);
                                }, 800);
                                
                            }
                        });
                    });
                }else{
                    $('#odvModalListSearch').modal('show');
                    $('#odvModalBodyListSearch').html(tResult);
                }
            }
        });
    }
}

//Next page  List search
function JSvClickPageList(ptNameroute,ptPage){
    var nPageCurrent = '';
    var nPageNew;
    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            $('.xWBtnNext').addClass('disabled');
            nPageOld = $('.xWPageListDataSearch .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
            nPageCurrent = nPageNew;
            break;
        case 'previous': //กดปุ่ม Previous
            nPageOld = $('.xWPageListDataSearch .active').text(); // Get เลขก่อนหน้า
            nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }
    JSxBTNListSearch(ptNameroute,nPageCurrent,'false');
}

//Choose by document
var pnID;
function JSxSelectDocument(elem,pnID){
    if (elem.getAttribute("data-dblclick") == null) {
        elem.setAttribute("data-dblclick", 1);
        setTimeout(function () {
            if (elem.getAttribute("data-dblclick") == 1) {
                var tEvent = 'Click';
                //alert(tEvent);

                $('#otbTableSuggestHD tbody tr').removeClass('xCNActiveRecord');
                $(elem).addClass('xCNActiveRecord');
                $('#ohdDocumentnoForSearch').val(pnID);
                pnID = '';

            }
            elem.removeAttribute("data-dblclick");
        }, 300);
    } else {
        elem.removeAttribute("data-dblclick");
        var tEvent = 'Doubleclick';
        // alert(tEvent);

        $('#ohdDocumentnoForSearch').val(pnID);
        $('.xCNBTNActionListSearch').click();

    }



}
