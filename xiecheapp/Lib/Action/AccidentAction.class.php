<?php
//订单
class AccidentAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->ShopModel = D('shop');
		$this->bidorder = D('bidorder');//事故车订单表
		$this->CouponModel = D('Coupon');
		$this->FsModel = D('fs');//4S店
		$this->CarseriesModel = D('carseries');//车系表
		$this->CarbandModel = D('carbrand');//品牌绑定
		$this->RegionModel = D('Region');
		$this->MemberModel = D('member');//验证码
		$this->model_sms = D('Sms');//手机短信
		$this->PadataModel = D('Padatatest');//接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->LossModel = D("loss");
		$this->MembercouponModel = D('membercoupon');
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		import("ORG.Util.hm");
		$_hmt = new hm("25bc1468b91be39c26580a917bfbf673");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('baiduimg',$_hmtPixel);
	}

	/*
		@author:chf
		@function:(显示事故车WAP主页)
		@time:2014-06-06
	*/
	function index(){
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$this->display();
	}

	/*
		@author:chf
		@function:(显示事故车处理流程步骤页)
		@time:2014-06-06
	*/
	function ac_guide(){
		$this->display();
	}
	
	/*
		@author:chf
		@function:(显示添加事故车订单页)
		@time:2014-06-06
	*/
	function ac_form(){
		//选择车型取得数据(XX需求呵呵)
		$FSdata = $this->FsModel->select();
		foreach($FSdata as $k=>$v){
			$Carseries = $this->CarseriesModel->where(array('fsid'=>$v['fsid']))->find();
			$Carband = $this->CarbandModel->where(array('brand_id'=>$Carseries['brand_id']))->find();
			$FSdata[$k]['word'] = $Carband['word']; 
		}
		$this->assign('FSdata',$FSdata);
		$this->display();
	}


	/*
		@author:chf
		@function:(显示添加事故车订单页)
		@time:2014-06-06
	*/
	function add_InsuranceModel(){
		
		if($_POST['hide_fsid']){
			$data['fsid'] = $_POST['hide_fsid'];
		}

		
		if($_POST['description']){
			$data['description'] = $_POST['description'];
		}
		
		if($_POST['mobile']){
			$data['user_phone'] = $_POST['mobile'];
			//得到微信标绑定ID 29521
			$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
			
			$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
		
			if(!$Paweixin){
				$id = $this->PaweixinModel->add(array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time()));
				
				$Paweixin_id = $id;
				$weixin_status = '1';//要验证的状态
			}else{
				
				$old_weixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2','status'=>'2'))->find();
				$weixin_status = $Paweixin['status'];//要验证的状态
				$Paweixin_id = $Paweixin['id'];
			}
			
			$member = $this->MemberModel->where(array('mobile'=>$data['user_phone']))->find();
			$data['uid'] = $member['uid'];
			//需要验证注册
			if($weixin_status == '1'){
				$code = rand(100000,999999);
				$uid = $this->CheckMember($member,$data['user_phone'],$Paweixin_id,$code,'2');
				$data['uid'] = $uid;
			}
		}

		$Insurance_id = $this->InsuranceModel->add($data);
		if(!empty($Insurance_id)){
			$this->success('订单提交成功！',__APP__.'/Accident/index/pa_id/'.$_SESSION['xc_id']);
		}else {
			$this->error('订单预约失败',__APP__.'/Accident/index/index/pa_id/'.$_SESSION['xc_id']);
		}

	}



	/*
		@author:chf
		@function:记录微信ID,手机绑定UID修改status为2
		@time:2014-02-19
	*/
	function CheckMember($member,$mobile,$Paweixin_id,$code,$type){
		if($member){
			$this->PaweixinModel->where(array('id'=>$Paweixin_id,'type'=>$type))->save(array('mobile'=>$mobile,'uid'=>$member['uid'],'status'=>'2'));
			return $member['uid'];
		}else{
			$member_data['mobile'] = $mobile;
			$member_data['password'] = md5($code);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '36';//携车微信
			$uid = $this->MemberModel->data($member_data)->add();

			$this->PaweixinModel->where(array('id'=>$Paweixin_id,'type'=>$type))->save(array('mobile'=>$mobile,'uid'=>$uid,'status'=>'2'));
			/*
			$send_add_user_data = array(
			'phones'=>$mobile,
			'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$code.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
			*/

			// dingjb 2015-09-29 10:44:50 切换到云通讯
			$send_add_user_data = array(
				'phones'	=> $mobile,
				'content'	=> array(
					$mobile,
					$code
				)
			);
			$this->curl_sms($send_add_user_data, null, 4, '37653');

			$send_add_user_data['sendtime'] = time();
			$this->model_sms->data($send_add_user_data)->add();
			return $uid;
		}


	}
	
	/*
		@author:chf
		@function:(显示保险公司电话列表)
		@time:2014-06-06
	*/
	function ic_list(){
	
		$this->display();
	}
	
	/*
		@author:chf
		@function:(显示处理流程页)
		@time:2014-06-06
	*/
	function f_place(){
		
		$this->display();
	}

	/*
		@author:chf
		@function:(显示处理流程页)
		@time:2014-06-06
	*/
	function mybid_order(){
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$Padata = $this->PadataModel->where(array('id'=>$_SESSION['xc_id']))->find();
		$Pawx = $this->PaweixinModel->where(array('wx_id'=>$Padata['FromUserName']))->find();

		// 导入分页类
		import("ORG.Util.Page");
		$count = $this->bidorder->where(array('uid'=>$Pawx['uid']))->count();
		// 实例化分页类
		$p = new Page($count, '10');

		$page = $p->show_app();
		$arr['uid'] = $Pawx['uid'];
	
		$bidorder = $this->bidorder->where($arr)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($bidorder){
			foreach($bidorder as $k=>$v){
			
			}
		}
		
		$this->assign('bidorder',$bidorder);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:(显示店铺地图)
		@time:2014-06-06
	*/

	function maps(){
		$data['id'] = $_GET['shopid'];
		$shop = $this->LossModel->where($data)->find();
		$lat = $_GET['lat'];
		$long = $_GET['long'];
		$this->assign('shop_name',$shop['loss_name']);
		$this->assign('shop_address',$shop['loss_address']);
		$this->assign('lat',$lat);
		$this->assign('long',$long);
		$this->display();
	}


	function ajax_shop_list() {
		$page_size = 30;
		 $map_shop['status'] = 1;
        if ($_REQUEST['order'] == 'distance' and $_REQUEST['lat'] and $_REQUEST['long']){
		
            $shops = $this->LossModel->where($map_shop)->select();
            if ($shops){
                foreach ($shops as $_key=>$_val){
                    $shop_maps = $_val['loss_map'];
                    $shop_maps_arr = explode(',',$shop_maps);
                    $shops[$_key]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
					
                }
                $count = count($shops);
                for($i = 0; $i < $count; $i ++) {
                    for($j = $count - 1; $j > $i; $j --) {
                        if ($shops[$j]['distance'] < $shops[$j - 1]['distance']) {
                            //交换两个数据的位置
                            $temp = $shops [$j];
                            $shops [$j] = $shops [$j - 1];
                            $shops [$j - 1] = $temp;
                        }
                    }
                }
                $p_count = ceil($count/$page_size);
                for ($ii=0;$ii<$p_count;$ii++){
                   $page_shops[$ii] = array_slice ($shops, $ii*$page_size,$page_size);

                }
                $p = isset($_REQUEST['p'])?$_REQUEST['p']:1;
				if($p == 0) {
					$p=1;
				}
                $shop_info = $page_shops[$p-1];
            }
        }else{
            // 导入分页类
            import("ORG.Util.Page");
            $count = $this->LossModel->where($map_shop)->count();
            // 实例化分页类

            $p = new Page($count, $page_size);
		
			$page = $p->show_app();
			$shop_info = $this->LossModel->where($map_shop)->limit($p->firstRow.','.$p->listRows)->select();
			if( $shop_info) {	
				foreach($shop_info as $key=>$val) {
					$shop_maps = $val['loss_map'];
					$shop_maps_arr = explode(',',$shop_maps);
					//$shop_info[$key]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
					$shop_info[$key]['distance'] = "地理位置不可用";
					
				}
			}
            $p_count = ceil($count/$page_size);
			$this->assign('allnum',$p_count);
      }

	  if($shop_info) {
		  foreach ($shop_info as $k=>$v){
			$result[$k]['id'] = $v['id'];
			$result[$k]['shop_name'] = $v['loss_name'];
			$result[$k]['shop_address'] = $v['loss_address'];
			$result[$k]['distance'] = round($v['distance']/1000,2);
			$result[$k]['shop_mobile'] = $v['mobile'];
			$result[$k]['worktime'] = $v['service'];
			$Arr_shopmaps = explode(",",$v['loss_map']);
			$result[$k]['lat'] = $Arr_shopmaps[0];
			$result[$k]['long'] = $Arr_shopmaps[1];
			
			
		  }
	  }


	  echo json_encode($result);exit();
	}
}