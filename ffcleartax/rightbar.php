<?php 
require_once('../include/class.customer.php');
$obj_admins=new admin();
$obj_customers=new customer();
$current_logged_in_type=0;
$my_id=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$admins=$obj_admins->getAdmins($_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],array("id","name","image_path","occupation"));
$customers=$obj_customers->getCustomers($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']);
$chats=array();
$chat_users=array();
$chat_rooms=array();
?>  
  
  <!-- BEGIN QUICK SIDEBAR -->
                <a href="javascript:;" class="page-quick-sidebar-toggler">
                    <i class="icon-login"></i>
                </a>
                <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
                    <div class="page-quick-sidebar">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab"> Chat
                                    
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" data-target="#quick_sidebar_tab_2" data-toggle="tab"> Alerts
                                    
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> More
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-bell"></i> Alerts </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-info"></i> Notifications </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-speech"></i> Activities </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-settings"></i> Settings </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                                <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd" data-wrapper-class="page-quick-sidebar-list">
                                    <h3 class="list-heading">Staff</h3>
									<?php 
										if($admins['ack']==1)
										{
											$admins=$admins['result'];
											?>
											<ul class="media-list list-items">
											<?php 
											foreach($admins as $a)
											{
												
												if($a['chat']['ack']==1)
												{
													
													$chat_room_id=$a['chat']['result'][0]['id'];
													$chats[]=array('id'=>"page-quick-sidebar-item".$a['id'],'content'=>$a['chat']['result']);
													$chat_users[$a['id']]=array("id"=>$a['id'],"name"=>$a['name'],"profile_picture_path"=>$a['image_path']);
													$chat_rooms[$a['chat']['result'][0]['id']]=array("chat_room_id"=>$a['chat']['result'][0]['id'],"component_id"=>"#page-quick-sidebar-item".$a['id'],"messages"=>$a['chat']['result'][0]['messages'],"participant_first_info"=>$a['chat']['result'][0]['participant_first_info'],"participant_second_info"=>$a['chat']['result'][0]['participant_second_info'],"participant_first_type"=>$a['chat']['result'][0]['participant_first_type'],"participant_second_type"=>$a['chat']['result'][0]['participant_second_type']);
												}
												else
												{
													$chat_room_id=0;
												}
												
											?>
                                    
											<li class="media" data-id="<?php echo $a['id'];?>" data-cr-id="<?php echo $chat_room_id;?>" data-chat-container="<?php echo "#page-quick-sidebar-item".$a['id'];?>">
												<div class="media-status">
													<span class="badge badge-success">0</span>
												</div>
												<img class="media-object" src="<?php echo $a['image_path']?>" alt="...">
												<div class="media-body">
													<h4 class="media-heading"><?php echo $a['name']?></h4>
													<div class="media-heading-sub"> <?php echo $a['occupation']?> </div>
												</div>
											</li>
										   
											<?php 
											}
											
									?>
                                    </ul>
									
									<?php 
										}
										else
										{
											?>
											<h4 class="text-center">No Chat found!!</h4><br/>
										   
											<?php
										}	
										
									?>
                                    <h3 class="list-heading">Customers</h3>
                                    <?php 
										if($customers['ack']==1)
										{
											$customers=$customers['result'];
											?>
											<ul class="media-list list-items">
											<?php 
											foreach($customers as $a)
											{
												
											?>
                                    
											<li class="media">
												<div class="media-status">
													<span class="badge badge-success">0</span>
												</div>
												<img class="media-object" src="<?php echo $a['image_path']?>" alt="...">
												<div class="media-body">
													<h4 class="media-heading"><?php echo $a['name']?></h4>
													<div class="media-heading-sub"> <?php echo $a['occupation']?> </div>
												</div>
											</li>
										   
											<?php 
											}
											
									?>
                                    </ul>
									
									<?php 
										}
										else
										{
											?>
											<h4 class="text-center">No Chat found!!</h4><br/>
										   
											<?php
										}	
										
									?>
                                </div>
								<?php 
								if(!empty($chats))
								{
									foreach($chats as $chat)
									{
										//print_r($chat);									
									?>
										<div class="page-quick-sidebar-item" id="<?php echo $chat['id'];?>">
										<div class="page-quick-sidebar-chat-user">
										<div class="page-quick-sidebar-nav">
											<a href="javascript:;" class="page-quick-sidebar-back-to-list">
												<i class="icon-arrow-left"></i>Back</a>
										</div>
										<div class="page-quick-sidebar-chat-user-messages">
											<?php
											
											$chat_content=$chat['content'][0];
											$chat_room_id=$chat_content['id'];
											$participant_first=$chat_content['participant_first'];
											$participant_second=$chat_content['participant_second'];
											$participant_first_type=$chat_content['participant_first_type'];
											$participant_second_type=$chat_content['participant_second_type'];
											$participant_first_info=$chat_content['participant_first_info'];
											$participant_second_info=$chat_content['participant_second_info'];
											$chat_messages=$chat_content['messages'];
											$mine_info=array();
											$her_info=array();
											if($current_logged_in_type==$participant_first_type && $my_id==$participant_first)
											{
												$who_am_i=1;// You are participant first;
												$mine_info=$participant_first_info;
												$her_info=$participant_second_info;
											}
											else if($current_logged_in_type==$participant_second_type && $my_id==$participant_second)
											{
												$who_am_i=2;// You are participant second;
												$mine_info=$participant_second_info;
												$her_info=$participant_first_info;
											}
											else
											{
												continue;
											}
											
											
											foreach($chat_messages as $message)
											{
												$out=false;
												$in=false;
												$message_sent_by=$message['sent_by'];
												if($message_sent_by==$who_am_i)
												{
													$out=true;	
												}
												else 
												{
													$in=true;
												}
												
												
												if($out)
												{
																									
													?>
													<div class="post out">
													<img class="avatar" alt="" src="<?php echo $mine_info['image_path']?>" />
														<div class="message">
															<span class="arrow"></span>
															<a href="javascript:;" class="name"><?php echo $mine_info['name']?></a>
															<span class="datetime"><?php echo $message['adate']?></span>
															<span class="body"> <?php echo  $message['content']?></span>
														</div>
													</div>
													<?php
												}
												else if($in) 
												{										
													?>
													<div class="post in">
													<img class="avatar" alt="" src="<?php echo $her_info['image_path']?>" />
														<div class="message">
															<span class="arrow"></span>
															<a href="javascript:;" class="name"><?php echo  $her_info['name']?></a>
															<span class="datetime"><?php echo  $message['adate']?></span>
															<span class="body"> <?php echo $message['content']?></span>
														</div>
													</div>
													<?php
												}										
											}
											
											?>
											
										   
										</div>
										<div class="page-quick-sidebar-chat-user-form">
											<div class="input-group">
												<input type="text" class="form-control" placeholder="Type a message here...">
												<div class="input-group-btn">
													<button data-id="<?php echo $her_info['id'];?>" data-cr-id="<?php echo $chat_room_id;?>" type="button" class="btn green">
														<i class="icon-paper-clip"></i>
													</button>
												</div>
											</div>
										</div>
										</div>
										</div>

									<?php 
									}
								}
								else
								{
									
								}
								
								?>
                            </div>
                            <div class="tab-pane page-quick-sidebar-alerts" id="quick_sidebar_tab_2">
                                <div class="page-quick-sidebar-alerts-list">
                                    <h3 class="list-heading">General</h3>
                                    <ul class="feeds list-items">
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 4 pending tasks.
                                                            <span class="label label-sm label-warning "> Take action
                                                                <i class="fa fa-share"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> Just now </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bar-chart-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc"> Finance Report for year 2013 has been released. </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date"> 20 mins </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-danger">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 5 pending membership that requires a quick review. </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 24 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> New order received with
                                                            <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 30 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-success">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 5 pending membership that requires a quick review. </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 24 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bell-o"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> Web server hardware needs to be upgraded.
                                                            <span class="label label-sm label-warning"> Overdue </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 2 hours </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-default">
                                                                <i class="fa fa-briefcase"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc"> IPO Report for year 2013 has been released. </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date"> 20 mins </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                    <h3 class="list-heading">System</h3>
                                    <ul class="feeds list-items">
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 4 pending tasks.
                                                            <span class="label label-sm label-warning "> Take action
                                                                <i class="fa fa-share"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> Just now </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-danger">
                                                                <i class="fa fa-bar-chart-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc"> Finance Report for year 2013 has been released. </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date"> 20 mins </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 5 pending membership that requires a quick review. </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 24 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> New order received with
                                                            <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 30 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-success">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> You have 5 pending membership that requires a quick review. </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 24 mins </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-warning">
                                                            <i class="fa fa-bell-o"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc"> Web server hardware needs to be upgraded.
                                                            <span class="label label-sm label-default "> Overdue </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date"> 2 hours </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-info">
                                                                <i class="fa fa-briefcase"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc"> IPO Report for year 2013 has been released. </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date"> 20 mins </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">
                                <div class="page-quick-sidebar-settings-list">
                                    <h3 class="list-heading">General Settings</h3>
                                    <ul class="list-items borderless">
                                        <li> Enable Notifications
                                            <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                        <li> Allow Tracking
                                            <input type="checkbox" class="make-switch" data-size="small" data-on-color="info" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                        <li> Log Errors
                                            <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="danger" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                        <li> Auto Sumbit Issues
                                            <input type="checkbox" class="make-switch" data-size="small" data-on-color="warning" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                        <li> Enable SMS Alerts
                                            <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                    </ul>
                                    <h3 class="list-heading">System Settings</h3>
                                    <ul class="list-items borderless">
                                        <li> Security Level
                                            <select class="form-control input-inline input-sm input-small">
                                                <option value="1">Normal</option>
                                                <option value="2" selected>Medium</option>
                                                <option value="e">High</option>
                                            </select>
                                        </li>
                                        <li> Failed Email Attempts
                                            <input class="form-control input-inline input-sm input-small" value="5" /> </li>
                                        <li> Secondary SMTP Port
                                            <input class="form-control input-inline input-sm input-small" value="3560" /> </li>
                                        <li> Notify On System Error
                                            <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="danger" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                        <li> Notify On SMTP Error
                                            <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="warning" data-on-text="ON" data-off-color="default" data-off-text="OFF"> </li>
                                    </ul>
                                    <div class="inner-content">
                                        <button class="btn btn-success">
                                            <i class="icon-settings"></i> Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END QUICK SIDEBAR -->