<?php
/*
 */
class CommentAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ServicememberModel = D('servicemember');
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Public/login');
			//$this->error('没有登录');
			header("Location: ".__APP__."/Public/login");
		}
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
		$model_comment = D(GROUP_NAME.'/Comment');
        if (isset($_GET['comment_id']) and $_GET['comment_id']){
            $comment_info = $model_comment->find($_GET['comment_id']);
        }elseif (isset($_GET['order_id']) and $_GET['order_id']) {
            $map['order_id'] = $_GET['order_id'];
            $comment_info = $model_comment->where($map)->find();
        }
        if (isset($comment_info) and $comment_info['shop_id'] != $shop_id){
            $this->error('评论不属于该店铺！');
        }
	}
    public function index(){
		$shop_id = $_SESSION['shop_id'];
        $model_comment = D(GROUP_NAME.'/Comment');
		$commentreply_model = D(GROUP_NAME.'/Commentreply');
		$servicemember_model = D(GROUP_NAME.'/Servicemember');
		
		$model_order = D(GROUP_NAME.'/Order');
        $map['shop_id'] = $shop_id;
		$comment_type = $_REQUEST['comment_type'];
		if($comment_type) {
			$map['comment_type'] = $comment_type;
		}

		if($_REQUEST['servicemember_id']){
			$map['servicemember_id'] = $_REQUEST['servicemember_id'];
		}
        // 计算总数
        $count = $model_comment->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_comment->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
		if($_REQUEST['servicemember_id']) {
			$good_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>1,'servicemember_id'=>$_REQUEST['servicemember_id']))->count();
			$mid_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>2,'servicemember_id'=>$_REQUEST['servicemember_id']))->count();
			$bad_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>3,'servicemember_id'=>$_REQUEST['servicemember_id']))->count();

		}else {
			$good_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>1))->count();
			$mid_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>2))->count();
			$bad_num = $model_comment->where(array('shop_id'=>$shop_id,'comment_type'=>3))->count();
		}
		

        if ($list){
            foreach ($list as $k=>$v){
				$order_info = $model_order->find($v['order_id']);
				$list[$k]['true_order_id'] = $v['order_id'];
                $list[$k]['order_id'] = $this->get_orderid($v['order_id']);
				$list[$k]['mobile'] = $order_info['mobile'];
				$list[$k]['licenseplate'] = $order_info['licenseplate'];
				$list[$k]['order_time'] = $order_info['order_time'];

				$map_reply['comment_id'] = $v['id'];
				$map_reply['status'] = 0;
				$list[$k]['comment_reply_num'] = $commentreply_model->where($map_reply)->count();
				
				if($v['servicemember_id'] && $v['type']==2) {
					$servicemember = $servicemember_model->find($v['servicemember_id']);
					$list[$k]['servicemember_name'] = $servicemember['name'];
				}
				
            }
        }
		/*服务顾问*/
		$Sermember = $this->ServicememberModel->where(array('shop_id'=>$shop_id,'state'=>'1'))->select();
		
        // 赋值赋值
        $this->assign('page', $page);
		$this->assign('servicemember_id', $_REQUEST['servicemember_id']);
        $this->assign('comments',$list);
        $this->assign('good_num',$good_num);
        $this->assign('mid_num',$mid_num);
        $this->assign('bad_num',$bad_num);
		$this->assign('Sermember',$Sermember);
        $this->display();
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
           $this->assign('comment_info',$comment_info);
        }
        $this->display();
    }
    
   public function reply_comment(){
	    $data['reply'] = isset($_POST['content'])?$_POST['content']:'';
	    $data['comment_id'] = isset($_POST['comment_id'])?$_POST['comment_id']:'0';
	    $data['create_time'] = time();
	    $data['update_time'] = time();
	    $uid = $_SESSION['shopadmin_authId'];
	    $data['operator_id'] = $uid;
	    //$model_member = D(GROUP_NAME.'/Member');
	    //$memberinfo = $model_member->where("uid=$uid")->find();
	    $data['operator_name'] = '商家';
	    $data['operator_type'] = 3;
	    $model_commentreply = D(GROUP_NAME.'/Commentreply');
	    if ($model_commentreply->data($data)->add()){
	        // $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	        $this->success('回复成功！');
	    }else {
	        $this->error('回复失败！');
	    }
		
	}
}