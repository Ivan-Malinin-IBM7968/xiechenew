<?php
//订单
class BidjiesuanAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//商铺表
		$this->BidorderModel = D('bidorder');//保险订单表
		$this->BidorderjiesuanModel = D('bidorderjiesuan');//保险订单表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//竞价表
		$this->Bidjiesuan_logModel = D('bidorderjiesuan_log');//保险订单结算日志表
	}
	
	/*
		@author:chf
		@function:显示保险订单结算页
		@time:2013-7-18
	*/
	function index(){
		//print_r($_REQUEST);
		if(!$_REQUEST['type']){
			$data=$this->shopask();
			//$data['th_name']=array('订单号','车主名','车牌号','手机号码','4S店','保险公司','保险金额','获取佣金比例','4S店佣金','完成时间');
		}
		if($_REQUEST['type']=='adminask'){
			$data=$this->adminask();
		}
		if($_REQUEST['type']=='openbidorder'){
			$data=$this->openbidorder();
		}
		if($_REQUEST['type']=='collectbidorder'){
			$data=$this->collectbidorder();
		}
		if($_REQUEST['type']=='donebidorder'){
			$data=$this->donebidorder();
		}

		if($_REQUEST['type']=='viewdetail'){
			$data=$this->viewdetail();
		}
		/*if($_REQUEST['shopname']){
			$data['shopname'] = $_REQUEST['shopname'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		}
		$count = $this->BidorderModel->group('shop_id')->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,25);
		// 分页显示输出

		foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }

		$page = $p->show_admin();
		$data['Bidorder'] = $this->BidorderModel->where($map)->group('shop_id')->limit($p->firstRow.','.$p->listRows)->select();

		
		foreach($data['Bidorder'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'];
			
			$data['Bidorder'][$k]['count'] = $this->BidorderModel->where(array('is_jiesuan'=>0,'order_status'=>4,'pay_status'=>1,'shop_id'=>$v['shop_id']))->count();
			$data['Bidorder'][$k]['isjiesuan_count'] = $this->BidorderjiesuanModel->where(array('jiesuan_status'=>2,'shop_id'=>$v['shop_id']))->count();
			
			$lastcoupon = $this->BidorderModel->where(array('is_jiesuan'=>1,'order_status'=>4,'pay_status'=>1,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();
			


			$data['Bidorder'][$k]['open_count'] = $this->BidorderjiesuanModel->where(array('jiesuan_status'=>3,'open'=>0,'shop_id'=>$v['shop_id']))->count();


			$data['Bidorder'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];
		}
		$data['shop_list'] = $this->ShopModel->select();
		


		$this->assign('page',$page);

		*/

		//print_r($data);
		$this->assign('data',$data);
		$this->assign('th_name',$th_name);
		$this->assign('type',$_REQUEST['type']);
		$this->display();
	}

	/*
		@author:chf
		@function:显示保险订单申请结算页
		@time:2013-7-18
	*/
	function shopask(){
		$map['xc_bidorder.order_status'] = 4;
		$map['xc_bidorder.is_jiesuan'] = 0;
		$map['xc_bidorder.status'] = 1;
		//$map['xc_bidorder.pay_status'] = 1;
		$map['xc_shop.safestate'] = 1;
		$map['xc_bidorder.truename'] = array('notlike','%测%');
		if($_REQUEST['shop_id']){
			$map['shop_id'] = $_REQUEST['shop_id'];
		}
		if($_REQUEST['pay_start_time'] && $_REQUEST['pay_end_time']){
			$map['xc_bidorder.complete_time'] = array(array('lt',strtotime($_REQUEST['pay_end_time'].' 23:59:59')),array('gt',strtotime($_REQUEST['pay_start_time'])),'AND');
			$data['pay_start_time'] = $_REQUEST['pay_start_time'];
			$data['pay_end_time'] = $_REQUEST['pay_end_time'];
		}

		$count = $this->BidorderModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 200);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorder'] = $this->BidorderModel->join('xc_shop ON xc_bidorder.shop_id = xc_shop.id')->order('xc_bidorder.id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->field('xc_bidorder.*')->select();
		//echo $this->BidorderModel->getLastsql();
		$count = 0;
		if($data['Bidorder']){
			foreach($data['Bidorder'] as $k=>$v){
				$Shopbidding = $this->ShopbiddingModel->where(array('id'=>$v['bid_id']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Shopbidding['shop_id']))->find();
				$data['Bidorder'][$k]['order_id'] = $this->get_orderid($v['id']);
				$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
				$data['Bidorder'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorder'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorder'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorder'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$data['allpay']+=$Insurance['loss_price'];//结算金额
				$data['commission']+=$data['Bidorder'][$k]['insurance_commission']+$data['Bidorder'][$k]['company_commission'];//返利佣金总和
				$count = $count+1;
			}
			$data['amount'] = $data['allpay']-$data['commission'];//应付款
		}
		//print_r($data);
		$this->assign('count',$count);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;

	}

	/*
		@author:chf
		@function:显示确认保险订单申请结算页
		@time:2013-7-19
	*/
	function confirmshopask(){
		$bidorder_id = $_REQUEST['bidorder_id'];//保险订单IDS
		
		if($_REQUEST['start_createtime'] && $_REQUEST['end_createtime']){
			$map['create_time'] = array(array('lt',strtotime($_REQUEST['end_createtime'])),array('gt',strtotime($_REQUEST['start_createtime'])),'AND');
			$data['start_createtime'] = $_REQUEST['start_createtime'];
			$data['end_createtime'] = $_REQUEST['end_createtime'];
		}
		foreach($bidorder_id as $k=>$v){
			$bidorder_ids.=$v.",";
		}
		$bidorder_ids = substr($bidorder_ids,0,-1);
		
		$map['id'] = array('in',$bidorder_ids);
	    $count = $this->BidorderModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorder'] = $this->BidorderModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($data['Bidorder']){
			foreach($data['Bidorder'] as $k=>$v){
				$Shopbidding = $this->ShopbiddingModel->where(array('id'=>$v['bid_id']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Shopbidding['shop_id']))->find();
				$data['Bidorder'][$k]['order_id'] = $this->get_orderid($v['id']);
				$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
				$data['Bidorder'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorder'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorder'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorder'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$allpay+=$Insurance['loss_price'];//结算金额
				$commission+=$data['Bidorder'][$k]['insurance_commission']+$data['Bidorder'][$k]['company_commission'];//返利佣金总和

			}
			$amount = $allpay-$commission;//应付款
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$allpay);
		$this->assign('commission',$commission);
		$this->assign('amount',$amount);
		$this->assign('bidorder_id',$bidorder_id);
		$this->display();
	
	}

	/*
		@author:chf
		@function:向商家申请结算信息
		@time:2013-7-19
	*/
	function save_shopask(){
		$bidorder_id = $_REQUEST['bidorder_id'];//保险订单IDS
		foreach($bidorder_id as $k=>$v){
			$Bidorder = $this->BidorderModel->where(array('id'=>$v))->find();
			$Insurance = $this->InsuranceModel->where(array('id'=>$Bidorder['insurance_id']))->find();

			$data['bidorder_id'] = $v;//保险类订单ID
			
			$data['insurance_id'] = $Bidorder['insurance_id'];//竞标ID
			$data['bid_id'] = $Bidorder['bid_id'];//店铺竞标ID
			$data['shop_id'] = $Bidorder['shop_id'];//店铺ID
			$data['insurance_name'] = $Insurance['insurance_name'];//保险公司名称
			$data['insurance_rebate'] = $Bidorder['insurance_rebate'];//4S店返点比例
			$data['company_rebate'] = $Bidorder['company_rebate'];//保险公司返点比例
			$data['insurance_commission'] = $Insurance['loss_price']*($Bidorder['insurance_rebate']/100);//4S店返利佣金
			$data['company_commission'] = $Insurance['loss_price']*($Bidorder['company_rebate']/100);//保险公司返点佣金
			$data['loss_price'] = $Insurance['loss_price'];//保险金额
			$data['jiesuan_status'] = 1;
			$data['addtime'] = time();

			$this->BidorderModel->where(array('id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			
			$jiesuan_id = $this->BidorderjiesuanModel->add($data);

			$this->Bidjiesuan_logModel->add(array('bidorderjiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'申请商家结算将结算订单状态改为申请结算','addtime'=>time()));
			
				
		}
		$this->success('申请结算成功！',U('/Balance/Bidjiesuan/index/type/adminask'));	
	
	}

	/*
		@author:chf
		@function:显示网站结算页
		@time:2013-7-19
	*/
	function adminask(){
		$map['jiesuan_status'] = 1;
		if($_REQUEST['shop_id']){
			$map['shop_id'] = $_REQUEST['shop_id'];
		}
		if($_REQUEST['pay_start_time'] && $_REQUEST['pay_end_time']){
			$map['complete_time'] = array(array('lt',strtotime($_REQUEST['pay_end_time'])),array('gt',strtotime($_REQUEST['pay_start_time'])),'AND');
			$data['pay_start_time'] = $_REQUEST['pay_start_time'];
			$data['pay_end_time'] = $_REQUEST['pay_end_time'];
		}
		/*
		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
			$data['start_addtime'] = $_REQUEST['start_addtime'];
			$data['end_addtime'] = $_REQUEST['end_addtime'];
		}*/

		$count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->BidorderjiesuanModel->getLastsql();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				$data['Bidorderjiesuan'][$k]['licenseplate'] = $Bidorder['licenseplate'];
				$data['Bidorderjiesuan'][$k]['mobile'] = $Bidorder['mobile'];
				$data['Bidorderjiesuan'][$k]['complete_time'] = date('Y-m-d H:i:s',$Bidorder['complete_time']);
				$data['Bidorderjiesuan'][$k]['jiesuan_status'] = $this->get_status($v['jiesuan_status']);
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$data['allpay']+=$Insurance['loss_price'];//结算金额
				$data['commission']+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$data['amount'] = $data['allpay']-$data['commission'];//应付款
		}
		$this->assign('type',$_REQUEST['type']);
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;
	}



	/*
		@author:chf
		@function:显示网站确认结算页
		@time:2013-7-19
	*/
	function confirmadminask(){
		$id = $_REQUEST['id'];//保险订单IDS
		
		foreach($id as $k=>$v){
			$ids.=$v.",";
		}
		$ids = substr($ids,0,-1);
		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
			$data['start_addtime'] = $_REQUEST['start_addtime'];
			$data['end_addtime'] = $_REQUEST['end_addtime'];
		}

		$map['id'] = array('in',$ids);
	    $count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$allpay+=$Insurance['loss_price'];//结算金额
				$commission+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$amount = $allpay-$commission;//应付款
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$allpay);
		$this->assign('commission',$commission);
		$this->assign('amount',$amount);
		$this->display();
	}

	
	/*
		@author:chf
		@function:结算状态改为已结算
		@time:2013-7-19
	
	*/
	function save_admin(){
		$id = $_REQUEST['id'];//保险订单IDS
		
		foreach($id as $k=>$v){
			$Bidorderjiesuan = $this->BidorderjiesuanModel->where(array('id'=>$v))->find();
			$this->BidorderjiesuanModel->where(array('id'=>$v))->data(array('jiesuan_status'=>3))->save();
			
			$this->Bidjiesuan_logModel->add(array('bidorder_id'=>$Bidorderjiesuan['bidorder_id'],'name'=>$_SESSION['loginAdminUserName'],'log'=>'将结算订单状态改为已结算','addtime'=>time()));
			
				
		}
		$this->success('结算成功！',U('/Balance/Bidjiesuan/index/type/openbidorder'));	
	
	
	
	}
	

	/*
		@author:chf
		@function:保险订单详情
		@time:2013-7-19
	*/
	function viewdetail(){
		$map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		$map['shop_id'] = $_REQUEST['shop_id'];
// 		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
// 			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
// 			$data['start_addtime'] = $_REQUEST['start_addtime'];
// 			$data['end_addtime'] = $_REQUEST['end_addtime'];
// 		}
		if($_REQUEST['pay_start_time'] && $_REQUEST['pay_end_time']){
			$map['complete_time'] = array(array('lt',strtotime($_REQUEST['pay_end_time'])),array('gt',strtotime($_REQUEST['pay_start_time'])),'AND');
			$data['pay_start_time'] = $_REQUEST['pay_start_time'];
			$data['pay_end_time'] = $_REQUEST['pay_end_time'];
		}
		
		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->BidorderjiesuanModel->getLastsql();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$data['allpay']+=$Insurance['loss_price'];//结算金额
				$data['commission']+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$data['amount'] = $data['allpay']-$data['commission'];//应付款
		}
	
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;
	}

	/*
		@author:chf
		@function:显示保险订单可开票信息页
		@time:2013-7-19
	*/
	function openbidorder(){
		$map['jiesuan_status'] = 2;
		if($_REQUEST['shop_id']){
			$map['shop_id'] = $_REQUEST['shop_id'];
		}
// 		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
// 			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
// 			$data['start_addtime'] = $_REQUEST['start_addtime'];
// 			$data['end_addtime'] = $_REQUEST['end_addtime'];
// 		}
		if($_REQUEST['pay_start_time'] && $_REQUEST['pay_end_time']){
			$map['complete_time'] = array(array('lt',strtotime($_REQUEST['pay_end_time'])),array('gt',strtotime($_REQUEST['pay_start_time'])),'AND');
			$data['pay_start_time'] = $_REQUEST['pay_start_time'];
			$data['pay_end_time'] = $_REQUEST['pay_end_time'];
		}
		
		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			//$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		$limit='limit '.$p->firstRow.','.$p->listRows;
		
		//print_r($map);
		$sql=" SELECT shop_id,COUNT(shop_id) as count,sum(insurance_commission) as insurance_commission,sum(company_commission) AS company_commission FROM `xc_bidorderjiesuan` WHERE ( `jiesuan_status` = 2 ) GROUP BY shop_id {$limit} ";
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->query($sql);
		//echo $this->BidorderjiesuanModel->getLastsql();

		
		foreach($data['Bidorderjiesuan'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
			
			$lastcoupon = $this->BidorderModel->where(array('is_jiesuan'=>1,'order_status'=>4,'pay_status'=>1,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Bidorderjiesuan'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];

			$data['Bidorderjiesuan'][$k]['commission'] = $v['insurance_commission']+$v['company_commission'];
 
		}

		/*$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		echo $this->BidorderjiesuanModel->getLastsql();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$data['allpay']+=$Insurance['loss_price'];//结算金额
				$data['commission']+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$data['amount'] = $data['allpay']-$data['commission'];//应付款
		}
		*/
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;

	}


	/*
		@author:chf
		@function:确认开票页(保险订单)
		@time:2013-7-19
	*/
	function confirmopen(){
		$id = $_REQUEST['id'];//保险订单IDS
		
		foreach($id as $k=>$v){
			$ids.=$v.",";
		}
		$ids = substr($ids,0,-1);
		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
			$data['start_addtime'] = $_REQUEST['start_addtime'];
			$data['end_addtime'] = $_REQUEST['end_addtime'];
		}

		$map['id'] = array('in',$ids);
	    $count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$allpay+=$Insurance['loss_price'];//结算金额
				$commission+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$amount = $allpay-$commission;//应付款
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$allpay);
		$this->assign('commission',$commission);
		$this->assign('amount',$amount);
		$this->display();
	}
	
		/*
		@author:chf
		@function:开票(保险订单)
		@time:2013-7-19
	
	*/
	function save_open(){
		$id = $_REQUEST['id'];//保险订单IDS
		$open_time = $_REQUEST['open_time'];//保险订单IDS
		foreach($open_time as $k=>$v){
			if($v==''){ unset($open_time[$k]); }
		}
		$info = array_combine($id,$open_time);
		//print_r($info);
		
		foreach($info as $k=>$v){
			$Bidorderjiesuan = $this->BidorderjiesuanModel->where(array('shop_id'=>$k,'jiesuan_status'=>'2'))->select();
			//print_r($Bidorderjiesuan);
			$this->BidorderjiesuanModel->where(array('shop_id'=>$k,'jiesuan_status'=>'2'))->data(array('jiesuan_status'=>'3','open'=>1,'open_time'=>strtotime($v)))->save();
			//echo $this->BidorderjiesuanModel->getLastsql();exit;
			foreach($Bidorderjiesuan as $kk=>$vv){
				$this->Bidjiesuan_logModel->add(array('bidorderjiesuan_id'=>$vv['id'],'name'=>$_SESSION['loginAdminUserName'],'log'=>'将商家确认订单改为已开票','addtime'=>time()));
			}
				
		}
		$this->success('开票成功！',U('/Balance/Bidjiesuan/index/type/collectbidorder'));
	
	
	
	}


	/*
		@author:wwy
		@function:显示保险订单待收款信息页
		@time:2014-4-24
	*/
	function collectbidorder(){
		$map['jiesuan_status'] = 3;
		if($_REQUEST['shop_id']){
			$map['shop_id'] = $_REQUEST['shop_id'];
		}
// 		if($_REQUEST['start_addtimetime'] && $_REQUEST['end_addtimetime']){
// 			$map['addtime'] = array(array('lt',strtotime($_REQUEST['end_addtime'])),array('gt',strtotime($_REQUEST['start_addtime'])),'AND');
// 			$data['start_addtime'] = $_REQUEST['start_addtime'];
// 			$data['end_addtime'] = $_REQUEST['end_addtime'];
// 		}
		if($_REQUEST['pay_start_time'] && $_REQUEST['pay_end_time']){
			$map['complete_time'] = array(array('lt',strtotime($_REQUEST['pay_end_time'])),array('gt',strtotime($_REQUEST['pay_start_time'])),'AND');
			$data['pay_start_time'] = $_REQUEST['pay_start_time'];
			$data['pay_end_time'] = $_REQUEST['pay_end_time'];
		}
		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			//$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出说
		$page = $p->show_admin();
		
		//print_r($map);
		
		$sql=" SELECT shop_id,COUNT(shop_id) as count,sum(insurance_commission) as insurance_commission,sum(company_commission) AS company_commission FROM `xc_bidorderjiesuan` WHERE ( `jiesuan_status` = 3 ) GROUP BY shop_id {$limit} ";
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->query($sql);
		//echo $this->BidorderjiesuanModel->getLastsql();

		
		foreach($data['Bidorderjiesuan'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
			
			$lastcoupon = $this->BidorderModel->where(array('is_jiesuan'=>1,'order_status'=>4,'pay_status'=>1,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Bidorderjiesuan'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];

			$data['Bidorderjiesuan'][$k]['commission'] = $v['insurance_commission']+$v['company_commission'];
 
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;

	}

	/*
	@author:wwy
	@function:收款(保险订单)
	@time:2013-7-19

	*/
	function save_collect(){
		$id = $_REQUEST['id'];//保险订单IDS
		$collect_time = $_REQUEST['collect_time'];//保险订单IDS
		foreach($collect_time as $k=>$v){
			if($v==''){ unset($collect_time[$k]); }
		}
		$info = array_combine($id,$collect_time);
		//print_r($info);
		
		foreach($info as $k=>$v){
			$Bidorderjiesuan = $this->BidorderjiesuanModel->where(array('shop_id'=>$k,'jiesuan_status'=>'3'))->select();
			//print_r($Bidorderjiesuan);
			$this->BidorderjiesuanModel->where(array('shop_id'=>$k,'jiesuan_status'=>'3'))->data(array('jiesuan_status'=>'4','collect_time'=>strtotime($v)))->save();
			//echo $this->BidorderjiesuanModel->getLastsql();exit;
			foreach($Bidorderjiesuan as $kk=>$vv){
				$this->Bidjiesuan_logModel->add(array('bidorderjiesuan_id'=>$vv['id'],'name'=>$_SESSION['loginAdminUserName'],'log'=>'将已开票订单改为已收款','addtime'=>time()));
			}
				
		}
		$this->success('收款成功！',U('/Balance/Bidjiesuan/index/type/donebidorder'));
	
	}

	/*
		@author:wwy
		@function:显示结算完成保险订单信息页
		@time:2014-4-24
	*/
	function donebidorder(){
		$map['jiesuan_status'] = 4;
		$count = $this->BidorderjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出说
		$page = $p->show_admin();
		
		//print_r($map);
		
		$sql=" SELECT shop_id,COUNT(shop_id) as count,sum(insurance_commission) as insurance_commission,sum(company_commission) AS company_commission FROM `xc_bidorderjiesuan` WHERE ( `jiesuan_status` = 4 ) GROUP BY shop_id {$limit} ";
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->query($sql);
		//echo $this->BidorderjiesuanModel->getLastsql();

		
		foreach($data['Bidorderjiesuan'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
			
			$lastcoupon = $this->BidorderModel->where(array('is_jiesuan'=>1,'order_status'=>4,'pay_status'=>1,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Bidorderjiesuan'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];

			$data['Bidorderjiesuan'][$k]['commission'] = $v['insurance_commission']+$v['company_commission'];
 
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('allpay',$data['allpay']);
		$this->assign('commission',$data['commission']);
		$this->assign('amount',$data['amount']);
		return $data;

	}



	/*
		@author:chf
		@function:得到店铺名
		@time:2013-5-6
	*/
	function GetShopname(){
		if($_REQUEST['shopname']){
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
		}
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}

	//结算状态转换 wuwenyu
	function get_status($key){
		$status_array=array(
			'0'=>'未结算',
			'1'=>'申请结算',
			'2'=>'商家确认',
			'3'=>'已打款',
		);
		if($key){$jiesuan_status=$status_array[$key];}
		return $jiesuan_status;
	}
	//导出EXECL wuwenyu
	function export_execl(){
		$map['jiesuan_status'] = 2;
		$data['Bidorderjiesuan'] = $this->BidorderjiesuanModel->order('id DESC')->where($map)->select();
		//echo $this->BidorderjiesuanModel->getLastsql();
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $k=>$v){
				$Bidorder = $this->BidorderModel->where(array('id'=>$v['bidorder_id']))->find();
				$data['Bidorderjiesuan'][$k]['truename'] = $Bidorder['truename'];
				
			
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidorderjiesuan'][$k]['order_id'] = $this->get_orderid($v['bidorder_id']);
				$data['Bidorderjiesuan'][$k]['shop_name'] = $shop['shop_name'];
				$Insurance = $this->InsuranceModel->where(array('id'=>$v['insurance_id']))->find();
			
				$data['Bidorderjiesuan'][$k]['insurance_name'] = $Insurance['insurance_name'];//公司名称
				$data['Bidorderjiesuan'][$k]['loss_price'] = $Insurance['loss_price'];
				$data['Bidorderjiesuan'][$k]['insurance_commission'] = $Insurance['loss_price']*($v['insurance_rebate']/100);//4S店返利佣金
				$data['Bidorderjiesuan'][$k]['company_commission'] = $Insurance['loss_price']*($v['company_rebate']/100);//保险公司返利佣金
				$data['allpay']+=$Insurance['loss_price'];//结算金额
				$data['commission']+=$data['Bidorderjiesuan'][$k]['insurance_commission']+$data['Bidorderjiesuan'][$k]['company_commission'];//返利佣金总和

			}
			$data['amount'] = $data['allpay']-$data['commission'];//应付款
		}
		if($data['Bidorderjiesuan']){
			foreach($data['Bidorderjiesuan'] as $key=>$val){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table .= '<tr bgcolor='.$color.'><td>'.$val['order_id'].'('.$val['bidorder_id'].')</td><td>'.$val['truename'].'</td><td>'.$val['shop_name'].'</td><td>'.$val['insurance_name'].'</td><td>'.$val['loss_price'].'</td><td>'.$val['insurance_commission'].'</td><td>'.$val['insurance_rebate'].'</td><td>'.$val['company_commission'].'</td><td>'.$val['company_rebate'].'</td><td>商家确认</td>';
			}
		}
		$color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename='代开票订单.xls'");
        $str = '<table><tr bgcolor='.$color.'><td>保险订单号</td><td>姓名</td><td>4S店</td><td>保险公司</td><td>保险金额</td><td>4S店佣金</td><td>4S店佣金比例</td><td>保险公司佣金</td><td>保险公司佣金比例</td><td>订单状态</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

   
}