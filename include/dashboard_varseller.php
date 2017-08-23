<?php
/*
** >>> dashboard_main_array Parameter Description By Ravi Patel :) <<<
		-> 1 = color of box
		-> 2 = table name
		-> 3 = where condition for filtered record i.e 1=1
		-> 4 = Title of box
		-> 5 = URL 
*/
$dashboard_main_array = array(
	
		//0=>array("green",$db->rp_getTotalRecord("android_home_detail","isDelete=0"),"Android Home","manage_android_home_detail.php"),
		//1=>array("green",$db->rp_getTotalRecord("advertise","isDelete=0"),"Advertises","manage_advertise.php"),
		//2=>array("yellow",$db->rp_getTotalRecord("category","isDelete=0"),"Category","manage_category.php"),
		//3=>array("yellow",$db->rp_getTotalRecord("sub_category","isDelete=0"),"Sub Category","manage_sub_category.php"),
		//4=>array("yellow",$db->rp_getTotalRecord("sub_sub_category","isDelete=0"),"Sub to sub category","manage_sub_sub_category.php"),
		5=>array("yellow",$db->rp_getTotalRecord("product","isDelete=0 AND seller_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'"),"Product","manage_product.php"),
		//6=>array("green",$db->rp_getTotalRecord("cartdetails"),"Order","manage_order.php"),
		//7=>array("green",$db->rp_getTotalRecord("utility"),"Blocked Ips","manage_blocked_ips.php"),
		//8=>array("green",$db->rp_getTotalRecord("dbbackup"),"Data base backup","database_backup.php"),
		//9=>array("green","1","View User Cart Status","view_user_cart_status.php"),
		//10=>array("green","1","Slide Show","manage_slideshow.php"),
		//11=>array("green","1","Newslatter","manage_newsletter.php"),
		
		
	);
	/*
	
	0=>array("green",$db->rp_getTotalRecord("customer","isDelete=0"),"Customer(s)","manage_customer.php"),
		1=>array("green",$db->rp_getTotalRecord("part","1=1"),"Part(s)","manage_part.php"),
		2=>array("green",$db->rp_getTotalRecord("material_specification","isDelete=0"),"Material Specification(s)","manage_material_specification.php"),
		3=>array("green",$db->rp_getTotalRecord("grade","isDelete=0"),"Grade(s)","manage_grade.php"),
		5=>array("green",$db->rp_getTotalRecord("body_heat","isDelete=0"),"Body Heat(s)","manage_body_heat.php"),
		6=>array("green",$db->rp_getTotalRecord("bonnet_heat","isDelete=0"),"Bonnet Heat(s)","manage_bonnet_heat.php"),
		7=>array("green",$db->rp_getTotalRecord("report1","1=1"),"Essar Report(s)","manage_report1.php"),
		8=>array("green",$db->rp_getTotalRecord("report2","1=1"),"General Report(s)","manage_report2.php"),
		9=>array("green",$db->rp_getTotalRecord("report3","1=1"),"IV Report(s)","manage_report3.php"),
	*/
?>