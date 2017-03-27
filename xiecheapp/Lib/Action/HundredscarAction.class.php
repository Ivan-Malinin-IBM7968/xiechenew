<?php
//文章
class HundredscarAction extends CommonAction {
	public function __construct() {
            exit('接口升级中');
		parent::__construct();
		$this->orderModel = D('order');//订单表
		$this->membercouponModel = D('membercoupon');//优惠券表
		$this->hundredorderModel = D('hundredorder');//优惠券表
		$this->commentModel = D('comment');//评论表
		$this->order_logModel = D('order_log'); //记录订单操作表
	}


	/*
		@author:chf
		@function:得到所有车品牌
		@time:2014-04-10
	*/
	function PostCar_brand(){
		$model_carbrand = D('Carbrand');
        $brand = $model_carbrand->select();
		$xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($brand){
            foreach ($brand as $k=>$v){
                $xml_content .= "<brand_item><brand_id>".$v['brand_id']."</brand_id><word>".$v['word']."</word><brand_name>".$v['brand_name']."</brand_name></brand_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
	}
	
	/*
		@author:chf
		@function:根据品牌ID取值对应的车系内容
		@time:2014-04-10
	*/
    public function PostCar_series(){
        if($_REQUEST['brand_id']){
            $map_s['brand_id'] = $_REQUEST['brand_id'];
        }
        $model_carseries = D('Carseries');
        $carseries = $model_carseries->where($map_s)->select();
        $xml_content ="<?xml version=\"1.0 \" encoding=\"UTF-8\"?><XML>";
        if ($carseries){
            foreach ($carseries as $k=>$v){
                $xml_content .= "<series_item><series_id>".$v['series_id']."</series_id><word>".$v['word']."</word><series_name>".$v['series_name']."</series_name><brand_id>".$v['brand_id']."</brand_id></series_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }


	/*
		@author:chf
		@function:根据车系ID取值对应的车型内容
		@time:2014-04-10
	*/
    public function PostCar_model(){
        if($_REQUEST['series_id']){
            $map_m['series_id'] = $_REQUEST['series_id'];
        }
        $model_carmodel = D('Carmodel');
        $carmodel = $model_carmodel->where($map_m)->select();
        $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
        if ($carmodel){
            foreach ($carmodel as $k=>$v){
                $xml_content .= "<model_item><model_id>".$v['model_id']."</model_id><model_name>".$v['model_name']."</model_name><series_id>".$v['series_id']."</series_id></model_item>";
            }
        }
        $xml_content .="</XML>";
        echo $xml_content;exit;
    }
	
	/*
		@author:chf
		@function:返回店铺所有信息
		@time:2014-04-10
	*/
	function PostCar_shop(){
		$ShopModel = D('shop');
		$model_comment = D('Comment');//评论表
		$timesaleModel = D('timesale');
		$model_carseries = D('Carseries');
		
		$FsModel = D('shop_fs_relation');
		if($_REQUEST['shop_id']){
            $map_m['id'] = $_REQUEST['shop_id'];
        }
		 $map_m['status'] = '1';
		 $shop = $ShopModel->where($map_m)->select();
	
		 $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		 if($shop){
			foreach($shop as $k=>$v){
				$map_c['comment_type'] = 1;
				$map_c['shop_id'] = $v['id'];
				$good = $model_comment->where($map_c)->count();
				$good = round($good/$count*100);//Praise
				$FS = $FsModel->where(array('shopid'=>$v['id']))->select();
				if($FS){
					foreach($FS as $fs_k=>$fs_v){
						$carseries = $model_carseries->where(array('fsid'=>$fs_v['fsid']))->find();
						$brand_ids.=$carseries['brand_id'].",";	
					}
				}
				if($v['shop_class'] == 1){
					$data[$k]['shop_pic'] = "http://www.xieche.com.cn/UPLOADS/Shop/200/".$v ['id'].".jpg";
				}else{
					$fsid = $this->getfsid($v ['id']);
					$data[$k]['shop_pic'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/".$fsid.".jpg";
				}

				$data[$k]['brand_ids'] = substr($brand_ids,0,-1);
				$data[$k]['brand_ids'] = explode(',',$data[$k]['brand_ids']);
				$data[$k]['brand_ids'] = array_unique($data[$k]['brand_ids']);
				$data[$k]['brand_ids'] = implode(",", $data[$k]['brand_ids']);
				$comment_count = $this->commentModel->where(array('shop_id'=>$v['id']))->count();
				$timesale = $timesaleModel->where(array('shop_id'=>$v['id']))->find();
				$xml_content.= "<shop_item><shop_id>".$v['id']."</shop_id><shop_name>".$v['shop_name']."</shop_name><shop_mobile>4006602822</shop_mobile><shop_maps>".$v['shop_maps']."</shop_maps><brand_ids>".$data[$k]['brand_ids']."</brand_ids><shop_address>".$v['shop_address']."</shop_address><shop_pic>".$data[$k]['shop_pic']."</shop_pic><comment_count>".$comment_count."</comment_count><shop_account>".$v['shop_account']."</shop_account><week>".$timesale['week']."</week><begin_time>".$timesale['begin_time']."</begin_time><end_time>".$timesale['end_time']."</end_time><Praise>".$good."</Praise></shop_item>";
				$brand_ids ="";
			}
		 }
		 $xml_content .="</XML>";
         echo $xml_content;exit;
	}
	
	/*
		@author:chf
		@function:通过shop_id得到对应的fsid
		@time:2014-04-16
	*/
	function getfsid($shop_id){
        $model_shop_fs_relation = D('Shop_fs_relation');
        $map_shopfs['shopid'] = $shop_id;
        $shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
        if ($shop_fs_relation){
            $model_carseries = D('Carseries');
            foreach ($shop_fs_relation as $k=>$v){
                if ($v['fsid']){
                    return $v['fsid'];
                }
            }
        }
    }

	/*
		@author:chf
		@function:根据店铺shop_id返回优惠券信息(上线前要百车保链接)
		@time:2014-04-10
	*/
	function PostCar_coupon(){
		$CouponModel = D('coupon');
		if($_REQUEST['shop_id']){
            $map_m['shop_id'] = $_REQUEST['shop_id'];
			$map_m['show_s_time'] = array('lt',time());
			$map_m['show_e_time'] = array('gt',time());
        }
		 $map_m['is_delete'] = '0';
		 $Coupon = $CouponModel->where($map_m)->select();
		 $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		 if($Coupon){
			foreach($Coupon as $k=>$v){
				$data[$k]['id'] = $v['id'];
				$data[$k]['coupon_name'] = $v['coupon_name'];
				$data[$k]['coupon_type'] = $v['coupon_type']; //1现金券 2团购券
				$data[$k]['url'] = "www.xieche.com.cn/coupon/".$v['id'];
				$data[$k]['series_id'] = explode(',',$v['series_id']);
				$data[$k]['series_id'] = array_unique($data[$k]['series_id']);
				$data[$k]['series_id'] = implode(",", $data[$k]['series_id']);
				$xml_content .= "<coupon_item><coupon_id>".$v['id']."</coupon_id><cost_price>".$v['cost_price']."</cost_price><coupon_amount>".$v['coupon_amount']."</coupon_amount><coupon_name>".$v['coupon_name']."</coupon_name><coupon_type>".$v['coupon_type']."</coupon_type><url>"."www.xieche.com.cn/coupon/".$v['id']."</url><series_id>".$data[$k]['series_id']."</series_id></coupon_item>";
			}
		 }
		 $xml_content .="</XML>";
         echo $xml_content;exit;
	}
	
	/*
		@author:chf
		@function:根据店铺shop_id返回优惠券信息+小保养的工时费
		@time:2014-04-10
	*/
	function PostCar_Repair(){
		$CouponModel = D('coupon');
		if($_REQUEST['shop_id']){
            $map_m['shop_id'] = $_REQUEST['shop_id'];
			$data = $this->get_server($map_m['shop_id'],'9');//大保养
			$data1 = $this->get_server($map_m['shop_id'],'10');//小保养
        }
		$new_array = array_merge($data, $data1);
		$xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		 if($new_array){
			foreach($new_array as $k=>$v){
				$xml_content.= "<item><service_id>".$v['service_id']."</service_id><model_id>".$v['model_id']."</model_id><car_name>".$v['car_name']."</car_name><workhour>".$v['workhour']."</workhour><product>".$v['product']."</product><total>".$v['total']."</total><sales_total>"."".$v['sales_total']."</sales_total><save_total>".$v['save_total']."</save_total><Repairtype>".$v['Repairtype']."</Repairtype></item>";
			}
		 }
		 $xml_content .="</XML>";
         echo $xml_content;exit;
	}


	function get_server($shop_id,$type){
		$timesale_model = D('Timesale');
		$timesaleversion_model = D('Timesaleversion');
		$map_m['shop_id'] = $shop_id;

		$list_timesale = $timesale_model->where(array('shop_id'=>$map_m['shop_id'],'status'=>'1'))->select();
		if ($list_timesale){
		    foreach ($list_timesale as $_k=>$_v){
				$query_array_timesale_id[] = $_v['id'];
			}
		}
		
		$timesaleversion_params['timesale_id'] = array('in', $query_array_timesale_id);
		$timesaleversion_params['status'] = 1;
		$timesaleversion_params['s_time'] = array('lt',time());
		$timesaleversion_params['e_time'] = array('gt',time());	
		$last_version = $timesaleversion_model->where($timesaleversion_params)->order('workhours_sale')->limit(1)->find();
		if ($last_version['workhours_sale']>0){
			$salesversion['workhours_sale'] = round($last_version['workhours_sale']*10,1)."折";
		}else if($last_version['workhours_sale'] == '-1') {
			$salesversion['workhours_sale'] = "全免";
		}else{
			$salesversion['workhours_sale'] = "无折扣";
		}
		
		if ($last_version['product_sale']>0){
			$salesversion['product_sale'] = round($last_version['product_sale']*10,1)."折";
		}else {
			$salesversion['product_sale'] = "无折扣";
		}
		$salesversion['id'] = $last_version['id'];

		if ($last_version['workhours_sale'] == '0.00'){
			$last_version['workhours_sale'] = 1;
		}
		if ($last_version['product_sale'] == '0.00'){
			$last_version['product_sale'] = 1;
		}

		if ($last_version['workhours_sale'] == '-1'){
			$last_version['workhours_sale'] = 0;
		}

		$model_product = D("Product");
		$model_product_version = D("Productversion");
		$model_serviceitem = D("Serviceitem");
	    $model_shop_fs_relation = D('Shop_fs_relation');
	    $fs_arr = $model_shop_fs_relation->where(array('shopid'=>$map_m['shop_id']))->select();
	    $fsids_arr = array();
	    if (!empty($fs_arr)){
	        $model_fs = D('Fs');
	        foreach ($fs_arr as $v){
	            $fs_info = $model_fs->find($v['fsid']);
	            $fsids_arr[] = $fs_info['fsname'];
				$fsids_id_arr[] = $v['fsid'];
	        }
	    }
		$param['service_id'] = $type;
		$service_name = $model_serviceitem->find($param['service_id']);
		$param['fsid'] = array('in',$fsids_id_arr);
		$product_list = $model_product->where($param)->group("flag")->select();
		if($product_list) {
			foreach($product_list as $kk=>$vv) {
				$product_detail = $model_product_version->where("id = {$vv[versionid]} AND status = 0 ")->field("product_detail")->find();
				$car_name = $vv['flag']." ".$vv['emission']." ".$vv['shift'];
				$product_detail = unserialize($product_detail['product_detail']);
				//工时费 	零件费 	门市价 	折后价 	节省
				if($product_detail) {
					
					foreach ($product_detail as $_v){
						$item_total = $_v['quantity']*$_v['price'];
						 if ($_v['Midl_name'] == '工时费'){
							$workhour = $item_total;
							$work_sales = $item_total * $last_version['workhours_sale'];

						 }else {
							$product += $item_total;
							$product_sales += $item_total * $last_version['product_sale'];
						 }
					}
					
					if($work_sales == 0 && $product_sales == 0) {
						$work_sales = $workhour;
						$product_sales = $product;
					}
					$array_car[$kk]['model_id'] = $vv['model_id'];
					$array_car[$kk]['car_name'] = $car_name;
					$array_car[$kk]['workhour'] = round($workhour,2);
					$array_car[$kk]['product'] = round($product,2);
					$array_car[$kk]['total'] = round($workhour+$product,2);
					$array_car[$kk]['sales_total'] = round($work_sales+$product_sales,2);
					
					$array_car[$kk]['save_total'] = round(($workhour+$product)-($work_sales+$product_sales),2);
					if($vv['service_id'] == '9'){//大保养
						$array_car[$kk]['Repairtype'] = '1';//大保养
						$array_car[$kk]['service_id'] = '9';
					}else{
						$array_car[$kk]['service_id'] = '10';
						$array_car[$kk]['Repairtype'] = '2';//小保养
					}
					unset($product);
					unset($product_sales);
				}
			}
		} 
		return $array_car;
	}


	//测试百车宝插入订单
	function test(){
		$data['brand_id'] = '43';//品牌
		$data['series_id'] = '318';//车系
		$data['shop_id'] = '71';//店铺ID
		$data['service_id'] = '1';//保养ID
		$data['truename'] = '测试';//真实姓名
		$data['mobile'] = '13681971367';//手机号
		$data['licenseplate'] = '浙AA516M';//车牌号
		$data['total_price'] = '2000';//总费用
		$data['remark'] = '测试订单';//备注
		$data['order_time'] = time();//预约时间
		$data['create_time'] = time();
		$data['boid'] = '2';//百车保订单ID
		$data['is_pa'] = '3';
		$dd = $this->Api_toPA('http://www.xieche.com.cn/Hundredscar/Post_addcar',$data);
		echo $dd;
	
	}

	/*
		@author:chf
		@function:从百车保接数据
		@time:2014-04-11
	*/
	function Post_addcar(){
		$orderModel = D('order');
		$this->MemberModel = D('member');
		$this->HundredorderModel = D('hundredorder');
		$this->model_sms = D('Sms');
		$data['brand_id'] = $_POST['brand_id'];//品牌
		$sms = $_POST['sms'];//判断百车宝是否要发短信
		$data['series_id'] = $_POST['series_id'];//车系
		$data['shop_id'] = $_POST['shop_id'];//店铺ID
		$data['mobile'] = $_POST['mobile'];//店铺ID
		$data['service_ids'] = $_POST['service_id'];//保养ID
		if($_POST['service_id'] == '1'){
			$data['service_ids'] = '9'; //大保养
		}else if($_POST['service_id'] == '2'){
			$data['service_ids'] = '10';//小保养
		}else if($_POST['service_id'] == '99'){
			$data['service_ids'] = '0';//小保养
		}
		
		if($data['mobile']){
			$rand_verify = rand(10000, 99999);//密码随机
			$map['mobile'] = $data['mobile'];
			$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();

			if(!$res ){
				$member_data['mobile'] = $data['mobile'];
				$member_data['password'] = md5($rand_verify);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$member_data['fromstatus'] = '39';
				$member['uid'] = $this->MemberModel->data($member_data)->add();
				if($member['uid']!=''){
					/*
					$send_add_user_data = array(
						'phones'=>$data['mobile'],
						'content'=>'您已注册成功，您可以使用您的手机号码'.$data['mobile'].'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
					 );
					$this->curl_sms($send_add_user_data);
					*/
					// dingjb 2015-09-29 11:04:09 切换到云通讯
					$send_add_user_data = array(
						'phones'  => $data['mobile'],
						'content' => array(
							$data['mobile'],
							$_POST['mobile_verify_lottery']
						)
					 );
					$this->curl_sms($send_add_user_data, null, 4, '37653');

					$send_add_user_data['sendtime'] = time();
					$this->model_sms->data($send_add_user_data)->add();
					
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $data['mobile'];
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$data['mobile'],'status'=>'1'))->find();
				}else{
					echo "insert member fail";
					echo $this->MemberModel->getLastsql();
				}
			}
		}


		$data['truename'] = $_POST['truename'];//真实姓名
		$data['mobile'] = $_POST['mobile'];//手机号
		$data['licenseplate'] = $_POST['licenseplate'];//车牌号
		$data['total_price'] = $_POST['total_price'];//总费用
		$data['remark'] = $_POST['remark'];//备注
		$data['order_time'] = $_POST['order_time'];//预约时间
		$data['create_time'] = time();
		$data['boid'] = $_POST['boid'];//百车保订单ID
		$data['uid'] = $res['uid'];
		$data['is_pa'] = '3';
		
		$order_id = $orderModel->data($data)->add();
		//echo $orderModel->getLastsql();

		if($order_id){
			$time = time();
			$bo_id = $this->HundredorderModel->data(array('order_id'=>$order_id,'boid'=>$data['boid'],'create_time'=>$time,'sms'=>$sms))->add();
			//echo $this->HundredorderModel->getLastsql();
			$this->HundredorderModel->where(array('id'=>$bo_id))->save(array('sms'=>$hundred['sms']));//如果是新用户就记录已发送短信字段
			if($bo_id){
				echo "ok";
			}else{
				echo "insert Hundredorder fail";
			}
		}else{
			echo "insert order fail";
			echo $orderModel->getLastsql();
		}
		
	}

	/*
		@author:chf
		@function:根据时间返回订单信息
		@time:2014-04-10
	*/
	function Posttime_order(){
		$this->hundredorderModel = D('hundredorder');//优惠券表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		if($start_time && $end_time){
			$map_m['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
			$map_m['is_pa'] = '3';
		}
	
		$data = $this->orderModel->where($map_m)->select();
		 $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		 if($data){
			foreach($data as $k=>$v){
				$hundredorder = $this->hundredorderModel->where(array('order_id'=>$v['id']))->find();
				$xml_content.= "<item><brand_id>".$v['brand_id']."</brand_id><series_id>".$v['series_id']."</series_id><shop_id>".$v['shop_id']."</shop_id><service_id>".$v['service_ids']."</service_id><truename>".$v['truename']."</truename><mobile>".$v['mobile']."</mobile><licenseplate>".$v['licenseplate']."</licenseplate><total_price>".$v['total_price']."</total_price><remark>".$v['remark']."</remark><order_time>".$v['order_time']."</order_time><create_time>".$v['create_time']."</create_time><boid>".$hundredorder['boid']."</boid></item>";
			}
		 }
		 $xml_content .="</XML>";
         echo $xml_content;exit;
	}

	/*
		@author:chf
		@function:根据时间返回优惠券信息
		@time:2014-04-10
	*/
	function Posttime_coupon(){
		$this->membercouponModel = D('membercoupon');
		$this->couponModel = D('coupon');
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		if($start_time && $end_time){
			$map_m['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
			$map_m['pa'] = '4';
		}
	
		$data = $this->membercouponModel->where($map_m)->select();
		
		 $xml_content ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><XML>";
		 if($data){
			foreach($data as $k=>$v){
				$coupon = $this->couponModel->where(array('id'=>$v['coupon_id']))->find();
				$xml_content.= "<item><coupon_name>".$v['coupon_name']."</coupon_name><coupon_type>".$v['coupon_type']."</coupon_type><cost_price>".$coupon['cost_price']."</cost_price><create_time>".$v['create_time']."</create_time></item>";
			}
		 }
		 $xml_content .="</XML>";
         echo $xml_content;exit;
	}

	/*
		@author:chf
		@function:
		@time:2014-07-08
	*/
	function update_order(){
		$order_status = $_POST['status'];//订单状态
		$boid = $_POST['boid'];
	
		$this->HundredorderModel = D('hundredorder');
		$Hundredorder = $this->HundredorderModel->where(array('boid'=>$boid))->find();
		if(count($Hundredorder) == '0'){
			$data['error'] = '3';
			$data['description'] = '参数不全';
			$data['boid'] = $boid;
			print_r($data);
			exit;
		}
		if($order_status == '1'){
			$status = '1';//已受理
			$order_log['content'] = "订单确认";
		}elseif($order_status == '3'){

			$status = '2';//已完成
			$order_log['content'] = "订单完成";

		}elseif($order_status == '0'){
			$status = '-1';//已取消
			$order_log['content'] = "订单取消";
		}else{
			$status = $order_status;
		}
		$this->orderModel->where(array('order_id'=>$Hundredorder['order_id']))->save(array('status'=>$status));
		if($boid){
			$data['error'] = '0';
			$data['description'] = '订单更新成功';
			$data['boid'] = $boid;

			$order_log['name'] = '百车宝';//记录用户名
			$order_log['ip'] = $_SERVER["REMOTE_ADDR"];//记录ip
			$order_log['order_id'] = $Hundredorder['order_id'];//订单号
			$order_log['create_time'] = time();//创建时间	
			$this->order_logModel->add($order_log);
		}
		else if(!$boid){
			$data['error'] = '1';
			$data['description'] = '参数不全';
			$data['boid'] = $boid;
		}
		print_r($data);

	}

	/*
		@author:chf
		@function:读取页面XML信息变成PHP可读数组(方法)
		@time:2014-04-11
	*/
	function Phpgetxml(){
		$url = "http://www.xieche.com.cn/Hundredscar/PostCar_brand"; 
		$contents = file_get_contents($url);
		$data = simplexml_load_string($contents);
		$array['tk'] = json_decode(json_encode($data),TRUE);
		foreach($array['tk']['brand_item'] as $k=>$v){
			echo $v['brand_name']."<br>";
		}
	}

}