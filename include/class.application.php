<?php
class Application extends Functions
{
	public $detail=array();
	public $db,$seller;
	public $home_item_type=array("offer","advertise");
	public $product_from=array("android_home_item","sub_sub_category","wishlist");
	public $sortby=array("popularity","whats_new","discount","price_h_to_l","price_l_to_h");
	function __construct($id="") 
	{
		
		$db = new Functions();
		$conn = $db->connect();		
		$this->db=$db;		  
    }
	function getCategory($cids=array(),$required_columns=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);		
		$limit=$this->getLimit();		
		if(!empty($cids))
		{
			$where.=" AND isDelete=0";
			$cids=implode(",",$cids);
			$category=$this->db->rp_getData("category",$required_columns,"id IN (".$cids.")","",0,$limit);			
		}
		else
		{
			$category=$this->db->rp_getData("category",$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($category)
		{
			while($r=mysql_fetch_assoc($category))
			{
				$r['image_path']=ADMINSITEURL.CATEGORY_MAIN.$r['image_path'];
				$r['banner_image_path']=ADMINSITEURL.CATEGORY_BANNER.$r['banner_image_path'];
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Category found in database.","ack_msg"=>"Great !! Category  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no category found in database.","ack_msg"=>"Sorry !! No category found.");
			return $reply;
		}
	}
	function getSubCategory($sids=array(),$cids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();		
		$where="";
		if(!empty($cids))
		{
			$cids=implode(",",$cids);
			$where=$this->db->generateWhere($where,"cid IN (".$cids.")");
						
		}
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"id IN (".$sids.")");
						
		}		
		if($where!="")
		{
			$where.=" AND isDelete=0";
			$sub_category=$this->db->rp_getData("sub_category",$required_columns,$where,"",0,$limit);
		}
		else
		{			
			$sub_category=$this->db->rp_getData("sub_category",$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($sub_category)
		{
			while($r=mysql_fetch_assoc($sub_category))
			{
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Sub category found in database.","ack_msg"=>"Great !! Sub category  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no sub category found in database.","ack_msg"=>"Sorry !! No sub category found.");
			return $reply;
		}
	}
	function getSubSubCategory($ssids=array(),$sids=array(),$cids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);
		$limit=$this->getLimit();			
		$where="";
		if(!empty($cids))
		{
			$cids=implode(",",$cids);
			$where=$this->db->generateWhere($where,"cid IN (".$cids.")");
						
		}
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"sid IN (".$sids.")");
						
		}
		if(!empty($ssids))
		{
			$ssids=implode(",",$ssids);
			$where=$this->db->generateWhere($where,"id IN (".$ssids.")");
						
		}	
		if($where!="")
		{
			$where.=" AND isDelete=0";
			$sub_sub_category=$this->db->rp_getData("sub_sub_category",$required_columns,$where,"",0,$limit);
		}
		else
		{			
			$sub_sub_category=$this->db->rp_getData("sub_sub_category",$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($sub_sub_category)
		{
			while($r=mysql_fetch_assoc($sub_sub_category))
			{
				if(array_key_exists("attr",$r))
				{
					$r['attr']=unserialize($r['attr']);
					$attr=array();
					foreach($r['attr'] as $key => $value)
					{
						$a_value=$this->getAttribute(array($key));
						$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
						$attr[]=array("aid"=>$key,"title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
					}
					$r['attr']=$attr;
				}
				
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Sub to Sub category found in database.","ack_msg"=>"Great !! Child Sub category  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no sub sub category found in database.","ack_msg"=>"Sorry !! No child sub category found.");
			return $reply;
		}
	}	
	function getProduct($pids=array(),$protag=array(),$bids=array(),$ssids=array(),$sids=array(),$cids=array(),$uid="",$required_columns=array(),$limit=array(),$isFilterRequired=true,$sellerids=array(),$sortOption="",$query="")
	{
		
		require_once("class.seller.php");
		$this->seller=new Seller();
		
		$result=array();
		if(empty($limit))
		{
			$limit=$this->getLimit();	
		}
		else
		{
			$limit="";
		}
				
		$where="";
		$orderby="";
		if(!empty($cids))
		{
			$cids=implode(",",$cids);
			$where=$this->db->generateWhere($where,"cid IN (".$cids.")");
						
		}
		if(!empty($sids))
		{
			$sids=implode(",",$sids);
			$where=$this->db->generateWhere($where,"sid IN (".$sids.")");
						
		}
		if(!empty($ssids))
		{
			$ssids=implode(",",$ssids);
			$where=$this->db->generateWhere($where,"ssid IN (".$ssids.")");
						
		}
		if(!empty($bids))
		{
			$bids=implode(",",$bids);
			$where=$this->db->generateWhere($where,"bid IN (".$bids.")");
				
		}
		if(!empty($sellerids))
		{
			//print_r($sellerids);
			//exit();
			$sellerids=implode(",",$sellerids);
			$where=$this->db->generateWhere($where,"seller_id IN (".$sellerids.")");
						
		}
		if(!empty($protag))
		{
			$protagQuery="";
			foreach($protag as $p)
			{
				$protagQuery=$this->db->generateLike($protagQuery,"pro_tag LIKE '%".$p."%'","OR");
			}
			$where=$this->db->generateWhere($where,$protagQuery);
						
		}
		if(!empty($query))
		{
			$protagQuery="";
			$protagQuery=$this->db->generateLike($protagQuery,"name LIKE '%".$query."%'","OR");
			$protagQuery=$this->db->generateLike($protagQuery,"pro_tag LIKE '%".$query."%'","OR");		
			$where=$this->db->generateWhere($where,$protagQuery);
						
		}
		if(!empty($pids))
		{
			$pids=implode(",",$pids);
			$where=$this->db->generateWhere($where,"id IN (".$pids.")");
						
		}	
		
		// Sorting Required?
		if($sortOption!="")
		{
			if($sortOption==0)
			{
				// Popularity
				$orderby="rate desc";
			}
			else if ($sortOption==1)
			{
				// WhatsNew
				$where=$this->db->generateWhere($where,"isWhatsNew=1");
			}
			else if ($sortOption==2)
			{
				// Price High to low
				$required_columns[]="(max_price-sell_price) AS discount";
				$orderby="discount desc";
			}else if ($sortOption==3)
			{
				// Price High to low
				$orderby="max_price desc";
			}
			else if ($sortOption==4)
			{
				// Price low to high
				$orderby="max_price asc";
			}
		}
		$required_columns=$this->getRequiredColumns($required_columns);
		
		if($where!="")
		{
			$where.=" AND isDelete=0";
			$product=$this->db->rp_getData("product",$required_columns,$where,$orderby,0,$limit,1);
		}
		else
		{			
			$product=$this->db->rp_getData("product",$required_columns,"1=1 AND isDelete=0",$orderby,0,$limit);
		}
		
		if($product)
		{
			while($r=mysql_fetch_assoc($product))
			{
				$r['category_name']=$this->db->rp_getValue("category","name","id='".$r['cid']."'");
				$r['sub_category_name']=($temp=$this->db->rp_getValue("sub_category","name","id='".$r['sid']."'"))?$temp:"";
				$r['sub_sub_category_name']=($temp=$this->db->rp_getValue("sub_sub_category","name","id='".$r['sid']."'"))?$temp:"";
				$r['image_path']=ADMINSITEURL.PRODUCT_MAIN.$r['image_path'];
				$r['banner_image_path']=ADMINSITEURL.PRODUCT_BANNER.$r['banner_image_path'];
				if(array_key_exists("size_chart_image_path",$r))
				$r['size_chart_image_path']=ADMINSITEURL.PRODUCT_SIZE_CHART.$r['size_chart_image_path'];
				if($r['max_price']!=0)
				$r['discount_price']=round((floatval($r['max_price'])-floatval($r['sell_price']))*100/floatval($r['max_price']));
				else
				$r['discount_price']=100;	
				$seller_info=$this->seller->getSellerDetail($r['seller_id'],array("id","name","email","image_path"));
					
				$r['seller_info']=array();
				if($seller_info['ack']==1)
				{						
					$r['seller_info']=$seller_info['result'];					
				}
				if(array_key_exists("discount",$r))
				{
					$r['discount']=((intval($r['max_price'])-intval($r['sell_price'])));
				}
				if(array_key_exists("attr",$r))
				{
					$r['attr']=unserialize($r['attr']);
					$attr=array();
					if(is_array($r['attr']))
					{
						foreach($r['attr'] as $key => $value)
					{
						$a_value=$this->getAttribute(array($key));
						$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
						if($av_value['ack']==1)
						$attr[]=array("id"=>$key,"title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
					}
					$r['attr']=$attr;
					}
					
					$a_image_r=$this->getProductAltImage(array($r['id']));
					$r['alt_image_path']=array();
					if($a_image_r['ack']==1)
					{
						
						$r['alt_image_path']=$a_image_r['result'];					
					}
					else
					{
						$r['alt_image_path'][]=$r['image_path'];
					}
					
					
					
					
					$r['descr']=html_entity_decode($r['descr']);
					$r['feature']=html_entity_decode($r['feature']);
				}
				if($uid!="" && $uid!=0)
				{					
					$wishlistCount=$this->db->rp_getTotalRecord("wishlist","uid='".$uid."' AND pid='".$r['id']."'",0);
					if($wishlistCount>=1)
					{
						$r['isWishListed']=1;
					}
					else
					{
						$r['isWishListed']=0;
					}
				}
				else
				{
					$r['isWishListed']=0;
				}
				$result[]=$r;
			}
			$filter=array();
			if($isFilterRequired)
			{
				if(empty($bids) && $query=="" && $protag=="")
				{					
						if(empty($ssids))
						{
							$ssids_r= $this->db->rp_getData("sub_sub_category","id","sid='".$sid."'");
							if($ssids_r)
							{
								while($w=mysql_fetch_assoc($ssids_r))
								{
									$ssids[]=$w['id'];
								}
							}
							$ssids=implode(",",$ssids);
						}
					
						$sids=array();
						$bids=array();
						$prices=array();
						$sids_r=$this->db->rp_getData("sub_sub_category","sid","id IN(".$ssids.")","",0);
						if($sids_r)
						{
							while($t=mysql_fetch_assoc($sids_r))
							{
								
								$sids[]=$t['sid'];
							}
						}
						
								
					if(!empty($ssids))
					{
						$sids=array();
						$bids=array();
						$prices=array();
						$sids_r=$this->db->rp_getData("sub_sub_category","sid","id IN(".$ssids.")","",0);
						if($sids_r)
						{
							while($t=mysql_fetch_assoc($sids_r))
							{
								
								$sids[]=$t['sid'];
							}
						}
						
						$sids_d=$this->getSubSubCategory(array(),$sids,array(),array("id","id as aid","id as value","name"));
						if($sids_d['ack']==1)
						$subcategory=$sids_d['result'];
						
						for($i=0;$i<sizeof($subcategory);$i++)
						{
							
							$subcategory[$i]['aid']=5895;
							
						}					
						$filter[]=array("aid"=>5895,"title"=>"category","type"=>"fix","avids"=>$subcategory);
					
						$sids=implode(",",$sids);
						$bids_r=$this->db->rp_getData("product","bid","sid IN(".$sids.")","",0);
						if($bids_r)
						{
							while($t=mysql_fetch_assoc($bids_r))
							{
								if($t['bid']!=0)
								$bids[]=$t['bid'];
							}
						}
						
						$bids_d=$this->getBrand($bids,array("id ","id as aid","id as value","name"));
						if($bids_d['ack']==1)
						{
							$bids=$bids_d['result'];
							for($i=0;$i<sizeof($bids);$i++)
							{
								
								$bids[$i]['aid']=5896;
								
							}
							$filter[]=array("aid"=>5896,"title"=>"brand","type"=>"fix","avids"=>$bids);					
						}
						
						$price_r=$this->db->rp_getData("product","MAX(max_price),MIN(max_price)","sid IN(".$sids.")","",0);
						if($price_r)
						{
							
							$t=mysql_fetch_assoc($price_r);
							$max=intval($t['MAX(max_price)']);
							$min=intval($t['MIN(max_price)']);
							$n = 500;
							$range=array();
							  $length_of_range = round($max / $n);
							  if($length_of_range!=0)
							  {
								for($i=1;$i<=$length_of_range;$i++)
								  {
									$a['min']= $min; 
									$a['max']= $min+500; 
									$min=$min+500;
									  if($min<500)
									  {
										 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>"Below 500"); 
										
									  }
									  else
									  {
										 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max']);  
									  }							   						  
								   
								  }  
							  }
												
							  
							 
						}
						
						$filter[]=array("aid"=>5897,"title"=>"price","type"=>"fix","avids"=>$prices);
						
						$attr_r=$this->db->rp_getData("sub_category","attr","id IN(".$sids.")","",0);
						if($attr_r)
						{
							while($r=mysql_fetch_assoc($attr_r))
							{						
								if(array_key_exists("attr",$r))
								{
									$r['attr']=unserialize($r['attr']);
									$attr=array();
									foreach($r['attr'] as $key => $value)
									{
										$a_value=$this->getAttribute(array($key));
										$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
										if($av_value['ack']==1)
										$filter[]=array("aid"=>$key,"type"=>"dynamic","title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
									}								
									
								}
							}
						}
						
						
					}
				}
				else
				{
						
						$ssids_r= $this->db->rp_getData("sub_sub_category","id","1=1");
						if($ssids_r)
						{
							$ssids=array();
							while($w=mysql_fetch_assoc($ssids_r))
							{
								$ssids[]=$w['id'];
							}
						}
						$ssids=implode(",",$ssids);
						
					
						$sids=array();
						$bids=array();
						$prices=array();
						$sids_r=$this->db->rp_getData("sub_sub_category","sid","id IN(".$ssids.")","",0);
						if($sids_r)
						{
							while($t=mysql_fetch_assoc($sids_r))
							{
								
								$sids[]=$t['sid'];
							}
						}
						
								
					if(!empty($ssids))
					{
						$sids=array();
						$bids=array();
						$prices=array();
						$sids_r=$this->db->rp_getData("sub_sub_category","sid","id IN(".$ssids.")","",0);
						if($sids_r)
						{
							while($t=mysql_fetch_assoc($sids_r))
							{
								
								$sids[]=$t['sid'];
							}
						}
						
						$sids_d=$this->getSubSubCategory(array(),$sids,array(),array("id","id as aid","id as value","name"));
						if($sids_d['ack']==1)
						$subcategory=$sids_d['result'];
						
						for($i=0;$i<sizeof($subcategory);$i++)
						{
							
							$subcategory[$i]['aid']=5895;
							
						}					
						$filter[]=array("aid"=>5895,"title"=>"category","type"=>"fix","avids"=>$subcategory);
					
						$sids=implode(",",$sids);
						$bids_r=$this->db->rp_getData("product","bid","sid IN(".$sids.")","",0);
						if($bids_r)
						{
							while($t=mysql_fetch_assoc($bids_r))
							{
								if($t['bid']!=0)
								$bids[]=$t['bid'];
							}
						}
						
						$bids_d=$this->getBrand($bids,array("id ","id as aid","id as value","name"));
						if($bids_d['ack']==1)
						{
							$bids=$bids_d['result'];
							for($i=0;$i<sizeof($bids);$i++)
							{
								
								$bids[$i]['aid']=5896;
								
							}
							$filter[]=array("aid"=>5896,"title"=>"brand","type"=>"fix","avids"=>$bids);					
						}
						
						$price_r=$this->db->rp_getData("product","MAX(max_price),MIN(max_price)","sid IN(".$sids.")","",0);
						if($price_r)
						{
							
							$t=mysql_fetch_assoc($price_r);
							$max=intval($t['MAX(max_price)']);
							$min=intval($t['MIN(max_price)']);
							$n = 500;
							$range=array();
							  $length_of_range = round($max / $n);
							  if($length_of_range!=0)
							  {
								for($i=1;$i<=$length_of_range;$i++)
								  {
									$a['min']= $min; 
									$a['max']= $min+500; 
									$min=$min+500;
									  if($min<500)
									  {
										 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>"Below 500"); 
										
									  }
									  else
									  {
										 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max']);  
									  }							   						  
								   
								  }  
							  }
												
							  
							 
						}
						
						$filter[]=array("aid"=>5897,"title"=>"price","type"=>"fix","avids"=>$prices);
						
						$attr_r=$this->db->rp_getData("attribute","id","1=1","",0);
						if($attr_r)
						{
							while($r=mysql_fetch_assoc($attr_r))
							{						
											
								$a_value=$this->getAttribute(array($r['id']));
								$av_value=$this->getAttributeValue("",array($a_value['result'][0]['id']),array("id","aid","name","value"));
								if($av_value['ack']==1)
								$filter[]=array("aid"=>$a_value['result'][0]['id'],"type"=>"dynamic","title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
															
								
							}
						}
						
						
					}
				}
				
			}
			
			$reply=array("ack"=>1,"result"=>$result,"filter"=>$filter,"developer_msg"=>"Product found in database.","ack_msg"=>"Great !! Product fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no product found in database.","ack_msg"=>"Sorry !! No product found.");
			return $reply;
		}
	}	
	function getFilteredProduct($detail,$required_columns=array(),$limit=array(),$isFilterRequired=false,$sortOption="")
	{
		require_once("class.seller.php");
		$this->seller=new Seller();
		
		$result=array();
		$limit=$this->getLimit();			
		$filter=$detail['filter'];
		$sid=$detail['sid'];
		$refine=array("where"=>array(),"order"=>array());
		$attributes=array();
		if($sid!="" && $sid!=0)
		{
			$sidWhere=" AND sid='".$sid."'";
		}
		else
		{
			$sidWhere="";
		}
		if(!empty($filter))
		{
			foreach($filter as $f)
			{
				$filter_id=$f->aid;
				$filter_values=$f->avids;
				if($filter_id==5895)
				{
					if(!empty($filter_values))
					{
						//category refine
						$ssids=implode(",",$filter_values);
						$refine['where'][]="ssid IN (".$ssids.")";
					}
				}
				else if($filter_id==5896)
				{
					if(!empty($filter_values))
					{
						//brand refine
						$bids=implode(",",$filter_values);
						$refine['where'][]="bid IN (".$bids.")";
					}
					
				}
				else if($filter_id==5897)
				{
					if(!empty($filter_values))
					{
						$ctable_where="";					
						foreach($filter_values  as $t)
						{
							if($t!="")
							{
								$price=explode("-",$t);
								$price_min=floatval($price[0]);
								$price_max=floatval($price[1]);							
								$ctable_where.="(max_price >='".$price_min."' AND max_price<='".$price_max."') OR ";
							}
							
						}
						
						$ctable_where = trim($ctable_where," OR");
						$refine['where'][]=$ctable_where;
					}
					
				}
				else
				{
					if(!empty($filter_values))
					{
						$ctable_where="";					
						foreach($filter_values  as $t)
						{
							if($t!=0)
							$ctable_where.="attr REGEXP '.*;s:[0-9]+:\"".$t."\".*' OR ";
						}
						
						$ctable_where = trim($ctable_where," OR");
						$refine['where'][]="(".$ctable_where.")";
					}
				}
			}
			//print_r($attributes);
			if(!empty($refine))
			{
				$where="";
				foreach($refine['where'] as $r)
				{
					if($where!="")
					{
						$where.=" AND ".$r;
					}
					else 
					{
						$where=$r;
					}
				}
				$where="(".$where.")";
				$orderby="";
				foreach ($refine['order'] as $o)
				{
					if($orderby!="")
					{
						$orderby.=",".$r;
					}
					else 
					{
						$orderby=$r;
					}
				}
				
			}
			
			// Sorting Required?
			if($sortOption!="")
			{
				if($sortOption==0)
				{
					// Popularity
					$orderby="rate desc";
				}
				else if ($sortOption==1)
				{
					// WhatsNew
					$where=$this->db->generateWhere($where,"isWhatsNew=1");
				}
				else if ($sortOption==2)
				{
					// Price High to low
					$required_columns[]="(max_price-sell_price) AS discount";
					$orderby="discount desc";
				}else if ($sortOption==3)
				{
					// Price High to low
					$orderby="max_price desc";
				}
				else if ($sortOption==4)
				{
					// Price low to high
					$orderby="max_price asc";
				}
			}
			$required_columns=$this->getRequiredColumns($required_columns);
		
			if($where!="")
			{
				$where.=" AND isDelete=0".$sidWhere;
				$product=$this->db->rp_getData("product",$required_columns,$where,$orderby,0,$limit,0);
			}
			else
			{			
				$product=$this->db->rp_getData("product",$required_columns,"1=1 AND isDelete=0".$sidWhere,$orderby,0,$limit);
			}
		
		if($product)
		{
			while($r=mysql_fetch_assoc($product))
			{
				$r['category_name']=$this->db->rp_getValue("category","name","id='".$r['cid']."'");
				$r['sub_category_name']=($temp=$this->db->rp_getValue("sub_category","name","id='".$r['sid']."'"))?$temp:"";
				$r['sub_sub_category_name']=($temp=$this->db->rp_getValue("sub_sub_category","name","id='".$r['sid']."'"))?$temp:"";
				$r['image_path']=ADMINSITEURL.PRODUCT_MAIN.$r['image_path'];
				$r['banner_image_path']=ADMINSITEURL.PRODUCT_BANNER.$r['banner_image_path'];
				$ssids[]=$r['ssid'];
				$seller_info=$this->seller->getSellerDetail($r['seller_id'],array("id","name","email","image_path"));
					
				$r['seller_info']=array();
				if($seller_info['ack']==1)
				{						
					$r['seller_info']=$seller_info['result'];					
				}
				if(array_key_exists("attr",$r))
				{
					$r['attr']=unserialize($r['attr']);
					$attr=array();
					$isValid=false;
					foreach($r['attr'] as $key => $value)
					{
						
						$a_value=$this->getAttribute(array($key));
						$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
					//	print_r($av_value);
						
						if($av_value['ack']==1)
						$attr[]=array("id"=>$key,"title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
					}
					
					$r['attr']=$attr;
					$a_image_r=$this->getProductAltImage(array($r['id']));
					$r['alt_image_path']=array();
					if($a_image_r['ack']==1)
					{
						
						$r['alt_image_path']=$a_image_r['result'];					
					}
					
									
					$r['descr']=html_entity_decode($r['descr']);
					$r['feature']=html_entity_decode($r['feature']);
				}
				if($detail['uid']!="" && $detail['uid']!=0)
				{					
					$wishlistCount=$this->db->rp_getTotalRecord("wishlist","uid='".$detail['uid']."' AND pid='".$r['id']."'");
					if($wishlistCount>=1)
					{
						$r['isWishListed']=1;
					}
					else
					{
						$r['isWishListed']=0;
					}
				}
				else
				{
					$r['isWishListed']=0;
				}
				$result[]=$r;
			}
			$filter=array();
			if($isFilterRequired)
			{
							
				if(empty($ssids))
				{
					$ssids_r= $this->db->rp_getData("sub_sub_category","id","sid='".$sid."'");
					if($ssids_r)
					{
						while($w=mysql_fetch_assoc($ssids_r))
						{
							$ssids[]=$w['id'];
						}
					}
					
				}
					$ssids=array_unique($ssids);
					$ssids=implode(",",$ssids);
				
					$sids=array();
					$bids=array();
					$prices=array();
					$sids_r=$this->db->rp_getData("sub_sub_category","sid","id IN(".$ssids.")","",0);
					if($sids_r)
					{
						while($t=mysql_fetch_assoc($sids_r))
						{
							
							$sids[]=$t['sid'];
						}
					}
					$sids=array_unique($sids);
					$sids_d=$this->getSubSubCategory(array(),$sids,array(),array("id","id as aid","id as value","name"));
					if($sids_d['ack']==1)
					$subcategory=$sids_d['result'];
					
					for($i=0;$i<sizeof($subcategory);$i++)
					{
						
						$subcategory[$i]['aid']=5895;
						
					}					
					$filter[]=array("aid"=>5895,"title"=>"category","type"=>"fix","avids"=>$subcategory);
				
					$sids=implode(",",$sids);
					$bids_r=$this->db->rp_getData("product","bid","sid IN(".$sids.")","",0);
					if($bids_r)
					{
						while($t=mysql_fetch_assoc($bids_r))
						{
							if($t['bid']!=0)
							$bids[]=$t['bid'];
						}
					}
					
					$bids_d=$this->getBrand($bids,array("id ","id as aid","id as value","name"));
					if($bids_d['ack']==1)
					{
						$bids=$bids_d['result'];
						for($i=0;$i<sizeof($bids);$i++)
						{
							
							$bids[$i]['aid']=5896;
							
						}
						$filter[]=array("aid"=>5896,"title"=>"brand","type"=>"fix","avids"=>$bids);					
					}
					
					$price_r=$this->db->rp_getData("product","MAX(max_price),MIN(max_price)","sid IN(".$sids.")","",0);
					if($price_r)
 					{
						
						$t=mysql_fetch_assoc($price_r);
						$max=intval($t['MAX(max_price)']);
						$min=intval($t['MIN(max_price)']);
						$n = 500;
						$range=array();
						 $length_of_range = round($max / $n);
						  if($length_of_range!=0)
						  {
							for($i=1;$i<=$length_of_range;$i++)
							  {
								$a['min']= $min; 
								$a['max']= $min+500; 
								$min=$min+500;
								  if($min<500)
								  {
									 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>"Below 500"); 
									
								  }
								  else
								  {
									 $prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max']);  
								  }							   						  
							   
							  }  
						  }
						  else
						  {
							$a['min']= 0; 
							$a['max']= 500; 														 
							$prices[]=array("id"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max'],"aid"=>5897,"value"=>$a['min']."-".$a['max'],"name"=>$a['min']."-".$a['max']);  
							 
						  }
						  					
						  
						$filter[]=array("aid"=>5897,"title"=>"price","type"=>"fix","avids"=>$prices); 
					}
					
					$attr_r=$this->db->rp_getData("sub_category","DISTINCT(attr)","id IN(".$sids.")","",0);
					if($attr_r)
					{
						while($r=mysql_fetch_assoc($attr_r))
						{						
							if(array_key_exists("attr",$r))
							{
								$r['attr']=unserialize($r['attr']);
								$attr=array();
								foreach($r['attr'] as $key => $value)
								{
									$a_value=$this->getAttribute(array($key));
									$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
									if($av_value['ack']==1)
									$filter[]=array("aid"=>$key,"type"=>"dynamic","title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
								}								
								
							}
						}
					}
					
					
				
				
			}
			
			$reply=array("ack"=>1,"result"=>$result,"filter"=>$filter,"developer_msg"=>"Product found in database.","ack_msg"=>"Great !! Product fetched.");
			return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Product not found in database.","ack_msg"=>"No Product Found!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Empty Filter","ack_msg"=>"Internal Error!!");
			return $reply;
		}
	}		
	function getAdvertise($advids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();			
		if(!empty($advids))
		{
			$advids=implode(",",$advids);
			$advertise=$this->db->rp_getData("advertise",$required_columns,"id IN (".$advids.") AND isDelete=0","",0,$limit);			
		}
		else
		{
			$advertise=$this->db->rp_getData("advertise",$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($advertise)
		{
			while($r=mysql_fetch_assoc($advertise))
			{
				$r['image_path']=ADMINSITEURL.ADVERTISE_MAIN.$r['image_path'];
				$r['banner_image_path']=ADMINSITEURL.ADVERTISE_BANNER.$r['banner_image_path'];
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Advertises found in database.","ack_msg"=>"Great !! Advertises  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no advertises found in database.","ack_msg"=>"Sorry !! No advertises found.");
			return $reply;
		}
	}
	function getOffer($required_columns=array(),$limit=array(),$uid="")
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();			
		
		$advertise=$this->db->rp_getData("advertise",$required_columns,"1=1 AND isDelete=0 AND isOffer=1","",0,$limit);
		
		if($advertise)
		{
			while($r=mysql_fetch_assoc($advertise))
			{
				$r['image_path']=ADMINSITEURL.ADVERTISE_MAIN.$r['image_path'];
				$r['banner_image_path']=ADMINSITEURL.ADVERTISE_BANNER.$r['banner_image_path'];
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Advertises found in database.","ack_msg"=>"Great !! Advertises  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no advertises found in database.","ack_msg"=>"Sorry !! No advertises found.");
			return $reply;
		}
	}
	function getNotificationDetail($notification_id,$required_columns=array(),$limit=array(),$uid="")
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();			
		$cids=array();$sids=array();$ssids=array();$pids=array();$bids=array();$protag=array();
		$sort="";$query="";
		$notification_r=$this->db->rp_getData("notification",$required_columns,"id='".$notification_id."' AND isDelete=0","",0,$limit);
		
		if($notification_r)
		{
			$notification=mysql_fetch_assoc($notification_r);
			if($notification['cid']!=0)
			{
				$cids=array($notification['cid']);
			}
			if($notification['sid']!=0)
			{
				$sids=array($notification['sid']);
			}
			if($notification['ssid']!=0)
			{
				$ssids=array($notification['ssid']);
			}
			if($notification['bid']!=0)
			{
				$bids=array($notification['bid']);
			}
			
			$ack=$this->getProduct($pids,$protag,$bids,$ssids,$sids,$cids,$uid,array("id","name","sid","ssid","cid","sell_price","max_price","discount_price","image_path","banner_image_path","rate","seller_id"),array(),true,array(),$sort,$query); // product_id,protag,,brand_id,sub_sub_category_id,sub_category_id,category_id
			return $ack;
			
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Advertises found in database.","ack_msg"=>"Great !! Notification fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no notification detail found in database.","ack_msg"=>"Sorry !! No Notification found or expired.");
			return $reply;
		}
	}
	function getBrand($bids=array(),$required_columns=array(),$limit=array())
	{
		$brand=false;
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();			
		if(!empty($bids))
		{
			
			$bids=implode(",",$bids);
			$brand=$this->db->rp_getData("brand",$required_columns,"id IN (".$bids.") AND isDelete=0","name ASC",0,$limit);			
		}
		else
		{
			$brand=$this->db->rp_getData("brand",$required_columns,"isDelete=0","name ASC",0,$limit);
		}
		
		if($brand)
		{
			while($r=mysql_fetch_assoc($brand))
			{
				if(array_key_exists("image_path",$r))
				$r['image_path']=ADMINSITEURL.BRAND_MAIN.$r['image_path'];	
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Brand found in database.","ack_msg"=>"Great !! Brand  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no brand found in database.","ack_msg"=>"Sorry !! No brand found.");
			return $reply;
		}
	}
	function getAttribute($aids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);
		$limit=$this->getLimit();	
		if(!empty($aids))
		{
			
			$aids=implode(",",$aids);
			$attribute=$this->db->rp_getData("attribute",$required_columns,"id IN (".$aids.") AND isDelete=0");			
		}
		else
		{
			$attribute=$this->db->rp_getData("attribute",$required_columns,"1=1 AND isDelete=0");
		}
		
		if($attribute)
		{
			while($r=mysql_fetch_assoc($attribute))
			{
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Attribute found in database.","ack_msg"=>"Great !! Attribute fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no attribute found in database.","ack_msg"=>"Sorry !! No Attribute found.");
			return $reply;
		}
	}
	function getProductAltImage($pids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);
		$limit=$this->getLimit();	
		if(!empty($pids))
		{
			$pids=implode(",",$pids);
			$altimage=$this->db->rp_getData("alt_image",$required_columns,"pid IN (".$pids.") AND isDelete=0");			
		}
		else
		{
			$altimage=$this->db->rp_getData("alt_image",$required_columns,"1=1 AND isDelete=0");
		}
		
		if($altimage)
		{
			while($r=mysql_fetch_assoc($altimage))
			{
				$r=ADMINSITEURL.PRODUCT_ALT.$r['image_path'];
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Product Alter Images found in database.","ack_msg"=>"Great !! Alter Images fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no  alter images found in database.","ack_msg"=>"Sorry !! No Alter Images found.");
			return $reply;
		}
	}
	function updateProductRating($pid)
	{
		if($pid!="" && $pid!=0)
		{
			$avgRating=$this->rp_getValue("product_review","AVG(`rate`)","pid='".$pid."' AND isDelete=0");
			$totalRating=$this->rp_getTotalRecord("product_review","pid='".$pid."' AND isDelete=0");
			$values=array("rate"=>$avgRating,"total_rating"=>$totalRating);
			$isUpdated=$this->db->rp_update("product",$values,"id='".$pid."'",0);
			if($isUpdated==1)
			{
				$reply=array("ack"=>1,"ack_msg"=>"Product Rating Updated","result"=>array("avg_rating"=>$avgRating,"total_rating"=>$totalRating));
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database Error!!","ack_msg"=>"Internal Error!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no pids founds","ack_msg"=>"Internal Error!!");
			return $reply;
		}
	}
	function getAttributeValue($av_ids=array(),$aids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);
		$limit=$this->getLimit();	
		$where="";
		if(!empty($aids))
		{
			$aids=implode(",",$aids);
			$where=$this->db->generateWhere($where,"aid IN (".$aids.")");
						
		}

		if(!empty($av_ids))
		{
			$av_ids=implode(",",$av_ids);
			$where=$this->db->generateWhere($where,"id IN (".$av_ids.")");			
		}
		
		
		if($where!="")
		{
			
			$where.=" AND isDelete=0";
			$attribute_val=$this->db->rp_getData("attribute_val",$required_columns,$where,"",0);
		}
		else
		{			
			$attribute_val=$this->db->rp_getData("attribute_val",$required_columns,"1=1 AND isDelete=0");
		}
		
		if($attribute_val)
		{
			while($r=mysql_fetch_assoc($attribute_val))
			{
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Attribute Values found in database.","ack_msg"=>"Great !! Attribute value fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no attribute value found in database.","ack_msg"=>"Sorry !! No Attribute value found.");
			return $reply;
		}
	}
	function getCountry($country_ids=array(),$required_columns=array(),$limit=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);
		$limit=$this->getLimit();		
		if(!empty($country_ids))
		{
			$country_ids=implode(",",$country_ids);
			$country=$this->db->rp_getData("country",$required_columns,"id IN (".$country_ids.")","",0,$limit);			
		}
		else
		{
			$country=$this->db->rp_getData("country",$required_columns,"1=1","",0,$limit);
		}
		
		if($country)
		{
			while($r=mysql_fetch_assoc($country))
			{
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"country found in database.","ack_msg"=>"Great !! Country  fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no country found in database.","ack_msg"=>"Sorry !! No country found.");
			return $reply;
		}
	}	
	function getAndroidHome()
	{
		$row=$this->db->rp_getData("android_home_detail","*","1=1 AND isDelete=0 AND isActive=1","id ASC LIMIT 1",0);
		if($row)
		{
			$home=mysql_fetch_assoc($row);
			$row_items=$this->db->rp_getData("android_home_item","*","android_home_id='".$home['id']."' AND isDelete=0","id ASC ",0);
			if($row_items)
			{
				$result=array();
				while($d=mysql_fetch_assoc($row_items))
				{					
					$type=$d['type'];
					$values=$d['value'];
					if($type==0)
					{
						/* Fetch Products
						$product_ids=str_replace("offer_","",$values);
						if($product_ids!="")
						$product_ids=explode(",",$product_ids);	*/
						$column_name=$this->db->rp_getValue("special_tag","title","id='".$values."'");
						if($column_name && $column_name!="")
						{
							$where="".$column_name."='1'";
							$products=$this->db->rp_getData("product","*",$where,"id ASC limit 0,6");
							if($products)
							{
								$reply_products=array();
								while($r=mysql_fetch_assoc($products))
								{
									$r['category_name']=$this->db->rp_getValue("category","name","id='".$r['cid']."'");
									$r['sub_category_name']=($temp=$this->db->rp_getValue("sub_category","name","id='".$r['sid']."'"))?$temp:"";
									$r['sub_sub_category_name']=($temp=$this->db->rp_getValue("sub_sub_category","name","id='".$r['sid']."'"))?$temp:"";
									$r['image_path']=ADMINSITEURL.PRODUCT_MAIN.$r['image_path'];
									$r['banner_image_path']=ADMINSITEURL.PRODUCT_BANNER.$r['banner_image_path'];
									
									if(array_key_exists("attr",$r))
									{
										$r['attr']=unserialize($r['attr']);
										$attr=array();
										foreach($r['attr'] as $key => $value)
										{
											$a_value=$this->getAttribute(array($key));
											$av_value=$this->getAttributeValue($value,array(),array("id","aid","name","value"));
											if($av_value['ack']==1)
											$attr[]=array("id"=>$key,"title"=>$a_value['result'][0]['name'],"avids"=>$av_value['result']);
										}
										$r['attr']=$attr;
										$a_image_r=$this->getProductAltImage(array($r['id']));
										$r['alt_image_path']=array();
										if($a_image_r['ack']==1)
										{
											
											$r['alt_image_path']=$a_image_r['result'];					
										}
										$r['descr']=html_entity_decode($r['descr']);
										$r['feature']=html_entity_decode($r['feature']);
									}
									$reply_products[]= $r;
								}
								$result[]=array("title"=>$d['title'],"items"=>$reply_products,"item_type"=>$this->home_item_type[intval($type)],"item_type_slug"=>$type);
							}
							
						}
												
						
					}
					else
					{
					
						// fetch advertises
						$advertise_ids=array($values);						
						$ads=$this->getAdvertise($advertise_ids);
						if($ads['ack']==1)
						$result[]=array("title"=>$d['title'],"items"=>$ads['result'],"item_type"=>$this->home_item_type[intval($type)],"item_type_slug"=>$type);
					}
					
				}
				$banners=array();
				$row_items=$this->db->rp_getData("promotion","*","","id ASC ",0);
				if($row_items)
				{
					
					while($d=mysql_fetch_assoc($row_items))
					{
						$d['image_path']=ADMINSITEURL.SLIDESHOW_BANNER.$d['image_path'];
						$banners[]=$d;
					}
					
				}
				$reply=array("ack"=>1,"result"=>array("banners"=>$banners,"bottom"=>$result),"developer_msg"=>"Home found in database.","ack_msg"=>"Great !! Home fetched.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"no home items found in database.","ack_msg"=>"Sorry !! No content found.");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no home items found in database.","ack_msg"=>"Sorry !! No content found.");
			return $reply;
		}
		
		
		
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
}
?>