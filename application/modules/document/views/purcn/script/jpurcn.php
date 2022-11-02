<script>    

    //กดปุ่ม Save
    function JSxBTNPURSave(ptRoute){
        //รหัสซับพลายเออร์
        var tSplCode = $('#ohdPnSupCode').val();

        //รหัสประเภทซับพลายเออร์
        var tStyCode = $('#ohdPnTypeSupCode').val();

        //เลขที่เอกสาร auto gen
        var tDocumentNumber = $('#ospDocumentnoValue').text();

        //เหตุผล
        var tReason = $('#oetPURSupplierReason').val();

        //ใบรับของ / ใบซื้อ
        var tNumPO = $('#oetPURSUPCode').val();

        //เอกสารที่ส่ง
        var tNumberSend = $('#oetPURDocNumberSendCode').val();

        //วันที่ส่ง
        var tDateSend = $('#oetPURDocDateSend').val();

        //เลขที่เอกสาร EDI
        var tDocNumber = $('oetPURDocnumber').val();

        //วันที่เอกสาร EDI
        var tDocDate = $('#oetPURDocDate').val();

        //เวลาเอกสาร EDI
        var tDocTime = $('#oetPURDocTime').val();

        //วันที่ส่งคืนเอกสาร EDI
        var tDocDateReturn = $('#oetPURDocReturnDate').val();

        //ผลรวมเป็นคำพูด
        var tTextCalculate = $('#oetPURCalculateText').val();

        //รวมจำนวนเงิน
        var nCalResult = $('#oetPURCalResult').val();
        
        //ส่วนลด
        var nCalDiscount = $('#oetPURCalDiscount').val();

        //ส่วนลดข้อความ
        var tTextCalDiscount = $('#oetPURCalTextDiscount').val();
        
        //จำนวนเงินหลัง ลด/มัดจำ
        var nCalBeforeDiscount = $('#oetPURCalBeforeDiscount').val();
        
        //ภาษีมูลค่าเพิ่ม
        var nCalVat = $('#oetPURCalVat').val();

        //จำนวนเงินรวมทั้งสิ้น
        var nCalNet = $('#oetPURCalNet').val();

        //Vat Value
        var nVatValue = $('#ohdHiddenVat').val();

        //VatCode
        var tVatCode = $('#ohdHiddenTypeVat').val();

        //หมายเหตุล่างสุด
        var tReasonTextArea  = $('#otaReason').val();

        //ประเภท PUR1 : ตามรอบ PUR2 : ตามสาขา
        var tTypeRoundBranch = $('#ohdPtRoundBranch').val();

        if(tReason == '' || tReason == null){
            $('#odvModalReasonNull').modal('show');
            $('#osmModalReasonNull').click(function() {
                $('#oetPURSupplierReasonName').focus();
            });
            var bPrepareSave = false;
        }else{
            var bPrepareSave = true;
        }

        if(bPrepareSave == true){
            $('#odvModalTextBeforeSave').modal('show');
            $('.xCNConfirmBeforeSavePur').click(function() {
                $.ajax({
                    url     : ptRoute,
                    data    : { 
                        'tSplCode'              : tSplCode,
                        'tStyCode'              : tStyCode,
                        'tDocumentNumber'       : tDocumentNumber,
                        'tReason'               : tReason,
                        'tNumPO'                : tNumPO,
                        'tNumberSend'           : tNumberSend,
                        'tDateSend'             : tDateSend,
                        'tDocNumber' 	        : tDocNumber,
                        'tDocDate'	            : tDocDate,
                        'tDocTime'	            : tDocTime,
                        'tDocDateReturn'        : tDocDateReturn,
                        'tTextCalculate'        : tTextCalculate,
                        'nCalResult'            : nCalResult,
                        'nCalDiscount'          : nCalDiscount,
                        'tTextCalDiscount'      : tTextCalDiscount,
                        'nCalBeforeDiscount'    : nCalBeforeDiscount,
                        'nCalVat'               : nCalVat,
                        'nCalNet'               : nCalNet,
                        'tVatCode'              : tVatCode,
                        'nVatValue'             : nVatValue,
                        'tReasonTextArea'       : tReasonTextArea,
                        'tTypeRoundBranch'      : tTypeRoundBranch,
                        'dDocDate'              : $.trim($('.ospDocumentdateValue').text())
                    },
                    type    : 'POST',
                    success : function(result){
                        //console.log(result);
                        $('#odvModalTextBeforeSave').modal('hide');
                        JSxPURAfterSave();
                    }
                });
            });
        }
    }

    //หลังกดปุ่มบันทึก
    function JSxPURAfterSave(){
        $('#obtCancel').show();
        $('#obtApprove').show();
        $('#obtReport').show();
        $('#obtSave').addClass('xCNBTNActionSaveDisable');
    }

    //กดปุ่มยกเลิก
    var tRountCancelDoc = '<?=$tROUTE_omnPurCNNew_cancelDoc?>';
    function JSxBTNPURCancel(){
        $('#odvModalWaringCancel').modal('show');

        $('.xCNCalcelDocument').click(function() {
            $('#odvModalWaringCancel').modal('hide');
            JSxContentLoader('show');
            $.ajax({
                url     : tRountCancelDoc,
                data    : { 
                    'tDocumentNumber' : $('#ospDocumentnoValue').text()
                },
                type    : 'POST',
                success : function(result){
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            });
        });
    }

    //ลบสินค้าใน ตารางฝั่งขวา
    function JSvPURDelete(paPackdata){
        var aResultdata = JSON.stringify(paPackdata);
        var aResultdata = JSON.parse(aResultdata);
        $('#odvModalDelete').modal('show');
        $('#ospConfirmDeleteValue').text(aResultdata.FTPdtCode + ' (' + aResultdata.FTPdtName + ') ');

        $('#osmConfirmSingle').unbind().click(function(evt) {
            var nPageCurrent = aResultdata.nPageCurrent;
            $.ajax({
                url     : aResultdata.tRouteDelete,
                data    : { 
                    ptDocumentNo    : aResultdata.FTXthDocNo,
                    pnSeq           : aResultdata.FNXtdSeqNo,
                    pnProductcode   : aResultdata.FTPdtCode,
                    pnBchCode       : aResultdata.FTBchCode
                },
                type    : 'POST',
                success : function(result){
                    $('#odvModalDelete').modal('hide');
                    setTimeout(function(){ 
                        $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                        JSvCalculateTotal();
                        JSxSelectDataintoTablePUR(1);
                    }, 500);
                }
            });
        });
    }

    //Edit inline
    tCheckEventClick    = 1;

    //Edit inline PDT
    $('.xWPurreqPDTCode').click(function() { 
        poElement = this;
        if (poElement.getAttribute("data-dblclick") == null) {
            poElement.setAttribute("data-dblclick", 1);
            $(poElement).select();
            setTimeout(function () {
                if (poElement.getAttribute("data-dblclick") == 1) {
                    var tEvent = 'Click';
                    JSxEditInlineByEvent(poElement,tEvent);
                }
                poElement.removeAttribute("data-dblclick");
            }, 300);
        } else {
            poElement.removeAttribute("data-dblclick");
            var tEvent = 'Doubleclick';
            JSxEditInlineByEvent(poElement,tEvent);
        }
    });

    //Browse Edit inline
    function JSxEditInlineByEvent(poElement,tEvent){
        if(tEvent == 'Doubleclick'){
            var nSeq    = $(poElement).parents('#otbTablePURProduct tbody tr').data('seq');
            var tDocno  = $(poElement).parents('#otbTablePURProduct tbody tr').data('docno');
            oCrdBrwCardType.CallBack.ReturnType = 'S';
            oCrdBrwCardType.NextFunc.FuncName   = 'JSxEditChangePDT';
            oCrdBrwCardType.NextFunc.ArgReturn  = [nSeq];
            JCNxBrowseData('oCrdBrwCardType');
        }else if(tEvent == 'Click'){
            //edit inline
        }
    }

    //Change value in INPUT Data
    function JSxEditChangePDT(elem,nSeq){
        var tDocumentID         = $('#ospDocumentnoValue').text();
        var nSeqItem            = nSeq;
        var tNameRouteInsertPDT = '<?=$tROUTE_omnPurCNNew_insertpdt?>';
        $('#obtSave').removeClass('xCNBTNActionSaveDisable');
        var aData = JSON.parse(elem);
        $.ajax({
            url     : tNameRouteInsertPDT,
            data    : { 
                tParamter       : aData ,
                tDocumentID     : tDocumentID,
                nSeq            : nSeqItem,
                dDocDate        : $.trim($('.ospDocumentdateValue').text()),
                tTypeVat        : $('#ohdHiddenTypeVat').val(),
                nValueVat       : $('#ohdHiddenVat').val(),
                tSPLCode        : $('#ohdPnSupCode').val(),
                tTypeSPL        : $('#ohdPtRoundBranch').val()
            },
            type    : 'POST',
            success : function(oResult){
                var oResult = JSON.parse(oResult);
                if(oResult.tResult == 'success'){
                    JSxSelectDataintoTablePUR(1);
                }else{
                    alert('error');
                }
            }
        });
    }

    //Edit inline รหัสสินค้า
    $('.inputs').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode === 13){
            var nSeq = $(this).parents('tr').data('seq');
            var tDoc = $(this).parents('tr').data('docno');
            JSUpdateEditinline(nSeq,tDoc);
        }
    });

    $('.inputs').on("focusout",function(e){
        var nSeq = $(this).parents('tr').data('seq');
        var tDoc = $(this).parents('tr').data('docno');
        JSUpdateEditinline(nSeq,tDoc);
        e.preventDefault();

    });

    //Edit inline ช่องจำนวน
    $('.inputsChange').keydown(function(e) {
        var nSta = sessionStorage.getItem("nStaEditInLine");
        if(nSta != 1){
            var keyCode = e.keyCode || e.which; 
            if(keyCode === 13){
                var nSeq = $(this).parents('tr').data('seq');
                var tDoc = $(this).parents('tr').data('docno');
                var tPdt = $(this).parents('tr').data('pdt');
                JSUpdateEditinline(nSeq,tDoc,tPdt);
                console.log('keydown');
            }
        }
    });

    $('.inputsChange').focusout(function(e) {
        var nSta = sessionStorage.getItem("nStaEditInLine");
        if(nSta != 1){
            var nSeq    = $(this).parents('tr').data('seq');
            var tDoc    = $(this).parents('tr').data('docno');
            var tPdt    = $(this).parents('tr').data('pdt');
            var tOldqty = $(this).parents('tr').data('oldqty');
            var tValue  = $(this).val();
            if(tOldqty != tValue){
                if(tCheckEventClick == 1){
                    console.log('focusout');
                    JSUpdateEditinline(nSeq,tDoc,tPdt);
                    return false;
                }
            }
            e.preventDefault();
        }
    });

    $('.inputsChange').on("change", function(e){
        var nSta = sessionStorage.getItem("nStaEditInLine");
        if(nSta != 1){
            var nSeq = $(this).parents('tr').data('seq');
            var tDoc = $(this).parents('tr').data('docno');
            var tPdt = $(this).parents('tr').data('pdt');
            if(tCheckEventClick == 1){
                JSUpdateEditinline(nSeq,tDoc,tPdt);
                console.log('change');
                return false;
            }
            e.preventDefault();
        }
    });

    $('.inputsChange').on( "click", function(e){
        var nSta = sessionStorage.getItem("nStaEditInLine");
        if(nSta != 1){
            var nSeq = $(this).parents('tr').data('seq');
            $('#oetFieldFCXtdQty'+nSeq).focus();
            $('#oetFieldFCXtdQty'+nSeq).select();
        }
    });

    //Edit inline
    var tRountEditinline = '<?=$tROUTE_omnPurCNNew_editinline?>';
    function JSUpdateEditinline(nSeq,tDoc,ptPdt){
        var nValue      = $('#oetFieldFCXtdQty'+nSeq).val();
        var nB4DisChg   = nValue * $('#oetFieldSetPrice'+nSeq).text(); 
        sessionStorage.setItem("nStaEditInLine", 1);
        $.ajax({
            type    : "POST",
            url     : tRountEditinline,
            data    : {
                nSeq        : nSeq,
                tDoc        : tDoc,
                nValue      : nValue,
                nB4DisChg   : nB4DisChg,
                nVatRate    : $('#ohdHiddenVat').val(),
                tPdt        : ptPdt
            },
            cache: false,
            success: function(tResult) {
                if( tResult == 'success'){
                    var nGoPage         = $('#ohdPageGo').val();
                    JSxSelectDataintoTablePUR(nGoPage);
                    tCheckEventClick = 1;
                    JSvCalculateTotal();
                }else if( tResult == 'PdtQtyRet' ){
                    $('#odvModalChkPdtQtyRet').modal('show');
                    $('.xCNTextChkPdtQtyRet').html('เกินกว่าจำนวนสินค้าที่มีในสต็อก');
                    $("#odvModalChkPdtQtyRet").on('hidden.bs.modal', function(){
                        $('#oetFieldFCXtdQty'+nSeq).focus();
                        $('#oetFieldFCXtdQty'+nSeq).select();
                    });
                }else if(tResult == 'LESSQTY'){
                    if(nValue != 0){
                        $('#odvModalChkPdtQtyRet').modal('show');
                        $('.xCNTextChkPdtQtyRet').html('เกินกว่าจำนวนสินค้าที่มีในสต็อก');
                    }

                    $('#oetFieldFCXtdQty'+nSeq).val(0);
                }else{
                    alert('error');
                    console.log(tResult);
                }
                setTimeout(function(){ 
                    sessionStorage.setItem("nStaEditInLine", 0);
                }, 100);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    //Next Page สินค้า
    function JSvClickPageListPDT(ptPage){
		var nPageCurrent = '';
		var nPageNew;
		switch (ptPage) {
			case 'next': //กดปุ่ม Next
				$('.xWBtnNext').addClass('disabled');
				nPageOld = $('.xWPageListPDT .active').text(); // Get เลขก่อนหน้า
				nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
				nPageCurrent = nPageNew;
				break;
			case 'previous': //กดปุ่ม Previous
				nPageOld = $('.xWPageListPDT .active').text(); // Get เลขก่อนหน้า
				nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
				nPageCurrent = nPageNew;
				break;
			default:
				nPageCurrent = ptPage;
		}
		JSxSelectDataintoTablePUR(nPageCurrent);
	}

    //คำนวณส่วนลด
    var tRountCalculate = '<?=$tROUTE_omnPurCNNew_calculate?>';
    function JSvCalculateTotal(){
        var tFormatCode = $('#ospDocumentnoValue').text();
        if(tFormatCode == 'PCBCHYY-#######'){
            var tDocumentID = null;
        }else{
            var tDocumentID = "'"+tFormatCode+"'";
        }

        if(tDocumentID != null){

            //ลบปุ่มเพิ่มส่วนลดท้ายบิล
            $('.xCNClickDiscount').prop('disabled',false);
        
            $.ajax({
                type    : "POST",
                url     : tRountCalculate,
                data    : {
                    tDocumentID  : tDocumentID
                },
                cache: false,
                success: function(tResult) {
                    var oResult = JSON.parse(tResult);
                    if(oResult.nTotal == 0.00 || oResult.nTotal == null || oResult.nTotal == 'null'){
                        var nResultTotal = 0.00;
                    }else{
                        var nResultTotal = oResult.nTotal;
                    }

                    //รวมจำนวนเงิน
                    $('#oetPURCalResult').val(formatNumber(nResultTotal));

                    //ส่วนลด
                    var tTextDiscount = $('#oetPURCalDiscount').val();
                    if(tTextDiscount == '' || tTextDiscount == null || tTextDiscount == 0){
                        $('#oetPURCalDiscount').val(formatNumber(0.00));
                    }

                    //ส่วนลด
                    $('#oetPURCalBeforeDiscount').val(formatNumber(nResultTotal));

                    //จำนวนเงินหลัง ลด/มัดจำ
                    var nTotal      = $('#oetPURCalResult').val();
                    var nTotal      = nTotal.replace(",","");
                    var nDsicount   = $('#oetPURCalDiscount').val();
                    var nDsicount   = nDsicount.replace(",","");
                    var nAfterCal   = nTotal - nDsicount;
                    $('#oetPURCalBeforeDiscount').val(formatNumber(nAfterCal));
                    var nBeforeDiscount = $('#oetPURCalBeforeDiscount').val();
                    var nBeforeDiscount = nBeforeDiscount.replace(",","");

                    //ภาษีมูลค่าเพิ่ม
                    if($('#ohdHiddenTypeVatSPL').val() == 1){ //แยกนอก
                        var nVat        = $('#ohdHiddenVat').val();
                        var nResultVat  = nBeforeDiscount - (nBeforeDiscount * 100) / (100 + parseInt(nVat));
                        
                        //จำนวนเงินรวมทั้งสิ้น
                        var nNet = parseFloat(nBeforeDiscount);
                    }else if($('#ohdHiddenTypeVatSPL').val() == 2){ //รวมใน
                        var nVat        = $('#ohdHiddenVat').val();
                        var nResultVat  = nBeforeDiscount * (nVat/100);

                        //จำนวนเงินรวมทั้งสิ้น
                        var nNet = parseFloat(nBeforeDiscount) + parseFloat(nResultVat);
                    }
                    $('#oetPURCalVat').val(formatNumber(nResultVat));

                    //จำนวนเงินรวมทั้งสิ้น
                    $('#oetPURCalNet').val(formatNumber(nNet));

                    //ข้อความจำนวนเงิน
                    var tThaibath = ArabicNumberToText(formatNumber(nNet)); 
                    $('#oetPURCalculateText').val(tThaibath);

                    //ถ้ารวมจำนวนเงินเป็น 0 จะไม่อนุญาติไม่ลด
                    var nValueDisForBlock = $('#oetPURCalResult').val();
                    if(nValueDisForBlock == 0.00 || nValueDisForBlock == '0.00'){
                        $('.xCNClickDiscount').prop('disabled',true);
                        $('#oetPURCalTextDiscount').val('');
                        $('#oetPURCalDiscount').val('0.00');
                        $('#otbTableDiscountCharge tbody tr').remove();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('error');
                }
            });
        }

    }

    //Calculate Total คำนวณส่วนลด
    function formatNumber(num) {
        if(num == null || num == 'null'){
            return num;
        }else{
            return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    }

    //Approve อนุมัติ
    function JSxBTNPURApprove(){
        $('#odvModalApprove').modal('show');
	
        $('.xCNBTNActionConfirmApprove').off('click');
        $('.xCNBTNActionConfirmApprove').on( "click", function( event ) {
            $('.xCNBTNActionConfirmApprove').off('click');
            var tDocumentID      = $('#ospDocumentnoValue').text();

            var paParams = {
                'MQApprove' : 'PURCNNEW',
                'MQDelete'  : 'PURCNNEWDEL',
                'params'    : {
                    'ptRouteOther'  : $('#ohdRabbitCasePurcnFail').val(),
                    'ptDocName'		: 'PC',
                    'ptDocNo'       : tDocumentID
                },
                'tType'		: 'backgroundprocess'
            };
            SubcribeToRabbitMQ(paParams);
            $('#odvModalApprove').modal('hide');

            //Edit By Jame(06/11/2562) เนื่องจากปุ่ม osmCloseRabbit มันเบิ้ล
            // $('#osmConfirmRabbit , #osmCloseRabbit').on( "click", function( event ) {
            $('#osmConfirmRabbit').off('click');
            $('#osmConfirmRabbit').on('click',function(){
                setTimeout(function(){ 
                    JSxAfterApprove();
                }, 500);
            });

            //กดปิด
            // $('#osmCloseRabbit').on( "click", function( event ) {
            //     var paParams = {
            //         'MQApprove' : 'PURCNNEW',
            //         'MQDelete'  : 'PURCNNEWDEL',
            //         'params'    : {
            //             'ptRouteOther'  : $('#ohdRabbitCasePurcnFail').val(),
            //             'ptDocName'		: 'PC',
            //             'ptDocNo'       : tDocumentID
            //         },
            //         'tType'		: 'backgroundprocessRollBack'
            //     };
            //     SubcribeToRabbitMQ(paParams);
            // });
        });
    }

    //After Approve หลังจากอนุมัติ
    function JSxAfterApprove(){
        $('#obtSave').hide();
        $('#obtCancel').hide();
        $('#obtApprove').hide();
        JSxContentLoader('hide');
        var tRouteSelectDocument = '<?=$tROUTE_omnPurCNNew_selectafter?>';
        $.ajax({
            url     : tRouteSelectDocument,
            data    : { 
                tDocumentID     : $('#ospDocumentnoValue').text()
            },
            type    : 'POST',
            success : function(tResult){
                $('#odvContentMainPUR').html('');
                $('#odvContentMainPUR').html(tResult); 

                var tDocumentID  = $('#ospDocumentnoValue').text();
                JSxBTNPURReport('backgroundprocess')
            }
        });
    }

    //Control BTN หลังจากอนุมัติ
    function JSxControlBTNAfterApprove(ptFlagApprove,ptFlagStaPrc){
        // alert(ptFlagStaPrc);
        if(ptFlagApprove == 1 && ptFlagStaPrc == 1){
            $('#obtNew').show();
            $('#obtReport').show();
        }
    }

    //สร้างเอกสารใหม่
    function JSxBTNPURNew(){
        location.reload();
    }

    //กดปุ่มค้นหาเอกสาร
    function JSxBTNPURListSearch(ptRoute,nPageCurrent){
        if(nPageCurrent == '' || nPageCurrent == null){ nPageCurrent = 1 }
        $('#odvModalListSearch').modal('show');
        var tTextSearchPUR = $('#oetSearchPUR').val();

        $.ajax({
            url     : ptRoute,
            data    : { 
                tTextSearchPUR  : tTextSearchPUR,
                ptRoute         : ptRoute,
                nPageCurrent    : nPageCurrent
            },
            type    : 'POST',
            success : function(oResult){
                $('#odvContentListSearch').html(oResult);               
            }
        });
    }

    //Next page  List search ค้นหาเอกสาร
    function JSvClickPageList(ptNameroute,ptPage){
        var nPageCurrent = '';
        var nPageNew;
        switch (ptPage) {
            case 'next': //กดปุ่ม Next
                $('.xCNBTNNextprevious').addClass('disabled');
                nPageOld = $('.xWPageListSearch .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
                nPageCurrent = nPageNew;
                break;
            case 'previous': //กดปุ่ม Previous
                nPageOld = $('.xWPageListSearch .active').text(); // Get เลขก่อนหน้า
                nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSxBTNPURListSearch(ptNameroute,nPageCurrent);
    }

    //เลือกเอกสารใน ในกล่องค้นหา
    var pnID;
    function JSxSelectDocument(elem,pnID){
        if (elem.getAttribute("data-dblclick") == null) {
            elem.setAttribute("data-dblclick", 1);
            setTimeout(function () {
                if (elem.getAttribute("data-dblclick") == 1) {
                    var tEvent = 'Click';
                    $('#otbTableListSearch tbody tr').removeClass('xCNActiveRecord');
                    $(elem).addClass('xCNActiveRecord');
                    $('#ohdDocumentSearch').val(pnID);
                }
                elem.removeAttribute("data-dblclick");
            }, 300);
        } else {
            elem.removeAttribute("data-dblclick");
            var tEvent = 'Doubleclick';
            $('#ohdDocumentSearch').val(pnID);
            JSxSelectListSearch();
        }
    }

    //เลือกเอกสาร ในกล่องค้นหา
    $('.xCNBTNActionListSearch').click(function() {
        JSxSelectListSearch();
    });

    //เลือกเอกสาร ในหล่องค้นหา
    function JSxSelectListSearch(){
        var tDocumentID = $('#ohdDocumentSearch').val();

        var tRouteSelectDocument = '<?=$tROUTE_omnPurCNNew_selectafter?>';
        $.ajax({
            url     : tRouteSelectDocument,
            data    : { 
                tDocumentID     : tDocumentID
            },
            type    : 'POST',
            success : function(tResult){
                $('#odvModalListSearch').modal('hide');
                JSxContentLoader('show');

                setTimeout(function(){
                    JSxContentLoader('hide');
                    $('#odvContentMainPUR').html('');
                }, 1500); 

                setTimeout(function(){
                    $('#odvContentMainPUR').html(tResult); 
                }, 2000); 

            }
        });
    }

    //ส่งออกรายงาน ของพี่หนุ่ย
    function JSxBTNPURReport(ptType){
        //ถ้าวิ่ง backgroundprocess เสร็จเเล้วต้องไป select เอกสารมาว่า ถูก split ใบออกมากี่ใบ 
        if(ptType == 'backgroundprocess'){
            var tRouteCheckDocument = '<?=$tROUTE_omnPurCNNew_CheckDocSplit?>';
            var tCompCode           = $('#ohdHiddenComp').val();
            $.ajax({
                url     : tRouteCheckDocument,
                data    : { 
                    tDocumentID     : $('#ospDocumentnoValue').text()
                },
                type    : 'POST',
                success : function(oResult){
                   var tResult = JSON.parse(oResult);

                    $('#odvModalRabbitExportSuccess').modal('show');
                    var tTextReport = '';
                    for(i=0; i<tResult.length; i++){
                        tTextReport += '<p> เอกสารหมายเลข : ' + tResult[i].FTXthDocNo + '</p>';	
                    }
                    $('.xCNTextRabbitExportSuccess').html('<p>ส่งข้อมูลออกสำเร็จ</p>' + tTextReport);

                    $('#obtRabbitExportSuccess').off('click')
                    $('#obtRabbitExportSuccess').on('click',function(){
                        for(i=0; i<tResult.length; i++){
                            var aInfor = [
                                {"SP_nLang":'1'},                    // ภาษา
                                {"SP_tCompCode":tCompCode},              // รหัสบริษัท
                                {"SP_tDocNo":tResult[i].FTXthDocNo},  // เลขที่เอกสาร
                                {"SP_DocName":"ptReport"}               // ชื่อเอกสาร
                            ];
                            window.open("<?=$tBase_url;?>formreport/ReportFamily?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');   	
                        }
                    });

                }
            });
        }else if(ptType == 'singlereport'){
            var tDocumentID  = $('#ospDocumentnoValue').text();
            var tCompCode    = $('#ohdHiddenComp').val();

            var aInfor = [
                {"SP_nLang":'1'},                    // ภาษา
                {"SP_tCompCode":tCompCode},              // รหัสบริษัท
                {"SP_tDocNo":tDocumentID},            // เลขที่เอกสาร
                {"SP_DocName":"ptReport"}         // ชื่อเอกสาร
            ];
            window.open("<?=$tBase_url;?>formreport/ReportFamily?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
        }
       
    }


</script>