<?php if($tType == '1'): ?>

<div class="col-lg-12" style="padding-left: 0px;">
    <label>ส่วนเอกสาร</label>
</div>

<div class="col-lg-5" style="padding-left: 0px;">
    <div class="form-group">
        <div class="input-group">
            <input class="form-control oetTextFilter xWPASSearch xCNInputWithoutSingleQuote" type="text" value="" autocomplete="off" placeholder="<?= language('common/systems','tLabelInputSearch')?>">
            <span class="input-group-btn">
                <button id="obtPASSearchDoc" class="btn xCNBtnSearch" type="button">
                    <img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
                </button>
            </span>
        </div>
    </div>
</div>

<table id="otbTableSearch" class="table table-striped xCNTableHead xWPASSTableearch">
    <thead>
        <tr>
            <th width="20%">เลขที่เอกสาร</th>
            <th width="15%" style="text-align:center;">วันที่เอกสาร</th>
            <th width="15%" style="text-align:center;">ปรับยอดสต๊อก</th>
            <th width="50%"></th>
        </tr>
    </thead>
    <tbody>
        <?php if($nStaQuery == 1 && isset($aItems)): ?>
            <?php foreach($aItems AS $nKey => $aItem): ?>
            <tr class="xWSchHDSeleted <?php if($nKey==0){ echo "xWSchHQSelected"; }?>" data-docno="<?=$aItem['FTIuhDocNo']?>" style="cursor:pointer;">
                <td><?=$aItem['FTIuhDocNo']?></td>
                <td align="center"><?=date_format($aItem['FDIuhDocDate'],'Y-m-d');?></td>
                <td align="center"><input class="form-check-input" type="checkbox" id="ocbPASAdjType" name="ocbPASAdjType" <?php if($aItem['FTIuhAdjType'] == '1'){ echo "checked"; } ?> disabled="disabled"></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td class="text-center" colspan="99">ไม่พบรายการ</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>


<div class="row" style="margin-top:15px;">
<?php if($nStaQuery == 1): ?>  
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$nAllRow?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$nCurrentPage?> / <?=$nAllPage?></p>
    </div>
    <div class="col-md-6">
        <div class="xWPageSearchHD btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button type="button" onclick="JSxPASSearchClickPage('previous','HD')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                <button onclick="JSxPASSearchClickPage('<?php echo $i?>','HD')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button type="button" onclick="JSxPASSearchClickPage('next','HD')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
<?php endif; ?>
</div>
<input type="text" class="xCNHide" id="oetPASCurrentPageSearchHD" name="oetPASCurrentPageSearchHD" value="1">

<?php else: ?>
<label>ส่วนรายละเอียด</label>
<table id="otbTableSearchDT" class="table table-striped xCNTableHead xWPASSTableearch">
    <thead>
        <tr>
            <th style="width:15%; text-align: left;">รหัสสินค้า</th>
            <th style="width:15%; text-align: left;">บาร์โค๊ด</th>
            <th style="width:70%; text-align: left;">ชื่อสินค้า</th>
        </tr>
    </thead>
    <tbody>
        <?php if($nStaQuery == 1 && isset($aItems)): ?>
            <?php foreach($aItems as $aItem): ?>
            <tr class="xWSchDTSeleted" style="cursor:pointer;">
                <td><?=$aItem['FTPdtCode']?></td>
                <td><?=$aItem['FTIudBarCode']?></td>
                <td><?=$aItem['FTPdtName']?></td>
            <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td class="text-center" colspan="99">ไม่พบรายการ</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>


<div class="row" style="margin-top:15px;">
<?php if($nStaQuery == 1): ?>  
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$nAllRow?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$nCurrentPage?> / <?=$nAllPage?></p>
    </div>
    <div class="col-md-6">
        <div class="xWPageSearchDT btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button type="button" onclick="JSxPASSearchClickPage('previous','DT')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                <button onclick="JSxPASSearchClickPage('<?php echo $i?>','DT')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button type="button" onclick="JSxPASSearchClickPage('next','DT')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
<?php endif; ?>
</div>
<input type="text" class="xCNHide" id="oetPASCurrentPageSearchDT" name="oetPASCurrentPageSearchDT" value="1">

<?php endif; ?>

<script>
$('.xWSchHDSeleted').off('click');
$('.xWSchHDSeleted').on('click',function(){
    $('#otbTableSearch').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
    JSxPASCallSearchDT($(this).data('docno'),1);
});

$('.xWSchDTSeleted').off('click');
$('.xWSchDTSeleted').on('click',function(){
    $('#otbTableSearchDT').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
});

$('#obtPASSearchDoc').off('click');
$('#obtPASSearchDoc').on('click',function(){
    JSxPASCallSearchHD(1,$('.xWPASSearch').val());
});

$('.xWPASSearch').off('keydown');
$('.xWPASSearch').on('keydown',function(){
    if(event.keyCode == 13){
        JSxPASCallSearchHD(1,$('.xWPASSearch').val());
    }
});
</script>