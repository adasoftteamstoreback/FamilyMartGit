<?php
    if($aGetDataHD['nStaQuery'] == 1){
        $tIuhDocNo          = $aGetDataHD['aItems']['FTIuhDocNo'];
        $tIuhDocNoShow      = $aGetDataHD['aItems']['FTIuhDocNo'];
        $dIuhDocDate        = $aGetDataHD['aItems']['FDIuhDocDate'];
        $tIuhHhdNumber      = $aGetDataHD['aItems']['FTIuhHhdNumber'];
        $tWahCode           = $aGetDataHD['aItems']['FTWahCode'];
        $tIuhRmk            = $aGetDataHD['aItems']['FTIuhRmk'];
        $tIuhStaDoc         = $aGetDataHD['aItems']['FTIuhStaDoc'];
        $tIuhStaPrcDoc      = $aGetDataHD['aItems']['FTIuhStaPrcDoc'];
        $tIuhDocType        = $aGetDataHD['aItems']['FTIuhDocType'];
        $tIuhAdjType        = $aGetDataHD['aItems']['FTIuhAdjType'];
    }else{
        $tIuhDocNo          = "";
        $tIuhDocNoShow      = "IUBCHYY-#######";
        $dIuhDocDate        = date_create(date('Y-m-d'));
        $tIuhHhdNumber      = "";
        $tWahCode           = $ptWahCode;
        $tIuhRmk            = "";
        $tIuhStaDoc         = "";
        $tIuhStaPrcDoc      = "";
        $tIuhDocType        = "1";
        $tIuhAdjType        = "1";
    }

    if($nTypePage!=1){
        $tHeightPanel = "height:193px";
    }else{
        $tHeightPanel = "height:262px;margin-bottom:10px;";
    }

?>
<!--Content-->
<form id="ofmPdtAdjStkChk">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="odvPanelcontent row xWForm-GroupDatePicker">
        <!--เลขที่เอกสาร-->
        <div id="odvPASPanel1" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left:0;font-size: 12px;">
            <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> -->
            <input type="text" class="xCNHide" id="oetPASDocNoForReChkDT" name="oetPASDocNoForReChkDT" value="">
            <input type="text" class="xCNHide" id="oetPASDocNo" name="oetPASDocNo" value="<?=$tIuhDocNo?>">
            <input type="text" class="xCNHide" id="oetPASIuhStaDoc" name="oetPASIuhStaDoc" value="<?=$tIuhStaDoc?>">
            <input type="text" class="xCNHide" id="oetPASIuhStaPrcDoc" name="oetPASIuhStaPrcDoc" value="<?=$tIuhStaPrcDoc?>">
            <input type="text" class="xCNHide" id="oetPASIuhDocType" name="oetPASIuhDocType" value="<?=$tIuhDocType?>">
            <input type="text" class="xCNHide" id="oetPASTypePage" name="oetPASTypePage" value="<?=$nTypePage?>">

            <input type="text" class="xCNHide" id="oetPASPdtAndBarFromCode" name="oetPASPdtAndBarFromCode" value="">
            <input type="text" class="xCNHide" id="oetPASPdtAndBarToCode" name="oetPASPdtAndBarToCode" value="">

            <span class="xCNHeadSpanDocument">
                <?=language('common/systems', 'tDocumentNo')?>
                <span class="xCNHeadSpanDocumentDetails"><?=$tIuhDocNoShow?></span>
            </span>
            <span class="xCNHeadSpanDocument">
                <?=language('common/systems', 'tDate')?>
                <span class="xCNHeadSpanDocumentDetails">
                    <input type="text" class="form-control xWDatepicker xCNDontKey" style="width: 100px !important; display: inline; height: 23px;" id="oetPASDocDate" name="oetPASDocDate" value="<?=date_format($dIuhDocDate,'Y-m-d')?>">
                </span>
            </span>
            <span class="xCNHeadSpanDocument">
                <?=language('common/systems', 'tCreatedBy')?>
                <span class="xCNHeadSpanDocumentDetails"><?=$_SESSION["SesUsername"]?></span>
            </span>
            
            <span class="xCNHeadSpanDocument">
                <?=language('common/systems', 'tDepartment')?>
                <span class="xCNHeadSpanDocumentDetails"><?=$_SESSION["SesUserDptName"]?></span>
            </span>

            <span class="xCNHeadSpanDocument">
                <!-- <span class="xCNHeadSpanDocumentDetails"><?=$_SESSION["SesUserDptName"]?></span> -->
                
                    <!-- <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                    <label class="form-check-label" for="defaultCheck1">
                        
                    </label> -->
                <!-- <div class="form-check form-check-inline"> -->
                    <input class="form-check-input" type="checkbox" id="ocbPASAdjType" name="ocbPASAdjType" value="1" <?php if($tIuhAdjType <> "2"){ echo "checked"; } ?> >
                    <label class="form-check-label" for="inlineCheckbox1"><?=language('common/systems', 'tAdjType')?></label>
                <!-- </div> -->
            </span>
            
            <!-- </div> -->
            <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="form-group row" style="margin-bottom: 0;">
                    <label for="staticEmail" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-form-label" style="padding-right: 0;margin-top: 6px;"><?=language('document/turnoffsuggestorder', 'tTSODocumentno')?></label>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0;margin-top: 6px;">
                        <?=$tIuhDocNoShow?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="form-group row" style="margin-bottom: 0;">
                    <label for="staticEmail" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-form-label" style="padding-right: 0;margin-top: 6px;"><?=language('document/turnoffsuggestorder', 'tTSODate')?></label>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0;">
                        <input type="text" class="form-control xWDatepicker xCNDontKey" id="oetPASDocDate" name="oetPASDocDate" value="<?=date_format($dIuhDocDate,'Y-m-d')?>">
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="padding-left: 0;">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <input type="text" class="xCNHide" id="oetPASDocNo" name="oetPASDocNo" value="<?=$tIuhDocNo?>">
                <input type="text" class="xCNHide" id="oetPASIuhStaDoc" name="oetPASIuhStaDoc" value="<?=$tIuhStaDoc?>">
                <input type="text" class="xCNHide" id="oetPASIuhStaPrcDoc" name="oetPASIuhStaPrcDoc" value="<?=$tIuhStaPrcDoc?>">
                <input type="text" class="xCNHide" id="oetPASIuhDocType" name="oetPASIuhDocType" value="<?=$tIuhDocType?>">
                <input type="text" class="xCNHide" id="oetPASTypePage" name="oetPASTypePage" value="<?=$nTypePage?>">
            </div> -->
        </div>

        <!--Panel ฝั่งซ้าย-->
        <div id="odvPASPanel2" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 5px;padding:0px;">
        <?php if($nTypePage==1){ ?>
            <!-- Panel เอกสาร -->
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="padding:0px;">
                <div class="panel panel-default" style="<?=$tHeightPanel;?>">
                    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                        <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse"  href="#odvPanelPASDoc" aria-expanded="true"> -->
                            <div id="headingodvPanelPASDoc">
                                <!-- <i class="fa xCNPlus fa-minus"></i> -->
                                <span class="xCNTextDetail1"><?=language('document/pdtadjstkchk', 'tPASPanelDoc'); ?></span>
                            </div>
                        <!-- </a> -->
                    </div>
                    <div id="odvPanelPASDoc" class="panel-collapse collapse in" role="tabpanel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <!--เมนู Tab-->
                                    <ul class="nav nav-tabs" style="margin:0 -10px 0 -10px;">
                                        <li class="active"><a data-toggle="tab" href="#odvPASTabProduct" style="padding:4px;" ><?=language('document/pdtadjstkchk', 'tPASNavTabPdt'); ?></a></li>
                                        <li><a data-toggle="tab" href="#odvPASTabSupplier" style="padding:4px;" ><?=language('document/pdtadjstkchk', 'tPASNavTabSup'); ?></a></li>
                                        <li><a data-toggle="tab" href="#odvPASTabUser" style="padding:4px;" ><?=language('document/pdtadjstkchk', 'tPASNavTabUser'); ?></a></li>
                                        <li><a data-toggle="tab" href="#odvPASTabGroupProduct" style="padding:4px;" ><?=language('document/pdtadjstkchk', 'tPASNavTabGrpPdt'); ?></a></li>
                                        <li><a data-toggle="tab" href="#odvPASTabLocation" style="padding:4px;" ><?=language('document/pdtadjstkchk', 'tPASNavTabLoc'); ?></a></li>
                                    </ul>
                                    <!--รายละเอียด ข้างใน Tab-->
                                    <div class="tab-content">

                                        <div id="odvPASTabProduct" class="tab-pane fade in active" style="margin-top:20px;">
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASPdtFromCode" name="oetPASPdtFromCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASPdtFromName" name="oetPASPdtFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseProductFrom" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> 
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASPdtFromCode" name="oetPASPdtFromCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASPdtFromName" name="oetPASPdtFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseProductFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabToCode');?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASPdtToCode" name="oetPASPdtToCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASPdtToName" name="oetPASPdtToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseProductTo" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> 
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabToCode');?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASPdtToCode" name="oetPASPdtToCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASPdtToName" name="oetPASPdtToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseProductTo" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group text-center" style="margin-bottom:7px;margin-top: 15px;">
                                                <button type="button" data-tab="Pdt" class="btn xWPASBtnAddFormCodeToCode xWDisabledOnApvSub"><i class="fa fa-arrow-down"></i> <?=language('document/pdtadjstkchk', 'tPASBtnImportToTable');?></button>
                                            </div>
                                        </div>

                                        <div id="odvPASTabSupplier" class="tab-pane fade" style="margin-top:20px;">
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASPdtSupFromCode" name="oetPASPdtSupFromCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASPdtSupFromName" name="oetPASPdtSupFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseSupplierFrom" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASPdtSupFromCode" name="oetPASPdtSupFromCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASPdtSupFromName" name="oetPASPdtSupFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseSupplierFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabToCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASPdtSupToCode" name="oetPASPdtSupToCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASPdtSupToName" name="oetPASPdtSupToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseSupplierTo" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabToCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASPdtSupToCode" name="oetPASPdtSupToCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASPdtSupToName" name="oetPASPdtSupToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseSupplierTo" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group text-center" style="margin-bottom:7px;margin-top: 15px;">
                                                <button type="button" data-tab="PdtSup" class="btn xWPASBtnAddFormCodeToCode xWDisabledOnApvSub"><i class="fa fa-arrow-down"></i> <?=language('document/pdtadjstkchk', 'tPASBtnImportToTable');?></button>
                                            </div>    
                                        </div>

                                        <div id="odvPASTabUser" class="tab-pane fade" style="margin-top:20px;">
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASUserFromCode" name="oetPASUserFromCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASUserFromName" name="oetPASUserFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseUserFrom" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabFromCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASUserFromCode" name="oetPASUserFromCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASUserFromName" name="oetPASUserFromName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseUserFrom" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabToCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASUserToCode" name="oetPASUserToCode" class="form-control xWDisabledOnApvSub"  value="">
                                                    <input type="text" id="oetPASUserToName" name="oetPASUserToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseUserTo" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabToCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASUserToCode" name="oetPASUserToCode" class="form-control"  value="">
                                                    <input type="text" id="oetPASUserToName" name="oetPASUserToName" class="form-control xCNHide"  value="">
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseUserTo" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group text-center" style="margin-bottom:7px;margin-top: 15px;">
                                                <button type="button" data-tab="User" class="btn xWPASBtnAddFormCodeToCode xWDisabledOnApvSub"><i class="fa fa-arrow-down"></i> <?=language('document/pdtadjstkchk', 'tPASBtnImportToTable');?></button>
                                            </div>
                                        </div>
                                        
                                        <div id="odvPASTabGroupProduct" class="tab-pane fade" style="margin-top:20px;">
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-4 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASNavTabGrpPdt'); ?></label>
                                                <div class="input-group col-sm-8">
                                                    <input type="text" id="oetPASGrpPdtCode" name="oetPASGrpPdtCode" class="form-control xCNHide" value="">
                                                    <input type="text" id="oetPASGrpPdtName" name="oetPASGrpPdtName" class="form-control"  value="" readonly>
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseGroup" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASNavTabGrpPdt'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASGrpPdtCode" name="oetPASGrpPdtCode" class="form-control xCNHide" value="">
                                                    <input type="text" id="oetPASGrpPdtName" name="oetPASGrpPdtName" class="form-control"  value="" readonly>
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseGroup" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <div class="form-group text-center" style="margin-bottom:7px;margin-top: 15px;">
                                                <button type="button" data-tab="Group" class="btn xWPASBtnAddFormCodeToCode xWDisabledOnApvSub"><i class="fa fa-arrow-down"></i> <?=language('document/pdtadjstkchk', 'tPASBtnImportToTable');?></button>
                                            </div>
                                        </div>
                                        <div id="odvPASTabLocation" class="tab-pane fade" style="margin-top:20px;">
                                            <div class="form-group form-horizontal">
                                                <label class="col-sm-3 control-label" style="padding-left:0px;"><?=language('document/pdtadjstkchk', 'tPASTabLocCode'); ?></label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" id="oetPASLocCode" name="oetPASLocCode" class="form-control"  value="" readonly>
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseLoc" type="button" class="btn xCNBtnBrowseAddOn xWDisabledOnApvSub">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group" style="margin-bottom:7px;">
                                                <label><?=language('document/pdtadjstkchk', 'tPASTabLocCode'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" id="oetPASLocCode" name="oetPASLocCode" class="form-control"  value="" readonly>
                                                    <span class="input-group-btn">
                                                        <button id="obtBrowseLoc" type="button" class="btn xCNBtnBrowseAddOn">
                                                            <img class="xCNIconFind">
                                                        </button>
                                                    </span>
                                                </div>
                                            </div> -->
                                            <p class="col-sm-3"></p>
                                            <p class="bg-warning xWPASLocName col-sm-9" style="padding:5px;margin-bottom:10px;background-color: #ffe0c1;"></p>
                                            <div class="form-group text-center" style="margin-bottom:7px;margin-top: 15px;">
                                                <button type="button" data-tab="Location" class="btn xWPASBtnAddFormCodeToCode xWDisabledOnApvSub"><i class="fa fa-arrow-down"></i> <?=language('document/pdtadjstkchk', 'tPASBtnImportToTable');?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Panel เอกสาร -->
        <?php } ?>
            <?php
                if($nTypePage!=1){
                    $tStylePanelWah = "padding:0px";
                }else{
                    $tStylePanelWah = "";
                }
            ?>
            <!-- Panel คลัง -->
            <?php if($nTypePage == 1){ ?>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="<?=$tStylePanelWah;?>">
                <div class="panel panel-default" style="<?=$tHeightPanel;?>">
                    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                        <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse"  href="#odvPanelPASStore" aria-expanded="true"> -->
                            <div id="headingodvPanelPASStore">
                                <!-- <i class="<?php if($nTypePage==1){ echo "fa fa-plus"; }else{ echo "fa fa-minus"; }?> xCNPlus"></i> -->
                                <span class="xCNTextDetail1"><?=language('document/pdtadjstkchk', 'tPASPanelStore'); ?></span>
                            </div>
                        <!-- </a> -->
                    </div>
                    <div id="odvPanelPASStore" class="panel-collapse collapse in" role="tabpanel"> <!--class="panel-collapse collapse <?php /*if($nTypePage!=1){ echo "in"; }*/ ?>" -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                    <?php if($nTypePage==1){ ?>
                                    <div class="form-group xCNHide" style="margin-bottom:7px;">
                                        <label><?=language('document/pdtadjstkchk', 'tPASStoreHHDNo'); ?></label>
                                        <input type="text" id="oetPASHHD" name="oetPASHHD" class="form-control" maxlength="20" value="<?=$tIuhHhdNumber?>">
                                    </div>
                                    <?php } ?>

                                    <div class="form-group xCNHide" style="margin-bottom:7px;">
                                        <label><?=language('document/pdtadjstkchk', 'tPASPanelStore'); ?></label>
                                        <input type="text" id="oetPASWahCode" name="oetPASWahCode" class="form-control" value="<?=$tWahCode?>" readonly>
                                    </div> 

                                    <?php if($nTypePage==1){ ?>
                                    <div class="form-group" style="margin-bottom:7px;">
                                        <label><?=language('document/pdtadjstkchk', 'tPASStoreLoc'); ?></label>
                                        <div class="table-responsive" style="height: 156px;overflow-y: auto;">
                                            <table class="table table-striped" style="width:100%;margin-bottom:0px;">
                                                <?php
                                                if($aGetLoc['nStaQuery'] == 1){
                                                    foreach($aGetLoc['aItems'] AS $tKey => $aValue){
                                                ?>
                                                    <tr class="text-left">
                                                        <td nowrap><input type="checkbox" class="xWPASPlcCode xWDisabledOnApvSub" name="orbPASLocation[]" value="<?=$aValue['FTPlcCode']?>" <?php if($tKey == 0){ echo "checked"; }?>></td>
                                                        <td style="width:45%;" nowrap><?=$aValue['FTPlcCode']?></td>
                                                        <td style="width:45%;" nowrap><?=$aValue['FTPlcName']?></td>
                                                    </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php } ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <!-- End Panel คลัง -->

            <!-- Panel หมายเหตุ -->
            <?php 
                if($nTypePage != 1){
                    $tHeadSizeNote      = "col-xs-12 col-sm-12 col-md-12 col-lg-12";
                    $tSizeInputNote     = "col-xs-12 col-sm-6 col-md-6 col-lg-6";
                    $tHeadStyleNote     = "";
                }else{
                    $tHeadSizeNote      = "col-xs-12 col-sm-4 col-md-4 col-lg-4";
                    $tSizeInputNote     = "col-xs-12 col-sm-12 col-md-12 col-lg-12";
                    $tHeadStyleNote     = "";
                }
            ?>
            
            <div class="<?=$tHeadSizeNote;?>" style="padding:0px;<?=$tHeadStyleNote;?>">
                <div class="panel panel-default" style="<?=$tHeightPanel;?>">
                    <div class="panel-heading xCNPanelHeadColor" role="tab" style="padding-top:10px;padding-bottom:10px;">
                        <!-- <a class="xCNMenuplus" role="button" data-toggle="collapse"  href="#odvPanelPASNote" aria-expanded="true"> -->
                            <div id="headingodvPanelPASNote">
                                <!-- <i class="<?php if($nTypePage==1){ echo "fa fa-plus"; }else{ echo "fa fa-minus"; }?> xCNPlus"></i> -->
                                <span class="xCNTextDetail1"><?=language('document/pdtadjstkchk', 'tPASPanelNote'); ?></span>
                            </div>
                        <!-- </a> -->
                    </div>
                    <div id="odvPanelPASNote" class="panel-collapse collapse in" role="tabpanel">
                        <div class="panel-body">
                            <div class="row">

                                <?php if($nTypePage!=1){ ?>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group" style="margin-bottom:7px;">
                                            <label class="xCNLabelFrm active"><?=language('document/pdtadjstkchk', 'tPASUseStock'); ?></label>
                                            <input type="text" id="oetPASUseStock" class="form-control" value="จำนวนนับ 1" readonly>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="<?=$tSizeInputNote;?>">
                                    <div class="form-group" style="margin-bottom:7px;">
                                        <label class="xCNLabelFrm active"><?=language('document/pdtadjstkchk', 'tPASPanelNote'); ?></label>
                                        <textarea class="form-control" maxlength="200" rows="4" id="otaPASNote" name="otaPASNote"><?=$tIuhRmk?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Panel หมายเหตุ -->

        </div>

        <div class="row">
            <!-- แท็บ สินค้าตรวจนับ , สินค้าไม่มีในระบบ -->
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                <ul class="nav nav-tabs xWPASTabColor xWPASTabMenuProduct" style="border-bottom: 0px;">
                    <li class="active"><a class="xWTabCallDetails xWPASTabPdtChk" data-tab="PDTCHK" data-toggle="tab" href="#odvPASContentTable"><?=language('document/pdtadjstkchk', 'tPASTabPdtChk');?></a></li>
                    <li><a class="xWTabCallDetails xWPASTabPdtWithOutSystem" data-tab="PDTSYS" data-toggle="tab" href="#odvPASContentTableWithOutSystem"><?=language('document/pdtadjstkchk', 'tPASTabPdtNotHaveInSys');?></a></li>
                </ul>
            </div>
            <!-- ช่องค้นหาสินค้า -->
            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                <div class="form-group form-horizontal" style="margin-bottom:10px;">
                    <label id="olbPASSeachItemsLabel" class="col-sm-6 control-label" style="padding-left:0px;"><?=language('common/systems', 'tLabelSearch')?> <?=str_replace("<br>","",language('document/pdtadjstkchk', 'tPASTBPdtStkCode')); ?></label>
                    <div class="input-group col-sm-6">
                        <input id="oetSearchItemsFilter" class="xCNHide" value="FTIudStkCode">
                        <input id="oetSearchItems" class="form-control oetTextFilter xCNInputWithoutSingleQuote" type="text" value="" autocomplete="off" placeholder="<?=language('common/systems', 'tLabelInputSearch')?>">
                        <span class="input-group-btn">
                            <button id="obtPASSeachItems" class="btn xCNBtnSearch" type="button">
                                <img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id="odvPASContentTables" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;">
            <div class="tab-content">
                <div id="odvPASContentTable" class="tab-pane fade in active"></div> <!-- สินค้าตรวจนับ -->
                <div id="odvPASContentTableWithOutSystem" class="tab-pane fade"></div> <!-- สินค้าไม่มีในระบบ -->
            </div>
        </div>

    </div>
</div>
</form>

<script>

    //ค้นหาสินค้าใน DT
    $('#oetSearchItems').off('keydown');
    $('#oetSearchItems').on('keydown',function(event){
        if(event.keyCode == 13){
            $('#obtPASSeachItems').click();
        }
    });

    $('#obtPASSeachItems').off('click');
    $('#obtPASSeachItems').on('click',function(){
        var tTabActive = $('.xWPASTabMenuProduct').find('.active .xWTabCallDetails').attr('data-tab');
        var aPackData = {
            FTIudBarCode : $('#oetSearchItems').val(),
            ptPageType   : $('#oetPASTypePage').val(),
            ptFilter     : $('#oetSearchItemsFilter').val()
        };
        JSvPASSearchProduct(aPackData,tTabActive);
    });

    $('.xWTabCallDetails').off('click');
    $('.xWTabCallDetails').on('click',function(){
        // var tPageType = $('#oetPASTypePage').val();
        var tTabType  = $(this).data('tab');

        var tTextTitle  = '<?=language('common/systems', 'tLabelSearch')?>';
        var tFieldName = "";
        var tLabelName = "";

        // if(tPageType == '1'){
            if(tTabType == "PDTCHK"){
                tFieldName = "FTIudStkCode";
                tLabelName = '<?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?>';
            }else{
                tFieldName = "FTPdtBarCode";
                tLabelName = "<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>";
            }
        // }else{
        //     if(tTabType == "PDTCHK"){
        //         tFieldName = "FTIudStkCode";
        //         tLabelName = '<?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?>';
        //     }else{
        //         tFieldName = "FTPdtBarCode";
        //         tLabelName = "<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>";
        //     }
        // }

        $('#oetSearchItemsFilter').val(tFieldName);
        $('#olbPASSeachItemsLabel').text(tTextTitle + ' ' + tLabelName.replace(/<br>/gi,''));
    });

    //เปิด หุบ ของ panel
    // $(function(){
    //     /* On Collapse: Plus to Minus */
    //     $('.collapse').off('shown.bs.collapse');
    //     $('.collapse').on('shown.bs.collapse', function(event){
    //         // console.log(event.currentTarget);
    //         var idName = $(event.currentTarget).attr('id');
    //         var headingTarget = '#heading' + idName.replace('collapse','').trim();
    //         $(headingTarget + ' > i').removeClass('fa-plus').addClass('fa-minus');
    //     });
        
    //     /* On Hidden: Minus to Plus */ 
    //     $('.collapse').off('hidden.bs.collapse');
    //     $('.collapse').on('hidden.bs.collapse', function(event){
    //         // console.log(event.currentTarget); 
    //         var idName = $(event.currentTarget).attr('id');
    //         var headingTarget = '#heading' + idName.replace('collapse','').trim();
    //         $(headingTarget + ' > i').removeClass('fa-minus').addClass('fa-plus');
    //     });
    // });

    var oetInputdate 	= $('.xWDatepicker');
    var container 		= $('.xWForm-GroupDatePicker').length>0 ? $('.xWForm-GroupDatePicker').parent() : "body";
    var options 		= {
			format          		: 'yyyy-mm-dd',
			container       		: container,
			todayHighlight  		: true,
			enableOnReadonly		: false,
			disableTouchKeyboard 	: true,
			autoclose       		: true,
			orientation     		: 'bottom',
			// startDate 				: dPoTime
	};
    oetInputdate.datepicker(options);

    $('.xWPASBtnAddFormCodeToCode').off('click');
    $('.xWPASBtnAddFormCodeToCode').on('click',function(){
        var aModalText = {
			tHead1	    : '<?=language('document/pdtadjstkchk', 'tModalHeadInComplete')?>',
            tDetail1	: '<?=language('document/pdtadjstkchk', 'tModalTextInComplete')?>',
            tHead2	    : '<?=language('document/pdtadjstkchk', 'tModalHeadNotFoundGroup')?>',
			tDetail2	: '<?=language('document/pdtadjstkchk', 'tModalTextNotFoundGroup')?>',
			nType	    : 2
		};
        JSvPASAddProduct($(this).data('tab'),aModalText);
    });

    $('#obtBrowseProductFrom').off('click');
    $('#obtBrowseProductFrom').on('click',function(){
        //oPASBrwProduct.NextFunc.FuncName = 'JSxPASControlBrwPdt';
        oPASBrwProduct.NextFunc.ArgReturn = ['1'];
        JCNxBrowseData('oPASBrwProduct');
    });
    $('#obtBrowseProductTo').off('click');
    $('#obtBrowseProductTo').on('click',function(){
        //oPASBrwProduct.NextFunc.FuncName = 'JSxPASControlBrwPdt';
        oPASBrwProduct.NextFunc.ArgReturn = ['2'];
        JCNxBrowseData('oPASBrwProduct');
    });
    var oPASBrwProduct = {
        Title 		: ['document/pdtadjstkchk','tPASBrwProductTitle'],
        Table		: {Master:'TCNMPdt P',PK:'FTPdtBarCode'},
        Join		: {
						Table	: ['TCNMPdtBar B'],
						On		: ['P.FTPdtCode = B.FTPdtCode LEFT JOIN TCNMPdtUnit U ON P.FTPunCode = U.FTPunCode']
		},
		Where 		: {
						Condition : [
							" AND (P.FTPdtType IN('1','4'))",
							" AND (P.FTPdtStaSet IN('1','2','3'))", 
							" AND P.FTPdtStaAudit IN ('1')",
                            " AND P.FTPdtStaActive = '1'",
                            " AND P.FTPdtStaAlwSale = '1' ",
                            " AND ISNULL(B.FTPdtBarCode,'') != '' ",
                            " AND ISNULL(U.FTPunCode,'') != '' "
						]
		},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASTBBarCode','tPASBrwProductName','tPASBrwProductNameOth','tPASBrwProductNameShort','tPASBrwProductNameShortEng','tPASBrwProductPunCode','tPASBrwProductPgpChain','tPASBrwProductSplCode','tPASBrwProductType','tPASBrwProductQtyRet'],
			DataColumns		: ['B.FTPdtBarCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort','P.FTPdtNameShortEng','U.FTPunName','P.FTPgpChain','P.FTSplCode','P.FTPdtType','P.FCPdtQtyRet','P.FTPdtCode'],
            DisabledColumns : ['10'],
            Perpage			: 20,
            OrderBy			: ['B.FTPdtBarCode'],
            SearchLike	    : ['B.FTPdtBarCode'] //,'P.FTPdtCode'
			//SearchLike	    : ['B.FTPdtBarCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort','P.FTPdtNameShortEng','U.FTPunName','P.FTPgpChain','P.FTSplCode','P.FTPdtType','P.FCPdtQtyRet']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwPdt',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };
    
    
    $('#obtBrowseSupplierFrom').off('click');
    $('#obtBrowseSupplierFrom').on('click',function(){
        oPASBrwSupplier.NextFunc.ArgReturn = ['1'];
        JCNxBrowseData('oPASBrwSupplier');
    });
    $('#obtBrowseSupplierTo').off('click');
    $('#obtBrowseSupplierTo').on('click',function(){
        oPASBrwSupplier.NextFunc.ArgReturn = ['2'];
        JCNxBrowseData('oPASBrwSupplier');
    });
    var oPASBrwSupplier = {
        Title 		: ['document/pdtadjstkchk','tPASBrwSupplierTitle'],
		Table		: {Master:'TCNMSpl',PK:'FTSplCode'},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASBrwSupplierCode','tPASBrwSupplierName','tPASBrwSupplierAddr','tPASBrwSupplierStreet','tPASBrwSupplierDistrict','tPASBrwSupplierDstCode','tPASBrwSupplierPvnCode','tPASBrwSupplierTel','tPASBrwSupplierFax','tPASBrwSupplierStyCode'],
			DataColumns		: ['FTSplCode','FTSplName','FTSplAddr','FTSplStreet','FTSplDistrict','FTDstCode','FTPvnCode','FTSplTel','FTSplFax','FTStyCode'],
            Perpage			: 20,
			OrderBy			: ['FTSplCode'],
			SearchLike	    : ['FTSplCode','FTSplName','FTSplAddr','FTSplStreet','FTSplDistrict','FTDstCode','FTPvnCode','FTSplTel','FTSplFax','FTStyCode']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwSpl',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };


    $('#obtBrowseUserFrom').off('click');
    $('#obtBrowseUserFrom').on('click',function(){
        oPASBrwUser.NextFunc.ArgReturn = ['1'];
        JCNxBrowseData('oPASBrwUser');
    });
    $('#obtBrowseUserTo').off('click');
    $('#obtBrowseUserTo').on('click',function(){
        oPASBrwUser.NextFunc.ArgReturn = ['2'];
        JCNxBrowseData('oPASBrwUser');
    });
    var oPASBrwUser = {
        Title 		: ['document/pdtadjstkchk','tPASBrwUserTitle'],
		Table		: {Master:'TSysUser',PK:'FTUsrCode'},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASBrwUserCode','tPASBrwUserName'],
            DataColumns		: ['FTUsrCode','FTUsrName'],
            ColumnsSize  	: ['15%','85%'],
            Perpage			: 20,
			OrderBy			: ['FTUsrCode'],
			SearchLike	    : ['FTUsrCode','FTUsrName']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwUsr',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };
    
    $('#obtBrowseGroup').off('click');
    $('#obtBrowseGroup').on('click',function(){
        JCNxBrowseData('oPASBrwGroup');
    });
    var oPASBrwGroup = {
        Title 		: ['document/pdtadjstkchk','tPASBrwGroupTitle'],
		Table		: {Master:'TCNMPdtGrp',PK:'FTPgpChain'},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASBrwGroupPgpName','tPASBrwGroupPgpLevel','tPASBrwGroupPgpChain','tPASBrwGroupPgpChainName'],
            DataColumns		: ['FTPgpName','FNPgpLevel','FTPgpChain','FTPgpChainName'],
            Perpage			: 20,
			OrderBy			: ['FTPgpChain'],
			SearchLike	    : ['FTPgpName','FNPgpLevel','FTPgpChain','FTPgpChainName']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwGroup',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };

    $('#obtBrowseLoc').off('click');
    $('#obtBrowseLoc').on('click',function(){
        JCNxBrowseData('oPASBrwLoc');
    });
    var oPASBrwLoc = {
        Title 		: ['document/pdtadjstkchk','tPASBrwLocTitle'],
		Table		: {Master:'TCNMPdtLoc',PK:'FTPlcCode'},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASBrwLocPlcCode','tPASBrwLocPlcName'],
            DataColumns		: ['FTPlcCode','FTPlcName'],
            ColumnsSize  	: ['15%','85%'],
            Perpage			: 20,
			OrderBy			: ['FTPlcCode'],
			SearchLike	    : ['FTPlcCode','FTPlcName']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwLoc',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };

    var oPASBrwProductEditInLine = {
        Title 		: ['document/pdtadjstkchk','tPASBrwProductTitle'],
        Table		: {Master:'TCNMPdt P',PK:'FTPdtCode'},
        Join		: {
						Table	: ['TCNTPdtChkDT CHK'],
						On		: ['CHK.FTPdtCode = P.FTPdtCode']
		},
		Where 		: {
						Condition : [
							" AND (P.FTPdtType IN('1','4'))",
							" AND (P.FTPdtStaSet IN('1','2','3'))", 
							" AND P.FTPdtStaAudit IN ('1')",
                            " AND P.FTPdtStaActive = '1'",
                            " AND CHK.FTPdtCode IS NULL"
						]
		},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASBrwProductCode','tPASBrwProductName','tPASBrwProductNameOth','tPASBrwProductNameShort','tPASBrwProductNameShortEng','tPASBrwProductPunCode','tPASBrwProductStkFac','tPASBrwProductPgpChain','tPASBrwProductSplCode','tPASBrwProductType','tPASBrwProductQtyRet'],
			DataColumns		: ['P.FTPdtCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort','P.FTPdtNameShortEng','P.FTPunCode','P.FCPdtStkFac','P.FTPgpChain','P.FTSplCode','P.FTPdtType','P.FCPdtQtyRet'],
            Perpage			: 20,
			OrderBy			: ['P.FTPdtCode'],
			SearchLike	    : ['P.FTPdtCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort','P.FTPdtNameShortEng','P.FTPunCode','P.FCPdtStkFac','P.FTPgpChain','P.FTSplCode','P.FTPdtType','P.FCPdtQtyRet']
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASControlBrwPdtEditInLine',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };
    
    // Browse Products Insert Manuals
    var oPASBrwProductInsertManuals = {
        Title 		: ['document/pdtadjstkchk','tPASBrwProductTitle'],
        Table		: {Master:'TCNMPdt P',PK:'FTPdtBarCode'},
        Join		: {
						Table	: ['TCNMPdtBar B'],
						On		: [""]
		},
		Where 		: {
						Condition : [
							" AND (P.FTPdtType IN('1','4')) ",
							" AND (P.FTPdtStaSet IN('1','2','3')) ", 
							" AND P.FTPdtStaAudit IN ('1') ",
                            " AND P.FTPdtStaActive = '1' ",
                            //" AND DT.FTIuhDocNo IS NULL ",
                            " AND P.FTPdtStaAlwSale = '1' ",
                            " AND ISNULL(B.FTPdtBarCode,'') != '' ",
                            " AND ISNULL(U.FTPunCode,'') != '' "
						]
		},
        GrideView	: {
            ColumnPathLang	: 'document/pdtadjstkchk',
			ColumnKeyLang	: ['tPASTBBarCode','tPASBrwProductName','tPASBrwProductNameOth','tPASBrwProductNameShort','tPASBrwProductNameShortEng','tPASBrwProductPunCode','tPASBrwProductPgpChain','tPASBrwProductSplCode','tPASBrwProductType','tPASBrwProductQtyRet'],
			DataColumns		: ['B.FTPdtBarCode','P.FTPdtName','P.FTPdtNameOth','P.FTPdtNameShort','P.FTPdtNameShortEng','U.FTPunName','P.FTPgpChain','P.FTSplCode','P.FTPdtType','P.FCPdtQtyRet','P.FTPdtCode'],
            DisabledColumns : ['10'],
            Perpage			: 20,
			OrderBy			: ['B.FTPdtBarCode'],
			SearchLike	    : ['B.FTPdtBarCode'] //'P.FTPdtCode'
        },
        CallBack:{
            ReturnType	    : 'S'
        },
        NextFunc:{
            FuncName	    : 'JSxPASAddBrwPdtManual',
            ArgReturn       : []
		},
		// DebugSQL : 'true'
    };

    
    // var nPageType = $('#oetPASTypePage').val();
    // // console.log(bAdjType);
    // if(nPageType == 2){
    //     var bAdjType  = localStorage.getItem("bPASAdjType");
    //     console.log(bAdjType);
    //     if(bAdjType == true){
    //         console.log('if');
    //         $('#ocbPASAdjType').attr('checked',true);
    //         setTimeout(function(){
    //             localStorage.removeItem("bPASAdjType");
    //         },1000);
    //     }else{
    //         console.log('else');
    //         $('#ocbPASAdjType').attr('checked',false);
    //     }
    //     $('#ocbPASAdjType').attr('disabled',true);
    // }

</script>