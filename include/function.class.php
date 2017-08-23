<?php
include("main.class.php");

class Functions extends Database
{
	/*
		*** Main Function Developed By Ravi Patel :) <<<
			-> rp_getData() 
				- return single and multi records
			-> rp_getValue() 
				- return single records
			-> rp_getTotalRecord()
				- return number of records
			-> rp_getMaxVal()
				- return maximum value
			-> rp_insert()
				- insert record
			-> rp_delete()
				- delete record
			-> rp_update()
				- update record
			-> tableExists()
				- check whether table exist or not
			-> rp_limitChar()
				- return trimed character string
			-> rp_dupCheck()
				- check for duplicate record in table
			-> rp_location()
				- redirect to given URL
			-> rp_getDisplayOrder()
				- get next display order
			-> rp_createSlug()
				- create alias of given string
			-> rp_getTotalReview()
				- number of total review of product
			-> rp_catData()
				- get cid/sid/ssid from slug
			-> clean()
				- prevent mysql injction
			-> rp_productQty()
				- Current Product Qty
			-> rp_getProductPriceDiv()
				- Product Price Div
			-> aj_updateUserPassword()
				- update change password
			-> rp_location_post()
				- pass url data into post
	*/
	
	public function rp_getData($table, $rows = '*', $where = null, $order = null,$die=0,$limit="") // Select Query, $die==1 will print query By Ravi Patel
    {
		$results = array();
        $q = 'SELECT '.$rows.' FROM '.$table;
        if($where != null)
            $q .= ' WHERE '.$where;
        if($order != null)
            $q .= ' ORDER BY '.$order;
		if($limit != null)
            $q .= ' LIMIT '.$limit;
		if($die==1){
			echo $q;die;
		}
        if($this->tableExists($table))
       	{
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_query($q);
				return $results;
			}else{
				return false;
			}
        }
		else{
			return false;
		}
    }
	
	public function rp_getValue($table, $row=null, $where=null,$die=0) // single records ref HB function
    {
		if($this->tableExists($table) && $row!=null && $where!=null)
       	{
			$q = 'SELECT '.$row.' FROM '.$table.' WHERE '.$where;
			if($die==1){
				echo $q;die;
			}
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_fetch_array(mysql_query($q));
				return $results[$row];
			}else{
				return false;
			}
        }
		else{
			return false;
		}
    }
	
	public function rp_getMaxVal($table, $row=null, $where=null,$die=0)
    {
		if($this->tableExists($table) && $row!=null && $where!=null)
       	{
			$q = 'SELECT MAX('.$row.') as '.$row.' FROM '.$table.' WHERE '.$where;
			if($die==1){
				echo $q;die;
			}
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_fetch_array(mysql_query($q));
				return $results[$row];
			}else{
				return 0;
			}
        }
		else{
			return 0;
		}
    }
	public function rp_getMinVal($table, $row=null, $where=null,$die=0)
    {
		if($this->tableExists($table) && $row!=null && $where!=null)
       	{
			$q = 'SELECT MIN('.$row.') as '.$row.' FROM '.$table.' WHERE '.$where;
			if($die==1){
				echo $q;die;
			}
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_fetch_array(mysql_query($q));
				return $results[$row];
			}else{
				return 0;
			}
        }
		else{
			return 0;
		}
    }
	
	public function rp_getSumVal($table, $row=null, $where=null,$die=0)
    {
		if($this->tableExists($table) && $row!=null && $where!=null)
       	{
			$q = 'SELECT SUM('.$row.') as '.$row.' FROM '.$table.' WHERE '.$where;
			if($die==1){
				echo $q;die;
			}
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_fetch_array(mysql_query($q));
				return $results[$row];
			}else{
				return 0;
			}
        }
		else{
			return 0;
		}
    }
	
	public function rp_getAvgVal($table, $row=null, $where=null,$die=0)
    {
		if($this->tableExists($table) && $row!=null && $where!=null)
       	{
			$q = 'SELECT AVG('.$row.') as '.$row.' FROM '.$table.' WHERE '.$where;
			if($die==1){
				echo $q;die;
			}
			if(mysql_num_rows(mysql_query($q))>0){
				$results = @mysql_fetch_array(mysql_query($q));
				return $results[$row];
			}else{
				return 0;
			}
        }
		else{
			return 0;
		}
    }
	
	
	public function rp_getTotalRecord($table, $where = null,$die=0,$limit=null) // return number of records By Ravi Patel
    {
		$q = 'SELECT * FROM '.$table;
        if($where != null)
            $q .= ' WHERE '.$where;
		if($limit != null)
            $q .= ' LIMIT '.$limit;
		if($die==1){
			echo $q;die;
		}
        if($this->tableExists($table))
			return mysql_num_rows(mysql_query($q))+0;
        else
			return 0;
    }
	
	public function rp_insert($table,$values,$rows = 0,$die=0) // rp_insert - Insert and Die Values By Rav-i Pa-tel
    {
		if($this->tableExists($table))
        {
            $insert = 'INSERT INTO '.$table;
            if(count($rows) > 0)
            {
                $insert .= ' ('.implode(",",$rows).')';
            }
 
            for($i = 0; $i < count($values); $i++)
            {
                if(is_string($values[$i]))
                    $values[$i] = '"'.$values[$i].'"';
            }
            $values = implode(',',$values);
            $insert .= ' VALUES ('.$values.')';
			if($die==1){
				echo $insert;die;
			}
            $ins = @mysql_query($insert);           
            if($ins)
            {
				$last_id = mysql_insert_id();
                return $last_id;
            }
            else
            {
                return false;
            }
        }
    }
	
	public function rp_delete($table,$where = null,$die=0)
    {
        if($this->tableExists($table))
        {
            if($where != null)
            {
                $delete = 'DELETE FROM '.$table.' WHERE '.$where;
				if($die==1){
					echo $delete;die;
				}
				$del = @mysql_query($delete);
            }
            if($del)
            {
                return true;
            }
            else
            {
               return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function rp_update($table,$rows,$where,$die=0) //update query by Ravi Patel
    {
        if($this->tableExists($table))
        {
            // Parse the where values
            // even values (including 0) contain the where rows
            // odd values contain the clauses for the row
			//print_r($where);die;
            
            $update = 'UPDATE '.$table.' SET ';
            $keys = array_keys($rows);
            for($i = 0; $i < count($rows); $i++)
           	{
                if(is_string($rows[$keys[$i]]))
                {
                    $update .= $keys[$i].'="'.$rows[$keys[$i]].'"';
                }
                else
                {
                    $update .= $keys[$i].'='.$rows[$keys[$i]];
                }
                 
                // Parse to add commas
                if($i != count($rows)-1)
                {
                    $update .= ',';
                }
            }
            $update .= ' WHERE '.$where;
			if($die==1){
				echo $update;die;
			}
			//$update = trim($update," AND");
            $query = @mysql_query($update);
            if($query)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
	
	public function tableExists($table)
    {
		$tablesInDb = @mysql_query('SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($tablesInDb)
        {
            if(mysql_num_rows($tablesInDb)==1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
	
	public function rp_limitChar($content,$limit,$url="javascript:void(0);",$txt="&hellip;")
    {
        if(strlen($content)<=$limit){
			return $content;
		}else{
			$ans = substr($content,0,$limit);
			if($url!=""){
				$ans .= "<a href='$url' class='desc'>$txt</a>";
			}else{
				$ans .= "&hellip;";
			}
			return $ans;
		}
    }
	
	public function rp_dupCheck($table, $where = null,$die=0)
    {
        $q = 'SELECT id FROM '.$table;
        if($where != null)
            $q .= ' WHERE '.$where;
		if($die==1){
			echo $q;die;
		}
		if($this->tableExists($table))
       	{
			$results = @mysql_num_rows(mysql_query($q));
			if($results>0){
				return true;
			}else{
				return false;
			}
        }
		else
      		return false;
    }
	
	public function rp_location($redirectPageName=null)
    {
		if($redirectPageName==null){
			header("Location:".$this->SITEURL);
			exit;
		}else{
			header("Location:".$redirectPageName);
			exit;
		}
    }
	
	public function rp_getDisplayOrder($table,$where=null,$die=0)
    {
        $q = 'SELECT MAX(display_order) as display_order FROM '.$table;
        if($where != null)
            $q .= ' WHERE '.$where;
		if($die==1){
			echo $q;die;
		}
        if($this->tableExists($table))
       	{
			$results = @mysql_query($q);
			if(@mysql_num_rows($results)>0){
				$disp_d = mysql_fetch_array($results);
				return intval($disp_d['display_order'])+1;
			}else{
				return 1;
			}
        }
		else{
      		return 1;
		}
    }
	
	public function rp_createSlug($string)   
	{   
		$slug = strtolower(trim(preg_replace('/-{2,}/','-',preg_replace('/[^a-zA-Z0-9-]/', '-', $string)),"-"));
		return $slug;
	}
	
	public function rp_createProSlug($string)   
	{   
		$slug = strtolower(trim(preg_replace('/-{2,}/','-',preg_replace('/[^a-zA-Z0-9-.]/', '-', $string)),"-"));
		return $slug;
	}
	
	public function getDBName()   
	{   
		$dbData = $this->db_host.",".$this->db_user.",".$this->db_pass.",".$this->db_name;
		return $dbData;
	}
	
	public function setViewCounter($tableName,$counterFieldName,$setCounterOnField,$setCounterOnFieldValue)
	{  
		setcookie($counterFieldName.'_'.$setCounterOnFieldValue,"productViewCookie",time() + 3600);
		$counterUpdateQuery = "UPDATE ".$tableName." SET ".$counterFieldName." = ".$counterFieldName."+1 WHERE ".$setCounterOnField."=".$setCounterOnFieldValue;
		//echo $counterUpdateQuery; exit;
		mysql_query($counterUpdateQuery);
	}
	
	public function rp_num($val,$deci="2",$sep=".",$thousand_sep=""){
		return number_format($val,$deci,$sep,$thousand_sep);
	}
	
	public function catData($cslug=null,$sslug=null,$ssslug=null){
		if($cslug!=null && $sslug==null && $ssslug==null){
			return $this->rp_getData("category","*","slug='".$cslug."' AND isDelete=0");
		}else if($cslug!=null && $sslug!=null && $ssslug==null){
			$cid	= $this->rp_getValue("category","id","slug='".$cslug."'");
			return $this->rp_getData("sub_category","*","cid='".$cid."' AND slug='".$sslug."' AND isDelete=0");
		}else if($cslug!=null && $sslug!=null && $ssslug!=null){
			$cid	= $this->rp_getValue("category","id","slug='".$cslug."'");
			$sid	= $this->rp_getValue("sub_category","id","slug='".$sslug."'");
			return $this->rp_getData("sub_sub_category","*","cid='".$cid."' AND sid='".$sid."' AND slug='".$ssslug."' AND isDelete=0");
		}else{
			return false;
		}
		return number_format($val,$deci,$sep,$thousand_sep);
	}
	
	public function rp_getTotalReview($pid)
    {
		return $this->rp_getTotalRecord("product_review","pid = '".$pid."'");
    }
	
	public function clean($string)
	{
		$string = trim($string);								// Trim empty space before and after
		if(get_magic_quotes_gpc()) {
			$string = stripslashes($string);					        // Stripslashes
		}
		$string = mysql_real_escape_string($string);			        // mysql_real_escape_string
		return $string;
	}
	public function cleanArray($array)
	{
		$result=array();
		foreach($array as $key=>$value)
		{
			$result[$key]=$this->clean($value);
		}
		return $result;
	}
	public function rp_getProductQty($pid)
    {
		$proQty = $this->rp_getValue("product","qty","id='".$pid."'"); 
		return $proQty;
    }
	public function rp_getProductPriceDiv($max_price,$sell_price)
    {
		if($sell_price<$max_price && $sell_price!=$max_price){ 
		?>
			<span class="price"><?php echo CURR; ?><?php echo $sell_price; ?></span>
			<span class="price-before-discount"><?php echo CURR; ?><?php echo $max_price; ?></span>
		<?php
		}else{
		?>
			<span class="price"><?php echo CURR; ?><?php echo $sell_price; ?></span>
			<span class="price-before-discount"></span>
		<?php
		}
    }
	public function rp_getShippingCharge($pincode,$pid,$subpid=0)
    {
		if($subpid>0){
			$tabName= "sub_product";
			$pro_id	= $subpid;
		}else{
			$tabName = "product";
			$pro_id	= $pid;
		}
		$deliveryPin_r = $this->rp_getData("delivery_pincode","*","pincode='".$pincode."' AND isDelivery=1","",0);
		if($deliveryPin_r){
			$deliveryPin_d = mysql_fetch_array($deliveryPin_r);
			$area_type 	= $deliveryPin_d["area_type"];
			$area_type;
			if($area_type==0){
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName,"local_ship_charge","id='".$pro_id."'"));
			}else if($area_type==1){
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName,"zonal_ship_charge","id='".$pro_id."'"));
			}else{
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName,"national_ship_charge","id='".$pro_id."'"));
			}
			return $shipping_charge;
		}else{
			return 0;//$this->rp_num($this->rp_getValue($tabName,"national_ship_charge","id='".$pro_id."'"));
		}
    }
	public function rp_checkDeliveryAndShipping($pincode,$pid)
    {
		if($this->rp_getTotalRecord("delivery_pincode","pincode='".$pincode."'")>0){
			if($this->rp_getTotalRecord("delivery_pincode","pincode='".$pincode."' AND isDelivery=1")>0){
				$shipping_charge = $this->rp_getShippingCharge($pincode,$pid);
				if($shipping_charge==0.00){
					$shipping_charge = "Free";
				}else{
					$shipping_charge = CURR.$shipping_charge;
				}
				$_SESSION['SHOPWALA_SESS_PINCODE'] = $pincode;
				
				?>
				<div class="col-md-5"><strong>Delivery available at pincode:</strong> <?php echo $pincode; ?></div>
				<div class="col-md-5"><strong>Shipping Charges:</strong> <?php echo $shipping_charge; ?></div>
				<?php
			}else{
				?>
				<div class="col-md-12"><strong>Delivery not available at pincode:</strong> <?php echo $pincode; ?></div>
				<?php
			}
		}else{
			?>
			<div class="col-md-12"><strong>Sorry, we couldn't find pincode:</strong><?php echo $pincode; ?></div>
			<?php
		}
    }
	public function printr($val,$isDie=1){
		echo "<pre>";
		print_r($val);
		if($isDie){die;}
	}
	public function SToA($array,$val,$die=0){
		
		if($val!="")
		{
			$array[]=$val;			
		}
		return $array;
		
		if($die)
		exit();
	}
	public function today()
	{
		return date("Y-m-d H:i:s");		
	}
	public function getLimit()
	{
		$ul=$this->getRequestedParam("ul"); //upper_limit
		$ll=$this->getRequestedParam("ll"); //lower_limit
		if($ul!="")
		{			
			return array("ul"=>$ul,"ll"=>$ll);
		}
		else
		{
			return array();
		}
		
	}
	public function generateWhere($where,$append,$die=0){
		if($where!="")
		{
			return $where." AND ".$append;
		}
		else
		{
			return $append;
		}
		if($die)
		exit();
	}
	public function generateLike($query,$append,$separator ,$die=0){
		if($query!="")
		{
			return $query." ".$separator." ".$append;
		}
		else
		{
			return $append;
		}
		if($die)
		exit();
	}public function printJSON($val,$die=0){
		$val["extra"]=array("requested_params"=>$_REQUEST);
		echo json_encode($val);
		if($die)
		exit();
	}
	public function getRequestedParam($val,$die=0){		
		if($val!="")
		{
			return (isset($_REQUEST[$val]) && $_REQUEST[$val]!="")?$_REQUEST[$val]:"";
		}
		else
		{
			return "";
		}
		if($die)
		exit();
	}
	public function checkAPI($api_slug,$die=0)
	{
		$count=$this->rp_getTotalRecord("api_table","api_slug='".$api_slug."' OR id='".$api_slug."'");
		if($count>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}  
	public function checkAPIKey($key,$die=0)
	{
		$count=$this->rp_getTotalRecord("api_key_table","api_key='".$key."'");
		if($count>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}  
	
	public function rp_randomString($len=5){
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = "";
		for ($i = 0; $i < $len; $i++) {
			$str .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $str;
	}
	public function rp_get_client_ip(){
	  $ipaddress = '';
	  if (getenv('HTTP_CLIENT_IP'))
		  $ipaddress = getenv('HTTP_CLIENT_IP');
	  else if(getenv('HTTP_X_FORWARDED_FOR'))
		  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	  else if(getenv('HTTP_X_FORWARDED'))
		  $ipaddress = getenv('HTTP_X_FORWARDED');
	  else if(getenv('HTTP_FORWARDED_FOR'))
		  $ipaddress = getenv('HTTP_FORWARDED_FOR');
	  else if(getenv('HTTP_FORWARDED'))
		  $ipaddress = getenv('HTTP_FORWARDED');
	  else if(getenv('REMOTE_ADDR'))
		  $ipaddress = getenv('REMOTE_ADDR');
	  else
		  $ipaddress = 'UNKNOWN';
	
	  return $ipaddress;
	}
	
	function round($number,$scale=2)
	{
		return round($number,$scale);
	}
	public function getlastInsertId($ctable,$die=0)
	{
		$lastInsertId=$this->rp_getValue($ctable,"MAX(`id`)","1=1",0);
		return $lastInsertId+1;
	}
	public function generateSerialWithPrefix($prefix,$number,$pading_number=4)
	{
		return $prefix.(str_pad($number,$pading_number,'0',STR_PAD_LEFT));
	}
	function getIndianCurrency($number)
	{
		$decimal = round($number - ($no = floor($number)), 2) * 100;
		$hundred = null;
		$digits_length = strlen($no);
		$i = 0;
		$str = array();
		$words = array(0 => '', 1 => 'one', 2 => 'two',
			3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
			7 => 'seven', 8 => 'eight', 9 => 'nine',
			10 => 'ten', 11 => 'eleven', 12 => 'twelve',
			13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
			16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
			19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
			40 => 'forty', 50 => 'fifty', 60 => 'sixty',
			70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
		$digits = array('', 'hundred','thousand','lakh', 'crore');
		while( $i < $digits_length ) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += $divider == 10 ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
				$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
				$str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
			} else $str[] = null;
		}
		$Rupees = implode('', array_reverse($str));
		$paise = ($decimal) ? " And " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
		return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise ;
	}
}
include("cart.class.php");
include("admin.class.php");
?>