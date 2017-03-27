<?php
// 本类由系统自动生成，仅供测试用途
class Index2Action extends Action {
    public function index(){
        $aData = $this->getCSVdata('./111.csv');  
		//var_dump($aData);  
		echo $aData[0];
    }
	



	function index2(){
		$csvdata = $this->getCsvData2('./111.csv',7);  
		$th = $csvdata[0];
		$data = $csvdata[1];

		//if('索纳塔' == $data[0][0] )
		//dump($data[0]);

		$carmodel = M('Carmodel')->where('model_id > 1000949')->select();//车型信息
		//dump($carmodel);
		$service = M('serviceitem')->select();//服务项目
		$maintainclass = M('maintainclass')->select();//配件
//dump($maintainclass);
		$product_model = M('Product');
		$product_relation_model = M('productrelation');
		//开始循环处理数据
		$product_insert_arr = array();
		foreach($data as $_kr => $row){
			//根据配件组织配件序列化串
			$_peijian = array();
			for($i = 6;$i<=24;$i+=3){
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
			//echo $_carmodel_info;
			$relation_arr = array();
			foreach($carmodel as $c){
				//echo $c['model_name'].'<br>';
				if($c['model_name'] == $_carmodel_info){
					$relation_arr=array(
						'service_id' => $_service_id,
						'service_item_id'=>$_service_item_id,
						'product_id'=>$product_id,
						'car_brand_id'=> 12,//品牌写死了是12
						'car_series_id'=>$c['series_id'],
						'car_model_id'=>$c['model_id'],
						);
				}else{
					$relation_arr=array(
						'service_id' => $_service_id,
						'service_item_id'=>$_service_item_id,
						'product_id'=>$product_id,
						'car_brand_id'=> 12,//品牌写死了是12
						'car_series_id'=>'0',//不匹配则写入0
						'car_model_id'=>'0',//不匹配则写入0
						);
				}
			}
			//dump($relation_arr);
			//exit;
			if(!$product_relation_model->add($relation_arr)){
				echo '写入关系信息出问题了------';
			}
			
			echo '成功写入第 '.$_kr.' 条服务信息<br>';
		}
		
	}
	function index4(){
		$aData = $this->getCsvData2('./111.csv',7);  
		echo ($aData[1][0]);  
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
								if($k==1){//排量保留数字
									$rv = trim($rv,'L');
								}
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
					if($row <7 ){
						continue;
					}
					if($row === 7)//第一行作为字段    
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
  

}