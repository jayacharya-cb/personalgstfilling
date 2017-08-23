<?php
class Category extends Functions
{
	public $detail=array();
	public $ctable="category";
	public $db;
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;
		   if($id!="")
		   {
			   $this->detail=$this->getFromId($id);
			   
		   }
    }     
	function get()
	{	
		$result=array();
		$data    = $this->db->rp_getData($this->ctable,"*");
		
		if($data)
		{
			while($row=mysql_fetch_assoc($data))
			{
				$result[]=$row;
			}
			return $result;
		}
		else
		{
			return $result;
		}	
		
	}
	function set($data)
	{
		$this->detail=$data;
	}	
	
	function getFromId($id)
    {
		$where = "id='".$id."'";
		$data    = mysql_fetch_assoc($this->db->rp_getData($this->ctable,"*",$where));
		return $data;
    }
	   
	function getFromQuery($where)
	{	
		$result=array();		
		$data    = $this->db->rp_getData($this->ctable,"*",$where);
		if($data)
		{
			while($row=mysql_fetch_assoc($data))
			{
				$result[]=$row;
			}
			return $result;
		}
		else
		{
			return $result;
		}
	}
	function add($data)
	{
		$rows=array_keys($data);
		$values=array_values($data);
		$cid=$this->db->rp_insert($this->ctable,$values,$rows);
		if($cid!=0)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Success!! New category added.",
					"developer_msg"=>"Category Added",
					"result"=>array('cid'=>$cid),
					);				
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"Error!! New category can't be added.",
					"developer_msg"=>"Check Database for column names.",					
					);
		}	
		return $ack;
	}
	function update($data,$condition)
	{	$isUpdated=$this->db->rp_update($this->ctable,$data,$condition,0);
		if($isUpdated)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Success!! Category detail updated.",
					"developer_msg"=>"Category Updated",					
					);				
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"Error!! Category can't be updated.",
					"developer_msg"=>"Check Database for column names.",					
					);
		}	
		return $ack;
	}
	function active($data,$condition)
	{
		$isUpdated=$this->db->rp_update($this->ctable,$data,$condition);
		if($isUpdated)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Success!! Category status changed.",
					"developer_msg"=>"Category Status Changed",					
					);				
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"Error!! Category status can't be changed.",
					"developer_msg"=>"Check Database for column names.",					
					);
		}
		return $ack;
	}
	function delete($data,$condition)
	{
		
		$isUpdated=$this->db->rp_update($this->ctable,$data,$condition,0);
		if($isUpdated)
		{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Success!! Category deleted.",
					"developer_msg"=>"Category Deleted",					
					);				
		}
		else
		{
				$ack=array( "ack"=>0,
					"ack_msg"=>"Error!! Category can't be delete.",
					"developer_msg"=>"Check Database for column names.",					
					);
		}
		return $ack;
	}
	function count($condition)
	{
		$r = $this->db->rp_getTotalRecord($this->ctable,$condition,0);
		return $r;
	}	
	
}
?>