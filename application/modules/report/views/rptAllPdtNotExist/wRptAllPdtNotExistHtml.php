<style>
    /*แนวนอน*/
    @media print{
        @page {
            size: A4 landscape;
        }
    }
</style>
<input type="hidden" id="ohdReportTitle" value="<?php echo $aDataTextRef['tTitleReport']; ?>">

<div id="odvRptAllPdtNotExist">
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
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="10%"><?=$aDataTextRef['tRptHhdNo']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="8%"><?=$aDataTextRef['tRptPdtLocation']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="12%"><?=$aDataTextRef['tRptDocNo']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="10%"><?=$aDataTextRef['tRptPdtBarCode']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left"><?=$aDataTextRef['tRptPdtName']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-right" width="7%"><?=$aDataTextRef['tRptSalePrice']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-right" width="7%"><?=$aDataTextRef['tRptCountUnit']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-right" width="7%"><?=$aDataTextRef['tRptSaleValue']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="7%"><?=$aDataTextRef['tRptDateCount']?></th>
    
                    </tr>
                
                </thead>
                <tbody>
            <?php
                if($aDataReport['tCode'] == '1'){
                    foreach($aDataReport['aItems'] as $nKey => $aValue){
            ?>
                    <tr>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTIuhHhdNumber']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPlcCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTIuhDocNo']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtBarCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtName']?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudSetPrice'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudUnitC1'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudSetPrice'] * $aValue['FCIudUnitC1'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FDIudChkDate']?></td>
                    </tr>

            <?php
                    }
            ?>
                    <?php if($aDataReport['nCurrentPage'] == $aDataReport['nAllPage']){ ?>
                    <tr>
                        <td colspan="5" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold"><?=$aDataTextRef['tRptTotal']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumAllFCIudUnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumAllSaleValue'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"></td>
                    
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