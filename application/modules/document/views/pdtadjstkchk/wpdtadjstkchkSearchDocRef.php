<?php if($tType == '1'): ?>
<div class="col-lg-12" style="padding-left: 0px;">
    <label><?=language('document/pdtadjstkchk', 'tPASModalSearchHeader'); ?></label>
</div>

<div class="col-lg-5" style="padding-left: 0px;">
    <div class="form-group">
        <div class="input-group">
            <input class="form-control oetTextFilter xWSearchDocRef xCNInputWithoutSingleQuote" type="text" value="" autocomplete="off" placeholder="<?= language('common/systems','tLabelInputSearch')?>">
            <span class="input-group-btn">
                <button id="obtPASSearchDocRef" class="btn xCNBtnSearch" type="button">
                    <img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
                </button>
            </span>
        </div>
    </div>
</div>

<div style='overflow-y: auto;height: 220px;width: 100%;'>
    <table id="otbTableSearchDocRef" class="table table-striped xCNTableHead xWPASSTableearch">
        <thead>
            <tr>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASModalSearchChoose'); ?></th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASTBDocNo'); ?></th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASSearchDate'); ?></th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASBrwSupplierCode'); ?></th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASBrwSupplierName'); ?></th>
                <th style="text-align: left;">
                    <?php
                        if( $tDocType == 'D/O' ){
                            echo language('document/pdtadjstkchk', 'tPASModalSearchDocPI');
                        }else{
                            echo language('document/pdtadjstkchk', 'tPASModalSearchDueDate');
                        }
                    ?>
                </th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASModalSearchInRefDoc'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if($nStaQuery == 1 && isset($aItems)): ?>
                <?php foreach($aItems AS $nKey => $aItem): ?>
                <tr class="xWSchHQSeleted <?php if($nKey==0){ echo "xWSchHQSelected"; }?>" data-docno="<?=$aItem['FTXihDocNo']?>" style="cursor:pointer;">
                    <td><input type="checkbox" id="ocbItemLists" class="xWItemLists" data-docno="<?=$aItem['FTXihDocNo'];?>" <?php if( $aItem['FNXihStaActive'] == 1 ){ echo "checked"; } ?>></td>
                    <td><?=$aItem['FTXihDocNo'];?></td>
                    <td><?=$aItem['FDXihDocDate'];?></td>
                    <td><?=$aItem['FTSplCode']?></td>
                    <td><?=$aItem['FTSplName']?></td>
                    <td>
                        <?php
                            if( $tDocType == 'D/O' ){
                                echo $aItem['FTXihRefInt'];
                            }else{
                                echo $aItem['FDXihDueDate'];
                            }
                        ?>
                    </td>
                    <td><?=$aItem['FTDocRefIn']?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td class="text-center" colspan="99">ไม่พบรายการ</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="row" style="margin-top:15px;">
    <?php if($nStaQuery == 1): ?>  
        <div class="col-md-6">
            <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($nAllRow,0);?> <?= language('common/systems','tRecord')?></p>
        </div>
        <div class="col-md-6"></div>
    <?php endif; ?>
</div>

<input type="text" class="xCNHide" id="oetPASCurrentPageSearchDO" name="oetPASCurrentPageSearchDO" value="1">
<input type="text" class="xCNHide" id="oetPASDocTypeSearchRef" name="oetPASDocTypeSearchRef" value="<?=$tDocType;?>">

<script>
// สคริปท์สำหรับ Header
// $(document).ready(function(){
//     let tGetItemLists   = localStorage.getItem("AdaLocStroage_CheckboxItemLists");
//     if( tGetItemLists !== null ){
//         let oOldItemLists   = JSON.parse(tGetItemLists);
//         $('.xWItemLists').each(function(){
//             let oElem      = $(this);
//             let oDataInEle = $(this).data();
//             $.grep(oOldItemLists, function(oItem) {
//                 if( JSON.stringify(oItem) == JSON.stringify(oDataInEle) ){
//                     $(oElem).prop("checked",true);
//                 }
//             });
//         });
//     }
// });

var tDocRef = $('#oetPASDocTypeSearchRef').val();

$('.xWSchHQSeleted').off('click');
$('.xWSchHQSeleted').on('click',function(){
    $('#otbTableSearchDocRef').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
    if( tDocRef == "D/O" ){
        JSxPASCallSearchDOList($(this).data('docno'));
    }else{
        JSxPASCallSearchAutoReceiveList($(this).data('docno'));   
    }
});

$('#obtPASSearchDocRef').off('click');
$('#obtPASSearchDocRef').on('click',function(){
    if( tDocRef == "D/O" ){
        JSxPASCallSearchDO($('.xWSearchDocRef').val());
    }else{
        JSxPASCallSearchAutoReceive($('.xWSearchDocRef').val()); 
    }
});

$('.xWSearchDocRef').off('keydown');
$('.xWSearchDocRef').on('keydown',function(){
    if(event.keyCode == 13){
        if( tDocRef == "D/O" ){
            JSxPASCallSearchDO($('.xWSearchDocRef').val());
        }else{
            JSxPASCallSearchAutoReceive($('.xWSearchDocRef').val()); 
        }
    }
});

// $('.xWItemLists').off('click');
// $('.xWItemLists').on('click',function(){
//     let oItemData = $(this).data();
//     let tGetItemLists   = localStorage.getItem("AdaLocStroage_CheckboxItemLists");
//     if( $(this).is(':checked') === true ){
//         let aSendData = [];
//         aSendData.push(oItemData);
//         if( tGetItemLists !== null ){
//             let oOldItemLists   = JSON.parse(tGetItemLists);
//             oOldItemLists.push(oItemData);
//             localStorage.setItem("AdaLocStroage_CheckboxItemLists",JSON.stringify(oOldItemLists));
//         }else{
//             localStorage.setItem("AdaLocStroage_CheckboxItemLists",JSON.stringify(aSendData));
//         }
//     }else{
//         let tGetItemLists   = localStorage.getItem("AdaLocStroage_CheckboxItemLists");
//         let oOldItemLists   = JSON.parse(tGetItemLists);
//         oOldItemLists = $.grep(oOldItemLists, function(oItem) {
//             return oItem.docno !== oItemData.docno;
//         });
//         if( oOldItemLists.length != 0 ){
//             localStorage.setItem("AdaLocStroage_CheckboxItemLists",JSON.stringify(oOldItemLists));
//         }else{
//             localStorage.removeItem("AdaLocStroage_CheckboxItemLists");
//         }
//     }
// });

</script>

<?php else: ?>
<label><?=language('document/pdtadjstkchk', 'tPASModalSearchDetails'); ?></label>
<div style="height:199px;">
    <table id="otbTableSearchDocRefList" class="table table-striped xCNTableHead xWPASSTableearch">
        <thead>
            <tr>
                <th style="width:5%; text-align: left;"><?=language('document/pdtadjstkchk', 'tPASModalSearchNo');?></th>
                <th style="width:20%; text-align: left;"><?=language('document/pdtadjstkchk', 'tPASTBBarCode');?></th>
                <th style="text-align: left;"><?=language('document/pdtadjstkchk', 'tPASTBPdtName');?></th>
                <th style="width:10%; text-align: left;"><?=language('document/pdtadjstkchk', 'tPASTBPunCode');?></th>
                <th style="width:10%; text-align: right;"><?=language('document/pdtadjstkchk', 'tPASModalSearchQtyAll');?></th>
            </tr>
        </thead>
        <tbody>
            <?php if($nStaQuery == 1 && isset($aItems)): ?>
                <?php foreach($aItems as $aItem): ?>
                <tr class="xWSchHQListSeleted" style="cursor:pointer;">
                    <td><?=$aItem['rtRowID'];?></td>
                    <td><?=$aItem['FTXidBarCode']?></td>
                    <td><?=$aItem['FTPdtName']?></td>
                    <td><?=$aItem['FTXidUnitName']?></td>
                    <td class="text-right"><?=$aItem['FCXidQtyAll']?></td>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td class="text-center" colspan="99"><?=language('document/pdtadjstkchk', 'tPASTBNotFoundData');?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="row" style="margin-top:15px;">
<?php if($nStaQuery == 1): ?>  
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($nAllRow,0)?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=number_format($nCurrentPage,0);?> / <?=number_format($nAllPage,0);?></p>
    </div>
    <div class="col-md-6">
        <div class="xWPageSearchDocRefList btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button type="button" onclick="JSxPASSearchDocRefClickPage('previous','<?=$tDocType;?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                <button onclick="JSxPASSearchDocRefClickPage('<?php echo $i?>','<?=$tDocType;?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button type="button" onclick="JSxPASSearchDocRefClickPage('next','<?=$tDocType;?>')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
<?php endif; ?>
</div>
<input type="text" class="xCNHide" id="oetPASCurrentPageSearchDocRefList" name="oetPASCurrentPageSearchDocRefList" value="1">

<script>
// สคริปท์สำหรับ Details
$('.xWSchHQListSeleted').off('click');
$('.xWSchHQListSeleted').on('click',function(){
    $('#otbTableSearchDocRefList').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
});
</script>

<?php endif; ?>