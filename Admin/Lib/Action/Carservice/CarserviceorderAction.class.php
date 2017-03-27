<?php
//首页
class CarserviceorderAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		
        $this->car_brand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->car_model_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->car_style_model = M('tp_xieche.car_style','xc_');  //车系号

        $this->checkreport_model = M('tp_xieche.checkreport_total', 'xc_');  //检测报告表
        $this->check_step_model = M('tp_xieche.check_step', 'xc_');  //技师步骤表
        $this->technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表 
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //保养项目
        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目
        
        $this->PadataModel = M('tp_xieche.padatatest', 'xc_');//接收微信订单数据表
        $this->PaweixinModel = M('tp_xieche.paweixin', 'xc_');//携车手机微信比对表
        $this->user_model = M('tp_xieche.member', 'xc_');//用户表
		$this->admin_model = M('tp_admin.user', 'xc_');//后台用户表
		$this->invoice_model = M('tp_xieche.invoice', 'xc_');//发票数据表
		$this->recall_model = M('tp_xieche.reservation_recall', 'xc_');//客服回访表
		$this->carservicecode_model = M('tp_xieche.carservicecode', 'xc_');//优惠券表
		$this->carservicecode1_model = M('tp_xieche.carservicecode_1', 'xc_');//优惠券表

        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
        
        $this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
        $this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段
        $this->model_sms = M('tp_xieche.sms','xc_');//手机短信
        $this->storehouse_item_model = M('tp_xieche.storehouse_item','xc_');//仓库数据详情表
        $this->finance_model = M('tp_xieche.finance','xc_');  //财务表
		$this->city_model = M('tp_xieche.city','xc_');  //财务表
		$this->package_model = M('tp_xieche.package','xc_');  //套餐表
        $this->dphome_linshi = D('dphome_linshi');  //点评数据临时表   
        $this->interface_log = D('interface_log');  //接口通知记录表
		$this->city = D('city');   //城市表
        $this->item_city = D('item_city');   //新城市表
        $this->item_area = D('item_area');   //地区表
		
		
		$this->business_source = D('business_source');   //地区表
        
        //新版优惠券相关模型
        $this->newCouponModel = D('Oilactivity/NewCoupon');
        $this->newCouponCodeModel = D('Oilactivity/NewCouponCode');
        

        $this->technician_group = D('technician_group'); //技师组
        $this->change_amount = D('changeamount'); //申请改价表

        $this->new_carbrand = D('new_carbrand');   //新品牌表
        $this->new_carseries = D('new_carseries');   //新车系表
        $this->new_carmodel = D('new_carmodel');   //新车型表
        
        $this->new_oil = D('new_item_oil');  //保养机油        
        $this->new_filter = D('new_item_filter');  //保养三滤配件
        
        
                
	}
	/*
	 * 代下单
	*/
	public function place_order(){
		
		$uid = @$_GET['uid'];
		if (!$uid) {
			$this->error('uid为空，预约失败');
		}
		$_SESSION['user_id'] = $uid;
		if($_GET['order_type']){
			$_SESSION['order_type'] = $_GET['order_type'];
		}else{
			unset($_SESSION['order_type']);
		}
		if($_GET['exchange_id']){
			$_SESSION['exchange_id'] = $_GET['exchange_id'];
		}else{
			unset($_SESSION['exchange_id']);
		}
        
        //只查询力洋数据库
        $where['is_show'] = 1 ;
		$brand_list = $this->new_carbrand->where($where)->order('word asc')->select();
		$this->assign("brand_list", $brand_list);

		$this->assign('title',"上门保养-携车网");
        //初始化用户信息
		$userinfo = $this->reservation_order_model->where(array('uid'=>$_SESSION['user_id']))->order('create_time desc')->find();
		$spreadprize = D('spreadprize');
		$prize_info = $spreadprize->where(array('id'=>$_SESSION['exchange_id']))->find();
		if(!$userinfo['username']){ $userinfo['username'] = $prize_info['username']; }
		if(!$userinfo['address']){ $userinfo['address'] = $prize_info['address']; }
		$this->assign('userinfo',$userinfo);
        
        //城市数据
        $city_list = $this->city->select();
        $this->assign('city_list',$city_list);
        
        
                //业务来源数据
//		$business_source_list = array('1'=>'IB',
//                                    '2'=>'OB',
//                                    '3'=>'三星',
//                                    '4'=>'百度',
//                                    '5'=>'淘宝',
//                                    '6'=>'优惠券',
//                                    '7'=>'微信',
//                                    '8'=>'微信活动',
//                                    '9'=>'小区',
//                                    '10'=>'事故车',
//                                    '11'=>'介绍',
//                                    '12'=>'平安好车',
//                                    '13'=>'微博',
//                                    '14'=>'搜索',
//                                    '15'=>'朋友圈',
//                                    '16'=>'加油站',
//                                    '17'=>'宝驾',
//                                    '18'=>'e袋洗',
//                                    '19'=>'老客户',
//                                    '20'=>'养车点点',
//                                    '21'=>'驴妈妈',
//                                    '22'=>'其他',
//                                    '23'=>'4S店预约转化',
//                                    '24'=>'上海平安',
//                                    '25'=>'点评团购',
//                                    '26'=>'光大',
//                                    '27'=>'京东',
//                                    '28'=>'人保',
//                                    '29'=>'光大信用卡',
//                                    '30'=>'淘宝现场活动',
//                                    '31'=>'光大黄金信用卡',
//                                    '32'=>'凹凸租车',
//                                    '33'=>'建设银行',
//                                    '34'=>'pad无配件订单',
//                                    '35'=>'百度生活',
//									'36'=>'通用',
//									'37'=>'UBER车主',
//									'38'=>'UBER活动',
//									'39'=>'i保养',
//									'40'=>'点评到家',
//									'41'=>'苏州平安',
//									'42'=>'上海人保',
//									'43'=>'安盛天平',
//									'44'=>'e代泊',
//									'45'=>'车挣',
//                                    '46'=>'杭州车猫',
//									'47'=>'搜房网',
//									'48'=>'安师傅',
//									'49'=>'杭州人保',
//									'50'=>'大众公社',
//									'51'=>'上海美亚',
//									'52'=>'微信文章',
//                                    '53'=>'天猫',
//                                    '54'=>'同程旅游'
//		);
        $business_source_list = $this->business_source->where(array('level'=>1))->order('id asc')->select();
		$this->assign('business_source_list',$business_source_list);
                //默认价格
                $item_amount = 99;
		$this->assign('item_amount',$item_amount);

		$this->display('place_order');
	
	}

    /**
     * 业务来源LEVEL-2
     */
    public function ajax_level(){
        $condition['level'] = intval($_POST['level_id']);
        if($_POST['pid']>0){
            $condition['pid'] = intval($_POST['pid']);
        }
        $level_list = $this->business_source->where($condition)->order('id asc')->select();

        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $return['result'] = array('level_list' => $level_list );

        $this->ajaxReturn( $return );
    }

	public function sub_car(){
		session_start();
		$_SESSION['admin_brand_id'] = intval($_REQUEST['brand_id']);
		$_SESSION['admin_model_id'] = intval($_REQUEST['model_id']);
		$_SESSION['admin_style_id'] = intval($_REQUEST['style_id']);
		$_SESSION['code'] = $_REQUEST['code'];

		if($_SESSION['code'] and $_SESSION['code']!='016888' and $_SESSION['code']!='j8610000'){
			$chk_code = $this->_check_replace_code($_SESSION['code'],0,0,0);
			if(!$chk_code){

				//echo "该抵用码不能使用，请重新填写";   exit;
			}
		}
        $info = $this->carservicecode_model->where(array('coupon_code'=>$_SESSION['code']))->find();
		$first = substr($_SESSION['code'],0,1);
		if($first==c or $first==r or $first==y){
			$_SESSION['order_type'] = 7;
		}elseif($first==d or $first==s or $first==x or ($first==e and $info['pici']==102)){
			$_SESSION['order_type'] = 8;
		}elseif(($first==e and $info['pici']!=102) or $first==t or $first==w){
			$_SESSION['order_type'] = 10;
		}elseif($first==h){
			$_SESSION['order_type'] = 11;
		}elseif($first==i){
			$_SESSION['order_type'] = 12;
		}elseif($first==j){
			$_SESSION['order_type'] = 35;
		}elseif($first==z){
			$_SESSION['order_type'] = '14z';
		}elseif($first==u){
			$_SESSION['order_type'] = 54;
		}
		if($this->_get('order_type')){
			unset($_SESSION['order_type']);
			$_SESSION['order_type'] = $this->_get('order_type');
		}
        if ($this->newCouponCodeModel->isValidNewCouponCode($_REQUEST['code'], 'all')) {//新优惠券非保养订单套餐配件
            $_SESSION['order_type'] = $this->newCouponCodeModel->getOrderType($_REQUEST['code']);
        }
		//print_r($_SESSION);exit;
		$this->redirect('carserviceorder/sel_item');
	}
	/**
	 * 车型
	 */
	public function ajax_car_model(){
		$brand_id = intval($_POST['brand_id']);
		if( $brand_id ){
			$condition['brand_id'] = $brand_id;
            $condition['is_show'] =  1 ;
			//$car_model_list = $this->car_model_model->where( $condition )->select();
			$car_model_list = $this->new_carseries->where($condition)->select();
		}else{
			$car_model_list = "";
		}
		if( $car_model_list ){
			$return['errno'] = '0';
			$return['errmsg'] = 'success';
			$return['result'] = array('model_list' => $car_model_list );
		}else{
			$return['errno'] = '1';
			$return['errmsg'] = '该品牌下无录入车系';
		}
		$this->ajaxReturn( $return );
	}
	
	/**
	 * 车款
	 */
	public function ajax_car_style(){
		$model_id = intval( $_POST['model_id'] );
		if( $model_id ){
			//$condition['model_id'] = $model_id;
			//$car_style_list = $this->car_style_model->where( $condition )->select();
            //$condition['oil_mass'] = array('neq',' ');
            //$condition['oil_type'] = array('neq',' ');
            
			$condition['series_id'] = $model_id;
            $condition['is_show'] =  1 ;
            
			$car_style_list = $this->new_carmodel->where($condition)->select();
		}else{
			$car_style_list = "";
		}
	
		if( $car_style_list ){
			$return['errno'] = '0';
			$return['errmsg'] = 'success';
			$return['result'] = array('style_list' => $car_style_list );
		}else{
			$return['errno'] = '1';
			$return['errmsg'] = '该车型下无录入车辆';
		}
		$this->ajaxReturn( $return );
	}
    
    
    /**
	 * 获取城市地区   wql@20150720
	 */
	public function ajax_area(){
		$city_id = intval($_POST['city_id']);
		if($city_id==1){
            //通过老的城市id获取新的城市id ,然后再去查询地区信息并返回
			$cond['old_cityid'] = $city_id;
            $cityInfo = $this->item_city->where($cond)->find();
            $map['fatherID'] = $cityInfo['cityID'];
			$area_list = $this->item_area->where($map)->select();
		}else{
			$area_list = "";
		}
	
        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $return['result'] = array('area_list' => $area_list );
		$this->ajaxReturn( $return );
	}

	/**
	* 获取抵扣券数据
	*/
	public function get_codeinfo(){
        $code = $_POST['code'];
        if( $code ){
            $condition['coupon_code'] = $code;
            $first = substr($code,0,1);
            if($first==c or $first==d or $first==e or $first==h){
                $value = '套餐约价';
                $code = $this->carservicecode_model->where( $condition )->select();
            }else{
                $value = $this->get_codevalue($code);
                $code = $this->carservicecode_model->where( $condition )->select();
                $code1 = $this->carservicecode1_model->where( $condition )->select();
            }
        }else{
            $code = "";
        }

        if($code){
            foreach($code as $k=>$v){
                $code[$k]['end_time'] = date('Y-m-d H:i:s',$v['end_time']);
                $code[$k]['price'] = $value;
            }
        }

        if( $code or $code1){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            if($code){
                $return['result'] = array('code' => $code );
            }
            if($code1){
                $return['result'] = array('code' => $code1 );
            }
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '无效的优惠券码';
        }

		$this->ajaxReturn( $return );
	}
	public function sel_item(){
		session_start();
		if($_SESSION['admin_style_id']){
		}else{
			$this->redirect('carservice');
			return false;
		}
	
		//车型
		$style_param['model_id'] = $_SESSION['admin_style_id'];
		//$car_style = $this->car_style_model->where($style_param)->find();
		$car_style = $this->new_carmodel->where($style_param)->find();
		$style_name = $car_style['model_name'];
		$car_name = $car_style['model_name'];
	
		if($car_style){
			$model_param['series_id'] = $car_style['series_id'];
			//$car_model = $this->car_model_model->where($model_param)->find();
			$car_model = $this->new_carseries->where($model_param)->find();
	
			if($car_model){
				$model_name = $car_model['series_name'];
				$car_name = $car_model['series_name']." - ".$car_name;
	
				$brand_param['brand_id'] = $car_model['brand_id'];
				//$car_brand = $this->car_brand_model->where($brand_param)->find();
				$car_brand = $this->new_carbrand->where($brand_param)->find();
				if($car_brand){
					$brand_name = $car_brand['brand_name'];
					$car_name = $car_brand['brand_name']." - ".$car_name;
				}
			}
		}
		$this->assign('car_name', $car_name);
		$this->assign('brand_name', $brand_name);
		$this->assign('model_name', $model_name);
		$this->assign('style_name', $style_name);
	
		//机油
		//$oil_num = ceil($car_style['oil_num']);
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
		$oil_list_all = $this->new_oil->where()->select();
        
		$oil_item = array();
		foreach( $oil_list_all as $nors){
			
			$nors['name'] = rtrim($nors['name'],'装');
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

		if($_SESSION['order_type']==7 or $_SESSION['order_type']==17 or $_SESSION['order_type']==22 or $_SESSION['order_type']==25 or $_SESSION['order_type']==36 or $_SESSION['order_type']==56 or $_SESSION['order_type']==60){
			$oli_taocan = array('49','56');
		}
		if($_SESSION['order_type']==8 or $_SESSION['order_type']==12 or $_SESSION['order_type']==18 or $_SESSION['order_type']==23 or $_SESSION['order_type']==26 or $_SESSION['order_type']==37 or $_SESSION['order_type']==57 or $_SESSION['order_type']==61){
			$oli_taocan = array('47','48','54','55');
		}
		if($_SESSION['order_type']==9 or $_SESSION['order_type']==19 or $_SESSION['order_type']==27 or $_SESSION['order_type']==38 or $_SESSION['order_type']==58){
			$oli_taocan = array('45','46','50','51','52','53','57','58');
		}
		if($_SESSION['order_type']==10 or $_SESSION['order_type']==11 or $_SESSION['order_type']==24){
			$oli_taocan = array('50','51');
		}
		if($_SESSION['order_type'] =='14z' or $_SESSION['order_type']==14 or $_SESSION['order_type']==33 or $_SESSION['order_type']==50 or $_SESSION['order_type']==4 or $_SESSION['order_type']==52 or $_SESSION['order_type']==62 or $_SESSION['order_type']==65 or $_SESSION['order_type']==71){
			$oli_taocan = array();
		}
		//符合要求的品牌详情
		$item_sets=array();
		$oil_name_distinct = $this->new_oil->order('price')->where($oil_param)->select();
        //echo  $this->new_oil->getLastSql();

		foreach( $oil_name_distinct as $keys=>$names ){
			
			$names['name'] = rtrim($names['name'],'装');
			
			$item_sets[$keys]['id'] = $names['id'];
			$item_sets[$keys]['name'] = $names['name'];
			//规格1L详情
			$item_sets[$keys]['oil_1'] = $oil_item[$names['name']][1]['id'];
			$item_sets[$keys]['oil_1_num'] = $xx;
			$item_sets[$keys]['oil_1_price'] = $oil_item[$names['name']][1]['price'];
			//规格4L详情
			$item_sets[$keys]['oil_2'] = $oil_item[$names['name']][4]['id'];
			$item_sets[$keys]['oil_2_num'] = $yy;
			$item_sets[$keys]['oil_2_price'] = $oil_item[$names['name']][4]['price'];
	
			//计算总价
			$item_sets[$keys]['price'] = $xx*$oil_item[$names['name']][1]['price']+$yy*$oil_item[$names['name']][4]['price'];
			//干掉4升以上金嘉护的选项
			if($car_style['oil_mass']>4 and $names['id']==56){ unset($item_sets[$keys]); }
		}

		//排除套餐机油类型之外的机油选项
		if(isset($_SESSION['order_type']) && $_SESSION['order_type']>3 and $_SESSION['order_type']!=34){
			//print_r($item_sets);
			foreach($item_sets as $_kk=>$_vv){
				if(!in_array($_vv['id'],$oli_taocan)){
					unset($item_sets[$_kk]);
				}
			}
			//print_r($item_sets);
			foreach($item_sets as $_kk=>$_vv){
				$item_set[] = $_vv;
			}
			$item_sets = $item_set;
		}

        //print_r($item_sets); exit ;
		if($_SESSION['order_type']>15 and empty($item_sets) and $_SESSION['order_type']!=68 and $_SESSION['order_type']!=69){
			$tips = "该车型无法使用套餐机油";
			$this->assign('tips', $tips);
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

		//项目
		$style_id = $_SESSION['admin_style_id'];
		$item_set = array();
		if( $style_id ){
			$condition['model_id'] = $style_id;
			//$style_info = $this->car_style_model->where($condition)->find();
			$style_info = $this->new_carmodel->where($condition)->find();
			$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
			if( $set_id_arr ){
				foreach( $set_id_arr as $k=>$v){
					if(is_array($v)){
						foreach( $v as $_k=>$_v){
							$item_condition['id'] = $_v;
                            //价格不为零
                            $item_condition['price'] = array('neq','0');
                            
							if( !$_SESSION['order_type'] or !in_array($_SESSION['order_type'],array(47,49))){
								$item_condition['name'] = array('notlike','%pm2%');
							}
							if( in_array($_SESSION['order_type'],array(47,49))){
								$item_condition['name'] = array('like','%pm2%');
							}
							$item_info_res = $this->new_filter->where($item_condition)->find();
                            
                            //去掉品牌后面的规格 by bright
                            $item_name = $this->getBrandName($item_info_res['name']);
                            //只显示四个品牌   wql@20150928
                            $show_brand = array( 
                                '1'=>'曼牌',
                                '2'=>'马勒',
                                '3'=>'博世',
                                '4'=>'索菲玛',
                                '4'=>'长安',
                                '5'=>'标致',
                                '6'=>'奇瑞'
                            );
                            if(!in_array($item_name, $show_brand)){
                                continue;
                            }
                            
                            
                            
							if($item_info_res) {
								$item_info['id'] = $item_info_res['id'];
								$item_info['name'] = $item_info_res['name'];
								$item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
								$item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
								$item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
								$item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
								$item_set[$k][$_k] = $item_info;
								//排除数组中缺乏元素页面选项空白的问题
								if (!$item_set[$k][0] and $item_set[$k][1]) {
									$item_set[$k][0] = $item_set[$k][1];
									unset($item_set[$k][1]);
								}
							}else{
								continue;
							}
						}
                        
//						foreach($item_set[$k] as $kk=>$vv){
//							if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
//								$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
//								$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
//							}else{
//								$item_set_new[$k][$kk] = $item_set[$k][$kk];
//							}
//						}
	
                        //重置索引
                        $item_set[$k] =  array_values($item_set[$k]) ;

                         // wql && jichi @20150923
                        $price = array() ;
                        foreach ($item_set[$k] as $kk => $vv) {
                            $price[$kk] = $vv['price'] ;
                        }
                        asort($price) ;
                        foreach($price as $kkk =>$vvv){
                           $item_set_new[$k][] =  $item_set[$k][$kkk] ;
                        }
                        
						$item_set = $item_set_new;
					}
				}
			}
		}

		//处理套餐配件
		$no_item = array(4,13,15,16,28,29,30,31,32,35,50,53,54,62,65,67,69,71,72,73);
		/*if($_SESSION['order_type']==13 or $_SESSION['order_type']==15){
			//好车况套餐和好动力套餐，没有可选配件
			unset($item_set);
		}*/

		if(in_array($_SESSION['order_type'],$no_item)){
			//不需要配件的套餐
			unset($item_set);
		}
		if($_SESSION['order_type']=='14z' or $_SESSION['order_type']==14 or $_SESSION['order_type']==33 or $_SESSION['order_type']==47 or $_SESSION['order_type']==48  or $_SESSION['order_type']==49 or $_SESSION['order_type']==52 or $_SESSION['order_type']==55 or $_SESSION['order_type']==63 or $_SESSION['order_type']==68){
			//好车况套餐和好动力套餐，没有可选配件
			unset($item_set[1]);
			unset($item_set[2]);
		}
		if($_SESSION['order_type']=='14z'){
			//好空气套餐16.8 ,奥迪宝马奔驰除外
			if ($_SESSION['admin_brand_id']==1 OR $_SESSION['admin_brand_id']==3 OR $_SESSION['admin_brand_id']==4) {
				$tips = "该车型无法使用该套餐码（奥迪，宝马，奔驰不能是用该券码！）";
				$this->assign('tipss', $tips);
			}
		}
		//防雾霾活动强制选择防雾霾空调滤
		/*if($_SESSION['order_type']==47 or $_SESSION['order_type']==49){
			foreach($item_set[3] as $k=>$v){
				if($v['id']<764){ unset($item_set[3][$k]);}
			}
			/*if(empty($item_set[3])){
				 $item_set[3][0] = array(
                    'id' => '725',
                    'name' => 'pm2.5公共',
                    'unit_price' => '0',
                    'number' => '0',
                    'price' => '0',
                    'type' => '0'
                );
			}else{
				/*$a = array_keys($item_set[3]);
				if($a[0]!=0){
					foreach($item_set[3] as $k=>$v){
						if($k!=0){
							$item_set[3][] = $item_set[3][$k];
							unset($item_set[3][$k]);
						}
					}
				}
			//}
		}*/
		//补单添加所有可选机油的1升选项
		if($_SESSION['order_type']==34){
			foreach( $oil_name_distinct as $keys=>$names ){
				
				$names['name'] = rtrim($names['name'],'装');
				
				$item_sets_bu[$keys]['id'] = $names['id'];
				$item_sets_bu[$keys]['name'] = $names['name'];
				//规格1L详情
				$item_sets_bu[$keys]['oil_1'] = $oil_item[$names['name']][1]['id'];
				$item_sets_bu[$keys]['oil_1_num'] = 1;
				$item_sets_bu[$keys]['oil_1_price'] = $oil_item[$names['name']][1]['price'];
				//规格4L详情
				$item_sets_bu[$keys]['oil_2'] = '';
				$item_sets_bu[$keys]['oil_2_num'] = '';
				$item_sets_bu[$keys]['oil_2_price'] = '';
		
				//计算总价
				$item_sets_bu[$keys]['price'] = 1*$oil_item[$names['name']][1]['price'];
				//干掉4升以上金嘉护的选项
				if($car_style['oil_mass']>4 and $names['id']==56){ unset($item_sets_bu[$keys]); }
			}
		}
		//淘宝订单数组
		$tb_array = array('13','14','15','16','17','18','19','28','29','30','31','32','33','34','54','60','61','71','72');
		if(in_array($_SESSION['order_type'],$tb_array)){
			$this->assign('tbid_show', 1);
		}

		$this->assign('authId', $_SESSION['authId']);
		$this->assign('order_type', $_SESSION['order_type']);
		$this->assign('car_style', $car_style);
		$this->assign('item_sets', $item_sets);
		$this->assign('item_sets_bu', $item_sets_bu);
		$this->assign('item_set', $item_set);
        
	
		$this->assign('title',"选择项目-上门保养-携车网");
	
		$this->display('sel_item');
	}
	
	/* public function order(){
		session_start();
		//黄壳
		if($_SESSION['order_type']==7 and $this->_post('CheckboxGroup0_res')!=49 and $this->_post('CheckboxGroup0_res')!=0){
			$this->error('套餐与所选机油类型不符');
		}
		//蓝壳
		if($_SESSION['order_type']==8 and $this->_post('CheckboxGroup0_res')!=47 and $this->_post('CheckboxGroup0_res')!=48 and $this->_post('CheckboxGroup0_res')!=0){
			$this->error('套餐与所选机油类型不符');
		}
		//灰壳
		if($_SESSION['order_type']==9 and $this->_post('CheckboxGroup0_res')!=45 and $this->_post('CheckboxGroup0_res')!=46 and $this->_post('CheckboxGroup0_res')!=0){
			$this->error('套餐与所选机油类型不符');
		}

		//金美孚
		if($_SESSION['order_type']==11 and $this->_post('CheckboxGroup0_res')!=50 and $this->_post('CheckboxGroup0_res')!=51 and $this->_post('CheckboxGroup0_res')!=0){
			$this->error('套餐与所选机油类型不符');
		}

		unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
	
		if($this->_post('CheckboxGroup0_res')){
			$_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res'));
			$_SESSION['oil_detail'] = array(
					$this->_post('oil_1_id') => $this->_post('oil_1_num'),
					$this->_post('oil_2_id') => $this->_post('oil_2_num')
			);
		}
		if($this->_post('CheckboxGroup1_res')){
			$_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res'));
		}
		if($this->_post('CheckboxGroup2_res')){
			$_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res'));
		}
		if($this->_post('CheckboxGroup3_res')){
			$_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res'));
		}
		$item_num = 1;
		if(!empty($_SESSION['item_0'])){
        	if( $_SESSION['item_0'] == '-1' ){
        		$item_list['0']['id'] = 0;
        		$item_list['0']['name'] = "自备配件";
        		$item_list['0']['price'] = 0;
        	}else{
        		//通过机油id查出订单数据
        		$item_oil_price = 0;
        		$oil_data = $_SESSION['oil_detail'];
        		foreach ( $oil_data as $id=>$num){
        			if($num > 0){
        				$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
        				$item_oil_price += $res['price']*$num;
        				$name = $res['name'];
        			}
        		}
        		$item_list['0']['id'] = $_SESSION['item_0'];
        		$item_list['0']['name'] = $name;
        		$item_list['0']['price'] = $item_oil_price;
        	}
        	$item_num++;
        }
        
        if(!empty($_SESSION['item_1'])){
        	if($_SESSION['item_1'] == '-1'){
        		$item_list['1']['id'] = 0;
        		$item_list['1']['name'] = "自备配件";
        		$item_list['1']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_1'];
        		$item_list['1'] = $this->item_model->where($item_condition)->find();
        		//$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));//去掉后面的规格 by bright
        	}
        	$item_num++;
        	
        }
        if(!empty($_SESSION['item_2'])){
        	if($_SESSION['item_2'] == '-1'){
        		$item_list['2']['id'] = 0;
        		$item_list['2']['name'] = "自备配件";
        		$item_list['2']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_2'];
        		$item_list['2'] = $this->item_model->where($item_condition)->find();
        		//$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
        	}
        	$item_num++;
        }
        if(!empty($_SESSION['item_3'])){
        	if($_SESSION['item_3'] == '-1'){
        		$item_list['3']['id'] = 0;
        		$item_list['3']['name'] = "自备配件";
        		$item_list['3']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_3'];
        		$item_list['3'] = $this->item_model->where($item_condition)->find();
        		//$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
        	}
        	$item_num++;
        }
	
		$item_amount = 99;
		if(is_array($item_list)){
			foreach ($item_list as $key => $value) {
				$item_amount += $value['price'];
			}
		}
		if($_SESSION['order_type']>3){
			$info = $this->get_typeprice($_SESSION['order_type'],$item_list['0']['price'],$item_list['1']['price'],$item_list['3']['price']);
		}

		$item_amount = $item_amount-$info['typeprice'];
		//初始化用户信息
		$userinfo = $this->reservation_order_model->where(array('uid'=>$_SESSION['user_id']))->order('create_time desc')->find();
		$spreadprize = D('spreadprize');
		$prize_info = $spreadprize->where(array('id'=>$_SESSION['exchange_id']))->find();
		if(!$userinfo['username']){ $userinfo['username'] = $prize_info['username']; }
		if(!$userinfo['address']){ $userinfo['address'] = $prize_info['address']; }
		$this->assign('userinfo',$userinfo);
		//城市数据
		$city_list = $this->city_model->select();
		//业务来源数据
		$business_source_list = array('1'=>'IB',
									'2'=>'OB',
									'3'=>'三星',
									'4'=>'百度',
									'5'=>'淘宝',
									'6'=>'优惠券',
									'7'=>'微信',
									'8'=>'微信活动',
									'9'=>'小区',
									'10'=>'事故车',
									'11'=>'介绍',
									'12'=>'平安好车',
									'13'=>'微博',
									'14'=>'搜索',
									'15'=>'朋友圈',
									'16'=>'加油站',
									'17'=>'宝驾',
									'18'=>'e袋洗',
									'19'=>'老客户',
									'20'=>'养车点点',
									'21'=>'驴妈妈',
									'22'=>'其他',
									'23'=>'4S店预约转化',
									'24'=>'上海平安',
									'25'=>'点评活动',
									'26'=>'光大',
									'27'=>'京东',
									'28'=>'人保',
									'29'=>'光大信用卡',
									'30'=>'淘宝现场活动',
									'31'=>'光大黄金信用卡',
									'32'=>'凹凸租车',
			                        '33'=>'建设银行',
									'34'=>'pad无配件订单',
									'35'=>'百度生活',
									'36'=>'通用',
									'37'=>'UBER车主',
									'38'=>'UBER活动',
			                        '39'=>'i保养'
		);
		$this->assign('business_source_list',$business_source_list);
		$this->assign('city_list',$city_list);
		$this->assign('taobao_id',$_REQUEST['CheckboxGroup5_res']);
		$this->assign('taobao_price',$_REQUEST['CheckboxGroup6_res']);
		$this->assign('order_type',$_REQUEST['CheckboxGroup4_res']);
		$this->assign('typeprice',$info['typeprice']);
		$this->assign('item_num',$item_num);
		$this->assign("item_list", $item_list);
		$this->assign("item_amount", $item_amount);
		$this->assign('title',"订单详情-上门保养-携车网");

		$this->display('order');
	} */
	
	
	public function create_order(){
		session_start();
		//if($_SESSION['authId']==1){
			//echo 'code='.$this->_post('code');exit;
		//}
		unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
		//存储配件session信息。
		if($this->_post('CheckboxGroup0_res')){
			$_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res'));
			$_SESSION['oil_detail'] = array(
					$this->_post('oil_1_id') => $this->_post('oil_1_num'),
					$this->_post('oil_2_id') => $this->_post('oil_2_num')
			);
		}
		if($this->_post('CheckboxGroup1_res')){
			$_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res'));
		}
		if($this->_post('CheckboxGroup2_res')){
			$_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res'));
		}
		if($this->_post('CheckboxGroup3_res')){
			$_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res'));
		}
        
        //echo  $_SESSION['item_3'] ;

		
		$yzm = $this->_post('yzm');
		if($yzm && $yzm == $_SESSION['mobileeverify']){
		}else{
			//$this->error('验证码不正确，预约失败');
		}
		
        $replace_code = $this->_post('code');
                
		$has_replace_code = false;
        if ($this->newCouponCodeModel->isValidNewCouponCode($replace_code, 'all')) {//新版优惠券可用
            $hasNewCoupon = true;
            $order_info['replace_code'] = $replace_code;
            $order_info['order_type'] = $this->newCouponCodeModel->getOrderType($order_info['replace_code']);

        }elseif($replace_code and $replace_code!='016888' and $replace_code!='j8610000'){
			$chk_code = $this->_check_replace_code($replace_code,$this->_post('mobile'),$_SESSION['admin_style_id'],$this->_post('licenseplate_type').$this->_post('licenseplate'));
			if(!$chk_code){
				$this->error('该抵用码不能使用，请重新填写');
			}else{
				//总价减去抵用码的价钱
				$has_replace_code = true;
				$order_info['replace_code']= $replace_code;
				$update['status'] = 1;
				$where = array('coupon_code'=>$replace_code);
                $first = substr($order_info['replace_code'],0,1);
                if($first==f){
                    $update['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
                    $update['model_id'] = $_SESSION['admin_style_id'];
                    $this->carservicecode1_model->where($where)->save($update);//改抵用码已经用过了，不能再用了
                }else{
				    $this->carservicecode_model->where($where)->save($update);//改抵用码已经用过了，不能再用了
                }
			}
	
		}elseif($replace_code=='016888' or $replace_code=='j8610000'){
			$has_replace_code = true;
			$order_info['replace_code']= $replace_code;
		}
		$order_info['uid'] = $_SESSION['user_id'];
		$order_info['truename'] = $this->_post('truename');
		$order_info['address'] = $this->_post('address');
		$order_info['mobile'] = $this->_post('mobile');
		$order_info['model_id'] = $_SESSION['admin_style_id'];
		$order_info['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
		if($this->_post('order_type')==34){
			$order_info['admin_id'] = 1;
		}else{
			$order_info['admin_id'] = @$_SESSION['authId'];
		}
		
		if($this->_post('car_reg_time')){
			$order_info['car_reg_time'] = strtotime($this->_post('car_reg_time'));
		}else{
			$order_info['car_reg_time'] = 0;
		}
		$order_info['engine_num'] = $this->_post('engine_num');
		$order_info['vin_num'] = $this->_post('vin_num');
		//$order_info['invoices_type'] = $this->_post('invoices_type');
		//$order_info['invoices_title'] = $this->_post('invoices_title');
	
		$order_info['order_time'] = $this->_post('order_time');
		$order_time2 = intval($this->_post('order_time2'));
		$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
	
		$order_info['create_time'] = time();
		$order_info['remark'] = '代下单: '.$this->_post('remark');
        if (!$hasNewCoupon) {//不使用新版优惠码才让选套餐
            if($_SESSION['order_type']){
                $order_info['order_type'] = $_SESSION['order_type'];
            }else{
                $order_info['order_type'] = $this->_post('order_type');
            }
        }
		//$order_info['taobao_id'] = $this->_post('taobao_id');
		if($this->_post('taobao_id')){
                    $order_info['taobao_id'] = $this->_post('taobao_id');
                }
		$order_info['city_id'] = $this->_post('city_id');
        $order_info['area_id'] = $this->_post('area_id');
		//杭州订单直接分给技师刘成
		if($order_info['order_type']>12 and $order_info['order_type']<20 or $order_info['order_type']==3 or( $order_info['order_type']>27 and $order_info['order_type']<34)){
			//$order_info['technician_id'] = 14;
			$order_info['pay_type'] = 4;//支付方式：淘宝
			//默认为分配技师状态
			// $order_info['status'] = 2;
		}

		if($this->_post('order_type')==2 or ($_SESSION['order_type']>12 and $_SESSION['order_type']<20) or ($_SESSION['order_type']>27 and $_SESSION['order_type']<34)){
			$order_info['pay_status'] = 1;
		}

		//京东支付
		//if($this->_post('business_source')=='27'){
        if($this->_post('business_source')=='8' and $this->_post('order_type')!=34){
            $order_info['pay_type'] = 7;//支付方式：京东
            $order_info['pay_status'] = 1;
        }

		//淘宝支付
		//if($this->_post('business_source')=='5'){
        if($this->_post('business_source')=='7'){
			$order_info['pay_type'] = 4;//支付方式：淘宝
			$order_info['pay_status'] = 1;
		}
                
        //点评到家
		//if($this->_post('business_source')=='40'){
        if($this->_post('business_source')=='33' and $this->_post('order_type')!=34){
			$order_info['pay_type'] = 10;//支付方式：点评到家
			$order_info['pay_status'] = 1;
		}
                
                

        //天猫
        //if($this->_post('business_source')=='53'){
        if($this->_post('business_source')=='31'){
            $order_info['pay_type'] = 12;//支付方式：天猫
            $order_info['pay_status'] = 1;
        }

		$dianping_array = array('36','37','38','50','51');
		//大众点评支付
		//if($this->_post('business_source')=='25' or in_array($this->_post('order_type'),$dianping_array)){
        if($this->_post('business_source')=='9' or in_array($this->_post('order_type'),$dianping_array)){
				$order_info['pay_type'] = 5;//支付方式：大众点评
		}

		//渠道
		$order_info['business_source'] = $this->_post('business_source');

		$oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;
		if($_SESSION['item_0']>0){ 
			foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
				$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
				$total_norms+=$res['norms']*$_num;
			}
		}
		//订单不是淘宝支付的套餐且不是补单，并且订单机油大于4升或者有多余的空气滤，空调滤就进行拆单
        $ruleOut = array(1,2,3,13,14,15,16,33,34,47,48,49,52,55,60,61,68);
        //大保养数组
        $keepArr = array(60,61);
        
		if(!empty($order_info['order_type']) and !in_array($order_info['order_type'],$ruleOut) and ($total_norms>4 or $_SESSION['item_2']>0 or $_SESSION['item_3']>0) or (in_array($order_info['order_type'],$keepArr)and $total_norms>4)){
        //当机油大于4升，拆机油
			if($total_norms>4){
				foreach ( $_SESSION['oil_detail'] as $id=>$num){
					$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
					$norms+=$res['norms']*$num;
					if($res['norms']==1){
						$one_id = $id;
						unset($_SESSION['oil_detail'][$id]);
					}else{
						$four_id = $id;
						$_SESSION['oil_detail'][$id] = 1;
					}
				}

				$left_norms = $norms-4;
				$aa = $left_norms%4;
				$bb = ($left_norms - $aa)/4;
				$oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
				//print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

				/*计算拆分订单的配件数据---START---*/
				if($oil_detail2){
					//通过机油id查出订单数据
					$item_oil_price = 0;
					$i = 0;
					foreach ( $oil_detail2 as $_a=>$_b){
						if($_b > 0){
							$res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
							if ($i == 0) {
								$oil_1_id = $_a;
								$oil_1_price = $res['price']*$_b;
								$i++;
							}else{
								$oil_2_id = $_a;
								$oil_2_price = $res['price']*$_b;
							}
							$item_oil_price += $res['price']*$_b;
							$name = $res['name'];
						}
					}
					$item_list['0']['id'] = $_SESSION['item_0'];
					$item_list['0']['name'] = $name;
					$item_list['0']['price'] = $item_oil_price;
				}
			}
			
			if($_SESSION['item_2'] && !in_array($order_info['order_type'],$keepArr)){
				$kongqi_id2 = $item_condition['id'] = $_SESSION['item_2'];
				$item_list['2'] = $this->new_filter->where($item_condition)->find();
				$kongqi_price2 = $item_list['2']['price'];
				$_SESSION['item_2'] = 0;
			}
			if($_SESSION['item_3'] && !in_array($order_info['order_type'],$keepArr)){
				$kongtiao_id2 = $item_condition['id'] = $_SESSION['item_3'];
				$item_list['3'] = $this->new_filter->where($item_condition)->find();
				$kongtiao_price2 = $item_list['3']['price'];
				$_SESSION['item_3'] = 0;
			}
			$item_amount2 = 0;
			if(is_array($item_list)){
				foreach ($item_list as $key => $value) {
					$item_amount2 += $value['price'];
				}
			}
			unset($item_list);
			$item_content2 = array(
					'oil_id'     =>	$_SESSION['item_0'],
					'oil_detail' => $oil_detail2,
					'filter_id'  => -1,
					'kongqi_id'  =>  $kongqi_id2,
					'kongtiao_id' =>  $kongtiao_id2,
					'price'=>array(
							'oil'=>array(
									$oil_1_id=>$oil_1_price,
									$oil_2_id=>$oil_2_price
							),
							'filter'=>array(
									$filter_id=>$filter_price
							),
							'kongqi'=>array(
									$kongqi_id2=>$kongqi_price2
							),
							'kongtiao'=> array(
									$kongtiao_id2=>$kongtiao_price2
							)
					)
			);
			 
			$item2 = serialize($item_content2);
			/*计算拆分订单的配件数据---END---*/
		}

		//计算总价
		if($_SESSION['item_0']){
			//通过机油id查出订单数据
			$item_oil_price = 0;
			$oil_data = $_SESSION['oil_detail'];
			$i = 0;
			foreach ( $oil_data as $id=>$num){
				if($num > 0){
					$res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
			$item_list['0']['id'] = $_SESSION['item_0'];
			$item_list['0']['name'] = $name;
			$item_list['0']['price'] = $item_oil_price;
		}
		if($_SESSION['item_1']){
			$filter_id = $item_condition['id'] = $_SESSION['item_1'];
			$item_list['1'] = $this->new_filter->where($item_condition)->find();
			$filter_price = $item_list['1']['price'];
		}
		if($_SESSION['item_2']){
			$kongqi_id = $item_condition['id'] = $_SESSION['item_2'];
			$item_list['2'] = $this->new_filter->where($item_condition)->find();
			$kongqi_price = $item_list['2']['price'];
		}
		if($_SESSION['item_3']){
			$kongtiao_id = $item_condition['id'] = $_SESSION['item_3'];
			$item_list['3'] = $this->new_filter->where($item_condition)->find();
			$kongtiao_price = $item_list['3']['price'];
		}
        

		$item_amount = 0;
		if(is_array($item_list)){
			foreach ($item_list as $key => $value) {
				$item_amount += $value['price'];
			}
		}
		 
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
		 
		$order_info['item'] = serialize($item_content);
		 
    	$value = 0;
        if ($hasNewCoupon) {
            $value = $this->newCouponCodeModel->getDiscountAmount($order_info['replace_code']);
            if ($order_info['order_type'] == '1') {
                $order_info['dikou_amount'] = $value;
                $order_info['amount'] = $item_amount + 99 - $value;
                $order_info['remark'] = $order_info['remark'].'后台下单新版保养优惠价格:'.$value.'元';
            } else {
                $order_info['dikou_amount'] = $value;
                $order_info['amount'] = $this->newCouponCodeModel->getAmount($order_info['replace_code']) - $value;
                $order_info['remark'] = $order_info['remark'].'后台下单新版基础服务优惠价格:'.$value.'元';
            }
        } else {
            if($has_replace_code){
                //根据优惠券渠道分类
                $info = $this->carservicecode_model->where(array('coupon_code'=>$order_info['replace_code']))->find();
                if($info['source_type']==1){
                    if($first==a){
                        $order_info['order_type'] = 64;
                        $order_info['amount'] = $item_amount+99;
                    }
                    if($first==b){
                        $order_info['order_type'] = 63;
                        $order_info['amount'] = $item_amount+99;
                    }
                    if($first==d){
                        $order_info['order_type'] = 54;
                        $order_info['amount'] = $item_amount+99;
                    }
                }else{
                    //套餐不进折扣券计算
                    $first = substr($order_info['replace_code'], 0, 1);
                    if ($first == c) {
                        $order_info['order_type'] = 7;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == d) {
                        $order_info['order_type'] = 8;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == e) {
                        $order_info['order_type'] = 10;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == h) {
                        $order_info['order_type'] = 11;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == i) {
                        $order_info['order_type'] = 12;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == j) {
                        $j_num = substr($order_info['replace_code'], -5, 5);
                        if (($j_num >= 49751 and $j_num <= 50000) or ($j_num >= 49001 and $j_num <= 49250)) {
                            $order_info['business_source'] = 32;
                        }
                        $order_info['order_type'] = 35;
                        $order_info['amount'] = 99;
                    } elseif ($first == p) {
                        $order_info['order_type'] = 2;
                        $order_info['business_source'] = 32;
                    } elseif ($first == z) {
                        $order_info['order_type'] = 14;
                        $order_info['remark'] = "uber好空气16.8优惠券码0元下单";
                        $order_info['business_source'] = 22;
                        $order_info['amount'] = 0;
                        $order_info['dikou_amount'] = $item_content['price']['kongtiao'][$kongtiao_id] + 99;
                    } elseif ($first == r or $first == y) {
                        $order_info['order_type'] = 7;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == s or $first == x) {
                        $order_info['order_type'] = 8;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == t or $first == w) {
                        $order_info['order_type'] = 10;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == u) {
                        $order_info['order_type'] = 59;
                        $order_info['amount'] = $item_amount + 99;
                    } elseif ($first == v) {
                        $order_info['amount'] = $item_amount + 99;
                        $order_info['remark'] = $order_info['remark'] . '抵扣套餐价格￥' . $order_info['dikou_amount'] . ';安盛天平财险';
                    } else {
                        $order_info['dikou_amount'] = $value = $this->get_codevalue($order_info['replace_code']);
                        if ($value != 99) {
                            $order_info['amount'] = $item_amount + 99 - $value;
                        } else {
                            $order_info['amount'] = $item_amount;
                        }
                    }
                }
            }else{
                $order_info['amount'] = $item_amount + 99;
            }
            //如果是上门保养且有99元优惠券
            if($order_info['order_type']==1 and $has_replace_code){
                $order_info['dikou_amount'] = $value;
            }
            //如果是检测订单，免费
            if($order_info['order_type']==2){
                $order_info['amount'] = 0;
            }
            //9.8元检测单
            if($order_info['order_type']==4){
                $order_info['amount'] = 9.8;
                $order_info['dikou_amount'] = 89.2;
                $order_info['remark'] = $order_info['remark'].'抵扣套餐价格￥89.2;9.8元检测套餐:38项全车检测+7项细节养护';
            }
            if($order_info['order_type']==47){
                $order_info['amount'] = 0;
                $order_info['dikou_amount'] = 99;
            }
            //如果是淘宝已支付订单
            if($order_info['order_type']==3){
                $order_info['dikou_amount'] = $order_info['amount'];
                $order_info['amount'] = 0;
            }

            //计算油桶套餐价
            if($order_info['order_type']>4 and $order_info['order_type']!=47){
                $info = $this->get_typeprice($order_info['order_type'],$item_oil_price,$filter_price,$kongtiao_price,$kongqi_price);

                //$taobao_price = $this->_post('taobao_price');
                if($this->_post('taobao_price')){
                    $taobao_price = $this->_post('taobao_price');
                }


                if(isset($taobao_price) and is_numeric($taobao_price)){
                    if($_SESSION['authId']==1){
                        //echo $order_info['amount'].' '.$this->_post('taobao_price');exit;
                    }
                    $order_info['amount'] = $this->_post('taobao_price');
                    $order_info['dikou_amount'] = $item_amount + 99 -$this->_post('taobao_price');
                    $order_info['remark'] = $order_info['remark'].'手动抵扣套餐价格￥'.$order_info['dikou_amount'].';'.$info['remark'];
                } elseif($order_info['order_type']==14 and $_SESSION['order_type'] =='14z'){
                    $order_info['amount'] = 0;
                    $order_info['dikou_amount'] = $item_content['price']['kongtiao'][$kongtiao_id]+99;
                    $order_info['remark'] = "uber好空气16.8优惠券码0元下单";
                    $order_info['pay_status'] = 1;
                    $order_info['pay_type'] = 1;

                }else{
                    $order_info['amount'] = $order_info['amount']-$info['typeprice'];
                    $order_info['dikou_amount'] = $info['typeprice']+$value;
                    $order_info['remark'] = $order_info['remark'].'抵扣套餐价格￥'.$info['typeprice'].';'.$info['remark'];
                }

                if($order_info['order_type']>24 and $order_info['order_type']<28){
                    $order_info['price_remark'] = '浦发减30活动：刷浦发卡，POS机收款'.($order_info['amount']-30).'元';
                }
                //更新兑换记录
                $spreadprize = D('spreadprize');
                $data['exchange_type'] = 2;
                $data['exchange_time'] = time();
                $spreadprize ->where(array('id'=>$_SESSION['exchange_id']))->save($data);
            }elseif($order_info['order_type']==47){
                $order_info['remark'] = $order_info['remark'].'防雾霾空调滤活动';
            }
            if($info['order_type']==12){
                $order_info['amount'] = '9.80';
            }
            $order_info['origin'] = 3;
            //如果由于某些奇葩套餐，导致价格为负数，就直接0元
            if($order_info['amount']<0){
                $order_info['amount'] = '0.00';
            }

            //套餐和优惠码叠加
            if($first==v){
                //echo $order_info['amount'];exit;
                if($order_info['order_type']==1 or $order_info['order_type']==''){//如果没有配件，免人工费
                    $order_info['amount'] = $order_info['amount']-99;
                    $order_info['dikou_amount'] = 99;
                }else{//如果是套餐就减20
                    $order_info['amount'] = $order_info['amount']-20;
                    $order_info['dikou_amount'] = $order_info['dikou_amount']+20;
                }
            }

            //判断ordertype是否为空
            if($order_info['order_type'] == ''){
                unset($order_info['order_type']);
            }
            if($first==r or $first==s or $first==t or $first==u){
                $order_info['remark'] = $order_info['remark'].'发动机舱深度喷雾精洗,7项细节养护,38项全车检测';
            }
            if($first=='b' and strlen($order_info['replace_code'])==11) {
                $order_info['amount'] = 0;
                $order_info['dikou_amount'] = 99 - $order_info['amount'];
                $order_info['order_type'] = 59;
                $car_code['coupon_code'] = $order_info['replace_code'];
                $code_info = $this->carservicecode_model->where($car_code)->find();
                if( $code_info){
                    $order_info['remark'] = '后台待下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';'.$code_info['remark'];
                }else{
                    $order_info['remark'] = '后台待下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';发动机舱精洗';
                }
            }
        }

		$res = $this->reservation_order_model->data($order_info)->add();
		//echo $this->reservation_order_model->getLastsql();exit;

        if ($res && $hasNewCoupon) {//使用新版优惠券
            $this->newCouponCodeModel->useNewCoupon($order_info['replace_code']);
        }

		if($item_content2){
			$order_info2 = $order_info;
			$order_info2['item'] = $item2;
			$order_info2['amount'] = $item_amount2;
			$order_info2['dikou_amount'] = 99;
			$order_info2['remark'] = '代下单：补订单'.$res.'机油';
			$order_info2['order_type'] = 34;
			$order_info2['pay_type'] = 1;
			$order_info2['pay_status'] = 0;
			$order_info2['admin_id'] = 1;
            $order_info2['business_source'] = 47;
			$res = $this->reservation_order_model->data($order_info2)->add();
			//echo $this->reservation_order_model->getLastsql();exit;
		}
        //重复使用码
        if($order_info['replace_code']=='d2762600087'){
            $data['use_count'] = array('exp','use_count+1');
            $data['status'] = 0;
            $this->carservicecode_model->where(array('coupon_code'=>$order_info['replace_code']))->save($data);
        }

		$this->success('预约成功',U('Carserviceorder/index'));
	}
	//检查抵用码能否使用
    private  function _check_replace_code($replace_code,$mobile,$model_id,$licenseplate){
    	$where = array(
    			'coupon_code' => $replace_code,
    			'status' => 0
    	);
    	if(strlen($replace_code) == 4){
    		$where['mobile'] = $mobile;
			$where['start_time'] = array('elt',time());
			$where['end_time'] = array('egt',time());
    	}
		$first = substr($replace_code,0,1);
		if($first == f){
			unset($where['status']);
		}
    	$count = $this->carservicecode_model->where($where)->count();
        $count1 = $this->carservicecode1_model->where($where)->find();
    	if($count > 0){
    		return true;
    	}elseif($count1){
			//有码且数据正确的情况下，通过验证
			if($count1['model_id']!=0 and $count1['licenseplate']!=null){
				if($count1['model_id']!=$model_id or $count1['licenseplate']!=$licenseplate){
					return false;
				}else{
					return true;
				}
			}else{
				return true;  
			}
			return true;
		}else{
    		return false;    
    	}
    }
	


	/**
     * 上门保养预约订单列表
     * @date 2014/8/13
     */
	public function index() {
		//排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $this->reservation_order_model->getPk();
           // echo $order;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

		//数据转换
        if (method_exists($this, '_get_order_sort')) {
            $order_sort_arr = $this->_get_order_sort();
			print_r($order_sort_arr);
            $order = $order_sort_arr['order'];
            $sort = $order_sort_arr['sort'];
        }
        
        //查询从哪里来的
        $searchSource = false;
        if ( !empty($_POST['remark']) ) {
        	$map['replace_code']=array('NEQ','NULL');
        	$this->assign('remark',$_POST['remark']);
        	$searchSource = true;
        }
		//查询是否绑定微信
        $searchWeixin = false;
		if(!empty($_REQUEST['weixinbind'])){
			$searchWeixin = true;
			$this->assign('weixinbind',$_REQUEST['weixinbind']);
		}
        //搜索
        if($_POST['id']){
            $map['id'] = $_POST['id'];
        }
        if($_POST['mobile']){
            $map['mobile'] = $_POST['mobile'];
        }
        if($_REQUEST['city_id'] and $_REQUEST['city_id']!='all'){
            $map['city_id'] = $_REQUEST['city_id'];
        }
        if($_POST['licenseplate']){
            $map['licenseplate'] = array('like','%'.$_POST['licenseplate'].'%');
			$this->assign('licenseplate',$_POST['licenseplate']);
        }
        if($_REQUEST['technician_id']){
            $map['technician_id'] = $_REQUEST['technician_id'];
        }
		if($_POST['address']){
            $map['address'] = array('like','%'.$_POST['address'].'%');
			$this->assign('address',$_POST['address']);
        }
        if($map['technician_id']==0){
            unset($map['technician_id']);
			unset($_POST['technician_id']);
        }
		if($_REQUEST['customer_id']){
            $map['admin_id'] = $admin_id = $_REQUEST['customer_id'];
			$this->assign('admin_id',$admin_id);
        }
		if($_REQUEST['order_type']){
            $map['order_type'] = $_REQUEST['order_type'];
        }
		if($_REQUEST['business_source']){
            $map['business_source'] = $_REQUEST['business_source'];
        }
        if($_REQUEST['business_source_old']){
            $map['business_source_old'] = $_REQUEST['business_source_old'];
        }
		if($_REQUEST['pay_type'] and $_REQUEST['pay_type']!='all'){
			if($_REQUEST['pay_type']==1){
				$map['pay_type'] = array('in','0,1');
			}else{
				$map['pay_type'] = $_REQUEST['pay_type'];
			}
        }else{
			unset($_REQUEST['pay_type']); 
		}
		if($_REQUEST['status'] and $_REQUEST['status']!='all' and $_REQUEST['status']!='99'){
            $map['status'] = $status = $_REQUEST['status'];
        }elseif($_REQUEST['status']=='all'){
			if($_REQUEST['type']==2 and !$_REQUEST['is_del']){
				//$status = 'all';
			}elseif($_REQUEST['type']==2 and $_REQUEST['is_del']==1){
				$map['status'] = array('neq',8);
			}elseif(!$_REQUEST['type']){
				//$status = 'all';
				$map['status'] = array('neq',8);
			}
			$status = 'all';
		}elseif($_REQUEST['status']=='99'){
			$map['status'] = array('in','1,2');
			$status = $_REQUEST['status'];
		}elseif(!$_REQUEST['status']){
			$map['status'] = $status = 0;
		}
 
		if( $_REQUEST['start_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])));
			$this->assign('time_type',$_REQUEST['time_type']);
			$this->assign('start_time',$_REQUEST['start_time']);
		}
		if( $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
			$this->assign('time_type',$_REQUEST['time_type']);
			$this->assign('end_time',$_REQUEST['end_time']);
		}
		if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
			$this->assign('time_type',$_REQUEST['time_type']);
			$this->assign('start_time',$_REQUEST['start_time']);
			$this->assign('end_time',$_REQUEST['end_time']);
		}
		//显示预约时间在当前时间2小时之前的未完成订单
		if($_REQUEST['is_delay']){
			$map['status'] = array('not in','8,9');
			$map['order_time'] = array('lt',time()-7200);
		}
		if (!empty($_REQUEST['order_origin'])) {
			$origin = $_REQUEST['order_origin'];
			if ($origin == 1) {	//pc
				$map['pa_id'] = array('exp','is null');
				$map['remark'] = array('notlike','%代下单%');
			}elseif ($origin == 2){	//微信
				$map['pa_id'] = array('exp','is not null');
			}elseif ($origin == 3){	//后台
				$map['remark'] = array('like','%代下单%');
			}elseif ($origin == 4){	//app
                $map['origin'] = '8';
            }
			$this->assign('origin',$origin);
		}

		if($_REQUEST['list_type']=='bill'){
			$this->assign('list_type',$_REQUEST['list_type']);
		}
		if($_SESSION['authId']==315 or $_SESSION['authId']==310 or $_SESSION['authId']==286 or $_SESSION['authId']==287 or $_SESSION['authId']==288 or $_SESSION['authId']==271 or $_SESSION['authId']==366 or $_SESSION['authId']==384 or $_SESSION['authId']==392 or $_SESSION['authId']==248){
			//根据城市划分可查看订单权限
			$map['city_id'] = array('in',$_SESSION['city_id']);
		}

        $count = $this->reservation_order_model->where($map)->count();
        
		// 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}

		$this->assign('search','/'.$p->parameter);
		if(!$_REQUEST['status'] and !$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
        
        // 分页显示输出
        $page = $p->show_admin();

        // 当前页数据查询
        if ( !empty($_POST['remark']) or !empty($_REQUEST['list_type'])) {
			$list = $this->reservation_order_model->where($map)->order($order . ' ' . $sort)->select();
		}else{
			$list = $this->reservation_order_model->where($map)->order($order . ' ' . $sort)->limit($p->firstRow.','.$p->listRows)->select();
		}
		if($_SESSION['authId']==1){
			echo $this->reservation_order_model->getLastsql();
		}

		$source_count = 0;
        if(is_array($list)){

            // 如果有订单来源 dingjb 2015年9月15日09:44:58
            if ($searchSource) {
                // 唯一手机号集合
                $tmpDistinctMobiles = array();

                // 唯一车牌号集合
                $tmpDistinctLicense = array();

                // 唯一地址集合
                $tmpDistinctAddress = array();

                // 绝对唯一集合
                $tmpAbsulute        = array();

                // 订单总价
                $source_price       = 0;

            }

            foreach ($list as $key => $value) {
                //判断实例化新配件表品牌车型表还是老配件表品牌车型表
                $this->getNeedModel($value['create_time']);
                
				//查询从哪里来的
            	if ($searchSource) {
            		$code_info = $this->carservicecode_model->field('remark')->where(array('coupon_code'=>$value['replace_code']))->find();
            		if ($code_info['remark'] != $_POST['remark']) {
            			unset($list[$key]);
            			continue;
            		}else{
						$source_count++;
						$source_price+=$value['amount'];

                        // 组装唯一手机号的订单
                        $tmpDistinctMobiles[] = $value['mobile'] . '|' . $value['order_time'];

                        // 组装唯一车牌号的订单
                        $tmpDistinctLicense[] = $value['licenseplate'] . '|' .$value['order_time'];

                        // 组装唯一地址的订单
                        $tmpDistinctAddress[] = $value['address'] . '|' . $value['order_time'];

                        // 组装绝对唯一有效的订单
                        $tmpAbsulute[]        = $value['address'] . '|' . $value['mobile'] . '|' . $value['licenseplate'] . '|' . $value['order_time'];

					}
            	}
				//该订单电话是否绑定微信
				if ($searchWeixin) {
					$Paweixin = $this->PaweixinModel->where(array('mobile'=>$value['mobile']))->find();
					if(!$Paweixin and $_REQUEST['weixinbind']==2){
            			unset($list[$key]);
            			continue;
					}elseif($Paweixin and $_REQUEST['weixinbind']==1){
						unset($list[$key]);
            			continue;
					}
				}
            	$list[$key]['show_id'] = $this->get_orderid($value['id']).'('.$value['id'].')';
            	$list[$key]['id'] = $this->get_orderid($value['id']);
				$list[$key]['true_id'] = $value['id'];
				if($value['admin_id']>0){
					$operate_id = $value['admin_id'];
				}else{
					$model_operatelog =  M("tp_xieche.operatelog","xc_");
					$operate_info = $model_operatelog->where(array('oid'=>$value['id']))->order('create_time asc')->find();
					$operate_id = $operate_info['operate_id'];
				}
				$model_user =  M("tp_admin.user","xc_");
                $user_info = $model_user->where(array('id'=>$operate_id))->order('create_time asc')->find();
				$list[$key]['operate'] = $user_info['nickname'];
				$list[$key]['operate_id'] = $operate_id;
                $list[$key]['status_name'] = $this->getStatusName($value['status']);
				if($_REQUEST['list_type']==''){
					if($value['order_type']!=2){
						if($value['pay_type']==1 or $value['pay_type']==0){
							$pay_type = '(现)';
						}elseif($value['pay_type']==2){
							$pay_type = '(微)';
						}elseif($value['pay_type']==3){
							$pay_type = '(P)';
						}elseif($value['pay_type']==4){
							$pay_type = '(淘)';
						}elseif($value['pay_type']==7){
							$pay_type = '(京)';
						}elseif($value['pay_type']==5){
							$pay_type = '(大)';
						}elseif($value['pay_type']==6){
							$pay_type = '(建)';
						}elseif($value['pay_type']==8){
							$pay_type = '(养)';
						}elseif($value['pay_type']==9){
							$pay_type = '(支)';
						}elseif($value['pay_type']==10){
							$pay_type = '(家)';
						}elseif($value['pay_type']==11){
                            $pay_type = '(WAP)';
                        }elseif($value['pay_type']==12){
                            $pay_type = '(猫)';
                        }elseif($value['pay_type']==13){
                            $pay_type = '(同)';
                        }
                                                
                                                
						if($value['pay_status']==0){
							$list[$key]['amount'] = '<font color=red>'.$value['amount'].$pay_type.'</font>';
						}else{
							$list[$key]['amount'] = '<font color=green>'.$value['amount'].$pay_type.'</font>';
						}
					}else{
						$list[$key]['amount'] = '上门检测订单';
					}
				}else{
					$paytype = array('0'=>'现金','1'=>'现金','2'=>'微信','3'=>'POS','4'=>'淘宝','5'=>'大众点评','6'=>'建行','7'=>'京东','8'=>'养车点点','9'=>'支付宝', '10'=>'点评到家', '11'=>'支付宝');
					$list[$key]['pay_type'] = array_key_exists($value['pay_type'], $paytype) ? $paytype[$value['pay_type']] : '未知方式';
				}
                if($value['technician_id']){
                    $condition['id'] = $value['technician_id'];
					//$condition['status'] = 1;
                    $technician_info = $this->technician_model->where($condition)->find();
                    $list[$key]['technician_name'] = $technician_info['truename'].'('.$technician_info['id'].')';
					//已绑定标记
					if($Paweixin){
						$list[$key]['technician_name'] = $list[$key]['technician_name'].'(已绑定)';
					}else{
						$list[$key]['technician_name'] = $list[$key]['technician_name'].'(未绑定)';
					}
                }
				$sum_amount = $sum_amount+$value['amount'];
				//获取优惠券用途
				if($value['replace_code'] and $value['replace_code']!='016888'){
					$code_info = $this->carservicecode_model->where(array('coupon_code'=>$value['replace_code']))->find();
					if(!$code_info){
						$code_info = $this->carservicecode1_model->where(array('coupon_code'=>$value['replace_code']))->find();
					}
                    if ($this->newCouponCodeModel->isNewCouponCode($value['replace_code'])) {//新版优惠券名称作为用途
                        $code_info['remark'] = $this->newCouponCodeModel->getCouponName($value['replace_code']);
                    }
					$list[$key]['replace_code'] = $list[$key]['replace_code']."</br>(".$code_info['remark'].")";
				}
				if($value['replace_code']=='016888'){
					$list[$key]['replace_code'] = $list[$key]['replace_code']."</br>(市场部通用券)";
				}
				//拼车型
				$model_id = $value['model_id'];
				$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
				$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
				$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
				$list[$key]['car_name'] = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];
				//配件数据
				$order_items = unserialize($value['item']);
				$oil_data = $order_items['oil_detail'];
				$oil = '';
				foreach($oil_data as $_id=>$num){
					if($num>0){
						$info = $this->item_oil_model->where(array('id'=>$_id))->find();
						$oil = $oil.$info['name'].' '.$info['norms']."L : ".$num."件;";
					}
				}

				if($oil){
					$list[$key]['oil'] = $oil;
				}else{ //机油不存在，取出套餐名称
					$rs = $this->package_model->field('name')->where('order_type='.$value['order_type'])->find();
					$list[$key]['oil'] = $rs['name'];
				}
				
				
				
				$info = $this->item_model->where(array('id'=>$order_items['filter_id']))->find();
				$list[$key]['filter'] = $info['name'];
				$info = $this->item_model->where(array('id'=>$order_items['kongqi_id']))->find();
				$list[$key]['kongqi'] = $info['name'];
				$info = $this->item_model->where(array('id'=>$order_items['kongtiao_id']))->find();
				/*if( $info['id']==765){
				}*/
				$list[$key]['kongtiao'] = $info['name'];
            }
        }
		//echo $this->reservation_order_model->getLastsql();
		//print_r($list);
        $condition = array();
        $condition['status'] = 1;
        if($_SESSION['authId']==278){
            $condition['city_id'] = array('in',$_SESSION['city_id']);
        }
        $technician_list = $this->technician_model->where($condition)->select();

		//列表排序显示
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'asc' ? '升序排列' : '倒序排列'; //排序提示
        $sort = $sort == 'asc' ? 0 : 1; //排序方式

		$admin['status'] = 1;
		$admin['remark'] = '客服';
        $customer_list = $this->admin_model->where($admin)->select();

		//合计明细数据处理,有来源和没来源分开处理
		if($searchSource){
			$priceInfo[] = array(
                'name'=>'全部',
                'orderCount' => $source_count,
                'distinctMobile' => is_array($tmpDistinctMobiles) ? count(array_unique($tmpDistinctMobiles)) : $source_count,
                'distinctLicenseplate' => is_array($tmpDistinctLicense) ? count(array_unique($tmpDistinctLicense)) : $source_count,
                'distinctAddress' => is_array($tmpDistinctAddress) ? count(array_unique($tmpDistinctAddress)) : $source_count,
                'absoluteCount' => is_array($tmpAbsulute) ? count(array_unique($tmpAbsulute)) : $source_count,
                'value'=> $source_price
			);
		}else{
			// 计算价格
			$priceInfo = array();
			//计算总价
			$countPrice = $this->reservation_order_model->where($map)->sum('amount');
			if ($countPrice) {
                // 全部个数
				$countPriceCount = $this->reservation_order_model->where($map)->count();

                // 去除非法手机号
                $eliminateIllegalMobile = $map;
                $eliminateIllegalMobile['_string'] = 'CHAR_LENGTH(mobile) = 11';

                // 唯一手机号个数
                $distinctMobileCount = $this->reservation_order_model->where($eliminateIllegalMobile)->count('DISTINCT mobile, order_time');
                $distinctMobileCountArray = is_array($distinctMobileCount) ? array_keys($distinctMobileCount) : array(0);
                $distinctMobileCount = $distinctMobileCountArray[0];

                // 去除非法车牌号
                $eliminateIllegalLicense = $map;
                $eliminateIllegalLicense['_string'] = 'CHAR_LENGTH(licenseplate) = 7';

                // 唯一车牌号个数
                $distinctLicenseplateCount = $this->reservation_order_model->where($eliminateIllegalLicense)->count('DISTINCT licenseplate, order_time');
                $distinctLicenseplateCountArray = is_array($distinctLicenseplateCount) ? array_keys($distinctLicenseplateCount) : array(0);
                $distinctLicenseplateCount = $distinctLicenseplateCountArray[0];

                // 唯一地址个数
                $distinctAddressCount = $this->reservation_order_model->where($map)->count('DISTINCT address, order_time');
                $distinctAddressCountArray = is_array($distinctAddressCount) ? array_keys($distinctAddressCount) : array(0);
                $distinctAddressCount = $distinctAddressCountArray[0];

                // 去除非法车牌号和非法手机
                $eliminateIllegal = $map;
                $eliminateIllegal['_string'] = 'CHAR_LENGTH(mobile) = 11 AND CHAR_LENGTH(licenseplate) = 7';

                // 绝对唯一的个数
                $absoluteCount = $this->reservation_order_model->where($eliminateIllegal)->count('DISTINCT mobile, licenseplate, address, order_time');
                $absoluteCountArray = is_array($absoluteCount) ? array_keys($absoluteCount) : array(0);
                $absoluteCount = $absoluteCountArray[0];


                //组装前台数据
				$priceInfo[] = array(
                    'name'=>'全部',
                    'orderCount' => $countPriceCount,
                    'distinctMobile' => $distinctMobileCount,
                    'distinctLicenseplate' => $distinctLicenseplateCount,
                    'distinctAddress' => $distinctAddressCount,
                    'absoluteCount' => $absoluteCount,
                    'value'=> $countPrice
				);
			}
			//计算其他订单价钱，给财务看的
			$where2 = $map;
			for ($i=1;$i<=73;$i++){
				$name = $this->_carserviceConf($i);
				if ($name) {
					$where2['order_type'] = array('eq',$i);
					$baoyangPrice = $this->reservation_order_model->where($where2)->sum('amount');//上门保养订单价钱
					if ($baoyangPrice) {
                        // 订单总价
						$baoyangPriceCount = $this->reservation_order_model->where($where2)->count();


                        // 去除非法手机号
                        $eliminateIllegalMobile = $where2;
                        $eliminateIllegalMobile['_string'] = 'CHAR_LENGTH(mobile) = 11';

                        // 唯一手机号个数
                        $distinctMobileCount = $this->reservation_order_model->where($eliminateIllegalMobile)->count("DISTINCT mobile, order_time");
                        $distinctMobileCountArray = is_array($distinctMobileCount) ? array_keys($distinctMobileCount) : array(0);
                        $distinctMobileCount = $distinctMobileCountArray[0];

                        // 去除非法车牌号
                        $eliminateIllegalLicense = $where2;
                        $eliminateIllegalLicense['_string'] = 'CHAR_LENGTH(licenseplate) = 7';

                        // 唯一车牌号个数
                        $distinctLicenseplateCount = $this->reservation_order_model->where($eliminateIllegalLicense)->count("DISTINCT licenseplate, order_time");
                        $distinctLicenseplateCountArray = is_array($distinctLicenseplateCount) ? array_keys($distinctLicenseplateCount) : array(0);
                        $distinctLicenseplateCount = $distinctLicenseplateCountArray[0];

                        // 唯一地址数
                        $distinctAddressCount = $this->reservation_order_model->where($where2)->count("DISTINCT address, order_time");
                        $distinctAddressCountArray = is_array($distinctAddressCount) ? array_keys($distinctAddressCount) : array(0);
                        $distinctAddressCount = $distinctAddressCountArray[0];

                        // 去除非法车牌号和非法手机
                        $eliminateIllegal = $where2;
                        $eliminateIllegal['_string'] = 'CHAR_LENGTH(mobile) = 11 AND CHAR_LENGTH(licenseplate) = 7';

                        // 绝对唯一
                        $absoluteCount = $this->reservation_order_model->where($eliminateIllegal)->count("DISTINCT mobile, address, licenseplate, order_time");
                        $absoluteCountArray = is_array($absoluteCount) ? array_keys($absoluteCount) : array(0);
                        $absoluteCount = $absoluteCountArray[0];

                        // 组装前台数据
                        $priceInfo[] = array(
                            'name' => $name,
                            'orderCount' => $baoyangPriceCount,
                            'distinctMobile' => $distinctMobileCount,
                            'distinctLicenseplate' => $distinctLicenseplateCount,
                            'distinctAddress' => $distinctAddressCount,
                            'absoluteCount' => $absoluteCount,
                            'value'=> $baoyangPrice
                        );
					}
				}
				
			}
		}
        //导出csv权限数组
        $csvArray = array(1,219,238,247,272,329);
        
		//业务来源数据
		$business_source_old = array('1'=>'IB',
									'2'=>'OB',
									'3'=>'三星',
									'4'=>'百度',
									'5'=>'淘宝',
									'6'=>'优惠券',
									'7'=>'微信',
									'8'=>'微信活动',
									'9'=>'小区',
									'10'=>'事故车',
									'11'=>'介绍',
									'12'=>'平安好车',
									'13'=>'微博',
									'14'=>'搜索',
									'15'=>'朋友圈',
									'16'=>'加油站',
									'17'=>'宝驾',
									'18'=>'e袋洗',
									'19'=>'老客户',
									'20'=>'养车点点',
									'21'=>'驴妈妈',
									'22'=>'其他',
									'23'=>'4S店预约转化',
									'24'=>'上海平安',
									'25'=>'点评团购',
									'26'=>'光大',
									'27'=>'京东',
									'28'=>'人保',
									'29'=>'光大信用卡',
									'30'=>'淘宝现场活动',
									'31'=>'光大黄金信用卡',
									'32'=>'凹凸租车',
			 						'33'=>'建设银行',
									'34'=>'pad无配件订单',
									'35'=>'百度生活',
									'36'=>'通用',
									'37'=>'UBER车主',
									'38'=>'UBER活动',
									'39'=>'i保养',
									'40'=>'点评到家',
									'41'=>'苏州平安',
									'42'=>'上海人保',
									'43'=>'安盛天平',
									'44'=>'e代泊',
									'45'=>'车挣',
									'46'=>'杭州车猫',
									'47'=>'搜房网',
									'48'=>'安师傅',
									'49'=>'杭州人保',
									'50'=>'大众公社',
									'51'=>'上海美亚',
									'52'=>'微信文章',
                                    '53'=>'天猫',
                                    '54'=>'同程旅游'
        );
        $business_source_list = $this->business_source->where(array('level'=>3))->order('id asc')->select();
        $this->assign('csvArray',$csvArray);
		$this->assign('business_source_list',$business_source_list);
        $this->assign('business_source_old',$business_source_old);
        $this->assign('time_type',$_REQUEST['time_type']);
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
        $this->assign('customer_list', $customer_list);
		$this->assign('authId',$_SESSION['authId']);
        $this->assign('data', $map);
		$this->assign('status', $status);
		$this->assign('pay_type',$_REQUEST['pay_type']);
		$this->assign('city_id', $_POST['city_id']);
		$this->assign('sort', $sort);
        $this->assign('order', $order);
		$this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
        $this->assign('list', $list);
		$this->assign('sum_amount', $sum_amount);
		$this->assign('priceInfo', $priceInfo);
		if($searchSource){
			$this->assign('source_count', $source_count);
		}else{
			$this->assign('page', $page);
		}
        $this->assign('technician_list', $technician_list);
		$this->display();
	}
	
	
	//导出订单查询数据  wql@20150709
	public  function order_export(){
        //防止内存超过限制。
        set_time_limit(0);
        
		if(!empty($_REQUEST['end_time'])&&!empty($_REQUEST['start_time'])){
            $start = strtotime($_REQUEST['start_time']);
            $end = strtotime($_REQUEST['end_time'].'23:59:59'); 
            $map[$_REQUEST['time_type']] = array(array('lt', $end),array('gt',$start),"AND");
		}
		if($_REQUEST['city_id']=='all'){
           $cityInfo['name'] = '全部城市' ; 
        }else{
           $map['city_id'] = $_REQUEST['city_id'] ;
           $cityInfo = $this->city->where('id='.$_REQUEST['city_id'])->find(); 
        }
        
        
		if($_REQUEST['status']=='all'){
		   $map['status'] = array('neq',8) ; 
		}else{
           $map['status'] = $_REQUEST['status'] ; 
        }
        

        
        //查询字段数组
		$fieldArr = array('id','technician_id','item,model_id','order_time,truename','mobile',
          'licenseplate','address','area_id','amount');
		$rs = $this->reservation_order_model->field($fieldArr)->where($map)->select();
        //echo  $this->reservation_order_model->getLastSql();exit;
		$str = "订单id,技师id,车型,机油,机滤,空滤,空调滤,预约时间,车主姓名,车牌,地址,区域,金额\n";  

		foreach($rs as $k=>$v){
            //判断实例化新配件表品牌车型表还是老配件表品牌车型表
                $this->getNeedModel($v['create_time']);
			//拼车型
			$model_id = $v['model_id'];
			$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
			$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
			$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
			$car_name = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];
			
			//配件数据
			$order_items = unserialize($v['item']);
			$oil_data = $order_items['oil_detail'];
			$oil = '';
			foreach($oil_data as $_id=>$num){
				if($num>0){
					$info = $this->item_oil_model->where(array('id'=>$_id))->find();
					$oil = $oil.$info['name'].' '.$info['norms']."L : ".$num."件;";
				}
			} 
			
            //先定义一个空数组
            $fitArr = array();
			foreach($order_items as $key=>$value){
				if($key=='filter_id'||$key=='kongqi_id'||$key=='kongtiao_id')
				{
					$keyArr = explode('_',$key);
					$name = $keyArr[0] ;
					$info = $this->item_model->where(array('id'=>$value))->find();
					$fitArr[$name] = $info['name'];
				}
			}
            
			//查询技师名称
			if($v['technician_id']){
				$condition['id'] = $v['technician_id'];
				$technician_info = $this->technician_model->where($condition)->find();
				$technician_name = $technician_info['truename'].'('.$technician_info['id'].')';
            }else{
				$technician_name = $v['technician_id'] ;
			}
            //预约时间
            $order_time = date('Y-m-d H:i:s',$v['order_time']);
            //地区
            if($v['area_id']){
                $cond['areaID'] = $v['area_id'] ;
                $areaArr = $this->item_area->where($cond)->find();
                $area = $areaArr['area'] ;
            }else{
                $area = '暂无地区信息' ;
            }  
			//字段值
			$id = $v['id'] ;   
			$filter = $fitArr['filter']; //中文转码  
			$kongqi = $fitArr['kongqi']; //中文转码 
			$kongtiao = $fitArr['kongtiao']; //中文转码  
            
            //字符串需要的值,组织csv字符串
			$strArray = array($id,$technician_name,$car_name,$oil,$filter,$kongqi,$kongtiao,$order_time,$v['truename'],
            $v['licenseplate'],$v['address'],$area,$v['amount']);
            $str .= implode(',', $strArray)."\n";
		}
		
		//echo  $str ;
		//exit ;
		$filename = $_REQUEST['start_time'].'-'.$cityInfo['name'].'订单数据'.'.csv'; //设置文件名   
		$this->export_csv($filename,$str); //导出   
		
	}
	
	
	//导出配件合计数据 wql@20150709
	public  function fit_export(){
        //防止内存超过限制。
        set_time_limit(0);
        
		if(!empty($_REQUEST['end_time'])&&!empty($_REQUEST['start_time'])){
            $start = strtotime($_REQUEST['start_time']);
            $end = strtotime($_REQUEST['end_time'].'23:59:59'); 
            $map[$_REQUEST['time_type']] = array(array('lt', $end),array('gt',$start),"AND");
		}
        
		if($_REQUEST['city_id']=='all'){
           $cityInfo['name'] = '全部城市' ; 
        }else{
           $map['city_id'] = $_REQUEST['city_id'] ;
           $cityInfo = $this->city->where('id='.$_REQUEST['city_id'])->find(); 
        }
        
		if($_REQUEST['status']=='all'){
		   $map['status'] = array('neq',8) ; 
		}else{
           $map['status'] = $_REQUEST['status'] ; 
        }
		
		$rs = $this->reservation_order_model->field('id,technician_id,item,model_id')->where($map)->select();
        //echo  $this->reservation_order_model->getLastSql();

		//获取配件字符串
		$str = $this->fitToStr($rs);
		//处理字符串为一维数组
		$strArr = explode(',',$str);
		//统计数组中所有的值出现的次数
		$countNum = array_count_values($strArr);
        
        $newCountArr = array();   //重新定义一个数组
        foreach($countNum as $k=>$v){
            if(strstr($k,':')){ //机油处理
               $kArr = explode(':',$k);
               if(array_key_exists($kArr[0],$newCountArr)){
                  $newCountArr[$kArr[0]] += $kArr[1]*$v ; //计算机油总件数  
               }else{
                  $newCountArr[$kArr[0]] = $kArr[1]*$v ; //计算机油总件数 
               }   //echo  '<br>';
            }else{  //其他配件
               $newCountArr[$k] = $v ;
            }
        }
        
		//将数组转换成csv格式字符串
		$csvStr = "配件名称,配件件数\n";
		foreach($newCountArr as $k=>$v){
			$csvStr .= $k.",".$v."\n" ;
		}
		//echo  $csvStr ;exit ; 
		$filename = $_REQUEST['start_time'].'-'.$cityInfo['name'].'配件合计'.'.csv'; //设置文件名   
		$this->export_csv($filename,$csvStr); //导出   
		
	}
	
	//配件处理为字符串方法 wql@20150709
	public  function  fitToStr($rs){
		$str = "";  
		foreach($rs as $k=>$v){
            //判断实例化新配件表品牌车型表还是老配件表品牌车型表
                $this->getNeedModel($v['create_time']);
			//配件数据
			$order_items = unserialize($v['item']);
			$oil_data = $order_items['oil_detail'];
			$oil = '';
            if($oil_data){
                foreach($oil_data as $_id=>$num){
                    if($num>0){
                        $info = $this->item_oil_model->where(array('id'=>$_id))->find();
                        $oil = $oil.$info['name'].' '.$info['norms']."L : ".$num.";";
                    }
                }
            }
			
			//以分号分割机油
            unset($oil_1,$oil_2);
			if($oil){
                $oilArr = explode(';',$oil);
                if($oilArr[0]){
                    $oil_1 = $oilArr[0];
                }
                if($oilArr[1]){
                   $oil_2 = $oilArr[1]; 
                }
			}
			
            //先定义一个空数组
            $fitArr = array() ;
			foreach($order_items as $key=>$value){
				if($key=='filter_id'||$key=='kongqi_id'||$key=='kongtiao_id')
				{
					$keyArr = explode('_',$key);
					$name = $keyArr[0] ;
					$info = $this->item_model->where(array('id'=>$value))->find();
					$fitArr[$name] = $info['name'];
				}
			}
			
			//字段值 
			
			$filter = $fitArr['filter']; //中文转码  
			$kongqi = $fitArr['kongqi']; //中文转码 
			$kongtiao = $fitArr['kongtiao']; //中文转码 
			if($oil_1){ $str .= $oil_1."," ;}
			if($oil_2){ $str .= $oil_2."," ;} 
			if($filter){ $str .= $filter."," ;}
			if($kongqi){ $str .= $kongqi."," ;}
			if($kongtiao){ $str .= $kongtiao."," ;}
            
		}
        
		
		return $str = substr($str,0,-1);
	}
	
	
	
	//导出数据为csv  wql@20150709
    function export_csv($filename,$data)   
    {   
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8'); 
        header("Content-Disposition:attachment;filename=".$filename);   
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        header('Expires:0');   
        header('Pragma:public'); 
        echo "\xEF\xBB\xBF"; // UTF-8 BOM   
        echo $data;
    }  

	
	
	private function _carserviceConf($type){
		switch ($type){
			case 1:
				$name = '保养订单';
				break;
			case 2:
				$name = '免费检测订单';
				break;
			case 3:
				$name = '淘宝99元保养订单';
				break;
			case 4:
				$name = '1元检测订单';
				break;
			case 5:
				$name = '更换配件订单（火花塞、雨刷、刹车片）';
				break;
			case 6:
				$name = '免99元服务费订单';
				break;
			case 7:
				$name = '黄喜力套餐';
				break;
			case 8:
				$name = '蓝喜力套餐';
				break;
			case 9:
				$name = '灰喜力套餐';
				break;
			case 10:
				$name = '金美孚套餐';
				break;
			case 11:
				$name = '爱代驾高端保养';
				break;
			case 12:
				$name = '爱代驾中端保养';
				break;
			case 13:
				$name = '好车况';
				break;
			case 14:
				$name = '好空气';
				break;
			case 15:
				$name = '好动力';
				break;
			case 16:
				$name = '保养服务+检测+养护';
				break;
			case 17:
				$name = '矿物质油保养套餐+检测+养护';
				break;
			case 18:
				$name = '半合成油保养套餐+检测+养护';
				break;
			case 19:
				$name = '全合成油保养套餐+检测+养护';
				break;
			case 20:
				$name = '平安保险微信';
				break;
            case 21:
                $name = '38项检测+7项细节养护(光大)';
                break;
			case 22:
				$name = '光大168套餐';
				break;
			case 23:
				$name = '光大268套餐';
				break;
			case 24:
				$name = '光大368套餐';
				break;
			case 25:
				$name = '浦发199套餐';
				break;
			case 26:
				$name = '浦发299套餐';
				break;
			case 27:
				$name = '浦发399套餐';
				break;
			case 28:
				$name = '全车检测38项(淘38)';
				break;
			case 29:
				$name = '细节养护7项(淘38)';
				break;
			case 30:
				$name = '更换空调滤工时(淘38)';
				break;
			case 31:
				$name = '更换雨刮工时(淘38)';
				break;
			case 32:
				$name = '小保养工时(淘38)';
				break;
			case 33:
				$name = '好空气套餐(奥迪宝马奔驰)';
				break;
			case 34:
				$name = '补配件免人工订单';
				break;
			case 35:
				$name = '好车况套餐:38项全车检测+7项细节养护';
				break;
			case 36:
				$name = '大众点评199套餐';
				break;
			case 37:
				$name = '大众点评299套餐';
				break;
			case 38:
				$name = '大众点评399套餐';
				break;
			case 39:
				$name = '安盛天平50元检测套餐(不含机油配件)';
				break;
			case 40:
				$name = '安盛天平168套餐';
				break;
			case 41:
				$name = '安盛天平268套餐';
				break;
			case 42:
				$name = '安盛天平368套餐';
				break;
			case 43:
				$name = '平安银行50元检测套餐(不含机油配件)';
				break;
			case 44:
				$name = '平安银行168/199套餐';
				break;
			case 45:
				$name = '平安银行268/299套餐';
				break;
			case 46:
				$name = '平安银行368/399套餐';
				break;
			case 47:
				$name = '防雾霾空调滤活动';
				break;
			case 48:
				$name = '防雾霾1元';
				break;
			case 49:
				$name = '防雾霾8元';
				break;
			case 50:
				$name = '好车况套餐（大众点评)';
				break;
			case 51:
				$name = '保养服务+检测+养护（大众点评）';
				break;
			case 52:
				$name = '好空气套餐（平安财险）';
				break;
			case 53:
				$name = '好动力套餐（后付费)';
				break;
			case 54:
				$name = '好空气套餐(奥迪宝马奔驰后付费)';
				break;
			case 55:
				$name = '发动机舱精洗套餐（淘）';
				break;
			case 56:
				$name = '黄壳199套餐（预付费)';
				break;
			case 57:
				$name = '蓝壳299套餐（预付费）';
				break;
			case 58:
				$name = '灰壳399套餐（预付费）';
				break;
			case 59:
				$name = '发动机机舱精洗套餐（预付费）';
				break;
			case 60:
				$name = '268大保养（预付费）';
				break;
			case 61:
				$name = '378大保养（预付费）';
				break;
			case 62:
				$name = '空调清洗套餐（后付费）';
				break;
			case 63:
				$name = '空调清洗套餐（免费）';
				break;
			case 64:
				$name = '好动力套餐（免费）';
				break;
			case 65:
				$name = '轮毂清洗套餐（预付费）';
				break;
			case 66:
				$name = '空调清洗（点评到家）';
				break;
			case 67:
				$name = '汽车检测和细节养护套餐（点评到家）';
				break;
			case 68:
				$name = '好空气套餐(点评到家)';
				break;
			case 69:
				$name = '保养人工费工时套餐（点评到家）';
				break;
			case 70:
				$name = '9.8细节养护与检测（微信）';
				break;
            case 71:
                $name = '空调清洗套餐（预付费）';
                break;
            case 72:
                $name = '发动机舱除碳（预付费）';
                break;
            case 73:
                $name = '发动机舱除碳（后付费）';
                break;
		}
		return $name;
	}

    /**
     * 上门保养预约订单详情
     * @date 2014/8/13
     */
    public function detail() {
	
        $id = intval($this->get_true_orderid($_GET['id']));
        $order_param['id'] = $id;

        $order_info = $this->reservation_order_model->where($order_param)->find();
        //echo $this->reservation_order_model->getLastsql();
        $order_info['price'] = $order_info['amount'];//页面原价赋值用

		if($order_info['pay_type']==1 or $order_info['pay_type']==0){
			$order_info['amount'] = $order_info['amount'].'(现)';
		}elseif($order_info['pay_type']==2){
			$order_info['amount'] = $order_info['amount'].'(微)';
		}elseif($order_info['pay_type']==3){
			$order_info['amount'] = $order_info['amount'].'(P)';
		}elseif($order_info['pay_type']==4){
			$order_info['amount'] = $order_info['amount'].'(淘)';
		}elseif($order_info['pay_type']==5){
            $order_info['amount'] = $order_info['amount'].'(大)';
        }elseif($order_info['pay_type']==6){
            $order_info['amount'] = $order_info['amount'].'(建)';
        }elseif($order_info['pay_type']==7){
            $order_info['amount'] = $order_info['amount'].'(京)';
        }elseif($order_info['pay_type']==8){
            $order_info['amount'] = $order_info['amount'].'(养)';
        }elseif($order_info['pay_type']==9){
            $order_info['amount'] = $order_info['amount'].'(支)';
        }elseif($order_info['pay_type']==10){
            $order_info['amount'] = $order_info['amount'].'(家)';
        }elseif($order_info['pay_type']==11){
            $order_info['amount'] = $order_info['amount'].'(WAP)';
        }elseif($order_info['pay_type']==12){
            $order_info['amount'] = $order_info['amount'].'(猫)';
        }elseif($order_info['pay_type']==13){
            $order_info['amount'] = $order_info['amount'].'(同)';
        }

		if($order_info['origin']==1){
			$order_info['origin'] = '网站';
		}elseif($order_info['origin']==2){
			$order_info['origin'] = '微信';
		}elseif($order_info['origin']==3){
			$order_info['origin'] = '后台';
		}

		$model_id = $order_info['model_id'];
		$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
		$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
		$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
		$order_info['car_name'] = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];
        
        //判断实例化新配件表品牌车型表还是老配件表品牌车型表
        $this->getNeedModel($order_info['create_time']);
        

        $model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
        $series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
        $brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();   
        
        //echo  $this->carmodel_model->getLastSql() ;
		
		
        
        $order_info['car_name'] = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];
        

		$order_info['order_date'] = date('Y-m-d',$order_info['order_time']);
		$order_info['order_hours'] = date('H',$order_info['order_time']);
		if($order_info['order_hours']<10){
			$order_info['order_hours'] = substr($order_info['order_hours'],1,1);
		}
		$order_info['order_minutes'] = date('i',$order_info['order_time']);

		$step_info = $this->check_step_model->where( array('order_id'=>$order_info['id']))->group('step_id')->select();
		foreach($step_info as $a=>$b){
			$step_info[$a]['create_time'] = date('Y-m-d H:i:s',$b['create_time']);
			$step_info[$a]['step_name'] = $this->get_stepname($b['step_id']);
		}

		$order_info['show_id'] = $this->get_orderid($order_info['id']).'('.$order_info['id'].')';
		$order_info['id'] = $this->get_orderid($order_info['id']);

		//检测报告数据
		$report_info = $this->checkreport_model->where(array('reservation_id'=>$id))->find();

        if(empty($order_info['vin_num'])) {
            $report_info['data'] = unserialize($report_info['data']);
            $order_info['vin_num'] = $report_info['data']['vin'];
        }
		if($report_info){
			$order_info['report_id'] = base64_encode($report_info['id'].'168');
		}

		//点播链接
		$live_id = $_GET['id'].'s'.md5($_GET['id'].'fqcd123223');
		$url = WEB_ROOT.'mobilecar-live-order_id-'.$live_id;
		$this->assign('url',$url);

		//发票数据
		$invoice_info = $this->invoice_model->where(array('order_id'=>$id))->find();
		$this->assign('invoice_info',$invoice_info);

		//回访数据
		$recall_info = $this->recall_model->where(array('order_id'=>$id))->select();
		$this->assign('recall_info',$recall_info);

		//技师数据
		if($order_info['technician_id']){
			$condition['id'] = $order_info['technician_id'];
			$technician_info = $this->technician_model->where($condition)->find();
			$order_info['technician_name'] = $technician_info['truename'];
		}

        //商品详情
        $order_items = unserialize($order_info['item']);
		$default = array();//初始化时默认已选配件数组
		$default['oil_id'] = $order_items['oil_id'];
		$default['filter_id'] = $order_items['filter_id'];
		$default['kongqi_id'] = $order_items['kongqi_id'];
		$default['kongtiao_id'] = $order_items['kongtiao_id'];
        if($order_items['oil_id']>0){
        	$item_oil_price = 0;
        	$oil_data = $order_items['oil_detail'];
			$key = array_keys($oil_data);
			$default['oil_1_id'] = $key['0'];
			$default['oil_2_id'] = $key['1'];
        	foreach ( $oil_data as $_id=>$num){
        		if($num > 0){
					foreach($order_items['price']['oil'] as $kk=>$vv){
						if($kk==$_id){
							$item_oil_price += $vv;
						}
					}
                    
                    $res = $this->item_oil_model->field('name,price,type,norms')->where( array('id'=>$_id))->find();
                    
        			//$item_oil_price += $res['price']*$num;
					$norms += $res['norms']*$num;
        			$name = $res['name'];
					$item_list['0']['type'] = '('.$this->getOiltype($res['type']).')';
        		}
				if($_id==$default['oil_1_id']){ $default['oil_1_num']=$num; }
				if($_id==$default['oil_2_id']){ $default['oil_2_num']=$num; }
        	}
        	$oil_param['id'] =  $order_items['oil_id'];
        	$item_list['0']['id'] = $order_items['oil_id'];
			if($norms>4){ $norms = '<font color=red>'.$norms.'</font>';}
        	$item_list['0']['name'] = $name.' '.$norms.'L';
        	$item_list['0']['price'] = $item_oil_price;
			$item_list['0']['norms'] = $norms;
        }elseif($order_items['oil_id']<0){
        	$item_list['0']['id'] = 0;
        	$item_list['0']['name'] = "自备配件";
        	$item_list['0']['price'] = 0;
        }elseif($order_items['oil_id']==0){
			$item_list['0']['id'] = 0;
			$item_list['0']['name'] = "不需要配件";
			$item_list['0']['price'] = 0;
		}
        if($order_items['filter_id']>0){
        	$item_condition['id'] = $order_items['filter_id'];
            
            $item_list['1'] = $this->item_model->where($item_condition)->find();
        	
			foreach($order_items['price']['filter'] as $kk=>$vv){
				if($kk>0){
					$item_list['1']['price'] = $vv;
				}
			}
        }elseif($order_items['filter_id']<0){
        	$item_list['1']['id'] = 0;
        	$item_list['1']['name'] = "自备配件";
        	$item_list['1']['price'] = 0;
        }elseif($order_items['filter_id']==0){
        	$item_list['1']['id'] = 0;
        	$item_list['1']['name'] = "不需要配件";
        	$item_list['1']['price'] = 0;
        }
        if($order_items['kongqi_id']>0){
        	$item_condition['id'] = $order_items['kongqi_id'];
            
            $item_list['2'] = $this->item_model->where($item_condition)->find();
        	
			foreach($order_items['price']['kongqi'] as $kk=>$vv){
				if($kk>0){
					$item_list['2']['price'] = $vv;
				}
			}
        }elseif($order_items['kongqi_id']<0){
        	$item_list['2']['id'] = 0;
        	$item_list['2']['name'] = "自备配件";
        	$item_list['2']['price'] = 0;
        }elseif($order_items['kongqi_id']==0){
        	$item_list['2']['id'] = 0;
        	$item_list['2']['name'] = "不需要配件";
        	$item_list['2']['price'] = 0;
        }
        if($order_items['kongtiao_id']>0){
        	$item_condition['id'] = $order_items['kongtiao_id'];
            
            $item_list['3'] = $this->item_model->where($item_condition)->find();
			foreach($order_items['price']['kongtiao'] as $kk=>$vv){
				if($kk>0){
					$item_list['3']['price'] = $vv;
				}
			}
        }elseif($order_items['kongtiao_id']<0){
        	$item_list['3']['id'] = 0;
        	$item_list['3']['name'] = "自备配件";
        	$item_list['3']['price'] = 0;
        }elseif($order_items['kongtiao_id']==0){
        	$item_list['3']['id'] = 0;
        	$item_list['3']['name'] = "不需要配件";
        	$item_list['3']['price'] = 0;
        }
		/*
		//价格为零的配件不显示
		foreach($item_list as $k=>$v){
			if($v['price']==0){
				unset($item_list[$k]);
			}
		}*/
		$default['oil_name'] = $item_list['0']['name'];
		$default['filter_name'] = $item_list['1']['name'];
		$default['kongqi_name'] = $item_list['2']['name'];
		$default['kongtiao_name'] = $item_list['3']['name'];
		$default['oil_price'] = $item_list['0']['price'];
		$default['filter_price'] = $item_list['1']['price'];
		$default['kongqi_price'] = $item_list['2']['price'];
		$default['kongtiao_price'] = $item_list['3']['price'];

		//获取所选车型配件数据start
		//车型
		$style_param['model_id'] = $model_id;

        
        $car_style = $this->carmodel_model->where($style_param)->find();
		
		$style_name = $car_style['model_name'];
		$car_name = $car_style['model_name'];
	
		if($car_style){
			$model_param['series_id'] = $car_style['series_id'];
			//$car_model = $this->car_model_model->where($model_param)->find();
            $car_model = $this->carseries_model->where($model_param)->find();
		
			if($car_model){
				$model_name = $car_model['series_name'];
				$car_name = $car_model['series_name']." - ".$car_name;
	
				$brand_param['brand_id'] = $car_model['brand_id'];
				//$car_brand = $this->car_brand_model->where($brand_param)->find();
                $car_brand = $this->carbrand_model->where($brand_param)->find();
				
				if($car_brand){
					$brand_name = $car_brand['brand_name'];
					$car_name = $car_brand['brand_name']." - ".$car_name;
				}
			}
		}
	
		//机油
		//$oil_num = ceil($car_style['oil_num']);
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
		$default['norms'] = $car_style['norms'];
		//所有物品详情
        $oil_list_all = $this->item_oil_model->where()->select();
		
		$oil_item = array();
		foreach( $oil_list_all as $nors){
			$nors['name'] = rtrim($nors['name'],'装');
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
		$item_sets=array();
        
        $oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select(); 
		
	
		foreach( $oil_name_distinct as $keys=>$names ){
			
			$names['name'] = rtrim($names['name'],'装');
			$item_sets[$keys]['id'] = $names['id'];
			$item_sets[$keys]['name'] = $names['name'];
			//规格1L详情
			$item_sets[$keys]['oil_1'] = $oil_item[$names['name']][1]['id'];
			$item_sets[$keys]['oil_1_num'] = $xx;
			$item_sets[$keys]['oil_1_price'] = $oil_item[$names['name']][1]['price'];
			//规格4L详情
			$item_sets[$keys]['oil_2'] = $oil_item[$names['name']][4]['id'];
			$item_sets[$keys]['oil_2_num'] = $yy;
			$item_sets[$keys]['oil_2_price'] = $oil_item[$names['name']][4]['price'];
	
			//计算总价
			$item_sets[$keys]['price'] = $xx*$oil_item[$names['name']][1]['price']+$yy*$oil_item[$names['name']][4]['price'];
	
		}
		$car_style['type'] = '未设定';
		if($car_style['oil_type']=='a'){
			$car_style['type'] = '矿物油';
		}
		if($car_style['oil_type']=='b'){
			$car_style['type'] = '半合成油';
		}
		if($car_style['oil_type']=='c'){
			$car_style['type'] = '全合成油';
		}
	
		//项目
		$style_id = $model_id;
		$item_set = array();
		if( $style_id ){
			//echo $style_id;
			$condition2['model_id'] = $style_id;
			//$style_info = $this->car_style_model->where($condition)->find();
			$style_info = $this->carmodel_model->where($condition2)->find();
			//var_dump($this->carmodel_model->getLastSql());
			$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
// 	var_dump($style_info);
            
            
			if( $set_id_arr ){
				foreach( $set_id_arr as $k=>$v){
					if(is_array($v)){
						foreach( $v as $_k=>$_v){
							$item_condition['id'] = $_v;
							$item_condition['name'] = array('notlike','%pm2%');
							$item_info_res = $this->filter_model->where($item_condition)->find();
                           // $item_info_res = $this->filter_model->where($item_condition)->find();
                            
							if($item_info_res) {
								$item_info['id'] = $item_info_res['id'];
								$item_info['name'] = $item_info_res['name'];
								$item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
								$item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
								$item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
								$item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
								$item_set[$k][$_k] = $item_info;
							}
							//排除数组中缺乏元素页面选项空白的问题
							if(!$item_set[$k][0] and $item_set[$k][1]){
								$item_set[$k][0] = $item_set[$k][1];
								unset($item_set[$k][1]);
							}
						}
						foreach($item_set[$k] as $kk=>$vv){
//							if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
//								$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
//								$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
//							}else{
                            $item_set_new[$k][$kk] = $item_set[$k][$kk];

						}
						$item_set = $item_set_new;
					}
				}
			}
		}
		//print_r($default);
        
        // print_r($item_set);
         
		$this->assign('default', $default);
		$this->assign('car_style', $car_style);
		$this->assign('item_sets', $item_sets);
		$this->assign('item_set', $item_set);
		//print_r($item_sets);
		//获取配置项目END
		
        //用户坐标
        if($order_info['latitude'] && $order_info['longitude']){
            $order_info['coordinate'] = $order_info['latitude'].",".$order_info['longitude'];
        }else{
            $res = $this->geocoder($order_info['address']);
            $res = json_decode($res, true);
            if($res['status'] == 0){
                $order_info['coordinate'] = $res['result']['location']['lat'].",".$res['result']['location']['lng'];
            }
        }

		//获取车型
		$brand_list = $this->car_brand_model->select();

		//计算优惠券价格
		if($order_info['replace_code']){
            if ($this->newCouponCodeModel->isNewCouponCode($order_info['replace_code'])) {// 新版优惠券计算详情显示抵扣金额
                $dikou = $this->newCouponCodeModel->getDiscountAmount($order_info['replace_code']);
            } else {
                $dikou = $this->get_codevalue($order_info['replace_code']);
            }
		}
		if($dikou>0){
			$dikou = '-'.$dikou;
		}else{
			$dikou = 0;
		}

		if($order_info['order_type']==2){
			$service_cost = 0;
		}else{
			$service_cost = 99;
		}
         
        if($order_info['area_id']==0){
           $order_info['area_id']== false ;
        }
                
		$this->assign('service_cost',$service_cost);
		$this->assign('dikou',$dikou);
        $this->assign('brand_list',$brand_list);
        $this->assign("item_list", $item_list);

		$this->assign('authId',$_SESSION['authId']);
        $this->assign('id', $id);
        $this->assign('order_info',$order_info);
        $this->assign('step_info',$step_info);
        $this->display();
    }
    
    public function show() {
    	$id = intval($_GET['id']);
    	$order_param['id'] = $id;
    
    	$order_info = $this->reservation_order_model->where($order_param)->find();
    	//echo $this->reservation_order_model->getLastsql();
    	$model_id = $order_info['model_id'];
    	$model_res = $this->car_model_model->field('model_name')->where( array('model_id'=>$model_id))->find();
    	$order_info['car_name'] = $model_res['model_name'];
    	//商品详情
    	$order_items = unserialize($order_info['item']);
    
    	if( !empty( $order_items['oil_detail'] ) ){
    		$item_oil_price = 0;
    		$oil_data = $order_items['oil_detail'];
    		foreach ( $oil_data as $_id=>$num){
    			if($num > 0){
    				$res = $this->item_oil_model->field('name,price')->where( array('id'=>$_id))->find();
    				$item_oil_price += $res['price']*$num;
    				$name = $res['name'];
    			}
    		}
    		$oil_param['id'] =  $order_items['oil_id'];
    		$item_list['0']['id'] = $order_items['oil_id'];
    		$item_list['0']['name'] = $name;
    		$item_list['0']['price'] = $item_oil_price;
    	}else{
    		$item_list['0']['id'] = 0;
    		$item_list['0']['name'] = "自备配件";
    		$item_list['0']['price'] = 0;
    	}
    	if($order_items['filter_id']){
    		$item_condition['id'] = $order_items['filter_id'];
    		$item_list['1'] = $this->item_model->where($item_condition)->find();
    	}else{
    		$item_list['1']['id'] = 0;
    		$item_list['1']['name'] = "自备配件";
    		$item_list['1']['price'] = 0;
    	}
    	if($order_items['kongqi_id']){
    		$item_condition['id'] = $order_items['kongqi_id'];
    		$item_list['2'] = $this->item_model->where($item_condition)->find();
    	}else{
    		$item_list['2']['id'] = 0;
    		$item_list['2']['name'] = "自备配件";
    		$item_list['2']['price'] = 0;
    	}
    	if($order_items['kongtiao_id']){
    		$item_condition['id'] = $order_items['kongtiao_id'];
    		$item_list['3'] = $this->item_model->where($item_condition)->find();
    	}else{
    		$item_list['3']['id'] = 0;
    		$item_list['3']['name'] = "自备配件";
    		$item_list['3']['price'] = 0;
    	}
    
    	//用户坐标
    	if($order_info['latitude'] && $order_info['longitude']){
    		$order_info['coordinate'] = $order_info['latitude'].",".$order_info['longitude'];
    	}
    	$this->assign("item_list", $item_list);
    
    	$this->assign('id', $id);
    	$this->assign('order_info',$order_info);
    
    	$this->display();
    }

    public function edit(){
		//print_r($_REQUEST);exit;
        $order_id = intval($_GET['id']);
        $condition['id'] = $order_id;

        $coordinate = $_POST['coordinate'];
        $coordinate = explode(",", $coordinate);
        $update['latitude'] = $coordinate['0'];  //纬度2014/9/19
        $update['longitude'] = $coordinate['1'];  //经度
        $update['operator_remark'] = $_POST['operator_remark'];
        $update['truename'] = $_POST['truename'];
        $update['mobile'] = $_POST['mobile'];
        //$update['order_time'] = strtotime(substr($_REQUEST['order_time'],0,11).' '.$_REQUEST['order_hours'].":".$_REQUEST['order_minutes']);
		$update['order_time'] = $this->_post('order_time');
		$order_time2 = intval($this->_post('order_time2'));
		//echo 'order_time2='.$this->_post('order_time2').'/'.$order_time2;exit;
		$update['order_time'] = strtotime($update['order_time']) + $order_time2*3600;
        $update['create_time'] = strtotime($_POST['create_time']);
        $update['licenseplate'] = $_POST['licenseplate'];
        $update['remark'] = $_POST['remark'];
        $update['address'] = $_POST['address'];
        $update['taobao_id'] = $_POST['taobao_id'];
		session_start();
        
        
        //判断实例化新配件表品牌车型表还是老配件表品牌车型表
        $this->getNeedModel($update['create_time']);
	
		unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
		//var_dump($_POST);exit;
		if($this->_post('CheckboxGroup0_res')>0){
			$_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res'));
			$_SESSION['oil_detail'] = array(
					$this->_post('oil_1_id') => $this->_post('oil_1_num'),
					$this->_post('oil_2_id') => $this->_post('oil_2_num')
			);
		}elseif($this->_post('CheckboxGroup0_res')==0){
			$_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res'));
			$_SESSION['oil_detail'] = array(
					'0' => '0',
					'0' => '0'
			);
		}elseif($this->_post('CheckboxGroup0_res')<0){
			$_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res'));
			$_SESSION['oil_detail'] = array();
		}
		if($this->_post('CheckboxGroup1_res')){
			$_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res'));
		}
		if($this->_post('CheckboxGroup2_res')){
			$_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res'));
		}
		if($this->_post('CheckboxGroup3_res')){
			$_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res'));
		}

		$info = $this->reservation_order_model->where($condition)->find();
    	//商品详情
    	$order_items = unserialize($info['item']);

		//计算总价
		if($_SESSION['item_0']){
			//通过机油id查出订单数据
			$item_oil_price = 0;
			//如果未重选机油则不改变总价，防止调价使老订单价格变化
			if($_SESSION['item_0']==$order_items['oil_id']){
				foreach ($order_items['price']['oil'] as $k => $v) {
					$item_oil_price += $v;
					$oil_price[]=$v;
					$oil_id[]=$k;
				}
				//print_r($oil_price);exit;
				$oil_1_price = $oil_price['0'];
				$oil_2_price = $oil_price['1'];
				$oil_1_id = $oil_id['0'];
				$oil_2_id = $oil_id['1'];
			}else{
				$oil_data = $_SESSION['oil_detail'];
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
		}
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
	
		//if($this->_post('sub')==1){
		//print_r($item_list);exit;
			$item_amount = 0;
			if(is_array($item_list)){
				foreach ($item_list as $key => $value) {
					$item_amount += $value['price'];
				}
			}

//            $first = substr($info['replace_code'],0,1);
//			if($info['replace_code']>0 or $first==f){
//				$value = $this->get_codevalue($info['replace_code']);
//				if ($value != 99){
//					$update['amount'] = $item_amount +99 -$value;
//				}else{
//					$update['amount'] = $item_amount;
//				}
//			}else{
//              $update['amount'] = $item_amount + 99;
//			}
           
            if ($info['order_type'] != '1' && $this->newCouponCodeModel->isNewCouponCode($info['replace_code'], 'all')) {//新版优惠券可用
                $value = $this->newCouponCodeModel->getDiscountAmount($info['replace_code']);
                $update['amount'] = $this->newCouponCodeModel->getAmount($info['replace_code']) - $value;
            } else {
                if($info['order_type']==2){
                    $update['amount'] = 0;
                }elseif($info['order_type']==3){
                    $update['amount'] = 0;
                }else{
                    $update['amount'] = $item_amount+99-$info['dikou_amount'];
                }
            }
			/*if($info['order_type']==13){
				$update['amount'] = '9.80';
			}*/
			//echo $update['amount'];exit;
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
        
        if($_REQUEST['area_id']){
           $update['area_id'] = $_REQUEST['area_id'] ; 
        }
        if($update['amount']<0){
            $this->error('价格错误,无法修改订单');
        }

        $this->reservation_order_model->where($condition)->save($update);
		//echo $this->reservation_order_model->getLastsql();exit;

		$log = str_replace("'",'',serialize($update));
		$log = str_replace('"','',$log);

		$array = array(
				'oid'=>$order_id,
				'operate_id'=>$_SESSION['authId'],
				'log'=>'修改了订单，订单号:'.$order_id.'data:'.$log
				);
		$this->addOperateLog($array);
        $this->success('订单修改成功！',U('/Carservice/Carserviceorder/'));
    }

	//订单初始化
	public function initialization(){
		$map['id'] = $_REQUEST['id'];
		$info = $this->reservation_order_model->where($map)->find();
		$data['status'] = 0;
		$data['technician_id'] = 0;
		$order_info = $this->reservation_order_model->where($map)->save($data);

        $array = array(
        		'oid'=>$_REQUEST['id'],
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作初始化订单，订单号:'.$_REQUEST['id']
        );
        $this->addOperateLog($array);
		$time = time();
		$shangmen_time = strtotime(date('Y-m-d 0:00:00',$info['order_time']));
		$item = unserialize($info['item']);
		//作废发生在上门当天并且已分配技师，就记录取消日志
		if($time>$shangmen_time and $info['status']==2 and ($item['oil_id']>0 or $item['filter_id']>0 or $item['kongqi_id']>0 or $item['kongtiao_id']>0)){
			//技师配件日志
			$log_data['order_id'] = $info['id'];
			$log_data['item'] = $info['item'];
			$log_data['technician_id'] = $info['technician_id'];
			$log_data['create_time'] = time();
			$this->get_itemback_log($log_data);
		}

		$this->success('初始化成功！',U('/Carservice/Carserviceorder/'));
	}

	//订单回到已确认未分配状态
	public function update_to_process1(){
		$map['id'] = intval($this->get_true_orderid($_GET['id']));
		$data['status'] = 1;
		$data['technician_id'] = 0;
		$order_info = $this->reservation_order_model->where($map)->save($data);

        $array = array(
        		'oid'=>$_REQUEST['id'],
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作订单回到已确认未分配，订单号:'.$_REQUEST['id']
        );
        $this->addOperateLog($array);

		$this->success('设置成功！',U('/Carservice/Carserviceorder/'));
	}

	//更新发票数据
	public function update_invoice(){
		$map['order_id'] = $_REQUEST['id'];
        $data['invoice_no'] = $_REQUEST['invoice_no'];
		$data['customer_name'] = $_REQUEST['customer_name'];
		$data['receiver_name'] = $_REQUEST['receiver_name'];
		$data['receiver_phone'] = $_REQUEST['receiver_phone'];
		$data['receiver_address'] = $_REQUEST['receiver_address'];
		$data['express'] = $_REQUEST['express'];
		$data['express_id'] = $_REQUEST['express_id'];
		$data['invoice_status'] = $_REQUEST['invoice_status'];
		$data['invoice_type'] = $_REQUEST['invoice_type'];
		$data['bank_name'] = $_REQUEST['bank_name'];
		$data['bank_account'] = $_REQUEST['bank_account'];
		$data['taxpayer_id'] = $_REQUEST['taxpayer_id'];
		$data['remark'] = $_REQUEST['remark'];
		$invoice_info = $this->invoice_model->where($map)->find();
		if(!$invoice_info){
			$data['order_id'] = $_REQUEST['id'];
			$data['create_time'] = time();		
			$order_info = $this->invoice_model->add($data);
		}else{		
			$order_info = $this->invoice_model->where($map)->save($data);
		}
		//通知用户开票和快递信息
		$order_info = $this->reservation_order_model->where(array('id'=>$_REQUEST['id']))->find();
		$sms = array(
				'phones'=>$order_info['mobile'],
				'content'=>$order_info['truename'].'，您的府上养车发票已通过'.$this->get_express($_REQUEST['express']).'快递寄出，快递单号'.$_REQUEST['express_id'].'，请您届时查收',
		);
		$this->curl_sms($sms,'',1);
		$sms['sendtime'] = time();
		$this->model_sms->data($sms)->add();

		$this->success('发票信息修改成功！',U('/Carservice/Carserviceorder/'));
	}

	//插入客服回访数据
	public function insert_recall(){
		$data['order_id'] = $_REQUEST['id'];
		$data['content'] = $_REQUEST['content'];
		$data['admin_id'] = $_SESSION['authId'];
		$data['create_time'] = time();
		$recall_id = $this->recall_model->add($data);
		
		/*$array = array(
				'oid'=>$_REQUEST['id'],
				'operate_id'=>$_SESSION['authId'],
				'log'=>'提交回访数据，订单号:'.$_REQUEST['id']
				);
		$this->addOperateLog($array);*/

		$this->success('回访数据提交成功！',U('/Carservice/Carserviceorder/'));
	}

	private function get_express($express){
        switch ($express) {
            case '1':
                return "顺丰";
                break;

            case '2':
                return "申通";
                break;

			case '3':
                return "韵达";
                break;

			case '4':
                return "中通";
                break;

			case '5':
                return "圆通";
                break;

			default:
                return "未知类型";
                break;
        }
    }

    public function process_1(){
        $id = intval($_GET['id']);
        //订单信息
        $order_param['id'] = $id;
        $order_info = $this->reservation_order_model->where($order_param)->find();

		//经纬度
		$res = $this->geocoder($order_info['address']);
		$res = json_decode($res, true);
		if($res['status'] == 0){
			$update['longitude'] = $res['result']['location']['lng'];
			$update['latitude'] = $res['result']['location']['lat'];
		}

        $condition['id'] = $id;

        if($order_info['status'] == 0){
            $update['status'] = 1;  //订单预约处理
            $update['update_time'] = time();
        }else{
            $this->error('订单预约处理失败',U('/Carservice/Carserviceorder/')); 
        }

        $this->reservation_order_model->where($condition)->save($update);
        
        //绑定微信号和手机
        $pa_id = $order_info['pa_id'];
        if ($pa_id) {
        	$palog = $this->PadataModel->where(array('id'=>$pa_id,'type'=>'2'))->order('id desc')->find();
        	if ($palog) {
        		//关注过我们微信
        		$Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
        		if(!$Paweixin){
        			//绑定手机号和微信号
        			$addData = array(
        					'wx_id' => $palog['FromUserName'],
        					'type'  => '2',
        					'create_time' => time(),
        					'mobile'=> $_POST['mobile'],
        			);
        			$this->PaweixinModel->add($addData);
        			$this->addCodeLog('carservice_bind', '后台绑定微信号'.$palog['FromUserName'].'和手机号'.$_POST['mobile'] ,$id);
        		}else{
        			if (!empty($_POST['mobile'])) {  //更新电话号码
        				$updateData['mobile'] = $_POST['mobile'];
        				$this->addCodeLog('carservice_bind', '后台微信号'.$palog['FromUserName'].'更新手机号'.$_POST['mobile'] ,$id);
        				$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->save($updateData);
        			}
        		}
        	}else{
        		//不是微信用户,更新相关信息
        		$updateData = array();
        		if (!empty($_POST['mobile'])) {
        			$updateData['mobile'] = $_POST['mobile'];
        		}
        		if (!empty($_POST['truename'])) {
        			$updateData['username'] = $_POST['truename'];
        		}
        		$where = array(
        				'uid'=>$order_info['uid']
        		);
        		$this->addCodeLog('carservice_bind', '后台上门保养绑定用户id'.$order_info['uid'].'和手机号'.$_POST['mobile'] ,$id);
        		$this->user_model->where($where)->save($updateData);
        	}
		$this->submitCodeLog('carservice_bind');
        }
        $array = array(
        		'oid'=>$id,
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作处理订单，订单号:'.$id
        );
        $this->addOperateLog($array);
		if($order_info['admin_id']==''){
			$order_param['id'] = $id;
			$data['admin_id'] = $_SESSION['authId'];
			$order_info = $this->reservation_order_model->where($order_param)->save($data);
		}
        $this->success('订单预约处理成功！',U('/Carservice/Carserviceorder/'));
    }

    public function del(){
        $id = intval($_GET['id']);

        $condition['id'] = $id;
        $info = $this->reservation_order_model->where($condition)->find();
        $update['status'] = 8;
		//解绑验证码
		$update['replace_code'] = '';
        $this->reservation_order_model->where($condition)->save($update);
		//恢复成未使用
		$map['coupon_code'] = trim($info['replace_code']);
		$data['status'] = 0;
		$this->carservicecode_model->where($map)->save($data);
        
        //$this->new_coupon->where($map)->save($data);
        $this->newCouponCodeModel->where($map)->save($data);

        $array = array(
        		'oid'=>$id,
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作作废订单，订单号:'.$id
        );
        $this->addOperateLog($array);
		$time = time();
		$shangmen_time = strtotime(date('Y-m-d 0:00:00',$info['order_time']));
		
		$item = unserialize($info['item']);
		//作废发生在上门当天并且已分配技师，就记录取消日志
		if($time>$shangmen_time and $info['status']==2 and ($item['oil_id']>0 or $item['filter_id']>0 or $item['kongqi_id']>0 or $item['kongtiao_id']>0)){
			//技师配件日志
			$log_data['order_id'] = $info['id'];
			$log_data['item'] = $info['item'];
			$log_data['technician_id'] = $info['technician_id'];
			$log_data['create_time'] = time();
			$this->get_itemback_log($log_data);
		}
        //查询临时id ,通知点评到家取消订单  wql@20150702
        if($info['business_source']==33){
            $con['order_id '] = $info['id'] ;
            $rs = $this->dphome_linshi->where($con)->find();
            if(is_array($rs)){
                $save_data['status'] = 2  ; //取消订单
                $this->dphome_linshi->where($con)->save($save_data);
                //执行更新点评代码
                $linshi_id = $rs['id'] ;
                $this->cancelDianping($linshi_id ,$info['id']);
            }    
        }
        
        $this->success('订单作废成功！',U('/Carservice/Carserviceorder/'));
    }
    
    
    //点评更新方法  wql@20150701
    function cancelDianping($linshi_id ,$id){
        //curl更新点评信息
        $uri = "http://m.api.dianping.com/tohome/openapi/xieche/partnerCancelOrder";
        //echo  $uri ;
        // 参数数组
        $data = array (
           'orderId' => $linshi_id ,   //传递临时表id
           'methodName'=>'partnerCancelOrder',
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
        $this->interface_log->add($add_data);       
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
    
    
    /**
     * 分配技师
     * @date 2014/8/14
     */
    public function technician_assign(){
		$this->checkaccess(__FUNCTION__,__CLASS__);//检查操作权限
        $id = intval($this->get_true_orderid($_GET['id']));

        if($_SESSION['city_id']==1){
            //上海启用技师组分配模式
            $condition['status'] = "1";
            $group_list = $this->technician_group->where($condition)->select();
            foreach($group_list as $k=>$v){
                $info = $this->technician_model->where(array('id'=>$v['major_tid']))->find();
                $group_list[$k]['truename'] = $v['group_name'].':'.$info['truename'].'(主)';
                $group_list[$k]['tid'] = $info['id'];
            }
            $this->assign("technician_list", $group_list);
        }else {
            //非上海城市沿用技师分配模式
            $condition['status'] = "1";
            if ($_SESSION['city_id'] and strpos($_SESSION['city_id'], ',') > 0) {
                $condition['city_id'] = array('in', $_SESSION['city_id']);
            } else {
                $condition['city_id'] = $_SESSION['city_id'];
            }
            $technician_count = $this->technician_model->where($condition)->count();
            $technician_list = $this->technician_model->where($condition)->select();
            echo $this->technician_model->getLastsql();
            foreach($technician_list as $k=>$v){
                $technician_list[$k]['tid'] = $v['id'];
                $technician_list[$k]['id'] = 0;
            }
            $condition['date'] = date("Y-m-d", time());
            $condition['is_del'] = 0;
            $technician_schedule_count = $this->technician_schedule_model->where($condition)->count();

            if ($technician_schedule_count % $technician_count != 0) {
            }
            $this->assign("technician_list", $technician_list);
        }
        //getDistance($latitude1, $longitude1, $latitude2, $longitude2)

        $this->assign("id", $id);
        
        $this->display();
    }

    public function technician_schedule(){
        $order_id = intval($_POST['id']);
        $group_id = intval($_POST['group_id']);
        $technician_id = intval($_POST['technician_id']);

        //订单信息
        $order_param['id'] = $order_id;
        $order_info = $this->reservation_order_model->where($order_param)->find();

		//允许已分配情况下继续分配
        if($order_info['status'] == 1 or $order_info['status'] == 2){
        }else{
            $this->error('订单分配技师失败',U('/Carservice/Carserviceorder/')); 
        }

        $num = 1;

        $data['order_id'] = $order_id;
        $data['technician_id'] = $group_id;
        $data['group_id'] = $group_id;
        $data['num'] = $num;
        $data['date'] = date("Y-m-d", time());

        $this->technician_schedule_model->add($data);
        //echo $this->technician_schedule_model->getLastsql();

        $condition['id'] = $order_id;
        $update['technician_id'] = $technician_id;
        $update['group_id'] = $group_id;
        $update['status'] = 2;  //已分配技师
        $update['update_time'] = time();
        $result = $this->reservation_order_model->where($condition)->save($update);

        if($result==1) {
            echo 1;
        }
		//分配技师完成之后通知用户
		//if($order_info['city_id']==1){ 
			$model_technician = D('Technician');
			$info = $model_technician->where(array('id'=>$technician_id))->find();
			$model_user = M('tp_admin.user','xc_');
			$user_info = $model_user->where(array('id'=>$info['user_id']))->find();
		   
			/*$sms = array(
					'phones'=>$order_info['mobile'],
					'content'=>'您的订单'.$order_id.'已安排技师'.$info['truename'].'于'.date('Y-m-d H:i:s',$order_info['order_time']).'上门，联系方式'.$user_info['mobile'].'。如有疑问请拨打客服电话400-660-2822咨询',
			);*/
			//改为7点脚本自动发了
			/*$sms = array(
					'phones'=>$order_info['mobile'],
					'content'=>'亲，您'.date('m',$order_info['order_time']).'月'.date('d',$order_info['order_time']).'日预约的携车网-府上养车，将由五星技师'.$info['truename'].'师傅（'.$user_info['mobile'].'）上门为您服务。期待与您的见面！有疑问询：400-660-2822。',
			);
			$this->curl_sms($sms,'',1,1);
			$sms['sendtime'] = time();
			$this->model_sms->data($sms)->add();*/
		//}
		//发送进销存接口数据
		if (!empty($order_info['item']) && ($order_info['order_type'] !=2) ) {
		
			$order_items = unserialize($order_info['item']);
			 
			$oil = @$order_items['oil_detail'];
			$filter = @$order_items['price']['filter'];
			$kongqi = @$order_items['price']['kongqi'];
			$kongtiao = @$order_items['price']['kongtiao'];
			 
			$cpxx = '';
			if($oil){
				$mOil = M('tp_xieche.item_oil','xc_');
				foreach ($oil as $oilId=>$oilNum){
					$oilDetail = $mOil->field('name')->where( array('id'=>$oilId) ) -> find();
					$oilName = $oilDetail['name'];
					$oilPrice = @$order_items['price']['oil'][$oilId] / $oilNum;//单价
					if($oilName){
						if (!$oilPrice) {
							$oilPrice = 0;
						}
						$cpxx.='<==>byxm='.str_replace(' ','',$oilName).',cpjg='.$oilPrice.',xssl='.$oilNum;
					}
				}
			}
			$mItem = M('tp_xieche.item_filter','xc_');
			if($filter){
				list($filterId,$filterPrice) = each($filter);
				$data = $mItem->field('name')->where( array('id'=>$filterId) )->find();
				$filterName = $data['name'];
				if($filterName){
					if (!$filterPrice) {
						$filterPrice = 0;
					}
					$cpxx.='<==>byxm='.str_replace(' ','',$filterName).',cpjg='.$filterPrice.',xssl=1';
				}
			}
			if($kongqi){
				list($kongqiId,$kongqiPrice) = each($kongqi);
				$data = $mItem->field('name')->where( array('id'=>$kongqiId) )->find();
				$kongqiName = $data['name'];
				if($kongqiName){
					if (!$kongqiPrice) {
						$kongqiPrice = 0;
					}
					$cpxx.='<==>byxm='.str_replace(' ','',$kongqiName).',cpjg='.$kongqiPrice.',xssl=1';
				}
			}
			if($kongtiao){
				list($kongtiaoId,$kongtiaoPrice) = each($kongtiao);
				$data = $mItem->field('name')->where( array('id'=>$kongtiaoId) )->find();
				$kongtiaoName = $data['name'];
				if($kongtiaoName){
					if (!$kongtiaoPrice) {
						$kongtiaoPrice = 0;
					}
					$cpxx.='<==>byxm='.str_replace(' ','',$kongtiaoName).',cpjg='.$kongtiaoPrice.',xssl=1';
				}
			}
			//$admin = !empty($order_info['admin_id'])?$order_info['admin_id']:1;
			$vin = !empty($order_info['vin'])?$order_info['vin']:1;
			
			//人工费
			$order_type = $order_info['order_type'];
			if ($order_type == 1) {
				$service_price = 99;
				if( !empty($order_info['replace_code']) ){
					$service_price = $this->get_codevalue($order_info['replace_code']);
				}
			}else{
				$service_price = 0;
			}
			$cpxx.='<==>byxm=服务费,cpjg='.$service_price.',xssl=1';
			
			$model_id = $order_info['model_id'];
			$cx = '';
			if ($model_id) {
				$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
				$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
				$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
				$cx = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];
			}
			
			$url = 'http://www.fqcd.3322.org:88/api.php';
			$post = array(
					'task'=>4,
					'code'=> 'fqcd123223',
					'username'=>$order_info['truename'],
					'chepai'=>$order_info['licenseplate'],
					'address'=>$order_info['address'],
					'order_time'=>date('Y-m-d H:i:s',$order_info['order_time']),
					'create_time'=>date('Y-m-d H:i:s',$order_info['create_time']),
					'remark'=>$order_info['remark'],
					//'admin'=>'郁栋',
					'admin'=>'赵轩尊',
					'mobile'=>$order_info['mobile'],
					'vin'=>$vin,
					'js'=>$info['truename'],
					'cpxx' => $cpxx,
					'cx'=>$cx
			);
// 			$sendUrl = $url.'?'.http_build_query($post);
			$this->addCodeLog('进销存自动插入', var_export($post,true).'order_id'.$order_info['id']);
			
// 			$exec = 'nohup wget '.$sendUrl.'> /var/log/send_api.log 2>&1 &';
// 			echo $exec;exit;
// 			$res = system($exec,$ret);
			$res = $this->curl($url,$post);
			$this->addCodeLog('进销存自动插入', var_export($res,true));
			$this->submitCodeLog('进销存自动插入');

		}
		
		//加日志
        $array = array(
        		'oid'=>$order_id,
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作分配技师，订单号:'.$order_id
        );
        $this->addOperateLog($array);
//        $this->success('分配技师成功',U('/Carservice/Carserviceorder/'));
        if($result==1) {
            echo 1;
        }
    }

    /**
     * 完成订单
     * @date 2014/8/14
     */
    public function process_9(){
        $id = intval($_GET['id']);

        //订单信息
        $order_param['id'] = $id;
        $order_info = $this->reservation_order_model->where($order_param)->find();

        $condition['id'] = $id;
		
        if($order_info['status'] == 2){
            $update['status'] = 9;  //订单已完成
            $update['update_time'] = time();
        }else{
            $this->error('订单完成处理失败',U('/Carservice/Carserviceorder/'.$id)); 
        }

        $this->reservation_order_model->where($condition)->save($update);
        
        //绑定库存表
//         $item = unserialize($order_info['item']);
//         $oil = $item['price']['oil'];
//         $oil_detail = $item['oil_detail'];
//         $i = $oil_1_num = $oil_2_num = 0;
//         foreach ($oil as $k_id=>$k_price){
//         	if($i == 0){
//         		$oil_1_id = $k_id;
//         		$oil_1_price = $k_price;
//         		$oil_1_num = $oil_detail[$k_id];
//         	}else{
//         		$oil_2_id = $k_id;
//         		$oil_2_price = $k_price;
//         		$oil_2_num = $oil_detail[$k_id];
//         	}
//         	$i++;
//         }
        
//         list($filter_id,$filter_price) = each($item['price']['filter']);
//         list($kongqi_id,$kongqi_price) = each($item['price']['kongqi']);
//         list($kongtiao_id,$kongtiao_price) = each($item['price']['kongtiao']);
        
//         $oil_1 = array('id'=>$oil_1_id,'price'=>$oil_1_price,'num'=>$oil_1_num);
//         $oil_2 = array('id'=>$oil_2_id,'price'=>$oil_2_price,'num'=>$oil_2_num);
//         $filter = array('id'=>$filter_id,'price'=>$filter_price);
//         $kongqi = array('id'=>$kongqi_id,'price'=>$kongqi_price);
//         $kongtiao = array('id'=>$kongtiao_id,'price'=>$kongtiao_price);
//         $this->_bind_storehouse($id,$oil_1,$oil_2,$filter,$kongqi,$kongtiao);
        
//         //绑定财务表
//         $data = array(
//         		'finance_type'=>1,
//         		'oid'=>$id
//         );
//         $this->finance_model->add($data);
//         $this->addCodeLog('bind_storehouse', '绑定财务表:'.$id.'：'.$this->finance_model->getLastSql());
//         $this->submitCodeLog('bind_storehouse');
        
        $array = array(
        		'oid'=>$id,
        		'operate_id'=>$_SESSION['authId'],
        		'log'=>'操作分配技师，订单号:'.$id
        );
        $this->addOperateLog($array);
        $this->success('订单完成处理成功！',U('/Carservice/Carserviceorder/'));
    }

	/**
     * 配置
     */
    public function ajax_car_item(){
        $style_id = intval( $_POST['style_id'] )?intval( $_POST['style_id'] ):1;
        $item_set = array();
        if( $style_id ){
            $condition['model_id'] = $style_id;
            $style_info = $this->carmodel_model->where($condition)->find();
            $set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
            if( $set_id_arr ){
                foreach( $set_id_arr as $k=>$v){
                    if(is_array($v)){
                        foreach( $v as $_k=>$_v){
                            $item_condition['id'] = $_v;
                            $item_info_res = $this->filter_model->where($item_condition)->find();
                            $item_info['id'] = $item_info_res['id'];
                            $item_info['name'] = $item_info_res['name'];
                            $item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
                            $item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
                            $item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
                            $item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
                            $item_set[$k][$_k] = $item_info;
                        }
                    }
                }
            }
        }
        $return['errno'] = '0';
        $return['msg'] = 'success';
        $return['result'] ['item_set'] = $item_set;
        $return['result'] ['oil_type'] = $style_info['oil_type'] ? $style_info['oil_type'] : 0;
        $return['result'] ['oil_num'] = $style_info['oil_mass'] ? $style_info['oil_mass'] : 0;
        $return['result'] ['style_id'] = $style_info['id'] ? $style_info['id'] : 0;
        $this->ajaxReturn( $return );
    }

    private function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        //return compact('miles','feet','yards','kilometers','meters');
        return $kilometers;
    }

    private function getStatusName($status){
        switch ($status) {
            case '0':
                return "等待处理";
                break;
            
            case '1':
                return "预约确认";
                break;

            case '2':
                return "已分配技师";
                break;

            case '7':
                return "已终止";
                break;

            case '8':
                return "已作废";
                break;

            case '9':
                return "服务已完成";
                break;

            default:
                return "等待处理";
                break;
        }
    }

	private function getOiltype($status){
        switch ($status) {
            case '1':
                return "矿物";
                break;
            
            case '2':
                return "半合成";
                break;

            case '3':
                return "全合成";
                break;

            default:
                return "未知类型";
                break;
        }
    }

	private function get_stepname($step_id){
        switch ($step_id) {
            case '0':
                return "出发";
                break;
            
            case '1':
                return "到达";
                break;

            case '2':
                return "架摄像机并开始保养";
                break;

			case '3':
                return "车辆检查报告";
                break;

			case '4':
                return "收款";
                break;

			case '5':
                return "评价";
                break;

			case '6':
                return "完成";
                break;

            default:
                return "未知类型";
                break;
        }
    }

	/**
     * 百度Geocoding API
     */
    private function geocoder($address){
        $res = file_get_contents("http://api.map.baidu.com/geocoder/v2/?address={$address}&output=json&ak=3db05159a3e3c55937fbf0160e2d8933");

        return $res;
    }

	/**
     * ajax返回坐标
     */
	 function ajax_geocoder(){
		$address = $_POST['address'];
        $res = file_get_contents("http://api.map.baidu.com/geocoder/v2/?address={$address}&output=json&ak=3db05159a3e3c55937fbf0160e2d8933");
		//print_r($res);
		$res = json_decode($res, true);
		//print_r($res);
		if($res['status'] == 0){
			$coordinate = $res['result']['location']['lat'].",".$res['result']['location']['lng'];
		}
		if($coordinate){
			$return['errno'] = '0';
			$return['errmsg'] = 'success';
			$return['result'] = array('coordinate' => $coordinate );
		}else{
			$return['errno'] = '1';
			$return['errmsg'] = '获取失败';
		}
		//print_r($return);
		$this->ajaxReturn( $return );
    }

    
    //20元券领取流程
    function code_process(){
    	$repeat['mobile'] = $_POST['mobile'];
    	$r_info = $this->carservicecode_model->where($repeat)->find();
    	if(!$r_info){
    		//获取一个随机券码绑定手机号码
    		$map['pici'] = 11;
    		$map['mobile'] = '';
    		$info = $this->carservicecode_model->where($map)->find();
    		$update['mobile'] = $_POST['mobile'];
    		$update['start_time'] = time();
    		$update['end_time'] = $update['start_time']+30*86400;
    		$umap['id'] = $info['id'];
    		$res = $this->carservicecode_model->where($umap)->save($update);
    
    		if( $res ){
    			//领券成功，给用户发短信
    			$sms = array(
    					'phones'=>$_POST['mobile'],
    					'content'=>'您的20元代金券领取成功，券码'.$info['coupon_code'].'，有效期至'.date('m-d',$update['end_time']).'。可在携车网微信公众号预约“府上养车”时使用。或致电4006602822',
    			);
    			$this->curl_sms($sms,'',1);
    			$sms['sendtime'] = time();
    			$this->model_sms->data($sms)->add();
    
    			$ret = array('message'=>'恭喜，领券成功');
    			$this->ajaxReturn($ret,'',1);
    		}else{
    			$ret = array('message'=>'领券失败，请重试');
    			$this->ajaxReturn($ret,'',0);
    		}
    	}else{
    		$ret = array('message'=>'该手机号已领过券了');
    		$this->ajaxReturn($ret,'',0);
    	}
    
    }
    private function _bind_storehouse($id,$oil_1,$oil_2,$filter,$kongqi,$kongtiao){
    	$this->addCodeLog('bind_storehouse', '开始绑定仓库2:'.$id);
    	if (!empty($oil_1['id'])) {
    		$num1 = ($oil_1['num'] >1 )?$oil_1['num']:1;
    		$update_oil_1 = array(
    				'order_id'=>$id,
    				'order_type'=>'上门保养',
    				'sale_price'=>$oil_1['price']/$num1,
    				'lock_status'=>2,
    				'update_time'=>time()
    		);
    		
    		$this->storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$oil_1['id']))->limit($num1)->save($update_oil_1);
    		$this->addCodeLog('bind_storehouse', '绑定仓库2:'.$id.'机油1：'.$this->storehouse_item_model->getLastSql());
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
    		$this->storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$oil_2['id']))->limit($num2)->save($update_oil_2);
    		$this->addCodeLog('bind_storehouse', '绑定仓库2:'.$id.'机油2：'.$this->storehouse_item_model->getLastSql());
    	}
    	if (!empty($filter['id'])) {
    		$update_filter = array(
    				'order_id'=>$id,
    				'order_type'=>'上门保养',
    				'sale_price'=>$filter['price'],
    				'lock_status'=>2,
    				'update_time'=>time()
    		);
    		$this->storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$filter['id']))->limit(1)->save($update_filter);
    		$this->addCodeLog('bind_storehouse', '绑定仓库2:'.$id.'机滤：'.$this->storehouse_item_model->getLastSql());
    	}
    	if (!empty($kongqi['id'])) {
    		$update_kongqi = array(
    				'order_id'=>$id,
    				'order_type'=>'上门保养',
    				'sale_price'=>$kongqi['price'],
    				'lock_status'=>2,
    				'update_time'=>time()
    		);
    		$this->storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$kongqi['id']))->limit(1)->save($update_kongqi);
    		$this->addCodeLog('bind_storehouse', '绑定仓库2:'.$id.'空气滤：'.$this->storehouse_item_model->getLastSql());
    	}
    	if (!empty($kongtiao['id'])) {
    		$update_kongtiao = array(
    				'order_id'=>$id,
    				'order_type'=>'上门保养',
    				'sale_price'=>$kongtiao['price'],
    				'lock_status'=>2,
    				'update_time'=>time()
    		);
    		$this->storehouse_item_model->where(array('lock_status'=>1,'item_id'=>$kongtiao['id']))->limit(1)->save($update_kongtiao);
    		$this->addCodeLog('bind_storehouse', '绑定仓库2:'.$id.'空调滤：'.$this->storehouse_item_model->getLastSql());
    	}
    }
	//AJAX阻止同一时间点过分下单
	function prevent(){
        if($_POST['type']==1){
            $order_time = strtotime($_POST['order_time'].$_POST['order_time2']);
        }else {
            $order_time = strtotime($_POST['order_time']) + ($_POST['order_time2'] + 8) * 3600;
        }
        $map['status'] = array('lt','8');
		$map['truename'] = array('notlike','%测试%');
		$map['city_id'] = 1;
		$map['order_time'] = $order_time;
		$list = $this->reservation_order_model->where($map)->Distinct(true)->field('mobile')->select();
		$i=0;
		foreach($list as $k=>$v){
			$i++;
		}
		if($_SESSION['authId']=='238'){
			$i=1;
		}
		//echo $this->reservation_order_model->getLastsql();
		echo $i;exit;
	}
        
        /*
         * 养车点点ajax更新方法
         */
        function  ajax_dd_update(){
            $map['id'] = $_REQUEST['id'] ;
            //根据订单id更新订单表
            $data['pay_type'] = 8 ;
            $data['pay_status'] = 1 ;
            
            $rs = $this->reservation_order_model->where($map)->data($data)->save();
            if($rs !== false) {
                echo 'success';
            }else{
                echo 'false';
            }

        }
        //申请改价 wwy
        function apply_change_amount(){
            $data['old_amount'] = $_REQUEST['old_amount'];
            $data['new_amount'] = $_REQUEST['new_amount'];
            if (preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $_REQUEST['new_amount'])) {
            }else{
                $return['errno'] = '1';
                $return['errmsg'] = '非法的价格';
                $this->ajaxReturn( $return );
            }
            $data['change_type'] = $_REQUEST['change_type'];
            $data['remark'] = $_REQUEST['remark'];
            $data['create_time'] = time();
            $data['admin_id'] = $_SESSION['authId'];
            $id = $this->change_amount->add($data);
            if($id){
                $return['errno'] = '0';
                $return['errmsg'] = '申请成功';
            }else{
                $return['errno'] = '1';
                $return['errmsg'] = '申请失败';
            }

            $this->ajaxReturn( $return );
        }
        
        
        
        // 检查并修改价格   wql@20150728
	public function ajax_chk_price(){
		$map['id'] = intval($this->get_true_orderid($_REQUEST['order_id']));
        $rs = $this->reservation_order_model->where($map)->find();
        //计算配件总价
        $order_items = unserialize($rs['item']);
        $item_amount = 99 ;
        foreach($order_items['price'] as $k => $v){
            $item_amount  +=  array_sum($v);
        }

        if($rs['replace_code']){
           $cond['replace_code'] = $rs['replace_code'] ;
           $count = $this->reservation_order_model->where($cond)->count(); 
        }
        //如果抵扣码存在并且没有在其他订单使用过，且抵扣金额为零，修改价格
        if($rs['replace_code'] && $count==1 && $rs['dikou_amount']==0.00){
            //获取抵扣码的金额
            $codevalue = $this->get_codevalue($rs['replace_code']);
            $data['dikou_amount'] = $codevalue ;
            $data['amount'] =  $item_amount -  $data['dikou_amount'] ;
            $this->reservation_order_model->where($map)->save($data);
            
            $return['errmsg'] = '抵扣码未抵扣，已成功抵扣';
        }else{
            $return['errmsg'] = '价格没有问题，请在价格有问题的时候使用此功能！';
        }
        
        $return['errno'] = '0';
		$this->ajaxReturn( $return );
	}
    
    
    //获取配件的品牌  wql@20150821
    public function getBrandName($item_name) {
        $filterArr = array(
            '1'=>'曼牌',
            '2'=>'马勒',
            '3'=>'博世',
            '4'=>'索菲玛',
            '5'=>'方牌',
            '6'=>'豹王',
            '7'=>'耐诺思',
            '8'=>'AC德科',
            '9'=>'汉格斯特',
            '10'=>'韦斯特',
            '11'=>'冠军',
            '12'=>'Denso', 
            '13'=>'J-WORKS',
            '14'=>'K&N',
            '14'=>'长安',
            '15'=>'标致',
            '16'=>'奇瑞'
        ) ;

        foreach ($filterArr as $k => $v) {
            if(mb_strstr($item_name,$v)){
                $brandName = $v ;    
            }
        }

        return $brandName ;

    }
    
    
    
    //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150820
    public function getNeedModel($orderCreateTime) {
        $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
        if($orderCreateTime <= $new_filter_time){
            $this->carbrand_model = D('carbrand');  //车品牌
            $this->carmodel_model = D('carmodel');  //车型号
            $this->carseries_model = D('carseries');  //车系
            $this->filter_model = D('item_filter');  //保养项目
            $this->item_oil_model = D('item_oil');  //保养机油
            $this->item_model = D('item_filter');  //保养项目
        }else{
            $this->carbrand_model = D('new_carbrand');  //车品牌
            $this->carmodel_model = D('new_carmodel');  //车型号
            $this->carseries_model = D('new_carseries');  //车系
            $this->filter_model = D('new_item_filter');  //保养项目
            $this->item_oil_model = D('new_item_oil');  //保养机油
            $this->item_model = D('new_item_filter');  //保养项目
        }
        
    }


}
