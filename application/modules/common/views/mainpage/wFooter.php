<!--Sort By Column-->
<input type="hidden" name="ohdNameSort" id="ohdNameSort" value="">
<input type="hidden" name="ohdTypeSort" id="ohdTypeSort" value="ASC">

<?php 
    $file = fopen($tBase_url."application/modules/".$tModules."/version/".$tFeatures."/version.txt","r");
    $aVersion = array();
    while(!feof($file)){
        $aVersion[] = fgets($file);
    }
    fclose($file);

    if(empty($aVersion[0])){
        $tTextVersion = 'Version 0.0.1';
    }else{
        $tTextVersion = $aVersion[0];
    }
?>


<div class="containner-fluid FooterBar">
    <div class="row-fluid">
        <div class="col-12" style="height:100%;">
            <span class="ospTextFooter xCNVersion"><?=$tTextVersion?></span>
        </div>
    </div>
</div>

<!--Version-->
<!--Version 0.4.1 : turnoffsuggest order แก้ตาม requrement หมดทุกข้อ พร้อม resize table-->
<!--Version 0.4.2 : turnoffsuggest order สามารถ sort by column / rabbit mq connecet ไม่ได้-->
<!--Version 0.4.3 : insert PDT ซ้ำไม่ได้ กับ Date ซ้ำ-->
<!--Version 0.4.4 : Editinline date picker ต้องไม่ทับกันใน turnoffsuggest order-->

<!--Rabbit MQ-->
<div class="modal fade" id="odvModalMQInfoMessage" data-backdrop="static" data-keyboard="true" style="overflow: hidden auto; z-index: 7000; display: none;">
    <div class="modal-dialog" role="document" style="width: 450px; height: 370px; margin: 1.75rem auto; top: 10%;">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label style="color:#000;"><i class="fa fa-info"></i> <?php echo language('common/systems', 'tModalMQTitle')?></label>
            </div>
            <div class="modal-body">
                <div class="clearfix"></div>
                <div class="text-center">
                    <div 
                        class="ldBar label-center"
                        style="width:50%;height:50%;margin:auto;color:#179bfd;font-weight:bold;font-size:25px;"
                        data-value="0"
                        data-preset="circle"
                        data-stroke="#179bfd"
                        data-stroke-trail="#179bfd"
                        id="odvIdBar">
                    </div>
                </div>
                <div id="odvModalMQReciveMessage" class="xCNMessage"></div>
            </div>
            <div class="modal-footer xCNRabbitMQBTN" style="display:none;">
                <button id="osmResent" class="btn xCNBTNActionCancel xCNResent" type="button" style="display:none; width:auto;" data-resent="0">
                    <?php echo language('common/systems', 'tModalMQBtnReprocess'); ?>
                </button>
                <button id="osmConfirmRabbit" class="btn xCNBTNActionCancel xCNCancelRabit xCNConfirmRabbit" type="button" data-dismiss="modal" style="display:none; width:100px ; background: #179bfd !important; color: #FFF !important;">
                    <?php echo language('common/systems', 'tModalMQBtnConfirm'); ?>
                </button>
                <button id="osmCloseRabbit" class="btn xCNBTNActionInsert xCNCancelRabit" type="button" data-dismiss="modal" style="display:none;"> <!-- width:100px ; background: #179bfd !important; color: #FFF !important; -->
                    <?php echo language('common/systems', 'tModalMQBtnClose'); ?>
                </button>
                <button id="osmTimeOut" class="btn xCNBTNActionCancel xCNCancelRabit" type="button" data-dismiss="modal" style="display:none; width:100px ; background: #179bfd !important; color: #FFF !important;">
                    <?php echo language('common/systems', 'tModalMQBtnConfirm'); ?>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Dialog Message -->
<div class="modal fade" id="odvModalDialogMessage" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xCNModalDialogMessageHead"></label>
			</div>
			<div class="modal-body xCNModalDialogMessageBody">
                
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel xCNCloseDialogMessage" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Dialog Message -->

<!-- Modal แจ้งเตือนหน้าจอซ้ำ -->
<div class="modal fade" id="odvModalSameScreen" data-backdrop="static" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard xCNModalSameScreenHead"></label>
			</div>
			<div class="modal-body xCNModalSameScreenBody">
                
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel xCNCloseSameScreen" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal แจ้งเตือนหน้าจอซ้ำ -->


<?php //include('application/config/rabbitmq.php'); ?>
<!-- <script src="application/libraries/rabbitmq/stomp.js"></script> -->
<?php include('application/config/database.php'); ?>

<!-- case \ หายในตัวแปร -->
<?php $tServername = base64_encode(tServername);?>

<script>
    $(document).ready(function(){
        $('.progress').hide(); 
    });

    $('#osmResent').on('click',function(){
        var nCount = $('#osmResent').attr('data-resent');
        if(nCount > 0){
            $('#osmResent').attr('data-resent','0');
        }
    });
    
    /**
    * Functionality : เข้ารหัสค่าตัวแปรที่ส่งมาพร้อมกับค่า get ใน url
    * Parameters : รูปแบบ array ค่าตัวแปรที่ส่งมาพร้อมกับค่า get ใน url
    * Creator : 29/05/2019 Pap
    * Last Modified : -
    * Return : text ค่าที่ถูกเข้ารหัสแล้ว เป็นก้อนเดียวกัน
    * Return Type : string
    */
    function JCNtEnCodeUrlParameter(paParameter){
        /*
        ตัวอย่างค่าที่ต้องส่งมา ไม่จำกัดจำนวนเอเรย์
        [
            {"Lang":"1"},
            {"ComCode":"C0001"},
            {"BranchCode":"00342"},
            {"DocCode":"TM0034219000001"}
        ]
        */
        let tResult = "";
        let tRuleCompareUpper = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        let tRuleCompareLower = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        let tEnCodeParameter = "";
        for(let i = 0;i<paParameter.length;i++){
            //========================= frist encode
            let tKey;
            if(JSON.stringify(Object.keys( paParameter[i] )).indexOf("\"")!=-1 &&
            JSON.stringify(Object.keys( paParameter[i] )).indexOf("[")!=-1 &&
            JSON.stringify(Object.keys( paParameter[i] )).indexOf("]")!=-1){
                tKey = (JSON.stringify(Object.keys( paParameter[i] )).replace("[\"", "")).replace("\"]", "");
            }else{
                tKey = JSON.stringify(Object.keys( paParameter[i] ));
            }
            let tKeyChar = tKey.split('');
            let tVldFristKey = "";
            for(let j = 0;j<tKeyChar.length;j++){
                let letters = "^[a-zA-Z]+$";
                if(tKeyChar[j].match(letters)){
                    if(tKeyChar[j]==tKeyChar[j].toUpperCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareUpper.length;z++){
                            if(tKeyChar[j]==tRuleCompareUpper[z]){
                                if((z+2)>tRuleCompareUpper.length){
                                    nDifLoop = (z+2)-tRuleCompareUpper.length;
                                }else{
                                    nDifLoop = z+2;
                                }
                                break;
                            }
                        }
                        tKeyChar[j] = tRuleCompareUpper[nDifLoop];
                    }else if(tKeyChar[j]==tKeyChar[j].toLowerCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareLower.length;z++){
                            if(tKeyChar[j]==tRuleCompareLower[z]){
                                if((z+2)>tRuleCompareLower.length){
                                    nDifLoop = (z+2)-tRuleCompareLower.length;
                                }else{
                                    nDifLoop = z+2;
                                }
                                break;
                            }
                        }
                        tKeyChar[j] = tRuleCompareLower[nDifLoop];
                    }
                }
                tVldFristKey += tKeyChar[j];
            }
            let tValue;
            if(JSON.stringify(Object.values( paParameter[i] )).indexOf("\"")!=-1 &&
            JSON.stringify(Object.values( paParameter[i] )).indexOf("[")!=-1 &&
            JSON.stringify(Object.values( paParameter[i] )).indexOf("]")!=-1){
                tValue = (JSON.stringify(Object.values( paParameter[i] )).replace("[\"", "")).replace("\"]", "");
            }else{
                tValue = JSON.stringify(Object.values( paParameter[i] ));
            }
            let tValueChar = tValue.split('');
            let tVldFristValue = "";
            for(let j = 0;j<tValueChar.length;j++){
                let letters = "^[a-zA-Z]+$";
                if(tValueChar[j].match(letters)){
                    if(tValueChar[j]==tValueChar[j].toUpperCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareUpper.length;z++){
                            if(tValueChar[j]==tRuleCompareUpper[z]){
                                if((z+2)>tRuleCompareUpper.length){
                                    nDifLoop = (z+2)-tRuleCompareUpper.length;
                                }else{
                                    nDifLoop = z+2;
                                }
                                break;
                            }
                        }
                        tValueChar[j] = tRuleCompareUpper[nDifLoop];
                    }else if(tValueChar[j]==tValueChar[j].toLowerCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareLower.length;z++){
                            if(tValueChar[j]==tRuleCompareLower[z]){
                                if((z+2)>tRuleCompareLower.length){
                                    nDifLoop = (z+2)-tRuleCompareLower.length;
                                }else{
                                    nDifLoop = z+2;
                                }
                                break;
                            }
                        }
                        tValueChar[j] = tRuleCompareLower[nDifLoop];
                    }
                }
                tVldFristValue += tValueChar[j];
            }
            //========================= end frist encode 
            //========================= secound encode
            tKey = tVldFristKey;
            tKeyChar = tKey.split('');
            let tVldSecoundKey = "";
            for(let j = 0;j<tKeyChar.length;j++){
                let letters = "^[a-zA-Z]+$";
                if(tKeyChar[j].match(letters)){
                    if(tKeyChar[j]==tKeyChar[j].toUpperCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareUpper.length;z++){
                            if(tKeyChar[j]==tRuleCompareUpper[z]){
                                if((z-5)<0){
                                    nDifLoop = (z-5)+tRuleCompareUpper.length;
                                }else{
                                    nDifLoop = z-5;
                                }
                                break;
                            }
                        }
                        tKeyChar[j] = tRuleCompareUpper[nDifLoop];
                    }else if(tKeyChar[j]==tKeyChar[j].toLowerCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareLower.length;z++){
                            if(tKeyChar[j]==tRuleCompareLower[z]){
                                if((z-5)<0){
                                    nDifLoop = (z-5)+tRuleCompareLower.length;
                                }else{
                                    nDifLoop = z-5;
                                }
                                break;
                            }
                        }
                        tKeyChar[j] = tRuleCompareLower[nDifLoop];
                    }
                }
                tVldSecoundKey += tKeyChar[j];
            }
            tValue = tVldFristValue;
            tValueChar = tValue.split('');
            let tVldSecoundValue = "";
            for(let j = 0;j<tValueChar.length;j++){
                let letters = "^[a-zA-Z]+$";
                if(tValueChar[j].match(letters)){
                    if(tValueChar[j]==tValueChar[j].toUpperCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareUpper.length;z++){
                            if(tValueChar[j]==tRuleCompareUpper[z]){
                                if((z-5)<0){
                                    nDifLoop = (z-5)+tRuleCompareUpper.length;
                                }else{
                                    nDifLoop = z-5;
                                }
                                break;
                            }
                        }
                        tValueChar[j] = tRuleCompareUpper[nDifLoop];
                    }else if(tValueChar[j]==tValueChar[j].toLowerCase()){
                        let nDifLoop = 0;
                        for(let z = 0;z<tRuleCompareLower.length;z++){
                            if(tValueChar[j]==tRuleCompareLower[z]){
                                if((z-5)<0){
                                    nDifLoop = (z-5)+tRuleCompareLower.length;
                                }else{
                                    nDifLoop = z-5;
                                }
                                break;
                            }
                        }
                        tValueChar[j] = tRuleCompareLower[nDifLoop];
                    }
                }
                tVldSecoundValue += tValueChar[j];
            }
            //========================= end secound encode
            if(i==0){
                tEnCodeParameter += tVldSecoundKey+"="+tVldSecoundValue;
            }else{
                tEnCodeParameter += "&"+tVldSecoundKey+"="+tVldSecoundValue;
            }
        }
        //==================== third encode
        return btoa(tEnCodeParameter);
        //==================== end third encode
    }


    //เซตสิทธิต่างๆ 
    var tDevelopmentType    = '<?=$tDevelopmentType?>';
    if(tDevelopmentType != 'Dev'){
        //ห้ามคลิกขวา
        $(document).on("contextmenu", function(e) { 
            e.preventDefault(); 
        }); 

        //ห้ามคลิก F5
        $(document).on('keydown keyup', function(e) {
            //ห้ามกด F12
            if(e.which === 123) {
                return false;
            }
            if(e.which === 116) {
                return false;
            }
            if(e.which === 82 && e.ctrlKey) {
                return false;
            }
        });
    }

    //เซต database เพื่อส่งไป background process
    var aDBConfig = {
        'tDBServername'     : "<?php echo $tServername; ?>",
        'tDBUsername'       : "<?php echo tUsername; ?>",
        'tDBPassword'       : "<?php echo tPassword; ?>",
        'tDBName'           : "<?php echo tDBName; ?>",
        'tDBTypeConnect'    : "<?php echo tTypeConnect; ?>"
    };

    /*

    //Call Rabbit
    function FCNxCallRabbitMQ(paParams){

        var client = Stomp.client('ws://' + aDataConfig['ptHost'] + ':15674/ws');

        client.debug = function() {
            if (window.console && console.log && console.log.apply) {
                console.log.apply(console, arguments);
            }
        };

        var on_connect = function(x) {
            if(paParams['tType'] == 'backgroundprocess'){
                var aMQParams = "{ 'ptDocNo':'" + paParams['params']['ptDocNo'] + "',";
                    aMQParams += " 'ptDocName':'" + paParams['params']['ptDocName'] + "' , ";
                    aMQParams += " 'pnBchCode':'" + '<?=$_SESSION["SesBchCode"]?>' + "' , ";
                    aMQParams += " 'ptWhoCode':'" + '<?=$_SESSION["SesUsercode"]?>' + "' , ";
                    aMQParams += " 'ptWhoIns':'" + '<?=$_SESSION["SesUsername"]?>' + "' , ";
                    aMQParams += " 'tDBServername':'" + aDBConfig['tDBServername'] + "' , ";
                    aMQParams += " 'tDBUsername':'" + aDBConfig['tDBUsername'] + "' , ";
                    aMQParams += " 'tDBPassword':'" + aDBConfig['tDBPassword'] + "' , ";
                    aMQParams += " 'tTypeRB':'" + 'INS' + "' , ";
                    aMQParams += " 'tDBName':'" + aDBConfig['tDBName'] + "' }";
                var aMQParams = btoa(unescape(encodeURIComponent(aMQParams)));
            }else if(paParams['tType'] == 'exportservice'){
                var aMQParams = '{ "ExportServiceList" : ';
                    aMQParams += paParams['params']['ptDataExport'];
                    aMQParams += '}';
            }else{
                var aMQParams = "{ 'ptDocNo':'" + paParams['params']['ptDocNo'] + "' }";
            }
            
            client.send('/queue/' + paParams['MQApprove'], {}, aMQParams); //durable: false, auto-delete: false
            
            setTimeout(function(){
                client.disconnect();
            }, 5000);

        };

        var on_error = function() {
            console.log('error Call Rabbit');
        };

        client.connect(aDataConfig['ptUser'], aDataConfig['ptPass'], on_connect, on_error, 'AdaStoreBack');

    }*/

    function SubcribeToRabbitMQ(paParams){

        if(paParams['tType'] == 'backgroundprocess'){
            var aMQParams = "{ 'ptDocNo':'" + paParams['params']['ptDocNo'] + "',";
                aMQParams += " 'ptDocName':'" + paParams['params']['ptDocName'] + "' , ";
                aMQParams += " 'pnBchCode':'" + '<?=$_SESSION["SesBchCode"]?>' + "' , ";
                aMQParams += " 'ptWhoCode':'" + '<?=$_SESSION["SesUsercode"]?>' + "' , ";
                aMQParams += " 'ptWhoIns':'" + '<?=$_SESSION["SesUsername"]?>' + "' , ";
                aMQParams += " 'tDBServername':'" + aDBConfig['tDBServername'] + "' , ";
                aMQParams += " 'tDBUsername':'" + aDBConfig['tDBUsername'] + "' , ";
                aMQParams += " 'tDBPassword':'" + aDBConfig['tDBPassword'] + "' , ";
                aMQParams += " 'tTypeRB':'" + 'INS' + "' , ";
                aMQParams += " 'tDBName':'" + aDBConfig['tDBName'] + "' }";
            var aMQParams = btoa(unescape(encodeURIComponent(aMQParams)));
        }else if(paParams['tType'] == 'exportservice'){
            var aMQParams = '{ "ExportServiceList" : ';
                aMQParams += paParams['params']['ptDataExport'];
                aMQParams += '}';
        }else{
            var aMQParams = "{ 'ptDocNo':'" + paParams['params']['ptDocNo'] + "' }";
        }

        // Open Starting Modal Process 0%
        FSxCMNSetMsgInfoMessageDialog('{"ptProgress":"0"}',paParams);

        // เปิด background process
        $.ajax({
            type    : "POST",
            url     : 'Content.php?route=common&func_method=FSxCCOMCallBGPHP',
            data    : {},
            // async   : false,
            success: function (response){
                setTimeout(function(){
                    // Open Connection
                    var bCheckTimeout   = false;
                    var client          = new WebSocket('ws://localhost:8089');
                    var nCountOnMsg     = 0;
                    client.onopen = function(e) {
                        console.log('Web Socket Opened...');
                        console.log('>>> CONNECT');

                        //Create Queue
                        console.log('>>> CREATE\nQueueName: ' + paParams['params']['ptDocNo']);
                        client.send(JSON.stringify({
                                'MSMQ_Type' : 'Create',
                                'MSMQ_Data' : {
                                    'MSMQ_QueueName'    : paParams['params']['ptDocNo']
                                }
                            })
                        );
                        
                        //Subscribe Queue
                        console.log('>>> SUBSCRIBE\nQueueName: ' + paParams['params']['ptDocNo']);
                        Subscribe = setInterval(function(){ 
                            client.send(JSON.stringify({
                                    'MSMQ_Type' : 'Subscribe',
                                    'MSMQ_Data' : {
                                        'MSMQ_QueueName'    : paParams['params']['ptDocNo']
                                    }
                                })
                            );
                        }, 500);

                        //Publish Queue
                        console.log('>>> SEND\nQueueName: ' + paParams['MQApprove'] + '\nMessage: ' + aMQParams);
                        client.send(JSON.stringify({
                                'MSMQ_Type' : 'Publish',
                                'MSMQ_Data' : {
                                    'MSMQ_QueueName'    : paParams['MQApprove'],
                                    'MSMQ_Message'      : aMQParams
                                }
                            })
                        );

                    };
                    client.onmessage = function(e) {
                        if(e.data == 'null'){
                            if((nCountOnMsg/8)%2 == 0){
                                console.log('>>> PING ' + nCountOnMsg);
                            }else if((nCountOnMsg/8)%1 == 0){
                                console.log('<<< PONG ' + nCountOnMsg);
                            }

                            //ถ้าไม่มีการส่ง message มาภายในเวลาที่กำหนด จะส่ง -1
                            if(nCountOnMsg >= 40 && bCheckTimeout == false){
                                console.log('>>> SEND\nQueueName: ' + paParams['params']['ptDocNo'] + '\nMessage: {"ptProgress":"-1","ptMessage":"WebPHP"}');
                                client.send(JSON.stringify({
                                        'MSMQ_Type' : 'Publish',
                                        'MSMQ_Data' : {
                                            'MSMQ_QueueName'    : paParams['params']['ptDocNo'],
                                            'MSMQ_Message'      : '{"ptProgress":"-1","ptMessage":"WebPHP"}'
                                        }
                                    })
                                );
                            }

                            nCountOnMsg++;
                        }else{
                            bCheckTimeout = true;
                            var aDataRecive = JSON.parse("[" + e.data + "]");
                            console.log('>>> RECEIVE\nQueueName: ' + paParams['params']['ptDocNo'] + '\nMessage: ' + e.data);
                            if(aDataRecive[0]['ptProgress'] == '99'){
                                console.log('>>> DELETE\nQueueName: ' + paParams['params']['ptDocNo']);
                                client.send(JSON.stringify({
                                        'MSMQ_Type' : 'Delete',
                                        'MSMQ_Data' : {
                                            'MSMQ_QueueName'    : paParams['params']['ptDocNo']
                                        }
                                    })
                                );
                                clearInterval(Subscribe);
                                FSxCMNSetMsgInfoMessageDialog('{"ptProgress":"100","ptMessage":"WebPHP"}',paParams);
                                client.close();
                            }else if(aDataRecive[0]['ptProgress'] == '-1'){
                                console.log('>>> DELETE\nQueueName: ' + paParams['params']['ptDocNo']);
                                client.send(JSON.stringify({
                                        'MSMQ_Type' : 'Delete',
                                        'MSMQ_Data' : {
                                            'MSMQ_QueueName'    : paParams['params']['ptDocNo']
                                        }
                                    })
                                );
                                clearInterval(Subscribe);
                                FSxCMNSetMsgInfoMessageDialog(e.data,paParams);
                                client.close();
                            }else{
                                FSxCMNSetMsgInfoMessageDialog(e.data,paParams);
                            }
                        }
                    };
                    client.onerror = function(e) {
                        console.log('>>> ERROR');
                        
                        // Rollback
                        FSxCMNSetMsgInfoMessageDialog('{"ptProgress":"-99","ptMessage":"เชื่อมต่อ MSMQ ล้มเหลว"}',paParams);

                        console.log('>>> DELETE\nQueueName: ' + paParams['params']['ptDocNo']);
                        client.send(JSON.stringify({
                                'MSMQ_Type' : 'Delete',
                                'MSMQ_Data' : {
                                    'MSMQ_QueueName'    : paParams['params']['ptDocNo']
                                }
                            })
                        );
                        clearInterval(Subscribe);

                    };
                    client.onclose = function(e) {
                        console.log('<<< DISCONNECT');
                        clearInterval(Subscribe);
                    };

                }, 300);
            }
        });

    }

    function FSxCMNSetMsgInfoMessageDialog(ptMessage,paParams){
        var aDataRecive = JSON.parse("[" + ptMessage + "]");
        $('#odvModalMQInfoMessage').modal('show');
        $('#odvIdBar').fadeIn();
        
		$('.xCNRabbitMQBTN').fadeOut();
		$('.ldBar-label').html(aDataRecive[0]['ptProgress']);
		var bar1 = new ldBar("#odvIdBar");
        bar1.set(parseFloat(aDataRecive[0]['ptProgress']));

        if(aDataRecive[0]['ptProgress'] == '-1'){

            $('#odvModalMQReciveMessage').html('');
            
            $('.xCNRabbitMQBTN').fadeIn();
            //ปุ่ม ส่งใหม่
            $('#osmResent').fadeIn();

            //ปุ่ม ตกลง
            $('#osmConfirmRabbit').fadeOut();

            //ปุ่ม สำหรับ timeout
            $('#osmTimeOut').fadeOut();
            
            //ปุ่ม ปิด
            $('#osmCloseRabbit').fadeIn();
            
            //Resent
            $('#osmResent').off('click');
            $('#osmResent').on('click',function(evt) {
                $('.xCNRabbitMQBTN').fadeOut();
                SubcribeToRabbitMQ(paParams);
            });

            // console.log(paParams);

            //ถ้ากดปิดต้อง roll back กลับไป Update staprc , staflag เหมือนเดิม
            if(paParams['tType'] == 'backgroundprocess' && (paParams['params']['ptDocName'] == 'PC' || paParams['params']['ptDocName'] == 'PDTADJSTKCHK')){
                //วิ่งเข้า background process อีกรอบแต่เข้า roll back
            }else{
                // console.log('2หน้าจอแรก roll back');
                //วิ่ง route ปกติที่ถูกส่งมา
                // $('#osmCloseRabbit').off('click');
                $('#osmCloseRabbit').on('click',function(event) {
                    // $('#osmResent').attr('data-resent','0');
                    $.ajax({
                        type    : "POST",
                        url     : paParams['params']['ptRouteOther'],
                        data    : { 'ptDocno' : paParams['params']['ptDocNo'] },
                        success: function (response){}
                    });
                });
            }

            // var nCountResent = $('#osmResent').attr('data-resent');
            // if(nCountResent == 1){
            //     $('#osmResent').attr('data-resent','0');
                $('.xCNRabbitMQBTN').hide();
                // setTimeout(function(){ 
                    $('.xCNRabbitMQBTN').fadeIn();
                // }, 15000);
            // }

            // $('#osmResent').attr('data-resent','1');

        }else if(aDataRecive[0]['ptProgress'] == '100'){
          
            setTimeout(function(){
                
                $('#odvIdBar').hide();
                $('.xCNRabbitMQBTN').fadeIn();

                //ปุ่มส่งใหม่
                $('#osmResent').hide();

                //ปุ่มตกลง
                $('#osmConfirmRabbit').fadeIn();

                //ปุ่ม สำหรับ timeout
                $('#osmTimeOut').hide();

                //ปุ่มปิด
                $('#osmCloseRabbit').hide();

                // console.log(paParams['params']['ptRouteSuccess']);

                if(paParams['params']['ptRouteSuccess'] === undefined || paParams['params']['ptRouteSuccess'] == ""){
                    $('#odvModalMQReciveMessage').html('<p>ประมวลผลเสร็จสมบูรณ์</p>');
                }else{
                    $.ajax({
                        type    : "POST",
                        url     : paParams['params']['ptRouteSuccess'],
                        data    : { 'ptDocno' : paParams['params']['ptDocNo'] },
                        success: function (tResult) {
                            $('#odvModalMQReciveMessage').html(tResult);
                        }
                    });
                }

            }, 2000);

        }else if(aDataRecive[0]['ptProgress'] == '-99'){

            if(aDataRecive[0]['ptMessage'] == ""){
                ptMesReturn = "ประมวลผลไม่สำเร็จ";
            }else{
                ptMesReturn = aDataRecive[0]['ptMessage'];
            }
            
            $('#odvModalMQReciveMessage').html('');
            $('#odvIdBar').hide();
            $('#osmResent').hide();
            $('#osmCloseRabbit').hide();
            $('#odvModalMQReciveMessage').html('<p>'+ptMesReturn+'</p>');

            $.ajax({
                type    : "POST",
                url     : paParams['params']['ptRouteOther'],
                data    : { 'ptDocno' : paParams['params']['ptDocNo'] },
                success: function (response) {
                    $('.xCNRabbitMQBTN').show();
                    //ปุ่ม สำหรับ timeout
                    $('#osmTimeOut').show();
                }
            });

        }else if(aDataRecive[0]['ptProgress'] == '89'){
            $('#odvModalMQReciveMessage').html('<p style="text-decoration:blink"><center>ระบบกำลังส่งออกข้อมูล กรุณารอสักครู่</center></p>');
        }else if(aDataRecive[0]['ptProgress'] == '1'){
            $('#odvModalMQReciveMessage').html('<p style="text-decoration:blink"><center>ระบบกำลังประมวลผลข้อมูล กรุณารอสักครู่</center></p>');
        }else if(aDataRecive[0]['ptProgress'] == '0'){
            $('#odvModalMQReciveMessage').html('<p style="text-decoration:blink"><center>ระบบกำลังประมวลผลข้อมูล กรุณารอสักครู่</center></p>');
        }
    }

</script>