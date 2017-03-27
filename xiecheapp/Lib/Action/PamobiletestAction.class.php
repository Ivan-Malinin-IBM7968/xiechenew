<?php
class WeixinAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		//header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->ShopModel = D('shop');
		$this->OrderModel = D('order');
		$this->CouponModel = D('Coupon');
		$this->FsModel = D('fs');//4S店
		$this->CarseriesModel = D('carseries');//车系表
		$this->CarbandModel = D('carbrand');//品牌绑定
		$this->RegionModel = D('Region');
		$this->MemberModel = D('member');//验证码
		$this->model_sms = D('Sms');//手机短信
		$this->PadataModel = D('Padatatest');//测试接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->ServiceitemModel = D('Serviceitem');
		$this->CommentModel = D('Comment');
		$this->MembercouponModel = D('membercoupon');
		$this->ScanModel = D('scan_data');
		
		$_SESSION['xc_id'] ="";
		//$_SESSION['pa_mobile'] = "";
	}

	function decryptStrin($str,$keys="6461772803150152",$iv="8105547186756005",$cipher_alg=MCRYPT_RIJNDAEL_128){
		$decrypted_string = mcrypt_decrypt($cipher_alg, $keys, pack("H*",$str),MCRYPT_MODE_CBC, $iv);
		return $decrypted_string;
		
	}
	/*
		@author:ysh
		@function:平安传送的加密的信息
		@time:2014/1/22
	*/
	function userCheck() {
		if($_REQUEST['imNo']){
			$data['imNo'] = $this->getdecrypt( $_REQUEST['imNo'] );
			if($_REQUEST['imType']){
				$data['imType'] = $this->getdecrypt( $_REQUEST['imType'] );
			}
			if($_REQUEST['mobileNo']){
				$data['mobileNo'] = $this->getdecrypt( $_REQUEST['mobileNo'] );
			}
			if($_REQUEST['bind']){
				$data['bind'] = $this->getdecrypt( $_REQUEST['bind'] );
			}
			if($_REQUEST['coordinate']){
				$data['coordinate'] = $this->getdecrypt($_REQUEST['coordinate'] );
					if($data['coordinate']) {
					$coordinate = explode("|",$data['coordinate']);
					$data['Latitude'] = $coordinate[0];
					$data['Longitude'] = $coordinate[1];
				}
			}
			
			if($_REQUEST['models']) {
				$data['models'] = $this->getdecrypt( $_REQUEST['models'] );
			}
			$data['create_time'] = time();
			

			$map['FromUserName'] = $data['imNo'];

			$FromUserName = $this->PadataModel->where($map)->order("create_time DESC")->find();
			
			if(!$FromUserName) {
				$data['FromUserName'] = $data['imNo'];
				$FromUserName['id'] = $this->PadataModel->add($data);
			}else{
				$arr = array();
				$arr['Latitude'] = $data['Latitude'];
				$arr['Longitude'] = $data['Longitude'];
				$this->PadataModel->where(array('FromUserName'=>$data['imNo']))->save($arr);
			}
		}
		$url = "http://www.xieche.com.cn/Pamobile-index-order-commment-lat-{$FromUserName[Latitude]}-long-{$FromUserName[Longitude]}-pa_id-{$FromUserName[id]}";
		header("Location:".$url); 
		
	}
	/*
		@author:ysh
		@function:平安解码
		@time:2014/2/14
	*/
	function getdecrypt($encText){
		import("ORG.Util.MyAes");
		$keyStr = 'UIMN85UXUQC436IM'; //平安给的密钥
		//$plainText = 'this is a string will be AES_Encrypt';  原文
		//$plainText =  iconv('UTF-8', 'GBK',$plainText) ;
		$aes = new MyAes();
		$aes->set_key($keyStr);
		$aes->require_pkcs5();
		//$encText = $aes->encrypt($plainText);//加密
		$decString = $aes->decrypt($encText);//解密
		//$decString = iconv('GBK', 'UTF-8',$decString);//平安给我们的是GBK的 我们要转成UTF8
		return $decString;
	}


	/*验证token微信测试
		http://www.xieche.com.cn/index.php/App/wx_checkSignature
		id: zhangzc@xieche.net
		pw: Chexie!@#@@#
	*/
	function wx_checkSignature(){
		$echoStr = $_GET["echostr"];
        //valid signature , option
		$token = "xieche";
			
        if($this->checkSignature($token)){
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$res = (array) $postObj;
			//获取微信用户基本信息
			$weixin_info = $this->get_weixininfo($res['FromUserName']);
			$map['FromUserName'] = $res['FromUserName'];
			$map['type'] = '2';
			
			//关注后发送信息开始
			if($res['Event'] == 'subscribe') {
				$fromUsername = $res['FromUserName'];
				$toUsername =$res['ToUserName'];;
				$keyword = trim($postObj->Content);
				$time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
                            <item>
                            <Title><![CDATA[汽车保养，你哪也不用去！——【府上养车】]]></Title> 
                            <Description><![CDATA[有携车网【府上养车】，车在哪，保养就到哪！专业技师召之即来，直达府上。您可以足不出户，半小时轻松完成爱车保养。]]></Description>
                            <PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/c0HsF7TLnqBeufbGDX5DYkjZyibSWJF166f6wu7LAVx9EqXDa4BbH6XtDuPIwp6zNFueqyBcpb6ZR5lkhogYBHQ/640]]></PicUrl>
                            <Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzA5NDA1NjcyNw==&mid=200682015&idx=1&sn=c295a928f18868fa325e6e38980ecb13#rd]]></Url>
                            </item>
                            </Articles>
							</xml>";
							
				$msgType = "news";
				
				if ( mb_strpos($res['EventKey'],'qrscene_') !==false ) {
					$res['EventKey'] = str_replace('qrscene_','',$res['EventKey']);
					//当扫码场景ID大于100的时候，进入加油扫码流程，否则进入技师扫码流程
					if($res['EventKey']<100){
						$map['FromUserName'] = $res['FromUserName'];
						$map['type'] = '2';
						$FromUserName = $this->PadataModel->where($map)->count();
						if($FromUserName=='0') {
							$arr = array();
							$arr['FromUserName'] = $res['FromUserName'];
							$arr['Latitude'] = $res['Latitude'];
							$arr['Longitude'] = $res['Longitude'];
							$arr['create_time'] = time();
							$arr['type'] = '2';
							//补充微信用户基本信息
							if($weixin_info['nickname']){
								$arr['nickname'] = $weixin_info['nickname'];
							}
							if($weixin_info['sex']){
								$arr['sex'] = $weixin_info['sex'];
							}
							if($weixin_info['city']){
								$arr['city'] = $weixin_info['city'];
							}
							if($weixin_info['province']){
								$arr['province'] = $weixin_info['province'];
							}
							if($weixin_info['country']){
								$arr['country'] = $weixin_info['country'];
							}
							$this->PadataModel->add($arr);
						}else {
							$arr = array();
							$arr['Latitude'] = $res['Latitude'];
							$arr['Longitude'] = $res['Longitude'];
							$arr['type'] = '2';
							//补充微信用户基本信息
							if($weixin_info['nickname']){
								$arr['nickname'] = $weixin_info['nickname'];
							}
							if($weixin_info['sex']){
								$arr['sex'] = $weixin_info['sex'];
							}
							if($weixin_info['city']){
								$arr['city'] = $weixin_info['city'];
							}
							if($weixin_info['province']){
								$arr['province'] = $weixin_info['province'];
							}
							if($weixin_info['country']){
								$arr['country'] = $weixin_info['country'];
							}
							$this->PadataModel->where(array('FromUserName'=>$res['FromUserName']))->save($arr);
						}
						$scan_map['scene_id'] = $res['EventKey'];
						$scan_map['FromUserName'] = $res['FromUserName'];
						$scan_map['create_time'] = time();
						$this->ScanModel->add($scan_map);
					}else{
						$this->addCodeLog('addoil', 'nologin start');
						//扫码加油流程
						$getOilName = $this->oil_scanprocess($res['FromUserName'],$res['EventKey']);
						$this->submitCodeLog('addoil');
						$sendOilName = $weixin_info['nickname'];
						if ($getOilName['status']) {
							$sendMsg = "携车网很高兴遇见你！\n\n" ;
							$sendMsg .="\n";
							$sendMsg .= "由于你的关注，你的朋友{$getOilName['nickname']}刚刚获得了100ml机油，很快他就能拿到由携车网免费送出的一桶壳牌机油或者千元油卡了！\n\n" ;
							$sendMsg .="\n";
							$sendMsg .= "你也一起来玩吧，点击下方【给我加油】，生成您的专属加油图片，将图片发给朋友，群、朋友圈…让你的朋友为你加油。\n\n" ;
							$sendMsg .="\n";
							$sendMsg .= "根据加油量的不同，你就可免费兑换快的打车券、携车网车辆上门保养、蓝壳机油甚至是千元油卡。\n";
							//扫描成功以后
							$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[{$sendOilName}，终于等到你，还好我没放弃！]]></Title>
							<Description><![CDATA[{$sendMsg}]]></Description>
							<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzA5NDA1NjcyNw==&mid=200682015&idx=1&sn=c295a928f18868fa325e6e38980ecb13#rd]]></Url>
							</item>
							</Articles>
							</xml>";
							//发测试链接
							$testdata['open_id'] = $res['FromUserName'];
							$testpa_id = str_replace('-', '**', $res['FromUserName']);
							$testdata['content'] = $weixin_info['nickname'].'<a href=\'http://www.xieche.com.cn/mobilecar/get_spreadpic-pa_id-'.$testpa_id.'\'>点击生成我的测试图片</a>';
							$this->send_weixintext($testdata);
						}
					}
				}

				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;

			}
			
			$FromUserName = $this->PadataModel->where($map)->order("create_time DESC")->find();
			if(!$FromUserName){
				$arr =array();
				if($res['Latitude'] || $res['Longitude']) {
					$arr['Latitude'] = $res['Latitude'];
					$arr['Longitude'] = $res['Longitude'];
				}
				$arr['FromUserName'] = $res['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				//补充微信用户基本信息
				if($weixin_info['nickname']){
					$arr['nickname'] = $weixin_info['nickname'];
				}
				if($weixin_info['sex']){
					$arr['sex'] = $weixin_info['sex'];
				}
				if($weixin_info['city']){
					$arr['city'] = $weixin_info['city'];
				}
				if($weixin_info['province']){
					$arr['province'] = $weixin_info['province'];
				}
				if($weixin_info['country']){
					$arr['country'] = $weixin_info['country'];
				}
				$FromUserName['id'] = $this->PadataModel->add($arr);
				
			}
			
			//新关注送100ml机油
			$mMyoil = D('spreadmyoil');
			$isAdd = $mMyoil->where( array('weixin_id'=>$res['FromUserName']))->count();
			if(!$isAdd){
				$insertOilData = array(
						'weixin_id'=>$res['FromUserName'],
						'oil_num'=>100,
						'create_time'=>time()
				);
				$mMyoil->add($insertOilData);
			}
			
			if($res['MsgType'] == 'event') {
				/*获取地理位置*/
				if($res['Event'] == 'LOCATION') {
					$map['FromUserName'] = $res['FromUserName'];
					$map['type'] = '2';
					$FromUserName = $this->PadataModel->where($map)->count();
					if($FromUserName=='0') {
						$arr = array();
						$arr['FromUserName'] = $res['FromUserName'];
						$arr['Latitude'] = $res['Latitude'];
						$arr['Longitude'] = $res['Longitude'];
						$arr['create_time'] = time();
						$arr['type'] = '2';
						//补充微信用户基本信息
						if($weixin_info['nickname']){
							$arr['nickname'] = $weixin_info['nickname'];
						}
						if($weixin_info['sex']){
							$arr['sex'] = $weixin_info['sex'];
						}
						if($weixin_info['city']){
							$arr['city'] = $weixin_info['city'];
						}
						if($weixin_info['province']){
							$arr['province'] = $weixin_info['province'];
						}
						if($weixin_info['country']){
							$arr['country'] = $weixin_info['country'];
						}
						$this->PadataModel->add($arr);
					}else {
						$arr = array();
						$arr['Latitude'] = $res['Latitude'];
						$arr['Longitude'] = $res['Longitude'];
						$arr['type'] = '2';
						//补充微信用户基本信息
						if($weixin_info['nickname']){
							$arr['nickname'] = $weixin_info['nickname'];
						}
						if($weixin_info['sex']){
							$arr['sex'] = $weixin_info['sex'];
						}
						if($weixin_info['city']){
							$arr['city'] = $weixin_info['city'];
						}
						if($weixin_info['province']){
							$arr['province'] = $weixin_info['province'];
						}
						if($weixin_info['country']){
							$arr['country'] = $weixin_info['country'];
						}
						$this->PadataModel->where(array('FromUserName'=>$res['FromUserName']))->save($arr);
					}
				}
				$getOilName = '';
				/*扫码获取渠道数据*/
				if($res['Event'] == 'SCAN') {
					//当扫码场景ID大于100的时候，进入加油扫码流程，否则进入技师扫码流程
					if($res['EventKey']<100){
						$map['FromUserName'] = $res['FromUserName'];
						$map['type'] = '2';
						$FromUserName = $this->PadataModel->where($map)->count();
						if($FromUserName=='0') {
							$arr = array();
							$arr['FromUserName'] = $res['FromUserName'];
							$arr['Latitude'] = $res['Latitude'];
							$arr['Longitude'] = $res['Longitude'];
							$arr['create_time'] = time();
							$arr['type'] = '2';
							//补充微信用户基本信息
							if($weixin_info['nickname']){
								$arr['nickname'] = $weixin_info['nickname'];
							}
							if($weixin_info['sex']){
								$arr['sex'] = $weixin_info['sex'];
							}
							if($weixin_info['city']){
								$arr['city'] = $weixin_info['city'];
							}
							if($weixin_info['province']){
								$arr['province'] = $weixin_info['province'];
							}
							if($weixin_info['country']){
								$arr['country'] = $weixin_info['country'];
							}
							$this->PadataModel->add($arr);
						}else {
							$arr = array();
							$arr['Latitude'] = $res['Latitude'];
							$arr['Longitude'] = $res['Longitude'];
							$arr['type'] = '2';
							//补充微信用户基本信息
							if($weixin_info['nickname']){
								$arr['nickname'] = $weixin_info['nickname'];
							}
							if($weixin_info['sex']){
								$arr['sex'] = $weixin_info['sex'];
							}
							if($weixin_info['city']){
								$arr['city'] = $weixin_info['city'];
							}
							if($weixin_info['province']){
								$arr['province'] = $weixin_info['province'];
							}
							if($weixin_info['country']){
								$arr['country'] = $weixin_info['country'];
							}
							$this->PadataModel->where(array('FromUserName'=>$res['FromUserName']))->save($arr);
						}
						$scan_map['scene_id'] = $res['EventKey'];
						$scan_map['FromUserName'] = $res['FromUserName'];
						$scan_map['create_time'] = time();
						$this->ScanModel->add($scan_map);
					}else{
						$this->addCodeLog('addoil', 'already login start');
						
						$getOilName = $this->oil_scanprocess($res['FromUserName'],$res['EventKey']);
						
						$this->submitCodeLog('addoil');
						//扫码加油流程
					}
					
					$sendOilName = $weixin_info['nickname'];
					$fromUsername = $res['FromUserName'];
					$toUsername =$res['ToUserName'];
					$time = time();
					
					if ($getOilName['status']) {
						if ( $getOilName['status'] == 1 ) {
							//扫描成功以后
							$sendMsg = "携车网很高兴遇见你！\n" ;
							$sendMsg .="\n";
							$sendMsg .= "由于你的关注，你的朋友{$getOilName['nickname']}刚刚获得了100ml机油，很快他就能拿到由携车网免费送出的一桶壳牌机油或者千元油卡了！\n" ;
							$sendMsg .="\n";
							$sendMsg .= "你也一起来玩吧，点击下方【给我加油】，生成您的专属加油图片，将图片发给朋友，群、朋友圈…让你的朋友为你加油。\n" ;
							$sendMsg .="\n";
							$sendMsg .= "根据加油量的不同，你就可免费兑换快的打车券、携车网车辆上门保养、蓝壳机油甚至是千元油卡。\n";
							$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[{$sendOilName}，终于等到你，还好我没放弃！]]></Title>
							<Description><![CDATA[{$sendMsg}]]></Description>
							<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzA5NDA1NjcyNw==&mid=200682015&idx=1&sn=c295a928f18868fa325e6e38980ecb13#rd]]></Url>
							</item>
							</Articles>
							</xml>";
							$msgType = "news";
							//发测试链接
							$testdata['open_id'] = $res['FromUserName'];
							$testpa_id = str_replace('-', '**', $res['FromUserName']);
							$testdata['content'] = $weixin_info['nickname'].'<a href=\'http://www.xieche.com.cn/mobilecar-get_spreadpic-pa_id-'.$testpa_id.'\'>点击生成我的测试图片</a>';
							$this->send_weixintext($testdata);
						}elseif ( $getOilName['status'] == 2 ){
						//已经帮别人加过了
						$textTpl = "<xml>
						 <ToUserName><![CDATA[%s]]></ToUserName>
						 <FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						 <MsgType><![CDATA[text]]></MsgType>
						 <Content><![CDATA[对不起{$sendOilName}，你已经为{$getOilName['nickname']}加过油了，不能三心二意再为别人加油了，要专一哦！]]></Content>
						</xml>";
						}elseif ( $getOilName['status'] == 3 ){
						//不能给自己加油
						$textTpl = "<xml>
						 <ToUserName><![CDATA[%s]]></ToUserName>
						 <FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						 <MsgType><![CDATA[text]]></MsgType>
						 <Content><![CDATA[对不起{$sendOilName}，你已经关注过携车网咯，你不能自己给自己加油哦，所以拿着自己的油桶等人加满就好啦！]]></Content>
						</xml>";
						}
					}else{
						//不符合加油条件
						$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[{$sendOilName}，你已经关注过携车网咯，所以拿着自己的油桶等人加满就好，就别再往{$getOilName['nickname']}的油桶里倒了！]]></Content>
						</xml>";
					}
					
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;

				} 
				
				/*获取地理位置*/
				if($res['Event'] == 'CLICK') {
					//跳转去SHOP_LIST
					if($res['EventKey'] == 'WX_INDEX') {
						$now = time();
			
						$Url = "http://www.xieche.com.cn/Mobile-index-order-distance-lat-{$FromUserName[Latitude]}-long-{$FromUserName[Longitude]}-pa_id-{$FromUserName[id]}";
						echo    
							"<xml>
							<ToUserName><![CDATA[{$res[FromUserName]}]]></ToUserName>
							<FromUserName><![CDATA[{$res[ToUserName]}]]></FromUserName>
							<CreateTime>{$now}</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[携车网主页]]></Title> 
							<Description><![CDATA[{$res[FromUserName]}  {$sql}]]></Description>
							<PicUrl><![CDATA[http://www.evdays.com/uploads/allimg/120627/3255_1631365451.jpg]]></PicUrl>
							<Url><![CDATA[{$Url}]]></Url>
							</item>
							</Articles>
							</xml> 
							";		
						exit();
					}

					if($res['EventKey'] == 'V1001_BAD') {
						$now = time();
						$Url = "http://www.xieche.com.cn/Mobile-coupon_list-order-distance1-lat-{$FromUserName[Latitude]}-long-{$FromUserName[Longitude]}-pa_id-{$FromUserName[id]}";
						echo    
							"<xml>
							<ToUserName><![CDATA[{$res[FromUserName]}]]></ToUserName>
							<FromUserName><![CDATA[{$res[ToUserName]}]]></FromUserName>
							<CreateTime>{$now}</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[携车网优惠券]]></Title> 
							<Description><![CDATA[{$res[FromUserName]}  {$sql}]]></Description>
							<PicUrl><![CDATA[picurl]]></PicUrl>
							<Url><![CDATA[{$Url}]]></Url>
							</item>
							</Articles>
							</xml> 
							";
						exit();
					}
					//跳入用户中心
					if($res['EventKey'] == 'V1001_TODAY_MUSIC'){
						$now = time();
						$Url = "http://www.xieche.com.cn/Mobile-my_account-order-distance-lat-{$FromUserName[Latitude]}-long-{$FromUserName[Longitude]}-pa_id-{$FromUserName[id]}";
						echo "<xml>
							<ToUserName><![CDATA[{$res[FromUserName]}]]></ToUserName>
							<FromUserName><![CDATA[{$res[ToUserName]}]]></FromUserName>
							<CreateTime>{$now}</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[用户中心]]></Title> 
							<Description><![CDATA[用户中心]]></Description>
							<PicUrl><![CDATA[picurl]]></PicUrl>
							<Url><![CDATA[{$Url}]]></Url>
							</item>
							</Articles>
							</xml> 
						";exit();
					}


					//跳入优惠券页
					if($res['EventKey'] == 'V1001_TODAY_SINGER'){
						$now = time();
						$Url = "http://www.xieche.com.cn/Mobile-my_coupon-order-distance-lat-{$FromUserName[Latitude]}-long-{$FromUserName[Longitude]}-pa_id-{$FromUserName[id]}";
						echo "<xml>
							<ToUserName><![CDATA[{$res[FromUserName]}]]></ToUserName>
							<FromUserName><![CDATA[{$res[ToUserName]}]]></FromUserName>
							<CreateTime>{$now}</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[已购买的优惠券]]></Title> 
							<Description><![CDATA[已购买的优惠券]]></Description>
							<PicUrl><![CDATA[picurl]]></PicUrl>
							<Url><![CDATA[{$Url}]]></Url>
							</item>
							</Articles>
							</xml>";exit();
					}

				}
			}




			//语音转发转发给xxx
			if($res['MsgType'] == 'voice') {
					$now = time();
					//$data['touser'] = "oF49ruJukiRNno_6NJ4CEY6waiN4";
					//$data['msgtype'] = "voice";
					//$data['media_id'] = $res['MediaId'];
					//$this->weixin_api($data);
					$access_token = $this->get_weixinkey();

					$mediaid = $res['MediaId'];
					$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$mediaid}";
					$fileInfo = $this->downloadWeixinFile($url);
					$filename = "down_voice.mp3";
					$this->saveWeixinFile($filename, $fileInfo["body"]);
					
					$test = D('test');
					$test->data(array('imNo'=>$access_token,'mobile'=>$mediaid))->add();
					exit();
			}
			
			/*
			//文本转发给xxx
			if($res['MsgType'] == 'text') {
					$now = time();
					$data['touser'] = "oF49ruJukiRNno_6NJ4CEY6waiN4";
					$data['title'] = "转发转发转发";
					$data['description'] = $res['Content'];
					$this->weixin_api($data);
					exit();
			}
			
			
			//语音转发转发给xxx
			if($res['MsgType'] == 'image') {
					$now = time();
					//$data['touser'] = "oF49ruJukiRNno_6NJ4CEY6waiN4";
					$data['touser'] = "oF49ruA-hSfXSnOrJYqiut1HK8HI";
					$data['msgtype'] = "image";
					$data['media_id'] = $res['MediaId'];
					$this->weixin_api($data);
					exit();
			}
			*/
		
        }else{
			echo "token 错误";
        }
	}


	
	//微信测试
	private function checkSignature($token)
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		//$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}


	function get_access_token() {
		
		$result = $this->getWeixinToken();
		
// 		$appid = "wx43430f4b6f59ed33";
// 		$secret = "e5f5c13709aa0ae7dad85865768855d6";
// 		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
// 		$result = file_get_contents($url);
// 		$result = json_decode($result,true);
		//$SESSION['access_token'] = $result['access_token'];
		print_r($result);
	}



	function create_menu() {
		$access_token = "VRrMAUaH3KOPFfJYM6g9ZmBsStSwUdgjw37irV3lUJTNFeEY6wVa64atn88T_lPKub2yc0fN56e8E37Roc40xGArALcZKbyQZlJ5BtqENmL7X4gmsh0sj-BhCkCViSH7rWvQx3SkTrN927GHNoTLcA";
		exit();
	}


	//主页
	function get_OpenID() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-get_OpenID2");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	//府上养车
	function get_OpenIDCarservice() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_carservice");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	//府上养车服务视频
	function get_OpenIDMovie() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_movie");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	//问
	function get_OpenIDAsk() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_ask");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	//我的订单
	function get_OpenIDmycarservice() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_mycarservice");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	//4S店预约订单
	function get_OpenIDorder_list() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_order_list");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//事故车订单
	function get_OpenIDbidorder_list() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_bidorder_list");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//我的优惠券
	function get_OpenIDomy_coupon() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_my_coupon");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//我的检测报告
	function get_OpenIDomy_check() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_my_check");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//优惠速递
	function get_OpenIDocoupon_list() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_coupon_list");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	
	function login_list() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-login_verify");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}

	function login_list1() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_test");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//事故车返利
	function get_sgc() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_Accident");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}


	
	
	//养车三折团
	function get_meinv() {
		$redirect_uri = urlencode("http://www.xieche.com.cn/Weixin-goto_quiz");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx43430f4b6f59ed33&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		echo $url;
	}
	//下载手机客户端
	//http://www.xieche.com.cn/Mobile-app_download

	/*
		验证跳转去账号绑定页面
	*/
	function login_verify() {
		$code = $_GET['code'];
		if($_GET['state']!= 'STATE') $state = 1;
		
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		
		$result = $this->curl_file_get_contents($url);
		
		$time = $time_end - $time_start;
		$result = json_decode($result,true);
		$aa = D('test');
		//$da['imNo'] =  $result['openid'];
		//$aa->data($da)->add();
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$FromUserName['FromUserName'],'type'=>'2','status'=>'2'))->count();
		if($Paweixin>0){
			$str = 'cancel_verify';
		}else{	
			$str = 'login_verify';
		}
		
		header("location:http://www.xieche.com.cn/Mobile-{$str}-pa_id-{$FromUserName[id]}-ic-{$state}");
	}
	


	
	/*
		验证跳转去WAP预约维修保养页面
	*/
	function get_OpenID2() {
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$code = $_GET['code'];
		$state = $_GET['state'];
		
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		
		$result = json_decode($result,true);
		
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
		if(!$FromUserName) {
			$arr['FromUserName'] = $wx['FromUserName'];
			$arr['create_time'] = time();
			$arr['type'] = '2';
			$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->get_OpenID2();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-index-order-commment-pa_id-{$FromUserName[id]}");	
		}
		
	}

	//curl接受页面信息
	function curl_file_get_contents($durl){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $durl);
		curl_setopt($ch, CURLOPT_TIMEOUT,40);
		curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
    	curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}

	/*
		验证跳转去WAP我的主页
	*/
	function goto_order_list() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-order_list-pa_id-{$FromUserName[id]}");
		}
	}

	/*
		验证跳转去WAP我的府上养车和事故车
	*/
	function goto_mycarservice() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			header("location:http://www.xieche.com.cn/Mobilecar-mycarservice-pa_id-{$FromUserName[id]}");
		}
	}

	/*
		验证跳转去WAP府上养车
	*/
	function goto_movie() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			header("location:http://www.xieche.com.cn/movie");
		}
	}

	/*
		验证跳转去问
	*/
	function goto_ask() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			//header("location:http://mp.weixin.qq.com/s?__biz=MzA5NDA1NjcyNw==&mid=200513350&idx=1&sn=931888106dda417590a8cd06a34868ef&scene=4#wechat_redirect");
			header("location:http://mp.weixin.qq.com/s?__biz=MzA5NDA1NjcyNw==&mid=200546957&idx=1&sn=318a06fc66ab5a702a012675346783bb#rd");
		}
	}

	/*
		验证跳转去WAP府上养车
	*/
	function goto_carservice() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$FromUserName['FromUserName'],'type'=>'2','status'=>'2'))->find();
			if ($Paweixin) {
				$_SESSION['mobile'] = $Paweixin['mobile'];
			}
			$_SESSION['pa_id'] = $FromUserName[id];
			header("location:http://www.xieche.com.cn/Mobilecar-carservice");
		}
	}

	/*
		验证跳转去WAP事故车页
	*/
	function goto_bidorder_list() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_order_list();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-bidorder_list-pa_id-{$FromUserName[id]}");
		}
	}


	/*
		验证跳转去WAP我的优惠券页
	*/
	function goto_my_coupon() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_my_coupon();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-my_coupon-pa_id-{$FromUserName[id]}");	
		}
	}

	/*
		验证跳转去WAP我的检测报告
	*/
	function goto_my_check() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_my_coupon();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-check_report-pa_id-{$FromUserName[id]}");	
		}
	}


	/*
		验证跳转去WAP优惠券页
	*/
	function goto_coupon_list() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";

		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_coupon_list();
		}else{
			header("location:http://www.xieche.com.cn/Mobile-coupon_list-pa_id-{$FromUserName[id]}");
		}
	}


	/*
		验证跳转去50元领券页
	*/
	function goto_test() {
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_test();
		}else{
			header("location:http://www.xieche.com.cn/Weixinlottery-index-pa_id-{$FromUserName[id]}");	
		}
		
	}


	/*
		验证跳转去事故车WAP
	*/
	function goto_Accident(){
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_Accident();
		}else{
			header("location:http://www.xieche.com.cn/Accident-index-pa_id-{$FromUserName[id]}");	
		}
	}


	/*
		验证跳转去事故车WAP
	*/
	function goto_Explosion(){
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_Explosion();
		}else{
			header("location:http://www.xieche.com.cn/explosion-index-pa_id-{$FromUserName[id]}");	
		}
	}
	


	/*
		验证跳转去爆款WAP
	*/
	function goto_quiz(){
		$code = $_GET['code'];
		$state = $_GET['state'];
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		$result = $this->curl_file_get_contents($url);
		$result = json_decode($result,true);
		$wx['FromUserName'] = $result['openid'];
		$wx['type'] = '2';
		$FromUserName = $this->PadataModel->where($wx)->order("create_time DESC")->find();
			if(!$FromUserName) {
				$arr['FromUserName'] = $wx['FromUserName'];
				$arr['create_time'] = time();
				$arr['type'] = '2';
				$FromUserName['id'] = $this->PadataModel->add($arr);
		}
		if(!$FromUserName['id']){
			//$this->goto_test();
		}else{
			header("location:http://www.xieche.com.cn/Explosion-index-pa_id-{$FromUserName[id]}");	
		}
	}

	//校验扫码
	function validIsNeedSend($weixin_id,$r_weixin_id){
		$myOilModel = D('spreadoil');
		$where = array(	
			'r_weixin_id' => $r_weixin_id	//已经帮别人加过油了
		);
		
		
		$count = $myOilModel->where($where)->count();
		if ($count) {
			return 1;	//已经帮别人加过油了,不能再加
		}else{
			$where2 = array(
					'weixin_id' => $r_weixin_id, //防止互相加油
					'r_weixin_id' =>$weixin_id
			);
			$count = $myOilModel->where($where2)->count();
			if ($count){
				return 2; 	//不能互相加油
			}else{
				return 3;	//可以加油
			}
		}
	}
	
	//油桶扫码流程
	function oil_scanprocess($open_id,$pic_id){
		$this->addCodeLog('addoil', 1);
		//获取扫码用户信息
		$weixin_info = $this->get_weixininfo($open_id);
		
		if ($weixin_info) {
			
			$face_url = "UPLOADS/spread/cface/{$open_id}.jpg";//cface文件夹存加油人头像图片
			if(!file_exists($face_url)){	//油头像了就不抓了
				//头像存到本地
				import("ORG.Net.Http");
				Http::curlDownload(substr($weixin_info['headimgurl'],0,-1).'132', $face_url);
			}
			$this->addCodeLog('addoil', 2);
			//获取推荐人信息
			$spreadpic_Model = D('spreadpic');
			$spreadoil_Model = D('spreadoil');
			$info = $spreadpic_Model->where(array('id'=>$pic_id))->find();
			$this->addCodeLog('addoil', 3);
			
			if ($info['FromUserName'] == $open_id) {	//自己不能帮自己加油
				return array('status'=>3);
			}

			//校验是否符合加油标准
			$scancheck_res = $this->validIsNeedSend($info['FromUserName'],$open_id);
			if($scancheck_res == 3){ //可以加油
				//帮加油100ml
				$this->addCodeLog('addoil', 5);
				$data['weixin_id'] = $info['FromUserName'];
				$data['r_weixin_id'] = $open_id;
				$data['nickname'] = $weixin_info['nickname'];
				$data['face'] = $face_url;
				$data['oil_num'] = 100;
				$data['create_time'] = time();
				$res = $spreadoil_Model->add($data);
			
				//推消息给推荐人
				if($res){
					$this->addCodeLog('addoil', 6);
					
					//给油桶里面添加100ml数据 bright
					$mSpreadmyoil = D('spreadmyoil');
					$mSpreadmyoil->where(array('weixin_id'=>$info['FromUserName']))->setInc('oil_num',100);
					
					$data['open_id'] = $info['FromUserName'];
					$pa_id = str_replace('-', '**', $info['FromUserName']);
					$data['content'] = '啊哈～'.$weixin_info['nickname'].'往你的油桶加了100毫升油！<a href=\'http://www.xieche.com.cn/mobilecar-newoil-wx-'.$pa_id.'\'>看看谁为我加过油</a>';
					$ret = $this->send_weixintext($data);
					$this->addCodeLog('addoil', '7');
					return array('status'=>1,'nickname'=>$info['nickname']);
				}
				
			}elseif($scancheck_res == 1){	//已经帮别人加过油
				//获取当前扫码open_id的历史推荐人
				$myOilModel = D('spreadoil');
				$where = array(
						'r_weixin_id' => $open_id
				);
				$count = $myOilModel->where($where)->find();
				$repeat_info = $this->get_weixininfo($count['weixin_id']);
				return array('status'=>2,'nickname'=>$repeat_info['nickname']);
			}else{
				return array('status'=>0,'nickname'=>$info['nickname']);//不能互相加油,不符合加油条件
			}
		}
		return array('status'=>0,'nickname'=>$info['nickname']);
	}
	
}