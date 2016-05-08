<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	var $_tokenItem;
	var $_getTokenTime;
	public function _construnt(){

	}

    public function index(){
		
    	$nonce = $_GET['nonce'];
    	$token = 'Webchat16421';
    	$timestamp = $_GET['timestamp'];
    	$echostr = $_GET['echostr'];
    	$signature = $_GET['signature'];
    	$array =array();
    	$array = array($nonce, $timestamp, $token);
    	sort($array);
    	$str = sha1( implode($array) );
    	if( $str == $signature && $echostr){
    		echo $echostr;
    		exit;
    	}else{
    		$this->responseMsg();
    	}
    }

    public function show(){
    	echo 'application';
    }

    public function delete_menu(){

    }

	public function create_menu(){
		$data = '{
					 "button":[
					{	
						"type":"click",
						"name":"Alert",
						"key":"V_ALERT"
					},
					{	
						"type":"click",
						"name":"Message",
						"key":"V_MESSAGE"
					},
					{
						"name":"About Me",
						"sub_button":[
						{	
						   "type":"click",
						   "name":"Register",
						   "key":"V_REGISTER"
						},
						{
						   "type":"click",
						   "name":"Log on",
						   "key":"V_LOGON"
						}]
					}]
				 }';

		$ACC_TOKEN = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;
		$ch = curl_init();
				
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$info = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Errno'.curl_error($ch);
		}
		curl_close($ch);
		var_dump($info);	
	}
	public function getAccessToken(){ 

		// $appid = "wxd80c6f09aa0419af";
		// $appsecret = "8169b507bd3edceb8696a1d115b2467a ";
		
		//TEST ID & SECRET
			$ch = curl_init();
			$appid = "wx87ae063fece3d458";
			$appsecret = "46e06ee52bd66e91edc399a7b277daf2 ";
			$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			$output = curl_exec($ch);
			curl_close($ch);
			$arr = json_decode($output);
			$this->_tokenItem = $arr;
			$this->_getTokenTime = time();
			echo $arr->access_token;
			return 	$arr->access_token;

	}
	
    public function responseMsg(){

    	$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
    	$postObj = simplexml_load_string($postArr);
    	$defaultTemplate = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					</xml>";
		$toUser = $postObj->FromUserName;
    	$fromUser = $postObj->ToUserName;
    	$time = time();
    	if(strtolower($postObj->MsgType) == 'event'){
    		if(strtolower($postObj->Event) == 'subscribe'){   			
    			$msgType = 'text';
    			$content = "Welcome to our wechat platform!";   			
				$info = sprintf($defaultTemplate,$toUser,$fromUser,$time,$msgType,$content);
				echo $info;
    		}else if(strtolower($postObj->Event) == 'click'){   			
    			$msgType = 'text';
    			$eventKey = $postObj->EventKey;
    			switch ($eventKey) {
    				case 'V_ALERT':
    					$content = "Alert!";
    					break;
    				case 'V_MESSAGE':
    					$content = "Message!";
    					break;
    				case 'V_REGISTER':
    					//$content = "Register!";
    					$this->redirect("http://www.baidu.com");
    					break;
    				case 'V_LOGON':
    					$content = "Logon!";
    					break;
    				default:
    					break;
    			}
 			
				$info = sprintf($defaultTemplate,$toUser,$fromUser,$time,$msgType,$content);
				echo $info;
    		}
    	}else if(strtolower($postObj->MsgType == 'text')){
    		switch (strtolower($postObj->Content)) {
    			case 'time':
    				$content =  date("Y-m-d H:i:s",time());
    				$msgType = "text";
    				$info = sprintf($defaultTemplate,$toUser,$fromUser,$time,$msgType,$content);
					echo $info;
    				break;
    			case 'url':
    				$content =  "<a href='http://www.baidu.com'>baidu</a>";
    				$msgType = "text";
    				$info = sprintf($defaultTemplate,$toUser,$fromUser,$time,$msgType,$content);
					echo $info;
    				break;
    			case 'test1':
    				$arr = array(
		    			array(
							'title'=>'title 1',
		    				'description'=>'description 1',
		    				'picUrl'=> 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png',
		    				'url'=>'http://www.baidu.com',
		    			),
		    			array(
							'title'=>'title 2',
		    				'description'=>'description 2',
		    				'picUrl'=> 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png',
		    				'url'=>'http://www.163.com',
		    			),
    				
    				);
    				$template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>".count($arr)."</ArticleCount>
									<Articles>";
					foreach ($arr as $k=>$v) {
						$template .= "<item>
											<Title><![CDATA[".$v['title']."]]></Title> 
											<Description><![CDATA[".$v['description']."]]></Description>
											<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
											<Url><![CDATA[".$v['url']."]]></Url>
										</item>";		
					}
					$template .="			
									</Articles>
								</xml> ";
					echo sprintf($template, $toUser,$fromUser,time(),'news');
					break;
    			default:
    				break;
    		}		
    	}
    }
}