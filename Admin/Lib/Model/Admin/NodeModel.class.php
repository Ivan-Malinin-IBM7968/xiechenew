<?php

// 节点模型
class NodeModel extends CommonModel {
	/*
	*添加节点不能重名，目前设计可能有重名，先注释，后期调整
	*modify by yyc
	*time 2012.05.26
	*/
	/*
	protected $_validate	=	array(
		array('name','checkNode','节点已经存在',0,'callback'),
		);
	*/
    
    protected $dbName = 'tp_admin';
    protected $tablePrefix = 'xc_';
    
	public function checkNode() {
        if(is_string($_POST['name'])) {
            $map['name']	 =	 $_POST['name'];
            $map['pid']	=	isset($_POST['pid'])?$_POST['pid']:0;
            $map['status'] = 1;
            if(!empty($_POST['id'])) {
                $map['id']	=	array('neq',$_POST['id']);
            }
            $result	=	$this->where($map)->field('id')->find();
            if($result) {
                return false;
            }else{
                return true;
            }
        }
        return true;
	}
}
?>