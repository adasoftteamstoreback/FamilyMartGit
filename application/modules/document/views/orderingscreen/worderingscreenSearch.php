<?php
    if($aDataSearch['nStaQuery'] == 99){
        $aDataSearch['rnAllRow']            = 0;
        $aDataSearch['rnCurrentPage']       = 0;
        $aDataSearch['rnAllPage']           = 0;
    }
?>

<div class="row">
    <div class="col-lg-12 table-responsive">
        <table class="table table-striped xCNTableHead xCNTableSearch" id="otbTableOrderingScreenHD">
            <thead>
                <tr>
                    <th nowrap><?php echo language('document/orderingscreen','tODSTBDocNo'); ?></th>
                    <th nowrap><?php echo language('document/orderingscreen','tODSTBDocDate'); ?></th>
                    <th nowrap><?php echo language('document/orderingscreen','tODSTBStaPrcDoc'); ?></th>
                    <th nowrap><?php echo language('document/orderingscreen','tODSTBStaDoc'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($aDataSearch['aItems'] AS $nKey => $aValue){
            ?>
                    <tr onclick="JSxODSSelectDocument(this,'<?php echo $aValue['FTXohDocNo']; ?>');" style="cursor: pointer;">
                        <td nowrap><?php echo $aValue['FTXohDocNo']; ?></td>
                        <td nowrap><?php echo date_format($aValue['FDXohDocDate'],"d/m/Y"); ?></td>
                        <td nowrap><?php echo language('document/orderingscreen','tODSTBStatusPrcDoc'.$aValue['FTXohStaPrcDoc']); ?></td>
                        <td nowrap><?php echo language('document/orderingscreen','tODSTBStatusDoc'.$aValue['FTXohStaPrcDoc']); ?></td>
                    </tr> 
            <?php
                    
                }

                if(count($aDataSearch['aItems']) <= 0){
            ?>
                <tr>
                        <th nowrap colspan="16" class="text-center"><?php echo language('document/orderingscreen','tODSTBSearchNotFound'); ?></th>
                    </tr>
            <?php
                }
            ?>
            </tbody>
        </table>

    </div>
</div>

<div class="row" style="margin-top:15px;" >
    <!-- เปลี่ยน -->
    <div class="col-md-6">
        <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aDataSearch['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aDataSearch['rnCurrentPage']?> / <?=$aDataSearch['rnAllPage']?></p>
    </div>
    <!-- เปลี่ยน -->
    <?php if($aDataSearch['nStaQuery'] == 1){ ?>
        <div class="col-md-6">
            <div class="xWPageOrderingScreenSearch btn-toolbar pull-right"> <!-- เปลี่ยนชื่อ Class เป็นของเรื่องนั้นๆ --> 
                <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                <button type="button" onclick="JSxODSClickPageSearch('previous')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                </button>
                <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataSearch['rnAllPage'],$nPage+2)); $i++){?> <!-- เปลี่ยนชื่อ Parameter Loop เป็นของเรื่องนั้นๆ --> 
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
                    <button onclick="JSxODSClickPageSearch('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                <?php } ?>
                <?php if($nPage >= $aDataSearch['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                <button type="button" onclick="JSxODSClickPageSearch('next')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                    <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                </button>
            </div>
        </div>
    <?php } ?>
</div>