<?php
//用户个人中心controller
class MyhomeAction extends CommonAction {	
	protected $mRepireprocess;
	
	function __construct() {
		parent::__construct();
		if( true !== $this->login()){
			exit;
		}
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->MemberModel = D('member');//用户表
		$this->MembercarModel = D('Membercar');//我的车辆表
		$this->MembercouponModel = D('membercoupon');//优惠券用户表(团购现金券)
		$this->CouponModel = D('coupon');//优惠券
		$this->MembersalecouponModel = D('membersalecoupon');//抵用券领取表
		$this->SalecouponModel = D('salecoupon');//抵用券表
		$this->BidcouponModel = D('bidcoupon');//用户返利抵用券
		$this->ShopModel = D('shop');//店铺表
		$this->BidcommentModel = D('bidordercomment');//评论表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->reservation_order_model = D('reservation_order');
		$this->car_brand_model = M('tp_xieche.car_brand','xc_');  //车品牌 老数据,不用了
		$this->car_model_model = M('tp_xieche.car_model','xc_');  //车型号
		$this->car_style_model = M('tp_xieche.car_style','xc_');  //车型号
		
		$this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌	新数据
		$this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
		$this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
		
		
		$this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
		$this->item_model = M('tp_xieche.item_filter', 'xc_');  //新保养项目
		$this->technician_model = D('technician');  //技师表
		$this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表
		$this->mRepireprocess = D('repairprocess');//维修进度表
	}
	
	
	function mycarservice_detail(){
		$order_id = $this->get_true_orderid($this->_get('order_id'));
		if (!$order_id) {
			$this->error('订单号不能为空','/myhome');
		}
		 
		$condition['id'] = $order_id;
		$order_info = $this->reservation_order_model->where($condition)->find();
		 
		$uid = $this->GetUserId();
		if($order_info['uid'] != $uid){
			$this->error('很抱歉，您无法查看该订单信息','/myhome');
		}
		 
		
		//车型
		$style_param['model_id'] = $order_info['model_id'];
		$car_model = $this->carmodel_model->where($style_param)->find();
		$model_name = $car_model['model_name'];
		
		$series_id = $car_model['series_id'];
		$car_style = $this->carseries_model->where( array('series_id'=>$series_id) )->find();
		
		$style_name = $car_style['series_name'];
		
		$car_name = $style_name." - ".$model_name;;
		
		$brand_param['id'] = $car_style['brand_id'];
		$car_brand = $this->carbrand_model->where($brand_param)->find();
		if($car_brand){
			$brand_name = $car_brand['brand_name'];
			$car_name = $brand_name." - ".$car_name;
		}
		$order_info['car_name'] = $car_name;
		
		//商品详情
		$item_num = 1;
		$order_items = unserialize($order_info['item']);
	
		if( !empty( $order_items['oil_detail'] ) ){
			$item_oil_price = 0;
			$oil_data = $order_items['oil_detail'];
			foreach ( $oil_data as $id=>$num){
				if($num > 0){
					$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
					$item_oil_price += $res['price']*$num;
					$name = $res['name'];
				}
			}
			$oil_param['id'] =  $order_items['oil_id'];
			$item_list['0']['id'] = $order_items['oil_id'];
			$item_list['0']['name'] = $name;
			$item_list['0']['price'] = $item_oil_price;
			if (!$oil_param['id']){
				unset($item_list['0']);
			}
			if( $order_items['oil_id'] == '-1' ){
				$item_list['0']['id'] = 0;
				$item_list['0']['name'] = "自备配件";
				$item_list['0']['price'] = 0;
			}
			$item_num++;
		}
		if($order_items['filter_id']){
			if($order_items['filter_id'] == '-1'){	//自备配件的情况
				$item_list['1']['id'] = 0;
				$item_list['1']['name'] = "自备配件";
				$item_list['1']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['filter_id'];
				$item_list['1'] = $this->item_model->where($item_condition)->find();
				$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
			}
			$item_num++;
		}
		if($order_items['kongqi_id']){
			if( $order_items['kongqi_id'] == '-1' ){
				$item_list['2']['id'] = 0;
				$item_list['2']['name'] = "自备配件";
				$item_list['2']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['kongqi_id'];
				$item_list['2'] = $this->item_model->where($item_condition)->find();
				$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
			}
			$item_num++;
		}
		if($order_items['kongtiao_id']){
			if( $order_items['kongtiao_id'] == '-1' ){
				$item_list['3']['id'] = 0;
				$item_list['3']['name'] = "自备配件";
				$item_list['3']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['kongtiao_id'];
				$item_list['3'] = $this->item_model->where($item_condition)->find();
				$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
			}
			$item_num++;
		}
	
		$item_amount = 99;
        if ( $order_info['replace_code'] ) {
	        $value = $this->get_codevalue($order_info['replace_code']);//判断抵用卷的价钱
	        if($value != 99){
		        $this->assign('replace_value',$value);
	        	$item_amount = $item_amount - $value;	//加服务费，减抵用券费用
	        }else{
	        	$item_amount = 0;
	        }
        }

		if($order_info['order_type']==2){
			$item_amount = 0;
		}

        if(is_array($item_list)){
        	foreach ($item_list as $key => $value) {
        		$item_amount += $value['price'];
        	}
        }

		$order_info['true_id'] = $order_info['id'];
		$order_info['id'] = $this->get_orderid($order_info['id']);

		$this->assign('item_num',$item_num);
		$this->assign('replace_code', $order_info['replace_code']);
		$this->assign("item_list", $item_list);
		$this->assign("item_amount", $item_amount);
	
		$this->assign("order_info", $order_info);
	
		$this->display();
	}
	
	
	
	/*
	 @author:wwy
	@function:显示个人中心我的上门保养订单页(新)
	@time:2014-08-21
	*/
	function mycarservice(){
		Cookie::set('_currentUrl_', '/myhome/mycarservice');
		$map = array();
		//搜索
		if($_POST['id']){
			$map['id'] = $_POST['id'];
		}
		if($_POST['mobile']){
			$map['mobile'] = $_POST['mobile'];
		}
		if($_POST['licenseplate']){
			$map['licenseplate'] = $_POST['licenseplate'];
		}
		if($_POST['technician_id']){
			$map['technician_id'] = $_POST['technician_id'];
		}
		
		$uid = $this->GetUserId();
		if (!$uid){
			$this->error('很抱歉，您无法查看该订单信息','/index.php/myhome');
			return false;
		}
		
		$map['uid'] = $uid;
		
		// 计算总数
		$count = $this->reservation_order_model->where($map)->count();
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,40);
		// 分页显示输出
		$page = $p->show();
	
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		// 当前页数据查询
		$list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if(is_array($list)){
			foreach ($list as $key => $value) {
				$list[$key]['id'] = $this->get_orderid($value['id']);
				$list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);
	
				$list[$key]['status_name'] = $this->getStatusName($value['status']);
				
				if($value['technician_id']){
					$condition['id'] = $value['technician_id'];
					$technician_info = $this->technician_model->where($condition)->find();
					$list[$key]['technician_name'] = $technician_info['truename'];
				}
			}
		}
		$condition = array();
		$condition['status'] = 1;
		$technician_list = $this->technician_model->where($condition)->select();
		$this->assign('data', $map);
		$this->assign('list', $list);
		$this->assign('page',$page);
		$this->assign('technician_list', $technician_list);
		$this->display();
	}
	private function getPayStatusName($pay_status){
		switch ($pay_status) {
			case '0':
				return "未支付 ";
				break;
	
			case '1':
				return "已支付";
				break;
	
			case '2':
				return "申请退款";
				break;
	
			case '3':
				return "已退款";
				break;
	
			default:
				return "未支付";
				break;
		}
	}
	private function getStatusName($status){
		switch ($status) {
			case '0':
				return "等待处理 ";
				break;
	
			case '1':
				return "预约确认";
				break;
	
			case '2':
				return "已分配技师";
				break;
	
			case '3':
				return "服务已完成";
				break;
	
			default:
				return "等待处理";
				break;
		}
	}
	
	/*
	 @author:liuhui
	@function:维修进度
	@time:2014-8-21
	*/
	public function bidorder_progress(){
		$f_order_id = @$_GET['bidorder_id'];
		if (!$f_order_id) {
			$this->error('参数为空','/index.php/myhome');
		}
		$order_id = $this->get_true_orderid($f_order_id);
		$uid = $this->GetUserId();
		$where = array(
				'order_id'=>$order_id,
				'uid'=>$uid
		);
	
		$data = $this->mRepireprocess->where($where)->order('up_time desc')->select();
		$des = '';
		if ( !empty($data) ) {
			foreach ( $data as &$val){
				if ( $val['describe'] ) {
					$val['describe'] = unserialize($val['describe']);
					$val['count'] = count($val['describe']);
					$val['des_t'] = $val['describe'][0]['des'];
					unset($val);
				}
			}
		}
		$mBidorder = D("Bidorder");
		$where2 = array(
				'id'=>$order_id,
				'uid'=>$uid
		);
		$complete_status = $mBidorder->field('order_status,complete_time')->where($where2)->find();
		if ($complete_status) {
			$order_status = ( $complete_status['order_status'] == 4 ) ? 1 : 0;	//是否已经提车
			$this->assign('complete_time',$complete_status['complete_time']);
			$this->assign('order_status',$order_status);
		}
		$this->assign('data',$data);
		$this->display();
	}
	
	public function index_bak(){
	    $tab_n = isset($_COOKIE['tab_n'])?$_COOKIE['tab_n']:0;
	    $this->assign('tab_n',$tab_n);
	    Cookie::set('_currentUrl_', __SELF__);
		//R('Myhome/orderlist'); 
		R('Myhome/index1');
		//$this->display();
	}

	/*
		@author:chf
		@function:显示我的返利券
		@time:2013-12-17
	*/
		function mybidcoupon(){
			$map['uid'] = $_SESSION['uid'];
			// 计算总数
	        $count = $this->BidcouponModel->where($map)->count();
	        // 导入分页类
	        import("@.ORG.Util.Page");
	        // 实例化分页类
	        $p = new Page($count, 15);
	        // 分页显示输出
	        $page = $p->show();
			$data['Bidcoupon'] = $this->BidcouponModel->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
			
			if($data['Bidcoupon']){
				foreach($data['Bidcoupon'] as $k=>$v){
					$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
					 $data['Bidcoupon'][$k]['shop_name'] = $shop['shop_name'];
				}
			}
		
			$this->assign('data',$data);
			$this->assign('page',$page);	
			$this->display();
		}
	/*
		@author:chf
		@function:显示个人中心主页(旧上线删除)
		@time:2013-03-26
	*/
    public function index1(){
		
        $model_order = D('Order');
        $uid = $this->GetUserId();
        //$map_order = $map;
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		$onemonth_time = time()-date('t')*24*3600;
		$order_state = isset($_REQUEST['order_state'])?$_REQUEST['order_state']:'all';
		$order_date = isset($_REQUEST['order_date'])?$_REQUEST['order_date']:1;
		if ($order_state!='all'){
		    $map_order['order_state'] = $order_state;
		}
        if ($order_date==2){
		    $map_order['create_time'] = array(array('lt',$onemonth_time));
		}else {
		    $map_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		}
		$this->assign('order_state',$order_state);
		$this->assign('order_date',$order_date);
		$map_order['order_type'] = 0;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list){
            foreach ($list as $k=>$v){
				//echo $v['id']."<br>";
                $list[$k]['order_id'] = $this->get_orderid($v['id']);
				//echo $list[$k]['order_id']."<br>";
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->get_order_number($map_order);
        $this->display('orderlist');
    }

	/*
		@author:chf
		@function:显示个人中心主页(新主页)
		@time:2013-08-30
	*/
    public function index(){
        $model_order = D('Order');
        $uid = $this->GetUserId();
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		//onemonth_time = time()-date('t')*24*3600;
		//$map_order['create_time'] = array(array('lt',$onemonth_time));
		$map_order['iscomment'] = 0;
		$map_order['order_state'] = 2;
		$map_order['order_type'] = 0;
		$map_order['uid'] = $uid;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 5);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['order_id'] = $this->get_orderid($v['id']);
            }
        }
		/*查询最近1个月的订单*/
		//$month_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		/*未消费订单*/
		$consume['order_state'] = 1;
		$consume['uid'] = $uid;
		$momth_list = $model_order->where($consume)->order('id DESC')->select();
		if ($momth_list){
            foreach ($momth_list as $k=>$v){
                $momth_list[$k]['order_id'] = $this->get_orderid($v['id']);
            }
        }
		
		/*店铺信息*/
		$model_shop = D('Shop');
		if($this->city_name == "上海" ) {
			$map_sh['status'] = 1;
			$map_sh['shop_class'] = 1;
		}
		$map_sh['shop_city'] = $this->city_id;
		$shoplist = $model_shop->where($map_sh)->order("comment_rate DESC")->limit(3)->select();
		$shop_count = $model_shop->where($map_sh)->count();
		$this->assign('shop_count',$shop_count);
		if ($shoplist){
			$model_shop_fs_relation = D('Shop_fs_relation');

			foreach ($shoplist as $kk=>$vv){
				if (file_exists("UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
				$Timesale = $this->TimesaleModel->where(array('shop_id'=>$vv['id'],'status'=>1))->find();
				$Timesaleversion = $this->TimesaleversionModel->where(array('timesale_id'=>$Timesale['id'],'status'=>1))->find();
				$shoplist[$kk]['workhours_sale'] = $Timesaleversion['workhours_sale']*10;
			}
		}
		$this->assign('shoplist',$shoplist);
		
		/*查询优惠卷信息*/

		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		//$Coupon_map['coupon_type'] = $coupon_type;
		$Coupon = $this->CouponModel->where($Coupon_map)->order("id DESC")->limit(2)->select();
		foreach($Coupon as $C_k=>$C_v){
				$shop = $model_shop->where(array('id'=>$C_v['shop_id']))->find();
				$Coupon[$C_k]['shop_name'] = $shop['shop_name'];
				$Coupon[$C_k]['shop_address'] = $shop['shop_address'];
		}
		$this->assign('Coupon',$Coupon);
		/*查询优惠卷 团购券 抵用券数量 */
		$data['cash_coupon'] = $this->MembercouponModel->where(array('xc_membercoupon.uid'=>$uid,'xc_membercoupon.coupon_type'=>1,'xc_membercoupon.is_delete'=>0,'xc_coupon.is_delete'=>0))->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();//现金券数量
		$data['group_coupon'] = $this->MembercouponModel->where(array('xc_membercoupon.uid'=>$uid,'xc_membercoupon.coupon_type'=>2,'xc_membercoupon.is_delete'=>0,'xc_coupon.is_delete'=>0))->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();//团购券数量
		$data['sale_coupon'] = $this->SalecouponModel->where(array('uid'=>$uid,'is_delete'=>0))->count();//抵用券数量
		$this->assign('list',$list);//1个月前的订单LIST
		$this->assign('momth_list',$momth_list);//1个月的订单LIST
        $this->assign('page',$page);
        $this->assign('count',$count);
		$this->assign('data',$data);
        $this->get_order_number($map_order);
		$this->assign('member',$member);
        $this->display('account_index');
    }

	/*
		@author:chf
		@function:显示个人中心我的订单页(新)
		@time:2013-08-30
	*/
	function myorder(){
		Cookie::set('_currentUrl_', '/myhome/myorder');
        $model_order = D('Order');
        $uid = $this->GetUserId();
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		$onemonth_time = time()-date('t')*24*3600; 
		$order_state = isset($_REQUEST['order_state'])?$_REQUEST['order_state']:'all';
		$order_date = isset($_REQUEST['order_date'])?$_REQUEST['order_date']:1;
		
		if ($order_state!='all'){
			if($order_state == 5){
				$map_order['order_state'] = -1;
			}else{
				 $map_order['order_state'] = $order_state;
			}
		   
		}
        if ($order_date==2){
		    $map_order['create_time'] = array(array('lt',$onemonth_time));
		}else {
		    $map_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		}
		$this->assign('order_state',$order_state);
		$this->assign('order_date',$order_date);
		$map_order['order_type'] = 0;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_order->getlastSql();

        if ($list){
            foreach ($list as $k=>$v){
				
				if($v['membersalecoupon_id']){
					$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
					
					$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				
					$list[$k]['salecoupon_name'] = $Salecoupon['coupon_name'];
				}
				$list[$k]['order_id'] = $this->get_orderid($v['id']);
				
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->get_order_number($map_order);
		$this->display();
	}
	
	/*
		@author:chf
		@function:显示个人中心我的积分(新)
		@time:2013-09-01
	*/
	function mypoints(){
		$uid = $this->GetUserId();
        $model_point = D('Point');
        $map_p['uid'] = $uid;//取得UID
		$Memberinfo = $this->MemberModel->where($map_p)->find();
		$this->assign('Memberinfo',$Memberinfo);//得到用户积分信息
        // 计算总数
        $count = $model_point->where($map_p)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$point_info =$model_point->where($map_p)->order('pid DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($point_info){
            foreach ($point_info as $k=>$v){
                $point_info[$k]['order_id'] = $this->get_orderid($v['orderid']);
            }
        }
        $this->assign('point_info',$point_info);
        $this->assign('page',$page);
        $this->display('mypoints');
	
	
	}

    public function mycoupon1order(){
        $model_order = D('Order');
        $uid = $this->GetUserId();
        //$map_order = $map;
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		$onemonth_time = time()-date('t')*24*3600;
		$order_state = isset($_REQUEST['order_state'])?$_REQUEST['order_state']:'all';
		$order_date = isset($_REQUEST['order_date'])?$_REQUEST['order_date']:1;
		if ($order_state!='all'){
		    $map_order['order_state'] = $order_state;
		}
        if ($order_date==2){
		    $map_order['create_time'] = array(array('lt',$onemonth_time));
		}else {
		    $map_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		}
		$this->assign('order_state',$order_state);
		$this->assign('order_date',$order_date);
		$map_order['order_type'] = 1;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->get_order_number($map_order);
        $this->display();
    }
    public function mycoupon2order(){
        $model_order = D('Order');
        $uid = $this->GetUserId();
        //$map_order = $map;
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		$onemonth_time = time()-date('t')*24*3600;
		$order_state = isset($_REQUEST['order_state'])?$_REQUEST['order_state']:'all';
		$order_date = isset($_REQUEST['order_date'])?$_REQUEST['order_date']:1;
		if ($order_state!='all'){
		    $map_order['order_state'] = $order_state;
		}
        if ($order_date==2){
		    $map_order['create_time'] = array(array('lt',$onemonth_time));
		}else {
		    $map_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		}
		$this->assign('order_state',$order_state);
		$this->assign('order_date',$order_date);
		$map_order['order_type'] = 2;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->get_order_number($map_order);
        $this->display();
        //R('Order/orderlist');
    }
    public function get_order_number($map_order){
        $model_order = D('Order');
        //未完成的订单数
        $map_order['order_state'] = array('lt',2);
        $count1 = $model_order->where($map_order)->count();
        //已完成的订单数
        $map_order['order_state'] = 2;
        $count2 = $model_order->where($map_order)->count();
        //已取消订单数
        $map_order['order_state'] = -1;
        $count3 = $model_order->where($map_order)->count();
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count3',$count3);
    }
    public function show_order_detail(){
        R('Order/show_order_detail');
    }
    
	/*
		@author:chf
		@function:显示我的车辆管理(新)
		@time:2013-9-2
	*/
	function my_car(){
		$uid = $this->GetUserId();
        $map_c['uid'] = $uid;
        $map_c['status'] = 1;
        $membercar = $this->MembercarModel->where($map_c)->select();
        $this->assign('membercar',$membercar);
		$this->display();
	}

	/*
		@author:chf
		@function:显示我的车辆管理(旧上线删除)
		@time:2013-9-2
	*/
    public function mycarlist(){
        $uid = $this->GetUserId();
        $model_membercar = D('Membercar');
        $map_c['uid'] = $uid;
        $map_c['status'] = 1;
        $membercar = $model_membercar->where($map_c)->select();
        $this->assign('membercar',$membercar);
        $this->display();
    }
    /*
		@author:chf
		@function:显示修改用户信息页
		@time:2013-04-18
	*/
    public function myinfo(){
        Cookie::set('_currentUrl_', '/index.php/myhome/myinfo');
        $uid = $this->GetUserId();
        $model_member = D('Member');
        $map_m['uid'] = $uid;
        $map_m['status'] = 1;
        $member = $model_member->where($map_m)->find();
        $this->assign('member',$member);
        $this->display('my_info');
    }
    
    public function mycoupon1(){
        $uid = $this->GetUserId();
		$is_pay = $this->request("is_pay");
        $model_membercoupon = D('Membercoupon');
        $model_shop = D('Shop');
		$couponmap['xc_membercoupon.uid'] = $uid;
		$couponmap['xc_membercoupon.is_delete'] = 0;
		$couponmap['xc_coupon.is_delete'] = 0;
		$couponmap['xc_membercoupon.coupon_type'] = 1;
		if($is_pay !="") {
			$couponmap['xc_membercoupon.is_pay'] = $is_pay;
			$this->assign("is_pay",$is_pay);
		}
		// 计算总数
        $count = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$couponinfo = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("xc_membercoupon.membercoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_membercoupon->getLastSql();
		if ($couponinfo){
		    foreach ($couponinfo as $k=>$v){
		        $shop_id = $v['shop_id'];
		        $map_s['id'] = $shop_id;
		        $shopinfo = $model_shop->where($map_s)->find();
		        $couponinfo[$k]['shop_name'] = $shopinfo['shop_name'];
		        $couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
		        $couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
		        $couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
		    }
		}
		$this->assign('couponinfo',$couponinfo);
        $this->assign('page',$page);
        $this->display('my_coupon1');
    }
    public function mycoupon2(){
        $uid = $this->GetUserId();
		$is_pay = $this->request("is_pay");
        $model_membercoupon = D('Membercoupon');
        $model_shop = D('Shop');
		$couponmap['xc_membercoupon.uid'] = $uid;
		$couponmap['xc_membercoupon.is_delete'] = 0;
		$couponmap['xc_coupon.is_delete'] = 0;
		$couponmap['xc_membercoupon.coupon_type'] = 2;
		if($is_pay !="") {
			$couponmap['xc_membercoupon.is_pay'] = $is_pay;
			$this->assign("is_pay",$is_pay);
		}
		// 计算总数
        $count = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$couponinfo = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("xc_membercoupon.membercoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
        if ($couponinfo){
		    foreach ($couponinfo as $k=>$v){
		        $shop_id = $v['shop_id'];
		        $map_s['id'] = $shop_id;
		        $shopinfo = $model_shop->where($map_s)->find();
		        $couponinfo[$k]['shop_name'] = $shopinfo['shop_name'];
		        $couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
		        $couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
		        $couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
		    }
		}
		$this->assign('couponinfo',$couponinfo);
        $this->assign('page',$page);
        $this->display('my_coupon2');
    }
    
	/*
		@author:ysh
		@function:用户的抵用券
		@time:2013/9/2
	*/
	public function my_salecoupon(){
        $uid = $this->GetUserId();
		
        $model_membersalecoupon = D('Membersalecoupon');
        $model_shop = D('Shop');
		$couponmap['xc_membersalecoupon.uid'] = $uid;
		$couponmap['xc_membersalecoupon.is_delete'] = 0;
		$couponmap['xc_salecoupon.is_delete'] = 0;
		$couponmap['xc_salecoupon.coupon_type'] = '1';
		// 计算总数
        $count = $model_membersalecoupon->where($couponmap)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.coupon_id")->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$couponinfo = $model_membersalecoupon->where($couponmap)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
        if ($couponinfo){
		    foreach ($couponinfo as $k=>$v){
				if( $v['shop_id'] ) {
					$shop_id = $v['shop_id'];
					$map_s['id'] = $shop_id;
					$shopinfo = $model_shop->where($map_s)->find();
					$couponinfo[$k]['shop_name'] = $shopinfo['shop_name'];
					$couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
					$couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
					$couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
				}
		    }
		}
		$this->assign('couponinfo',$couponinfo);
        $this->assign('page',$page);
        $this->display('my_salecoupon');
    }

	/*
		@author:ysh
		@function:用户的礼品卡
		@time:2014/3/18
	*/
	public function my_salecoupon2(){
        $uid = $this->GetUserId();
		
        $model_membersalecoupon = D('Membersalecoupon');
        $model_shop = D('Shop');

		//------------------您购买的礼品卡----------------start
		$couponmap_all['xc_membersalecoupon.uid'] = $uid;
		$couponmap_all['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_all['xc_salecoupon.is_delete'] = 0;
		$couponmap_all['xc_salecoupon.coupon_type'] = '2';
		// 计算总数
        $count_all = $model_membersalecoupon->where($couponmap_all)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数
		$p_all = new AjaxPage($count_all, $limitRows,"get_comment_all"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_all->firstRow . "," . $p_all->listRows;

		$page_all = $p_all->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_all = $model_membersalecoupon->where($couponmap_all)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->group('xc_membersalecoupon.start_time')->select();
		
		//------------------您购买的礼品卡----------------end
		
		//------------------已使用的礼品卡----------------start
		$couponmap_use['xc_membersalecoupon.uid'] = $uid;
		$couponmap_use['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_use['xc_salecoupon.is_delete'] = 0;
		$couponmap_use['xc_salecoupon.coupon_type'] = '2';
		$couponmap_use['xc_membersalecoupon.is_use'] = '1';
		// 计算总数
        $count_use = $model_membersalecoupon->where($couponmap_use)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_use = new AjaxPage($count_use, $limitRows,"get_comment_use"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_use->firstRow . "," . $p_use->listRows;

		$page_use = $p_use->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_use = $model_membersalecoupon->where($couponmap_use)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------已使用的礼品卡----------------end

		//------------------未使用的礼品卡----------------start
		$couponmap_notuse['xc_membersalecoupon.uid'] = $uid;
		$couponmap_notuse['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_notuse['xc_salecoupon.is_delete'] = 0;
		$couponmap_notuse['xc_salecoupon.coupon_type'] = '2';
		$couponmap_notuse['xc_membersalecoupon.is_use'] = '0';
		// 计算总数
        $count_notuse = $model_membersalecoupon->where($couponmap_notuse)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_notuse = new AjaxPage($count_notuse, $limitRows,"get_comment_notuse"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_notuse->firstRow . "," . $p_notuse->listRows;

		$page_notuse = $p_notuse->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_notuse = $model_membersalecoupon->where($couponmap_notuse)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------未使用的礼品卡----------------end

		//------------------已过期的礼品卡----------------start
		$couponmap_passtime['xc_membersalecoupon.uid'] = $uid;
		$couponmap_passtime['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_passtime['xc_salecoupon.is_delete'] = 0;
		$couponmap_passtime['xc_salecoupon.coupon_type'] = '2';
		$couponmap_passtime['xc_membersalecoupon.is_use'] = '0';
		$couponmap_passtime['xc_salecoupon.end_time'] = array('lt',time());
		// 计算总数
        $count_passtime = $model_membersalecoupon->where($couponmap_passtime)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_passtime = new AjaxPage($count_passtime, $limitRows,"get_comment_passtime"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_passtime->firstRow . "," . $p_passtime->listRows;

		$page_passtime = $p_passtime->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_passtime = $model_membersalecoupon->where($couponmap_passtime)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------已过期的礼品卡----------------end

		$this->assign('couponinfo_all',$couponinfo_all);
		$this->assign('couponinfo_use',$couponinfo_use);
		$this->assign('couponinfo_notuse',$couponinfo_notuse);
		$this->assign('couponinfo_passtime',$couponinfo_passtime);

        $this->assign('page_all',$page_all);
		$this->assign('page_use',$page_use);
		$this->assign('page_notuse',$page_notuse);
		$this->assign('page_passtime',$page_passtime);

        $this->display('my_salecoupon2');
    }

	/*
		@author:chf
		@function:显示积分页面 (旧上线后可删除)
		@time:2013-04-18
	*/
    public function mypoint(){
        $uid = $this->GetUserId();
        $model_point = D('Point');
        $map_p['uid'] = $uid;//取得UID
		$Memberinfo = $this->MemberModel->where($map_p)->find();
		$this->assign('Memberinfo',$Memberinfo);//得到用户积分信息
        // 计算总数
        $count = $model_point->where($map_p)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$point_info =$model_point->where($map_p)->order('pid DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($point_info){
            foreach ($point_info as $k=>$v){
                $point_info[$k]['order_id'] = $this->get_orderid($v['orderid']);
            }
        }
        $this->assign('point_info',$point_info);
        $this->assign('page',$page);
        $this->display();
    }
    
    public function pointrule(){
        $this->display();
    }
    
    public function registerrecommend(){
        //订单信息
		$model_point = D('Point');
		$model_member = D('Member');
        $uid = $this->GetUserId();
        $uidadd = $uid+UID_ADD;
		//用户注册推荐地址
		$register_url = WEB_ROOT.'/index.php/member/add/uid/'.$uidadd.'/registercode/'.md5($uidadd.REGISTER_CODE);
		$this->assign('register_url',$register_url);
		
        //推荐注册的用户
		$model_registerrecommend = D('registerrecommend');
		$map['ruid'] = $uid;
		$registerrecommend_list = $model_registerrecommend->where($map)->select();
		$registerre_list = array();
		if (!empty($registerrecommend_list)){
		    foreach ($registerrecommend_list as $key=>$user){
		        $registerre_list [$key] = $model_member->find($user['uid']);
		        unset($map);
		        $map['uid'] = $uid;
		        $map['order_uid'] = $user['uid'];
		        $point_number = $model_point->where($map)->sum('point_number');
		        if ($point_number>0){
		            $registerre_list [$key]['point_number'] = $point_number;
		        }else {
		            $registerre_list [$key]['point_number'] = 0;
		        }
		        unset($point_number);
		    }
		}
		
        $this->display();
    }
    

	/*
		@author:chf
		@function:订单详情显示页(旧上线删除)
		@time:2013-03-28
	*/
    public function orderdetailold(){
		$model_shop = D('Shop');
		$model_point = D('Point');
		$ProductModel = D('Product');
		$ServiceitemModel = D('Serviceitem');//服务项目对应表
        $uid = $this->GetUserId();
		
        $order_id = $this->get_true_orderid($_REQUEST['order_id']);
        $model_order = D('Order');
        $map_o['id'] = $order_id;
        $map_o['uid'] = $uid;
		
        $order = $model_order->where($map_o)->find();
        
        if ($order){
            $order['order_id'] = $order['id'];
            $map_p['orderid'] = $order['id'];
            $map_p['uid'] = $uid;
            $point = $model_point->where($map_p)->find();
            if ($point){
                $order['point'] = $point['point_number'];
            }
        }
        if ($order){
            if ($order['workhours_sale']){
                if ($order['workhours_sale']==1){
                    $order['workhours_sale_str'] = "无折扣";
                }elseif($order['workhours_sale'] == '0.00'){
                    $order['workhours_sale_str'] = "免工时费";
                }else{
                    $order['workhours_sale_str'] = ($order['workhours_sale']*10)."折";
                }
            }else{
                $order['workhours_sale_str'] = "免工时费";
            }
            if ($order['product_sale']){
                if ($order['product_sale']==1){
                    $order['product_sale_str'] = "无折扣";
                }elseif($order['product_sale'] == '0.00'){
                    $order['product_sale_str'] = "免工时费";
                }else{
                    $order['product_sale_str'] = ($order['product_sale']*10)."折";
                }
            }else{
                $order['product_sale_str'] = "免零件费";
            }
            $service_ids = $order['service_ids'];
            $model_serviceitem = D('serviceitem');
            $map_s['id'] = array('in',$service_ids);
            $serviceitem = $model_serviceitem->where($map_s)->select();
            $serviceitem_name = array();
            if ($serviceitem){
                foreach ($serviceitem as $_k=>$_v){
					
                    $serviceitem_name[] = $_v['name'];
                }
            }
            $this->assign('serviceitem_name',$serviceitem_name);
			
            $order['serviceitem_name'] = implode(',',$serviceitem_name);
			
            if ($order['model_id']){
                $model_id = $order['model_id'];
                $car_arr = array();
                $model_carmodel = D('Carmodel');
                $model_carseries = D('Carseries');
                $model_carbrand = D('Carbrand');
                $map_m['model_id'] = $model_id;
                $carmodel = $model_carmodel->where($map_m)->find();
                $series_id = $carmodel['series_id'];
                $map_se['series_id'] = $series_id;
                $carseries = $model_carseries->where($map_se)->find();
                $map_b['brand_id'] = $carseries['brand_id'];
                $carbrand = $model_carbrand->where($map_b)->find();
                $car_arr['brand_name'] = $carbrand['brand_name'];
                $car_arr['series_name'] = $carseries['series_name'];
                $car_arr['model_name'] = $carmodel['model_name'];
                $this->assign('car_arr',$car_arr);
            }
            
            if ($order['shop_id']){
                $shop_id = $order['shop_id'];
               
                $map_shop['id'] = $shop_id;
                $shop = $model_shop->where($map_shop)->find();
                $this->assign('shop',$shop);

            }
			/*得到产品详细表信息*/
			$Product['service_id'] = array('in',$order['service_ids']);
            $Product['model_id'] = array('eq',$order['model_id']);
			$ProductList = $ProductModel->where($Product)->select();
			if($ProductList){
				foreach($ProductList as $Pr_k=>$Pr_v){
					$ArrVersionid[] = $Pr_v['versionid'];
					$ArrProductid[] = $Pr_v['id'];
				}
			}
			$StrVersionid = implode(',', $ArrVersionid);
            $StrProductid = implode(',', $ArrProductid);

			$Product_map['id'] = array('in',$StrProductid);
			if(!empty($order['service_ids'])){
				$ArrSelectservices = explode(',',$order['service_ids']);
				 foreach ($ArrSelectservices as $Sel_key=>$Sel_val){
					if ($Sel_val){
						$Product_map['service_id'] = array('eq',$Sel_val);
						$ListProduct[$Sel_key]['productinfo'] = $ProductModel->where($Product_map)->find();
						$ListProduct[$Sel_key]['service_id'] = $Sel_val;
					}
				}
			}

			if($order['product_sale'] == 0.00){
				$ArrSale['product_sale'] = '无折扣';
				$ValueSale['product_sale'] = 1;
			}else{
				$ArrSale['product_sale'] = ($order['product_sale']*10)."折";
				$ValueSale['product_sale'] = $order['product_sale'];
			}
			
			if($order['workhours_sale'] == 0.00){
				$ArrSale['workhours_sale'] = '无折扣';
				$ValueSale['workhours_sale'] = 1;
			}else{
				if($order['workhours_sale'] == '-1'){
					$ArrSale['workhours_sale'] = 0;
					$ValueSale['workhours_sale'] = '全免';
				}else{
					$ArrSale['workhours_sale'] = ($order['workhours_sale'] * 10)."折";
					$ValueSale['workhours_sale'] = $order['workhours_sale'];
				}
			}

			if(isset($ListProduct) && !empty($ListProduct)){
				
				foreach($ListProduct as $Lp_key=>$Lp_val){
					 $ListProduct[$Lp_key]['ServerName'] = $ServiceitemModel->getById($Lp_val['service_id']);
					if($Lp_val['productinfo']['product_detail']){
						 $ListDetail = unserialize($Lp_val['productinfo']['product_detail']);

						if($ListDetail){
							foreach($ListDetail as $Ld_key=>$Ld_val){
								//echo $Ld_val['Midl_name'];
								//数量*单价
								$data[$kk]['total'] = $Ld_val['quantity'] * $Ld_val['price'];
								$AllTotal += $data[$kk]['total'];//总共花费金额
								if($Ld_val['Midl_name'] != '工时费'){
										$ListProduct[$Lp_key]['test'][] = array(
										'Midl_name' => $Ld_val['Midl_name'],
										'price' => $Ld_val['price'],
										'quantity' => $Ld_val['quantity'],
										'unit' => $Ld_val['unit'],
										'product_sale' => $ArrSale['product_sale'],
									);
									$price['ProductPrice']+= $Ld_val['price'];//总零件费
								}
								else{
										$ListProduct[$Lp_key]['test'][] = array(
										'Midl_name' => $Ld_val['Midl_name'],
										'price' => $Ld_val['price'],
										'quantity' => $Ld_val['quantity'],
										'unit' => $Ld_val['unit'],
										'product_sale' => $ArrSale['product_sale'],
									);
									
									$price['WorkPrice']+= $Ld_val['price'];//总工时费
									
									$workhours_price += $data[$kk]['total'];
									$workhours_price_sale += $data[$kk]['after_sale_total'];
								}

								$all_after_total += $data[$kk]['after_sale_total'];

							}
						}
					}
				}
				
				$price['SaleProductPrice'] = $price['ProductPrice'] * $ValueSale['product_sale'];//打折后的总零件费
				$price['FavProductPrice'] = $price['ProductPrice'] - $price['SaleProductPrice'];//优惠零件的差价
				$price['SaleWorkPrice'] = $price['WorkPrice'] * $ValueSale['workhours_sale'];//打折后的总工时费;
				$price['FavWorkPrice'] = $price['WorkPrice'] * $ValueSale['workhours_sale'];//优惠总工时费的差价;

				$price['Allprice'] = $price['ProductPrice'] + $price['WorkPrice'];//工时+零件总价
				$price['SaleAllPrice'] = $price['SaleProductPrice'] + $price['SaleWorkPrice'];//工时+零件优惠价
				$price['FavAllPrice'] = $price['Allprice'] - $price['SaleAllPrice'];//优惠总工时+零件费的差价;
				$this->assign('price',$price);
				$this->assign('data',$ListProduct);
				$this->assign('product_sale',$ArrSale['product_sale']);
				$this->assign('workhours_sale',$ArrSale['workhours_sale']);
				
			}
			
			

			/*得到产品详细表信息*/
			/*
            //图片文件名      '店铺id_服务id_产品id_modelid'
    		$product_img_str = $order['shop_id'].'_'.$order['model_id'].'_'.$order['service_ids'].'_'.$order['productversion_ids'].'_'.$order['timesaleversionid']."_".$order['workhours_sale']."_".$order['product_sale'];
    		if ($order['coupon_id']){
    		   $product_img_str .= '_'.$order['coupon_id'];
    		}
			
    		$img_name = sha1($product_img_str).'.png';
    		
    		$this->assign('model_id',$order['model_id']);
    		$this->assign('img_name',$img_name);
			*/
        }
		
        $this->assign('order_id',$_REQUEST['order_id']);
        $this->assign('order',$order);
        $this->display('orderdetail');
    }

	/*
		@author:chf
		@function:订单详情显示页(新)
		@time:2013-03-28
	*/
    public function orderdetail(){
		$model_shop = D('Shop');
		$model_point = D('Point');
		$ProductModel = D('Product');
		$ServiceitemModel = D('Serviceitem');//服务项目对应表
        $uid = $this->GetUserId();
		
        $order_id = $this->get_true_orderid($_REQUEST['order_id']);
        $model_order = D('Order');
        $map_o['id'] = $order_id;
        $map_o['uid'] = $uid;
		
        $order = $model_order->where($map_o)->find();
        
        if ($order){
            $order['order_id'] = $order['id'];
            $map_p['orderid'] = $order['id'];
            $map_p['uid'] = $uid;
            $point = $model_point->where($map_p)->find();
            if ($point){
                $order['point'] = $point['point_number'];
            }
        }
        if ($order){
            if ($order['workhours_sale']){
                if ($order['workhours_sale']=='-1'){
                    $order['workhours_sale_str'] = "免工时费";
                }elseif($order['workhours_sale'] == '0.00'){
                    $order['workhours_sale_str'] = "无折扣";
                }else{
                    $order['workhours_sale_str'] = ($order['workhours_sale']*10)."折";
                }
            }else{
                $order['workhours_sale_str'] = "无折扣";
            }
            if ($order['product_sale']){
                if ($order['product_sale']=='-1'){
                    $order['product_sale_str'] = "免零件费";
                }elseif($order['product_sale'] == '0.00'){
                    $order['product_sale_str'] = "无折扣";
                }else{
                    $order['product_sale_str'] = ($order['product_sale']*10)."折";
                }
            }else{
                $order['product_sale_str'] = "无折扣";
            }
            $service_ids = $order['service_ids'];
            $model_serviceitem = D('serviceitem');
            $map_s['id'] = array('in',$service_ids);
            $serviceitem = $model_serviceitem->where($map_s)->select();
            $serviceitem_name = array();
            if ($serviceitem){
                foreach ($serviceitem as $_k=>$_v){
					
                    $serviceitem_name[] = $_v['name'];
                }
            }
            $this->assign('serviceitem_name',$serviceitem_name);
			
            $order['serviceitem_name'] = implode(',',$serviceitem_name);
			
            if ($order['model_id']){
                $model_id = $order['model_id'];
                $car_arr = array();
                $model_carmodel = D('Carmodel');
                $model_carseries = D('Carseries');
                $model_carbrand = D('Carbrand');
                $map_m['model_id'] = $model_id;
                $carmodel = $model_carmodel->where($map_m)->find();
                $series_id = $carmodel['series_id'];
                $map_se['series_id'] = $series_id;
                $carseries = $model_carseries->where($map_se)->find();
                $map_b['brand_id'] = $carseries['brand_id'];
                $carbrand = $model_carbrand->where($map_b)->find();
                $car_arr['brand_name'] = $carbrand['brand_name'];
                $car_arr['series_name'] = $carseries['series_name'];
                $car_arr['model_name'] = $carmodel['model_name'];
                $this->assign('car_arr',$car_arr);
            }
            
            if ($order['shop_id']){
                $shop_id = $order['shop_id'];
               
                $map_shop['id'] = $shop_id;
                $shop = $model_shop->where($map_shop)->find();
                $this->assign('shop',$shop);

            }
			/*得到产品详细表信息*/
			$Product['service_id'] = array('in',$order['service_ids']);
            $Product['model_id'] = array('eq',$order['model_id']);
			$ProductList = $ProductModel->where($Product)->select();
			if($ProductList){
				foreach($ProductList as $Pr_k=>$Pr_v){
					$ArrVersionid[] = $Pr_v['versionid'];
					$ArrProductid[] = $Pr_v['id'];
				}
			}
			$StrVersionid = implode(',', $ArrVersionid);
            $StrProductid = implode(',', $ArrProductid);

			$Product_map['id'] = array('in',$StrProductid);
			if(!empty($order['service_ids'])){
				$ArrSelectservices = explode(',',$order['service_ids']);
				 foreach ($ArrSelectservices as $Sel_key=>$Sel_val){
					if ($Sel_val){
						$Product_map['service_id'] = array('eq',$Sel_val);
						$ListProduct[$Sel_key]['productinfo'] = $ProductModel->where($Product_map)->find();
						$ListProduct[$Sel_key]['service_id'] = $Sel_val;
					}
				}
			}

			if($order['product_sale'] == 0.00){
				$ArrSale['product_sale'] = '无折扣';
				$ValueSale['product_sale'] = 1;
			}else{
				$ArrSale['product_sale'] = ($order['product_sale']*10)."折";
				$ValueSale['product_sale'] = $order['product_sale'];
			}
			
			if($order['workhours_sale'] == 0.00){
				$ArrSale['workhours_sale'] = '无折扣';
				$ValueSale['workhours_sale'] = 1;
			}else{
				if($order['workhours_sale'] == '-1'){
					$ArrSale['workhours_sale'] = 0;
					$ValueSale['workhours_sale'] = '全免';
				}else{
					$ArrSale['workhours_sale'] = ($order['workhours_sale'] * 10)."折";
					$ValueSale['workhours_sale'] = $order['workhours_sale'];
				}
			}

			if(isset($ListProduct) && !empty($ListProduct)){
				
				foreach($ListProduct as $Lp_key=>$Lp_val){
					 $ListProduct[$Lp_key]['ServerName'] = $ServiceitemModel->getById($Lp_val['service_id']);
					if($Lp_val['productinfo']['product_detail']){
						 $ListDetail = unserialize($Lp_val['productinfo']['product_detail']);

						if($ListDetail){
							foreach($ListDetail as $Ld_key=>$Ld_val){
								//echo $Ld_val['Midl_name'];
								//数量*单价
								$data[$kk]['total'] = $Ld_val['quantity'] * $Ld_val['price'];
								$AllTotal += $data[$kk]['total'];//总共花费金额
								if($Ld_val['Midl_name'] != '工时费'){
										$ListProduct[$Lp_key]['test'][] = array(
										'Midl_name' => $Ld_val['Midl_name'],
										'price' => $Ld_val['price'],
										'quantity' => $Ld_val['quantity'],
										'unit' => $Ld_val['unit'],
										'product_sale' => $ArrSale['product_sale'],
									);
									$price['ProductPrice']+= $Ld_val['price'];//总零件费
								}
								else{
										$ListProduct[$Lp_key]['test'][] = array(
										'Midl_name' => $Ld_val['Midl_name'],
										'price' => $Ld_val['price'],
										'quantity' => $Ld_val['quantity'],
										'unit' => $Ld_val['unit'],
										'product_sale' => $ArrSale['product_sale'],
									);
									
									$price['WorkPrice']+= $Ld_val['price'];//总工时费
									
									$workhours_price += $data[$kk]['total'];
									$workhours_price_sale += $data[$kk]['after_sale_total'];
								}

								$all_after_total += $data[$kk]['after_sale_total'];

							}
						}
					}
				}
				$price['SaleProductPrice'] = $price['ProductPrice'] * $ValueSale['product_sale'];//打折后的总零件费
				$price['FavProductPrice'] = $price['ProductPrice'] - $price['SaleProductPrice'];//优惠零件的差价
				$price['SaleWorkPrice'] = $price['WorkPrice'] * $ValueSale['workhours_sale'];//打折后的总工时费;
				$price['FavWorkPrice'] = $price['WorkPrice'] * $ValueSale['workhours_sale'];//优惠总工时费的差价;

				$price['Allprice'] = $price['ProductPrice'] + $price['WorkPrice'];//工时+零件总价
				$price['SaleAllPrice'] = $price['SaleProductPrice'] + $price['SaleWorkPrice'];//工时+零件优惠价
				$price['FavAllPrice'] = $price['Allprice'] - $price['SaleAllPrice'];//优惠总工时+零件费的差价;
				$this->assign('price',$price);
				$this->assign('data',$ListProduct);
				$this->assign('product_sale',$ArrSale['product_sale']);
				$this->assign('workhours_sale',$ArrSale['workhours_sale']);
				
			}

			//是否存在服务顾问
			if($order['servicemember_id']) {
				$model_servicemember = D("Servicemember");
				$servicemember = $model_servicemember->find($order['servicemember_id']);
				$this->assign("servicemember",$servicemember);
			}
        }
		
        $this->assign('order_id',$_REQUEST['order_id']);
        $this->assign('order',$order);
        $this->display('my_order_detail');
    }
    
	/*
		@author:chf
		@function:添加我的车辆  (新)
		@time:2013-09-02
	
	*/
	 public function mycaradd(){
        Cookie::set('_currentUrl_', '/myhome/mycaradd');
        $this->display();
    }

	/*
		@author:chf
		@function:添加我的车辆  (旧上线删除)
		@time:2013-09-02
	
	*/
    public function caradd(){
        Cookie::set('_currentUrl_', '/myhome/mycarlist');
        $this->display();
    }
    
	/*
		@author:chf
		@function:修改我的车辆  (新)
		@time:2013-09-02
	
	*/
	function change_my_car(){
		$u_c_id = $_REQUEST['u_c_id'];
        $uid = $this->GetUserId();
        if ($u_c_id){
            $model_membercar = D('Membercar');
            $map_mc['u_c_id'] = $u_c_id;
            $map_mc['uid'] = $uid;
            $vo = $model_membercar->where($map_mc)->find();
            if (isset($vo['car_number']) and !empty($vo['car_number'])){
                $car_number_arr = explode('_',$vo['car_number']);
                if (isset($car_number_arr[1])){
                    $vo['s_pro'] = $car_number_arr[0];
                    $vo['car_number'] = $car_number_arr[1];
                }else{
                    $vo['s_pro'] = '';
                    $vo['car_number'] = $car_number_arr[0];
                }
            }
            $this->assign('vo',$vo);
        }else{
            $this->error('没找到车辆信息','/myhome');
        }
        $this->display();
	}

	/*
		@author:chf
		@function:修改我的车辆  (旧上线可删除)
		@time:2013-09-02
	
	*/
    public function caredit(){
        $u_c_id = $_REQUEST['u_c_id'];
        $uid = $this->GetUserId();
        if ($u_c_id){
            $model_membercar = D('Membercar');
            $map_mc['u_c_id'] = $u_c_id;
            $map_mc['uid'] = $uid;
            $vo = $model_membercar->where($map_mc)->find();
            if (isset($vo['car_number']) and !empty($vo['car_number'])){
                $car_number_arr = explode('_',$vo['car_number']);
                if (isset($car_number_arr[1])){
                    $vo['s_pro'] = $car_number_arr[0];
                    $vo['car_number'] = $car_number_arr[1];
                }else{
                    $vo['s_pro'] = '';
                    $vo['car_number'] = $car_number_arr[0];
                }
            }
            $this->assign('vo',$vo);
        }else{
            $this->error('没找到车辆信息','/myhome');
        }
        $this->display();
    }
    
    public function _before_update(){
	    if (isset($_POST['s_pro']) and isset($_POST['car_number'])){
		    $_POST['car_number'] = $_POST['s_pro']."_".$_POST['car_number'];
		    unset($_POST['s_pro']);
		}
	    if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
		    $this->error('您的车辆信息没有选完整，请选全！');
		}
		if (empty($_POST['car_name'])){
		    $_POST['car_name'] = "未命名";
		}
	}
    
	public function update(){
	    $this->assign('jumpUrl','/myhome/my_car');
        $model_membercar = D('membercar');
        if (false === $model_membercar->create()) {
            $this->error($model_membercar->getError());
        }
        // 更新数据
        $list = $model_membercar->save();
        if (false !== $list) {
            //成功提示
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
        
	}
    public function delete(){
        $this->assign('jumpUrl', '/myhome/my_car');
	    $u_c_id = isset($_REQUEST['u_c_id'])?$_REQUEST['u_c_id']:0;
		
	    if ($u_c_id){
	        $model_membercar = D('Membercar');
	        $map['u_c_id'] = $u_c_id;
	        $membercar = $model_membercar->where($map)->find();
	        if ($membercar['is_default']==1){
	            $uid = $this->GetUserId();
	            $map_other['u_c_id'] = array('neq',$u_c_id);
	            $map_other['status'] = 1;
	            $map_other['uid'] = $uid;
	            $membercarother = $model_membercar->where($map_other)->order("u_c_id DESC")->find();
	            if ($membercarother['u_c_id']){
	                $map_d['u_c_id'] = $membercarother['u_c_id'];
	                $data_d['is_default'] = 1;
	                $model_membercar->where($map_d)->save($data_d);
	            }
	        }
	        $data['status'] = -1;
	        if($model_membercar->where($map)->save($data)){
	            $this->assign('jumpUrl', '/index.php/myhome-my_car');
                $this->success('删除成功！');
	        }else{
	            $this->error('删除失败！');
	        }
	    }else {
	        $this->error('删除失败！');
	    }
	}
	/*
		@author:chf
		@function:添加车辆管理	(新)
		@time:2013-09-02
	*/
    public function save(){
	    $_SESSION['r_data'] = $_POST;
		$Model =D('Membercar');
		if (isset($_POST['s_pro']) and isset($_POST['car_number'])){
		    $_POST['car_number'] = $_POST['s_pro']."_".$_POST['car_number'];
		    unset($_POST['s_pro']);
		}
	    if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
	        $this->assign('jumpUrl','add');
		    $this->error('您的车辆信息没有选完整，请选全！');
		}
		if (empty($_POST['car_name'])){
		    $_POST['car_name'] = "未命名";
		}
		$_POST['create_time'] = time();
		$map['uid'] = $this->GetUserId();
		$map['status'] = 1;
		$map['is_default'] = 1;
		$membercar = $Model->where($map)->select();
		if (!$membercar){
		    $_POST['is_default'] = 1;
		}
		$list = $Model -> add_car();
		$this->action_tip($list);
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
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
		
	$order_status_arr = C("BIDORDER_STATE");
	
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
		$this->assign('page',$page);
		$this->display('my_bidorder_new');
	}
	
	/*
		@author:ysh
		@function:用户的点评
		@time:2013/9/2
	*/
	function my_comment() {
		$model_comment = D("Comment");
		$model_shop = D("Shop");
		
		$uid = $this->GetUserId();
		$map['uid'] = $uid;
		//$map['state'] = 1;
		// 计算总数
        $count = $model_comment->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
		$list = $model_comment->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if($list) {
			foreach($list as $key=>$val) {
				$shop_info = $model_shop->find($val['shop_id']);
				$list[$key]['shop_name'] = $shop_info['shop_name'];
			}
		}

		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}

	
	//戳瞎掉眼乌珠的ajax分页开始 还有4个！！！
	function get_comment_all() {
		$uid = $this->GetUserId();
        $model_membersalecoupon = D('Membersalecoupon');

		//------------------您购买的礼品卡----------------start
		$couponmap_all['xc_membersalecoupon.uid'] = $uid;
		$couponmap_all['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_all['xc_salecoupon.is_delete'] = 0;
		$couponmap_all['xc_salecoupon.coupon_type'] = '2';
		// 计算总数
        $count_all = $model_membersalecoupon->where($couponmap_all)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数
		$p_all = new AjaxPage($count_all, $limitRows,"get_comment_all"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_all->firstRow . "," . $p_all->listRows;

		$page_all = $p_all->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_all = $model_membersalecoupon->where($couponmap_all)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------您购买的礼品卡----------------end
		
		$str = "<table cellpadding='0 cellspacing='0' width='980'>
							<thead>
								<tr>
									<td width='160'>订单号</td>
									<td width='170'>礼品卡卡号</td>
									<td width='78'>面值</td>
									<td width='300'>有效期</td>
									<td width='90'>卡片状态</td>
								</tr>
							</thead>
							<tbody>";
		

		if($couponinfo_all) {
			foreach($couponinfo_all as $key=>$val) {
				if($val['is_use'] == 1) {
					$use_info = "已使用";
				}else {
					$use_info = "未使用";
					if($val['end_time'] < time()) {
						$use_info = "已过期";
					}
				}
				$val['start_time'] = date("Y-m-d",$val['start_time'] );
				$val['end_time'] = date("Y-m-d",$val['end_time'] );
				$val['use_time'] = date("Y-m-d",$val['use_time'] );
				
				$str .= "<tr>
								<td>{$val[membersalecoupon_id]}</td>
								<td>{$val[coupon_code]}</td>
								<td class='price-red'>￥{$val[coupon_amount]}</td>
								<td>{$val[start_time]} 至 {$val[end_time]}</td>
								<td>{$use_info}</td>
							</tr>";
			}
		}

		$str .="</tbody>
						</table>
						<div class='paginator'>
							<ul class='pages'>
								{$page_all}
							</ul>
						</div>";


		echo $str;exit();
	}

	function get_comment_use() {
		$uid = $this->GetUserId();
        $model_membersalecoupon = D('Membersalecoupon');

		//------------------已使用的礼品卡----------------start
		$couponmap_use['xc_membersalecoupon.uid'] = $uid;
		$couponmap_use['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_use['xc_salecoupon.is_delete'] = 0;
		$couponmap_use['xc_salecoupon.coupon_type'] = '2';
		$couponmap_use['xc_membersalecoupon.is_use'] = '1';
		// 计算总数
        $count_use = $model_membersalecoupon->where($couponmap_use)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_use = new AjaxPage($count_use, $limitRows,"get_comment_use"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_use->firstRow . "," . $p_use->listRows;

		$page_use = $p_use->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_use = $model_membersalecoupon->where($couponmap_use)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------已使用的礼品卡----------------end

		$str = "<table cellpadding='0 cellspacing='0' width='980'>
							<thead>
								<tr>
									<td width='160'>礼品卡卡号</td>
									<td width='90'>面值</td>
									<td width='260'>使用时间</td>
									<td width='90'>卡片状态</td>
								</tr>
							</thead>
							<tbody>";
		

		if($couponinfo_use) {
			foreach($couponinfo_use as $key=>$val) {
				$val['use_time'] = date("Y-m-d H:i",$val['use_time'] );
				$str .= "<tr>
								<td>{$val[coupon_code]}</td>
								<td class='price-red'>￥{$val[coupon_amount]}</td>
								<td>{$val[use_time]}</td>
								<td>已使用</td>
							</tr>";
			}
		}

		$str .="</tbody>
						</table>
						<div class='paginator'>
							<ul class='pages'>
								{$page_use}
							</ul>
						</div>";


		echo $str;exit();
	}

	function get_comment_notuse() {
		$uid = $this->GetUserId();
        $model_membersalecoupon = D('Membersalecoupon');

		//------------------未使用的礼品卡----------------start
		$couponmap_notuse['xc_membersalecoupon.uid'] = $uid;
		$couponmap_notuse['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_notuse['xc_salecoupon.is_delete'] = 0;
		$couponmap_notuse['xc_salecoupon.coupon_type'] = '2';
		$couponmap_notuse['xc_membersalecoupon.is_use'] = '0';
		// 计算总数
        $count_notuse = $model_membersalecoupon->where($couponmap_notuse)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_notuse = new AjaxPage($count_notuse, $limitRows,"get_comment_notuse"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_notuse->firstRow . "," . $p_notuse->listRows;

		$page_notuse = $p_notuse->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_notuse = $model_membersalecoupon->where($couponmap_use)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------未使用的礼品卡----------------end

		$str = "<table cellpadding='0 cellspacing='0' width='980'>
							<thead>
								<tr>
									<td width='170'>礼品卡卡号</td>
									<td width='78'>面值</td>
									<td width='300'>有效期</td>
									<td width='90'>卡片状态</td>
								</tr>
							</thead>
							<tbody>";
		

		if($couponinfo_notuse) {
			foreach($couponinfo_notuse as $key=>$val) {
				$val['start_time'] = date("Y-m-d",$val['start_time'] );
				$val['end_time'] = date("Y-m-d",$val['end_time'] );
				$val['use_time'] = date("Y-m-d H:i",$val['use_time'] );
				$str .= "<tr>
								<td>{$val[coupon_code]}</td>
								<td class='price-red'>￥{$val[coupon_amount]}</td>
								<td>{$val[start_time]} 至 {$val[end_time]}</td>
								<td>未使用</td>
							</tr>";
			}
		}

		$str .="</tbody>
						</table>
						<div class='paginator'>
							<ul class='pages'>
								{$page_notuse}
							</ul>
						</div>";


		echo $str;exit();
	}

	function get_comment_passtime() {
		$uid = $this->GetUserId();
        $model_membersalecoupon = D('Membersalecoupon');

		//------------------已过期的礼品卡----------------start
		$couponmap_passtime['xc_membersalecoupon.uid'] = $uid;
		$couponmap_passtime['xc_membersalecoupon.is_delete'] = 0;
		$couponmap_passtime['xc_salecoupon.is_delete'] = 0;
		$couponmap_passtime['xc_salecoupon.coupon_type'] = '2';
		$couponmap_passtime['xc_membersalecoupon.is_use'] = '0';
		$couponmap_passtime['xc_salecoupon.end_time'] = array('lt',time());
		// 计算总数
        $count_passtime = $model_membersalecoupon->where($couponmap_passtime)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->count();
        // 导入分页类
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 1; // 设置每页记录数

		$p_passtime = new AjaxPage($count_passtime, $limitRows,"get_comment_passtime"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p_passtime->firstRow . "," . $p_passtime->listRows;

		$page_passtime = $p_passtime->show(); // 产生分页信息，AJAX的连接在此处生成

		$couponinfo_passtime = $model_membersalecoupon->where($couponmap_passtime)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($limit_value)->select();
		//------------------已过期的礼品卡----------------end

		$str = "<table cellpadding='0 cellspacing='0' width='980'>
							<thead>
								<tr>
									<td width='170'>礼品卡卡号</td>
									<td width='78'>面值</td>
									<td width='300'>有效期</td>
									<td width='90'>卡片状态</td>
								</tr>
							</thead>
							<tbody>";
		

		if($couponinfo_passtime) {
			foreach($couponinfo_passtime as $key=>$val) {
				$val['start_time'] = date("Y-m-d",$val['start_time'] );
				$val['end_time'] = date("Y-m-d",$val['end_time'] );
				$val['use_time'] = date("Y-m-d H:i",$val['use_time'] );
				$str .= "<tr>
								<td>{$val[coupon_code]}</td>
								<td class='price-red'>￥{$val[coupon_amount]}</td>
								<td>{$val[start_time]} 至 {$val[end_time]}</td>
								<td>已过期</td>
							</tr>";
			}
		}

		$str .="</tbody>
						</table>
						<div class='paginator'>
							<ul class='pages'>
								{$page_passtime}
							</ul>
						</div>";


		echo $str;exit();
	}


	/*
		@author:wwy
		@function:显示个人中心我的账户余额
		@time:2014-06-11
	*/
	function my_account(){
		$model_memberaccountlog = D('Memberaccountlog');
		$map['uid'] = $_SESSION['uid'];
		// 计算总数
        $count = $model_memberaccountlog->where($map)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
		$account_info =$model_memberaccountlog->where($map)->order('create_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_memberaccountlog->getLastsql();
        if ($account_info){
            foreach ($account_info as $k=>$v){
				if($v['type']<0){
					$account_info[$k]['pay'] = abs($v['amount']);
				}else{
					$account_info[$k]['save'] = abs($v['amount']);
				}
            }
        }
		//print_r($account_info);
        $this->assign('account_info',$account_info);
        $this->assign('page',$page);
		$this->display();
	}

		/*
		@author:wwy
		@function:显示个人中心账户安全
		@time:2014-06-12
	*/
	function my_safe(){
		$this->display();
	}

	/*
		@author:wwy
		@function:显示个人中心手机验证
		@time:2014-06-12
	*/
	function verifymobile(){
		//print_r($_SESSION);
		$this->display();
	}

	/*
		@author:wwy
		@function:执行个人中心手机验证
		@time:2014-06-16
	*/
	function do_verifymobile(){
		if($_SESSION['verify'] != md5($_POST['authCode'])) {
			echo 1;
		}else if($_SESSION['mobile_verify_xieche'] != md5($_POST['code'])){
			echo 2;
		}else{
			echo 3;
		}
	}

	/*
		@author:wwy
		@function:个人中心支付密码设置页
		@time:2014-06-18
	*/
	function insert_paycode(){
		$this->display();
	}

	/*
		@author:wwy
		@function:个人中心支付密码设置页
		@time:2014-06-18
	*/
	function do_insertpaycode(){
		//验证码校验
		if($_SESSION['verify'] != md5($_POST['authCode'])) {
			echo 1;
			exit;
		}
		//验证支付密码
		if($_POST['payPwd'] != $_POST['rePayPwd']) {
			echo 2;
			exit;
		}
		$model_membersafe = D('membersafe');
		$map['uid'] = $_SESSION['uid'];
		$count = $model_membersafe->where($map)->count();
		$data['uid'] = $_SESSION['uid'];
		$data['paycode'] = pwdHash($_POST['payPwd']);
		if($count>0){
			$data['update_time'] = time();
			$result = $model_membersafe->where($map)->save($data);
		}else{
			$data['create_time'] = time();
			$result = $model_membersafe->add($data);
		}
		if($result){
			echo 3;
		}
	}

	/*
		@author:wwy
		@function:个人中心支付密码设置结果页
		@time:2014-06-18
	*/
	function done_paycode(){
		$this->display();
	}

	/*
		@author:wwy
		@function:发送验证码
		@time:2014-06-12
	*/
	function send_verify() {
		if(is_numeric($_REQUEST['mobile'])){
			$mobile = $_REQUEST['mobile'];
		}else{
			$mobile = $_SESSION['mobile'];
		}
		$model_sms = D('Sms');

	    if ($mobile){
			/*添加发送手机验证码*/
			$condition['phones'] = $mobile;
			$rand_verify = rand(10000, 99999);
			$_SESSION['mobile_verify_ceshi'] = $rand_verify;
			$_SESSION['mobile_verify_xieche'] = md5($rand_verify);
			$verify_str = "正在为您的手机验证，您的短信验证码：".$rand_verify;
			/*
			$send_verify = array(
				'phones'=>$mobile,
				'content'=>$verify_str,
			);
			$return_data = $this->curl_sms($send_verify);
			*/
			// dingjb 2015-09-29 09:54:05 切换到云通讯
			$send_verify = array(
				'phones'  => $mobile,
				'content' => array($rand_verify),
			);
			$return_data = $this->curl_sms($send_verify, null, 4, '37650');

			$send_verify['sendtime'] = time();
			//外网注视去掉保存进短信记录表
			$model_sms->add($send_verify);
			echo 1;
			exit;
		}else {
			echo -1;
		}	
	}

}


