<?php
//订单
class MobiletmpAction extends CommonAction {
	protected $offlinespread;
	protected $mBidorder;
	protected $mRepairprocess;
	function __construct() {
		parent::__construct();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->ShopModel = D('shop');
		$this->OrderModel = D('order');
		$this->CouponModel = D('Coupon');
		$this->FsModel = D('fs');//4S店
		$this->CarseriesModel = D('carseries');//车系表
		$this->CarbandModel = D('carbrand');//品牌绑定
		$this->RegionModel = D('Region');
		$this->MemberModel = D('member');//验证码
		$this->model_sms = D('Sms');//手机短信
		$this->PadataModel = D('Padatatest');//接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->ServiceitemModel = D('Serviceitem');
		$this->CommentModel = D('Comment');
		$this->MembercouponModel = D('membercoupon');
		import("ORG.Util.hm");
		$_hmt = new hm("25bc1468b91be39c26580a917bfbf673");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('baiduimg',$_hmtPixel);
		$this->offlinespread = D("offlinespread");//地推表
		$this->mBidorder = D("bidorder");	//事故车订单表
		$this->mRepairprocess = D("repairprocess");//事故车进度表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->AnswerModel = D('answer');//用户保险竞价表
		$this->QuestionModel = D('question');//用户保险竞价表

        //Todo 新增
        $this->car_brand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->car_style_model = M('tp_xieche.carseries','xc_');  //车系号
        $this->car_model_model = M('tp_xieche.carmodel','xc_');  //车型号
		$this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目
		
		$this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段

        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目

        $this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单

        $this->technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表 

        $this->user_model = M('tp_xieche.member', 'xc_');//用户表
        $this->memberlog_model = D('Memberlog');  //用户日志表

        $this->sms_model = D('Sms');  //短信表
        $this->membercar_model = D('Membercar');

        $this->assign("service_cost", 99);
	}
	
	/*
	 * test
	*/
	public function index_test(){
		if ( isset($_REQUEST['pa_id']) && !empty($_REQUEST['pa_id']) ) {
			$_SESSION['pa_id'] = $_REQUEST['pa_id'];
		}else{
			$this->error('参数为空',__APP__.'/Mobile/index');
		}
		$brand_list = $this->car_brand_model->select();
		$this->assign("brand_list", $brand_list);
	
		$this->assign('title',"上门保养-携车网");
	
		$this->display('index_test');
	}

	/*
	@function： 支付方式wap页面
	@author：ysh
	@time：2013-11-20
	*/
	function showpay() {
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}

		$membercoupon_id = $_REQUEST['membercoupon_id'];

		$this->assign('header',$header);
		$this->assign('membercoupon_id',$membercoupon_id);
		$this->display();
	}

	/*
	@function： 支付方式跳转页面
	@author：ysh
	@time：2013-11-20
	*/
	function topay() {
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		$pay_type = $_REQUEST['pay_type'];
		if($pay_type == 1) {//财付通
			header("Location:http://www.xieche.com.cn/txwappay/payRequest.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==2 ) {//支付宝
			header("Location:http://www.xieche.com.cn/apppay/alipayto_new.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==3 ) {//银联
			header("Location:http://www.xieche.com.cn/unionwappay/examples/purchase.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==4 ) {//微信支付
			header("Location:http://www.xieche.com.cn/weixinpay/wxpay.php?order_id={$membercoupon_id}&showwxpaytitle=1");
			exit();
		}
		if($pay_type ==5 ) {//现金支付
			$model_reservation = D('reservation_order');
    	    $data['pay_type'] = 1;
    	    $model_reservation->where("id=$membercoupon_id")->save($data);
			header("Location:http://www.xieche.com.cn/mobile");
			exit();
		}
	}
	

    //Todo 新增
    /**
     * 上门保养
     * @author  chaozhou
     * @date  2014/8/30
     * @version  1.0.0
     */
    public function carservice(){
        if ( isset($_REQUEST['pa_id']) && !empty($_REQUEST['pa_id']) ) {
			$_SESSION['pa_id'] = $_REQUEST['pa_id'];
		}elseif( isset($_REQUEST['wx_id']) && !empty($_REQUEST['wx_id']) ){
			$wx_id = $_REQUEST['wx_id'];
			$palog = $this->PadataModel->where(array('FromUserName'=>$wx_id,'type'=>'2'))->order('id desc')->find();
			if($palog){
				$_SESSION['pa_id'] = $palog['id'];
			}			
			//不是微信进来
		}
		$brand_list = $this->car_brand_model->select();
		$this->assign("brand_list", $brand_list);
	
		$this->assign('title',"上门保养-携车网");
	
		$this->display('carservice');
    }

    /**
     * 车型
     */
    public function ajax_car_model(){
        $serise_id = intval($_POST['series_id']);
        if( $serise_id ){
            $condition['series_id'] = $serise_id;
            $car_model_list = $this->car_model_model->where( $condition )->select();
        }else{
            $car_model_list = "";
        }
        if( $car_model_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('model_list' => $car_model_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该车型下无录入车辆';
        }
        $this->ajaxReturn( $return );
    }

    /**
     * 车款
     */
    public function ajax_car_style(){
        $brand_id = intval( $_POST['brand_id'] );
        if( $brand_id ){
            $condition['brand_id'] = $brand_id;
            $car_style_list = $this->car_style_model->where( $condition )->order('word')->order('word')->group('series_name')->select();
            
        }else{
            $car_style_list = "";
        }

        if( $car_style_list ){
            $return['errno'] = '0';
            $return['errmsg'] = 'success';
            $return['result'] = array('style_list' => $car_style_list );
        }else{
            $return['errno'] = '1';
            $return['errmsg'] = '该品牌下无录入车系';
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
        $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        $this->redirect('Mobiletmp/sel_item');
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
            $this->redirect('Mobiletmp/carservice');
            return false;
        }
        
        
        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_model = $this->car_model_model->where($style_param)->find();
        $model_name = $car_model['model_name'];
        
        $series_id = $car_model['series_id'];
        $car_style = $this->car_style_model->where( array('series_id'=>$series_id) )->find();
        
        $style_name = $car_style['series_name'];
        
        $car_name = $style_name." - ".$model_name;;
        
        $brand_param['brand_id'] = $car_style['brand_id'];
        $car_brand = $this->car_brand_model->where($brand_param)->find();
        if($car_brand){
        	$brand_name = $car_brand['brand_name'];
        	$car_name = $brand_name." - ".$car_name;
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
        	$car_style['norms'] = '4L';
        }else{
        	$car_style['norms'] = $oil_num."L";
        }
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_model['oil_type']){
        	case 'a':
        		$oil_param['type'] = array('in','1,2,3');
        		break;
        	case 'b':
        		$oil_param['type'] = array('in','2,3');
        		break;
        	case 'c':
        		$oil_param['type'] = 3;
        		break;
        	default:
        		$oil_param['type'] = 0;
        		break;
        }
        
        //所有物品详情
        $oil_list_all = $this->item_oil_model->where( $oil_param )->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
        	$oil_item[$nors['name']][$nors['norms']] = $nors;
        }
        $oil_param['norms'] = 4;
        //符合要求的品牌详情
        $item_sets=array();
        $oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select();
        
        foreach( $oil_name_distinct as $keys=>$names ){
        	$item_sets[$keys]['id'] = $names['id'];
        	$item_sets[$keys]['name'] = $names['name'];
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
        
        }
        $car_style['type'] = '未设定';
        if($car_style['oil_type']==1){
        	$car_style['type'] = '矿物油';
        }
        if($car_style['oil_type']==2){
        	$car_style['type'] = '半合成油';
        }
        if($car_style['oil_type']==3){
        	$car_style['type'] = '全合成油';
        }
        
        //项目
        $style_id = $_SESSION['model_id'];
        $item_set = array();
        if( $style_id ){
        	$condition['model_id'] = $style_id;
        	$style_info = $this->car_model_model->where($condition)->find();
        	$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

        	if( $set_id_arr ){
        		foreach( $set_id_arr as $k=>$v){
        			if(is_array($v)){
        				foreach( $v as $_k=>$_v){
        					$item_condition['id'] = $_v;
        					$item_info_res = $this->filter_model->where($item_condition)->find();
        					$item_info['id'] = $item_info_res['id'];
        					$item_info['name'] =  mb_substr( $item_info_res['name'], 0 ,mb_strpos($item_info_res['name'],' ') );
        					//$item_info['name'] = $item_info_res['name'];
        					$item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
        					$item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
        					$item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
        					$item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
        					$item_set[$k][$_k] = $item_info;
							if($item_info['price']=='0'){ unset($item_set[$k][$_k]); }
							//排除数组中缺乏元素页面选项空白的问题
							if(!$item_set[$k][0] and $item_set[$k][1]){
								$item_set[$k][0] = $item_set[$k][1];
								unset($item_set[$k][1]);
							}
        				}
						//机滤空滤空气滤按价格排序
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

        $this->assign('car_style', $car_style);
        $this->assign('item_sets', $item_sets);
        $this->assign('item_set', $item_set);
        if ( isset($_SESSION['pa_id']) ){
        	$this->assign('pa_id', $_SESSION['pa_id']);
        }
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
		//用户没有绑定的情况，让他显示验证码
        if ( isset($_SESSION['pa_id']) ){
	        $weixin_status = 0;
	        $palog = $this->PadataModel->where(array('id'=>$_SESSION['pa_id'],'type'=>'2'))->order('id desc')->find();
	        $Paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
	        if(!$Paweixin){
	        	$id = $this->PaweixinModel->add(array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time()));
	        	$Paweixin_id = $id;
	        	$weixin_status = 1;//要验证的状态
	        }else{
				$Paweixin_id = $Paweixin['id'];
			}
        }else{
        	if ( !empty( $_SESSION['uid'] ) ) {
        		$weixin_status = 0;
        	}else{
    	    	//不从微信过来
	        	$weixin_status = 1;
        	}
        	$Paweixin_id = '';
        }
    	$userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();
    	unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
        if($this->_post('CheckboxGroup0_res')){
            $_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res')); //机油id
            $_SESSION['oil_detail'] = array(
            		$this->_post('oil_1_id') => $this->_post('oil_1_num'),
            		$this->_post('oil_2_id') => $this->_post('oil_2_num')
            );
        }
        if($this->_post('CheckboxGroup1_res')){
            $_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res')); //机滤id
        }
        if($this->_post('CheckboxGroup2_res')){
            $_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res')); //空气滤清id
        }
        if($this->_post('CheckboxGroup3_res')){
            $_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res')); //空调滤清id
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
        
        $this->assign('item_num',$item_num);
        $this->assign('pa_id',$Paweixin_id);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('weixin_status',$weixin_status);
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
        
        $_SESSION['pa_mobile'] = $mobile = $_REQUEST['mobile'];
        $_SESSION['pa_code'] = rand(10000,999999);
        /*
        $sms = array(
                'phones'=>$mobile,
                'content'=>"您的验证码：".$_SESSION['pa_code'], 
        );
        $this->curl_sms($sms); 
        */
        // dingjb 2015-09-29 13:07:13 切换到云通讯
        $sms = array(
            'phones' => $mobile,
            'content'=> array(
                $_SESSION['pa_code']
            )
        );
        $this->curl_sms($sms, null, 4, '37656'); 
        
        $sms['sendtime'] = time();
        $this->model_sms->data($sms)->add();

        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $this->ajaxReturn($return);
       
    }

    /**
     * 提交订单
     * @author  chaozhou
     * @date  2014/8/23
     * @version  1.0.1 
     */
    public function create_order(){
        session_start();

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
                $member_data['fromstatus'] = '50';//上门宝洋
                $_SESSION['uid'] = $this->user_model->data($member_data)->add();
                /*
                $send_add_user_data = array(
                    'phones'=>$this->_post('mobile'),
                    'content'=>'您已注册成功，您可以使用您的手机号码'.$this->_post('mobile').'，密码'.$this->_post('mobile').'来登录携车网，客服4006602822。',
                );
                $this->curl_sms($send_add_user_data);
                */
                // dingjb 2015-09-29 11:55:13 切换到云通讯
                $send_add_user_data = array(
                    'phones' => $this->_post('mobile'),
                    'content'=> array(
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

        $yzm = $this->_post('code');
        if($yzm && $yzm != $_SESSION['pa_code']){
        	$this->error('验证码不正确，预约失败');
        	exit;
        }
        
        $has_replace_code = false;
        if( !empty($_SESSION['replace_code']) ){
        	//总价减去抵用码的价钱
        	$has_replace_code = true;
        	$order_info['replace_code']= $_SESSION['replace_code'];
        	unset($_SESSION['replace_code']);
        }
        /*
        $replace_code = $this->_post('replace_code');
        $has_replace_code = false;
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
        
        }
        */
        
        $order_info['pay_type'] = $this->_post('pay_type');
        
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

        $order_info['order_time'] = $this->_post('order_time');
        $order_time2 = intval($this->_post('order_time2'));
        $order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;

        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
		
        //修改手机号有修改的话 更新微信表里新的手机号
        /*
        if ( !empty( $order_info['mobile'] ) ) {
        	$palog = $this->PadataModel->where(array('id'=>$_SESSION['pa_id'],'type'=>'2'))->order('id desc')->find();
        	$Paweixin = $this->PaweixinModel->field('mobile')->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->find();
        	if( $Paweixin && $Paweixin['mobile'] != $order_info['mobile'] ){
        		$update = array('mobile'=>$order_info['mobile']);
        		$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName'],'type'=>'2'))->save($update);
        	}
        } */
        
    	$oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;
    	
    	//计算总价
    	if($_SESSION['item_0']){
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
    	if($_SESSION['item_1']){
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
    	if($_SESSION['item_2']){
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
    	if($_SESSION['item_3']){
    		if($_SESSION['item_3'] == '-1'){
    			$item_list['3']['id'] = 0;
    			$item_list['3']['name'] = "自备配件";
    			$item_list['3']['price'] = 0;
    		}else{
	    		$kongtiao_id = $item_condition['id'] = $_SESSION['item_3'];
	    		$item_list['3'] = $this->item_model->where($item_condition)->find();
	    		$kongtiao_price = $item_list['2']['price'];
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
        
        
        if($has_replace_code){
        	$order_info['amount'] = $item_amount;
        }else{
        	$order_info['amount'] = $item_amount + 99;
        }
        
        //print_r($order_info);
        $order_id = $this->reservation_order_model->data($order_info)->add();
        //echo $this->reservation_order_model->getLastsql();exit;
        
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
		header("Location:http://www.xieche.com.cn/mobiletmp/order_success-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
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
    
    //ajax验证抵用码
    function valid_coupon_code(){
    	unset($_SESSION['replace_code']);
    	$replace_code = @$_POST['coupon_code'];
    	if(!$replace_code){
    		$ret = array('message'=>'抵用码为空');
    		$this->ajaxReturn($ret,'',0);
    	}
    	$chk_code = $this->_check_replace_code($replace_code);
    	if(!$chk_code){
    		$ret = array('message'=>'该抵用码不能使用，请重新填写');
    		$this->ajaxReturn($ret,'',0);
    	}else{
    		//改抵用码已经用过了，不能再用了
    		$update = array('status'=>1);
    		$where = array('coupon_code'=>$replace_code);
    		$res = $this->carservicecode_model->where($where)->save($update);
    		if( $res ){
    			$ret = array('price'=>99);
    			//用于减去总价，总价减去抵用码的价钱
    			$_SESSION['replace_code']= $replace_code;
    			$this->ajaxReturn($ret,'',1);
    		}else{
    			$ret = array('message'=>'更新数据失败，请重试用');
    			$this->ajaxReturn($ret,'',0);
    		}
    	}
    }
    
    //检查抵用码能否使用
    private  function _check_replace_code($replace_code){
    	$where = array(
    			'coupon_code' => $replace_code,
    			'status' => 0
    	);
    	$count = $this->carservicecode_model->where($where)->count();
    	if($count > 0){
    		return true;
    	}else{
    		return false;
    
    	}
    }

    /**
     * 下单成功
     * @author  chaozhou
     * @date  2014/8/30
     * @version  1.0.1 
     */
    public function order_success(){
        $order_id = intval($this->_get('order_id'));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
        	$this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
    }

    /**
     * 订单列表
     * @author  chaozhou
     * @date  2014/9/2
     * @version  1.0.0
     */
    public function mycarservice(){
        if($_REQUEST['pa_id']){
        	$_SESSION['pa_id'] = $_REQUEST['pa_id'];
            $Padata = $this->PadataModel->where(array('id'=>$_REQUEST['pa_id']))->find();
            $Pawx = $this->PaweixinModel->where(array('wx_id'=>$Padata['FromUserName']))->find();
            $mobile = $Pawx['mobile'];
            if (!$mobile) {
            	$this->redirect('mobile/login_verify', array('pa_id'=>$_REQUEST['pa_id']));
            }
        }else{
        	//没有从微信过来的情况
        	$uid = $_SESSION['uid'];
        	$userinfo = $this->user_model->where(array('uid'=>$uid,'status'=>'1'))->find();
        	$mobile = $userinfo['mobile'];
        }

        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,40);
        foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        if(!$_REQUEST['p']){
            $p->parameter = "index/".$p->parameter;
        }

        $map['mobile'] = $mobile;
        
        // 分页显示输出
        $page = $p->show();
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);

                if($value['technician_id']){
                    $condition['id'] = $value['technician_id'];
                    $technician_info = $this->technician_model->where($condition)->find();
                    $list[$key]['technician_name'] = $technician_info['truename'];
                }

                //车型
                $style_param['model_id'] = $value['model_id'];
                $car_model = $this->car_model_model->where($style_param)->find();
                $model_name = $car_model['model_name'];
                
                $series_id = $car_model['series_id'];
                $car_style = $this->car_style_model->where( array('series_id'=>$series_id) )->find();
                
                $style_name = $car_style['series_name'];
                
                $car_name = $style_name." - ".$model_name;;
                
                $brand_param['id'] = $car_style['brand_id'];
                $car_brand = $this->car_brand_model->where($brand_param)->find();
                if($car_brand){
                	$brand_name = $car_brand['brand_name'];
                	$car_name = $brand_name." - ".$car_name;
                }
               	$list[$key]['car_name'] = $car_name;

               	//商品详情
               	$order_items = unserialize($value['item']);               	
               	if( !empty( $order_items['oil_detail'] ) ){
               		$item_oil_price = 0;
               		$oil_data = $order_items['oil_detail'];
               		foreach ( $oil_data as $id=>$num){
               			if($num > 0){
               				$res = $this->item_oil_model->field('name,price,norms')->where( array('id'=>$id))->find();
               				$item_oil_price += $res['price']*$num;
							$item_norms += $res['norms']*$num;
               				$name = $res['name'];
               			}
               		}
               		$oil_param['id'] =  $order_items['oil_id'];
               		$item_list['0']['id'] = $order_items['oil_id'];
               		$item_list['0']['name'] = $name;
               		$item_list['0']['price'] = $item_oil_price;
					$item_list['0']['norms'] = $item_norms;
					
					if( $order_items['oil_id'] == '-1' ){
						$item_list['0']['id'] = 0;
						$item_list['0']['name'] = "自备配件";
						$item_list['0']['price'] = 0;
					}
               	}
               	if($order_items['filter_id']){
               		if($order_items['filter_id'] == '-1'){	//自备配件的情况
               			$item_list['1']['id'] = 0;
               			$item_list['1']['name'] = "自备配件";
               			$item_list['1']['price'] = 0;
               		}else{
	               		$item_condition['id'] = $order_items['filter_id'];
	               		$item_list['1'] = $this->item_model->where($item_condition)->find();
	               		$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
               		}
               	}
               	if($order_items['kongqi_id']){
               		if( $order_items['kongqi_id'] == '-1' ){
               			$item_list['2']['id'] = 0;
               			$item_list['2']['name'] = "自备配件";
               			$item_list['2']['price'] = 0;
               		}else{
	               		$item_condition['id'] = $order_items['kongqi_id'];
	               		$item_list['2'] = $this->item_model->where($item_condition)->find();
	               		$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
               		}
               	}
               	if($order_items['kongtiao_id']){
               		if( $order_items['kongtiao_id'] == '-1' ){
               			$item_list['3']['id'] = 0;
               			$item_list['3']['name'] = "自备配件";
               			$item_list['3']['price'] = 0;
               		}else{
	               		$item_condition['id'] = $order_items['kongtiao_id'];
	               		$item_list['3'] = $this->item_model->where($item_condition)->find();
	               		$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
               		}
               	}
               	$list[$key]['item_list'] = $item_list;
            }
        }
        
        $condition = array();
        $condition['status'] = 1;
        $technician_list = $this->technician_model->where($condition)->select();
        //print_r($list);
        $this->assign('data', $map);
        $this->assign('list', $list);
        $this->assign('pa_id', $_SESSION['pa_id']);
        $this->assign('page',$page);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }

    /**
     * 订单详情
     * @author  chaozhou
     * @date  2014/9/1
     * @version  1.0.0 
     */
    public function mycarservice_detail(){
        $order_id = $this->_get('order_id');
		if( empty( $_SESSION['pa_id'] ) ){
			//不是从微信过来的
            redirect('http://www.xieche.com.cn/mobilecar-mycarservice_detail-order_id-'.$order_id, 0, '页面跳转中...');
		}else{
            redirect('http://www.xieche.com.cn/mobilecar-mycarservice_detail-pa_id-'.$_SESSION['pa_id'].'-order_id-'.$order_id, 0, '页面跳转中...');
        }

        

        $condition['id'] = $order_id;
        $order_info = $this->reservation_order_model->where($condition)->find();

        //支付状态
        $order_info['pay_status_name'] = $this->getPayStatusName($order_info['pay_status']);
		
        
        //车型
        $style_param['model_id'] = $order_info['model_id'];
        $car_model = $this->car_model_model->where($style_param)->find();
        $model_name = $car_model['model_name'];
        
        $series_id = $car_model['series_id'];
        $car_style = $this->car_style_model->where( array('series_id'=>$series_id) )->find();
        
        $style_name = $car_style['series_name'];
        
        $car_name = $style_name." - ".$model_name;;
        
        $brand_param['id'] = $car_style['brand_id'];
        $car_brand = $this->car_brand_model->where($brand_param)->find();
        if($car_brand){
        	$brand_name = $car_brand['brand_name'];
        	$car_name = $brand_name." - ".$car_name;
        }
        $order_info['car_name'] = $car_name;
        
        //商品详情
        $item_num = 1;
        $order_items = unserialize($order_info['item']);
        if( !empty( $order_items['oil_detail'] ) ){
        	$item_oil_price = 0;
        	$oil_data = $order_items['oil_detail'];
        	foreach ( $oil_data as $id=>$num){
        		if($num > 0){
        			$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
        			$item_oil_price += $res['price']*$num;
					$item_norms += $res['norms'];
        			$name = $res['name'];
        		}
        	}
        	$oil_param['id'] =  $order_items['oil_id'];
        	$item_list['0']['id'] = $order_items['oil_id'];
        	$item_list['0']['name'] = $name;
        	$item_list['0']['price'] = $item_oil_price;
			$item_list['0']['norms'] = $item_norms;
			
			if( $order_items['oil_id'] == '-1' ){
				$item_list['0']['id'] = 0;
				$item_list['0']['name'] = "自备配件";
				$item_list['0']['price'] = 0;
			}
			$item_num++;
        }
        if($order_items['filter_id']){
        	if($order_items['filter_id'] == '-1'){	//自备配件的情况
        		$item_list['1']['id'] = 0;
        		$item_list['1']['name'] = "自备配件";
        		$item_list['1']['price'] = 0;
        	}else{
	        	$item_condition['id'] = $order_items['filter_id'];
	        	$item_list['1'] = $this->item_model->where($item_condition)->find();
	        	$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
        	}
        	$item_num++;
        }
        if($order_items['kongqi_id']){
        	if( $order_items['kongqi_id'] == '-1' ){
        		$item_list['2']['id'] = 0;
        		$item_list['2']['name'] = "自备配件";
        		$item_list['2']['price'] = 0;
        	}else{
	        	$item_condition['id'] = $order_items['kongqi_id'];
	        	$item_list['2'] = $this->item_model->where($item_condition)->find();
	        	$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
        	}
        	$item_num++;
        }
        if($order_items['kongtiao_id']){
        	if( $order_items['kongtiao_id'] == '-1' ){
        		$item_list['3']['id'] = 0;
        		$item_list['3']['name'] = "自备配件";
        		$item_list['3']['price'] = 0;
        	}else{
        		$item_condition['id'] = $order_items['kongtiao_id'];
        		$item_list['3'] = $this->item_model->where($item_condition)->find();
        		$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
        	}
        	$item_num++;
        }
        if ( $order_info['replace_code'] ) {
        	$item_amount = 0;
        }else{
        	$item_amount = 99;
        }
        if(is_array($item_list)){
        	foreach ($item_list as $key => $value) {
        		$item_amount += $value['price'];
        	}
        }
        $this->assign('item_num',$item_num);
        $this->assign('replace_code',$order_info['replace_code']);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        if ( isset($_SESSION['pa_id']) ) {
        	$this->assign("pa_id", $_SESSION['pa_id']);
        }else{
			$this->assign("pa_id", $_REQUEST['pa_id']);
		}
        $this->assign("order_info", $order_info);

        $this->display();
    }

    private function getStatusName($status){
        switch ($status) {
            case '0':
                return "等待处理 ";
                break;
            
            case '1':
                return "预约确认";
                break;

            case '2':
                return "已分配技师";
                break;

            case '3':
                return "服务已完成";
                break;

            default:
                return "等待处理";
                break;
        }
    }

    //0.未支付;1.已支付;2.申请退款;3.已退款
    private function getPayStatusName($status){
        switch ($status) {
            case '0':
                return "未支付";
                break;
            
            case '1':
                return "已支付";
                break;

            case '2':
                return "申请退款";
                break;

            case '3':
                return "已退款";
                break;

            default:
                return "未支付";
                break;
        }
    }
    //手机码验证
    function CheckCode2(){
    	$mobile = $_REQUEST['mobile'];
    	$truename = $_REQUEST['truename'];
    	$code = $_REQUEST['code'];
    	$Paweixin_id = $_REQUEST['Paweixin_id'];
    	
    	if($_SESSION['pa_code'] == $code && $_SESSION['pa_mobile'] == $mobile){
    		if( !empty($Paweixin_id) ){
	    		$pa_res = $this->PaweixinModel->where(array('wx_id'=>$Paweixin_id))->find();
	    		if( !empty($pa_res['mobile']) && ($pa_res['mobile'] == $mobile)  ){//存在跳出手机号已注册
	    				echo "3";
	    		}else{
	    			$palog = $this->PadataModel->where(array('id'=>$_SESSION['pa_id']))->order('id desc')->find();
	    			$weixincount = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->count();
	    			if($weixincount == 0){
	    				$data = array('wx_id'=>$palog['FromUserName'],'type'=>'2','create_time'=>time(),'mobile'=>$mobile,'bind_time'=>time());
	    				$Paweixin_id = $this->PaweixinModel->add($data);
	    			}else{
	    				//更新用户填写的数据
	    				$updateData = array();
	    				//传过来的和数据库里的不一样
	    				if ( $pa_res['mobile'] != $mobile ) {
	    					//更新新的手机号并且已经表示该手机号已经绑定了
	    					$updateData['mobile'] = $mobile;
	    				}
	   					$this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->save($updateData);
	    			}
	    			echo "1";
	    		}
    		}else{
    			echo "1";
    			//没有从微信过来的情况
    		}
    	}else{
    		echo "2";
    	}
    }
}
