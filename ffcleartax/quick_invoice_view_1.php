<?php
$page_id                     ="514";
$page_title                     ="View Sales Invoice";
include("connect.php");
$ctableQuickInvoiceInfo="quick_invoice_info";
$ctableQuickInvoiceItem="quick_invoice_item";
$invoice=array();
$invoice_items=array();
try
{
	if(isset($_REQUEST['iid']) && $_REQUEST['iid']!="")
	{
		$invoice_id=$_REQUEST['iid'];
		$invoice=$db->rp_getData($ctableQuickInvoiceInfo,"*","id='".$invoice_id."'");
		if($invoice)
		{
			$invoice=mysql_fetch_assoc($invoice);
			$invoice_items_r=$db->rp_getData($ctableQuickInvoiceItem,"*","invoice_id='".$invoice_id."'");
			if($invoice_items_r)
			{
					while($item=mysql_fetch_assoc($invoice_items_r))
					{
						$invoice_items[]=$item;
					}
			}
		}
		else
		{
			throw new Exception("No Invoice Found!!");
		}
	}
	else
	{
		throw new Exception("No Invoice Found!!");
	}
}
catch(Exception $e)
{
	$system->addMessage($e->getMessage(),'error');
	$db->rp_location("quick_invoice_manage.php");
}	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
	
    <head>
        <meta charset="utf-8" />
        <title>Dashboard | <?php echo SITETITLE; ?></title>
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

   <body class="page-container-bg-solid">
        <div class="page-wrapper">
			<?php include("top.php");?>
			<!-- BEGIN CONTAINER -->
		   <div class="page-wrapper-row full-height">
                <div class="page-wrapper-middle">
                    <!-- BEGIN CONTAINER -->
                    <div class="page-container">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <!-- BEGIN CONTENT BODY -->
                            <!-- BEGIN PAGE HEAD-->
                            <div class="page-head">
                                <div class="container-fluid">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1><a href="<?php echo "quick_invoice_manage.php" ?>" class="btn btn-default active"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php echo $page_title; ?></h1>
                                    </div>
                                    <!-- END PAGE TITLE -->                                   
                                </div>
                            </div>
                            <!-- END PAGE HEAD-->
                            <!-- BEGIN PAGE CONTENT BODY -->
                            <div class="page-content">
                                <div class="container-fluid">
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
									<div class="row">
									<div class="col-sm-12">
										<?php $system->getMessageBlock(); ?>	
									</div>
									</div>
                                    <!-- END PAGE HEADER-->
									<!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner bg-white">
                                        <div class="invoice" style="padding:10px;">
                                            <div class="row invoice-logo">
                                                <div class="col-xs-6 invoice-logo-space">
                                                    <img src="../assets/pages/media/invoice/walmart.png" class="img-responsive" alt="" /> </div>
                                                <div class="col-xs-6 text-right">
                                                    <p> #<?php echo $invoice['invoice_serial_no']; ?> / <?php echo date("d M Y",strtotime($invoice['invoice_serial_no'])); ?>
                                                        
                                                    </p>
													<a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();"> Print
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                   
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="row">
												<div class="col-xs-4">
                                                    <h4>Customer Name:</h4>
                                                    <ul class="list-unstyled">
                                                       <li> <?php echo $invoice['customer_name']; ?> </li>
                                                    </ul>
													 <h4>Customer GSTIN:</h4>
                                                    <ul class="list-unstyled">
                                                       <li> <?php echo $invoice['customer_gstin']; ?> </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xs-4">
                                                    <h4>Billing Address:</h4>
                                                    <ul class="list-unstyled">
                                                        <li> <?php echo $invoice['customer_name']; ?> </li>
                                                        <li> <?php echo $invoice['customer_billing_address']; ?></li>
                                                        <li> <?php echo $invoice['customer_billing_city']; ?></li>
                                                        <li> <?php echo $invoice['customer_billing_pincode']; ?> </li>
                                                        <li> <?php echo $invoice['customer_billing_state']; ?> </li>
                                                        <li> <?php echo $invoice['customer_billing_country']; ?> </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xs-4">
                                                    <h4>Shipping Address:</h4>
                                                    <ul class="list-unstyled">
                                                        <li> <?php echo $invoice['customer_name']; ?> </li>
                                                        <li> <?php echo $invoice['customer_shipping_address']; ?></li>
                                                        <li> <?php echo $invoice['customer_shipping_city']; ?></li>
                                                        <li> <?php echo $invoice['customer_shipping_pincode']; ?> </li>
                                                        <li> <?php echo $invoice['customer_shipping_state']; ?> </li>
                                                        <li> <?php echo $invoice['customer_shipping_country']; ?> </li>
                                                    </ul>
                                                </div>
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
												<div class="table-scrollable table-list table-list-view">
										<table class="table table-bordered table-hover" id="line_items_table">
											<thead>
												<tr >
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th class="text-center" colspan="2" > CGST</th>
													<th class="text-center" colspan="2"> SGST</th>
													<th class="text-center" colspan="2"> CESS</th>
													<th></th>
													
												</tr>
												<tr>
													<th> HSN Code</th>
													<th> Description of Services </th>
													<th> Qty </th>
													<th> Rate<br/>(Rs.)</th>
													<th> Disc.<br/>(Rs.)</th>
													<th> Taxable<br/> Value </th>
													<th width="50px"> %</th>
													<th width="50px">Amt (Rs.)</th>
													<th > %</th>
													<th >Amt (Rs.)</th>
													<th > %</th>
													<th >Amt (Rs.)</th>
													
													<th> Net Amt</th>
												</tr>
												
												
											</thead>
											<tbody class="line_items">
												
												<tr>
													<td>23456789</td>
													<td>6000MLPLBTN1X24 tdumbs Up NORM 36.00 (00101640)</td>
													<td class="text-right">1.00</td>
													<td class="text-right">561.43</td>
													<td class="text-right">561.43</td>
													<td class="text-right">57.14</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 12</td>
													<td class="text-right" widtd="50px">60.51</td>
													<td class="text-right" >706.00</td>
													
												</tr>
												<tr>
													<td>23456789</td>
													<td>6000MLPLBTN1X24 tdumbs Up NORM 36.00 (00101640)</td>
													<td class="text-right">1.00</td>
													<td class="text-right">561.43</td>
													<td class="text-right">561.43</td>
													<td class="text-right">57.14</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 12</td>
													<td class="text-right" widtd="50px">60.51</td>
													<td class="text-right" >706.00</td>
													
												</tr>
												<tr>
													<td>23456789</td>
													<td>6000MLPLBTN1X24 tdumbs Up NORM 36.00 (00101640)</td>
													<td class="text-right">1.00</td>
													<td class="text-right">561.43</td>
													<td class="text-right">561.43</td>
													<td class="text-right">57.14</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 14</td>
													<td class="text-right" widtd="50px">70.60</td>
													<td class="text-right" widtd="50px"> 12</td>
													<td class="text-right" widtd="50px">60.51</td>
													<td class="text-right" >706.00</td>
													
												</tr>
											</tbody>
											<tfoot >
												<tr>
													<th colspan="1"></th>
													<th class="item-discount">Total</th>
													<th class=""> </th>
													<th class=""> </th>
													<th class=""> 564.16</th>
													<th class="total-taxable text-right">3468.23 </th>
													<th></th>
													<th class="total-cgst text-right"> 335</th>
													<th></th>
													<th class="total-sgst text-right">335</th>
													<th></th>
													<th class="total-cess text-right">206.13</th>
													<th class="grand-total text-right">4384.36</th>
													
												</tr>
											</tfoot>
										</table>
										
									  </div>
                                    </div>
                              
                                                    <table class="hidden table table-bordered table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th> # </th>
                                                                <th> Item </th>
                                                                <th class="hidden-xs text-center"> HSN/SAC </th>
                                                                <th class="hidden-xs text-center"> Quantity </th>
                                                                <th class="hidden-xs text-center"> Rate </th>
                                                                <th class="hidden-xs text-center"> Discount </th>
                                                                <th class="text-center"> Taxable Value </th>
                                                                <th class="text-center"> IGST </th>
                                                                <th class="text-center"> CESS </th>
                                                                <th class="text-center"> Total </th>                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
															<?php 
															foreach($invoice_items as $key=>$item)
															{
															?>
                                                            <tr>
																<td> <?php echo $key+1; ?> </td>
                                                                <td><?php echo $item['item_description'] ?> </td>
                                                                <td class="hidden-xs"> <?php echo $item['item_gst_code'] ?>  </td>
                                                                <td class="hidden-xs text-right"> <?php echo $item['item_qty'] ?>  </td>
                                                                <td class="hidden-xs text-right"> <?php echo $item['item_unit_price'] ?>  </td>
                                                                <td class="text-right"><?php echo $item['item_discount'] ?>  </td>
                                                                <td class="text-right"><?php echo $item['item_taxable_value'] ?>  </td>
                                                                <td class="text-right"> <?php echo $item['item_igst_amount'] ?>  </td>
                                                                <td class="text-right"> <?php echo $item['item_cess_amount'] ?>  </td>
                                                                <td class="text-right"> <?php echo $item['item_subtotal'] ?>  </td>
                                                            </tr>
															<?php }?>
                                                            <tr>
                                                            <td colspan="8">
															</td>
                                                            <td class="text-right">
																Taxable Amount
                                                            </td>
                                                            <td class="text-right">
																<?php echo $invoice['invoice_total_taxable_value'] ?>
                                                            </td>
															</tr>
															 <tr>
                                                            <td colspan="8">
															</td>
                                                            <td class="text-right">
																Total Tax
                                                            </td>
                                                            <td class="text-right">
																<?php echo $invoice['invoice_total_igst'] ?>
                                                            </td>
															</tr>
															 <tr>
                                                            <td colspan="8">
															</td>
                                                            <td class="text-right">
																Invoice Total
                                                            </td>
                                                            <td class="text-right">
																<?php echo $invoice['invoice_grand_total'] ?>
                                                            </td>
															</tr>
															 <tr>
                                                            <td colspan="7">
															</td>
                                                            <td  class="text-right">
																Invoice Total In Words
                                                            </td>
                                                            <td colspan="2"class="text-right">
																<?php echo $invoice['invoice_grand_total'] ?>
                                                            </td>
															</tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
											<div class="row">
                                                
                                                <div class="col-xs-1 pull-right invoice-block">
                                                   <label>
												   <br/>
												   <br/>
													Signature
												   </label>
                                                </div>
                                            </div>
											<div class="row">
                                                
                                                <div class="col-xs-12 pull-right invoice-block">
                                                   <label>
												   <br/>
												   <br/>
													Footer Text
												   </label>
                                                </div>
                                            </div>                                           
                                        </div>
                                    </div>
                                    <!-- END PAGE CONTENT INNER -->
                       
									<!-- END PAGE CONTENT INNER -->
									</div>
                                </div>
                            </div>
                            <!-- END PAGE CONTENT BODY -->
                            <!-- END CONTENT BODY -->
                        </div>
                        <!-- END CONTENT -->
                       <?php include('rightbar.php'); ?>
                    </div>
                    <!-- END CONTAINER -->
                </div>
            </div>
            <div class="page-wrapper-row">
				<?php include('footer.php'); ?>
		    </div>
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
	 $("#billing-address-modal").modal('hide');
	
 });
 $("#ssa-btn").on("click",function(){
	 var shipping_address=$("#edit_shipping_address").val();
	 var shipping_pincode=$("#edit_shipping_pincode").val();
	 var shipping_city=$("#edit_shipping_city").val();
	 var shipping_state=$("#edit_shipping_state").val();
	 var shipping_state_display=$("#edit_shipping_state").find("option:selected").html();
	 
	 var final_readonly_address=[shipping_address,shipping_city,shipping_state_display+" "+shipping_pincode];
	 var final_readonly_address=final_readonly_address.join(",<br/>");
	 $(".read-only-shipping-address").html(final_readonly_address);
	 $("input[name=shipping_address]").val(shipping_address);
	 $("input[name=shipping_pincode]").val(shipping_pincode);
	 $("input[name=shipping_city]").val(shipping_city);
	 $("input[name=shipping_state]").val(shipping_state);
	 $("#shipping-address-modal").modal('hide');
	
 });
 $("#same-as-billing-btn").on("change",function(){
	 if($(this).prop("checked"))
	 {
		 copyBillingAddress();
	 }
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
 function copyBillingAddress()
 {
	 var billing_address= $("input[name=billing_address]").val();
	 var billing_pincode=$("input[name=billing_pincode]").val();
	 var billing_city= $("input[name=billing_city]").val();
	 var billing_state=$("input[name=billing_state]").val();
	 var billing_state_display=$("#edit_billing_state").find("option:selected").html();
	 
	 var final_readonly_address=[billing_address,billing_city,billing_state+" "+billing_pincode];
	 var final_readonly_address=final_readonly_address.join(",<br/>");
	 $(".read-only-shipping-address").html(final_readonly_address);
	 $("input[name=shipping_address]").val(billing_address);
	 $("input[name=shipping_pincode]").val(billing_pincode);
	 $("input[name=shipping_city]").val(billing_city);
	 $("input[name=shipping_state]").val(billing_state);
 }
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