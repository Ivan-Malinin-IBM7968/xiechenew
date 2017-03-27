<?php

/**
 * Created by PhpStorm.
 * User: o
 * Date: 2015/7/15
 * Time: 16:19
 */
class BankorderinfoAction extends CommonAction
{
    function __construct()
    {
        parent::__construct();
        $this->item_type_model = M('tp_xieche.item_type', 'xc_');  //配件类型对应表
        //$this->filter_model = M('tp_xieche.item_filter', 'xc_');  //配件表
        $this->item_brand_model = M('tp_xieche.item_brand', 'xc_');  //配件类型对应表
        $this->order_model = M('tp_xieche.reservation_order', 'xc_'); //配件类型对应表
        $this->car_brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $this->car_model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $this->car_style_model = M('tp_xieche.car_style', 'xc_');  //车系号

        $this->checkreport_model = M('tp_xieche.checkreport_total', 'xc_');  //检测报告表
        $this->check_step_model = M('tp_xieche.check_step', 'xc_');  //技师步骤表
        $this->technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表
        $this->item_oil_model = M('tp_xieche.item_oil', 'xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //保养项目

        $this->carbrand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries', 'xc_');  //车型号
        //$this->filter_model = M('tp_xieche.item_filter', 'xc_');  //保养项目

        $this->PadataModel = M('tp_xieche.padatatest', 'xc_');//接收微信订单数据表
        $this->PaweixinModel = M('tp_xieche.paweixin', 'xc_');//携车手机微信比对表
        $this->user_model = M('tp_xieche.member', 'xc_');//用户表
        $this->admin_model = M('tp_admin.user', 'xc_');//后台用户表
        $this->invoice_model = M('tp_xieche.invoice', 'xc_');//发票数据表
        $this->recall_model = M('tp_xieche.reservation_recall', 'xc_');//客服回访表
        $this->carservicecode_model = M('tp_xieche.carservicecode', 'xc_');//优惠券表
        $this->carservicecode1_model = M('tp_xieche.carservicecode_1', 'xc_');//优惠券表

        $this->item_oil_model = M('tp_xieche.item_oil', 'xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //保养项目

        $this->reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $this->carservicecode_model = M('tp_xieche.carservicecode', 'xc_');//上门保养抵用码字段
        $this->model_sms = M('tp_xieche.sms', 'xc_');//手机短信
        $this->storehouse_item_model = M('tp_xieche.storehouse_item', 'xc_');//仓库数据详情表
        $this->finance_model = M('tp_xieche.finance', 'xc_');  //财务表
        $this->city_model = M('tp_xieche.city', 'xc_');  //财务表
        $this->package_model = M('tp_xieche.package', 'xc_');  //套餐表
        $this->dphome_linshi = D('dphome_linshi');  //点评数据临时表
        $this->interface_log = D('interface_log');  //接口通知记录表
        $this->city = D('city');   //城市表
        $this->Reservation_commentModel = D('reserveorder_comment');         //用户的上门评论表

    }
    /**
     * 保险公司订单管理
     * @date 2015/7/16
     * xuzw
     */
    public function index()
    {
        
        $authId = $_SESSION['authId'];
        $this->assign('authId',$authId);
        
        
        if($_SESSION['business_source']==41){
            if($_POST['type_name']=='all'){
                //$map['replace_code']  = array(array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
                $map['replace_code']  = array(array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'),'OR');

            }
            
            $map['order_type'] = array('neq', '34');

            
            if($_POST['type_name']==1)
                $map['replace_code']=array('like','%r%');
            if($_POST['type_name']==2)
                $map['replace_code']=array('like','%s%');
            if($_POST['type_name']==3)
                $map['replace_code']=array('like','%t%');
            if($_POST['type_name']==4)
                $map['replace_code']=array('like','%u%');
            $this->assign('type_name2','1');
        }
        if($_SESSION['business_source']==42){
            if($_POST['type_name']=='all'){
                $map['replace_code']  = array(array('like','%c%'),array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }
            if($_POST['type_name']==1)
                $map['replace_code']=array('like','%y%');
            if($_POST['type_name']==2)
                $map['replace_code']=array('like','%x%');
            if($_POST['type_name']==3)
                $map['replace_code']=array('like','%w%');
            $this->assign('type_name2','2');
        }
        

         $this->assign('type_name',$_POST['type_name']);
         
        //搜索订单id
        if ($_POST['id']) {
            $map['id'] = trim($_POST['id']);
        }
        //搜索订单手机号
        if ($_POST['mobile']) {
            $map['mobile'] = trim($_POST['mobile']);
        }
        //姓名查找
        if ($_POST['truename']) {
            $map['truename'] = trim($_POST['truename']);
        }
        //搜索订单牌照
        if ($_POST['licenseplate']) {
            $map['licenseplate'] = array('like', '%' . $_POST['licenseplate'] . '%');
            $this->assign('licenseplate', $_POST['licenseplate']);
        }
        //搜索订单地址
        if ($_POST['address']) {
            $map['address'] = array('like', '%' . $_POST['address'] . '%');
            $this->assign('address', $_POST['address']);
        }
        //搜索订单套餐类型
        if ($_REQUEST['order_type']) {
            $map['order_type'] = $_REQUEST['order_type'];
        }

        //搜索订单状态
        if ($_REQUEST['status'] == '0') {
            $map['status'] = array(IN, array(0, 1, 2));
        } elseif ($_REQUEST['status'] == '9') {
            $map['status'] = 9;
        } elseif ($_REQUEST['status'] == 'all') {
            $map['status'] = array('neq', 8);
        } elseif (!$_REQUEST['status']) {
            $map['status'] = array('neq', 8);
        }
        
        
        //搜索订单（时间查询）
        if ($_REQUEST['start_time']) {
            $map[$_REQUEST['time_type']] = array(array('gt', strtotime($_REQUEST['start_time'])));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('start_time', $_REQUEST['start_time']);
        }
        if ($_REQUEST['end_time']) {
            $map[$_REQUEST['time_type']] = array(array('lt', strtotime($_REQUEST['end_time'] . ' 23:59:59')));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('end_time', $_REQUEST['end_time']);
        }
        if ($_REQUEST['start_time'] && $_REQUEST['end_time']) {
            $map[$_REQUEST['time_type']] = array(array('gt', strtotime($_REQUEST['start_time'])), array('lt', strtotime($_REQUEST['end_time'] . ' 23:59:59')));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('start_time', $_REQUEST['start_time']);
            $this->assign('end_time', $_REQUEST['end_time']);
        }
        //insurance_name：1.安盛2.平安3.人保    city_id：1.上海2.杭州3.苏州4.成都5.济南
        if($_SESSION['business_source']>0){
            //$map['business_source']=$_SESSION['business_source'];
            if($_SESSION['business_source']==41){
                $map['_string'] = '(business_source_old=41) OR (business_source=19)';
            }elseif($_SESSION['business_source']==42){
                $map['_string'] = '(business_source_old=42) OR (business_source=18)';
            }else{
                $map['business_source'] =  $_SESSION['business_source'] ;
            }
            
            $map['city_id'] = $_SESSION['city_id'];
            if($_POST['type_name']){}else{
                $map['replace_code']  = array(array('like','%c%'),array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }
        }elseif($_SESSION['authId'] ==1){
            echo $this->order_model->getLastsql();
        }else{
            $map['business_source']=100000000;
        }
        $count = $this->order_model->where($map)->count();

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 25);
        // 分页显示输出
        $page = $p->show_admin();
        $list = $this->order_model->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();
        
        echo  $this->order_model->getLastSql();
        
        
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                //判断实例化新配件表品牌车型表还是老配件表品牌车型表
                $this->getNeedModel($value['create_time']);
                
                
                $list[$key]['order_type_name'] = $this->_carserviceConf($value['order_type']);
                $list[$key]['show_id'] = $this->get_orderid($value['id']) . '(' . $value['id'] . ')';
                $list[$key]['id'] = $this->get_orderid($value['id']);
                $list[$key]['true_id'] = $value['id'];
                $list[$key]['status_name'] = $this->getStatusName($value['status']);
                //获取优惠券用途
                if ($value['replace_code'] and $value['replace_code'] != '016888') {
                    $code_info = $this->carservicecode_model->where(array('coupon_code' => $value['replace_code']))->find();
                    if (!$code_info) {
                        $code_info = $this->carservicecode1_model->where(array('coupon_code' => $value['replace_code']))->find();
                    }
                    $list[$key]['replace_code'] = $list[$key]['replace_code'] . "</br>(" . $code_info['remark'] . ")";
                }
                if ($value['replace_code'] == '016888') {
                    $list[$key]['replace_code'] = $list[$key]['replace_code'] . "</br>(市场部通用券)";
                }
                //拼车型
                $model_id = $value['model_id'];
                $model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where(array('model_id' => $model_id))->find();
                $series_res = $this->carseries_model->field('series_name,brand_id')->where(array('series_id' => $model_res['series_id']))->find();
                $brand_res = $this->carbrand_model->field('brand_name')->where(array('brand_id' => $series_res['brand_id']))->find();
                $list[$key]['car_name'] = $brand_res['brand_name'] . $series_res['series_name'] . $model_res['model_name'];
            }
        }
        $this->assign('business_source', $map['business_source']);
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 上门保养预约订单详情
     * @date 2014/8/13
     */
    public function detail()
    {
        //查看评论
        $id = $_GET['id'];
        $order_info = $this->reservation_order_model->where('id=' . $id)->find();
        if ($order_info['model_id']) {
            //判断实例化新配件表品牌车型表还是老配件表品牌车型表
            $this->getNeedModel($order_info['create_time']);
            
            //拼车型
            $model_id = $order_info['model_id'];
            $model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where(array('model_id' => $model_id))->find();
            $series_res = $this->carseries_model->field('series_name,brand_id')->where(array('series_id' => $model_res['series_id']))->find();
            $brand_res = $this->carbrand_model->field('brand_name')->where(array('brand_id' => $series_res['brand_id']))->find();
            $order_info['car_name'] = $brand_res['brand_name'] . $series_res['series_name'] . $model_res['model_name'];
        }
        if ($order_info['order_type']) {
            $order_info['order_type_name'] = $this->_carserviceConf($order_info['order_type']);
        }
        //商品详情
        $order_items = unserialize($order_info['item']);

        if (!empty($order_items['oil_detail'])) {
            $item_oil_price = 0;
            $oil_data = $order_items['oil_detail'];
            foreach ($oil_data as $id => $num) {
                if ($num > 0) {
                    $res = $this->item_oil_model->field('name,price')->where(array('id' => $id))->find();
                    $item_oil_price += $res['price'] * $num;
                    $name = $res['name'];
                }
            }
            $oil_param['id'] = $order_items['oil_id'];
            $item_list['0']['id'] = $order_items['oil_id'];
            $item_list['0']['name'] = $name;
            $item_list['0']['price'] = $item_oil_price;
            if (!$oil_param['id']) {
                unset($item_list['0']);
            }
            if ($order_items['oil_id'] == '-1') {
                $item_list['0']['id'] = 0;
                $item_list['0']['name'] = "自备配件";
                $item_list['0']['price'] = 0;
            }
        }
        if ($order_items['filter_id']) {
            if ($order_items['filter_id'] == '-1') {    //自备配件的情况
                $item_list['1']['id'] = 0;
                $item_list['1']['name'] = "自备配件";
                $item_list['1']['price'] = 0;
            } else {
                $item_condition['id'] = $order_items['filter_id'];
                $name1 = $this->item_model->where($item_condition)->find();
                if (mb_strpos($name1['name'], '马勒') === 0) {
                    $item_list[1]['name'] = "马勒";
                } elseif (mb_strpos($name1['name'], '曼牌') === 0) {
                    $item_list[1]['name'] = "曼牌";
                } elseif (mb_strpos($name1['name'], '博世') === 0) {
                    $item_list[1]['name'] = "博世";
                } else {
                    $item_list[1]['name'] = $name1['name'];
                }
                $item_list[1]['price'] = $name1['price'];
            }
        }
        if ($order_items['kongqi_id']) {
            if ($order_items['kongqi_id'] == '-1') {
                $item_list['2']['id'] = 0;
                $item_list['2']['name'] = "自备配件";
                $item_list['2']['price'] = 0;
            } else {
                $item_condition['id'] = $order_items['kongqi_id'];
                $name2 = $this->item_model->where($item_condition)->find();
                if (mb_strpos($name2['name'], '马勒') === 0) {
                    $item_list[2]['name'] = "马勒";
                } elseif (mb_strpos($name2['name'], '曼牌') === 0) {
                    $item_list[2]['name'] = "曼牌";
                } elseif (mb_strpos($name2['name'], '博世') === 0) {
                    $item_list[2]['name'] = "博世";
                } else {
                    $item_list[2]['name'] = $name2['name'];
                }
                $item_list[2]['price'] = $name2['price'];
            }
        }
        if ($order_items['kongtiao_id']) {
            if ($order_items['kongtiao_id'] == '-1') {
                $item_list['3']['id'] = 0;
                $item_list['3']['name'] = "自备配件";
                $item_list['3']['price'] = 0;
            } else {
                $item_condition['id'] = $order_items['kongtiao_id'];
                $name3 = $this->item_model->where($item_condition)->find();
                if (mb_strpos($name3['name'], '马勒') === 0) {
                    $item_list[3]['name'] = "马勒";
                } elseif (mb_strpos($name3['name'], '曼牌') === 0) {
                    $item_list[3]['name'] = "曼牌";
                } elseif (mb_strpos($name3['name'], '博世') === 0) {
                    $item_list[3]['name'] = "博世";
                } else {
                    $item_list[3]['name'] = $name3['name'];
                }
                $item_list[3]['price'] = $name3['price'];
            }
        }
        //预约时间
        if ($order_info['order_time']) {
            $order_info['order_date'] = date('Y-m-d H:i:s', $order_info['order_time']);
        }
        $comment = $this->Reservation_commentModel->where('reserveorder_id=' . $_GET['id'])->find();
        if ($comment) {
            $this->assign('comment', $comment['content']);
        } else {
            $this->assign('comment', '该用户暂无评论！！');
        }
        //图片展示
        $step_info = $this->check_step_model->where( 'order_id='.$_GET['id'])->group('step_id')->select();
        //echo $this->check_step_model->getLastSql();
        foreach($step_info as $a=>$b){
            $step_info[$a]['create_time'] = date('Y-m-d H:i:s',$b['create_time']);
            $step_info[$a]['step_name'] = $this->get_stepname($b['step_id']);
        }
        //检测报告数据
        $report_info = $this->checkreport_model->where(array('reservation_id'=>$_GET['id']))->find();
        if($report_info){
            $order_info['report_id'] = base64_encode($report_info['id'].'168');
        }

       // var_dump($order_info);
        //var_dump($step_info);
        $this->assign('replace_code', $order_info['replace_code']);
        $this->assign('item_list', $item_list);
        $this->assign('step_info', $step_info);
        $this->assign('order_info', $order_info);
        $this->display();
    }

    public function order_export()
    {
        date_default_timezone_set('PRC');
        if($_SESSION['business_source']==41){
            if($_REQUEST['type_name']=='all'){
                $map['replace_code']  = array(array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }
            if($_REQUEST['type_name']==1)
                $map['replace_code']=array('like','%r%');
            if($_REQUEST['type_name']==2)
                $map['replace_code']=array('like','%s%');
            if($_REQUEST['type_name']==3)
                $map['replace_code']=array('like','%t%');
            if($_REQUEST['type_name']==4)
                $map['replace_code']=array('like','%u%');
            $this->assign('type_name2','1');
            
            $map['_string'] = '(business_source_old=41) OR (business_source=19)';
        }elseif($_SESSION['business_source']==42){
            if($_REQUEST['type_name']=='all'){
                $map['replace_code']  = array(array('like','%c%'),array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }
            if($_REQUEST['type_name']==1)
                $map['replace_code']=array('like','%y%');
            if($_REQUEST['type_name']==2)
                $map['replace_code']=array('like','%x%');
            if($_REQUEST['type_name']==3)
                $map['replace_code']=array('like','%w%');
            $this->assign('type_name2','2');
            
            $map['_string'] = '(business_source_old=42) OR (business_source=18)';
        }else{
            $map['business_source'] =  $_SESSION['business_source'] ;
            if($_SESSION['business_source']==18){ //人保
                $map['replace_code']  = array(array('like','%c%'),array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }elseif($_SESSION['business_source']==19) {  //平安
                $map['replace_code']  = array(array('like','%v%'), array('like','%r%'), array('like','%s%'), array('like','%t%'), array('like','%u%'), array('like','%w%'),array('like','%x%'),array('like','%y%'),'OR');
            }
        }
        

        if (!empty($_REQUEST['end_time']) && !empty($_REQUEST['start_time'])) {
            $start = strtotime($_REQUEST['start_time']);
            $end = strtotime($_REQUEST['end_time'] . '23:59:59');
            $map['order_time'] = array(array('lt', $end), array('gt', $start), "AND");
        }
        
        $map['city_id'] = $_SESSION['city_id'];
        
        
        if ($_REQUEST['status'] == 'all') {
            $map['status'] = array('neq', 8);
        } else {
            $map['status'] = $_REQUEST['status'];
        }

        $rs = $this->reservation_order_model->where($map)->select();
        //echo  $this->reservation_order_model->getLastSql();
        //exit ;
        
        
        $str = "订单id,用户名,验证码,手机号,地址,预约时间,上门时间,完成时间,服务类型,用户所付金额,抵扣的金额,车型,机油,机滤,空滤,空调滤,套餐卡号,保单号\n";

        foreach ($rs as $k => $v) {
            //判断实例化新配件表品牌车型表还是老配件表品牌车型表
            $this->getNeedModel($v['create_time']);
                
            //拼车型
            $model_id = $v['model_id'];
            $model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where(array('model_id' => $model_id))->find();
            $series_res = $this->carseries_model->field('series_name,brand_id')->where(array('series_id' => $model_res['series_id']))->find();
            $brand_res = $this->carbrand_model->field('brand_name')->where(array('brand_id' => $series_res['brand_id']))->find();
            $car_name = $brand_res['brand_name'] . $series_res['series_name'] . $model_res['model_name'];

            //配件数据
            $order_items = unserialize($v['item']);
            $oil_data = $order_items['oil_detail'];
            $oil = '';
            foreach ($oil_data as $_id => $num) {
                if ($num > 0) {
                    $info = $this->item_oil_model->where(array('id' => $_id))->find();
                    $oil = $oil . $info['name'] . ' ' . $info['norms'] . "L : " . $num . "件;";
                }
            }
            foreach ($order_items as $key => $value) {
                if ($key == 'filter_id' || $key == 'kongqi_id' || $key == 'kongtiao_id') {
                    $keyArr = explode('_', $key);
                    $name = $keyArr[0];
                    $info = $this->item_model->where(array('id' => $value))->find();
                    $fitArr[$name] = $info['name'];
                }
            }
            $city_info = $this->city->where('id ='.$v['city_id'])->find();
            //print_r($fitArr);
            //字段值
            $id = $v['id'];
            $name = $v['truename'];
            $code = $v['replace_code'];
            $mobile = $v['mobile'];
            $address = $city_info['name'].$v['address'];
            $time = date('Y-m-d H:i:s', $v['create_time']);    //下单时间
            $time2 = date('Y-m-d H:i:s', $v['order_time']);  //预约时间
            //完成时间：
            $time_where['order_id'] =$id;
            $time_where['step_id'] =6;
            $step_info = $this->check_step_model->where($time_where)->find();
            if($step_info){
                $time3 = date('Y-m-d H:i:s', $step_info['create_time']);
            }else{
                $time3='';
            }
            $order_type = $this->_carserviceConf($v['order_type']);
            //如果订单类型是保养订单
            if($order_type=='保养订单'){
                if ($v['replace_code'] and $v['replace_code'] != '016888') {
                    $code_info = $this->carservicecode_model->where(array('coupon_code' => $v['replace_code']))->find();
                    if (!$code_info) {
                        $code_info = $this->carservicecode1_model->where(array('coupon_code' => $v['replace_code']))->find();
                    }
                    $order_type =  $code_info['remark'] ;
                }
                if ($value['replace_code'] == '016888') {
                    $order_type =  "市场部通用券";
                }   
            }
            
            $amount = $v['amount'];
            $dikou_amount = $v['dikou_amount'];
            $filter = $fitArr['filter']; //中文转码
            $kongqi = $fitArr['kongqi']; //中文转码
            $kongtiao = $fitArr['kongtiao']; //中文转码

            $str .= $id . "," . $name . ",".$code. "," .$mobile . "," . $address . "," . $time . ",".$time2. "," . $time3 . ",". $order_type . ",". $amount . ",". $dikou_amount . "," . $car_name . "," . $oil . ",". $filter . "," . $kongqi . "," . $kongtiao .",".$v['card_number'].",".$v['warranty_id']."\n"; //用英文逗号分开
        }

        //echo  $str ;
        //exit ;
        $filename = $_REQUEST['start_time'] . '-' . $cityInfo['name'] . '订单数据' . '.csv'; //设置文件名
        $this->export_csv($filename, $str); //导出
    }

    //导出数据为csv  wql@20150709
    function export_csv($filename, $data)
    {
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8'); 
        header("Content-Disposition:attachment;filename=".$filename);   
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        header('Expires:0');   
        header('Pragma:public'); 
        echo "\xEF\xBB\xBF"; // UTF-8 BOM   
        echo $data;
    }
    //订单状态
    private function getStatusName($status)
    {
        switch ($status) {
            case '0':
                return "等待处理";
                break;

            case '1':
                return "预约确认";
                break;

            case '2':
                return "已分配技师";
                break;

            case '7':
                return "已终止";
                break;

            case '8':
                return "已作废";
                break;

            case '9':
                return "服务已完成";
                break;

            default:
                return "等待处理";
                break;
        }
    }

    private function _carserviceConf($type)
    {
        switch ($type) {
            case 1:
                $name = '保养订单';
                break;
            case 2:
                $name = '检测订单';
                break;
            case 3:
                $name = '淘宝99元保养订单';
                break;
            case 6:
                $name = '免99元服务费订单';
                break;
            case 7:
                $name = '黄喜力套餐';
                break;
            case 8:
                $name = '蓝喜力套餐';
                break;
            case 9:
                $name = '灰喜力套餐';
                break;
            case 10:
                $name = '金美孚套餐';
                break;
            case 11:
                $name = '爱代驾高端保养';
                break;
            case 12:
                $name = '爱代驾中端保养';
                break;
            case 13:
                $name = '好车况';
                break;
            case 14:
                $name = '好空气';
                break;
            case 15:
                $name = '好动力';
                break;
            case 16:
                $name = '保养服务+检测+养护';
                break;
            case 17:
                $name = '矿物质油保养套餐+检测+养护';
                break;
            case 18:
                $name = '半合成油保养套餐+检测+养护';
                break;
            case 19:
                $name = '全合成油保养套餐+检测+养护';
                break;
            case 20:
                $name = '平安保险微信';
                break;
            case 22:
                $name = '168黄喜力';
                break;
            case 23:
                $name = '268蓝喜力';
                break;
            case 24:
                $name = '368金美孚';
                break;
            case 25:
                $name = '199';
                break;
            case 26:
                $name = '299';
                break;
            case 27:
                $name = '399';
                break;
            case 28:
                $name = '全车检测38项(淘38)';
                break;
            case 29:
                $name = '细节养护7项(淘38)';
                break;
            case 30:
                $name = '更换空调滤工时(淘38)';
                break;
            case 31:
                $name = '更换雨刮工时(淘38)';
                break;
            case 32:
                $name = '小保养工时(淘38)';
                break;
            case 33:
                $name = '好空气套餐(奥迪宝马奔驰)';
                break;
            case 34:
                $name = '补配件免人工订单';
                break;
            case 35:
                $name = '好车况套餐:38项全车检测+7项细节养护';
                break;
            case 49:
                $name = '防雾霾8元套餐';
                break;
            case 59:
                $name = '平安苏州2015-7发动机舱精细套餐';
                break;
            default:
                return "保养订单";
                break;
        }
        return $name;
    }
    private function get_stepname($step_id){
        switch ($step_id) {
            case '0':
                return "出发";
                break;

            case '1':
                return "到达";
                break;

            case '2':
                return "架摄像机并开始保养";
                break;

            case '3':
                return "车辆检查报告";
                break;

            case '4':
                return "收款";
                break;

            case '5':
                return "评价";
                break;

            case '6':
                return "完成";
                break;

            default:
                return "未知类型";
                break;
        }
    }
    
    
       //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150820
    public function getNeedModel($orderCreateTime) {
        $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
        if($orderCreateTime <= $new_filter_time){
            $this->carbrand_model = D('carbrand');  //车品牌
            $this->carmodel_model = D('carmodel');  //车型号
            $this->carseries_model = D('carseries');  //车系
            $this->item_oil_model = D('item_oil');  //保养机油
            $this->item_model = D('item_filter');  //保养项目
        }else{
            $this->carbrand_model = D('new_carbrand');  //车品牌
            $this->carmodel_model = D('new_carmodel');  //车型号
            $this->carseries_model = D('new_carseries');  //车系
            $this->item_oil_model = D('new_item_oil');  //保养机油
            $this->item_model = D('new_item_filter');  //保养项目
        }
        
    }

}