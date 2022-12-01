<?php

class cMQPC extends cMQ{
    
    public $dDateLog;

    public function __construct() {
        parent::__construct();
        $this->dDateLog = date('His'); 
        $this->load->model('MQPC/mMQPC','mMQPC'.$this->dDateLog , TRUE);
    }

    //Main page
    public function FSxCRABMainFunction($paPackData){
        $this->db->trans_strict(TRUE); //ถ้า Query ไหน Error จะ rollback ทั้งหมด
        //$this->db->trans_begin();
        $this->db->db_debug = false;    //ปิด Error ของ CI ตอนมัน Query Error

        $this->FSaReturnProgress('15',$paPackData['ptDocNo']);

        //CALL STEP[1]
        $this->FSxCRABClearTemp($paPackData);
    }

    //Step 1 ลบข้อมูลใน temp where comname
    public function FSxCRABClearTemp($paPackData){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        $this->$tConnectModal->FSxCMQPDeleteTemp();
        $this->FSaReturnProgress('20',$paPackData['ptDocNo']);

        //CALL STEP[2] 
        $this->FSxCRABCheckVatCodeinDocument($paPackData);
    }

    //Step 2 เช็ค vatcode มีมากกว่า 1 ต้อง split
    public function FSxCRABCheckVatCodeinDocument($paPackData){
        $tConnectModal = 'mMQPC'.$this->dDateLog;

        $aCheckByVatCode    = $this->$tConnectModal->FSxCMQPCheckVatCodeinDocument($paPackData);
		$tDocumentID        = $paPackData['ptDocNo'];
		
        if(count($aCheckByVatCode) == 1){
            //คือมี VATCODE ตัวเดียว
            // Fixed Issue Comsheet 2021-121 ถ้ามีสินค้าตัวเดียว Update FTSplCode, FTVatCode ให้กับ HD
            $this->$tConnectModal->FSaMPURUpdSplVatInHD($tDocumentID);

            //CALL STEP[6] 
            $this->FSaReturnProgress('30',$paPackData['ptDocNo']);
            $tReturnDocument    = $tDocumentID;
            $nCountDocument     = 1;
        }else{
            //คือมี VATCODE มากกว่าหนึ่ง
            $tReturnDocument = $tDocumentID;
            for($j=1; $j<count($aCheckByVatCode); $j++){
                $aReturnDoc = $this->$tConnectModal->FSaMPURSelectintoHDDT($tDocumentID,$aCheckByVatCode[$j],$paPackData);
                if($aReturnDoc['nStaReturn'] == 99){
                    $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aReturnDoc['aMessageError']);
                    $this->db->trans_rollback();
                    return false;
                }else{
                    $tReturnDocument .= ','. $aReturnDoc['tFormatCode'];
                }
            }
            $this->$tConnectModal->FSaMPURDeleteHDDT($tDocumentID,$aCheckByVatCode[0],$paPackData);
            $this->FSaReturnProgress('30',$paPackData['ptDocNo']);
            $nCountDocument     = count($aCheckByVatCode);
        }
		$this->FSxCMQWriteLog("[FSxCRABCheckVatCodeinDocument] พบ VatCode = ".$nCountDocument." รายการ");
		
        //CALL STEP[2.1] 
        $this->FSxCRABLoopDocument($paPackData,$nCountDocument,$tReturnDocument,0,$nCountDocument);
    }

    //Step 2.1 ย้ายข้อมูลจาก DT to Tmp
    public function FSxCRABLoopDocument($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
		$tConnectModal = 'mMQPC'.$this->dDateLog;
		$this->$tConnectModal->FSxCMQPDeleteTemp();


        if($nAllCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            //ลำดับเอกสารตัวแรก 
            $nSeqDoc                = $nSeqDoc;
            $nCountDocument         = 1;
            $tReturnDocument        = $tReturnDocument;
            $nAllCountDocument      = $nAllCountDocument;
            $paPackData['ptDocNo']  = $aReturnDocument[$nSeqDoc];
            $aMoveDTToTemp = $this->$tConnectModal->FSxCMQPMoveDTToTemp($paPackData,$aReturnDocument[$nSeqDoc]);
        }else{
            //ถ้ามีเอกสารเดียว
            $nSeqDoc            = null;
            $nAllCountDocument  = $nCountDocument;
            $aMoveDTToTemp = $this->$tConnectModal->FSxCMQPMoveDTToTemp($paPackData,$tReturnDocument);
        }
        if($aMoveDTToTemp['nStaReturn'] == 1){
            //CALL STEP[3] 
            $this->FSxCRABChangePDT($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
        }else{
            $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aMoveDTToTemp['aMessageError']);
            $this->db->trans_rollback();
        }
    }

    //Step 3 ปรับยอดคงเหลือสินค้า และทำให้สินค้ามีความเคลื่อนไหว
    public function FSxCRABChangePDT($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        if($nCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            for($i=0; $i<$nCountDocument; $i++){
                $this->$tConnectModal->FSxCMQPChangePDT($paPackData,$aReturnDocument[$i]);
            }
        }else{
            $this->$tConnectModal->FSxCMQPChangePDT($paPackData,'null');
        }
        // $this->FSxCMQWriteLog("[FSxCRABChangePDT] ปรับยอดคงเหลือ และเคลื่อนไหวสินค้า = ".$nCountDocument." รายการ");
        $this->FSaReturnProgress('45',$paPackData['ptDocNo']);

        //CALL STEP[4] 
        $this->FSxCRABAdjInWha($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
    }

    //Step 4 ปรับสต้อกสินค้าต่อคลัง InWha
    public function FSxCRABAdjInWha($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        if($nCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            for($i=0; $i<$nCountDocument; $i++){
                $aInWha = $this->$tConnectModal->FSxCMQPInWha($paPackData,$aReturnDocument[$i]);
                if($aInWha['nStaReturn'] == 99){
                    $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aInWha['aMessageError']);
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }else{
            $aInWha = $this->$tConnectModal->FSxCMQPInWha($paPackData,'null');
            if($aInWha['nStaReturn'] == 99){
                $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aInWha['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }
        }
        // $this->FSxCMQWriteLog("[FSxCRABAdjInWha] ปรับสต้อกสินค้าคงคลัง InWha = ".$nCountDocument." รายการ");
        $this->FSaReturnProgress('50',$paPackData['ptDocNo']);

        //CALL STEP[5] 
        $this->FSxCRABProcessSTKCard($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
    }

    //Step 5 ปรับสต๊อกการ์ด STKCard
    public function FSxCRABProcessSTKCard($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        if($nCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            for($i=0; $i<$nCountDocument; $i++){
                $this->$tConnectModal->FSxCMQPSTKCard($paPackData,$aReturnDocument[$i]);
            }
        }else{
            $this->$tConnectModal->FSxCMQPSTKCard($paPackData,'null');
        }
        // $this->FSxCMQWriteLog("[FSxCRABProcessSTKCard] ปรับสต๊อกการ์ด = ".$nCountDocument." รายการ");
        $this->FSaReturnProgress('60',$paPackData['ptDocNo']);

        //CALL STEP[6] 
        $this->FSxCRABProrate($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
    }

    //Step 6 คำนวณ Prorate และกลับไปอัพเดทที่ DT
    public function FSxCRABProrate($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        if($nCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            for($i=0; $i<$nCountDocument; $i++){
                $aProrate = $this->$tConnectModal->FSxCMQPProrate($paPackData,$aReturnDocument[$i]);
                if($aProrate['nStaReturn'] == 99){
                    $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aProrate['aMessageError']);
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }else{
            $aProrate = $this->$tConnectModal->FSxCMQPProrate($paPackData,'null');
            if($aProrate['nStaReturn'] == 99){
                $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aProrate['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }
        }
        // $this->FSxCMQWriteLog("[FSxCRABProrate] คำนวณ Prorate และอัพเดท DT = ".$nCountDocument." รายการ");
        $this->FSaReturnProgress('70',$paPackData['ptDocNo']);

        //CALL STEP[7] 
        $this->FSxCRABUpdateHD($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
    }

    //Step 7 กลับไปอัพเดทเอกสาร ว่าอนุมัติแล้ว
    public function FSxCRABUpdateHD($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        if($nCountDocument > 1){
            $aReturnDocument = explode(",",$tReturnDocument);
            for($i=0; $i<$nCountDocument; $i++){
                $this->$tConnectModal->FSxCMQPApprove($paPackData,$aReturnDocument[$i]);
                $this->FSxCMQWriteLog("[FSxCRABUpdateHD] อนุมัติเอกสาร = ".$aReturnDocument[$i]);
            }
        }else{
            $this->$tConnectModal->FSxCMQPApprove($paPackData,'null');
            $this->FSxCMQWriteLog("[FSxCRABUpdateHD] อนุมัติเอกสาร = ".$paPackData['ptDocNo']);
        }
        
        $this->FSaReturnProgress('75',$paPackData['ptDocNo']);

        //CALL STEP[8] 
        $this->FSxCRABUpdateSTKDaily($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
    }

    //Step 8 ไปอัพเดทปฎิทิน ว่า task นี้เคยทำงานเเล้ว
    public function FSxCRABUpdateSTKDaily($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        // if($nCountDocument > 1){
        //     $aReturnDocument = explode(",",$tReturnDocument);
        //     for($i=0; $i<$nCountDocument; $i++){
        //         $this->$tConnectModal->FSxCMQPUpdateSTKDaily($paPackData,$aReturnDocument[$i]);
        //     }
        // }else{
        //     $this->$tConnectModal->FSxCMQPUpdateSTKDaily($paPackData,'null');
        // }
        $aUpdSTKDaily = $this->$tConnectModal->FSxCMQPUpdateSTKDaily($paPackData);
        if($aUpdSTKDaily['nStaReturn'] == 1){
            //CALL STEP[9] 
            $this->FSaReturnProgress('78',$paPackData['ptDocNo']);
            $this->FSxCRABChangeBalanceonHand($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
        }else{
            $this->FSaReturnProgress('-1',$paPackData['ptDocNo'],$aUpdSTKDaily['aMessageError']);
            $this->db->trans_rollback();
        }
    }

    //Step 9 ปรับบาลานซ์ ออน แฮนด์
    public function FSxCRABChangeBalanceonHand($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        
        // Comsheet 2020-203 เอาตาราง TCNTBi ออก
        // if($nCountDocument > 1){
        //     $aReturnDocument = explode(",",$tReturnDocument);
        //     for($i=0; $i<$nCountDocument; $i++){
        //         $this->$tConnectModal->FSxCMQPUpdateBalanceonHand($paPackData,$aReturnDocument[$i]);
        //     }
        // }else{
        //     $this->$tConnectModal->FSxCMQPUpdateBalanceonHand($paPackData,'null');
        // }

        $this->FSaReturnProgress('80',$paPackData['ptDocNo']);

        //CALL STEP[10] 
        if($nAllCountDocument > 1){
            $nNewSeqDoc = (int)$nSeqDoc + 1;
            if($nNewSeqDoc == $nAllCountDocument){
                $this->FSxCRABCallExport($paPackData,$nCountDocument,$tReturnDocument,$nNewSeqDoc,$nAllCountDocument);
            }else{
                //loop กลับไปทำใหม่
                $this->FSxCRABLoopDocument($paPackData,$nCountDocument,$tReturnDocument,$nNewSeqDoc,$nAllCountDocument);
            }
        }else{
            // มีตัวเดียว
            $this->FSxCRABCallExport($paPackData,$nCountDocument,$tReturnDocument,$nSeqDoc,$nAllCountDocument);
        }
    }

    //Step 10 Export ไฟล์
    public function FSxCRABCallExport($paPackData,$nCountDocument,$tReturnDocument,$nAllCountDocument){
        if($nAllCountDocument > 1){
            $tCallExport        = '';
            $aReturnDocument    = explode(",",$tReturnDocument);
            for($i=0;$i<$nAllCountDocument; $i++){
                $tCallExport .= '{';
                $tCallExport .= '"ptTbl":"TACTPtHD",';
                $tCallExport .= '"ptDocType":"5,6",';
                $tCallExport .= '"ptPrcDocNo":"'.$aReturnDocument[$i].'",';
                $tCallExport .= '"ptStartDocNo":"",';
                $tCallExport .= '"ptEndDocNo":""';
                $tCallExport .= '},';

                if($i == $nAllCountDocument-1){
                    $tCallExport = substr($tCallExport,0,-1);
                }
                $this->FSxCMQWriteLog("[FSxCRABCallExport] Export DocNo = ".$aReturnDocument[$i]);
            }
        }else{
            $tDocNo = $paPackData['ptDocNo'];
            $tCallExport = "{";
            $tCallExport .= '"ptTbl":"TACTPtHD",';
            $tCallExport .= '"ptDocType":"5,6",';
            $tCallExport .= '"ptPrcDocNo":"'.$tDocNo.'",';
            $tCallExport .= '"ptStartDocNo":"",';
            $tCallExport .= '"ptEndDocNo":""';
            $tCallExport .= "}";
            $this->FSxCMQWriteLog("[FSxCRABCallExport] Export DocNo = ".$tDocNo);
        }

        $aFirstMsg = "{";
        $aFirstMsg .= '"ExportServiceList":[' . $tCallExport .']';
        $aFirstMsg .= "}";

        $aPublishParams = [
			'tMsgFormat'    => 'text',
			'tQname'		=> 'ExportService',
            'tMsg'          => $aFirstMsg
        ];
        $this->FSxCMQWriteLog("[FSxCRABCallExport] ".json_encode($aPublishParams));
        $this->FSxMQPublish($aPublishParams);
    }
    
    //---------------- Roll Back ----------------//
    public function FSxCRABRollBack($paPackData){
        $tConnectModal = 'mMQPC'.$this->dDateLog;
        $this->$tConnectModal->FSxCMQPRollBack();
        $this->FSaReturnProgress('100',$paPackData['ptDocNo']);
    }
}
