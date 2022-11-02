<style>
    /*แนวนอน*/
    @media print{
        @page {
            size: A4 landscape;
        }
    }
</style>

<input type="hidden" id="ohdReportTitle" value="<?php echo $aDataTextRef['tTitleReport']; ?>">
<div id="odvRptSaleVatInvoiceByBillHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            
            <div class="row" style="margin-bottom:10px;">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php 
                        if (isset($aCompanyInfo) && !empty($aCompanyInfo)) {
                            FCNvRptCompanyInfo($aCompanyInfo);
                        } 
                    ?>
                </div>
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 report-filter">
                
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-left">
                                <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php FCNvRptHeaderInfo($tRptDocNo); ?>

        </div>
        <div class="xCNContentReport">
            <table class="table table xCNRptTable">
                <thead>
                    <tr>
                        <th rowspan="3" class="xCNRptColumnHeader text-left" width="15%"><?=$aDataTextRef['tRptPdtBarCode']?></th>
                        <th rowspan="3" class="xCNRptColumnHeader text-left"><?=$aDataTextRef['tRptPdtName']?></th>
                        <th colspan="3" class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptCounting']?></th>
                    </tr>
                    <tr>
                        <th class="xCNRptColumnHeader text-right" width="10%"><?=$aDataTextRef['tRptSalePrice']?></th>
                        <th class="xCNRptColumnHeader text-right" width="10%"><?=$aDataTextRef['tRptQty']?></th>
                        <th class="xCNRptColumnHeader text-right" width="10%"><?=$aDataTextRef['tRptSaleValue']?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if($aDataReport['tCode'] == '1'){
                            foreach($aDataReport['aItems'] as $nKey => $aValue){
                    ?>
                        
                            <?php
                                if(!isset($tPlcCode) || empty($tPlcCode)){
                                    $tPlcCode = $aValue['FTPlcCode'];
                            ?>
                                    <tr><td class="text-bold" colspan="100%"><?=$aDataTextRef['tRptCountingLocation']." : ".$aValue['FTPlcCode']?></td></tr>
                            <?php
                                }else{
                                    if($tPlcCode != $aValue['FTPlcCode']){
                                        $tPlcCode = $aValue['FTPlcCode'];
                            ?>
                                    <tr><td class="text-bold" colspan="100%"><?=$aDataTextRef['tRptCountingLocation']." : ".$aValue['FTPlcCode']?></td></tr>
                            <?php
                                    }
                                }
                            ?>
                            
                        <tr>
                            <td class="xCNRptDetail text-left" style="padding-left:25px;"><?=$aValue['FTIudBarCode']?></td>
                            <td class="xCNRptDetail text-left"><?=$aValue['FTPdtName']?></td>
                            <td class="xCNRptDetail text-right"><?=number_format($aValue['FCPdtRetPri1'],$nDecimal)?></td>
                            <td class="xCNRptDetail text-right"><?=$aValue['FCIudQtyC1']?></td>
                            <td class="xCNRptDetail text-right"><?=number_format($aValue['FCSumPdtRetPri1'],$nDecimal)?></td>
                        </tr>

                        <?php if($aValue['RowPlcCode'] == $aValue['CountPlcCode']){ ?>
                            <tr>
                                <td colspan="2" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold"><?=$aDataTextRef['tRptTotal']." : ".$aValue['FTPlcCode']?></td>
                                <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"></td>
                                <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=$aValue['FCSumAllQtyC1']?></td>
                                <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['FCSumAllPdtRetPri1'],$nDecimal)?></td>
                            </tr>
                        <?php } ?>
                       
                    <?php
                        }
                    ?>

                      

                    <?php
                        }else{
                    ?>
                        <tr><td class='text-center xCNRptDetail xCNFooterRpt' colspan='100%'><?=$aDataTextRef['tRptNotFoundData']?></td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>