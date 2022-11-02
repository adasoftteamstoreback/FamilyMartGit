<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/document/assets/css/orderingscreen/orderingscreen.css?v=<?php echo date("dmyhis"); ?>">
<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/localcss/input.css?v=<?php echo date("dmyhis"); ?>">
<div class="col-xs-12 col-md-12 col-lg-12 odvPanelhead">
	<div class="row">
		<div class="col-xs-12 col-md-3 col-lg-3">
			<p class="ospTextHeadMenu"><?php echo language('document/orderingscreen','tODSTitle'); ?></p>
		</div>
		<div class="col-xs-12 col-md-9 col-lg-9">
			<div class="odvHeadRight">
				<button class="btn xCNBTNActionSearch" id="obtODSSearch" style="width:auto;" type="button"><?php echo language('document/orderingscreen','tODSBtnSearch'); ?></button>
				<button class="btn xCNBTNActionInsert" id="obtODSNew" style="width:auto;" type="button"><?php echo language('document/orderingscreen','tODSBtnNew'); ?></button>
				<button class="btn xCNBTNActionSave" id="obtODSSave" style="width:auto;" type="button"><?php echo language('document/orderingscreen','tODSBtnSave'); ?></button>
				<button class="btn xCNBTNActionCancel" id="obtODSCancel" style="width:auto;" type="button"><?php echo language('document/orderingscreen','tODSBtnCancel'); ?></button>
				<?php if($nChkSugQty == 1){ ?>
				<button class="btn xCNBTNActionSave" id="obtODSCopySGOQTY" style="background-color:#fb8642 !important;width:auto;color: #6f280d !important;" type="button"><?php echo language('document/orderingscreen','tODSBtnCopySGOQTY'); ?></button>
				<?php } ?>
				<button class="btn xCNBTNActionApprove xCNHide" id="obtODSConfirmOrder" style="width:auto;" type="button"><?php echo language('document/orderingscreen','tODSBtnConfirmOrder'); ?></button>
				<!-- <button class="btn xCNBTNActionClose" id="obtODSCloseBrowser" type="button"> <?=language('common/systems', 'tBTNClose')?> </button> -->
			</div>
		</div>
	</div>
</div>

<div id="odvODSContentMain"></div>

<!--modal approve -->
<div class="modal fade" id="odvModalApprove" data-backdrop="static" data-keyboard="false">
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
				<button type="button" class="btn xCNBTNActionConfirm xCNBTNActionConfirmApprove" data-dismiss="modal">
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

<!--modal List search -->
<div class="modal fade" id="odvModalListSearch" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
                <div class="row">
                    <div class="col-xs-3 col-sm-6 col-md-6 col-lg-6">
                        <label class="xCNTextModalHeard"><?php echo language('document/turnoffsuggestorder', 'tModalListSearch')?></label>
                    </div>
                    <div class="col-xs-9 col-sm-6 col-md-6 col-lg-6 text-right">
                        <button type="button" style="border-color : transparent !important;" class="btn xCNBTNActionConfirm xCNBTNActionListSearch">
                            <?php echo language('common/systems', 'tModalConfirm'); ?>
                        </button>
                        <button type="button" class="btn xCNBTNActionClose" data-dismiss="modal">
                            <?php echo language('common/systems', 'tModalCancel'); ?>
                        </button>
                    </div>
                </div>
			</div>
			<div class="modal-body" id="odvModalBodyListSearch"></div>
		</div>
	</div>
</div>
<!-- end modal List search -->


<!--modal Close Browser -->
<div class="modal fade" id="odvModalCloseBrowser" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadNewform')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmApprove" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalNewformText')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xCNBTNClosebrowser">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Close Browser -->

<!--modal alertmessage -->
<div class="modal fade" id="odvModalAlertMessage" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xWODSModalAlertMessageHead"><?php echo language('common/systems', 'tModalHeadApprove')?></label>
			</div>
			<div class="modal-body xWODSModalAlertMessageBody">
                
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xWODSBtnOrderProduct" data-dismiss="modal" style="width: auto;">
					<?php echo language('document/orderingscreen', 'tODSBtnOrderProduct'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionConfirm xWODSConfirmAlertMessage" data-dismiss="modal">
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

<input type="text" class="xCNHide" id="oetODSStaDocSuccess" value="<?=$aDataNotSubmit['nStaQuery'];?>">
<input type="text" class="xCNHide" id="oetODSTexttModalHeadDocComplete" value="<?php echo language('common/systems', 'tModalHeadDocComplete')?>">

<script src="<?=$tBase_url?>application/modules/document/assets/src/orderingscreen/jorderingscreen.js?v=<?php echo date("dmyhis"); ?>"></script>
<?php include('script/jorderingscreen.php');?>