<?php
if(empty($aHD)){
    $tStatusapprove = 0;
    $tStaDoc        = 0;
    $tDocumentDates  = date('d/m/Y');
}else{
    $tStatusapprove = $aHD[0]['FTPthStaPrcDoc'];
    $tStaDoc        = $aHD[0]['FTPthStaDoc'];
    $tDocumentDates = $aHD[0]['AFDPthDocDate'];
}

//กดปิดไปก่อนยังทำไม่เสร็จ
if($tFoundLastDatanoneapprove == 'true'){
    $tTempDocNo = $tDocNoNew;
    $tDocumentDate  = date('d/m/Y');
}else{
    if($aDataList['rtCode'] == 800){
        $tTempDocNo     = 'SGBCHYYMM-######';
        $tDocumentDate  = date('d/m/Y');
    }else{
        $tTempDocNo     = $aDataList['raItems'][0]['FTPthDocNo']; 
        $tDocumentDate  = $aHD[0]['AFDPthDocDate'];
    }
}

//สำหรับ insert ลง HD ครั้งแรก โดยที่ไม่ต้องขึ้นเอกสารล่าสุดยังไม่สมบูรณ์
$tSessionCheckFistInsert = $_SESSION['TurnoffFirtInsert'];

?>

<style>
    #otbTableSuggestorder>tbody>tr>td{
        line-height : 0px !important;
    } 

    input[type="text"] {
        font-family: TAHOMA_0;
        font-size: 12px !important;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="xWForm-GroupDatePicker">

            <div class="table-scroll">
                <table id="otbTableSuggestorder" class="table table-striped xCNTableHead xCNTableData xCNTableResponsive xCNTableResize">
                    <thead>
                        <tr>
                            <th style="width:6%;  text-align: center; cursor: context-menu;"><?=language('document/turnoffsuggestorder', 'tTSOTableNo')?> </th>
                            <th style="width:13%; text-align: center; cursor: pointer;" data-sortby='FTPdtCode' class="xCNSortDatacolumn"><?=language('document/turnoffsuggestorder', 'tTSOTableProductcode')?></th>
                            <th style="width:13%; text-align: center; cursor: pointer;" data-sortby='FTPdtBarCode' class="xCNSortDatacolumn"><?=language('document/turnoffsuggestorder', 'tTSOTableBarcode')?></th>
                            <th style="text-align: center; cursor: pointer;" data-sortby='FTPdtName' class="xCNSortDatacolumn"><?=language('document/turnoffsuggestorder', 'tTSOTableProductname')?></th>
                            <th style="width:14%; text-align: center; cursor: pointer;" data-sortby='FDPdtStartdate' class="xCNSortDatacolumn"><?=language('document/turnoffsuggestorder', 'tTSOTableStartdate')?></th>
                            <th style="width:14%; text-align: center; cursor: pointer;" data-sortby='FDPdtEnddate' class="xCNSortDatacolumn"><?=language('document/turnoffsuggestorder', 'tTSOTableEnddate')?></th>
                            <th style="width:80px;  text-align: center; cursor: context-menu;"><?=language('document/turnoffsuggestorder', 'tTSOTableDelete')?></th>
                            <!-- <th style="width:80px;  text-align: center;"><?=language('document/turnoffsuggestorder', 'tTSOTableManage')?></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($aDataList['rtCode'] == 1){ ?>
                            <?php foreach($aDataList['raItems'] AS $key=>$aValue){  ?>

                                <?php 
                                    //packdata
                                    $aPackdata = array(
                                        'trouteedit'    => $tROUTE_omnTurnOffSuggest_update,
                                        'troutedelete'  => $tROUTE_omnTurnOffSuggest_delete, 
                                        'nPageCurrent'  => $aDataList['rnCurrentPage'],
                                        'tDocument'     => $aValue['FTPthDocNo'],
                                        'nPDTCode'      => $aValue['FTPdtCode'],
                                        'nPDTName'      => $aValue['FTPdtName'],
                                        'nPDTSeq'       => $aValue['FNPtdSeqNo']
                                    );
                                    $aResultdata = JSON_encode($aPackdata);

                                    $oEventDelete   = 'JSvCallSuggestorderDelete('.$aResultdata.')';
                                    $oEventEdit     = 'JSvCallPageTurnoffsuggestorderEdit(this, event,'.$aResultdata.')';
                                    $oEventSave     = 'JSvTurnoffsuggestorderDataSourceSaveOperator(this, event,'.$aResultdata.')';
                                    $oEventCancel   = 'JSvPageTurnoffsuggestorderDataSourceCancelOperator(this, event)';
                                ?>
                                
                                <tr class="xWTurnoffsuggestorderDataSource xCNTableTrClickActive" id="otrSuggestorder<?=$aValue['FNPtdSeqNo'];?>" data-seq="<?=$aValue['FNPtdSeqNo'];?>"> <!-- onclick="JSxHighLightTable(this)" -->    
                                    <td nowrap class="text-left">
                                        <input class="form-control xCNEditinlineHiddenFrom" type="text" disabled="true" value="<?=$aValue['FNPtdSeqNo']?>">
                                    </td>
                                    <td nowrap class="text-left">
                                        <div class="field a-field a-field_a1 page__field" style="padding: 10px;">
                                            <input id="oetPDTCode<?=$aValue['FNPtdSeqNo']; ?>" name="oetPDTCode<?=$aValue['FNPtdSeqNo']; ?>" class="inputs field__input a-field__input xWSuggestorderPDTCode xCNInputNumericWithDecimal" type="text" maxlength=20 value="<?=$aValue['FTPdtCode']?>" data-oldpdtcode="<?=$aValue['FTPdtCode']?>">
                                        </div>
                                    </td>
                                    <td nowrap class="text-left xWSuggestorderBARCode">
                                        <input style="min-width: 130px;" id="oetBarcode<?=$aValue['FNPtdSeqNo']; ?>" name="oetBarcode<?=$aValue['FNPtdSeqNo']; ?>" class="form-control xCNEditinlineHiddenFrom" type="text" disabled="true" value="<?=$aValue['FTPdtBarCode']?>">
                                    </td>
                                    <td nowrap class="text-left xWSuggestorderPDTName">
                                        <input style="min-width: 200px;" id="oetPDTName<?=$aValue['FNPtdSeqNo']; ?>" name="oetPDTName<?=$aValue['FNPtdSeqNo']; ?>" class="form-control xCNEditinlineHiddenFrom" type="text" disabled="true" value="<?=$aValue['FTPdtName']?>">
                                    </td>
                                    <td nowrap class="text-left xWSuggestorderSTRDate">
                                        <div class="field a-field a-field_a1 page__field" style="padding: 10px;">
                                            <?php 
                                                $StartDate = explode("-",$aValue['FDPdtStartdate']); 
                                                $StartDate = $StartDate[2].'-'.$StartDate[1].'-'.$StartDate[0];
                                            ?>
                                            <input id="oetStartdate<?=$aValue['FNPtdSeqNo']; ?>" name="oetStartdate<?=$aValue['FNPtdSeqNo']; ?>" class="inputsChange field__input a-field__input xWDatepicker xCNInputAddressNumber" type="text" value="<?=$StartDate?>" autocomplete="off" maxlength="10">
                                        </div>
                                    </td>
                                    <td nowrap class="text-left xWSuggestorderENDDate">
                                        <div class="field a-field a-field_a1 page__field" style="padding: 10px;">
                                            <?php 
                                                $tEndDate = explode("-",$aValue['FDPdtEnddate']); 
                                                $tEndDate = $tEndDate[2].'-'.$tEndDate[1].'-'.$tEndDate[0];
                                            ?>
                                            <input id="oetEnddate<?=$aValue['FNPtdSeqNo']; ?>" name="oetEnddate<?=$aValue['FNPtdSeqNo']; ?>" class="inputsChange field__input a-field__input xWDatepicker xCNInputAddressNumber" type="text" value="<?=$tEndDate?>" autocomplete="off"  maxlength="10">
                                        </div>
                                    </td>
                                    <td style="text-align: center; vertical-align:middle;">                         
                                        <img class="xCNIconTable xWIconDelete" src="<?=$tBase_url?>application/modules/common/assets/images/icons/delete.png"      onclick='<?=$oEventDelete?>'>
                                    </td>
                                    <!-- <td style="text-align: center; vertical-align:middle;">
                                        <img class="xCNIconTable xWIconEdit"                src="<?=$tBase_url?>application/modules/common/assets/images/icons/edit.png"        onClick='<?=$oEventEdit?>'>
                                        <img class="xCNIconTable xWIconSave     hidden"     src="<?=$tBase_url?>application/modules/common/assets/images/icons/save.png"        onclick='<?=$oEventSave?>'>
                                        <img class="xCNIconTable xWIconCencel   hidden"     src="<?=$tBase_url?>application/modules/common/assets/images/icons/reply_new.png"   onClick='<?=$oEventCancel?>'>
                                    </td> -->
                                </tr>
                            <?php } ?>
                        <?php }else{ ?>
                            <tr class="otrNoData">
                                <td nowrap colspan="7" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <img class="oimImageInsert xCNImageInsert" src="<?=$tBase_url?>application/modules/common/assets/images/icons/add-circular2.png">
                            </td>
                            <td style="padding: 0px;">
                                <div class="field a-field a-field_a1 page__field xCNInputorBarcode" style="padding: 10px;">
                                    <input class="field__input a-field__input xWInputPdtOrdLot xCNInsertInputPDTorBarcode xCNInputWithoutSingleQuote" id="oetInputCodeorBarcode" name="oetInputCodeorBarcode" placeholder="<?=language('common/systems', 'tPlaceholderProductorbarcode')?>" maxlength="25" autocomplete="off">
                                </div>
                            </td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="row" style="margin-top:10px;">
	<!-- เปลี่ยน -->
	<div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
    </div>
    <!-- เปลี่ยน -->
    <?php if($aDataList['rtCode'] == 1){ ?>
        <div class="col-md-6">
            <div class="xWPageTurnoffsuggestorder btn-toolbar pull-right"> <!-- เปลี่ยนชื่อ Class เป็นของเรื่องนั้นๆ --> 
                <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                <button type="button" onclick="JSvClickPage('previous','<?=$tDocumentID?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                </button>
                <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?> <!-- เปลี่ยนชื่อ Parameter Loop เป็นของเรื่องนั้นๆ --> 
                    <?php 
                        if($nPage == $i){ 
                            $tActive        = 'active'; 
                            $tDisPageNumber = 'disabled';
                        }else{ 
                            $tActive        = '';
                            $tDisPageNumber = '';
                        }
                    ?>
                    <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <button onclick="JSvClickPage('<?php echo $i?>','<?=$tDocumentID?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                <?php } ?>
                <?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                <button type="button" onclick="JSvClickPage('next','<?=$tDocumentID?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                </button>
            </div>
        </div>
    <?php } ?>
</div>

<!--modal Delete -->
<div class="modal fade" id="odvModalDelete" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalDelete')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmDelete" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalConfirmDeleteItems')?>
                </span>
                <span id="ospConfirmDeleteValue"></span>
				<input type='hidden' id="ohdConfirmIDDelete">
			</div>
			<div class="modal-footer">
				<button id="osmConfirmSingle" type="button" class="btn xCNBTNActionConfirm">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal delete-->

<!--modal approve -->
<div class="modal fade" id="odvModalApprove" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
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
				<button type="button" class="btn xCNBTNActionConfirm xCNBTNActionConfirmApprove">
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

<!--modal confirm new form -->
<div class="modal fade" id="odvModalNewform" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
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
                <button type="button" class="btn xCNBTNActionConfirm xCNBTNCallSaveDocument">
					<?php echo language('common/systems', 'tModalMQBtnConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel xCNBTNActionConfirmNewform xCNBTNDeleteTempNewFrom">
					<?php echo language('common/systems', 'tModalNo'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionClose" data-dismiss="modal">
					<?php echo language('common/systems', 'tBTNCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal confirm new form -->

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

<!--modal List search -->
<div class="modal fade" id="odvModalListSearch" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
                <div class="row">
                    <div class="col-xs-4 col-sm-6 col-md-6 col-lg-6">
                        <label class="xCNTextModalHeard"><?php echo language('document/turnoffsuggestorder', 'tModalListSearch')?></label>
                    </div>
                    <div class="col-xs-8 col-sm-6 col-md-6 col-lg-6 text-right">
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

<!--modal Cancle -->
<div class="modal fade" id="odvModalListCancle" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('document/turnoffsuggestorder', 'tModalListDocumentCancle')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmApprove" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('document/turnoffsuggestorder', 'tModalListDetailDocumentCancle')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xCNBTNActionDocumentCancle">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Cancle -->

<!--modal product Not Found-->
<div class="modal fade" id="odvModalProductNotFound" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadPDTNotFound')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalHeadPDTDetailNotFound')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal" id="obtDataNotFound">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal product Not Found -->

<!--modal DataDuplicate-->
<div class="modal fade" id="odvModalDataDuplicate" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xCNHeadTextModalDataDuplicate"><?php echo language('common/systems', 'tModalHeadDataDuplicate')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModalDataDuplicate" style="display: inline-block;">

                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel xCNBTNActionCancelDuplicate" id="obtFoundDatainTable" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal product Not Found -->

<!--modal DateStart < DateEnd-->
<div class="modal fade" id="odvModalDateStartDateEnd" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xCNHeadTextModalDateStartDateEnd">
                    <?php echo language('common/systems', 'tModalHeadDataDuplicate')?>
                </label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModalDateStartDateEnd" style="display: inline-block;">

                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel xCNBTNActionCancelDateStartDateEnd" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal product Not Found -->

<!--modal Found Data in table-->
<div class="modal fade" id="odvModalFoundDatainTable" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadFoundDataDuplicate')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModalDataDuplicate" id="xCNTextDataDuplicate" style="display: inline-block;">
                    <?php echo language('common/systems', 'tModalTextFoundDataDuplicate')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal"  id="obtFoundDataDupinTable">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Found Data in table-->

<!--modal Document not complete -->
<div class="modal fade" id="odvModalDocumentnotcomplete" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadDocComplete')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalHeadDocComplete')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal" id="obtnotcomplete" onclick="JSxBTNSavePDT('<?=$tROUTE_omnTurnOffSuggest_save?>');">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal Document not complete -->

<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script src="<?=$tBase_url?>application/modules/common/assets/js/global/ColResizable1.6/colResizable-1.6.js?v=<?php echo date("dmyhis"); ?>"></script>
<?php include "script/jturnoffsuggestorder.php"; ?>


<style>
    .xCNHighLightTable{
        background-color : #f0fbed !important;
    }
</style>

<script>        
    //HighLightTable
    function JSxHighLightTable(elem){
        $('#otbTableSuggestorder tbody tr td').removeClass('xCNHighLightTable');
        $(elem).children().addClass('xCNHighLightTable');
    }

    //Resize table
    $(document).ready(function () {

        //เซตวันที่เอกสาร ตามที่เลือก
        if('<?= $tDocumentDate ?>' == ''){
            $('.ospDocumentdateValue').text('<?= $tDocumentDates ?>');
        }else{
            $('.ospDocumentdateValue').text('<?= $tDocumentDate ?>');
        }
        
        sizeControl();

        //กด enter ใน modal ไม่พบข้อมูล
        $('#odvModalProductNotFound').keydown(function(e) {
            var keyCode = e.keyCode || e.which; 
            if(keyCode === 13){
                $('#obtDataNotFound').click();
                $('.xCNInsertInputPDTorBarcode').focus();
            }
        });

       

        //กดตกลง บันทึกเอกสาร
        $('.xCNBTNCallSaveDocument').click(function(){
            var tDocumentID      = $('#ospDocumentnoValue').text();
            $.ajax({
                url     : 'Content.php?route=omnTurnOffSuggest&func_method=FSxCTSOSaveForSearch',
                data    : { 
                    tDocumentID     : tDocumentID
                },
                type    : 'POST',
                success : function(tResult){
                   $('.xCNBTNDeleteTempNewFrom').click();
                   $('#ohdFlagSave').val('save');
                   $('.xCNBTNActionSave').addClass('xCNBTNActionSaveDisable');
                }
            });

        });
        

    });

    $(window).resize(function () {
        sizeControl();
    });

    function sizeControl(){
        var w = window,
			d = document,
			e = d.documentElement,
			g = d.getElementsByTagName('body')[0],
			x = w.innerWidth || e.clientWidth || g.clientWidth,
			y = w.innerHeight || e.clientHeight || g.clientHeight;

        if(x <= 992){ //MD XS 
            var nNewWidth = y - 400;
        }else{ //LG
            var nNewWidth = y - 260;
        }
        $('.table-scroll').css('height',nNewWidth+'px');
    }
    //End Resize table

    //Edit
    var tDocumentID     = '<?=$tDocumentID?>';
    var tLastStaprcDoc  = '<?=$tFoundLastDatanoneapprove;?>';

    if((tDocumentID == '' || tDocumentID == null) && (tLastStaprcDoc == 'true')){


        $tSessionCheckFistInsert = '<?=$tSessionCheckFistInsert?>';
        if($tSessionCheckFistInsert == '' || $tSessionCheckFistInsert == null){
            $('#odvModalDocumentnotcomplete').modal('show');

            //กด enter ใน modal เอกสารไม่สมบูรณ์
            $('#odvModalDocumentnotcomplete').keydown(function(e) {
                var keyCode = e.keyCode || e.which; 
                if(keyCode === 13){
                    $('#obtnotcomplete').click();
                }
            });

            $('#obtnotcomplete').click(function(e) {
                JSxContentLoader('show');
            });
        }else{
            //เพิ่มครั้งเเรก ไม่ต้องโชว์ modal
        }
    }

    if(tDocumentID == '' || tDocumentID == 'null'){
        $('#ospDocumentnoValue').text('<?=$tTempDocNo?>');
    }else{
        $('#ospDocumentnoValue').text(tDocumentID);
        if(tDocumentID != 'SGBCHYYMM-######'){
            $('.ospDocumentdateValue').text('<?=$tDocumentDates?>');
        }
    }

    $('.xCNSuggestorderCancel').hide(); //ปุ่มยกเลิก
    var tStatusapprove  = '<?=$tStatusapprove?>';
    var tStaDoc         = '<?=$tStatusapprove?>';
    $('#ohdStaprcDoc').val(tStatusapprove);
    if(tStatusapprove == 1){ //1:Approve
        $('.oimImageapprove').attr("src",'<?=$tBase_url?>application/modules/common/assets/images/icons/ApproveIcon.png');
        $('.ospTextApprove').text('<?=language('document/turnoffsuggestorder', 'tTSOTextApprove')?>');
        $('.xCNIconTable , .oimImageInsert').addClass('xCNBlockWhenApprove');
        $('.xCNBTNActionApprove').hide();
        $('.xCNBTNActionSave').hide(); //ปุ่มบันทึก
        $('.xCNBTNActionInsert').show(); //ปุ่มเพิ่มใหม่
        $('.xCNSuggestorderCancel').hide(); //ปุ่มยกเลิก

        if(tLastStaprcDoc == 'true'){
            $('.xCNSuggestorderInsert').hide(); 
        }

        //Edit inline
        $('.xWSuggestorderPDTCode').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
        $('.xWDatepicker').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
        $('#oetInputCodeorBarcode').hide();

        $('.field').css('padding','0px');
    }else{ // N/A or 3:cancle
    
        if(tStatusapprove == 3){
            $('.oimImageapprove').attr("src",'<?=$tBase_url?>application/modules/common/assets/images/icons/NoneApproveIcon.png');
        }else{
            $('.oimImageapprove').attr("src",'<?=$tBase_url?>application/modules/common/assets/images/icons/WarningIcon.png');
        }
        $('.ospTextApprove').text('<?=language('document/turnoffsuggestorder', 'tTSOTextNoneApprove')?>');
        $('.xCNIconTable , .oimImageInsert').removeClass('xCNBlockWhenApprove');
        $('.xCNBTNActionSave').show();
        $('.xCNBTNActionInsert').hide();
        
        if(tStaDoc == '' || tStaDoc == null){
            $('.xCNBTNActionSave').show(); 
            //setTimeout(function(){
                var nLenRecord = $('#otbTableSuggestorder tbody tr').hasClass('otrNoData');
                if(nLenRecord == true){
                    $('.xCNBTNActionApprove').hide();
                    $('.xCNSuggestorderCancel').hide();
                }else{
                    $('.xCNBTNActionApprove').show();
                    $('.xCNSuggestorderCancel').show();
                }

            //}, 1500);
            $('.xCNIconTable , .oimImageInsert').removeClass('xCNBlockWhenApprove');
            //Edit inline
            $('.xWSuggestorderPDTCode').removeClass('form-control xCNEditinlineHiddenFrom').addClass('field__input a-field__input');
            $('.xWDatepicker').removeClass('form-control xCNEditinlineHiddenFrom').addClass('field__input a-field__input');            
            $('.field').css('padding','10px');
        }

        if(tStaDoc == 3){
            $('.ospTextApprove').text('<?=language('document/turnoffsuggestorder', 'tTSOTextCancleDocument')?>');
            $('.xCNBTNActionSave').hide(); 
            $('.xCNBTNActionApprove').hide();
            $('.xCNSuggestorderCancel').hide();
            $('.xCNSuggestorderInsert').show();
            $('.xCNIconTable , .oimImageInsert').addClass('xCNBlockWhenApprove');
            $('#oetInputCodeorBarcode').hide();

            //Edit inline
            $('.xWSuggestorderPDTCode').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
            $('.xWDatepicker').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
            $('.field').css('padding','0px');
        }

        if(tLastStaprcDoc == 'true'){
            $('.xCNSuggestorderInsert').hide(); 
        }

        // var nLenRecord = $('#otbTableSuggestorder tbody tr').hasClass('otrNoData');
        // if(nLenRecord == true){

        // }else{
        //     $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
        // }
    }

    
	//ถ้า record ไม่มีปุ่มบันทึกต้องซ่อน
    var nLenRecord = $('#otbTableSuggestorder tbody tr').hasClass('otrNoData');
    if(nLenRecord == true){
        $('.xCNBTNActionSave').addClass('xCNBTNActionSaveDisable');
	}else{
        if(tStaDoc != '' || tStaDoc != null){

        }else{
            $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
        }
	}

    $('.xCNInputorBarcode').css('padding','10px');
    
    //Calendar
    var oetInputdate = $('.xWDatepicker');
    var container = $('.xWForm-GroupDatePicker').length>0 ? $('.xWForm-GroupDatePicker').parent() : "body";
    var options = {
        format                  : 'dd-mm-yyyy',
        container               : container,
        todayHighlight          : true,
        enableOnReadonly        : false,
        startDate               : new Date(),
        disableTouchKeyboard    : true,
        autoclose               : true,
        orientation             : 'top',
        

    };
    oetInputdate.datepicker(options);

    //case ลบจนหน้านั้นหมด ต้องย้อนกลับไป 1 หน้า
    var nCurrentpage    = '<?=$aDataList['rnCurrentPage']?>';
    var nCountpage      = '<?=$aDataList['rnAllPage']?>';
    if(nCurrentpage > 1 && nCountpage == 0){
        JSxSelectDataintoTable(nCurrentpage-1,tDocumentID);
    }

    //Insert
    var oCrdBrwCardType = {
        Title 		: ['document/turnoffsuggestorder','tTSOTitlePDT'],
		Table		: {Master:'TCNMPdt P',PK:'FTPdtCode'},
		Join		: { 
                        /*Table	: ['TSPdtSuggestDT'],
						On		: ['TSPdtSuggestDT.FTPdtCode = P.FTPdtCode']*/
						Table	: ['TCNMPdtBar B'],
						On		: ['B.FTPdtCode = P.FTPdtCode LEFT JOIN TSPdtSuggestDT ON TSPdtSuggestDT.FTPdtCode = P.FTPdtCode']
		},
		Where 		: {
						Condition : [
							"AND (FTPdtType IN('1','4')) AND (FTPdtStaSet IN('1','2','3'))" +
							/*"AND TCNMPdt.FTPdtStaAudit IN ('1')" +*/
                            "AND P.FTPDTStaAlwBuy = '1'" +
							// "AND P.FTPdtStaActive = '1'" +
                            "AND B.FDPdtPriAffect <= GETDATE()" +
                            "AND TSPdtSuggestDT.FTPdtCode IS NULL " 
						]
		},
        GrideView	: {
            ColumnPathLang	: 'document/turnoffsuggestorder',
            ColumnKeyLang	: ['tTSOCodePDT','tTSOBarcodePDT','tTSONamePDT','tTSONameotherPDT','tTSONameotherShortPDT'],
			DataColumns		: [],
            //DistinctField   : [0],
            ColumnsSize  	: ['10%','15%','15%','15%','15%'],
            SearchLike     : [
                "P.FTPdtCode IN (SELECT P2.FTPdtCode FROM TCNMPdt P2 WHERE P2.FTPdtStkCode IN (SELECT FTPdtStkCode FROM TCNMPdt P2 WHERE P2.FTPdtCode IN (SELECT FTPdtCode FROM TCNMPdtBar WHERE FTPdtBarCode LIKE '%%tFilerGride%%')) AND P2.FTPdtStaAlwBuy='1')",
                "P.FTPdtCode IN (SELECT P2.FTPdtCode FROM TCNMPdt P2 WHERE P2.FTPdtStkCode IN (SELECT P1.FTPdtStkCode FROM TCNMPdt P1 WHERE P1.FTPdtCode LIKE '%%tFilerGride%%' OR P1.FTPdtName LIKE '%%tFilerGride%%') AND P2.FTPdtStaAlwBuy='1')"
            ],
            //SearchLike      : ['P.FTPdtCode','B.FTPdtBarCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort'],
            Perpage			: 20,
            OrderBy			: ['P.FTPdtCode'],
            SourceOrder		: "ASC"
        },

        CallBack:{
            ReturnType	: 'S'
        },
        NextFunc:{
            FuncName	: 'JSxPushDataintoTable',
            ArgReturn   : []
        },
        //DebugSQL : true
    };

    oCrdBrwCardType.GrideView.DataColumns = ['P.FTPdtCode',
                                        'B.FTPdtBarCode',
                                        'P.FTPdtName',
                                        'P.FTPdtNameOth',
                                        'P.FTPdtNameShort'
                                        ];

    $('.oimImageInsert').click(function(){
        oCrdBrwCardType.CallBack.ReturnType = 'S';
        oCrdBrwCardType.NextFunc.FuncName   = 'JSxPushDataintoTable';
        oCrdBrwCardType.NextFunc.ArgReturn  = [];
        JCNxBrowseData('oCrdBrwCardType');
    });

    var tNameroute = '<?php echo $tROUTE_omnTurnOffSuggest_insert?>';
	function JSxPushDataintoTable(elem,ptKey){
      
        $('#oetSearchTSO').val('');
        $('#ohdFlagSave').val('unsave');

        var tFormatCode = $('#ospDocumentnoValue').text();
        if(tFormatCode == 'SGBCHYYMM-######'){
            var tDocumentID = '';
        }else{
            var tDocumentID = tFormatCode;
        }
		var aData = JSON.parse(elem);
		$.ajax({
			url     : tNameroute,
			data    : { 
                tParamter   : aData ,
                tDocumentID : tDocumentID
            },
			type    : 'POST',
			success : function(result){
                var aResult = JSON.parse(result);
                if(aResult[0] == 'success'){
                    $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                    //BTN Approve ปลดปุ่ม
                    $('.xCNBTNActionApprove').removeClass('xCNBTNActionApproveDisable');

                    var nCountItem      = $('#otbTableSuggestorder tbody tr').length;
                    var nAllRow         = '<?=$aDataList['rnAllRow']?>';
                    var nSlotItem       = '<?=$nRowTable?>';
                    var nAllSlot        = parseInt(nSlotItem) + 1;
                    var nGoPage         = '<?=$nPage?>';
                    var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                    var nResultCountItem = parseInt(nCountItem) + 1;
                    //Remove sort
                    JSxRemoveValueSortByColumn();
                    if(nResultCountItem == nAllSlot){
                        var nGoPage       = nPageTogo + 1;
                        JSxSelectDataintoTable(nGoPage);
                    }else{
                        JSxSelectDataintoTable(nGoPage);
                    }
                    JSxCheckSession(result);

                    //Focus last record
                    setTimeout(function () {
                        $('.table-scroll').scrollTop($('.table-scroll')[0].scrollHeight);

                        //Focus calendar
                        var tResult = $('#otbTableSuggestorder tbody tr:last td').find('.inputsChange').attr('id');  
                        $('#'+tResult).focus();
                        tCheckEventClick = 1;
                    }, 1000);
                }
			}
		});
    }

    //BTN ปุ่มค้นหา + กดยืนยัน
    $('.xCNBTNActionListSearch').click(function(){
        $('#odvModalListSearch').modal('hide');
        JSxMoveMasterToTemp();
    });

    //Input barcode or product
    $('#oetInputCodeorBarcode').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode  === 13){
            InsertPDTorBarcode();
            e.preventDefault();
            return false;
         }
    });

    $('#oetInputCodeorBarcode').change(function(e) { 
        InsertPDTorBarcode();
    });

    //Event -> Input barcode or product
    function InsertPDTorBarcode(){
        var tNamerouteInsertBarcode = '<?php echo $tROUTE_omnTurnOffSuggest_insertBarcode?>';
        var tFormatCode = $('#ospDocumentnoValue').text();
        if(tFormatCode == 'SGBCHYYMM-######'){
            var tDocumentID = '';
        }else{
            var tDocumentID = tFormatCode;
        }
        $.ajax({
            url     : tNamerouteInsertBarcode,
            data    : { 
                tPDTCodeorBarcode   : $( "#oetInputCodeorBarcode" ).val(),
                tDocumentID         : tDocumentID
            },
            type    : 'POST',
            success : function(result){
                var aResult = JSON.parse(result);
                //console.log(aResult);
                if(aResult[0] == 'success'){
                    //BTN Approve ปลดปุ่ม
                    $('.xCNBTNActionApprove').removeClass('xCNBTNActionApproveDisable');
                    
                    var nCountItem      = $('#otbTableSuggestorder tbody tr').length;
                    var nAllRow         = '<?=$aDataList['rnAllRow']?>';
                    var nSlotItem       = '<?=$nRowTable?>';
                    var nAllSlot        = parseInt(nSlotItem) + 1;
                    var nGoPage         = '<?=$nPage?>';
                    var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                    var nResultCountItem = parseInt(nCountItem) + 1;
                    
                    //Remove sort
                    JSxRemoveValueSortByColumn();
                    if(nResultCountItem == nAllSlot){
                        var nGoPage       = nPageTogo + 1;
                        JSxSelectDataintoTable(nGoPage);
                    }else{
                        JSxSelectDataintoTable(nGoPage);
                    }

                    //Focus last record
                    setTimeout(function () {
                        $('.table-scroll').scrollTop($('.table-scroll')[0].scrollHeight);

                        //Focus calendar
                        var tResult = $('#otbTableSuggestorder tbody tr:last td').find('.inputsChange').attr('id');  
                        $('#'+tResult).focus();
                        tCheckEventClick = 1;
                    }, 1000);

                    JSxCheckSession(result);
                    $('.xCNBTNActionSave').removeClass('xCNBTNActionSaveDisable');
                    $('#ohdFlagSave').val('unsave');
                }else if(aResult[0] == 'DataDuplicate'){
                    var tBarcodeOrPDT                   = $("#oetInputCodeorBarcode").val();
                    var tLangModal                      = '<?=language('common/systems', 'tModalTextProductDuplicate')?>';
                    var tModalTextFoundDataDuplicate    = '<?=language('common/systems', 'tModalTextFoundDataDuplicate')?>';
                    var tModalTextBarcodeDuplicate      = '<?=language('common/systems', 'tModalTextBarcodeDuplicate')?>';
                    $('#xCNTextDataDuplicate').text(tLangModal + tBarcodeOrPDT + tModalTextBarcodeDuplicate + aResult[1] + tModalTextFoundDataDuplicate);
                    $('#odvModalFoundDatainTable').modal('show');

                     //กด enter ใน modal พบข้อมูลซ้ำ
                    $('#odvModalFoundDatainTable').keydown(function(e) {
                        var keyCode = e.keyCode || e.which; 
                        if(keyCode === 13){
                            $('#obtFoundDataDupinTable').click();
                            $('.xCNInsertInputPDTorBarcode').focus();
                        }
                    });
                }else{
                    //alert('ไม่พบบาร์โค๊ด');
                    $('#odvModalProductNotFound').modal('show');
                }
            }
        });

        setTimeout(function(){
            $('#oetInputCodeorBarcode').val('');
        }, 500);
    }

    //Check permission 
    var FTSumFull      =  '<?=$_SESSION['FTSumFull']?>';
    var FTSumRead      =  '<?=$_SESSION['FTSumRead']?>';
    var FTSumAdd       =  '<?=$_SESSION['FTSumAdd']?>';
    var FTSumEdit      =  '<?=$_SESSION['FTSumEdit']?>';
    var FTSumDelete    =  '<?=$_SESSION['FTSumDelete']?>';
    var FTSumCancel    =  '<?=$_SESSION['FTSumCancel']?>';
    var FTSumAppv      =  '<?=$_SESSION['FTSumAppv']?>';
    var FTSumPrint     =  '<?=$_SESSION['FTSumPrint']?>';

    //สิทธิเต็ม
    if(FTSumFull == 0){
        $('.oimImageInsert').addClass('xCNBlockWhenApprove');
        $('.xCNBTNActionSave').addClass('xCNBTNActionSaveDisable');
        $('.xWIconEdit').addClass('xCNBlockWhenApprove');
        $('.xWIconDelete').addClass('xCNBlockWhenApprove');
        $('.xCNSuggestorderCancel').addClass('xCNBlockWhenApprove');
        $('.xCNBTNActionApprove').addClass('xCNBlockWhenApprove');
        $('.xCNBTNActionSearch').addClass('xCNBlockWhenApprove');
        
        //Edit inline
        $('.xWSuggestorderPDTCode').css('margin','0px 5%');
        $('.xWSuggestorderPDTCode').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
        $('.xWDatepicker').css('margin','0px 5%');
        $('.xWDatepicker').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
    }

    //สิทธิเพิ่ม - บันทึก
    if(FTSumAdd == 0){
        $('.oimImageInsert').addClass('xCNBlockWhenApprove');
        $('.xCNBTNActionSave').addClass('xCNBTNActionSaveDisable');
        $('#oetInputCodeorBarcode').hide();
    }

    //สิทธิแก้ไข
    if(FTSumEdit == 0){
        $('.xWIconEdit').addClass('xCNBlockWhenApprove');

        //Edit inline
        $('.xWSuggestorderPDTCode').css('margin','0px 5%');
        $('.xWSuggestorderPDTCode').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
        $('.xWDatepicker').css('margin','0px 5%');
        $('.xWDatepicker').addClass('form-control xCNEditinlineHiddenFrom').removeClass('field__input a-field__input');
    }

    //สิทธิลบ
    if(FTSumDelete == 0){
        $('.xWIconDelete').addClass('xCNBlockWhenApprove');
    }

    //สิทธิยกเลิก
    if(FTSumCancel == 0){
        $('.xCNSuggestorderCancel').addClass('xCNBlockWhenApprove');
    }

    //สิทธิอนุมัติ
    if(FTSumAppv == 0){
        $('.xCNBTNAction  Approve').addClass('xCNBlockWhenApprove');
    }

    //Jame 07/01/2563
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