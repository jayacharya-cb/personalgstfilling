 <?php
$loggedInUser=$db->getAdminDetail($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']); 
if($loggedInUser['ack']==1)
{
	$loggedInUser=$loggedInUser['result'];
}
else
{
	$db->rp_location("logout.php");
}
$parser=xml_parser_create();

//Function to use at the start of an element
function start($parser,$element_name,$element_attrs) {
    switch($element_name) {
        case "GROUP":
		$selected=($GLOBALS['page_id']==$element_attrs['PAGE_ID'])?' <span class="selected"></span>':"";
		$active=($GLOBALS['page_id']==$element_attrs['PAGE_ID'])?'active':"";
		$open=($GLOBALS['page_id']==$element_attrs['PAGE_ID'])?'open':"";
        echo '<li class="menu-dropdown classic-menu-dropdown '.$active.'">
					<a href="javascript:;">
						'.$element_attrs['TITLE'].'
					  '.$selected.'
					  <i class=" fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">';
    break;     
        case "ITEM":		
        echo '<li><a href="'.$element_attrs['URL'].'"><i class="'.$element_attrs['ICON'].'"></i><span class="title"></span>';
    }
}

//Function to use at the end of an element
function stop($parser,$element_name) {    
	 switch($element_name) {
        case "GROUP":
        echo "</ul></li>";
    break;     
        case "ITEM":		
        echo '</a></li>';
    }
}

//Function to use when finding character data
function char($parser,$data) {
    echo $data;
}

//Specify element handler
xml_set_element_handler($parser,"start","stop");

//Specify data handler
xml_set_character_data_handler($parser,"char");

//Open XML file
$fp=fopen(__DIR__."/xml/var_config_sidebar.xml","r");

?>
 <div class="page-wrapper-row">
	<div class="page-wrapper-top">
		<!-- BEGIN HEADER -->
		<div class="page-header">
			<!-- BEGIN HEADER TOP -->
			<div class="page-header-top hidden-lg hidden-md">
				<div class="">
					<!-- BEGIN LOGO -->
					<div class="page-logo">
						<a href="dashboard.php">
							<img src="../assets/layouts/layout3/img/logo-default.jpg" alt="logo" class="logo-default">
						</a>
					</div>
					<!-- END LOGO -->
					<!-- BEGIN RESPONSIVE MENU TOGGLER -->
					<a href="javascript:;" class="menu-toggler"></a>
					<!-- END RESPONSIVE MENU TOGGLER -->
					<!-- BEGIN TOP NAVIGATION MENU -->
					<div class="top-menu">
						<ul class="nav navbar-nav pull-right">
							<!-- BEGIN TODO DROPDOWN -->
							<li class="dropdown dropdown-extended dropdown-tasks dropdown-dark" id="header_task_bar">
								<a href="logout.php" class="dropdown-toggle" >
									<i class="icon-lock"></i>
								</a>
								
							</li>
							<!-- END TODO DROPDOWN -->
							<li class="droddown dropdown-separator">
								<span class="separator"></span>
							</li>
						
						
						</ul>
					</div>
					<!-- END TOP NAVIGATION MENU -->
				</div>
			</div>
			<!-- END HEADER TOP -->
			<!-- BEGIN HEADER MENU -->
			<div class="page-header-menu">
				<div class="">
					<!-- BEGIN MEGA MENU -->
					<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
					<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
					<div class="hor-menu  ">
						<ul class="nav navbar-nav">
							
							<li class="hidden-xs hidden-sm">
							<a href="dashboard.php">
							<img src="../assets/layouts/layout3/img/logo-default.png" style="height: 20px" alt="logo" class="logo-default">
							</a>
							</li>
					
							<?php 			
								//Read data
								while ($data=fread($fp,4096)) {
									xml_parse($parser,$data,feof($fp)) or
									die (sprintf("XML Error: %s at line %d",
									xml_error_string(xml_get_error_code($parser)),
									xml_get_current_line_number($parser)));
								}

								//Free the XML parser
								xml_parser_free($parser);
							?>
						</ul>	
						
						
					</div>
					<div class="hor-menu  pull-right hidden-xs hidden-sm" >
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown dropdown-user dropdown-dark">
						<a href="logout.php" class="dropdown-toggle" >
						<span class="username username-hide-mobile"><i class="icon-lock"></i> &nbsp;Logout</span>
						</a>
						
					</li>
				</ul>
		</div>
					<!-- END MEGA MENU -->
				</div>
			</div>
			<!-- END HEADER MENU -->
		</div>
		<!-- END HEADER -->
	</div>
	</div>
 
           