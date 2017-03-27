<?php
	 /*
	**插入新订单的脚本
	*/
	class OrdercheatAction extends  CommonAction {

		function __construct() {
			parent::__construct();
		}

		public function index() {
			set_time_limit(0);
			$order_model = D("Order");
			$ordercheat_model = D("Ordercheat");
			$membernew_model = D("Membernew");
			
			$map['complete_time'] = array('gt',1372239673);
			$list = $ordercheat_model->where($map)->select();
			foreach($list as $key=>$val) {
				
				$complete_time = mktime(rand(8,16),rand(0,59),rand(0,59),6 , rand(21,26) ,date("Y",$val['complete_time']) ) ;
				$data['complete_time'] = $complete_time;
				
					$ordercheat_model->where(array('id'=>$val['id']))->save($data);
				
			}

			/*
			foreach($list as $key=>$val) {
				$timeasc = date('G',$val['order_time']);
				if($timeasc <7 || $timeasc > 17){
					$order_time = mktime(rand(8,16),0,0,date("m",$val['order_time']) , date("d",$val['order_time']) ,date("Y",$val['order_time']) ) ;
					$data['order_time'] = $order_time;
				}

				$c_timeasc = date('G',$val['complete_time']);
				if($c_timeasc <7 || $c_timeasc > 17){
					$complete_time = mktime(rand(8,16),rand(0,59),rand(0,59),date("m",$val['complete_time']) , date("d",$val['complete_time']) ,date("Y",$val['complete_time']) ) ;
					$data['complete_time'] = $complete_time;
				}
				if($order_time || $complete_time) {
					echo $val['id'];
					print_r($data);
					$ordercheat_model->where(array('id'=>$val['id']))->save($data);
				}

			}*/
			exit();
			//2月	1361116800 1362067200
			//3月	1362067200	1364745600
			//4月	1364745600	1367337600
			//5月	1367337600	1370016000
			//6月	1370016000	1371744000

			//3.订单按2月54单，3月85单，4月115单，5月152单，6月178单，平均到天插入
			//2月份20	3月份40	4月份60	5月份80	6月份100

			//上海大众，通用，雪佛来，起亚，斯柯达，本田，丰田也要有，一汽丰田，广汽丰田，东风本田

			if($_REQUEST['month']==2) {//7单 
				$map['create_time'] = array(array('egt',1361116800),array('lt',1362067200));
				$need_count = 20;
			}elseif($_REQUEST['month']==3) {//18单
				$map['create_time'] = array(array('egt',1362067200),array('lt',1364745600));
				$need_count = 40;
			}elseif($_REQUEST['month']==4) {//35
				$map['create_time'] = array(array('egt',1364745600),array('lt',1367337600));
				$need_count = 60;
			}elseif($_REQUEST['month']==5) {//36
				$map['create_time'] = array(array('egt',1367337600),array('lt',1370016000));
				$need_count = 80;
			}elseif($_REQUEST['month']==6) {//30
				$map['create_time'] = array(array('egt',1370016000),array('lt',1371744000));
				$need_count = 100;
			}
			$map['status'] = 1;
			$list = $order_model->where($map)->select();
			$count = $order_model->where($map)->count();

			foreach($list as $key=>$val) {
				for($i=1; $i<=ceil($need_count/$count); $i++) {

					//$data['id'] = $order_id;
					$data['uid'] = rand(600000001, 600006200);
					$member_info = $membernew_model->find($data['uid']);
					$data['truename'] = $member_info['truename'];
					$data['mobile'] = $member_info['mobile'];
					$data['licenseplate'] = $member_info['licenseplate'];

					$data['city_id'] = 0;
					
					$brand_index = rand(0,3);
					$data['brand_id'] = $member_info['brand_id'];
					$data['series_id'] = $member_info['series_id'];
					$data['model_id'] = $member_info['model_id'];
					$data['shop_id'] = $member_info['shop_id'];
					
					$data['timesaleversion_id'] = 0;
					$data['service_ids'] = 10;
					$data['total_credit'] = 0;
					$data['mark'] = 0;
					
					$data['total_price'] = rand(400,2000);
					
					$data['jiesuan_money'] = 0.00;
					$data['cost_price'] = 0.00;
					$data['mileage'] = rand(1000,40000);
					
					$data['order_time'] = $val['order_time']+($i*3600*rand(1,3));

					$data['order_state'] = 2;
					$data['complain_state'] = 0;
					$data['status'] = 0;
					$data['create_time'] = $val['create_time']+($i*rand(600,1800));
					$data['update_time'] = $val['update_time']+($i*rand(600,1800));
					$data['complete_time'] = $data['order_time']+($i*rand(86400,109600));
					$data['iscomment'] = 0;

					$data['is_cheat'] = 1;
					$ordercheat_model->add($data);

				}
			}
		echo "ok";
		}
	}
?>
