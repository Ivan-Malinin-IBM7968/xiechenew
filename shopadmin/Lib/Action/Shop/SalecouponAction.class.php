<?php
/*
 */
class SalecouponAction extends CommonAction {
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
		$model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
        $coupon_code = isset($_REQUEST['coupon_code'])?$_REQUEST['coupon_code']:0;
        if ($coupon_code){
            $map_mc['xc_membersalecoupon.coupon_code'] = $coupon_code;
            $map_mc['xc_membersalecoupon.shop_id'] = '';
			$map_mc['_string'] = "FIND_IN_SET('{$shop_id}', xc_membersalecoupon.shop_ids) OR FIND_IN_SET('{$shop_id}', xc_salecoupon.shop_ids)";
			$map_mc['xc_salecoupon.is_delete'] = 0;
            $map_mc['xc_membersalecoupon.is_delete'] = 0;
            // 计算总数
            $count = $model_membersalecoupon->where($map_mc)->join("xc_salecoupon ON xc_membersalecoupon.salecoupon_id=xc_salecoupon.id")->count();
			
			
            // 导入分页类
            import("ORG.Util.Page");
            // 实例化分页类
            $p = new Page($count, 10);
            // 分页显示输出
            $page = $p->show_admin();
        
            $membersaleconpon = $model_membersalecoupon->where($map_mc)->join("xc_salecoupon ON xc_membersalecoupon.salecoupon_id=xc_salecoupon.id")->limit($p->firstRow.','.$p->listRows)->select();
            $this->assign('membersaleconpon',$membersaleconpon);

			$this->assign('coupon_code',$coupon_code);
			$this->assign('licenseplate_str',$licenseplate_str);
			$this->assign('licenseplate',$licenseplate);
			$this->assign('licenseplate_title',$licenseplate_title);
            $this->assign('now',time());
            $this->assign('page',$page);
        }
        $this->display();
    }
    
    public function use_salecoupon(){//使用抵用券
        $shop_id = $_SESSION['shop_id'];
        $model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
        $membersalecoupon_id = isset($_REQUEST['membersalecoupon_id'])?$_REQUEST['membersalecoupon_id']:0;
		$licenseplate = $_REQUEST['licenseplate_str'];

        if ($membersalecoupon_id){
            $map_mc['shop_id'] = '';
            $map_mc['membersalecoupon_id'] = $membersalecoupon_id;
            $map_mc['is_delete'] = 0;
            $map_mc['is_use'] = 0;
			$map_mc['order_id'] = array('neq',0);
			$map_mc['start_time'] = array('lt',time());
            $map_mc['end_time'] = array('gt',time());
            $membersalecoupon = $model_membersalecoupon->where($map_mc)->find();
            if ($membersalecoupon){
				$map_mcoupon['membersalecoupon_id'] = $membersalecoupon_id;
				$data['shop_id'] = $shop_id;
				$data['is_use'] = 1;
				$data['use_time'] = time();
				$data['licenseplate'] = $licenseplate;
				if($model_membersalecoupon->where($map_mcoupon)->save($data)){
					$number = substr($membersalecoupon['coupon_code'],6);
					$time = date('m月d日H时i分');
					$model_shop = D('Shop');
					$map_s['id'] = $shop_id;
					$shop = $model_shop->where($map_s)->find();
					$address = $shop['shop_address'];
					$shop_name = $shop['shop_name'];
					$send_data = array(
						'phones'=>$membersalecoupon['mobile'],
						'content'=>'您尾号'.$number.'的'.$membersalecoupon['coupon_name'].'抵用券于'.$time.'在'.$shop_name.'('.$address.')被使用',	
					);
					//dump($send_add_order_data);
					$return_data = curl_sms($send_data);
					
					$shop_str = "套餐券: <font color=red>".$membersalecoupon['coupon_name']."</font> 使用成功！";

					$model_sms = D('Sms');
					$send_data['sendtime'] = time();
					$model_sms->add($send_data);

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