<?php
class System extends Functions
{
	function __construct() {
	   $db = new Functions();
	   $conn = $db->connect();
	   $this->db=$db;
       
	}
	
	function deleteImage($url)
	{
		if(file_exists($url))
		{
			unlink($url);
		}
	}
	function getImageDetail($filename,$folder,$default_image)
	{
		if($filename!="")
		{
			if(file_exists($folder.$filename))
			{
				$detail=array('image_name'=>$filename,"preview_path"=>$folder.$filename,"default_img"=>$default_image);
				
			}
			else
			{				
				$detail=array('image_name'=>"","preview_path"=>$default_image,"default_img"=>$default_image);
			}
		}
		else
		{
			$detail=array('image_name'=>$filename,"preview_path"=>$default_image,"default_img"=>$default_image);
		}
		return $detail;		
	}
	function pageBar($hierarchy,$pageToolbar="")
	{
		if(!empty($hierarchy))
		{
			?>
		<!-- BEGIN PAGE BAR -->
		<div class="page-bar">
			<ul class="page-breadcrumb">
			<?php for($i=0;$i<sizeof($hierarchy);$i++)
			{				
			?>
				<li>
					<?php if($i!=sizeof($hierarchy)-1)
					{
						?>
						<a href="<?php echo $hierarchy[$i]['link'];?>"><?php echo $hierarchy[$i]['title'];?></a>
						<i class="fa fa-circle"></i>
					<?php 
					}
					else
					{
						?>
						 <span><?php echo $hierarchy[$i]['title'];?></span>
						<?php 
					}
					?>
				</li>
			<?php 
			}
			?>								
			</ul>
			<div class="page-toolbar">
				<?php echo $pageToolbar;?>
			</div>
		</div>
		<!-- END PAGE BAR -->
		<?php 
		}
	}
	function changeThemeMenu()
	{
		?>
		 <!-- BEGIN THEME PANEL -->
			<div class="theme-panel hidden-xs hidden-sm">
				<div class="toggler"> </div>
				<div class="toggler-close"> </div>
				<div class="theme-options">
					<div class="theme-option theme-colors clearfix">
						<span> THEME COLOR </span>
						<ul>
							<li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default"> </li>
							<li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue"> </li>
							<li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue"> </li>
							<li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey"> </li>
							<li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light"> </li>
							<li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2"> </li>
						</ul>
					</div>
					<div class="theme-option">
						<span> Layout </span>
						<select class="layout-option form-control input-sm">
							<option value="fluid" selected="selected">Fluid</option>
							<option value="boxed">Boxed</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Header </span>
						<select class="page-header-option form-control input-sm">
							<option value="fixed" selected="selected">Fixed</option>
							<option value="default">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Top Menu Dropdown</span>
						<select class="page-header-top-dropdown-style-option form-control input-sm">
							<option value="light" selected="selected">Light</option>
							<option value="dark">Dark</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Sidebar Mode</span>
						<select class="sidebar-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Sidebar Menu </span>
						<select class="sidebar-menu-option form-control input-sm">
							<option value="accordion" selected="selected">Accordion</option>
							<option value="hover">Hover</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Sidebar Style </span>
						<select class="sidebar-style-option form-control input-sm">
							<option value="default" selected="selected">Default</option>
							<option value="light">Light</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Sidebar Position </span>
						<select class="sidebar-pos-option form-control input-sm">
							<option value="left" selected="selected">Left</option>
							<option value="right">Right</option>
						</select>
					</div>
					<div class="theme-option">
						<span> Footer </span>
						<select class="page-footer-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
				</div>
			</div>
			<!-- END THEME PANEL -->
		<?php
	}
	
	function getMessageBlock()
	{
			if(isset($_SESSION['success_msg']))
			{
				$success_msg=$_SESSION['success_msg'];
				unset($_SESSION['success_msg']);
			}
			if(isset($_SESSION['error_msg']))
			{
				$error_msg=$_SESSION['error_msg'];
				unset($_SESSION['error_msg']);
			}
			if(!empty($success_msg)){
				?>			
				<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
					<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
					<?php 
					foreach($success_msg as $s)
					{
					?>
					<b>Success! </b><?php echo $s; ?>
					<?php }?>
				</div>
				<?php
				}
				if(!empty($error_msg)){
				?>
				<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
					<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
					<?php 
					foreach($error_msg as $e)
					{
					?>
					<b>Error! </b><?php echo $e; ?>
					<?php 
					}
					?>
				</div>
				<?php
				}
									
	}
	
	function addMessage($message,$type)// success/error
	{
		if($message!="")
		{
			if($type=='success')
			{
				$success_msg=array();
				if(isset($_SESSION['success_msg']))
				{
					$success_msg=$_SESSION['success_msg'];
					
				}
				$success_msg[]=$message;
				$_SESSION['success_msg']=$success_msg;				
			}
			else if($type=='error')
			{
				$error_msg=array();
				if(isset($_SESSION['error_msg']))
				{
					$error_msg=$_SESSION['error_msg'];					
				}	
				$error_msg[]=$message;
				$_SESSION['error_msg']=$error_msg;
			}
		}
		return false;
	}

	public function getAddButton($ctable,$url=null)
		{
			$rights=$_SESSION['rights'];	
			if($ctable!="" && $rights['insert_flag']==1 ){
				if($url!=null){
					?>
					<div class="btn-group">
						<a class="btn sbold blue-ebonyclay" href="<?php echo $url; ?>"> Add New
							<i class="fa fa-plus"></i>
						</a>
					</div>
					<?php
				}else{
					?>
					<div class="btn-group">
						<a class="btn sbold blue-ebonyclay" href="add_<?php echo $ctable; ?>.php?mode=add"> Add New
							<i class="fa fa-plus"></i>
						</a>
					</div>
					<?php
				}
			}	
		}
		public function getUpdateButton($frmId=null,$title="Update")
		{
			if($frmId!=null && $rights['update_flag']==1){
				?>
				<button class="btn btn-primary btn-flat sidebar" onClick="document.<?php echo $frmId; ?>.submit();"><?php echo $title ?></button>
				<?php
			}else{
				?>
				<button class="btn btn-primary btn-flat sidebar" onClick="document.frm.submit();"><?php echo $title; ?></button>
				<?php
			}
		}
		public function getAddApplicationPageButton()
		{
			?>
			<a class="btn btn-primary btn-flat sidebar" href="manage_page_table.php" >Application Pages</a>
			<?php
		}
		public function getLabel($content,$href,$type)
		{
			
			$label_type=array("danger"=>"label label-danger","success"=>"label label-success","warning"=>"label label-warning","info"=>"label label-info","default"=>"label label-default");
			if($type=='auto')
			{
				$header=$this->checkPageResponse($href);
				if($header=='200')
				{
					$class=$label_type['success'];
				}
				else if($header=='302')
				{
					$class=$label_type['success'];
				}
				else if($header=='404')
				{
					$class=$label_type['danger'];
				}
				else
				{
					$class=$label_type['info'];
				}
			}
			else if($type=='random')
			{
				$key = array_rand($label_type);
				$class = $label_type[$key];
			}
			else
			{
				if(array_key_exists($type,$label_type))
				{
					$class=$label_type[$type];
				}
				else
				{
					$class=$label_type['default'];
				}
			}
			
			
			return '<a class="'.$class .' col-sm-12" style="margin-top:10px" href="'. $href.'" >'.$content.'</a>';
			
		}
		public function rp_paginate_function($item_per_page, $current_page, $total_records, $total_pages)
		{
			
			$pagination = '';
			if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
				$right_links    = $current_page + 3; 
				$previous       = $current_page - 3; //previous link 
				$next           = $current_page + 1; //next link
				$first_link     = true; //boolean var to decide our first link
				
				if($current_page > 1){
					$previous_link = ($previous<=0)?1:$previous;
					$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
					$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
						for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
							if($i > 0){
								$pagination .= '<li class="paginate_button "><a href="#"  data-page="'.$i.'" aria-controls="datatable1" title="Page'.$i.'">'.$i.'</a></li>';
							}
						}   
					$first_link = false; //set first link to false
				}
				
				if($first_link){ //if current active page is first link
					$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
				}elseif($current_page == $total_pages){ //if it's the last active link
					$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
				}else{ //regular current link
					$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
				}
						
				for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
					if($i<=$total_pages){
						$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
					}
				}
				if($current_page < $total_pages){ 
					$next_link = ($i > $total_pages)? $total_pages : $i;
					$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
					$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
				}
			}
			return $pagination; //return pagination links
		}	
}
?>