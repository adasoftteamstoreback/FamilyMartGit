<?php
ini_set("memory_limit","-1");

require_once(APPPATH . 'libraries/rabbitmq/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class cMQ extends CI_Controller {
    
    public  $aMQConfig  = [];
    public  $aQname     = [];
    private $aParams    = [];
    
    public function __construct() {
        parent::__construct();
        $this->config->load('rabbitmq');
        $this->FSxInit();
    }
    
    //ค่าเริ่มต้นการทำงาน เซต Rabbit MQ
    public function FSxInit(){
        $this->aMQConfig = [
            'tHost'         => $this->config->item('mq_host'),
            'tUsername'     => $this->config->item('mq_username'),
            'tPassword'     => $this->config->item('mq_password'),
            'tPort'         => $this->config->item('mq_port'),
            'tVHost'        => $this->config->item('mq_vhost')
        ];
        $this->aQname = $this->config->item('documentname');
    }

    //Consumer รอรับข้อความ เพื่อประมวลผล
    public function FSxMQConsumer($paParams = []) {
        // echo "Connect : ".$this->aMQConfig['tHost'].":".$this->aMQConfig['tPort']." ".$this->aMQConfig['tUsername']." ".$this->aMQConfig['tPassword']." ".$this->aMQConfig['tVHost']."\n";
        echo "\n====================================== START ==========================================\n\n";

        // $this->FSxCMQWriteLog("Connect : ".$this->aMQConfig['tHost'].":".$this->aMQConfig['tPort']." ".$this->aMQConfig['tUsername']." ".$this->aMQConfig['tPassword']." ".$this->aMQConfig['tVHost']);
        $this->FSxCMQWriteLog("====================================== START ==========================================");

        // $oConnection = new AMQPStreamConnection(
        //     $this->aMQConfig['tHost'], 
        //     $this->aMQConfig['tPort'], 
        //     $this->aMQConfig['tUsername'], 
        //     $this->aMQConfig['tPassword'], 
        //     $this->aMQConfig['tVHost']
        // );
        
        // $oChannel = $oConnection->channel();
        
        //echo " [*] Waiting for messages. To exit press CTRL+C\n\n\n";
        // $oChannel->basic_qos(null, 1, null);
        
        foreach ($this->aQname as $tQname) {
            $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
            $MSMQInfo->PathName = ".\\private$\\".$tQname;
            if(strlen($MSMQInfo->Label) == 0){
                $MSMQInfo->Label = $tQname;
                $MSMQInfo->Create(false,true);
            }

            // $oChannel->queue_declare($tQname, false, true, false, false);
            // $oChannel->basic_consume($tQname, '', false, true, false, false, [$this, 'FSxCallback']);
        }

        while(true){
            foreach ($this->aQname as $tQname){
                $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
                $MSMQInfo->PathName = ".\\private$\\".$tQname;
                if(strlen($MSMQInfo->Label) > 0){
                    $MSMQQueue = $MSMQInfo->Open(1,0);
                    $MSMQMessage = $MSMQQueue->Receive(0, False, True, 0, False);
                    if(isset($MSMQMessage->body)){
                        $this->FSxCallback($MSMQMessage);
                    }
                    $MSMQQueue->Close();
                }
            }
        }
        
        // while (count($oChannel->callbacks)) {
        //     $oChannel->wait();
        // }

        // $oChannel->close();
        // $oConnection->close();
    }

    //MQ Callback คือขารับ จาก rabbit - STEP[1]
    public function FSxCallback($msg) {
        // echo ' [/] Received ', '['.$msg->body.']', "\n";
        // sleep(substr_count('['.$msg->body.']', '.'));
        // echo " [/] Done\n";

        $aJsonDecode    = base64_decode($msg->body);
        $aJson          = str_replace("'",'"',$aJsonDecode);
        $aData          = json_decode($aJson ,true); 

        //เซตฐานข้อมูล
        $aWriteDB = base64_decode($aData['tDBServername']) . "\n";
        $aWriteDB .= $aData['tDBUsername'] . "\n";
        $aWriteDB .= $aData['tDBPassword'] . "\n";
        $aWriteDB .= $aData['tDBName'] . "\n";
        $tFileName = '../BackgroundProcess/application/config/DBWrite.txt';
        $fp = fopen($tFileName , 'w') or die("Die");
        fwrite($fp,$aWriteDB);
        fclose($fp);

        if($aData['tTypeRB'] == 'INS'){  //รับคิวมาเป็น INSERT
            $aProcessParams = [
                'oMsg' =>  $aJson
            ];
            $this->FSaCProcess($aProcessParams);
        }else if($aData['tTypeRB'] == 'DEL'){ //รับคิวมาเป็น DELETE
            $this->FSxCDeleteQueue($aData['ptDocNo']);
        }else if($aData['tTypeRB'] == 'ROLLBACK'){
            $aProcessParams = [
                'oMsg' =>  $aJson
            ];
            $this->FSxCRollBack($aProcessParams);
        }
        echo "\n====================================== END ==========================================\n\n";
    }

    //Producer ประมวลผล - STEP[2]
    public function FSaCProcess($paParams = []) {
        $oData = JSON_decode($paParams['oMsg'],true);
        echo "PROCESS_QUEUE : " . $oData['ptDocNo'], "\n\n";
        $this->FSxCMQWriteLog("PROCESS_QUEUE : ".$oData['ptDocNo']);
        switch ($oData['ptDocName']) {
            case "PCREQExport":
                break;
            case "PC":
                //ใบลดหนี้
                $aPackData = [
                    'ptDocNo'   =>  $oData['ptDocNo'],
                    'pnBchCode' =>  $oData['pnBchCode'],
					'ptWhoIns'  =>  $oData['ptWhoIns'],
					'ptUsrCode' =>  $oData['ptWhoCode']
                ];
                require_once APPPATH.'controllers/MQPC/cMQPC.php';
                $cMQPC = new cMQPC();
                $cMQPC->FSxCRABMainFunction($aPackData);
                break;
            case "PDTADJSTKCHK":
                //ใบเอกสารตรวจนับ
                $aPackData = [
                    'ptDocNo'   =>  $oData['ptDocNo'],
                    'pnBchCode' =>  $oData['pnBchCode'],
                    'ptUsrName' =>  $oData['ptWhoIns'],
                    'ptUsrCode' =>  $oData['ptWhoCode']
                ];
                require_once APPPATH.'controllers/MQPDTADJSTKCHK/cMQPDTADJSTKCHK.php';
                $cMQPDTADJSTKCHK = new cMQPDTADJSTKCHK();
                $cMQPDTADJSTKCHK->FSxCRABPASMainFunction($aPackData);
                break;
            default:
        }
    }

    //รัน % ความคืบหน้า - STEP[3]
    public function FSaReturnProgress($pnProgress,$ptDocumentNumber,$ptMessage = ''){

        if(!isset($ptMessage) || empty($ptMessage)){
            $ptMessage = '';
        }

        echo "[$ptDocumentNumber] Progress $pnProgress%\n";
        // $this->FSxCMQWriteLog("[$ptDocumentNumber] Progress $pnProgress%");
        $aFirstMsg = [
            'ptProgress'        => $pnProgress,
            'ptMessage'         => $ptMessage
        ];
        $aPublishParams = [
            'tMsgFormat'    => 'text',
            'tQname'        => $ptDocumentNumber,
            'tMsg'          => json_encode($aFirstMsg)
        ];
        $this->FSxMQPublish($aPublishParams);
    }

    //Consumer ส่งข้อความสถานะการทำงาน ให้กับผู้เรียก - STEP[4]
    public function FSxMQPublish($paParams = []) {
        $tMsgFormat = $paParams['tMsgFormat'];
        $tQueueName = $paParams['tQname'];

        // $oConnection = new AMQPStreamConnection(
        //     $this->aMQConfig['tHost'], 
        //     $this->aMQConfig['tPort'], 
        //     $this->aMQConfig['tUsername'], 
        //     $this->aMQConfig['tPassword'], 
        //     $this->aMQConfig['tVHost']
        // );
        // $oChannel = $oConnection->channel();
        
        $tMsg = '';
        switch ($tMsgFormat) {
            case 'json' : {
                $tMsg = json_encode($paParams['tMsg']);
                break;
            }
            case 'text' : {
                $tMsg = $paParams['tMsg'];
                break;
            }
            default : {
                $tMsg = $paParams['tMsg'];
            }
        }

        $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$tQueueName;
        if(strlen($MSMQInfo->Label) > 0){
            $oMSMQDest = new \COM("MSMQ.MSMQDestination") or die ("Error MSMQ.MSMQDestination");
            $oMSMQDest->PathName = ".\\private$\\".$tQueueName;
            $oMSMQMessage = new \COM("MSMQ.MSMQMessage");

            $oMSMQMessage->Priority = 5;
            $oMSMQMessage->Label = $tQueueName;
            $oMSMQMessage->Body  = $tMsg;
            $oMSMQMessage->Send($oMSMQDest);
        }
        
        // $oMessage = new AMQPMessage($tMsg);
        // $oChannel->basic_publish($oMessage, "", $tQueueName);
        // $oChannel->close();
        // $oConnection->close();
    
        //echo ' [/] Send Progress Success' , "\n";
    }

    //Consumer ส่งไป Export (call พี่ปุ้ย) STEP[4.1]
    public function FSxMQPublishExport($paParams = []) {
        $tMsgFormat = 'json';
        $tQueueName = 'exportservice';

        // $oConnection = new AMQPStreamConnection(
        //     $this->aMQConfig['tHost'], 
        //     $this->aMQConfig['tPort'], 
        //     $this->aMQConfig['tUsername'], 
        //     $this->aMQConfig['tPassword'], 
        //     $this->aMQConfig['tVHost']
        // );
        // $oChannel = $oConnection->channel();
        
        $tMsg = '';
        switch ($tMsgFormat) {
            case 'json' : {
                $tMsg = json_encode($paParams['tMsg']);
                break;
            }
            case 'text' : {
                $tMsg = $paParams['tMsg'];
                break;
            }
            default : {
                $tMsg = $paParams['tMsg'];
            }
        }

        $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$tQueueName;
        if(strlen($MSMQInfo->Label) > 0){
            $oMSMQDest = new \COM("MSMQ.MSMQDestination") or die ("Error MSMQ.MSMQDestination");
            $oMSMQDest->PathName = ".\\private$\\".$tQueueName;
            $oMSMQMessage = new \COM("MSMQ.MSMQMessage");

            $oMSMQMessage->Priority = 5;
            $oMSMQMessage->Label = $tQueueName;
            $oMSMQMessage->Body  = $tMsg;
            $oMSMQMessage->Send($oMSMQDest);
        }
        
        // $oMessage = new AMQPMessage($tMsg);
        // $oChannel->basic_publish($oMessage, "", $tQueueName);
        // $oChannel->close();
        // $oConnection->close();
    
        //echo ' [/] Send Progress Success' , "\n";
    }

    //Producer ลบคิวเอกสาร - STEP[5]
    public function FSxCDeleteQueue($ptDocNo){
        $tQueueName = $ptDocNo;
        // $oConnection = new AMQPStreamConnection(
        //     $this->aMQConfig['tHost'], 
        //     $this->aMQConfig['tPort'], 
        //     $this->aMQConfig['tUsername'], 
        //     $this->aMQConfig['tPassword'], 
        //     $this->aMQConfig['tVHost']
        // );
        
        $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$tQueueName;
        if(strlen($MSMQInfo->Label) > 0){ //เช็คว่ามี Queue อยู่ไหม
            $MSMQInfo->Delete();
            echo "DELETE_QUEUE : " . $ptDocNo . ' SUCCESS' . "\n";
            $this->FSxCMQWriteLog("DELETE_QUEUE : " . $ptDocNo . " SUCCESS");
        }

        // $oChannel = $oConnection->channel();
        // $oChannel->queue_delete($tQueueName);
        // $oChannel->close();
        // $oConnection->close();
    }

    //Producer roll back เอกสาร
    public function FSxCRollBack($paParams = []){
        echo ' [/] R O L L B A C K' , "\n";
        $this->FSxCMQWriteLog("[/] R O L L B A C K");

        // $ci = &get_instance();
        // $ci->load->database();
        // $ci->db->trans_rollback();

        $oData = JSON_decode($paParams['oMsg'],true);
        switch ($oData['ptDocName']) {
            case "PCREQExport":
                break;
            case "PC":
                //ใบลดหนี้
                $aPackData = [
                    'ptDocNo'   =>  $oData['ptDocNo'],
                    'pnBchCode' =>  $oData['pnBchCode'],
                    'ptWhoIns'  =>  $oData['ptWhoIns']
                ];
                require_once APPPATH.'controllers/MQPC/cMQPC.php';
                $cMQPC = new cMQPC();
                $cMQPC->FSxCRABRollBack($aPackData);
                break;
            case "PDTADJSTKCHK":
                break;
            default:
        }
    }

    public function FSxCMQWriteLog($ptLogMsg){
        $tLogData    = '['.date('Y-m-d H:i:s').'] '.$ptLogMsg."\n";
        $tFileName   = APPPATH.'logs/LogBackPrc_'.date('Ymd').'.txt';
        file_put_contents($tFileName,$tLogData,FILE_APPEND);
    }
   
}
