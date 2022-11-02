<style>
    #otbTableListSearch{
        color           : #1D2530 !important;
        font-size       : 14px !important;
        border-top      : 1px solid #dddddd;

    }
</style>  

 <!--Table-->
 <div class="row">
    <input type="hidden" id="ohdDocumentSearch" name="ohdDocumentSearch" >
    <div class="col-lg-12 table-responsive">
        <table class="table table-striped xCNTableHead xCNTableSearch" id="otbTableListSearch" style="width:100%">
            <thead>
                <tr>
                    <th style="width:20%; text-align: center;"><?=language('document/turnoffsuggestorder', 'tModalDocumentno')?> </th>
                    <th style="width:25%; text-align: center;"><?=language('document/turnoffsuggestorder', 'tModalDate')?></th>
                    <th style="width:25%; text-align: center"><?=language('document/turnoffsuggestorder', 'tModalStatusApprove')?></th>
                    <th style="width:25%; text-align: center"><?=language('document/turnoffsuggestorder', 'tModalStatusDocument')?></th>
                </tr>
            </thead>
            <tbody>
                <div>  
                    <?php if($aDataList['rtCode'] == 1){ ?>
                        <?php foreach($aDataList['raItems'] AS $key=>$aValue){  ?>
                            <?php 

                             //สถานะเอกสาร
                             if($aValue['FTXthStaDoc'] == 1){
                                $tFlagStaDocument   = 1;
                                $tNameStaDocument   = language('document/turnoffsuggestorder', 'tTSOSave');
                            }else if($aValue['FTXthStaDoc'] == 2){
                                $tFlagStaDocument   = 2;
                                $tNameStaDocument   = 'ไม่สมบูรณ์';
                            }else if($aValue['FTXthStaDoc'] == 3){
                                $tFlagStaDocument   = 3;
                                $tNameStaDocument   = language('document/turnoffsuggestorder', 'tTSOCancel');
                            }else{
                                $tFlagStaDocument   = 1;
                                $tNameStaDocument   = 'ยังไม่สมบูรณ์';
                            }

                            //สถานะอนุมัติ
                            if($aValue['FTXthStaPrcDoc'] == 1){
                                $tNameProcess   = language('document/turnoffsuggestorder', 'tTSOTextApprove');
                                $tImageProcess  =  $tBase_url."application/modules/common/assets/images/icons/ApproveIcon.png";
                            }else{
                                if($tFlagStaDocument == 3){
                                    $tNameProcess   = language('document/turnoffsuggestorder', 'tTSOTextCancleDocument');
                                    $tImageProcess  =  $tBase_url."application/modules/common/assets/images/icons/NoneApproveIcon.png";
                                }else{
                                    $tNameProcess   = language('document/turnoffsuggestorder', 'tTSOTextNoneApprove');
                                    $tImageProcess  =  $tBase_url."application/modules/common/assets/images/icons/WarningIcon.png";
                                }
                            }
                            
                            ?>

                            <tr id="otrDocumentSuggestorder<?=$aValue['FTXthDocNo'];?>" onclick="JSxSelectDocument(this,'<?=$aValue['FTXthDocNo'];?>')" style="cursor: pointer;">    
                                <td nowrap class="text-left"><?=$aValue['FTXthDocNo']?></td>
                                <td nowrap class="text-left"><?=$aValue['FDDateIns']?> <?=$aValue['FTXthDocTime']?></td>
                                <td nowrap class="text-left">
                                    <img class="oimImageapprove" src="<?=$tImageProcess?>">
                                    <?= $tNameProcess ?>
                                </td>
                                <td nowrap class="text-left"><?= $tNameStaDocument ?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr>
                            <td nowrap colspan="99" style="text-align: center;"><?= language('common/systems','tSYSDatanotfound')?></td>
                        </tr>
                    <?php } ?>
                </div>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
    </div>
    <?php if($aDataList['rtCode'] == 1){ ?>
        <div class="col-md-6">
            <div class="xWPageListSearch btn-toolbar pull-right"> <!-- เปลี่ยนชื่อ Class เป็นของเรื่องนั้นๆ --> 
                <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                <button type="button" onclick="JSvClickPageList('<?=$ptNameroute?>','previous')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                </button>
                <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?> <!-- เปลี่ยนชื่อ Parameter Loop เป็นของเรื่องนั้นๆ --> 
                    <?php 
                        if($nPage == $i){ 
                            $tActive        = 'active'; 
                            $tDisPageNumber = 'disabled';
                        }else{ 
                            $tActive        = '';
                            $tDisPageNumber = '';
                        }
                    ?>
                    <button onclick="JSvClickPageList('<?=$ptNameroute?>','<?=$i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                <?php } ?>
                <?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                <button type="button" onclick="JSvClickPageList('<?=$ptNameroute?>','next')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                </button>
            </div>
        </div>
    <?php } ?>
</div>
<!--Table-->