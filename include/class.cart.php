<?php
class AndroidCart extends Functions
{
	public $detail=array();
	public $db,$application,$user,$nt;
	public $orderStatus = array("Cancelled","In Cart","Completed","Shipped","Delivered");
	function __construct($id="") 
	{
		require_once('cart.class.php');
		require_once('class.user.php');
		require_once('class.application.php');
		require_once('notification.class.php');
		$db = new Functions();
		$conn = $db->connect();
		$this->cart=new Cart();		  
		$this->db=$db;		  
		$this->user=new User();		  
		$this->application=new application();		  
		$this->nt=new Notification();		  
    }

	function addItemToCart($detail)
	{
		
		// Add new item to cart
		$cart_id=$this->getUserCartId($detail);
		$detail['cid']=$cart_id;
		if($cart_id!=0)
		{
			
			$detail['pincode']=($detail['pincode']=="")?0:$detail['pincode'];
			$check_qty = $this->cart->rp_checkQtyToAddInCart($detail['cid'],$detail['pid'],$detail['qty'],1,0);
			if($check_qty!=0)
			{			
				$product_detail = $this->application->getProduct(array($detail['pid']));				
				if($product_detail['ack']==1)
				{
					$product_detail=$product_detail['result'][0];
					$pro_tax		= $product_detail['pro_tax'];
					$name 		= $product_detail['name'];					
					$seller_id 		= $product_detail['seller_id'];					
					$product_status = $product_detail['status'];
					$product_attr=$detail['attr'];
					if($product_status==0){
						$check_duplicate_product_in_cart = $this->db->rp_getTotalRecord("cartitems","cart_id = '".$detail['cid']."' AND pid='".$detail['pid']."' AND attr='".$detail['attr']."'");
						if($check_duplicate_product_in_cart>0){
														
							// If Product already in cart then update quantity
							$cqty = $this->db->rp_getValue("cartitems","qty","cart_id = '".$detail['cid']."' AND pid='".$detail['pid']."' AND attr='".$detail['attr']."'");			
							$qty 		= intval($cqty + $detail['qty']);// New Quantity
							$totalprice = $this->db->rp_num($qty*$product_detail['sell_price']);							
							$ship_charge= $this->db->rp_num($this->db->rp_getShippingCharge($detail['pincode'],$detail['pid'])*$detail['qty']);
							$ship_days	= $this->db->rp_getValue("product","ship_days","id='".$detail['pid']."'");
							$cirows 	= array(
									"uid"		=> $detail['uid'],
									"name"		=> $name,
									"qty"		=> $qty,
									"ship_charge"=>$ship_charge,
									"ship_days" => $ship_days,
									"unitprice"	=> $product_detail['sell_price'],
									"pro_tax"	=> $pro_tax,
									"totalprice"=> $totalprice,
								);
							$ciwhere	= "pid = '".$detail['pid']."' AND cart_id='".$detail['cid']."' AND attr='".$detail['attr']."'";
							
							if($this->db->rp_update("cartitems",$cirows,$ciwhere,0))
							{
								$this->cart->aj_updateSubTotalCart($detail['cid']);
								$reply=array('ack'=>1,'developer_msg'=>'duplicate item qty updated','ack_msg'=>"Product already in cart quantity updated!!");
								return $reply;
							}
							else
							{
								$reply=array('ack'=>0,'developer_msg'=>'Database Error!!','ack_msg'=>"Internal Error!! Product couldn't added to cart.");
								return $reply;
							}
						}
						else
						{
							
							// Insert New Cart Item
							$ship_charge= $this->db->rp_num($this->db->rp_getShippingCharge($detail['pincode'],$detail['pid'])*$detail['qty']);
							$ship_days	= $this->db->rp_getValue("product","ship_days","id='".$detail['pid']."'");
							$totalprice = $this->db->rp_num($detail['qty']*$product_detail['sell_price']);
							$adate = $this->today();
							$cirows 	= array(
									"cart_id",
									"seller_id",
									"uid",
									"pid",
									"attr",		
									"name",																		
									"qty",											
									"ship_charge",
									"ship_days",
									"unitprice",
									"pro_tax",
									"totalprice",
									"orderstatus",
									"adate",
								);
							$civalues = array(
									$detail['cid'],
									$seller_id,
									$detail['uid'],
									$detail['pid'],
									$detail['attr'],
									$name,
									$detail['qty'],
									$ship_charge,
									$ship_days,
									$product_detail['sell_price'],
									$pro_tax,
									$totalprice,
									"1",// In Progress
									$adate,
								);
							if($this->db->rp_insert("cartitems",$civalues,$cirows,0))
							{
								$this->cart->aj_updateSubTotalCart($cart_id);
								$reply=array('ack'=>1,'developer_msg'=>'Product added to cart!!','ack_msg'=>"Product added to cart!!");								
								return $reply;
							}
							else
							{
								$reply=array('ack'=>0,'developer_msg'=>'Database Error!!','ack_msg'=>"Internal Error!! Product couldn't added to cart.");
								return $reply;
							}
						}
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Product not available either waiting for approval or drafted!!","ack_msg"=>"Product not available!! Try Later.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Product not found either deleted or wrong product id!!","ack_msg"=>"Product couldn't added to cart either it is removed or suspended.");
					return $reply;
				}
								
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Product out of stock!!","ack_msg"=>"Product out of stock!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Neither cart found nor cart created!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	
	}	
	function removeItemFromCart($detail)
	{
		$cart_id=$this->getUserCartId($detail);
		$detail['cid']=$cart_id;		
		if($cart_id!=0)
		{								
			$where="id='".$detail['ciid']."'";
			$countItem=$this->db->rp_getTotalRecord("cartitems",$where);
			if($countItem>=1)
			{
				if($this->db->rp_delete("cartitems",$where,0))
				{				
					$total_cart_price=$this->cart->aj_getCartSubTotalPrice($cart_id);
					$this->cart->aj_updateSubTotalCart($cart_id);
					$cartdetails=$this->getUserCartDetail(array("uid"=>$detail['uid']));
					$reply=array("ack"=>1,"ack_msg"=>"Item Successfully Removed From Cart!!","result"=>$cartdetails);
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Cart item not found its like you already removed it !!","ack_msg"=>"Cart item already removed or cart shipped or removed . Cart already shipped or cancelled!!");
				return $reply;
			}
			
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Cart found !!","ack_msg"=>"Cart item couldn't removed . Cart already shipped or cancelled!!");
			return $reply;
		}
	}
	function cancelOrder($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			/*$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;*/
			$cart_id=$detail['cid'];
			$countCart=$this->db->rp_getTotalRecord("cartdetails","cart_id='".$cart_id."'",0);			
			if($countCart>=1)
			{
				$where="cart_id='".$cart_id."'";
				if($cart_id!="" || $cart_id!=0)
				{
					$reason 	= $this->db->clean($detail['reason']);	
					$orderstatus= $this->db->rp_getValue('cartdetails',"orderstatus","cart_id='".$cart_id."'");
					if($orderstatus!=0 && $orderstatus!=1 && $orderstatus!=4 )
					{
						$adate	= date('Y-m-d H:i');						
						$amount			= $this->db->rp_getValue("cartdetails","finaltotal","cart_id='".$cart_id."'");						
						$order_status 	= "";
						/**Update Order Status Starts**/
						$rows 	= array(							
								"orderstatus"	=> "0",
								"notes"	=> $reason,
							);
						$this->db->rp_update("cartdetails",$rows,$where,0);
						/**Update Order Status Ends**/
						
						/**Cartitems Order Status Update Starts**/
						$cirows 	= array("orderstatus"	=> "0");
						$this->db->rp_update("cartitems",$cirows,$where,0);
						$order_id="#".$cart_id;
						$cartDetails=mysql_fetch_assoc($this->db->rp_getData("cartdetails","name,email,phone,finaltotal",$where));
						$name=$cartDetails['name'];
						$amount=$cartDetails['finaltotal'];
						$email=$cartDetails['email'];
						$cartPhone=$cartDetails['phone'];
						$userPhone=$this->db->rp_getValue("user","phone","id='".$detail['uid']."'");
						$shipping_date=date('D, d M Y', strtotime("+7 days"));						
						$this->sendCancelAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount);				
						$reply=array("ack"=>1,"ack_msg"=>"Order Successfully cancelled!! You will get SMS and Email for confirmation!!");
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"ack_msg"=>"Something went wrong with cart!! cart already have been cancelled or delivered!!");
						return $reply;
					}			
								
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found or status changed!!","ack_msg"=>"Your cart removed or already shipped!!");
				return $reply;
			}
			
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function getCoupan($coupan_code="",$required_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();				
		$where="";
		$coupans=false;
		if($coupan_code!="")
		{
			$where=$this->db->generateWhere($where,"coupon_code='".$coupan_code."'");
						
		}
		if($where!="")
		$where.=" AND isDelete=0 AND (valid_from <= NOW() AND valid_to >= NOW())";
		else
		$where.="isDelete=0 AND (valid_from <= NOW() AND valid_to >= NOW())";	
	
		$coupans_r=$this->db->rp_getData("coupon_code",$required_columns,$where,"",0);
		if($coupans_r)
		{
			while($r=mysql_fetch_assoc($coupans_r))
			{
				$coupans[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$coupans,"developer_msg"=>"Coupans found in database.","ack_msg"=>"Great !!Coupans fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no coupans found or expired.","ack_msg"=>"Sorry !! No Coupans found or expired.");
			return $reply;
		}
	
	}
	function applyCoupan($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;					
			if($cart_id!="" || $cart_id!=0)
			{
				if($detail['coupan']!="")
				{
										
					/******Get Coupon Data*******/
					$coupan_r=$this->getCoupan($detail['coupan']);
					if($coupan_r['ack']==1){
						$coupan_detail=$coupan_r['result'][0];
						$coupon_id	= $coupan_detail['id'];
						$cat_type	= stripslashes($coupan_detail['cat_type']);
						$cat_id		= explode(",",$coupan_detail['cat_id']);
						$disc_type 	= stripslashes($coupan_detail['disc_type']);
						$discount	= $this->db->rp_num($coupan_detail['discount']);
						$min_amount = $this->db->rp_num($coupan_detail['min_amount']);
						/******Get Shoppoing Cart Data and Update Cartitems*******/
							$shop_cart_r = $this->db->rp_getData("cartitems","*","cart_id='".$cart_id."'");
							$cc = 0;
							while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
								$id 		= $shop_cart_d['id'];
								$pid 		= $shop_cart_d['pid'];
								$totalprice = $this->db->rp_num($shop_cart_d['totalprice']);
								
								$pro_r 		= $this->db->rp_getData("product","*","id='".$pid."'");
								$pro_d 		= mysql_fetch_array($pro_r);
								
								$pro_bid	= stripslashes($pro_d["bid"]);
								$pro_cid	= stripslashes($pro_d["cid"]);
								$pro_sid	= stripslashes($pro_d["sid"]);
								$pro_ssid	= stripslashes($pro_d["ssid"]);
								
								if($cat_type==0){ //All
									$discount_amt = $this->cart->rp_getDiscountAmount($disc_type,$discount,$totalprice);
									$cc++;
								}else if($cat_type==1){ //Category
									if(in_array($pro_cid,$cat_id)){
										$discount_amt = $this->cart->rp_getDiscountAmount($disc_type,$discount,$totalprice);
										$cc++;
									}
								}else if($cat_type==2){ //Sub Category
									if(in_array($pro_sid,$cat_id)){
										$discount_amt = $this->cart->rp_getDiscountAmount($disc_type,$discount,$totalprice);
										$cc++;
									}
								}else if($cat_type==3){ //Sub Sub Category
									if(in_array($pro_ssid,$cat_id)){
										$discount_amt = $this->cart->rp_getDiscountAmount($disc_type,$discount,$totalprice);
										$cc++;
									}
								}else if($cat_type==4){
									if(in_array($pro_bid,$cat_id)){
										$discount_amt = $this->db->rp_getDiscountAmount($disc_type,$discount,$totalprice);
										$cc++;
									}
								}
								
								
								$cartitem_rows 	= array(
										"coupon_id"	=> $coupon_id,
										"discount"	=> $discount_amt,
									);
									
								$where	= "cart_id='".$cart_id."' AND id='".$id."'";
								$this->db->rp_update("cartitems",$cartitem_rows,$where);
								
							}
						/******Get Shopping Cart Data and Update Cartitems*******/
						
						/******Get Shopping Cart Data and Update Cartitems*******/
						if($cc>0){
							/*********Update CartDetail*********/
							$shop_cart_r = $this->db->rp_getData("cartitems","*","cart_id='".$cart_id."'");
							$totalprice = 0;
							$discount 	= 0;
							$sub_total	= 0;
							$pid_ids	= "";
							$total_ship_charge= 0;
							$tax=0;
							if($shop_cart_r){			
								
								while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
									$id 		= $shop_cart_d['id'];
									$pid 		= $shop_cart_d['pid'];
									$subpid 	= $shop_cart_d['subpid'];							
									$qty 		= $shop_cart_d['qty'];
									$ship_charge= $this->db->rp_num($shop_cart_d["ship_charge"]);
									$total_ship_charge += $ship_charge;
									$ship_days	= intval($shop_cart_d["ship_days"]);
									$unitprice 	= $this->db->rp_num($shop_cart_d['unitprice']);
									$discount	+= $shop_cart_d['discount'];
									$totalprice = $this->db->rp_num($shop_cart_d['totalprice']);
									$sub_total 	+= $totalprice;
									$pro_r = mysql_fetch_assoc($this->db->rp_getData("product","*","id='".$pid."'"));											
									$tax+=(($totalprice*$pro_r['pro_tax'])/100);
									
								}
							}					
							$sub_total 			= $this->db->rp_num($sub_total);
							$shipping_charge 	= $this->db->rp_num($total_ship_charge);
							$shipping_discount 	= $this->db->rp_num($this->cart->rp_getShippingDiscount($sub_total,$shipping_charge));
							$tax 				= $this->db->rp_num($tax);
							// if tax is excluded then add tax to final total here...
							$final_total 		= $this->db->rp_num(($sub_total + $shipping_charge) - $discount - $shipping_discount);					
							$isUpdated=$this->db->rp_update("cartdetails",array(
															"coupon_id"		=> $coupon_id,
															"coupon_code"	=> $detail['coupan'],
															"total_ship_charge"=> $total_ship_charge,									
															"total_shipping_discount"	=> $shipping_discount,
															"subtotal"		=> $sub_total,
															"finaltotal"	=> $final_total,
															),"cart_id='".$detail['cid']."'");
							if($isUpdated==1)
							{
								$result=$this->getUserCartDetail($detail,array("cart_id","uid","orderstatus","subtotal","coupon_id","coupon_code","total_shipping_discount","finaltotal","zip"),array(),array(),false);								
								$reply=array("ack"=>1,"developer_msg"=>"Coupan Applied Successfully!!","ack_msg"=>"Coupan Applied Successfully!!","result"=>$result['result']);
								return $reply;					
							}
							else
							{
								$reply=array("ack"=>0,"developer_msg"=>"Error While Applying Coupan Code!!","ack_msg"=>"Error while applying coupan code!!");
								return $reply;
							}						
						}else{
							$reply=array("ack"=>0,"developer_msg"=>"Error While Applying Coupan Code!!","ack_msg"=>"Error while applying coupan code!!");
							return $reply;
						}
						
					}
					else{
					
						$reply=array("ack"=>0,"developer_msg"=>"not valid coupan code!!","ack_msg"=>"Not valid coupan code or coupan expire!!");
						return $reply;
					}
					/******Get Coupon Data*******/											
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"coupan code not found!!","ack_msg"=>"Not Valid Coupan Code!!");
					return $reply;
				}				
							
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function removeCoupan($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;					
			if($cart_id!="" || $cart_id!=0)
			{						
				/******Get Shoppoing Cart Data and Update Cartitems*******/
				$shop_cart_r = $this->db->rp_getData("cartitems","*","cart_id='".$cart_id."'");
				if($shop_cart_r)
				{
						while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
						$id 		= $shop_cart_d['id'];
						$cartitem_rows 	= array(
								"coupon_id"	=> "",
								"discount"	=> 0,
							);
							
						$where	= "cart_id='".$cart_id."' AND id='".$id."'";
						$this->db->rp_update("cartitems",$cartitem_rows,$where);
											
					}
				}					
				
				/*********Update CartDetail*********/
				$shop_cart_r = $this->db->rp_getData("cartitems","*","cart_id='".$cart_id."'");
				$totalprice = 0;
				$discount 	= 0;
				$sub_total	= 0;
				$pid_ids	= "";
				$total_ship_charge= 0;
				$tax=0;
				if($shop_cart_r){			
					
					while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
						$id 		= $shop_cart_d['id'];
						$pid 		= $shop_cart_d['pid'];
						$subpid 	= $shop_cart_d['subpid'];							
						$qty 		= $shop_cart_d['qty'];
						$ship_charge= $this->db->rp_num($shop_cart_d["ship_charge"]);
						$total_ship_charge += $ship_charge;
						$ship_days	= intval($shop_cart_d["ship_days"]);
						$unitprice 	= $this->db->rp_num($shop_cart_d['unitprice']);
						$discount	+= $shop_cart_d['discount'];
						$totalprice = $this->db->rp_num($shop_cart_d['totalprice']);
						$sub_total 	+= $totalprice;
						$pro_r = mysql_fetch_assoc($this->db->rp_getData("product","*","id='".$pid."'"));											
						$tax+=(($totalprice*$pro_r['pro_tax'])/100);
						
					}
				}					
				$sub_total 			= $this->db->rp_num($sub_total);
				$shipping_charge 	= $this->db->rp_num($total_ship_charge);
				$shipping_discount 	= $this->db->rp_num($this->cart->rp_getShippingDiscount($sub_total,$shipping_charge));
				$tax 				= $this->db->rp_num($tax);
				// if tax is excluded then add tax to final total here...
				$final_total 		= $this->db->rp_num(($sub_total + $shipping_charge) - $discount - $shipping_discount);					
				$isUpdated=$this->db->rp_update("cartdetails",array(
												"coupon_id"		=>"",
												"coupon_code"	=>"",
												"total_ship_charge"=> $total_ship_charge,									
												"total_shipping_discount"	=> $shipping_discount,
												"subtotal"		=> $sub_total,
												"finaltotal"	=> $final_total,
												),"cart_id='".$detail['cid']."'");
				if($isUpdated==1)
				{
					$result=$this->getUserCartDetail($detail,array("cart_id","uid","orderstatus","subtotal","coupon_id","coupon_code","total_shipping_discount","finaltotal","zip"),array(),array(),false);								
								
					$reply=array("ack"=>1,"developer_msg"=>"Coupan Removed Successfully!!","ack_msg"=>"Coupan Removed Successfully!!","result"=>$result['result']);
					return $reply;					
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Error While Removing Coupan Code!!","ack_msg"=>"Error While Removing Coupan Code!!");
					return $reply;
				}						
											
			
						
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}													
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}	
	function placeOrderCOD($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			/*$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;*/
			$cart_id=$detail['cid'];
			$countCart=$this->db->rp_getTotalRecord("cartdetails","cart_id='".$cart_id."' AND orderstatus=1",0);			
			if($countCart>=1)
			{
				$where="cart_id='".$cart_id."'";
				if($cart_id!="" || $cart_id!=0)
				{
					$isUpdated=$this->db->rp_update("cartdetails",array("orderstatus"=>2,"payment_method"=>1,"orderdate"=>$this->db->today()),$where,0);
					if($isUpdated==1)
					{
						// change order status for particular item
						$this->db->rp_update("cartitems",array("orderstatus"=>2),$where);
						$order_id="#".$cart_id;
						$cartDetails=mysql_fetch_assoc($this->db->rp_getData("cartdetails","name,email,phone,finaltotal",$where));
						$name=$cartDetails['name'];
						$amount=$cartDetails['finaltotal'];
						$email=$cartDetails['email'];
						$cartPhone=$cartDetails['phone'];
						$userPhone=$this->db->rp_getValue("user","phone","id='".$detail['uid']."'");
						$shipping_date=date('D, d M Y', strtotime("+7 days"));
						$this->sendAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount);
						$reply=array("ack"=>1,"developer_msg"=>"Order placed successfully!!","ack_msg"=>"Order placed successfully","result"=>array("order_id"=>$order_id,"shipping_date"=>$shipping_date));
						return $reply;						
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
						return $reply;
					}				
								
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found or status changed!!","ack_msg"=>"Your cart removed or already shipped!!");
				return $reply;
			}
			
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function placeOrderOnline($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			/*$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;*/
			$cart_id=$detail['cid'];
			$countCart=$this->db->rp_getTotalRecord("cartdetails","cart_id='".$cart_id."' AND orderstatus=1",0);			
			if($countCart>=1)
			{
				$where="cart_id='".$cart_id."'";
				if($cart_id!="" || $cart_id!=0)
				{
					// NEED THIS FROM PAYMENT GATWAY
					$tracking_id=$this->db->clean($detail['tracking_id']);			
					$bank_ref_no=$this->db->clean($detail['bank_ref_no']);			
					$order_status=$this->db->clean($detail['order_status']);			
					$failure_message=$this->db->clean($detail['failure_message']);			
					$payment_method=$this->db->clean($detail['payment_method']);			
					$card_name=$this->db->clean($detail['card_name']);			
					$status_code=$this->db->clean($detail['status_code']);			
					$status_message=$this->db->clean($detail['status_message']);			
					$currency=$this->db->clean($detail['currency']);			
					$amount=$this->db->clean($detail['amount']);
					$billing_name=$this->db->clean($detail['billing_name']);			
					$billing_address=$this->db->clean($detail['billing_address']);			
					$billing_city=$this->db->clean($detail['billing_city']);			
					$billing_state=$this->db->clean($detail['billing_state']);			
					$billing_zip=$this->db->clean($detail['billing_zip']);			
					$billing_country=$this->db->clean($detail['billing_country']);			
					$billing_tel=$this->db->clean($detail['billing_tel']);			
					$billing_email=$this->db->clean($detail['billing_email']);			
					$vault=$this->db->clean($detail['vault']);			
					$offer_type=$this->db->clean($detail['offer_type']);			
					$offer_code=$this->db->clean($detail['offer_code']);			
					$discount_value=$this->db->clean($detail['discount_value']);			
					$mer_amount=$this->db->clean($detail['mer_amount']);			
					$eci_value=$this->db->clean($detail['eci_value']);
							
					$rows=array("cart_id", "tracking_id", "bank_ref_no", "order_status", "failure_message", "payment_mode", "card_name", "status_code", "status_message", "currency", "amount", "billing_name", "billing_address", "billing_city", "billing_state", "billing_zip", "billing_country", "billing_tel", "billing_email", "vault", "offer_type", "offer_code", "discount_value", "mer_amount", "eci_value");
					$values=array($cart_id, $tracking_id, $bank_ref_no, $order_status, $failure_message, $payment_method, $card_name, $status_code, $status_message, $currency, $amount, $billing_name, $billing_address, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_tel, $billing_email, $vault, $offer_type, $offer_code, $discount_value, $mer_amount, $eci_value);
					if($this->db->rp_insert("paymentdetails",$values,$rows,0))
					{
						if($status_message!="Y")
						{
							$reply=array("ack"=>0,"developer_msg"=>"Failure Status Updated!!","ack_msg"=>"Payment Failure!!");
							return $reply;
						}					
						else
						{
							
								$isUpdated=$this->db->rp_update("cartdetails",array("orderstatus"=>2,"payment_method"=>2,"orderdate"=>$this->db->today()),$where,0);
								if($isUpdated==1)
								{
									// change order status for particular item
									$this->db->rp_update("cartitems",array("orderstatus"=>2),$where);
									$order_id="#".$cart_id;
									$cartDetails=mysql_fetch_assoc($this->db->rp_getData("cartdetails","name,email,phone,finaltotal",$where));
									$name=$cartDetails['name'];
									$amount=$cartDetails['finaltotal'];
									$email=$cartDetails['email'];
									$cartPhone=$cartDetails['phone'];
									$userPhone=$this->db->rp_getValue("user","phone","id='".$detail['uid']."'");
									$shipping_date=date('D, d M Y', strtotime("+7 days"));
									$this->sendAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount);
									/* Entry to Invoice */
									$adate	= date('Y-m-d H:i');
									$uid=$detail['uid'];
									$irows 		= array("cart_id","uid","payment_method	","adate",);
									$ivalues 	= array($cart_id,$uid,'2',$adate,);
									$this->db->rp_insert("invoice",$ivalues,$irows);
									/* Entry to Invoice */
																	
									$reply=array("ack"=>1,"developer_msg"=>"Order placed successfully!!","ack_msg"=>"Order placed successfully","result"=>array("order_id"=>$order_id,"shipping_date"=>$shipping_date));
									return $reply;
																	
								}
								else
								{
									$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
									return $reply;
								}	
								
								
							
						}
					}
					else
					{
						$reply=array("ack"=>0,"ack_msg"=>"Payment information can't be added");
						return $reply;
					}		
								
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found or status changed!!","ack_msg"=>"Your cart removed or already shipped!!");
				return $reply;
			}
			
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function placeOrderWalletOnline($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			/*$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;*/
			$cart_id=$detail['cid'];
			$countCart=$this->db->rp_getTotalRecord("cartdetails","cart_id='".$cart_id."' AND orderstatus=1",0);			
			if($countCart>=1)
			{
				$where="cart_id='".$cart_id."'";
				if($cart_id!="" || $cart_id!=0)
				{
					// NEED THIS FROM PAYMENT GATWAY
					$tracking_id=$this->db->clean($detail['tracking_id']);
					if($tracking_id!="")
					{
						$bank_ref_no=$this->db->clean($detail['bank_ref_no']);			
						$order_status=$this->db->clean($detail['order_status']);			
						$failure_message=$this->db->clean($detail['failure_message']);			
						$payment_method=$this->db->clean($detail['payment_method']);			
						$card_name=$this->db->clean($detail['card_name']);			
						$status_code=$this->db->clean($detail['status_code']);			
						$status_message=$this->db->clean($detail['status_message']);			
						$currency=$this->db->clean($detail['currency']);			
						$amount=$this->db->clean($detail['amount']);
						$billing_name=$this->db->clean($detail['billing_name']);			
						$billing_address=$this->db->clean($detail['billing_address']);			
						$billing_city=$this->db->clean($detail['billing_city']);			
						$billing_state=$this->db->clean($detail['billing_state']);			
						$billing_zip=$this->db->clean($detail['billing_zip']);			
						$billing_country=$this->db->clean($detail['billing_country']);			
						$billing_tel=$this->db->clean($detail['billing_tel']);			
						$billing_email=$this->db->clean($detail['billing_email']);			
						$vault=$this->db->clean($detail['vault']);			
						$offer_type=$this->db->clean($detail['offer_type']);			
						$offer_code=$this->db->clean($detail['offer_code']);			
						$discount_value=$this->db->clean($detail['discount_value']);			
						$mer_amount=$this->db->clean($detail['mer_amount']);			
						$eci_value=$this->db->clean($detail['eci_value']);
								
						$rows=array("cart_id", "tracking_id", "bank_ref_no", "order_status", "failure_message", "payment_mode", "card_name", "status_code", "status_message", "currency", "amount", "billing_name", "billing_address", "billing_city", "billing_state", "billing_zip", "billing_country", "billing_tel", "billing_email", "vault", "offer_type", "offer_code", "discount_value", "mer_amount", "eci_value");
						$values=array($cart_id, $tracking_id, $bank_ref_no, $order_status, $failure_message, $payment_method, $card_name, $status_code, $status_message, $currency, $amount, $billing_name, $billing_address, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_tel, $billing_email, $vault, $offer_type, $offer_code, $discount_value, $mer_amount, $eci_value);
						if($this->db->rp_insert("paymentdetails",$values,$rows,0))
						{
							if($status_message!="Y")
							{
								$reply=array("ack"=>0,"developer_msg"=>"Failure Status Updated!!","ack_msg"=>"Payment Failure!!");
								return $reply;
							}					
							else
							{
								
									
									
								
							}
						}
						else
						{
							$reply=array("ack"=>0,"ack_msg"=>"Payment information can't be added");
							return $reply;
						}
					}						
					
					
					
					$isUpdated=$this->db->rp_update("cartdetails",array("orderstatus"=>2,"payment_method"=>3,"orderdate"=>$this->db->today()),$where,0);
					if($isUpdated==1)
					{
						// change order status for particular item
						$this->db->rp_update("cartitems",array("orderstatus"=>2),$where);
						$order_id="#".$cart_id;
						$cartDetails=mysql_fetch_assoc($this->db->rp_getData("cartdetails","name,email,phone,finaltotal",$where));
						$name=$cartDetails['name'];
						$amount=$cartDetails['finaltotal'];
						$email=$cartDetails['email'];
						$cartPhone=$cartDetails['phone'];
						$userPhone=$this->db->rp_getValue("user","phone","id='".$detail['uid']."'");
						$shipping_date=date('D, d M Y', strtotime("+7 days"));
						//$this->sendAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount);
						/* Entry to Invoice */
						$adate	= date('Y-m-d H:i');
						$uid=$detail['uid'];
						
						$irows 		= array("cart_id","uid","payment_method	","adate",);
						$ivalues 	= array($cart_id,$uid,'3',$adate,);
						$this->db->rp_insert("invoice",$ivalues,$irows);
						/* Entry to Invoice */
						// Update User Wallet
						 $wallet_inforamtion=$this->user->getUserWalletDetail(array("id"=>$uid));
						
						if($wallet_inforamtion['ack']==1)
						{
							// If Wallet Information found then update amount.
							$wallet_inforamtion=$wallet_inforamtion['result'];
							$previous_amount=$wallet_inforamtion['amount'];
							$used_amount=$detail['wallet_amount_used'];
							$new_amount=$previous_amount-$used_amount;
							
							$updateDetail=array("id"=>$uid,"used_amount"=>$used_amount,"amount"=>$new_amount,"ref_id"=>$cart_id,"ref_type"=>0,"transaction_type"=>0);
							$wallet_reply=$this->user->updateWalletAmount($updateDetail);
						}
						else
						{
							// If In case User wallet not found then create empty wallet for that user.
							$wallet_reply=$this->user->addNewWallet(array("id"=>$uid));
						}

						
						$reply=array("ack"=>1,"developer_msg"=>"Order placed successfully!!","ack_msg"=>"Order placed successfully","result"=>array("order_id"=>$order_id,"shipping_date"=>$shipping_date),"wallet_reply"=>$wallet_reply);
						return $reply;
														
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
						return $reply;
					}	
						
					
						
								
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found or status changed!!","ack_msg"=>"Your cart removed or already shipped!!");
				return $reply;
			}
			
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function checkDeliveryAtPincode($detail)
	{
		$zip = intval(trim($detail['zip']));
		$delWhere 		= " pincode='".$zip."' AND isDelete=0";
		$delPinCheck_r 	= $this->db->rp_getData("delivery_pincode","*",$delWhere);
		if($delPinCheck_r && mysql_num_rows($delPinCheck_r)>0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Wohoo!! Delivery Available!!","ack_msg"=>"Great!! We can deliver this product to your place!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"RIP Delivery not available","ack_msg"=>"Sorry We can't deliver product to your place!!");
			return $reply;
		}
	}
	function verifyBillingKeys($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;
			if($cart_id!="" || $cart_id!=0)
			{
				$zip = intval(trim($detail['zip']));
				$delWhere 		= " pincode='".$zip."' AND isDelete=0";
				$delPinCheck_r 	= $this->db->rp_getData("delivery_pincode","*",$delWhere);
				if($delPinCheck_r && mysql_num_rows($delPinCheck_r)>0)
				{
				
					$phone=$this->db->rp_getValue("user","phone","id='".$detail['uid']."'");				
					$validatedPhone_r=$this->db->rp_getData("cartdetails","phone","uid='".$detail['uid']."' AND orderstatus=4");
					$validatedPhone=array();
					if($validatedPhone_r)
					{
						while($v=mysql_fetch_array($validatedPhone_r))
						{							
							$validatedPhone[]=$v['phone'];
						}
					}					
					if($phone==$detail['phone'] || in_array($detail['phone'],$validatedPhone))
					{
						
						$reply=array("ack"=>1,"developer_msg"=>"Verified Number!!","ack_msg"=>"Mobile number verified!!");
						return $reply;	
							
					}
					else
					{
						$otpReply=$this->user->sendOTPToContactNumber(array("id"=>$detail['uid'],"phone"=>$detail['phone']));						
						if($otpReply['ack']==1)
						{
							$reply=array("ack"=>3,"developer_msg"=>"OTP sent!!","ack_msg"=>"Please Verify OTP!!");
							return $reply;	
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"OTP can't be send","ack_msg"=>"We can't reach to your contact number. Try Again!!");
							return $reply;
						}
					}
				}
				else
				{
					$reply=array("ack"=>2,"developer_msg"=>"RIP Delivery not available","ack_msg"=>"Sorry We can't deliver product to your place!!");
					return $reply;
				}					
							
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function getCartSummary($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;
			if($cart_id!="" || $cart_id!=0)
			{
				return $cartdetailsReply=$this->getUserCartDetail($detail,array("cart_id","uid","orderstatus","subtotal","coupon_id","coupon_code","total_shipping_discount","finaltotal","name","address1","locality","city","state","country","zip","phone"),array(),array(),false);
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function updateBillingAddress($detail,$required_cart_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;
			if($cart_id!="" || $cart_id!=0)
			{
				$zip = intval(trim($detail['zip']));
				$delWhere 		= " pincode='".$zip."' AND isDelete=0";
				$delPinCheck_r 	= $this->db->rp_getData("delivery_pincode","*",$delWhere);
				if($delPinCheck_r && mysql_num_rows($delPinCheck_r)>0)
				{
					
					$this->cart->aj_shipChargeUpdate($detail['cid'],intval($detail['zip']));
					$shop_cart_r = $this->db->rp_getData("cartitems","*","cart_id='".$cart_id."'");
					$totalprice = 0;
					$discount 	= 0;
					$sub_total	= 0;
					$pid_ids	= "";
					$total_ship_charge= 0;
					$tax=0;
					if($shop_cart_r){			
						
						while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
							$id 		= $shop_cart_d['id'];
							$pid 		= $shop_cart_d['pid'];
							$subpid 	= $shop_cart_d['subpid'];							
							$qty 		= $shop_cart_d['qty'];
							$ship_charge= $this->db->rp_num($shop_cart_d["ship_charge"]);
							$total_ship_charge += $ship_charge;
							$ship_days	= intval($shop_cart_d["ship_days"]);
							$unitprice 	= $this->db->rp_num($shop_cart_d['unitprice']);
							$discount	+= $shop_cart_d['discount'];
							$totalprice = $this->db->rp_num($shop_cart_d['totalprice']);
							$sub_total 	+= $totalprice;
							$pro_r = mysql_fetch_assoc($this->db->rp_getData("product","*","id='".$pid."'"));											
							$tax+=(($totalprice*$pro_r['pro_tax'])/100);
							
						}
					}					
					$sub_total 			= $this->db->rp_num($sub_total);
					$shipping_charge 	= $this->db->rp_num($total_ship_charge);
					$shipping_discount 	= $this->db->rp_num($this->cart->rp_getShippingDiscount($sub_total,$shipping_charge));
					$tax 				= $this->db->rp_num($tax);
					// if tax is excluded then add tax to final total here...
					$final_total 		= $this->db->rp_num(($sub_total + $shipping_charge) - $discount - $shipping_discount);					
					$isUpdated=$this->db->rp_update("cartdetails",array(
													"total_ship_charge"=> $total_ship_charge,									
													"total_shipping_discount"	=> $shipping_discount,
													"subtotal"		=> $sub_total,
													"finaltotal"	=> $final_total,
													"name"=>$detail['name'],
													"phone"=>$detail['phone'],
													"address1"=>$detail['address'],
													"locality"=>$detail['locality'],
													"city"=>$detail['city'],"state"=>$detail['state'],
													"country"=>$detail['country'],
													"zip"=>intval($detail['zip'])),"cart_id='".$detail['cid']."'");
					if($isUpdated==1)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Billing Detail Updated!!","ack_msg"=>"Billing Detail Updated!!");
						return $reply;						
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"RIP Delivery not available","ack_msg"=>"Sorry We can't deliver product to your place!!");
					return $reply;
				}				
							
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function updateDeliveryPincode($detail,$required_cart_columns=array(),$required_cart_item_columns=array(),$required_product_columns=array())
	{		
		$required_columns=$this->getRequiredColumns($required_cart_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->getUserCartId($detail);
			$detail['cid']=$cart_id;
			if($cart_id!="" || $cart_id!=0)
			{
				$zip = intval(trim($detail['zip']));
				$delWhere 		= " pincode='".$zip."' AND isDelete=0";
				$delPinCheck_r 	= $this->db->rp_getData("delivery_pincode","*",$delWhere);
				if($delPinCheck_r && mysql_num_rows($delPinCheck_r)>0)
				{
					$this->cart->aj_shipChargeUpdate($detail['cid'],intval($detail['zip']));					
					$isUpdated=$this->db->rp_update("cartdetails",array("zip"=>intval($detail['zip'])),"cart_id='".$detail['cid']."'");
					if($isUpdated==1)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Delivery Pincode Updated!!","ack_msg"=>"Delivery Pincode Updated!!");
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!! Try later");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"RIP Delivery not available","ack_msg"=>"Sorry We can't deliver product to your place!!");
					return $reply;
				}				
							
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
				
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}		
	}
	function getUserCartDetail($detail,$required_cart_columns=array(),$required_cart_item_columns=array(),$required_product_columns=array(),$isCartItemRequired=true)
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);
		$cart_id=$this->getUserCartId($detail);
		$detail['cid']=$cart_id;		
		if(!empty($detail) && array_key_exists("cid",$detail) && $detail['cid']!="" && $detail['cid']!=0)
		{
			
			// We have cart_id fetch details
			$cartdetails_r=$this->db->rp_getData("cartdetails",$required_columns,"cart_id='".$detail['cid']."'");
			if($cartdetails_r)
			{
				$cartdetails=mysql_fetch_assoc($cartdetails_r);	
				// If Delivery Pincode Not Available then notify android to take delivery pincode otherwise show shipping charge and delivery pincode
				if($cartdetails['zip']!="" && $cartdetails['zip']!=0)
				{
					$this->cart->aj_shipChargeUpdate($detail['cid'],$cartdetails['zip']);
					$cartdetails['isDeliveryPincodeAvailable']=1;
					// Create Full Address 
				}
				else
				{
					$cartdetails['isDeliveryPincodeAvailable']=0;
				}
				if(isset($cartdetails['address1']))
				{
						$country=$this->application->getCountry(array($cartdetails['country']),array("name"));
						if($country['ack']==1)
						$cartdetails['country']=$country['result'][0]['name'];
						else
						$cartdetails['country']="--";	
						$cartdetails['address']=$cartdetails['address1'];
						$cartdetails['full_address']=ucfirst($cartdetails['name']).",<br>".ucfirst($cartdetails['address1']).",<br>".ucfirst($cartdetails['locality']).",<br>".ucfirst($cartdetails['city'])."-".ucfirst($cartdetails['zip']).",<br>".ucfirst($cartdetails['state']).",<br>".ucfirst($cartdetails['country']).",<br>".ucfirst($cartdetails['phone']);
						$wallet_info=$this->user->getUserWalletDetail(array("id"=>$detail['uid']));
						if($wallet_info['ack']!=1)
						{
							$wallet_info=$this->user->addNewWallet(array("id"=>$detail['uid']));
						}
						$cartdetails['wallet_inforamtion']=$wallet_info['result'];	

				}
				//print_r($cartdetails);
				// Fetch cartitems now!!
				$cartItems=$this->getCartItems(array("cid"=>$detail['cid']),$required_cart_item_columns,$required_product_columns,$isCartItemRequired);
				if($cartItems['ack']==1)
				{
					$cartdetails['countItems']=$cartItems['count'];
					$cartdetails=array_merge($cartdetails,$cartItems['result']);
				}				
				else
				{
					$cartdetails['discount']=0;
					$cartdetails['total_ship_charge']=0;
					$cartdetails['items']=array();	
				}
				
				
								
									
				$reply=array("ack"=>1,"developer_msg"=>"Cart detail fetched!!","ack_msg"=>"Great!! Cart found!!","result"=>$cartdetails);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"cart not found","ack_msg"=>"Cart either shipped or removed!!");
				return $reply;
			}
			
			
		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"cart not found!!","ack_msg"=>"Empty Cart!!");
			return $reply;
		}		
	}
	function getOrderHistory($detail,$required_cart_columns=array(),$required_cart_item_columns=array(),$required_product_columns=array(),$isCartItemRequired=false)
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_result=array();
			// We have cart_id fetch details
			$cartdetails_r=$this->db->rp_getData("cartdetails",$required_columns,"uid='".$detail['uid']."' AND orderstatus!=1","cart_id DESC",0);
			if($cartdetails_r)
			{
								
				while($cartdetails=mysql_fetch_assoc($cartdetails_r))
				{
					$cartdetails['orderstatus_slug']=intval($cartdetails['orderstatus']);
					$cartdetails['orderstatus']=$this->orderStatus[intval($cartdetails['orderstatus'])];					
					if(isset($cartdetails['address1']))
					{
							$country=$this->application->getCountry(array($cartdetails['country']),array("name"));
							if($country['ack']==1)
							$cartdetails['country']=$country['result'][0]['name'];
							else
							$cartdetails['country']="--";	
							$cartdetails['address']=$cartdetails['address1'];
							$cartdetails['full_address']=ucfirst($cartdetails['name']).",<br>".ucfirst($cartdetails['address1']).",<br>".ucfirst($cartdetails['locality']).",<br>".ucfirst($cartdetails['city'])."-".ucfirst($cartdetails['zip']).",<br>".ucfirst($cartdetails['state']).",<br>".ucfirst($cartdetails['country']).",<br>".ucfirst($cartdetails['phone']);
							

					}
					$cartdetails['orderdate']=$this->formateDate($cartdetails['orderdate']);
					$cartdetails['shipdate']=$this->formateDate($cartdetails['shipdate']);
					$cartdetails['deliverydate']=$this->formateDate($cartdetails['deliverydate']);
					$cartdetails['rcdate']=$this->formateDate($cartdetails['rcdate']);
					//print_r($cartdetails);
					// Fetch cartitems now!!
					$cartItems=$this->getCartItems(array("cid"=>$cartdetails['cart_id']),$required_cart_item_columns,$required_product_columns,$isCartItemRequired);
					if($cartItems['ack']==1)
					{
						
						$cartdetails=array_merge($cartdetails,$cartItems['result']);
					}				
					else
					{
						$cartdetails['discount']=0;
						$cartdetails['total_ship_charge']=0;
						$cartdetails['items']=array();	
					}
					
					$cart_result[]=$cartdetails;
				}												
				$reply=array("ack"=>1,"developer_msg"=>"Orders fetched!!","ack_msg"=>"Great!! User Orders found!!","result"=>$cart_result);
				return $reply;					
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No order found","ack_msg"=>"No Order found!!");
				return $reply;
			}
			
			
		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user not found!!","ack_msg"=>"Empty Cart!!");
			return $reply;
		}		
	}
	function getOrderDetail($detail,$required_cart_columns=array(),$required_cart_item_columns=array(),$required_product_columns=array(),$isCartItemRequired=true)
	{
		$required_columns=$this->getRequiredColumns($required_cart_columns);
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0 && array_key_exists("cid",$detail) && $detail['cid']!="" && $detail['cid']!=0)
		{
			$cart_result=array();
			// We have cart_id fetch details
			$cartdetails_r=$this->db->rp_getData("cartdetails",$required_columns,"cart_id='".$detail['cid']."' AND uid='".$detail['uid']."'","cart_id DESC",0);
			if($cartdetails_r)
			{
								
					$cartdetails=mysql_fetch_assoc($cartdetails_r);			
					$cartdetails['orderstatus_slug']=intval($cartdetails['orderstatus']);
					$cartdetails['orderstatus']=$this->orderStatus[intval($cartdetails['orderstatus'])];
					
					if(isset($cartdetails['address1']))
					{
							$country=$this->application->getCountry(array($cartdetails['country']),array("name"));
							if($country['ack']==1)
							$cartdetails['country']=$country['result'][0]['name'];
							else
							$cartdetails['country']="--";	
							$cartdetails['address']=$cartdetails['address1'];
							$cartdetails['full_address']=ucfirst($cartdetails['name']).",<br>".ucfirst($cartdetails['address1']).",<br>".ucfirst($cartdetails['locality']).",<br>".ucfirst($cartdetails['city'])."-".ucfirst($cartdetails['zip']).",<br>".ucfirst($cartdetails['state']).",<br>".ucfirst($cartdetails['country']).",<br>".ucfirst($cartdetails['phone']);
							

					}
					// Fetch cartitems now!!
					$cartItems=$this->getCartItems(array("cid"=>$cartdetails['cart_id']),$required_cart_item_columns,$required_product_columns,$isCartItemRequired);
					if($cartItems['ack']==1)
					{
						
						$cartdetails=array_merge($cartdetails,$cartItems['result']);
					}				
					else
					{
						$cartdetails['discount']=0;
						$cartdetails['total_ship_charge']=0;
						$cartdetails['items']=array();	
					}
					$cartdetails['orderdate']=$this->formateDate($cartdetails['orderdate']);
					$cartdetails['shipdate']=$this->formateDate($cartdetails['shipdate']);
					$cartdetails['deliverydate']=$this->formateDate($cartdetails['deliverydate']);
					$cartdetails['rcdate']=$this->formateDate($cartdetails['rcdate']);
					$cart_result=$cartdetails;
				
													
				$reply=array("ack"=>1,"developer_msg"=>"Orders fetched!!","ack_msg"=>"Great!! User Orders found!!","result"=>$cart_result);
				return $reply;					
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No order found","ack_msg"=>"No Order found!!");
				return $reply;
			}
			
			
		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user or cart not found!!","ack_msg"=>"Internal Error!!");
			return $reply;
		}		
	}
	function createNewCart($detail,$required_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			// Add New Cart with In Progress Status							
			$rows 	= array("uid","orderstatus");
			$values = array($detail['uid'],1);
			$cart_id = $this->db->rp_insert("cartdetails",$values,$rows);
			return $cart_id;		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function getCartItems($detail,$required_cart_item_columns=array(),$required_product_columns=array(),$isCartItemRequired=true)
	{
		$required_cart_item_columns=$this->getRequiredColumns($required_cart_item_columns);	
		if(!empty($detail) && array_key_exists("cid",$detail) && $detail['cid']!="" && $detail['cid']!=0)
		{
			$cartitems_r = $this->db->rp_getData("cartitems",$required_cart_item_columns,"cart_id='".$detail['cid']."'","",0); 
			if($cartitems_r){
				
				$discount 	= 0;
				$sub_total	= 0;
				$tax	= 0;
				$total_ship_charge= 0;
				$cart=array();
				$item_count=mysql_num_rows($cartitems_r);
				$cart_items=array();
				while($cartitem = mysql_fetch_array($cartitems_r)){
					
					$id 		= $cartitem['id'];					
					$pid 		= $cartitem['pid'];
					$uid 		= $cartitem['uid'];
					$pro_name 	= stripslashes($cartitem['name']);
					$qty 		= $cartitem['qty'];											
					$ship_charge= $this->db->rp_num($cartitem["ship_charge"]);
					$total_ship_charge += $ship_charge;
					$ship_days	= intval($cartitem["ship_days"]);
					$unitprice 	= $this->db->rp_num($cartitem['unitprice']);
					$single_discount	= $this->db->rp_num($cartitem['discount']);
					$discount	+= $cartitem['discount'];
					$totalprice = $this->db->rp_num($cartitem['totalprice']);
					$sub_total 	+= $totalprice;					
					$pid=$cartitem['pid'];
					$product_detail=$this->application->getProduct(array($pid),array(),array(),array(),array(),array(),$uid,$required_product_columns);					
					if($product_detail['ack']==1)
					{
						//echo $cartitem['attr'];
						$cart_item_attr=unserialize($cartitem['attr']);
						//print_r($cart_item_attr);
						$attr=array();
						$attr_string="";
						foreach($cart_item_attr as $key => $value)
						{
							//echo $key;
							$a_value=$this->application->getAttribute(array($key));
							$av_value=$this->application->getAttributeValue($value,array(),array("id","aid","name","value"));							
							if($a_value['ack']==1)
							{
								if($av_value['ack']==1 && $a_value['ack']==1)
								$attr[]=array("id"=>$key,"title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
							}
							if($av_value['ack']==1)
							{
								if($attr_string!="")
								$attr_string=$attr_string."and ".$a_value['result'][0]['name']."-".$av_value['result'][0]['name']." ";
								else
								$attr_string="With ".$a_value['result'][0]['name']."-".$av_value['result'][0]['name']." ";	
							}
							
						}
						
						$product_detail_e=$product_detail['result'][0];
						$product_detail_e['name']=$product_detail_e['name']." ".$attr_string;						
						$product_detail_e['sell_price']=$unitprice;						
						$product_detail_e['id']=$id;
						$product_detail_e['qty']=$qty;	
						$product_detail_e['attr']=$attr;	
						$product_detail_e['attr_string']=$attr_string;	
						$tax+=(($totalprice*$product_detail_e['pro_tax'])/100);						
						array_push($cart_items,$product_detail_e);
					}
									
					
				}
				$shipping_charge 	= $this->db->rp_num($total_ship_charge);
				$shipping_discount 	= $this->db->rp_num($this->cart->rp_getShippingDiscount($sub_total,$shipping_charge));
				$tax 				= $this->db->rp_num($tax);		
				// if tax is excluded then add tax to final total here...
				$final_total = $this->db->rp_num(($sub_total + $shipping_charge) - $discount - $shipping_discount);				
				$cart['cart_id']=$detail['cid'];
				$cart['total_ship_charge']=$shipping_charge;
				$cart['total_shipping_discount']=$shipping_discount;
				$cart['discount']=$discount;
				$cart['subtotal']=$sub_total;
				$cart['finaltotal']=$final_total;
				if($isCartItemRequired)	
				$cart['items']=$cart_items;
				$reply=array("ack"=>1,"count"=>$item_count,"developer_msg"=>"cart item found!!","ack_msg"=>"Cart items founds!!","result"=>$cart);				
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"not cart items!!","ack_msg"=>"Empty cart!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"cart id not valid!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function getUserCartId($detail,$required_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_columns);	
		if(!empty($detail) && array_key_exists("uid",$detail) && $detail['uid']!="" && $detail['uid']!=0)
		{
			$cart_id=$this->rp_getValue("cartdetails","cart_id","uid='".$detail['uid']."' AND orderstatus='1'");// Get Cart detail if there are any cart with In Progress status
			if($cart_id=="" || $cart_id==0)
			{			
				// Create new Cart because user has not cart which is in progress
				$cart_id=$this->createNewCart($detail,$required_columns=array());			
			}
			return $cart_id;		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not found!!","ack_msg"=>"Internal Error!! Try later");
			return $reply;
		}
	}
	function countCart($whereClause)
	{		
		$countCart = $this->db->getTotalRecord("cartdetails","id",$where,0);
		return $countCart;	
	}
	function getRequiredColumns($required_columns=array())
	{
		if(!empty($required_columns))
		{
			$required_columns_string=implode(",",$required_columns);
			return $required_columns_string;
		}
		else
		{
			return "*";
		}
	}
	function getLimit($limit=array())
	{
		$limit=$this->db->getLimit();	
		if(!empty($limit) && array_key_exists("ul",$limit))
		{
			$ul=$limit['ul'];
			if(array_key_exists("ll",$limit) && $limit['ll']!="")
			{
				$ll=$limit['ll'];
			}
			else
			{
				$ll="18446744073709551615";
			}			
			$limit_string="".$ul.",".$ll;
			return $limit_string;
		}
		else
		{
			return "";
		}
	}	
	function sendAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount,$tracking_id="",$bank_ref_no="")
	{
		
		/**************************Send Confirmation Email Starts*****************************/
		$order_toemail = $email;
		$subject	= SITENAME." - Order #".$cart_id." Placed Successfully";
		$body = file_get_contents(SITEURL.'mailbody/order_placed.php?cart_id='.$cart_id.'&tracking_id='.$tracking_id.'&bank_ref_no='.$bank_ref_no.'');
		$this->nt->rp_sendGenEmail($order_toemail,$subject,$body);
		/**************************Send Confirmation Email Ends*****************************/
		
		/**Send Confirmation SMS Starts**/
		if($cartPhone!="")
		{
			$smsMsg = "We've received your order #".$cart_id." of Rs ".$amount.". Your order details has been sent to your registerd Email id. \nThank You For Your Order\n Team ".SITENAME;
			$this->nt->aj_sendSMS($userPhone,$smsMsg,"");
		}
		if($userPhone!="" && $cartPhone!=$userPhone)
		{
			$smsMsg = "We've received your order #".$cart_id." of Rs ".$amount." From Your Falando Account. Your order details has been sent to your registerd Email id. \nThank You For Your Order.\n Team ".SITENAME;
			$this->nt->aj_sendSMS($userPhone,$smsMsg,"");
		}
		
		/**Send Confirmation SMS Starts**/
	}
	function sendCancelAcknowledgeToUser($name,$email,$userPhone,$cartPhone,$cart_id,$amount,$tracking_id="",$bank_ref_no="")
	{
		
		/**************************Send Confirmation Email Starts*****************************/
			$order_toemail = $email;
		$subject	= SITENAME." - Order #".$cart_id." Cancelled";
		$body = file_get_contents(SITEURL.'mailbody/cancel_whole_order.php?cart_id='.$cart_id."'");
		$this->nt->rp_sendGenEmail($order_toemail,$subject,$body);
		/**************************Send Confirmation Email Ends*****************************/
		
		/**Send Confirmation SMS Starts**/
		if($cartPhone!="")
		{
			$smsMsg = "We've received your request to cancel order #".$cart_id." of Rs ".$amount.". Your order has been cancelled !! \nWe hope we'll get chance to serve you again.\nTeam ".SITENAME;
			$this->nt->aj_sendSMS($userPhone,$smsMsg,"");		
						
		}
		if($userPhone!="" && $cartPhone!=$userPhone)
		{
			$smsMsg = "We've received your request to cancel order #".$cart_id." of Rs ".$amount.". Your order has been cancelled !! \nWe hope we'll get chance to serve you again. \nTeam ".SITENAME;
			$this->nt->aj_sendSMS($userPhone,$smsMsg,"");		
		}
		
		/**Send Confirmation SMS Starts**/
	}
	function formateDate($date)
	{
	
		if($date!="" && $date!="null" && $date!="0000-00-00 00:00:00")
		{
			return date('D, d M Y', strtotime($date));
		}
		else
		{
			return "--";
		}
	}
}
?>