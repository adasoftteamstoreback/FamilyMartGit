var tModal = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">';
    tModal += '<div class="modal-dialog" id="modal-customs" role="document" >';
    tModal += '<div id="odvModalBody" style="background:#ffffff; border-radius: 5px;"></div>';
    tModal += '</div>';
    tModal += '</div>';

//Modal Browse
function JCNxBrowseData(ptOptions) {
    if (window[ptOptions].GrideView.WidthModal == '' || window[ptOptions].GrideView.WidthModal == null) {
        $nPercentWidth = '50';
    } else {
        $nPercentWidth = window[ptOptions].GrideView.WidthModal;
    }

    $max = jQuery(window).width();
    $min = jQuery(window).height();
    $nConvertopx = (($nPercentWidth / 100) * ($max - $min)) + $min;

    $("body").append(tModal);
    $('#modal-customs').attr("style", 'min-width:90%; margin: 1.75rem auto;');

    $('#myModal').modal({ show: true });
    if (window[ptOptions] != undefined || window[ptOptions] != null) {

        var dTime               = new Date();
        var dTimelocalStorage   = dTime.getTime();

        $.ajax({
            type: "POST",
            url: 'Content.php?route=Browser&func_method=index',
            cache: false,
            data: {
                paOptions           : window[ptOptions],
                tOptions            : ptOptions,
                dTimelocalStorage   : dTimelocalStorage,
                nCurentPage         : 1,
                tFilerGride         : ''
            },
            dataType: "Text",
            success: function(tResult) {
                $('#odvModalBody').html(tResult);
                localStorage.removeItem("LocalItemDataPDT" + dTimelocalStorage);

                $(".xCNInputWithoutSingleQuote").on("keypress keyup keydown blur", function(event) {
                    var tInputVal = $(this).val();
                    if(event.which == 222){
                        event.preventDefault();
                    }
                });
            },
            timeout: 0,
            error: function(data) {
                console.log(data);
            }
        });

    } else {
        $('#odvModalBody').html('Error : Do not set options or invalid options.');
    }

}

//Search 
function JCNxSearchBrowse(pnCurentPage, ptOptions , dTimelocalStorage) {
    if (window[ptOptions] != undefined || window[ptOptions] != null) {

        var tFilerGride = $('.oetSearchTable').val();
        if(tFilerGride == '' || tFilerGride == null){
            tFilerGride = '';
        }else{
            tFilerGride = tFilerGride;
        }

        $.ajax({
            type    : "POST",
            url     : 'Content.php?route=Browser&func_method=index',
            cache   : false,
            data    : {
                paOptions           : window[ptOptions],
                tOptions            : ptOptions,
                dTimelocalStorage   : dTimelocalStorage,
                nCurentPage         : pnCurentPage,
                tFilerGride         : tFilerGride
            },
            dataType    : "Text",
            success     : function(tResult) {
                $('#odvModalBody').html(tResult);
                $('.oetSearchTable').val(tFilerGride);
                var tStorePDT  = localStorage.getItem("LocalItemDataPDT" + dTimelocalStorage);
                var tStorePDT  = JSON.parse(tStorePDT);
                if(tStorePDT == null || tStorePDT == ''){

                }else{
                    var nLength    = tStorePDT.length;  
                    for($i=0; $i<nLength; $i++){
                        nKey = tStorePDT[$i].KEYPK;
                        $('.otrTable' + nKey).addClass('xCNActiveRecord');
                    }
                }
            },
            timeout: 3000,
            error: function(data) {
                console.log(data);
            }
        });

    } else {
        alert('Do not set options');
    }
}

//Select multi-record DBclick or Click
aMulti = [];
function JCNxPushSelection(poElement,paPackdata) {
    var aPackdata = JSON.parse(JSON.stringify(paPackdata).split("'").join("''"));
    if (poElement.getAttribute("data-dblclick") == null) {
        poElement.setAttribute("data-dblclick", 1);
        setTimeout(function () {
            if (poElement.getAttribute("data-dblclick") == 1) {
                var tEvent = 'Click';
                JCNEventPushDataMulti(tEvent,poElement,aPackdata);
            }
            poElement.removeAttribute("data-dblclick");
        }, 300);
    } else {
        poElement.removeAttribute("data-dblclick");
        var tEvent = 'Doubleclick';
        JCNEventPushDataMulti(tEvent,poElement,aPackdata);
    }
}

//Event -> Select multi-record
function JCNEventPushDataMulti(ptEventClick,poElement,paPackdata){
    if($('#otrTable' + paPackdata.KEY).hasClass('xCNActiveRecord')){
        $(poElement).removeClass('xCNActiveRecord');

        //case remove
        var tStorePDT   = localStorage.getItem("LocalItemDataPDT" + paPackdata.TIMELOCAL);
        if(tStorePDT == '' || tStorePDT == null){
            localStorage.removeItem("LocalItemDataPDT" + paPackdata.TIMELOCAL);
        }else{
            var tStorePDT       = localStorage.getItem("LocalItemDataPDT" + paPackdata.TIMELOCAL);
            var tStorePDT       = JSON.parse(tStorePDT);
            var nLength         = tStorePDT.length;  
            var aNewStore       = []; 
            var aNewarraydata   = [];

            for($i=0; $i<nLength; $i++){
                aNewStore.push(tStorePDT[$i]);
            }

            var nLengthStore = aNewStore.length; 
            for($i=0; $i<nLengthStore; $i++){
                if(paPackdata.KEY == aNewStore[$i].KEY ){
                    delete aNewStore[$i];
                }
            }
            
            for($i=0; $i<nLengthStore; $i++){
                if(aNewStore[$i] != undefined){
                    aNewarraydata.push(aNewStore[$i]);
                }
            }    
            
            aMulti = [];
            aMulti = aNewarraydata;
            localStorage.setItem("LocalItemDataPDT" + paPackdata.TIMELOCAL,JSON.stringify(aNewarraydata));
        }
    }else{
        $(poElement).addClass('xCNActiveRecord');
        //case insert
        aMulti.push(paPackdata);
        localStorage.setItem("LocalItemDataPDT" + paPackdata.TIMELOCAL,JSON.stringify(aMulti));
    }
}

//Select single-record DBclick or Click
aSingle = [];
function JCNxPushSingleSelection(poElement,paPackdata){
    var aPackdata = JSON.parse(JSON.stringify(paPackdata).split("'").join("''"));
    var tEvent = '';
    if (poElement.getAttribute("data-dblclick") == null) {
        poElement.setAttribute("data-dblclick", 1);
        setTimeout(function () {
            if (poElement.getAttribute("data-dblclick") == 1) {
                var tEvent = 'Click';
                JCNEventPushDataSingle(tEvent,poElement,aPackdata);
            }
            poElement.removeAttribute("data-dblclick");
        }, 300);
    } else {
        poElement.removeAttribute("data-dblclick");
        var tEvent = 'Doubleclick';
        JCNEventPushDataSingle(tEvent,poElement,aPackdata);
    }
    //poElement.preventDefault();
}

//Event -> Select single-record 
function JCNEventPushDataSingle(ptEventClick,poElement,paPackdata){
    if(ptEventClick == 'Click' || ptEventClick == 'Doubleclick'){
        $('tbody tr td').removeClass('xCNActiveRecord');
        $(poElement).find('td').addClass('xCNActiveRecord');
    
        //case remove
        aSingle = [];
        localStorage.removeItem("LocalItemDataPDT" + paPackdata.TIMELOCAL);
    
        //case insert
        aSingle.push(paPackdata);
        localStorage.setItem("LocalItemDataPDT" + paPackdata.TIMELOCAL,JSON.stringify(aSingle));

        if(ptEventClick == 'Doubleclick'){
            //setTimeout(function () {
                $('#myModal').modal('hide');
                $('.xCNConfirmBrowsePDT').click();
            //}, 300);
        }
    }
}

//confirm
function JCNxConfirmSelected(ptOptions,ptTimelocal) {

    aMulti  = [];
    aSingle = [];
    $('#myModal').modal('hide');

    var tStorePDT   = localStorage.getItem("LocalItemDataPDT" + ptTimelocal);
    if (window[ptOptions].NextFunc != undefined || window[ptOptions].NextFunc != null) {
        tGotoFunction = (window[ptOptions].NextFunc.FuncName);
        elem = tStorePDT;
        tAgrRet = $('#ohdCallBackArg').val();
        return window[tGotoFunction](elem,tAgrRet);
    }

    localStorage.removeItem("LocalItemDataPDT" + ptTimelocal);
}


