<?php

    function FCNxMSMQPulish($paParams){
        $oMSMQDest = new COM("MSMQ.MSMQDestination") or die ("Error MSMQ.MSMQDestination");
        $oMSMQDest->PathName = ".\\private$\\".$paParams['params']['ptDocNo'];
        $oMSMQMessage = new COM("MSMQ.MSMQMessage");

        $myXMLData =
        "<?xml version='1.0'?>
        <string>Test Message : 15/1/2563 18:03:39</string>";

        $oMSMQMessage->Label = $paParams['params']['ptDocNo'];
        $oMSMQMessage->Body = $myXMLData;
        $oMSMQMessage->Send($oMSMQDest);
    }

    function FCNxMSMQCreateQueue($paParams){
        $MSMQInfo = new COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$paParams['params']['ptDocNo'];
        $MSMQInfo->Label = $paParams['params']['ptDocNo'];
        $MSMQInfo->Create(false,true);
    }

    function FCNxMSMQDelQueue($paParams){
        $MSMQInfo = new COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$paParams['params']['ptDocNo'];
        $MSMQInfo->Delete();
    }

    function FCNxMSMQPurgeQueue($paParams){
        $MSMQInfo = new COM("MSMQ.MSMQQueueInfo") or die ("Error MSMQ.MSMQQueueInfo");
        $MSMQInfo->PathName = ".\\private$\\".$paParams['params']['ptDocNo'];
        $MSMQQueue = $MSMQInfo->Open(1,0);
        $MSMQQueue->Purge();
    }
    
?>