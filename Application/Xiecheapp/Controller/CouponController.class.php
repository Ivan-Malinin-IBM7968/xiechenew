<?php
//订单
namespace Xiecheapp\Controller;

class CouponController extends CommonController {
	public function __construct() {
		parent::__construct();
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('membersalecoupon');//
		$this->assign('noshow',true);
		$this->assign('noclose',true);
	}


    public function index(){
    	$model_id = @$_SESSION['modelId'];
		//获得券
		$this->get_tuijian_coupon('',$model_id,4,true);

		$this->assign('title',"保养团购价格最低_售后现金券实惠便捷_售后最优惠_纵横携车网");
		$this->assign('meta_keyword',"保养套餐,团购券,现金券,保养团购,保养打折优惠,保养优惠券");
		$this->assign('description',"纵横携车网保养套餐,售后现金券团购为汽车用户提供最实惠的4S店汽车维修保养服务,价格最低,最优惠,活动天天有,必将给您带来完美养车体验!");
		$this->display();
	}


	public function type(){
		$type_id = isset($_GET['id'])?$_GET['id']:1;
		$model_id = @$_SESSION['modelId'];
		if($type_id==1){

			$this->get_tuijian_coupon('',$model_id,12);
			$this->assign('title',"跨品牌团购券");

		}else if($type_id==2){
			$this->get_tuijian_coupon('',$model_id,12);
			$this->assign('title',"本品牌团购券");

		}else if($type_id==3){

			$this->get_tuijian_coupon('',$model_id,12);
			$this->assign('title',"现金券");

		}
		$this->assign('type_id',$type_id);
		$this->assign('meta_keyword',"保养套餐,团购券,现金券,保养团购,保养打折优惠,保养优惠券");
		$this->assign('description',"纵横携车网保养套餐,售后现金券团购为汽车用户提供最实惠的4S店汽车维修保养服务,价格最低,最优惠,活动天天有,必将给您带来完美养车体验!");
		$this->display();
	}


	function detail(){
		$coupon_id = isset($_GET['id'])?$_GET['id']:0;
		if ($coupon_id){
			$this->assign('choseCar',false);
			$model_coupon = D('Coupon');
	
			$memcache_coupon = S('coupon'.$_GET['id']);
			if($memcache_coupon) {
				$coupon = $memcache_coupon;
			}else {
				$coupon = $model_coupon->find($coupon_id);
				//var_dump($model_coupon);exit;
				if($coupon['show_e_time'] < time()) {
					$coupon['is_delete'] = 1;
				}
				S('coupon'.$_GET['id'] , $coupon );
			}
		
			$coupon['save_money'] = $coupon['cost_price'] - $coupon['coupon_amount'];
			$this->get_coupon_info($coupon);
			$shop_id = $coupon['shop_id'];
			$model_shop = D('Shop');
			$map_s['id'] = $shop_id;
			$shopinfo = $model_shop->where($map_s)->find();
	
			$memcache_other_coupon = S('other_coupon'.$_GET['id']);
			if($memcache_other_coupon) {
				$other_coupon = $memcache_other_coupon;
			}else {
				$map_c['shop_id'] = $shop_id;
				$map_c['id'] = array('neq',$coupon_id);
				$map_c['is_delete'] = 0;
				$map_c['show_s_time'] = array('lt',time());
				$map_c['show_e_time'] = array('gt',time());
				$other_coupon = $model_coupon->where($map_c)->select();
				S('other_coupon'.$_GET['id'] , $other_coupon );
			}
	
	
			$this->assign('other_coupon',$other_coupon);
			$this->assign('shopinfo',$shopinfo);
		}
		
		$this->assign("coupon",$coupon);
		$this->assign("coupon_id",$coupon_id);
	
		$model_carmodel = D('carmodel');
	
		$mode_info = $model_carmodel->getByModel_id($coupon['model_id']);
	
	
	
		$model_shop_fs_relation = D('shop_fs_relation');
		$FsModel = D('fs');
		$FsId = $model_shop_fs_relation->where(array('shopid'=>$coupon['shop_id']))->select();
	
		if($FsId){
			foreach($FsId as $k=>$v){
	
				$fsname = $FsModel->where(array('fsid'=>$v['fsid']))->find();
	
				$datafsname.= $fsname['fsname'];
			}
		}
	
	
	
		/*取得该团购券能使用的车型车系*/
	
		$memcache_seriesinfo = S('Seriesinfo'.$_GET['id']);
		//if($memcache_seriesinfo) {
		//$Seriesinfo = $memcache_seriesinfo;
		//}else {
	
		$SeriesModel = D('Carseries');
		$CarModel = D('Carmodel');
		$ArrModel_id = explode(',',$coupon['model_id']);
		$CarbrandModel = D('carbrand');
		$Brand = $CarbrandModel->where(array('brand_id'=>$coupon['brand_id']))->find();
		$ArrSeries_id = explode(',',$coupon['series_id']);
		$ArrSeries_id = array_unique($ArrSeries_id);
		if($coupon['coupon_type'] == 2){
			if($ArrSeries_id){
				foreach($ArrSeries_id as $v){
					$modelid = "";
					$SeriesList = $SeriesModel->where(array('series_id'=>$v))->find();
					$CarmodelID = $CarModel->where(array('series_id'=>$v))->select();
					foreach($CarmodelID as $car_k=>$car_v){
						for($a=0;$a<count($ArrModel_id);$a++){
							if($ArrModel_id[$a]){
								if($car_v['model_id'] == $ArrModel_id[$a]){
									$modelid.= $car_v['model_id'].",";
								}
							}
						}
					}
					$modelid = substr($modelid,0,-1);
					$sql = "model_id in (".$modelid.")";
					$str = $CarModel->where($sql)->select();
	
					$model_info ="";
	
					foreach($str as $str_k=>$str_v){
						$StrSeries = $SeriesModel->where(array('series_id'=>$str_v['series_id']))->find();
						$model_info.= " ".$str_v['model_name'];
					}
	
					$Seriesinfo.= "<li>".$datafsname.$SeriesList['series_name']."：".$model_info."</li>";
				}
			}
	
		}else{
			$Seriesinfo = $datafsname."所有车型车系";
	
		}
	
		//S('Seriesinfo'.$_GET['id'] , $Seriesinfo);
	
		//}
	
		/*取得该团购券能使用的车型车系*/
		
		$this->assign('Direction',$Seriesinfo);
		$this->assign('phone',C('CALL_400'));
		$this->assign('coupon_type',$coupon['coupon_type']);
		if(TOP_CSS == "pa") {
			$this->assign('title',"{$coupon[coupon_name]}_汽车保养维修团购_平安好车-携车网");
		}else {
	
			$this->assign('title',$coupon['coupon_name']."|".$shopinfo['shop_name']."|".$shopinfo['shop_address']."_汽车保养维修团购_携车网");
		}
		$this->assign('meta_keyword',"保养费用,".$coupon['coupon_name']);
		$this->assign('description',$coupon['coupon_name']."，原价{$coupon[cost_price]}元(全国同一价)，套餐优惠价{$coupon[coupon_amount]}元。套餐有效期截止到".date("Y年m月d日",$coupon['end_time'])."，仅限通过携车网提前预约并网上支付后到店接受服务。");
		
		$this->display();
	}
	
	public function order(){
		if( true !== $this->login()){
			exit;
		}
		$uid = $this->GetUserId();
		$model_member = D('Member');
		$map_m['uid'] = $uid;
		$member = $model_member->where($map_m)->find();
	    $coupon_id = isset($_GET['id'])?$_GET['id']:0;
	    if ($coupon_id){
	        //$model_tuangou = D('Tuangou');
	        $model_coupon = D('Coupon');
	        $coupon = $model_coupon->find($coupon_id);
	        $coupon['save_money'] = $coupon['cost_price'] - $coupon['coupon_amount'];
	        $this->get_coupon_info($coupon);
	    }
	    $this->get_tuijian_coupon();

	    $Member = $this->MemberModel->where(array('uid'=>$uid))->find();
		$Msalecoupon = $this->MembersalecouponModel->where(array('uid'=>$uid,'salecoupon_id'=>array('in','4,6'),'mobile'=>$Member['mobile'],'is_use'=>'0'))->find();

		//获取商户现金账户余额
		$model_memberaccount = D('memberaccount');
		$map_m['uid'] = $uid;
		$member = $model_memberaccount->where($map_m)->find();
		$coupon['amount'] = $member['amount'];

		$this->assign('Msalecoupon',$Msalecoupon);
		$this->assign('sessuid',$uid);

	    $this->assign("coupon",$coupon);
	    $this->assign("coupon_id",$coupon_id);
	    $this->assign("member",$member);

		if(TOP_CSS == "pa") {
			$this->assign('title',"{$coupon[coupon_name]}_汽车保养维修团购_平安好车-携车网");
		}else {
			$this->assign('title',$coupon['coupon_name']."-购买-4S店售后团购-携车网");
		}
		$this->assign('meta_keyword',"保养费用,".$coupon['coupon_name']);
		$this->assign('description',$coupon['coupon_name']."，原价{$coupon[cost_price]}元(全国同一价)，套餐优惠价{$coupon[coupon_amount]}元。套餐有效期截止到".date("Y年m月d日",$coupon['end_time'])."，仅限通过携车网提前预约并网上支付后到店接受服务。");
		
		$this->display();
	}

	function get_coupon_info($coupon){
	    if ($coupon['brand_id']){
            $model_carbrand = D('Carbrand');
            $map_b['brand_id'] = $coupon['brand_id'];
            $brand = $model_carbrand->where($map_b)->find();
            $this->assign('brand',$brand);
        }
         if ($coupon['series_id']){
            $model_carseries = D('Carseries');
            $map_s['series_id'] = $coupon['series_id'];
            $series = $model_carseries->where($map_s)->find();
            $this->assign('series',$series);
        }
        if ($coupon['model_id']){
            $model_carmodel = D('Carmodel');
            $map_m['model_id'] = $coupon['model_id'];
            $model = $model_carmodel->where($map_m)->find();
            $this->assign('model',$model);
        }
	}

	/*
		@author:bright
		@function:下团购券订单
		@time:2014-11-10
	*/
	function create_order(){
		
		$model_coupon = D('Coupon');
		$model_membercoupon = D('Membercoupon');
		
		$this->is_safepay();//处理现金账户支付流程
		
		$membersalecoupon_id = $_POST['membersalecoupon_id'];//立减券ID
		//得到优惠券信息
		$membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id,'is_use'=>'0','is_delete'=>'0'))->find();
		
		$coupon_id = isset($_POST['coupon_id'])?$_POST['coupon_id']:0;
		
		$number = isset($_POST['number'])?$_POST['number']:1;
		if ($number>6){
			$this->error("请分批购买",__APP__.'/coupon/',true);
		}
		$mobile = isset($_POST['mobile'])?$_POST['mobile']:0;
		if (!$mobile){
			$this->error("请确认手机号码是否正确",__APP__.'/coupon/',true);
		}
		 
		if ($number>0 and is_numeric($number)){
			$map_coupon['id'] = $coupon_id;
			$map_coupon['end_time'] = array('gt',time());
			$coupon = $model_coupon->where($map_coupon)->find();
			
			//			$model_shop = D('shop');
			//			$shop = $model_shop->where(array('id'=>$coupon['shop_id']))->find();
			if($coupon['is_delete'] == '1'){
				$this->error("抱歉,此优惠卷已下架！",null,__APP__.'/coupon/');
			}else{  //300*（1-100/600）
				if($coupon){
					$All_amount = $coupon['coupon_amount']*$number;//总支付金额
					if($membersalecoupon_id > 0 && $number>1){
						$AllSaleamount = $All_amount-$membersalecoupon['money'];//
					}
		
					if($membersalecoupon_id){
						$data['membersalecoupon_id'] = $membersalecoupon_id;
					}
					$membercoupon_id_str = '';
		
					for ($ii = 1; $ii <= $number; $ii++) {
						//有立减券-100
						if($membersalecoupon_id > 0 && $number==1){//1张优惠券
							$data['coupon_amount'] = $coupon['coupon_amount'] - $membersalecoupon['money'];//支付金额
						}else if($membersalecoupon_id > 0 && $number>1){//多张优惠券
		
							if($ii != $number){
								$data['coupon_amount'] = $coupon['coupon_amount']*(1-sprintf("%.2f", ($membersalecoupon['money']/$All_amount)));
								$Saleamount+= $data['coupon_amount'];
							}else{
								$data['coupon_amount'] = $AllSaleamount - $Saleamount;
							}
						}else if(!$membersalecoupon_id){//没有立减券
							$data['coupon_amount'] = $coupon['coupon_amount'];
						}
							
						$uid = $this->GetUserId();
						$data['uid'] = $uid;
						$data['coupon_id'] = $coupon_id;
						$data['coupon_name'] = $coupon['coupon_name'];
						$data['shop_ids'] = $coupon['shop_id'];
						$data['mobile'] = $mobile;
						$data['end_time'] = $coupon['end_time'];
						$data['start_time'] = $coupon['start_time'];
						$data['coupon_type'] = $coupon['coupon_type'];
						$data['create_time'] = time();
		
						/* 改到使用的时候再去计算
						 if($coupon['coupon_type']=='1'){
						$data['ratio'] = $shop['cash_rebate'];//现金券比例
						}else{
						$data['ratio'] = $shop['group_rebate'];
						}*/
						//记录购买平安好车访问用户
						if(PA_BANNER == 'pa'){
							$data['pa'] = '1';
						}
						//百车保用户
						if($_SESSION['bcb']){
							$data['pa'] = '4';
						}
						if($membercoupon_id = $model_membercoupon->add($data)){
							$membercoupon_id_arr[] = $membercoupon_id;
						}
					}
					$membercoupon_id_str = implode(',',$membercoupon_id_arr);
					$_SESSION['membercoupon_ids'] = $membercoupon_id_str;
					if($membersalecoupon_id > 0){
						$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('is_use'=>'1','use_time'=>$data['create_time']));//修改红包状态测试完毕后在加上
					}
					//消除百车宝session
					$_SESSION['bcb'] = "";
		
					if($_POST['account_amount']>0){
						//计算现金账户支付之后是否还需要走线上支付流程
						$map_c['id'] = $_REQUEST['coupon_id'];
						$coupon = $this->CouponModel->where($map_c)->find();
						$all_amount = $coupon['coupon_amount']*$_REQUEST['number'];
						if($_POST['account_amount']<$all_amount){
							$this->success("订单已提交,请立即付款",__APP__.'/coupon/couponpay',true);
						}else{
							//支付完成善后处理
							$this->update_membercoupon_state($membercoupon_id,1);
							$this->update_coupon_count($_REQUEST['coupon_id']);
							$this->success("购买成功并用现金账户余额完成支付",__APP__.'/coupon',true);
						}
					}else{
						$this->success("订单已提交,请立即付款",__APP__.'/coupon/couponpay',true);
					}
						
				}else{
					$_SESSION['bcb'] = "";
					$this->error("操作失败",__APP__.'/coupon',true);
				}
			}
		}else {
			$_SESSION['bcb'] = "";
			$this->error("购买数量输入错误",__APP__.'/coupon',true);
		}
		 	    
	}
	
	/*
		@author:bright
		@function:支付页
		@time:2014-11-12
	*/
	function couponpay(){
		$this->assign('sessuid',$_SESSION['uid']);
		$_SESSION['jump'] = "jump";
		if( isset($_GET['membercoupon_id']) ){
			$membercoupon_id = $_GET['membercoupon_id'];
		}else{
			$membercoupon_id = isset($_SESSION['membercoupon_ids'])?$_SESSION['membercoupon_ids']:0;
		}
		$uid = $this->GetUserId();
		if ($membercoupon_id){
			$model_membercoupon = D('Membercoupon');
			$model_coupon = D('Coupon');
			$map_m['membercoupon_id'] = array('in',$membercoupon_id);
			$map_m['uid'] = $uid;
			$map_m['end_time'] = array('gt',time());
			$membercoupon = $model_membercoupon->where($map_m)->select();
			//var_dump($membercoupon);exit;
			$all_amount = 0;
			if ($membercoupon){
				foreach ($membercoupon as $k=>$v){
					$coupon_id = $v['coupon_id'];
					$map_c['id'] = $coupon_id;
					$coupon = $model_coupon->where($map_c)->find();
					$membercoupon[$k]['couponinfo'] = $coupon;
					$all_amount+= $v['coupon_amount'];
					$this->get_coupon_info($coupon);
					$coupon_name = $v['coupon_name'];
				}
			}
			//扣除现金账户支付部分
			$all_amount = $all_amount-$_SESSION['account_amount'];
			$this->assign('all_amount',$all_amount);
			$this->assign('coupon_name',$coupon_name);
			$this->assign('membercoupon_id',$membercoupon_id);
			$this->assign('membercoupons',$membercoupon);
		}
		$this->get_tuijian_coupon();
	
	    $this->display('couponpay');
	}

	/*
		@author:chf
		@function:
		@time:2014-03-20
	*/

		//支付跳转判断 wuwenyu
	function do_couponpay(){
		if(true !== $this->login()){
			exit;
		}
		
		$key = isset($_REQUEST['sel_online_pay'])?$_REQUEST['sel_online_pay']:1;
		
		$membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:1;
	
		$bank_type=$this->get_bank($key);
		
		if($bank_type=='CFT'){
			$url=WEB_ROOT.'/txpay/payRequest.php?membercoupon_id='.$membercoupon_id;
		}
		if($bank_type=='YL'){
			$url=WEB_ROOT.'/unionpay/front.php?membercoupon_id='.$membercoupon_id;
		}
		//暂时关闭交行直通支付
		/*if($bank_type=='COMM'){
			$membercoupons = $this->get_membercoupons($membercoupon_id);
			$coupon_amount = 0;
			if($membercoupons){
				foreach($membercoupons as $k=>$v){
					$coupon_amount += $v['coupon_amount'];
				}
			}
			$array='interfaceVersion=1.0.0.0&orderid='.$membercoupon_id."&orderDate=".date('Ymd',time())."&orderTime=".date('His',time()).'&tranType=0&amount='.$coupon_amount.'&curType=CNY&orderMono=6222600110030037084& notifyType=1&merURL=http://www.xieche.com.cn&goodsURL=http://www.xieche.com.cn&netType=0';
			$url=WEB_ROOT.'/comm_pay/merchant.php?'.$array;
		}*/
		//建行网上支付 手机支付
		if($bank_type=='CCB'){
            $membercoupons = $this->get_membercoupons($membercoupon_id);
			$coupon_amount = 0;
			if($membercoupons){
				foreach($membercoupons as $k=>$v){
					$coupon_amount += $v['coupon_amount'];
				}
			}
			//$membercoupon_id =11111;
			//$coupon_amount = 0.01;
		   $array='ORDERID='.$membercoupon_id.'&PAYMENT='.$coupon_amount;
			$url=WEB_ROOT.'/ccb_pay/merchant.php?'.$array;

		}
		if($bank_type=='WXPAY'){
			$url=WEB_ROOT.'/weixinpaytest/nativecall.php?membercoupon_id='.$membercoupon_id;
		}
		//if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='COMM' and $bank_type!='WXPAY'){
		if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='CCB' and $bank_type!='WXPAY'){
		//if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='WXPAY'){
			$url=WEB_ROOT.'/txpay/payRequest.php?membercoupon_id='.$membercoupon_id.'&bank_type='.$bank_type;
		}

		header("Location:$url");
		//$this->display('do_couponpay');
	}
	//银行转换数组 wuwenyu
	function get_bank($key){
		$bank_array=array(
			'19'=>'CMB',//招商银行
			'31'=>'BOC',//中国银行
			'17'=>'ICBC',//工商银行
			'18'=>'ABC',//农业银行
			'20'=>'CCB',//建设银行
			'74'=>'PAB',//平安银行
			'501'=>'POSTGC',//邮储银行
			'34'=>'CEB',//光大银行
			'35'=>'CIB',//兴业银行
			'36'=>'CITIC',//中信银行
			'37'=>'CMBC',//民生银行
			'38'=>'COMM',//交通银行
			'39'=>'GDB',//广东发展银行
			'41'=>'BOSH',//上海银行
			'42'=>'SPDB',//浦发银行
			'100'=>'YL',//银联
			'8'=>'CFT',//财付通
			'502'=>'WXPAY',//微信支付
		);
		if($key){$bank_type=$bank_array[$key];}
		return $bank_type;
	}

	function get_membercoupons($membercoupon_ids){
	    $model_membercoupon = D('Membercoupon');
		$sql = "select * from xc_membercoupon where membercoupon_id in (".$membercoupon_ids.") ";
		//echo $sql;
		$membercoupon=$model_membercoupon->query($sql,'all');
		//echo '<pre>';print_r($membercoupon);exit;
		if($membercoupon){
			foreach($membercoupon as $k=>$v){
				$coupon_id = $v['coupon_id'];
				$model_coupon = D('Coupon');
				$sql_coupon = "select * from xc_coupon where id='".$coupon_id."' ";
				$coupon=$model_coupon->query($sql_coupon,'assoc');
				$membercoupon[$k]['coupon_summary'] = $coupon['coupon_summary'];
				//$membercoupon[$k]['coupon_amount'] = $coupon['coupon_amount'];
			}
		}
		return $membercoupon;
	}

	function coupon_txt(){
		$model_membercoupon = D('Dianping_coupon');
		$sql = "select distinct(coupon_id) from xc_dianping_coupon ";
		$info=$model_membercoupon->query($sql,'all');
		//echo $model_membercoupon->getLastsql();
		foreach($info as $key=>$value){

			echo "<table><tr><td>".$value['coupon_id']."</td><td><a href='http://192.168.1.66/coupon/export_txt/coupon_id/".$value['coupon_id']."'>导出</a></td></tr></table>";
		}

	}
	
	function export_txt(){
		$url_info =explode('/',$_SERVER['REQUEST_URI']);
		$coupon_id= $url_info['4'];
		$model_membercoupon = D('Dianping_coupon');
		$sql = "select * from xc_dianping_coupon where coupon_id = '".$coupon_id."' ";
		$code_info=$model_membercoupon->query($sql,'all');
		//$this->export_txt($code_info,$value['coupon_id']);
		if($code_info){
			foreach($code_info as $key=>$val){
				$str .= $val['coupon_code'].',';
			}
		} 
		Header( "Content-type:   application/octet-stream "); 
		Header( "Accept-Ranges:   bytes "); 
		header( "Content-Disposition:   attachment;   filename=".$coupon_id.".txt "); 
		header( "Expires:   0 "); 
		header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 "); 
		header( "Pragma:   public "); 
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;
	}
	//处理现金账户支付部分
	function is_safepay(){
			$map['uid'] = $this->GetUserId();
		//如果用户使用了账户余额支付
		if($_POST['account_amount']){
			if($_POST['paycode']){
				//验证支付密码
				$model_membersafe = D('Membersafe');
				$safe_info = $model_membersafe->where($map)->find();
				if (pwdHash($_POST['paycode'])!=$safe_info['paycode']){
					$this->error("请确认支付密码是否正确",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
				}else{
					//确认商户现金账户余额
					$model_memberaccount = D('memberaccount');
					$member = $model_memberaccount->where($map)->find();
					if($_REQUEST['account_amount'] > $member['amount']){
						$this->error("账户余额不足",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
					}else{
						//记录账户使用记录
						$model_memberaccountlog = D('Memberaccountlog');
						$data_a['uid'] = $this->GetUserId();
						$data_a['amount'] = abs($_REQUEST['account_amount']);
						$data_a['create_time'] = time();
						$data_a['type'] = '1';
						$data_a['content'] = '购买套餐券';
						$result_a = $model_memberaccountlog->add($data_a);
						if($result_a){
							//计算余额
							$data_n['amount'] = $member['amount']-abs($_REQUEST['account_amount']);
							echo "left=".$data_n['amount'];
							$result_b = $model_memberaccount->where($map)->save($data_n);
							echo $model_memberaccount->getLastsql();
							if($result_b){
								$_SESSION['account_amount'] = $_REQUEST['account_amount'];
								return $_SESSION['account_amount'];
							}
						}
					}
				}
			}else{
				$this->error("支付密码不能为空",__APP__.'/coupon/couponbuy-coupon_id-'.$_REQUEST['coupon_id']);
			}
		}

	}

}