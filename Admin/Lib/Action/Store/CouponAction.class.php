<?php
class CouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->CouponModel = D('coupon');//优惠卷表
		$this->CarbrandModel = D('carbrand');//品牌表
		$this->ShopModel = D('shop');//店铺表
	}
	
    public function index(){
		
        Cookie::set('_currentUrl_', __SELF__);
        $model_coupon = D(GROUP_NAME.'/Coupon');
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $model_serviceitem = D(GROUP_NAME.'/Serviceitem');
		if($_REQUEST['coupon_name']){
			$map['coupon_name'] = array('LIKE','%'.$_REQUEST['coupon_name'].'%');
			$data['coupon_name'] = $_REQUEST['coupon_name'];
		}
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['coupon_across']=='1'){
			$data['coupon_across'] = $map['coupon_across'] = $_REQUEST['coupon_across'];
		}
		if($_REQUEST['coupon_across']=='2'){
			$data['coupon_across'] = $map['coupon_across'] = array('neq',1);
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['_string'] =  "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_id)";
		}
        // 计算总数
        $count = $model_coupon->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);

		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}

        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_coupon->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
        if (!empty($list)){
            foreach ($list as $k=>$v){
                $map['coupon_id'] = $v['id'];
                $list[$k]['coupon_number'] = $model_membercoupon->where($map)->count();
                if ($v['service_ids']){
                    $list[$k]['service_info'] = $model_serviceitem->get_servicename($v['service_ids']);
                }
            }
        }
        // 赋值赋值
		$data['shop_list'] = $this->ShopModel->where(array('shop_city'=>'3306'))->select();
        $this->assign('page', $page);
		$this->assign('data', $data);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();
    }
    public function memberlist(){
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $model_member = D(GROUP_NAME.'/Member');
        $coupon_id = isset($_GET['id'])?$_GET['id']:0;
        if ($coupon_id){
            $map['coupon_id'] = $coupon_id;
            $membercoupon_info = $model_membercoupon->where($map)->select();
            if ($membercoupon_info){
                foreach ($membercoupon_info as $k=>$v){
                    $memberinfo = $model_member->find($v['uid']);
                    $membercoupon_info[$k]['memberinfo'] = $memberinfo;
                }
            }
        }
        $this->assign('memberlist',$membercoupon_info);
        $this->display();
    }
    public function _before_add(){
        //载入服务项目MODEL
		$model_serviceitem = D(GROUP_NAME.'/serviceitem');
		//获取所有的服务分类
		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$this->assign('list_si_level_0',$list_si_level_0);
    	$this->assign('list_si_level_1',$list_si_level_1);
    }
    public function edit(){
        //载入服务项目MODEL
		$model_serviceitem = D(GROUP_NAME.'/serviceitem');
		//获取所有的服务分类
		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$this->assign('list_si_level_0',$list_si_level_0);
    	$this->assign('list_si_level_1',$list_si_level_1);
    	$model_shop = D(GROUP_NAME.'/Shop');
		$shops = $model_shop->where("status=1")->select();
		$this->assign('shops',$shops);
		$model_coupon = D(GROUP_NAME.'/Coupon');
		$id = $_REQUEST['id'];
		$vo = $model_coupon->find($id);
        if ($vo['s_time']){
		    $s_time = explode(":",$vo['s_time']);
		    $vo['s_hour'] = $s_time[0];
		    $vo['s_minute'] = $s_time[1];
		}
	    if ($vo['e_time']){
		    $e_time = explode(":",$vo['e_time']);
		    $vo['e_hour'] = $e_time[0];
		    $vo['e_minute'] = $e_time[1];
		}
		$data['brand_id'] = explode(',',$vo['brand_id']);
		$data['model_id'] = explode(',',$vo['model_id']);
		$data['series_id'] = explode(',',$vo['series_id']);
		
		for($a=0;$a<=30;$a++){
			if(!$data['brand_id'][$a]){
				
				$data['brand_id'][$a] = 0;
			}
			if(!$data['model_id'][$a]){
				
				$data['model_id'][$a] = 0;
			}
			if(!$data['series_id'][$a]){
				
				$data['series_id'][$a] = 0;
			}
		}


		$model_shop = D(GROUP_NAME.'/Shop');
		$map_shops['id'] = array('in',$vo['shop_id']);
		$shops = $model_shop->where($map_shops)->select();
		$this->assign('shops',$shops);
		
		$this->assign('vo', $vo);
		$this->assign('data', $data);

		$this->display();
    }
    
     public function _before_insert(){

		
        if (isset($_POST['show_s_time']) and $_POST['show_s_time']){
            $_POST['show_s_time'] = strtotime($_POST['show_s_time']);
        }
        if (isset($_POST['show_e_time']) and $_POST['show_e_time']){
            $_POST['show_e_time'] = strtotime($_POST['show_e_time'].' 23:59:59');
        }
        if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
        if (!empty($_POST['select_services'])){
            $_POST['service_ids'] = implode(',',$_POST['select_services']);
        }
        $_POST['s_time']=$_POST['s_hours'].':'.$_POST['s_minute'];
		$_POST['e_time']=$_POST['e_hours'].':'.$_POST['e_minute'];
		if (!$_POST['model_id']){
		    $this->error("请选择车型");
		    //$model_series = D(GROUP_NAME.'/Carseries');
		    //$map_s['series_id'] = $_POST['series_id'];
		    //$series = $model_series->where($map_s)->find();
		    //$_POST['fsid'] = $series['fsid'];
		}
		foreach($_POST['shop_id'] as $v){
				$shop_ids.= $v.",";
		}
		$_POST['shop_id'] = substr($shop_ids,0,-1);

		foreach($_POST['brand_id'] as $v){
			if($v != 0) {
				$brand_id.= $v.",";
			}
		}
		$_POST['brand_id'] = substr($brand_id,0,-1);

		foreach($_POST['model_id'] as $v){
				$model_id.= $v.",";
		}
		$_POST['model_id'] = substr($model_id,0,-1);
		
		
		foreach($_POST['series_id'] as $v){
			$series_id.= $v.",";
		}
		$_POST['series_id'] = substr($series_id,0,-1);
		
		//print_r($_POST['series_id']);
        //echo '<pre>';print_r($_FILES);exit;
        if ($_FILES['coupon_pic']['name'] || $_FILES['coupon_logo']['name']){
            $this->couponupload();
        }

		$model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
		$map_fs['shopid'] = array('in',$_POST['shop_id']);
		$fs_relation = $model_shop_fs_relation->where($map_fs)->select();
		if($fs_relation) {
			foreach($fs_relation as $key=>$val) {
				$coupon_fs .= $val['fsid'].",";
			}
			$_POST['fsid'] = substr($coupon_fs,0,-1);
		}
		
		$series_model = D(GROUP_NAME.'/Carseries');
		$map_series = array();
		$map_series['series_id'] = array('in',$_POST['series_id']);
		$series = $series_model->where($map_series)->select();
		foreach($series as $sk=>$sv) {
			$fsid_series[] = $sv['fsid']; 
		}
		$fsid_series = array_unique($fsid_series);
		$_POST['fsid_across'] = implode(",",$fsid_series);

		    //$_POST['fsid'] = $series['fsid'];
       //echo '<pre>';print_r($_POST);exit;
    }

    public function _before_update(){

		if (isset($_POST['show_s_time']) and $_POST['show_s_time']){
            $_POST['show_s_time'] = strtotime($_POST['show_s_time']);
        }
        if (isset($_POST['show_e_time']) and $_POST['show_e_time']){
            $_POST['show_e_time'] = strtotime($_POST['show_e_time'].' 23:59:59');
        }
        if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
        if (!empty($_POST['select_services'])){
            $_POST['service_ids'] = implode(',',$_POST['select_services']);
        }
        $_POST['s_time']=$_POST['s_hours'].':'.$_POST['s_minute'];
		$_POST['e_time']=$_POST['e_hours'].':'.$_POST['e_minute'];
		if (!$_POST['model_id']){
		    $this->error("请选择车型");
		}
        //echo '<pre>';print_r($_FILES);exit;
        if ($_FILES['coupon_pic']['name'] || $_FILES['coupon_logo']['name']){
            $this->couponupload();
        }

         foreach($_POST['shop_id'] as $v){
				$shop_ids.= $v.",";
		}
		$_POST['shop_id'] = substr($shop_ids,0,-1);

		foreach($_POST['brand_id'] as $v){
			if($v != 0) {
				$brand_id.= $v.",";
			}
		}
		$_POST['brand_id'] = substr($brand_id,0,-1);

		foreach($_POST['model_id'] as $v){
				$model_id.= $v.",";
		}
		$_POST['model_id'] = substr($model_id,0,-1);
		
		
		foreach($_POST['series_id'] as $v){
			$series_id.= $v.",";
		}
		$_POST['series_id'] = substr($series_id,0,-1);

		$model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
		$map_fs['shopid'] = array('in',$_POST['shop_id']);
		$fs_relation = $model_shop_fs_relation->where($map_fs)->select();
		if($fs_relation) {
			foreach($fs_relation as $key=>$val) {
				$coupon_fs .= $val['fsid'].",";
			}
			$_POST['fsid'] = substr($coupon_fs,0,-1);
		}
		
		$series_model = D(GROUP_NAME.'/Carseries');
		$map_series = array();
		$map_series['series_id'] = array('in',$_POST['series_id']);
		$series = $series_model->where($map_series)->select();
		foreach($series as $sk=>$sv) {
			$fsid_series[] = $sv['fsid']; 
		}
		$fsid_series = array_unique($fsid_series);
		$_POST['fsid_across'] = implode(",",$fsid_series);
        //$this->upload('coupon_logo');
        //echo '<pre>';print_r($_POST);exit;
    }
	
	/*
		author:chf
		function:修改优惠券信息(规则如果修改现价或者团购价金额讲废除此优惠卷并自动添加新优惠券)
		time:2013-9-1
	
	*/
	function update_rule(){
		$this->Coupon_Model = M('coupon');
		$this->Membercoupon_Model = M('membercoupon');
		$arr['id'] = $_POST['coupon_id'];
		$coupon = $this->Coupon_Model->where($arr)->find();
		if (isset($_POST['show_s_time']) and $_POST['show_s_time']){
            $data['show_s_time'] = strtotime($_POST['show_s_time']);//购买开始时间
        }
        if (isset($_POST['show_e_time']) and $_POST['show_e_time']){
            $data['show_e_time'] = strtotime($_POST['show_e_time'].' 23:59:59');//购买结束时间
        }
        if (isset($_POST['start_time']) and $_POST['start_time']){
            $data['start_time'] = strtotime($_POST['start_time']);//开始时间
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $data['end_time'] = strtotime($_POST['end_time'].' 23:59:59');//结束时间
        }
        if (!empty($_POST['select_services'])){
            $data['service_ids'] = implode(',',$_POST['select_services']);//服务项目
        }
        $data['s_time']=$_POST['s_hours'].':'.$_POST['s_minute'];//每天开始时间
		$data['e_time']=$_POST['e_hours'].':'.$_POST['e_minute'];//每天结束时间
		if (!$_POST['model_id']){
		    $this->error("请选择车型");
		}
        //echo '<pre>';print_r($_FILES);exit;
        if ($_FILES['coupon_pic']['name'] || $_FILES['coupon_logo']['name']){
			
            $this->couponupload();
        }
		if($_FILES['coupon_pic']['name']){
			$data['coupon_pic'] = $_POST['coupon_pic'];//图片
		}
		if($_FILES['coupon_logo']['name']){
			$data['coupon_logo'] = $_POST['coupon_logo'];//LOGO图片
		}
        foreach($_POST['shop_id'] as $v){
				$shop_ids.= $v.",";
		}
		$_POST['shop_id'] = substr($shop_ids,0,-1);

		foreach($_POST['brand_id'] as $v){
			if($v != 0) {
				$brand_id.= $v.",";
			}
		}
		$_POST['brand_id'] = substr($brand_id,0,-1);

		foreach($_POST['series_id'] as $v){
			$series_id.= $v.",";
		}
		$_POST['series_id'] = substr($series_id,0,-1);
		
		foreach($_POST['model_id'] as $v){
				$model_id.= $v.",";
		}
		$_POST['model_id'] = substr($model_id,0,-1);

		$model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
		$map_fs['shopid'] = $_POST['shop_id'];
		$fs_relation = $model_shop_fs_relation->where($map_fs)->select();
		if($fs_relation) {
			foreach($fs_relation as $key=>$val) {
				$coupon_fs .= $val['fsid'].",";
			}
			$data['fsid'] = substr($coupon_fs,0,-1);//品牌对应FSID
		}

		$series_model = D(GROUP_NAME.'/Carseries');
		$map_series = array();
		$map_series['series_id'] = array('in',$_POST['series_id']);
		$series = $series_model->where($map_series)->select();
		foreach($series as $sk=>$sv) {
			$fsid_series[] = $sv['fsid']; 
		}
		$fsid_series = array_unique($fsid_series);
		$_POST['fsid_across'] = implode(",",$fsid_series);

		$data['coupon_name'] = $_POST['coupon_name'];//标题
		$data['brand_id'] = $_POST['brand_id'];
		$data['series_id'] = $_POST['series_id'];
		$data['model_id'] = $_POST['model_id'];
		$data['coupon_type'] = $_POST['coupon_type'];//优惠卷类型
		$data['coupon_across'] = $_POST['coupon_across'];
		$data['week'] = $_POST['week'];//星期
		$data['coupon_discount'] = $_POST['coupon_discount'];//工时折扣率
		$data['coupon_productsale'] = $_POST['coupon_productsale'];//工时折扣率

		$data['cost_price'] = $_POST['cost_price'];//原价
		$data['coupon_amount'] = $_POST['coupon_amount'];//优惠券金额
		
		$data['jiesuan_money'] = $_POST['jiesuan_money'];//结算金额
		$data['pay_count'] = $_POST['pay_count'];//结算金额
		$data['coupon_url'] = $_POST['coupon_url'];//活动地址
		$data['coupon_summary'] = $_POST['coupon_summary'];//优惠券简介
		$data['coupon_des'] = $_POST['coupon_des'];//优惠券详情
		$data['shop_id'] = $_POST['shop_id'];//店铺ID
		$data['is_delete'] = $_POST['is_delete'];//状态
		
		$pay_count = $this->Coupon_Model->where(array('id'=>$arr['id']))->find();
		

		//if($coupon['cost_price'] == $data['cost_price'] && $coupon['coupon_amount'] == $data['coupon_amount'] && $pay_count==0){
		//2B逻辑
		/*if( $pay_count['is_delete']=='1'){
			
			$this->Coupon_Model->where(array('id'=>$arr['id']))->save($data);
			//echo $this->Coupon_Model->getlastSql();exit;
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success("更新成功");
		}else{
			$data['coupon_name'] = $_POST['coupon_name'];
			$data['is_delete'] = 0;
			$this->Coupon_Model->where(array('id'=>$arr['id']))->save(array('is_delete'=>1));
			$this->Coupon_Model->add($data);
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			
			$this->success("修改成功");
		//}*/


			/*如果改店铺团购或者优惠券数量为0把SHOP里have_coupon改为0
			$have_coupon = $this->Coupon_Model->where(array('shop_id'=>$pay_count['shop_id'],'is_delete'=>'0'))->count();	
			
			if($have_coupon=='0'){
				$this->ShopModel->where(array('id'=>$pay_count['shop_id']))->save(array('have_coupon'=>'0'));	
			}*/

			$this->Coupon_Model->where(array('id'=>$arr['id']))->save($data);
			//echo $this->Coupon_Model->getlastSql();exit;
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success("更新成功");
	}
    
    public function del(){
        $map['id'] = $_POST['id'];
        $data['update_time'] = time();
        $data['is_delete'] = 1;
        $model_coupon = D(GROUP_NAME.'/Coupon');
        if($model_coupon->where($map)->save($data)){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }
    public function send(){
        $data['uid'] = $_POST['uid'];
        $data['coupon_id'] = $_POST['id'];
        $data['create_time'] = time();
        $model_coupon = D(GROUP_NAME.'/Membercoupon');
        if($model_coupon->add($data)){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }
    



    public function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Coupon/Logo/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'coupon1_,coupon2_';//coupon1_网站图片显示；coupon2_手机APP图片显示
        //$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('299,99','225,75');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }


	/*
		@author:chf
		@function:发送优惠卷ID
		@time:2013-04-12
	*/
	function AjaxSendsms(){
		$MembercouponModel = D(GROUP_NAME.'/Membercoupon');
		$ShopModel = M('shop');
		$coupon_code = $_REQUEST['coupon_code'];
		$coupon_code = $_REQUEST['mobile'];
		$map['membercoupon_id'] = $_REQUEST['membercoupon_id'];
		$membercoupon_info = $MembercouponModel->where($map)->find();
		$Coupon = $this->CouponModel->where(array('id'=>$membercoupon_info['coupon_id']))->find();
		$shop_info = $ShopModel->where(array('id'=>$membercoupon_info['shop_id']))->find();
		if($membercoupon_info['coupon_type']==1){
			$coupon_type_str = "现金券编号：";
		}
		if($membercoupon_info['coupon_type']==2){
			$coupon_type_str = "团购券编号：";
		}
		if($membercoupon_info['start_time']){
			$start_time = date('Y-m-d h:i:s',$membercoupon_info['start_time']);
		}
		if($membercoupon_info['end_time']){
			$end_time = date('Y-m-d h:i:s',$membercoupon_info['end_time']);
		}
		$verify_str = "您的".$coupon_type_str.$membercoupon_info['membercoupon_id']."(".$membercoupon_info['coupon_name']."金额:".$Coupon['coupon_amount'].")请凭消费码至商户(".$shop_info['shop_name'].",".$shop_info['shop_address'].")处在有效期(".$start_time."至".$end_time.")优惠卷编码:".$membercoupon_info['coupon_code'];
		$send_verify = array('phones'=>$membercoupon_info['mobile'],
    	'content'=>$verify_str,
    	);
    	$return_data = $this->curl_sms($send_verify);
		echo "发送成功！";
	}
	/*
		@author:chf
		@function:复制优惠卷信息
		@time:2013-05-16
	
	*/
	 public function copycoupon(){
        $coupon_id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
        if ($coupon_id) {
        	$model_coupon = D(GROUP_NAME.'/Coupon');
        	$map_c['id'] = $coupon_id;
        	$coupon = $model_coupon->where($map_c)->find();
        	unset($coupon['id']);
			$coupon['is_delete'] = 1;
        	if ($model_coupon->add($coupon)){
        	    echo 1;
        	}else{
        	    echo 0;
        	}
        }else{
            echo 0;
        }
        exit;
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

	/*
		@author:ysh
		@function:清除前台memcache缓存
		@time:2013/8/8
	*/
	function clean_memcache() {
		$model_coupon = D(GROUP_NAME.'/Coupon');

		$coupon_info = $model_coupon->where(array('is_delete'=>0))->field('id')->select();
		if($coupon_info) {
			foreach($coupon_info as $key=>$val) {
				S('coupon'.$val['id'],NULL);
				S('other_coupon'.$val['id'],NULL);
				S('Seriesinfo'.$val['id'],NULL);
			}
		}

		S('coupon_sql_default' , NULL);
		S('app_coupon_sql_default' ,NULL);
		$this->success("缓存清除");
		
	}

	/*
		@author:wwy
		@function:优惠券续约
		@time:2014/3/31
	*/
	function contract() {
		Cookie::set('_currentUrl_', __SELF__);
	    $model_shop = D(GROUP_NAME.'/Shop');
        $model_coupon = D(GROUP_NAME.'/Coupon');
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $model_serviceitem = D(GROUP_NAME.'/Serviceitem');

		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
			//$this->assign('coupon_type',$coupon_type);
		}

		$model = D(GROUP_NAME . "/" . $this->getActionName());
		$now=time();
		$reference_time=$now+5184000;
		$map['end_time']=array('lt',$reference_time);
		$map['is_delete']= 0;

		/**导出数据start**/
        $execl = $_REQUEST['execl'];
		if($execl){
			//导出数据处理
			$list_execl = $model_coupon->where($map)->order('end_time asc')->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			if (!empty($list_execl)){
				foreach ($list_execl as $k=>$v){
					$map['coupon_id'] = $v['id'];
					$list_execl[$k]['coupon_number'] = $model_membercoupon->where($map)->count();
					if ($v['service_ids']){
						$list_execl[$k]['service_info'] = $model_serviceitem->get_servicename($v['service_ids']);
					}
					$mad['id']=$v['shop_id'];
					$shop_name= $model_shop->where($mad)->field('shop_name')->find();
					$list_execl[$k]['shop_name']= $shop_name['shop_name'];
					$last_date=floor(($v['end_time']-$now)/86400);
					$list_execl[$k]['last_date']="距续约还有".$last_date."天";
					$list_execl[$k]['start_time']=date('Y-m-d',$v['start_time']);
					$list_execl[$k]['end_time']=date('Y-m-d',$v['end_time']);
					$list_execl[$k]['new_name']=$list_execl[$k]['service_info']['0']['name'].'</br>'.$list_execl[$k]['service_info']['1']['name'];
				}
			}
			$this->export_execl($list_execl,'续约.xls');
		}
		/**导出数据end**/

        // 计算总数
        $count = $model_coupon->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_coupon->where($map)->order('end_time asc')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_coupon->getlastSql();
		
        if (!empty($list)){
            foreach ($list as $k=>$v){
                $map['coupon_id'] = $v['id'];
                $list[$k]['coupon_number'] = $model_membercoupon->where($map)->count();
                if ($v['service_ids']){
                    $list[$k]['service_info'] = $model_serviceitem->get_servicename($v['service_ids']);
                }
				$mad['id']=$v['shop_id'];
				$shop_name= $model_shop->where($mad)->field('shop_name')->find();
				$list[$k]['shop_name']= $shop_name['shop_name'];
				$last_date=floor(($v['end_time']-$now)/86400);
				$list[$k]['last_date']="距续约还有".$last_date."天";
            }
        }
		//echo '<pre>';print_r($list);
        // 赋值赋值
		$data['shop_list'] = $this->ShopModel->where(array('shop_city'=>'3306'))->select();
        $this->assign('page', $page);
		$this->assign('data', $data);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();

	}

	function export_execl($list,$filename){
		if($list){
			foreach($list as $key=>$val){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table .= '<tr bgcolor='.$color.'><td>'.$val['id'].'</td><td>'.$val['coupon_name'].'</td><td>'.$val['shop_name'].'</td><td>'.$val['start_time'].'</td><td>'.$val['end_time'].'</td><td>'.$val['coupon_discount'].'</td><td>'.$val['coupon_amount'].'</td><td>'.$val['new_name'].'</td><td>'.$val['last_date'].'</td>';
			}
		}
		$color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename='{$filename}'");
        $str = '<table><tr bgcolor='.$color.'><td>优惠券ID</td><td>优惠券名称</td><td> 4S店</td><td>开始时间</td><td>结束时间</td><td>优惠券折扣</td><td>优惠券金额</td><td>服务名称</td><td>签约状态</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

    
}
?>