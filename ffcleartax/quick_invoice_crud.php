<?php
$page_id                     ="514";
$page_title                     ="Create Sales Invoice";
include("connect.php");
$ctableQuickInvoiceInfo="quick_invoice_info";
$ctableQuickInvoiceItem="quick_invoice_item";
$ctableQuickItemMaster="quick_invoice_item_master";
$invoice=array();
$invoice_items=array();
$_REQUEST['mode']=$mode=(isset($_REQUEST['mode']))?$_REQUEST['mode']:"add";
$customer_id                 ="";
$customer_name               ="";
$customer_billing_address    ="";
$customer_billing_pincode    ="";
$customer_billing_city       ="";
$customer_billing_state      ="";
$customer_billing_country    ="";
$customer_billing_state_display="";
$customer_shipping_address   ="";
$customer_shipping_pincode   ="";
$customer_shipping_city      ="";
$customer_shipping_state     ="";
$customer_shipping_country   ="";
$customer_shipping_state_display="";
				
$next_number=intval($db->getlastInsertId($ctableQuickInvoiceInfo)+1);
$invoice_serial_no           =$db->generateSerialWithPrefix(INVOICE_SERIAL_PREFIX,$next_number,4);
$invoice_date                =date("d-m-Y");
$invoice_reference           ="";
$invoice_due_date            =date("d-m-Y");
$customer_gstin              ="";
$customer_place_of_supply    ="";
$invoice_total_taxable_value ="";
$invoice_grand_total         ="";
$invoice_total_cgst          ="";
$invoice_total_sgst          ="";
$invoice_total_igst          ="";
$invoice_total_cess          ="";
$invoice_total_discount      ="";
$disabled_input				 ="";

if(isset($_POST['save_invoice']))
{
	
	$customer_id                 =isset($_POST['customer_id'])?$db->clean($_POST['customer_id']):"";
	$customer_name               =isset($_POST['customer_name'])?$db->clean($_POST['customer_name']):"";
	$customer_billing_address    =isset($_POST['billing_address'])?$db->clean($_POST['billing_address']):"";
	$customer_billing_pincode    =isset($_POST['billing_pincode'])?$db->clean($_POST['billing_pincode']):"";
	$customer_billing_city       =isset($_POST['billing_city'])?$db->clean($_POST['billing_city']):"";
	$customer_billing_state      =isset($_POST['billing_state'])?$db->clean($_POST['billing_state']):"";
	$customer_billing_country    =isset($_POST['billing_country'])?$db->clean($_POST['billing_country']):"";
	$customer_shipping_address   =isset($_POST['shipping_address'])?$db->clean($_POST['shipping_address']):"";
	$customer_shipping_pincode   =isset($_POST['shipping_pincode'])?$db->clean($_POST['shipping_pincode']):"";
	$customer_shipping_city      =isset($_POST['shipping_city'])?$db->clean($_POST['shipping_city']):"";
	$customer_shipping_state     =isset($_POST['shipping_state'])?$db->clean($_POST['shipping_state']):"";
	$customer_shipping_country   =isset($_POST['shipping_country'])?$db->clean($_POST['shipping_country']):"";
	//$invoice_serial_no           =isset($_POST['invoice_serial_no'])?$db->clean($_POST['invoice_serial_no']):"";
	$invoice_date                =isset($_POST['invoice_date'])?date("Y-m-d",strtotime($db->clean($_POST['invoice_date']))):"";
	$invoice_reference           =isset($_POST['invoice_reference'])?$db->clean($_POST['invoice_reference']):"";
	$invoice_due_date            =isset($_POST['due_date'])?date("Y-m-d",strtotime($db->clean($_POST['due_date']))):"";
	$customer_gstin              =isset($_POST['gstin'])?$db->clean($_POST['gstin']):"";
	$customer_place_of_supply    =isset($_POST['place_of_supply'])?$db->clean($_POST['place_of_supply']):"";
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
		
		try
		{
			if($customer_name!="")
			{
				$isValid=true;
				$error=array();
				foreach($Items as $Item)
				{
					$item_id				           =$Item['id'];
					$item_description		           =$Item['description'];
					if($item_description!="")
					{
						
						$item_qty                          =floatval($Item['quantity']);
						// Add item to master if not available
						$item_id=$db->rp_getValue($ctableQuickItemMaster,"aid","description='".$item_description."'",0);
						if($item_id!="")
						{
							$item_current_stock=$db->rp_getValue($ctableQuickItemMaster,"stock_qty","aid='".$item_id."'",0);
							$new_stock=$item_current_stock-$item_qty;
							if($new_stock<0)
							{
								$isValid=false;
								$error[]=$item_description." not available in stock. Avaialble stock qty ".$item_current_stock;
							}
						}
					}
					
				}
				if($isValid)
				{
					if($mode!="edit")
					{
						if($invoice_serial_no!="" )
						{
							$columns_invoice                           =array("customer_id", "customer_name", "customer_billing_address", "customer_billing_pincode", "customer_billing_city", "customer_billing_state", "customer_billing_country", "customer_shipping_address", "customer_shipping_pincode", "customer_shipping_city", "customer_shipping_state", "customer_shipping_country", "invoice_serial_no", "invoice_date", "invoice_reference", "invoice_due_date", "customer_gstin", "customer_place_of_supply", "invoice_total_taxable_value", "invoice_grand_total", "invoice_total_cgst", "invoice_total_sgst", "invoice_total_igst", "invoice_total_cess", "created_date", "isDelete", "isActive");
							$value_invoice                             =array($customer_id, $customer_name, $customer_billing_address, $customer_billing_pincode, $customer_billing_city, $customer_billing_state, $customer_billing_country, $customer_shipping_address, $customer_shipping_pincode, $customer_shipping_city, $customer_shipping_state, $customer_shipping_country, $invoice_serial_no, $invoice_date, $invoice_reference, $invoice_due_date, $customer_gstin, $customer_place_of_supply, $invoice_total_taxable_value, $invoice_grand_total, $invoice_total_cgst, $invoice_total_sgst, $invoice_total_igst, $invoice_total_cess, $created_date, $isDelete, $isActive);

							$QuickInvoiceInfoId                        =$db->rp_insert($ctableQuickInvoiceInfo,$value_invoice,$columns_invoice);
						}
						else
						{
							$QuickInvoiceInfoId=0;
							throw new Exception("Following entry required.<br/>Invoice Serial Number. Error Code CQI102");							
						}
						
					}
					else
					{
						$update_values                           
						=array("customer_id"=>$customer_id,
								"customer_name"=>$customer_name,
								"customer_billing_address"=>$customer_billing_address,
								"customer_billing_pincode" =>$customer_billing_pincode,
								"customer_billing_city"=>$customer_billing_city, 
								"customer_billing_state"=>$customer_billing_state, 
								"customer_billing_country"=>$customer_billing_country, 
								"customer_shipping_address"=>$customer_shipping_address, 
								"customer_shipping_pincode"=>$customer_shipping_pincode, 
								"customer_shipping_city"=>$customer_shipping_city, 
								"customer_shipping_state"=>$customer_shipping_state, 
								"customer_shipping_country"=>$customer_shipping_country,
								"invoice_date"=>$invoice_date,
								"invoice_reference"=>$invoice_reference,
								"invoice_due_date"=>$invoice_due_date,
								"customer_gstin"=>$customer_gstin,
								"customer_place_of_supply"=>$customer_place_of_supply, 
								"invoice_total_taxable_value"=>0,
								"invoice_grand_total"=>0,
								"invoice_total_cgst"=>0,
								"invoice_total_sgst"=>0,
								"invoice_total_igst"=>0,
								"invoice_total_cess"=>0,
								"created_date"=>$created_date, 
								"isDelete"=>$isDelete, 
								"isActive"=>$isActive);
								$QuickInvoiceInfoId=$_REQUEST['iid'];
								$db->rp_update($ctableQuickInvoiceInfo,$update_values,"id='".$QuickInvoiceInfoId."'");
					}
					
					if($QuickInvoiceInfoId!=0)
					{
							// is Old item available then add stock back
							$old_items=$db->rp_getData($ctableQuickInvoiceItem,"*","invoice_id='".$QuickInvoiceInfoId."'");
							if($old_items)
							{
								while($old_item=mysql_fetch_assoc($old_items))
								{
									$current_stock=$db->rp_getValue($ctableQuickItemMaster,"stock_qty","aid='".$old_item['item_id']."'");
									if($current_stock!="")
									{
										$current_stock=floatval($current_stock);	
									}
									else
									{
										$current_stock=0;
									}										
									$new_stock=$current_stock+$old_item['item_qty'];
									$isUpdated=$db->rp_update($ctableQuickItemMaster,array("stock_qty"=>$new_stock),"aid='".$old_item['item_id']."'");
								}
							}
							$db->rp_delete($ctableQuickInvoiceItem,"invoice_id='".$QuickInvoiceInfoId."'");
							foreach($Items as $Item)
							{
								$item_id				           =$Item['id'];
								$item_description		           =$Item['description'];
								if($item_description!="")
								{
									
									$Item['cgst_rate']=(isset($Item['cgst_rate']))?$Item['cgst_rate']:0;
									$Item['sgst_rate']=(isset($Item['sgst_rate']))?$Item['sgst_rate']:0;
									$Item['igst_rate']=(isset($Item['igst_rate']))?$Item['igst_rate']:0;
									$Item['cess_rate']=(isset($Item['cess_rate']))?$Item['cess_rate']:0;
									$item_type                         =$Item['gst_type'];
									$item_gst_code                     =$Item['gst_code'];
									$item_code                 		   ="";
									$item_qty                          =floatval($Item['quantity']);
									$item_unit_price                   =floatval($Item['unit_price']);
									$item_unit_cost                    =0;
									$item_unit_of_messurement          ="";
									$item_discount                     =floatval($Item['discount']);
									$item_note              		   ="";
									$item_taxable_value                =$db->round(($item_qty*$item_unit_price)-$item_discount);
									$item_cgst_per					   =floatval($Item['cgst_rate']);
									$item_cgst_amount				   =$db->round(($item_taxable_value*$item_cgst_per)/100);
									$item_sgst_per					   =floatval($Item['sgst_rate']);
									$item_sgst_amount				   =$db->round(($item_taxable_value*$item_sgst_per)/100);
									$item_igst_per					   =floatval($Item['igst_rate']);
									$item_igst_amount				   =$db->round(($item_taxable_value*$item_igst_per)/100);
									$item_cess_per				 	   =floatval($Item['cess_rate']);
									$item_cess_amount				   =$db->round(($item_taxable_value*$item_cess_per)/100);
									$item_subtotal				 	   =$db->round($item_taxable_value+$item_cgst_amount+$item_sgst_amount+$item_igst_amount+$item_cess_amount);
									$isDelete                          =0;
									$isActive                          =1;
									$created_date                      =date("Y-m-d H:i:s");
									
									// Add item to master if not available
									$item_id=$db->rp_getValue($ctableQuickItemMaster,"aid","description='".$item_description."'",0);
									if($item_id=="")
									{
										$rows=array(  "item_code",
											"gst_code",
											"gst_type",
											"description",
											"unit_price",
											"unit_of_measurement",
											"unit_cost",
											"discount",
											"cgst_rate",
											"sgst_rate",
											"igst_rate",
											"cess_rate",
											"opening_qty",
											"stock_qty");
									$values=array($item_code,
											$item_gst_code,
											$item_type,
											$item_description,
											$item_unit_price,
											$item_unit_of_messurement,
											$item_unit_cost,
											$item_discount,
											$item_cgst_per,
											$item_sgst_per,
											$item_igst_per,
											$item_cess_per,
											$item_qty,
											$item_qty);
										$item_id=$db->rp_insert($ctableQuickItemMaster,$values,$rows);
									}
									
									$columns_invoice_item              =array("invoice_id","item_id", "item_description", "item_type", "item_gst_code", "item_code","item_qty", "item_unit_price", "item_unit_cost", "item_unit_of_messurement", "item_discount", "item_note", "item_taxable_value", "item_cgst_per", "item_cgst_amount", "item_sgst_per", "item_sgst_amount", "item_igst_per", "item_igst_amount", "item_cess_per", "item_cess_amount", "item_subtotal", "isDelete", "isActive", "created_date");
									$value_invoice_item                =array($QuickInvoiceInfoId,$item_id, $item_description, $item_type, $item_gst_code, $item_code,$item_qty, $item_unit_price, $item_unit_cost, $item_unit_of_messurement, $item_discount, $item_note, $item_taxable_value, $item_cgst_per, $item_cgst_amount, $item_sgst_per, $item_sgst_amount, $item_igst_per, $item_igst_amount, $item_cess_per, $item_cess_amount, $item_subtotal, $isDelete, $isActive, $created_date);
									
									
									
									$ItemInsertedId=$db->rp_insert($ctableQuickInvoiceItem,$value_invoice_item,$columns_invoice_item,0);
									if($ItemInsertedId!=0)
									{
										// Deduct item stock from master
										
										$current_stock=$db->rp_getValue($ctableQuickItemMaster,"stock_qty","aid='".$item_id."'");
										if($current_stock!="")
										{
											$current_stock=floatval($current_stock);	
										}
										else
										{
											$current_stock=0;
										}										
										
										$new_stock=$current_stock-$item_qty;
										// Update New Stock;
										$isUpdated=$db->rp_update($ctableQuickItemMaster,array("stock_qty"=>$new_stock),"aid='".$item_id."'");
										
										
										$invoice_total_taxable_value+=$item_taxable_value;
										$invoice_total_cgst+=$item_cgst_amount;
										$invoice_total_sgst+=$item_sgst_amount;
										$invoice_total_igst+=$item_igst_amount;
										$invoice_total_cess+=$item_cess_amount;
										$invoice_total_discount+=$item_discount;
									}
									else
									{
										throw new Exception("Something went wrong with Item ".$item_description." Error Code CQI105");		
									}
									
								}
								
							}
							
							// Calculate Final Values
							$invoice_grand_total=$db->round($invoice_total_taxable_value+$invoice_total_cgst+$invoice_total_sgst+$invoice_total_igst+$invoice_total_cess);
							
							// Update Invoice Details
							$UpdateInvoice=array("invoice_grand_total"=>$invoice_grand_total,
												 "invoice_total_cgst"=>$invoice_total_cgst,
												 "invoice_total_sgst"=>$invoice_total_sgst,
												 "invoice_total_igst"=>$invoice_total_igst,
												 "invoice_total_cess"=>$invoice_total_cess,
												 "invoice_total_taxable_value"=>$invoice_total_taxable_value,
												 "invoice_total_discount"=>$invoice_total_discount,
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
					else
					{
						$reply=array("ack"=>0,"ack_msg"=>"Invoice could not be saved try again!!  Error Code CQI103");	
					}
				}
				else
				{
					$system->addMessage("Following Items not available in stock.<br/>".implode("<br/>",$error),'error');
					if($mode!="edit")
					{
						$_SESSION['last_submitted']=$_POST;
						$db->rp_location("quick_invoice_crud.php?m=a");
					}
					else
					{
						$_SESSION['last_submitted']=$_POST;
						$db->rp_location("quick_invoice_crud.php?m=e&iid=".$QuickInvoiceInfoId);
					}
				}
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Following entry required.<br/>Customer Name Required. Error Code CQI102");
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
		$reply=array("ack"=>0,"ack_msg"=>"Select atleast one item before saving invoice!! Error Code CQI101");	
	}
	
	if($reply['ack']==1)
	{
		$system->addMessage($reply['ack_msg'],'success');
		$db->rp_location("quick_invoice_view.php?m=e&iid=".$QuickInvoiceInfoId);
	}
	else
	{
		$system->addMessage($reply['ack_msg'],'error');
	}

}

if($_REQUEST['mode']=="edit")
{
		try
		{
			$disabled_input="disabled";
		if(isset($_REQUEST['iid']) && $_REQUEST['iid']!="")
		{
			$invoice_id=$_REQUEST['iid'];
			$invoice=$db->rp_getData($ctableQuickInvoiceInfo,"*","id='".$invoice_id."'");
			if($invoice)
			{
				$invoice=mysql_fetch_assoc($invoice);
				extract($invoice);
				$customer_billing_state_display=$db->rp_getValue("state","name","slug='".$invoice['customer_billing_state']."'");
				$customer_shipping_state_display=$db->rp_getValue("state","name","slug='".$invoice['customer_shipping_state']."'");;
				$invoice_items_r=$db->rp_getData($ctableQuickInvoiceItem,"*","invoice_id='".$invoice_id."'");
				$customer_shipping_pincode=($customer_shipping_pincode!=0)?$customer_shipping_pincode:"";
				$customer_billing_pincode=($customer_billing_pincode!=0)?$customer_billing_pincode:"";
				$invoice_date=($invoice_date=='0000-00-00')?"":date("d-m-Y",strtotime($invoice_date));
				$invoice_due_date=($invoice_due_date=='0000-00-00')?"":date("d-m-Y",strtotime($invoice_due_date));
				if($invoice_items_r)
				{
						while($item=mysql_fetch_assoc($invoice_items_r))
						{
							if($item['item_id']!="")
							{
								$item['item_stock_qty']=$item['item_qty']+floatval($db->rp_getValue($ctableQuickItemMaster,"stock_qty","aid='".$item['item_id']."'"));
								if($customer_place_of_supply=="GUJARAT")
								{
									$item['item_igst_per']=$db->rp_getValue($ctableQuickItemMaster,"igst_rate","aid='".$item['item_id']."'");
								}
								else
								{
									$item['item_cgst_per']=$db->rp_getValue($ctableQuickItemMaster,"cgst_rate","aid='".$item['item_id']."'");
									$item['item_sgst_per']=$db->rp_getValue($ctableQuickItemMaster,"sgst_rate","aid='".$item['item_id']."'");									
								}
									
							}
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
}
$tax_variants_for_cgst_r=$db->rp_getData("tax_variant","*","variant_for_cgst=1");
$tax_variants_for_sgst_r=$db->rp_getData("tax_variant","*","variant_for_sgst=1");
$tax_variants_for_igst_r=$db->rp_getData("tax_variant","*","variant_for_igst=1");
$tax_variants_for_cess_r=$db->rp_getData("tax_variant","*","variant_for_cess=1");
while($tax_variant=mysql_fetch_assoc($tax_variants_for_cgst_r)){$tax_variants_for_cgst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_sgst_r)){$tax_variants_for_sgst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_igst_r)){$tax_variants_for_igst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_cess_r)){$tax_variants_for_cess[]=$tax_variant;};
$states_r=$db->rp_getData("state","*","1=1","");
while($s=mysql_fetch_assoc($states_r)){$states[]=$s;}

if(isset($_SESSION['last_submitted']))
{
	$last_submitted=$_SESSION['last_submitted'];
	unset($_SESSION['last_submitted']);
	$customer_id                 =isset($last_submitted['customer_id'])?$db->clean($last_submitted['customer_id']):"";
	$customer_name               =isset($last_submitted['customer_name'])?$db->clean($last_submitted['customer_name']):"";
	$customer_billing_address    =isset($last_submitted['billing_address'])?$db->clean($last_submitted['billing_address']):"";
	$customer_billing_pincode    =isset($last_submitted['billing_pincode'])?$db->clean($last_submitted['billing_pincode']):"";
	$customer_billing_city       =isset($last_submitted['billing_city'])?$db->clean($last_submitted['billing_city']):"";
	$customer_billing_state      =isset($last_submitted['billing_state'])?$db->clean($last_submitted['billing_state']):"";
	$customer_billing_country    =isset($last_submitted['billing_country'])?$db->clean($last_submitted['billing_country']):"";
	$customer_shipping_address   =isset($last_submitted['shipping_address'])?$db->clean($last_submitted['shipping_address']):"";
	$customer_shipping_pincode   =isset($last_submitted['shipping_pincode'])?$db->clean($last_submitted['shipping_pincode']):"";
	$customer_shipping_city      =isset($last_submitted['shipping_city'])?$db->clean($last_submitted['shipping_city']):"";
	$customer_shipping_state     =isset($last_submitted['shipping_state'])?$db->clean($last_submitted['shipping_state']):"";
	$customer_shipping_country   =isset($last_submitted['shipping_country'])?$db->clean($last_submitted['shipping_country']):"";
	//$invoice_serial_no           =isset($last_submitted['invoice_serial_no'])?$db->clean($last_submitted['invoice_serial_no']):"";
	$invoice_date                =isset($last_submitted['invoice_date'])?date("Y-m-d",strtotime($db->clean($last_submitted['invoice_date']))):"";
	$invoice_reference           =isset($last_submitted['invoice_reference'])?$db->clean($last_submitted['invoice_reference']):"";
	$invoice_due_date            =isset($last_submitted['due_date'])?date("Y-m-d",strtotime($db->clean($last_submitted['due_date']))):"";
	$customer_gstin              =isset($last_submitted['gstin'])?$db->clean($last_submitted['gstin']):"";
	$customer_place_of_supply    =isset($last_submitted['place_of_supply'])?$db->clean($last_submitted['place_of_supply']):"";
	$invoice_total_taxable_value =isset($last_submitted['invoice_total_taxable_value'])?$db->clean($last_submitted['invoice_total_taxable_value']):0;
	$invoice_grand_total         =isset($last_submitted['invoice_grand_total'])?$db->clean($last_submitted['invoice_grand_total']):0;
	$invoice_total_cgst          =isset($last_submitted['invoice_total_cgst'])?$db->clean($last_submitted['invoice_total_cgst']):0;
	$invoice_total_sgst          =isset($last_submitted['invoice_total_sgst'])?$db->clean($last_submitted['invoice_total_sgst']):0;
	$invoice_total_igst          =isset($last_submitted['invoice_total_igst'])?$db->clean($last_submitted['invoice_total_igst']):0;
	$invoice_total_cess          =isset($last_submitted['invoice_total_cess'])?$db->clean($last_submitted['invoice_total_cess']):0;
	$invoice_items          	 =isset($last_submitted['line_items'])?$last_submitted['line_items']:array();
	foreach($invoice_items as $key=>$Item)
	{
		$invoice_items[$key]['item_description']=$Item['description'];
		$invoice_items[$key]['cgst_rate']=(isset($Item['cgst_rate']))?$Item['cgst_rate']:0;
		$invoice_items[$key]['sgst_rate']=(isset($Item['sgst_rate']))?$Item['sgst_rate']:0;
		$invoice_items[$key]['igst_rate']=(isset($Item['igst_rate']))?$Item['igst_rate']:0;
		$invoice_items[$key]['cess_rate']=(isset($Item['cess_rate']))?$Item['cess_rate']:0;
		$invoice_items[$key]['item_type']                        =$Item['gst_type'];
		$invoice_items[$key]['item_gst_code']                     =$Item['gst_code'];
		$invoice_items[$key]['item_code']                 		   ="";
		$invoice_items[$key]['item_qty']                          =floatval($Item['quantity']);
		$invoice_items[$key]['item_unit_price']                   =floatval($Item['unit_price']);
		$invoice_items[$key]['item_unit_cost']                    =0;
		$invoice_items[$key]['item_unit_of_messurement']          ="";
		$invoice_items[$key]['item_discount']                     =floatval($Item['discount']);
		$invoice_items[$key]['item_note']              		 	  ="";
		$invoice_items[$key]['item_taxable_value']                =0;
		$invoice_items[$key]['item_cgst_per']					   =floatval($invoice_items[$key]['cgst_rate']);
		$invoice_items[$key]['item_cgst_amount']				   =0;
		$invoice_items[$key]['item_sgst_per']					   =floatval($invoice_items[$key]['sgst_rate']);
		$invoice_items[$key]['item_sgst_amount']				   =0;
		$invoice_items[$key]['item_igst_per']					   =floatval($invoice_items[$key]['igst_rate']);
		$invoice_items[$key]['item_igst_amount']				   =0;
		$invoice_items[$key]['item_cess_per']				 	   =floatval($Item['cess_rate']);
		$invoice_items[$key]['item_cess_amount']				   =0;
		$invoice_items[$key]['item_subtotal']				 	   =0;		
	}
	$created_date                =date("Y-m-d H:i:s");
	$isActive                    =1;
	$isDelete                    =0;
	
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
                                        <h1><a href="<?php echo "quick_invoice_manage.php" ?>" class="btn btn-default active"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;Back</a> &nbsp;<?php echo $page_title; ?></h1>
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
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
								<form name="invoice-form" id="invoice-form" method="POST" action="">
								
                                <div class="portlet light portlet-fit bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-file font-red"></i>
                                            <span class="caption-subject font-red sbold uppercase">Invoice</span>
                                        </div>
										<div class="actions">
										
											<div class="btn-group btn-group-devided">
													<a name="options" href="quick_invoice_manage.php" class="btn btn-transparent blue btn-outline btn-square btn-sm active" id="back-invoice-btn" type="button">Back</a>
													<button class="btn btn-transparent green-jungle btn-outline btn-square btn-sm active" name="save_invoice" id="save-invoice-btn" type="submit">Save</button>	
													
											</div>
										</div>
                                    </div>
                                    <div class="portlet-body">
									  <div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Invoice Serial Number</b></label>
												<input class="form-control" placeholder="Serial Number" name="invoice_serial_no" id="invoice_serial_no" type="text" data-validation="required" data-validation-error-msg="Invoice Serial Number Required" value="<?php echo $invoice_serial_no; ?>" <?php echo $disabled_input; ?> disabled> 
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Invoice Date</b></label>
												<div class="input-group">
													<input class="form-control" id="invoice_date" name="invoice_date" id="invoice_date" placeholder="Invoice Date" type="text" value="<?php echo $invoice_date; ?>"> 
													<span class="input-group-addon">
														<i class="fa fa-calendar-check-o"></i>
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Reference</b></label>
												<input class="form-control" placeholder="Reference PO,text etc.." name="invoice_reference" id="invoice_reference" type="text" value="<?php echo $invoice_reference; ?>"> 
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Due Date</b></label>
												<div class="input-group">
													<input class="form-control" id="due_date" name="due_date" placeholder="Due Date" type="text" value="<?php echo $invoice_due_date; ?>"> 
													<span class="input-group-addon">
														<i class="fa fa-calendar-check-o"></i>
													</span>
												</div>
											</div>
										</div>
									  </div>
                                      <div class="row">
										<div class="col-sm-6">
										 <div class="row">
											<div class="col-sm-12">
											<div class="form-group">
												<label><b>Customer Name</b></label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input data-validation="required" data-validation-error-msg="Customer Name Required" class="form-control" placeholder="Customer Name" id="customer_name" name="customer_name" type="text" value="<?php echo $customer_name; ?>"> 
													<input class="form-control" placeholder="Customer Name" id="customer_id" name="customer_id" type="hidden" value="<?php echo $customer_id; ?>"> 
													<span class="input-group-addon btn customer-add-btn">
														<i class="fa fa-plus"></i>
													</span>
												</div>
											</div>
											</div>
											<div class="row">
											<div class="col-sm-6">
											<div class="form-group">
												<div class="col-sm-3"><label class="lable-inline"><b>GSTIN</b></label></div>
												<div class="col-sm-9"><input class="form-control" id="gstin" name="gstin" placeholder="GSTIN" type="text" value="<?php echo $customer_gstin; ?>"> 
												</div>
											</div>
											</div>
										
										   <div class="col-sm-6">
												<div class="form-group">
													<div class="col-sm-5"><label class="lable-inline"><b>Place of supply</b></label></div>
													<div class="col-sm-7">
													<select class="form-control" name="place_of_supply" id="place_of_supply" placeholder="Place of supply" type="text">
														<option value="">Select State</option>
														<?php 
															if($states)
															{
																foreach($states as $state)
																{
																	?>
																	<option <?php echo ($state['slug']==$customer_place_of_supply)?"selected":""; ?> value="<?php echo $state['slug'] ?>">
																	<?php echo $state['name']; ?>
																	</option>
																	<?php 
																}
															}
														?>
													</select>
												</div>
												</div>
											
										</div>
										</div>
										</div>
									 
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Billing Address</b> &nbsp;<a class="" data-target="#billing-address-modal" data-toggle="modal">Edit</a></label>
												<p class="read-only-billing-address">
												
												</p> 
												<input name="billing_address" type="hidden" value="<?php echo $customer_billing_address; ?>">
												<input name="billing_pincode" type="hidden" value="<?php echo $customer_billing_pincode; ?>">
												<input name="billing_city" type="hidden" value="<?php echo $customer_billing_city; ?>">
												<input name="billing_state" type="hidden" value="<?php echo $customer_billing_state; ?>">
												<input name="billing_state_display" type="hidden" value="<?php echo $customer_billing_state_display; ?>">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label><b>Shipping Address</b> &nbsp;<a class="" data-target="#shipping-address-modal" data-toggle="modal">Edit</a></label>
												<p class="read-only-shipping-address">
												</p> 
												<input name="shipping_address" type="hidden" value="<?php echo $customer_shipping_address; ?>">
												<input name="shipping_pincode" type="hidden" value="<?php echo $customer_shipping_pincode; ?>">
												<input name="shipping_city" type="hidden" value="<?php echo $customer_shipping_city; ?>">
												<input name="shipping_state" type="hidden" value="<?php echo $customer_shipping_state; ?>">
												<input name="shipping_state_display" type="hidden" value="<?php echo $customer_shipping_state_display; ?>">
											</div>
											<div class="form-group">
												<label class="mt-checkbox">
													<input type="checkbox" class="same-as-billing-btn" id="same-as-billing-btn">Use same as billing address
													<span></span>
												</label>												
											</div>
										</div>
									  </div>
                                       <div class="row">
										</div>
									  <div class="table-scrollable table-list">
										<table class="table table-bordered table-hover" id="line_items_table">
											<thead>
												<tr>
													<th> # </th>
													<th> Item Description </th>
													<th> Item <br/>Type</th>
													<th> HSN <br/>Code</th>
													<th> Qty </th>
													<th> Rate<br/>(Rs.)</th>
													<th> Total </th>
													<th> Disc.<br/>(Rs.)</th>
													<th> Taxable<br/> Value </th>
													<th colspan="2" > CGST</th>
													<th colspan="2"> SGST</th>
													<th colspan="2"> IGST</th>
													<th colspan="2"> CESS</th>
													<th> Amount</th>
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
													<th></th>
													<th width="50px"> %</th>
													<th width="50px">Amt (Rs.)</th>
													<th > %</th>
													<th >Amt (Rs.)</th>
													<th > %</th>
													<th >Amt (Rs.)</th>
													<th > %</th>
													<th >Amt (Rs.)</th>
													<th></th>
													
												</tr>
											</thead>
											<tbody class="line_items">
												<?php 
															$row_count=1;
															$invoice_s_total=0;
															foreach($invoice_items as $key=>$item)
															{
																$s_total=$db->round($item['item_qty']* $item['item_unit_price']);
																$invoice_s_total+=$s_total;
															?>
															<tr class="error-container<?php echo $row_count ?>" data-count="<?php echo $row_count ?>">
																 <td><a class="remove-item-btn text-danger"><i class="fa fa-trash p2"></i></a></td>
																<td>
																	<input class="form-control table-input quick-item item-description" name="line_items[<?php echo $row_count ?>][description]" id="line_items[<?php echo $row_count ?>][description]" type="text" value="<?php echo $item['item_description']; ?>">
																	<input class="form-control table-input" type="hidden" name="line_items[<?php echo $row_count ?>][id]"  value="<?php echo $item['item_id']; ?>">
																</td>
																<td>
																	<select id="line_items[<?php echo $row_count ?>][gst_type]" name="line_items[<?php echo $row_count ?>][gst_type]" class="form-control table-input text-center item-type">
																		<option <?php echo ($item['item_type']=='GOODS')?"Selected":""; ?> value="GOODS">Goods</option>
																		<option <?php echo ($item['item_type']=='SERVICES')?"Selected":""; ?> value="SERVICES">Services</option>
																	</select>
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][gst_code]" name="line_items[<?php echo $row_count ?>][gst_code]"  placeholder="" class="form-control table-input text-center item-hsn" type="text" value="<?php echo $item['item_gst_code']; ?>">
																</td>
																<td>
																	<input autocomplete="off" data-validation="number" data-validation-allowing="float" id="line_items[<?php echo $row_count ?>][quantity]" name="line_items[<?php echo $row_count ?>][quantity]"  placeholder="" class="form-control table-input text-right numeric-input item-qty" type="text" value="<?php echo $item['item_qty']; ?>">
																	<input autocomplete="off" data-validation="number" data-validation-allowing="float" id="line_items[<?php echo $row_count ?>][stock_qty]" name="line_items[<?php echo $row_count ?>][stock_qty]"  placeholder="" class="form-control table-input text-right numeric-input item-stock-qty" type="hidden" value="<?php echo $item['item_stock_qty']; ?>">
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][unit_price]" name="line_items[<?php echo $row_count ?>][unit_price]"  placeholder="" class="form-control table-input text-right numeric-input item-rate" type="text" value="<?php echo $item['item_unit_price']; ?>">
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][stotal]" name="line_items[<?php echo $row_count ?>][stotal]"  placeholder="" class="form-control table-input text-right numeric-input item-stotal" type="text" value="<?php echo $s_total; ?>" disabled>
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][discount]" name="line_items[<?php echo $row_count ?>][discount]"  placeholder="" class="form-control table-input text-right numeric-input item-discount" type="text" value="<?php echo $item['item_discount']; ?>">
																</td>
																<td class=""> 
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][taxable_val]" name="line_items[<?php echo $row_count ?>][taxable_val]"  data-viewport=".error-container<?php echo $row_count ?>" placeholder="" class="form-control table-input text-right numeric-input item-taxable" type="text" disabled value="<?php echo $item['item_taxable_value']; ?>">
																</td>
																<td>
																	<select id="line_items[<?php echo $row_count ?>][cgst_rate]" name="line_items[<?php echo $row_count ?>][cgst_rate]" class="form-control table-input tax-cgst item-tax-rate" data-val="<?php echo $item['item_cgst_per']; ?>" >
																		
																		<?php 
																		if($tax_variants_for_cgst)
																		{
																			foreach($tax_variants_for_cgst as $tax_variant)
																			{
																				?>
																				<option <?php echo ($item['item_cgst_per']==$tax_variant['variant_value'])?"Selected":""; ?> value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>
																				<?php 
																			}
																		}
																		?>		
																	</select>
																</td>
																<td> 
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][cgst_val]" name="line_items[<?php echo $row_count ?>][cgst_val]"  placeholder="" class="form-control table-input text-right numeric-input  item-tax-amount tax-cgst-amount" disabled type="text" value="<?php echo $item['item_cgst_amount']; ?>">
																</td>
																<td>
																	<select id="line_items[<?php echo $row_count ?>][sgst_rate]" name="line_items[<?php echo $row_count ?>][sgst_rate]" class="form-control table-input tax-sgst input-tax-rate " data-val="<?php echo $item['item_sgst_per']; ?>" >   
																	<?php 
																		if($tax_variants_for_sgst)
																		{
																			foreach($tax_variants_for_sgst as $tax_variant)
																			{
																				?>
																				<option <?php echo ($item['item_sgst_per']==$tax_variant['variant_value'])?"Selected":""; ?> value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>
																				<?php 
																			}
																		}
																		?>
																	</select>
																</td>
																<td> 
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][sgst_val]" name="line_items[<?php echo $row_count ?>][sgst_val]"  placeholder="" class="form-control table-input text-right numeric-input  item-tax-amount tax-sgst-amount" type="text" value="<?php echo $item['item_sgst_amount']; ?>" disabled>
																</td>
																<td>
																	<select id="line_items[<?php echo $row_count ?>][igst_rate]" name="line_items[<?php echo $row_count ?>][igst_rate]" class="form-control table-input tax-igst item-tax-rate" data-val="<?php echo $item['item_igst_per']; ?>" >
																		<?php 
																		if($tax_variants_for_cgst)
																		{
																			foreach($tax_variants_for_igst as $tax_variant)
																			{
																				?>
																				<option <?php echo ($item['item_igst_per']==$tax_variant['variant_value'])?"Selected":""; ?> value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>
																				<?php 
																			}
																		}
																		?>
																	</select>
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][igst_val]" name="line_items[<?php echo $row_count ?>][igst_val]"  placeholder="" class="form-control table-input text-right numeric-input tax-igst-amount  item-tax-amount" type="text" value="<?php echo $item['item_igst_amount']; ?>" disabled>
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][cess_rate]" name="line_items[<?php echo $row_count ?>][cess_rate]"  placeholder="" class="form-control table-input text-center numeric-input item-tax-rate" type="text" value="<?php echo $item['item_cess_per']; ?>">
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][cess_val]" name="line_items[<?php echo $row_count ?>][cess_val]"  placeholder="" class="form-control table-input text-right numeric-input item-tax-amount" type="text" value="<?php echo $item['item_cess_amount']; ?>" disabled>
																</td>
																<td>
																	<input autocomplete="off" id="line_items[<?php echo $row_count ?>][total_val]" name="line_items[<?php echo $row_count ?>][total_val]"  placeholder="" disabled="" class="form-control table-input text-right numeric-input item-subtotal" type="text" value="<?php echo $item['item_subtotal']; ?>">
																</td>
															</tr>

                                                          
															<?php
															
															$row_count++;
															}?>
											</tbody>
											<tfoot >
												<tr>
													<th colspan="5"></th>
													<th class="item-discount">Total
													<th class="total-stotal text-right"> <?php echo $invoice_s_total; ?></th>
													<th class="total-discount text-right"> <?php echo $invoice_total_discount; ?></th>
													
													<th class="total-taxable text-right"> <?php echo $invoice_total_taxable_value; ?></th>
													<th> </th>
													<th class="total-cgst text-right"> <?php echo $invoice_total_cgst; ?></th>
													<th></th>
													<th class="total-sgst text-right"><?php echo $invoice_total_sgst; ?></th>
													<th></th>
													<th class="total-igst text-right"><?php echo $invoice_total_igst; ?></th>
													<th></th>
													<th class="total-cess text-right"><?php echo $invoice_total_cess; ?></th>
													<th class="grand-total text-right"><?php echo $invoice_grand_total; ?></th>
													
												</tr>
											</tfoot>
										</table>
										
									  </div>
                                    </div>
                                </div>
								</form>
								<!-- END EXAMPLE TABLE PORTLET-->
                            </div>
                        </div>
                    
									<!--- MODALS -->
								   <?php include('quick_invoice_modal.php'); ?>
									<!--- MODALS -->
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
var count_row=$(".table-list").find("tbody").find("tr").length+1;
$(function(){ 
  $("body").find(".numeric-input").numeric();
  $.validate({
		form : '#invoice-form', 
  	    onSuccess : function($form) {
			var valid=checkStock();
			
			return valid;
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
  initBillingAddress();
  initShippingAddress();
  createNewRow(count_row);
  toggleTax();
  assignAutoCompleteContact($("#customer_name"))	
  assignAutoComplete($(".line_items").find("tr").find('.quick-item'));		
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
	 $("input[name=billing_state_display]").val(billing_state_display);
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
	 $("input[name=shipping_state_display]").val(shipping_state_display);
	 $("#shipping-address-modal").modal('hide');
	
 });
  $(".line_items").on("click",".remove-item-btn",function(){
		var isConfirm=confirm("Are you sure you want to delete this item?");
		if(isConfirm)
		{
			$(this).closest("tr").remove();
			toastr.success("Item Deletetd successfully");
			var row_count=$(".line_items").find(".quick-item").length;
			if(row_count==0)
			{
				createNewRow(count_row);
			}
			calculateTotal();
		}
	
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
		 //var row_count=$(".line_items").find(".quick-item").length+1;
		 createNewRow(count_row);
	 }		 
  });
  $(".line_items").on("change","input,select",function(){
	  calculateTotal()	 
  });
  $("#place_of_supply").on("change",function(){
	  toggleTax();
  });
  $("#shipping-address-modal").on('show.bs.modal',function(){
	  var shipping_address= $("input[name=shipping_address]").val();
	  var shipping_pincode=$("input[name=shipping_pincode]").val();
	  var shipping_city= $("input[name=shipping_city]").val();
	  var shipping_state=$("input[name=shipping_state]").val();
		$("#edit_shipping_address").val(shipping_address);
		$("#edit_shipping_pincode").val(shipping_pincode);
		$("#edit_shipping_city").val(shipping_city);
		$("#edit_shipping_state").val(shipping_state);
  });
  $("#billing-address-modal").on('show.bs.modal',function(){
    	var billing_address= $("input[name=billing_address]").val();
		var billing_pincode=$("input[name=billing_pincode]").val();
		var billing_city= $("input[name=billing_city]").val();
		var billing_state=$("input[name=billing_state]").val();
		$("#edit_billing_address").val(billing_address);
		$("#edit_billing_pincode").val(billing_pincode);
		$("#edit_billing_city").val(billing_city);
		$("#edit_billing_state").val(billing_state);
  });
 })
 function copyBillingAddress(){
	 var billing_address= $("input[name=billing_address]").val();
	 var billing_pincode=$("input[name=billing_pincode]").val();
	 var billing_city= $("input[name=billing_city]").val();
	 var billing_state=$("input[name=billing_state]").val();
	 var billing_state_display=$("input[name=billing_state_display]").val();
	 $("input[name=shipping_address]").val(billing_address);
	 $("input[name=shipping_pincode]").val(billing_pincode);
	 $("input[name=shipping_city]").val(billing_city);
	 $("input[name=shipping_state]").val(billing_state);
	 $("input[name=shipping_state_display]").val(billing_state_display);
	 initShippingAddress();
 }
 function initBillingAddress(){
	 
	 var billing_address= $("input[name=billing_address]").val();
	 var billing_pincode=$("input[name=billing_pincode]").val();
	 var billing_city= $("input[name=billing_city]").val();
	 var billing_state=$("input[name=billing_state]").val();
	 var billing_state_display=$("input[name=billing_state_display]").val();
	 var final_readonly_address=(billing_address!="")?billing_address+"<br/>":"";
	 var final_readonly_address=final_readonly_address+((billing_city!="")?billing_city+"<br/>":"");
	 var final_readonly_address=final_readonly_address+((billing_state_display!="")?billing_state_display:"");
	 var final_readonly_address=final_readonly_address+((billing_pincode!="")?"-"+billing_pincode:"");
	 
	 $(".read-only-billing-address").html(final_readonly_address);	 
 }
 function initShippingAddress(){
	 var shipping_address= $("input[name=shipping_address]").val();
	 var shipping_pincode=$("input[name=shipping_pincode]").val();
	 var shipping_city= $("input[name=shipping_city]").val();
	 var shipping_state=$("input[name=shipping_state]").val();
	 var shipping_state_display=$("input[name=shipping_state_display]").val();
	 
	 var final_readonly_address=(shipping_address!="")?shipping_address+"<br/>":"";
	 var final_readonly_address=final_readonly_address+((shipping_city)?shipping_city+"<br/>":"");
	 var final_readonly_address=final_readonly_address+((shipping_state_display)?shipping_state_display:"");
	 var final_readonly_address=final_readonly_address+((shipping_pincode!="")?"-"+shipping_pincode:"");
	 
	 $(".read-only-shipping-address").html(final_readonly_address);
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
				  beforeSend:function(){
					    var index=$(node).closest("tr").data("count"); 
						var parent=$(node).closest("tr");
						var item_id_input=$(parent).find('input[name="line_items['+index+'][id]"]');
						$(item_id_input).val(0);
						var stock_qty_input=$(parent).find('input[name="line_items['+index+'][stock_qty]"]');
							$(stock_qty_input).val(0);
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
				var index=$(this).closest("tr").data("count"); 
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
				var place_of_supply=$("#place_of_supply").val();
				if(place_of_supply=="GUJARAT")
				{
					var cgst_input=$(parent).find('select[name="line_items['+index+'][cgst_rate]"]');
					$(cgst_input).val((ui.item.cgst_rate));
					var sgst_input=$(parent).find('select[name="line_items['+index+'][sgst_rate]"]');
					$(sgst_input).val((ui.item.sgst_rate));
					var igst_input=$(parent).find('select[name="line_items['+index+'][igst_rate]"]');
					$(igst_input).attr("data-val",(ui.item.igst_rate));
					$(cgst_input).attr("data-val",(ui.item.cgst_rate));
					$(sgst_input).attr("data-val",(ui.item.sgst_rate));
				}
				else
				{
					var cgst_input=$(parent).find('select[name="line_items['+index+'][cgst_rate]"]');
					$(cgst_input).attr("data-val",(ui.item.cgst_rate));
					var sgst_input=$(parent).find('select[name="line_items['+index+'][sgst_rate]"]');
					$(sgst_input).attr("data-val",(ui.item.sgst_rate));
					var igst_input=$(parent).find('select[name="line_items['+index+'][igst_rate]"]');
					$(igst_input).val((ui.item.igst_rate));
					$(igst_input).attr("data-val",(ui.item.igst_rate));
					$(cgst_input).attr("data-val",(ui.item.cgst_rate));
					$(sgst_input).attr("data-val",(ui.item.sgst_rate));
	
				}
				var cess_input=$(parent).find('input[name="line_items['+index+'][cess_rate]"]');
				$(cess_input).val((ui.item.cess_rate));
				var quantity_input=$(parent).find('input[name="line_items['+index+'][quantity]"]');
				$(quantity_input).val(1);
				var stock_qty_input=$(parent).find('input[name="line_items['+index+'][stock_qty]"]');
				$(stock_qty_input).val(ui.item.stock_qty);
				calculateTotal();
				
			  },
		open: function(event, ui) {
		$('.ui-autocomplete').append('<li><a class="item-add-btn text-primary text-center">Create New Item</a></li>');} ,
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
					$("input[name=gstin]").val(ui.item.gstin);
					$("input[name=billing_address]").val(ui.item.address);
					$("input[name=billing_city]").val(ui.item.city);
					$("input[name=billing_pincode]").val(ui.item.zipcode);
					$("input[name=billing_state]").val(ui.item.state);
					$("input[name=billing_country]").val(ui.item.country);
					$("input[name=billing_state_display]").val(ui.item.state_display);
					initBillingAddress();
			  },
		open: function(event, ui) {
		$('.ui-autocomplete').append('<li><a class="customer-add-btn">Create New Customer or Vendor</a></li>');} ,
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
	 var tableRow='<tr class="error-container'+row_count+'" data-count="'+row_count+'">'+
					'<td><a class="remove-item-btn text-danger"><i class="fa fa-trash p2"></i></a></td>'+
					'<td><input class="form-control table-input quick-item item-description" name="line_items['+row_count+'][description]" id="line_items['+row_count+'][description]" type="text"><input class="form-control table-input" type="hidden" name="line_items['+row_count+'][id]"></td>'+
					'<td>'+
					'<select id="line_items['+row_count+'][gst_type]" name="line_items['+row_count+'][gst_type]" class="form-control table-input text-center item-type">'+
					'<option value="GOODS">Goods</option>'+
					'<option value="SERVICES">Services</option>'+
					'</select>'+
					'</td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][gst_code]" name="line_items['+row_count+'][gst_code]" value="" placeholder="" class="form-control table-input text-center item-hsn" type="text"></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][quantity]" name="line_items['+row_count+'][quantity]" value="" placeholder="" class="form-control table-input text-right numeric-input item-qty" type="text"><input autocomplete="off" id="line_items['+row_count+'][stock_qty]" name="line_items['+row_count+'][stock_qty]" value="" placeholder="" class="form-control table-input text-right numeric-input item-stock-qty" type="hidden"></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][unit_price]" name="line_items['+row_count+'][unit_price]" value="" placeholder="" class="form-control table-input text-right numeric-input item-rate" type="text"></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][stotal]" name="line_items['+row_count+'][stotal]" value="" placeholder="" class="form-control table-input text-right numeric-input item-stotal" type="text" disabled></td>'+
					'<td><input autocomplete="off" id="line_items['+row_count+'][discount]" name="line_items['+row_count+'][discount]" value="" placeholder="" class="form-control table-input text-right numeric-input item-discount" type="text"></td>'+
					'<td class=""> <input autocomplete="off" id="line_items['+row_count+'][taxable_val]" name="line_items['+row_count+'][taxable_val]" value="" data-viewport=".error-container'+row_count+'" placeholder="" class="form-control table-input text-right numeric-input item-taxable" type="text" disabled></td>'+
					'<td><select id="line_items['+row_count+'][cgst_rate]" name="line_items['+row_count+'][cgst_rate]" class="form-control table-input tax-cgst item-tax-rate" data-val="0" >'+
						<?php 
						if($tax_variants_for_cgst)
						{
							foreach($tax_variants_for_cgst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					'</select>'+
					'</td>'+
					'<td> <input autocomplete="off" id="line_items['+row_count+'][cgst_val]" name="line_items['+row_count+'][cgst_val]" value="" placeholder="" class="form-control table-input text-right numeric-input item-tax-amount tax-cgst-amount" type="text" disabled></td>'+
					'<td><select id="line_items['+row_count+'][sgst_rate]" name="line_items['+row_count+'][sgst_rate]" class="form-control table-input tax-sgst input-tax-rate" data-val="0">'+
					<?php 
					if($tax_variants_for_sgst)
					{
						foreach($tax_variants_for_sgst as $tax_variant)
						{
							?>
							'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
							<?php 
						}
					}
					?>
					'</select></td>'+
					'<td> <input autocomplete="off" id="line_items['+row_count+'][sgst_val]" name="line_items['+row_count+'][sgst_val]" value="" placeholder="" class="form-control table-input text-right numeric-input tax-sgst-amount item-tax-amount" disabled type="text"></td>'+
					'<td><select id="line_items['+row_count+'][igst_rate]" name="line_items['+row_count+'][igst_rate]" class="form-control table-input tax-igst item-tax-rate" data-val="0">'+
					<?php 
					if($tax_variants_for_igst)
					{
						foreach($tax_variants_for_igst as $tax_variant)
						{	?>
							'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
							<?php 
						}
					}
					?>
					'</select></td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][igst_val]" name="line_items['+row_count+'][igst_val]" value="" placeholder="" class="form-control table-input text-right numeric-input tax-igst-amount item-tax-amount" type="text" disabled>'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][cess_rate]" name="line_items['+row_count+'][cess_rate]" value="" placeholder="" class="form-control table-input text-center numeric-input item-tax-rate tax-cess-amount"  type="text">'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][cess_val]" name="line_items['+row_count+'][cess_val]" value="" placeholder="" class="form-control table-input text-right numeric-input item-tax-amount" type="text" disabled="">'+
					'</td>'+
					'<td>'+
					'<input autocomplete="off" id="line_items['+row_count+'][total_val]" name="line_items['+row_count+'][total_val]" value="" placeholder="" disabled="" class="form-control table-input text-right numeric-input item-subtotal" type="text">'
					'</td>'+
				'</tr>';
		$(".line_items").append(tableRow);
		count_row++;
		$(".numeric-input").numeric();
		assignAutoComplete($(".line_items").find("tr:last-child").find('.quick-item'));	
		toggleTax();	
 }
 function calculateTotal(){
	 var quick_item_total_taxable=0;
	 var quick_item_total_cgst=0;
	 var quick_item_total_sgst=0;
	 var quick_item_total_igst=0;
	 var quick_item_total_cess=0;
	 var quick_item_grand_total=0;
	 var quick_item_total_discount=0;
	 var quick_item_stotal=0;
	 checkStock();
	 $(".line_items").find("tr").each(function(){
		total_cgst=0;
		total_sgst=0;
		total_igst=0;
		total_cess=0;
		 var index=$(this).data("count");
		var parent=$(this);
		 var quantity_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[quantity\\]]');
		 var quantity=$(quantity_input).val();
		 quantity=(quantity!="")?quantity:0;
		 var unit_price_input=$(parent).find('input[name="line_items['+index+'][unit_price]"]');
		 var unit_price=$(unit_price_input).val();
		 unit_price=(unit_price!="")?unit_price:0;
		 var discount_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[discount\\]]');
		 var discount=$(discount_input).val();
		 discount=(discount!="")?discount:0;
		 var total_taxable_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[taxable_val\\]]');
		 var total_taxable_stotalinput=$(parent).find('input[name=line_items\\['+index+'\\]\\[stotal\\]]');
		 var total_taxable=aj.roundNumber((parseFloat(quantity)*parseFloat(unit_price))-discount);
		 var s_total=aj.roundNumber(parseFloat(quantity)*parseFloat(unit_price));
		 total_taxable=(total_taxable<0)?0:total_taxable;
		 $(total_taxable_input).val(total_taxable);
		 $(total_taxable_stotalinput).val(s_total);
		 
		 var place_of_supply=$("#place_of_supply").val();
		 if(place_of_supply=="GUJARAT")
		 {
			 var cgst_rate_input=$(parent).find('select[name=line_items\\['+index+'\\]\\[cgst_rate\\]]');
			 var cgst_value_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[cgst_val\\]]');
			 var cgst_rate=$(cgst_rate_input).val();
			 cgst_rate=(cgst_rate!="" && cgst_rate!=undefined)?cgst_rate:0;
			 total_cgst=aj.roundNumber((parseFloat(cgst_rate)*parseFloat(total_taxable))/100);
			 $(cgst_value_input).val(total_cgst);
			
			 var sgst_rate_input=$(parent).find('select[name=line_items\\['+index+'\\]\\[sgst_rate\\]]');
			 var sgst_value_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[sgst_val\\]]');
			 var sgst_rate=$(sgst_rate_input).val();
			 sgst_rate=(sgst_rate!="" && sgst_rate!=undefined)?sgst_rate:0;
			 total_sgst=aj.roundNumber((parseFloat(sgst_rate)*parseFloat(total_taxable))/100);
			 $(sgst_value_input).val(total_sgst);
			 
			 quick_item_total_cgst=aj.roundNumber(quick_item_total_cgst+total_cgst);
			 quick_item_total_sgst=aj.roundNumber(quick_item_total_sgst+total_sgst);
			 
		 }
		 else
		 {
			 var igst_rate_input=$(parent).find('select[name=line_items\\['+index+'\\]\\[igst_rate\\]]');
			 var igst_value_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[igst_val\\]]');
			 var igst_rate=$(igst_rate_input).val();
			 igst_rate=(igst_rate!="" && igst_rate!=undefined)?igst_rate:0;
			 total_igst=aj.roundNumber((parseFloat(igst_rate)*parseFloat(total_taxable))/100);
			 $(igst_value_input).val(total_igst);
			 quick_item_total_igst=quick_item_total_igst+total_igst;
		}
		 
		
    	 var cess_rate_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[cess_rate\\]]');
		 var cess_value_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[cess_val\\]]');
		 var cess_rate=$(cess_rate_input).val();
		 var cess_rate=(cess_rate!="")?cess_rate:0;
		 var total_cess=aj.roundNumber((parseFloat(cess_rate)*parseFloat(total_taxable))/100);		  
		 $(cess_value_input).val(total_cess);
		 quick_item_total_cess=aj.roundNumber(quick_item_total_cess+total_cess);
		 var grand_total_input=$(parent).find('input[name=line_items\\['+index+'\\]\\[total_val\\]]');
		 var total_val=aj.roundNumber(total_taxable+total_cgst+total_sgst+total_igst+total_cess);
		 $(grand_total_input).val(total_val);
		 quick_item_total_taxable=aj.roundNumber(quick_item_total_taxable+total_taxable);
		 quick_item_total_discount=quick_item_total_discount+aj.roundNumber(parseFloat(discount));
		 quick_item_stotal=aj.roundNumber(quick_item_stotal+s_total);
		 
	 })
	 $(".total-taxable").html(quick_item_total_taxable);
	 $(".total-discount").html(quick_item_total_discount);
	 $(".total-stotal").html(quick_item_stotal);
	 $(".total-cgst").html(quick_item_total_cgst);
	 $(".total-sgst").html(quick_item_total_sgst);
	 $(".total-igst").html(quick_item_total_igst);
	 $(".total-cess").html(quick_item_total_cess);
	 quick_item_grand_total=aj.roundNumber(quick_item_total_taxable+quick_item_total_cgst+quick_item_total_sgst+quick_item_total_igst+quick_item_total_cess);
	 $(".grand-total").html(quick_item_grand_total);
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
 function toggleTax(){
	 var place_of_supply=$("#place_of_supply").val();
	 if(place_of_supply=="GUJARAT")
	 {
		 $(".tax-cgst").removeAttr("disabled");
		 $(".tax-sgst").removeAttr("disabled");
		 $(".tax-igst-amount").val("0");
		 $(".tax-igst").attr("disabled","disabled");
		 $(".tax-igst").val(0);
		 $(".tax-sgst").each(function(i,tax){
			var pre_selected=$(this).attr("data-val");
		 
			 if(pre_selected!="" && pre_selected!=undefined)
			 $(this).val(pre_selected);
			 else
			 $(this).val(0);
				
		 }) ;
		 $(".tax-cgst").each(function(i,tax){
			 var pre_selected=$(this).attr("data-val");
			 if(pre_selected!="" && pre_selected!=undefined)
			 $(this).val(pre_selected);
			 else
			 $(this).val(0);
				
		 });
	 }
	 else
	 {
		 $(".tax-sgst-amount").val("0");
		 $(".tax-cgst-amount").val("0");
		 $(".tax-igst").removeAttr("disabled");
		 $(".tax-cgst").attr("disabled","disabled");
		 $(".tax-sgst").attr("disabled","disabled");
		 $(".tax-cgst").val(0);
		 $(".tax-sgst").val(0);
		 $(".tax-igst").each(function(i,tax){	
			 
			 var pre_selected=$(this).attr("data-val");
			 if(pre_selected!="" && pre_selected!=undefined)
			 $(this).val(pre_selected);
			 else
			 $(this).val(0);
				
		 });
	 }
	 
	 
	 calculateTotal();
 }
 function checkStock(){
	 var valid=true;
	 $(".line_items").find("tr").each(function(){
		var index=$(this).data("count");
		var parent=$(this);
		var quantity_input=$(parent).find('input[name="line_items['+index+'][quantity]"]');
		var qty=parseFloat($(quantity_input).val());
		var stock_qty_input=$(parent).find('input[name="line_items['+index+'][stock_qty]"]');
		var stock_qty=parseFloat($(stock_qty_input).val());
		if(qty>stock_qty)
		{
			
			var _popover;
			var message="Qty not available in stock. Available stock qty. "+stock_qty;
			_popover = $(quantity_input).popover({
				trigger: "manual,click",
				placement: "top",
				content: message,
				template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
			});
			//_popover.data("popover").options.content = message;
			//$(quantity_input).popover('show');
			$(quantity_input).addClass('has-error');
			valid=false;
		}
		else
		{
			$(quantity_input).removeClass('has-error');
			$(quantity_input).popover("hide");
			$(quantity_input).popover("destroy");
		}
	});
	return valid;
 }
 
 </script>
 <!-- END PAGELEVEL JS -->

</body>
</html>	