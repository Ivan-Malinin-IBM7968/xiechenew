<?php
class ScandataAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ScanModel = D('scan_data');
		$this->scan_licenseplate = D('scan_licenseplate');
		$this->Carservicecode = D('Carservicecode');//优惠卷表
		$this->ShopModel = D('shop');//店铺表
		$this->technician_model = D('technician');//技师表
	}
	
	/*
		@author:wwy
		@function:
		@time:2014-11-10
	*/
    public function index(){
		if($_REQUEST['year']){
			$this->assign('year',$_REQUEST['year']);
		}
		if($_REQUEST['month']){
			$this->assign('month',$_REQUEST['month']);
		}
		if($_REQUEST['day']){
			$this->assign('day',$_REQUEST['day']);
		}
		//输入年,月时的定位时间
		if($_REQUEST['year'] and $_REQUEST['month']){
			$month_date = $_REQUEST['year'].'-'.$_REQUEST['month'].'-1';
			$month_time = strtotime($month_date);
		}
		//输入年,月,日时的定位时间
		if($_REQUEST['year'] and $_REQUEST['month'] and $_REQUEST['day']){
			$day_date = $_REQUEST['year'].'-'.$_REQUEST['month'].'-'.$_REQUEST['day'];
			$day_time = strtotime($day_date);
		}

		$map['scene_id'] = array(array('elt','30'),array('neq','0'));
		// 计算总数
		$count = $this->scan_licenseplate->where($map)->count();

		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 30);
		if($_REQUEST['is_use'] != "" ) {
			$p->parameter .= "is_use/" . urlencode($_REQUEST['is_use']) . "/";
		}
		if($_REQUEST['order_id'] != "" ) {
			$p->parameter .= "order_id/" . urlencode($_REQUEST['order_id']) . "/";
		}
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		// 分页显示输出
		$page = $p->show_admin();
		$scan_info = $this->scan_licenseplate->where($map)->limit($p->firstRow.','.$p->listRows)->order('id DESC')->select();
		//echo $this->scan_licenseplate->getLastsql();

		if ($scan_info){
			foreach ($scan_info as $k=>$v){
				$count_map['scene_id'] = $v['scene_id'];
				//当日数据
				if($day_time){
					$start_time = strtotime(date('Y-m-d'.' 0:00:00',$day_time));
					$end_time = strtotime(date('Y-m-d'.' 23:59:59',$day_time));
				}else{
					$start_time = strtotime(date('Y-m-d'.' 0:00:00',time()));
					$end_time = time();
				}
				$count_map['create_time'] = array(array('egt',$start_time),array('elt',$end_time));
				$scan_info[$k]['daycount'] = $this->ScanModel->where($count_map)->count();

				//当周数据
				if($day_time){
					$date = $day_date;  //当前日期
				}else{
					$date=date('Y-m-d'); //当前日期
				}
				$first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
				$w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6 
				$start_time=strtotime("$date -".($w ? $w - $first : 6).' days'); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
				if($day_time){
					$end_time = $start_time+7*86400;
				}else{
					$end_time=time();
				}
				$count_map['create_time'] = array(array('egt',$start_time),array('elt',$end_time));
				$scan_info[$k]['weekcount'] = $this->ScanModel->where($count_map)->count();

				//上周数据
				$end_time=strtotime("$date -".($w ? $w - $first : 6).' days'); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
				$start_time=$end_time-7*86400;  //上周开始日期
				$count_map['create_time'] = array(array('egt',$start_time),array('elt',$end_time));
				$scan_info[$k]['lastcount'] = $this->ScanModel->where($count_map)->count();
				//echo $this->ScanModel->getLastsql();
				//echo "</br>";

				//当月数据
				if($month_time){
					$start_time=strtotime(date('Y-m-01', $month_time));
					$end_time = strtotime(date('Ymd', mktime(0, 0, 0, $_REQUEST['month'] + 1, 1, $_REQUEST['year'])));
				}else{
					$start_time=strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
					$end_time = time();
				}
				$count_map['create_time'] = array(array('egt',$start_time),array('elt',$end_time));
				$scan_info[$k]['monthcount'] = $this->ScanModel->where($count_map)->count();
			}
		}
		
        $condition = array();
        $condition['status'] = 1;
        $technician_list = $this->technician_model->where($condition)->select();

		$this->assign('mobile',$mobile);
		$this->assign('is_use',$_REQUEST['is_use']);
		$this->assign('order_id',$_REQUEST['order_id']);
		$this->assign('page', $page);
		$this->assign('ARRAY', $ARRAY);
        $this->assign('memberlist',$scan_info);
		$this->assign('coupon_id',$coupon_id);
		$this->assign('technician_list', $technician_list);
        $this->display();
    }
	    
}
?>