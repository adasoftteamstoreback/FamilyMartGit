<script>    

    //New Edit inline
    $( document ).ready(function() {
        $('.xWSuggestorderPDTCode').click(function() { 
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
    });

    //Edit inline - case สินค้า
    $('.inputs').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode === 13){
            var nSeq = $(this).parents('tr').data('seq');
            JSUpdateEditinline(nSeq,'');
            //$(this).closest('td').nextAll().eq(2).find('.inputsChange').focus();
        }
    });

    //Edit inline - case สินค้า
    $('.inputs').on("focusout",function(e){
        var nSeq = $(this).parents('tr').data('seq');
        JSUpdateEditinline(nSeq,'');
        e.preventDefault();

    });

    //Edit inline - case ปฎิทิน
    var tTypeModalDuplicate = false;
    var tTypeSpcNotApprove  = false;
    var tDateStartOld       = '';
    var tDateEndOld         = '';
    var tTypeClick          = '';
    var tTypespecialClick   = '';

    $(".inputsChange").focus(function() { 
        tTypeModalDuplicate = false;
    }); 

    $('.inputsChange').on("focusout keydown keyup",function(e){
        if(e.type == "focusout") {
            //กดออก
            if(tTypeModalDuplicate == false){
                var nSeq            = $(this).parents('tr').data('seq');
                var tID             = $(this).attr('id');

                var tSearchEnd = tID.search("Enddate");
                if(tSearchEnd == -1){
                    
                    //วันที่เริ่มต้น
                    var tStartDate      = $('#oetStartdate'+nSeq).val();
                    if(tStartDate == '' || tStartDate == null){
                        var tTextwarning        = "<?=language('common/systems', 'tModalTextDatalessCurrent')?>";
                        $('.xCNHeadTextModalDataDuplicate').text('<?=language('common/systems', 'tModalHeadDataFormatDateError')?>');
                        $('#odvModalDataDuplicate').modal('show');
                        $('.xCNTextModalDataDuplicate').text(tTextwarning);
                        tTypeModalDuplicate = true;
                        $('.inputsChange').blur();

                        var tCurrentDate    = new Date();
                        var dd              = String(tCurrentDate.getDate()).padStart(2, '0');
                        var mm              = String(tCurrentDate.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy            = tCurrentDate.getFullYear();
                        tCurrentDateShow    = dd + '-' + mm + '-' + yyyy;
                        $('#oetStartdate'+nSeq).val(tCurrentDateShow);

                        //Modal กด Enter
                        tTypeSpcNotApprove = true;
                        $('#odvModalDataDuplicate').on('keydown', function ( e ) {
                            var keyCode = e.keyCode || e.which; 
                            if(keyCode === 13){
                                $('#oetStartdate'+nSeq).datepicker("setDate", new Date(yyyy,mm-1,dd) );
                                $('#obtFoundDatainTable').click();
                                $('#oetStartdate'+nSeq).focus();
                            }
                        });

                        $('#obtFoundDatainTable').on('click', function ( e ) {
                            $('#oetStartdate'+nSeq).datepicker("setDate", new Date(yyyy,mm-1,dd) );
                            $('#oetStartdate'+nSeq).val(tCurrentDateShow);
                            $('#oetStartdate'+nSeq).focus();
                        });
                    }else{
                        tTypeSpcNotApprove = false;
                        var tStart =  $('#oetStartdate'+nSeq).val();
                        $('#oetStartdate'+nSeq).val(tStart);
                    }

                    setTimeout(function(){
                        tDateStartOld = tStart;
                        JSUpdateEditinline(nSeq,tID);
                    }, 500);
                }else{
                    //วันที่สิ้นสุด
                    tTypeSpcNotApprove = false;
                    var tEndDate      = $('#oetEnddate'+nSeq).val();
                    if(tEndDate == '' || tEndDate == null){
                        //สำหรับกรอกวันที่น้อยกว่า วันที่ปัจจุบัน ให่้มันเท่ากับ วันที่เริ่มต้น
                        var tStartDate      = $('#oetStartdate'+nSeq).val();
                        $('#oetEnddate'+nSeq).val(tStartDate);
                        var tResultStartDate = tStartDate.split("-");
                        $('#oetEnddate'+nSeq).datepicker("setDate", new Date(tResultStartDate[2],tResultStartDate[1] - 1,tResultStartDate[0]) );
                        JSUpdateEditinline(nSeq,tID);
                    }else{
                        //สำหรับเลือกวันที่น้อยกว่า วันที่เริ่มต้น ให่้มันเท่ากับ วันที่เริ่มต้น
                        setTimeout(function(){
                            var tResultStartDate = $('#oetStartdate'+nSeq).val().split("-");
                            var tResultStartDateS = tResultStartDate[2]+'-'+tResultStartDate[1]+'-'+tResultStartDate[0];
                            
                            var tResultEndDate = $('#oetEnddate'+nSeq).val().split("-");
                            var tResultEndDateS = tResultEndDate[2]+'-'+tResultEndDate[1]+'-'+tResultEndDate[0];

                            var tConvertStartDate     = new Date(tResultStartDateS).setHours(0,0,0,0);
                            var tConvertEndDate       = new Date(tResultEndDateS).setHours(0,0,0,0);

                            if(tConvertEndDate < tConvertStartDate){
                                //console.log('DATE ต้องกลับมาเป็นวันเดียวกันกับเริ่ม');

                                //วันที่ห้ามน้อยกว่าวันเริ่มต้น แสดงเเบล๊กดร๊อป
                                var tStartDate      = $('#oetStartdate'+nSeq).val();
                                $('#oetEnddate'+nSeq).val(tStartDate);
                                var tResultStartDate = tStartDate.split("-");
                                $('#oetEnddate'+nSeq).datepicker("setDate", new Date(tResultStartDate[2],tResultStartDate[1] - 1,tResultStartDate[0]) );

                                var tTextwarning        = "<?=language('common/systems', 'tModalTextDataFormatDateError')?>";
                                $('.xCNHeadTextModalDateStartDateEnd').text('<?=language('common/systems', 'tModalHeadDataFormatDateError')?>');
                                $('#odvModalDateStartDateEnd').modal('show');
                                $('.xCNTextModalDateStartDateEnd').text(tTextwarning);
                                $('#odvModalDateStartDateEnd').on('keydown', function ( e ) {
                                    var keyCode = e.keyCode || e.which; 
                                    if(keyCode === 13){
                                        $('.xCNBTNActionCancelDateStartDateEnd').click();
                                        $('#oetEnddate'+nSeq).focus();
                                    }
                                });
                                
                                $('.xCNBTNActionCancelDateStartDateEnd').click(function() {
                                    setTimeout(function(){
                                        $('#oetEnddate'+nSeq).focus();
                                        var tStartDate      = $('#oetStartdate'+nSeq).val();
                                        $('#oetEnddate'+nSeq).val(tStartDate);
                                        var tResultStartDate = tStartDate.split("-");
                                        $('#oetEnddate'+nSeq).datepicker("setDate", new Date(tResultStartDate[2],tResultStartDate[1] - 1,tResultStartDate[0]) );
                                    }, 500);
                                });

                                tDateEndOld = tEndDate;
                                JSUpdateEditinline(nSeq,tID);
                            }else{
                                //console.log('ELSE ปล่อย เลือก');
                                tDateEndOld = tEndDate;
                                JSUpdateEditinline(nSeq,tID);
                            }
                        }, 500);

                    }
                }
            }
            
            tTypeClick = 'focusout';
        }else if(e.type == "keydown"){
            //กดenter
            if(e.keyCode == 13){
                var nSeq                = $(this).parents('tr').data('seq');
                var tID                 = $(this).attr('id')
                var tStartDateInput     = $('#oetStartdate'+nSeq).val();
                var tEnddateInput       = $('#oetEnddate'+nSeq).val();

                var aStartDate      = tStartDateInput.split("-"); 
                var tStartDateInput = aStartDate[2]+'-'+aStartDate[1]+'-'+aStartDate[0];

                var aEndDate        = tEnddateInput.split("-"); 
                var tEndDateInput = aEndDate[2]+'-'+aEndDate[1]+'-'+aEndDate[0];

                var tStartDate      = new Date(tStartDateInput).setHours(0,0,0,0);
                var tEndDate        = new Date(tEndDateInput).setHours(0,0,0,0);
                var tCurrentDate    = new Date().setHours(0,0,0,0);

                tTypeModalDuplicate = true;
                var tSearchEnd = tID.search("Enddate");
                if(tSearchEnd == -1){
                    //วันที่เริ่มต้น
                    if(String(tStartDate) == String(tCurrentDate)){
                        tTypeSpcNotApprove = true;
                        $(this).parent().parent().next().find('.xWDatepicker').focus();
                    }else if(String(tStartDate) < String(tCurrentDate)){
                        tTypeSpcNotApprove      = true;
                        var tTextwarning        = "<?=language('common/systems', 'tModalTextDatalessCurrent')?>";
                        $('.xCNHeadTextModalDataDuplicate').text('<?=language('common/systems', 'tModalHeadDataFormatDateError')?>');
                        $('#odvModalDataDuplicate').modal('show');
                        $('.xCNTextModalDataDuplicate').text(tTextwarning);
                        $('.inputsChange').blur();

                        $('#odvModalDataDuplicate').on('keydown', function ( e ) {
                            var keyCode = e.keyCode || e.which; 
                            if(keyCode === 13){
                                $('#obtFoundDatainTable').click();
                                $('#oetStartdate'+nSeq).focus();
                            }
                        });
                    }else{
                        tTypeSpcNotApprove = false;
                        $(this).parent().parent().next().find('.xWDatepicker').focus();
                    }
                    
                    setTimeout(function(){
                        JSUpdateEditinline(nSeq,tID);
                    }, 500);
                }else{
                    //วันที่สิ้นสุด
                    var tResultStartDate        = $('#oetStartdate'+nSeq).val().split("-");
                    var tResultStartDateS       = tResultStartDate[2]+'-'+tResultStartDate[1]+'-'+tResultStartDate[0];
                    
                    var tResultEndDate          = $('#oetEnddate'+nSeq).val().split("-");
                    var tResultEndDateS         = tResultEndDate[2]+'-'+tResultEndDate[1]+'-'+tResultEndDate[0];

                    var tConvertStartDate       = new Date(tResultStartDateS).setHours(0,0,0,0);
                    var tConvertEndDate         = new Date(tResultEndDateS).setHours(0,0,0,0);

                    if(tConvertEndDate < tConvertStartDate){
                        tTypeSpcNotApprove      = true;
                        var tStartDate      = $('#oetStartdate'+nSeq).val();
                        $('#oetEnddate'+nSeq).val(tStartDate);
                        var tResultStartDate = tStartDate.split("-");
                        $('#oetEnddate'+nSeq).datepicker("setDate", new Date(tResultStartDate[2],tResultStartDate[1] - 1,tResultStartDate[0]) );
                        
                        //วันที่ห้ามน้อยกว่าวันเริ่มต้น แสดงเเบล๊กดร๊อป
                        var tTextwarning        = "<?=language('common/systems', 'tModalTextDataFormatDateError')?>";
                        $('.xCNHeadTextModalDateStartDateEnd').text('<?=language('common/systems', 'tModalHeadDataFormatDateError')?>');
                        $('#odvModalDateStartDateEnd').modal('show');
                        $('.xCNTextModalDateStartDateEnd').text(tTextwarning);
                        $('#odvModalDateStartDateEnd').on('keydown', function ( e ) {
                            var keyCode = e.keyCode || e.which; 
                            if(keyCode === 13){
                                $('.xCNBTNActionCancelDateStartDateEnd').click();
                                $('#oetEnddate'+nSeq).focus();
                            }
                        });
                        $('.xCNBTNActionCancelDateStartDateEnd').click(function() {
                            setTimeout(function(){ 
                                $('#oetEnddate'+nSeq).focus();
                            }, 500);
                        });
                        
                        JSUpdateEditinline(nSeq,tID);
                    }else{
                        if(isNaN(tConvertEndDate) == true){
                            var tStartDate      = $('#oetStartdate'+nSeq).val();
                            $('#oetEnddate'+nSeq).val(tStartDate);
                        }else{
                            //console.log('ปล่อยคีย์');
                            $('.inputsChange').blur();
                        }
                        JSUpdateEditinline(nSeq,tID);
                    }
                }
            }

            tTypeClick = 'keydown';
        }else if(e.type == "keyup"){
            tTypespecialClick = 'keyup';
        }
    });
    
    //Event resize table
    $('.xCNTableResize').colResizable({
        fixed           : false,
        liveDrag        : true,
        gripInnerHtml   : "<div class='grip'></div>", 
        draggingClass   : "dragging"
    });

    //Function sort by data
    $('.xCNSortDatacolumn').click(function(e){
        var tValueKey = $(this).data('sortby');
        $('#ohdNameSort').val(tValueKey);

        var tTypeSort = '';
        var tCheckTypeSort = $('#ohdTypeSort').val();
        if(tCheckTypeSort == 'ASC'){
            tTypeSort = 'DESC';
            $('#ohdTypeSort').val(tTypeSort);
        }else{
            tTypeSort = 'ASC';
            $('#ohdTypeSort').val(tTypeSort);
        }
        JSxSelectDataintoTable();
    });

    //Browse Edit inline
    function JSxEditInlineByEvent(poElement,tEvent){
        if(tEvent == 'Doubleclick'){
            var nSeq = $(poElement).parents('.xWTurnoffsuggestorderDataSource').data('seq');
            oCrdBrwCardType.CallBack.ReturnType = 'S';
            oCrdBrwCardType.NextFunc.FuncName   = 'JSxEditChangePDT';
            oCrdBrwCardType.NextFunc.ArgReturn  = [nSeq];
            JCNxBrowseData('oCrdBrwCardType'); 
        }else if(tEvent == 'Click'){
            //edit inline
        }
    }

    //Change value in INPUT Data
    function JSxEditChangePDT(elem,ptSeq){
        var aData = JSON.parse(elem);
        $('#oetPDTCode'+ptSeq).val(aData[0].FTPdtCode);
        $('#oetBarcode'+ptSeq).val(aData[0].FTPdtBarCode);
        $('#oetPDTName'+ptSeq).val(aData[0].FTPdtName);

        JSUpdateEditinline(ptSeq,'');
    }

    //Event -> Edit inline
    function JSUpdateEditinline(pnSeq,ptID){
        try{
            
            var nPDTCode    = $('#oetPDTCode'+pnSeq).val();
            var nBarCode    = $('#oetBarcode'+pnSeq).val();
            var tPDTName    = $('#oetPDTName'+pnSeq).val();
            var tStartDate  = $('#oetStartdate'+pnSeq).val();
            var tEndDate    = $('#oetEnddate'+pnSeq).val();

            var tResultStartDate = tStartDate.split("-");
            var tResultStartDateS = tResultStartDate[2]+'-'+tResultStartDate[1]+'-'+tResultStartDate[0];
            
            var tResultEndDate = tEndDate.split("-");
            var tResultEndDateS = tResultEndDate[2]+'-'+tResultEndDate[1]+'-'+tResultEndDate[0];

            var tConvertStartDate     = new Date(tResultStartDateS).setHours(0,0,0,0);
            var tConvertEndDate       = new Date(tResultEndDateS).setHours(0,0,0,0);
            var tConvertCurrentDate   = new Date().setHours(0,0,0,0);

            if(ptID == '' || ptID == null){
                JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDate,tEndDate,'');
            }else{
                var tSearchEnd = ptID.search("Enddate");
                if(tSearchEnd == -1){
                    var tFlagStartorEnd = 'Start';
                    JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDate,tEndDate,tFlagStartorEnd);
                }else{
                    var tFlagStartorEnd = 'End';
                    if(tConvertStartDate == tConvertEndDate){
                        JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDate,tEndDate,tFlagStartorEnd);
                    }else if(tConvertStartDate > tConvertEndDate){
                        var tTextwarning        = "<?=language('common/systems', 'tModalTextDataFormatDateError')?>";
                        $('.xCNHeadTextModalDateStartDateEnd').text('<?=language('common/systems', 'tModalHeadDataFormatDateError')?>');
                        $('#odvModalDateStartDateEnd').modal('show');
                        $('.xCNTextModalDateStartDateEnd').text(tTextwarning);
                        $('.xCNBTNActionCancelDateStartDateEnd').click(function() {
                            setTimeout(function(){ 
                                var date = new Date(tResultStartDateS);
                                    date.setDate(date.getDate() +1);
                                    month   = '' + (date.getMonth()),
                                    day     = '' + date.getDate(),
                                    year    = date.getFullYear();
                                    if (month.length < 2) month = '0' + month;
                                    if (day.length < 2) day = '0' + day;
                                var tValueNewDate = day + '-' + month + '-' + year;
                                $('#oetEnddate'+pnSeq).val(tValueNewDate);
                                $('#oetEnddate'+pnSeq).datepicker("setDate", new Date(year,month,day) );
                                var tEndDate = $('#oetEnddate'+pnSeq).val();
                                JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDate,tEndDate,tFlagStartorEnd);
                                //$('#oetEnddate'+pnSeq).focus();
                            }, 500);

                        });
                    }else{
                        JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDate,tEndDate,tFlagStartorEnd);
                    }
                }
            }
        }catch(err){
            console.log("JSxTimeStampUpdateDataOnTemp Error: ", err);
        }
    }

    function JSxSaveEditInline(pnSeq,nPDTCode,nBarCode,tPDTName,tStartDateInput,tEndDateInput,tFlagStartorEnd){
        // console.log(' : : : : : F U N C T I O N S A V E : : : : : ');
        // console.log('tDateStartOld : ' + tDateStartOld);
        // console.log('tStartDateInput : ' + tStartDateInput);
        // console.log('tTypeClick : ' + tTypeClick);
        // console.log('tTypespecialClick : ' + tTypespecialClick);
        // console.log('tFlagStartorEnd : ' + tFlagStartorEnd)

        if(tFlagStartorEnd == 'Start'){
            if(tTypespecialClick == 'keyup'){
                if(tDateStartOld == tStartDateInput || tEndDateInput == tDateEndOld){
                    tStatusUpdate = true;
                }else{
                    tStatusUpdate = false;
                }
            }else if(tTypespecialClick == ''){
                if(tDateStartOld != tStartDateInput){
                    tStatusUpdate = true;
                }else{
                    tStatusUpdate = false;
                }
            }
        }else if(tFlagStartorEnd == 'End'){
            // console.log('TYPE : ' + tTypespecialClick);
            // console.log('DATE INPUT : ' + tEndDateInput);
            // console.log('DATE OLD : ' + tDateEndOld);
            if(tTypespecialClick == 'keyup'){
                if(tEndDateInput == tDateEndOld){
                    tStatusUpdate = true;
                }else{
                    tStatusUpdate = false;
                }
            }else if(tTypespecialClick == ''){
                tStatusUpdate = true;
            }
        }else{
            tStatusUpdate = true;
        }

        tTypespecialClick = '';
        if(tStartDateInput.length == 10 && tEndDateInput.length == 10 && tStatusUpdate == true){
            $.ajax({
                type    : "POST",
                url     : 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOUpdatePDTTempintoTable',
                data    : {
                    pnSeq          : pnSeq,
                    nPDTCode       : nPDTCode,
                    nBarCode       : nBarCode, 
                    tPDTName       : tPDTName,
                    tStartDate     : tStartDateInput,
                    tEndDate       : tEndDateInput
                },
                cache: false,
                success: function(tResult) {
                    //var d = new Date();
                    // console.log(' ================> S U C C E S S <================ ' + d)
                    var aData = JSON.parse(tResult);

                    if(tTypeSpcNotApprove != true){
                        var tStartDate = $('#oetStartdate'+pnSeq).val(); 
                        if(tFlagStartorEnd == 'Start'){
                            var tStartDate = $('#oetStartdate'+pnSeq).val();
                            
                            
                            var tResultStartDate = $('#oetStartdate'+pnSeq).val().split("-");
                            var tResultStartDateS = tResultStartDate[2]+'-'+tResultStartDate[1]+'-'+tResultStartDate[0];
                            
                            var tResultEndDate = $('#oetEnddate'+pnSeq).val().split("-");
                            var tResultEndDateS = tResultEndDate[2]+'-'+tResultEndDate[1]+'-'+tResultEndDate[0];

                            var tConvertStartDate     = new Date(tResultStartDateS).setHours(0,0,0,0);
                            var tConvertEndDate       = new Date(tResultEndDateS).setHours(0,0,0,0);
                            //วันที่สิ้นสุด มากกว่าวันที่เริ่มต้น
                            if(tConvertEndDate > tConvertStartDate){

                            }else{ //วันที่สิ้นสุด น้อยกว่า หรือเท่ากับ วันที่เริ่มต้น
                                $('#oetEnddate'+pnSeq).val(tStartDate);
                                var tResultStartDate = tStartDate.split("-");
                                $('#oetEnddate'+pnSeq).datepicker("setDate", new Date(tResultStartDate[2],tResultStartDate[1] - 1,tResultStartDate[0]) );
                                $('#oetEnddate'+pnSeq).focus();
                            }
                        }
                    }

                    
                    if(aData[0] == 'fail'){
                        
                    }else if(aData[0] == 'duplicate'){
                        var tBarcodeOrPDT                   = nPDTCode;
                        var tLangModal                      = '<?=language('common/systems', 'tModalTextProductDuplicate')?>';
                        var tModalTextFoundDataDuplicate    = '<?=language('common/systems', 'tModalTextFoundDataDuplicate')?>';
                        var tModalTextBarcodeDuplicate      = '<?=language('common/systems', 'tModalTextBarcodeDuplicate')?>';
                        $('#xCNTextDataDuplicate').text(tLangModal + tBarcodeOrPDT + tModalTextFoundDataDuplicate);
                        $('#odvModalFoundDatainTable').modal('show');
                        var nOldCode = $('#oetPDTCode'+pnSeq).data('oldpdtcode');
                        $('#oetPDTCode'+pnSeq).val(nOldCode);
                    }else{
                        if(tResult == 'false'){
                            var nOldCode = $('#oetPDTCode'+pnSeq).data('oldpdtcode');
                            $('#oetPDTCode'+pnSeq).val(nOldCode);
                            alert('ไม่พบบาร์โค๊ด');
                            //$('#odvModalProductNotFound').modal('show');
                        }else{
                            $('#oetPDTCode'+pnSeq).val(aData.FTPdtCode);
                            $('#oetBarcode'+pnSeq).val(aData.FTPdtBarCode);
                            $('#oetPDTName'+pnSeq).val(aData.FTPdtName);
                            /*$('#oetStartdate'+pnSeq).val(aData.FDPdtStartdate);
                            $('#oetEnddate'+pnSeq).val(aData.FDPdtEnddate);*/

                            $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                            $('#ohdFlagSave').val('unsave');
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('error');
                }
            });
        }
    }

</script>