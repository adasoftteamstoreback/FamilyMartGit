<?php

namespace MSMQ;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Connection implements MessageComponentInterface {

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "Congratulations! the server is now running\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $paPackData) {
        $aPackData = json_decode($paPackData,true);

        $MSMQInfo = new \COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$aPackData['MSMQ_Data']['MSMQ_QueueName'];

        switch($aPackData['MSMQ_Type']){
            //Create Queue
            case "Create":
                if(strlen($MSMQInfo->Label) == 0){
                    $MSMQInfo->Label = $aPackData['MSMQ_Data']['MSMQ_QueueName'];
                    $MSMQInfo->Create(false,true);
                }
                break;
                
            //Delete Queue
            case "Delete":
                if(strlen($MSMQInfo->Label) > 0){ //เช็คว่ามี Queue อยู่ไหม
                    $MSMQInfo->Delete();
                }
                break;

            //Subscribe Queue
            case "Subscribe":
                if(strlen($MSMQInfo->Label) > 0){
                    $MSMQQueue = $MSMQInfo->Open(1,0);
                    $MSMQMessage = $MSMQQueue->Receive(0, False, True, 0, False);
                    if(isset($MSMQMessage->body)){
                        $from->send($MSMQMessage->body);
                    }else{
                        $from->send('null');
                    }
                    $MSMQQueue->Close();
                }
                break;

            //Publish Queue
            case "Publish":
                if(strlen($MSMQInfo->Label) > 0){
                    $oMSMQDest = new \COM("MSMQ.MSMQDestination") or die ("Error MSMQ.MSMQDestination");
                    $oMSMQDest->PathName = ".\\private$\\".$aPackData['MSMQ_Data']['MSMQ_QueueName'];
                    $oMSMQMessage = new \COM("MSMQ.MSMQMessage");

                    $oMSMQMessage->Priority = 5;
                    $oMSMQMessage->Label = $aPackData['MSMQ_Data']['MSMQ_QueueName'];
                    $oMSMQMessage->Body  = $aPackData['MSMQ_Data']['MSMQ_Message'];
                    $oMSMQMessage->Send($oMSMQDest);
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

}
?>