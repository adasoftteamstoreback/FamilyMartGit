<link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/localcss/input.css?v=<?php echo date("dmyhis"); ?>">
<style> .form-control[disabled], fieldset[disabled] .form-control{ cursor: auto; } </style> 


<?php 
if($tPathLogoimage == 'notfound'){
    $tPathImage = 'application/modules/common/assets/images/logo/logo.png';
    $tSrcImage  = '<img class="ImageLogoHeaderBar" src="'.$tPathImage.'">';
}else{
    if($tDevelopmentType == 'Dev'){
        $tPathImage = 'application/modules/common/assets/images/logo/logo.png';
        $tSrcImage  = '<img class="ImageLogoHeaderBar" src="'.$tPathImage.'">';
    }else{
        $tPathImage = $tPathLogoimage.'.'.'png';
        $tSrcImage = '<img class="ImageLogoHeaderBar" src="data:image/png;base64,' . base64_encode(file_get_contents("$tPathImage")) .'" >';
    }
}
?>

<div class="containner-fluid HeaderBar">
    <div class="row-fluid">
        <div class="col-lg-6 col-sm-6 col-xs-4" style="height:100%;">
            <?=$tSrcImage?>
            <!-- <div id="odv001" style="width:150px; height:30px; background:red; display:inline;"> page INVOICE</div>

            <div id="odv002" style="width:150px; height:30px; background:green; display:inline;"> page PURCHASE </div>

            <div id="odv003" style="width:150px; height:30px; background:yellow; display:inline;"> page COMMON </div>
        
            <div id="odv004" style="width:150px; height:30px; background:blue; display:inline;"> sesstion destroy </div> -->

        </div>
    
        <?php if($tUseraccount != 'emptydata'){ ?>
            <?php include('application/config/database.php'); ?>
            <div class="col-lg-6 col-sm-6 col-xs-8" style="height:100%;">
                <p class='ospusernameHeader'>User : <?=$tUseraccount[0]['FTUsrName']?> ( DB : <?=tDBName?> )</p>
            </div>
        <?php } ?>

    </div>
</div>