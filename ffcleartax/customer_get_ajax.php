<?php
$invoice_status=array("0"=>"","1"=>"");
$page_id="507";
$page_slug="quick_invoice_contact";
include("connect.php");
$ctable 	= "quick_invoice_contact";
$ctable_where = "";
$search_query = "";
// Get the total number of rows in the table

if(isset($_REQUEST['query']) && $_REQUEST['query']!=""){
	$search_query=trim($db->clean($_REQUEST['query']));
	$ctable_where .= " (
							id LIKE '%".$search_query."%'  OR nick_name LIKE '%".$search_query."%'  OR gstin LIKE '%".$search_query."%' OR mobile_no LIKE '%".$search_query."%' OR pan_number LIKE '%".$search_query."%'	) AND ";
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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page");
?>
<form action="" name="frm" id="frm" method="post">
	<div id="customer_container">	
	 <div class="row">
		<div class="col-md-12">
			<div class="input-group input-large pull-right">
				
				<input class="form-control" id="customer_search_input" value="<?php echo $search_query; ?>" placeholder="Search" type="text">
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
	<table class="table table-striped table-hover table-bordered customer_table_class" id="quick_invoice_table_id">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
													<th>Name</th>
													<th>GSTIN</th>
													<th>PAN Number</th>
													<th>Mobile Number</th>
													<th>Email</th>
													<th>City</th>
													<th>Action</th>
																										
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
														<td><a class="view-customer-btn" data-cid="<?php echo $r['id']; ?>"><?php echo $r['nick_name']; ?></a></td>
														<td><?php echo $r['gstin']; ?></td>
														<td><?php echo $r['pan_number']; ?></td>
														<td><?php echo $r['mobile_no']; ?></td>
														<td><?php echo $r['email']; ?></td>
														<td><?php echo $r['city']; ?></td>
														<td>
														<a class="edit-customer-btn btn btn-primary" data-cid="<?php echo $r['id'] ?>" data-mode="edit" data-callback=" TableCustomerDatatablesAjax.init" ><i class="fa fa-pencil"></i></a>
														<a class="delete-customer-btn btn btn-danger" data-cid="<?php echo $r['id'] ?>"><i class="fa fa-trash"></i></a>
														</td>
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