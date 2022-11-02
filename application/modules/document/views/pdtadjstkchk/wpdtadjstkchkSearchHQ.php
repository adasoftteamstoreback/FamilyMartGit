<?php if($tType == '1'): ?>

<div class="col-lg-12" style="padding-left: 0px;">
    <label>ส่วนเอกสาร</label>
</div>

<div class="col-lg-5" style="padding-left: 0px;">
    <div class="form-group">
        <div class="input-group">
            <input class="form-control oetTextFilter xWSearchHQ xCNInputWithoutSingleQuote" type="text" value="" autocomplete="off" placeholder="<?= language('common/systems','tLabelInputSearch')?>">
            <span class="input-group-btn">
                <button id="obtPASSearchHQ" class="btn xCNBtnSearch" type="button">
                    <img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png">
                </button>
            </span>
        </div>
    </div>
</div>

    <table id="otbTableSearchHQ" class="table table-striped xCNTableHead xWPASSTableearch">
        <thead>
            <tr>
                <th style="width:20%; text-align: left;">เลขที่เอกสาร</th>
                <th style="width:80%; text-align: left;">วันที่เอกสาร</th>
            </tr>
        </thead>
        <tbody>
            <?php if($nStaQuery == 1 && isset($aItems)): ?>
                <?php foreach($aItems AS $nKey => $aItem): ?>
                <tr class="xWSchHQSeleted <?php if($nKey==0){ echo "xWSchHQSelected"; }?>" data-pdtcylcntno="<?=$aItem['FTPdtCylCntNo']?>" style="cursor:pointer;">
                    <td><?=$aItem['FTPdtCylCntNo']?></td>
                    <td><?=date_format($aItem['FDPdtStkChkDate'],'Y-m-d');?></td>
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
            <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($nAllRow,0);?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=number_format($nCurrentPage,0);?> / <?=number_format($nAllPage,0)?></p>
        </div>
        <div class="col-md-6">
            <div class="xWPageSearchHQ btn-toolbar pull-right">
                <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                <button type="button" onclick="JSxPASSearchHQClickPage('previous','HQ')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                    <button onclick="JSxPASSearchHQClickPage('<?php echo $i?>','HQ')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                <?php } ?>
                <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                <button type="button" onclick="JSxPASSearchHQClickPage('next','HQ')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                    <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<input type="text" class="xCNHide" id="oetPASCurrentPageSearchHQ" name="oetPASCurrentPageSearchHQ" value="1">

<?php else: ?>
<label>ส่วนรายละเอียด</label>
<div style="height:199px;">
    <table id="otbTableSearchHQList" class="table table-striped xCNTableHead xWPASSTableearch">
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
                <tr class="xWSchHQListSeleted" style="cursor:pointer;">
                    <td><?=$aItem['FTPdtCode']?></td>
                    <td><?=$aItem['FTPdtBarCode']?></td>
                    <td><?=$aItem['FTPdtName']?></td>
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
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=number_format($nAllRow,0)?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=number_format($nCurrentPage,0);?> / <?=number_format($nAllPage,0);?></p>
    </div>
    <div class="col-md-6">
        <div class="xWPageSearchHQList btn-toolbar pull-right">
            <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
            <button type="button" onclick="JSxPASSearchHQClickPage('previous','HQList')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
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
                <button onclick="JSxPASSearchHQClickPage('<?php echo $i?>','HQList')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
            <?php } ?>
            <?php if($nPage >= $nAllPage){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button type="button" onclick="JSxPASSearchHQClickPage('next','HQList')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
<?php endif; ?>
</div>
<input type="text" class="xCNHide" id="oetPASCurrentPageSearchHQList" name="oetPASCurrentPageSearchHQList" value="1">

<?php endif; ?>

<script>
$('.xWSchHQSeleted').off('click');
$('.xWSchHQSeleted').on('click',function(){
    $('#otbTableSearchHQ').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
    JSxPASCallSearchHQList($(this).data('pdtcylcntno'));
});

$('.xWSchHQListSeleted').off('click');
$('.xWSchHQListSeleted').on('click',function(){
    $('#otbTableSearchHQList').find('.xWSchHQSelected').removeClass('xWSchHQSelected');
    $(this).addClass('xWSchHQSelected');
});

$('#obtPASSearchHQ').off('click');
$('#obtPASSearchHQ').on('click',function(){
    JSxPASCallSearchHQ($('.xWSearchHQ').val());
});

$('.xWSearchHQ').off('keydown');
$('.xWSearchHQ').on('keydown',function(){
    if(event.keyCode == 13){
        JSxPASCallSearchHQ($('.xWSearchHQ').val());
    }
});
</script>