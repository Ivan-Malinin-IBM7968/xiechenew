<?php
class MyhomeModel extends CommonModel {
	
	/*
	 * //where条件  用户指定时间查询
	 * $time str 月份的时间 例如3 最近三个月的统计
	 * $begin_time int 用户指定的开始时间
	 * $end_time int 用户指定的结束时间
	 * Return  Success ：Array  error : ''
	 */
		function __condition_happen_time($time='',$begin_time='',$end_time=''){				
			if($time || ($begin_time && $end_time)){
					//dump($time);		
    			if(!empty($time)){
    				$get_time = get_note_time($time);
    				if(!empty($get_time)){
    					return $get_time;
    				}else{
    					return '';
    				}
    			}
    			if(!empty($begin_time) && !empty($end_time)){
    				$begin_time = strtotime($begin_time);
    				$end_time = strtotime($end_time);
    				if(($begin_time >= $end_time) && ($begin_time < time())){
    					js_back('输入时间错误');
    				}else{
    					$map = array(array('GT',$begin_time),array('LT',$end_time));
    					return $map;
    				}
    			}
			}else{				
				return '';
			}
		}
		
	/*
	 * //显示统计内容
	 * $time str 月份的时间 例如3 最近三个月的统计
	 * $begin_time int 用户指定的开始时间
	 * $end_time int 用户指定的结束时间
	 * Return Array
	 */
		public function list_total($u_c_id,$avgoil_type=1,$time='',$begin_time='',$end_time=''){
			/*
			 * 查询条件预处理
			 */
			$map['u_c_id'] = array('EQ',$u_c_id);
			//查询时间判断
			$get_time = $this->__condition_happen_time($time,$begin_time,$end_time);			
			if(!empty($get_time)){
				$map['happen_time'] = $get_time;
			}
			//数据处理过程
			//$total_tripmile = $this->total_tripmile($map);
			$total_cost = $this->total_cost($map);
			//$total_quantity = $this->total_quantity($map);
			$oil_cost_100 = $this->oil_cost_100($map);
			//$avg_unit_price = $this->avg_unit_price($map);
			$avg_month_cost = $this->avg_month_cost($map);
			$total_cost_100 = $this->total_cost_100($map);
			/*if($avgoil_type == 1){
				//$avg_oil = $this->avg_oil_method_1($map);
			}else{
				$avg_oil = $this->avg_oil_method_2($map);
			}*/
			$oilwear = $this->get_avg_oil($map);
			$avg_oil = $oilwear['oilwear'];
			$avg_unit_price = $oilwear['per_oilprice'];
			$total_tripmile = $oilwear['total_tripmile'];
			$total_quantity = $oilwear['total_quantity'];
			
			$list_everynote_cost=$this->list_everynote_cost($map);
			//将数据处理结果合并为数组
			$total_list = array(
					'total_tripmile'=>$total_tripmile,
					'total_cost'=>$total_cost,
					'total_quantity'=>$total_quantity,
					'oil_cost_100'=>$oil_cost_100,
					'avg_unit_price'=>$avg_unit_price,
					'avg_month_cost'=>$avg_month_cost,
					'total_cost_100'=>$total_cost_100,
					'avg_oil'=>$avg_oil,
					'list_everynote_cost'=>$list_everynote_cost,
				);
				return $total_list;	
		}
	
	
		
		/*
		 * //行驶总里程
		 * $map Array where的数组
		 * Return int 
		 */
		public function total_tripmile($map) {
			$model = D('Noteoil');
			$last_mile = $model->switchModel("Adv")->where($map)->order('oil_id ASC')->last();//最后一条记录
			$first_mile = $model->switchModel("Adv")->where($map)->order('oil_id ASC')->first();//第一条记录	
			if($last_mile['odometer'] && $first_mile['odometer'] && ($last_mile['odometer'] > $first_mile['odometer'])){
				$mile = $last_mile['odometer']-$first_mile['odometer'];
			}else{
				$mile = '';
			}
			return $mile;
			
		}
		/*
		 * //总费用 
		 * 总费用 （除收入费用）
		 * $map Array where的数组
		 * Retrun success : double error : ''
		 * 
		 */
		//
		public function total_cost($map){
			$model = D('Notemain');
			//note_type  10  记账收入编号
			$map['note_type'] = array('neq','10');
			$map['main_del'] = 0;
			$total_cost = $model->where($map)->sum('total_cost');//->
			$first_oilmain = $model ->switchModel("Adv")->where($map)->order('happen_time ASC')->first();
			$total_cost = $total_cost - $first_oilmain['total_cost'];
			if($total_cost){
				return $total_cost;
			}else{
				return '';
			}
			
		}
		/*
		 * //总加油量
		 * $map Array where的数组
		 * Retrun success : double error : ''
		 * 
		 */
		public function total_quantity($map){
			$model = D('Noteoil');	
			$total_quantity = $model -> where($map)->sum('quantity');
			if(!empty($total_quantity)){
				return $total_quantity;
			}else{
				return '';
			}
		}
		/*
		 * //百公里油费
		 * 原型：（总加油费用/总里程数）*100
		 * $map Array where的数组
		 * Retrun success : double error : ''
		 */
		public function oil_cost_100($map){
			$model = D('Notemain');
			//note_type  1  记账加油编号
			$total_mile = $this->total_tripmile($map);
			if($total_mile){
				$map['note_type'] = array('EQ','1');
				$map['main_del'] = 0;
				$total_oil_cost = $model -> where($map)->sum('total_cost');
				$first_oilmain = $model ->switchModel("Adv")->where($map)->order('happen_time ASC')->first();
				$total_oil_cost = $total_oil_cost - $first_oilmain['total_cost'];
				if(!empty($total_oil_cost)){
					$oil_cost_100 = ($total_oil_cost/$total_mile)*100;
					$oil_cost_100 = number_format($oil_cost_100,2);		
					return $oil_cost_100;
				}else{
					return '';
				}
			}else{
				return '';
			}
		}
		/*
		 * //平均油价
		 * 原型：avg(unit_price)
		 * $map Array where的数组
		 * Return success : double error : ''
		 * 
		 */
		public function avg_unit_price($map){
			$model = D('Noteoil');
				$avg_unit_price = $model->where($map)->avg('unit_price');
				if(!empty($avg_unit_price)){
					$avg_unit_price = number_format($avg_unit_price,2);	
					return $avg_unit_price;
				}else{
					return '';
				}
			
		}
		/*
		 * //月均费用
		 * 原型：查询记录的总费用/总月数
		 * $map Array where的数组
		 * Return success : double error : ''
		 */
		public function avg_month_cost($map){
			$model = D('Notemain');
			//note_type  10  记账收入编号
			$map['note_type'] = array('neq','10');
			$map['main_del'] = 0;
			$month_mun = $model ->where($map)->group('cmonth')->select();
			if(!empty($month_mun)){
				$month_mun = count($month_mun);
				$month_cost = $this->total_cost($map);
				if(!empty($month_cost)){
					$avg_month_cost = $month_cost/$month_mun;
					$avg_month_cost = number_format($avg_month_cost,2);
					return $avg_month_cost;
				}else{
					return '';
				}
			}else{
				return '';
			}
		}
		
		/*
		 * //百公里费用
		 * 原型：（查询记录的总费用/总公里数）*100
		 * $map Array where的数组
		 * Return success : double error : ''
		 * 
		 */
		public function total_cost_100($map) {
			$model = D('Notemain');
			$total_cost = $this->total_cost($map);
			$total_mile = $this->total_tripmile($map);
			//echo $total_cost;
			//echo $total_mile;
			if(!empty($total_cost) && !empty($total_mile) ){
				$total_cost_100 = ($total_cost/$total_mile)*100;
				$total_cost_100 = number_format($total_cost_100,2);	
				return $total_cost_100;		
			}else{
				return '';
			}
			
		}
		/*
		 * //记账主表数据调用获取四条记录
		 * 
		 * 
		 */
		public function get_note($type){			
			$model=M('Notemain');
			$list = $model->switchModel("Adv")->where("note_type = $type")->order('happen_time desc')->top4();
			if($list){	
				return $list;
			}else{
				return '';
			}
		}
		/**
		 * 
		 *###### 
		 * @desc 
		 * @return 
		 * @example
		 */
		public function get_avg_oil($map){
		    $model = D('Oilwear');
		    return $model->where($map)->find();
		    
		}
		/*
		 * //平均油耗计算1
		 * 原型：前次油量/行驶里程=每次油耗
		 * 
		 * 
		 */
		public function avg_oil_method_1($map) {
			$model = D('Noteoil');
			$last_2_oil = $model->switchModel("Adv")->where($map)->getN(-2);
			$last_odometer = $model->switchModel("Adv")->where($map)->getN(-1);
			$last_odometer_2 = $model->switchModel("Adv")->where($map)->getN(-2);
			if(!empty($last_2_oil) && !empty($last_odometer) && !empty($last_odometer_2)){
				
				$odometer = $last_odometer['odometer'] - $last_odometer_2['odometer'];
				 $avg_oil_1 = ($last_2_oil['quantity']/$odometer)*100;
				$avg_oil_1 = number_format($avg_oil_1,2);

				return $avg_oil_1;
			}else{
				return '';
			}
		}
		/*
		 * //平均油耗计算2
		 * 原型：本次油量/行驶里程 =每次油耗
		 * 
		 */
		public function avg_oil_method_2($map){
			$model = D('Noteoil');
			$last_2_oil = $model->switchModel("Adv")->where($map)->getN(-1);
			$last_odometer = $model->switchModel("Adv")->where($map)->getN(-1);
			$last_odometer_2 = $model->switchModel("Adv")->where($map)->getN(-2);
			if(!empty($last_2_oil) && !empty($last_odometer) && !empty($last_odometer_2)){
				//dump($last_2_oil);
				//dump($last_odometer);
				$odometer = $last_odometer['odometer'] - $last_odometer_2['odometer'];
				$avg_oil_2 = $last_2_oil['quantity']/$odometer*100;
				$avg_oil_2 = number_format($avg_oil_2,2);
				return $avg_oil_2;
			}else{
				return '';
			}			
			
		}
		
		/*获取用户指定车型的每个记账分类总费用
		 * 
		 * 
		 * 
		 */
		public function list_everynote_cost($map) {
			$model_notemain = D('Notemain');
			$map['main_del'] = 0;
			$list = $model_notemain->field('note_type,SUM(total_cost) AS total_cost')->where($map)->group('note_type')->select();
			
			if(is_array($list)){
			    foreach ($list as $k=>$v){
			        if ($v['note_type'] == 1){
			            $map['note_type'] = 1;
			            $first_oilmain = $model_notemain ->switchModel("Adv")->where($map)->order('happen_time ASC')->first();
				        $list[$k]['total_cost'] = $v['total_cost'] - $first_oilmain['total_cost'];
			        }
			    }
				return $list;
			}else{
				return '';
			}
		}
		
		public function test(){
			echo '111';
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
}