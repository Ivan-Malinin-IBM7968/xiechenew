<?php
/*
 */
class BidcouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
		$this->BidorderModel = D('bidorder');//保险类订单表
		$this->ShopModel = D('shop');//店铺表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->BidCouponModel = D('bidcoupon');//保险类订单返利券表
	
	}

	/*
		@author:chf
		@function:显示保险类现金卷页
		@time:2013-05-23
	*/
	public function index(){
		$array = array('1','2','3','4','5');
		$this->assign('array',$array);
		$map['shop_id'] = $_SESSION['shop_id'];
		if($_REQUEST['licenseplate'] != '000000' && $_REQUEST['licenseplate']){
			$data['licenseplate_title'] = $_REQUEST['licenseplate_title'];
			$data['licenseplate'] = $_REQUEST['licenseplate'];
			$map['licenseplate'] = $_REQUEST['licenseplate_title'].$_REQUEST['licenseplate'];
		
			if($_REQUEST['arraycode']){
				$map['code'] = array('in',$_REQUEST['arraycode']);
				$this->assign('sqlcode',$_REQUEST['arraycode']);
			}
			$BidCoupon = $this->BidCouponModel->where($map)->select();
			
			if($BidCoupon){
				foreach($BidCoupon as $k=>$v){
					$Shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
					$data['BidOrder'][$k]['bidcoupon_name'] = $Shop['shop_name'].$v['price']."返利券";
					$data['BidOrder'][$k]['bidcoupon_endtime'] = $v['end_time'];
					$data['BidOrder'][$k]['price'] = $v['price'];
					$data['BidOrder'][$k]['order_id'] =  $this->get_orderid($v['id']);
					$data['BidOrder'][$k]['bidcoupon_id'] = $v['id'];
					$data['BidOrder'][$k]['bidstatus'] = $v['status'];
				}
			}else {
				$BidCoupon = $this->BidCouponModel->where(array('code'=>$_REQUEST['code'],'shop_id'=>$_SESSION['shop_id']))->find();
				if($BidCoupon) {
					$data['type'] = 3;//验证码存在 车牌号不存在
				}
			}
			
			if($_REQUEST['code'] && !$BidCoupon){
				$data['type'] = 2;
			}
			$this->assign('data',$data);
		}
		$this->display();
	}

	/*
		@author:chf
		@function:显示返给用户优惠券的记录
		@time:2013-05-23
	*/
	function show(){
		$new['shop_id'] = $arr['shop_id'] = $map['shop_id'] = $_SESSION['shop_id'];
		if($_REQUEST['licenseplate']){
			$data['licenseplate_title'] = $_REQUEST['licenseplate_title'];
			$data['licenseplate'] = $_REQUEST['licenseplate'];
			$map['licenseplate'] = $_REQUEST['licenseplate_title'].$_REQUEST['licenseplate'];
		}
		
		if($_REQUEST['mobile']){
			$data['mobile'] = $map['mobile'] = $_REQUEST['mobile'];

		}
		if($_REQUEST['licenseplate']){
			$data['licenseplate_title'] = $_REQUEST['licenseplate_title'];
			$data['licenseplate'] = $_REQUEST['licenseplate'];
			//$map['licenseplate'] = $_REQUEST['licenseplate_title'].$_REQUEST['licenseplate'];
		}
		if($_REQUEST['type']=='1'){
			$data['type'] = $map['status'] = $_REQUEST['type'];
			//昨日返券总数
			//$map['create_time'] = array( array('gt',$sqlstart_time),array('lt',$sqlend_time,"AND"));
			//$data['status'] = $map['status'] = '1'; 
			
		}elseif($_REQUEST['type']=='2'){
			$data['type'] = $_REQUEST['type'];
			//$map['create_time'] = array( array('gt',$sqlstart_time),array('lt',$sqlend_time,"AND"));
			$data['status'] = $map['status'] = '0'; 
		}
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['create_time'] = array( array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time']),"AND"));
		}
		$count = $this->BidCouponModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,10);
		// 分页显示输出
		$page = $p->show_admin();
		$data['BidOrder'] = $this->BidCouponModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->BidCouponModel->getlastSql();
		if($data['BidOrder']){
			foreach($data['BidOrder'] as $k=>$v){
				$data['BidOrder'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				//$data['BidOrder'][$k]['ture_orderid'] = $v['bidorder_id'];									
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['BidOrder'][$k]['ture_orderid'] = $Bidorder['insurance_id'];
				$data['BidOrder'][$k]['licenseplate'] = $v['licenseplate'];
			}
		}
		$data['BidOrder_Count'] = count($data['BidOrder']);
		$time = date('Y-m-d',time()-86400);

		//共给用户返券总数和金额
		$data['getcoupon'] = $this->BidCouponModel->where($arr)->count();
		$data['getprice'] = $this->BidCouponModel->where($arr)->sum('price');
		
		//已使用返利券总数和金额
		$new['status'] = 0;
		$data['allcoupon'] = $this->BidCouponModel->where($new)->count();
		$data['allprice'] = $this->BidCouponModel->where($new)->sum('price');
		$this->assign('data',$data);
		$this->display();
	}


	/*
		@author:chf
		@function:显示返利券记录
		@time:2013-12-23
	*/
	function rebate(){
		$new['shop_id'] = $arr['shop_id'] = $map['shop_id'] = $_SESSION['shop_id'];
		$map['status'] = '0'; 
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['create_time'] = array( array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time']),"AND"));
		}
		$count = $this->BidCouponModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,10);
		// 分页显示输出
		$page = $p->show_admin();
		$data['BidOrder'] = $this->BidCouponModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->BidCouponModel->getlastSql();
		if($data['BidOrder']){
			foreach($data['BidOrder'] as $k=>$v){
				$data['BidOrder'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['BidOrder'][$k]['ture_orderid'] = $v['bidorder_id'];									
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['BidOrder'][$k]['truename'] = $Bidorder['truename'];
				$data['BidOrder'][$k]['licenseplate'] = $v['licenseplate'];
			}
		}
		$data['BidOrder_Count'] = count($data['BidOrder']);
		$time = date('Y-m-d',time()-86400);

		//共给用户返券总数和金额
		$data['getcoupon'] = $this->BidCouponModel->where($arr)->count();
		$data['getprice'] = $this->BidCouponModel->where($arr)->sum('price');
		
		//已使用返利券总数和金额
		$new['status'] = 0;
		$data['allcoupon'] = $this->BidCouponModel->where($new)->count();
		$data['allprice'] = $this->BidCouponModel->where($new)->sum('price');
		$this->assign('data',$data);
		$this->display();
	}

	
	/*
		@author:chf
		@function:AJAX更改保险类返利卷状态
		@time:2013-05-23
	*/
	function AjaxUpdate(){
		$map['id'] = $_REQUEST['id'];
		$map['create_time'] = time();
		$this->BidCouponModel->where($map)->data(array('status'=>0))->save();
		echo "1";
	
	}

	/*
		@author:chf
		@function:把事故车返利券状态改成完成
		@time:2014-03-27
	*/
	function BidFinish(){
		if($_POST['bidcoupon_id']){
			foreach($_POST['bidcoupon_id'] as $v ){
				$map['id'] = array('in',$_POST['bidcoupon_id']);
				$this->BidCouponModel->where($map)->data(array('status'=>'0','use_time'=>time()))->save();
			}
			
		}
			
		$this->success('抵用成功！',U('/Shop/Bidcoupon/show/type/2'));
	}
	

    
}