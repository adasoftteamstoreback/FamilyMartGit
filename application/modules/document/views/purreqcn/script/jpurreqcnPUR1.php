<script>

    //Browse ใบซื้อ
    $('#obtBrowsePURNumberSend').click(function(){
        var tRouteGetdocument = '<?=$tROUTE_omnPurReqCNNew_getdocument?>';
        $.ajax({
            url     : tRouteGetdocument,
            data 	: { },
            type    : 'POST',
            success : function(oResult){
                var oResult = JSON.parse(oResult);
                var tHTMLBody = '';
                if(oResult == false){
                    $('#odvModalDataNotFoundinDatabase').modal('show');
                }else{
                    $('#odvModalDetailPn').modal('show');
                    $('#otbTablePn tbody').html('');
                    for(i=0; i<oResult.length; i++){
                        tHTMLBody += '<tr style="cursor:pointer;" onclick='+'JSxClickDetailShowProductPu("'+oResult[i].FTXnhDocNo.trim()+'",this) '+'>';
                        tHTMLBody += '<td>' + oResult[i].FTXnhDocNo + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FDXnhDocDate + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplName + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTSplCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTStyCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FDXnhBchReturn + '</td>';
                        tHTMLBody += '</tr>';

                        if(i==0){
                            //curror css
                            JSxClickDetailShowProductPu(oResult[i].FTXnhDocNo.trim(),this);
                        }
                    }
                    $('#otbTablePn tbody').append(tHTMLBody);
                    $('#otbTablePn tbody tr:first-child').addClass('PurtrRoundBranchClick');
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

    //ข้อมูลสินค้า จากเอกสาร PU
    function JSxClickDetailShowProductPu(ptDocumentNumber,element){
        $('#ohdInputDocumentPN').val(ptDocumentNumber);
        $('#ohdPnSupCode').val($(element).find('td:eq(3)').text());
        $('#ohdPnTypeSupCode').val($(element).find('td:eq(4)').text());

        //Hightlight record
        $('#otbTablePn tbody tr').removeClass('PurtrRoundBranchClick');
		$(element).addClass('PurtrRoundBranchClick');

        var tRouteGetPDTByDocument = '<?=$tROUTE_omnPurReqCNNew_getpdtbydocument?>';
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
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FNXndSeqNo + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTPdtCode + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTPdtName + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXndBarCode + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + '' + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXndUnitName + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXndQty + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXndSetPrice + '</td>';
                        tHTMLBody += '<td>' + oResult[i].FTXndDisChgTxt + '</td>';
                        tHTMLBody += '<td style="text-align: right;">' + oResult[i].FCXndNet + '</td>';
                        tHTMLBody += '</tr>';
                    }
                    $('#otbTablePuPDT tbody').append(tHTMLBody);
                }
			}
		});
    }

    // กดเลือกยืนยัน ใบ PU
    // Last Updated : Napat(Jame) 28/04/2020 เพิ่มเงื่อนไขให้มันเลือกได้หลายครั้ง
    function JSxConfrimPDTByPu(){
        // Create By Jame 28/04/2020
        // ComSheet 2020-210
        // เช็คว่ามีสินค้าใน Table Grid ไหม ? ถ้ามีให้แจ้งเตือน จะเคลียร์ หรือ จะทับ
        if($('.xCNTableTrClickActive').length > 0){
            $('#odvModalDetailPn').modal('hide');
            $('#odvPURModalClearTemp').modal('show');

            // ลบสินค้าใน Temp ทั้งหมด และเพิ่มเข้าไปใหม่
            $('#obtPURComfirmClearTemp').off('click');
            $('#obtPURComfirmClearTemp').on('click',function(){
                JSxPURAddPdtByPuToTableGrid('1');
            });

            // ไม่เคลียร์ใน Temp แต่เพิ่มสินค้าลงไป
            $('#obtPURCancelClearTemp').off('click');
            $('#obtPURCancelClearTemp').on('click',function(){
                JSxPURAddPdtByPuToTableGrid('2');
            });
        }else{
            JSxPURAddPdtByPuToTableGrid('0');
        }
    }

    // เพิ่มสินค้าลง Table Grid
    // Create By    : Napat(Jame) 28/04/2020
    // Parameters   : TypeDelTemp 0 = เพิ่มสินค้าครั้งแรก , 1 = เพิ่มใหม่ , 2 = เพิ่มแค่สินค้าไม่เปลี่ยน spl/vat
    function JSxPURAddPdtByPuToTableGrid(ptTypeDelTemp){
        $('#odvModalDetailPn').modal('hide');
        var tDocumentPN    = $('#ohdInputDocumentPN').val();
        var tPackData      = '';

        //เอาค่ากลับไปที่ input
        $('#oetPURDocNumberSendName').val(tDocumentPN);
        $('#oetPURDocNumberSendCode').val(tDocumentPN);

        $.each($("input[name='ocmCheckPDTPu']:checked"), function(){            
            tPackData += $(this).val() + ",";
        });

        var tFormatCode = $('#ospDocumentnoValue').text();
        if(tFormatCode == 'PEBCHYY-#######'){
            var tDocumentID = '';
        }else{
            var tDocumentID = tFormatCode;
        }

        var tRouteInsertPDTByPUR1 = '<?=$tROUTE_omnPurReqCNNew_insertpdtByPUR1?>';
        $.ajax({
			url     : tRouteInsertPDTByPUR1,
			data 	: { 
                tPackData       : tPackData ,
                tDocumentID     : tDocumentID,
                tDocumentPN     : tDocumentPN,
                tTypeDelTemp    : ptTypeDelTemp
            },
			type    : 'POST',
			success : function(oResult){
                var aData = JSON.parse(oResult);
                var tTextSupplier      = aData.aDetailSup[0].FTSplName + '(' + aData.aDetailSup[0].FTSplCode + ')';
                var tTextAddress       = '<?=language('document/purreqcn', 'tPURAddress');?>' + aData.aDetailSup[0].FTSplStreet + aData.aDetailSup[0].FTSplDistrict + aData.aDetailSup[0].FTDstName + aData.aDetailSup[0].FTPvnName + aData.aDetailSup[0].FTDstCode;
                var tTextTelphone      = '<?=language('document/purreqcn', 'tPURTelphone');?>' + aData.aDetailSup[0].FTSplTel;
                var tTextFax           = '<?=language('document/purreqcn', 'tPURFax');?>' + aData.aDetailSup[0].FTSplFax;

                if(ptTypeDelTemp != '3'){
                    $('#ospDocumentnoValue').text(aData.tDocno);
                    $('#oetPURSupplier').val(tTextSupplier);
                    $('#ospSPLAddress').text(tTextAddress);
                    $('#ospSPLTelphone').text(tTextTelphone);
                    $('#ospSPLFax').text(tTextFax);
                    $('#oetPURDocDateSend').val(aData.tDocDate);

                    //ค่า spl
                    $('#ohdPnSupCode').val(aData.aHDPn[0].FTSplCode);
                    $('#ohdPnTypeSupCode').val(aData.aHDPn[0].FTStyCode);
                    $('#ohdPtRoundBranch').val('PUR1');

                    //VAT
                    $('#ohdHiddenVat').val(aData.aHDPn[0].FCXnhVATRate);
                    $('#ohdHiddenTypeVat').val(aData.aHDPn[0].FTVatCode);

                    //VatType SPL
                    $('#ohdHiddenTypeVatSPL').val(aData.aHDPn[0].FTXnhVATInOrEx);
                }

                JSxSelectDataintoTablePUR(1);
                $('body').animate({scrollTop:0}, 'slow');

                //ไม่สามารถเลือก เอกสาร ref ใหม่ได้ 
                // $('#obtBrowsePURNumberSend').prop('disabled',true);

                // Create By Jame 27/04/2020
                // ComSheet 2020-213
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
</script>