<?php

namespace Xiecheapp\Controller;

use Think\Controller;

class TestController extends Controller {
	
	public function testBug(){
		
		$url = 'http://www.fqcd.3322.org/api.php';
		$post = array(
				'code'=> 'fqcd123223',
				'mobile'=> 13111111111,
				'name'=>iconv("utf-8","gb2312//IGNORE",'test'),
				'remark'=>iconv("utf-8","gb2312//IGNORE",'test'),
				'staff_id'=>'2014102900000025',
				'task'=>2
		);
		$return = $this->curl_test($url,$post);
		var_dump($return);
	}
	public function curl_test($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:192.168.1.1, CLIENT-IP:192.168.1.1'));  //构造IP
		curl_setopt($ch, CURLOPT_REFERER, 'http://www.sina.com.cn/');   //构造来路
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "999999999");
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		//curl_setopt($ch, CURLOPT_PROXY, $wk_ip[$jyi]);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 3);
		$out = curl_exec($ch);
		curl_close($ch);
		return $out;
	}
	
	public function testSendMsg() {
		$url = 'http://114.80.208.222:8080/NOSmsPlatform/server/SMServer.htm';
		$header = array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8");
		$param = http_build_query(array(
				'account'=>'csmlxx',
				'password'=>'888333',
// 				'destmobile'=>'18149767591;13621912356;13061697017',
				'destmobile'=>'15201924396',
				'msgText'=>'【携车网-府上养车】你很忙，我们知道，99元上门为您保养爱车，4S品质，省钱省事。另有4S店预约折扣、车辆维修返利等，好多便宜。400-660-2822，服务工号：001 徐伟忠',
				'types'=>'send'
		)
		);
		
		$res = $this->_curl($url,$param,$header);
		var_dump($res);
	}
	function test2(){
		$url = 'http://www.fqcd.3322.org/api.php';
		$test = 'test';
		$post = array(
				'task'=>4,
				'code'=> 'fqcd123223',
				'username'=>$test,
				'chepai'=>$test,
				'address'=>$test,
				'order_time'=>'2014-12-01 12:00:00',
				'create_time'=>'2014-12-01 12:00:00',
				'remark'=>$test,
				'admin'=>$test,
				'mobile'=>13111111111
		);
		$data = $this->_curl($url,$post);
		var_dump($data);		
	}
	
	function test(){
		$url = 'http://www.fqcd.3322.org/api.php';
		$post = array(
				'task'=>3,
				'code'=> 'fqcd123223',
				'call'=>1520192439
		);
		$data = $this->_curl($url,$post);
		$return= json_decode($data,true);
		if ($return){
			$string = '<table>';
			foreach ($return as  $key=>$val){
				$string .='<tr><td>'.$key.':</td>';
				foreach ($val as $td){
					$string .= '<td>'.$td.'</td>';
				}
				$string .='</tr>';
			}
			$string .= '</tr></table>';
			echo $string;
		}
	}
	
	function insertDataForCall(){
		//昨天车主下的订单已经被客服确认，且服务时间是明天及以后的
		$reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
		$technician_model = M('tp_xieche.technician', 'xc_');  //技师表
		$model_operatelog =  M("tp_xieche.operatelog","xc_");
	
	
		$today = strtotime( date('Y-m-d',time() ));
		$yesterday = $today - 3600*24;
		$tomorrow = $tomorrow + 3600*24;
	
		$staff = array(
				223 => '2014102900000025',//王俊炜
				171 => '2014102900000027',//彭晓文
				182 => '2014102900000029',//张丹红
				241 => '2014102900000031',//朱笑龙
				234 => '2014102900000033',//张美婷
				243 => '2014102900000035',//黄美琴
				242 => '2014102900000037',//李宝峰
				251 => '2014102900000041',//庄玉成
		);
	
		$where['create_time'] = array(array('egt',$yesterday),array('elt',$today)) ;
		$where['order_time'] = array(array('egt',$tomorrow)) ;
		$where['status']  = array('lt',8);
		$datas = $reservation_order_model->where($where)->select();
		$return = array();
		if($datas){
			$url = 'http://www.fqcd.3322.org/api.php';
			foreach ($datas as $data){
				$technician_id = $data['technician_id'];
				$remark = '订单号:'.$data['id'].'车牌:'.$data['licenseplate'].'地址:'.$data['address'].'上门时间:'.date('Y-m-d-H:i:s', $data['order_time']);
				//技师
				if ($technician_id) {
					$condition['id'] = $technician_id;
					$technician_info = $technician_model->where($condition)->find();
					$remark .= '技师姓名:'.$technician_info['truename'];
				}
				//客服信息
				if ($data['admin_id']) {
					$operate_id = $data['admin_id'];
				}else{
					$operate_info = $model_operatelog->where(array('oid'=>$data['id']))->order('create_time asc')->find();
					$operate_id = $operate_info['operate_id'];
				}
	
				$post = array(
						'code'=> 'fqcd123223',
						'mobile'=> $data['mobile'],
						'name'=>iconv("utf-8","gb2312//IGNORE",$data['truename']),
						'remark'=>iconv("utf-8","gb2312//IGNORE",$remark),
						'staff_id'=>$staff[$operate_id],
						'code'=>'fqcd123223',
						'task'=>1
				);
	
				if (!$operate_id || !$staff[$operate_id]) {
					$return['error'][] = $post;
				}else{
					//var_dump($post);
					$return['send'][] = $this->curl($url,$post);
					sleep(1);
				}
			}
		}
		//程序日志
		$this->addCodeLog('crontab', var_export($return,true));
		$this->submitCodeLog('crontab');
		//var_dump($return);
	}
	private function _curl($url, $post = NULL, $host = NULL) {
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$cookieFile = NULL;
		$hCURL = curl_init ();
		curl_setopt ( $hCURL, CURLOPT_URL, $url );
		curl_setopt ( $hCURL, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $hCURL, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_USERAGENT, $userAgent );
		curl_setopt ( $hCURL, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt ( $hCURL, CURLOPT_AUTOREFERER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_ENCODING, "gzip,deflate" );
		curl_setopt ( $hCURL, CURLOPT_HTTPHEADER, $host );
		if ($post) {
			curl_setopt ( $hCURL, CURLOPT_POST, 1 );
			curl_setopt ( $hCURL, CURLOPT_POSTFIELDS, $post );
		}
		
		$sContent = curl_exec ( $hCURL );
		// var_dump(curl_getinfo($hCURL));exit;
		if ($sContent === FALSE) {
			$error = curl_error ( $hCURL );
			curl_close ( $hCURL );
			
			throw new \Exception ( $error . ' Url : ' . $url );
		} else {
			curl_close ( $hCURL );
			return $sContent;
		}
	}

	function alitest222(){
		$url = WEB_ROOT.'/alipay_fuwuchuang/ali_test.php';
		$post = array(
				"title" => "支付宝服务窗推送测试-标题",
				"desc" => "支付宝服务窗推送测试-简介",
				"url" => WEB_ROOT,
				"imageUrl" => WEB_ROOT."/Public_new/images/index/logo.png",
				"authType" => "loginAuth",
				"toUserId" => "koagTT7t4pybqZGTaCr9999DtD9JvhrUUphjjgr7S1Xp7Xh6N6uxXH27PqW7Qzpm01"
		);
		$data = $this->_curl($url,$post);
		var_dump($data);		
	}
}