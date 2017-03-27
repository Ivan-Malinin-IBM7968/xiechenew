<?php
//文章
class PucommentAction extends CommonAction {
	
	public function __construct() {
		parent::__construct();
		$this->CouponModel = D('coupon');
		$this->ShopModel = D('shop');
		$this->CarbrandModel = D('carbrand');
		$this->RegionModel = D('region');//区域
		
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->CommentModel = D('comment');//评论表
		define('PA_BANNER', 'pacar');
	}
	
	/*
		@author:chf
		@function:显示平安IFRAME我的搜索页
		@time:2014-04-22
	*/
	function index(){
		//现金券coupon_type=1
		$data_Pcoupon = $this->return_couponlist('Pcoupon','1');
		$data_Gcoupon = $this->return_couponlist('Gcoupon','2');
		$this->assign('data_Pcoupon',$data_Pcoupon);
		$this->assign('data_Gcoupon',$data_Gcoupon);


		$data_AllPcoupon = $this->return_Allcouponlist('1');
		$data_AllGcoupon = $this->return_Allcouponlist('2');
		$this->assign('data_AllPcoupon',$data_AllPcoupon);
		$this->assign('data_AllGcoupon',$data_AllGcoupon);


	
	//	$coupon_map['id'] = array('in',$Couponarr);
		$coupon_map['show_s_time'] = array('lt',time());
		$coupon_map['show_e_time'] = array('gt',time());
		$coupon = $this->CouponModel->where($coupon_map)->order('rand()')->limit('6')->select();
		if($coupon){
			foreach($coupon as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$coupon[$k]['shop_address'] = $shop['shop_address'];
			}
		}
		$this->assign('coupon',$coupon);


		//滚动4S店列表
		$model_shop_fs_relation = D('Shop_fs_relation');

		if($this->city_name == "上海" ) {
			$map_sh['status'] = 1;
			$map_sh['shop_class'] = 1;
		}
		$map_sh['shop_city'] = $this->city_id;
		$shoplist = $this->ShopModel->where($map_sh)->order("comment_rate DESC")->limit(6)->select();
		$shop_count = $this->ShopModel->where($map_sh)->count();
		$this->assign('shop_count',$shop_count);
		if ($shoplist){
			foreach ($shoplist as $kk=>$vv){
				$shoplist[$kk]['comment_number'] = $this->CommentModel->where(array('shop_id'=>$vv['id']))->count();
				if (file_exists("UPLOADS/Shop/280/".$vv['id'].".jpg")){
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Shop/280/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Brand/280/".$shop_fs_relation['fsid'].".jpg";
				}
				$Timesale = $this->TimesaleModel->where(array('shop_id'=>$vv['id'],'status'=>1))->find();
				$Timesaleversion = $this->TimesaleversionModel->where(array('timesale_id'=>$Timesale['id'],'status'=>1))->find();
				$shoplist[$kk]['workhours_sale'] = $Timesaleversion['workhours_sale']*10;
			}
		}
		
		$this->assign('shoplist',$shoplist);
		$this->display();
	}
	



	/*
		@author:chf
		@function:查找相关品牌相关优惠卷团购和优惠全部
		@time:2013-09-08
	*/
	function return_Allcouponlist($coupon_type){
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = $coupon_type;
		

		$coupon_arr['coupon_type'] =  $coupon_type;
		$coupon_arr['start_time'] = array('lt',time());
		$coupon_arr['end_time'] =  array('gt',time());
		$coupon_arr['is_delete'] =  '0';
		$coupontype = $this->CouponModel->where($coupon_arr)->order('rand()')->select();
		foreach($coupontype as $k=>$v){
			$TUIJIAN_COUPON.=$v['id'].",";
			
		}
		

		$tuijian_coupon_ids = substr($TUIJIAN_COUPON,0,-1);
		
		
		$Coupon_map['id'] = array('in',explode(',',$tuijian_coupon_ids));

		$data = $this->CouponModel->where($Coupon_map)->field('coupon_des',true)->order("id DESC")->limit(6)->select();
		
		foreach($data as $C_k=>$C_v){
				$shop = $this->ShopModel->where(array('id'=>$C_v['shop_id']))->find();
				$data[$C_k]['shop_name'] = $shop['shop_name'];
				$data[$C_k]['shop_address'] = $shop['shop_address'];
		}
		return $data;
	}



	/*
		@author:chf
		@function:查找相关品牌相关优惠卷(如改品牌没满6个加入推荐现金券或团购券)
		@time:2013-09-08
	*/
	function return_couponlist($arrayname,$coupon_type){
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = $coupon_type;
		$brand_list = $this->CouponModel->where($Coupon_map)->field('coupon_des',true)->group('brand_id')->order("brand_id ASC")->select();

		foreach($brand_list as $key=>$val) {
			$brand_id_str .= $val['brand_id'].",";
		}
		$brand_id_str = substr($brand_id_str,0,-1);
		$brand_id_arr = explode(",",$brand_id_str);
		$brand_id_arr = array_unique($brand_id_arr);

		

		foreach($brand_id_arr as $C_k=>$C_v){

			$Carbrand = $this->CarbrandModel->where(array('brand_id'=>$C_v))->find();

			$data['Coupon'][$C_k]['brand_id'] = $Carbrand['brand_id'];
			$data['Coupon'][$C_k]['brand_name'] = $Carbrand['brand_name'];
			$Pricecoupon['is_delete'] = 0;
			$Pricecoupon['show_s_time'] = array('lt',time());
			$Pricecoupon['show_e_time'] = array('gt',time());
			$Pricecoupon['coupon_type'] = $coupon_type;
			$Pricecoupon['_string'] =  "FIND_IN_SET('{$C_v}', brand_id)";
			$data['Coupon'][$C_k][$arrayname] = $this->CouponModel->where($Pricecoupon)->field('coupon_des',true)->order("id DESC")->limit('0,6')->select();

			foreach($data['Coupon'][$C_k][$arrayname] as $k=>$v){
				$coupon_id[] = $v['id'];
			}

			$count = count($data['Coupon'][$C_k][$arrayname]);
			$need_tuijian_count = 6-$count;
			if($need_tuijian_count > 0 ) {
				unset($Pricecoupon['_string'] );
				if( $coupon_type == 1 ) {
					
					$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
				}else {
					$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
					//$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
				}
				$Pricecoupon['id'] = array('in',implode(',',$tuijian_coupon_ids));
				
				$Pricecoupon['id'] = array('not in',$coupon_id);
				$tuijian_coupons = $this->CouponModel->where($Pricecoupon)->field('coupon_des',true)->order("id DESC")->limit($need_tuijian_count)->select();
				if($_SESSION['username'] == 'z'){
					//echo $this->CouponModel->getlastSql();
				}
				unset($coupon_id);unset($Pricecoupon);
				foreach($tuijian_coupons as $tuijian_key=>$tuijian_val) {
					$tuijian_val['is_tuijian'] = 1;
					$data['Coupon'][$C_k][$arrayname][] = $tuijian_val;

				}

			}

			foreach($data['Coupon'][$C_k][$arrayname] as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Coupon'][$C_k][$arrayname][$k]['shop_name'] = $shop['shop_name'];
				$data['Coupon'][$C_k][$arrayname][$k]['shop_address'] = $shop['shop_address'];
			}
		}
		return $data;
	}


	/*
		@author:chf
		@function:通过type得到对应信息 type = 1 城市区域
		@time 2013-05-02
	*/
	function AjaxInfo(){
		
		$type = $_REQUEST['type'];
		if($type == 1){
			$map['parent_id'] = $this->city_id;
			$map['region_type'] = 3;
			$map['status'] = 1;
			$data = $this->RegionModel->where($map)->select();
			echo json_encode($data);
		}
		elseif($type == 2){
			$map['shop_city'] = $this->city_id;
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%"); 
			$map['status'] = 1;
			$data = $this->ShopModel->where($map)->order("id DESC")->limit(7)->select();
			echo json_encode($data);
		}
	
	}


	/*
		@author:chf
		@function:得到所有车品牌
		@time:2014-04-10
	*/
	function PostCar_brand(){
		$model_carbrand = D('Carbrand');
		
        $brand = $model_carbrand->select();
		foreach($brand as $k=>$v){
			
		}
		echo json_encode($brand);
	}

	/*
		@author:chf
		@function:根据品牌ID取值对应的车系内容
		@time:2014-04-10
	*/
    public function PostCar_series(){
        if($_REQUEST['brand_id']){
            $map_s['brand_id'] = $_REQUEST['brand_id'];
        }
        $model_carseries = D('Carseries');
        $carseries = $model_carseries->where($map_s)->select();
		//$c[0]['series_id'] = "0";
		//$c[0]['series_name'] = "请选车车系";
		//$arr = array_merge($c,$carseries);
        echo json_encode($carseries);exit;
    }

	/*
		@author:chf
		@function:根据车系ID取值对应的车型内容
		@time:2014-04-10
	*/
    public function PostCar_model(){
        if($_REQUEST['series_id']){
            $map_m['series_id'] = $_REQUEST['series_id'];
        }
        $model_carmodel = D('Carmodel');
        $carmodel = $model_carmodel->where($map_m)->select();
		//$c[0]['model_id'] = "0";
		//$c[0]['model_name'] = "请选择车型";
		//$arr = array_merge($c,$carmodel);
        echo json_encode($carmodel);exit;
    }

}