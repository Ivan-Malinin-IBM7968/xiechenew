<?php

class CardrecordAction extends CommonAction {	
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
		//$where = array('tp_xieche.xc_cardrecord.type'=>5);
		$store = false;
		if( !empty($_POST['type']) ){
			if ($_POST['type'] == 7) {
				$store = true;
			}else{
				$where['tp_xieche.xc_cardrecord.type'] = $_POST['type'];
			}
			$this->assign('type',$_POST['type']);
		}
		if( !empty($_POST['use_way']) ){
			$where['tp_xieche.xc_info.use_way'] = $_POST['use_way'];
			$this->assign('use_way',$_POST['use_way']);
		}
		if( !empty($_POST['name']) ){
			$where['tp_xieche.xc_cardinfo.name'] = $_POST['name'];
			$this->assign('name',$_POST['name']);
		}
		$use_way = $this->mCardcate->distinct(true)->field('use_way')->select();
		$this->assign('use_way_list',$use_way);
		
		$name = $this->mCardinfo->distinct(true)->field('name')->select();
		$this->assign('name_list',$name);
		// 计算总数
		if ($store) { //查库存
			$count = $this->mCardhouse->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardinfo.id = tp_xieche.xc_cardhouse.info_id')->count();
		}else{	//查记录
			$count = $this->mCardcate->where($where)->join('LEFT JOIN tp_xieche.xc_cardrecord ON tp_xieche.xc_cardinfo.id = tp_xieche.xc_cardrecord.info_id')->count();
		}
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
		if ($store) {
			$list = $this->mCardhouse->field('xc_cardinfo.*,xc_cardhouse.num as house_num,xc_cardhouse.id as house_id')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardinfo.id = tp_xieche.xc_cardhouse.info_id')->select();
		}else{
			$list = $this->mCardinfo->field('xc_cardinfo.*,xc_cardrecord.type as record_type,xc_cardrecord.receive_person,xc_cardrecord.operator_person,xc_cardrecord.id as record_id,xc_cardrecord.record_num,xc_cardrecord.sale_type')->where($where)->join('LEFT JOIN tp_xieche.xc_cardrecord ON tp_xieche.xc_cardinfo.id = tp_xieche.xc_cardrecord.info_id')->order('record_id DESC')->limit($p->firstRow.','.$p->listRows)->select();
			if ($list) {
				$mAdmin = M('tp_admin.user','xc_');
				foreach ($list as &$val){
					$username = $mAdmin->field('nickname')->where(array('id'=>$val['operator_person']))->find();
					$val['username'] = $username['nickname'];
					//库存数
					$houseData = $this->mCardhouse->field('num as house_num')->where( array('info_id'=>$val['id']) )->find();
					$val['house_num'] = $houseData['house_num'];
					unset($val);
				}
			}
		}
		$this->assign('store',$store);       
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	
	public function del(){
		$id = $_GET['id'];
		$ret = $this->mCardinfo->delete(array('id'=>$id));
		if($ret){
			$this->success('删除成功',U('Storehouse/Cardcate/index/'));
		}else{
			$this->error('删除失败');
		}
	}
	public function add(){
		$where = array('status'=> 3);//出库以后的数据才可以退卡
		$list = $this->mCardinfo->where($where)->select();
		if ($list) {
			foreach ( $list as &$val){
				$cateData = $this->mCardcate->field('ic')->where( array('id'=>$val['cate_id']))->find();
				if ($cateData) {
					$val['ic'] = $cateData['ic'];
				}
				unset($val);
			};
		}
		$this->assign('list', $list);
		
		$this->assign('time', time());
		$this->display();
	}
	public function do_add(){
		$ids = $this->_post('id');
		$remark = $this->_post('remark');
		$type = $this->_post('type');
		$receive_person = $this->_post('receive_person');
		$num = $this->_post('num');//退卡数量
		$idArr = explode('|', $ids);
		$res = $up = $record = '';
		foreach ($idArr as $id){
			if (!$id) {
				continue;
			}
			$num = $this->mCardinfo->field('num')->where( array('id'=>$id))->find();
			//退卡入库,更新库存的数量
// 			$data = array(
// 					'info_id' => $id,
// 					'num' => $num['num'],
// 					'remark' => $remark,
// 					'create_time' => time()
// 			);
// 			$res = $this->mCardhouse->add($data);
			if ($res) {
				//更新部分数据为退卡状态,根据num来算
				
				$update = array(
					'status'=>3
				);
				$up = $this->mCardinfo->where( array('id'=>$id))->save($update);
				
				//退卡记录
				$recordData = array(
						'info_id' => $id,
						'type'=>5,
						'create_time'=>time(),
						'operator_person'=>$_SESSION['authId'],
						'remark' => $remark,
						'sale_type' => $type,
						'receive_person'=>$receive_person
				);
				$record = $this->mCardrecord->add($recordData);
			}
			
		}
		if($record){
			//TODO::更新卡列表的数据 按照info id 来更新
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作出库:'.$res
			);
			$this->addOperateLog($array);
			$this->success('操作退卡成功',U('Storehouse/Cardhouse/index/'),true);
		}else{
			$this->error('操作退卡失败','',true);
		}		
	}

}
