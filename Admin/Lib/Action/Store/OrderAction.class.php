<?php
class OrderAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//店铺表
		$this->MembercouponModel = D('membercoupon');//优惠卷信息
		$this->MembersalecouponModel = D('membersalecoupon');//优惠卷信息
		$this->CarModel = D('carmodel');//车型表
		$this->CarseriesModel = D('carseries');//车系表
		$this->MemberModel = D('member');
		$this->CarcodeModel = D('carcode');//车会验证码表
		$this->Salecouponmodel = D('Salecoupon');//
		$this->Smsmodel = D('Sms');//短信表

		$this->shop_fs_relationModel = D('shop_fs_relation');
		$this->carseriesModel = D('carseries');
		$this->carbrandModel = D('carbrand');
		$this->carModel = D('carmodel');
		$this->order_logModel = D('order_log'); //记录订单操作表
	}
	/*
	 * 判断条件
    *
    
    function _filter(&$map){
    	if (isset ( $_REQUEST ['licenseplate'] ) && $_REQUEST ['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%,'.$_REQUEST['licenseplate'].',%');
        }
		if(isset($_REQUEST['type']) && $_REQUEST['keyword'] !=''){
			$map[$_REQUEST['type']] = $_REQUEST['keyword'];
		}
		
		if(isset($_POST['shop_id']) && $_POST['shop_id'] !=''){
			$map['shop_id'] = $_POST['shop_id'];
		}
		if(isset($_GET['shop_id']) && $_GET['shop_id'] !=''){
			$map['shop_id'] = $_GET['shop_id'];
		}
		$this->assign('shop_id',$map['shop_id']);

		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !=''){
			$map['order_state'] = $_REQUEST['order_state'];
			$this->assign('order_state',$_REQUEST['order_state']);
		}
		if($_REQUEST['iscomment'] && $_REQUEST['iscomment']!='3'){
			if($_REQUEST['iscomment'] == 4){
				$map['iscomment'] = '0';
				$data['iscomment'] = '4';
			}else{
				$data['iscomment'] = $map['iscomment'] = $_REQUEST['iscomment'];
			}
		}else{
			$data['iscomment'] = $_REQUEST['iscomment'];
			unset($map['iscomment']);
		}
		$this->assign('data',$data);
		if(isset($_REQUEST['search_type']) and $_REQUEST['search_type']!='' and isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			$map[$_REQUEST['search_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'])));
			$this->assign('search_type',$_REQUEST['search_type']);
			$this->assign('start_time',$_REQUEST['start_time']);
			$this->assign('end_time',$_REQUEST['end_time']);
		}
	}
	*/
	
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


    // 框架首页
    public function _before_index() {
		$shop_model = D('Shop');
		$shop_list = $shop_model->select();
		$this->assign('shop_list',$shop_list);
		$model_salecoupon = D("Salecoupon");
		$salecoupon = $model_salecoupon->where(array('is_delete'=>0))->field('id,coupon_name')->select();
		$this->assign("salecoupon",$salecoupon);
    }


    /*
	public function _trans_data(&$list){
		$shop_model = D('Shop');
		$comment_model = D('Comment');
		$MemberModel = D('member');
		//$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
		    $list[$kk]['order_id'] = $this->get_orderid($vv['id']);
			$member = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
			$list[$kk]['fromstatus'] = $member['fromstatus'];

			$shop_info = $shop_model->find($vv['shop_id']);
			$list[$kk]['shop_id'] = $shop_info['id'];
			$list[$kk]['shop_name'] = $shop_info['shop_name'];

			$shop_name_array[$shop_info['id']]['id'] = $shop_info['id'];
			$shop_name_array[$shop_info['id']]['name'] = $shop_info['shop_name'];
			
			$map['order_id'] = $vv['id'];
			$list[$kk]['comment_num'] = $comment_model->where($map)->count();
		}
		$shop_name_array = $this->array_unique_fb($shop_name_array);
		//dump($shop_name_array);
		$this->assign('shop_name_array' , $shop_name_array);
		return $list;
	}
	*/
	
	/*
		@author:ysh
		@function:订单查询太复杂 独立index
		@time:2013-10-29 
	*/
	function index() {
		//_filter条件模块
		if (isset ( $_REQUEST ['id'] ) && $_REQUEST ['id'] != '') {
            $map['xc_order.id'] = $_REQUEST ['id'];
			$this->assign("id",$_REQUEST['id']);
        }
		if (isset ( $_REQUEST ['licenseplate'] ) && $_REQUEST ['licenseplate'] != '') {
            $map['xc_order.licenseplate'] = array('like','%,'.$_REQUEST['licenseplate'].',%');
        }
		if(isset($_REQUEST['type']) && $_REQUEST['keyword'] !=''){
			$map["xc_order.".$_REQUEST['type']] = $_REQUEST['keyword'];
		}
		
		if(isset($_POST['shop_id']) && $_POST['shop_id'] !=''){
			$map['xc_order.shop_id'] = $_POST['shop_id'];
		}
		if(isset($_GET['shop_id']) && $_GET['shop_id'] !=''){
			$map['xc_order.shop_id'] = $_GET['shop_id'];
		}
		$this->assign('shop_id',$_REQUEST['shop_id']);

		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !='99'){
			$map['xc_order.order_state'] = $_REQUEST['order_state'];
		}else {
			if($_REQUEST['order_state'] !='99') {
				$map['xc_order.order_state'] = 0;
			}else {
				unset($map['xc_order.order_state']);
			}
		}
		$this->assign('order_state',$_REQUEST['order_state']);

		if($_REQUEST['iscomment']!=''){
			$map['iscomment'] = $_REQUEST['iscomment'];
		}
		$this->assign('iscomment',$_REQUEST['iscomment']);

		if(isset($_REQUEST['search_type']) and $_REQUEST['search_type']!='' and isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			//@author 翻页问题数字2013-12-22+00:00:00  自动过略+号;
			$start_time = strtr($_REQUEST['start_time'],'+'," ");
			$end_time = strtr($_REQUEST['end_time'],'+'," ");
			
			if( $_REQUEST['start_time'] ) {
				$map["xc_order.".$_REQUEST['search_type']] = array(array('gt',strtotime($start_time)));
			}
			if( $_REQUEST['end_time'] ) {
				$map["xc_order.".$_REQUEST['search_type']] = array(array('lt',strtotime($end_time)));
			}
			if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
				
				$map["xc_order.".$_REQUEST['search_type']] = array(array('gt',strtotime($start_time)),array('lt',strtotime($end_time)));
				
			
			}
			
			$this->assign('search_type',$_REQUEST['search_type']);
			$this->assign('start_time',$_REQUEST['start_time']);
			$this->assign('end_time',$_REQUEST['end_time']);
		}

		if($_REQUEST['salecoupon_id']) {
			$map['xc_membersalecoupon.salecoupon_id'] = $_REQUEST['salecoupon_id'];
			if($_REQUEST['is_use'] == 1) {//绑定已使用==1
				$map['xc_membersalecoupon.is_use'] = 1;
			}
			if($_REQUEST['is_use'] == '0') {//绑定未使用==0
				$map['xc_membersalecoupon.is_use'] = '0';
			}
			$this->assign('salecoupon_id',$_REQUEST['salecoupon_id']);
			$this->assign('is_use',$_REQUEST['is_use']);
		}
		
		$model_order = D("Order");
		 // 计算总数
        $count = $model_order->where($map)->join("xc_membersalecoupon ON xc_order.id=xc_membersalecoupon.order_id")->count();

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		//分页跳转的时候保证查询条件
        foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != '') {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_order->where($map)->join("xc_membersalecoupon ON xc_order.id=xc_membersalecoupon.order_id")->order('id DESC')->limit($p->firstRow.','.$p->listRows)->field('xc_order.id,xc_order.order_time,xc_order.create_time,xc_order.uid,xc_order.mobile,xc_order.truename,xc_order.shop_id,xc_order.order_state,xc_order.iscomment,xc_order.postpone_time,xc_order.complete_time,xc_order.complain_state,xc_membersalecoupon.order_id,xc_membersalecoupon.is_use')->select();

		//echo $model_order->getLastsql();

		//trans_data模块
		$shop_model = D('Shop');
		$comment_model = D('Comment');
		$MemberModel = D('member');
		//$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
		    $list[$kk]['order_id'] = $this->get_orderid($vv['id']);
			$member = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
			$list[$kk]['fromstatus'] = $member['fromstatus'];
			
			/*
			$shop_info = $shop_model->find($vv['shop_id']);
			$list[$kk]['shop_id'] = $shop_info['id'];
			$list[$kk]['shop_name'] = $shop_info['shop_name'];
			

			$shop_name_array[$shop_info['id']]['id'] = $shop_info['id'];
			$shop_name_array[$shop_info['id']]['name'] = $shop_info['shop_name'];
			*/

			$c_map['order_id'] = $vv['id'];
			$list[$kk]['comment_num'] = $comment_model->where($c_map)->count();
		}
		//$shop_name_array = $this->array_unique_fb($shop_name_array);
		
		//$this->assign('shop_name_array' , $shop_name_array);

		$this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
	}

	/*
		@author:ysh
		@function:订单查询太复杂 独立index
		@time:2013-10-29 
	*/
	function index_bak() {
		//$map['xc_order.order_des'] = ' ';
		//_filter条件模块
		if (isset ( $_REQUEST ['licenseplate'] ) && $_REQUEST ['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%,'.$_REQUEST['licenseplate'].',%');
        }
		if(isset($_REQUEST['type']) && $_REQUEST['keyword'] !=''){
			$map[$_REQUEST['type']] = $_REQUEST['keyword'];
		}
		
		if(isset($_POST['shop_id']) && $_POST['shop_id'] !=''){
			$map['shop_id'] = $_POST['shop_id'];
		}
		if(isset($_GET['shop_id']) && $_GET['shop_id'] !=''){
			$map['shop_id'] = $_GET['shop_id'];
		}
		$this->assign('shop_id',$map['shop_id']);

		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !=''){
			$map['order_state'] = $_REQUEST['order_state'];
			$this->assign('order_state',$_REQUEST['order_state']);
		}
		if($_REQUEST['iscomment'] && $_REQUEST['iscomment']!='3'){
			if($_REQUEST['iscomment'] == 4){
				$map['iscomment'] = '0';
				$data['iscomment'] = '4';
			}else{
				$data['iscomment'] = $map['iscomment'] = $_REQUEST['iscomment'];
			}
		}else{
			$data['iscomment'] = $_REQUEST['iscomment'];
			unset($map['iscomment']);
		}

		if(isset($_REQUEST['search_type']) and $_REQUEST['search_type']!='' and isset($_REQUEST['start_time']) && $_REQUEST['start_time'] !=''){
			if( $_REQUEST['start_time'] ) {
				$start_time = str_replace('+'," ",$_REQUEST['start_time']);
				$map["xc_order.".$_REQUEST['search_type']] = array(array('gt',strtotime($start_time)));
			}
			if( $_REQUEST['end_time'] ) {
				$end_time = str_replace('+'," ",$_REQUEST['end_time']);
				$map["xc_order.".$_REQUEST['search_type']] = array(array('lt',strtotime($end_time)));
			}
			if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
				$start_time = str_replace('+'," ",$_REQUEST['start_time']);
				$end_time = str_replace('+'," ",$_REQUEST['end_time']);
				$map["xc_order.".$_REQUEST['search_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($end_time)));
			}
			
			$this->assign('search_type',$_REQUEST['search_type']);
			$this->assign('start_time',$start_time);
			$this->assign('end_time',$end_time);
		}

		if($_REQUEST['salecoupon_id']) {
			$map['xc_membersalecoupon.salecoupon_id'] = $_REQUEST['salecoupon_id'];
			if($_REQUEST['is_use'] == 1) {//绑定已使用==1
				$map['xc_membersalecoupon.is_use'] = 1;
			}
			if($_REQUEST['is_use'] == '0') {//绑定未使用==0
				$map['xc_membersalecoupon.is_use'] = '0';
			}
			$this->assign('salecoupon_id',$_REQUEST['salecoupon_id']);
			$this->assign('is_use',$_REQUEST['is_use']);
		}
		
		$model_order = D("Order");
		 // 计算总数
        $count = $model_order->where($map)->join("xc_membersalecoupon ON xc_order.id=xc_membersalecoupon.order_id")->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		//分页跳转的时候保证查询条件
        foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != '') {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_order->where($map)->join("xc_membersalecoupon ON xc_order.id=xc_membersalecoupon.order_id")->order('id DESC')->limit($p->firstRow.','.$p->listRows)->field('xc_order.id,xc_order.licenseplate,xc_order.order_time,xc_order.create_time,xc_order.uid,xc_order.mobile,xc_order.truename,xc_order.shop_id,xc_order.order_state,xc_order.iscomment,xc_order.postpone_time,xc_order.complete_time,xc_order.complain_state,xc_membersalecoupon.order_id,xc_membersalecoupon.is_use')->select();

		//echo $model_order->getLastSql();

		//trans_data模块
		$shop_model = D('Shop');
		$comment_model = D('Comment');
		$MemberModel = D('member');
		//$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
		    $list[$kk]['order_id'] = $this->get_orderid($vv['id']);
			$member = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
			$list[$kk]['fromstatus'] = $member['fromstatus'];
			
			
			$shop_info = $shop_model->find($vv['shop_id']);
			$list[$kk]['shop_id'] = $shop_info['id'];
			$list[$kk]['shop_name'] = $shop_info['shop_name'];
			

			$shop_name_array[$shop_info['id']]['id'] = $shop_info['id'];
			$shop_name_array[$shop_info['id']]['name'] = $shop_info['shop_name'];
			

			$c_map['order_id'] = $vv['id'];
			$list[$kk]['comment_num'] = $comment_model->where($c_map)->count();
		}
		//$shop_name_array = $this->array_unique_fb($shop_name_array);
		
		//$this->assign('shop_name_array' , $shop_name_array);

		$this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
	}
	

	function array_unique_fb($array2D){
		foreach ($array2D as $v){
			$v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[] = $v;
		}
		$temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
		foreach ($temp as $k => $v){
			$temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
		}
		return $temp;
	}
	/*
		@author:chf
		@function:显示用户来源信息页
		@time:2013-09-26
	*/
	function fromstatus(){
		$MemberModel = D('member');
		$map['uid'] = $_REQUEST['uid'];
		$member = $this->MemberModel->where($map)->find();
		$this->assign('member',$member);
		$this->assign('uid',$map['uid']);
		$this->display();
	}
	
	/*
		@author:chf
		@function:修改来源方法
		@time:2013-09-26
	*/
	function edit_do(){
		$MemberModel = D('member');
		$map['uid'] = $_REQUEST['uid'];
		$edit['fromstatus'] = $_REQUEST['fromstatus'];
		$this->MemberModel->where($map)->data($edit)->save();
		$this->success('修改成功！',U('/Store/Order/index'));
	}


	public function selectshop(){
		if($_GET['uid']){
		    if ($_GET['order_id']){
		        $model_order = D('Order');
		        $map_order['id'] = $_GET['order_id'];
		        $orderinfo = $model_order->where($map_order)->find();
		        if ($orderinfo['model_id'] || $orderinfo['u_c_id']){
    		        if ($orderinfo['u_c_id']){
    		            $this->assign('u_c_id',$orderinfo['u_c_id']); 
    		        }else{
        	            if ($orderinfo['brand_id']){
        	               $this->assign('brand_id',$orderinfo['brand_id']); 
        	            }
        	            if ($orderinfo['series_id']){
        	               $this->assign('series_id',$orderinfo['series_id']); 
        	            }
        	            if ($orderinfo['model_id']){
        	               $this->assign('model_id',$orderinfo['model_id']); 
        	            }
        	            $this->assign('other_car',1);
    		        }
    		        $this->assign('orderinfo',$orderinfo);
		        }
		        $data = array();
		        if ($orderinfo['shop_id']){
		            $shop_id = $orderinfo['shop_id'];
		            $model_shop_fs_relation = D('Shop_fs_relation');
            	    $model_carseries = D('Carseries');
            	    $model_carbrand = D('Carbrand');
            	    $model_carmodel = D('Carmodel');
                    $map_sf['shopid'] = $shop_id;
                    $shop_fs_relation = $model_shop_fs_relation->where($map_sf)->select();
                    if ($shop_fs_relation){
                        foreach ($shop_fs_relation as $k=>$v){
                            $map_series['fsid'] = $v['fsid'];
                            $carseries = $model_carseries->where($map_series)->select();
                            if ($carseries){
                                foreach ($carseries as $_k=>$_v){
                                    $map_brand['brand_id'] = $_v['brand_id'];
                                    $carbrand = $model_carbrand->where($map_brand)->find();
                                    $map_model['series_id'] = $_v['series_id'];
                                    $carmodel = $model_carmodel->where($map_model)->select();
                                    $data[$_v['brand_id']]['brand_id'] = $_v['brand_id'];
                                    $data[$_v['brand_id']]['fsid'] = $v['fsid'];
                                    $data[$_v['brand_id']]['brand_name'] = $carbrand['brand_name'];
                                    $data[$_v['brand_id']]['seriesinfo'][$_v['series_id']] = $_v;
                                    $data[$_v['brand_id']]['seriesinfo'][$_v['series_id']]['modelinfo'] = $carmodel;
                                }
                            }
                        }
                    }

		        }
		        $this->assign('data',$data);
		    }
		    
		    
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
    		}else{
    		    if ($orderinfo){
    		        $this->assign('select_services_str',$orderinfo['service_ids']);
    		    }
    		}
    		
    		//dump($list);
		
    		$model_carseries = D(GROUP_NAME.'/Carseries');
    		if (isset($model_id['series_id']) and $model_id['series_id']){
				
    		    $carseriesinfo = $model_carseries->find($model_id['series_id']);
    		    $fsid = $carseriesinfo['fsid'];
    		    $model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
    		    $relation_shopids = $model_shop_fs_relation->where("fsid=$fsid")->select();
					
    		   
    		    if ($relation_shopids){
					
    		        foreach ($relation_shopids as $shopid_v){
						
    		            if ($orderinfo['shop_id'] && $orderinfo['shop_id']==$shopid_v['shopid']){
    		                $shop_id = $shopid_v['shopid'];
    		            }else{
    		                $shopid_arr[] = $shopid_v['shopid'];
    		            }
    		        }
    		    }
    		   
    		    if ($shopid_arr || $shop_id){
    		        $shop_model = D(GROUP_NAME.'/Shop');
    		        $shopid_str = implode(',',$shopid_arr);
					
    		        if ($shop_id){
    		            if ($shopid_str){
    		                $shopid_str = $shop_id.','.$shopid_str;
    		            }else{
    		                $shopid_str = $shop_id;
    		            }
    		        }
					
					if($_POST['is_across'] != 1) {
						$condition['id'] = array('in',$shopid_str);
					}
				
    		        $condition['status'] = 1;
					if($_REQUEST['shop_name'] != '') {
						$condition['shop_name'] =  array('like', "%" . $_REQUEST['shop_name'] . "%");
						$this->assign("shop_name",$_REQUEST['shop_name']);
					}
    		        //$list_product = $this->_list($shop_model,$condition);
    		        $list_product = $shop_model->where($condition)->order('id ASC,shop_class ASC')->select();
					//echo $shop_model->getLastSql();
    		    }
    		}
    		
    		//$list_product = $shop_model->where("brand_id = $brand_id")->select();
    		

    		//echo '<pre>';print_r($list_product);
		if (!empty($list_product)){
		    $timesale_model = D(GROUP_NAME.'/Timesale');
		    $list_timesale_arr = array();
		    $model_region = D(GROUP_NAME.'/region');
		    $model_coupon = D(GROUP_NAME.'/Coupon');
		    foreach ($list_product as $key=>$val){
		        $area_info = $model_region->find($val['shop_area']);
		        $list_product[$key]['area_name'] = $area_info['region_name'];
		        
		        $timesale_map['xc_timesale.shop_id'] = $val['id'];
		        $timesale_map['xc_timesale.status'] = 1;
		        $timesale_map['xc_timesaleversion.status'] = 1;
		        $list_timesale = $timesale_model->where($timesale_map)->join("xc_timesaleversion ON xc_timesale.id = xc_timesaleversion.timesale_id")->order("xc_timesaleversion.s_time ASC")->select();
		        //echo '<pre>';print_r($timesale_model->getLastSql());exit;
		        if (!empty($list_timesale)){
		            $timesale_arr = array();
		            foreach ($list_timesale as $k=>$timesale){
		                $timesale['oldworkhours_sale'] = $timesale['workhours_sale'];
		            if (($timesale['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($timesale['e_time']<time()+3600*24) || $timesale['s_time']>(time()+24*3600*15) ){
		                    continue;
		                }
		                $not_coupon_services_str = '';
		                $coupon_amount = 0;
		                //绑定优惠券
//		                if ($timesale['coupon_id']){
//		                    $coupon = $model_coupon->find($timesale['coupon_id']);
//		                    if ($coupon['is_delete']=='0'){
//		                        if ($coupon['coupon_discount']>0){
//		                            $timesale['workhours_sale'] = max($timesale['workhours_sale']-$coupon['coupon_discount'],0);
//		                            if ($timesale['workhours_sale'] == 0){
//		                                $timesale['workhours_sale'] = '-1';
//		                            }
//		                        }elseif ($coupon['coupon_amount']>0){
//		                            $coupon_amount = $coupon['coupon_amount'];
//		                        }
//		                        $services_str = $this->get_coupon_serverids($coupon['service_ids'],$select_services_str);
//		                        $not_coupon_services_str = $this->get_not_coupon_serverids($coupon['service_ids'],$select_services_str);
//		                    }
//		                    $timesale['coupon_url'] = $coupon['coupon_url'];
//		                }else {
            			    $services_str = $select_services_str;
            			    $timesale['coupon_url'] = '';
//		                }
						
		                $timesale_arr[$timesale['timesale_id']][$k] = $timesale;
		                if ($timesale['oldworkhours_sale']>0){
            		        $timesale_arr[$timesale['timesale_id']][$k]['oldworkhours_sale_str'] = round($timesale['oldworkhours_sale']*10,1)."折";
            		    }else {
            		        $timesale_arr[$timesale['timesale_id']][$k]['oldworkhours_sale_str'] = "无折扣";
            		    }
            		    if ($timesale['workhours_sale']>0){
            		        $timesale_arr[$timesale['timesale_id']][$k]['workhours_sale_str'] = round($timesale['workhours_sale']*10,1)."折";
            		        $timesale_arr[$timesale['timesale_id']][$k]['share_workhours_sale_str'] = "工时费：".round($timesale['workhours_sale']*10,1)."折";
            		    }else {
            		        if ($timesale['workhours_sale'] == '-1'){
            		            $timesale_arr[$timesale['timesale_id']][$k]['workhours_sale_str'] = "0扣";
                		        $timesale_arr[$timesale['timesale_id']][$k]['share_workhours_sale_str'] = "工时费：全免";
            		        }else{
                		        $timesale_arr[$timesale['timesale_id']][$k]['workhours_sale_str'] = "无折扣";
                		        $timesale_arr[$timesale['timesale_id']][$k]['share_workhours_sale_str'] = "";
            		        }
            		    }
            		    if ($timesale['product_sale']>0){
            		        $timesale_arr[$timesale['timesale_id']][$k]['product_sale_str'] = round($timesale['product_sale']*10,1)."折";
            		        $timesale_arr[$timesale['timesale_id']][$k]['share_product_sale_str'] = "，零件费：".round($timesale['product_sale']*10,1)."折";
            		    }else {
            		        $timesale_arr[$timesale['timesale_id']][$k]['product_sale_str'] = "无折扣";
            		        $timesale_arr[$timesale['timesale_id']][$k]['share_product_sale_str'] = "";
            		    }
            			$timesale_arr[$timesale['timesale_id']][$k]['week_name'] = explode(',',$timesale['week']);
            			foreach($timesale_arr[$timesale['timesale_id']][$k]['week_name'] AS $kk=>$vv){
            			    if ($timesale_arr[$timesale['timesale_id']][$k]['week_name'][$kk]=='0'){
            			        $timesale_arr[$timesale['timesale_id']][$k]['week_name'][$kk] = '日';
            			    }
            			    $timesale_arr[$timesale['timesale_id']][$k]['week_name_s'] .= '周'.$timesale_arr[$timesale['timesale_id']][$k]['week_name'][$kk].',';
            			}
            			$timesale_arr[$timesale['timesale_id']][$k]['week_name_s'] = substr($timesale_arr[$timesale['timesale_id']][$k]['week_name_s'],0,-1);
            			if ($services_str !=''){
            			    //$list_product[$key]['product_info'][$timesale['id']] = $this->ajax_get_product_info($model_id['model_id'],$val['id'],$services_str,$timesale['workhours_sale'],$timesale['product_sale'],$timesale['id'],$timesale['memo'],$timesale['coupon_id'],$coupon_amount);
            			}

            			$timesale_arr[$timesale['timesale_id']][$k]['services_str'] = $services_str;
            			$timesale_arr[$timesale['timesale_id']][$k]['not_coupon_services_str'] = $not_coupon_services_str;

            			//$timesale_arr[$timesale['timesale_id']][$k]['wb_url'] = 'url_data/'.$url_data_encode.'/shopid/'.$val['id'].'/timesaleid/'.$timesale['id'];
            			//$timesale_arr[$timesale['timesale_id']][$k]['s_wb_url'] = '/s_url/'.sha1($timesale_arr[$timesale['timesale_id']][$k]['wb_url']);
		                //$list_product[$key]['getShop'] = $shop_model->where("id = $_POST[shop_id]")->find();
		            }

		            $list_timesale_arr[$val['id']]['timesale_arr'] = $timesale_arr;
		        }
		    }
		}

    		//echo '<pre>';print_r($list_timesale_arr);exit;
    		//用户三级联动的第一级，品牌
			
			$model_member = D('Member');
		    $memberinfo = $model_member->find($_GET['uid']);
		    $this->assign('memberinfo',$memberinfo);
		

    		$model = D('carbrand');
    		$brand = $model->select();
    		$this->assign('brand',$brand);
    		$this->assign('list_si_level_0',$list_si_level_0);
    		$this->assign('list_si_level_1',$list_si_level_1);
    		$this->assign('list_membercar',$list_membercar);
    		$this->assign('list',$list_product);
    		$this->assign('list_timesale_arr',$list_timesale_arr);
    		$this->assign('uid',$uid);
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
        							'str'=>'通过携车网预订您所选择的维修保养项目，共为您节省：','color'=>'0, 0, 0','size'=>'15','colspan'=>'4','width'=>'630'
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
*插入订单的表单
*
*
*/
    public function addorder() {
		if (!empty($_REQUEST['select_services'])){
		    $model_service = D('serviceitem');
		    $map['id'] = array('in',$_REQUEST['select_services']);
		    $services_info = $model_service->where($map)->select();
		    unset($map);
		    $this->assign('services_info',$services_info);
		}else {
		    $this->error("请选择您所需要的维修保养项目！");
		}
		$uid = $_GET['uid']; //获取uid
		if ($uid){
		    $model_order = D('Order');
		    $model_ordernologin = D('Ordernologin');
    		if ($_REQUEST['order_id']){
    		    //$map_order['uid'] = $uid;
    		    $map_order['id'] = $_REQUEST['order_id'];
    		    $lastorder = $model_ordernologin->where($map_order)->order("id DESC")->find();
    		    if ($lastorder){
    		        if ($lastorder['licenseplate']){
    		            $lastorder['s_pro'] = substr($lastorder['licenseplate'],0,3);
    		            $lastorder['licenseplate'] = substr($lastorder['licenseplate'],3);
    		        }
    		        if ($lastorder['order_time']){
    		            $lastorder['order_date'] = date('Y-m-d',$lastorder['order_time']);
    		            $lastorder['order_hour'] = date('H',$lastorder['order_time']);
    		            $lastorder['order_min'] = date('i',$lastorder['order_time']);
    		        }
    		    }
    		}else{
    		    $map_order['uid'] = $uid;
    		    $map_order['model_id'] = $_REQUEST['model_id'];
    		    $lastorder = $model_order->where($map_order)->order("id DESC")->find();
    		    if ($lastorder){
    		        if ($lastorder['licenseplate']){
    		            $lastorder['s_pro'] = substr($lastorder['licenseplate'],0,3);
    		            $lastorder['licenseplate'] = substr($lastorder['licenseplate'],3);
    		        }
					$ordercarinfo = $this->get_car_info($lastorder['brand_id'],$lastorder['series_id'],$lastorder['model_id']);
					
					$this->assign('ordercarinfo',$ordercarinfo);
    		    }
    		}
    		$this->assign('lastorder',$lastorder);
		}
		$model_member = D('Member'); 
		$list = $model_member->getByUid($uid); //查询用户信息	

		$this->WebusercarModel = D('webusercar'); //客服备注车型
		$Webusercar = $this->WebusercarModel->where(array('uid'=>$uid,'model_id'=>$_REQUEST['model_id']))->find();
		$Webusercar['s_pro'] = substr($Webusercar['car_number'],0,3);
    	$Webusercar['licenseplate'] = substr($Webusercar['car_number'],3);
		if($Webusercar){
			$carinfo = $this->get_car_info($Webusercar['brand_id'],$Webusercar['series_id'],$Webusercar['model_id']);
			$this->assign('Webusercar',$Webusercar);
			$this->assign('carinfo',$carinfo);
		}

		
		$model_product = D(GROUP_NAME.'/Product');//载入产品MODEL
		$map['model_id'] = array('eq',$_REQUEST['model_id']); 
		//$map['id'] = array('in',$_REQUEST['product_str']);  //获取多个产品的字符窜
		$map['service_id'] = array('in',$_REQUEST['select_services']);  //获取多个服务的字符窜
		$get_product = $model_product->where($map)->select();  //查询产品
		//echo '<pre>';print_r($get_product);exit;
		$order_model = D(GROUP_NAME.'/Order');
		//$list_product = $order_model->ListProduct($get_product);//格式化数据
		$timesale_model = D('Timesaleversion');
		$map_timesalev['id'] = $_GET['timesaleversion_id'];
		$sale_arr = $timesale_model->where($map_timesalev)->find();
		if (!empty($sale_arr)){
		    if ($sale_arr['product_sale'] >0){
		        $sale_arr['product_sale_str'] = ($sale_arr['product_sale']*10).'折';
		    }else {
		        $sale_arr['product_sale_str'] = '无折扣';
		    }
		    if ($sale_arr['workhours_sale'] >0){
		        $sale_arr['workhours_sale_str'] = ($sale_arr['workhours_sale']*10).'折';
		    }else {
		        $sale_arr['workhours_sale_str'] = '无折扣';
		    }
		    $this->assign('sale_arr',$sale_arr);
		}
		
		//$output_str = $order_model->ListProductdetail_S($get_product,$sale_arr);//处理数据，输出表格
		$shop_model = D(GROUP_NAME.'/Shop');
		unset($map);
		$map['id'] = $_GET['shop_id'];
		$get_shop = $shop_model->where($map)->find();//商家信息
		//获取分时折扣id
		if($_GET['timesaleversion_id']){
			$model_timesale = D('Timesale'); //载入模型
			$map_ts['xc_timesaleversion.id'] = $_GET['timesaleversion_id'];
			$list_timesale = $model_timesale->where($map_ts)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find(); //根据id查询分时折扣信息
			$sale_check = sale_check($list_timesale['week']);  //根据分时折扣的星期数，处理无效日期
			$min_hours = explode(':',$list_timesale['begin_time']);   //分时具体上下午时间输出到模板，做判断
			$max_hours = explode(':',$list_timesale['end_time']);     //分时具体上下午时间输出到模板，做判断
			$now = time();
		    $fourhour = strtotime(date('Y-m-d').' 16:00:00');
		    
		    if ($now < $fourhour){
		        $min = 0;
		        $max = 100;
		    }else{
		        $min = 0;
		        $max = 100;
		    }
		    if(($list_timesale['s_time'] - strtotime(date('Y-m-d')))>0){
		        $s_day = floor(($list_timesale['s_time'] - strtotime(date('Y-m-d')))/24/3600);
		        $min = max($s_day,$min);
		    }
		    if(($list_timesale['e_time'] - strtotime(date('Y-m-d')))>0){
		        $e_day = floor(($list_timesale['e_time'] - strtotime(date('Y-m-d')))/24/3600);
		        $max = min($e_day,$max);
		    }
			$minday = "%y-%M-{%d+".$min."}";
			$maxday = "%y-%M-{%d+".$max."}";
			$this->assign("minday",$minday);
			$this->assign("maxday",$maxday);
		}
		$doubleCalendar = double_or_single_Calendar(); //单双月显示判断
		$car_model = D('Membercar');
		if (isset($_GET['u_c_id']) and !empty($_GET['u_c_id'])){
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
		}else {
		    $my_car['brand_id'] = $_GET['brand_id'];
		    $my_car['series_id'] = $_GET['series_id'];
		    $my_car['model_id'] = $_GET['model_id'];
		}
		//用户所有自定义车型初始化
		//$my_car = $car_model->Membercar_format_by_arr($my_car,1);
		$this->assign('my_car',$my_car);
		Cookie::set('_currentUrl_', __SELF__);
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
		//$this->assign('output_str',$output_str);
		$this->display();
	}
	
	/*
		author:chf
		function:代下单添加数据
		time:2013-9-1
	*/
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
    		//载入产品MODEL
    		$model_product = D('Product');
    		//$map['product_id'] = array('in',$_REQUEST['product_str']);
    		$map['service_id'] = array('in',$_REQUEST['select_services']);
    		$map['model_id'] = array('eq',$_REQUEST['model_id']);
    		$list_product = $model_product->where($map)->select();
    		$model_membercar = D('Membercar');
    		$uid = $_POST['uid'];
    		$order_time= $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
    		$order_time = strtotime($order_time);
    		$now = time();
    		
		    $timesale_model = D('Timesale');
    		$map_tsv['xc_timesaleversion.id'] = $_POST['timesaleversion_id'];
    		$sale_arr = $timesale_model->where($map_tsv)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find();


    		if ($order_time>$sale_arr['s_time'] and $order_time<$sale_arr['e_time']){
    		    $order_week = date("w",$order_time);
    		    $normal_week = explode(',',$sale_arr['week']);

    		    if (!in_array(intval($order_week),$normal_week)){
    		        $this->error('预约时间错误,请重新选择！');
    		    }
    		    $order_hour = date("H:i",$order_time);
    		    if (strtotime(date('Y-m-d').' '.$order_hour)<strtotime(date('Y-m-d').' '.$sale_arr['begin_time']) || strtotime(date('Y-m-d').' '.$order_hour)>strtotime(date('Y-m-d').' '.$sale_arr['end_time'])){
    		        $this->error('预约时间错误,请重新选择！');
    		    }
    		}else{
    		    $this->error('预约时间错误,请重新选择！');
    		}
    		$membercoupon_id = isset($_POST['membercoupon_id'])?$_POST['membercoupon_id']:0;
    		if ($membercoupon_id){
    		    $model_membercoupon = D('Membercoupon');
    		    $map_coupon['xc_membercoupon.membercoupon_id'] = $membercoupon_id;
    		    $coupon_info = $model_membercoupon->where($map_coupon)->join("xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->find();
    		    if ($now>$coupon_info['end_time']){
    		        $this->error('优惠券已经过期,请重新下定！','/index.php/order');
    		    }
    		}
    		
    		if(!$u_c_id = $_POST['u_c_id']){
    			$u_c_id = 0;
    		}
    		
    		//计算订单总价格
    		$total_product_price = 0;
		    $total_workhours_price = 0;
		    $productversion_ids_arr = array();
    		if (!empty($list_product)){
    		    foreach ($list_product as $kk=>$vv){
    		        $productversion_ids_arr[] = $vv['versionid'];
    		        $list_product[$kk]['list_detai'] = unserialize($vv['product_detail']);
    		        if (!empty($list_product[$kk]['list_detai'])){
    		            foreach ($list_product[$kk]['list_detai'] as $key=>$val){
    		                $list_product[$kk]['list_detai'][$key]['total'] = $val['price']*$val['quantity'];
    		                if ($val['Midl_name'] == '工时费'){
    		                    $total_workhours_price += $list_product[$kk]['list_detai'][$key]['total'];
    		                }else {
    		                    $total_product_price += $list_product[$kk]['list_detai'][$key]['total'];
    		                }
    		            }
    		        }
    		    }
    		}
    		$productversion_ids_str = implode(",",$productversion_ids_arr);
    		
    		$total_price = 0;
    		$save_price = 0;
    		$save_discount = 0.00;
		    if (!empty($sale_arr)){
    		    if ($sale_arr['product_sale']>0){
    		        $total_price += $total_product_price*$sale_arr['product_sale'];
    		    }else {
    		        $total_price += $total_product_price;
    		    }
    		    if ($sale_arr['workhours_sale']=='0.00'){
    		        $sale_arr['workhours_sale'] = 1;
    		    }
    		    if (isset($coupon_info) and $coupon_info){
    		        if ($coupon_info['coupon_discount']){
    		             $workhours_sale = $sale_arr['workhours_sale'] - $coupon_info['coupon_discount'];
    		             if ($workhours_sale<=0){
    		                 $workhours_sale = '-1';
    		             }
    		        }else{
    		            $workhours_sale = $sale_arr['workhours_sale'];
    		        }
    		    }else {
    		        $workhours_sale = $sale_arr['workhours_sale'];
    		    }
    		    if ($workhours_sale>0){
    		        $total_price += $total_workhours_price*$workhours_sale;
    		        $save_price = $total_workhours_price*($sale_arr['workhours_sale']-$workhours_sale);
    		        $save_discount = $sale_arr['workhours_sale']-$workhours_sale;
    		    }elseif($workhours_sale=='-1') {
    		        $total_price += $total_workhours_price*0;
    		        $save_price = $total_workhours_price;
    		        $save_discount = $sale_arr['workhours_sale'];
    		    }
    		}else {
    		    $total_price += $total_product_price+$total_workhours_price;
    		}
    		if (isset($coupon_info) and $coupon_info and $coupon_info['coupon_discount']=='0.00' and $coupon_info['coupon_amount']>0){
    		    $save_price = $coupon_info['coupon_amount'];
    		}
    		$data = array(
    			'u_c_id'=>$u_c_id,
    			'uid'=>$uid,
    			'shop_id'=>$_POST['shop_id'],
    			'brand_id'=>$_POST['brand_id'],
    			'series_id'=>$_POST['series_id'],
    			'model_id'=>$_POST['model_id'],
    			'timesaleversion_id'=>$_POST['timesaleversion_id'],
    			'service_ids'=>$_REQUEST['select_services'],
    			'product_sale'=>$sale_arr['product_sale'],
    			'workhours_sale'=>$sale_arr['workhours_sale'],
    			'truename'=>$_POST['truename'],
    			'mobile'=>$_POST['mobile'],
    			'licenseplate'=>trim($_POST['cardqz'].$_POST['licenseplate']),
    			'mileage'=>$_POST['miles'],
    			'car_sn'=>$_POST['car_sn'],
    			'remark'=>$_POST['remark'],
    			'operator_id'=>$_SESSION[C('USER_AUTH_KEY')],
    			'order_time'=>$order_time,
    			'create_time'=>time(),
    		    'total_price'=>$total_price,
    		    'productversion_ids'=>$productversion_ids_str,
    		    'membercoupon_id'=>$membercoupon_id,
    		    'coupon_save_money'=>$save_price,
    		    'coupon_save_discount'=>$save_discount,
    		);
		    $model = D('Order');
    		if(false !== $model->add($data)){
    			$order_id = $model->getLastInsID();
    			if ($_REQUEST['order_id']){
    			    $model_ordernologin = D('Ordernologin');
    			    $order_data['uid'] = $uid;
    			    $map_ordernologin['id'] = $_REQUEST['order_id'];
    			    $model_ordernologin->where($map_ordernologin)->save($order_data);
    			}
    			if ($membercoupon_id){
    			    $membercoupon_map['membercoupon_id'] = $membercoupon_id;
    			    $membercoupon_data['use_time'] = time();
    			    $membercoupon_data['is_use'] = 1;
    			    $membercoupon_data['order_id'] = $order_id;
    			    $model_membercoupon->where($membercoupon_map)->save($membercoupon_data);
    			}
    		}
    		$model_member = D('Member');
    		$get_user_name = $model_member->where("uid=$uid")->find();
    		foreach($list_product AS $k=>$v){
    				$sub_order[]=array(
    				'order_id'=>$order_id,
    				'productversion_id'=>$list_product[$k]['versionid'],
    				'service_id'=>$list_product[$k]['service_id'],
    				'service_item_id'=>$list_product[$k]['service_item_id'],
    				'uid'=>$uid,
    				'user_name'=>$get_user_name['username'],
    				'series_id'=>$_POST['series_id'],
    				'create_time'=>time(),
    				'update_time'=>time(),
    				);
    		}
    		$model_suborder = D('Suborder');
    		$list=$model_suborder->addAll($sub_order);
			//记录订单操作记录开始
			$order_log['name'] = $_SESSION['loginAdminUserName'];//记录用户名
			$order_log['ip'] = $_SERVER["REMOTE_ADDR"];//记录ip
			$order_log['order_id'] = $order_id;//订单号
			$order_log['create_time'] = time();//创建时间	
			$order_log['content'] = '客服代下单';//操作内容
			$this->order_logModel->add($order_log);
			//记录订单操作记录结束
    		if(!empty($order_id)){
    			$this->success('预约提交成功！',U('/Store/Order/edit/order_id/'.$order_id));
    		}else {
    		    $this->error('预约提交失败！',U('/Store/Order/selectshop/uid/'.$uid));
    		}
		}
	}
	
	/*public function insert(){
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

	
	}*/
    
	public function check_member(){
	    $mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:0;
	    if ($mobile) {
	    	$model_member = D('Member');
	    	$map_member['mobile'] = $mobile;
	    	$memberinfo = $model_member->where($map_member)->find();
	    	if ($memberinfo){
	    	    echo json_encode($memberinfo);
	    	    exit;
	    	}
	    }
	    echo 0;
	    exit;
	}
	
	public function bangding_member(){
	    $mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:0;
	    $order_id = isset($_REQUEST['order_id'])?$_REQUEST['order_id']:0;
	    if ($mobile and $order_id) {
	    	$model_member = D('Member');
	    	$model_order = D('Order');
	    	$map_member['mobile'] = $mobile;
	    	$memberinfo = $model_member->where($map_member)->find();
	    	if ($memberinfo){//APDATE
	    	    $uid = $memberinfo['uid'];
	    	    //$map_order['id'] = $order_id;
	    	    //$data['uid'] = $uid;
	    	    //if($model_order->where($map_order)->save($data)){
	    	        echo $uid;exit;
	    	    //}
	    	}else{//ADD
	    	    $rand = rand_string(6,1);
				//$_POST['username'] = 'xieche_'.$rand.'_'.time();
				$data['password'] = md5($rand);
				$data['rand'] = $rand;
				$data['reg_time'] = time();
				$data['ip'] = get_client_ip();
	    	    $data['mobile'] = $mobile;
	    	    if($model_member->add($data)){
	    	        echo $model_member->getLastInsID();exit;
	    	    }
	    	}
	    }
	    echo 0;
	    exit;
	}
	
	//编辑
	public function edit(){
	    $model = D(GROUP_NAME.'/Order');
		$model_member = D(GROUP_NAME.'/Member');
	    if ($_REQUEST['order_id']){
			
	        $model_orderdeallog = D(GROUP_NAME.'/Orderdeallog');
	        $data['order_id'] = $_REQUEST['order_id'];
	        $data['createtime'] = time();
	        $data['deal_uid'] = $_SESSION['authId'];
	        $data['status'] = 1;
	        $data['memo'] = '订单处理';
	        $model_orderdeallog->add($data);
	        $dataorder['update_time'] = time();
	        $maporder['id'] = $_REQUEST['order_id'];
	        $model->where($maporder)->save($dataorder);
			$Membercoupon = $this->MembercouponModel->where(array('order_id'=>$_REQUEST['order_id']))->select();
			
			$order_log = $this->order_logModel->where(array('order_id'=>$data['order_id']))->order('create_time desc')->find();
			$this->assign('Membercoupon',$Membercoupon);
			$this->assign('order_log',$order_log);
	    }else {
	        $this->error('缺少订单号！');
	    }
        
		$vo = $model->getById($_REQUEST['order_id']);
		if ($vo['uid'] == 0){
		    $map_member['mobile'] = $vo['mobile'];
		    $memberinfo = $model_member->where($map_member)->find();
		    $this->assign('memberinfo',$memberinfo);
		}
		$vo['order_id'] = $this->get_orderid($vo['id']);
	    if ($vo['coupon_save_discount']>0){
	        $workhours_sale = sprintf("%.2f", $vo['workhours_sale']-$vo['coupon_save_discount']);
		    if ($workhours_sale=='0.00'){
		        $workhours_sale = -1;
		    }
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
		//dump($vo);
		//$model_membercar = D('Membercar');
		$select_car = $this->Membercar_format_by_model_id($vo['model_id']);
		$map['xc_order.id'] = array('eq',$_GET['order_id']);
		$list_by_suborder = $model->where($map)->join('xc_suborder ON xc_suborder.order_id = xc_order.id')->select();
		//echo '<pre>';print_r($list_by_suborder);exit;
		if (!empty($list_by_suborder)){
    		$service_ids_arr = explode(',',$list_by_suborder[0]['service_ids']);
		}
		$model_serviceitem = D('Serviceitem');
		$serviceitem = $model_serviceitem->select();
		$serviceitem_arr = array();
		if (!empty($serviceitem)){
		    foreach ($serviceitem as $v){
		        $serviceitem_arr[$v['id']] = $v;
		    }
		}
	    $product_img_str = $vo['shop_id'].'_'.$vo['model_id'].'_'.$vo['service_ids'].'_'.$vo['productversion_ids'].'_'.$vo['timesaleversion_id'].'_'.$workhours_sale.'_'.$vo['product_sale'];
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
		
		$model_expert = D(GROUP_NAME.'/Expert');
		$map_e['shop_id'] = $vo['shop_id'];
		$expert = $model_expert->where($map_e)->select();
		if ($expert){
		    foreach ($expert as $kk=>$vv){
		        $uid = $vv['uid'];
		        $map_m['uid'] = $uid;
		        $member = $model_member->where($map_m)->find();
		        $expert[$kk]['username'] = $member['username'];
		    }
		}

		$member = $model_member->where(array('uid'=>$vo['uid']))->find();
		$vo['fromstatus'] = $member['fromstatus'];

		$this->assign('expert', $expert);
		$this->assign('serviceitem', $serviceitem_arr);
		$this->assign('select_car',$select_car);
		$this->assign('list_by_suborder',$list_by_suborder);
		$this->assign('service_ids_arr',$service_ids_arr);
		$postpone_time = date('Y-m-d H:i:s',$vo['postpone_time']);
        $this->assign('vo', $vo);

		$this->assign('postpone_time', $postpone_time);
        $this->assign('coupon', $coupon);

		foreach($service_ids_arr as $k=>$v){
			$servicename.=$serviceitem_arr[$v]['name'];
		}

		if ($vo['workhours_sale']>0){
			$workhours_sale_str = ($vo['workhours_sale']*10).'折';
		}elseif($vo['workhours_sale'] == '-1') {
			$workhours_sale_str = '全免';
		}else{
			$workhours_sale_str = '无折扣';
		}
		if ($vo['product_sale']>0){
			$product_sale_str = ($vo['product_sale']*10).'折';
		}else{
			$product_sale_str = '无折扣';
		}

		//抵用券显示
		$model_membersalecoupon = D('Membersalecoupon');
		$model_salecoupon = D('Salecoupon');
		//已绑定的
		$salecoupon_map = array();
		$salecoupon_map['uid'] = $vo['uid'];
		$salecoupon_map['order_id'] = $vo['id'];
		//$salecoupon_map['shop_id'] = $vo['shop_id'];
		//$salecoupon_map['is_use'] = 1;
		$membersalecouponlist = $model_membersalecoupon->where($salecoupon_map)->select();

		//等待绑定的
		if( $membersalecouponlist == "") {
			$salecoupon_map = array();
			$salecoupon_map['xc_membersalecoupon.uid'] = $vo['uid'];
			$salecoupon_map['xc_membersalecoupon.order_id'] = '0';
			$salecoupon_map['_string'] = "FIND_IN_SET('{$vo[shop_id]}', xc_membersalecoupon.shop_ids) OR FIND_IN_SET('{$vo[shop_id]}', xc_salecoupon.shop_ids)";
			$salecoupon_map['xc_membersalecoupon.is_use'] = 0;
			$salecoupon_map['xc_membersalecoupon.is_delete'] = 0;
			$salecoupon_map['xc_membersalecoupon.start_time'] = array('lt',time());
			$salecoupon_map['xc_membersalecoupon.end_time'] = array('gt',time());

			$membersalecouponlist = $model_membersalecoupon->where($salecoupon_map)->join("xc_salecoupon ON xc_membersalecoupon.salecoupon_id=xc_salecoupon.id")->select();
		}

		if($membersalecouponlist) {
			foreach($membersalecouponlist as $key=>$val) {
				$salecoupon = $model_salecoupon->where(array('id'=>$val['salecoupon_id'],'is_delete'=>0))->find();
				$membersalecouponlist[$key]['coupon_amount'] = $salecoupon['coupon_amount'];
			}
		}
		//print_r($membersalecouponlist);

			/*判断是否选择过车型 如果没有页面显示改店铺下所有类型车选择*/
		if(!$vo['model_id']){
			
			$fs = $this->shop_fs_relationModel->where(array('shopid'=>$vo['shop_id']))->select();
			foreach($fs as $k=>$v){
				$fs_arr[] = $v['fsid']; 
			}
			$carseries_map['fsid'] = array('in',$fs_arr);
			$carseries = $this->carseriesModel->group('brand_id')->where($carseries_map)->select();
			foreach($carseries as $k=>$v){
				$brand_arr[] = $v['brand_id']; 
			}
			$band_map['brand_id'] = array('in',$brand_arr);
			$brand = $this->carbrandModel->where($band_map)->select();
			//echo $this->carbrandModel->getlastSql();
			$this->assign('brand',$brand);
			$this->assign('brand_car','1');
		}else{
			//2为没值
			$this->assign('brand_car','2');
		}

		/**/
		$this->assign('membersalecouponlist',$membersalecouponlist);
		$send_add_order_data = '您的车辆'.$vo['licenseplate'].'预定的'.$vo['order_time']['date'].' '.$vo['order_time']['hour'].':'.$vo['order_time']['minute'].'去'.$vo['shop_info']['shop_name'].'['.$vo['shop_info']['shop_address'].']进行'.$servicename.'的服务已预订成功，工时费'.$workhours_sale_str.'，配件费'.$product_sale_str.'，到了4S店后请告知工作人员您是通过携车网预定的，将会有专人为您提供贴心的服务，到店时请出示此短信以享受上述折扣,如有问题请致电携车网'.C('CALL_400');
		$this->assign('send_add_order_data',$send_add_order_data);
		$this->assign('hide_order_id',$_REQUEST['order_id']);
		$this->assign('authId',$_SESSION['authId']);
        $this->display();
		
		
	}

	function Get_series(){
		$brand_id = $_POST['brand_id'];
		$carseries = $this->carseriesModel->where(array('brand_id'=>$brand_id))->select();
		echo json_encode($carseries);
	}

	function Get_model(){
		$series_id = $_POST['series_id'];
		$carmodel = $this->carModel->where(array('series_id'=>$series_id))->select();
		echo json_encode($carmodel);
	}


	/*
		@author:chf
		@function:修改订单前
		@time:2013-10-11
	*/
	public function  _before_update(){
		$order_time = $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
		$_POST['order_time'] = strtotime($order_time);
		if (!$_POST['product_sale']){
		    $_POST['product_sale'] = '0.00';
		}
		if (!$_POST['workhours_sale']){
		    $_POST['workhours_sale'] = '0.00';
		}
		if($_POST['postpone_time']){
			$_POST['postpone_time'] = strtotime($_POST['postpone_time']);
		}
	}

	/*
		@author:chf
		@function:验证车会订单
		@time:2013-11-28
	*/
	function checkcode(){
		$mobile = $_POST['mobile'];
		$code = $_POST['code'];
		$uid = $_POST['uid'];
		$carcode = $this->CarcodeModel->where(array('status'=>'1','coupon_code'=>$code))->count();//随机找个已经生成的车会验证码
		if($carcode>0){
			echo "fail";//验证码已使用
			exit;
		}
		$carcode = $this->CarcodeModel->where(array('status'=>'0','coupon_code'=>$code))->count();//随机找个已经生成的车会验证码
		if($carcode==0){
			echo "noway";//验证码填写错误
			exit;
		}else{
			$membersalecouponcount = $this->MembersalecouponModel->where(array('mobile'=>$mobile,'is_use'=>'0','salecoupon_id'=>'3'))->find();
			if(count($membersalecouponcount)==0){
				$salecoupon = $this->Salecouponmodel->where(array('id'=>'3'))->find();//查找车会的相关优惠卷信息
				//插入membersalecoupon表
				$Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
				$Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
				$Membersalecoupon['mobile'] = $mobile;
				$Membersalecoupon['create_time'] = time();
				$Membersalecoupon['start_time'] = $salecoupon['start_time'];
				$Membersalecoupon['end_time'] = $salecoupon['end_time'];
				$Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
				$Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
				$Membersalecoupon['coupon_code'] = $code;
				$Membersalecoupon['from'] = 'ch';//来源
				$Membersalecoupon['uid'] = $uid;
				$membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
				
				$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
				$end_time = date('Y-m-d',$salecoupon['end_time']);
				
				//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
				
				$verify_str = "您的抵用券已绑定到您的账户.请通过携车网预订后凭消费码:".$code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服电话4006602822";

				//130  131  132 155 156  186  185 联通短信多发内容
				$submobile = substr($mobile,0,3);
				$datamobile = array('130','131','132','155');
				if(in_array($submobile,$datamobile)){
					$verify_str = $verify_str."回复TD退订";
				}
				//130  131  132 155 156  186  185 联通短信多发内容

				$send_verify = array(
					'phones'=>$mobile,
					//'phones'=>'13681971367',
					'content'=>$verify_str,
				);
				curl_sms($send_verify);
				$send_verify['sendtime'] = time();
				$this->Smsmodel->add($send_verify);
				$this->CarcodeModel->where(array('coupon_code'=>$code))->save(array('status'=>'1'));
			
				echo $membersalecoupon_id;
				exit;
			}else{
				echo $membersalecouponcount['membersalecoupon_id'];
				exit;
			}
			
		}
		
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
    				'content'=>$_REQUEST['send_add_order_data'],
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
	


	/*
		@author:ysh
		@function:为了修改后页面不跳转 将更新的操作写出来
		@time:2013/7/23
	*/
	public function update() {
		$model = D(GROUP_NAME . "/" . $this->getActionName());
        if (false === $model->create()) {
            $this->error($model->getError());
        }
		
		if($_REQUEST['id']){
			$map['id'] = $_REQUEST['id'];
		
		}
        // 更新数据
		$result = $model->where($map)->save();
		$model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
		if($_REQUEST['membersalecoupon_id']) {
			
			
			$salecoupon_map['membersalecoupon_id'] = $_REQUEST['membersalecoupon_id'];
			$salecoupon_data['order_id'] = $_REQUEST['id'];
			$model_membersalecoupon->where($salecoupon_map)->save($salecoupon_data);
		}
		
		if($_REQUEST['hide_id'] && !$_REQUEST['membersalecoupon_id']){
			$salecoupon_map['membersalecoupon_id'] = $_REQUEST['hide_id'];
			$salecoupon_data['order_id'] = $_REQUEST['id'];
			$model_membersalecoupon->where($salecoupon_map)->save($salecoupon_data);
			$model->where(array('id'=>$salecoupon_data['order_id']))->save(array('membersalecoupon_id'=>$salecoupon_map['membersalecoupon_id']));
			
		}
		if($_REQUEST['fromstatus']) {
			$from_map['uid'] = $_REQUEST['uid'];
			$from_edit['fromstatus'] = $_REQUEST['fromstatus'];
			$this->MemberModel->where($from_map)->data($from_edit)->save();
		}
		 //成功提示
		$this->success("修改成功");
	}


	public function update_order_state(){
	    $id = isset($_POST['id'])?$_POST['id']:0;
		$order_log['name'] = $_SESSION['loginAdminUserName'];//记录用户名
		$order_log['ip'] = $_SERVER["REMOTE_ADDR"];//记录ip
		$order_log['order_id'] = $id;//订单号
		$order_log['create_time'] = time();//创建时间		
	    $order_state = isset($_POST['order_state'])?$_POST['order_state']:'';
	    if ($id and $order_state){
	        $model_order = D('Order');
            $orderinfo = $model_order->find($id);
	        if($order_state == '10'){
				$data['order_state'] = 0;
			}else{
				$data['order_state'] = $order_state;
				//修改优惠券绑定---解除绑定
				if($order_state == "-1") {
					$data['cancel_time'] = time();//作废时间
					$data['membersalecoupon_id'] = 0;
					$model_membersalecoupon = D("Membersalecoupon");
					$map_membersale['order_id'] = $id;
					$map_membersale['is_use'] = 0;
					$model_membersalecoupon->where($map_membersale)->save(array('order_id'=>0));
					$order_log['content'] = "取消订单";
					$this->order_logModel->add($order_log);
				}
			}

	        if($order_state == 2){//预约完成
	            $data['complete_time'] = time();
				$order_log['content'] = "订单完成";
				$this->order_logModel->add($order_log);
	        }

	        $condition['id'] = $id;
	        $model_order->where($condition)->save($data);
			
		    $model_shop = D('Shop');
		    $shop_info = $model_shop->find($orderinfo['shop_id']);
    	    if($order_state == 1){//预约确认
				
			    $this->send_sms($orderinfo,$shop_info,$_POST['send_add_order_data']);
			    $data['status'] = 3;
			    $data['memo'] = '订单确认';
				
				//微信公众号 推送消费信息----------start
				$weixin_model = D('Paweixin');
				$padata_model = D('Padatatest');

				$weixin_map['uid'] = $orderinfo['uid'];
				$weixin_map['mobile'] = $orderinfo['mobile'];
				$weixin_map['type'] = 2;
				$weixin = $weixin_model->where($weixin_map)->find();

				if($weixin) {
					$padata_map['FromUserName'] = $weixin['wx_id'];
					$padata_map['type'] = 2;
					$FromUserName = $padata_model->where($padata_map)->find();

					$weixin_data['touser'] = $weixin['wx_id'];
					$weixin_data['title'] = "预约已确认通知";
					$weixin_data['description'] = $_POST['send_add_order_data'];
					$weixin_data['url'] = WEB_ROOT."Mobile-order_list-pa_id-{$FromUserName[id]}";
					
					$this->weixin_api($weixin_data);
				}
				//微信公众号 推送消费信息----------end
				
				$carmodel = $this->CarModel->where(array('model_id'=>$orderinfo['model_id']))->find();//车型 A4L 1.8MT
				$carseries = $this->CarseriesModel->where(array('series_id'=>$orderinfo['series_id']))->find();//车型 A4L 1.8MT
				//chf新增发送给4S店预约信息
				//@有脚本每天跑预约到店 zzc说注释掉 2013-11-28 
				if($shop_info['shop_mobile']){
					$model_service = D("Serviceitem");
					$service_map['id'] = array('in',$orderinfo['service_ids']);
					$services_list = $model_service->where($service_map)->select();
					if($services_list) {
						foreach($services_list as $k=>$v) {
							$services_str .= $v['name']."，";
						}
					}
					
					if ($orderinfo['workhours_sale']>0){
						$workhours_sale_str = ($orderinfo['workhours_sale']*10).'折';
					}elseif ($orderinfo['workhours_sale'] == '-1'){
						$workhours_sale_str = '全免';
					}else{
						$workhours_sale_str = '无折扣';
					}
					if ($orderinfo['product_sale']>0){
						$product_sale_str = ($orderinfo['product_sale']*10).'折';
					}else{
						$product_sale_str = '无折扣';
					}

					$send_shop = array(
					'phones'=>$shop_info['shop_mobile'],
					'content'=>"您好，车辆 ".$carseries['series_name'].$carmodel['model_name']."  车牌为".$orderinfo['licenseplate']." 通过携车网预定 用户于".date('Y-m-d,H:i:s',$orderinfo['order_time'])."进店进行".$services_str." 工时费".$workhours_sale_str."，配件费".$product_sale_str. "，请给予关照，谢谢",	
					);
				}
				curl_sms($send_shop);

				$model_sms = D('Sms');
				$now = time();
				$send_shop['sendtime'] = $now;
				$model_sms->add($send_shop);
				$order_log['content'] = "订单确认";
				$this->order_logModel->add($order_log);
			}
			if ($order_state == '-1' and $orderinfo['order_state'] == 1){
			    $this->send_sms_cancel($orderinfo,$shop_info);
			}
	        if ($order_state==2){
	            $this->add_point($orderinfo,$id);
	            $data['status'] = 4;
			    $data['memo'] = '订单完成';
	        }

	        if ($order_state == '-1'){//预约作废
	            $data['status'] = 5;
				$data['cancel_state'] = $_REQUEST['cancel_state'];
			    $data['memo'] = '订单作废';
	        }
			 if ($order_state == '6'){
	            $data['status'] = 6;
				$data['postpone_time'] = $_REQUEST['postpone_time'];
			    $data['memo'] = '订单推后';
				$order_log['content'] = "订单推后";
				$this->order_logModel->add($order_log);
	        }
	        $model_orderdeallog = D(GROUP_NAME.'/Orderdeallog');
            $data['order_id'] = $id;
            $data['createtime'] = time();
            $data['deal_uid'] = $_SESSION['authId'];
            $model_orderdeallog->add($data);

			if($orderinfo['is_pa'] == 1) {//平安
				$this->Post_Orderinfo($id,$order_state);
			}
			if($orderinfo['is_pa'] == 3){//百车宝
				$this->Post_hundredcar($order_state,$id);
				//这里调用Post_hundredcar给百车保发送订单数据
			}
	        echo 1;
	    }else{
	        echo -1;
		    exit; 
	    }
	}
	
	/*
		@author:chf
		@function:显示订单操作记录
		@time:2014-05-21
	*/
	function order_log(){
		$data['order_id'] = $_GET['order_id'];
		$order_log = $this->order_logModel->where($data)->select();
		$this->assign('order_log',$order_log);
		$this->display();
	}



	/*
		@author:chf
		@function:返回给百车宝数据
		@time:2014-05-15
	*/
	function Post_hundredcar($order_status,$order_id){
		$this->testModel = D('test');
		$this->HundredorderModel = D('hundredorder');
		$Hundredorder = $this->HundredorderModel->where(array('order_id'=>$order_id))->find();
		if($order_status == '1'){
			$status = '1';//已受理
		}elseif($order_status == '2'){
			$status = '3';//已完成
		}elseif($order_status == '-1'){
			$status = '0';//已取消
		}

		$data_car['boid'] = $Hundredorder['boid'];//订单号
		$data_car['status'] = $status;//状态
		/*测试数据start
		$data_car['boid'] = 20140416145626516;//订单号
		$data_car['status'] = 2;//状态
		测试数据start*/
		$strmd5 = $this->sign('pn13jlf32h26im5vwkods8srjh67uq9m',$data_car);

		$header = array(
        'appkey:lo175muov',
        'sign:'.$strmd5,
		);
		$pa_result = $this->Api_toHUN("http://www.baichebao.com/shop-manager/update_order",$data_car,$header);
		$this->testModel->add(array('imNo'=>$strmd5));
		return '1';
	}

	/*
		@author:chf
		@function:百车宝密码接口
		@time:2014-05-15
	*/
	function sign($secretKey,$params){
		ksort($params);
		$string = '';
		foreach ($params as $key => $val) {
			$string .= "{$key}={$val}";
		}
		$string .= $secretKey;
		return strtoupper(md5($string));
	}

	/*
		@author:chf
		@function:
		@time:2014-4-16
	*/
	function Post_Orderinfo($order_id,$order_state){
		//平安好车传送接口-------------------start
		$model_order = D('Order');
		$order = $model_order->where(array('id'=>$order_id))->find();	
		$car_model = D("Carmodel");
		$carmodel = $car_model->find($order['model_id']);

		$carseries_model = D("Carseries");
		$carseries = $carseries_model->find($carmodel['series_id']);

		$carbrand_model = D("Carbrand");
		$carbrand = $carbrand_model->find($carseries['brand_id']);

		$serviceitem_model = D("Serviceitem");
		$map_service['id'] = array('in',$order['service_ids']);
		$serviceitem = $serviceitem_model->where($map_service)->order("field (id,{$select_services})")->select();
		if($serviceitem) {
			$maintenanceItem = "";
		foreach($serviceitem as $key=>$val) {
			$maintenanceItem .= $val['name'].",";
		}
		$maintenanceItem = substr($maintenanceItem,0,-1);
		}
		//服务顾问信息
		if($order['servicemember_id']) {			
			$servicemember_model = D("Servicemember");
			$servicemember_info = $servicemember_model->find($data['servicemember_id']);
		}

		$shop_info = $this->ShopModel->find($order['shop_id']);

		//平安好车新的接口--参数规则--start 2014/4/15
		$data_pa['orderNum'] = $order['id'];//订单号---------必须
		$data_pa['orderChannel'] = "SH_03";//订单来源（合作方）
		$data_pa['carType']	 = $carmodel['model_name'];
		$data_pa['carTypeCode'] = $order['model_id'];
		$data_pa['carModel'] = $carseries['series_name'];
		$data_pa['carModelCode'] = $carseries['series_id'];
		$data_pa['brand'] = $carbrand['brand_name'];
		$data_pa['brandCode'] = $carbrand['brand_id']; 

		$data_pa['name'] = $order['truename']; 
		$data_pa['shopSign'] = $order['licenseplate'];

		$data_pa['alMileage'] = $order['mileage'];//公里数
		$data_pa['voitureNo'] = $order['car_sn'];//车辆识别码
	

		$orderState = $this->Get_orderstate($order['order_state']);
		$data_pa['orderState'] = $orderState;//订单状态----没做的 看xls
		
		
		$data_pa['serviceEndTime'] = date("Y-m-d H:i:s",$order['update_time']);//服务完成日期

		$data_pa['operatorType'] = 1;//0:修改预约---------必须
		//平安好车新的接口--参数规则--end 2014/4/15

		foreach($data_pa as $key=>$val) {
			$replace = array("<", ">", "(", ")", "\"", "'", "=");
			$data_pa[$key] = str_replace($replace, "", $val);
			$hkey .= $data_pa[$key];
		}
		$data_pa['hkey'] = md5($hkey);
		$pa_result = $this->Api_toPA("http://www.pahaoche.com/yangche/yuyue.w",$data_pa);
		$paresult_model = D("Paresult");
		$paresult_data['order_id'] = $order['id'];
		$paresult_data['result'] =  str_replace("\"","",$pa_result);
		$paresult_data['type'] =1;
		$paresult_data['create_time'] = time();
		$paresult_model->add($paresult_data);
		//平安好车传送接口-------------------end*/
	}

	/*
		@author:chf
		@function:和平安数据比对得到订单状态数据
		@time:2014-04-16
	*/
	function Get_orderstate($order_state){
		if($order_state == '0'){
			$orderState = '01';
		}elseif($order_state == '1'){
			$orderState = '02';
		}elseif($order_state == '2'){
			$orderState = '03';
		}
		return $orderState;
	}

	public function send_sms_cancel($orderinfo,$shop_info){
	    $order_time = date("Y-m-d H:i",$orderinfo['order_time']);
	    $order_id = $this->get_orderid($orderinfo['id']);
	    $send_add_order_data = array(
			'phones'=>$orderinfo['mobile'],
			'content'=>'尊敬的用户您好，您的订单'.$order_id.'号，预订的'.$order_time.'去'.$shop_info['shop_name'].'['.$shop_info['shop_address'].']的服务已取消，如有疑问请拨打'.C('CALL_400'),
		);
		//echo '<pre>';print_r($send_add_order_data);
		$return_data = curl_sms($send_add_order_data);

		$model_sms = D('Sms');
		$now = time();
		$send_add_order_data['sendtime'] = $now;
		$model_sms->add($send_add_order_data);

		//微信公众号 推送消费信息----------start
		$weixin_model = D('Paweixin');
		$padata_model = D('Padatatest');

		$weixin_map['uid'] = $orderinfo['uid'];
		$weixin_map['mobile'] = $orderinfo['mobile'];
		$weixin_map['type'] = 2;
		$weixin = $weixin_model->where($weixin_map)->find();

		if($weixin) {
			$padata_map['FromUserName'] = $weixin['wx_id'];
			$padata_map['type'] = 2;
			$FromUserName = $padata_model->where($padata_map)->find();

			$weixin_data['touser'] = $weixin['wx_id'];
			$weixin_data['title'] = "预约已取消通知";
			$weixin_data['description'] = $send_add_order_data['content'];
			$weixin_data['url'] = WEB_ROOT."Mobile-order_list-pa_id-{$FromUserName[id]}";
			
			$this->weixin_api($weixin_data);
		}
		//微信公众号 推送消费信息----------end
	}
	public function send_sms($orderinfo,$shop_info,$text=""){
	    
	    $order_time = date("Y-m-d H:i",$orderinfo['order_time']);
	    
	    $serviceitem_model = D('Serviceitem');
	    $map['id']  = array('in',$orderinfo['service_ids']);
		$getServiceitemArr = $serviceitem_model->where($map)->select();
		foreach($getServiceitemArr AS $k=>$v){
			$getServiceitem .= $getServiceitemArr[$k]['name'].',';
		}
		$getServiceitem = substr($getServiceitem,0,-1);
		$coupon_str1 = "";
		$coupon_str2 = "";
		if ($orderinfo['membercoupon_id']){
		    $model_membercoupon = D('Membercoupon');
    		$couponmap['xc_membercoupon.membercoupon_id'] = $orderinfo['membercoupon_id'];
    		$couponmap['xc_membercoupon.is_delete'] = 0;
    		$couponmap['xc_membercoupon.is_use'] = 1;
    		$couponmap['xc_coupon.is_delete'] = 0;
    		$couponinfo = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->find();
    		if ($couponinfo['coupon_discount']){
    		    $orderinfo['workhours_sale'] = $orderinfo['workhours_sale']-$orderinfo['coupon_save_discount'];
    		    if ($orderinfo['workhours_sale'] == 0){
    		        $orderinfo['workhours_sale'] = -1;
    		    }
    		    $coupon_str1 = "您此次预订使用了“".$couponinfo['coupon_name']."”优惠券，享有";
    		}elseif($couponinfo['coupon_amount']>0){
    		    $coupon_str2 = "您此次预订使用了“".$couponinfo['coupon_name']."”优惠券，工时费 折后再抵扣".$couponinfo['coupon_name']."元，";
    		}
		}
	    if ($orderinfo['workhours_sale']>0){
	        $workhours_sale_str = ($orderinfo['workhours_sale']*10).'折';
	    }elseif ($orderinfo['workhours_sale'] == '-1'){
	        $workhours_sale_str = '全免';
	    }else{
	        $workhours_sale_str = '无折扣';
	    }
	    if ($orderinfo['product_sale']>0){
	        $product_sale_str = ($orderinfo['product_sale']*10).'折';
	    }else{
	        $product_sale_str = '无折扣';
	    }
		
		$send_add_order_data = array(
			'phones'=>$orderinfo['mobile'],
			'content'=>$text,	
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
	
    public function check_coupon(){
	    $coupon_id = $_POST['coupon_id'];
	    $uid = $_POST['uid'];
	    $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
	    if ($uid and $coupon_id){
	        $map['xc_membercoupon.uid'] = $uid;
	        $map['xc_membercoupon.coupon_id'] = $coupon_id;
	        $map['xc_coupon.end_time'] = array('gt',time());
	        $map['xc_membercoupon.is_delete'] = 0;
	        $map['xc_membercoupon.is_use'] = 0;
	        //$membercoupon = $model_membercoupon->where($map)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("membercoupon_id ASC")->find();
	        //echo $model_membercoupon->getLastSql();exit;
	        if($membercoupon = $model_membercoupon->where($map)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("membercoupon_id ASC")->find()){
	            echo $membercoupon['membercoupon_id'];
	        }else{
	            echo 0;
	        }
	    }
	    exit;
	}
	
    public function get_new_order(){
        $model_order = D(GROUP_NAME.'/Order');
        $map['update_time'] = 0;
        $neworders = $model_order->where($map)->select();
        echo count($neworders);
        exit;
    }
    
    public function _save_orderdeallog($model){
        $model_orderdeallog = D(GROUP_NAME.'/Orderdeallog');
        $data['order_id'] = $_POST['id'];
        $data['createtime'] = time();
        $data['deal_uid'] = $_SESSION['authId'];
        $data['status'] = 2;
        $data['memo'] = '订单修改';
        $model_orderdeallog->add($data);
        
    }

    public function get_coupon_serverids($coupon_serverids,$select_serverids){
        $return_id = '';
        if ($select_serverids){
            $select_serverids_arr = explode(',',$select_serverids);
            if ($coupon_serverids){
                $coupon_serverids_arr = explode(',',$coupon_serverids);
                foreach ($coupon_serverids_arr as $k=>$v){
                    if (in_array($v,$select_serverids_arr)){
                        $return_id = $v.',';
                    }
                }
                $return_id = substr($return_id,0,-1);
            }
        }
        return $return_id;
    }
    
    public function get_not_coupon_serverids($coupon_serverids,$select_serverids){
        $return_id = '';
        if ($coupon_serverids){
            $coupon_serverids_arr = explode(',',$coupon_serverids);
            if ($select_serverids){
                $select_serverids_arr = explode(',',$select_serverids);
                foreach ($select_serverids_arr as $k=>$v){
                    if (!in_array($v,$coupon_serverids_arr)){
                        $return_id = $v.',';
                    }
                }
                $return_id = substr($return_id,0,-1);
            }
        }
        return $return_id;
    }
    
    public function delete_order(){
        $id = isset($_POST['id'])?$_POST['id']:0;
        $model_order = D(GROUP_NAME.'/Order');
        $model_orderdelete = D(GROUP_NAME.'/Orderdelete');
        if ($id){
            $order = $model_order->find($id);
            if ($model_orderdelete->add($order)){
                $map_order['id'] = $id;
                $model_order->where($map_order)->delete();
                echo 1;exit;
            }
        }
    }

	function get_order90() {
		$model_order = D(GROUP_NAME.'/Order');
		$sql = "SELECT distinct(uid) FROM xc_order WHERE `order_des`!='membernew' AND order_state=2 AND uid NOT IN( SELECT distinct(uid) FROM xc_order WHERE create_time>UNIX_TIMESTAMP()-(90*86400*2) AND `order_des`!='membernew' AND order_state=2 )";
		$order_uid = $model_order->query($sql);
		foreach($order_uid as $key=>$val) {
			$array_uid[] = $val['uid'];
		}
		$model_member = D("Member");
		$MembersalecouponModel = D('membersalecoupon');

		$map['uid'] = array('in',$array_uid);
		$result = $model_member->where($map)->order("uid ASC")->select();

		$member_from = C('MEMBER_FORM');
		$str = "<table><tr><td>编号</td><td>用户名</td><td>电话</td><td>抵用券</td><td>来源</td><td>邮箱</td><td>注册时间</td><td>状态</td></tr>";
		foreach($result as $key=>$val) {
			$val[fromstatus] = $member_from[$val[fromstatus]];
			$val[reg_time] = date("Y-m-d H:i:s",$val[reg_time]);

			$Membersalecoupon = $MembersalecouponModel->where(array('uid'=>$val['uid']))->find();
			if($Membersalecoupon){
				$val[salecoupon] = "已领";
			}else{
				$val[salecoupon] = "<a href='__APP__/Store/salecoupon/add_membersalecoupon/coupon_id/1/mobile/".$vv['mobile']."' target='_blank'>点击领取</a>";
			}

			$str.= "<tr>
							<td><a href='http://www.xieche.com.cn/Admin/index.php/Store/member/read/id/{$val[uid]}' target='_blank'>{$val[uid]}</a></td>
							<td>{$val[username]}</td>
							<td>{$val[mobile]}</td>
							<td>{$val[salecoupon]}</td>
							<td>{$val[fromstatus]}</td>
							<td>{$val[email]}</td>
							<td>{$val[reg_time]}</td>
							<td>{$val[status]}</td>
						</tr>";
		}

		$str .="</table>";
			echo $str;

	}
}
?>