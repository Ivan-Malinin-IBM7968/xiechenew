<?php
class Coupon1Action extends CommonAction {//现金券
    function __construct() {
		parent::__construct();
		$this->coupon_logModel = D('coupon_log');//优惠券信息表
	}
	
    public function index(){
        Cookie::set('_currentUrl_', __SELF__);
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $model_shop = D(GROUP_NAME.'/Shop');
        if ($_REQUEST['coupon_type']){
            $map_mc['coupon_type'] = $_REQUEST['coupon_type'];
        }
        $map_mc['is_pay'] = 1;
        $map_mc['is_delete'] = 0;
        if ($_REQUEST['start_time']){
            $map_mc['start_time'] = array('gt',strtotime($_REQUEST['start_time']));
        }
        if ($_REQUEST['end_time']){
            $map_mc['end_time'] = array('lt',strtotime($_REQUEST['end_time']));
        }
        $coupon_state = isset($_REQUEST['coupon_state'])?$_REQUEST['coupon_state']:0;
        if ($coupon_state==1){
            $map_mc['is_use'] = 0;
            $map_mc['order_id'] = 0;
        }
        if ($coupon_state==2){
            $map_mc['is_use'] = 1;
            $map_mc['order_id'] = 0;
        }
        if ($coupon_state==3){
            $map_mc['is_use'] = 1;
            $map_mc['order_id'] = array('gt',0);
        }
        if ($coupon_state==4){
            $map_mc['is_use'] = 0;
            $map_mc['order_id'] = 0;
            $map_mc['is_refund'] = 0;
            $map_mc['end_time'] = array('lt',time());
        }
        if ($coupon_state==5){
            $map_mc['is_refund'] = 1;
        }
        $this->assign('coupon_state',$coupon_state);
        // 计算总数
        $count = $model_membercoupon->where($map_mc)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_membercoupon->where($map_mc)->limit($p->firstRow.','.$p->listRows)->select();
        if ($list){
            foreach ($list as $k=>$v){
                $shop_id = $v['shop_id'];
                $map_s['id'] = $shop_id;
                $shop = $model_shop->where($map_s)->find();
                $list[$k]['shop'] = $shop;
            }
        }
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->assign('now', time());
        $this->assign('coupon_type', $_REQUEST['coupon_type']);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();
    }
    public function orders(){
        $model_membercoupon = D('Membercoupon');
        $membercoupon_id = $_REQUEST['membercoupon_id'];
        $map_mc['membercoupon_id'] = $membercoupon_id;
        $membercoupon = $model_membercoupon->where($map_mc)->find();
        $coupon_id = $membercoupon['coupon_id'];
        $model_coupon = D('Coupon');
        $map_c['id'] = $coupon_id;
        $coupon = $model_coupon->where($map_c)->find();
        $shop_id = $coupon['shop_id'];
        $model_shop = D('Shop');
        $map_s['id'] = $shop_id;
        $shop = $model_shop->where($map_s)->find();
        $uid = $membercoupon['uid'];
        $model_order = D('Order');
        $map_o['uid'] = $uid;
        $map_o['order_state'] = 2;
		if($membercoupon['coupon_type'] == '2'){
		
		
		 $map_o['model_id'] = $coupon['model_id'];
		}
        $orders = $model_order->where($map_o)->select();
		
        if($orders){
            foreach ($orders as $k=>$v){
                $map_m['order_id'] = $v['id'];
                $membercoupons = array();
                $membercoupons = $model_membercoupon->where($map_m)->select();
                $orders[$k]['membercoupons'] = $membercoupons;
            }
        }
        //echo '<pre>';print_r($orders);
        $this->assign('orderlist',$orders);
        $this->assign('membercoupon',$membercoupon);
        $this->assign('coupon',$coupon);
        $this->assign('shop',$shop);
        $this->display();
    }
    public function showmembercoupon(){
        $model_membercoupon = D('Membercoupon');
        $membercoupon_id = $_REQUEST['membercoupon_id'];
        $map_mc['membercoupon_id'] = $membercoupon_id;
        $membercoupon = $model_membercoupon->where($map_mc)->find();
        $coupon_id = $membercoupon['coupon_id'];
        $model_coupon = D('Coupon');
        $map_c['id'] = $coupon_id;
        $coupon = $model_coupon->where($map_c)->find();
        $shop_id = $coupon['shop_id'];
        $model_shop = D('Shop');
        $map_s['id'] = $shop_id;
        $shop = $model_shop->where($map_s)->find();
        $uid = $membercoupon['uid'];
        $model_order = D('Order');
        //$map_o['uid'] = $uid;
        //$map_o['order_state'] = 2;
        //$map_o['model_id'] = $coupon['model_id'];
        //$orders = $model_order->where($map_o)->select();
        
        //$this->assign('orderlist',$orders);
        $this->assign('membercoupon',$membercoupon);
        $this->assign('coupon',$coupon);
        $this->assign('shop',$shop);
        $this->display();
    }
    public function saveorder(){
        $order_id = isset($_REQUEST['order_id'])?$_REQUEST['order_id']:0;
        $membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:0;
        if ($membercoupon_id){
            $model_membercoupon = D('Membercoupon');
            $map_mc['membercoupon_id'] = $membercoupon_id;
			//记录客服优惠券绑定
			$coupon_log['name'] = $_SESSION['loginAdminUserName'];//记录用户名
			$coupon_log['ip'] = $_SERVER["REMOTE_ADDR"];//记录ip
			$coupon_log['membercoupon_id'] = $membercoupon_id;//订单号
			$coupon_log['create_time'] = time();//创建时间	
			$coupon_log['content'] = '客服优惠券绑定';//操作内容
			$this->coupon_logModel->add($coupon_log);	

            if ($order_id){
                $data['order_id'] = $order_id;
                if ($model_membercoupon->where($map_mc)->save($data)){
				
					
                    $this->success("绑定成功");
                }else{
                    $this->error("绑定失败");
                }
            }else{
                $model_order = D('Order');
                $membercoupon = $model_membercoupon->where($map_mc)->find();
                $model_coupon = D('Coupon');
                $coupon_id = $membercoupon['coupon_id'];
                $map_c['id'] = $coupon_id;
                $coupon = $model_coupon->where($map_c)->find();
                $data['order_name'] = $membercoupon['coupon_name'];
                $data['uid'] = $membercoupon['uid'];
                $data['shop_id'] = $membercoupon['shop_id'];
                $data['brand_id'] = $coupon['brand_id'];
                $data['series_id'] = $coupon['series_id'];
                $data['model_id'] = $coupon['model_id'];
                $data['service_ids'] = $coupon['service_ids'];
                $data['mobile'] = $membercoupon['mobile'];
                $data['total_price'] = $coupon['coupon_amount'];
                $data['cost_price'] = $coupon['cost_price'];
                $data['jiesuan_money'] = $coupon['jiesuan_money'];
                $data['order_time'] = $membercoupon['use_time'];
                $data['order_state'] = 2;
                $data['create_time'] = time();
                $data['complete_time'] = time();
                $data['order_type'] = $membercoupon['coupon_type'];
                if ($order_id = $model_order->add($data)){
                    $data_mc['order_id'] = $order_id;
                    $model_membercoupon->where($map_mc)->save($data_mc);
                    $this->success("订单生成成功");
                }else{
                    $this->error("订单生成失败");
                }
            }
        }
    }
}
?>