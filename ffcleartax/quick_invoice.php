<?php
$page_id                     ="514";
include("connect.php");
$ctableQuickInvoiceInfo="quick_invoice_info";
$ctableQuickInvoiceItem="quick_invoice_item";
if(isset($_POST['save_invoice']))
{
	$customer_id                 =isset($_POST['customer_id'])?$db->clean($_POST['customer_id']):"";
	$customer_name               =isset($_POST['customer_name'])?$db->clean($_POST['customer_name']):"";
	$customer_billing_address    =isset($_POST['customer_billing_address'])?$db->clean($_POST['customer_billing_address']):"";
	$customer_billing_pincode    =isset($_POST['customer_billing_pincode'])?$db->clean($_POST['customer_billing_pincode']):"";
	$customer_billing_city       =isset($_POST['customer_billing_city'])?$db->clean($_POST['customer_billing_city']):"";
	$customer_billing_state      =isset($_POST['customer_billing_state'])?$db->clean($_POST['customer_billing_state']):"";
	$customer_billing_country    =isset($_POST['customer_billing_country'])?$db->clean($_POST['customer_billing_country']):"";
	$customer_shipping_address   =isset($_POST['customer_shipping_address'])?$db->clean($_POST['customer_shipping_address']):"";
	$customer_shipping_pincode   =isset($_POST['customer_shipping_pincode'])?$db->clean($_POST['customer_shipping_pincode']):"";
	$customer_shipping_city      =isset($_POST['customer_shipping_city'])?$db->clean($_POST['customer_shipping_city']):"";
	$customer_shipping_state     =isset($_POST['customer_shipping_state'])?$db->clean($_POST['customer_shipping_state']):"";
	$customer_shipping_country   =isset($_POST['customer_shipping_country'])?$db->clean($_POST['customer_shipping_country']):"";
	$invoice_serial_no           =isset($_POST['invoice_serial_no'])?$db->clean($_POST['invoice_serial_no']):"";
	$invoice_date                =isset($_POST['invoice_date'])?$db->clean($_POST['invoice_date']):"";
	$invoice_reference           =isset($_POST['invoice_reference'])?$db->clean($_POST['invoice_reference']):"";
	$invoice_due_date            =isset($_POST['invoice_due_date'])?date("Y-m-d",strtotime($db->clean($_POST['invoice_due_date']))):"";
	$customer_gstin              =isset($_POST['customer_gstin'])?$db->clean($_POST['customer_gstin']):"";
	$customer_place_of_supply    =isset($_POST['customer_place_of_supply'])?$db->clean($_POST['customer_place_of_supply']):"";
	$invoice_total_taxable_value =isset($_POST['invoice_total_taxable_value'])?$db->clean($_POST['invoice_total_taxable_value']):0;
	$invoice_grand_total         =isset($_POST['invoice_grand_total'])?$db->clean($_POST['invoice_grand_total']):0;
	$invoice_total_cgst          =isset($_POST['invoice_total_cgst'])?$db->clean($_POST['invoice_total_cgst']):0;
	$invoice_total_sgst          =isset($_POST['invoice_total_sgst'])?$db->clean($_POST['invoice_total_sgst']):0;
	$invoice_total_igst          =isset($_POST['invoice_total_igst'])?$db->clean($_POST['invoice_total_igst']):0;
	$invoice_total_cess          =isset($_POST['invoice_total_cess'])?$db->clean($_POST['invoice_total_cess']):0;
	$Items          			 =isset($_POST['line_items'])?$_POST['line_items']:array();
	$created_date                =date("Y-m-d H:i:s");
	$isActive                    =1;
	$isDelete                    =0;

	if(!empty($Items))
	{
		if($customer_id!="" && $customer_name!="" && $invoice_serial_no!="" )
		{
			$columns_invoice                           =array("customer_id", "customer_name", "customer_billing_address", "customer_billing_pincode", "customer_billing_city", "customer_billing_state", "customer_billing_country", "customer_shipping_address", "customer_shipping_pincode", "customer_shipping_city", "customer_shipping_state", "customer_shipping_country", "invoice_serial_no", "invoice_date", "invoice_reference", "invoice_due_date", "customer_gstin", "customer_place_of_supply", "invoice_total_taxable_value", "invoice_grand_total", "invoice_total_cgst", "invoice_total_sgst", "invoice_total_igst", "invoice_total_cess", "created_date", "isDelete", "isActive");
			$value_invoice                             =array($customer_id, $customer_name, $customer_billing_address, $customer_billing_pincode, $customer_billing_city, $customer_billing_state, $customer_billing_country, $customer_shipping_address, $customer_shipping_pincode, $customer_shipping_city, $customer_shipping_state, $customer_shipping_country, $invoice_serial_no, $invoice_date, $invoice_reference, $invoice_due_date, $customer_gstin, $customer_place_of_supply, $invoice_total_taxable_value, $invoice_grand_total, $invoice_total_cgst, $invoice_total_sgst, $invoice_total_igst, $invoice_total_cess, $created_date, $isDelete, $isActive);

			$QuickInvoiceInfoId                        =$db->rp_insert($ctableQuickInvoiceInfo,$value_invoice,$columns_invoice);
			if($QuickInvoiceInfoId!=0)
			{
				try
				{
					foreach($Items as $Item)
					{
						$item_id				           =$Item['id'];
						$item_description		           =$Item['description'];
						if($item_description!="")
						{
							$item_type                         =$Item['gst_type'];
							$item_gst_code                     ="";
							$item_code                 		   ="";
							$item_qty                          =floatval($Item['quantity']);
							$item_unit_price                   =floatval($Item['unit_price']);
							$item_unit_cost                    =0;
							$item_unit_of_messurement          ="";
							$item_discount                     =floatval($Item['discount']);
							$item_note              		   ="";
							$item_taxable_value                =($item_qty*$item_unit_price)-$item_discount;
							$item_cgst_per					   =floatval($Item['cgst_rate']);
							$item_cgst_amount				   =($item_taxable_value*$item_cgst_per)/100;
							$item_sgst_per					   =floatval($Item['sgst_rate']);
							$item_sgst_amount				   =($item_taxable_value*$item_sgst_per)/100;
							$item_igst_per					   =floatval($Item['igst_rate']);
							$item_igst_amount				   =($item_taxable_value*$item_igst_per)/100;
							$item_cess_per				 	   =floatval($Item['cess_rate']);
							$item_cess_amount				   =($item_taxable_value*$item_cess_per)/100;
							$item_subtotal				 	   =($item_taxable_value+$item_cgst_amount+$item_sgst_amount+$item_igst_amount+$item_cess_amount);
							$isDelete                          =0;
							$isActive                          =1;
							$created_date                      =date("Y-m-d H:i:s");
							$columns_invoice_item              =array("invoice_id","item_id", "item_description", "item_type", "item_gst_code", "item_code","item_qty", "item_unit_price", "item_unit_cost", "item_unit_of_messurement", "item_discount", "item_note", "item_taxable_value", "item_cgst_per", "item_cgst_amount", "item_sgst_per", "item_sgst_amount", "item_igst_per", "item_igst_amount", "item_cess_per", "item_cess_amount", "item_subtotal", "isDelete", "isActive", "created_date");
							$value_invoice_item                =array($QuickInvoiceInfoId,$item_id, $item_description, $item_type, $item_gst_code, $item_code,$item_qty, $item_unit_price, $item_unit_cost, $item_unit_of_messurement, $item_discount, $item_note, $item_taxable_value, $item_cgst_per, $item_cgst_amount, $item_sgst_per, $item_sgst_amount, $item_igst_per, $item_igst_amount, $item_cess_per, $item_cess_amount, $item_subtotal, $isDelete, $isActive, $created_date);
							
							$ItemInsertedId=$db->rp_insert($ctableQuickInvoiceItem,$value_invoice_item,$columns_invoice_item,0);
							if($ItemInsertedId!=0)
							{
								$invoice_total_taxable_value+=$item_taxable_value;
								$invoice_total_cgst+=$item_cgst_amount;
								$invoice_total_sgst+=$item_sgst_amount;
								$invoice_total_igst+=$item_igst_amount;
								$invoice_total_cess+=$item_cess_amount;
							}
							else
							{
								throw new Exception("Something went wrong with Item ".$item_description." Error Code CQI105");		
							}
							
						}
						
					}
					
					// Calculate Final Values
					$invoice_grand_total=$invoice_total_taxable_value+$invoice_total_cgst+$invoice_total_sgst+$invoice_total_igst+$invoice_total_cess;
					
					// Update Invoice Details
					$UpdateInvoice=array("invoice_grand_total"=>$invoice_grand_total,
										 "invoice_total_cgst"=>$invoice_total_cgst,
										 "invoice_total_sgst"=>$invoice_total_sgst,
										 "invoice_total_igst"=>$invoice_total_igst,
										 "invoice_total_cess"=>$invoice_total_cess,
										 "invoice_total_taxable_value"=>$invoice_total_taxable_value,
										 );
					$IsQuickInvoiceUpdated=$db->rp_update($ctableQuickInvoiceInfo,$UpdateInvoice,"id='".$QuickInvoiceInfoId."'");
					if($IsQuickInvoiceUpdated)
					{
						$reply=array("ack"=>1,"ack_msg"=>"Invoice saved !!");	
					}
					else
					{
						throw new Exception("Invoice could not be saved try again!! Error Code CQI104");		
													
					}
				}
				catch(Exception $e)
				{
					$reply=array("ack"=>0,"ack_msg"=>$e->getMessage());	
					// Revert All Entries
					$db->rp_delete($ctableQuickInvoiceInfo,"id='".$QuickInvoiceInfoId."'");
					$db->rp_delete($ctableQuickInvoiceItem,"invoice_id='".$QuickInvoiceInfoId."'");
				}												
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Invoice could not be saved try again!!  Error Code CQI103");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Following entry required.<br/>Customer Name And Invoice Serial Number. Error Code CQI102");
		}
		
	}
	else
	{
		$reply=array("ack"=>0,"ack_msg"=>"Select atleast one item before saving invoice!! Error Code CQI101");	
	}
	
	if($reply['ack']==1)
	{
		$system->addMessage($reply['ack_msg'],'success');
		$db->rp_location("quick_invoice_manage.php");
	}
	else
	{
		$system->addMessage($reply['ack_msg'],'error');
	}

}
?>
<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title><?php echo SITETITLE; ?> | Manage Invoice</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <?php include("include_css.php");?>
		<!-- BEGIN PAGE LEVEL PLUGINS -->  	
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		 <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
       <link href="../assets/global/plugins/datatables/plugins/bootstrap/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css" />
       	<style>
		 th, td { white-space: nowrap; }
   
		</style>
		<!-- END PAGE LEVEL STYLES -->
		
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white page-md">
        <div class="page-wrapper">
			<?php include("top.php");?>
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
			   <?php include('sidebar.php'); ?>	
			   <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- END PAGE HEADER-->
                        <div class="row">
							<div class="col-md-12">
								<?php $system->getMessageBlock(); ?>
							</div>
                            
                        </div>						
                        <!-- END PAGE HEADER-->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
								<form name="invoice-form" id="invoice-form" method="POST" action="quick_invoice.php">
								
                                <div class="portlet light portlet-fit bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-file font-red"></i>
                                            <span class="caption-subject font-red sbold uppercase">Invoice</span>
                                        </div>
										<div class="actions">
										
											<div class="btn-group btn-group-devided">
													<button name="options" data-toggle="modal" data-target="#item-modal" class="btn btn-transparent red btn-outline btn-square btn-sm active" id="cancel-invoice-btn" type="button">Cancel</button>
													<button name="options" data-toggle="modal" data-target="#customer-modal" class="btn btn-transparent blue btn-outline btn-square btn-sm active" id="back-invoice-btn" type="button">Back</button>
													<button class="btn btn-transparent green-jungle btn-outline btn-square btn-sm active" name="save_invoice" id="save-invoice-btn" type="submit">Save</button>										
													
													<button name="options" class="btn btn-transparent yellow btn-outline btn-square btn-sm active" id="print-invoice-btn" type="button">Print</button>
													
											</div>
										</div>
                                    </div>
                                    <div class="portlet-body">
									  <div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Invoice Serial Number</b></label>
												<div class="input-group">
													<span class="input-group-addon">
														IN/16-17/
													</span>
													<input class="form-control" placeholder="Serial Number" name="invoice_serial_no" id="invoice_serial_no" type="text" data-validation="required" data-validation-error-msg="Invoice Serial Number Required"> 
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Invoice Date</b></label>
												<div class="input-group">
													<input class="form-control" id="invoice_date" name="invoice_date" id="invoice_date" placeholder="Invoice Date" type="text"> 
													<span class="input-group-addon">
														<i class="fa fa-calendar-check-o"></i>
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Reference</b></label>
												<input class="form-control" placeholder="Reference PO,text etc.." name="invoice_reference" id="invoice_reference" type="text"> 
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Due Date</b></label>
												<div class="input-group">
													<input class="form-control" id="due_date" name="due_date" placeholder="Due Date" type="text"> 
													<span class="input-group-addon">
														<i class="fa fa-calendar-check-o"></i>
													</span>
												</div>
											</div>
										</div>
									  </div>
                                      <div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label><b>Customer Name</b></label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input data-validation="required" data-validation-error-msg="Customer Name Required" class="form-control" placeholder="Customer Name" id="customer_name" name="customer_name" type="text"> 
													<input class="form-control" placeholder="Customer Name" id="customer_id" name="customer_id" type="hidden"> 
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Billing Address</b> &nbsp;<a class="" data-target="#billing-address-modal" data-toggle="modal">Edit</a></label>
												<p class="read-only-billing-address">
												A-402,Ratnam Elegance,<br/>1-Arihant Nagar,<br/>Jamnagar Road,Rajkot-360005.
												</p> 
												<input name="billing_address" type="hidden">
												<input name="billing_pincode" type="hidden">
												<input name="billing_city" type="hidden">
												<input name="billing_state" type="hidden">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Shipping Address</b> &nbsp;<a class="" data-target="#shipping-address-modal" data-toggle="modal">Edit</a></label>
												<p class="read-only-shipping-address">
												A-402,Ratnam Elegance,<br/>1-Arihant Nagar,<br/>Jamnagar Road,Rajkot-360005.
												</p> 
												<input name="shipping_address" type="hidden">
												<input name="shipping_pincode" type="hidden">
												<input name="shipping_city" type="hidden">
												<input name="shipping_state" type="hidden">
											</div>
											<div class="form-group">
												<label class="mt-checkbox">
													<input type="checkbox">Use same as billing address
													<span></span>
												</label>												
											</div>
										</div>
									  </div>
                                      <div class="table-scrollable">
										<table class="table table-bordered table-hover" id="line_items_table">
											<thead>
												<tr>
													<th> # </th>
													<th width="15%"> Item Description </th>
													<th width="15%"> Item Type</th>
													<th  width="15%"> HSN / SAC </th>
													<th  width="15%"> Qty </th>
													<th> Rate/Item (Rs.) </th>
													<th> Discount (Rs.) </th>
													<th> Taxable Value </th>
													<th colspan="2" > CGST</th>
													<th colspan="2"> SGST</th>
													<th colspan="2"> IGST</th>
													<th colspan="2"> CESS</th>
													<th> Total</th>
												</tr>
												<tr>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th> %</th>
													<th>Amt (Rs.)</th>
													<th> %</th>
													<th>Amt (Rs.)</th>
													<th> %</th>
													<th>Amt (Rs.)</th>
													<th> %</th>
													<th>Amt (Rs.)</th>
													<th></th>
													
												</tr>
											</thead>
											<tbody class="line_items">
												<tr>
													<td>1</td>
													<td><input class="form-control quick-item" type="text" name="line_items[0][description]">
													<input class="form-control quick-item" type="hidden" name="line_items[0][id]"></td>
													<td>
													<select id="line_items[0][gst_type]" name="line_items[0][gst_type]" class="form-control text-center">
													<option value="Goods">Goods</option>
													<option value="SERVICES">Services</option>
													</select>
													</td>
													<td><input autocomplete="off" id="line_items[0][gst_code]" name="line_items[0][gst_code]" value="" placeholder="" class="form-control text-center" type="text" data-validation="required" ></td>
													<td><input autocomplete="off" id="line_items[0][quantity]" name="line_items[0][quantity]" value="1" placeholder="" class="form-control text-right numeric-input" type="text"></td>
													<td><input autocomplete="off" id="line_items[0][unit_price]" name="line_items[0][unit_price]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>
													<td><input autocomplete="off" id="line_items[0][discount]" name="line_items[0][discount]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>
													<td> <input autocomplete="off" id="line_items[0][taxable_val]" name="line_items[0][taxable_val]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>
													<td><select id="line_items[0][cgst_rate]" name="line_items[0][cgst_rate]" class="form-control">
														<option value="0">0</option>
														<option value="0.125">0.125</option>
														<option value="1.5">1.5</option>
														<option value="2.5">2.5</option>
														<option value="6">6</option>
														<option value="9">9</option>
														<option value="14">14</option>
													</select>
													</td>
													<td> <input autocomplete="off" id="line_items[0][cgst_val]" name="line_items[0][cgst_val]" value="0" placeholder="" disabled="" class="form-control text-right" type="text"></td>
													<td><select id="line_items[0][sgst_rate]" name="line_items[0][sgst_rate]" class="form-control"><option value="0">0</option><option value="0.125">0.125</option><option value="1.5">1.5</option><option value="2.5">2.5</option><option value="6">6</option><option value="9">9</option><option value="14">14</option></select></td>
													<td> <input autocomplete="off" id="line_items[0][sgst_val]" name="line_items[0][sgst_val]" value="0" placeholder="" disabled="" class="form-control text-right" type="text"></td>
													<td><select id="line_items[0][igst_rate]" name="line_items[0][igst_rate]" class="form-control"><option value="0">0</option><option value=".25">0.25</option><option value="3">3</option><option value="5">5</option><option value="12">12</option><option value="18">18</option><option value="28">28</option></select></td>
													<td>
													<input autocomplete="off" id="line_items[0][igst_val]" name="line_items[0][igst_val]" value="" placeholder="" disabled="" class="form-control text-right" type="text">
													</td>
													<td>
													<input autocomplete="off" id="line_items[0][cess_rate]" name="line_items[0][cess_rate]" value="0" placeholder="" class="form-control text-center" type="text">
													</td>
													<td>
													<input autocomplete="off" id="line_items[0][cess_val]" name="line_items[0][cess_val]" value="0" placeholder="" class="form-control text-right" type="text">
													</td>
													<td>
													<input autocomplete="off" id="line_items[0][total_val]" name="line_items[0][total_val]" value="" placeholder="" disabled="" class="form-control text-right" type="text">
													</td>
												</tr>
											</tbody>
										</table>
										
									  </div>
                                    </div>
                                </div>
								</form>
								<!-- END EXAMPLE TABLE PORTLET-->
                            </div>
                        </div>
                    
                        <!--- MODALS -->
                        <div class="modal  fade in" id="shipping-address-modal" tabindex="-1" role="shipping-address-modal" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h4 class="modal-title">Edit Shipping Address</h4>
									</div>
									<form role="form" name="shipping-address-form" id="shipping-address-form" type="POST">
									<div class="modal-body modal-sm">								
											<div class="form-body">
												<div class="form-group">
													<label>Address</label>
													<textarea placeholder="Address"  name="edit_shipping_address" id="edit_shipping_address" class="form-control">
													</textarea>
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>Pincode</label>
													<input class="form-control" name="edit_shipping_pincode" id="edit_shipping_pincode" placeholder="Pincode" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>City</label>
													<input class="form-control" name="edit_shipping_city" id="edit_shipping_city" placeholder="City" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>State</label>
													<select class="form-control" name="edit_shipping_state" id="edit_shipping_state" placeholder="State" type="text">
														<option value="">Select State</option>
														<option value="ANDAMANANDNICOBARISLANDS">Andaman and Nicobar Islands</option>
														<option value="ANDHRAPRADESH">Andhra Pradesh</option>
														<option value="ARUNACHALPRADESH">Arunachal Pradesh</option>
														<option value="ASSAM">Assam</option>         
														<option value="BIHAR">Bihar</option>         
														<option value="CHANDIGARH">Chandigarh</option>         
														<option value="CHHATISHGARH">Chhattisgarh</option>         
														<option value="DADRANAGARHAVELI">Dadra Nagar Haveli</option>         
														<option value="DAMANDIU">Daman and Diu</option>         
														<option value="DELHI">Delhi</option>         
														<option value="GOA">Goa</option>         
														<option value="GUJARAT">Gujarat</option>         
														<option value="HARYANA">Haryana</option>         
														<option value="HIMACHALPRADESH">Himachal Pradesh</option>         
														<option value="JAMMUKASHMIR">Jammu and Kashmir</option>         
														<option value="JHARKHAND">Jharkhand</option>         
														<option value="KARNATAKA">Karnataka</option>         
														<option value="KERALA">Kerala</option>         
														<option value="LAKHSWADEEP">Lakshadweep</option>         
														<option value="MADHYAPRADESH">Madhya Pradesh</option>         
														<option value="MAHARASHTRA">Maharashtra</option>         
														<option value="MANIPUR">Manipur</option>         
														<option value="MEGHALAYA">Meghalaya</option>         
														<option value="MIZORAM">Mizoram</option>         
														<option value="NAGALAND">Nagaland</option>         
														<option value="ORISSA">Orissa</option>         
														<option value="OUTSIDEINDIA">Outside India</option>         
														<option value="PONDICHERRY">Pondicherry</option>         
														<option value="PUNJAB">Punjab</option>         
														<option value="RAJASTHAN">Rajasthan</option>         
														<option value="SIKKIM">Sikkim</option>         
														<option value="TAMILNADU">Tamil Nadu</option>         
														<option value="TELANGANA">Telangana</option>         
														<option value="TRIPURA">Tripura</option>         
														<option value="UTTARPRADESH">Uttar Pradesh</option>         
														<option value="UTTARAKHAND">Uttarakhand</option>         
														<option value="WESTBENGAL">West Bengal</option>
													</select>
												</div>
											</div>
										
									</div>
									<div class="modal-footer">
										<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
										<button type="button" class="btn green" id="ssa-btn">Save changes</button>
									</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<div class="modal  fade in" id="billing-address-modal" tabindex="-1" role="billing-address-modal" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h4 class="modal-title">Edit Billing Address</h4>
									</div>
									<form role="form" id="billing-address-form" name="billing-address-form" type="POST">
									<div class="modal-body modal-sm">
										
											<div class="form-body">
												<div class="form-group">
													<label>Address</label>
													<textarea placeholder="Address" name="edit_billing_address" id="edit_billing_address" class="form-control">
													</textarea>
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>Pincode</label>
													<input class="form-control" name="edit_billing_pincode" id="edit_billing_pincode" placeholder="Pincode" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>City</label>
													<input class="form-control" name="edit_billing_city" id="edit_billing_city" placeholder="City" type="text"> 
												</div>
											</div>
											<div class="form-body">
												<div class="form-group">
													<label>State</label>
													<select class="form-control" name="edit_billing_state" id="edit_billing_state" placeholder="State" type="text">
														<option value="">Select State</option>
														<option value="ANDAMANANDNICOBARISLANDS">Andaman and Nicobar Islands</option>
														<option value="ANDHRAPRADESH">Andhra Pradesh</option>
														<option value="ARUNACHALPRADESH">Arunachal Pradesh</option>
														<option value="ASSAM">Assam</option>         
														<option value="BIHAR">Bihar</option>         
														<option value="CHANDIGARH">Chandigarh</option>         
														<option value="CHHATISHGARH">Chhattisgarh</option>         
														<option value="DADRANAGARHAVELI">Dadra Nagar Haveli</option>         
														<option value="DAMANDIU">Daman and Diu</option>         
														<option value="DELHI">Delhi</option>         
														<option value="GOA">Goa</option>         
														<option value="GUJARAT">Gujarat</option>         
														<option value="HARYANA">Haryana</option>         
														<option value="HIMACHALPRADESH">Himachal Pradesh</option>         
														<option value="JAMMUKASHMIR">Jammu and Kashmir</option>         
														<option value="JHARKHAND">Jharkhand</option>         
														<option value="KARNATAKA">Karnataka</option>         
														<option value="KERALA">Kerala</option>         
														<option value="LAKHSWADEEP">Lakshadweep</option>         
														<option value="MADHYAPRADESH">Madhya Pradesh</option>         
														<option value="MAHARASHTRA">Maharashtra</option>         
														<option value="MANIPUR">Manipur</option>         
														<option value="MEGHALAYA">Meghalaya</option>         
														<option value="MIZORAM">Mizoram</option>         
														<option value="NAGALAND">Nagaland</option>         
														<option value="ORISSA">Orissa</option>         
														<option value="OUTSIDEINDIA">Outside India</option>         
														<option value="PONDICHERRY">Pondicherry</option>         
														<option value="PUNJAB">Punjab</option>         
														<option value="RAJASTHAN">Rajasthan</option>         
														<option value="SIKKIM">Sikkim</option>         
														<option value="TAMILNADU">Tamil Nadu</option>         
														<option value="TELANGANA">Telangana</option>         
														<option value="TRIPURA">Tripura</option>         
														<option value="UTTARPRADESH">Uttar Pradesh</option>         
														<option value="UTTARAKHAND">Uttarakhand</option>         
														<option value="WESTBENGAL">West Bengal</option>
													</select>
												</div>
											</div>
										
									</div>
									<div class="modal-footer">
										<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
										<button type="button" class="btn green" id="sba-btn">Save changes</button>
									</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<div class="modal  fade in" id="item-modal" tabindex="-1" role="item-modal" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h4 class="modal-title">Add New Item</h4>
									</div>
									<form role="form" name="item-save-form" id="item-save-form" type="POST" >
									<div class="modal-body modal-md">
										
											<div class="form-body">
												<div class="form-group">
													<label>Item description(required)</label>
													<input autocomplete="off" id="description" name="description" value="" placeholder="Enter Item Description" class="form-control" type="text" data-validation="required" data-validation-error-msg="Item description required">
												</div>
											</div>
											<div class="row">
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>Item type(required)</label>
													<select id="gst_type" name="gst_type" class="form-control" data-validation="required" data-validation-error-msg="Item type required"><option value="GOODS">Goods</option><option value="SERVICES">Services</option></select>
												</div>
											</div>
											</div>
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>HSN/SAC code(optional)</label>
													<input id="gst_code" name="gst_code" value="" placeholder="Enter 1 digit to search" class="form-control auto_suggest__input" autocomplete="off" type="search">
												</div>
											</div>
											</div>
											</div>
											<div class="row">
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>Item/SKU code(optional)</label>
													<input autocomplete="off" id="item_code" name="item_code" value="" placeholder="Enter Item Code" class="form-control" type="text">
												</div>
											</div>
											</div>
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>Selling price(optional)</label>
													<input autocomplete="off" id="unit_price" name="unit_price" value="" placeholder="Enter Price" class="form-control" type="text">
												</div>
											</div>
											</div>
											</div>
											<div class="row">
											<div class="col-sm-6">
											
											<div class="form-body">
												<div class="form-group">
													<label>Purchase price(optional)</label>
													<input autocomplete="off" id="unit_cost" name="unit_cost" value="" placeholder="Enter Price" class="form-control" type="text">
												</div>
											</div>
											</div>
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>Unit of measurement(optional)</label>
													<select id="unit_of_measurement" name="unit_of_measurement" class="form-control"><option value="">None</option><option value="boxes">boxes</option><option value="cm">cm</option><option value="crates">crates</option><option value="cu mtr">cu mtr</option><option value="gm">gm</option><option value="kg">kg</option><option value="ltr">ltr</option><option value="metric ton">metric ton</option><option value="ml">ml</option><option value="mm">mm</option><option value="mtr">mtr</option><option value="pallets">pallets</option><option value="pieces">pieces</option><option value="pkts">pkts</option><option value="sheets">sheets</option><option value="sq.cm">sq.cm</option><option value="sq.m">sq.m</option></select> 
												</div>
											</div>
											</div>
											</div>
											
											<div class="row">
											<div class="col-sm-6">
											<div class="form-body">
												<div class="form-group">
													<label>Discount(%)(optional)</label>
													<input autocomplete="off" id="discount" name="discount" value="" placeholder="Enter Discount" class="form-control" type="text">
												</div>
											</div>
											</div>
											
											</div>
											<div class="row">
											<div class="col-sm-12">
											<div class="form-body">
												<div class="form-group">
													<label>Item notes(optional)</label>
													<textarea autocomplete="off" id="notes" name="notes" placeholder="Enter Note" class="form-control"></textarea>
												</div>
											</div>
											</div>
											</div>
										
									</div>
									<div class="modal-footer">
										<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
										<button type="submit" class="btn green" id="submit-item">Save</button>
									</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<div class="modal  fade in" id="customer-modal" tabindex="-1" role="customer-modal" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h4 class="modal-title">Add Customer Or Vendor</h4>
									</div>
									<form role="form" name="customer-save-form" id="customer-save-form" type="POST" >
									<div class="modal-body modal-md">
										
											<div class="row">
												<div class="col-sm-12">
													<div class="form-body">
														<div class="form-group">
															<label>Customer Or Vendor name (required)</label>
															<input autocomplete="off" id="business_name" name="business_name" value="" placeholder="Enter Customer Or Vendor name" class="form-control" type="text" data-validation="required" data-validation-error-msg="Customer or vendor name required">
														</div>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="form-body">
														<div class="form-group">
															<label for="gstin" class="">GSTIN &nbsp;<span class="inline_link"><a href="https://services.gst.gov.in/services/track-provisional-id-status" rel="noopener noreferrer" target="_blank">Find your contact's GSTIN</a></span></label>
															<input autocomplete="off" id="gstin" name="gstin" value="" placeholder="Enter GST identification no." class="form-control" type="text">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="country" class="">Country&nbsp;<span class="input_condition"></span></label><select id="country" name="country" class="form-control"><option value="">Select Country</option><option value="AFGHANISTAN">AFGHANISTAN</option><option value="ALBANIA">ALBANIA</option><option value="ALGERIA">ALGERIA</option><option value="AMERICAN SAMOA">AMERICAN SAMOA</option><option value="ANDORRA">ANDORRA</option><option value="ANGOLA">ANGOLA</option><option value="ANGUILLA">ANGUILLA</option><option value="ANTARCTICA">ANTARCTICA</option><option value="ANTIGUA AND BARBUDA">ANTIGUA AND BARBUDA</option><option value="ARGENTINA">ARGENTINA</option><option value="ARMENIA">ARMENIA</option><option value="ARUBA">ARUBA</option><option value="AUSTRALIA">AUSTRALIA</option><option value="AUSTRIA">AUSTRIA</option><option value="AZERBAIJAN">AZERBAIJAN</option><option value="BAHAMAS">BAHAMAS</option><option value="BAHRAIN">BAHRAIN</option><option value="BANGLADESH">BANGLADESH</option><option value="BARBADOS">BARBADOS</option><option value="BELARUS">BELARUS</option><option value="BELGIUM">BELGIUM</option><option value="BELIZE">BELIZE</option><option value="BENIN">BENIN</option><option value="BERMUDA">BERMUDA</option><option value="BHUTAN">BHUTAN</option><option value="BOLIVIA">BOLIVIA</option><option value="BONAIRE">BONAIRE</option><option value="BOSNIA AND HERZEGOVINA">BOSNIA AND HERZEGOVINA</option><option value="BOTSWANA">BOTSWANA</option><option value="BOUVET ISLAND">BOUVET ISLAND</option><option value="BRAZIL">BRAZIL</option><option value="BRUNEI">BRUNEI</option><option value="BULGARIA">BULGARIA</option><option value="BURKINA FASO">BURKINA FASO</option><option value="BURUNDI">BURUNDI</option><option value="CABO VERDE">CABO VERDE</option><option value="CAMBODIA">CAMBODIA</option><option value="CAMEROON">CAMEROON</option><option value="CANADA">CANADA</option><option value="CENTRAL AFRICAN REPUBLIC">CENTRAL AFRICAN REPUBLIC</option><option value="CHAD">CHAD</option><option value="CHILE">CHILE</option><option value="CHINA">CHINA</option><option value="COLOMBIA">COLOMBIA</option><option value="COMOROS">COMOROS</option><option value="CONGO">CONGO</option><option value="COSTA RICA">COSTA RICA</option><option value="CROATIA">CROATIA</option><option value="CUBA">CUBA</option><option value="CURAÇAO">CURAÇAO</option><option value="CYPRUS">CYPRUS</option><option value="CZECHIA">CZECHIA</option><option value="CÔTE DIVOIRE">CÔTE DIVOIRE</option><option value="DEMOCRATIC REPUBLIC OF THE CONGO">DEMOCRATIC REPUBLIC OF THE CONGO</option><option value="DENMARK">DENMARK</option><option value="DJIBOUTI">DJIBOUTI</option><option value="DOMINICA">DOMINICA</option><option value="DOMINICAN REPUBLIC">DOMINICAN REPUBLIC</option><option value="ECUADOR">ECUADOR</option><option value="EGYPT">EGYPT</option><option value="EL SALVADOR">EL SALVADOR</option><option value="EQUATORIAL GUINEA">EQUATORIAL GUINEA</option><option value="ERITREA">ERITREA</option><option value="ESTONIA">ESTONIA</option><option value="ETHIOPIA">ETHIOPIA</option><option value="FAROE ISLANDS">FAROE ISLANDS</option><option value="FIJI">FIJI</option><option value="FINLAND">FINLAND</option><option value="FRANCE">FRANCE</option><option value="FRENCH GUIANA">FRENCH GUIANA</option><option value="FRENCH POLYNESIA">FRENCH POLYNESIA</option><option value="GABON">GABON</option><option value="GAMBIA">GAMBIA</option><option value="GEORGIA">GEORGIA</option><option value="GERMANY">GERMANY</option><option value="GHANA">GHANA</option><option value="GIBRALTAR">GIBRALTAR</option><option value="GREECE">GREECE</option><option value="GREENLAND">GREENLAND</option><option value="GRENADA">GRENADA</option><option value="GUADELOUPE">GUADELOUPE</option><option value="GUAM">GUAM</option><option value="GUATEMALA">GUATEMALA</option><option value="GUERNSEY">GUERNSEY</option><option value="GUINEA">GUINEA</option><option value="GUINEA-BISSAU">GUINEA-BISSAU</option><option value="GUYANA">GUYANA</option><option value="HAITI">HAITI</option><option value="HOLY SEE">HOLY SEE</option><option value="HONDURAS">HONDURAS</option><option value="HONG KONG">HONG KONG</option><option value="HUNGARY">HUNGARY</option><option value="ICELAND">ICELAND</option><option value="INDIA">INDIA</option><option value="INDONESIA">INDONESIA</option><option value="IRAN">IRAN</option><option value="IRAQ">IRAQ</option><option value="IRELAND">IRELAND</option><option value="ISRAEL">ISRAEL</option><option value="ITALY">ITALY</option><option value="JAMAICA">JAMAICA</option><option value="JAPAN">JAPAN</option><option value="JERSEY">JERSEY</option><option value="JORDAN">JORDAN</option><option value="KAZAKHSTAN">KAZAKHSTAN</option><option value="KENYA">KENYA</option><option value="KIRIBATI">KIRIBATI</option><option value="KUWAIT">KUWAIT</option><option value="KYRGYZSTAN">KYRGYZSTAN</option><option value="LAOS">LAOS</option><option value="LATVIA">LATVIA</option><option value="LEBANON">LEBANON</option><option value="LESOTHO">LESOTHO</option><option value="LIBERIA">LIBERIA</option><option value="LIBYA">LIBYA</option><option value="LIECHTENSTEIN">LIECHTENSTEIN</option><option value="LITHUANIA">LITHUANIA</option><option value="LUXEMBOURG">LUXEMBOURG</option><option value="MACAO">MACAO</option><option value="MACEDONIA">MACEDONIA</option><option value="MADAGASCAR">MADAGASCAR</option><option value="MALAWI">MALAWI</option><option value="MALAYSIA">MALAYSIA</option><option value="MALDIVES">MALDIVES</option><option value="MALI">MALI</option><option value="MALTA">MALTA</option><option value="MARSHALL ISLANDS">MARSHALL ISLANDS</option><option value="MARTINIQUE">MARTINIQUE</option><option value="MAURITANIA">MAURITANIA</option><option value="MAURITIUS">MAURITIUS</option><option value="MAYOTTE">MAYOTTE</option><option value="MEXICO">MEXICO</option><option value="MICRONESIA">MICRONESIA</option><option value="MOLDOVA">MOLDOVA</option><option value="MONACO">MONACO</option><option value="MONGOLIA">MONGOLIA</option><option value="MONTENEGRO">MONTENEGRO</option><option value="MONTSERRAT">MONTSERRAT</option><option value="MOROCCO">MOROCCO</option><option value="MOZAMBIQUE">MOZAMBIQUE</option><option value="MYANMAR">MYANMAR</option><option value="NAMIBIA">NAMIBIA</option><option value="NAURU">NAURU</option><option value="NEPAL">NEPAL</option><option value="NETHERLANDS">NETHERLANDS</option><option value="NEW CALEDONIA">NEW CALEDONIA</option><option value="NEW ZEALAND">NEW ZEALAND</option><option value="NICARAGUA">NICARAGUA</option><option value="NIGER">NIGER</option><option value="NIGERIA">NIGERIA</option><option value="NIUE">NIUE</option><option value="NORFOLK ISLAND">NORFOLK ISLAND</option><option value="NORTH KOREA">NORTH KOREA</option><option value="NORTHERN MARIANA ISLANDS">NORTHERN MARIANA ISLANDS</option><option value="NORWAY">NORWAY</option><option value="OMAN">OMAN</option><option value="PAKISTAN">PAKISTAN</option><option value="PALAU">PALAU</option><option value="PALESTINE">PALESTINE</option><option value="PANAMA">PANAMA</option><option value="PAPUA NEW GUINEA">PAPUA NEW GUINEA</option><option value="PARAGUAY">PARAGUAY</option><option value="PERU">PERU</option><option value="PHILIPPINES">PHILIPPINES</option><option value="POLAND">POLAND</option><option value="PORTUGAL">PORTUGAL</option><option value="PUERTO RICO">PUERTO RICO</option><option value="QATAR">QATAR</option><option value="ROMANIA">ROMANIA</option><option value="RUSSIA">RUSSIA</option><option value="RWANDA">RWANDA</option><option value="RÉUNION">RÉUNION</option><option value="SAINT KITTS AND NEVIS">SAINT KITTS AND NEVIS</option><option value="SAINT LUCIA">SAINT LUCIA</option><option value="SAINT VINCENT AND THE GRENADINES">SAINT VINCENT AND THE GRENADINES</option><option value="SAMOA">SAMOA</option><option value="SAN MARINO">SAN MARINO</option><option value="SAO TOME AND PRINCIPE">SAO TOME AND PRINCIPE</option><option value="SAUDI ARABIA">SAUDI ARABIA</option><option value="SENEGAL">SENEGAL</option><option value="SERBIA">SERBIA</option><option value="SEYCHELLES">SEYCHELLES</option><option value="SIERRA LEONE">SIERRA LEONE</option><option value="SINGAPORE">SINGAPORE</option><option value="SINT MAARTEN">SINT MAARTEN</option><option value="SLOVAKIA">SLOVAKIA</option><option value="SLOVENIA">SLOVENIA</option><option value="SOLOMON ISLANDS">SOLOMON ISLANDS</option><option value="SOMALIA">SOMALIA</option><option value="SOUTH AFRICA">SOUTH AFRICA</option><option value="SOUTH KOREA">SOUTH KOREA</option><option value="SOUTH SUDAN">SOUTH SUDAN</option><option value="SPAIN">SPAIN</option><option value="SRI LANKA">SRI LANKA</option><option value="SUDAN">SUDAN</option><option value="SURINAME">SURINAME</option><option value="SWAZILAND">SWAZILAND</option><option value="SWEDEN">SWEDEN</option><option value="SWITZERLAND">SWITZERLAND</option><option value="SYRIA">SYRIA</option><option value="TAIWAN">TAIWAN</option><option value="TAJIKISTAN">TAJIKISTAN</option><option value="TANZANIA">TANZANIA</option><option value="THAILAND">THAILAND</option><option value="TIMOR-LESTE">TIMOR-LESTE</option><option value="TOGO">TOGO</option><option value="TOKELAU">TOKELAU</option><option value="TONGA">TONGA</option><option value="TRINIDAD AND TOBAGO">TRINIDAD AND TOBAGO</option><option value="TUNISIA">TUNISIA</option><option value="TURKEY">TURKEY</option><option value="TURKMENISTAN">TURKMENISTAN</option><option value="TUVALU">TUVALU</option><option value="UGANDA">UGANDA</option><option value="UKRAINE">UKRAINE</option><option value="UNITED ARAB EMIRATES">UNITED ARAB EMIRATES</option><option value="UNITED KINGDOM">UNITED KINGDOM</option><option value="UNITED STATES OF AMERICA">UNITED STATES OF AMERICA</option><option value="URUGUAY">URUGUAY</option><option value="UZBEKISTAN">UZBEKISTAN</option><option value="VANUATU">VANUATU</option><option value="VENEZUELA">VENEZUELA</option><option value="VIETNAM">VIETNAM</option><option value="YEMEN">YEMEN</option><option value="ZAMBIA">ZAMBIA</option><option value="ZIMBABWE">ZIMBABWE</option><option value="ÅLAND ISLANDS">ÅLAND ISLANDS</option></select>
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="state" class="">State&nbsp;<span class="input_condition">(<!-- /react-text --><!-- react-text: 3383 -->required<!-- /react-text --><!-- react-text: 3384 -->)<!-- /react-text --></span></label>
															<select id="state" name="state" class="form-control" data-validation="required" data-validation-error-msg="State required">
															<option value="">Select State</option><option value="ANDAMANANDNICOBARISLANDS">Andaman and Nicobar Islands</option><option value="ANDHRAPRADESH">Andhra Pradesh</option><option value="ARUNACHALPRADESH">Arunachal Pradesh</option><option value="ASSAM">Assam</option><option value="BIHAR">Bihar</option><option value="CHANDIGARH">Chandigarh</option><option value="CHHATISHGARH">Chhattisgarh</option><option value="DADRANAGARHAVELI">Dadra Nagar Haveli</option><option value="DAMANDIU">Daman and Diu</option><option value="DELHI">Delhi</option><option value="GOA">Goa</option><option value="GUJARAT">Gujarat</option><option value="HARYANA">Haryana</option><option value="HIMACHALPRADESH">Himachal Pradesh</option><option value="JAMMUKASHMIR">Jammu and Kashmir</option><option value="JHARKHAND">Jharkhand</option><option value="KARNATAKA">Karnataka</option><option value="KERALA">Kerala</option><option value="LAKHSWADEEP">Lakshadweep</option><option value="MADHYAPRADESH">Madhya Pradesh</option><option value="MAHARASHTRA">Maharashtra</option><option value="MANIPUR">Manipur</option><option value="MEGHALAYA">Meghalaya</option><option value="MIZORAM">Mizoram</option><option value="NAGALAND">Nagaland</option><option value="ORISSA">Orissa</option><option value="OUTSIDEINDIA">Outside India</option><option value="PONDICHERRY">Pondicherry</option><option value="PUNJAB">Punjab</option><option value="RAJASTHAN">Rajasthan</option><option value="SIKKIM">Sikkim</option><option value="TAMILNADU">Tamil Nadu</option><option value="TELANGANA">Telangana</option><option value="TRIPURA">Tripura</option><option value="UTTARPRADESH">Uttar Pradesh</option><option value="UTTARAKHAND">Uttarakhand</option><option value="WESTBENGAL">West Bengal</option></select>
														</div>
													</div>
												</div>	
											</div>	
											<div class="row">
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="contact_person_name" class=""><!-- react-text: 3427 -->Contact person &nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3429 -->(<!-- /react-text --><!-- react-text: 3430 -->optional<!-- /react-text --><!-- react-text: 3431 -->)<!-- /react-text --></span></label><input autocomplete="off" id="contact_person_name" name="contact_person_name" value="" placeholder="Enter Name" class="form-control" type="text">
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="mobile_number" class=""><!-- react-text: 3436 -->Mobile no.<!-- /react-text -->&nbsp;<span class="input_condition"><!-- react-text: 3438 -->(<!-- /react-text --><!-- react-text: 3439 -->optional<!-- /react-text --><!-- react-text: 3440 -->)<!-- /react-text --></span></label><input autocomplete="off" id="mobile_number" name="mobile_number" value="" placeholder="Enter Mobile No." class="form-control" type="text">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="pan_number" class=""><!-- react-text: 3445 -->PAN&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3447 -->(<!-- /react-text --><!-- react-text: 3448 -->optional<!-- /react-text --><!-- react-text: 3449 -->)<!-- /react-text --></span></label><input autocomplete="off" id="pan_number" name="pan_number" value="" placeholder="Enter PAN no." class="form-control" type="text">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="form-body">
														<div class="form-group">
															<label for="address" class=""><!-- react-text: 3455 -->Address&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3457 -->(<!-- /react-text --><!-- react-text: 3458 -->optional<!-- /react-text --><!-- react-text: 3459 -->)<!-- /react-text --></span></label><textarea autocomplete="off" id="address" name="address" placeholder="Enter full address" class="form-control"></textarea>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="zip_code" class=""><!-- react-text: 3464 -->Pincode&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3466 -->(<!-- /react-text --><!-- react-text: 3467 -->optional<!-- /react-text --><!-- react-text: 3468 -->)<!-- /react-text --></span></label><input autocomplete="off" id="zip_code" name="zip_code" value="" placeholder="Enter Pincode" class="form-control" type="text">
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="city" class=""><!-- react-text: 3473 -->City&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3475 -->(<!-- /react-text --><!-- react-text: 3476 -->optional<!-- /react-text --><!-- react-text: 3477 -->)<!-- /react-text --></span></label><input autocomplete="off" id="city" name="city" value="" placeholder="Enter City" class="form-control" type="text">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="email" class=""><!-- react-text: 3482 -->Email id&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3484 -->(<!-- /react-text --><!-- react-text: 3485 -->optional<!-- /react-text --><!-- react-text: 3486 -->)<!-- /react-text --></span></label><input autocomplete="off" id="email" name="email" value="" placeholder="Enter Email Id" class="form-control" type="text">
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-body">
														<div class="form-group">
															<label for="land_line_number" class=""><!-- react-text: 3491 -->Landline no.&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3493 -->(<!-- /react-text --><!-- react-text: 3494 -->optional<!-- /react-text --><!-- react-text: 3495 -->)<!-- /react-text --></span></label><input autocomplete="off" id="land_line_number" name="land_line_number" value="" placeholder="Enter Landline No." class="form-control" type="text">
														</div>
													</div>
												</div>
											</div>
													
										
									</div>
									<div class="modal-footer">
										<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
										<button type="submit" class="btn green" id="submit-customer">Save changes</button>
									</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						
						<!--- MODALS -->
						
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
                 <?php include('rightbar.php'); ?>
            </div>
            <!-- END CONTAINER -->
             <?php include('footer.php'); ?>
        </div>
        <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
		
		<?php include('include_js.php');?>
 
 <!-- START PAGELEVEL JS -->
  <!-- <script src="js/jquery.autocomplete.js"></script>-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/dataTables.fixedColumns.min.js" type="text/javascript"></script>        
<script src="../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        
<script>
$(function(){
	 $("body").find(".numeric-input").numeric();
	 $.validate({
		form : '#invoice-form', 
  	    onSuccess : function($form) {
		  alert('The form '+$form.attr('id')+' is valid!');
		  return false; // Will stop the submission of the form
		},
		 onElementValidate : function(valid, $el, $form, errorMess) {
		 if (!valid) {
				$el.addClass('invalid');
				if(typeof $el.data("shown") == "undefined" || $el.data("shown") == false){
				  $el.popover('show');
				  $el.popover('hide');
				}
				
			}
			else {
				if(typeof $el.data("shown") != "undefined" || $el.data("shown") != false){
					$el.popover('destroy');
				}
			}
		}
	 });
	 $.validate({
		form : '#customer-save-form', 
  	    onSuccess : function($form) {
		  saveCustomer();
		  return false; // Will stop the submission of the form
		},		
	 });
	 $.validate({
		form : '#item-save-form', 
  	    onSuccess : function($form) {
		  saveItem();
		  return false; // Will stop the submission of the form
		},		
	 });
  $("#edit_billing_pincode").numeric();
  $("#edit_shipping_pincode").numeric();
  $("#due_date").inputmask("d-m-y", {
		"placeholder": "dd-mm-yyyy"
  });
  $("#invoice_date").inputmask("d-m-y", {
		"placeholder": "dd-mm-yyyy"
  }); 
  assignAutoCompleteContact($("#customer_name"))	
  assignAutoComplete($(".line_items").find("tr:last-child").find('.quick-item'));		
  
  // LISTENERS
  $("#edit_billing_pincode").on("keyup",function(){
	 limitText(this,6);
  });
  $("#edit_shipping_pincode").on("keyup",function(){
	 limitText(this,6);
  });
  $("#sba-btn").on("click",function(){
	 var billing_address=$("#edit_billing_address").val();
	 var billing_pincode=$("#edit_billing_pincode").val();
	 var billing_city=$("#edit_billing_city").val();
	 var billing_state=$("#edit_billing_state").val();
	 var billing_state_display=$("#edit_billing_state").find("option:selected").html();
	 
	 var final_readonly_address=[billing_address,billing_city,billing_state_display+" "+billing_pincode];
	 var final_readonly_address=final_readonly_address.join(",<br/>");
	 $(".read-only-billing-address").html(final_readonly_address);
	 $("input[name=billing_address]").val(billing_address);
	 $("input[name=billing_pincode]").val(billing_pincode);
	 $("input[name=billing_city]").val(billing_city);
	 $("input[name=billing_state]").val(billing_state);
	
 });
  $(".line_items").on("focus",".quick-item",function(){
	 if($(this).closest("tr").index()== $(".line_items tr:last-child").index())
	 {
		 var row_count=$(".line_items").find(".quick-item").length;
		 createNewRow(row_count);
	 }		 
  });
  $(".line_items").on("change","input",function(){
	 calculateTotal()	 
  });	 
 })
 
 function assignAutoComplete(node) {
		 $(node).autocomplete({	
			   source: function( request, response ) {
				$.ajax( {
				  url: "quick_invoice_ajax_function.php",
				  dataType: "json",
				  type:"POST",
				  data: {
					description: request.term,
					m:"fi"
				  },
				  success: function( data ) {
					response( $.map(data.result, function (value, key) {
					return value;
				}) );
				  }
				} );
			  },
			  minLength: 2,
			  select: function( event, ui ) {
				var index=$(this).closest("tr").index(); 
				var parent=$(this).closest("tr");
				var item_id_input=$(parent).find('input[name="line_items['+index+'][id]"]');
				$(item_id_input).val(ui.item.aid);
				var gst_type_input=$(parent).find('select[name="line_items['+index+'][gst_type]"]');
				$(gst_type_input).val(ui.item.gst_type);
				var gst_code_input=$(parent).find('input[name="line_items['+index+'][gst_code]"]');
				$(gst_code_input).val(ui.item.gst_code);
				var unit_price_input=$(parent).find('input[name="line_items['+index+'][unit_price]"]');
				$(unit_price_input).val(ui.item.unit_price);
				var discount_input=$(parent).find('input[name="line_items['+index+'][discount]"]');
				$(discount_input).val(ui.item.discount);
				
			  },
		open: function(event, ui) {
		$('.ui-autocomplete').append('<li><a data-target="#item-modal" data-toggle="modal">Create New Item</a></li>');} ,
			  _renderItem: function( ul, item ) {
				  return $( "<li>" )
					.attr( "data-value", item.value )
					.append( item.label )
					.appendTo( ul );
				},
			  _renderMenu: function( ul, items ) {
				  var that = this;
				  $.each( items, function( index, item ) {
					that._renderItemData( ul, item );
				  });
				  
			}
		});
 }
 function assignAutoCompleteContact(node){
	  $(node).autocomplete({	
			   source: function( request, response ) {
				$.ajax( {
				  url: "quick_invoice_ajax_function.php",
				  dataType: "json",
				  type:"POST",
				  data: {
					nick_name: request.term,
					m:"fc"
				  },
				  success: function( data ) {
					response( $.map(data.result, function (value, key) {
					return value;
				}) );
				  }
				} );
			  },
			  minLength: 2,
			  select: function( event, ui ) {
					$("input[name=customer_id]").val(ui.item.id);
			  },
		open: function(event, ui) {
		$('.ui-autocomplete').append('<li><a data-target="#customer-modal" data-toggle="modal">Create New Customer or Vendor</a></li>');} ,
			  _renderItem: function( ul, item ) {
				  return $( "<li>" )
					.attr( "data-value", item.value )
					.append( item.label )
					.appendTo( ul );
				},
			  _renderMenu: function( ul, items ) {
				  var that = this;
				  $.each( items, function( index, item ) {
					that._renderItemData( ul, item );
				  });
				  
			}
		});
 }
 function createNewRow(row_count){
	 var tableRow='<tr class="error-container'+row_count+'">'+
					'<td>'+row_count+'</td>'+
					'<td><input class="form-control quick-item" name="line_items['+row_count+'][description]" id="line_items['+row_count+'][description]" type="text"><input class="form-control quick-item" type="hidden" name="line_items['+row_count+'][id]"></td>'+
					'<td>'+
					'<select id="line_items['+row_count+'][gst_type]" name="line_items['+row_count+'][gst_type]" class="form-control text-center">'+
					'<option value="GOODS">Goods</option>'+
					'<option value="SERVICES">Services</option>'+
					'</select>'+
					'</td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][gst_code]" name="line_items['+row_count+'][gst_code]" value="" placeholder="" class="form-control text-center" type="text"></td>'+
					'<td><input autocomplete="off" data-validation="number" data-validation-allowing="float" id="line_items['+row_count+'][quantity]" name="line_items['+row_count+'][quantity]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][unit_price]" name="line_items['+row_count+'][unit_price]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][discount]" name="line_items['+row_count+'][discount]" value="" placeholder="" class="form-control text-right numeric-input" type="text"></td>'+
					'<td class=""> <input autocomplete="off" id="line_items['+row_count+'][taxable_val]" name="line_items['+row_count+'][taxable_val]" value="" data-viewport=".error-container'+row_count+'" placeholder="" class="form-control text-right numeric-input" type="text" data-validation="required" data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Taxable value should not be empty"   data-validation-error-msg=" "></td>'+
					'<td><select id="line_items['+row_count+'][cgst_rate]" name="line_items['+row_count+'][cgst_rate]" class="form-control" disabled="">'+
						'<option value="0">0</option>'+
						'<option value="0.125">0.125</option>'+
						'<option value="1.5">1.5</option>'+
						'<option value="2.5">2.5</option>'+
						'<option value="6">6</option>'+
						'<option value="9">9</option>'+
						'<option value="14">14</option>'+
					'</select>'+
					'</td>'+
					'<td> <input autocomplete="off" id="line_items['+row_count+'][cgst_val]" name="line_items['+row_count+'][cgst_val]" value="" placeholder="" disabled="" class="form-control text-right numeric-input" type="text"></td>'+
					'<td><select id="line_items['+row_count+'][sgst_rate]" name="line_items['+row_count+'][sgst_rate]" class="form-control" disabled=""><option value="0">0</option>'+
					'<option value="0.125">0.125</option>'+
					'<option value="1.5">1.5</option>'+
					'<option value="2.5">2.5</option>'+
					'<option value="6">6</option>'+
					'<option value="9">9</option>'+
					'<option value="14">14</option>'+
					'</select></td>'+
					'<td> <input autocomplete="off" id="line_items['+row_count+'][sgst_val]" name="line_items['+row_count+'][sgst_val]" value="" placeholder="" disabled="" class="form-control text-right numeric-input" type="text"></td>'+
					'<td><select id="line_items['+row_count+'][igst_rate]" name="line_items['+row_count+'][igst_rate]" class="form-control"><option value="0">0</option><option value=".25">0.25</option><option value="3">3</option><option value="5">5</option><option value="12">12</option><option value="18">18</option><option value="28">28</option></select></td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][igst_val]" name="line_items['+row_count+'][igst_val]" value="" placeholder="" disabled="" class="form-control text-right numeric-input" type="text">'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][cess_rate]" name="line_items['+row_count+'][cess_rate]" value="" placeholder="" class="form-control text-center numeric-input" type="text">'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][cess_val]" name="line_items['+row_count+'][cess_val]" value="" placeholder="" class="form-control text-right numeric-input" type="text">'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][total_val]" name="line_items['+row_count+'][total_val]" value="" placeholder="" disabled="" class="form-control text-right numeric-input" type="text">'
					'</td>'+
				'</tr>';
		$(".line_items").append(tableRow);
		assignAutoComplete($(".line_items").find("tr:last-child").find('.quick-item'));		
 }
 function calculateTotal(){
	 $(".line_items").find("tr").each(function(){
		 var index=$(this).index();
		 var parent=$(this);
		 var quantity_input=$(parent).find('input[name="line_items['+index+'][quantity]"]');
		 var quantity=$(quantity_input).val();
		 var unit_price_input=$(parent).find('input[name="line_items['+index+'][unit_price]"]');
		 var unit_price=$(unit_price_input).val();
		 var total_val_input=$(parent).find('input[name="line_items['+index+'][total_val]"]');
		 var total_val=parseFloat(quantity)*parseFloat(unit_price);
		 $(total_val_input).val(total_val);
		
		 
	 })
 }
 function limitText(field, maxChar){
    var ref = $(field),
        val = ref.val();
    if ( val.length >= maxChar ){
        ref.val(function() {
            return val.substr(0, maxChar);       
        });
    }
}
 function saveItem(){
	 var description=$("#description").val();
	 var gst_code=$("#gst_code").val();
	 var gst_type=$("#gst_type").val();
	 var item_code=$("#item_code").val();
	 var unit_price=$("#unit_price").val();
	 var unit_cost=$("#unit_cost").val();
	 var unit_of_measurement=$("#unit_of_measurement").val();
	 var discount=$("#discount").val();
	 var notes=$("#notes").val();
	 if(description!="" && gst_type!="")
	 {
		 $.ajax({
			 url:"quick_invoice_ajax_function.php",
			 type:"POST",
			 data:{
				 m:"si",
				 description:description,
				 gst_code:gst_code,
				 gst_type:gst_type,
				 item_code:item_code,
				 unit_price:unit_price,
				 unit_cost:unit_cost,
				 unit_of_measurement:unit_of_measurement,
				 discount:discount,
				 notes:notes,
			 },
			 success:function(result){
				result=$.parseJSON(result);
				if(result.a==1)
				{
					toastr.success("Item Saved!!","Great!!");
					$("#item-save-form")[0].reset();					
					$("#item-modal").modal('hide');
				}
				else
				toastr.error("Item Couldn't Saved!!","Sorry!!"); 	
			 },
			 error:function(){
				 toastr.error("Item Could Not Saved!!","Sorry!!");
			 }
		 })
	 }
	 else
	 {
		 toast.error("Item Saved!!","Great!!");toast.success("Item Saved!!","Great!!");
	 }
	 
	
 } 
 function saveCustomer(){
	 var nick_name=$("#business_name").val();
	 var gstin=$("#gstin").val();
	 var state=$("#state").val();
	 var city=$("#city").val();
	 var mobile_no=$("#mobile_no").val();
	 var pan_number=$("#pan_number").val();
	 var address=$("#address").val();
	 var zipcode=$("#zipcode").val();
	 var email=$("#email").val();
	 var land_line_number=$("#land_line_number").val();
	 if(nick_name!="" && state!="")
	 {
		 $.ajax({
			 url:"quick_invoice_ajax_function.php",
			 type:"POST",
			 data:{
				 m:"sc",
				 nick_name:nick_name,
				 gstin:gstin,
				 state:state,
				 city:city,
				 mobile_no:mobile_no,
				 pan_number:pan_number,
				 address:address,
				 zipcode:zipcode,
				 email:email,
				 land_line_number:land_line_number,
			 },
			 success:function(result){
				result=$.parseJSON(result);
				if(result.a==1)
				{
					$("#customer-save-form")[0].reset();
					toastr.success("Contact Saved!!","Great!!"); 
					$("#customer-modal").modal('hide');
				}
				else
				toastr.error("Contact Couldn't Saved!!","Sorry!!"); 	
			 },
			 error:function(){
				 toastr.error("Contact Could Not Saved!!","Sorry!!");
			 }
		 })
	 }
	 else
	 {
		 toastr.error("Contact Could Not Saved!!","Sorry!!");
	 }
	 
	
 }
 </script>
 <!-- END PAGELEVEL JS -->
</body>
</html>	