<?php
header('Content-Type: application/json');
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
		if($service=="g_n_m")
		{
			if(isset($_REQUEST['l_m_ttstmp']) && $_REQUEST['l_m_ttstmp']!="")
			{
				// How often to poll, in microseconds (1,000,000 μs equals 1 s)
				define('MESSAGE_POLL_MICROSECONDS', 500000);
				 
				// How long to keep the Long Poll open, in seconds
				define('MESSAGE_TIMEOUT_SECONDS', 30);
				 
				// Timeout padding in seconds, to avoid a premature timeout in case the last call in the loop is taking a while
				define('MESSAGE_TIMEOUT_SECONDS_BUFFER', 5);
				 
				// Hold on to any session data you might need now, since we need to close the session before entering the sleep loop				
				$last_message_timestamp=$_REQUEST['l_m_ttstmp'];
				 
				// Close the session prematurely to avoid usleep() from locking other requests
				session_write_close();
				 
				// Automatically die after timeout (plus buffer)
				set_time_limit(MESSAGE_TIMEOUT_SECONDS+MESSAGE_TIMEOUT_SECONDS_BUFFER);
				 
				// Counter to manually keep track of time elapsed (PHP's set_time_limit() is unrealiable while sleeping)
				$counter = MESSAGE_TIMEOUT_SECONDS;
				
				while($counter > 0)
				{
					// Check for new data (not illustrated)
					$params=array("viewer_id"=>$user_id,"viewer_type"=>$user_type);
					$chatroom_ids=$chat->getUserChatRooms($params,false,array("id"));
					if($chatroom_ids['ack']==1)
					{
						$chat_room_ids=$chatroom_ids['result'];
						$messages=$chat->getChatMessage(array(),$chat_room_ids,$last_message_timestamp);						
						if($messages['ack']==1)
						{
							$response=array('ack'=>1,'ack_msg'=>'New Messages Found!!',"result"=>$messages['result']);
							break;
						}
						else
						{
							
							$response=array('ack'=>0,'ack_msg'=>'');							
							// Otherwise, sleep for the specified time, after which the loop runs again
							usleep(MESSAGE_POLL_MICROSECONDS);					
							// Decrement seconds from counter (the interval was set in μs, see above)
							$counter -= MESSAGE_POLL_MICROSECONDS / 1000000;
						}
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'No Chatroom found!!');						
						break;
					}
					
				}
				
				echo json_encode($response);
				
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
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
?>