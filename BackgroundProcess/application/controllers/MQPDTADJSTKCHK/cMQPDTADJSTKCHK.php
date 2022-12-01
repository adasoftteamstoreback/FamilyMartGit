<?php

class cMQPDTADJSTKCHK extends cMQ{
    
    public $oConnectModal;

    public function __construct() {
        parent::__construct();
        $this->oConnectModal = 'mMQPDTADJSTKCHK'.date('His');
        $this->load->model('MQPDTADJSTKCHK/mMQPDTADJSTKCHK',$this->oConnectModal);
    }

    //Main page
    public function FSxCRABPASMainFunction($paData){
        try{
            $this->db->trans_strict(TRUE); //ถ้า Query ไหน Error จะ rollback ทั้งหมด
            $this->db->trans_begin();
            $this->db->db_debug = false;    //ปิด Error ของ CI ตอนมัน Query Error
            // ini_set('memory_limit', '9999M');
            // ini_set('sqlsrv.ClientBufferMaxKBSize', '9999M');
            $this->FSaReturnProgress('1',$paData['ptDocNo']); //ส่ง 1 กลับไปเพื่อให้หน้าเว็บรอรับ process ถัดไป
            $tConnectModal = $this->oConnectModal;
            // 1.func DOCxApproved
            // 2.func PrcxReSeqPdtChkDT
            // 3.func PRCbAdjStk
            //     3.1 PRCxGenAdjDT
            //     3.2 PRCxGenAdjHD
            //     3.3 DOCxApprovedT In and Out

            //STEP 3 count stk and gen adjust stk
            $aDataHD    = $this->$tConnectModal->FSaMRABPASGetDataHD($paData);
            $aChkDataHQ = $this->$tConnectModal->FSaMRABPASCheckDataFromHQ($paData);
            // print_r($aDataHD);
            if($aDataHD['nStaReturn'] == 1){
                if($aDataHD['aResult']['FTIuhAdjType'] == '1'){

                    $aDefPriLvl  = $this->$tConnectModal->FSaMRABPASGetDefPriLvl();
                    $aGetVatRate = $this->$tConnectModal->FSaMRABPASGetVatRate();
                    $nGetNonVat  = $this->$tConnectModal->FSnMRABPASGetNonVat($paData['ptDocNo']);
                    $tDocNoTD    = $this->$tConnectModal->FStMRABPASGenAMDocNo('TCNTPdtTnfHD','FTPthDocNo','9');
                    $tDocNoTE    = $this->$tConnectModal->FStMRABPASGenAMDocNo('TCNTPdtTnfHD','FTPthDocNo','0');

                    //ข้อมูลจำเป็นในการปรับสต๊อกสินค้า
                    $aDataReq = array(
                        'aDefPriLvl'        => $aDefPriLvl,
                        'aGetVatRate'       => $aGetVatRate,
                        'nGetNonVat'        => $nGetNonVat,
                        'tDocNoTD'          => $tDocNoTD,
                        'tDocNoTE'          => $tDocNoTE
                    );

                    //เตรียมข้อมูลเพื่อนำไปค้นหาข้อมูลสินค้า
                    $aDataQuery = array(
                        'FTIuhDocNo'    => $paData['ptDocNo'],
                        'FTBchCode'     => $paData['pnBchCode'],
                        'FTIuhDocType'  => '2'
                    );

                    // จัดการสต๊อกสินค้าที่อยู่ใน DT(ใบรวม)
                    // CFMPOS-STAR Phase2-RQ-12 Full Stock count (Audit stock)-Analyze-00.04.00.xlsx 
                    // (sheet: Flow-New requirement 6.1.1) FTIuhRefTaxLoss = 'CFM','TOP'
                    $aDataAdjStk = array(
                        'nAdjPrcType'       => 1,
                        'aDataList'         => $this->$tConnectModal->FSaMRABPASGetDataPdtChkDT($aDataQuery),
                        'nStartProgress'    => 3, //เริ่มจาก 5 - 30
                        'nMaxProgress'      => 30
                    );
                    $this->FSxCMQWriteLog(" ============================ START ADJUST STOCK IN DT ================================");
                    $this->FSbCRABPASPrcAdjStk($paData,$aDataAdjStk,$aDataReq);
                    $this->FSxCMQWriteLog(" ============================= END ADJUST STOCK IN DT =================================");

                    // ต้องเป็นรายการ Full Count (HQ)
                    // ไปหาสินค้าที่ไม่ได้อยู่ใน ChkDT
                    // CFMPOS-STAR Phase2-RQ-12 Full Stock count (Audit stock)-Analyze-00.04.00.xlsx 
                    // (sheet: Flow-New requirement 6.1.1) FTIuhRefTaxLoss = 'CFM'
                    // สินค้ารายการใดที่ ไม่ได้อยู่ใน เอกสารตรวจนับ(ใบรวม) ให้ คำนวณยอด Diff โดย  หาจำนวนสินค้า ‘เคลื่อนไหวหลังนับ” เทียบกับ TCNMPdt.FCPdtQtyRet และ Add สินค้านั้นเพื่อสร้างรายการ 
                    // Adjust เพิ่มสินค้า (เอกสาร TD/ TCNTPdtTnfHD type ‘9’)
                    // Adjust ลดสินค้า(เอกสาร TE/ TCNTPdtTnfHD type ‘0’) ด้วย เพื่อให้ยอดสต๊อก เป็น 0 (TCNMPdt.FCPdtQtyRet = 0, TCNMPdt.FCPdtQtyNow=0) แต่ไม่ต้อง Add สินค้านั้นลงใน เอกสารตรวจนับ(ใบรวม)
                    if($aChkDataHQ['nStaReturn'] == 1){
                        if($aDataHD['aResult']['FTIuhRefTaxLoss'] == 'CFM' || $aDataHD['aResult']['FTIuhRefTaxLoss'] == ''){
                            //จัดการสต๊อกสินค้า ทั้งหมดที่ไม่ได้อยู่ใน DT(ใบรวม)
                            $this->FSxCMQWriteLog(" ============================ START FULL COUNT ================================");
                            $aDataAdjStk = array(
                                'nAdjPrcType'       => 2, 
                                'aDataList'         => $this->$tConnectModal->FSaMRABPASGetDataWithOutDT($aDataQuery,1),
                                'nStartProgress'    => 33, //เริ่มจาก 35 - 60
                                'nMaxProgress'      => 60  
                            );
                            $this->FSbCRABPASPrcAdjStk($paData,$aDataAdjStk,$aDataReq);
                            $this->FSxCMQWriteLog(" ============================= END FULL COUNT =================================");
                        }
                    }

                }
            }else{
                $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aDataHD['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }

            //SETP 3.5 ตรวจสอบสินค้าที่ QtyRet ใน (master) ไม่ถูกต้อง และนำปรับ QtyRet ให้เป็น 0
            // CFMPOS-STAR Phase2-RQ-12 Full Stock count (Audit stock)-Analyze-00.04.00.xlsx 
            // (sheet: Flow-New requirement 6.1.1) FTIuhRefTaxLoss = 'CFM'
            if($aChkDataHQ['nStaReturn'] == 1){
                if($aDataHD['aResult']['FTIuhAdjType'] == '1'){
                    if($aDataHD['aResult']['FTIuhRefTaxLoss'] == 'CFM' || $aDataHD['aResult']['FTIuhRefTaxLoss'] == ''){
                        //เตรียมข้อมูลเพื่อนำไปค้นหาข้อมูลสินค้า
                        $aDataQuery = array(
                            'FTIuhDocNo'    => $paData['ptDocNo'],
                            'FTBchCode'     => $paData['pnBchCode'],
                            'FTIuhDocType'  => '2',
                            'FTWhoUpd'      => $paData['ptUsrName']
                        );
                        $aUpdPdtWithOutDT = $this->$tConnectModal->FSaMRABPASUpdPdtWithOutDT($aDataQuery);
                        if($aUpdPdtWithOutDT['nStaReturn'] == 1){
                            $this->FSaReturnProgress('70',$paData['ptDocNo']);
                        }else{
                            $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aUpdPdtWithOutDT['aMessageError']);
                            $this->db->trans_rollback();
                            return false;
                        }
                    }
                }
            }

            //STEP 4 เช็คสินค้าในเอกสารต่างๆ เพื่อทำการ Update วันที่เคลื่อนไหวล่าสุด
            if($aDataHD['aResult']['FTIuhAdjType'] == '1'){
                $aPdtInDocActUpd = $this->$tConnectModal->FSxMRABPASPdtInDocActUpd($paData); //'nEN_DocMergeStock'
                if($aPdtInDocActUpd['nStaReturn'] == 1){
                    $this->FSaReturnProgress('75',$paData['ptDocNo']);
                }else{
                    $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aPdtInDocActUpd['aMessageError']);
                    $this->db->trans_rollback();
                    return false;
                }
            }

            //STEP 5 อัพเดท StaPrc HD and DT
            $aUpdPdtChkHDDT = $this->$tConnectModal->FSxMRABPASUpdatePdtChkHDandDT($paData);
            if($aUpdPdtChkHDDT['nStaReturn'] == 1){
                $this->FSaReturnProgress('80',$paData['ptDocNo']);
            }else{
                $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aUpdPdtChkHDDT['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }

            //STEP 6 อัพเดท JobDaily
            $aUpdJobDaily = $this->$tConnectModal->FSxMRABPASUpdateJobDaily($paData);
            if($aUpdJobDaily['nStaReturn'] == 1){
                $this->FSaReturnProgress('85',$paData['ptDocNo']);
            }else{
                $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aUpdJobDaily['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }

            //STEP 7 หาสินค้าของใบตรวจนับใบย่อย ที่ไม่มีในใบรวม
            $aPdtChkDtCut = $this->$tConnectModal->FSxMRABPASPdtChkDTCut($paData);
            if($aPdtChkDtCut['nStaReturn'] == 1){
                $this->FSaReturnProgress('90',$paData['ptDocNo']);
                // $this->db->trans_commit();
            }else{
                $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aPdtChkDtCut['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }


            //STEP 8 (เพิ่มใหม่) Update HQDiff to FTClrName
            // $this->$tConnectModal->FSxMRABPASUpdHQDiff($paData); //Comsheet 2019-302_2 ขอยกเลิกการคำนวณ
            // $this->FSaReturnProgress('80',$paData['ptDocNo']);
            $this->$tConnectModal->FSxMRABPASCallStoredUpdatePdtChkDT($paData);

            $this->FSxCMQWriteLog("[FSaMRABPASChkTSysSQL] Start Check STP_PRCxUpdatePdtChkDT");
            $x = 0;
            while($x = 0) {
                $aChkTSysSQL = $this->$tConnectModal->FSaMRABPASChkTSysSQL($paData);
                $tPrcSta = $aChkTSysSQL['aResult'];
                if( $tPrcSta == "Y" ){
                    $x = 1;
                }
            }
            $this->FSxCMQWriteLog("[FSaMRABPASChkTSysSQL] Finish Check STP_PRCxUpdatePdtChkDT");
            

            //STEP 9 Export Zip File
            //Last Update: Napat(Jame) 14/10/2022 RQ-12 กรณีอนุมัติข้ามวัน หลังจากที่ทำการอนุมัติเรียบร้อยแล้ว จะต้องทำการ Auto Export ข้อมูลออกไป HQ
            $aSubHDDocDate = $this->$tConnectModal->FSxMRABPASGetMaxDocDateSubHD($paData); // ค้นหาวันที่บันทึกเอกสารของใบย่อย
            if( $aSubHDDocDate['nStaReturn'] == 1 ){
                $dSubHDDocDate  = $aSubHDDocDate['aResult']['FDIuhDocDate'];
                $dDateCurrent   = date('Y-m-d');
                $this->FSxCMQWriteLog("[AutoExport] วันที่ปัจจุบัน:".$dDateCurrent." วันที่ใบย่อย:".$dSubHDDocDate);
                if( $dDateCurrent > $dSubHDDocDate ){
                    // $aFirstMsg = [
                    //     'ExportServiceList' => [
                    //         [
                    //             'ptTbl'         => 'TCNTPdtChkHD',
                    //             'ptDocType'     => '2',
                    //             'ptPrcDocNo'    => $paData['ptDocNo'],
                    //             'ptStartDocNo'  => '',
                    //             'ptEndDocNo'    => ''
                    //         ],
                    //         [
                    //             'ptTbl'         => 'TCNTPdtStkNotExist',
                    //             'ptDocType'     => '2',
                    //             'ptPrcDocNo'    => $paData['ptDocNo'],
                    //             'ptStartDocNo'  => '',
                    //             'ptEndDocNo'    => ''
                    //         ]
                    //     ]
                    // ];
                    // $aPublishParams = [
                    //     'tMsgFormat'    => 'text',
                    //     'tQname'        => 'ExportService',
                    //     'tMsg'          => json_encode($aFirstMsg)
                    // ];
                    // $this->FSxMQPublish($aPublishParams);

                    // อัพเดท FTSqlCode = 'ExpFullCnt' ก่อนสั่งรัน .vbs
                    $this->$tConnectModal->FSxMRABPASUpdExpFullCnt($paData);
                    exec('C:\WINDOWS\system32\cmd.exe /c START ..\BackgroundProcess\RunExpFullCount.vbs');

                    $this->FSxCMQWriteLog("[AutoExport] ".json_encode($aFirstMsg));
                    $this->FSxCMQWriteLog("[AutoExport] ส่งข้อมูลให้ MQ Success");
                }else{
                    $this->FSxCMQWriteLog("[AutoExport] เอกสารนี้อนุมัติภายในวัน ไม่ต้อง Export");
                }

                $this->FSaReturnProgress('95',$paData['ptDocNo']);
                $this->db->trans_commit();
            }else{
                $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aPdtChkDtCut['aMessageError']);
                $this->db->trans_rollback();
                return false;
            }
            
            $this->FSaReturnProgress('99',$paData['ptDocNo']);

            // $this->db->trans_commit();
        }catch(Exception $e){
            $this->FSxCMQWriteLog("FSxCRABPASMainFunction: ".$e->getMessage());
            echo 'Message: ' .$e->getMessage();
            return false;
        }
    }

    //STEP 3
    public function FSbCRABPASPrcAdjStk($paData,$paPackDataList,$paDataReq){
        try{
            $tConnectModal = $this->oConnectModal;            
            $aDataListDT = $paPackDataList['aDataList'];

            if($aDataListDT['tCode'] == '1' && $aDataListDT['nRows'] > 0){
                // $this->FSxCMQWriteLog("FSbCRABPASPrcAdjStk: TRUE");
                // $aDefPriLvl  = $this->$tConnectModal->FSaMRABPASGetDefPriLvl();
                // $aGetVatRate = $this->$tConnectModal->FSaMRABPASGetVatRate();
                // $nGetNonVat  = $this->$tConnectModal->FSnMRABPASGetNonVat($paData['ptDocNo']);
                // $tDocNoTD    = $this->$tConnectModal->FStMRABPASGenAMDocNo('TCNTPdtTnfHD','FTPthDocNo','9');
                // $tDocNoTE    = $this->$tConnectModal->FStMRABPASGenAMDocNo('TCNTPdtTnfHD','FTPthDocNo','0');

                $aDefPriLvl  = $paDataReq['aDefPriLvl'];
                $aGetVatRate = $paDataReq['aGetVatRate'];
                $nGetNonVat  = $paDataReq['nGetNonVat'];
                $tDocNoTD    = $paDataReq['tDocNoTD'];
                $tDocNoTE    = $paDataReq['tDocNoTE'];
                
                $nCountTD   = 0;
                $nCountTE   = 0;
                $nCountRow  = 0;  // จำนวนรายการสินค้าที่ วนลูป
                $nPrgSend   = $paPackDataList['nStartProgress'];  // Queue %
                $nPersen    = 10; // ตั้งค่าตัวหาร เริ่มต้นด้วย 10%
                // print_r($aDataListDT['aItems']);
                foreach($aDataListDT['aItems'] AS $tKey => $tValue){
                    $nCountRow++;
                    $tDocType = $aDataListDT['aItems'][$tKey]['tDocType'];
                    // if($tDocType <> NULL || $tDocType !== NULL){
                        if($tDocType == '9'){ $nCountTD++; }else{ $nCountTE++; }
                        $aDataGenAdj = array(
                            'nSeq'          => ($tDocType == '9' ? $nCountTD : $nCountTE),
                            'tBchCode'      => $paData['pnBchCode'],
                            'tUsrName'      => $paData['ptUsrName'],
                            'tUsrCode'      => $paData['ptUsrCode'],
                            'tDocType'      => $tDocType,
                            'tDocNoIn'      => ($tDocType == '9' ? $tDocNoTD : $tDocNoTE),
                            'aDefPriLvl'    => $aDefPriLvl['aItems'],
                            'bIsVatExclude' => TRUE, //กำหนดให้ภาษีเป็นแบบ แยกนอก
                            'nNonVat'       => $nGetNonVat,
                            'tVatCode'      => $aGetVatRate['aItems']['FTVatCode'],
                            'cVatRate'      => $aGetVatRate['aItems']['FCVatRate'],
                            'aData'         => $aDataListDT['aItems'][$tKey]
                        );
                        //STEP 3.1 - 3.2
                        $aGenAdjDT = $this->$tConnectModal->FSaMRABPASGenAdjDT($aDataGenAdj);
                        if($aGenAdjDT['nStaReturn'] == 99){
                            $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aGenAdjDT['aMessageError']);
                            $this->db->trans_rollback();
                            return false;
                        }
                        $aGenAdjHD = $this->$tConnectModal->FSaMRABPASGenAdjHD($aDataGenAdj);
                        if($aGenAdjHD['nStaReturn'] == 99){
                            $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aGenAdjHD['aMessageError']);
                            $this->db->trans_rollback();
                            return false;
                        }

                        //ถ้ามีข้อมูลมากกว่า 10 หาจำนวนข้อมูลทั้งหมดเป็นเปอร์เซ็น ทุกๆ ..% ให้ส่งกลับไปที่ MQ
                        if($aDataListDT['nRows'] >= 10){
                            if($nCountRow == ROUND(($nPersen*$aDataListDT['nRows'])/100)){
                                $this->FSaReturnProgress($nPrgSend,$paData['ptDocNo']);
                                $nPrgSend += 3;
                                $nPersen  += 10;
                            }
                        }
                    // }
                }

                //STEP 3.3 อนุมติ เอกสาร adjust IN และ adjust OUT
                $this->FSxCMQWriteLog("nCountTD: ".$nCountTD);
                $this->FSxCMQWriteLog("nCountTE: ".$nCountTE);

                if($nCountTD > 0){
                    // $this->FSxCMQWriteLog("DocNoTD: ".$tDocNoTD);
                    $this->FSaCRABPASApprovedTInAndTOut($tDocNoTD,$paData,"IN",$paPackDataList['nAdjPrcType']);
                    // $aPrcPdtBIDoc = $this->$tConnectModal->FSxMRABPASPrcPdtBIDoc($tDocNoTD); // Comsheet 2020-203 เอาตาราง TCNTBi ออก
                }
                if($nCountTE > 0){
                    // $this->FSxCMQWriteLog("DocNoTE: ".$tDocNoTE);
                    $this->FSaCRABPASApprovedTInAndTOut($tDocNoTE,$paData,"OUT",$paPackDataList['nAdjPrcType']);
                    // $aPrcPdtBIDoc = $this->$tConnectModal->FSxMRABPASPrcPdtBIDoc($tDocNoTE); // Comsheet 2020-203 เอาตาราง TCNTBi ออก
                }
                // print_r($aPrcPdtBIDoc);
                // exit;

                // if($aPrcPdtBIDoc['nStaReturn'] == 99){
                //     $this->FSaReturnProgress('-1',$paData['ptDocNo'],$aPrcPdtBIDoc['aMessageError']);
                //     $this->db->trans_rollback();
                //     return false;
                // }

                //ถ้าข้อมูลน้อยกว่า 10 ให้ return ...% ตอนวนลูปเสร็จทีเดียว
                if($aDataListDT['nRows'] < 10){ 
                    $this->FSaReturnProgress($paPackDataList['nMaxProgress'],$paData['ptDocNo']);
                }
                return true;
            }else{
                // $this->FSxCMQWriteLog("FSbCRABPASPrcAdjStk: FALSE");
                $this->FSaReturnProgress($paPackDataList['nMaxProgress'],$paData['ptDocNo']);
                return true;
            }
        }catch(Exception $e){
            $this->FSxCMQWriteLog("FSbCRABPASPrcAdjStk: ".$e->getMessage());
            echo 'Message: ' .$e->getMessage();
            return false;
        }
    }

    //approved Product Transfer-In and Transfer-OUT document
    public function FSaCRABPASApprovedTInAndTOut($ptDocNo,$paData,$ptType,$pnAdjType){
        try{
            $tConnectModal = $this->oConnectModal;
            if($this->$tConnectModal->FSbMRABPASSumPdt2Temp($ptType, $ptDocNo)){                            //process to temp
                // $this->FSxCMQWriteLog("INSERT TCNTmpPrcDT: TRUE");
                if($this->$tConnectModal->FSbMRABPASPrcStk($ptType,$ptDocNo,'001',$paData,$pnAdjType)){
                    // $this->FSxCMQWriteLog("FSbMRABPASPrcStk: TRUE");
                }else{
                    // $this->FSxCMQWriteLog("FSbMRABPASPrcStk: ERROR");
                    return false;
                }
            }else{
                // $this->FSxCMQWriteLog("INSERT TCNTmpPrcDT: ERROR");
                return false;
            }

            // $this->$tConnectModal->FSbMRABPASUpdStkAvg($paData['pnBchCode'],$ptDocNo); //Update StkAvg
            $aDataApv = array(
                'tDocNo'    => $paData['ptDocNo'],
                'tBchCode'  => $paData['pnBchCode'],
                'tUsrName'  => $paData['ptUsrName'],
                'tUsrCode'  => $paData['ptUsrCode']
            );
            return $this->$tConnectModal->FSaMRABPASUpdateApproveHD($aDataApv);
        }catch(Exception $e){
            $this->FSxCMQWriteLog("FSaCRABPASApprovedTInAndTOut: ".$e->getMessage());
            echo 'Message: ' .$e->getMessage();
            return false;
        }
    }

    //approved Product Transfer-Out document
    // public function FSaCRABPASApprovedTOut($ptDocNoOut,$paData){
    //     $tConnectModal = $this->oConnectModal;
    //     if($this->$tConnectModal->FSbMRABPASSumPdt2Temp("OUT", $ptDocNoOut, False)){                            //process to temp
    //         if(!$this->$tConnectModal->FSbMRABPASPrcStk("OUT", $ptDocNoOut, '001', $paData)){
    //             $aDataReturn = array(
    //                 'tCode'     => 'Error FSbMRABPASPrcStk'
    //             );
    //             return $aDataReturn;
    //         }
    //     }else{
    //         $aDataReturn = array(
    //             'tCode'     => 'Error FSbMRABPASSumPdt2Temp'
    //         );
    //         return $aDataReturn;
    //     }

    //     $aDataApv = array(
    //         'tDocNo'    => $paData['ptDocNo'],
    //         'tUsrName'  => $paData['ptUsrName'],
    //         'tUsrCode'  => $paData['ptUsrCode']
    //     );
    //     return $this->$tConnectModal->FSaMRABPASUpdateApprove($aDataApv);
    // }

    public function FSxCRABPASErrorRespone($ptFuncName,$ptDocNo){
        try{
            $this->FSaReturnProgress('-1',$ptDocNo);
            echo "[Error] function $ptFuncName\n";
        }catch(Exception $e){
            echo 'Message: ' .$e->getMessage();
            return false;
        }
    }
   
}
