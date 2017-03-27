<?php
/*
 * 活动统计
 */
class ActivitycountAction extends CommonAction {
	protected $mMyoil;
	protected $mSpreadoil;
	protected $mSpreadpic;
	protected $mSpreadprize;
	protected $mSpreadsign;
    function __construct() {
		parent::__construct();
		$this->mMyoil = D('spreadmyoil');//我的油桶表
		$this->mSpreadpic = D('spreadpic');//推广二维码图片生成表
		$this->mSpreadsign = D('spreadsign');//签到表
		$this->mSpreadoil = D('spreadoil');//别人帮我加油表
		$this->mSpreadprize = D('spreadprize');//领奖表
	}

	function index(){
		$type = 1;
		if (!empty($_POST['type']) && $_POST['type'] == 2) {
			//查询每一天的数据数据
			$type = 2;
			//最早时间和最晚时间
			if( $_REQUEST['start_time'] ) {
				$firstTime = strtotime($_REQUEST['start_time']);
			}else{
				$fistTimeData = $this->mSpreadoil->field('create_time')->order('id asc')->find();
				$fistTimeDate = date('Y-m-d', $fistTimeData['create_time']);
				$firstTime = strtotime($fistTimeDate);
			}
			if( $_REQUEST['end_time'] ) {
				$lastTime = strtotime($_REQUEST['end_time'].' 23:59:59');
			}else{
				$lastTimeData = $this->mSpreadoil->field('create_time')->order('id desc')->find();
				$lastTimeDate = date('Y-m-d', $lastTimeData['create_time']);
				$lastTime = strtotime($lastTimeDate.' 23:59:59');
			}
// 			var_dump($firstTime);
// 			var_dump($lastTime);
			$j = 86400;
			for( $i=$firstTime; $i<=$lastTime; $i+=$j ){
				$end = $i + $j;
				$where['create_time'] =  array(array('egt',$i),array('lt',$end));	//当天的数据
				$today = date('Y-m-d',$i);
// 				var_dump($today);
				//参与人数
				$count1 = $this->mSpreadoil->Distinct(true)->field('r_weixin_id')->where($where)->count();
				//生成机油桶的人数
				$count2 = $this->mMyoil->where($where)->count();
				//帮别人加油的人数
				$count3 = $this->mSpreadoil->Distinct(true)->field('r_weixin_id')->where($where)->count();
				//各个兑奖情况
				$prizeCount = array();
				for( $t=1; $t<=7; $t++){
					$where['type'] = $t;
					if($t==1){
						$onename = '10元打车代金券';
					}else if($t==2){
						$onename = '上门汽车安全检测';
					}else if($t==3){
						$onename = '上门汽车保养专业服务';
					}else if($t==4){
						$onename = '府上养车-壳牌黄喜力机油套餐';
					}else if($t==5){
						$onename = '府上养车-壳牌蓝喜力机油套餐';
					}else if($t==6){
						$onename = '府上养车-壳牌灰喜力机油套餐';
					}else if($t==7){
						$onename = '1000元油卡';
					}
					$pCount = $this->mSpreadprize->where($where)->count();
					$prizeCount[] = array(
							'name' =>$onename,
							'count' =>$pCount
					);
				}
				unset($where['type']);
				//各层次的机油统计
				$where['oil_num'] = array(array('egt',100),array('lt',1000));
				$count4 = $this->mMyoil->where($where)->count();
				
				$where['oil_num'] = array(array('egt',1000),array('lt',2500));
				$count5 = $this->mMyoil->where($where)->count();
				
				$where['oil_num'] = array(array('egt',2500),array('lt',4000));
				$count6 = $this->mMyoil->where($where)->count();
				
				$where['oil_num'] = array(array('egt',4000),array('lt',6000));
				$count7 = $this->mMyoil->where($where)->count();
				
				$where['oil_num'] = array(array('egt',6000),array('lt',10000));
				$count8 = $this->mMyoil->where($where)->count();
				
				$where['oil_num'] = array(array('egt',10000));
				$count9 = $this->mMyoil->where($where)->count();
				
				$list[$today] = array(
						'count1' => $count1,
						'count2' => $count2,
						'count3' => $count3,
						'prizeCount' => $prizeCount,
						'oilCount' => array(
							'100到1000' => $count4,
							'1000到2500' => $count5,
							'2500到4000' => $count6,
							'4000到6000' => $count7,
							'6000到10000' => $count8,
							'大于10000' => $count9,
						)
				);
				
				unset($where['oil_num']);
			}
			
			
		}else{
			//查询所有数据
			
			//参与人数
			$count1 = $this->mSpreadoil->Distinct(true)->field('r_weixin_id')->count();
			//生成机油桶的人数
			$count2 = $this->mMyoil->count();
			//帮别人加油的人数
			$count3 = $this->mSpreadoil->Distinct(true)->field('r_weixin_id')->count();
			//各个兑奖情况
			$prizeCount = array();
			for( $t=1; $t<=7; $t++){
					$where['type'] = $t;
					if($t==1){
						$onename = '10元打车代金券';
					}else if($t==2){
						$onename = '上门汽车安全检测';
					}else if($t==3){
						$onename = '上门汽车保养专业服务';
					}else if($t==4){
						$onename = '府上养车-壳牌黄喜力机油套餐';
					}else if($t==5){
						$onename = '府上养车-壳牌蓝喜力机油套餐';
					}else if($t==6){
						$onename = '府上养车-壳牌灰喜力机油套餐';
					}else if($t==7){
						$onename = '1000元油卡';
					}
					$pCount = $this->mSpreadprize->where($where)->count();
					$prizeCount[] = array(
							'name' =>$onename,
							'count' =>$pCount
					);
				}
			//各层次的机油统计
			$where = array( 'oil_num' =>  array(array('egt',100),array('lt',1000)) );
			$count4 = $this->mMyoil->where($where)->count();
			
			$where = array( 'oil_num' =>  array(array('egt',1000),array('lt',2500)) );
			$count5 = $this->mMyoil->where($where)->count();
			
			$where = array( 'oil_num' =>  array(array('egt',2500),array('lt',4000)) );
			$count6 = $this->mMyoil->where($where)->count();
			
			$where = array( 'oil_num' =>  array(array('egt',4000),array('lt',6000)) );
			$count7 = $this->mMyoil->where($where)->count();
			
			$where = array( 'oil_num' =>  array(array('egt',6000),array('lt',10000)) );
			$count8 = $this->mMyoil->where($where)->count();
			
			$where = array( 'oil_num' =>  array(array('egt',10000)));
			$count9 = $this->mMyoil->where($where)->count();
			
			$list = array(
					'count1' => $count1,
					'count2' => $count2,
					'count3' => $count3,
					'prizeCount' => $prizeCount,
					'oilCount' => array(
						'100到1000' => $count4,
						'1000到2500' => $count5,
						'2500到4000' => $count6,
						'4000到6000' => $count7,
						'6000到10000' => $count8,
						'大于10000' => $count9,
							
					)
			);
		}
		
        //var_dump($list);
		$this->assign('type',$type);
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
        $this->assign("list",$list);
		$this->display();
	}

}
