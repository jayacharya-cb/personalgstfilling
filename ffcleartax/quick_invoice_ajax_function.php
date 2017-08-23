<?php 
include("connect.php");
$ctable="quick_invoice_item_master";
$ctableContact="quick_invoice_contact";
$ctableItem="quick_invoice_item_master";
$ctableStockAdjustment="quick_stock_adjustment";
if(isset($_POST['m']) && $_POST['m']!="")
{
	$m=$_POST['m'];
	if($m=="fi")
	{
		if(isset($_POST['description']) && $_POST['description']!="")
		{
			$description=$_POST['description'];
			$limit=isset($_POST['limit'])?$_POST['limit']:"25";
			$Items=$db->rp_getData($ctable,"item_code,gst_code,gst_type,description,unit_price,unit_of_measurement,aid,unit_cost,discount,notes,cgst_rate,sgst_rate,igst_rate,cess_rate,stock_qty","isDelete=0","id DESC",0);
			if($Items)
			{
				$Result=array();
				while($Item=mysql_fetch_assoc($Items))
				{
					$Item['label']=$Item['description'];
					$Item['value']=$Item['description'];
					$Result[]=$Item;
				}
				
				$reply=array("a"=>1,"mg"=>"Service Availabel","dmg"=>"Result Found!! SUC -1","result"=>$Result);
				echo json_encode($reply);
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);		
			}
			
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="fii")
	{
		if(isset($_POST['cid']) && $_POST['cid']!="")
		{
			$cid=$_POST['cid'];
			$limit=isset($_POST['limit'])?$_POST['limit']:"25";
			$Items=$db->rp_getData($ctable,"*","aid='".$cid."' AND isDelete=0","id DESC",0);
			if($Items)
			{
				$Item=mysql_fetch_assoc($Items);
				
				$reply=array("a"=>1,"mg"=>"Service Availabel","dmg"=>"Result Found!! SUC -1","result"=>$Item);
				echo json_encode($reply);
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);		
			}
			
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="si")
	{
		if(isset($_POST['description']) && $_POST['description']!="" && isset($_POST['gst_type']) && $_POST['gst_type']!="" && isset($_POST['opening_qty']) && $_POST['opening_qty']!="" )
		{
			// Required 
			$description=$_POST['description'];
			$gst_type=$_POST['gst_type'];
			$item_code=isset($_POST['item_code'])?$_POST['item_code']:"";
			$gst_code=isset($_POST['gst_code'])?$_POST['gst_code']:"";
			$gst_type=isset($_POST['gst_type'])?$_POST['gst_type']:"";
			$description=isset($_POST['description'])?$_POST['description']:"";
			$unit_price=isset($_POST['unit_price'])?$_POST['unit_price']:"";
			$opening_qty=isset($_POST['opening_qty'])?$_POST['opening_qty']:"";
			$stock_qty=isset($_POST['opening_qty'])?$_POST['opening_qty']:"";
			$unit_of_measurement=isset($_POST['unit_of_measurement'])?$_POST['unit_of_measurement']:"";
			$aid=isset($_POST['aid'])?$_POST['aid']:"";
			$unit_cost=isset($_POST['unit_cost'])?$_POST['unit_cost']:"";
			$discount=isset($_POST['discount'])?$_POST['discount']:"";
			$notes=isset($_POST['notes'])?$_POST['notes']:"";
			$cgst_rate=isset($_POST['cgst_rate'])?$_POST['cgst_rate']:"";
			$sgst_rate=isset($_POST['sgst_rate'])?$_POST['sgst_rate']:"";
			$igst_rate=isset($_POST['igst_rate'])?$_POST['igst_rate']:"";
			$cess_rate=isset($_POST['cess_rate'])?$_POST['cess_rate']:"";
			$mode=(isset($_POST['mode']))?$_POST['mode']:"add";
			$cid=(isset($_POST['cid']))?$_POST['cid']:"";
			
			if($mode=="edit" && $cid!="")
			{
				
				$values=array("item_code"=>$item_code,
								"gst_code"=>$gst_code,
								"gst_type"=>$gst_type,
								"description"=>$description,
								"unit_price"=>$unit_price,
								"unit_of_measurement"=>$unit_of_measurement,
								"unit_cost"=>$unit_cost,
								"discount"=>$discount,
								"notes"=>$notes,
								"cgst_rate"=>$cgst_rate,
								"sgst_rate"=>$sgst_rate,
								"igst_rate"=>$igst_rate,
								"cess_rate"=>$cess_rate);
				$isUpdated=$db->rp_update($ctableItem,$values,"aid='".$cid."'",0);
				if($isUpdated)
				{
					$reply=array("a"=>1,"mg"=>"Item Saved","dmg"=>"Result Found!! SUC -1");
					echo json_encode($reply);
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
					echo json_encode($reply);		
				}
				
			}
			else if($mode=="add")
			{
				
				// OPTIONAL
				$columns=array("item_code","gst_code","gst_type","description","unit_price","unit_of_measurement","aid","unit_cost","opening_qty","stock_qty","discount","notes","cgst_rate","sgst_rate","igst_rate","cess_rate");
				$values=array($item_code,$gst_code,$gst_type,$description,$unit_price,$unit_of_measurement,$aid,$unit_cost,$opening_qty,$stock_qty,$discount,$notes,$cgst_rate,$sgst_rate,$igst_rate,$cess_rate);
				$ItemInsertedID=$db->rp_insert($ctableItem,$values,$columns,0);
				if($ItemInsertedID)
				{
					$reply=array("a"=>1,"mg"=>"Item Saved","dmg"=>"Result Found!! SUC -1");
					echo json_encode($reply);
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
					echo json_encode($reply);		
				}
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);	
			}
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Item Description, Item Type And Opening Qty Required","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="fc")
	{
		if(isset($_POST['nick_name']) && $_POST['nick_name']!="")
		{
			$nick_name=$_POST['nick_name'];
			$limit=isset($_POST['limit'])?$_POST['limit']:"25";
			$Contacts=$db->rp_getData($ctableContact,"*","isDelete=0","id DESC","nick_name LIKE '%".$nick_name."%'","",0);
			if($Contacts)
			{
				$Result=array();
				while($Contact=mysql_fetch_assoc($Contacts))
				{
					$state_display=$db->rp_getValue("state","name","slug='".$Contact['state']."'");
					$Contact['label']=$Contact['nick_name'];
					$Contact['value']=$Contact['nick_name'];
					$Contact['state_display']=$state_display;
					$Result[]=$Contact;
				}
				
				$reply=array("a"=>1,"mg"=>"Service Availabel","dmg"=>"Result Found!! SUC -1","result"=>$Result);
				echo json_encode($reply);
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);		
			}
			
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="fci")
	{
		if(isset($_POST['cid']) && $_POST['cid']!="")
		{
			$id=$_POST['cid'];
			$limit=isset($_POST['limit'])?$_POST['limit']:"25";
			$Contacts=$db->rp_getData($ctableContact,"*","id='".$id."' AND isDelete=0","id DESC","",0);
			if($Contacts)
			{
				$Contact=mysql_fetch_assoc($Contacts);
				$reply=array("a"=>1,"mg"=>"Service Availabel","dmg"=>"Result Found!! SUC -1","result"=>$Contact);
				echo json_encode($reply);
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);		
			}
			
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="sc")
	{
		if(isset($_POST['nick_name']) && $_POST['nick_name']!="" && isset($_POST['state']) && $_POST['state']!="")
		{
			// REQUIRED
			$nick_name=$_POST['nick_name'];
			$state=$_POST['state'];
			// OPTIONAL
			$gstin=(isset($_POST['gstin']))?$_POST['gstin']:"";
			$country=(isset($_POST['country']))?$_POST['country']:"";
			$city=(isset($_POST['city']))?$_POST['city']:"";
			$mobile_no=(isset($_POST['mobile_no']))?$_POST['mobile_no']:"";
			$pan_number=(isset($_POST['pan_number']))?$_POST['pan_number']:"";
			$address=(isset($_POST['address']))?$_POST['address']:"";
			$zipcode=(isset($_POST['zipcode']))?$_POST['zipcode']:"";
			$email=(isset($_POST['email']))?$_POST['email']:"";
			$land_line_number=(isset($_POST['land_line_number']))?$_POST['land_line_number']:"";
			$contact_person=(isset($_POST['contact_person']))?$_POST['contact_person']:"";
			$mode=(isset($_POST['mode']))?$_POST['mode']:"add";
			$cid=(isset($_POST['cid']))?$_POST['cid']:"";
			
			if($mode=="edit" && $cid!="")
			{
				$values=array("nick_name"=>$nick_name,"gstin"=>$gstin,"country"=>$country,"state"=>$state,"city"=>$city,"mobile_no"=>$mobile_no,"pan_number"=>$pan_number,"address"=>$address,"zipcode"=>$zipcode,"email"=>$email,"land_line_number"=>$land_line_number,"contact_person"=>$contact_person);	
				$isUpdated=$db->rp_update($ctableContact,$values,"id='".$cid."'");		
				if($isUpdated)
				{
					$reply=array("a"=>1,"mg"=>"Contact Saved!!","dmg"=>"Result Found!! SUC -1");
					echo json_encode($reply);
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-6");
					echo json_encode($reply);		
				}	
			}
			else if($mode=="add")
			{
				$columns=array("nick_name","gstin","country","state","city","mobile_no","pan_number","address","zipcode","email","land_line_number","contact_person");
				$values=array($nick_name,$gstin,$country,$state,$city,$mobile_no,$pan_number,$address,$zipcode,$email,$land_line_number,$contact_person);
				$ContactsInsertedID=$db->rp_insert($ctableContact,$values,$columns);
				if($ContactsInsertedID!=0)
				{
					$reply=array("a"=>1,"mg"=>"Contact Saved!!","dmg"=>"Result Found!! SUC -1");
					echo json_encode($reply);
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-5");
					echo json_encode($reply);		
				}
				
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);	
			}
				
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Nick name and State Required","dmg"=>"Missing parameters ER-3");
			echo json_encode($reply);
		}
	}
	else if($m=="dc")
	{
			$cid=(isset($_POST['cid']))?$_POST['cid']:"";
			if($cid!="")
			{
				$values=array("isDelete"=>1);	
				$isUpdated=$db->rp_update($ctableContact,$values,"id='".$cid."'");		
				if($isUpdated)
				{
					$reply=array("a"=>1,"mg"=>"Contact Deleted!!","dmg"=>"Result Found!! SUC -1");
					echo json_encode($reply);
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-6");
					echo json_encode($reply);		
				}	
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);	
			}
	}
	else if($m=="sa")
	{
			$iid=(isset($_POST['iid']))?$db->clean($_POST['iid']):"";
			$iq=(isset($_POST['iq']))?floatval($db->clean($_POST['iq'])):"";
			if($iid!="" && $iq!="")
			{
				$item_info=$db->rp_getData($ctableItem,"description,gst_code,stock_qty","aid='".$iid."' AND isDelete=0 AND isActive=1");
				if($item_info)
				{
					$item_info=mysql_fetch_assoc($item_info);
					$old_stock_qty=($item_info['stock_qty']!="")?floatval($item_info['stock_qty']):0;
					$new_stock_qty=$old_stock_qty+$iq;
					$values=array("stock_qty"=>$new_stock_qty);	
					$isUpdated=$db->rp_update($ctableItem,$values,"aid='".$iid."'");		
					if($isUpdated)
					{
						$today=date("Y-m-d H:i:s");
						$values=array($iid,$iq,$item_info['description'],$item_info['gst_code'],$today);
						$rows=array("item_id","adjustment_qty","description","gst_code","created_date");
						$db->rp_insert($ctableStockAdjustment,$values,$rows,0);
						$reply=array("a"=>1,"mg"=>"Adjustment Saved!!","dmg"=>"Result Found!! IA -1");
						echo json_encode($reply);
					}
					else
					{
						$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found IA-6");
						echo json_encode($reply);		
					}
				}
				else
				{
					$reply=array("a"=>0,"mg"=>"Item Not Available Or Deleted Try Again","dmg"=>"Result Not Found INA-7");
					echo json_encode($reply);	
				}
					
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Result Not Found ER-4");
				echo json_encode($reply);	
			}
	}	
	else
	{
		$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing Service ER-2");
		echo json_encode($reply);
	}
}
else
{
	$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-1");
	echo json_encode($reply);
}
?>