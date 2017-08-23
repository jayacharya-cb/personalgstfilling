<?php
class Chat extends Functions
{
	public $detail=array();
	public $ctableChatRoom="chat_room";
	public $ctableChatMessage="chat_message";
	public $db,$application,$admin,$customer;	
	function __construct($id="") 
	{
		require_once("class.application.php");
		require_once("class.customer.php");
		require_once("admin.class.php");
		$db = new Functions();
		$this->application = new application();
		$this->admin = new admin();
		$this->customer = new customer();
		$conn = $db->connect();
		$this->db=$db;		   
    }     
	
	
	function getChatRoom($detail,$isParticipantInfoRequired=true,$isChatMessageRequired=false,$ifNotFoundCreateNew=true,$limit=array())//should Include Seller Id and User Id
	{
		if(!empty($detail))
		{
				$countChatRoom=$this->countChatRoom($detail['participant_first'],$detail['participant_second'],$detail['participant_first_type'],$detail['participant_second_type']);
				if($countChatRoom<=0)
				{
					if($ifNotFoundCreateNew)
					{
						// Add new chatroom
						$value=array($detail['seller_id'],$detail['uid'],$this->db->today());
						$rows=array("participant_first","participant_second","adate");
						$created_chatroom_id=$this->rp_insert($this->ctableChatRoom,$value,$rows,1);
					}
					else					
					{
						$created_chatroom_id=0;
					}
				}
				else
				{
					// Get Existing ChatRoom
					$created_chatroom_id=$this->getChatRoomId($detail['participant_first'],$detail['participant_second'],$detail['participant_first_type'],$detail['participant_second_type']);
					
				}
				
				if($created_chatroom_id!=0)
				{
					
					$chat_room_detail=$this->getChatRoomDetail($detail,$isParticipantInfoRequired,$isChatMessageRequired);
					if($chat_room_detail['ack']==1)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Chat Room Created!!","ack_msg"=>"Chat Room Created Successfully.","result"=>$chat_room_detail['result']);
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Neither Chat Created Nor Found!!","ack_msg"=>"Internal Error!!");
						return $reply;
					}		
				}				
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"No Chat Created or Found!!","ack_msg"=>"No Chat Found!!");
					return $reply;
				}
							
		}	
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function addMessageToChatRoom($detail)//should Include Sender Id and Sender type and Chatroom ID
	{
		if(!empty($detail))
		{
				$countChatRoom=$this->countChatRoomFromID($detail['chat_room_id']);
				if($countChatRoom>=1)
				{
					$chat_room_id=$detail['chat_room_id'];
					$chat_room_detail=$this->db->rp_getData($this->ctableChatRoom,"*","id='".$chat_room_id."'");
					if($chat_room_detail)
					{
						$detail_room=mysql_fetch_assoc($chat_room_detail);
						$participant_first=$detail_room['participant_first'];
						$participant_second=$detail_room['participant_second'];
						$participant_first_type=$detail_room['participant_first_type'];
						$participant_second_type=$detail_room['participant_second_type'];
						
						if($detail['sender_type']==$participant_first_type && $detail['sender_id']==$participant_first)
						{
							// Participant First Is Sender
							$sender_id=$detail['sender_id'];
							$sender_type=1;
							$receiver_id=$detail_room['participant_second'];
							$receiver_type=2;
						}
						else if($detail['sender_type']==$participant_second_type && $detail['sender_id']==$participant_second)
						{
							// Participant Second Is Sender
							$sender_id=$detail['sender_id'];
							$sender_type=2;
							$receiver_id=$detail_room['participant_first'];
							$receiver_type=1;
						}
						
						$value=array($chat_room_id,$sender_id,$receiver_id,$sender_type,$detail['content'],$this->db->today());
						$rows=array("chat_room_id","sender","receiver","sent_by","content","adate");
						$created_message_id=$this->rp_insert($this->ctableChatMessage,$value,$rows,0);
						if($created_message_id!=0)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Message Sent!!","ack_msg"=>"Message Sent Successfully.");
							return $reply;	
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Sending Failed Try Again!!");
							return $reply;
						}							
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Chat Room not found","ack_msg"=>"No Detail Found For Your Chat Try Later!!.");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Chat Room not found","ack_msg"=>"No Detail Found For Your Chat Try Later!!.");
					return $reply;
				}	
		}	
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function getUserChatRooms($detail,$isChatRoomDetailRequired=false,$required_columns=array())//should Include User Id
	{
		if(!empty($detail))
		{
				$viewer_id=$detail['viewer_id'];
				$viewer_type=$detail['viewer_type'];
				$required_columns=$this->getRequiredColumns($required_columns);	
				$where="(participant_first='".$viewer_id."' AND participant_first_type='".$viewer_type."')|| ( participant_second='".$viewer_id."' AND participant_second_type='".$viewer_type."')";
				$chatRoomsIds_r=$this->rp_getData($this->ctableChatRoom,$required_columns,$where);
				if($chatRoomsIds_r)
				{
					while($room=mysql_fetch_assoc($chatRoomsIds_r))
					{
						$chatRoomsIds[]=$room['id'];
					}
					if(!empty($chatRoomsIds))
					{
						if($isChatRoomDetailRequired)
						{
							$chatRoomDetails=$this->getChatRoomDetail($chatRoomsIds);
							if($chatRoomDetails['ack']==1)
							{
								$reply=array("ack"=>1,"developer_msg"=>"Great!! Chat Rooms Fetched.","ack_msg"=>"Great!! Chat Rooms Fetched.","result"=>$chatRoomDetails['result']);
								return $reply;
							}	
							else
							{
								$reply=array("ack"=>0,"developer_msg"=>"No Chat Room Founds!!","ack_msg"=>"No Chat Room Founds!!");
								return $reply;
							}
						}
						else
						{
							$reply=array("ack"=>1,"developer_msg"=>"Great!! Chat Rooms Ids Fetched.","ack_msg"=>"Great!! Chat Rooms Fetched.","result"=>$chatRoomsIds);
							return $reply;
						}
						
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"No Chat Room Founds!!","ack_msg"=>"No Chat Room Founds!!");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"No Chat Room Founds!!","ack_msg"=>"No Chat Room Founds!!");
					return $reply;
				}
			
		}	
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"user detail not found","ack_msg"=>"Internal Error!!.");
			return $reply;
		}
	}
	function getChatRoomDetail($detail=array(),$isParticipantInfoRequired=true,$isChatMessageRequired=false,$limit=array()) //done //Get Chat Room Detail From ID
	{
		$result=array();
		if(empty($limit))
		{
			$limit=$this->getLimit();	
		}
		else
		{
			$limit="";
		}
		
		if(!empty($detail))
		{
			$participant_first=$detail['participant_first'];
			$participant_second=$detail['participant_second'];
			$participant_first_type=$detail['participant_first_type'];
			$participant_second_type=$detail['participant_second_type'];
			
			$where="(participant_first='".$participant_first."' AND participant_second='".$participant_second."' AND participant_first_type='".$participant_first_type."' AND participant_second_type='".$participant_second_type."')|| (participant_first='".$participant_second."' AND participant_second='".$participant_first."' AND participant_first_type='".$participant_second_type."' AND participant_second_type='".$participant_first_type."')";
			
			$chatRooms_r=$this->rp_getData($this->ctableChatRoom,"*",$where,"",0);
			if($chatRooms_r)
			{
				while($detail=mysql_fetch_assoc($chatRooms_r))
				{
					$result=$detail;
					$participant_first=$detail['participant_first'];
					$participant_second=$detail['participant_second'];
					$participant_first_type=$detail['participant_first_type'];
					$participant_second_type=$detail['participant_second_type'];
										
					if($isParticipantInfoRequired)
					{
						//  Get Participant first info
						if($participant_first_type==0)
						{
							
							$participant_first_info=$this->admin->getAdminDetail($participant_first,array("id","name","image_path"),false);
							if($participant_first_info['ack']==1)
							{
								$result['participant_first_info']=$participant_first_info['result'];
							}
							else
							{
								$result['participant_first_info']=array();
							}
						}
						else if($participant_first_type==1)
						{
							$participant_first_info=$this->customer->getCustomerDetail($participant_first,array("id","name","image_path"),false);
							if($participant_first_info['ack']==1)
							{								
								$result['participant_first_info']=$participant_first_info['result'];
							}
							else
							{
								$result['participant_first_info']=array();
							}
						}	
						else
						{
							$result['participant_first_info']=array();
						}
						
						//  Get Participant second info
						if($participant_second_type==0)
						{
							
							$participant_second_info=$this->admin->getAdminDetail($participant_second,array("id","name","image_path"),false);
							if($participant_second_info['ack']==1)
							{
								$result['participant_second_info']=$participant_second_info['result'];
							}
							else
							{
								$result['participant_second_info']=array();
							}
						}
						else if($participant_second_type==1)
						{
							$participant_second_info=$this->customer->getCustomerDetail($participant_second,array("id","name","image_path"),false);
							if($participant_second_info['ack']==1)
							{								
								$result['participant_second_info']=$participant_second_info['result'];
							}
							else
							{
								$result['participant_second_info']=array();
							}
						}	
						else
						{
							$result['participant_second_info']=array();
						}
					}
					
					if($isChatMessageRequired)
					{
						
						$messages=$this->getChatMessage(array(),array($detail['id']));
						if($messages['ack']==1)
						$result['messages']=$messages['result'];
						else
						$result['messages']=array();
									
					}
					$results[]=$result;
				}
				
				$reply=array("ack"=>1,"developer_msg"=>"Chat Room detail found","ack_msg"=>"Seller detail found.","result"=>$results);
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
			$reply=array("ack"=>0,"developer_msg"=>"Chat Room not found","ack_msg"=>"Chat Room not found.");
			return $reply;
		}
		
	}
	function getChatMessage($mids=array(),$crids=array(),$lasttimestamp="",$required_columns=array())
	{
		$result=array();
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();		
		$where="";
		if(!empty($mids))
		{
			$mids=implode(",",$mids);
			$where=$this->db->generateWhere($where,"id IN (".$mids.")");
						
		}
		if(!empty($crids))
		{
			$crids=implode(",",$crids);
			$where=$this->db->generateWhere($where,"chat_room_id IN (".$crids.")");
						
		}
		
		if($lasttimestamp!="")
		{
			$where=$this->db->generateWhere($where,"adate >'".$lasttimestamp."'");					
		}
		
		
		if($where!="")
		{
			$where.=" AND isDelete=0";
			$messages=$this->db->rp_getData($this->ctableChatMessage,$required_columns,$where,"",0,$limit);
		}
		else
		{			
			$messages=$this->db->rp_getData($this->ctableChatMessage,$required_columns,"1=1 AND isDelete=0","",0,$limit);
		}
		
		if($messages)
		{
			while($r=mysql_fetch_assoc($messages))
			{
				$r['adate']=date("M d,D",strtotime($r['adate']));
				$result[]=$r;
			}
			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Messages found in database.","ack_msg"=>"Great !! Messages fetched.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"no message found in database.","ack_msg"=>"Sorry !! No message found.");
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
	function countChatRoom($participant_first,$participant_second,$participant_first_type,$participant_second_type)
	{
		$where="(participant_first='".$participant_first."' AND participant_second='".$participant_second."' AND participant_first_type='".$participant_first_type."' AND participant_second_type='".$participant_second_type."')|| (participant_first='".$participant_second."' AND participant_second='".$participant_first."' AND participant_first_type='".$participant_second_type."' AND participant_second_type='".$participant_first_type."')";
		$count=$this->rp_getTotalRecord($this->ctableChatRoom,$where,0);
		return $count;
	}
	function countChatRoomFromID($chat_room_id)
	{
		$count=$this->rp_getTotalRecord($this->ctableChatRoom,"id='".$chat_room_id."'",0);
		return $count;
	}
	function getChatRoomId($participant_first,$participant_second,$participant_first_type,$participant_second_type)
	{
		$where="(participant_first='".$participant_first."' AND participant_second='".$participant_second."' AND participant_first_type='".$participant_first_type."' AND participant_second_type='".$participant_second_type."')|| ( participant_first='".$participant_second."' AND participant_second='".$participant_first."' AND participant_first_type='".$participant_second_type."' AND participant_second_type='".$participant_first_type."')";
		$id=$this->rp_getValue($this->ctableChatRoom,"id",$where,0);
		return $id;
	}
	function countFeedLike($val,$key)
	{
		$where=$key."='".$val."'";
		$count=$this->rp_getTotalRecord($this->ctableFeedLike,$where,0);
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