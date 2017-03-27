<?php
//用户保险竞价
class InsuranceAction extends CommonAction {
    function __construct() {
		parent::__construct();
		if( true !== $this->login()){
			exit;
		}
	}
	
	function index() {
		
		$memcache_app_shops = S('coupon58');
	
		var_dump($memcache_app_shops);
		$this->display();
	}

	/*
		@author:ysh
		@function:用户的保险订单列表-4S竞价查询
		@time:2013/5/8
	*/
	function  my_insurance() {
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");
		$shopbidding_model = D("Shopbidding");
		
		$insurance_status = array(
			'1'=>'竞价中',
			'2'=>'竞价结束',
			'3'=>'订单确认',
			'4'=>'订单完成',
		);
		$uid = $this->GetUserId();
		$map['uid'] = $uid;
		// 计算总数
        $count = $insurance_model->where($map)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $insurance_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if($list) {
			foreach($list as $key=>$val) {
				$shop_count = $shopbidding_model->where(array('insurance_id'=>$val['id'],'status'=>1))->count();
				$list[$key]['shop_count'] = $shop_count;
				
				$car_info = $this->get_car_info($val['brand_id'],$val['series_id'],$val['model_id']);
				$list[$key]['car_info'] = $car_info;

				$list[$key]['insurance_status'] = $insurance_status[$val['insurance_status']];
				if( ($val['validity_time']<time() ) && ($val['insurance_status'] == 1) ) {
					$save_map = array();
					$save_map['id'] = $val['id'];
					$save_map['insurance_status'] = 2;
					$insurance_model->save($save_map);
					$list[$key]['insurance_status'] = "竞价结束";
				}

			}
		}
		
		$this->assign('list',$list);
		$this->display();
	}

	/*
		@author:ysh
		@function:用户保险竞价添加页面
		@time:2013/5/7
	*/
	public function add() {
		$insurance_name = C("INSURANCE_NAME");

		$this->assign("insurance_name",$insurance_name);
		$this->assign("user_name",$_SESSION['username']);
		$this->assign("user_phone",$_SESSION['mobile']);
		$this->display();
	}
	
	/*
		@author:ysh
		@function:添加用户保险竞价表
		@time:2013/5/7
	*/
	public function insert() {
		import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload = $this->_upload_init($upload);
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $uploadList = $upload->getUploadFileInfo();
		}
		if($uploadList) {
			foreach($uploadList as $key=>$val) {
				switch ($val['key']) {
					case 0:
						$car_imgs['car_img1'] = $val['savename'];
						break;
					case 1:
						$car_imgs['car_img2'] = $val['savename'];
						break;
					case 2:
						$car_imgs['car_img3'] = $val['savename'];
						break;
					case 3:
						$car_imgs['car_img4'] = $val['savename'];
						break;
					case 4:
						$car_imgs['car_img5'] = $val['savename'];
						break;
					case 5:
						$driving_img = $val['driving_img'];
						break;
				}

			}
		}		
		
		$uid = $this->GetUserId();
		$brand_id = isset($_POST['brand_id'])?$_POST['brand_id']:0;
		$series_id = isset($_POST['series_id'])?$_POST['series_id']:0;
		$model_id = isset($_POST['model_id'])?$_POST['model_id']:0;
		
		if ($series_id){
			$model_carseries = D(GROUP_NAME.'/Carseries');
			$series = $model_carseries->find($series_id);
			$fsid = $series['fsid'];
		}

		$data['uid'] = $uid;
		$data['user_name'] = $_POST['user_name'];
		$data['user_phone'] = $_POST['user_phone'];
		$data['brand_id'] = $brand_id;
		$data['series_id'] = $series_id;
		$data['model_id'] = $model_id;
		$data['fsid'] = $fsid;

		//$data['driving_img'] = $driving_img;
		$data['insurance_name'] = $_POST['insurance_name'];
		$data['loss_price'] = $_POST['loss_price'];
		$data['description'] = $_POST['description'];

		//有效期 15分钟
		$validity_time = 900;
		$data['validity_time'] = time()+$validity_time;

		$insurance_model = D("Insurance");
		if (false === $insurance_model->create($data)) {
			$this->error($insurance_model->getError());			
		}
		$insurance_id = $insurance_model->add();
		
		$insuranceimg_model = D("Insuranceimg");
		$data_img['insurance_id'] = $insurance_id;
		
		if($car_imgs) {
			foreach($car_imgs as $key=>$val) {
				$data_img['car_img'] = $val;
				$i = substr($key , -1);
				$data_img['car_location'] = $i;
				if (false === $insuranceimg_model->create($data_img)) {
					$this->error($insuranceimg_model->getError());			
				}
				$list=$insuranceimg_model->add ($data_img);  
			}
		}

		
		/*
		$fs_shop_model = D("shop_fs_relation");
		$map_fs['fsid'] = $_POST['fsid'];
		$shop_ids = $fs_shop_model->where($map_fs)->select();
		if($shop_ids) {
			foreach($shop_ids as $key=>$val) {
				$shop_ids_arr[] = $val['shopid'];
			}
		}
		
		$map_shop['safestate'] = 1;
		$map_shop['status'] = 1;
		$map_shop['id'] = array('in' , $shop_ids_arr);
		$shop_list = $shop_model->where()->select();
		if($shop_list) {
			foreach($shop_list as $key=>$val) {
				//$val['shop_mobile'];
				//$verify_str = "您的保险类订单".$Bidorder_id."号已完成。您已获得".$shop['shop_name']."店铺的现金抵用券".$Shopbid['rebate']."元请凭消费码至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".date('Y-m-d ',$BidOrder['start_time'])."至".date('Y-m-d',$BidOrder['end_time']).")优惠卷编码:".$rand_str;

				//$send_verify = array('phones'=>$BidorderList['mobile'],'content'=>$verify_str,);
				$this->curl_sms($send_verify);

			}
		}
		*/
		$this->success("提交成功!");
		
	}

	/*
		@author:ysh
		@function:用户查看车辆受损照片
		@time:2013/5/0
	*/
	function get_car_imgs() {
		$insurance_id = $_REQUEST['insurance_id'];
		$insuranceimg_model = D("insuranceimg");
		
		$car_location_arr = array(
			'1' => "正面",
			'2' => "左侧面",
			'3' => "右侧面",
			'4' => "背面",
			'5' => "远景照",	
		);
		$img_list = $insuranceimg_model->where(array('insurance_id'=>$insurance_id))->select();
		if($img_list) {
			foreach($img_list as $key=>$val) {
				$img_list[$key]['car_location'] = $car_location_arr[$val['car_location']];
			}
		}
		$this->assign("img_list",$img_list);
		$this->display("car_imgs");
	}
	
	/*
		@author:ysh
		@function:用户查看保险订单 被哪些4s店竞标 4s店竞价方案列表
		@time:2013/5/8
	*/
	function get_shopbidding() {
		$_REQUEST['long'] = 121.406646;
		$_REQUEST['lat'] = 31.166517;

		$id = $_REQUEST['insurance_id'];
		$order_by = $_REQUEST['order_by'];
		$order_type = $_REQUEST['order_type'];
		$shopbidding_model = D("Shopbidding");
		$bidorder_model = D("Bidorder");
		$shop_model = D("Shop");
		$map['insurance_id'] = $id;
		$map['xc_shopbidding.status'] = 1;
		// 计算总数
        $count = $shopbidding_model->where($map)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
		$field = array(
			'xc_shopbidding.id',
			'xc_shopbidding.create_time',
			'xc_shopbidding.servicing_time',
			'xc_shopbidding.scooter',
			'xc_shopbidding.rebate',
			'xc_shopbidding.remark',
			
			'xc_shop.shop_name',
			'xc_shop.comment_rate',
			'xc_shop.shop_maps',
		);
		if($order_by && $order_by != "distance") {
		   $list = $shopbidding_model->join('xc_shop ON xc_shopbidding.shop_id = xc_shop.id')->where($map)->field($field)->order("{$order_by} {$order_type}")->limit($p->firstRow.','.$p->listRows)->select();
		}else {
			$list = $shopbidding_model->join('xc_shop ON xc_shopbidding.shop_id = xc_shop.id')->where($map)->field($field)->order('xc_shopbidding.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		}

		if($list) {
			foreach($list as $key=>$val) {
                $shop_maps_arr = explode(',',$val['shop_maps']);
                $distance = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
				$list[$key]['distance'] =  round($distance/1000,2);
				
				$list[$key]['is_bidorder'] = 0;
				$is_bid = $bidorder_model->where(array('insurance_id'=>$id))->count();
				if($is_bid>0) {
					$list[$key]['is_bidorder'] = 1;
				}
			}
		}
		if($order_by == 'distance') {
			$count = count($list);
			for($i = 0; $i < $count; $i ++) {
				for($j = $count - 1; $j > $i; $j --) {
					if ($list[$j]['distance'] < $list[$j - 1]['distance']) {
						//交换两个数据的位置
						$temp = $list [$j];
						$list [$j] = $list [$j - 1];
						$list [$j - 1] = $temp;
					}
				}
			}
		}
		
		$this->assign('list',$list);
		$this->assign('id',$id);
		$this->assign('order_by',$order_by);
		$this->assign('order_type',$order_type);
		$this->display();
	}


	/*
		@author:ysh
		@function:用户选择4s店填写预约到店时间 确认提交订单
		@time:2013/5/24
	*/
	function select_tostore_time() {
		$id = $_REQUEST['shopbidding_id'];
		
		$shopbidding_model = D("Shopbidding");
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");
		$shop_model = D("Shop");
		$is_bid = $bidorder_model->where(array('bid_id'=>$id))->count();
		if($is_bid) {
			$this->error("已经预约无需重复下单");
			exit();
		}

		$shopbidding_info = $shopbidding_model->find($id);
		$insurance_info = $insurance_model->find($shopbidding_info['insurance_id']);
		$car_info = $this->get_car_info($insurance_info['brand_id'],$insurance_info['series_id'],$insurance_info['model_id']);
		$shop_info = $shop_model->find($shopbidding_info['shop_id']);

		$now = strtotime(date("Y-m-d"));
		$tostore_time_arr = array(
			$now,
		);
		for($i=0; $i<7; $i++) {
			$next_day = $now+86400;
			$tostore_time_arr[] = $next_day;
			$now = $next_day;
		}
		$WEEK = array(
			'1'=>'星期一',
			'2'=>'星期二',
			'3'=>'星期三',
			'4'=>'星期四',
			'5'=>'星期五',
			'6'=>'星期六',
			'0'=>'星期天',
		);
		foreach($tostore_time_arr as $key=>$val) {
			$tostore_time[$val] = date("Y-m-d",$val)." ".$WEEK[date("w",$val)];
		}

		for($ii=8; $ii<21; $ii++) {
			$hours[] = $var=sprintf("%02d", $ii);//生成4位数，不足前面补0   
		}
		$this->assign('id',$id);
		$this->assign('car_info',$car_info);
		$this->assign("shop_info",$shop_info);
		$this->assign("shopbidding_info",$shopbidding_info);
		$this->assign("insurance_info",$insurance_info);
		$this->assign("tostore_time",$tostore_time);
		$this->assign("hours",$hours);
		$this->display();
	}


	/*
		@author:ysh
		@function:用户选择4s店下单
		@time:2013/5/8
	*/
	function add_order() {
		$id = $_REQUEST['id'];
		
		$shopbidding_model = D("Shopbidding");
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");

		$is_bid = $bidorder_model->where(array('bid_id'=>$id))->count();
		if($is_bid) {
			$this->error("已经预约无需重复下单");
			exit();
		}

		$shopbidding_info = $shopbidding_model->find($id);
		$insurance_info = $insurance_model->find($shopbidding_info['insurance_id']);
		//插入订单
		$data['insurance_id'] = $shopbidding_info['insurance_id'];
		$data['shop_id'] = $shopbidding_info['shop_id'];
		$data['uid'] = $this->GetUserId();
		$data['truename'] = $insurance_info['user_name'];
		$data['mobile'] = $insurance_info['user_phone'];
		$data['bid_id'] = $shopbidding_info['id'];
		//$data['fav_id'] = $fav_id;
		$data['order_status'] = 0;
		$data['status'] = 1;
		$data['create_time'] = time();
		$tostore_time = $_POST['tostore_time']+($_POST['hours']*3600);
		$data['tostore_time'] = $tostore_time;
		$data['takecar_time'] = $tostore_time+($shopbidding_info['servicing_time']*86400);
		
		$bidorder_id = $bidorder_model->add($data);
		
		//修改insurance_status
		$insurance_data['id'] = $shopbidding_info['insurance_id'];
		$insurance_data['insurance_status'] = 3;
		$insurance_model->save($insurance_data);
		/*插入支付订单 用于合并支付
		$memberbidorder_model = D("memberbidorder");
		$data_member['insurance_id'] = $shopbidding_info['insurance_id'];
		$data_member['shopbidding_id'] = $id;
		$data_member['bidorder_id'] = $bidorder_id;
		$data_member['uid'] = $this->GetUserId();
		$data_member['shop_id'] = $shopbidding_info['shop_id'];
		$data_member['loss_price'] = $insurance_info['loss_price'];
		$data_member['create_time'] = time();
		$data_member['status'] = 1;
		$memberbidorder_model->add($data_member);
		*/
		$this->success('预约成功！');
	}


	
	/*
		@author:ysh
		@function:用户的保险订单列表-已下定的列表
		@time:2013/5/10
	*/
	function my_bidorder() {
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");
		$shopbidding_model = D("Shopbidding");
		$shop_model = D("Shop");
		$fs_model = D("fs");

		$uid = $this->GetUserId();
		$map['uid'] = $uid;
		//$map['state'] = 1;
		// 计算总数
        $count = $bidorder_model->where($map)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
		
		$order_status_arr = array(
			'0'=>'未确认',
			'1'=>'订单已确认',
			'2'=>'修理完毕收到材料',
			'3'=>'修理完毕材料已提交至保险公司',
			'4'=>'修理完毕保险公司已结算'
		);
		
		$pay_state = C("PAY_STATE");

        $list = $bidorder_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if($list) {
			foreach($list as $key=>$val) {
				$list[$key]['order_id'] = $this->get_orderid($val['id']);
				$list[$key]['order_status'] = $order_status_arr[$val['order_status']];
				$list[$key]['pay_status'] = $pay_state[$val['pay_status']];

				$shop_info = $shop_model->find($val['shop_id']);
				$list[$key]['shop_name'] = $shop_info['shop_name'];
				$insurance_info = $insurance_model->find($val['insurance_id']);
				$fs = $fs_model->find($insurance_info['fsid']);
				$insurance_info['fsname'] = $fs['fsname'];

				$shopbidding_info = $shopbidding_model->find($val['bid_id']);
				
				$list[$key]['insurance_info'] = $insurance_info;
				$list[$key]['shopbidding_info'] = $shopbidding_info;
			}
		}

		$this->assign('list',$list);
		$this->display();
	}

	/*
		@author:ysh
		@function:用户的保险订单详细
		@time:2013/5/10
	*/
	function get_bidorder_detail() {
		$id = $_REQUEST['id'];
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");
		$shopbidding_model = D("Shopbidding");
		$shop_model = D("Shop");
		$fs_model = D("fs");

		$order_status_arr = array(
			'0'=>'未确认',
			'1'=>'订单已确认',
			'2'=>'修理完毕收到材料',
			'3'=>'修理完毕材料已提交至保险公司',
			'4'=>'修理完毕保险公司已结算'
		);
		$pay_state = C("PAY_STATE");

        $list = $bidorder_model->find($id);
		if($list) {
			$list['order_id'] = $this->get_orderid($list['id']);
			$list['order_status'] = $order_status_arr[$list['order_status']];
			$list['pay_status'] = $pay_state[$list['pay_status']];

			$shop_info = $shop_model->find($list['shop_id']);
			$list['shop_name'] = $shop_info['shop_name'];
			$insurance_info = $insurance_model->find($list['insurance_id']);
			$fs = $fs_model->find($insurance_info['fsid']);
			$insurance_info['fsname'] = $fs['fsname'];

			$shopbidding_info = $shopbidding_model->getByInsurance_id($list['insurance_id']);
			$car_info = $this->get_car_info($insurance_info['brand_id'],$insurance_info['series_id'],$insurance_info['model_id']);

			$list['car_info'] = $car_info;
			$list['insurance_info'] = $insurance_info;
			$list['shopbidding_info'] = $shopbidding_info;
		}

		$this->assign('list',$list);
		$this->display();
	}

	/*
		@author:ysh
		@function:得到汽车信息
		@time:2013/5/24
	*/
	function get_car_info($brand_id,$series_id,$model_id) {
		if ($brand_id){
			$model_carbrand = D('Carbrand');
			$map_b['brand_id'] = $brand_id;
			$brand = $model_carbrand->where($map_b)->find();
		}
		if ($series_id){
			$model_carseries = D('Carseries');
			$map_s['series_id'] = $series_id;
			$series = $model_carseries->where($map_s)->find();
		}
		if ($model_id){
			$model_carmodel = D('Carmodel');
			$map_m['model_id'] = $model_id;
			$model = $model_carmodel->where($map_m)->find();
		}
		return $brand['brand_name']." ".$series['series_name']." ".$model['model_name'] ;
	}

	/*
		@author:chf
		@function:支付成功
		@time:2013/5/10
	*/
	function pay() {
		$bidorder_model = D("Bidorder");
		$map['id'] = $_REQUEST['id'];
	
		$bidorder_model->where($map)->data(array('pay_status'=>1))->save();
		echo '1';
	}


	public function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Driving/';
        $upload->saveRule = 'uniqid';

		$upload->thumb = true;
        $upload->thumbPrefix = 'thumb_';
		$resizeThumbSize_arr = array('60','45');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];

		$upload->uploadReplace = false;
		//$this->watermark = 1;水印
        return $upload;
    }

}