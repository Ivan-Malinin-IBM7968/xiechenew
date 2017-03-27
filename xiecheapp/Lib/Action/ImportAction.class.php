<?php
// 本类由系统自动生成，仅供测试用途
class ImportAction extends CommonAction {

    public function index(){
		set_time_limit(0);
		$c_note_array = array(
			'易车网-优惠促销--'=>'utf-8',
			'易车网-维修保养--'=>'utf-8',
			'汽车之家-优惠促销-'=>'gb2312',
			'汽车之家-维修保养-'=>'gb2312',
			'汽车之家-近期活动-'=>'gb2312',
		);

		//$data = $this->get_data();
		//echo count($data);exit();
		$shop_model = D('Shop');
		$fs_model = D('Fs');
		$shop_fs_relation_model = D('Shop_fs_relation');
		$dede_co_note_model = M('dedecmsv57utf8sp1.co_note','dede_');
		
		$map['id'] = array(array('gt',1302),array('lt',1322)) ;
		$data = $shop_model->where($map)->select();
		foreach($data as $key=>$val) {
			/*
			$area_info = $this->getAreaInfo($val['3'],$val['4']);
			$fs_info = $fs_model->getByFsname($val['1']);
			$shop_data = array();
			$shop_data['fsid'] = $fs_info['fsid'];
			$shop_data['shop_class'] = 2;
			$shop_data['shop_name'] = $val['5'];
			$shop_data['shop_address'] = $val['6'];
			$shop_data['shop_phone'] = $val['7'];
			$shop_data['shop_prov'] = empty($area_info['shop_prov'])?0:$area_info['shop_prov'];
			$shop_data['shop_city'] = empty($area_info['shop_city'])?0:$area_info['shop_city'];
			$shop_data['shop_area'] = empty($area_info['shop_area'])?0:$area_info['shop_area'];
			$shop_data['create_time'] = time();

			
			$shop_id = $shop_model->add($shop_data);
			
			$fs_data = array();
			$fs_data['shopid'] = $shop_id;
			$fs_data['fsid'] = $fs_info['fsid'];
			$shop_fs_relation_model->add($fs_data);
			//1623
			*/
			$fs_info = $shop_fs_relation_model->getByShopid($val['id']);
			foreach($c_note_array as $kk=>$vv) {
				$c_note_data = array();
				$c_note_data['channelid'] = 1;
				$c_note_data['notename'] = $kk.$val['shop_name'];//$kk.$val['5'];
				$c_note_data['fsid'] = $fs_info['fsid'];
				$c_note_data['shopid'] = $val['id'];//$shop_id;
				if($val['shop_prov'==2913]) {
					$city_name = '北京';
				}else {
					$city_name = '广州';
				}
				$c_note_data['city_name'] = $city_name;//$val['3'];
				$c_note_data['sourcelang'] = $vv;
				$c_note_data['uptime'] = time();
				
				$dede_co_note_model->add($c_note_data);
			}
			
			
		}
			//523
echo("ok");

		//echo $aData[0]['保养项目'];
    }

	function getAreaInfo($city_name,$prov_name) {
		$region_model = D("region");	
		if($city_name == '北京') {
			$shop_prov = '2913';
			$shop_city = '2912';
		}
		if($city_name == '广州') {
			$shop_prov = '2914';
			$shop_city = '2918';
		}
		$region_area = $region_model->where("region_name LIKE '%{$prov_name}%' AND parent_id IN (2918,2912)")->find();
		return array(
			'shop_prov' => $shop_prov,
			'shop_city' => $shop_city,
			'shop_area' => $region_area['id']
		);
	}
	
	function index2(){
		$csvdata = $this->getCsvData2('./new_import_version.csv',7);  
		$th = $csvdata[0];
		$data = $csvdata[1];

		//if('索纳塔' == $data[0][0] )
		//dump($data[0]);

		$carmodel = M('Carmodel')->where('model_id > 1000949')->select();//车型信息
		//dump($carmodel);exit;
		$service = M('serviceitem')->select();//服务项目
		//$service = include('~serviceitem.php');//服务项目
		$maintainclass = M('maintainclass')->select();//配件
//dump($maintainclass);exit;
		$product_model = M('Product');
		$product_relation_model = M('productrelation');
		//开始循环处理数据
		$product_insert_arr = array();
		foreach($data as $_kr => $row){
			//根据配件组织配件序列化串
			$_peijian = array();
			for($i = 4;$i<=25;$i+=3){
				//echo $row[$i];exit;
				if($row[$i] != ''){//配件名不为空
					foreach($maintainclass as $m){//遍历配件
						if($m['ItemName'] == $row[$i]){//名称与当前配件相同
							//exit('配件来了');
							$_peijian[] = array(
								'Big' => $m['PItemID'],
								'Midl' => $m['ItemID'],
								'Midl_name'=>$m['ItemName'],
								'content' => '',//这个没有来源，统一为空
								'price' => intval($row[($i+1)]),
								'quantity' => intval($row[($i+2)]),
							);
						}
					}
				}
			}
			$_product_detail = serialize($_peijian);
			//dump($_product_detail);
			//exit;
			//取得服务信息
			$_service_item_id = 0;
			$_service_id = 0;
			foreach($service as $s){
				if($s['name'] == $row[3]){
					$_service_item_id = $s['service_item_id'];
					$_service_id = $s['id'];
				}
			}

			$_flag = $row[0];
			$_emission = $row[1];
			$_shift = $row[2];

			$pro_save_data = array(
				'flag'=>$_flag,
				'emission'=>$_emission,
				'shift'=>$_shift,
				'product_detail'=>$_product_detail,
				'service_id'=>$_service_id,
				'service_item_id'=>$_service_item_id,
				'brand_id'=>12,//品牌写死了是12
			);
//dump($pro_save_data);
			//exit;
			$product_model->add($pro_save_data);
			//echo ($product_model->getLastSql());
			$product_id = 0;
			$product_id = $product_model->getLastInsID();
			//根据0，1，2拼接查询车型信息
			$_carmodel_info = $row[0].' '.$row[1].' '.$row[2];
//echo $_carmodel_info.'<br>';			
			$relation_arr=array(
				'service_id' => $_service_id,
				'service_item_id'=>$_service_item_id,
				'product_id'=>$product_id,
				'car_brand_id'=> 12,//品牌写死了是12
				'car_series_id'=>'0',//不匹配则写入0
				'car_model_id'=>'0',//不匹配则写入0
				);

			foreach($carmodel as $c){
				if($c['model_name'] == $_carmodel_info){
					$relation_arr=array(
						'service_id' => $_service_id,
						'service_item_id'=>$_service_item_id,
						'product_id'=>$product_id,
						'car_brand_id'=> 12,//品牌写死了是12
						'car_series_id'=>$c['series_id'],
						'car_model_id'=>$c['model_id'],
						);
				}
			}
			//dump($relation_arr);
			//exit;
			if(!$product_relation_model->add($relation_arr)){
				echo '写入关系信息出问题了------';
			}
			
			echo '成功写入第 '.$_kr.' 条服务信息<br>';//.$product_relation_model->getLastSql().'<br>';
		}
		
	}

	
	//$th_row_num  表头所在行，从1开始数
	function getCsvData2($filename,$th_row_num = 1){
		$row_num = 0;
		$file_content = file_get_contents($filename);
		$row_arr = explode("\n",$file_content);
		$data = array();
		$th = array();//表头
		if(!$row_arr){
			return array();
		}else{
			foreach($row_arr as $row => $row_data){
				$row_num ++;
				if($row_num < $th_row_num){
					continue;
				}elseif($row_num == $th_row_num){//头
					$_th = explode(',',$row_data);
					foreach($_th as $k => $v){
						$th[$k] = trim($v);
					}
				}else{//普通数据
					$_data_ = array();
					$_row_arr = explode(',',$row_data);
					if($_row_arr[0]==''){continue;}
					foreach($_row_arr as $rk => $rv){
						foreach($th as $k => $v){
							if($k == $rk){
								/*
								if($k==1){//排量保留数字
									$rv = trim($rv,'L');
								}
								*/
								$_data_[$k] = iconv('GB2312', 'UTF-8', trim($rv));
								//echo $rv.'<br>';
							}
							
						}
					}
					$data[] = $_data_;
				}
			}
		}
		return array($th,$data);
	}


	function getCSVdata($filename){  
		$row = 1;//第一行开始   
		if(($handle = fopen($filename, "r")) !== false){  
		  while(($dataSrc = fgetcsv($handle)) !== false)   
			{  
				$num = count($dataSrc);  
				for ($c=0; $c < $num; $c++)//列 column    
				{  
					if($row === 1)//第一行作为字段    
					{  
						$dataName[] = $dataSrc[$c];//字段名称   
					}  
					else  
					{  
						foreach ($dataName as $k=>$v)  
						{  
							if($k == $c)//对应的字段   
							{  
								$data[$v] = $dataSrc[$c];  
							}  
						}  
					}  
				}  
				if(!empty($data))  
				{  
					 $dataRtn[] = $data;  
					 unset($data);  
				}  
				$row++;  
			}  
			fclose($handle);  
			return $dataRtn;  
		}  
	}  
  
	function import_friendlink() {
		$friendLink = C("FRIENDLYLINK");
		
		$model_link = D("friendlink");
		foreach($friendLink as $key=>$val) {
			$data['name'] = $val['title'];
			$data['link'] = $val['link'];
			$data['addtime'] = time();
			$model_link->add($data);
		}
	}

	function get_product() {
		exit();
		set_time_limit(0);
		$model_product = D('Product');
		$model_productversion = D('Productversion');
		$model_service = D('Serviceitem');
		//$model_fs = D('Fs');
		$model_brand = D(GROUP_NAME.'/Carbrand');
	    $model_series = D(GROUP_NAME.'/Carseries');
	    $model_model = D(GROUP_NAME.'/Carmodel');


		$product = $model_product->select();
		$n=1;
		foreach($product as $key=>$val) {
			$tmp['car_info'] = $this->get_car_info2($val['brand_id'],$val['series_id'],$val['model_id']);
			$tmp['service'] = $model_service->where("id={$val['service_id']}")->getField('name');
			$tmp['service_item'] = $model_service->where("id={$val['service_item_id']}")->getField('name');
			$tmp['product_info'] = unserialize($model_productversion->where("product_id={$val[versionid]} AND status=0")->getField('product_detail'));

			if ($n%2==1){
				$color = "#CCDDDD";
			}else {
				$color = "#FFFFFF";
			}

			$str_table .= '<tr bgcolor='.$color.'><td>'.$tmp['car_info'][0].'</td><td>'.$tmp['car_info'][1].'</td><td>'.$tmp['car_info'][2].'</td><td>'.$tmp['service_item'].'</td><td>'.$tmp['service'].'</td>';
			
			$str_table .= "<td>";
			foreach($tmp['product_info'] as $kk=>$vv) {
				$str_table .= $vv['Midl_name'].":  ".$vv['price']." * ".$vv['quantity'].$vv['unit']." = ".$vv['price']*$vv['quantity']."元<br>";
			}
			$str_table .= "</td>";
			$n++;
			unset($tmp);
		}

		header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=价格表.xls");
		$color = "#00CD34";
        $str = '<table><tr bgcolor='.$color.'><td>品牌</td><td>车系</td><td>车型</td><td>服务类型</td><td>服务项目</td><td>价格明细</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

	function get_car_info2($brand_id,$series_id,$model_id) {
		if ($brand_id){
			$model_carbrand = D('Carbrand');
			$map_b['brand_id'] = $brand_id;
			$brand = $model_carbrand->where($map_b)->find();
		}
		if ($series_id){
			$model_carseries = D('Carseries');
			$map_s['series_id'] = $series_id;
			$series = $model_carseries->where($map_s)->find();
		}
		if ($model_id){
			$model_carmodel = D('Carmodel');
			$map_m['model_id'] = $model_id;
			$model = $model_carmodel->where($map_m)->find();
		}
		return array($brand['brand_name'],$series['series_name'],$model['model_name'] );
	}
}