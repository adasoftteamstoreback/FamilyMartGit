<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/document/assets/css/pdtadjstkchk/pdtadjstkchk.css?v=<?php echo date("dmyhis"); ?>">
<input type="text" class="xCNHide" id="oetPASDocNoPrevious" name="oetPASDocNoPrevious" value="">
<input type="text" class="xCNHide" id="oetPASPassword" name="oetPASPassword" value="">
<input type="text" class="xCNHide" id="oetPASCmpCode" name="oetPASCmpCode" value="<?=$_SESSION["SesFTCmpCode"];?>">
<input type="text" class="xCNHide" id="oetPASHeadLastDocNotComplete" value="<?=language('document/pdtadjstkchk', 'tPASHeadLastDocNotComplete')?>">
<input type="text" class="xCNHide" id="oetPASTextLastDocNotComplete" value="<?=language('document/pdtadjstkchk', 'tPASTextLastDocNotComplete')?>">
<input type="text" class="xCNHide" id="oetPASBaseURL" value="<?=$tBase_url;?>">
<input tpye="text" class="xCNHide" id="oetIuhRefTaxOver" name="oetIuhRefTaxOver" value="">

<!-- Confirm Code -->
<input type="text" class="xCNHide" id="oetPASHeadAlert" value="<?=language('document/pdtadjstkchk', 'tModalHeadAlert')?>">
<input type="text" class="xCNHide" id="oetPASHeadConfirmCode" value="<?=language('document/pdtadjstkchk', 'tModalHeadConfirmCode')?>">
<input type="text" class="xCNHide" id="oetPASHeadWrongPass" value="<?=language('document/pdtadjstkchk', 'tModalHeadWrongPass')?>">
<input type="text" class="xCNHide" id="oetPASTextWrongPass" value="<?=language('document/pdtadjstkchk', 'tModalTextWrongPass')?>">
<input type="text" class="xCNHide" id="oetPASTextPlsEnterPass" value="<?=language('document/pdtadjstkchk', 'tModalTextPlsEnterPass')?>">

<!--Head and BTN-->
<div class="col-lg-12 col-sm-12 col-xs-12 odvPanelhead">
	<div class="row">
		<div id="odvPASTitle" class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
    		<p id="ospTextHeadMenu" class="ospTextHeadMenu" style="margin:0;"> <?=language('document/pdtadjstkchk', 'tPASHeadMenu')?> </p>
			<p id="ospTextSumHeadMenu" class="ospTextHeadMenu" style="margin:0;"> <?=language('document/pdtadjstkchk', 'tPASSumHeadMenu')?> </p>
		</div>
		<div id="odvPASButton" class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
			<div class="odvHeadRight">
				<!-- ปุ่มหน้าใบรวม(ใหม่) Create By: Napat(Jame) 05/08/2020 -->
				<button class="btn xCNBTNActionSearchHQ xWPASBTSearchDO" type="button"> <?=language('document/pdtadjstkchk', 'tBTNSearchDO')?> </button>
				<button class="btn xCNBTNActionSearchHQ xWPASBTSearchAutoReceive" type="button"> <?=language('document/pdtadjstkchk', 'tBTNSearchAutoReceive')?> </button>
				<!-- ปุ่มหน้าใบรวม(ใหม่) -->

				<button class="btn xCNBTNActionSearchHQ xWPASBTSearchHQ" type="button"> <?=language('document/pdtadjstkchk', 'tBTNSearchHQ')?> </button>
				<button class="btn xCNBTNActionSearch xWPASBTSearch" type="button"> <?=language('common/systems', 'tBTNSearch')?> </button>
				<button class="btn xCNBTNActionSave xWPASBTSave" type="button"> <?=language('common/systems', 'tBTNSave')?> </button>
				<button class="btn xCNBTNActionCancel xWPASBTCancel" type="button"> <?=language('common/systems', 'tBTNCancel')?> </button>
				<button class="btn xCNBTNActionInsert xWPASBTAddNew" type="button"> <?=language('common/systems', 'tBTNInsert')?> </button>
				<button class="btn xCNBTNActionReport xWPASBTReport" type="button"> <?=language('common/systems', 'tBTNReport')?> </button>
				<button class="btn xCNBTNActionApprove xWPASBTApprove" type="button"> <?=language('common/systems', 'tBTNApprove')?> </button>
				<button class="btn xCNBTNActionPrevious xWPASBTPrevious" type="button"> <?=language('document/pdtadjstkchk', 'tPASBtnPrevious')?> </button>
				<button class="btn xCNBTNActionNext xWPASBTNext" type="button"> <?=language('document/pdtadjstkchk', 'tPASBtnNext')?> </button>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top:10px;">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<ol class="breadcrumb xWPASSubMenu xWPASSubMenu1">
				<li class="xWPASSubMenuActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu1')?></li>
				<li class="xWPASSubMenuNotActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu1-1')?></li>
				<li class="xWPASSubMenuNotActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu1-2')?></li>
				<li class="xWPASSubMenuNotActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu1-3')?></li>
			</ol>
			<ol class="breadcrumb xWPASSubMenu xWPASSubMenu2">
				<li class="xWPASSubMenuActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu2')?></li>
				<li class="xWPASSubMenuNotActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu2-1')?></li>
			</ol>
			<ol class="breadcrumb xWPASSubMenu xWPASSubMenu3">
				<li class="xWPASSubMenuActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu3')?></li>
				<li class="xWPASSubMenuNotActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu3-1')?></li>
			</ol>
			<ol class="breadcrumb xWPASSubMenu xWPASSubMenu4">
				<li class="xWPASSubMenuActive"><?=language('document/pdtadjstkchk', 'tPASSubMenu4')?></li>
			</ol>
		</div>
	</div>
</div>

<div id="odvPASContentMain"></div>

<!--modal alertmessage -->
<div class="modal fade" id="odvPASModalAlertMessage" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xWPASModalAlertMessageHead"></label>
			</div>
			<div class="modal-body xWPASModalAlertMessageBody">
                
			</div>
			<div class="modal-body xWPASModalAlertMessageBodyInput">
				<input type="text" class="form-control" id="oetPASModalInput" name="oetPASModalInput" value="" autocomplete="off">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xWPASConfirmAlertMessage" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel xWODSCloseAlertMessage" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal alertmessage-->

<!--modal approve -->
<div class="modal fade" id="odvPASModalApprove" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadApprove')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmApprove" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalApproveWarning')?> <br> 
                    <?php echo language('common/systems', 'tModalApproveWarningLine01')?> <br>
                    <?php echo language('common/systems', 'tModalApproveWarningLine02')?> <br>
                    <?php echo language('common/systems', 'tModalApproveWarningLine03')?> <br>
                    <?php echo language('common/systems', 'tModalApproveWarningLine04')?> <br>
                    <?php echo language('common/systems', 'tModalApproveWarningResult')?> <br>
                    <p id="ospConfirmResultApprove"><?php echo language('common/systems', 'tModalApproveWarningResultApprove')?></p>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xWPASModalConfirmApv" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal approve-->

<!--modal search -->
<div class="modal fade" id="odvPASModalSearch" data-backdrop="static" data-keyboard="true" tabindex="-1" data-searchtype="search">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xWPASModalSearchTitle"><?php echo language('document/pdtadjstkchk', 'tBTNSearchHQ')?></label>
			</div>
			<div class="modal-body">
                <div id="odvPASModalSearchHD"></div>
				<div id="odvPASModalSearchDT"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xWPASModalSearchConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal search-->

<!--modal report -->
<div class="modal fade" id="odvPASModalReport" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xWPASModalTitleReport"><?php echo language('document/pdtadjstkchk', 'tPASReportTitle')?></label>
			</div>
			<div class="modal-body">

				<!-- <div class="row">

					<div class="col-sm-8"> -->

						<div style="border:1px solid #ccc;position:relative;padding:15px;margin-top:20px;">
							<label class="xCNLabelFrm" style="position:absolute;top:-15px;left:15px;background: #fff;padding-left: 10px;padding-right: 10px;"><?=language('document/pdtadjstkchk', 'tPASReportDetailDoc');?></label>
							<div class="row">

								<form id="ofmPASB4View" method="post" target="_blank">

									<div class="col-sm-6">
										<div class="form-group form-horizontal">
											<label class="col-sm-3 control-label" style="padding-left:0px;margin-right: -15px;text-align: left;"><?=language('document/pdtadjstkchk', 'tPASReportDoc'); ?></label>
											<div class="input-group col-sm-9">
												<input type="text" id="ohdRptDocNo" name="ohdRptDocNo" class="form-control"  value="">
												<input type="hidden" id="ohdRptCompCode" name="ohdRptCompCode" class="form-control"  value="<?=$_SESSION["SesFTCmpCode"]?>">
												<span class="input-group-btn">
													<button id="obtBrowseReportDocuments" type="button" class="btn xCNBtnBrowseAddOn">
														<img class="xCNIconFind">
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group form-horizontal">
											<label class="col-sm-3 control-label" style="padding-left:0px;margin-right: -15px;text-align: left;"><?=language('document/pdtadjstkchk', 'tPASTBIudChkDate'); ?></label>
											<div class="input-group col-sm-9">
												<input type="text" class="form-control" id="oetPASReportDocDate" name="oetPASReportDocDate" value="" disabled="disabled">
											</div>
										</div>
									</div>

								</form>

							</div>
						</div>
				


						<div style="border:1px solid #ccc;position:relative;padding:15px;margin-top:30px;">
							<label class="xCNLabelFrm" style="position:absolute;top:-15px;left:15px;background: #fff;padding-left: 10px;padding-right: 10px;"><?=language('document/pdtadjstkchk', 'tPASReportCondition');?></label>

							<div id="odvPASMutiSelectReport1">
								<div class="row mb-5" style="margin-bottom: 10px;margin-top: 10px;">
									<div class="col-sm-5">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_1" value="Frm_427">
											<label class="form-check-label" for="orbPASReportList_1">การตรวจนับสต๊อกสินค้า</label>
										</div>
									</div>
									<div class="col-sm-7">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_2" value="Frm_433">
											<label class="form-check-label" for="orbPASReportList_2">เอกสารย่อย : แจกแจงตามกลุ่มสินค้า</label>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-5">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_3" value="Frm_424">
											<label class="form-check-label" for="orbPASReportList_3">เอกสารย่อย</label>
										</div>
									</div>
									<div class="col-sm-7">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_4" value="Frm_432">
											<label class="form-check-label" for="orbPASReportList_4">เอกสารย่อย : แจกแจงตามสถานที่ตรวจนับ</label>
										</div>
									</div>
								</div>
							</div>

							<div id="odvPASMutiSelectReport2">
								<div class="row mb-5" style="margin-bottom: 10px;margin-top: 10px;">
									<div class="col-sm-5">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_5" value="Frm_425">
											<label class="form-check-label" for="orbPASReportList_5">เอกสารรวม</label>
										</div>
									</div>
									<div class="col-sm-7">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_6" value="Frm_440">
											<label class="form-check-label" for="orbPASReportList_6">สินค้าไม่มีในรายการตรวจนับ</label>
										</div>
									</div>
								</div>
							</div>

							<div id="odvPASMutiSelectReport5">
								<div class="row" style="margin-bottom: 10px;margin-top: 10px;">
									<div class="col-sm-12">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="orbPASReportList" id="orbPASReportList_7" value="Frm_428">
											<label class="form-check-label" for="orbPASReportList_7">รายงานการตรวจนับสินค้ามากกว่า 1 จุด</label>
										</div>
									</div>
								</div>
							</div>

						</div>

					<!-- </div>

					<div class="col-sm-4"></div> -->

				<!-- </div> -->

			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xWPASModalConfirmReport" style="width: auto;">
					<?php echo language('document/pdtadjstkchk', 'tPASReportBeforePrint'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal report-->

<script src="<?=$tBase_url?>application/modules/document/assets/src/pdtadjstkchk/jpdtadjstkchk.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>
	$('title').html("Stock Checking");

	$('.xWPASModalSearchConfirm').off('click');
	$('.xWPASModalSearchConfirm').on('click',function(){
		switch($('#odvPASModalSearchHD').data('searchtype')){
			case "searchHQ":
				JSxPASAddDataFromSearchHQ();
				break;
			case "searchDO":
				JSxPASEventAddDocRef(1);
				break;
			case "searchAutoReceive":
				JSxPASEventAddDocRef(2);
				break;
			default:
				JSvPASCallPageMain($('#otbTableSearch').find('.xWSchHQSelected').data('docno'));
				break;
		}
	});

	$('.xWPASBTSearchHQ').off('click');
	$('.xWPASBTSearchHQ').on('click',function(){
		$('.xWPASModalSearchTitle').html('');
		$('#odvPASModalSearchHD').html('');
		$('#odvPASModalSearchDT').html('');
		JSxPASCallSearchHQ();
		$('#odvPASModalSearch').modal('show');
	});

	$('.xWPASBTAddNew').off('click');
	$('.xWPASBTAddNew').on('click',function(){ 
		JSvPASCallPageMain();
	});
	$('.xWPASBTSearch').off('click');
	$('.xWPASBTSearch').on('click',function(){ 
		$('.xWPASModalSearchTitle').html('');
		$('#odvPASModalSearchHD').html('');
		$('#odvPASModalSearchDT').html('');
		JSxPASCallSearchHD();
		$('#odvPASModalSearch').modal('show');
		// JCNxBrowseData('oPASBrwPdtChkHD'); 
	});
	// var tDptCode = '<?=$_SESSION["SesUserDptCode"]?>';
    // var oPASBrwPdtChkHD = {
    //     Title 		: ['document/pdtadjstkchk','tPASHeadMenu'],
	// 	Table		: {Master:'TCNTPdtChkHD',PK:'FTIuhDocNo'},
	// 	Where 		: {
	// 					Condition : [
	// 						" AND FTIuhDocType='1'",
	// 						" AND FTIuhStaDoc = '1'", 
	// 						" AND (ISNULL(FTCstCode,'') = '')",
    //                         " AND (FTIuhStaPrcDoc ='1' OR FTIuhStaPrcDoc ='' OR FTIuhStaPrcDoc IS NULL)",
	// 						" AND (FTIuhDocRef ='' OR FTIuhDocRef IS NULL)",
	// 						" AND ( FTDptCode = '" + tDptCode + "' )"
	// 					]
	// 	},
    //     GrideView	: {
    //         ColumnPathLang	: 'document/pdtadjstkchk',
	// 		ColumnKeyLang	: ['tPASSearchDocNo','tPASSearchDate','tPASSearchStaApv'],
    //         DataColumns		: ['FTIuhDocNo','CONVERT(VARCHAR(10),FDIuhDocDate,121) AS DocDate','FTIuhStaPrcDoc'],
    //         ColumnsSize  	: ['20%','65%','15%'],
    //         Perpage			: 20,
	// 		OrderBy			: ['FTIuhDocNo DESC'],
	// 		SearchLike	    : ['FTIuhDocNo','FDIuhDocDate']
    //     },
    //     CallBack:{
    //         ReturnType	    : 'S'
    //     },
    //     NextFunc:{
    //         FuncName	    : 'JSxPASSearchPdtChkHD',
    //         ArgReturn       : []
	// 	},
	// 	DebugSQL : 'true'
	// };
	
	$('.xWPASBTNext').off('click');
	$('.xWPASBTNext').on('click',function(){
		var tTypePage = String($(this).data('page'));
		switch(tTypePage){
			case "1":
				// var aModalText = {
				// 	tHead	: '<?=language('document/pdtadjstkchk', 'tModalHeadNextStep')?>',
				// 	tDetail	: '<?=language('document/pdtadjstkchk', 'tModalTextNextStep')?>',
				// 	nType	: 1
				// };
				// JSxPASNextStep(aModalText);
				var tDocNo = $('#oetPASDocNo').val();
				$('#oetPASDocNoPrevious').val(tDocNo);
				JSxPASCallPagePdtReChkDT();
				break;
			case "2":
				$('#oetPASTypePage').val('3');
				$('.xWPASBTNext').data('page', 3);
				$('.xWPASBTNext').attr('data-page', 3);
				JSxPASControlButton(); // เปิด/ปิด ปุ่มต่างๆ

				var tPageCurrent = $('.xWPagePdtAdjStkChk .active').text();
				JSvPASCallDataTable(tPageCurrent,'2'); // เรียกตาราง เพื่อให้มันดึงข้อมูล เคลื่อนไหวหลังตรวจนับ StkCard ใหม่ล่าสุดเสมอ
				break;
			case "3":
				$('.xWPASBTPrevious').data('page', 3);
				$('.xWPASBTPrevious').attr('data-page', 3);
				$('#oetPASTypePage').val('4');
				JSxPASControlButton(); // เปิด/ปิด ปุ่มต่างๆ

				var tPageCurrent = $('.xWPagePdtAdjStkChk .active').text();
				JSvPASCallDataTable(tPageCurrent,'3'); // เรียกตาราง เพื่อให้มันดึงข้อมูล เคลื่อนไหวหลังตรวจนับ StkCard ใหม่ล่าสุดเสมอ
				break;
			case "5":
				var aModalText = {
					tHead	: '<?=language('document/pdtadjstkchk', 'tModalHeadNextStep')?>',
					tDetail	: '<?=language('document/pdtadjstkchk', 'tModalTextNextStep')?>',
					nType	: 1
				};

				$.ajax({
					type: "POST",
					url: "Content.php?route=omnPdtAdjStkChkNew&func_method=FSoCPASChkNewQty",
					data: {
						ptDocNo: $('#oetPASDocNo').val()
					},
					cache: false,
					timeout: 0,
					success: function(oResult) {
						var aReturn = JSON.parse(oResult);
						if (aReturn['nStaQuery'] == 1) {
							var aItems 			= aReturn['aItems'];
							var tTextContents 	= "<div><b>"+aReturn['tStaMessage']+"</b></div>";
							if( aItems.length > 0 ){
								$.each( aItems, function( key, value ) {
									tTextContents += "<div>"+(key+1)+". ("+value['FTIudStkCode']+") "+value['FTPdtName']+"</div>";
								});
							}

							var aTextAlert = {
								tHead	: 'แจ้งเตือน',
								tDetail	: tTextContents,
								nType	: 2
							};
							JSxPASAlertMessage(aTextAlert);

							// $('.xWPASConfirmAlertMessage').off('click');
							// $('.xWPASConfirmAlertMessage').on('click', function() {
							// 	setTimeout(function() {
							// 		JSxPASNextStep(aModalText);
							// 	}, 500);
							// });
						}else{
							JSxPASNextStep(aModalText);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('jqXHR: ' + jqXHR + ' textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
					}
				});
				break;
		}
	});

	$('.xWPASBTPrevious').off('click');
	$('.xWPASBTPrevious').on('click',function(){
		var tPageType = String($('#oetPASTypePage').val());
		switch(tPageType){
			case "2":
				JSvPASCallPageMain($('#oetPASDocNoPrevious').val()); // กลับไปเอกสารล่าสุด
				$('#oetPASDocNoPrevious').val(''); // ลบเลขที่เอกสารก่อนหน้า
				break;
			// case "3":
			// 	$('.xWPASBTNext').data('page', 2);
			// 	$('.xWPASBTNext').attr('data-page', 2);
			// 	$('#oetPASTypePage').val('2');
			// 	JSxPASControlButton(); // เปิด/ปิด ปุ่มต่างๆ
			// 	$('.xWPASBTPrevious').attr('disabled',true); // ปิดปุ่มย้อนกลับ

			// 	var tPageCurrent = $('.xWPagePdtAdjStkChk .active').text();
			// 	JSvPASCallDataTable(tPageCurrent,'2'); // เรียกตาราง เพื่อให้มันดึงข้อมูล เคลื่อนไหวหลังตรวจนับ StkCard ใหม่ล่าสุดเสมอ
			// 	break;
			case "4":
				$('.xWPASBTNext').data('page', 3);
				$('.xWPASBTNext').attr('data-page', 3);
				$('#oetPASTypePage').val('3');
				JSxPASControlButton(); // เปิด/ปิด ปุ่มต่างๆ
				$('.xWPASBTPrevious').attr('disabled',true); // ปิดปุ่มย้อนกลับ

				var tPageCurrent = $('.xWPagePdtAdjStkChk .active').text();
				JSvPASCallDataTable(tPageCurrent,'3'); // เรียกตาราง เพื่อให้มันดึงข้อมูล เคลื่อนไหวหลังตรวจนับ StkCard ใหม่ล่าสุดเสมอ
				break;
			case "5":
				JSvPASCallPageMain($('#oetPASDocNoPrevious').val()); // กลับไปเอกสารล่าสุด
				$('#oetPASDocNoPrevious').val(''); // ลบเลขที่เอกสารก่อนหน้า
				break;
		}
	});

	$('.xWPASBTSave').off('click');
    $('.xWPASBTSave').on('click',function(){
		var tTypePage   = String($('#oetPASTypePage').val());
		switch(tTypePage){
			case "1":
				var aTextChkDateTime = {
					tHead	: '<?=language('document/pdtadjstkchk', 'tModalHeadCheckDateTime')?>',
					tDetail	: '<?=language('document/pdtadjstkchk', 'tModalTextCheckDateTime')?>',
					nType	: 1
				};
				var aTextConfirmCode = {
					tHead	: '<?=language('document/pdtadjstkchk', 'tModalHeadConfirmCode')?>',
					tDetail	: '',
					nType	: 3
				};
				JSxPASCheckDateTime(aTextChkDateTime,aTextConfirmCode);
				break;
			case "3":
				JSxPASNextStepConfirmCode("3");
		}
	});
	
	$('.xWPASBTCancel').off('click');
	$('.xWPASBTCancel').on('click',function(){
		var aModalText = {
			tHead	: '<?=language('common/systems', 'tModalHeadDocumentCancel')?>',
			tDetail	: '<?=language('common/systems', 'tModalTextDocumentCancel')?>',
			nType	: 1
		};
		JSxPASAlertMessage(aModalText);
		$('.xWPASConfirmAlertMessage').off('click');
		$('.xWPASConfirmAlertMessage').on("click",function(){
			JSxPASNextStepConfirmCode("Cancel");
		});
	});

	$('.xWPASBTApprove').off('click');
	$('.xWPASBTApprove').on('click',function(){
		JSxPASNextStepConfirmCode("Approve");
	});

	$('.xWPASBTReport').off('click');
	$('.xWPASBTReport').on('click',function(){
		var tStaDocTyp  = String($('#oetPASIuhDocType').val());     //ประเภทเอกสาร 1=ใบย่อย , 2=ใบรวม
		var tPageType   = String($('#oetPASTypePage').val());
		if(tStaDocTyp == '1'){
			if( tPageType == '5' ){
				$('#odvPASMutiSelectReport1').hide();
				$('#odvPASMutiSelectReport2').hide();
				$('#odvPASMutiSelectReport5').show();
				$('#orbPASReportList_7').attr('checked',true);
			}else{
				$('#odvPASMutiSelectReport1').show();
				$('#odvPASMutiSelectReport2').hide();
				$('#odvPASMutiSelectReport5').hide();
				$('#orbPASReportList_3').attr('checked',true);
			}
		}else{
			$('#odvPASMutiSelectReport1').hide();
			$('#odvPASMutiSelectReport2').show();
			$('#odvPASMutiSelectReport5').hide();
			$('#orbPASReportList_5').attr('checked',true);
		}
		$('#ohdRptDocNo').val($('#oetPASDocNo').val());
		$('#oetPASReportDocDate').val($('#oetPASDocDate').val());

		$('#odvPASModalReport').modal('show');

		// var tPageType = String($('#oetPASTypePage').val());
		// switch(tPageType){
		// 	case "1": //ปริ้นเอกสารใบย่อย
		// 		$('#ohdRptDocNo').val($('#oetPASDocNo').val());
		// 		$('#oetPASReportDocDate').val($('#oetPASDocDate').val());
		// 		$('#odvPASModalReport').modal('show');
		// 		break;
		// 	case "3": 
		// 	case "4": //ปริ้นเอกสารใบรวม
		// 		JSxPASCallStimulReport();
		// 		break;
    	// }
	});

	$('.xWPASModalConfirmReport').off('click');
	$('.xWPASModalConfirmReport').on('click',function(){

		var tReportName = $("input[name='orbPASReportList']:checked").val();
		var tCallType 	= $('#odvCNTCallType').val();
		var tParameter 	= $('#odvCNTParameter').val();

		if(tReportName == 'Frm_424'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtChkStk&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_425'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtPhysicalChkStk&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_427'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtChkStkDif&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_428'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptPdtReChkDT&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_433'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtStkChecking&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_432'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtStkCheckBylocation&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else if(tReportName == 'Frm_440'){
			$('#ofmPASB4View').attr('action','<?=$tBase_url?>?route=rptAllPdtNotExist&calltype='+tCallType+'&Param='+tParameter);
			$('#ofmPASB4View').submit();
			$('#ofmPASB4View').attr('action','javascript:void(0)');
		}else{
			var tPageType = String($('#oetPASTypePage').val());
			switch(tPageType){
				case "1": //ปริ้นเอกสารใบย่อย
					var tDocumentID  = $('#ohdRptDocNo').val(); //รับค่ามาจากหน้าเลือกรูปแบบ report
					var tCompCode    = $('#oetPASCmpCode').val();
					var aInfor = [
						{"SP_nLang"		: '1'},             			// ภาษา
						{"SP_tCompCode"	: tCompCode},       			// รหัสบริษัท
						{"SP_tDocNo"	: tDocumentID},      			// เลขที่เอกสาร
						{"SP_DocName"	: tReportName}   				// ชื่อเอกสาร
					];
					window.open("<?=$tBase_url;?>formreport/ReportFamily?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
					break;
			}
		}

	});

	// $('#osmConfirmRabbit').off('click');
	// $('#osmConfirmRabbit').on('click',function(){
	// 	var tDocumentID  	= $('#oetPASDocNo').val();
	// 	var tCompCode    	= $('#oetPASCmpCode').val();
	// 	var tUrl 			= $('#oetPASBaseURL').val();
	// 	var aInfor = [
	// 		{"SP_nLang"     : '1' },             // ภาษา
	// 		{"SP_tCompCode" : tCompCode },       // รหัสบริษัท
	// 		{"SP_tDocNo"    : tDocumentID }      // เลขที่เอกสาร
	// 	];
	// 	window.open(tUrl + "formreport/ChkStkReport?infor=" + JCNtEnCodeUrlParameter(aInfor), '_blank');
	// });

	$('#obtBrowseReportDocuments').off('click');
	$('#obtBrowseReportDocuments').on('click',function(){
		var tDocType = $('#oetPASIuhDocType').val();
		oPASBrwReportPdtChkHD.Where.Condition = [" AND TCNTPdtChkHD.FTIuhDocType='"+tDocType+"' "];
		JCNxBrowseData('oPASBrwReportPdtChkHD');
		$('#odvPASModalReport').modal('hide');
	});

	var oPASBrwReportPdtChkHD = {
        Title 		: ['document/pdtadjstkchk','tPASHeadMenu'],
		Table		: {Master:'TCNTPdtChkHD',PK:'FTIuhDocNo'},
		Where 		: {
			Condition : []
		},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASSearchDocNo','tPASSearchDate'],
            DataColumns		: ['FTIuhDocNo','CONVERT(VARCHAR(10),FDIuhDocDate,121) AS FDIuhDocDate'],
            ColumnsSize  	: ['20%','65%'],
            Perpage			: 20,
			OrderBy			: ['FTIuhDocNo DESC'],
			SearchLike	    : ['FTIuhDocNo','FDIuhDocDate']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASSetReport',
            ArgReturn       : ['FTIuhDocNo','FDIuhDocDate']
		},
		// DebugSQL : 'true'
	};

	function JSxPASSetReport(oElem){
		var aPackData = JSON.parse(oElem);
		$('#ohdRptDocNo').val(aPackData[0]['FTIuhDocNo']);
		$('#oetPASReportDocDate').val(aPackData[0]['FDIuhDocDate']);
		$('#odvPASModalReport').modal('show');
	}

	// Create By: Napat(Jame) 05/08/2020
	$('.xWPASBTSearchDO').off('click');
	$('.xWPASBTSearchDO').on('click',function(){
		$('.xWPASModalSearchTitle').html('');
		$('#odvPASModalSearchHD').html('');
		$('#odvPASModalSearchDT').html('');
		JSxPASCallSearchDO();
		$('#odvPASModalSearch').modal('show');
	});

	// Create By: Napat(Jame) 05/08/2020
	$('.xWPASBTSearchAutoReceive').off('click');
	$('.xWPASBTSearchAutoReceive').on('click',function(){
		$('.xWPASModalSearchTitle').html('');
		$('#odvPASModalSearchHD').html('');
		$('#odvPASModalSearchDT').html('');
		JSxPASCallSearchAutoReceive();
		$('#odvPASModalSearch').modal('show');
	});

	
</script>
