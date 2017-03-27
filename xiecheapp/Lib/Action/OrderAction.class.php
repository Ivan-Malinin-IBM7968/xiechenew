<?php
//订单
class OrderAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->assign('current','order');
		$this->ShopModel = D('shop');
		$this->MembersalecouponModel = D('membersalecoupon');
		$this->CarcodeModel = D('carcode');
		$this->MemberModel = D('member');
		$this->Salecouponmodel = D('Salecoupon');
		$this->MembersalecouponModel = D('membersalecoupon');
		$this->Smsmodel = D('Sms');//短信表
		$this->Servcemembermodel = D('servicemember');//服务顾问表
		$this->Commentmodel = D('comment');//评论表
		$this->Fsmodel = D('fs');//评论表
		$this->Regionmodel = D("Region");
		$this->CouponModel = D("coupon");
	}

	/*
	 * 判断条件
     *
     */
    function _filter(&$map){
    	//项目分类S1
        if (isset ( $_REQUEST ['s1'] ) && $_REQUEST ['s1'] != '') {
            $map['xc_product.service_item_id'] = array('eq',$_REQUEST['s1']);
        }
        //服务项目S2
        if (isset ( $_REQUEST ['s2'] ) && $_REQUEST ['s2'] != '') {
            $map['xc_product.service_id'] = array('eq',$_REQUEST['s2']);
        }
        //车型ID
        if (isset ( $_REQUEST ['model_id'] ) && $_REQUEST ['model_id'] != '') {
            $map['xc_product.car_model_id'] = array('eq',$_REQUEST['model_id']);
        }
        //店铺ID
    	if (isset ( $_REQUEST ['shop_id'] ) && $_REQUEST ['shop_id'] != '') {
            $map['xc_product.shop_id'] = array('eq',$_REQUEST['shop_id']);
        }
        //订单状态
        if (isset ( $_REQUEST ['order_state'] ) && $_REQUEST ['order_state'] != '') {
            $map['order_state'] = array('eq',$_REQUEST['order_state']);
        }
        //订单投诉状态
        if (isset ( $_REQUEST ['complain_state'] ) && $_REQUEST ['complain_state'] != '') {
            $map['complain_state'] = array('eq',$_REQUEST['complain_state']);
        }
        //订单ID
        if (isset ( $_REQUEST ['order_id'] ) && $_REQUEST ['order_id'] != '') {
            $map['order_id'] = array('eq',$_REQUEST['order_id']);
        }
	}
	
	/*
		@author:chf
		@function:搜索车型页面
		@time:2013-03-21
	*/
	public function index(){
		Header( "HTTP/1.1 301 Moved Permanently" );
		Header( "Location: http://www.xieche.com.cn/shopservice" );
		exit;
		$city_name = $_SESSION['area_info']['0'];
	    $model_membercar = D('Membercar');
		//得到区域ID(CityId)(方法寄存在CommonAction里)
		$this->GetArea();
		//得到区域ID(CityId)
	    if($uid = $this->GetUserId()){
		    //查询用户所有自定义车型
    		$list_membercar = $model_membercar->where("uid=$uid AND status=1")->select();
    		//用户所有自定义车型初始化
    		$list_membercar = $model_membercar->Membercar_format_by_arr($list_membercar);
		    $this->assign('uid',$uid);
		    $this->assign('list_membercar',$list_membercar);
		
		}
		$map_s['shop_city'] = $this->city_id;
		$map_s['status'] = 1;
			
		if ($this->request_int('shop_area')){
		    $map_s['shop_area'] = $this->request_int('shop_area');
			
			$this->assign('shop_area',$this->request_int('shop_area'));
		}else{
			if($_POST['area_name'] && $_POST['area_name']!='例:徐汇区'){

				$subarea = mb_substr($_POST['area_name'], -1,1, 'utf-8'); 
				if($subarea !='区'){
					$subarea = $_POST['area_name']."区";
				}else{
					$subarea = $_POST['area_name'];
				}
				$sess_area = $this->ReturnGetArea();
				
				$map_s['shop_area'] = array_search($subarea,$sess_area);
				//echo $map_s['shop_area'];
				$this->assign('shop_area',$map_s['shop_area']);

			}
		}
		
	
	    if ($this->request_int('u_c_id') || $this->request_int('model_id') || $this->request_int('fsid') || $this->request_int('brand_id') || $this->request_int('brand_id1')){
	        $model_series = D('Carseries');
    		if($this->request_int('u_c_id')){
    	        $map_m['u_c_id'] = $this->request_int('u_c_id');
    		    $membercar = $model_membercar->where($map_m)->find();
    		    $map_se['series_id'] = $membercar['series_id'];
				$brand_id = 0;
	            $this->assign('brand_id',$brand_id);
	            $this->assign('series_id',$membercar['series_id']);
	            $this->assign('model_id',$membercar['model_id']);
	            $this->assign('u_c_id',$membercar['u_c_id']);
	            $this->assign('other_car',0);
    		}else{
				 if($this->request_int('brand_id')){
					$brand_id = $this->request_int('brand_id');
					$this->assign('fsid',"");
				 }elseif ($this->request_int('fsid')){
					$map_se['fsid'] = $this->request_int('fsid');
					$this->assign('fsid',$this->request_int('fsid'));
				}
				 $map_se['brand_id'] = $brand_id;
				 $this->assign('brand_id',$brand_id);
				
			}
			if ($this->request_int('series_id') and $this->request_int('model_id')){
	            $map_se['series_id'] = $this->request_int('series_id');
	            $this->assign('series_id',$this->request_int('series_id'));
	            $this->assign('model_id',$this->request_int('model_id'));
	            $this->assign('other_car',1);
	        }
			
			elseif ($this->request_int('model_id')){
	            $map_se['model_id'] = $this->request_int('model_id');
				$this->assign('model_id',$this->request_int('model_id'));
	        }
			elseif ($this->request_int('series_id')){
	            $map_se['series_id'] = $this->request_int('series_id');
				$this->assign('series_id',$this->request_int('series_id'));
	        }
			if(!$map_se['brand_id']){
				unset($map_se['brand_id']);
			}
	        $series = $model_series->where($map_se)->select();
			
	        $fsid_arr = array();
	        if ($series){
	            foreach ($series as $_k=>$_v){
	                $fsid_arr[$_v['fsid']] = $_v['brand_id'];
	            }
	        }
	        $shopid_arr = array();
	        if ($fsid_arr){
	            $model_shop_fs_relation = D('Shop_fs_relation');
	            foreach ($fsid_arr as $kk=>$vv){
	                $map_fs['fsid'] = $kk;
	                $shop_fs_relation = $model_shop_fs_relation->where($map_fs)->select();
	                if ($shop_fs_relation){
	                    foreach ($shop_fs_relation as $_kk=>$_vv){
	                        $shopid_arr[$_vv['shopid']] = $_vv['fsid'];
	                    }
	                }
	            }
	        }
	        if ($shopid_arr){
		        $shop_id_arr = array_keys($shopid_arr);
		        $shop_id_str = implode(',',$shop_id_arr);
	        }
	        $map_s['id'] = array('in',$shop_id_str); 
		}else{
		    $this->assign('default_check',1);
		}
		
		if($this->request('shop_name') && $this->request('shop_name')!="可选填"){
			$arrshopname = $this->request('shop_name');
			$map_s['shop_name'] = array('like','%'.$arrshopname.'%');//组合要查询的条件
			
		}
		/*
		if($this->request('shop_name_1') && $this->request('shop_name_1')!="可选填"){
			$arrshopname = $this->request('shop_name_1');
			$map_s['shop_name'] = array('like','%'.$arrshopname.'%');//组合要查询的条件
		}*/
	

		$this->assign('shop_name',$arrshopname);
        $shop_model = D('Shop');
		
        $count = $shop_model->where($map_s)->count();
		$this->assign("count",$count);
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
		$shops = $shop_model->where($map_s)->order("shop_class ASC,have_coupon DESC,comment_rate DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($_SESSION['username'] == 'z'){
		//	echo  $shop_model->getlastSql();
		}
		foreach($_POST as $key => $val) {
            if (!is_array($val) && $val != "" && $key!='__hash__') {
                $p->parameter .= "$key-" . urlencode($val) . "-";
            }
        }
        // 分页显示输出
		if($_GET['p'] == '' || $_GET['p'] == '1' ) {
			$shop_p = 0;
		}else if($_GET['p'] == '2'){
			$shop_p = 1;
		}else {
			$shop_p = $_GET['p'];
		}
		
		$shop_num = ($shop_p)*20;
		$this->assign('shop_num',$p->firstRow);
        $page = $p->show();
		$model_timesale = D('Timesale');
		$model_timesaleversion = D('Timesaleversion');
		$tuijian_shop = C('TUIJIAN_SHOP');
		$tuijian_shop = array();
		$model_shop_fs_relation = D('Shop_fs_relation');
		$model_coupon = D("Coupon");
		if ($shops){
		    foreach ($shops as $k=>$v){
				//查询服务顾问
				$shops[$k]['Servcemember'] = $this->Servcemembermodel->where(array('shop_id'=>$v['id'],'state'=>'1'))->select();
				if($shops[$k]['Servcemember']){
					foreach($shops[$k]['Servcemember'] as $m_k=>$m_v){
						//总服务态度总条数
						$servicecount = $this->Commentmodel->where(array('shop_id'=>$v['id'],'servicemember_id'=>$m_v['id'],'type'=>'2'))->count();	
						//总服务态度评分
						$sumservice = $this->Commentmodel->where(array('shop_id'=>$v['id'],'servicemember_id'=>$m_v['id'],'type'=>'2'))->sum('service');
						//总服专业技能评分
						$sumprofession = $this->Commentmodel->where(array('shop_id'=>$v['id'],'servicemember_id'=>$m_v['id'],'type'=>'2'))->sum('profession');
						//总诚信态度评分
						$sumsincerity = $this->Commentmodel->where(array('shop_id'=>$v['id'],'servicemember_id'=>$m_v['id'],'type'=>'2'))->sum('sincerity');
						$shops[$k]['Servcemember'][$m_k]['service'] = number_format($sumservice/$servicecount,1);
						$shops[$k]['Servcemember'][$m_k]['profession'] = number_format($sumprofession/$servicecount,1);
						$shops[$k]['Servcemember'][$m_k]['sincerity'] = number_format($sumsincerity/$servicecount,1);
					}
				}


		        //店铺品牌
                $shops[$k]['brands'] = $this->getshopbrand($v ['id']);
				if($this->city_name=='上海'){
					if ($v['shop_class'] == 1){
						$shops[$k]['shop_pic'] = "/UPLOADS/Shop/200/".$v ['id'].".jpg";
					}else{
						$fsid = $this->getfsid($v ['id']);
						$shops[$k]['shop_pic'] = "/UPLOADS/Brand/200/".$fsid.".jpg";
					}
				
				}else{
					if (file_exists("UPLOADS/Shop/200/".$v['id'].".jpg")){
						$shops[$k]['shop_pic'] = "/UPLOADS/Shop/200/".$v['id'].".jpg";
					}else {
						$shop_id = $v['id'];
						$map_sfr['shopid'] = $shop_id;
						$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
						$shops[$k]['shop_pic'] = "/UPLOADS/Brand/200/".$shop_fs_relation['fsid'].".jpg";
					}
				
				}
				$map_c['shop_id'] = $v['id'];
				$comment_count = $this->Commentmodel->where($map_c)->count();
				$map_c['comment_type'] = 1;
				$good = $this->Commentmodel->where($map_c)->count();
				$map_c['comment_type'] = 2;
				$normal = $this->Commentmodel->where($map_c)->count();
				$map_c['comment_type'] = 3;
				$bad = $this->Commentmodel->where($map_c)->count();

				$good = round($good/$comment_count*100);
				$normal = floor($normal/$comment_count*100);
				//$bad = $bad/$count*100;
				/*ZZC要这么搞不知道逻辑*/
				

				$shops[$k]['good'] = $good;
				$shops[$k]['normal'] = $normal;
				//$shops[$k]['bad'] = round($bad);
				$bad = 100-$good-$normal;
				$shops[$k]['comment_rate_new'] = $good;



		        $map_t['xc_timesale.shop_id'] = $v['id'];
		        $map_t['xc_timesale.status'] = 1;
		        $map_t['xc_timesaleversion.status'] = 1;
		        $map_t['xc_timesaleversion.s_time'] = array('lt',time());
		        $map_t['xc_timesaleversion.e_time'] = array('gt',time());

		        $list_timesale = $model_timesale->where($map_t)->join("xc_timesaleversion ON xc_timesale.id = xc_timesaleversion.timesale_id")->select();
		        $timesale_arr = array();
		        foreach ($list_timesale as $_key=>$timesale){
	                $timesale['oldworkhours_sale'] = $timesale['workhours_sale'];
	                if (($timesale['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($timesale['e_time']<time()+3600*24) || $timesale['s_time']>(time()+24*3600*15) ){
	                    continue;
	                }
	                $timesale_arr[$timesale['timesale_id']] = $timesale;
	                if ($timesale['oldworkhours_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['oldworkhours_sale_str'] = round($timesale['oldworkhours_sale']*10,1)."折";
        		    }else {
        		        $timesale_arr[$timesale['timesale_id']]['oldworkhours_sale_str'] = "无折扣";
        		    }
        		    if ($timesale['workhours_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = round($timesale['workhours_sale']*10,1)."折";
        		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "工时费：".round($timesale['workhours_sale']*10,1)."折";
        		    }else {
        		        if ($timesale['workhours_sale'] == '-1'){
        		            $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = "全免";
            		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "工时费：全免";
        		        }else{
            		        $timesale_arr[$timesale['timesale_id']]['workhours_sale_str'] = "无折扣";
            		        $timesale_arr[$timesale['timesale_id']]['share_workhours_sale_str'] = "";
        		        }
        		    }
        		    if ($timesale['product_sale']>0){
        		        $timesale_arr[$timesale['timesale_id']]['product_sale_str'] = round($timesale['product_sale']*10,1)."折";
        		        $timesale_arr[$timesale['timesale_id']]['share_product_sale_str'] = "，零件费：".round($timesale['product_sale']*10,1)."折";
        		    }else {
        		        $timesale_arr[$timesale['timesale_id']]['product_sale_str'] = "无折扣";
        		        $timesale_arr[$timesale['timesale_id']]['share_product_sale_str'] = "";
        		    }
        			$timesale_arr[$timesale['timesale_id']]['week_name'] = explode(',',$timesale['week']);
        			foreach($timesale_arr[$timesale['timesale_id']]['week_name'] AS $kk=>$vv){
        			    if (trim($vv)=='0'){
        			        $timesale_arr[$timesale['timesale_id']]['week_name'][$kk] = '日';
        			    }
        			    $timesale_arr[$timesale['timesale_id']]['week_name_s'] .= '周'.$timesale_arr[$timesale['timesale_id']]['week_name'][$kk].',';
        			}
        			$timesale_arr[$timesale['timesale_id']]['week_name_s'] = substr($timesale_arr[$timesale['timesale_id']]['week_name_s'],0,-1);
	                //$timesale_arr[] = $timesale;
		        }
		        $shops[$k]['timesale'] = $timesale_arr;
		        if (in_array($v['id'],$tuijian_shop)){
		            $tuijian_shop_array[] = $shops[$k];
		            unset($shops[$k]);
		        }
				
				$coupon_map['shop_id'] = $v['id'];
				$coupon_map['is_delete'] = 0;
				$coupon_map['start_time'] = array('lt',time());
				$coupon_map['end_time'] = array('gt',time());
				$coupon_list = $model_coupon->where($coupon_map)->select();
				$shops[$k]['coupon_list'] = $coupon_list;
				
				if($v['shop_maps']) {
					$shoplist_lng .= $v['shop_maps']."|";
					$shoplist_name .= $v['shop_name']."|";
				}
				
				$model_region = D("Region");
				$region_info = $model_region->find($v['shop_area']);
				$shops[$k]['shop_address'] = $region_info['region_name']." ".$v['shop_address'];
				
		    }
		}
		
		$this->get_ordermenu();
		$this->get_expert_tuijian();
		//echo '<pre>';print_r($shops);exit;
		$shoplist_lng = mb_substr($shoplist_lng , 0 , -1);
		$shoplist_name = mb_substr($shoplist_name , 0 , -1);
		$this->assign('shoplist_lng',$shoplist_lng);
		$this->assign('shoplist_name',$shoplist_name);
		$this->assign('page',$page);
		$this->assign('phone',C('CALL_400'));
		$this->assign('tuijian_shop',$tuijian_shop_array);
		$this->assign('shops',$shops);
		
		/*这里开始查询跨品牌信息*/

		if($brand_id && $brand_id != 'all'){
			$ex_map['_string'].= " FIND_IN_SET('{$brand_id}', brand_id)";
		}
		if($series_id && $series_id != 'all'){
			$ex_map['_string'].= " AND FIND_IN_SET('{$series_id}', series_id)";
		}
		if($model_id && $model_id != 'all'){
			$ex_map['_string'].= " AND FIND_IN_SET('{$model_id}', model_id)";
		}
		if($this->request_int('fsid') && $this->request_int('fsid') != 'all'){
			$sql_fsid = $this->request_int('fsid');
			
			$ex_map['_string'].= "  FIND_IN_SET('{$sql_fsid}', fsid_across)";
			
		}
		
		$ex_map['coupon_across'] = '1';
		$ex_map['is_delete'] = '0';
		$ex_map['start_time'] = array('lt',time());
		$ex_map['end_time'] = array('gt',time());
		
		$ex_coupon = $this->CouponModel->where($ex_map)->limit(5)->order('rand()')->select();
		//echo $this->CouponModel->getLastsql();
		$this->assign('ex_coupon',$ex_coupon);
			
		if(TOP_CSS == "pa") {
			$this->assign('title',"平安好车-携车网-汽车保养,事故车维修,全国唯一4S店售后折扣网站");
		}else {
		
			if($map_s['shop_area']){
				$title_region = $this->Regionmodel->where(array('id'=>$map_s['shop_area']))->find();
				$region_name = $title_region['region_name'];
				$region_name;
			}

			if($this->request_int('fsid')){
				 $title_Fs	= $this->Fsmodel->where(array('fsid'=>$this->request_int('fsid')))->find();
				 $title_Fs_str = $title_Fs['fsname'];
			}
			$last_regionname = $region_name.$title_Fs_str;
		}
		
		if(!$map_s['shop_area'] && !$this->request_int('fsid')){
			$this->assign('title',"4s店售后保养事故车维修预约-携车网");
			$this->assign('meta_keyword',"汽车保养,汽车维修,4S店保养预约,4S店维修预约,事故车维修");
			$this->assign('description',"汽车4S店售后保养预约,事故车维修首选携车网,在线预约 ,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
		}else{
			$this->assign('title',$last_regionname."4s店售后保养事故车维修预约-携车网");
			$this->assign('meta_keyword',$last_regionname."4S店保养预约,".$last_regionname."汽车保养,".$last_regionname."汽车维修,".$last_regionname."4S店维修预约,".$last_regionname."事故车维修");
			$this->assign('description',$last_regionname."汽车4S店售后保养预约,事故车维修首选携车网,在线预约 ,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
		}


	    Cookie::set('_currentUrl_', __URL__);
		$this->assign('brand_id',$brand_id);
		$this->assign('series_id',$this->request_int('series_id'));
		$this->assign('model_id',$this->request_int('model_id'));
		
		$this->assign('uid',$_SESSION['uid']);
		if($_GET['old']==1) {
			$this->display();
		}else {
			$this->display('index_new');
		}
	}

    public function getshopbrand($shop_id){
        $model_shop_fs_relation = D('Shop_fs_relation');
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
        $brand_id_arr = array();
        if ($shop_fs_relation){
            $model_carseries = D('Carseries');
            foreach ($shop_fs_relation as $k=>$v){
                $map_s['fsid'] = $v['fsid'];
                $carseries = $model_carseries->where($map_s)->select();
                if ($carseries){
                    foreach ($carseries as $_k=>$_v){
                        $brand_id_arr[$_v['brand_id']] = $_v['brand_id'];
                    }
                }
            }
        }
        $model_brand = D('Carbrand');
        $map_b['brand_id'] = array('in',implode(',',$brand_id_arr));
        $brand = $model_brand->where($map_b)->select();
        return $brand;
    }

    public function getfsid($shop_id){
        $model_shop_fs_relation = D('Shop_fs_relation');
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
        if ($shop_fs_relation){
            $model_carseries = D('Carseries');
            foreach ($shop_fs_relation as $k=>$v){
                if ($v['fsid']){
                    return $v['fsid'];
                }
            }
        }
    }


	/*
		@author:chf
		@function:显示预约页面
		@time:2013-03-22
	*/
	public function yuyue(){
	    Cookie::set('_currentUrl_', __SELF__);
	    $model_serviceitem = D('Serviceitem');
	    $list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$this->assign('list_si_level_0',$list_si_level_0);
		$this->assign('list_si_level_1',$list_si_level_1);
		$timesaleversion_id = $_REQUEST['timesaleversion_id'];
		$model_id = $_REQUEST['model_id'];
		$this->assign('shop_area',$_REQUEST['shop_area']);
		$this->assign('brand_id',$_REQUEST['brand_id']);
		//添加得到区域ID(CityId)
		$this->GetArea();
		//得到区域ID(CityId)
		$uid = $this->GetUserId();
		/*@author:chf @查询店铺名字 新添加*/
		$shop_info = $this->ShopModel->where(array('id'=>$this->request_int('shop_id')))->find();
		//优惠券下订
		if ($_REQUEST['membercoupon_id']){
		    $model_membercoupon = D('Membercoupon');
		    $map_mc['membercoupon_id'] = $_REQUEST['membercoupon_id'];
		    $map_mc['uid'] = $uid;
		    $map_mc['is_pay'] = 1;
		    $membercoupon = $model_membercoupon->where($map_mc)->find();
		    if ($membercoupon){
		        if ($membercoupon['is_use']==1){
		            $this->error('优惠券已使用！','__APP__/coupon');
		        }
		        if ($membercoupon['end_time']<time()){
		            $this->error('优惠券已过期！','__APP__/coupon');
		        }
    		    $model_coupon = D('Coupon');
    		    $map_c['id'] = $membercoupon['coupon_id'];
    		    $coupon = $model_coupon->where($map_c)->find();
    		    $model_id = $coupon['model_id'];
    		    $service_ids = $coupon['service_ids'];
    		    $shop_id = $coupon['shop_id'];
    		    $map_s['id'] = array('in',$service_ids);
    		    $serviceitem = $model_serviceitem->where($map_s)->select();
    		    
    		    $sale_check = sale_check($coupon['week']);  //根据分时折扣的星期数，处理无效日期
    			$min_hours = explode(':',$coupon['s_time']);   //分时具体上下午时间输出到模板，做判断
    			$max_hours = explode(':',$coupon['e_time']);     //分时具体上下午时间输出到模板，做判断
    			$now = time();
    		    $fourhour = strtotime(date('Y-m-d').' 16:00:00');
    		    
    		    if ($now < $fourhour){
    		        $min = 1;
    		        $max = 15;
    		    }else{
    		        $min = 2;
    		        $max = 16;
    		    }
    		    if(($coupon['start_time'] - strtotime(date('Y-m-d')))>0){
    		        $s_day = floor(($coupon['start_time'] - strtotime(date('Y-m-d')))/24/3600);
    		        $min = max($s_day,$min);
    		    }
    		    if(($coupon['end_time'] - strtotime(date('Y-m-d')))>0){
    		        $e_day = floor(($coupon['end_time'] - strtotime(date('Y-m-d')))/24/3600);
    		        $max = min($e_day,$max);
    		    }
    			$minday = "%y-%M-{%d+".$min."}";
    			$maxday = "%y-%M-{%d+".$max."}";
    			$this->assign("minday",$minday);
    			$this->assign("maxday",$maxday);
    		    $this->assign('membercoupon_id',$_REQUEST['membercoupon_id']);
    			$this->assign("serviceitem",$serviceitem);
    			$this->assign("coupon",$coupon);
		    }else {
		        $this->error('优惠券ID无效！','__APP__/coupon');
		    }
		}
		//获取分时折扣id
		if($timesaleversion_id){
			$model_timesale = D('Timesale'); //载入模型
			$map_ts['xc_timesaleversion.id'] = $timesaleversion_id;
			$list_timesale = $model_timesale->where($map_ts)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find(); //根据id查询分时折扣信息
			$shop_id = $list_timesale['shop_id'];
			$model_shop = D('Shop');
			$map_shop['id'] = $shop_id;
			$shop = $model_shop->where($map_shop)->find();
			$this->assign('shop',$shop);
			$sale_check = sale_check($list_timesale['week']);  //根据分时折扣的星期数，处理无效日期
			$min_hours = explode(':',$list_timesale['begin_time']);   //分时具体上下午时间输出到模板，做判断
			$max_hours = explode(':',$list_timesale['end_time']);     //分时具体上下午时间输出到模板，做判断
			$now = time();
		    $fourhour = strtotime(date('Y-m-d').' 16:00:00');
		    if ($now < $fourhour){
		        $min = 1;
		        $max = 15;
		    }else{
		        $min = 2;
		        $max = 16;
		    }
		    if(($list_timesale['s_time'] - strtotime(date('Y-m-d')))>0){
		        $s_day = floor(($list_timesale['s_time'] - strtotime(date('Y-m-d')))/24/3600);
		        $min = max($s_day,$min);
		    }
		    if(($list_timesale['e_time'] - strtotime(date('Y-m-d')))>0){
		        $e_day = floor(($list_timesale['e_time'] - strtotime(date('Y-m-d')))/24/3600);
		        $max = min($e_day,$max);
		    }


			if ($list_timesale['workhours_sale']>0){
				$salesversion['workhours_sale'] = round($list_timesale['workhours_sale']*10,1)."折";
			}else if($list_timesale['workhours_sale'] == '-1') {
				$salesversion['workhours_sale'] = "全免";
			}else{
				$salesversion['workhours_sale'] = "无折扣";
			}
			
			if ($list_timesale['product_sale']>0){
				$salesversion['product_sale'] = round($list_timesale['product_sale']*10,1)."折";
			}else {
				$salesversion['product_sale'] = "无折扣";
			}


			$minday = "%y-%M-{%d+".$min."}";
			$maxday = "%y-%M-{%d+".$max."}";
			$this->assign("minday",$minday);
			$this->assign("maxday",$maxday);
		}
		$doubleCalendar = double_or_single_Calendar(); //单双月显示判断
		
		if ($uid){
    		$model_member = D('Member');
    		$map_m['uid'] = $uid;
    		$member = $model_member->where($map_m)->find();
		    $this->assign('member',$member);
		}
		$this->get_ordermenu();
		$this->get_expert_tuijian();

		$model_carmodel = D('Carmodel');
		$model_carseries = D('Carseries');
		$model_carbrand = D('Carbrand');

		if ($model_id || $_REQUEST['u_c_id']){
			
			if($_REQUEST['u_c_id']) {
				$model_membercar = D("membercar");
				$membercar = $model_membercar->find($_REQUEST['u_c_id']);
				
				$car_number_arr = explode('_',$membercar['car_number']);
				$membercar['s_pro'] = $car_number_arr[0];
				$membercar['licenseplate'] = $car_number_arr[1];
				$this->assign("membercar" , $membercar);

				$model_id = $membercar['model_id'];
			}

		    $carname = array();
		   
		    $map_cm['model_id'] = $model_id;
		    $carmodel = $model_carmodel->where($map_cm)->find();
		    $carname['model_name'] = $carmodel['model_name'];
		    $series_id = $carmodel['series_id'];
		    
		    $map_cs['series_id'] = $series_id;
		    $carseries = $model_carseries->where($map_cs)->find();
		    $carname['series_name'] = $carseries['series_name'];
		    $brand_id = $carseries['brand_id'];
		   
		    $map_cb['brand_id'] = $brand_id;
		    $carbrand = $model_carbrand->where($map_cb)->find();
		    $carname['brand_name'] = $carbrand['brand_name'];
		    $this->assign('carname',$carname);
		}else{
			$model_shop_fs_relation = D('shop_fs_relation');
			$fsid_info = $model_shop_fs_relation->where(array("shopid"=>$_REQUEST['shop_id']))->select();
			if($fsid_info) {
				foreach($fsid_info as $key=>$val) {
					$fsid_arr[] = $val['fsid'];
				}
			}
			$map_series['fsid'] = array('in',$fsid_arr);
			$series_info = $model_carseries->where($map_series)->group('brand_id')->select();
			if($series_info) {
				foreach($series_info as $key=>$val) {
					$brand_ids_arr[] = $val['brand_id'];
				}
			}
			$map_brand['brand_id'] = array('in',$brand_ids_arr);
			$brand_info = $model_carbrand->where($map_brand)->select();
			$this->assign('brand_arr',$brand_info);
		}

		/*取得拥有抵用券信息*/
		if($_SESSION['uid']){
			//echo $_SESSION['uid'];
			$arr4['uid'] = $_SESSION['uid'];
			$arr4['is_use'] = '0';
			$arr4['start_time'] = array('gt',time());
			$arr4['end_time'] = array('lt',time());
			$arr4['salecoupon_id'] = array('in','1,2,3,5');
			$arr4['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
			$sale_count = $this->MembersalecouponModel->where($arr4)->count();
			
			$this->MembersalecouponModel->where($arr3)->count();
			$salecoupon_map['is_delete'] = '0';
			$salecoupon_map['uid'] = $_SESSION['uid'];
			$salecoupon_map['order_id'] = '0';
			$salecoupon_map['start_time'] = array('gt',time());
			$salecoupon_map['end_time'] = array('lt',time());
			$salecoupon_map['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";	
			$salecoupon = $this->MembersalecouponModel->where($salecoupon_map)->select();
			$arr4['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";	
			$Salecoupon_count= $this->Salecouponmodel->where($arr4)->count();//判断是否有车惠抵用券参加的店铺
			$this->assign('sale_count',$sale_count);
			$this->assign('Salecoupon_count',$Salecoupon_count);
			$this->assign('salelmm_count',$salelmm_count);
			$this->assign('salech_count',$salech_count);
			$this->assign('salecoupon',$salecoupon);
		}
	
		
		/*服务顾问查询*/
		$Servicemember = $this->Servcemembermodel->where(array('shop_id'=>$_REQUEST['shop_id'],'state'=>'1'))->order('id DESC')->limit('0,3')->select();
		$this->assign('Servicemember',$Servicemember);
		$this->assign('sess_uid',$_SESSION['uid']);
		$this->assign('timesaleversion_id',$timesaleversion_id);
		$this->assign('model_id',$model_id);
		$this->assign('u_c_id',$_REQUEST['u_c_id']);
		$this->assign('shop_id',$shop_id);
		$this->assign('min_hours',$min_hours[0]);
		$this->assign('min_minute',$min_hours[1]);
		$this->assign('max_hours',$max_hours[0]);
		$this->assign('max_minute',$max_hours[1]);
		$this->assign('sale_check',$sale_check);
		$this->assign('doubleCalendar',$doubleCalendar);
		
		$this->assign('title',"4S店售后保养维修预约,更换制动盘,制动盘,火花塞,正时皮带,防冻液,机油滤-携车网");
		$this->assign('meta_keyword',"汽车保养，汽车维修，汽车保养预约，汽车维修预约");
		$this->assign('description',"4S店售后保养更换更换制动盘,制动盘,火花塞,正时皮带,防冻液,机油滤就选携程网，还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822");
		if(TOP_CSS == "pa") {
			$this->assign('title',"平安好车-携车网-汽车保养,事故车维修,全国唯一4S店售后折扣网站");
		}else {
			//这seo一天一个花头精
			/*
			if($_SESSION['SEO'] == "result"){
				$this->assign('title',$shop['shop_name']."-".$carname['brand_name']." ".$carname['series_name']." ".$carname['model_name']."-常规保养优惠-预约下单-4S店售后预约-携车网");
				$_SESSION['SEO'] =  "";
			}else 
			*/
			$title = "售后保养维修预约,更换制动盘,制动盘,火花塞,正时皮带,防冻液,机油滤-携车网";
			if($_GET['shop_id'] and !$_GET['model_id']){
				$this->assign('title',$shop['shop_name'].'汽车4S店'.$title);
				$this->assign('meta_keyword',$shop['shop_name']."汽车保养,".$shop['shop_name']."汽车维修,".$shop['shop_name']."汽车保养预约,".$shop['shop_name']."汽车维修预约");
				$this->assign('description',$shop['shop_name']."汽车4S店售后保养更换更换制动盘,制动盘,火花塞,正时皮带,防冻液,机油滤就选携程网,还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822");
			}else{
				$this->assign('title',$carname['brand_name'].$carname['series_name'].$carname['model_name'].$title);
				$this->assign('meta_keyword',$carname['brand_name'].$carname['model_name']."汽车保养,".$carname['brand_name'].$carname['model_name']."汽车维修,".$carname['brand_name'].$carname['model_name']."汽车保养预约,".$carname['brand_name'].$carname['model_name']."汽车维修预约");
				$this->assign('description',$carname['brand_name'].$carname['series_name'].$carname['model_name']."售后保养更换更换制动盘,制动盘,火花塞,正时皮带,防冻液,机油滤就选携程网，还有更多汽车保养维修团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822");
			}
			/*if($carname) {
				$this->assign('title',$shop['shop_name']."-".$carname['brand_name']." ".$carname['series_name']." ".$carname['model_name']."-预约下单-4S店售后预约-携车网");
				if(strpos($_SERVER['REQUEST_URI'],"u_c_id-")) {
					$canonical = "http://www.xieche.com.cn/order/yuyue-timesaleversion_id-{$timesaleversion_id}-shop_id-{$shop_id}-model_id-{$model_id}.html";
					$this->assign('canonical',$canonical);
				}
			}else {
				$time_str = $min_hours[0].":".$min_hours[1]."-".$max_hours[0].":".$max_hours[1];
				$this->assign('title',$shop['shop_name'].$time_str."|工时折扣率".$salesversion['workhours_sale']."|零件折扣率".$salesversion['product_sale']."-预约下单-携车网");
				if(strpos($_SERVER['REQUEST_URI'],"model_id--u_c_id--")) {
					$canonical = "http://www.xieche.com.cn/order/yuyue-timesaleversion_id-{$timesaleversion_id}-shop_id-{$shop_id}.html";
					$this->assign('canonical',$canonical);
				}
			}*/
		}
		$this->get_tuijian_coupon($this->request_int('shop_id'),$model_id);

	    $this->display('bespeak');
	}

    /*
		@author:chf
		@function:验证编码是否有效
		@time:2013-11-10
	*/
	function AjaxGetcode(){
		$uid = $_SESSION['uid'];
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		$code = $_REQUEST['code'];
		$carcode = $this->CarcodeModel->where(array('status'=>'1','coupon_code'=>$code))->count();//随机找个已经生成的车会验证码
		if($carcode>0){
			echo "fail";
			exit;
		}
		$carcode = $this->CarcodeModel->where(array('status'=>'0','coupon_code'=>$code))->count();//随机找个已经生成的车会验证码
		if($carcode==0){
			echo "noway";
			exit;
		}else{
			$mobile = $member['mobile'];
			$membersalecouponcount = $this->MembersalecouponModel->where(array('mobile'=>$member['mobile'],'is_use'=>'0','salecoupon_id'=>'3'))->count();
			if($membersalecouponcount==0){
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
				
				$verify_str = "您的抵用券已绑定到您的账户.请通过携车网预约后凭消费码:".$code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服电话4006602822";

				//130  131  132 155 156  186  185 联通短信多发内容
				$submobile = substr($mobile,0,3);
				$datamobile = array('130','131','132','155');
				if(in_array($submobile,$datamobile)){
					$verify_str = $verify_str."回复TD退订";
				}
				//130  131  132 155 156  186  185 联通短信多发内容
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$this->curl_sms($send_verify);
				
				$send_verify['sendtime'] = time();
				$this->Smsmodel->add($send_verify);
				$this->CarcodeModel->where(array('coupon_code'=>$code))->save(array('status'=>'1'));
				
				//$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('is_use'=>1,'use_time'=>$nowtime));
			}else{
				echo "again";
				exit;
			}
			echo $salecoupon['coupon_name']."|".$code."|".date('Y-m-t H:i:s',$salecoupon['start_time'])."|".date('Y-m-t H:i:s',$salecoupon['end_time']);
		}

	}


	public function get_price(){
	    $timesaleversion_id = $_REQUEST['timesaleversion_id'];
	    $model_id = $_REQUEST['model_id'];
	    $servers_str = $_REQUEST['str'];
	    if ($timesaleversion_id and $model_id and $servers_str){
	        $model_timesaleversion = D('Timesaleversion');
	        $map_t['id'] = $timesaleversion_id;
	        $map_t['status'] = 1;
	        $timesaleversion = $model_timesaleversion->where($map_t)->find();
	        if ($timesaleversion['workhours_sale'] == '0.00'){
	            $timesaleversion['workhours_sale'] = 1;
	        }
	        if ($timesaleversion['product_sale'] == '0.00'){
	            $timesaleversion['product_sale'] = 1;
	        }
	        $servers_str = substr($servers_str,0,-1);
	        $map['service_id']  = array('in',$servers_str);
    		$map['model_id']  = array('eq',$model_id);
    		$product_model = D('Product');
    		$model_serviceitem = D('Serviceitem');
    		$map_s['id'] = array('in',$servers_str);
    		$serviceitem = $model_serviceitem->where($map_s)->select();
    		$servicename_arr = array();
    		if ($serviceitem){
    		    foreach ($serviceitem as $key=>$val){
    		        $servicename_arr[] = $val['name'];
    		    }
    		}
    		$servicename_str = implode('<br/>',$servicename_arr);
    		$model_productversion = D('Productversion');
    		$list = $product_model->where($map)->select();
			//echo $product_model->getlastSql();
    		if ($list){
    		    foreach ($list as $k=>$v){
    		        //$map_pv['id'] = $v['versionid'];  刷数据好像有点问题 暂时用下面的查询
					$map_pv['product_id'] = $v['id'];
    		        $map_pv['status'] = 0;
    		        $productversion = $model_productversion->where($map_pv)->find();
    		        $list[$k]['product_detail'] = $productversion['product_detail'];
    		        $product_detail = unserialize($productversion['product_detail']);
    		        if ($product_detail){
    		            foreach ($product_detail as $_k=>$_v){
    		                $product_detail[$_k]['total'] = $product_detail[$_k]['quantity']*$product_detail[$_k]['price'];
    		                $all_total +=$product_detail[$_k]['total'];
    		                if ($product_detail[$_k]['Midl_name'] == '工时费'){
    		                    $product_detail[$_k]['after_sale_total'] = $product_detail[$_k]['total']*$timesaleversion['workhours_sale'];
    		                    $product_price += $product_detail[$_k]['total'];
        						$product_price_sale += $product_detail[$_k]['after_sale_total'];
    		                }else{
    		                    $product_detail[$_k]['after_sale_total'] = $product_detail[$_k]['total']*$timesaleversion['product_sale'];
    							$workhours_price += $product_detail[$_k]['total'];
    							$workhours_price_sale += $product_detail[$_k]['after_sale_total'];
    		                }
    		                $all_after_total += $product_detail[$_k]['after_sale_total'];
    		            }
    		        }
    		    }
    		    //零件配节省费用
				$product_price_save = $product_price-$product_price_sale;
				//零件配节省费用
				$workhours_price_save = $workhours_price-$workhours_price_sale;
				$save_total = $all_total-$all_after_total;
			
				$product_info = $this->ajax_get_product_info($model_id,$val['id'],$servers_str,$timesaleversion['workhours_sale'],$timesaleversion['product_sale'],$timesaleversion['id'],$timesaleversion['memo'],$timesaleversion['coupon_id']);
				$url = $product_info['pic_path'];
				//echo "<td width=176>".$all_total." </td><td width=176 ".$all_after_total."</td><td width=176>".$save_total."<a href='###' onclick='get_detail(\"".$url."\");'>查看详情</a></td>";
				echo $all_total."|".$all_after_total."|".$save_total."|<a style='margin-left:5px;' href='###' onclick='get_detail(\"".$url."\");'>查看</a>|".$url;
				exit;
    		}else{
    		    echo "店铺价格未更新";
				exit;
    		}
	    }
	}

	public function price_detail(){
	    $url = $_REQUEST['url'];
	    $this->assign('url',$url);
	    $this->display();
	}
    public function appindex(){
	    if($this->isPost()){
	        $_SESSION['s_data'] = $_POST;
	        unset($_POST['__hash__']);
	        $url_data_encode = urlencode(serialize($_POST));
	        $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
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
	            $this->assign('other_car',$_POST['other_car']);
	        }
	        if (isset($_POST['u_c_id'])){
	             $this->assign('u_c_id',$_POST['u_c_id']); 
	        }
	    }
    	    
	    //载入用户自定义车型MODEL
		$model_membercar = D('Membercar');	
		//获取用户ID
		if($uid = $this->appGetUserId()){
		    //查询用户所有自定义车型
    		$list_membercar = $model_membercar->where("uid=$uid AND status=1")->select();
    		//dump($list_membercar);
    		//用户所有自定义车型初始化
    		$list_membercar = $model_membercar->Membercar_format_by_arr($list_membercar);
		    //$this->assign('uid',$uid);
		    //$this->assign('list_membercar',$list_membercar);
		}
		//载入服务项目MODEL
		$model_serviceitem = D('serviceitem');
		//获取所有的服务分类
		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		if ($this->isPost()){
		    $model_id = $this->get_model_id($_POST);
		}elseif ($_SESSION['s_data']) {
		    $model_id = $this->get_model_id($_SESSION['s_data']);
		}
		//echo '<pre>';print_r($model_id);exit;
		if($model_id){
			$select_car = $model_membercar->Membercar_format_by_model_id($model_id['model_id']);
			$this->assign('select_car',$select_car);
			$this->assign('select_brand_id',$model_id['brand_id']);
			$this->assign('select_model_id',$model_id['model_id']);
			$this->assign('select_series_id',$model_id['series_id']);
			
		}
		if($this->isPost()){
			if (isset($_POST['select_services']) and $_POST['select_services']){
    			$select_services_str = implode(',', $_POST['select_services']);
    			$this->assign('select_services',$_POST['select_services']);
    			$this->assign('select_services_str',$select_services_str);
			}else {
    			$this->assign('select_services_str',0);
			}
		}
		
        $shop_model = D('Shop');
		if ($_GET['id']){//分页
		    unset($map);
		    $map['id'] = array('in',$_GET['id']);
		    $map['status'] = 1;
		    //$list_product = $this->_list($shop_model,$map);
		    //$list_product = $shop_model->where($map)->limit(10)->select();
		}else {
		    $model_carseries = D('Carseries');
    		if (isset($model_id['series_id']) and $model_id['series_id']){
    		    $carseriesinfo = $model_carseries->find($model_id['series_id']);
    		    $fsid = $carseriesinfo['fsid'];
    		    $model_shop_fs_relation = D('Shop_fs_relation');
    		    $relation_shopids = $model_shop_fs_relation->where("fsid=$fsid")->select();
    		    $shopid_arr = array();
    		    if (!empty($relation_shopids)){
    		        foreach ($relation_shopids as $shopid_v){
    		            $shopid_arr[] = $shopid_v['shopid'];
    		        }
    		    }
    		    if (!empty($shopid_arr)){
    		        $condition['id'] = array('in',implode(',',$shopid_arr));
    		        $condition['status'] = 1;
    		        //$list_product = $this->_list($shop_model,$condition);
    		        $list_product = $shop_model->where($condition)->limit(10)->select();
    		    }
    		}
		}
		//echo $shop_model->getlastsql();
		//echo '<pre>';print_r($list_product);
		if (!empty($list_product)){
		    $timesale_model = D('Timesale');
		    $list_timesale_arr = array();
		    $model_region = D('Region');
		    $model_coupon = D('Coupon');
		    foreach ($list_product as $key=>$val){
		        $area_info = $model_region->find($val['shop_area']);
		        $list_product[$key]['area_name'] = $area_info['region_name'];
		        if ($val['logo'] and file_exists("UPLOADS/Shop/Logo/".$val['logo'])){
		            $list_product[$key]['have_logo'] = 1;
		        }else {
		            $list_product[$key]['have_logo'] = 0;
		        }
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
		                if ($timesale['coupon_id']){
		                    $coupon = $model_coupon->find($timesale['coupon_id']);
		                    if ($coupon['is_delete']=='0'){
		                        if ($coupon['coupon_discount']>0){
		                            $timesale['workhours_sale'] = max($timesale['workhours_sale']-$coupon['coupon_discount'],0);
		                            if ($timesale['workhours_sale'] == 0){
		                                $timesale['workhours_sale'] = '-1';
		                            }
		                        }elseif ($coupon['coupon_amount']>0){
		                            $coupon_amount = $coupon['coupon_amount'];
		                        }
		                        //优惠券优惠服务ID
		                        $services_str = $this->get_coupon_serverids($coupon['service_ids'],$select_services_str);
		                        $not_coupon_services_str = $this->get_not_coupon_serverids($coupon['service_ids'],$select_services_str);
		                        $timesale['coupon_url'] = $coupon['coupon_url'];
		                        $timesale['coupon_str'] = ',并使用了“'.$coupon['coupon_name'].'”优惠劵';
		                    }
		                }else {
		                    $services_str = $select_services_str;
            			    $timesale['coupon_url'] = '';
            			    $timesale['coupon_str'] = '';
		                }
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
            		            $timesale_arr[$timesale['timesale_id']][$k]['workhours_sale_str'] = "全免";
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
            			    if (trim($vv)=='0'){
            			        $timesale_arr[$timesale['timesale_id']][$k]['week_name'][$kk] = '日';
            			    }
            			    $timesale_arr[$timesale['timesale_id']][$k]['week_name_s'] .= '周'.$timesale_arr[$timesale['timesale_id']][$k]['week_name'][$kk].',';
            			}
            			$timesale_arr[$timesale['timesale_id']][$k]['week_name_s'] = substr($timesale_arr[$timesale['timesale_id']][$k]['week_name_s'],0,-1);
            			if ($services_str !=''){
            			    $product_info = $this->ajax_get_product_info($model_id['model_id'],$val['id'],$services_str,$timesale['workhours_sale'],$timesale['product_sale'],$timesale['id'],$timesale['memo'],$timesale['coupon_id'],$coupon_amount);
            			    $list_product[$key]['product_info'][$timesale['id']] = $product_info['pic_path'];
            			    $timesale_arr[$timesale['timesale_id']][$k]['savemoney'] = $product_info['savemoney'];
            			    if ($product_info['savemoney']=='0'){
            			        $have_savemoney = 0;
            			    }else{
            			        $have_savemoney = 1;
            			    }
            			}
            			$timesale_arr[$timesale['timesale_id']][$k]['services_str'] = $services_str;
            			$timesale_arr[$timesale['timesale_id']][$k]['not_coupon_services_str'] = $not_coupon_services_str;
            			$timesale_arr[$timesale['timesale_id']][$k]['wb_url'] = 'url_data/'.$url_data_encode.'/shopid/'.$val['id'].'/timesaleid/'.$timesale['id'];
            			$timesale_arr[$timesale['timesale_id']][$k]['s_wb_url'] = '/s_url/'.sha1($timesale_arr[$timesale['timesale_id']][$k]['wb_url']);
		                //$list_product[$key]['getShop'] = $shop_model->where("id = $_POST[shop_id]")->find();
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
		$this->assign('list_product',$list_product);
		$this->assign('allcount',count($list_product));
		$this->assign('have_savemoney',$have_savemoney);
		$this->assign('list_timesale_arr',$list_timesale_arr);
	    Cookie::set('_currentUrl_', __URL__);
		$this->display();
	}
	
	public function timesale($shop_arr){
		$week = date(N);
		//return $week;
		$map['week'] = array('eq',$week);
		return date('Y-m-d');
		//$model_timesale = D('Timesale');
		//$list = $model_timesale->
	}
	//获取model_id
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
	
    public function code_help(){
	    $this->display('codehelp');
	}
	
	/*
		@author:chf
		@function:预约订单
		@time:2013-03-26
	*/
	public function insert(){
	    $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	    if (empty($_POST['xcagreement'])){
		    $this->error('请勾选同意携车网维修保养预约协议！');
		}
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
		$CityId = $this->GetCityId($_SESSION['area_info'][0]);//得到城市ID
		//根据提交过来的预约时间，做判断(暂时先注销)
		if($_POST['order_date']){
    		//载入产品MODEL
    		$model_product = D('Product');
    		//$map['product_id'] = array('in',$_REQUEST['product_str']);
		    if ($_REQUEST['select_services']){
    		    $select_services = implode(',',$_REQUEST['select_services']);
    		}else {
    		    $select_services = '';
    		}
    		$uid = $this->GetUserId();
    		$order_time= $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
    		$order_time = strtotime($order_time);

			$now = time();
		    $fourhour = strtotime(date('Y-m-d').' 16:00:00');		    
		    if ($now < $fourhour){
		        $min = 1;
		        $max = 15;
		    }else{
		        $min = 2;
		        $max = 16;
		    }
			if($order_time > (time()+86400*$max) ) {				
				$this->error('最多预约15天以内,请重新选择！');
			}
		    if(!$u_c_id = $_POST['u_c_id']){
    			$u_c_id = 0;
    		}
    		$save_discount = 0.00;
    		$productversion_ids_str = '';
    		if ($_REQUEST['membercoupon_id']){
    		    $membercoupon_id = $_REQUEST['membercoupon_id'];
    		    $model_membercoupon = D('Membercoupon');
    		    $map_mc['membercoupon_id'] = $_REQUEST['membercoupon_id'];
    		    $membercoupon = $model_membercoupon->where($map_mc)->find();
    		    $coupon_id = $membercoupon['coupon_id'];
    		    $model_coupon = D('Coupon');
    		    $map_c['id'] = $coupon_id;
    		    $coupon = $model_coupon->where($map_c)->find();
    		    $model_id = $coupon['model_id'];
    		    $select_services = $coupon['service_ids'];
    		    $shop_id = $coupon['shop_id'];
    		    $total_price = $coupon['coupon_amount'];
    		    $cost_price = $coupon['cost_price'];
    		    $jiesuan_money = $coupon['jiesuan_money'];
    		    $save_price = $cost_price - $total_price;
    		    $order_type = $coupon['coupon_type'];
    		    $data['order_name'] = $coupon['coupon_name'];
    		    $data['order_des'] = $coupon['coupon_summary'];
    		}else{
    		    $order_type = 0;
        		if ($select_services){
            		$map['service_id'] = array('in',$select_services);
            		$map['model_id'] = array('eq',$_REQUEST['model_id']);
            		$list_product = $model_product->where($map)->select();
        		}
    		
        		$timesale_model = D('Timesale');
        		$map_tsv['xc_timesaleversion.id'] = $_POST['timesaleversion_id'];
        		$sale_arr = $timesale_model->where($map_tsv)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find();
        		if ($order_time>$sale_arr['s_time'] and $order_time<$sale_arr['e_time']){
        		    $order_week = date("w",$order_time);
        		    $normal_week = explode(',',$sale_arr['week']);
        		    if (!in_array($order_week,$normal_week)){
        		        $this->error('预约时间错误,请重新选择！');
        		    }
        		    $order_hour = date("H:i",$order_time);
        		    if (strtotime(date('Y-m-d').' '.$order_hour)<strtotime(date('Y-m-d').' '.$sale_arr['begin_time']) || strtotime(date('Y-m-d').' '.$order_hour)>strtotime(date('Y-m-d').' '.$sale_arr['end_time'])){
        		        $this->error('预约时间错误,请重新选择！');
        		    }
        		}else {
        		    $this->error('预约时间错误,请重新选择！');
        		}
        		//echo '<pre>';print_r($sale_arr);exit;
    		    
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
        		$cost_price = $total_workhours_price+$total_product_price;
        		$jiesuan_money = 0;
        		$productversion_ids_str = implode(",",$productversion_ids_arr);
        		
        		$total_price = 0;
        		$save_price = 0;
        		
        		if (!empty($sale_arr)){
        		    if ($sale_arr['product_sale']>0){
        		        $total_price += $total_product_price*$sale_arr['product_sale'];
        		    }else {
        		        $total_price += $total_product_price;
        		    }
        		    /*if ($sale_arr['workhours_sale']=='0.00'){
        		        $sale_arr['workhours_sale'] = 1;
        		    }*/
        		    $workhours_sale = $sale_arr['workhours_sale'];
        		    if ($workhours_sale>0){
        		        $total_price += $total_workhours_price*$workhours_sale;
        		        $save_price = $total_workhours_price*($sale_arr['workhours_sale']-$workhours_sale);
        		        $save_discount = $sale_arr['workhours_sale']-$workhours_sale;
        		    }else{
        		        $total_price += $total_workhours_price*0;
        		        $save_price = $total_workhours_price;
        		        $save_discount = $sale_arr['workhours_sale'];
        		    }
        		}else {
        		    $total_price += $total_product_price+$total_workhours_price;
        		}
        		$membercoupon_id = 0;
        		$coupon_id = 0;
    		}
    		if ($sale_arr['product_sale']){
    		    $product_sale = $sale_arr['product_sale'];
    		}else{
    		    $product_sale = 0;
    		}
    		if ($sale_arr['workhours_sale']){
    		    $workhours_sale = $sale_arr['workhours_sale'];
    		}else{
    		    $workhours_sale = 0;
    		}
    		if ($_REQUEST['member_id']){
    		    $data['member_id'] = $_REQUEST['member_id'];
    		}
			$data['u_c_id']=$u_c_id;
			$data['uid']=$uid;
			$data['shop_id']=$_POST['shop_id'];
			$data['model_id']=$_POST['model_id'];
			$data['timesaleversion_id']=$_POST['timesaleversion_id'];
			$data['service_ids']=$select_services;
			$data['product_sale']=$product_sale;
			$data['workhours_sale']=$workhours_sale;
			$data['truename']=$_POST['truename'];
			$data['mobile']=$_POST['mobile'];
			$data['licenseplate']=trim($_POST['cardqz'].$_POST['licenseplate']);
			$data['mileage']=$_POST['miles'];
			$data['car_sn']=$_POST['car_sn'];
			$data['remark']=$_POST['remark'];
			$data['order_time']=$order_time;
			$data['create_time'] = time();
		    $data['total_price']=$total_price;
		    $data['cost_price']=$cost_price;
		    $data['jiesuan_money']=$jiesuan_money;
		    $data['productversion_ids']=$productversion_ids_str;
		    $data['coupon_save_money']=$save_price;
		    $data['coupon_save_discount']=$save_discount;
		    $data['membercoupon_id']=$membercoupon_id;
		    $data['coupon_id']=$coupon_id;
		    $data['order_type']=$order_type;
			$data['city_id'] = $CityId;
			
			if($_REQUEST['ra_servicemember_id']){
				$data['servicemember_id'] = $_REQUEST['ra_servicemember_id'];
			}
			/*
				@得到是否是百度过来的用户
			*/
			if($_COOKIE["Baidu_id"] > 0 ){
				$data['baidu_id'] = $_COOKIE["Baidu_id"];
				$data['baidu_ip'] = $_SERVER["REMOTE_ADDR"];
			}
			//@检查是否通过平安好车访问的用户
			if(PA_BANNER == 'pa'){
				$data['is_pa'] = '1';
			}

    		$model = D('Order');
    		if($uid){
				
				
        		if(false !== $model->add($data)){
        			$_POST['order_id'] = $model->getLastInsID();
					$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('order_id'=>$_POST['order_id']));
        			/*if ($membercoupon_id and $order_type==2){
        			    $data_mc['order_id'] = $_POST['order_id'];
        			    $data_mc['is_use'] = 1;
        			    $model_membercoupon->where($map_mc)->save($data_mc);
        			}*/
        		}

				/*抵用券选择[1]50元抵用券 [2]车会抵用券*/
				$radio_sale = $_REQUEST['radio_sale'];
				$code = $_REQUEST['code'];
				//echo $radio_sale."gg".$code;exit;
				if($radio_sale || $code){
					$membersalecoupon_id = $this->add_salemembercoupon($uid,$radio_sale,$code,$data['shop_id'],$_POST['order_id']);
					$data['membersalecoupon_id'] = $membersalecoupon_id;
				}

        		$model_member = D('Member');
        		$get_user_name = $model_member->where("uid=$uid")->find();
        		if ($list_product){
            		foreach($list_product AS $k=>$v){
            				$sub_order[]=array(
            				'order_id'=>$_POST['order_id'],
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
        		}
    		}else{
    		    $model = D('Ordernologin');
        		if(false !== $model->add($data)){
        			$_POST['order_id'] = $model->getLastInsID();
        		}
    		}



			//平安好车传送接口-------------------start
			if(PA_BANNER == "pa") {
				//从这里开始是平安要的配件拼接数据！！
				$Arrservice = explode(',',$select_services);
				 foreach ($Arrservice as $k=>$services_id){
    		        $map['service_id'] = array('eq',$services_id);
            		$map['model_id'] = array('eq',$_REQUEST['model_id']);
            		$list_product = $model_product->where($map)->find();
					$list_product_detail = unserialize($list_product['product_detail']);
					if($list_product_detail) {
						foreach ($list_product_detail as $key=>$val){
							if ($val['Midl_name'] != '工时费'){
								$partsName .= $val['Midl_name']."#";
								$partsBrand .= "原厂#";
								$partsQuantity .= $val['quantity']."#";
								$specificationParameter .= $val['unit']."#";
							}
						}

						$partsName = substr( $partsName , 0 , -1 );
						$partsBrand = substr( $partsBrand , 0 , -1 );
						$partsQuantity = substr( $partsQuantity , 0 , -1 );
						$specificationParameter = substr( $specificationParameter , 0 , -1 );
						
						if($services_id == '28' && $services_id == '30') {//四轮定位//车辆维修
							$partsName .= "|";
							$partsBrand .= "|";
							$partsQuantity .= "|";
							$specificationParameter .= '|';
						}else {
							$partsName .= ";";
							$partsBrand .= ";";
							$partsQuantity .= ";";
							$specificationParameter .= ';';
						}

					}else {
						$partsName .= ";";
						$partsBrand .= ";";
						$partsQuantity .= ";";
						$specificationParameter .= ';';
					}
    		    }

				$partsName = substr( $partsName , 0 , -1 );
				$partsBrand = substr( $partsBrand , 0 , -1 );
				$partsQuantity = substr( $partsQuantity , 0 , -1 );
				$specificationParameter = substr( $specificationParameter , 0 , -1 );

				$rest = substr($partsName, -1);
				if($rest == ";") {
					$partsName = substr( $partsName , 0 , -1 );
				}
				$rest = substr($partsBrand, -1);
				if($rest == ";") {
					$partsBrand = substr( $partsBrand , 0 , -1 );
				}
				$rest = substr($partsQuantity, -1);
				if($rest == ";") {
					$partsQuantity = substr( $partsQuantity , 0 , -1 );
				}
				$rest = substr($specificationParameter, -1);
				if($rest == ";") {
					$specificationParameter = substr( $specificationParameter , 0 , -1 );
				}

				//从这里开始是平安要的配件拼接数据！！-------------end
				
				$car_model = D("Carmodel");
				$carmodel = $car_model->find($_POST['model_id']);

				$carseries_model = D("Carseries");
				$carseries = $carseries_model->find($carmodel['series_id']);

				$carbrand_model = D("Carbrand");
				$carbrand = $carbrand_model->find($carseries['brand_id']);

				$shop_model = D("Shop");
				$shop_info = $shop_model->find($_POST['shop_id']);

				//平安好车新的接口--参数规则--start 2014/4/15
				$data_pa['carType']	 = $carmodel['model_name'];
				$data_pa['carTypeCode'] = $_POST['model_id'];
				$data_pa['carModel'] = $carseries['series_name'];
				$data_pa['carModelCode'] = $carseries['series_id'];
				$data_pa['brand']	 = $carbrand['brand_name'];
				$data_pa['brandCode'] = $carbrand['brand_id'];
				
				//$Arrservice = explode(',',$select_services);
				$maintenance = $this->GET_paservice_all($Arrservice);
				$maintenanceType = $maintenance['maintenanceType'];//得到服务类型ID(一级)	
				$maintenanceItem = $maintenance['maintenanceItem'];//得到服务类型名称(二级)	
				$maintenanceItemCode = $maintenance['maintenanceItemCode'];//得到服务类型ID(二级)	

				$data_pa['maintenanceType'] = $maintenanceType; //服务类型ID（一级分类）---------必须
				$data_pa['maintenanceItem'] = $maintenanceItem;//服务项目名称,多个时以';'分隔(二级分类) ---------必须
				$data_pa['maintenanceItemCode'] = $maintenanceItemCode; //多个时以';'分隔,顺序与保养项目对应(二级分类) ---------必须
				
				$data_pa['partsName'] = $partsName;//配件名称----这尼玛逗比 还是必须的字段 真搞不懂这个必须的是什么东西！！！---------必须
				$data_pa['partsBrand'] = $partsBrand;//配件品牌
				$data_pa['specificationParameter'] = $specificationParameter;//规格参数用';'串联，机油、轮胎常见
				$data_pa['partsQuantity'] = $partsQuantity;//配件数量-----这tm也是必须的艹!!! ---------必须

				$data_pa['name']	= $_POST['truename'];
				$data_pa['mobile'] = $_POST['mobile'];
				$data_pa['shopSign'] = trim($_POST['cardqz'].$_POST['licenseplate']);
				$data_pa['partsCost'] = $total_product_price;//配件总费-----没做的
				$data_pa['laborCost '] = $total_workhours_price ;//人工总费-----没做的
				$data_pa['totalCost'] = $total_price;
				$data_pa['alMileage'] = $_POST['miles'];
				$data_pa['voitureNo'] = $_POST['car_sn'];
				$data_pa['appointmentTime'] = $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'].":00";
				$data_pa['orderTime'] = date("Y-m-d H:i:s" , time());//下单日期
				
				$data_pa['clientManager '] = $clientManager;//客户经理
				$data_pa['servicesName'] = $shop_info['shop_name'];//服务商名称---------必须
				$data_pa['orderNum'] = $_POST['order_id'];//订单号---------必须
				$data_pa['orderState'] = '01';//订单状态----没做的 看xls
				$data_pa['orderChannel'] = "SH_03";//订单来源（合作方）

				$data_pa['serviceEndTime'] = $serviceEndTime;//服务完成日期
				
				$servicesCity = '上海';
				$data_pa['servicesCity'] = $servicesCity;//服务商城市 ---------必须
				$data_pa['servicesAddress'] = $shop_info['shop_address'];//服务商地址
				$data_pa['servicesTel'] = $shop_info['shop_phone'];//服务商电话
				$data_pa['operatorType'] = 0;//0:新增预约---------必须
				//平安好车新的接口--参数规则--end 2014/4/15

				foreach($data_pa as $key=>$val) {
					$replace = array("<", ">", "(", ")", "\"", "'", "=");
					$data_pa[$key] = str_replace($replace, "", $val);
					$hkey .= $data_pa[$key];
				}
				$data_pa['hkey'] = md5($hkey);
				$pa_result = $this->Api_toPA("http://www.pahaoche.com/yangche/yuyue.w",$data_pa);
				
				$paresult_model = D("Paresult");
				$paresult_data['order_id'] = $_POST['order_id'];
				$paresult_data['result'] = str_replace("\"","",$pa_result);
				$paresult_data['create_time'] = time();
				$paresult_model->add($paresult_data);
			}
			//平安好车传送接口-------------------end*/

    		if(!empty($_POST['order_id'])){
				if($uid) {
					$this->success('预约提交成功！',__APP__.'/myhome');
				}else {
					$this->success('预约提交成功！',__APP__.'/index');
				}
    		}else {
    		    $this->error('预约失败！',__APP__.'/myhome');
    		}
		}
	}

	/*
		@author:chf
		@function:添加进入抵用券表
		@time:2013-11-4
	*/
	function add_salemembercoupon($uid,$radio_sale,$code,$shop_id,$order_id){
		/*抵用券选择[1]50元抵用券 [2]驴妈妈50元抵用券 [3]车会抵用券*/
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		if(!$member['mobile']){
			$this->error('请填写个人资料手机号！',__APP__.'/myhome');
			exit;
		}
	
		$nowtime = time();
		if($radio_sale == 3 || $radio_sale == 5 || $radio_sale == 1 || $radio_sale ==2){
			$mobile = $member['mobile'];
			$membersalecouponcount = $this->MembersalecouponModel->where(array('mobile'=>$member['mobile'],'is_use'=>0,'salecoupon_id'=>$radio_sale))->count();
		
				if($membersalecouponcount > 0){
					$membersale = $this->MembersalecouponModel->where(array('mobile'=>$member['mobile'],'is_use'=>0,'salecoupon_id'=>$radio_sale))->find();
					
					$membersalecoupon_id = $membersale['membersalecoupon_id'];
					$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('order_id'=>$order_id));
					
				}
		}

		return $membersalecoupon_id;
	}


	public function editstate() {
	    if( true !== $this->login()){
			exit;
		}
		if(!empty($_REQUEST)){
			$uid = $this->GetUserId();
			$model_order = D('Order');
			$map['id'] = array('eq',$_REQUEST['order_id']);
			$map['uid'] = array('eq',$uid);
			$list = $model_order->where($map)->find();
			
			if($list){
			    $result = false;
				if($_REQUEST['order_state']){
					if($_REQUEST['order_state'] == 'cancel'){
						$data['order_state'] = '-1';
					}else{
						$data['order_state'] = $_REQUEST['order_state'];
					}
					$data['cancel_time'] = time();
					
				    $result = $model_order->where($map)->data($data)->save();
					//echo $model_order->getlastSql();
				}
				if($_REQUEST['complain_state']){
					$data['complain_state'] = $_REQUEST['complain_state'];
					$result = $model_order->where($map)->data($data)->save();
				}
				if($result){
					$this->success('成功',__APP__.'/myhome/');
				}else{
					$this->error('操作失败');
				}
			}
		}
	}
	public function orderlist(){
	    if( true !== $this->login()){
			exit;
		}
		$model_order = D('Order');
		$model_serviceitem = D('Serviceitem');
		$uid = $this->GetUserId();
        if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['uid'] = array('eq',$uid);
		
		$map_order = $map;
		$map_order['coupon_id'] = 0;
        // 计算总数
        $order_count = $model_order->where($map_order)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($order_count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
	    //分页跳转的时候保证查询条件
        foreach ($map_order as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        foreach($list AS $k=>$v){
		    $list[$k]['order_id'] = $this->get_orderid($v['id']);
        }
		$map_coupon_order = $map;
		$map_coupon_order['coupon_id'] = array('gt',0);
        // 计算总数
        $coupon_order_count = $model_order->where($map_coupon_order)->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $coupon_p = new Page($coupon_order_count, 10);
        // 分页显示输出
        $coupon_page = $coupon_p->show();
    
        // 当前页数据查询
        $coupon_list = $model_order->where($map_coupon_order)->order('id DESC')->limit($coupon_p->firstRow.','.$coupon_p->listRows)->select();
	    //分页跳转的时候保证查询条件
        foreach ($map_coupon_order as $key => $val) {
            if (!is_array($val)) {
                $coupon_p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
	    foreach($coupon_list AS $coupon_k=>$coupon_v){
		    $coupon_list[$coupon_k]['order_id'] = $this->get_orderid($coupon_v['id']);
        }
		
	    //我的车辆列表
	    $model_membercar = D('Membercar');
		$list_all_car = $model_membercar->where("uid=$uid AND status=1")->select();
		$count_car = count($list_all_car);
		if($count_car > 0){
			foreach($list_all_car AS $k=>$v){
					$model_myhome = D('Myhome');
					$list_all_car[$k]['list_total'] = $model_myhome->list_total($list_all_car[$k]['u_c_id'],$list_all_car[$k]['avgoil_type']);			
			}
		}
		
		//用户信息
		$model_member = D('Member');
		$memberinfo = $model_member->where("uid=$uid")->select();
		if (isset($_GET['type'])){
		    $this->assign('type',$_GET['type']);
		}
		
		//订单信息
		$model_point = D('Point');
		$point_info =$model_point->where("uid=$uid")->select();
		$uidadd = $uid+UID_ADD;
		//用户注册推荐地址
		$register_url = WEB_ROOT.'/index.php/member/add/uid/'.$uidadd.'/registercode/'.md5($uidadd.REGISTER_CODE);
		$this->assign('register_url',$register_url);
		//推荐注册的用户
		$model_registerrecommend = D('registerrecommend');
		unset($map);
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
		
		//优惠券信息
		$model_membercoupon = D('Membercoupon');
		$couponmap['xc_membercoupon.uid'] = $uid;
		$couponmap['xc_membercoupon.is_delete'] = 0;
		$couponmap['xc_coupon.is_delete'] = 0;
		$couponinfo = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("is_use ASC,end_time DESC")->select();
		
		$couponmap['xc_membercoupon.is_use'] = 0;
		$couponmap['xc_coupon.end_time'] = array('gt',time());
		$no_usenum = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("is_use ASC,end_time DESC")->count();
        		
		$this->assign('couponinfo',$couponinfo);
		$this->assign('no_usenum',$no_usenum);
		$this->assign('registerre_list',$registerre_list);
		$this->assign('point_info',$point_info);
		$this->assign('memberinfo',$memberinfo[0]);
		$this->assign('list_all_car',$list_all_car);
		$this->assign('count',$order_count);//echo '<pre>';print_r($list);exit;
        $this->assign('page', $page);
        $this->assign('list', $list);
		$this->assign('coupon_count',$coupon_order_count);//echo '<pre>';print_r($list);exit;
        $this->assign('coupon_page', $coupon_page);
        $this->assign('coupon_list', $coupon_list);
		
		$this->display();
	}
	
    public function updatemember(){
        echo 2;exit;
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
		//echo '<pre>';print_r($product_img);
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
        							'str'=>'通过携车网预约您所选择的维修保养项目，共为您节省：','color'=>'0, 0, 0','size'=>'15','colspan'=>'4','width'=>'630'
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


				$table2_arr[] = array(//tr
        			'tr'=>array(
        						array(//td
        							'str'=>'价格仅供参考，已到店实际发生的交易金额为准','color'=>'0, 0, 255','size'=>'12','colspan'=>'4','align'=>'right','width'=>'630'
        						),
        					),
        			'height'=>'30'
        		);
        		$tables_arr[] = $table1_arr;
        		$tables_arr[] = $table2_arr;
            }
    		if ($coupon_id>0){
    		    $savemoney = '凭券优惠'.($save_total+$coupon_amount).'元';
    		}else{
    		    if (($save_total+$coupon_amount)>0){
    		        $savemoney = '优惠'.($save_total+$coupon_amount).'元';
    		    }else{
    		        $savemoney = '';
    		    }
    		}
            //echo '<pre>';print_r($tables_arr);exit;
            $this->createtableimg($tables_arr,$folder,$img_name);
            if (empty($product_img)){
                $data['create_time'] = time();
                $data['savemoney'] = $savemoney;
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
		
		return array('pic_path'=>$folder.'/'.sha1($product_img['product_imgname']).'.png','savemoney'=>$product_img['savemoney']);
		//$this->assign('getShop',$getShop);
		//$this->assign('folder',$folder);
		//$this->assign('product_imgname',$product_img['product_imgname']);
		//$this->display();
	}
	
	
	public function ajax_get_orderlog(){
		$map['order_id'] = array('eq',$_REQUEST['order_id']);
		$model_orderlog = D('Orderlog');
		$list = $model_orderlog->where($map)->select();
		//echo $model_orderlog->getLastSql();
		//dump($list);
		$html = '<table>';
		foreach($list AS $k=>$v){
			//echo $list[$k]['uid'];
			if($list[$k]['uid']){
				$html .= '<tr><td>用户：</td><td>'.$list[$k][log].'</td></tr>';
			}else{
				 $html .= '<tr><td>客服：</td><td>'.$list[$k][log].'</td></tr>';
			}
		}
		$html .= '</table>';
		echo $html;
	}
	
    public function show_order_detail(){
        if( true !== $this->login()){
			exit;
		}
        $model_order = D('Order');
        $uid = $this->GetUserId();
        $orderid = isset($_GET['orderid'])?$_GET['orderid']:0;
        $map['xc_order.id'] = array('eq',$orderid);
		$list = $model_order->where($map)->find();
		$list['order_id'] = $this->get_orderid($list['id']);
		if ($list['coupon_save_discount']>0){
		    $workhours_sale = sprintf("%.2f", $list['workhours_sale']-$list['coupon_save_discount']);
		    if ($workhours_sale=='0.00'){
		        $workhours_sale = -1;
		    }
		}else {
		    $workhours_sale = $list['workhours_sale'];
		}
		
		$product_img_str = $list['shop_id'].'_'.$list['model_id'].'_'.$list['service_ids'].'_'.$list['productversion_ids'].'_'.$list['timesaleversion_id'].'_'.$workhours_sale.'_'.$list['product_sale'];
		if ($list['membercoupon_id']){
		    $model_membercoupon = D('Membercoupon');
		    $membercoupon = $model_membercoupon->find($list['membercoupon_id']);
		    $coupon_id = $membercoupon['coupon_id'];
		    $product_img_str .= '_'.$coupon_id;
		    $model_coupon = D('Coupon');
		    $coupon = $model_coupon->find($coupon_id);
		}
		$img_name = $list['model_id'].'/'.sha1($product_img_str).'.png';
		$this->assign("img_name",$img_name);
	    $model_shop = D('Shop');
	    $getShop = $model_shop->where("id = $list[shop_id]")->find();
		
		if($list['product_sale'] > 0){
		    $sale_value['product_sale'] = $list['product_sale'];
			$sale_arr['product_sale'] = ($list['product_sale']*10).'折';
		}else{
			$sale_arr['product_sale'] = '无折扣';
			$sale_value['product_sale'] = 1;
		}

		if($list['workhours_sale'] > 0){
			$sale_value['workhours_sale'] = $list['workhours_sale'];
			$sale_arr['workhours_sale'] = ($list['workhours_sale']*10).'折';
		}else{
			$sale_arr['workhours_sale'] = '无折扣';
			$sale_value['workhours_sale'] = 1;
		}
		
        if($list['coupon_save_discount'] > 0 || $list['coupon_save_money'] > 0){
			$sale_arr['coupon_save_money'] = $list['coupon_save_money'];
			$sale_value['coupon_save_discount'] = $list['coupon_save_discount'];
			$sale_arr['coupon_save_discount'] = ($list['coupon_save_discount']*10).'折';
		}
		
		$model_serviceitem = D('Serviceitem');
		unset($map);
	    $map['id'] = array('in',$list['service_ids']);
	    $services_info = $model_serviceitem->where($map)->select();
	    $this->assign('services_info',$services_info);
	    
		$car_model = D('Membercar');
		$my_car['brand_id'] = $list['brand_id'];
		$my_car['series_id'] = $list['series_id'];
		$my_car['model_id'] = $list['model_id'];
		//用户所有自定义车型初始化
		$my_car = $car_model->Membercar_format_by_arr($my_car,1);
		$this->assign('my_car',$my_car);
		//echo '<pre>';print_r($list_product);exit;
		//订单完成积分
		$model_point = D('Point');
		$pointinfo = $model_point->where("uid=$uid AND orderid=$orderid")->find();
		$this->assign('sale_value',$sale_value);
		$this->assign('sale_arr',$sale_arr);
		$this->assign('getShop',$getShop);
		$this->assign('list',$list);
		$this->assign('pointinfo',$pointinfo);
		$this->assign('coupon',$coupon);
    	$this->display();
    }
	
	public function check_coupon(){
	    $coupon_id = $_POST['coupon_id'];
	    $uid = $_POST['uid'];
	    $model_membercoupon = D('Membercoupon');
	    if ($uid and $coupon_id){
	        $map['xc_membercoupon.uid'] = $uid;
	        $map['xc_membercoupon.coupon_id'] = $coupon_id;
	        $map['xc_coupon.end_time'] = array('gt',time());
	        $map['xc_membercoupon.is_delete'] = 0;
	        $map['xc_membercoupon.is_use'] = 0;
	        if($membercoupon = $model_membercoupon->where($map)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("membercoupon_id ASC")->find()){
	            echo $membercoupon['membercoupon_id'];
	        }else{
	            echo 0;
	        }
	    }
	    exit;
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
                    if(!in_array($v,$coupon_serverids_arr)){
                        $return_id = $v.',';
                    }
                }
                $return_id = substr($return_id,0,-1);
            }
        }
        return $return_id;
    }

	/*
		@author:ysh
		@function:和平安数据比对得到订单状态数据
		@time:2014-5-6
	*/
	function GET_paservice_all($Arrservicea){
		foreach($Arrservicea as $v){
			if($v=='28'){
				$maintenanceType[]='12';
			}else if($v=='30'){
				$maintenanceType[]='14';
			}else{
				$maintenanceType[]='11';
			}

			if($v=='9'){//大保养
				$maintenanceType[]='11';
				$maintenanceItem['11'] .= '大保养;';
				$maintenanceItemCode['11'] .= '001;';
			}elseif($v=='10'){//小保养
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='小保养;';
				$maintenanceItemCode['11'] .='002;';
			}elseif($v=='11' || $v=='12'){//更换前后刹车片
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='刹车片更换;';
				$maintenanceItemCode['11'] .='020;';
			}elseif($v=='14'){//更换蓄电池
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='电瓶更换;';
				$maintenanceItemCode['11'] .='018;';
			}elseif($v=='15'){//更换变速箱油
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='变速箱油更换;';
				$maintenanceItemCode['11'] .='008;';
			}elseif($v=='16'){//更换制动液
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='刹车油更换;';
				$maintenanceItemCode['11'] .='012;';
			}elseif($v=='17'){//清洗节气门
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='节气门清洁;';
				$maintenanceItemCode['11'] .='023;';
			}elseif($v=='18'){//清洗喷油嘴
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='喷油嘴清洗;';
				$maintenanceItemCode['11'] .='026;';
			}elseif($v=='19'){//更换专向助理液
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='换转向助力油;';
				$maintenanceItemCode['11'] .='009;';
			}elseif($v=='20'){//更换雨刮
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='雨刮更换;';
				$maintenanceItemCode['11'] .='017;';
			}elseif($v=='25'){//更换正时皮带
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='正时套件更换;';
				$maintenanceItemCode['11'] .='007;';
			}elseif($v=='22'){//更换防冻液
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='防冻液更换;';
				$maintenanceItemCode['11'] .='006;';
			}elseif($v=='23'){//更换空调滤清器
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='空调滤更换;';
				$maintenanceItemCode['11'] .='005;';
			}elseif($v=='24'){//更换火花塞
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='火花塞更换;';
				$maintenanceItemCode['11'] .='019;';
			}elseif($v=='26' || $v=='27'){//更换后前制动盘
				$maintenanceType[]='11';
				$maintenanceItem['11'] .='刹车盘更换;';
				$maintenanceItemCode['11'] .='021;';
			}elseif($v=='28'){//四轮定位
				$maintenanceType[]='12';
				$maintenanceItem['12'] .='四轮定位;';
				$maintenanceItemCode['12'] .='002;';
			}elseif($v=='30'){
				$maintenanceType[]='14';
				$maintenanceItem['14'] .='车辆维修;';
				$maintenanceItemCode['14'] .='001;';
			}
		}
		$maintenanceType = array_unique($maintenanceType);

		foreach($maintenanceItem as $key=>$val) {
			$maintenanceItem[$key] = substr( $val , 0 , -1 );
		}
		foreach($maintenanceItemCode as $key=>$val) {
			$maintenanceItemCode[$key] = substr( $val , 0 , -1 );
		}
		$result['maintenanceType'] = implode("|",$maintenanceType);
		$result['maintenanceItem'] =  implode("|",$maintenanceItem);
		$result['maintenanceItemCode'] =  implode("|",$maintenanceItemCode);

		return $result;
	}
	
}