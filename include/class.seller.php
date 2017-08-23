<?php
class Seller extends Functions
{
	public $detail=array();
	public $ctable="seller";
	public $ctableFeed="seller_feed";
	public $ctableSellerFollower="seller_follower";
	public $ctableFeedLike="feed_like";
	public $ctableCompany="seller_company_info";
	public $ctableBank="seller_account";
	public $ctableProduct="product";
	public $ctableOrderItem="cartitems";
	public $db,$application;
	function __construct($id="") 
	{
		require_once("class.application.php");
		$db = new Functions();
		$this->application = new application();
		$conn = $db->connect();
		$this->db=$db;		   
    }     
	
	function addSeller($detail)//done
	{
		if(!empty($detail))
		{
			$isValid=$this->validateDetail($detail,array("name","email","password","phone","address","locality","city","zip","state","country"));
			if($isValid['ack']==1)
			{
				$countFromEmail=$this->countSeller($detail['email'],"email");
				$countFromPhone=$this->countSeller($detail['email'],"phone");
				if($countFromEmail<=0 && $countFromPhone<=0)
				{
					// Registration  of normal user
					$value=array($detail['name'],$detail['email'],md5($detail['password']),$detail['phoneno'],$detail['address'],$detail['locality'],$detail['city'],$detail['zip'],$detail['state'],$detail['country'],$detail['imei'],$detail['refresh_token'],$this->db->today());
					$rows=array("name","email","password","phone","address","locality","city","zip","state","country","imei","refresh_token","adate");
					$registerd_seller_id=$this->rp_insert($this->ctable,$value,$rows,0);
					if($registerd_seller_id!=0)
					{
						$seller_detail=$this->getSellerDetail($registerd_seller_id);
						if($seller_detail['ack']==1)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Seller Registered.","ack_msg"=>"Registration Successfull.","result"=>$seller_detail['result']);
							return $reply;
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Registration Failed.");
							return $reply;
						}		
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Registration Failed.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Email already exits","ack_msg"=>"Email already registered.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"user detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function addSellerFeed($detail)//done
	{
		//print_r($detail);
		//exit();
		if(!empty($detail))
		{
			$isValid=$this->validateDetail($detail,array("title","type","pid","seller_id","content","image_path"));
			
			if($isValid['ack']==1)
			{
				$countFromSeller=$this->countSeller($detail['seller_id'],"id");
				//print_r($countFromSeller);
				//exit();
				//$countFromPhone=$this->countSeller($detail['email'],"phone");
				if($countFromSeller==1)
				{
					if($detail['type']==0)
					{
						$detail['pid']=0;
					}
					else if($detail['type']==1)
					{
						
					}
					
					$value=array($detail['title'],$detail['type'],$detail['pid'],$detail['seller_id'],$detail['content'],$detail['image_path'],$this->db->today());
					
					$rows=array("title","type","pid","seller_id","content","image_path","adate");
					$registerd_seller_id=$this->rp_insert($this->ctableFeed,$value,$rows,0);
					
					if($registerd_seller_id!=0)
					{
						//$seller_detail=$this->getSellerDetail($registerd_seller_id);
						$reply=array("ack"=>1,"developer_msg"=>"Feed Added Successfully.","ack_msg"=>"Feed Added Successfully","result"=>$detail);
						return $reply;
							
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Feed Add Failed.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Seller Not Found","ack_msg"=>"Invalid Seller Id.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Feed detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"Feed detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}	
	function addFeedComment($detail)//done
	{
		//print_r($detail);
		//exit();
		if(!empty($detail))
		{
			$isValid=$this->validateDetail($detail,array("feed_id","commenter_id","content"));
			//$isValid['ack']
			if($isValid['ack']==1)
			{
				$countFromFeed=$this->countFeed($detail['feed_id'],"id");
				$countFromUser=$this->countUser($detail['commenter_id'],"id");
				
				if($countFromFeed==1)
				{
					if($countFromUser==1)
					{				
						$value=array($detail['feed_id'],$detail['commenter_id'],$detail['content'],$this->db->today(),$this->db->today());
					
						$rows=array("feed_id","commenter_id","content","adate","mdate",);
						$registerd_comment_id=$this->rp_insert("feed_comment",$value,$rows,0);
					
						if($registerd_comment_id!=0)
						{
							$result_r=$this->getFeedComment(array($registerd_comment_id),array(),array(),true);
							if($result_r['ack']==1)
							{
								
								$result['comment']=$result_r['result']['comment'];
								$reply=array("ack"=>1,"developer_msg"=>"Feed Comment Added Successfully.","ack_msg"=>"Feed Comment Added Successfully","result"=>$result);
								return $reply;
							}
						}				
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Feed Comment Add Failed.");
							return $reply;
						}
					
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"User Not Found","ack_msg"=>"Invalid User Id.");
						return $reply;
					}
						
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Feed Not Found","ack_msg"=>"Invalid Feed Id.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Feed detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"Feed detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function addNewProduct($detail)//done
	{
		if(!empty($detail))
		{
			$isValid=$this->validateDetail($detail,array("cid","sid","ssid","mode","name","seller_id"));
			if($isValid['ack']==1)
			{
				$countProductName=$this->countProductName($detail['name'],"name");
				
				if($countProductName<=0 && $detail['mode']=="add")
				{
					// Registration  of normal user
					$value=array($detail['cid'],$detail['sid'],$detail['ssid'],"0",$detail['name'],$this->db->today());
					$rows=array("cid","sid","ssid","isActive","name","adate");
					$registerd_seller_id=$this->rp_insert($this->ctableProduct,$value,$rows,0);
					
					$detail['product_id']="$registerd_seller_id";
					
					if($registerd_seller_id!=0)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Product Added Successfully.","ack_msg"=>"Product Added Successfully.","result"=>$detail);
							return $reply;
						/*else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Registration Failed.");
							return $reply;
						}*/		
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Product Add Failed.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Product Name already exits","ack_msg"=>"Product Name already registered.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Product detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}	
	function updateProductDraft($detail)//done
	{
		if(!empty($detail))
		{
			
			$isValid=$this->validateDetail($detail,
												array(    "mode",
														 // "seller_id",
														  "product_id",
														  "bid",
														  "name",
														  "sku",
														  "sold_by",
														  "max_price",
														  "sell_price",
														  "discount_price",
														  "pro_tax",
														  "ship_days",
														  "local_ship_charge",
														  "zonal_ship_charge",
														  "national_ship_charge",
														  "qty",
														  "min_qty_alert",
														  "image_path",
														  "banner_image_path",
														  "status",
														  "feature",
														  "descr",
														  "attr",
														  "pro_tag",
														  "isWhatsNew",
														  "isSale",
														  "isFeatured",
														  "isHot",
														  "isOffer",
														  "isAffiliate",
														  "isDeal"
													  ));
			
			if($isValid['ack']==1)
			{
				
				
				$where="name='".$detail['name']."' AND id!='".$detail['product_id']."'";
				$countProductName=$this->rp_getTotalRecord($this->ctableProduct,$where,0);
				$slug		= $this->rp_createProSlug($detail['name']);
				if($countProductName<=0 && $detail['mode']=="edit")
				{
					if($detail['action']=="draft")
					{
						$isActive="0";
					}
					else if($detail['action']=="reqapproval")
					{
						$isActive="1";
					}
					// Registration  of normal user
					$rows =array(
									"name"=>$detail['name'],
									"slug"=>$slug,
									"bid"=>$detail['bid'],
									"sku"=>$detail['sku'],
									"sold_by"=>$detail['sold_by'],
									"max_price"=>$detail['max_price'],
									"sell_price"=>$detail['sell_price'],
									"discount_price"=>$detail['discount_price'],
									"pro_tax"=>$detail['pro_tax'],
									"ship_days"=>$detail['ship_days'],
									"local_ship_charge"=>$detail['local_ship_charge'],
									"zonal_ship_charge"=>$detail['zonal_ship_charge'],
									"national_ship_charge"=>$detail['national_ship_charge'],
									"qty"=>$detail['qty'],
									"min_qty_alert"=>$detail['min_qty_alert'],
									"image_path"=>$detail['image_path'],
									"banner_image_path"=>$detail['banner_image_path'],
									"status"=>$detail['status'],
									"feature"=>$detail['feature'],
									"descr"=>$detail['descr'],
									"attr"=>$detail['attr'],
									"pro_tag"=>$detail['pro_tag'],
									"isWhatsNew"=>$detail['isWhatsNew'],
									"isSale"=>$detail['isSale'],
									"isFeatured"=>$detail['isFeatured'],
									"isHot"=>$detail['isHot'],
									"isOffer"=>$detail['isOffer'],
									"isDeal"=>$detail['isDeal'],
									"isAffiliate"=>$detail['isAffiliate'],
									"isActive"=>$isActive,
								);
					
					$registerd_seller_id=$this->rp_update($this->ctableProduct,$rows,'id="'.$detail['product_id'].'"',0);
					
					
					
					if($registerd_seller_id)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Product Update Successfully.","ack_msg"=>"Product Update In Draft Mode Successfully.","result"=>$detail);
							return $reply;
					
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Product Update Failed.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Product Name already exits","ack_msg"=>"Product Name already Exist.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Product detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function loginSeller($detail)//done
	{
		if(!empty($detail))
		{
			$isValid=$this->validateDetail($detail,array("email","password"));
			if($isValid['ack']==1)
			{
				$countFromEmail=$this->countSeller($detail['email'],"email");
				if($countFromEmail>=1)
				{
					$registerd_seller_id=$this->db->rp_getValue($this->ctable,"id","email='".$detail['email']."'",0);
					$seller_detail=$this->getSellerDetail($registerd_seller_id);
					//print_r($seller_detail);
					$seller_detail=$seller_detail['result'];
					if(($seller_detail['password']==md5($detail['password'])))
					{
						$values=array("imei"=>$detail['imei'],"refresh_token"=>$detail['refresh_token'],"last_login"=>$this->db->today());
						$this->db->rp_update($this->ctable,$values,"id='".$seller_detail['id']."'");
						
						if($seller_detail['status']==0)
						{
							$reply=array("ack"=>0,"developer_msg"=>"Email Not Verified!!","ack_msg"=>"Email not verified!!","result"=>$seller_detail);
						}
						else if($seller_detail['status']==1)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Email Verified Seller Redirect To Company Fillup Form.","ack_msg"=>"Successfully Logged in","result"=>$seller_detail);
						}
						else if($seller_detail['status']==2)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Email Verified And Company Registered. Waiting For Company Approval","ack_msg"=>"Successfully Logged in","result"=>$seller_detail);
						}
						else if($seller_detail['status']==3)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Email Verified And Company Registered Seller Redirect To Bank Fillup Form.","ack_msg"=>"Successfully Logged in","result"=>$seller_detail);
						}
						else if($seller_detail['status']==4)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Email Verified And Company Registered And Bank Registered. Waiting For Account Approval","ack_msg"=>"Successfully Logged in","result"=>$seller_detail);
						}
						else if ($seller_detail['status']==7)
						{
							$reply=array("ack"=>4,"developer_msg"=>"Suspended Seller.","ack_msg"=>"Your account has been suspended.","result"=>$seller_detail);
						}
						else if ($seller_detail['status']==5)
						{
							$reply=array("ack"=>4,"developer_msg"=>"Aprroved Seller","ack_msg"=>"Your account has been Approved.","result"=>$seller_detail);
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Unknown Status!!","ack_msg"=>"Internal Error!!");
						}
						
						if($seller_detail['imei']!=$detail['imei'])
						{
							$rows=array("imei"=>$detail['imei']);
							$whereupdate="id='".$seller_detail['id']."'";
							$updateimei=$this->db->rp_update($this->ctable,$rows,$whereupdate,0);
						}
						
						//print_r($detail);
						//exit();
						if($seller_detail['refresh_token']!=$detail['refresh_token'])
						{
							$rows=array("refresh_token"=>$detail['refresh_token']);
							$whereupdate="id='".$seller_detail['id']."'";
							$updateimei=$this->db->rp_update($this->ctable,$rows,$whereupdate,0);
						}
						
						return $reply;
						
						
					}
					
					else
					{							
						$reply=array("ack"=>0,"developer_msg"=>"Email and password not match.","ack_msg"=>"Email and password match.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"email not registered","ack_msg"=>"Email not registered.","invalid_field"=>$isValid['invalid']);
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"user detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function getOrder($detail)
	{
		if(($detail['id']!=NULL || $detail['id']!=""))
		{
			$countFromEmail=$this->countSeller($detail['id'],"id");
			if($countFromEmail>0)
			{
				$result=$this->db->rp_getData("cartdetails","*"," seller_id='".$detail['id']."'","",0);
				if(mysql_num_rows($result)>0)
				{
					
					$detail1=array();
					while($detail=mysql_fetch_array($result))
					{
						$data=array();
						//$detail1=mysql_fetch_assoc($detail);
						$data=array(
										"cart_id"=>$detail['cart_id'],
										"seller_id"=>$detail['seller_id'],
										"uid"=>$detail['uid'],
										"orderdate"=>$detail['orderdate'],
										"shipdate"=>$detail['shipdate'],
										"deliverydate"=>$detail['deliverydate'],
										"orderstatus"=>$detail['orderstatus'],
										"subtotal"=>$detail['subtotal'],
										"coupon_id"=>$detail['coupon_id'],
										"coupon_code"=>$detail['coupon_code'],
										"total_ship_charge"=>$detail['total_ship_charge'],
										"SDP"=>$detail['SDP'],
										"MOTAFSD"=>$detail['MOTAFSD'],
										"total_shipping_discount"=>$detail['total_shipping_discount'],
										"payment_method"=>$detail['payment_method'],
										"COD_PER"=>$detail['COD_PER'],
										"COD_FLAT"=>$detail['COD_FLAT'],
										"cod_charge"=>$detail['cod_charge'],
										"finaltotal"=>$detail['finaltotal'],
										"notes"=>$detail['notes'],
										"track_url"=>$detail['track_url'],
										"name"=>$detail['name'],
										"address1"=>$detail['address1'],
										"locality"=>$detail['locality'],
										"phone"=>$detail['phone'],
										"zip"=>$detail['zip'],
										"city"=>$detail['city'],
										"state"=>$detail['state'],
										"country"=>$detail['country'],
										"email"=>$detail['email'],
									);
						
						array_push($detail1,$data);
						
					}
					
					//print_r($detail1);
					
					$reply=array("ack"=>1,"developer_msg"=>"seller detail found","ack_msg"=>"Seller detail found.","result"=>$detail1);
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Invalid Seller","ack_msg"=>"Invalid Seller");
				return $reply;
			}
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"Missing Parameter","ack_msg"=>"Missing Parameter!!.");
			return $reply;
		}
	}
	function updateOrderStatus($detail)//done
	{
		
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['sellerid'],"id");
			
			if($countFromId>=1)
			{
				$isValid=$this->validateDetail($detail,array("sellerid","orderid","orderitemid","orderstatus","trackurl"));
				
				if($isValid['ack']==1)
				{
				
					// Detail of normal user
					$where="cart_id='".$detail['orderid']."' AND id='".$detail['orderitemid']."' AND seller_id='".$detail['sellerid']."'";
					
					$values=array("orderstatus"=>$detail['orderstatus'],
								  "track_url"=>$detail['trackurl'],
								  
								  );
					
					$registerd_seller_id=$this->rp_update($this->ctableOrderItem,$values,$where,0);
					
					
					if($detail['orderstatus']==3)
					{
						$valuesdate=array("shipdate"=>$this->db->today(),);
						$registerd_seller_id1=$this->rp_update($this->ctableOrderItem,$valuesdate,$where,0);
						
					}
					else if($detail['orderstatus']==4)
					{
						$valuesdate=array("deliverydate"=>$this->db->today(),);
						$registerd_seller_id1=$this->rp_update($this->ctableOrderItem,$valuesdate,$where,0);
					
					}
					else if($detail['orderstatus']==5)
					{
						$valuesdate=array("rcdate"=>$this->db->today(),);
						$registerd_seller_id1=$this->rp_update($this->ctableOrderItem,$valuesdate,$where,0);
						
					}
					
					
					if($registerd_seller_id)
					{
						//$seller_detail=$this->getSellerDetail($detail['id']);
						$reply=array("ack"=>1,"developer_msg"=>"Order Status Updated.","ack_msg"=>"Order Status Updated successfully.","result"=>$detail);
						return $reply;
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Profile couldn't be updated. Try later!!");
						return $reply;
					}				
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Something Went Wrong","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller id not valid","ack_msg"=>"Seller id not valid!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function getSellerProfile($detail,$required_columns=array(),$isProductRequired=true,$isFeedRequired=true,$isFollowerRequired=true)
	{
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['id'],"id");
			if($countFromId>=1)
			{
				$seller_info_r=$this->db->rp_getData("seller",$required_columns,"id='".$detail['id']."'");
				if($seller_info_r)
				{
					$feeds=array();
					$seller_info=mysql_fetch_assoc($seller_info_r);
					
					$seller_info['isUserFollowSeller']=($this->db->rp_getTotalRecord("seller_follower","follower_id='".$detail['uid']."' AND seller_id='".$detail['id']."'")>0)?1:0;
					$seller_info['image_path']=SITEURL.ADMINFOLDER."/".SELLER_MAIN.$seller_info['image_path'];
					
					if($isFeedRequired)
					{
						$feeds=$this->getFeed(array(),array($detail['id']),array(),$detail['uid']);
						if($feeds['ack']==1)
						{
							$seller_info['feeds']=$feeds['result'];
							$seller_info['countFeeds']=$feeds['count'];
						}
						else
						{
							$seller_info['feeds']=array();
							$seller_info['countFeeds']=0;
						}
						
					
					}
					if($isProductRequired)
					{
						$products=$this->getProduct(array(),array($detail['id']),$detail['uid'],array("id","name","sid","ssid","cid","sell_price","max_price","discount_price","image_path","banner_image_path","seller_id","rate"),true);
						if($feeds['ack']==1)
						{$seller_info['products']=$products['result'];
						$seller_info['countProducts']=$products['count'];
						}
						else
						{
							$seller_info['products']=array();
							$seller_info['countProducts']=0;
						}
					}
					
					if($isFollowerRequired)
					{
						$follower=$this->getSellerFollower(array($detail['id']));
						if($follower['ack']==1)
						{
							
							$seller_info['follower']=$follower['result'];
							$seller_info['countFollowers']=$follower['count'];
							
						}
						else
						{
							$seller_info['follower']=array();
							$seller_info['countFollowers']=0;
						}
					
					}
					
					$reply=array("ack"=>1,"developer_msg"=>"Seller info fetched","ack_msg"=>"Great!! Seller Info Fetched!!","result"=>$seller_info);
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Seller info not found","ack_msg"=>"Internal Error!!");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	
	function getProduct($pids=array(),$sids=array(),$uid="",$required_columns=array(),$isDetailRequired=true)
	{
	//	print_r($sids);
		//exit();
		$limit=$this->getLimit();	
		$count=0;		
		if(!empty($sids) || !empty ($pids))
		{	
			$seller_ids=implode(",",$sids);
			if($seller_ids!="")
			$count=$this->db->rp_getTotalRecord("product","seller_id IN (".$seller_ids.")");
			$products=array("ack"=>0,"result"=>array());
			if($isDetailRequired)
			{
				$products=$this->application->getProduct($pids,array(),array(),array(),array(),array(),$uid,$required_columns,array(),false,$sids);	
			}
			if($products['ack']==1)	
			{$reply=array("ack"=>1,"count"=>$count,"result"=>$products['result'],"developer_msg"=>"Seller Product Fetched","ack_msg"=>"Sorry !! No seller product found.");
			return $reply;
			}
			else
			{$reply=array("ack"=>0,"developer_msg"=>"no product found for this seller in database.","ack_msg"=>"Sorry !! No product found.");
			return $reply;	
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no product found for this seller in database.","ack_msg"=>"Sorry !! No product found.");
			return $reply;
		}
	}	
	function getFeed($fids=array(),$sids=array(),$required_columns=array(),$uid="") //done
	{
		
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		$where="";
		if(!empty($fids))
		{
			$fids=implode(",",$fids);
			$where=$this->db->generateWhere($where,"id IN (".$fids.")");
						
		}
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"seller_id IN (".$sids.")");
						
		}		
		if($where!="")
		{
			$where.=" AND isDelete=0";
			$feeds=$this->db->rp_getData("seller_feed",$required_columns,$where,"",0,$limit);
		}
		else
		{			
			$feeds=$this->db->rp_getData("seller_feed",$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($feeds)
		{
			$result=array();
			$count=$this->db->rp_getTotalRecord("seller_feed",$where);
			while($r=mysql_fetch_assoc($feeds))
			{
				if($r['type']==1)
				{
					$pid=$r['pid'];
					$product=$this->application->getProduct(array($pid),array(),array(),array(),array(),array(),$uid,array("id","name","sid","ssid","cid","sell_price","max_price","discount_price","image_path","banner_image_path","rate","seller_id"),array("1"=>"No Limit"),false,array());	
						
					if($product['ack']==1)
					{
										
						$r['product_detail']=$product['result'][0];
					}
					else
					{
						continue;
					}

				}				
				$seller_detail=$this->getSellerDetail($r['seller_id'],array("name","id","image_path"));
				//print_r($seller_detail);
				if($seller_detail['ack']==1)
				{
					
					$isUserLiked=0;
					if($uid!="")
					{
						$isUserLiked=$this->db->rp_getTotalRecord("feed_like","liker_id='".$uid."' AND feed_id='".$r['id']."'");
					}
					
					$r['isLiked']=$isUserLiked;
					$r['seller_info']=$seller_detail['result'];
					$countLike=$this->getFeedLikes(array(),array($r['id']));
					if($countLike['ack']==1)
					$r['countLike']=$countLike['result']['count'];
					else
					$r['countLike']=0;	
									
					$countShare=$this->getFeedShare(array(),array($r['id']));
					if($countShare['ack']==1)
					$r['countShare']=$countLike['result']['count'];
					else
					$r['countShare']=0;
					
					
					$countComment=$this->getFeedComment(array(),array($r['id']));
					if($countComment['ack']==1)
					$r['countComment']=$countComment['result']['count'];
					else
					$r['countComment']=0;
					$r['content']=htmlspecialchars($r['content']);
					$r['image_path']=SITEURL.ADMINFOLDER."/".FEED_MAIN.$r['image_path'];
					$result[]=$r;
				}
			}
			$reply=array("ack"=>1,"count"=>$count,"result"=>$result,"developer_msg"=>"Feeds found in database.","ack_msg"=>"Great !! Feeds  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no feeds found in database.","ack_msg"=>"Sorry !! No feeds found.");
			return $reply;
		}
	}
	function getSeller($sids=array(),$required_columns=array(),$cids=array()) //done
	{
		
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		$where="";
		$sellers=false;
		if(!empty($cids))
		{
			$cids=implode(",",$cids);
			
			$whereCategory=$this->db->generateWhere($where,"cid IN (".$cids.")");
			$categoryDetail_r=$this->rp_getData("category","id,name","id in (".$cids.")","",0);
			if($categoryDetail_r)
			{
				$categoryDetail=mysql_fetch_assoc($categoryDetail_r);
				$sellersIds_r=$this->db->rp_getData("product","seller_id",$whereCategory,"",0);
				if($sellersIds_r)
				{
					while($t=mysql_fetch_assoc($sellersIds_r))
					{
						$sids[]=$t['seller_id'];
					}
				}
				if(!empty($sids))
				{
					$sids=array_unique($sids);
					$sids=implode(",",$sids);
					$where=$this->db->generateWhere($where,"id IN (".$sids.")");
								
				}		
				if($where!="")
				{
					
					$where.=" AND isDelete=0";
					$sellers=$this->db->rp_getData("seller",$required_columns,$where,"name ASC",0,$limit);
				}
				
				
				if($sellers)
				{
					$result=array();
					$count=$this->db->rp_getTotalRecord("sellers",$where);
					while($r=mysql_fetch_assoc($sellers))
					{
						if($r['image_path']!="")
						{
							$r['image_path']=SITEURL.ADMINFOLDER."/".SELLER_MAIN.$r['image_path'];
						}
						$categoryDetail['sellers'][]=$r;
						
					}
					$reply=array("ack"=>1,"result"=>array($categoryDetail),"developer_msg"=>"Sellers found in database.","ack_msg"=>"Great !! Sellers fetched.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"no sellers found in database.","ack_msg"=>"Sorry !! No sellers found.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"no sellers found in database.","ack_msg"=>"Sorry !! No sellers found.");
				return $reply;
			}
			
		}
		else
		{
					
			$categories=$this->rp_getData("category","id,name","isDelete=0 AND isActive=1");
			if($categories)
			{
				
				while($c=mysql_fetch_assoc($categories))
				{
					
					$sids=array();
					$sellers=false;
					$where="";
					$whereCategory=$this->db->generateWhere($where,"cid='".$c['id']."'");
					$sellersIds_r=$this->db->rp_getData("product","seller_id",$whereCategory,"",0);
					if($sellersIds_r)
					{
						
						while($t=mysql_fetch_assoc($sellersIds_r))
						{
							
							$sids[]=$t['seller_id'];
						}
						
					}
					if(!empty($sids))
					{
						$sids=array_unique($sids);
						$sids=implode(",",$sids);						
						$where=$this->db->generateWhere($where,"id IN (".$sids.")");
									
					}
					
					if($where!="")
					{
						
						$where.=" AND isDelete=0";
						$sellers=$this->db->rp_getData("seller",$required_columns,$where,"name ASC",0,$limit);
					}
					if($sellers)
					{
						while($r=mysql_fetch_assoc($sellers))
						{
							$r['name']=$this->db->clean($r['name']);
							if($r['image_path']!="")
							{
								$r['image_path']=SITEURL.ADMINFOLDER."/".SELLER_MAIN.$r['image_path'];
							}
							$c['sellers'][]=$r;
							
						}
					}
					
					$result[]=$c;
				}
				$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Sellers found in database.","ack_msg"=>"Great !! Sellers fetched.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"no sellers found in database.","ack_msg"=>"Sorry !! No sellers found.");
				return $reply;
			}
		}
		
	}
	function getSellerFollower($sids=array(),$required_columns=array(),$isUserInfoRequired=false)
	{
		$required_columns=$this->getRequiredColumns($required_columns);		
		$seller_followers=false;	
		$where="";		
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"seller_id IN (".$sids.")");
						
		}
		if($where!="")
		{
			$seller_followers=$this->db->rp_getData("seller_follower",$required_columns,$where,"",0);
		}
		
		
		if($seller_followers)
		{
			
			$count=mysql_num_rows($seller_followers);
			$result=array();
			if($isUserInfoRequired)
			{
				while($r=mysql_fetch_assoc($seller_followers))
				{
					$user_r=$this->db->rp_getData("user","name,image_path,email","id='".$r['follower_id']."'");
					if($user_r)
					{
						$user=mysql_fetch_assoc($user_r);
						$result['user'][]=$user;
					}													
				}	
			}			
			$reply=array("ack"=>1,"count"=>$count,"result"=>$result,"developer_msg"=>"Fetched Seller Followers in database.","ack_msg"=>"Great !! Feeds  Followers Fetched.");
			return $reply;
		}
		else
		{			
			$reply=array("ack"=>0,"developer_msg"=>"no seller followers found in database.","ack_msg"=>"Sorry !! No Followers found.");
			return $reply;
		}
	}
	function getFeedLikes($lids=array(),$sids=array(),$required_columns=array(),$isUserInfoRequired=false)
	{
		$required_columns=$this->getRequiredColumns($required_columns);				
		$where="";
		$feed_likes=false;
		if(!empty($lids))
		{
			$lids=implode(",",$lids);
			$where=$this->db->generateWhere($where,"id IN (".$lids.")");
						
		}
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"feed_id IN (".$sids.")");
						
		}		
		if($where!="")
		{
			$feed_likes=$this->db->rp_getData("feed_like",$required_columns,$where,"",0);
		}
		
		
		if($feed_likes)
		{
			
			$result['count']=mysql_num_rows($feed_likes);
			if($isUserInfoRequired)
			{
				while($r=mysql_fetch_assoc($feed_likes))
				{
					$user_r=$this->db->rp_getData("user","name,image_path,email","id='".$r['liker_id']."'");
					if($user_r)
					{
						$user=mysql_fetch_assoc($user_r);
						$result['user'][]=$user;
					}													
				}	
			}			
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Feeds Likes in database.","ack_msg"=>"Great !! Feeds  Like Fetched.");
			return $reply;
		}
		else
		{			
			$reply=array("ack"=>0,"developer_msg"=>"no feeds found in database.","ack_msg"=>"Sorry !! No feeds found.");
			return $reply;
		}
	}
	function getFeedShare($shids=array(),$fids=array(),$required_columns=array(),$isUserInfoRequired=false)
	{
		
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		$where="";
		$feed_share=false;
		if(!empty($shids))
		{
			$shids=implode(",",$shids);
			$where=$this->db->generateWhere($where,"id IN (".$shids.")");
						
		}
		if(!empty($fids))
		{
			$fids=implode(",",$fids);
			$where=$this->db->generateWhere($where,"feed_id IN (".$fids.")");
						
		}		
		
		if($where!="")
		{
			
			$feed_share=$this->db->rp_getData("feed_share",$required_columns,$where,"",0);
		}
		
		
		if($feed_share)
		{
			$result['count']=mysql_num_rows($feed_share);
			if($isUserInfoRequired)
			{
				while($r=mysql_fetch_assoc($feed_share))
				{
					$user_r=$this->db->rp_getData("user","name,image_path,email","id='".$r['sharer_id']."'");
					if($user_r)
					{
						$user=mysql_fetch_assoc($user_r);
						$result['user'][]=$user;
					}													
				}	
			}			
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Feeds Share in database.","ack_msg"=>"Great !! Feeds  Share Fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no feeds share found in database.","ack_msg"=>"Sorry !! No feeds share found.");
			return $reply;
		}
	}
	function getFeedComment($coids=array(),$fids=array(),$required_columns=array(),$isCommentInfoRequired=false)
	{
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		$where="";
		$feed_comment=false;
		if(!empty($coids))
		{
			$coids=implode($coids);
			$where=$this->db->generateWhere($where,"id IN (".$coids.")");
						
		}
		if(!empty($fids))
		{
			$fids=implode(",",$fids);
			$where=$this->db->generateWhere($where,"feed_id IN (".$fids.")");
						
		}		
		if($where!="")
		{
			$feed_comment=$this->db->rp_getData("feed_comment",$required_columns,$where,"",0,$limit);
		}
		
		
		if($feed_comment)
		{
			$result['count']=mysql_num_rows($feed_comment);
			if($isCommentInfoRequired)
			{
				while($r=mysql_fetch_assoc($feed_comment))
				{					
					$user_r=$this->db->rp_getData("user","name,image_path,email","id='".$r['commenter_id']."'");
					if($user_r)
					{
						$user=mysql_fetch_assoc($user_r);
						if($user['image_path']!="")
						{
							$user['image_path']=SITEURL.ADMINFOLDER."/".USER_MAIN.$user['image_path'];
						}
						$r['user']=$user;
					}
					$result['comment'][]=$r;		
				}	
			}			
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Feeds Comments in database.","ack_msg"=>"Great !! Feeds Comments Fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no feeds comments found in database.","ack_msg"=>"Sorry !! No feeds comments found.");
			return $reply;
		}
	}
	function updateSellerProfile($detail)//done
	{
		
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['id'],"id");
			if($countFromId>=1)
			{
				$isValid=$this->validateDetail($detail,array("name","address","locality","city","zip","state","country"));
				if($isValid['ack']==1)
				{
					// Detail of normal user
					$where=" id='".$detail['id']."'";
					$values=array("name"=>$detail['name'],"address"=>$detail['address'],"phone"=>$detail['phoneno'],"locality"=>$detail['locality'],"city"=>$detail['city'],"zip"=>$detail['zip'],"state"=>$detail['state'],"country"=>$detail['country']);			
					$registerd_seller_id=$this->rp_update($this->ctable,$values,$where,0);
					if($registerd_seller_id!=0)
					{
						$seller_detail=$this->getSellerDetail($detail['id']);
						$reply=array("ack"=>1,"developer_msg"=>"Seller Profile Updated.","ack_msg"=>"Profile updated successfully.","result"=>$seller_detail['result']);
						return $reply;
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Profile couldn't be updated. Try later!!");
						return $reply;
					}				
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"user detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"user id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function updateSellerStatus($detail)//done
	{
		
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['id'],"id");
			if($countFromId>=1)
			{
				$isValid=$this->validateDetail($detail,array("profile_status"));
				if($isValid['ack']==1)
				{
					// Detail of normal user
					$where=" id='".$detail['id']."'";
					$values=array("profile_status"=>$detail['profile_status']);			
					$registerd_seller_id=$this->rp_update($this->ctable,$values,$where,0);
					if($registerd_seller_id!=0)
					{
						$seller_detail=$this->getSellerDetail($detail['id']);
						$reply=array("ack"=>1,"developer_msg"=>"Seller Profile Updated.","ack_msg"=>"Profile status updated successfully.","result"=>$seller_detail['result']);
						return $reply;
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Profile status couldn't be updated. Try later!!");
						return $reply;
					}				
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"user detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"user id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function updateSellerCompanyProfile($detail)//done
	{
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['sid'],"id");
			$countFromPAN=$this->countPAN($detail['pan']);
			$countFromCompany=$this->countCompanyProfile($detail['sid'],$this->ctableCompany);
			if($countFromId>=1)
			{
				if($countFromPAN<=0)
				{
					if($countFromCompany<=0)
					{
					$isValid=$this->validateDetail($detail,array("name","type","phone","address","locality","city","zip","state","country","pan","tin","vat"));
					if($isValid['ack']==1)
					{
						
						// Registration  of normal user
						$value=array($detail['sid'],$detail['name'],$detail['email'],$detail['phone'],$detail['address'],$detail['locality'],$detail['city'],$detail['zip'],$detail['state'],$detail['country'],$detail['pan'],$detail['tin'],$detail['vat'],$this->db->today());
						$rows=array("sid","name","email","phone","address","locality","city","zip","state","country","pan","tin","vat","adate");
						$registerd_seller_company_id=$this->rp_insert("seller_company_info",$value,$rows,0);
						if($registerd_seller_company_id!=0)
						{
							$seller_detail=$this->getSellerDetail($detail['sid']);
							$reply=array("ack"=>1,"developer_msg"=>"Seller Company Registered.","ack_msg"=>"Company Registration Successfull.","result"=>$seller_detail['result']);
							return $reply;
						}				
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Company Registration Failed.");
							return $reply;
						}				
					
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Seller detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
						return $reply;
					}
				  }
				  else
				  {
					$reply=array("ack"=>0,"developer_msg"=>"Company Profile Already Available!","ack_msg"=>"Company Profile Already Available!");
					return $reply;
				  }
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"PAN already associated with another account","ack_msg"=>"PAN already associated with another account.");
					return $reply;
				}
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function updateSellerBankInfo($detail)//done
	{
		//echo "abcdjnksjd";
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['sid'],"id");
			$countFromCompany=$this->countCompanyProfile($detail['sid'],$this->ctableBank);
			if($countFromId>=1)
			{		
				if($countFromCompany<=0)
					{
				$isValid=$this->validateDetail($detail,array("sid","bname","accno","ifsc","baddress","zip"));
				if($isValid['ack']==1)
				{
					
					// Registration  of normal user
					$value=array($detail['sid'],$detail['bname'],$detail['accno'],$detail['ifsc'],$detail['baddress'],$detail['zip'],$this->db->today());
					$rows=array("sid","bank_name","account_no","ifsc","address","zip","adate");
					$registerd_seller_company_id=$this->rp_insert($this->ctableBank,$value,$rows,0);
					if($registerd_seller_company_id!=0)
					{
						$seller_detail=$this->getSellerDetail($detail['sid']);
						$reply=array("ack"=>1,"developer_msg"=>"Company Account Registered.","ack_msg"=>"Account Registration Successfull.","result"=>$seller_detail['result']);
						return $reply;
					}				
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Account Registration Failed.");
						return $reply;
					}				
				
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Seller detail not valid","ack_msg"=>"Invalid details.","invalid_field"=>$isValid['invalid']);
					return $reply;
				}
			   }
				  else
				  {
					$reply=array("ack"=>0,"developer_msg"=>"Company Account Already Available!","ack_msg"=>"Company Account Already Available!");
					return $reply;
				  }
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function updateSellerProfilePicture($detail)
	{
		
		if(!empty($detail))
		{
			$countFromId=$this->countSeller($detail['id'],"id");
			if($countFromId>=1)
			{
				if (isset($_FILES["file"])) {
					$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
					$temp = explode(".", $_FILES["file"]["name"]);
					 $extension = end($temp);
				 
					if($_FILES["file"]["error"]>0) {
						$error .= "Error opening the file. ";
					}
					if($_FILES["file"]["type"]=="application/x-msdownload"){	
						$error .= "Mime type not allowed. ";
					}
					if(!in_array($extension, $allowedExts)){
						$error .= "Extension not allowed. ";
					}
					if($_FILES["file"]["size"] > 26214400){ //26214400 Bytes = 25 MB, 102400 = 100KB
						$error .= "File size shoud be less than 25 MB ";
					}
					if($error=="") {
						
						$fileName 	= $db->clean($_FILES["file"]["name"]);			
						$fileSize 	= round($_FILES["file"]["size"]); // BYTES			
						
						$adate 		= date('Y-m-d H:i:m');
						$r = checkUserStorage($id,$totalStorage,$usedStorage,$fileSize);

						if($r=='success'){
							
							$extension	= end(explode(".", $fileName));				
							$fileName	= $id.'_'.substr(sha1(time()), 0, 6).".".$extension;
							$filePath 	= "aws/tempImg/".$fileName;
							$temp2="tempImg/".$fileName;	
							move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
							$responses=file_get_contents("http://ednurture.net/ednurture_app/webservice/aws/aws.php?filepath=".$temp2."&filename=".$fileName."&user_id=".$id);	
							$responses=json_decode($resposes);				
							if($responses['ack']=1)
							{
									
												  include('aws/awsService.php');
												  
												   $aws=new AWS();
																						 
									   $resp=$aws->deleteObject($oldFileName);	

									   $rows=array('used_storage'=>$usedStorage-$resp['fileSize']);
									   $db->rp_update('user_personal_info',$rows,"id='".$id."'",0);
									   $response = array(
										"status"		=> 1,
										"res"			=> "success",
										"msg"			=> "File uploaded successfully",
										"fileName"		=> $fileName,
										"result"=>$responses,
										"deleteFileResult"=>$resp
									);
								
									unlink($filePath);	
									
							}
							else
							{	
								$response = array(
										"status"		=> 0,
										"res"			=> "Error occurred while storing file!!",
										"fileName"		=> $fileName,
										"result"=>$responses
										
									);
							}
						
						}
						else
						{
							$response = array(
										"status"		=> 0,
										"res"			=> $r

										
									);
						}
						
						
						
						
					}
					else
					{
						
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"image type not valid","ack_msg"=>"Invalid image or image not found.");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"user id not valid","ack_msg"=>"Internal Error!!");
				return $reply;
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}	
	function maintainSellerFollower($uid,$fid,$operation)
	{
		$countFromId=$this->countUser($uid,"id");			
		if($countFromId>=1)
		{
			$where="follower_id='".$uid."' AND seller_id='".$fid."'";	
			$count=$this->rp_getTotalRecord("seller_follower",$where);
			if($operation==1)
			{
				
				if($count==0)					
				{
					 //Add Product to wishlist				
					 $values=array($uid,$fid,$this->db->today());
					 $rows=array("follower_id","seller_id","adate");
					 $feed_like_id=$this->rp_insert("seller_follower",$values,$rows,0);
					 if($feed_like_id!=0)
					 {
						$count=$this->countSellerFollower($fid,"seller_id"); 
						$reply=array("ack"=>1,"developer_msg"=>"Followed!!","ack_msg"=>"Followed!!","result"=>array("count"=>$count));
						return $reply;
					 }
					 else
					 {
						 $reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Internal Error!!");
						return $reply;
					 }
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Already Followed","ack_msg"=>"Already Followed!!.");
					return $reply;
				}
				
			}
			if($operation==0)
			{				
				if($count==1)					
				{
					 //remove Product from wishlist									
					 $seller_followed_id=$this->rp_getValue("seller_follower","id",$where);			
					 $isRemoved=$this->rp_delete("seller_follower","id='".$seller_followed_id."'",0);
					 if($isRemoved)
					 {
						$count=$this->countSellerFollower($fid,"seller_id");  
						$reply=array("ack"=>1,"developer_msg"=>"UnFollowed!!","ack_msg"=>"UnFollowed!!","result"=>array("count"=>$count));
						return $reply;
					 }
					 else
					 {
						 $reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Internal Error!!");
						return $reply;
					 }
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Already UnFollowed!!","ack_msg"=>"Already UnFollowed!!");
					return $reply;
				}
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Operation not found","ack_msg"=>"Internal Error!!");
				return $reply;
			}
																		
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"user id not valid","ack_msg"=>"Internal Error!! You are blocked or account suspended.");
			return $reply;
		}
	}
	function getSellerDetail($user_id=0,$required_columns=array()) //done //Get Personal Information of Seller
	{
		$required_columns=$this->getRequiredColumns($required_columns);
		if($user_id!=0)
		{
			$where="id='".$user_id."'";
			$result=$this->rp_getData($this->ctable,$required_columns,$where,"",0);
			if($result)
			{
				$detail=mysql_fetch_assoc($result);
				if($detail['image_path']!="")
				{
					$detail['image_path']=SITEURL.ADMINFOLDER."/".SELLER_MAIN.$detail['image_path'];
				}
				$reply=array("ack"=>1,"developer_msg"=>"seller detail found","ack_msg"=>"Seller detail found.","result"=>$detail);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
			return $reply;
		}
	}
	function getSellerCompanyDetail($user_id=0,$required_columns=array()) //done //Get Company Information of Seller
	{
		$required_columns=$this->getRequiredColumns($required_columns);
		if($user_id!=0)
		{
			$where="id='".$user_id."'";
			$result=$this->rp_getData($this->ctableCompany,$required_columns,$where,"",0);
			if($result)
			{
				$detail=mysql_fetch_assoc($result);
				$reply=array("ack"=>1,"developer_msg"=>"seller detail found","ack_msg"=>"Seller detail found.","result"=>$detail);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
			return $reply;
		}
	}
	function getSellerAccountDetail($user_id=0,$required_columns=array()) //done //Get Account Information of Seller
	{
		$required_columns=$this->getRequiredColumns($required_columns);
		if($user_id!=0)
		{
			$where="sid='".$user_id."'";
			$result=$this->rp_getData($this->ctableBank,$required_columns,$where,"",0);
			if($result)
			{
				$detail=mysql_fetch_assoc($result);
				$reply=array("ack"=>1,"developer_msg"=>"seller detail found","ack_msg"=>"Seller detail found.","result"=>$detail);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Seller detail not found","ack_msg"=>"Seller not found.");
			return $reply;
		}
	}
	function validateDetail($detail,$validateKey)
	{
		$isValid=true;
		$result=array("invalid"=>array());
		// Name Validation
		if(array_key_exists("name",$validateKey) && !array_key_exists("name",$detail) && strlen(trim(" ",$detail['name']))>0)
		{
			$result['invalid']['name']="Name must be entered.";
			$isValid=false;
		}
		
		// Profile status Validation
		if(array_key_exists("profile_status",$validateKey) && !array_key_exists("profile_status",$detail) && strlen(trim(" ",$detail['profile_status']))>0 && strlen(trim(" ",$detail['profile_status']))<160)
		{
			$result['invalid']['name']="Profile status should be 160 character long.";
			$isValid=false;
		}
		
		// Email Validation
		if(array_key_exists("email",$validateKey) && !array_key_exists("email",$detail) && strlen($detail['email'])>0)
		{
			$result['invalid']['email']="Email must be entered.";
			$isValid=false;
		}
		else if(array_key_exists("email",$validateKey) && filter_var($detail['email'], FILTER_VALIDATE_EMAIL) === false)
		{
			$result['invalid']['email']="Email is not valid.";
			$isValid=false;
		}
		
		// Password Validation
		if(array_key_exists("password",$validateKey) && !array_key_exists("password",$detail) && strlen($detail['password'])>0)
		{
			$result['invalid']['password']="Password must be entered.";
			$isValid=false;
		}
		
		// Phone Validation
		if(array_key_exists("phone",$validateKey) && !array_key_exists("phone",$detail) && strlen($detail['phone'])>0)
		{
			$result['invalid']['phone']="Phone must be entered.";
			$isValid=false;
		}
		
		// Address Validation
		if(array_key_exists("phone",$validateKey) && !array_key_exists("phone",$detail) && strlen($detail['phone'])>0)
		{
			$result['invalid']['phone']="Phone must be entered.";
			$isValid=false;
		}
		// Locality Validation
		if(array_key_exists("locality",$validateKey) && !array_key_exists("locality",$detail) && strlen($detail['locality'])>0)
		{
			$result['invalid']['locality']="Locality must be entered.";
			$isValid=false;
		}
		// City Validation
		if(array_key_exists("city",$validateKey) && !array_key_exists("city",$detail) && strlen($detail['city'])>0)
		{
			$result['invalid']['city']="City must be entered.";
			$isValid=false;
		}
		
		// Zip Validation
		if(array_key_exists("zip",$validateKey) && !array_key_exists("zip",$detail) && strlen($detail['zip'])>0 && strlen($detail['zip']<=6))
		{
			$result['invalid']['state']="Not Valid Zip.";
			$isValid=false;
		}
		
	
		// State Validation
		if(array_key_exists("state",$validateKey) && !array_key_exists("state",$detail) && strlen($detail['state'])>0)
		{
			$result['invalid']['state']="State must be entered.";
			$isValid=false;
		}
		
		// Country Validation
		if(array_key_exists("country",$validateKey) && !array_key_exists("country",$detail) && strlen($detail['country'])>0)
		{
			$result['invalid']['country']="Country must be entered.";
			$isValid=false;
		}
		
		
		// Pan Validation
		if(array_key_exists("pan",$validateKey) && !array_key_exists("pan",$detail) && strlen($detail['pan'])<16)
		{
			$result['invalid']['pan']="Not Valid PAN No.";
			$isValid=false;
		}
		
		// vat Validation
		if(array_key_exists("vat",$validateKey) && !array_key_exists("vat",$detail) && strlen($detail['vat'])<16)
		{
			$result['invalid']['vat']="Not Valid VAT No.";
			$isValid=false;
		}
		
		// tin Validation
		if(array_key_exists("tin",$validateKey) && !array_key_exists("tin",$detail) && strlen($detail['tin'])<16)
		{
			$result['invalid']['tin']="Not Valid TIN No.";
			$isValid=false;
		}
		if($isValid)
		{
			return array("ack"=>1);
		}
		else
		{
			$result['ack']=0;
			return $result;
		}
		
	}
	function getSellerId($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getValue($this->ctable,"id",$where);
		return $count;
	}
	function countSeller($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctable,$where,0);
		return $count;
	}
	function countFeedLike($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableFeedLike,$where,0);
		return $count;
	}
	function countProductName($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableProduct,$where,0);
		return $count;
	}
	function countPAN($val)
	{
		$where="pan='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableCompany,$where,0);
		return $count;
	}
	function countFeed($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableFeed,$where,0);
		return $count;
	}
	function countUser($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord("user",$where,0);
		return $count;
	}
	function countCompanyProfile($val,$table)
	{
		$where="sid='".$val."'";
		//echo $table;
		$count=$this->rp_getTotalRecord($table,$where,0);
		return $count;
	}
	function countSellerFollower($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableSellerFollower,$where,0);
		return $count;
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
				$ll="10";
			}			
			$limit_string="".$ul.",".$ll;
			return $limit_string;
		}
		else
		{
			return "";
		}
	}
	
}
?>