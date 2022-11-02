<script>

    //Browse ใบขอคืนสินค้า
    $('#obtBrowsePURNumberSend').click(function(){
        var tRouteGetdocument = '<?=$tROUTE_omnPurCNNew_getdocument?>';
  
		$.ajax({
			url     : tRouteGetdocument,
			data 	: { 
                'tTypeRoundorBranch'    : $('#ohdPtRoundBranch').val(), 
                'tTypeSpl'              : $('#ohdPnTypeSupCode').val(),
                'tSearch'               : '',
                'tColumSearch'          : ''
            },
			type    : 'POST',
			success : function(oResult){
                var oResult = JSON.parse(oResult);
                var tHTMLBody = '';
                if(oResult == false){
                    $('#odvModalDataNotFoundinDatabase').modal('show');
                }else{
                    $('.xCNBTNActionConfirm').attr('disabled',false);
                    $('#odvModalDetailPn').modal('show');
                    $('#otbTablePn tbody').html('');
                    for(i=0; i<oResult.length; i++){
                        tHTMLBody += '<tr style="cursor:pointer;" onclick='+'JSxClickDetailShowProductPu("'+oResult[i].FTXrhDocNo.trim()+'",this,"'+oResult[i].FTSplCode+'") '+'>';
                        tHTMLBody += '<td>' + oResult[i].FTXrhDocNo + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FDXrhDocDate + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplName + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTStyName + '</td>';
                        // tHTMLBody += '<td>' + oResult[i].FDXrhBchReturn + '</td>';
                        tHTMLBody += '</tr>';

                        if(i==0){
                            //curror css
                            JSxClickDetailShowProductPu(oResult[i].FTXrhDocNo.trim(),this,oResult[i].FTSplCode);
                        }
                    }
                    $('#otbTablePn tbody').append(tHTMLBody);
                    $('#otbTablePn tbody tr:first-child td').addClass('PurtrRoundBranchClick');
                }
			}
		});

        var tHasClass = $('#otbTablePuPDT tbody tr').hasClass('otrNoData');
        if(tHasClass == true){
            $('.table-scroll').attr('style','height : 100px !important;');
        }else{
            $('.table-scroll').attr('style','height : 200px !important;');
        }
    });

    //napat 22/01/2563
    function JSxChangeColumSearch(ptField){
        $('#oetPURCNColumSearch').val(ptField);
        var tLebel = '';
        switch(ptField){
            case 'FTXrhDocNo':
                tLebel = "<?=language('document/purcn', 'tPUNModalPONumDoc')?>";
                break;
            case 'FDXrhDocDate':
                tLebel = "<?=language('document/purcn', 'tPUNModalPODate')?>";
                break;
            case 'FTSplName':
                tLebel = "<?=language('document/purcn', 'tPUNModalPONameSup')?>";
                break;
            case 'FTSplCode':
                tLebel = "<?=language('document/purcn', 'tPUNModalPOCodeSup')?>";
                break;
            case 'FTStyCode':
                tLebel = "<?=language('document/purcn', 'tPUNModalPOType')?>";
                break;
            case 'FDXrhBchReturn':
                tLebel = "<?=language('document/purcn', 'tPUNModalPODateReturn')?>";
                break;
        }
        $('#ospPURCNLabelSearch').html(tLebel);
    }

    //napat 04/02/2563 กดปุ่ม enter แล้วให้มันไปคลิกปุ่มค้นหา
    $('#oetPURCNSearchPdtReturnReq').on('keydown',function(event){
        if(event.keyCode == 13){
            $('#obtPURCNSearchReturnReq').click();
        }
    });

    //napat 22/01/2563
    $('#obtPURCNSearchReturnReq').off('click');
    $('#obtPURCNSearchReturnReq').on('click',function(){
        var tRouteGetdocument = '<?=$tROUTE_omnPurCNNew_getdocument?>';

        $.ajax({
			url     : tRouteGetdocument,
			data 	: { 
                'tTypeRoundorBranch'    : $('#ohdPtRoundBranch').val(), 
                'tTypeSpl'              : $('#ohdPnTypeSupCode').val(),
                'tSearch'               : $('#oetPURCNSearchPdtReturnReq').val(),
                'tColumSearch'          : $('#oetPURCNColumSearch').val()
            },
			type    : 'POST',
			success : function(oResult){
                $('#oetPURCNSearchPdtReturnReq').val('');
                var oResult = JSON.parse(oResult);
                var tHTMLBody = '';
                if(oResult == false){
                    $('.xCNBTNActionConfirm').attr('disabled',true);
                    $('#otbTablePuPDT tbody').html('');
                    $('#otbTablePn tbody').html('');

                    tHTMLBody += '<tr>';
                    tHTMLBody += '<td align="center" colspan="99">ไม่พบข้อมูล</td>';
                    tHTMLBody += '</tr>';
                    $('#otbTablePn tbody').append(tHTMLBody);
                    $('#otbTablePuPDT tbody').append(tHTMLBody);
                    // $('#odvModalDataNotFoundinDatabase').modal('show');
                }else{
                    // $('#odvModalDetailPn').modal('show');
                    $('.xCNBTNActionConfirm').attr('disabled',false);
                    $('#otbTablePn tbody').html('');
                    for(i=0; i<oResult.length; i++){
                        tHTMLBody += '<tr style="cursor:pointer;" onclick='+'JSxClickDetailShowProductPu("'+oResult[i].FTXrhDocNo.trim()+'",this,"'+oResult[i].FTSplCode+'") '+'>';
                        tHTMLBody += '<td>' + oResult[i].FTXrhDocNo + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FDXrhDocDate + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplName + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTStyName + '</td>';
                        // tHTMLBody += '<td>' + oResult[i].FDXrhBchReturn + '</td>';
                        tHTMLBody += '</tr>';

                        if(i==0){
                            //curror css
                            JSxClickDetailShowProductPu(oResult[i].FTXrhDocNo.trim(),this,oResult[i].FTSplCode);
                        }
                    }
                    $('#otbTablePn tbody').append(tHTMLBody);
                    $('#otbTablePn tbody tr:first-child td').addClass('PurtrRoundBranchClick');
                }
			}
		});

        var tHasClass = $('#otbTablePuPDT tbody tr').hasClass('otrNoData');
        if(tHasClass == true){
            $('.table-scroll').attr('style','height : 100px !important;');
        }else{
            $('.table-scroll').attr('style','height : 200px !important;');
        }
    });

    //ข้อมูลสินค้า จากเอกสาร PR
    function JSxClickDetailShowProductPu(ptDocumentNumber,element,ptSplCode){
        $('#ohdInputDocumentPN').val(ptDocumentNumber);
        // $('#ohdInputSplCode').val(ptSplCode);
        $('#ohdPnSupCode').val(ptSplCode);
        // $('#ohdPnTypeSupCode').val($(element).find('td:eq(4)').text());

        //Hightlight record
        $('#otbTablePn tbody tr td').removeClass('PurtrRoundBranchClick');
		$(element).find('td').addClass('PurtrRoundBranchClick');

        var tRouteGetPDTByDocument = '<?=$tROUTE_omnPurCNNew_getpdtbydocument?>';
        $.ajax({
			url     : tRouteGetPDTByDocument,
			data 	: { ptDocumentNumber : ptDocumentNumber },
			type    : 'POST',
			success : function(oResult){
                var oResult = JSON.parse(oResult);
                var tHTMLBody = '';

                $('#otbTablePuPDT tbody').html('');

                if(oResult == false){
                    tHTMLBody += '<tr class="otrNoData">';
                    tHTMLBody += '<td nowrap colspan="11" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>';
                    tHTMLBody += '</tr>';
                    $('#otbTablePuPDT tbody').append(tHTMLBody);
                    $('.table-scroll').attr('style','height : 100px !important;');
                }else{
                    //กำหนดให้มัน scroll ได้
                    var tHasClass = $('#otbTablePuPDT tbody tr').hasClass('otrNoData');
                    if(tHasClass == true){
                        $('.table-scroll').attr('style','height : 100px !important;');
                    }else{
                        $('.table-scroll').attr('style','height : 200px !important;');
                    }
                
                    for(i=0; i<oResult.length; i++){
                        // var tDocumentNumber = "JSxClickDetailShowProductPu('"+oResult[i].FTXnhDocNo.trim()+"')";
                        tHTMLBody += '<tr style="cursor:pointer;">';
                        tHTMLBody += '<td>' + '<input id="ocmCheckPDTPu[]" class="ocmCheckPDTPu" name="ocmCheckPDTPu" type="checkbox" value="'+oResult[i].FTPdtCode+'">' + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FNXrdSeqNo + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTPdtCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTPdtName + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXrdBarCode + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + '' + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXrdUnitName + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXrdQty + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXrdSalePrice + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXrdDisChgTxt + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXrdNet + '</td>';
                        tHTMLBody += '</tr>';
                    }
                    $('#otbTablePuPDT tbody').append(tHTMLBody);
                }
			}
		});
    }

    //กดเลือกยืนยัน ใบ PR
    function JSxConfrimPDTByPu(ptType){
        $('#odvModalDetailPn').modal('hide');
        var tDocumentPN    = $('#ohdInputDocumentPN').val();
        var tPackData      = '';
        var tSplCodeCur    = $('#ohdPnSupCode').val();
        var tSplCodeOld    = $('#ohdInputSplCode').val();

        if(ptType == '' || ptType === undefined){ ptType = 'INSERT_DATA'; }

        // console.log('SendCode: ' + $('#oetPURDocNumberSendCode').val());
        // console.log('tDocumentPN: ' + tDocumentPN);
        // console.log('old: '+tSplCodeOld);
        // console.log('cur: '+tSplCodeCur);

        if( (tSplCodeOld != tSplCodeCur) && (tSplCodeOld != "" && tSplCodeCur != "") && ptType != "CLOSE_INSERT_DATA" && ptType != "CHANGE_SPL" && ptType != "ALTER_AND_INSERT_DATA" && ptType != "ALTER_DATA"  ){ //เลือกใบขอคืนสินค้าอีกครั้ง และรหัสผู้จำหน่าย ไม่เหมือนกับรหัสผู้จำหน่ายปัจจุบัน
            
            // console.log('เมื่อเปลี่ยนผู้จำหน่าย โปรแกรมจะเคลียร์รายการสินค้า\nคุณยืนยันที่จะเปลี่ยนผู้จำหน่ายหรือไม่ ?');

            $('#odvModalWaringProductReturnRequest').modal('show');
            $('#odvModalWaringProductReturnRequest .modal-body').html('เมื่อเปลี่ยนผู้จำหน่าย โปรแกรมจะเคลียร์รายการสินค้า\nคุณยืนยันที่จะเปลี่ยนผู้จำหน่ายหรือไม่ ?');

            //คลิ๊กยืนยันให้ทำการ เปลี่ยนผู้จำหน่าย เคลียร์รายการของเก่า และเพิ่มรายการใหม่เข้าไป
            $('.xWPURCNConfirm').off('click');
            $('.xWPURCNConfirm').on('click',function(){
                // console.log('เปลี่ยนผู้จำหน่าย เคลียร์รายการของเก่า และเพิ่มรายการใหม่เข้าไป');
                setTimeout(function(){
                    JSxConfrimPDTByPu('CHANGE_SPL');
                },500);
            });

            //คลิ๊กยกเลิก ไปยังคำถามถัดไป
            $('.xWPURCNCancel').off('click');
            $('.xWPURCNCancel').on('click',function(){
                // console.log('คำถามถัดไป');
                setTimeout(function(){
                    JSxConfrimPDTByPu('ALTER_AND_INSERT_DATA');
                },500);
            });

        }else if( ((tSplCodeOld == tSplCodeCur) && (tSplCodeOld != "" && tSplCodeCur != "")) && ptType != "CLOSE_INSERT_DATA" && ptType != "ALTER_DATA" || ptType == "ALTER_AND_INSERT_DATA"){ //เลือกใบขอคืนสินค้าอีกครั้ง และรหัสผู้จำหน่ายเดียวกัน
            
            // console.log('คุณต้องการเคลียร์รายการเก่าก่อน หรือไม่ ?');
            
            $('#odvModalWaringProductReturnRequest').modal('show');
            $('#odvModalWaringProductReturnRequest .modal-body').html('คุณต้องการเคลียร์รายการเก่าก่อน หรือไม่ ?');

            //คลิ๊กยืนยันให้ทำการ เคลียร์รายการของเก่า และเพิ่มรายการใหม่เข้าไป
            $('.xWPURCNConfirm').off('click');
            $('.xWPURCNConfirm').on('click',function(){
                // console.log('เคลียร์รายการของเก่า และเพิ่มรายการใหม่เข้าไป');
                setTimeout(function(){
                    JSxConfrimPDTByPu('ALTER_DATA');
                },500);
            });

            //คลิ๊กยกเลิก นำรายการใหม่แทรกเข้าไป
            $('.xWPURCNCancel').off('click');
            $('.xWPURCNCancel').on('click',function(){
                // console.log('เพิ่มรายการใหม่');
                setTimeout(function(){
                    JSxConfrimPDTByPu('CLOSE_INSERT_DATA');
                },500);
            });
        }else{

            // console.log('ptType: ' + ptType);
            // console.log('เพิ่มสินค้า');
            var tSendCode = $('#ohdInputDocPN').val();
            if((ptType != "CHANGE_SPL" && tSendCode != "") || ptType == "CLOSE_INSERT_DATA" ){
                // console.log('ไม่เปลี่ยนผู้จำหน่าย');
                tDocPNCurrent = tSendCode;
            }else{
                // console.log('เปลี่ยนผู้จำหน่าย');
                tDocPNCurrent = tDocumentPN;
            }

            // console.log(tDocPNCurrent);

            //เอาค่ากลับไปที่ input
            $('#oetPURDocNumberSendName').val(tDocumentPN);
            $('#oetPURDocNumberSendCode').val(tDocumentPN);

            $.each($("input[name='ocmCheckPDTPu']:checked"), function(){            
                tPackData += $(this).val() + ",";
            });

            
            var tFormatCode = $('#ospDocumentnoValue').text();
            if(tFormatCode == 'PCBCHYY-#######'){
                var tDocumentID = '';
            }else{
                var tDocumentID = tFormatCode;
            }
            var tRouteInsertPDTByPUR1 = '<?=$tROUTE_omnPurCNNew_insertpdtByPUR1?>';
            $.ajax({
                url     : tRouteInsertPDTByPUR1,
                data 	: { 
                    tPackData       : tPackData ,
                    tDocumentID     : tDocumentID,
                    tDocumentPN     : tDocumentPN,
                    tDocPNCurrent   : tDocPNCurrent,
                    tType           : ptType
                },
                type    : 'POST',
                success : function(oResult){
                    var aData = JSON.parse(oResult);

                    var tTextSupplier      = aData.aDetailSup[0].FTSplName + '(' + aData.aDetailSup[0].FTSplCode + ')';
                    var tTextAddress       = '<?=language('document/purreqcn', 'tPURAddress');?>' + aData.aDetailSup[0].FTSplStreet + aData.aDetailSup[0].FTSplDistrict + aData.aDetailSup[0].FTDstName + aData.aDetailSup[0].FTPvnName + aData.aDetailSup[0].FTDstCode;
                    var tTextTelphone      = '<?=language('document/purreqcn', 'tPURTelphone');?>' + aData.aDetailSup[0].FTSplTel;
                    var tTextFax           = '<?=language('document/purreqcn', 'tPURFax');?>' + aData.aDetailSup[0].FTSplFax;

                    $('#ospDocumentnoValue').text(aData.tDocno);
                    $('#oetPURSupplier').val(tTextSupplier);
                    $('#ospSPLAddress').text(tTextAddress);
                    $('#ospSPLTelphone').text(tTextTelphone);
                    $('#ospSPLFax').text(tTextFax);
                    $('#oetPURDocDateSend').val(aData.tDocDate);

                    //เก็บค่า ผู้จำหน่าย ล่าสุดไว้ เพื่อเปรียบเทียบ เวลาเปลี่ยนผู้จำหน่าย
                    if(ptType == "CHANGE_SPL" || ptType == "INSERT_DATA"){
                        $('#ohdInputSplCode').val(aData.aHDPr[0].FTSplCode);
                        $('#ohdInputDocPN').val(tDocumentPN);
                    }

                    //ค่า spl
                    $('#ohdPnSupCode').val(aData.aHDPr[0].FTSplCode);
                    $('#ohdPnTypeSupCode').val(aData.aHDPr[0].FTStyCode);
                    
                    //VAT
                    $('#ohdHiddenVat').val(aData.aHDPr[0].FCXnhVATRate);
                    $('#ohdHiddenTypeVat').val(aData.aHDPr[0].FTVatCode);

                    //เหตุผล
                    $('#oetPURSupplierReasonName').val(aData.aHDPr[0].FTCutName);
                    $('#oetPURSupplierReason').val(aData.aHDPr[0].FTSpnCode);
                    $('#otaReason').text(aData.aHDPr[0].FTXrhRmk);
                    

                    JSxSelectDataintoTablePUR(1);
                    $('body').animate({scrollTop:0}, 'slow');

                    //ไม่สามารถเลือก เอกสาร ref ใหม่ได้ 
                    // $('#obtBrowsePURNumberSend').prop('disabled',true); //Comsheet 2020-012

                    //ส่วนของคำนวณภาษี
                    if(aData.aDetailSup[0].FTSplVATInOrEx == 1){
                        var nVatRate = '(VI) '+'7%';
                        $('#oetTextVat').val(nVatRate);
                        $('#ohdHiddenVat').val(7);
                        $('#ohdHiddenTypeVat').val('VI');
                    }else{
                        var nVatRate = '(VE) '+'7%';
                        $('#oetTextVat').val(nVatRate);
                        $('#ohdHiddenVat').val(7);
                        $('#ohdHiddenTypeVat').val('VE');
                    }
                    JSvCalculateTotal();

                    // Create By Jame 30/04/2020
                    // ComSheet 2020-219
                    // ถ้าเจอสินค้าที่ไม่อนุญาติให้คืน ให้ Popup แสดงรายการสินค้านั้นๆ
                    if(aData['aDataNotReturn'].length > 0){
                        let tHTML = '';
                        for (let i = 0; i < aData['aDataNotReturn'].length; ++i) {
                            tHTML += aData['aDataNotReturn'][i]['FTPdtBarCode'] + '&nbsp;&nbsp;&nbsp;' + aData['aDataNotReturn'][i]['FTPdtName'] + '<br>';
                        }
                        $('.xCNTextPdtNotReturn').html(tHTML);
                        $('#odvModalPdtNotReturn').modal('show');
                    }

                }
            });
        }

        

    }
</script>