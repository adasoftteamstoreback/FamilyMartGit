<script>
    $(document).ready(function () {
        JSxODSSizeControl();
        var tSection = ["NEW", "PROMOTION", "TOP1000", "OTHER", "ADDON"];
        for(i=0;i<tSection.length;i++){
            $('#otbTableOrderingScreen' + tSection[i]).colResizable({
                fixed:false,
                liveDrag:true,
                gripInnerHtml:"<div class='grip'></div>", 
                draggingClass:"dragging"
            });
        }
    });

    $(window).resize(function(){
        JSxODSSizeControl();
    });

    function JSxODSSizeControl(){
        var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            x = w.innerWidth || e.clientWidth || g.clientWidth,
            y = w.innerHeight || e.clientHeight || g.clientHeight;

        if(x <= 992){ //MD XS 
            var nNewWidth = y - 400;
        }else{ //LG
            var nNewWidth = y - 330;
        }
        $('.table-scroll').css('height',nNewWidth+'px');
    }
    
    $('#oetODSAddPdt').off('keypress');
    $('#oetODSAddPdt').on('keypress',function(event){
        if(event.keyCode == 13){
            var aMessage = {
                tNotFoundPdt : {
                    tHead    : '<?php echo language('document/orderingscreen','tODSHeadAlertNotFoundData')?>',
                    tDetail  : '<?php echo language('document/orderingscreen','tODSDetailAlertNotFoundData')?>',
                    tType    : 2
                },
                tPdtDup      : {
                    tHead    : '<?php echo language('document/orderingscreen','tODSHeadAlertPdtDup')?>',
                    tDetail  : '<?php echo language('document/orderingscreen','tODSDetailAlertPdtDup')?>',
                    tType    : 2
                }
            };
            JSxODSAddPdtManual(aMessage);
        }
    });

    $('.xWInputPdtOrdLot').off('keydown');
    $('.xWInputPdtOrdLot').on('keydown',function(event){
        var nSta = $('#oetODSStaEdit').val();
        if(nSta != "1"){
            var aMessage = {
                tHead   : '<?php echo language('document/orderingscreen','tODSHeadAlertPdtMax')?>',
                tDetail : '<?php echo language('document/orderingscreen','tODSDetailAlertPdtMax')?>',
                tType   : 2
            };
            switch(event.keyCode){
                case 9:
                    // console.log('keydown tab');
                    event.preventDefault();
                    JSxODSEditInLine($(this).data('seq'),$(this).data('sec'),$(this).data('val'),$('.xWInputPdtOrdLot').index(this),aMessage,'TRUE','DOWN');
                    event.stopImmediatePropagation();
                    event.stopPropagation();
                    break;
                case 13:
                    // console.log('keydown enter');
                    event.preventDefault();
                    JSxODSEditInLine($(this).data('seq'),$(this).data('sec'),$(this).data('val'),$('.xWInputPdtOrdLot').index(this),aMessage,'TRUE','DOWN');
                    event.stopImmediatePropagation();
                    event.stopPropagation();
                    break;
                case 38:
                    // console.log('up');
                    event.preventDefault();
                    JSxODSEditInLine($(this).data('seq'),$(this).data('sec'),$(this).data('val'),$('.xWInputPdtOrdLot').index(this),aMessage,'TRUE','UP');
                    event.stopImmediatePropagation();
                    event.stopPropagation();
                    break;
                case 40:
                    // console.log('down');
                    event.preventDefault();
                    JSxODSEditInLine($(this).data('seq'),$(this).data('sec'),$(this).data('val'),$('.xWInputPdtOrdLot').index(this),aMessage,'TRUE','DOWN');
                    event.stopImmediatePropagation();
                    event.stopPropagation();
                    break;
            }
        }
    });

    $('.xWInputPdtOrdLot').off('focusout');
    $('.xWInputPdtOrdLot').on('focusout',function(event){
        var nSta = $('#oetODSStaEdit').val();
        if(nSta != "1"){
            // console.log('focusout');
            var aMessage = {
                tHead   : '<?php echo language('document/orderingscreen','tODSHeadAlertPdtMax')?>',
                tDetail : '<?php echo language('document/orderingscreen','tODSDetailAlertPdtMax')?>',
                tType   : 2
            };
            JSxODSEditInLine($(this).data('seq'),$(this).data('sec'),$(this).data('val'),$('.xWInputPdtOrdLot').index(this),aMessage,'FALSE','DOWN');
            event.stopImmediatePropagation();
            event.stopPropagation();
        }
    });

    $('.xWInputPdtOrdLot').off('focus');
    $('.xWInputPdtOrdLot').on('focus',function(event){
        $(this).select();
        event.stopImmediatePropagation();
        event.stopPropagation();
    });

    $('.xCNImageInsert').off('click');
    $('.xCNImageInsert').on('click',function(){
        var tDocNo      = $('#oetODSDocNo').val();
        var dOrderDate  = JStODSConvertFormatDate($('#oetODSOrderDate').val());
        var dDate       = new Date(dOrderDate);
        var nDay        = dDate.getDay();
        if(tDocNo == "" || tDocNo === undefined){ tDocNo = ''; }
        switch (nDay) {
            case 0:
                tADS                = "ISNULL(W.FCSaleQtySun,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdSun = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDSun IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDSun, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 1:
                tADS                = "ISNULL(W.FCSaleQtyMon,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdMon = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDMon IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDMon, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 2:
                tADS                = "ISNULL(W.FCSaleQtyTue,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdTue = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDTue IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDTue, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 3:
                tADS                = "ISNULL(W.FCSaleQtyWed,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdWed = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDWed IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDWed, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 4:
                tADS                = "ISNULL(W.FCSaleQtyThu,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdThu = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDThu IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDThu, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 5:
                tADS                = "ISNULL(W.FCSaleQtyFri,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdFri = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDFri IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDFri, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
            case 6:
                tADS                = "ISNULL(W.FCSaleQtySat,0) AS ADS";
                tOrderPdtOnDay      = "AND P.FTPdtOrdSat = '1'";
                tDeliveryDate       = "CASE WHEN S.FNLTDSat IS NULL THEN CONVERT(VARCHAR(10),'" + dOrderDate + "',121) ELSE CONVERT(VARCHAR(10),DATEADD(day, S.FNLTDSat, '" + dOrderDate + "'),121) END AS DELIVERY_DATE";
                break;
        }

        oODSBrwPdt.GrideView.DataColumns = ['P.FTPdtCode',
                                            "CASE WHEN B.FTPdtBarCode IS NULL THEN '' ELSE B.FTPdtBarCode END AS FTPdtBarCode",
                                            'P.FTPdtName',
                                            'P.FTPdtNameOth',
                                            'P.FTPdtNameShort',
                                            'U.FTPunName',
                                            'G.FTPgpName AS CATEGORY',
                                            'G1.FTPgpName AS SUBCAT',
                                            'T.FTStyName',
                                            'SPL.FTSplViaRmk',
                                            'P.FCPdtCostStd',
                                            'B.FCPdtRetPri1',
                                            'P.FCPdtQtyRet',
                                            'P.FCPdtStkFac',
                                            tADS,
                                            tDeliveryDate,
                                            "ISNULL((SELECT TOP 1 (C.FCXodQty) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='"+dOrderDate+"' AND C.FTPdtName=P.FTPdtName ORDER BY C.FTXohDocNo DESC),'0') AS ORDER_LOT",
                                            "ISNULL((SELECT TOP 1 (C.FCXodQtyAll) FROM TACTPoDT C (NOLOCK) WHERE CONVERT(VARCHAR(10),C.FDXohDocDate,121)='"+dOrderDate+"' AND C.FTPdtName=P.FTPdtName ORDER BY C.FTXohDocNo DESC),'0') AS ORDER_PCS",
                                            "'' AS PROMO",
                                            'O.FCSugQty',
                                            'P.FTSplCode',
                                            'P.FCPdtMax',
                                            'CASE WHEN A.FC_T1 IS NULL THEN 0 ELSE (A.FC_T1+A.FC_T2) END AS IN_TRANSIT',
                                            "CASE WHEN (SELECT DISTINCT(SUM(C.FCXodQty)) FROM TACTPoDT C (NOLOCK) WHERE C.FDXohDocDate='"+dOrderDate+"' AND C.FTPdtName=P.FTPdtName) IS NULL THEN '0' ELSE '1' END AS POFlag",
        ];
        // "'On going ' +SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStart,103),4,2)+'-'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),1,2)+'/'+SUBSTRING(CONVERT(VARCHAR,H.FDPmhDStop,103),4,2) AS PROMO",
        oODSBrwPdt.Join.On             = [
            "B.FTPdtCode=P.FTPdtCode LEFT JOIN TCNMSGOPara S WITH (NOLOCK) ON S.FTPdtCode=P.FTPdtCode LEFT JOIN TCNTSGOPara A WITH (NOLOCK) ON A.FTStkCode=P.FTPdtStkCode AND A.FDOrderDate='"+dOrderDate+"' LEFT JOIN TCNMPdtSugOrd O WITH (NOLOCK) ON O.FTPdtStkCode=P.FTPdtStkCode LEFT JOIN TCNTHisSale4Week W WITH (NOLOCK) ON W.FTPdtStkCode=P.FTPdtStkCode LEFT JOIN TCNMPdtGrp G WITH (NOLOCK) ON SUBSTRING(G.FTPgpChain,1,6)=SUBSTRING(P.FTPgpChain,1,6) AND G.FNPgpLevel='1' LEFT JOIN TCNMPdtGrp G1 WITH (NOLOCK) ON G1.FTPgpChain=P.FTPgpChain AND G1.FNPgpLevel='4' LEFT JOIN TCNTPdtPmtDT M WITH (NOLOCK) ON M.FTPdtName=P.FTPdtName LEFT JOIN TCNTPdtPmtHD H WITH (NOLOCK) ON H.FTPmhDocNo=M.FTPmhDocNo AND H.FDPmhDStop>='" + dOrderDate + "' LEFT JOIN TCNMSplType T WITH (NOLOCK) ON T.FTStyCode=P.FTStyCode LEFT JOIN TCNMSpl SPL WITH (NOLOCK) ON SPL.FTSplCode=P.FTSplCode LEFT JOIN TSPoDT DT WITH (NOLOCK) ON DT.FTPdtCode = P.FTPdtCode AND DT.FTPdtBarCode = B.FTPdtBarCode AND DT.FTXohDocNo = '" + tDocNo + "' AND DT.FCPdtOrdLot >= 0 LEFT JOIN TCNMPdtUnit U ON P.FTPunCode = U.FTPunCode"
        ];
        oODSBrwPdt.Where.Condition     = [
            tOrderPdtOnDay,
            " AND P.FTPdtStaAlwBuy = '1' AND ('" + dOrderDate + "' >= FDPdtOrdStart AND '" + dOrderDate + "' <= P.FDPdtOrdStop)",
            " AND DT.FTPdtCode IS NULL",
            " AND P.FCPdtStkFac != 0",
            " AND T.FTStyName!='Result'"
        ];
        
        JCNxBrowseData('oODSBrwPdt');
        
    });

    $('.xCNSortDatacolumn').off('click');
    $('.xCNSortDatacolumn').on('click',function(e){
        var tSection    = $('#oetODSSelectSectionType').val();
        var tDocNo      = $('#oetODSDocNo').val();
        var tCurSec     = $(this).data('section');
        var nCurPage    = $('#oetODSCurrentPageInTab' + tCurSec).val();
        var tValueKey   = $(this).data('sortby');
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

        if(tSection == "SUMMARY"){
            JSxODSDataTable(tCurSec,tDocNo,nCurPage,tSection);
        }else{
            JSxODSDataTable(tSection,tDocNo,nCurPage);
        }
    });

    $('.xCNTableTrClickActive').off('click');
    $('.xCNTableTrClickActive').on('click',function(){
        $(this).parent().find('.xCNTableTrActive').removeClass('xCNTableTrActive')
        if($(this).hasClass('xCNTableTrActive')){
            $(this).removeClass('xCNTableTrActive');
        }else{
            $(this).addClass('xCNTableTrActive');
        }
    });

</script>