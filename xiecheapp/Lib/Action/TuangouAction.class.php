<?php
//订单
class TuangouAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->assign('current','coupon');
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('membersalecoupon');//
		$this->CarbrandModel = D('carbrand');//品牌
		$this->CarseriesModel = D('carseries');//车系
		$this->CarModel = D('carmodel');//车型
	}
	
	/*
		@author:chf
		@function:显示优惠券首页
		@time:2014-04-02
	*/
    function index(){
	    $model_brand = D('Carbrand');
	    $model_series = D('Carseries');
	    $model_carmodel = D('Carmodel');
	    $model_fs = D('Fs');
		$model_shop = D('Shop');

		if($this->city_id=='3306'){
			$model_coupon = D('Coupon');
			$model_shop_fs_relation = D('Shop_fs_relation');
			$map_tg['is_delete'] = 0;
			$map_tg['show_s_time'] = array('lt',time());
			$map_tg['show_e_time'] = array('gt',time());
			if ($_GET['coupon_type'] and $_GET['coupon_type']!='all'){
				$map_tg['coupon_type'] = $_GET['coupon_type'];
			}
			$tuangou_arr = $model_coupon->where($map_tg)->select();
			if ($tuangou_arr){
				$brand_id_arr = array();
				$series_id_arr = array();
				$model_id_arr = array();
				foreach ($tuangou_arr as $_k=>$_v){
					if ($_v['coupon_type']==1){
						$shop_id = $_v['shop_id'];
						$map_sf['shopid'] = $shop_id;
						$shop_fs_relation = $model_shop_fs_relation->where($map_sf)->select();
						if ($shop_fs_relation){
							foreach ($shop_fs_relation as $_kk=>$_vv){
								$map_sa['fsid'] = $_vv['fsid'];
								$seriesall = $model_series->where($map_sa)->select();
								if ($seriesall){
									foreach ($seriesall as $k=>$v){
										$brand_id_arr[] = $_v['brand_id'];
										$series_id_arr[] = $v['series_id'];
										$map_mall['series_id'] = $v['series_id'];
										$modelall = $model_carmodel->where($map_mall)->select();
										if ($modelall){
											foreach ($modelall as $kk=>$vv){
												$model_id_arr[] = $vv['model_id'];
											}
										}
									}
								}
							}
						}
					}else{
						
						$ArrSeries_id = explode(',',$_v['series_id']);
						foreach($ArrSeries_id as $v){
							$series_id_arr[] = $v;
						}
						$ArrModel_id = explode(',',$_v['model_id']);
						foreach($ArrModel_id as $v){
							$model_id_arr[] = $v;
						}
					
						$brand_id_arr[] = $_v['brand_id'];
					}
				}
				$brand_id_arr = array_unique($brand_id_arr);
				$series_id_arr = array_unique($series_id_arr);
				$model_id_arr = array_unique($model_id_arr);
				$brand_id_str = implode(',',$brand_id_arr);
				$series_id_str = implode(',',$series_id_arr);
				$model_id_str = implode(',',$model_id_arr);
			}
			$map_b['brand_id'] = array('in',$brand_id_str);
			$brands = $model_brand->where($map_b)->select();
			$this->assign('brands',$brands);
			
			$brand_url = '';
			if (isset($_GET['brand_id'])){
				$brand_id = $_GET['brand_id'];
				if ($_GET['brand_id'] !='all'){
					$brand_url = '/brand_id/'.$brand_id;
					$map_series['brand_id'] = $brand_id;
					$map_series['series_id'] = array('in',$series_id_str);
					$series = $model_series->where($map_series)->select();
					
					//echo $model_series->getLastSql();
					if ($series){
						foreach ($series as $_ks=>$_vs){
							$fsid_arr[] = $_vs['fsid'];
						}
						$fsid_arr = array_unique($fsid_arr);
						$fsid_str = implode(',',$fsid_arr);
						$map_f['fsid'] = array('in',$fsid_str);
						$fs = $model_shop_fs_relation->where($map_f)->select();
						if ($fs){
							foreach ($fs as $_kf=>$_vf){
								$shopid_arr[] = $_vf['shopid'];
							}
							
							$shopid_arr = array_unique($shopid_arr);
							$shopid_str = implode(',',$shopid_arr);
							//echo $shopid_str;
							//$map_s['shop_id'] = array('in',$shopid_str);
						}
					}
					if ($shopid_str and $_GET['coupon_type']!=2){
						$sql_1 = " coupon_type=1 AND shop_id IN (".$shopid_str.") ";
					}else{
						$sql_1 = "";
					}
					if ($_GET['coupon_type']!=1){
						$sql_2 = " coupon_type=2 AND brand_id='".$brand_id."' ";
					}else {
						$sql_2 = "";
					}
					
					
					//$map_tg['brand_id'] = $brand_id;
					$this->assign('series',$series);
					$this->assign('brand_id',$brand_id);
					//$this->assign('brand_url',$brand_url);
				}
			}else{
				if ($_GET['coupon_type']==1){
					$sql_1 = " coupon_type=1 ";
					
				}elseif ($_GET['coupon_type']==2) {
					$sql_2 = " coupon_type=2 ";
					
				}
			}
			$series_url = '';
			if (isset($_GET['series_id'])){
				$series_id = $_GET['series_id'];
				if ($_GET['series_id'] !='all'){
					$series_url .= '/series_id/'.$series_id;
					$map_carmodel['series_id'] = $series_id;
					$map_carmodel['model_id'] = array('in',$model_id_str);
					$carmodel = $model_carmodel->where($map_carmodel)->select();
					
					$map_se['series_id'] = $series_id;
					$series = $model_series->where($map_se)->find();
					 
					if ($series){
						$fsid = $series['fsid'];
						$map_fs['fsid'] = $fsid;
						$fs = $model_shop_fs_relation->where($map_fs)->select();
						if ($fs){
							foreach ($fs as $_kf=>$_vf){
								$shopid_arrs[] = $_vf['shopid'];
							}
							$shopid_arrs = array_unique($shopid_arrs);
							$shopid_strs = implode(',',$shopid_arrs);
							$map_s['shop_id'] = array('in',$shopid_strs);
						}
					}
					if ($shopid_strs and $_GET['coupon_type']!=2 ){
						$sql_1 = " coupon_type=1 AND shop_id IN (".$shopid_strs.") ";
					}else{
						$sql_1 = "";
					}
					if ($_GET['coupon_type']!=1){
						//$sql_2 = " coupon_type=2 AND series_id='".$series_id."' ";
						$sql_2 = " coupon_type=2 AND series_id LIKE '%".$series_id."%' ";
					}else {
						$sql_2 = "";
					}
					
					
					//$map_tg['series_id'] = $series_id;
					$this->assign('carmodel',$carmodel);
					$this->assign('series_id',$series_id);
					//$this->assign('series_url',$series_url);
				}
			}
			$model_url = '';
			if (isset($_GET['model_id'])){
				$model_id = $_GET['model_id'];
				if ($_GET['model_id'] !='all'){
					$model_url .= '/model_id/'.$model_id;
					if ($_GET['coupon_type']!=1){
						//$sql_2 .= " AND model_id='".$model_id."'";
						$sql_2 .= " AND model_id LIKE '%".$model_id."%'";
					}
					//$map_tg['model_id'] = $model_id;
					$this->assign('model_id',$model_id);
					//$this->assign('model_url',$model_url);
				}
			}
			if ($sql_1 and $sql_2){
				$sql = "( (".$sql_1.") OR (".$sql_2.") )";
			}elseif ($sql_1) {
				$sql = $sql_1;
			}else {
				$sql = $sql_2;
			}
			
			$coupon_type_url = '';
			if ($_GET['coupon_type'] and $_GET['coupon_type']!='all'){
				
				//$sql_3 = " AND coupon_type='".$_GET['coupon_type']."'";
				//$sql .= $sql_3;
				//$map_tg['coupon_type'] = $_GET['coupon_type'];
				$coupon_type_url .= '/coupon_type/'.$_GET['coupon_type'];
				$this->assign('coupon_type',$_GET['coupon_type']);
				$this->assign('coupon_type_url',$coupon_type_url);
			}
			$map_tg['is_delete'] = 0;
			$map_tg['start_time'] = array('lte',strtotime(date("Y-m-d",time())));
			$map_tg['end_time'] = array('gt',strtotime(date("Y-m-d",time())));
			$now = strtotime(date("Y-m-d",time()));
			if ($sql){
				$sql .= " AND is_delete=0 AND show_s_time<='".$now."' AND show_e_time>'".$now."'";
			}else{
				$sql .= " is_delete=0 AND show_s_time<='".$now."' AND show_e_time>'".$now."'";
			}

			$count = $model_coupon->where($sql)->count();
			// 导入分页类
			import("ORG.Util.Page");
			// 实例化分页类
			$p = new Page($count, 16);
			// 分页显示输出
			$page = $p->show();
			
			/*
			*@name:针对第一页无条件 做缓存处理
			*@author:ysh
			*@time:2013/8/1 
			*@缓存先注视
			
			if( ( $sql == " is_delete=0 AND show_s_time<='".$now."' AND show_e_time>'".$now."'" ) && ( $p->firstRow == 0 )) {
				$memcache_tuangous = S('coupon_sql_default');
				if($memcache_tuangous) {
					$tuangous = $memcache_tuangous;
				}else {
					$tuangous = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
					S('coupon_sql_default',$tuangous);
				}

			}else {*/
				$tuangous = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
			//}

			if($tuangous){
				foreach($tuangous as $key=>$val) {
					$shop_info = $model_shop->find($val['shop_id']);
					$tuangous[$key]['shop_name'] = $shop_info['shop_name'];
					$tuangous[$key]['shop_address'] = $shop_info['shop_address'];
				}
			}

		   if(!$tuangous){
				$this->assign('brand_url',"");
				$this->assign('series_url',"");
				$this->assign('model_url',"");
		   }else{
				$this->assign('brand_url',$brand_url);
				$this->assign('series_url',$series_url);
				$this->assign('model_url',$model_url);
		   }
			$this->assign('page',$page);
			$this->assign('tuangous',$tuangous);
			$this->get_tuijian_coupon();
			
			if($_GET['coupon_type']){
				if($_GET['coupon_type'] == '1'){
					$title_coupon_type = '现金券';
				}else{
					$title_coupon_type = '套餐券';
				}
			}
			if($_GET['brand_id']){//品牌
				$Str_carbrand = $this->CarbrandModel->where(array('brand_id'=>$_GET['brand_id']))->find();
				$title_carbrand = $Str_carbrand['brand_name'];
				
			}
			if($_GET['series_id']){//车系
				$Str_series = $this->CarseriesModel->where(array('series_id'=>$_GET['series_id']))->find();
				$title_series_name = $Str_series['series_name'];
			}
			if($_GET['model_id']){//车型
				$Str_model = $this->CarModel->where(array('model_id'=>$_GET['model_id']))->find();
				$title_model_name = $Str_model['model_name'];
			}
				$last_title = $title_carbrand.$title_series_name.$title_model_name."汽车维修|保养".$title_coupon_type."-携车网";
		
			if(!$_GET['model_id'] && !$_GET['series_id'] && !$_GET['brand_id'] && !$_GET['coupon_type']){
				$this->assign('title',"4S店售后团购-携车网");
			}else{
				$this->assign('title',$last_title);
			}
			$this->assign('meta_keyword',"汽车保养,汽车保养团购,汽车维修,汽车维修团购,4S预约保养");
			$this->assign('description',"汽车保养维修团首选携车网,海量车型,团购套餐任您选,还有分时折扣,最低5折起,汽车保养维修团购,50元养车券免费领,在线预约专人接待,即到即修4006602822");
		}
	    $this->display();
	}
	
	/*
		@author:chf
		@function:显示现金券或者团购券详情
		@time:2013-04-09
	*/
	function detail(){
	    $coupon_id = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;
	    if ($coupon_id){
	        $model_coupon = D('Coupon');

			$memcache_coupon = S('coupon'.$_GET['coupon_id']);
			if($memcache_coupon) {
				$coupon = $memcache_coupon;
			}else {
				$coupon = $model_coupon->find($coupon_id);
				if($coupon['show_e_time'] < time()) {
					$coupon['is_delete'] = 1;
				}
				S('coupon'.$_GET['coupon_id'] , $coupon );
			}

	        $coupon['save_money'] = $coupon['cost_price'] - $coupon['coupon_amount'];
	        $this->get_coupon_info($coupon);
	        $shop_id = $coupon['shop_id'];
	        $model_shop = D('Shop');
	        $map_s['id'] = $shop_id;
	        $shopinfo = $model_shop->where($map_s)->find();

			$memcache_other_coupon = S('other_coupon'.$_GET['coupon_id']);
			if($memcache_other_coupon) {
				$other_coupon = $memcache_other_coupon;
			}else {
				$map_c['shop_id'] = $shop_id;
				$map_c['id'] = array('neq',$coupon_id);
				$map_c['is_delete'] = 0;
				$map_c['show_s_time'] = array('lt',time());
				$map_c['show_e_time'] = array('gt',time());
				$other_coupon = $model_coupon->where($map_c)->select();
				S('other_coupon'.$_GET['coupon_id'] , $other_coupon );
			}

	       
    	    $this->assign('other_coupon',$other_coupon);
    	    $this->assign('shopinfo',$shopinfo);
	    }
	    $this->get_tuijian_coupon();
	    $this->assign("coupon",$coupon);
	    $this->assign("coupon_id",$coupon_id);
		
		$model_carmodel = D('carmodel');
		
		$mode_info = $model_carmodel->getByModel_id($coupon['model_id']);

		
		
		$model_shop_fs_relation = D('shop_fs_relation');
		$FsModel = D('fs');
		$FsId = $model_shop_fs_relation->where(array('shopid'=>$coupon['shop_id']))->select();
		
		if($FsId){
			foreach($FsId as $k=>$v){
				
				$fsname = $FsModel->where(array('fsid'=>$v['fsid']))->find();
				
				$datafsname.= $fsname['fsname'];
			}
		}
		
		
		
		/*取得该团购券能使用的车型车系*/

		$memcache_seriesinfo = S('Seriesinfo'.$_GET['coupon_id']);
		//if($memcache_seriesinfo) {
			//$Seriesinfo = $memcache_seriesinfo;
		//}else {

			$SeriesModel = D('Carseries');
			$CarModel = D('Carmodel');	
			$ArrModel_id = explode(',',$coupon['model_id']);
			$CarbrandModel = D('carbrand');
			$Brand = $CarbrandModel->where(array('brand_id'=>$coupon['brand_id']))->find();
			$ArrSeries_id = explode(',',$coupon['series_id']);
			$ArrSeries_id = array_unique($ArrSeries_id);
			if($coupon['coupon_type'] == 2){
				if($ArrSeries_id){
					foreach($ArrSeries_id as $v){
						$modelid = "";
						$SeriesList = $SeriesModel->where(array('series_id'=>$v))->find();
						$CarmodelID = $CarModel->where(array('series_id'=>$v))->select();
						foreach($CarmodelID as $car_k=>$car_v){
							for($a=0;$a<count($ArrModel_id);$a++){
								if($ArrModel_id[$a]){
									if($car_v['model_id'] == $ArrModel_id[$a]){
										$modelid.= $car_v['model_id'].",";
									}
								}
							}
						}
						 $modelid = substr($modelid,0,-1);
						 $sql = "model_id in (".$modelid.")";
						 $str = $CarModel->where($sql)->select();
						
						 $model_info ="";

						 foreach($str as $str_k=>$str_v){
							$StrSeries = $SeriesModel->where(array('series_id'=>$str_v['series_id']))->find();
						 	$model_info.= "<br>".$str_v['model_name'];
						 }
						 
						$Seriesinfo.= "<li><b>".$datafsname.$SeriesList['series_name']."</b>：".$model_info."</li>";
					}
				}
				
			}else{
				$Seriesinfo = "<b>".$datafsname."</b>所有车型车系";
				
			}

			//S('Seriesinfo'.$_GET['coupon_id'] , $Seriesinfo);

		//}
		
		/*取得该团购券能使用的车型车系*/

		$this->assign('Direction',$Seriesinfo);
		$this->assign('phone',C('CALL_400'));
		$this->assign('coupon_type',$coupon['coupon_type']);
		if(TOP_CSS == "pa") {
			$this->assign('title',"{$coupon[coupon_name]}_汽车保养维修团购_平安好车-携车网");
		}else {

			$this->assign('title',$coupon['coupon_name']."|".$shopinfo['shop_name']."|".$shopinfo['shop_address']."_汽车保养维修团购_携车网");
		}
		$this->assign('meta_keyword',"保养费用,".$coupon['coupon_name']);
		$this->assign('description',$coupon['coupon_name']."，原价{$coupon[cost_price]}元(全国同一价)，套餐优惠价{$coupon[coupon_amount]}元。套餐有效期截止到".date("Y年m月d日",$coupon['end_time'])."，仅限通过携车网提前预约并网上支付后到店接受服务。");
	    $this->display();
	}

	function get_coupon_info($coupon){
	    if ($coupon['brand_id']){
            $model_carbrand = D('Carbrand');
            $map_b['brand_id'] = $coupon['brand_id'];
            $brand = $model_carbrand->where($map_b)->find();
            $this->assign('brand',$brand);
        }
         if ($coupon['series_id']){
            $model_carseries = D('Carseries');
            $map_s['series_id'] = $coupon['series_id'];
            $series = $model_carseries->where($map_s)->find();
            $this->assign('series',$series);
        }
        if ($coupon['model_id']){
            $model_carmodel = D('Carmodel');
            $map_m['model_id'] = $coupon['model_id'];
            $model = $model_carmodel->where($map_m)->find();
            $this->assign('model',$model);
        }
	}

}