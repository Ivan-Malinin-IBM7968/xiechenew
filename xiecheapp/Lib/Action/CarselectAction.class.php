<?php
// 本类由系统自动生成，仅供测试用途
class CarselectAction extends Action {

	public function index(){
		$model = D('carbrand');
		$brand = $model->select();
		$this->assign('brand',$brand);
		$this->display();
	}


	public function ajax_get_carinfo(){
		header("Content-Type: text/plain; charset=utf-8");
		if($_POST['brand_id']){
    		if($_POST['brand_ids']){
    	        $brand_ids = $_POST['brand_ids'];
    	        $data = explode(',',$brand_ids);
    	        if ($data){
    	            foreach ($data as $k=>$v){
    	                $brand_fs_arr = explode('|',$v);
    	                if ($brand_fs_arr[0]==$_POST['brand_id']){
    	                    $map['fsid'] = $brand_fs_arr[1];
    	                }
    	            }
    	        }
    	    }
			$model = D('carseries');
			$brand_id = $_POST['brand_id'];
			$map['brand_id'] = $brand_id;
			$list = $model->where($map)->order("word")->select();
			unset($map);
		}
		if($_POST['series_id']){
			$model = D('carmodel');
			$series_id = $_POST['series_id'];
			$map['series_id'] = $series_id;
			$list = $model->where($map)->select();
			
			unset($map);
		}
	    if(!$_POST['series_id'] and !$_POST['brand_id']){
			$model = D('carbrand');
			$list = $model->select();
		}
		echo json_encode($list);
		return;
	}
	public function ajax_get_brand(){
	    header("Content-Type: text/plain; charset=utf-8");
	    $data = array();
	    $brand_arr = array();
	    if($_POST['brand_ids']){
	        $brand_ids = $_POST['brand_ids'];
	        $data = explode(',',$brand_ids);
	        if ($data){
	            foreach ($data as $k=>$v){
	                $brand_fs_arr = explode('|',$v);
	                $brand_arr[] = $brand_fs_arr[0];
	            }
	        }
	    }
	    $brand_str = implode(',',$brand_arr);
	    $model_brand = D('Carbrand');
	    if ($brand_str){
	        $map_brand['brand_id'] = array('in',$brand_str);
	    }
	    $brand_info = $model_brand->where($map_brand)->select();
	    echo json_encode($brand_info);exit;
	}
}


