<?php
/*
 */
class SafeorderAction extends CommonAction {
    function __construct() {
		parent::__construct();
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }


		$this->BidorderModel = D('bidorder');//保险类订单表
		
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->InsuranceimgModel = D('insuranceimg');//用户保险竞价表
		$this->ShopModel = D('shop');//用户保险竞价表
		$this->shop_fs_relationModel = D('shop_fs_relation');//
		$this->FsModel = D('fs');//品牌表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
	}


	/*
		@author:chf
		@function:显示保险类订单页
		@time:2013-05-07
	*/
    public function index(){
		if($_REQUEST['insurance_status']){
			$data['insurance_status'] = $map['insurance_status'] = $_REQUEST['insurance_status'];
		}else {
			$map['insurance_status']=array('gt','0');
		}
		 $shop_fs_relation = $this->shop_fs_relationModel->where(array('shopid'=>$_SESSION['shop_id']))->find();
		 if($shop_fs_relation){
			$shop = $this->ShopModel->where(array('id'=>$shop_fs_relation['shopid']))->find();	
			
			if($shop['safestate'] == '1'){
				if($_REQUEST['start_time'] && $_REQUEST['end_time']){
					$data['start_time'] = $_REQUEST['start_time'];
					$data['end_time'] = $_REQUEST['end_time'];
					$map['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
				}

				$map['state'] = 1;
				$map['fsid'] = $shop_fs_relation['fsid'];
				// 计算总数
				$count = $this->InsuranceModel->where($map)->count();
				//导入分页类
				import("ORG.Util.Page");
				// 实例化分页类
				$p = new Page($count,10);
				// 分页显示输出
				$page = $p->show_admin();
				$data['Insurance'] = $this->InsuranceModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
				
				if($data['Insurance']){
					foreach($data['Insurance'] as $k=>$v){
						//$data['Insurance'][$k]['user_phone'] = preg_replace("/(\d{3})(\d{4})/","$1****",$v['user_phone']);
						$fs = $this->FsModel->where(array('fsid'=>$v['fsid']))->find();					
						$data['Insurance'][$k]['fsname'] = $fs['fsname'];
						$shop_bidding  = $this->ShopbiddingModel->where(array('insurance_id'=>$v['id'],'shop_id'=>$_SESSION['shop_id']))->find();
						if($shop_bidding) {
							$data['Insurance'][$k]['shopbidcount'] = 1;
							$data['Insurance'][$k]['shop_bidding_id'] = $shop_bidding['id'];
						}else {
							$data['Insurance'][$k]['shopbidcount'] = 0;
						}
						$bidorder = $this->BidorderModel->where(array('insurance_id'=>$v['id'],'shop_id'=>$_SESSION['shop_id']))->find();
						 if ($bidorder) {
						 	$data['Insurance'][$k]['bidcount'] = 1;
						 }else{
						 	$data['Insurance'][$k]['bidcount'] = 0;
						 }

					}
				}

				$this->assign('data',$data);
				$this->assign('page',$page);
				$this->assign('nowtime',time());
				$this->display();
			}else {
				$this->error('抱歉您没参与此项保险订单活动！');
			}
		 }else{
			$this->error('抱歉您没参与此项保险订单活动！');
		 }
    }

	/*
		@author:chf
		@function:显示4s店参与此保险订单类页
		@time:2013-05-07
	*/	
	function partake(){
		$data['Insurance'] = $this->InsuranceModel->where(array('id'=>$_REQUEST['id']))->find();
		$type = $_REQUEST['type'];//记录是从订单详情后退过来的值
//		if((time() > $data['Insurance']['create_time']+60*15) && !$type){//15分钟内没预约就无法参与
//			$this->error('此订单已超过参与时间！');
//		
//		}else{
		
		
			$data['Insuranceimg'] = $this->InsuranceimgModel->where(array('insurance_id'=>$_REQUEST['id']))->select();
			$data['Fs'] = $this->FsModel->where(array('fsid'=>$data['Insurance']['fsid']))->find();

			$car_info = $this->get_car_info($data['Insurance']['brand_id'] , $data['Insurance']['series_id'] , $data['Insurance']['model_id']);
			$data['Insurance']['car_info'] = $car_info;

			$this->assign('insurance_id',$_REQUEST['id']);
			$this->assign('data',$data);
			$this->assign('nowtime',time());
			$this->display();
//		}
	}

	/*
		@author:chf
		@function:显示用户车辆受损图片页面
		@time:2013-05-07
	*/
	function showimg(){
		$car_location_arr = array(
			'1' => "正面",
			'2' => "左侧面",
			'3' => "右侧面",
			'4' => "背面",
			'5' => "远景照",	
		);
		$data['Insuranceimg'] = $this->InsuranceimgModel->where(array('insurance_id'=>$_REQUEST['id']))->select();
		if($data['Insuranceimg']){
			foreach($data['Insuranceimg'] as $k=>$v){
				$data[$key]['car_location'] = $car_location_arr[$v['car_location']];
			}
		}
		$this->assign('data',$data);
		$this->assign('id',$_REQUEST['id']);
		$this->assign('type',$_REQUEST['type']);
		$this->display();
	}
	
	/*
		@author:chf
		@function:添加进4S店竞价表
		@time:2013-05-07
	*/	
	function insert(){

		$data['servicing_time'] = $_REQUEST['servicing_time'];//维修时间
		$data['scooter'] = $_REQUEST['scooter'];//代步车
		$data['rebate'] = $_REQUEST['rebate'];//现金返利
		$data['insurance_id'] = $_REQUEST['insurance_id'];//竞价表ID
		$data['remark'] = $_REQUEST['remark'];//备注
		$data['create_time'] = time();//时间
		$data['shop_id'] = $_SESSION['shop_id'];//店铺ID

		$this->ShopbiddingModel->add($data);
		$this->success('提交成功！',U('/Shop/Safeorder/index'));

	}

	/*
		@author:chf
		@function:进4S店竞价表(修改页面)
		@time:2013-05-07
	
	*/
    function edit(){
		$map['insurance_id'] = $_REQUEST['id'];
		$map['shop_id'] = $_SESSION['shop_id'];
		$data['Shopbid'] = $this->ShopbiddingModel->where($map)->find();
		$data['Insurance'] = $this->InsuranceModel->where(array('id'=>$_REQUEST['id']))->find();
		$data['Insuranceimg'] = $this->InsuranceimgModel->where(array('insurance_id'=>$_REQUEST['id']))->select();
		$data['Fs'] = $this->FsModel->where(array('fsid'=>$data['Insurance']['fsid']))->find();
		
		$car_info = $this->get_car_info($data['Insurance']['brand_id'] , $data['Insurance']['series_id'] , $data['Insurance']['model_id']);
		$data['Insurance']['car_info'] = $car_info;

		$this->assign('insurance_id',$_REQUEST['id']);
		$this->assign('data',$data);
		$this->display();
	}

	/*
		@author:chf
		@function:修改4S店竞价表
		@time:2013-05-13
	*/
	function edit_do(){
		$data['servicing_time'] = $_REQUEST['servicing_time'];//维修时间
		$data['scooter'] = $_REQUEST['scooter'];//代步车
		$data['rebate'] = $_REQUEST['rebate'];//现金返利
		$map['insurance_id'] = $_REQUEST['insurance_id'];//竞价表ID
		$map['shop_id'] = $_SESSION['shop_id'];
		$data['remark'] = $_REQUEST['remark'];//备注
		//$data['create_time'] = time();//时间
		$this->ShopbiddingModel->where($map)->data($data)->save();
		$this->success('修改成功！',U('/Shop/Safeorder/index'));
	
	}

	/*
		@author:chf
		@function:订单详情页
		@time:2013-05-13
	*/
	function bidorder(){
		$map['insurance_id'] = $_REQUEST['id'];//竞价ID
		$map['status'] = 1;
		$data = $this->BidorderModel->where($map)->find();
		$shop = $this->ShopModel->where(array('id'=>$data['shop_id']))->find();
		$data['shop_name'] = $shop['shop_name'];
		$data['order_id'] = $this->get_orderid($data['id']);

		$array['Shopbid'] = $this->ShopbiddingModel->where(array('id'=>$data['bid_id']))->find();
		$array['Insurance'] = $this->InsuranceModel->find($_REQUEST['id']);
		$car_info = $this->get_car_info($array['Insurance']['brand_id'] , $array['Insurance']['series_id'] , $array['Insurance']['model_id']);
		$array['Insurance']['car_info'] = $car_info;

		$array['Insuranceimg'] = $this->InsuranceimgModel->where(array('insurance_id'=>$_REQUEST['id']))->select();
		$array['Fs'] = $this->FsModel->where(array('fsid'=>$array['Insurance']['fsid']))->find();

		$this->assign('data',$data);
		$this->assign('array',$array);
		$this->assign('insurance_id',$_REQUEST['id']);
		$this->assign('type',$_REQUEST['type']);
		$this->display();
	}

	

    
}