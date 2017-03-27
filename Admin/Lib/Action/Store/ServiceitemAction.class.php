<?php
/*
 * create by yyc
 * 服务分类
 * 
 */
class ServiceitemAction extends CommonAction{
	
    function _filter(&$map){
        $map['name'] = array('like',"%".$_POST['name']."%");

		if (isset ( $_REQUEST ['pid'] ) && $_REQUEST ['pid'] != '') {
			$map['service_item_id'] = array('eq',$_REQUEST['pid']);
        }else{
			$map['service_item_id'] = array('eq',0);
		}
    }
    
	function _before_index(){
		if (isset($_REQUEST['pid'])) {
			$Articlecategory_Model  = D(GROUP_NAME."/"."Serviceitem");
			$res = $Articlecategory_Model->getById($_REQUEST ['pid']);
			$this->assign('cateName',$res['name']);
			$this->assign('pid',$res['id']);
		}
	}

    function _before_add() {
        $model	=	D(GROUP_NAME."/"."Serviceitem");
        $list	=	$model->where('service_item_id=0')->getField('id,name');
        $this->assign('list',$list);
    }

    function _before_edit() {
            $model	=	D(GROUP_NAME."/"."Serviceitem");
            $list	=	$model->where('service_item_id=0')->getField('id,name');
            $this->assign('list',$list);
        }
	function _before_insert(){
		if($_POST['service_item_id']){
			$_POST['si_level'] = 1;
		}
	}
	
}