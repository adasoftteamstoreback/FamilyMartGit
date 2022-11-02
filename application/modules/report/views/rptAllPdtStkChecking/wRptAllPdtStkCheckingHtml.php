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
                        <th class="xCNRptColumnHeader text-left" width="20%"><?=$aDataTextRef['tRptDivision']?></th>
                        <th class="xCNRptColumnHeader text-left"><?=$aDataTextRef['tRptDepartment']?></th>
                        <th class="xCNRptColumnHeader text-right" width="10%"><?=$aDataTextRef['tRptCountNumber']?></th>
                        <th class="xCNRptColumnHeader text-right" width="10%"><?=$aDataTextRef['tRptSumSaleValue']?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
                if($aDataReport['tCode'] == '1'){
                    foreach($aDataReport['aItems'] as $nKey => $aValue){
            ?>
                    <tr>
                        <td class="xCNRptDetail text-left text-bold">
                            <?php
                                if(!isset($tPgpLev1ChainDesc) || empty($tPgpLev1ChainDesc)){
                                    $tPgpLev1ChainDesc = $aValue['FTPgpLev1ChainDesc'];
                                    echo $aValue['FTPgpLev1ChainDesc'];
                                }else{
                                    if($tPgpLev1ChainDesc != $aValue['FTPgpLev1ChainDesc']){
                                        $tPgpLev1ChainDesc = $aValue['FTPgpLev1ChainDesc'];
                                        echo $aValue['FTPgpLev1ChainDesc'];
                                    }
                                }
                            ?>
                        </td>
                        <td class="xCNRptDetail text-left text-bold">
                            <?php
                                if(!isset($tPgpLev2ChainDesc) || empty($tPgpLev2ChainDesc)){
                                    $tPgpLev2ChainDesc = $aValue['FTPgpLev2ChainDesc'];
                                    echo $aValue['FTPgpLev2ChainDesc'];
                                }else{
                                    if($tPgpLev2ChainDesc != $aValue['FTPgpLev2ChainDesc']){
                                        $tPgpLev2ChainDesc = $aValue['FTPgpLev2ChainDesc'];
                                        echo $aValue['FTPgpLev2ChainDesc'];
                                    }
                                }
                            ?>
                        </td>
                        <td class="xCNRptDetail text-right"><?=$aValue['SumLev2QtyC1']?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['SumLev2SaleValue'],$nDecimal)?></td>
                    </tr>

                    <?php if($aValue['RowLev1Chain'] == $aValue['CountLev1Chain']){ ?>
                    <tr>
                        <td colspan="2" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold"><?=$aDataTextRef['tRptSumDivision']?><?=$aValue['FTPgpLev1ChainDesc']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=$aValue['SumLev1QtyC1']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev1SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>
            <?php
                    }
            ?>
                    <?php if($aDataReport['nCurrentPage'] == $aDataReport['nAllPage']){ ?>
                    <tr>
                        <td colspan="2" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold"><?=$aDataTextRef['tRptTotal']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=$aValue['SumAllLev1QtyC1']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumAllLev1SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>
            <?php
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