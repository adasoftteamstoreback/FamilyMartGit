<!--Table-->
<div class="row">
    <div class="col-lg-12 table-responsive">
        <table class="table table-striped xCNTableHead" id="otbTableSupplier" style="width:100%">
            <thead>
				<tr>
					<th style="width:20%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupCodeType')?> </th>
					<th style="width:80%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupNameType')?></th>
				</tr>
            </thead>
            <tbody>
                <div>  
                    <?php if($aDataList['rtCode'] == 1){ ?>
                        <?php foreach($aDataList['raItems'] AS $key=>$aValue){  ?>
                            <tr onclick="JSxPURSelectSUP(this,'<?=$aValue['FTSplCode'];?>')" style="cursor: pointer;">    
                                <td nowrap class="text-left"><?=$aValue['FTSplCode']?></td>
                                <td nowrap class="text-left"><?=$aValue['FTSplName']?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr class="otrNodataSupplier">
                            <td nowrap colspan="99" style="text-align: center;"><?= language('common/systems','tSYSDatanotfound')?></td>
                        </tr>
                    <?php } ?>
                </div>
            </tbody>
        </table>
		<input type="hidden" id="ohdPURValueSupplier">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
    </div>
    <?php if($aDataList['rtCode'] == 1){ ?>
        <div class="col-md-6">
            <div class="xWPageListDataSearch btn-toolbar pull-right"> 
                <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                <button type="button" onclick="JSvClickPageListSup('previous')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                    <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                </button>
                <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?> 
                    <?php 
                        if($nPage == $i){ 
                            $tActive        = 'active'; 
                            $tDisPageNumber = 'disabled';
                        }else{ 
                            $tActive        = '';
                            $tDisPageNumber = '';
                        }
                    ?>
                    <button onclick="JSvClickPageListSup('<?=$i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                <?php } ?>
                <?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                <button type="button" onclick="JSvClickPageListSup('next')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                </button>
            </div>
        </div>
    <?php } ?>
</div>
<!--Table-->

<script>
	//click page
	function JSvClickPageListSup(ptPage){
		var nPageCurrent = '';
		var nPageNew;
		switch (ptPage) {
			case 'next': //กดปุ่ม Next
				$('.xWBtnNext').addClass('disabled');
				nPageOld = $('.xWPageListDataSearch .active').text(); // Get เลขก่อนหน้า
				nPageNew = parseInt(nPageOld, 10) + 1; // +1 จำนวน
				nPageCurrent = nPageNew;
				break;
			case 'previous': //กดปุ่ม Previous
				nPageOld = $('.xWPageListDataSearch .active').text(); // Get เลขก่อนหน้า
				nPageNew = parseInt(nPageOld, 10) - 1; // -1 จำนวน
				nPageCurrent = nPageNew;
				break;
			default:
				nPageCurrent = ptPage;
		}
		JSxGetSupplier('','<?=$nSupCode?>',nPageCurrent,'page');
	}

	//Select Supplier
	function JSxPURSelectSUP(elem,pnCode){
		$('#otbTableSupplier tbody tr').removeClass('PurtrRoundBranchClick');
		$(elem).addClass('PurtrRoundBranchClick');
		$('#ohdPURValueSupplier').val(pnCode);
	}
</script>