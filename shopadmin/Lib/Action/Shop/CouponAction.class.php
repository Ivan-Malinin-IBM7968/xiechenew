<?php
/*
 */
class CouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
	}
    public function index(){//套餐券
        $shop_id = $_SESSION['shop_id'];
		if($_REQUEST['licenseplate']!='000000' && $_REQUEST['licenseplate']){
			$licenseplate_title = $_REQUEST['licenseplate_title'];
			$licenseplate = $_REQUEST['licenseplate'];
			$licenseplate_str = $licenseplate_title.$_REQUEST['licenseplate'];
		}
		$model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $coupon_code = $_REQUEST['coupon_code'];
		if( intval($coupon_code) == 0 && isset($coupon_code)) {
			$this->error("验证码输入错误");
		}
        //$membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:0;
        if ($coupon_code){
            //$map_mc['membercoupon_id'] = $membercoupon_id;
            $map_mc['coupon_code'] = intval($coupon_code);
            //$map_mc['shop_id'] = $shop_id;
            $map_mc['coupon_type'] = 2;
            $map_mc['is_delete'] = 0;
			$map_mc['is_refund'] = 0;
			$map_mc['_string'] = "FIND_IN_SET('{$shop_id}', shop_ids)";


            /* 计算总数
            $count = $model_membercoupon->where($map_mc)->count();
            // 导入分页类
            import("ORG.Util.Page");
            // 实例化分页类
            $p = new Page($count, 10);
            // 分页显示输出
            $page = $p->show_admin();
			*/
            $memberconpon = $model_membercoupon->where($map_mc)->select();
			//echo $model_membercoupon->getlastSql();
            $this->assign('memberconpon',$memberconpon);
            //$this->assign('membercoupon_id',$membercoupon_id);
            $this->assign('coupon_code',$coupon_code);
			$this->assign('licenseplate_str',$licenseplate_str);
			$this->assign('licenseplate',$licenseplate);
			$this->assign('licenseplate_title',$licenseplate_title);
            $this->assign('now',time());
            //$this->assign('page',$page);
        }
        $this->display();
    }
    
    public function use_coupon2(){//使用套餐券
        $shop_id = $_SESSION['shop_id'];
		$model_shop = D('Shop');
		$map_s['id'] = $shop_id;
		$shop = $model_shop->where($map_s)->find();

        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $membercoupon_id = isset($_REQUEST['membercoupon_id'])?$_REQUEST['membercoupon_id']:0;
		$licenseplate = $_REQUEST['licenseplate_str'];

        if ($membercoupon_id){
             //$map_mc['shop_id'] = $shop_id;
			$map_mc['_string'] = "FIND_IN_SET('{$shop_id}', shop_ids)";
            $map_mc['membercoupon_id'] = $membercoupon_id;
            $map_mc['coupon_type'] = 2;
            $map_mc['is_delete'] = 0;
            $map_mc['is_pay'] = 1;
            $map_mc['is_use'] = 0;
			$map_mc['is_refund'] = 0;
            $map_mc['end_time'] = array('gt',time());
            $membercoupon = $model_membercoupon->where($map_mc)->find();
            if ($membercoupon){

				if($membercoupon['coupon_type']=='1'){
					$data['ratio'] = $shop['cash_rebate'];//现金券比例
				}else{
					$data['ratio'] = $shop['group_rebate'];
				}

				$map_mcoupon['membercoupon_id'] = $membercoupon_id;
				$data['is_use'] = 1;
				$data['use_time'] = time();
				$data['licenseplate'] = $licenseplate;
				$data['shop_id'] = $shop_id;
				if($model_membercoupon->where($map_mcoupon)->save($data)){
					$number = substr($membercoupon['coupon_code'],6);
					$time = date('m月d日H时i分');
					
					$address = $shop['shop_address'];
					$shop_name = $shop['shop_name'];
					$send_data = array(
						'phones'=>$membercoupon['mobile'],
						'content'=>'您尾号'.$number.'的'.$membercoupon['coupon_name'].'套餐券于'.$time.'在'.$shop_name.'('.$address.')被使用',	
					);
					//dump($send_add_order_data);
					$return_data = curl_sms($send_data);

					$model_sms = D(GROUP_NAME.'/Sms');
					$now = time();
					$send_data['sendtime'] = $now;
					$model_sms->add($send_data);

					//微信公众号 推送消费信息----------start
					$weixin_model = D('Paweixin');
					$padata_model = D('Padatatest');

					$weixin_map['uid'] = $membercoupon['uid'];
					$weixin_map['mobile'] = $membercoupon['mobile'];
					$weixin_map['type'] = 2;
					$weixin = $weixin_model->where($weixin_map)->find();

					if($weixin) {
						$padata_map['FromUserName'] = $weixin['wx_id'];
						$padata_map['type'] = 2;
						$FromUserName = $padata_model->where($padata_map)->find();

						$weixin_data['touser'] = $weixin['wx_id'];
						$weixin_data['title'] = "优惠券已使用通知";
						$weixin_data['description'] = $send_data['content'];
						$weixin_data['url'] = "http://www.xieche.com.cn/Mobile-my_coupon-pa_id-{$FromUserName[id]}";
						
						$this->weixin_api($weixin_data);
					}
					//微信公众号 推送消费信息----------end

					$shop_str = "套餐券: <font color=red>".$membercoupon['coupon_name']."</font> 使用成功！";
					$this->success("</br>{$shop_str}");
				}else{
					$this->error('操作失败');
				}
            }else {
				$this->error('操作失败');
            }
        }
    }

}