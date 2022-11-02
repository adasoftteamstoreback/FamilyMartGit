<input type="hidden" id="odvCNTCallType" value="<?=$tCNCallType?>">
<input type="hidden" id="odvCNTParameter" value="<?=$tCNParameter?>">
<div class="containner-fluid Content">
    <div class="row-fluid">
        <div class="col-12">
           <div id="odvContentPanel"></div>
        </div>
    </div>
</div>

<?php 
    include('route.php'); 
    $tName          = $tModulename;
    $tRoute         = 'tROUTE_'.$tModulename.'_content';
    if(isset($$tRoute)){
        $tResultRoute   = $$tRoute;
    }else{
        $tResultRoute   = $tROUTE_common;
    }

    //parameter
    $tParam   = $tParameter;
?>

<!-- <script>
    $(function() {
        setTimeout(function(){
            var tNameContent    = '<?= $tResultRoute; ?>';
            var tParameter      = '<?= $tParam; ?>';
            var tCallType       = '<?= $tCallType; ?>';
            $.ajax({
                url     : tNameContent,
                data    : { 
                    tParamter : tParameter,
                    tCallType : tCallType
                },
                type    : 'POST',
                success : function(result){
                    JSxCheckSession(result);
                    $("#odvContentPanel").html(result);
                }
            });
        }, 100);

    });
</script> -->


<!-- //demo -->

<script>
    $(function() {
        //ห้ามย้อนกลับ
        history.pushState(null, null, '');
        window.addEventListener('popstate', function(event) {
            history.pushState(null, null, '');
        });
    });
</script>

<!-- 
    Create By : Napat(Jame) 02/07/2020
    Comsheet 2020-017 ป้องกันการเปิดหน้าจอเดียวกัน หลายหน้าจอ โดยใช้ cookie เป็นตัวกำหนดว่าเปิดอยู่ 
-->
<script>
$(document).ready(function(){

    // Settings
    var tCurrentScreen              = '<?php echo $tModulename;?>';
    var tGlobalCheckCloseWindow     = false;

    // เช็คว่ามี cookie ของหน้าจอที่เปิดอยู่หรือไม่ ?
    if($.cookie(tCurrentScreen) !== undefined){
        // ถ้ามีให้แจ้งเตือน และปิดหน้าเว็บไซต์ไป
        var tNameScreen = "";
        switch(tCurrentScreen){
            case 'omnPdtAdjStkChkNew':
                tNameScreen = "เอกสารตรวจนับสินค้า";
                break;
            case 'omnTurnOffSuggest':
                tNameScreen = "ปิดการแนะนำสินค้า";
                break;
            case 'omnOrderingScreen':
                tNameScreen = "สั่งซื้อ";
                break;
            case 'omnPurReqCNNew':
                tNameScreen = "ใบขอคืนสินค้า";
                break;
            case 'omnPurCNNew':
                tNameScreen = "ใบคืนสินค้า";
                break;
            default:
                tNameScreen = tCurrentScreen;
                break;
        }
        $('#odvModalSameScreen .xCNModalSameScreenBody').html('หน้าจอ'+tNameScreen+'มีการเปิดใช้งานแล้ว กรุณาปิดหน้าจอนี้');
        $('#odvModalSameScreen').modal('show');
        // window.location = '/exitkiosk';
        tGlobalCheckCloseWindow = true;
    }else{
        // ถ้าไม่มี cookie ให้สร้างใหม่
        // Last Update : Napat(Jame) 15/01/2021 ย้ายมา set cookie ตอนโหลดหน้าจอเสร็จแล้ว และเพิ่มเวลา expire 1 วัน
        // Last Update : Napat(Jame) 18/03/2021 ย้ายกลับ set cookie ที่ตำแหน่งเดิม
        var dDate   = new Date();
        var nMinute = 1440;
        dDate.setTime(dDate.getTime() + (nMinute * 60 * 1000));
        $.cookie(tCurrentScreen, true, { path: '/', expires: dDate });
        setTimeout(function(){
            var tNameContent    = '<?= $tResultRoute; ?>';
            var tParameter      = '<?= $tParam; ?>';
            var tCallType       = '<?= $tCallType; ?>';
            $.ajax({
                url     : tNameContent,
                data    : { 
                    tParamter : tParameter,
                    tCallType : tCallType
                },
                type    : 'POST',
                success : function(result){
                    JSxCheckSession(result);
                    $("#odvContentPanel").html(result);
                }
            });
        }, 100);

    }

    // เมื่อมีการโหลดหน้าจอ หรือปิดหน้าจอไป ให้ลบ cookie
    $(window).on("unload", function(e) {
        if(tGlobalCheckCloseWindow === false){
            $.removeCookie(tCurrentScreen, { path: '/' });
        }
    });

    // window.onbeforeunload = function(e) {
    //     alert("The Window is closing!");
    // };

});
</script>