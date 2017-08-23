<?php
$invoice_status=array("0"=>"","1"=>"");
$page_id="507";
$page_slug="quick_invoice";
include("connect.php");
$ctable 	= "quick_invoice_info";
$ctable_where = "";
$search_query = "";
$date_filter_query="";
// Get the total number of rows in the table

if(isset($_REQUEST['query']) && $_REQUEST['query']!=""){
	$search_query=trim($db->clean($_REQUEST['query']));
	$ctable_where .= " (
							id LIKE '%".$search_query."%'  OR invoice_serial_no LIKE '%".$search_query."%'  OR customer_name LIKE '%".$search_query."%'	) AND ";
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	$date_filter_query=trim($db->clean($_REQUEST['df']));
	$date_filter_query_ex=explode("-",$date_filter_query);
	$ctable_where = ($ctable_where=="")?" (
							DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."' ) AND ":$ctable_where." AND(
							DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}
$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<div id="quick_invoice_container">	
	 <div class="row">
		<div class="col-md-6">
			<div class="input-group input-large pull-left">
				
				<input class="form-control datetimerange-picker-input" id="quick_stock_adjustment_filter_input" value="<?php echo $date_filter_query; ?>" placeholder="Select Date Range" type="text">
				<span class="input-group-addon datetimerange-picker-btn">
						<i class="fa fa-calendar"></i>
				</span>
				<span class="input-group-btn">
					<button class="btn blue filterBtn" type="button">Filter</button>
				</span>
				
			</div>
			<!-- /input-group -->
		</div>
		<div class="col-md-6">
			<div class="input-group input-large pull-right">
				
				<input class="form-control" id="quick_invoice_search_input" value="<?php echo $search_query; ?>" placeholder="Search" type="text">
				<span class="input-group-btn">
					<button class="btn blue searchBtn" type="button">Search</button>
				</span>
				<span class="input-group-btn">
					<button class="btn red clearBtn" type="button">Clear</button>
				</span>
			</div>
			<!-- /input-group -->
		</div>
	
	</div>	
	<br/>
	<table class="table table-striped table-hover table-bordered quick_invoice_table_class" id="quick_invoice_table_id">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
													<th>Invoice id</th>
													<th>Customer</th>
													<th>Created</th>
													<th>Due Date</th>
													<th>Total Amount</th>
																									
                                                </tr>
                                            </thead>
                                            <tbody>
                                             <?php 
											 if($ctable_r)
											 {
												
												  // In Items there are all objects you need here are keys you will find in this array
													// id|name|mobile_no|birthdate|age|address	
												$count=0;	
												 while($r=mysql_fetch_assoc($ctable_r))
												 {
													 $count++;
													 ?>
													  <tr class="">
														
														<td><?php echo $count; ?></td>
														<td><a href="quick_invoice_reverse_view.php?m=e&iid=<?php echo $r['id']; ?>"><?php echo $r['invoice_serial_no']; ?></a></td>
														<td><?php echo $r['customer_name']; ?></td>
														<td><?php echo ($r['created_date']!="" && $r['created_date']!="0000-00-00")?date("d M Y",strtotime($r['created_date'])):"--"; ?></td>
														<td><?php echo ($r['invoice_due_date']!="" && $r['invoice_due_date']!="0000-00-00")?date("d M Y",strtotime($r['invoice_due_date'])):"--"; ?></td>
														<td><?php echo $r['invoice_grand_total']; ?></td>
													
														</tr>
													<?php
													 
												 }
											 }
											 ?>   
                                            </tbody>
                                        </table>
    <div class="row">
		<div class="col-md-2">
			<div class="dataTables_info">
				<div class="form-group"> 
				<label>Rows Limit:</label>
				<select id="numRecords" class="rowCountSpinner form-control input-sm">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="200" <?php if ($_REQUEST["show"] == 200) { echo ' selected="selected"'; }  ?> >200</option>
					<option value="300" <?php if ($_REQUEST["show"] == 300) { echo ' selected="selected"'; }  ?> >300</option>
				</select>
				</div>
			</div>
		</div>
		<div class="col-md-6 pull-right">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination <?php echo $page_slug	?>_pagination">
				<?php 
				echo $system->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
	</div>
</form>
<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	})
	$(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
</script>