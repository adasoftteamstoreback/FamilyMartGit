<?php
require_once "stimulsoft/helper.php";
require_once "../decodeURLCenter.php";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
	if (isset($_GET["infor"])) {
		$aParamiterMap = array(
			"SP_nLang", "SP_tCompCode", "SP_tDocNo" , "SP_DocName"
		);
		$aDataMQ = FSaHDeCodeUrlParameter($_GET["infor"], $aParamiterMap);
		// print_r($aDataMQ);
		// exit;
	} else {
		$aDataMQ = false;
	}

	if ($aDataMQ) {
	?>
	
	<link rel="stylesheet" type="text/css" href="css/stimulsoft.viewer.office2013.whiteblue.css?v=<?=date("dmyhis");?>">
	<script type="text/javascript" src="scripts/stimulsoft.reports.js?v=<?=date("dmyhis");?>"></script>
	<script type="text/javascript" src="scripts/stimulsoft.viewer.js?v=<?=date("dmyhis");?>"></script>

	<?php
		$options = StiHelper::createOptions();
		$options->handler = "handler.php";
		$options->timeout = 30;
		StiHelper::initialize($options);

		switch($aDataMQ["SP_DocName"]){
			case 'prReqReport': 			//ใบขอลดหนี้
				$tTitle = "Frm_208_SMBillReqPc - Viewer";
				break;
			case 'ptReport': 				//ใบลดหนี้
				$tTitle = "Frm_207_SMBillPc - Viewer";
				break;
			case 'ChkStkReport': 			//ใบตรวจนับ (แบบรวม)
				$tTitle = "Frm_425_ALLPdtPhysicalChkStk - Viewer";
				break;
			case 'Frm_424':	 //ใบตรวจนับ (แบบย่อย)
				$tTitle = "Frm_424_ALLPdtChkStk - Viewer";
				break;
			case 'Frm_427':
				$tTitle = "Frm_427_ALLPdtChkStkDif - Viewer";
				break;
			case 'Frm_432':
				$tTitle = "Frm_432_ALLPdtStkCheckingByLocation - Viewer";
				break;
			case 'Frm_433':
				$tTitle = "Frm_433_ALLPdtStkChecking - Viewer";
				break;
			default:
				$tTitle = $aDataMQ["SP_DocName"];
				break;
		}
	?>

	<title><?=$tTitle;?></title>

	<script type="text/javascript">
		function Start() {
			Stimulsoft.Base.StiLicense.key =
				"6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHlDr8/6PIqNKBuLMEkN8xMUPugEQPeiwAHVm+OV" +
				"bCQVabeN3of7ZbnsixRRu+7irZqJ8c0f4LGB9+5sPaMJomcsE37V4Zf1NuPeQ8n+CDF+5Cp4IOyIAra8" +
				"o4iA3x/nD4ktTT7e/BzGEHvbCZvNgR9i00xpzfC/5xrrzGqNC0AF8PWDnOCg0MPNodj9soA4ZH0NPRLj" +
				"jwNPBOxmmG1pLoKBG3Bh7ALEQ2moT93cIEj124GvRIPnChAkiyLRMZkIlTdPYuBHEa7CPM9knzuGqaiz" +
				"ZrN9eWQ+iGiV/grvhEJU3foCQaGJgwnsRHbMPCSZdHtT/4yxoO42SWgZFayM/pDuOXkVhKytawLWnrrQ" +
				"oNUQpmvSarHOUVDLRe70HbyRswH0AXraboEed4qTfn+CUBtMdSEwQLqj237m6N8OTvsROjcXLi4QfXlP" +
				"A28SpfXbQBvEN2TrGqBr5dyKpgbkG+58x85lFO9s1XcQoKXfml8elYzFhMlcae97o5u4dTE/VIseSJ7W" +
				"/scPHOg5gM3Tn72U32bW53UF8/kcNl4+T0WHpg==";

			Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("localization/en.xml", true);

			var report = new Stimulsoft.Report.StiReport();
			switch ('<?=$aDataMQ["SP_DocName"];?>') {
				case 'prReqReport': 			//ใบขอลดหนี้
					report.loadFile("reports/Frm_208_SMBillReqPc.mrt");
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("nLanguage").valueObject 		= 1;
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tCmpBch").valueObject 	= "00342";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					report.dictionary.variables.getByName("SP_nAddSeq").valueObject 	= 10149;
					break;
				case 'ptReport': 				//ใบลดหนี้
					report.loadFile("reports/Frm_207_SMBillPc.mrt");
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("nLanguage").valueObject 		= 1;
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tCmpBch").valueObject 	= "00342";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					report.dictionary.variables.getByName("SP_nAddSeq").valueObject 	= 10149;
					break;
				case 'ChkStkReport': 			//ใบตรวจนับ (แบบรวม)
					report.loadFile("reports/Frm_425_ALLPdtPhysicalChkStk.mrt"); 
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					break;
				case 'Frm_424':	 //ใบตรวจนับ (แบบย่อย)
					report.loadFile("reports/Frm_424_ALLPdtChkStk.mrt");
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					break;
				case 'Frm_427':
					report.loadFile("reports/Frm_427_ALLPdtChkStkDif.mrt");
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					break;
				case 'Frm_432':
					report.loadFile("reports/Frm_432_ALLPdtStkCheckingByLocation.mrt");
					// report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					// report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					// report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					break;
				case 'Frm_433':
					report.loadFile("reports/Frm_433_ALLPdtStkChecking.mrt");
					report.dictionary.variables.getByName("SP_nLang").valueObject 		= "<?php echo $aDataMQ["SP_nLang"];?>";
					report.dictionary.variables.getByName("SP_tCompCode").valueObject 	= "<?php echo $aDataMQ["SP_tCompCode"];?>";
					report.dictionary.variables.getByName("SP_tDocNo").valueObject 		= "<?php echo $aDataMQ["SP_tDocNo"];?>";
					break;
			}

			var options = new Stimulsoft.Viewer.StiViewerOptions();
			options.appearance.fullScreenMode = true;
			options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
			
			var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

			viewer.onBeginProcessData = function (args, callback) {
				<?php StiHelper::createHandler(); ?>
			}

			viewer.report = report;
			viewer.renderHtml("viewerContent");
		}
	</script>
	<?php } ?>
	
</head>
<body onload="Start()">
	<?php if ($aDataMQ) { ?>
		<div id="viewerContent"></div>
	<?php
		}else {
			echo "ไม่สามารถเข้าถึงข้อมูลนี้ได้";
		}
	?>
</body>
</html>