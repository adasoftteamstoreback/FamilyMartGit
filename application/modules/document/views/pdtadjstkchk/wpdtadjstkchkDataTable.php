<?php
    $tSizeIudQty            = "50px";
    $tSizeIudChkDate        = "90px";
    $tSizeIudChkTime        = "80px";
    $tSizeBarCode           = "100px";
    $tSizeStkFac            = "50px";
    $tSizeQtyBal            = "50px";
    $tSizeQtyDiff           = "50px";
    $tSizeSleB4Count        = "45px";
    $tSizeWahQty            = "55px";
    $tSizePdtStkCode        = "60px";
    $tSizeNo                = "35px";
    $tSizePunCode           = "50px";
    $tSizeDelete            = "40px";
    $tSizePlcCode           = "60px";
    $tSizeAfterCount        = "80px";
?>

<div class="xWPASTableProduct table-responsive row xWDataTableMargin table-scroll" style="overflow-y:hidden">
    <table class="table table-striped xCNTableHead xWTableOrdScn xCNTableResize" id="otbPASTableProduct">
        <thead>
            <tr>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeNo;?>" data-colum="FNIudSeqNo"><?=language('document/pdtadjstkchk', 'tPASTBNo'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizePdtStkCode;?>" data-colum="FTIudStkCode"><?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?></th>
                <!-- <th nowrap><?=language('document/pdtadjstkchk', 'tPASTBPdtCode'); ?></th> --> <!-- COMSHEET 2019 310 - ให้ซ่อนคอลัม PdtCode -->
            <?php if($nTypePage==1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeBarCode;?>" data-colum="FTIudBarCode"><?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?></th>
            <?php } ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-colum="FTPdtName"><?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizePunCode;?>" data-colum="FTPunName"><?=language('document/pdtadjstkchk', 'tPASTBPunCode'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeStkFac;?>"  data-colum="FCIudStkFac"><?=language('document/pdtadjstkchk', 'tPASTBStkFac'); ?></th>
            <?php if($nTypePage!=1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeWahQty;?>" data-colum="FCIudSetPrice"><?=language('document/pdtadjstkchk', 'tPASTBWahQty'); ?></th>
            <?php } ?>
            <?php if($nTypePage==1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizePlcCode;?>" data-colum="FTPlcCode"><?=language('document/pdtadjstkchk', 'tPASTBPlcCode'); ?></th>
            <?php } ?>
            <?php if($nTypePage!=1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeSleB4Count;?>" data-colum="FCIudUnitC2"><?=language('document/pdtadjstkchk', 'tPASTBSleB4Count'); ?></th>
            <?php } ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeIudQty;?>" data-colum="FCIudUnitC1"><?=language('document/pdtadjstkchk', 'tPASTBIudQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeIudChkDate;?>" data-colum="FDIudChkDate"><?=language('document/pdtadjstkchk', 'tPASTBIudChkDate'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeIudChkTime;?>" data-colum="FTIudChkTime"><?=language('document/pdtadjstkchk', 'tPASTBIudChkTime'); ?></th>
            <?php if($nTypePage!=1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeAfterCount;?>" data-colum="FTClrName"><?=language('document/pdtadjstkchk', 'tPASTBAfterCount'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeQtyDiff;?>" data-colum="FCIudQtyDiff"><?=language('document/pdtadjstkchk', 'tPASTBQtyDiff'); ?></th>
            <?php } ?>
            <?php if($nTypePage!=1){ ?>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap width="<?=$tSizeQtyBal;?>" data-colum="FCIudQtyBal"><?=language('document/pdtadjstkchk', 'tPASTBQtyBal'); ?></th>
            <?php } ?>
                <th class="xCNTh2Line" nowrap width="<?=$tSizeDelete;?>"><?=language('document/pdtadjstkchk', 'tPASTBDelete')?></th>
            <tr>
        </thead>
        <tbody>
        <?php
            if($aDataTable['nStaQuery'] == 1){
                // $nAfterCount    = 0;
                foreach($aDataTable['aItems'] AS $tKey => $tValue){
                    //คำนวณ เคลื่อนไหวหลังตรวจนับ
                    // if($aDataAfterCount['nStaQuery'] == 1 && $nTypePage!=1){
                    //     foreach($aDataAfterCount['aItems'] AS $tKey => $aValueAC){
                    //         if($aValueAC['FTIudStkCode'] == $tValue['FTIudStkCode']){
                    //             $nAfterCount = $aValueAC['FCAfterCount'];
                    //         }
                    //     }
                    // }else{
                    //     $nAfterCount    = 0;
                    // }
        ?>
                    <tr class="xWPASProductSeq<?=$tValue['FNIudSeqNo']?> xWPASDataPdtList xCNTableTrClickActive" data-seq="<?=$tValue['FNIudSeqNo']?>" data-stkcode="<?=$tValue['FTIudStkCode']?>" data-pdtname="<?=$tValue['FTPdtName']?>">
                        <td nowrap><?=$tValue['FNIudSeqNo']?></td>
                        <td nowrap><?=$tValue['FTIudStkCode']?>
                            <!-- <div class="field a-field a-field_a1 page__field">
                                <input id="oetPASPdtCode<?=$tValue['FNIudSeqNo']?>" name="oetPASPdtCode<?=$tValue['FNIudSeqNo']?>" class="inputs field__input a-field__input xWPASClickEditInLine xCNInputNumericWithDecimal" type="text" value="<?=$tValue['FTIudStkCode']?>" data-oldpdtcode="<?=$tValue['FTPdtCode']?>">
                            </div> -->
                        </td>
                        <!-- <td nowrap><?=$tValue['FTPdtCode']?></td> -->
                        <?php if($nTypePage==1){ ?>
                            <td nowrap><?=$tValue['FTIudBarCode']?></td>
                        <?php } ?>
                        <td nowrap><?=$tValue['FTPdtName']?></td>
                        <td nowrap><?=$tValue['FTPunName']?></td>
                        <td nowrap class="xWPASIudStkFac<?=$tValue['FNIudSeqNo']?> text-right"><?=number_format($tValue['FCIudStkFac'],2)?></td> <!-- เปลี่ยนเป็น StkFac [FCIudUnitFact] -->
                        <?php if($nTypePage!=1){ ?>
                            <td nowrap class="text-right">
                                <span class="xWPASIudWahQty<?=$tValue['FNIudSeqNo']?>"><?=$tValue['FCIudWahQty']?></span>
                            </td>
                        <?php } ?>
                        <?php if($nTypePage==1){ ?>
                            <td nowrap class="text-left xCNColorEditLine">
                                <div class="field a-field a-field_a1 page__field">
                                    <input id="oetPASPlcCode<?=$tValue['FNIudSeqNo']?>" name="oetPASPlcCode<?=$tValue['FNIudSeqNo']?>" class="inputs field__input a-field__input text-left xWInputEditInLine xWInputCanEdit" type="text" value="<?=$tValue['FTPlcCode']?>" style="padding: 0px;">
                                </div>
                            </td>
                        <?php } ?>
                        <?php if($nTypePage!=1){ ?>
                            <td nowrap class="text-right"><?=$tValue['FCIudUnitC2']?></td> <!-- ขายก่อนนับ -->
                        <?php } ?>
                        <td nowrap class="text-right <?php if($nTypePage==1){ echo "xCNColorEditLine"; }?>">
                            <?php if($nTypePage==1){ ?>
                                <div class="field a-field a-field_a1 page__field">
                                    <input id="oetPASIudQtyC1<?=$tValue['FNIudSeqNo']?>" name="oetPASIudQtyC1<?=$tValue['FNIudSeqNo']?>" class="inputs field__input a-field__input xCNInputNumericWithDecimal text-right xWInputEditInLine xWInputCanEdit" type="text" value="<?=$tValue['FCIudUnitC1']?>" style="padding: 0px;">
                                </div>
                            <?php }else{ ?>
                                    <span class="xWPASIudQtyC1<?=$tValue['FNIudSeqNo']?>"><?=$tValue['FCIudUnitC1']?></span>
                            <?php } ?>
                        </td>
                        <td nowrap class="text-center <?php if($nTypePage==1){ echo "xCNColorEditLine"; }?>">
                            <?php if($nTypePage==1){ ?>
                                <div class="field a-field a-field_a1 page__field">
                                    <input id="oetPASIudChkDate<?=$tValue['FNIudSeqNo']?>" name="oetPASIudChkDate<?=$tValue['FNIudSeqNo']?>" class="xWPASNotNextFocus inputs field__input a-field__input text-center xWDatepicker xWDatePickerChange xCNInputAddressNumber " type="text" value="<?=(is_null($tValue['FDIudChkDate']) ? '' : date_format($tValue['FDIudChkDate'],'Y-m-d'))?>" style="padding: 0px;">
                                </div>
                            <?php 
                                }else{ 
                                    echo (is_null($tValue['FDIudChkDate']) ? '' : date_format($tValue['FDIudChkDate'],'Y-m-d'));
                                }
                            ?>
                        </td>
                        <td nowrap class="text-center <?php if($nTypePage==1){ echo "xCNColorEditLine"; }?>">
                            <?php if($nTypePage==1){ ?>
                                <div class="field a-field a-field_a1 page__field">
                                    <input id="oetPASIudChkTime<?=$tValue['FNIudSeqNo']?>" name="oetPASIudChkTime<?=$tValue['FNIudSeqNo']?>" class="xWPASNotNextFocus inputs field__input a-field__input text-center xWTimepicker " type="text" value="<?=(is_null($tValue['FTIudChkTime']) ? '' : date_format(date_create($tValue['FTIudChkTime']),'H:i:s')) ?>" style="padding: 0px;">
                                </div>
                            <?php 
                                }else{ 
                                    echo (is_null($tValue['FTIudChkTime']) ? '' : date_format(date_create($tValue['FTIudChkTime']),'H:i:s'));
                                }
                            ?>
                        </td>
                        <?php if($nTypePage!=1){ ?>
                            <td nowrap class="text-right">
                                <span class="xWPASAfterCount<?=$tValue['FNIudSeqNo']?>"><?=number_format($tValue['FTClrName'],0);?></span>
                            </td>
                            <td nowrap class="text-right">
                                <span class="xWPASQtyDiff<?=$tValue['FNIudSeqNo']?>"><?=$tValue['FCIudQtyDiff']?></span>
                            </td>
                            <td nowrap class="text-right xCNColorEditLine">
                                <div class="field a-field a-field_a1 page__field">
                                    <input id="oetPASIudQtyBal<?=$tValue['FNIudSeqNo']?>" name="oetPASIudQtyBal<?=$tValue['FNIudSeqNo']?>" class="inputs field__input a-field__input xCNInputNumericWithDecimal text-right xWInputEditInLine xWInputCanEdit" type="text" value="<?=$tValue['FCIudQtyBal'];?>" style="padding: 0px;"> <!-- $tValue['FCIudQtyBal'] $nBal -->
                                </div>
                            </td>
                        <?php } ?>
                        <td style="text-align: center; vertical-align:middle;">                         
                            <img class="xCNIconTable xWIconDelete" src="<?=$tBase_url?>application/modules/common/assets/images/icons/delete.png">
                        </td>
                    </tr>
        <?php
                }  

            }else{
        ?>
                <tr><td nowrap colspan="9999" class="text-center"><?=language('document/pdtadjstkchk', 'tPASTBNotFoundData'); ?></td></tr>
        <?php
            }
        ?>
            
        </tbody>
        <tfoot>
            <tr>
                <td><img class="xCNImageInsert xWDisabledOnApvSub" style="margin:auto;" src="<?=$tBase_url?>application/modules/common/assets/images/icons/add-circular2.png"></td> <!-- xCNBlockWhenApprove -->
                <th  colspan="2">
                    <label class="field a-field a-field_a1 page__field" style="margin-bottom:0px;">
                        <input class="field__input a-field__input xCNInsertInputPDTorBarcode xCNInputWithoutSingleQuote xWInputAddPdt xWDisabledOnApvSub" id="oetPASAddPdt" type="text" autocomplete="off" placeholder="<?=language('common/systems', 'tPlaceholderProductorbarcode')?>" maxlength="25">
                    </label>
                </th>
                <th  colspan="9999"></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row" style="margin-top:15px;">
<?php
if($aDataTable['nStaQuery'] == 1){
?> 
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <p class="ospTextpagination" style="margin-top:5px;"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($aDataTable['nAllRow'])?> <?=language('common/systems','tRecord')?> <?=language('common/systems','tCurrentPage')?> <?=number_format($aDataTable['nCurrentPage'])?> / <?=number_format($aDataTable['nAllPage'])?></p>
    </div>
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <div class="row">

            <div class="col-sm-4">
                <div class="form-group form-horizontal">
                    <label class="col-sm-5 control-label" style="padding-left:0px;">ไปยังหน้า</label>
                    <div class="input-group col-sm-7">
                        <input type="text" class="form-control xCNInputNumericWithDecimal" id="oetPASGotoPage" placeholder="<?=$aDataTable['nCurrentPage']?>" value="">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="obtPASSubmitGotoPage">ตกลง</button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="xWPagePdtAdjStkChk btn-toolbar pull-right">
                    <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                    <button onclick="JSxPASClickPage('1','1')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>><?=language('common/systems','tFirstPage')?></button>
                    <button onclick="JSxPASClickPage('previous','1')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                        <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                    </button>
                    <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataTable['nAllPage'],$nPage+2)); $i++){?>
                        <?php 
                            if($nPage == $i){ 
                                $tActive        = 'active'; 
                                $tDisPageNumber = 'disabled';
                            }else{ 
                                $tActive        = '';
                                $tDisPageNumber = '';
                            }
                        ?>
                        <button onclick="JSxPASClickPage('<?php echo $i?>','1')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                    <?php } ?>
                    <?php if($nPage >= $aDataTable['nAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                    <button onclick="JSxPASClickPage('next','1')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm xWBtnNext" <?php echo $tDisabledRight ?>>
                        <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                    </button>
                    <button onclick="JSxPASClickPage('<?=$aDataTable['nAllPage']?>','1')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>><?=language('common/systems','tLastPage')?></button>
                </div>
            </div>

        </div>

    </div>
</div>

<input type="text" class="xCNHide" id="oetPASTotalPage" value="<?=$aDataTable['nAllPage']?>">
<input type="text" class="xCNHide" id="oetPASCurrentPage" name="oetPASCurrentPage" value="<?=$aDataTable['nCurrentPage']?>">

<?php
}
?>

<input type="text" class="xCNHide" id="oetPASStaDT" name="oetPASStaDT" value="<?=$aDataTable['nStaQuery']?>">
<input type="text" class="xCNHide" id="oetPASStaChkDateDT" name="oetPASStaChkDateDT" value="<?=($bChkDateDT === TRUE ? 'TRUE' : 'FALSE')?>">

</div>



<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>

    $('.xWPASFilterSearch').off('click');
    $('.xWPASFilterSearch').on('click',function(){
        var tTextTitle  = '<?=language('common/systems', 'tLabelSearch')?>';
        var tTextFilter = '';
        $('#oetSearchItemsFilter').val($(this).data('colum'));

        switch($(this).data('colum')){
            case "FNIudSeqNo":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBNo'); ?>';
            break;
            case "FTIudStkCode":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?>';
            break;
            case "FTIudBarCode":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>';
            break;
            case "FTPdtName":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?>';
            break;
            case "FTPunName":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPunCode'); ?>';
            break;
            case "FCIudStkFac":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBStkFac'); ?>';
            break;
            case "FCIudSetPrice":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBWahQty'); ?>';
            break;
            case "FTPlcCode":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPlcCode'); ?>';
            break;
            case "FCIudUnitC2":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBSleB4Count'); ?>';
            break;
            case "FCIudUnitC1":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBIudQty'); ?>';
            break;
            case "FDIudChkDate":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBIudChkDate'); ?>';
            break;
            case "FTIudChkTime":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBIudChkTime'); ?>';
            break;
            case "FTClrName":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBAfterCount'); ?>';
            break;
            case "FCIudQtyDiff":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBQtyDiff'); ?>';
            break;
            case "FCIudQtyBal":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBQtyBal'); ?>';
            break;
        }


        $('#olbPASSeachItemsLabel').text(tTextTitle + ' ' + tTextFilter.replace(/<br>/gi,''));

    });

    $('#oetPASGotoPage').off('keydown');
    $('#oetPASGotoPage').on('keydown',function(e){
        switch(event.keyCode){
            case 13:
                $('#obtPASSubmitGotoPage').click();
            break;
        }
    });

    $('#obtPASSubmitGotoPage').off('click');
    $('#obtPASSubmitGotoPage').on('click',function(){
        var nPage       = parseInt($('#oetPASGotoPage').val());
        var nMaxPage    = parseInt($('#oetPASTotalPage').val());

        if(nPage <= nMaxPage && nPage > 0){
            JSxPASClickPage(nPage,'1');
        }else{
            $('#oetPASGotoPage').val('');
        }
    });

    $('document').ready(function () {
        
        $('.xWInputEditInLine').off('keydown');
        $('.xWInputEditInLine').on('keydown',function(){
            switch(event.keyCode){
                case 13:
                    if(sessionStorage.getItem("EditInLine") != "2"){
                        sessionStorage.setItem("EditInLine", "1");
                        JSxPASEditInLine($(this),1);
                    }
                    break;
                case 38://up
                    $('.xWInputEditInLine').eq($('.xWInputEditInLine').index(this) - 1).focus();
                    break;
                case 40://down
                    $('.xWInputEditInLine').eq($('.xWInputEditInLine').index(this) + 1).focus();
                    break;
                case 39://right
                    $('.xWInputEditInLine').eq($('.xWInputEditInLine').index(this) + 1).focus();
                    break;
                case 37://left
                    $('.xWInputEditInLine').eq($('.xWInputEditInLine').index(this) - 1).focus();
                    break;
            }
        });
        
        $('.xWInputEditInLine').off('focus');
        $('.xWInputEditInLine').on('focus',function(){
            this.select();
        });
    });

    $(".xWDatePickerChange").datepicker({
        // setDate                 : new Date(),
        format          		: 'yyyy-mm-dd',
        container       		: $('.xWForm-GroupDatePicker').length>0 ? $('.xWForm-GroupDatePicker').parent() : "body",
        todayHighlight  		: true,
        enableOnReadonly		: false,
        disableTouchKeyboard 	: true,
        autoclose       		: true,
        orientation     		: 'bottom',
        // startDate               : new Date(),
        onSelect: function(dateText) {
            $(this).change();
            // console.log('select');
        }
    }).on("blur", function() {
        if(sessionStorage.getItem("EditInLine") != "2"){
            sessionStorage.setItem("EditInLine", "1");
            JSxPASEditInLine($(this),2);
        }
    }).on("keydown", function() {
        if(event.keyCode == 13){
            if(sessionStorage.getItem("EditInLine") != "2"){
                sessionStorage.setItem("EditInLine", "1");
                JSxPASEditInLine($(this),2);
            }
            this.blur();
        }
        
    }).on('hide', function() {
        if(sessionStorage.getItem("EditInLine") != "2"){
            sessionStorage.setItem("EditInLine", "1");
            JSxPASEditInLine($(this),2);
        }
        if($(this).val()==""){
            $(this).val(JStPASGetDateTime(121));
        }
    });

    $('.xWInputEditInLine').off('change');
    $('.xWInputEditInLine').on('change',function(){
        if(sessionStorage.getItem("EditInLine") != "2"){
            sessionStorage.setItem("EditInLine", "1");
            JSxPASEditInLine($(this),1);
        }
    });

    $('.xWTimepicker').datetimepicker({
        format                  : 'HH:mm:ss',
        widgetParent            : $('.xWForm-GroupDatePicker').length > 0 ? $('.xWForm-GroupDatePicker').parent() : "body",
    }).on('dp.hide', function(){
        if(sessionStorage.getItem("EditInLine") != "2"){
            sessionStorage.setItem("EditInLine", "1");
            JSxPASEditInLine($(this),2);
        }
    });

    $('.xWIconDelete').off('click');
    $('.xWIconDelete').on('click',function(){
        var tStkCode = $(this).parent().parent().data('stkcode');
        var tPdtName = $(this).parent().parent().data('pdtname');
        var oElm     = $(this);
        
        var aModalText = {
			tHead	: '<?=language('common/systems', 'tModalDelete')?>',
			tDetail	: '<?=language('common/systems', 'tModalConfirmDeleteItems')?>' + ' ' + tStkCode + ' (' + tPdtName + ')',
			nType	: 1
		};
		JSxPASAlertMessage(aModalText);
		$('.xWPASConfirmAlertMessage').off('click');
		$('.xWPASConfirmAlertMessage').on("click",function(){
			JSxPASDeleteProduct(oElm);
		});
    });
    
    //New Edit inline
    $( document ).ready(function() {
        tCheckEventClick    = 0;

        $('.xWPASClickEditInLine').off('click');
        $('.xWPASClickEditInLine').on('click',function() { 
            poElement = this;
            if (poElement.getAttribute("data-dblclick") == null) {
                poElement.setAttribute("data-dblclick", 1);
                $(poElement).select();
                setTimeout(function () {
                    if (poElement.getAttribute("data-dblclick") == 1) {
                        var tEvent = 'Click';
                        alert(tEvent);
                        // JSxEditInlineByEvent(poElement,tEvent);
                    }
                    poElement.removeAttribute("data-dblclick");
                }, 300);
            } else {
                poElement.removeAttribute("data-dblclick");
                var tEvent  = 'Doubleclick';
                var nSeq    = $(poElement).parents('.xWPASDataPdtList').data('seq');
                oPASBrwProductEditInLine.NextFunc.ArgReturn  = [nSeq];
                JCNxBrowseData('oPASBrwProductEditInLine');
                // alert(tEvent);
                // JSxEditInlineByEvent(poElement,tEvent);
            }
        });
    });

    $('.xCNImageInsert').off('click');
    $('.xCNImageInsert').on('click',function(){
        var tDocNo = $('#oetPASDocNo').val();
        oPASBrwProductInsertManuals.Join.On = ["P.FTPdtCode = B.FTPdtCode LEFT JOIN TCNMPdtUnit U ON P.FTPunCode = U.FTPunCode "] //LEFT JOIN TCNTPdtChkDT DT WITH(NOLOCK) ON P.FTPdtCode = DT.FTPdtCode AND DT.FTIuhDocNo='" + tDocNo + "'
        JCNxBrowseData('oPASBrwProductInsertManuals');
    });

    $('#oetPASAddPdt').on('change',function(){
        $('#oetPASPdtAndBarFromCode').val($(this).val());
        $('#oetPASPdtAndBarToCode').val($(this).val());
        JSvPASAddProduct('PdtAndBar','');

        $(this).val('');
    });

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