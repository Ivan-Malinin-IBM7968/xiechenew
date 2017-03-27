<?php
//首页
class BidsourceAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		
        $this->car_brand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->car_model_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->car_style_model = M('tp_xieche.car_style','xc_');  //车系号

        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        
        $this->PadataModel = M('tp_xieche.padatatest', 'xc_');//接收微信订单数据表
        $this->PaweixinModel = M('tp_xieche.paweixin', 'xc_');//携车手机微信比对表
        $this->user_model = M('tp_xieche.member', 'xc_');//用户表
		$this->insurancecompany_model = M('tp_xieche.insurancecompany', 'xc_');//用户表
		$this->ShopModel = D('shop');//店铺表
		$this->sms_model = D('sms');//短信表
		$this->memberlog_model = D('memberlog');
		
        $this->bidsource_model = M('tp_xieche.bidsource','xc_');  //车源信息表
        $this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段
	}

	/**
	 * 车型
	 */
	public function ajax_car_model(){
		$brand_id = intval($_POST['brand_id']);
		if( $brand_id ){
			$condition['brand_id'] = $brand_id;
			//$car_model_list = $this->car_model_model->where( $condition )->select();
			$car_model_list = $this->carseries_model->where( $condition )->select();
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
			//$condition['model_id'] = $model_id;
			//$car_style_list = $this->car_style_model->where( $condition )->select();
			$condition['series_id'] = $model_id;
			$car_style_list = $this->carmodel_model->where( $condition )->select();
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
     * 上门保养预约订单列表
     * @date 2014/10/10
     */
	public function index() {

		//排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $this->bidsource_model->getPk();
           // echo $order;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

		//数据转换
        if (method_exists($this, '_get_order_sort')) {
            $order_sort_arr = $this->_get_order_sort();
			print_r($order_sort_arr);
            $order = $order_sort_arr['order'];
            $sort = $order_sort_arr['sort'];
        }

        //搜索
        if($_REQUEST['id']){
            $map['id'] = $_REQUEST['id'];
        }
        if($_REQUEST['source']){
            $map['source'] = $_REQUEST['source'];
        }
        if($_REQUEST['is_out']){
            $map['is_out'] = $_REQUEST['is_out'];
        }

		// 计算总数
        $count = $this->bidsource_model->where($map)->count();
		// 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		//if(!$_REQUEST['p']){
			//$p->parameter = "index/".$p->parameter;
		//}
        
        // 分页显示输出
        $page = $p->show_admin();

        // 当前页数据查询
        $list = $this->bidsource_model->where($map)->order($order . ' ' . $sort)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->bidsource_model->getLastsql();
        if(is_array($list)){
            foreach ($list as $key => $value) {
				$sourece = array('','定损点','地推','其他');
				$list[$key]['source'] = $sourece[$value['source']];
				if($value['customer_id']=='182'){ $list[$key]['customer_id']='张丹红'; }
				if($value['customer_id']=='234'){ $list[$key]['customer_id']='张美婷'; }
            }
        }

		//列表排序显示
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'asc' ? '升序排列' : '倒序排列'; //排序提示
        $sort = $sort == 'asc' ? 0 : 1; //排序方式

        $this->assign('data', $map);
		$this->assign('status', $status);
		$this->assign('sort', $sort);
        $this->assign('order', $order);
		$this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
        $this->assign('list', $list);
		$this->assign('page', $page);
        $this->assign('technician_list', $technician_list);

		$this->display();
	}

    /**
     * 添加车源信息页
     * @date 2014/10/11
     */
    public function add() {
        $id = intval($_GET['id']);
        $order_param['id'] = $id;

        $order_info = $this->bidsource_model->where($order_param)->find();
        //echo $this->reservation_order_model->getLastsql();
		$model_id = $order_info['model_id'];
		$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
		$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
		$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
		$order_info['car_name'] = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];

		$order_info['order_date'] = date('Y-m-d',$order_info['order_time']);
		$order_info['order_hours'] = date('H',$order_info['order_time']);
		$order_info['order_minutes'] = date('i',$order_info['order_time']);


		//获取车型
		$brand_list = $this->car_brand_model->select();
		//获取保险公司
		$insurance_name = $this->insurancecompany_model->where()->select();
		$shop_list = $this->ShopModel->select();

		$this->assign("insurance_name",$insurance_name);
        $this->assign('shop_list',$shop_list);
        $this->assign('brand_list',$brand_list);
        $this->assign("item_list", $item_list);

        $this->assign('id', $id);
        $this->assign('order_info',$order_info);

        $this->display();
    }

	public function doadd(){
		//print_r($_REQUEST);exit;

		//手机10日内去重
		if($_POST['mobile']){
			$map['mobile'] = $_POST['mobile'];
			$map['create_time'] = array('gt',time()-10*86400);
			$bidsource_info = $this->bidsource_model->where($map)->find();
			echo $this->bidsource_model->getLastsql();
			if($bidsource_info){
				$this->error('10日内重复手机号！',U('/Store/Bidsource/add'));
			}
		}else{
			$this->error('必须录入车主手机号！',U('/Store/Bidsource/add'));
		}
		if($_POST['is_reg']==1){
			$userinfo = $this->user_model->where(array('mobile'=>$this->_post('mobile'),'status'=>'1'))->find();
            if($userinfo){
                $data['uid'] = $userinfo['uid'];
            }else{
                $member_data['mobile'] = $this->_post('mobile');
                $member_data['password'] = md5($this->_post('mobile'));
                $member_data['reg_time'] = time();
                $member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
                $member_data['fromstatus'] = '50';//上门宝洋
                $data['uid'] = $this->user_model->data($member_data)->add();
                $send_add_user_data = array(
                    'phones'=>$this->_post('mobile'),
                    'content'=>'您已注册成功，您可以使用您的手机号码'.$this->_post('mobile').'，密码'.$this->_post('mobile').'来登录携车网，客服4006602822。',
                );
                $this->curl_sms($send_add_user_data);  //Todo 内外暂不发短信
                $send_add_user_data['sendtime'] = time();
                $this->sms_model->data($send_add_user_data)->add();
                
                $data['createtime'] = time();
                $data['mobile'] = $this->_post('mobile');
                $data['memo'] = '用户注册';
                $this->memberlog_model->data($data)->add();
            }
		}

        $data['truename'] = $_POST['truename'];
        $data['mobile'] = $_POST['mobile'];
		$data['carmodel'] = $_POST['carmodel'];
		$data['loss_price'] = $_POST['loss_price'];
		$data['loss_man'] = $_POST['loss_man'];
		$data['insurance_name'] = $_POST['insurance_name'];
		$data['source'] = $_POST['source'];
		//不选择商铺和到店时间无法转订单
		if($_POST['shop_id'] and $_REQUEST['tostore_time']){
		}else{
			$_POST['is_transfer'] = 1;
		}
		$data['is_transfer'] = $_POST['is_out'];
		$data['is_transfer'] = $_POST['is_transfer'];
		$data['shop_id'] = $_POST['shop_id'];
		$data['tostore_time'] = strtotime(substr($_REQUEST['tostore_time'],0,11).' '.$_REQUEST['tostore_hours'].":".$_REQUEST['tostore_minutes']);
        $data['create_time'] = time();
        $data['accident_time'] = strtotime($_POST['accident_time']);
        $data['remark'] = $_POST['remark'];
		$data['customer_id'] = $_POST['customer_id'];
		$data['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
		//print_r($data);exit;

        $id = $this->bidsource_model->add($data);
		//print_r($_POST);exit;
		if($_POST['shop_id'] and $_POST['tostore_time'] and $_POST['is_transfer']==2){
			//插入订单
			$bidorder_model = D("Bidorder");
			//$data['insurance_id'] = $insurance_id;
			$data2['shop_id'] = $_REQUEST['shop_id'];
			$data2['uid'] = $data['uid'];
			$data2['truename'] = $_POST['truename'];
			$data2['mobile'] = $_POST['mobile'];
			$data2['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
			//$data2['bid_id'] = $shopbidding_id;
			//$data2['fav_id'] = $fav_id;
			$data2['order_status'] = 1;//代下单 ---订单直接为已确认
			$data2['status'] = 1;
			$data2['create_time'] = time();
			$data2['tostore_time'] = strtotime(substr($_REQUEST['tostore_time'],0,11).' '.$_REQUEST['tostore_hours'].":".$_REQUEST['tostore_minutes']);
			//$data2['takecar_time'] = $_REQUEST['tostore_time']+($shop_bidding_data['servicing_time']*86400);
			$shopinfos = $this->ShopModel->where($shop_map)->find($_REQUEST['shop_id']);
			//echo $this->ShopModel->getlastsql();
			if($shopinfos['insurance_rebate']>0){
				$data2['insurance_rebate'] = $shopinfos['insurance_rebate'];
			}else{
				$data2['insurance_rebate'] = '5';
			}
			$data2['is_out'] = $_POST['is_out'];
			$data2['customer_id'] = $_POST['customer_id'];
			$bidorder_id = $bidorder_model->add($data2);
			//echo $bidorder_model->getlastsql();exit;

			/*给4s店事故专员发送短信---------------*/
			if ($shopinfos['shop_salemobile']) {
			$send_sms_data = array(
			'phones'=>$shopinfos['shop_salemobile'],
			'content'=>$_REQUEST['sale_sms_content'],
			);
			$this->curl_sms($send_sms_data);

			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
			}

			//给用户发送短信---------------
			$send_sms_data = array(
			'phones'=>$_POST['mobile'],
			'content'=>$_REQUEST['member_sms_content'],
			);
			$this->curl_sms($send_sms_data);

			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
		}
		if($id and !$bidorder_id){
			$this->success('车源添加成功！',U('/Store/Bidsource/'));
		}
		if($id and $bidorder_id){
			$this->success('车源,订单添加成功！',U('/Store/Bidsource/'));
		}
    }
    //批量导入车源信息
    public function import() {
    	$this->display();
    }

	public function doimport() {
		if ($_FILES['bidsource']['name']){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload = $this->_upload_init($upload);
			if (!$upload->upload()) {
				$this->error($upload->getErrorMsg());
			} else {
				$uploadList = $upload->getUploadFileInfo();
			}
			//print_r($uploadList);
			$file = fopen(WEB_ROOT.'UPLOADS/Bidsource/'.$uploadList[0]['savename'],'r');
			while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
				//print_r($data); //此为一个数组，要获得每一个数据，访问数组下标即可
				//事故车时间，姓名，电话，定损点来源，来源方式，分配客服必填
				//print_r($data);exit;
				if(!$data['0'] or !$data['1'] or !$data['4'] or !$data['6'] or !$data['7'] or !$data['8'] ){
					echo '数据不全无法生成';
				}else{
					//10日内去重
					$map['mobile'] = $data['4'];
					$map['create_time'] = array('gt',time()-10*86400);
					$bidsource_info = $this->bidsource_model->where($map)->find();
					if(!$bidsource_info){
							$data['accident_time'] = strtotime($data['0']);
							$data['truename'] = $data['1'];
							$data['carmodel'] = $data['2'];
							$data['licenseplate'] = $data['3'];
							$data['mobile'] = $data['4'];
							$data['insurance_name'] = $data['5'];
							$data['loss_man'] = $data['6'];
							$data['7'] = $data['7'];
							if($data['7']=='定损点'){ $data['source']=1; }
							if($data['7']=='地推'){ $data['source']=2; }
							if($data['7']=='其他'){ $data['source']=3; }
							$data['customer_id'] = $data['8'];
							$data['create_time'] = time();
							//print_r($data);exit;
							$id = $this->bidsource_model->add($data);
							echo '生成车源编号：'.$id.'</br>';
					}else{
						echo '手机号码：'.$data['1'].'在10日内已下单</br>';
					}
				}
			}
			fclose($file);
		}else{
			$this->error('请选择您要导入的CSV文件！',U('/Store/Bidsource/import'));
		}
    }

	/*
		@author:ysh
		@function:图片上传的初始化
		@time:2013/6/22
	*/
	public function _upload_init($upload) {
		//设置上传文件大小
		$upload->maxSize = C('UPLOAD_MAX_SIZE');
		//设置上传文件类型
		$upload->allowExts = explode(',', 'csv');
		//设置附件上传目录
		$upload->savePath = C('UPLOAD_ROOT') . '/Bidsource/';
		$upload->saveRule = 'uniqid';

		$upload->uploadReplace = false;
		//$this->watermark = 1;水印
		return $upload;
	}

    /**
     * 添加车源信息页
     * @date 2014/10/11
     */
    public function edit() {
        $id = intval($_GET['id']);
        $order_param['id'] = $id;

        $order_info = $this->bidsource_model->where($order_param)->find();
        //echo $this->reservation_order_model->getLastsql();
		$model_id = $order_info['model_id'];
		$model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where( array('model_id'=>$model_id))->find();
		$series_res = $this->carseries_model->field('series_name,brand_id')->where( array('series_id'=>$model_res['series_id']))->find();
		$brand_res = $this->carbrand_model->field('brand_name')->where( array('brand_id'=>$series_res['brand_id']))->find();
		$order_info['car_name'] = $brand_res['brand_name'].$series_res['series_name'].$model_res['model_name'];

		$order_info['order_date'] = date('Y-m-d',$order_info['order_time']);
		$order_info['order_hours'] = date('H',$order_info['order_time']);
		$order_info['order_minutes'] = date('i',$order_info['order_time']);
		$order_info['accident_time'] = date('Y-m-d',$order_info['accident_time']);


		//获取车型
		$brand_list = $this->car_brand_model->select();
		//获取保险公司
		$insurance_name = $this->insurancecompany_model->where()->select();
		$shop_list = $this->ShopModel->select();

		$this->assign("insurance_name",$insurance_name);
        $this->assign('shop_list',$shop_list);
        $this->assign('brand_list',$brand_list);
        $this->assign("item_list", $item_list);

        $this->assign('id', $id);
        $this->assign('order_info',$order_info);

        $this->display();
    }

	public function doedit(){
		//print_r($_REQUEST);exit;
		//print_r($_REQUEST);exit;

		//手机10日内去重
		if($_POST['mobile']){
			$map['mobile'] = $_POST['mobile'];
			$map['create_time'] = array('gt',time()-10*86400);
			$bidsource_info = $this->bidsource_model->where($map)->find();
			echo $this->bidsource_model->getLastsql();
			if($bidsource_info){
				$this->error('10日内重复手机号！',U('/Store/Bidsource/add'));
			}
		}else{
			$this->error('必须录入车主手机号！',U('/Store/Bidsource/add'));
		}
		if($_POST['is_reg']==1){
			$userinfo = $this->user_model->where(array('mobile'=>$this->_post('mobile'),'status'=>'1'))->find();
            if($userinfo){
                $data['uid'] = $userinfo['uid'];
            }else{
                $member_data['mobile'] = $this->_post('mobile');
                $member_data['password'] = md5($this->_post('mobile'));
                $member_data['reg_time'] = time();
                $member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
                $member_data['fromstatus'] = '50';//上门宝洋
                $data['uid'] = $this->user_model->data($member_data)->add();
                $send_add_user_data = array(
                    'phones'=>$this->_post('mobile'),
                    'content'=>'您已注册成功，您可以使用您的手机号码'.$this->_post('mobile').'，密码'.$this->_post('mobile').'来登录携车网，客服4006602822。',
                );
                $this->curl_sms($send_add_user_data);  //Todo 内外暂不发短信
                $send_add_user_data['sendtime'] = time();
                $this->sms_model->data($send_add_user_data)->add();
                
                $data['createtime'] = time();
                $data['mobile'] = $this->_post('mobile');
                $data['memo'] = '用户注册';
                $this->memberlog_model->data($data)->add();
            }
		}

        $data['truename'] = $_POST['truename'];
        $data['mobile'] = $_POST['mobile'];
		$data['carmodel'] = $_POST['carmodel'];
		$data['loss_price'] = $_POST['loss_price'];
		$data['loss_man'] = $_POST['loss_man'];
		$data['insurance_name'] = $_POST['insurance_name'];
		$data['source'] = $_POST['source'];
		//不选择商铺和到店时间无法转订单
		if($_POST['shop_id'] and $_REQUEST['tostore_time']){
		}else{
			$_POST['is_transfer'] = 1;
		}
		$data['is_transfer'] = $_POST['is_transfer'];
		$data['shop_id'] = $_POST['shop_id'];
		$data['tostore_time'] = strtotime(substr($_REQUEST['tostore_time'],0,11).' '.$_REQUEST['tostore_hours'].":".$_REQUEST['tostore_minutes']);
        $data['create_time'] = time();
        $data['accident_time'] = strtotime($_POST['accident_time']);
        $data['remark'] = $_POST['remark'];
		$data['customer_id'] = $_POST['customer_id'];
		$data['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
		//print_r($data);exit;
		$map['id'] = $_POST['id'];
        $id = $this->bidsource_model->where($map)->save($data);

		if($_POST['shop_id'] and $_POST['tostore_time'] and $_POST['is_transfer']==2){
			//插入订单
			$bidorder_model = D("Bidorder");
			//$data['insurance_id'] = $insurance_id;
			$data2['shop_id'] = $_REQUEST['shop_id'];
			$data2['uid'] = $data['uid'];
			$data2['truename'] = $_POST['truename'];
			$data2['mobile'] = $_POST['mobile'];
			$data2['licenseplate'] = $this->_post('licenseplate_type').$this->_post('licenseplate');
			//$data2['bid_id'] = $shopbidding_id;
			//$data2['fav_id'] = $fav_id;
			$data2['order_status'] = 1;//代下单 ---订单直接为已确认
			$data2['status'] = 1;
			$data2['create_time'] = time();
			$data2['tostore_time'] = strtotime(substr($_REQUEST['tostore_time'],0,11).' '.$_REQUEST['tostore_hours'].":".$_REQUEST['tostore_minutes']);
			//$data2['takecar_time'] = $_REQUEST['tostore_time']+($shop_bidding_data['servicing_time']*86400);
			$shopinfos = $this->ShopModel->where($shop_map)->find($_REQUEST['shop_id']);
			//echo $this->ShopModel->getlastsql();
			if($shopinfos['insurance_rebate']>0){
				$data2['insurance_rebate'] = $shopinfos['insurance_rebate'];
			}else{
				$data2['insurance_rebate'] = '5';
			}
			$data2['is_out'] = $_POST['is_out'];
			$data2['customer_id'] = $_POST['customer_id'];
			$bidorder_id = $bidorder_model->add($data2);
			//echo $bidorder_model->getlastsql();exit;

			/*给4s店事故专员发送短信---------------*/
			if ($shopinfos['shop_salemobile']) {
			$send_sms_data = array(
			'phones'=>$shopinfos['shop_salemobile'],
			'content'=>$_REQUEST['sale_sms_content'],
			);
			$this->curl_sms($send_sms_data);

			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
			}

			//给用户发送短信---------------
			$send_sms_data = array(
			'phones'=>$_POST['mobile'],
			'content'=>$_REQUEST['member_sms_content'],
			);
			$this->curl_sms($send_sms_data);

			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
		}
		if($id and !$bidorder_id){
			$this->success('车源更新成功！',U('/Store/Bidsource/'));
		}
		if($id and $bidorder_id){
			$this->success('车源,订单添加成功！',U('/Store/Bidsource/'));
		}
      
        $this->success('订单修改成功！',U('/Carservice/Carserviceorder/'));
    }

    public function del(){
        $id = intval($_GET['id']);

        $condition['id'] = $id;

        $update['status'] = 8;

        $this->reservation_order_model->where($condition)->save($update);

        $this->success('订单作废成功！',U('/Carservice/Carserviceorder/'));
    }

	/*
		@author:wwy
		@function:得到店铺名
		@time:2014-10-11
	*/
	function GetShopname(){
		if($_REQUEST['shopname']){
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
		}
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}


}
