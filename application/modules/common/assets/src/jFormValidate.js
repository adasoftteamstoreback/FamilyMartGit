$(document).ready(function() {
    
    $(".xCNInputNumericWithDecimal").on("keypress keyup blur", function(event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $(".xCNInputNumberOnly").on("keypress keyup blur", function(event) {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });


    $(".xCNInputNumericWithoutDecimal").on("keypress", function(event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        InputId = event.target.id;
        if((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $(".xCNInputAddressNumber").on("keypress keyup blur", function(event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[0-9,/,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

     $(".xCNDontKey").on("keypress keyup blur", function(event) {
        if((event.which >= 96 || event.which <= 105)) {
            event.preventDefault();
        }
    });

    $('.xCNInputWithoutSpc').on("keypress keyup blur", function(event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[a-z,A-Z,ก-๙, ,0-9,@,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $('.xCNInputWithoutSpcNotThai').on("keypress keyup blur", function(event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[a-z,A-Z, ,0-9,@,.,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });


    $(".xCNInputOnlyEng").on("keypress keyup blur", function(event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /[A-Za-z0-9]/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    
    $(".xCNInputWithoutSingleQuote").on("keypress keyup keydown blur", function(event) {
        var tInputVal = $(this).val();
        if(event.which == 222){
            event.preventDefault();
        }
        // var tCharacterReg = /(?=.*[!@#$%\^&*()_+}{":;'?>.<,])/g;
        // if (tCharacterReg.test(tInputVal) && tInputVal != '') {
        //     $(this).val(tInputVal.slice(0, -1));
        //     event.preventDefault();
        // }
    });

    $('.xCNInputMaskCurrency').on("blur", function() {
        var tInputVal = $(this).val();
        tInputVal += '';
        tInputVal = tInputVal.replace(',', '');
        tInputVal = tInputVal.split('.');
        tValCurency = tInputVal[0];
        tDegitInput = tInputVal.length > 1 ? '.' + tInputVal[1] : '';
        var tCharecterComma = /(\d+)(\d{3})/;
        while (tCharecterComma.test(tValCurency))
            tValCurency = tValCurency.replace(tCharecterComma, '$1' + ',' + '$2');
        var tInputReplaceComma = tValCurency + tDegitInput;
        var tSearch = ".";
        var tStrinreplace = ".00";
        var tInputCommaDegit = ""
        if (tInputReplaceComma.indexOf(tSearch) == -1 && tInputReplaceComma != "") {
            tInputCommaDegit = tInputReplaceComma.concat(tStrinreplace);
        } else {
            tInputCommaDegit = tInputReplaceComma;
        }
        $(this).val(tInputCommaDegit);
    });

});


//Functionality : validate number
//Parameters : ptObjName = [ID input] , ptTypeNumber = [FN,FC] , pnPosition = [2]
//Creator : 01/10/2018 Phisan(arm)
//Return : validate number
//Return Type : Decimal
function JCNdValidatelengthDecimal(ptObjName, ptTypeNumber , ptMaxlength, pnPosition){
	var cNum;
	var nVal;

	var nValx = $('#'+ptObjName).val();//ดักกดจุดอย่างเดียว
	var tNumberx = nValx.toString();//ดักกดจุดอย่างเดียว
	var tNumNotCommax = tNumberx.replace(",", "");//ดักกดจุดอย่างเดียว
	var bDotx = tNumNotCommax.includes(".");
	if(tNumNotCommax.length == 1){//ดักกดจุดอย่างเดียว
		if (bDotx == true) {
			nVal = '0.00';
		}else{
			nVal = $('#'+ptObjName).val()*1;//*1เพราะมี0นำหน้ามา
		}
	}else{
		nVal = $('#'+ptObjName).val()*1;//*1เพราะมี0นำหน้ามา
	}
	var tNumber = nVal.toString();
	var tNumNotComma = tNumber.replace(",", "");

	if(ptTypeNumber == 'FC'){
		var bDot = tNumNotComma.includes(".");
		if (bDot == true) {
			var cPow = Math.pow(10, pnPosition);
			var dRound = Math.round(tNumNotComma * cPow) / cPow;
			var tRound = dRound.toString()
			var bRound = tRound.includes(".");
			if (bRound == true) {
				cNum = tRound;
			} else {
				cNum = tRound + '.00';
			}
		}else{
			cNum = tNumNotComma + '.00';
		}
		var tMaxValML = '9';
		var tFinalMaxValML = '9';
		for(var i = 1 ; i < ptMaxlength ; i++){
			tFinalMaxValML += tMaxValML;
		}
		var tMaxValPT = '9';
		var tFinalMaxValPT = '9';
		for(var i = 1 ; i < pnPosition ; i++){
			tFinalMaxValPT += tMaxValPT;
		}
		var cFinalMaxVal = parseFloat(tFinalMaxValML+'.'+tFinalMaxValPT);
		if(cNum > cFinalMaxVal){
			cNum = cFinalMaxVal;
		}
	}else{
		cNum = tNumNotComma;
	}

    var cNumx = parseFloat(cNum);
    
    if(ptObjName == 'oetCrdDeposit' || ptObjName == 'oetCtyDeposit'){
        if(cNumx >= 100){
            var cNumFinal = 100;
        }else{
            var cNumFinal = cNumx.toLocaleString({ minimumFractionDigits: 4 });
        }
    }else{
        var cNumFinal = cNumx.toLocaleString({ minimumFractionDigits: 4 });
    }

    $('#'+ptObjName).val(accounting.formatNumber(cNumFinal,pnPosition));
}
