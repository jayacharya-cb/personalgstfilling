<?php
include("connect.php");
require_once('../../include/class.chat.php');
$chat=new Chat();
//var_dump($_REQUEST);
if(isset($_REQUEST['uid']) && $_REQUEST['uid']!="" && isset($_REQUEST['u_type']) && $_REQUEST['u_type']!="")
{
	$user_id=$_REQUEST['uid'];
	$user_type=$_REQUEST['u_type'];
	if(isset($_REQUEST['op']) && $_REQUEST['op']!="")
	{
		$service=$_REQUEST['op'];
		if($service=="a_n_m")
		{
			if(isset($_REQUEST['cr_id']) && $_REQUEST['cr_id']!="")
			{
			
				$chat_room_id=$_REQUEST['cr_id'];
				$message_content=$_REQUEST['content'];
				$params=array("sender_id"=>$user_id,"sender_type"=>$user_type,"content"=>$message_content,"chat_room_id"=>$chat_room_id);
				$result=$chat->addMessageToChatRoom($params);
				if($result['ack']==1)
				{
					$response=array("ack"=>1,"ack_msg"=>"Message Sent Successfully!!");
					echo json_encode($response);
				}
				else
				{
					$response=array("ack"=>0,"ack_msg"=>"Message Sending Failed!! Try Later");
					echo json_encode($response);
				}
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Internal Error!!');
				echo json_encode($response);
			}
			
		}			
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
			echo json_encode($response);
		}
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
?>