<?php
//首页
class CarconfigAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->item_type_model = M('tp_xieche.item_type','xc_');  //配件类型对应表
        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //车型号
        
        $this->new_carbrand = D('new_carbrand');   //新品牌表
        $this->new_carseries = D('new_carseries');   //新车系表
        $this->new_carmodel = D('new_carmodel');   //新车型表
        $this->new_filter = D('new_item_filter');  //保养三滤配件
        
        
	}
	//技师列表
	public function index() {
        $map['is_show'] = array('neq',2) ;
        $brand_list = $this->new_carbrand->where($map)->order('word asc')->select();
		//echo $this->reservation_order_model->getLastsql();
        $this->assign('brand_list',$brand_list);
		$this->display();
	}

    /**
     * 车型
     */
    public function ajax_car_model(){
        $brand_id = intval($_POST['brand_id']);
        if( $brand_id ){
            $condition['brand_id'] = $brand_id;
            $car_model_list = $this->new_carseries->where( $condition )->select();
        }else{
            $car_model_list = "";
        }
        if( $car_model_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('model_list' => $car_model_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该品牌下无录入车系';
        }
        $this->ajaxReturn( $return );
    }

    /**
     * 车款
     */
    public function ajax_car_style(){
        $model_id = intval( $_POST['model_id'] );
        if( $model_id ){
            $condition['series_id'] = $model_id;
            $car_style_list = $this->new_carmodel->where( $condition )->select();
        }else{
            $car_style_list = "";
        }

        if( $car_style_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('style_list' => $car_style_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该车型下无录入车辆';
        }
        $this->ajaxReturn( $return );
    }

    /**
     * 配置
     */
    public function ajax_car_config(){
        $style_id = intval( $_POST['style_id'] )?intval( $_POST['style_id'] ):1;
        $item_set = array();
        if( $style_id ){
            $condition['model_id'] = $style_id;
            $style_info = $this->new_carmodel->where($condition)->find();
            $set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
            if( $set_id_arr ){
                foreach( $set_id_arr as $k=>$v){
                    if(is_array($v)){
                        foreach( $v as $_k=>$_v){
                            $item_condition['id'] = $_v;
                            $item_info_res = $this->new_filter->where($item_condition)->find();
                            $item_map_type['id'] = $item_info_res['type_id'];
                            $item_type_info = $this->item_type_model->where($item_map_type)->find();
                            $item_info['id'] = $item_info_res['id'];
                            $item_info['name'] = $item_info_res['name'];
                            $item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
                            $item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
                            $item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
                            $item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
                            $item_info['type_name'] = $item_type_info['name'];
                            $item_set[$k][$_k] = $item_info;
                        }
                    }
                }
            }
        }
        $return['errno'] = '0';
        $return['msg'] = 'success';
        $return['result'] ['item_set'] = $item_set;
        $return['result'] ['oil_type'] = $style_info['oil_type'] ? $style_info['oil_type'] : 0;
        $return['result'] ['oil_num'] = $style_info['oil_mass'] ? $style_info['oil_mass'] : 0;
        $return['result'] ['style_id'] = $style_info['id'] ? $style_info['id'] : 0;
        $this->ajaxReturn( $return );
    }
	
    /**
     *  修改配置
     */
    public function ajax_edit_car_config(){
        //print_r($_POST);
        $item_id = intval( $_POST['id'] );
        $item_name = trim( $_POST['name'] );
        $item_unit = $_POST['unit'];
        $item_num =  $_POST['num'];
        $item_type =  intval($_POST['type']);
        //$item_price = ceil($item_num )*$item_unit;
        $item_price =  $_POST['price'];

        $condition['id'] = $item_id;
        $update['name'] = $item_name;
        //$update['unit_price'] = $item_unit;
        //$update['number'] = $item_num;
        $update['price'] = $item_price;
        //$update['type'] = $item_type;
        //print_r($update);
        $this->new_filter->where($condition)->save($update);
        //echo $this->new_filter->getLastsql();

        $return['errno'] = '0';
        $return['msg'] = 'success';
        $return['result'] ['price'] = $item_price;
        $this->ajaxReturn( $return );
    }

    /**
     *  修改配置
     */
    public function ajax_edit_car_oil(){
        $model_id = intval( $_POST['id'] );
        $oil_type = $_POST['oil_type'];
        $oil_num =  $_POST['oil_num'];

        $condition['model_id'] = $model_id;
        $update['oil_type'] = $oil_type;
        $update['oil_mass'] = $oil_num;

        $this->new_carmodel->where($condition)->save($update);

        $return['errno'] = '0';
        $return['msg'] = 'success';
        $this->ajaxReturn( $return );
    }


    /**
     * 查询
     */
    public function select_car(){
        $car_brand = M( 'brand' );
        $car_brand_list = $car_brand -> select();
        $this->assign( 'car_brand_list' , $car_brand_list );
        $this->display('select');

    }
    /*
     * 解除绑定
     */
    function unbind(){
    	$model_id = $_POST['model_id'];
    	$unbind_id = $_POST['unbind_id'];
    	$type = $_POST['type'];
    	if( !$model_id || !$unbind_id || !$type){
    		$this->ajaxReturn('','参数为空',0);
    	}
    	$condition['model_id'] = $model_id;
    	$style_info = $this->new_carmodel->where($condition)->find();
    	$item_set = unserialize($style_info['item_set']);
    	$unlind_ids = $item_set[$type];
    	$k = array_search($unbind_id, $unlind_ids);	//找到传过来的id给过滤掉再保存
    	unset($unlind_ids[$k]);
    	$item_set[$type] = $unlind_ids; //过滤过的数据再放回去
    	$update = array(
    			'item_set' => serialize($item_set)
    	);
        //如果是机滤，并且机滤数组为空，更新此车型is_show为不显示
        if($type == 1 && count($item_set[$type]) == 0){
            $update['is_show'] = 2 ;
        }
        
    	$res = $this->new_carmodel->where($condition)->save($update);
    	if($res){
    		$this->ajaxReturn('','',1);
    	}else{
    		$this->ajaxReturn('','更新失败',0);
    	}
    }
    /*
     * 添加绑定
    */
    function addbind(){
    	$model_id = $_POST['model_id'];
    	$bind_id = $_POST['bind_id'];
    	$type = $_POST['type'];
    	if( !$model_id || !$bind_id || !$type){
    		$this->ajaxReturn('','参数为空',0);
    	}
    	$condition['model_id'] = $model_id;
    	$item_info = $this->new_carmodel->where($condition)->find();
    	if ( !empty($item_info) ) {
    		if ( !empty($item_info['item_set']) ) {
    			$item_set = unserialize($item_info['item_set']);
    			if ( !empty($item_set[$type]) ) {
    				$unlind_ids = $item_set[$type];
    				//验证绑定的id数据是否存在
    				$isExist = $this->_validData($type,$bind_id);
    				if(!$isExist){
    					$this->ajaxReturn('','您填写的id不正确，请重新填写',0);
    				}
    				//数据取出来再添加进去
    				array_push($unlind_ids, $bind_id);
    			}else{
    				$unlind_ids = array($bind_id);
    			}
    		}else{
    			$unlind_ids = array($bind_id);
    		}
    		
    		$item_set[$type] = $unlind_ids; //过滤过的数据再放回去
    		$update = array(
    				'item_set' => serialize($item_set)
    		);
            //如果是机滤，并且机滤数组不为空，更新此车型is_show为显示状态
            if($type == 1 && count($item_set[$type]) != 0){
                $update['is_show'] = 1;
            }
            
    		$res = $this->new_carmodel->where($condition)->save($update);
    		if($res){
    			$this->ajaxReturn('','',1);
    		}else{
    			$this->ajaxReturn('','更新失败',0);
    		}	
    	}else{
    		$this->ajaxReturn('','改车型配置参数为空',0);
    	}
    	
    }
    private function _validData($type,$id){
    	switch($type){
    		case 1:
    			$name = '机滤';
    			break;
    		case 2:
    			$name = '空气滤清器';
    			break;
    		case 3:
    			$name = '空调滤清器';
    			break;
            case 4:
    			$name = '雨刷';
    			break;
            case 5:
    			$name = '刹车片';
    			break;
    		default:
    			$name = false;
    			break;
    		
    	}
    	if($name){
    		$where = array(
    				'name'=>$name
    		);
    		$type_res = $this->item_type_model->where($where)->find();
            $where1['main_id'] = $type_res['id'];
            $type_res2 = $this->item_type_model->where($where1)->find();
             if($type_res2){
                 $where2 = array(
                 'id' => $id
                 );
             }else {
                 $where2 = array(
                     'type_id' => $type_res['id'],    //配件类型
                     'id' => $id
                 );
             }

    		$count = $this->new_filter->where($where2)->count();
    		return $count;
    	}else{
    		return 0;
    	}
    }

}
