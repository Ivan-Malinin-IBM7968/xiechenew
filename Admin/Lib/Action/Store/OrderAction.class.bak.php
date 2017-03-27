<?php
class OrderAction extends CommonAction {
	/*
	 * 判断条件
    *
    */
    function _filter(&$map){
    	if (isset ( $_REQUEST ['licenseplate'] ) && $_REQUEST ['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%,'.$_REQUEST['licenseplate'].',%');
        }
		
		if(isset($_REQUEST['type']) && $_REQUEST['keyword'] !=''){
			$map[$_REQUEST['type']] = array('eq',$_REQUEST['keyword']);
		}
		
		if(isset($_REQUEST['shop_id']) && $_REQUEST['shop_id'] !=''){
			$map['shop_id'] = array('eq',$_REQUEST['shop_id']);
		}
		if(isset($_REQUEST['order_state']) && $_REQUEST['order_state'] !=''){
			$map['order_state'] = array('eq',$_REQUEST['order_state']);
		}
	}
	function test(){
		echo "123";
	}
	
    // 框架首页
    public function _before_index() {
		$shop_model = D('Shop');
		$shop_list = $shop_model->select();
		$this->assign('shop_list',$shop_list);        
    }
    
	public function _trans_data(&$list){
		$shop_model = D('Shop');
		$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
			foreach($shop_list AS $k=>$v){
				if($list[$k]['shop_id'] == $shop_list[$kk]['id']){
					$list[$k]['shop_name'] = $shop_list[$kk]['shop_name'];
				}
				
			}
		}
		return $list;
	}


	//编辑
	public function edit(){
        $model = D('Order');
		$vo = $model->getById($_REQUEST['order_id']);
		$order_time_arr = $this->breakdate($vo['order_time']);
		$shop_model = D('Shop');
		$getShopName = $shop_model->getById($vo['shop_id']);
		$vo['shop_info'] = $getShopName;
		//$vo['order_time'] = date('Y-m-d',$vo['order_time']);
		$vo['order_time'] =$order_time_arr;
		dump($vo);
			//$model_membercar = D('Membercar');
			$select_car = $this->Membercar_format_by_model_id($vo['model_id']);
			$map['order_id'] = array('eq',$_GET['order_id']);
			$list_by_suborder = $model->where($map)->join('xc_suborder ON xc_suborder.order_id = xc_order.id')->select();
			echo $model->getlastsql();
			//dump($list_by_suborder);
			$this->assign('serviceitem', include($this->getCacheFilename('Serviceitem')));
			$this->assign('select_car',$select_car);
			$this->assign('list_by_suborder',$list_by_suborder);
			//dump($vo);
        $this->assign('vo', $vo);
        $this->display();
		
		
	}
	public function  _before_update(){
		$order_time = $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
		//dump($order_time);
		$_POST['order_time'] = strtotime($order_time);		
	}
	
	public function orderlog(){

			$map['order_id'] = array('eq',$_REQUEST['order_id']);
			$model_orderlog = D('Orderlog');
			$list = $model_orderlog->where($map)->select();
			$this->assign('list',$list);
			$this->display();		
	}
	
	public function addorderlog(){
		$model_orderlog = D('Orderlog');
	        if (false === $model_orderlog->create()) {
            $this->error($model_orderlog->getError());
        }
        $_POST['submit_time'] = time();
        $_POST['operate_id'] = $_SESSION[C('USER_AUTH_KEY')];
        //保存当前数据对象
        $list = $model_orderlog->add($_POST);
        echo $model_orderlog->getLastSql();
        if ($list !== false) { //保存成功
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }	
		
	}
	
	/*
	 * 通过model_id来获取车型对应数据
	 * 初始化数据
	 * 
	 */
	public function Membercar_format_by_model_id($model_id) {
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		if(!empty($model_id)){
			$list_default_model = $model_model->getByModel_id($model_id);
			//echo $model_model->getLastSql();
			$list_default_series = $model_series->getBySeries_id($list_default_model['series_id']);
		//	dump($list_default_series);
			$list_default_brand = $model_brand->getByBrand_id($list_default_series['brand_id']);
			
		}
		if($list_default_brand && $list_default_model && $list_default_series){
			$list['brand_name'] = $list_default_brand['brand_name'];
			$list['series_name'] = $list_default_series['series_name'];
			$list['model_name'] = $list_default_model['model_name'];
			//dump($list);
			return $list;
		}else{
				
			return false;
		}
	}

	/*
 * ajax返回产品信息数据
 * 
 * 
 */
	public function ajax_get_product_info() {
		header("Content-Type: text/plain; charset=utf-8");
		//载入产品MODEL
		$model_product = D(GROUP_NAME.'/Product');
		$map['product_id'] = array('in',$_REQUEST['product_str']);
		$map['xc_product.service_id'] = array('in',$_REQUEST['select_services']);
		$map['xc_productrelation.car_model_id'] = array('eq',$_REQUEST['select_model_id']);
		$list_product = $model_product->where($map)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
		//echo $model_product->getlastsql();
		$model_order = D(GROUP_NAME.'/Order');
		if(empty($_POST['workhours_sale'])){
			$model_shop = D(GROUP_NAME.'/Shop');
			$sale = $model_shop->getById($_POST['shop_id']);
			$sale_arr['workhours_sale']=$sale['workhours_sale'];
			//echo 111;
		}else{
			$sale_arr['workhours_sale']=$_POST['workhours_sale'];
		}
		if(empty($_POST['product_sale'])){
			$model_shop = D(GROUP_NAME.'/Shop');
			$sale = $model_shop->getById($_POST['shop_id']);
			$sale_arr['product_sale']=$sale['product_sale'];
		}else{		
			$sale_arr['product_sale']=$_POST['product_sale'];
		}
		
		$output_str = $model_order->ListProductdetail_S($list_product,$sale_arr);
		echo $output_str;		
	}
		
	/*
 * 日期 小时 分钟 拆分为数组
 * 
 */

	function breakdate($time){
		$date = date('Y-m-d',$time);
		$arr['date'] = $date;
		$hour = date('H',$time);
		$arr['hour'] = $hour;
		$hour = date('s',$time);
		$arr['minute'] = $hour;
		return $arr;
		
	}
}
?>