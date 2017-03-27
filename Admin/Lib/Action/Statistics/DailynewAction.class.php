<?php
class DailynewAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}
	
    public function index(){
		Cookie::set('_currentUrl_', __SELF__);
		$pro_info['year']=$_REQUEST['year'];
		$pro_info['month']=$_REQUEST['month'];
		if($_REQUEST['start_time'] or $_REQUEST['end_time']){
			if($_REQUEST['start_time']>0){
				$start_time=strtotime($_REQUEST['start_time']);
				$where_sql.=" and create_time>='$start_time' ";
				$this->assign('start_time', $_REQUEST['start_time']);
			}
			if($_REQUEST['end_time']>0){
				$end_time=strtotime($_REQUEST['end_time']);
				$where_sql.=" and create_time<='$end_time' ";
				$this->assign('end_time', $_REQUEST['end_time']);
			}
		}else{
			$pro_info=$this->filter($pro_info);
			$start=strtotime($pro_info['start_time']);
			$end=strtotime($pro_info['end_time']);
			$where_sql.=" and create_time>='$start' and create_time<='$end' ";
		}
		if($_REQUEST['origin_id']){
			$where_sql.=" and origin_id='$_REQUEST[origin_id]' ";
			$this->assign('origin_id', $_REQUEST['origin_id']);
		}
		//echo $where_sql;
		/*
        // 计算总数
        $count = $model_daily->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		*/

        $model_daily = D(GROUP_NAME.'/Daily_new');
		$model_bidorder = D(GROUP_NAME.'/Bidorder');
		$model_insurance = D(GROUP_NAME.'/Insurance');
        // 当前页数据查询
		$sql = "SELECT create_time,sum(order_num) as order_num,sum(member_num) as member_num,sum(sum_groupon) as sum_groupon,sum(sold_cashcoupon) as sold_cashcoupon,sum(sum_cashcoupon) as sum_cashcoupon,sum(sold_groupon) as sold_groupon FROM xc_daily_new WHERE 1 {$where_sql} GROUP BY create_time ORDER BY create_time DESC ";
        $list = $model_daily->query($sql);

		//echo $model_daily->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$map['create_time']=$value['create_time'];
				$info=$model_daily->where($map)->find();
				$list[$key]['shop_num']=$info['shop_num'];
				$list[$key]['onshelf_cashcoupon']=$info['onshelf_cashcoupon'];
				$list[$key]['onshelf_groupon']=$info['onshelf_groupon'];
				$sum['order_num']=$sum['order_num']+$value['order_num'];
				$sum['member_num']=$sum['member_num']+$value['member_num'];
				$sum['sum_groupon']=$sum['sum_groupon']+$value['sum_groupon'];
				$sum['sold_cashcoupon']=$sum['sold_cashcoupon']+$value['sold_cashcoupon'];
				$sum['sum_cashcoupon']=$sum['sum_cashcoupon']+$value['sum_cashcoupon'];
				$sum['sold_groupon']=$sum['sold_groupon']+$value['sold_groupon'];
				$sum['shop_num']=$sum['shop_num']+$value['shop_num'];
				$sum['onshelf_cashcoupon']=$sum['onshelf_cashcoupon']+$value['onshelf_cashcoupon'];
				$sum['onshelf_groupon']=$sum['onshelf_groupon']+$value['onshelf_groupon'];
				//事故车
				$bidorder_map['xc_shop.safestate']=0;
				$bidorder_map['xc_bidorder.status']=1;
				$bidorder_map['xc_bidorder.order_status']=array('neq',3);
				$bidorder_map['xc_bidorder.create_time']=array(array('egt',$value['create_time']),array('elt',($value['create_time']+86400)));
				$bidorder=$model_bidorder->join('xc_shop ON xc_bidorder.shop_id = xc_shop.id')->where($bidorder_map)->select();
				//echo $model_bidorder->getLastsql();
				//print_r($bidorder);
				$list[$key]['bidorder_num']=count($bidorder);
				if($bidorder){
					foreach($bidorder as $k=>$v){
						$price['id']=$v['insurance_id'];
						$loss=$model_insurance->where($price)->find();
						$list[$key]['loss_price']=$list[$key]['loss_price']+$loss['loss_price'];
					}
				}else{
					$list[$key]['loss_price']=0;
				}
				//事故车签约
				$contract_bidorder_map['xc_shop.safestate']=1;
				$contract_bidorder_map['xc_bidorder.status']=1;
				$contract_bidorder_map['xc_bidorder.order_status']=array('neq',3);
				$contract_bidorder_map['xc_bidorder.create_time']=array(array('egt',$value['create_time']),array('elt',($value['create_time']+86400)));
				$contract_bidorder=$model_bidorder->join('xc_shop ON xc_bidorder.shop_id = xc_shop.id')->where($contract_bidorder_map)->select();
				//echo $model_bidorder->getLastsql();
				//print_r($contract_bidorder);
				$list[$key]['contract_bidorder_num']=count($contract_bidorder);
				if($contract_bidorder){
					foreach($contract_bidorder as $a=>$b){
						$contract_price['id']=$b['insurance_id'];
						$contract_loss=$model_insurance->where($contract_price)->find();
						//print_r($contract_loss);
						$list[$key]['contract_loss_price']=$list[$key]['contract_loss_price']+$contract_loss['loss_price'];
					}
				}else{
					$list[$key]['contract_loss_price']=0;
				}
				$sum['bidorder_num']=$sum['bidorder_num']+$list[$key]['bidorder_num'];
				$sum['loss_price']=$sum['loss_price']+$list[$key]['loss_price'];
				$sum['contract_bidorder_num']=$sum['contract_bidorder_num']+$list[$key]['contract_bidorder_num'];
				$sum['contract_loss_price']=$sum['contract_loss_price']+$list[$key]['contract_loss_price'];
			}
		}
		//print_r($list);
		//print_r($sum);
        $this->assign('sum', $sum);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();
    }
	
	//新增订单明细
	function order_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_order = D(GROUP_NAME.'/Order');

		$map['create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_order->where($map)->count();
        // 当前页数据查询
        $list = $model_order->where($map)->order('create_time DESC')->select();
		//echo $model_order->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$model_member = D(GROUP_NAME.'/Member');
				$max['uid']=$value['uid'];
				$member = $model_member->where($max)->find();
				$list[$key]['fromstatus'] = $this->get_fromstatus($member['fromstatus']);
	
				$model_shop = D(GROUP_NAME.'/Shop');
				$mad['id']=$value['shop_id'];
				$shop = $model_shop->where($mad)->find();
				$list[$key]['shop_name'] = $shop['shop_name'];

				$model_user = M('tp_admin.user','xc_');
				$data['id'] = $shop['user_id'];
				$user_name = $model_user->where($data)->field('nickname')->find();
				$list[$key]['nickname'] = $user_name['nickname'];
			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}
	//新增商铺明细
	function shop_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_shop = D(GROUP_NAME.'/Shop');

		$map['create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_shop->where($map)->count();
        // 当前页数据查询
        $list = $model_shop->where($map)->order('create_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		echo $model_shop->getLastsql();

		//数据转换
		if (method_exists($this, '_trans_shop_area')) {
			$list = $this->_trans_shop_area($list);
		}

		if($list and is_array($list)){
			foreach($list as $key=>$value){

			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
		$this->assign('shop_class', C('SHOP_CLASS'));
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}
	//新增售出的现金券明细
	function sold_cashcoupon_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');

		$map['xc_membercoupon.create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));
		$map['xc_membercoupon.coupon_type'] = 1;

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_membercoupon->where($map)->count();
        // 当前页数据查询
        $list = $model_membercoupon->where($map)->join("xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->order('xc_membercoupon.create_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_membercoupon->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$shop = $this->GetShopname($value['shop_id']);
				$list[$key]['shop_name'] = $shop['shop_name'];
				$model_order = D('Order');
				$mad['id'] = $value['order_id'];
				$order = $model_order->where($mad)->find();
				$list[$key]['truename'] = $order['truename'];

				$model_user = M('tp_admin.user','xc_');
				$data['id'] = $shop['user_id'];
				$user_name = $model_user->where($data)->field('nickname')->find();
				$list[$key]['nickname'] = $user_name['nickname'];
			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
		$this->assign('shop_class', C('SHOP_CLASS'));
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}

	//新增售出的团购券明细
	function sold_groupcoupon_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');

		$map['xc_membercoupon.create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));
		$map['xc_membercoupon.coupon_type'] = 2;

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_membercoupon->where($map)->count();
        // 当前页数据查询
        $list = $model_membercoupon->where($map)->join("xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->order('xc_membercoupon.create_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_membercoupon->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$shop = $this->GetShopname($value['shop_id']);
				$list[$key]['shop_name'] = $shop['shop_name'];
				$model_order = D('Order');
				$mad['id'] = $value['order_id'];
				$order = $model_order->where($mad)->find();
				$list[$key]['truename'] = $order['truename'];

				$model_user = M('tp_admin.user','xc_');
				$data['id'] = $shop['user_id'];
				$user_name = $model_user->where($data)->field('nickname')->find();
				$list[$key]['nickname'] = $user_name['nickname'];
			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
		$this->assign('shop_class', C('SHOP_CLASS'));
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}

	//新增上架的现金券明细
	function onshelf_cashcoupon_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_coupon = D(GROUP_NAME.'/Coupon');

		$map['xc_coupon.start_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));
		$map['xc_coupon.coupon_type'] = 1;

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_coupon->where($map)->count();
        // 当前页数据查询
        $list = $model_coupon->where($map)->join("xc_membercoupon ON xc_membercoupon.coupon_id=xc_coupon.id")->order('xc_coupon.start_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_coupon->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$shop = $this->GetShopname($value['shop_id']);
				$list[$key]['shop_name'] = $shop['shop_name'];
				$model_order = D('Order');
				$mad['id'] = $value['order_id'];
				$order = $model_order->where($mad)->find();
				$list[$key]['truename'] = $order['truename'];

				$model_user = M('tp_admin.user','xc_');
				$data['id'] = $shop['user_id'];
				$user_name = $model_user->where($data)->field('nickname')->find();
				$list[$key]['nickname'] = $user_name['nickname'];
			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
		$this->assign('shop_class', C('SHOP_CLASS'));
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}

	//新增上架的团购券明细
	function onshelf_groupcoupon_new(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_coupon = D(GROUP_NAME.'/Coupon');

		$map['xc_coupon.start_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));
		$map['xc_coupon.coupon_type'] = 2;

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_coupon->where($map)->count();
        // 当前页数据查询
        $list = $model_coupon->where($map)->join("xc_membercoupon ON xc_membercoupon.coupon_id=xc_coupon.id")->order('xc_coupon.start_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_coupon->getLastsql();

		if($list and is_array($list)){
			foreach($list as $key=>$value){
				$shop = $this->GetShopname($value['shop_id']);
				$list[$key]['shop_name'] = $shop['shop_name'];
				$model_order = D('Order');
				$mad['id'] = $value['order_id'];
				$order = $model_order->where($mad)->find();
				$list[$key]['truename'] = $order['truename'];

				$model_user = M('tp_admin.user','xc_');
				$data['id'] = $shop['user_id'];
				$user_name = $model_user->where($data)->field('nickname')->find();
				$list[$key]['nickname'] = $user_name['nickname'];
			}
		}
		
		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
		$this->assign('shop_class', C('SHOP_CLASS'));
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}

	//新增事故车明细
	function bidorder_detail(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_bidorder = D(GROUP_NAME.'/Bidorder');
		$model_insurance = D(GROUP_NAME.'/Insurance');

		$bidorder_map['xc_shop.safestate']=0;
		$bidorder_map['xc_bidorder.status']=1;
		$bidorder_map['xc_bidorder.order_status']=array('neq',3);
		$bidorder_map['xc_bidorder.create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));

		$list=$model_bidorder->join('xc_shop ON xc_bidorder.shop_id = xc_shop.id')->where($bidorder_map)->field('xc_bidorder.*,xc_shop.shop_name')->select();
		//echo $model_bidorder->getLastsql();
		//print_r($list);
		if($list){
			foreach($list as $k=>$v){
				$price['id']=$v['insurance_id'];
				$loss=$model_insurance->where($price)->find();
				$list[$k]['loss_price']=$loss['loss_price'];
			}
		}else{
			$list[$k]['loss_price']=0;
		}

		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
        $this->assign('list', $list);
        $this->display();

	}

	//新增事故车签约明细
	function bidorder_contract_detail(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_bidorder = D(GROUP_NAME.'/Bidorder');
		$model_insurance = D(GROUP_NAME.'/Insurance');

		$bidorder_map['xc_shop.safestate']=1;
		$bidorder_map['xc_bidorder.status']=1;
		$bidorder_map['xc_bidorder.order_status']=array('neq',3);
		$bidorder_map['xc_bidorder.create_time'] = array(array('egt',strtotime($_REQUEST['time']."0:00:00")),array('elt',strtotime($_REQUEST['time']."23:59:59")));
		
		$list=$model_bidorder->join('xc_shop ON xc_bidorder.shop_id = xc_shop.id')->field('xc_bidorder.*,xc_shop.shop_name')->where($bidorder_map)->select();
		//echo $model_bidorder->getLastsql();
		//print_r($bidorder);
		if($list){
			foreach($list as $k=>$v){
				$price['id']=$v['insurance_id'];
				$loss=$model_insurance->where($price)->find();
				$list[$k]['loss_price']=$list[$k]['loss_price']+$loss['loss_price'];
			}
		}else{
			$list[$k]['loss_price']=0;
		}

		//print_r($list);
		$this->assign('time', $_REQUEST['time']);
        $this->assign('list', $list);
        $this->display();

	}

	/*
		@author:wuwenyu
		@function:得到店铺名
		@time:2014-4-10
	*/
	function GetShopname($shop_id){
		$model_shop = D('Shop');
		$map['id'] = $shop_id;
		$shop = $model_shop->where($map)->find();
		return $shop;
	}

	function _trans_shop_area($list){
		if ($list){
			foreach ($list as $k=>$v){
				$list[$k]['area_name'] = $this->get_area_name($v['shop_area']);
			}
		}
		return $list;
	}
	function get_area_name($shop_area){
		$model_region = D('Region');
		$map['id'] = $shop_area;
		$region = $model_region->where($map)->find();
		$map_1['id'] = $region['parent_id'];
		$region_1 = $model_region->where($map_1)->find();
		return $region_1['region_name'].'-'.$region['region_name'];
	}

	//来源转换数组 wuwenyu
	function get_fromstatus($key){
		$fromstatus_array=array(
			'1'=>'百度/谷歌',
    		'2'=>'论坛/微博',
    		'3'=>'朋友介绍',
    		'4'=>'APP',
			'5'=>'客服电话',
			'6'=>'老用户',
			'7'=>'宣传单',
			'8'=>'其他',
			'9'=>'短信推广',
			'10'=>'vw',
			'11'=>'svw',
			'12'=>'别克',
			'13'=>'雪弗莱',
			'14'=>'斯柯达',
			'15'=>'一汽丰田',
			'16'=>'东风日产',
			'17'=>'起亚',
			'18'=>'优惠',
			'19'=>'th',
			'20'=>'sh',
			'21'=>'都市港湾小区',
			'22'=>'lmm',
			'23'=>'微博',
			'24'=>'彩生活',
			'25'=>'出租车广告',
			'26'=>'传单',
			'27'=>'ch',
			'28'=>'fby',
			'29'=>'小区',
			'30'=>'短信',
			'31'=>'微信',
			'32'=>'百度SEO',
			'33'=>'平安好车',
			'34'=>'平安WAP',
			'35'=>'事故车代下单',
			'36'=>'携车微信',
		);
		if($key){$fromstatus_type=$fromstatus_array[$key];}
		return $fromstatus_type;
	}
	//查询过滤器
	function filter(){
		if($pro_info['year'] and $pro_info['month']){
			$pro_info['month']=intval($pro_info['month']);
			if($pro_info['month']<10){
				$pro_info['start_time']=$pro_info['year']."-0".$pro_info['month']."-1 0:00:00";
			}else{
				$pro_info['start_time']=$pro_info['year']."-".$pro_info['month']."-1 0:00:00";
			}
			$pro_info['end_time']=date('Y-m-d H:i:s',strtotime("+1 month",strtotime($pro_info['start_time'])));
			$this->assign("year", $pro_info['year']);
			$this->assign("month", $pro_info['month']);
			$jicheng['year']=$pro_info['year'];
			$jicheng['month']=$pro_info['month'];
			unset($pro_info['year'],$pro_info['month']);
		}else{
			$pro_info['start_time']=date("Y-m",time())."-1 0:00:00";
			$pro_info['end_time']=date('Y-m-d H:i:s',time());
		}
		return $pro_info;
	}
	
	//跑数据
	function get_shopinfo(){
		$model_dianping_coupon = D('Dianping_coupon');
		$end = strtotime(date('Y-m-d',time()).'0:00:00');
		$start = strtotime("-1 day",$end);
		echo $start.'</br>'.$end;
		$sql="SELECT *
		FROM `xc_dianping_coupon` as a,xc_membercoupon as b
		WHERE 1
		AND a.mobile=b.mobile
		AND a.coupon_id=b.coupon_id
		AND b.use_time >'{$start}'
		AND b.use_time <'{$end}'
		AND a.is_bind = 1
		AND b.is_use = 1
		ORDER BY a.id DESC";
		$info = $model_dianping_coupon->query($sql);
		echo $model_dianping_coupon->getLastsql();
		print_r($info);
		echo '</br>';
		echo "<table>";
		$model_coupon = D('Coupon');
		foreach($info as $k=>$v){
			$map['id'] = $v['coupon_id'];
			$coupon_info = $model_coupon->where($map)->find();
			$v['coupon_name'] = $coupon_info['coupon_name'];
			$v['use_time'] = date('Y-m-d H:i:s',$v['use_time']);
			echo "<tr><td>".$v['coupon_name']."</td><td>".$v['coupon_code']."</td><td>".$v['use_time']."</td></tr>";
		}
		echo "</table>";
	}
}
?>