<!--SetRabbit MQ-->
<input type="hidden" id="ohdRabbitSuggestUpdateApprove" name="ohdRabbitSuggestUpdateApprove" value="<?=$tROUTE_omnTurnOffSuggest_rabbitfail;?>">

<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/document/assets/css/turnoffsuggestorder/turnoffsuggestorder.css">

<!--เก็บ route เอาไว้ เพราะมันต้องเอาไปใช้ในไฟล์ .js php เข้าถึงไม่ได้-->
<input type="hidden" name="oetHiddenrouteSelect" id="oetHiddenrouteSelect">
<input type="hidden" name="oetHiddenrouteSelectHD" id="oetHiddenrouteSelectHD" value='<?=$tROUTE_omnTurnOffSuggest_selectHD?>'>

<!--Flag save-->
<input type="hidden" id="ohdFlagSave" name="ohdFlagSave" value='unsave'>

<div class="col-lg-12 col-sm-12 col-xs-12 odvPanelhead">
	<div class="row">
		<div class="col-lg-5 col-sm-3">
    		<p class="ospTextHeadMenu"> <?=language('document/turnoffsuggestorder', 'tTSOHeadMenu')?> </p>
		</div>
		<div class="col-lg-7 col-sm-9">
			<div class="odvHeadRight">
				<!-- <button onclick="JSxClosebrowser('<?=$tROUTE_omnTurnOffSuggest_newform?>','close')"	class="btn xCNBTNActionClose" 							type="button"> <?=language('common/systems', 'tBTNClose')?> </button> -->
				<button onclick="JSxBTNListSearch('<?=$tROUTE_omnTurnOffSuggest_searchlist?>')" 	class="btn xCNBTNActionSearch" 							type="button"> <?=language('common/systems', 'tBTNSearch')?>  </button>
				<button onclick="JSxBTNSavePDT('<?=$tROUTE_omnTurnOffSuggest_save?>')" 				class="btn xCNBTNActionSave xCNSaveDocument" 			type="button"> <?=language('common/systems', 'tBTNSave')?> </button>
				<button onclick="JSxBTNNewPDT('<?=$tROUTE_omnTurnOffSuggest_newform?>','cancel')" 	class="btn xCNBTNActionCancel xCNSuggestorderCancel" 	type="button"> <?=language('common/systems', 'tBTNCancel')?> </button>
				<button onclick="JSxBTNNewPDT('<?=$tROUTE_omnTurnOffSuggest_newform?>','new')" 		class="btn xCNBTNActionInsert xCNSuggestorderInsert" 	type="button"> <?=language('common/systems', 'tBTNInsert')?> </button>
				<button onclick="JSxBTNApprovePDT('<?=$tROUTE_omnTurnOffSuggest_approve?>')" 		class="btn xCNBTNActionApprove" 						type="button"> <?=language('common/systems', 'tBTNApprove')?> </button>
			</div>
		</div>
	</div>
</div>

<div class="col-lg-12 col-sm-12 col-xs-12">
    <div class="odvPanelcontent row">

        <div class="col-xs-12 col-md-12 col-lg-4">
			<div class="">
				<label class="xCNLabelFrm"><?=language('common/systems', 'tLabelSearch')?></label>
				<div class="input-group" style="display: inline-flex;">
					<input type="text" class="form-control xCNInputWithoutSingleQuote" id="oetSearchTSO" name="oetSearchTSO" onkeypress="Javascript:if(event.keyCode==13 ) JSxSearchTSO()" placeholder="<?=language('common/systems', 'tLabelInputSearch')?>">
					<span class="input-group-btn">
						<button class="btn xCNBtnSearch" type="button" onclick="JSxSearchTSO();">
							<img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
						</button>
					</span>
				</div>
			</div>
		</div>
		
		<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12">
					<div class="row">

						<div class="col-lg-6 col-md-6 col-xs-6" style="margin: 10px auto;">
							<span class="ospDocumentno"><?=language('document/turnoffsuggestorder', 'tTSODocumentno')?></span>
							<span class="ospDocumentnoValue" id="ospDocumentnoValue"> SGBCHYYMM-######</span>
							<input type="hidden" id="ohdDocumentno" name="ohdDocumentno">
							<input type="hidden" id="ohdDocumentnoForSearch" name="ohdDocumentnoForSearch">
							&nbsp;&nbsp;
							<span class="ospDocumentdate"><?=language('document/turnoffsuggestorder', 'tTSODate')?></span>
							<span class="ospDocumentdateValue"> <?=date('d/m/Y')?> </span>
						</div>
						
						<div class="col-lg-6 col-md-6 col-xs-6">
							<div class="row">
								<div class="col-lg-9 col-md-9 col-xs-7">
									<div class="ospStatusapprove">
										<input type="hidden" id="ohdStaprcDoc" name="ohdStaprcDoc">
										<img class="oimImageapprove" src="<?=$tBase_url?>application/modules/common/assets/images/icons/NoneApproveIcon.png">
										<span class="ospTextApprove"> <?=language('document/turnoffsuggestorder', 'tTSOTextNoneApprove')?> </span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-xs-5">
									<div class="">
										<select class="form-control" id="osmLimitRecord" name="osmLimitRecord">
											<option value="10" selected disabled><?=language('common/systems', 'tLimitrecord')?></option>
											<option value="10">10</option>
											<option value="20">20</option>
											<option value="30">30</option>
											<option value="40">40</option>
										</select>
									</div>
								</div>
							</div>
						</div>

					</div>					
				</div>

			</div>

			<!--Table-->
			<div id="odvContentTable"></div>
			<!--end Table-->
			
		</div>
        
    </div>
</div>

<script>
	$('title').html("Turn Off Suggest");

	//function select - autoload
	var tNamerouteselect = '<?php echo $tROUTE_omnTurnOffSuggest_select?>';
	$('#oetHiddenrouteSelect').val(tNamerouteselect);
	// JSxSelectDataintoTable('X99');

	// //Control button
	// JSxControlButtonBar();

	// $('#osmLimitRecord').change(function() {
	// 	JSxSelectDataintoTable();
	// });

</script>
<script src="<?=$tBase_url?>application/modules/document/assets/src/turnoffsuggestorder/jturnoffsuggestorder.js?v=<?php echo date("dmyhis"); ?>"></script>