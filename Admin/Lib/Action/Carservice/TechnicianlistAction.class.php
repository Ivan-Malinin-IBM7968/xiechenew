<?php
//技师分配情况
class TechnicianlistAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->UserroleModel = M('tp_admin.role_user','xc_');//网站用户关系表
		$this->UserModel = M('tp_admin.user','xc_');//网站用户表
		$this->TechnicianModel = M('tp_xieche.technician','xc_');//技师信息拓展表
		$this->CarbrandModel = M('tp_xieche.carbrand','xc_');//技师信息拓展表
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');//订单表
		$this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表
		$this->model_sms = M('tp_xieche.sms','xc_');//手机短信
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
	
	
	//技师排班列表
	public function index() {
	
		$today = strtotime(date('Y-m-d',time()));
	
		if(!empty($_POST['order_time'])){
			$today = strtotime($_POST['order_time']);
			$this->assign('order_time',$_POST['order_time']);
		}else{
			$this->assign('order_time',date('Y-m-d',time()));
		}
	
		$tomorrow = $today + 86400;
		$where['order_time'] = $map['order_time'] = array(array('gt',$today),array('lt',$tomorrow));
	
		//查询当天技师情况
		$technician = $this->TechnicianModel->field('id,truename')->select();
		$list = array();
		$order_time_arr = array('08','09','10','11','12','13','14','15','16','17','18','19','20','21');//组织数据
		$count = $count1 = $count2 = $count3 = $count6 = $count7 = 0;
		$count8 = $count9 = $count10 = $count11 = $count12 = $count13 = 0;
		$count14 = $count15 = $count16 = $count17 = $count18 = $count19 = 0;
                //查询订单总数
                $count = $this->reservation_order_model->where($map)->count();
                
		$defaultCoo = '121.618094,31.036347';//技师在基地的坐标
		$lastCoo = '';//上一单的距离
		$lastTime = '';//上一单的时间
	
		if ($technician) {
			foreach ($technician as $key => $val){
				$technician_id = $val['id'];
				$where['technician_id'] = $technician_id;
				$where['status'] = array('neq',8);
				$technician_name = $val['truename'];
				$data = $this->reservation_order_model->where($where)->order('order_time asc')->select();
				//var_dump($this->reservation_order_model->getLastSql());
				if($data){
					$countDetail[$technician_name] = 0;
					foreach ($data as $k => $res){
						$res['id'] = $this->get_orderid($res['id']);
						$orderCoo = $res['longitude'].','.$res['latitude'];//订单的坐标
	
						if ($k == 0) {
							$dis = $this->getDistance($defaultCoo, $orderCoo);
							$coo = '距离基地'.$dis.'千米';
						}else{
							$dis = $this->getDistance($lastCoo, $orderCoo);
							$res['near_time'] = ($res['order_time'] - $lastTime) / 3600;
							$coo = '距离上一单'.$dis.'千米';
						}
						
						$lastCoo = $orderCoo;
						$lastTime = $res['order_time'];
						
						$res['coo'] = $coo;
	
						switch ($res['order_type']) {
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
						$res['order_name'] = $name;
						$val['order'] = $res;
						$order_time = date('H',$res['order_time']);
						$list[$technician_name][$order_time][] = $val;
 						$countDetail[$technician_name]++;
					}
					//$countDetail[$technician_name] = count($list[$technician_name]);
					$key = array_keys($list[$technician_name]);//判断时间段有没有数据
					$diff = array_diff($order_time_arr,$key);
					if ( $diff ) {
						foreach ($diff as $diff_time){
							$list[$technician_name][$diff_time] = array();
						}
					}
					ksort($list[$technician_name]);
				}
			}
			$count_js = count($list);
		}
	
		$this->assign('countDetail', $countDetail);
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
		$this->assign('list',$list);
		$this->display();
	}

}
?>