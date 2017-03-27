<?php
class AlipayAction extends CommonAction
{
	function __construct() {
		parent::__construct();
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
		$this->order_model = M('tp_xieche.order','xc_');  //预约订单


		
		
	}

	//获取服务窗token值 用户ID
	public function get_token(){
		require 'alipay_fuwuchuang/config.php';
	    $url = $config['gatewayUrl'];
	    $paramsArray = array(
	        'app_id'=>$config ['app_id'],
	        'method'=>'alipay.system.oauth.token',
	        'charset'=>$config ['charset'],
	        'sign_type'=>'RSA',
	        'timestamp'=>date ( 'Y-m-d H:i:s', time () ) ,
	        'version'=>'1.0',
	        'grant_type'=>'authorization_code',
	        'code'=>$_GET['auth_code'],
	        'refresh_token'=>''
	    );

		require_once 'alipay_fuwuchuang/AlipaySign.php';
		$as = new AlipaySign ();
		$sign = $as->sign_request ( $paramsArray, $config ['merchant_private_key_file'] );
		$paramsArray ['sign'] = $sign;
		$data = $this->curl_file_get_contents($url,$paramsArray);
	    $data = json_decode($data,true);
	    return $data;
	}

	//府上养车页面 授权跳转
    public function goto_carservice(){
   		$data = $this->get_token();
   		$openid = $data['alipay_system_oauth_token_response']['alipay_user_id'];
   		$_SESSION['ali_id'] = $openid;
	    header("location:http://www.xieche.com.cn/Mobilecar-carservice?ali_id=".$openid);
	}

	//我的订单 授权跳转
	public function goto_mycarservice(){
   		$data = $this->get_token();
   		$openid = $data['alipay_system_oauth_token_response']['alipay_user_id'];
   		$_SESSION['ali_id'] = $openid;
   		$mAlipay = D('alipay_fuwuchuang');
   		$where['FromUserId'] = $openid;
   		$where['status'] = 2;
   		$res = $mAlipay->where($where)->find();
   		if($res){
   			$_SESSION['uid'] = $res['uid'];
   			header("location:http://www.xieche.com.cn/Mobilecar-mycarservice?ali_id=".$openid);
   		}else{
   			echo '未绑定服务窗';
   		}
	    
	}

	//app_download 授权跳转
	public function goto_appdownload(){
   		$data = $this->get_token();
   		$openid = $data['alipay_system_oauth_token_response']['alipay_user_id'];
	    header("location:http://www.xieche.com.cn/mobile-app_download?ali_id=".$openid);
	}

	//绑定手机 授权跳转
	public function goto_bind(){
		$data = $this->get_token();
   		$openid = $data['alipay_system_oauth_token_response']['alipay_user_id'];
   		$_SESSION['ali_id'] = $openid;
   		$mAlipay = D('alipay_fuwuchuang');
   		$res = $mAlipay->where(array('FromUserId'=>$openid))->find();
   		if($res){
   			if($res['status'] == 2){
   				header("location:http://www.xieche.com.cn/mobile-alipay_bind_cancel?ali_id=".$openid);
   			}else{
   				header("location:http://www.xieche.com.cn/mobile-alipay_bind?ali_id=".$openid);
   			}
   		}
   		
	}

	//我的检测报告 授权跳转
	public function goto_check(){
		$data = $this->get_token();
   		$openid = $data['alipay_system_oauth_token_response']['alipay_user_id'];
   		$_SESSION['ali_id'] = $openid;
   		$mAlipay = D('alipay_fuwuchuang');
   		$res = $mAlipay->where(array('FromUserId'=>$openid))->find();
   		if($res['status'] == 2 && $res['mobile']!=''){
   			$mCheck = D('checkreport_total');
   			$resCheck = $mCheck->where(array('mobile'=>$res['mobile']))->order('create_time desc')->find();
   			if($resCheck){
   				header('location:http://www.xieche.com.cn/mobile/check_report-report_id-'.base64_encode($resCheck['id'].'168'));
   			}else{
   				echo '暂无有效的检测报告';
   			}
   		}else{
   			echo '未绑定手机号';
   		}
		
	}

	//服务窗关注 记录用户id
	public function followAdd(){
		if(!$_REQUEST['FromUserId']){
			$this->error('参数错误');
		}
		$mAlipay = D('alipay_fuwuchuang');
		$where['FromUserId'] = $_REQUEST['FromUserId'];
		$resID = $mAlipay->where($where)->find();
		if($resID){
			if($resID['mobile']!=''){
				$data['status'] = 2;
			}else{
				$data['status'] = 1;
			}
			$data['create_time'] = time();
			$res = $mAlipay->where($where)->save($data);
			return $res;
		}else{
			$data['status'] = 1;
			$data['FromUserId'] = $_REQUEST['FromUserId'];
			$data['create_time'] = time();
			$res = $mAlipay->add($data);
			return $res;
		}


	}	

	//取消关注
	public function unfollow(){
		if(!$_REQUEST['FromUserId']){
			$this->error('参数错误');
		}
		$mAlipay = D('alipay_fuwuchuang');
		$where['FromUserId'] = $_REQUEST['FromUserId'];
		$data['status'] = 0;
		$mAlipay->where($where)->save($data);
	}

	//curl接受页面信息
	public function curl_file_get_contents($durl,$post){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $durl);
	    curl_setopt($ch, CURLOPT_TIMEOUT,40);
	    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt ($ch, CURLOPT_POST, 1 );
	    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post );
	    $r = curl_exec($ch);
	    curl_close($ch);
	    return $r;
	}



}
?>
