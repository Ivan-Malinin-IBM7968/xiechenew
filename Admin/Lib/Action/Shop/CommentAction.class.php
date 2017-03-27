<?php
/*
 */
class CommentAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
    public function index(){
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
        $model_comment = D(GROUP_NAME.'/Comment');
        $map['shop_id'] = $shop_id;
        // 计算总数
        $count = $model_comment->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $model_comment->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('comments',$list);
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
	    $uid = $_SESSION['authId'];
	    $data['operator_id'] = $uid;
	    //$model_member = D(GROUP_NAME.'/Member');
	    //$memberinfo = $model_member->where("uid=$uid")->find();
	    $data['operator_name'] = '商家';
	    $data['operator_type'] = 3;
	    $model_commentreply = D(GROUP_NAME.'/Commentreply');
	    if ($model_commentreply->add($data)){
	        //$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	        $this->success('回复成功！');
	    }else {
	        $this->error('回复失败！');
	    }
	}
}