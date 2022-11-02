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
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="5%"><?=$aDataTextRef['tRptDivision']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="5%"><?=$aDataTextRef['tRptDepartment']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="5%"><?=$aDataTextRef['tRptCategory']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="9%"><?=$aDataTextRef['tRptSubCategory']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="10%"><?=$aDataTextRef['tRptPdtBarCode']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left"><?=$aDataTextRef['tRptPdtName']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-right" width="9%"><?=$aDataTextRef['tRptSalePrice']?></th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="9%">ที่เก็บ</th>
                        <th rowspan="2" class="xCNRptColumnHeader text-left" width="7%">วันที่</th>
                        <th colspan="2" class="xCNRptColumnHeader text-center">การตรวจนับ</th>
                    </tr>
                    <tr>
                        <th class="xCNRptColumnHeader text-right" width="7%">จำนวน</th>
                        <th class="xCNRptColumnHeader text-right" width="8%">มูลค่าขาย</th>
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
                        <td class="xCNRptDetail text-left text-bold">
                            <?php
                                if(!isset($tPgpLev3ChainDesc) || empty($tPgpLev3ChainDesc)){
                                    $tPgpLev3ChainDesc = $aValue['FTPgpLev3ChainDesc'];
                                    echo $aValue['FTPgpLev3ChainDesc'];
                                }else{
                                    if($tPgpLev3ChainDesc != $aValue['FTPgpLev3ChainDesc']){
                                        $tPgpLev3ChainDesc = $aValue['FTPgpLev3ChainDesc'];
                                        echo $aValue['FTPgpLev3ChainDesc'];
                                    }
                                }
                            ?>
                        </td>
                        <td class="xCNRptDetail text-left text-bold">
                            <?php
                                if(!isset($tPgpLev4ChainDesc) || empty($tPgpLev4ChainDesc)){
                                    $tPgpLev4ChainDesc = $aValue['FTPgpLev4ChainDesc'];
                                    echo $aValue['FTPgpLev4ChainDesc'];
                                }else{
                                    if($tPgpLev4ChainDesc != $aValue['FTPgpLev4ChainDesc']){
                                        $tPgpLev4ChainDesc = $aValue['FTPgpLev4ChainDesc'];
                                        echo $aValue['FTPgpLev4ChainDesc'];
                                    }
                                }
                            ?>
                        </td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTIudBarCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtName']?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudSetPrice'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPlcCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FDIudChkDate']?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudUnitC1'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudSetPrice'] * $aValue['FCIudQtyBal'],$nDecimal)?></td>
                    </tr>

                    <?php if($aValue['RowLev4Chain'] == $aValue['CountLev4Chain']){ ?>
                    <tr>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail"></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold">รวม - Sub Cateory : <?=$aValue['FTPgpLev4ChainDesc']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev4SetPrice'],$nDecimal)?></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev4UnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev4SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>

                    <?php if($aValue['RowLev3Chain'] == $aValue['CountLev3Chain']){ ?>
                    <tr>
                        <td colspan="2" class="xCNRPTSubFooterLine xCNRptDetail"></td>
                        <td colspan="4" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold">รวม - Cateory : <?=$aValue['FTPgpLev3ChainDesc']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev3SetPrice'],$nDecimal)?></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev3UnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev3SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>

                    <?php if($aValue['RowLev2Chain'] == $aValue['CountLev2Chain']){ ?>
                    <tr>
                        <td colspan="1" class="xCNRPTSubFooterLine xCNRptDetail"></td>
                        <td colspan="5" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold">รวม - Department : <?=$aValue['FTPgpLev2ChainDesc']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev2SetPrice'],$nDecimal)?></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev2UnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev2SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>

                    <?php if($aValue['RowLev1Chain'] == $aValue['CountLev1Chain']){ ?>
                    <tr>
                        <td colspan="6" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold">รวม - Division : <?=$aValue['FTPgpLev1ChainDesc']?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev1SetPrice'],$nDecimal)?></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev1UnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aValue['SumLev1SaleValue'],$nDecimal)?></td>
                    </tr>
                    <?php } ?>
            <?php
                    }
            ?>
                    <?php if($aDataReport['nCurrentPage'] == $aDataReport['nAllPage']){ ?>
                    <tr>
                        <td colspan="6" class="xCNRPTSubFooterLine xCNRptDetail text-left text-bold">รวม</td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aDataReport['aItems'][0]['SumAllSetPrice'],$nDecimal)?></td>
                        <td colspan="3" class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aDataReport['aItems'][0]['SumAllUnitC1'],$nDecimal)?></td>
                        <td class="xCNRPTSubFooterLine xCNRptDetail text-right text-bold"><?=number_format($aDataReport['aItems'][0]['SumAllSaleValue'],$nDecimal)?></td>
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