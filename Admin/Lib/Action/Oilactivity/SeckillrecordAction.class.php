<?php
/*
 * 活动记录
 */
class SeckillrecordAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->mMyoil = D('spreadmyoil');//我的油桶表
		$this->mSpreadpic = D('spreadpic');//推广二维码图片生成表
		$this->mSpreadsign = D('spreadsign');//签到表
		$this->mSpreadoil = D('spreadoil');//别人帮我加油表
		$this->mSpreadprize = D('spreadprize');//领奖表
		$this->mRankprize = D('rankprize');//领奖表
	}

	function index(){
		$rankprize = D("rankprize");
		$mMyoil = D('spreadmyoil');

		import('ORG.Util.Page');// 导入分页类
		$count      = $rankprize->count();// 查询满足要求的总记录数
		$p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}

		$page = $p->show_admin();

		$list = $rankprize->order('create_time')->limit($p->firstRow.','.$p->listRows)->select();

		//$list = $rankprize->select();
		$myoil = "";
		foreach ($list as $key => $value) {
			$data['weixin_id'] = $value['weixin_id'];
			$mlist = $mMyoil->field('oil_num')->where($data)->find();
			$list[$key]['oil_num'] = $mlist['oil_num'];
		}
		//var_dump($list);
		$this->assign('page',$page);// 赋值分页输出
		$this->assign("list",$list);
		$this->display();
	}
  

}
