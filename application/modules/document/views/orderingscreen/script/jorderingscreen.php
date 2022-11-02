<script>
	$('title').html("Ordering Screen");
	
	$('#obtODSConfirmOrder').off('click');
	$('#obtODSConfirmOrder').on('click',function(){
		JSxODSConfirmOrder();
	});

	$('#obtODSCancel').off('click');
	$('#obtODSCancel').on('click',function(){
		var aMessage = {
			tHead   : '<?php echo language('document/orderingscreen','tODSHeadAlertCancel')?>',
			tDetail : '<?php echo language('document/orderingscreen','tODSDetailAlertCancel')?>',
			tType   : 1
		};
		JSxODSCancelOrder(aMessage);
		// JSxODSCloseBrowser('cancel');
	});

	$('#obtODSSave').off('click');
	$('#obtODSSave').on('click',function(){
		$('#obtODSSave').addClass('xCNHide');
		// $(this).hide();
		// JSxODSUpdOrdLotAndOrdPcsToNull();
	});
	
	$('#obtODSCopySGOQTY').off('click');
	$('#obtODSCopySGOQTY').on('click',function(){
		JSxODSCopySGOQTY();
	});

	$('#obtODSSearch').off('click');
	$('#obtODSSearch').on('click',function(){
		// JSxODSDataSearchList()
		JSxODSCloseBrowser('search');
	});

	$('#obtODSNew').off('click');
	$('#obtODSNew').on('click',function(){
		JSxODSCallPageMain();
		$('#obtODSNew').attr('disabled',true);
		$('#obtODSLoadOrder').attr('disabled',false);
	});

	var oODSBrwPdt = {
        Title 		: ['document/orderingscreen','tTSOTitlePDT'],
		Table		: {Master:'TCNMPdt P',PK:'FTPdtCode'},
		Join		: {
						Table	: ['TCNMPdtBar B'],
						On		: []
		},
		Where 		: {
						Condition : []
		},
        GrideView	: {
            ColumnPathLang	: 'document/orderingscreen',
			ColumnKeyLang	: ['tTSOCodePDT','tTSOBarcodePDT','tTSONamePDT','tTSONameotherPDT','tTSONameotherShortPDT','tODSUnitCount'],
			DataColumns		: [],
			DisabledColumns : ['0','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24'],
			ColumnsSize  	: ['10%','15%','15%','15%','15%'],
            Perpage			: 20,
			//OrderBy			: ['B.FDPdtPriAffect DESC'],
			OrderBy			: ['P.FTPdtCode'],
			GroupBy			: ['P.FTPdtCode,B.FTPdtBarCode,P.FTPdtName,P.FTPdtNameOth,P.FTPdtNameShort,G.FTPgpName,G1.FTPgpName,T.FTStyName,P.FCPdtCostStd,B.FCPdtRetPri1,P.FCPdtQtyRet,P.FCPdtStkFac,O.FCSugQty,P.FTSplCode,P.FCPdtMax,P.FTPdtOrdSun,P.FTPdtOrdMon,P.FTPdtOrdTue,P.FTPdtOrdWed,P.FTPdtOrdThu,P.FTPdtOrdFri,P.FTPdtOrdSat,S.FNLTDSun,S.FNLTDMon,S.FNLTDTue,S.FNLTDWed,S.FNLTDThu,S.FNLTDFri,S.FNLTDSat,W.FCSaleQtySun,W.FCSaleQtyMon,W.FCSaleQtyTue,W.FCSaleQtyWed,W.FCSaleQtyThu,W.FCSaleQtyFri,W.FCSaleQtySat,A.FC_T1,A.FC_T2,SPL.FTSplViaRmk,B.FDPdtPriAffect,U.FTPunName'],
			SearchLike	    : [
				"P.FTPdtCode IN (SELECT P2.FTPdtCode FROM TCNMPdt P2 WHERE P2.FTPdtStkCode IN (SELECT P1.FTPdtStkCode FROM TCNMPdt P1 WHERE P1.FTPdtCode = '%tFilerGride%' OR P1.FTPdtName = '%tFilerGride%'))",
				"B.FTPdtBarCode IN (SELECT BAR.FTPdtBarCode FROM TCNMPdtBar BAR WHERE BAR.FTPdtBarCode = '%tFilerGride%')"
				// "P.FTPdtCode 	= '%tFilerGride%'",
				// "B.FTPdtBarCode = '%tFilerGride%'"
				// "P.FTPdtCode IN (SELECT P2.FTPdtCode FROM TCNMPdt P2 WHERE P2.FTPdtStkCode IN (SELECT FTPdtStkCode FROM TCNMPdt P2 WHERE P2.FTPdtCode IN (SELECT FTPdtCode FROM TCNMPdtBar WHERE FTPdtBarCode LIKE '%%tFilerGride%%')) AND P2.FTPdtStaAlwBuy='1')",
				// "P.FTPdtCode IN (SELECT P2.FTPdtCode FROM TCNMPdt P2 WHERE P2.FTPdtStkCode IN (SELECT P1.FTPdtStkCode FROM TCNMPdt P1 WHERE P1.FTPdtCode LIKE '%%tFilerGride%%' OR P1.FTPdtName LIKE '%%tFilerGride%%') AND P2.FTPdtStaAlwBuy='1')"
			],
			SourceOrder		: "ASC",
            // WidthModal      : 150
        },
        CallBack:{
            ReturnType	: 'S'
        },
        NextFunc:{
            FuncName	: 'JSxODSAddPdtBrowse',
            ArgReturn   : []
		},
		//DebugSQL : 'true'
	};
//H.FDPmhDStart,H.FDPmhDStop
</script>