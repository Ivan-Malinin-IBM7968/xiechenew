<?php
/*
 * 活动记录
 */
class ActivityrecordAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->mMyoil = D('spreadmyoil');//我的油桶表
		$this->mSpreadpic = D('spreadpic');//推广二维码图片生成表
		$this->mSpreadsign = D('spreadsign');//签到表
		$this->mSpreadoil = D('spreadoil');//别人帮我加油表
		$this->mSpreadprize = D('spreadprize');//领奖表
	}

	function index(){
		/*if( $_REQUEST['start_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])));
		}
		if( $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}*/
		if($_REQUEST['exchange_type'] and $_REQUEST['exchange_type']!='all'){
			$map['exchange_type'] = $_REQUEST['exchange_type'];
		}elseif(!$_REQUEST['exchange_type']){
			$map['exchange_type'] = $_REQUEST['exchange_type'] = 1;
		}
		if($_REQUEST['type'] and $_REQUEST['type']!='all'){
			$map['type'] = $_REQUEST['type'];
		}
        $spreadprize = D("spreadprize");
        $mMyoil = D('spreadmyoil');
		$model_user = D('member');

		// 计算总数
        $count = $spreadprize->where($map)->count();
		// 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		/*if(!$_REQUEST['status'] and !$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}*/
        
        // 分页显示输出
        $page = $p->show_admin();

        $list = $spreadprize->where($map)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select(); 
		//echo $spreadprize->getLastsql();
        if($list){
        	foreach ($list as $key => $value) {
        		$data['weixin_id'] = $value['weixin_id'];
        		$mlist = $mMyoil->field('oil_num')->where($data)->find();
        		$list[$key]['oil_num'] = $mlist['oil_num'];
				if($value['type']==2){
					$list[$key]['order_type'] = $value['type'];
				}else{
					$list[$key]['order_type'] = $value['type']+3;
				}
				$userinfo = $model_user->where(array('mobile'=>$value['mobile'],'status'=>'1'))->find();
				$list[$key]['uid'] = $userinfo['uid'];
			}
        }

        //var_dump($list);
		$this->assign('time_type',$_REQUEST['time_type']);
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
		$this->assign('exchange_type',$_REQUEST['exchange_type']);
		$this->assign('type',$_REQUEST['type']);
        $this->assign('page',$page);
        $this->assign("list",$list);
		$this->display();
	}

	function send_didi(){
		$didi =  M("tp_didi.code2","didi_");
		$can_prize = $this->mSpreadprize->where(array('id'=>$_REQUEST['id']))->find();
		$didi_repeat = $didi->where(array('orderid'=>$_REQUEST['id']))->find();
		if($didi_repeat){
			$this->error('这个订单已经换过码了');
		}
		if($can_prize['exchange_type']==2){
			$this->error('这个订单是已兑换状态');
		}
		//取滴滴打车券
		$didi_info = $didi->where(array('orderid'=>'0'))->order('id asc')->find();
		
		//短信发券给用户
		//$sms_post["phones"]=$order_mobile;
		$sms_post["phones"]="13774236413";
		$sms_content="快的充值码为".$didi_info['dcode']."，面额10一张。请在快的手机客户端充值后使用，激活后有效期14天，请在".$didi_info['yxq']."前激活";
		$sms_post["content"]=$sms_content;
		curl_sms($sms_post,'',1);

		//更新状态
		$data['orderid'] = $_REQUEST['id'];
		$data['ordermobile'] = $_REQUEST['mobile'];
		$didi->where(array('id'=>$didi_info['id']))->save($data);

		$data_prize['exchange_type'] = 2;
		$this->mSpreadprize->where(array('id'=>$didi_info['id']))->save($data_prize);

		$array = array(
				'oid'=>$_REQUEST['id'],
				'operate_id'=>$_SESSION['authId'],
				'log'=>'操作发券，订单号:'.$_REQUEST['id']
				);
        $this->addOperateLog($array);
	}
  

}
