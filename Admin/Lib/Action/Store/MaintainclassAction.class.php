<?php

/*
*配件管理
 */

class MaintainclassAction extends CommonAction {
	   function _filter(&$map){
        $map['ItemName'] = array('like',"%".$_POST['ItemName']."%");
/*
		if (isset ( $_REQUEST ['pid'] ) && $_REQUEST ['pid'] != '') {
			$map['ItemLevel'] = array('eq',$_REQUEST['pid']);
        }else{
			$map['ItemLevel'] = array('eq',0);

		}
		*/
    }

	function _before_index(){
		if (isset($_REQUEST['pid'])) {
			$Articlecategory_Model  = D(GROUP_NAME."/"."Maintainclass");
			$res = $Articlecategory_Model->getByItemID($_REQUEST ['pid']);
			$this->assign('cateName',$res['name']);
			$this->assign('pid',$res['ItemID']);
		}
	}

    function _before_add() {
        $model	=	D(GROUP_NAME."/"."Articlecategory");
        $list	=	$model->where('PItemID =0')->getField('ItemID,ItemName');
        $this->assign('list',$list);
    }

    function _before_edit() {
            $model	=	D(GROUP_NAME."/"."Articlecategory");
            $list	=	$model->where('PItemID =0')->getField('ItemID,ItemName');
            $this->assign('list',$list);
      }

	


}