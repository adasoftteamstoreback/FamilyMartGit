<div class="xWPASTableProduct table-responsive row xWDataTableMargin table-scroll" style="overflow-y:hidden">
    <table class="table table-striped xCNTableHead xWTableOrdScn xCNTableResize" id="otbPASTableProduct">
        <thead>
            <tr>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FNIudSeqNo"><?=language('document/pdtadjstkchk', 'tPASTBNo'); ?></th>
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FTIudStkCode"><?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?></th>
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
                <th class="xCNTh2Line xWPASFilterSearch" nowrap data-column="FCIudNewQty"><?=language('document/pdtadjstkchk', 'tPASTableColumNewQty'); ?></th>
            <tr>
        </thead>
        <tbody>
        <?php
            if( isset($nStaQuery) == 1 ){
                foreach($aItems AS $tKey => $tValue){
        ?>
                    <tr class="xWPASProductSeq<?=$tValue['FNIudSeqNo']?> xWPASDataPdtList xCNTableTrClickActive" data-seq="<?=$tValue['FNIudSeqNo']?>" data-stkcode="<?=$tValue['FTIudStkCode']?>" data-pdtname="<?=$tValue['FTPdtName']?>">
                        <td nowrap><?=$tValue['FNNewSeq']?></td>
                        <td nowrap><?=$tValue['FTIudStkCode']?></td>
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
                                <input id="oetPASIudNewQty<?=$tValue['FNIudSeqNo']?>" name="oetPASIudNewQty<?=$tValue['FNIudSeqNo']?>" class="inputs field__input a-field__input xCNInputNumericWithDecimal text-right xWInputEditInLine xWInputCanEdit" type="text" value="<?=$tValue['FCIudNewQty']?>" style="padding: 0px;">
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
<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>
    $('document').ready(function () {

        $('.xWPASFilterSearch').off('click');
        $('.xWPASFilterSearch').on('click',function(){
            var tTextTitle  = '<?=language('common/systems', 'tLabelSearch')?>';
            var tTextFilter = '';
            $('#oetSearchItemsFilter').val($(this).data('column'));
    
            switch($(this).data('column')){
                case "FNIudSeqNo":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBNo'); ?>';
                break;
                case "FTIudStkCode":
                    tTextFilter = '<?=language('document/pdtadjstkchk', 'tPASTBPdtStkCode'); ?>';
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

    });
</script>