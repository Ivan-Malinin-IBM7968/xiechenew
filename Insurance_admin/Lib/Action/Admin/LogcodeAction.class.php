<?php
/*
 * 活动记录
 */
class LogcodeAction extends CommonAction {
	function index(){
		if( $_REQUEST['start_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])));
		}
		if( $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		if($_REQUEST['exchange_type'] and $_REQUEST['exchange_type']!='all'){
			$map['exchange_type'] = $_REQUEST['exchange_type'];
		}
		if($_REQUEST['oid']){
			$map['oid'] = $_REQUEST['oid'];
		}
		if($_REQUEST['type']){
			$map['type'] = $_REQUEST['type'];
		}
		if(!$_REQUEST['table']){
			$model = M("tp_xieche.logcode","xc_");
		}else{
			$model = M("tp_xieche.".$_REQUEST['table'],"xc_");
		}

        import('ORG.Util.Page');
        $count = $model->count();
        $Page  = new Page($count,25);
        $show  = $Page->show();
        $list = $model->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//echo $model->getLastsql();
        if($list){
        	foreach ($list as $key => $value) {
				$list[$key]['insertTime'] = date('Y-m-d H:i:s',$value['insertTime']);
			}
        }

		$type_info = $model->Distinct(true)->field('type')->select();

		$this->assign('type_info',$type_info);
		$this->assign('time_type',$_REQUEST['time_type']);
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
		$this->assign('oid',$_REQUEST['oid']);
		$this->assign('type',$_REQUEST['type']);
        $this->assign('page',$show);
        $this->assign("list",$list);
		$this->display();
	}


  

}
