<?php
//订单
class CarserviceAction extends CommonAction {	
	function __construct() {

		parent::__construct();

		$this->assign('current','carservice');
        $this->assign('meta_keyword',"汽车保养,汽车维修,4S店预约保养,汽车保养预约,汽车维修预约");
        $this->assign('description',"汽车保养维修,事故车维修首选携车网,在线预约,50元养车券免费领,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
        Cookie::set('_currentUrl_', __URL__);
        $this->assign('series_id',$this->request_int('series_id'));
        $this->assign('model_id',$this->request_int('model_id'));
        $this->assign('shop_area',$this->request_int('shop_area'));
        $this->assign('uid',$_SESSION['uid']);

		$this->car_brand_model = M('tp_xieche.car_brand','xc_');  //车品牌
        $this->car_model_model = M('tp_xieche.car_model','xc_');  //车型号
        $this->car_style_model = M('tp_xieche.car_style','xc_');  //车型号
        
        $this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段

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
	}

	/**
	 * 上门养车首页
	 * @author  chaozhou
	 * @date  2014/8/22
	 * @version  1.0.0
	*/
	public function index(){
		$brand_list = $this->carbrand_model->select();
		foreach($brand_list as $k=>$v){
			$car_model_list = $this->carseries_model->where(array('brand_id'=>$v['brand_id']))->select();
			foreach($car_model_list as $key=>$value){
				$map['series_id'] = $value['series_id'];
				$map['is_show'] = '1';
				$count = $this->carmodel_model->where($map)->count();
				if($count==0){
					unset($car_model_list[$key]);
				}
			}
			if(count($car_model_list)==0){
				unset($brand_list[$k]);
			}
		}
		$title = "汽车上门保养【府上养车】-携车网";
		$meta_keyword = "上门保养,汽车上门保养,府上养车,上门汽车保养";
		$description = "汽车上门保养找携车网上门仅需99正品配件京东价,拥有资深4S店技师和1800种车型标配工具,38项爱车深度体检,电话,APP,微信召之即来,车在哪,保养就到哪!";

		$this->assign("title", $title);
		$this->assign("meta_keyword", $meta_keyword);
		$this->assign("description", $description);

		$this->assign("brand_list", $brand_list);
		$this->display('index');
	}
	

	/**
     * 车型
     */
    public function ajax_car_model(){
        $brand_id = intval($_POST['brand_id']);
        if( $brand_id ){
            $condition['brand_id'] = $brand_id;
			$car_model_list = $this->carseries_model->where( $condition )->order('word')->group('series_name')->select();
			foreach($car_model_list as $key=>$value){
				$map['series_id'] = $value['series_id'];
				$map['is_show'] = '1';
				$count = $this->carmodel_model->where($map)->count();
				if($count==0){
					unset($car_model_list[$key]);
				}
			}
        }else{
            $car_model_list = "";
        }
        if( $car_model_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('model_list' => $car_model_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该品牌下无录入车系';
        }
        $this->ajaxReturn( $return );
    }

    /**
     * 车款
     */
    public function ajax_car_style(){
        $model_id = intval( $_POST['model_id'] );
        if( $model_id ){
			$condition['series_id'] = $model_id;
			$condition['is_show'] = '1';
			$car_style_list = $this->carmodel_model->where( $condition )->select();
			//echo $this->carmodel_model->getLastsql();
        }else{
            $car_style_list = "";
        }

        if( $car_style_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('style_list' => $car_style_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该车型下无录入车辆';
        }
        $this->ajaxReturn( $return );
    }

    /**
	 * 提交车型
	 * @author  chaozhou
	 * @date  2014/8/22
	 * @version  1.0.0 
	 */
    public function sub_car(){
    	session_start();

    	$_SESSION['brand_id'] = intval($this->_get('brand_id'));
    	$_SESSION['series_id'] = intval($this->_get('model_id'));
    	$_SESSION['model_id'] = intval($this->_get('style_id'));

    	$this->redirect('carservice/sel_item');
    }
	
	/**
	 * 选择项目
	 * @author  chaozhou
	 * @date  2014/8/22
	 * @version  1.0.0 
	 */
	public function sel_item(){
		session_start();

		if($_SESSION['model_id']){
		}else{
			$this->redirect('carservice');
			return false;
		}

		//车型
		$style_param['model_id'] = $_SESSION['model_id'];
		$car_model = $this->carmodel_model->where($style_param)->find();
        $style_name = $car_model['model_name'];
        $car_name = $car_model['model_name'];

        if($car_model){
            $model_param['series_id'] = $car_model['series_id'];
			$car_carseries = $this->carseries_model->where($model_param)->find();
            
            if($car_carseries){
                $model_name = $car_carseries['series_name'];
                $car_name = $car_carseries['series_name']." - ".$car_name;

                $brand_param['brand_id'] = $car_carseries['brand_id'];
				$car_brand = $this->carbrand_model->where($brand_param)->find();
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
                //$norms = $yy."x4L + ".$xx."x1L";
            	$norms = $yy+$xx."L";
            }else{
                //$norms = $yy."x4L";
            	$norms = $yy."L";
            }
        }else{
            $xx = $oil_num;
            $yy = 0;
            //$norms = $oil_num . "x1L";
            $norms = $oil_num . "L";
        }
        if( $oil_num <4 ){
        	$car_model['norms'] = '4L';
        }else{
        	$car_model['norms'] = $oil_num."L";//多少升机油
        }

        //所有物品详情
        $oil_list_all = $this->item_oil_model->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
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
        $oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select();
        
        foreach( $oil_name_distinct as $keys=>$names ){
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
            //取消原来的逻辑，按照价钱低高来推荐
//             //全合成灰壳放在最前面
//             if ($names['id'] == 45 && $car_model['oil_type'] == 'c') {
//             	$qiaopai = $item_sets[$keys];
//             	unset($item_sets[$keys]);
//             }
//             //半合成蓝壳放在最前面
//             if ($names['id'] == 47 && $car_model['oil_type'] == 'b') {
//             //临时放磁护
//             //if ($names['id'] == 54 && $car_model['oil_type'] == 'b') {
//             	$qiaopai = $item_sets[$keys];
//             	unset($item_sets[$keys]);
//             }
        }
        //按照总价钱排序
        usort($item_sets, function($a,$b){
        	return strcmp($a["price"], $b["price"]);
        });
//         //壳牌放在最前面
//         if (isset($qiaopai)) {
//         	array_unshift($item_sets,$qiaopai);
//         }
        
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

        //项目
        $model_id = $_SESSION['model_id'];
        $item_set = array();
        if( $model_id ){
            $condition['model_id'] = $model_id;
			$style_info = $this->carmodel_model->where($condition)->find();
            $set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

            if( $set_id_arr ){
                foreach( $set_id_arr as $k=>$v){
                    if(is_array($v)){
                        foreach( $v as $_k=>$_v){
                            $item_condition['id'] = $_v;
                            $item_info_res = $this->filter_model->where($item_condition)->find();
                            $item_info['id'] = $item_info_res['id'];
                            //去掉后面的规格 by bright
                            //$item_info['name'] =  mb_substr( $item_info_res['name'], 0 ,mb_strpos($item_info_res['name'],' ') );
							//强制配件品名只显示品牌名
							$pattern = '曼牌';
							$a = mb_strpos($item_info_res['name'],$pattern);
							if($a){
								$item_info_res['name'] = '曼牌';
							}
							$pattern = '马勒';
							$a = mb_strpos($item_info_res['name'],$pattern);
							if($a){
								$item_info_res['name'] = '马勒';
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
						foreach($item_set[$k] as $kk=>$vv){
							if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
								$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
								$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
							}else{
								$item_set_new[$k][$kk] = $item_set[$k][$kk];
							}
						}
						$item_set = $item_set_new;
                    }
                }
            }
        }
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
        $this->assign('recommend_low', $recommend_low);
        $this->assign('recommend_high', $recommend_high);
        $this->assign('car_style', $car_model);
        $this->assign('item_sets', $item_sets);
        $this->assign('item_set', $item_set);

		$this->assign('title',"选择项目-上门保养-携车网");

		$this->display('sel_item');
	}

	/**
	 * 订单详情
	 * @author  chaozhou
	 * @date  2014/8/22
	 * @version  1.0.1 
	 */
	public function order(){
        session_start();
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();
		
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
		
        if($this->_post('CheckboxGroup0_res')){
            $_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res')); 
            $_SESSION['oil_detail'] = array(
            		$this->_post('oil_1_id') => $this->_post('oil_1_num'),
            		$this->_post('oil_2_id') => $this->_post('oil_2_num')
            );
        }
        if($this->_post('CheckboxGroup1_res')){
            $_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res'));
        }
        if($this->_post('CheckboxGroup2_res')){
            $_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res'));
        }
        if($this->_post('CheckboxGroup3_res')){
            $_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res'));
        }
        $item_num = 1;
        if(!empty($_SESSION['item_0'])){
        	if( $_SESSION['item_0'] == '-1' ){
        		$item_list['0']['id'] = 0;
        		$item_list['0']['name'] = "自备配件";
        		$item_list['0']['price'] = 0;
        	}else{
        		//通过机油id查出订单数据
        		$item_oil_price = 0;
        		$oil_data = $_SESSION['oil_detail'];
        		foreach ( $oil_data as $id=>$num){
        			if($num > 0){
        				$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
        				$item_oil_price += $res['price']*$num;
        				$name = $res['name'];
        			}
        		}
        		$item_list['0']['id'] = $_SESSION['item_0'];
        		$item_list['0']['name'] = $name;
        		$item_list['0']['price'] = $item_oil_price;
        	}
        	$item_num++;
        }
        
        if(!empty($_SESSION['item_1'])){
        	if($_SESSION['item_1'] == '-1'){
        		$item_list['1']['id'] = 0;
        		$item_list['1']['name'] = "自备配件";
        		$item_list['1']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_1'];
        		$item_list['1'] = $this->item_model->where($item_condition)->find();
        		$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));//去掉后面的规格 by bright
        	}
        	$item_num++;
        	
        }
        if(!empty($_SESSION['item_2'])){
        	if($_SESSION['item_2'] == '-1'){
        		$item_list['2']['id'] = 0;
        		$item_list['2']['name'] = "自备配件";
        		$item_list['2']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_2'];
        		$item_list['2'] = $this->item_model->where($item_condition)->find();
        		$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
        	}
        	$item_num++;
        }
        if(!empty($_SESSION['item_3'])){
        	if($_SESSION['item_3'] == '-1'){
        		$item_list['3']['id'] = 0;
        		$item_list['3']['name'] = "自备配件";
        		$item_list['3']['price'] = 0;
        	}else{
        		$item_condition['id'] = $_SESSION['item_3'];
        		$item_list['3'] = $this->item_model->where($item_condition)->find();
        		$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
        	}
        	$item_num++;
        }
        
        $item_amount = 99;
        if(is_array($item_list)){
        	foreach ($item_list as $key => $value) {
        		$item_amount += $value['price'];
        	}
        }
        if( empty( $_SESSION['uid'] ) ){
        	//$this->assign('yzm',1);//需要验证码,后期需求都不需要验证码了
        }
        //var_dump($_SESSION);exit;
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);

		$this->assign('title',"订单详情-上门保养-携车网");
		
		$this->display('order');
	}

    /**
     * 发送验证码
     * @author  chaozhou
     * @date  2014/8/24
     * @version  1.0.0 
     */
    function giveeverify(){
        session_start();

        $mobile = $this->_post('mobile');
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
     * @author  chaozhou
     * @date  2014/8/23
     * @version  1.0.0 
     */
    
    public function create_order(){
    	session_start();
    //print_r($_SESSION['model_id']);exit;
    	if($_SESSION['uid']){
    	}else{
    		$userinfo = $this->user_model->where(array('mobile'=>$this->_post('mobile'),'status'=>'1'))->find();
    		if($userinfo){
    			$_SESSION['uid'] = $userinfo['uid'];
    		}else{
    			$member_data['mobile'] = $this->_post('mobile');
    			$member_data['password'] = md5($this->_post('mobile'));
    			$member_data['reg_time'] = time();
    			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
    			$member_data['fromstatus'] = '51';//WEB上门保养
    			$_SESSION['uid'] = $this->user_model->data($member_data)->add();
                /*
    			$send_add_user_data = array(
    					'phones'=>$this->_post('mobile'),
    					'content'=>'您已注册成功，您可以使用您的手机号码'.$this->_post('mobile').'，密码'.$this->_post('mobile').'来登录携车网，客服4006602822。',
    			);
    			$this->curl_sms($send_add_user_data);
                */

                // dingjb 2015-09-29 10:55:52 切换到云通讯
                $send_add_user_data = array(
                    'phones'  => $this->_post('mobile'),
                    'content' => array(
                        $this->_post('mobile'),
                        $this->_post('mobile')
                    )
                );
                $this->curl_sms($send_add_user_data, null, 4, '37653');
                
    			$send_add_user_data['sendtime'] = time();
    			$this->sms_model->data($send_add_user_data)->add();
    
    			$data['createtime'] = time();
    			$data['mobile'] = $this->_post('mobile');
    			$data['memo'] = '用户注册';
    			$this->memberlog_model->data($data)->add();
    		}
    	}
    	
    	$yzm = $this->_post('yzm');
    	if($yzm && $yzm != $_SESSION['mobileeverify']){
    		$this->error('验证码不正确，预约失败');
    	}
    	
    	$has_replace_code = false;
    	if( !empty($_SESSION['replace_code']) ){
    		//总价减去抵用码的价钱
    		$has_replace_code = true;
    		$order_info['replace_code']= $_SESSION['replace_code'];
    		unset($_SESSION['replace_code']);
    	}
    	
    	/*$replace_code = $this->_post('replace_code');
    	if($replace_code){
    		$chk_code = $this->_check_replace_code($replace_code);
    		if(!$chk_code){
    			$this->error('该抵用码不能使用，请重新填写');
    		}else{
    			//总价减去抵用码的价钱
    			$has_replace_code = true;
    			$order_info['replace_code']= $replace_code;
    			$update = array('status'=>1);
    			$where = array('coupon_code'=>$replace_code);
    			$this->carservicecode_model->where($where)->save($update);//改抵用码已经用过了，不能再用了
    		}
    	}*/
    	
    	$order_info['uid'] = $_SESSION['uid'];
    	$order_info['truename'] = $this->_post('truename');
    	$order_info['address'] = $this->_post('address');
    	$order_info['mobile'] = $this->_post('mobile');
    	$order_info['model_id'] = $_SESSION['model_id'];
    	$order_info['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
    	if($this->_post('car_reg_time')){
    		$order_info['car_reg_time'] = strtotime($this->_post('car_reg_time'));
    	}else{
    		$order_info['car_reg_time'] = 0;
    	}
    	$order_info['engine_num'] = $this->_post('engine_num');
    	$order_info['vin_num'] = $this->_post('vin_num');
    	//$order_info['invoices_type'] = $this->_post('invoices_type');
    	//$order_info['invoices_title'] = $this->_post('invoices_title');
		
    	$order_info['pay_type'] = $this->_post('pay_type');   
    	$order_info['order_time'] = $this->_post('order_time');
    	$order_time2 = intval($this->_post('order_time2'));
    	$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
    
    	$order_info['create_time'] = time();
    	$order_info['remark'] = $this->_post('remark');
    
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
        	if ($value != 99){
        		$order_info['amount'] = $item_amount +99 -$value;
        	}else{
        		$order_info['amount'] = $item_amount;
        	}
        }else{
        	$order_info['amount'] = $item_amount + 99;
        }
    	$id = $this->reservation_order_model->data($order_info)->add();

		if($id){
			$update = array('status'=>1);
			$where = array('coupon_code'=>$order_info['replace_code']);
			$res = $this->carservicecode_model->where($where)->save($update);
		}
    	
    	//插入数据到我的车辆
    	$this->_insert_membercar($order_info);
		if($this->_post('pay_type')==2){
			if($_SESSION['uid']==6591){
				$url = '/carservice/orderpay_test-id-'.$id;
				$this->success('预约成功',U($url));
			}else{
				$url = '/carservice/orderpay-id-'.$id;
				$this->success('预约成功',U($url));
			}
		}else{
    		$this->success('预约成功',U('/myhome/mycarservice'));
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
    		$ret = array('message'=>'抵用码为空');
    		$this->ajaxReturn($ret,'',0);
    	}
		if($replace_code=='016888'){
			$ret = array('price'=>20);
			//用于减去总价，总价减去抵用码的价钱
			$_SESSION['replace_code']= $replace_code;
			$this->ajaxReturn($ret,'',1);
		}else{
			$chk_code = $this->_check_replace_code($replace_code,$mobile);
			if(!$chk_code){
				$ret = array('message'=>'该抵用码不能使用，请重新填写');
				$this->ajaxReturn($ret,'',0);
			}else{
				/*放到订单生成完成后进行
				//改抵用码已经用过了，不能再用了
				$update = array('status'=>1);
				$where = array('coupon_code'=>$replace_code);

				$res = $this->carservicecode_model->where($where)->save($update);
				*/
				$price = $this->get_codevalue($replace_code);
				//if( $res ){
					$ret = array('price'=>$price);
					//用于减去总价，总价减去抵用码的价钱
					$_SESSION['replace_code']= $replace_code;
					$this->ajaxReturn($ret,'',1);
				/*}else{
					$ret = array('message'=>'更新数据失败，请重试用');
					$this->ajaxReturn($ret,'',0);
				}
*/
			}
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
	//支付测试页，账号Z
	function orderpay_test(){
		$map['id'] = $_REQUEST['id'];
		$info = $this->reservation_order_model->where($map)->find();

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
		if($bank_type=='CFT')$url='http://www.xieche.com.cn/txpay/payRequest.php?order_id='.$order_id;
		if($bank_type=='YL')$url='http://www.xieche.com.cn/unionpay/front.php?order_id='.$order_id;
		if($bank_type=='COMM'){
			$coupon_amount = $info['amount'];

			$array='interfaceVersion=1.0.0.0&orderid='.$order_id."&orderDate=".date('Ymd',time())."&orderTime=".date('His',time()).'&tranType=0&amount='.$coupon_amount.'&curType=CNY&orderMono=6222600110030037084& notifyType=1&merURL=http://www.xieche.com.cn/comm_pay/merchant_result.php&goodsURL=http://www.xieche.com.cn/comm_pay/merchant_result.php&netType=0';
			$url='http://www.xieche.com.cn/comm_pay/merchant.php?'.$array;
		}
		if($bank_type!='CFT' and $bank_type!='YL' and $bank_type!='COMM')$url='http://www.xieche.com.cn/txpay/payRequest.php?order_id='.$order_id.'&bank_type='.$bank_type;
		//echo $url;exit;
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

}