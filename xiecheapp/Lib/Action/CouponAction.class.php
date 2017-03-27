<?php
//订单
class CouponAction extends CommonAction {	
	public function __construct() {
				Header( "Location: http://www.xieche.com.cn/coupon" );
		exit;
		parent::__construct();
		$this->assign('current','coupon');
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('membersalecoupon');//
		$this->MembercouponModel = D('membercoupon');//
		$this->CarbrandModel = D('carbrand');//品牌
		$this->CarseriesModel = D('carseries');//车系
		$this->CarModel = D('carmodel');//车型

		$this->CouponModel = D('coupon');//优惠券表
		$this->Shopmodel = D('shop');//店铺
	}
	
	
    public function index(){

		//百车宝
		if($_GET['bcb'] == '1'){
			$_SESSION['bcb'] = '1';
		}
		
		$coupon_type = $_GET['coupon_type'];
		$brand_id = $_GET['brand_id'];
		$series_id = $_GET['series_id'];
		$model_id = $_GET['model_id'];
		$this->assign('coupon_type',$coupon_type);
		$this->assign('brand_id',$brand_id);
		$this->assign('series_id',$series_id);
		$this->assign('model_id',$model_id);

		$url_map['is_delete'] = $data['is_delete'] = '0';
		$url_map['start_time'] = $data['start_time'] = array('lt',time());
		$url_map['end_time'] = $data['end_time'] = array('gt',time());

		if($coupon_type && $coupon_type != 'all'){
			$url_map['coupon_type'] = $data['coupon_type'] = $coupon_type;
		}
		if($brand_id && $brand_id != 'all'){
			$data['_string'].= " FIND_IN_SET('{$brand_id}', brand_id)";
		}
		if($series_id && $series_id != 'all'){
			$data['_string'].= " AND FIND_IN_SET('{$series_id}', series_id)";
		}
		if($model_id && $model_id != 'all'){
			$data['_string'].= " AND FIND_IN_SET('{$model_id}', model_id)";
		}
			
		$count = $this->CouponModel->where($data)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show();
		
		$tuangous = $this->CouponModel->where($data)->limit($p->firstRow.','.$p->listRows)->order("id DESC")->select();
		if($tuangous){
			foreach($tuangous as $k=>$v){
				//$shop_map['id'] = array('in',$v['shop_id']);
				$shop_map['id'] = $v['shop_id'];
				$shop = $this->Shopmodel->where($shop_map)->find();
				
				$tuangous[$k]['address'] = $shop['shop_address'];
			}
		}
		//print_r($tuangous);
		$url = $this->CouponModel->where($url_map)->select();
	
		if($url){
			$brand_idarr[] = "";
			$series_idarr[] = "";
			$model_idarr[] = "";
			foreach($url as $k=>$v){
				$brand_idarr[] = $v['brand_id'];
				$series_idarr[]= $v['series_id'];
				$series_idstr.= $v['series_id'].",";
				$model_idstr.= $v['model_id'].",";
			}
			
			$series_idarr[] = $v['series_id'];
			/*从团购券中搜索优惠券对应的品牌信息....*/
			//if($_GET['coupon_type'] && $_GET['coupon_type']!='all'){
				$carbrand['brand_id'] = array('in',array_unique($brand_idarr));
				$brands = $this->CarbrandModel->where($carbrand)->select();
				$this->assign('brands',$brands);
			/*从团购券中搜索优惠券对应的品牌信息结束....*/
			//}
			$series_idstr = implode(',',array_unique(explode(',',substr($series_idstr,0,-1))));//处理车系数据
			$model_idstr = implode(',',array_unique(explode(',',substr($model_idstr,0,-1))));//处理车型数据
			if($_GET['brand_id'] && $_GET['brand_id']!='all'){
				//print_r($series_idarr);
				/*从团购券中搜索优惠券对应的车系信息....*/
				$carseries['series_id'] = array('in',$series_idstr);
				//$series_idar = substr($series_idar,0,-1);
				//$carseries['_string'] .= " FIND_IN_SET('{$a}', series_id)";
				$carseries['brand_id'] = $_GET['brand_id'];
			
				$series = $this->CarseriesModel->where($carseries)->select();
			
				$this->assign('series',$series);
				/*从团购券中搜索优惠券对应的车系信息结束....*/
			}
			if($_GET['series_id'] && $_GET['series_id']!='all'){
				$carmodel['model_id'] = array('in',$model_idstr);
				$carmodel['series_id'] = $series_id;
				$carmodel = $this->CarModel->where($carmodel)->select();
			
				$this->assign('carmodel',$carmodel);
			}
		}
		$this->get_tuijian_coupon();
		$this->assign('page',$page);
		$this->assign('tuangous',$tuangous);

			
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
				$last_title = $title_carbrand.$title_series_name.$title_model_name."汽车维修保养预约".$title_coupon_type."-携车网";
				$last_description = $title_carbrand.$title_series_name.$title_model_name."汽车保养维修团首选携车网,海量车型,团购套餐任您选,还有分时折扣,最低5折起,汽车保养维修团购,50元养车券免费领,在线预约专人接待,即到即修4006602822";

			if(!$_GET['model_id'] && !$_GET['series_id'] && !$_GET['brand_id'] && !$_GET['coupon_type']){
				$this->assign('title',"4S店售后团购-携车网");
				$this->assign('description',"汽车保养维修团首选携车网,海量车型,团购套餐任您选,还有分时折扣,最低5折起,汽车保养维修团购,50元养车券免费领,在线预约专人接待,即到即修4006602822");
			}else{
				$this->assign('title',$last_title);
				$this->assign('description',$last_description);
			}
			$this->assign('meta_keyword',"汽车保养,汽车保养团购,汽车维修,汽车维修团购");

		
		
	    $this->display("index_new");
	}
	
	/*
		@author:chf
		@function:显示现金券或者团购券详情
		@time:2013-04-09
	*/
	function coupondetail(){
	
	    $coupon_id = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;
	    if ($coupon_id){
	        $model_coupon = D('Coupon');

			$memcache_coupon = S('coupon'.$_GET['coupon_id']);
			if($memcache_coupon) {
				$coupon = $memcache_coupon;
			}else {
				$coupon = $model_coupon->find($coupon_id);
				if($coupon['show_e_time'] < time() && $coupon) {
					$coupon['is_delete'] = 1;
				}
				S('coupon'.$_GET['coupon_id'] , $coupon );
			}
			


			if($coupon) {
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

				$model_carseries = D('Carseries');
				$model_carmodel = D('Carmodel');
				
				$series = explode(',',$coupon['series_id']);
				$model_id = explode(',',$coupon['model_id']);
				$num_count = count($series);
				for($a=1;$a<=$num_count;$a++){
					
					$map_s['series_id'] = $series[$a];
					$series = $model_carseries->where($map_s)->find();
					
					if ($model_id){
						$map_m['model_id'] = $model_id[$a];
						$model = $model_carmodel->where($map_m)->find();
						$model_name = $model['model_name'];
					}else{
						$model_name = '全车系';
					}
					$car[$a]['car_name'] = $series['series_name'].$model_name;
					if($car[$a]['car_name']){
						if($a == '1' || $a % 4 == '1'){
						$car[$a]['top'] = "<tr>";
						}
						if($a %4 == '0'){
							$car[$a]['foot'] = "</tr>";
						}
					}else{
						unset($car[$a]);
					}
				}
				
				$this->assign('car',$car);

			}else {
				$this->_empty();
				exit();
			}
			
	        //查询符合的车系车型信息开始
			$model_carseries = D('Carseries');
			$model_carmodel = D('Carmodel');
			$series = explode(',',$coupon['series_id']);
			$model_id = explode(',',$coupon['model_id']);
			$num_count = count($series);
			for($a=0;$a<$num_count;$a++){
				$map_s['series_id'] = $series[$a];
				$series = $model_carseries->where($map_s)->find();
				
				if ($model_id){
					$map_m['model_id'] = $model_id[$a];
					$model = $model_carmodel->where($map_m)->find();
					$model_name = $model['model_name'];
				}else{
					$model_name = '全车系';
				}
				$car[$a]['car_name'] = $series['series_name'].$model_name;
			}
		
			$this->assign('car',$car);

			//查询符合的车系车型信息结束
	       
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
							//$model_info.= "<a target=_blank href='__APP__/coupon/index/coupon_type/2/brand_id/$StrSeries[brand_id]/series_id/$StrSeries[series_id]/model_id/$str_v[model_id]'>".$str_v['model_name']."</a><br>   ";
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
			if($coupon['coupon_type']  == '1') {
				$coupon_type = "现金券";
			}else {
				$coupon_type = "套餐券";
			}
			$this->assign('title',$shopinfo['shop_name']."-".$coupon['coupon_name'].$coupon['coupon_amount'].$coupon_type."-携车网");
		}
		$this->assign('meta_keyword',"保养费用,".$coupon['coupon_name']);
		$this->assign('description',$coupon['coupon_name']."，原价{$coupon[cost_price]}元(全国同一价)，套餐优惠价{$coupon[coupon_amount]}元。套餐有效期截止到".date("Y年m月d日",$coupon['end_time'])."，仅限通过携车网提前预约并网上支付后到店接受服务。");
	    $this->display("coupondetail_new");
	}

	/*
		author:chf
		function:购买优惠卷页面
		time:2013-04-12
	*/
	function couponbuy(){
	    if( true !== $this->login()){
			exit;
		}
	
		$uid = $this->GetUserId();
		$model_member = D('Member');
		$map_m['uid'] = $uid;
		$member = $model_member->where($map_m)->find();
	    $coupon_id = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;
	    if ($coupon_id){
	        //$model_tuangou = D('Tuangou');
	        $model_coupon = D('Coupon');
	        $coupon = $model_coupon->find($coupon_id);
	        $coupon['save_money'] = $coupon['cost_price'] - $coupon['coupon_amount'];
	        $this->get_coupon_info($coupon);
	    }
	    $this->get_tuijian_coupon();

	    $Member = $this->MemberModel->where(array('uid'=>$uid))->find();
		$Msalecoupon = $this->MembersalecouponModel->where(array('uid'=>$uid,'salecoupon_id'=>array('in','4,6'),'mobile'=>$Member['mobile'],'is_use'=>'0'))->find();

		//获取商户现金账户余额
		$model_memberaccount = D('memberaccount');
		$map_m['uid'] = $uid;
		$memberaccount = $model_memberaccount->where($map_m)->find();
		$coupon['amount'] = $memberaccount['amount'];

		$this->assign('Msalecoupon',$Msalecoupon);
		$this->assign('sessuid',$uid);

	    $this->assign("coupon",$coupon);
	    $this->assign("coupon_id",$coupon_id);
	    $this->assign("member",$member);

		if(TOP_CSS == "pa") {
			$this->assign('title',"{$coupon[coupon_name]}_汽车保养维修团购_平安好车-携车网");
		}else {
			$this->assign('title',$coupon['coupon_name']."-购买-4S店售后团购-携车网");
		}
		$this->assign('meta_keyword',"保养费用,".$coupon['coupon_name']);
		$this->assign('description',$coupon['coupon_name']."，原价{$coupon[cost_price]}元(全国同一价)，套餐优惠价{$coupon[coupon_amount]}元。套餐有效期截止到".date("Y年m月d日",$coupon['end_time'])."，仅限通过携车网提前预约并网上支付后到店接受服务。");

		$this->display('couponbuy_new');
	}

	/*
		@author:chf
		@function:
		@time:2014-3-21
	*/
	function savecoupon(){
	    if( true !== $this->login()){
			exit;
		}
        $model_coupon = D('Coupon');
        $model_membercoupon = D('Membercoupon');
		
		$this->is_safepay();//处理现金账户支付流程

		$membersalecoupon_id = $_POST['membersalecoupon_id'];//立减券ID
		//得到优惠券信息
		$membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id,'is_use'=>'0','is_delete'=>'0'))->find();
		
	    $coupon_id = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;

	    $number = isset($_REQUEST['number'])?$_REQUEST['number']:1;
		
	    $mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:0;
	    if (!$mobile){
	        $this->error("请确认手机号码是否正确",__APP__.'/coupon/');
	    }
	    if ($number>0 and is_numeric($number)){
	        $map_coupon['id'] = $coupon_id;
	        $map_coupon['end_time'] = array('gt',time());
	        $coupon = $model_coupon->where($map_coupon)->find();
//			$model_shop = D('shop');
//			$shop = $model_shop->where(array('id'=>$coupon['shop_id']))->find();
			if($coupon['is_delete'] == '1'){
				$this->error("抱歉,此优惠卷已下架！",__APP__.'/coupon/');
			}else{  //300*（1-100/600）
				 if($coupon){
					$All_amount = $coupon['coupon_amount']*$number;//总支付金额
					if($membersalecoupon_id > 0 && $number>1){
						$AllSaleamount = $All_amount-$membersalecoupon['money'];//
					}

					if($membersalecoupon_id){
						$data['membersalecoupon_id'] = $membersalecoupon_id;
					}
					$membercoupon_id_str = '';
				
					for ($ii = 1; $ii <= $number; $ii++) {
							//有立减券-100
							if($membersalecoupon_id > 0 && $number==1){//1张优惠券
								$data['coupon_amount'] = $coupon['coupon_amount'] - $membersalecoupon['money'];//支付金额
							}else if($membersalecoupon_id > 0 && $number>1){//多张优惠券
								
								if($ii != $number){
									$data['coupon_amount'] = $coupon['coupon_amount']*(1-sprintf("%.2f", ($membersalecoupon['money']/$All_amount)));
									$Saleamount+= $data['coupon_amount'];
								}else{
									$data['coupon_amount'] = $AllSaleamount - $Saleamount;
								}
							}else if(!$membersalecoupon_id){//没有立减券
								$data['coupon_amount'] = $coupon['coupon_amount'];
							}
					
						$uid = $this->GetUserId();
						$data['uid'] = $uid;
						$data['coupon_id'] = $coupon_id;
						$data['coupon_name'] = $coupon['coupon_name'];
						$data['shop_ids'] = $coupon['shop_id'];
						$data['mobile'] = $mobile;
						$data['end_time'] = $coupon['end_time'];
						$data['start_time'] = $coupon['start_time'];
						$data['coupon_type'] = $coupon['coupon_type'];
						$data['create_time'] = time();

						/* 改到使用的时候再去计算
						if($coupon['coupon_type']=='1'){
							$data['ratio'] = $shop['cash_rebate'];//现金券比例
						}else{
							$data['ratio'] = $shop['group_rebate'];
						}*/
						//记录购买平安好车访问用户
						if(PA_BANNER == 'pa'){
							$data['pa'] = '1';
						}
						//百车保用户
						if($_SESSION['bcb']){
							$data['pa'] = '4';
						}
						if($membercoupon_id = $model_membercoupon->add($data)){
							$membercoupon_id_arr[] = $membercoupon_id;
						}
					}
					$membercoupon_id_str = implode(',',$membercoupon_id_arr);
					$_SESSION['membercoupon_ids'] = $membercoupon_id_str;
					if($membersalecoupon_id > 0){
						$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('is_use'=>'1','use_time'=>$data['create_time']));//修改红包状态测试完毕后在加上
					}
					//消除百车宝session
					$_SESSION['bcb'] = "";

					if($_POST['account_amount']>0){
						//计算现金账户支付之后是否还需要走线上支付流程
						$map_c['id'] = $_REQUEST['coupon_id'];
						$coupon = $this->CouponModel->where($map_c)->find();
						$all_amount = $coupon['coupon_amount']*$_REQUEST['number'];
						if($_POST['account_amount']<$all_amount){
							$this->success("订单已提交,请立即付款",__APP__.'/coupon/couponpay');
						}else{
							//支付完成善后处理
							$this->update_membercoupon_state($membercoupon_id,1);
							$this->update_coupon_count($_REQUEST['coupon_id']);
							$this->success("购买成功并用现金账户余额完成支付",__APP__.'/coupon');
						}
					}else{
						$this->success("订单已提交,请立即付款",__APP__.'/coupon/couponpay');
					}
					
				}else{
					$_SESSION['bcb'] = "";
					$this->error("操作失败",__APP__.'/coupon/');
				}	
			}
	    }else {
			$_SESSION['bcb'] = "";
	       $this->error("购买数量输入错误",__APP__.'/coupon/');
	    }
	    
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


	
	
	function couponpay(){
		$this->assign('sessuid',$_SESSION['uid']);
		$_SESSION['jump'] = "jump";
		if($_GET['membercoupon_id']){
			$membercoupon_id = isset($_GET['membercoupon_id'])?$_GET['membercoupon_id']:0;
		}else{
			$membercoupon_id = isset($_SESSION['membercoupon_ids'])?$_SESSION['membercoupon_ids']:0;
		}
	    $uid = $this->GetUserId();
	    if ($membercoupon_id){
	        $model_membercoupon = D('Membercoupon');
	        $model_coupon = D('Coupon');
	        $map_m['membercoupon_id'] = array('in',$membercoupon_id);
	        $map_m['uid'] = $uid;
	        $map_m['end_time'] = array('gt',time());
	        $membercoupon = $model_membercoupon->where($map_m)->select();
	        $all_amount = 0;
	        if ($membercoupon){
	            foreach ($membercoupon as $k=>$v){
	                $coupon_id = $v['coupon_id'];
	                $map_c['id'] = $coupon_id;
	                $coupon = $model_coupon->where($map_c)->find();
	                $membercoupon[$k]['couponinfo'] = $coupon;
	                $all_amount+= $v['coupon_amount'];
	                $this->get_coupon_info($coupon);
					$coupon_name = $v['coupon_name'];
	            }
	        }

			//扣除现金账户支付部分
			$all_amount = $all_amount-$_SESSION['account_amount'];
	        $this->assign('all_amount',$all_amount);
			$this->assign('coupon_name',$coupon_name);
	        $this->assign('membercoupon_id',$membercoupon_id);
	        $this->assign('membercoupons',$membercoupon);
	    }
	    $this->get_tuijian_coupon();
	    $this->display('couponpay_new');
	}

	function check_sale(){
		 $membercoupon_id = $_POST['membercoupon_id'];
	}

	//支付跳转判断 wuwenyu
	function do_couponpay(){
		if(true !== $this->login()){
			exit;
		}
		$key = isset($_REQUEST['sel_online_pay'])?$_REQUEST['sel_online_pay']:1;
		$membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:1;

		$model_membercoupon = D('Membercoupon');
		$map_m['membercoupon_id'] = array('in',$membercoupon_id);
		$uid = $this->GetUserId();
		$map_m['uid'] = $uid;
		$map_m['end_time'] = array('gt',time());
		$membercoupon = $model_membercoupon->where($map_m)->find();
		if($membercoupon['is_pay']=='1'){
			$this->error("订单已支付",__APP__.'/coupon/');
		}
	
		$bank_type=$this->get_bank($key);
		if($bank_type=='CFT')$url='http://www.xieche.com.cn/txpay/payRequest.php?membercoupon_id='.$membercoupon_id.'&account_amount='.$_SESSION['account_amount'];
		if($bank_type=='YL')$url='http://www.xieche.com.cn/unionpay/front.php?membercoupon_id='.$membercoupon_id.'&account_amount='.$_SESSION['account_amount'];
		if($bank_type=='COMM'){
			$membercoupons = $this->get_membercoupons($membercoupon_id);
			$coupon_amount = 0;
			if($membercoupons){
				foreach($membercoupons as $k=>$v){
					$coupon_amount += $v['coupon_amount'];
				}
			}
			$coupon_amount = $coupon_amount-$_SESSION['account_amount'];//扣除现金账户支付部分
			//7月1日开始交行支付95折优惠
			$a = strtotime('now');
			if($a>1404144000){
				$coupon_amount = round($coupon_amount*0.95);
			}

			$array='interfaceVersion=1.0.0.0&orderid='.$membercoupon_id."&orderDate=".date('Ymd',time())."&orderTime=".date('His',time()).'&tranType=0&amount='.$coupon_amount.'&curType=CNY&orderMono=6222600110030037084& notifyType=1&merURL=http://www.xieche.com.cn/comm_pay/merchant_result.php&goodsURL=http://www.xieche.com.cn/comm_pay/merchant_result.php&netType=0';
			$url='http://www.xieche.com.cn/comm_pay/merchant.php?'.$array;
		}
		if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='COMM')$url='http://www.xieche.com.cn/txpay/payRequest.php?membercoupon_id='.$membercoupon_id.'&bank_type='.$bank_type.'&account_amount='.$_SESSION['account_amount'];
		header("Location:$url");
		//$this->display('do_couponpay');
	}
	//银行转换数组 wuwenyu
	function get_bank($key){
		$bank_array=array(
			'19'=>'CMB',//招商银行
			'31'=>'BOC',//中国银行
			'17'=>'ICBC',//工商银行
			'18'=>'ABC',//农业银行
			'20'=>'CCB',//建设银行
			'74'=>'PAB',//平安银行
			'501'=>'POSTGC',//邮储银行
			'34'=>'CEB',//光大银行
			'35'=>'CIB',//兴业银行
			'36'=>'CITIC',//中信银行
			'37'=>'CMBC',//民生银行
			'38'=>'COMM',//交通银行
			'39'=>'GDB',//广东发展银行
			'41'=>'BOSH',//上海银行
			'42'=>'SPDB',//浦发银行
			'100'=>'YL',//银联
			'8'=>'CFT',//财付通
		);
		if($key){$bank_type=$bank_array[$key];}
		return $bank_type;
	}

	function get_membercoupons($membercoupon_ids){
	    $model_membercoupon = D('Membercoupon');
		$sql = "select * from xc_membercoupon where membercoupon_id in (".$membercoupon_ids.") ";
		//echo $sql;
		$membercoupon=$model_membercoupon->query($sql,'all');
		//echo '<pre>';print_r($membercoupon);exit;
		if($membercoupon){
			foreach($membercoupon as $k=>$v){
				$coupon_id = $v['coupon_id'];
				$model_coupon = D('Coupon');
				$sql_coupon = "select * from xc_coupon where id='".$coupon_id."' ";
				$coupon=$model_coupon->query($sql_coupon,'assoc');
				$membercoupon[$k]['coupon_summary'] = $coupon['coupon_summary'];
				//$membercoupon[$k]['coupon_amount'] = $coupon['coupon_amount'];
			}
		}
		return $membercoupon;
	}

	/*
	*@name:养车3折团---爆款优惠券整合页
	*@author:ysh
	*@time:2014/6/25
	*/
	function explosion() {
		$brand_id = $_REQUEST['brand_id'];
		$series_id = $_REQUEST['series_id'];
		$model_id = $_REQUEST['model_id'];
		
		$this->assign('brand_id',$brand_id);
		$this->assign('series_id',$series_id);
		$this->assign('model_id',$model_id);

		$model_shop = D('Shop');
		
		if($this->city_id=='3306'){
			$model_coupon = D('Coupon');
			
			$url_map['coupon_across'] = $map['coupon_across'] = '1';
			$url_map['is_delete'] = $map['is_delete'] = '0';
			$url_map['start_time'] = $map['start_time'] = array('lt',time());
			$url_map['end_time'] = $map['end_time'] = array('gt',time());
			if($brand_id  && $brand_id != 'all'){
				$map['_string'].= " FIND_IN_SET('{$brand_id}', brand_id)";
			}
			if($series_id  && $series_id != 'all'){
				$map['_string'].= " AND FIND_IN_SET('{$series_id}', series_id)";
			}
		
			if($model_id  && $series_id != 'all'){
				$map['_string'].= " AND FIND_IN_SET('{$model_id}', model_id)";
			}

			$count = $model_coupon->where($map)->count();
			// 导入分页类
			import("ORG.Util.Page");
			// 实例化分页类
			$p = new Page($count, 18);
			// 分页显示输出
			$page = $p->show();

			$tuangous = $model_coupon->where($map)->limit($p->firstRow.','.$p->listRows)->select();
			//echo $model_coupon->getLastSql();
			if($tuangous){
				foreach($tuangous as $key=>$val) {
					$shop_info = $model_shop->find($val['shop_id']);
					$tuangous[$key]['shop_name'] = $shop_info['shop_name'];
					$tuangous[$key]['shop_address'] = $shop_info['shop_address'];
				}
			}
		}

		$url = $this->CouponModel->where($url_map)->select();
	
		if($url){
			$brand_idarr[] = "";
			
			foreach($url as $k=>$v){
				$brand_idarr[] = $v['brand_id'];
			}
			
			/*从团购券中搜索优惠券对应的品牌信息....*/
			$carbrand['brand_id'] = array('in',array_unique($brand_idarr));
			$brands = $this->CarbrandModel->where($carbrand)->select();
			$this->assign('brands',$brands);

		}



		$this->assign('page',$page);
		$this->assign('tuangous',$tuangous);
		$this->get_tuijian_coupon();
		$this->assign('current','yangchetuan');
		$this->display();
	}



	/*
	*@name:养车3折团---爆款优惠券整合页
	*@author:ysh
	*@time:2014/6/25
	*/
	function test() {
		
		$brand_id = $_REQUEST['brand_id'];
		$series_id = $_REQUEST['series_id'];
		$model_id = $_REQUEST['model_id'];

		$model_shop = D('Shop');
		
		$model_coupon = D('Coupon');
		
		$url_map['coupon_across'] = $map['coupon_across'] = '1';
		$url_map['is_delete'] = $map['is_delete'] = '0';
		$url_map['start_time'] = $map['start_time'] = array('lt',time());
		$url_map['end_time'] = $map['end_time'] = array('gt',time());
		if($brand_id ){
			$map['_string'].= " FIND_IN_SET('{$brand_id}', brand_id)";

		}
		if($series_id ){
			$map['_string'].= " AND FIND_IN_SET('{$series_id}', series_id)";
		}
		if($model_id ){
			$map['_string'].= " AND FIND_IN_SET('{$model_id}', model_id)";
		}

		$count = $model_coupon->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 16);
		// 分页显示输出
		foreach($_POST as $key => $val) {
			if (!is_array($val) && $val != "" && $key!='__hash__') {
				$p->parameter .= "$key-" . urlencode($val) . "-";
			}
		}
		$page = $p->show();

		$tuangous = $model_coupon->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_coupon->getLastSql();
		if($tuangous){
			foreach($tuangous as $key=>$val) {
				$shop_info = $model_shop->find($val['shop_id']);
				$tuangous[$key]['shop_name'] = $shop_info['shop_name'];
				$tuangous[$key]['shop_address'] = $shop_info['shop_address'];
			}
		}
		
		$url = $this->CouponModel->where($url_map)->select();
	
		if($url){
			$brand_idarr[] = "";
			
			foreach($url as $k=>$v){
				$brand_idarr[] = $v['brand_id'];
			}
			
			/*从团购券中搜索优惠券对应的品牌信息....*/
			$carbrand['brand_id'] = array('in',array_unique($brand_idarr));
			$brands = $this->CarbrandModel->where($carbrand)->select();
			$this->assign('brands',$brands);

		}
		


		$this->assign('brand_id',$brand_id);
		$this->assign('page',$page);
		$this->assign('tuangous',$tuangous);
		$this->get_tuijian_coupon();
		$this->assign('current','yangchetuan');
		$this->display();
	}

	//处理现金账户支付部分
	function is_safepay(){
			$map['uid'] = $this->GetUserId();
		//如果用户使用了账户余额支付
		if($_POST['account_amount']){
			if($_POST['paycode']){
				//验证支付密码
				$model_membersafe = D('Membersafe');
				$safe_info = $model_membersafe->where($map)->find();
				if (pwdHash($_POST['paycode'])!=$safe_info['paycode']){
					$this->error("请确认支付密码是否正确",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
				}else{
					//确认商户现金账户余额
					$model_memberaccount = D('memberaccount');
					$member = $model_memberaccount->where($map)->find();
					if($_REQUEST['account_amount'] > $member['amount']){
						$this->error("账户余额不足",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
					}else{
						//记录账户使用记录
						$model_memberaccountlog = D('Memberaccountlog');
						$data_a['uid'] = $this->GetUserId();
						$data_a['amount'] = abs($_REQUEST['account_amount']);
						$data_a['create_time'] = time();
						$data_a['type'] = '1';
						$data_a['content'] = '购买套餐券';
						$result_a = $model_memberaccountlog->add($data_a);
						if($result_a){
							//计算余额
							$data_n['amount'] = $member['amount']-abs($_REQUEST['account_amount']);
							//echo "left=".$data_n['amount'];
							$result_b = $model_memberaccount->where($map)->save($data_n);
							//echo $model_memberaccount->getLastsql();
							if($result_b){
								$_SESSION['account_amount'] = $_REQUEST['account_amount'];
							}
						}
					}
				}
			}else{
				$this->error("支付密码不能为空",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
			}
		}else{
			$_SESSION['account_amount']='';
		}
	}

	function update_coupon_count($coupon_id){
		$map['id'] = $coupon_id;
		$result = $this->CouponModel->where($map)->find();
		$data['pay_count'] = $result['pay_count']+1;
		$this->CouponModel->where($map)->save($data);
		//echo $this->CouponModel->getLastsql();
	}

	function update_membercoupon_state($membercoupon_id,$pay_status){
		$rand_str = $this->get_coupon_code();

		$map['membercoupon_id'] = $membercoupon_id;
		$data['pay_time'] = time();
		$data['is_pay'] = $pay_status;
		$data['pay_type'] = 6;
		$data['coupon_code'] = $rand_str;
		$this->MembercouponModel->where($map)->save($data);
		//echo $this->MembercouponModel->getLastsql();
	}

	function get_coupon_code(){
        $orderid = $this->randString(9,1);
        $sum = 0;
	    for($ii=0;$ii<strlen($orderid);$ii++){
	        $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum%10;
		$result = $orderid.$str;
        return $result;
    }

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function randString($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 4:
			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// 中文随机字
		for($i=0;$i<$len;$i++){
		  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
		}
	}
	return $str;
}

}