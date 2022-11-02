<?php
    $tSizeNo                = "35px";
    $tSizePlcCode           = "60px";
    $tSizeSetPrice          = "70ox";
    $tSizeIudQty            = "50px";
    $tSizeIudChkDate        = "90px";
    $tSizeIudChkTime        = "80px";
    $tSizeBarCode           = "180px";
?>

<div class="xWPASTableProduct table-responsive row xWDataTableMargin table-scroll" style="overflow-y:hidden">
    <table class="table table-striped xCNTableHead xWTableOrdScn xCNTableResize" id="otbPASTableProduct" style="border-bottom: 1px solid #ddd;">
        <thead>
            <tr>
                <th class="xCNTh2Line" nowrap width="<?=$tSizeNo;?>"><?=language('document/pdtadjstkchk', 'tPASTBNo'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizeBarCode;?>" data-colum="FTPdtBarCode"><?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap data-colum="FTPdtName"><?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizePlcCode;?>" data-colum="FTPlcCode"><?=language('document/pdtadjstkchk', 'tPASTBPlcCode'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizeSetPrice;?>" data-colum="FCIudSetPrice"><?=language('document/pdtadjstkchk', 'tPASTBPrice'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizeIudQty;?>" data-colum="FCIudUnitC1"><?=language('document/pdtadjstkchk', 'tPASTBIudQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizeIudChkDate;?>" data-colum="FDIudChkDate"><?=language('document/pdtadjstkchk', 'tPASTBIudChkDate'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch2" nowrap width="<?=$tSizeIudChkTime;?>" data-colum="FTIudChkTime"><?=language('document/pdtadjstkchk', 'tPASTBIudChkTime'); ?></th>
            <tr>
        </thead>
        <tbody>

        <?php
            if($aDataTable['nStaQuery'] == 1){
                foreach($aDataTable['aItems'] AS $nKey => $aValue){
                    $tDocNoShotCut = substr($aValue['FTIuhDocNo'],11) . "_" . $aValue['FTPlcCode'] . "_" . $aValue['FTPdtBarCode'];
        ?>
                    <tr class="xWPASPdtWithOutSystem_<?=$tDocNoShotCut;?> xCNTableTrClickActive" data-docno="<?=$aValue['FTIuhDocNo'];?>" data-barcode="<?=$aValue['FTPdtBarCode'];?>" data-plc="<?=$aValue['FTPlcCode']?>">
                        <td nowrap><?=$aValue['RowID']?></td>
                        <td nowrap><?=$aValue['FTPdtBarCode']?></td>

                        <!-- PDT NAME -->
                        <td nowrap class="text-right xCNColorEditLine">
                            <div class="field a-field a-field_a1 page__field">
                                <input id="oetPAS2PdtName_<?=$tDocNoShotCut;?>" name="oetPAS2PdtName_<?=$tDocNoShotCut;?>" class="inputs text-left field__input a-field__input xWInputEditInLine2 xWInputCanEdit" type="text" value="<?=$aValue['FTPdtName']?>" style="padding: 0px;">
                            </div>
                        </td>

                        <td nowrap><?=$aValue['FTPlcCode']?></td>

                        <!-- SET PRICE -->
                        <td nowrap class="text-right xCNColorEditLine">
                            <div class="field a-field a-field_a1 page__field">
                                <input id="oetPAS2SetPri_<?=$tDocNoShotCut;?>" name="oetPAS2SetPri_<?=$tDocNoShotCut;?>" class="inputs text-right field__input a-field__input xCNInputNumericWithDecimal xWInputEditInLine2 xWInputCanEdit" type="text" value="<?=number_format($aValue['FCIudSetPrice'],2)?>" style="padding: 0px;">
                            </div>
                        </td>

                        <!-- UNIT COUNT -->
                        <td nowrap class="text-right xCNColorEditLine">
                            <div class="field a-field a-field_a1 page__field">
                                <input id="oetPAS2UnitC1_<?=$tDocNoShotCut;?>" name="oetPAS2UnitC1_<?=$tDocNoShotCut;?>" class="inputs text-right field__input a-field__input xCNInputNumericWithDecimal xWInputEditInLine2 xWInputCanEdit" type="text" value="<?=number_format($aValue['FCIudUnitC1'],0)?>" style="padding: 0px;">
                            </div>
                        </td>

                        <td nowrap align="center"><?=$aValue['FDIudChkDate']?></td>
                        <td nowrap align="center"><?=$aValue['FTIudChkTime']?></td>
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
                        <input type="text" class="form-control xCNInputNumericWithDecimal" id="oetPASGotoPage2" placeholder="<?=$aDataTable['nCurrentPage']?>" value="">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="obtPASSubmitGotoPage2">ตกลง</button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="xWPagePdtWithOutSystem btn-toolbar pull-right">
                    <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                    <button onclick="JSxPASClickPage('1','2')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>><?=language('common/systems','tFirstPage')?></button>
                    <button onclick="JSxPASClickPage('previous','2')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                        <button onclick="JSxPASClickPage('<?php echo $i?>','2')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                    <?php } ?>
                    <?php if($nPage >= $aDataTable['nAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                    <button onclick="JSxPASClickPage('next','2')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm xWBtnNext" <?php echo $tDisabledRight ?>>
                        <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                    </button>
                    <button onclick="JSxPASClickPage('<?=$aDataTable['nAllPage']?>','2')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>><?=language('common/systems','tLastPage')?></button>
                </div>
            </div>

        </div>

    </div>
</div>

<input type="text" class="xCNHide" id="oetPASTotalPage2" value="<?=$aDataTable['nAllPage']?>">

<?php
}
?>

<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>
    $('.xWPASFilterSearch2').off('click');
    $('.xWPASFilterSearch2').on('click',function(){
        var tTextTitle  = '<?=language('common/systems', 'tLabelSearch')?>';
        var tTextFilter = '';
        $('#oetSearchItemsFilter').val($(this).data('colum'));

        switch($(this).data('colum')){
            case "FTPdtBarCode":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>';
            break;
            case "FTPdtName":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?>';
            break;
            case "FCIudSetPrice":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPrice'); ?>';
            break;
            case "FTPlcCode":
                tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPlcCode'); ?>';
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
        }


        $('#olbPASSeachItemsLabel').text(tTextTitle + ' ' + tTextFilter.replace(/<br>/gi,''));

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

    $('document').ready(function () {
        
        $('.xWInputEditInLine2').off('keydown');
        $('.xWInputEditInLine2').on('keydown',function(){
            switch(event.keyCode){
                case 13:
                    // console.log('keydown');
                    // sessionStorage.removeItem("EditInLine");
                    if(sessionStorage.getItem("EditInLine") != "2"){
                        sessionStorage.setItem("EditInLine", "1");
                        JSxPASEditInLinePdtWithOutSystem($(this));
                    }
                    break;
                case 38://up
                    $('.xWInputEditInLine2').eq($('.xWInputEditInLine2').index(this) - 1).focus();
                    break;
                case 40://down
                    $('.xWInputEditInLine2').eq($('.xWInputEditInLine2').index(this) + 1).focus();
                    break;
                case 39://right
                    $('.xWInputEditInLine2').eq($('.xWInputEditInLine2').index(this) + 1).focus();
                    break;
                case 37://left
                    $('.xWInputEditInLine2').eq($('.xWInputEditInLine2').index(this) - 1).focus();
                    break;
            }
        });

        $('.xWInputEditInLine2').off('change');
        $('.xWInputEditInLine2').on('change',function(){
            // console.log('change');
            if(sessionStorage.getItem("EditInLine") != "2"){
                sessionStorage.setItem("EditInLine", "1");
                JSxPASEditInLinePdtWithOutSystem($(this));
            }
        });
        
        $('.xWInputEditInLine2').off('focus');
        $('.xWInputEditInLine2').on('focus',function(){
            this.select();
        });

    });

    $('#oetPASGotoPage2').off('keydown');
    $('#oetPASGotoPage2').on('keydown',function(event){
        switch(event.keyCode){
            case 13:
                $('#obtPASSubmitGotoPage2').click();
            break;
        }
    });

    $('#obtPASSubmitGotoPage2').off('click');
    $('#obtPASSubmitGotoPage2').on('click',function(){
        var nPage       = parseInt($('#oetPASGotoPage2').val());
        var nMaxPage    = parseInt($('#oetPASTotalPage2').val());
        if(nPage <= nMaxPage && nPage > 0){
            JSxPASClickPage(nPage,'2');
        }else{
            $('#oetPASGotoPage2').val('');
        }
    });

    

</script>