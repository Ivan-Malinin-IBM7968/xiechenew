<?php

namespace Xiecheapp\Controller;

class CarserviceController extends CommonController {
		function __construct() {
	
			parent::__construct();
	
			$this->assign('current','carservice');
			$this->assign('meta_keyword',"汽车保养,汽车维修,4S店预约保养,汽车保养预约,汽车维修预约");
			$this->assign('description',"汽车保养维修,事故车维修首选携车网,在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
	
			$this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段
			$this->carservicecode_model2 = M('tp_xieche.carservicecode_2','xc_');//上门保养抵用码字段
			$this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
			$this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
			$this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
			$this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目
	
			$this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
			$this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
	
			$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
	
			$this->user_model = M('tp_xieche.member', 'xc_');//用户表
			$this->memberlog_model = D('Memberlog');  //用户日志表
	
			$this->sms_model = D('Sms');  //短信表
			$this->membercar_model = D('Membercar');
            
            $this->city = D('city');   //城市表
            $this->item_city = D('item_city');   //新城市表
            $this->item_area = D('item_area');   //地区表
            
            $this->new_carbrand = D('new_carbrand');   //新品牌表
            $this->new_carseries = D('new_carseries');   //新车系表
            $this->new_carmodel = D('new_carmodel');   //新车型表
        
            $this->new_oil = D('new_item_oil');  //保养机油        
            $this->new_filter = D('new_item_filter');  //保养三滤配件
		}
	
		/**
		 * 上门养车首页
		 * @author  bright
		 * @date  2014/8/22
		 * @version  1.0.0
		 */
		public function index(){
			$title = "汽车上门保养【府上养车】-携车网";
			$meta_keyword = "上门保养,汽车上门保养,府上养车,上门汽车保养";
			$description = "汽车上门保养找携车网上门仅需99正品配件京东价,拥有资深4S店技师和1800种车型标配工具,38项爱车深度体检,电话,APP,微信召之即来,车在哪,保养就到哪!";
			$this->assign("title", $title);
	
			//车型
			$style_param['model_id'] = $_SESSION['modelId'];
			$car_model = $this->new_carmodel->where($style_param)->find();
			$style_name = $car_model['model_name'];
			$car_name = $car_model['model_name'];
			
			if($car_model){
				$model_param['series_id'] = $car_model['series_id'];
				$car_carseries = $this->new_carseries->where($model_param)->find();
			
				if($car_carseries){
					$model_name = $car_carseries['series_name'];
					$car_name = $car_carseries['series_name']." - ".$car_name;
			
					$brand_param['brand_id'] = $car_carseries['brand_id'];
					$car_brand = $this->new_carbrand->where($brand_param)->find();
					if($car_brand){
						$brand_name = $car_brand['brand_name'];
						$car_name = $car_brand['brand_name']." - ".$car_name;
					}
				}
			}
			$_SESSION['car_name'] = $car_brand['brand_name'];
			$this->assign('car_name', $car_name);
			$this->assign('brand_name', $brand_name);
			$this->assign('model_name', $model_name);
			$this->assign('style_name', $style_name);
			
			//机油
			$oil_num = ceil($car_model['oil_mass']);
			if ($oil_num <4) {
				$oil_num = 4;
			}
			if( $oil_num >3 ){
				$xx = $oil_num%4;
				$yy = ($oil_num - $xx)/4;
				if($xx){
					$norms = $yy+$xx."L";
				}else{
					$norms = $yy."L";
				}
			}else{
				$xx = $oil_num;
				$yy = 0;
				$norms = $oil_num . "L";
			}
			if( $oil_num <4 ){
				$car_model['norms'] = '4L';
			}else{
				$car_model['norms'] = $oil_num."L";//多少升机油
			}
			
			//所有物品详情
			//$oil_list_all = $this->item_oil_model->where()->select();
            $oil_list_all = $this->new_oil->where()->select();
			$oil_item = array();
			foreach( $oil_list_all as $nors){
				$nors['name'] = rtrim($nors['name'],'装');
				$oil_item[$nors['name']][$nors['norms']] = $nors;
			}
			
			$oil_param = array();
			//根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
			switch ($car_model['oil_type']){
				case a:
					$oil_param['type'] = array('in','1,2,3');
					break;
				case b:
					$oil_param['type'] = array('in','2,3');
					break;
				case c:
					$oil_param['type'] = 3;
					break;
				default:
					$oil_param['type'] = 0;
					break;
			}
			//$oil_param['norms'] = $oil_num;	//需要多少升的机油
			$oil_param['norms'] = 4;	//需要多少升的机油
			//符合要求的品牌详情
			$item_sets=array();
			//$oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select();
			$oil_name_distinct = $this->new_oil->order('price')->where($oil_param)->select();
            
			foreach( $oil_name_distinct as $keys=>$names ){
				
				$names['name'] = rtrim($names['name'],'装');
				
				$item_sets[$keys]['id'] = $names['id'];
				$item_sets[$keys]['name'] = $names['name'];
				$item_sets[$keys]['type'] = $names['type'];
				//规格1L详情
				$item_sets[$keys]['oil_1'] = $oil_item[$names['name']]['1']['id'];
				$item_sets[$keys]['oil_1_num'] = $xx;
				$item_sets[$keys]['oil_1_price'] = $oil_item[$names['name']]['1']['price'];
				//规格4L详情
				$item_sets[$keys]['oil_2'] = $oil_item[$names['name']]['4']['id'];
				$item_sets[$keys]['oil_2_num'] = $yy;
				$item_sets[$keys]['oil_2_price'] = $oil_item[$names['name']]['4']['price'];
			
				//计算总价
				$item_sets[$keys]['price'] = $xx*$oil_item[$names['name']]['1']['price']+$yy*$oil_item[$names['name']]['4']['price'];
				
			}
			//按照总价钱排序
			usort($item_sets, function($a,$b){
				if ($a['price'] == $b['price']) {
					return 0;
				}
				return ($a['price'] < $b['price']) ? -1 : 1;
			});
			$car_model['type'] = '未设定';
			//推荐栏位
			$recommend_low = array(
					'id' => $item_sets[0]['id'],
					'name'=>$item_sets[0]['name'],
					'price'=>$item_sets[0]['price']
			);
			if($car_model['oil_type']=='a'){
				$recommend_low['type'] = $car_model['type'] = '矿物油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[2]['id'],
						'name'=>$item_sets[2]['name'],
						'type'=>'半合成油',
						'price'=>$item_sets[2]['price']
				);
			
			}
			if($car_model['oil_type']=='b'){
				$recommend_low['type'] = $car_model['type'] = '半合成油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[2]['id'],
						'name'=>$item_sets[2]['name'],
						'type'=>'全合成油',
						'price'=>$item_sets[2]['price']
				);
			}
			if($car_model['oil_type']=='c'){
				$recommend_low['type'] = $car_model['type'] = '全合成油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[3]['id'],
						'name'=>$item_sets[3]['name'],
						'type'=>'全合成油',
						'price'=>$item_sets[3]['price']
				);
			}
			$tmpArr = $item_sets;
			$recommend_high = array_pop($tmpArr);
			if ( isset($recommend_high['type']) ) {
				if ($recommend_high['type'] == 3) {
					$recommend_high['type'] = '全合成油';
				}elseif ($recommend_high['type'] == 2){
					$recommend_high['type'] = '全合成油';
				}elseif ($recommend_high['type'] == 1){
					$recommend_high['type'] = '半合成油';
				}
			}
			
			//项目
			$model_id = @$_SESSION['modelId'];
			$item_set = array();
			if( $model_id ){
				$condition['model_id'] = $model_id;
				$style_info = $this->new_carmodel->where($condition)->find();
				$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
                
				if( $set_id_arr ){
					foreach( $set_id_arr as $k=>$v){
						if(is_array($v)){
							foreach( $v as $_k=>$_v){
								$item_condition['id'] = $_v;
                                //价格不为零
                                $item_condition['price'] = array('neq','0');
                                
								//$item_info_res = $this->filter_model->where($item_condition)->find();
                                $item_info_res = $this->new_filter->where($item_condition)->find();
                                
                                if(!$item_info_res){
                                    continue;
                                }
                                //去掉品牌后面的规格 by bright
                                $item_info_res['name'] = $this->getBrandName($item_info_res['name']);
                                //只显示四个品牌   wql@20150928
                                $show_brand = array( 
                                    '1'=>'曼牌',
                                    '2'=>'马勒',
                                    '3'=>'博世',
                                    '4'=>'索菲玛'
                                );
                                if(!in_array($item_info_res['name'], $show_brand)){
                                    continue;
                                }
                                
                                
                                $item_info['id'] = $item_info_res['id'];
                                $item_info['name'] = $item_info_res['name'] ;
                                $item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
                                $item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
                                $item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
                                $item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;        

								
                                
								$item_set[$k][$_k] = $item_info;
								//排除数组中缺乏元素页面选项空白的问题
								if(!$item_set[$k][0] and $item_set[$k][1]){
									$item_set[$k][0] = $item_set[$k][1];
									unset($item_set[$k][1]);
								}
									
							}
                            
//							foreach($item_set[$k] as $kk=>$vv){
//								if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
//									$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
//									$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
//								}else{
//									$item_set_new[$k][$kk] = $item_set[$k][$kk];
//								}
//							}
                            //重置索引
                            $item_set[$k] =  array_values($item_set[$k]) ;
                            
                             // wql && jichi @20150923
                            $price = array() ;
                            foreach ($item_set[$k] as $kk => $vv) {
                                $price[$kk] = $vv['price'] ;
                            }
                            asort($price) ;
                            foreach($price as $kkk =>$vvv){
                               $item_set_new[$k][] =  $item_set[$k][$kkk] ;
                            }
                            
							$item_set = $item_set_new;
						}
					}
				}
			}
            
            //print_r($item_set[1]) ;exit ;
           
            
           
			//文章
			$Article = D("article");
			$Notice = D("notice");
			$arlist1 = $Article->where('status=1 AND type=1')->order('id desc,status')->limit(0,5)->select();
			$this->assign('arlist1', $arlist1);

			$arlist2 = $Article->where('status=1 AND type=2')->order('id desc,status')->limit(0,5)->select();
			$this->assign('arlist2', $arlist2);

			$notice = $Notice->where('status=1')->order('id desc,status')->limit(0,5)->select();
			$this->assign('notice', $notice);

			$recommend_low['price'] += $item_set[1][0]['price'] + 99;
			if($_SESSION['city']) {
				$city_model = D('city');
				$city_map['id'] = $_SESSION['city'];
				$city_info = $city_model->where($city_map)->find();
				$this->assign('new_city_name', $city_info['name']);
			}
			$this->assign('recommend_low', $recommend_low);
			$this->assign("meta_keyword", $meta_keyword);
			$this->assign("description", $description);
			$this->assign('noshow',true);
			$this->assign('noclose',true);
			$this->display('index');
		}
	
		/**
		 * 订单详情
		 * @author  bright
		 * @date  2014/8/22
		 * @version  1.0.1
		 */
		public function order(){
			if($_SESSION['city']) {
				$city_model = D('city');
				$city_map['id'] = $_SESSION['city'];
				$city_info = $city_model->where($city_map)->find();
				$this->assign('new_city_name', $city_info['name']);
				$this->assign('city_abbreviation',$city_info['abbreviation']);
			}
			//车型
			$style_param['model_id'] = $_SESSION['modelId'];
			$car_model = $this->new_carmodel->where($style_param)->find();
			$style_name = $car_model['model_name'];
			$car_name = $car_model['model_name'];
	
			if($car_model){
				$model_param['series_id'] = $car_model['series_id'];
				$car_carseries = $this->new_carseries->where($model_param)->find();
	
				if($car_carseries){
					$model_name = $car_carseries['series_name'];
					$car_name = $car_carseries['series_name']." - ".$car_name;
	
					$brand_param['brand_id'] = $car_carseries['brand_id'];
					$car_brand = $this->new_carbrand->where($brand_param)->find();
					if($car_brand){
						$brand_name = $car_brand['brand_name'];
						$car_name = $car_brand['brand_name']." - ".$car_name;
					}
				}
			}
			$_SESSION['car_name'] = $car_brand['brand_name'];
			$this->assign('car_name', $car_name);
			$this->assign('brand_name', $brand_name);
			$this->assign('model_name', $model_name);
			$this->assign('style_name', $style_name);
	
			//机油
			$oil_num = ceil($car_model['oil_mass']);
			if ($oil_num <4) {
				$oil_num = 4;
			}
			if( $oil_num >3 ){
				$xx = $oil_num%4;
				$yy = ($oil_num - $xx)/4;
				if($xx){
					$norms = $yy+$xx."L";
				}else{
					$norms = $yy."L";
				}
			}else{
				$xx = $oil_num;
				$yy = 0;
				$norms = $oil_num . "L";
			}
			if( $oil_num <4 ){
				$car_model['norms'] = '4L';
			}else{
				$car_model['norms'] = $oil_num."L";//多少升机油
			}
	
			//所有物品详情
			$oil_list_all = $this->new_oil->where()->select();
			$oil_item = array();
			foreach( $oil_list_all as $nors){
				$nors['name'] = rtrim($nors['name'],'装');
				$oil_item[$nors['name']][$nors['norms']] = $nors;
			}
// 	var_dump($oil_item);
			$oil_param = array();
			//根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
			switch ($car_model['oil_type']){
				case a:
					$oil_param['type'] = array('in','1,2,3');
					break;
				case b:
					$oil_param['type'] = array('in','2,3');
					break;
				case c:
					$oil_param['type'] = 3;
					break;
				default:
					$oil_param['type'] = 0;
					break;
			}
			//$oil_param['norms'] = $oil_num;	//需要多少升的机油
			$oil_param['norms'] = 4;	//需要多少升的机油
			//符合要求的品牌详情
			$item_sets=array();
			$oil_name_distinct = $this->new_oil->order('price')->where($oil_param)->select();
	
			foreach( $oil_name_distinct as $keys=>$names ){
				if($car_model['oil_mass']>4){}
				$names['name'] = rtrim($names['name'],'装');
				
				$item_sets[$keys]['id'] = $names['id'];
				$item_sets[$keys]['name'] = $names['name'];
				$item_sets[$keys]['type'] = $names['type'];
				//规格1L详情
				$item_sets[$keys]['oil_1'] = $oil_item[$names['name']][1]['id'];
				$item_sets[$keys]['oil_1_num'] = $xx;
				$item_sets[$keys]['oil_1_price'] = $oil_item[$names['name']][1]['price'];
				//规格4L详情
				$item_sets[$keys]['oil_2'] = $oil_item[$names['name']][4]['id'];
				$item_sets[$keys]['oil_2_num'] = $yy;
				$item_sets[$keys]['oil_2_price'] = $oil_item[$names['name']][4]['price'];
	
				//计算总价
				$item_sets[$keys]['price'] = $xx*$oil_item[$names['name']][1]['price']+$yy*$oil_item[$names['name']][4]['price'];
				//全合成灰壳放在最前面
// 				if ($names['id'] == 45 && $car_model['oil_type'] == 'c') {
// 					$qiaopai = $item_sets[$keys];
// 					unset($item_sets[$keys]);
// 				}
// 				//半合成蓝壳放在最前面
// 				if ($names['id'] == 47 && $car_model['oil_type'] == 'b') {
// 					$qiaopai = $item_sets[$keys];
// 					unset($item_sets[$keys]);
// 				}
				//干掉4升以上金嘉护的选项
				if($car_model['oil_mass']>4 and $names['id']==56){ unset($item_sets[$keys]); }
			}
			//var_dump($item_sets);
			//按照总价钱排序
			usort($item_sets, function($a,$b){
				if ($a['price'] == $b['price']) {
					return 0;
				}
				return ($a['price'] < $b['price']) ? -1 : 1;
			});
// 			var_dump($item_sets);
			//壳牌放在最前面
// 			if (isset($qiaopai)) {
// 				array_unshift($item_sets,$qiaopai);
// 			}
	
			$car_model['type'] = '未设定';
			//推荐栏位
			$recommend_low = array(
					'id' => $item_sets[0]['id'],
					'name'=>$item_sets[0]['name'],
					'price'=>$item_sets[0]['price']
			);
			if($car_model['oil_type']=='a'){
				$recommend_low['type'] = $car_model['type'] = '矿物油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[2]['id'],
						'name'=>$item_sets[2]['name'],
						'type'=>'半合成油',
						'price'=>$item_sets[2]['price']
				);
	
			}
			if($car_model['oil_type']=='b'){
				$recommend_low['type'] = $car_model['type'] = '半合成油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[2]['id'],
						'name'=>$item_sets[2]['name'],
						'type'=>'全合成油',
						'price'=>$item_sets[2]['price']
				);
			}
			if($car_model['oil_type']=='c'){
				$recommend_low['type'] = $car_model['type'] = '全合成油';
				$recommend_high = array(	//推荐栏位
						'id' => $item_sets[3]['id'],
						'name'=>$item_sets[3]['name'],
						'type'=>'全合成油',
						'price'=>$item_sets[3]['price']
				);
			}
			$tmpArr = $item_sets;
			$recommend_high = array_pop($tmpArr);
			if ( isset($recommend_high['type']) ) {
				if ($recommend_high['type'] == 3) {
					$recommend_high['type'] = '全合成油';
				}elseif ($recommend_high['type'] == 2){
					$recommend_high['type'] = '全合成油';
				}elseif ($recommend_high['type'] == 1){
					$recommend_high['type'] = '半合成油';
				}
			}
			//项目
			$model_id = @$_SESSION['modelId'];
			$item_set = array();
			if( $model_id ){
				$condition['model_id'] = $model_id;
				$style_info = $this->new_carmodel->where($condition)->find();
				$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

				if( $set_id_arr ){
					foreach( $set_id_arr as $k=>$v){
						if(is_array($v)){
							foreach( $v as $_k=>$_v){
                                    //价格不为零
                                    $item_condition['price'] = array('neq','0');
                                    
									$item_condition['id'] = $_v;
									$item_condition['name'] = array('notlike','%pm2%');
									$item_info_res = $this->new_filter->where($item_condition)->find();
                                    if(!$item_info_res){
                                        continue;
                                    }
									$item_info['id'] = $item_info_res['id'];
                                    
									//去掉品牌后面的规格 by bright
                                    $item_info_res['name'] = $this->getBrandName($item_info_res['name']);
                                    //只显示四个品牌   wql@20150928
                                    $show_brand = array( 
                                        '1'=>'曼牌',
                                        '2'=>'马勒',
                                        '3'=>'博世',
                                        '4'=>'索菲玛'
                                    );
                                    if(!in_array($item_info_res['name'], $show_brand)){
                                        continue;
                                    }
                                    
                                    
									$item_info['name'] = $item_info_res['name'];
									$item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
									$item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
									$item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
									$item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
									$item_set[$k][$_k] = $item_info;
									//排除数组中缺乏元素页面选项空白的问题
									if(!$item_set[$k][0] and $item_set[$k][1]){
										$item_set[$k][0] = $item_set[$k][1];
										unset($item_set[$k][1]);
									}
								
							}
                            
                            //重置索引
                            $item_set[$k] =  array_values($item_set[$k]) ;
                            
//							foreach($item_set[$k] as $kk=>$vv){ 
//								if($item_set[$k][$kk]['price'] < $item_set[$k][$kk-1]['price']){
//									$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
//									$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
//								}else{
//									$item_set_new[$k][$kk] = $item_set[$k][$kk];
//								}
//                                if($k==1){
//                                    echo   $kk ;
//                                    print_r($item_set_new) ;
//                                }
//                                
//							}
                            // wql && jichi @20150923
                            $price = array() ;
                            foreach ($item_set[$k] as $kk => $vv) {
                                $price[$kk] = $vv['price'] ;
                            }
                            asort($price) ;
                            foreach($price as $kkk =>$vvv){
                               $item_set_new[$k][] =  $item_set[$k][$kkk] ;
                            }
                        
							$item_set = $item_set_new;
                            
    
						}
					}
				}
			}
            
            
                    
            //exit ;
            
            
			$recommend_high['price'] += $item_set[1][0]['price'] +99;
			$recommend_low['price'] += $item_set[1][0]['price'] + 99;
			if(isset($_SESSION['item_0'])){
				$this->assign('item_0', $_SESSION['item_0']);
			}
			if(isset($_SESSION['item_1'])){
				$this->assign('item_1', $_SESSION['item_1']);
			}
			if(isset($_SESSION['item_2'])){
				$this->assign('item_2', $_SESSION['item_2']);
			}
			if(isset($_SESSION['item_3'])){
				$this->assign('item_3', $_SESSION['item_3']);
			}
			if(isset($_SESSION['uid']) && isset($_SESSION['mobile'])){
				$map['uid'] =$_SESSION['uid'];
				$map['mobile'] =$_SESSION['mobile'];
				$order_info = $this->reservation_order_model->where($map)->order('id desc')->field('truename,mobile,licenseplate,address,vin_num')->find();
				if($order_info){
					$order_info['license_plate'] = substr($order_info['licenseplate'],0,3);
					$order_info['license_plate_num'] = substr($order_info['licenseplate'],3);
				}
			}
            
            //print_r($item_set) ;
            
			$this->assign('order_info',$order_info);
			$this->assign('recommend_low', $recommend_low);
			$this->assign('recommend_high', $recommend_high);
			$this->assign('car_style', $car_model);
			$this->assign('item_sets', $item_sets);
			$this->assign('item_set', $item_set);
            $this->assign('city_id', $_SESSION['city']);
			$this->assign('title',"选择项目-上门保养-携车网");
			$this->display('order');
		}
        
        
        //获取配件的品牌  wql@20150821
        public function getBrandName($item_name) {
            $filterArr = array(
                '1'=>'曼牌',
                '2'=>'马勒',
                '3'=>'博世',
                '4'=>'索菲玛',
                '5'=>'方牌',
                '6'=>'豹王',
                '7'=>'耐诺思',
                '8'=>'AC德科',
                '9'=>'汉格斯特',
                '10'=>'韦斯特',
                '11'=>'冠军',
                '12'=>'Denso', 
                '13'=>'J-WORKS',
                '14'=>'K&N',  
            ) ;
            
            foreach ($filterArr as $k => $v) {
                if(mb_strstr($item_name,$v)){
                    $brandName = $v ;    
                }
            }
            
            return $brandName ;
            
        }

		/**
		 * 优惠速递文章列表
		 */

		public function nlist(){

			$data['status'] = '1';

			$notice = D("notice");
			$count = $notice->where($data)->count();// 查询满足要求的总记录数
			import("Org.Util.Page");
			// 实例化分页类
			$Page = new \Page($count, 20);
			$show = $Page->show();// 分页显示输出
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $notice->where($data)->order('update_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			$show = strtolower($show);
			$this->assign("list",$list);
			$this->assign('page',$show);
			$this->assign('title',"优惠速递-上门保养-携车网");
			$this->assign("noshow",true);
			$this->assign("noclose",true);
			$this->display();
		}

		/**
		 * 文章列表
		 */

		public function alist(){

			$data['status'] = '1';

			if($_GET['type']){
				$data['type'] = $_GET['type'];
			}
			// var_dump($data);
			if($data['type']==1){
				$typename = '养车心得';
			}else if($data['type']==2){
				$typename = '府上养车专题';
			}else{
				$typename = '携车资讯';
			}
			$Article = D("article");
			$count = $Article->where($data)->count();// 查询满足要求的总记录数
			//$Page = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
			import("Org.Util.Page");
			// 实例化分页类
			$Page = new \Page($count, 20);
			$show = $Page->show();// 分页显示输出
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $Article->where($data)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			$show = strtolower($show);
			$this->assign("list",$list);
			$this->assign('page',$show);
			$this->assign('typename',$typename);
			$this->assign('title',$typename."-上门保养-携车网");
			$this->assign("noshow",true);
			$this->assign("noclose",true);
			$this->display();
		}


		/**
		 * 文章显示
		 */

		public function article(){
			if(!$_GET['id']){
				$this->error("参数错误");
			}
			
			$data['id'] = $_GET['id'];
			$data['status'] = 1;
			$Article = D("article");
			$content = $Article->where($data)->find();
			if(!$content){
				$this->error("参数错误");
			}
			if($content['type']==2){
				//上一篇
				$prev = $Article->where('id<'.$content['id'].' AND status = 1 AND type = 2')->order('id desc')->find();
				//下一篇
				$next = $Article->where('id>'.$content['id'].' AND status = 1 AND type = 2')->order('id asc')->find();
			}elseif($content['type']==1){
				//上一篇
				$prev = $Article->where('id<'.$content['id'].' AND status = 1 AND type = 1')->order('id desc')->find();
				//下一篇
				$next = $Article->where('id>'.$content['id'].' AND status = 1 AND type = 1')->order('id asc')->find();
			}else{
				//上一篇
				$prev = $Article->where('id<'.$content['id'].' AND status = 1 ')->order('id desc')->find();
				//下一篇
				$next = $Article->where('id>'.$content['id'].' AND status = 1 ')->order('id asc')->find();
			}
			$this->assign("prev",$prev);
			$this->assign("next",$next);
			$this->assign("content",$content);
			$this->assign('title',$content['title']."-上门保养-携车网");
			$this->assign("noshow",true);
			$this->assign("noclose",true);
			$this->display();
		}

		/**
		 * 优惠速递显示
		 */

		public function narticle(){
			if(!$_GET['id']){
				$this->error("参数错误");
			}
		
			$data['id'] = $_GET['id'];
			$data['status'] = 1;
			$notice = D("notice");
			$content = $notice->where($data)->find();
			if(!$content){
				$this->error("参数错误");
			}
			//上一篇
			$prev =$notice->where('id<'.$content['id'].' AND status = 1')->order('id desc')->find();
			//下一篇
			$next = $notice->where('id>'.$content['id'].' AND status = 1')->order('id asc')->find();
			$this->assign("prev",$prev);
			$this->assign("next",$next);
			$this->assign("content",$content);
			$this->assign('title',$content['noticetitle']."-上门保养-携车网");
			$this->assign("noshow",true);
			$this->assign("noclose",true);
			$this->display();
		}
	
		/**
		 * 发送验证码
		 * @author  chaozhou
		 * @date  2014/8/24
		 * @version  1.0.0
		 */
		function giveeverify(){
			session_start();
	
			$mobile = I('post.mobile');
			$_SESSION['mobileeverify'] = rand(10000,999999);
			$userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();
	
			if($userinfo['mobile'] == $mobile || true){
				$send_add_order_data = array(
						'phones'=>$mobile,
						'content'=>"您上门预约订单的验证码为：".$_SESSION['mobileeverify'],
				);
				$this->curl_sms($send_add_order_data);  //Todo 内外暂不发短信
	
				$return['errno'] = '0';
				$return['errmsg'] = 'success';
				$this->ajaxReturn($return);
			}else{
				$return['errno'] = '1';
				$return['errmsg'] = '很抱歉，手机号码与用户不一致';
				$this->ajaxReturn($return);
			}
		}
	
		/**
		 * 提交订单
		 * @author  bright
		 * @date  2014/11/03
		 * @version  1.0.0
		 */
	
		public function create_order(){
			//判断是否是同一个人下单子,防止客服下两个人的单子出现uidbug
			if ($_SESSION['ck_mobile'] != I('post.mobile') || ( $_SESSION['ck_name'] != I('post.truename') ) ) {
				unset($_SESSION['uid']);
			}
			if (!$_SESSION['modelId']) {
				$this->error('请先选择车型',null,'/carservice/order');
			}
			unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
			 
			if(I('post.CheckboxGroup0_res')){
				$_SESSION['item_0'] = intval(I('post.CheckboxGroup0_res'));
				$_SESSION['oil_detail'] = array(
						I('post.oil_1_id') => I('post.oil_1_num'),
						I('post.oil_2_id') => I('post.oil_2_num')
				);
			}
			if(I('post.CheckboxGroup1_res')){
				$_SESSION['item_1'] = intval(I('post.CheckboxGroup1_res'));
			}
			if(I('post.CheckboxGroup2_res')){
				$_SESSION['item_2'] = intval(I('post.CheckboxGroup2_res'));
			}
			if(I('post.CheckboxGroup3_res')){
				$_SESSION['item_3'] = intval(I('post.CheckboxGroup3_res'));
			}
	
			if($_SESSION['uid']){
			}else{
				$userinfo = $this->user_model->where(array('mobile'=>I('post.mobile'),'status'=>'1'))->find();
				if($userinfo){
					$_SESSION['uid'] = $userinfo['uid'];
				}else{
					$member_data['mobile'] = I('post.mobile');
					$member_data['password'] = md5(I('post.mobile'));
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$member_data['fromstatus'] = '51';//WEB上门保养
					$_SESSION['uid'] = $this->user_model->data($member_data)->add();
					$send_add_user_data = array(
							'phones'=>I('post.mobile'),
							'content'=>'您已注册成功，您可以使用您的手机号码'.I('post.mobile').'，密码'.I('post.mobile').'来登录携车网，客服4006602822。',
					);
					$this->curl_sms($send_add_user_data);  //Todo 内外暂不发短信
					$send_add_user_data['sendtime'] = time();
					$this->sms_model->data($send_add_user_data)->add();
	
					$data['createtime'] = time();
					$data['mobile'] = I('post.mobile');
					$data['memo'] = '用户注册';
					$this->memberlog_model->data($data)->add();
				}
			}
			 
			$yzm = I('post.yzm');
			if($yzm && $yzm != $_SESSION['mobileeverify']){
				$this->error('验证码不正确，预约失败',null,'/carservice/order');
			}
			 
			$has_replace_code = false;
			if( !empty($_SESSION['replace_code']) ){
				//总价减去抵用码的价钱
				$has_replace_code = true;
				$order_info['replace_code']= $_SESSION['replace_code'];
				unset($_SESSION['replace_code']);
			}
			 
			$order_info['uid'] = $_SESSION['uid'];
			$order_info['truename'] = I('post.truename');
			$order_info['address'] = I('post.address');
			$order_info['mobile'] = I('post.mobile');
			$order_info['model_id'] = $_SESSION['modelId'];
			$order_info['licenseplate'] = I('post.licenseplate_type').I('post.licenseplate');
			if(I('post.car_reg_time')){
				$order_info['car_reg_time'] = strtotime(I('post.car_reg_time'));
			}else{
				$order_info['car_reg_time'] = 0;
			}
			$order_info['engine_num'] = I('post.engine_num');
			$order_info['vin_num'] = I('post.vin_num');
	
			$order_info['order_time'] = I('post.order_time');
			$order_time2 = intval(I('post.order_time2'));
			$order_info['order_time'] = strtotime($order_info['order_time']) + $order_time2 * 3600;
			$order_info['pay_type'] = I('post.pay_type');
			$order_info['create_time'] = time();
			$order_info['remark'] = I('post.remark');
			$order_info['city_id'] = I('post.city_id');
            $order_info['area_id'] = I('post.area_id');
            
			$oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;
			 
			//计算总价
			if(!empty($_SESSION['item_0'])){
				if( $_SESSION['item_0'] == '-1' ){	//不是自备配件
					$item_list['0']['id'] = 0;
					$item_list['0']['name'] = "自备配件";
					$item_list['0']['price'] = 0;
				}else{
					//通过机油id查出订单数据
					$item_oil_price = 0;
					$oil_data = $_SESSION['oil_detail'];
					$i = 0;
					foreach ( $oil_data as $id=>$num){
						if($num > 0){
							$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
							if ($i == 0) {
								$oil_1_id = $id;
								$oil_1_price = $res['price']*$num;
								$i++;
							}else{
								$oil_2_id = $id;
								$oil_2_price = $res['price']*$num;
							}
							$item_oil_price += $res['price']*$num;
							$name = $res['name'];
						}
					}
					$item_list['0']['id'] = $_SESSION['item_0'];
					$item_list['0']['name'] = $name;
					$item_list['0']['price'] = $item_oil_price;
				}
			}
			if(!empty($_SESSION['item_1'])){
				if($_SESSION['item_1'] == '-1'){
					$item_list['1']['id'] = 0;
					$item_list['1']['name'] = "自备配件";
					$item_list['1']['price'] = 0;
				}else{
					$filter_id = $item_condition['id'] = $_SESSION['item_1'];
					$item_list['1'] = $this->item_model->where($item_condition)->find();
					$filter_price = $item_list['1']['price'];
				}
			}
			if(!empty($_SESSION['item_2'])){
				if($_SESSION['item_2'] == '-1'){
					$item_list['2']['id'] = 0;
					$item_list['2']['name'] = "自备配件";
					$item_list['2']['price'] = 0;
				}else{
					$kongqi_id = $item_condition['id'] = $_SESSION['item_2'];
					$item_list['2'] = $this->item_model->where($item_condition)->find();
					$kongqi_price = $item_list['2']['price'];
				}
			}
			if(!empty($_SESSION['item_3'])){
				if($_SESSION['item_3'] == '-1'){
					$item_list['3']['id'] = 0;
					$item_list['3']['name'] = "自备配件";
					$item_list['3']['price'] = 0;
				}else{
					$kongtiao_id = $item_condition['id'] = $_SESSION['item_3'];
					$item_list['3'] = $this->item_model->where($item_condition)->find();
					$kongtiao_price = $item_list['3']['price'];
				}
			}
	
			$item_amount = 0;
			if(is_array($item_list)){
				foreach ($item_list as $key => $value) {
					$item_amount += $value['price'];
				}
			}
			 
			$item_content = array(
					'oil_id'     =>	$_SESSION['item_0'],
					'oil_detail' => $_SESSION['oil_detail'],
					'filter_id'  => $_SESSION['item_1'],
					'kongqi_id'  => $_SESSION['item_2'],
					'kongtiao_id' =>$_SESSION['item_3'],
					'price'=>array(
							'oil'=>array(
									$oil_1_id=>$oil_1_price,
									$oil_2_id=>$oil_2_price
							),
							'filter'=>array(
									$filter_id=>$filter_price
							),
							'kongqi'=>array(
									$kongqi_id=>$kongqi_price
							),
							'kongtiao'=> array(
									$kongtiao_id=>$kongtiao_price
							)
					)
			);
			 
			$order_info['item'] = serialize($item_content);
			 
			$value = 0;
			if($has_replace_code){
				$value = $this->get_codevalue($order_info['replace_code']);
				$order_info['dikou_amount'] = $value;
				if ($value != 99){
					$order_info['amount'] = $item_amount +99 -$value;
				}else{
					$order_info['amount'] = $item_amount;
				}
			}else{
				$order_info['amount'] = $item_amount + 99;
			}
			$order_info['origin'] = 1;
			$id = $this->reservation_order_model->data($order_info)->add();

			if($id){
				$update = array('status'=>1);
				$where = array('coupon_code'=>$order_info['replace_code']);
				$res = $this->carservicecode_model->where($where)->save($update);
				//发短信通知用户
				$send_add_order_data = array(
						'phones'=>$order_info['mobile'],
						'content'=>"您预约".date('m',strtotime(I('post.order_time'))).'月'.date('d',strtotime(I('post.order_time'))).'日'.$order_time2."时的“府上养车”上门保养服务,我们客服将于2小时内联系您确认订单(工作时间9-18点)。4006602822",
				);
				$this->curl_sms($send_add_order_data,'',1);  //Todo 内外暂不发短信
				$send_add_order_data['sendtime'] = time();
				$this->sms_model->data($send_add_order_data)->add();
				//echo $this->sms_model->getLastsql();exit;
			}
			//var_dump($this->reservation_order_model->getLastSql());exit;
			//插入数据到我的车辆
			$this->_insert_membercar($order_info);
			
			$_SESSION['ck_mobile'] = $order_info['mobile'];
			$_SESSION['ck_name'] = $order_info['truename'];
			
			if(I('post.pay_type')==2){
				$url = WEB_ROOT.'/weixinpaytest/nativecall.php?membercoupon_id='.$id.'&all_amount='.$order_info['amount'].'&coupon_name=上门保养套餐';
				//$url = '/carservice/orderpay-id-'.$id;
				$this->success('预约成功',$url,true);
			}elseif(I('post.pay_type')==3){
                                $order_id = "m".$id;
				$arr='ORDERID='.$order_id.'&PAYMENT='.$order_info['amount'];
                               // $this->addCodeLog('订单保养',$arr);
                                $url = WEB_ROOT.'/ccb_pay/merchant.php?'.$arr;
                                //header("Location:$url");
				$this->success('预约成功',$url,true);
			}else{
				$this->success('预约成功',U('/myhome/carservice_order'),true);
			}
		}
	
		private function _insert_membercar($param){
			if (!$param['uid']) {
				return false;
			}
			$where = array(
					'uid'=>$param['uid'],
					'brand_id'=>@$_SESSION['brand_id'],
					'series_id' => @$_SESSION['series_id'],
					'model_id' => $param['model_id']
			);
			$membercar = $this->membercar_model->where($where)->select();
			$data = array(
					'uid' => $param['uid'],
					'brand_id' => @$_SESSION['brand_id'],
					'series_id' => @$_SESSION['series_id'],
					'model_id' => $param['model_id'],
					'car_name' => @$_SESSION['car_name'],
					'car_number'=> $param['licenseplate'],
					'car_identification_code'=> '',
					'avgoil_type'=>1,
					'status'=>1,
					'create_time'=> time(),
					'is_default' => 1
			);
			if (!$membercar){
				$this->membercar_model->add($data);
			}else{
				$this->membercar_model->where($where)->save($data);
			}
		}
	
		//检查抵用码能否使用
		private  function _check_replace_code($replace_code,$mobile){
			$where = array(
					'coupon_code' => $replace_code,
					'status' => 0
			);
			if(strlen($replace_code) == 4){
				$where['mobile'] = $mobile;
				$where['start_time'] = array('elt',time());
				$where['end_time'] = array('egt',time());
			}
			$count = $this->carservicecode_model->where($where)->count();
			if($count > 0){
				return true;
			}else{
				return false;
	
			}
		}
	
		//ajax验证抵用码
		function valid_coupon_code(){
			unset($_SESSION['replace_code']);
			$replace_code = @$_POST['coupon_code'];
			$mobile = @$_POST['mobile'];
			if(!$replace_code){
				$this->error('抵用码为空','',0,true);
			}
			$first = substr($replace_code,0,1);
			if($first==f){
				$this->error('您持有的为年卡抵用码，请致电400-660-2822由客服为您服务','',0,true);
			}
			$chk_code = $this->_check_replace_code($replace_code,$mobile);
			if(!$chk_code){
				$this->error('该抵用码不能使用，请重新填写','',0,true);
			}else{
				//改抵用码已经用过了，不能再用了
				/*$update = array('status'=>1);
				$where = array('coupon_code'=>$replace_code);
				$res = $this->carservicecode_model->where($where)->save($update);*/

				$price = $this->get_codevalue($replace_code);
				//if( $res ){
					//用于减去总价，总价减去抵用码的价钱
					$_SESSION['replace_code']= $replace_code;
					$this->success($price,'',true);
				//}else{
					//$this->success('更新数据失败，请重试用','',true);
				//}
			}
		}
	
	
		function orderpay(){
			$map['id'] = $_REQUEST['id'];
			$info = $this->reservation_order_model->where($map)->find();
			$info['true_id'] = $info['id'];
			$info['id'] = $this->get_orderid($info['id']);
			$this->assign('id',$info['id']);
			$this->assign('info',$info);
			$this->display();
		}
	
		//支付跳转判断 wuwenyu
		function do_orderpay(){
			if(true !== $this->login()){
				exit;
			}
			$key = isset($_REQUEST['sel_online_pay'])?$_REQUEST['sel_online_pay']:1;
			$order_id = isset($_REQUEST['order_id'])?$_REQUEST['order_id']:1;
	
			$map['id'] = $order_id;
			$info = $this->reservation_order_model->where($map)->find();
			if($info['pay_status']=='1'){
				$this->error("订单已支付",__APP__.'/carservice/');
			}
	
			$bank_type=$this->get_bank($key);
			if($bank_type=='CFT'){
				$url=WEB_ROOT.'/txpay/payRequest.php?order_id='.$order_id;
			}
			if($bank_type=='YL'){
				$url=WEB_ROOT.'/unionpay/front.php?order_id='.$order_id;
			}
			if($bank_type=='COMM'){
				$coupon_amount = $info['amount'];
				$array='interfaceVersion=1.0.0.0&orderid='.$order_id."&orderDate=".date('Ymd',time())."&orderTime=".date('His',time()).'&tranType=0&amount='.$coupon_amount.'&curType=CNY&orderMono=6222600110030037084& notifyType=1&merURL='.WEB_ROOT.'/comm_pay/merchant_result.php&goodsURL='.WEB_ROOT.'/comm_pay/merchant_result.php&netType=0';
				$url=WEB_ROOT.'/comm_pay/merchant.php?'.$array;
			}
			if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='COMM'){
				$url=WEB_ROOT.'/txpay/payRequest.php?order_id='.$order_id.'&bank_type='.$bank_type;
			}
	
			header("Location:$url");
			//$this->display('do_couponpay');
		}
		//银行转换数组 wuwenyu
		function get_bank($key){
			$bank_array=array(
					'19'=>'CMB',//招商银行
					'31'=>'BOC',//中国银行
					'17'=>'ICBC',//工商银行
					'18'=>'ABC',//农业银行
					'20'=>'CCB',//建设银行
					'74'=>'PAB',//平安银行
					'501'=>'POSTGC',//邮储银行
					'34'=>'CEB',//光大银行
					'35'=>'CIB',//兴业银行
					'36'=>'CITIC',//中信银行
					'37'=>'CMBC',//民生银行
					'38'=>'COMM',//交通银行
					'39'=>'GDB',//广东发展银行
					'41'=>'BOSH',//上海银行
					'42'=>'SPDB',//浦发银行
					'100'=>'YL',//银联
					'8'=>'CFT',//财付通
			);
			if($key){$bank_type=$bank_array[$key];}
			return $bank_type;
		}
		function lvmama(){
			$this->display();
		}
                
                
    //AJAX阻止同一时间点过分下单
    function prevent(){
        $order_time = strtotime($_POST['order_time']) + ($_POST['order_time2']) * 3600;
        $map['status'] = array('lt','8');
        $map['truename'] = array('notlike','%测试%');
        $map['city_id'] = 1;
        $map['order_time'] = $order_time;
        $list = $this->reservation_order_model->where($map)->Distinct(true)->field('mobile')->select();
        $i=0;
        foreach($list as $k=>$v){
                $i++;
        }

        if($_SESSION['authId']=='238'){
                $i=1;
        }
        //echo $this->reservation_order_model->getLastsql();
        echo $i;exit;
    }
    
    
    /**
	 * 获取城市地区   wql@20150720
	 */
	public function ajax_area(){
		$city_id = intval($_POST['city_id']);
		if($city_id==1){
            //通过老的城市id获取新的城市id ,然后再去查询地区信息并返回
			$cond['old_cityid'] = $city_id;
            $cityInfo = $this->item_city->where($cond)->find();
            $map['fatherID'] = $cityInfo['cityID'];
			$area_list = $this->item_area->where($map)->select();
		}else{
			$area_list = "";
		}
	
        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $return['result'] = array('area_list' => $area_list );
		$this->ajaxReturn( $return );
	}
}