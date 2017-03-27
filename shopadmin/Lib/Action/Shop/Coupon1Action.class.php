<?php
/*
 */
class Coupon1Action extends CommonAction {
    function __construct() {
		parent::__construct();
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
	}
    public function index(){//现金券
        $shop_id = $_SESSION['shop_id'];
		if($_REQUEST['licenseplate'] !='000000'){
			$licenseplate_title = $_REQUEST['licenseplate_title'];
			$licenseplate = $_REQUEST['licenseplate'];
			$licenseplate_str = $licenseplate_title.$_REQUEST['licenseplate'];
		}
		
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $coupon_code_arr = isset($_REQUEST['coupon_code'])?$_REQUEST['coupon_code']:array();
        if ($coupon_code_arr){
            foreach ($coupon_code_arr as $k=>$v){
                if (!$v || !intval($v)){
                    unset($coupon_code_arr[$k]);
                }else{
                    $memberconpon_id_arr[] = $v;
                }
            }
            if($memberconpon_id_arr){
                //$map_mc['shop_id'] = $shop_id;
                $map_mc['coupon_type'] = 1;
                $map_mc['is_delete'] = 0;
				$map_mc['is_refund'] = 0;
				$map_mc['_string'] = "FIND_IN_SET('{$shop_id}', shop_ids)";

                $memberconpon_id_str = implode(',',$memberconpon_id_arr);
                $have_code_arr = array();
                foreach ($memberconpon_id_arr as $key=>$val){
                    $map_mc['coupon_code'] = $val;
                    $memcoupon = $model_membercoupon->where($map_mc)->find();
					//echo $model_membercoupon->getLastsql();
                    if ($memcoupon){
                        $memberconpon[$key] = $memcoupon;
                    }else{
                        $memberconpon[$key]['coupon_code'] = $val;
                        $memberconpon[$key]['membercoupon_id'] = 0;
                    }
                    if (in_array($val,$have_code_arr)){
                        $memberconpon[$key]['have_code'] = 1;
                    }else{
                        $memberconpon[$key]['have_code'] = 0;
                    }
                    $have_code_arr[] = $val;
                }
            }
            if ($memberconpon){
                foreach ($memberconpon as $kk=>$vv){
                    if ($vv['membercoupon_id']>0 and $vv['have_code']===0 and $vv['is_pay']==1 and $vv['is_use']==0 and $vv['end_time']>time()){
                        $memberconpon_arr[] = $vv['membercoupon_id'];
                    }
                }
                $memberconpon_str = implode(',',$memberconpon_arr);
            }
            $this->assign('memberconpon_id_str',$memberconpon_str);
            $this->assign('memberconpon',$memberconpon);
			$this->assign('licenseplate_str',$licenseplate_str);
			$this->assign('licenseplate',$licenseplate);
			$this->assign('licenseplate_title',$licenseplate_title);
			$this->assign('now',time());
            $this->assign('coupon_code',$coupon_code_arr);
        }
        $this->display();
    }
    
    public function use_coupon1(){//使用现金券
        $shop_id = $_SESSION['shop_id'];
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
		$model_coupon = D(GROUP_NAME.'/Coupon');
        $membercoupon_ids = isset($_REQUEST['membercoupon_ids'])?$_REQUEST['membercoupon_ids']:0;
		$licenseplate = $_REQUEST['licenseplate_str'];
		//print_r($membercoupon_ids);
        if ($membercoupon_ids){
            //$map_mc['shop_id'] = $shop_id;
			$map_mc['_string'] = "FIND_IN_SET('{$shop_id}', shop_ids)";
            $map_mc['membercoupon_id'] = array('in',$membercoupon_ids);
            $map_mc['coupon_type'] = 1;
            $map_mc['is_delete'] = 0;
            $map_mc['is_pay'] = 1;
            $map_mc['is_use'] = 0;
			$map_mc['is_refund'] = 0;
            $map_mc['end_time'] = array('gt',time());
            $membercoupons = $model_membercoupon->where($map_mc)->select();
            $model_shop = D('Shop');
            $map_s['id'] = $shop_id;
            $shop = $model_shop->where($map_s)->find();
            $address = $shop['shop_address'];
            $shop_name = $shop['shop_name'];
            $is_do = 0;
            if ($membercoupons){
                foreach ($membercoupons as $k=>$v){
                    if ($v['is_pay']==1 and $v['is_use']==0 and $v['end_time']>time()){

						if($v['coupon_type']=='1'){
							$data['ratio'] = $shop['cash_rebate'];//现金券比例
						}else{
							$data['ratio'] = $shop['group_rebate'];
						}

                        $map_mcoupon['membercoupon_id'] = $v['membercoupon_id'];
                        $data['is_use'] = 1;
                        $data['use_time'] = time();
						$data['licenseplate'] = $licenseplate;
						$data['shop_id'] = $shop_id;

                        if($model_membercoupon->where($map_mcoupon)->save($data)){
                            $is_do = 1;
                            $number = substr($v['coupon_code'],6);
                            $time = date('m月d日H时i分');
                            $send_data = array(
                				'phones'=>$v['mobile'],
                				'content'=>'您尾号'.$number.'的'.$v['coupon_name'].'现金券于'.$time.'在'.$shop_name.'('.$address.')被使用',	
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

							$weixin_map['uid'] = $v['uid'];
							$weixin_map['mobile'] = $v['mobile'];
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

							$coupon_info = $model_coupon->find($v['coupon_id']);
							$shop_str_membercoupon .= $v['coupon_name']." 售价:￥".$coupon_info['coupon_amount']." ,";
							$count_amount += $coupon_info['coupon_amount'];
                        }
                    }
                }
            }else{
                $this->error('操作失败！');
            }
				
            if ($is_do == 1){
				$shop_str_membercoupon = substr($shop_str_membercoupon,0,-1);
               	$shop_str = "现金券: <font color=red>".$shop_str_membercoupon."</font> 使用成功！<br/>总计:￥".$count_amount;
				$this->success("</br>{$shop_str}");
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->error('没有可使用的券！');
        }
    }
    
    
}