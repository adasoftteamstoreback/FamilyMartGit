<?php

class cBrowser extends Controller {

    public $tBase_url;

    public function __construct() {
        $this->input = new Input();
        include('application/config/config.php');
        $this->tBase_url = $tBase_url;
        session_start(); 
    }

    public function index() {
        $tIDCurrent = '';
        $oOptions   = $_POST['paOptions'];
        $tLocalTime = $_POST['dTimelocalStorage'];
        if ($oOptions != '' || $oOptions != 'undefined'){

            $tOptions = $_POST['tOptions'];

            //Order by 
                // $aOrderBy = $oOptions['GrideView']['OrderBy'];
                // $tOrderBy = "";
            if(empty($oOptions['GrideView']['OrderBy']) || !isset($oOptions['GrideView']['OrderBy'])){
                $tOrderBy = "ASC";
            }else{
                $tOrderBy = implode(',', $oOptions['GrideView']['OrderBy']);
            }

            //Table
                $tMasterTable = $oOptions['Table']['Master'];
                $tMasterPK    = $oOptions['Table']['PK'];

            //Pagination
                //show perpage
                if($oOptions['GrideView']['Perpage'] != ''){ $nPerpage = $oOptions['GrideView']['Perpage']; }else{ $nPerpage = 5; }

                //current page
                if($this->input->post('nCurentPage') != ''){ $nCurentPage = $this->input->post('nCurentPage'); }else{ $nCurentPage = 1; }

                //Total page 
                $nPagination = $this->FCNaHCallLenData($nPerpage,$nCurentPage);

            //Columns
                $aColumns = $oOptions['GrideView']['DataColumns']; 

            //Choose type
                $tChooseType = $oOptions['CallBack']['ReturnType']; 
            
            $DB         =  new Driver_database();
            $tSQL       =  "SELECT top 15000 Result.* FROM (";

            //Distinct
            if(isset($oOptions['GrideView']['DistinctField'])){
                $aDistinct = $oOptions['GrideView']['DistinctField'];
            }else{
                $aDistinct = '';
            }
            if($aDistinct != ''){
                $aColumns = $oOptions['GrideView']['DataColumns']; 
                $tTextResultShow = '';
                for($i=0; $i<count($aColumns); $i++){
                    $tTextShow = Explode('.',$aColumns[$i]);
                    $tTextResultShow .=  'ResultSubquery.'.$tTextShow[1] . ',';

                    //remove ,
                    if($i == count($aColumns)-1){
                        $tResultShow = substr($tTextResultShow,0,-1);
                    }

                    //orderby
                    if($i == $aDistinct[0]){
                        $tOrderByDistinct = $tTextShow[1];
                    }
                }
                $tSQL .= "SELECT ROW_NUMBER() OVER(ORDER BY ResultSubquery.$tOrderByDistinct) AS rtRowID , $tResultShow FROM ( SELECT ";
            }else{
                $tSQL .= "SELECT ROW_NUMBER() OVER(ORDER BY $tOrderBy) AS rtRowID , ";
            }

                // if (is_array($aColumns)){
                //     $tColumns   = implode(',', $aColumns);
                //     $tSQL       .= " $tColumns ";
                // }else{
                //     echo "Error:No column select.";
                //     exit();
                // }

            // Select Column From Options
            if (isset($oOptions['GrideView'])):
                if (isset($oOptions['GrideView']['DataColumns'])):
                    $aColumns = $oOptions['GrideView']['DataColumns']; // Return Column

                    if(empty($aDistinct)){
                        if (is_array($aColumns)){
                            $tColumns = implode(',', $aColumns);
                            $tSQL .= " $tColumns ";
                        }else{
                            echo "Error:No column select.";
                            exit();
                        }
                    }else{
                        if (is_array($aColumns)){
                            $tText = '';
                            for($i=0; $i<count($aColumns); $i++){
                                if(isset($aDistinct[$i])){
                                    $tText .= 'DISTINCT('.$aColumns[$i].')' . ',';
                                }else{
                                    $tText .= $aColumns[$i] . ',';
                                }
                                if($i == count($aColumns)-1){
                                    $tTextResult = substr($tText,0,-1);
                                }
                            }
                            $tSQL .= " $tTextResult ";

                        }else{
                            echo "Error:No column select.";
                            exit();
                        }

                        //echo print_r($aDistinct[0]);
                    }
                else:
                    echo "Error:No column select.";
                    exit();
                endif;

            else:
                echo "Error: No column select.";
                exit();
            endif;
            // end select column

            $tSQL       .= " FROM $tMasterTable "; 

                //join
                if (isset($oOptions['Join']['Table'])){
                    for ($j = 0; $j < count($oOptions['Join']['Table']); $j++) {
                        $tSQL .= " LEFT JOIN " . $oOptions['Join']['Table'][$j] . " On " . $oOptions['Join']['On'][$j] . " ";
                    }
                }

            $tSQL       .= " WHERE 1=1 ";

            if(!empty($aDistinct)){
                //WHERE
                if (isset($oOptions['Where'])):
                    if ($oOptions['Where']['Condition']):
                        for ($w = 0; $w < count($oOptions['Where']['Condition']); $w++) {
                            $tSQL .= " " . $oOptions['Where']['Condition'][$w];
                        }
                    endif;
                endif;
                $tSQL .= ' ) as ResultSubquery '; 
            }else{
                //WHERE
                if (isset($oOptions['Where'])):
                    if ($oOptions['Where']['Condition']):
                        for ($w = 0; $w < count($oOptions['Where']['Condition']); $w++) {
                            $tSQL .= " " . $oOptions['Where']['Condition'][$w];
                        }
                    endif;
                endif;
            }

                // //where
                // if (isset($oOptions['Where'])){
                //     if ($oOptions['Where']['Condition']){
                //         for ($w = 0; $w < count($oOptions['Where']['Condition']); $w++) {
                //             $tSQL .= " " . $oOptions['Where']['Condition'][$w];
                //         }
                //     }
                // }

                $tFilerGride    = $this->input->post('tFilerGride'); // Filter Value
                $tColPk         = $tMasterTable . "." . $tMasterPK;

                // Filter Data From Filter Element
                if ($tFilerGride != null){
                    if ($tFilerGride != null){
                        if(isset($oOptions['GrideView']['SearchLike'])){
                            $tSQL .= " AND ( ";
                            for($i=0;$i<count($oOptions['GrideView']['SearchLike']);$i++){
                                //แก้ไข สามารถเขียนคิวรี่ในการค้นหาได้ โดยใส่ %tFilerGride% แทนค่า Edit By Napat(Jame) 09/09/2019
                                if($i == 0){
                                    $tShwOR = " ";
                                    // $tSQL .= " " . $oOptions['GrideView']['SearchLike'][$i] . " LIKE '%$tFilerGride%' ";
                                }else{
                                    $tShwOR = " OR ";
                                    // $tSQL .= " OR " . $oOptions['GrideView']['SearchLike'][$i] . " LIKE '%$tFilerGride%' ";
                                }
                                if(strpos($oOptions['GrideView']['SearchLike'][$i],"%tFilerGride%")){
                                    $tSQL .= $tShwOR.str_replace("%tFilerGride%","$tFilerGride",$oOptions['GrideView']['SearchLike'][$i]);
                                }else{
                                    $tSQL .= $tShwOR.$oOptions['GrideView']['SearchLike'][$i] . " LIKE '%$tFilerGride%' ";
                                };
                            }
                            $tSQL .= " ) ";
                        }else{
                            echo "กรุณาเพิ่ม SearchLike ใน Browser เพื่อใช้ฟังค์ชั่นการค้นหา";
                        }
                        // $tSQL .= " AND ( $tColPk LIKE '%$tFilerGride%' ";
                        // $oOptions['GrideView']['SearchLike']
                        // for ($fc = 0; $fc < count($oOptions['GrideView']['DataColumns']); $fc++){
                        //     $tFilterCol = $oOptions['GrideView']['DataColumns'][$fc];
                        //     $tSQL .= "  OR $tFilterCol LIKE '%$tFilerGride%' ";
                        // }
                        
                    }else{
                        $tSQL .= " ";
                    }
                }else{
                    $tSQL .= '';
                }

                //Group By
                if(isset($oOptions['GrideView']['GroupBy'])){
                    $tSQL .= " GROUP BY ";
                    $tSQL .= $oOptions['GrideView']['GroupBy'][0];
                    // for ($i = 0; $i < count($oOptions['GrideView']['DataColumns']); $i++){
                    //     $tColumns = explode(' ',$oOptions['GrideView']['DataColumns'][$i]);
                    //     if((count($oOptions['GrideView']['DataColumns'])-1) > $i){
                    //         $tSQL   .= $tColumns[0].",";
                    //     }else{
                    //         $tSQL   .= $tColumns[0];
                    //     }
                    // }
                }

            $tSQL       .= " ) AS Result ";

            //Totalrecord
            $nTotalRecord   = ceil($DB->DB_SELECTCOUNT($tSQL));
            $nTotalPage     = ceil($nTotalRecord / $nPerpage);
            $tSQL           .= " WHERE  Result.rtRowID > " . $nPagination[0] . " AND Result.rtRowID <=" . $nPagination[1];

            $tResult        = $DB->DB_SELECT($tSQL);

            if(isset($oOptions['DebugSQL']) && $oOptions['DebugSQL'] == 'true'){
                echo $tSQL;
            }

            //MODAL HEAD
            $tShowdata  = language('common/systems','tSYSShowdata');
            $tPopupmenu = language($oOptions['Title'][0],$oOptions['Title'][1]);
            $tDataTable = '<div class="modal-header xCNModalHead">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <label class="xCNTextModalHeard">' . $tShowdata  . $tPopupmenu . '</label>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">
                                        <button class="btn xCNBTNActionConfirm xCNConfirmBrowsePDT" onclick=JCNxConfirmSelected("'.$tOptions.'","'.$tLocalTime.'") style="border-color:transparent !important;">'.language('common/systems', 'tModalConfirm').'</button>
                                        <button class="btn xCNBTNActionClose" data-dismiss="modal">' . language('common/systems', 'tModalCancel') . '</button>
                                    </div>
                                </div>
                            </div>';
                
            //MODAL CONTENT
            $tDataTable .= '<div class="xCNModalContent">
                                <div class="row">
                                    
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input class="form-control oetTextFilter oetSearchTable xCNInputWithoutSingleQuote" type="text" value="" onkeypress="Javascript:if(event.keyCode==13 ) JCNxSearchBrowse('."'1',"."'".$tOptions."'".','.$tLocalTime.')" autocomplete="off" placeholder="กรอกคำค้นหา">
                                                <span class="input-group-btn">
                                                    <button class="btn xCNBtnSearch" type="button" onclick="JCNxSearchBrowse('."'1',"."'".$tOptions."'".','.$tLocalTime.')">
                                                        <img src="'.$this->tBase_url.'application/modules/common/assets/images/icons/search-24.png"">
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>';

            $tDataTable .=  '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
            if($nTotalRecord == 0){
                $nHeightTable = 'auto';
             }else if($nTotalRecord <= 8){
                $nHeightTable = 'auto';
             }else{
               $nHeightTable = '300px';
             }
            
            $tDataTable .=  '<div class="table-responsive table-scroll" style="overflow: overlay; height: '.$nHeightTable.' ;">';
            $tDataTable .=  '<table id="otbBrowserList" class="table table-striped xCNTableHead" style="width:100%; border-collapse: collapse !important;">';
            $tDataTable .=  '<thead>';
            $tDataTable .=  '<tr>';

                            //THead
                            for ($c = 0; $c < count($oOptions['GrideView']['DataColumns']); $c++){

                                if(isset($oOptions['GrideView']['ColumnsSize'][$c])){
                                    $nWidth = $oOptions['GrideView']['ColumnsSize'][$c];
                                }else{
                                    $nWidth = '10%';
                                }

                                if(isset($oOptions['GrideView']['DisabledColumns'])){
                                    if ($this->JCNtColDisabled($c, $oOptions['GrideView']['DisabledColumns']) == false) {
                                        $tDataTable .= '<th nowrap style="text-align:center;  border:1px solid #dee2e6; width:'.$nWidth.';">'.language($oOptions['GrideView']['ColumnPathLang'],$oOptions['GrideView']['ColumnKeyLang'][$c]).'</th>';
                                    }
                                }else{
                                    $tDataTable .= '<th nowrap style="text-align:center;  border:1px solid #dee2e6; width:'.$nWidth.';">'.language($oOptions['GrideView']['ColumnPathLang'],$oOptions['GrideView']['ColumnKeyLang'][$c]).'</th>';
                                }

                            };

            $tDataTable .=  '</tr>';
            $tDataTable .=  '</thead>';
            $tDataTable .=  '<tbody>';
                            
                if (isset($oOptions['NextFunc']['ArgReturn'])){
                    $nValue = $oOptions['NextFunc']['ArgReturn'][0];
                    $tDataTable .= "<input type='text' style='display:none' id='ohdCallBackArg' value='" . $nValue . "'" . ">";
                }

                //TBody
                if($nTotalRecord == 0){
                    $tDataTable .= "<tr>";
                    $tDataTable .= "<td colspan='99' style='text-align: center; background:#f9f9f9;'>".language('common/systems','tSYSDatanotfound')."</td>";
                    $tDataTable .= "</tr>";
                }else{
                    foreach ($tResult as $key => $val){

                        $aPackData  = array(
                            'KEY'           => $val['rtRowID'],
                            'KEYPK'         => $val[$tMasterPK],
                            'TIMELOCAL'     => $tLocalTime
                        );
                        for ($c = 0; $c < count($oOptions['GrideView']['DataColumns']); $c++){

                            if(strstr($oOptions['GrideView']['DataColumns'][$c],"AS")){
                                $tData      = explode('AS ', $oOptions['GrideView']['DataColumns'][$c]);
                                $aPackData  = array_merge($aPackData,array(
                                    $tData[1] =>  $val[$tData[1]]
                                ));
                            }else if(strstr($oOptions['GrideView']['DataColumns'][$c],".")){
                                $tData      = explode('.', $oOptions['GrideView']['DataColumns'][$c]);
                                $aPackData  = array_merge($aPackData,array(
                                    $tData[1] =>  $val[$tData[1]]
                                ));
                            }else{
                                $tData      = $oOptions['GrideView']['DataColumns'][$c];
                                $aPackData  = array_merge($aPackData,array(
                                    $tData  =>  $val[$tData]
                                ));
                            }

                            // $tData      = explode('.', $oOptions['GrideView']['DataColumns'][$c]);
                            // $tColumnVal = $tData[1];

                            // $tDataAS        = explode(' ', $tColumnVal);
                            // if(isset($tDataAS[2])){
                            //     $tColumnValAS   = $tDataAS[2];
                            // }
                            
                            // if(isset($tColumnValAS)){
                            //     $aPackData = array_merge($aPackData,array(
                            //         $tColumnValAS =>  $val[$tColumnValAS]
                            //     ));
                            // }else{
                            //     $aPackData = array_merge($aPackData,array(
                            //         $tColumnVal =>  $val[$tColumnVal]
                            //     ));
                            // }

                            $tColumnValAS = NULL;
                            
                        }
                        $aJSONPackData = JSON_encode($aPackData,JSON_HEX_APOS); //JSON_HEX_APOS แก้ปัญหาข้อมูลมี single quotes [By Jame 13/08/62]

                        if($tChooseType == 'M'){
                            //$tNameFunctionClick = 'JCNxPushSelection(this,'.$aJSONPackData.')';
                            $tNameFunctionClickorDBClick = 'JCNxPushSelection(this,'.$aJSONPackData.')';
                        }else{
                            //$tNameFunctionClick = 'JCNxPushSingleSelection(this,'.$aJSONPackData.')';
                            $tNameFunctionClickorDBClick = 'JCNxPushSingleSelection(this,'.$aJSONPackData.')';
                        }

                        $tDataTable .= "<tr class='otrTable".$val[$tMasterPK]."' id='otrTable".$val['rtRowID']."' onclick='$tNameFunctionClickorDBClick' >";
                        for ($c = 0; $c < count($oOptions['GrideView']['DataColumns']); $c++){

                            // $tData      = explode('.', $oOptions['GrideView']['DataColumns'][$c]);
                            // $tColumnVal = $tData[1];

                            // $tDataAS        = explode(' ', $tColumnVal);
                            // if(isset($tDataAS[2])){
                            //     $tColumnValAS   = $tDataAS[2];
                            // }

                            // if(isset($tColumnValAS)){
                            //     $tResultColumn = $val[$tColumnValAS];
                            // }else{
                            //     $tResultColumn = $val[$tColumnVal];
                            // }

                            if(strstr($oOptions['GrideView']['DataColumns'][$c],"AS")){
                                $tData      = explode('AS ', $oOptions['GrideView']['DataColumns'][$c]);
                                $aPackData  = array_merge($aPackData,array(
                                    $tData[1] =>  $val[$tData[1]]
                                ));
                            }else if(strstr($oOptions['GrideView']['DataColumns'][$c],".")){
                                $tData      = explode('.', $oOptions['GrideView']['DataColumns'][$c]);
                                $aPackData  = array_merge($aPackData,array(
                                    $tData[1] =>  $val[$tData[1]]
                                ));
                            }else{
                                $tData      = $oOptions['GrideView']['DataColumns'][$c];
                                $aPackData  = array_merge($aPackData,array(
                                    $tData =>  $val[$tData]
                                ));
                            }

                            $tColumnValAS = NULL;

                            if(strstr($oOptions['GrideView']['DataColumns'][$c],"AS") || strstr($oOptions['GrideView']['DataColumns'][$c],".")){
                                $tValueDataCol = $tData[1];
                            }else{
                                $tValueDataCol = $tData;
                            }

                            if(isset($oOptions['GrideView']['DisabledColumns'])){
                                if ($this->JCNtColDisabled($c, $oOptions['GrideView']['DisabledColumns']) == false) {
                                    $tDataTable .= '<td nowrap>' . $val[$tValueDataCol] . '</td>';
                                }
                            }else{
                                $tDataTable .= '<td nowrap>' . $val[$tValueDataCol] . '</td>';
                            }
                            
                        };
                        $tDataTable .= '</tr>';
                    }
                }

            $tDataTable .=  '</tbody>';
            $tDataTable .=  '</table>';
            $tDataTable .=  '</div>';
            $tDataTable .=  '</div>';

            $tDataTable .= "<div class='col-xs-12 col-md-6 xCNModalFooter'>";
            $tDataTable .= language('common/systems', 'tResultTotalRecord');
            $tDataTable .= " " . number_format($nTotalRecord) . " ";
            $tDataTable .= language('common/systems', 'tRecord');
            $tDataTable .= " ";
            $tDataTable .= language('common/systems', 'tCurrentPage');
            $tDataTable .= " " . ($nCurentPage == "" ? "1" : $nCurentPage) . " / " . $nTotalPage;
            $tDataTable .= "</div>";

            if ($nCurentPage == 1) {
                $nPrvPage       = 1;
                $tDisabledLeft  = 'disabled';
                $tClassBlock    = 'xCNBTNNextpreviousBlock';
            } else {
                $nPrvPage       = $nCurentPage - 1;
                $tDisabledLeft  = '';
                $tClassBlock    = '';
            }

            if ($nCurentPage == $nTotalPage){
                $nNextPage = $nTotalPage;
            }else{
                $nNextPage = $nCurentPage + 1;
            }

            if($nTotalRecord != 0){
                $tDataTable .= "<div class='col-xs-12 col-md-6 text-right xCNModalFooter'>";
                $tDataTable .= '<div class="btn-toolbar pull-right">';
                $tDataTable .= '<button onclick="JCNxSearchBrowse('."'".$nPrvPage."',"."'".$tOptions."'".','.$tLocalTime.')" class="xCNBTNNextprevious btn btn-white btn-sm '.$tClassBlock.'" ' . $tDisabledLeft . '>';
                $tDataTable .= '<i class="fa fa-chevron-left f-s-14 t-plus-1"></i>';
                $tDataTable .= '</button>';

                for ($p = 1; $p <= $nTotalPage; $p++){

                    if ($p == $nCurentPage){ $tActived = "active"; }else{ $tActived = ""; }
                    if ($nCurentPage == 1){
                        $nStartPage = 1;
                        $nEndPage   = 5;
                    }else{
                        if ($nCurentPage == $nTotalRecord){
                            $nStartPage = $nTotalRecord - 3;
                            $nEndPage   = $nTotalRecord;
                        }else{
                            $nStartPage = $nCurentPage - 2;
                            $nEndPage   = $nCurentPage + 2;
                        }
                    }

                    if ($p >= $nStartPage and $p <= $nEndPage){
                        $tDataTable .= '<button onclick="JCNxSearchBrowse('.$p.",'".$tOptions."'".','.$tLocalTime.')" type="button" class="page-item btn xCNBTNNumPagenation ' . $tActived . '">' . $p . '</button>';
                    }
                }

                if ($nCurentPage >= $nTotalPage) {
                    $tDisabledRight = 'disabled';
                } else {
                    $tDisabledRight = '';
                }

                $tDataTable .= '<button onclick="JCNxSearchBrowse('."'".$nNextPage."',"."'". $tOptions."'".','.$tLocalTime.')" class="xCNBTNNextprevious btn btn-white btn-sm" ' . $tDisabledRight . '>';
                $tDataTable .= '<i class="fa fa-chevron-right f-s-14 t-plus-1"></i>';
                $tDataTable .= '</button>';
                $tDataTable .= "</div>";
                $tDataTable .= "</div>";
            }

            $tDataTable .=  '</div>';
            $tDataTable .=  '</div>';
            echo  $tDataTable;
        }else{
            echo 'Error: Invarid oOptions';
        }
    
    }

    function FCNaHCallLenData($pnPerPage, $pnPage){
        $nPerPage = $pnPerPage;
        if (isset($pnPage)) {
            $nPage = $pnPage;
        } else {
            $nPage = 1;
        }
        
        $nRowStart = (($nPerPage * $nPage) - $nPerPage);
        
        $nRowEnd = $nPerPage * $nPage;
        
        $aLenData = array($nRowStart, $nRowEnd);
        return $aLenData;
    }

    private function JCNtColDisabled($pnInx, $paDisable) {
        if (in_array($pnInx, $paDisable)) {
            return true;
        } else {
            return false;
        }
    }
    

    

}


