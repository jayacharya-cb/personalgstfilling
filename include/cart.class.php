<?php
class Cart extends Functions
{
	/*
		*** Cart Function Developed By Ravi Patel :) <<<
	*/
	public function rp_getCartTotalItem() // Total no. of products in cart
    {
		if(isset($_SESSION['SHOPWALA_SESS_CART_ID']) && $_SESSION['SHOPWALA_SESS_CART_ID']>0){
			return parent::rp_getTotalRecord("cartitems","cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'");
		}else{
			return 0;
		}
    }
	
	public function rp_getCartSubTotalPrice() // Total Price of cart
    {
		if(isset($_SESSION['SHOPWALA_SESS_CART_ID']) && $_SESSION['SHOPWALA_SESS_CART_ID']>0){
			$t = parent::rp_getSumVal("cartitems","totalprice","cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'");
			$s = parent::rp_getSumVal("cartitems","ship_charge","cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'");
			$d = $this->rp_getShippingDiscount($t,$s);
			return parent::rp_num(($t+$s)-$d);
		}else{
			return parent::rp_num(0);
		}
    }
	public function aj_getCartSubTotalPrice($cid) // Total Price of cart
    {		
		if($cid>0){
			
			$t = parent::rp_getSumVal("cartitems","totalprice","cart_id='".$cid."'",0);
			if($t==null)
			{
				$t=0;
			}
			$s=$d=0;
			$s = parent::rp_getSumVal("cartitems","ship_charge","cart_id='".$cid."'");
			$d = $this->rp_getShippingDiscount($t,$s);
			return parent::rp_num(($t+$s)-$d);
		}else{
			echo "d";
			return parent::rp_num(0);
		}
    }
	public function aj_updateSubTotalCart($cid)
	{
		if($cid>0){
			$t = parent::rp_getSumVal("cartitems","totalprice","cart_id='".$cid."'",0);
			if($t==null)
			{
				$t=0;
			}
			$drows=array("subtotal"=>$t);
			$dwhere="cart_id='".$cid."'";
			parent::rp_update("cartdetails",$drows,$dwhere);
		}else{
			return parent::rp_num(0);
		}
	}
	public function aj_checkForUpdateCartItemQty($cartItemId,$newqty)
	{
		$q = 1;
		$shop_cart_r = parent::rp_getData("cartitems","*","id='".$cartItemId."'","",0);
		if(mysql_num_rows($shop_cart_r)>0){
			
			while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
				$pid 		= $shop_cart_d['pid'];
				$qty 		= $newqty;
				
				$pro_qty 	= parent::rp_getValue("product","qty","id='".$pid."'",0);
				
				if($pro_qty<=0 || $pro_qty<$qty){					
					$q = 0;
				}
			}
			if($q>0){
				
				return 1;
			}else{
				
				return 0;
			}
		}else{
			return 0;
		}
	}
	public function rp_checkCartQuantity($cartid){
		$q = 1;
		$shop_cart_r = parent::rp_getData("cartitems","*","cart_id='".$cartid."'");
		if(mysql_num_rows($shop_cart_r)>0){
			while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
				$pid 		= $shop_cart_d['pid'];
				$qty 		= $shop_cart_d['qty'];
				$pro_qty 	= parent::rp_getValue("product","qty","id='".$pid."'");
				if($pro_qty<=0 || $pro_qty<$qty){
					$q = 0;
				}
			}
			if($q>0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	public function rp_checkCart($uid){
		$q = 1;
		$shop_cart_r = parent::rp_getData("cartdetails","*","uid='".$uid."' AND orderstatus=1","",0);
		if(mysql_num_rows($shop_cart_r)>0){
			$shop_cartDetail=mysql_fetch_array($shop_cart_r);
			$result=array('ack'=>1,'cart_id'=>$shop_cartDetail['cart_id']);			
			return $result;
		}else{
			return array('ack'=>0);;
		}
	}
	public function aj_getProductDetail($pid,$uid=""){
		$q = 1;
		
		$product_r = parent::rp_getData("product","*","id='".$pid."' AND isDelete=0","",0);
		if(mysql_num_rows($product_r)>0){
			$product_detail=mysql_fetch_assoc($product_r);	
			
			// Fetching Ingredient of that product
			$product_detail['ingredient']=explode("&xx5895",$product_detail['ingredient']);
			// Fetching Ingredient of that product
			
			/*-----------------------------------------------------------*/
			// Fetching Comelete ImagePath of that product
			$product_detail['image_path']=FINAL_URL."".ADMINFOLDER."/".PRODUCT."".$product_detail['image_path'];
			// Fetching Comelete ImagePath of that product
			
			/*-----------------------------------------------------------*/
			
			// Fetching Alt Images
			$alt_images=array();
			$alt_images_q=parent::rp_getData("alt_image","*","pid='".$product_detail['id']."'","display_order ASC",0);
			if(mysql_num_rows($alt_images_q)>0){

				while($f = mysql_fetch_array($alt_images_q)){
					array_push($alt_images,FINAL_URL."".ADMINFOLDER."/".PRODUCT_ALT_A."".$f['image_path']);
				}
			}
			$product_detail['alt_img']=$alt_images;	
			// Fetching Alt Images
			/*-----------------------------------------------------------*/
			
			// Fetching Favourite Flag if uid exits
			if($uid!="")
			{					
					$fav=(parent::rp_getTotalRecord("product_favorite","uid = '".$uid."' AND pid='".$product_detail['id']."'",0)>0)?1:0;
			}
			else
			{
					$fav=0;
			}
			$product_detail['favorite']=$fav;
			// Fetching Favourite Flag if uid exits
			
			/*-----------------------------------------------------------*/
			
			// Fetching Price According to user if uid exits other wise accrofding to list 0
			$price=$product_detail['max_price'];
			if($uid!="")
			{					
					$pricelist=parent::rp_getValue("user","price_list","id='".$uid."'");
					$discountPer=parent::rp_getValue("pricelist","percentage","id='".$pricelist."'");
					
					if($discountPer!=0)
					{
						$discountAmount=$price*$discountPer/100;			
					}
					else
					{
						$discountAmount=0;
					}
					
					$finalPrice=$price-$discountAmount;
			}
			else
			{
					$finalPrice=$product_detail['max_price'];
			}
			$product_detail['max_price']=$price;
			$product_detail['sell_price']=$finalPrice;
			
			
			$product_detail['pricing']=array();
			$weights=parent::rp_getData("product_weight_price","*","product_id='".$product_detail['id']."'","",0);
			if(mysql_num_rows($weights)>0){
			while($w=mysql_fetch_assoc($weights))
			{
				$price=$w['price'];
				$w['orignal_price']=$price;
				$w['title']=parent::rp_getValue("weight","name","id='".$w['weight_id']."'");
				if($uid!="")
				{
					$pricelist=parent::rp_getValue("user","price_list","id='".$uid."'");
					$discountPer=parent::rp_getValue("pricelist","percentage","id='".$pricelist."'");
					
					if($discountPer!=0)
					{
						$discountAmount=$price*$discountPer/100;			
					}
					else
					{
						$discountAmount=0;
					}
					
					$finalPrice=$price-$discountAmount;
				}
				else
				{
					$finalPrice=$price;
				}
				$w['price']=$finalPrice;
				$product_detail['pricing'][]=$w;				
			}
			}
						
			
			// Fetching Favourite Flag if uid exits
			/*-----------------------------------------------------------*/
			
			// Fetching Category of that product
			$cid		=$product_detail['cid'];
			$cat_d		= mysql_fetch_assoc(parent::rp_getData("category","slug,name","id=".$cid));			
			$product_detail['category']=stripslashes($cat_d['name']);
			$product_detail['type']=stripslashes($cat_d['name']);
			// Fetching Category of that product			
			/*-----------------------------------------------------------*/
			return $product_detail;
		}else{
			
			return array();;
		}
	}
	public function aj_getProductPrice($pid,$priceid,$uid="")
	{
		
		$price=parent::rp_getValue("product_weight_price","price","id='".$priceid."' AND product_id='".$pid."'",0);
		if($price)
		{
			$weight_id=parent::rp_getValue("product_weight_price","weight_id","id='".$priceid."'","",0);
			$title=parent::rp_getValue("weight","name","id='".$weight_id."'");
			if($uid!="")
			{
				$pricelist=parent::rp_getValue("user","price_list","id='".$uid."'");
				$discountPer=parent::rp_getValue("pricelist","percentage","id='".$pricelist."'");

				if($discountPer!=0)
				{
					$discountAmount=$price*$discountPer/100;			
				}
				else
				{
					$discountAmount=0;
				}

				$finalPrice=$price-$discountAmount;
			} 
			else
			{
				$finalPrice=$price;
			}
			return array('ack'=>1,'title'=>$title,'price'=>$finalPrice,"weight_id"=>$weight_id);
		}
		else
		{
			return array('ack'=>0,'ack_msg'=>'Price list mismatch');
		}
		
	}
	public function aj_getRecipeDetail($rid,$uid=""){
		$q = 1;
		$recipe_r = parent::rp_getData("recipes","*","id='".$rid."' AND isDelete=0","",0);
		if(mysql_num_rows($recipe_r)>0){
			
			$recipe_detail=mysql_fetch_assoc($recipe_r);
			
			$recipe_detail['material']=explode("&xx5895",$recipe_detail['material']);
			$recipe_detail['steps']=explode("&xx5895",$recipe_detail['steps']);
			
			$recipe_detail['image_path']=FINAL_URL."".ADMINFOLDER."/".RECIPE."".$recipe_detail['image_path'];
			
			$recipe_detail['recipe_type']=parent::rp_getValue("recipe_category","name","id=".$recipe_detail['type'],"id LIMIT ".$limit);
			
			$alt_images=array();
			$alt_images_q=parent::rp_getData("recipe_alt_image","*","rid='".$recipe_detail['id']."'","display_order ASC",0);
			
			if(mysql_num_rows($alt_images_q)>0){
					
				while($f = mysql_fetch_array($alt_images_q)){
					array_push($alt_images,FINAL_URL."".ADMINFOLDER."/".RECIPE_ALT_A."".$f['image_path']);
				}
			}
			$recipe_detail['alt_img']=$alt_images;
			if(isset($_REQUEST['uid']) && $_REQUEST['uid']!="")
			{
					$uid=$_REQUEST['uid'];
					$fav=(parent::rp_getTotalRecord("recipe_favorite","uid = '".$uid."' AND rid='".$recipe_detail['id']."'",0)>0)?1:0;
			}
			else
			{
					$fav=0;
			}
			$recipe_detail['favorite']=$fav;		
			return $recipe_detail;
		}else{
			return array();;
		}
	}
	
	public function rp_updateCartQuantity() //update pro qty after succcessfull order
	{
		$shop_cart_r = parent::rp_getData("cartitems","*","cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'");
		if(mysql_num_rows($shop_cart_r)>0){
			while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
				$pid 		= $shop_cart_d['pid'];
				$subpid 	= $shop_cart_d['subpid'];
				$qty 		= $shop_cart_d['qty'];
				if($subpid>0){
					$pro_qty 	= parent::rp_getValue("sub_product","qty","id='".$subpid."'");
				}else{
					$pro_qty 	= parent::rp_getValue("product","qty","id='".$pid."'");
				}	
				$new_qty = intval($pro_qty)-intval($qty);
				if($new_qty==0){
					$rows 	= array(
						"qty"		=> $new_qty,
						"status"	=> "1",
					);
				}else{
					$rows 	= array(
						"qty"		=> $new_qty,
					);
				}
				if($subpid>0){
					$where	= "id='".$subpid."'";
					parent::rp_update("sub_product",$rows,$where);
					/****/
					$isDefault 	= parent::rp_getValue("sub_product","isDefault","id='".$subpid."'");
					if($isDefault>0){
						if($new_qty==0){
							$drows 	= array(
								"qty"		=> $new_qty,
								"status"	=> "1",
							);
						}else{
							$drows 	= array(
								"qty"		=> $new_qty,
							);
						}
						$dwhere	= "id='".$pid."'";
						parent::rp_update("product",$drows,$dwhere);
					}
					/****/
				}else{
					$where	= "id='".$pid."'";
					parent::rp_update("product",$rows,$where);
				}
			}
		}
	}
	public function aj_updateCartQuantity($cid) //update pro qty after succcessfull order
	{
		$shop_cart_r = parent::rp_getData("cartitems","*","cart_id='".$cid."'");
		if(mysql_num_rows($shop_cart_r)>0){
			while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
				$pid 		= $shop_cart_d['pid'];
				$subpid 	= $shop_cart_d['subpid'];
				$qty 		= $shop_cart_d['qty'];
				if($subpid>0){
					$pro_qty 	= parent::rp_getValue("sub_product","qty","id='".$subpid."'");
				}else{
					$pro_qty 	= parent::rp_getValue("product","qty","id='".$pid."'");
				}	
				$new_qty = intval($pro_qty)-intval($qty);
				if($new_qty==0){
					$rows 	= array(
						"qty"		=> $new_qty,
						"status"	=> "1",
					);
				}else{
					$rows 	= array(
						"qty"		=> $new_qty,
					);
				}
				if($subpid>0){
					$where	= "id='".$subpid."'";
					parent::rp_update("sub_product",$rows,$where);
					/****/
					$isDefault 	= parent::rp_getValue("sub_product","isDefault","id='".$subpid."'");
					if($isDefault>0){
						if($new_qty==0){
							$drows 	= array(
								"qty"		=> $new_qty,
								"status"	=> "1",
							);
						}else{
							$drows 	= array(
								"qty"		=> $new_qty,
							);
						}
						$dwhere	= "id='".$pid."'";
						parent::rp_update("product",$drows,$dwhere);
					}
					/****/
				}else{
					$where	= "id='".$pid."'";
					parent::rp_update("product",$rows,$where);
				}
			}
		}
	}
	public function rp_getDiscountAmount($disc_type,$discount,$totalprice){ // $disc_type : 0=flat, 1=perc
		if($disc_type==0){
			return $discount;
		}else{
			$discount_amt = $totalprice*($discount/100);
			return $discount_amt;
		}
	}
	
	public function rp_checkQtyToAddInCart($cart_id,$pid,$qty,$type,$subpid=0){ //check product qty before add to cart
		$curr_qty = parent::rp_getProductQty($pid,$subpid);
		
		if($type==2){
			$curr_cart_qty = 0;
		}else{
			if($cart_id>0){
				if($subpid>0){
					$curr_cart_qty = parent::rp_getValue("cartitems","qty","pid='".$pid."' AND subpid='".$subpid."' AND cart_id='".$cart_id."'");
				}else{
					$curr_cart_qty = parent::rp_getValue("cartitems","qty","pid='".$pid."' AND cart_id='".$cart_id."'");
				}
			}else{
				$curr_cart_qty = 0;
			}
		}
		$qty1 = $curr_cart_qty+$qty;
		if($qty1>$curr_qty){
			return 0;
		}else{
			return 1;
		}
	}
	
	public function rp_shipChargeUpdate(){ // update shipping charge in cart if Pincode avail
		if(isset($_SESSION['SHOPWALA_SESS_CART_ID']) && $_SESSION['SHOPWALA_SESS_CART_ID']>0 && isset($_SESSION['SHOPWALA_SESS_PINCODE']) && $_SESSION['SHOPWALA_SESS_PINCODE']!=""){
			$shop_cart_r = parent::rp_getData("cartitems","*","cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'");
			if(mysql_num_rows($shop_cart_r)>0){
				while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
					$pid 		= $shop_cart_d['pid'];
					$subpid 	= $shop_cart_d['subpid'];
					$qty 		= $shop_cart_d['qty'];
					$ship_charge = parent::rp_getShippingCharge($_SESSION['SHOPWALA_SESS_PINCODE'],$pid,$subpid);
					$ship_charge = parent::rp_num($ship_charge*$qty);
					$rows 	= array(
						"ship_charge"		=> $ship_charge,
					);
					$where	= "pid='".$pid."' AND cart_id='".$_SESSION['SHOPWALA_SESS_CART_ID']."'";
					parent::rp_update("cartitems",$rows,$where);
				}
			}
		}
	}
	public function aj_shipChargeUpdate($cid,$pincode){ // update shipping charge in cart if Pincode avail
		if($cid>0 && $pincode!=""){
			$shop_cart_r = parent::rp_getData("cartitems","*","cart_id='".$cid."'");
			if($shop_cart_r){
				while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
					$pid 		= $shop_cart_d['pid'];
					$subpid 	= $shop_cart_d['subpid'];				
					$qty 		= $shop_cart_d['qty'];
					$ship_charge = parent::rp_getShippingCharge($pincode,$pid,$subpid);
					$ship_charge = parent::rp_num($ship_charge*$qty);
					$rows 	= array(
						"ship_charge"		=> $ship_charge,
					);
					$where	= "pid='".$pid."' AND cart_id='".$cid."'";
					parent::rp_update("cartitems",$rows,$where);					
				}
			}
		}
	}
	public function rp_rcorder($rcOrder,$cart_id){ // return or cancel order
		//Get Cart Details
		$cart_details_r = parent::rp_getData("cartdetails","*","cart_id='".$cart_id."'");
		$cart_details_d = mysql_fetch_array($cart_details_r);
		$order_date 	= $cart_details_d["orderdate"];
		$order_status 	= $cart_details_d["orderstatus"];
		
		if($rcOrder==$cart_id+6){ // Ret
			// Return Order
			//return $this->rp_returnOrder($cart_id,$order_date,$order_status);
			$_SESSION['return_ty']			= 0;
			$_SESSION['return_cart_id'] 	= $cart_id;
			$_SESSION['return_cartitem_id'] = 0;
			return "ret";
		}else if($rcOrder==$cart_id+9){ //Can
			// Cancel Order
			//return $this->rp_cancelOrder($cart_id,$order_date,$order_status);
			$_SESSION['cancel_ty']			= 0;
			$_SESSION['cancel_cart_id'] 	= $cart_id;
			$_SESSION['cancel_cartitem_id'] = 0;
			return "can";
		}else{
			return "Something went wrong. Please try again or you can contact our customer care.";
		}
	}
	
	public function rp_rcorder_history($cart_id,$from_status,$to_status){ // Save RCOrder History Starts
		/****RCOrder Item Starts****/
		$today_date	= date('Y-m-d H:i:s');
		$shop_cart_r= parent::rp_getData("cartitems","*","cart_id='".$cart_id."'");
		if(mysql_num_rows($shop_cart_r)>0){
			while($shop_cart_d = mysql_fetch_array($shop_cart_r)){
				$cart_item_id 	= $shop_cart_d['id'];
				$uid 			= $shop_cart_d['uid'];
				$pid 			= $shop_cart_d['pid'];
				$subpid 		= $shop_cart_d['subpid'];
				$cdrows 	= array(
						"cart_id",
						"cart_item_id",
						"pid",
						"subpid",
						"uid",
						"from_status",
						"to_status",
						"rcdate",
					);
				$cdvalues = array(
						$cart_id,
						$cart_item_id,
						$pid,
						$subpid,
						$uid,
						$from_status,
						$to_status,
						$today_date,
					);
				parent::rp_insert("rcorder_items",$cdvalues,$cdrows);
				/****Update Cartitem Starts****/
				$rows 	= array(
						"rcdate"		=> $today_date,
						"orderstatus"	=> $to_status,
					);
				$where	= "id='".$cart_item_id."'";
				parent::rp_update("cartitems",$rows,$where);
				/****Update Cartitem Ends****/
			}
		}
		/****RCOrder Item Ends****/
		/****RCOrder Starts****/
		$uid = parent::rp_getValue("cartdetails","uid","cart_id='".$cart_id."'");
		$cdrows 	= array(
				"cart_id",
				"uid",
				"from_status",
				"to_status",
				"rcdate",
			);
		$cdvalues = array(
				$cart_id,
				$uid,
				$from_status,
				$to_status,
				$today_date,
			);
		$rcid = parent::rp_insert("rcorder",$cdvalues,$cdrows);
		return $rcid;
		/****RCOrder Ends****/
	}
	
	public function rp_rcSingleOrder_history($cart_id,$cartitem_id,$from_status,$to_status){ // Save RCSingleOrder History Starts
		/****RCOrder Single Item Starts****/
		$today_date	= date('Y-m-d H:i:s');
		$shop_cart_r= parent::rp_getData("cartitems","*","cart_id='".$cart_id."' AND id='".$cartitem_id."'");
		if(mysql_num_rows($shop_cart_r)>0){
			$shop_cart_d = mysql_fetch_array($shop_cart_r);
			$cart_item_id 	= $shop_cart_d['id'];
			$uid 			= $shop_cart_d['uid'];
			$pid 			= $shop_cart_d['pid'];
			$subpid 		= $shop_cart_d['subpid'];
			$cdrows 	= array(
					"cart_id",
					"cart_item_id",
					"pid",
					"subpid",
					"uid",
					"from_status",
					"to_status",
					"rcdate",
				);
			$cdvalues = array(
					$cart_id,
					$cartitem_id,
					$pid,
					$subpid,
					$uid,
					$from_status,
					$to_status,
					$today_date,
				);
			$rcsid = parent::rp_insert("rcorder_items",$cdvalues,$cdrows);
			return $rcsid;
		}
		/****RCOrder Single Item Ends****/
	}
	
	public function rp_returnOrder($cart_id,$order_date,$order_status){ // Return Full Order
		$last_date_to_return= date('Y-m-d', strtotime($order_date." +".RETURN_HOURS." hours"));
		$today_date			= date('Y-m-d');
		if(strtotime($today_date)<=strtotime($last_date_to_return)){
			if($order_status==4){
				/***Save History Starts***/
				$rcid = $this->rp_rcorder_history($cart_id,$order_status,"5");	
				/***Save History Ends***/
				$today_date1	= date('Y-m-d H:i:s');
				$rows 	= array(
						"rcdate"		=> $today_date1,
						"orderstatus"	=> "5",
					);
				$where	= "cart_id='".$cart_id."'";
				parent::rp_update("cartdetails",$rows,$where);
				$fn = new parent;
				$nt = new notification($fn);
				/**Send SMS**/
				$smsMsg = "We've receive your return request from order #".$cart_id.". We can reject or accept your request based on conditions ";
				$nt->rp_sendSMS2($cart_id,$smsMsg,SMSPROMOTEXT);
				/**Send SMS**/
				
				/*******************************************************/
				$toemail 	= $db->rp_getValue("cartdetails","email","cart_id='".$cart_id."'");
				$subject	= SITENAME." - Order #".$cart_id." Return Request";
				$body = file_get_contents(SITEURL.'mailbody/return_whole_order_request.php?cart_id='.$cart_id.'');
				$nt->rp_sendGenEmail($toemail,$subject,$body);
				/*******************************************************/
				
				$_SESSION['rcid'] = $rcid;
				return "Your order return request has been placed successfully.";
				
			}else{
				return "Your order is not delivered yet. So you can not return the order. Instead of that you can cancel your order. If you have any query than please contact our customer care.";
			}
		}else{
			return "Last date to 'Return Order' is already passed. You can not return order. If you have any query than please contact our customer care.";
		}
	}
	
	public function rp_cancelOrder($cart_id,$order_date,$order_status){ // update shipping charge in cart if Pincode avail
		$last_date_to_return= date('Y-m-d', strtotime($order_date." +".RETURN_HOURS." hours"));
		$today_date			= date('Y-m-d');
		if($order_status==2 || $order_status==3){
			if(strtotime($today_date)<=strtotime($last_date_to_return)){
				
				/**Update Qty Starts**/
				$this->rp_rcQtyUpdate($cart_id);
				/**Update Qty Ends**/
				
				/***Save History Starts***/
				$rcid = $this->rp_rcorder_history($cart_id,$order_status,"0");	
				/***Save History Ends***/
				
				$today_date1	= date('Y-m-d H:i:s');
				$rows 	= array(
						"rcdate"		=> $today_date1,
						"orderstatus"	=> "0",
					);
				$where	= "cart_id='".$cart_id."'";
				parent::rp_update("cartdetails",$rows,$where);
				
				$fn = new parent;
				$nt = new notification($fn); 
				/**Send SMS**/
				$smsMsg = "We've receive your cancel request for order #".$cart_id.". You can check your updated order in your Account. ";
				$nt->rp_sendSMS2($cart_id,$smsMsg,SMSPROMOTEXT);
				/**Send SMS**/
				
				/**Send Email**/
				$subject	= SITENAME." - Order #".$cart_id." Cancelled";
				$toemail 	= parent::rp_getValue("cartdetails","email","cart_id='".$cart_id."'");
				$body = file_get_contents(SITEURL.'mailbody/cancel_whole_order.php?cart_id='.$cart_id.'');
				$nt->rp_sendGenEmail($toemail,$subject,$body);
				/**Send Email**/
				
				$_SESSION['rcid'] = $rcid;
				return "Your order has been cancelled successfully.";
			}else{
				return "Last date to 'Cancel Order' is already passed. You can not cancel order. If you have any query than please contact our customer care.";
			}
		}else{
			return "Your order is delivered. You can not cancel your order. Instead of that you can return your order. If you have any query than please contact our customer care.";
		}
	}
	
	public function rp_getRefundAmount($cart_id){ // return or cancel order
		//Get Cart Details
		$cart_details_r = parent::rp_getData("cartdetails","*","cart_id='".$cart_id."'");
		$cart_details_d = mysql_fetch_array($cart_details_r);
		$order_status 	= $cart_details_d["orderstatus"];
		$total_ship_charge 	= parent::rp_num($cart_details_d["total_ship_charge"]);
		$cod_charge 		= parent::rp_num($cart_details_d["cod_charge"]);
		$finaltotal 		= parent::rp_num($cart_details_d["finaltotal"]);
		if($order_status==0){
			$prevoius_orderstatus = parent::rp_getValue("rcorder","from_status","cart_id='".$cart_id."' AND to_status='".$order_status."'");
			if($prevoius_orderstatus==3){ // Shipped than take shipping charge
				$refund_amount = parent::rp_num($finaltotal - $total_ship_charge - $cod_charge);
			}else{ //In Progress than refund all amount
				$refund_amount = parent::rp_num($finaltotal);
			}
		}elseif($order_status==5){ // Delivered order refund amount
			$refund_amount = parent::rp_num($finaltotal - $total_ship_charge - $cod_charge);
		}else{
			$refund_amount = 0.00;
		}
		return $refund_amount;
	}
	
	public function rp_getSingleItemRefundAmount($cart_id,$cart_item_id){ // return or cancel order
		//Get Cart Details
		$cart_item_r 	= parent::rp_getData("cartitems","*","cart_id='".$cart_id."' AND id='".$cart_item_id."'");
		$cart_item_d 	= mysql_fetch_array($cart_item_r);
		$order_status	= $cart_item_d["orderstatus"];
		$totalprice 	= parent::rp_num($cart_item_d["totalprice"]);
		return $totalprice;
	}
	
	public function rp_getSingleItemShippingRefundAmount($cart_id,$cart_item_id){ // return or cancel order
		//Get Cart Details
		$cart_item_r 	= parent::rp_getData("cartitems","*","cart_id='".$cart_id."' AND id='".$cart_item_id."'");
		$cart_item_d 	= mysql_fetch_array($cart_item_r);
		$order_status	= $cart_item_d["orderstatus"];
		$ship_charge	= parent::rp_num($cart_item_d["ship_charge"]);
		if($order_status==0){
			$prevoius_orderstatus = parent::rp_getValue("rcorder_items","from_status","cart_id='".$cart_id."' AND cart_item_id='".$cart_item_id."' AND to_status='".$order_status."'");
			if($prevoius_orderstatus==3){ // Shipped than take shipping charge
				$ship_refund_amount = 0.00;
			}else{ //In Progress than refund all amount
				$ship_refund_amount = parent::rp_num($ship_charge);
			}
		}elseif($order_status==5){ // Delivered order refund amount
			$ship_refund_amount = 0.00;
		}else{
			$ship_refund_amount = 0.00;
		}
		return $ship_refund_amount;
	}
	
	public function rp_getSingleItemCODRefundAmount($cart_id,$cart_item_id,$payment_method){ // return or cancel order
		if($payment_method==1){
			//Get Cart Details
			$cart_item_r 	= parent::rp_getData("cartitems","*","cart_id='".$cart_id."' AND id='".$cart_item_id."'");
			$cart_item_d 	= mysql_fetch_array($cart_item_r);
			$order_status	= $cart_item_d["orderstatus"];
			$totalprice 	= parent::rp_num($cart_item_d["totalprice"]);
			if($order_status==0){
				$prevoius_orderstatus = parent::rp_getValue("rcorder_items","from_status","cart_id='".$cart_id."' AND cart_item_id='".$cart_item_id."' AND to_status='".$order_status."'");
				if($prevoius_orderstatus==3){ // Shipped than take shipping charge
					$COD_refund_amount = 0.00;
				}else{ //In Progress than refund all amount
					$cod_charge	= parent::rp_num($totalprice*(COD_PER/100));
					if($cod_charge<COD_FLAT){
						$cod_charge = parent::rp_num(COD_FLAT);
					}
					$COD_refund_amount = parent::rp_num($cod_charge);
				}
			}elseif($order_status==5){ // Delivered order refund amount
				$COD_refund_amount = 0.00;
			}else{
				$COD_refund_amount = 0.00;
			}
		}else{
			$COD_refund_amount = 0.00;
		}
		return $COD_refund_amount;
	}
	
	public function rp_rcsingle_order($rcOrder,$cart_id,$cartitem_id){ // return or cancel order
		
		$cart_details_r = parent::rp_getData("cartdetails","orderdate,orderstatus","cart_id='".$cart_id."'");
		$cart_details_d = mysql_fetch_array($cart_details_r);
		$order_date 	= $cart_details_d["orderdate"];
		$order_status	= $cart_details_d["orderstatus"];
		
		if($rcOrder==$cartitem_id+6){ // Ret
			// Return Single Order
			/*if(parent::rp_getTotalRecord("cartitems","cart_id='".$cart_id."'")==1){
				return $this->rp_returnOrder($cart_id,$order_date,$order_status);
			}else{
				return $this->rp_returnSingleOrder($cart_id,$cartitem_id,$order_date,$order_status);
			}*/
			$_SESSION['return_ty']			= 1;
			$_SESSION['return_cart_id'] 	= $cart_id;
			$_SESSION['return_cartitem_id'] = $cartitem_id;
			return "ret";
		}else if($rcOrder==$cartitem_id+9){ // Can
			// Cancel Single Order
			/*if(parent::rp_getTotalRecord("cartitems","cart_id='".$cart_id."'")==1){
				return $this->rp_cancelOrder($cart_id,$order_date,$order_status);
			}else{
				return $this->rp_cancelSingleOrder($cart_id,$cartitem_id,$order_date,$order_status);
			}*/
			$_SESSION['cancel_ty']			= 1;
			$_SESSION['cancel_cart_id'] 	= $cart_id;
			$_SESSION['cancel_cartitem_id'] = $cartitem_id;
			return "can";
		}else{
			return "Something went wrong. Please try again or you can contact our customer care.";
		}
	}
	
	public function rp_returnSingleOrder($cart_id,$cartitem_id,$order_date,$order_status){ // return single order
		$last_date_to_return= date('Y-m-d', strtotime($order_date." +".RETURN_HOURS." hours"));
		$today_date			= date('Y-m-d');
		if(strtotime($today_date)<=strtotime($last_date_to_return)){
			if($order_status==4){
				
				/***Save History Starts***/
				$rcsid = $this->rp_rcSingleOrder_history($cart_id,$cartitem_id,$order_status,"5");	
				/***Save History Ends***/
				
				/****Update Cartitem Starts****/
				$rows 	= array(
						"rcdate"		=> $today_date,
						"orderstatus"	=> "5",
					);
				$where	= "id='".$cartitem_id."'";
				parent::rp_update("cartitems",$rows,$where);
				/****Update Cartitem Ends****/
				
				/*******Check ALL Item is Returned Starts*******/
				$this->rp_isAllRC($cart_id,$order_status,"5");
				/*******Check ALL Item is Returned Ends*******/
				$fn = new parent;
				$nt = new notification($fn);
				/**Send SMS**/
				$itemName = parent::rp_getValue("cartitems","name","id='".$cartitem_id."'");
				$msg = "Your mufat.in order #".$cart_id." item '".$itemName."' return request has been placed...";
				$nt->rp_sendSMS2($cart_id,$msg,SMSPROMOTEXT);
				/**Send SMS**/
				
				/**Send Email**/
				$subject	= SITENAME." - Order #".$cart_id." item '".$itemName."' Return Request";
				$toemail 	= parent::rp_getValue("cartdetails","email","cart_id='".$cart_id."'");
				$body = file_get_contents(SITEURL.'mailbody/return_single_order_item.php?cart_id='.$cart_id.'&ciid='.$cartitem_id.'');
				$nt->rp_sendGenEmail($toemail,$subject,$body);
				/**Send Email**/
				$_SESSION['rcsid'] = $rcsid;
				return "Your ordered item return request has been placed.";
			}else{
				return "Your order is not delivered yet. So you can not return the order item. Instead of that you can cancel your order item. If you have any query than please contact our customer care.";
			}
		}else{
			return "Last date to 'Return Order' is already passed. You can not return order item. If you have any query than please contact our customer care.";
		}
	}
	
	public function rp_cancelSingleOrder($cart_id,$cartitem_id,$order_date,$order_status){ // Cancel single order
		$last_date_to_return= date('Y-m-d', strtotime($order_date." +".RETURN_HOURS." hours"));
		$today_date			= date('Y-m-d');
		if(strtotime($today_date)<=strtotime($last_date_to_return)){
			if($order_status==2 || $order_status==3){
				
				/**Update Qty Starts**/
				$this->rp_rcSingleQtyUpdate($cartitem_id);
				/**Update Qty Ends**/
				
				/***Save History Starts***/
				$rcsid = $this->rp_rcSingleOrder_history($cart_id,$cartitem_id,$order_status,"0");	
				/***Save History Ends***/
				
				/****Update Cartitem Starts****/
				$rows 	= array(
						"rcdate"		=> $today_date,
						"orderstatus"	=> "0",
					);
				$where	= "id='".$cartitem_id."'";
				parent::rp_update("cartitems",$rows,$where);
				/****Update Cartitem Ends****/
				/*******Check ALL Item is Returned Starts*******/
				$this->rp_isAllRC($cart_id,$order_status,"0");
				/*******Check ALL Item is Returned Ends*******/
				
				$fn = new parent;
				$nt = new notification($fn); 
				/**Send SMS**/
				
				$msg = "Your mufat.in order #".$cart_id." item '".$itemName."' has been cancelled successfully...";
				$nt->rp_sendSMS2($cart_id,$msg,SMSPROMOTEXT);
				/**Send SMS**/
				
				/**Send Email**/
				$subject	= SITENAME." - Order #".$cart_id." item '".$itemName."' Cancelled";
				$toemail 	= parent::rp_getValue("cartdetails","email","cart_id='".$cart_id."'");
				$body = file_get_contents(SITEURL.'mailbody/cancel_single_order_item.php?cart_id='.$cart_id.'');
				$nt->rp_sendGenEmail($toemail,$subject,$body);
				/**Send Email**/
				$_SESSION['rcsid'] = $rcsid;
				return "Your ordered item has been cancelled successfully.";
			}else{
				return "Your order is delivered. You can not cancel your order. Instead of that you can return your order. If you have any query than please contact our customer care.";
			}
		}else{
			return "Last date to 'Cancel Order' is already passed. You can not cancel order. If you have any query than please contact our customer care.";
		}
	}
	
	public function rp_isAllRC($cart_id,$from_status,$to_status){
		//if all item in cartitem are returned or cancel than update cartdetails and rcorder
		$shop_cart_t = parent::rp_getTotalRecord("cartitems","cart_id='".$cart_id."' AND orderstatus='".$from_status."'");
		if($shop_cart_t>0){
			//Do nothing
		}else{
			/***UPdate Cartdetails Starts***/
			$today_date1	= date('Y-m-d H:i:s');
			$rows 	= array(
					"rcdate"		=> $today_date1,
					"orderstatus"	=> $to_status,
				);
			$where	= "cart_id='".$cart_id."'";
			parent::rp_update("cartdetails",$rows,$where);
			/***Update Cartdetails Ends***/
			/****RCOrder Starts****/
			$uid = parent::rp_getValue("cartdetails","uid","cart_id='".$cart_id."'");
			$cdrows 	= array(
					"cart_id",
					"uid",
					"from_status",
					"to_status",
					"rcdate",
				);
			$cdvalues = array(
					$cart_id,
					$uid,
					$from_status,
					$to_status,
					$today_date1,
				);
			parent::rp_insert("rcorder",$cdvalues,$cdrows);
			/****RCOrder Ends****/
		}
		
	}
	
	public function rp_getPaymentMode(){
		if(isset($_SESSION['SW_ADMIN_SESS_ID']) && $_SESSION['SW_ADMIN_SESS_ID']!=""){
			return parent::rp_getValue("ccavenue_paymentgateway","status","id=1");
		}else{
			return "0";
		}
	}
	
	public function rp_getShippingDiscount($sub_total,$shipping_charge){
		if(SDP>0){
			if($sub_total>=MOTAFSD){
				$disc_amount = parent::rp_num(($shipping_charge*SDP)/100);
				if($disc_amount<=$shipping_charge){
					return parent::rp_num($disc_amount);
				}else{
					return 0.00;
				}
			}else{
				return 0.00;
			}
		}else{
			return 0.00;
		}
	}
	
	public function rp_rcSingleQtyUpdate($cartitem_id){
		$ciid_up_d = mysql_fetch_array(parent::rp_getData("cartitems","pid,subpid,name,qty","id='".$cartitem_id."' AND orderstatus!=0 AND orderstatus!=5"));
		$itemName 	= stripslashes($ciid_up_d['name']);
		$itemPid	= $ciid_up_d['pid'];
		$itemSubPid	= $ciid_up_d['subpid'];
		$itemQty	= $ciid_up_d['qty'];
		
		if($itemSubPid>0){
			$sp_r 	= parent::rp_getData("sub_product","qty,status","id='".$itemSubPid."'");
			if(mysql_num_rows($sp_r)>0){
				$sp_d 	= mysql_fetch_array($sp_r);
				$cqty	= $sp_d["qty"];
				$cspstt	= $sp_d["status"];
				$nqty	= $cqty+$itemQty;
				if($cspstt==1 && $nqty>0){
					$nspstt	= 0;
				}else{
					$nspstt	= 0;
				}
				$qrows 	= array(
						"qty"		=> $cqty+$itemQty,
						"status"	=> $nspstt,
					);
				$qwhere	= "id='".$itemSubPid."'";
				parent::rp_update("sub_product",$qrows,$qwhere);
			}
		}else{
			$p_r 	= parent::rp_getData("product","qty,status","id='".$itemPid."'");
			if(mysql_num_rows($p_r)>0){
				$p_d 	= mysql_fetch_array($p_r);
				$cqty	= $p_d["qty"];
				$cpstt	= $p_d["status"];
				$nqty	= $cqty+$itemQty;
				if($cpstt==1 && $nqty>0){
					$npstt	= 0;
				}else{
					$npstt	= 0;
				}
				$qrows 	= array(
						"qty"		=> $cqty+$itemQty,
						"status"	=> $npstt,
					);
				$qwhere	= "id='".$itemPid."'";
				parent::rp_update("product",$qrows,$qwhere);
			}
		}
	}
	public function rp_rcQtyUpdate($cart_id){
		$rcQty_r = parent::rp_getData("cartitems","id","cart_id='".$cart_id."'  AND orderstatus!=0 AND orderstatus!=5");
		if(mysql_num_rows($rcQty_r)>0){
			while($rcQty_d = mysql_fetch_array($rcQty_r)){
				$this->rp_rcSingleQtyUpdate($rcQty_d['id']);
			}
		}
	}
}
include("notification.class.php");
include("ccavenue.class.php");
/*
	*** Cart Function Developed By Ravi Patel :) <<<
*/
?>