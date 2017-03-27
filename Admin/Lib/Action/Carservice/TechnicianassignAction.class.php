<?php

class TechnicianassignAction extends CommonAction {
	function __construct() {
		parent::__construct();
		$this->UserroleModel = M('tp_admin.role_user','xc_');//网站用户关系表
		$this->UserModel = M('tp_admin.user','xc_');//网站用户表
		$this->TechnicianModel = M('tp_xieche.technician','xc_');//技师信息拓展表
		$this->CarbrandModel = M('tp_xieche.carbrand','xc_');//技师信息拓展表
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');//订单表
		$this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表
		$this->model_sms = M('tp_xieche.sms','xc_');//手机短信
		$this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
		$this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
		$this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
	}
	
	//算距离
	public function getDistance($latlng1,$latlng2){
		$latlng1 = explode(',',$latlng1);
		$latlng2 = explode(',',$latlng2);
		list($lat1,$lng1) = $latlng1;
		list($lat2,$lng2) = $latlng2;
		$EARTH_RADIUS = 6378.137;
		$radLat1 = $this->rad($lat1);
		//echo $radLat1;
		$radLat2 = $this->rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = $this->rad($lng1) - $this->rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) +
				cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
		$s = $s *$EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return (int)$s;
	}
	function rad($d)
	{
		return $d * 3.1415926535898 / 180.0;
	}
	
	
// 	function getDistance2($latlng1,$latlng2)
// 	{
// 		 // 纬度1,经度1 ~ 纬度2,经度2
// 		 $latlng1 = explode(',',$latlng1);
// 		 $latlng2 = explode(',',$latlng2);
// 		 list($lat1,$lng1) = $latlng1;
// 		 list($lat2,$lng2) = $latlng2;
// 		 $EARTH_RADIUS = 6378.137;
// 		 $radLat1 = $lat1 * pi() / 180.0; 
// 		 $radLat2 = $lat2 * pi() / 180.0; 
// 		 $a = $radLat1 - $radLat2;
// 		 $b = $lng1 * pi() / 180.0 - $lng2 * pi() / 180.0;
// 		 $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
// 		 $s = $s * $EARTH_RADIUS;
// 		 $s = round($s * 10000) / 10000;
// 		 return (int)$s;
// 	}
	
	public function distance($order,$technician){
		foreach ($technician as $technician_name=>$v){
			$dis[$technician_name] = $this->getDistance($order,$v);
		}
		asort($dis);
// 		$ret = array_shift($dis);
		return $dis;
	}
		
	//技师排班
	public function index() {
		$today = strtotime(date('Y-m-d',time()));

		if(!empty($_POST['start_time'])){
			$today = $start_time = strtotime($_POST['start_time']);
			$map['order_time'] = array('gt',$start_time);
			$this->assign('start_time',$_POST['start_time']);
		}else{
			$this->assign('start_time',date('Y-m-d',time()));
		}
// 		if(!empty($_POST['end_time'])){
// 			$end_time = strtotime($_POST['start_time'].' 23:59:59');
// 			$map['order_time'] = array('gt',$end_time);
// 			$this->assign('start_time',$_POST['start_time']);
// 		}
// 		if(!empty($_POST['start_time']) && isset($_POST['end_time'])){
// 			$map['order_time'] = array(array('gt',$start_time),array('lt',$end_time));
// 			$this->assign('end_time',$_POST['end_time']);
// 		}
		if(!empty($_POST['technician_id'])){
			$map['technician_id'] = $_POST['technician_id'];
			$this->assign('s_js',$_POST['technician_id']);
		}
		if (!empty($_POST['city_id'])) {
			$map['city_id'] = $_POST['city_id'];
			$this->assign('city_id',$_POST['city_id']);
		}
		
		$last_time = array();
		
		$tomorrow = $today + 86400;
		$where2['order_time'] = $map['order_time'] = array(array('gt',$today),array('lt',$tomorrow));
		$where2['status'] = array('neq',8);
		//先用李雅杰测试
		if($_SESSION['authId']==287 or $_SESSION['authId']==288){
			//根据城市划分可查看订单权限
			$map['city_id'] = array('in',$_SESSION['city_id']);
		}

		//查询当天技师情况
		$coo = array();//所有技师傅今天的地址
		$name1['status']= 1;
		$technician = $this->TechnicianModel->where($name1)->field('id,truename')->select();
		if ($technician) {
			foreach ($technician as $key => &$val){
				$technician_id = $val['id'];
				
				$last_time[$technician_id] = '';
				
				$where2['technician_id'] = $technician_id;
				$res = $this->reservation_order_model->field('longitude,latitude,order_time')->where($where2)->order('order_time asc')->find();//技师是不是在客户地
				if ($res) {
					$coo[$res['order_time'].'|'.$val['truename']] = $res['longitude'].','.$res['latitude'];//技师坐标
				}else{
					$coo['|'.$val['truename']] = '121.618094,31.036347';//技师在基地的坐标
				}
				unset($val);
			}
		}
 		//var_dump($coo);
		//查询当天的所有订单
		
		$count = $count1 = $count2 = $count3 = $count6 = $count7 = 0;
		$count8 = $count9 = $count10 = $count11 = $count12 = $count13 = 0;
		$count14 = $count15 = $count16 = $count17 = $count18 = $count19 = 0;
		$count = $this->reservation_order_model->where($map)->count();
        $list = $this->reservation_order_model->where($map)->order('order_time ASC')->select();
        //var_dump( $this->reservation_order_model->getLastSql());
		$data = array();
		if(is_array($list)){
			
			foreach($list as $k=>$v){
				if ($v['status'] == 8) {
					continue;
				}
				if($v['technician_id']){
					$condition['id'] = $v['technician_id'];
					$technician_info = $this->TechnicianModel->where($condition)->find();
					$v['technician_name'] = $technician_info['truename'];
				}
				$v['id'] = $this->get_orderid($v['id']);
				
				switch ($v['order_type']) {
					case 1:
						$name = '保养订单';
						$count1++;
						break;
					case 2:
						$name = '检测订单';
						$count2++;
						break;
					case 3:
						$name = '淘宝99元保养订单';
						$count3++;
						break;
					case 6:
						$name = '免99元服务费订单';
						$count6++;
						break;
					case 7:
						$name = '黄喜力套餐';
						$count7++;
						break;
					case 8:
						$name = '蓝喜力套餐';
						$count8++;
						break;
					case 9:
						$name = '灰喜力套餐';
						$count9++;
						break;
					case 10:
						$name = '金美孚套餐';
						$count10++;
						break;
					case 11:
						$name = '爱代驾高端保养';
						$count11++;
						break;
					case 12:
						$name = '爱代驾中端保养';
						$count12++;
						break;
					case 13:
						$name = '好车况';
						$count13++;
						break;
					case 14:
						$name = '好空气';
						$count14++;
						break;
					case 15:
						$name = '保养服务+检测+养护';
						$count15++;
						break;
					case 16:
						$name = '矿物质油保养套餐+检测+养护';
						$count16++;
						break;
					case 17:
						$name = '半合成油保养套餐+检测+养护';
						$count17++;
						break;
					case 18:
						$name = '全合成油保养套餐+检测+养护';
						$count18++;
						break;
					default:
						$name = '保养订单';
						break;
				
				}
				
				$v['order_name'] = $name;
				$order_time = date('H',$v['order_time']);
				//算距离
				$order_coo = $v['longitude'].','.$v['latitude'];//客户地址
				$distances = $this->distance($order_coo, $coo);
				//var_dump($v['id'].'=>'.$order_coo);
				$allDistance = array();
				
				if ($distances) {
					$i = 1;
					
					foreach ($distances as $name => $distance){
						$js = explode('|', $name);
						$time = $js[0];
						$near_time = '';
						
						if ($time) {
							$near_time = str_replace('-', '', ($v['order_time']-$time)/3600);
						}

						if(!empty($v['technician_id'])&&$v['technician_name']===$js[1]){	//已经分配
							
							$v['near_technician'] =  $v['technician_name'];
							
							if (!empty($last_time[$v['technician_id']]) ) {
								$v['near_time'] =  str_replace('-', '', ($v['order_time'] - $last_time[$v['technician_id']])/3600) ;
							}
							
							$v['near_distance'] = $distance;
							
							$last_time[$v['technician_id']] = $time;
							
						}else{
							if ($i == 1){
								$near_name = $js[1];    //默认最近的
								$v['near_time'] = $near_time;
								$v['near_distance'] = $distance;
							}
						}

						$v['allDistance'][] = array(
								'name'=>$js[1],
								'distance'=>$distance,
								'time'=>$near_time
						);
						
						$i++;
					}
				}
				$data[$order_time][] = $v;
			}
		}
		$count_js = count($data);
		$uid = $_SESSION['authId'];
		$userData = $this->UserModel->field('remark')->where( array('id'=>$uid) )->find();
		if ($userData && strstr($userData['remark'],'客服')) {
			$this->assign('isKefu',true);
		}
		//$count = $count1 + $count2 + $count3 + $count6 + $count7 + $count8 + $count9 + $count10 + $count11 + $count12 + $count13 + $count14 + $count15 + $count16 + $count17 + $count18;
		$this->assign('countDetail', $countDetail);
		// 		var_dump($list);
		$this->assign('count1',$count1);
		$this->assign('count2',$count2);
		$this->assign('count3',$count3);
		$this->assign('count6',$count6);
		$this->assign('count7',$count7);
		$this->assign('count8',$count8);
		$this->assign('count9',$count9);
		$this->assign('count10',$count10);
		$this->assign('count11',$count11);
		$this->assign('count12',$count12);
		$this->assign('count13',$count13);
		$this->assign('count14',$count14);
		$this->assign('count15',$count15);
		$this->assign('count16',$count16);
		$this->assign('count17',$count17);
		$this->assign('count18',$count18);
		
		$this->assign('count',$count);
		$this->assign('count_js',$count_js);
		
		$this->assign('authId',$_SESSION['authId']);
		$this->assign('jses',$technician);
		$this->assign('technician',$allDistance);
		$this->assign('list',$data);
		$this->display();
	}

	//绑定技师
	function bindTech(){
		$technician_name = @$_POST['technician_name'];
		$order_id = @$_POST['order_id'];
		$order_id = $this->get_true_orderid($order_id);
		if (!$technician_name || !$order_id) {
			$this->ajaxReturn('参数错误','',0);
		}
		
		$order_param['id'] = $order_id;
		$order_info = $this->reservation_order_model->where($order_param)->find();
		
		if($order_info['status'] != 1){
			$this->ajaxReturn('该订单还没有预约确认，不能排班','',0);
		}
		
		$model_technician = D('Technician');
		$info = $model_technician->where(array('truename'=>$technician_name))->find();
		
		if (!$info) {
			$this->ajaxReturn('技师不存在','',0);;
		}
		
		$dataUpdate = array(
			'technician_id' => $info['id'],
			'status'=>2,
			'update_time'=>time()
			
		);
		$where = array(
			'id' => $order_id
		);
		
		$res = $this->reservation_order_model->where($where)->save($dataUpdate);

		//分配技师完成之后通知用户
		
		$model_user = M('tp_admin.user','xc_');
		$user_info = $model_user->where(array('id'=>$info['user_id']))->find();
		//改为7点脚本自动发了
		/*if ($order_info['city_id'] == 1) {
			$sms = array(
					'phones'=>$order_info['mobile'],
					'content'=>'亲，您'.date('m',$order_info['order_time']).'月'.date('d',$order_info['order_time']).'日预约的携车网-府上养车，将由五星技师'.$technician_name.'师傅（'.$user_info['mobile'].'）上门为您服务。期待与您的见面！有疑问询：400-660-2822。',
			);
			$this->curl_sms($sms,'',1,1);
			$sms['sendtime'] = time();
			$this->model_sms->data($sms)->add();
		}*/
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
			$this->addCodeLog('进销存自动插入', var_export($post,true).'order_id'.$order_info['id']);
			//var_dump($post);exit;
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
		
		if ($res) {
			$this->ajaxReturn('订单分配技师成功','',1);
		}else{
			$this->ajaxReturn('订单分配技师失败','',0);
		}
		
	}

    /**
     * 新版自动排单
     */
    public function autoAssignNew()
    {

        header('Content-Type:text/html; charset=utf-8');

        // 组装日期城市数据
        $day   = isset($_GET['day']) ? $_GET['day'] : (isset($_SESSION['assignToday']) ? $_SESSION['assignToday'] : date('Y-m-d'));
        $today = strtotime($day);
        $city  = isset($_GET['city']) ? $_GET['city'] : (isset($_SESSION['city']) ? $_SESSION['city'] : 1);
        $tomorrow = $today + 24 * 3600;

        // 组装查询条件
        $map   = array(
            'status' => array('eq', 1),             // 预约确认
            'city_id' => array('eq', $city),        // 城市ID
            'order_time' => array(                  // 上门时间
                array('gt', $today),
                array('lt', $tomorrow)
            ),
            'area_id' => array(                     // 地区
                array('EXP', 'IS NOT NULL'),
                array('neq', 0)
            )
        );

        // 获取订单
        $list = $this->reservation_order_model->where($map)->order('order_time ASC')->select();

        // 区域划分
        $areaGroup = array(
            1 => array('310115', '310119', '310120'),                       // 1组 浦东新区 南汇区 奉贤区 (浦东)
            2 => array('310112', '310117', '310118', '310114'),             // 2组 闵行区 松江区 青浦区 嘉定区 (浦西)
            3 => array('310105', '310107', '310106', '310104', '310103', '310101'), // 3组 长宁区 普陀区 静安区 徐汇区 卢湾区 黄浦区 (浦西)
            4 => array('310110', '310109', '310113', '310108')              // 4组 杨浦 虹口 宝山 闸北 (浦西)
        );

        // 获取技师班组
        $technicianGroups = M('tp_xieche.technician_group', 'xc_')->where(array(
            'city' => $city,
            'status' => 1
        ))->select();

        // 按车辆类型分组 (1:普通车辆, 2:有除碳功能的车辆)
        $carList = array();
        foreach ($technicianGroups as $k => $v) {

            // 普通车辆
            if ($v['type'] == 1) {
                $carList[1][] = $v;
                continue;
            }

            // 除碳车辆
            $carList[2][] = $v;
        }

        // 特殊装备的车辆订单
        $secondAssign  = array();

        // 分组后的订单列表
        $assignedList  = array();
        $tmpList       = array();

        // 订单分组
        foreach ($list as $key => $order) {
            // 订单时间段
            $order['hour']     = date('H', $order['order_time']);

            // 订单位置
            $order['position'] = $order['longitude'].','.$order['latitude'];

            // 区域 (按区域,时间段分组订单)
            if (in_array($order['area_id'], $areaGroup[1])) {
                $order['dist'] = 1;
                $assignedList[1][$order['hour']][] = $order;
            } elseif (in_array($order['area_id'], $areaGroup[2])) {
                $order['dist'] = 2;
                $assignedList[2][$order['hour']][] = $order;
            } elseif (in_array($order['area_id'], $areaGroup[3])) {
                $order['dist'] = 3;
                $assignedList[3][$order['hour']][] = $order;
            } else {
                $order['dist'] = 4;
                $assignedList[4][$order['hour']][] = $order;
            }

            if (mb_strpos($order['remark'], '除碳') !== false) { // 如果有除碳工作，技师类型不同
                $secondAssign[] = $order;
            }

            // 重排订单列表
            $tmpList[$order['id']] = $order;

        }
        // 订单分组完毕

        // 已分配的车辆
        $workCars = array();

        // 未处理的订单
        $untreatedOrders = array();

        // 如果存在除碳类型
        if ($secondAssign) {

            // 遍历所有除碳订单
                // 为每一个除碳订单都分配一辆车，同时将这个订单从除碳订单列表和区域订单列表中删除
                    // 获取当前除碳订单的时间段。
                    // 遍历区域列表当前时间段的所有订单
                        // 如果订单与除碳订单距离小于 10 km, 且时间段不同则加入当前车辆。





            foreach ($secondAssign as $k => $order) {
                $area = $order['dist'];
                $pos  = $order['longitude'].','.$order['latitude'];
                $hour = $order['hour'];

                // 循环已分配过的车辆
/*                if ($workCars) {
                    // 循环已安排的车辆
                    foreach ($workCars[$area] as $kk => $car) {

                        // 如果车辆该时间段有单
                        if (array_key_exists($order['hour'], $car['orders']) && $car['orders'][$order['hour']]) { // 此处可扩展 (地点相同，时间段不同可以人工调整)
                            $lastOrder = end( $car['orders'][$order['hour']]);

                            // 如果手机与该时间段的最后一班手机相同，则直接加入该车辆
                            if ($order['mobile'] == $lastOrder['mobile']) {
                                $workCars[$area][$kk]['orders'][$hour][] = $order;

                                // 从 secondeAssign 中删除
                                if ($rmkey = array_search($order, $secondAssign) !== false) {
                                    unset($secondAssign[$rmkey]);
                                }

                                // 从订单列表中删除已分配的
                                $this->unsetOrder($assignedList[$area], $order);

                                continue 2;
                            }
                        }
                    }
                }*/

                // 分配带有特殊工具的新车

                if (!$carList[2]) {
                    $untreatedOrders[$order['dist']][] = $order;
                    continue;
                }

                $newCar = array_shift($carList[2]);

                // 删除订单
                $this->unsetOrder($assignedList[$area], $order);


                // 为新车建立所有时间段
                $this->setAllHours($newCar);

                // 在新车的指定时间段中添加订单
                $newCar['orders'][$hour][] = $order;

                // 遍历订单所在区域的所有订单
                foreach ($assignedList[$area] as $kk => $orders) {
                    // 如果订单时间和遍历的时间段相同则跳过当前时间段
                    if ($kk == $hour) {
                        continue;
                    }

                    // 距离列表
                    $minDist = array('order' => 0, 'dist' => 11);
                    foreach ($orders as $a => $v) {
                        // 此订单的位置
                        $tmpPos  = $v['longitude'].','.$v['latitude'];
                        // 订单之间的距离
                        $tmpDist = $this->getDistance($pos, $tmpPos);

                        // 不管什么时间段，如果最短距离都为 0， 则都加入新车
/*                        if ($tmpDist == 0) {
                            $newCar['orders'][$kk][] = $v;

                            // 从订单列表中删除已分配的
                            $this->unsetOrder($assignedList[$area], $v);

                            // 开始下个个订单
                            continue 1;
                        }*/

                        $distArr[$v['id']] = $tmpDist;

                        // 检测最小距离
                        if ($a == 0 || $minDist['dist'] >= $tmpDist ) {
                            $minDist = array('order' => $v['id'], 'dist' => $tmpDist);
                        }
                    }

                    // 如果最短距离小于 10 公里, 则加入新车的订单列表
                    if ($minDist['dist'] <= 10) {
                        $newCar['orders'][$kk][] = $tmpList[$minDist['order']];

                        // 从订单列表中删除已分配的
                        $this->unsetOrder($assignedList[$area], $tmpList[$minDist['order']]);

                    }
                }

                $workCars[$area][] = $newCar;

            }

        }

//        dd($assignedList);
//dd($workCars[1]);

        // 开始分配正常订单, 区域ID为 (1,2,3,4)
        $carList = $carList[1] + $carList[2];

        foreach (range(1, 4) as $area) {
            $this->assignAreaOrder($assignedList[$area], $carList, $area, $workCars[$area], $untreatedOrders);
        }

        $areas    = array(
            '1' => '一组 (浦东新区, 南汇区, 奉贤区)',
            '2' => '二组 (闵行区, 松江区, 青浦区, 嘉定区)',
            '3' => '三组 (长宁区, 普陀区, 静安区, 徐汇区, 卢湾区, 黄浦区)',
            '4' => '四组 (杨浦, 虹口, 宝山, 闸北)',
        );

        $carTypes = array(
            1 => '普通车辆',
            2 => '除碳车辆'
        );

        $this->assign('carTypes',   $carTypes);
        $this->assign('areas',      $areas);
        $this->assign('list',       $assignedList);
        $this->assign('workCars',   $workCars);
        $this->display('auto_assign_new');

    }

    /**
     * 统计数据订单数量
     *
     * @param $assignList
     * @return array
     */
    private function statistics($assignList)
    {
        $results = array();

        $areas = array(1, 2, 3, 4);

        foreach ($areas as $k => $v) {
            if (array_key_exists($v, $assignList)) {
                $results[$v] = 0;
                foreach ($assignList[$v] as $vv) {
                    $results[$v] += count($vv);
                }
            }
        }

        return $results;

    }

    /**
     * 分配指定区域的订单
     *
     * @param $orders       订单列表(按照时间段分组)
     * @param $cars         未分配的技师组列表(车辆列表)
     * @param $area_id      区域ID
     * @param $workCars     已分配的车辆列表
     * @param $untreatedOrders
     */
    private function assignAreaOrder(&$orders, &$cars, $area_id, &$workCars, &$untreatedOrders)
    {

        // 为区域分配的车辆数量，取决于当天内该区域单数最多的时间段的单数
        $areaCarCount = array_reduce($orders, function($tecCount, $hourOrders) {
            $tecCount = ($tecCount > count($hourOrders)) ? $tecCount : count($hourOrders);
            return $tecCount;
        });

        // 所有时间段取出
        $hours = array_keys($orders);

        // 遍历所有订单, 按照时间段分组
        foreach ($orders as $hour => $hourOrders) {

            // 遍历小时内的订单
            foreach ($hourOrders as $k => $hourOrder) {

                // 如果是首个时间段的订单
                if ($hour == $hours[0]) {
                    // 分配新车
                    if (!$cars) {
                        $untreatedOrders[$hourOrder['dist']][] = $hourOrder;
                        continue;
                    }

                    $newCar = array_shift($cars);

                    // 为新车建立所有时间段
                    $this->setAllHours($newCar);

                    // 将订单加入新车的当前时间段中
                    $newCar['orders'][$hour][] = $hourOrder;

                    // 将新车加入到 已分配车辆列表中
                    $workCars[] = $newCar;
                } else {

                    // 定义最短距离(11km)和相对订单
                    $minDist = array('car' => 0, 'dist' => 11);

                    // 遍历已安排的车辆
                    foreach ($workCars as $kk => $car) {
                        // 获取当前车的所有小时段
                        $carHours  = array_keys($car['orders']);

                        // 获取当前车辆当前时段的的前一个时段
                        $prevHour  = array_reduce($carHours, function($lastOrderHour, $h) use ($hour) {
                            $lastOrderHour = 0;

                            if ($h < $hour) {
                                $lastOrderHour = $h;
                            }

                            return $lastOrderHour;
                        });

                        // 如果当前车辆的前一时间段没有安排, 则跳过当前车辆。
                        if (!$prevHour) {
                            continue;
                        }

                        // 获取最后一个订单
                        $lastOrder = end(array_get($car, 'orders.'.$prevHour));

                        // 获取最后一个订单位置
                        $lastPos   = array_get($lastOrder, 'position', '0,0');
                        // 获取距离
                        $tmpDist = $this->distance($lastPos, $hourOrder['position']);

                        // 如果距离为 0, 则直接加入当前车辆
                        if ($tmpDist == 0) {

                            // 加入当前的车辆
                            $workCars[$kk]['orders'][$hour][] = $hourOrder;

                            // 删除当前的订单
                            $this->unsetOrder($orders, $hourOrder);
                            continue 2;
                        }


                        // 判断是否为最短距离, 如果小于最短距离，则更新
                        if ($tmpDist < $minDist['dist']) {
                            $minDist['dist'] = $tmpDist;
                            $minDist['car'] = $kk;
                        }

                    }

                    // 如果最短距离都超过 10 km, 则分配新的车辆
                    if ($minDist['dist'] > 10) {
                        // 分配新车
                        if (!$cars) {
                            $untreatedOrders[$hourOrder['dist']][] = $hourOrder;
                            continue;
                        }

                        $newCar = array_shift($cars);

                        // 为新车创建所有时间段
                        $this->setAllHours($newCar);

                        // 将订单加入新车的当前时间段中
                        $newCar['orders'][$hour][] = $hourOrder;

                        // 将新车加入已分配的车辆
                        $workCars[] = $newCar;

                        $this->unsetOrder($orders, $hourOrder);
                        continue 2;
                    } else {
                        // 将订单加入已存在的车辆
                        $workCars[$minDist['car']]['orders'][$hour][] = $hourOrder;

                        // 删除订单
                        $this->unsetOrder($orders, $hourOrder);
                        continue 1;
                    }
                }
            }
        }
    }

    /**
     * 为新车建立所有的时间段 (方便前台页面遍历)
     */
    private function setAllHours(&$car)
    {
        // 从 8 点 - 21 点
        for ($i = 8; $i <= 21; $i++) {
            $num = str_pad($i, 2, 0, STR_PAD_LEFT);

            $car['orders'][$num]  = array();
        }

    }

    /**
     * 获取最短距离
     *
     * @param $orders
     */
    private function findMinDist($orders)
    {

    }

    /**
     * 删除指定订单列表中的一个订单
     *
     * @param $orders
     * @param $order
     */
    private function unsetOrder(&$orders, $order)
    {
        // 如果 在 $orders 中能找到 $order, 则在 $orders 中删除 $order
        if ($rmKey = array_search($order, $orders[$order['hour']]) !== false) {
            $rmKey = array_search($order, $orders[$order['hour']]);
            unset($orders[$order['hour']][$rmKey]);
        }
    }


    /**
     * 自动排班 (老版)
     */
	function autoAssign(){
		$day = @$_GET['day'];
		//查询当天的订单
		if ($day) {
			$today = strtotime($day);
		}
		$map['status'] = array('neq',8);
		$map['technician_id'] = array( array('EXP','IS NULL'),array('eq',0), 'or') ;
		if(!empty($_POST['start_time'])){
			$today = $start_time = strtotime($_POST['start_time']);
			$this->assign('start_time',$_POST['start_time']);
		}else{
			$this->assign('start_time',$day);
		}
		if(!empty($_POST['technician_id'])){
			$map['technician_id'] = $_POST['technician_id'];
			$this->assign('s_js',$_POST['technician_id']);
		}
		if (!empty($_POST['city_id'])) {
			$map['city_id'] = $_POST['city_id'];
			$this->assign('city_id',$_POST['city_id']);
		}

		//先用李雅杰测试
		//if($_SESSION['authId']==287 or $_SESSION['authId']==288){
			//根据城市划分可查看订单权限
                        if($_SESSION['authId']==1){
                            $map['city_id'] = 1;
                        }else{
                            $map['city_id'] = array('in',$_SESSION['city_id']);
                        }
			
		//}

		if (!$today && isset($_SESSION['assignToday'])) {
			$today = $_SESSION['assignToday'];
		}
		$tomorrow = $today + 86400;
		
		$map['order_time'] = array(array('gt',$today),array('lt',$tomorrow));
	
		$mAutoSchedule = D('auto_schedule');
		
		//切换日期重新排班
		if (!empty($_SESSION['assignToday']) && ($_SESSION['assignToday'] != $today) ) {
			$mAutoSchedule->query('truncate xc_auto_schedule');
		}
		
		//首次进入
		if (!isset($_SESSION['assignToday'])) {
			$mAutoSchedule->query('truncate xc_auto_schedule');
		}
		
		$mTechnician = D('Technician');
		//如果已经智能分配过 那么直接从数据库里面读
		$dataFromDbs = $mAutoSchedule->where()->select();
		//echo $mAutoSchedule->getLastsql();
		//print_r($dataFromDbs);
		if ($dataFromDbs) {
                        //echo '11111';
			$carArrs = array();
			foreach ($dataFromDbs as $k => $dataFromDb){
				$order_id_arr = explode('|', $dataFromDb['order_id']) ;
				if ($order_id_arr) {
					$carArr = array();
					$last_coo = '';
					foreach ($order_id_arr as $order_key => $order_id){
	
						if (!$order_id) {
							continue;
						}
						$data = $this->reservation_order_model->where( array('id'=>$order_id) )->find();
						$data['tmpId'] = $this->get_orderid($data['id']);
						$order_time = $data['order_time'];
						$hour = date('H',$order_time);
						$data['hour'] = $hour;
						$order_coo = $data['longitude'].','.$data['latitude'];
						if ($order_key > 0 ) {
							$distance = $this->getDistance($order_coo, $last_coo);
							$data['near_distance'] = $distance;
						}
						
						$last_coo = $order_coo;
						
						$data['dis'] = $order_coo;
						$data['order_name'] = $this->getOrderNameBytype($data['order_type']);
	
						$techData = $mTechnician->field('id,truename')->where( array('id'=>$dataFromDb['tech_id']) )->find();
						$data['technician_name'] = $techData['truename'];
						$data['tech_id'] = $techData['id'];
						
						$techName[$k+1] = $techData['truename'];
						$carArr[] = $data;
					}
					usort($carArr, function($a,$b){
						if ($a['order_time'] == $b['order_time']) {
							return 0;
						}
						return ($a['order_time'] < $b['order_time']) ? -1 : 1;
					});
					$carArrs[] = $carArr;
				}
			}
                        
                        //print_r($carArrs) ;
		}else{
			$list = $this->reservation_order_model->where($map)->order('order_time ASC')->select();
			//echo $this->reservation_order_model->getLastsql();
			$first_hour = '';
			$carArrs = array();
			if ($list) {
				//先清空数据库
				$mAutoSchedule->query('truncate xc_auto_schedule');
				// 			$mAutoSchedule->where('1')->delete();
				$j = 0 ;
				$lastOrderHour = '';
				$arr_n = array('虹口区','杨浦区','嘉定区','宝山区','闸北区');
				$arr_d = array('黄浦区','长宁区','静安区','普陀区');
				$arr_x = array('闵行区','徐汇区','松江区','青浦区');
				$arr_s = array('浦东新区','奉贤区','南汇区');
				foreach ($list as $key => $val){
					//排班8：00-8：59的所有订单初始化分配车辆
					$val['tmpId'] = $this->get_orderid($val['id']);
					$order_time = $val['order_time'];
					$hour = date('H',$order_time);
					$val['hour'] = $hour;
					$order_coo = $val['longitude'].','.$val['latitude'];
					$val['dis'] = $order_coo;
					$val['order_name'] = $this->getOrderNameBytype($val['order_type']);
					$longitude = $val['longitude'];
					$latitude = $val['latitude'];
					$quyu = $this->get_address($latitude,$longitude);
					if(in_array($quyu,$arr_n)){
						$val['quyu'] = 1;
					}elseif(in_array($quyu,$arr_d)){
						$val['quyu'] = 2;
					}elseif(in_array($quyu,$arr_x)){
						$val['quyu'] = 3;
					}elseif(in_array($quyu,$arr_s)){
						$val['quyu'] = 4;
					}

					if ($key == 0) {
						$first_hour = $hour;
					}
					if ($hour == $first_hour) {
						$carArrs[][] = $val; //初始化车辆,分配数据-》车辆-》订单
                                                //print_r($carArrs);
					}else{
						//排班后面的订单放入相应的车辆
						/*按照距离和保养单之后不能放保养单的逻辑排班*/
						/**/
                                                $nearDis = '';

						$choseKey = 0;
						$i = 0;
						$val['not'] = '';
						foreach ($carArrs as $carKey => $carArr){
							//拉出这辆车的最后一个订单
							$lastOrder = end($carArr);
							//两单是同一个顾客可以放一起
							if ($val['mobile'] != $lastOrder['mobile']) {
								//如果这个订单是保养订单,且相差时间为一小时内跳过
								/*if ($lastOrder['order_type'] != 2 && ($hour-$lastOrder['hour'] < 2 ) ) {
									$i++;
									$val['not'] .= $carKey."|";
									//echo $i.'不合适'.'<br>';
									continue;
								}*/
								if ($val['quyu'] != $lastOrder['quyu']) {
									$i++;
									$val['not'] .= $carKey."|";
									//echo $i.'不合适'.'<br>';
									continue;
								}
								if( $hour - $lastOrder['hour'] <1 ) //同一时间段只能有一个订单
								{
									$i++;
									$val['not'] .= $carKey."|";
									continue;
								}
							}
// 							echo '合适<br>';
							//计算最短距离
							foreach ($carArr as $k => $order){
								$nearTime = $order['hour'] - $val['hour'];
								if ($nearTime < 0) {
									$nearTime = str_replace('-','',$nearTime);
								}
								if ( $nearTime >=4 ) {
									$order['dis'] = '121.618094,31.036347';	// 如果两单相差4小时以上 技师回到了基地
								}
								$distance = $this->getDistance($order_coo, $order['dis']);
								if ($k == 0) {
									$nearDis = $distance;
								}
								if ($distance <= $nearDis) {
									$nearDis = $distance;
									$choseKey = $carKey;	//该订单应该放到哪辆车里面
								}
							}
							break;
						}
						$val['near_distance'] = $nearDis;
// 						echo $i.'=='.count($carArrs).'<br>';
						if ($i == count($carArrs)) {
							$carArrs[][] = $val; //不适合的订单放到新的车辆里面去
						}else{
							$carArrs[$choseKey][] = $val;
						}

						/*
						//按照区域排班
						$area_qu = '';
						print_r($carArrs);
						foreach ($carArrs as $carKey => $carArr){
							//拉出这辆车的最后一个订单
							$lastOrder = end($carArr);
							$l_longitude = $lastOrder['longitude'];
							$l_latitude = $lastOrder['latitude'];
							$l_qu = $this->get_address($l_latitude,$l_longitude);
							$v_longitude = $val['longitude'];
							$v_latitude = $val['latitude'];
							$val_qu = $this->get_address($v_latitude,$v_longitude);
							if($l_qu==$val_qu and $lastOrder['order_time']==$val['order_time']){
								$carArrs[$carKey][] = $val;//相同的区放在一部车上
							}else{
								$carArrs[][] = $val;//不适合的订单放到新的区域里面去
							}
						}*/

					}
				}
                                //车辆订单已分配完毕。
				//print_r($carArrs);
                                //exit;


				echo "</br>";
				foreach ($carArrs as $key => &$carArr){
					$order_id = '';
					foreach ($carArr as &$val){
						//随机分配一个技师绑定到这个单子上
						//刘辉的逻辑，暂时看不懂。。。
						//$techData = $mTechnician->field('id,truename')->where( array('id'=>$key+14))->find();
						$t_map['id'] = $key+14;
						$t_map['city_id'] = 1;
						//按城市取技师
						$techData = $mTechnician->field('id,truename')->where($t_map)->find();
						//echo $mTechnician->getLastsql();
						$val['technician_name'] = $techData['truename'];
						$val['tech_id'] = $techData['id'];
						$order_id .= $val['id'].'|';
						$tech_id = $techData['id'];
						unset($val);
						$techName[$key+1] = $techData['truename'];
					}
					//插入数据库
					$insert = array(
							'order_id' => $order_id,
							'tech_id' => $tech_id,
							'create_time' => time()
					);
					$mAutoSchedule->add($insert);
					unset($carArr);
				}
				$_SESSION['assignToday'] = $today;
			}
		}
		//组织数据
		$order_time_arr = array('08','09','10','11','12','13','14','15','16','17','18','19','20','21');//组织数据

		if (!empty($carArrs)) {
			foreach ($carArrs as $carArr){
				$newCarArr = array();
				foreach ($carArr as $k=>$v){
					
					//查找这个技师的当天已经排好的订单
					$where_new = array(
							'technician_id'=>$v['tech_id'],
							'order_time' => array(array('gt',$today),array('lt',$tomorrow))
					);
					$order_datas = $this->reservation_order_model->where($where_new)->select();
					if ($order_datas) {
						foreach ( $order_datas as $order_data){
							$order_data['technician_name'] = $v['technician_name'];
							$order_data['tmpId'] = $this->get_orderid($order_data['id']);
							$order_data['hour'] = date('H',$order_data['order_time']);
							$order_data['order_name'] = $this->getOrderNameBytype($order_data['order_type']);
							$order_data['alreadyAssign'] = 1;
							$newCarArr[$order_data['hour']][] = $order_data;
						}
					}
				
					$newCarArr[$v['hour']][] = $v;
				}
				$key = array_keys($newCarArr);//判断时间段有没有数据
				$diff = array_diff($order_time_arr,$key);
				if ( $diff ) {
					foreach ($diff as $diff_time){
						$newCarArr[$diff_time] = array();
					}
				}
				ksort($newCarArr);
				$newCarArrs[] = $newCarArr;
			}
		}
                
		//print_r($newCarArrs);
                
		//技师
		$technicial = $mTechnician->select();
		//车辆
		$cars = count($carArrs)+1;
		$this->assign('cars',$cars);
		$this->assign('techName',$techName);
		$this->assign('technical',$technicial);
		$this->assign('carArrs',$newCarArrs);
		$this->display('auto_assign');
	}
	private function getOrderNameBytype($order_type){
		switch ($order_type) {
			case 1:
				$name = '保养订单';
				break;
			case 2:
				$name = '检测订单';
				break;
			case 3:
				$name = '淘宝99元保养订单';
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
				$name = '保养服务+检测+养护';
				break;
			case 16:
				$name = '矿物质油保养套餐+检测+养护';
				break;
			case 17:
				$name = '半合成油保养套餐+检测+养护';
				break;
			case 18:
				$name = '全合成油保养套餐+检测+养护';
				break;
			default:
				$name = '保养订单';
				break;
		}
		return $name;
	}
	
	//绑定车辆
	function bindCar(){
		$car_id = @$_POST['car_id'];
		$order_id = @$_POST['order_id'];
		if (!$car_id || !$order_id) {
			$this->ajaxReturn('参数错误','',0);
		}
		$mAutoSchedule = D('auto_schedule');
	
		//清除老的数据
		$where['order_id'] = array('like','%'.$order_id.'|%');
		$data = $mAutoSchedule->where($where)->find();
		if ($data) {
			$new_order_id = str_replace($order_id.'|', '', $data['order_id']);
			$data['order_id'] = $new_order_id;
			$remove = $mAutoSchedule->where( array('id'=>$data['id']) )->save($data);//去除老数据
		}
		
		//保存新的
		$data_new = $mAutoSchedule->where( array('id'=>$car_id) )->find();
		if ($data_new) {
			$data_new['order_id'] .= $order_id.'|';
			$mAutoSchedule->where( array('id'=>$data_new['id']) )->save($data_new);//添加新数据
		}else{
			//开新的车
			$insert = array(
					'order_id' => $order_id.'|',
					'tech_id' =>  $car_id+14,
					'create_time' => time()
			);
			$mAutoSchedule->add($insert);
		}
		$this->ajaxReturn('重新分配车辆成功','',1);
	
	}
	
	//修改技师
	function changeJs(){
		$car_id = @$_POST['car_id'];
		$tech_id = @$_POST['tech_id'];
		if (!$car_id || !$tech_id) {
			$this->ajaxReturn('参数错误','',0);
		}
		$mAutoSchedule = D('auto_schedule');
		$mAutoSchedule->where( array('id'=>$car_id) )->save( array('tech_id'=>$tech_id) );
		$this->ajaxReturn('修改技师成功','',1);
	}
	
	//提交排班
	function submitAutoAssign(){
		$mAutoSchedule = D('auto_schedule');
		//从数据库里面读出order_id,和技师id，绑定订单表
		$dataFromDbs = $mAutoSchedule->select();
		if ($dataFromDbs) {
			$carArrs = array();
			foreach ($dataFromDbs as $dataFromDb){
				$order_id_arr = explode('|', $dataFromDb['order_id']) ;
				if ( !empty($order_id_arr) ) {
					$carArr = array();
					foreach ($order_id_arr as $order_id){
						if (!$order_id) {
							continue;
						}
						$order_info = $this->reservation_order_model->where( array('id'=>$order_id) )->find();
						if ($order_info) {
							$dataUpdate = array(
									'technician_id' => $dataFromDb['tech_id'],
									'status'=>2,
									'update_time'=>time()
										
							);
							$this->reservation_order_model->where( array('id'=>$order_id) )->save($dataUpdate);
								
							//分配技师完成之后通知用户
							//改成早上7点脚本发了
							$model_user = M('tp_admin.user','xc_');
							$user_info = $model_user->where(array('id'=>$dataFromDb['tech_id']))->find();
							/*if ($order_info['city_id'] == 1) {
							$sms = array(
									'phones'=>$order_info['mobile'],
									'content'=>'亲，您'.date('m',$order_info['order_time']).'月'.date('d',$order_info['order_time']).'日预约的携车网-府上养车，将由五星技师'.$user_info['nickname'].'师傅（'.$user_info['mobile'].'）上门为您服务。期待与您的见面！有疑问询：400-660-2822。',
							);
							$this->curl_sms($sms,'',1,1);
							$sms['sendtime'] = time();
							$this->model_sms->data($sms)->add();
							}*/
							
							
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
										'js'=>$user_info['nickname'],
										'cpxx' => $cpxx,
										'cx'=>$cx
								);
								$this->addCodeLog('进销存自动插入', var_export($post,true).'order_id'.$order_info['id']);
								//var_dump($post);exit;
								$res = $this->curl($url,$post);
								$this->addCodeLog('进销存自动插入', var_export($res,true));
								$this->submitCodeLog('进销存自动插入');
							
						}
					}
					}
					$this->addCodeLog('自动排班', 'order_id:'.$dataFromDb['order_id']);
						
					$mAutoSchedule->query('truncate xc_auto_schedule');
					$this->ajaxReturn('提交成功','',1);
				}else{
					$this->ajaxReturn('提交失败,order_id 为空','',0);
				}
			}
		}else{
			$this->ajaxReturn('提交失败,数据为空','',0);
		}
	}

function auto_order(){
		//查到今天所有订单
		$arr_n = array('普陀区','嘉定区','宝山区','闸北区');
		$arr_d = array('虹口区','杨浦区','黄浦区','卢湾区','静安区');
		$arr_x = array('长宁区','徐汇区','松江区','青浦区');
		$arr_s = array('浦东新区','奉贤区');
		$jishi_zu1 = array(12,11,15,24);
		$jishi_zu2 = array(2,10,18,27);
		$jishi_zu3 = array(132,101,125,4);
		$jishi_zu4 = array(912,181,5,21);
		$map['order_time']=12222223; //还有其他的状态
		$today_order = $this->reservation_order_model->where($map)->select();
		foreach($today_order as $k=>$v){
			$longitude = $v['longitude'];
			$latitude = $v['latitude'];
			$area_qu = $this->get_address($latitude,$longitude);
			if( in_array($area_qu,$arr_n)){
				$zu_1[] =$v['id'];
			}
			if( in_array($area_qu,$arr_d)){
				$zu_2[] =$v['id'];
			}
			if( in_array($area_qu,$arr_x)){
				$zu_3[] =$v['id'];
			}
			if( in_array($area_qu,$arr_s)){
				$zu_4[] =$v['id'];
			}
			//订单已分到区中
			//在分配技师
			//每个区的订单分给所属区的技师
		}
		//bangding绑定好数据放到车辆里面
		$car_arr = array();
		foreach($zu_1 as $k=>$v ){
			$order_info =  $this->reservation_order_model->where('id ='.$v )->find();
			$v ;
		}
	}



	//百度逆向地址接口
	function get_address($latitude,$longitude){
		$host ="http://api.map.baidu.com/geocoder/v2/?ak=6af4266c4310701eb7d7ae0564209287&location=".$latitude.','.$longitude."&output=json&pois=0";
		$array = array(
			"location"=>"39.983424,116.322987",//".$latitude.','.$longitude.", //31.2434217369,121.49440307655
			"output"=>"xml",
			"pois"=>"1",
		);
		$json_body = json_encode($array);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		return $result['result']['addressComponent']['district'];

	}

}
?>