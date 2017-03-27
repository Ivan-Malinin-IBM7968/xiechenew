<?php
//首页
class IndexAction extends CommonAction {
	public function __construct() {
		parent::__construct();
		$this->IpcountModel = D('ipcount');//记录访问IP
		$this->RegionModel = D('region');//区域
		$this->ShopModel = D('shop');//商铺
		$this->CouponModel = D('coupon');//优惠券
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->CarbrandModel = D('carbrand');//
		$this->BaiduModel = D('baidu');//百度表
		$this->MembersalecouponModel = D('membersalecoupon');//抵用券购买表
		$this->CommentModel = D('comment');//评论表
	}

	/*
		@author:chf
		@function:携车网车会引导页
		@time:2013-09-02
	*/
	function ch(){
		/*套餐券*/
		//现金券 23,24,25,46,51,60,61,75
		$Membersalecount = $this->MembersalecouponModel->where(array('salecoupon_id'=>1))->count();
		$group['id'] = array('in','66,64,65,55,53,52,42,41,21');
		$data['GROUP'] = $this->CouponModel->where($group)->select();
		foreach($data['GROUP'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['GROUP'][$k]['shop_name'] = $shop['shop_name'];
			$data['GROUP'][$k]['shop_address'] = $shop['shop_address'];
		}

		$PRICE['id'] = array('in','24,25,61,46,51,60');
		$data['PRICE'] = $this->CouponModel->where($PRICE)->select();
		foreach($data['PRICE'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['PRICE'][$k]['shop_name'] = $shop['shop_name'];
			$data['PRICE'][$k]['shop_address'] = $shop['shop_address'];
		}
		if($_REQUEST['wd']){
			$BD['wd'] = $_REQUEST['wd'];
		}
		$word = $this->search_word_from();
		$BD['wd'] = $word['keyword'];
		$BD['from'] = $word['from'];
		$BD['ip'] = $_SERVER["REMOTE_ADDR"];
		$BD['create_time'] = time();
		$id = $this->BaiduModel->add($BD);
		setcookie("Baidu_id",$id,time()+2592000);
		$this->assign('data',$data);
		$this->assign('Membersalecount',$Membersalecount);
		$this->display();
	}


	/*
		@author:chf
		@function:携车网车会引导页
		@time:2013-09-02
	*/
	function advert(){
		/*套餐券*/
		//现金券 23,24,25,46,51,60,61,75
		$Membersalecount = $this->MembersalecouponModel->where(array('salecoupon_id'=>1))->count();
		$group['id'] = array('in','66,64,65,55,53,52,42,41,21');
		$data['GROUP'] = $this->CouponModel->where($group)->select();
		foreach($data['GROUP'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['GROUP'][$k]['shop_name'] = $shop['shop_name'];
			$data['GROUP'][$k]['shop_address'] = $shop['shop_address'];
		}

		$PRICE['id'] = array('in','24,25,61,46,51,60');
		$data['PRICE'] = $this->CouponModel->where($PRICE)->select();
		foreach($data['PRICE'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['PRICE'][$k]['shop_name'] = $shop['shop_name'];
			$data['PRICE'][$k]['shop_address'] = $shop['shop_address'];
		}
		if($_REQUEST['wd']){
			$BD['wd'] = $_REQUEST['wd'];
		}
		$word = $this->search_word_from();
		$BD['wd'] = $word['keyword'];
		$BD['from'] = $word['from'];
		$BD['ip'] = $_SERVER["REMOTE_ADDR"];
		$BD['create_time'] = time();
		$id = $this->BaiduModel->add($BD);
		setcookie("Baidu_id",$id,time()+2592000);
		$this->assign('data',$data);
		$this->assign('Membersalecount',$Membersalecount);
		$this->display();
	}

	/*
		@author:chf
		@funtion:截取关键字
		@time:2013-09-18
	*/
	function search_word_from() {
		$referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		if(strstr( $referer, 'baidu.com')){ //百度
			preg_match( "|baidu.+wo?r?d=([^\&]*)|is", $referer, $tmp );
			$keyword = urldecode( $tmp[1] );
			$from = 'baidu';
		}elseif(strstr( $referer, 'google.com') or strstr( $referer, 'google.cn')){ //谷歌
			preg_match( "|google.+q=([^\&]*)|is", $referer, $tmp );
			$keyword = urldecode( $tmp[1] );
			$from = 'google';
		}elseif(strstr( $referer, 'so.com')){ //360搜索
			preg_match( "|so.+q=([^\&]*)|is", $referer, $tmp );
			$keyword = urldecode( $tmp[1] );
			$from = '360';
		}elseif(strstr( $referer, 'sogou.com')){ //搜狗
			preg_match( "|sogou.com.+query=([^\&]*)|is", $referer, $tmp );
			$keyword = urldecode( $tmp[1] );
			$from = 'sogou';
		}elseif(strstr( $referer, 'soso.com')){ //搜搜
			preg_match( "|soso.com.+w=([^\&]*)|is", $referer, $tmp );
			$keyword = urldecode( $tmp[1] );
			$from = 'soso';
		}else {
			$keyword ='';
			$from = '';
		}
		return array('keyword'=>$keyword,'from'=>$from);
	}


	/*
		@author:chf
		@function:携车网主页显示 (新)
		@time:2013-09-6
	*/
	  public function index(){
		if(TOP_CSS == "pa") {
			header("Location:http://baoyang.pahaoche.com/order");exit;
		}
		Cookie::set('_currentUrl_', __URL__);
		$this->assign('areaname',$this->city_name);//匹配地区
		//滚动4S店列表
		$model_shop = D('Shop');
		$model_shop_fs_relation = D('Shop_fs_relation');

		if($this->city_name == "上海" ) {
			$map_sh['status'] = 1;
			$map_sh['shop_class'] = 1;
		}
		$map_sh['shop_city'] = $this->city_id;
		$shoplist = $model_shop->where($map_sh)->order("comment_number DESC")->limit(6)->select();
		$shop_count = $model_shop->where($map_sh)->count();
		$this->assign('shop_count',$shop_count);
		if ($shoplist){
			foreach ($shoplist as $kk=>$vv){
				$shoplist[$kk]['comment_number'] = $this->CommentModel->where(array('shop_id'=>$vv['id']))->count();
				if (file_exists("UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
				$Timesale = $this->TimesaleModel->where(array('shop_id'=>$vv['id'],'status'=>1))->find();
				$Timesaleversion = $this->TimesaleversionModel->where(array('timesale_id'=>$Timesale['id'],'status'=>1))->find();
				$shoplist[$kk]['workhours_sale'] = $Timesaleversion['workhours_sale']*10;
			}
		}

		/*按照fsid来筛选店铺*/
		$fs_shoplist = array(
			array('fsid'=>18,'fs_name'=>'上海大众'),
			array('fsid'=>61,'fs_name'=>'斯柯达'),
			array('fsid'=>67,'fs_name'=>'雪佛兰'),
			array('fsid'=>12,'fs_name'=>'别克'),
		);
		foreach($fs_shoplist as $key=>$val) {
			$fs_map['fsid']=$val['fsid'];
			$shop_fs_arr = $model_shop_fs_relation->where($fs_map)->select();
			foreach($shop_fs_arr as $fs_k=>$fs_v) {
				$shop_id_arr[] = $fs_v['shopid'];
			}

			$map_shop['status'] = 1;
			$map_shop['shop_class'] = 1;
			$map_shop['id'] = array('in',$shop_id_arr);

			$shopfslist = $model_shop->where($map_shop)->order("comment_number DESC")->limit(6)->select();
			foreach($shopfslist as $shop_k=>$shop_v) {
				if (file_exists("UPLOADS/Shop/130/".$shop_v['id'].".jpg")){
					$shopfslist[$shop_k]['shop_pic'] = "/UPLOADS/Shop/130/".$shop_v['id'].".jpg";
				}else {
					$shopfslist[$shop_k]['shop_pic'] = "/UPLOADS/Brand/130/".$val['fsid'].".jpg";
				}
				$Timesale = $this->TimesaleModel->where(array('shop_id'=>$shop_v['id'],'status'=>1))->find();
				$Timesaleversion = $this->TimesaleversionModel->where(array('timesale_id'=>$Timesale['id'],'status'=>1))->find();
				$shopfslist[$shop_k]['workhours_sale'] = $Timesaleversion['workhours_sale']*10;
			}
			$fs_shoplist[$key]['shoplist'] = $shopfslist;
			unset($shop_id_arr);
		}
		$this->assign('fs_shoplist',$fs_shoplist);


		//推荐优惠券

		//现金券coupon_type=1
		$data_Pcoupon = $this->return_couponlist('Pcoupon','1');
		$data_Gcoupon = $this->return_couponlist('Gcoupon','2');
		$this->assign('data_Pcoupon',$data_Pcoupon);
		$this->assign('data_Gcoupon',$data_Gcoupon);
		$data_AllPcoupon = $this->return_Allcouponlist('1');
		$data_AllGcoupon = $this->return_Allcouponlist('2');

		$this->assign('data_AllPcoupon',$data_AllPcoupon);
		$this->assign('data_AllGcoupon',$data_AllGcoupon);


		//快捷信息公告区
		/*$model_shopnotice = D('Notice');
		$map_notice['status'] = 1;
		$map_notice['city_name'] = $this->city_name;
		if($this->city_name == '上海') {
			$shopnotice = $model_shopnotice->where($map_notice)->order("update_time DESC")->limit(15)->select();
		}
		if ($shopnotice){
			$model_shop = D('Shop');
			foreach ($shopnotice as $_key=>$_val){
				$shop_id = $_val['shop_id'];
				$map_shop['id'] = $shop_id;
				$shop = $model_shop->where($map_shop)->find();
				$shopnotice[$_key]['shopinfo'] = $shop;
			}
		}
		$this->assign('shopnotice',$shopnotice);*/

		//府上养车专题区
		$model_article = D('Article');
		$map_b['status'] = 1;
		$map_b['type'] = 2;
		$map_b['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		if($this->city_name == "上海") {
			$shopnotice = $model_article->where($map_b)->order("create_time DESC")->limit(15)->select();
		}else {
			$shopnotice = $model_article->where($map_b)->order("create_time DESC")->limit(20)->select();
		}
		$this->assign('shopnotice',$shopnotice);

		//资讯列表一
		$model_article = D('Article');
		$map_a['status'] = 1;
		$map_a['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		if($this->city_name == "上海") {
			$articlelist = $model_article->where($map_a)->order("create_time DESC")->limit(15)->select();
		}else {
			$articlelist = $model_article->where($map_a)->order("create_time DESC")->limit(20)->select();
		}

		//查询用户所有自定义车型
		$model_membercar = D('Membercar');
		if($uid = $this->GetUserId()){
    		$list_membercar = $model_membercar->where("uid=$uid AND status=1")->select();
    		//用户所有自定义车型初始化
			$list_membercar = $model_membercar->Membercar_format_by_arr($list_membercar);
		    $this->assign('uid',$uid);
		    $this->assign('list_membercar',$list_membercar);

			$model_order = D('Order');
			$member_map['uid'] = $uid;
			$member_map['u_c_id'] = array('neq',0);
			$member_order = $model_order->where($member_map)->order('id DESC')->find();
			$this->assign('u_c_id',$member_order['u_c_id']);
		}

		$this->assign('articlelist',$articlelist);

		$this->assign('shoplist',$shoplist);
		$this->assign('title',"携车网-上海汽车上门保养,4S店折扣优惠,事故车维修预约");
		$this->assign('meta_keyword',"上门保养,上海上门保养,汽车上门保养,汽车保养,汽车维修,4S店预约保养,事故车维修");
		$this->assign('description',"携车网:让养车省钱又省事的汽车售后服务网站.【府上养车】99元享4S品质上门汽车保养服务,机油配件京东同价.【4S店折扣】预约工时优 惠:【维修返利】出险事故车预约维修返利");
		$canonical = "http://www.xieche.com.cn";
		$this->assign('canonical',$canonical);

		if($this->city_name == '上海') {
			$map_shoptype1['status'] = 1;
		}
		$map_shoptype1['shop_class'] = 1;
		$map_shoptype1['shop_city'] =$this->city_id;
		$shop1count = $model_shop->where($map_shoptype1)->count();
		$this->assign('shop1count',$shop1count);

		//非签约4S店数量
		if($this->city_name == '上海') {
			$map_shoptype2['status'] = 1;
		}
		$map_shoptype2['shop_class'] = 2;
		$map_shoptype2['shop_city'] =$this->city_id;
		$shop2count = $model_shop->where($map_shoptype2)->count();
		$this->assign('shop2count',$shop2count);
		$this->display();
    }

	/*
		@author:chf
		@function:查找相关品牌相关优惠卷(如改品牌没满6个加入推荐现金券或团购券)
		@time:2013-09-08
	*/
	function return_couponlist($arrayname,$coupon_type){
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = $coupon_type;
		$brand_list = $this->CouponModel->where($Coupon_map)->field('coupon_des',true)->group('brand_id')->order("brand_id ASC")->select();

		foreach($brand_list as $key=>$val) {
			$brand_id_str .= $val['brand_id'].",";
		}
		$brand_id_str = substr($brand_id_str,0,-1);
		$brand_id_arr = explode(",",$brand_id_str);
		$brand_id_arr = array_unique($brand_id_arr);

		

		foreach($brand_id_arr as $C_k=>$C_v){

			$Carbrand = $this->CarbrandModel->where(array('brand_id'=>$C_v))->find();

			$data['Coupon'][$C_k]['brand_id'] = $Carbrand['brand_id'];
			$data['Coupon'][$C_k]['brand_name'] = $Carbrand['brand_name'];
			$Pricecoupon['is_delete'] = 0;
			$Pricecoupon['show_s_time'] = array('lt',time());
			$Pricecoupon['show_e_time'] = array('gt',time());
			$Pricecoupon['coupon_type'] = $coupon_type;
			$Pricecoupon['_string'] =  "FIND_IN_SET('{$C_v}', brand_id)";
			$data['Coupon'][$C_k][$arrayname] = $this->CouponModel->where($Pricecoupon)->field('coupon_des',true)->order("id DESC")->limit('0,6')->select();

			foreach($data['Coupon'][$C_k][$arrayname] as $k=>$v){
				$coupon_id[] = $v['id'];
			}

			$count = count($data['Coupon'][$C_k][$arrayname]);
			$need_tuijian_count = 6-$count;
			if($need_tuijian_count > 0 ) {
				unset($Pricecoupon['_string'] );
				if( $coupon_type == 1 ) {
					
					$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
				}else {
					$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
					//$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
				}
				$Pricecoupon['id'] = array('in',implode(',',$tuijian_coupon_ids));
				
				$Pricecoupon['id'] = array('not in',$coupon_id);
				$tuijian_coupons = $this->CouponModel->where($Pricecoupon)->field('coupon_des',true)->order("pay_count DESC")->limit($need_tuijian_count)->select();
				if($_SESSION['username'] == 'z'){
					//echo $this->CouponModel->getlastSql();
				}
				unset($coupon_id);unset($Pricecoupon);
				foreach($tuijian_coupons as $tuijian_key=>$tuijian_val) {
					$tuijian_val['is_tuijian'] = 1;
					$data['Coupon'][$C_k][$arrayname][] = $tuijian_val;

				}

			}

			foreach($data['Coupon'][$C_k][$arrayname] as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Coupon'][$C_k][$arrayname][$k]['shop_name'] = $shop['shop_name'];
				$data['Coupon'][$C_k][$arrayname][$k]['shop_address'] = $shop['shop_address'];
			}
		}
		return $data;
	}

	/*
		@author:chf
		@function:查找相关品牌相关优惠卷
		@time:2013-09-08
	*/
	function return_shoplist($arrayname,$coupon_type){
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = $coupon_type;
		$data['Coupon'] = $this->CouponModel->where($Coupon_map)->group('shop_id')->order("id DESC")->select();
		foreach($data['Coupon'] as $C_k=>$C_v){
			$Carbrand = $this->CarbrandModel->where(array('brand_id'=>$C_v['brand_id']))->find();
			$data['Coupon'][$C_k]['brand_name'] = $Carbrand['brand_name'];
			$Pricecoupon['is_delete'] = 0;
			$Pricecoupon['show_s_time'] = array('lt',time());
			$Pricecoupon['show_e_time'] = array('gt',time());
			$Pricecoupon['coupon_type'] = $coupon_type;
			$Pricecoupon['shop_id'] = $C_v['shop_id'];
			$data['Coupon'][$C_k][$arrayname] = $this->CouponModel->where($Pricecoupon)->order("id DESC")->limit('0,6')->select();
			foreach($data['Coupon'][$C_k][$arrayname] as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Coupon'][$C_k][$arrayname][$k]['shop_name'] = $shop['shop_name'];
				$data['Coupon'][$C_k][$arrayname][$k]['shop_address'] = $shop['shop_address'];
			}
		}
		return $data;
	}

	/*
		@author:chf
		@function:查找相关品牌相关优惠卷团购和优惠全部
		@time:2013-09-08
	*/
	function return_Allcouponlist($coupon_type){
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = $coupon_type;
		
		
		if( $coupon_type == 1 ) {
			$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
		}else {
			$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
		}
		$coupon_arr['coupon_type'] =  $coupon_type;
		$coupon_arr['start_time'] = array('lt',time());
		$coupon_arr['end_time'] =  array('gt',time());
		$coupon_arr['is_delete'] =  '0';
		$coupontype = $this->CouponModel->where($coupon_arr)->order('rand()')->select();
		foreach($coupontype as $k=>$v){
			$TUIJIAN_COUPON.=$v['id'].",";
			
		}
		

		$tuijian_coupon_ids = substr($TUIJIAN_COUPON,0,-1);
		
		
		$Coupon_map['id'] = array('in',explode(',',$tuijian_coupon_ids));

		$data = $this->CouponModel->where($Coupon_map)->field('coupon_des',true)->order("pay_count DESC")->limit(6)->select();
		
		foreach($data as $C_k=>$C_v){
				$shop = $this->ShopModel->where(array('id'=>$C_v['shop_id']))->find();
				$data[$C_k]['shop_name'] = $shop['shop_name'];
				$data[$C_k]['shop_address'] = $shop['shop_address'];
		}
		return $data;
	}




	/*
		@author:chf
		@function:携车网主页显示 (旧上线删除)
		@time:2013-03-21
	*/
    public function index_old(){
		Cookie::set('_currentUrl_', __URL__);
		$this->assign('areaname',$this->city_name);//匹配地区
		//滚动4S店列表
		$model_shop = D('Shop');
		if($this->city_name == "上海" ) {
			$map_sh['status'] = 1;
			$map_sh['shop_class'] = 1;
		}

		$map_sh['shop_city'] = $this->city_id;
		$shoplist = $model_shop->where($map_sh)->order("id DESC")->limit(20)->select();
		$shop_count = $model_shop->where($map_sh)->count();
		$this->assign('shop_count',$shop_count);

		if ($shoplist){
			$model_shop_fs_relation = D('Shop_fs_relation');

			foreach ($shoplist as $kk=>$vv){
				if (file_exists("UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
			}
		}
		//推荐优惠券
        $this->get_tuijian_coupon();
		//快捷信息公告区
		$model_shopnotice = D('Notice');
		$map_notice['status'] = 1;
		$map_notice['city_name'] = $this->city_name;
		if($this->city_name == '上海') {
			$shopnotice = $model_shopnotice->where($map_notice)->order("update_time DESC")->limit(11)->select();
		}else {
			$shopnotice = $model_shopnotice->where($map_notice)->order("update_time DESC")->limit(20)->select();
		}
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
		$map_a['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		if($this->city_name == "上海") {
			$articlelist = $model_article->where($map_a)->order("id DESC")->limit(11)->select();
		}else {
			$articlelist = $model_article->where($map_a)->order("id DESC")->limit(20)->select();
		}
		$this->assign('articlelist',$articlelist);
		//获取商家列表
		//签约4S店数量
		if($this->city_name == '上海') {
			$map_shoptype1['status'] = 1;
		}
		$map_shoptype1['shop_class'] = 1;
		$map_shoptype1['shop_city'] =$this->city_id;
		$shop1count = $model_shop->where($map_shoptype1)->count();
		$this->assign('shop1count',$shop1count);

		//非签约4S店数量
		if($this->city_name == '上海') {
			$map_shoptype2['status'] = 1;
		}
		$map_shoptype2['shop_class'] = 2;
		$map_shoptype2['shop_city'] =$this->city_id;
		$shop2count = $model_shop->where($map_shoptype2)->count();
		$this->assign('shop2count',$shop2count);
		$this->assign('shoplist',$shoplist);
		$this->assign('title',"携车网:4S店预约保养,保养团购,售后资讯,汽车售后服务网站");
		$this->display();
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

	/*
		@author:chf
		@function:通过快捷信息公告ID得到商铺对应信息
		@time:2013-03-07
	*/
	function AjaxNotice(){
		header("Content-type: text/html; charset=utf-8");
		//header('content-type: application/json');
		$model_shop = D('Shop');
		$ModelShopnotice = D('Notice');
		$map_shop['id'] = $_POST['ShopId'];
		$Notice['id'] = $_POST['NoticeId'];

		if($map_shop['id']){
			$Shop = $model_shop->where($map_shop)->find();
			$Notice = $ModelShopnotice->where($Notice)->find();
			if($Shop) {
				$data = array_merge($Shop,$Notice);
			}else {
				$data = $Notice;
			}
			echo json_encode($data);
		}else{
			$Notice = $ModelShopnotice->where($Notice)->find();
			echo json_encode($Notice);
		}
	}

	/*
		@author:chf
		@function:通过type得到对应信息 type = 1 城市区域
		@time 2013-05-02
	*/
	function AjaxInfo(){
		header("Content-type: text/html; charset=utf-8");
		$type = $_REQUEST['type'];
		if($type == 1){
			$map['parent_id'] = $this->city_id;
			$map['region_type'] = 3;
			$map['status'] = 1;
			$data = $this->RegionModel->where($map)->select();
			echo json_encode($data);
		}
		elseif($type == 2){
			$map['shop_city'] = $this->city_id;
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
			$map['status'] = 1;
			$data = $this->ShopModel->where($map)->order("id DESC")->limit(7)->select();
			echo json_encode($data);
		}

	}

	function test() {

		$Model = new Model();

		$sql = "SELECT id FROM `xc_shop` where id in (SELECT shopid FROM `xc_shop_fs_relation` where fsid in(18, 12, 61, 67, 26, 54, 44)) and shop_class='1'";
		$shopids = $Model->query($sql);

		$shop_model = D("Shop");
		$shopsaleconfig_model = D("Shopsaleconfig");

		foreach($shopids as $key=>$val) {
			$data = array();
			$data['id'] = $val['id'];
			$data['safestate'] = 1;
			$shop_model->save($data);

			$sale_data = array();
			$sale_data['shop_id'] = $val['id'];
			$sale_data['scooter'] = 0;
			$sale_data['rebate'] = 0;
			$sale_data['auto'] = 1;
			$sale_data['state'] = 1;
			$shopsaleconfig_model->add($sale_data);
		}

	}
}