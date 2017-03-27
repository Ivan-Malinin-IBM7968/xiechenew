<?php
/*class 礼品类*/
class GiftAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->GiftModel = D('gift');//礼品表
		$this->MemberModel = D('member');//用户信息表
		$this->RegionModel = D('region');//地区表

		$this->PointModel = D('point');//积分表
		$this->PointAdress = D('pointadress');//积分表
	}

	/*
		@author:chf
		@function:前台显示积分换礼页
		@time:2013-04-18
	*/
	
    public function index(){
		$member = $this->MemberModel->where(array('uid'=>$_SESSION['uid']))->find();
		$this->assign('member',$member);
        $this->display();
    }
	
	/*
		@author:chf
		@function:AJAX检查积分是否够用
		@time:2013-04-18
	*/
	function AjaxCheckpoint(){
		$data['id'] = $_REQUEST['id'];
		$data['status'] = 0;
		$Gift = $this->GiftModel->where($data)->find();
		$map_p['uid'] = $uid;//取得UID
		
		$Memberinfo = $this->MemberModel->where($map_p)->find();
		if($Memberinfo['point_number'] >= $Gift['point']){
			echo 1;
		}else if($Memberinfo['point_number'] < $Gift['point']){
			echo -1;
		}
	
	}


	/*
		author:chf
		function:购买优惠卷页面
		time:2013-04-12
	*/
	function giftbuy(){
		if( true !== $this->login()){
			exit;
		}
		$GiftMap['id'] = $_REQUEST['id'];
		$uid = $this->GetUserId();
		$model_member = D('Member');
		$map_m['uid'] = $uid;
		$member = $this->MemberModel->where($map_m)->find();
		$this->get_tuijian_coupon();

		$Arr['Gift'] = $this->GiftModel->where($GiftMap)->find();

		$Arr['prov'] = $this->RegionModel->where(array('region_type'=>1,'status'=>1))->select();
		$this->assign('Arr',$Arr);
		$this->display();
	}

	/*
		author:chf
		function:兑换礼品操作
		time:2013-04-12
	*/
	function SaveGift(){
		$GiftMap['id'] = $_REQUEST['Gift_id'];
		$uid = $this->GetUserId();
		$model_member = D('Member');
		$number = $_REQUEST['number'];
		$map_m['uid'] = $uid;
		
		$Memberinfo = $this->MemberModel->where($map_m)->find();
		$Gift = $this->GiftModel->where($GiftMap)->find();
		$number_Gift = $Gift['point']*$number; 
		if($Memberinfo['point_number'] >= $number_Gift){
			
			$point_number = $Memberinfo['point_number'] - $number_Gift;
			$this->MemberModel->where($map_m)->save(array('point_number'=>$point_number));
			$PointMap['point_number'] = "-".$number_Gift;
			$PointMap['uid'] = $uid;
			$PointMap['create_time'] = time();
			$PointMap['point_memo'] = "兑换奖品：".$Gift['title'];
			
			$this->PointModel->add($PointMap);
			$PointAdressMap['gift_id'] = $_REQUEST['Gift_id'];
			$PointAdressMap['prov_id'] = $_REQUEST['prov'];
			$PointAdressMap['city_id'] = $_REQUEST['city'];
			$PointAdressMap['area_id'] = $_REQUEST['area'];
			$PointAdressMap['adress'] = $_REQUEST['adress'];
			$PointAdressMap['moblie'] = $_REQUEST['moblie'];
			$PointAdressMap['truename'] = $_REQUEST['truename'];
			$PointAdressMap['uid'] = $uid;
			$PointAdressMap['create_time'] = time();

			$this->PointAdress->add($PointAdressMap);
			
			$this->success("兑换完成稍后客服将与您联系",__APP__.'/myhome/mypoint');
	
	
		}else if($Memberinfo['point_number'] < $number_Gift){
			$this->success("积分不足兑换失败",__APP__.'/myhome/mypoint');
		}
	
	}
	/*
		author:chf
		function:AJAX得到城市三连动
		time:2013-04-12
	*/
	function AjaxGetCity(){
		$city = $this->RegionModel->where(array('parent_id'=>$_REQUEST['prov'],'status'=>1))->select();
		echo json_encode($city);
	}

}
?>