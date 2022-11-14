<?php session_start(); ?>
<?php require_once(dirname(__FILE__). '/application/config/config.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>FamilyMart</title>
    <!-- <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrap/Ada.Grid.css"> -->
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap.css?v=<?php echo date("dmyhis"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/font-awesome/css/font-awesome.min.css?v=<?php echo date("dmyhis"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap-datepicker.css?v=<?php echo date("dmyhis"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap-datetimepicker.css?v=<?php echo date("dmyhis"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap-select.css?v=<?php echo date("dmyhis"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/vendor/loading-bar/loading-bar.css?v=<?php echo date("dmyhis"); ?>">

    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/jquery/jquery.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/jquery/jquery.cookie.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/src/jCommon.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/src/jBrowseModal.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/src/jThaibath.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/moment.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap-datepicker.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap-datetimepicker.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap-timepicker.min.js?v=<?php echo date("dmyhis"); ?>"></script>

    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/bootstrapV3/bootstrap-select.js?v=<?php echo date("dmyhis"); ?>"></script>
    <script src="<?=$tBase_url?>application/modules/common/assets/vendor/loading-bar/loading-bar.js?v=<?php echo date("dmyhis"); ?>"></script>

    <!-- Time Picker -->
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/js/global/Wickedpicker/stylesheets/wickedpicker.css?v=<?php echo date("dmyhis"); ?>">
    <script src="<?=$tBase_url?>application/modules/common/assets/js/global/Wickedpicker/src/wickedpicker.js?v=<?php echo date("dmyhis"); ?>"></script>

    <!--css common-->
    <link rel="stylesheet" type="text/css" href="<?=$tBase_url?>application/modules/common/assets/css/localcss/common.css?v=<?php echo date("dmyhis"); ?>">
    <!-- <link rel="icon" href="<?=$tBase_url?>application/modules/common/assets/images/logo/favicon.png"> -->
</head>
<body>
<div class="odvLoaderprogress">
    <div id="floatingCirclesG">
        <div class="f_circleG" id="frotateG_01"></div>
        <div class="f_circleG" id="frotateG_02"></div>
        <div class="f_circleG" id="frotateG_03"></div>
        <div class="f_circleG" id="frotateG_04"></div>
        <div class="f_circleG" id="frotateG_05"></div>
        <div class="f_circleG" id="frotateG_06"></div>
        <div class="f_circleG" id="frotateG_07"></div>
        <div class="f_circleG" id="frotateG_08"></div>
    </div>        
    <span id="ospLoaderText"> Loading </span>
    <span id="ospLoaderTextSub"> PLEASE WAIT ... </span>
</div>

<?php 
    $_SESSION["FMLogin"] = 'supawat putayanan';
    if(!isset($_SESSION["FMLogin"]) || $_SESSION["FMLogin"] == null){
        header('Location: content.php?route=login');
        exit;
    }else{
        include('Content.php');
    }

    // Create By : Napat(Jame) 14/11/2022 ถ้ายังไม่มีโฟลเดอร์ logs ให้สร้างไว้
    if( !file_exists('application/logs') ){
        mkdir('application/logs', 0777, true);
    }
?>
</body>
</html>


