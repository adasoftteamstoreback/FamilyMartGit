<script>
	$(document).ready(function () {
        JSxODSSizeControlSUMMARY();
    });

    $(window).resize(function () {
        JSxODSSizeControlSUMMARY();
    });

    function JSxODSSizeControlSUMMARY(){
        var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            x = w.innerWidth || e.clientWidth || g.clientWidth,
            y = w.innerHeight || e.clientHeight || g.clientHeight;

        if(x <= 992){ //MD XS 
            var nNewWidth = y - 295;
        }else{ //LG
            var nNewWidth = y - 295;
        }
        $('#odvODSContentSUMMARY').css('height',nNewWidth+'px');
    }

	$('#obtODSLoadOrder').off('click');
	$('#obtODSLoadOrder').on('click',function(){
		JSxODSAddDocTmpDT();
	});

	$('.xWTabCallDetails').off('click');
	$('.xWTabCallDetails').on('click',function(){
		JSxODSClickTab($(this).data('sec'));
	});

	$('#osmODSLimitRecord').off('change');
	$('#osmODSLimitRecord').on('change',function(){
		JSxODSDataTable($('#oetODSSelectSectionType').val(),$('#oetODSDocNo').val(),1,'');
	});

	//date
	var dPoTime			= "<?php echo $dODSOrderDate; ?>";
    var oetInputdate 	= $('.xWDatepicker');
    var container 		= $('.xWForm-GroupDatePicker').length>0 ? $('.xWForm-GroupDatePicker').parent() : "body";
    var options 		= {
			format          		: 'dd/mm/yyyy',
			container       		: container,
			todayHighlight  		: true,
			enableOnReadonly		: false,
			disableTouchKeyboard 	: true,//startDate :dPoTime
			autoclose       		: true,
			orientation     		: 'bottom',
			startDate 				: dPoTime
	};
    oetInputdate.datepicker(options);
</script>