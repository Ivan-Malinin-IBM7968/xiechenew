<?php
class CarservicedailyAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}
	
    public function index(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_order = D(GROUP_NAME.'/daycomplete');

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
		//$sql = "SELECT id FROM `xc_reservation_order` WHERE 1 AND order_time >1430582400 AND order_time <1430668800 AND `truename` NOT LIKE '%测试%'";
		//$ids = $model_order->query($sql);
		//echo serialize($ids);
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $model_order->where($map)->count();
        // 当前页数据查询
        $list = $model_order->where($map)->order('create_time DESC')->select();
		//echo $model_order->getLastsql();

		if($list and is_array($list)){
			foreach($list as $k=>$v){
				$ids = unserialize($v['order_ids']);
				$i=0;
				foreach($ids as $kk=>$vv){
					$i++;
				}
				$list[$k]['complete'] = round(($v['complete_sum']/$i)*100,2).'%';
				$list[$k]['cancel'] = round(($v['cancel_sum']/$i)*100,2).'%';
				$list[$k]['delay'] = round(($v['delay_sum']/$i)*100,2).'%';
			}
		}
		
		$this->assign('time', $_REQUEST['time']);
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();

	}
}
?>