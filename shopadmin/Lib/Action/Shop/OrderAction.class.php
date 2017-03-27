<?php
class OrderAction extends CommonAction {
    function __construct() {
		parent::__construct();
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			//$this->assign('jumpUrl',__APP__.'/Public/login');
			//$this->error('没有登录');
			header("Location: ".__APP__."/Public/login");
		}
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
        if (isset($_GET['order_id']) and $_GET['order_id']){
            $model = D('Order');
		    $vo = $model->getById($_REQUEST['order_id']);
		    if ($vo['shop_id'] != $shop_id){
		        $this->error('没有权限查看该订单号订单！');
		    }
        }
	}
	/*
	 * 判断条件
    *
    */
    function _filter(&$map){
    	if (isset ( $_REQUEST['licenseplate'] ) && $_REQUEST['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%'.$_REQUEST['licenseplate'].'%');
			$this->assign("licenseplate",$_REQUEST['licenseplate']);
        }
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] !=''){
			$map['mobile'] = array('eq',$_REQUEST['mobile']);
			$this->assign("mobile",$_REQUEST['mobile']);
		}
		
		if(isset($_REQUEST['shop_id']) && $_REQUEST['shop_id'] !=''){
			$map['shop_id'] = array('eq',$_REQUEST['shop_id']);
		}

		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !=''){
			$map['order_state'] = array('eq',$_REQUEST['order_state']);
			$this->assign("order_state",$_REQUEST['order_state']);
		}

		if(isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			$map['order_time'] = array('egt',strtotime($_REQUEST['start_time']));
			$this->assign("start_time",$_REQUEST['start_time']);
		}

		if(isset($_REQUEST['end_time']) && $_REQUEST['end_time'] !=''){
			$map['order_time'] = array('elt',strtotime($_REQUEST['end_time']));
			$this->assign("end_time",$_REQUEST['end_time']);
		}
		if(isset($_REQUEST['start_time']) && isset($_REQUEST['end_time']) && $_REQUEST['start_time'] !='' && $_REQUEST['end_time'] !='') {
			$map['order_time'] = array(array('egt',strtotime($_REQUEST['start_time'])),array('elt',strtotime($_REQUEST['end_time'])));
			$this->assign("start_time",$_REQUEST['start_time']);
			$this->assign("end_time",$_REQUEST['end_time']);
		}
	}
	
	/*
    // 框架首页
    public function _before_index() {//echo '<pre>';print_r($_SESSION);
		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
        $model_shop = D(GROUP_NAME.'/Shop');
        $shopinfo = $model_shop->find($shop_id);
        $this->assign('shop_info',$shopinfo);

		$model_order = D(GROU_NAME."/Order");
		$map['shop_id'] = $shop_id;
		$all_count = $model_order->where($map)->count();
		$membersalecoupon_model = D('membersalecoupon');
		$model_order->where()->select();

		$salecoupon = array('in'=>$shop_id);
		$membersalecoupon_model->where($salecoupon)->select();
		
		$this->assign('all_count',$all_count);
    }
	*/
	/*
		@author:chf
		@function:显示预约订单界面
		@time:2013-8-29
	*/
	function index(){
		if (isset ( $_REQUEST['licenseplate'] ) && $_REQUEST['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%'.$_REQUEST['licenseplate'].'%');
			$this->assign("licenseplate",$_REQUEST['licenseplate']);
        }
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] !=''){
			$map['mobile'] = array('eq',$_REQUEST['mobile']);
			$this->assign("mobile",$_REQUEST['mobile']);
		}

		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !=''){
			$map['order_state'] = array('eq',$_REQUEST['order_state']);
			$this->assign("order_state",$_REQUEST['order_state']);
		}

		if(isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			$map['order_time'] = array('egt',strtotime($_REQUEST['start_time']));
			$this->assign("start_time",$_REQUEST['start_time']);
		}

		if(isset($_REQUEST['end_time']) && $_REQUEST['end_time'] !=''){
			$map['order_time'] = array('elt',strtotime($_REQUEST['end_time']));
			$this->assign("end_time",$_REQUEST['end_time']);
		}
		if(isset($_REQUEST['start_time']) && isset($_REQUEST['end_time']) && $_REQUEST['start_time'] !='' && $_REQUEST['end_time'] !='') {
			$map['order_time'] = array(array('egt',strtotime($_REQUEST['start_time'])),array('elt',strtotime($_REQUEST['end_time'])));
			$this->assign("start_time",$_REQUEST['start_time']);
			$this->assign("end_time",$_REQUEST['end_time']);
		}

		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
        $model_shop = D(GROUP_NAME.'/Shop');
		$model_order = D(GROUP_NAME.'/Order');
		$membersalecoupon_model = D('membersalecoupon');
        $shopinfo = $model_shop->find($shop_id);
        $this->assign('shop_info',$shopinfo);

		$model_order = D(GROU_NAME."/Order");
		$map['shop_id'] = $shop_id;
		$all_count = $model_order->where($map)->count();
			
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($all_count, 10);

		foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
		// 分页显示输出
		$page = $p->show_admin();
		

		$list = $model_order->where($map)->order("id desc")->limit($p->firstRow.','.$p->listRows)->select();
		if($list){
			foreach($list as $k=>$v){
				$salecoupon = array('in'=>$shop_ids);
				$salecoupon['uid'] = $v['uid'];
				$salecoupon_info = $membersalecoupon_model->where($salecoupon)->find();
				$list[$k]['membersalecoupon_id'] = $salecoupon_info['membersalecoupon_id'];
				$list[$k]['order_id'] = $this->get_orderid($v['id']);
			}
			
		}
		
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('all_count',$all_count);
		$this->display();
	}

    public function _trans_data(&$list){
		$shop_model = D('Shop');
		$comment_model = D('Comment');
		$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
		    $list[$kk]['order_id'] = $this->get_orderid($vv['id']);
			foreach($shop_list AS $k=>$v){
				if($list[$kk]['shop_id'] == $shop_list[$k]['id']){
					$list[$kk]['shop_name'] = $shop_list[$k]['shop_name'];
				}
			}
			$map['order_id'] = $vv['id'];
			$list[$kk]['comment_num'] = $comment_model->where($map)->count();
		}
		return $list;
	}

    public function get_map(){
		$model = D(GROUP_NAME . "/Shop");
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->find($id);
        //echo '<pre>';print_r($vo);exit;
		$data = array(
			'name'=>$vo['shop_name'],
			'address'=>$vo['shop_address'],
			'tel'=>$vo['shop_maps'],
			'citycode'=>'131',
		
		);
		$data = '{"name":"'.$vo['shop_name'].'","address":"'.$vo['shop_address'].'","tel":"'.$vo['shop_maps'].'","point":"'.$vo['shop_maps'].'","citycode":131}';
		//$data = json_encode($data);
		$this->assign('data',$data);
        $this->display();
	}
	
	public function selectshop(){
		if($_GET['uid']){
		    $uid = $_GET['uid'];
			if($this->isPost()){
    	        if (empty($_POST['select_services'])){
    	            $this->error('查看价格请首先选择您所需要的维修保养项目！');
    	        }
    	        if (empty($_POST['model_id']) and empty($_POST['u_c_id'])){
    	            $this->error('请选择车型！');
    	        }
    	        //print_r($_POST);exit;
    	        if (isset($_POST['other_car']) and $_POST['other_car']==1){
    	            if (isset($_POST['brand_id']) and $_POST['brand_id']){
    	               $this->assign('brand_id',$_POST['brand_id']); 
    	            }
    	            if (isset($_POST['series_id']) and $_POST['series_id']){
    	               $this->assign('series_id',$_POST['series_id']); 
    	            }
    	            if (isset($_POST['model_id']) and $_POST['model_id']){
    	               $this->assign('model_id',$_POST['model_id']); 
    	            }
    	        }
    	        if (isset($_POST['u_c_id'])){
    	             $this->assign('u_c_id',$_POST['u_c_id']); 
    	        }
    	        
    	    }
    	    //载入用户自定义车型MODEL
    		$model_membercar = D(GROUP_NAME.'/Membercar');	
    		//查询用户所有自定义车型
    		$list_membercar = $model_membercar->where("uid=$uid AND status=1")->select();
    		$list_membercar = $model_membercar->Membercar_format_by_arr($list_membercar);
    		//dump($list_membercar);
    		//载入服务项目MODEL
    		$model_serviceitem = D(GROUP_NAME.'/serviceitem');
    		//获取所有的服务分类
    		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
    		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
    		//判断$model_id不存在，$uid存在	
    		/*
    		if(empty($_REQUEST['model_id']) && !empty($uid)){
    			$map['xc_product.car_model_id'] = array('eq',$list_membercar[0]['model_id']);		
    		}
    		*/
    		$model_id = $this->get_model_id($_POST);
    		//dump($model_id);
    		if($model_id){
    			$select_car = $model_membercar->Membercar_format_by_model_id($model_id['model_id']);
    			//dump($select_car);
    			$this->assign('select_car',$select_car);
    			$this->assign('select_brand_id',$model_id['brand_id']);
			    $this->assign('select_model_id',$model_id['model_id']);
			    $this->assign('select_series_id',$model_id['series_id']);
    		}
    		if($this->isPost()){
    		    if (isset($_POST['other_car']) && $_POST['other_car']==1){
    		        $this->assign('other_car',$_POST['other_car']);
    		    }
    			$_POST['select_services'];
    			$select_services_str = implode(',', $_POST['select_services']);
    			$this->assign('select_services',$_POST['select_services']);
    			$this->assign('select_services_str',$select_services_str);
    		}
    		
    		//查询产品--join商家
    		$map['service_id']  = array('in',$select_services_str);
    		$map['car_model_id']  = array('eq',$model_id['model_id']);
    		$productrelation_model = D(GROUP_NAME.'/Productrelation');
    		$list = $productrelation_model->where($map)->select();
    		//echo $productrelation_model->getlastsql();
    		foreach($list AS $k=>$v){
    			$product_arr[] = $list[$k]['product_id'];
    		}
    		$product_str = implode(',', $product_arr);
    		$this->assign('product_str',$product_str);
    		//dump($list);
    		$model_carseries = D(GROUP_NAME.'/Carseries');
    		if (isset($model_id['series_id']) and $model_id['series_id']){
    		    $carseriesinfo = $model_carseries->find($model_id['series_id']);
    		    $fsid = $carseriesinfo['fsid'];
    		    $model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
    		    $relation_shopids = $model_shop_fs_relation->where("fsid=$fsid")->select();
    		    $shopid_arr = array();
    		    if (!empty($relation_shopids)){
    		        foreach ($relation_shopids as $shopid_v){
    		            $shopid_arr[] = $shopid_v['shopid'];
    		        }
    		    }
    		    if (!empty($shopid_arr)){
    		        $shop_model = D(GROUP_NAME.'/Shop');
    		        $condition['id'] = array('in',implode(',',$shopid_arr));
    		        $condition['status'] = 1;
    		        $list_product = $this->_list($shop_model,$condition);
    		    }
    		}
    		
    		//$list_product = $shop_model->where("brand_id = $brand_id")->select();
    		
    		//echo $shop_model->getlastsql();
    		//echo '<pre>';print_r($list_product);
    		if (!empty($list_product)){
    		    $timesale_model = D('Timesale');
    		    $list_timesale_arr = array();
    		    foreach ($list_product as $key=>$val){
    		        $list_timesale = $timesale_model->where("shop_id = $val[id]")->select();
    		        if (!empty($list_timesale)){
    		            $timesale_arr = array();
    		            foreach ($list_timesale as $k=>$timesale){
    		                $timesale_arr[$k] = $timesale;
                		    if ($timesale['workhours_sale']>0){
                		        $timesale_arr[$k]['workhours_sale_str'] = round($timesale['workhours_sale']*10,1)."折";
                		    }else {
                		        $timesale_arr[$k]['workhours_sale_str'] = "无折扣";
                		    }
                		    if ($timesale['product_sale']>0){
                		        $timesale_arr[$k]['product_sale_str'] = round($timesale['product_sale']*10,1)."折";
                		    }else {
                		        $timesale_arr[$k]['product_sale_str'] = "无折扣";
                		    }
                			$timesale_arr[$k]['week_name'] = explode(',',$timesale['week']);
                			foreach($timesale_arr[$k]['week_name'] AS $kk=>$vv){
                			    if ($timesale_arr[$k]['week_name'][$kk]=='0'){
                			        $timesale_arr[$k]['week_name'][$kk] = '日';
                			    }
                			    $timesale_arr[$k]['week_name_s'] .= '周'.$timesale_arr[$k]['week_name'][$kk].',';
                			}
                			$timesale_arr[$k]['week_name_s'] = substr($timesale_arr[$k]['week_name_s'],0,-1);
    		            }
    		            $list_timesale_arr[$val['id']]['timesale_arr'] = $timesale_arr;
    		        }
    		        
    		    }
    		}
    		
    		
    		
    		//echo '<pre>';print_r($list_timesale_arr);exit;
    		//用户三级联动的第一级，品牌
    		$model = D('carbrand');
    		$brand = $model->select();
    		$this->assign('brand',$brand);
    		$this->assign('list_si_level_0',$list_si_level_0);
    		$this->assign('list_si_level_1',$list_si_level_1);
    		$this->assign('list_membercar',$list_membercar);
    		$this->assign('list_product',$list_product);
    		$this->assign('list_timesale_arr',$list_timesale_arr);
    		$this->display();
		}
	}



		public function get_model_id($data){
    		if($data['other_car'] == 1){
    			return $data;
    		}elseif($data['u_c_id']){
    			$u_c_id = $data['u_c_id'];
    			$model_membercar = D(GROUP_NAME.'/Membercar');
    			$getCar = $model_membercar->where("u_c_id = $u_c_id")->find();
    			return $getCar;
    		}else{
    			return false;
    		}
		}



/*
 * ajax返回产品信息数据
 * 
 * 
 */
	public function ajax_get_product_info_bak() {
		header("Content-Type: text/plain; charset=utf-8");
		//载入产品MODEL
		$model_product = D(GROUP_NAME.'/Product');
		$map['product_id'] = array('in',$_REQUEST['product_str']);
		$map['xc_product.service_id'] = array('in',$_REQUEST['select_services']);
		$map['xc_productrelation.car_model_id'] = array('eq',$_REQUEST['select_model_id']);
		//dump($map);
		$list_product = array();
		if (!empty($_REQUEST['select_services'])){
		    $select_services_arr = explode(',',$_REQUEST['select_services']);
		    foreach ($select_services_arr as $key=>$services_id){
		        $map['xc_product.service_id'] = array('eq',$services_id);
		        $list_product[$key] = $model_product->where($map)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->find();
		        $list_product[$key]['service_id'] = $services_id;
		    }
		}
		//$list_product = $model_product->where($map)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
		//echo $model_product->getlastsql();
		//echo '<pre>';print_r($list_product);
		$model_order = D(GROUP_NAME.'/Order');
		if(empty($_POST['workhours_sale'])){
			$model_shop = D(GROUP_NAME.'/Shop');
			$sale = $model_shop->getById($_POST['shop_id']);
			$sale_arr['workhours_sale']=$sale['workhours_sale'];
			//echo 111;
		}else{
			$sale_arr['workhours_sale']=$_POST['workhours_sale'];
		}
		if(empty($_POST['product_sale'])){
			$model_shop = D(GROUP_NAME.'/Shop');
			$sale = $model_shop->getById($_POST['shop_id']);
			$sale_arr['product_sale']=$sale['product_sale'];
		}else{		
			$sale_arr['product_sale']=$_POST['product_sale'];
		}
		//echo '<pre>';print_r($sale_arr);exit;
		//dump($list_product);
		//dump($sale_arr);
		$output_str = $model_order->ListProductdetail_S($list_product,$sale_arr);

		echo $output_str;			
	}




/*
*插入订单的表单
*
*
*/
		public function addorder() {
			$uid = $_GET['uid']; //获取uid
			$model_member = D('Member'); 
			$list = $model_member->getByUid($uid); //查询用户信息			
			$model_product = D(GROUP_NAME.'/Product');//载入产品MODEL
			$map['product_id'] = array('in',$_REQUEST['product_str']);  //获取多个产品的字符窜
			$map['xc_product.service_id'] = array('in',$_REQUEST['select_services']);  //获取多个服务的字符窜
			$map['xc_productrelation.car_model_id'] = array('eq',$_REQUEST['model_id']); 
			$get_product = $model_product->getProduct($map);  //查询产品
			$order_model = D(GROUP_NAME.'/Order');
			//$list_product = $order_model->ListProduct($get_product);//格式化数据
			$timesale_model = D('Timesale');
			$sale_arr = $timesale_model->where("id = $_GET[timesale_id]")->find();
			$output_str = $order_model->ListProductdetail_S($get_product,$sale_arr);//处理数据，输出表格
			$shop_model = D(GROUP_NAME.'/Shop');
			$get_shop = $shop_model->where("id=$_GET[shop_id]")->find();//商家信息
			//获取分时折扣id
			if($_GET['timesale_id']){
				$model_timesale = D('Timesale'); //载入模型
				$list_timesale = $model_timesale->where("id=$_GET[timesale_id]")->find(); //根据id查询分时折扣信息
				$sale_check = sale_check($list_timesale['week']);  //根据分时折扣的星期数，处理无效日期
				$min_hours = explode(':',$list_timesale['begin_time']);   //分时具体上下午时间输出到模板，做判断
				$max_hours = explode(':',$list_timesale['end_time']);     //分时具体上下午时间输出到模板，做判断
			}
			
			$doubleCalendar = double_or_single_Calendar(); //单双月显示判断
			
    		if (isset($_GET['u_c_id']) and !empty($_GET['u_c_id'])){
    		    $car_model = D('Membercar');
    		    $my_car = $car_model->where("u_c_id=$_GET[u_c_id]")->find();
        		if (isset($my_car['car_number']) and !empty($my_car['car_number'])){
                    $car_number_arr = explode('_',$my_car['car_number']);
                    if (isset($car_number_arr[1])){
                        $my_car['s_pro'] = $car_number_arr[0];
                        $my_car['car_number'] = $car_number_arr[1];
                    }else{
                        $my_car['s_pro'] = '';
                        $my_car['car_number'] = $car_number_arr[0];
                    }
                }
    		    $this->assign('my_car',$my_car);
    		}
    		Cookie::set('_currentUrl_', __SELF__);
    		//时间判断
    		$now = time();
    		$fourhour = strtotime(date('Y-m-d').' 16:00:00');
    		$this->assign('now',$now);
    		$this->assign('fourhour',$fourhour);
			/*
			******************************模板输出
			*/
			$this->assign('min_hours',$min_hours[0]);
    		$this->assign('min_minute',$min_hours[1]);
    		$this->assign('max_hours',$max_hours[0]);
    		$this->assign('max_minute',$max_hours[1]);
			$this->assign('list',$list);
			$this->assign('sale_check',$sale_check);
			$this->assign('doubleCalendar',$doubleCalendar);
			$this->assign('get_shop',$get_shop);
			//$this->assign('list_product',$list_product);
			$this->assign('output_str',$output_str);
			$this->display();
		}
	
	
	
	
	public function insert(){
	    $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	    if (empty($_POST['order_date'])){
		    $this->error('请选择预约日期！');
		}
	    if (empty($_POST['order_hours']) || empty($_POST['order_minute'])){
		    $this->error('请选择预约时间！');
		}
	    if (empty($_POST['truename'])){
		    $this->error('姓名不能为空！');
		}
	    if (empty($_POST['mobile'])){
		    $this->error('手机号不能为空！');
		}
	    if (empty($_POST['licenseplate'])){
		    $this->error('车牌号不能为空！');
		}
		//根据提交过来的预约时间，做判断(暂时先注销)
		
		if($_POST['order_date']){
			/*
			$str = strtotime($_POST['order_date']);
			$week = date(N,$str);
			$model_timesale = D('Timesale');
			$map['week'] = array('like',"%".$week."%");
			$map['shop_id'] = array('eq',$_POST['shop_id']);
			$saletime = $model_timesale->where($map)->find();
			if(!$sale_check){
				$this->error('您选择的商家星期'.$week.'无分时折扣，请重新选择！');
			}
    			unset($map);
    		*/
    		//载入产品MODEL
    		$model_product = D('Product');
    		$map['product_id'] = array('in',$_REQUEST['product_str']);
    		$map['xc_product.service_id'] = array('in',$_REQUEST['select_services']);
    		$map['xc_productrelation.car_model_id'] = array('eq',$_REQUEST['model_id']);
    		$list_product = $model_product->where($map)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
    		//echo $model_product->getlastsql();
    		$model_membercar = D('Membercar');
    		$u_c_id = $model_membercar->getByModel_id($_POST['model_id']);
    		$uid = $_POST['uid'];
    		$order_time= $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
    		$order_time = strtotime($order_time);
    		if(!$u_c_id['u_c_id']){
    			$u_c_id['u_c_id'] = 0;
    		}
    		$timesale_model = D('Timesale');
    		$sale_arr = $timesale_model->where("id = $_POST[timesale_id]")->find();
    		$data = array(
    			'u_c_id'=>$u_c_id['u_c_id'],
    			'uid'=>$uid,
    			'shop_id'=>$_POST['shop_id'],
    			'brand_id'=>$_POST['brand_id'],
    			'series_id'=>$_POST['series_id'],
    			'model_id'=>$_POST['model_id'],
    			'saletime_id'=>$_POST['timesale_id'],
        		'service_ids'=>$_REQUEST['select_services'],
    			'product_sale'=>$sale_arr['product_sale'],
    			'workhours_sale'=>$sale_arr['workhours_sale'],
    			'truename'=>$_POST['truename'],
    			'mobile'=>$_POST['mobile'],
    			'licenseplate'=>$_POST['cardqz'].$_POST['licenseplate'],
    			'miles'=>$_POST['miles'],
    			'car_sn'=>$_POST['car_sn'],
    			'remark'=>$_POST['remark'],
    			'order_time'=>$order_time,
    			'create_time'=>time(),
    			'update_time'=>time(),
    		);
    	    //echo '<pre>';print_r($data);exit;
    		$model = D('Order');
    		if(false !== $model->add($data)){
    			$_POST['order_id'] = $model->getLastInsID();
    		}
    		$model_member = D('Member');
    		$get_user_name = $model_member->where("uid=$uid")->find();
    		foreach($list_product AS $k=>$v){
    				$sub_order[]=array(
    				'order_id'=>$_POST['order_id'],
    				'product_id'=>$list_product[$k]['product_id'],
    				'service_id'=>$list_product[$k]['service_id'],
    				'service_item_id'=>$list_product[$k]['service_item_id'],
    				'uid'=>$uid,
    				'user_name'=>$get_user_name['username'],
    				'car_series_id'=>$_POST['series_id'],
    				'create_time'=>time(),
    				'update_time'=>time(),
    				);
    		}
    		$model_suborder = D('Suborder');
    		$list=$model_suborder->addAll($sub_order);
    		//echo $model->getlastsql();
    		if($_POST['order_id']){	
    			$this->success('预约提交成功！',U('/Store/Order/edit/order_id/'.$_POST['order_id']));
    		}else {
    		   $this->error('预约提交失败！',U('/Store/Order/selectshop/uid/'.$uid)); 
    		}
		}

	
	}
	/*
		author:chf
		function:显示预约订单编辑
		time:2013-8-29
	*/
	//编辑
	public function edit(){
        $model = D('Order');
		$membercoupon_model = D('membercoupon');
		$vo = $model->getById($_REQUEST['order_id']);
		$vo['order_id'] = $this->get_orderid($vo['id']);
	    if ($vo['coupon_save_discount']){
		    $workhours_sale = $vo['workhours_sale']-$vo['coupon_save_discount'];
		}else {
		    $workhours_sale = $vo['workhours_sale'];
		}
		$order_time_arr = $this->breakdate($vo['order_time']);
		$shop_model = D('Shop');
		$getShopName = $shop_model->getById($vo['shop_id']);
		$vo['shop_info'] = $getShopName;
		//$vo['order_time'] = date('Y-m-d',$vo['order_time']);
		$vo['order_time'] =$order_time_arr;
		$vo['create_time'] = date("Y-m-d H:i:s",$vo['create_time']);
		
		$select_car = $this->Membercar_format_by_model_id($vo['model_id']);
		/*不知道什么逻辑 以前的看不懂表 ysh 2014/2/14
		$map['order_id'] = array('eq',$_GET['order_id']);
		$list_by_suborder = $model->where($map)->join('xc_suborder ON xc_suborder.order_id = xc_order.id')->select();
		if (!empty($list_by_suborder)){
			$service_ids_arr = explode(',',$list_by_suborder[0]['service_ids']);
		}*/
		//直接用order表的
		$service_ids_arr = explode(',',$vo['service_ids']);


		$model_serviceitem = D('Serviceitem');
		$serviceitem = $model_serviceitem->select();
		$serviceitem_arr = array();
		if (!empty($serviceitem)){
		    foreach ($serviceitem as $v){
		        $serviceitem_arr[$v['id']] = $v;
		    }
		}
		$product_img_str = $vo['shop_id'].'_'.$vo['model_id'].'_'.$vo['service_ids'].'_'.$vo['productversion_ids'].'_'.$vo['timesaleversion_id']."_".$workhours_sale."_".$vo['product_sale'];
	    $coupon_amount = 0;
		if ($vo['membercoupon_id']){
		    $model_membercoupon = D('Membercoupon');
		    $membercoupon = $model_membercoupon->find($vo['membercoupon_id']);
		    $coupon_id = $membercoupon['coupon_id'];
		    $product_img_str .= '_'.$coupon_id;
		    $model_coupon = D('Coupon');
		    $coupon = $model_coupon->find($coupon_id);
		    if ($coupon['coupon_discount']<=0 and $coupon['coupon_amount']>0){
                $coupon_amount = $coupon['coupon_amount'];
            }
		}
		
		$img_name = sha1($product_img_str).'.png';
		if (file_exists('../UPLOADS/Product/'.$vo['model_id'].'/'.$img_name) ){
		    $vo['img_name'] = $vo['model_id'].'/'.$img_name;
		}else {
		    $timesaleversion_id = $vo['timesaleversion_id'];
		    $timesaleversion_model = D(GROUP_NAME.'/Timesaleversion');
		    $timesaleversion = $timesaleversion_model->find($timesaleversion_id);
		    
		    $vo['img_name'] = $this->ajax_get_product_info($vo['model_id'],$vo['shop_id'],$vo['service_ids'],$workhours_sale,$vo['product_sale'],$vo['timesaleversion_id'],$timesaleversion['memo'],$timesaleversion['coupon_id'],$coupon_amount);
		}

		if($vo['servicemember_id']) {
			$servicemember_model = D(GROUP_NAME.'/Servicemember');
			$servicemember = $servicemember_model->find($v['servicemember_id']);
			$vo['servicemember_name'] = $servicemember['name'];
		}

		$this->assign('serviceitem', $serviceitem_arr);
		$this->assign('select_car',$select_car);
		$this->assign('list_by_suborder',$list_by_suborder);
		$this->assign('service_ids_arr',$service_ids_arr);
        $this->assign('vo', $vo);
        $this->assign('coupon', $coupon);
        $this->display();
	}
	

/*
 * ajax返回产品信息数据
 * 
 * 
 */
public function ajax_get_product_info($model_id,$shop_id,$select_services_str,$workhours_sale,$product_sale,$timesaleversionid,$memo='',$coupon_id=0,$coupon_amount=0) {
		//header("Content-Type: text/plain; charset=utf-8");
		$model_order = D('Order');
		//查询产品--join商家
		$map['service_id']  = array('in',$select_services_str);
		$map['model_id']  = array('eq',$model_id);
		$product_model = D('Product');
		$list = $product_model->where($map)->select();
		foreach($list AS $k=>$v){
			$versionid_arr[] = $v['versionid'];
			$productid_arr[] = $v['id'];
		}
		$versionid_str = implode(',', $versionid_arr);
		$productid_str = implode(',', $productid_arr);
		unset($map);
		//图片文件名      '店铺id_服务id_产品id_modelid'
		$product_img_str = $shop_id.'_'.$model_id.'_'.$select_services_str.'_'.$versionid_str.'_'.$timesaleversionid."_".$workhours_sale."_".$product_sale;
		if ($coupon_id){
		   $product_img_str .= '_'.$coupon_id;
		}
		$img_name = sha1($product_img_str).'.png';
		
		$folder = $model_id;
		$model_product_img = D('Product_img');
		$data['product_imgname'] = $product_img_str;
        $data['shop_id'] = $shop_id;
        $data['timesaleversionid'] = $timesaleversionid;
        $data['productversion_ids'] = ','.$versionid_str.',';
        $data['product_sale'] = $product_sale;
        $data['workhours_sale'] = $workhours_sale;
        $data['coupon_id'] = $coupon_id;
        $data['status'] = 0;
		$product_img = $model_product_img->where($data)->find();
		if (empty($product_img) || !file_exists('UPLOADS/Product/'.$folder.'/'.$img_name) ){
    		//载入产品MODEL
    		$model_product = D('Product');
    		$map['id'] = array('in',$productid_str);
    		$list_product = array();
    		if (!empty($select_services_str)){
    		    $select_services_arr = explode(',',$select_services_str);
    		    foreach ($select_services_arr as $key=>$services_id){
    		        $map['service_id'] = array('eq',$services_id);
    		        $list_product[$key]['productinfo'] = $model_product->where($map)->find();
    		        $list_product[$key]['service_id'] = $services_id;
    		    }
    		}
    		$sale_arr['workhours_sale']=$workhours_sale;
    		$sale_arr['product_sale']=$product_sale;
    	    if($sale_arr['product_sale'] == 0.00){
    			$sale_arr['product_sale'] = '无折扣';
    			$sale_value['product_sale'] = 1;
    		}else{
    			$sale_value['product_sale'] = $sale_arr['product_sale'];
    			$sale_arr['product_sale'] = ($sale_arr['product_sale']*10).'折';
    		}
    
    		if($sale_arr['workhours_sale'] == 0.00){
    			$sale_arr['workhours_sale'] = '无折扣';
    			$sale_value['workhours_sale'] = 1;
    		}else{
    		    if ($sale_arr['workhours_sale'] == '-1'){
    		        $sale_value['workhours_sale'] = 0;
        			$sale_arr['workhours_sale'] = '全免';
    		    }else{
        			$sale_value['workhours_sale'] = $sale_arr['workhours_sale'];
        			$sale_arr['workhours_sale'] = ($sale_arr['workhours_sale']*10).'折';
    		    }
    		}
    		//生成产品数据
    		$model_serviceitem = D('Serviceitem');
    		$tables_arr = array();
    		
    	    $table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>'费用明细','color'=>'0, 0, 0','width'=>'630','size'=>'15','align'=>'center','colspan'=>'6'
                						),
                					),
                			'height'=>'30'
                		);
            if ($memo){
                $memo .= ',以下为活动优惠后的价格';
                $table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>$memo,'color'=>'255, 0, 0','width'=>'630','size'=>'12','colspan'=>'6'
                						),
                					),
                			'height'=>'20'
                		);
            }
            if (isset($list_product) and !empty($list_product)){
                $i=0;
                foreach ($list_product as $k=>$v){
                    $i++;
                    $service_name = $model_serviceitem->getById($v['service_id']);
                    if ($v['productinfo']['product_detail']){
    				    $list_detail = $v['productinfo']['product_detail'];
    				    $list_detail = unserialize($list_detail);
    				}else {
    				    $list_detail = array();
    				}
    				$table1_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'维修项目'.$i.'：'.$service_name['name'],'color'=>'0,0,0','width'=>'630','size'=>'12','colspan'=>'6'
            						),
            					),
            			'height'=>'30'
            		);
    				if (!empty($list_detail)){
    				    $table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>'零件明细','color'=>'0,0,255','width'=>'150','size'=>'12'
                						),
                						array(//td
                							'str'=>'零件单价','color'=>'0,0,255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'零件数量','color'=>'0, 0, 255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'门市零件价格','color'=>'0, 0, 255','width'=>'110','size'=>'12'
                						),
                						array(//td
                							'str'=>'折扣率','color'=>'0, 0, 255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'折后价格','color'=>'0, 0, 255','width'=>'100','size'=>'12'
                						),
                					),
                			'height'=>'30'
                		);
    				    foreach($list_detail AS $kk=>$vv){
    				        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
    						$all_total +=$list_detail[$kk]['total'];
    						if($list_detail[$kk]['Midl_name'] != '工时费'){
    							$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
    							$table1_arr[] = array(//tr
                        			'tr'=>array(
                        						array(//td
                        							'str'=>$list_detail[$kk]['Midl_name']
                        						),
                        						array(//td
                        							'str'=>$list_detail[$kk]['price']
                        						),
                        						array(//td
                        							'str'=>$list_detail[$kk]['quantity'].' '.$list_detail[$kk]['unit']
                        						),
                        						array(//td
                        							'str'=>$list_detail[$kk]['total']
                        						),
                        						array(//td
                        							'str'=>$sale_arr['product_sale']
                        						),
                        						array(//td
                        							'str'=>$list_detail[$kk]['after_sale_total']
                        						),
                        					),
                        			'height'=>'20'
                        		);
                        		$product_price += $list_detail[$kk]['total'];
        						$product_price_sale += $list_detail[$kk]['after_sale_total'];
    						}else {
    						    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
    							$workhours_price += $list_detail[$kk]['total'];
    							$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
    						}
    						$all_after_total += $list_detail[$kk]['after_sale_total'];
    				    }
    				    $table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>'工时明细','color'=>'0,0,255','width'=>'150','size'=>'12'
                						),
                						array(//td
                							'str'=>'工时单价','color'=>'0,0,255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'工时数量','color'=>'0, 0, 255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'门市工时价格','color'=>'0, 0, 255','width'=>'110','size'=>'12'
                						),
                						array(//td
                							'str'=>'折扣率','color'=>'0, 0, 255','width'=>'90','size'=>'12'
                						),
                						array(//td
                							'str'=>'折后价格','color'=>'0, 0, 255','width'=>'100','size'=>'12'
                						),
                					),
                			'height'=>'30'
                		);
                		$table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>$list_detail[0]['Midl_name']
                						),
                						array(//td
                							'str'=>$list_detail[0]['price']
                						),
                						array(//td
                							'str'=>$list_detail[0]['quantity'].' '.$list_detail[0]['unit']
                						),
                						array(//td
                							'str'=>$list_detail[0]['total']
                						),
                						array(//td
                							'str'=>$sale_arr['workhours_sale']
                						),
                						array(//td
                							'str'=>$list_detail[0]['after_sale_total']
                						),
                					),
                			'height'=>'20'
                		);
    				}else {
    				    $table1_arr[] = array(//tr
                			'tr'=>array(
                						array(//td
                							'str'=>'很抱歉，您所查询的这款车型或这个维修项目还没有维修保养价格明细，有可能您的车型不需要做这个项目（如某些车型不需要更换自动变速箱油，某些使用正时链条的车型不需要更换正时皮带），也有可能您这款车型或这个维修项目的价格明细还未收入到我们的数据库。我们将尽快完善我们的维修保养价格数据库，以为您提供全面的服务。','color'=>'0, 0, 0','width'=>'630','noproduct'=>true,'colspan'=>'6'
                						),
                					),
                			'height'=>'20'
                		);
    				}
    				//零件配节省费用
    				$product_price_save = $product_price-$product_price_sale;
    				//零件配节省费用
    				$workhours_price_save = $workhours_price-$workhours_price_sale;
    				$save_total = $all_total-$all_after_total;
                }
    			$table2_arr[] = array(//tr
        			'tr'=>array(
        						array(//td
        							'str'=>'通过纵横携车网预约您所选择的维修保养项目，共为您节省：','color'=>'0, 0, 0','size'=>'15','colspan'=>'4','width'=>'630'
        						),
        					),
        			'height'=>'30'
        		);
        		if ($coupon_amount>0){
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'门市价','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'折后价','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'优惠劵抵用','color'=>'0,0,255','size'=>'12','width'=>'90'
            						),
            						array(//td
            							'str'=>'总价','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'节省','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'零件费'
            						),
            						array(//td
            							'str'=>$product_price
            						),
            						array(//td
            							'str'=>$product_price_sale
            						),
            						array(//td
            							'str'=>''
            						),
            						array(//td
            							'str'=>''
            						),
            						array(//td
            							'str'=>$product_price_save
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'工时费'
            						),
            						array(//td
            							'str'=>$workhours_price
            						),
            						array(//td
            							'str'=>$workhours_price_sale
            						),
            						array(//td
            							'str'=>$coupon_amount
            						),
            						array(//td
            							'str'=>$workhours_price_sale-$coupon_amount
            						),
            						array(//td
            							'str'=>$workhours_price_save+$coupon_amount
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'合计(元)'
            						),
            						array(//td
            							'str'=>$all_total
            						),
            						array(//td
            							'str'=>$all_after_total
            						),
            						array(//td
            							'str'=>$coupon_amount
            						),
            						array(//td
            							'str'=>$all_after_total-$coupon_amount
            						),
            						array(//td
            							'str'=>$save_total+$coupon_amount
            						),
            					),
            			'height'=>'20'
            		);
        		}else {
        		    $table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'门市价','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'折后价','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            						array(//td
            							'str'=>'节省','color'=>'0,0,255','size'=>'12','width'=>'70'
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'零件费'
            						),
            						array(//td
            							'str'=>$product_price
            						),
            						array(//td
            							'str'=>$product_price_sale
            						),
            						array(//td
            							'str'=>$product_price_save
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'工时费'
            						),
            						array(//td
            							'str'=>$workhours_price
            						),
            						array(//td
            							'str'=>$workhours_price_sale
            						),
            						array(//td
            							'str'=>$workhours_price_save+$coupon_amount
            						),
            					),
            			'height'=>'20'
            		);
            		$table2_arr[] = array(//tr
            			'tr'=>array(
            						array(//td
            							'str'=>'合计(元)'
            						),
            						array(//td
            							'str'=>$all_total
            						),
            						array(//td
            							'str'=>$all_after_total
            						),
            						array(//td
            							'str'=>$save_total+$coupon_amount
            						),
            					),
            			'height'=>'20'
            		);
        		}
        		$tables_arr[] = $table1_arr;
        		$tables_arr[] = $table2_arr;
            }
            //echo '<pre>';print_r($tables_arr);exit;
            $this->createtableimg($tables_arr,$folder,$img_name);
            if (empty($product_img)){
                $data['create_time'] = time();
                $model_product_img->add($data);
                $product_img = $data;
            }
		}
		//echo $folder.'--'.$product_img['product_imgname'];exit;
		//$output_str = $model_order->ListProductdetail_pic($folder,$product_img['product_imgname']);
		//echo '<br>';print_r($product_img);exit;
		//echo $output_str;exit;	
		//$model = D('Shop');
		//$getShop = $model->where("id = $_POST[shop_id]")->find();
		return $folder.'/'.sha1($product_img['product_imgname']).'.png';
		//$this->assign('getShop',$getShop);
		//$this->assign('folder',$folder);
		//$this->assign('product_imgname',$product_img['product_imgname']);
		//$this->display();
	}
	
	public function  _before_update(){
		$order_time = $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
		$_POST['order_time'] = strtotime($order_time);	
	}
	public function _tigger_update($model){
		if($model->id){
			$shop_model = D('Shop');
			$getShopArr = $shop_model -> where("id = $_POST[shop_id]")->find();		
			$serviceitem_model = D('Serviceitem');
			unset($map);
			$_REQUEST['select_services'] = implode(',',$_REQUEST['select_services']);
			$map['id']  = array('in',$_REQUEST['select_services']);
			$getServiceitemArr = $serviceitem_model->where($map)->select();
			foreach($getServiceitemArr AS $k=>$v){
				$getServiceitem .= $getServiceitemArr[$k]['name'].',';
			}
			$getServiceitem = substr($getServiceitem,0,-1);
			//折扣
			//$sale_10 = $sale_check['workhours_sale']*10;
			$order_date= $_POST['date'];
			if($_POST['order_state'] == 1){
			    if ($_POST['workhours_sale']>0){
			        $workhours_sale_str = ($_POST['workhours_sale']*10).'折';
			    }else{
			        $workhours_sale_str = '无折扣';
			    }
			    if ($_POST['product_sale']>0){
			        $product_sale_str = ($_POST['product_sale']*10).'折';
			    }else{
			        $product_sale_str = '无折扣';
			    }
    			$send_add_order_data = array(
    				'phones'=>$_POST['mobile'],
    				'content'=>'您的车辆'.$_POST['licenseplate'].'预定的'.$order_date.' '.$_POST['order_hours'].':'.$_POST['order_minute'].'去'.$_POST['shop_name'].'['.$_POST['shop_address'].']进行'.$getServiceitem.'的服务已预订成功，工时费'.$workhours_sale_str.'，配件费'.$product_sale_str.'，到了4S店后请告知工作人员您是通过携车网预约的，将会有专人为您提供贴心的服务，结算时请出示此短信以享受上述折扣,如有问题请致电纵横携车网'.C('CALL_400'),	
    			);
    			//dump($send_add_order_data);
    			$return_data = curl_sms($send_add_order_data);

				$model_sms = D('Sms');
				$now = time();
				$send_add_order_data['sendtime'] = $now;
				$model_sms->add($send_add_order_data);
			}
			//增加用户积分
			if ($_POST['order_state'] == 2){
			    $model_order = D('Order');
			    $orderinfo = $model_order->find($_POST['id']);
			    
			    $model_registerrecommend = D('registerrecommend');
			    $registerrecommend_info = $model_registerrecommend->where("uid=$orderinfo[uid]")->find();

			    if ($registerrecommend_info['ruid']){
    			    $model_member = D('Member');
                	$condition['uid'] = $registerrecommend_info['ruid'];
                	$model_member->where($condition)->setInc('recommend_effective_number',1);
                	//echo $model_member->getLastSql();
			    }
			    if ($orderinfo['total_price']>=100){
			        $model_point = D('Point');
			        $data['point_number'] = POINT_ADD;
			        $data['uid'] = $orderinfo['uid'];
			        $data['operator_id'] = $_SESSION[C('USER_AUTH_KEY')];
			        $data['orderid'] = $orderinfo['id'];
			        $data['create_time'] = time();
			        $data['point_memo'] = '订单完成奖励积分';
			        $data['order_uid'] = $orderinfo['uid'];
			        $model_point->add($data);
			        $model_member = D('Member');
			        
			        $condition['uid'] = $orderinfo['uid'];
			        $model_member->where($condition)->setInc('point_number',POINT_ADD);
			        
			        if ($registerrecommend_info['ruid']){
			            $data['uid'] = $registerrecommend_info['ruid'];
			            $data['point_number'] = POINT_ADD*0.2;
			            $data['point_memo'] = '您推荐注册的用户 '.$orderinfo['uid'].' 订单完成给您的奖励积分';
			            $model_point->add($data);
			            
			            $condition['uid'] = $registerrecommend_info['uid'];
			            $model_member->where($condition)->setInc('point_number',POINT_ADD*0.2);
			        }
			    }
			}
			//dump($_POST);
			$this->success('修改成功，跳转中~~');
			exit;
			//$this->redirect('/Store/Order/selectshop/uid/'.$model->id);
		}else{
			$this->error('修改失败');
			exit;
		}
		
	
	}
    public function update_order_state(){
	    $id = isset($_POST['id'])?$_POST['id']:0;
	    $order_state = isset($_POST['order_state'])?$_POST['order_state']:'';
	    if ($id and $order_state){
	        $model_order = D('Order');
            $orderinfo = $model_order->find($id);
	        $data['order_state'] = $order_state;
	        $condition['id'] = $id;
	        $model_order->where($condition)->save($data);
		    $model_shop = D('Shop');
		    $shop_info = $model_shop->find($orderinfo['shop_id']);
    	    if($order_state == 1){
			    $this->send_sms($orderinfo,$shop_info);
			}
			if ($order_state == '-1' and $orderinfo['order_state'] == 1){
			    $this->send_sms_cancel($orderinfo,$shop_info);
			}
	        if ($order_state==2){
	            $this->add_point($orderinfo,$id);
	        }
	        echo 1;
	    }else{
	        echo -1;
		    exit; 
	    }
	}
	public function send_sms_cancel($orderinfo,$shop_info){
	    $order_time = date("Y-m-d H:i",$orderinfo['order_time']);
	    $send_add_order_data = array(
			'phones'=>$orderinfo['mobile'],
			'content'=>'尊敬的用户您好，您的订单'.$orderinfo['id'].'号，预订的'.$order_time.'去'.$shop_info['shop_name'].'['.$shop_info['shop_address'].']的服务已取消，如有疑问请拨打'.C('CALL_400'),
		);
		//echo '<pre>';print_r($send_add_order_data);
		$return_data = curl_sms($send_add_order_data);

		$model_sms = D('Sms');
		$now = time();
		$send_add_order_data['sendtime'] = $now;
		$model_sms->add($send_add_order_data);

	}
	public function send_sms($orderinfo,$shop_info){
	    if ($orderinfo['workhours_sale']>0){
	        $workhours_sale_str = ($orderinfo['workhours_sale']*10).'折';
	    }else{
	        $workhours_sale_str = '无折扣';
	    }
	    if ($orderinfo['product_sale']>0){
	        $product_sale_str = ($orderinfo['product_sale']*10).'折';
	    }else{
	        $product_sale_str = '无折扣';
	    }
	    $order_time = date("Y-m-d H:i",$orderinfo['order_time']);
	    
	    $serviceitem_model = D('Serviceitem');
	    $map['id']  = array('in',$orderinfo['service_ids']);
		$getServiceitemArr = $serviceitem_model->where($map)->select();
		foreach($getServiceitemArr AS $k=>$v){
			$getServiceitem .= $getServiceitemArr[$k]['name'].',';
		}
		$getServiceitem = substr($getServiceitem,0,-1);
		$send_add_order_data = array(
			'phones'=>$orderinfo['mobile'],
			'content'=>'您的车辆'.$orderinfo['licenseplate'].'预定的'.$order_time.'去'.$shop_info['shop_name'].'['.$shop_info['shop_address'].']进行'.$getServiceitem.'的服务已预订成功，工时费'.$workhours_sale_str.'，配件费'.$product_sale_str.'，到了4S店后请告知工作人员您是通过携车网预约的，将会有专人为您提供贴心的服务，结算时请出示此短信以享受上述折扣,如有问题请致电纵横携车网'.C('CALL_400'),	
		);
		//echo '<pre>';print_r($send_add_order_data);
		$return_data = curl_sms($send_add_order_data);

		$model_sms = D('Sms');
		$now = time();
		$send_add_order_data['sendtime'] = $now;
		$model_sms->add($send_add_order_data);

	}
	public function add_point($orderinfo,$id){
        $model_registerrecommend = D('registerrecommend');
        $registerrecommend_info = $model_registerrecommend->where("uid=$orderinfo[uid]")->find();
    	if ($registerrecommend_info['ruid']){
    	    $model_member = D('Member');
        	$condition['uid'] = $registerrecommend_info['ruid'];
        	$model_member->where($condition)->setInc('recommend_effective_number',1);
        }
        if ($orderinfo['total_price']>=100){
            $model_point = D('Point');
            $data['point_number'] = POINT_ADD;
            $data['uid'] = $orderinfo['uid'];
            $data['operator_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $data['orderid'] = $orderinfo['id'];
            $data['create_time'] = time();
            $data['point_memo'] = '订单完成奖励积分';
            $data['order_uid'] = $orderinfo['uid'];
            $model_point->add($data);
            $model_member = D('Member');
            
            $condition['uid'] = $orderinfo['uid'];
            $model_member->where($condition)->setInc('point_number',POINT_ADD);
            
            if ($registerrecommend_info['ruid']){
                $data['uid'] = $registerrecommend_info['ruid'];
                $data['point_number'] = POINT_ADD*0.2;
                $data['point_memo'] = '您推荐注册的用户 '.$orderinfo['uid'].' 订单完成给您的奖励积分';
                $model_point->add($data);
                $condition['uid'] = $registerrecommend_info['uid'];
                $model_member->where($condition)->setInc('point_number',POINT_ADD*0.2);
            }
        }
	} 
	public function orderlog(){

			$map['order_id'] = array('eq',$_REQUEST['order_id']);
			$model_orderlog = D('Orderlog');
			$list = $model_orderlog->where($map)->select();
			$this->assign('list',$list);
			$this->display();		
	}
	
	public function addorderlog(){
		$model_orderlog = D('Orderlog');
	        if (false === $model_orderlog->create()) {
            $this->error($model_orderlog->getError());
        }
        $_POST['submit_time'] = time();
        $_POST['operate_id'] = $_SESSION[C('USER_AUTH_KEY')];
        //保存当前数据对象
        $list = $model_orderlog->add($_POST);
        echo $model_orderlog->getLastSql();
        if ($list !== false) { //保存成功
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }	
		
	}
	
	/*
	 * 通过model_id来获取车型对应数据
	 * 初始化数据
	 * 
	 */
	public function Membercar_format_by_model_id($model_id) {
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		if(!empty($model_id)){
			$list_default_model = $model_model->getByModel_id($model_id);
			//echo $model_model->getLastSql();
			$list_default_series = $model_series->getBySeries_id($list_default_model['series_id']);
		//	dump($list_default_series);
			$list_default_brand = $model_brand->getByBrand_id($list_default_series['brand_id']);
			
		}
		if($list_default_brand && $list_default_model && $list_default_series){
			$list['brand_name'] = $list_default_brand['brand_name'];
			$list['series_name'] = $list_default_series['series_name'];
			$list['model_name'] = $list_default_model['model_name'];
			//dump($list);
			return $list;
		}else{
				
			return false;
		}
	}
		
	/*
 * 日期 小时 分钟 拆分为数组
 * 
 */

	function breakdate($time){
		$date = date('Y-m-d',$time);
		$arr['date'] = $date;
		$hour = date('H',$time);
		$arr['hour'] = $hour;
		$minute = date('i',$time);
		$arr['minute'] = $minute;
		return $arr;
		
	}

}
?>