<?php
class SalesInquiry extends Functions
{
	public $inquiry_status=array("In Detailed","For Suspecting","For Email","For Followup","For Converted To Order","For Cancelled","For Future");
	function __construct() {
	   $db = new Functions();
	   $conn = $db->connect();
	   $this->db=$db;
       
	}
    function getInquiry($id)
    {
		$r = array();
		$data    = $this->db->rp_getData('inquiry',"*","id= '".$id."' AND isActive=1");
		while($row = mysql_fetch_assoc($data))
		{
				$row['inquiry_date'] = date('d-m-Y',strtotime($row['inquiry_date'] ));
				
				$row['s_brand_name_slug'] = $row['s_brand_name'];
				$row['s_brand_name'] = $this->db->rp_getValue("category","name","id='".$row['s_brand_name']."'");
				
				$row['s_dd_type_firm_slug']= $row['s_dd_type_firm'];
				$row['s_dd_type_firm'] = $this->db->rp_getValue("inquiry_firm","name","id='".$row['s_dd_type_firm']."'");
				
				$row['mode_of_inquiry_slug']= $row['mode_of_inquiry'];
				$row['mode_of_inquiry']= $this->db->rp_getValue("mode_inquiry","title","id='".$row['mode_of_inquiry']."'");
				
				
				$r[] = $row;
		}
		if($r)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully get Inquiry !!",
					"developer_msg"=>"You got it!!",
					"result"=>$r,
					);
					
					return $ack;
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"No data found !!",
					"developer_msg"=>"No found!!",
					"result"=>$r,
					);
					return $ack;
		}	
		
	}
	function addInquiry($id,$end_user_type,$distributor_id,$person_name,$address,$company_name,$city,$state,$country,$email,$contact_number,$office_number,$current_requirement,$type_of_inquiry,$mode_of_inquiry,$inquiry_date)
    {	
			if($type_of_inquiry == "distributor")
			{
				$dup_where = "name='".$person_name."' AND phone='".$contact_number."'";
				$r = $this->db->rp_dupCheck("distributor",$dup_where);
				if(!$r)
				{
					$rows	 = array(	
							
						
						"name",
						"email",
						"phone",
						"address",						
						"country",						
						"state",
						"city",
							);
					$values	 = array(
						$person_name,
						$email,
						$contact_number,
						$address,
						$country,
						$state,
						$city,
						);
							
				$distributor_id=$this->db->rp_insert("distributor",$values,$rows,0);
				}
				
			}
			$rows	 = array(	
							
						"sales_id",
						"end_user_type",
						"distributor_id",
						"person_name",
						"address",						
						"company_name",
						"city",
						"state",
						"country",
						"email",
						"contact_number",
						"office_number",
						"current_requirement",
						"type_of_inquiry",
						"mode_of_inquiry",
						"inquiry_date" ,
						"created_date",
						"isActive"
						);
			$values	 = array(
						
						$id,
						$end_user_type,
						$distributor_id,
						$person_name,
						$address,
						$company_name,
						$city,
						$state,
						$country,
						$email,
						$contact_number,
						$office_number,
						$current_requirement,
						$type_of_inquiry,
						$mode_of_inquiry,
						date('Y-m-d H:i:s',strtotime($inquiry_date)),
						date('Y-m-d H:i:s'),
						1
						);
				
					$add = $this->db->rp_insert("inquiry",$values,$rows,0);
					
					if($add)
					{
							$result = $this->getInquiry($add);
							$ack=array( "ack"=>1,
						"ack_msg"=>"Thanks !! Successfully Send Your Inquiry !!",
						"developer_msg"=>"You got it!!",
						"result"=>$result['result'],
						);
						return $ack;
					}
					else
					{
								$ack=array("ack"=>0,
							"ack_msg"=>"not inserted !!",
							"developer_msg"=>"not inserted!!",
							"result"=>array(),
							);
							return $ack;
					}
					
		
	}
	function updateInquiry($id,$end_user_type,$distributor_id,$person_name,$address,$company_name,$city,$state,$country,$email,$contact_number,$office_number,$current_requirement,$type_of_inquiry,$mode_of_inquiry,$inquiry_date)
    {	
			if($this->db->rp_getTotalRecord("inquiry","id = '".$id."'")>0)
			{
				if($type_of_inquiry == "distributor")
				{
					$dup_where = "name='".$person_name."' AND phone='".$contact_number."'";
					$r = $this->db->rp_dupCheck("distributor",$dup_where);
					if(!$r)
					{
						$rows	 = array(	
								
							
							"name",
							"email",
							"phone",
							"address",						
							"country",						
							"state",
							"city",
								);
						$values	 = array(
							$person_name,
							$email,
							$contact_number,
							$address,
							$country,
							$state,
							$city,
							);
								
					$distributor_id=$this->db->rp_insert("distributor",$values,$rows,0);
					}
				
				}
				$rows	 = array(	
						"end_user_type"		=>$end_user_type,
						"distributor_id"	=>$distributor_id,	
						"person_name" 		=>$person_name,
						"address" 	  		=>$address,						
						"company_name"		=>$company_name,
						"city"		  		=>$city,
						"state"   			=>$state,
						"country" 			=>$country,
						"email"				=>$email,
						"contact_number"	=>$contact_number,
						"office_number"		=>$office_number,
						"current_requirement" =>$current_requirement,
						"type_of_inquiry" 	=>$type_of_inquiry,
						"mode_of_inquiry" 	=>$mode_of_inquiry,
						"inquiry_date" 		=>date('Y-m-d H:i:s',strtotime($inquiry_date)),
						"created_date" 		=>date('Y-m-d H:i:s'),
						"isActive"     		=>1,
						);
			
						$where = "id='".$id."'";
						$uid = $this->db->rp_update("inquiry",$rows,$where);
						
						if($uid)
						{
							$result = $this->getInquiry($id);
								$ack=array( "ack"=>1,
							"ack_msg"=>"Thanks !! Successfully Update Inquiry !!",
							"developer_msg"=>"You got it!!",
							"result"=>$result['result'],
							);
							return $ack;
						}
						else
						{
									$ack=array("ack"=>0,
								"ack_msg"=>"not Updated !!",
								"developer_msg"=>"not Updated!!",
								"result"=>array(),
								);
								return $ack;
						}
				}
				else
				{
								$ack=array("ack"=>0,
								"ack_msg"=>"not updated !!",
								"developer_msg"=>"not inserted!!",
								"result"=>array(),
								);
								return $ack;
				}
			
			
	}
	function updateDealerDistributorInquiry($id,$distributor_id, $type_of_inquiry,$s_brand_name, $s_designation, $s_ex_distribution_product,
	$s_decision_making, $s_product_req_detail, $s_dd_company_size, $s_dd_num_employee,$s_dd_num_marketing_person, $s_dd_exciting_product_detail,$s_dd_num_end_user, $s_dd_area_coverage, $s_dd_type_firm,$inquiry_status)
	{
		if($this->db->rp_getTotalRecord("inquiry","id = '".$id."'")>0)
		{
			
			$values	 = array(	
						
						"distributor_id" 				=>	$distributor_id,
						"type_of_inquiry" 				=>	$type_of_inquiry,
						"s_brand_name" 					=>	$s_brand_name,			
						"s_designation" 				=>	$s_designation,
						"s_ex_distribution_product"	    =>	$s_ex_distribution_product,
						"s_decision_making"				=>	$s_decision_making,
						"s_product_req_detail"			=>	$s_product_req_detail,
						"s_dd_company_size"				=>	$s_dd_company_size,
						"s_dd_num_employee"				=>	$s_dd_num_employee,
						"s_dd_num_marketing_person"		=>  $s_dd_num_marketing_person,
						"s_dd_exciting_product_detail"	=>	$s_dd_exciting_product_detail,
						"s_dd_num_end_user"				=>	$s_dd_num_end_user,
						"s_dd_area_coverage"			=>	$s_dd_area_coverage,
						"s_dd_type_firm"				=>	$s_dd_type_firm,
						"inquiry_status"				=>  1,
						);
				
					$where = "id='".$id."'";
					$uid = $this->db->rp_update("inquiry",$values,$where);
					$result = $this->getInquiry($id);
					$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Send Suspecting Inquiry !!",
					"developer_msg"=>"You got it!!",
					"result"=>$result['result'],
					);
					
					return $ack;
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"not updated!!",
					"developer_msg"=>"not  it!!",
					"result"=>array(),
					);
					
					return $ack;
		}
		
		
	}
	function updateEndUserInquiry($id, $type_of_inquiry,$s_brand_name, $s_designation, $s_ex_distribution_product,
					$s_decision_making, $s_product_req_detail,$s_eu_num_pond,$s_eu_pond_size,$s_eu_stock_density,$s_eu_used_quantity,$s_eu_market_reputation)
	{
		if($this->db->rp_getTotalRecord("inquiry","id = '".$id."'") > 0)
		{
			
			$values	 = array(	
		
						"type_of_inquiry" 				=>	$type_of_inquiry,
						"s_brand_name" 					=>	$s_brand_name,		
						"s_designation" 				=>	$s_designation,
						"s_ex_distribution_product"	    =>	$s_ex_distribution_product,
						"s_decision_making"				=>	$s_decision_making,
						"s_product_req_detail"			=>	$s_product_req_detail,
						"s_eu_num_pond"					=>	$s_eu_num_pond,
						"s_eu_pond_size"				=>	$s_eu_pond_size,
						"s_eu_stock_density"			=>	$s_eu_stock_density,
						"s_eu_used_quantity"			=>	$s_eu_used_quantity,
						"s_eu_market_reputation"		=>	$s_eu_market_reputation,
						
						);
				
					$where = "id='".$id."'";
					$uid = $this->db->rp_update("inquiry",$values,$where);
					$result = $this->getInquiry($id);
					$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Fullfill your Suspecting Inquiry !!",
					"developer_msg"=>"You got it!!",
					"result"=>$result['result'],
					);
					
					return $ack;
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"not Send please try again!!",
					"developer_msg"=>"not  it!!",
					"result"=>array(),
					);
					
					return $ack;
		}
		
		
	}	
	function getDistributor()
    {
		$r = array();
		$data    = $this->db->rp_getData('distributor',"*","isActive=1");
		if($data)
		{
			while($row = mysql_fetch_assoc($data))
			{
					$r[] = $row;
			}
			if($r)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Distributor !!",
						"developer_msg"=>"You got it!!",
						"result"=>$r,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Distributor Not found !!",
						"developer_msg"=>"No found!!",
						"result"=>$r,
						);
						return $ack;
			}	
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Distributor !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($data),
						);
						return $ack;
		}
	}
	function getModeInquiry()
    {
		$r = array();
		$data    = $this->db->rp_getData('mode_inquiry',"*");
		if($data)
		{
			while($row = mysql_fetch_assoc($data))
			{
					
					$r[] = $row;
			}
			if($r)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get mode of Inquiry !!",
						"developer_msg"=>"You got it!!",
						"result"=>$r,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"No data found !!",
						"developer_msg"=>"No found!!",
						"result"=>$r,
						);
						return $ack;
			}	
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Mode of Inquiry !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($data),
						);
						return $ack;
		}
	}
	
	function getCountry()
    {
		$c = array();
		$data    = $this->db->rp_getData('country',"*");
		if($data)
		{
			while($row = mysql_fetch_assoc($data))
			{
					$c[] = $row;
			}
			if($c > 0 )
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Country !!",
						"developer_msg"=>"You got it!!",
						"result"=>$c,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found !!",
						"developer_msg"=>"Not found!!",
						"result"=>$c,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Country !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($data),
						);
						return $ack;
		}
		
	}
	function getEnduser()
    {
		$u = array();
		$user    = $this->db->rp_getData('end_user',"*");
		if($user)
		{
			while($row = mysql_fetch_assoc($user))
			{
					$u[] = $row;
			}
			if($u)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get End user type !!",
						"developer_msg"=>"You got it!!",
						"result"=>$u,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found !!",
						"developer_msg"=>"Not found!!",
						"result"=>$u,
						);
						return $ack;
			}	
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any End User !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($user),
						);
						return $ack;
		}
	}
	function getBrand()
    {
		$u = array();
		$cat    = $this->db->rp_getData('category',"*");
		if($cat)
		{
			while($row = mysql_fetch_assoc($cat))
			{
					$row['adate'] = date('d-m-Y',strtotime($row['adate'] ));
					$u[] = $row;
			}
			if($u)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Brand !!",
						"developer_msg"=>"You got it!!",
						"result"=>$u,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Brand !!",
						"developer_msg"=>"Not found!!",
						"result"=>$u,
						);
						return $ack;
			}	
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Brand !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($cat),
						);
						return $ack;
		}
	}
	function getFirm()
    {
		$u = array();
		$cat    = $this->db->rp_getData('inquiry_firm',"*");
		if($cat)
		{
			while($row = mysql_fetch_assoc($cat))
			{
					$u[] = $row;
			}
			if($u > 0)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Type of Firm  !!",
						"developer_msg"=>"You got it!!",
						"result"=>$u,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any type of Firm !!",
						"developer_msg"=>"Not found!!",
						"result"=>$u,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Firm !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($cat),
						);
						return $ack;
		}
		
	}
	function getDealer()
    {
		$u = array();
		$dealer = "dealer";
		$where = "type_of_inquiry ='dealer' GROUP BY contact_number";
		$cat    = $this->db->rp_getData('inquiry',"DISTINCT(contact_number),inquiry.*",$where);
		if($cat)
		{
			while($row = mysql_fetch_assoc($cat))
			{
					$u[] = $row;
			}
			if($u > 0)
			{
					
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Dealer  !!",
						"developer_msg"=>"You got it!!",
						"result"=>$u,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Dealer !!",
						"developer_msg"=>"Not found!!",
						"result"=>$u,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Dealer !!",
						"developer_msg"=>"Not found!!",
						"result"=>array($cat),
						);
						return $ack;
		}
			
		
	}
	function getEndUserByInquiry()
    {
		$eu = array();
		$where = "type_of_inquiry ='end_user'";
		$end_user    = $this->db->rp_getData('inquiry',"*",$where,"",0);
		if($end_user)
		{
			while($row = mysql_fetch_assoc($end_user))
			{
					$eu[] = $row;
			}
			if($eu > 0)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get End User By Inquiry  !!",
						"developer_msg"=>"You got it!!",
						"result"=>$eu,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any End User By Inquiry !!",
						"developer_msg"=>"Not found!!",
						"result"=>$eu,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any End User!!",
						"developer_msg"=>"Not found!!",
						"result"=>array($end_user),
						);
						return $ack;
		}
			
		
	}
	function getSalesInquiryById($sales_id)
    {
		$r = array();
		$data    = $this->db->rp_getData('inquiry',"*","sales_id= '".$sales_id."' AND isActive=1");
		if($data)
		{
			while($row = mysql_fetch_assoc($data))
			{
					$row['inquiry_date'] = date('d-m-Y',strtotime($row['inquiry_date'] ));
					
					$row['s_brand_name_slug'] = $row['s_brand_name'];
					$row['s_brand_name'] = $this->db->rp_getValue("category","name","id='".$row['s_brand_name']."'");
					
					$row['s_dd_type_firm_slug']= $row['s_dd_type_firm'];
					$row['s_dd_type_firm'] = $this->db->rp_getValue("inquiry_firm","name","id='".$row['s_dd_type_firm']."'");
					
					$row['mode_of_inquiry_slug']= $row['mode_of_inquiry'];
					$row['mode_of_inquiry']= $this->db->rp_getValue("mode_inquiry","title","id='".$row['mode_of_inquiry']."'");
					
					
					$r[] = $row;
			}
			if($r)
			{
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Inquiry By Sales Executive !!",
						"developer_msg"=>"You got it!!",
						"result"=>$r,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"No data found Inquiry By Sales Executive  !!",
						"developer_msg"=>"No found!!",
						"result"=>$r,
						);
						return $ack;
			}	
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Sales Executive Inquiry!!",
						"developer_msg"=>"Not found!!",
						"result"=>array($end_user),
						);
						return $ack;
		}
	}
	function sendEmailByInquiry($inquiry_id,$subject,$body)
    {
		
			$rows	 = array(	
					
					"inquiry_id",
					"subject",
					"body",
					);
			$values	 = array(
					
					$inquiry_id,
					$subject,
					$body,
					);
					
			$inq=$this->db->rp_insert("inquiry_email",$values,$rows,0);
			if($inq)
			{
				$where = "id= '".$inq."'"; 
				$em =mysql_fetch_assoc($this->db->rp_getData("inquiry_email","*",$where,0));
				$ack=array( "ack"=>1,
							"ack_msg"=>"Mail Data Inserted Successfull!!",
							"developer_msg"=>"You Got It !!",
							"result"=>array($em),
							);
							return $ack;
			}
			else
			{
				$ack=array( "ack"=>0,
							"ack_msg"=>"Not updated!!",
							"developer_msg"=>"Not found!!",
							"result"=>array($inq),
							);
							return $ack;
			}
		
	}
	function updateDocumnets($id,$inquiry_id,$attachment)
    {
		
		$attachment = explode(",",$attachment);
		$finalattachment = "";
		foreach($attachment as $a)
		{
		
			$where = "id = '".$a."'";
			$url = $this->db->rp_getValue("documents","attachment",$where);
			if($finalattachment!="")			
			$finalattachment = $finalattachment."&5895".$url;
			else
			$finalattachment = $url;	
		}
		
		$data    = $this->db->rp_getData('inquiry_email',"*","inquiry_id= '".$inquiry_id."'");
		if($data)
		{
			$rows	 = array(	
					
					"attachment"      => $finalattachment
					);
			
			$where = "id = '".$id."'";
			$inq=$this->db->rp_update("inquiry_email",$rows,$where,0);
			if($inq)
			{
				$ack=array( "ack"=>1,
							"ack_msg"=>"Updated Successfull!!",
							"developer_msg"=>"You Got It !!",
							"result"=>array($inq),
							);
							return $ack;
			}
			else
			{
				$ack=array( "ack"=>0,
							"ack_msg"=>"Not updated!!",
							"developer_msg"=>"Not found!!",
							"result"=>array($data),
							);
							return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"This document is not updated!!",
						"developer_msg"=>"Duplicate Inquiry id!!",
						"result"=>array(),
						);
						return $ack;
		}
	}
	function getDocuments()
    {
	
		$d = array();
		$doc    = $this->db->rp_getData('documents',"*","","",0);
		if($doc)
		{
			
			while($row = mysql_fetch_assoc($doc))
			{
					$d[] = $row;
			}
			if(!empty($d))
			{
				
					$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully get Documnets  !!",
						"developer_msg"=>"You got it!!",
						"result"=>$d,
						);
						
						return $ack;
			}
			else
			{
					$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Documents !!",
						"developer_msg"=>"Not found!!",
						"result"=>$d,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
						"ack_msg"=>"Not found any Documents !!",
						"developer_msg"=>"Not found!!",
						"result"=>$d,
						);
						return $ack;
		}
		
	}
	function getAllInquiry($sid)
    {
		$r = array();
		$data    = $this->db->rp_getData('inquiry',"*","sales_id='".$sid."' AND isActive=1","",0);
		while($row = mysql_fetch_assoc($data))
		{
				$row['inquiry_date'] = date('d-m-Y',strtotime($row['inquiry_date'] ));
				
				$row['s_brand_name_slug'] = $row['s_brand_name'];
				$row['s_brand_name'] = $this->db->rp_getValue("category","name","id='".$row['s_brand_name']."'");
				
				$row['s_dd_type_firm_slug']= $row['s_dd_type_firm'];
				$row['s_dd_type_firm'] = $this->db->rp_getValue("inquiry_firm","name","id='".$row['s_dd_type_firm']."'");
				
				$row['mode_of_inquiry_slug']= $row['mode_of_inquiry'];
				$row['mode_of_inquiry']= $this->db->rp_getValue("mode_inquiry","title","id='".$row['mode_of_inquiry']."'");
				
				$row['inquiry_status_slug']= $row['inquiry_status'];
				$row['inquiry_status']= $this->inquiry_status[intval($row['inquiry_status'])];
				
				$r[] = $row;
		}
		if($r)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully get Inquiry !!",
					"developer_msg"=>"You got it!!",
					"result"=>$r,
					);
					
					return $ack;
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"No data found !!",
					"developer_msg"=>"No found!!",
					"result"=>$r,
					);
					return $ack;
		}	
		
	}
	
	
	
}
?>