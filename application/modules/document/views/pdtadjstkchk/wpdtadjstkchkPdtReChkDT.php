<div class="xWPASTableProduct table-responsive row xWDataTableMargin table-scroll" style="overflow-y:hidden">
    <table class="table table-striped xCNTableHead xWTableOrdScn xCNTableResize" id="otbPASTableProduct">
        <thead>
            <tr>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTIudBarCode"><?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTPdtName"><?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTGon1"><?=language('document/pdtadjstkchk', 'tPASTableColumGon1'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCGon1Qty"><?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTGon2"><?=language('document/pdtadjstkchk', 'tPASTableColumGon2'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCGon2Qty"><?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTGon3"><?=language('document/pdtadjstkchk', 'tPASTableColumGon3'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCGon3Qty"><?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTGon4"><?=language('document/pdtadjstkchk', 'tPASTableColumGon4'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCGon4Qty"><?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTGon5"><?=language('document/pdtadjstkchk', 'tPASTableColumGon5'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCGon5Qty"><?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCIudTotalQty"><?=language('document/pdtadjstkchk', 'tPASTableColumTotalQty'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCIudNewQty" width="85"><?=language('document/pdtadjstkchk', 'tPASTableColumNewQty'); ?></th>
            <tr>
        </thead>
        <tbody>
        <?php
            if( isset($nStaQuery) == 1 ){
                foreach($aItems AS $tKey => $tValue){
        ?>
                    <tr class="xWPASProductSeq<?=$tValue['RowID']?> xWPASDataPdtList xCNTableTrClickActive" data-seq="<?=$tValue['RowID']?>" data-realseqno="<?=$tValue['FNIudSeqNo']?>" data-stkcode="<?=$tValue['FTIudStkCode']?>" data-pdtname="<?=$tValue['FTPdtName']?>">
                        <td nowrap><?=$tValue['FTIudBarCode']?></td>
                        <td nowrap><?=$tValue['FTPdtName']?></td>

                        <td nowrap><?=$tValue['FTGon1']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCGon1Qty']?></td>
                        <td nowrap><?=$tValue['FTGon2']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCGon2Qty']?></td>
                        <td nowrap><?=$tValue['FTGon3']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCGon3Qty']?></td>
                        <td nowrap><?=$tValue['FTGon4']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCGon4Qty']?></td>
                        <td nowrap><?=$tValue['FTGon5']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCGon5Qty']?></td>
                        <td nowrap class="text-right"><?=$tValue['FCIudTotalQty']?></td>

                        <td nowrap class="text-right xCNColorEditLine">
                            <div class="field a-field a-field_a1 page__field">
                                <input id="oetPASIudNewQty<?=$tValue['RowID']?>" name="oetPASIudNewQty<?=$tValue['RowID']?>" class="inputs field__input a-field__input xCNInputNumericWithDecimal text-right xWInputEditInLine xWInputCanEdit" type="text" value="<?=$tValue['FCIudNewQty']?>" maxlength="4" style="padding: 0px;">
                            </div>
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
    </table>
</div>



<?php if($nStaQuery == 1){ ?> 
    <div class="row" style="margin-top:15px;">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <p class="ospTextpagination" style="margin-top:5px;"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($nAllRow)?> <?=language('common/systems','tRecord')?> <?=language('common/systems','tCurrentPage')?> <?=number_format($nCurrentPage)?> / <?=number_format($nAllPage)?></p>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <div class="row">

                <div class="col-sm-4">
                    <div class="form-group form-horizontal">
                        <label class="col-sm-5 control-label" style="padding-left:0px;">ไปยังหน้า</label>
                        <div class="input-group col-sm-7">
                            <input type="text" class="form-control xCNInputNumericWithDecimal" id="oetPASGotoPage" placeholder="<?=$nCurrentPage?>" value="">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" id="obtPASSubmitGotoPage">ตกลง</button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="xWPagePdtReChkDT btn-toolbar pull-right">
                        <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                        <button onclick="JSxPASClickPage('1','5')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>><?=language('common/systems','tFirstPage')?></button>
                        <button onclick="JSxPASClickPage('previous','5')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                            <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                        </button>
                        <?php for($i=max($nPage-2, 1); $i<=max(0, min($nAllPage,$nPage+2)); $i++){?>
                            <?php 
                                if($nPage == $i){ 
                                    $tActive        = 'active'; 
                                    $tDisPageNumber = 'disabled';
                                }else{ 
                                    $tActive        = '';
                                    $tDisPageNumber = '';
                                }
                            ?>
                            <button onclick="JSxPASClickPage('<?php echo $i?>','5')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                        <?php } ?>
                        <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                        <button onclick="JSxPASClickPage('next','5')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm xWBtnNext" <?php echo $tDisabledRight ?>>
                            <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                        </button>
                        <button onclick="JSxPASClickPage('<?=$nAllPage?>','5')" type="button" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>><?=language('common/systems','tLastPage')?></button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <input type="text" class="xCNHide" id="oetPASTotalPage" value="<?=$nAllPage?>">
    <input type="text" class="xCNHide" id="oetPASCurrentPage" name="oetPASCurrentPage" value="<?=$nCurrentPage?>">
<?php } ?>


<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>
    $('document').ready(function () {

        var tTextTitle  = '<?=language('common/systems', 'tLabelSearch')?>';
        $('#oetSearchItemsFilter').val('FTIudBarCode');
        $('#olbPASSeachItemsLabel').text(tTextTitle + ' ' + '<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>'.replace(/<br>/gi,''));

        $('.xWPASFilterSearch').off('click');
        $('.xWPASFilterSearch').on('click',function(){
            
            var tTextFilter = '';
            $('#oetSearchItemsFilter').val($(this).data('column'));
    
            switch($(this).data('column')){
                case "FTIudBarCode":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBBarCode'); ?>';
                break;
                case "FTPdtName":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPdtName'); ?>';
                break;
                case "FTGon1":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGon1'); ?>';
                break;
                case "FCGon1Qty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?>';
                break;
                case "FTGon2":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGon2'); ?>';
                break;
                case "FCGon2Qty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?>';
                break;
                case "FTGon3":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGon3'); ?>';
                break;
                case "FCGon3Qty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?>';
                break;
                case "FTGon4":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGon4'); ?>';
                break;
                case "FCGon4Qty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?>';
                break;
                case "FTGon5":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGon5'); ?>';
                break;
                case "FCGon5Qty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumGonQty'); ?>';
                break;
                case "FCIudTotalQty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumTotalQty'); ?>';
                break;
                case "FCIudNewQty":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTableColumNewQty'); ?>';
                break;
            }

            $('#olbPASSeachItemsLabel').text(tTextTitle + ' ' + tTextFilter.replace(/<br>/gi,''));

        });

        $('.xWInputEditInLine').off('keydown');
        $('.xWInputEditInLine').on('keydown',function(){
            switch(event.keyCode){
                case 13:
                    if(sessionStorage.getItem("EditInLine") != "2"){
                        sessionStorage.setItem("EditInLine", "1");
                        JSxPASPdtReChkDTEditInLine($(this),1);
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

        $('.xWInputEditInLine').off('change');
        $('.xWInputEditInLine').on('change',function(){
            if(sessionStorage.getItem("EditInLine") != "2"){
                sessionStorage.setItem("EditInLine", "1");
                JSxPASPdtReChkDTEditInLine($(this),1);
            }
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

        $('#obtPASSubmitGotoPage').off('click');
        $('#obtPASSubmitGotoPage').on('click',function(){
            var nPage       = parseInt($('#oetPASGotoPage').val());
            var nMaxPage    = parseInt($('#oetPASTotalPage').val());

            if(nPage <= nMaxPage && nPage > 0){
                JSxPASClickPage(nPage,'5');
            }else{
                $('#oetPASGotoPage').val('');
            }
        });

    });
</script>