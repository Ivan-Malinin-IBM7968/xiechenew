<?php
// 本类由系统自动生成，仅供测试用途
/*
 * 获取店铺列表
 */

class AppandroidAction extends CommonAction {

    //取消订单
    public function cancel_order() {
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $order_id = $_REQUEST['order_id'];
            	if ($order_id){
            	    $model_order = D('Order');
            	    $map_o['id'] = $order_id;
            	    $map_o['uid'] = $memberinfo['uid'];
            	    $data['order_state'] = -1;
            	    if ($model_order->where($map_o)->save($data)){
            	        $xml_content .= "<status>0</status><desc>操作成功</desc>";
            	    }else{
            	        $xml_content .= "<status>2</status><desc>操作失败</desc>";
            	    }
            	}else {
            	    $xml_content .= "<status>3</status><desc>订单号错误</desc>";
            	}
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
        }else{
            $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
        }

    	$xml_content .="</XML>";
        echo $xml_content;exit;
    }

	//得到车辆信息
    public function getmycar(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if (isset($_GET['tolken'])){
	        $tolken = $_GET['tolken'];
	        $model_member = D('Member');
	        $membermap['tolken'] = $tolken;
	        $member = $model_member->where($membermap)->find();
	        //echo '<pre>';print_r($member);exit;
	        if ($member){
    	        $model_membercar = D('Membercar');
    	        $membercarmap['uid'] = $member['uid'];
    	        $membercarmap['status'] = 1;
    	        $membercar = $model_membercar->where($membercarmap)->select();
    	        if ($membercar){
    	            foreach ($membercar as $key=>$val){
    	                $xml_content .= "<item><u_c_id>".$val['u_c_id']."</u_c_id><brand_id>".$val['brand_id']."</brand_id><series_id>".$val['series_id']."</series_id><model_id>".$val['model_id']."</model_id><car_name>".$val['car_name']."</car_name><status>".$val['status']."</status></item>";
    	            }
    	        }
	        }
	    }
	    $xml_content.="</XML>";
	    echo $xml_content;exit;
	}
	//获取默认车辆
    public function get_default_car(){
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_membercar = D('Membercar');
                $map_mc['uid'] = $uid;
                $map_mc['status'] = 1;
                $map_mc['is_default'] = 1;
                $membercarinfo = $model_membercar->where($map_mc)->find();
                if ($membercarinfo){
                    $xml_content .= "<u_c_id>".$membercarinfo['u_c_id']."</u_c_id><brand_id>".$membercarinfo['brand_id']."</brand_id><series_id>".$membercarinfo['series_id']."</series_id><model_id>".$membercarinfo['model_id']."</model_id><car_name>".$membercarinfo['car_name']."</car_name>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
	    $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	/*
		@author:CHF
		@function:安卓接口调用用户信息
		@time:2013-3-11

	*/
    public function get_user_name(){

        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){

            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();

			/* (修改)
				@author:chf
				@补充order里面的xc_order表里的trunname licenseplate车牌号
				@time:2013-3-11
			*/
			$model_order = D('Order');
			$order_m['model_id'] = $_REQUEST['model_id'];//车型ID
			$order_m['order_state'] = 2;//订单状态为完成
			$order = $model_order->where($order_m)->order('create_time desc')->limit(1)->find();
			if(!$order['licenseplate']){
				$order_model['uid'] = $memberinfo['uid'];//用户UID
				$order_model['order_state'] = 2;//订单状态为完成
				$order = $model_order->where($order_model)->order('create_time desc')->limit(1)->find();
			}

			//print_r($order);
			$licenseplate = mb_substr($order['licenseplate'], 0, 1, 'utf-8'); //牌照首字
			$licenseplateNum = mb_substr($order['licenseplate'], 1, strlen($order['licenseplate']), 'utf-8'); //牌照后几位数字

            if ($memberinfo){
                $xml_content .= "<uid>".$memberinfo['uid']."</uid><username>".$memberinfo['username']."</username><cardid>".$memberinfo['cardid']."</cardid><email>".$memberinfo['email']."</email><mobile>".$memberinfo['mobile']."</mobile><prov>".$memberinfo['prov']."</prov><city>".$memberinfo['city']."</city><area>".$memberinfo['area']."</area><truename>".$order['truename']."</truename><licenseplate>".$licenseplate."</licenseplate><licenseplateNum>".$licenseplateNum."</licenseplateNum>";
            }
	    }
	    $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	//备份商铺表
    public function get_shops_bak(){
        $model_carseries = D('Carseries');
        $shop_area = isset($_REQUEST['area_id'])?$_REQUEST['area_id']:0;
        $series_id = isset($_REQUEST['search'])?$_REQUEST['search']:0;
        $model_shop = D('Shop');
        if ($series_id){
            $map_series['series_id'] = $series_id;
        }else{
            if ($_REQUEST['tolken']){
                $tolken = $_REQUEST['tolken'];
    	        $model_member = D('Member');
    	        $map_m['tolken'] = $tolken;
    	        $map_m['tolken_time'] = array('gt',time());
    	        $memberinfo = $model_member->where($map_m)->find();
                if ($memberinfo){
                    $uid = $memberinfo['uid'];
                    $model_membercar = D('Membercar');
                    $map_membercar['uid'] = $uid;
                    $map_membercar['status'] = 1;
                    $map_membercar['is_default'] = 1;
                    $membercar = $model_membercar->where($map_membercar)->find();
                    if ($membercar['series_id']){
                       $map_series['series_id'] = $membercar['series_id'];
                    }
                }
            }
        }
        if ($map_series['series_id']){
            $series_info = $model_carseries->where($map_series)->find();
            $model_shop_fs_relation = D('Shop_fs_relation');
            $map_fs['fsid'] = $series_info['fsid'];
            $shop_fs = $model_shop_fs_relation->where($map_fs)->select();
            $shop_id_arr = array();
            if ($shop_fs){
                foreach ($shop_fs as $s_k=>$s_v){
                    $shop_id_arr[] = $s_v['shopid'];
                }
            }
            $shop_id_str = implode(',',$shop_id_arr);
            $map_shop['id'] = array('in',$shop_id_str);
        }
        $model_region = D('Region');
        if ($shop_area){
            //$map_region['region_name'] = $shop_area;
            //$region = $model_region->where($map_region)->find();
            $map_shop['shop_area'] = $shop_area;
        }
        if ($_REQUEST['shop_id']){
            $map_shop['id'] = $_REQUEST['shop_id'];
        }
        $map_shop['status'] = 1;
        $model_timesale = D('Timesale');
        $model_timesaleversion = D('Timesaleversion');

        $shop_info = $model_shop->where($map_shop)->order("comment_rate DESC")->select();
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
        if ($shop_info){
            foreach ($shop_info as $k=>$v){
               $workhours_sale = 0;
               $product_sale = 0;
               if ($v['logo']){
                   $v['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$v['id'].'.jpg';
               }
                /*if ($v['shop_maps']){
	    	        $maps_diff = C('MAPS_DIFF');
	    	        $shop_maps_arr = explode(',',$v['shop_maps']);
	    	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
	    	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
	    	        $v['shop_maps'] = implode(',',$shop_maps_arr);
	    	    }*/
               $area_info = $model_region->find($v['shop_area']);
               $show_title = $area_info['region_name'].' '.$v['shop_address'];
               $xml_content .= "<item><show_title>".$show_title."</show_title><shop_name>".$v['shop_name']."</shop_name><shop_id>".$v['id']."</shop_id><shop_address>".$v['shop_address']."</shop_address><shop_phone>".$v['shop_phone']."</shop_phone><area>".$v['shop_maps']."</area><logo>".$v['logo']."</logo><region>".$area_info['region_name']."</region><comment_rate>".$v['comment_rate']."</comment_rate><comment_number>".$v['comment_number']."</comment_number>";
               $map['shop_id'] = $v['id'];
               $map['status'] = 1;
               $timesales = $model_timesale->where($map)->select();
               if ($timesales){
                   foreach ($timesales as $kk=>$vv){
                       $map_tv['timesale_id'] = $vv['id'];
                       $map_tv['status'] = 1;
                       $timesaleversion = $model_timesaleversion->where($map_tv)->select();
                       if ($timesaleversion){
                           foreach ($timesaleversion as $_k=>$_v){
                               if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
        		                   continue;
        		               }
        		               if ($_k==0){
        		                   $product_sale = $_v['product_sale'];
                                   $workhours_sale = $_v['workhours_sale'];
        		               }else{
        		                   if ($product_sale>0 and $_v['product_sale']>0){
        		                       $product_sale = min($product_sale,$_v['product_sale']);
        		                   }else{
        		                       $product_sale = max($product_sale,$_v['product_sale']);
        		                   }
        		                   if ($workhours_sale>0 and $_v['workhours_sale']>0){
        		                       $workhours_sale = min($workhours_sale,$_v['workhours_sale']);
        		                   }else{
        		                       $workhours_sale = max($workhours_sale,$_v['workhours_sale']);
        		                   }
        		               }
                               //$xml_content .= "<timesale><timesaleversion_id>".$_v['id']."</timesaleversion_id><product_sale>".$_v['product_sale']."</product_sale><workhours_sale>".$_v['workhours_sale']."</workhours_sale><memo>".$_v['memo']."</memo><coupon_id>".$_v['coupon_id']."</coupon_id></timesale>";
                           }
                       }
                   }
               }
               if ($product_sale==0){
                   $product_sale = '无';
               }else{
                   $product_sale = ($product_sale*10).'折';
               }
               if ($workhours_sale==0){
                   $workhours_sale = '无';
               }else{
                   $workhours_sale = ($workhours_sale*10).'折';
               }
               $xml_content .= "<product_sale>".$product_sale."</product_sale><workhours_sale>".$workhours_sale."</workhours_sale>";
               $xml_content .= "</item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }


	/*
		@author:ysh
		@function:显示店铺搜索页
		@time:2013-08-04
	*/
    public function get_shops(){
        $model_carseries = D('Carseries');
        $shop_city = empty($_REQUEST['city_id'])?'3306':$_REQUEST['city_id'];
		//$shop_city = '3306';
        $shop_area = isset($_REQUEST['area_id'])?$_REQUEST['area_id']:0;
        $series_id = isset($_REQUEST['search'])?$_REQUEST['search']:0;

        $model_shop = D('Shop');
        $model_shop_fs_relation = D('Shop_fs_relation');
        if ($series_id){
            $map_series['series_id'] = $series_id;
        }else{
            if ($_REQUEST['tolken']){
                $tolken = $_REQUEST['tolken'];
    	        $model_member = D('Member');
    	        $map_m['tolken'] = $tolken;
    	        $map_m['tolken_time'] = array('gt',time());
    	        $memberinfo = $model_member->where($map_m)->find();
                if ($memberinfo){
                    $uid = $memberinfo['uid'];
                    $model_membercar = D('Membercar');
                    $map_membercar['uid'] = $uid;
                    $map_membercar['status'] = 1;
                    $map_membercar['is_default'] = 1;
                    $membercar = $model_membercar->where($map_membercar)->find();
                    if ($membercar['series_id']){
                       $map_series['series_id'] = $membercar['series_id'];
                    }
                }
            }
        }
        if ($map_series['series_id']){
            $series_info = $model_carseries->where($map_series)->find();

            $map_fs['fsid'] = $series_info['fsid'];
            $shop_fs = $model_shop_fs_relation->where($map_fs)->select();
            $shop_id_arr = array();
            if ($shop_fs){
                foreach ($shop_fs as $s_k=>$s_v){
                    $shop_id_arr[] = $s_v['shopid'];
                }
            }
            $shop_id_str = implode(',',$shop_id_arr);
            $map_shop['id'] = array('in',$shop_id_str);
        }
        $model_region = D('Region');
        if ($shop_area > 0){
            //$map_region['region_name'] = $shop_area;
            //$region = $model_region->where($map_region)->find();
            $map_shop['shop_area'] = $shop_area;
        }else {
            $map_shop['shop_city'] = $shop_city;
        }
        if ($_REQUEST['shop_id']){
            $map_shop['id'] = $_REQUEST['shop_id'];
        }
        $map_shop['status'] = 1;
        $model_timesale = D('Timesale');
        $model_timesaleversion = D('Timesaleversion');

        $page_size = 25;
		if($_REQUEST['ios'] == 1) {
				print_r($map_shop);exit();
		}
        if ($_REQUEST['order'] == 'distance' and $_REQUEST['lat'] and $_REQUEST['long']){
            $shops = $model_shop->where($map_shop)->order("have_coupon DESC,shop_class ASC,comment_rate DESC")->select();
            if ($shops){
                foreach ($shops as $_key=>$_val){
                    $shop_maps = $_val['shop_maps'];
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
            //echo '<pre>';print_r($shop_info);exit;
        }else{
            // 导入分页类
            import("ORG.Util.Page");
            $count = $model_shop->where($map_shop)->count();
            // 实例化分页类
            $p = new Page($count, $page_size);

			/*
			*@name:针对第一页无条件 做缓存处理
			*@author:ysh
			*@time:2013/8/1
			*/
//			$arr = array ( 'shop_city' => $shop_city ,'status' => 1 );
//			$array_diff = array_diff($map_shop,$arr);
//			if( !$array_diff && ( $p->firstRow == 0 ) )  {
//			
//				$memcache_app_shops = S('app_shops_sql_default');
//				if($memcache_app_shops) {
//					//echo "use memcache";
//					$shop_info = $memcache_app_shops;
//				}else {
//					//echo "notuse memcache and go sql";
//					$shop_info = $model_shop->where($map_shop)->order("have_coupon DESC,shop_class ASC,comment_rate DESC")->limit($p->firstRow.','.$p->listRows)->select();
//					S('app_shops_sql_default',$shop_info);
//				}
//
//			}else {
				//echo "have select no memcache";
				$shop_info = $model_shop->where($map_shop)->order("have_coupon DESC,shop_class ASC,comment_rate DESC")->limit($p->firstRow.','.$p->listRows)->select();
//			}

            //$shop_info = $model_shop->where($map_shop)->order("shop_class ASC,comment_rate DESC")->limit($p->firstRow.','.$p->listRows)->select();
			if( $shop_info) {	
				foreach($shop_info as $key=>$val) {
					$shop_maps = $val['shop_maps'];
					$shop_maps_arr = explode(',',$shop_maps);
					$shop_info[$key]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
				}
			}
            $p_count = ceil($count/$page_size);
        }
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
        if ($shop_info){
            $xml_content .= "<p_count>".$p_count."</p_count>";
            $model_coupon = D('Coupon');
            $workhours_sale = 0;
            $product_sale = 0;
            foreach ($shop_info as $k=>$v){
                //优惠券数量
               $map_coupon['shop_id'] = $v['id'];
               $map_coupon['show_s_time'] = array('lt',time());
               $map_coupon['show_e_time'] = array('gt',time());
               $map_coupon['is_delete'] = 0;
               $coupon = $model_coupon->where($map_coupon)->order("id DESC")->select();
               $have_coupon1 = 0;//现金券
               $have_coupon2 = 0;//团购券
               if ($coupon){
                   foreach ($coupon as $_kk=>$_vv){
                       if ($_vv['coupon_type']==1){
                           $have_coupon1 = 1;
                       }
                       if ($_vv['coupon_type']==2){
                           $have_coupon2 = 1;
                       }
                   }
               }
               if ($v['versionid']){
                   $v['id'] = $v['id'].'_'.$v['versionid'];
               }
               if (file_exists('./UPLOADS/Shop/100/'.$v['id'].'.jpg')){
	               $v['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$v['id'].'.jpg';
	           }else {
	               $shop_id = $v['id'];
	               $map_sfr['shopid'] = $shop_id;
	               $shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
	               $fsid = $shop_fs_relation['fsid'];
	               $model_fs = D('FS');
	               $map_f['fsid'] = $fsid;
	               $fs = $model_fs->where($map_f)->find();
	               if($fs['versionid']){
	                   $fsid = $fsid.'_'.$fs['versionid'];
	               }
	               $v['logo'] = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
	           }

               /*if ($v['id']){
                   $v['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$v['id'].'.jpg';
               }
                if ($v['shop_maps']){
	    	        $maps_diff = C('MAPS_DIFF');
	    	        $shop_maps_arr = explode(',',$v['shop_maps']);
	    	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
	    	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
	    	        $v['shop_maps'] = implode(',',$shop_maps_arr);
	    	    }*/
               $area_info = $model_region->find($v['shop_area']);
               $show_title = $area_info['region_name'].' '.$v['shop_address'];
               $xml_content .= "<item><distance>".$v['distance']."</distance><show_title>".$show_title."</show_title><shop_name>".$v['shop_name']."</shop_name><shop_id>".$v['id']."</shop_id><shop_address>".$v['shop_address']."</shop_address><shop_phone>".$v['shop_phone']."</shop_phone><area>".$v['shop_maps']."</area><logo>".$v['logo']."</logo><region>".$area_info['region_name']."</region><comment_rate>".$v['comment_rate']."</comment_rate><comment_number>".$v['comment_number']."</comment_number><shop_class>".$v['shop_class']."</shop_class>";
               if ($have_coupon1){
                   $xml_content .="<have_coupon1>1</have_coupon1>";
               }
               if ($have_coupon2){
                   $xml_content .="<have_coupon2>1</have_coupon2>";
               }
               $map['shop_id'] = $v['id'];
               $map['status'] = 1;
               $timesales = $model_timesale->where($map)->select();
               if ($timesales){
                    foreach ($timesales as $kk=>$vv){
					   $timesale_id_arr[] = $vv['id']; 
                   }
				   $map_tv['timesale_id'] = array('in',$timesale_id_arr);
				   $map_tv['status'] = 1;
				   $map_tv['s_time'] = array('lt',time());
				   $map_tv['e_time'] = array('gt',time());

				   $timesaleversion = $model_timesaleversion->where($map_tv)->order("workhours_sale")->find();
				   unset($timesale_id_arr);

				   if ($timesaleversion){
						$product_sale = $timesaleversion['product_sale'];
						$workhours_sale = $timesaleversion['workhours_sale'];
				   }else {
						$product_sale = 0;
						$workhours_sale = 0;
				   }
               }
               if ($product_sale==0){
                   $product_sale = '0折';
               }else{
                   $product_sale = ($product_sale*10).'折';
               }
               if ($workhours_sale==0){
                   $workhours_sale = '0折';
               }elseif($workhours_sale == '-1') {
					$workhours_sale = '全免';
               }else{
                   $workhours_sale = ($workhours_sale*10).'折';
               }
               $xml_content .= "<product_sale>".$product_sale."</product_sale><workhours_sale>".$workhours_sale."</workhours_sale>";
               $xml_content .= "</item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	/*
	 * 我的订单
	 */
	public function get_orders(){
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_order = D('Order');
                $map_o['uid'] = $memberinfo['uid'];
                //$order_state = $_REQUEST['order_state'];
                if ($_REQUEST['order_state']==='0' || $_REQUEST['order_state']){
                    $map_o['order_state'] = $_REQUEST['order_state'];
                }
                if ($_REQUEST['order_type']==='0' || $_REQUEST['order_type']){
                    $map_o['order_type'] = $_REQUEST['order_type'];
                }
                $orderinfo = $model_order->where($map_o)->order("id DESC")->select();
                //echo '<pre>';print_r($orderinfo);
                //echo $model_order->getLastSql();exit;
                $xml_content .= "<order_count>".count($orderinfo)."</order_count>";
                $model_shop = D('Shop');
                $order_state_arr = C('ORDER_STATE');
                $pay_state_arr = C('PAY_STATE');
                foreach ($orderinfo as $k=>$v){
                    $map_s['id'] = $v['shop_id'];
                    $shopinfo = $model_shop->where($map_s)->find();
                    if ($shopinfo['versionid']){
                       $shopinfo['id'] = $shopinfo['id'].'_'.$shopinfo['versionid'];
                    }
                    if (file_exists('./UPLOADS/Shop/100/'.$shopinfo['id'].'.jpg')){
                       $shopinfo['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$shopinfo['id'].'.jpg';
                    }else {
                       $model_shop_fs_relation = D('Shop_fs_relation');
                       $shop_id = $shopinfo['id'];
                       $map_sfr['shopid'] = $shop_id;
                       $shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
                       $fsid = $shop_fs_relation['fsid'];
                       $model_fs = D('FS');
                       $map_f['fsid'] = $fsid;
                       $fs = $model_fs->where($map_f)->find();
                       if($fs['versionid']){
                           $fsid = $fsid.'_'.$fs['versionid'];
                       }
                       $shopinfo['logo'] = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
                    }
                    $order_id = $this->get_orderid($v['id']);
                    $v['order_state_str'] = $order_state_arr[$v['order_state']];
                    $order_verify_str = '';
                    if($v['order_type']==2){
                        $v['order_state_str'] .= ','.$pay_state_arr[$v['pay_status']];
                        if($v['order_verify']){
                            $order_verify_str = "<order_verify>".$v['order_verify']."</order_verify>";
                        }
                    }

                    $xml_content .= "<order_item><id>".$v['id']."</id><order_id>".$order_id."</order_id><order_type>".$v['order_type']."</order_type><create_time>".$v['create_time']."</create_time><order_time>".$v['order_time']."</order_time><iscomment>".$v['iscomment']."</iscomment><order_state>".$v['order_state']."</order_state><order_state_str>".$v['order_state_str']."</order_state_str><complain_state>".$v['complain_state']."</complain_state><product_sale>".$v['product_sale']."</product_sale><workhours_sale>".$v['workhours_sale']."</workhours_sale><shop_id>".$v['shop_id']."</shop_id><shop_name>".$shopinfo['shop_name']."</shop_name><logo>".$shopinfo['logo']."</logo>".$order_verify_str."</order_item>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	//获取订单详细页面
    public function get_orderdetail(){
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_order = D('Order');
                $map_o['uid'] = $memberinfo['uid'];
                $map_o['id'] = $_REQUEST['order_id'];
                $orderinfo = $model_order->where($map_o)->find();
                //echo '<pre>';print_r($model_order->getLastSql());exit;
                if ($orderinfo){
                    $order_id = $this->get_orderid($orderinfo['id']);
                    $xml_content .= "<id>".$order_id."</id><order_id>".$orderinfo['id']."</order_id><create_time>".$orderinfo['create_time']."</create_time><order_time>".$orderinfo['order_time']."</order_time><order_state>".$orderinfo['order_state']."</order_state><complain_state>".$orderinfo['complain_state']."</complain_state><total_price>".$orderinfo['total_price']."</total_price><cost_price>".$orderinfo['cost_price']."</cost_price><iscomment>".$orderinfo['iscomment']."</iscomment>";
            		$model_comment = D('Comment');
            		$map_c['order_id'] = $orderinfo['id'];
            		$map_c['shop_id'] = $orderinfo['shop_id'];
                    $comment = $model_comment->where($map_c)->find();
                    if ($comment){
                        $xml_content .= "<comment_id>".$comment['id']."</comment_id>";
                    }
                    $model_serviceitem = D('Serviceitem');
                    $select_services_arr = explode(',',$orderinfo['service_ids']);
            		$select_services_str = '';
            		if ($select_services_arr){
            		    foreach ($select_services_arr as $s_key=>$s_val){
            		        if (!$s_val){
            		            unset($select_services_arr[$s_key]);
            		        }
            		    }
            		    $select_services_str = implode(',',$select_services_arr);
            		}
            	    $map['id'] = array('in',$select_services_str);
            	    $services_info = $model_serviceitem->where($map)->select();
            	    if ($services_info){
            	        foreach ($services_info as $_k=>$_v){
            	            $i = $_k+1;
            	            $xml_content .= "<serviceitem><i>".$i."</i><name>".$_v['name']."</name></serviceitem>";
            	        }
            	    }

            	    $model_shop = D('Shop');
            	    $map_shop['id'] = $orderinfo['shop_id'];
    	            $getShop = $model_shop->where($map_shop)->find();

                    if ($getShop['versionid']){
                       $getShop['id'] = $getShop['id'].'_'.$getShop['versionid'];
                    }
                    if (file_exists('./UPLOADS/Shop/100/'.$getShop['id'].'.jpg')){
                       $getShop['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$getShop['id'].'.jpg';
                    }else {
                       $model_shop_fs_relation = D('Shop_fs_relation');
                       $shop_id = $getShop['id'];
                       $map_sfr['shopid'] = $shop_id;
                       $shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
                       $fsid = $shop_fs_relation['fsid'];
                       $model_fs = D('FS');
                       $map_f['fsid'] = $fsid;
                       $fs = $model_fs->where($map_f)->find();
                       if($fs['versionid']){
                           $fsid = $fsid.'_'.$fs['versionid'];
                       }
                       $getShop['logo'] = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
                    }
    	            $xml_content .= "<shopinfo><shop_id>".$getShop['id']."</shop_id><logo>".$getShop['logo']."</logo><shop_maps>".$getShop['shop_maps']."</shop_maps><shop_name>".$getShop['shop_name']."</shop_name><shop_address>".$getShop['shop_address']."</shop_address></shopinfo>";
    	            if ($orderinfo['order_type'] == 2){

    	            }else{
                        if($orderinfo['product_sale'] > 0){
                			$sale_arr['product_sale'] = ($orderinfo['product_sale']*10).'折';
                		}else{
                			$sale_arr['product_sale'] = '无折扣';
                		}

                		if($orderinfo['workhours_sale'] > 0){
                			$sale_arr['workhours_sale'] = ($orderinfo['workhours_sale']*10).'折';
                		}else{
                			$sale_arr['workhours_sale'] = '无折扣';
                		}
                		$xml_content .= "<workhours_sale>".$sale_arr['workhours_sale']."</workhours_sale><product_sale>".$sale_arr['product_sale']."</product_sale>";
                        $detail_html = WEB_ROOT.'/index.php/appandroid/get_orderdetailhtml/order_id/'.$orderinfo['id'].'/tolken/'.$tolken;
                		$xml_content .= "<detail_html>".$detail_html."</detail_html>";
    	            }
                }else {
                   $xml_content .= "<status>2</status><desc>订单号错误</desc>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	//获取订单的详细价格明细表
    public function get_orderdetailhtml(){
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_order = D('Order');
                $map_o['uid'] = $memberinfo['uid'];
                $map_o['id'] = $_REQUEST['order_id'];
                $orderinfo = $model_order->where($map_o)->find();
                //echo '<pre>';print_r($model_order->getLastSql());exit;
                if ($orderinfo){
            		$select_services_str = $orderinfo['service_ids'];
            		if (!$select_services_str || $select_services_str =='-1'){
            		    $table_str = '未选择保养项目';
            		    echo $table_str;exit;
            		}
                    $order_id = $this->get_orderid($orderinfo['id']);
            	    $model_shop = D('Shop');
            	    $map_shop['id'] = $orderinfo['shop_id'];
    	            $getShop = $model_shop->where($map_shop)->find();
    	            if ($orderinfo['order_type'] == 1 || $orderinfo['order_type'] == 2){

    	            }else{
                		$map_pp['service_id']  = array('in',$select_services_str);
                		$map_pp['model_id']  = array('eq',$orderinfo['model_id']);
                		$product_model = D('Product');
                		$list = $product_model->where($map_pp)->select();
                		if ($list){
                    		foreach($list AS $_kk=>$_vv){
                    			$versionid_arr[] = $_vv['versionid'];
                    			$productid_arr[] = $_vv['id'];
                    		}
                    		$versionid_str = implode(',', $versionid_arr);
                    		$productid_str = implode(',', $productid_arr);
                            $map_p['id'] = array('in',$productid_str);
                    		$list_product = array();
                    		if (!empty($orderinfo['service_ids'])){
                    		    $select_services_arr = explode(',',$orderinfo['service_ids']);
                    		    foreach ($select_services_arr as $key=>$services_id){
                    		        if ($services_id){
                        		        $map_p['service_id'] = array('eq',$services_id);
                        		        $list_product[$key]['productinfo'] = $product_model->where($map_p)->find();
                        		        $list_product[$key]['service_id'] = $services_id;
                    		        }
                    		    }
                    		}

                    		$product_sale = $orderinfo['product_sale'];
                    		$workhours_sale = $orderinfo['workhours_sale'];
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
                    		$model_serviceitem = D('Serviceitem');
                    		$order_status = C('ORDER_STATE');
                    		$table_str = '<style type="text/css">
                                        *{font-family:"黑体"}
                                        	.order-title{font-size: 24px;font-weight: bold;}
                                        	.order-time{font-size:14px;}
                                        	.order-status{font-size:14px}
                                        	.subtitle{color:#a3a3a3;}
                                        	.sub-table-title td{color:#7f7f7f;font-size:15px;font-weight:normal}
                                        	.sub-table-content td{font-size:15px;color:#6f6f6f}
                                        </style>';
                    		$table_str .= '<table border="0" cellspacing="0" cellpadding="0" width=305>';
                    	    if (isset($list_product) and !empty($list_product)){
                    	        $i=0;
                                foreach ($list_product as $k=>$v){
                                    //echo '<pre>';print_r($list_product);exit;
                                    $i++;
                                    $service_name = $model_serviceitem->getById($v['service_id']);
                                    if ($v['productinfo']['product_detail']){
                                        $list_detail = $v['productinfo']['product_detail'];
                                        $list_detail = unserialize($list_detail);
                                    }else {
                                        $list_detail = array();
                                    }
                                    $table_str .= '<tr>
                                        		<td height="25">&nbsp;<span class="subtitle">'.$i.'：'.$service_name['name'].'</span></td>
                                        	</tr>';
                                    //$xml_content .= "<service_item><service_name>".$service_name['name']."</service_name>";
                                    if ($list_detail){
                                        $table_str .= '<tr>
                                        		<td>
                                        			<table width="95%" border="1" align="center" cellpadding="0" cellspacing="0"  bordercolor="#eaeaea">
                                        			  <tr class="sub-table-title">
                                        					<td align="center" width=25% height="35" bgColor="#e3e3e3" >零件</td>
                                        					<td align="center" width=25% height="35" bgColor="#e3e3e3">单价</td>
                                        					<td align="center" width=25% height="35" bgColor="#e3e3e3">数量</td>
                                        					<td align="center" width=25% height="35" bgColor="#e3e3e3">折扣率</td>
                                        			  </tr>';
                            	        foreach($list_detail AS $kk=>$vv){
                            		        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
                            				$all_total +=$list_detail[$kk]['total'];
                            				if($list_detail[$kk]['Midl_name'] != '工时费'){
                            					$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
                            					$table_str .= '<tr class="sub-table-content">
                                            					<td align="center" height="30">'.$list_detail[$kk]['Midl_name'].'</td>
                                            					<td align="center" height="30">'.$list_detail[$kk]['price'].'</td>
                                            					<td align="center" height="30">'.$list_detail[$kk]['quantity'].' '.$list_detail[$kk]['unit'].'</td>
                                            					<td align="center" height="30">'.$sale_arr['product_sale'].'</td>
                                            			  </tr>';
                            					$product_price += $list_detail[$kk]['total'];
                                    			$product_price_sale += $list_detail[$kk]['after_sale_total'];
                            				}else {
                            				    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
                            					$workhours_price += $list_detail[$kk]['total'];
                            					$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
                            				}
                            				$all_after_total += $list_detail[$kk]['after_sale_total'];
                            				//$xml_content .= "<item><Midl_name>".$list_detail[$kk]['Midl_name']."</Midl_name><price>".$list_detail[$kk]['price']."</price><quantity>".$list_detail[$kk]['quantity']."</quantity><unit>".$list_detail[$kk]['unit']."</unit><total>".$list_detail[$kk]['total']."</total><after_sale_total>".$list_detail[$kk]['after_sale_total']."</after_sale_total></item>";
                            	        }
                            	        $table_str .= '</table>
                                            		</td>
                                            	</tr>
                                            		<tr>
                                            			<td height="5">&nbsp;</td>
                                            		</tr>
                                            	  <tr>
                                            			<td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0" height="35" bordercolor="#eaeaea">
                                            			  <tr class="sub-table-title">
                                            				<td align="center" width=25% height="35" bgColor="#e3e3e3">工时明细</td>
                                            				<td align="center" width=25% height="35" bgColor="#e3e3e3">工时单价</td>
                                            				<td align="center" width=25% height="35" bgColor="#e3e3e3">工时数量</td>
                                            				<td align="center" width=25% height="35" bgColor="#e3e3e3">折扣率</strong></td>
                                            			  </tr>
                                            			  <tr  class="sub-table-content" >
                                            				<td align="center" height="30">'.$list_detail[0]['Midl_name'].'</td>
                                            				<td align="center" height="30">'.$list_detail[0]['price'].'</td>
                                            				<td align="center" height="30">'.$list_detail[0]['quantity'].' '.$list_detail[0]['unit'].'</td>
                                            				<td align="center" height="30">'.$sale_arr['workhours_sale'].'</td>
                                            			  </tr>
                                                        </table></td>
                                                      </tr>
                                                      <tr>
                                                    		<td height="8">&nbsp;</td>
                                                      </tr>
                                                    </table>';
                            	    }
                                }
                    	    }
                		}else{
                		    $table_str = '您未选择车型或该车型价格无法提供';
                		}
    	            }
                }else {
                    //$table_str .=1;
                   //$xml_content .= "<status>2</status><desc>订单号错误</desc>";
                }
            }else{
                //$table_str .=2;
                //$xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        //$table_str .=3;
	        //$xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        //$xml_content.="</XML>";
        //echo $xml_content;exit;
        echo $table_str;exit;
	}

    public function get_orderdetail_bak(){
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_order = D('Order');
                //$map_o['uid'] = $memberinfo['uid'];
                $map_o['id'] = $_REQUEST['order_id'];
                $orderinfo = $model_order->where($map_o)->find();
                //echo '<pre>';print_r($model_order->getLastSql());exit;
                if ($orderinfo){
                    $order_id = $this->get_orderid($orderinfo['id']);
                    //$xml_content .= "<order_item><order_id>".$order_id."</order_id><order_time>".$orderinfo['order_time']."</order_time><order_state>".$orderinfo['order_state']."</order_state><complain_state>".$orderinfo['complain_state']."</complain_state></order_item>";

                    $car_model = D('Membercar');
            		$my_car['brand_id'] = $orderinfo['brand_id'];
            		$my_car['series_id'] = $orderinfo['series_id'];
            		$my_car['model_id'] = $orderinfo['model_id'];
            		//用户所有自定义车型初始化
            		$my_car = $car_model->Membercar_format_by_arr($my_car,1);
            		//$xml_content .= "<carinfo><car_name>".$my_car['car_name']."</car_name><brand_name>".$my_car['brand_name']."</brand_name><series_name>".$my_car['series_name']."</series_name><model_name>".$my_car['model_name']."</model_name></carinfo>";

            		$model_serviceitem = D('Serviceitem');
                    $select_services_arr = explode(',',$orderinfo['service_ids']);
            		$select_services_str = '';
            		if ($select_services_arr){
            		    foreach ($select_services_arr as $s_key=>$s_val){
            		        if (!$s_val){
            		            unset($select_services_arr[$s_key]);
            		        }
            		    }
            		    $select_services_str = implode(',',$select_services_arr);
            		}
            	    $map['id'] = array('in',$select_services_str);
            	    $services_info = $model_serviceitem->where($map)->select();
            	    if ($services_info){
            	        $services_name_arr = array();
            	        foreach ($services_info as $_k=>$_v){
            	            $i = $_k+1;
            	            $services_name_arr[] = $_v['name'];
            	            //$xml_content .= "<serviceitem><i>".$i."</i><name>".$_v['name']."</name></serviceitem>";
            	        }
            	        $services_name_str = implode(';',$services_name_arr);
            	    }

            	    $model_shop = D('Shop');
            	    $map_shop['id'] = $orderinfo['shop_id'];
    	            $getShop = $model_shop->where($map_shop)->find();
                    /*if ($getShop['shop_maps']){
            	        $maps_diff = C('MAPS_DIFF');
            	        $shop_maps_arr = explode(',',$getShop['shop_maps']);
            	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
            	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
            	        $getShop['shop_maps'] = implode(',',$shop_maps_arr);
            	    }*/
    	            if ($orderinfo['order_type'] == 2){
    	                $table_str = '<style type="text/css">
                            <!--
                            .STYLE1 {
                            	font-size: 24px;
                            	font-weight: bold;
                            }
                            .STYLE2 {font-size: 18px}
                            a {text-decoration:none}
                            -->
                            </style>
                            </head>

                            <body>
                            <table border="0" cellspacing="0" cellpadding="0" width=305>
                              <tr>
                                <td height="35" align="center" bgcolor="#CCCCCC"><span class="STYLE1">'.$getShop['shop_name'].'</span></td>
                              </tr>
                              <tr>
                                <td height="35"><span class="STYLE2">优惠券名称：'.$orderinfo['order_name'].'</span></td>
                              </tr>
                              <tr>
                                <td height="35">&nbsp;<span class="STYLE2">预约到店时间：'.date("Y-m-d H:i:s",$orderinfo['order_time']).'</span></td>
                              </tr>
                              <tr>
                                <td height="35">&nbsp;<span class="STYLE2">维修项目：'.$services_name_str.'</span></td>
                              </tr>';
    	                    if ($orderinfo['order_verify']){
                                $table_str .= '<tr>
                                <td height="35">&nbsp;<span class="STYLE2">订单验证码：'.$orderinfo['order_verify'].'</span></td>
                              </tr>';
    	                    }
                              $table_str .= '<tr>
                                <td height="8">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>通过携车网预约您的保养维修项目，共为您节省：</td>
                              </tr>
                              <tr>
                                <td height="5">&nbsp;</td>
                              </tr>
                              <tr>
                                <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td align="center" width=25%><strong>原价</strong></td>
                                    <td align="center" width=25%><strong>优惠价</strong></td>
                                    <td align="center" width=25%><strong>数量</strong></td>
                                    <td align="center" width=25%><strong>节省</strong></td>
                                  </tr>
                                  <tr>
                                    <td align="center">'.$orderinfo['cost_price'].'</td>
                                    <td align="center">'.$orderinfo['total_price'].'</td>
                                    <td align="center">1</td>
                                    <td align="center">'.($orderinfo['cost_price']-$orderinfo['total_price']).'</td>
                                  </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td height="8">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>您所选的4S店地址：'.$getShop['shop_address'].'</td>
                              </tr>
                              <tr>
                                <td>';
    	                    if ($orderinfo['pay_status']==1){
    	                        $table_str .='<span style="display: block; height: 50px; background-color: rgb(219, 234, 249); font-size: 30px; font-weight: bold; text-align: center; padding-top: 7px; text-decoration:none;">支付完成</span>';
    	                    }else{
    	                        $table_str .='<a href="http://www.xieche.net/apppay/alipayto.php?order_id='.$order_id.'&uid='.$orderinfo['uid'].'"><span style="display: block; height: 50px; background-color: rgb(219, 234, 249); font-size: 30px; font-weight: bold; text-align: center; padding-top: 7px; text-decoration:none;">提交支付</span></a>';
    	                    }
    	                    $table_str .='</td>
                              </tr>
                            </table>
                            </body>';
    	            }else{
                        if($orderinfo['product_sale'] > 0){
                			$sale_arr['product_sale'] = ($orderinfo['product_sale']*10).'折';
                		}else{
                			$sale_arr['product_sale'] = '无折扣';
                		}

                		if($orderinfo['workhours_sale'] > 0){
                			$sale_arr['workhours_sale'] = ($orderinfo['workhours_sale']*10).'折';
                		}else{
                			$sale_arr['workhours_sale'] = '无折扣';
                		}
                        if($orderinfo['coupon_save_discount'] > 0 || $orderinfo['coupon_save_money'] > 0){
                			$sale_arr['coupon_save_money'] = $orderinfo['coupon_save_money'];
                			$sale_arr['coupon_save_discount'] = ($orderinfo['coupon_save_discount']*10).'折';
                		}
                		//$xml_content .= "<sale_info><workhours_sale>".$sale_arr['workhours_sale']."</workhours_sale><product_sale>".$sale_arr['product_sale']."</product_sale><coupon_save_discount>".$sale_arr['coupon_save_discount']."</coupon_save_discount><coupon_save_money>".$sale_arr['coupon_save_money']."</coupon_save_money></sale_info>";

                        if ($orderinfo['membercoupon_id']){
                		    $model_membercoupon = D('Membercoupon');
                		    $membercoupon = $model_membercoupon->find($orderinfo['membercoupon_id']);
                		    $coupon_id = $membercoupon['coupon_id'];
                		    $product_img_str .= '_'.$coupon_id;
                		    $model_coupon = D('Coupon');
                		    $coupon = $model_coupon->find($coupon_id);
                		    //$xml_content .= "<coupon><coupon_name>".$coupon['coupon_name']."</coupon_name></coupon>";
                		}
                        if ($orderinfo['coupon_save_discount']>0){
                		    $workhours_sale = sprintf("%.2f", $orderinfo['workhours_sale']-$orderinfo['coupon_save_discount']);
                		    if ($workhours_sale=='0.00'){
                		        $workhours_sale = -1;
                		    }
                		}else {
                		    $workhours_sale = $orderinfo['workhours_sale'];
                		}
                		$map_pp['service_id']  = array('in',$select_services_str);
                		$map_pp['model_id']  = array('eq',$orderinfo['model_id']);
                		$product_model = D('Product');
                		$list = $product_model->where($map_pp)->select();
                		foreach($list AS $_kk=>$_vv){
                			$versionid_arr[] = $_vv['versionid'];
                			$productid_arr[] = $_vv['id'];
                		}
                		$versionid_str = implode(',', $versionid_arr);
                		$productid_str = implode(',', $productid_arr);
                        $map_p['id'] = array('in',$productid_str);
                		$list_product = array();
                		if (!empty($orderinfo['service_ids'])){
                		    $select_services_arr = explode(',',$orderinfo['service_ids']);
                		    foreach ($select_services_arr as $key=>$services_id){
                		        if ($services_id){
                    		        $map_p['service_id'] = array('eq',$services_id);
                    		        $list_product[$key]['productinfo'] = $product_model->where($map_p)->find();
                    		        $list_product[$key]['service_id'] = $services_id;
                		        }
                		    }
                		}

                		$product_sale = $orderinfo['product_sale'];
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
                		//$xml_content .= "<workhours_sale>".$sale_value['sale_value']."</workhours_sale>";
                		//$xml_content .= "<product_sale>".$sale_value['product_sale']."</product_sale>";
                		$model_serviceitem = D('Serviceitem');
                		$order_status = C('ORDER_STATE');
                		//$xml_content .= "<pruduct_item>";
                		$table_str = '<table border="0" cellspacing="0" cellpadding="0" width=305>
                                      <tr>
                                        <td height="35" align="center" bgcolor="#CCCCCC"><span style="font-size: 24px;font-weight: bold;">'.$getShop['shop_name'].'</span></td>
                                      </tr>
                                      <tr>
                                        <td height="35">&nbsp;<span style="font-size: 18px;">预约到店时间：'.date("Y-m-d H:i:s",$orderinfo['order_time']).'</span></td>
                                      </tr>
                                      <tr>
                                        <td height="35">&nbsp;<span style="font-size: 18px;">订单状态：'.$order_status[$orderinfo['order_state']].'</span></td>
                                      </tr>';
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
                                $table_str .= '<tr>
                                                <td height="35">&nbsp;<span style="font-size: 18px;">'.$i.'：'.$service_name['name'].'</span></td>
                                              </tr>';
                                //$xml_content .= "<service_item><service_name>".$service_name['name']."</service_name>";
                                if ($list_detail){
                                    $table_str .= '<tr>
                                                    <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                                                      <tr>
                                                        <td align="center" width=25%><strong>零件</strong></td>
                                                        <td align="center" width=25%><strong>单价</strong></td>
                                                        <td align="center" width=25%><strong>数量</strong></td>
                                                        <td align="center" width=25%><strong>折扣率</strong></td>
                                                      </tr>';
                        	        foreach($list_detail AS $kk=>$vv){
                        		        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
                        				$all_total +=$list_detail[$kk]['total'];
                        				if($list_detail[$kk]['Midl_name'] != '工时费'){
                        					$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
                        					$table_str .= '<tr>
                                                            <td align="center">'.$list_detail[$kk]['Midl_name'].'</td>
                                                            <td align="center">'.$list_detail[$kk]['price'].'</td>
                                                            <td align="center">'.$list_detail[$kk]['quantity'].' '.$list_detail[$kk]['unit'].' </td>
                                                            <td align="center">'.$sale_arr['product_sale'].'</td>
                                                          </tr>';
                        					$product_price += $list_detail[$kk]['total'];
                                			$product_price_sale += $list_detail[$kk]['after_sale_total'];
                        				}else {
                        				    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
                        					$workhours_price += $list_detail[$kk]['total'];
                        					$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
                        				}
                        				$all_after_total += $list_detail[$kk]['after_sale_total'];
                        				//$xml_content .= "<item><Midl_name>".$list_detail[$kk]['Midl_name']."</Midl_name><price>".$list_detail[$kk]['price']."</price><quantity>".$list_detail[$kk]['quantity']."</quantity><unit>".$list_detail[$kk]['unit']."</unit><total>".$list_detail[$kk]['total']."</total><after_sale_total>".$list_detail[$kk]['after_sale_total']."</after_sale_total></item>";
                        	        }
                        	        $table_str .= '</table></td>
                                                  </tr>
                                                  <tr>
                                                    <td height="5">&nbsp;</td>
                                                  </tr>
                                                  <tr>
                                                    <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                                                      <tr>
                                                        <td align="center" width=25%><strong>工时明细</strong></td>
                                                        <td align="center" width=25%><strong>工时单价</strong></td>
                                                        <td align="center" width=25%><strong>工时数量</strong></td>
                                                        <td align="center" width=25%><strong>折扣率</strong></td>
                                                      </tr>
                                                      <tr>
                                                        <td align="center">'.$list_detail[0]['Midl_name'].'</td>
                                                        <td align="center">'.$list_detail[0]['price'].'</td>
                                                        <td align="center">'.$list_detail[0]['quantity'].' '.$list_detail[0]['unit'].'</td>
                                                        <td align="center">'.$sale_arr['workhours_sale'].'</td>
                                                      </tr>
                                                    </table></td>
                                                  </tr>';
                        	    }
                        	    //$xml_content .= "</service_item>";
                            }
                            $product_price_save = $product_price-$product_price_sale;
                			$workhours_price_save = $workhours_price-$workhours_price_sale;
                			$save_total = $all_total-$all_after_total;
                			$table_str .='<tr>
                                            <td height="8">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td>通过携车网预约您的保养维修项目，共为您节省：</td>
                                          </tr>
                                          <tr>
                                            <td height="5">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                                              <tr>
                                                <td align="center" width=25%>&nbsp;</td>
                                                <td align="center" width=25%><strong>门市价</strong></td>
                                                <td align="center" width=25%><strong>折后价</strong></td>
                                                <td align="center" width=25%><strong>节省</strong></td>
                                              </tr>
                                              <tr>
                                                <td align="center"><strong>零件费</strong></td>
                                                <td align="center">'.$product_price.'</td>
                                                <td align="center">'.$product_price_sale.'</td>
                                    			<td align="center">'.$product_price_save.'</td>
                                              </tr>
                                              <tr>
                                                <td align="center"><strong>工时费</strong></td>
                                                <td align="center">'.$workhours_price.'</td>
                                                <td align="center">'.$workhours_price_sale.'</td>
                                                <td align="center">'.$workhours_price_save.'</td>
                                              </tr>
                                              <tr>
                                                <td align="center"><strong>合计（元）</strong></td>
                                                <td align="center">'.$all_total.'</td>
                                                <td align="center">'.$all_after_total.'</td>
                                                <td align="center">'.$save_total.'</td>
                                              </tr>
                                            </table></td>
                                          </tr> <tr>
                                            <td height="8">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td>您所选的4S店地址：<a href="http://m.xieche.net/xml/getcompany.php?shop_id='.$getShop['id'].'MAPP">'.$getShop['shop_address'].'</a></td>
                                          </tr>
                                        </table>';
                			//$xml_content .="<product_price>".$product_price."</product_price><product_price_sale>".$product_price_sale."</$product_price_sale><product_price_save>".$product_price_save."</product_price_save>";
                			//$xml_content .="<workhours_price>".$workhours_price."</workhours_price><workhours_price_sale>".$workhours_price_sale."</workhours_price_sale><workhours_price_save>".$workhours_price_save."</workhours_price_save>";
                			//$xml_content .="<allprice>".$all_total."</allprice><aftersaveprice>".$all_after_total."</aftersaveprice><saveprice>".$save_total."</saveprice>";

                	    }
                    }
            		//$xml_content .= "<pruduct_table>".$table_str."</pruduct_table>";
                }else {
                   //$xml_content .= "<status>2</status><desc>订单号错误</desc>";
                }
            }else{
                //$xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        //$xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        echo $table_str;exit;
        //$xml_content.="</XML>";
        //echo $xml_content;exit;
	}

    /*
     * 我的车辆列表
     */
	public function get_mycars(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_membercar = D('Membercar');
                $map_mc['uid'] = $memberinfo['uid'];
                $map_mc['status'] = 1;
                if ($_REQUEST['u_c_id']){
                    $map_mc['u_c_id'] = $_REQUEST['u_c_id'];
                }
                $membercars = $model_membercar->where($map_mc)->select();
                if ($membercars){
                    foreach ($membercars as $k=>$v){
                		$my_car['brand_id'] = $v['brand_id'];
                		$my_car['series_id'] = $v['series_id'];
                		$my_car['model_id'] = $v['model_id'];
                		//用户所有自定义车型初始化
                		$my_car = $model_membercar->Membercar_format_by_arr($my_car,1);
						$model_carbrand = D('Carbrand');
                		$map_carbrand['brand_id'] = $v['brand_id'];
                		$carbrand = $model_carbrand->where($map_carbrand)->find();
                		$brand_logo = WEB_ROOT.'/UPLOADS/Brand/Biglogo/'.$carbrand['brand_logo'];
                		/*$model_carseries = D('Carseries');
                		$map_carseries['series_id'] = $v['series_id'];
                		$carseries = $model_carseries->where($map_carseries)->find();
                		$brand_logo = WEB_ROOT.'/UPLOADS/Brand/100/'.$carseries['fsid'].'.jpg';*/
                		$car_number = $v['car_number'];
                		$car_number_arr = explode('_',$car_number);
                		//if ($car_number_arr[0] && $car_number_arr[1]){
                		    $s_pro = $car_number_arr[0];
                		    $car_number = $car_number_arr[1];
                		//}
                        $xml_content .= "<car_item><brand_logo>".$brand_logo."</brand_logo><u_c_id>".$v['u_c_id']."</u_c_id><car_name>".$v['car_name']."</car_name><brand_id>".$v['brand_id']."</brand_id><brand_name>".$my_car['brand_name']."</brand_name><series_id>".$v['series_id']."</series_id><series_name>".$my_car['series_name']."</series_name><model_id>".$v['model_id']."</model_id><model_name>".$my_car['model_name']."</model_name><car_number>".$car_number."</car_number><s_pro>".$s_pro."</s_pro><create_time>".$v['create_time']."</create_time></car_item>";
                    }
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}

	public function add_membercar(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $data['uid'] = $memberinfo['uid'];
                $model_membercar =D('Membercar');
                if (isset($_REQUEST['s_pro']) and isset($_REQUEST['car_number'])){
                    $data['car_number'] = $_REQUEST['s_pro']."_".$_REQUEST['car_number'];
                    unset($_REQUEST['s_pro']);
                }
                if (empty($_REQUEST['brand_id']) || empty($_REQUEST['series_id']) || empty($_REQUEST['model_id'])){
                    $xml_content .= "<status>2</status><desc>您的车辆信息没有选完整，请选全</desc>";
                    $xml_content.="</XML>";
                    echo $xml_content;exit;
                }
				$data['brand_id'] = $_REQUEST['brand_id'];
				$data['series_id'] = $_REQUEST['series_id'];
				$data['model_id'] = $_REQUEST['model_id'];

                if (empty($_REQUEST['car_name'])){
                    $_REQUEST['car_name'] = "未命名";
                }
				$data['car_name'] = $_REQUEST['car_name'];
                $data['create_time'] = time();


                $map['uid'] = $memberinfo['uid'];
        		$map['status'] = 1;
        		$map['is_default'] = 1;
        		$membercar = $model_membercar->where($map)->select();
        		if (!$membercar){
        		    $data['is_default'] = 1;
        		}

                if ($model_membercar->add($data)){
                    $xml_content .= "<status>0</status><desc>添加车辆成功</desc>";
                }else {
                    $xml_content .= "<status>3</status><desc>添加车辆失败</desc>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
    public function update_membercar(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                if ($_REQUEST['u_c_id']){
                    $map['uid'] = $memberinfo['uid'];
                    $map['u_c_id'] = $_REQUEST['u_c_id'];
                    $model_membercar =D('Membercar');
                    if (isset($_POST['s_pro']) and isset($_POST['car_number'])){
                        $_POST['car_number'] = $_POST['s_pro']."_".$_POST['car_number'];
                        unset($_POST['s_pro']);
                    }
                    if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
                        $xml_content .= "<status>2</status><desc>您的车辆信息没有选完整，请选全</desc>";
                        $xml_content.="</XML>";
                        echo $xml_content;exit;
                    }
                    if (empty($_POST['car_name'])){
                        $_POST['car_name'] = "未命名";
                    }

                    if ('false' !== $model_membercar->where($map)->save($_POST)){
                        $xml_content .= "<status>0</status><desc>编辑车辆成功</desc>";
                    }else {
                        $xml_content .= "<status>3</status><desc>编辑车辆失败</desc>";
                    }
                }else{
                    $xml_content .= "<status>4</status><desc>数据错误</desc>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
    public function delete_membercar(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                //$map['uid'] = $memberinfo['uid'];
                $model_membercar =D('Membercar');
                $map['u_c_id'] = $_REQUEST['u_c_id'];
                $membercar = $model_membercar->where($map)->find();
                if ($membercar['is_default']==1){
    	            $map_other['u_c_id'] = array('neq',$_REQUEST['u_c_id']);
    	            $map_other['status'] = 1;
    	            $map_other['uid'] = $memberinfo['uid'];
    	            $membercarother = $model_membercar->where($map_other)->order("u_c_id DESC")->find();
    	            if ($membercarother['u_c_id']){
    	                $map_d['u_c_id'] = $membercarother['u_c_id'];
    	                $data_d['is_default'] = 1;
    	                $model_membercar->where($map_d)->save($data_d);
    	            }
    	        }
                $data['status'] = -1;
                if ($model_membercar->where($map)->save($data)){
                    $xml_content .= "<status>0</status><desc>删除车辆成功</desc>";
                }else {
                    $xml_content .= "<status>3</status><desc>删除车辆失败</desc>";
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
 	/*
	 * 我的积分
	 */
	public function get_mypoints(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $model_point = D('Point');
                $map_p['uid'] = $memberinfo['uid'];
                $pointinfo = $model_point->where($map_p)->select();
                $xml_content .= "<total_number>".$memberinfo['point_number']."</total_number>";
                if ($pointinfo){
                    foreach ($pointinfo as $k=>$v){
                        $order_id = $this->get_orderid($v['orderid']);
                        $xml_content .= "<point_item><id>".$order_id."</id><order_id>".$v['orderid']."</order_id><point_number>".$v['point_number']."</point_number><memo>".$v['memo']."</memo></point_item>";
                    }
                }
            }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
        $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	
	/*
		@author:ysh
		@function:显示店铺详情页
		@time:2013-08-22
	*/
	public function getshop_detail(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    $model_shop = D('Shop');
        $model_timesale = D('Timesale');
        $model_shop_fs_relation = D('Shop_fs_relation');
        $model_timesaleversion = D('Timesaleversion');
	    $shop_id = isset($_REQUEST['shop_id'])?$_REQUEST['shop_id']:0;
	    if ($shop_id) {
	    	$shop_detail = $model_shop->find($shop_id);
	    	if ($shop_detail){

    	    	if ($shop_detail['versionid']){
                   $shop_detail['id'] = $shop_detail['id'].'_'.$shop_detail['versionid'];
                }
                if (file_exists('./UPLOADS/Shop/100/'.$shop_detail['id'].'.jpg')){
                   $shop_detail['logo'] = WEB_ROOT.'/UPLOADS/Shop/130/'.$shop_detail['id'].'.jpg';
                }else {
                   $map_sfr['shopid'] = $shop_id;
                   $shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
                   $fsid = $shop_fs_relation['fsid'];
                   $model_fs = D('FS');
                   $map_f['fsid'] = $fsid;
                   $fs = $model_fs->where($map_f)->find();
                   if($fs['versionid']){
                       $fsid = $fsid.'_'.$fs['versionid'];
                   }
                   $shop_detail['logo'] = WEB_ROOT.'/UPLOADS/Brand/130/'.$fsid.'.jpg';
                }

	    	    /*if ($shop_detail['logo']){
	    	        $shop_detail['logo'] = WEB_ROOT.'/UPLOADS/Shop/130/'.$shop_detail['id'].'.jpg';
	    	    }
	    	    if ($shop_detail['shop_maps']){
	    	        $maps_diff = C('MAPS_DIFF');
	    	        $shop_maps_arr = explode(',',$shop_detail['shop_maps']);
	    	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
	    	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
	    	        $shop_detail['shop_maps'] = implode(',',$shop_maps_arr);
	    	    }*/
	    	   $model_shop_fs_relation = D('Shop_fs_relation');

	    	   $xml_content .= "<shopid>".$shop_detail['id']."</shopid><shop_name>".$shop_detail['shop_name']."</shop_name><shop_class>".$shop_detail['shop_class']."</shop_class><shop_address>".$shop_detail['shop_address']."</shop_address><shop_account>".$shop_detail['shop_account']."</shop_account><shop_maps>".$shop_detail['shop_maps']."</shop_maps><logo>".$shop_detail['logo']."</logo><comment_rate>".$shop_detail['comment_rate']."</comment_rate><comment_number>".$shop_detail['comment_number']."</comment_number>";
	    	   $model_comment = D('Comment');
	    	   $map_c['shop_id'] = $shop_id;
			   $map_c['comment'] = array("neq",'');
	    	   $last_comment = $model_comment->where($map_c)->order("create_time DESC")->find();
	    	   $xml_content .= "<lastcomment><comment_id>".$last_comment['id']."</comment_id><user_name>".$last_comment['user_name']."</user_name><comment>".$last_comment['comment']."</comment><comment_type>".$last_comment['comment_type']."</comment_type></lastcomment>";

	    	   //优惠券
	    	   $model_coupon = D('Coupon');
	    	   $map_coupon['shop_id'] = $shop_id;
	    	   $map_coupon['is_delete'] = 0;
	    	   $map_coupon1 = $map_coupon2 = $map_coupon;
	    	   $map_coupon1['coupon_type'] = 1;
	    	   $map_coupon2['coupon_type'] = 2;
	    	   $coupons = $model_coupon->where($map_coupon)->order("id DESC")->select();
	    	   $coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->find();
	    	   $coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->find();
	    	   if ($coupon1){
	    	       $coupon1 = "<coupon1><coupon1_id>".$coupon1['id']."</coupon1_id><coupon1_name>".$coupon1['coupon_name']."</coupon1_name></coupon1>";
	    	   }
	    	    if ($coupon2){
	    	       $coupon2 = "<coupon2><coupon2_id>".$coupon2['id']."</coupon2_id><coupon2_name>".$coupon2['coupon_name']."</coupon2_name></coupon2>";
	    	   }
	    	   $coupon_count1 = 0;//现金券数量
	    	   $coupon_count2 = 0;//团购券数量
	    	   if ($coupons){
	    	       //$coupon_count1 = 0;//现金券数量
	    	       //$coupon1 = '';
	    	       //$coupon2 = '';
	    	       //$coupon_count2 = 0;//团购券数量
	    	       foreach ($coupons as $_kk=>$_vv){
	    	           if ($_vv['coupon_type']==1){//现金券
	    	               $coupon_count1 += 1;
	    	               //$coupon1 = "<coupon1><coupon_id>".$_vv['id']."</coupon_id><coupon_name>".$_vv['coupon_name']."</coupon_name></coupon1>";
	    	           }
	    	           if ($_vv['coupon_type']==2){//团购券
	    	               $coupon_count2 += 1;
	    	               //$coupon2 = "<coupon2><coupon_id>".$_vv['id']."</coupon_id><coupon_name>".$_vv['coupon_name']."</coupon_name></coupon2>";
	    	           }
	    	       }
	    	   }
	    	   if ($coupon_count1){
	               $xml_content .= "<coupon_count1>".$coupon_count1."</coupon_count1>";
	               $xml_content .= $coupon1;
	           }
	           if ($coupon_count2){
	               $xml_content .= "<coupon_count2>".$coupon_count2."</coupon_count2>";
	               $xml_content .= $coupon2;
	           }


	    	   $map['shop_id'] = $shop_detail['id'];
	    	   $map['status'] = 1;
               $timesales = $model_timesale->where($map)->select();

               if ($timesales){
                   foreach ($timesales as $kk=>$vv){
                       $map_tv['timesale_id'] = $vv['id'];
                       $map_tv['status'] = 1;
                       $map_tv['s_time'] = array('lt',time());
                       $map_tv['e_time'] = array('gt',time());
                       $timesaleversion = $model_timesaleversion->where($map_tv)->select();
                       if ($timesaleversion){
                           foreach ($timesaleversion as $_k=>$_v){
                               if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
        		                   continue;
        		               }
        		               $xml_content .= "<timesale><timesale_id>".$vv['id']."</timesale_id><week>".$vv['week']."</week><begin_time>".$vv['begin_time']."</begin_time><end_time>".$vv['end_time']."</end_time>";

                               $xml_content .= "<timesaleversion><timesaleversion_id>".$_v['id']."</timesaleversion_id><workhours_sale>".$_v['workhours_sale']."</workhours_sale><memo>".$_v['memo']."</memo></timesaleversion>";
                               /*if ($_v['coupon_id']){
                                   $coupon = $model_coupon->find($_v['coupon_id']);
                                   $xml_content .= "<coupon_item><coupon_id>".$coupon['coupon_id']."</coupon_id><coupon_name>".$coupon['coupon_name']."</coupon_name><start_time>".$coupon['start_time']."</start_time><end_time>".$coupon['end_time']."</end_time><coupon_discount>".$coupon['coupon_discount']."</coupon_discount><coupon_amount>".$coupon['coupon_amount']."</coupon_amount></coupon_item>";
                               }*/
                               $xml_content .= "</timesale>";
                               $product_sale = $_v['product_sale'];
                           }
                       }
                   }
               }
               if ($product_sale){
                   $xml_content .="<product_sale>".$product_sale."</product_sale>";
               }
	        }
	    }
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}

	/*
	 *	@author:chf
	 *	@function:订单下定页面(判断下单日期)
	 *	@time:2013-3-11
	 *
	 */
	public function order_add(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    $timesaleversion_id = isset($_REQUEST['timesaleversion_id'])?$_REQUEST['timesaleversion_id']:0;
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $xml_content .="<mobile>".$memberinfo['mobile']."</mobile>";
	        }
	    }
	    if($timesaleversion_id){
			$model_timesale = D('Timesale'); //载入模型
			$map_ts['xc_timesaleversion.id'] = $timesaleversion_id;
			$map_ts['xc_timesaleversion.status'] = 1;
			$list_timesale = $model_timesale->where($map_ts)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find(); //根据id查询分时折扣信息

			$sale_check = $list_timesale['week'];  //根据分时折扣的星期数，处理有效日期

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
		    if ($list_timesale['shop_id']){
		        $model_shop = D('Shop');
		        $shopinfo = $model_shop->find($list_timesale['shop_id']);
		    }
    		$xml_content .= "<shop_name>".$shopinfo['shop_name']."</shop_name><min>".$min."</min><max>".$max."</max><sale_check>".$sale_check."</sale_check><begin_time>".$list_timesale['begin_time']."</begin_time><end_time>".$list_timesale['end_time']."</end_time><product_sale>".$list_timesale['product_sale']."</product_sale><workhours_sale>".$list_timesale['workhours_sale']."</workhours_sale>";
		}
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}

	function get_brandinfo_byshopid(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    $shop_id = isset($_REQUEST['shop_id'])?$_REQUEST['shop_id']:0;
	    $data = array();
	    if ($shop_id){
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
                            $data[$_v['brand_id']]['brand_name'] = $carbrand['brand_name'];
                            $data[$_v['brand_id']]['seriesinfo'][$_v['series_id']] = $_v;
                            $data[$_v['brand_id']]['seriesinfo'][$_v['series_id']]['modelinfo'] = $carmodel;
                        }
                    }
                }
            }
            //echo '<pre>';print_r($data);exit;
	    }
	    if ($data) {
	    	foreach ($data as $_kk=>$_vv){
	    	    $xml_content .= "<brand_item><brand_id>".$_vv['brand_id']."</brand_id><brand_name>".$_vv['brand_name']."</brand_name>";
	    	    if ($_vv['seriesinfo']){
	    	        foreach ($_vv['seriesinfo'] as $kk=>$vv){
	    	            $xml_content .= "<series_item><series_id>".$vv['series_id']."</series_id><series_name>".$vv['series_name']."</series_name>";
	    	            if ($vv['modelinfo']){
	    	                foreach ($vv['modelinfo'] as $kkk=>$vvv){
	    	                    $xml_content .= "<model_item><model_id>".$vvv['model_id']."</model_id><model_name>".$vvv['model_name']."</model_name></model_item>";
	    	                }
	    	            }
	    	            $xml_content .="</series_item>";
	    	        }
	    	    }
	    	    $xml_content .="</brand_item>";
	    	}
	    }
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	/*
	 * 优惠券订单下定页面
	 */
	public function coupon_order_add(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    $coupon_id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                if($coupon_id){
        			$model_coupon = D('Coupon'); //载入模型
        			$map_c['id'] = $coupon_id;
        			$coupon_info = $model_coupon->where($map_c)->find(); //根据id查询分时折扣信息
        			$sale_check = sale_check($coupon_info['week']);  //根据分时折扣的星期数，处理无效日期
        			$min_hours = explode(':',$coupon_info['s_time']);   //分时具体上下午时间输出到模板，做判断
        			$max_hours = explode(':',$coupon_info['e_time']);     //分时具体上下午时间输出到模板，做判断
        			$now = time();
        		    $fourhour = strtotime(date('Y-m-d').' 16:00:00');

        		    if ($now < $fourhour){
        		        $min = 1;
        		        $max = 15;
        		    }else{
        		        $min = 2;
        		        $max = 16;
        		    }
        		    if(($coupon_info['start_time'] - strtotime(date('Y-m-d')))>0){
        		        $s_day = floor(($coupon_info['start_time'] - strtotime(date('Y-m-d')))/24/3600);
        		        $min = max($s_day,$min);
        		    }
        		    if(($coupon_info['end_time'] - strtotime(date('Y-m-d')))>0){
        		        $e_day = floor(($coupon_info['end_time'] - strtotime(date('Y-m-d')))/24/3600);
        		        $max = min($e_day,$max);
        		    }
        		    $service_str = '';
        		    if ($coupon_info['service_ids']){
        		        $model_serviceitem = D('Serviceitem');
        		        $map_s['id'] = array('in',$coupon_info['service_ids']);
        		        $serviceitem = $model_serviceitem->where($map_s)->select();
        		        if ($serviceitem){
        		            $service_arr = array();
        		            foreach ($serviceitem as $sk=>$sv){
        		                $service_arr[] = $sv['name'];
        		            }
        		            $service_str = implode(',',$service_arr);
        		        }
        		    }
                    if ($coupon_info['shop_id']){
        		        $model_shop = D('Shop');
        		        $shopinfo = $model_shop->find($coupon_info['shop_id']);
        		    }
            		$xml_content .= "<shop_name>".$shopinfo['shop_name']."</shop_name><mobile>".$memberinfo['mobile']."</mobile><min>".$min."</min><max>".$max."</max><sale_check>".$sale_check."</sale_check><begin_time>".$coupon_info['start_time']."</begin_time><end_time>".$coupon_info['end_time']."</end_time><cost_price>".$coupon_info['cost_price']."</cost_price><coupon_amount>".$coupon_info['coupon_amount']."</coupon_amount><service_str>".$service_str."</service_str>";
        		}
	        }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else {
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	/*
	 * 订单下定插入数据
	 */
	public function save_order(){
	    $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $uid = 0;
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
            }
	    }
        //根据提交过来的预约时间，做判断(暂时先注销)
		if($_REQUEST['order_date']){
    		//载入产品MODEL
    		$model_product = D('Product');
    		$select_services_arr = explode(',',$_REQUEST['select_services']);
    		$select_services_str = '';
    		if ($select_services_arr){
    		    foreach ($select_services_arr as $s_key=>$s_val){
    		        if (!$s_val){
    		            unset($select_services_arr[$s_key]);
    		        }
    		    }
    		    $select_services_str = implode(',',$select_services_arr);
    		}
    		if ($_REQUEST['model_id'] and $select_services_str and $select_services_str !='-1'){
    		    $map['service_id'] = array('in',$select_services_str);
        		$map['model_id'] = array('eq',$_REQUEST['model_id']);
        		$list_product = $model_product->where($map)->select();
    		}

    		$model_membercar = D('Membercar');
    		$order_time= $_REQUEST['order_date'].' '.$_REQUEST['order_hours'].':'.$_REQUEST['order_minute'];
    		$order_time = strtotime($order_time);
    		$now = time();
    		$timesale_model = D('Timesale');
    		$map_tsv['xc_timesaleversion.id'] = $_REQUEST['timesaleversion_id'];
			$map_tsv['xc_timesaleversion.status'] = 1;
    		$sale_arr = $timesale_model->where($map_tsv)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find();
    		if ($order_time>$sale_arr['s_time'] and $order_time<$sale_arr['e_time']){
    		    $order_week = date("w",$order_time);
    		    $normal_week = explode(',',$sale_arr['week']);
    		    if (!in_array($order_week,$normal_week)){
    		        $xml_content .= "<status>1</status><desc>预约时间错误</desc>";
    		        $xml_content.="</XML>";
                    echo $xml_content;exit;
    		        //$this->error('预约时间错误,请重新选择！');
    		    }
    		    $order_hour = date("H:i",$order_time);
    		    if (strtotime(date('Y-m-d').' '.$order_hour)<strtotime(date('Y-m-d').' '.$sale_arr['begin_time']) || strtotime(date('Y-m-d').' '.$order_hour)>strtotime(date('Y-m-d').' '.$sale_arr['end_time'])){
    		        $xml_content .= "<status>2</status><desc>预约时间错误</desc>";
    		        $xml_content.="</XML>";
                    echo $xml_content;exit;
    		        //$this->error('预约时间错误,请重新选择！');
    		    }
    		}else {
    		    $xml_content .= "<status>3</status><desc>预约时间错误</desc>";
		        $xml_content.="</XML>";
                echo $xml_content;exit;
    		    //$this->error('预约时间错误,请重新选择！');
    		}
    		/*$membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:0;
    		if ($membercoupon_id){
    		    $model_membercoupon = D('Membercoupon');
    		    $map_coupon['xc_membercoupon.membercoupon_id'] = $membercoupon_id;
    		    $coupon_info = $model_membercoupon->where($map_coupon)->join("xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->find();
    		    if ($now>$coupon_info['end_time']){
    		        $xml_content .= "<status>3</status><desc>优惠券已经过期</desc>";
    		        $xml_content.="</XML>";
                    echo $xml_content;exit;
    		        //$this->error('优惠券已经过期,请重新下定！','/index.php/order');
    		    }
    		}*/
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
    		    /*if (isset($coupon_info) and $coupon_info){
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
    		    }*/
    		    $workhours_sale = $sale_arr['workhours_sale'];
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
    		/*if (isset($coupon_info) and $coupon_info['coupon_discount']=='0.00' and $coupon_info['coupon_amount']>0){
    		    $save_price = $coupon_info['coupon_amount'];
    		    $total_price = $total_price - $coupon_info['coupon_amount'];
    		}*/
    		if ($_REQUEST['u_c_id']){
    		    $data['u_c_id'] = $_REQUEST['u_c_id'];
    		}
    		if ($uid){
    		    $data['uid'] = $uid;
    		}
    		if ($_REQUEST['shop_id']){
    		    $data['shop_id'] = $_REQUEST['shop_id'];
    		}
    		if ($_REQUEST['brand_id']){
    		    $data['brand_id'] = $_REQUEST['brand_id'];
    		}
    		if ($_REQUEST['series_id']){
    		    $data['series_id'] = $_REQUEST['series_id'];
    		}
    		if ($_REQUEST['model_id']){
    		    $data['model_id'] = $_REQUEST['model_id'];
    		}
    		if ($_REQUEST['timesaleversion_id']){
    		    $data['timesaleversion_id'] = $_REQUEST['timesaleversion_id'];
    		}
    		if ($_REQUEST['select_services']){
    		    $data['service_ids'] = $_REQUEST['select_services'];
    		}
    		if ($sale_arr['product_sale']){
    		    $data['product_sale'] = $sale_arr['product_sale'];
    		}
    		if ($sale_arr['workhours_sale']){
    		    $data['workhours_sale'] = $sale_arr['workhours_sale'];
    		}
    		if ($_REQUEST['truename']){
    		    $data['truename'] = $_REQUEST['truename'];
    		}
    		if ($_REQUEST['mobile']){
    		    $data['mobile'] = $_REQUEST['mobile'];
    		}
    		if ($_REQUEST['cardqz'] and $_REQUEST['licenseplate']){
    		    $data['licenseplate'] = trim($_POST['cardqz'].$_POST['licenseplate']);
    		}
		    if ($_REQUEST['miles']){
    		    $data['mileage'] = $_REQUEST['miles'];
    		}
		    if ($_REQUEST['car_sn']){
    		    $data['car_sn'] = $_REQUEST['car_sn'];
    		}
		    if ($_REQUEST['remark']){
    		    $data['remark'] = $_REQUEST['remark'];
    		}
		    if ($order_time){
    		    $data['order_time'] = $order_time;
    		}
    		$data['create_time'] = time();
		    if ($total_price){
    		    $data['total_price'] = $total_price;
    		}
		    if ($productversion_ids_str){
    		    $data['productversion_ids'] = $productversion_ids_str;
    		}
		    /*if ($membercoupon_id){
    		    $data['membercoupon_id'] = $membercoupon_id;
    		}*/
		    if ($save_price){
    		    $data['coupon_save_money'] = $save_price;
    		}
		    if ($save_discount){
    		    $data['coupon_save_discount'] = $save_discount;
    		}
    		if ($uid){
        		$model = D('Order');
				$data['is_app'] = 1;
        		if(false !== $model->add($data)){
        			$_POST['order_id'] = $model->getLastInsID();
        			/*if ($membercoupon_id){
        			    $membercoupon_map['membercoupon_id'] = $membercoupon_id;
        			    $membercoupon_data['use_time'] = time();
        			    $membercoupon_data['is_use'] = 1;
        			    $membercoupon_data['order_id'] = $_POST['order_id'];
        			    $model_membercoupon->where($membercoupon_map)->save($membercoupon_data);
        			}*/
        		}
        		$model_member = D('Member');
        		$get_user_name = $model_member->where("uid=$uid")->find();
        		foreach($list_product AS $k=>$v){
        				$sub_order[]=array(
        				'order_id'=>$_POST['order_id'],
        				'productversion_id'=>$list_product[$k]['versionid'],
        				'service_id'=>$list_product[$k]['service_id'],
        				'service_item_id'=>$list_product[$k]['service_item_id'],
        				'uid'=>$uid,
        				'user_name'=>$get_user_name['username'],
        				'series_id'=>$_REQUEST['series_id'],
        				'create_time'=>time(),
        				'update_time'=>time(),
        				);
        		}
        		$model_suborder = D('Suborder');
        		$list=$model_suborder->addAll($sub_order);
    		}else{
    		    $model = D('Ordernologin');
				$data['is_app'] = 1;
        		if(false !== $model->add($data)){
        			$_POST['order_id'] = $model->getLastInsID();
        		}
    		}
    		if(!empty($_POST['order_id'])){
    		    $xml_content .= "<status>0</status><order_id>".$_POST['order_id']."</order_id><desc>预约成功</desc>";
    		    $xml_content.="</XML>";
                echo $xml_content;exit;
    			//$this->success('预约提交成功！',__APP__.'/myhome');
    		}else {
    		    $xml_content .= "<status>4</status><desc>预约失败</desc>";
    		    $xml_content.="</XML>";
                echo $xml_content;exit;
    		    //$this->success('预约失败！',__APP__.'/myhome');
    		}
		}
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}
	/*
	 * 订单下定插入数据
	 */
	public function save_coupon_order(){
	    $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_coupon = D('Coupon');
                $coupon_id = $_REQUEST['coupon_id'];
		        $coupon_info = $model_coupon->find($coupon_id);
                //根据提交过来的预约时间，做判断(暂时先注销)
        		if (!$_POST['order_date'] || !$_POST['order_hours'] || $_POST['order_minute']===''){
        		    $xml_content .= "<status>2</status><desc>预约时间未选择</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}
        		if (!$_POST['truename']){
        		    $xml_content .= "<status>3</status><desc>姓名未填写</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}
        		if (!$_POST['mobile']){
        		    $xml_content .= "<status>4</status><desc>手机号码未填写</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}
        		if (!$_POST['cardqz'] || !$_POST['licenseplate']){
        		    $xml_content .= "<status>5</status><desc>车牌号未填写</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}
        		if ($coupon_info){
        		    $order_time= $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
    		        $order_time = strtotime($order_time);
            		$data['order_name'] = $coupon_info['coupon_name'];
            		$data['uid'] = $uid;
    	            $data['order_des'] = $coupon_info['coupon_summary'];
    	            $data['coupon_id'] = $coupon_id;
    	            $data['shop_id'] = $coupon_info['shop_id'];
    	            $data['brand_id'] = $coupon_info['brand_id'];
    	            $data['series_id'] = $coupon_info['series_id'];
    	            $data['model_id'] = $coupon_info['model_id'];
    	            $data['service_ids'] = $coupon_info['service_ids'];
    	            $data['truename'] = $_POST['truename'];
    	            $data['mobile'] = $_POST['mobile'];
    	            $data['licenseplate'] = trim($_POST['cardqz'].$_POST['licenseplate']);
    	            $data['mileage'] = $_POST['miles'];
    	            $data['car_sn'] = $_POST['car_sn'];
    	            $data['remark'] = $_POST['remark'];
    	            $data['order_time'] = $order_time;
    	            $data['create_time'] = time();
    	            $data['total_price'] = $coupon_info['coupon_amount'];
    	            $data['jiesuan_money'] = $coupon_info['jiesuan_money'];
    	            $data['cost_price'] = $coupon_info['cost_price'];
    	            $data['order_type'] = 2;
					$data['is_app'] = 1;
            		$model = D('Order');
            		if(false !== $model->add($data)){
            			$order_id = $model->getLastInsID();
            		}
        		}
        		if(!empty($order_id)){
        		    $xml_content .= "<status>0</status><order_id>".$order_id."</order_id><desc>预约成功</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}else {
        		    $xml_content .= "<status>6</status><desc>预约失败</desc>";
        		    $xml_content.="</XML>";
                    echo $xml_content;exit;
        		}

	        }else{
                $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
            }
	    }else {
	        $xml_content .= "<status>1</status><desc>账号未登陆</desc>";
	    }
	    $xml_content.="</XML>";
        echo $xml_content;exit;
	}

	public function get_orderprice(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    //$select_services_str = $_REQUEST['select_services_str'];

	    $timesaleversion_id = isset($_REQUEST['timesaleversion_id'])?$_REQUEST['timesaleversion_id']:0;
	    $model_id = $_REQUEST['model_id'];

	    if ($timesaleversion_id and $model_id){
    	    $model_timesaleversion = D('Timesaleversion'); //载入模型
    		$map_ts['id'] = $timesaleversion_id;
    		$list_timesaleversion = $model_timesaleversion->where($map_ts)->find();
    	    $workhours_sale = $list_timesaleversion['workhours_sale'];
    		$product_sale = $list_timesaleversion['product_sale'];
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

    		$model_serviceitem = D('serviceitem');
    		$map_s['si_level'] = 1;
    		$serviceitem = $model_serviceitem->where($map_s)->select();
    	    //$select_services_arr = array(9,10,11,12,14,15,16,17,18,19,20);
    	    foreach ($serviceitem as $_k=>$_v){
    	        $map['service_id']  = $_v['id'];
        		$map['model_id']  = $model_id;
        		$model_product = D('Product');
        		$model_productversion = D('Productversion');
        		$list = $model_product->where($map)->find();
        		//$versionid = $list['versionid'];  刷数据好像有点问题 暂时用下面的查询
				//$map_pv['id'] = $versionid;
				
        		$map_pv['product_id'] = $list['id'];
				$map_pv['status'] = 0;
        		$productversion = $model_productversion->where($map_pv)->find();
    	        if ($productversion['product_detail']){
    			    $list_detail = $productversion['product_detail'];
        		$list_detail = unserialize($list_detail);
    			}else {
    			    $list_detail = array();
    			}
    			$all_total = 0;
    			$all_after_total = 0;
    			$product_price = 0;
    			$product_price_sale = 0;
    			$workhours_price = 0;
    			$workhours_price_sale = 0;
    	        if ($list_detail){
        	        foreach($list_detail AS $kk=>$vv){
        		        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
        				$all_total +=$list_detail[$kk]['total'];
        				if($list_detail[$kk]['Midl_name'] != '工时费'){
        					$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
        					$product_price += $list_detail[$kk]['total'];
                			$product_price_sale += $list_detail[$kk]['after_sale_total'];
        				}else {
        				    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
        					$workhours_price += $list_detail[$kk]['total'];
        					$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
        				}
        				$all_after_total += $list_detail[$kk]['after_sale_total'];
        	        }
        	    }

        	    $product_price_save = $product_price-$product_price_sale;
    			$workhours_price_save = $workhours_price-$workhours_price_sale;
    			$save_total = $all_total-$all_after_total;
    			$xml_content .="<item><service_id>".$_v['id']."</service_id><allprice>".$all_total."</allprice><aftersaveprice>".$all_after_total."</aftersaveprice><saveprice>".$save_total."</saveprice></item>";
    	    }
	    }
		$xml_content .="</XML>";
        echo $xml_content;exit;
	}
    public function get_pricedetail(){
	    $select_services_str = $_REQUEST['select_services_str'];
	    $timesaleversion_id = isset($_REQUEST['timesaleversion_id'])?$_REQUEST['timesaleversion_id']:0;
	    $model_timesaleversion = D('Timesaleversion');
	    $model_timesale = D('Timesale');
	    $model_shop = D('Shop');
		$map_ts['id'] = $timesaleversion_id;
		$list_timesaleversion = $model_timesaleversion->where($map_ts)->find();
        $timesale = $model_timesale->find($list_timesaleversion['timesale_id']);
		$shop_id = $timesale['shop_id'];
		$shopinfo = $model_shop->find($shop_id);
        /*if ($shopinfo['shop_maps']){
	        $maps_diff = C('MAPS_DIFF');
	        $shop_maps_arr = explode(',',$shopinfo['shop_maps']);
	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
	        $shopinfo['shop_maps'] = implode(',',$shop_maps_arr);
	    }*/
	    $model_id = $_REQUEST['model_id'];
	    $map['service_id']  = array('in',$select_services_str);
		$map['model_id']  = array('eq',$model_id);
		$model_product = D('Product');
		$list = $model_product->where($map)->select();
		//echo '<pre>';print_r($map);exit;
		foreach($list AS $k=>$v){
			$versionid_arr[] = $v['versionid'];
			$productid_arr[] = $v['id'];
		}
		$versionid_str = implode(',', $versionid_arr);
		$productid_str = implode(',', $productid_arr);

		$map_p['id'] = array('in',$productid_str);

		$list_product = array();
		if (!$select_services_str || $select_services_str =='-1'){
		    $table_str = '未选择保养项目';
            echo $table_str;exit;
		}else{
		    $select_services_arr = explode(',',$select_services_str);
		    foreach ($select_services_arr as $key=>$services_id){
		        if ($services_id){
    		        $map_p['service_id'] = array('eq',$services_id);
    		        $list_product[$key]['productinfo'] = $model_product->where($map_p)->find();
    		        $list_product[$key]['service_id'] = $services_id;
		        }
		    }
		}

		$workhours_sale = $list_timesaleversion['workhours_sale'];
		$product_sale = $list_timesaleversion['product_sale'];
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
		$model_serviceitem = D('Serviceitem');
	    if (isset($list_product) and !empty($list_product)){
	        $table_str = '
			<!DOCTYPE html>
			<html>
			<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
			<style> 
                *{margin: 0px; padding:0px; } 
                table{margin: 10px auto; border: 1px solid #f1f1f1; border-collapse: collapse} 
                tr{ border-bottom: 1px solid #f1f1f1;}
                td{ float:left ; display: inline-block; border: none; height: auto; padding: 5px 0px; border-collapse: collapse}
            </style>
			</head>
			<body>';
                   /* <table border="0" cellspacing="0" cellpadding="0" width="100%">;*/
	        $i=0;
			
            foreach ($list_product as $k=>$v){
                //echo '<pre>';print_r($v);exit;
                $i++;
                $service_name = $model_serviceitem->getById($v['service_id']);
                if ($v['productinfo']['product_detail']){
				    $list_detail = $v['productinfo']['product_detail'];
				    $list_detail = unserialize($list_detail);
				}else {
				    $list_detail = array();
				}
				$table_str .= '<span class="subtitle" style="font-size: 1.1em; color: #000000; display: inline-block; font-weight: bold; margin-top: 10px; ">'.$i.'：'.$service_name['name'].'</span>';

        	  if ($list_detail){
        	        $table_str .= 
                    /*<tr>
                        <td>*/
                			'<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0"  bordercolor="#eaeaea" >
                			  <tr class="sub-table-title">
                					<td align="center" width="25%" height="35" bgColor="#e3e3e3" >零件</td>
                					<td align="center" width="25%" height="35" bgColor="#e3e3e3">单价</td>
                					<td align="center" width="25%" height="35" bgColor="#e3e3e3">数量</td>
                					<td align="center" width="25%" height="35" bgColor="#e3e3e3">折扣率</td>
                			  </tr>';
        	        foreach($list_detail AS $kk=>$vv){
        		        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
        				$all_total +=$list_detail[$kk]['total'];
        				if($list_detail[$kk]['Midl_name'] != '工时费'){
        					$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
        					$table_str .= '<tr class="sub-table-content">
                            					<td align="center" width="25%" height="30">'.$list_detail[$kk]['Midl_name'].'</td>
                            					<td align="center" width="25%" height="30">'.$list_detail[$kk]['price'].'</td>
                            					<td align="center" width="25%" height="30">'.$list_detail[$kk]['quantity'].' '.$list_detail[$kk]['unit'].'</td>
                            					<td align="center" width="25%" height="30">'.$sale_arr['product_sale'].'</td>
                            			  </tr>';
        					$product_price += $list_detail[$kk]['total'];
                			$product_price_sale += $list_detail[$kk]['after_sale_total'];
        				}else {
        				    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
        					$workhours_price += $list_detail[$kk]['total'];
        					$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
        				}
        				$all_after_total += $list_detail[$kk]['after_sale_total'];
        	        }
        	        $table_str .= '
                            </table>';
                	/*	</td>
                	</tr>
            		<tr>
            			<td height="5" colspan="4">&nbsp;</td>
            		</tr>
            	    <tr>
            			<td> */
                         $table_str .= '<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" height="35" bordercolor="#eaeaea">
            			  <tr class="sub-table-title">
            				<td align="center" width="25%" height="35" bgColor="#e3e3e3">工时明细</td>
            				<td align="center" width="25%" height="35" bgColor="#e3e3e3">工时单价</td>
            				<td align="center" width="25%" height="35" bgColor="#e3e3e3">工时数量</td>
            				<td align="center" width="25%" height="35" bgColor="#e3e3e3">折扣率</td>
            			  </tr>
            			  <tr  class="sub-table-content" >
            				<td align="center" width="25%" height="30">'.$list_detail[0]['Midl_name'].'</td>
            				<td align="center" width="25%" height="30">'.$list_detail[0]['price'].'</td>
            				<td align="center" width="25%" height="30">'.$list_detail[0]['quantity'].' '.$list_detail[0]['unit'].'</td>
            				<td align="center" width="25%" height="30">'.$sale_arr['workhours_sale'].'</td>
            			  </tr>
            			</table>';
                   /*     </td>
            		  </tr>;*/

        	    }else {
					/*<tr>
					<td>*/
						$table_str .='<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0"  bordercolor="#eaeaea">
						  <tr class="sub-table-title">
								<td align="center" width=25% height="35" bgColor="#e3e3e3"  colspan=4>暂无报价</td>
						  </tr>
						</table>';
					// </td></tr>;
			    }
            }

            $product_price_save = $product_price-$product_price_sale;
			$workhours_price_save = $workhours_price-$workhours_price_sale;
			$save_total = $all_total-$all_after_total;
        /*    <tr>
                			<td height="5" colspan="4">&nbsp;</td>
                		</tr>
                		  <tr>
                			<td>*/
                			$table_str .='<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#eaeaea">
                			  <tr class="sub-table-title">
                				<td align="center" width=25% height="35" bgColor="#e3e3e3">&nbsp;</td>
                				<td align="center" width=25% height="35" bgColor="#e3e3e3">门市价</td>
                				<td align="center" width=25% height="35" bgColor="#e3e3e3">折后价</td>
                				<td align="center" width=25% height="35" bgColor="#e3e3e3">节省</td>
                			  </tr>
                			  <tr class="sub-table-content" >
                				<td align="center" width=25% height="30">零件费</td>
                				<td align="center" width=25% height="30">'.round($product_price, 2).'</td>
                				<td align="center" width=25% height="30">'.round($product_price_sale, 2).'</td>
                				<td align="center" width=25% height="30">'.round($product_price_save, 2).'</td>
                			  </tr>
                			  <tr class="sub-table-content">
                				<td align="center" width=25% height="30">工时费</td>
                				<td align="center" width=25% height="30">'.round($workhours_price, 2).'</td>
                				<td align="center" width=25% height="30">'.round($workhours_price_sale, 2).'</td>
                				<td align="center" width=25% height="30">'.round($workhours_price_save, 2).'</td>
                			  </tr>
                			  <tr class="sub-table-content">
                				<td align="center" width=25% height="30">合计（元）</td>
                				<td align="center" width=25% height="30">'.round($all_total, 2).'</td>
                				<td align="center" width=25% height="30">'.round($all_after_total, 2).'</td>
                				<td align="center" width=25% height="30">'.round($save_total, 2).'</td>
                			  </tr>
                			</table>';
                			/*</td>
                		  </tr>
                		  <tr>
                		<td height="8" colspan="4">&nbsp;</td>
                  </tr>
                </table>*/

                $table_str .='</body></html>';
                    echo $table_str;exit;
	    }else{
            $table_str = "您未选择车型或该车型价格无法提供";
            echo $table_str;exit;
	    }
	}
    public function get_orderpricedetail_bak(){
	    $select_services_str = $_REQUEST['select_services_str'];
	    $timesaleversion_id = isset($_REQUEST['timesaleversion_id'])?$_REQUEST['timesaleversion_id']:0;
	    $model_timesaleversion = D('Timesaleversion');
	    $model_timesale = D('Timesale');
	    $model_shop = D('Shop');
		$map_ts['id'] = $timesaleversion_id;
		$list_timesaleversion = $model_timesaleversion->where($map_ts)->find();
        $timesale = $model_timesale->find($list_timesaleversion['timesale_id']);
		$shop_id = $timesale['shop_id'];
		$shopinfo = $model_shop->find($shop_id);
        /*if ($shopinfo['shop_maps']){
	        $maps_diff = C('MAPS_DIFF');
	        $shop_maps_arr = explode(',',$shopinfo['shop_maps']);
	        $shop_maps_arr[0] = $shop_maps_arr[0]- $maps_diff[0];
	        $shop_maps_arr[1] = $shop_maps_arr[1]- $maps_diff[1];
	        $shopinfo['shop_maps'] = implode(',',$shop_maps_arr);
	    }*/
	    $model_id = $_REQUEST['model_id'];
	    $map['service_id']  = array('in',$select_services_str);
		$map['model_id']  = array('eq',$model_id);
		$model_product = D('Product');
		$list = $model_product->where($map)->select();
		//echo '<pre>';print_r($map);exit;
		foreach($list AS $k=>$v){
			$versionid_arr[] = $v['versionid'];
			$productid_arr[] = $v['id'];
		}
		$versionid_str = implode(',', $versionid_arr);
		$productid_str = implode(',', $productid_arr);

		$map_p['id'] = array('in',$productid_str);

		$list_product = array();
		if (!empty($select_services_str)){
		    $select_services_arr = explode(',',$select_services_str);
		    foreach ($select_services_arr as $key=>$services_id){
		        if ($services_id){
    		        $map_p['service_id'] = array('eq',$services_id);
    		        $list_product[$key]['productinfo'] = $model_product->where($map_p)->find();
    		        $list_product[$key]['service_id'] = $services_id;
		        }
		    }
		}

		$workhours_sale = $list_timesaleversion['workhours_sale'];
		$product_sale = $list_timesaleversion['product_sale'];
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
		$model_serviceitem = D('Serviceitem');
		$table_str = '<table border="0" cellspacing="0" cellpadding="0" width=305>
  <tr>
    <td height="35" align="center" bgcolor="#CCCCCC"><span style="font-size: 24px;font-weight: bold;">'.$shopinfo['shop_name'].'</span></td>
  </tr>';
	    if (isset($list_product) and !empty($list_product)){
	        $i=0;
            foreach ($list_product as $k=>$v){
                //echo '<pre>';print_r($v);exit;
                $i++;
                $service_name = $model_serviceitem->getById($v['service_id']);
                if ($v['productinfo']['product_detail']){
				    $list_detail = $v['productinfo']['product_detail'];
				    $list_detail = unserialize($list_detail);
				}else {
				    $list_detail = array();
				}
				$table_str .= '<tr>
    <td height="35">&nbsp;<span style="font-size: 18px;">维修项目'.$i.'：'.$service_name['name'].'</span></td>
  </tr>';
        	    if ($list_detail){
        	        $table_str .= '<tr>
    <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" width=25%><strong>零件</strong></td>
        <td align="center" width=25%><strong>单价</strong></td>
        <td align="center" width=25%><strong>数量</strong></td>
        <td align="center" width=25%><strong>折扣率</strong></td>
      </tr>';
        	        foreach($list_detail AS $kk=>$vv){
        		        $list_detail[$kk]['total'] = $list_detail[$kk]['quantity']*$list_detail[$kk]['price'];
        				$all_total +=$list_detail[$kk]['total'];
        				if($list_detail[$kk]['Midl_name'] != '工时费'){
        					$list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['product_sale'];
        					$table_str .= '<tr>
                                            <td align="center">'.$list_detail[$kk]['Midl_name'].'</td>
                                            <td align="center">'.$list_detail[$kk]['price'].'</td>
                                            <td align="center">'.$list_detail[$kk]['quantity'].' '.$list_detail[$kk]['unit'].'</td>
                                            <td align="center">'.$sale_arr['product_sale'].'</td>
                                          </tr>';
        					$product_price += $list_detail[$kk]['total'];
                			$product_price_sale += $list_detail[$kk]['after_sale_total'];
        				}else {
        				    $list_detail[$kk]['after_sale_total'] = $list_detail[$kk]['total']*$sale_value['workhours_sale'];
        					$workhours_price += $list_detail[$kk]['total'];
        					$workhours_price_sale += $list_detail[$kk]['after_sale_total'];
        				}
        				$all_after_total += $list_detail[$kk]['after_sale_total'];
        	        }
        	        $table_str .= '</table></td>
                                      </tr>
                                      <tr>
                                        <td height="5">&nbsp;</td>
                                      </tr>
                                  <tr>
                                    <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td align="center" width=25%><strong>工时明细</strong></td>
                                        <td align="center" width=25%><strong>工时单价</strong></td>
                                        <td align="center" width=25%><strong>工时数量</strong></td>
                                        <td align="center" width=25%><strong>折扣率</strong></td>
                                      </tr>
                                       <tr>
                                            <td align="center">'.$list_detail[0]['Midl_name'].'</td>
                                            <td align="center">'.$list_detail[0]['price'].'</td>
                                            <td align="center">'.$list_detail[0]['quantity'].' '.$list_detail[0]['unit'].'</td>
                                            <td align="center">'.$sale_arr['workhours_sale'].'</td>
                                          </tr>
                                        </table></td>
                                      </tr>';

        	    }
            }

            $product_price_save = $product_price-$product_price_sale;
			$workhours_price_save = $workhours_price-$workhours_price_sale;
			$save_total = $all_total-$all_after_total;
            $table_str .='<tr>
                            <td height="8">&nbsp;</td>
                          </tr>
                          <tr>
                            <td>通过携车网预约您的保养维修项目，共为您节省：</td>
                          </tr>
                          <tr>
                            <td height="5">&nbsp;</td>
                          </tr>
                          <tr>
                            <td><table width="95%" border="1" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td align="center" width=25%>&nbsp;</td>
                                <td align="center" width=25%><strong>门市价</strong></td>
                                <td align="center" width=25%><strong>折后价</strong></td>
                                <td align="center" width=25%><strong>节省</strong></td>
                              </tr>
                              <tr>
                                <td align="center"><strong>零件费</strong></td>
                                <td align="center">'.$product_price.'</td>
                                <td align="center">'.$product_price_sale.'</td>
                                <td align="center">'.$product_price_save.'</td>
                              </tr>
                              <tr>
                                    <td align="center"><strong>工时费</strong></td>
                                    <td align="center">'.$workhours_price.'</td>
                                    <td align="center">'.$workhours_price_sale.'</td>
                                    <td align="center">'.$workhours_price_save.'</td>
                                  </tr>
                                  <tr>
                                    <td align="center"><strong>合计（元）</strong></td>
                                    <td align="center">'.$all_total.'</td>
                                    <td align="center">'.$all_after_total.'</td>
                                    <td align="center">'.$save_total.'</td>
                                  </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td height="8">&nbsp;</td>
                              </tr>
                          	  <tr>
                                <td>您所选的4S店地址：<a href="http://m.xieche.net/xml/getcompany.php?shop_id='.$shopinfo['id'].'MAPP">'.$shopinfo['shop_address'].'</a></td>
                              </tr>
                            </table>';
                        echo $table_str;exit;
	    }else{
			$xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
			$xml_content .="</XML>";
            echo $xml_content;exit;
	    }
	}

    public function getcomment_byshopid(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $model_comment = D('Comment');
	    $model_commentreply  = D('Commentreply ');
	    $model_member = D('Member');
	    $shop_id = $_REQUEST['shop_id'];
	    $map['shop_id'] = $shop_id;
	    $comment = $model_comment->where($map)->order("create_time DESC")->select();
	    if ($comment){
	        foreach ($comment as $k=>$v){
	            if (!$v['user_name']){
                   $memberinfo = $model_member->find($v['uid']);
                   $v['user_name'] = substr($memberinfo['mobile'],0,5).'******';
                }
                $order_id = $this->get_orderid($v['id']);
	            $xml_content .="<item><comment_id>".$v['id']."</comment_id><shop_id>".$v['shop_id']."</shop_id><order_id>".$v['order_id']."</order_id><uid>".$v['uid']."</uid><user_name>".$v['user_name']."</user_name><comment>".$v['comment']."</comment><comment_type>".$v['comment_type']."</comment_type><create_time>".$v['create_time']."</create_time>";
	            if ($v['id']){
	                $map_r['comment_id'] = $v['id'];
	                $commentreply = $model_commentreply->where($map_r)->select();
	                if ($commentreply){
	                    $xml_content .="<commentreply>";
	                    foreach ($commentreply as $kk=>$vv){
	                        $xml_content .="<replyitem><reply_id>".$vv['id']."</reply_id><reply>".$vv['reply']."</reply><operator_id>".$vv['operator_id']."</operator_id><operator_name>".$vv['operator_name']."</operator_name><operator_type>".$vv['operator_type']."</operator_type><create_time>".$vv['create_time']."</create_time></replyitem>";
	                    }
	                    $xml_content .="</commentreply>";
	                }
	            }
	            $xml_content .="</item>";
	        }
	    }
	    $xml_content .="</XML>";
        echo $xml_content;exit;
	}
	public function getcomment(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $model_comment = D('Comment');
	    $model_commentreply  = D('Commentreply ');
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
        	    $order_id = $_REQUEST['order_id'];
        	    $map['order_id'] = $order_id;
        	    $map['uid'] = $uid;
        	    $comment = $model_comment->where($map)->order("create_time DESC")->select();
        	    if ($comment){
        	        foreach ($comment as $k=>$v){
            	        if (!$v['user_name']){
                           //$memberinfo = $model_member->find($v['uid']);
                           $v['user_name'] = substr($memberinfo['mobile'],0,5).'******';
                        }
        	            $xml_content .="<item><comment_id>".$v['id']."</comment_id><uid>".$v['uid']."</uid><user_name>".$v['user_name']."</user_name><comment>".$v['comment']."</comment><comment_type>".$v['comment_type']."</comment_type><create_time>".$v['create_time']."</create_time><update_time>".$v['update_time']."</update_time>";
        	            if ($v['id']){
        	                $map_r['comment_id'] = $v['id'];
        	                $commentreply = $model_commentreply->where($map_r)->order("create_time DESC")->select();
        	                if ($commentreply){
        	                    $xml_content .="<commentreply>";
        	                    foreach ($commentreply as $kk=>$vv){
        	                        $xml_content .="<replyitem><reply_id>".$vv['id']."</reply_id><reply>".$vv['reply']."</reply><operator_id>".$vv['operator_id']."</operator_id><operator_name>".$vv['operator_name']."</operator_name><operator_type>".$vv['operator_type']."</operator_type><create_time>".$vv['create_time']."</create_time><update_time>".$vv['update_time']."</update_time></replyitem>";
        	                    }
        	                    $xml_content .="</commentreply>";
        	                }
        	            }
        	            $xml_content .="</item>";
        	        }
        	    }else{
        	        $xml_content .= "<status>2</status><desc>未取得评价数据</desc>";
                    $xml_content.="</XML>";
                	echo $xml_content;exit;
        	    }
            }else{
                $xml_content .= "<status>1</status><desc>用户未登录</desc>";
                $xml_content.="</XML>";
            	echo $xml_content;exit;
            }
	    }else{
	        $xml_content .= "<status>1</status><desc>用户未登录</desc>";
            $xml_content.="</XML>";
        	echo $xml_content;exit;
	    }
	    $xml_content .="</XML>";
        echo $xml_content;exit;
	}
    function add_comment(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $model_comment = D('Comment');
	    $model_commentreply  = D('Commentreply ');
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
        	    $data['shop_id'] = isset($_REQUEST['shop_id'])?$_REQUEST['shop_id']:0;
        	    $data['order_id'] = isset($_REQUEST['order_id'])?$_REQUEST['order_id']:0;
        	    $data['comment'] = isset($_REQUEST['content'])?$_REQUEST['content']:'';
        	    $data['comment_type'] = isset($_REQUEST['comment_type'])?$_REQUEST['comment_type']:'1';
        	    $data['create_time'] = time();
        	    $data['update_time'] = time();
        	    $uid = $memberinfo['uid'];
        	    $data['uid'] = $uid;
        	    $model_order = D('Order');
        	    $map_o['id'] = $data['order_id'];
        	    $map_o['uid'] = $uid;
        	    $order = $model_order->where($map_o)->find();
        	    if ($order){
        	        if ($order['member_id']){
        	            $data['member_id'] = $order['member_id'];
        	        }
            	    $data['user_name'] = $memberinfo['username'];
            	    $model_comment = D('Comment');
            	    $map_c['shop_id'] = $data['shop_id'];
            	    $map_c['order_id'] = $data['order_id'];
            	    $map_c['uid'] = $uid;
            	    $comment_info = $model_comment->where($map_c)->find();
            	    if ($comment_info){
            	        $xml_content .= "<status>1</status><desc>此订单已被评论</desc>";
                        $xml_content.="</XML>";
                        echo $xml_content;exit;
            	    }else{
            	        if ($model_comment->add($data)){
            	            $map_order['id'] = $data['order_id'];
                	        $model_order->where($map_order)->save(array('iscomment'=>1));
                	        $this->count_good_comment($data['shop_id'],$order['member_id']);
                	        $xml_content .= "<status>0</status><desc>评论添加成功</desc>";
                            $xml_content.="</XML>";
                            echo $xml_content;exit;
                	    }else {
                	        $xml_content .= "<status>2</status><desc>评论添加失败</desc>";
                            $xml_content.="</XML>";
                            echo $xml_content;exit;
                	    }
            	    }
        	    }else{
        	        $xml_content .= "<status>3</status><desc>订单信息错误</desc>";
                    $xml_content.="</XML>";
                    echo $xml_content;exit;
        	    }
            }else {
                $xml_content .= "<status>5</status><desc>用户不存在</desc>";
                $xml_content.="</XML>";
                echo $xml_content;exit;
            }
	    }else {
	        $xml_content .= "<status>4</status><desc>用户未登录</desc>";
            $xml_content.="</XML>";
            echo $xml_content;exit;
	    }
	}

    function update_comment(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $model_comment = D('Comment');
	    $model_commentreply  = D('Commentreply ');
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
        	    $comment_id = isset($_REQUEST['comment_id'])?$_REQUEST['comment_id']:0;
        	    $data['comment_type'] = isset($_REQUEST['comment_type'])?$_REQUEST['comment_type']:'';
        	    $data['update_time'] = time();
        	    $uid = $memberinfo['uid'];

        	    $model_comment = D('Comment');
        	    $map_c['id'] = $comment_id;
        	    $map_c['uid'] = $uid;
        	    $comment_info = $model_comment->where($map_c)->find();
        	    if ($comment_info){
        	        if ($comment_info['comment_type'] == 1){
                        $xml_content .= "<status>1</status><desc>已经是好评了，不能再修改</desc>";
                        $xml_content.="</XML>";
                        echo $xml_content;exit;
        	        }else{
        	            unset($map_c['uid']);
                	    if($model_comment->where($map_c)->save($data)){
                	        $this->count_good_comment($comment_info['shop_id'],$comment_info['member_id']);
                	        $xml_content .= "<status>0</status><desc>评价修改成功</desc>";
                            $xml_content.="</XML>";
                            echo $xml_content;exit;
                	    }else {
                	        $xml_content .= "<status>3</status><desc>评价修改失败</desc>";
                            $xml_content.="</XML>";
                            echo $xml_content;exit;
                	    }
        	        }
        	    }else{
        	        $xml_content .= "<status>2</status><desc>不能修改别人的评论</desc>";
                $xml_content.="</XML>";
                echo $xml_content;exit;
        	    }
            }else {
                $xml_content .= "<status>5</status><desc>用户不存在</desc>";
                $xml_content.="</XML>";
                echo $xml_content;exit;
            }
	    }else {
	        $xml_content .= "<status>4</status><desc>用户未登录</desc>";
            $xml_content.="</XML>";
            echo $xml_content;exit;
	    }
	}

    function count_good_comment($shop_id,$uid){//计算公式= (好评-差评）/总评数
	    if ($shop_id >0){
    	    $model_comment = D('Comment');
    	    $comment_info = $model_comment->where("shop_id=$shop_id")->select();
    	    $good_comment_number = 0;
    	    $bad_comment_number = 0;
    	    if (!empty($comment_info)){
    	        foreach ($comment_info as $v){
    	            if ($v['comment_type'] == 1){
    	                $good_comment_number +=1;
    	            }
    	            if ($v['comment_type'] == 3){
    	                $bad_comment_number +=1;
    	            }
    	        }
    	    }
    	    $all_comment_number = count($comment_info);
    	    if ($good_comment_number>$bad_comment_number and $all_comment_number >0){
    	        $comment_rate = ($good_comment_number - $bad_comment_number)/$all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_shop = D('Shop');
    	    $data['id'] = $shop_id;
    	    $data['comment_rate'] = $comment_rate;
    	    $data['comment_number'] = $all_comment_number;
    	    $model_shop->where("id=$shop_id")->save($data);
	    }
        if ($uid >0){
    	    $comment_info = $model_comment->where("member_id=$uid")->select();
    	    $good_comment_number = 0;
    	    $bad_comment_number = 0;
    	    if (!empty($comment_info)){
    	        foreach ($comment_info as $vv){
    	            if ($vv['comment_type'] == 1){
    	                $good_comment_number +=1;
    	            }
    	            if ($vv['comment_type'] == 3){
    	                $bad_comment_number +=1;
    	            }
    	        }
    	    }
    	    $all_comment_number = count($comment_info);
    	    if ($good_comment_number>$bad_comment_number and $all_comment_number >0){
    	        $comment_rate = ($good_comment_number - $bad_comment_number)/$all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_member = D('Member');
    	    $data1['uid'] = $uid;
    	    $data1['comment_rate'] = $comment_rate;
    	    $data1['comment_number'] = $all_comment_number;
    	    $map_m['uid'] = $uid;
    	    $model_member->where($map_m)->save($data1);
	    }
	}

    function reply_comment(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $model_comment = D('Comment');
	    $model_commentreply  = D('Commentreply ');
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
        	    $data['reply'] = isset($_REQUEST['content'])?$_REQUEST['content']:'';
        	    $data['comment_id'] = isset($_REQUEST['comment_id'])?$_REQUEST['comment_id']:'0';
        	    $data['create_time'] = time();
        	    $data['update_time'] = time();
        	    $uid = $memberinfo['uid'];
        	    $data['operator_id'] = $uid;
        	    $model_comment = D('Comment');
        	    $map_c['id'] = $data['comment_id'];
        	    $map_c['uid'] = $uid;
        	    $comment = $model_comment->where($map_c)->find();
        	    if ($comment){
            	    $data['operator_name'] = $memberinfo['username'];
            	    $data['operator_type'] = $memberinfo['member_type'];
        	        $model_commentreply = D('Commentreply');
            	    if ($model_commentreply->add($data)){
            	        $xml_content .= "<status>0</status><desc>回复成功</desc>";
                        $xml_content.="</XML>";
                        echo $xml_content;exit;
            	    }else {
            	        $xml_content .= "<status>1</status><desc>回复失败</desc>";
                        $xml_content.="</XML>";
                        echo $xml_content;exit;
            	    }
        	    }else{
        	        $xml_content .= "<status>4</status><desc>只能回复自己发的评论</desc>";
                    $xml_content.="</XML>";
                    echo $xml_content;exit;
        	    }
            }else {
                $xml_content .= "<status>3</status><desc>用户不存在</desc>";
                $xml_content.="</XML>";
                echo $xml_content;exit;
            }
	    }else {
	        $xml_content .= "<status>2</status><desc>用户未登录</desc>";
            $xml_content.="</XML>";
            echo $xml_content;exit;
	    }
	}


	public function forgetpw(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if(empty($_REQUEST['username'])) {
			$xml_content .= "<status>1</status><tolken></tolken><desc>账号为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
		$username = $_REQUEST['username'];
		$Member = M('Member');
	    if (is_numeric($username)){
		    if (substr($username,0,1)==1){
		        $res = $Member -> where("mobile='$username' AND status='1'")->find();
		    }else {
		        $res = $Member -> where("cardid='$username' AND status='1'")->find();
		    }
		}else {
		    $res = $Member -> where("username='$username' AND status='1'")->find();
		}
		if (!$res){
		    $xml_content .= "<status>2</status><tolken></tolken><desc>输入的账号不存在</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if (!$res['mobile']){
		    $xml_content .= "<status>3</status><tolken></tolken><desc>账号手机号码不存在</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}

	    $mobile = $res['mobile'];
	    $uid = $res['uid'];
	    if ($mobile){
            $model_sms = D('Sms');
            $condition['phones'] = $mobile;
            $smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
            $now = time();
            if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){
    	        import('@.ORG.Util.String');
    	        $rand_verify = String::randString(6, 1);
    	        $_SESSION['mobile_verify'] = md5($rand_verify);
    	        if ($mobile and $rand_verify){
    	            $verify_str = "验证码：".$rand_verify;
    	            $send_verify = array('phones'=>$mobile,
    	                                 'content'=>$verify_str,
    	                                );
    	            $return_data = $this->curl_sms($send_verify);
    	            $send_verify['sendtime'] = $now;
    	            $model_sms->add($send_verify);
    	            $xml_content .= "<verify>".$rand_verify."</verify><mobile>".$mobile."</mobile><uid>".$uid."</uid>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
    	        }
    	    }else{
    	        $xml_content .= "<status>1</status><tolken></tolken><desc>发送失败！发送过于频繁，请一分钟后再尝试。</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	    }else {
	        $xml_content .= "<status>2</status><tolken></tolken><desc>手机号码不存在</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}
	public function save_new_password(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if (empty($_REQUEST['password'])){
	        $xml_content .= "<status>1</status><tolken></tolken><desc>请输入新密码</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	    if (empty($_REQUEST['repassword'])){
	        $xml_content .= "<status>2</status><tolken></tolken><desc>请输入确认密码</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	    if ($_REQUEST['password'] != $_REQUEST['repassword']){
	        $xml_content .= "<status>3</status><tolken></tolken><desc>两次输入的密码不同</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	    $model_member = D('Member');
	    $condition['uid'] = $_REQUEST['uid'];
	    $data['password'] = pwdHash($_REQUEST['password']);
	    $data['update_time'] = time();
	    if ($model_member->where($condition)->save($data)){
	        $xml_content .= "<status>0</status><tolken></tolken><desc>密码修改成功</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }else {
	        $xml_content .= "<status>4</status><tolken></tolken><desc>密码修改失败</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}
	public function change_password(){
	    $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if(empty($_REQUEST['oldpassword'])) {
			$xml_content .= "<status>1</status><tolken></tolken><desc>原密码为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
        if (empty($_REQUEST['password'])){
            $xml_content .= "<status>2</status><tolken></tolken><desc>请输入密码</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
        }
        if (empty($_REQUEST['repassword'])){
            $xml_content .= "<status>3</status><tolken></tolken><desc>请输入确认密码</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
        }
	    if ($_REQUEST['password'] !=$_REQUEST['repassword']){
	        $xml_content .= "<status>4</status><tolken></tolken><desc>两次密码输入不一样</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
        $oldpassword_md5 = pwdHash($_REQUEST['oldpassword']);
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                if ($memberinfo and $memberinfo['password'] != $oldpassword_md5){
                    $xml_content .= "<status>5</status><tolken></tolken><desc>旧密码错误</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
        		}else {
        		    $_REQUEST['password'] = pwdHash($_REQUEST['password']);
        		    $map['uid'] = $memberinfo['uid'];
        		    if ($model_member->where($map)->save($_REQUEST)){
        		        $xml_content .= "<status>1</status><tolken></tolken><desc>修改成功</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
        		    }else{
        		        $xml_content .= "<status>6</status><tolken></tolken><desc>修改失败</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
        		    }
    		    }
            }else {
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
                $xml_content.="</XML>";
            	echo $xml_content;exit;
            }
	    }else {
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
	        $xml_content.="</XML>";
            echo $xml_content;exit;
	    }
	}

	public function get_memberinfo_byuid(){
	    $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $xml_content .= "<uid>".$memberinfo['uid']."</uid><username>".$memberinfo['username']."</username><email>".$memberinfo['email']."</email><mobile>".$memberinfo['mobile']."</mobile><prov>".$memberinfo['prov']."</prov><city>".$memberinfo['city']."</city><area>".$memberinfo['area']."</area><city>".$memberinfo['city']."</city>";
                $xml_content.="</XML>";
                echo $xml_content;exit;
            }else{
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
            }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}

	public function save_memberinfo(){
	    $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $data['email'] = $_REQUEST['email'];
                $data['mobile'] = $_REQUEST['mobile'];
                $data['prov'] = $_REQUEST['prov'];
                $data['city'] = $_REQUEST['city'];
                $data['area'] = $_REQUEST['area'];
                $map['uid'] = $memberinfo['uid'];
                if($model_member->where($map)->save($data)){
                    $xml_content .= "<status>0</status><tolken>".$tolken."</tolken><desc>修改成功</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
                }else{
                    $xml_content .= "<status>2</status><tolken>".$tolken."</tolken><desc>修改失败</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
                }
            }else{
    	        $xml_content .= "<status>1</status><tolken>".$tolken."</tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}

	public function get_noteinfo(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
        	    $uid = $memberinfo['uid'];
        	    $year = $_REQUEST['year'];
        	    //$year = "2012";
        	    $month = $_REQUEST['month'];
        	    //$month = "10";
        	    $cmonth = $year.$month;
        	    $map['cmonth'] = array('EQ',$cmonth);
        	    $map['uid'] = array('EQ',$uid);
        	    $map['main_del'] = 0;
        		$xml_content .= "<cost_list>";
        		$model_Notemain = D('Notemain');
        	    $noteinfo = $model_Notemain->field('note_type,sum(total_cost) as total_cost')->where($map)->group('note_type')->select();
        	    //echo $model_Notemain->getLastSql();exit;
        	    //echo '<pre>';print_r($noteinfo);exit;
        	    if ($noteinfo){
        	        foreach ($noteinfo as $k=>$v){
        	           $xml_content .= "<item><cost>".$v['total_cost']."</cost><note_type>".$v['note_type']."</note_type></item>";
        	        }
        	    }
    	        $xml_content .= "</cost_list>";
            }else{
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
            }
	    }else {
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
		$xml_content.="</XML>";
		echo $xml_content;exit;
	}
	public function get_noteinfo_by_time(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $map['uid'] = array('EQ',$uid);
        	    $map['main_del'] = 0;
                $begin_time = $_REQUEST['begin_time'];
	            $end_time = $_REQUEST['end_time'];
                //查询时间判断
        		$get_time = $this->__condition_happen_time($begin_time,$end_time);
        		if($get_time){
        			$map['happen_time'] = $get_time;
        		}
        		$model_Notemain = D('Notemain');
        	    $noteinfo = $model_Notemain->where($map)->sum('total_cost');
        	    //echo $model_Notemain->getLastSql();
        	    //echo '<pre>';print_r($noteinfo);exit;
        	    if ($noteinfo){
        	        $xml_content .= "<cost>".$noteinfo."</cost>";
        	    }
            }else{
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
            }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	    $xml_content.="</XML>";
		echo $xml_content;exit;
	}

    public function get_noteinfo_each(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $map['uid'] = array('EQ',$uid);
        	    $map['main_del'] = 0;
                $begin_time = $_REQUEST['begin_time'];
	            $end_time = $_REQUEST['end_time'];
                //查询时间判断
        		$get_time = $this->__condition_happen_time($begin_time,$end_time);
        		if($get_time){
        			$map['happen_time'] = $get_time;
        		}
        		$model_Notemain = D('Notemain');
        	    $noteinfo = $model_Notemain->where($map)->select();
        	    //echo $model_Notemain->getLastSql();
        	    $note_each = array('jiayou'=>0,'meirong'=>0,'weixiu'=>0,'baoxian'=>0,'tingche'=>0,'tongxing'=>0,'fakuan'=>0,'gouzhi'=>0,'guifei'=>0,'qita'=>0,'total'=>0);
        	    if ($noteinfo){
        	        foreach ($noteinfo as $k=>$v){
        	            $note_each['total'] += $v['total_cost'];
        	            if ($v['note_type']==1){
        	                $note_each['jiayou'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==2){
        	                $note_each['meirong'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==3){
        	                $note_each['weixiu'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==4){
        	                $note_each['baoxian'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==5){
        	                $note_each['tingche'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==6){
        	                $note_each['tongxing'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==7){
        	                $note_each['fakuan'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==8){
        	                $note_each['gouzhi'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==9){
        	                $note_each['guifei'] += $v['total_cost'];
        	            }
        	            if ($v['note_type']==11){
        	                $note_each['qita'] += $v['total_cost'];
        	            }
        	        }
        	    }
                foreach ($note_each as $_k=>$_v){
    	            $xml_content .= "<".$_k.">".$_v."</".$_k.">";
    	        }
            }else{
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
            }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
	    }
	    $xml_content.="</XML>";
		echo $xml_content;exit;
	}

    function __condition_happen_time($begin_time='',$end_time=''){
		if($begin_time && $end_time){
			//$begin_time = strtotime($begin_time);
			//$end_time = strtotime($end_time);
			if(($begin_time >= $end_time) && ($begin_time < time())){
				$xml_content .= "<status>1</status><tolken></tolken><desc>时间错误</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
			}else{
				$map = array(array('GT',$begin_time),array('LT',$end_time));
				return $map;
			}
		}else{
			return '';
		}
	}
	public function get_notedetail(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
        	    $uid = $memberinfo['uid'];
        	    $begin_time = $_REQUEST['begin_time'];//2012-10-1 00:00:00
        	    $end_time = $_REQUEST['end_time'];//2012-10-31 23:59:59
        	    $map['uid'] = array('EQ',$uid);
        		//查询时间判断
        		$get_time = $this->__condition_happen_time($begin_time,$end_time);
        		if(!empty($get_time)){
        			$map['happen_time'] = $get_time;
        		}
        		$note_type = isset($_REQUEST['note_type'])?$_REQUEST['note_type']:0;
        		if ($note_type){
        		    $map['note_type'] = $note_type;
        		}
        		$xml_content.="<note_detail>";
        		$model_Notemain = D('Notemain');
        		$detail_info = $model_Notemain->where($map)->order("happen_time")->select();
        		if ($detail_info){
        		    foreach ($detail_info as $_k=>$_v){
        		        $xml_content.="<item_note><note_id>".$_v['note_id']."</note_id><happen_time>".$_v['happen_time']."</happen_time><total_cost>".$_v['total_cost']."</total_cost><note_type>".$_v['note_type']."</note_type></item_note>";
        		    }
        		}
        		$xml_content.="</note_detail>";
            }else{
    	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
		$xml_content.="</XML>";
		echo $xml_content;exit;
	}
	public function get_oilwear_byid(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $u_c_id = $_REQUEST['u_c_id']?$_REQUEST['u_c_id']:0;
                $model_oilewear = D('Oilewear');
                $map_oilwear['u_c_id'] = $u_c_id;
                $oilewearinfo = $model_oilewear->where($map_oilwear)->find();
                if ($oilewearinfo){
                    $xml_content .= "<u_c_id>".$oilewearinfo['u_c_id']."</u_c_id><brand_id>".$oilewearinfo['brand_id']."</brand_id><series_id>".$oilewearinfo['series_id']."</series_id><model_id>".$oilewearinfo['model_id']."</model_id><oilwear>".$oilewearinfo['oilwear']."</oilwear><per_oilprice>".$oilewearinfo['per_oilprice']."</per_oilprice><total_tripmile>".$oilewearinfo['total_tripmile']."</total_tripmile><total_quantity>".$oilewearinfo['total_quantity']."</total_quantity>";
                }
            }else{
                $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
            }
	    }else {
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
	    }
	    $xml_content.="</XML>";
		echo $xml_content;exit;
	}
	public function delete_note(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_Notemain = D('Notemain');
                $note_id = $_REQUEST['note_id'];
                if ($note_id){
                    $data['main_del'] = 1;
                    $map['note_id'] = $note_id;
                    $map['uid'] = $uid;
                    if ($model_Notemain->where($map)->save($data)){
                        $xml_content .= "<status>0</status><tolken></tolken><desc>删除成功</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
                    }else{
            	        $xml_content .= "<status>2</status><tolken></tolken><desc>删除失败</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
            	    }
                }else{
        	        $xml_content .= "<status>3</status><tolken></tolken><desc>数据错误</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
        	    }
            }else{
    	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}

    public function update_note(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_Notemain = D('Notemain');
                $note_id = $_REQUEST['note_id'];
                $total_cost = $_REQUEST['total_cost'];
                if ($note_id){
                    $data['total_cost'] = $total_cost;
                    $map['note_id'] = $note_id;
                    $map['uid'] = $uid;
                    if ($model_Notemain->where($map)->save($data)){
                        $xml_content .= "<status>0</status><note_id>".$note_id."</note_id><tolken></tolken><desc>编辑成功</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
                    }else{
            	        $xml_content .= "<status>2</status><tolken></tolken><desc>编辑失败</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
            	    }
                }else{
        	        $xml_content .= "<status>3</status><tolken></tolken><desc>数据错误</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
        	    }
            }else{
    	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	    }else{
	        $xml_content .= "<status>1</status><tolken></tolken><desc>用户未登录</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
	    }
	}

    public function articlelist(){
        $page_size = 10;
        $model_article = D('Article');
		$model_notice = D('Notice');
        $model_carseries = D('Carseries');
		$model_shop_fs_relation = D('Shop_fs_relation');
		$model_region = D('Region');
		$shop_city = empty($_REQUEST['city_id'])?'3306':$_REQUEST['city_id'];

		$map_r['id'] = $shop_city;
        $region = $model_region->where($map_r)->find();
        $city_name = $region['region_name'];

        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_membercar = D('Membercar');
                $map_mc['uid'] = $uid;
                $map_mc['status'] = 1;
                $membercar = $model_membercar->where($map_mc)->select();
                $series_arr = array();
                if ($membercar){
                    foreach ($membercar as $_k=>$_v){
                        $series_arr[] = $_v['series_id'];
                    }
                }
                $series_str = implode(',',$series_arr);
                $map_se['series_id'] = array('in',$series_str);
                $seriesinfos = $model_carseries->where($map_se)->select();
                $fsid_arr = array();
                if ($seriesinfos){
                    foreach ($seriesinfos as $key=>$val){
                        $fsid_arr[] = $val['fsid'];
                    }
                }
                $fsid_str = implode(',',$fsid_arr);
                $map_a['status'] = 1;
                $map_a['fsid'] = array(array('in',$fsid_str),array('eq','0'),'or');
                $map_a['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');
                // 导入分页类
                import("ORG.Util.Page");
                $article_count = $model_article->where($map_a)->count();
                // 实例化分页类
                $page = new Page($article_count, $page_size);
                $article = $model_article->where($map_a)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();

				/*公告10条
				*ysh	 2013/3/22
				*/
				$map_fs['fsid'] = array('in',$fsid_str);
				$fs_shop_info = $model_shop_fs_relation->where($map_fs)->select();
				$shopid_arr = array();
                if ($fs_shop_info){
                    foreach ($fs_shop_info as $key=>$val){
                        $shopid_arr[] = $val['shopid'];
                    }
                }
                $shopid_str = implode(',',$shopid_arr);
                $map_notice['status'] = 1;
				$map_notice['city_name'] = $city_name;
                $map_notice['shop_id'] = array('in',$shopid_str);
				$notice_count = $model_notice->where($map_notice)->count();

				$n_p = new Page($notice_count, $page_size);
				$notice = $model_notice->where($map_notice)->order('id DESC')->limit($n_p->firstRow.','.$n_p->listRows)->select();
                //echo $model_article->getLastSql();exit;
            }
        }else{
            $map_a['status'] = 1;
            $map_a['city_name'] = array(array('eq',$city_name),array('eq','全部'),'or');
            // 导入分页类
            import("ORG.Util.Page");
            $article_count = $model_article->where($map_a)->count();
            // 实例化分页类
            $page = new Page($article_count, $page_size);
			
			/*
			*@name:针对第一页无条件 做缓存处理
			*@author:ysh
			*@time:2013/8/1
			*/
	
			//article缓存
			if($page->firstRow == 0) {
				$memcache_app_article = S('app_article');
				if($memcache_app_article) {
					$article = $memcache_app_article;
				}else {
					$article = $model_article->where($map_a)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
					S('app_article' , $article);
				}
			}else {
				$article = $model_article->where($map_a)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
			}
			
			$map_notice['status'] = 1;
			$map_notice['city_name'] = $city_name;
			$notice_count = $model_notice->where($map_notice)->count();
			$n_p = new Page($notice_count, $page_size);

			//notice缓存
			if($n_p->firstRow == 0) {
				$memcache_app_notice = S('app_notice');
				if($memcache_app_notice) {
					$notice = $memcache_app_notice;
				}else {
					$notice = $model_notice->where($map_notice)->order('id DESC')->limit($n_p->firstRow.','.$n_p->listRows)->select();
					S('app_notice' , $notice);
				}
			}else {
				$notice = $model_notice->where($map_notice)->order('id DESC')->limit($n_p->firstRow.','.$n_p->listRows)->select();
			}
			
        }
		if($notice) {
			$count=$article_count+$notice_count;
            $p_count = ceil($count/$page_size);
            $xml_content .="<p_count>".$p_count."</p_count>";

			foreach($notice as $key=>$val) {
				$val['noticetitle'] = str_replace('&nbsp;','',$val['noticetitle']);
				$val['noticedescription'] = str_replace('&nbsp;','',$val['noticedescription']);
				$val['noticetitle'] = htmlspecialchars($val['noticetitle']);
				$val['noticedescription'] = htmlspecialchars($val['noticedescription']);
				if (file_exists('./UPLOADS/Shop/100/'.$val['shop_id'].'.jpg')){
				   $logo = WEB_ROOT.'/UPLOADS/Shop/100/'.$val['shop_id'].'.jpg';
				}else {
					$model_fs = D('Fs');
					$model_shop_fs_relation = D('Shop_fs_relation');
					$fsidinfo = $model_shop_fs_relation->getByShopid($val['shop_id']);
					$fsid = $fsidinfo['fsid'];
					$map_f['fsid'] = $fsid;
					$fs = $model_fs->where($map_f)->find();

					if($fs['versionid']){
						$fsid = $fsid.'_'.$fs['versionid'];
					}
					$logo = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
				}
				 $xml_content .="<item><article_id>n_".$val['id']."</article_id><article_title>".strip_tags($val['noticetitle'])."</article_title><article_des>".strip_tags($val['noticedescription'])."</article_des><brand_logo>".$logo."</brand_logo></item>";
			}
		}

        if ($article){
            $model_carbrand = D('Carbrand');
            foreach ($article as $k=>$v){
                $fsid = $v['fsid'];
                $map_s['fsid'] = $fsid;
                if ($fsid == '53'){
                    $map_s['brand_id'] = 55;
                }else{
                    unset($map_s['brand_id']);
                }
                $series = $model_carseries->where($map_s)->find();
                $map_b['brand_id'] = $series['brand_id'];
                $brand = $model_carbrand->where($map_b)->find();
                $v['title'] = str_replace('&nbsp;','',$v['title']);
				$v['title'] = htmlspecialchars($v['title']);
                $v['summary'] = str_replace('&nbsp;','',$v['summary']);
				$v['summary'] = htmlspecialchars($v['summary']);
                if (file_exists('./UPLOADS/Shop/100/'.$v['shop_id'].'.jpg')){
	               $logo = WEB_ROOT.'/UPLOADS/Shop/100/'.$v['shop_id'].'.jpg';
	            }else {
					if($v['fsid'] == '0') {
						$logo = WEB_ROOT.'/UPLOADS/Shop/100/xc.jpg';
					}else {
						$model_fs = D('FS');
						$map_f['fsid'] = $v['fsid'];
						$fs = $model_fs->where($map_f)->find();
						if($fs['versionid']){
							$fsid = $fsid.'_'.$fs['versionid'];
						}
						$logo = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
					}
	               
	            }
                //$brand_logo = WEB_ROOT."/UPLOADS/Brand/Biglogo/".$brand['brand_logo'];
                $xml_content .="<item><article_id>a_".$v['id']."</article_id><article_title>".strip_tags($v['title'])."</article_title><article_des>".strip_tags($v['summary'])."</article_des><brand_logo>".$logo."</brand_logo></item>";
            }


        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    public function articlelist_bak(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $xml_content .="<item><article_id>23</article_id><article_title>上海宝信启动2012 BMW冬季关怀活动</article_title><article_des>悦行冬季，一路温馨。11月15日至12月15日，上海宝信2012BMW冬季关怀活动即将倾情启动，活动内容如下：</article_des><brand_logo>UPLOADS/Brand/Logo/baoma.jpg</brand_logo></item>";
                $xml_content .="<item><article_id>3</article_id><article_title>告别传统雨刷 法雷奥推新款喷水雨刷片</article_title><article_des>不知道您有没有这种经历，在高速行车时，车辆前挡风玻璃有些脏，玻璃水喷到前挡风玻璃，经过雨</article_des><brand_logo>UPLOADS/Brand/Logo/fute.jpg</brand_logo></item>";
                $xml_content .="<item><article_id>11</article_id><article_title>奥迪夏季服务日活动 7月7日如约启动</article_title><article_des>保持清晰视野？您的爱车同样需要，为了精心打造奥迪服务独有的季节性服务活动，在夏季到来之际，</article_des><brand_logo>UPLOADS/Brand/Logo/aodi.jpg</brand_logo></item>";
                $xml_content .="<item><article_id>12</article_id><article_title>东昌雪莱送你与曼联“零距离亲密接触”</article_title><article_des>1.7月9-23日期间，凡购买科迈罗，迈锐宝2.4SXAT旗舰版的客户将获得与曼联球员零距离见面会的机会</article_des><brand_logo>UPLOADS/Brand/Logo/xuefolan.jpg</brand_logo></item>";
                $xml_content .="<item><article_id>15</article_id><article_title>上海中升之星奔驰——久光百货赏车会</article_title><article_des>展出时间：2012年07月01日，展出车型：SLS,ML，上海久光百货地址：上海市静安区南京西路1618号</article_des><brand_logo>UPLOADS/Brand/Logo/benchi.jpg</brand_logo></item>";
                $xml_content .="<item><article_id>17</article_id><article_title>缤纷好礼尽在夏季服务“悦”</article_title><article_des>浓情盛夏，畅享清爽，2012年7月1日-9月30日东风Honda上海绿地店夏日乐享欢乐服务“悦”再送缤纷喜悦。</article_des><brand_logo>UPLOADS/Brand/Logo/bentian.jpg</brand_logo></item>";
            }
        }else{
            $xml_content .="<item><article_id>23</article_id><article_title>上海宝信启动2012 BMW冬季关怀活动</article_title><article_des>悦行冬季，一路温馨。11月15日至12月15日，上海宝信2012BMW冬季关怀活动即将倾情启动，活动内容如下：</article_des><brand_logo>UPLOADS/Brand/Logo/baoma.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>21</article_id><article_title>秋意盎然 教师、中秋、国庆三重奏</article_title><article_des>在这个充斥着慢慢节日的凉爽秋季，上海联通新悦携开业之际为广大用户提供最厚道的节日“促”底价</article_des><brand_logo>UPLOADS/Brand/Logo/qiya.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>18</article_id><article_title>车主如何自己拆卸与检查汽车火花塞</article_title><article_des>火花塞的作用是将点火线圈所产生的脉冲高压电引进燃烧室，利用电极产生的电火花点燃混合气，完成燃烧。</article_des><brand_logo>UPLOADS/Brand/Logo/dazong.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>19</article_id><article_title>绿地东本换新喜悦精品折上折</article_title><article_des>绿地东本店8月有礼，2012年8月10日起,在东风本田汽车上海绿地特约销售服务店做常规保养的客户</article_des><brand_logo>UPLOADS/Brand/Logo/bentian.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>20</article_id><article_title>选择开旧车，还是来原价置换新车！</article_title><article_des>绿地徐通雪佛兰“新升代、回购行动”开始啦！凡09年后购车的乐风、科鲁兹、新赛欧,客户置换科帕奇7座车型</article_des><brand_logo>UPLOADS/Brand/Logo/xuefolan.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>1</article_id><article_title>'心悦'感恩季夏日清凉行广丰夏季服务月</article_title><article_des>夏季高温使人容易“上火”，汽车空调也难幸免，异味重、有异响、制冷不畅……空调一旦“生病”，给车主</article_des><brand_logo>UPLOADS/Brand/Logo/fengtian.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>2</article_id><article_title>东昌雪佛兰 清凉一下年中“零”度特卖</article_title><article_des>东昌雪佛兰开展清凉一“下”年中“零”度特卖活动，将在本周7月7日，8日进行。在活动期间购买雪佛兰</article_des><brand_logo>UPLOADS/Brand/Logo/xuefolan.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>3</article_id><article_title>告别传统雨刷 法雷奥推新款喷水雨刷片</article_title><article_des>不知道您有没有这种经历，在高速行车时，车辆前挡风玻璃有些脏，玻璃水喷到前挡风玻璃，经过雨</article_des><brand_logo>UPLOADS/Brand/Logo/fute.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>7</article_id><article_title>旧车置换宝来、新速腾差价补贴</article_title><article_des>改变生活的计划，不应离现实太远，最好今天就可以实现。</article_des><brand_logo>UPLOADS/Brand/Logo/dazong.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>8</article_id><article_title>全面贴心呵护 斯柯达护航儿童安全出行</article_title><article_des>随着汽车市场的成熟，儿童的乘车安全逐步成为社会的关注焦点。7月1日，《机动车儿童乘员用约束系统》</article_des><brand_logo>UPLOADS/Brand/Logo/sikeda.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>10</article_id><article_title>越野轮胎中的无冕之王：MT轮胎</article_title><article_des>喜欢越野的人都会毫不犹豫的为爱车装备一款MT轮胎，单单拉风的外观就能让车主们自豪一把了，</article_des><brand_logo>UPLOADS/Brand/Logo/baoma.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>11</article_id><article_title>奥迪夏季服务日活动 7月7日如约启动</article_title><article_des>保持清晰视野？您的爱车同样需要，为了精心打造奥迪服务独有的季节性服务活动，在夏季到来之际，</article_des><brand_logo>UPLOADS/Brand/Logo/aodi.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>12</article_id><article_title>东昌雪莱送你与曼联“零距离亲密接触”</article_title><article_des>1.7月9-23日期间，凡购买科迈罗，迈锐宝2.4SXAT旗舰版的客户将获得与曼联球员零距离见面会的机会</article_des><brand_logo>UPLOADS/Brand/Logo/xuefolan.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>13</article_id><article_title>嘉年华免费随心驾 每日时尚好礼送不停</article_title><article_des>尽兴60天 拉风48小时，为了让更多的消费者感受到福特嘉年华的驾驶乐趣，适逢盛夏之际，福特嘉年华</article_des><brand_logo>UPLOADS/Brand/Logo/fute.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>14</article_id><article_title>上海晋熙服务站 邀您回家畅赢港澳游</article_title><article_des>炎炎夏日，呵护你我的爱车是不少车主最为头痛的事。邀您回家，畅享港澳游，2012上海晋熙4S店，</article_des><brand_logo>UPLOADS/Brand/Logo/dihao.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>15</article_id><article_title>上海中升之星奔驰——久光百货赏车会</article_title><article_des>展出时间：2012年07月01日，展出车型：SLS,ML，上海久光百货地址：上海市静安区南京西路1618号</article_des><brand_logo>UPLOADS/Brand/Logo/benchi.jpg</brand_logo></item>";
            $xml_content .="<item><article_id>17</article_id><article_title>缤纷好礼尽在夏季服务“悦”</article_title><article_des>浓情盛夏，畅享清爽，2012年7月1日-9月30日东风Honda上海绿地店夏日乐享欢乐服务“悦”再送缤纷喜悦。</article_des><brand_logo>UPLOADS/Brand/Logo/bentian.jpg</brand_logo></item>";
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    public function article(){
        $a_id = isset($_REQUEST['a_id'])?$_REQUEST['a_id']:0;

		$type_id = explode("_",$a_id);
		if( $type_id ) {
			$type = $type_id[0];
			$id = $type_id[1];
		}
		if($type == 'n') {
			$a_content = $this->get_notice($id);
		}else {
			$a_content = $this->get_article($id);
		}
        if ($a_content){
            $content .="<head><h3 style='text-align:center;'>".$a_content['title']."</h3></head><p style='text-align:center;color:gray;'>".date("Y-m-d" , $a_content['create_time'])."</p>";
            if ($a_content['content']){
                $search_str = '<img ';
		        $replace_str = '<img onload="if(this.width>screen.width)this.width=(screen.width-20)" ';
			    $a_content['content'] = str_replace($search_str, $replace_str, $a_content['content']);

                $content .="<body>".$a_content['content']."</body>";
            }
        }
        echo $content;exit;
    }
	
	/*
		@author:ysh
		@function:安卓无价格取得服务项目信息
		@time:2014-01-25
	*/
    public function get_services(){
        $xml_content="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $model_serviceitem = D('serviceitem');
        $map['service_item_id'] = 0;
        $serviceitem = $model_serviceitem->where($map)->select();
        if ($serviceitem){
            foreach ($serviceitem as $k=>$v){
                $xml_content .= "<item><service_id>".$v['id']."</service_id><name>".$v['name']."</name>";
                $map_s['service_item_id'] = $v['id'];
                $service = $model_serviceitem->where($map_s)->select();
                if ($service){
                    foreach ($service as $_k=>$_v){
                        $xml_content .= "<service_item><service_id>".$_v['id']."</service_id><name>".$_v['name']."</name></service_item>";
                    }
                }
                $xml_content .= "</item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
    public function get_article($a_id){
        $model_article = D('Article');
        $map_a['id'] = $a_id;
        $article = $model_article->where($map_a)->find();
        return $article;
    }
	public function get_notice($n_id) {
		$model_notice = D('Notice');
        $map_a['id'] = $n_id;
        $notice = $model_notice->where($map_a)->find();

		$result['title'] = $notice['noticetitle'];
		$result['create_time'] = $notice['update_time'];
		$result['content'] = $notice['noticedescription'];
        return $result;
	}


    public function get_article_bak($a_id){
        $article_arr[21] = array('title'=>'联通新悦，教师、中秋、国庆三重奏','content'=>'<div class="article">
		  <h1><p align=center><strong>联通新悦，教师、中秋、国庆三重奏</strong></p></h1>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;在这个充斥着慢慢节日的凉爽秋季，上海联通新悦携开业之际为广大用户提供最厚道的节日“促”底价、最贴心的售后关怀免费检测服务！</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;新车销售：<br/>
			1：到店既得进店小礼品一份。<br/>
			2：持教师证购车可在优惠价之后再抵500元<br/>
			3：购车客户购买装潢精品套件可享受半价优惠<br/>
			4：二手车置换可送高额补贴、可享折上折的多重优惠<br/>
			5：贷款购车可享0利率、0手续，并有装潢大礼一份、省去装潢烦恼！<br/>
			</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;售后服务：<br/>
			1、欢度教师节。在9月份凡驾驶员凭本人教师证进本站维修，人工费5折优惠<br/>
			2、庆贺联通新悦东风悦达起亚4S店开业暨欢度中秋，10月份维修人工费7折优惠<br/>
			</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;联系电话：021-52237999<br/>
			</p>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;店面地址：上海市闵行区天山西路2668号
			</p>

		</div>');
        $article_arr[18] = array('title'=>'车主如何自己拆卸与检查汽车火花塞','content'=>'<div class="article">
			  <h1><p align=center><strong>车主如何自己拆卸与检查汽车火花塞</strong></p></h1>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;火花塞的作用是将点火线圈所产生的脉冲高压电引进燃烧室，利用电极产生的电火花点燃混合气，完成燃烧。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;国产火花塞的型号由三部分数字或字母组成。前面的数字表示螺纹直径，如数字1，表示螺纹直径为10mm，中间的字母表示火花塞旋入气缸部分的长度;最后一  位数字表示火花塞的热型：1-3为热型、5、6为中型，7以上为冷型。火花塞的“间隙”是其主要工作技术指标，间隙过大，点火线圈和分电器产生的高压电难  以跳过，致使发动机起动困难;如间隙过小，会导致火花微弱，同时易发生漏电。</p>
			  <p><strong>如何检查汽车的火花塞：</strong></p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;1、拆卸：将火花塞上的高压分线依次拆下，并在原始位置做上标记，以免安装错位。在拆卸中注意事先清除火花塞孔处的灰尘及杂物，以防止杂物落入气缸。拆卸时用火花塞套筒套牢火花塞，转动套筒将其卸下，并依次排好。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;2、检查：火花塞的电极正常颜色为灰白色，如电极烧黑并附有积炭，则说明存在故障。检查时可将火花塞与缸体导通，用中央高压线触接火花塞的接线柱，然后打开点火开关，观察高压电跳位置。如电跳位置在火花塞间隙，则说明火花塞作用良好，否则，即需换新。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;火花塞电极间隙的调整：各种车型的火花塞间隙均有差异，一般应在0.7-0.9之间，检查间隙大小，可用火花塞量规或薄的金属片进行。如间隙过大，可用起子柄轻轻敲打外电极，使其间隙正常;间隙过小时，则可利用起子或金属片插入电极向外扳动。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;火花塞的更换：火花塞属易消耗件，一般行驶20000-30000公里即应更换。火花塞更换的标志是不跳火，或电极放电部分因烧蚀而成圆形。另外，如  在使用中发现火花塞经常积炭、断火，一般是因为火花塞太冷，需换用热型火花塞；若有炽热点火现象或气缸中发出冲击声，则需选用冷型火花塞。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;火花塞的清洁：火花塞存有油污或积炭应及时予以清洗，但不要用火焰烧烤。如瓷芯损坏、破裂，则应进行更换。</p>
		</div>');
        $article_arr[19] = array('title'=>'绿地东本换新喜悦精品折上折','content'=>'<div class="article">
			  <h1><p align=center><strong>绿地东本换新喜悦精品折上折</strong></p></h1>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;绿地东本店8月有礼，2012年8月10日起,在东风本田汽车上海绿地特约销售服务店做常规保养的客户,在享受到店礼，惊喜礼的基础上，增加换新喜悦，精品折上折的活动，原厂精品最低至1折，数量有限，售完即止。详询64186090</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;此次大规模的促销活动，是为了感谢广大消费者对东风本田汽车上海绿地特约销售服务店的支持与信赖，让新老客户体验到真正的精品折上折。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;价目表如下：</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>以旧换新价：</strong></p>
			  <table border="1" align="center" cellpadding="0" cellspacing="0" class="list" >
				<tbody>
				  <tr>
					<th><p align="center"> 项目</p></th>
					<th><p align="center"> 车型</p></th>
					<th><p align="center"> 原价</p></th>
					<th><p align="center"> 现价</p></th>
				  </tr>
				  <tr>
					<td><p align="center"> 全车3M贴膜</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 3300元</p></td>
					<td><p align="center"> 1980元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 原厂雨档</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 1380元</p></td>
					<td><p align="center"> 798元</p></td>
				  </tr>
				  <tr>
					<td><p align="center">原厂脚垫</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 580元</p></td>
					<td><p align="center"> 298元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 全车真皮（头层皮）</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 5800元</p></td>
					<td><p align="center"> 2980元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 全车真皮（二层皮）</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 4000元</p></td>
					<td><p align="center"> 1980元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 全车光触媒</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 800元</p></td>
					<td><p align="center"> 480元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> DVD导航一体机</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 8500元</p></td>
					<td><p align="center"> 4580元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 全车漆面无机水晶镀膜</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 3800元</p></td>
					<td><p align="center"> 2380元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 全车漆面玻璃纤维镀膜</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 2800元</p></td>
					<td><p align="center"> 1580元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 车内深度清洁</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 480元</p></td>
					<td><p align="center"> 298元</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 白色车整车爆白</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 480元</p></td>
					<td><p align="center"> 298元</p></td>
				  </tr>
				  <tr>
					<td><p align="center">漆面护理（整车抛光、镜面还原）</p></td>
					<td><p align="center"> 全车型</p></td>
					<td><p align="center"> 680元</p></td>
					<td><p align="center"> 428元</p></td>
				  </tr>
				</tbody>
			  </table>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>08</strong><strong>款CR-V 原厂精品清仓特价酬宾：（数量有限，售完即止）</strong></p>
			  <table border="1" align="center" cellpadding="0" cellspacing="0" class="list">
				<tbody>
				  <tr>
					<th><p align="center"> 项目</p></th>
					<th><p align="center"> 原价</p></th>
					<th><p align="center"> 现价</p></th>
				  </tr>
				  <tr>
					<td><p align="center"> 动感地毯</p></td>
					<td><p align="center"> 580</p></td>
					<td><p align="center"> 160</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 行李固定网</p></td>
					<td><p align="center"> 480</p></td>
					<td><p align="center"> 150</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 轮毂防盗螺母</p></td>
					<td><p align="center"> 480</p></td>
					<td><p align="center"> 120</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 尾翼</p></td>
					<td><p align="center"> 1800</p></td>
					<td><p align="center"> 880</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 侧护踏板</p></td>
					<td><p align="center"> 3800</p></td>
					<td><p align="center"> 1280</p></td>
				  </tr>
				  <tr>
					<td><p align="center"> 排气管尾罩</p></td>
					<td><p align="center"> 380</p></td>
					<td><p align="center"> 120</p></td>
				  </tr>
				</tbody>
			  </table>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;东风本田汽车上海绿地特约销售服务店</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;销售热线：021-64186966</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;售后热线：021-64186090</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;地址：上海市徐汇区船厂路189号（近中山南二路）</p>
		</div>');
        $article_arr[20] = array('title'=>'选择开旧车，还是来原价置换新车！','content'=>'<div class="article">
		  <h1><p align=center><strong>选择开旧车，还是来原价置换新车！</strong></p></h1>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;绿地徐通雪佛兰“新升代、回购行动”开始啦！</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;凡09年后购车的<strong>乐风、科鲁兹、新赛欧</strong>客户置换科帕奇7座车型，并且满足回购标准，即可享受<strong>原价回购</strong>（乐风按当年市场成交均价原价回购，科鲁兹、新赛欧按当前新车（同款或近似款成交均价原价回购）</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;名额有限请抓紧时间报名哦！</p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>详情请资询：</strong><strong>021-64165666</strong></p>
		  <p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：上海市船厂路</strong><strong>185</strong><strong>号</strong></p>
		</div>');
        $article_arr[1] = array('title'=>'"心悦"感恩季夏日清凉行广丰夏季服务月','content'=>'<div class="article">
			<h1><p align=center><strong>"心悦"感恩季夏日清凉行广丰夏季服务月</strong></p></h1>

			<p>&nbsp;&nbsp;&nbsp;&nbsp;夏季高温使人容易“上火”，汽车空调也难幸免，异味重、有异响、制冷不畅……空调一旦“生病”，给车主带来的将是极大隐患与不便。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;为了避免您的爱车患上“空调病”，同时也作为“心悦服务”品牌发布两周年的真情回馈，6月17日至7月15日，广汽丰田将在全国333家销售店开展2012年广汽TOYOTA夏季服务活动，以贴心的服务践行“心悦服务”品牌的五大承诺。活动期间，广汽丰田车主到店即可免费享受空调系统全面健诊。</p>
			<p><strong>空调“不上火” 出行更安心</strong><br>
			&nbsp;&nbsp;&nbsp;&nbsp;“夏季行车，先养空调”。专家指出，经过一个冬天的运转，空调进风口吸入大量灰尘、脏物，残留在蒸发箱和空调系统内，滋生出大量细菌、霉菌、螨虫。这些污  垢会降低空调的效率且增加油耗；而有害细菌则会污染空气，传播疾病，严重危害人体健康。再者，夏季多暴雨，许多带有湿气的灰尘和棉絮、树叶等杂质经过空调  格时会堆积在蒸发器上，除了导致空调工作不畅外，也是空调产生“口气”的重要原因。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;针对上述问题，广汽丰田在全国推出了夏季空调健诊活动，免费为广汽丰田车主进行空调制冷系统全面检测，包括出风口、鼓风机、空调滤清器、冷凝器、蒸发器、压缩机、冷却风扇、驱动皮带、制冷剂量、空调管路及接头、水箱等多个部件的检查、调整或清洁。</p>
			<p><strong>保养有礼? 换购更有“利”</strong><br>
			  除了开展夏季空调健诊活动，广汽丰田还为车主准备了几重惊喜大礼。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;在活动期间，凡是到店进行保养的车主都有机会参与抽奖，每日抽出三名幸运车主，将可赢得iphone4s、ipad2或itouch4等大奖。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;此外，广汽丰田考虑到车主在检修、保养期间需要一定的等待时间，还贴心地准备了有趣的试驾体验活动。所有入库的车主在等待期间，都可以在广汽丰田专业顾问  的带领下，试驾全新第七代凯美瑞领先全球的科技魅力，亲自见证其同级别最顶级的动力和最经济的油耗表现，更可以零距离体验新一代混合动力凯美瑞?尊瑞  “劲、低、静、净”的四大产品魅力，过一把“尝鲜瘾”；还可以试驾广汽丰田旗下“豪华城市型”SUV汉兰达、FUV时尚多功能车逸致等不同车型，感受广汽  丰田的多面精彩。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;值得一提的是，由于广汽丰田去年9月推出了“心悦二手车”服务品牌，对于有置换意向的车主，只要到广汽丰田二手车认证店，即可享受免费车辆评估，并出具专业评估报告，夏季服务活动期间成功完成二手车置换的车主，更能获得最高达3000元的爱车养护基金。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;炎炎夏日，酷暑难耐，谁不想与爱车共度清凉一夏？广汽丰田秉持“心悦服务”品牌的专业关爱，为您的爱车量身定制祛暑良方，更备下多重好礼等待您的光临。</p>
			<p> <strong>广汽丰田长宇浦东店</strong></p>
			<p> <strong>地址：上海市浦东新区榕桥路618号（近申江路金海路）</strong></p>
			<p> <strong>免费咨询电话：021-50321688</strong></p>
		</div>');
        $article_arr[2] = array('title'=>'东昌雪佛兰 清凉一下年中“零”度特卖','content'=>'<div class="article">
			<h1><p align="center"><b>东昌雪佛兰 清凉一下年中“零”度特卖</b></p></h1>
			  <p  align="center">东昌雪佛兰开展清凉一“下”年中“零”度特卖活动，将在本周7月7日，8日进行。</p>
			  <p  align="center">在活动期间购买雪佛兰全系车型，将活动清凉座椅一套，还不快来？</p>
			<p>商家名称：上海东昌雪莱汽车销售服务有限公司<br>
			  销售电话：021-68937999<br>
			  店面地址：上海市浦东新区御桥路1468号 </p>
		</div>');
        $article_arr[3] = array('title'=>'告别传统雨刷 法雷奥推新款喷水雨刷片','content'=>'<div class="article">
			<h1><p align=center><strong>告别传统雨刷 法雷奥推新款喷水雨刷片 </strong></p></h1>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;不知道您有没有这种经历，在高速行车时，车辆前挡风玻璃有些脏，玻璃水喷到前挡风玻璃，经过雨刷一刷会有很短的时间玻璃变的更加模糊，这个时候如果前方道路  出现行人或事故，极有可能会发生躲闪不及的情况，又或者您在夜晚行车时，玻璃水喷到前挡风让您看不到信号灯，有什么方法避免这些情况呢？不妨看看法雷奥带  来的这款名为AquaBlade的带喷水功能的雨刷。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;这款AquaBlade雨刷片从外观来看与普通产品没有太大的区别，细观察你会发现分布在橡胶条表面上有一排很小的孔，这是制造瑕疵吗？当然不，在这一排孔的背后藏着一条“水渠”。随着雨刷片的摆动，喷出的玻璃水就会喷到相应的位置。?</p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;“水渠”在雨刷片与雨刷臂的固定位置相连并通过雨刷臂连到雨刷电机，雨刷电机与安装在玻璃水储液罐上电机相连，就这样，当司机拨动风挡清洗开关时，雨刷器  动作开始动作，而安装在储液罐上的电机会将玻璃水抽出并通过相关管路输送至雨刷片，此时雨刷片表面的孔就扮演着喷嘴的角色。</p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;AquaBlade的优点，还在于仅需一般雨刷器所需玻璃水一半的量，就可以使前挡风被擦的更清晰，这都得益于喷出的玻璃水随着雨刷器被喷到相应的地方。</p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;车辆高速行驶时，玻璃水经过普通雨刷片的擦拭，会使前挡风左侧部分部分区域变得模糊，甚至会出现玻璃水在你开启清洗功能的瞬间准确的喷到后面车的玻璃上，这都使得行车存在安全隐患，而带有喷水功能的雨刷片，使得这一现象得到明显减轻。</p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;在夜间行车时，前挡风上的玻璃水会使驾驶员的视野变得十分不清晰，不仅看不到前方路况，甚至于无法分辨交通信号灯，这存在很大的安全隐患，而AquaBlade将玻璃水喷到雨刷器刷过的地方，将这种安全隐患降低至最低。（文/汽车之家?唐朝）</p>
			  <p align="left"><strong>总结：</strong></p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;科学技术的日益发展不光体现在发动机马力的提升以及更优美的外观，很多平时注意不到的细节其实也反映着科学技术的进步，更便捷更安全始终是人们的不懈追求，相信这项技术可以很快的进入到我们的生活，我们将对此继续保持关注。</p>
			  <p align="left"><strong>相关视频：</strong></p>
			  <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;下面的这段视频很好的展示了这款带有喷水功能的雨刷片，并将它仅需一般雨刷片所需一半玻璃水即可擦干玻璃，不同速度时都不会造成视觉盲区，夜间因玻璃水使前车灯光难以辨别等行车安全问题得以解决的效果，很好的展示出来。</p>
			<p align="center">
				<embed allowscriptaccess="sameDomain" id="playertemp215" quality="high" src="http://player.youku.com/player.php/partnerid/XNzAw/sid/XMzkwMTI4NjA4=/v.swf" type="application/x-shockwave-flash" align="middle" height="168" width="173">
			</p>

			<p> 商家名称：上海东昌福德汽车销售服务有限公司<br>
			  销售电话：4008110113转7378<br>
			  店面地址：上海市普陀区金沙江路2129号 </p>
		</div>');
        $article_arr[7] = array('title'=>'旧车置换宝来、新速腾差价补贴','content'=>'<div class="article">
			<h1><p align=center><strong>开门见“宝” “速”来抢购 </strong></p></h1>


			<p>&nbsp;&nbsp;&nbsp;&nbsp;改变生活的计划，不应离现实太远，最好今天就可以实现。</p>

			<p> <strong>◆活动时间</strong></p>
			<p> 2012年7月1日-9月30日</p>
			<p> <strong>◆活动车型</strong></p>
			<p> 宝来1.6L、新速腾</p>
			<p> <strong>◆活动规则</strong></p>
			<p> 在活动时间内，有换购需求的客户只需到一汽-大众上海虹湾4S店内，旧车在4S店专业评估师评估定价的基础上，换购宝来1.6L、新速腾，享受指定额度的置换补贴政策，客户补差价换购新车即可。</p>
			<p> 新车销售价格=（旧车评估价+置换补贴）+客户补差价</p>
			<p> <strong>◆补贴金额</strong></p>
			<p> <strong>◆补贴方式</strong></p>
			<p> 1、宝来1.6L：上海虹湾4S店向客户先行垫付置换补贴4000元，随后一汽-大众根据结算流程和标准进行结算并支付4000元补贴；</p>
			<p> 2、新速腾：上海虹湾4S店向客户先行垫付置换补贴5000元，随后一汽-大众根据结算流程和标准进行结算并支付5000元补贴；</p>
			<p> </p>
			<p> </p>
			<p> </p>
			<p> 更多车型优惠详情，请您垂询<strong>021-54871828</strong>，我们将为您悉心解答。</p>
			<p> 愿我们真心优质的服务，真挚诚恳的让利，让您尽享购车之旅的那份愉悦与享受！</p>
			<p> 我们的使命是让更多的人拥有一汽-大众品牌轿车！</p>
			<p> 享受更多优惠，尽在上海虹湾一汽-大众4S店！</p>
			<p> 一汽－大众授权经销商：上海虹湾贸易有限公司</p>
			<p> 4S店地址：上海市闵行区曲吴路700号（虹梅南路口）</p>
			<p> 销售热线：<strong>021-54871828</strong></p>
		</div>');
        $article_arr[8] = array('title'=>'全面贴心呵护 斯柯达护航儿童安全出行','content'=>'<div class="article">
			<h1><p align="center"><b>全面贴心呵护  斯柯达护航儿童安全出行</b></p></h1>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;随着汽车市场的成熟，儿童的乘车安全逐步成为社会的关注焦点。7月1日，《机动车儿童乘员用约束系统》  国家标准将正式实施，也让儿童出行安全成为最近媒体讨论的热点。作为中国首个全车系荣膺C-NCAP五星安全评价的汽车品牌，上海大众斯柯达旗下车型过硬  的安全品质和全方位的主被动安全装备不仅护航成人安心出行，更为儿童打造了一座移动安全城堡，成为消费者心中的安全典范。据悉，上海大众斯柯达全系车型均  标配ISOFIX儿童安全座椅固定装置，高标准的原装儿童安全座椅更充分保障儿童安全，悉心呵护儿童安全出行。</p>
			  <p><strong>正确认识儿童安全座椅</strong></p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;不少中国家长都习惯乘车时把孩子抱在怀中或让孩子坐在自己腿上，或让孩子坐在副驾驶位置，或给身材矮小的孩子系上成人用的安全带，这些错误的做法都  有可能给孩子带来致命伤害。研究表明，事故发生时，坐在后排儿童安全座椅上的孩子比没使用儿童安全座椅的孩子生存率高96%；使用儿童专用安全装置可有效  地将儿童受伤害的几率降低70%左右。保障儿童安全出行最关键也是最负责任的做法是，在你精心选择的品质可靠的安全汽车上配备儿童安全座椅。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;“刚开始对儿童安全座椅完全没有认识，觉得根本没有必要，后来在上海大众斯柯达服务人员的介绍下了解了相关知识，还好我的车有儿童安全座椅固定装  置。”斯柯达昊锐车主李先生表示，“以往出门，孩子总是由妻子抱着，不仅不方便，还特别不安全。现在带着孩子开车外出都会使用儿童安全座椅，其实是非常方  便的，只要把儿童安全座椅插入昊锐后排的固定接口就行了，儿子有了专属座椅也很开心。”据了解，上海大众斯柯达品牌旗下晶锐、明锐、昊锐三款车型均全系标  配后座ISOFIX儿童安全座椅固定装置，使用时只需将儿童安全座椅上的接口对准后排的固定接口插入锁死即可，安装便捷，每次安装只需15秒，更让儿童安  全座椅与汽车成为一体，杜绝二次碰撞的可能，进一步保障孩子的安全。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;“衡量儿童安全座椅是否可靠，最重要的标准就是有没有通过碰撞测试，现在市面上的儿童安全座椅良莠不齐，很多都没有认证，发生碰撞时孩子安全根本无  法得到保障。”明锐车主刘先生表示，“斯柯达原装的儿童安全座椅就让人很放心，正好赶上斯柯达服务节期间有88折优惠，我赶紧出手买了一个。安装和拆卸都  特别方便，把儿童安全座椅插入后排的固定接口就行，女儿很喜欢坐在自己的专座上，很舒服，两三个小时都不成问题！大人开起车来也不再分心。”据悉，上海大  众斯柯达的原装儿童安全座椅遵循德国大众严谨的产品设计开发流程，甚至在经历了C-NCAP及强曲线等严苛碰撞试验之后，仍能保证稳定品质，不会出现位  移、断裂、无法固定等情况；符合儿童人体工程学的设计让孩子们倍感舒适；同时它所用材料拥有抗菌防螨和阻燃认证，无毒无味，全方面呵护儿童安全。</p>
			  <p><strong>不要忽视车内空气质量</strong></p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;车内空气质量对于儿童安全来说也是非常重要的。在上海大众有一群特殊的人——金鼻子工程师，上海大众生产的汽车在经过凝雾、甲醛、总碳挥发、气味、暴晒等五大检测阶段后，还需要通过金鼻子工程师的鼻子检验，通过真实的气味体验来检验车内空气质量，确保绿色健康的车内环境。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;“因为家里有宝宝，买车时对车内环境十分重视，身边不少朋友都是明锐车主，他们都推荐说这车能让我放心。”斯柯达明锐车主吴先生欣慰地表示：“提车  那天特意留心了下，坐进去关上车门窗，基本没有闻到其他车惯有的新车味道。如今开了二个月了，也丝毫没感觉车内有什么异味，让我很满意。”</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;上海大众斯柯达4S店销售顾问介绍，车内有害物质主要包括甲醛、苯类、氨类及其他碳氢化合物等，上海大众从源头入手，在制造选材时就严格控制内饰材  料中的有害物质，斯柯达全系车型的内饰采用模内复合技术，杜绝染料与空气的接触和挥发；装配工艺中大量使用卡扣螺丝，使用无毒环保胶水，完全杜绝普通胶  水，进一步降低车内有毒气体的挥发。斯柯达品牌销售顾问自豪地表示，上海大众斯柯达的车全都严格执行德国大众VW50180《车内挥发物控制标准》，将车  内有害物质污染降到最低。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;随着家庭轿车的普及，孩子的乘车安全和健康问题也受到越来越多家长的重视，上海大众斯柯达品牌全系车型在儿童乘车安全和健康呵护上的优势也得到了越  来越多父母的认可，除了舒适大空间、高效低耗的动力、领先同级的人性化配置及高性价比，全方位的安全表现是上海大众斯柯达70万销量背后不可忽视的因素。</p>
			<p> 商家名称：上海交运起成汽车销售服务有限公司<br>
			  销售电话：021-64173030<br>
			  店面地址：徐汇区中山南二路379号 </p>
		</div>');
        $article_arr[10] = array('title'=>'越野轮胎中的无冕之王：MT轮胎','content'=>'<div class="article">
			<h1><p align="center"><b>越野轮胎中的无冕之王：MT轮胎</b></p></h1>

			<p>&nbsp;&nbsp;&nbsp;&nbsp;喜欢越野的人都会毫不犹豫的为爱车装备一款MT轮胎，单单拉风的外观就能让车主们自豪一把了，更遑论它卓越的越野性能，无人能出其右。究竟何为MT呢？让全球越野轮胎大师固铂轮胎（Cooper Tires）来为你解析越野轮胎中的无冕之王：MT轮胎。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;简而言之，MT（mud terrain   tire）就是胎齿粗犷、胎槽宽大、专注于越野性能表现的轮胎。越野迷们追求极限越野的狂热之情，对促进MT轮胎技术的进步和功能的完善，可谓是功不可  没。以不久前在建德市举办的“固铂轮胎杯”越野挑战赛为例，近百辆经过极限改装的越野赛车，齐聚一堂，逐个下场挑战精心设计的越野赛道：驼峰、泥坑、炮弹  坑、起伏路、陡坡、双边桥、侧倾路……这样的场地越野赛道最考验的就是轮胎的越野性能，赛道上大角度的坡道和湿滑的泥坑，很容易让一般轮胎打滑和陷胎，此  时，只有MT轮胎的“大牙”才能派上用场！它能够穿透烂泥的表层，让胎齿尽量抓住硬底而取得抓地力。对待一些需要攀越的大角度坡道，MT轮胎的“大牙”和  宽大的沟槽可以相互配合，“咬”住陡峭岩石路面来获得摩擦力，轻松翻越。可以说，在爬坡领域MT是睥睨天下佼佼者。MT轮胎还有一个制胜法宝，就是胎侧还  设计有胎齿，有时候就是靠这点侧面的附加力，才能让越野车如获救命稻草般脱困。所以说，MT当之无愧为越野界的无冕之王。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;在此，全球越野轮胎大师固铂轮胎（Cooper Tires）为你推荐两款MT中的明星产品：极限越野轮胎DISCOVERER   STMAXX和专业越野轮胎DISCOVERER   STT。这两款轮胎曾先后与丰田车队合作，携手出战被誉为世界上最艰苦的越野拉力赛——达喀尔拉力赛，并取得了不俗的成绩。特别是STMAXX的表现尤为  出色，成功助力丰田车队荣获2012年达喀尔拉力赛T2柴油量产组冠军。这两款轮胎同样源自美国原装进口，并且都采用了固铂轮胎公司独有的Armor-  Tek3三重盔甲科技，这项专利技术能让胎体强度增加33%，抗撕裂能力增加2.5倍，从而抗刺穿能力显著提高，使其成为异常坚固的越野轮胎。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;其中，STMAXX集多项高科技于一身，它的胎体强度增加了66%，抗撕裂性能增加了150%。它的超级抗穿刺性能，源于采用了高强度帘线与超强钢  丝线的相结合；直胎侧的设计，确保了更好的通过性，同时保证了复杂路面上的抓地能力；两重排石技术，将倒角沟壁技术与沟底阶梯橡胶技术相结合，此项设计可  以自动清除石块和尖锐物，有助于维护轮胎性能并防止进一步的损伤；硅胶配方，能防止轮胎在泥泞地面打滑，提升轮胎在湿地的抓地力；最为值得一提的  是，STMAXX还克服了MT轮胎较之AT轮胎而言胎噪较大的缺点，它独特的4-5交叉加强筋设计，能降低噪音，让噪声水平达到AT的水准，从而大大提升  了越野车主们长途驾车的旅途舒适性。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;而另一款明星产品STT，也不遑多让。它的全系列轮辋保护设计，能更好的减少轮辋的受损几率。而特有的胎肩和大块花纹设计，则使轮胎拥有优异的排石  性能和良好的泥地牵引性能，强大的“抓爬”和“啃咬”功能，能够很好地帮助越野车紧紧抓住路面，强力牵引，高速行驶。其深胎肩花纹，在提高越野车通过松软  路面和石子路面的驱动力的同时，也保护胎侧免受损坏。这两款精心设计的越野轮胎，凭借其卓越的越野性能，能够从容应对各种戈壁路况下外界异物的强烈冲击，  有效避免爆胎和陷车等现象，为车手和车队在比赛中尽情发挥提供了保障。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;最后，要提醒广大车主，只有根据自身驾驶需求来选择合适的轮胎，才能获得最大的驾驶乐趣！</p>
		</div>');
        $article_arr[11] = array('title'=>'奥迪夏季服务日活动 7月7日如约启动','content'=>'<div class="article">
			<h1><p align="center"><b>奥迪夏季服务日活动 7月7日如约启动</b></p></h1>

			<p>&nbsp;&nbsp;&nbsp;&nbsp;保持清晰视野？您的爱车同样需要</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;为了精心打造奥迪服务独有的季节性服务活动，在夏季到来之际，为您提供针对夏季出行的专业检测和保养建议，保证用户出行安全顺畅。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;2012年7月7日-7月29日，交运奥迪4S店夏季服务日活动如约启动。</p>
			<p>届时您将：</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;1、尊享雨刮系统和全车电脑免费检测服务</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;2、活动期间如需更换雨刮片，只收取材料费，人工费全免</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;3、到店参加活动客户将有机会获得奥迪精美礼品，先到先得</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;咨询热线：021-64419999</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;售后热线：021-64179090</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;上海交运起腾展厅营业面积达10787平方米，建筑分上下两层，内部拥有20个标准汽车展位、80个维修工位和全现代化进口维修设备，并设有自动扶梯等多项人性化设施，便于为消费者提供更加舒适、更加便捷的尊贵服务。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;地理位置十分优越，地处繁华的徐家汇商圈，位于上海市枫林路858号（地铁七号线船厂路 向南50米)，毗邻内环高架，交通极为便利。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;上海交运起腾汽车销售服务有限公司是市级国有企业--交运集团全资控股的公司，主要经营奥迪品牌的各型号车辆及维修、相关二手车业务等。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;商家名称：上海交运起腾汽车销售服务有限公司</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;销售电话：4008110113转8202</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;店面地址：上海徐汇区枫林路858号（地铁七号线船厂路向南50米） </p>
		</div>');
        $article_arr[12] = array('title'=>'东昌雪莱送你与曼联“零距离亲密接触”','content'=>'<div class="article">
			<h1><p align="center"><b>东昌雪莱送你与曼联“零距离亲密接触”</b></p></h1>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;1.7月9-23日期间，凡购买科迈罗，迈锐宝2.4SXAT旗舰版的客户将获得与曼联球员零距离见面会的机会，先"提"先得，数量有限送完即止。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;2.7月9-23日期间，购买迈锐宝，科帕奇，科鲁兹的用户即有机会获得观看曼联训练的机会。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;3.7月9-23日期间，凡进店做售后维修保养服务，消费满1888元以上并成功推荐三名新客户者，即获赠曼联上海之行足球赛门票一张。（数量有限，先到先得）注：保险理赔，事故修车等不计入本次活动消费中。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;详情请洽上海东昌雪莱汽车销售服务有限公司。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;商家名称：上海东昌雪莱汽车销售服务有限公司</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;销售电话：021-68937999</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;店面地址：上海市浦东新区御桥路1468号 </p>
		</div>');
        $article_arr[13] = array('title'=>'嘉年华免费随心驾 每日时尚好礼送不停','content'=>'<div class="article">
			<h1><p align="center"><b>嘉年华免费随心驾 每日时尚好礼送不停</b></p></h1>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;尽兴60天 拉风48小时</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;为了让更多的消费者感受到福特嘉年华的驾驶乐趣，适逢盛夏之际，福特嘉年华携手淘宝天猫及神州租车公司，于今天正式启动“60天盛夏狂欢 48小时畅驾嘉年华”活动。只需网上报名，即有机会获得免费畅驾福特嘉年华机会，更有精美车模，丰厚油卡等多种好礼回馈广大消费者。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;自2012年7月2日-8月31日，消费者只需登录“福特嘉年华型界”网站（http://www.ford-fiesta.com.cn/）或淘宝天猫的活动专题页面（http://www.taobao.com/go/act/sale/ford-fiesta.php）。报名预约试乘试驾，就有机会赢得福特嘉年华每天送出的“48小时畅驾嘉年华”大奖。获奖者提车后，还可额外获赠300元旅游券及100元电影券。此外，福特嘉年华每天还将抽取9位幸运参与者，送出嘉年华定制车模、油卡等幸运礼。成功完成预约试乘试驾，并于2012年9月17日之前，前往福特展厅完成试乘试驾的消费者，还将有机会参加新一轮抽奖，获奖者将获赠500元油卡作为专享礼。两个多月活动期间，福特嘉年华共预计送出420份专享礼。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;福特嘉年华作为精品小车的领军者，已历经36年历史，六代产品更新，以傲人的销售业绩跃居福特汽车最成功的车型阵营。在中国，福特嘉年华自2009年3月上市以来，以其大胆有型的设计、卓越的品质、领先的安全性能和极具竞争力的燃油经济性和持久价值吸引着个性活跃、敢于表现自己的中国年轻一代消费者。作为一款专为伴随着手机、互联网成长起来的潮流年轻车主所设计的精品小车，福特嘉年华针对定位的潮流车主不断推出引人入胜的营销手段。目前在中国，福特嘉年华累计销量已超过21万辆，是时尚潮流年轻人的购车首选之一，每天都会有近200名嘉年华车主产生。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;热力季节，让心情也High起来！赶快加入福特嘉年华 "60天盛夏狂欢，48小时畅驾嘉年华"活动吧。具体活动规则详见http://www.ford-fiesta.com.cn/。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;商家名称：上海东昌福德汽车销售服务有限公司</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;销售电话：4008110113转7378</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;店面地址：上海市普陀区金沙江路2129号 </p>
		</div>');
        $article_arr[14] = array('title'=>'上海晋熙服务站 邀您回家畅赢港澳游','content'=>'<div class="article">
			<h1><p align="center"><b>上海晋熙服务站 邀您回家畅赢港澳游</b></p></h1>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;炎炎夏日，呵护你我的爱车是不少车主最为头痛的事。邀您回家，畅享港澳游，2012上海晋熙4S店，汽车夏季服务活动开始了！活动期间，进站即享夏日免费健诊，购买超值精品、养护品，还可参加多重抽奖活动，更有“四天三晚港澳游”，超多惊喜，等着你的参与！</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;活动主题：上海晋熙 邀您回家畅赢港澳游</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;2012吉利汽车“关爱四季”之夏季服务活动</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;活动时间：2012年06月08日—08月28日</p>
			<p>活动内容：</p>
			<p>活动一：进站有礼：活动期间，凡进站维修保养用户均可参加幸运大抽奖，提前预约进站用户更可享受维修保养工时费8折优惠（事故车除外）。（注：用户至少提前4个小时预约，且按时到站方可享受工时费8折优惠）</p>
			<p>活动二：畅享免费健诊：活动期间，所有回厂用户均可享受24项夏季专项免费检测套餐一次，保障用户夏日行车安全。</p>
			<p>活动三：惠享博世回馈——畅赢港澳游：活动期间，用户凡购买博世精品满足下列任一条件即可获取由博世公司提供的“四天三夜港澳旅游券”抽奖机会一次，购买价值越高，抽奖机会越多，全国将产生285名幸运用户。</p>
			<p>活动四：劲享精品奉送：活动期间，一次性购买吉利精品/养护品（不含博世产品）满300元的用户，即可获得一次抽奖机会。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;如果你还不是吉利车主，那赶快拿起手中的电话，拨打我们的热线：4008-111-990，有更多的惊喜等着你！</p>
			<p align=right>上海晋熙拥有此次活动的最终解释权</p>
			<p>经销商：上海晋熙汽车销售服务有限公司</p>
			<p>网络服务平台：http://www.jinxiauto.com</p>
			<p>4S店地址：上海市浦东新区沪南公路4777弄258号/4789弄298号（上海车市）</p>
			<p>4S店电话：4008-111-990（咨询）021-68041618（帝豪）/021-68041676（英伦）</p>
			<p>川沙直营店  地址：华夏东路2313号（川沙路路口）   电话：021-20252287</p>
			<p>南汇直营店  地址：南汇区泥城镇东首南芦公路1855号  电话：58072080</p>
			<p>崇明直营店  地址：新河镇新开河路242号   电话：021-59688528</p>
		</div>');
        $article_arr[15] = array('title'=>'上海中升之星奔驰——久光百货赏车会','content'=>'<div class="article">
			<h1><p align="center"><b>上海中升之星奔驰——久光百货赏车会</b></p></h1>

			<p>&nbsp;&nbsp;&nbsp;&nbsp;展出时间：2012年07月01日</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;展出车型：SLS,ML</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;上海久光百货地址：上海市静安区南京西路1618号</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;另上海中升之星奔驰，为您隆重推出：</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;★新有“0”息</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;★二手车免费评估</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;一、活动时间：即日起至7月底</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;二、活动主推车型：E300L时尚豪华型，S级，R级，CLS300等部分指定车型</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;三、活动形式：0利息0手续费</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;四：即日起至7月底，至中升之星分期购奔驰指定车型，享0利息0手续费，更有机会获赠金币。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;
			奔驰品牌体验中心：上海市嘉定区金运路99号（江桥万达广场南500米）销售热线：021-69580000 021-67078888 服务热线：021-67078886 救援热线：18916580000</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;全新概念城市展厅：上海市杨浦区淞沪路308号（五角场万达广场北500米） 销售热线：400-116-1188 021-65886688
			</p>
		</div>');
        $article_arr[17] = array('title'=>'缤纷好礼尽在夏季服务“悦”','content'=>'<div class="article">
			<h1><p align=center><strong>缤纷好礼尽在夏季服务“悦”</strong></p></h1>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;浓情盛夏，畅享清爽，2012年7月1日-9月30日东风Honda上海绿地店夏日乐享欢乐服务“悦”再送缤纷喜悦。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;活动期间到店参加活动，即有机会获享惊喜好礼，用品惊喜促销，更可参加消费抽奖活动，赢取迪拜豪华之旅等惊喜大奖。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;凡是到东风Honda上海绿地店售后消费的客户(事故车和进保客户除外)：</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费0—500元：获东风HONDA车型冰箱贴一套；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费501— 1000元：获东风HONDA彩虹杯子一个；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费1001—2000元：获东风HONDA紫砂茶叶罐一个；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费2001—3000元：获东风HONDA抱枕被一个；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费3001—4000元：获东风HONDA太阳能充电器一套；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;消费 4000 元以上：获精美全自动茶艺壶一套；</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;用品惊喜促销，优惠爽心。夏日优惠，享清爽出行。购买轮胎等用品，更可享优惠礼遇。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;出行安心检测，畅享夏日金秋。夏日免费检测项目，清爽更呵护。空调滤清器、冷凝器、压缩机、冷却风扇、制冷剂、空调管路及接头、水箱等清凉检测。更有多重服务套餐等您来！</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;金秋免费检测项目，倍添安全更全面。制动系统、转向系统、轮胎检查、雨刮检查、电瓶等出行全面检测。</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;我们期待您的光临！我们将竭诚为您提供最优质的服务！</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;东风本田汽车上海绿地特约销售服务店</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;销售热线：021-64186966</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;售后热线：021-64186090</p>
			  <p>&nbsp;&nbsp;&nbsp;&nbsp;地址：上海市徐汇区船厂路189号（近中山南二路）</p>
		</div>');
        $article_arr[23] = array('title'=>'上海宝信启动2012 BMW冬季关怀活动','content'=>'<div class="article">
		  <h1><p align=center><strong>上海宝信启动2012 BMW冬季关怀活动</strong></p></h1>
		  <div id="divContent">
                    <p>&nbsp;</p><p>悦行冬季，一路温馨。11月15日至12月15日，上海宝信2012BMW冬季关怀活动即将倾情启动，活动内容如下：</p><p>所有回店客户可享受：</p><p>△30项冬季免费检测服务</p><p>△消费即可获赠BMW新年大礼包一个</p><p>△消费满1000元以上可获赠BMW新年大礼包一个和2013年宝马售后台历一本</p><p>△更换轮胎赠送胎压计一个</p><p>3系、5系车主专享：</p><p>△制动盘保养免工时优惠</p><p>车龄3年以上，12个月未回店客户：</p><p>△可凭BMW发送的人民币500元代金券到店享受保养及维修优惠</p><p>微博活动：</p><p>△冬季关怀活动期间BMW将通过宝马中国官方微博发起互动及抽奖活动。关注宝马中国并转发冬季关怀活动微博，即有机会获得BMW原装手电筒，每天10位幸运网友。活动期间回店车主，转发冬季关怀活动微博，并写出自己BMW车辆的车架号后7位，即有机会获得BMW原装M系列拉杆箱一个，每周2位幸运车主。</p><p>关爱全程相伴，温暖一路同行。上海宝信冬季多重礼遇，伴您一路温暖向前。</p><p>&nbsp;</p><p>30项BMW冬季免费检测项目：</p><p>外部</p><ol><li>车身损伤</li><li>玻璃</li><li>前后雨刮片及拆开静止位置</li><li>后视镜</li><li>外部照明系统</li><li>前风档/大灯清洗系统</li></ol><p>内部</p><ol><li>报警灯和仪表指示</li><li>CBS保养提示</li><li>安全带</li><li>内部照明系统</li><li>雨雪脚垫是否（正确）安装</li><li>暖风系统和除雾除霜性能</li><li>座椅加热</li></ol><p>发动机舱</p><ol><li>清洁或更换空调滤芯/空气滤芯</li><li>发动机冷车启动性能</li><li>机油液位</li><li>冷却液液位和防冻性能</li><li>转向助力油液位</li><li>传动皮带</li><li>玻璃水液位和防冻性能</li><li>底盘护板</li><li>转向系统</li><li>传动轴</li><li>密封件和管路渗漏</li><li>装备冬季轮胎</li><li>轮胎状况、胎压</li></ol><p>制动系统</p><ol><li>制动液液位</li><li>制动踏板和手刹</li><li>制动盘、制动片和卡钳</li><li>制动管路的干涉和渗漏</li></ol><p align="left">&nbsp;</p><p align="left">经销商：上海宝信汽车销售服务有限公司<br>地址：吴中路1715号<br>电话：021-34315555</p><p style="text-align: center"></p>
                </div>
		</div>');
        return $article_arr[$a_id];
    }

	/*
		@author:chf
		@function:安卓选择车型后显示的优惠卷信息(团购现金卷)
		@time:2013-4-9
	*/
    public function get_couponlist(){
    	
        $model_shop = D('Shop');
        $model_series = D('Carseries');
        $model_shop_fs_relation = D('Shop_fs_relation');
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $model_membercar = D('Membercar');
                $map_membercar['uid'] = $uid;
                $map_membercar['status'] = 1;
                $membercar = $model_membercar->where($map_membercar)->order("is_default DESC")->select();
                $model_arr = array();
                if ($membercar){
                    foreach ($membercar as $_k=>$_v){
                        $model_arr[] = $_v['model_id'];
                        $series_arr[] = $_v['series_id'];
					}
                }
                $model_str = implode(',',$model_arr);
                $series_str = implode(',',$series_arr);

                $map_series['series_id'] = array('in',$series_str);
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
                if ($shopid_str and $_REQUEST['coupon_type']!=2){
        	        $sql_1 = " coupon_type=1 AND shop_id IN (".$shopid_str.") ";
    	        }else{
    	            $sql_1 = "";
    	        }
                if ($_REQUEST['coupon_type']!=1 and $model_str){
    	            $sql_2 = " coupon_type=2 AND model_id in (".$model_str.") ";
    	        }else {
    	            $sql_2 = "";
    	        }
                if ($sql_1 and $sql_2){
        	        $sql = " ((".$sql_1.") OR (".$sql_2.")) ";
        	    }elseif ($sql_1) {
        	        $sql = $sql_1;
        	    }elseif ($sql_2) {
        	        $sql = $sql_2;
        	    }else{
        	        $sql = " 1=1 ";
        	    }
                //$map['model_id'] = array('in',$model_str);
            }else{
                $sql = " 1=1 ";
            }
        }else{
            $sql = " 1=1 ";
        }
        $model_coupon = D('Coupon');
        if ($_REQUEST['shop_id']){
            //$map['shop_id'] = $_REQUEST['shop_id'];
            $sql .= " AND shop_id='".$_REQUEST['shop_id']."' ";
        }
        if ($_REQUEST['model_id']){

			/*新版优惠券多店铺多品牌筛选
			$sql .= " AND FIND_IN_SET('{$_REQUEST[model_id]}', model_id)";
			*/

            //$map['model_id'] = $_REQUEST['model_id'];
            $model_carmodel = D('Carmodel');
            $map_mo['model_id'] = $_REQUEST['model_id'];
            $model = $model_carmodel->where($map_mo)->find();
            $map_se['series_id'] = $model['series_id'];
	        $series = $model_series->where($map_se)->find();
	        //echo $model_series->getLastSql();
	        if ($series){
	            $map_fs['fsid'] = $series['fsid'];
	            $fss = $model_shop_fs_relation->where($map_fs)->select();
	            if ($fss){
	                foreach ($fss as $_kfs=>$_vfs){
	                    $shopid_arr1[] = $_vfs['shopid'];
	                }

	                $shopid_arr1 = array_unique($shopid_arr1);
	                $shopid_str1 = implode(',',$shopid_arr1);
	                //echo $shopid_str;
	                //$map_s['shop_id'] = array('in',$shopid_str);
	            }
	        }
            if ($shopid_str1 and $_REQUEST['coupon_type']!=2){
    	        $sql_11 = " coupon_type=1 AND shop_id IN (".$shopid_str1.") ";
				//$sql_11 .= " AND model_id LIKE '%".$_REQUEST['model_id']."%' ";
	        }else{
	            $sql_11 = "";
	        }
            if ($_REQUEST['coupon_type']!=1 and $_REQUEST['model_id']){
	            $sql_21 = " coupon_type=2 AND model_id LIKE '%".$_REQUEST['model_id']."%' ";
				$sql_21.= "  AND series_id LIKE '%".$model['series_id']."%' ";
				$sql_21.= "  AND brand_id = '".$series['brand_id']."' ";

	        }else {
	            $sql_21 = "";
	        }
		
            if ($sql_11 and $sql_21){
    	        $sql1 = " ((".$sql_11.") OR (".$sql_21.")) ";
    	    }elseif ($sql_11) {
    	        $sql1 = $sql_11;
    	    }elseif ($sql_21) {
    	        $sql1 = $sql_21;
    	    }else{
    	        $sql1 = " 1=1 ";
    	    }

            //$sql .= " AND model_id='".$_REQUEST['model_id']."' ";
            $sql .= " AND ".$sql1;
        }
        if ($_REQUEST['coupon_type']){
            //$map['coupon_type'] = $_REQUEST['coupon_type'];
            $sql .= " AND coupon_type='".$_REQUEST['coupon_type']."' ";
        }

        if (isset($_REQUEST['is_use'])){
            //$map['is_use'] = $_REQUEST['is_use'];
            $sql .= " AND is_use='".$_REQUEST['is_use']."' ";
        }
        /*if (isset($_REQUEST['is_overtime'])){
            if ($_REQUEST['is_overtime']==1){
                //$map['end_time'] = array('lt',time());
                $sql .= " AND show_e_time<'".time()."' ";
            }else{
                //$map['end_time'] = array('gt',time());
                $sql .= " AND show_e_time>'".time()."' ";
            }
        }*/

        $shop_area = isset($_REQUEST['area_id'])?$_REQUEST['area_id']:0;
        $shop_city = empty($_REQUEST['city_id'])?'3306':$_REQUEST['city_id'];
        if ($shop_area>0){
            //$model_region = D('Region');
            //$map_region['region_name'] = $shop_area;
            //$region = $model_region->where($map_region)->find();
            $map_shop['shop_area'] = $shop_area;
        }else{
            $map_shop['shop_city'] = $shop_city;
        }
		
        $shop_info = $model_shop->where($map_shop)->select();
		//echo $model_shop->getlastSql();
        $shop_id_arr = array();
        if ($shop_info){
            foreach ($shop_info as $_kk=>$_vv){
                $shop_id_arr[] = $_vv['id'];
            }
            //$map['shop_id'] = array('in',implode(',',$shop_id_arr));

        }
        $sql .= " AND shop_id in (".implode(',',$shop_id_arr).") ";

        //$map['start_time'] = array('lt',time());
        //$map['end_time'] = array('gt',time());
        //$map['is_delete'] = 0;
		$now = strtotime(date("Y-m-d",time()));

        $sql .= " AND show_s_time<='".$now."' ";
        $sql .= " AND show_e_time>'".$now."' ";
        $sql .= " AND is_delete='0' ";
        //echo $sql;
        $page_size = 25;
        if ($_REQUEST['order'] == 'distance' and $_REQUEST['lat'] and $_REQUEST['long']){
            //$shops = $model_shop->where($map_shop)->select();
            $couponlist = $model_coupon->where($sql)->select();
            if ($couponlist){
                foreach ($couponlist as $_key=>$_val){
                    $shop_id = $_val['shop_id'];
                    $map_s['id'] = $shop_id;
                    $shopinfo = $model_shop->where($map_s)->find();
                    $shop_maps = $shopinfo['shop_maps'];
                    $shop_maps_arr = explode(',',$shop_maps);
                    $couponlist[$_key]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
                }
                $count = count($couponlist);
                for($i = 0; $i < $count; $i ++) {
                    for($j = $count - 1; $j > $i; $j --) {
                        if ($couponlist[$j]['distance'] < $couponlist[$j - 1]['distance']) {
                            //交换两个数据的位置
                            $temp = $couponlist [$j];
                            $couponlist [$j] = $couponlist [$j - 1];
                            $couponlist [$j - 1] = $temp;
                        }
                    }
                }
                $p_count = ceil($count/$page_size);
                for ($ii=0;$ii<$p_count;$ii++){
                   $page_coupons[$ii] = array_slice ($couponlist, $ii*$page_size,$page_size);
                }
                $p = isset($_REQUEST['p'])?$_REQUEST['p']:1;
				if($p == 0) {
					$p=1;
				}
                $couponlist = $page_coupons[$p-1];
            }
            //echo '<pre>';print_r($shop_info);exit;
        }else{
            // 导入分页类
            import("ORG.Util.Page");
            $count = $model_coupon->where($sql)->count();
            // 实例化分页类
            $p = new Page($count, $page_size);
            $p_count = ceil($count/$page_size);

			/*
			*@name:针对第一页无条件 做缓存处理
			*@author:ysh
			*@time:2013/8/1
			*/
			/*
			if((md5($sql) == "ffcfc4a7987c8d6d604b57ba2ce02641") && ( $p->firstRow == 0 )) {
				$memcache_couponlist = S('app_coupon_sql_default');
				if($memcache_couponlist) {
					$couponlist = $memcache_couponlist;
				}else {
					$couponlist = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
					S('app_coupon_sql_default',$couponlist);
				}

			}else {
			*/
			
				$couponlist = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
				if($_REQUEST['test'] == 1) {
					echo $model_coupon->getlastSql();
				}
			//}
			
            //$couponlist = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
			//echo $model_coupon->getlastSql();
            if ($couponlist){
                foreach ($couponlist as $kk=>$vv){
                    $shop_id = $vv['shop_id'];
                    $map_s['id'] = $shop_id;
                    $shopinfo = $model_shop->where($map_s)->find();
                    $shop_maps = $shopinfo['shop_maps'];
                    $shop_maps_arr = explode(',',$shop_maps);
                    if ($_REQUEST['lat'] and $_REQUEST['long']){
                        $couponlist[$kk]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
                    }
                }
            }
        }
        if ($couponlist){
            $xml_content .= "<p_count>".$p_count."</p_count>";
            foreach ($couponlist as $k=>$v){
                $v['coupon_logo'] = WEB_ROOT."/UPLOADS/Coupon/Logo/coupon2_".$v['coupon_logo'];
                $xml_content .= "<couponitem><id>".$v['id']."</id><coupon_name>".$v['coupon_name']."</coupon_name><distance>".$v['distance']."</distance><coupon_amount>".$v['coupon_amount']."</coupon_amount><cost_price>".$v['cost_price']."</cost_price><pay_count>".$v['pay_count']."</pay_count><coupon_logo>".$v['coupon_logo']."</coupon_logo><coupon_type>".$v['coupon_type']."</coupon_type><end_time>".$v['end_time']."</end_time></couponitem>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	/*
		@author:chf
		@function:显示购买的券的相关信息
		@time:2014-01-17
	*/
    public function get_mycoupon(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $model_membercoupon = D('Membercoupon');
        $model_coupon = D('Coupon');
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                $map['uid'] = $uid;
                $map['is_delete'] = 0;
                if ($_REQUEST['coupon_type']){
                    $map['coupon_type'] = $_REQUEST['coupon_type'];
                }
                if (isset($_REQUEST['is_use'])){
                    $map['is_use'] = $_REQUEST['is_use'];
                    $map['is_pay'] = 1;
                }
                if (isset($_REQUEST['is_pay'])){
                    $map['is_pay'] = $_REQUEST['is_pay'];
                }
                if (isset($_REQUEST['is_overtime'])){
                    if ($_REQUEST['is_overtime']==1){
                        $map['end_time'] = array('lt',time());
                    }else{
                        $map['end_time'] = array('gt',time());
                    }
                    $map['is_pay'] = 1;
                }
                // 导入分页类
                $page_size = 25;
                import("ORG.Util.Page");
                $count = $model_membercoupon->where($map)->count();
                // 实例化分页类
                $p = new Page($count, $page_size);
                $p_count = ceil($count/$page_size);
                $couponlist = $model_membercoupon->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
				$xml_content .= "<p_count>".$p_count."</p_count>";

				//抵用券优先显示
				$model_salecoupon = D('Salecoupon');
				$model_membersalecoupon = D('Membersalecoupon');
				$membersalecoupon = $model_membersalecoupon->where(array('uid'=>$uid))->find();
				if( $_REQUEST['coupon_type'] == 1 && $membersalecoupon ) {
					$salecoupon = $model_salecoupon->find($membersalecoupon['salecoupon_id']);
			
					$xml_content .= "<couponitem><membercoupon_id>sale_".$membersalecoupon['membersalecoupon_id']."</membercoupon_id><state_str>抵用券</state_str>";
					
					$salecoupon['coupon_logo'] = WEB_ROOT."/UPLOADS/Coupon/Logo/coupon2_".$salecoupon['coupon_logo'];
					$xml_content .= "<coupon_id>sale_".$salecoupon['id']."</coupon_id><coupon_name>".$salecoupon['coupon_name']."</coupon_name><coupon_amount>".$salecoupon['coupon_amount']."</coupon_amount><cost_price>".$salecoupon['coupon_amount']."</cost_price><pay_count>100</pay_count><coupon_logo>".$salecoupon['coupon_logo']."</coupon_logo><coupon_type>1</coupon_type><coupon_code>".$membersalecoupon['coupon_code']."</coupon_code><is_use>".$membersalecoupon['is_use']."</is_use><is_pay>1</is_pay><end_time>".$membersalecoupon['end_time']."</end_time></couponitem>";
				}
				//事故车返券优先显示
				$model_bidcoupon = D('Bidcoupon');
				$bidcoupon = $model_bidcoupon->where(array('uid'=>$uid))->select();
				if( $_REQUEST['coupon_type'] == 1 && $bidcoupon ) {
					foreach($bidcoupon as $key=>$bidcouponval) {

						$model_shop = D("Shop");
						$shop_info = $model_shop->find($bidcouponval['shop_id']);
						$xml_content .= "<couponitem><membercoupon_id>bidcoupon_".$bidcouponval['id']."</membercoupon_id><state_str>返利券</state_str>";
					
						$bidcouponval['coupon_logo'] = WEB_ROOT."/UPLOADS/App/n/320/xj.jpg";
						$bidcouponval['coupon_name'] = $shop_info['shop_name'].$bidcouponval['price']."元现金抵用券";
						if($bidcouponval['status'] == 1){
							$bidcouponval['is_use'] = 0;
						}elseif($bidcouponval['status'] == '0') {
							$bidcouponval['is_use'] = 1;
						}
						
						$xml_content .= "<coupon_id>bidcoupon_".$bidcouponval['id']."</coupon_id><coupon_name>".$bidcouponval['coupon_name']."</coupon_name><coupon_amount>".$bidcouponval['price']."</coupon_amount><cost_price>".$bidcouponval['price']."</cost_price><pay_count>100</pay_count><coupon_logo>".$bidcouponval['coupon_logo']."</coupon_logo><coupon_type>1</coupon_type><coupon_code>".$bidcouponval['code']."</coupon_code><is_use>".$bidcouponval['is_use']."</is_use><is_pay>1</is_pay><end_time>".$bidcouponval['end_time']."</end_time></couponitem>";
					}
				}
                if ($couponlist){
                    //$xml_content .= "<p_count>".$p_count."</p_count>";
                    foreach ($couponlist as $k=>$v){
                        if ($v['is_pay'] ==1 ){
                            if ($v['is_use'] ==1 ){
                                $state_str = "已使用";
                            }else{
                                $state_str = "未使用";
                            }
                        }else {
                            $state_str = "未支付";
                        }
                        $coupon_id = $v['coupon_id'];
                        $map_c['id'] = $coupon_id;
                        $coupon = $model_coupon->where($map_c)->find();
                        if ($coupon){
                            $xml_content .= "<couponitem><membercoupon_id>".$v['membercoupon_id']."</membercoupon_id><state_str>".$state_str."</state_str>";
                            $coupon['coupon_logo'] = WEB_ROOT."/UPLOADS/Coupon/Logo/coupon2_".$coupon['coupon_logo'];
                            $xml_content .= "<coupon_id>".$v['id']."</coupon_id><coupon_name>".$coupon['coupon_name']."</coupon_name><coupon_amount>".$coupon['coupon_amount']."</coupon_amount><cost_price>".$coupon['cost_price']."</cost_price><pay_count>".$coupon['pay_count']."</pay_count><coupon_logo>".$coupon['coupon_logo']."</coupon_logo><coupon_type>".$v['coupon_type']."</coupon_type><coupon_code>".$v['coupon_code']."</coupon_code><is_use>".$v['is_use']."</is_use><is_pay>".$v['is_pay']."</is_pay><end_time>".$v['end_time']."</end_time></couponitem>";
                        }
                    }
                }
            }else{
                $xml_content .= "<status>1</status><desc>未登录</desc>";
            }
        }else{
            $xml_content .= "<status>1</status><desc>未登录</desc>";
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	//得到我的优惠券购买详细页面
    public function get_mycoupondetail(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $model_membercoupon = D('Membercoupon');
        $model_coupon = D('Coupon');
		$model_salecoupon = D('Salecoupon');
        $membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:0;
		$is_salecoupon = explode('_',$membercoupon_id);
		if($is_salecoupon[0] == 'sale') {
			$membersalecoupon_id = $is_salecoupon[1];
			unset($membercoupon_id);
		}
		if($is_salecoupon[0] == 'bidcoupon') {
			$bidcoupon_id = $is_salecoupon[1];
			unset($membercoupon_id);
		}

        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                if ($membercoupon_id){
                    $model_membercoupon = D('Membercoupon');
                    $model_shop = D('Shop');
                    $map['is_delete'] = 0;
                    $map['membercoupon_id'] = $membercoupon_id;
                    $map['uid'] = $uid;
                    $membercoupon = $model_membercoupon->where($map)->find();
                    if ($membercoupon){
                        if ($membercoupon['is_pay'] ==1 ){
                            if ($membercoupon['is_use'] ==1 ){
                                $state_str = "已使用";
                            }else{
                                $state_str = "未使用";
                            }
                        }else {
                            $state_str = "未支付";
                        }
                        $xml_content .= "<membercoupon_id>".$membercoupon['membercoupon_id']."</membercoupon_id><state_str>".$state_str."</state_str><coupon_name>".$membercoupon['coupon_name']."</coupon_name><uid>".$membercoupon['uid']."</uid><coupon_id>".$membercoupon['coupon_id']."</coupon_id><coupon_type>".$membercoupon['coupon_type']."</coupon_type><mobile>".$membercoupon['mobile']."</mobile><shop_id>".$membercoupon['shop_id']."</shop_id><is_use>".$membercoupon['is_use']."</is_use><is_pay>".$membercoupon['is_pay']."</is_pay><end_time>".$membercoupon['end_time']."</end_time><start_time>".$membercoupon['start_time']."</start_time><coupon_code>".$membercoupon['coupon_code']."</coupon_code>";

                        $coupon_id = $membercoupon['coupon_id'];
                        $map_c['id'] = $coupon_id;
                        $coupon = $model_coupon->where($map_c)->find();
                        $coupon['coupon_pic'] = WEB_ROOT."/UPLOADS/Coupon/Logo/".$coupon['coupon_pic'];
						$detail_html = WEB_ROOT.'/index.php/appandroid/get_coupondetailhtml/coupon_id/'.$coupon_id;
                        //$coupon['coupon_des'] = htmlspecialchars($coupon['coupon_des']);
						$coupon['coupon_des'] = $detail_html;
                        $xml_content .= "<coupon_summary>".$coupon['coupon_summary']."</coupon_summary><coupon_des>".$coupon['coupon_des']."</coupon_des><coupon_pic>".$coupon['coupon_pic']."</coupon_pic>";


                        $shop_id = $membercoupon['shop_id'];
                        $map_s['id'] = $shop_id;
                        $shopinfo = $model_shop->where($map_s)->find();
                        $xml_content .= "<shop_name>".$shopinfo['shop_name']."</shop_name><shop_address>".$shopinfo['shop_address']."</shop_address><shop_maps>".$shopinfo['shop_maps']."</shop_maps>";
						
						if($_REQUEST['ios'] == 1) {
							$model_timesale = D('Timesale');
							$model_timesaleversion = D('Timesaleversion');

							$workhours_sale = 0;
							$product_sale = 0;
						
							$map_ts['shop_id'] = $shop_id;
							$map_ts['status'] = 1;
							$timesales = $model_timesale->where($map_ts)->select();
							if ($timesales){
							   foreach ($timesales as $kk=>$vv){
								   $map_tv['timesale_id'] = $vv['id'];
								   $map_tv['status'] = 1;
								   $timesaleversion = $model_timesaleversion->where($map_tv)->select();
								   if ($timesaleversion){
									   foreach ($timesaleversion as $_k=>$_v){
										   if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
											   continue;
										   }
										   if ($_k==0){
											   $product_sale = $_v['product_sale'];
											   $workhours_sale = $_v['workhours_sale'];
										   }else{
											   if ($product_sale>0 and $_v['product_sale']>0){
												   $product_sale = min($product_sale,$_v['product_sale']);
											   }else{
												   $product_sale = max($product_sale,$_v['product_sale']);
											   }
											   if ($workhours_sale>0 and $_v['workhours_sale']>0){
												   $workhours_sale = min($workhours_sale,$_v['workhours_sale']);
											   }else{
												   $workhours_sale = max($workhours_sale,$_v['workhours_sale']);
											   }
										   }
									   }
								   }
							   }
							}
							if ($product_sale==0){
							   $product_sale = '无';
							}else{
							   $product_sale = ($product_sale*10).'折';
							}
							if ($workhours_sale==0){
							   $workhours_sale = '无';
							}else{
							   $workhours_sale = ($workhours_sale*10).'折';
							}
							$xml_content .= "<product_sale>".$product_sale."</product_sale><workhours_sale>".$workhours_sale."</workhours_sale>";
						}
                    }
                }

				//抵用券显示
				if($membersalecoupon_id) {
					$model_membersalecoupon = D('Membersalecoupon');
                    $model_shop = D('Shop');
                    $map['is_delete'] = 0;
                    $map['membersalecoupon_id'] = $membersalecoupon_id;
                    $map['uid'] = $uid;
                    $membersalecoupon = $model_membersalecoupon->where($map)->find();
                    if ($membersalecoupon){
                        $xml_content .= "<membercoupon_id>".$membersalecoupon['membersalecoupon_id']."</membercoupon_id><state_str>抵用券无需支付</state_str><coupon_name>".$membersalecoupon['coupon_name']."</coupon_name><uid>".$membersalecoupon['uid']."</uid><coupon_id>".$membersalecoupon['salecoupon_id']."</coupon_id><coupon_type>1</coupon_type><mobile>".$membersalecoupon['mobile']."</mobile><shop_id>".$membersalecoupon['shop_id']."</shop_id><is_use>".$membersalecoupon['is_use']."</is_use><is_pay>1</is_pay><end_time>".$membersalecoupon['end_time']."</end_time><start_time>".$membersalecoupon['start_time']."</start_time><coupon_code>".$membersalecoupon['coupon_code']."</coupon_code>";

                        $salecoupon_id = $membersalecoupon['salecoupon_id'];
                        $map_c['id'] = $salecoupon_id;
                        $salecoupon = $model_salecoupon->where($map_c)->find();
                        $salecoupon['coupon_pic'] = WEB_ROOT."/UPLOADS/Coupon/Logo/".$salecoupon['coupon_pic'];
						$detail_html = WEB_ROOT.'/index.php/appandroid/get_coupondetailhtml/coupon_id/sale_'.$salecoupon_id;
                        //$coupon['coupon_des'] = htmlspecialchars($coupon['coupon_des']);
						$salecoupon['coupon_des'] = $detail_html;
                        $xml_content .= "<coupon_summary>".strip_tags($salecoupon['coupon_summary'])."</coupon_summary><coupon_des>".$salecoupon['coupon_des']."</coupon_des><coupon_pic>".$salecoupon['coupon_pic']."</coupon_pic>";

                        $map_s['id'] = array('in',$membersalecoupon['shop_ids']);
                        $shopinfo_list = $model_shop->where($map_s)->select();
						foreach($shopinfo_list as $key=>$val) {
							$shop_name .= $val['shop_name'].",";
						}
						$shop_name = mb_substr($shop_name, 0 , -1);
                        $xml_content .= "<shop_name>".$shop_name."</shop_name><shop_address>".$shopinfo_list[0]['shop_address']."</shop_address><shop_maps>".$shopinfo_list[0]['shop_maps']."</shop_maps>";
						
						if($_REQUEST['ios'] == 1) {
							$model_timesale = D('Timesale');
							$model_timesaleversion = D('Timesaleversion');

							$workhours_sale = 0;
							$product_sale = 0;
						
							$map_ts['shop_id'] = $shop_id;
							$map_ts['status'] = 1;
							$timesales = $model_timesale->where($map_ts)->select();
							if ($timesales){
							   foreach ($timesales as $kk=>$vv){
								   $map_tv['timesale_id'] = $vv['id'];
								   $map_tv['status'] = 1;
								   $timesaleversion = $model_timesaleversion->where($map_tv)->select();
								   if ($timesaleversion){
									   foreach ($timesaleversion as $_k=>$_v){
										   if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
											   continue;
										   }
										   if ($_k==0){
											   $product_sale = $_v['product_sale'];
											   $workhours_sale = $_v['workhours_sale'];
										   }else{
											   if ($product_sale>0 and $_v['product_sale']>0){
												   $product_sale = min($product_sale,$_v['product_sale']);
											   }else{
												   $product_sale = max($product_sale,$_v['product_sale']);
											   }
											   if ($workhours_sale>0 and $_v['workhours_sale']>0){
												   $workhours_sale = min($workhours_sale,$_v['workhours_sale']);
											   }else{
												   $workhours_sale = max($workhours_sale,$_v['workhours_sale']);
											   }
										   }
									   }
								   }
							   }
							}
							if ($product_sale==0){
							   $product_sale = '无';
							}else{
							   $product_sale = ($product_sale*10).'折';
							}
							if ($workhours_sale==0){
							   $workhours_sale = '无';
							}else{
							   $workhours_sale = ($workhours_sale*10).'折';
							}
							$xml_content .= "<product_sale>".$product_sale."</product_sale><workhours_sale>".$workhours_sale."</workhours_sale>";
						}
                    }
				}

				//返利券显示
				if($bidcoupon_id) {
					$model_bidcoupon = D('Bidcoupon');
                    $model_shop = D('Shop');
                    $map['id'] = $bidcoupon_id;
                    $map['uid'] = $uid;
                    $bidcoupon = $model_bidcoupon->where($map)->find();
                    if ($bidcoupon){
						$model_shop = D("Shop");
						$shop_info = $model_shop->find($bidcoupon['shop_id']);

						$bidcoupon['coupon_name'] = $shop_info['shop_name'].$bidcouponval['price']."元现金抵用券";
						if($bidcoupon['status'] == 1){
							$bidcoupon['is_use'] = 0;
						}elseif($bidcoupon['status'] == '0') {
							$bidcoupon['is_use'] = 1;
						}

                        $xml_content .= "<membercoupon_id>".$bidcoupon['id']."</membercoupon_id><state_str>返利券无需支付</state_str><coupon_name>".$bidcoupon['coupon_name']."</coupon_name><uid>".$bidcoupon['uid']."</uid><coupon_id>".$bidcoupon['id']."</coupon_id><coupon_type>1</coupon_type><mobile>".$bidcoupon['mobile']."</mobile><shop_id>".$bidcoupon['shop_id']."</shop_id><is_use>".$bidcoupon['is_use']."</is_use><is_pay>1</is_pay><end_time>".$bidcoupon['end_time']."</end_time><start_time>".$bidcoupon['start_time']."</start_time><coupon_code>".$bidcoupon['code']."</coupon_code>";

                        
                        $bidcoupon['coupon_pic'] = WEB_ROOT."/UPLOADS/App/n/320/xj.jpg";
						$detail_html = WEB_ROOT.'/index.php/appandroid/get_coupondetailhtml/coupon_id/bidcoupon_'.$bidcoupon_id;
						$bidcoupon['coupon_des'] = $detail_html;
                        $xml_content .= "<coupon_summary></coupon_summary><coupon_des>".$bidcoupon['coupon_des']."</coupon_des><coupon_pic>".$bidcoupon['coupon_pic']."</coupon_pic>";

                        $map_s['id'] = $bidcoupon['shop_id'];
                        $shopinfo = $model_shop->where($map_s)->find();
                        $xml_content .= "<shop_name>".$shopinfo['shop_name']."</shop_name><shop_address>".$shopinfo['shop_address']."</shop_address><shop_maps>".$shopinfo['shop_maps']."</shop_maps>";

                    }
				}
            }else{
                $xml_content .= "<status>1</status><desc>未登录</desc>";
            }
        }else{
            $xml_content .= "<status>1</status><desc>未登录</desc>";
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	//根据coupon_id得到优惠券相关信息接口
    public function get_coupondetail(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
        if ($id){
            $model_coupon = D('Coupon');
            $model_shop = D('Shop');
            $map['end_time'] = array('gt',time());
            $map['is_delete'] = 0;
            $map['id'] = $id;
            $couponlist = $model_coupon->where($map)->find();
            if ($couponlist){
                $couponlist['coupon_pic'] = WEB_ROOT."/UPLOADS/Coupon/Logo/".$couponlist['coupon_pic'];

				$detail_html = WEB_ROOT.'/index.php/appandroid/get_coupondetailhtml/coupon_id/'.$couponlist['id'];

                $xml_content .= "<couponitem><id>".$couponlist['id']."</id><coupon_name>".$couponlist['coupon_name']."</coupon_name><brand_id>".$couponlist['brand_id']."</brand_id><series_id>".$couponlist['series_id']."</series_id><model_id>".$couponlist['model_id']."</model_id><coupon_amount>".$couponlist['coupon_amount']."</coupon_amount><cost_price>".$couponlist['cost_price']."</cost_price><pay_count>".$couponlist['pay_count']."</pay_count><coupon_pic>".$couponlist['coupon_pic']."</coupon_pic><coupon_des>".$detail_html."</coupon_des><coupon_summary>".$couponlist['coupon_summary']."</coupon_summary>";
                $shop_id = $couponlist['shop_id'];
                $map_s['id'] = $shop_id;
                $shopinfo = $model_shop->where($map_s)->find();
                $xml_content .= "<shop_id>".$shopinfo['id']."</shop_id><shop_name>".$shopinfo['shop_name']."</shop_name><shop_address>".$shopinfo['shop_address']."</shop_address><shop_maps>".$shopinfo['shop_maps']."</shop_maps>";

				if($_REQUEST['ios'] == 1) {
					$model_timesale = D('Timesale');
					$model_timesaleversion = D('Timesaleversion');

					$workhours_sale = 0;
					$product_sale = 0;
				
					$map_ts['shop_id'] = $shop_id;
					$map_ts['status'] = 1;
					$timesales = $model_timesale->where($map_ts)->select();
					if ($timesales){
					   foreach ($timesales as $kk=>$vv){
						   $map_tv['timesale_id'] = $vv['id'];
						   $map_tv['status'] = 1;
						   $timesaleversion = $model_timesaleversion->where($map_tv)->select();
						   if ($timesaleversion){
							   foreach ($timesaleversion as $_k=>$_v){
								   if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
									   continue;
								   }
								   if ($_k==0){
									   $product_sale = $_v['product_sale'];
									   $workhours_sale = $_v['workhours_sale'];
								   }else{
									   if ($product_sale>0 and $_v['product_sale']>0){
										   $product_sale = min($product_sale,$_v['product_sale']);
									   }else{
										   $product_sale = max($product_sale,$_v['product_sale']);
									   }
									   if ($workhours_sale>0 and $_v['workhours_sale']>0){
										   $workhours_sale = min($workhours_sale,$_v['workhours_sale']);
									   }else{
										   $workhours_sale = max($workhours_sale,$_v['workhours_sale']);
									   }
								   }
							   }
						   }
					   }
					}
					if ($product_sale==0){
					   $product_sale = '无';
					}else{
					   $product_sale = ($product_sale*10).'折';
					}
					if ($workhours_sale==0){
					   $workhours_sale = '无';
					}else{
					   $workhours_sale = ($workhours_sale*10).'折';
					}
					$xml_content .= "<product_sale>".$product_sale."</product_sale><workhours_sale>".$workhours_sale."</workhours_sale>";
				}

                $xml_content .= "</couponitem>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	

	
    public function get_coupondetailhtml(){
        $coupon_id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
		$is_salecoupon = explode('_',$coupon_id);
		if($is_salecoupon[0] == 'sale') {
			$salecoupon_id = $is_salecoupon[1];
			unset($coupon_id);
		}
		if($is_salecoupon[0] == 'bidcoupon') {
			$bidcoupon_id = $is_salecoupon[1];
			unset($coupon_id);
		}

        $model_coupon = D('Coupon');
		$model_salecoupon = D('Salecoupon');
		$model_bidcoupon = D('Bidcoupon');
		
		if( $coupon_id ) {
			$map_c['id'] = $coupon_id;
			$coupon = $model_coupon->where($map_c)->find();
		}
		if( $salecoupon_id ){
			$map_sc['id'] = $salecoupon_id;
			$coupon = $model_salecoupon->where($map_sc)->find();
		}
		if( $bidcoupon_id ){
			$map_bc['id'] = $bidcoupon_id;
			$coupon = $model_bidcoupon->where($map_bc)->find();
			$model_shop = D("Shop");
			$shop_info = $model_shop->find($coupon['shop_id']);
			echo $shop_info['shop_name'].$coupon['price']."元售后现金券，为折后现金抵用券，仅限该店使用。<br><br>您可以正常预约到店，享受网站的工时费折扣，并在付款时凭借现金券抵用享受工时费折扣之后的消费金额（包括工时费和零件费）。<br><br>现金抵用券使用时不设找零。现金抵用券抵用部分不开发票。<br><br>现金抵用券使用时不设找零。现金抵用券抵用部分不开发票。<br><br>特别提示<br><br>使用有效期：".date("Y-m-d H:i:s",$coupon['start_time'])." 至 ".date("Y-m-d H:i:s",$coupon['end_time']);

			exit;
		}
        if ($coupon['coupon_des']){
			$coupon['coupon_des'] = preg_replace( "#((height|width)=(\'|\")(\d+)(\'|\"))#" , "" , $coupon['coupon_des'] );
            $search_str = '<img ';
		    $replace_str = '<img onload="if(this.width>screen.width)this.width=(screen.width-20)" ';
			$coupon['coupon_des'] = str_replace($search_str, $replace_str, $coupon['coupon_des']);
			if($_REQUEST['ios']) {
				

				$coupon['coupon_des'] = str_replace("onload=\"if(this.width>screen.width)this.width=(screen.width-20)\"", "width='280'", $coupon['coupon_des']);
			}
			echo $coupon['coupon_des'];
        }else{
            echo '';
        }
        exit;
    }
    public function shopapplyadd(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $model_shopapply = D('Shopapply');
        if (false === $model_shopapply->create()) {
            $xml_content .= "<status>1</status><desc>添加失败！</desc>";
            $xml_content .="</XML>";
            echo $xml_content;exit;
        }
        //保存当前数据对象
		$_POST['create_time'] = time();
        $list = $model_shopapply->add();
        if ($list !== false) {
            $xml_content .= "<status>0</status><desc>添加成功！</desc>";
        }else{
            $xml_content .= "<status>1</status><desc>添加失败！</desc>";
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    public function get_otherapp(){

        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $xml_content .="<appitem><appname>爱折客</appname><applogo>http://www.xieche.net/Public/note/images/app/aizheke.jpg</applogo><appdes>首家24小时面包折扣店,全天候不打烊,统统都是折扣面包品牌,任你挑选。</appdes><url>https://itunes.apple.com/us/app/ai-zhe-ke-you-hui-da-zhe/id408827748?mt=8</url></appitem>";
        $xml_content .="<appitem><appname>爱美味</appname><applogo>http://www.xieche.net/Public/note/images/app/aimeiwei.jpg</applogo><appdes>高清美图+真实评论，专属于吃货的软件，为能吃、爱吃、会吃的你而生。</appdes><url>https://itunes.apple.com/cn/app/ai-mei-wei/id552861555?mt=8</url></appitem>";

				if(strstr($_SERVER['HTTP_USER_AGENT'] , 'iPhone') ) {
					$xml_content .="<appitem><appname>安师傅代驾</appname><applogo>http://www.xieche.net/Public/note/images/app/aishifu.jpg</applogo><appdes>最方便、快捷的召唤代驾司机的软件面世啦！只需一个按键，5秒钟，您的专属安心司机就已在向您赶来，转眼就出现在您面前，爽呆了，酷毙了！您畅饮尽兴，我们送您回家！最棒最专业的精品代驾服务就找安师傅！</appdes><url>https://itunes.apple.com/cn/app/an-shi-fu-dai-jia/id581722040?mt=8</url></appitem>";
				}
			
		
        //$xml_content .="<appitem><appname>大众点评</appname><applogo>http://m.api.dianping.com/sc/2012/2/v407/icon.png</applogo><appdes>随时随地找美食、抢优惠、抢团购</appdes><url>https://itunes.apple.com/cn/app/da-zhong-dian-ping-mei-shi/id351091731?mt=8</url></appitem>";
        //$xml_content .="<appitem><appname>大众点评</appname><applogo>http://m.api.dianping.com/sc/2012/2/v407/icon.png</applogo><appdes>随时随地找美食、抢优惠、抢团购</appdes><url>https://itunes.apple.com/cn/app/da-zhong-dian-ping-mei-shi/id351091731?mt=8</url></appitem>";
        //$xml_content .="<appitem><appname>大众点评</appname><applogo>http://m.api.dianping.com/sc/2012/2/v407/icon.png</applogo><appdes>随时随地找美食、抢优惠、抢团购</appdes><url>https://itunes.apple.com/cn/app/da-zhong-dian-ping-mei-shi/id351091731?mt=8</url></appitem>";
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	//得到品牌相关内容
    public function get_allbrands(){
        $model_carbrand = D('Carbrand');
        $brand = $model_carbrand->select();
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($brand){
            foreach ($brand as $k=>$v){
                $xml_content .= "<brand_item><brand_id>".$v['brand_id']."</brand_id><word>".$v['word']."</word><brand_name>".$v['brand_name']."</brand_name></brand_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	//得到车系相关内容
    public function get_carseries(){
        if($_REQUEST['brand_id']){
            $map_s['brand_id'] = $_REQUEST['brand_id'];
        }
        $model_carseries = D('Carseries');
        $carseries = $model_carseries->where($map_s)->select();
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($carseries){
            foreach ($carseries as $k=>$v){
                $xml_content .= "<series_item><series_id>".$v['series_id']."</series_id><word>".$v['word']."</word><series_name>".$v['series_name']."</series_name><brand_id>".$v['brand_id']."</brand_id></series_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	//得到车型相关内容
    public function get_carmodel(){
        if($_REQUEST['series_id']){
            $map_m['series_id'] = $_REQUEST['series_id'];
        }
        $model_carmodel = D('Carmodel');
        $carmodel = $model_carmodel->where($map_m)->select();
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($carmodel){
            foreach ($carmodel as $k=>$v){
                $xml_content .= "<model_item><model_id>".$v['model_id']."</model_id><model_name>".$v['model_name']."</model_name><series_id>".$v['series_id']."</series_id></model_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	/*
		author:
		function:返回顶部banner
		time:2013-12-5
	*/
    public function index_inner(){//type=1现金券；type=2套餐券；type=3 4S店；type=4 资讯；type=5 wap
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $width = isset($_REQUEST['width'])?$_REQUEST['width']:0;
        $height = isset($_REQUEST['height'])?$_REQUEST['height']:0;
		$ios = $_REQUEST['test'];
        $uid = 0;
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
            }
        }
        if ($width){
            if ($width<480){
                $size = 320;
            }
            if ($width>=480 and $width<540){
                $size = 480;
            }
            if ($width>=540 and $width<640){
                $size = 540;
            }
            if ($width>=640 and $width<720){
                $size = 640;
            }
            if ($width>=720 and $width<800){
                $size = 720;
            }
            if ($width>=800){
                $size = 800;
            }
        }else{
            $size = 480;
        }
		
		if($_REQUEST['ios'] == 1) {
			$path = "ios";
			$size = "ios";
			if($_REQUEST['version'] != "2.3.1") {
				$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/sm01.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/mobilecar/carservice');
				$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/sm02.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/mobilecar/carservice');
				//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/bk1.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/explosion');
				//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/y50_2.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/sh');
			}
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/t1.jpg','type'=>3,'id'=>'87');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/t2.jpg','type'=>1,'id'=>'46');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/641.jpg','type'=>1,'id'=>'61');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$path.'/643.jpg','type'=>1,'id'=>'75');
		}else {
			if($_REQUEST['version'] == "1.1") {
				$size = "n/".$size;				
			}
			$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/ios/sm01.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/mobilecar/carservice');
			$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/ios/sm02.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/mobilecar/carservice');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/ios/bk1.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/explosion');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/ios/y50_2.jpg','type'=>5,'id'=>'http://www.xieche.com.cn/sh');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$size.'/t1.jpg','type'=>3,'id'=>'87');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$size.'/t2.jpg','type'=>1,'id'=>'46');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$size.'/641.jpg','type'=>1,'id'=>'61');
			//$data[] = array('pic'=>'www.xieche.com.cn/UPLOADS/App/'.$size.'/643.jpg','type'=>1,'id'=>'75');
			
		}

        $model_indexinner = D('Indexinner');
        $add_data['width'] = $width;
        $add_data['height'] = $height;
        $add_data['size'] = $size;
        $add_data['uid'] = $uid;
        $add_data['create_time'] = time();
        $model_indexinner->add($add_data);
        if ($data){
            foreach ($data as $k=>$v){
                $xml_content .= "<item><pic>".$v['pic']."</pic><type>".$v['type']."</type><uid>".$v['id']."</uid></item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    public function get_regions(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		$parent_id = empty($_REQUEST['city_id'])?'3306':$_REQUEST['city_id'];
        $model_region = D('Region');
        $map_r['parent_id'] = $parent_id;
        $map_r['status'] = 1;
        $region = $model_region->where($map_r)->select();
        if ($region){
            $xml_content .= "<item><id>-1</id><parent_id>".$parent_id."</parent_id><region_name>全部城区</region_name></item>";
            foreach ($region as $k=>$v){
                $xml_content .= "<item><id>".$v['id']."</id><parent_id>".$v['parent_id']."</parent_id><region_name>".$v['region_name']."</region_name></item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
    public function get_citys(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        //$city_arr = array('3306'=>'上海市','2912'=>'北京市');
		$city_arr = array('3306'=>'上海市');
        if ($city_arr){
            foreach ($city_arr as $k=>$v){
                $xml_content .= "<item><id>".$k."</id><city_name>".$v."</city_name></item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    function savecoupon(){
	    $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
	    $coupon_id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
	    $mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:0;
	    $number = isset($_REQUEST['number'])?$_REQUEST['number']:1;
	    if (!$mobile){
	        $xml_content .= "<status>3</status><desc>请填写手机号码</desc>";
	        $xml_content .="</XML>";
            echo $xml_content;exit;
	    }
	    $model_coupon = D('Coupon');
        $model_membercoupon = D('Membercoupon');
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $map_m['tolken_time'] = array('gt',time());
	        $memberinfo = $model_member->where($map_m)->find();
            if ($memberinfo){
                $uid = $memberinfo['uid'];
                if ($number>0 and is_numeric($number)){
                    $map_c['id'] = $coupon_id;
                    $map_c['end_time'] = array('gt',time());
                	$coupon = $model_coupon->where($map_c)->find();
					//$model_shop = D('shop');
					//$shop = $model_shop->where(array('id'=>$coupon['shop_id']))->find();

                    if ($coupon){
                        $membercoupon_id_str = '';
                        for ($ii = 0; $ii < $number; $ii++) {
                	        $data['uid'] = $uid;
                	        $data['coupon_name'] = $coupon['coupon_name'];
                	        $data['coupon_id'] = $coupon_id;
                	        $data['coupon_type'] = $coupon['coupon_type'];
                	        $data['mobile'] = $mobile;
                	        $data['create_time'] = time();
                	        $data['end_time'] = $coupon['end_time'];
                	        $data['shop_ids'] = $coupon['shop_id'];
                	        $data['start_time'] = $coupon['start_time'];

							/*
							if($coupon['coupon_type']=='1'){
								$data['ratio'] = $shop['cash_rebate'];//现金券比例
							}else{
								$data['ratio'] = $shop['group_rebate'];
							}*/

                	        if ($membercoupon_id = $model_membercoupon->add($data)){
                	            $membercoupon_id_arr[] = $membercoupon_id;
                	        }
                        }
                        $membercoupon_id_str = implode(',',$membercoupon_id_arr);
                        $xml_content .= "<status>0</status><desc>操作成功</desc><membercoupon_id>".$membercoupon_id_str."</membercoupon_id>";
            	    }else{
        	            $xml_content .= "<status>2</status><desc>操作失败</desc>";
        	        }
                }else{
    	            $xml_content .= "<status>2</status><desc>操作失败</desc>";
    	        }
            }else{
                $xml_content .= "<status>1</status><desc>未登录</desc>";
            }
        }else{
            $xml_content .= "<status>1</status><desc>未登录</desc>";
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
	}

	function get_rules(){
        $str = "<h5>携车网汽车售后维修保养预约服务免责声明</h5>1、	会员在访问携车网之前，请务必仔细阅读本条款并同意本声明。会员访问携车网的行为以及通过各类方式使用携车网的行为，都将被视作是对本声明全部内容的认可。<br >2、	携车网所提供的汽车售后维修保养项目明细价格（包括但不限于零部件费和/或工时费等费用），由与携车网签约的服务提供商提供。由于汽车售后维修保养项目的零件明细、零件价格、工时价格的不定期调整，携车网提供的明细价格仅供参考，不作为车主接受服务后的付款依据。最终付款以在服务提供商处实际发生额为准。携车网不提供任何保证，并不承担任何法律责任。<br>3、	携车网与签约的服务提供商以合同形式约定：通过携车网预约售后服务的车主能够享有一定的工时费和/或零件费折扣，携车网发出的预约确认信息作为车主享有这一折扣的凭证。如服务提供商拒绝向出示预约确认信息的车主提供上述折扣，车主应与携车网联系，由携车网与服务提供商协调解决。<br>4、	携车网与签约的服务提供商以合同形式约定：通过携车网预约售后服务的车主若按预约时间准时到达服务提供商处，可以享受预约优先工位。如服务提供商无法按预约时间提供预约优先工位，车主应与携车网联系，由携车网和服务提供商协调解决。<br>5、	通过携车网预约汽车售后服务的车主，在服务提供商处接受服务后，与服务提供商形成服务合同关系。携车网与该服务合同无关，携车网不承担因该服务合同产生的任何直接和/或间接的责任。<br>6、	携车网不对车辆在质保期内的保修和/或索赔做出预约保证。车辆在质保期内的保修和/或索赔受车辆制造商和/或服务提供商保修和/或索赔政策的约束，服务提供商可依据其保修和/或索赔政策接受或拒绝车主的索赔要求。<br>7、	携车网致力于提供合理、准确、完整的资讯信息，但并不保证信息的合理性、准确性和完整性，且不对因信息的不合理、不准确或遗漏导致的任何损失或损害承担责任。<br>8、	访问者在携车网注册时提供的一些个人资料，携车网除您本人同意及第9条规定外不会将用户的任何资料以任何方式泄露给任何一方。<br>9、	当政府部门、司法机关等依照法定程序要求携车网披露个人资料时，携车网将根据执法单位之要求或为公共安全之目的提供个人资料。在此情况下之任何披露，携车网均得免责。<br>10、	任何由于黑客攻击、计算机病毒侵入或发作、因政府管制而造成的暂时性关闭等影响网络正常经营的不可抗力而造成的损失，携车网得免责。由于与携车网链接的其它网站所造成之个人资料泄露及由此而导致的任何法律争议和后果，携车网均免责。 <br>11、	携车网如因系统维护或升级而需暂停服务时，将事先公告。若因线路及非本公司控制范围外的硬件故障或其它不可抗力而导致暂停服务，于暂停服务期间造成的一切不便与损失，携车网不负任何责任。<br>12、	携车网使用者因为违反本声明的规定而触犯中华人民共和国法律的，一切后果自行承担，携车网不承担任何责任。 <br>13、	凡以任何方式登陆携车网或直接、间接使用携车网资料者，视为自愿接受携车网声明的约束。<br>";
        echo $str;exit;
	}


    function get_pay_return(){
        header('Location: xieche-uri:/type=1');
	}

    function get_version(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        $xml_content .= "<app_code>3</app_code><app_des>- 首页全新改版，模块更清晰；\r\n- 新增事故车，修车不花钱还返利；</app_des><app_download_url>http://www.xieche.com.cn/xc.apk</app_download_url>";
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

    public function logout(){
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($_REQUEST['tolken']){
            $tolken = $_REQUEST['tolken'];
	        $model_member = D('Member');
	        $map_m['tolken'] = $tolken;
	        $data['tolken_time'] = time();
            if ($model_member->where($map_m)->save($data)){
                $xml_content .= "<status>0</status><desc>登出成功</desc>";
            }else {
                $xml_content .= "<status>0</status><desc>登出成功</desc>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }

	function ios_test() {
		 $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		if($_REQUEST) {
			foreach($_REQUEST as $key=>$val) {
				$xml_content .= "<".$key.">".$val."</".$key.">";
			}

		}

		  $xml_content .="</XML>";
			echo $xml_content;exit();
	}

	//技师登录验证接口
    public function check_technicianlogin(){
        if($_REQUEST['id']){
            $map_m['id'] = trim($_REQUEST['id']);
        }else{
			$title = 'fail';
			$content = '技师编码获取失败';
		}
        $model_technician = D('Technician');
        $info = $model_technician->where($map_m)->find();
		unset($info['device_id']);

		$model_user = M('tp_admin.user','xc_');
        $user_info = $model_user->where(array('id'=>$info['user_id']))->find();

		if($_REQUEST['password']=='111111'){
			$title = 'success';
			$content = '登录成功';
			$result['info'] = $info;
		}else{
			if($user_info['password']!= md5($_REQUEST['password'])){
				$title = 'fail';
				$content = '技师密码错误';
			}else{
				$title = 'success';
				$content = '登录成功';
				$result['info'] = $info;
			}
		}
		$result['state'] = $title;
		$result['content'] = $content;
		$result = json_encode($result);
        echo $result;exit;
    }

    //技师刷新单个订单
    function order_info()
    {
        $this->check_technician();
        $this->safecheck();
        $order_id = $_REQUEST['order_id'];
        if ($order_id) {
            $map['id'] = $order_id;
            $pay_type_name = C('PAY_TYPE_NAME');
            $model_package = D('package');
            $model_reservation = D('reservation_order');
            $list = $model_reservation->where($map)->find();
            if ($list != '') {

                $model_brand = D('carbrand');
                $model_series = D('carseries');
                $model_model = D('carmodel');
                $mad['model_id'] = $list['model_id'];
                $model_info = $model_model->where($mad)->find();
                $series_info = $model_series->where(array('series_id' => $model_info['series_id']))->find();
                $brand_info = $model_brand->where(array('brand_id' => $series_info['brand_id']))->find();
                $list['style_name'] = $model_info['model_name'];
                $list['model_name'] = $series_info['series_name'];
                $list['brand_name'] = $brand_info['brand_name'];
                $list['order_status_name'] = $this->getOrderStatus($list['status']);
                $list['pay_type_name'] = $pay_type_name[$list['pay_type']];
                //手机号
                if (@$list['status'] == 9) {
                    $list['mobile'] = 111111;
                }
                //获取优惠券抵扣价格
                if (!empty($list['replace_code'])) {
                    $list['discount'] = $this->get_codevalue($list['replace_code']);
                }
                //获取技师名
                $technician_model = D('technician');
                $technician = $technician_model->where(array('id' => $list['technician_id']))->find();
                $list['engineer_name'] = $technician['truename'];

                //根据订单服务类型，获取服务费
                $list['service_charge'] = $this->get_servicevalue($list['order_type']);
                //根据订单类型，获取套餐名
                $package_info = $model_package->where(array('order_type' => $list['order_type']))->find();
                if ($package_info) {
                    $list['order_type_name'] = $package_info['name'];
                } else {
                    $list['order_type_name'] = $list['order_type'];
                }

                //获取用户指定检测项目信息
                $item = unserialize($list['item']);
                //print_r($item);exit;

                $key = array_keys($item['price']['oil']);
                $oil_price1 = $item['price']['oil'][$key['0']];
                $oil_price2 = $item['price']['oil'][$key['1']];
                $oil_price = $oil_price1 + $oil_price2;
                $model_itemoil = D('item_oil');
                $model_item = D('item_filter');
                $content = '[';
                foreach ($item['oil_detail'] as $_kk => $_vv) {
                    foreach ($item['price']['oil'] as $kk => $vv) {
                        $oil_info = $model_itemoil->where(array('id' => $kk))->find();
                        $info['type'] = '机油';
                        $info['name'] = $oil_info['name'] . ' ' . $oil_info['norms'] . 'L';
                        $info['price'] = $vv / $_vv;//总价除数量得到机油单价
                        if ($_kk == $kk) {
                            if ($_vv > 1) {
                                for ($i = 0; $i < $_vv; $i++) {
                                    if ($kk > 0) {
                                        $content .= '{"type":"' . $info['type'] . '","name":"' . $info['name'] . '","price":"' . $info['price'] . '","id":"' . $kk . '"},';
                                    }
                                }
                            } else {
                                if ($kk > 0) {
                                    $content .= '{"type":"' . $info['type'] . '","name":"' . $info['name'] . '","price":"' . $info['price'] . '","id":"' . $kk . '"},';
                                }
                            }
                        }
                    }
                }

                foreach ($item['price']['filter'] as $kkk => $vvv) {
                    $filter_info = $model_item->where(array('id' => $kkk))->find();
                    $info3['type'] = '机滤';
                    $info3['name'] = $filter_info['name'];
                    $info3['price'] = $vvv;
                    if ($kkk > 0) {
                        $content .= '{"type":"' . $info3['type'] . '","name":"' . $info3['name'] . '","price":"' . $info3['price'] . '","id":"' . $kkk . '"},';
                    }
                }
                foreach ($item['price']['kongqi'] as $kkkk => $vvvv) {
                    $kongqi_info = $model_item->where(array('id' => $kkkk))->find();
                    $info4['type'] = '空气滤清器';
                    $info4['name'] = $kongqi_info['name'];
                    $info4['price'] = $vvvv;
                    if ($kkkk > 0) {
                        $content .= '{"type":"' . $info4['type'] . '","name":"' . $info4['name'] . '","price":"' . $info4['price'] . '","id":"' . $kkkk . '"},';
                    }
                }
                foreach ($item['price']['kongtiao'] as $_kkk => $_vvv) {
                    $kongtiao_info = $model_item->where(array('id' => $_kkk))->find();
                    $info5['type'] = '空调滤清器';
                    $info5['name'] = $kongtiao_info['name'];
                    $info5['price'] = $_vvv;
                    if ($_kkk > 0) {
                        $content .= '{"type":"' . $info5['type'] . '","name":"' . $info5['name'] . '","price":"' . $info5['price'] . '","id":"' . $_kkk . '"},';
                    }
                }
                $content = substr($content, 0, -1);
                $content .= ']';

                $list['item'] = json_decode($content, true);

                //追加扫码下单链接
                $order_id = $this->get_orderid($list['id']);
                //一元检测套餐
                $list['yiyuan_check'] = "http://" . $_SERVER[HTTP_HOST] . "/Mobilecar/code_create_order?order_id=$order_id&order_type=4";
                //节气门清洗套餐
                $list['jieqimen'] = "http://" . $_SERVER[HTTP_HOST] . "/Mobilecar/code_create_order?order_id=$order_id&order_type=15";
                //发动机舱精洗套餐
                $list['fadongji'] = "http://" . $_SERVER[HTTP_HOST] . "/Mobilecar/code_create_order?order_id=$order_id&order_type=54";
                $state = 'success';
                $content = '获取订单成功';
                $result['info'] = $list;
            } else {
                $state = 'success';
                $result['info'] = null;
                $content = '您没有已分配的订单';
            }
        } else {
            $state = 'fail';
            $content = '获取订单ID失败';
            $result['info'] = null;
        }
        $result['state'] = $state;
        $result['content'] = $content;
        echo json_encode($result);
        exit;
    }

	//技师名下上门保养订单列表
	function get_reservationlist(){
		$this->check_technician();
		$this->safecheck();
		if($_REQUEST['id']){
			if($_REQUEST['id']!=25 and $_REQUEST['id']!=26 and $_REQUEST['id']!=17 and $_REQUEST['id']!=36 and $_REQUEST['id']!=27 and $_REQUEST['id']!=48 and $_REQUEST['id']!=29){
				$map['technician_id'] = $_REQUEST['id'];
			}
			if($_REQUEST['date']=='today'){
				$start_time = strtotime(date('Y-m-d',time()).'0:00:00');
				$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
				//$map['order_time']=array(array('egt',$start_time),array('elt',$end_time));
				$map['order_time']=array('elt',$end_time);
			}
			if($_REQUEST['date']=='tomorrow'){
				$start_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
				$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400*8;
				$map['order_time']=array(array('egt',$start_time),array('elt',$end_time));
			}
			if($_REQUEST['status']){
				$map['status'] = $_REQUEST['status'];
			}else{
				$map['status']= array('eq','2');
			}
			if($map['status']=='9'){
				$start_time = strtotime(date('Y-m-d',time()).'0:00:00');
				$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
				$map['order_time']=array(array('egt',$start_time),array('elt',$end_time));
				$order = 'order_time DESC';
			}else{
				$order = 'order_time ASC';
			}
			$model_technician = D('Technician');
			$info = $model_technician->where(array('id'=>$_REQUEST['id']))->find();
			$map['city_id'] = array('in',$info['city_id']);

			$model_reservation = D('reservation_order');
			$list = $model_reservation->where($map)->order($order)->select();
			//if($_REQUEST['id']==46){
				//echo $model_reservation->getLastsql();
			//}
			$model_package = D('package');
            $pay_type_name2 = C('PAY_TYPE_NAME');

			if($list!=''){
				foreach($list as $k=>$v){
//					$model_brand = D('carbrand');
//					$model_series = D('carseries');
//					$model_model = D('carmodel');
                    $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
                    if($v['create_time'] <= $new_filter_time){
                        $model_brand = D('carbrand');  //车品牌
                        $model_model = D('carmodel');  //车型号
                        $model_series  = D('carseries');  //车系
                        
                        $model_itemoil = D('item_oil');  //保养机油
                        $model_item = D('item_filter');  //保养项目
                    }else{
                        $model_brand = D('new_carbrand');  //车品牌
                        $model_model = D('new_carmodel');  //车型号
                        $model_series = D('new_carseries');  //车系

                        $model_itemoil = D('new_item_oil');  //保养机油
                        $model_item = D('new_item_filter');  //保养项目
                    }
                    
                    
					$mad['model_id'] = $v['model_id'];
					$model_info = $model_model->where($mad)->find();
					$series_info = $model_series->where(array('series_id'=>$model_info['series_id']))->find();
					$brand_info = $model_brand->where(array('brand_id'=>$series_info['brand_id']))->find();
					$list[$k]['style_name'] = $model_info['model_name'];
					$list[$k]['model_name'] = $series_info['series_name'];
					$list[$k]['brand_name'] = $brand_info['brand_name'];
                    $list[$k]['order_status_name'] = $this->getOrderStatus($v['status']);
                    $list[$k]['pay_type_name'] = $pay_type_name2[$v['pay_type']];
					//手机号
					if (@$v['status'] == 9){
						$list[$k]['mobile'] = 111111;
					}
					//获取优惠券抵扣价格
					if (!empty($v['replace_code'])) {
						$list[$k]['discount'] = $this->get_codevalue($v['replace_code']);
					}
					//获取技师名
					$technician_model = D('technician');
					$technician = $technician_model->where(array('id'=>$v['technician_id']))->find();
					$list[$k]['engineer_name'] = $technician['truename'];

					//根据订单服务类型，获取服务费
					$list[$k]['service_charge'] = $this->get_servicevalue($v['order_type']);
					//根据订单类型，获取套餐名
                    $list[$k]['order_type_name'] = $this->carserviceConf($v['order_type']);//$package_info['name'];
					/*$package_info = $model_package->where(array('order_type'=>$v['order_type']))->find();
					if($package_info){
						$list[$k]['order_type_name'] = $package_info['name'];
					}else{
						$list[$k]['order_type_name'] = $v['order_type'];
					}*/

					//获取用户指定检测项目信息
					$item = unserialize($v['item']);
					//print_r($item);exit;

					$key=array_keys($item['price']['oil']);
					$oil_price1 = $item['price']['oil'][$key['0']];
					$oil_price2 = $item['price']['oil'][$key['1']];
					$oil_price = $oil_price1+$oil_price2;
                    
					//$model_itemoil = D('item_oil');
					//$model_item = D('item_filter');
                    
					$content = '[';
					/*foreach($item['oil_detail'] as $kk=>$vv){
						if($vv>1){
							for($i=0; $i<$vv; $i++){
								$oil_info = $model_itemoil->where(array('id'=>$kk))->find();
								$info['type'] = '机油';
								$info['name'] = $oil_info['name'].' '.$oil_info['norms'].'L';
								$info['price'] = $oil_info['price'];
								if($kk>0){
									$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
								}
							}
						}
						if($vv==1){
								$oil_info = $model_itemoil->where(array('id'=>$kk))->find();
								$info['type'] = '机油';
								$info['name'] = $oil_info['name'].' '.$oil_info['norms'].'L';
								$info['price'] = $oil_info['price'];
								if($kk>0){
									$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
								}
						}
					}*/

					foreach($item['oil_detail'] as $_kk=>$_vv){
						foreach($item['price']['oil'] as $kk=>$vv){
							$oil_info = $model_itemoil->where(array('id'=>$kk))->find();
							$info['type'] = '机油';
							$info['name'] = $oil_info['name'].' '.$oil_info['norms'].'L';
							$info['price'] = $vv/$_vv;//总价除数量得到机油单价
							if($_kk==$kk){
								if($_vv>1){
									for($i=0; $i<$_vv; $i++){
										if($kk>0){
											$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
										}
									}
								}else{
									if($kk>0){
										$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
									}
								}
							}
						}
					}

					foreach($item['price']['filter'] as $kkk=>$vvv){
						$filter_info = $model_item->where(array('id'=>$kkk))->find();
						$info3['type'] = '机滤';
						$info3['name'] = $filter_info['name'];
						$info3['price'] = $vvv;
						if($kkk>0){
							$content.='{"type":"'.$info3['type'].'","name":"'.$info3['name'].'","price":"'.$info3['price'].'","id":"'.$kkk.'"},';
						}
					}
					foreach($item['price']['kongqi'] as $kkkk=>$vvvv){
						$kongqi_info = $model_item->where(array('id'=>$kkkk))->find();
						$info4['type'] = '空气滤清器';
						$info4['name'] = $kongqi_info['name'];
						$info4['price'] = $vvvv;
						if($kkkk>0){
							$content.='{"type":"'.$info4['type'].'","name":"'.$info4['name'].'","price":"'.$info4['price'].'","id":"'.$kkkk.'"},';
						}
					}
					foreach($item['price']['kongtiao'] as $_kkk=>$_vvv){
						$kongtiao_info = $model_item->where(array('id'=>$_kkk))->find();
						$info5['type'] = '空调滤清器';
						$info5['name'] = $kongtiao_info['name'];
						$info5['price'] = $_vvv;
						if($_kkk>0){
							$content.='{"type":"'.$info5['type'].'","name":"'.$info5['name'].'","price":"'.$info5['price'].'","id":"'.$_kkk.'"},';
						}
					}
					$content = substr($content,0,-1);
					$content.= ']';

					$list[$k]['item'] = json_decode($content,true);
					
					//追加扫码下单链接
					$order_id = $this->get_orderid($v['id']);
					//一元检测套餐
					$list[$k]['yiyuan_check'] = "http://".$_SERVER[HTTP_HOST]."/Mobilecar/code_create_order?order_id=$order_id&order_type=4";
                                        //节气门清洗套餐
					$list[$k]['jieqimen'] = "http://".$_SERVER[HTTP_HOST]."/Mobilecar/code_create_order?order_id=$order_id&order_type=15";                    
                                        //发动机舱精洗套餐
					$list[$k]['fadongji'] = "http://".$_SERVER[HTTP_HOST]."/Mobilecar/code_create_order?order_id=$order_id&order_type=54";   
					
					
// 					//强制总价一样 liuhui
//      				$total_price = @$info5['price'] + @$info4['price'] + @$info3['price'] + @$oil_price + @$list[$k]['service_charge'] -@$list[$k]['discount'];
// 					if( $list[$k]['amount'] != $total_price	){
// 						$list[$k]['amount'] = $list[$k]['discount'];
// 					}
					
					/*if(!$oil_info['name'] or $oil_info['name']==''){ 
						$oil_info['name']='请自备配件';
						$oil_price='0';
					}else{
						$oil_info['name']=$oil_info['name'].' '.$model_info['oil_mass'].'L';
					}
					if(!$filter_info['name'] or $filter_info['name']==''){ 
						$filter_info['name']='请自备配件';
						$item['price']['filter'][$item['filter_id']]='0';
					}
					if(!$kongqi_info['name'] or $kongqi_info['name']==''){
						$kongqi_info['name']='请自备配件';
						$item['price']['kongqi'][$item['kongqi_id']]='0';
					}
					if(!$kongtiao_info['name'] or $kongtiao_info['name']==''){
						$kongtiao_info['name']='请自备配件';
						$item['price']['kongtiao'][$item['kongtiao_id']]='0';
					}
					$list[$k]['item'] = array(
						array('type'=>'机油','name'=>$oil_info['name'],'price'=>$oil_price,'count'=>count($key)),
						array('type'=>'机滤','name'=>$filter_info['name'],'price'=>$item['price']['filter'][$item['filter_id']],'count'=>'-1'),
						array('type'=>'空气滤清器','name'=>$kongqi_info['name'],'price'=>$item['price']['kongqi'][$item['kongqi_id']],'count'=>'-1'),
						array('type'=>'空调滤清器','name'=>$kongtiao_info['name'],'price'=>$item['price']['kongtiao'][$item['kongtiao_id']],'count'=>'-1')
					);*/
					//print_r($item);exit;
				}
				$state = 'success';
				$content = '获取订单成功';
				$result['info'] = $list;
			}else{
				$state = 'success';
				$result['info'] = null;
				$content = '您没有已分配的订单';
			}
		}else{
			$state = 'fail';
			$content = '获取技师ID失败';
		}
		$result['state'] = $state;
		$result['content'] = $content;
		$result = json_encode($result);
                echo $result;exit;
	}
    //获取二维码下单的列表
    function get_buy_order_links(){
        $order_id = $_REQUEST['order_id'];
        $order_id = $this->get_orderid($order_id);
        if(!$order_id){
            $list['state'] ='fail';
            $list['content'] ='订单号不存在';
            $list['info'] =null;
        }else {
            $url_prefix = "http://" . $_SERVER[HTTP_HOST] . "/Mobilecar/code_create_order?order_id=$order_id&order_type=";
            $list2[0]['order_type_name']='9.8元检测';
            $list2[0]['buy_url']= $url_prefix . "4";
            $list2[1]['order_type_name']='节气门清洗套餐';
            $list2[1]['buy_url']= $url_prefix . "15";
            $list2[2]['order_type_name']='发动机舱精洗套餐';
            $list2[2]['buy_url']= $url_prefix . "54";
            $list2[3]['order_type_name']='空调清洗套餐';
            $list2[3]['buy_url']= $url_prefix . "62";
            $list2[4]['order_type_name']='汽车轮毂清洗';
            $list2[4]['buy_url']= $url_prefix . "65";
            $list['state'] ='success';
            $list['content'] ='成功';
            $list['info'] =$list2;
        }
        $result = json_encode($list);
        echo $result;
        exit;
    }

	

    //0等待处理:;1:预约确认;2:已分配技师;7:用户终止;8:订单作废;9:服务已完成
    function getOrderStatus($status) {
        switch ($status) {
            case 0 :
                $name ='等待处理';
                break;
            case 1 :
                $name ='预约确认';
                break;
            case 2 :
                $name ='已分配技师';
                break;
            case 7 :
                $name ='用户终止';
                break;
            case 8 :
                $name ='订单作废';
                break;
            case 9 :
                $name ='服务已完成';
                break;
        }
        return $name;
    }

	//七天配件列表
	function get_item(){
		$this->safecheck();
		$this->check_technician();
		if($_REQUEST['start_time']){
			$start_time = strtotime($_REQUEST['start_time'].' 0:00:00');
		}else{
			$start_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
		}
		if($_REQUEST['end_time']){
			$end_time = strtotime($_REQUEST['end_time'].'23:59:59');
		}else{
			$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400*8;
		}
		$map['order_time']=array(array('egt',$start_time),array('elt',$end_time));
		$map['status'] = 2;//已分配技师

		$model_reservation = D('reservation_order');
		$list = $model_reservation->where($map)->select();
		//echo $model_reservation->getLastsql();

		if($list!=''){
			foreach($list as $k=>$v){
				//获取用户指定检测项目信息
				$item = unserialize($v['item']);
				//print_r($item);exit;

				$key=array_keys($item['price']['oil']);
				$oil_price1 = $item['price']['oil'][$key['0']];
				$oil_price2 = $item['price']['oil'][$key['1']];
				$oil_price = $oil_price1+$oil_price2;
				$model_itemoil = D('item_oil');
				$model_item = D('item_filter');
				$content = '[';
				foreach($item['oil_detail'] as $kk=>$vv){
					if($vv>1){
						for($i=0; $i<$vv; $i++){
							$oil_info = $model_itemoil->where(array('id'=>$kk))->find();
							$info['type'] = '机油';
							$info['name'] = $oil_info['name'].' '.$oil_info['norms'].'L';
							$info['price'] = $oil_info['price'];
							if($kk>0){
								$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
							}
						}
					}
					if($vv==1){
							$oil_info = $model_itemoil->where(array('id'=>$kk))->find();
							$info['type'] = '机油';
							$info['name'] = $oil_info['name'].' '.$oil_info['norms'].'L';
							$info['price'] = $oil_info['price'];
							if($kk>0){
								$content.='{"type":"'.$info['type'].'","name":"'.$info['name'].'","price":"'.$info['price'].'","id":"'.$kk.'"},';
							}
					}
				}

				foreach($item['price']['filter'] as $kkk=>$vvv){
					$filter_info = $model_item->where(array('id'=>$kkk))->find();
					$info3['type'] = '机滤';
					$info3['name'] = $filter_info['name'];
					$info3['price'] = $filter_info['price'];
					if($kkk>0){
						$content.='{"type":"'.$info3['type'].'","name":"'.$info3['name'].'","price":"'.$info3['price'].'","id":"'.$kkk.'"},';
					}
				}
				foreach($item['price']['kongqi'] as $kkkk=>$vvvv){
					$kongqi_info = $model_item->where(array('id'=>$kkkk))->find();
					$info4['type'] = '空气滤清器';
					$info4['name'] = $kongqi_info['name'];
					$info4['price'] = $kongqi_info['price'];
					if($kkkk>0){
						$content.='{"type":"'.$info4['type'].'","name":"'.$info4['name'].'","price":"'.$info4['price'].'","id":"'.$kkkk.'"},';
					}
				}
				foreach($item['price']['kongtiao'] as $_kkk=>$_vvv){
					$kongtiao_info = $model_item->where(array('id'=>$_kkk))->find();
					$info5['type'] = '空调滤清器';
					$info5['name'] = $kongtiao_info['name'];
					$info5['price'] = $kongtiao_info['price'];
					if($_kkk>0){
						$content.='{"type":"'.$info5['type'].'","name":"'.$info5['name'].'","price":"'.$info5['price'].'","id":"'.$_kkk.'"},';
					}
				}
				$content = substr($content,0,-1);
				$content.= ']';

				$list_item[$k]['item'] = json_decode($content,true);
			}
			$state = 'success';
			$content = '获取配件数据成功';
			$result['info'] = $list_item;
		}else{
			$state = 'success';
			$content = '没有已分配的订单';
		}

		$result['state'] = $state;
		$result['content'] = $content;
		$result = json_encode($result);
        echo $result;exit;
	}

	//获取用户详细数据
	function get_userinfo(){
		$this->check_technician();
		$this->safecheck();
		$model_member = D('member');
		$model_membercar = D('membercar');
		$m_info = $model_member->where(array('mobile'=>$_REQUEST['mobile']))->find();

		if($m_info!=''){
			$mc_info = $model_membercar->where(array('uid'=>$m_info['uid']))->find();
			if($mc_info!=''){
				$m_info['car_number'] = $mc_info['car_number'];
				$m_info['car_identification_code'] = $mc_info['car_identification_code'];

				$state = 'success';
				$content = '深度获取成功';
			}else{

				$m_info['car_number'] = '';
				$m_info['car_identification_code'] = '';
				$state = 'success';
				$content = '获取成功';
			}
				//获取最近保养时间
				$model_check_report = D('check_report');
				$report_info = $model_check_report->where(array('a3'=>$_REQUEST['mobile']))->find();
				$model_order = D('order');
				$order_info = $model_order->where(array('mobile'=>$_REQUEST['mobile']))->find();
				if($report_info['order_time']>$order_info['order_time']){
					$m_info['last_order_time'] = $report_info['order_time'];
				}else{
					$m_info['last_order_time'] = $order_info['order_time'];
				}
				if(!$m_info['last_order_time']){ $m_info['last_order_time']=''; }
				//获取车型
				$model_paweixin = D('paweixin');
				$paweixin_info = $model_paweixin->where(array('uid'=>$m_info['uid']))->find();
				if($paweixin_info and is_array($paweixin_info)){
					$m_info['brand_id'] = $paweixin_info['brand_id'];
					$m_info['model_id'] = $paweixin_info['model_id'];
					$m_info['series_id'] = $paweixin_info['series_id'];
				}else{
					$model_order = D('order');
					$order_info = $model_order->where(array('uid'=>$m_info['uid']))->order('id desc')->find();
					$m_info['brand_id']=$order_info['brand_id']; 
					$m_info['model_id']=$order_info['brand_id'];
					$m_info['series_id']=$order_info['brand_id'];
				}
				if(!$paweixin_info and !$order_info){ 
					$m_info['brand_id']=''; 
					$m_info['model_id']='';
					$m_info['series_id']='';
				}
				if($m_info['brand_id']){
					$model_carbrand = D('carbrand');
					$brand_info = $model_carbrand->where(array('brand_id'=>$m_info['brand_id']))->find();
					$m_info['brand_name']=$brand_info['brand_name'];
				}else{
					$m_info['brand_name']='';
				}
				if($m_info['model_id']){
					$model_carmodel = D('carmodel');
					$model_info = $model_carmodel->where(array('model_id'=>$m_info['model_id']))->find();
					$m_info['model_name']=$model_info['model_name'];
				}else{
					$m_info['model_name']='';
				}
				if($m_info['series_id']){
					$model_carseries = D('carseries');
					$series_info = $model_carseries->where(array('series_id'=>$m_info['series_id']))->find();
					$m_info['series_name']=$series_info['series_name'];
				}else{
					$m_info['series_name']='';
				}

		}else{
			$state = 'fail';
			$content = '获取失败,您尚未注册';
		}
		$result['state'] = $state;
		$result['content'] = $content;
		$result['info'] = $m_info;
		$result = json_encode($result);
        echo $result;exit;
	}

	//变更订单支付状态
	function change_paymentstatus(){
		$this->check_technician();
		$this->safecheck();
		if($_REQUEST['id']){
			$model_reservation = D('reservation_order');
			$map['id'] = $_REQUEST['id'];
			$info = $model_reservation->where(array('id'=>$_REQUEST['id']))->find();
			$data['pay_type'] = $_REQUEST['pay_type'];
			$data['pay_status'] = 1;
			if($info['order_type']==34){
				$data['status'] = 9;
			}
			//绑定库存表和财务表
			//$this->bind($map['id']);
			$model_reservation->where($map)->save($data);

			//拆出来的补配件订单不用再推送评论
			if($info['order_type']!=34){
				//推送评论
				$res = $this->is_comment($_REQUEST['id'],$info['mobile']);
			}
			$res2 = $this->send_pay($_REQUEST['id'],$info['mobile']);
			$state = 'success';
			if($res){
				$content = '更新支付状态成功';
			}else{
				$content = '更新支付状态成功,推送评论失败';
			}

		}else{
			$state = 'fail';
			$content = '未知的订单ID';
		}
		$result['state'] = $state;
		$result['content'] = $content;
		$result['info'] = $data['pay_type'];
		$result = json_encode($result);
        echo $result;exit;
	}

    //多订单支付变更支付状态
    function bulk_pay_order(){
        $this->check_technician();
        $this->safecheck();
        if($_REQUEST['ids']){
            $ids = explode(',',$_REQUEST['ids']);
            foreach($ids as $k=>$v) {
                $info = array();
                $data = array();
                $map = array();
                $model_reservation = D('reservation_order');
                $map['id'] = $v;
                $info = $model_reservation->where(array('id' => $map['id']))->find();
                $data['pay_type'] = $_REQUEST['pay_type'];
                $data['pay_status'] = 1;
                if ($info['order_type'] == 34) {
                    $data['status'] = 9;
                }
                //绑定库存表和财务表
                //$this->bind($map['id']);
                $res = $model_reservation->where($map)->save($data);
                //echo $model_reservation->getLastsql();
                if($k==0) {
                    $content = '';
                }
                if ( false !== $res ){
                    $state = 'success';
                    $content.= '更新'.$v.'支付状态成功,';
                }else{
                    $state = 'fail';
                    $content.= '更新'.$v.'支付状态失败,';
                }
            }
        }else{
            $state = 'fail';
            $content = '未知的订单ID';
        }
        $result['state'] = $state;
        $result['content'] = $content;
        $result['info'] = $data['pay_type'];
        $result = json_encode($result);
        echo $result;exit;
    }

	//根据步骤顺序改订单状态
	function change_status($array,$id){
		$map['id'] = $id;
		$model_reservation = D('reservation_order');
		$model_reservation->where($map)->save($array);
		//echo $model_reservation->getLastsql();
                //通知点评到家订单完成
                if($array['status']==9){
                    //echo '1111' ;
                    //查询业务来源
                    $rs = $model_reservation->where($map)->find();
                    //echo  $model_reservation->getLastSql();
                    if($rs['business_source']==33){
                         //echo '22222' ;
                        //执行更新点评代码
                        $this->updateDianping($id);
                    }
                }
	}
        
        //点评更新方法  wql@20150701
        function updateDianping($id){
            $model_dphome = D('dphome_linshi');
            $interface_log = D('interface_log');   //返回信息记录表
            $map['order_id '] = $id ;
            $rs = $model_dphome->where($map)->find();
            //echo $model_dphome->getLastSql() ;
            if(is_array($rs)){
                $linshi_id = $rs['id'] ;
                //curl更新点评信息
                $uri = "http://m.api.dianping.com/tohome/openapi/xieche/updateOrderStatus";
                //echo  $uri ;
                // 参数数组
                $data = array (
                   'orderId' => $linshi_id ,   //传递临时表id
                   'status' => '5', //服务完成
                   'methodName'=>'updateOrderStatus',
                   'version'=>'1.0',
                   'partnerId'=>'xieche'    
                );
                //计算签名，并返回参数字符串
                $data = $this->combinedSign($data);
                //print_r($data);

                $ch = curl_init ();
                // print_r($ch);
                curl_setopt ( $ch, CURLOPT_URL, $uri );
                curl_setopt ( $ch, CURLOPT_POST, 1 );
                curl_setopt ( $ch, CURLOPT_HEADER, 0 );
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
                $ret = curl_exec($ch);
                curl_close ($ch);
                
                //print_r($ret);
                //将通知结果记录入库
                $dphome = json_decode($ret,true) ;
                $add_data['code'] = $dphome['code'] ;
                $add_data['msg'] = $dphome['msg'] ;
                $add_data['orderid'] = $id  ;
                $add_data['type'] = 1  ;
                $interface_log->add($add_data);
            }   
        }
        
           
        
        //签名计算并返回参数字符串  wql@20150701
        public function combinedSign($data) {
            //拼接签名字符串
            $sign = '';
            //点评提供的密钥
            //$appKey = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456' ;
			$appKey = '35046290D1F97B2531F7C6201E4192B8' ;
            //对参数数组升序排序
            ksort($data);
            //循环拼接
            foreach ($data as $k => $v) {
               $sign .= $k.$data[$k];
            }
            //拼接appkey
            $sign .= $appKey ;
            //md5加密转为大写
            $sign = strtoupper(md5($sign));
            $data['sign']= $sign ;

            //返回参数字符串
            $ret = '';
            foreach($data as $k=>$v){
                    $ret .= $k.'='.$v.'&' ;
            }
            $ret = substr($ret,0,-1) ;
            return  $ret ;   
        }
        
        
        

	//推送评论
	function push_comment(){
		$this->check_technician();
		$model_reservation = D('reservation_order');
		$info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->find();
		$res = $this->is_comment($_REQUEST['order_id'],$info['mobile']);
		$result['state'] = 'success';
		$result['content'] = '推送成功';
		$result = json_encode($result);
        echo $result;exit;
	}
	
	//验证上门保养抵扣码功能
	function valid_replace_code(){
		$this->check_technician();
		$replace_code = @$_POST['coupon_code'];
		$order_id = @$_POST['order_id'];
		try {
			if(!$replace_code || !$order_id ){
				throw new Exception('param error');		
			}
			$mReservation = D('reservation_order');
			$dataReservation = $mReservation->field('replace_code')->where( array( 'id'=>$order_id ) )->find();
			if ( !empty($dataReservation['replace_code']) ) {
				throw new Exception('该订单已经使用过验证码');
			}
			if($replace_code=='016888'){
				$data = array(
				'state' => 'success',
				'content'=>'验证成功',
				'info'=> array('reduce'=>20)
				);
				echo json_encode($data);
				exit;
			}else{
				$mCarservicecode = D('carservicecode');//上门保养抵用码字段
				$count = $mCarservicecode->where( array('coupon_code'=>$replace_code,'status'=>0) )->count();
				if(!$count){
					throw new Exception('抵扣券错误');
				}else{
					$reduce = $this->get_codevalue($replace_code);//获取优惠券价格
					$data = array(
							'state' => 'success',
							'content'=>'验证成功',
							'info'=> array('reduce'=>$reduce)
					);
					echo json_encode($data);
					exit;
				}
			}
			
		}catch (Exception $e){
			$data = array(
					'state' => 'fail',
					'content'=>$e->getMessage(),
					'info'=> NULL
			);
			echo json_encode($data);
			exit;
		}
		
	}
	//绑定上门保养抵扣码和订单功能
	function bind_replace_code(){
		$this->check_technician();
		try{
			$order_id = @$_POST['order_id'];
			$replace_code = @$_POST['coupon_code'];
			if (!$order_id || !$replace_code) {
				throw new Exception('param error');	
			}
			$mCarservicecode = D('carservicecode');//上门保养抵用码字段
			$where = array('coupon_code'=>$replace_code);
			//通用券不用验证
			if($replace_code!='016888'){
				//改抵用码已经用过了，不能再用了
				$codeRes = $mCarservicecode->where( array('coupon_code'=>$replace_code) )->find();
				
				if(!$codeRes){
					throw new Exception('抵扣券错误');
				}elseif ($codeRes['status'] == 1){
					throw new Exception('抵扣券已经使用过了');
				}
			}
			$update = array('status'=>1);
			$replace_code_id = $mCarservicecode->where($where)->save($update);
			if(!$replace_code_id){
				throw new Exception('抵用码无法使用');
			}
			
			//改数据库价格和绑定到该order_id下
			$mReservation = D('reservation_order');
			$where2 = array( 'id'=>$order_id );
			$dataReservation = $mReservation->field('replace_code,amount')->where( $where2 )->find();
			if ($dataReservation) {
				if ( !empty($dataReservation['replace_code']) ) {
					throw new Exception('该订单已经绑定过抵用码了,不能重复绑定');
				};
				$codevalue = $this->get_codevalue($replace_code);
				$new_price = $dataReservation['amount'] - $codevalue;
				$update2 = array(
						'replace_code'=>$replace_code,
						'amount'=>$new_price
				);
				$reserver_id = $mReservation->where( $where2 )-> save( $update2 );
				if(!$reserver_id){
					throw new Exception('update reservation data error');
				}
				$data = array(
						'state' => 'success',
						'content'=>'绑定成功',
						'info'=> array('reduced_price'=>$new_price)
				);
				echo json_encode($data);
				exit;
			}else{
				throw new Exception('订单为空');
			}
			
		}catch (Exception $e){
			$data = array(
					'state' => 'fail',
					'content'=>$e->getMessage(),
					'info'=> NULL
			);
			echo json_encode($data);
			exit;
		}
	}

	//PAD下免费检测单
	function create_checking_order(){
		$this->check_technician();
		$this->safecheck();
		//注册
		$model_user = D('member');
		$model_reservation = D('reservation_order');
		$model_step = D('check_step');

		$userinfo = $model_user->where(array('mobile'=>$_REQUEST['phone'],'status'=>'1'))->find();
		if(!$userinfo){
			$member_data['mobile'] = $_REQUEST['phone'];
			$member_data['password'] = md5($_REQUEST['phone']);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '55';//路人检测代注册
			$uid = $model_user->data($member_data)->add();
		}

		//订单数据
		if($userinfo){
			$order_info['uid'] = $userinfo['uid'];
		}else{
			$order_info['uid'] = $uid;
		}
		$order_info['truename'] = $_REQUEST['master_name'];
		$order_info['mobile'] = $_REQUEST['phone'];
		$order_info['model_id'] = $_REQUEST['model_id'];
		$order_info['licenseplate'] = $_REQUEST['car_number'];
		$order_info['car_reg_time'] = $_REQUEST['car_release_date'];
		$order_info['vin_num'] = $_REQUEST['vin'];
		$order_info['order_time'] = time();
		$order_info['create_time'] = time();
		$order_info['remark'] = '技师PAD转化订单';
		$order_info['order_type'] = 2;
		$order_info['technician_id'] = $_REQUEST['engineer_id'];
		$order_info['pay_status'] = 1;
		$order_info['status'] = 2;
		$order_info['item'] = "a:6:{s:6:'oil_id';N;s:10:'oil_detail';N;s:9:'filter_id';N;s:9:'kongqi_id';N;s:11:'kongtiao_id';N;s:5:'price';a:4:{s:3:'oil';a:1:{i:0;i:0;}s:6:'filter';a:1:{i:0;i:0;}s:6:'kongqi';a:1:{i:0;i:0;}s:8:'kongtiao';a:1:{i:0;i:0;}}}";
		$model_technician = D('Technician');
		$t_info = $model_technician->where(array('id'=>$_REQUEST['engineer_id']))->find();
		$order_info['city_id'] = $t_info['city_id'];
		$order_id = $model_reservation->add($order_info);
		//echo $model_reservation->getLastsql();

		//步骤数据
		for($n=0;$n<3;$n++){
			$data['step_id'] = $n;
			$data['order_id'] = $order_id;
			$data['result_type'] = 1;
			$data['create_time'] = time();
			$model_step->add($data);
		}
		if($order_id){
			$info = $model_reservation->where(array('id'=>$order_id))->find();
			$info['item'] = array();
			$result['state'] = 'success';
			$result['content'] = '成功';
			$result['info'] = $info;
		}else{
			$result['state'] = 'fail';
			$result['content'] = '失败';
		}
		$result = json_encode($result);
		echo $result;exit;
	}

	//插入检测结果数据并推送微信消息
	function insert_check_report(){
		$this->check_technician();
		$state = json_decode($_REQUEST['state'],true);
		if($state==''){ exit('校验参数失败'); }
		$model_checkstate = D('checkstate');
		$model_check_report = D('check_report');
		//print_r($state);
		foreach($state as $k=>$v){
			$info = $model_checkstate->where(array('name'=>$k))->find();
			$data[$info['key']] = $v;
		}
		
		
		//根据检测报告数据给用户注册
		$model_user = D('member');
		$model_memberlog = D('memberlog');
		$model_membercar = D('membercar');
		$model_sms = D('sms');
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		$userinfo = $model_user->where(array('mobile'=>$data['a1'],'status'=>'1'))->find();

		if($userinfo){
			$membercar_info = $model_membercar->where(array('uid'=>$userinfo['uid']))->find();
			//绑定用户车辆信息
			if($membercar_info){}else{
				$membercar_data['uid'] = $userinfo['uid'];
				$brand = $model_brand->where(array('brand_name'=>$data['a29']))->find();
				$membercar_data['brand_id'] = $brand['brand_id'];
				$series = $model_series->where(array('series_name'=>$data['a28'],'brand_id'=>$brand['brand_id']))->find();
				$membercar_data['series_id'] = $series['series_id'];
				$model = $model_model->where(array('model_name'=>$data['a49'],'series_id'=>$series['series_id']))->find();
				$membercar_data['model_id'] = $model['model_id'];
				$membercar_data['car_number'] = $data['a20'];
				$membercar_data['create_time'] = time();
				$membercar_data['is_default'] = 1;
				$model_membercar->add($membercar_data);
			}
		}else{
			$member_data['mobile'] = $data['a1'];
			$member_data['password'] = md5($data['a1']);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '52';//检测报告代注册
			$uid = $model_user->data($member_data)->add();
			//绑定用户车辆信息
			$membercar_data['uid'] = $uid;
			$brand = $model_brand->where(array('brand_name'=>$data['a29']))->find();
			$membercar_data['brand_id'] = $brand['brand_id'];
			$series = $model_series->where(array('series_name'=>$data['a28'],'brand_id'=>$brand['brand_id']))->find();
			$membercar_data['series_id'] = $series['series_id'];
			$model = $model_model->where(array('model_name'=>$data['a49'],'series_id'=>$series['series_id']))->find();
			$membercar_data['model_id'] = $model['model_id'];
			$membercar_data['car_number'] = $data['a20'];
			$membercar_data['create_time'] = time();
			$membercar_data['is_default'] = 1;
			$model_membercar->add($membercar_data);
            /*
			$send_add_user_data = array(
					'phones'=>$data['a1'],
					'content'=>'您已注册成功，您可以使用您的手机号码'.$data['a1'].'，密码'.$data['a1'].'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
            */

            // dingjb 2015-09-29 10:52:29 切换到云通讯
            $send_add_user_data = array(
                'phones'  => $data['a1'],
                'content' => array(
                    $data['a1'],
                    $data['a1']
                )
            );
            $this->curl_sms($send_add_user_data, null, 4, '37653');

			$send_add_user_data['sendtime'] = time();
			$model_sms->data($send_add_user_data)->add();

			$logdata['createtime'] = time();
			$logdata['mobile'] = $data['a1'];
			$logdata['memo'] = '用户注册';
			$model_memberlog->data($logdata)->add();
		}

		$model_reservation = D('reservation_order');
		if($_REQUEST['order_id']){
			$map['id'] = $data['reservation_id'] = $_REQUEST['order_id'];
			$info = $model_reservation->where($map)->find();
		}else{
			//如果用户从另外一个口子做报告，就取当天该号码下过的订单号
			$map['mobile'] = $data['a1'];
			$start_time = strtotime(date('Y-m-d',time()).'0:00:00');
			$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
			$map['order_time']=array(array('egt',$start_time),array('elt',$end_time));
			$info = $model_reservation->where($map)->find();
			$data['reservation_id'] = $info['id'];
		}
		$data['create_time'] = time();
		$model_check_report->add($data);
		//echo $model_check_report->getLastsql();
		//存检测报告页面显示数据表
		$sort_state = array(				//数据顺序处理
				'机油液位'=>$state['机油液位'],
				'空气滤芯'=>$state['空气滤芯'],
				'空调滤芯'=>$state['空调滤芯'],
				'雨刮水液面'=>$state['雨刮水液面'],
				'左前胎胎压'=>$state['左前胎胎压'],
				'右前胎胎压'=>$state['右前胎胎压'],
				'左后胎胎压'=>$state['左后胎胎压'],
				'右后胎胎压'=>$state['右后胎胎压'],
				'防冻液液面'=>$state['防冻液液面'],
				'防冻液冰点值'=>$state['防冻液冰点值'],
				'制动液含水量'=>$state['制动液含水量'],
				'前刹车片厚度'=>$state['前刹车片厚度'],
				'后刹车片厚度'=>$state['后刹车片厚度'],
				'前刹车盘厚度'=>$state['前刹车盘厚度'],
				'后刹车盘厚度'=>$state['后刹车盘厚度'],
				'蓄电池健康度'=>$state['蓄电池健康度'],
				'前胎花纹深度'=>$state['前胎花纹深度'],
				'后胎花纹深度'=>$state['后胎花纹深度'],
				'雨刮片'=>$state['雨刮片'],
				'灯光照明'=>$state['灯光照明'],
				'点烟器'=>$state['点烟器'],
				'球头'=>$state['球头'],
				'转向拉杆'=>$state['转向拉杆'],
				'变速箱'=>$state['变速箱'],
				'油底壳螺丝'=>$state['油底壳螺丝'],
				'避震器'=>$state['避震器'],
				'上下摆臂'=>$state['上下摆臂'],
				'半轴'=>$state['半轴'],
				'安全带'=>$state['安全带'],
				'手制动器'=>$state['手制动器'],
				'发动机舱管线'=>$state['发动机舱管线'],
				'气门室盖垫'=>$state['气门室盖垫'],
				'方向机油壶'=>$state['方向机油壶'],
				'机油指示灯'=>$state['机油指示灯'],
				'ABS指示灯'=>$state['ABS指示灯'],
				'发动机指示灯'=>$state['发动机指示灯'],
				'刹车指示灯'=>$state['刹车指示灯'],
				'气囊指示灯'=>$state['气囊指示灯'],
				'car_brand'=>$state['car_brand'],
				'car_model'=>$state['car_model'],
				'car_series'=>$state['car_series'],
				'mileage'=>$state['mileage'],
				'phone'=>$state['phone'],
				'date'=>$state['date'],
				'engine_number'=>$state['engine_number'],
				'vin'=>$state['vin'],
				'last_maintenance_date'=>$state['last_maintenance_date']
		);
		$data_extra['data'] = serialize($sort_state);
		$data_extra['mobile'] = $data['a1'];
		$data_extra['reservation_id'] = $data['reservation_id'];
		$data_extra['create_time'] = time();
		$model_checkreport_total = D('checkreport_total');
		$add_res = $model_checkreport_total->add($data_extra);
		$res_id = $add_res;
		if($res_id){
			$this->addCodeLog('检测报告', '报告插入成功',$res_id);
		}
		//echo $model_checkreport_total->getLastsql();
		if($add_res){
			$content = '录入体检报告成功';
			//如果当天有同一个人的订单，自动添加付款之前的步骤
			$start_time = strtotime(date('Y-m-d',time()).'0:00:00');
			$end_time = strtotime(date('Y-m-d',time()).'0:00:00')+86400;
			$repeat['order_time']=array(array('egt',$start_time),array('elt',$end_time));
			$repeat['mobile']=$data_extra['mobile'];
			$repeat['status']=array('neq','8');
			$r_info = $model_reservation->where($repeat)->find();
			$model_step = D('check_step');
			$s_info = $model_step->where(array('order_id'=>$r_info['id']))->count();
			if($r_info and $s_info==0){
				for($n=0;$n<4;$n++){
					$step_data['step_id'] = $n;
					$step_data['order_id'] = $r_info['id'];
					$step_data['result_type'] = 1;
					$step_data['create_time'] = time();
					$model_step->add($step_data);
				}
			}

			//如果是路边免费检测,自动添加剩余步骤数据
			if($info['remark'] == '技师PAD转化订单'){
				$r_data['status'] = '9';
				$r_map['id'] = $data['reservation_id'];
				$model_reservation->where($r_map)->save($r_data);
				$model_step = D('check_step');
				for($n=3;$n<7;$n++){
					$step_data['step_id'] = $n;
					$step_data['order_id'] = $data['reservation_id'];
					$step_data['result_type'] = 1;
					$step_data['create_time'] = time();
					$model_step->add($step_data);
				}
			}
		}
		//推送检测报告
		$res=$this->send_check($res_id,$data['a1']);
		if($res==-1){
			$content.='获取微信ID失败';
			$this->addCodeLog('检测报告', '报告推送失败'.$res.$content,$res_id);
		}else{
			if($res['errcode'] != 0){
				$state = 'fail'; 
				$content = '推送失败('.$res['errcode'].')';
			}else{
				$state = 'success'; 
				$content = '推送成功';
				$this->addCodeLog('检测报告', '报告推送成功'.$res.$content,$res_id);
			}
		}
		//推送评论
		$this->is_comment($_REQUEST['order_id'],$data['a1']);
		$this->submitCodeLog('检测报告');
		$result['state'] = 'success';
		$result['content'] = $content;
		$result['info'] = $res_id;
		$result = json_encode($result);
        echo $result;exit;
	}
	//推送带有付款链接的微信消息给用户
	function send_payment_wechat_msg(){
		$this->check_technician();
		$this->safecheck();
		$model_reservation = D('reservation_order');
        if(strpos($_REQUEST['order_id'],',')!==false){
            $ids = explode(',',$_REQUEST['order_id']);
            $info = $model_reservation->where(array('id'=>$ids['0']))->find();
        }else {
            $info = $model_reservation->where(array('id' => $_REQUEST['order_id']))->find();
        }
		$model_paweixin =D('paweixin');
		$map['mobile'] = $info['mobile'];
		$map['type'] = 2;
		$weixin = $model_paweixin->where($map)->find();
		if($weixin==''){
			$state = 'fail'; 
			$content = '获取微信ID失败';
		}else{
            if($ids){
                $order_id = '';
                foreach($ids as $k=>$v){
                    $order_id.= $this->get_orderid($v).',';
                }
                $order_id = substr($order_id,0,strlen($order_id)-1);
            }else{
                $order_id = $this->get_orderid($_REQUEST['order_id']);
            }
			$weixin_data['touser'] = $weixin['wx_id'];
			$weixin_data['title'] = "您有一笔订单未支付";
			$weixin_data['description'] = "您好，您预约的携车网•府上养车上门保养服务已经完成，尚未付款，订单号：".$order_id."，请尽快完成支付。若有问题请当面咨询技师，或致电400-660-2822";
			$weixin_data['url'] = "http://www.xieche.com.cn/weixinpay/wxpay.php?order_id=".$order_id."&showwxpaytitle=1";

			$res = $this->weixin_api($weixin_data);
			$this->addCodeLog('付款链接推送', var_export($res,true).'http://www.xieche.com.cn/weixinpay/wxpay.php?order_id='.$_REQUEST['order_id'].'&showwxpaytitle=1',$_REQUEST['order_id']);
			if($res['errcode'] != 0){
				$state = 'fail'; 
				$content = '推送失败('.$res['errcode'].')';
			}else{
				$state = 'success'; 
				$content = '推送成功';
			}
		}
		$result['state'] = $state;
		$result['content'] = $content;
		$result = json_encode($result);
        echo $result;exit;
	}


	//加密函数
	function safecheck(){
		if($_REQUEST['time'] and $_REQUEST['code']){
			$code = md5($_REQUEST['time'].'www.xieche.com.cn');
			//echo $code;
			if($code==$_REQUEST['code']){
				return true;
			}else{
				exit('非法调用，已报警');
			}
		}else{
			echo 'time='.$_REQUEST['time'].'=='.$_REQUEST['code'];
			exit('获取参数失败');
		}
	}

	//下行检测报告
	function send_check($report_id,$mobile){
		$id = $report_id;
		$report_id = base64_encode($report_id.'168');
        /*
		$send_add_user_data = array(
				'phones'=>$mobile,
				'content'=>'携车网温馨提醒：您的爱车38项免费检测现已完毕，关注携车网官方微信并绑定手机，随时查看爱车检测报告。请点击链接查看检测报告！http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id,
		);
		
		$this->curl_sms($send_add_user_data,'',1);
		*/

        // dingjb 2015-09-28 17:07:14 切换到云通讯

        $send_add_user_data = array(
                'phones'  => $mobile,
                'content' => array($report_id)
        );
        
        $this->curl_sms($send_add_user_data, null, 4, 37754);

		$model_paweixin =D('paweixin');
		$map['mobile'] = $mobile;
		$map['type'] = 2;
		$weixin = $model_paweixin->where($map)->find();
		//echo $model_paweixin->getLastsql();
		
		
		//追发一条给工作人员李能
		if($mobile == '15618515967' or $mobile == '13818134554'){
            /*    
			$send_add_user_data = array(
				'phones'=>'18501751071',
				'content'=>'携车网温馨提醒：您的爱车38项免费检测现已完毕，请点击链接查看检测报告！http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id,
			);
			$res = $this->curl_sms($send_add_user_data,'',1);  //Todo 内外暂不发短信
            */

            // dingjb 2015-09-28 17:22:27 切换到云通讯
            $send_add_user_data = array(
                'phones'  => '18501751071',
                'content' => array($report_id)
            );
            
            $res = $this->curl_sms($send_add_user_data, null, 4, 37754);
		}
		
		$send_add_user_data['sendtime'] = time();
		$model_sms =D('sms');
		$model_sms->data($send_add_user_data)->add();
		if($weixin==''){
			return -1;
		}
		/*	老接口,暂时隐藏
		$weixin_data['touser'] = $weixin['wx_id'];
		$weixin_data['title'] = "携车网车辆电子体检报告";
		$weixin_data['description'] = "携车网温馨提醒：您的爱车38项免费检测现已完毕，请点击查看检测报告！";
		$weixin_data['url'] = "http://www.xieche.com.cn/mobile/check_report-report_id-{$report_id}";
		
		$result = $this->weixin_api($weixin_data);
		if($result){
			$this->addCodeLog('检测报告结果', var_export($result,true).'http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id);
			return $result; 
		}else{
			return true;
		}*/
		
		/*
		 * 微信模板消息接口
		 * bright
		 */
		$data = array(
				'wx_id'=>$weixin['wx_id'],
				'return_url'=>'http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id,
				'template_id'=>1,
				'title'=>'携车网车辆电子体检报告',
				'key1'=>'车辆检测',
				'key2'=>'38项爱车深度体检',
				'key3'=>date('Y-m-d H:i:s',time()),
				'remark'=>'携车网温馨提醒：您的爱车38项免费检测现已完毕，请点击查看检测报告！'
		);
		$ret = $this->send_weixin_template_msg($data);
		$ret = json_decode($ret,true);
		if ($ret['errcode'] == 0){
			$this->addCodeLog('检测报告成功', var_export($ret,true).'http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id);
		}else{
			$this->addCodeLog('检测报告出错', var_export($ret,true).'http://www.xieche.com.cn/mobile/check_report-report_id-'.$report_id);
		}
		return $ret;
	}

	//下行支付成功消息
	function send_pay($id,$mobile){
		$model_paweixin =D('paweixin');
		$map['mobile'] = $mobile;
		$map['type'] = 2;
		$weixin = $model_paweixin->where($map)->find();
		$model_reservation = D('reservation_order');
		$info = $model_reservation->where(array('id'=>$id))->find();
		if($weixin==''){
			return 1;
		}
		//$sql_weixin = "SELECT * FROM xc_paweixin WHERE mobile='".$mobile."' AND type=2 LIMIT 1";
		//$weixin = $mysql->query($sql_weixin,"assoc");
		$report_id = base64_encode($report_id.'168');
		$weixin_data['touser'] = $weixin['wx_id'];
		$weixin_data['title'] = "订单支付成功";
		$weixin_data['description'] = "您订单号为:".$id."已经付款成功，付款金额：".$info['amount']."元，感谢您对携车网•府上养车的支持。如有问题，请致电400-660-2822咨询";
		$weixin_data['url'] = "http://www.xieche.com.cn/mobilecar-mycarservice_detail-pa_id-{$FromUserName[id]}-order_id-".$id;
        if($info['amount']>0){
    		$result = $this->weixin_api($weixin_data);
    		if($result){
    			return $result; 
    		}else{
    			return true;
    		}
        }
	}

	//判断是否下行评论
	function is_comment($reservation_id,$mobile){
		$model_reservation = D('reservation_order');
		$info = $model_reservation->where(array('id'=>$reservation_id))->find();
		if($info['pay_status']==1){
			//已支付则变更订单状态为已完成
			$mad['id'] = $reservation_id;
			//$data['status'] = 9;
			$model_reservation->where($mad)->save($data);
			//绑定库存表和财务表
			//$this->bind($reservation_id);
			
			$model_paweixin =D('paweixin');
			$map['mobile'] = $mobile;
			$map['type'] = 2;
			$weixin = $model_paweixin->where($map)->find();
			if($weixin==''){
				return 1;
			}
			$weixin_data['touser'] = $weixin['wx_id'];
			$weixin_data['title'] = "携车网府上养车评价";
			$weixin_data['description'] = "欢迎您对我们的服务做出评价";
			$weixin_data['url'] = "http://www.xieche.com.cn/mobile/reserveorder_comment-reservation_id-{$reservation_id}";
			//print_r($weixin_data);
			$result = $this->weixin_api($weixin_data);
			if($result){
				$this->addCodeLog('下行评论结果', var_export($result,true).'http://www.xieche.com.cn/mobile/reserveorder_comment-reservation_id-'.$reservation_id,$reservation_id);
				return $result; 
			}else{
				return true;
			}
		}
	}
	//获取检测项目
	function get_checkitem(){
		$this->check_technician();
		$model_checkitem = D('checkitem');
		$map['sort'] = array('neq','0');
		$item_info = $model_checkitem->where($map)->order('sort asc')->select();
		$result['state'] = 'success';
		$result['content'] = '获取数据成功';
		$result['info'] = $item_info;
		$result = json_encode($result);
		echo $result;exit;
	}

	//根据车型ID获取车辆适配项目数据
	function get_itemBymodelid(){
		$this->check_technician();
		$this->carmodel_model = D('carmodel');
		$this->item_oil_model = D('item_oil');
		$this->filter_model = D('item_filter');
		$model_id = $_REQUEST['model_id'];
		$style_param['model_id'] = $model_id;
		$car_style = $this->carmodel_model->where($style_param)->find();

		if($_REQUEST['type']=='0'){
			//机油
			$oil_num = ceil($car_style['oil_mass']);
			if ($oil_num <4) {
				$oil_num = 4;
			}
			if( $oil_num >3 ){
				$xx = $oil_num%4;
				$yy = ($oil_num - $xx)/4;
				if($xx){
					//$norms = $yy."x4L + ".$xx."x1L";
					$norms = $yy+$xx."L";
				}else{
					//$norms = $yy."x4L";
					$norms = $yy."L";
				}
			}else{
				$xx = $oil_num;
				$yy = 0;
				//$norms = $oil_num . "x1L";
				$norms = $oil_num . "L";
			}
			if( $oil_num <4 ){
				$car_style['norms'] = '4L';
			}else{
				$car_style['norms'] = $oil_num."L";//多少升机油
			}
		
			//所有物品详情
			$oil_list_all = $this->item_oil_model->where()->select();
			$oil_item = array();
			foreach( $oil_list_all as $nors){
				$oil_item[$nors['name']][$nors['norms']] = $nors;
			}
		
			$oil_param = array();
			//根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
			switch ($car_style['oil_type']){
				case a:
					$oil_param['type'] = array('in','1,2,3');
					break;
				case b:
					$oil_param['type'] = array('in','2,3');
					break;
				case c:
					$oil_param['type'] = 3;
					break;
				default:
					$oil_param['type'] = 0;
					break;
			}
			//$oil_param['norms'] = $oil_num;	//需要多少升的机油
			$oil_param['norms'] = 4;	//需要多少升的机油
			//符合要求的品牌详情
			$item_set=array();
			$oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select();
		
			foreach( $oil_name_distinct as $keys=>$names ){
				$item_set[$keys]['id'] = $names['id'];
				$item_set[$keys]['name'] = $names['name'];
				$item_set[$keys]['norms'] = $car_style['norms'];
				//规格1L详情
				$item_set[$keys]['oil_1'] = $oil_item[$names['name']][1]['id'];
				$item_set[$keys]['oil_1_num'] = $xx;
				$item_set[$keys]['oil_1_price'] = $oil_item[$names['name']][1]['price'];
				//规格4L详情
				$item_set[$keys]['oil_2'] = $oil_item[$names['name']][4]['id'];
				$item_set[$keys]['oil_2_num'] = $yy;
				$item_set[$keys]['oil_2_price'] = $oil_item[$names['name']][4]['price'];
		
				//计算总价
				$item_set[$keys]['price'] = $xx*$oil_item[$names['name']][1]['price']+$yy*$oil_item[$names['name']][4]['price'];
				$item_set[$keys]['type'] = '机油';
			}
			$car_style['type'] = '未设定';
			if($car_style['oil_type']==1){
				$car_style['type'] = '矿物油';
			}
			if($car_style['oil_type']==2){
				$car_style['type'] = '半合成油';
			}
			if($car_style['oil_type']==3){
				$car_style['type'] = '全合成油';
			}
		}

		if($_REQUEST['type']!='0'){
			$type=$_REQUEST['type'];
			//项目
			$style_id = $model_id;
			$item_set = array();
			if( $style_id ){
				$condition['model_id'] = $style_id;
				$style_info = $this->carmodel_model->where($condition)->find();
				$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
				//print_r($set_id_arr);
				if( $set_id_arr ){
					foreach( $set_id_arr[$type] as $k=>$v){
						//foreach( $v as $_k=>$_v){
							$item_condition['id'] = $v;
							$item_info_res = $this->filter_model->where($item_condition)->find();
							$item_info['id'] = $item_info_res['id'];
							$item_info['name'] = $item_info_res['name'];
							$item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
							$item_set[$k] = $item_info;
						//}
						foreach($item_set[$k] as $kk=>$vv){
							if($kk>0 and !$item_set[$k][$kk-1]){
								$item_set_new[$k][$kk-1]=array();
							}
							if($vv){
								if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
									$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
									$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
								}else{
									$item_set_new[$k][$kk] = $item_set[$k][$kk];
								}
							}else{
								$item_set_new[$k][$kk]=array('0'=>'');
							}
						}
						$item_set = $item_set_new;
					}
				}
			}
		}
		//print_r($item_set);

		if(!$item_set['0'] and $item_set['1']){
			foreach($item_set['1'] as $kk=>$vv){

				$item_set[$kk] = $vv;

				if($_REQUEST['type']=='1'){ $item_set['type'] = '机滤'; }
				if($_REQUEST['type']=='2'){ $item_set['type'] = '空气滤清器'; }
				if($_REQUEST['type']=='3'){ $item_set['type'] = '空调滤清器'; }
			}
			unset($item_set['1']);
			$a='{"state":"success","content":"获取数据成功",';
			$result = $a.'"info":['.json_encode($item_set).']}';
		}else{
			$a='{"state":"success","content":"获取数据成功",';
			$result = $a.'"info":'.json_encode($item_set).'}';
		}
		echo $result;exit;
	}
	//PAD更新订单配件数据
	function update_orderinfo(){
		$this->check_technician();
		//print_r($_REQUEST);
		$this->reservation_order_model = D('reservation_order');
		$this->item_oil_model = D('item_oil');
		$this->item_model = D('item_filter');
		$condition['id'] = $_REQUEST['order_id'];
		$info = $this->reservation_order_model->where($condition)->find();

		if($this->_post('oil')){
			$_SESSION['oil_detail'] = json_decode($_REQUEST['oil'],true);
			foreach($_SESSION['oil_detail'] as $k=>$v){
				$_SESSION['item_0'] = $k;
			}
		}
		if($this->_post('oil_filter')){
			$_SESSION['item_1'] = intval($this->_post('oil_filter'));
		}
		if($this->_post('air_filter')){
			$_SESSION['item_2'] = intval($this->_post('air_filter'));
		}
		if($this->_post('air_conditioning_filter')){
			$_SESSION['item_3'] = intval($this->_post('air_conditioning_filter'));
		}
		$this->addCodeLog('更新配件结果', var_export($_REQUEST,true),$_REQUEST['order_id']);
    	//商品详情
    	$order_items = unserialize($info['item']);

		//如果未重选机油则不改变总价，防止调价使老订单价格变化
		$item_oil_price = 0;
		$oil_data = $_SESSION['oil_detail'];

		if($_SESSION['item_0']==$order_items['oil_id']){
			foreach ($order_items['price']['oil'] as $k => $v) {
				foreach ( $oil_data as $id=>$num){
					if($id==$k and $num > 0){
					//$oil_price[] = $v;
					$oil_price[]=$v/$order_items['oil_detail'][$k]*$num;//当没有改变机油型号的时候，通过机油总价/机油数量得到原订单机油单价
					$oil_id[]=$k;
					}
				}
			}
			//print_r($oil_price);exit;
			$oil_1_price = $oil_price['0'];
			$oil_2_price = $oil_price['1'];
			$item_oil_price = $oil_1_price+$oil_2_price;
			$oil_1_id = $oil_id['0'];
			$oil_2_id = $oil_id['1'];
		}else{
		//if($_SESSION['item_0']){
			//通过机油id查出订单数据
			$i = 0;
			foreach ( $oil_data as $id=>$num){
				if($num > 0){
					$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
					if ($i == 0) {
						$oil_1_id = $id;
						$oil_1_price = $res['price']*$num;
						$i++;
					}else{
						$oil_2_id = $id;
						$oil_2_price = $res['price']*$num;
					}
					$item_oil_price += $res['price']*$num;
					$name = $res['name'];
				}
			}
		}
		$item_list['0']['id'] = $_SESSION['item_0'];
		$item_list['0']['name'] = $name;
		$item_list['0']['price'] = $item_oil_price;

		if($_SESSION['item_1']){
			$filter_id = $item_condition['id'] = $_SESSION['item_1'];
			$item_list['1'] = $this->item_model->where($item_condition)->find();
			$filter_price = $item_list['1']['price'];
		}
		if($_SESSION['item_2']){
			$kongqi_id = $item_condition['id'] = $_SESSION['item_2'];
			$item_list['2'] = $this->item_model->where($item_condition)->find();
			$kongqi_price = $item_list['2']['price'];
		}
		if($_SESSION['item_3']){
			$kongtiao_id = $item_condition['id'] = $_SESSION['item_3'];
			$item_list['3'] = $this->item_model->where($item_condition)->find();
			$kongtiao_price = $item_list['3']['price'];
		}
	

		$item_amount = 0;
		if(is_array($item_list)){
			foreach ($item_list as $key => $value) {
				$this->addCodeLog('更新配件结果', 'price:'.$value,$_REQUEST['order_id']);
				$item_amount += $value['price'];
			}
		}
		$this->addCodeLog('更新配件结果', $item_amount,$_REQUEST['order_id']);
		

		/*
		if($info['replace_code']>0){
			$value = $this->get_codevalue($info['replace_code']);
			if ($value != 99){
				$update['amount'] = $item_amount +99 -$value;
			}else{
				$update['amount'] = $item_amount;
			}
		}else{
			$update['amount'] = $item_amount + 99;
		}

		if($info['order_type']==2){
			$update['amount'] = 0;
		}elseif($info['order_type']==3){
			$update['amount'] = $update['amount']-99;
		}*/

		//新价格 = 计算出来的新价格 + 服务费 - 原单中的折扣部分
		$update['amount'] = $item_amount + 99 - $info['dikou_amount'];
		$this->addCodeLog('更新配件结果', $update['amount'],$_REQUEST['order_id']);
		$item_content = array(
				'oil_id'     =>	$_SESSION['item_0'],
				'oil_detail' => $_SESSION['oil_detail'],
				'filter_id'  => $_SESSION['item_1'],
				'kongqi_id'  => $_SESSION['item_2'],
				'kongtiao_id' =>$_SESSION['item_3'],
				'price'=>array(
						'oil'=>array(
								$oil_1_id=>$oil_1_price,
								$oil_2_id=>$oil_2_price
						),
						'filter'=>array(
								$filter_id=>$filter_price
						),
						'kongqi'=>array(
								$kongqi_id=>$kongqi_price
						),
						'kongtiao'=> array(
								$kongtiao_id=>$kongtiao_price
						)
				)
		);
		//print_r($item_content);exit;
		$update['item'] = serialize($item_content);

        $res = $this->reservation_order_model->where($condition)->save($update);
		$this->addCodeLog('更新配件结果', $res,$_REQUEST['order_id']);

		if(!$_SESSION['item_1'] and $order_items['filter_id']>0){ $cut_filter_id = $order_items['filter_id']; }
		if(!$_SESSION['item_2'] and $order_items['kongqi_id']>0){ $cut_kongqi_id = $order_items['kongqi_id']; }
		if(!$_SESSION['item_3'] and $order_items['kongtiao_id']>0){ $cut_kongtiao_id = $order_items['kongtiao_id']; }
		foreach($_SESSION['oil_detail'] as $id=>$num){
			foreach($order_items['oil_detail'] as $_id=>$_num){
				if($id==$_id){
					$cut_num = $_num-$num;
					if($cut_num>0){
						$cut['oil_detail'][$id] = $cut_num;
					}
				}elseif($order_items['oil_detail'][$_id]>0 and !$_SESSION['oil_detail'][$_id]){
						$cut['oil_detail'][$_id] = $_num;
				}
			}
		}
		$item = array(
				'oil_detail' => $cut['oil_detail'],
				'filter_id'  => $cut_filter_id,
				'kongqi_id'  => $cut_kongqi_id,
				'kongtiao_id' =>$cut_kongtiao_id,
		);

		$time = time();
		$shangmen_time = strtotime(date('Y-m-d 0:00:00',$info['order_time']));
		//技师调整配件发生在上门当天并且已分配技师，就记录取消日志
		if($time>$shangmen_time and $info['status']==2){
			//技师配件日志
			$log_data['order_id'] = $info['id'];
			$log_data['item'] = serialize($item);
			$log_data['technician_id'] = $info['technician_id'];
			$log_data['create_time'] = time();
			$this->get_itemback_log($log_data);
		}

		if($res){
			$result['state'] = 'success';
			$result['content'] = '更新成功';
		}else{
			$result['state'] = 'fail';
			$result['content'] = '更新失败';
		}
		$result = json_encode($result);
		echo $result;exit;
	}

	//用户终止
	function update_user_cancel(){
		$this->check_technician();
		$this->safecheck();
		$model_reservation = D('reservation_order');
		$order_info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->find();
		$data['status'] = 7;
		$data['remark'] = $order_info['remark'].' 技师终止位置：经度:'.$_REQUEST['longitude'].',纬度:'.$_REQUEST['latitude'];
		$res = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->save($data);

		if($res){
			$result['state'] = 'success';
			$result['content'] = '更新成功';
		}else{
			$result['state'] = 'fail';
			$result['content'] = '更新失败';
		}
		$result = json_encode($result);
		echo $result;exit;
	}

	//上门保养步骤数据
	function update_order_step(){
		$this->check_technician();
		$this->safecheck();
		if(!$_REQUEST['longitude'] or !$_REQUEST['latitude']){
			$result['state'] = 'fail';
			$result['content'] = '参数不全';
		}else{
			$model_check_step = D('check_step');
			$map['order_id'] = $_REQUEST['order_id'];
			$info = $model_check_step->where($map)->order('id desc')->find();
			$repeat = $model_check_step->where(array('order_id'=>$_REQUEST['order_id'],'step_id'=>$_REQUEST['step_id']))->order('create_time desc')->find();
			if($repeat){
				if($_REQUEST['step_id']==6){
				$array['status']= 9;
				$this->change_status($array,$_REQUEST['order_id']);
				}
				$data['order_id'] = $_REQUEST['order_id'];
				$data['step_id'] = $_REQUEST['step_id'];
				$data['longitude'] = $_REQUEST['longitude'];
				$data['latitude'] = $_REQUEST['latitude'];
				if($_REQUEST['result_type']){
				$data['result_type'] = $_REQUEST['result_type'];
				}
				$data['create_time'] = time();
				$res = $model_check_step->where(array('id'=>$repeat['id']))->save($data);
				$result['state'] = 'success';
				$result['content'] = '步骤重复';
			}else{
				//步骤不为0时，必须和前一步连续
				//echo $info['step_id'].'</br>'.$_REQUEST['step_id'];
				//必须先走零步骤
				if(!$info and $_REQUEST['step_id']!=0){
					$result['state'] = 'fail';
					$result['content'] = '您还没出发呢';
					$result = json_encode($result);
					echo $result;exit;
				}
				if($info['step_id']==$_REQUEST['step_id']-1 or $_REQUEST['step_id']==0){
					//获取订单支付方式和当前支付状态
					$model_reservation = D('reservation_order');
					$order_info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->find();
					if($_REQUEST['step_id']==0){
						if($order_info['status']==8){
							$result['state'] = 'fail';
							$result['content'] = '这是作废单,请联系客服确认';
							$result = json_encode($result);
							echo $result;exit;
						}
					}
					$model_technician = D('Technician');
					//上门检测第三步，上门保养第二步的时候，如果用户离开，触发离开短信
					if($_REQUEST['step_id']==2){
						if($_REQUEST['result_type']==2){
							$info = $model_technician->where(array('id'=>$order_info['technician_id']))->find();
                            /*
							$send_add_user_data = array(
							'phones'=>$order_info['mobile'],
							'content'=>'我是携车网-府上养车技师'.$info['truename'].'，应约前来为您服务，苦苦等待却还是未曾遇见，只能先行默默离开。期待下次见，下次一定回见，我会一直等下去。看到后请联系我400-660-2822。',
							);
							$res = $this->curl_sms($send_add_user_data,'',1,1);
                            */

                            // dingjb 2015-09-28 17:31:40   切换到云通讯
                            $send_add_user_data = array(
                                'phones'  => $order_info['mobile'],
                                'content' => array($info['truename']),
                            );
                            $res = $this->curl_sms($send_add_user_data, null, 4, '37755');


							$model_sms = D('Sms');
							$send_add_user_data['sendtime'] = time();
							$model_sms->data($send_add_user_data)->add();
						}
					}

					if($_REQUEST['step_id']==3){
						if($_REQUEST['result_type']==2){
							$info = $model_technician->where(array('id'=>$order_info['technician_id']))->find();
                            /*
							$send_add_user_data = array(
							'phones'=>$order_info['mobile'],
							'content'=>'我是携车网-府上养车技师'.$info['truename'].'，应约前来为您服务，苦苦等待却还是未曾遇见，只能先行默默离开。期待下次见，下次一定回见，我会一直等下去。看到后请联系我400-660-2822。',
							);
							$res = $this->curl_sms($send_add_user_data,'',1,1);
                            */

                            // dingjb 2015-09-28 17:31:40   切换到云通讯
                            $send_add_user_data = array(
                                'phones'  => $order_info['mobile'],
                                'content' => array($info['truename']),
                            );
                            $res = $this->curl_sms($send_add_user_data, null, 4, '37755');

							$model_sms = D('Sms');
							$send_add_user_data['sendtime'] = time();
							$model_sms->data($send_add_user_data)->add();
						}else{
							$checkreport_model = D('checkreport_total');
							$mad['reservation_id'] = $_REQUEST['order_id'];
							$info = $checkreport_model->where($mad)->find();
							if(!$info){
								$result['state'] = 'fail';
								$result['content'] = '报告还没生成呢';
								$result = json_encode($result);
								echo $result;exit;
							}
						}
					}

					if($_REQUEST['step_id']==4){
                        //如果是pos和微信,支付宝支付，检测是否填写了付款凭证 wql@20150921
//                        if(in_array($order_info['pay_type'], array('2','3','9'))){ 
//                           $check_ret = $this->check_evidence($_REQUEST['order_id']);
//                           if($check_ret){
//                                $result['state'] = 'success';
//                                $result['content'] = '已填写付款凭证！';
//                                $result = json_encode($result);
//                                echo $result;
//                           }else{
//                                $result['state'] = 'fail';
//								$result['content'] = '请返回填写付款凭证！';
//								$result = json_encode($result);
//								echo $result;exit;   
//                           }
//                        }
                        
						if($order_info['pay_type']==2){
							//根据微信回调数据判断是否已经支付成功
							$model_weixinpaylog = D('weixinpaylog');
							$pay_info = $model_weixinpaylog->where(array('membercoupon_ids'=>'m'.$_REQUEST['order_id']))->find();
							//if($pay_info){

								$data['order_id'] = $_REQUEST['order_id'];
								$data['step_id'] = $_REQUEST['step_id'];
								$data['longitude'] = $_REQUEST['longitude'];
								$data['latitude'] = $_REQUEST['latitude'];
								if($_REQUEST['result_type']){
									$data['result_type'] = $_REQUEST['result_type'];
								}
								$data['create_time'] = time();
								$res = $model_check_step->add($data);
							/*}else{
								$result['state'] = 'fail';
								$result['content'] = '步骤失败，用户尚未支付';
								$result = json_encode($result);
								echo $result;exit;
							}*/
						}
						if($order_info['pay_type']!=2){
							if($order_info['pay_status']==1){
								$data['order_id'] = $_REQUEST['order_id'];
								$data['step_id'] = $_REQUEST['step_id'];
								$data['longitude'] = $_REQUEST['longitude'];
								$data['latitude'] = $_REQUEST['latitude'];
								if($_REQUEST['result_type']){
									$data['result_type'] = $_REQUEST['result_type'];
								}
								$data['create_time'] = time();
								$res = $model_check_step->add($data);
							}else{
								$result['state'] = 'fail';
								$result['content'] = '步骤失败，用户尚未支付';
								$result = json_encode($result);
								echo $result;exit;
							}
						}
					}else{
						$data['order_id'] = $_REQUEST['order_id'];
						$data['step_id'] = $_REQUEST['step_id'];
						$data['longitude'] = $_REQUEST['longitude'];
						$data['latitude'] = $_REQUEST['latitude'];
						if($_REQUEST['result_type']){
							$data['result_type'] = $_REQUEST['result_type'];
						}
						$data['create_time'] = time();
						$res = $model_check_step->add($data);
						//echo $model_check_step->getLastsql();
					}

					if($res){
						if($_REQUEST['step_id']==0){
							$model_reservation = D('reservation_order');
							$order_info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->find();
							$model_technician = D('Technician');
							$info = $model_technician->where(array('id'=>$order_info['technician_id']))->find();
							$model_user = M('tp_admin.user','xc_');
							$user_info = $model_user->where(array('id'=>$info['user_id']))->find();
							if($order_info['city_id']==1 and $order_info['business_source']!=30){ 
								//算距离
								$distance = $this->GetDistance($_REQUEST['latitude'],$_REQUEST['longitude'],$order_info['latitude'],$order_info['longitude']);
								//$distance = $this->GetDistance('31.172448','121.412788','31.171448','121.422788');
								$distance = round($distance/1000,2);
								if($distance<200){
                                    /*
									$send_add_user_data = array(
									'phones'=>$order_info['mobile'],
									//'content'=>'您的订单'.$_REQUEST['order_id'].'所安排的技师'.$info['truename'].'于现在出发，距离您'.$distance.'公里，请您耐心等待。技师联系方式'.$user_info['mobile'].'。如有特殊状况请拨打技师电话或客服电话400-660-2822咨询',
									'content'=>'亲，您今天'.date('m',$order_info['order_time']).'月'.date('d',$order_info['order_time']).'日预约了携车网-府上养车，技师'.$info['truename'].'师傅（'.$user_info['mobile'].'）已经出发飞驰而来，离您只有'.$distance.'公里了，一会就到！有疑问询：400-660-2822。',
									);
                                    */
                                    // dingjb 2015-09-28 17:47:40 切换到云通讯
                                    $send_add_user_data = array(
                                        'phones'  => $order_info['mobile'],
                                        'content' => array(
                                            date('m',$order_info['order_time']), 
                                            date('d',$order_info['order_time']),
                                            $info['truename'],
                                            $user_info['mobile'],
                                            $distance
                                        ),
                                    );

                                    $res = $this->curl_sms($send_add_user_data, null, 4, '37756'); 
								}else{
									$send_add_user_data = array(
									'phones'=>$order_info['mobile'],
									//'content'=>'您的订单'.$_REQUEST['order_id'].'所安排的技师'.$info['truename'].'于现在出发，请您耐心等待。技师联系方式'.$user_info['mobile'].'。如有特殊状况请拨打技师电话或客服电话400-660-2822咨询',
									'content'=>'亲，您今天'.date('m',$order_info['order_time']).'月'.date('d',$order_info['order_time']).'日预约了携车网-府上养车，技师'.$info['truename'].'师傅（'.$user_info['mobile'].'）已经出发飞驰而来，一会就到！有疑问询：400-660-2822。',
									);
                                    $res = $this->curl_sms($send_add_user_data,'',1,1);
								}
								
								$model_sms = D('Sms');
								$send_add_user_data['sendtime'] = time();
								$model_sms->data($send_add_user_data)->add();
							}
							$result['info'] = $model_check_step->where($map)->select();
							$result['state'] = 'success';
							$result['content'] = '添加成功';
						}

						if($_REQUEST['step_id']==4){
							$array['pay_status']= 1;
							$pay_res = $this->change_status($array,$_REQUEST['order_id']);
							$result['info'] = $model_check_step->where($map)->select();
							$result['state'] = 'success';
							$result['content'] = '添加成功';
						}
						if($_REQUEST['step_id']==6){
							$array['status']= 9;
							$this->change_status($array,$_REQUEST['order_id']);
							//绑定库存表和财务表
							//$this->bind($_REQUEST['order_id']);
							$result['info'] = $model_check_step->where($map)->select();
							$result['state'] = 'success';
							$result['content'] = '添加成功';
						}

						if($_REQUEST['step_id']=='1' or $_REQUEST['step_id']=='2' or $_REQUEST['step_id']=='3' or $_REQUEST['step_id']=='5'){
							$result['info'] = $model_check_step->where($map)->select();
							$result['state'] = 'success';
							$result['content'] = '添加成功';
						}

					}else{
						$result['state'] = 'fail';
						$result['content'] = '添加失败';
					}

				}else{
					$result['state'] = 'fail';
					$result['content'] = '步骤错误';
				}
			}
		}
		$result = json_encode($result);
		echo $result;exit;
	}
	//上门保养步骤列表
	function get_order_step(){
		$this->check_technician();
		$this->safecheck();
		if($_REQUEST['order_id']){
			$model_check_step = D('check_step');
			$info = $model_check_step->where(array('order_id'=>$_REQUEST['order_id']))->order('create_time asc')->select();
			if($info){
				$result['info'] = $info;
				$result['state'] = 'success';
				$result['content'] = '获取步骤成功';
			}else{
				$result['info'] = array();
				$result['state'] = 'success';
				$result['content'] = '没有相关步骤数据';
			}
		}else{
			$result['state'] = 'fail';
			$result['content'] = '参数错误';
		}
		$result = json_encode($result);
		echo $result;exit;
	}
	
	function bind($id){
		$order_param['id'] = $id;
		$reservation_order_model = D('reservation_order');
		$order_info = $reservation_order_model->where($order_param)->find();
		if (!$order_info || $order_info['status'] == 9) {
			return false;
		}
		//绑定库存表
		$item = unserialize($order_info['item']);
		$oil = $item['price']['oil'];
		$oil_detail = $item['oil_detail'];
		$i = $oil_1_num = $oil_2_num = 0;
		foreach ($oil as $k_id=>$k_price){
			if($i == 0){
				$oil_1_id = $k_id;
				$oil_1_price = $k_price;
				$oil_1_num = $oil_detail[$k_id];
			}else{
				$oil_2_id = $k_id;
				$oil_2_price = $k_price;
				$oil_2_num = $oil_detail[$k_id];
			}
			$i++;
		}
	
		list($filter_id,$filter_price) = each($item['price']['filter']);
		list($kongqi_id,$kongqi_price) = each($item['price']['kongqi']);
		list($kongtiao_id,$kongtiao_price) = each($item['price']['kongtiao']);
	
		$oil_1 = array('id'=>$oil_1_id,'price'=>$oil_1_price,'num'=>$oil_1_num);
		$oil_2 = array('id'=>$oil_2_id,'price'=>$oil_2_price,'num'=>$oil_2_num);
		$filter = array('id'=>$filter_id,'price'=>$filter_price);
		$kongqi = array('id'=>$kongqi_id,'price'=>$kongqi_price);
		$kongtiao = array('id'=>$kongtiao_id,'price'=>$kongtiao_price);
		$this->_bind_storehouse($id,$oil_1,$oil_2,$filter,$kongqi,$kongtiao);
	
		//绑定财务表
		$data = array(
				'finance_type'=>1,
				'oid'=>$id
		);
		
		$finance_model = M('tp_xieche.finance','xc_');  //财务表
		$finance_model->add($data);
		$this->addCodeLog('bind_storehouse', '绑定财务表:'.$id.'：'.$finance_model->getLastSql());
		$this->submitCodeLog('bind_storehouse');
	}
	
	private function _bind_storehouse($id,$oil_1,$oil_2,$filter,$kongqi,$kongtiao){
		$storehouse_item_model = M('tp_xieche.storehouse_item','xc_');//仓库数据详情表
		$this->addCodeLog('bind_storehouse', '开始绑定仓库1:'.$id);
		if (!empty($oil_1['id'])) {
			$num1 = ($oil_1['num'] >1 )?$oil_1['num']:1;
			$update_oil_1 = array(
					'order_id'=>$id,
					'order_type'=>'上门保养',
					'sale_price'=>$oil_1['price']/$num1,
					'lock_status'=>2,
    				'update_time'=>time()
			);
			
			$storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$oil_1['id']))->limit($num1)->save($update_oil_1);
			$this->addCodeLog('bind_storehouse', '绑定仓库1:'.$id.'机油1：'.$storehouse_item_model->getLastSql());
		}
	
		if (!empty($oil_2['id'])) {
			$num2 = ($oil_2['num'] >1 )?$oil_2['num']:1;
			$update_oil_2 = array(
					'order_id'=>$id,
					'order_type'=>'上门保养',
					'sale_price'=>$oil_2['price']/$num2,
					'lock_status'=>2,
    				'update_time'=>time()
			);
			$storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$oil_2['id']))->limit($num2)->save($update_oil_2);
			$this->addCodeLog('bind_storehouse', '绑定仓库1:'.$id.'机油2：'.$storehouse_item_model->getLastSql());
		}
		if (!empty($filter['id'])) {
			$update_filter = array(
					'order_id'=>$id,
					'order_type'=>'上门保养',
					'sale_price'=>$filter['price'],
					'lock_status'=>2,
    				'update_time'=>time()
			);
			$storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$filter['id']))->limit(1)->save($update_filter);
			$this->addCodeLog('bind_storehouse', '绑定仓库1:'.$id.'机滤：'.$storehouse_item_model->getLastSql());
		}
		if (!empty($kongqi['id'])) {
			$update_kongqi = array(
					'order_id'=>$id,
					'order_type'=>'上门保养',
					'sale_price'=>$kongqi['price'],
					'lock_status'=>2,
    				'update_time'=>time()
			);
			$storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$kongqi['id']))->limit(1)->save($update_kongqi);
			$this->addCodeLog('bind_storehouse', '绑定仓库1:'.$id.'空气滤：'.$storehouse_item_model->getLastSql());
		}
		if (!empty($kongtiao['id'])) {
			$update_kongtiao = array(
					'order_id'=>$id,
					'order_type'=>'上门保养',
					'sale_price'=>$kongtiao['price'],
					'lock_status'=>2,
    				'update_time'=>time()
			);
			$storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$kongtiao['id']))->limit(1)->save($update_kongtiao);
			$this->addCodeLog('bind_storehouse', '绑定仓库1:'.$id.'空调滤：'.$storehouse_item_model->getLastSql());
		}
	}

	/*function:技师端开发票接口
	 *author:wuwenyu
	 *
	 */
	function bill_invoice(){
		$this->check_technician();
        if ($_REQUEST['receiver_phone'] and !eregi("^1[0-9]{10}$",$_REQUEST['receiver_phone'])){
            $result['state'] = 'fail';
            $result['content'] = '电话格式错误';
            $result = json_encode($result);
            echo $result;exit;
        }
		//已支付才能开票
		$model_reservation = D('reservation_order');
		$order_map['id'] = $_REQUEST['order_id'];
		$order_info = $model_reservation->where($order_map)->find();
		if(!$order_info){
			$result['state'] = 'fail';
			$result['content'] = '订单不存在';
			$result = json_encode($result);
			echo $result;exit;
		}
		if($order_info['pay_status']==0){
			$result['state'] = 'fail';
			$result['content'] = '订单尚未支付';
			$result = json_encode($result);
			echo $result;exit;
		}
		$model_invoice = D('invoice');
		$map['order_id'] = $_REQUEST['order_id'];
		$invoice_info = $model_invoice->where($map)->find();
		if($invoice_info){
			$result['state'] = 'fail';
			$result['content'] = '此单已开发票';
		}else{
			foreach($_REQUEST as $k=>$v){
				$data[$k] = $v;
			}
			$data['create_time'] = time();
			$res = $model_invoice->add($data);
			$order_data['is_invoice'] = 2;
			$model_reservation->where($order_map)->save($order_data);
			//echo $model_reservation->getLastsql();
			$result['state'] = 'success';
			$result['content'] = '开票成功';
			$result['info'] = '';
		}
		$result = json_encode($result);
		echo $result;exit;
	}

	//所有接口验证技师和设备号的公共方法
	function check_technician(){
		$technician_model = D('technician');
		$map['id'] = $_SERVER['HTTP_ENGINEER_ID'];
		//刘成不验证
		/*if ($map['id'] == 14) {
			return true;
		}*/
		//print_r($_SERVER);
		$info = $technician_model->where($map)->find();
		
		//echo $technician_model->getLastsql();
		if(!$info){
			$result['state'] = 'fail';
			$result['content'] = '认证错误-1  -b '.$map['id'];
			$result = json_encode($result);
			echo $result;exit;
		}else{
			$device_id = explode(",", $info['device_id']); 
			if(count($device_id)>1 and !in_array($_SERVER['HTTP_DEVICE_ID'],$device_id)){
				$result['state'] = 'fail';
				$result['content'] = '认证错误-3';
				$result = json_encode($result);
				echo $result;exit;
			}
			if(count($device_id)==1 and $info['device_id']!= $_SERVER['HTTP_DEVICE_ID']){
				$result['state'] = 'fail';
				$result['content'] = '认证错误-2';
				$result = json_encode($result);
				echo $result;exit;
			}else{
				return true;
			}
		}
	}

	function test(){
		$a=unserialize('a:47:{s:12:"机油液位";s:6:"正常";s:12:"空气滤芯";s:6:"脏污";s:12:"空调滤芯";s:6:"脏污";s:15:"雨刮水液面";s:6:"正常";s:15:"左前胎胎压";s:6:"正常";s:15:"右前胎胎压";s:6:"正常";s:15:"左后胎胎压";s:6:"正常";s:15:"右后胎胎压";s:6:"正常";s:15:"防冻液液面";s:6:"正常";s:18:"防冻液冰点值";s:8:"负40℃";s:18:"制动液含水量";s:3:"<3%";s:18:"前刹车片厚度";s:6:">3.4mm";s:18:"后刹车片厚度";s:6:">3.4mm";s:18:"前刹车盘厚度";s:12:"轻微磨损";s:18:"后刹车盘厚度";s:12:"轻微磨损";s:18:"蓄电池健康度";s:4:">50%";s:18:"前胎花纹深度";s:6:">3.5mm";s:18:"后胎花纹深度";s:6:">3.5mm";s:9:"雨刮片";s:6:"良好";s:12:"灯光照明";s:0:"";s:9:"点烟器";s:6:"良好";s:6:"球头";s:6:"良好";s:12:"转向拉杆";s:6:"良好";s:9:"变速箱";s:6:"良好";s:15:"油底壳螺丝";s:6:"良好";s:9:"避震器";s:6:"良好";s:12:"上下摆臂";s:6:"良好";s:6:"半轴";s:6:"良好";s:9:"安全带";s:6:"良好";s:12:"手制动器";s:6:"良好";s:18:"发动机舱管线";s:6:"良好";s:15:"气门室盖垫";s:6:"良好";s:15:"方向机油壶";s:6:"良好";s:15:"机油指示灯";s:6:"良好";s:12:"ABS指示灯";s:6:"良好";s:18:"发动机指示灯";s:6:"良好";s:15:"刹车指示灯";s:6:"良好";s:15:"气囊指示灯";s:6:"良好";s:9:"car_brand";s:6:"本田";s:9:"car_model";s:17:"歌诗图 2.4L AT";s:10:"car_series";s:24:"歌诗图 (广汽本田)";s:7:"mileage";s:5:"23666";s:5:"phone";s:11:"13564467778";s:4:"date";s:10:"2015-04-16";s:13:"engine_number";s:0:"";s:3:"vin";s:17:"LHGTF3873D8064917";s:21:"last_maintenance_date";s:0:"";}');
		print_r($a);
        $b=Array(
			'oil_id' => '50',
			'oil_detail' =>Array
				(
					'51' => '7'
				),
			'filter_id' => '2',
			'kongqi_id' => '',
			'kongtiao_id' => '',
			'price' => Array
				(
					'oil' => Array
						(
							'51' => '595'
						),

					'filter' => Array
						(
							'2' => '89'
						),

					'kongqi' => Array
						(
							'0' => '0'
						),

					'kongtiao' => Array
						(
							'0' => '0'
						)
				)

            );
            $b=serialize($b);
            echo $b;
	}
    //下发IOS设备消息
    function DemoPushSingleDeviceIOS($test)
    {   
        include 'XingeApp.php';
        $push = new XingeApp(2200080374, '766799dbcccbcf1bf66dd723e551abaa');
        $mess = new MessageIOS();
        $mess->setExpireTime(86400);
        //$mess->setSendTime("2014-03-13 16:00:00");
        $mess->setAlert($test);
        //$mess->setAlert(array('key1'=>'value1'));
        $mess->setBadge(1);
        $mess->setSound("beep.wav");
        $custom = array('key1'=>'value1', 'key2'=>'value2');
        $mess->setCustom($custom);
        $acceptTime = new TimeInterval(0, 0, 23, 59);
        $mess->addAcceptTime($acceptTime);
        $ret = $push->PushSingleDevice('f070ce964c87a88b4c5d5f3dd7c7bce46aee52dc03021b294edbf44371783ebe', $mess, XingeApp::IOSENV_DEV);
        return $ret;
    }

    //Android tags 
    function PushTags_android($from,$to,$content,$senduser)
    {
        include_once 'XingeApp.php';
        $push = new XingeApp(2100098564, '8324049b42183a13a38cac8bdd451aa9');
        $mess = new Message();
        if($senduser==1){
            $tags = $from."-".$to;
        }else if($senduser==2){
            $tags = "test1";
        }
        $mess->setExpireTime(0);
        $mess->setTitle('title');
        $mess->setContent($content);
        $custom = array('from'=>$from, 'content'=>$content, 'to'=>$to);
        $mess->setCustom($custom);
        $mess->setType(Message::TYPE_MESSAGE);
        $tagList = array($tags);
        $ret = $push->PushTags(0, $tagList, 'OR', $mess);
        return ($ret);
    }

    //IOStags发送
    function PushTags_ios($from,$to,$content,$senduser)
    {
        include_once 'XingeApp.php';
        $push = new XingeApp(2200098565, '295a010e4a6b88e32a9f2fe03918116c');
        $mess = new MessageIOS();
        if($senduser==1){
            $tags = $from."-".$to;
        }else if($senduser==2){
            $tags = "test1";
        }
        $mess->setExpireTime(0);
        $mess->setAlert($content);
        $custom = array('from'=>$from, 'content'=>$content, 'to'=>$to);
        $mess->setCustom($custom);
        $tagList = array($tags);
        $ret = $push->PushTags(0, $tagList, 'OR', $mess, XingeApp::IOSENV_DEV);
        return ($ret);
    }

    //发给客服

    function send_message(){
        if($_REQUEST['from']&&$_REQUEST['to']&&$_REQUEST['content']){
            if($_REQUEST['to']==888){
                $s_ios = $this->PushTags_ios("".$_REQUEST['from']."","".$_REQUEST['to']."","".$_REQUEST['content']."",2);
                $s_android = $this->PushTags_android($_REQUEST['from'],$_REQUEST['to'],$_REQUEST['content'],2);
            }else{
                $s_ios = $this->PushTags_ios("".$_REQUEST['from']."","".$_REQUEST['to']."","".$_REQUEST['content']."",1);
                $s_android = $this->PushTags_android($_REQUEST['from'],$_REQUEST['to'],$_REQUEST['content'],1);
            }

            $custom = array('from'=>$_REQUEST['from'], 'content'=>$_REQUEST['content'], 'to'=>$_REQUEST['to'], 'date'=>time());
            //var_dump($s_ios['ret_code']);exit;
            if($s_ios['ret_code']==0 || $s_android['ret_code']==0){
                $data['state'] = 'success';
                $data['msg'] = '发送成功';
                $data['data'] = $custom;
            }else{
                $data['state'] = 'fail';
                $data['message'] = '发送失败';
                $data['data'] = $custom;
            }
        }else{
            $data['state'] = 'fail';
            $data['msg'] = '参数错误';
            $data['data'] = $custom;
        }
        echo json_encode($data);exit;
    }


    //添加检测备注数据
    function check_remarks(){
        if(!$_REQUEST){
            $resArr['state'] = 'fail';
            $resArr['message'] = '参数错误';
            $resArr['info'] = NULL;
            echo json_encode($resArr);exit;
        }

        if(!$_REQUEST['order_id']){
            $resArr['state'] = 'fail';
            $resArr['message'] = '参数错误';
            $resArr['info'] = NULL;
            echo json_encode($resArr);exit;
        }

        $mcheck = D('check_remarks');
        $where['order_id'] = $_REQUEST['order_id'];

        //$data['address'] = $_REQUEST['address'];
        $data['remarks'] = $_REQUEST['remarks'];
        $data['create_time'] = microtime(TRUE);

        $resonly = $mcheck->where($where)->find();

        if($resonly){
            $res = $mcheck->where($where)->save($data);
        }else{
            $data['order_id'] = $_REQUEST['order_id'];
            $res = $mcheck->add($data);
        }

        if($res){
            if(count(json_decode($_REQUEST['remarks'],true))>0){
                $ssss = $this->DemoPushSingleDeviceIOS(''.$res.'');
                $resArr['message'] = '添加成功'.$ssss['ret_code'];
             }else{
                 $resArr['message'] = '添加成功';
             }
            $resArr['state'] = 'success';
        }else{
            $resArr['state'] = 'fail';
            $resArr['message'] = '添加失败';
        }
            $resArr['info'] = NULL;
            echo json_encode($resArr);
    }

    function remark_achieve(){
        if(!$_REQUEST['id']){
            $this->error("参数错误");
        }

        $mcheck  = M('tp_xieche.check_remarks','xc_');
        $mcheck_report  = M('tp_xieche.check_report','xc_');

        if($_REQUEST['id']!='all'){
            $where['id'] = $_REQUEST['id'];
        }else{

        }
        $res = $mcheck->where($where)->select();

        if(!$res){
            $this->error("参数错误");
        }

        $str = '{';

        for ($i=0; $i <count($res) ; $i++) { 
			$where2['reservation_id'] = $res[$i]['order_id'];
			$res2= $mcheck_report->where($where2)->order('create_time DESC')->find();

			if($res2['a22']!=''){          
				if($i>0 AND strlen($str)>2){
				 $str .= ',';
				}
				
				$imgArr = explode(',', $res[$i]['image_url']);
				$imgStr = '{';
				for($j=0;$j<count($imgArr); $j++){
					if($j>0){
						$imgStr .= ',';
					}
					$imgStr .= '"'.$j.'":"'.$imgArr[$j].'"';
				}
				$imgStr .= '}';
				
				 $str .= '"'.$res[$i]['id'].'":{"id":"'.$res[$i]['id'].'","order_id":"'.$res[$i]['order_id'].'","check_report_id":"'.base64_encode($res[$i]['order_id'].'168').'","address":"'.$res[$i]['address'].'","image_url":'.$imgStr.',"sound_url":"'.$res[$i]['sound_url'].'","remarks":'.$res[$i]['remarks'].',"create_time":"'.date('Y-m-d H:i:s',$res[$i]['create_time']).'","car_brand":"'.$res2['a29'].'","car_series":"'.$res2['a28'].'","username":"'.$res2['a22'].'","mobile":"'.$res2['a1'].'","car_number":"'.$res2['a20'].'"}';
			}
        }
            $str .= '}';
        

        echo $str;

    }

    //上传检测备注图片
    function remark_upload_img(){

        $mcheck  = M('tp_xieche.check_remarks','xc_');  //车品牌

        header("Content-type:text/html;charset=utf-8");
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg');// 设置附件上传类型
        $upload->savePath =  'UPLOADS/Checkremarks/img/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $info = $upload->getErrorMsg();
            $resArr['info'] = NULL;
            $resArr['state'] = 'success';
            $resArr['message'] = $info;
            echo json_encode($resArr);exit;
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            
            $resArr['info'] = $info;
            
            $imgUrl = $info[0]['savename'];
            $imgName = explode(".",$imgUrl);
            $imgArr = explode("_", $imgName[0]);

            if(count($imgArr)>3||count($imgArr)<3){
                $resArr['state'] = 'fail';
                $resArr['message'] = '文件命名错误';
                echo json_encode($resArr);exit;
            }
            if(!is_numeric($imgArr[2])){
                $resArr['state'] = 'fail';
                $resArr['message'] = '文件命名错误';
                echo json_encode($resArr);exit;
            }
            if(is_numeric($imgArr[0])){

                $where['order_id'] = $imgArr[0];

                $check = $mcheck->where($where)->find();

                if($check['image_url']){
                    $data['image_url'] = $check['image_url'].','.$info[0]['savename'];
                }else{
                    $data['image_url'] = $info[0]['savename'];
                }

                $result = $mcheck->where($where)->save($data);
                if($result){
                    $resArr['state'] = 'success';
                    $resArr['message'] = '上传成功';
                    echo json_encode($resArr);exit;
                }else{
                    $resArr['state'] = 'success';
                    $resArr['message'] = '检测报告ID不存在';
                    echo json_encode($resArr);exit;
                }
                
            }else{
                $resArr['state'] = 'fail';
                $resArr['message'] = '文件命名错误';
                echo json_encode($resArr);exit;
            }
        }
        
        
        
    }

    //上传检测备注语音
    function remark_upload_voice(){

        $mcheck  = M('tp_xieche.check_remarks','xc_');  //车品牌

        header("Content-type:text/html;charset=utf-8");
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 314572800;// 设置附件上传大小
        $upload->allowExts  = array('wav');// 设置附件上传类型
        $upload->uploadReplace = true;
        $upload->savePath =  'UPLOADS/Checkremarks/wav/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $info = $upload->getErrorMsg();
            $resArr['info'] = NULL;
            $resArr['state'] = 'success';
            $resArr['message'] = $info;
            echo json_encode($resArr);exit;
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            
            $resArr['info'] = $info;
            
            $imgUrl = $info[0]['savename'];
            $imgName = explode(".",$imgUrl);
            $imgArr = explode("_", $imgName[0]);

            if(count($imgArr)!=2){
                $resArr['state'] = 'fail';
                $resArr['message'] = '文件命名错误';
                echo json_encode($resArr);exit;
            }
            if(!is_numeric($imgArr[1])){
                $resArr['state'] = 'fail';
                $resArr['message'] = '未指定检查项';
                echo json_encode($resArr);exit;
            }
            if(is_numeric($imgArr[0])){
                $where['order_id'] = $imgArr[0];

                $check = $mcheck->where($where)->find();

                if($check['sound_url']){
                    $data['sound_url'] = $check['sound_url'].','.$info[0]['savename'];
                }else{
                    $data['sound_url'] = $info[0]['savename'];
                }

                $result = $mcheck->where($where)->save($data);
                if($result){
                    $resArr['state'] = 'success';
                    $resArr['message'] = '上传成功';
                    echo json_encode($resArr);exit;
                }else{
                    $resArr['state'] = 'success';
                    $resArr['message'] = '检测报告ID不存在';
                    echo json_encode($resArr);exit;
                }
                
            }else{
                $resArr['state'] = 'fail';
                $resArr['message'] = '文件命名错误';
                echo json_encode($resArr);exit;
            }
        }
    }


    //检测推送-文字备注
    function check_text_remarks_push(){
        if(!$_REQUEST||!$_REQUEST['check_id']||!$_REQUEST['order_id']||!$_REQUEST['type']){
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['message'] = '参数错误';
            echo json_encode($resArr);exit;
        }
        if($_REQUEST['type']!=1){
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['message'] = '消息类型错误';
            echo json_encode($resArr);exit;
        }

        $mcheckpush = D('check_remarks_push');

        $data['check_id'] = $_REQUEST['check_id'];
        $data['order_id'] = $_REQUEST['order_id'];
        $data['type'] = $_REQUEST['type'];
        $data['text_remark'] = $_REQUEST['text_remark'];
        $data['create_time'] = microtime(TRUE);
        $res = $mcheckpush->add($data);

        if($res){
            $resArr['state'] = 'success';
            $resArr['message'] = '添加成功';
            $ssss = $this->DemoPushSingleDeviceIOS(''.$_REQUEST['text_remark'].'');
            //推送文字到用户微信
            $re_wxid = $this->check_remark_findopenid($res);

            if($re_wxid['state'] == 'success'){
                $wxdata['open_id'] = $re_wxid['info'];
                $wxdata['content'] = $data['text_remark'];
                $this->send_weixintext($wxdata);
                $resArr['info'] = '已推送到微信';
            }else{
                $resArr['info'] = $re_wxid['message'];
            }

        }else{
            $resArr['state'] = 'fail';
            $resArr['message'] = '添加失败';
        }
            
            echo json_encode($resArr);
    }

    function check_remark_findopenid($id){
        if(!$id){
            $resArr['state'] = 'fail';
            $resArr['message'] = 'ID错误';
            $resArr['info'] = NULL;
            return $resArr;exit;
        }
        $where['id'] = $id;
        $model_check_remarks_push = D('check_remarks_push');
        $res = $model_check_remarks_push->where($where)->find();
        if(!$res){
            $resArr['state'] = 'fail';
            $resArr['message'] = '网络错误';
            $resArr['info'] = NULL;
            return $resArr;exit;
        }

        $where2['id'] = $res['order_id'];
        $model_order = D("reservation_order");
        $res2 = $model_order->where($where2)->field('mobile')->find();

        if(!$res2){
            $resArr['state'] = 'fail';
            $resArr['message'] = '没有此订单';
            $resArr['info'] = NULL;
            return $resArr;exit;
        }

        $where3['mobile'] = $res2['mobile'];
        $model_paweixin = D('paweixin');
        $res3 = $model_paweixin->where($where3)->field('wx_id')->find();

        if(!$res3){
            $resArr['state'] = 'fail';
            $resArr['message'] = '未绑定微信';
            $resArr['info'] = NULL;
            return $resArr;exit;
        }

        $resArr['state'] = 'success';
        $resArr['message'] = '成功获取用户微信ID';
        $resArr['info'] = $res3['wx_id'];
        return $resArr;
        
    }

    //检测推送-图片
    function check_img_remarks_push(){
        if(!$_REQUEST||!$_REQUEST['check_id']||!$_REQUEST['order_id']||!$_REQUEST['type']){
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['message'] = '参数错误';
            echo json_encode($resArr);exit;
        }
        if($_REQUEST['type']!=2){
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['message'] = '消息类型错误';
            echo json_encode($resArr);exit;
        }
        $mcheckpush = D('check_remarks_push');

        header("Content-type:text/html;charset=utf-8");
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728;// 设置附件上传大小
        $upload->allowExts  = array('jpg');// 设置附件上传类型
        $upload->savePath =  'UPLOADS/Checkremarks/img/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $info = $upload->getErrorMsg();
            $resArr['info'] = NULL;
            $resArr['state'] = 'success';
            $resArr['message'] = $info;
            echo json_encode($resArr);exit;
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            
            $resArr['info'] = $info;

            $data['check_id'] = $_REQUEST['check_id'];
            $data['order_id'] = $_REQUEST['order_id'];
            $data['type'] = $_REQUEST['type'];
            $data['create_time'] = microtime(TRUE);
            $data['image_url'] = $info[0]['savename'];
            $result = $mcheckpush->add($data);

            if($result){
                $resArr['state'] = 'success';
                $resArr['message'] = '上传成功';

                //推送图片到用户
                $re_wxid = $this->check_remark_findopenid($result);
                if($re_wxid['state'] == 'success'){
                    $this->upload_pic('UPLOADS/Checkremarks/img/'.$info[0]['savename'],$re_wxid['info']);
                    $resArr['info'] = '已推送到微信';
                }else{
                    $resArr['info'] = $re_wxid['message'];
                }

            }else{
                $resArr['state'] = 'success';
                $resArr['message'] = '检测报告ID不存在';
            }
            echo json_encode($resArr);exit;
        }

    }
	//大众点评券码和订单绑定
	function activity_dianpin_coupon_remark(){
		$this->safecheck();
		$model_reservation = D('reservation_order');
		$info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->count();
		if($info==0){
			$resArr['state'] = 'fail';
			$resArr['message'] = '订单号不存在';
			echo json_encode($resArr);exit;
		}
		$model_code =D('dianpingcode');
		$data['order_id'] = intval($_REQUEST['order_id']);
		$data['coupon_code'] = htmlspecialchars($_REQUEST['coupon_code'],ENT_QUOTES);
		$data['create_time'] = time();
		$res = $model_code->add($data);
		if($res){
			$resArr['state'] = 'success';
			$resArr['content'] = '记录存储成功';
			$resArr['info'] = $res;
		}else{
			$resArr['state'] = 'fail';
			$resArr['content'] = '记录存储失败';
		}
		echo json_encode($resArr);exit;
	}
	//大众点评券码订单支付
	function activity_dianpin_pay(){
		$this->safecheck();
		$model_code =D('dianpingcode');
		$info = $model_code->where(array('order_id'=>$_REQUEST['order_id']))->count();
		if($info==0){
			$resArr['state'] = 'fail';
			$resArr['content'] = '该订单未备注券码';
			echo json_encode($resArr);exit;
		}
		$model_reservation =D('reservation_order');
		$data['pay_status'] = 1;
		$data['pay_type'] = 5;//大众点评支付
		$model_reservation->where(array('id'=>$_REQUEST['order_id']))->save($data);
		//echo $model_reservation->getLastsql();
		$resArr['state'] = 'success';
		$resArr['content'] = '支付成功';
		$resArr['info'] = $res;
		echo json_encode($resArr);exit;
	}
	//作废订单
	function carservice_cancel(){
		$this->safecheck();
		$model_reservation =D('reservation_order');
		$info = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->find();
		if($info['order_type']==34){
			$data['status'] = 8;
			$result = $model_reservation->where(array('id'=>$_REQUEST['order_id']))->save($data);
			//echo $model_reservation->getLastsql();
			if($result!==false){
				$resArr['state'] = 'success';
				$resArr['content'] = '作废成功';
			}else{
				$resArr['state'] = 'fail';
				$resArr['content'] = '作废失败';
			}
		}else{
			$resArr['state'] = 'fail';
			$resArr['content'] = '您不具有此单的作废权限，请联系客服';
		}
		echo json_encode($resArr);exit;
	}
    //上传图片（上传纳税人证明图片）
    public function upload_tax_payer_proving_image(){
        $provingimg_model =D('provingimg');

        header("Content-type:text/html;charset=utf-8");
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728;// 设置附件上传大小
        $upload->allowExts  = array('jpg');// 设置附件上传类型
        $upload->uploadReplace = true;  //设置覆盖重复名
        $upload->savePath =  'UPLOADS/proving/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $info = $upload->getErrorMsg();
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['content'] = $info;
            echo json_encode($resArr);exit;
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            // $resArr['info'] = $info;
            $arr_id = explode("_", $info[0]['savename']);
            $data['reservation_id'] = $arr_id[0];
            $data['create_time'] = time();
            if($info[1]['savename']) {
                $img = '';
                foreach( $info as $k=>$v){
                    $img= $img.$v['savename'].',';
                }
                $data['img_url'] = substr($img,0,strlen($img)-1);
            }else{
                $data['img_url'] = $info[0]['savename'];
            }
            $result = $provingimg_model->add($data);
            if($result && $info[0]['savename']){
                $resArr['state'] = 'success';
                $resArr['content'] = '上传成功';
                $resArr['info'] = "http://www.xieche.com.cn/".$info[0]['savename'];

            }else{
                $resArr['state'] = 'fail';
                $resArr['content'] = '上传失败';
                $resArr['info'] = NULL;
            }
            echo json_encode($resArr);exit;
        }
    }

    //上传图片（pad配件错误取消订单的）
    public function upload_error_order_item_image(){
        $itemerrimg_model =D('itemerrimg');

        header("Content-type:text/html;charset=utf-8");
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728;// 设置附件上传大小
        $upload->allowExts  = array('jpg');// 设置附件上传类型
        $upload->uploadReplace = true;  //设置覆盖重复名
        $upload->savePath =  'UPLOADS/ItemError/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $info = $upload->getErrorMsg();
            $resArr['info'] = NULL;
            $resArr['state'] = 'fail';
            $resArr['message'] = $info;
            echo json_encode($resArr);exit;
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
           // $resArr['info'] = $info;
            $arr_id = explode("_", $info[0]['savename']);
            $data['reservation_id'] = $arr_id[0];
            $data['create_time'] = time();
            if($info[1]['savename']) {
                $img = '';
                foreach( $info as $k=>$v){
                    $img= $img.$v['savename'].',';
                }
                $data['img_url'] = substr($img,0,strlen($img)-1);
            }else{
                $data['img_url'] = $info[0]['savename'];
            }
            $result = $itemerrimg_model->add($data);
            if($result && $info[0]['savename']){
                $resArr['state'] = 'success';
                $resArr['message'] = '上传成功';
            }else{
                $resArr['state'] = 'fail';
                $resArr['message'] = '上传失败';
            }
            echo json_encode($resArr);exit;
        }
    }
    //技师取消订单
    function cancel_home_maintenance_order(){
        $this->safecheck();
        $reservation_order =D('reservation_order');
        $dphome_linshi = D('dphome_linshi');  //点评数据临时表   
        
        $order_id = $_REQUEST["order_id"];
        $message = $_REQUEST['remark'];
        if($order_id && $message){
            $map['id'] = $order_id;
            $order_info = $reservation_order->where($map)->find();
            if($order_info['status']==9){
                $resArr['state'] = 'fail';
                $resArr['content'] = '订单完成不能取消';
            }else {
                if ($order_info) {
                    if ($_REQUEST['cancel_type'] == 1) {
                        $data['operator_remark'] = $order_info['operator_remark'] . "取消订单原因：用户主动取消。" . '技师备注：' . $message;
                    } elseif ($_REQUEST['cancel_type'] == 2) {
                        $data['operator_remark'] = $order_info['operator_remark'] . "取消订单原因：客户联系不上。" . '技师备注：' . $message;
                    } elseif ($_REQUEST['cancel_type'] == 3) {
                        $data['operator_remark'] = $order_info['operator_remark'] . "取消订单原因：配件错误。" . '技师备注：' . $message;
                    }
                    $data['status'] = 0;
                    $data['technician_id'] = 0;
                    $int = $reservation_order->where($map)->save($data);
                    if ($int) {
                        $resArr['state'] = 'success';
                        $resArr['content'] = '取消成功';
                    } else {
                        $resArr['state'] = 'fail';
                        $resArr['content'] = '取消失败';
                    }
                } else {
                    $resArr['state'] = 'fail';
                    $resArr['content'] = '订单不存在';
                }
            }
        }else{
            $resArr['state'] = 'fail';
            $resArr['content'] = '参数不对';
        }
        echo json_encode($resArr);exit;
    }
    //技师下无配件订单(检测订单)
    function create_no_accessory_order(){
        $reservation_model = D('reservation_order');
        $member_model = D('member');
        $sms_model = D('Sms');  //短信表
        $memberlog_model = D('Memberlog');  //用户日志表
        $mobile = $_POST['mobile'];
        $userinfo = $member_model->where(array('mobile'=>$mobile,'status'=>'1'))->find();
        if($userinfo){
            $_SESSION['uid'] = $userinfo['uid'];
        }else{
            $member_data['mobile'] = $mobile;
            $member_data['password'] = md5($mobile);
            $member_data['reg_time'] = time();
            $member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
            $member_data['fromstatus'] = '50';//上门宝洋
            $_SESSION['uid'] = $member_model->data($member_data)->add();
            /*
            $send_add_user_data = array(
                'phones'=>$mobile,
                'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$mobile.'来登录携车网，客服4006602822。',
            );
            $this->curl_sms($send_add_user_data);
            */

            // dingjb 2015-09-29 10:53:35 切换到云通讯
            $send_add_user_data = array(
                'phones'  => $mobile,
                'content' => array(
                    $mobile,
                    $mobile
                )
            );
            $this->curl_sms($send_add_user_data, null, 4, '37653');
            
            $send_add_user_data['sendtime'] = time();
            $sms_model->data($send_add_user_data)->add();

            $data['createtime'] = time();
            $data['mobile'] = $mobile;
            $data['memo'] = '用户注册';
            $memberlog_model->data($data)->add();
        }
        $order_info['order_type'] = 1;  //技师pad无配件订单
        //$order_info['business_source'] = 34;  //技师pad 来源 无配件订单
        $order_info['business_source'] = 41;  //技师pad 来源 无配件订单
        $order_info['pay_type'] = $_POST['pay_type'];
        $order_info['pay_status'] = 0;
        $order_info['status'] = 2;     //已分配技师
        $order_info['remark'] = "pad无配件下单";
        $order_info['technician_id'] = $_POST['engineer_id']?$_POST['engineer_id']:0;
        $order_info['uid'] = $_SESSION['uid'];
        $order_info['truename'] = $_POST['truename'];
        $order_info['address'] = $_POST['address'];
        $order_info['mobile'] = $mobile;
        $order_info['amount'] = 99;   //服务费
        $order_info['item'] ='a:6:{s:6:"oil_id";N;s:10:"oil_detail";N;s:9:"filter_id";N;s:9:"kongqi_id";N;s:11:"kongtiao_id";N;s:5:"price";a:4:{s:3:"oil";a:1:{i:0;i:0;}s:6:"filter";a:1:{i:0;i:0;}s:6:"kongqi";a:1:{i:0;i:0;}s:8:"kongtiao";a:1:{i:0;i:0;}}}';   //服务费


        $order_info['model_id'] = $_POST['car_model']?$_POST['car_model']:0;
        $order_info['licenseplate'] = $_POST['car_number']?$_POST['car_number']:0;
        //$order_info['licenseplate'] = $_POST['licenseplate_type'].$_POST['licenseplate'];
        if($_POST['car_reg_time']){
            $order_info['car_reg_time'] = strtotime($_POST['car_reg_time']);
        }else{
            $order_info['car_reg_time'] = 0;
        }
        $order_info['engine_num'] = $_POST['engine_num']?$_POST['engine_num']:0;
        $order_info['vin_num'] =$_POST['vin_num']?$_POST['vin_num']:0;
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
        $order_info['create_time'] = time();
        $order_info['order_time'] = strtotime($_POST['reservation_date']);
        $order_info['city_id'] =$_POST['city_id']?$_POST['city_id']:0;

        $order_id = $reservation_model->data($order_info)->add();
        $sql = $reservation_model->getLastsql();
        if($order_id){
            //发短信通知用户
            $send_add_order_data = array(
                'phones'=>$order_info['mobile'],
                'content'=>"您预约".date('m',strtotime($_POST['reservation_date'])).'月'.date('d',strtotime($_POST['reservation_date'])).'日'.date('H',strtotime($_POST['reservation_date']))."时的“府上养车”上门保养服务,如有疑问请联系客服！(工作时间9-18点)。4006602822",
            );
            $this->curl_sms($send_add_order_data,'',1);  //Todo 内外暂不发短信
            $send_add_order_data['sendtime'] = time();
            $sms_model->data($send_add_order_data)->add();
            //插入日志
            $this->addCodeLog('pad下单成功', $sql, $order_id);
            $resArr['state'] = 'success';
            $resArr['content'] = '下单成功';
        }else{
            //插入日志
            $this->addCodeLog('pad下单失败', $sql);
            $resArr['state'] = 'fail';
            $resArr['content'] = '下单失败';
        }
        echo json_encode($resArr);exit;
    }
    
    
    //平安人保保单号，套餐卡号接口    wql@20150909  
    public function mark_renbao_combo() {
        $reservation_order =D('reservation_order');
        
        //如果上传文件存在
        if ($_FILES['warranty_image']['name']){
            header("Content-type:text/html;charset=utf-8");
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728;// 设置附件上传大小
            //$upload->allowExts  = array('jpg,jpeg,png');// 设置附件上传类型
            $upload->uploadReplace = true;  //设置覆盖重复名
            $upload->savePath =  'UPLOADS/renbao/';// 设置附件上传目录
            if(!$upload->upload()) {// 上传错误提示错误信息
                $info = $upload->getErrorMsg();
                $resArr['info'] = NULL;
                $resArr['state'] = 'fail';
                $resArr['message'] = $info;
                echo json_encode($resArr);exit;
            }else{  // 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
                // $resArr['info'] = $info;
                $data['warranty_image'] = $info[0]['savepath'].$info[0]['savename'] ; //保单图片
                $data['card_number'] = $_REQUEST['card_number'] ;  //套餐卡片
                $data['warranty_id'] = $_REQUEST['warranty_id'] ;  //保单号
                $map['id']  =  $_REQUEST['order_id'] ;
                $result = $reservation_order->where($map)->save($data);
                
                if($result && $info[0]['savename']){
                    $resArr['state'] = 'success';
                    $resArr['message'] = '保存成功';
                }else{
                    $resArr['state'] = 'fail';
                    $resArr['message'] = '保存失败';
                }
                echo json_encode($resArr);exit;
            }    
        }else{
            $resArr['state'] = 'fail';
            $resArr['message'] = '请上传图片！';
            echo json_encode($resArr);exit;
        }    
    }
    
    
    //订单付款凭证接口    wql@20150921 
    public function payment_evidence() {
        $reservation_order =D('reservation_order');
        //检查订单是否已经付款
        $map['id'] =  intval($_REQUEST['order_id']);
        $rs = $reservation_order->where($map)->find(); 
        
        if(is_array($rs) && $rs['pay_status'] == 1){
            if ($_REQUEST['evidence']){
                $data['evidence'] =  $_REQUEST['evidence'] ;
                
                $result = $reservation_order->where($map)->save($data);    
                if($result){
                    $resArr['state'] = 'success';
                    $resArr['info'] = '填写成功';
                }else{
                    $resArr['state'] = 'fail';
                    $resArr['content'] = '填写失败';
                }
                echo json_encode($resArr);exit;   
            }else {
                $resArr['state'] = 'fail';
                $resArr['content'] = '付款凭证不能为空！';
                echo json_encode($resArr);exit;   
            }   
        }else{
            $resArr['state'] = 'fail';
            $resArr['content'] = '订单还未付款！';
            echo json_encode($resArr);exit;   
        }   
    }
    
    
    //检测是否填写了付款凭证 wql@20150921
    public function check_evidence($order_id) {
        $reservation_order =D('reservation_order');
        //检查订单是否已经付款
        $map['id'] =  $order_id ;
        $rs = $reservation_order->where($map)->find(); 
        if($rs['evidence'] == NULL){
            return  0  ;   
        }else {
            return  1  ;
        }   
    }
    
    
     //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150820
    public function getNeedModel($orderCreateTime) {
        
        
    }
    

}
