<?php
//用户记账详细controller
class DetailAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		/*if( true !== $this->login()){
			exit;
		}*/
	}
	/*
	 * 用户主页，如果存在多辆车，则显示多辆车的列表，如果只有一辆车，就直接重定向到ListDetail
	 * 
	 * 
	 */
	public function index(){
		$model = D('Membercar');
		$uid = $this->GetUserId();
		$list_all_car = $model->where("uid=$uid AND status='1'")->select();
		$count = count($list_all_car);
			if($count != 1){
				foreach($list_all_car AS $k=>$v){
						$model_myhome = D('Myhome');
						$list_all_car[$k]['list_total'] = $model_myhome->list_total($list_all_car[$k]['u_c_id'],$list_all_car[$k]['avgoil_type']);			
				}
				//dump($list_all_car);
				Cookie::set('_currentUrl_', __SELF__);
				$this->assign('list_all_car',$list_all_car);
				$this->display();
			}else{
				$this->redirect('ListDetail');
			}

				
	}
	
	

	//随便看看
	public function otherhome(){
		
		
	}
	//朋友主页
	
	public function friendhome(){
		
	
	}
	//显示记账
	public function ListDetail(){
	    if( true !== $this->login()){
			exit;
		}
	    $model_membercar = D('Membercar');
		$uid = $this->GetUserId();
		$list_all_car = $model_membercar->where("uid=$uid AND status='1'")->select();
		//echo '<pre>';print_r($list_all_car);exit;
		$get_u_c_id = isset($_GET['u_c_id'])?intval($_GET['u_c_id']):null;
		if (isset($_GET['u_c_id']) and intval($_GET['u_c_id'])>0){
		    $get_u_c_id = intval($_GET['u_c_id']);
		}elseif (isset($list_all_car[0]['u_c_id']) and intval($list_all_car[0]['u_c_id'])>0) {
		    $get_u_c_id = $list_all_car[0]['u_c_id'];
		}else {
		    $this->error('您没有管理的车辆,请到车辆管理添加!');
		}
		//获取用户ID
		//获取车型ID 如果GET没有数据，则从数据库中查询第一条为默认，并获取平均油耗记录方式
		
		$time = isset($_REQUEST['time'])?intval($_REQUEST['time']):null;
		$begin_time = isset($_REQUEST['begin_time'])?$_REQUEST['begin_time']:null;
		$end_time = isset($_REQUEST['end_time'])?$_REQUEST['end_time']:null;
		
		$select_avgoil_type = $model_membercar->where("uid=$uid AND u_c_id=$get_u_c_id")->find();
		$get_car_name = $select_avgoil_type['car_name'];
		$get_avgoil_type = $select_avgoil_type['avgoil_type'];
		//查询油耗计算方式
		//获取统计数据
		$model_myhome = D('Myhome');
		$all_data = $model_myhome->list_total($get_u_c_id,$get_avgoil_type['avgoil_type'],$time='',$begin_time='',$end_time='');
		//记账列表数据
		//$notemain_list = $model_myhome->get_note('1');
		//dump($notemain_list);
		/*
		 *模板输出
		 */
		//dump($all_data);
		$item_tmp_arr = $all_data['list_everynote_cost'];
		$note_type = C('NOTE_TYPE_NAME');
		if(is_array($item_tmp_arr)){
			foreach($item_tmp_arr AS $key=>$val){
				$item_tmp_arr[$key]['note_type'] = $note_type[$item_tmp_arr[$key]['note_type']];
				$tmp_list_everynote_cost[] = "['".$item_tmp_arr[$key]['note_type']."',".$item_tmp_arr[$key]['total_cost']."]";	
				$list_everynote_cost = implode(',', $tmp_list_everynote_cost);
			}
		}
		$this->assign('list_all_car',$list_all_car);
		$this->assign('note_total',$all_data);
		$this->assign('list_everynote_cost',$list_everynote_cost);
		$this->assign('avgoil_type',$get_avgoil_type['avgoil_type']);
		$this->assign('get_u_c_id',$get_u_c_id);
		$this->assign('get_car_name',$get_car_name);
		$this->display();
			
	}
	
	//耗油查询
	public function oildetail(){
	    $brand_id = isset($_POST['brand_id'])?$_POST['brand_id']:0;
		$series_id = isset($_POST['series_id'])?$_POST['series_id']:0;
		$model_id = isset($_POST['model_id'])?$_POST['model_id']:0;
		$this->assign('jumpUrl',Cookie::get('_currentUrl_'));
		if (empty($model_id)){
		    $this->error('请选择车型！');
		}
	    $model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		
		if ($brand_id){
		    $search_brand = $model_brand->getByBrand_id($brand_id);
		    if (isset($search_brand['brand_name']) and $search_brand['brand_name']){
		        $this->assign('brand_name',$search_brand['brand_name']);
		    }
		}
		if ($series_id){
		    $search_series = $model_series->getBySeries_id($series_id);
		    if (isset($search_series['series_name']) and $search_series['series_name']){
		        $this->assign('series_name',$search_series['series_name']);
		    }
		}
		
		/*if ($model_id){
		    $search_model = $model_model->getByModel_id($model_id);
		    if (isset($search_model['model_name']) and $search_model['model_name']){
		        $this->assign('model_name',$search_model['model_name']);
		    }
		}*/
		
		if ($brand_id and $series_id and $model_id){
	        $model_ids = $model_model->where("series_id=$series_id")->select();
	        $result_data = array();
	        if (!empty($model_ids)){
	            foreach ($model_ids as $model_info){
	                //计算每个类型的车辆的平均油耗
	                $result_data [$model_info['model_id']] = $this->get_peroilwear_by_model_id($model_info['model_id']);
	                $search_model = $model_model->getByModel_id($model_info['model_id']);
	                $result_data [$model_info['model_id']]['model_name'] = $search_model['model_name'];
	            }
	        }
	        //echo "<pre>";print_r($result_data);exit;
	        if (isset($result_data[$model_id])){
	            $current_oilwear_info = $result_data[$model_id];
	            $this->assign('current_oilwear_info',$current_oilwear_info);
	            unset($result_data[$model_id]);
	        }
	        $this->assign('per_oilwear_arr',$result_data);
		    
		    /*$model_oilwear  = D('Oilwear ');
		    $model_oilwear_demo = D('Oilwear_demo');
		    
		    $membercars_other = $model_oilwear->where("brand_id=$brand_id AND series_id=$series_id")->select();
		    if (!empty($membercars_other)){
		        $per_oilwear_arr = array();
		        foreach ($membercars_other as $othercar){
		            if ($per_oilwear_arr[$othercar['model_id']]['oilwearcount']){
		                $per_oilwear_arr[$othercar['model_id']]['oilwearcount'] += $othercar['oilwear'];
		                $per_oilwear_arr[$othercar['model_id']]['count'] += 1;
		            }else{
		                $per_oilwear_arr[$othercar['model_id']]['oilwearcount'] = $othercar['oilwear'];
		                $per_oilwear_arr[$othercar['model_id']]['count'] = 1;
		            }
		        }
		        if (!empty($per_oilwear_arr)){
		            foreach ($per_oilwear_arr as $mid=>$val){
		                $modelinfo = $model_model->getByModel_id($mid);
		                $per_oilwear_arr[$mid]['model_name'] = $modelinfo['model_name'];
		                $per_oilwear_arr[$mid]['perwear'] = round($val['oilwearcount']/$val['count'],2);
		            }
		        }
		        if ($per_oilwear_arr[$model_id]){
		            $per_oilwear = $per_oilwear_arr[$model_id];
		            $this->assign('per_oilwear',$per_oilwear);
		            unset($per_oilwear_arr[$model_id]);
		        }
		        $this->assign('per_oilwear_arr',$per_oilwear_arr);
		    }*/
		}
	    $this->display();
	}
	
	public function get_peroilwear_by_model_id($model_id){
	    $model_oilwear_demo = D('oilwear_demo');
	    $demo_oilwear_info = $model_oilwear_demo->where("model_id=$model_id")->find();
	    
	    $model_oilwear = D('oilwear');
	    $oilwear_infos = $model_oilwear->where("model_id=$model_id")->select();
	    $total_oil = 0;
	    if (!empty($oilwear_infos)){
	        foreach ($oilwear_infos as $val){
	            $total_oil +=$val['oilwear'];
	        }
	    }
	    $result_arr['total_oilwear'] = $demo_oilwear_info['percent_oilwear']*$demo_oilwear_info['member_number'] + $total_oil;
	    $result_arr['member_number'] = $demo_oilwear_info['member_number']+count($oilwear_infos);
	    $result_arr['per_oilwear'] = round(($demo_oilwear_info['percent_oilwear']*$demo_oilwear_info['member_number'] + $total_oil)/($demo_oilwear_info['member_number']+count($oilwear_infos)),2);
	    return $result_arr;
	}
	
	//添加用户自定义车型
	public function add_car(){
		
	}
	
	/*
	 * 显示记账内容
	 * 
	 */
	public function ListNoteRows(){
		$get_u_c_id = isset($_GET['u_c_id'])?intval($_GET['u_c_id']):null;
		$get_note_type = isset($_GET['note_type'])?intval($_GET['note_type']):null;
		if($get_u_c_id && $get_note_type){
			$model_notemain = D('Notemain');
			$map['u_c_id'] = array('EQ',$get_u_c_id);
			$map['note_type'] = array('EQ',$get_note_type);
			$uid = $this->GetUserId();
			$map['uid'] = array('EQ',$uid);
			$map['main_del'] = 0;
			$list = $this->_list($model_notemain, $map);
			$this->display();
		}
	}	
}
?>