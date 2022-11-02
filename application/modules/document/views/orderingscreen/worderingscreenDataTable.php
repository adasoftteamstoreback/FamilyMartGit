<?php
    if(@$aODSDataTable['nStaQuery'] == 99 || @$aODSDataTable == ""){

        $aODSDataTable['rnAllRow'] = 0;
        $aODSDataTable['rnOrderSKU'] = 0;
        $aODSDataTable['rnSKUAmount'] = 0;
        $aODSDataTable['rnCurrentPage'] = 0;
        $aODSDataTable['rnAllPage'] = 0;
        $nCountRow = 0;

    }else{

        $nCountRow = count($aODSDataTable['raItems']);

    }
?>

<?php
    switch ($tODSCurrentSecion) {
        case "SUMMARY":
?>
            <div class="row">
                <div class="col-xs-2 col-md-2 col-lg-2"></div>
                <div class="col-xs-10 col-md-10 col-lg-10">
                    <div style="font-size:14px;font-weight:bold;text-align:right;">
                        <input type="text" class="form-control xCNHide" id="oetODSTotalSKU" value="<?=$nTotalSKU?>">
                        <?php echo language('document/orderingscreen','tODSGrandTotalSKU') . " : " . number_format($nTotalSKU); ?>  <?php echo language('document/orderingscreen','tODSGrandTotalAmount') . " : " . number_format($nTotalAmount,2); ?>
                    </div>
                </div>
            </div>
<?php
            break;
        default:
?>
            <div class="row xWDataTableMargin table-scroll xWTableODS<?php echo $tODSCurrentSecion; ?> xWScrollbarStyle3" style="overflow-x: hidden;">
                <?php
                    if($tODSFromSec == "SUMMARY"){
                ?>
                    <div class="xWODSTitleTable"><?php echo language('document/orderingscreen','tODSTitle'.$tODSCurrentSecion); ?></div>
                <?php
                    }
                ?>
                <table class="table table-striped xCNTableHead xWTableOrdScn xCNTableResize" id="otbTableOrderingScreen<?php echo $tODSCurrentSecion; ?>">
                    <thead>
                        <tr>
                            <th width="45" class="xCNTableCannotSortBy xCNTableCanSortBy" ><?php echo language('document/orderingscreen','tODSTBNo'); ?></th>
                            <th width="90" data-sortby='FTPdtCategory' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBCategory'); ?></th>
                            <th width="95" data-sortby='FTPdtSubCat' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBSubCat'); ?></th>
                            <th <?php if($tODSCurrentSecion != "TOP1000" && $tODSCurrentSecion != "OTHER" && $tODSCurrentSecion != "ADDON"){ echo 'width="120"'; }else{ echo 'width="100%"'; } ?> data-sortby='FTPdtName' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBProductName'); ?></th>
                            <th width="95" data-sortby='FTPdtBarCode' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBBarCode'); ?></th>
                            <th width="55" data-sortby='FTPdtDelivery' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBDeliveryType'); ?></th>
                            <th width="45" data-sortby='FCPdtIntransit' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBCostPrice'); ?></th>
                            <?php if($tODSCurrentSecion != "TOP1000" && $tODSCurrentSecion != "OTHER" && $tODSCurrentSecion != "ADDON"){ ?>
                            <th width="39" data-sortby='FTPdtPromo' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBPromo'); ?></th>
                            <?php } ?>
                            <th width="70" data-sortby='FDDeliveryDate' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBDeliveryDate'); ?></th>
                            <th width="45" data-sortby='FCPdtStock' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBStock'); ?></th>
                            <th width="50" data-sortby='FCPdtLotSize' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBLotSize'); ?></th>
                            <th width="60" data-sortby='FCPdtADS' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBADS'); ?></th>
                            <th width="55" data-sortby='FCPdtSGOQty' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBSGOQTY'); ?></th>
                            <th width="50" data-sortby='FCPdtOrdLot' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBOrderLOT'); ?></th>
                            <th width="50" data-sortby='FCPdtOrdPcs' data-section='<?php echo $tODSCurrentSecion; ?>' class="xCNSortDatacolumn xCNTableCanSortBy"><?php echo language('document/orderingscreen','tODSTBOrderPCS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(is_array($aODSDataTable['raItems']) || is_object($aODSDataTable['raItems'])){
                            foreach($aODSDataTable['raItems'] AS $nKey => $aValue){
                                if($aValue['FTPdtSecCode'] == $tODSCurrentSecion){
                    ?>
                                    <tr id="otrODSTableList<?php echo $tODSCurrentSecion;?>" class="xWODSTr<?=$tODSCurrentSecion;?><?=$aValue['FNXdtSeqNo'];?> xCNTableTrClickActive <?php if($aValue['FTPdtPOFlag']!='0'){ echo "xWODSPOFlag"; } ?>" data-page="<?php echo $aODSDataTable['rnCurrentPage']; ?>">
                                        <td align="center">
                                            <?php echo $aValue['rtRowID']; ?>
                                            <input type="text" class="xCNHide" id="oetODSPdtLotSize<?=$tODSCurrentSecion?><?=$aValue['FNXdtSeqNo'];?>" value="<?php echo $aValue['FCPdtLotSize']; ?>">
                                        </td>
                                        <td ><?php echo $aValue['FTPdtCategory']; ?></td>
                                        <td class="xWPdtSubCat"><?php echo $aValue['FTPdtSubCat']; ?></td>
                                        <td ><?php echo $aValue['FTPdtName']; ?></td>
                                        <td ><?php echo $aValue['FTPdtBarCode']; ?></td>
                                        <td ><?php echo $aValue['FTPdtDelivery']; ?></td>
                                        <td align="right"><?php echo $aValue['FCPdtIntransit']; ?></td>
                                        <?php if($tODSCurrentSecion != "TOP1000" && $tODSCurrentSecion != "OTHER" && $tODSCurrentSecion != "ADDON"){ ?>
                                        <td >
                                            <?php 
                                                $aDataPromo = explode(',',$aValue['FTPdtPromo']);
                                                if(count($aDataPromo) > 1){
                                                    // var_dump($aDataPromo);
                                                    if($aDataPromo[0]==" "){
                                                        echo $aDataPromo[1];
                                                    }else{
                                                        echo $aDataPromo[0].",<br>".$aDataPromo[1];
                                                    }
                                                }else{
                                                    echo $aValue['FTPdtPromo'];
                                                }
                                             ?>
                                        </td>
                                        <?php } ?>
                                        <td class="xWDeliveryDate" align="center"><?php echo date_format($aValue['FDDeliveryDate'],"d/m/Y"); ?></td>
                                        <td align="right"><?php echo $aValue['FCPdtStock']; ?></td>
                                        <td align="right"><?php echo $aValue['FCPdtLotSize']; ?></td>
                                        <td align="right"><?php echo ceil($aValue['FCPdtADS']); ?></td>
                                        <?php 
                                            //ถ้า FTSysUsrValue ใน TSysConfig = 0 ให้โชว์เป็นค่าว่าง
                                            //แก้ไข 15/07/2019 วัฒน์
                                            if($_SESSION["tSysUsrValueOrderingScreen"] == 0){
                                                $nFCPdtSGOQty = '';
                                            }else{
                                                $nFCPdtSGOQty = $aValue['FCPdtSGOQty'];
                                            }
                                        ?>
                                        <td align="right"><?=$nFCPdtSGOQty?></td>
                                        <td align="right" class="xWPdtOrdLot xCNColorEditLine">
                                            <div class="field a-field a-field_a1 page__field">
                                                <?php 
                                                    if($aValue['FTPdtPOFlag'] == '1'){
                                                        $nLotPDT_Tmp = $aValue['FCPdtOrdLot_Tmp'];
                                                        
                                                    }else{
                                                        $nLotPDT_Tmp = NULL;
                                                    }
                                                    if($aValue['FCPdtOrdLot'] == '' || $aValue['FCPdtOrdLot'] == null){ 
                                                        $nLotPDT = $aValue['FCPdtOrdLot'];
                                                    }else{
                                                        $nLotPDT = number_format($aValue['FCPdtOrdLot']);
                                                    }
                                                ?>
                                                <input class="field__input a-field__input xCNInputNumericWithoutDecimal xWInputPdtOrdLot xWPdtOrdLot<?=$tODSCurrentSecion?> text-right" style="padding:0;" id="oetODSPdtOrdLot<?=$tODSCurrentSecion?><?=$aValue['FNXdtSeqNo']; ?>" value="<?=$nLotPDT;?>" data-seq="<?=$aValue['FNXdtSeqNo']; ?>" data-val="<?=$nLotPDT;?>" data-barcode="<?=$aValue['FTPdtBarCode'];?>" data-pdtcode="<?php echo $aValue['FTPdtCode']; ?>" data-sec="<?php echo $tODSCurrentSecion; ?>" data-valtmp="<?=$nLotPDT_Tmp?>" autocomplete="off" placeholder="<?=$nLotPDT_Tmp?>">
                                            </div>
                                        </td>

                                        <?php 
                                            if($aValue['FCPdtOrdPcs'] == '' || $aValue['FCPdtOrdPcs'] == null){ 
                                                $nLotPCS = $aValue['FCPdtOrdPcs'];
                                            }else{
                                                $nLotPCS = number_format($aValue['FCPdtOrdPcs']);
                                            }
                                        ?>

                                        <td align="right">
                                        <?php
                                            if($aValue['FTPdtPOFlag'] == '1'){
                                                echo "<span class='xWLotPCS'>$aValue[FCPdtOrdPcs_Tmp]</span>";
                                            }else{
                                                echo "<span>$nLotPCS</span>";
                                            }
                                        ?>
                                            
                                        </td>
                                    </tr>
                    <?php
                                }
                            }
                        }else{
                    ?>
                        <tr>
                            <td  colspan="15" class="text-center"><?php echo language('document/orderingscreen','tODSTBNotFound'); ?></td>
                        </tr>
                    <?php
                        }
                        if($nCountRow <= 0){
                    ?>
                        <tr>
                            <td  colspan="15" class="text-center"><?php echo language('document/orderingscreen','tODSTBNotFound'); ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <?php
                            if($tODSCurrentSecion == "ADDON"){
                        ?>
                        <tr>
                            <td><img class="xCNImageInsert xCNBlockWhenApprove" style="margin:auto;" src="<?=$tBase_url?>application/modules/common/assets/images/icons/add-circular2.png"></td>
                            <th  colspan="2">
                                <label class="field a-field a-field_a1 page__field" style="margin-bottom:0px;">
                                    <input class="field__input a-field__input xCNInsertInputPDTorBarcode xCNInputWithoutSingleQuote xWInputAddPdt" id="oetODSAddPdt" type="text" autocomplete="off" placeholder="<?=language('common/systems', 'tPlaceholderProductorbarcode')?>" maxlength="25" disabled>
                                </label>
                            </th>
                            <th  colspan="13"></th>
                        </tr>
                        <?php
                            }
                        ?>
                        <tr>
                            <td nowrap colspan="1"></td>
                            <th nowrap><?php echo language('document/orderingscreen','tODSTBTotalSKU')." ".number_format($aODSDataTable['rnAllRow']); ?></th>
                            <th nowrap><?php echo language('document/orderingscreen','tODSTBOrderSKU')." ".number_format($aODSDataTable['rnOrderSKU']); ?></th>
                            <th nowrap><?php echo language('document/orderingscreen','tODSTBSKUAmount')." ".number_format($aODSDataTable['rnSKUAmount'],2); ?></th>
                            <?php
                                if($tODSCurrentSecion == "ADDON"){
                            ?>
                                <td nowrap colspan="12"></td>
                            <?php
                                }else{
                            ?>
                                <td nowrap colspan="11"></td>
                            <?php
                                }
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row" style="margin-top:15px;">
                <!-- เปลี่ยน -->
                <div class="col-md-6">
                    <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aODSDataTable['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aODSDataTable['rnCurrentPage']?> / <?=$aODSDataTable['rnAllPage']?></p>
                </div>
                <!-- เปลี่ยน -->
                <?php if($aODSDataTable['nStaQuery'] == 1){ ?>
                    <div class="col-md-6">
                        <div class="xWPageOrderingScreen<?php echo $tODSCurrentSecion; ?> btn-toolbar pull-right"> <!-- เปลี่ยนชื่อ Class เป็นของเรื่องนั้นๆ --> 
                            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                            <button type="button" onclick="JSvODSClickPage('previous','<?php echo $tODSCurrentSecion; ?>','<?php echo $tODSFromSec; ?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                            </button>
                            <?php for($i=max($nPage-2, 1); $i<=max(0, min($aODSDataTable['rnAllPage'],$nPage+2)); $i++){?> <!-- เปลี่ยนชื่อ Parameter Loop เป็นของเรื่องนั้นๆ --> 
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
                                <button onclick="JSvODSClickPage('<?php echo $i?>','<?php echo $tODSCurrentSecion; ?>','<?php echo $tODSFromSec; ?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                            <?php } ?>
                            <?php if($nPage >= $aODSDataTable['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                            <button type="button" onclick="JSvODSClickPage('next','<?php echo $tODSCurrentSecion; ?>','<?php echo $tODSFromSec; ?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <input type="text" class="xCNHide" id="oetODSCurrentPageInTab<?php echo $tODSCurrentSecion; ?>" value="<?php echo $aODSDataTable['rnCurrentPage']; ?>">
            <input type="text" class="xCNHide" id="oetODSAllRow<?php echo $tODSCurrentSecion; ?>" value="<?php echo $aODSDataTable['rnAllRow']; ?>">
            <input type="text" class="xCNHide" id="oetODSRowTable<?php echo $tODSCurrentSecion; ?>" value="<?php echo $nRowTable; ?>">
            <input type="text" class="xCNHide" id="oetODSPage<?php echo $tODSCurrentSecion; ?>" value="<?php echo $nPage; ?>">

<?php
            break;
    }
?>

<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script src="<?=$tBase_url?>application/modules/common/assets/js/global/ColResizable1.6/colResizable-1.6.js?v=<?php echo date("dmyhis"); ?>"></script>