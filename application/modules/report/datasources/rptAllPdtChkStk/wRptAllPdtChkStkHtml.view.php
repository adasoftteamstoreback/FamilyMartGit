<?php
    use \koolreport\widgets\koolphp\Table;
    $nCurrentPage   = $this->params['nCurrentPage'];
    $nAllPage       = $this->params['nAllPage'];
    $aDataTextRef   = $this->params['aDataTextRef'];
    $aDataReport    = $this->params['aDataReturn'];
    $aCompanyInfo   = $this->params['aCompanyInfo'];
    $tRptDocNo      = $this->params['tRptDocNo'];
    $nStaPrintPDF   = $this->params['nStaPrintPDF'];
?>

<style>
    /*แนวนอน*/
    @media print{
        @page {
            size: A4 landscape;
        }
    }
</style>

<section id="ostPdf"> <!--class="sheet padding-20mm"-->
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

                <?php FCNvRptHeaderInfo(@$aDataReport['aItems'][0]['FTIuhDocNo']); ?>

            </div>
            
            <div class="xCNContentReport">
                <div id="odvTableKoolReport"><!-- class="table-responsive" -->
                    <?php if(isset($aDataReport['tCode']) &&  !empty($aDataReport['tCode']) && $aDataReport['tCode'] == '1'):?>
                        <?php 
                            $bShowFooter = false;
                            if( $nCurrentPage == $nAllPage ) {
                                $bShowFooter = true;
                            } 
                        ?>
                        <?php
                            $tColumName = "AA";
                            // Check Page Footer Show Total Sum
                                $oOptionKoolRpt = array(
                                    "dataSource"        => $this->dataStore("RptAllPdtChkStk"),
                                    // "showFooter"        => $bShowFooter,
                                    "cssClass"          => array(
                                        "table"         => "table xCNRptTable",
                                        "th"            => "xCNRptColumnHeader",
                                        "td"            => "xCNRptDetail",
                                        "td"            => function($row,$columnName){
                                            if($row['RowLev1Chain'] == 1){
                                                return "xCNRptDetail xCNRPTUnderline";
                                            }else{
                                                return "xCNRptDetail";
                                            }
                                        },
                                        "tf"            => "xCNFoot",
                                    ),
                                    "removeDuplicate" => array(
                                        "FTPgpLev1ChainDesc",
                                        "FTPgpLev2ChainDesc",
                                        "FTPgpLev3ChainDesc",
                                        "FTPgpLev4ChainDesc"
                                    ),
                                    // "fixedHeader"=>true,
                                    // "paging"=>array(
                                    //     "pageSize"=>10,
                                    //     "pageIndex"=>0,
                                    // ),
                                    // "showFooter"        => true,
                                    // "showFooter"=>"bottom",
                                    "columns"           => array(
                                        "FTPgpLev1ChainDesc" => array(
                                            "cssStyle"  => array(
                                                // "tf"    => "padding-left: 10px; font-size: 16px; font-weight: bold",
                                                "td"    => "text-align:left;font-weight: bold;"
                                            ),
                                            "label"     => $aDataTextRef['tRptDivision']
                                            // "footer"    => "sum",
                                            // "footerText"=> "Avg Sale: @value",
                                        ),
                                        "FTPgpLev2ChainDesc" => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left;font-weight: bold;"
                                            ),
                                            "label"     => $aDataTextRef['tRptDepartment']
                                        ),
                                        "FTPgpLev3ChainDesc" => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left;font-weight: bold;"
                                            ),
                                            "label"     => $aDataTextRef['tRptCategory']
                                        ),
                                        "FTPgpLev4ChainDesc" => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left;font-weight: bold;"
                                            ),
                                            "label"     => $aDataTextRef['tRptSubCategory']
                                        ),
                                        "FTPdtCode"             => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left;"
                                            ),
                                            "label"     => $aDataTextRef['tRptPdtCode'],
                                        ),
                                        "FTPdtName"             => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left"
                                            ),
                                            "label"     => $aDataTextRef['tRptPdtName'],
                                        ),
                                        "FTPunName"             => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:left"
                                            ),
                                            "label"     => $aDataTextRef['tRptPunName'],
                                        ),
                                        "FCIudSetPrice" => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:right"
                                            ),
                                            "decimals"  => 2,
                                            "label"     => $aDataTextRef['tRptSalePrice'],
                                        ),
                                        "FCIudUnitC1" => array(
                                            "cssStyle"  => array(
                                                "td"    => "text-align:right; border-bottom : 1px dashed #666 !important;"
                                            ),
                                            "label"     => $aDataTextRef['tRptCountUnit'],
                                        ),
                                            // "label"     => $aDataTextRef['tQty'],
                                        
                                        
                                        // "FCXsdQty"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:left"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tQty'],
                                        
                                        // ),
                                        // "FTPunName"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:left",
                                        //         "th"    => "text-align:left"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tRptUnit'],
                                        
                                        // ),
                                        // "FCXsdDigChg"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:left"
                                        //     ),
                                        // ),
                                        // "FCXsdDis"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:left"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tPdtCode'],
                                        // ),
                                        // "FCXsdSetPrice"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:right",
                                        //         "th"    => "text-align:right"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tRptAverage'],
                                        //     "type"      =>"number",
                                        //     "decimals"  =>2,
                                        
                                        // ),
                                        // "FCXsdNetAfHD"             => array(
                                        //     "cssStyle"  => array(
                                        //         "tf"    => "font-weight: bold;",
                                        //         "td"    => "text-align:left"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tPdtCode'],
                                        // ),
                                    
                                        // "FCXsdQty"             => array(
                                        //     "cssStyle"  => array(
                                        //         "th"    => "text-align:right",
                                        //         "tf"    => "text-align:right; font-size: 16px; font-weight: bold",
                                        //         "td"    => "text-align:right"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tQty'],
                                        //     "type"      =>"number",
                                        //     "decimals"  =>2,
                                        //     "footerText"    => $bShowFooter ? number_format($aSumDataReport['FCXsdSumQty'], 2) : '',
                                        // ),
                                        // "FCXsdDigChg"             => array(
                                        //     "cssStyle"  => array(
                                        //         "th"    => "text-align:right",
                                        //         "tf"    => "text-align:right; font-size: 16px; font-weight: bold",
                                        //         "td"    => "text-align:right"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tSales'],
                                        //     "type"      =>"number",
                                        //     "decimals"  =>2,
                                        //     "footerText"    => $bShowFooter ? number_format($aSumDataReport['FCXsdSumDigChg'], 2) : '',
                                        // ),
                                    
                                        // "FCXsdDis"             => array(
                                        //     "cssStyle"  => array(
                                        //         "th"    => "text-align:right",
                                        //         "tf"    => "text-align:right; font-size: 16px; font-weight: bold",
                                        //         "td"    => "text-align:right"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tDiscount'],
                                        //     "type"      =>"number",
                                        //     "decimals"  =>2,
                                        //     "footerText"    => $bShowFooter ? number_format($aSumDataReport['FCXsdSumDis'], 2) : '',
                                        // ),

                                        // "FCXsdNetAfHD"             => array(
                                        //     "cssStyle"  => array(
                                        //         "th"    => "text-align:right",
                                        //         "tf"    => "text-align:right; font-size: 16px; font-weight: bold",
                                        //         "td"    => "text-align:right"
                                        //     ),
                                        //     "label"     => $aDataTextRef['tTotalsales'],
                                        //     "type"      =>"number",
                                        //     "decimals"  =>2,
                                        //     "footerText"    => $bShowFooter ? number_format($aSumDataReport['FCSumFooter'], 2) : '',
                                        // )
                                        
                                    )
                            
                                );
                            
                            
                            // Create Table Kool Report
                            Table::create($oOptionKoolRpt);
                        ?>
                    <?php else:?>
                            <table class="table">
                                <thead>
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptDivision'];?></th> 
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptDepartment'];?></th>    
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptCategory'];?></th>   
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSubCategory'];?></th>
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptPdtCode'];?></th>
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptPdtName'];?></th> 
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptPunName'];?></th>  
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptSalePrice'];?></th>  
                                    <th nowrap  class="text-center xCNRptColumnHeader"><?php echo $aDataTextRef['tRptCountUnit'];?></th>       
                                </thead>
                                <tbody>
                                    <tr><td class='text-center xCNTextDetail2' colspan='100%'><?=$aDataTextRef['tRptNotFoundData']?></td></tr>
                                </tbody>
                            </table>
                    <?php endif;?>
                </div>
        </div>
    </div>
</section>

<?php
    // require_once 'application/libraries/mPDF/vendor/autoload.php'; 
    // $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
    // $html = ob_get_contents();
    // $head = '
    //         <style>
    //             #odvRptSaleVatInvoiceByBillHtml , .xCNRptTable{
    //                 font-family: "Garuda" !important;//เรียกใช้font Garuda สำหรับแสดงผล ภาษาไทย
    //             }
    //         </style>
    // ';

    // $stylesheet1 = file_get_contents('application/modules/common/assets/css/globalcss/bootstrapV3/bootstrap.css');
    // $stylesheet2 = file_get_contents('application/modules/report/assets/css/localcss/ada.rptlayout.css');

    // $mpdf->WriteHTML($head);
    // $mpdf->WriteHTML($stylesheet1,1);
    // $mpdf->WriteHTML($stylesheet2,1);
    // $mpdf->WriteHTML($html);
    // $mpdf->Output('application/modules/report/assets/exportpdf/filename11.pdf', "F");
    // ob_end_flush();
?>