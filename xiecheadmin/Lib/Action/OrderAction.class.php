<?php
class OrderAction extends CommonAction {
	/*
	 * 判断条件
    *
    */
    function _filter(&$map){
        if (isset ( $_REQUEST ['turename'] ) && $_REQUEST ['turename'] != '') {
            $map['turename'] = array('like','%,'.$_REQUEST['turename'].',%');
        }
    	if (isset ( $_REQUEST ['mobile'] ) && $_REQUEST ['mobile'] != '') {
            $map['mobile'] = array('like','%,'.$_REQUEST['mobile'].',%');
        }
    	if (isset ( $_REQUEST ['licenseplate'] ) && $_REQUEST ['licenseplate'] != '') {
            $map['licenseplate'] = array('like','%,'.$_REQUEST['licenseplate'].',%');
        }
	}
	
	
    // 框架首页
    public function index() {
        $model = D('Order');
    	$arr = $this->_list($model, '');
    	$this->display();
        
    }
    
	//编辑
	public function edit(){
		 $name = $this->getActionName();
        $model = M($name);
		$vo = $model->getByOrder_id($_REQUEST['order_id']);
       // dump($vo);
        $model_serviceitem = D('Serviceitem');
		$service_name = $model_serviceitem->GetServiceName($vo['service_id']);
		$vo['service_name'] = $service_name['si_name'];
		$service_item_name = $model_serviceitem->GetServiceItemName($vo['service_item_id']);
		$vo['service_item_name'] = $service_item_name['si_name'];
		
		$order_time_arr = breakdate($vo['order_time']);
		$vo['order_time'] =$order_time_arr;
		
		$memebercar_arr[] = array(
			'brand_id'=>$vo['brand_id'],
			'series_id'=>$vo['series_id'],
			'model_id'=>$vo['model_id'],
		);
		$model_membercar = D('Membercar');

		$membercar = $model_membercar->ListMembercar($memebercar_arr);

		$vo['membercar'] = $membercar;
		dump($vo);
        $this->assign('vo', $vo);
        $this->display();
		
	}
	public function  _before_update(){
		$order_time = $_POST['order_date'].' '.$_POST['order_hours'].':'.$_POST['order_minute'];
		dump($order_time);
		$_POST['order_time'] = strtotime($order_time);		
	}
	
	public function orderlog(){

			$map['order_id'] = array('eq',$_REQUEST['order_id']);
			$model_orderlog = D('Orderlog');
			$list = $model_orderlog->where($map)->select();
			//echo $model_orderlog->getLastSql();
			//dump($_SESSION);
			//dump($list);
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
	
	

}
?>