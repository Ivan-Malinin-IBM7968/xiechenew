<?php

class CslookassignAction extends CommonAction {	
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
                 //查询订单总数
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
		
		$this->assign('countDetail', $countDetail);
		//print_r($data);
		foreach($data as $kk=>$vv){
			$i = 0;
			$r_array = array();
			foreach($vv as $a=>$b){
				if(!in_array($b['mobile'],$r_array)){
					$r_array[] = $b['mobile'];
					$i++;
				}
			}
			$data[$kk]['time_count'] = $i;
		}
		$this->assign('time_count',$time_count);
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
	
	
}
?>