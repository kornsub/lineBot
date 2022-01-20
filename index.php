<?php 

//$access_token = "0iUcdcZOrWfzqmAsJvL+4w71v+DKgg+a+QKnZZuu4kO02zRaG03ei2FS1VUgz5M8C0ip/IwOQC7HQMZTHche5CpJUWaxvHjnVH9/uCNcmgvxBbBmLU4zLZ9fg1CQbRCdXPxWN/zGwm48V83918r2kwdB04t89/1O/w1cDnyilFU=";
//$access_token = "Q5JtYUSsKcDOw2qt1WBLFe7pAv+lq1HH1TpkokYfhc5SSk9fUOJxh1ijHkNDq5SKiPjQRsbtrujAG4YyCZK4zecc1a8cfUvJ2bHnDrZ0GBN25DRFJFd7SPJ94CpK7lquMxeGhLAL79jfEpWYh+gPMAdB04t89/1O/w1cDnyilFU=";
$access_token = "DBSY+rfxqfzGO26aJtKFoVPUqimxWuZ7DZaZX0kpltZVZ+sQrNMG+9qb1KjELbqXr/rvJb9LbH+xbUbP7KRMlW0ucrd0vFF4Nz7238rB59CUFKmFLT7Q1dplgAjBf1704K9jA9gLkdFVSscHcKKZqwdB04t89/1O/w1cDnyilFU=";
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = "";
			$msg = $event['message']['text'];
			//$text = "User ID is : ".$event['source']['userId'] ."Group Id : ".$event['source']['groupId'];
			
			
			//print_r($event);
			
			$check_msg = explode(":",$msg);
			
			if( count($check_msg) == 2 ){
				if($check_msg[0] == 'รายงานยกเลิกบิล'){// รายงานยกเลิกบิล:25-09-2019
					$url = 'http://119.59.98.116/report_cancel_bill?date='.$check_msg[1];
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					
					$res = json_decode($result, true);
					
					$text = $res['text'];
					//$text = $url;
				}
			}
			
			if( count($check_msg) == 3 ){
				if($check_msg[0] == 'ยกเลิกบิล' && is_numeric($check_msg[1])){
					//$text = "ยกเลิกบิล ".$check_msg[1]." เรียบร้อยแล้ว";
					
					$url = 'http://119.59.98.116/cancel_bill?id='.$check_msg[1];
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					
					$res = json_decode($result, true);
					
					if($res['status'] == 0){
						$text = "ยกเลิกบิล ".$check_msg[1]." เรียบร้อยแล้ว";
					}elseif($res['status'] == 1){
						$text = "บอลกำลังแข่งขันยกเลิกไม่ได้";
					}elseif($res['status'] == 3){
						$text = "บอลแข่งจบแล้วยกเลิกไม่ได้";
					}
					
				}else{
					$text = "กรอกรูปแบบผิด";
				}
			}else{
				
				if($msg == 'บอท'){
					$text = "ว่าไง";
				}
				if($msg == 'ไอ้บอท'){
					$text = "อะไรหรอครับ";
				}
				
				if($msg == 'บอทขอเลขไอดี'){
					$text = "Group Id : ".$event['source']['groupId'];
				}
				/*
				if($msg == 'เองทำให้ข้าดูแย่'){
					$text = "ขอประทานอภัย";
				}
				*/
			}
			
			if( $text != '' ){
				// Get replyToken
				$replyToken = $event['replyToken'];

				// Build message to reply back
				$messages = [
					'type' => 'text',
					'text' => $text
				];

				// Make a POST Request to Messaging API to reply to sender
				$url = 'https://api.line.me/v2/bot/message/reply';
				$data = [
					'replyToken' => $replyToken,
					'messages' => [$messages],
				];
				$post = json_encode($data);
				$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$result = curl_exec($ch);
				curl_close($ch);

				echo $result . "\r\n";
			}
		}
	}
}

?>
