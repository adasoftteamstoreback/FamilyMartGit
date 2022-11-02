<style>
    #otbTablePURProduct tr td{
        font-size: 12px;
        padding: 5px;
    }

    .xWColorEditinLine{
        background-color: #ffe9df !important; 
    }

    #otbTablePURProduct input[type="text"] {
        font-family: TAHOMA_0;
        font-size: 12px !important;
    }

    .xCNBlockIcon{
        opacity         : 0.5;
        pointer-events  : none;
    }

</style>

<div class="col-lg-12">
    <div class="table-responsive">
        <table id="otbTablePURProduct" class="table xCNTableHead">    
            <thead>
                <tr>
                    <th nowrap style="width:5%;    text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableNumber')?> </th>
                    <!-- <th nowrap style="width:15%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableCodePDT')?></th> -->
                    <th nowrap style="             text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableNamePDT')?></th>
                    <th nowrap style="width:20%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableBarcode')?></th>
                    <th nowrap style="width:10%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableCodeSup')?></th>
                    <th nowrap style="width:5%;    text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableUnit')?></th>
                    <th nowrap style="width:5%;    text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableCount')?></th>
                    <th nowrap style="width:10%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTablePrice')?></th>
                    <!-- <th nowrap style="width:10%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTablePricePer')?></th> -->
                    <!-- <th nowrap style="width:10%;   text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableNet')?></th> -->
                    <th nowrap style="width:5%;    text-align: center; cursor: context-menu;"><?=language('document/purreqcn', 'tPURTableDelete')?></th>

                </tr>
            </thead>
            <tbody>
                <?php if($aDataList['rtCode'] == 1){ ?>
                    <?php foreach($aDataList['raItems'] AS $key=>$aValue){  
                        
                        //packdata
                        $aPackdata = array(
                            'tRouteDelete'      => $tROUTE_omnPurCNNew_delete, 
                            'FTXthDocNo'        => $aValue['FTXthDocNo'],
                            'FNXtdSeqNo'        => $aValue['FNXtdSeqNo'],
                            'FTPdtName'         => $aValue['FTPdtName'],
                            'FTPdtCode'         => $aValue['FTPdtCode'],
                            'FTBchCode'         => $aValue['FTBchCode'],
                        );
                        $aResultdata = JSON_encode($aPackdata);

                        $oEventDelete   = 'JSvPURDelete('.$aResultdata.')';
                    ?>
                        <tr data-docno="<?=$aValue['FTXthDocNo'];?>" data-oldqty="<?=$aValue['FCXtdQty']?>" data-pdt="<?=$aValue['FTPdtCode']?>" data-seq="<?=$aValue['FNXtdSeqNo'];?>" class="xCNTableTrClickActive">    
                            <td nowrap class="text-center"><?=$aValue['FNXtdSeqNo']?></td>
                            <?php if($aValue['FTXthDocType'] == 5){ ?>
                                <!-- <td nowrap class="text-left"><?=$aValue['FTPdtCode']?></td> -->
                            <?php }else{ ?>
                                <?php if($aValue['FTXthStaDoc']  == ''){ ?>
                                    <!-- <td nowrap class="text-right xWColorEditinLine">
                                        <div class="field a-field a-field_a1 page__field" style="padding: 0px;">
                                            <input id="oetFieldFTPdtCode" name="oetFieldFTPdtCode" class="xWPurreqPDTCode inputsChange field__input a-field__input" type="text" style="text-align: left;" value="<?=$aValue['FTPdtCode']?>">
                                        </div>
                                    </td> -->
                                <?php }else{ ?>
                                    <!-- <td nowrap class="text-left"><?=$aValue['FTPdtCode']?></td> -->
                                <?php } ?>
                            <?php } ?>
                            <td nowrap class="text-left"><?=$aValue['FTPdtName']?></td>
                            <td nowrap class="text-left"><?=$aValue['FTXtdBarCode']?></td>
                            <td nowrap class="text-left"><?=$aValue['FTXtdApOrAr']?></td>
                            <td nowrap class="text-left"><?=$aValue['FTPunName']?></td>

                            <?php if($aValue['FTXthStaDoc']  == '' || $aValue['FTXthStaPrcDoc'] == ''){ ?>
                                <td nowrap class="text-right xWColorEditinLine">
                                    <div class="field a-field a-field_a1 page__field" style="padding: 0px;">
                                        <input id="oetFieldFCXtdQty<?=$aValue['FNXtdSeqNo']?>" name="oetFieldFCXtdQty<?=$aValue['FNXtdSeqNo']?>" maxlength='5' class="xCNInputNumberOnly inputsChange field__input a-field__input" type="text" style="text-align: right;" value="<?=$aValue['FCXtdQty']?>" autocomplete="off">
                                    </div>
                                </td>
                            <?php }else{ ?>
                                <td nowrap class="text-right"><?=$aValue['FCXtdQty']?></td>
                            <?php }; ?>

                            <td nowrap class="text-right"><?=number_format($aValue['FCXtdSalePrice'], 2)?></td>
                            <!-- <td nowrap class="text-right" id="oetFieldSetPrice<?=$aValue['FNXtdSeqNo']?>"><?=number_format($aValue['FCXtdSetPrice'], 2)?></td> -->
                            <!-- <td nowrap class="text-right" id="oetFieldFCXtdB4DisChg<?=$aValue['FNXtdSeqNo']?>"><?=number_format($aValue['FCXtdNet'], 2)?></td> -->
                            <?php if($aValue['FTXthStaDoc'] == ''  || $aValue['FTXthStaPrcDoc'] == ''){ 
                                $tStaBlockIcon = '';
                            }else{ 
                                $tStaBlockIcon = 'xCNBlockIcon';
                            } ?>
                            <td style="text-align: center; vertical-align:middle;">                         
                                <img class="xCNIconTable xWIconDelete <?=$tStaBlockIcon?>" src="<?=$tBase_url?>application/modules/common/assets/images/icons/delete.png" onclick='<?=$oEventDelete?>'>
                            </td>
                        </tr>
                    <?php } ?>
                <?php }else{ ?>
                    <tr class="otrNoData">
                        <td nowrap colspan="12" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if($ptRoundBranch == 'PUR2'){ ?>
                <?php 
                    if(empty($aDataList['raItems'])){
                        $tDisplayKeyProduct = 'show';
                    }else{ 
                        if($aDataList['raItems'][0]['FTXthStaPrcDoc'] == ''){ 
                            $tDisplayKeyProduct = 'show';
                        }else{ 
                            $tDisplayKeyProduct = 'hide';
                        }; 
                    } 
                ?>
                    <?php if($tDisplayKeyProduct == 'show'){ ?>
                    <tfoot>
                        <tr>
                            <td>
                                <img class="oimImageInsert xCNImageInsert" src="<?=$tBase_url?>application/modules/common/assets/images/icons/add-circular2.png">
                            </td>
                            <td style="padding: 0px;">
                                <div class="field a-field a-field_a1 page__field xCNInputorBarcode" style="padding: 10px;">
                                    <input class="field__input a-field__input xWInputPdtOrdLot xCNInsertInputPDTorBarcode" id="oetInputCodeorBarcode" name="oetInputCodeorBarcode" placeholder="<?=language('common/systems', 'tPlaceholderProductorbarcode')?>" maxlength="25" autocomplete="off">
                                </div>
                            </td>
                            <td colspan="11"></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-6">
            <input type="hidden" id="ohdAllRow" value="<?=$aDataList['rnAllRow']?>">
            <input type="hidden" id="ohdPageGo" value="<?=$nPage?>">
            <p class="ospTextpagination"><?= language('common/systems','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/systems','tRecord')?> <?= language('common/systems','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
        </div>
        <?php if($aDataList['rtCode'] == 1){ ?>
            <div class="col-md-6">
                <div class="xWPageListPDT btn-toolbar pull-right"> 
                    <?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
                    <button type="button" onclick="JSvClickPageListPDT('previous')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                        <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
                    </button>
                    <?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?> 
                        <?php 
                            if($nPage == $i){ 
                                $tActive        = 'active'; 
                                $tDisPageNumber = 'disabled';
                            }else{ 
                                $tActive        = '';
                                $tDisPageNumber = '';
                            }
                        ?>
                        <button onclick="JSvClickPageListPDT('<?=$i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
                    <?php } ?>
                    <?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
                    <button type="button" onclick="JSvClickPageListPDT('next')" class="xCNBTNNextprevious btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                        <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
                    </button>
                </div>
            </div>
        <?php } ?>
    </div>
</div>


<!--modal เหตุผลเป็นค่าว่าง -->
<div class="modal fade" id="odvModalReasonNull" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadReasonNull')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmDelete" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalReasonNull')?>
                </span>
			</div>
			<div class="modal-footer">
				<button id="osmModalReasonNull" type="button" class="btn xCNBTNActionConfirm" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalMQBtnConfirm'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal เหตุผลเป็นค่าว่าง-->

<!--modal Delete -->
<div class="modal fade" id="odvModalDelete" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalDelete')?></label>
			</div>
			<div class="modal-body">
                <span id="ospConfirmDelete" class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalConfirmDeleteItems')?>
                </span>
                <span id="ospConfirmDeleteValue"></span>
				<input type='hidden' id="ohdConfirmIDDelete">
			</div>
			<div class="modal-footer">
				<button id="osmConfirmSingle" type="button" class="btn xCNBTNActionConfirm">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal delete-->

<!--modal Found Data in table-->
<div class="modal fade" id="odvModalFoundDatainTable" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;" data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadFoundDataDuplicate')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModalDataDuplicate" style="display: inline-block;">
                    <?php echo language('common/systems', 'tModalTextFoundDataDuplicate')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal"  id="obtFoundDatainTable">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Found Data in table-->

<!--modal Product Not Found-->
<div class="modal fade" id="odvModalProductNotFound" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadPDTNotFound')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalHeadPDTDetailNotFound')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal" id="obtDataNotFound">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal product Not Found -->

<!--modal Not found Database -->
<div class="modal fade" id="odvModalDataNotFoundinDatabase" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/systems', 'tModalHeadPDTNotFound')?></label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    <?php echo language('common/systems', 'tModalDatabaseNotFound')?>
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel" data-dismiss="modal" id="obtDatabaseNotFound">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal Not found Database-->

<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<?php include "script/jpurcn.php"; ?>
<script>

    //กด enter ใน modal ไม่พบข้อมูล
    $('#odvModalProductNotFound').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode === 13){
            $('#obtDataNotFound').click();
            $('.xCNInsertInputPDTorBarcode').focus();
        }
    });

    //กด enter ใน modal พบข้อมูลซ้ำ
    $('#odvModalFoundDatainTable').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode === 13){
            $('#obtFoundDatainTable').click();
            $('.xCNInsertInputPDTorBarcode').focus();
        }
    });

    //กด enter ใน modal ไม่พบข้อมูลในฐานข้อมูล
    $('#odvModalDataNotFoundinDatabase').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode === 13){
            $('#obtDatabaseNotFound').click();
        }
    });

    var tSupCode = '<?=$pnSupCode?>';
    var tCheckSupCode = tSupCode.search("-");
    if(tCheckSupCode == -1 ){
        var tResultSupCode = tSupCode;
    }else{
        var tSupCode = tSupCode.split("-");
        var tResultSupCode = tSupCode[0];
    }


    //STEP : 2 เพิ่มสินค้า
    //case : browse
    var tFormatCode = $('#ospDocumentnoValue').text();
    if(tFormatCode == 'PCBCHYY-#######'){
        var tDocumentID = null;
    }else{
        var tDocumentID = "'"+tFormatCode+"'";
    }
    var oCrdBrwCardType = {
        Title 		: ['document/turnoffsuggestorder','tTSOTitlePDT'],
		Table		: {Master:'TCNMPdt',PK:'FTPdtCode'},
		Join		: { 
            Table	: ['TCNMPdtBar'],
            On		: [
                        "TCNMPdtBar.FTPdtCode = TCNMPdt.FTPdtCode "+
                        " LEFT JOIN TACTPtDT ON TACTPtDT.FTPdtCode = TCNMPdt.FTPdtCode AND TACTPtDT.FTXthDocNo = "+tDocumentID+" " +
                        " LEFT JOIN TCNMPdtUnit (NOLOCK) ON TCNMPdtUnit.FTPunCode = TCNMPdt.FTPunCode"
                    ]
		},
		Where 		: {
            Condition : [
                " AND TCNMPdt.FTPdtType IN('1','4')  AND (TCNMPdt.FTPdtStaSet IN('1','2','3'))"+
                " AND (TCNMPdt.FTStyCode= '<?=$pnTypeSupCode?>')  AND (TCNMPdt.FTPdtStaReturn IN('1'))" +
                " AND TACTPtDT.FTPdtCode IS NULL "
                // + " AND (TCNMPdt.FTSplCode LIKE  '" +tResultSupCode+ "%') "
            ]
		},
        GrideView	: {
            ColumnPathLang	: 'document/turnoffsuggestorder',
            ColumnKeyLang	: ['tTSOCodePDT','tTSOBarcodePDT','tTSONamePDT','tTSONameotherPDT','tTSONameotherShortPDT'
                                ,'','','','','','','','','','','','','','','','','','',''],
			DataColumns		: ['TCNMPdt.FTPdtCode','TCNMPdtBar.FTPdtBarCode',
                                'TCNMPdt.FTPdtName','TCNMPdt.FTPdtNameOth',
                                'TCNMPdt.FTPdtNameShort','TCNMPdtBar.FCPdtRetPri1',
                                'TCNMPdt.FTPunCode','TCNMPdtUnit.FTPunName',
                                'TCNMPdt.FTSplCode','TCNMPdt.FCPdtCostStd','TCNMPdt.FTPdtStkCode',
                                'TCNMPdt.FCPdtStkFac','TCNMPdt.FTPdtVatType','TCNMPdt.FTPgpChain',
                                'TCNMPdt.FTPdtSaleType','TCNMPdt.FTPdtStaSet',
                                'TCNMPdt.FTPdtArticle','TCNMPdt.FTDcsCode','TCNMPdt.FTPszCode',
                                'TCNMPdt.FTClrCode',
                                'TCNMPdt.FTPdtNoDis','TCNMPdt.FCPdtLawControl'
                            ],
            ColumnsSize  	: ['10%','15%','15%','15%','15%'
                                ,'','','','','','','','','','','','','','','','','','',''],
            SearchLike      : ['TCNMPdt.FTPdtCode','TCNMPdtBar.FTPdtBarCode',
                                'TCNMPdt.FTPdtName','TCNMPdt.FTPdtNameOth',
                                'TCNMPdt.FTPdtNameShort','TCNMPdtBar.FCPdtRetPri1',
                                'TCNMPdt.FTPunCode','TCNMPdtUnit.FTPunName',
                                'TCNMPdt.FTSplCode','TCNMPdt.FCPdtCostStd','TCNMPdt.FTPdtStkCode',
                                'TCNMPdt.FCPdtStkFac','TCNMPdt.FTPdtVatType','TCNMPdt.FTPgpChain',
                                'TCNMPdt.FTPdtSaleType','TCNMPdt.FTPdtStaSet',
                                'TCNMPdt.FTPdtArticle','TCNMPdt.FTDcsCode','TCNMPdt.FTPszCode',
                                'TCNMPdt.FTClrCode',
                                'TCNMPdt.FTPdtNoDis','TCNMPdt.FCPdtLawControl'
                            ],
            Perpage			: 20,
            DisabledColumns	: ['5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22'],
            OrderBy			: ['TCNMPdt.FTPdtCode'],
            SourceOrder		: "ASC"
        },
        CallBack:{
            ReturnType	: 'S'
        },
        NextFunc:{
            FuncName	: 'JSxPushDataintoTable',
            ArgReturn   : []
        },
        // DebugSQL : true,
    };

    $('.oimImageInsert').click(function(){
        oCrdBrwCardType.CallBack.ReturnType = 'S';
        oCrdBrwCardType.NextFunc.FuncName   = 'JSxPushDataintoTable';
        oCrdBrwCardType.NextFunc.ArgReturn  = [];
        JCNxBrowseData('oCrdBrwCardType');
    });

    var tNameRouteInsertPDT = '<?=$tROUTE_omnPurCNNew_insertpdt?>';
	function JSxPushDataintoTable(elem,ptKey){
        $('#obtSave').removeClass('xCNBTNActionSaveDisable');
        var tFormatCode = $('#ospDocumentnoValue').text();
        if(tFormatCode == 'PCBCHYY-#######'){
            var tDocumentID = '';
        }else{
            var tDocumentID = tFormatCode;
        }
		var aData = JSON.parse(elem);
        var tTypeVat = $('#ohdHiddenTypeVat').val();
		$.ajax({
			url     : tNameRouteInsertPDT,
			data    : { 
                tParamter       : aData ,
                tDocumentID     : tDocumentID ,
                nSeq            : '',
                dDocDate        : $.trim($('.ospDocumentdateValue').text()),
                tTypeVat        : $('#ohdHiddenTypeVat').val(),
                nValueVat       : $('#ohdHiddenVat').val(),
                tSPLCode        : $('#ohdPnSupCode').val(),
                tTypeSPL        : $('#ohdPtRoundBranch').val()
            },
			type    : 'POST',
			success : function(oResult){
                //console.log(oResult);
                var oResult = JSON.parse(oResult);
                if(oResult.tResult == 'success'){
                    $('#ospDocumentnoValue').text(oResult.tFormatCode);

                    var nCountItem      = $('#otbTablePURProduct tbody tr').length;
                    var nAllRow         = '<?=$aDataList['rnAllRow']?>';
                    var nSlotItem       = 10;
                    var nAllSlot        = parseInt(nSlotItem) + 1;
                    var nGoPage         = '<?=$nPage?>';
                    var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                    var nResultCountItem = parseInt(nCountItem) + 1;
                    if(nResultCountItem == nAllSlot){
                        var nGoPage       = nPageTogo + 1;
                        JSxSelectDataintoTablePUR(nGoPage);
                    }else{
                        JSxSelectDataintoTablePUR(nGoPage);
                    }
                    
                    JSvCalculateTotal();
                }else{
                    alert('error');
                }
			}
		});
    }

    //STEP : 2 เพิ่มสินค้า
    //case : input
    $('#oetInputCodeorBarcode').keydown(function(e) {
        var keyCode = e.keyCode || e.which; 
        if(keyCode  === 13){
            InsertPDTorBarcode();
            e.preventDefault();
            return false;
        }
    });

    $('#oetInputCodeorBarcode').change(function(e) { 
        InsertPDTorBarcode();
    });

    //Event -> Input barcode or product - ยังไม่เสร็จ
    function InsertPDTorBarcode(){
        var tNamerouteInsertBarcode = '<?php echo $tROUTE_omnPurCNNew_insertBarcode?>';
        var tFormatCode = $('#ospDocumentnoValue').text();
        //$('#oetInputCodeorBarcode').off();
        if(tFormatCode == 'PCBCHYY-#######'){
            var tDocumentID = '';
        }else{
            var tDocumentID = tFormatCode;
        }

        $.ajax({
            url     : tNamerouteInsertBarcode,
            data    : { 
                tPDTCodeorBarcode   : $('#oetInputCodeorBarcode').val(),
                nSPLCode            : $('#ohdPnSupCode').val(),
                tDocumentID         : tDocumentID,
                nVat                : $('#ohdHiddenVat').val(),
                tStyCode            : $('#ohdPnTypeSupCode').val()
            },
            type    : 'POST',
            success : function(oResult){
                var oResult = JSON.parse(oResult);
                if(oResult.tResult == 'success'){
                    $('#ospDocumentnoValue').text(oResult.tFormatCode);
                    var nCountItem      = $('#otbTablePURProduct tbody tr').length;
                    var nAllRow         = '<?=$aDataList['rnAllRow']?>';
                    var nSlotItem       = 10;
                    var nAllSlot        = parseInt(nSlotItem) + 1;
                    var nGoPage         = '<?=$nPage?>';
                    var nPageTogo       = Math.floor(nAllRow / nSlotItem);
                    var nResultCountItem = parseInt(nCountItem) + 1;
                    if(nResultCountItem == nAllSlot){
                        var nGoPage       = nPageTogo + 1;
                        JSxSelectDataintoTablePUR(nGoPage);
                    }else{
                        JSxSelectDataintoTablePUR(nGoPage);
                    }
                }else if(oResult.tResult == 'DataDuplicate'){
                    // alert('ข้อมูลซ้ำ');
                    $('#odvModalFoundDatainTable').modal('show');
                }else{
                    // alert('ไม่พบบาร์โค๊ด');
                    $('#odvModalProductNotFound').modal('show');
                }
            }
        });
        $('#oetInputCodeorBarcode').val('');
    }

    $('.xCNTableTrClickActive').off('click');
    $('.xCNTableTrClickActive').on('click',function(){
        $(this).parent().find('.xCNTableTrActive').removeClass('xCNTableTrActive')
        if($(this).hasClass('xCNTableTrActive')){
            $(this).removeClass('xCNTableTrActive');
        }else{
            $(this).addClass('xCNTableTrActive');
        }
    });

</script>