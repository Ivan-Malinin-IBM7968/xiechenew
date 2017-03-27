<?php
// 本类由系统自动生成，仅供测试用途
class ImportshopAction extends Action {
	function import(){
		$linshi7_model = D('Linshi7');
		$shoptmp_model = D('Shop_tmp');
		$shopdetailtmp_model = D('shopdetail_tmp');
		$timesale_model = D('timesale');
		$list = $linshi7_model->select();
		dump($list);
		foreach($list AS $k=>$v){
			$insert_arr = array(
				'shop_class'=>1,
				'fsid'=>$list[$k]['a1'],
				'shop_name'=>$list[$k]['a5'],
				'shop_address'=>$list[$k]['a6'],
				'shop_phone'=>$list[$k]['a7'],

			);
			$insert_id = $shoptmp_model->add($insert_arr);
			$insert_detail_arr = array(
				'shop_id'=>$insert_id,
				'shop_detail'=>'',
			);
			$check = $shopdetailtmp_model->add($insert_detail_arr);
					if($list[$k]['a13'] == '双休日不预约'){
						if($list[$k]['a8'] && $list[$k]['a9']){
							$time_arr =  explode("-", $list[$k]['a9']);
							$workhours_sale = $list[$k]['a8']/10;
							$product_sale = $list[$k]['a12']/10;
							$insert_timesale_arr = array(
								'shop_id'=>	$insert_id,
								'week'=>'1,2,3,4,5',
								'begin_time'=>$time_arr[0],
								'end_time'=>$time_arr[1],
								'workhours_sale'=>$workhours_sale,
								'product_sale'=>$product_sale,
								'status'=>1,
							);
							$timesale_model->add($insert_timesale_arr);
						}
						if($list[$k]['a10'] && $list[$k]['a11']){
							$time_arr =  explode("-", $list[$k]['a11']);
							$workhours_sale = $list[$k]['a10']/10;
							$product_sale = $list[$k]['a12']/10;		
							$insert_timesale_arr = array(
								'shop_id'=>	$insert_id,
								'week'=>'1,2,3,4,5,6,0',
								'begin_time'=>$time_arr[0],
								'end_time'=>$time_arr[1],
								'workhours_sale'=>$workhours_sale,
								'product_sale'=>$product_sale,
								'status'=>1,
							);
							$timesale_model->add($insert_timesale_arr);
						}

					}else{
							$workhours_sale = $list[$k]['a8']/10;
							$product_sale = $list[$k]['a12']/10;
							$insert_timesale_arr = array(
								'shop_id'=>	$insert_id,
								'week'=>'1,2,3,4,5,6,0',
								'begin_time'=>'8:30',
								'end_time'=>'17:00',
								'workhours_sale'=>$workhours_sale,
								'product_sale'=>$product_sale,
								'status'=>1,
							);	
							$timesale_model->add($insert_timesale_arr);
					}



			if($check){
				echo $insert_id.'---success';
			}

		}
	}
}