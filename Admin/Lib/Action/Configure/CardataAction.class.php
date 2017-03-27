<?php
class CardataAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
	
	public function index(){
	    $this->display();
	}
	public function get_order_mobile(){
	    $order_state = $_GET['order_state'];
	    $model_order = D(GROUP_NAME."/Order");
	    $order_map['order_state'] = $order_state;
	    $order = $model_order->where($order_map)->select();
	    $uids = "";
	    if ($order){
	        foreach ($order as $k=>$v){
	            $uids .= $v['uid'].',';
	        }
	        $uids = substr($uids,0,-1);
	    }
	    $model_member = D(GROUP_NAME."/Member");
	    $member_map['uid'] = array('in',$uids);
	    $members = $model_member->where($member_map)->select();
	    $mobiles = "";
	    if ($members){
	        foreach ($members as $kk=>$vv){
	            $mobiles .= $vv['mobile'].',';
	        }
	        $mobiles = substr($mobiles,0,-1);
	    }
	    if ($mobiles){
	        $model_phonesenddata = D(GROUP_NAME."/Phonesenddata");
	        $phonesenddata_map['mobile'] = array('in',$mobiles);
	        $phonesenddatas = $model_phonesenddata->where($phonesenddata_map)->select();
	    }
	    if ($phonesenddatas){
	        $n = 1;
	        foreach ($phonesenddatas as $key=>$val){
	            if ($n%2==1){
	                $color = "#CCDDDD";
	            }else {
	                $color = "#FFFFFF";
	            }
	            $str_table .= '<tr bgcolor='.$color.'><td>'.$val['mobile'].'</td><td>'.$val['sender_id'].'</td><td>'.$val['sender_name'].'</td>';
	            $n++;
	        }
	    }
	    $color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=用户表.xls");
        $str = '<table><tr bgcolor='.$color.'><td>手机号码</td><td>员工ID</td><td>员工姓名</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}
	public function get_memberinfo(){
	    $member_model = D(GROUP_NAME."/Member");
	    $membercar_model = D(GROUP_NAME."/Membercar");
	    $carbrand_model = D(GROUP_NAME."/Carbrand");
	    $carseries_model = D(GROUP_NAME."/Carseries");
	    $carmodel_model = D(GROUP_NAME."/Carmodel");
	    
	    
	    $member_info = $member_model->order("uid asc")->select();
	    $str_table = '';
	    if ($member_info){
	        $n = 1;
	        foreach ($member_info as $_k=>$_v){
	            if ($n%2==1){
	                $color = "#CCDDDD";
	            }else {
	                $color = "#FFFFFF";
	            }
	            $str_table .= '<tr bgcolor='.$color.'><td>'.$_v['uid'].'</td><td>'.$_v['username'].'</td><td>'.$_v['mobile'].'</td><td>'.$_v['cardid'].'</td><td>'.$_v['email'].'</td><td>'.$_v['memo'].'</td>';
	            $car_map['uid'] = $_v['uid'];
	            $member_info[$_k]['carinfo'] = $membercar_model->where($car_map)->select();
	            
	            if ($member_info[$_k]['carinfo']){
	                $i = 1;
	                foreach ($member_info[$_k]['carinfo'] as $_kk=>$_vv){
    	                if ($_vv['brand_id']){
        	                $carbrand = $carbrand_model->find($_vv['brand_id']);
        	                $member_info[$_k]['carinfo'][$_kk]['brand_name'] = $carbrand['brand_name'];
        	            }
        	            if ($_vv['series_id']){
        	                $carseries = $carseries_model->find($_vv['series_id']);
        	                $member_info[$_k]['carinfo'][$_kk]['series_name'] = $carseries['series_name'];
        	            }
        	            if ($_vv['model_id']){
        	                $carmodel = $carmodel_model->find($_vv['model_id']);
        	                $member_info[$_k]['carinfo'][$_kk]['model_name'] = $carmodel['model_name'];
        	            }
        	            if ($i == 1){
        	                $str_table .= '<td>'.$member_info[$_k]['carinfo'][$_kk]['brand_name'].'</td><td>'.$member_info[$_k]['carinfo'][$_kk]['series_name'].'</td><td>'.$member_info[$_k]['carinfo'][$_kk]['model_name'].'</td></tr>';
        	            }else{
        	                $str_table .= '<tr bgcolor='.$color.'><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$member_info[$_k]['carinfo'][$_kk]['brand_name'].'</td><td>'.$member_info[$_k]['carinfo'][$_kk]['series_name'].'</td><td>'.$member_info[$_k]['carinfo'][$_kk]['model_name'].'</td></tr>';
        	            }
        	            $i++;
	                }
	            }else{
	                $str_table .= '<td></td><td></td><td></td></tr>';
	            }
	            $n++;
	        }
	    }
	    $color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=用户表.xls");
        $str = '<table><tr bgcolor='.$color.'><td>用户ID</td><td>用户名</td><td>电话号码</td><td>卡号</td><td>邮箱</td><td>备注</td><td>品牌</td><td>车系</td><td>车型</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        //$str = iconv("UTF-8", "GBK", $str);
        echo $str;
        exit;
	}
	public function card_data(){echo '数据已经生成。';exit;
	    $model_card = D('Card');
	    $data = array();
	    $carid = '6001000';
	    for ($i=0;$i<10000;$i++){
	        $carid ++;
	        $sum = 0;
    	    for($ii=0;$ii<strlen($carid);$ii++){
    	        $carid = (string)$carid;
                $sum += intval($carid[$ii]);
            }
            $str = $sum%10;
	        $data[$i]['cardid'] = $carid.$str;
	        $data[$i]['password'] = rand(1000,9999);
	    }
	    $model_card->addAll($data);
	    $this->success("生成成功！");
	}
	public function create_data(){
	    $model_brand = D(GROUP_NAME.'/carbrand');
	    $model_carseries = D(GROUP_NAME.'/carseries');
	    $model_carmodel = D(GROUP_NAME.'/carmodel');
	    $brand_data = $model_brand->order('word')->select();
	    $js_data = "var fct=new Array();";
	    if (!empty($brand_data)){
	        $js_data .="fct['0']='";
	        foreach ($brand_data as $brand){
	            $js_data .= $brand['brand_id'].",".$brand['word']." ".$brand['brand_name'].",";
	        }
	        $js_data = substr($js_data,0,-1);
	        $js_data .= "';";
	    }
	    $js_data .= "var br=new Array();";
	    if (!empty($brand_data)){
	        foreach ($brand_data as $brand1){
	            $map['brand_id'] = $brand1['brand_id'];
	            $series_data = $model_carseries->where($map)->order('word')->select();
	            if (!empty($series_data)){
	                $js_data .="br['".$brand1['brand_id']."']='";
	                foreach ($series_data as $series){
	                    if (strlen($series['word'])>1){
	                        $series['word'] = substr($series['word'],-1);
	                    }
    	                $js_data .= $series['series_id'].",".$series['word']." ".$series['series_name'].",";
    	            }
    	            $js_data = substr($js_data,0,-1);
    	            $js_data .= "';";
	            }
	        }
	    }
	    $js_data .= "var md=new Array();";
	    if (!empty($brand_data)){
	        foreach ($brand_data as $brand2){
	            $map['brand_id'] = $brand2['brand_id'];
	            $series_data = $model_carseries->where($map)->select();
	            unset($map);
	            if (!empty($series_data)){
	                foreach ($series_data as $series1){
	                    $map['series_id'] = $series1['series_id'];
	                    $model_data = $model_carmodel->where($map)->order('model_name')->select();
	                    unset($map);
	                    if (!empty($model_data)){
	                        $js_data .="md['".$series1['series_id']."']='";
	                        foreach ($model_data as $model){
	                            $js_data .= $model['model_id'].",".$model['model_name'].",";
	                        }
	                        $js_data = substr($js_data,0,-1);
	                        $js_data .= "';";
	                    }
    	            }
	            }
	        }
	    }
	    if (!empty($js_data)){
	        $str = file_put_contents('../Public/Js/car_select/car_data.js',$js_data);
	        if($str){
	            //echo $str;
	            echo '数据生成成功！';exit;
	        }else {
	            echo '数据生成失败！';exit;
	        }
	    }
	}
	public function run(){
	    $model_brand = D(GROUP_NAME.'/carbrand');
	    $model_carseries = D(GROUP_NAME.'/carseries');
	    $model_carmodel = D(GROUP_NAME.'/carmodel');
	    $brand_data = $model_brand->select();
	    $js_data = "var data = { ";
	    if (!empty($brand_data)){
	        foreach ($brand_data as $brand){
	            $js_data .= "'".$brand['brand_id']."' : '".$brand['brand_name']."', ";
	        }
	        $js_data .= " };";
	    }
	    $js_data .= "var data2 = {";
	    if (!empty($brand_data)){
	        foreach ($brand_data as $brand1){
	            $map['brand_id'] = $brand1['brand_id'];
	            $series_data = $model_carseries->where($map)->select();
	            if (!empty($series_data)){
	                $js_data .="'".$brand1['brand_id']."' : { ";
	                foreach ($series_data as $series){
    	                $js_data .= "'".$series['series_id']."' : '".$series['series_name']."',";
    	            }
    	            $js_data .= "},";
	            }
	        }
	        $js_data .= " };";
	    }
	    $js_data .= "var data3 = {";
	    if (!empty($brand_data)){
	        foreach ($brand_data as $brand2){
	            $map['brand_id'] = $brand2['brand_id'];
	            $series_data = $model_carseries->where($map)->select();
	            unset($map);
	            if (!empty($series_data)){
	                foreach ($series_data as $series1){
	                    $map['series_id'] = $series1['series_id'];
	                    $model_data = $model_carmodel->where($map)->select();
	                    unset($map);
	                    if (!empty($model_data)){
	                        $js_data .="'".$series1['series_id']."' : { ";
	                        foreach ($model_data as $model){
	                            $js_data .= "'".$model['model_id']."' : '".$model['model_name']."',";
	                        }
	                        $js_data .= "},";
	                    }
    	            }
	            }
	        }
	        $js_data .= "};";
	    }
	    file_put_contents('../PUBLIC/Js/car_select/car_js_data.js',$js_data);
	    echo 'OK';exit;
	}

	public function get_car_xls() {
		$model_brand = D(GROUP_NAME.'/carbrand');
	    $model_carseries = D(GROUP_NAME.'/carseries');
	    $model_carmodel = D(GROUP_NAME.'/carmodel');
		$model_fs = D(GROUP_NAME.'/fs');

		$brand_data = $model_brand->select();
		foreach($brand_data as $b_key=>$b_val) {
			
			$b_map['brand_id'] = $b_val['brand_id'];
			$series_data = $model_carseries->where($b_map)->order('word ASC')->select();

			foreach($series_data as $s_key=>$s_val) {

				$s_map['series_id'] = $s_val['series_id'];
				$model_data = $model_carmodel->where($s_map)->select();
				
				$fs_map['fsid'] = $s_val['fsid'];
				$fs_data = $model_fs->where($fs_map)->find();
				foreach ($model_data as $m_val){

					//$str_table .= '<tr><td>'.$b_val['brand_id'].'</td><td>'.$b_val['brand_name'].'</td><td>'.$s_val['series_id'].'</td><td>'.$s_val['series_name'].'</td><td>'.$m_val['model_id'].'</td><td>'.$m_val['model_name'].'</td>';
					$str_table .= $b_val['brand_id'].",".$b_val['brand_name'].",";
					$str_table .= $s_val['series_id'].",".$s_val['word'].",".$s_val['type'].",".$s_val['sort'].",".$s_val['start_year'].",".$s_val['end_year'].",".$s_val['fsid'].",".$fs_data['fsname'].",";
					$str_table .= $m_val['model_id'].",".$m_val['type'].",".$m_val['model'].",".$m_val['output'].",".$m_val['gearbox']."\n";
				}

			}
		}
	    

	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=车型车系表.csv");
        //$str = '<table><tr><td>品牌ID</td><td>品牌</td><td>车系ID</td><td>车系</td><td>车型ID</td><td>车型</td></tr>';
		$str = "品牌ID,品牌,";
		$str .= "车系ID,首字母,车系型号,种类,投产年份,停产年份,fsid,fsname,";
		$str .= "车型ID,车系型号,车型,排量,变速箱\n";
        $str .= $str_table;
        //$str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}
}
?>