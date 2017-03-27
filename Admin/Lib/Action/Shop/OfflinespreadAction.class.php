<?php
/*
 * 地推控制器
 */
class OfflinespreadAction extends CommonAction {

    public function index(){
        Cookie::set('_currentUrl_', __SELF__);

		$mOfflinespread = D(GROUP_NAME.'/Offlinespread');
		$mPaweixin = D(GROUP_NAME.'/paweixin');
		
		$where = array();
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$list_start_time = $_REQUEST['start_time'];
			$start_time = strtotime($_REQUEST['start_time']);
			$list_end_time = $_REQUEST['end_time'];
			$end_time = strtotime($_REQUEST['end_time']);
			$where['updateTime'] = array(array('gt',$start_time),array('lt',$end_time),'and');
		}
		
        // 计算总数
        $count = $mOfflinespread->where($where)->count( );
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);

        // 分页显示输出
        $page = $p->show_admin();
        
        // 当前页数据查询
        $list = $mOfflinespread->where($where)->order('id ASC')->limit($p->firstRow.','.$p->listRows)->select();
        
        $allInviteNum = 0;
        if ($list){
        	foreach ($list as $key=> &$val){
        		$invite_code = $val['invite_code'];
        		$num = $mPaweixin->where( array(
        				'invite_code'=>$invite_code
        		))->count();
        		$val['invite_num'] = $num;
        		unset($val);
        	}
        }
        $res = $mOfflinespread->where($where)->order('id ASC')->select();
        foreach ($res as $value){
        	$allInviteNum += $num;
        }
        
        if(isset($list_start_time)){
        	$list['start_time'] = $list_start_time;
        }
        if(isset($list_end_time)){
        	$list['end_time'] = $list_end_time;
        }
        // 赋值赋值
        $this->assign('allInviteNum', $allInviteNum);
        $this->assign('page', $page);
        $this->assign('list', $list);

        $this->display();
    }
    public function delete_spread(){
    	$id = isset($_POST['id'])?$_POST['id']:0;
    	$mOfflinespread = D(GROUP_NAME."/offlinespread");
    	$where['id'] = $id;
    	$mOfflinespread->where($where)->delete();
    	echo 1;exit;
    }

    public function _before_edit(){
        $mOfflinespread = D(GROUP_NAME."/offlinespread");
        $id = $_GET['id'];
        $where['id'] = $id;
        $model = $mOfflinespread->where($where)->select();
        $this->assign('vo',$model);
    }




}