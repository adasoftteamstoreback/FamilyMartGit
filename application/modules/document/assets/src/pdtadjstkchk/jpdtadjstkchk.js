$('document').ready(function() {
    JSvPASCallPageMain();
    JSxPASHideAllButton(); //Reset Button on start
    $('.xWPASSubMenu').hide();
    sessionStorage.removeItem("EditInLine");
});

function JSxPASHideAllButton() {
    $('.xWPASBTSearchDO').hide();
    $('.xWPASBTSearchAutoReceive').hide();
    $('.xWPASBTSearch').hide();
    $('.xWPASBTSave').hide();
    $('.xWPASBTPrevious').hide();
    $('.xWPASBTCancel').hide();
    $('.xWPASBTAddNew').hide();
    $('.xWPASBTApprove').hide();
    $('.xWPASBTReport').hide();
    $('.xWPASBTNext').hide();
    $('#ospTextHeadMenu').hide();
    $('#ospTextSumHeadMenu').hide();
}

function JSvPASCallPageMain(ptDocNo, pnTypePage) {
    var nTypePage = "";
    if (ptDocNo == "" || ptDocNo === undefined) { ptDocNo = ' '; }
    if (pnTypePage == "" || pnTypePage === undefined) { // 1=เอกสารตรวจนับสินค้า 2=รวมเอกสารตรวจนับสินค้า
        nTypePage = 1;
    } else {
        nTypePage = pnTypePage;
    }

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCallPageMain",
        data: {
            FTIuhDocNo: ptDocNo,
            pnTypePage: nTypePage
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var nSetPageType = "";
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);

            if (aReturn['tQuery']['nSetPageType'] == "" || aReturn['tQuery']['nSetPageType'] === undefined) {
                nSetPageType = nTypePage;
            } else {
                if (pnTypePage == "" || pnTypePage === undefined) {
                    nSetPageType = aReturn['tQuery']['nSetPageType'];
                } else {
                    nSetPageType = nTypePage;
                }
            }

            if (aReturn['tQuery']['nSetPageType'] == 2 && (pnTypePage == "" || pnTypePage === undefined)) {
                $('.xWPASBTPrevious').attr('disabled', true);
                JSxPASAlertMessage(aModalText = {
                    tHead: $('#oetPASHeadLastDocNotComplete').val(),
                    tDetail: $('#oetPASTextLastDocNotComplete').val(),
                    nType: 2
                });
                $('#oetPASPassword').val(aReturn['tQuery']['aItems']['FTSplCode']);
                JSvPASCallPageMain(ptDocNo, 2);
            } else {
                $('#odvPASContentMain').html(aReturn['tHTML']);
                JSvPASCallDataTable('', nSetPageType);
                JSvPASCallDataPdtWithOutSystemTable('', nSetPageType);
            }

            if (aReturn['tQuery']['nStaQuery'] == 88 && aReturn['tQuery']['nSetPageType'] == 1) {
                var tDocNo = aReturn['tQuery']['aItems']['FTIuhDocNo'];
                JSxPASAlertMessage(aModalText = {
                    tHead: $('#oetPASHeadLastDocNotComplete').val(),
                    tDetail: $('#oetPASTextLastDocNotComplete').val(),
                    nType: 2
                });
                $('#oetPASStaChkDateDT').val('TRUE');
                JSvPASCallPageMain(tDocNo, 1);
            }

            $('.xWPASBTNext').data('page', nSetPageType);
            $('.xWPASBTNext').attr('data-page', nSetPageType);
            $('.xWPASBTPrevious').data('page', nSetPageType);
            $('.xWPASBTPrevious').attr('data-page', nSetPageType);
            $('#oetPASTypePage').val(nSetPageType);

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });

}

function JSvPASCallDataTable(nPage, pnTypePage, pnSeq) {
    var tDocNo = $('#oetPASDocNo').val();

    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    if (nPage == "" || nPage === undefined) { nPage = 1; }
    if (pnTypePage == "" || pnTypePage === undefined) { pnTypePage = 1; }

    if (pnTypePage != 1) {
        JSxContentLoader('show');
    }

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCallDataTable",
        data: {
            FTIuhDocNo: tDocNo,
            nPageCurrent: nPage,
            pnTypePage: pnTypePage,
            ptStaPrcDoc: $('#oetPASIuhStaPrcDoc').val()
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASContentTable').html(tResult);
            JSxPASControlButton();

            //ถ้าส่ง Seq มาด้วยให้ Focus
            if (pnSeq != "" || pnSeq !== undefined) {
                $('.xWPASProductSeq' + pnSeq).addClass('xCNTableTrActive');
                if (pnTypePage != '1') {
                    $('#oetPASIudQtyBal' + pnSeq).focus();
                } else {
                    $('#oetPASIudQtyC1' + pnSeq).focus();
                }
            }

            JSxContentLoader('hide');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSvPASCallDataPdtWithOutSystemTable(nPage, pnTypePage, paPackData) {
    var tDocNo = $('#oetPASDocNo').val();
    var tPassword = $('#oetPASPassword').val();

    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    if (nPage == "" || nPage === undefined) { nPage = 1; }
    if (pnTypePage == "" || pnTypePage === undefined) { pnTypePage = 1; }

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCallDataPdtWithOutSystemTable",
        data: {
            FTIuhDocNo: tDocNo,
            nPageCurrent: nPage,
            pnTypePage: pnTypePage,
            ptPassword: tPassword
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASContentTableWithOutSystem').html(tResult);
            JSxPASControlButton();

            //ถ้าส่ง Seq มาด้วยให้ Focus
            if (paPackData != "" && paPackData !== undefined) {
                var tDocRef = paPackData['FTIuhDocNo'];
                var tBarCode = paPackData['FTPdtBarCode'];
                var tPlcCode = paPackData['FTPlcCode'];

                var tRefShotCut = tDocRef.substring(11, 16) + "_" + tPlcCode + "_" + tBarCode;

                $('.xWPASPdtWithOutSystem_' + tRefShotCut).addClass('xCNTableTrActive');
                $('#oetPAS2PdtName_' + tRefShotCut).focus();
            }

            JSxContentLoader('hide');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSvPASAddProduct(ptTab, paModalText) {
    if (ptTab == 'Pdt' || ptTab == 'PdtSup' || ptTab == 'User') {
        if ($('#oetPAS' + ptTab + 'FromCode').val() == "" || $('#oetPAS' + ptTab + 'ToCode').val() == "") {
            // alert('ข้อมูลไม่สมบูรณ์ กรุณาใส่ใหม่');
            JSxPASAlertMessage(aModalText = {
                tHead: paModalText['tHead1'],
                tDetail: paModalText['tDetail1'],
                nType: paModalText['nType']
            });
            return false;
        }
    } else if (ptTab == 'Group') {
        if ($('#oetPASGrpPdtCode').val() == "") {
            // alert('ข้อมูลไม่สมบูรณ์ กรุณาใส่ใหม่');
            JSxPASAlertMessage(aModalText = {
                tHead: paModalText['tHead1'],
                tDetail: paModalText['tDetail1'],
                nType: paModalText['nType']
            });
            return false;
        }
    } else if (ptTab == 'Location') {
        if ($('#oetPASLocCode').val() == "") {
            // alert('ไม่พบข้อมูล หรือสินค้าไม่อยู่ในกลุ่มที่สามารถตรวจนับได้');
            JSxPASAlertMessage(aModalText = {
                tHead: paModalText['tHead2'],
                tDetail: paModalText['tDetail2'],
                nType: paModalText['nType']
            });
            return false;
        }
    }

    var tDocNo = $('#oetPASDocNo').val();
    var aPASLoc = [];
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    $("input[name='orbPASLocation[]']:checked").each(function() {
        aPASLoc.push($(this).val());
    });

    if (aPASLoc.length !== 0) {
        JSxContentLoader('show');
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASAddProduct",
            data: {
                ptDocDate: $('#oetPASDocDate').val(),
                ptDocNo: tDocNo,
                ptTab: ptTab,
                ptFromCode: $('#oetPAS' + ptTab + 'FromCode').val(),
                ptToCode: $('#oetPAS' + ptTab + 'ToCode').val(),
                ptGrpCode: $('#oetPASGrpPdtCode').val(),
                ptLocCode: $('#oetPASLocCode').val(),
                paPlcCode: aPASLoc
            },
            cache: false,
            timeout: 0,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                if (aReturn['nStaQuery'] == 1) {
                    JSvPASCallDataTable();
                } else if (aReturn['nStaQuery'] == 88) {
                    JSxContentLoader('hide');
                    if (ptTab == 'Pdt' || ptTab == 'PdtAndBar') {
                        JSxPASAlertMessage(aModalText = {
                            tHead: '',
                            tDetail: 'สินค้ารายการนี้อยู่ในเอกสารตรวจนับแล้ว',
                            nType: 2
                        });
                    }
                    JSvPASCallDataTable();
                } else {
                    JSxContentLoader('hide');
                    if (ptTab == 'Pdt' || ptTab == 'PdtAndBar') {
                        JSxPASAlertMessage(aModalText = {
                            tHead: '',
                            tDetail: 'สินค้านี้ไม่มีอยู่ในระบบ',
                            nType: 2
                        });
                    }
                    JSvPASCallDataTable();
                }
                $('#oetPAS' + ptTab + 'FromCode').val('');
                $('#oetPAS' + ptTab + 'ToCode').val('');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    } else {
        JSxPASAlertMessage(aModalText = {
            tHead: '',
            tDetail: 'กรุณาเลือกที่เก็บ',
            nType: 2
        });
    }
}

function JSxPASClickPage(ptPage, ptPageType) {
    if (ptPageType == '1') {
        var nPageOld = $('.xWPagePdtAdjStkChk .active').text();
        var nTypePage = $('#oetPASTypePage').val();
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, 10) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, 10) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSvPASCallDataTable(nPageCurrent, nTypePage);
        $('#oetPASCurrentPage').val(nPageCurrent);
    } else {
        var nPageOld = $('.xWPagePdtWithOutSystem .active').text();
        var nTypePage = $('#oetPASTypePage').val();
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, 10) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, 10) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSvPASCallDataPdtWithOutSystemTable(nPageCurrent, nTypePage);
        $('#oetPASCurrentPage').val(nPageCurrent);
    }
}

function JSxPASDeleteProduct(oObj) {
    var tDocNo = $('#oetPASDocNo').val();
    var tSeq = oObj.parent().parent().data('seq');
    var tScrollVal = $('.xWPASTableProduct').scrollLeft();
    var nPage = $('#oetPASCurrentPage').val();
    var nPageType = $('#oetPASTypePage').val();
    if (nPage == "" || nPage === undefined) { nPage = 1; }
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASDelProduct",
        data: {
            ptSeq: tSeq,
            ptDocNo: tDocNo
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            if (aReturn['nStaQuery'] == 1) {
                JSvPASCallDataTable(nPage, nPageType);
                setTimeout(function() {
                    $('.xWPASTableProduct').scrollLeft(tScrollVal);
                }, 100);

            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASCheckDateTime(paTextChkDateTime, paTextConfirmCode) {
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCheckDateTime",
        data: {
            ptDocNo: tDocNo,
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
            if (aReturn['nStaQuery'] == 1) {
                JSxPASAlertMessage(paTextChkDateTime);
                $('.xWPASConfirmAlertMessage').off('click');
                $('.xWPASConfirmAlertMessage').on('click', function() {
                    // $('.xWPASConfirmAlertMessage').off('click');
                    JSxPASUpdateDateTime();
                    if (tDocNo == " ") {
                        setTimeout(function() {
                            JSxPASConfirmCode(paTextConfirmCode);
                        }, 500);
                    }
                });
            } else {
                if (tDocNo == " ") {
                    JSxPASConfirmCode(paTextConfirmCode);
                } else {
                    JSvPASCallDataTable(); // Napat(Jame) 08/12/2022 กรณีได้เลขที่ใบย่อยแล้ว เพิ่มสินค้าใหม่ เมื่อกดบันทึกให้โหลดหน้า DataTable ใหม่
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASUpdateDateTime() {
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASUpdateDateTime",
        data: {
            ptDocNo: tDocNo,
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            if (tDocNo != " " && $('#oetPASStaChkDateDT').val() == "TRUE") {
                JSvPASCallPageMain(tDocNo, 1);
            }
            // var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASConfirmCode(paModalText) {
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }

    JSxPASAlertMessage(paModalText);
    $('.xWPASConfirmAlertMessage').off('click');
    $('.xWPASConfirmAlertMessage').on('click', function() {
        var tInput = $('#oetPASModalInput').val();
        if (tInput == "" || tInput === undefined) {
            var aPlsEnterPsw = {
                tHead: $('#oetPASHeadAlert').val(),
                tDetail: $('#oetPASTextPlsEnterPass').val(),
                nType: 2
            };
            setTimeout(function() {
                JSxPASAlertMessage(aPlsEnterPsw);
            }, 500);
            // $('.xWODSCloseAlertMessage').off('click');
            // $('.xWODSCloseAlertMessage').on('click',function(){
            //     setTimeout(function(){ 
            //         JSxPASAlertMessage(paModalText);
            //     }, 500);
            // });
        } else {
            JSxPASAddEditHD();
        }
    });
}

function JSxPASAddEditHD() { //pnTypeUpd
    // console.log($('#ofmPdtAdjStkChk').serializeArray());
    var oDataList = $('#ofmPdtAdjStkChk').serialize();
    var tInput = $('#oetPASModalInput').val();
    var tDocNo = $('#oetPASDocNo').val();
    var tTypePage = $('#oetPASTypePage').val();
    var tRefTaxOver = $('#oetIuhRefTaxOver').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    JSxContentLoader('show');
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASAddEditHD",
        data: oDataList + '&ptInput=' + tInput + '&ptTypePage=' + tTypePage + '&ptAdjType=' + $('#ocbPASAdjType').prop("checked") + '&ptRefTaxOver=' + tRefTaxOver,
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);

            if (tTypePage == '3') { //ใบรวม
                if (aReturn['aDataQuery']['nStaQuery'] == 1) {
                    JSvPASCallPageMain(aReturn['FTIuhDocNo'], 3);
                } else {
                    JSxPASAlertMessage(aModalText = {
                        tHead: 'Error',
                        tDetail: aReturn['aDataQuery']['tStaMessage'],
                        nType: 2
                    });
                }
            } else { //ใบย่อย
                if (aReturn['aDataQuery']['nStaQuery'] == 1) {
                    JSvPASCallPageMain(aReturn['FTIuhDocNo']);
                } else {
                    JSxPASAlertMessage(aModalText = {
                        tHead: 'Error',
                        tDetail: aReturn['aDataQuery']['tStaMessage'],
                        nType: 2
                    });
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

//Edit inline ของหน้าจอ สินค้าตรวจนับ
function JSxPASEditInLine(poElm, pnType) {

    if (sessionStorage.getItem("EditInLine") == "1") {
        sessionStorage.setItem("EditInLine", "2");
        var tDocNo = $('#oetPASDocNo').val();
        var tSeq = poElm.parent().parent().parent().data('seq');

        //Check Values if null or ""
        if ($('#oetPASIudQtyC1' + tSeq).val() == "") { $('#oetPASIudQtyC1' + tSeq).val(0); }
        if ($('#oetPASIudQtyBal' + tSeq).val() == "") { $('#oetPASIudQtyBal' + tSeq).val(0); }

        // Values
        var tVal = parseInt($('#oetPASIudQtyC1' + tSeq).val());
        var dDate = JStPASGetDateTime(121, $('#oetPASIudChkDate' + tSeq).val());
        var tTime = $('#oetPASIudChkTime' + tSeq).val();
        var tQtyBal = parseInt($('#oetPASIudQtyBal' + tSeq).val());
        var tPlcCode = $('#oetPASPlcCode' + tSeq).val();

        // var nPage       = $('#oetPASCurrentPage').val();
        var nIndex = $('.xWInputCanEdit').index(poElm);
        var tTypePage = String($('#oetPASTypePage').val());

        if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }

        $.ajax({
            type: "POST",
            url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASEditInLine",
            data: {
                ptDocNo: tDocNo,
                ptSeq: tSeq,
                ptVal: tVal,
                pdDate: dDate,
                ptTime: tTime,
                ptTypePage: tTypePage,
                ptQtyBal: tQtyBal,
                ptPlcCode: tPlcCode
            },
            cache: false,
            timeout: 0,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                // console.log(aReturn);
                if (aReturn['nStaQuery'] == 1) {
                    if (tTypePage == 1) { //ใบย่อย
                        switch (pnType) {
                            case 1:
                                var tQty = $('#oetPASIudQtyC1' + tSeq).val();
                                var tDate = $('#oetPASIudChkDate' + tSeq);
                                var tTime = $('#oetPASIudChkTime' + tSeq);
                                // if(tDate.val()=="" && tTime.val()==""){ //Comsheet 2020-016
                                tDate.val(JStPASGetDateTime(121));
                                tTime.val(JStPASGetDateTime(108));
                                $('#oetPASIudQtyC1' + tSeq).val(parseInt(tQty));
                                // }
                                if ($('#' + poElm.context.id).hasClass('xWPASNotNextFocus') === false) {
                                    $('.xWInputCanEdit').eq(nIndex + 1).focus();
                                }
                                break;
                            case 2:
                                break;
                        }
                    } else { //ใบรวม
                        var tWahQty = $('.xWPASIudWahQty' + tSeq).text();
                        $('#oetPASIudQtyBal' + tSeq).val(tQtyBal);
                        $('.xWPASQtyDiff' + tSeq).text(tQtyBal - tWahQty);
                        $('.xWInputCanEdit').eq(nIndex + 1).focus();
                    }
                } else {
                    JSxPASAlertMessage(aModalText = {
                        tHead: 'Error',
                        tDetail: aReturn['nStaQuery']['tStaMessage'],
                        nType: 2
                    });
                }

                sessionStorage.removeItem("EditInLine");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    }

}

//Edit inline ของหน้าจอ สินค้าไม่มีในระบบ
function JSxPASEditInLinePdtWithOutSystem(poElm) {

    //ป้องกันปัญหา event keydown กับ change ทำงานพร้อมกัน
    if (sessionStorage.getItem("EditInLine") == "1") {
        sessionStorage.setItem("EditInLine", "2");

        //Set Values
        var tDocRef = poElm.parent().parent().parent().data('docno');
        var tBarCode = poElm.parent().parent().parent().data('barcode');
        var tPlcCode = poElm.parent().parent().parent().data('plc');
        var tRefShotCut = tDocRef.substring(11, 16) + "_" + tPlcCode + "_" + tBarCode;
        var nIndex = $('.xWInputCanEdit').index(poElm);

        if ($('#oetPAS2SetPri_' + tRefShotCut).val() == "") { $('#oetPAS2SetPri_' + tRefShotCut).val(0); }
        if ($('#oetPAS2UnitC1_' + tRefShotCut).val() == "") { $('#oetPAS2UnitC1_' + tRefShotCut).val(0); }

        //Get Values In Input
        var tPdtName = $('#oetPAS2PdtName_' + tRefShotCut).val();
        var cSetPri = parseFloat($('#oetPAS2SetPri_' + tRefShotCut).val());
        var nUnitC1 = parseInt($('#oetPAS2UnitC1_' + tRefShotCut).val());

        $.ajax({
            type: "POST",
            url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASEditInLinePdtWithOutSystem",
            data: {
                ptDocRef: tDocRef,
                ptBarCode: tBarCode,
                ptPlcCode: tPlcCode,
                ptPdtName: tPdtName,
                pcSetPri: cSetPri,
                pnUnitC1: nUnitC1
            },
            cache: false,
            timeout: 0,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                // console.log(aReturn);

                $('#oetPAS2SetPri_' + tRefShotCut).val(cSetPri.toFixed(2));
                $('#oetPAS2UnitC1_' + tRefShotCut).val(nUnitC1);
                $('.xWInputCanEdit').eq(nIndex + 1).focus();

                sessionStorage.removeItem("EditInLine");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });

    }
}

function JStPASGetDateTime(nType, tDate) {
    if (tDate == '' || tDate === undefined) {
        var today = new Date();
    } else {
        var today = new Date(tDate);
    }
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var mi = today.getMinutes();
    var ss = today.getSeconds();
    var tReturn = "";
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    if (hh < 10) {
        hh = '0' + hh;
    }
    if (mi < 10) {
        mi = '0' + mi;
    }
    if (ss < 10) {
        ss = '0' + ss;
    }
    switch (nType) {
        case 121:
            tReturn = yyyy + '-' + mm + '-' + dd;
            break;
        case 108:
            tReturn = hh + ':' + mi + ':' + ss;
            break;
    }
    return tReturn;
}

function JSxPASCancelHD() {
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    // JSxPASAlertMessage(paModalText);
    // $('.xWPASConfirmAlertMessage').off('click');
    // $('.xWPASConfirmAlertMessage').on("click",function(){
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCancelHD",
        data: {
            ptDocNo: tDocNo
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);

            if (aReturn['nStaQuery'] == 1) {
                JSvPASCallPageMain();
            } else {
                JSxPASAlertMessage(aModalText = {
                    tHead: 'Error',
                    tDetail: aReturn['nStaQuery']['tStaMessage'],
                    nType: 2
                });
                // console.log(aReturn['nStaQuery']['tStaMessage']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
    // });
}

function JSxPASControlSubMenu(pnShow) {
    switch (pnShow) {
        case 1:
            $('.xWPASSubMenu1').show();
            $('.xWPASSubMenu2').hide();
            $('.xWPASSubMenu3').hide();
            $('.xWPASSubMenu4').hide();
            $('#ospTextHeadMenu').show();
            $('#ospTextSumHeadMenu').hide();
            break;
        case 2:
            $('.xWPASSubMenu1').hide();
            $('.xWPASSubMenu2').show();
            $('.xWPASSubMenu3').hide();
            $('.xWPASSubMenu4').hide();
            $('#ospTextSumHeadMenu').show();
            $('#ospTextHeadMenu').hide();
            break;
        case 3:
            $('.xWPASSubMenu1').hide();
            $('.xWPASSubMenu2').hide();
            $('.xWPASSubMenu3').show();
            $('.xWPASSubMenu4').hide();
            $('#ospTextSumHeadMenu').show();
            $('#ospTextHeadMenu').hide();
            break;
        case 4:
            $('.xWPASSubMenu1').hide();
            $('.xWPASSubMenu2').hide();
            $('.xWPASSubMenu3').hide();
            $('.xWPASSubMenu4').show();
            $('#ospTextSumHeadMenu').show();
            $('#ospTextHeadMenu').hide();
            break;
    }
}

function JSxPASControlButton() {
    var tDocNo = String($('#oetPASDocNo').val());
    var tStaDoc = String($('#oetPASIuhStaDoc').val()); //สถานะเอกสาร
    var tStaPrcDoc = String($('#oetPASIuhStaPrcDoc').val()); //สถานะการประมวลผล
    var tStaDT = String($('#oetPASStaDT').val()); //สินค้าในตาราง DT
    var tStaDocTyp = String($('#oetPASIuhDocType').val()); //ประเภทเอกสาร 1=ใบย่อย , 2=ใบรวม
    var tTypePage = String($('#oetPASTypePage').val()); //แบ่งหน้า
    var tChkDateDT = String($('#oetPASStaChkDateDT').val()); //ตรวจสอบว่าสินค้าใน DT มีวันที่หรือยัง true = มีสินค้าบางตัวไม่มีวันที่ , false = สินค้าทุกตัวมีวันที่

    //เปิดหน้าใหม่
    if (tStaDoc == "" && tStaPrcDoc == "" && tStaDT == '99' && tStaDocTyp == '1') {
        JSxPASHideAllButton();
        $('.xWPASBTSearch').show();
        $('.xWPASBTSearchHQ').show().attr('disabled', false);
        // JSxPASControlSubMenu(1);
    }
    //กรณีเลือกสินค้าลง DT แล้ว
    if (tStaDoc == "" && tStaDT == '1' && tStaDocTyp == '1') {
        $('.xWPASBTSave').show().attr('disabled', false);
        $('.xWPASBTSearch').hide();
        // JSxPASControlSubMenu(1);
    }
    //กรณีกดบันทึกแล้ว
    if (tStaDoc == '1' && tStaDocTyp == '1') {
        // JSxPASControlSubMenu(1);
        $('.xWPASBTPrevious').hide();
        if (tChkDateDT == 'FALSE') {
            $('.xWPASBTNext').show();
            $('.xWPASBTReport').show();
            $('.xWPASBTAddNew').show();
            $('.xWPASBTSearch').show();
            $('.xWPASBTSave').hide();
        } else {
            $('.xWPASBTSave').show();
            $('.xWPASBTSearch').hide();
            $('.xWPASBTCancel').hide();
            $('.xWPASBTNext').hide();
            $('.xWPASBTReport').hide();
            $('.xWPASBTAddNew').hide();
        }

        $('#otaPASNote').attr('readonly', true);
        //$('.xWPASPlcCode').attr('disabled',true);
        $('#oetPASHHD').attr('readonly', true);
        // $('.xWPASBtnAddFormCodeToCode').attr('disabled',true); // Comsheer 2019 335
        $('.xWPASBTSearchHQ').attr('disabled', true);
        // $('.xCNBtnBrowseAddOn').attr('disabled',true); // Comsheer 2019 335
        $('#oetPASDocDate').attr('disabled', true);
        $('#ocbPASAdjType').attr('disabled', true);
    }

    //แบ่งหน้าตาม section
    switch (tTypePage) {
        case "1":
            JSxPASControlSubMenu(1);
            if (tDocNo != "" && tChkDateDT == 'FALSE') {
                $('.xWPASBTCancel').show();
            }
            $('.xCNImageInsert').removeClass('xCNBlockWhenApprove');
            $('#oetPASAddPdt').attr('disabled', false);

            if( tStaPrcDoc == '2' ){ // ถ้าอนุมัติใบย่อยไปแล้ว กลับมาดูใบย่อยต้องแก้ไขไม่ได้
                $('.xWDisabledOnApvSub').attr('disabled', true);
                $('.xWInputCanEdit').attr('disabled', true);
                $('.xWIconDelete').addClass('xCNBlockWhenApprove');
                $('.xCNImageInsert').addClass('xCNBlockWhenApprove');
                $('.xWDatepicker').attr('disabled', true);
                $('.xWTimepicker').attr('disabled', true);
            }else{
                $('.xWDisabledOnApvSub').attr('disabled', false);
                $('.xWInputCanEdit').attr('disabled', false);
                $('.xWIconDelete').removeClass('xCNBlockWhenApprove');
                $('.xCNImageInsert').removeClass('xCNBlockWhenApprove');
                $('.xWDatepicker').attr('disabled', false);
                $('.xWTimepicker').attr('disabled', false);
            }
            $('#oetPASDocDate').attr('readonly', true);
            break;
        case "2":
            JSxPASControlSubMenu(2);
            //ปิดปุ่มหน้า 1
            $('.xWPASBTSearchHQ').hide();
            $('.xWPASBTSearch').hide();
            $('.xWPASBTApprove').hide();
            $('#otaPASNote').attr('readonly', true);
            $('.xWPASBTSave').hide();
            $('.xWPASBTAddNew').hide();
            $('.xWPASBTReport').hide();
            $('.xWPASBTCancel').hide();
            $('.xWPASBTNext').show().attr('disabled', false);
            $('.xWPASBTPrevious').show();
            // $('#oetPASDocDate').attr('readonly', false);
            $('#oetPASDocDate').attr('readonly', true);
            $('.xWInputCanEdit').attr('disabled', false);
            $('.xWIconDelete').removeClass('xCNBlockWhenApprove');
            $('.xCNImageInsert').addClass('xCNBlockWhenApprove');
            $('#oetPASAddPdt').attr('disabled', true);
            $('#ocbPASAdjType').attr('disabled', true);

            //ถ้าใบย่อยติ๊กให้ปรับยอดสต๊อก ใบรวมต้องทำตาม Comsheet 2020-009
            var bAdjType = localStorage.getItem("bPASAdjType");
            if (bAdjType == 'true') {
                $('#ocbPASAdjType').attr('checked', true);
                // setTimeout(function(){
                //     localStorage.removeItem("bPASAdjType");
                // },1000);
            } else {
                $('#ocbPASAdjType').attr('checked', false);
            }

            if (tDocNo != "") {
                $('#oetPASDocDate').attr('readonly', true);
                // $('#ocbPASAdjType').attr('disabled',true);
            }
            break;
        case "3":
            JSxPASControlSubMenu(3);
            $('.xWPASBTSearchHQ').hide();
            $('#otaPASNote').attr('readonly', true);
            $('.xWPASBTApprove').hide();
            $('.xWPASBTSave').show();
            $('.xWPASBTReport').show().attr('disabled', true);
            $('.xWPASBTPrevious').show().attr('disabled', false);
            $('.xWPASBTNext').show().attr('disabled', true);
            $('#oetPASDocDate').attr('readonly', true);
            $('#ocbPASAdjType').attr('disabled', true);
            $('.xWInputCanEdit').attr('disabled', true);
            $('.xWPASBTCancel').show().attr('disabled', true);
            $('.xWIconDelete').addClass('xCNBlockWhenApprove');
            $('.xCNImageInsert').addClass('xCNBlockWhenApprove');
            $('#oetPASAddPdt').attr('disabled', true);
            if (tStaPrcDoc == '4') { //บันทึกเอกสารรวมแล้ว
                $('.xWPASBTSearchDO').show();
                $('.xWPASBTSearchAutoReceive').show();
                $('.xWPASBTReport').attr('disabled', false);
                $('.xWPASBTCancel').attr('disabled', false);
                $('.xWPASBTSave').attr('disabled', true);
                $('.xWPASBTSave').hide();
                $('.xWPASBTNext').attr('disabled', false);
            }
            break;
        case "4":
            JSxPASControlSubMenu(4);
            $('.xWPASBTSearchHQ').hide();
            $('#otaPASNote').attr('readonly', true);
            $('#oetPASDocDate').attr('readonly', true);
            $('#ocbPASAdjType').attr('disabled', true);
            $('.xWInputCanEdit').attr('disabled', true);
            $('.xWIconDelete').addClass('xCNBlockWhenApprove');
            $('.xCNImageInsert').addClass('xCNBlockWhenApprove');
            $('#oetPASAddPdt').attr('disabled', true);
            $('.xWPASBTApprove').show();
            $('.xWPASBTCancel').hide();
            $('.xWPASBTSave').hide();
            $('.xWPASBTNext').hide();
            $('.xWPASBTReport').show().attr('disabled', true);

            if (tStaPrcDoc == '1') {
                $('.xWPASBTSearch').show().attr('disabled', false);
                $('.xWPASBTReport').show().attr('disabled', false);
                $('.xWPASBTAddNew').show().attr('disabled', false);

                $('.xWPASBTApprove').hide();
                $('.xWPASBTPrevious').hide();

                $('.xWPASBTSearchDO').hide();
                $('.xWPASBTSearchAutoReceive').hide();
            }

            break;
    }
}

//Get Data And Insert Product
function JSxPASAddBrwPdtManual(oElem) {
    var aBrwDataPdt = JSON.parse(oElem);
    $('#oetPASPdtFromCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
    $('#oetPASPdtToCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
    JSvPASAddProduct('Pdt', '');
}

function JSxPASControlBrwPdt(oElem, tType) {
    var aBrwDataPdt = JSON.parse(oElem);
    if (aBrwDataPdt != null) {
        if (tType == '1') { //จากรหัส
            // $('#oetPASBarCodeFrom').val(aBrwDataPdt[0]['FTPdtBarCode']);//แสดงบารโค๊ด
            $('#oetPASPdtFromCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
            $('#oetPASPdtFromName').val(aBrwDataPdt[0]['FTPdtName']);
            if ($('#oetPASPdtToCode').val() == "") {
                // $('#oetPASBarCodeTo').val(aBrwDataPdt[0]['FTPdtBarCode']);
                $('#oetPASPdtToCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
                $('#oetPASPdtToName').val(aBrwDataPdt[0]['FTPdtName']);
            }
        } else { //ถึงรหัส
            // $('#oetPASBarCodeTo').val(aBrwDataPdt[0]['FTPdtBarCode']);
            $('#oetPASPdtToCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
            $('#oetPASPdtToName').val(aBrwDataPdt[0]['FTPdtName']);
            if ($('#oetPASPdtFromCode').val() == "") {
                // $('#oetPASBarCodeFrom').val(aBrwDataPdt[0]['FTPdtBarCode']);
                $('#oetPASPdtFromCode').val(aBrwDataPdt[0]['FTPdtBarCode']);
                $('#oetPASPdtFromName').val(aBrwDataPdt[0]['FTPdtName']);
            }
        }
    }
}

function JSxPASControlBrwSpl(oElem, tType) {
    var aBrwDataSpl = JSON.parse(oElem);
    if (aBrwDataSpl != null) {
        if (tType == '1') { //จากรหัส
            $('#oetPASPdtSupFromCode').val(aBrwDataSpl[0]['FTSplCode']);
            $('#oetPASPdtSupFromName').val(aBrwDataSpl[0]['FTSplName']);
            if ($('#oetPASPdtSupToCode').val() == "") {
                $('#oetPASPdtSupToCode').val(aBrwDataSpl[0]['FTSplCode']);
                $('#oetPASPdtSupToName').val(aBrwDataSpl[0]['FTSplName']);
            }
        } else { //ถึงรหัส
            $('#oetPASPdtSupToCode').val(aBrwDataSpl[0]['FTSplCode']);
            $('#oetPASPdtSupToName').val(aBrwDataSpl[0]['FTSplName']);
            if ($('#oetPASPdtSupFromCode').val() == "") {
                $('#oetPASPdtSupFromCode').val(aBrwDataSpl[0]['FTSplCode']);
                $('#oetPASPdtSupFromName').val(aBrwDataSpl[0]['FTSplName']);
            }
        }
    }
}

function JSxPASControlBrwUsr(oElem, tType) {
    var aBrwDataUsr = JSON.parse(oElem);
    if (aBrwDataUsr != null) {
        if (tType == '1') { //จากรหัส
            $('#oetPASUserFromCode').val(aBrwDataUsr[0]['FTUsrCode']);
            $('#oetPASUserFromName').val(aBrwDataUsr[0]['FTUsrName']);
            if ($('#oetPASUserToCode').val() == "") {
                $('#oetPASUserToCode').val(aBrwDataUsr[0]['FTUsrCode']);
                $('#oetPASUserToName').val(aBrwDataUsr[0]['FTUsrName']);
            }
        } else { //ถึงรหัส
            $('#oetPASUserToCode').val(aBrwDataUsr[0]['FTUsrCode']);
            $('#oetPASUserToName').val(aBrwDataUsr[0]['FTUsrName']);
            if ($('#oetPASUserFromCode').val() == "") {
                $('#oetPASUserFromCode').val(aBrwDataUsr[0]['FTUsrCode']);
                $('#oetPASUserFromName').val(aBrwDataUsr[0]['FTUsrName']);
            }
        }
    }
}

function JSxPASControlBrwGroup(oElem, tType) {
    var aBrwDataGrp = JSON.parse(oElem);
    $('#oetPASGrpPdtCode').val(aBrwDataGrp[0]['FTPgpChain']);
    $('#oetPASGrpPdtName').val(aBrwDataGrp[0]['FTPgpChainName']);
}

function JSxPASControlBrwLoc(oElem, tType) {
    var aBrwDataLoc = JSON.parse(oElem);
    $('#oetPASLocCode').val(aBrwDataLoc[0]['FTPlcCode']);
    $('.xWPASLocName').text(aBrwDataLoc[0]['FTPlcName']);
}

function JSxPASAlertMessage(aDataMesage) {
    JSxContentLoader('hide');

    setTimeout(function() {
        // $('#odvPASModalAlertMessage').modal('hide');
        // $('.modal-backdrop').remove();
        $('#odvPASModalAlertMessage').modal('show');
        $('.xWPASModalAlertMessageBody').show();
        $('.xWPASModalAlertMessageBodyInput').hide();
        $('.xWPASModalAlertMessageHead').html(aDataMesage['tHead']);
        $('.xWPASModalAlertMessageBody').html(aDataMesage['tDetail']);

        //tHead,tDetail,nType
        // tType 1 = Alert Confirm
        // tType 2 = Message Alert
        // tType 3 = Confirm Input Alert
        switch (aDataMesage['nType']) {
            case 1:
                $('#odvPASModalAlertMessage').find('.modal-dialog').css('width', '');
                $('.xWPASConfirmAlertMessage').show();
                $('#odvPASModalAlertMessage').off('keyup');
                $('#odvPASModalAlertMessage').on('keyup', function(e) {
                    if (e.keyCode === 13) {
                        $('.xWPASConfirmAlertMessage').click();
                    }
                });
                // $('.xWPASConfirmAlertMessage').off('click');
                // $('.xWPASConfirmAlertMessage').on('click',function(){
                //     $('#odvPASModalAlertMessage').modal('hide');
                //     //$('.modal-backdrop').remove();
                // });
                break;
            case 2:
                $('#odvPASModalAlertMessage').find('.modal-dialog').css('width', '');
                $('.xWPASConfirmAlertMessage').hide();
                $('#odvPASModalAlertMessage').off('keyup');
                $('#odvPASModalAlertMessage').on('keyup', function(e) {
                    if (e.keyCode === 13) {
                        $('.xWODSCloseAlertMessage').click();
                    }
                });
                break;
            case 3:
                $('#oetPASModalInput').val('');
                $('#odvPASModalAlertMessage').find('.modal-dialog').css('width', '350px');
                $('.xWPASModalAlertMessageBody').hide();
                $('.xWPASModalAlertMessageBodyInput').show();
                $('.xWPASConfirmAlertMessage').show();
                setTimeout(function() {
                    $('#oetPASModalInput').focus();
                }, 500);
                $('#odvPASModalAlertMessage').off('keyup');
                $('#odvPASModalAlertMessage').on('keyup', function(e) {
                    if (e.keyCode === 13) {
                        $('.xWPASConfirmAlertMessage').click();
                    }
                });
                break;
        }
    }, 500);
}

function JSxPASControlBrwPdtEditInLine(poElem, pnSeq) {
    // console.log(oElem);
    // console.log(pnSeq);
    var aBrwDataPdt = JSON.parse(poElem);
    // console.log(aBrwDataPdt[0]['FTPdtCode']);

    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    // var aPASLoc = []
    // $("input[name='orbPASLocation[]']:checked").each(function (){
    //     aPASLoc.push($(this).val());
    // });

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASChangeProduct",
        data: {
            ptDocNo: tDocNo,
            ptPdtCode: aBrwDataPdt[0]['FTPdtCode'],
            pnSeq: pnSeq
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
            // $('#oetPAS'+ptTab+'FromCode').val('');
            // $('#oetPAS'+ptTab+'ToCode').val('');
            JSvPASCallDataTable();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSbPASCheckConfirmCode(ptPass) {
    var bReturn = false;
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    if (ptPass == "" || ptPass === undefined) {
        var aPlsEnterPsw = {
            tHead: $('#oetPASHeadAlert').val(),
            tDetail: $('#oetPASTextPlsEnterPass').val(),
            nType: 2
        };
        setTimeout(function() {
            JSxPASAlertMessage(aPlsEnterPsw);
        }, 500);
        return false;
    } else {
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASCheckConfirmCode",
            data: {
                ptDocNo: tDocNo,
                ptPass: ptPass
            },
            cache: false,
            timeout: 0,
            async: false,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                // console.log(aReturn);
                if (aReturn['nStaQuery'] == 1) {
                    bReturn = true;
                } else {
                    bReturn = false;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    }
    return bReturn;
}

function JSxPASNextStep(paTextNextStep) {
    JSxPASAlertMessage(paTextNextStep);
    $('.xWPASConfirmAlertMessage').off('click');
    $('.xWPASConfirmAlertMessage').on('click', function() {
        JSxPASNextStepConfirmCode();
    });
}

function JSxPASNextStepConfirmCode(ptStep) {
    var aTextConCode = {
        tHead: $('#oetPASHeadConfirmCode').val(),
        tDetail: '',
        nType: 3
    };
    setTimeout(function() {
        JSxPASAlertMessage(aTextConCode);
        $('.xWPASConfirmAlertMessage').off('click');
        $('.xWPASConfirmAlertMessage').on('click', function() {
            JSxContentLoader('show');
            switch (ptStep) {
                case "3":
                    if ($('#oetPASModalInput').val() == $('#oetPASPassword').val()) {
                        JSxPASAddEditHD();
                    } else {
                        JSxPASShowMsgWrongPassword(ptStep);
                    }
                    break;
                case "Cancel":
                    if (JSbPASCheckConfirmCode($('#oetPASModalInput').val()) === true) {
                        JSxPASCancelHD();
                    } else {
                        JSxPASShowMsgWrongPassword(ptStep);
                    }
                    break;
                case "Approve":
                    JSxContentLoader('hide');
                    if (JSbPASCheckConfirmCode($('#oetPASModalInput').val()) === true) {
                        JSxPASApprove();
                    } else {
                        JSxPASShowMsgWrongPassword(ptStep);
                    }
                    break;
                default:
                    //บันทึกใบรวม
                    if (JSbPASCheckConfirmCode($('#oetPASModalInput').val()) === true) { //ตรวจสอบรหัสผ่าน
                        var tDocNo = $('#oetPASDocNo').val();
                        localStorage.setItem("bPASAdjType", $('#ocbPASAdjType').prop("checked"));
                        if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
                        setTimeout(function() {
                            $.ajax({
                                type: "POST",
                                url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASMergeSTK",
                                data: {
                                    ptDocNo: tDocNo,
                                    ptPassword: $('#oetPASModalInput').val(),
                                    pbAdjType: $('#ocbPASAdjType').prop("checked")
                                },
                                cache: false,
                                timeout: 0,
                                async: false,
                                success: function(oResult) {
                                    var aReturn = JSON.parse(oResult);
                                    // console.log(aReturn);
                                    if (aReturn['nStaQuery'] == 1) {
                                        $('#oetPASDocNoPrevious').val(tDocNo);
                                        $('#oetPASPassword').val($('#oetPASModalInput').val());
                                        $('#oetPASDocNo').val(''); // เคลียร์ DocNo
                                        JSvPASCallPageMain('', 2); //เรียกหน้า รวมเอกสารตรวจนับสินค้า
                                    } else if (aReturn['nStaQuery'] == 42000) {
                                        JSxContentLoader('hide');
                                        JCNxDisplayErrorSQL(aReturn['aResultAll']);
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                                }
                            });
                        }, 500);
                    } else {
                        JSxPASShowMsgWrongPassword();
                    }
                    break;
            }
        });
    }, 500);
}

function JSxPASShowMsgWrongPassword(ptStep) {
    var aTextWrongPass = {
        tHead: $('#oetPASHeadWrongPass').val(),
        tDetail: $('#oetPASTextWrongPass').val(),
        nType: 2
    }
    setTimeout(function() {
        JSxPASAlertMessage(aTextWrongPass);
        $('.xWPASConfirmAlertMessage').off('click');
        $('.xWPASConfirmAlertMessage').on('click', function() {
            if (ptStep != "") {
                JSxPASNextStepConfirmCode(ptStep);
            } else {
                JSxPASNextStepConfirmCode();
            }
        });
    }, 500);
}

function JSxPASSearchPdtChkHD(poElem) {
    var aBrwDataPdt = JSON.parse(poElem);
    JSvPASCallPageMain(aBrwDataPdt[0]['FTIuhDocNo']);
}

//ตรวจสอบยกยอดมาสิ้นเดือน
function JSbPASCheckMonthEnd() {
    var bReturn = true;
    var tDocNo  = $('#oetPASDocNo').val();
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASCheckMonthEnd",
        data: {
            FTIuhDocNo : tDocNo,
        },
        cache: false,
        timeout: 0,
        async: false,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            if (aReturn['nStaQuery'] == 1) {
                bReturn = true;
            } else {
                bReturn = false;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });

    return bReturn;
}

function JSxPASApprove() {
    if (JSbPASCheckMonthEnd()) {
        $('#odvPASModalApprove').modal('show');

        $('#odvPASModalApprove').off('keyup');
        $('#odvPASModalApprove').on('keyup', function(e) {
            if (e.keyCode === 13) {
                $('.xWPASModalConfirmApv').click();
            }
        });

        $('.xWPASModalConfirmApv').off('click');
        $('.xWPASModalConfirmApv').on('click', function() {
            var tDocNo = $('#oetPASDocNo').val();

            // var aParams = {
            //     'MQApprove' : 'PDTADJSTKCHK',
            //     'MQDelete'  : 'PDTADJSTKCHKDEL', //PDTADJSTKCHKDEL //ExportServiceDelete
            //     'params'    : {
            //         'ptDocNo'           : tDocNo,
            //         'ptDocName'         : 'PDTADJSTKCHK',
            //         'ptRouteRollBack'   : ''
            //         // 'ptRouteOther'      : $('#oetRabbitOrderingScreenUpdateApprove').val(),
            //         // 'ptRouteSuccess'    : $('#oetODSRouteSuccessApprove').val()
            //     },
            //     'tType'     : 'backgroundprocess'
            // };

            // $.ajax({
            //     type: "POST",
            //     url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASApprove",
            //     data: {
            //         ptDocNo     : tDocNo,
            //         paParams    : aParams
            //     },
            //     cache: false,
            //     timeout: 0,
            //     success: function(oResult){
            //         console.log(oResult);
            $('#odvPASModalApprove').modal('hide');
            var paParams = {
                'MQApprove': 'PDTADJSTKCHK',
                'MQDelete': 'PDTADJSTKCHKDEL', //PDTADJSTKCHKDEL //ExportServiceDelete
                'params': {
                    'ptDocNo': tDocNo,
                    'ptDocName': 'PDTADJSTKCHK',
                    'ptRouteRollBack': ''
                        // 'ptRouteOther'      : $('#oetRabbitOrderingScreenUpdateApprove').val(),
                        // 'ptRouteSuccess'    : $('#oetODSRouteSuccessApprove').val()
                },
                'tType': 'backgroundprocess'
            };
            SubcribeToRabbitMQ(paParams);

            $('#osmConfirmRabbit').off("click");
            $('#osmConfirmRabbit').on("click", function(event) {
                setTimeout(function() {
                    JSvPASCallPageMain(tDocNo, 4);
                    // $.ajax({
                    //     type: "POST",
                    //     url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASUpdStaExport",
                    //     data: {
                    //         ptDocNo: tDocNo,
                    //     },
                    //     cache: false,
                    //     timeout: 0,
                    //     success: function() {
                            setTimeout(function() {
                                JSxPASCallStimulReport();
                            }, 500);
                    //     },
                    //     error: function(jqXHR, textStatus, errorThrown) {
                    //         console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                    //     }
                    // });

                }, 500);
            });

            //     },
            //     error: function(jqXHR, textStatus, errorThrown) {
            //         console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            //     }
            // });
        });
    } else {
        setTimeout(function() {
            var dDate = new Date();
            var month = [];
            month[0] = "มกราคม";
            month[1] = "กุมภาพันธ์";
            month[2] = "มีนาคม";
            month[3] = "เมษายน";
            month[4] = "พฤษภาคม";
            month[5] = "มิถุนายน";
            month[6] = "กรกฏาคม";
            month[7] = "สิงหาคม";
            month[8] = "กันยายน";
            month[9] = "ตุลาคม";
            month[10] = "พฤศจิกายน";
            month[11] = "ธันวาคม";
            var tMonth = month[dDate.getMonth()];

            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            var lastDayWithSlashes = ((lastDay.getDate() > 9) ? lastDay.getDate() : ('0' + lastDay.getDate())) + '/' + ((lastDay.getMonth() > 8) ? (lastDay.getMonth() + 1) : ('0' + (lastDay.getMonth() + 1))) + '/' + lastDay.getFullYear();
            var firstDayWithSlashes = ((firstDay.getDate() > 9) ? firstDay.getDate() : ('0' + firstDay.getDate())) + '/' + ((firstDay.getMonth() > 8) ? (firstDay.getMonth() + 1) : ('0' + (firstDay.getMonth() + 1))) + '/' + firstDay.getFullYear();

            JSxPASAlertMessage(aModalText = {
                tHead: 'แจ้งเตือน',
                tDetail: 'ยังไม่มีการประมวลผลยอดยกมาสิ้นเดือน. คุณต้องประมวลผลยอดยกมาสิ้นเดือนก่อน<br>รอบบัญชีปัจจุบัน : ' + tMonth + ' ' + firstDayWithSlashes + ' - ' + lastDayWithSlashes,
                nType: 2
            });
        }, 500);
    }
}

function JSxPASCallStimulReport() {
    var tDocumentID = $('#oetPASDocNo').val();
    var tCompCode = $('#oetPASCmpCode').val();
    var tUrl = $('#oetPASBaseURL').val();
    var tCallType = $('#odvCNTCallType').val();
    var tParameter = $('#odvCNTParameter').val();

    $('#ohdRptDocNo').val(tDocumentID);
    $('#ofmPASB4View').attr('action', tUrl + '?route=rptAllPdtPhysicalChkStk&calltype=' + tCallType + '&Param=' + tParameter);
    $('#ofmPASB4View').submit();
    $('#ofmPASB4View').attr('action', 'javascript:void(0)');

    // var aInfor = [
    //     {"SP_nLang":'1'},                // ภาษา
    //     {"SP_tCompCode":tCompCode},          // รหัสบริษัท
    //     {"SP_tDocNo":tDocumentID},        // เลขที่เอกสาร
    //     {"SP_DocName":"ChkStkReport"}   // ชื่อเอกสาร
    // ];
    // window.open(tUrl + "formreport/ReportFamily?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
}

function JSxPASCallSearchHQ(ptSearch, pnPageCurrent) {
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    if (ptSearch == "" || ptSearch === undefined) { ptSearch = "NULL"; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASCallSearchHQ",
        data: {
            ptSearch: ptSearch,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            $('.xWPASModalSearchTitle').html('ค้นหา(HQ)');
            $('#odvPASModalSearchHD').data('searchtype', "searchHQ");
            $('#odvPASModalSearchHD').attr('data-searchtype', "searchHQ");
            $('#odvPASModalSearchHD').html(aReturn['tHTML']);
            if (aReturn['tFirtItem'] != "NULL") {
                JSxPASCallSearchHQList(aReturn['tFirtItem']);
            } else {
                JSxPASCallSearchHQList();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASCallSearchHQList(ptPdtCylCntNo, pnPageCurrent) {
    if (ptPdtCylCntNo == "" || ptPdtCylCntNo === undefined) { ptPdtCylCntNo = 'NULL'; }
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSvCPASCallSearchHQList",
        data: {
            ptPdtCylCntNo: ptPdtCylCntNo,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASModalSearchDT').html(tResult);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASAddDataFromSearchHQ() {
    var aPASLoc = [];
    var tCylCntNo = $('#otbTableSearchHQ').find('.xWSchHQSelected').data('pdtcylcntno');
    $("input[name='orbPASLocation[]']:checked").each(function() {
        aPASLoc.push($(this).val());
    });
    if (aPASLoc != '') {
        JSxContentLoader('show');
        $.ajax({
            type: "POST",
            url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASAddDataFromSearchHQ",
            data: {
                paPlcCode: aPASLoc,
                ptPdtCylCntNo: $('#otbTableSearchHQ').find('.xWSchHQSelected').data('pdtcylcntno')
            },
            cache: false,
            timeout: 0,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                switch (aReturn['nStaQuery']) {
                    case 1:
                        $('.xWPASBTSearchHQ').attr('disabled', true);
                        JSvPASCallDataTable();
                        $('#oetIuhRefTaxOver').val(tCylCntNo);
                        // console.log(tCylCntNo);
                        break;
                    case 2: //พบสินค้าที่ตรวจนับไม่ได้
                        var nCountItems = aReturn['aItems'].length;
                        var tText = '';
                        var tTitle = '99';
                        var tTitleCur = '98';
                        //loop สินค้นเอาไปต่อกันเป็น string
                        tText += '<ul class="list-group">';
                        for (var i = 0; i < nCountItems; i++) {

                            // if (aReturn['aItems'][i]['FTPdtStaAudit'] == '2' && aReturn['aItems'][i]['FTStaBarCode'] == '1' && aReturn['aItems'][i]['FTStaPunCode'] == '1') {
                            //     tTitle = 'พบสินค้าที่ตรวจนับไม่ได้';
                            // } else if (aReturn['aItems'][i]['FTPdtStaAudit'] == '1' && aReturn['aItems'][i]['FTStaBarCode'] == '2' && aReturn['aItems'][i]['FTStaPunCode'] == '1') {
                            //     tTitle = 'พบสินค้าที่ไม่มีบาร์โค้ด';
                            // } else if (aReturn['aItems'][i]['FTPdtStaAudit'] == '1' && aReturn['aItems'][i]['FTStaBarCode'] == '1' && aReturn['aItems'][i]['FTStaPunCode'] == '2') {
                            //     tTitle = 'พบสินค้าที่ไม่มีหน่วย';
                            // } else if (aReturn['aItems'][i]['FTPdtStaAudit'] == '2' && aReturn['aItems'][i]['FTStaBarCode'] == '2' && aReturn['aItems'][i]['FTStaPunCode'] == '1') {
                            //     tTitle = 'พบสินค้าที่ตรวจนับไม่ได้ และไม่มีบาร์โค้ด';
                            // } else if (aReturn['aItems'][i]['FTPdtStaAudit'] == '1' && aReturn['aItems'][i]['FTStaBarCode'] == '2' && aReturn['aItems'][i]['FTStaPunCode'] == '2') {
                            //     tTitle = 'พบสินค้าที่ไม่มีบาร์โค้ด และไม่มีหน่วย';
                            // } else {
                            //     tTitle = 'พบสินค้าที่ตรวจนับไม่ได้ ไม่มีบาร์โค้ด และไม่มีหน่วย';
                            // }
                            if( aReturn['aItems'][i]['FTStaPdtCode'] == '2' ){
                                tTitle = 'พบสินค้าที่ไม่มีในระบบ';
                            } else if (aReturn['aItems'][i]['FTStaBarCode'] == '2' && aReturn['aItems'][i]['FTStaPunCode'] == '1') {
                                tTitle = 'พบสินค้าที่ไม่มีบาร์โค้ด';
                            } else if (aReturn['aItems'][i]['FTStaBarCode'] == '1' && aReturn['aItems'][i]['FTStaPunCode'] == '2') {
                                tTitle = 'พบสินค้าที่ไม่มีหน่วย';
                            } else if (aReturn['aItems'][i]['FTStaBarCode'] == '2' && aReturn['aItems'][i]['FTStaPunCode'] == '2') {
                                tTitle = 'พบสินค้าที่ไม่มีบาร์โค้ด และไม่มีหน่วย';
                            } else {
                                tTitle = 'อื่นๆ';
                            }

                            if (tTitleCur != tTitle) {
                                if (i != 0) {
                                    tText += '</ul><ul class="list-group">';
                                }
                                tTitleCur = tTitle;
                                tText += '<li class="list-group-item list-group-item-danger">' + tTitle + '</li>';
                            }

                            tText += '<li class="list-group-item list-group-item-warning">' + aReturn['aItems'][i]['FTPdtCode'] + "&nbsp;&nbsp;&nbsp;" + aReturn['aItems'][i]['FTPdtBarCode'] + "&nbsp;&nbsp;&nbsp;" + aReturn['aItems'][i]['FTPdtName'] + "</li>";
                        }
                        tText += '</ul>';

                        //แสดง alert message
                        JSxPASAlertMessage(aModalText = {
                            tHead: 'แจ้งเตือน', //พบสินค้าที่ตรวจนับไม่ได้
                            tDetail: tText,
                            nType: 2
                        });
                        //load DataTable แสดงสินค้า
                        $("#odvPASModalAlertMessage").on('hide.bs.modal', function() {
                            $('.xWPASBTSearchHQ').attr('disabled', true);
                            JSvPASCallDataTable();
                            $('#oetIuhRefTaxOver').val(tCylCntNo);
                        });

                        break;
                    default:
                        JSxPASAlertMessage(aModalText = {
                            tHead: 'Error',
                            tDetail: aReturn['tStaMessage'],
                            nType: 2
                        });
                        break;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
            }
        });
    } else {
        // alert('ไม่พบข้อมูล คลัง/ที่เก็บ (Location)')
        $('#odvPASModalAlertMessage').modal('show');
        $('.xWPASModalAlertMessageBody').show();
        $('.xWPASModalAlertMessageBodyInput').hide();
        $('.xWPASConfirmAlertMessage').hide();
        $('.xWPASModalAlertMessageHead').html();
        $('.xWPASModalAlertMessageBody').html('ไม่พบข้อมูล คลัง/ที่เก็บ (Location)');
    }

}

function JSxPASSearchHQClickPage(ptPage, ptType) {
    var nPage = 10;
    if (ptType == "HQ") {
        var nPageOld = $('.xWPageSearchHQ .active').text();
        var nPageNew = parseInt(nPageOld, nPage) / nPage + 1;
        var nPageCurrent = nPageNew;
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, nPage) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, nPage) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSxPASCallSearchHQ('', nPageCurrent);
        $('#oetPASCurrentPageSearchHQ').val(nPageCurrent);
    } else {
        var tPdtCylCntNo = $('#otbTableSearchHQ').find('.xWSchHQSelected').data('pdtcylcntno')
        var nPageOld = $('.xWPageSearchHQList .active').text();
        var nPageNew = parseInt(nPageOld, nPage) / nPage + 1;
        var nPageCurrent = nPageNew;
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, nPage) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, nPage) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSxPASCallSearchHQList(tPdtCylCntNo, nPageCurrent);
        $('#oetPASCurrentPageSearchHQList').val(nPageCurrent);
    }
}

function JSxPASCallSearchHD(pnPageCurrent, ptSearch) {
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    if (ptSearch == "" || ptSearch === undefined) { ptSearch = "NULL"; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASCallSearchHD",
        data: {
            ptSearch: ptSearch,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            $('.xWPASModalSearchTitle').html('ค้นหาเอกสาร');
            $('#odvPASModalSearchHD').data('searchtype', "search");
            $('#odvPASModalSearchHD').attr('data-searchtype', "search");
            $('#odvPASModalSearchHD').html(aReturn['tHTML']);
            if (aReturn['tFirtItem'] != "NULL") {
                JSxPASCallSearchDT(aReturn['tFirtItem'], 1);
            } else {
                JSxPASCallSearchDT();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASCallSearchDT(ptIuhDocNo, pnPageCurrent) {
    if (ptIuhDocNo == "" || ptIuhDocNo === undefined) { ptIuhDocNo = 'NULL'; }
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSvCPASCallSearchDT",
        data: {
            ptIuhDocNo: ptIuhDocNo,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASModalSearchDT').html(tResult);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

function JSxPASSearchClickPage(ptPage, ptType) {
    var nPage = 10;
    if (ptType == "HD") {
        var nPageOld = $('.xWPageSearchHD .active').text();
        var nPageNew = parseInt(nPageOld, nPage) / nPage + 1;
        var nPageCurrent = nPageNew;
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, nPage) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, nPage) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSxPASCallSearchHD(nPageCurrent, '');
        $('#oetPASCurrentPageSearchHD').val(nPageCurrent);
    } else {
        var tIuhDocNo = $('#otbTableSearch').find('.xWSchHQSelected').data('docno');
        var nPageOld = $('.xWPageSearchDT .active').text();
        var nPageNew = parseInt(nPageOld, nPage) / nPage + 1;
        var nPageCurrent = nPageNew;
        switch (ptPage) {
            case 'next':
                $('.xWBtnNext').addClass('disabled');
                nPageNew = parseInt(nPageOld, nPage) + 1;
                nPageCurrent = nPageNew;
                break;
            case 'previous':
                nPageNew = parseInt(nPageOld, nPage) - 1;
                nPageCurrent = nPageNew;
                break;
            default:
                nPageCurrent = ptPage;
        }
        JSxPASCallSearchDT(tIuhDocNo, nPageCurrent);
        $('#oetPASCurrentPageSearchDT').val(nPageCurrent);
    }
}

//ค้นหาสินค้า
function JSvPASSearchProduct(paPackData, ptTabActive) {
    var tDocNo = $('#oetPASDocNo').val();
    if (tDocNo == "" || tDocNo === undefined) { tDocNo = ' '; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSxCPASSearchProduct",
        data: {
            FTIuhDocNo: tDocNo,
            FTIudBarCode: paPackData['FTIudBarCode'],
            ptPageType: paPackData['ptPageType'],
            ptFilter: paPackData['ptFilter'],
            ptTabActive: ptTabActive
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            // console.log(aReturn);
            if (aReturn['nStaQuery'] == 1) {
                var nPage = Math.ceil(aReturn['aItems']['RowIDItems'] / 20);
                $('#oetSearchItems').val(''); //Clear Inputs
                if (ptTabActive == "PDTCHK") {
                    JSvPASCallDataTable(nPage, paPackData['ptPageType'], aReturn['aItems']['RowIDItems']);
                } else {
                    JSvPASCallDataPdtWithOutSystemTable(nPage, paPackData['ptPageType'], aReturn['aItems']);
                }
            } else {
                JSxPASAlertMessage(aModalText = {
                    tHead: 'ไม่พบข้อมูล',
                    tDetail: 'ไม่พบข้อมูล',
                    nType: 2
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function : ค้นหาเอกสาร D/O (HD)
// Create By: Napat(Jame) 05/08/2020
function JSxPASCallSearchDO(ptSearch, pnPageCurrent) {
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    if (ptSearch == "" || ptSearch === undefined) { ptSearch = "NULL"; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASCallSearchDO",
        data: {
            ptDocNo: $('#oetPASDocNo').val(),
            ptSearch: ptSearch,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            $('.xWPASModalSearchTitle').html('เอกสาร D/O');
            $('#odvPASModalSearchHD').data('searchtype', "searchDO");
            $('#odvPASModalSearchHD').attr('data-searchtype', "searchDO");
            $('#odvPASModalSearchHD').html(aReturn['tHTML']);
            if (aReturn['tFirtItem'] != "NULL") {
                JSxPASCallSearchDOList(aReturn['tFirtItem']);
            } else {
                JSxPASCallSearchDOList();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function : ค้นหาเอกสาร D/O (DT)
// Create By: Napat(Jame) 05/08/2020
function JSxPASCallSearchDOList(ptDocNo, pnPageCurrent) {
    if (ptDocNo == "" || ptDocNo === undefined) { ptDocNo = 'NULL'; }
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSvCPASCallSearchDOList",
        data: {
            ptDocNo: ptDocNo,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASModalSearchDT').html(tResult);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function   : เพิ่มการอ้างอิงเอกสาร D/O and Auto Receive
// Create By  : Napat(Jame) 06/08/2020
// Parameters : 1 = D/O , 2 = Auto Receive
function JSxPASEventAddDocRef(pnRefType) {
    let tDocRef = "";
    $('.xWItemLists').each(function() {
        if ($(this).is(':checked') === true) {
            let tSymbol = "";
            if (tDocRef != "") {
                tSymbol = ",";
            }
            tDocRef += tSymbol + "'" + $(this).data('docno') + "'";
        }
    });

    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASEventAddUpdDocRef",
        data: {
            ptDocRef: tDocRef,
            ptDocNo: $('#oetPASDocNo').val(),
            pnRefType: pnRefType
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            if (aReturn['nStaQuery'] == 1) {
                var nPageType = $('#oetPASTypePage').val();
                JSvPASCallDataTable('', nPageType);
            } else {
                JSxPASAlertMessage(aModalText = {
                    tHead: '',
                    tDetail: aReturn['tStaMessage'],
                    nType: 2
                });
            }
            // console.log(aReturn);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function : ค้นหาเอกสารอ้างอิง Auto Receive (HD)
// Create By: Napat(Jame) 07/08/2020
function JSxPASCallSearchAutoReceive(ptSearch, pnPageCurrent) {
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    if (ptSearch == "" || ptSearch === undefined) { ptSearch = "NULL"; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSaCPASCallSearchAutoReceive",
        data: {
            ptDocNo: $('#oetPASDocNo').val(),
            ptSearch: ptSearch,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(oResult) {
            var aReturn = JSON.parse(oResult);
            $('.xWPASModalSearchTitle').html('เอกสาร Auto Receive');
            $('#odvPASModalSearchHD').data('searchtype', "searchAutoReceive");
            $('#odvPASModalSearchHD').attr('data-searchtype', "searchAutoReceive");
            $('#odvPASModalSearchHD').html(aReturn['tHTML']);
            if (aReturn['tFirtItem'] != "NULL") {
                JSxPASCallSearchAutoReceiveList(aReturn['tFirtItem']);
            } else {
                JSxPASCallSearchAutoReceiveList();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function : ค้นหาเอกสารอ้างอิง Auto Receive (DT)
// Create By: Napat(Jame) 07/08/2020
function JSxPASCallSearchAutoReceiveList(ptDocNo, pnPageCurrent) {
    if (ptDocNo == "" || ptDocNo === undefined) { ptDocNo = 'NULL'; }
    if (pnPageCurrent == "" || pnPageCurrent === undefined) { pnPageCurrent = 1; }
    $.ajax({
        type: "POST",
        url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSvCPASCallSearchAutoReceiveList",
        data: {
            ptDocNo: ptDocNo,
            nPageCurrent: pnPageCurrent
        },
        cache: false,
        timeout: 0,
        success: function(tResult) {
            $('#odvPASModalSearchDT').html(tResult);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
        }
    });
}

// Function : คลิกเลื่อนหน้ารายการสินค้า Doc Ref
// Create By: Napat(Jame) 10/08/2020
function JSxPASSearchDocRefClickPage(ptPage, ptType) {
    var nPage = 10;
    var tDocNo = $('#otbTableSearchDocRef').find('.xWSchHQSelected').data('docno')
    var nPageOld = $('.xWPageSearchDocRefList .active').text();
    var nPageNew = parseInt(nPageOld, nPage) / nPage + 1;
    var nPageCurrent = nPageNew;
    switch (ptPage) {
        case 'next':
            $('.xWBtnNext').addClass('disabled');
            nPageNew = parseInt(nPageOld, nPage) + 1;
            nPageCurrent = nPageNew;
            break;
        case 'previous':
            nPageNew = parseInt(nPageOld, nPage) - 1;
            nPageCurrent = nPageNew;
            break;
        default:
            nPageCurrent = ptPage;
    }
    if (ptType == 'D/O') {
        JSxPASCallSearchDOList(tDocNo, nPageCurrent);
    } else {
        JSxPASCallSearchAutoReceiveList(tDocNo, nPageCurrent);
    }
    $('#oetPASCurrentPageSearchDocRefList').val(nPageCurrent);
}