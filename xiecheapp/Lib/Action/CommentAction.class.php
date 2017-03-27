<?php
//订单
class CommentAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		
	}
	/*
	 * 判断条件
     *
     */
    function _filter(&$map){
    
	}
	
	function index(){
	    if( true !== $this->login()){
			exit;
		}
		$shopid = isset($_REQUEST['shopid'])?$_REQUEST['shopid']:0;
		$orderid = isset($_REQUEST['orderid'])?$_REQUEST['orderid']:0;
		if ($shopid and $orderid){
			$model_shop = D("Shop");
			$shop_info = $model_shop->find($shopid);
			
			$model_order = D("Order");
			$order_info = $model_order->find($orderid);
			
			$model_servicemember = D("Servicemember");
			$servicemember = $model_servicemember->find($order_info['servicemember_id']);
			
			$this->assign('servicemember',$servicemember);
			$this->assign('shop_name',$shop_info['shop_name']);
			$this->assign('shopid',$shopid);
			$this->assign('orderid',$orderid);
			$this->display(); 
		}else {
			echo '店铺ID或者订单ID不存在！';
		}
		exit;
	}
	
	function showcomment(){
	    if( true !== $this->login()){
			exit;
		}
	    $shopid = isset($_POST['shopid'])?$_POST['shopid']:0;
	    $orderid = isset($_POST['orderid'])?$_POST['orderid']:0;
	    if ($shopid and $orderid){
	       //订单信息
	       $model_order = D('Order');
	       $orderinfo = $model_order->where("id=$orderid")->find();
	       $model_comment = D('Comment');
	       $comment_info = $model_comment->where("shop_id=$shopid AND order_id=$orderid")->find();
	       if (!empty($comment_info)){
	           $model_commentreply = D('Commentreply');
	           $commentreply_list = $model_commentreply->where("comment_id=$comment_info[id] AND status=0")->select();
	           if (isset($commentreply_list) and !empty($commentreply_list)){
	               $this->assign('commentreply_list',$commentreply_list);
	           }
	       }
	       //echo '<pre>';print_r($comment_list);exit;
	       $this->assign('orderinfo',$orderinfo);
	       $this->assign('comment_info',$comment_info);
	       $this->assign('shopid',$shopid);
	       $this->assign('orderid',$orderid);
	       $this->display(); 
	    }else {
	        //$this->display();
	        echo '店铺ID或者订单ID不存在！';
	    }
	    exit;
	}
	
    function showcommentall(){
	    $shopid = isset($_REQUEST['shopid'])?$_REQUEST['shopid']:0;
	    if ($shopid){
	       //订单信息
	       $model_comment = D('Comment');
	       $model_member = D('Member');
	       $comment_info = $model_comment->where("shop_id=$shopid")->order("create_time DESC")->select();
	       if (!empty($comment_info)){
	           $model_commentreply = D('Commentreply');
	           foreach ($comment_info as $key=>$val){
	               if (!$val['user_name']){
	                   $memberinfo = $model_member->find($val['uid']);
	                   $comment_info[$key]['user_name'] = substr($memberinfo['mobile'],0,5).'******';
	               }
    	           $comment_info[$key]['reply'] = $model_commentreply->where("comment_id=$val[id] AND status=0")->select();
	           }
	       }
	       if (!empty($comment_info)){
	            $this->assign('comment_info',$comment_info);
	           $this->assign('shopid',$shopid);
	           $this->display(); 
	       }else {
	           echo '还没有评价！';
	       }
	    }else {
	        //$this->display();
	        echo '还没有评价！';
	    }
	    exit;
	}
	
	
	function add_comment(){
	    if( true !== $this->login()){
			exit;
		}
	    $data['shop_id'] = isset($_POST['shopid'])?$_POST['shopid']:0; 
	    $data['order_id'] = isset($_POST['orderid'])?$_POST['orderid']:0;
	    $data['comment'] = isset($_POST['content'])?$_POST['content']:'';
	    $data['comment_type'] = isset($_POST['comment_type'])?$_POST['comment_type']:'';
	    $data['create_time'] = time();
	    $data['update_time'] = time();
	    $uid = $this->GetUserId();
	    $data['uid'] = $uid;
	    $model_order = D('Order');
	    $map_o['id'] = $data['order_id'];
	    $map_o['uid'] = $uid;
	    $order = $model_order->where($map_o)->find();
	    if ($order['member_id']){
	        $data['member_id'] = $order['member_id'];
	    }
		/*服务顾问*/
		if ($order['servicemember_id']){
	        $data['servicemember_id'] = $order['servicemember_id'];
			$data['service'] = $_POST['service'];
			$data['profession'] = $_POST['profession'];
			$data['sincerity'] = $_POST['sincerity'];
			$data['type'] = 2;
	    }

	    $model_member = D('Member');
	    $memberinfo = $model_member->where("uid=$uid")->find();
	    $data['user_name'] = $memberinfo['username'];
	    $model_comment = D('Comment');
	    $comment_info = $model_comment->where("shop_id=$data[shop_id] AND order_id=$data[order_id]")->find();
	    if (!empty($comment_info)){
	        unset($data['create_time']);
	        if ($comment_info['comment_type'] == 1){
	            $this->error('已经是好评了，不能再修改！');
	        }else{
        	    if($model_comment->where("shop_id=$data[shop_id] AND order_id=$data[order_id]")->save($data)){
					$this->count_servicecomment_rate($order['servicemember_id']);//服务顾问
        	        $this->count_good_comment($data['shop_id'],$data['member_id']);
        	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
        	        $this->success('评价修改成功！');
        	    }else {
        	        $this->error('评价修改失败！');
        	    }
	        }
	    }else{
	        if ($model_comment->add($data)){
    	        $model_order->where("id=$data[order_id]")->save(array('iscomment'=>1));
				$this->count_servicecomment_rate($order['servicemember_id']);//服务顾问
    	        $this->count_good_comment($data['shop_id'],$data['member_id']);
    	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    	        $this->success('评论添加成功！');
    	    }else {
    	        $this->error('评论添加失败！');
    	    }
	    }
	}
	

	/*
	@function:服务顾问评价计算
	@author:ysh
	@time:2013-11-15 
	*/
	function count_servicecomment_rate($servicemember_id="") {
		if($servicemember_id) {
			if( true !== $this->login()){
				exit;
			}
			$model_comment = D('Comment');

			$servicecount = $model_comment->where(array('servicemember_id'=>$servicemember_id,'type'=>'2'))->count();	
			//总服务态度评分
			$sumservice = $model_comment->where(array('servicemember_id'=>$servicemember_id,'type'=>'2'))->sum('service');
			//总服专业技能评分
			$sumprofession = $model_comment->where(array('servicemember_id'=>$servicemember_id,'type'=>'2'))->sum('profession');
			//总诚信态度评分
			$sumsincerity = $model_comment->where(array('servicemember_id'=>$servicemember_id,'type'=>'2'))->sum('sincerity');
			$service = number_format($sumservice/$servicecount,1);
			$profession = number_format($sumprofession/$servicecount,1);
			$sincerity = number_format($sumsincerity/$servicecount,1);
			
			$servicemember_model = D("Servicemember");
			$data['service'] = $service;
			$data['profession'] = $profession;
			$data['sincerity'] = $sincerity;
			$data['id'] = $servicemember_id;
			$data['comment_sum'] = $servicecount;
			$servicemember_model->save($data);
		}
	}


	function count_good_comment($shop_id,$uid){//计算公式= (好评-差评）/总评数
	    if( true !== $this->login()){
			exit;
		}
    	$model_comment = D('Comment');
	    if ($shop_id >0){
    	    $comment_info = $model_comment->where("shop_id=$shop_id")->select();
    	    $good_comment_number = 0;
    	    $bad_comment_number = 0;
    	    if (!empty($comment_info)){
    	        foreach ($comment_info as $v){
    	            if ($v['comment_type'] == 1){
    	                $good_comment_number +=1;
    	            }
    	            if ($v['comment_type'] == 3){
    	                $bad_comment_number +=1;
    	            }
    	        }
    	    }
    	    $all_comment_number = count($comment_info);
    	    if ($good_comment_number>$bad_comment_number and $all_comment_number >0){
    	       //$comment_rate = ($good_comment_number - $bad_comment_number)/$all_comment_number*100;
    	    	/*新好评率不扣除差评 直接按照好评的百分比来算
		@author:ysh
		@time:2013-12-16
		*/
		$comment_rate = $good_comment_number / $all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_shop = D('Shop');
    	    $data['id'] = $shop_id;
    	    $data['comment_rate'] = $comment_rate;
    	    $data['comment_number'] = $all_comment_number;
    	    $model_shop->where("id=$shop_id")->save($data);
	    }
		/*
	    if ($uid >0){
    	    $comment_info = $model_comment->where("member_id=$uid")->select();
    	    $good_comment_number = 0;
    	    $bad_comment_number = 0;
    	    if (!empty($comment_info)){
    	        foreach ($comment_info as $vv){
    	            if ($vv['comment_type'] == 1){
    	                $good_comment_number +=1;
    	            }
    	            if ($vv['comment_type'] == 3){
    	                $bad_comment_number +=1;
    	            }
    	        }
    	    }
    	    $all_comment_number = count($comment_info);
    	    if ($good_comment_number>$bad_comment_number and $all_comment_number >0){
    	        $comment_rate = ($good_comment_number - $bad_comment_number)/$all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_member = D('Member');
    	    $data1['uid'] = $uid;
    	    $data1['comment_rate'] = $comment_rate;
    	    $data1['comment_number'] = $all_comment_number;
    	    $map_m['uid'] = $uid;
    	    $model_member->where($map_m)->save($data1);
	    }
		*/
	}
	function reply_comment(){
	    if( true !== $this->login()){
			exit;
		}
	    $data['reply'] = isset($_POST['content'])?$_POST['content']:'';
	    $data['comment_id'] = isset($_POST['comment_id'])?$_POST['comment_id']:'0';
	    $data['create_time'] = time();
	    $data['update_time'] = time();
	    $uid = $this->GetUserId();
	    $data['operator_id'] = $uid;
	    $model_member = D('Member');
	    $memberinfo = $model_member->where("uid=$uid")->find();
	    $data['operator_name'] = $memberinfo['username'];
	    $data['operator_type'] = $memberinfo['member_type'];
	    $model_commentreply = D('Commentreply');
	    if ($model_commentreply->add($data)){
	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	        $this->success('回复成功！');
	    }else {
	        $this->error('回复失败！');
	    }
	}
}