<?php
//首页
class IndexBackAction extends CommonAction {

	/*
		@author:chf
		@function:携车网主页显示（测试调试备份页面）
		@time:2013-03-21
	*/
    public function index(){
		$area = $_SESSION['area_info'];
		Cookie::set('_currentUrl_', __URL__);
		$model_shop = D('Shop');
		$this->assign('areaname',$area[0]);//匹配地区
		$this->assign('areaflag',$area[1]);//匹配地区
		//资讯列表一
		$model_article = D('Article');
		$region_model = D("Region");
		$region_data['region_name'] = $area[0];
		
		$region_data['region_type'] = 2;
		$region = $region_model->where($region_data)->find();
		$city_id = $region['id'];
		//滚动4S店列表
		$map_sh['status'] = 1;
		$map_sh['shop_city'] = $city_id;
		$Nonlocal['shoplist'] = $model_shop->where($map_sh)->order("id DESC")->limit(20)->select();
		
		if ($Nonlocal['shoplist']){
			$model_shop_fs_relation = D('Shop_fs_relation');
			foreach ($Nonlocal['shoplist'] as $kk=>$vv){
				if (file_exists("/UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$Nonlocal['shoplist'][$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$Nonlocal['shoplist'][$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
			}
		}
		
		if($area[0] == '北京' || $area[0] == '广州') {
			
			//得到相对应地区的4S店数量
			$map_shoptype['status'] = 1;
			$map_shoptype['shop_city'] = $city_id;
			$Nonlocal['shopcount'] = $model_shop->where($map_shoptype)->count();
		
			$this->assign('Nonlocal',$Nonlocal);
			$this->display('NonlocalIndex');
		}else{
			//现金券coupon_type=1
			$model_coupon = D('Coupon');
			$map_coupon1['is_delete'] = 0;
			$map_coupon1['show_s_time'] = array('lt',time());
			$map_coupon1['show_e_time'] = array('gt',time());
			$map_coupon1['coupon_type'] = 1;
			$coupon1 = $model_coupon->where($map_coupon1)->order("id DESC")->select();
			//echo '<pre>';print_r($coupon1);exit;
			$this->assign('coupon1',$coupon1);
			
			//现金券coupon_type=2
			$map_coupon2['is_delete'] = 0;
			$map_coupon2['show_s_time'] = array('lt',time());
			$map_coupon2['show_e_time'] = array('gt',time());
			$map_coupon2['coupon_type'] = 2;
			$coupon2 = $model_coupon->where($map_coupon2)->order("id DESC")->select();
			//echo '<pre>';print_r($coupon);exit;
			$this->assign('coupon2',$coupon2);
			
			
			//快捷信息公告区
			$model_shopnotice = D('Notice');
			$map_notice['status'] = 1;
			$shopnotice = $model_shopnotice->where($map_notice)->order("update_time DESC")->limit(10)->select();
			if ($shopnotice){
				$model_shop = D('Shop');
				foreach ($shopnotice as $_key=>$_val){
					$shop_id = $_val['shop_id'];
					$map_shop['id'] = $shop_id;
					$shop = $model_shop->where($map_shop)->find();
					$shopnotice[$_key]['shopinfo'] = $shop;
				}
			}
			$this->assign('shopnotice',$shopnotice);
			//资讯列表一
			$model_article = D('Article');
			$map_a['status'] = 1;
			$map_a['city_name'] = $area[0];
			$article = $model_article->where($map_a)->order("id DESC")->limit(10)->select();
			//$article_1 = array_slice ($article, 0 ,4);
			$this->assign('article_1',$article);
			
			//品牌推荐
			$model_carbrand = D('Carbrand');
			$model_carseries = D('Carseries');
			$model_carmodel = D('Carmodel');
			$model_id_arr = array(167,11,23,1,5,2,10,29,14,16,17,19,33,12);
			$rand_keys = array_rand ($model_id_arr, 3);
			$model_ids[0] = $model_id_arr[$rand_keys[0]];
			$model_ids[1] = $model_id_arr[$rand_keys[1]];
			$model_ids[2] = $model_id_arr[$rand_keys[2]];
			//echo '<pre>';print_r($model_ids);
			$map_m['model_id'] = array('in',implode(',',$model_ids));
			$model_arr = $model_carmodel->where($map_m)->select();
			if ($model_arr){
				foreach ($model_arr as $k=>$v){
					$series_id = $v['series_id'];
					$map_s['series_id'] = $series_id;
					$series_arr[$k] = $model_carseries->where($map_s)->find();
					$brand_id = $series_arr[$k]['brand_id'];
					$map_b['brand_id'] = $brand_id;
					$brand_arr[$k] = $model_carbrand->where($map_b)->find();
					$fsid = $series_arr[$k]['fsid'];
					$map_f['fsid'] = $fsid;
					$map_f['status'] = 1;
					$article_arr[$k] = $model_article->where($map_f)->select();
					$article_count[$k] = count($article_arr[$k]);
				}
			}
			//echo '<pre>';print_r($article_arr);exit;
			$this->assign('model_arr',$model_arr);
			$this->assign('series_arr',$series_arr);
			$this->assign('brand_arr',$brand_arr);
			$this->assign('article_arr',$article_arr);
			$this->assign('article_count',$article_count);
			
			$model_expert = D('Expert');
			$model_user = D('User');
			//推荐专家列表
			$this->get_expert_tuijian();
			
			//专家列表排名
			$expert = $model_expert->select();
			$uid_arr = array();
			if ($expert){
				foreach ($expert as $_k=>$_v){
					$uid_arr[] = $_v['uid'];
				}
			}
			$uid_str = implode(',',$uid_arr);
			$map_u['uid'] = array('in',$uid_str);
			$expert_user = $model_user->where($map_u)->order("credit1 DESC")->select();
			if ($expert_user){
				foreach ($expert_user as $_kk=>$_vv){
					$expert_user[$_kk]['brand_name'] = $this->getexpertbrand($_vv['uid']); 
				}
			}
			//echo '<pre>';print_r($expert_user);exit;
			$this->assign('expert',$expert_user);
			
			//问答列表
			$model_question = D('Question');
			$question = $model_question->order("id DESC")->limit(2)->select();
			$this->assign('question',$question);
			$model = D('carbrand');
			$brand = $model->order("word")->select();
			//获取商家列表
			$model_shop = D('shop');
			//签约4S店数量
			$map_shoptype1['shop_class'] = 1;
			$map_shoptype1['status'] = 1;
			$map_shoptype1['shop_city'] =$city_id;
			$shop1count = $model_shop->where($map_shoptype1)->count();
			$this->assign('shop1count',$shop1count);
			
			//非签约4S店数量
			$map_shoptype2['status'] = 1;
			$map_shoptype2['shop_class'] = 2;
			$map_shoptype2['shop_city'] =$city_id;
			$shop2count = $model_shop->where($map_shoptype2)->count();
			$this->assign('shop2count',$shop2count);
			$this->assign('brand',$brand);
			$this->assign('shoplist',$Nonlocal['shoplist']);
			$this->display('IndexBack');
		}
		
    }

    public function getexpertbrand($uid){
        $model_expert = D('Expert');
        $model_category = D('Category');
        $map_e['uid'] = $uid;
        $expert = $model_expert->where($map_e)->find();
        $map_c['id'] = $expert['cid'];
        $category = $model_category->where($map_c)->find();
        $map_cp['id'] = $category['pid'];
        $category_p = $model_category->where($map_cp)->find();
        return $category_p['name'];
    }

	
}