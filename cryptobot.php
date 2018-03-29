// Modified by DeEn

<?php
$access_token = 'xxx';
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
			$text = $event['message']['text']; 			// Get text sent
			$replyToken = $event['replyToken'];			// Get replyToken
			$text_ex = explode(' ', $text); 			// เอาข้อความมาแยกอาข้อความมาแยก วรรค ได้เป็น Array
		if($text_ex[0] == "ราคา") { // ตรวจสอบเงื่อนไขจาก event text  ในวรรคแรก
			$str = strtoupper($text_ex[1]); // แปลงวรรคให้เป็นอักษรตัวใหญ่
			$urlx = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?convert=THB'); // ดึงข้อมูล JSON จาก coinmarketcap
			$jsonx = json_decode($urlx);
			foreach($jsonx as $item) { // ค้นหาสกุลเงินตามที่ป้อนมา
				if($item->symbol ==  $str) {
					$iname = $item->name;
					$irank = $item->rank;
					$ibtc = $item->price_btc;
					$ithb = $item->price_thb;
					$iusd = $item->price_usd;
					$symb = $item->symbol;
					$c1 = $item->percent_change_1h;
					$c24 = $item->percent_change_24h;
					$c7 = $item->percent_change_7d;
					break;
					}
				}
			$res_tx = "Coin: ".$iname." (".$symb.")\nPosition: ".$irank."\nPrice:\n≈ ".$ibtc." BTC\n≈ ".number_format($iusd, 2)." USD\n≈ ".number_format($ithb, 2)." THB\n---------\nPercent 1h: ".$c1."%\nPercent 24h: ".$c24."%\nPercent 7d: ".$c7."%" ;
					else if(empty($iusd)){
					$res_tx = 'ไม่พบข้อมูล '.$str.'';		
				}
  			$response_format_text = $res_tx ;
			}
			
			// Build message to reply back
				$messages = [
					'type' => 'text',
					'text' => $response_format_text
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
echo "OK";
