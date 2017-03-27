<?php
//首页
class OilconfigAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		
        $this->item_model = M('tp_xieche.item','xc_');  //车型号
        $this->item_oil = M('tp_xieche.item_oil','xc_');  //机油
        
        $this->new_oil = D('new_item_oil');  //新机油表  
	}
	
    /**
     * 机油管理
     * @date 2014/8/13
     */
    public function index(){
    	//搜索
    	$where = array();
    	if( isset($_POST['name']) ){
    		$where['name'] = array(
    				array("like","%{$_POST['name']}%")
    		);
    	}
    	$count = $this->new_oil->where($where)->count();
    	 
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
    	
        $oil_list = $this->new_oil->where($where)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select(); ; 
        $this->assign( 'oil_list' , $oil_list );
        $this->assign( 'page' , $page );
        $this->display();
    }

    public function add(){
        $this->display();
    }

    public function ajax_edit_oil(){
        $oil_id = intval($_POST['id']);
        $oil_name = trim($_POST['name']);
        $oil_type = intval($_POST['type']);
        $oil_norms = intval($_POST['norms']);
        $oil_price = intval($_POST['price']);
        $save_data['name'] = $oil_name;
        $save_data['type'] = $oil_type;
        $save_data['norms'] = $oil_norms;
        $save_data['price'] = $oil_price;
        if($oil_id){
            $condition['id'] = $oil_id;
            $this->new_oil->where($condition)->save($save_data);
        }else{
            $this->new_oil->add($save_data);
        }
        $return['errno'] = "0";
        $return['errmsg'] = "操作成功";
        $this->ajaxReturn( $return );
    }

    public function ajax_del_oil(){
        $oil_id = intval($_POST['id']);
        $this->new_oil->delete($oil_id);
        $return['errno'] = "0";
        $return['errmsg'] = "删除成功";
        $this->ajaxReturn( $return );
    }
    
    
     /*
     * 绑定二维码  wql@20150504
     */
    
     public function ajax_bindcode(){
        $oil_id = intval($_POST['id']);
        $oil_code = trim($_POST['code']);
               
        $save_data['code'] = $oil_code;
       
        if($oil_id){
            $condition['id'] = $oil_id;
            $this->new_oil->where($condition)->save($save_data);
        }else{
            $this->new_oil->add($save_data);
        }
        $return['errno'] = "0";
        $return['msg'] = "操作成功";
        $this->ajaxReturn( $return );
    }
    
    
    /**
     * 编辑品牌
     */
    public function edit_brand(){
        $brand_id = intval($_POST['brand_id']);
        if( $brand_id ){
            $brand = M( 'brand' );
            $brand_param['id'] = $brand_id;
            $brand_info = $car_brand->where($brand_param)->find();
            $this->assign( 'modify_type' , 2 );
            $this->assign( 'brand_info' , $brand_info );
        }else{
            $this->assign( 'modify_type' , 1 );
        }
        $this->display( 'brand_edit' );
        $return['errno'] = "0";
        $return['msg'] = "操作成功!";

    }

    /**
     * 品牌操作
     * modify_type 1添加 2编辑 3删除
     */
    public function do_modify_brand(){
        $brand_id = intval($_POST['brand_id']);
        $modify_type = intval($_POST['modify_type']);
        $brand_name = trim($_POST['name']);
        $save_data['name'] = $brand_name;
        $brand = M('brand');
        switch ( $modify_type ) {
            case '1'://新增
                $check_res = $brand->where( $save_data )->find();
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $brand->save($save_data); 
                }
                break;
            case '2'://编辑
                $save_data['id']['NEQ'] = $brand_id;
                $check_res = $brand->where( $save_data )->find();
                unset( $save_data['id'] );
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $where['id'] = $brand_name;
                    $brand->where( $where )->save( $save_data); 
                }
                break;
            case '3'://删除
                $del_param['id'] = $brand_id;
                $brand->where( $del_param )->delete();
                break;
        }
        $return['errno'] = "0";
        $return['msg'] = "操作成功!";
    }

    /**
     * 车型管理
     */
    public function models_manage(){
        $brand_id = intval($_POST['brand_id']);
        $models = M( 'models' );
        $models_param['brand_id'] = $brand_id;
        $models_list = $models -> where( $models_param)-> select();
        $this->assign( 'brand_list' , $brand_list );
        $this->display('models_manage');
    }

    /**
     * 车型编辑
     */
    public function edit_models(){
        $brand_id = intval($_POST['brand_id']);
        $models_id = intval($_POST['models_id']);
        if( $models ){
            $models = M( 'models' );
            $models_param['id'] = $models_id;
            $models_info = $models->where($models_param)->find();
            $this->assign( 'modify_type' , 2 );
            $this->assign( 'models_info' , $models_info );
        }else{
            $this->assign( 'modify_type' , 1 );
        }
        $this->assign( 'brand_id' , $brand_id );
        $this->display( 'models_edit' );
    }

    /**
     * 车型操作
     */
    public function do_modify_models(){
        $brand_id = intval($_POST['brand_id']);
        $models_id = intval($_POST['models_id']);
        $modify_type = intval($_POST['modify_type']);
        $models_name = trim($_POST['name']);

        $save_data['brand_id'] = $brand_id;
        $save_data['name'] = $models_name;
        $models = M('models');
        switch ( $modify_type ) {
            case '1'://新增
                $check_res = $models->where( $save_data )->find();
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $models->save($save_data); 
                }
                break;
            case '2'://编辑
                $save_data['id']['NEQ'] = "";
                $check_res = $models->where( $save_data )->find();
                unset( $save_data['id'] );
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $where['id'] = $models_name;
                    $models->where( $where )->save( $save_data); 
                }
                break;
            case '3'://删除
                $del_param['id'] = $models_id;
                $models->where( $del_param )->delete();
                break;
        }
        $return['errno'] = "0";
        $return['msg'] = "操作成功!";
    }

    /**
     * 车管理
     */
    public function style_manage(){
        $models_id = intval($_POST['models_id']);
        $style = M( 'style' );
        $style_param['models_id'] = $models_id;
        $style_list = $style -> where( $style_param)-> select();
        $this->assign( 'style_list' , $style_list );
        $this->display('style_manage');

    }

    /**
     * 车编辑
     */
    public function edit_style(){
        $models_id = intval($_POST['models_id']);
        $style_id = intval($_POST['style_id']);
        if( $style_id ){
            $style = M( 'style' );
            $style_param['id'] = $style_id;
            $style_info = $style->where($style_param)->find();
            $this->assign( 'modify_type' , 2 );
            $this->assign( 'style_info' , $style_info );
        }else{
            $this->assign( 'modify_type' , 1 );
        }
        $this->assign( 'style_id' , $models_id );
        $this->display( 'style_edit' );

    }

    /**
     * 车操作
     */
    public function do_modify_style(){
        $models_id = intval($_POST['models_id']);
        $style_id = intval($_POST['style_id']);
        $modify_type = intval($_POST['modify_type']);
        $style_name = trim($_POST['name']);

        $save_data['models_id'] = $models_id;
        $save_data['name'] = $style_name;
        $style = M('style');
        switch ( $modify_type ) {
            case '1'://新增
                $check_res = $style->where( $save_data )->find();
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $style->save($save_data); 
                }
                break;
            case '2'://编辑
                $save_data['id']['NEQ'] = "";
                $check_res = $models->where( $save_data )->find();
                unset( $save_data['id'] );
                if( $check_res ){
                    $return['errno'] = "1001";
                    $return['msg'] = "该品牌名已经存在!";
                }else{
                    $where['id'] = $models_name;
                    $models->where( $where )->save( $save_data); 
                }
                break;
            case '3'://删除
                $del_param['id'] = $models_id;
                $models->where( $del_param )->delete();
                break;
        }
        $return['errno'] = "0";
        $return['msg'] = "操作成功!";
    }

}
