<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/document/assets/css/purreqcn/purreqcn.css">
<script src="<?=$tBase_url?>application/modules/document/assets/src/purreqcn/jpurreqcn.js?v=<?php echo date("dmyhis"); ?>"></script>

<style>

    .xCNPanelHeadColor > a:hover, .xCNPanelHeadColor > a:focus {
        color : #FFF !important;
        text-decoration: none;
    }

    /*ตารางสินค้า*/
    #otbTablePURProduct thead tr th , 
    #otbTablePURProduct , 
    #otbTablePURProduct tbody tr td,
    #otbTablePURProduct tfoot tr td{
        border : 1px solid #dddddd !important;
    }

    #otbTablePn  thead tr th , #otbTablePuPDT thead tr th {
        border	    : 0px solid #dddddd;
		font-size 	: 13px;
    }

    .table-scroll td , .table-scroll tbody td:first-child {
        border-right: 0px solid #ddd; 
        border-left: 0px;
    }

    #otbTablePn , #otbTablePuPDT{
        border	    : 1px solid #dddddd;
        font-size 	: 11px;
	}

    #otbTablePn{
		padding		: 5px 5px;
		font-size 	: 11px;
    }
    

    /*ตารางส่วนลด*/
    #otbTableDiscountCharge thead tr th , 
    #otbTableDiscountCharge , 
    #otbTableDiscountCharge tbody tr td,
    #otbTableDiscountCharge tfoot tr td{
        border : 1px solid #dddddd !important;
    }

    #otbTableDiscountCharge thead tr th , 
    #otbTableDiscountCharge , 
    #otbTableDiscountCharge tbody tr td,
    #otbTableDiscountCharge tfoot tr td{
        border : 1px solid #dddddd !important;
    }

    #otbTableDiscountCharge tr td{
        font-size: 12px;
        padding: 5px;
    }

    .xCNCalculateInput{
        height: 25px !important;
        font-weight: bold;
    }

    .osmTypeDiscount{
        height      : 25px !important;
        padding     : 0px 10px;
    }

    .xCNBlockDelete{
        opacity         : 0.5;
        cursor          : no-drop;
        pointer-events  : none;
    }

    #otbTableListSearch{
        color           : #1D2530 !important;
        font-size       : 14px !important;
        border-top      : 1px solid #dddddd;
    }

    .oimImageapprove{
        width           : 15px;
        margin-right    : 5px;
    }

    .xCNCantKey{
        background: #ffe9df !important;
    }


</style>

<?php   
    //เริ่มเอกสารใหม่
    if($tDocno == ''){
        $nCodeReasonDetail      = '';
        $tTextReasonDetail      = '';
        $tReason                = '';
        $tUserApprove           = '';
        $nTextDiscount          = null;
        $nDiscount              = '';

        //วันที่เอกสาร
        $FDXrhDocDate           = '';

        //รายละเอียด 2 
        $PURDocnumber           = '';
        $PURDocDate             = date('d/m/Y');
        $PURDocTime             = date('H:i:s');
        $PURDocReturnDate       = date('d/m/Y');

        //รายละเอียด 1
        $tFDXrhRefExtDate       = date('d/m/Y');
        $tFTXrhRefExt           = '';

        //ส่วนคำนวณ
        $tHiddenTypeVatSPL      = $aDetailSup[0]['FTSplVATInOrEx'];
        $tHiddenVatCode         = date('d/m/Y');
        $tHiddenVatValue        = '';

        //Flag อนุมัติ
        $tStaApprove            = '';
    }else{ //มีข้อมูล
        $nCodeReasonDetail      = $aPackHD[0]['FTCutCode'];
        $tTextReasonDetail      = $aPackHD[0]['FTCutName'];
        $tReason                = $aPackHD[0]['FTXrhRmk'];
        $tUserApprove           = $aPackHD[0]['FTWhoIns'];
        $nTextDiscount          = $aPackHD[0]['FTXrhDisChgTxt'];
        $nDiscount              = $aPackHD[0]['FCXrhDis'];

        //วันที่เอกสาร
        $FDXrhDocDate           = $aPackHD[0]['FDXrhDocDate'];

        //รายละเอียด 2 
        $PURDocnumber           = $aPackHD[0]['FTEdiDocNo'];
        $PURDocDate             = $aPackHD[0]['FDEdiDate'];
        $PURDocTime             = $aPackHD[0]['FTEdiTime'];
        $PURDocReturnDate       = $aPackHD[0]['FDXrhBchReturn'];

        //รายละเอียด 1
        $tFDXrhRefExtDate       = $aPackHD[0]['FDXrhRefExtDate'];
        $tFTXrhRefExt           = $aPackHD[0]['FTXrhRefExt'];

        //ส่วนคำนวณ
        $tHiddenTypeVatSPL      = $aDetailSup[0]['FTSplVATInOrEx'];
        $tHiddenVatCode         = $aPackHD[0]['FTVatCode'];
        $tHiddenVatValue        = $aPackHD[0]['FCXrhVATRate'];

        //Flag อนุมัติ
        $tStaApprove            = $aPackHD[0]['FTXrhStaPrcDoc'];
    }

?>

<input type="hidden" id="ohdRabbitCaseFail" value="<?=$tROUTE_omnPurReqCNNew_CaseProcessFail; ?>">
<input type="hidden" id="ohdPnSupCode" value="<?=$pnSupCode?>">
<input type="hidden" id="ohdPnTypeSupCode" value="<?=$pnTypeSupCode?>">
<input type="hidden" id="ohdPtRoundBranch" value="<?=$ptRoundBranch?>">
<input type="hidden" id="ohdHiddenComp" value="<?=$_SESSION["SesFTCmpCode"]?>">
<input type="hidden" id="ohdPURConfigReason" value="<?=$aGetConfig['FTSysValue'];?>">

<!--Head and BTN-->
<div class="col-lg-12 col-sm-12 col-xs-12 odvPanelhead">
	<div class="row">
		<div class="col-lg-5 col-sm-3">
    		<p class="ospTextHeadMenu" style="font-size:25px; cursor: pointer;"> <?=language('document/purreqcn', 'tPURHeadMenu')?> </p>
		</div>
		<div class="col-lg-7 col-sm-9">
			<div class="odvHeadRight">
				<button onclick="JSxBTNPURListSearch('<?=$tROUTE_omnPurReqCNNew_listdocument?>','1')"  id="obtListSearch"  class="btn xCNBTNActionSearch" 		type="button"> <?=language('common/systems', 'tBTNSearch')?>  </button>
				<?php if($tStaApprove == ''){ ?>
                    <button onclick="JSxBTNPURSave('<?=$tROUTE_omnPurReqCNNew_save?>')"       id="obtSave"        class="btn xCNBTNActionSave" 		    type="button"> <?=language('common/systems', 'tBTNSave')?> </button>
                    <button onclick="JSxBTNPURCancel()" 	                                  id="obtCancel"      class="btn xCNBTNActionCancel" 	    type="button"> <?=language('common/systems', 'tBTNCancel')?> </button>
                    <button class="btn xCNBTNActionPrevious" type="button"> <?=language('common/systems', 'tPrevious')?> </button>
                <?php } ?>
				<button onclick="JSxBTNPURNew()" 		                                  id="obtNew"         class="btn xCNBTNActionInsert" 	    type="button"> <?=language('common/systems', 'tBTNInsert')?> </button>
				<button onclick="JSxBTNPURApprove('<?=$tROUTE_omnPurReqCNNew_approve?>')" id="obtApprove"     class="btn xCNBTNActionApprove" 	    type="button"> <?=language('common/systems', 'tBTNApprove')?> </button>
                <button onclick="JSxBTNPURReport('','','mainpage')" 	                  id="obtReport"      class="btn xCNBTNActionReport" 		type="button"> <?=language('common/systems', 'tBTNReport')?> </button>
			</div>
		</div>
	</div>
</div>

<!--Content-->
<div class="col-lg-12 col-sm-12 col-xs-12">
    <div class="odvPanelcontent row xWForm-GroupDatePicker">

        <!--เลขที่เอกสาร-->
        <div class="col-lg-12">
            <span class="ospDocumentno"><?=language('document/turnoffsuggestorder', 'tTSODocumentno')?></span>
            <?php
            if($tDocno == '' ){
                $tDocno = 'PEBCHYY-#######';
            }else{
                $tDocno = $tDocno;
            }
            ?>
            <span class="ospDocumentnoValue" id="ospDocumentnoValue"><?=$tDocno?></span>
            &nbsp;&nbsp;
            <span class="ospDocumentdate"><?=language('document/turnoffsuggestorder', 'tTSODate')?></span>
            <?php
            if($FDXrhDocDate == '' ){
                $tDateDocument = date('d/m/Y');
            }else{
                $tDateDocument = $FDXrhDocDate;
            }
            ?>
            <span class="ospDocumentdateValue"> <?=$tDateDocument?> </span>
            &nbsp;&nbsp;
            <span class="ospDocumentdate"><?=language('document/purreqcn', 'tPURCreatedBy')?></span>
            <span><?=$_SESSION["SesUsername"]?> </span>
            <!-- &nbsp;&nbsp;
            <span class="ospDocumentdate"><?=language('document/purreqcn', 'tPURDepartment')?></span>
            <span><?=$_SESSION["SesUserDptName"]?> </span> -->
        </div>

        <!--Panel ฝั่งซ้าย-->
        <div class="col-lg-12 col-md-12" style="margin-top: 15px;">

            <div class="row">
                <!-- Panel ผู้จำหน่าย -->
                <div class="col-lg-7 col-md-7">
                    <div class="panel panel-default" style="margin-bottom: 25px;">
                        <div class="panel-heading xCNPanelHeadColor"  role="tab" style="padding-top:10px;padding-bottom:10px;">
                            <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPanelPURSUP" aria-expanded="true" >
                                <div id="headingodvPanelPURSUP">
                                    <i class="fa fa-minus xCNPlus"></i>
                                    <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></span>
                                </div>
                            </a> -->
                            <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></span>
                        </div>
                        <div id="odvPanelPURSUP" class="panel-collapse collapse in" role="tabpanel" style="min-height: 193px;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-horizontal">
                                        <?php if($ptRoundBranch == 'PUR1'){ ?>
                                            <!--รายละเอียด-->
                                            <?php 
                                                if($tDocno == 'PEBCHYY-#######'){
                                                    $tTextSupplier      = '';
                                                    $tTextAddress       = '';
                                                    $tTextTelphone      = '';
                                                    $tTextFax           = '';
                                                }else{
                                                    $tTextSupplier      = $aDetailSup[0]['FTSplName'] . '(' . $aDetailSup[0]['FTSplCode'] .')';
                                                    $tTextAddress       = $aDetailSup[0]['FTSplStreet'] . $aDetailSup[0]['FTSplDistrict'] . $aDetailSup[0]['FTDstName'] . $aDetailSup[0]['FTPvnName'] . $aDetailSup[0]['FTDstCode'];
                                                    $tTextTelphone      = $aDetailSup[0]['FTSplTel'];
                                                    $tTextFax           = $aDetailSup[0]['FTSplFax'];
                                                }
                                            ?>
                                            <!-- <div class="form-group">
                                                <label><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></label>
                                                <input type="text" readonly class="form-control" id="oetPURSupplier" value='<?=$tTextSupplier;?>'>
                                            </div>  -->
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" style="padding-left:0px;"><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></label>
                                                <div class="col-sm-10" style="padding-left:0px;">
                                                    <input type="text" readonly class="form-control" id="oetPURSupplier" value='<?=$tTextSupplier;?>'>
                                                </div>
                                            </div>

                                            <!-- ที่อยู่ -->
                                            <div style="margin-left: 75px;">
                                                <span id="ospSPLAddress"><?=language('document/purreqcn', 'tPURAddress'); ?><?=$tTextAddress;?></span><br>
                                                <span id="ospSPLTelphone"><?=language('document/purreqcn', 'tPURTelphone'); ?><?=$tTextTelphone;?></span>
                                                <span id="ospSPLFax"><?=language('document/purreqcn', 'tPURFax'); ?><?=$tTextFax;?></span>
                                            </div>
                                        <?php }else if($ptRoundBranch == 'PUR2'){ ?>
                                            <!--รายละเอียด-->
                                            <!-- <div class="form-group">
                                                <label><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></label>
                                                <input type="text" readonly class="form-control" id="oetPURSupplier" value='<?=$aDetailSup[0]['FTSplName']?> (<?=$aDetailSup[0]['FTSplCode']?>)'>
                                            </div> -->
                                            
                                            <div class="form-group" style="margin-bottom: 10px;">
                                                <label class="col-sm-2 control-label" style="padding-left:0px;"><?=language('document/purreqcn', 'tPURPanelSupplier'); ?></label>
                                                <div class="col-sm-10" style="padding-left:0px;">
                                                    <input type="text" readonly class="form-control" id="oetPURSupplier" value='<?=$aDetailSup[0]['FTSplName']?> (<?=$aDetailSup[0]['FTSplCode']?>)'>
                                                </div>
                                            </div>
                                            
                                            <!-- ที่อยู่ -->
                                            <div style="margin-left: 75px;">
                                                <span id="ospSPLAddress"><?=language('document/purreqcn', 'tPURAddress'); ?><?=($aDetailSup[0]['FTSplAddr'] == '' ? '-' : $aDetailSup[0]['FTSplAddr'])?><br>
                                                <?=$aDetailSup[0]['FTSplStreet']?>
                                                <?=$aDetailSup[0]['FTSplDistrict']?> <?=$aDetailSup[0]['FTDstName']?>
                                                <?=$aDetailSup[0]['FTPvnName']?> <?=$aDetailSup[0]['FTDstCode']?></span><br>
                                                <span id="ospSPLTelphone"><?=language('document/purreqcn', 'tPURTelphone'); ?><?=$aDetailSup[0]['FTSplTel']?></span>
                                                <span id="ospSPLFax"><?=language('document/purreqcn', 'tPURFax'); ?><?=$aDetailSup[0]['FTSplFax']?></span>
                                            </div>
                                        <?php } ?>

                                        <div class="form-group" style="margin-top: 10px;margin-right: 0px;">
                                            <label class="col-sm-2 control-label" style="padding-left:0px;"><?=language('document/purreqcn', 'tPURPanelSupplierReason'); ?></label>
                                            <div class="input-group col-sm-10">
                                                <input type="text" id="oetPURSupplierReason" class="form-control xCNHide" name="oetPURSupplierReason" value="<?=$nCodeReasonDetail?>">
                                                <input type="text" id="oetPURSupplierReasonName" class="form-control" name="oetPURSupplierReasonName" value="<?=$tTextReasonDetail?>" readonly="">
                                                <span class="input-group-btn">
                                                    <?php if($tStaApprove == ''){
                                                        $tStaDisable = '';
                                                    }else{
                                                        $tStaDisable = 'disabled';
                                                    }; ?>
                                                    <button id="obtBrowseReason" <?=$tStaDisable?> type="button" class="btn xCNBtnBrowseAddOn">
                                                        <img class="xCNIconFind">
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group" style="margin-top: 10px;">
                                            <label><?=language('document/purreqcn', 'tPURPanelSupplierReason'); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="oetPURSupplierReason" class="form-control xCNHide" name="oetPURSupplierReason" value="<?=$nCodeReasonDetail?>">
                                                <input type="text" id="oetPURSupplierReasonName" class="form-control" name="oetPURSupplierReasonName" value="<?=$tTextReasonDetail?>" readonly="">
                                                <span class="input-group-btn">
                                                    <?php if($tStaApprove == ''){
                                                        $tStaDisable = '';
                                                    }else{
                                                        $tStaDisable = 'disabled';
                                                    }; ?>
                                                    <button id="obtBrowseReason" <?=$tStaDisable?> type="button" class="btn xCNBtnBrowseAddOn">
                                                        <img class="xCNIconFind">
                                                    </button>
                                                </span>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Panel รายละเอียด 1  -->
                <div class="col-lg-5 col-md-5">
                    <div class="panel panel-default" style="margin-bottom: 25px;">
                        <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                            <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPanelPURDE1" aria-expanded="true" >
                                <div id="headingodvPanelPURDE1">
                                    <i class="fa fa-minus xCNPlus"></i>
                                    <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelDE1'); ?></span>
                                </div>
                            </a> -->
                            <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelDE1'); ?></span>
                        </div>
                        <div id="odvPanelPURDE1" class="panel-collapse collapse in" role="tabpanel" style="min-height: 193px;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-horizontal">

                                        <!--ใบรับของ / ใบซื้อ-->   
                                        <div class="form-group" style="display:none;">
                                            <label><?=language('document/purreqcn', 'tPURPanelDE2DocPO'); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="oetPURSUPCode" class="form-control xCNHide" name="oetPURSUPCode" value="">
                                                <input type="text" id="oetPURSUPName" class="form-control xCNCantKey" name="oetPURSUPName" value="" readonly="">
                                                <span class="input-group-btn">
                                                    <button id="obtBrowsePURSUP" disabled type="button" class="btn xCNBtnBrowseAddOn">
                                                        <img class="xCNIconFind">
                                                    </button>
                                                </span>
                                            </div>
                                        </div>

                                        <?php if($ptRoundBranch == 'PUR1'){ 
                                            $tPURPanelPUR2DocSend       = language('document/purreqcn', 'tPURPanelPUR2DocSend');
                                            $tPURPanelPUR2DocDateSend   = language('document/purreqcn', 'tPURPanelPUR2DocDateSend'); 
                                            $tPURPanelDisable           = 'disabled'; 

                                            if($tDocno == 'PEBCHYY-#######'){
                                                $tPURPanelDisableNumberSend = '';
                                            }else{
                                                $tPURPanelDisableNumberSend = 'disabled';
                                            }
                                            ?>
                                            
                                            <!--เอกสารที่ส่ง-->    
                                            <div class="form-group" style="margin-right: 0px;">
                                                <label class="col-sm-4 control-label" style="padding-left:0px;"><?=$tPURPanelPUR2DocSend;?></label>
                                                <div class="input-group col-sm-8">
                                                    <input type="text" id="oetPURDocNumberSendCode" value='<?=$tFTXrhRefExt?>' class="form-control xCNHide" name="oetPURDocNumberSendCode">
                                                    <input type="text" id="oetPURDocNumberSendName" value='<?=$tFTXrhRefExt?>' class="form-control" name="oetPURDocNumberSendName" readonly="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowsePURNumberSend" <?=$tPURPanelDisableNumberSend?> type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>      
                                                                   
                                            <!-- <div class="form-group">
                                                <label><?=$tPURPanelPUR2DocSend;?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPURDocNumberSendCode" value='<?=$tFTXrhRefExt?>' class="form-control xCNHide" name="oetPURDocNumberSendCode">
                                                    <input type="text" id="oetPURDocNumberSendName" value='<?=$tFTXrhRefExt?>' class="form-control" name="oetPURDocNumberSendName" readonly="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowsePURNumberSend" <?=$tPURPanelDisableNumberSend?> type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                        <?php }else{ 
                                            $tPURPanelPUR2DocSend       = language('document/purreqcn', 'tPURPanelDE2DocSend');
                                            $tPURPanelPUR2DocDateSend   = language('document/purreqcn', 'tPURPanelDE2DocDateSend');
                                            $tPURPanelDisable           = '';
                                            ?>
                                        <!--เอกสารที่ส่ง--> 
                                        <?php if($tStaApprove == ''){
                                                $tStaReadonly = '';
                                            }else{
                                                $tStaReadonly = 'readonly';
                                            }; ?>
                                            <div class="form-group" style="display:none;">
                                                <label class="col-sm-4 control-label" style="padding-left:0px;"><?=$tPURPanelPUR2DocSend;?></label>
                                                <div class="col-sm-8" style="padding-left:0px;">
                                                    <input type="text" class="form-control" <?=$tStaReadonly?> id="oetPURDocNumberSendCode" value='<?=$tFTXrhRefExt?>' placeholder="<?=language('document/purreqcn', 'tPURPanelDE2DocPO'); ?>">
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label><?= $tPURPanelPUR2DocSend ?></label>
                                                <input type="text" class="form-control" <?=$tStaReadonly?> id="oetPURDocNumberSendCode" value='<?=$tFTXrhRefExt?>' placeholder="<?=language('document/purreqcn', 'tPURPanelDE2DocPO'); ?>">
                                            </div> -->
                                        <?php } ?>

                                        <!--วันที่ส่ง-->        
                                        <?php if($tStaApprove == ''){
                                            $tStaReadonly = '';
                                        }else{
                                            $tStaReadonly = 'readonly';
                                        }; ?>
                                        <div class="form-group" style="display:none;">
                                            <label class="col-sm-4 control-label" style="padding-left:0px;"><?=$tPURPanelPUR2DocDateSend;?></label>
                                            <div class="col-sm-8" style="padding-left:0px;">
                                                <input type="text" <?=$tPURPanelDisable?> <?=$tStaReadonly?> class="form-control xWDatepicker" id="oetPURDocDateSend" value='<?=$tFDXrhRefExtDate?>' placeholder="DD/MM/YYYY">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" style="padding-left:0px;"><?=language('document/purreqcn', 'tPURPanelSupplierReason'); ?></label>
                                            <div class="col-sm-8" style="padding-left:0px;">
                                                <?php if($tStaApprove == ''){
                                                    $tStaDisable        = '';
                                                }else{
                                                    $tStaDisable        = 'disabled';
                                                }; ?>
                                                <textarea <?=$tStaDisable?> class="form-control" rows="2" id="otaReason"><?=$tReason?></textarea>
                                                <label style="float: left; font-weight: 100 !important; margin-top: 5px; font-style: italic; font-size: 11px;" class="oliFooterApproveBy"><?=language('document/purreqcn', 'tPURFooterApproveBy'); ?> <?=$tUserApprove?></label>         
                                            </div>
                                        </div>

                                        <!-- <div class="form-group">
                                            <label><?=$tPURPanelPUR2DocDateSend?></label>
                                            <input type="text" <?=$tPURPanelDisable?> <?=$tStaReadonly?> class="form-control xWDatepicker" id="oetPURDocDateSend" value='<?=$tFDXrhRefExtDate?>' placeholder="DD/MM/YYYY">
                                        </div> -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel รายละเอียด 2 -->
                <div class="col-lg-4 col-md-4" style="display:none;">
                    <div class="panel panel-default" style="margin-bottom: 25px;">
                        <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                            <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse" href="#odvPanelPURDE2" aria-expanded="true" >
                                <div id="headingodvPanelPURDE2">
                                    <i class="fa fa-minus xCNPlus"></i>
                                    <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelDE2'); ?></span>
                                </div>
                            </a> -->
                            <span class="xCNTextDetail1"><?=language('document/purreqcn', 'tPURPanelDE2'); ?></span>
                        </div>
                        <div id="odvPanelPURDE2" class="panel-collapse collapse in" role="tabpanel" style="min-height: 193px;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                        <!--เลขที่เอกสาร-->        
                                        <div class="form-group">
                                            <label><?=language('document/purreqcn', 'tPURPanelDE2DocNumber'); ?></label>
                                            <input type="text" readonly class="form-control xCNCantKey" id="oetPURDocnumber" value="<?=$PURDocnumber?>">
                                        </div>

                                        <?php if($tStaApprove == ''){
                                            $tStaReadonly = '';
                                        }else{
                                            $tStaReadonly = 'readonly';
                                        }; ?>
                                        <!--วันที่เอกสาร-->        
                                        <div class="form-group">
                                            <label><?=language('document/purreqcn', 'tPURPanelDE2DocDate'); ?></label>
                                            <input type="text" class="form-control xWDatepicker" <?=$tStaReadonly?> id="oetPURDocDate" value="<?=$PURDocDate?>" placeholder="DD/MM/YYYY" autocomplete="off">
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <!--เวลาเอกสาร-->        
                                                <div class="form-group">
                                                    <label><?=language('document/purreqcn', 'tPURPanelDE2DocTime'); ?></label>
                                                    <input type="text" readonly class="form-control xCNCantKey" id="oetPURDocTime" placeholder="H:i:s" value="<?=$PURDocTime?>" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <!--วันที่ส่งคืน-->   
                                                <div class="form-group">
                                                    <label><?=language('document/purreqcn', 'tPURPanelDE2DocReturn'); ?></label>
                                                    <input type="text" readonly class="form-control xWDatepicker xCNCantKey" id="oetPURDocReturnDate" value="<?=$PURDocReturnDate?>" placeholder="DD/MM/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Content ฝั่งขวา-->
        <div class="col-lg-12 col-md-12">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <!--ตารางสินค้า-->     
                        <div id="odvContentTable"></div>
                    </div>

                    <div class="row" style="margin-top:5px;">
                        <!--ช่องหมายเหตุ-->
                        <div class="col-lg-5 col-md-5">
                            <div class="odvSectionReason" style="border: 1px solid #dddddd; padding: 20px; display:none;">
                                <label><?=language('document/purreqcn', 'tPURPanelSupplierReason'); ?></label>
                                <label style="float: right;" class="oliFooterApproveBy"><?=language('document/purreqcn', 'tPURFooterApproveBy'); ?> <?=$tUserApprove?></label>         
                                <?php if($tStaApprove == ''){
                                    $tStaDisable        = '';
                                }else{
                                    $tStaDisable        = 'disabled';
                                }; ?>
                                <textarea <?=$tStaDisable?> class="form-control" rows="3" id="otaReason"><?=$tReason?></textarea>
                                <input style="margin-top: 15px;" type="text" class="form-control xCNCantKey" readonly id="oetPURCalculateText">
                            </div>
                        </div>

                        <!--ช่องคำนวณราคา-->
                        <div class="col-lg-7 col-md-7">
                            <div class="odvSectionTotal row" style="border: 1px solid #dddddd; padding: 20px; margin: 0px; display:none;">

                                    <!--รวมจำนวนเงิน-->        
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8">
                                            <label><?=language('document/purreqcn', 'tPURCalResult'); ?></label>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <?php $nParameterTotal = 0; ?>
                                            <input type="text" style="text-align: right;" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalResult" value="<?=number_format($nParameterTotal, 2)?>">
                                        </div>
                                    </div>

                                    <!--ส่วนลด-->      
                                    <div class="row" style="margin-top: 6px;">
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6">
                                                    <label><?=language('document/purreqcn', 'tPURCalDiscount'); ?></label>
                                                </div> 
                                                <div class="col-lg-6 col-md-6" style="text-align: right;">
                                                    <?php if($tStaApprove == ''){
                                                        $tClassStaDiscount = 'xCNDiscount xCNClickDiscount';
                                                        $tStaDisable       = '';
                                                    }else{
                                                        $tClassStaDiscount = 'xCNDiscount';
                                                        $tStaDisable       = 'disabled';
                                                    }; ?>
                                                    <button class="btn <?=$tClassStaDiscount?>" <?=$tStaDisable?> type="button" onclick="" style="display: inline; height: 25px; margin-top: -4px;">...</button>
                                                    <input style="width: 70%; display: inline; border-radius: 0px; font-weight: 100 !important;" type="text" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalTextDiscount" value="<?=$nTextDiscount?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <input type="text" style="text-align: right;" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalDiscount" value="<?=$nDiscount?>">
                                        </div>
                                    </div>

                                    <!--จำนวนเงินหลัง ลด/มัดจำ-->      
                                    <div class="row" style="margin-top: 6px;">
                                        <div class="col-lg-8 col-md-8">
                                            <label><?=language('document/purreqcn', 'tPURCalAfterDiscount'); ?></label>
                                        </div>
                                        <div class="col-lg-4 col-md-4"> 
                                            <input type="text" style="text-align: right;" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalBeforeDiscount">
                                        </div>
                                    </div>

                                    <!--ภาษีมูลค่าเพิ่ม-->      
                                    <div class="row" style="margin-top: 6px;">
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6">
                                                    <label><?=language('document/purreqcn', 'tPURCalVat'); ?></label>
                                                </div> 
                                                <div class="col-lg-6 col-md-6" style="text-align: right;">
                                                    <?php if($tStaApprove == ''){
                                                        $tClassStaVatMore   = 'xCNDiscount xCNClickVatMore';
                                                        $tStaVatDisable     = '';
                                                    }else{
                                                        $tClassStaVatMore   = 'xCNDiscount';
                                                        $tStaVatDisable     = 'disabled';
                                                    }; ?>
                                                    <button class="btn <?=$tClassStaVatMore?>" <?=$tStaVatDisable?> type="button" onclick="" style="display: inline; height: 25px; margin-top: -4px;">...</button>
                                                    <input type="hidden" id="ohdHiddenTypeVatSPL" name="ohdHiddenTypeVatSPL" value="<?=$tHiddenTypeVatSPL?>"> 
                                                    <input type="hidden" id="ohdHiddenVat" name="ohdHiddenVat" value="<?=$tHiddenVatCode?>"> 
                                                    <input type="hidden" id="ohdHiddenTypeVat" name="ohdHiddenTypeVat" value="<?=$tHiddenVatValue?>"> 
                                                    <input style="width: 70%; display: inline; border-radius: 0px; text-align: right; font-weight: 100 !important;" type="text" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetTextVat" value="0%"> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <input type="text" style="text-align: right;" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalVat">
                                        </div>
                                    </div>

                                    <!--จำนวนเงินรวมทั้งสิ้น-->  
                                    <div class="row" style="margin-top: 6px;">
                                        <div class="col-lg-8 col-md-8">
                                            <label><?=language('document/purreqcn', 'tPURCalNet'); ?></label>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <input type="text" style="text-align: right;" class="form-control xCNCalculateInput xCNCantKey" readonly id="oetPURCalNet">
                                        </div>
                                    </div>

                                </div>  

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!--modal Detail Pn -->
<div class="modal fade" id="odvModalDetailPn" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog modal-lg" style="width: 1000px;">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?=language('document/purreqcn', 'tPURModalPu')?></label>
			</div>
			<div class="modal-body">
                <input id="ohdInputDocumentPN" type="hidden" name="ohdInputDocumentPN">
				<div class="row">
					<div class="col-lg-12">
						<label><?=language('document/purreqcn', 'tPURModalPOHead')?></label>
                    </div>
					<div class="col-lg-12">
						<div id="odvContentDetailPn">
							<table id="otbTablePn" class="table table-striped xCNTableHead ">
								<thead>
									<tr>
                                        <th style="width:15%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPONumDoc')?></th>
                                        <th style="width:15%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPODate')?></th>
                                        <th style="width:30%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPONameSup')?></th>
                                        <th style="width:15%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPOCodeSup')?></th>
										<th style="width:10%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPOType')?></th>
                                        <th style="width:10%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPODateReturn')?></th>
                                    </tr>
								</thead>
								<tbody id="otbTableDetailPn">
									<tr class="otrNoData">
										<td nowrap colspan="7" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<label><?=language('document/purreqcn', 'tPURModalPODetail')?></label>
					</div>
					<div class="col-lg-12">
						<div class="table-scroll" style="height: 200px;">
							<table id="otbTablePuPDT" class="table table-striped xCNTableHead">
								<thead>
									<tr>
										<th style="width:5%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPOSelect')?> </th>
										<th style="width:5%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPONum')?></th>
                                        <th style="width:10%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPOCodePDT')?> </th>
										<th style="width:20%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPONamePDT')?></th>
                                        <th style="width:13%; text-align: left;"><?=language('document/purreqcn', 'tPURModalPOBarcode')?> </th>
										<th style="width:5%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPONo')?></th>
                                        <th style="width:5%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPOUnitProduct')?></th>
                                        <th style="width:5%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPOCount')?></th>
                                        <th style="width:8%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPOPrice')?></th>
                                        <th style="width:8%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPODiscount')?></th>
                                        <th style="width:8%;  text-align: left;"><?=language('document/purreqcn', 'tPURModalPOResult')?></th>
									</tr>
								</thead>
								<tbody>
									<tr class="otrNoData">
										<td nowrap colspan="11" style=" text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <div style="float: left;">
                    <button type="button" class="btn" onclick="JSxCheckboxSelectAll();" style="border-radius: 0px; border: 1px #bdbdbd solid; padding: 5px 25px;" >
                        <?php echo language('document/purreqcn', 'tPURCheckboxAll'); ?>
                    </button>
                    <button type="button" class="btn" onclick="JSxCheckboxResetAll();" style="border-radius: 0px; border: 1px #bdbdbd solid; padding: 5px 25px;">
                        <?php echo language('document/purreqcn', 'tPURCheckboxResetAll'); ?>
                    </button>
                </div>
				<button type="button" class="btn xCNBTNActionConfirm" onclick="JSxConfrimPDTByPu()" >
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Pu -->

<!--modal Discount Charge -->
<div class="modal fade" id="odvModalDetailDiscountCharge" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?=language('document/purreqcn', 'tPURBrwDiscountCharge')?></label>
			</div>
			<div class="modal-body">
                <div id="odvContentDiscountCharge" style="overflow: auto;">
                    <table id="otbTableDiscountCharge" class="table table-striped xCNTableHead ">
                        <thead>
                            <tr>
                                <th style="width:5%;    text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeNum')?></th>
                                <th style="width:10%;   text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeAfter')?></th>
                                <th style="width:10%;   text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeValue')?></th>
                                <th style="width:10%;   text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeBefore')?></th>
                                <th style="width:10%;   text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeType')?></th>
                                <th style="width:10%;   text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountChargeTextDC')?></th>
                                <th style="width:5%;    text-align: center;"><?=language('document/purreqcn', 'tPURTableDelete')?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <td>
                                <img class="oimImageDiscountCharge xCNImageInsert" src="<?=$tBase_url?>application/modules/common/assets/images/icons/add-circular2.png">
                            </td>
                            <td colspan="6"></td>
                        </tfoot>
                    </table>
                </div>       
			</div>
			<div class="modal-footer">
                <input id="oetTextHiddenProduct" name="oetTextHiddenProduct" style="opacity: 0; cursor: context-menu;">
				<button type="button" class="btn xCNBTNActionConfirm" onclick="JSxConfirmDiscountCharge()">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Discount Charge  -->

<!--modal List Document -->
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
			<div class="modal-body">
            
                <div style="margin-bottom: 15px;">
                    <div class="input-group" style="display: inline-flex;">
                        <input type="text" class="form-control xCNInputWithoutSingleQuote" id="oetSearchPURReq" name="oetSearchPURReq" onkeypress="Javascript:if(event.keyCode==13 ) JSxBTNPURListSearch('<?=$tROUTE_omnPurReqCNNew_listdocument?>','1')" placeholder="กรอกคำค้นหา">
                        <span class="input-group-btn">
                            <button class="btn xCNBtnSearch" type="button" onclick="JSxBTNPURListSearch('<?=$tROUTE_omnPurReqCNNew_listdocument?>','1')">
                                <img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
                            </button>
                        </span>
                    </div>
                </div>
                <div id="odvContentListSearch"></div>
            
            </div>
		</div>
	</div>
</div>
<!-- end modal List Document  -->

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

<!--modal Waring Cancel -->
<div class="modal fade" id="odvModalWaringCancel" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadDocumentCancel')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmApprove" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalTextDocumentCancel')?> 
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm xCNCalcelDocument">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Waring Cancel-->

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
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal" id="obtnotcomplete">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal Document not complete -->

<!--modal waring before save -->
<div class="modal fade" id="odvModalTextBeforeSave" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadBeforeSavePur')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalContentBeforeSavePur')?>
                </span>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn xCNBTNActionConfirm xCNConfirmBeforeSavePur">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal waring before save -->

<!--modal waring before save -->
<div class="modal fade" id="odvModalRabbitExportSuccess" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard">ส่งข้อมูลออกสำเร็จ</label>
			</div>
			<div class="modal-body">
                <span class="xCNTextRabbitExportSuccess" style="display: inline-block; word-break:break-all"></span>
			</div>
			<div class="modal-footer">
				<button type="button" id="obtRabbitExportSuccess" class="btn xCNBTNActionConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal waring before save -->

<!--modal waring check pdt qty ret -->
<div class="modal fade" id="odvModalChkPdtQtyRet" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextChkPdtQtyRet" style="display: inline-block; word-break:break-all"></span>
			</div>
			<div class="modal-footer">
				<button type="button" id="obtChkPdtQtyRet" class="btn xCNBTNActionConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal waring before save -->

<!--modal สินค้าไม่อนุญาติคืน -->
<div class="modal fade" id="odvModalPdtNotReturn" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('document/purreqcn', 'tPURModalTitlePdtNotReturn')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextPdtNotReturn" style="display: inline-block; word-break:break-all"></span>
			</div>
			<div class="modal-footer">
				<button type="button" id="obtPdtNotReturn" class="btn xCNBTNActionConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal สินค้าไม่อนุญาติคืน -->

<!--modal สินค้าไม่อนุญาติคืน -->
<div class="modal fade" id="odvModalPdtNotHaveSplCode" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('document/purreqcn', 'tPURModalTitlePdtNotHaveSplCode')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextPdtNotHaveSplCode" style="display: inline-block; word-break:break-all"></span>
			</div>
			<div class="modal-footer">
				<button type="button" id="obtPdtNotHaveSplCode" class="btn xCNBTNActionConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal สินค้าไม่อนุญาติคืน -->

<!--modal คุณต้องการเคลียร์รายการเก่าก่อน หรือไม่? -->
<div class="modal fade" id="odvPURModalClearTemp" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalWarning')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextClearTemp" style="display: inline-block; word-break:break-all"><?php echo language('document/purreqcn', 'tPURModalTitleClearTemp')?></span>
			</div>
			<div class="modal-footer">
				<button type="button" id="obtPURComfirmClearTemp" class="btn xCNBTNActionConfirm" data-dismiss="modal"><?php echo language('common/systems', 'tModalConfirm'); ?></button>
                <button type="button" id="obtPURCancelClearTemp" class="btn xCNBTNActionCancel" data-dismiss="modal"><?php echo language('common/systems', 'tModalCancel'); ?></button>
            </div>
		</div>
	</div>
</div>
<!-- end modal คุณต้องการเคลียร์รายการเก่าก่อน หรือไม่? -->

<?php include "script/jpurreqcn.php"; ?>
<?php include "script/jpurreqcnPUR1.php"; ?>


<script>

    //Checkbox - in modal PN : Select 
    function JSxCheckboxSelectAll(){
        $('input:checkbox.ocmCheckPDTPu').prop('checked',true);
    };

    //Checkbox - in modal PN : Reset 
    function JSxCheckboxResetAll(){
        $('input:checkbox.ocmCheckPDTPu').prop('checked',false);
    }

    //เปิด หุบ ของ panel
    $(function(){
        /* On Collapse: Plus to Minus */
        $('.collapse').on('shown.bs.collapse', function(event){
            // console.log(event.currentTarget);
            var idName = $(event.currentTarget).attr('id');
            var headingTarget = '#heading' + idName.replace('collapse','').trim();
            $(headingTarget + ' > i').removeClass('fa-plus').addClass('fa-minus');
        });
        
        /* On Hidden: Minus to Plus */ 
        $('.collapse').on('hidden.bs.collapse', function(event){
            // console.log(event.currentTarget); 
            var idName = $(event.currentTarget).attr('id');
            var headingTarget = '#heading' + idName.replace('collapse','').trim();
            $(headingTarget + ' > i').removeClass('fa-minus').addClass('fa-plus');
        });
    });
        

    //เอกสารไม่สมบูรณ์
    if('<?=$tDocumentComplete?>' == 'notcomplete'){
        $('#odvModalDocumentnotcomplete').modal('show');

        //กด enter ใน modal เอกสารไม่สมบูรณ์
        $('#odvModalDocumentnotcomplete').keydown(function(e) {
            var keyCode = e.keyCode || e.which; 
            if(keyCode === 13){
                $('#obtnotcomplete').click();
            }
        });
    }

    //STEP : 1 
    //control ปุ่ม
    $('#obtListSearch').show();
    $('#obtSave').show(); 
    //$('#obtSave').addClass('xCNBTNActionSaveDisable');
    $('#obtCancel').hide();
    $('#obtNew').hide();
    $('#obtApprove').hide();
    $('#obtReport').hide();

    if('<?=$tStaApprove?>' == 3){
        $('#obtNew').show();
    }

     //ย้อนกลับ
     $('.ospTextHeadMenu').click(function(){
        location.reload();
    });

    //Date
    var oetInputDate 	= $('.xWDatepicker');
    var container 		= $('.xWForm-GroupDatePicker').length>0 ? $('.xWForm-GroupDatePicker').parent() : "body";
    var options 		= {
			format          		: 'dd/mm/yyyy',
			container       		: container,
            startDate               : new Date(),
			todayHighlight  		: true,
			enableOnReadonly		: false,
			disableTouchKeyboard 	: true,
            autoclose       		: true,
	};
    oetInputDate.datepicker(options);

    //ข้อมูลสินค้าในตารางฝั่งขวา 
    var tNameRouteSelectPDT = '<?=$tROUTE_omnPurReqCNNew_selectpdt?>';
    JSxSelectDataintoTablePUR(1);
    function JSxSelectDataintoTablePUR(nPageCurrent){
        var tDocumentID = $('#ospDocumentnoValue').text();
        $.ajax({
            url     : tNameRouteSelectPDT,
            data    : { 
                'nPageCurrent'      : nPageCurrent,
                'ptDocumentID'      : tDocumentID,
                'nLimitRecord'      : 10,
                'tSortBycolumn'     : [],
                'pnSupCode' 	    : $('#ohdPnSupCode').val(),
				'pnTypeSupCode'	    : $('#ohdPnTypeSupCode').val(),
				'ptRoundBranch'	    : $('#ohdPtRoundBranch').val()
            },
            type    : 'POST',
            success : function(result){
                $('#odvContentTable').html(result); 
                JSvCalculateTotal();   

                //ถ้าไม่ข้อมูลให้ ซ่อนปุ่ม
                var nCountItem      = $('#otbTablePURProduct tbody tr').hasClass('otrNoData');

                //ปุ่มยกเลิกต้องโชว์
                $('#obtCancel').show();

                if(nCountItem == true && '<?=$tStaApprove?>' == ''){
                    $('#obtSave').addClass('xCNBTNActionSaveDisable');
                    $('#obtCancel').removeClass('xCNBTNActionCancel').addClass('xCNBTNActionSaveDisable');
                }else if(nCountItem == false && '<?=$tStaApprove?>' == ''){
                    $('#obtSave').removeClass('xCNBTNActionSaveDisable');
                    $('#obtCancel').removeClass('xCNBTNActionSaveDisable').addClass('xCNBTNActionCancel');
                }

                JSxControlBTNAfterApprove('<?=$tStaApprove?>');

                //focus ช่องจำนวน
                $('#otbTablePURProduct tbody tr:last td').find('.inputsChange').focus();
                $('#otbTablePURProduct tbody tr:last td').find('.inputsChange').select();
            }
        });
    }

    //ช่องผู้อนุมัติ
    if($('#ospDocumentnoValue').text() == 'PEBCHYY-#######'){
        $('.oliFooterApproveBy').css('display','none');
    }else{
        $('.oliFooterApproveBy').css('display','block');
    }

    //เหตุผล
    var oBrwReason = {
        Title 		: ['document/purreqcn', 'tPURBrwReason'],
        Table		: {Master:'TCNMCutOff',PK:'FTCutCode'},
        Where 		: {
            Condition : [
                " AND FTReasonProcess LIKE '%PE%' "
            ]
		},
        GrideView	: {
            ColumnPathLang	: 'document/purreqcn',
            ColumnKeyLang	: ['tPURBrwReasonCode','tPURBrwReasonName'],
			DataColumns		: ['TCNMCutOff.FTCutCode','TCNMCutOff.FTCutName'],
            ColumnsSize  	: ['15%','80%'],
            SearchLike      : ['TCNMCutOff.FTCutCode','TCNMCutOff.FTCutName'],
            Perpage			: 20,
            OrderBy			: ['TCNMCutOff.FTCutCode'],
            SourceOrder		: "ASC"
        },
        CallBack:{
            ReturnType	: 'S'
        },
        NextFunc:{
            FuncName	: 'JSxPushValueReason',
            ArgReturn   : []
        }
    };
    
    $('#obtBrowseReason').click(function(){

        //Create By Napat(Jame) 12/03/63 Comsheet 2020-060
        var tConfig     = $('#ohdPURConfigReason').val();
        var aResConfig  = tConfig.split(";"); 
        var tWhereLike = "";

        for(var i = 0; aResConfig.length > i; i++){
            if(i == 0){
                tWhereLike += " AND ( FTReasonProcess LIKE '%" + aResConfig[i] + "%' ";
            }else{
                tWhereLike += " OR FTReasonProcess LIKE '%" + aResConfig[i] + "%' ";
            }
        }

        tWhereLike += " ) ";
        oBrwReason.Where.Condition = [ tWhereLike ];
        //-------------------------------------------------

        JCNxBrowseData('oBrwReason');
        $('#modal-customs').attr("style", 'min-width: 40%;');
    });

    function JSxPushValueReason(elem){
        var aData = JSON.parse(elem);
        var tCutCode = aData[0].FTCutCode;
        var tCutName = aData[0].FTCutName;
        $('#oetPURSupplierReason').val(tCutCode);
        $('#oetPURSupplierReasonName').val(tCutName);
    }

    //ภาษีมูลค่าเพิ่ม
    var tDocno      = '<?php echo $tDocno ?>';
    if(tDocno == 'PEBCHYY-#######' || tDocno == null){
        var tRateForSUP = '<?php echo $aDetailSup[0]['FTSplVATInOrEx'] ?>';
        if(tRateForSUP == 1){
            var nVatRate = '(VI) '+'7%';
            $('#oetTextVat').val(nVatRate);
            $('#ohdHiddenVat').val(7);
            $('#ohdHiddenTypeVat').val('VI');
        }else{
            var nVatRate = '(VE) '+'7%';
            $('#oetTextVat').val(nVatRate);
            $('#ohdHiddenVat').val(7);
            $('#ohdHiddenTypeVat').val('VE');
        }
    }else{
        var tHiddenVatCode         = '<?php echo $tHiddenVatCode; ?>';
        var tHiddenVatValue        = '<?php echo $tHiddenVatValue; ?>';
        var nVatRate = '('+tHiddenVatCode+') '+ tHiddenVatValue+'%';
        $('#oetTextVat').val(nVatRate);
        $('#ohdHiddenVat').val(tHiddenVatValue);
        $('#ohdHiddenTypeVat').val(tHiddenVatCode);
    }
    
    var oBrwVat = {
        Title 		: ['document/purreqcn', 'tPURCalVat'],
		Table		: {Master:'TCNMVatRate',PK:'FTVatCode'},
        GrideView	: {
            ColumnPathLang	: 'document/purreqcn',
            ColumnKeyLang	: ['tPURVatType','tPURVatValue'],
			DataColumns		: ['TCNMVatRate.FTVatCode','TCNMVatRate.FCVatRate'],
            ColumnsSize  	: ['50%','50%'],
            SearchLike      : ['TCNMVatRate.FTVatCode','TCNMVatRate.FCVatRate'],
            Perpage			: 20,
            OrderBy			: ['TCNMVatRate.FTVatCode'],
            SourceOrder		: "ASC"
        },
        CallBack:{
            ReturnType	: 'S'
        },
        NextFunc:{
            FuncName	: 'JSxPushValueVat',
            ArgReturn   : []
        }
    };

    function JSxPushValueVat(elem){
        var aData = JSON.parse(elem);
        var nVatRate = '('+aData[0].FTVatCode+') '+aData[0].FCVatRate + '%';
        $('#oetTextVat').val(nVatRate);
        $('#ohdHiddenVat').val(aData[0].FCVatRate);
        $('#ohdHiddenTypeVat').val(aData[0].FTVatCode);
        JSvCalculateTotal();
    }
    
    $('.xCNClickVatMore').click(function(){
        JCNxBrowseData('oBrwVat');
        $('#modal-customs').attr("style", 'min-width: 30%;');
    });

    //ส่วนลดท้ายบิล
    var nValueB4Dis         = $('#oetPURCalResult').val();
    var tStatusFlag         = false;
    var tStatusFlagHaveDB   = false;
    if(nValueB4Dis == '0.00'){
        $('.xCNClickDiscount').prop('disabled',true);
    }

    $('.xCNClickDiscount').click(function(){
        $('#odvModalDetailDiscountCharge').modal('show');
        if(tStatusFlag == false){
            $('.oimImageDiscountCharge').click();
            setTimeout(function(){ 
                $('#oetValueDiscount1').focus();
            }, 500);
        }

        //มีข้อมูลจาก database
        var tTextDiscount = '<?= $nTextDiscount ?>';
        if(tTextDiscount != ''  && tStatusFlagHaveDB == false){
            var tTextDiscount = tTextDiscount.split(","); 
            $('#otbTableDiscountCharge tbody tr').remove();
            for(g=0; g<tTextDiscount.length; g++){
                tStatusFlagHaveDB = true;
                var nTotal        = $('#oetPURCalResult').val();
                var tNumberKEY    = $('#otbTableDiscountCharge tbody tr').length;
                var tNumberKEY    = g + 1;

                if(tNumberKEY > 1){
                    var tNumberKeyOld = tNumberKEY - 1;
                    var nTotalOld = $('#otrDiscountCharge' + tNumberKeyOld).find('td').eq(3).text();
                    $('#otrDiscountCharge' + tNumberKeyOld).find('td').eq(6).children().addClass('xCNBlockDelete');
                    $('#oetValueDiscount' + tNumberKeyOld).attr('disabled',true);
                }else{
                    var nTotalOld = nTotal;
                }

                //check percent
                var tCheckPercent = tTextDiscount[g].search("%");
                if(tCheckPercent == '-1'){
                    var tTypeDiscount   = '<?=language('document/purreqcn', 'tPURBrwDiscountTypeBATH')?>';
                    var nDiscountNet    = tTextDiscount[g];
                }else{
                    var tTypeDiscount   = '%';
                    var tReplace        = tTextDiscount[g].replace("%", "");
                    var nDiscountNet    = tReplace / 100 * nTotalOld;
                    var nDiscountNet    = formatNumber(nDiscountNet);
                }
                
                var nCalculate = parseFloat(nTotalOld)-parseFloat(nDiscountNet);
                var nCalculate = nCalculate.toString();
                var tHTMLDiscount = '<tr id="otrDiscountCharge'+tNumberKEY+'">';
                    tHTMLDiscount += '<td style="text-align: center;">'+tNumberKEY+'</td>';
                    tHTMLDiscount += '<td style="text-align: right;">'+nTotalOld+'</td>';
                    tHTMLDiscount += '<td style="text-align: right;">'+nDiscountNet+'</td>';
                    tHTMLDiscount += '<td style="text-align: right;">'+nCalculate+'</td>';
                    tHTMLDiscount += '<td style="text-align: center;">'+tTypeDiscount+'</td>';
                    tHTMLDiscount += '<td class="xWColorEditinLine">' +
                                        '<div class="field a-field a-field_a1 page__field" style="padding: 0px;">' +
                                            '<input maxlength="5" id="oetValueDiscount'+tNumberKEY+'" name="oetValueDiscount'+tNumberKEY+'" class="xCNInputCalculateDiscount inputsDiscount field__input a-field__input" type="text" style="text-align: right;" value="'+parseFloat(tTextDiscount[g])+'" autocomplete="off">' +
                                        '</div>' +
                                    '</td>';
                    tHTMLDiscount += '<td style="text-align: center; vertical-align:middle;"> '+                        
                                        '<img class="xCNIconTable xWIconDelete" src="<?=$tBase_url?>application/modules/common/assets/images/icons/delete.png" onclick="JSxDeleteDiscount('+tNumberKEY+')">'+
                                    '</td>';
                    tHTMLDiscount += '</tr>';
                $('#otbTableDiscountCharge tbody').append(tHTMLDiscount);
            }

            //แก้ไขข้อมูล
            $('.inputsDiscount').on("focusout",function(e){
                JSxChangeTextDiscount();
            });

            $('.inputsDiscount').on("keydown",function(e) {
                var keyCode = e.keyCode || e.which; 
                if(keyCode === 13){
                    JSxChangeTextDiscount();
                }
            });

            $(".xCNInputCalculateDiscount").on("keypress keyup blur", function(event) {
                $(this).val($(this).val().replace(/[^0-9,-.]/g, ''));
                // $(this).val($(this).val().replace(/[^\d]+/, ''));
                $(this).val($(this).val().replace('--','-'));
                var nValue = $(this).val().search("-");
                if(nValue != '-1'){
                    event.preventDefault();
                }
            });

            function JSxChangeTextDiscount(){
                var nValue          = $('#otrDiscountCharge1').find('td').eq(1).text();
                var nValue          = nValue.replace(",","");
                //check ว่าเป็น % หรือ ลดบาท
                var tCheckPercentorBath = $('#oetValueDiscount'+ tNumberKEY).val().search("-");
                if(tCheckPercentorBath != '-1'){ //PERCENT
                    var tTypeDiscount   = '%';
                    var nDiscount       = $('#oetValueDiscount'+ tNumberKEY).val();
                    var nDiscount       = nDiscount.replace("-","");
                    $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
                }else{ //BATH
                    var tTypeDiscount = '<?=language('document/purreqcn', 'tPURBrwDiscountTypeBATH')?>';
                    var nDiscount     = $('#oetValueDiscount'+ tNumberKEY).val();
                }

                if(nDiscount == '' || nDiscount == 0 || nDiscount == null){
                    nDiscount       = 0;
                    $('#oetValueDiscount' + tNumberKEY).val(nDiscount);
                }

                if(tTypeDiscount == '%'){ //PERCENT
                    var nPercent        = ( nDiscount / 100 ) * nValue;
                    var nResultDiscount = formatNumber(nPercent);
                    var nResultValue    = nValue - nPercent;
                }else{ //BATH
                    var nResultValue    = nValue - nDiscount;
                    var nResultDiscount = nDiscount;
                }

                $('#otrDiscountCharge' + tNumberKEY).val(nDiscount);
                $('#otrDiscountCharge' + tNumberKEY).find('td').eq(2).text(nResultDiscount);
                $('#otrDiscountCharge' + tNumberKEY).find('td').eq(3).text(formatNumber(nResultValue));
                $('#otrDiscountCharge' + tNumberKEY).find('td').eq(4).text(tTypeDiscount);

                //ส่วนของ focusout กับ ส่วน keydown
                if(tTypeDiscount == '%'){
                    $('#oetValueDiscount'+ tNumberKEY).val(nDiscount+'-');
                }else{
                    $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
                }

                $('#oetTextHiddenProduct').focus(); 
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
            }
        }
        
        //Set value ทุกครั้งที่เข้ามาใหม่ case เวลาเพิ่ม item หรือ เปลี่ยนจำนวน พวกราคาต้องแสดงใหม่
        var tNumberKEY    = $('#otbTableDiscountCharge tbody tr').length;
        for(var k=1; k<=tNumberKEY; k++){

            if(k==1){
                var nB4Dis      = $('#oetPURCalResult').val();
                var nValueB4Dis = $('#oetPURCalResult').val().replace(',','');
                var nValueAfter = nValueB4Dis - $('#otrDiscountCharge'+k).find('td').eq(2).text().replace(',','');
            }else{
                var nKeyBefore  = k - 1;
                var nB4Dis      = $('#otrDiscountCharge'+nKeyBefore).find('td').eq(3).text();
                var nValueB4Dis = $('#otrDiscountCharge'+k).find('td').eq(2).text().replace(',','');
                var nValueAfter = nB4Dis - nValueB4Dis;
            }

            //input value ส่วนลด
            $('#otrDiscountCharge'+k).find('td').eq(1).text(nB4Dis);
            $('#otrDiscountCharge'+k).find('td').eq(3).text(nValueAfter.toFixed(2));
        }
        
    });

    $('.oimImageDiscountCharge').click(function(){
        tStatusFlag       = true;
        var nTotal        = $('#oetPURCalResult').val();
        var tNumberKEY    = $('#otbTableDiscountCharge tbody tr').length;
        var tNumberKEY    = tNumberKEY + 1;

        if(tNumberKEY > 1){
            var tNumberKeyOld = tNumberKEY - 1;
            var nTotalOld = $('#otrDiscountCharge' + tNumberKeyOld).find('td').eq(3).text();
            $('#otrDiscountCharge' + tNumberKeyOld).find('td').eq(6).children().addClass('xCNBlockDelete');
            $('#oetValueDiscount' + tNumberKeyOld).attr('disabled',true);
        }else{
            var nTotalOld = nTotal;
        }

        var tHTMLDiscount = '<tr id="otrDiscountCharge'+tNumberKEY+'">';
            tHTMLDiscount += '<td style="text-align: center;">'+tNumberKEY+'</td>';
            tHTMLDiscount += '<td style="text-align: right;">'+nTotalOld+'</td>';
            tHTMLDiscount += '<td style="text-align: right;">0.00</td>';
            tHTMLDiscount += '<td style="text-align: right;">0.00</td>';
            tHTMLDiscount += '<td style="text-align: center;"><?=language('document/purreqcn', 'tPURBrwDiscountTypeBATH')?></td>';
            tHTMLDiscount += '<td class="xWColorEditinLine">' +
                                '<div class="field a-field a-field_a1 page__field" style="padding: 0px;">' +
                                    '<input maxlength="5" id="oetValueDiscount'+tNumberKEY+'" name="oetValueDiscount'+tNumberKEY+'" class="xCNInputCalculateDiscount inputsDiscount field__input a-field__input" type="text" style="text-align: right;" value="" autocomplete="off">' +
                                '</div>' +
                             '</td>';
            tHTMLDiscount += '<td style="text-align: center; vertical-align:middle;"> '+                        
                                '<img class="xCNIconTable xWIconDelete" src="<?=$tBase_url?>application/modules/common/assets/images/icons/delete.png" onclick="JSxDeleteDiscount('+tNumberKEY+')">'+
                             '</td>';
            tHTMLDiscount += '</tr>';
        $('#otbTableDiscountCharge tbody').append(tHTMLDiscount);

        //Focus 
        $('#oetValueDiscount'+tNumberKEY).focus();

        //ทำให้กรอกได้แต่เเค่ตัวเลข
        $(".xCNInputNumericWithDecimal").on("keypress keyup blur", function(event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $(".xCNInputCalculateDiscount").on("keypress keyup blur", function(event) {
            $(this).val($(this).val().replace(/[^0-9,-.]/g, ''));
            // $(this).val($(this).val().replace(/[^\d]+/, ''));
            $(this).val($(this).val().replace('--','-'));
            var nValue = $(this).val().search("-");
            if(nValue != '-1'){
                event.preventDefault();
            }
        });

        //แก้ไขข้อมูล
        $('.inputsDiscount').keydown(function(e) {
            var keyCode = e.keyCode || e.which; 
            if(keyCode === 13){
                JSxChangeTextDiscount();
            }
        });

        $('.inputsDiscount').on("focusout",function(e){
            JSxChangeTextDiscount();
            e.preventDefault();
        });

        function JSxChangeTextDiscount(){
            var nValue          = $('#otrDiscountCharge' + tNumberKEY).find('td').eq(1).text();
            var nValue          = nValue.replace(",","");
            //check ว่าเป็น % หรือ ลดบาท
            var tCheckPercentorBath = $('#oetValueDiscount'+ tNumberKEY).val().search("-");
            if(tCheckPercentorBath != '-1'){ //PERCENT
                var tTypeDiscount   = '%';
                var nDiscount       = $('#oetValueDiscount'+ tNumberKEY).val();
                var nDiscount       = nDiscount.replace("-","");
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
            }else{ //BATH
                var tTypeDiscount = '<?=language('document/purreqcn', 'tPURBrwDiscountTypeBATH')?>';
                var nDiscount     = $('#oetValueDiscount'+ tNumberKEY).val();
            }

            if(nDiscount == '' || nDiscount == 0 || nDiscount == null){
                nDiscount       = 0;
                $('#oetValueDiscount' + tNumberKEY).val(nDiscount);
            }

            if(tTypeDiscount == '%'){ //PERCENT
                var nPercent        = ( nDiscount / 100 ) * nValue;
                var nResultDiscount = formatNumber(nPercent);
                var nResultValue    = nValue - nPercent;
            }else{ //BATH
                var nResultValue    = nValue - nDiscount;
                var nResultDiscount = nDiscount;
            }

            $('#otrDiscountCharge' + tNumberKEY).val(nDiscount);
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(2).text(nResultDiscount);
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(3).text(formatNumber(nResultValue));
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(4).text(tTypeDiscount);

            //ส่วนของ focusout กับ ส่วน keydown
            if(tTypeDiscount == '%'){
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount+'-');
            }else{
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
            }

            $('#oetTextHiddenProduct').focus(); 
            $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
        }
    });

    function formatNumber(num) {
        return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    //ลบส่วนลด
    function JSxDeleteDiscount(pnKey){
        $('#otrDiscountCharge'+pnKey).remove();
        var tNumberKEY      = $('#otbTableDiscountCharge tbody tr').length;
        var tNumberKEY      = tNumberKEY;
        $('#otrDiscountCharge' + tNumberKEY).find('td').eq(6).children().removeClass('xCNBlockDelete');
        $('#oetValueDiscount' + tNumberKEY).attr('disabled',false);

        //แก้ไขข้อมูล
        $('.inputsDiscount').keydown(function(e) {
            var keyCode = e.keyCode || e.which; 
            if(keyCode === 13){
                JSxChangeTextDiscount();
            }
        });

        $('.inputsDiscount').on("focusout",function(e){
            JSxChangeTextDiscount();
            e.preventDefault();
        });

        function JSxChangeTextDiscount(){
            var nValue          = $('#otrDiscountCharge' + tNumberKEY).find('td').eq(1).text();
            var nValue          = nValue.replace(",","");
            //check ว่าเป็น % หรือ ลดบาท
           var tCheckPercentorBath = $('#oetValueDiscount'+ tNumberKEY).val().search("-");
            if(tCheckPercentorBath != '-1'){ //PERCENT
                var tTypeDiscount   = '%';
                var nDiscount       = $('#oetValueDiscount'+ tNumberKEY).val();
                var nDiscount       = nDiscount.replace("-","");
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
            }else{ //BATH
                var tTypeDiscount = '<?=language('document/purreqcn', 'tPURBrwDiscountTypeBATH')?>';
                var nDiscount     = $('#oetValueDiscount'+ tNumberKEY).val();
            }

            if(nDiscount == '' || nDiscount == 0 || nDiscount == null){
                nDiscount       = 0;
                $('#oetValueDiscount' + tNumberKEY).val(nDiscount);
            }

            if(tTypeDiscount == '%'){ //PERCENT
                var nPercent        = ( nDiscount / 100 ) * nValue;
                var nResultDiscount = formatNumber(nPercent);
                var nResultValue    = nValue - nPercent;
            }else{ //BATH
                var nResultValue    = nValue - nDiscount;
                var nResultDiscount = nDiscount;
            }

            $('#otrDiscountCharge' + tNumberKEY).val(nDiscount);
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(2).text(nResultDiscount);
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(3).text(formatNumber(nResultValue));
            $('#otrDiscountCharge' + tNumberKEY).find('td').eq(4).text(tTypeDiscount);

            //ส่วนของ focusout กับ ส่วน keydown
            if(tTypeDiscount == '%'){
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount+'-');
            }else{
                $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
            }

            $('#oetTextHiddenProduct').focus(); 
            $('#oetValueDiscount'+ tNumberKEY).val(nDiscount);
        }
    }

    //ยืนยันส่วนลด
    function JSxConfirmDiscountCharge(){
        $('#odvModalDetailDiscountCharge').modal('hide');

        var nCount    = $('#otbTableDiscountCharge tbody tr').length;
        nResultValue = 0;
        tResultValue = '';
        for(i=1; i<=nCount; i++){
            var tTypePercent = $('#otrDiscountCharge'+i).find('td').eq(4).text();
            if(tTypePercent == '%'){ 
                var tTypePercent = '%';
                var tValue = $('#oetValueDiscount'+i).val();
            }else{ 
                var tTypePercent = ''; 
                var tValue = $('#oetValueDiscount'+i).val();
            }
            var nValue = $('#otrDiscountCharge'+i).find('td').eq(2).text();
            nResultValue = parseFloat(nResultValue) + parseFloat(nValue);
            tResultValue += tValue + tTypePercent + ',';
            if(i == nCount){
                tResultValue = tResultValue.slice(0, -1);
            }
        }

        $('#oetPURCalTextDiscount').val(tResultValue);
        $('#oetPURCalDiscount').val(formatNumber(nResultValue));
        JSvCalculateTotal();

        $('#obtSave').removeClass('xCNBTNActionSaveDisable');
    }

    $('document').ready(function(){
        if( $("input[type='checkbox']:checked").val() == 'PUR1'){
            $('.xCNBTNActionPrevious').attr('disabled',true);
        }
    });

    $('.xCNBTNActionPrevious').click(function(){
        if( $("input[type='checkbox']:checked").val() == 'PUR2'){
            JSvPURSelectRoundBranch();
        }
    });

</script>