<?php
//订单
class MobileAction extends CommonAction {
	protected $offlinespread;
	protected $mBidorder;
	protected $mRepairprocess;
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
		$this->PadataModel = D('Padatatest');//接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->ServiceitemModel = D('Serviceitem');
		$this->CommentModel = D('Comment');
		$this->MembercouponModel = D('membercoupon');
		import("ORG.Util.hm");
		$_hmt = new hm("25bc1468b91be39c26580a917bfbf673");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('baiduimg',$_hmtPixel);
		$this->offlinespread = D("offlinespread");//地推表
		$this->mBidorder = D("bidorder");	//事故车订单表
		$this->mRepairprocess = D("repairprocess");//事故车进度表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->AnswerModel = D('answer');//用户保险竞价表
		$this->QuestionModel = D('question');//用户保险竞价表
		$this->carservicecode_model = D('carservicecode');//上门保养抵扣券表
		$this->technician_model = D('technician');//技师表

	}
	
	function check_report_weixin(){
		if ( isset($_GET['code']) && !empty($_GET['code'])){
			$appid = "wx43430f4b6f59ed33";
			$secret = "e5f5c13709aa0ae7dad85865768855d6";
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$_GET['code'].'&grant_type=authorization_code';
			$res = file_get_contents($url);
			$res = json_decode($res,true);
			$this->redirect('Mobilecar/carservice',array('wx_id' => @$res['openid']));
		}
	}
	
	
	/*
	 @author:bright
	@function:上门保养检查报告
	@time:2014-08-30
	*/
	function check_report(){
		if ( !empty($_GET['report_id'])) {
			$id = base64_decode($_GET['report_id']);
            $len = strlen($id);
            $id  = substr($id,0,$len-3);
			//$id = str_replace('168','',$id);
			$where = array('id'=>$id);
		}elseif( !empty($_GET['pa_id']) ){
			$pa_id = $_GET['pa_id'];
			$palog = $this->PadataModel->where(array('id'=>$pa_id,'type'=>'2'))->find();
			$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
			$where = array('mobile'=>$Paweixin['mobile']);
		}else{
				$this->error('参数为空',__APP__.'/Mobile/index');
				exit;
		}
		
		$mCheckReport = D('checkreport_total');
		
		$res = $mCheckReport->where($where)->order('create_time desc')->limit(1)->select();
      // echo $mCheckReport->getLastSql();
		$res = $res[0];
		$model = array();
		if ($res) {
			$data = unserialize($res['data']);
            //var_dump($data);
			$model = array(
					'id'=>$res['id'],
					'reservation_id'=>$res['reservation_id'],
					'mobile'=>$res['mobile'],
					'create_time'=>$res['create_time'],
					'car_brand'=>$data['car_brand'].$data['car_model'],
					'mileage'=>$data['mileage']
			);
			if ($data) {
				unset($data['phone'],$data['car_brand'],$data['car_series'],$data['date'],$data['engine_number'],$data['car_model']);
				//数据处理
				if ($res['create_time']<1419321491) {
					$baoyang = array('机油液位','空气滤芯','空调滤芯','雨刮水液面','左前胎胎压','右前胎胎压','左后胎胎压','右后胎胎压','防冻液液面','防冻液冰点值','制动液含水量');
					$yishun = array('前刹车片厚度','后刹车片厚度','前刹车盘厚度','后刹车盘厚度','蓄电池健康度','前胎花纹深度','后胎花纹深度','雨刮片','灯光照明');
					$dipan = array("球头","转向拉杆","平衡杆","连接杆","避震","上下摆臂","半轴");
					$anquan = array("安全带","手制动器","发动机舱管线","电瓶指示灯","机油指示灯","水温指示灯","ABS指示灯","发动机指示灯","安全带指示灯","刹车指示灯","气囊指示灯");
					$this->assign('old',true);
				}else{
					$baoyang = array('机油液位','空气滤芯','空调滤芯','雨刮水液面','左前胎胎压','右前胎胎压','左后胎胎压','右后胎胎压','防冻液液面','防冻液冰点值','制动液含水量');
					$yishun = array('前刹车片厚度','后刹车片厚度','前刹车盘厚度','后刹车盘厚度','蓄电池健康度','前胎花纹深度','后胎花纹深度','雨刮片','灯光照明','点烟器');
					$dipan = array("球头","转向拉杆","变速箱","油底壳螺丝","避震器","上下摆臂","半轴");
					$anquan = array("安全带","手制动器","发动机舱管线","气门室盖垫","方向机油壶","机油指示灯","ABS指示灯","发动机指示灯","刹车指示灯","气囊指示灯");
				}
				
				$baoyangArr = $yishunArr = $dipanArr = $anquanArr = array();
				foreach ($data as $key=>$val){
					
					if (in_array($key,$baoyang)) {
						$baoyangArr[] = array('name'=>$key,'status'=>$val);
						continue;
					}
					if (in_array($key,$yishun)) {
						$yishunArr[] = array('name'=>$key,'status'=>$val);
						continue;
					}
					if (in_array($key,$dipan)) {
						$dipanArr[] = array('name'=>$key,'status'=>$val);
						continue;
					}
					if (in_array($key,$anquan)) {
						$anquanArr[] =array('name'=>$key,'status'=>$val);
						continue;
					}
				}
				$model['by_total'] = count($baoyangArr);
				$model['ys_total'] = count($yishunArr);
				$model['dp_total'] = count($dipanArr);
				$model['aq_total'] = count($anquanArr);
				
				$model['baoyang'] = $baoyangArr;
				$model['yishun'] = $yishunArr;
				$model['dipan'] = $dipanArr;
				$model['anquan'] = $anquanArr;
			}
			
		}else{
            // #### dingjb 2015-09-18 14:00:10 添加非手机端访问限制 ####
            /*
            if (array_key_exists('HTTP_REFERER', $_SERVER) && mb_strpos($_SERVER['HTTP_REFERER'], 'Admin/index.php/')) {
                die('<h3 style="text-align: center">您暂时没有检测报告</h3>');
            }
            */

            if (!$this->is_mobile()) {
                die('<h2 style="margin-top: 50px;text-align: center;">您暂时没有检测报告</h2>');
            }

			$this->error('您暂时没有检测报告',__APP__.'/Mobile/index');
			exit;
		}
		if( $this->_get('pa_id') ){
			$this->assign('pa_id',$this->_get('pa_id'));
		}else{
			$this->assign('report_id',$this->_get('report_id'));
		}
		$this->assign('model',$model);
        //var_dump($model);

		$check_remarks = D("check_remarks");
		$remarkdata['order_id'] = $res['reservation_id'];
		//$remarkdata['order_id'] = 5178;
		$res_remark = $check_remarks->where($remarkdata)->order('create_time desc')->select();

		$remarkcon = $res_remark[0]['remarks'];
		
		$remarkcon = json_decode($remarkcon,true);
		foreach( $remarkcon as $k=>$v){  
		    if( !$v )  {
		        unset( $remarkcon[$k] );
		    }
		}
		$this->assign('remarkcon',$remarkcon);
		$this->display();

	}
	/*
	@author:bright
	@function:上门保养评论
	@time:2014-08-30
	*/
	function reserveorder_comment(){
		$where = array();
		if (!empty($_GET['reservation_id'])) {
			$where = array(
				'id'=>$_GET['reservation_id']
			);
		}else{
			$this->error('参数为空',__APP__.'/Mobile/index');
			exit;
		}
		$mReservationOrder = D('reservation_order');
		$res = $mReservationOrder->field('id')->where($where)->find();
		if (!$res) {
			$this->error('参数为空',__APP__.'/Mobile/index');
			exit;
		}
		$this->assign('reserveorder_id',$res['id']);
		$this->display();
	}
	function addReserveComment(){
		$type = @$_REQUEST['type'];
		$content = @$_REQUEST['content'];
		$reserveorder_id = @$_REQUEST['reserveorder_id'];
		$insert = array(
				'type'=>$type,
				'content'=>$content,
				'reserveorder_id'=>$reserveorder_id,
				'comment_time'=>time()
		);
		$mReserveComment = D('reserveorder_comment');
		$map["reserveorder_id"] =$reserveorder_id;
		$comment_info = $mReserveComment->where($map)->find();
		if($content !='输入您的意见、不少于5个字'&& !empty($content)){
			if(!$comment_info){
				$mReserveComment->add($insert);
		      }
		}

		$this->ajaxReturn('','',1);
	}
	
	/*
		@author:bright
		@function:绑定备份
		@time:2014-03-18
	*/
	function login_verify_test(){
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id'],'type'=>'2'))->order('id desc')->find();
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
		if($Paweixin){
			$Paweixin_id = $Paweixin['id'];
		}
		$this->assign('Paweixin_id',$Paweixin_id);
		$this->display();
	}

	/*
	 * @author bright
	 * @function 用户绑定
	 * @time:2014-08-14
	 */
	function login_verify(){
		//print_r($_REQUEST);
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];
		}
		$hide_invite_code = 0;
		if( !empty($_REQUEST['ic']) && $_REQUEST['ic']==1 ){
			$hide_invite_code = 1;
		}
		$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id']))->order('id desc')->find();
		//$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
		if($palog){
			$Paweixin_id = $palog['FromUserName'];
			$this->assign('Paweixin_id',$Paweixin_id);
			$this->assign('hide_invite_code', $hide_invite_code);
			$this->assign('pa_id',$_SESSION['xc_id']);
			if (isset($_SERVER['HTTP_REFERER'])) {
				$back_url = $_SERVER['HTTP_REFERER'];
			}else{
				$back_url = __APP__.'/Mobile-index';
			}
			$this->assign('back_url',$back_url);
			$this->display();
		}else{
			$this->error('微信参数错误',__APP__.'/Mobile/index');
		}
		
	}
	/*
	 * @author bright
	 * @function 支付宝服务窗绑定
	 * @time:2014-08-14
	 */
	function alipay_bind(){
		//print_r($_REQUEST);
		if($_REQUEST['ali_id']){
			$_SESSION['ali_id'] = $_REQUEST['ali_id'];
		}else{
			$this->error('参数错误',__APP__.'/Mobile/index');
		}
		

		//$mAlipay = D('alipay_fuwuchuang');
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			$back_url = $_SERVER['HTTP_REFERER'];
		}else{
			$back_url = __APP__.'/mobilecar-carservice?ali_id='.$_REQUEST['ali_id'];
		}
		$this->assign('back_url',$back_url);
		$this->assign('ali_id',$_REQUEST['ali_id']);
		$this->display();
		
	}

	function alipay_bind_cancel(){
		if($_REQUEST['ali_id']){
			$_SESSION['ali_id'] = $_REQUEST['ali_id'];
		}
		$mAlipay = D('alipay_fuwuchuang');

		$res = $mAlipay->where(array('FromUserId'=>$_REQUEST['ali_id']))->find();
		$this->assign('mobile',$res['mobile']);
		$this->display();
	}

	/*
		@author:
		@function:解绑服务窗操作
		@time:2014-03-18
	*/
	function cancel_alipay(){
		$mAlipay = D('alipay_fuwuchuang');
		$alidata = $mAlipay->where(array('FromUserId'=>$_SESSION['ali_id'],'status'=>2))->order('id desc')->find();
		$mobile = $_REQUEST['mobile'];
		
		if($alidata){
			$data['mobile'] = '';
			$data['status'] = 1;
			$mAlipay->where(array('FromUserId'=>$_SESSION['ali_id'],'status'=>2,'mobile'=>$mobile))->save($data);
			if($mAlipay){
				echo "1";
			}
		}
		
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
		$this->assign('pa_id',$_REQUEST['pa_id']);
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
		
		$this->PaweixinModel->where(array('mobile'=>$mobile,'type'=>'2'))->delete();
		$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->delete();
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
               }elseif($workhours_sale == '-1') {
					$workhours_sale = 'free';
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
			if(!$Carband){
				unset($FSdata[$k]);
			}else{
				$FSdata[$k]['word'] = $Carband['word'];
			}
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
		$dztype = $_GET['dztype'];//接受大众点评跳来的数值
		if($dztype == '1'){
			$_SESSION['dztype'] = '1';
		}//接受大众点评跳来的数值
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
								if($_v['product_sale'] == '-1'){
									$timesales[$kk]['product_sale'] = '全免';
								}

								if($_v['workhours_sale'] == '-1'){
									$timesales[$kk]['workhours_sale'] = '全免';
								}else{
									$timesales[$kk]['workhours_sale'] = $_v['workhours_sale']*10;
								}
								$product_sale = $_v['product_sale']*10;
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
		$this->assign('product_sale',$product_sale);
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

		if($_SESSION['dztype'] == '1'){
			$weixin_status = '2';//要验证的状态
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
		$code = rand(1000,9999);
		$_SESSION['pa_code'] = $code;//平安验证码
		/*
		$sms = array(
			'phones'=>$mobile,
			'content'=>'您的验证码：'.$code,
		);

		$this->curl_sms($sms,'',5);
		*/
		// dingjb 2015-09-29 13:03:46 切换到云通讯
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

		if( ( $_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile) || ($code == '4006602822') ){

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
		if( ($_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile) || ($code == '4006602822') ){

			//判断是不是大众点评跳转来的开始
			if($_SESSION['dztype'] == '1'){
				$member = $this->MemberModel->where(array('mobile'=>$mobile))->find();
				$this->DzCheckMember($member,$mobile,$code);
				echo "1";
			//判断是不是大众点评跳转来的结束
			}else{
				$pa_res = $this->PaweixinModel->where(array('wx_id'=>$Paweixin_id,'status'=>'2'))->find();
				if( !empty($pa_res['mobile']) && ($pa_res['mobile'] == $mobile)  ){//存在跳出手机号已注册
					echo "3";
				}else{
					$_SESSION['needAddNumber'] = 'yes';
					$palog = $this->PadataModel->where(array('id'=>$_SESSION['xc_id']))->order('id desc')->find();
					$weixincount = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->count();
					if($weixincount == 0){
						$data = array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time(),'mobile'=>$mobile,'bind_time'=>time());
						//添加车型等字段
						if ( !empty($_POST['brand_id']) ) {
							$data['brand_id'] = $_POST['brand_id'];
						}
						if ( !empty($_POST['series_id']) ) {
							$data['series_id'] = $_POST['series_id'];
						}
						if ( !empty($_POST['model_id']) ) {
							$data['model_id'] = $_POST['model_id'];
						}

						$Paweixin_id = $this->PaweixinModel->add($data);
					}else{
						//更新用户填写的数据
						$updateData = array();
						//更新车型等字段
						if ( !empty($_POST['brand_id']) ) {
							$updateData['brand_id'] = $_POST['brand_id'];
						}
						if ( !empty($_POST['series_id']) ) {
							$updateData['series_id'] = $_POST['series_id'];
						}
						if ( !empty($_POST['model_id']) ) {
							$updateData['model_id'] = $_POST['model_id'];
						}
						
						
						//传过来的和数据库里的不一样
						if ( $pa_res['mobile'] != $mobile ) {
							//更新新的手机号并且已经表示该手机号已经绑定了
							$updateData['mobile'] = $mobile;
							if ( !empty($pa_res['mobile']) ) {
								$_SESSION['needAddNumber'] = 'no';
							}
						}
						
						$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->save($updateData);
					}

					$member = $this->MemberModel->where(array('mobile'=>$mobile))->find();
					$this->CheckMember($member,$mobile,$Paweixin_id,$code,'2');
					
					//根据uid来关联事故车维修进度的手机号
					if ( !empty($member['uid']) ) {
						$this->mRepairprocess->where( array(
								'uid'=>$member['uid']
						))->save( array(
								'mobile'=>$mobile
						));
					}

					if ( !empty($_POST['invite_code'] )){
						//验证邀请码是否存在
						$inviteCode = $_POST['invite_code'];
						$valid = $this->_validInviteCode($inviteCode);
						if ($valid) {
							//保存用户填写的邀请码
							$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->save(array('invite_code'=>$inviteCode));
							echo "4";//可以参加抽奖
						}else{
							echo "1";
						}
					}else{
						echo "1";
					}


				}
			}
		}else{
			echo "2";
		}
	}

	/*
		@author:
		@function:支付宝服务窗绑定
		@time:2015-4-1
	*/

	function alipay_bind_order(){

		if(!$_REQUEST['mobile']){
			$this->eror('参数错误');
		}

		if(!$_REQUEST['ali_id']){
			$this->eror('参数错误');
		}

		if(!$_REQUEST['code']){
			$this->eror('参数错误');
		}

		$mobile = $_REQUEST['mobile'];
		$ali_id = $_REQUEST['ali_id'];
		$code = $_REQUEST['code'];

		if( ($_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile) || ($code == '4006602822') ){

			$member = $this->MemberModel->where(array('mobile'=>$mobile))->find();
			
			if($member){
				$data['uid'] = $member['uid'];
			}

			$mAlipay = D('alipay_fuwuchuang');
			$resAli = $mAlipay->where(array('mobile'=>$mobile))->find();
			if($resAli){
				echo 3;
			}else{
				$data['mobile'] = $mobile;
				$data['create_time'] = time();
				$data['status'] = 2;
				$resAli2 = $mAlipay->where(array('FromUserId'=>$ali_id))->find();
				if($resAli2){
					$aaa = $mAlipay->where(array('FromUserId'=>$ali_id))->save($data);
					if($aaa){
						echo 1;
					}
				}
			}

		}else{
			echo 0;
		}
	}

	/*
		@author:
		@function:支付宝服务窗绑定自动注册
		@time:2015-4-1
	*/
	function alipay_zc($member,$mobile,$code){
		if(!$member){
			$member_data['mobile'] = $mobile;
			$member_data['password'] = md5($code);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '36';//携车微信
			$uid = $this->MemberModel->data($member_data)->add();
			/*
			$send_add_user_data = array(
				'phones'=>$mobile,
				'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$code.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
			*/
			// dingjb 2015-09-29 11:14:46 切换到云通讯
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
				'phones'=>$mobile,
				'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$code.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
			*/
			// dingjb 2015-09-29 11:16:32 切换到云通道
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
		@author:chf
		@function:大众点评调用方法,没有此手机帐号则注册个
		@time:2014-06-11
	*/
	function DzCheckMember($member,$mobile,$code){
		if(!$member){
			$member_data['mobile'] = $mobile;
			$member_data['password'] = md5($code);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$member_data['fromstatus'] = '36';//携车微信
			$uid = $this->MemberModel->data($member_data)->add();
			/*
			$send_add_user_data = array(
				'phones'=>$mobile,
				'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$code.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_add_user_data);
			*/
			// dingjb 2015-09-29 11:18:30 切换到云通道
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

			/*判断是否是大众点评跳转过来的用户*/
			if($_SESSION['dztype'] == '1'){
				$member = $this->MemberModel->where(array('mobile'=>$_REQUEST['mobile']))->find();
				$Paweixin['uid'] = $member['uid'];
			}else{
				$Paweixin = $this->PaweixinModel->where(array('id'=>$_REQUEST['Paweixin_id'],'status'=>'2'))->find();
			}

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
   				$this->success('预约提交成功！',__APP__.'/Mobile-order_list-pa_id-'.$_SESSION['xc_id']);
    		}else {
    			$this->error('预约失败',__APP__.'/Mobile/index');
    		}
		}

	}
	/*
	 * @author:bright
	 * @function:验证邀请码是否正确
	 * @time：2014-08-11
	 */
	function _validInviteCode($inviteCode){
		if(!$inviteCode){
			return false;
		}
		$Paweixin = $this->offlinespread->where(array('invite_code'=>$inviteCode))->count();
		if($Paweixin == 0){
			return false;
		}else{
			return true;
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

		//此处修改成选择FSID来匹配数据库和IOS安卓不一样的地方*******************************************
		/*
		//多品牌查询条件添加(这次修改)

		$Carseries = $model_series->where(array('fsid'=>$map_fs['fsid']))->select();
		if($Carseries){
			foreach($Carseries as $ser_k=>$ser_v){
				 $brand[] = $ser_v['brand_id'];
			}
			$brandarr = array_unique($brand);
			$data['_string'] .= " FIND_IN_SET('{$brandarr}', brand_id)";
		}
		*/

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
			if(!$Carband){
				unset($FSdata[$k]);
			}else{
				$FSdata[$k]['word'] = $Carband['word'];
			}
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
		//记录是不是要不绑定微信直接下单
		if($_GET['dztype']){
			$_SESSION['dztype'] = $_GET['dztype'];
		}
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
		if($_SESSION['dztype'] == '1'){//这个里的判断只要传入这个值就不用微信帮顶来直接下单
			$arr['weixin_status'] = '2';
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
        if ($_REQUEST['Paweixin_id'] || $_SESSION['dztype'] =='1'){
			//判断如果有dztype传来值则不用去微信表取UID
			if($_SESSION['dztype'] =='1'){
				$member = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
				$uid = $data['uid'] = $member['uid'];
			}else{
				$Paweixin_id = $_REQUEST['Paweixin_id'];
				$Paweixin = $this->PaweixinModel->where(array('id'=>$_REQUEST['Paweixin_id'],'status'=>'2'))->find();
				$uid = $data['uid'] = $Paweixin['uid'];
			}

            if ($Paweixin || $_SESSION['dztype'] =='1'){
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
                	        $data['shop_ids'] = $coupon['shop_id'];
                	        $data['start_time'] = $coupon['start_time'];

							//wap和网页不一样不用判断抵用券 直接存coupon_amount
							$data['coupon_amount'] = $coupon['coupon_amount'];
							/*
							if($coupon['coupon_type']=='1'){
								$data['ratio'] = $shop['cash_rebate'];//现金券比例
							}else{
								$data['ratio'] = $shop['group_rebate'];
							}*/

							if($_SESSION['dztype'] == '1'){
								$data['pa'] = '6';//爆款
							}else{
								$data['pa'] = '3';//自己wap
							}


                	        if ($membercoupon_id = $model_membercoupon->add($data)){
                	            $membercoupon_id_arr[] = $membercoupon_id;
                	        }
                        }
                        $membercoupon_id_str = implode(',',$membercoupon_id_arr);

//						if($_REQUEST['mobile'] == "13661743916" || $_REQUEST['mobile'] == "18516261797") {
							header("Location:http://www.xieche.com.cn/Mobile-showpay-membercoupon_id-{$membercoupon_id_str}");
							exit();
//						}else {
//							$this->success('购买成功!','/txwappay/payRequest.php?membercoupon_id='.$membercoupon_id_str);
//						}
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
		@author:chf
		@function:用户中心
		@time:2014/1/22
	*/
	function my_account(){
		if($_REQUEST['pa_id']){
				$_SESSION['pa_id'] = $_REQUEST['pa_id'];
		}
		$this->assign('pa_id',$_REQUEST['pa_id']);
		$this->display();
	}

	/*
		@author:ysh
		@function:显示购买的优惠券
		@time:2014/1/22
	*/
	function my_coupon() {
		if($_REQUEST['pa_id']){
			$_SESSION['xc_id'] = $_REQUEST['pa_id'];

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
		}else{
			$this->assign('show','no');
		}
		$this->display();
	}

	/*
		@author:ysh
		@function:用户中心订单列表
		@time:2014/2/20
	*/
	function order_list() {
		$order_state_arr = C('ORDER_STATE');

			if($_REQUEST['pa_id']){
				$_SESSION['xc_id'] = $_REQUEST['pa_id'];
			

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
			$this->assign('pa_id',$_SESSION['xc_id']);
			$this->assign('uid',$uid);
			$this->assign('order_list',$order_list);
		}else{
			$this->assign('show','no');
		}
		$this->display();
	}

	/*
	@author:liuhui
	@function:刮奖
	@time:2014/8/11
	*/
	function scratch() {
		//判断是否已经刮过奖
		if (!$_REQUEST['pa_id']) {
			return false;
		}
		$pa_id = $_REQUEST['pa_id'];
		$palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
		$res = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
		if($res['overpraised'] == 2){	//刮过
			$this->error('您已经刮过奖了，不能重复刮奖',__APP__.'/Mobile/index');
		}else{
			$this->assign('weixinId',$palog['FromUserName']);
			$this->assign('mobile',$res['mobile']);
			$this->display();
		}

	}

	/*
	@author:liuhui
	@function:设置该用户已经刮过奖
	@time:2014/8/11
	*/
	function SetOverPraise(){
		if ( !empty($_POST['weixinId']) ) {
			$weixinId = $_POST['weixinId'];
			$res = $this->PaweixinModel->where(array('wx_id'=>$weixinId))->find();
			if($res['overpraised'] == 2){	//刮过
				$this->ajaxReturn('','改用户已经挂过奖了，不能重复刮奖',0);
			}else{
				$data = array('overpraised'=>2,'prize'=>'中奖50块','bind_time'=>time());
				$this->PaweixinModel->where(array('wx_id'=>$weixinId))->save($data);
				//通知用户
				$weixin_data = array(
						'touser'=>$weixinId,
						'title'=>'中奖通知',
						'description'=>'恭喜您获得50元4S店养车抵用券，本券已打入您的携车网账户，您通过携车网购买团购套餐券时，可直接抵用（本券暂时只支持网站购买使用），详情请咨询400-660-2822',
						'url'=>'http://www.xieche.com.cn/coupon'
				);
				$this->weixin_api($weixin_data);
				
				if (@$_SESSION['needAddNumber'] == 'yes'){
					//地推人员邀请总数加一，产品定义的刮过奖才算邀请过，邀请码保持唯一
					$inviteCode = $res['invite_code'];
					$this->offlinespread->where(array('invite_code'=>$inviteCode))->save( array('updateTime'=>time()) );
					
					$add = $this->offlinespread->where(array('invite_code'=>$inviteCode))->setInc('invite_num');
					//地推人员邀请金额加一次，直接加次数，前端显示 次数X50
					
					if ($add) {
						$this->ajaxReturn($res,'success',1);
					}else{
						$this->ajaxReturn($res,$res['overpraised'],2);
					}
				}else{
					$this->ajaxReturn($res,$_SESSION['needAddNumber'],0);
				}
			}
		}else{
			$this->ajaxReturn('','param error',0);
		}

	}
	
	/*
	@author:liuhui
	@function:事故车订单列表
	@time:2014/8/11
	*/
	function bidorder_list(){
		if (!$_GET['pa_id']) {
			return false;
		}
		$pa_id = $_GET['pa_id'];
		$paData = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$paData['FromUserName'],'type'=>'2'))->find();
		if (!$Paweixin) {
			$this->error('请先绑定电话号码再查看您的订单',__APP__.'/Mobile/login_verify/pa_id/'.$pa_id);
			return false;
		}
		$model = array();
		
		if( !empty($Paweixin['mobile']) ){
			$where = array( 'mobile' => $Paweixin['mobile'] );
			$model = $this->mBidorder->where($where)->order('create_time desc')->select();
			if ($model){
				foreach ($model as $key=>&$val){
				//店铺名字
				$Shopbidding = $this->ShopbiddingModel->where(array('id'=>$val['bid_id']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Shopbidding['shop_id']))->find();
				$val['shop_name'] = $shop['shop_name'];
				//定损金额
				$Insurance = $this->InsuranceModel->where(array('id'=>$val['insurance_id']))->find();
				$val['insurance_name'] =  $Insurance['insurance_name'];
				$val['loss_price'] = $Insurance['loss_price'];
				unset($val);
				}
			}
		}
		$this->assign('pa_id',$pa_id);
		$this->assign('model',$model);
		$this->display();
	}
	
	/*
	@author:liuhui
	@function:事故车订单进度查询
	@time:2014/8/11
	*/
	function bidorder_process(){
		if (!$_GET['id']) {
			return false;
		}
		$where = array('order_id'=>$_GET['id']);
		$model = $this->mRepairprocess->where($where)->select();
		if ($model) {
			$total = count($model);
			foreach ($model as $key=>&$val){
				if ( $val['describe'] ) {
					$val['describe'] = unserialize($val['describe']);
					$count = $val['count'] = count($val['describe']);
					$val['des_t'] = $val['describe'][0]['des'];
					if ( $count == 3) {
						$val['describe'] = array_chunk($val['describe'],2);
						$val['describe1'] = $val['describe'][0];
						$val['describe0'] = $val['describe'][1][0];
					}elseif ($count == 4){
						$val['describe'] = array_chunk($val['describe'],2);
					}
					unset($val);
				}
			}
		}
		$this->assign('model',$model);
		$this->assign('total',$total);
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

	/*
	@function： 支付方式wap页面
	@author：ysh
	@time：2013-11-20
	*/
	function showpay() {
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}

		$membercoupon_id = $_REQUEST['membercoupon_id'];

		$this->assign('header',$header);
		$this->assign('membercoupon_id',$membercoupon_id);
		$this->display();
	}

	/*
	@function： 支付方式跳转页面
	@author：ysh
	@time：2013-11-20
	*/
	function topay() {
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		$pay_type = $_REQUEST['pay_type'];
		if($pay_type == 1) {//财付通
			header("Location:http://www.xieche.com.cn/txwappay/payRequest.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==2 ) {//支付宝
			header("Location:http://www.xieche.com.cn/apppay/alipayto_new.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==3 ) {//银联
			header("Location:http://www.xieche.com.cn/unionwappay/examples/purchase.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==4 ) {//微信支付
			header("Location:http://www.xieche.com.cn/weixinpay/wxpay.php?membercoupon_id={$membercoupon_id}&showwxpaytitle=1");
			exit();
		}
	}
	/*
	@function： 问卷调查
	@author：wwy
	@time：2014-8-27
	*/
	function survey(){
		$show = substr($_SERVER['HTTP_REFERER'],-2,2);
		if($_REQUEST['question_id']){
			$map['question_id'] = $_REQUEST['question_id'];
			$q_info = $this->QuestionModel->where($map)->find();
			$a_info = $this->AnswerModel->where(array('question_id'=>$q_info['question_id']))->select();
			//print_r($q_info);
			//print_r($a_info);
			$this->assign('q_info',$q_info);
			$this->assign('a_info',$a_info);
			$this->assign('q_id',$_REQUEST['question_id']);
		}
		$p_number = $this->get_picnumber($_REQUEST['question_id']);
		$q_info = '测测看';

		if($show>21 or $_REQUEST['question_id']>21){
			$mad['question_id'] = $_REQUEST['qid'];
			if($_REQUEST['question_id']) $mad['question_id'] = $_REQUEST['question_id'];
			$q_info = $this->QuestionModel->where($mad)->find();
			$q_info = '原来我'.substr($q_info['content'],strrpos($q_info['content'],'：')+6);
			$p_number = $this->get_picnumber($mad['question_id']);

			$this->assign('shareshow',$_REQUEST['shareshow']);
		}

		$this->assign('title',$q_info);
		$this->assign('p_number',$p_number);
		$this->display();
	}

	function get_picnumber($q_id){
		if($q_id and $q_id>21){
			$get_pic = array(	'22'=>'1',
								'23'=>'2',
								'24'=>'3',
								'25'=>'4',
								'26'=>'5',
								'27'=>'6',
							);
			$p_number = $get_pic[$q_id];
		}else{
			$p_number = 7;
		}
		return $p_number;
	}

	//上门保养20元优惠券领券页面
	function receive(){
		$this->display();
	}

	/*
		@author:wwy
		@function:发送验证码
		@time:2014-10-22
	*/
	function send_verify() {
		if(is_numeric($_REQUEST['mobile'])){
			$mobile = $_REQUEST['mobile'];
		}else{
			$mobile = $_SESSION['mobile'];
		}
		$model_sms = D('Sms');
		$repeat['mobile'] = $mobile;
    	$r_info = $this->carservicecode_model->where($repeat)->find();
		if(!$r_info){
			if ($mobile){
				/*添加发送手机验证码*/
				$condition['phones'] = $mobile;
				$rand_verify = rand(1000, 9999);
				$_SESSION['mobile_verify_ceshi'] = $rand_verify;
				$_SESSION['mobile_verify_xieche'] = md5($rand_verify);
				$verify_str = "正在为您的手机验证，您的短信验证码：".$rand_verify;
				/*
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$return_data = $this->curl_sms($send_verify,'',3);
				*/

				// dingjb 2015-09-29 09:52:17 切换到云通讯
				$send_verify = array(
					'phones'  => $mobile,
					'content' => array($rand_verify),
				);
				$return_data = $this->curl_sms($send_verify, null, 4, '37650');

				$send_verify['sendtime'] = time();
				//外网注视去掉保存进短信记录表
				$model_sms->add($send_verify);
				$ret = array('message'=>'验证码发送成功');
				$this->ajaxReturn($ret,'',1);
				
			}else {
				$ret = array('message'=>'手机号码错误');
				$this->ajaxReturn($ret,'',0);
			}
		}else{
			$ret = array('message'=>'亲，您已领过了抵用券，快去使用吧');
			$this->ajaxReturn($ret,'',0);
		}
	}

	//20元券领取流程
    function code_process(){
		//校验验证码
		if($_SESSION['mobile_verify_xieche'] != md5($_POST['code']) or !$_POST['code']){
			$ret = array('message'=>'验证码错误');
    		$this->ajaxReturn($ret,'',0);
		}
		$repeat['mobile'] = $_POST['mobile'];
    	$r_info = $this->carservicecode_model->where($repeat)->find();
		if(!$r_info){
			//获取一个随机券码绑定手机号码
			$map['pici'] = 11;
			$map['mobile'] = '';
			$map['status'] = 0;
			$info = $this->carservicecode_model->where($map)->find();

			//校验技师编码
			if($_POST['js_code'] and $_POST['js_code']!='输入推荐的技师编码(非必选)'){
				$j_info = $this->technician_model->select();
				foreach($j_info as $k=>$v){
					$js_info[]= '100'.$v['id'];
				}

				if(in_array($_POST['js_code'],$js_info)){
					$update['js_code'] = substr($_POST['js_code'],3);
				}else{
					$ret = array('message'=>'无效的技师编码');
					$this->ajaxReturn($ret,'',0);
				}
			}

			$update['mobile'] = $_POST['mobile'];
			$update['start_time'] = time();
			$update['end_time'] = $update['start_time']+30*86400;
			$umap['id'] = $info['id'];
			$res = $this->carservicecode_model->where($umap)->save($update);
			//echo $this->carservicecode_model->getLastsql();
			if( $res ){
				//领券成功，给用户发短信
				$sms = array(
				'phones'=>$_POST['mobile'],
				'content'=>'您的20元代金券领取成功，券码'.$info['coupon_code'].'，有效期至'.date('m-d',$update['end_time']).'。可在携车网微信公众号预约“府上养车”时使用。或致电4006602822',
				);
				$this->curl_sms($sms,'',1);
				$sms['sendtime'] = time();
				$this->model_sms->data($sms)->add();

				$ret = array('message'=>'恭喜您，领券成功');
				$this->ajaxReturn($ret,'',1);
			}else{
				$ret = array('message'=>'领券失败，请重试');
				$this->ajaxReturn($ret,'',0);
			}
		}else{
			$ret = array('message'=>'您的手机号已领过券了');
			$this->ajaxReturn($ret,'',0);
		}

    }

    //AJAX获取JSAPI验证数据
    function get_checkdata(){
        $mToken = D('weixin_token');
        $res = $mToken->field('ticket')->where( array('id'=>1))->find();
        $return['timestamp'] = $timestamp = time(); // 必填，生成签名的时间戳
        $return['noncestr'] = $noncestr = $this->create_noncestr();
		$url = $_SERVER['HTTP_REFERER'];
        $return['appId'] = 'wx43430f4b6f59ed33';// 必填，公众号的唯一标识
//         echo "jsapi_ticket={$res['ticket']}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
        $return['signature'] = sha1("jsapi_ticket={$res['ticket']}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}");// 必填，签名，见附录1
//         echo $return['signature'];exit;
        $this->ajaxReturn( $return );
    }
    
    function create_noncestr( $length = 16 ) {  
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$str ="";  
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
			//$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
		}  
		return $str;  
	}
    	//金数据活动页
	function jinshuju(){
		$this->display();
	}    
        	//金数据活动页2
	function jinshuju_edaibo1(){
		$this->display();
	}
	function jinshuju_sfw_haochekuang(){
		$this->display();
	}
    function jinshuju_anshifu_ktgdqx(){
        $this->display();
    }
    function jinshuju_pcauto_cyhhuodong(){
        $this->display();
    }
}
