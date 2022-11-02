<?php
    if($FTXohDocNo == "" || $FTXohDocNo == null){
		$dODSOrderDate						= date_format(date_create($FDXohDocDate),"d/m/Y");
        $tODSDocNo                          = '##########-#####';
        $nODSGrandTotalSKU                  = '0';
		$nODSGrandTotalAmount               = '0';
		$nODSStaPrcDoc						= '';
		$nODSStaDoc							= 0;
		$nODSCheckDataTemp					= 0;
    }else{
		$tODSDocNo                          = $FTXohDocNo;
		$dODSOrderDate						= date_format(date_create($FDXohDocDate),"d/m/Y");
		$nODSStaPrcDoc						= $FTXohStaPrcDoc;
		$nODSStaDoc							= $FTXohStaDoc;
		$nODSCheckDataTemp					= 1;
	}

	if(!isset($tDataNotSubmit)){
		$tDataNotSubmit = 1;
	}

	if($nODSStaPrcDoc == 1){
		$nODSStatus = 1;
	}else{
		if($nODSStaDoc == 3){
			$nODSStatus = 3;
		}else{
			$nODSStatus = '';
		}
	}

	$tReplace    = str_replace('/', '-', $dODSOrderDate);
	$nDays 		 = date_format(date_create($tReplace),'w');

?>


<input type="text" id="oetODSSelectSectionType" class="xCNHide" value="NEW">
<div class="col-xs-12 col-md-12 col-lg-12">
    <div class="odvPanelcontent row xWForm-GroupDatePicker" style="width:100%;">
		
		<input type="text" class="form-control xCNHide" id="oetRabbitOrderingScreenUpdateApprove" value="<?php echo $tROUTE_omnOrderingScreen_rabbitfail; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSRouteSuccessApprove" value="<?php echo $tROUTE_omnOrderingScreen_rabbitSuccess; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSStaPrcDoc" value="<?php echo $nODSStaPrcDoc; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSStaDoc" value="<?php echo $nODSStaDoc; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSCheckDataTemp" value="<?php echo $nODSCheckDataTemp; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSCheckNotSubmit" value="<?php echo $tDataNotSubmit; ?>">
		<input type="text" class="form-control xCNHide" id="oetODSStaEdit" value="0">

		<div class="col-xs-12 col-md-12 col-lg-12">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="row">
						<div class="col-xs-12 col-md-2 col-lg-2" style="padding-top:7px;">
							<span class="ospDocumentno"> <?php echo language('document/orderingscreen','tODSOrderDate'); ?> : </span>
						</div>
						<div class="col-xs-12 col-md-5 col-lg-5">
							<div class="row">
								<div class="col-xs-5 col-md-4 col-lg-4">
									<input class="form-control xWDatepicker xCNDontKey" type="text" id="oetODSOrderDate" name="oetODSOrderDate" data-oldval="<?php echo $dODSOrderDate; ?>" value="<?php echo $dODSOrderDate; ?>" autocomplete="off">
								</div>
								
								<div class="col-xs-1 col-md-1 col-lg-1" style="padding-top:7px;">
									<span id="ospODSShowDateText"><?php echo language('document/orderingscreen','tODSDate'.$nDays); ?></span>
								</div>
								<div class="col-xs-6 col-md-7 col-lg-7" style="text-align: right;">
									<button class="btn xCNBTNActionApprove" id="obtODSLoadOrder" style="width:auto;margin-left:18px;" type="button"><?php echo language('document/orderingscreen','tODSBtnLoadOrder'); ?></button>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-5 col-lg-5">
							<div id="odvODSContentDetailSUMMARY"></div>
						</div>
					</div>

					<div class="row" style="margin-top:10px;">
						<div class="col-xs-6 col-md-2 col-lg-2" style="padding-top:7px;">
							<span class="ospDocumentdate"> <?php echo language('document/orderingscreen','tODSOrderNo'); ?> : </span>
						</div>
						<div class="col-xs-6 col-md-3 col-lg-2" style="padding-top:7px;">
							<span class="ospDocumentdateValue"><?php echo $tODSDocNo; ?></span>
                            <input type="text" class="xCNHide" id="oetODSDocNo" value="<?php echo $FTXohDocNo; ?>">
						</div>
						<div class="col-xs-6 col-md-5 col-lg-6 text-right" style="padding-top:7px;">
							<?php echo language('document/orderingscreen','tODSTBStatusPrcDoc'.$nODSStatus); ?>
						</div>
						<div class="col-xs-6 col-md-2 col-lg-2 text-right">
							<div class="form-group">
								<select class="form-control" id="osmODSLimitRecord" name="osmODSLimitRecord">
									<option value="10" selected disabled><?php echo language('common/systems', 'tLimitrecord'); ?></option>
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

			<ul class="nav nav-tabs" style="margin-left:-20px;margin-right:-20px;">
				<li class="active"><a class="xWTabCallDetails" data-toggle="tab" data-sec="NEW" href="#odvODSContentNEW"><?php echo language('document/orderingscreen','tODSTitleNEW'); ?></a></li>
				<li><a class="xWTabCallDetails" data-toggle="tab" data-sec="PROMOTION" href="#odvODSContentPROMOTION"><?php echo language('document/orderingscreen','tODSTitlePROMOTION'); ?></a></li>
				<li><a class="xWTabCallDetails" data-toggle="tab" data-sec="TOP1000" href="#odvODSContentTOP1000"><?php echo language('document/orderingscreen','tODSTitleTOP1000'); ?></a></li>
				<li><a class="xWTabCallDetails" data-toggle="tab" data-sec="OTHER" href="#odvODSContentOTHER"><?php echo language('document/orderingscreen','tODSTitleOTHER'); ?></a></li>
				<li><a class="xWTabCallDetails" data-toggle="tab" data-sec="SUMMARY" href="#odvODSContentSUMMARY"><?php echo language('document/orderingscreen','tODSTitleSUMMARY'); ?></a></li>
			</ul>

			<div class="tab-content" style="margin-left:-20px;margin-right:-20px;">
				<div id="odvODSContentNEW" class="tab-pane fade in active">
					<div id="odvODSContentDetailNEW"></div>
				</div>
				<div id="odvODSContentPROMOTION" class="tab-pane fade">
					<div id="odvODSContentDetailPROMOTION"></div>
				</div>
				<div id="odvODSContentTOP1000" class="tab-pane fade">
					<div id="odvODSContentDetailTOP1000"></div>
				</div>
				<div id="odvODSContentOTHER" class="tab-pane fade">
					<div id="odvODSContentDetailOTHER"></div>
				</div>
				<div id="odvODSContentSUMMARY" class="tab-pane fade" style="overflow-x: hidden;overflow-y: scroll !important;">
					<!-- <div id="odvODSContentDetailSUMMARY"></div> -->
					<div id="odvODSContentDetailSUMMARY_NEW" style="margin-bottom: 20px;"></div>
					<div id="odvODSContentDetailSUMMARY_PROMOTION" style="margin-bottom: 20px;"></div>
					<div id="odvODSContentDetailSUMMARY_TOP1000" style="margin-bottom: 20px;"></div>
					<div id="odvODSContentDetailSUMMARY_OTHER" style="margin-bottom: 20px;"></div>
					<div id="odvODSContentDetailSUMMARY_ADDON"></div>
				</div>
			</div>

		</div>
        
    </div>
</div>


<div id="odvODSScriptDataTable"></div>
<?php include('script/jorderingscreenMain.php');?>