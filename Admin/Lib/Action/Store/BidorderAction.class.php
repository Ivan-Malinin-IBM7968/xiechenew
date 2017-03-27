<?php
/*
 */

class BidorderAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->BidorderModel = D('bidorder');//保险类订单表
		$this->ShopModel = D('shop');//店铺表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->InsuranceimgModel = D('insuranceimg');//用户保险竞价图片表
		$this->BidCouponModel = D('bidcoupon');//保险类订单返利券表
		$this->FsModel = D("Fs"); 
		$this->BidordercommentModel = D('bidordercomment');//保险类订单评价表
		$this->BidorderremarkModel = D('bidorderremark');//保险类订单评价表
		$this->BidorderjiesuanModel = D('bidorderjiesuan');//保险类订单结算表
		$this->admin_model = M('tp_admin.user', 'xc_');//后台用户表
	}
	/*
	 * 发送事故车名单到外呼列表
	*/
	function sendApi(){
		//$ret = $this->curl_test();
		//var_dump($ret);
		//exit;
		
		$uid = $_SESSION['authId'];
		$staff = array(
				1	=> '2014102900000025',//阿德方面
				223 => '2014102900000025',//王俊炜
				171 => '2014102900000027',//彭晓文
				182 => '2014102900000029',//张丹红
				259 => '2014102900000031',//乔敬超
				234 => '2014102900000033',//张美婷
				243 => '2014102900000035',//黄美琴
				242 => '2014102900000037',//李宝峰
				251 => '2014102900000041',//庄玉成
				252	=> '2014102900000039'//周祥金
		);
		$username = array(
				1	=> 'admin',
				223 => '王俊炜',
				171 => '彭晓文',
				182 => '张丹红',
				259 => '乔敬超',
				234 => '张美婷',
				243 => '黄美琴',
				242 => '李宝峰',
				251 => '庄玉成',
				252	=> '周祥金',
		);
		$operate_id = $staff[$uid];
		if (!$operate_id) {
			echo json_encode( array('status'=>0,'msg'=>'您无权发送该消息！'));
			exit;
		}
		if(!$_POST['mobile']){
			$_POST['mobile'] = '137742364131';
		}
		if(!$_POST['remark']){
			$_POST['remark'] = '137742364131';
		}
		if(!$_POST['username']){
			$_POST['username'] = '11测试';
		}
		$mobile = trim($_POST['mobile']);
		
		$url = 'http://www.fqcd.3322.org:88/api.php';
		$remark = trim('车牌号：'.$_POST['chepai'].'备注：'.$_POST['remark']);
		$post = array(
				'code'=> 'fqcd123223',
				'mobile'=> $mobile,
				'name'=>iconv("utf-8","gb2312//IGNORE",trim($_POST['username'])),
				'remark'=>iconv("utf-8","gb2312//IGNORE",trim($remark)),
				'staff_id'=>$operate_id,
				'task'=>2
		);
		$mShigucheRecord = D('api_record');
		
		$isRepeat = $mShigucheRecord->where(array('mobile'=>$mobile ))->find();
		if ( $isRepeat) {
			$name = $username[$isRepeat['staff_id']];
			$msg = '该用户已经在'.date('Y-m-d',$isRepeat['create_time']).'被'.$name.'添加过了，不能重复添加';
			echo json_encode( array('status'=>0,'msg'=>$msg));
			exit;
		}
		$this->addCodeLog('sendapi', 'start');
		$return = $this->_curl($url,$post);
		echo "333333333";
		var_dump($return);
		$this->addCodeLog('sendapi', var_export($return, true).'end');
		$this->submitCodeLog('sendapi');
		if ($return && $return == true) {
			$data = array(
					'mobile'=> $mobile,
					'name'=>$_POST['username'],
					'remark'=>$remark,
					'staff_id'=>$uid,
					'type'=>1,
					'create_time'=>time()
			);
			if ($mShigucheRecord->add($data)) {
				echo json_encode( array('status'=>1,'msg'=>$return));
			}else{
				echo json_encode( array('status'=>0,'msg'=>'发送失败,插入数据失败！'));
			}
		}else{
			echo json_encode( array('status'=>0,'msg'=>'发送失败,请重新发送！'));
		}
	}
	
	public function _curl($url, $post = NULL,$host=NULL) {
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$cookieFile = NULL;
		$hCURL = curl_init();
		curl_setopt($hCURL, CURLOPT_URL, $url);
		curl_setopt($hCURL, CURLOPT_TIMEOUT, 30);
		curl_setopt($hCURL, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($hCURL, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($hCURL, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($hCURL, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($hCURL, CURLOPT_ENCODING, "gzip,deflate");
		curl_setopt($hCURL, CURLOPT_HTTPHEADER,$host);
		if ($post) {
			curl_setopt($hCURL, CURLOPT_POST, 1);
			curl_setopt($hCURL, CURLOPT_POSTFIELDS, $post);
		}
		$sContent = curl_exec($hCURL);
		if ($sContent === FALSE) {
			$error = curl_error($hCURL);
			curl_close($hCURL);
	
			throw new \Exception($error . ' Url : ' . $url);
		} else {
			curl_close($hCURL);
			return $sContent;
		}
	}

	
	/*
		@author:chf
		@function:显示保险类订单页
		@time:2013-05-07
	*/
    public function index(){

		$status_a= array('未结算','申请结算','商家确认','已开票','已收款');

		if($this->request('id')){
			$data['id'] = $map['id'] = $_REQUEST['id'];
		}

		if($_REQUEST['mobile']){
			$data['mobile'] = $map['mobile'] = $_REQUEST['mobile'];
		}
		if($_REQUEST['licenseplate']){
			$data['licenseplate'] = $map['licenseplate'] = $_REQUEST['licenseplate'];
		}
		if(($_REQUEST['order_status'] || $_REQUEST['order_status']=='0') && $_REQUEST['order_status']!='5' ){
		
			$data['order_status'] = $map['order_status'] = $_REQUEST['order_status'];
		}else{
			$data['order_status'] = $_REQUEST['order_status'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		}
		if($_REQUEST['shopname']){
			$data['shop_name'] = $_REQUEST['shop_name'];
		}
		if($_REQUEST['customer_id']){
			$data['customer_id'] = $map['customer_id'] = $_REQUEST['customer_id'];
		}
		$smap['safestate'] = 1;
		$shopinfo = $this->ShopModel->where($smap)->field('id')->select();
		foreach($shopinfo as $k=>$v){
			$shop_info[] = $v['id'];
		}
		if($_REQUEST['qian']){
			$map['shop_id'] = array('in', implode(',',$shop_info));
			//$where['safestate'] = 1;
			//$subQuery = $this->BidorderModel->field('id')->table('xc_shop')->where($where)->buildSql(); 
		}
		if(isset($_REQUEST['search_type']) and $_REQUEST['search_type']!='' and isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			//@author 翻页问题数字2013-12-22+00:00:00  自动过略+号;
			$start_time = strtr($_REQUEST['start_time'],'+'," ");
			$end_time = strtr($_REQUEST['end_time'],'+'," ");
			
			if( $_REQUEST['start_time'] ) {
				$map[$_REQUEST['search_type']] = array(array('gt',strtotime($start_time)));
			}
			if( $_REQUEST['end_time'] ) {
				$map[$_REQUEST['search_type']] = array(array('lt',strtotime($end_time.' 23:59:59')));
			}
			if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
				$map[$_REQUEST['search_type']] = array(array('gt',strtotime($start_time)),array('lt',strtotime($end_time.' 23:59:59')));
			}
			
			$this->assign('search_type',$_REQUEST['search_type']);
			$this->assign('start_time',$_REQUEST['start_time']);
			$this->assign('end_time',$_REQUEST['end_time']);
		}
		
		$map['status'] = 1;

		// 计算总数
		$count = $this->BidorderModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
 
		// 实例化分页类
		$p = new Page($count,20,$parameter);

		//分页跳转的时候保证查询条件
        foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != '') {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        } 
		// 分页显示输出
		$page = $p->show_admin();
		//if($_REQUEST['qian']){
			$data['Bidorder'] = $this->BidorderModel->where($map)->table($subQuery)->order("create_time DESC")->select();
		//}else{
			$data['Bidorder'] = $this->BidorderModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		//}
		//echo $this->BidorderModel->getLastsql();
		if($data['Bidorder']){
			foreach($data['Bidorder'] as $k=>$v){
				$Shopbidding = $this->ShopbiddingModel->where(array('id'=>$v['bid_id']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Shopbidding['shop_id']))->find();
				$remark = $this->BidorderremarkModel->where(array('bidorder_id'=>$v['id']))->find();
				$data['Bidorder'][$k]['remark'] = $remark['remark'];
				$data['Bidorder'][$k]['order_id'] = $this->get_orderid($v['id']);
				if($shop['safestate']=='1'){
					$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'].'<font color=red><b>(签)</b></font>';
				}else{
					$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'];
				}
				if($v['is_surveyor']=='1'){
					$data['Bidorder'][$k]['shop_name'] = $data['Bidorder'][$k]['shop_name'].'<font color=#B5A642><b>(返)</b></font>';
				}

				$jiesuan = $this->BidorderjiesuanModel->where(array('bidorder_id'=>$v['id']))->find();

				$a='';
				if($jiesuan['open_time']){
					$a = '</br>票'.date('Y-m-d H:i',$jiesuan['open_time']);
				}
				if($jiesuan['collect_time']){
					$a.= '</br>钱'.date('Y-m-d H:i',$jiesuan['collect_time']);
				}

				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
				$data['Bidorder'][$k]['loss_price'] = $Insurance['loss_price'].'</br>'.$status_a[$jiesuan['jiesuan_status']].$a;
				$data['sum_loss_price'] = $data['sum_loss_price']+$data['Bidorder'][$k]['loss_price'];

				$where = array( 'admin_id' => $v['customer_id'] );
				$mStaff = M('tp_xieche.staff_oracle','xc_');
				$staff = $mStaff->where($where)->find();
				$data['Bidorder'][$k]['customer_id']=$staff['name'];
			}

		}

		$admin['status'] = 1;
		$admin['remark'] = '客服';
        $data['customer_list'] = $this->admin_model->where($admin)->select();

		$data['shop_list'] = $this->ShopModel->select();
		
		$uid = $_SESSION['authId'];
		$m_staff_oracle = M('tp_xieche.staff_oracle','xc_');
		$oracleData = $m_staff_oracle->select();	//后台和外呼系统对应关系
		
		$staff = array();
		if ($oracleData) {
			foreach ($oracleData as $oracleVal){
				$staff[$oracleVal['admin_id']] = $oracleVal['oracle_id']; 
			};
		}
// 		$staff = array(
// 				1	=> '2014102900000025',//admin
// 				223 => '2014102900000025',//王俊炜
// 				171 => '2014102900000027',//彭晓文
// 				182 => '2014102900000029',//张丹红
// 				259 => '2014102900000031',//乔敬超
// 				234 => '2014102900000033',//张美婷
// 				243 => '2014102900000035',//黄美琴
// 				242 => '2014102900000037',//李宝峰
// 				251 => '2014102900000041',//庄玉成
// 				252	=> '2014102900000039',//周祥金
// 				266 => '2014102900000041',	//黄赟	朱迎春6004，周洋6001
// 				274 => '2014102900000029', //王宇晨6004
// 				268 => '2014102900000023', //周洋 6001
// 				269 => '2014102900000031', //寇建学 6005
// 				273 => '2014102900000043',//杨超
// 		);

		$operate_id = $staff[$uid];

		//print_r($data['Bidorder']['0']);
		$this->assign('authId',$_SESSION['authId']);
		$this->assign('staff_id',$operate_id);
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
		
    }
	/*
		@author:chf
		@function:显示修改订单状态页面
		@time:2013-5-6
	*/
	function edit(){
		$map['id'] = $_REQUEST['id'];
		$$map['status'] = 1;
		$data = $this->BidorderModel->where($map)->find();
		if($data){
			$shop_bid = $this->ShopbiddingModel->where(array('id'=>$data['bid_id']))->find();
			$data['rebate'] = $shop_bid['rebate'];
		}
		$Insurance = $this->InsuranceModel->where(array('id'=>$data['insurance_id']))->find();
		$Insurance_img = $this->InsuranceimgModel->where(array('insurance_id'=>$data['insurance_id']))->select();
		$fs_info = $this->FsModel->find($Insurance['fsid']);

		$Shop = $this->ShopModel->where(array('id'=>$data['shop_id']))->find();
		$Shopbidding = $this->ShopbiddingModel->find($data['bid_id']);

		$data['loss_price'] = $Insurance['loss_price'];
		$data['proxy_phone'] = $Insurance['proxy_phone'];
		$shop = $this->ShopModel->where(array('id'=>$shop_bid['shop_id']))->find();
		$data['safestate'] = $shop['safestate'];

		$data['order_id'] = $this->get_orderid($data['id']);//虚假订单号
		$data['user_name'] = $Insurance['user_name'];//姓名:
		$data['user_phone'] = $Insurance['user_phone'];//手机号码
		$data['insurance_name'] = $Insurance['insurance_name'];//保险公司名

		$data['description'] = $Insurance['description'];//用户车辆描述
		//$data['operator_remark'] = $Insurance['operator_remark'];//客服描述
		$data['fsname'] = $fs_info['fsname'];
		$data['car_info'] = $this->get_car_info($Insurance['brand_id'],$Insurance['series_id'],$Insurance['model_id']);
		$data['shop_name'] = $Shop['shop_name'];//4S店铺名
		$data['shop_address'] = $Shop['shop_address'];//4S店铺地址
		$data['shop_mobile'] = $Shop['shop_mobile'];//4S店铺电话
		$data['shop_phone'] = $Shop['shop_phone'];//4S店铺电话
		$data['insuranceimg'] = $Insurance_img;//保险图片
		$data['driving_img'] = $Insurance['driving_img'];//行驶证照片
		$data['Shopbidding'] = $Shopbidding;

		$this->assign('data',$data);
		$this->assign('id',$map['id']);
		$this->display();
	}

		/*
		@author:chf
		@function:显示修改订单详情页面
		@time:2013-5-6
	*/
	function orderdetail(){
		$map['id'] = $_REQUEST['id'];
		$map['status'] = 1;
		$data = $this->BidorderModel->where($map)->find();
		$Insurance = $this->InsuranceModel->where(array('id'=>$data['insurance_id']))->find();
		$Insurance_img = $this->InsuranceimgModel->where(array('insurance_id'=>$data['insurance_id']))->select();
		$fs_info = $this->FsModel->find($Insurance['fsid']);

		$Shop = $this->ShopModel->where(array('id'=>$data['shop_id']))->find();
		$Shopbidding = $this->ShopbiddingModel->find($data['bid_id']);

		$data['order_id'] = $this->get_orderid($data['id']);//虚假订单号
		$data['user_name'] = $Insurance['user_name'];//姓名:
		$data['user_phone'] = $Insurance['user_phone'];//手机号码
		$data['insurance_name'] = $Insurance['insurance_name'];//保险公司名
		$data['proxy_phone'] = $Insurance['proxy_phone'];//代理人手机号
		$data['loss_price'] = $Insurance['loss_price'];//订单金额
		$data['description'] = $Insurance['description'];//用户车辆描述
		$data['operator_remark'] = $Insurance['operator_remark'];//客服描述
		$data['fsname'] = $fs_info['fsname'];
		$data['car_info'] = $this->get_car_info($Insurance['brand_id'],$Insurance['series_id'],$Insurance['model_id']);
		$data['shop_name'] = $Shop['shop_name'];//4S店铺名
		$data['shop_address'] = $Shop['shop_address'];//4S店铺地址
		$data['insuranceimg'] = $Insurance_img;//保险图片
		$data['driving_img'] = $Insurance['driving_img'];//行驶证照片
		
		$data['Shopbidding'] = $Shopbidding;
		
		$this->assign('data',$data);
		$this->display();
	}
	
	/*
		@author:chf
		@function:修改保险类订单状态
		@time:2013-5-6
	*/
	function edit_do(){
		$model_smslog = D('sms');
		$map['id'] = $_REQUEST['id'];
		$data['licenseplate'] = $_REQUEST['licenseplate'];//车牌号
		$data['order_status'] = $_REQUEST['order_status'];//订单状态		
		$data['tostore_time'] = strtotime($_REQUEST['tostore_time']);//预约到店时间
		$data['takecar_time'] = strtotime($_REQUEST['takecar_time']);//提车时间
		$data['remark'] = $_REQUEST['remark'];//订单备注
		$data['is_surveyor'] = $_REQUEST['is_surveyor'];//是否返利
		$shopbid['rebate'] = $_REQUEST['rebate'];//返利金额
		$BidorderList = $this->BidorderModel->where($map)->find();
		$Bidorder_id = $this->get_orderid($BidorderList['id']);

		$old_Shopbid = $this->ShopbiddingModel->where(array('id'=>$BidorderList['bid_id']))->find();
		$this->ShopbiddingModel->where(array('id'=>$BidorderList['bid_id']))->data(array('rebate'=>$shopbid['rebate']))->save();
		$Shopbid = $this->ShopbiddingModel->where(array('id'=>$BidorderList['bid_id']))->find();

		if($_REQUEST['order_status'] == '4' and $BidorderList['create_time']<=1415030400){
			$shop = $this->ShopModel->where(array('id'=>$Shopbid['shop_id']))->find();
			if($Shopbid['rebate'] > 0 ){
				$rebate = $Shopbid['rebate']/100;
				$arr = explode('.',$rebate);
				for($a=0;$a<$arr[0];$a++){
					$rand_str = $this->randString(9,1);
					$BidOrder['shop_id'] = $Shopbid['shop_id'];
					$BidOrder['start_time'] = time();
					$BidOrder['price'] = 100;
					$BidOrder['end_time'] = time() + 86400*180;
					$BidOrder['uid'] = $BidorderList['uid'];
					$BidOrder['bidorder_id'] = $BidorderList['id'];
					$BidOrder['create_time'] = time();
					$BidOrder['code'] = $rand_str;
					$BidOrder['mobile'] = $BidorderList['mobile'];
					$BidOrder['licenseplate'] = $BidorderList['licenseplate'];
					$this->BidCouponModel->add($BidOrder);

					$verify_str = "您的事故车维修订单".$Bidorder_id."号已完成。您已获得".$shop['shop_name']."店铺的现金抵用券100元请凭消费码".$rand_str."至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".date('Y-m-d ',$BidOrder['start_time'])."至".date('Y-m-d',$BidOrder['end_time']).")";

					$send_verify = array('phones'=>$BidorderList['mobile'],'content'=>$verify_str);
					$this->curl_sms($send_verify);
					$sms_log['phones'] = $BidorderList['mobile'];
					$sms_log['sendtime'] = time();
					$sms_log['content'] = $verify_str;
					$model_smslog->add($sms_log);
				}
				if($arr[1]>0){
					if(strlen($arr[1])>1){
						$rebate = $arr[1];
					}else{
						$rebate = $arr[1]*10;
					}
					$rand_str = $this->randString(9,1);
					$BidOrder['shop_id'] = $Shopbid['shop_id'];
					$BidOrder['start_time'] = time();
					$BidOrder['price'] = $rebate;
					$BidOrder['end_time'] = time() + 86400*180;
					$BidOrder['uid'] = $BidorderList['uid'];
					$BidOrder['bidorder_id'] = $BidorderList['id'];
					$BidOrder['create_time'] = time();
					$BidOrder['code'] = $rand_str;
					$BidOrder['mobile'] = $BidorderList['mobile'];
					$BidOrder['licenseplate'] = $BidorderList['licenseplate'];
					$this->BidCouponModel->add($BidOrder);
					$verify_str = "您的事故车维修订单".$Bidorder_id."号已完成。您已获得".$shop['shop_name']."店铺的现金抵用券".$rebate."元请凭消费码".$rand_str."至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".date('Y-m-d ',$BidOrder['start_time'])."至".date('Y-m-d',$BidOrder['end_time']).")";
					
					$send_verify = array('phones'=>$BidorderList['mobile'],'content'=>$verify_str);
					$return_data = $this->curl_sms($send_verify);
					$sms_log['phones'] = $BidorderList['mobile'];
					$sms_log['sendtime'] = time();
					$sms_log['content'] = $verify_str;
					$model_smslog->add($sms_log);
				}
			}
		}
		if($_REQUEST['order_status'] == '4'){
			$data['complete_time'] = time();
		}
		$this->BidorderModel->where($map)->data($data)->save();
		

		$bidorder_log_model = D("Bidorder_log");
		$log_data['name'] = $_SESSION['loginAdminUserName'];
		$log_data['bidorder_id'] = $BidorderList['id'];
		$log_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$log_data['create_time'] = time();
		
		if($BidorderList['order_status'] != $_REQUEST['order_status']) {
			$BIDORDER_STATE = C('BIDORDER_STATE');
			$log_data['content'] = "订单状态 ".$BIDORDER_STATE[$BidorderList['order_status']] ." 修改为 ".$BIDORDER_STATE[$_REQUEST['order_status']];
			$bidorder_log_model->add($log_data);
		}
		if($BidorderList['tostore_time'] != strtotime($_REQUEST['tostore_time'])) {
			$log_data['content'] = "到店时间 ".date("Y-m-d H:i:s",$BidorderList['tostore_time']) ." 修改为 ".$_REQUEST['tostore_time'];
			$bidorder_log_model->add($log_data);
		}
		if($BidorderList['takecar_time'] != strtotime($_REQUEST['takecar_time'])) {
			$log_data['content'] = "取车时间 ".date("Y-m-d H:i:s",$BidorderList['takecar_time']) ." 修改为 ".$_REQUEST['takecar_time'];
			$bidorder_log_model->add($log_data);
		}

		if($old_Shopbid['rebate'] != $_REQUEST['rebate']) {
			$log_data['content'] = "返利金额 ".$Shopbid['rebate'] ." 修改为 ".$_REQUEST['rebate'];
			$bidorder_log_model->add($log_data);
		}
		$insurance_info = $this->InsuranceModel->find($_REQUEST['insurance_id']);
		if($insurance_info['loss_price'] != $_REQUEST['loss_price']) {
			$log_data['content'] = "定损金额 ".$insurance_info['loss_price'] ." 修改为 ".$_REQUEST['loss_price'];
			$bidorder_log_model->add($log_data);
		}
		$map_Insurance['id'] = $_REQUEST['insurance_id'];
		if ($_REQUEST['order_status'] == '4') {
			$edit_Insurance['insurance_status'] = 4;
		}
		$edit_Insurance['loss_price'] = $_REQUEST['loss_price'];
		$edit_Insurance['licenseplate'] = $_REQUEST['licenseplate'];//车牌号
		$this->InsuranceModel->where($map_Insurance)->data($edit_Insurance)->save();

        $this->success('修改成功！',U('/Store/Bidorder/index'));
	
	}

	function delete_order(){
		$id = $_REQUEST['id'];
		$bidorder = $this->BidorderModel->find($id);
		$insurance_id = $bidorder['insurance_id'];

		$bid_data['status'] = 0;
		$bid_data['id'] = $id;
		$this->BidorderModel->save($bid_data);
		
		$shopbidding_data['status'] = 0;
		$shopbidding_data['insurance_id'] = $insurance_id;
		$this->ShopbiddingModel->save($shopbidding_data);

		$insurance_data['id'] = $insurance_id;
		$insurance_data['state'] = 0;
		$this->InsuranceModel->save($insurance_data);

		echo "1";exit();

	}
	/*
		@author:chf
		@function:得到店铺名
		@time:2013-5-6
	*/
	function GetShopname(){
		if($_REQUEST['shopname']){
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
		}
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}
    	
	function bidorder_log() {
		$bidorder_id = $_REQUEST['id'];
		$bidorder_log_model = D("Bidorder_log");
		$map['bidorder_id'] = $bidorder_id;
		$log_list = $bidorder_log_model->where($map)->select();
		$this->assign('log_list',$log_list);
		$this->display();
	}

	/*
		@author:wwy
		@function:显示事故车评价添加
		@time:2014-5-16
	*/
	function comment_edit(){
		$map['bidorder_id'] = $_REQUEST['id'];
		$list = $this->BidordercommentModel->where($map)->select();
		//echo $this->BidordercommentModel->getLastsql();
		$model_user = M('tp_admin.user','xc_');
		foreach($list as $key=>$value){
			$user_info = $model_user ->where("id = $value[operator_id]")->field('nickname')->find();
			$list[$key]['operator_name'] = $user_info['nickname'];
		}
		//print_r($list);
		$this->assign('list',$list);
		$this->assign('bidorder_id',$map['bidorder_id']);
		$this->display();
	}

	/*
		@author:wwy
		@function:执行添加事故车评价
		@time:2014-5-16
	*/
	function comment_edit_do(){
		$data['bidorder_id'] = $_REQUEST['bidorder_id'];
		$data['content'] = $_REQUEST['content'];
		$data['level'] = $_REQUEST['level'];
		$data['operator_id'] = $_SESSION['authId'];
		$data['create_time'] = time();
		//print_r($data);
		$this->BidordercommentModel->add($data);
		//echo $this->BidordercommentModel->getLastsql();

        $this->success('评价成功！',U('/Store/Bidorder/comment_edit/id/'.$_REQUEST['bidorder_id']));
	}

	/*
		@author:wwy
		@function:执行添加事故车备注（小赵专用）
		@time:2014-8-7
	*/
	function do_remark(){
		$data['bidorder_id'] = $_REQUEST['bidorder_id'];
		$data['remark'] = $_REQUEST['remark'];
		//print_r($data);
		$map['bidorder_id'] = $_REQUEST['bidorder_id'];
		$result = $this->BidorderremarkModel->where($map)->count();
		if($result>0){
			$this->BidorderremarkModel->where($map)->save($data);
			echo 1;
		}else{
			$this->BidorderremarkModel->add($data);
			//echo $this->BidorderremarkModel->getLastsql();
			echo 2;
		}
	}
	
	
}