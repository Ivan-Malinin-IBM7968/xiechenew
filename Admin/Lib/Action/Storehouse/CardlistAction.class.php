<?php

class CardlistAction extends CommonAction {	
	protected $mCardcate;
	protected $mCardhouse;
	protected $mCardinfo;
	protected $mCardlist;
	protected $mCardrecord;
	function __construct() {
		parent::__construct();
        $this->mCardcate = M('tp_xieche.cardcate','xc_');  //卡类别配置表
        $this->mCardhouse = M('tp_xieche.cardhouse','xc_');//卡仓库表
        $this->mCardinfo = M('tp_xieche.cardinfo','xc_');//卡类型信息表
        $this->mCardlist = M('tp_xieche.cardlist','xc_');//卡列表
        $this->mCardrecord = M('tp_xieche.cardrecord','xc_');  //卡进出库记录表
	}
	
	public function index() {
		$info_id = @$_GET['info_id'];
		$status = @$_GET['status'];
		if (!$info_id) {
			$this->error('参数错误');
		}
		$this->assign('title_name','库存明细');
		$where = array('xc_cardlist.info_id'=>$info_id);
		if ($status) {
			if ($status == 3) {
				$this->assign('title_name','领用明细');
			}elseif ($status == 4){
				$this->assign('title_name','销售明细');
			}elseif ($status == 5){
				$this->assign('title_name','退卡明细');
			}elseif ($status == 6){
				$this->assign('title_name','结算明细');
			}
			$where['xc_cardlist.status'] = $status;
		}
		// 计算总数
		$count = $this->mCardlist->where($where)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		// 分页显示输出
		$page = $p->show_admin();
		// 当前页数据查询
		$list = $this->mCardlist->field('xc_cardinfo.name,xc_cardinfo.cate_id,xc_cardlist.*')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardlist.info_id = tp_xieche.xc_cardinfo.id')->order('xc_cardlist.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
		if ($list) {
			foreach ($list as &$val){
				$icData = $this->mCardcate->field('ic')->where(  array('id'=>$val['cate_id']) )->find();
				if($icData){
					$val['ic'] = $icData['ic'].$val['id'];
				}
				switch ($val['status']){
					case 1:
						$val['status'] = '未入库';
						break;
					case 2:
						$val['status'] = '已入库';
						break;
					case 3:
						$val['status'] = '已领卡出库';
						break;
					case 4:
						$val['status'] = '已销售';
						break;
					case 5:
						$val['status'] = '已退卡';
						break;
					case 6:
						$val['status'] = '已结算';
						break;
				}
				unset($val);
			};
		}
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	
}
