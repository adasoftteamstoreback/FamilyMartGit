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
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 report-filter">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
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
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnNo']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnPdtStkCode']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnPdtName']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGon1']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGonQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGon2']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGonQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGon3']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGonQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGon4']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGonQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGon5']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnGonQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnTotalQty']?></th>
                        <th class="xCNRptColumnHeader text-center"><?=$aDataTextRef['tRptColumnNewQty']?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
                if($aDataReport['tCode'] == '1'){
                    foreach($aDataReport['aItems'] as $nKey => $aValue){
            ?>
                    <tr>
                        <td class="xCNRptDetail text-center"><?=$aValue['FNIudSeqNo']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTIudStkCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtName']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTGon1']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCGon1Qty']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTGon2']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCGon2Qty']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTGon3']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCGon3Qty']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTGon4']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCGon4Qty']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTGon5']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCGon5Qty']?></td>
                        <td class="xCNRptDetail text-right"><?=$aValue['FCIudTotalQty']?></td>
                        <td class="xCNRptDetail text-right" style="border-bottom: 1px dashed #666 !important;"><?=$aValue['FCIudNewQty']?></td>
                    </tr>
            <?php
                    }
                }else{
            ?>
                    <tr><td class='text-center xCNRptDetail xCNFooterRpt' colspan='100%'><?=$aDataTextRef['tRptNotFoundData']?></td></tr>
            <?php
                }
            ?>
                </tbody>
            </table>

        </div>
    </div>
</div>