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
                        <th class="xCNRptColumnHeader text-left" width="9%"><?=$aDataTextRef['tRptDivision']?></th>
                        <th class="xCNRptColumnHeader text-left" width="9%"><?=$aDataTextRef['tRptDepartment']?></th>
                        <th class="xCNRptColumnHeader text-left" width="9%"><?=$aDataTextRef['tRptCategory']?></th>
                        <th class="xCNRptColumnHeader text-left" width="9%"><?=$aDataTextRef['tRptSubCategory']?></th>
                        <th class="xCNRptColumnHeader text-left" width="6%"><?=$aDataTextRef['tRptPdtCode']?></th>
                        <th class="xCNRptColumnHeader text-left"><?=$aDataTextRef['tRptPdtName']?></th>
                        <th class="xCNRptColumnHeader text-left" width="7%"><?=$aDataTextRef['tRptPunName']?></th>
                        <th class="xCNRptColumnHeader text-right" width="8%"><?=$aDataTextRef['tRptSalePrice']?></th>
                        <th class="xCNRptColumnHeader text-right" width="8%"><?=$aDataTextRef['tRptCountUnit']?></th>
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
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtCode']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPdtName']?></td>
                        <td class="xCNRptDetail text-left"><?=$aValue['FTPunName']?></td>
                        <td class="xCNRptDetail text-right"><?=number_format($aValue['FCIudSetPrice'],$nDecimal)?></td>
                        <td class="xCNRptDetail text-right" style="text-align: right;border-bottom: 1px dashed #666 !important;"><?=number_format($aValue['FCIudUnitC1'],$nDecimal)?></td>
                    </tr>

            <?php
                    }
            ?>
                    <?php if($aDataReport['nCurrentPage'] == $aDataReport['nAllPage']){ ?>
                        <tr><td class='text-center xCNRptDetail xCNFooterRpt' colspan='100%'></td></tr>
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