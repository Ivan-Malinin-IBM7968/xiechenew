<?php

/**
 * Created by PhpStorm.
 * User: o
 * Date: 2015/7/31
 * Time: 10:43
 */
class ErroritemconfigAction extends CommonAction
{
    function __construct()
    {
        parent::__construct();
        $this->reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $this->carbrand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $this->itemerrimg_model = M('tp_xieche.itemerrimg', 'xc_');  //

    }

    public function index()
    {
        //搜索订单id
        if ($_POST['id']) {
            $map['reservation_id'] = trim($_POST['id']);
        }
        //搜索订单（时间查询）
        if ($_REQUEST['start_time']) {
            $map['create_time'] = array(array('gt', strtotime($_REQUEST['start_time'])));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('start_time', $_REQUEST['start_time']);
        }
        if ($_REQUEST['end_time']) {
            $map['create_time'] = array(array('lt', strtotime($_REQUEST['end_time'] . ' 23:59:59')));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('end_time', $_REQUEST['end_time']);
        }
        if ($_REQUEST['start_time'] && $_REQUEST['end_time']) {
            $map['create_time'] = array(array('gt', strtotime($_REQUEST['start_time'])), array('lt', strtotime($_REQUEST['end_time'] . ' 23:59:59')));
            $this->assign('time_type', $_REQUEST['time_type']);
            $this->assign('start_time', $_REQUEST['start_time']);
            $this->assign('end_time', $_REQUEST['end_time']);
        }
        $map['id'] = array('gt', 8);
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $count = count($this->itemerrimg_model->where($map)->Distinct(true)->field('reservation_id')->select());
        $p = new Page($count, 25);
        // 分页显示输出
        $page = $p->show_admin();
        $info = $this->itemerrimg_model->where($map)->Distinct(true)->field('reservation_id')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach ($info as $k => $v) {
            $map2['id'] = $v['reservation_id'];
            $order_info = $this->reservation_order_model->where($map2)->field('model_id')->find();
            //拼车型
            $model_id = $order_info['model_id'];
            $model_res = $this->carmodel_model->field('model_name,item_set,series_id')->where(array('model_id' => $model_id))->find();
            $series_res = $this->carseries_model->field('series_name,brand_id')->where(array('series_id' => $model_res['series_id']))->find();
            $brand_res = $this->carbrand_model->field('brand_name')->where(array('brand_id' => $series_res['brand_id']))->find();
            $info[$k]['car_name'] = $brand_res['brand_name'] . $series_res['series_name'] . $model_res['model_name'];
        }
        $this->assign('info', $info);
        $this->assign('page', $page);
        $this->display();
    }

    public function errimg()
    {
        $map['reservation_id'] = $_GET['id'];
        $info = $this->itemerrimg_model->where($map)->order('create_time desc')->find();
        $arr = explode(',', $info['img_url']);
        $this->assign('img_arr', $arr);
        $this->display();
    }
}