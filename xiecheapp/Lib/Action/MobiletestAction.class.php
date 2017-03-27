<?php
//订单
class MobiletestAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->ShopModel = D('shop');
		$this->OrderModel = D('order');
		$this->CouponModel = D('Coupon');
		$this->FsModel = D('fs');//4S店
		$this->CarseriesModel = D('carseries');//车系表
		$this->CarbandModel = D('carbrand');//品牌绑定
		$this->RegionModel = D('Region');
		$this->MemberModel = D('member');//验证码
		$this->model_sms = D('Sms');//手机短信
		$this->PadataModel = D('Padatatest');//测试接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->ServiceitemModel = D('Serviceitem');
		$this->CommentModel = D('Comment');
		$this->MembercouponModel = D('membercoupon');
	}
	/*
		@author:chf
		@function:绑定
		@time:2014-03-18
	*/
	function login_verify(){
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
		if($Paweixin){
			$Paweixin_id = $Paweixin['id'];
		}else{
		
		}
		
		$this->assign('Paweixin_id',$Paweixin_id);
		$this->display();
	}

	/*
		@author:chf
		@function:解绑
		@time:2014-03-18
	*/
	function cancel_verify(){
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
		$old_weixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2','status'=>'2'))->find();
		
		$this->assign('old_weixin',$old_weixin);
		$this->display();
	}

	/*
		@author:chf
		@function:解绑微信号操作
		@time:2014-03-18
	*/
	function cancel_weixin(){
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
		$mobile = $_REQUEST['mobile'];
		
		$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2','status'=>'2','mobile'=>$mobile))->delete();
		$_SESSION['xc_id'] ="";
		echo "1";
	}

	/*
		@author:chf
		@function:显示WAPDEMO店铺搜索页
		@time:2014-01-22
	*/
	function index(){
		if($_REQUEST['fs_Name']){
			$this->assign('fs_Name',$_REQUEST['fs_Name']);
		}
		
		$this->assign('time',$_REQUEST['time']);
		$model_carseries = D('Carseries');
        $shop_city = empty($_REQUEST['city_id'])?'3306':$_REQUEST['city_id'];
        $shop_area = isset($_REQUEST['area_id'])?$_REQUEST['area_id']:0;
        $series_id = isset($_REQUEST['search'])?$_REQUEST['search']:0;
		$page_num = $_REQUEST['page_num'];
		if($page_num == '1'){
			$page_num == '1';
		}else{
			$page_num++;
		}

		
		if($_REQUEST['pa_id']){
			
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
	
		
		$this->assign('pa_id',$_SESSION['xc_id']);
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
		//$map_shop['shop_class'] = 1;
		//此处修改成选择FSID来匹配数据库和IOS安卓不一样的地方
        if ($_REQUEST['fs_id']){
            $map_fs['fsid'] = $_REQUEST['fs_id'];
            $shop_fs = $model_shop_fs_relation->where($map_fs)->select();
            $shop_id_arr = array();
            if ($shop_fs){
                foreach ($shop_fs as $s_k=>$s_v){
                    $shop_id_arr[] = $s_v['shopid'];
                }
            }
            $shop_id_str = implode(',',$shop_id_arr);
            $map_shop['id'] = array('in',$shop_id_str);
            $this->assign('fs_id',$map_fs['fsid']);
        }
		//此处修改成选择FSID来匹配数据库和IOS安卓不一样的地方
        $model_region = D('Region');
        if ($shop_area > 0){
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
		$page_size = 10;
		
        if ($_REQUEST['order'] == 'distance' and $_REQUEST['lat'] and $_REQUEST['long']){
		
            $shops = $this->ShopModel->where($map_shop)->order("comment_rate DESC,have_coupon DESC,shop_class ASC")->select();
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
        }else{
            // 导入分页类
            import("ORG.Util.Page");
            $count = $this->ShopModel->where($map_shop)->count();
            // 实例化分页类

            $p = new Page($count, $page_size);

			$page = $p->show_app();
			$shop_info = $this->ShopModel->where($map_shop)->order("comment_rate DESC,have_coupon DESC,shop_class ASC")->limit($p->firstRow.','.$p->listRows)->select();
			if( $shop_info) {	
				foreach($shop_info as $key=>$val) {
					$shop_maps = $val['shop_maps'];
					$shop_maps_arr = explode(',',$shop_maps);
					$shop_info[$key]['distance'] = $this->GetDistance($_REQUEST['lat'],$_REQUEST['long'],$shop_maps_arr[1],$shop_maps_arr[0]);
					
				}
			}
            $p_count = ceil($count/$page_size);
			$this->assign('allnum',$p_count);
      }		
       
        if ($shop_info){
            $workhours_sale = 0;
            $product_sale = 0;
            foreach ($shop_info as $k=>$v){
                //优惠券数量
               $map_coupon['shop_id'] = $v['id'];
               $map_coupon['show_s_time'] = array('lt',time());
               $map_coupon['show_e_time'] = array('gt',time());
               $map_coupon['is_delete'] = 0;
               $coupon = $this->CouponModel->where($map_coupon)->select();
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
               if ( $v['shop_class']== 1 && file_exists('./UPLOADS/Shop/100/'.$v['id'].'.jpg')){
	               $v['logo'] = WEB_ROOT.'/UPLOADS/Shop/100/'.$v['id'].'.jpg';
	           }else {
	               $shop_id = $v['id'];
	               $map_sfr['shopid'] = $shop_id;
	               $shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
	               $fsid = $shop_fs_relation['fsid'];
	               $model_fs = D('FS');
	               $map_f['fsid'] = $fsid;
	               $fs = $this->FsModel->where($map_f)->find();
	               if($fs['versionid']){
	                   $fsid = $fsid.'_'.$fs['versionid'];
	               }
	               $v['logo'] = WEB_ROOT.'/UPLOADS/Brand/100/'.$fsid.'.jpg';
	           }
               $area_info = $this->RegionModel->find($v['shop_area']);
               $show_title = $area_info['region_name'].' '.$v['shop_address'];
			   
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

               if ($product_sale == '0'){
                   $product_sale = 'none';
               }else{
                   $product_sale = ($product_sale*10);
               }
               if ($workhours_sale  == '0'){
                   $workhours_sale = 'none';
               }else{
                   $workhours_sale = ($workhours_sale*10);
               }
				
				$arr_shop_name = explode('-',$v['shop_name']);
				if($arr_shop_name){
					$shop_info[$k]['shop_name1'] = $arr_shop_name[0];
					$shop_info[$k]['shop_name2'] = $arr_shop_name[1];
				}else{
					$shop_info[$k]['shop_name1'] = $v['shop_name'];
				}	

			   $shop_info[$k]['show_title'] = $show_title;
			   $shop_info[$k]['shop_id'] = $v['id'];
			   $shop_info[$k]['shop_address'] = $v['shop_address'];
			   $shop_info[$k]['shop_phone'] = $v['shop_phone'];
			   $shop_info[$k]['shop_maps'] = $v['shop_maps'];
			   $shop_info[$k]['logo'] = $v['logo'];
			   $shop_info[$k]['region_name'] = $area_info['region_name'];
			   $shop_info[$k]['comment_rate'] = $v['comment_rate'];
			   $shop_info[$k]['comment_number'] = $v['comment_number'];
			   $shop_info[$k]['shop_class'] = $v['shop_class'];
			   $shop_info[$k]['product_sale'] = $product_sale;
			   $shop_info[$k]['workhours_sale'] = $workhours_sale;
			   $shop_info[$k]['distance'] = round($shop_info[$k]['distance']/1000,2);
			   $shop_info[$k]['have_coupon1'] = $have_coupon1;
			   $shop_info[$k]['have_coupon2'] = $have_coupon2;
            }
        }
		//选择车型取得数据(XX需求呵呵)
		$FSdata = $this->FsModel->select();
		foreach($FSdata as $k=>$v){
			$Carseries = $this->CarseriesModel->where(array('fsid'=>$v['fsid']))->find();
			$Carband = $this->CarbandModel->where(array('brand_id'=>$Carseries['brand_id']))->find();
			$FSdata[$k]['word'] = $Carband['word']; 
		}
		$this->assign('FSdata',$FSdata);
		$this->assign('shop_info',$shop_info);
		$this->assign('order_type',$_REQUEST['order']);
		$this->assign('long',$_REQUEST['long']);
		$this->assign('lat',$_REQUEST['lat']);
		$this->assign('page',$page);
		$this->display();
	}
	/*
		@author:chf
		@function:显示店铺详情页
		@time:2014-01-22
	*/
	public function shop_detail(){
		$timesales_count = 0;
		$model_shop = D('Shop');
		$model_timesale = D('Timesale');
		$model_shop_fs_relation = D('Shop_fs_relation');
		$model_timesaleversion = D('Timesaleversion');
		$shop_id = isset($_REQUEST['shop_id'])?$_REQUEST['shop_id']:86;
		$this->assign('shop_id',$shop_id);
		if ($shop_id) {
			$shop_detail = $model_shop->find($shop_id);
			
			$map_sfr['shopid'] = $shop_id;
			$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
			$fsid = $shop_fs_relation['fsid'];
			$model_fs = D('FS');
			$map_f['fsid'] = $fsid;
			$fs = $model_fs->where($map_f)->find();
			$shop_detail['fsname'] = $fs['fsname'];
			$arr_shop_name = explode('-',$shop_detail['shop_name']);
			if($arr_shop_name){
				$shop_detail['shop_name1'] = $arr_shop_name[0];
				$shop_detail['shop_name2'] = $arr_shop_name[1];
			}else{
				$shop_detail['shop_name1'] = $v['shop_name'];
			}
			if ($shop_detail){
				if ($shop_detail['versionid']){
					$shop_detail['id'] = $shop_detail['id'].'_'.$shop_detail['versionid'];
			}
			if ($shop_detail['shop_class']== 1 && file_exists('./UPLOADS/Shop/100/'.$shop_detail['id'].'.jpg')){
				$shop_detail['logo'] = WEB_ROOT.'/UPLOADS/Shop/280/'.$shop_detail['id'].'.jpg';
			}else {

				if($fs['versionid']){
					$fsid = $fsid.'_'.$fs['versionid'];
				}
					$shop_detail['logo'] = WEB_ROOT.'/UPLOADS/Brand/130/'.$fsid.'.jpg';
			}
			$model_shop_fs_relation = D('Shop_fs_relation');
			$model_comment = D('Comment');
			$map_c['shop_id'] = $shop_id;
			$map_c['comment'] = array("neq",'');
			$last_comment = $model_comment->where($map_c)->order("create_time DESC")->find();

			//优惠券
			$model_coupon = D('Coupon');
			$map_coupon['shop_id'] = $shop_id;
			$map_coupon['is_delete'] = 0;
			$map_coupon1 = $map_coupon2 = $map_coupon;
			$map_coupon1['coupon_type'] = 1;
			$map_coupon2['coupon_type'] = 2;
			$coupons = $model_coupon->where($map_coupon)->order("id DESC")->select();
			//print_r($coupons);
			$coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->find();
			$coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->find();
			$coupon_count1 = 0;//现金券数量
			$coupon_count2 = 0;//团购券数量
			foreach($coupons as $k=>$v){
				$coupons[$k]['coupon_amount'] = round($v['coupon_amount']);
			}


			$this->assign('coupons',$coupons);
			$map['shop_id'] = $shop_detail['id'];
			$map['status'] = 1;
			$timesales = $model_timesale->where($map)->select();
			
				if ($timesales){
					$week_str = array(
						"1"=>"周一",
						"2"=>"周二",
						"3"=>"周三",
						"4"=>"周四",
						"5"=>"周五",
						"6"=>"周六",
						"7"=>"周日"
					);

					foreach ($timesales as $kk=>$vv){
						$map_tv['timesale_id'] = $vv['id'];
						$map_tv['status'] = 1;
						$map_tv['s_time'] = array('lt',time());
						$map_tv['e_time'] = array('gt',time());
						$timesaleversion = $model_timesaleversion->where($map_tv)->select();
						//眼乌珠瞎掉模式开启
						$arr_week = explode(',',$vv['week']);
						for($a=0;$a<=count($arr_week);$a++){
							if($arr_week[$a] === '0'){
								$arr_week[$a] = '7';
							}
						}
						for($a=1;$a<=7;$a++){
							if(in_array($a,$arr_week) || in_array('0',$arr_week)){
								$arr_week_res[$a]['week'] = $week_str[$a];
								$arr_week_res[$a]['css'] = '1';
							}else{
								$arr_week_res[$a]['week'] = $week_str[$a];
								$arr_week_res[$a]['css'] = '0';
							}
						}
						
						//眼乌珠瞎掉模式关闭
						if ($timesaleversion){
							foreach ($timesaleversion as $_k=>$_v){
								if (($_v['e_time']<time()+3600*48 and time()>strtotime(date("Y-m-d")." 16:00:00")) || ($_v['e_time']<time()+3600*24) || $_v['s_time']>(time()+24*3600*15) ){
									continue;
								}
								$timesales_count++;
								$timesales[$kk]['timesales_count'] = $timesales_count;
								$timesales[$kk]['week'] = $arr_week_res;
								$timesales[$kk]['count'] = $timesales_count+1;
								$timesales[$kk]['id'] = $_v['id'];//需要的是timesaleversion_id
								$timesales[$kk]['begin_time'] = $vv['begin_time'];
								$timesales[$kk]['end_time'] = $vv['end_time'];
								//$timesaleversion[$_k]['workhours_sale'] = $_v['end_time'];
								//$product_sale = $_v['product_sale'];
								$timesales[$kk]['workhours_sale'] = $_v['workhours_sale']*10;
								
							}
						}else {
							unset($timesales[$kk]);
						}
					}
				}
			}
		}
		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");

		$com['shop_id'] = $_REQUEST ['shop_id'];
		 // 计算总数
		$count = $model_comment->where($com)->count();
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 5; // 设置每页记录数

		$p = new AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p->firstRow . "," . $p->listRows;

		$page = $p->show_app(); // 产生分页信息，AJAX的连接在此处生成

		$comment = $model_comment->where($com)->order("create_time DESC")->limit($limit_value)->select();

		if ($comment){
			foreach ($comment as $key=>$val){
				//查询对应改评论里的对应汽车类型 get_car_info
				$order = $model_order->where(array('id'=>$val['order_id']))->find();
				$comment[$key]['car'] = $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id'],$type='1');
				$comment[$key]['brand_id'] = $order['brand_id'];
				$comment[$key]['series_id'] = $order['series_id'];
				$comment[$key]['model_id'] = $order['model_id'];
				if (!$val['comment']){
					$comment[$key]['comment'] = "此用户没有填写评价内容";
				}

				$map_cr['comment_id'] = $val['id'];
				if (!$val['user_name']){
				   $memberinfo = $model_member->find($val['uid']);
				   $comment[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
				}
				$commentreply = $model_commentreply->where($map_cr)->order("create_time DESC")->select();
				$comment[$key]['commentreply'] = $commentreply;

				//服务项目
				$order_info = $model_order->find($val['order_id']);
				$service_map = array();
				$service_map['id'] = array('in',$order_info['service_ids']);
				$serviceitem = $model_serviceitem->where($service_map)->select();
				$comment[$key]['serviceitem'] = $serviceitem;
				
				//服务顾问
				if($val['servicemember_id'] != 0) {
					$servicemember = $this->Servicemembermodel->find($val['servicemember_id']);
					$comment[$key]['servicemember_name'] = $servicemember['name'];
				}
			}
		}
		
		if(is_array($comment)){
			$this->assign('comment',$comment);
			$this->assign('page', $page);
			
		}
			
		$this->assign('shop_detail',$shop_detail);
		$this->assign('timesales',$timesales);
		$this->assign('timesaleversion',$timesaleversion);
		$this->display();
	}



	/*
		@author:chf
		@function:下单选择保养页
		@time:2014-01-25
	*/
	function order_one(){
		$shop_id = $_REQUEST['shop_id'];
        $model_serviceitem = D('serviceitem');
		$timesaleversion_id = $_REQUEST['timesaleversion_id'];
        $map['service_item_id'] = 0;
        $serviceitem = $model_serviceitem->where($map)->select();
        if ($serviceitem){
            foreach ($serviceitem as $k=>$v){
                $map_s['service_item_id'] = $v['id'];
                $service = $model_serviceitem->where($map_s)->select();
                if ($service){
                    foreach ($service as $_k=>$_v){
						$serviceitem[$k]['son_service'] = $service;
                       
                    }
                }
               
            }
        }
       
		//print_r($serviceitem);
		$this->assign("serviceitem",$serviceitem);
		$this->assign("shop_id",$shop_id);
		$this->assign("timesaleversion_id",$timesaleversion_id);
		$this->assign("timesaleversion_id",$_REQUEST['timesaleversion_id']);
		
		$this->assign("workhours_sale",$_REQUEST['workhours_sale']);
		$this->display();
	
	}

	/*
	 *	@author:chf
	 *	@function:订单下定页面(判断下单日期)
	 *	@time:2013-3-11
	 *
	 */
	public function order_two(){
		$shop_id = $_REQUEST['shop_id'];
		$selectedFinal = $_REQUEST['selectedFinal'];
	    $timesaleversion_id = isset($_REQUEST['timesaleversion_id'])?$_REQUEST['timesaleversion_id']:0;//保养项目ID
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
			
			$result['sale_check'] = $sale_check;
			$result['begin_time'] = $list_timesale['begin_time'];
			$result['end_time'] = $list_timesale['end_time'];
			$result['min'] = $min;
			$result['max'] = $max;

    		//$xml_content .= "<shop_name>".$shopinfo['shop_name']."</shop_name><min>".$min."</min><max>".$max."</max><sale_check>".$sale_check."</sale_check><begin_time>".$list_timesale['begin_time']."</begin_time><end_time>".$list_timesale['end_time']."</end_time><product_sale>".$list_timesale['product_sale']."</product_sale><workhours_sale>".$list_timesale['workhours_sale']."</workhours_sale>";
		}
		
		$result = json_encode($result);
	
		$this->assign('result',$result);
		$this->assign('shop_id',$shop_id);
		$this->assign('selectedFinal',$selectedFinal);
		$this->assign('timesaleversion_id',$timesaleversion_id);
		$this->assign("workhours_sale",$_REQUEST['workhours_sale']);
		$this->display();
	}


	/*
		@author:chf
		@function:下单选择保养页
		@time:2014-01-25
	*/
	function order_three(){
		$shop_id = $_REQUEST['shop_id'];
		$order_date = $_REQUEST['order_date'];
		$selectedFinal = $_REQUEST['selectedFinal'];
		$timesaleversion_id = $_REQUEST['timesaleversion_id'];
		
		//平安有手机号进来验证过的我们这就不用ORDER_THREE页面不用验证 <-次哦JB蛋的鸟平安逻辑 *********************************************
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
		
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
		if(!$Paweixin){
			$id = $this->PaweixinModel->add(array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time()));
			$Paweixin_id = $id;
			$weixin_status = '1';//要验证的状态
		}else{
			$old_weixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2','status'=>'2'))->find();
			$weixin_status = $Paweixin['status'];//要验证的状态
			$Paweixin_id = $Paweixin['id'];
		}
		//平安有手机号进来验证过的我们这就不用ORDER_THREE页面不用验证 <-次哦JB蛋的鸟平安逻辑 *********************************************
		$time_mn = $_REQUEST['time_mn'];//时间列2012-02-08
		$hour = $_REQUEST['zero'];//小时列9
		if(!$_REQUEST['half']){
			$half = "00";//分30
		}else{
			$half = $_REQUEST['half'];//分30
		}
		
		$order_time = strtotime($time_mn." ".$hour.":".$half);
		$this->assign('hour',$half);
		$this->assign('shop_id',$shop_id);
		$this->assign('order_time',$order_time);//预约时间
		$this->assign('selectedFinal',$selectedFinal);
		$this->assign('timesaleversion_id',$timesaleversion_id);
		$this->assign('pa_id',$_SESSION['xc_id']);
		$this->assign('Paweixin',$Paweixin);
		$this->assign('Paweixin_id',$Paweixin_id);
		$this->assign("workhours_sale",$_REQUEST['workhours_sale']);
		$this->assign('weixin_status',$weixin_status);
		$this->assign('old_weixin',$old_weixin);
		$this->display();
	}
	  
	/*
		@author:chf
		@function:发送验证码短信
		@time:2014-02-19
	*/
	function SendSms(){
		$_SESSION['pa_mobile'] = $mobile = $_REQUEST['mobile'];
		$code = rand(13275,96587);
		$_SESSION['pa_code'] = $code;//平安验证码
		/*
		$sms = array(
			'phones'=>$mobile,
			'content'=>'您的验证码：'.$code,
		);
		
		$this->curl_sms($sms);
		*/
		// dingjb 2015-09-29 13:05:08 切换到云通讯
		$sms = array(
			'phones'  => $mobile,
			'content' => array(
				$code
			)
		);
		
		$this->curl_sms($sms, null, 4, '37656');

		$sms['sendtime'] = time();
		$this->model_sms->data($sms)->add();
		echo "1";
	}

	//手机码验证
	function CheckCode(){
		$mobile = $_REQUEST['mobile'];
		$truename = $_REQUEST['truename'];
		
		$code = $_REQUEST['code'];
		$Paweixin_id = $_REQUEST['Paweixin_id'];
		
		if($_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile){

			$pa_count = $this->PaweixinModel->where(array('id'=>$Paweixin_id,'mobile'=>$mobile,'type'=>2))->count();
			if($pa_count > 0 ){
				echo "3";
			}
			$Pw_Arr = $this->PaweixinModel->where(array('id'=>$Paweixin_id))->find();
			//查询对应微信主键ID号
			if($Pw_Arr){
				$member = $this->MemberModel->where(array('mobile'=>$mobile))->find();
				$this->CheckMember($member,$mobile,$Paweixin_id,$code,'2');
			}
			echo "1";
		}else{
			echo "2";
		}
	}


	//手机码验证
	function CheckCode2(){
		$mobile = $_REQUEST['mobile'];
		$truename = $_REQUEST['truename'];
		$code = $_REQUEST['code'];
		$Paweixin_id = $_REQUEST['Paweixin_id'];
		if($_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile){

			$pa_count = $this->PaweixinModel->where(array('id'=>$Paweixin_id,'mobile'=>$mobile,'type'=>'2'))->count();
			if($pa_count > 0 ){//存在跳出手机号已注册
				echo "3";
			}else{

				$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
				$weixincount = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->count();
				if($weixincount == 0){
					$Paweixin_id = $this->PaweixinModel->add(array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time()));
				}
				$member = $this->MemberModel->where(array('mobile'=>$mobile))->find();
		
				$this->CheckMember($member,$mobile,$Paweixin_id,$code,'2');
				echo "1";
			}

		}else{
			echo "2"; 
		}
	}

	/*
		@author:ysh
		@function:平安加密
		@time:2014/2/14
	*/
	function postdecrypt($encText){
		import("ORG.Util.MyAes");
		$keyStr = 'UIMN85UXUQC436IM'; //平安给的密钥
		//$plainText = 'this is a string will be AES_Encrypt';  原文
		//$encText =  iconv('UTF-8', 'GBK',$encText) ;
		$aes = new MyAes();
		$aes->set_key($keyStr);
		$aes->require_pkcs5();
		$decString = $aes->encrypt($encText);//加密
		//$decString = $aes->decrypt($encText);//解密
		//$decString = iconv('UTF-8', 'GBK',$decString);//平安给我们的是GBK的 我们要转成UTF8
		return $decString;
	}

	

	/*
		@author:chf
		@function:记录微信ID,手机绑定UID修改status为2
		@time:2014-02-19
	*/
	function CheckMember($member,$mobile,$Paweixin_id,$code,$type){
		if($member){
			$this->PaweixinModel->where(array('id'=>$Paweixin_id,'type'=>$type))->save(array('mobile'=>$mobile,'uid'=>$member['uid'],'status'=>'2'));
		}else{
			$member_data['mobile'] = $mobile;
			$member_data['password'] = md5($code);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '36';//携车微信
			$uid = $this->MemberModel->data($member_data)->add();

			$this->PaweixinModel->where(array('id'=>$Paweixin_id,'type'=>$type))->save(array('mobile'=>$mobile,'uid'=>$uid,'status'=>'2'));

			/*
			$send_add_user_data = array(
				'phones' => $mobile,
				'content' => '您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$code.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
			*/
			// dingjb 2015-09-29 11:53:11 切换到云通讯
			$send_add_user_data = array(
				'phones'  => $mobile,
				'content' => array(
					$mobile,
					$code
				)
			);
			$this->curl_sms($send_add_user_data, null, 4, '37653');

			$send_add_user_data['sendtime'] = time();
			$this->model_sms->data($send_add_user_data)->add();
		}
	}

	/*
		@@author:chf
		@function:订单下定插入数据
		@time:2014-02-08
	 */
	function save_order(){
		 $model_member = D('Member');
        //根据提交过来的预约时间，做判断(暂时先注销)
		if($_REQUEST['order_time']){
		
			$Paweixin = $this->PaweixinModel->where(array('id'=>$_REQUEST['Paweixin_id'],'status'=>'2'))->find();
			$uid = $data['uid'] = $Paweixin['uid'];
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
    		if ($_REQUEST['workhours_sale']){
    		    $data['workhours_sale'] = ($_REQUEST['workhours_sale']/10);
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
		    if ($_REQUEST['order_time']){
    		    $data['order_time'] = $_REQUEST['order_time'];
    		}
    		$data['create_time'] = time();
		    if ($total_price){
    		    $data['total_price'] = $total_price;
    		}
		    if ($productversion_ids_str){
    		    $data['productversion_ids'] = $productversion_ids_str;
    		}
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
    			$this->success('预约提交成功！',__APP__.'/Mobile/order_list/pa_id/'.$_SESSION['xc_id']);
    		}else {
    			$this->error('预约失败',__APP__.'/Mobile/index');
    		}
		}
		
	}
	
	/*
		@author:ysh
		@function:优惠券主页
		@time:2014-02-12
	*/
	function coupon_list(){
		if($_REQUEST['fs_Name']){
			$this->assign('fs_Name',$_REQUEST['fs_Name']);
		}
        $model_shop = D('Shop');
        $model_series = D('Carseries');
        $model_shop_fs_relation = D('Shop_fs_relation');
        $sql = " 1=1 ";
        if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		
        $model_coupon = D('Coupon');
       //此处修改成选择FSID来匹配数据库和IOS安卓不一样的地方
        if ($_REQUEST['fs_id']){
            $map_fs['fsid'] = $_REQUEST['fs_id'];
            $shop_fs = $model_shop_fs_relation->where($map_fs)->select();
            $shop_id_arr = array();
            if ($shop_fs){
                foreach ($shop_fs as $s_k=>$s_v){
                    $shop_id_arr[] = $s_v['shopid'];
                }
            }
            $shop_id_str = implode(',',$shop_id_arr);
            $map_shop['id'] = array('in',$shop_id_str);
            $this->assign('fs_id',$map_fs['fsid']);
        }
		//此处修改成选择FSID来匹配数据库和IOS安卓不一样的地方

        $shop_info = $model_shop->where($map_shop)->select();
		
		//echo $model_shop->getlastSql();
        $shop_id_arr = array();
        if ($shop_info){
            foreach ($shop_info as $_kk=>$_vv){
                $shop_id_arr[] = $_vv['id'];
            }
            //$map['shop_id'] = array('in',implode(',',$shop_id_arr));

        }
		if($shop_id_arr){
			$sql .= " AND shop_id in (".implode(',',$shop_id_arr).") ";
		}

		$now = strtotime(date("Y-m-d",time()));

        $sql .= " AND show_s_time<='".$now."' ";
        $sql .= " AND show_e_time>'".$now."' ";
        $sql .= " AND is_delete='0' ";

      
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
			$page = $p->show_app();
			
			$couponlist = $model_coupon->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
	
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
            foreach ($couponlist as $k=>$v){
                $couponlist[$k]['coupon_logo'] = WEB_ROOT."/UPLOADS/Coupon/Logo/coupon1_".$v['coupon_logo'];
				$couponlist[$k]['distance'] = round($v['distance']/1000,2);
				$couponlist[$k]['coupon_amount'] = round($v['coupon_amount']);
            }
        }
		//选择车型取得数据(XX需求呵呵)
		$FSdata = $this->FsModel->select();
		foreach($FSdata as $k=>$v){
			$Carseries = $this->CarseriesModel->where(array('fsid'=>$v['fsid']))->find();
			$Carband = $this->CarbandModel->where(array('brand_id'=>$Carseries['brand_id']))->find();
			$FSdata[$k]['word'] = $Carband['word']; 
		}
		$this->assign('FSdata',$FSdata);
		$this->assign('page',$page);
		$this->assign('couponlist',$couponlist);
        $this->display();
    }
	/*
		@author:ysh
		@function:优惠券详细页
		@time:2014-02-12
	*/
	function coupon_detail() {
		$coupon_id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
		$map_c['id'] = $coupon_id;
		
		$coupon = $this->CouponModel->where($map_c)->find();
		$coupon['cost_price'] = round($coupon['cost_price']);
		$coupon['coupon_amount'] = round($coupon['coupon_amount']);
		$coupon['coupon_pic'] = WEB_ROOT."/UPLOADS/Coupon/Logo/".$coupon['coupon_pic'];

		$shop_info = $this->ShopModel->find($coupon['shop_id']);

		$area_info = $this->RegionModel->find($shop_info['shop_area']);
        $shop_address = $area_info['region_name'].' '.$shop_info['shop_address'];
		
		if ($coupon['coupon_des']){
			$coupon['coupon_des'] = preg_replace( "#((height|width)=(\'|\")(\d+)(\'|\"))#" , "" , $coupon['coupon_des'] );
            $search_str = '<img ';
		    $replace_str = '<img onload="if(this.width>screen.width)this.width=(screen.width-20)" ';
			$coupon['coupon_des'] = str_replace($search_str, $replace_str, $coupon['coupon_des']);
		//if($_REQUEST['ios']) {
		//$coupon['coupon_des'] = str_replace("onload=\"if(this.width>screen.width)this.width=(screen.width-20)\"", "width='280'", $coupon['coupon_des']);
		//			}
		}
		$this->assign('shop_name',$shop_info['shop_name']);
		$this->assign('shop_address',$shop_address);
		$this->assign('coupon',$coupon);
		$this->display();
	}





	/*
		@author:chf
		@function:AJAX分页调用函数()
		@time:2013-12-05
	*/
	function get_comment(){
		$model_comment = D('Comment');
		$model_commentreply = D('Commentreply');
		$model_member = D('Member');
		$model_order = D("Order");
		$model_serviceitem = D("Serviceitem");
	

		$map_c['shop_id'] = $_GET['shop_id'];
		$count = $model_comment->where($map_c)->count();

		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类

		$limitRows = 5; // 设置每页记录数

		$p = new AjaxPage($count, $limitRows,"get_comment"); //第三个参数是你需要调用换页的ajax函数名

		$limit_value = $p->firstRow . "," . $p->listRows;

		$page = $p->show_app(); // 产生分页信息，AJAX的连接在此处生成

		$comment = $model_comment->where($map_c)->order("create_time DESC")->limit($limit_value)->select();
		
		if ($comment){
			$str ="<ul class='ui-listview ui-listview-inset ui-corner-all ui-shadow' data-role='listview' data-inset='true'>";
			foreach ($comment as $key=>$val){
				//查询对应改评论里的对应汽车类型 get_car_info
				$order = $model_order->where(array('id'=>$val['order_id']))->find();
				$car= $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id']);

				$map_cr['comment_id'] = $val['id'];
				if (!$val['user_name']){
				   $memberinfo = $model_member->find($val['uid']);
				   $comment[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
				}
				$commentreply = $model_commentreply->where($map_cr)->order("create_time DESC")->select();
				$comment[$key]['commentreply'] = $commentreply;

				//服务项目
				$order_info = $model_order->find($val['order_id']);
				$service_map = array();
				$service_map['id'] = array('in',$order_info['service_ids']);
				$serviceitem = $model_serviceitem->where($service_map)->select();
				$comment[$key]['serviceitem'] = $serviceitem;
				$ajaxservcename = "";
				if($serviceitem){
					foreach($serviceitem as $sk=>$sv){
						$ajaxservcename.= "<dd>".$sv['name']."</dd>";
					}
				}
				//服务顾问
				if($val['servicemember_id']) {
					$servicemember_name = "";
					$servicemember = $this->Servicemembermodel->find($val['servicemember_id']);
					$comment[$key]['servicemember_name'] = $servicemember['name'];
					
					$comment[$key]['servicemember_name'] = "<dt>服务顾问:</dt><dd>".$servicemember['name']."</dd></dl>";
					
				}
				if($val['comment_type']=='1'){
					$comment_type = '好评';
					$comment_width = '100%';
				}elseif($val['comment_type']=='2'){
					$comment_type = '中评';
					$comment_width = '60%';
				}else{
					$comment_type = '差评';
					$comment_width = '30%';
				}
				$reply="";
				foreach($commentreply as $ck=>$cv){
					$reply.="<li><h4>商家回复：".$cv['operator_name'].(date('Y-m-d H:i:s',$cv['create_time']))."</h4><div>".$cv['reply']."</div></li>";
				}

				//author:chfMB的AJAX分页麻痹眼乌珠瞎掉蛋碎的一塌糊涂 麻痹的这次是WAP DEMO错那骂着比额伐晓得唉要改几躺↓
				
				
				$str.="<li class='ui-grid-a single-comment ui-li-static ui-body-inherit ui-first-child' style='margin-bottom: 10px; padding-bottom: 10px;'>";
				$str.="<h3 class='c-name' style='margin: 0px;'>用户:".$comment[$key]['user_name']."</h3> ";
				$str.="<h4 class='c-date' style='font-size: 0.7em; margin: 0px;'>".date('Y-m-d H:i:s',$val['create_time'])."</h4>";
				$str.="<dl class='c-service'><dt>维修车辆：</dt><dd>".$car."</dd><br><dt>服务项目：</dt><dd>".$ajaxservcename."</dd></dl>";
				$str.="<div class='customer-rating'><div class='rating-progress'><span class='good' style='width:".$comment_width."'></span></div>";
				$str.="<span class='rating-class'>".$comment_type."</span></div>";
				$str.="<div class='commment-body'>".$comment[$key]['comment']."</div><ul class='comment-reply'>";
				$str.=$reply;
				$str.="</ul></li>";
			}
			$str.="</ul><div style='clear: both;'></div><div id='pagination'>".$page."</div>";
			echo $str;
		}
	}

	/*
		@author:chf
		@function:显示购买优惠券页
		@time:2014-02-20
	*/
	function coupon_order(){
		$data['id'] = $_REQUEST['coupon_id'];
		$coupon = $this->CouponModel->where($data)->find();
		//平安有手机号进来验证过的我们这就不用coupon_order页面不用验证 <-次哦JB蛋的鸟平安逻辑 *********************************************
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>2))->order('id desc')->find();
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
		if(!$Paweixin){
			$id = $this->PaweixinModel->add(array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time()));
			$arr['Paweixin_id'] = $id;
			$arr['weixin_status'] = '1';//要验证的状态
		}else{
			$old_weixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2','status'=>'2'))->find();
			$arr['weixin_status'] = $Paweixin['status'];//要验证的状态
			$arr['Paweixin_id'] = $Paweixin['id'];
		}
		//平安有手机号进来验证过的我们这就不用ORDER_THREE页面不用验证 <-次哦JB蛋的鸟平安逻辑 *********************************************
		$this->assign('arr',$arr);
		$this->assign('old_weixin',$old_weixin);
		$this->assign('Paweixin',$Paweixin);
		$this->assign('coupon',$coupon);
		$this->display();
	}

	/*
		@author:chf
		@function:保存优惠券订单并且跳转去支付
		@time:2014-02-20
	*/
	 function savecoupon(){
	    $coupon_id = isset($_REQUEST['coupon_id'])?$_REQUEST['coupon_id']:0;
	    $mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:0;
	    $number = isset($_REQUEST['number'])?$_REQUEST['number']:1;
	    $model_coupon = D('Coupon');
        $model_membercoupon = D('Membercoupon');
        if ($_REQUEST['Paweixin_id']){
            $Paweixin_id = $_REQUEST['Paweixin_id'];
			$Paweixin = $this->PaweixinModel->where(array('id'=>$_REQUEST['Paweixin_id'],'status'=>'2'))->find();
			$uid = $data['uid'] = $Paweixin['uid'];
            if ($Paweixin){
                $uid = $memberinfo['uid'];
                if ($number>0 and is_numeric($number)){
                    $map_c['id'] = $coupon_id;
                    $map_c['end_time'] = array('gt',time());
                	$coupon = $model_coupon->where($map_c)->find();
					$model_shop = D('shop');
					$shop = $model_shop->where(array('id'=>$coupon['shop_id']))->find();
                    if ($coupon){
                        $membercoupon_id_str = '';
                        for ($ii = 0; $ii < $number; $ii++) {
                	        $data['coupon_name'] = $coupon['coupon_name'];
                	        $data['coupon_id'] = $coupon_id;
                	        $data['coupon_type'] = $coupon['coupon_type'];
                	        $data['mobile'] = $mobile;
                	        $data['create_time'] = time();
                	        $data['end_time'] = $coupon['end_time'];
                	        $data['shop_id'] = $coupon['shop_id'];
                	        $data['start_time'] = $coupon['start_time'];
							if($coupon['coupon_type']=='1'){
								$data['ratio'] = $shop['cash_rebate'];//现金券比例
							}else{
								$data['ratio'] = $shop['group_rebate'];
							}
							$data['pa'] = 3;//自己wap

                	        if ($membercoupon_id = $model_membercoupon->add($data)){
                	            $membercoupon_id_arr[] = $membercoupon_id;
                	        }
                        }
                        $membercoupon_id_str = implode(',',$membercoupon_id_arr);
						
						$this->success('购买成功!','/txwappay/payRequest.php?membercoupon_id='.$membercoupon_id_str);
            	    }else{
						$this->success('操作失败!',__APP__.'/Mobile/index');
        	        }
                }else{
					$this->success('操作失败!',__APP__.'/Mobile/index');
    	        }
            }else{
                $this->success('无微信ID!',__APP__.'/Mobile/index');
            }
        }else{
            $this->success('无微信ID!',__APP__.'/Mobile/index');
        }
	}

	function ajax_distance() {		
		$distance = array(
			'long' => $_REQUEST['long'],
			'lat' => $_REQUEST['lat'],
		);
		$long_lat = $distance;
		global $long_lat;
		return $distance;
	}
	/*
		@author:ysh
		@function:显示购买的优惠券
		@time:2014/1/22
	*/
	function my_coupon() {
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$Padata = $this->PadataModel->where(array('id'=>$_SESSION['xc_id']))->find();
		$Pawx = $this->PaweixinModel->where(array('wx_id'=>$Padata['FromUserName']))->find();

		// 导入分页类
		import("ORG.Util.Page");
		$count = $this->MembercouponModel->where(array('uid'=>$Pawx['uid']))->count();
		// 实例化分页类
		$p = new Page($count, '10');

		$page = $p->show_app();
		$arr['uid'] = $Pawx['uid'];
		if($_REQUEST['coupon_type']){
			$arr['coupon_type'] = $_REQUEST['coupon_type'];
		}
		$Mycoupon = $this->MembercouponModel->where($arr)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($Mycoupon){
			foreach($Mycoupon as $k=>$v){
				$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				 $Mycoupon[$k]['coupon_pic'] = $coupon['coupon_pic'];
				 $Mycoupon[$k]['cost_price'] = $coupon['cost_price'];//原价
				 $Mycoupon[$k]['coupon_amount'] = $coupon['coupon_amount'];//团购价
			}
		}
		
		$this->assign('Mycoupon',$Mycoupon);
		$this->assign('page',$page);
		$this->display();
	} 
	
	/*
		@author:ysh
		@function:用户中心订单列表
		@time:2014/2/20
	*/
	function order_list() {
		$order_state_arr = C('ORDER_STATE');
		/*
		$wx_id = $_GET['wx_id'];
		
		$wx_map['wx_id'] = $wx_id;
		$wx_map['status'] = 2;
		$paweixin = $this->PaweixinModel->where($wx_map)->find();*/
		//测试用先这样改
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		
		$Padata = $this->PadataModel->where(array('id'=>$_SESSION['xc_id']))->find();
		$Pawx = $this->PaweixinModel->where(array('wx_id'=>$Padata['FromUserName']))->find();

		$uid = $Pawx['uid'];
		
		$order_map['uid'] = $uid;
		$order_list = $this->OrderModel->where($order_map)->order("id DESC")->select();
		
		if($order_list) {
			foreach($order_list as $key=>$val) {
                $shopinfo = $this->ShopModel->where($map_s)->find($val['shop_id']);
				$order_list[$key]['shop_name'] = $shopinfo['shop_name'];
				$order_list[$key]['shop_address'] = $shopinfo['shop_address'];
				$order_list[$key]['order_id'] = $this->get_orderid($val['id']);
				$order_list[$key]['order_state_str'] = $order_state_arr[$val['order_state']];
				
				if($val['workhours_sale']>0) {
					$order_list[$key]['workhours_sale'] = ($val['workhours_sale']*10)."折";
				}else {
					$order_list[$key]['workhours_sale'] = "无折扣";
				}
				
				$service_map['id'] = array('in' , $val['service_ids']);
				$service_items = $this->ServiceitemModel->where($service_map)->select();
				if($service_items) {
					$order_list[$key]['service_items'] = $service_items;
				}

				//评论
				$comment_list = $this->CommentModel->where(array('order_id'=>$val['id']))->select();
				if($comment_list) {
					$order_list[$key]['comment_list'] = $comment_list;
				}
			}
		}
		
		$this->assign('uid',$uid);
		$this->assign('order_list',$order_list);
		$this->display();
	}
	

	function add_comment() {
		$comment = $_REQUEST['comment'];
		$order_id = $_REQUEST['order_id'];
		$shop_id = $_REQUEST['shop_id'];
		$uid = $_REQUEST['uid'];
		$comment_type = $_REQUEST['comment_type'];

		$user_info = $this->MemberModel->find($uid);

		$data['uid'] = $uid;
		$data['user_name'] = $user_info['username'];
		$data['shop_id'] = $shop_id;
		$data['order_id'] = $order_id;
		$data['comment'] = $comment;
		$data['comment_type'] = $comment_type;
		$data['create_time'] = time();

		if($this->CommentModel->add($data)){
			$this->OrderModel->where("id=$data[order_id]")->save(array('iscomment'=>1));
			$this->count_good_comment($data['shop_id']);
			echo 1;
		}else {
			echo 0;
		}
		exit();
	}

	function count_good_comment($shop_id){//计算公式= (好评-差评）/总评数
    	$model_comment = D('Comment');
	    if ($shop_id >0){
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
    	       //$comment_rate = ($good_comment_number - $bad_comment_number)/$all_comment_number*100;
    	    	/*新好评率不扣除差评 直接按照好评的百分比来算
				@author:ysh
				@time:2013-12-16
				*/
				$comment_rate = $good_comment_number / $all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_shop = D('Shop');
    	    $data['id'] = $shop_id;
    	    $data['comment_rate'] = $comment_rate;
    	    $data['comment_number'] = $all_comment_number;
    	    $model_shop->where("id=$shop_id")->save($data);
	    }
	}

	function app_download() {
		$this->display();
	}
}