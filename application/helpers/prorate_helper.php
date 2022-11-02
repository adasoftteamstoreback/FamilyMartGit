<?php

    // FCNaHCalculateProrate('TAPTPcHD','DEMODOCNO');

    //คำนวณ prorate
    function FCNaHCalculateProrate($ptPagename,$ptDocumentNo){
        include('autoload.php');
        set_time_limit(0);

        //Step 01 : วิ่งไปเช็คว่ามีส่วนลดท้ายบิล 
        $DB             = new Driver_database();
        $tSQLCheckDT    = "SELECT DT.FTXrhDocNo , HD.FCXrhDis FROM TACTPrDT DT
                            LEFT JOIN TACTPrHD HD ON DT.FTXrhDocNo = HD.FTXrhDocNo
                            WHERE DT.FTXrhDocNo = '$ptDocumentNo' ";
        $oResultCheclDT = $DB->DB_SELECT($tSQLCheckDT);
        if(empty($oResultCheclDT)){
            return;
        }else{
            $tSQLDT         = "SELECT 
                                    DT.FTPdtCode,
                                    DT.FNXrdSeqNo,
                                    DT.FTXrhDocNo,
                                    DT.FCXrdNet,
                                    DT.FTPdtNoDis,
                                    DT.FTXrhVATInOrEx,
                                    DT.FCXrdVat ,
                                    DT.FCXrdVatable ,
                                    HD.FCXrhVATRate,
                                    HD.FCXrhAftDisChg,
                                    SDT.NETS
                                FROM TACTPrDT DT 
                                INNER JOIN (SELECT FTXrhDocNo,SUM(FCXrdNet) AS NETS FROM TACTPrDT WHERE FTXrhDocNo = '$ptDocumentNo' AND FTPdtNoDis = 2  GROUP BY FTXrhDocNo ) SDT 
                                ON SDT.FTXrhDocNo = DT.FTXrhDocNo 
                                LEFT JOIN TACTPrHD HD ON  HD.FTXrhDocNo = DT.FTXrhDocNo
                                WHERE DT.FTXrhDocNo = '$ptDocumentNo' 
                                AND DT.FTPdtNoDis = 2 ";
            $oResultQueryDT = $DB->DB_SELECT($tSQLDT);
            if(empty($oResultQueryDT)){
                return;
            }else{
                //ส่วนลดท้ายบิล 
                $nDiscount      = $oResultCheclDT[0]['FCXrhDis'];
                $nDecimal       = 2;
                $aProrateByproduct = array();

                for($i=0; $i<count($oResultQueryDT); $i++){
                    //ต้องอนุญาติลดเท่านั้น FTPdtNoDis = 2
                    if($oResultQueryDT[$i]['FTPdtNoDis'] == 2){
                        if($oResultQueryDT[$i]['FCXrdNet'] == 0){
                            //ราคาสุทธิเป็น 0 (คือราคาหักส่วนลดรายการ)
                            $nProrate       = round($nDiscount * 0,2);
                        }else{
                            //ผลรวมทั้งหมดของราคาสุทธิ
                            if($oResultQueryDT[$i]['NETS'] == 0){
                                $nProrate       = round($nDiscount * $oResultQueryDT[$i]['FCXrdNet'],2);
                            }else{
                                $nProrate       = round($nDiscount * $oResultQueryDT[$i]['FCXrdNet']/$oResultQueryDT[$i]['NETS'],2);
                            }
                        }

                        if(strpos($nProrate,".") !== false){
                            //ถ้ามีทศนิยม
                            $aProrateDecimal = explode(".",$nProrate);
                            $nProrateDecimal = substr($aProrateDecimal[1],0,$nDecimal);
                            $nNewProrate = $aProrateDecimal[0].'.'.$nProrateDecimal;
                        }else{
                            //ถ้าไม่มีทศนิยม
                            $nNewProrate = substr($nProrate,0,$nDecimal);
                        }

                        $aNewArrayProduct = array(
                            'DocumentNumber'    => $oResultQueryDT[$i]['FTXrhDocNo'],
                            'SeqNumber'         => $oResultQueryDT[$i]['FNXrdSeqNo'] , 
                            'ProductNumber'     => $oResultQueryDT[$i]['FTPdtCode'] ,
                            'Value'             => $nNewProrate,
                            'FCXrdNet'          => $oResultQueryDT[$i]['FCXrdNet'] ,
                            'FTXrhVATInOrEx'    => $oResultQueryDT[$i]['FTXrhVATInOrEx'] ,
                            'FCXrhVATRate'      => $oResultQueryDT[$i]['FCXrhVATRate'],
                            'FCXrdVat'          => $oResultQueryDT[$i]['FCXrdVat'] , 
                            'FCXrdVatable'      => $oResultQueryDT[$i]['FCXrdVatable'] ,
                            'FCXrhAftDisChg'    => $oResultQueryDT[$i]['FCXrhAftDisChg']
                        );
                        array_push($aProrateByproduct,$aNewArrayProduct);
                    }else{
                        $aDataReturn    =  array(
                            'rtCode'    => '800',
                            'rtDesc'    => 'Data Not Found',
                        );
                        return $aDataReturn;
                        exit;
                    }
                }

                //ผลรวม prorate มันจะยังไม่ครบจำนวนส่วนลด ต้องเอาไปหยอดตัวสุดท้าย
                $nSumProrate = 0;
                for($j=0; $j<count($aProrateByproduct); $j++){
                    $nSumProrate = $nSumProrate + $aProrateByproduct[$j]['Value'];
                    //ผลรวม prorate ที่เหลือต้องเอาไป + ตัวสุดท้าย
                    if($j == count($aProrateByproduct) - 1){
                        $nDifferenceProrate = $nDiscount - $nSumProrate;
                        $aProrateByproduct[$j]['Value'] = $aProrateByproduct[$j]['Value'] + $nDifferenceProrate;
                    }
                }

                //เอาค่ากลับไป update
                for($k=0; $k<count($aProrateByproduct); $k++){
                    $nProrate       = $aProrateByproduct[$k]['Value'];
                    $tDocumentNo    = $aProrateByproduct[$k]['DocumentNumber'];
                    $nSeqPDT        = $aProrateByproduct[$k]['SeqNumber'];
                    $tCodePDT       = $aProrateByproduct[$k]['ProductNumber']; 

                    //คิด VAT ใหม่
                    $FCXrdNet           = $aProrateByproduct[$k]['FCXrdNet'];
                    $FCXrhVATRate       = $aProrateByproduct[$k]['FCXrhVATRate'];
                    $FTXrhVATInOrEx     = $aProrateByproduct[$k]['FTXrhVATInOrEx'];
                    $FCXrdVat           = $aProrateByproduct[$k]['FCXrdVat'];
                    $FCXrdVatable       = $aProrateByproduct[$k]['FCXrdVatable'];
                    $FCXrhAftDisChg     = $aProrateByproduct[$k]['FCXrhAftDisChg'];

                    //ส่วน VAT , VATABLE ใน DT
                    if($FTXrhVATInOrEx == 2 || $FTXrhVATInOrEx == '2'){ //แยกนอก
                        $nVatable = $FCXrdVatable - $nProrate; 
                        $nVat     = round($nVatable * $FCXrhVATRate / 100, 2);
                    }else{ //รวมใน
                        $nNetCalculate  = $FCXrdNet - $nProrate;
                        $nVat           = round($nNetCalculate * $FCXrhVATRate / (100 + $FCXrhVATRate), 2);
                        $nVatable       = round($nNetCalculate - $nVat,2);
                    }

                    //Update VAT , VATABLE ใน DT
                    $tUpdateProrate = "UPDATE TACTPrDT 
                                       SET FCXrdFootAvg = '$nProrate' ,
                                            FCXrdVat = '$nVat',
                                             FCXrdVatable = '$nVatable',
                                             FTXrdStaPrcStk = 1
                                       WHERE FTXrhDocNo = '$tDocumentNo' AND
                                             FNXrdSeqNo = '$nSeqPDT' AND
                                             FTPdtCode  = '$tCodePDT' ";
                    $oResultProrate = $DB->DB_EXECUTE($tUpdateProrate);

                    //ส่วน  VAT , VATABLE ใน HD
                    if($FTXrhVATInOrEx == 1 || $FTXrhVATInOrEx == '1'){ //รวมใน
                        $tDocNo         = $tDocumentNo;
                        $tSQLSUMVat     = "SELECT SUM(FCXrdVat) AS FCXrdVatReq FROM TACTPrDT WHERE FTXrhDocNo = '$tDocNo' ";
                        $oSUMVat        = $DB->DB_SELECT($tSQLSUMVat);
                        $FCXrdVatReq    = $oSUMVat[0]['FCXrdVatReq'];
                        $FCXrhVatable   = $FCXrhAftDisChg - $FCXrdVatReq;
                    }else{ //แยกนอก
                        $tDocNo         = $tDocumentNo;
                        $tSQLSUMVat     = "SELECT SUM(FCXrdVat) AS FCXrdVatReq FROM TACTPrDT WHERE FTXrhDocNo = '$tDocNo' ";
                        $oSUMVat        = $DB->DB_SELECT($tSQLSUMVat);
                        $FCXrdVatReq    = $oSUMVat[0]['FCXrdVatReq'];
                        $FCXrhVatable   = $FCXrhAftDisChg;
                    }

                    //Update VAT , VATABLE ใน HD
                    $tUpdateHD = "UPDATE TACTPrHD 
                        SET FTXrhStaPrcStk  = 1,
                            FCXrhVat  = '$FCXrdVatReq',
                            FCXrhVatable  = '$FCXrhVatable'
                        WHERE FTXrhDocNo = '$tDocumentNo' ";
                    $DB->DB_EXECUTE($tUpdateHD);
                }

                return $oResultProrate;
            }
        }
    }


?>