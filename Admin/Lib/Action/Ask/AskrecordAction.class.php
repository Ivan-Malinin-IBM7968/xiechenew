<?php

class AskrecordAction extends CommonAction {
	protected $mAsk;	
	protected $mPadatatest;
	function __construct() {
		parent::__construct();
		$this->mAsk = M('tp_xieche.ask','xc_');
		$this->mPadatatest = M('tp_xieche.padatatest','xc_');
	}
	
    /**
     * 空调滤管理
     * @date 2014/9/25 
     * bright
     */
    public function index(){
    	$map = array();
    	
		if( $_REQUEST['start_time'] ) {
			$end_time = strtotime($_REQUEST['start_time'].' 23:59:59');
			$map['create_time'] = array(array('gt',strtotime($_REQUEST['start_time'])));
		}
		if( $_REQUEST['end_time'] ) {
			$map['create_time'] = array(array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
			$map['create_time'] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		
		// 计算总数
        $count = $this->mAsk->where($map)->count();
		// 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		
        
        // 分页显示输出
        $page = $p->show_admin();

        $list = $this->mAsk->where($map)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select(); 
		if ($list){
			foreach ($list as &$val){
				$weixin_id = $val['openid'];
				switch ($val['opercode']){
					case 1000:
						$opercode = '未接入会话';
						break;
					case 1001:
						$opercode = '接入会话';
						break;
					case 1002:
						$opercode = '主动发起会话';
						break;
					case 1004:
						$opercode = '关闭会话';
						break;
					case 1005:
						$opercode = '抢接会话';
						break;
					case 2001:
						$opercode = '公众号收到消息';
						break;
					case 2002:
						$opercode = '客服发送消息';
						break;
					case 2003:
						$opercode = '客服收到消息';
						break;
					default:
						$opercode = '';
						break;
				}
				$val['opercode'] = $opercode;
				unset($val);
			}
		}
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
        $this->assign('page',$page);
        $this->assign("list",$list);
		$this->display();
	}
    
}
