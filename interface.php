<?php
$wechatObj = new wechat();
$wechatObj->responseMsg();
class wechat {
 public function responseMsg() {
	$postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (!empty($postStr)){
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
  	$fromUsername = $postObj->FromUserName;
  	$toUsername = $postObj->ToUserName;
  	$keyword = trim($postObj->Content);
  	$time = time();
  	$textTpl = "<xml>
  	<ToUserName><![CDATA[%s]]></ToUserName>
  	<FromUserName><![CDATA[%s]]></FromUserName>
  	<CreateTime>%s</CreateTime>
  	<MsgType><![CDATA[%s]]></MsgType>
  	<Content><![CDATA[%s]]></Content>
  	<FuncFlag>0</FuncFlag>
  	</xml>";

	$ev = $postObj->Event;
	if($ev == "suscribe"){
		$msgType = "text";
		$contentStr = "Welcome to our WeChat public platform!";
		$resultStr = sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
		return $resultStr;
	}


  $msgType = "text";
	$contentStr = "Hello " + $fromUsername;
	if ($keyword == "Time" || $keyword == "time")
	{
		$contentStr = date("Y-m-d H:i:s",time());
	}
  	$resultStr = sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
  	echo $resultStr; 
	}
 }
}
?>