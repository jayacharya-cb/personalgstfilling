<?php
class Product extends Functions
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
   
   function getProductFromId($id)
   {
		$where = "id='".$id."'";
		$data    = mysql_fetch_assoc($this->db->rp_getData('product',"*",$where));
		return $data;
   }
   
   function getProductFromQuery($query)
   {	
		$result=array();
		$where = "name like '%".$query."%' OR product_code like '%".$query."%'";
		$data    = $this->db->rp_getData('product',"*",$where);
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
	function getProductDetail()
	{	
		$result=array();
		$data    = $this->db->rp_getData('product',"*");
		
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
	
}
?>