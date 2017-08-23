<?php
class Feedback extends Functions
{
	public $detail=array();
	public $db;
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;
		   if($id!="")
		   {
			   $this->detail=$this->getProductFromId($id);
			   
		   }
    }
   
   
   function ratingAverage($id)
   {	
		$avg_rating = 0;
		$where = "topic_id='".$id."'";
		$avg_rating = mysql_fetch_assoc($this->db->rp_getData("feedback_topic_by_user","AVG(rating) as A",$where));
		
		return $avg_rating['A']==null?0:$avg_rating['A'];
		$rows = array(	
						"rating"
						);
		$values	= array (
						$avg_rating
						);
			
		$add = $this->db->rp_insert("feedback_topic",$values,$rows,$where);
   }
   
   function addFeedback($topic_id,$user_id,$rating,$review)
   {	
			$dup_where = "user_id='".$user_id."'AND topic_id = '".$topic_id."'";
			$r = $this->db->rp_dupCheck("feedback_topic_by_user",$dup_where);
			if(!$r)
			{				
					$date = date('Y-m-d H-i-s');
					$rows = array(	
		
						"topic_id",
						"user_id" ,
						"user_type",
						"rating",
						"review",
						"adate",						
						);
					$values	 = array(
						
						$topic_id,
						$user_id,
						1,
						$rating,
						$review,
						$date,
						);
				
					$add = $this->db->rp_insert("feedback_topic_by_user",$values,$rows);
					if($add)
					{
							$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully Added Your Feedback !!",
						"developer_msg"=>"You got it!!",
						"result"=>array($add),
						);
						return $ack;
					}
					else
					{
								$ack=array("ack"=>0,
							"ack_msg"=>"not inserted !!",
							"developer_msg"=>"not inserted!!",
							"result"=>array($add),
							);
							return $ack;
					}
					
			}
			else
			{
				$ack=array("ack"=>0,
							"ack_msg"=>"not inserted !!",
							"developer_msg"=>"ak var na padi ne !!",
							"result"=>array(),
							);
							return $ack;
			}	
			
	}
	      
	
}
?>