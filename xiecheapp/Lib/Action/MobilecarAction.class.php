<?php
//订单
class MobilecarAction extends CommonAction {
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
		$this->offlinespread = D("offlinespread");//地推表
		$this->mBidorder = D("bidorder");	//事故车订单表
		$this->mRepairprocess = D("repairprocess");//事故车进度表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->AnswerModel = D('answer');//用户保险竞价表
		$this->QuestionModel = D('question');//用户保险竞价表
		$this->spreadpicModel = D('spreadpic');//接收微信订单数据表

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

        $this->checkreport_model = M('tp_xieche.checkreport_total', 'xc_');  //检测报告表
        $this->check_step_model = M('tp_xieche.check_step', 'xc_');  //技师步骤表
        $this->technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表 
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //保养项目
        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目

        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
		$this->act_count = M('tp_xieche.act_count','xc_');  // 活动访问统计
        
        $this->city = D('city');   //城市表
        $this->item_city = D('item_city');   //新城市表
        $this->item_area = D('item_area');   //地区表

        //新版优惠券相关模型
        $this->newCouponModel = D('NewCoupon');
        $this->newCouponCodeModel = D('NewCouponCode');
        
        $this->new_carbrand = D('new_carbrand');   //新品牌表
        $this->new_carseries = D('new_carseries');   //新车系表
        $this->new_carmodel = D('new_carmodel');   //新车型表
        
        $this->new_oil = D('new_item_oil');  //保养机油        
        $this->new_filter = D('new_item_filter');  //保养三滤配件
	}
	/*
	 * 服务介绍
	 */
	function carservice_des(){
		$this->display();
	}
	/*
	 * 检测报告样板
	 */
	function check_report(){
		$this->display();
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
			header("Location:".WEB_ROOT."/txwappay/payRequest.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==2 ) {//支付宝
			header("Location:".WEB_ROOT."/apppay/alipayto_new.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==3 ) {//银联
			header("Location:".WEB_ROOT."/unionwappay/examples/purchase.php?membercoupon_id={$membercoupon_id}");
			exit();
		}
		if($pay_type ==4 ) {//微信支付
			header("Location:".WEB_ROOT."/weixinpay/wxpay.php?order_id={$membercoupon_id}&showwxpaytitle=1");
			exit();
		}
		if($pay_type ==5 ) {//现金支付
			$model_reservation = D('reservation_order');
    	    $data['pay_type'] = 1;
    	    $model_reservation->where("id=$membercoupon_id")->save($data);
			header("Location:".WEB_ROOT."/mobile");
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
		if($_REQUEST['param']){
			//echo $_REQUEST['param'];
		}
		
		if($_REQUEST['source_id']==1){
			//echo $_REQUEST['source_id'];
                    
		}
		
		if($_REQUEST['ordertype']){ 
                    $ordertype = intval(trim($_REQUEST['ordertype']));
                    $_SESSION['ordertype'] = $this->get_true_orderid($ordertype);
		}else{
            unset($_SESSION['ordertype']);
        }
		
		
		
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

        if(isset($_REQUEST['ali_id']) && !empty($_REQUEST['ali_id'])){
            $_SESSION['ali_id'] = $_REQUEST['ali_id'];
        }

		$brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        
		foreach($brand_list as $k=>$v){
			$car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
			foreach($car_model_list as $key=>$value){
				$map['series_id'] = $value['series_id'];
				$map['is_show'] = '1';
                
				$count = $this->new_carmodel->where($map)->count();
				if($count==0){
					unset($car_model_list[$key]);
				}
			}
			if(count($car_model_list)==0){
				unset($brand_list[$k]);
			}
		}
        $this->assign('fromMyCoupon', $_SESSION['from_my_coupon']);
        //print_r($brand_list);exit;
        
		$this->assign("brand_list", $brand_list);
	
		$this->assign('title',"上门保养-携车网");

		$this->display('carservice_new_1');
    }

    /**
     * 车型
     */
    public function ajax_car_model(){
        $serise_id = intval($_POST['series_id']);
        if( $serise_id ){
            $condition['series_id'] = $serise_id;
            $condition['is_show'] = 1;
            //$condition['oil_mass'] = array('neq','');
            //$condition['oil_type'] = array('neq','');
            
            $car_model_list = $this->new_carmodel->where( $condition )->order('model_id asc')->select();
            
            //echo  $this->new_carmodel->getLastSql();
            //去掉<br>  wql@20150825
            foreach ($car_model_list as $k => $v) {
                if(strstr($v['model_name'], '<br>')){
                    $car_model_list[$k]['model_name'] = str_replace('<br>', ' ', $v['model_name']) ;
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
            $condition['is_show'] = 1 ;
            $car_style_list = $this->new_carseries->where( $condition )->order('word')->group('series_name')->select();
			/*foreach($car_style_list as $key=>$value){
				$map['series_id'] = $value['series_id'];
				$map['is_show'] = '1';
				$count = $this->new_carmodel->where($map)->count();
				if($count==0){
					unset($car_style_list[$key]);
				}
			}*/
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
        $code = $this->_get('code');

		if(!empty($code)){
			$first = substr($code,0,1);
			$permit_array = array('a','b','r','s','t','u','v','w','x','y');
			if(!in_array($first,$permit_array)){
				$this->error('抱歉,请联系客服,核实您的券码有效性');
			}else{
				$map['status'] = 0;
				$map['coupon_code'] = $code;
				$info = $this->carservicecode_model->where($map)->find();
				if($info){
					unset($_SESSION['replace_code']);
					$_SESSION['replace_code'] = htmlspecialchars(addslashes($code));
				}else{
					$this->error('抱歉,券码验证失败,请联系客服');
				}
            }
		}else{
           unset($_SESSION['replace_code']);
        }
        
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        //$this->redirect('Mobilecar/sel_item');
        //如果是一元检测等三种套餐，直接跳到下单页面
        if($_SESSION['ordertype']==70||$_SESSION['ordertype']==15||$_SESSION['ordertype']==54||$_SESSION['ordertype']==62||$_SESSION['ordertype']==65||$_SESSION['ordertype']==73){
            $this->redirect('Mobilecar/carservice_wx_order'); 
        }else{
            $this->redirect('Mobilecar/sel_item');
        }
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
            $this->redirect('Mobilecar/carservice');
            return false;
        }
        
        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_model = $this->new_carmodel->where($style_param)->find();
        //检测是否有<br>   wql@20150825
        if(strstr($car_model['model_name'],'<br>')){
            $model_name = str_replace('<br>', ' ', $car_model['model_name']); 
        }else{
            $model_name = $car_model['model_name'];
        }
        
        
        $series_id = $car_model['series_id'];
        $car_style = $this->new_carseries->where( array('series_id'=>$series_id) )->find();
        
        $style_name = $car_style['series_name'];
        
        $car_name = $style_name." - ".$model_name;;
        
        $brand_param['brand_id'] = $car_style['brand_id'];
        $car_brand = $this->new_carbrand->where($brand_param)->find();
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
        $oil_list_all = $this->new_oil->where( $oil_param )->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
        	$nors['name'] = rtrim($nors['name'],'装');
        	$oil_item[$nors['name']][$nors['norms']] = $nors;
        }
        $oil_param['norms'] = 4;

		//根据券码计算套餐配件
		$first = substr($_SESSION['replace_code'],0,1);
		if($first==r or $first==y){
			$oli_taocan = array('49','59');
		}
		if($first==s or $first==x){
			$oli_taocan = array('47','48');
		}
		if($first==t or $first==w){
			$oli_taocan = array('50','51');
		}
		if($first==u or $first==b or $first==a){
			$oli_taocan = array();
		}

        //符合要求的品牌详情
        $item_sets=array();
        $oil_name_distinct = $this->new_oil->order('price')->where($oil_param)->select();
        
        foreach( $oil_name_distinct as $keys=>$names ){
        	
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
        
        }

		//排除套餐机油类型之外的机油选项
		if(isset($oli_taocan)){
			foreach($item_sets as $_kk=>$_vv){
				if(!in_array($_vv['id'],$oli_taocan)){
					unset($item_sets[$_kk]);
				}
			}
			//print_r($item_sets);
			foreach($item_sets as $_kk=>$_vv){
				$item_set[] = $_vv;
			}
			$item_sets = $item_set;
		}
        //排除大通柴油
        foreach($item_sets as $_kk=>$_vv){
            if($_vv['id']==62){
                unset($item_sets[$_kk]);
            }
        }
        foreach($item_sets as $_kk=>$_vv){
            $item_set[] = $_vv;
        }
        $item_sets = $item_set;

		if($_SESSION['order_type']>15 and empty($item_sets)){
			$tips = "该车型无法使用套餐机油";
			$this->assign('tips', $tips);
		}

        //按照总价钱排序
        usort($item_sets, function($a,$b){
        	return strcmp($a["price"], $b["price"]);
        });

        $car_style['type'] = '未设定';
        if($car_style['oil_type']=='a'){
        	$car_style['type'] = '矿物油';
        }
        if($car_style['oil_type']=='b'){
        	$car_style['type'] = '半合成油';
        }
        if($car_style['oil_type']=='c'){
        	$car_style['type'] = '全合成油';
        }
        
        //项目
        $style_id = $_SESSION['model_id'];
        $item_set = array();
        if( $style_id ){
        	$condition['model_id'] = $style_id;
        	$style_info = $this->new_carmodel->where($condition)->find();
        	$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

        	if( $set_id_arr ){
        		foreach( $set_id_arr as $k=>$v){
        			if(is_array($v)){
        				foreach( $v as $_k=>$_v){
        					$item_condition['id'] = $_v;
							$item_condition['name'] = array('notlike','%pm2%');

        					$item_info_res = $this->new_filter->where($item_condition)->find();
                            if(!$item_info_res){
                                continue;
                            }
                            
        					$item_info['id'] = $item_info_res['id'];
        					//$item_info['name'] =  mb_substr( $item_info_res['name'], 0 ,mb_strpos($item_info_res['name'],' ') );
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
                            //删除价格为0的配件
							if($item_info['price']=='0'){ unset($item_set[$k][$_k]); }
							//排除数组中缺乏元素页面选项空白的问题
							if(!$item_set[$k][0] and $item_set[$k][1]){
								$item_set[$k][0] = $item_set[$k][1];
								unset($item_set[$k][1]);
							}
        				}
                        
                        //重置索引
                        $item_set[$k] =  array_values($item_set[$k]) ;
                        
//						//机滤空滤空气滤按价格排序
//						foreach($item_set[$k] as $kk=>$vv){
//							if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
//								$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
//								$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
//							}else{
//								$item_set_new[$k][$kk] = $item_set[$k][$kk];
//							}
//						}
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

        foreach($item_set as $a=>$b){
            foreach($b as $aa=>$bb){
                $item_set_sort[$a][]=$bb;
            }
        }

		if($first == 'u' or $first == 'b' or $first == 'a'){
            $item_set_sort = array();
			$item_sets = array();
		}

        $this->assign('car_style', $car_style);
        $this->assign('item_sets', $item_sets);
        $this->assign('item_set', $item_set_sort);
        if ( isset($_SESSION['pa_id']) ){
        	$this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->assign('title',"选择项目-上门保养-携车网");
		$code_fenzhi = array('v');//判断优惠券的分支
        //有码时使用sel_item_withcode模板
        if($_SESSION['replace_code']!=null and !in_array($first,$code_fenzhi)){
            $this->assign('replace_code', $_SESSION['replace_code']);
            $this->display('sel_item_withcode');
        }else{
            $this->display('sel_item');
        }
     }

    /**
     * 下单页面
     * @author  chaozhou
     * @date  2014/8/22
     * @version  1.0.1 
     */
    public function order(){
        session_start();
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);
        if( !empty($_POST['CheckboxGroup0_res']) ){
            $_SESSION['item_0'] = intval($this->_post('CheckboxGroup0_res')); //机油id
            $_SESSION['oil_detail'] = array(
                    $this->_post('oil_1_id') => $this->_post('oil_1_num'),
                    $this->_post('oil_2_id') => $this->_post('oil_2_num')
            );
        }
        if( !empty($_POST['CheckboxGroup1_res']) ){
            $_SESSION['item_1'] = intval($this->_post('CheckboxGroup1_res')); //机滤id
        }
        if( !empty($_POST['CheckboxGroup2_res']) ){
            $_SESSION['item_2'] = intval($this->_post('CheckboxGroup2_res')); //空气滤清id
        }
        if( !empty($_POST['CheckboxGroup3_res']) ){
            $_SESSION['item_3'] = intval($this->_post('CheckboxGroup3_res')); //空调滤清id
        }
        $item_num = 1;
        if(!empty($_SESSION['item_0'])){
            if( $_SESSION['item_0'] == '-1' ){
                $item_list[0]['id'] = 0;
                $item_list[0]['name'] = "自备配件";
                $item_list[0]['price'] = 0;
                $item_list[0]['oil_1_name'] = '';
                $item_list[0]['oil_1_num'] = '';
                $item_list[0]['oil_1_price'] = '';
                $item_list[0]['oil_2_name'] = '';
                $item_list[0]['oil_2_num'] = '';
                $item_list[0]['oil_2_price'] = '';
            }else{
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $oil_data = $_SESSION['oil_detail'];
                
                foreach ( $oil_data as $id=>$num){
                    if($num > 0){
                        $res = $this->new_oil->field('name,price,norms')->where( array('id'=>$id))->find();
                        if($res['norms'] == 1){
                            $item_list[0]['oil_1_id'] = $id;
                            $item_list[0]['oil_1_name'] = $res['name'];
                            $item_list[0]['oil_1_num'] = $num;
                            $item_list[0]['oil_1_price'] = $res['price']*$num;
                        }else{
                            $item_list[0]['oil_2_id'] = $id;
                            $item_list[0]['oil_2_name'] = $res['name'];
                            $item_list[0]['oil_2_num'] = $num;
                            $item_list[0]['oil_2_price'] = $res['price']*$num;
                        }
                        $item_oil_price += $res['price']*$num;
                        $item_list[0]['price'] = $item_oil_price;
                    }
                }
            }
            $item_num++;
        }
        //var_dump($item_list[0]);exit;
        if(!empty($_SESSION['item_1'])){
            if($_SESSION['item_1'] == '-1'){
                $item_list['1']['id'] = 0;
                $item_list['1']['name'] = "自备配件";
                $item_list['1']['price'] = 0;
            }else{
                $item_condition['id'] = $_SESSION['item_1'];
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
                
                $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
                //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));//去掉后面的规格 by bright
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
                
                $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
                //$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
                
                $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
                //$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
            }
            $item_num++;
        }

        if(!empty($_SESSION['replace_code'])){
            $item_amount = 0;
        }else{
            $item_amount = 99;
        }
		$chaidan = array('r','s','t','w','x','y');
		$first = substr($_SESSION['replace_code'],0,1);
		//机油机滤服务费套餐
        if(is_array($item_list) and in_array($first,$chaidan)){
			$item_amount = $item_list['2']['price']+$item_list['3']['price'];
			$item_list['0']['oil_1_price'] = 0;
			$item_list['0']['oil_2_price'] = 0;
			$item_list['1']['price'] = 0;
		}else{
            foreach ($item_list as $key => $value) {
                $item_amount += $value['price'];
            }
        }
        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }
		$city_model = D('city');
		$map['is_show'] = 1;
		$city_info = $city_model->where($map)->select();

        if ($fromMyCoupon = $_SESSION['from_my_coupon']) {//从我的优惠券进入
            $this->assign('fromMyCoupon', $fromMyCoupon);
            $myCouponDikou = $this->newCouponModel->getUserCouponAmount($_SESSION['new_coupon_id']);
            $this->assign('my_coupon_dikou', $myCouponDikou);
            $item_amount = $item_amount - $myCouponDikou;
        }
        
        //print_r($item_list);


		$this->assign('city_info',$city_info);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display('order');
    }

    /**
     * 订单详情
     * @author  chaozhou
     * @date  2014/8/22
     * @version  1.0.1 
     */
    public function order2(){
        session_start();
        
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        //

        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
		$oil_num = ceil($car_style['oil_mass']);
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
			$car_style['norms'] = $oil_num."L";//多少升机油
		}

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
    
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
		unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        if($_SESSION['patype'] == 2){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
			//拼机油数据
			$_SESSION['item_0'] = 49;
			$_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
			$_SESSION['item_0'] = 47;
			$_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
			$_SESSION['item_0'] = 50;
			$_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
		if($_SESSION['patype']>1 and $_SESSION['patype']!=5){
			//获取价格最低的机滤
			$res = $this->get_item($_SESSION['model_id']);
			foreach($res['1'] as $a=>$b){
				$new_res[$b['id']] = $b['price']; 
			}
			$_SESSION['item_1'] = array_search(min($new_res),$new_res);
		}else{
			$_SESSION['item_0']= -1;
			$_SESSION['oil_detail']= array();
			$_SESSION['item_1']= -1;
		}
		$_SESSION['item_2'] = -1;
		$_SESSION['item_3'] = -1;
		//print_r($_SESSION);
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if($_SESSION['patype']==5){
            $item_amount = 58;
        }elseif ($_SESSION['patype']==2) {
            $item_amount = 199;
        }elseif ($_SESSION['patype']==3) {
            $item_amount = 299;
        }elseif ($_SESSION['patype']==4) {
            $item_amount = 399;
        }
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display('order2');
    }

public function order_as(){
        session_start();
        
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        //

        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
    
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        if($_SESSION['patype'] == 2){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
        if($_SESSION['patype']>1){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }
            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
        }
        $_SESSION['item_2'] = -1;
        $_SESSION['item_3'] = -1;
        //print_r($_SESSION);
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if($_SESSION['patype']==1){
            $item_amount = 50;
        }elseif ($_SESSION['patype']==2) {
            $item_amount = 168;
        }elseif ($_SESSION['patype']==3) {
            $item_amount = 268;
        }elseif ($_SESSION['patype']==4) {
            $item_amount = 368;
        }
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display();
    }

    public function order_pabank(){
        session_start();
       
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        
        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
    
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
		if($_SESSION['patype'] == 5 and $_SESSION['brand_id']==1 or $_SESSION['brand_id']==3 or $_SESSION['brand_id']==4){
			$tips = "奥迪宝马奔驰车型请选择98元套餐";
			$this->assign('tips', $tips);   
		}
        if($_SESSION['patype'] == 2){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
        if($_SESSION['patype']>1){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }
            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
        }
        $_SESSION['item_2'] = -1;
        $_SESSION['item_3'] = -1;
        //print_r($_SESSION);
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if($_SESSION['patype']==1){
            $item_amount = 50;
        }elseif ($_SESSION['patype']==2) {
            $item_amount = 168;
        }elseif ($_SESSION['patype']==3) {
            $item_amount = 268;
        }elseif ($_SESSION['patype']==4) {
            $item_amount = 368;
        }elseif($_SESSION['patype']==5 or $_SESSION['patype']==6){
			if($_SESSION['patype']==5){ // dingjb 2015-09-21 11:25:18 好空气16。8改为38
				$item_amount = 38;
			}
			if($_SESSION['patype']==6){
				$item_amount = 98;
			}
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
            $_SESSION['item_2'] = -1;
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['3'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }
            $_SESSION['item_3'] = array_search(min($new_res),$new_res);
        }

		//城市数据
		$city_model = D('city');
		$city_list = $city_model->select();
		$this->assign('city_list',$city_list);
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display();
    }

    //光大银行
    public function order_cebbank(){
        session_start();
       
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));


        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
    
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        if($_SESSION['patype'] == 2 OR $_SESSION['patype'] == 168 OR $_SESSION['patype']==12 OR $_SESSION['patype']==5){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3 OR $_SESSION['patype'] == 268 OR $_SESSION['patype']==13 OR $_SESSION['patype']==6){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4 OR $_SESSION['patype'] == 368 OR $_SESSION['patype']==14 OR $_SESSION['patype']==7){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
        if($_SESSION['patype']>1){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }
            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
        }
        $_SESSION['item_2'] = -1;
        $_SESSION['item_3'] = -1;
        //print_r($_SESSION);
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if($_SESSION['patype']==1  OR $_SESSION['patype']==11){
            $item_amount = 50;
        }elseif ($_SESSION['patype']==2 OR $_SESSION['patype']==168 OR $_SESSION['patype']==12) {
            $item_amount = 168;
        }elseif ($_SESSION['patype']==3 OR $_SESSION['patype']==268 OR $_SESSION['patype']==13) {
            $item_amount = 268;
        }elseif ($_SESSION['patype']==4 OR $_SESSION['patype']==368 OR $_SESSION['patype']==14) {
            $item_amount = 368;
        } elseif ($_SESSION['patype']==5) {
			$item_amount = 199;
		}elseif ($_SESSION['patype']==6) {
			$item_amount = 299;
		}elseif ($_SESSION['patype']==7) {
			$item_amount = 399;
		}

        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display();
    }
    
  
   
    //好空气
    public function order_hkq(){
        session_start();
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        

        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        //

        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
    
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        if($_SESSION['patype'] == 2){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
        if($_SESSION['patype']>1){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }
            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
        }
        $_SESSION['item_2'] = -1;
        $_SESSION['item_3'] = -1;
        //print_r($_SESSION);
        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if($_SESSION['patype']==1 OR $_SESSION['patype']==10){ // dingjb 2015-09-21 11:10:50 修改
            $item_amount = 38;
        }
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
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
        
        $_SESSION['pa_mobile'] = $mobile = $_REQUEST['mobile'];
        $_SESSION['pa_code'] = rand(1000,9999);
        $sms = array(
                'phones'=>$mobile,
                'content'=>"您上门预约订单的验证码为：".$_SESSION['pa_code'], 
        );
        $this->curl_sms($sms,'',''); 
        $sms['sendtime'] = time();
        $this->model_sms->data($sms)->add();

        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $this->ajaxReturn($return);
       
    }

    /**
     * 发送平安订单查询验证码
     * @author  chaozhou
     * @date  2014/8/24
     * @version  1.0.0 
     */
    function giveeverify2(){
        session_start();
        
        $_SESSION['pa_mobile'] = $mobile = $_REQUEST['mobile'];
        $_SESSION['pa_code'] = rand(1000,9999);
        $sms = array(
                'phones'=>$mobile,
                'content'=>"验证码：".$_SESSION['pa_code'], 
        );
        $this->curl_sms($sms,'',''); 
        $sms['sendtime'] = time();
        $this->model_sms->data($sms)->add();

        $return['errno'] = '0';
        $return['errmsg'] = 'success';
        $this->ajaxReturn($return);
       
    }

    /**
    *** 平安保险说明
    **/
    public function carservice_pa_use(){
        $this->display();
    }

    /**
     * 提交订单
     * @author  chaozhou
     * @date  2014/8/23
     * @version  1.0.1 
     */
    public function create_order(){
        session_start();

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

            // dingjb 2015-09-29 11:20:52 切换到云通讯
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
        } elseif ($_SESSION['from_my_coupon']) { //来自我的优惠券
        	$has_replace_code = true;
        	$order_info['replace_code']= $_SESSION['new_coupon_code'];
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
		$order_info['city_id'] = $this->_post('city_id');
		$order_info['area_id'] = $this->_post('area_id');
        
    	$oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

		if($_SESSION['item_0']>0){ 
			foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
				$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
				$total_norms+=$res['norms']*$_num;
			}
		}
		$chaidan = array('r','s','t','w','x','y');
		$first = substr($order_info['replace_code'],0,1);
		//订单不是淘宝支付的套餐且不是补单，并且订单机油大于4升或者有多余的空气滤，空调滤就进行拆弹
		if(in_array($first,$chaidan) and ($total_norms>4 or $_SESSION['item_2']>0 or $_SESSION['item_3']>0)){
			//当机油大于4升，拆机油
			if($total_norms>4){
				foreach ( $_SESSION['oil_detail'] as $id=>$num){
					$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
					$norms+=$res['norms']*$num;
					if($res['norms']==1){
						$one_id = $id;
						unset($_SESSION['oil_detail'][$id]);
					}else{
						$four_id = $id;
						$_SESSION['oil_detail'][$id] = 1;
					}
				}

				$left_norms = $norms-4;
				$aa = $left_norms%4;
				$bb = ($left_norms - $aa)/4;
				$oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
				//print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

				/*计算拆分订单的配件数据---START---*/
				if($oil_detail2){
					//通过机油id查出订单数据
					$item_oil_price = 0;
					$i = 0;
					foreach ( $oil_detail2 as $_a=>$_b){
						if($_b > 0){
							$res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
							if ($i == 0) {
								$oil_1_id = $_a;
								$oil_1_price = $res['price']*$_b;
								$i++;
							}else{
								$oil_2_id = $_a;
								$oil_2_price = $res['price']*$_b;
							}
							$item_oil_price += $res['price']*$_b;
							$name = $res['name'];
						}
					}
					$item_list['0']['id'] = $_SESSION['item_0'];
					$item_list['0']['name'] = $name;
					$item_list['0']['price'] = $item_oil_price;
				}
			}
			
			if($_SESSION['item_2']){
				$kongqi_id2 = $item_condition['id'] = $_SESSION['item_2'];
				$item_list['2'] = $this->new_filter->where($item_condition)->find();
				$kongqi_price2 = $item_list['2']['price'];
				$_SESSION['item_2'] = 0;
			}
			if($_SESSION['item_3']){
				$kongtiao_id2 = $item_condition['id'] = $_SESSION['item_3'];
				$item_list['3'] = $this->new_filter->where($item_condition)->find();
				$kongtiao_price2 = $item_list['3']['price'];
				$_SESSION['item_3'] = 0;
			}
			$item_amount2 = 0;
			if(is_array($item_list)){
				foreach ($item_list as $key => $value) {
					$item_amount2 += $value['price'];
				}
			}
			unset($item_list);
			$item_content2 = array(
					'oil_id'     =>	$_SESSION['item_0'],
					'oil_detail' => $oil_detail2,
					'filter_id'  => -1,
					'kongqi_id'  =>  $kongqi_id2,
					'kongtiao_id' =>  $kongtiao_id2,
					'price'=>array(
							'oil'=>array(
									$oil_1_id=>$oil_1_price,
									$oil_2_id=>$oil_2_price
							),
							'filter'=>array(
									$filter_id=>$filter_price
							),
							'kongqi'=>array(
									$kongqi_id2=>$kongqi_price2
							),
							'kongtiao'=> array(
									$kongtiao_id2=>$kongtiao_price2
							)
					)
			);
			 
			$item2 = serialize($item_content2);
			/*计算拆分订单的配件数据---END---*/
		}


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
	    				$res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
	    		$item_list['1'] = $this->new_filter->where($item_condition)->find();
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
	    		$item_list['2'] = $this->new_filter->where($item_condition)->find();
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
	    		$item_list['3'] = $this->new_filter->where($item_condition)->find();
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];

        //支付宝服务窗下单
        if($_SESSION['ali_id']){
            $ali_id = $_SESSION['ali_id'];

            $mAlipay = D('alipay_fuwuchuang');
            $resAli = $mAlipay->where(array('FromUserId' => $ali_id,'status'=> 2 ))->find();

            if($resAli){
                $order_info['ali_id'] = $resAli['id'];
            }
            $order_info['origin'] = 5;
        }else{
            $order_info['origin'] = 2;
        }


    	$value = 0;
        if($has_replace_code){
            if ($this->newCouponCodeModel->isValidNewCouponCode($order_info['replace_code'], $_SESSION['ordertype'])) {//新版优惠券下单操作
                $hasNewCoupon = true;
                $value = $this->newCouponCodeModel->getDiscountAmount($order_info['replace_code']);
                $order_info['dikou_amount'] = $value;
                $order_info['amount'] = $item_amount + 99 - $value;
                $order_info['order_type'] = 1;//上门保养订单
                $order_info['remark'] = $order_info['remark'].'新版优惠价格:'.$value.'元';
            } else {
                //根据优惠券渠道分类
                $info = $this->carservicecode_model->where(array('coupon_code'=>$order_info['replace_code']))->find();
                if($info['source_type']==1){
                    if($first==a){
                        $order_info['order_type'] = 64;
                        $order_info['amount'] = $item_amount+99;
                    }  
                    if($first==b){
                        $order_info['order_type'] = 63;
                        $order_info['amount'] = $item_amount+99;
                    }                
                }else{
                    //套餐不进折扣券计算
                    $first = substr($order_info['replace_code'],0,1);
                    if($first==r or $first==y){
                        $order_info['order_type'] = 7;
                        $order_info['amount'] = $item_amount+99;
                    }elseif($first==s or $first==x){
                        $order_info['order_type'] = 8;
                        $order_info['amount'] = $item_amount+99;
                    }elseif($first==t or $first==w){
                        $order_info['order_type'] = 10;
                        $order_info['amount'] = $item_amount+99;
                    }elseif($first==u){
                        $order_info['order_type'] = 59;
                        $order_info['amount'] = $item_amount+99;
                    }elseif($first==v){
                        if($item_amount==0){//如果没有配件，免人工费
                            $order_info['amount'] = $item_amount;
                            $order_info['dikou_amount'] = 99;
                        }elseif($item_amount>168){//如果是套餐就减20
                            $order_info['amount'] = $item_amount+99-20;
                            $order_info['dikou_amount'] = 20;
                        }
                        $order_info['remark'] = $order_info['remark'].'抵扣套餐价格￥'.$order_info['dikou_amount'].';安盛天平财险';
                    }else{
                        $value = $this->get_codevalue($order_info['replace_code']);
                        $order_info['dikou_amount'] = $value;
                        if ($value != 99){
                            $order_info['amount'] = $item_amount +99 -$value;
                        }else{
                            $order_info['amount'] = $item_amount;
                        }
                    }
                }
            }
        }else{
        	$order_info['amount'] = $item_amount + 99;
        }
		//计算油桶套餐价
		if($order_info['order_type']>4 and $order_info['order_type']!=47){
			$info = $this->get_typeprice($order_info['order_type'],$item_oil_price,$filter_price,$kongtiao_price);
			//echo $info;exit;
			$order_info['amount'] = $order_info['amount']-$info['typeprice'];
			$order_info['dikou_amount'] = $info['typeprice']+$value;
			$order_info['remark'] = $order_info['remark'].'抵扣套餐价格￥'.$info['typeprice'].';'.$info['remark'];
		}
        
		//计算业务渠道
		$first = substr($order_info['replace_code'],0,1);
		if($first==r or $first==s or $first==t or $first==u){
			//$order_info['business_source'] = '41';
            $order_info['business_source'] = '19';
			$order_info['remark'] = $order_info['remark'].'发动机舱深度喷雾精洗,7项细节养护,38项全车检测';
		}elseif($first==w or $first==x or $first==y){
			//$order_info['business_source'] = '42';
            $order_info['business_source'] = '18';
		}elseif($first==v){
			//$order_info['business_source'] = '43';
            $order_info['business_source'] = '21';
		}
        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
		if($order_id){
            if ($hasNewCoupon) {//新优惠券使用处理
                $this->newCouponCodeModel->useNewCoupon($order_info['replace_code']);
            } else {
                $update = array('status'=>1);
                $where = array('coupon_code'=>$order_info['replace_code']);
                $res = $this->carservicecode_model->where($where)->save($update);
            }
			//追加拆单
			if($item_content2){
				$order_info2 = $order_info;
				$order_info2['item'] = $item2;
				$order_info2['amount'] = $item_amount2;
				$order_info2['dikou_amount'] = 99;
				$order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
				$order_info2['order_type'] = 34;
				$order_info2['admin_id'] = 1;
				$res = $this->reservation_order_model->data($order_info2)->add();
				//echo $this->reservation_order_model->getLastsql();exit;
			}

            if($order_info['ali_id']){
                $Aliurl = WEB_ROOT.'/alipay_fuwuchuang/ali_test.php';
                $Alipost = array(
                            "title" => "下单成功",
                        "desc" => "您的订单号为：".$order_id."",
                        "url" => WEB_ROOT."/mobilecar-mycarservice_detail?order_id=".$order_id,
                        "imageUrl" => WEB_ROOT."/Public_new/images/index/logo.png",
                        "authType" => "loginAuth",
                        "toUserId" => $_SESSION['ali_id']
                );
                $data = $this->ali_curl($Aliurl,$Alipost);
            }
		}
        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
			//发短信通知用户
			/*
			$send_add_order_data = array(
					'phones'=>$order_info['mobile'],
					'content'=>"您预约".date('m',strtotime($this->_post('order_time'))).'月'.date('d',strtotime($this->_post('order_time'))).'日'.($order_time2 + 8)."时的“府上养车”上门保养服务,我们客服将于2小时内联系您确认订单(工作时间9-18点)。4006602822",
			);
			$this->curl_sms($send_add_order_data,'',1);  //Todo 内外暂不发短信
            */
            // dingjb 2015-09-29 13:18:00 切换到云通讯
            $send_add_order_data = array(
                'phones'  => $order_info['mobile'],
                'content' => array(
                    date('m',strtotime($this->_post('order_time'))),
                    date('d',strtotime($this->_post('order_time'))),
                    $order_time2 + 8
                )
            );
            $this->curl_sms($send_add_order_data, null, 4, '37751');

			$send_add_order_data['sendtime'] = time();
			$this->sms_model->data($send_add_order_data)->add();

            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
		// $order_info['pay_type']
		if($order_info['pay_type'] ==3){
			$order_id = "m".$order_id;
			//$item_amount = 0.01;
			$arr='ORDERID='.$order_id.'&PAYMENT='.$order_info['amount'];
			$this->addCodeLog('订单保养',$arr);
			$url = WEB_ROOT.'/ccb_pay/merchant.php?'.$arr;
			header("Location:$url");
		}elseif($order_info['pay_type'] ==4){
			$url = WEB_ROOT.'/alipay_wap/alipayapi.php?order_id='.$order_id;
			header("Location:$url");
		}elseif($order_info['pay_type'] == 5){//浦发银行支付
			$url = WEB_ROOT.'/pufa_pay/pay.php?order_id='.$order_id;
			header("Location:$url");
		} else{
			header("Location:".WEB_ROOT."/mobilecar/order_success-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
		}

    }

    //curl接受页面信息
    function ali_curl($durl,$post){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_TIMEOUT,40);
        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_POST, 1 );
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $post );
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    /**
     * 提交订单
     * @author  chaozhou
     * @date  2014/8/23
     * @version  1.0.1 
     */
    public function create_order2(){
        session_start();

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
            // dingjb 2015-09-29 11:22:57 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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

        //$order_info['order_time'] = $this->_post('order_time');
        //$order_time2 = intval($this->_post('order_time2'));
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
		$order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

		//根据机油拆弹计算
		foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
			$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
			$total_norms+=$res['norms']*$_num;
		}
		if($total_norms>4){
			foreach ( $_SESSION['oil_detail'] as $id=>$num){
				$res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
				$norms+=$res['norms']*$num;
				if($res['norms']==1){
					$one_id = $id;
					unset($_SESSION['oil_detail'][$id]);
				}else{
					$four_id = $id;
					$_SESSION['oil_detail'][$id] = 1;
				}
			}

			$left_norms = $norms-4;
			$aa = $left_norms%4;
			$bb = ($left_norms - $aa)/4;
			$oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
			//print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

			/*计算拆分订单的配件数据---START---*/
			if($oil_detail2){
				//通过机油id查出订单数据
				$item_oil_price = 0;
				$i = 0;
				foreach ( $oil_detail2 as $_a=>$_b){
					if($_b > 0){
						$res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
						if ($i == 0) {
							$oil_1_id = $_a;
							$oil_1_price = $res['price']*$_b;
							$i++;
						}else{
							$oil_2_id = $_a;
							$oil_2_price = $res['price']*$_b;
						}
						$item_oil_price += $res['price']*$_b;
						$name = $res['name'];
					}
				}
				$item_list['0']['id'] = $_SESSION['item_0'];
				$item_list['0']['name'] = $name;
				$item_list['0']['price'] = $item_oil_price;
			}
		
			$item_amount2 = 0;
			if(is_array($item_list)){
				foreach ($item_list as $key => $value) {
					$item_amount2 += $value['price'];
				}
			}
			 
			$item_content2 = array(
					'oil_id'     =>	$_SESSION['item_0'],
					'oil_detail' => $oil_detail2,
					'filter_id'  => -1,
					'kongqi_id'  => -1,
					'kongtiao_id' =>-1,
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
			 
			$item2 = serialize($item_content2);
			/*计算拆分订单的配件数据---END---*/
		}
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 平安保险合作
        $order_info['order_type'] = 20;
        $order_info['business_source'] = 19;
        if($_SESSION['patype']==5){
            $order_info['amount'] = 58;
            $order_info['remark'] = "平安保险发动机舱精洗套餐";
            $order_info['dikou_amount'] = 99-$order_info['amount'];
        }elseif ($_SESSION['patype']==2) {
            $order_info['amount'] = 199;
            $order_info['remark'] = "平安保险199套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-199;
        }elseif ($_SESSION['patype']==3) {
            $order_info['amount'] = 299;
            $order_info['remark'] = "平安保险299套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-299;
        }elseif ($_SESSION['patype']==4) {
            $order_info['amount'] = 399;
            $order_info['remark'] = "平安保险399套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-399;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
			//给用户发短信通知
			$send_add_user_data = array(
			'phones'=>$order_info['mobile'],
			'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
			);
			$this->curl_sms($send_add_user_data);
			$model_sms = D('sms');
			$send_add_user_data['sendtime'] = time();
			$model_sms->data($send_add_user_data)->add();
        }

		if($oil_detail2){
			$order_info2 = $order_info;
			$order_info2['item'] = $item2;
			$order_info2['amount'] = $item_amount2;
			$order_info2['dikou_amount'] = 99;
			$order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
			$order_info2['order_type'] = 34;
			$order_info2['admin_id'] = 1;
			$res = $this->reservation_order_model->data($order_info2)->add();
			//echo $this->reservation_order_model->getLastsql();exit;
		}

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_pa-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }

    /**
     * 提交订单
     * @author  chaozhou
     * @date  2014/8/23
     * @version  1.0.1 
     */
    public function create_order_as(){
        session_start();

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

            // dingjb 2015-09-29 11:26:55 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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

        //$order_info['order_time'] = $this->_post('order_time');
        //$order_time2 = intval($this->_post('order_time2'));
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆弹计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 平安保险合作
        
        $order_info['business_source'] = 24;
        if($_SESSION['patype']==1){
            $order_info['amount'] = 50;
            $order_info['remark'] = "安盛天平50元检测套餐(不含机油配件)";
            $order_info['dikou_amount'] = 49;
            $order_info['order_type'] = 39;
        }elseif ($_SESSION['patype']==2) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "安盛天平168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 40;
        }elseif ($_SESSION['patype']==3) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "安盛天平268套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 41;
        }elseif ($_SESSION['patype']==4) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "安盛天平368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 42;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_as-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }


    public function create_order_pabank(){
        session_start();

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
            // dingjb 2015-09-29 11:29:18 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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

        //$order_info['order_time'] = $this->_post('order_time');
        //$order_time2 = intval($this->_post('order_time2'));
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆弹计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 平安保险合作
        
        $order_info['business_source'] = 24;
        if($_SESSION['patype']==1){
            $order_info['amount'] = 50;
            $order_info['remark'] = "平安银行50元检测套餐(不含机油配件)";
            $order_info['dikou_amount'] = 49;
            $order_info['order_type'] = 43;
        }elseif ($_SESSION['patype']==2) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "平安银行168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 44;
        }elseif ($_SESSION['patype']==3) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "平安银行268套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 45;
        }elseif ($_SESSION['patype']==4) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "平安银行368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 46;
        }elseif ($_SESSION['patype']==5) { // dingjb 2015-09-21 11:24:02 修改套餐为 38
            $order_info['amount'] = 38;
            $order_info['remark'] = "平安银行38套餐";
            $order_info['dikou_amount'] = 99 - $order_info['amount'];
            $order_info['origin'] = 33;
        }elseif ($_SESSION['patype']==6) {
            $order_info['amount'] = 98;
            $order_info['remark'] = "平安银行98套餐";
            $order_info['dikou_amount'] = 1;
            $order_info['origin'] = 33;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            //提交成功缓存手机
            $_SESSION['pamobile'] = $order_info['mobile'];
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_pabank-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }

    //光大银行
    public function create_order_cebbank(){
        session_start();

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
            // dingjb 2015-09-29 11:31:36 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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

        //$order_info['order_time'] = $this->_post('order_time');
        //$order_time2 = intval($this->_post('order_time2'));
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆弹计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 平安保险合作
        
        
        if($_SESSION['patype']==1){
            $order_info['amount'] = 50;
            $order_info['remark'] = "中国光大银行50元检测套餐(不含机油配件)";
            $order_info['dikou_amount'] = 49;
            $order_info['order_type'] = 21;
            $order_info['business_source'] = 24;
        }elseif ($_SESSION['patype']==2) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "中国光大银行168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 22;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==3) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "中国光大银行268套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 23;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==4) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "中国光大银行368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 24;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==5) {
            $order_info['amount'] = 199;
            $order_info['remark'] = "中国光大银行199套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-199;
            $order_info['order_type'] = 24;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==6) {
            $order_info['amount'] = 299;
            $order_info['remark'] = "中国光大银行299套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-299;
            $order_info['order_type'] = 24;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==7) {
            $order_info['amount'] = 399;
            $order_info['remark'] = "中国光大银行399套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-399;
            $order_info['order_type'] = 24;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 29;
			}elseif($_SESSION['sourcetype']==2){
				$order_info['business_source'] = 31;
			}else{
				$order_info['business_source'] = 24;
			}
        }elseif ($_SESSION['patype']==168) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "携车网府上养车168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 22;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 17;
			}
        }elseif ($_SESSION['patype']==268) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "携车网府上养车268套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 23;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 17;
			}
        }elseif ($_SESSION['patype']==368) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "携车网府上养车368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 24;
			if($_SESSION['sourcetype']==1){
				$order_info['business_source'] = 17;
			}
        }elseif ($_SESSION['patype']==11) {
            $order_info['amount'] = 50;
            $order_info['remark'] = "人保财险50元检测套餐(不含机油配件)";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-50;
            $order_info['order_type'] = 21;
            $order_info['business_source'] = 28;
        }elseif ($_SESSION['patype']==12) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "人保财险168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 22;
            $order_info['business_source'] = 28;
        }elseif ($_SESSION['patype']==13) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "人保财险368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 23;
            $order_info['business_source'] = 28;
        }elseif ($_SESSION['patype']==14) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "人保财险368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 24;
            $order_info['business_source'] = 28;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            //提交成功缓存手机
            $_SESSION['pamobile'] = $order_info['mobile'];
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_cebbank-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }
    
    //光大银行
    public function create_order_hkq(){
        session_start();

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
            // dingjb 2015-09-29 11:33:49 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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

        //$order_info['order_time'] = $this->_post('order_time');
        //$order_time2 = intval($this->_post('order_time2'));
        //$order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆弹计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 平安保险合作
        
        
        if($_SESSION['patype']==1){ // dingjb 2015-09-21 11:15:51 修改好空气16.8套餐价格为38
            $order_info['amount'] = 38;
            $order_info['remark'] = "好空气套餐(不含机油配件)";
            $order_info['dikou_amount'] = 99 - $order_info['amount'];
            $order_info['order_type'] = 14;
            $order_info['business_source'] = 24;
        }else if($_SESSION['patype']==10){  // dingjb 2015-09-21 11:15:51 修改好空气16.8套餐价格为38
            $order_info['amount'] = 38;
            $order_info['remark'] = "好空气套餐(不含机油配件)";
            $order_info['dikou_amount'] = 99 - $order_info['amount'];
            $order_info['order_type'] = 14;
            $order_info['business_source'] = 28;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            //提交成功缓存手机
            $_SESSION['pamobile'] = $order_info['mobile'];
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_hkq-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }
    
    
    
    

    /**
     * 平安保险APP下单成功
     * @author  
     * @date  2015/2/2
     * @version  1.0.1 
     */
    public function order_success_pa(){
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
            $this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
    }

    public function order_success_as(){
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
            $this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
    }

    public function order_success_pabank(){
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
            $this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
    }

    //光大银行
    public function order_success_cebbank(){
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
            $this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
    }

    //好空气
    public function order_success_hkq(){
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
        $pay_type = intval($this->_get('pay_type'));
        $this->assign('order_id', $order_id);
        $this->assign('pay_type', $pay_type);
        if( isset($_SESSION['pa_id']) ){
            $this->assign('pa_id', $_SESSION['pa_id']);
        }
        $this->display();
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
    
    /**
     * 订单列表
     * @author  chaozhou
     * @date  2014/9/2
     * @version  1.0.0
     */
     public function mycarservice_pa(){
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
                        $map['mobile'] = $paweixin['mobile'];
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
			if($_SESSION['uid']){
				$uid = $_SESSION['uid'];
			}else{
				$uid = 22305;
			}
			$map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
        //$map['order_type'] = array('in',array('20','34'));

        if($_SESSION['pamobile']){
            $map['mobile'] = $_SESSION['pamobile'];
            $this->assign('isshow', 2);
        }else{
            $this->assign('isshow', 3);
        }
		//去掉僵尸单
		$map['origin'] = array('neq','4');
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->reservation_order_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);
                
                 //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
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
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
                        $item_list['1']['id'] = 0;
                        $item_list['1']['name'] = "自备配件";
                        $item_list['1']['price'] = 0;
                    }else{
                        $item_condition['id'] = $order_items['filter_id'];
                        $item_list['1'] = $this->item_model->where($item_condition)->find();
						
                        //去掉品牌后面的规格 by bright
                        $item_list['1']['name'] = $this->getBrandName($item_list['1']['name']);
                        //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
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
						
                        //去掉品牌后面的规格 by bright
                        $item_list['2']['name'] = $this->getBrandName($item_list['2']['name']);
                        
                        //$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
						
                        //去掉品牌后面的规格 by bright
                        $item_list['3']['name'] = $this->getBrandName($item_list['3']['name']);
                       // $item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                    }
                }
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                    $list[$key]['replace_value'] = $val;
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
        $this->assign('pa_id', $pa_id);
        $this->assign('page',$page);
		 //var_dump($list);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }

    public function mycarservice_pabank(){
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
                        $map['mobile'] = $paweixin['mobile'];
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
            if($_SESSION['uid']){
                $uid = $_SESSION['uid'];
            }else{
                $uid = 22305;
            }
            $map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
        //$map['order_type'] = array('in',array('34','43','44','45','46'));

        if($_SESSION['pamobile']){
            $map['mobile'] = $_SESSION['pamobile'];
            $this->assign('isshow', 2);
        }else{
            $this->assign('isshow', 3);
        }
       
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        //echo $this->reservation_order_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);
                
                //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
                
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
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
                        $item_list['1']['id'] = 0;
                        $item_list['1']['name'] = "自备配件";
                        $item_list['1']['price'] = 0;
                    }else{
                        $item_condition['id'] = $order_items['filter_id'];
                        $item_list['1'] = $this->item_model->where($item_condition)->find();
						
                         //去掉品牌后面的规格 by bright
                        $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
                        //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
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
						
                        //去掉品牌后面的规格 by bright
                        $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
                        //$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
						
                        //去掉品牌后面的规格 by bright
                        $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
                       // $item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                    }
                }
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                    $list[$key]['replace_value'] = $val;
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
        $this->assign('pa_id', $pa_id);
        $this->assign('page',$page);
         //var_dump($list);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }

    //光大银行
    public function mycarservice_cebbank(){
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
                        $map['mobile'] = $paweixin['mobile'];
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
            if($_SESSION['uid']){
                $uid = $_SESSION['uid'];
            }else{
                $uid = 22305;
            }
            $map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
        //$map['order_type'] = array('in',array('34','43','44','45','46'));

        if($_SESSION['pamobile']){
            $map['mobile'] = $_SESSION['pamobile'];
            $this->assign('isshow', 2);
        }else{
            $this->assign('isshow', 3);
        }
       
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        //echo $this->reservation_order_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);
                
                //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
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
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
                        $item_list['1']['id'] = 0;
                        $item_list['1']['name'] = "自备配件";
                        $item_list['1']['price'] = 0;
                    }else{
                        $item_condition['id'] = $order_items['filter_id'];
                        $item_list['1'] = $this->item_model->where($item_condition)->find();
                       
                        //去掉品牌后面的规格 by bright
                        $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
                        //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
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
                        
                        //去掉品牌后面的规格 by bright
                        $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
                        //$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
                       
                        //去掉品牌后面的规格 by bright
                        $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
                       // $item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                    }
                }
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                    $list[$key]['replace_value'] = $val;
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
        $this->assign('pa_id', $pa_id);
        $this->assign('page',$page);
         //var_dump($list);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }

    //好空气
    public function mycarservice_hkq(){
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
                        $map['mobile'] = $paweixin['mobile'];
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
            if($_SESSION['uid']){
                $uid = $_SESSION['uid'];
            }else{
                $uid = 22305;
            }
            $map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
        //$map['order_type'] = array('in',array('34','43','44','45','46'));

        if($_SESSION['pamobile']){
            $map['mobile'] = $_SESSION['pamobile'];
            $this->assign('isshow', 2);
        }else{
            $this->assign('isshow', 3);
        }
       
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        //echo $this->reservation_order_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);

                //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
                
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
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
                        $item_list['1']['id'] = 0;
                        $item_list['1']['name'] = "自备配件";
                        $item_list['1']['price'] = 0;
                    }else{
                        $item_condition['id'] = $order_items['filter_id'];
                        $item_list['1'] = $this->item_model->where($item_condition)->find();
                        
                        //去掉品牌后面的规格 by bright
                        $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
                        //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
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
                        
                        //去掉品牌后面的规格 by bright
                        $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
                        //$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
                        
                        //去掉品牌后面的规格 by bright
                        $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
                       // $item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                    }
                }
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                    $list[$key]['replace_value'] = $val;
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
        $this->assign('pa_id', $pa_id);
        $this->assign('page',$page);
         //var_dump($list);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }

    public function mycarservice_as(){
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
                        $map['mobile'] = $paweixin['mobile'];
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
            if($_SESSION['uid']){
                $uid = $_SESSION['uid'];
            }else{
                $uid = 22305;
            }
            $map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
        //$map['order_type'] = array('in',array('34','39','40','41','42'));

        if($_SESSION['pamobile']){
            $map['mobile'] = $_SESSION['pamobile'];
            $this->assign('isshow', 2);
        }else{
            $this->assign('isshow', 3);
        }
       
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        //echo $this->reservation_order_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);

                //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
                
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
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
                        $item_list['1']['id'] = 0;
                        $item_list['1']['name'] = "自备配件";
                        $item_list['1']['price'] = 0;
                    }else{
                        $item_condition['id'] = $order_items['filter_id'];
                        $item_list['1'] = $this->item_model->where($item_condition)->find();
						
                        //去掉品牌后面的规格 by bright
                        $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
                        //$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
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
						
                        
                        //去掉品牌后面的规格 by bright
                        $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
                       // $item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
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
						
                        //去掉品牌后面的规格 by bright
                        $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
                       // $item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                    }
                }
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                    $list[$key]['replace_value'] = $val;
                }

                $list[$key]['item_list'] = $item_list;
            }
        }
        
        $condition = array();
        $condition['status'] = 1;
        $technician_list = $this->technician_model->where($condition)->select();
       // var_dump($list);
        $this->assign('data', $map);
        $this->assign('list', $list);
        $this->assign('pa_id', $pa_id);
        $this->assign('page',$page);
        $this->assign('technician_list', $technician_list);

        $this->display();
    }
 	//ajax验证抵用码
    function valid_coupon_code(){
    	$replace_code = @$_POST['coupon_code'];
    	$mobile = @$_POST['mobile'];
        $type = @$_POST['type'];
    	if(!$replace_code){
    		$ret = array('message'=>'抵用码为空');
    		$this->ajaxReturn($ret,'',0);
    	}
        if ($this->newCouponCodeModel->isValidNewCouponCode($replace_code, $_SESSION['ordertype'])) {//新版优惠券下单时判断有效性
            $_SESSION['replace_code']= $replace_code;
            $ret = array(
                'price' => $this->newCouponCodeModel->getDiscountAmount($replace_code),
            );
            $this->ajaxReturn($ret, '', 1);
        } elseif($replace_code=='016888'){
			$ret = array('price'=>20);
			//用于减去总价，总价减去抵用码的价钱
			$_SESSION['replace_code']= $replace_code;
			$this->ajaxReturn($ret,'',1);
		}else{
            $first = substr($replace_code,0,1);
			$chk_code = $this->_check_replace_code($replace_code,$mobile,$type);
			if(!$chk_code){
				$ret = array('message'=>'该抵用码不能使用，请换一个');
				$this->ajaxReturn($ret,'',0);
			}elseif($_SESSION['ordertype']!=54 and $first=='b'){
                $ret = array('message'=>'该抵用码不能使用，请换一个');
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
					unset($_SESSION['replace_code']);
					$_SESSION['replace_code']= $replace_code;
					$this->ajaxReturn($ret,'',1);
				/*}else{
					$ret = array('message'=>'更新数据失败，请重试用');
					$this->ajaxReturn($ret,'',0);
				}*/
			}
		}
    }

    //ajax验证保险抵用码
    function valid_insurancecode(){
        $replace_code = @$_POST['coupon_code'];
        if(!$replace_code){
            $ret = array('message'=>'抵用码为空');
            $this->ajaxReturn($ret,'',0);
        }else{
            $first = substr($replace_code,0,1);
            $permit_array = array('a','b','r','s','t','u','v','w','x','y');
            if(!in_array($first,$permit_array)){
                $ret = array('message'=>'抱歉,请联系客服,核实您的券码有效性');
            }else{
                $map['status'] = 0;
                $map['coupon_code'] = $replace_code;
                $info = $this->carservicecode_model->where($map)->find();
                if($info){
                    unset($_SESSION['replace_code']);
                    $_SESSION['replace_code'] = htmlspecialchars(addslashes($replace_code));
                    if($first=='r' or $first=='s' or $first=='t' or $first=='u'){
                        $ret = array('message'=>'您成功使用了1张苏州平安套餐券码');
                    }elseif($first=='v'){
                        $ret = array('message'=>'您成功使用了1张安盛天平套餐券码');
                    }elseif($first=='w' or $first=='x' or $first=='y'){
                        $ret = array('message'=>'您成功使用了1张上海人保套餐券码');
                    }
                }else{
                    $ret = array('message'=>'抱歉,券码验证失败,请联系客服');
                }
            }
            $this->ajaxReturn($ret,'',1);
        }
    }

    //检查抵用码能否使用
    private  function _check_replace_code($replace_code,$mobile,$type){
        if($type=='base'){
            //检查是否为基础服务码
            $first = substr($replace_code,0,1);
            if($first=='c' or $first=='d'){
                $where = array(
                    'coupon_code' => $replace_code,
                    'status' => 0,
                    'source_type'=>1
                );
            }else{
                return false;
            }
        }else{
            //正常检查券码
            $where = array(
                'coupon_code' => $replace_code,
                'status' => 0
            );
        }
        if (strlen($replace_code) == 4) {
            $where['mobile'] = $mobile;
            $where['start_time'] = array('elt', time());
            $where['end_time'] = array('egt', time());
        }
        $count = $this->carservicecode_model->where($where)->count();
        if ($count > 0) {
            return true;
        } else {
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
        $order_id = $this->get_orderid(intval($this->_get('order_id')));
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
        $map = array();
        $pa_id = $_SESSION['pa_id'];
        $show_jishi = false;//是否显示技师
        //$_REQUEST['pa_id']=5020;
        if( !empty($_REQUEST['pa_id']) ){
            $pa_id = $_SESSION['pa_id'] = $_REQUEST['pa_id'];
            //第一次预订         
            if ( isset($_SESSION['uid']) ) {
                $map['uid'] = $_SESSION['uid'];
                $map['pa_id'] = $pa_id;
                unset($_SESSION['uid']);
            }else{
                $palog = $this->PadataModel->where(array('id'=>$pa_id))->order('id desc')->find();
                if($palog){
                    $paweixin = $this->PaweixinModel->where(array('wx_id'=>$palog['FromUserName']))->find();
                    if ($paweixin) {
						$model_user = D('member');
						$member = $model_user->where(array('mobile'=>$paweixin['mobile']))->find();
                        if($member){
							$map['uid'] = $member['uid'];
						}else{
							$map['mobile'] = $paweixin['mobile'];
						}
                        //轩轩直接看所有的数据
                        if ('13817880684' == $paweixin['mobile']) {
                            $show_jishi = true;
                            $map = array();
                        }
                    }else{
                        $this->error('请先绑定微信号和手机号',__APP__.'Mobile-login_verify-pa_id-'.$pa_id.'-ic-1');
                    }
                }else{
                    $this->error('数据错误');
                }
            }
        }else{
            //没有从微信过来的情况
            //$uid = $_SESSION['uid']; 
			$uid = isset($_SESSION['uid'])?$_SESSION['uid']:$_REQUEST['uid'];
            $map['uid'] = $uid;
        }
        $pay_status = $this->_get('pay_status');
        if( isset($pay_status) ){
            $map['pay_status'] = intval($pay_status);
        }
		//去掉僵尸单
		$map['origin'] = array('neq','4');
		//去掉作废
		$map['status'] = array('neq','8');
        // 计算总数
        $count = $this->reservation_order_model->where($map)->count();
        //print_r($map);
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
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
		//if($_REQUEST['pa_id']==5020){
		//echo $this->reservation_order_model->getLastsql();
		//}
        if(is_array($list)){
            foreach ($list as $key => $value) {
                $list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);

                $list[$key]['status_name'] = $this->getStatusName($value['status']);

                //判断需要实例化哪张数据表 wql@20150825
                $this->getNeedModel($value['create_time']);
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

                
                //品牌logo
                /**
                 * $this->car_brand_model = M('tp_xieche.carbrand','xc_');  //车品牌
                 * $this->car_style_model = M('tp_xieche.carseries','xc_');  //车系号
                 * $this->car_model_model = M('tp_xieche.carmodel','xc_');  //车型号              
                 */
                $Dseries_id = $this->car_model_model->where(array('model_id'=>$list[$key]['model_id'],'is_show'=>1))->find();
                if($Dseries_id){
                    $Dbrand_id = $this->car_style_model->where(array('series_id'=>$Dseries_id['series_id']))->find();
                    if($Dbrand_id){
                        $Dlogo = $this->car_brand_model->where(array('brand_id'=>$Dbrand_id['brand_id']))->find();
                        if($Dlogo){
                            $list[$key]['brand_logo'] = $Dlogo['brand_logo'];
                        }
                    }
                }

                //商品详情
                $order_items = unserialize($value['item']);                 
                if( !empty( $order_items['oil_detail'] ) ){
                    $item_oil_price = $item_norms = 0;
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
                    if($order_items['filter_id'] == '-1'){  //自备配件的情况
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
                if($show_jishi){    //是否显示技师
                    $this->assign('show_jishi',1);
                    if($value['technician_id']){
                        $condition['id'] = $value['technician_id'];
                        $technician_info = $this->technician_model->where($condition)->find();
                        $list[$key]['technician_name'] = $technician_info['truename'];
                    }
                }

                if ( $value['replace_code'] ) {
                    if ($this->newCouponCodeModel->isNewCouponCode($value['replace_code'])) {//订单列表抵扣显示
                        if ($this->newCouponCodeModel->isBaoyang($value['replace_code'])) {
                            $list[$key]['replace_value'] = $this->newCouponCodeModel->getDiscountAmount($value['replace_code']);
                        } else {
                            $list[$key]['replace_value'] = 0;
                        }
                    } else {
                        $val = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
                        $list[$key]['replace_value'] = $val;
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
        $this->assign('pa_id', $pa_id);
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
        //exit;
        $order_id = $this->get_orderid($this->_get('order_id'));
        $pa_id = $_SESSION['pa_id'];
        $padata = $this->PadataModel->where(array('id'=>$pa_id))->find();
		$paweixin = $this->PaweixinModel->where(array('wx_id'=>$padata['FromUserName']))->find();
        //echo $this->PadataModel->getLastSql();
		$member = $this->MemberModel->where(array('mobile'=>$paweixin['mobile']))->find();
        $condition['id'] = $this->_get('order_id');
        $order_info = $this->reservation_order_model->where($condition)->find();
		if($paweixin['uid']!=$order_info['uid']){
            //print_r($paweixin);print_r($order_info);exit;
			//不是从微信过来的
            //$this->error('您没有观看此订单详情的权限');
		}
        //是否可以查看视频order_info
        $this->assign('live',true);//都可以看了
		//拼视频ID
		$live_id = $order_id.'s'.md5($order_id.'fqcd123223');
        /*
        $mStep = M('tp_xieche.check_step','xc_');
        $resStep = $mStep->where( array('order_id'=>$order_id,'step_id'=>2) )->count();
        if ($resStep){
        	$this->assign('live',true);
        }*/
        
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
        /*if( !empty( $order_items['oil_detail'] ) ){
        	$item_oil_price = 0;
        	$oil_data = $order_items['oil_detail'];
        	
        	foreach ( $oil_data as $id=>$num){
        		if($num > 0){
        			$res = $this->item_oil_model->field('name,price,norms')->where( array('id'=>$id))->find();
        			if($res['norms'] == 1){
        				$item_list[0]['oil_1_id'] = $id;
        				$item_list[0]['oil_1_name'] = $res['name'];
        				$item_list[0]['oil_1_num'] = $num;
        				$item_list[0]['oil_1_price'] = $res['price']*$num;
        			}else{
        				$item_list[0]['oil_2_id'] = $id;
        				$item_list[0]['oil_2_name'] = $res['name'];
        				$item_list[0]['oil_2_num'] = $num;
        				$item_list[0]['oil_2_price'] = $res['price']*$num;
        			}
        			$item_oil_price += $res['price']*$num;
					$item_norms += $res['norms'];
        		}
        	}
        	
        	
        	$item_list['0']['price'] = $item_oil_price;
			$item_list['0']['norms'] = $item_norms;
			
			if( $order_items['oil_id'] == '-1' ){
				$item_list['0']['id'] = 0;
				$item_list['0']['name'] = "自备配件";
				$item_list['0']['price'] = 0;
				$item_list[0]['oil_1_name'] = '';
				$item_list[0]['oil_1_num'] = '';
				$item_list[0]['oil_1_price'] = '';
				$item_list[0]['oil_2_name'] = '';
				$item_list[0]['oil_2_num'] = '';
				$item_list[0]['oil_2_price'] = '';
			}
			$item_num++;
        }*/
        if($order_items['oil_id']>0){
        	$item_oil_price = 0;
        	$oil_data = $order_items['oil_detail'];
			$key = array_keys($oil_data);
			//$item_list['oil_1_id'] = $key['0'];
			//$item_list['oil_2_id'] = $key['1'];
        	foreach ( $oil_data as $_id=>$num){
        		if($num > 0){
					foreach($order_items['price']['oil'] as $kk=>$vv){
						if($kk==$_id){
							$item_oil_price += $vv;
                            $res = $this->item_oil_model->field('name,price,type,norms')->where( array('id'=>$_id))->find();
                            //$item_oil_price += $res['price']*$num;
                            $norms += $res['norms']*$num;
                            if($res['norms'] == 1){
                                $item_list[0]['oil_1_id'] = $id;
                                $item_list[0]['oil_1_name'] = $res['name'];
                                $item_list[0]['oil_1_num'] = $num;
                                $item_list[0]['oil_1_price'] = $vv;
                            }else{
                                $item_list[0]['oil_2_id'] = $id;
                                $item_list[0]['oil_2_name'] = $res['name'];
                                $item_list[0]['oil_2_num'] = $num;
                                $item_list[0]['oil_2_price'] = $vv;
                            }
                        }
					}
        		}
        	}
        	$oil_param['id'] =  $order_items['oil_id'];
        	$item_list['0']['id'] = $order_items['oil_id'];
        	$item_list['0']['price'] = $item_oil_price;
			$item_list['0']['norms'] = $norms;
        }elseif($order_items['oil_id']<0){
        	$item_list['0']['id'] = 0;
        	$item_list['0']['name'] = "自备配件";
        	$item_list['0']['price'] = 0;
        }elseif($order_items['oil_id']==0){
			$item_list['0']['id'] = 0;
			$item_list['0']['name'] = "不需要配件";
			$item_list['0']['price'] = 0;
		}

        if($order_items['filter_id']){
        	if($order_items['filter_id'] == '-1'){	//自备配件的情况
        		$item_list['1']['id'] = 0;
        		$item_list['1']['name'] = "自备配件";
        		$item_list['1']['price'] = 0;
        	}else{
	        	$item_condition['id'] = $order_items['filter_id'];
	        	$item_list['1'] = $this->item_model->where($item_condition)->find();
	        	//$item_list[1]['name'] = mb_substr( $item_list[1]['name'], 0, mb_strpos($item_list[1]['name'],' '));
                
                //去掉品牌后面的规格 by bright
                $item_list[1]['name'] = $this->getBrandName($item_list[1]['name']);
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
	        	//$item_list[2]['name'] = mb_substr( $item_list[2]['name'], 0, mb_strpos($item_list[2]['name'],' '));
                
                //去掉品牌后面的规格 by bright
                $item_list[2]['name'] = $this->getBrandName($item_list[2]['name']);
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
        		//$item_list[3]['name'] = mb_substr( $item_list[3]['name'], 0, mb_strpos($item_list[3]['name'],' '));
                
                //去掉品牌后面的规格 by bright
                $item_list[3]['name'] = $this->getBrandName($item_list[3]['name']);
        	}
        	$item_num++;
        }
        $item_amount = 99;
     	if ( $order_info['replace_code'] ) {
            if ($this->newCouponCodeModel->isNewCouponCode($order_info['replace_code'])) {//订单详情抵扣显示
                $value = $this->newCouponCodeModel->getDiscountAmount($order_info['replace_code']);
                if (!$this->newCouponCodeModel->isBaoyang($value['replace_code'])) {
                    $order_info['replace_code'] = null;
                }

                $item_amount = $item_amount - $value;
            } else {
                $value = $this->get_codevalue($order_info['replace_code']);//判断抵用卷的价钱
                if($value != 99){
                    $this->assign('replace_value',$value);
                    $item_amount = $item_amount - $value;	//加服务费，减抵用券费用
                }else{
                    $item_amount = 0;
                }
            }
            $this->assign('replace_value',$value);
        }
        
        if(is_array($item_list)){
        	foreach ($item_list as $key => $value) {
        		$item_amount += $value['price'];
        	}
        }
		$this->assign('live_id',$live_id);
		$this->assign('orderid',$order_id);
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

    //判断访问客户端 PC or Mobile
    function isMobile(){  
        $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  
        $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';        
        function CheckSubstrs($substrs,$text){  
            foreach($substrs as $substr)  
                if(false!==strpos($text,$substr)){  
                    return true;  
                }  
                return false;
        }
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  
              
        $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||  
                  CheckSubstrs($mobile_token_list,$useragent);  
              
        if ($found_mobile){  
            return true;  
        }else{  
            return false;  
        }  
    }


    //直播视频
    public function live(){
		if($_GET['order_id']){
			$get_live_id = @$_GET['order_id'];
			//判断用户合法性
			$order_id = substr($get_live_id,0,strrpos($get_live_id,'s'));//假ID
			$live_id = $order_id.'s'.md5($order_id.'fqcd123223');
			if($live_id == $get_live_id){
				$order_id = $this->get_true_orderid($order_id);//真ID
			}else{
				$this->error('您没有观看此订单视频的权限');
			}
		}else{
			$order_id = $_GET['order_id'];
		}
    	//查看订单是否正在录制
    	if (!$order_id) {
    		$this->assign('status','参数错误');
    	}
    	$mStep = M('tp_xieche.check_step','xc_');
    	$res = $mStep->field('step_id')->where( array('order_id'=>$order_id) )->order('id desc')->find();
		//echo $mStep->getLastsql();

		//获取播放链接
		//$url = "http://s.2xq.com:5099/get_order_video?order_id=".$order_id;
		$url = "http://s.2xq.com:9615/get_order_video?order_id=".$order_id;
		$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
		$result = file_get_contents($url,false,$strm);
		$result = json_decode($result,true);
		//print_r($result);
		//echo $result['data']['0'];

		$get_max = array();
		if(count($result)>0){
			foreach($result as $k=>$v){
				$get_max[] = $v['size'];
			}
		}
		$max = max($get_max);
		foreach($result as $k=>$v){
			if($v['size']==$max){
				$live_url = $v['live_url'];
				$vod_url = $v['vod_url'];
			}
		}
		
    	if ( !$res or $res['step_id']<2) {
			//echo "11111111111111";
    		$this->assign('status','还没有开始摄像，不能观看直播');
    	}elseif($res['step_id']>=2 and $res['step_id']<6){
			//echo "22222222222";
	    	$header = array("Accept:application/json");
	    	$url = "http://s.2xq.com:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live/instances/_definst_";
	    	$ch = curl_init ( ) ;
	    	curl_setopt( $ch,  CURLOPT_HTTPHEADER,$header);
	    	curl_setopt ( $ch , CURLOPT_URL, $url ) ;
	    	curl_setopt ( $ch , CURLOPT_RETURNTRANSFER, 1 ) ;
	    	// 发送用户名和密码
	    	curl_setopt ( $ch , CURLOPT_HTTPAUTH, CURLAUTH_DIGEST ) ;
	    	curl_setopt ( $ch , CURLOPT_USERPWD, "api:fqcd123223" ) ;
	    	// 你可以允许其重定向
	    	curl_setopt ( $ch , CURLOPT_FOLLOWLOCATION, 1 ) ;
	    	// 下面的选项让 cURL 在重定向后
	    	// 也能发送用户名和密码
	    	curl_setopt ( $ch , CURLOPT_UNRESTRICTED_AUTH, 1 ) ;
	    	$output = curl_exec ( $ch ) ;
	    	curl_close ( $ch ) ;
			//var_dump($output);echo "11111";
	    	$data = json_decode($output,true);
			//var_dump($data);
	    	if ( isset($data['incomingStreams'] )) {
		    	foreach($data['incomingStreams'] as $val){
		    		$name = $val['name'];
		    		$arr = explode('_', $name);
		    		$orderId = @$arr[1];
		    		//查询orderId
		    		if ($orderId !=$order_id) {
		    			continue;
		    		}
		    		$s_url = 'http://s.2xq.com:1935/live/'.$name.'/playlist.m3u8';
		    	}
				//echo $s_url;
		    	if( isset($name) && isset($s_url) ){
		    		$this->assign('url',$s_url);
		    	}else{
		    		$this->assign('status','还没有开始摄像，不能观看直播');
		    	}
	    	}

			if($live_url){
				$this->assign('url',$live_url);
			}else{
				$this->assign('status','还没有开始摄像，不能观看录像');
			}

			/*if($result['status'] == 1){
				//echo 'url='.$result['data']['0'];
				$url = str_replace('vod/mp4:','live/',$result['data']['0']);
				$url = str_replace('.mp4.tmp','',$url);
				$url = str_replace('.mp4','',$url);
				//echo $url;
				$this->assign('url',$url);
			}else{
				$this->assign('status','还没有开始摄像，不能观看录像');
			}*/
		}elseif($res['step_id']==6){
			//echo "33333333333";
			//获取播放链接
			/*$url = "http://s.2xq.com:5099/get_order_video?order_id=".$order_id;
			$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
			$result = file_get_contents($url,false,$strm);
			$result = json_decode($result,true);*/

			/*if($result['status'] == 1){
				$this->assign('url',$result['data']['0']);
			}else{
				$this->assign('status','还没有开始摄像，不能观看录像');
			}*/
			if($vod_url){
				$this->assign('url',$vod_url);
			}else{
				$this->assign('status','还没有开始摄像，不能观看录像');
			}
		}

        $isMobile = $this->isMobile();
        $this->assign('isMobile',$isMobile);
    	$this->display('live');
        
    }
    //三星活动页面
    public function samsung(){
        $this->display('samsung');
    }

    //三星活动页面
    public function baidu(){
        $this->display('baidu');
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


	//绑定推荐关系，推送图片，并且通知推荐人
	function spread_process(){
		//通知被推荐人
		$data['open_id'] = $open_id;
		$data['content'] = '';
		$this->send_weixintext($data);
	}

    //送机油首页
    public function castrol(){
        $weixin['weixin_id'] = "oF49ruGwcpz1RtvYpG4xG_5gkoCo";
        $spre = D('spreadoil');
        $mSpreadpic = D('spreadpic');
        $list = $spre->where($weixin)->select();
        $me='';
        if($list){
                $weixin_id = @$list[0]['weixin_id'];
                if($weixin_id){
                    $where = array('FromUserName'=>$weixin_id);
                    $spreadpicData = $mSpreadpic->where($where)->find();
                    $me = $spreadpicData;
                }
        }
        $this->assign("list",$list);
        $this->assign("me",$me);
        // var_dump($list);
        // var_dump($me);
        $this->display('castrol');
    }
    
    /*我的机油 
     * auth bright
     */
    public function newoil(){
    	$weixin_id = @$_GET['wx'];
    	if (!$weixin_id) {
    		$this->error('参数错误');
    	}
    	
    	if (mb_strpos($weixin_id,'**')) {
    		$weixin_id = str_replace('**', '-', $weixin_id);
    	}
    	if ( isset($_GET['can_prize']) && !isset($_SESSION['can_prize']) ) {
    		$canPrize = $_GET['can_prize'];
    		if (mb_strpos($canPrize,'**')) {
    			$canPrize = str_replace('**', '-', $canPrize);
    		}
    		//$_SESSION['can_prize'] = $canPrize;
    	}
    	$_SESSION['wx'] = $weixin_id;
        //echo 'weixin_id='.$weixin_id;
    	$where =  array('weixin_id'=>$weixin_id);
    	$mMyoil = D('spreadmyoil');
    	$list = $mMyoil->where($where)->find();
    	$userinfo = array();
    	if ($list) {
    		//当前剩余的油
    		if ($list['oil_num'] > 4000) {
    			$list['curOilNum'] =$list['oil_num'] % 4000 ;//当前剩余的油量
    			$list['oil_bucket'] = intval($list['oil_num'] / 4000);//已经积满的油桶
    		}else{
    			$list['curOilNum'] =$list['oil_num'];
    		}
    		//查找用户信息
    		$mSpreadpic = D('spreadpic');
    		$where2 = array('FromUserName'=>$weixin_id);
    		$userinfo = $mSpreadpic->field('nickname')->where($where2)->find();
    		$list['nickname'] = $userinfo['nickname'];
    		//查找最新其他人的动态
    		$mSpreadoil = D('spreadoil');
    		$otherDatas = $mSpreadoil->field('nickname,create_time')->where($where)->order('id desc')->limit(5)->select();
    		$otherDatas[] = $list;
    		//查找我的签到记录
    		$sign = D('spreadsign');
    	
    		$map['create_time'] = array(array('gt',strtotime(date('Y-m-d',time()).' 0:00:00')),array('lt',strtotime(date('Y-m-d',time()).' 23:59:59')));
    		$map['weixin_id'] = $weixin_id;
    		$sign_today = $sign->field('oil_num,create_time')->where($map)->find();
    		if ($sign_today) {
    			$sign_today['nickname'] = $userinfo['nickname'];
    			$sign_today['tag'] = 'sign';
    			 
    			$otherDatas[] = $sign_today;
    		}
    		//按照时间排序
    		usort($otherDatas, function($a,$b){
    			return strcmp($b["create_time"],$a["create_time"]);
    		});
    	}
    	$this->assign('weixin_id',$weixin_id);
    	$this->assign('otherDatas',$otherDatas);
    	$this->assign('userinfo',$userinfo);
    	$this->assign('list', $list);
    	$this->display('newoil');
    }

    //送机油府上养车介绍
    public function castrol_fsyc(){
        $this->display('castrol_fsyc');
    }

    //送机油订单提交
    public function castrol_order(){
        $this->display('castrol_order');
    }
    
    //签到页面
    public function sign(){
    	$weixin_id = @$_REQUEST['pa_id'];
    	if (!$weixin_id) {
    		$this->error('参数错误');
    	}
    	if (mb_strpos($weixin_id,'**')) {
    		 $_REQUEST['pa_id'] = str_replace('**', '-', $weixin_id);
    	}
    	$_SESSION['wx'] = $_REQUEST['pa_id'];
    	

		$sign = D('spreadsign');
		$map['create_time'] = array(array('gt',strtotime(date('Y-m-d',time()).' 0:00:00')),array('lt',strtotime(date('Y-m-d',time()).' 23:59:59')));
		$map['weixin_id'] = $_REQUEST['pa_id'];
		$sign_today = $sign->where($map)->find();
		
		
		if($sign_today){
			$this->assign('sign_today',$sign_today);
		}else{
			//如果当天已签到且不连续，签到天数不能强制为零
			//判断用户最后一次签到时间，如不连续，强制显示从零开始
			$l_map['weixin_id'] = $_REQUEST['pa_id'];
			$l_map['sign_date'] = strtotime(date('Y-m-d',time()).' 0:00:00')-86400;
			$sign_last = $sign->where($l_map)->find();

			if(!$sign_last){
				$myoil_info['sign_num'] = 0;
			}
		}

		$myoil = D('spreadmyoil');
		$myoil_info = $myoil->where(array('weixin_id'=>$_REQUEST['pa_id']))->find();

		$oil_num = array(
				1 => '5',
				2 => '6',
				3 => '7',
				4 => '8',
				5 => '9',
				6 => '10',
				7 => '10',
				8 => '10',
				);
		if($myoil_info['sign_num']<=6){
			$oilnum = $oil_num[$myoil_info['sign_num']];
			$a = $myoil_info['sign_num']+1;
			$next_oilnum = $oil_num[$a];
		}else{
			$oilnum = 10;
			$next_oilnum = 10;
		}

		if($myoil_info['sign_num']>7){
			$start = $myoil_info['sign_num']-4;
			$end = $myoil_info['sign_num']+6;
		}else{
			$start = 1;
			$end = 11;
		}

		for($a=$start;$a<$end;$a++){
			if($a==$myoil_info['sign_num'] and $sign_today){
				if($a<10){$a = '0'.$a;}
				$style[$a] = "<div class=\"fl\"><img src=\"/Public/mobile/images/castrol/signTipBg4.png\" width=\"24\" height=\"24\" /><p class=\"cur\">".$a."</p></div>";
			}elseif($a>$myoil_info['sign_num']){
                if($a==$myoil_info['sign_num']+1 and $a!=1){
					if($a<10){$a = '0'.$a;}
                    $style[$a] = "<div class=\"fl\"><p class=\"bg5\">{$a}<i></i></p><div class=\"line\">下次签到可领".$next_oilnum."ml</div></div>";
				}elseif($a==1){
                    $style[$a] = "<div class=\"fl\"><p class=\"bg5\">0{$a}<i></i></p><div class=\"line\">本次签到可领".$next_oilnum."ml</div></div>";
                }else{
					if($a<10){$a = '0'.$a;}
                    $style[$a] = "<div class=\"fl\"><p class=\"bg5\">".$a."</p></div>";
                }
			}else{
				if($a<10){$a = '0'.$a;}
				$style[$a] = "<div class=\"fl\"><p>".$a."</p></div>";
			}
		}

		$this->assign('oilnum',$oilnum);
		$this->assign('style',$style);
    	$this->assign('sign_num',$myoil_info['sign_num']);
    	$this->display('ac_sign');
    }
    public function commentList(){
    	$map = array();
    	$mComment = D('reserveorder_comment');
    	$mTechnican = D('technician');
    	// 计算总数
        $count = $mComment->where($map)->count();
        $goodcomment = $mComment->where(array('type'=>1))->count();
        $gootrate = sprintf("%.2f", (($goodcomment/$count)*100))."%";
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,10);
        foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        if(!$_REQUEST['p']){
            $p->parameter = "index/".$p->parameter;
        }
        // 分页显示输出
        $page = $p->show();
        
        // 当前页数据查询
        $list = $mComment->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list) {
        	foreach ($list as &$val){
        		switch ($val['type']){
        			case 1:
        				$type = '很满意';
        				break;
        			case 2:
        				$type = '一般满意';
        				break;
        			case 3:
        				$type = '不满意';
        				break;
        		}
        		$val['type'] = $type;
        		$order_id = $val['reserveorder_id'];
        		if ($order_id) {
	        		$order = $this->reservation_order_model->field('mobile,technician_id,truename')->where( array('id'=>$order_id) )->find();
	        		if ($order) {
	        			$mobile = substr($order['mobile'],0,3);
	        			$mobile .= '****';
	        			$mobile .= substr($order['mobile'],-4,4);
	        			$val['mobile'] = $mobile;

                        $val['truename'] = '*'.mb_substr($order['truename'],1,strlen($order['truename']),'utf-8');
	        			$technican = $mTechnican->field('truename')->where( array('id'=>$order['technician_id']) )->find();
	        			if ($technican) {
		        			$val['technician_name'] = $technican['truename'];
	        			}else{
	        				$val['technician_name'] = '刘成';
	        			}
	        		}
        		}else{
        			$val['technician_name'] = '刘成';
        			$val['mobile'] = '13'.rand(0,9).'****'.rand(1001,9999);
        		}
        		if (!$val['comment_time']) {
        			$rand = rand(10,99);
        			$val['comment_time'] = '141'.$rand.'76800';
        		}
        		if ($val['content'] == '输入您的意见、不少于5个字') {
        			unset($val['content']);
        		}
        		unset($val);
        	}
        }
        $page = mb_substr($page,0,mb_strpos($page,'下一页>')+12);
        $this->assign('page',$page);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('gootrate', $gootrate);
        $this->display('comment_list');
    }
    
	//签到流程
	//author:wwy
    public function sign_process(){
		//测试数据
		$_REQUEST['weixin_id'] = $_SESSION['wx'];
		if(!$_REQUEST['weixin_id']){
			echo json_encode(array('status'=>0,'msg'=>'参数错误')); 
			exit;
		}else{
			$myoil = D('spreadmyoil');
			$sign = D('spreadsign');
			$oil_num = array(
				0 => '5',
				1 => '6',
				2 => '7',
				3 => '8',
				4 => '9',
				5 => '10',
				6 => '10',
			);
			$map['create_time'] = array(array('gt',strtotime(date('Y-m-d',time()).' 0:00:00')),array('lt',strtotime(date('Y-m-d',time()).' 23:59:59')));
			$map['weixin_id'] = $_REQUEST['weixin_id'];
			$repeat = $sign->where($map)->find();

			//当天内签到过的不能重复签到
			if($repeat){
				echo json_encode(array('status'=>0,'msg'=>'今天已经签到过了，不能重复签到'));
				exit;
			}else{
				$myoil_info = $myoil->where(array('weixin_id'=>$_REQUEST['weixin_id']))->find();
				if(!$myoil_info){
					echo json_encode(array('status'=>0,'msg'=>'尚未关注，找不到您的油桶哦'));
					exit;
				}
				$sign_info = $sign->where(array('weixin_id'=>$_REQUEST['weixin_id']))->order('id desc')->find();
				//签到表进数据
				$data['weixin_id'] = $_REQUEST['weixin_id'];
				if($myoil_info['sign_num']<=6){
					$data['oil_num'] = $oil_num[$myoil_info['sign_num']];
				}else{
					$data['oil_num'] = 10;
				}
				$data['sign_date'] = strtotime(date('Y-m-d',time()).' 0:00:00');
				$data['create_time'] = time();
				$res = $sign->add($data);

				if($res){
					//计算连续签到数
					if($sign_info['sign_date'] == $data['sign_date']-86400){
						//连续签到，累积签到数+1
						$myoil->where(array('weixin_id'=>$_REQUEST['weixin_id']))->setInc('sign_num');
					}else{
						//签到中断，签到数重新计算
						$myoil->where(array('weixin_id'=>$_REQUEST['weixin_id']))->setField('sign_num','1');
					}
					$res2 = $myoil->where(array('weixin_id'=>$_REQUEST['weixin_id']))->setInc('oil_num',$data['oil_num']);
					if($res2){
						echo json_encode(array('status'=>1,'msg'=>'签到累积成功'));
					}else{
						echo json_encode(array('status'=>1,'msg'=>'签到累积失败'));
					}
				}else{
					echo json_encode(array('status'=>0,'msg'=>'签到失败'));
				}
			}
		}
    }

    public function carservice_base(){
        if($_REQUEST['param']){
            //echo $_REQUEST['param'];
        }
        
        if($_REQUEST['source_id']==1){
            //echo $_REQUEST['source_id'];
                    
        }
        
        if($_REQUEST['ordertype']){ 
                    $ordertype = intval(trim($_REQUEST['ordertype']));
                    $_SESSION['ordertype'] = $this->get_true_orderid($ordertype);
        }
		if($_REQUEST['business_source']){
			$_SESSION['business_source'] = $_REQUEST['business_source'];
		}

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

        if(isset($_REQUEST['ali_id']) && !empty($_REQUEST['ali_id'])){
            $_SESSION['ali_id'] = $_REQUEST['ali_id'];
        }

        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);
    
        $this->assign('title',"上门保养-携车网");

        $this->display();
    }

    public function carservice_pa(){
        $this->display();
    }

    //光大银行
    public function carservice_cebbank(){
        $this->display();
    }
	//光大银行金卡，白金卡首页
	public function carservice_cebmemberscard(){
		$this->display();
	}

    //光大银行信用卡
    public function carservice_cebbankcard(){
        $this->display();
    }
	//光大银行黄金信用卡
    public function carservice_cebbankgoldcard(){
        $this->display();
    }
     //我爱我家pc入口   xzw@20150609
	public function my_love_family_pc(){
		  //我爱我家统计
		$add_data['ip'] = $_SERVER["REMOTE_ADDR"];
		$add_data['type'] = 3 ;
		$add_data['create_time']= time();
		$this->act_count->add($add_data);
		$this->display();
	}
	//我爱我家(重礼1)   xzw@20150609
	public function gift1_aj(){
		$this->display();
	}
	//我爱我家pc(重礼2)   xzw@20150609
	public function gift2_aj(){
		$this->display();
	}
    //建设银行手机入口  wql@20150513
    public function carservice_ccbbank(){
        $_SESSION['enter'] = 'mobile' ;
		//访问信息统计
		$add_data['ip'] = $_SERVER["REMOTE_ADDR"];
		$add_data['type'] = 1 ;
		$add_data['create_time']= time();
		$this->act_count->add($add_data);
		
        $this->display();
    }
    //建设银行电脑入口  wql@20150513
    public function carservice_ccbbank_pc(){
        $_SESSION['enter'] = 'pc' ;
		//访问信息统计
		$add_data['ip'] = $_SERVER["REMOTE_ADDR"];
		$add_data['type'] = 1 ;
		$add_data['create_time']= time();
		$this->act_count->add($add_data);
		
        $this->display();
    }
    
    //百度生活手机入口  wwy@20150608
    public function carservice_baidulife(){
        $this->display();
    }
	//通用168入口    wwy@20150608
    public function carservice_168(){
        $this->display();
    }
	//通用199入口    wwy@20150608
    public function carservice_199(){
        $this->display();
    }
    
    //杭州车猫合作套餐入口   wql@20150717
    public function carservice_hzchemao(){
        $this->display();
    }

    //快的    wwy@20150817
    public function carservice_kuaidi(){
        $this->display();
    }

	//uber入口
	public function carservice_uber(){
		$this->display();
	}
	public function carservice_uber_pc(){
		$this->display();
	}
	//宝驾
    public function carservice_bj(){
        $this->display();
    }

    public function carservice_hd(){
        $this->display();
    }

    public function carservice_as(){
        $this->display();
    }

    public function carservice_pabank(){
        $this->display();
    }

    public function carservice_test(){
        $this->display();
    }

    public function carservice_con(){
        $this->display();
    }
    public function carservice_as_use(){
        $this->display();
    }
    public function carservice_rb_use(){
        $this->display();
    }
    public function carservice_pabank_use(){
        $this->display();
    }

    public function carservice_rb(){
        $this->display();
    }

    public function carservice_tencent(){
        $this->display();
    }

    public function carservice_tencent_kongtiao(){
        $this->display();
    }

    public function carservice_cmbchina(){
        $this->display();
    }

    public function carservice_cmbchina_kongtiao(){
        $this->display();
    }

    public function carservice_shangfei(){
        $this->display();
    }

    public function carservice_aidaijia(){
        $this->display();
    }

    public function haokongqi(){
        $this->display('wumai');
    }

    /**
     * 成都苏宁活动
     * @date 2015-09-17 11:54:48
     */
    public function carservice_chengdusuning()
    {
        $this->display();
    }

    //成都红星美凯龙    wwy@20150925
    public function carservice_meikailong(){
        $this->display();
    }

    //重庆聚满堂老火锅    wwy@20150928
    public function carservice_jumantang(){
        $this->display();
    }

    //全民PA    wwy@20150928
    public function carservice_quanmingpa(){
        $this->display();
    }

    //全民PA检测    wwy@20150928
    public function carservice_quanmingpa_jiance(){
        $this->display();
    }

    public function carservice_change(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);

        $this->display();
    }

    public function carservice_change_hkq(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);

        $this->display();
    }

    public function carservice_change_as(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);

        $this->display();
    }

    public function carservice_change_pabank(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);

        $this->display();
    }

    //光大银行
    public function carservice_change_cebbank(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
        
        if($_REQUEST['sourcetype']){
            $_SESSION['sourcetype'] = $_REQUEST['sourcetype'];
        }
        
        if(substr($_SERVER['HTTP_REFERER'],-11,11)=='cebbankcard'){
                $_SESSION['sourcetype'] = 1;
        }
        if(substr($_SERVER['HTTP_REFERER'],-15,15)=='cebbankgoldcard'){
                $_SESSION['sourcetype'] = 2;
        }
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);

        $this->display();
    }
    
    
    //建设银行  wql@20150514
    public function carservice_change_ccbbank(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
		
 
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);
        
        //通过入口变量找不同的模板
        if($_SESSION['enter']=='pc'){
            $this->display('carservice_change_ccbbank_pc');
        }else{
           $this->display(); 
        }
        
        
    }
    
    
    
      //建设银行下单页面  wql@20150514
    public function order_ccbbank(){
        session_start();
	
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

   
        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
        
       
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        

        
        if($_SESSION['patype'] == 2 or $_SESSION['patype'] == 7){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3 or $_SESSION['patype'] == 8){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
        if($_SESSION['patype'] == 4 or $_SESSION['patype'] == 9){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
		if($_SESSION['patype'] == 9){
            if ( !in_array('45', $oilIds2) and !in_array('46', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用灰喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 45;
            $_SESSION['oil_detail'] = array('45'=>$yy,'46'=>$xx);
        }
       
        //好空气套餐16.8 ,奥迪宝马奔驰除外 dingjb 2015-09-21 10:54:41 修改套餐价格为 38
        if($_SESSION['patype'] == 5 ){
            if ($_SESSION['brand_id']==1 OR $_SESSION['brand_id']==3 OR $_SESSION['brand_id']==4) {
                $tips = "该车型无法使用好空气38套餐";
                $this->assign('tips', $tips);   
            }
        }
        
       
        
        if($_SESSION['patype']>1&&$_SESSION['patype']<5 or $_SESSION['patype']>6&&$_SESSION['patype']<10){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }

            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
            $_SESSION['item_2'] = -1;
            $_SESSION['item_3'] = -1;  
        }elseif($_SESSION['patype'] == 5 OR $_SESSION['patype'] == 6 OR $_SESSION['patype'] == 10 OR $_SESSION['patype'] == 11){
            //获取价格最低的空调滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['3'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }

            $_SESSION['item_3'] = array_search(min($new_res),$new_res);
            $_SESSION['item_1'] = -1;
            $_SESSION['item_2'] = -1; 
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
            $_SESSION['item_2'] = -1;
            $_SESSION['item_3'] = -1;
        }
        
        

        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if ($_SESSION['patype']==2) {
            $item_amount = 168;
        }elseif ($_SESSION['patype']==3) {
            $item_amount = 268;
        }elseif ($_SESSION['patype']==4) {
            $item_amount = 368;
        }elseif ($_SESSION['patype']==7) {
            $item_amount = 199;
        }elseif ($_SESSION['patype']==8) {
            $item_amount = 299;
        }elseif ($_SESSION['patype']==9) {
            $item_amount = 399;
        }elseif($_SESSION['patype']==5 or $_SESSION['patype']==10){
            //好空气16.8套餐. dingjb 2015-09-21 10:48:07 修改套餐价格为 38
            $item_amount = 38;
        }elseif($_SESSION['patype']==6 or $_SESSION['patype']==11){
            //好空气98套餐.
            $item_amount = 98;
        }

       // echo  '<br>' ;
       // echo   $item_amount ;
        
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        //$this->display();
        //通过入口变量找不同的模板
        if($_SESSION['enter']=='pc'){
           $this->display('order_ccbbank_pc');
        }else{
           $this->display(); 
        }
    }
  
    
    
    //建设银行创建订单,订单入库   wql@20150514    
    public function create_order_ccbbank(){
        session_start();
 
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
            // dingjb 2015-09-29 11:35:39 切换到云通讯
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
        
        $order_info['pay_type'] = 1;
        
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
       
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆弹计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 建设银行合作
        if ($_SESSION['patype']==2) {
            $order_info['amount'] = 168;
            $order_info['remark'] = "中国建设银行168套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 22;	
        }elseif ($_SESSION['patype']==3) {
            $order_info['amount'] = 268;
            $order_info['remark'] = "中国建设银行268套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 23;
        }elseif ($_SESSION['patype']==4) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "中国建设银行368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 24;
        }elseif ($_SESSION['patype']==5) { // dingjb 2015-09-21 10:56:10 修改套餐价格 16 -> 38
            $order_info['amount'] = 38;
            $order_info['remark'] = "中国建设银行好空气38套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-38;
            $order_info['order_type'] = 14;
        }elseif ($_SESSION['patype']==6) {
            $order_info['amount'] = 98;
            $order_info['remark'] = "中国建设银行好空气98套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-98;
            $order_info['order_type'] = 33; 
        }
		//百度生活
		if ($_SESSION['patype']==7) {
            $order_info['amount'] = 199;
            $order_info['remark'] = "百度生活壳牌黄喜力199套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 36;	
        }elseif ($_SESSION['patype']==8) {
            $order_info['amount'] = 299;
            $order_info['remark'] = "百度生活壳牌蓝喜力299套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 37;
        }elseif ($_SESSION['patype']==9) {
            $order_info['amount'] = 399;
            $order_info['remark'] = "百度生活金美孚399套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 38;
        }elseif ($_SESSION['patype']==10) {
            $order_info['amount'] = 38; // dingjb 2015-09-21 10:51:07 修改套餐价格 : 16.8 -> 38
            $order_info['remark'] = "百度生活好空气38套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-38;
            $order_info['order_type'] = 52;
        }elseif ($_SESSION['patype']==11) {
            $order_info['amount'] = 98;
            $order_info['remark'] = "百度生活好空气98套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-98;
            $order_info['order_type'] = 55; 
        }
		
        //业务来源
		if($_SESSION['patype']==2 or $_SESSION['patype']==3 or $_SESSION['patype']==4 or $_SESSION['patype']==5 or $_SESSION['patype']==6){
			$order_info['business_source'] = 33 ;
		}else{
			$order_info['business_source'] = 35 ;
		}
        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            //提交成功缓存手机
            $_SESSION['pamobile'] = $order_info['mobile'];
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-18:00）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_cebbank-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }
    
    //通用  wwy@20150514
    public function carservice_change_ty(){
        if($_REQUEST['patype']){
            $_SESSION['patype'] = $_REQUEST['patype'];
        }else{
            $this->error('参数错误');
        }
		
 
        //var_dump($_SESSION['patype']);exit;
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
        $brand_list = $this->new_carbrand->where('is_show=1')->order('word asc')->select();
        foreach($brand_list as $k=>$v){
            $car_model_list = $this->new_carseries->where(array('brand_id'=>$v['brand_id']))->select();
            foreach($car_model_list as $key=>$value){
                $map['series_id'] = $value['series_id'];
                $map['is_show'] = '1';
                $count = $this->new_carmodel->where($map)->count();
                if($count==0){
                    unset($car_model_list[$key]);
                }
            }
            if(count($car_model_list)==0){
                unset($brand_list[$k]);
            }
        }
        $this->assign("brand_list", $brand_list);
        
        $this->display();       
    }
    
    
    
      //通用下单页面  wwy@20150514
    public function order_ty(){
        session_start();
        
        //brand_id如果是-1 ，表示是热门车型进来，重新查询品牌id 。wql@20150825
        if($this->_get('brand_id')=='-1'){
            $cond = array() ;
            $cond['series_id'] =  intval($this->_get('series_id')) ;
            $carseries_info = $this->new_carseries->where($cond)->find() ;
            $_SESSION['brand_id'] = $carseries_info['brand_id'] ;
        }else{
            $_SESSION['brand_id'] = intval($this->_get('brand_id'));
        }
        
        
        $model_id = $_SESSION['model_id'] = intval($this->_get('model_id'));
        $_SESSION['series_id'] = intval($this->_get('series_id'));

        //车型
        $style_param['model_id'] = $_SESSION['model_id'];
        $car_style = $this->new_carmodel->where($style_param)->find();
       
        //机油
        $oil_num = ceil($car_style['oil_mass']);
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
            $car_style['norms'] = $oil_num."L";//多少升机油
        }

    
        //所有物品详情
        $oil_list_all = $this->new_oil->where()->select();
        $oil_item = array();
        foreach( $oil_list_all as $nors){
            
            $nors['name'] = rtrim($nors['name'],'装');
            $oil_item[$nors['name']][$nors['norms']] = $nors;
        }
        
       
        $oil_param = array();
        //根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
        
        switch ($car_style['oil_type']){
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
 
        unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3'],$_SESSION['oil_detail']);

        //符合要求的品牌详情
        $item_sets=array();
        $oilIds = $this->new_oil->field('id')->where($oil_param)->select();
        foreach($oilIds as $key=>$value)
        {
          $oilIds2[$key]=$value['id'];
        }
        
        
        if($_SESSION['patype'] == 2 or $_SESSION['patype'] == 7 or $_SESSION['patype']==12 or $_SESSION['patype']==17 or $_SESSION['patype']==22 or $_SESSION['patype']==32 or $_SESSION['patype']==37 or $_SESSION['patype']==47 or $_SESSION['patype']==51 or $_SESSION['patype']==55 or $_SESSION['patype']==60 or $_SESSION['patype']==65 or $_SESSION['patype']==70){
            if ( !in_array('49', $oilIds2) and !in_array('59', $oilIds2) ) {//黄喜力
                $tips = "该车型无法使用壳牌黄喜力套餐机油";
                $this->assign('tips', $tips);   
            }
            //拼机油数据
            $_SESSION['item_0'] = 49;
            $_SESSION['oil_detail'] = array('49'=>$yy,'59'=>$xx);
        }
        if($_SESSION['patype'] == 3 or $_SESSION['patype'] == 8 or $_SESSION['patype']==13 or $_SESSION['patype']==18 or $_SESSION['patype']==23 or $_SESSION['patype']==33 or $_SESSION['patype']==38 or $_SESSION['patype']==48 or $_SESSION['patype']==52 or $_SESSION['patype']==56 or $_SESSION['patype']==61 or $_SESSION['patype']==66 or $_SESSION['patype']==71){
            if ( !in_array('47', $oilIds2) and !in_array('48', $oilIds2) ) {//蓝喜力
                $tips = "该车型无法使用壳牌蓝喜力套餐机油";
                $this->assign('tips', $tips);    
            }
            $_SESSION['item_0'] = 47;
            $_SESSION['oil_detail'] = array('47'=>$yy,'48'=>$xx);
        }
		//------------
		if($_SESSION['patype'] == 14 ) {
			if ( !in_array('45', $oilIds2) and !in_array('46', $oilIds2) ) {//灰喜力
				$tips = "该车型无法使用灰喜力套餐机油";
				$this->assign('tips', $tips);
			}
			$_SESSION['item_0'] = 45;
			$_SESSION['oil_detail'] = array('45'=>$yy,'46'=>$xx);
		}

        if($_SESSION['patype'] == 4 or $_SESSION['patype'] == 9 or $_SESSION['patype'] == 19 or $_SESSION['patype'] == 24 or $_SESSION['patype'] == 31  or $_SESSION['patype']==34 or $_SESSION['patype']==39 or $_SESSION['patype']==49 or $_SESSION['patype']==53 or $_SESSION['patype']==57 or $_SESSION['patype']==62 or $_SESSION['patype']==67 or $_SESSION['patype']==72){
            if ( !in_array('50', $oilIds2) and !in_array('51', $oilIds2) ) {//金美孚
                $tips = "该车型无法使用金美孚套餐机油";
                $this->assign('tips', $tips);   
            }
            $_SESSION['item_0'] = 50;
            $_SESSION['oil_detail'] = array('50'=>$yy,'51'=>$xx);
        }
       
        //好空气套餐16.8 ,奥迪宝马奔驰除外
        if($_SESSION['patype'] == 5 or $_SESSION['patype'] == 10 or $_SESSION['patype'] == 29  or $_SESSION['patype']==35 or $_SESSION['patype']==44
            or $_SESSION['patype'] == 46 or $_SESSION['patype'] == 58 or $_SESSION['patype'] == 63 or $_SESSION['patype']==68 or $_SESSION['patype']==73){
            if ($_SESSION['brand_id']==1 OR $_SESSION['brand_id']==3 OR $_SESSION['brand_id']==4) {
                $tips = $_SESSION['patype'] == 46 ? "该车型无法使用好空气38套餐" : "该车型无法使用好空气38套餐";
                $this->assign('tips', $tips);   
            }
        }
        
       
        
        if($_SESSION['patype']>1&&$_SESSION['patype']<5 or $_SESSION['patype']>6&&$_SESSION['patype']<10 or $_SESSION['patype']>11&&$_SESSION['patype']<15 or $_SESSION['patype']>16&&$_SESSION['patype']<20 or $_SESSION['patype']>21&&$_SESSION['patype']<25 or $_SESSION['patype']>30&&$_SESSION['patype']<35 or $_SESSION['patype']>36&&$_SESSION['patype']<40
            or $_SESSION['patype'] >= 47 && $_SESSION['patype'] <= 49 or $_SESSION['patype'] >= 51 && $_SESSION['patype'] <= 53 or $_SESSION['patype'] >= 55 && $_SESSION['patype'] <= 57 or $_SESSION['patype'] >= 60 && $_SESSION['patype'] <= 62 or $_SESSION['patype'] >= 65 && $_SESSION['patype'] <= 67 or $_SESSION['patype'] >= 70 && $_SESSION['patype'] <= 72){
            //获取价格最低的机滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['1'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }

            $_SESSION['item_1'] = array_search(min($new_res),$new_res);
            $_SESSION['item_2'] = -1;
            $_SESSION['item_3'] = -1;  
        }elseif($_SESSION['patype'] == 5 OR $_SESSION['patype'] == 6 OR $_SESSION['patype'] == 10 OR $_SESSION['patype'] == 11  OR $_SESSION['patype'] == 29 OR $_SESSION['patype'] == 30 OR $_SESSION['patype'] == 35 OR $_SESSION['patype'] == 36 OR $_SESSION['patype'] == 44 OR $_SESSION['patype'] == 45
                or $_SESSION['patype'] == 46 OR $_SESSION['patype'] == 50 OR $_SESSION['patype'] == 58 OR $_SESSION['patype'] == 59 OR $_SESSION['patype'] == 63 OR $_SESSION['patype'] == 64 OR $_SESSION['patype'] == 68 OR $_SESSION['patype'] == 69 OR $_SESSION['patype'] == 73 OR $_SESSION['patype'] == 74){
            //获取价格最低的空调滤
            $res = $this->get_item($_SESSION['model_id']);
            foreach($res['3'] as $a=>$b){
                $new_res[$b['id']] = $b['price']; 
            }

            $_SESSION['item_3'] = array_search(min($new_res),$new_res);
            $_SESSION['item_1'] = -1;
            $_SESSION['item_2'] = -1; 
        }else{
            $_SESSION['item_0']= -1;
            $_SESSION['oil_detail']= array();
            $_SESSION['item_1']= -1;
            $_SESSION['item_2'] = -1;
            $_SESSION['item_3'] = -1;
        }
        
        

        $userinfo = $this->user_model->where(array('uid'=>$_SESSION['uid']))->find();

        if (isset($_SESSION['mobile'])) {
            $userinfo['mobile']= $_SESSION['mobile'];
        }

        if ($_SESSION['patype']==2 or $_SESSION['patype']==12 or $_SESSION['patype']==17 or $_SESSION['patype']==32 or $_SESSION['patype']==51 or $_SESSION['patype']==55) {
            $item_amount = 168;
        }elseif ($_SESSION['patype']==3 or $_SESSION['patype']==13  or $_SESSION['patype']==18 or $_SESSION['patype']==33 or $_SESSION['patype']==52 or $_SESSION['patype']==56) {
            $item_amount = 268;
        }elseif ($_SESSION['patype']==4 or $_SESSION['patype']==14 or $_SESSION['patype']==19 or $_SESSION['patype']==34 or $_SESSION['patype']==53 or $_SESSION['patype']==57) {
            $item_amount = 368;
        }elseif ($_SESSION['patype']==7 or $_SESSION['patype']==22 or $_SESSION['patype']==37 or $_SESSION['patype']==47 or $_SESSION['patype']==60 or $_SESSION['patype']==65 or $_SESSION['patype']==70) { // 黄喜力 199
            $item_amount = 199;
        }elseif ($_SESSION['patype']==8 or $_SESSION['patype']==23 or $_SESSION['patype']==38 or $_SESSION['patype']==48 or $_SESSION['patype']==61 or $_SESSION['patype']==66 or $_SESSION['patype']==71) { // 蓝喜力 299
            $item_amount = 299;
        }elseif ($_SESSION['patype']==9 or $_SESSION['patype']==24 or $_SESSION['patype']==39 or $_SESSION['patype']==49 or $_SESSION['patype']==62 or $_SESSION['patype']==67 or $_SESSION['patype']==72) { // 金美孚 399
            $item_amount = 399;
        }elseif($_SESSION['patype']==5 or $_SESSION['patype']==10 or $_SESSION['patype']==29 or $_SESSION['patype']==35 or $_SESSION['patype']==44 or $_SESSION['patype']==58 or $_SESSION['patype']==63 or $_SESSION['patype']==68 or $_SESSION['patype']==73){
            //好空气16.8套餐. dingjb 2015-09-21 11:01:01  修改套餐价格 16.8 -> 38
            $item_amount = 38;
        }elseif($_SESSION['patype']==6 or $_SESSION['patype']==11 or $_SESSION['patype']==30 or $_SESSION['patype']==36 or $_SESSION['patype']==45 or $_SESSION['patype']==50 or $_SESSION['patype']==59 or $_SESSION['patype']==64 or $_SESSION['patype']==69 or $_SESSION['patype']==74){
            //好空气98套餐.
            $item_amount = 98;
        }elseif($_SESSION['patype']==15 or $_SESSION['patype']==16 or $_SESSION['patype']==27 or $_SESSION['patype']==28){
            //好动力套餐.发动机舱精洗套餐
            $item_amount = 38;
        }elseif($_SESSION['patype']==20 or $_SESSION['patype']==21){
            //空调清洗套餐.轮毂清洗套餐
            $item_amount = 18;
        }elseif($_SESSION['patype']==25 or $_SESSION['patype']==26 or $_SESSION['patype']==40 or $_SESSION['patype']==41){
            //腾讯节气门清洗套餐.腾讯发动机舱清洗套餐
            $item_amount = 68;
        }elseif($_SESSION['patype']==31){
            //一周年套餐价
            $item_amount = 365;
        }elseif($_SESSION['patype']==46) {
            // 成都苏宁 好空气 16.8 改为 38。 dingjb 2015-09-17 14:40:44
            $item_amount = 38;
        }elseif($_SESSION['patype']==54) {
            //商飞发动机舱精洗
            $item_amount = 58;
        }elseif($_SESSION['patype']==75) {
            //检测
            $item_amount = 9.8;
        }

       // echo  '<br>' ;
     // echo   $item_amount ;

		//城市数据
		$city_model = D('city');
		$city_list = $city_model->select();
		$this->assign('city_list',$city_list);
        $this->assign('patype',$_SESSION['patype']);
        $this->assign('item_num',$item_num);
        $this->assign("userinfo", $userinfo);
        $this->assign("item_list", $item_list);
        $this->assign("item_amount", $item_amount);
        $this->assign('title',"订单详情-上门保养-携车网");
        
        $this->display(); 
    }
  
    
    
    //通用创建订单,订单入库  wwy@20150514    
    public function create_order_ty(){
        session_start();
 
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
            // dingjb 2015-09-29 11:43:13 切换到云通讯
            $send_add_user_data = array(
                'phones' => $this->_post('mobile'),
                'content'=>array(
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
        
        $order_info['pay_type'] = 1;
        
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
       
        $order_info['order_time'] = 0;
        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        $order_info['city_id'] = $this->_post('city_id');      
        
        $oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;

        //根据机油拆单计算
        foreach ( $_SESSION['oil_detail'] as $_id=>$_num){
            $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$_id))->find();
            $total_norms+=$res['norms']*$_num;
        }
        if($total_norms>4){
            foreach ( $_SESSION['oil_detail'] as $id=>$num){
                $res = $this->new_oil->field('name,norms,price')->where( array('id'=>$id))->find();
                $norms+=$res['norms']*$num;
                if($res['norms']==1){
                    $one_id = $id;
                    unset($_SESSION['oil_detail'][$id]);
                }else{
                    $four_id = $id;
                    $_SESSION['oil_detail'][$id] = 1;
                }
            }

            $left_norms = $norms-4;
            $aa = $left_norms%4;
            $bb = ($left_norms - $aa)/4;
            $oil_detail2 = array($one_id=>$aa,$four_id=>$bb);
            //print_r($oil_detail2);print_r($_SESSION['oil_detail']);exit;

            /*计算拆分订单的配件数据---START---*/
            if($oil_detail2){
                //通过机油id查出订单数据
                $item_oil_price = 0;
                $i = 0;
                foreach ( $oil_detail2 as $_a=>$_b){
                    if($_b > 0){
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$_a))->find();
                        if ($i == 0) {
                            $oil_1_id = $_a;
                            $oil_1_price = $res['price']*$_b;
                            $i++;
                        }else{
                            $oil_2_id = $_a;
                            $oil_2_price = $res['price']*$_b;
                        }
                        $item_oil_price += $res['price']*$_b;
                        $name = $res['name'];
                    }
                }
                $item_list['0']['id'] = $_SESSION['item_0'];
                $item_list['0']['name'] = $name;
                $item_list['0']['price'] = $item_oil_price;
            }
        
            $item_amount2 = 0;
            if(is_array($item_list)){
                foreach ($item_list as $key => $value) {
                    $item_amount2 += $value['price'];
                }
            }
             
            $item_content2 = array(
                    'oil_id'     => $_SESSION['item_0'],
                    'oil_detail' => $oil_detail2,
                    'filter_id'  => -1,
                    'kongqi_id'  => -1,
                    'kongtiao_id' =>-1,
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
             
            $item2 = serialize($item_content2);
            /*计算拆分订单的配件数据---END---*/
        }
        //计算总价
        if($_SESSION['item_0']){
            if( $_SESSION['item_0'] == '-1' ){  //不是自备配件
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
                        $res = $this->new_oil->field('name,price')->where( array('id'=>$id))->find();
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
                $item_list['1'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['2'] = $this->new_filter->where($item_condition)->find();
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
                $item_list['3'] = $this->new_filter->where($item_condition)->find();
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
                'oil_id'     => $_SESSION['item_0'],
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
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];
        
        //上门保养订单类型 建设银行合作
        if ($_SESSION['patype']==2 or $_SESSION['patype']==12 or $_SESSION['patype']==17 or $_SESSION['patype']==32 or $_SESSION['patype']==51) {
            $order_info['amount'] = 168;
			if( $_SESSION['patype']==12){
				$order_info['remark'] = "uber壳牌黄喜力168套餐";
			}else{
				$order_info['remark'] = "壳牌黄喜力168套餐";
			}
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-168;
            $order_info['order_type'] = 22;	
        }elseif ($_SESSION['patype']==3 or $_SESSION['patype']==13 or $_SESSION['patype']==18 or $_SESSION['patype']==33 or $_SESSION['patype']==52) {
            $order_info['amount'] = 268;
			if( $_SESSION['patype']==13){
				$order_info['remark'] = "uber壳牌蓝喜力268套餐";
			}else{
				$order_info['remark'] = "壳牌蓝喜力268套餐";
			}
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-268;
            $order_info['order_type'] = 23;
        }elseif ($_SESSION['patype']==4 or $_SESSION['patype']==19 or $_SESSION['patype']==34 or $_SESSION['patype']==53) {
            $order_info['amount'] = 368;
            $order_info['remark'] = "金美孚368套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
            $order_info['order_type'] = 24;
        }elseif ($_SESSION['patype']==5 or $_SESSION['patype']==35) { // dingjb 2015-09-21 11:02:36 修改好空气 16.8 套餐价格为 38
            $order_info['amount'] = 38;
			if( $_SESSION['patype']==15){
				$order_info['remark'] = "uber好空气38套餐";
			}else{
				$order_info['remark'] = "好空气38套餐";
			}
            $order_info['dikou_amount'] = $kongtiao_price+99-38;
            $order_info['order_type'] = 14;
        }elseif ($_SESSION['patype']==6 or $_SESSION['patype']==36) {
            $order_info['amount'] = 98;
			if( $_SESSION['patype']==16){
				$order_info['remark'] = "uber好空气98套餐";
			}else{
				$order_info['remark'] = "好空气98套餐";
			}
            $order_info['dikou_amount'] = $kongtiao_price+99-98;
            $order_info['order_type'] = 33;
        }elseif($_SESSION['patype']==14 ) {
			$order_info['amount'] = 368;
			$order_info['remark'] = "uber灰喜力368套餐";
			$order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-368;
			$order_info['order_type'] = 24;

		}elseif ($_SESSION['patype']==15 or $_SESSION['patype']==25 or $_SESSION['patype']==40) {
            if($_SESSION['patype']==15) {
                $order_info['amount'] = 38;
            }else{
                $order_info['amount'] = 68;
            }
            $order_info['remark'] = "节气门清洗套餐";
            $order_info['dikou_amount'] = 61;
            $order_info['order_type'] = 53;
        }elseif ($_SESSION['patype']==1  or $_SESSION['patype']==26 or $_SESSION['patype']==16 or $_SESSION['patype']==41) {
            if($_SESSION['patype']==1 or $_SESSION['patype']==16) {
                $order_info['amount'] = 38;
            }else{
                $order_info['amount'] = 68;
            }
            $order_info['remark'] = "发动机舱精洗";
            $order_info['dikou_amount'] = 61;
            $order_info['order_type'] = 54;
        }elseif ($_SESSION['patype']==20 or $_SESSION['patype']==27 or $_SESSION['patype']==42) {
            if($_SESSION['patype']==20) {
                $order_info['amount'] = 18;
            }else{
                $order_info['amount'] = 38;
            }
            $order_info['remark'] = "空调清洗除菌套餐";
            $order_info['dikou_amount'] = 81;
            $order_info['order_type'] = 66;
        }elseif ($_SESSION['patype']==21 or $_SESSION['patype']==28 or $_SESSION['patype']==43) {
            if($_SESSION['patype']==21) {
                $order_info['amount'] = 18;
            }else{
                $order_info['amount'] = 38;
            }
            $order_info['remark'] = "轮毂清洗套餐";
            $order_info['dikou_amount'] = 81;
            $order_info['order_type'] = 65;
        }elseif ($_SESSION['patype'] == 54) { // 商飞发动机舱精洗
            $order_info['amount'] = 58;
            $order_info['remark'] = "发动机舱精洗";
            $order_info['dikou_amount'] = 99 - $order_info['amount'];
            $order_info['order_type'] = 54;
        }

		if ($_SESSION['patype']==7 or $_SESSION['patype']==22 or $_SESSION['patype']==37 or $_SESSION['patype']==47 or $_SESSION['patype']==60 or $_SESSION['patype']==65 or $_SESSION['patype']==70) {
            $order_info['amount'] = 199;
            $order_info['remark'] = "壳牌黄喜力199套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-199;
            $order_info['order_type'] = 25;	
        }elseif ($_SESSION['patype']==8 or $_SESSION['patype']==23 or $_SESSION['patype']==38 or $_SESSION['patype']==48 or $_SESSION['patype']==61 or $_SESSION['patype']==66 or $_SESSION['patype']==71) {
            $order_info['amount'] = 299;
            $order_info['remark'] = "壳牌蓝喜力299套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-299;
            $order_info['order_type'] = 26;
        }elseif ($_SESSION['patype']==9 or $_SESSION['patype']==24 or $_SESSION['patype']==39 or $_SESSION['patype']==49 or $_SESSION['patype']==62 or $_SESSION['patype']==67 or $_SESSION['patype']==72) {
            $order_info['amount'] = 399;
            $order_info['remark'] = "金美孚399套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$filter_price+99-399;
            $order_info['order_type'] = 27;
        }elseif ($_SESSION['patype']==10 or $_SESSION['patype']==29 or $_SESSION['patype']==44 or $_SESSION['patype']==63 or $_SESSION['patype']==68 or $_SESSION['patype']==73) { // 修改好空气16.8套餐价格为38
            $order_info['amount'] = 38;
            $order_info['remark'] = "好空气38套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-38;
            $order_info['order_type'] = 52;
        }elseif ($_SESSION['patype']==11 or $_SESSION['patype']==30 or $_SESSION['patype']==45 or $_SESSION['patype']==50 or $_SESSION['patype']==64 or $_SESSION['patype']==69 or $_SESSION['patype']==74) {
            $order_info['amount'] = 98;
            $order_info['remark'] = "好空气98套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-98;
            $order_info['order_type'] = 55; 
        }elseif($_SESSION['patype']==31){
            $order_info['amount'] = 365;
            $order_info['remark'] = "金美孚365套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-365;
            $order_info['order_type'] = 27;
        } elseif ($_SESSION['patype'] == 46) { // 成都苏宁好空气38元套餐
            $order_info['amount'] = 38;
            $order_info['remark'] = "好空气38套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-38;
            $order_info['order_type'] = 52;
        } elseif ($_SESSION['patype'] == 75) { // 检测
            $order_info['amount'] = 9.8;
            $order_info['remark'] = "检测套餐";
            $order_info['dikou_amount'] = $oil_1_price+$oil_2_price+$kongtiao_price+99-9.8;
            $order_info['order_type'] = 52;
        }
		
        //业务来源
		if( $_SESSION['patype']==12 or $_SESSION['patype']==13 or $_SESSION['patype']==14 or $_SESSION['patype']==15 or $_SESSION['patype']==16){
			$order_info['business_source'] = 37;
		}elseif( $_SESSION['patype']==17 or $_SESSION['patype']==18 or $_SESSION['patype']==19){ //杭州合作套餐
            $order_info['business_source'] = 46;
        }elseif( $_SESSION['patype']==2 or $_SESSION['patype']==3 or $_SESSION['patype']==4 or $_SESSION['patype']==5 or $_SESSION['patype']==6){ //杭州合作套餐
            $order_info['business_source'] = 34;
        }elseif( $_SESSION['patype']>21 and $_SESSION['patype']<31){ //腾讯汽车
            $order_info['business_source'] = 49;
        }elseif( $_SESSION['patype']>31 and $_SESSION['patype']<37){ //爱代驾
            $order_info['business_source'] = 13;
        }elseif( $_SESSION['patype']>36 and $_SESSION['patype']<46){ //招行
            $order_info['business_source'] = 51;
        }elseif( $_SESSION['patype'] >= 46 and $_SESSION['patype'] <= 50){ // 成都苏宁活动
            $order_info['business_source'] = 52;
        }elseif( $_SESSION['patype'] >= 51 and $_SESSION['patype'] <= 54){ // 商飞
            $order_info['business_source'] = 26;
        }elseif( $_SESSION['patype'] >= 60 and $_SESSION['patype'] <= 64){ // 成都红星美凯龙
            $order_info['business_source'] = 53;
        }elseif( $_SESSION['patype'] >= 65 and $_SESSION['patype'] <= 69){ // 重庆聚满堂
            $order_info['business_source'] = 54;
        }elseif( $_SESSION['patype'] >= 70 and $_SESSION['patype'] <= 75){ // 全民PA
            $order_info['business_source'] = 55;
        }else{
			$order_info['business_source'] = 40 ;
		}


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            //提交成功缓存手机
            $_SESSION['pamobile'] = $order_info['mobile'];
            $update = array('status'=>1);
            $where = array('coupon_code'=>$order_info['replace_code']);
            $res = $this->carservicecode_model->where($where)->save($update);
            //给用户发短信通知
            $send_add_user_data = array(
            'phones'=>$order_info['mobile'],
            'content'=>'您已成功预约携车网“'.$order_info['remark'].'”，稍后会有客服与您联系，确认保养时间和地点，服务完成后支付即可。您也可拨打客服热线4006602822（9:00-17:30）了解详情。',
            );
            $this->curl_sms($send_add_user_data);
            $model_sms = D('sms');
            $send_add_user_data['sendtime'] = time();
            $model_sms->data($send_add_user_data)->add();
        }

        if($oil_detail2){
            $order_info2 = $order_info;
            $order_info2['item'] = $item2;
            $order_info2['amount'] = $item_amount2;
            $order_info2['dikou_amount'] = 99;
            $order_info2['remark'] = '代下单：补订单'.$order_id.'机油';
            $order_info2['order_type'] = 34;
            $order_info2['admin_id'] = 1;
            $res = $this->reservation_order_model->data($order_info2)->add();
            //echo $this->reservation_order_model->getLastsql();exit;
        }

        $sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        //$this->redirect('mobiletmp/order_success', array('order_id', $order_id));
        header("Location:".WEB_ROOT."/mobilecar/order_success_cebbank-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
    }
    
    
    
    
    
    

    //兑换奖品页面
    public function newoil_order(){
    	
        $weixin_id = $_SESSION['wx'];
        
        if(!$weixin_id){
        	$this->error('参数错误');
        }
        
        if (mb_strpos($weixin_id,'**')) {
        	$weixin_id = str_replace('**', '-', $weixin_id);
        }
        
        $type = @$_GET['type'];
        $oneprice = 0;
        if(!$type){
            $this->error('参数错误');
        }else{
            if($type==1){
                $oneprice = 400;
                $onename = '10元打车代金券';
            }else if($type==2){
                $oneprice = 600;
                $onename = '上门汽车安全检测';
            }else if($type==3){
                $oneprice = 1000;
                $onename = '上门汽车保养专业服务';
            }else if($type==4){
                $oneprice = 2500;
                $onename = '府上养车-壳牌黄喜力机油套餐';
            }else if($type==5){
                $oneprice = 4000;
                $onename = '府上养车-壳牌蓝喜力机油套餐';
            }else if($type==6){
                $oneprice = 6000;
                $onename = '府上养车-壳牌灰喜力机油套餐';
            }else if($type==7){
            	$this->redirect('/mobilecar-ranking');
                $oneprice = 10000;
                $onename = '1000元油卡';
                $rankprize = D("rankprize");
                $where['weixin_id'] = $_SESSION['wx'];
                $rerank = $rankprize->where($where)->find();

                if($rerank){
                    $this->redirect('/mobilecar-myrank');
                    //$this->error("每个用户只能兑换一次");
                }
            }else {
                $oneprice = 100000000;
                $onename = '神秘奖品敬请期待';
            }
        }

        $where =  array('weixin_id'=>$weixin_id);
        $mMyoil = D('spreadmyoil');
        $list = $mMyoil->where($where)->find();

        $oil_num = @$list['oil_num'];
        if($oil_num>=$oneprice){
            if($type==7){
                $this->assign('defaultnum',-1);
            }else{
                $this->assign('defaultnum',1);
            }
        }else{
            $this->assign('defaultnum',0);
        }
        $this->assign('thistime',time()*1000);
        if($weixin_id == 'oF49ruFQSgL5TGDy5yEq92skI1EQ' || $weixin_id =='oF49ruFqVpSAKC-UNql7GHucUNXg'){
//             $this->assign('time',5);
            $this->assign('time',1419998400-time());
        }else{
            $this->assign('time',1419998400-time());//2014-12-31 12:00:00
        }
        $this->assign('oneprice',$oneprice);
        $this->assign('onename',$onename);
        $this->assign('type',$type);
        $this->assign('oil_num',$oil_num);
        $this->assign('weixin_id',$weixin_id);
        $this->display('newoil_order');
    }

    
    /*
     * 可兑换奖品页面 
     * auth bright
     */
    public function ech_testimonial(){
    	$weixin_id = !empty($_SESSION['wx'])?$_SESSION['wx']:$_GET['wx'];
    	
    	if (mb_strpos($weixin_id,'**')) {
    		$weixin_id = str_replace('**', '-', $weixin_id);
    	}
    	if (!$weixin_id) {
    		$this->error('参数错误');
    	}
		if(!$_SESSION['can_prize'] or $_SESSION['can_prize']!=$weixin_id){
			$this->error('你无权限兑奖');
		}
    	$_SESSION['wx'] = $weixin_id;
    	$where =  array('weixin_id'=>$weixin_id);
    	$mMyoil = D('spreadmyoil');
    	$list = $mMyoil->field('oil_num')->where($where)->find();
    	$oil_num = @$list['oil_num'];
    	$this->assign('oil_num',$oil_num);
        $this->assign("time",1419998400-time());//2014年12月31日 12:00:00
    	$this->assign('weixin_id',$weixin_id);
        $this->display('ech_testimonial');
    }

    //如何获取
    public function howgain(){
        $this->display('howgain');
    }

    /*
     *动态明细
    * auth bright
    */
    public function oilorderdetail(){
        $weixin_id = $_SESSION['wx'];
        if (!$weixin_id) {
            $this->error('参数错误');
        }
        $where =  array('weixin_id'=>$weixin_id);
        $mMyoil = D('spreadmyoil');
        $list = $mMyoil->where($where)->find();
        $userinfo = array();
        
        if ($list) {
        	//当前剩余的油
        	if ($list['oil_num'] > 4000) {
        		$list['curOilNum'] =$list['oil_num'] % 4000 ;//当前剩余的油量
        		$list['oil_bucket'] = intval($list['oil_num'] / 4000);//已经积满的油桶
        	}else{
        		$list['curOilNum'] =$list['oil_num'];
        	}
        	//查找用户信息
        	$mPadatatest = D('padatatest');
        	$where2 = array('FromUserName'=>$weixin_id);
        	$userinfo = $mPadatatest->field('nickname')->where($where2)->find();
        	$otherDatas = array();
        
        	//查找最新其他人的动态
        	$mSpreadoil = D('spreadoil');
        	$otherDatas = $mSpreadoil->field('nickname,create_time')->where($where)->order('id desc')->select();
        	
        	//查找我的签到记录
        	$sign = D('spreadsign');
        
        	$sign_todays = $sign->field('oil_num,create_time')->where($where)->select();
        	if ($sign_todays) {
        		foreach ($sign_todays as $sign_today){
        			$sign_today['nickname'] = $userinfo['nickname'];
        			$sign_today['tag'] = 'sign';
        			$otherDatas[] = $sign_today;
        		}
        	}
        	//查找我的兑换记录
        	$mSpreadprize = D('spreadprize');
        	$spreadprizes = $mSpreadprize->field('type,create_time,num')->where($where)->select();
        	if ($spreadprizes) {
        		foreach ($spreadprizes as $spreadprize){
        			$spreadprize['tag'] = 'prize';
        			$spreadprize['nickname'] = $userinfo['nickname'];
        			switch ($spreadprize['type']) {
        				case 1:
        					$prizeName = '10元打车代金券';
        					$oil = 400;
        					break;
        				case 2:
        					$prizeName = '上门汽车安全检测';
        					$oil = 600;
        					break;
        				case 3:
        					$prizeName = '上门汽车保养专业服务';
        					$oil = 1000;
        					break;
        				case 4:
        					$prizeName = '府上养车-黄壳机油套餐';
        					$oil = 2500;
        					break;
        				case 5:
        					$prizeName = '府上养车-蓝壳机油套餐';
        					$oil = 4000;
        					break;
        				case 6:
        					$prizeName = '府上养车-灰壳机油套餐';
        					$oil = 6000;
        					break;
        				case 7:
        					$prizeName = '1000元加油卡';
        					$oil = 10000;
        					break;
        			}
        			$spreadprize['prizename'] = $prizeName;
        			$spreadprize['oil'] = $oil*$spreadprize['num'];
        			$otherDatas[] = $spreadprize;
        		}
        	}
        	//按照时间排序
        	usort($otherDatas, function($a,$b){
        		return strcmp($b["create_time"],$a["create_time"]);
        	});
        }
        $this->assign('nickname',$userinfo['nickname']);
        $this->assign('weixin_id',$weixin_id);
        $this->assign('otherDatas',$otherDatas);
        $this->assign('userinfo',$userinfo);
        $this->assign('list', $list);
        $oil_num = @$list['oil_num'];
        $this->assign('oil_num',$oil_num);
        $this->display('oilorderdetail');
    }

    //活动府上养车介绍页
    public function newoildes(){
        $this->display('newoildes');
    }

    //兑换奖品页面
    public function newoil_order_test(){
        echo 'over';exit;
        $old_wx = $weixin_id = $_GET['wx'];//$weixin_id = $_SESSION['wx'];
        
        if(!$weixin_id){
            $this->error('参数错误');
        }
        
        if (mb_strpos($weixin_id,'**')) {
            $weixin_id = str_replace('**', '-', $weixin_id);
        }
        $_SESSION['wx'] = $weixin_id;
        $_SESSION['can_prize'] = $weixin_id;
        $type = @$_GET['type'];
        $oneprice = 0;
        if(!$type){
            $this->error('参数错误');
        }else{
            if($type==1){
                $oneprice = 400;
                $onename = '10元打车代金券';
            }else if($type==2){
                $oneprice = 600;
                $onename = '上门汽车安全检测';
            }else if($type==3){
                $oneprice = 1000;
                $onename = '上门汽车保养专业服务';
            }else if($type==4){
                $oneprice = 2500;
                $onename = '府上养车-壳牌黄喜力机油套餐';
            }else if($type==5){
                $oneprice = 4000;
                $onename = '府上养车-壳牌蓝喜力机油套餐';
            }else if($type==6){
                $oneprice = 6000;
                $onename = '府上养车-壳牌灰喜力机油套餐';
            }else if($type==7){
                $oneprice = 10000;
                $onename = '1000元油卡';
                $rankprize = D("rankprize");
                $where['weixin_id'] = $_SESSION['wx'];
                $rerank = $rankprize->where($where)->find();

                if($rerank){
                    $this->redirect('/mobilecar-myrank');
                    //$this->error("每个用户只能兑换一次");
                }
            }else {
                $oneprice = 100000000;
                $onename = '神秘奖品敬请期待';
            }
        }

        $where =  array('weixin_id'=>$weixin_id);
        $mMyoil = D('spreadmyoil');
        $list = $mMyoil->where($where)->find();

        $oil_num = @$list['oil_num'];
        if($oil_num>=$oneprice){
            if($type==7){
                $this->assign('defaultnum',-1);
            }else{
                $this->assign('defaultnum',1);
            }
        }else{
            $this->assign('defaultnum',0);
        }
        $this->assign('thistime',time()*1000);
        $this->assign('time',1419926700-time());//2014-12-31 12:00:00
        $this->assign('oneprice',$oneprice);
        $this->assign('onename',$onename);
        $this->assign('type',$type);
        $this->assign('oil_num',$oil_num);
        $this->assign('weixin_id',$old_wx);
        $this->display('newoil_order_test');
    }

    //抢购-排名前50
    public function ranking(){
        $rankprize = D("rankprize");
        $mSpreadpic = D('spreadpic');

        $list = $rankprize->limit(50)->order('id')->select();
        //var_dump($list);
        if($list){
            foreach ($list as $key => $value) {
                $weixin_id = @$list[$key]['weixin_id'];
                if($weixin_id){
                    $where = array('FromUserName'=>$weixin_id);
                    $spreadpicData = $mSpreadpic->where($where)->find();
                    $list[$key]['face'] = $spreadpicData['face'];
                    $list[$key]['nickname'] = $spreadpicData['nickname'];
                }  
            }
        }
        //var_dump($list);
        $this->assign("list",$list);
        $this->display('ranking');
    }

    //抢购-我的排名
    public function myrank(){
        $rankprize = D("rankprize");
        $spreadpic = D('spreadpic');

        $data['weixin_id'] = $_SESSION['wx'];
        $where['FromUserName'] = $_SESSION['wx'];
        $myrank = $rankprize->where($data)->find();
        $pic = $spreadpic->where($where)->find();
        if($myrank){
            $this->assign("myrank",$myrank);
        }
        if($pic){
            $this->assign("pic",$pic);
        }
        
        $this->display('myrank');
    }

    //抢购函数
    function rankadd(){
        
        if(!$_POST){
            $this->error("参数错误");
        }
		
        if (!isset($_SESSION['can_prize']) or $_SESSION['can_prize']!=$_SESSION['wx']) {
            $this->error('您无权限兑奖!');
        }

        // if($this->_post('weixin_id')!="oF49ruFQSgL5TGDy5yEq92skI1EQ"){
        //     $this->error('网络异常，请返回重试');
        // }

        if(1419998400>time()){
            $this->error('兑奖暂未开始');
        }
        

        // if(time()>1420000200){
        //     $this->error('兑奖已结束');
        // }

        $spreadmyoil = D("spreadmyoil");
        $spreadprize = D("spreadprize");
        $rankprize = D("rankprize");
        $data['weixin_id'] = $_SESSION['wx'];//*****
        $data['type'] = 7;
        $data['num'] = 1;
        $data['username'] = $_POST['username'];
        $data['mobile'] = $_POST['mobile'];
        $data['address'] = $_POST['address'];
        $data['carmodel'] = $_POST['carmodel'];
        $data['create_time'] = microtime(sec);

        if (!$data['username']) {
            $this->error("用户名不能为空");
        }

        if (!$data['mobile']) {
            $this->error("电话不能为空");
        }

        if (!$data['address']) {
            $this->error("地址不能为空");
        }

        $whereoil = array('weixin_id'=>$data['weixin_id']);
        $resoil = $spreadmyoil->where($whereoil)->find();
        if($resoil){
            if($resoil['oil_num']<10000){
                $this->error("您的机油总量不足");
            }
        }else{
            $this->error("用户不存在");
        }

        $rerank = $rankprize->where($whereoil)->find();

        if($rerank){
            $this->error("每个用户只能兑换一次");
        }else{

            $res = $rankprize->add($data);

            if($res){
                if($res>20){
                    
                }else{
                    $data2['oil_num'] = $resoil['oil_num'] - 10000;
                    $spreadmyoil->where($whereoil)->save($data2);
                }
                $this->success("提交成功,返回查看是否抢购成功",NULL,__APP__."mobilecar-myrank?wx=".$data['weixin_id']);
            }
        }
    }

    public function mycarservice_yz(){
        $yzm = $this->_post('code');
        if(!$yzm){
            $this->error('参数错误');
        }
        if($yzm && $yzm != $_SESSION['pa_code']){
            $this->error('验证码不正确，预约失败');
        }else{
            $_SESSION['pamobile'] = $this->_post('mobile');
            $this->success('验证通过');
        }
    }

    //兑换记录
    public function gift_records(){
        $weixin_id = $_SESSION['wx'];
        if (!$weixin_id) {
            $this->error('参数错误');
        }
        $where =  array('weixin_id'=>$weixin_id);
        $spreadprize = D("spreadprize");
        $list = $spreadprize->where($where)->order("id desc")->select();
        $this->assign("alist",$list);
        
        $this->display('gift_records');
    }
	function jiance(){

		$this->display();

	}

    //兑奖验证
    function exchangego(){

    	if (!isset($_SESSION['can_prize']) or $_SESSION['can_prize']!=$_SESSION['wx']) {
    		$this->error('您无权限兑奖');
    	}

        if (!empty($_POST)) {
			//代注册
			$model_user = D('member');
			$model_memberlog = D('memberlog');
			$model_sms = D('sms');

			$userinfo = $model_user->where(array('mobile'=>$this->_post('mobile'),'status'=>'1'))->find();
			if(!$userinfo){
				$member_data['mobile'] = $this->_post('mobile');
				$member_data['password'] = md5($this->_post('mobile'));
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$member_data['fromstatus'] = '54';//油桶兑奖代注册
				$uid = $model_user->data($member_data)->add();
                /*
				$send_add_user_data = array(
					'phones'=>$data['a1'],
					'content'=>'您已注册成功，您可以使用您的手机号码'.$data['a1'].'，密码'.$data['a1'].'来登录携车网，客服4006602822。',
				);
				$this->curl_sms($send_add_user_data);
                */
                // dingjb 2015-09-29 11:48:19 切换到云通讯
                $send_add_user_data = array(
                    'phones'  => $data['a1'],
                    'content' => array(
                        $data['a1'],
                        $data['a1']
                    )
                );
                $this->curl_sms($send_add_user_data, null, 4, '37653');

				$send_add_user_data['sendtime'] = time();
				$model_sms->data($send_add_user_data)->add();

				$logdata['createtime'] = time();
				$logdata['mobile'] = $this->_post('mobile');
				$logdata['memo'] = '用户注册';
				$model_memberlog->data($logdata)->add();
			}

            $data['weixin_id'] = $this->_post('weixin_id');
            $data['num'] = 1;
            $datanum = $this->_post('num');
            $data['type'] = $this->_post('type');
            $data['username'] = $this->_post('username');
            $data['mobile'] = $this->_post('mobile');
            $data['address'] = $this->_post('address');
            $data['carmodel'] = $this->_post('carmodel');
            $data['create_time'] = time();
            $oneprice = 0;
            $sum = 0;
            $onename='';
            $spreadmyoil = D("spreadmyoil");
            $spreadprize = D("spreadprize");
            if( $data['weixin_id'] ){
                $where = array('weixin_id'=>$data['weixin_id']);
                $res = $spreadmyoil->where($where)->find();
            }else{
                $this->error("数据异常，返回兑奖页面",true);
            }
            if($data['type']){
                if($data['type']==1){
					if ($datanum>2){
					   $this->error("对不起，一个用户只能兑换两张快的打车券",true);
					}
                    $oneprice = 400;
                    $onename = '10元快的打车券';
					$data['exchange_type'] = 2;
					$where2 = array(
							'weixin_id'=>$data['weixin_id'],
							'type'=>1
					);
					
					$count = $spreadprize->where($where2)->count();
					if( ($count + $datanum) >2 ){
						$this->error("对不起，一个用户只能兑换两张快的打车券",true);
					}
					
                }else if($data['type']==2){
                    $oneprice = 600;
                    $onename = '携车网上门汽车安全检测';
                }else if($data['type']==3){
                    $oneprice = 1000;
                    $onename = '携车网上门汽车保养专业服务';
                }else if($data['type']==4){
                    $oneprice = 2500;
                    $onename = '府上养车-黄壳机油套餐';
                }else if($data['type']==5){
                    $oneprice = 4000;
                    $onename = '府上养车-蓝壳机油套餐';
                }else if($data['type']==6){
                    $oneprice = 6000;
                    $onename = '府上养车-灰壳机油套餐';
                }else if($data['type']==7){
					$this->error("很遗憾50张油卡已换完，您可以换其他奖品哦!",true);
                    $oneprice = 10000;
                    $onename = '1000元油卡';
                }else{
                    $this->error("数据异常，返回兑奖页面",true);
                }
            }else{
                $this->error("数据异常，返回兑奖页面",true);
            }
            if($datanum){
                $sum = $oneprice*$datanum;
            }else{
                $this->error("数据异常，返回兑奖页面",true);
            }

            if($res){
                if($sum<=$res['oil_num']){
                    if($data['username']&&$data['mobile']){
                        if($datanum>1){
                            for($i=1;$i<=$datanum;$i++){
                                $res2 = $spreadprize->add($data);
								if($data['type']==1){
									//发券流程开始
									$didi =  M("tp_didi.code2","didi_");

									//取滴滴打车券
									$didi_info = $didi->where(array('orderid'=>'0'))->order('id asc')->find();

									//短信发券给用户
									$sms_post["phones"] = $data['mobile'];
									$sms_content="快的充值码为".$didi_info['dcode']."，面额10一张。请在快的手机客户端充值后使用，激活后有效期14天，请在".$didi_info['yxq']."前激活";
									$sms_post["content"]=$sms_content;
									$this->curl_sms($sms_post,'',1);

									//更新打车券状态
									$data['ordermobile'] = $data['mobile'];
									$data['orderid'] = $res2;
									$didi->where(array('id'=>$didi_info['id']))->save($data);
								}
                            }
                        }else{
                                $res2 = $spreadprize->add($data);
								if($data['type']==1){
									//发券流程开始
									$didi =  M("tp_didi.code2","didi_");

									//取滴滴打车券
									$didi_info = $didi->where(array('orderid'=>'0'))->order('id asc')->find();

									//短信发券给用户
									$sms_post["phones"] = $data['mobile'];
									$sms_content="快的充值码为".$didi_info['dcode']."，面额10一张。请在快的手机客户端充值后使用，激活后有效期14天，请在".$didi_info['yxq']."前激活";
									$sms_post["content"]=$sms_content;
									$this->curl_sms($sms_post,'',1);

									//更新打车券状态
									$data['ordermobile'] = $data['mobile'];
									$data['orderid'] = $res2;
									$didi->where(array('id'=>$didi_info['id']))->save($data);
								}
                        }
                        if($res2){
                            $data2['oil_num'] = $res['oil_num'] - $sum;
                            $spreadmyoil->where($where)->save($data2);
                            
                            if (mb_strpos($_SESSION['wx'],'-')) {
	                            $weixin_id = str_replace('-', '**', $_SESSION['wx']);
                            }else{
                            	$weixin_id = $_SESSION['wx'];
                            }
                            if($data['type']==1){
                                $alcontent = "兑换成功！10元快的打车券已发放至您的手机，请注意查收！";
                            }else{
                                $alcontent = "你已经成功兑换".$onename.",工作人员将会在一个工作日内致电安排奖品发放工作";
                            }
                            $this->success($alcontent,null,__APP__."mobilecar-newoil-wx-".$weixin_id);
                        }else{
                            $this->error("领取失败",true);
                        }                        
                    }else{
                        $this->error("请如实完整填写领奖者信息，以便准确发放奖品",true);
                    }
                }else{
                    $this->error("您的机油总量不足，请返回选择可兑换奖品",true);
                }
            }else{
                $this->error("数据异常，返回兑奖页面",true);
            }

        }else{
            $this->error("非法访问");
        }
    }

	function get_item($style_id){
	    //项目
        $item_set = array();
        if( $style_id ){
        	$condition['model_id'] = $style_id;
        	$style_info = $this->new_carmodel->where($condition)->find();
        	$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

        	if( $set_id_arr ){
        		foreach( $set_id_arr as $k=>$v){
        			if(is_array($v)){
        				foreach( $v as $_k=>$_v){
        					$item_condition['id'] = $_v;
                            $item_condition['name'] = array('notlike','%pm2%');
        					$item_info_res = $this->new_filter->where($item_condition)->find();
        					$item_info['id'] = $item_info_res['id'];
        				    	//$item_info['name'] =  mb_substr( $item_info_res['name'], 0 ,mb_strpos($item_info_res['name'],' ') );
        					
                             //去掉品牌后面的规格 by bright
                            $item_info_res['name'] = $this->getBrandName($item_info_res['name']);
                            
                            
        					$item_info['name'] = $item_info_res['name'];
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
		return $item_set;
	}
        
        
         //AJAX阻止同一时间点过分下单
        function prevent(){
            $order_time = strtotime($_POST['order_time']) + ($_POST['order_time2'] + 8) * 3600;
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
        
        
    //扫码创建订单  wql@20150610    
    public function code_create_order() {
        //调用时，传递两个参数,order_id和order_type 
	//获取真实id
	$true_id = $this->get_true_orderid($_REQUEST['order_id']);	
        $map['id'] = intval(trim($true_id)) ; 
        $order_type = intval(trim($_REQUEST['order_type'])) ;
        //查询订单信息
        $orderArr = $this->reservation_order_model->where($map)->find();
		if(!$orderArr){
			$list['state'] ='fail';
			$list['content'] ='订单不存在';
			$list['info'] =null;
			$result = json_encode($list);
			echo $result;
			exit;
		}
        //计算折扣,生成remark,等信息
        $adddata = $this->get_upinfo($order_type,$orderArr);
        
        //删除需要更新的字段
        $delArr = array('id','amount','dikou_amount','remark','item','order_type','replace_code','pay_type','pay_status','create_time','origin','operator_remark','admin_id');
        foreach($delArr as $k => $v){
            unset($orderArr[$v]);
        }
        $orderArr['business_source'] = 41;//新来源：41.作业拉单
        //合并两个数组,生成插入数组
        $data = array_merge($orderArr,$adddata);
        
        //入库之前检查是否重复下单
        $cond['mobile'] = $data['mobile']  ;
        $cond['order_type'] = $data['order_type']  ;
        $cond['status'] = array('neq',8)  ;
        $results =  $this->reservation_order_model->where($cond)->find() ;
        if(is_array($results)){
            $this->success('您已有相同类型的订单，不必重复下单！', 'mycarservice?uid='.$data['uid']);
        }else{
            //执行插入
            $rs = $this->reservation_order_model->add($data); 
            //提示和跳转
            if($rs){
                $this->success('扫码下单成功', 'mycarservice?uid='.$data['uid']);
            }else{
                $this->error('扫码下单失败，请稍后重试!','mycarservice?uid='.$data['uid']);
            }    
        }
        
    }
    
    //根据订单类型获取折扣和备注,以及item等信息 wql@20150610
    function get_upinfo($order_type,$orderArr) {
        if($order_type==4){ //一元检测套餐
            $data['amount'] = 9.80  ;
            $data['dikou_amount'] = 99 - $data['amount']  ;
            $data['remark'] = '抵扣套餐价格￥'. $data['dikou_amount'].';一元检测套餐' ;    
        }elseif($order_type==15){ //节气门清洗套餐
            $data['amount'] = 68.00  ;
            $data['dikou_amount'] = 99 - $data['amount'] ;
            $data['remark'] = '抵扣套餐价格￥'.$data['dikou_amount'].';好动力套餐:节气门清洗';  
        }elseif($order_type==54){ //发动机舱精洗套餐
            $data['amount'] = 68.00  ;
            $data['dikou_amount'] = 99 - $data['amount'] ;
            $data['remark'] = '抵扣套餐价格￥'.$data['dikou_amount'].';发动机舱精洗套餐';     
        }elseif($order_type==62){
			$data['amount'] = 38.00  ;
			$data['dikou_amount'] = 99 - $data['amount'] ;
			$data['remark'] = '抵扣套餐价格￥'.$data['dikou_amount'].';汽车空调清洗';
		}elseif($order_type==65){
			$data['amount'] = 38.00  ;
			$data['dikou_amount'] = 99 - $data['amount'] ;
			$data['remark'] = '抵扣套餐价格￥'.$data['dikou_amount'].';汽车轮毂清洗';
		}
        //生成item 
        $item_content = array(
            'oil_id'     =>0,
            'oil_detail' => array(),
            'filter_id'  =>0,
            'kongqi_id'  =>0,
            'kongtiao_id' =>0,
            'price'=>array(
                'oil'=>array(),
                'filter'=>array(),
                'kongqi'=>array(),
                'kongtiao'=> array()
            )
        );
        
        $data['item'] = serialize($item_content);
        //追加其他信息
        $data['order_type'] = $order_type ;
        $data['origin'] = 6;
        $data['pay_type'] = 1;
        $data['pay_status'] = 0;
        $data['status'] = 2;
        $data['create_time'] = time();
        
        return   $data ;    
    }
    
    
    //微信菜单下三种套餐入口
    public  function carservice_wx(){
        //echo  $this->get_orderid(4);
        //echo '<br>' ;
       // echo  $this->get_orderid(15);
        // echo '<br>' ;
        //echo  $this->get_orderid(54);
        //进入套餐选择页面
        $this->display();   
    }
    
	//下单页面
    public function carservice_wx_order() {
        $city_model = D('city');
        $map['is_show'] = 1;
        $city_info = $city_model->where($map)->select();
        $this->assign('city_info',$city_info);
		
	    if($_SESSION['ordertype']==70){ //一元检测套餐
            $amount = 9.8  ;
        }elseif($_SESSION['ordertype']==15){ //节气门清洗套餐
            $amount = 68.00  ;
        }elseif($_SESSION['ordertype']==54){ //发动机舱精洗套餐
            $amount = 68.00  ; 
        }elseif($_SESSION['ordertype']==62){ //空调清洗套餐
            $amount = 38.00  ;
        }elseif($_SESSION['ordertype']==65){ //轮毂清洗套餐
            $amount = 38.00  ;
        }elseif($_SESSION['ordertype']==73){ //发动机除碳(后付费)
            $amount = 98.00  ;
        }

        $fromMyCoupon = $_SESSION['from_my_coupon'];
        if ($fromMyCoupon) {
            $amount = $this->newCouponModel->getUserCouponAmount($_SESSION['new_coupon_id']);
        }

        $_SESSION['amount'] = $amount;
	    $this->assign('amount',$amount);
        $this->assign('ordertype',$_SESSION['ordertype']);
        $this->assign('fromMyCoupon', $fromMyCoupon);

        $this->display();   
    }
	
	//创建订单，入库。
    public function  wx_create_order(){
	session_start();
        $order_info['city_id'] = $this->_post('city_id');
        if($_SESSION['ordertype']==73 and $order_info['city_id']!='1'){
            $this->error('对不起,除碳服务目前仅限上海地区');
        }
        $userinfo = $this->user_model->where(array('mobile'=>$this->_post('mobile'),'status'=>'1'))->find();
        if($userinfo){
        	$_SESSION['uid'] = $userinfo['uid'];
        }else{
        	$member_data['mobile'] = $this->_post('mobile');
        	$member_data['password'] = md5($this->_post('mobile'));
        	$member_data['reg_time'] = time();
        	$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
        	$member_data['fromstatus'] = '50';//上门保养
        	$_SESSION['uid'] = $this->user_model->data($member_data)->add();
            /*
        	$send_add_user_data = array(
        			'phones'  => $this->_post('mobile'),
        			'content' => '您已注册成功，您可以使用您的手机号码'.$this->_post('mobile').'，密码'.$this->_post('mobile').'来登录携车网，客服4006602822。',
        	);
        	$this->curl_sms($send_add_user_data);
            */
            // dingjb 2015-09-29 11:51:03 切换到云通讯
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
        

        $yzm = $this->_post('code');
        if($yzm && $yzm != $_SESSION['pa_code']){
            $this->error('验证码不正确，预约失败');
            exit;
        }
        

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
        $order_info['order_time'] = $this->_post('order_time');
        $order_time2 = intval($this->_post('order_time2'));
        $order_info['order_time'] = strtotime($order_info['order_time']) + ($order_time2 + 8) * 3600;

        $order_info['create_time'] = time();
        $order_info['remark'] = $this->_post('remark');
        $order_info['city_id'] = $this->_post('city_id');
        $order_info['order_type'] = $_SESSION['ordertype']; 

        $order_info['amount'] = $_SESSION['amount'];
        
        if ($order_info['replace_code'] = $this->_post('replace_code')) {//抵扣劵存在
            if ($this->newCouponCodeModel->isValidNewCouponCode($order_info['replace_code'], $_SESSION['ordertype'])) {//新版优惠券基础服务下单时判断有效性
                $hasNewCoupon = true;
                $value = $this->newCouponCodeModel->getDiscountAmount($order_info['replace_code']);
                $order_info['amount'] = $order_info['amount'] - $value;
                $order_info['dikou_amount'] = 99 - $order_info['amount'];
                $order_info['remark'] = $order_info['remark'].'新版优惠券，输入优惠码下单';
            } else {
                //计算折扣
                $first = substr($order_info['replace_code'],0,1);

                if($_SESSION['ordertype']==70){ //一元检测套餐
                    if($first=='d'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ;  
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';细节养护与检测' ; 
                    $msg = '细节养护与检测' ;
                }elseif($_SESSION['ordertype']==15){ //节气门清洗套餐
                    if($first=='d'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ; 
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';节气门清洗套餐' ;
                    $msg = '节气门清洗套餐' ;
                }elseif($_SESSION['ordertype']==54){ //发动机舱精洗套餐
                    if($first=='b' or $first=='d' or $first=='u'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ; 
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';发动机舱精洗套餐' ;
                    $msg = '发动机舱精洗套餐' ;
                }elseif($_SESSION['ordertype']==62){ //空调清洗套餐
                    if($first=='e' or $first=='d'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ;
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';空调清洗套餐' ;
                    $msg = '空调清洗套餐' ;
                }elseif($_SESSION['ordertype']==65){ //轮毂清洗套餐
                    if($first=='d'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ;
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';轮毂清洗套餐' ;
                    $msg = '轮毂清洗套餐' ;
                }elseif($_SESSION['ordertype']==73){ //发动机除碳
                    if($first=='d'){
                        $order_info['amount']=0;
                    }
                    $order_info['dikou_amount'] = 99 - $order_info['amount'] ;
                    $order_info['remark'] = '微信下单:抵扣套餐价格￥'. $order_info['dikou_amount'].';发动机除碳' ;
                    $msg = '发动机除碳' ;
                }
            }
        } else {// 我的优惠券直接下单
            $hasNewCoupon = true;
            $order_info['replace_code'] = $_SESSION['new_coupon_code'];
            $order_info['dikou_amount'] = 99 - $order_info['amount'];
            $order_info['remark'] = $order_info['remark'].'新版优惠券，微信我的优惠券下单';
        }
		if($_SESSION['business_source']==52){
			$order_info['business_source'] = $_SESSION['business_source'];
		}
        //删除$_SESSION['ordertype'] ，避免先下三种套餐在下上门保养出现的问题。
        unset($_SESSION['ordertype'],$_SESSION['business_source']);

        $item_content = array(
            'oil_id'     =>0,
            'oil_detail' => array(),
            'filter_id'  =>0,
            'kongqi_id'  =>0,
            'kongtiao_id' =>0,
            'price'=>array(
                'oil'=>array(),
                'filter'=>array(),
                'kongqi'=>array(),
                'kongtiao'=> array()
            )
        );
         
        $order_info['item'] = serialize($item_content);
        //$order_info['pa_id'] = isset($_SESSION['pa_id'])?$_SESSION['pa_id']:'';
        $order_info['pa_id'] = $_SESSION['pa_id'];

        //支付宝服务窗下单
        if($_SESSION['ali_id']){
            $ali_id = $_SESSION['ali_id'];

            $mAlipay = D('alipay_fuwuchuang');
            $resAli = $mAlipay->where(array('FromUserId' => $ali_id,'status'=> 2 ))->find();

            if($resAli){
                $order_info['ali_id'] = $resAli['id'];
            }
            $order_info['origin'] = 5;
        }else{
            $order_info['origin'] = 2;
        }


        //print_r($order_info);exit;
        $order_id = $this->reservation_order_model->data($order_info)->add();
        if($order_id){
            if ($hasNewCoupon) {//新优惠券使用处理
                $this->newCouponCodeModel->useNewCoupon($order_info['replace_code']);
            } else {
                $update = array('status'=>1);
                $where = array('coupon_code'=>$order_info['replace_code']);
                $res = $this->carservicecode_model->where($where)->save($update);
            }

            if($order_info['ali_id']){
                $Aliurl = WEB_ROOT.'/alipay_fuwuchuang/ali_test.php';
                $Alipost = array(
                    "title" => "下单成功",
                    "desc" => "您的订单号为：".$order_id."",
                    "url" => WEB_ROOT."/mobilecar-mycarservice_detail?order_id=".$order_id,
                    "imageUrl" => WEB_ROOT."/Public_new/images/index/logo.png",
                    "authType" => "loginAuth",
                    "toUserId" => $_SESSION['ali_id']
                );
                $data = $this->ali_curl($Aliurl,$Alipost);
            }
        }
        //重复使用码
        if($order_info['replace_code']=='d2762600087'){
            $data['use_count'] = array('exp','use_count+1');
            $data['status'] = 0;
            $this->carservicecode_model->where(array('coupon_code'=>$order_info['replace_code']))->save($data);
        }
        
        //$sql = $this->reservation_order_model->getLastsql();
        if($order_id){
            //发短信通知用户
            $send_add_order_data = array(
                    'phones'=>$order_info['mobile'],
                    'content'=>"您预约".date('m',strtotime($this->_post('order_time'))).'月'.date('d',strtotime($this->_post('order_time'))).'日'.($order_time2 + 8)."时的".$msg."上门保养服务,我们客服将于2小时内联系您确认订单(工作时间9-18点)。4006602822",
            );
            $this->curl_sms($send_add_order_data,'',1);  //Todo 内外暂不发短信
            $send_add_order_data['sendtime'] = time();
            $this->sms_model->data($send_add_order_data)->add();

            //插入日志
            $this->addCodeLog('下单成功', $sql, $order_id);
        }else{
            //插入日志
            $this->addCodeLog('下单失败', $sql);
        }
        //echo $this->reservation_order_model->getLastsql();exit;
        $this->submitCodeLog('流程结束');
        //插入数据到我的车辆
        $this->_insert_membercar($order_info);

        
        if($order_info['pay_type'] ==3){
            $order_id = "m".$order_id;
            //$item_amount = 0.01;
            $arr='ORDERID='.$order_id.'&PAYMENT='.$order_info['amount'];
            $this->addCodeLog('订单保养',$arr);
            $url = WEB_ROOT.'/ccb_pay/merchant.php?'.$arr;
            header("Location:$url");
		} elseif ($order_info['pay_type'] == 5) {//浦发银行支付
			$url = WEB_ROOT.'/pufa_pay/pay.php?order_id='.$order_id;
			header("Location:$url");
        } else {
                header("Location:".WEB_ROOT."/mobilecar/order_success-order_id-{$order_id}-pay_type-{$order_info['pay_type']}");
        }
				
    }
	//金数据活动页
	function jinshuju(){
		$this->display();
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
    
    
    //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150821
    public function getNeedModel($orderCreateTime) {
        $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
        if($orderCreateTime <= $new_filter_time){
            $this->car_brand_model = D('carbrand');  //车品牌
            $this->car_model_model = D('carmodel');  //车型号
            $this->car_style_model = D('carseries');  //车系
            $this->item_oil_model = D('item_oil');  //保养机油
            $this->item_model = D('item_filter');  //保养项目
        }else{
            $this->car_brand_model = D('new_carbrand');  //车品牌
            $this->car_model_model = D('new_carmodel');  //车型号
            $this->car_style_model = D('new_carseries');  //车系
            $this->item_oil_model = D('new_item_oil');  //保养机油
            $this->item_model = D('new_item_filter');  //保养项目
        }
        
    }
    
             
}
