<?php
/*
 */
class CommentAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
    
    public function comment(){
        $model_comment = D(GROUP_NAME.'/Comment');
        if (isset($_GET['comment_id']) and !empty($_GET['comment_id'])){
            $comment_info = $model_comment->find($_GET['comment_id']);
        }elseif (isset($_GET['order_id']) and !empty($_GET['order_id'])) {
            $map['order_id'] = $_GET['order_id'];
            $comment_info = $model_comment->where($map)->find();
        }
        if (!empty($comment_info)){
            $model_commentreply = D('Commentreply');
            $commentreply_list = $model_commentreply->where("comment_id=$comment_info[id] AND status=0")->select();
            if (isset($commentreply_list) and !empty($commentreply_list)){
               $this->assign('commentreply_list',$commentreply_list);
            }
	    }
        //echo '<pre>';print_r($comment_info);exit;
        if (!empty($comment_info)){
           $this->assign('shopid',$comment_info['shop_id']);
           $this->assign('orderid',$comment_info['order_id']);
           $this->assign('uid',$comment_info['uid']);
           $this->assign('comment_info',$comment_info);
        }
        $this->display();
    }
    public function docomment(){
        $model_order = D(GROUP_NAME.'/Order');
        if (isset($_GET['order_id']) and !empty($_GET['order_id'])) {
            $order_info = $model_order->find($_GET['order_id']);
            $this->assign('orderid',$_GET['order_id']);
            $this->assign('shopid',$order_info['shop_id']);
            $this->assign('uid',$order_info['uid']);
        }
        $this->display();
    }
    public function add_comment(){
        $data['shop_id'] = isset($_POST['shopid'])?$_POST['shopid']:0; 
	    $data['order_id'] = isset($_POST['orderid'])?$_POST['orderid']:0;
	    $data['comment'] = isset($_POST['content'])?$_POST['content']:'';
	    $data['comment_type'] = isset($_POST['comment_type'])?$_POST['comment_type']:'';
	    $data['create_time'] = time();
	    $data['update_time'] = time();
	    $uid = isset($_POST['uid'])?$_POST['uid']:0;
	    $model_member = D(GROUP_NAME.'/Member');
	    $memberinfo = $model_member->where("uid=$uid")->find();
	    $data['uid'] = $uid;
	    $data['user_name'] = $memberinfo['username'];
	    $model_comment = D(GROUP_NAME.'/Comment');
	    $comment_info = $model_comment->where("shop_id=$data[shop_id] AND order_id=$data[order_id]")->find();
	    if (!empty($comment_info)){
	        unset($data['create_time']);
	        if ($comment_info['comment_type'] == 1){
	            $this->error('已经是好评了，不能再修改！');
	        }else{
        	    if($model_comment->where("shop_id=$data[shop_id] AND order_id=$data[order_id]")->save($data)){
        	        $this->count_good_comment($data['shop_id']);
        	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
        	        $this->success('评价修改成功！');
        	    }else {
        	        $this->error('评价修改失败！');
        	    }
	        }
	    }else{
	        if ($model_comment->add($data)){
    	        $model_order = D(GROUP_NAME.'/Order');
    	        $model_order->where("id=$data[order_id]")->save(array('iscomment'=>1));
    	        $this->count_good_comment($data['shop_id']);
    	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    	        $this->success('评论添加成功！');
    	    }else {
    	        $this->error('评论添加失败！');
    	    }
	    }
	}


    function count_good_comment($shop_id){//计算公式= (好评-差评）/总评数
	    if ($shop_id >0){
    	    $model_comment = D(GROUP_NAME.'/Comment');
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
				$comment_rate = $good_comment_number / $all_comment_number*100;
    	    }else {
    	        $comment_rate = 0;
    	    }
    	    $model_shop = D(GROUP_NAME.'/Shop');
    	    $data['id'] = $shop_id;
    	    $data['comment_rate'] = $comment_rate;
    	    $data['comment_number'] = $all_comment_number;
    	    $model_shop->where("id=$shop_id")->save($data);
	    }
	}



    public function reply_comment(){
	    $data['reply'] = isset($_POST['content'])?$_POST['content']:'';
	    $data['comment_id'] = isset($_POST['comment_id'])?$_POST['comment_id']:'0';
	    $data['create_time'] = time();
	    $data['update_time'] = time();
	    $uid = $_SESSION['authId'];
	    $data['operator_id'] = $uid;
	    //$model_member = D(GROUP_NAME.'/Member');
	    //$memberinfo = $model_member->where("uid=$uid")->find();
	    $data['operator_name'] = '网站工作人员';
	    $data['operator_type'] = 2;
	    $model_commentreply = D(GROUP_NAME.'/Commentreply');
	    if ($model_commentreply->add($data)){
	        //$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	        $this->success('回复成功！');
	    }else {
	        $this->error('回复失败！');
	    }
	}
}