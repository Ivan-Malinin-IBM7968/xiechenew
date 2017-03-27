<?php
/*
 * 微信绑定控制器
 */
class PaweixinAction extends CommonAction {

    public function index(){
        Cookie::set('_currentUrl_', __SELF__);

		$mPaweixin = D(GROUP_NAME.'/paweixin');
		$mCarbrand = D(GROUP_NAME.'/carbrand');
		$mCarseries = D(GROUP_NAME.'/carseries');
		$mCarmodel = D(GROUP_NAME.'/carmodel');
		
		//根据invite_code查询数据
		$invite_code = $_GET['invite_code'];
		if (!$invite_code) {
			return false;
		}
		if (isset($_POST['sTime']) and !empty($_POST['sTime'])){
			$sTime = strtotime($_POST['sTime']);
			$where['shop_boss'] = array('egt',$sTime);
			$this->assign('sTime',$_POST['sTime']);
		}
		if (isset($_POST['eTime']) and !empty($_POST['eTime'])){
			$eTime = strtotime($_POST['eTime']);
			$where['eTime'] = array('elt',$eTime);
			$this->assign('eTime',$_POST['eTime']);
		}
		$where['invite_code'] = $invite_code;

        // 计算总数
        $count = $mPaweixin->where($where)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);

        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $mPaweixin->order('id ASC')->limit($p->firstRow.','.$p->listRows)->where($where)->select();
        foreach ($list as &$val){
        	$brind_id = $val['brand_id'];
        	$series_id = $val['series_id'];
        	$model_id = $val['model_id'];
        	$val['brand_id'] = $mCarbrand->where( array('brand_id'=>$brind_id) )->getField('brand_name');
        	$val['series_id'] = $mCarseries->where( array('series_id'=>$series_id) )->getField('series_name');
        	$val['model_id'] = $mCarmodel->where( array('model_id'=>$model_id) )->getField('model_name');
        	unset($val);
        }
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);

        $this->display();
    }

    public function delete(){
    	$id = isset($_POST['id'])?$_POST['id']:0;
    	$mOfflinespread = D(GROUP_NAME."/paweixin");
    	$where['id'] = $id;
    	$mOfflinespread->where($where)->delete();
    	echo 1;exit;
    }




}