<?php

class MachinefilterconfigAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->item_type_model = M('tp_xieche.item_type','xc_');  //配件类型对应表
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //配件表
        $this->item_brand_model = M('tp_xieche.item_brand','xc_');  //配件类型对应表
        
        $this->new_filter = D('new_item_filter');  //新保养三滤配件表
	}
	
    /**
     * 机滤管理
     * @date 2014/9/25 
     * bright
     */
    public function index(){
    	$where = array(
    			'name'=>'机滤'
    	);
    	$type_res = $this->item_type_model->field('id')->where($where)->find();
    	
    	$where2 = array(
    			'type_id'=>$type_res['id']	//配件类型
    	);
    	//搜索
    	if( isset($_POST['name']) ){
    		$where2['name'] = array(
    				array("like","%{$_POST['name']}%")
    		);
    	}
    	$count = $this->new_filter->where($where2)->count();
    	
    	// 导入分页类
    	import("ORG.Util.Page");
    	// 实例化分页类
    	$p = new Page($count,25);
    	foreach ($_POST as $key => $val) {
    		if (!is_array($val) && $val != "" ) {
    			$p->parameter .= "$key/" . urlencode($val) . "/";
    		}
    	}
    	if(!$_REQUEST['p']){
    		$p->parameter = "index/".$p->parameter;
    	}
    	$page = $p->show_admin();
        $list = $this->new_filter->where($where2)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select(); 
//        if ($list) {
//        	foreach ($list as &$val){
//        		$res = $this->item_brand_model->where(array(
//        				'id'=>$val['brand_id']
//        		))->find();
//        		$val['brand'] = $res;
//        		unset($val);
//        	}
//        }
        //$brand_list = $this->item_brand_model->select();//品牌数据
        //$this->assign( 'brand_list' , $brand_list );
        
        $this->assign( 'list' , $list );
        $this->assign( 'page' , $page );
        $this->display();
    }

    //添加机滤
    public function add(){
//    	$brand_list = $this->item_brand_model->select();
//    	$this->assign( 'brand_list' , $brand_list );
        $this->display();
    }

    public function ajax_edit(){
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $brand_id = intval($_POST['brand_id']);
        $price = intval($_POST['price']);
        $save_data['name'] = $name;
        $save_data['brand_id'] = $brand_id;
        $save_data['price'] = $price;
        $where = array(
        		'name'=>'机滤'
        );
        $type_res = $this->item_type_model->field('id')->where($where)->find();
        $save_data['type_id'] = $type_res['id'];	//配件类型
        if($id){
            $condition['id'] = $id;
            $this->new_filter->where($condition)->save($save_data);
        }else{
            $this->new_filter->add($save_data);
        }
        $return['errno'] = "0";
        $return['errmsg'] = "操作成功";
        $this->ajaxReturn( $return );
    }

    public function ajax_del(){
        $id = intval($_POST['id']);
        $this->new_filter->delete($id);
        $return['errno'] = "0";
        $return['errmsg'] = "删除成功";
        $this->ajaxReturn( $return );
    }
    
    /*
     * 绑定二维码  wql@20150504
     */
    
     public function ajax_bindcode(){
        $id = intval($_POST['id']);
        $code = trim($_POST['code']);
               
        $save_data['code'] = $code;
       
        if($id){
            $condition['id'] = $id;
            $this->new_filter->where($condition)->save($save_data);
        }else{
            $this->new_filter->add($save_data);
        }
        $return['errno'] = "0";
        $return['msg'] = "操作成功";
        $this->ajaxReturn( $return );
    }
    
    
}
