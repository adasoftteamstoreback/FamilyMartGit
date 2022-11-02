<div class="list-group">
    <?php
        if(!isset($aItems)){
            echo "<p>ไม่พบข้อมูลรหัสสาขาสำนักงานใหญ่</p>";
        }else{
            if(count($aItems) > 0){
                foreach($aItems AS $nKey => $tValue){
    ?>
                    <a href="#" class="list-group-item list-group-item-action"><?php echo $tValue['FTXohRefPODocNo'] . " (" . $tValue['FTSplCode'] . ":" . $tValue['FTVatCode'] . ")"; ?></a>
    <?php
                }
            }
        }
    ?>
</div>