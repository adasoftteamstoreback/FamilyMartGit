<style>
	#otbTableRoundBranch tbody{
		border: 1px solid #dddddd;
	}

	.xCNPURCheckboxRoundBranch{
		margin	: 0px auto;
		position: relative;
		left	: 40%;
	}

	.PurtrRoundBranchClick{
		background-color : #def0d8 !important;
	}

	#otbTableTypeSupplier , #otbTableSupplier{
		border	: 1px solid #dddddd;
	}

	#otbTableTypeSupplier tr td{
		padding		: 5px 5px;
		font-size 	: 11px;
	}

	#otbTableSupplier tr td{
		padding		: 5px 5px;
		font-size 	: 11px;
	}

</style>


<!--modal Round or Branch -->

<div class="modal fade" id="odvModalRoundBranch" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('document/purcn', 'tPUNTitleModalRoundBranch')?></label>
			</div>
			<div class="modal-body">
				<table id="otbTableRoundBranch" class="table table-striped xCNTableHead ">
					<thead>
						<tr>
							<th style="width:20%; text-align: center;"><?=language('document/purcn', 'tPUNModalRoundBranchChoose')?> </th>
							<th style="width:80%; text-align: left;"><?=language('document/purcn', 'tPUNModalRoundBranchReturnPDT')?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="otrRoundBranch" style="cursor:pointer;" data-key="PUR1">
							<td>
								<div class="xCNPURCheckboxRoundBranch">
								<label><input class="xCNCheckPUR1" type="checkbox" value="PUR1"></label>
								</div>
							</td>
							<td><?=language('document/purcn', 'tPUNModalRoundBranchReturnROUND')?></td>
						</tr>

						<tr class="otrRoundBranch" style="cursor:pointer;" data-key="PUR2">
							<td>
								<div class="xCNPURCheckboxRoundBranch">
								<label><input class="xCNCheckPUR2" type="checkbox" value="PUR2"></label>
								</div>
							</td>
							<td><?=language('document/purcn', 'tPUNModalRoundBranchReturnBCH')?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm" onclick="JSvPURSelectRoundBranch()">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Round or Branch -->

<!--modal Supplier -->
<div class="modal fade" id="odvModalSupplier" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?=language('document/purreqcn', 'tPURModalSupplierTitle')?><?=date('d/m/Y')?></label>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="col-lg-12">
						<label><?=language('document/purreqcn', 'tPURModalSupplierSelectTypeSup')?></label>
					</div>
					<div class="col-lg-12">
						<div id="odvContentTypeSupplier">
							<table id="otbTableTypeSupplier" class="table table-striped xCNTableHead ">
								<thead>
									<tr>
										<th style="width:20%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupCode')?> </th>
										<th style="width:80%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupName')?></th>
									</tr>
								</thead>
								<tbody>
									<tr class="otrNoData">
										<td nowrap colspan="7" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
									</tr>
								</tbody>
							</table>
							<input type="hidden" id="ohdPURValueTypeSupplier">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<label><?=language('document/purreqcn', 'tPURModalSupplierSelectFilter')?></label>
						<label id="oliTextFilter">Direct</label>
					</div>
					<div class="col-lg-5">
						<div class="form-group">
							<div class="input-group">
								<input class="form-control oetTextFilter xCNInputWithoutSingleQuote" id="oetPusSearchSup" type="text" value="" onkeypress="Javascript:if(event.keyCode==13 ) JSxGetSupplier('','','1','page')" autocomplete="off" placeholder="กรอกคำค้นหา">
								<span class="input-group-btn">
									<button class="btn xCNBtnSearch" type="button" onclick="JSxGetSupplier('','','1','page')">
										<img src="<?=$tBase_url?>application/modules/common/assets/images/icons/search-24.png"">
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-lg-12">
						<div id="odvContentSupplier">
							<table id="otbTableSupplier" class="table table-striped xCNTableHead ">
								<thead>
									<tr>
										<th style="width:20%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupCodeType')?> </th>
										<th style="width:80%; text-align: left;"><?=language('document/purreqcn', 'tPURModalSupplierSupNameType')?></th>
									</tr>
								</thead>
								<tbody>
									<tr class="otrNoData">
										<td nowrap colspan="7" style="text-align: center; padding: 10px !important; height: 40px; vertical-align: middle;"><?= language('common/systems','tSYSDatanotfound')?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionConfirm" onclick="JSvPURSupplierConfirm();">
					<?php echo language('common/systems', 'tModalConfirm'); ?>
				</button>
				<button type="button" class="btn xCNBTNActionCancel" onclick="JSvPURSupplierClose();" data-dismiss="modal">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal Supplier -->

<!-- modal Supplier is null -->
<div class="modal fade" id="odvModalSupplierisnull" data-backdrop="static" data-keyboard="false" style="overflow: hidden auto; z-index: 7000; display: none;"  data-keyboard="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard">ไม่พบข้อมูลผู้จำหน่าย</label>
			</div>
			<div class="modal-body">
                <span class="xCNTextModal" style="display: inline-block; word-break:break-all">
                    กรุณาเลือกผู้จำหน่าย
                </span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn xCNBTNActionCancel xCNBTNModalSupplierisnull" data-dismiss="modal" id="obtDatabaseNotFound">
					<?php echo language('common/systems', 'tModalCancel'); ?>
				</button>
            </div>
		</div>
	</div>
</div>
<!-- end modal Supplier is null -->

<!--Content Main pur-->
<div id="odvContentMainPUR"></div>

<script src="<?=$tBase_url?>application/modules/common/assets/src/jFormValidate.js?v=<?php echo date("dmyhis"); ?>"></script>
<script>

	$('title').html("Purchase - Credit Note");

	//Modal ระบุประเภทการคืน จะโชว์ครั้งแรก
	$('#odvModalRoundBranch').modal('show');
	$('.xCNCheckPUR1').prop('checked', true);
	$('#otbTableRoundBranch tbody tr:first-child').addClass('PurtrRoundBranchClick');
	$('.otrRoundBranch').click(function(e) {
		$('.otrRoundBranch').removeClass('PurtrRoundBranchClick');
		var tKey = $(this).attr('data-key');
		if(tKey == 'PUR1'){
			$('.xCNCheckPUR1').prop('checked', true);
			$('.xCNCheckPUR2').prop('checked', false);
		}else if(tKey == 'PUR2'){
			$('.xCNCheckPUR1').prop('checked', false);
			$('.xCNCheckPUR2').prop('checked', true);
		}
		$(this).addClass('PurtrRoundBranchClick');
	});

	//เลือกระบุประเภทการคืน
	function JSvPURSelectRoundBranch(){

		sessionStorage.clear();

		//Close modal branch
		$('#odvModalRoundBranch').modal('hide');
		var tRoundBranch = $("input[type='checkbox']:checked").val();

		//Show Supplier
		setTimeout(function(){
			$('#odvModalSupplier').modal('show');
			//Call Type Sup
			var tRountGetTypeSup = '<?=$tROUTE_omnPurCNNew_gettypesupplier?>';
			$.ajax({
				url     : tRountGetTypeSup,
				type    : 'POST',
				success : function(result){
					var tResult = JSON.parse(result);
					if(tResult != false){
						var tHTMLBody;
						$('#otbTableTypeSupplier tbody').html('');
						for(i=0; i<tResult.length; i++){
							tHTMLBody += '<tr style="cursor:pointer;" onclick='+'JSxGetSupplier(this,'+tResult[i].FTStyCode+',1,"main")'+'>';
							tHTMLBody += '<td>' + tResult[i].FTStyCode + '</td>';
							tHTMLBody += '<td>' + tResult[i].FTStyName + '</td>';
							tHTMLBody += '</tr>';
						}
						$('#otbTableTypeSupplier tbody').append(tHTMLBody);

						//Fisrt load
						var tTypeCode = $('#otbTableTypeSupplier tbody tr:first-child td:first-child').text();
						JSxGetSupplier('',tTypeCode,'1','main');
						$('#otbTableTypeSupplier tbody tr:first-child').addClass('PurtrRoundBranchClick');

					}
				}
			});
		}, 500);
		
	}

	//Get Suplier
	function JSxGetSupplier(elem,pnTypeSupCode,nPage,tType){
		if($(elem).find('td:eq(1)').text() == '' || $(elem).find('td:eq(1)').text() == null){
			var tTextGroup = 'Direct';
		}else{
			var tTextGroup = $(elem).find('td:eq(1)').text();
		}
		$('#oliTextFilter').text(tTextGroup);

		//Hightlight
		if(tType == 'main'){
			$('#otbTableTypeSupplier tbody tr').removeClass('PurtrRoundBranchClick');
			$(elem).addClass('PurtrRoundBranchClick');
			$('#oetPusSearchSup').val('');
		}else if(tType == 'page'){
			var tTextGroup = $('#otbTableTypeSupplier').children('tbody').find('tr.PurtrRoundBranchClick').find('td:eq(1)').text();
			$('#oliTextFilter').text(tTextGroup);
		}

		if(pnTypeSupCode == ''){
			var pnTypeSupCode = $('#ohdPURValueTypeSupplier').val();
		}else{
			$('#ohdPURValueTypeSupplier').val(pnTypeSupCode);
			var pnTypeSupCode = $('#ohdPURValueTypeSupplier').val();
		}

		//Call Sup
		var tRountGetSup 	= '<?=$tROUTE_omnPurCNNew_getsupplier?>';
		var tSearchAll 		= $('#oetPusSearchSup').val();
		$.ajax({
			url     : tRountGetSup,
			data 	: { 
				'pnSupCode' : pnTypeSupCode ,
				'tSearchAll': tSearchAll,
				'nPage'		: nPage
			},
			type    : 'POST',
			success : function(result){
				$('#odvContentSupplier').html(result);

				var nLength = $('#otbTableSupplier tbody tr').length;
				if(nLength == 1){
					var bCheckSup = $('#otbTableSupplier tbody tr').hasClass('otrNodataSupplier');
					if(bCheckSup == true){
						//ไม่ต้องทำอะไรเพราะมันไม่เจอข้อมูล
					}else{
						$('#otbTableSupplier tbody tr').removeClass('PurtrRoundBranchClick');
						$('#otbTableSupplier tbody tr:first-child').addClass('PurtrRoundBranchClick');
						var pnCode = $('#otbTableSupplier tbody tr:first-child td:first-child').text();
						$('#ohdPURValueSupplier').val(pnCode);
					}
				}
			}
		});
	}

	//ยืนยัน modal supplier
	function JSvPURSupplierConfirm(){
		if($('#ohdPURValueSupplier').val() == ''){
			$('#odvModalSupplier').modal('hide');
			$('#odvModalSupplierisnull').modal('show');

			$('.xCNBTNModalSupplierisnull').click(function(e) {
				$('#odvModalSupplier').modal('show');
			});
		}else{
			var tRoundBranch 	= $("input[type='checkbox']:checked").val();
			var pnTypeSupCode 	= $('#ohdPURValueTypeSupplier').val();
			var pnSupCode 		= $('#ohdPURValueSupplier').val();

			JSvMainpagePurreq(tRoundBranch,pnTypeSupCode,pnSupCode);
			$('#odvModalSupplier').modal('hide');
		}
	}

	//ปิด modal Supplier
	function JSvPURSupplierClose(){
		$('#odvModalRoundBranch').modal('show');
		$('#odvModalSupplier').modal('hide');
	}

	//Load Main 
	//JSvMainpagePurreq();
	function JSvMainpagePurreq(tRoundBranch,pnTypeSupCode,pnSupCode){
		//ประเภท ตามรอบ หรือ ตามสาขา
		var tRoundBranch 	= (tRoundBranch == undefined ? 'PUR1' : tRoundBranch);
		//PUR1 : ตามรอบ
		//PUR2 : ตามสาขา

		//ประเภทผู้จำหน่าย
		var pnTypeSupCode 	= (pnTypeSupCode == undefined ? '10' : pnTypeSupCode);
		//รหัสผู้จำหน่าย
		var pnSupCode 		= (pnSupCode == undefined ? 'S372' : pnSupCode);
		

		var tRouteContent = '<?=$tROUTE_omnPurCNNew_mainpage?>';
		$.ajax({
			url     : tRouteContent,
			data 	: { 
				'pnSupCode' 	: pnSupCode ,
				'pnTypeSupCode'	: pnTypeSupCode,
				'ptRoundBranch'	: tRoundBranch
			},
			type    : 'POST',
			success : function(result){
				$('#odvContentMainPUR').html(result);
			}
		});
	}
</script>
