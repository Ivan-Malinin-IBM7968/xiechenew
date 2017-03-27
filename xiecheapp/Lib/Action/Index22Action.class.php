<?php
// 本类由系统自动生成，仅供测试用途
class Index22Action extends Action {

	function importProduct(){//echo '终止导入';exit;
	    //$linshi_tables = array('linshi5','linshi6','linshi8','linshi9','linshi10','linshi11','linshi12','linshi13','linshi14','linshi15','linshi16','linshi17','linshi18','linshi19','linshi21','linshi22','linshi23','linshi24','linshi25','linshi26','linshi27','linshi28','linshi29');
	    $linshi_tables = array('linshi_ff');
	    foreach ($linshi_tables as $table){
    		$data = M($table)->select();//数据
    		$carmodel = M('Carmodel')->select();//车型信息
    		$carseries = M('Carseries')->select();//车系
    		$service = M('serviceitem')->select();//服务项目
    		$maintainclass = M('maintainclass')->select();//配件
    		$fsdata = M('Fs')->select();//4S店
            
    		$car_s_m = M('Carmodel')->join('xc_carseries ON xc_carseries.series_id = xc_carmodel.series_id')->select();
    		$unkown_peijian = array();//未知配件
    
    		$product_model = M('Product_tmp');
    		//$product_relation_model = M('productrelation_tmp');
    		$productversion_model = M('Productversion_tmp');
    
    		//开始循环处理数据
    		$product_insert_arr = array();
    		foreach($data as $_kr => $row){
    			//根据配件组织配件序列化串
    			$_peijian = array();
    			for($i = 13;$i<44;$i+=4){
    				//echo $row[$i];exit;
    				if($row['a'.$i] != ''){//配件名不为空
    					//$preg_flag = false;//配件名称是否匹配上
    					//foreach($maintainclass as $m){//遍历配件
    						//if($m['ItemName'] == $row['a'.$i]){//名称与当前配件相同
    							//exit('配件来了');
    							$_peijian[] = array(
    								//'Big' => $m['PItemID'],
    								//'Midl' => $m['ItemID'],
    								'Midl_name'=>$row['a'.$i],
    								'content' => '',//这个没有来源，统一为空
    								'price' => $row[('a'.($i+1))],
    								'unit' => $row[('a'.($i+2))],
    								'quantity' => $row[('a'.($i+3))],
    							);
    							//dump($_peijian);
    							//$preg_flag = true;//设置为已经匹配上
    							//break;//匹配上以后中断匹配
    						//}
    					}
    					//if($preg_flag === false){
    						//$unkown_peijian[] = $row['a'.$i];//未匹配上，压入数组
    					//}
    				//}
    			}
    			$_product_detail = serialize($_peijian);
    			//取得服务信息
    			
    			$_service_item_id = 0;
    			$_service_id = 0;
    			foreach($service as $s){
    				if($s['name'] == $row['a12']){
    					$_service_item_id = $s['service_item_id'];
    					$_service_id = $s['id'];
    					break;
    				}
    			}
    
    			$_flag = $row['a4'];
    			$_emission = $row['a9'];
    			$_shift = $row['a10'];
    			
    			//4S店
    			$fsid = null;
    			foreach($fsdata as $fs){
    				if($row['a11'] == $fs['fsname']){
    					$fsid = $fs['fsid'];
    					break;
    				}
    			}
    
    			//拼接查询车型信息
    			if($row['a8']){
    			$_carmodel_info = $row['a4'].' '.$row['a8'].' '.$row['a9'].' '.$row['a10'];
    			}else{
    			$_carmodel_info = $row['a4'].' '.$row['a9'].' '.$row['a10'];
    			}
    			$_series_name = $row['a4'];
    			if ($row['a5']){
    			    $_series_name .= " (".$row['a5'].")";
    			}
    			if ($row['a6']){
    			    $_series_name .= " ".$row['a6']."-".$row['a7'];
    			}
    			//车系和品牌
    			$_carmodel_id = null;
    			$_series_id = null;
    			$_brand_id = null;
    			//车系和品牌信息
    			foreach($car_s_m as $c){
    			    //$series = M('Carseries')->where("series_id=$c[series_id]")->find();
    				//if($c['model_name'] == $_carmodel_info and $c['series_name']==$_series_name){
					if($c['model_name'] == '哈弗H6 1.5L MT' and $c['series_name']=='哈弗H6'){
    					$_series_id = $c['series_id'];
    					$_carmodel_id = $c['model_id'];
    					//根据车系找品牌
    					foreach($carseries as $cs){
    						if($_series_id == $cs['series_id']){
    							$_brand_id = $cs['brand_id'];
    							break 2;//找到品牌，直接中断carmodel循环
    						}
    						
    					} 
    					break;//中断carmodel循环
    				}
    			}
                
    			
    			$pro_save_data = array(
    				'flag'=>$_flag,
    				'emission'=>$_emission,
    				'shift'=>$_shift,
    				'product_detail'=>$_product_detail,
    				'service_id'=>$_service_id,
    				'service_item_id'=>$_service_item_id,
    				'brand_id'=>$_brand_id,
    				'series_id'=>$_series_id,
    				'model_id'=>$_carmodel_id,
					//'fsid'=>'15',
    				'fsid'=>$fsid,
    			);
    			//dump($pro_save_data);
    
    			if(!$product_model->add($pro_save_data)){
    			    echo 'aaaaa';echo $_series_id.'====>';echo $_brand_id;echo 'bbbb';
    			    echo $_carmodel_info.'--->'.$_series_name;
    				
    				//$this->printUnkownPeijian($unkown_peijian);
    				exit('写入第'.$_kr.'条产品信息出错，被迫终止导入1');
    			}
    			//dump($product_model->getLastSql());
    			
    			$product_id = 0;
    			$product_id = $product_model->getLastInsID();
    			
                $pro_version_data = array(
    			    'product_id'=>$product_id,
    			    'product_detail'=>$_product_detail,
    			);
    		    if(!$productversion_model->add($pro_version_data)){
    				//$this->printUnkownPeijian($unkown_peijian);
    				exit('写入第'.$_kr.'条关系信息出错，被迫终止导入');
    			}else {
    			    $versionid = 0;
    			    $versionid = $productversion_model->getLastInsID();
    			}
    			$save_data['versionid'] = $versionid;
    			$save_map['id'] = $product_id;
    			$product_model->where($save_map)->save($save_data);
    			/*$relation_arr=array(
    				'service_id' => $_service_id,
    				'service_item_id'=>$_service_item_id,
    				'product_id'=>$product_id,
    				'car_brand_id'=> $_brand_id,
    				'car_series_id'=>$_series_id,
    				'car_model_id'=>$_carmodel_id,
    				'fsid'=>$fsid,
    				);
    
    			if(!$product_relation_model->add($relation_arr)){
    				//$this->printUnkownPeijian($unkown_peijian);
    				exit('写入第'.$_kr.'条关系信息出错，被迫终止导入');
    			}*/
    		}
    		//$this->printUnkownPeijian($unkown_peijian);
    		
    		echo '<br><hr>数据已全部导入---->table=>'.$table;
	    }
	}



	function printUnkownPeijian($unkown_peijian){
	    //echo '<pre>';print_r($unkown_peijian);
		$unkown_peijian = array_unique($unkown_peijian);
		if(!empty($unkown_peijian)){
			foreach($unkown_peijian as $key=>$up){echo $key.'------>';
				echo '配件：“<span style="color:red;">'.$up.'</span>” 在配件库中没有匹配项<br>';
			}
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

	/*
		添加新车辆数据
	*/
	function save(){
		exit;
		$producttmp_model = M('Product_tmp');	
    	$productversiontmp_model = M('productversion_tmp');
		$product_model = M('Product');
		$productversion_model = M('Productversion');
		$data = $producttmp_model->limit('120,18')->select();
		/*
		foreach($data as $k=>$v){
			$array['flag'] = $v['flag'];
			$array['emission'] = $v['emission'];
			$array['shift'] = $v['shift'];
			$array['product_detail'] = $v['product_detail'];
			$array['service_id'] = $v['service_id'];
			$array['service_item_id'] = $v['service_item_id'];
			$array['brand_id'] = $v['brand_id'];
			$array['series_id'] = $v['series_id'];
			$array['model_id'] = $v['model_id'];
			$array['fsid'] = $v['fsid'];
			$id = $product_model->add($array);
			//echo $product_model->getlastSql();exit;
			$product_model->where(array('id'=>$id))->save(array('versionid'=>$id));
			
		}
		*/
		
		foreach($data as $k=>$v){
			$array['product_id'] = $v['id'];
			$array['product_detail'] = $v['product_detail'];
			$array['status'] = 0;
			
			$ids = $productversiontmp_model->add($array);
			//echo $product_model->getlastSql();exit;
		//	$product_model->where(array('id'=>$ids))->save(array('versionid'=>$ids));
		
		}
		
		echo "ok";
	}

}