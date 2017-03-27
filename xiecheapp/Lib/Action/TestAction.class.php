<?php
class TestAction extends CommonAction
{
    function __construct()
    {
        parent::__construct();
        $this->reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $this->order_model = M('tp_xieche.order', 'xc_');  //预约订单
        $this->technician_model = M('tp_xieche.technician', 'xc_');  //预约订单

        $this->carbrand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries', 'xc_');  //车型号
        $this->filter_model = M('tp_xieche.item_filter', 'xc_');  //保养项目

    }
    //统计取消订单的数量
    function cancle_order_num(){
        header("Content-Type:text/html;charset=utf-8");
        $map['pay_type'] = array(in,array(0,1));
        $map['pay_status'] = 1;
        $map['status'] =9;
        $map['operator_remark'] =array('like','%取消订单原因%');
        $info = $this->reservation_order_model->where($map)->select();
        echo "<table><tr><th>订单号</th><th>金额</th><th>支付方式</th><th>支付状态</th><th>订单状态</th><th>服务时间</th><th>技师</th><th>用户备注</th></tr>";
        foreach( $info as $k=>$v){
            if($v['technician_id']){
                $map1['id']=$v['technician_id'];
                $tech_info = $this->technician_model->where($map1)->find();
                $info[$k]['technician_name']=$tech_info['truename'];
            }
            echo "<tr><td>".$v['id']."</td><td>".$v['amount']."</td><td>现金</td><td>已支付</td><td>已完成</td><td>".date('Y-m-d H:i:s',$v['order_time'])."</td><td>".$info[$k]['technician_name']."</td><td>".$v['operator_remark']."</td></tr>";
        }
        echo "</table>";
    }
    //根据品牌统计各个月份的保养次数
    function brand_order_month_num()
    {
        header("Content-Type:text/html;charset=utf-8");
        //品牌循环一遍，月份循环一遍，然后找
        $brand_info = $this->carbrand_model->field('brand_id,brand_name')->select();   //品牌
        echo "<table>
			<tr><th>品牌</th><th>14年9月</th><th>14年10月</th><th>14年11月</th><th>14年12月</th><th>15年1月</th><th>15年2月</th><th>15年3月</th><th>15年4月</th><th>15年5月</th><th>15年6月</th><th>15年7月</th><th>15年8月</th></tr>";
        foreach ($brand_info as $k => $v) {
            $int_9 = $int_10 = $int_11 = $int_12 = $int_1 = $int_2 = $int_3 = $int_4 = $int_5 = $int_6 = $int_7 = $int_8 = 0;
            $name = $v['brand_name'];
            $map_s['brand_id'] = $v['brand_id'];
            $carseries_info = $this->carseries_model->where($map_s)->field('series_id')->select();
            $list = array();
            foreach ($carseries_info as $k1 => $v1) {
                $list[] = $v1['series_id'];
            }
            $map_m['series_id'] = array(IN, $list);
            $model_info = $this->carmodel_model->where($map_m)->field('model_id')->select();
            $list2 = array();
            foreach ($model_info as $k2 => $v2) {
                $list2[] = $v2['model_id'];
            }
            $map_9['model_id'] = array(IN, $list2);
            $map_9['order_time'] = array('between', array(strtotime('2014-9-1'), strtotime('2014-10-1')));
            $map_9['status'] = 9;
            $map_9['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_9 = $this->reservation_order_model->where($map_9)->select();
            foreach ($info_9 as $k_9 => $v_9) {
                $arr_9 = unserialize($v_9['item']);
                if ($arr_9['oil_id'] and $arr_9['filter_id']) {
                    $int_9++;
                }
            }
            $map_10['model_id'] = array(IN, $list2);
            $map_10['order_time'] = array('between', array(strtotime('2014-10-1'), strtotime('2014-11-1')));
            $map_10['status'] = 9;
            $map_10['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_10 = $this->reservation_order_model->where($map_10)->select();
            foreach ($info_10 as $k_10 => $v_10) {
                $arr_10 = unserialize($v_10['item']);
                if ($arr_10['oil_id'] and $arr_10['filter_id']) {
                    $int_10++;
                }
            }

            $map_11['model_id'] = array(IN, $list2);
            $map_11['order_time'] = array('between', array(strtotime('2014-11-1'), strtotime('2014-12-1')));
            $map_11['status'] = 9;
            $map_11['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_11 = $this->reservation_order_model->where($map_11)->select();
            foreach ($info_11 as $k_11 => $v_11) {
                $arr_11 = unserialize($v_11['item']);
                if ($arr_11['oil_id'] and $arr_11['filter_id']) {
                    $int_11++;
                }
            }
            $map_12['model_id'] = array(IN, $list2);
            $map_12['order_time'] = array('between', array(strtotime('2014-12-1'), strtotime('2015-1-1')));
            $map_12['status'] = 9;
            $map_12['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_12 = $this->reservation_order_model->where($map_12)->select();
            foreach ($info_12 as $k_12 => $v_12) {
                $arr_12 = unserialize($v_12['item']);
                if ($arr_12['oil_id'] and $arr_12['filter_id']) {
                    $int_12++;
                }
            }

            $map_1['model_id'] = array(IN, $list2);
            $map_1['order_time'] = array('between', array(strtotime('2015-1-1'), strtotime('2015-2-1')));
            $map_1['status'] = 9;
            $map_1['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_1 = $this->reservation_order_model->where($map_1)->select();
            foreach ($info_1 as $k_1 => $v_1) {
                $arr_1 = unserialize($v_1['item']);
                if ($arr_1['oil_id'] and $arr_1['filter_id']) {
                    $int_1++;
                }
            }

            $map_2['model_id'] = array(IN, $list2);
            $map_2['order_time'] = array('between', array(strtotime('2015-2-1'), strtotime('2015-3-1')));
            $map_2['status'] = 9;
            $map_2['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_2 = $this->reservation_order_model->where($map_2)->select();
            foreach ($info_2 as $k_2 => $v_2) {
                $arr_2 = unserialize($v_2['item']);
                if ($arr_2['oil_id'] and $arr_2['filter_id']) {
                    $int_2++;
                }
            }

            $map_3['model_id'] = array(IN, $list2);
            $map_3['order_time'] = array('between', array(strtotime('2015-3-1'), strtotime('2015-4-1')));
            $map_3['status'] = 9;
            $map_3['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_3 = $this->reservation_order_model->where($map_3)->select();
            foreach ($info_3 as $k_3 => $v_3) {
                $arr_3 = unserialize($v_3['item']);
                if ($arr_3['oil_id'] and $arr_3['filter_id']) {
                    $int_3++;
                }
            }

            $map_4['model_id'] = array(IN, $list2);
            $map_4['order_time'] = array('between', array(strtotime('2015-4-1'), strtotime('2015-5-1')));
            $map_4['status'] = 9;
            $map_4['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_4 = $this->reservation_order_model->where($map_4)->select();
            foreach ($info_4 as $k_4 => $v_4) {
                $arr_4 = unserialize($v_4['item']);
                if ($arr_4['oil_id'] and $arr_4['filter_id']) {
                    $int_4++;
                }
            }

            $map_5['model_id'] = array(IN, $list2);
            $map_5['order_time'] = array('between', array(strtotime('2015-5-1'), strtotime('2015-6-1')));
            $map_5['status'] = 9;
            $map_5['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_5 = $this->reservation_order_model->where($map_5)->select();
            foreach ($info_5 as $k_5 => $v_5) {
                $arr_5 = unserialize($v_5['item']);
                if ($arr_5['oil_id'] and $arr_5['filter_id']) {
                    $int_5++;
                }
            }

            $map_6['model_id'] = array(IN, $list2);
            $map_6['order_time'] = array('between', array(strtotime('2015-6-1'), strtotime('2015-7-1')));
            $map_6['status'] = 9;
            $map_6['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_6 = $this->reservation_order_model->where($map_6)->select();
            foreach ($info_6 as $k_6 => $v_6) {
                $arr_6 = unserialize($v_6['item']);
                if ($arr_6['oil_id'] and $arr_6['filter_id']) {
                    $int_6++;
                }
            }

            $map_7['model_id'] = array(IN, $list2);
            $map_7['order_time'] = array('between', array(strtotime('2015-7-1'), strtotime('2015-8-1')));
            $map_7['status'] = 9;
            $map_7['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_7 = $this->reservation_order_model->where($map_7)->select();
            foreach ($info_7 as $k_7 => $v_7) {
                $arr_7 = unserialize($v_7['item']);
                if ($arr_7['oil_id'] and $arr_7['filter_id']) {
                    $int_7++;
                }
            }

            $map_8['model_id'] = array(IN, $list2);
            $map_8['order_time'] = array('between', array(strtotime('2015-8-1'), strtotime('2015-9-1')));
            $map_8['status'] = 9;
            $map_8['order_type'] = array('not in', array(2, 13, 14, 15, 21, 28, 29, 30, 31, 33, 34, 35, 48, 49, 50, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67));
            $info_8 = $this->reservation_order_model->where($map_8)->select();
            foreach ($info_8 as $k_8 => $v_8) {
                $arr_8 = unserialize($v_8['item']);
                if ($arr_8['oil_id'] and $arr_8['filter_id']) {
                    $int_8++;

                }
            }
            echo "<tr><td>" . $name . "</td><td>" . $int_9 . "</td><td>" . $int_10 . "</td><td>" . $int_11 . "</td><td>" . $int_12 . "</td><td>" . $int_1 . "</td><td>" . $int_2 . "</td><td>" . $int_3 . "</td><td>" . $int_4 . "</td><td>" . $int_5 . "</td><td>" . $int_6 . "</td><td>" . $int_7 . "</td><td>" . $int_8 . "</td></tr>";
            unset($arr_9, $arr_10, $arr_11, $arr_12, $arr_1, $arr_2, $arr_3, $arr_4, $arr_5, $arr_6, $arr_7, $arr_8);
            unset($info_9, $info_10, $info_11, $info_12, $info_1, $info_2, $info_3, $info_4, $info_5, $info_6, $info_7, $info_8);
        }
        echo "</table>";
    }

    //查询5月1-5月20号，用的所有机油
    function get_item_oil()
    {
        header("Content-Type:text/html;charset=utf-8");
        $map['status'] = 9;
        // $map['order_type'] = 9;
        //$map['order_time'] = array(array('egt','1430409600'),array('elt','1432569600'));
        $order_model = D('reservation_order');
        //$oil_model = D('item_oil');
        $order_info = $order_model->where($map)->select();
        $oil45 = $oil46 = $oil47 = $oil48 = $oil49 = $oil50 = $oil51 = $oil52 = $oil53 = $oil54 = $oil55 = $oil56 = $oil57 = $oil58 = $oil59 = 0;
        foreach ($order_info as $k => $v) {
            $arr_item = unserialize($v['item']);
            //print_r($arr_item);
            if ($arr_item['oil_detail']) {
                foreach ($arr_item['oil_detail'] as $k1 => $v1) {
                    if ($v1 != 0) {
                        if ($k1 == 45) {
                            if (intval($v1, 10) > 1) {
                                $oil45 = $oil45 + intval($v1, 10);
                            } else {
                                $oil45++;
                            }
                        } elseif ($k1 == 46) {
                            if (intval($v1, 10) > 1) {
                                $oil46 = $oil46 + intval($v1, 10);
                            } else {
                                $oil46++;
                            }
                        } elseif ($k1 == 47) {
                            if (intval($v1, 10) > 1) {
                                $oil47 = $oil47 + intval($v1, 10);
                            } else {

                                $oil47++;
                            }
                        } elseif ($k1 == 48) {
                            if (intval($v1, 10) > 1) {
                                $oil48 = $oil48 + intval($v1, 10);
                            } else {

                                $oil48++;
                            }
                        } elseif ($k1 == 49) {
                            if (intval($v1, 10) > 1) {
                                $oil49 = $oil49 + intval($v1, 10);
                            } else {

                                $oil49++;
                            }
                        } elseif ($k1 == 50) {
                            if (intval($v1, 10) > 1) {
                                $oil50 = $oil50 + intval($v1, 10);
                            } else {

                                $oil50++;
                            }
                        } elseif ($k1 == 51) {
                            if (intval($v1, 10) > 1) {
                                $oil51 = $oil51 + intval($v1, 10);
                            } else {

                                $oil51++;
                            }
                        } elseif ($k1 == 52) {
                            if (intval($v1, 10) > 1) {
                                $oil52 = $oil52 + intval($v1, 10);
                            } else {

                                $oil52++;
                            }
                        } elseif ($k1 == 53) {
                            if (intval($v1, 10) > 1) {
                                $oil53 = $oil53 + intval($v1, 10);
                            } else {

                                $oil53++;
                            }
                        } elseif ($k1 == 54) {
                            if (intval($v1, 10) > 1) {
                                $oil54 = $oil54 + intval($v1, 10);
                            } else {

                                $oil54++;
                            }
                        } elseif ($k1 == 55) {
                            if (intval($v1, 10) > 1) {
                                $oil55 = $oil55 + intval($v1, 10);
                            } else {

                                $oil55++;
                            }
                        } elseif ($k1 == 56) {
                            if (intval($v1, 10) > 1) {
                                $oil56 = $oil56 + intval($v1, 10);
                            } else {

                                $oil56++;
                            }
                        } elseif ($k1 == 57) {
                            if (intval($v1, 10) > 1) {
                                $oil57 = $oil57 + intval($v1, 10);
                            } else {

                                $oil57++;
                            }
                        } elseif ($k1 == 58) {
                            if (intval($v1, 10) > 1) {
                                $oil58 = $oil58 + intval($v1, 10);
                            } else {

                                $oil58++;
                            }
                        } elseif ($k1 == 59) {
                            if (intval($v1, 10) > 1) {
                                $oil59 = $oil59 + intval($v1, 10);
                            } else {

                                $oil59++;
                            }
                        }
                    }
                }
            }
        }
        echo "5/1 - 5/25的机油用量：";
        echo "壳牌 灰喜力5W-40 4升" . $oil45 . "个" . "<br>";
        echo "壳牌 灰喜力5W-40 1升" . $oil46 . "个" . "<br>";
        echo "壳牌 蓝喜力HX7 5W-40 4升" . $oil47 . "个" . "<br>";
        echo "壳牌 蓝喜力HX7 5W-40 1升" . $oil48 . "个" . "<br>";
        echo "壳牌 黄喜力HX5 10W-40 4升" . $oil49 . "个" . "<br>";
        echo "美孚 金装美孚1号0W-40 4升" . $oil50 . "个" . "<br>";
        echo "美孚 金装美孚1号0W-40 1升" . $oil51 . "个" . "<br>";
        echo "嘉实多 极护5W-40 4升" . $oil52 . "个" . "<br>";
        echo "嘉实多 极护5W-40 1升" . $oil53 . "个" . "<br>";
        echo "嘉实多 磁护5W-40 4升" . $oil54 . "个" . "<br>";
        echo "嘉实多 磁护5W-40 1升" . $oil55 . "个" . "<br>";
        echo "嘉实多 金嘉护10W-40 4升" . $oil56 . "个" . "<br>";
        echo "美孚 银美孚1号5W-30 4升" . $oil57 . "个" . "<br>";
        echo "美孚 银美孚1号5W-30 1升" . $oil58 . "个" . "<br>";
        echo "黄喜力10W-40 1升" . $oil59 . "个" . "<br>";

    }

    //统计所有配件的使用次数
    function item_num()
    {
        header("Content-Type:text/html;charset=utf-8");
        unset($list2);
        $map['status'] = 9;
        $info = $this->reservation_order_model->where($map)->field('item')->select();
//		var_dump($info);
        echo "<table><tr><th>品牌</th><th>个数</th></tr>";
        foreach ($info as $k1 => $v1) {
            unset($map);
            unset($map2);
            unset($map3);
            unset($info_filter);
            unset($info_kongqi);
            unset($info_kongtiao);
            $item_arr = unserialize($v1['item']);
            if ($item_arr['filter_id']) {
                $map['id'] = $item_arr['filter_id'];
                $info_filter = $this->filter_model->where($map)->find();
                if ($info_filter) {
                    $list2[] = $info_filter['name'];
                }
            } elseif ($item_arr['kongqi_id']) {
                $map2['id'] = $item_arr['kongqi_id'];
                $info_kongqi = $this->filter_model->where($map2)->find();
                if ($info_kongqi) {
                    $list2[] = $info_kongqi['name'];
                }
            } elseif ($item_arr['kongtiao_id']) {
                $map3['id'] = $item_arr['kongtiao_id'];
                $info_kongtiao = $this->filter_model->where($map3)->find();
                if ($info_kongtiao) {
                    if ((substr($info_kongtiao['name'], 0, 5) == 'pm2.5')) {
                        if (strrpos($info_kongtiao['name'], "(8元装)")) {
                            $name_kongtiao = substr($info_kongtiao['name'], 0, 6);
                            $name_kongtiao = substr($name_kongtiao, 0, -9);
                        } else {
                            $name_kongtiao = substr($info_kongtiao['name'], 0, 5);
                        }
                        $name_kongtiao = $name_kongtiao;

                    } else {
                        $name_kongtiao = $info_kongtiao['name'];

                    }
                    $list2[] = $name_kongtiao;
                }
            }
        }
        $arr = array_count_values($list2);
        foreach ($arr as $k => $v) {
            echo "<tr><td>" . $k . "</td><td>" . $v . "</td></tr>";
        }
        echo "</table>";
    }

    //删除配件的空调虑pm2.5 多出来的曼牌
    function delete_item_air_condition_set_exitm()
    {
        $carmodel = D('carmodel_a');
        $item_filter = D('item_filter_a');
//		$id["model_id"] =436;
        $carmodel_info = $carmodel->order('model_id asc')->select();   //limit(5)->order('model_id asc')
        foreach ($carmodel_info as $k => $v) {
            if ($v['item_set']) {
                $arr = unserialize($v['item_set']);
                if (count($arr[3]) > 5) {
                    foreach ($arr[3] as $k1 => $v1) {
                        $where['id'] = $v1;
                        $item_filter_info = $item_filter->where($where)->find();
                        if (mb_strpos($item_filter_info['name'], 'pm2.5-曼牌') === 0) {
                            unset($arr[3][$k1]);
                        }
                    }
                    $arr_new = array(3 => $arr[3]);
                    array_replace($arr, $arr_new);
                    $item_set = serialize($arr);
                    $where6['model_id'] = $v['model_id'];
                    $where7['item_set'] = $item_set;
                    $int3 = $carmodel->where($where6)->save($where7);
                    echo $int3;
                }
            }
        }
    }

    //删除配件的空调虑pm2.5
    function delete_item_air_condition_set_exit_ff()
    {
        $carmodel = D('carmodel');
        $item_filter = D('item_filter');
        $carmodel_info = $carmodel->order('model_id asc')->select();   //limit(5)->order('model_id asc')where($id)->select();//
        foreach ($carmodel_info as $k => $v) {
            if ($v['item_set']) {
                $arr = unserialize($v['item_set']);
                if ($arr[3]) {
                    foreach ($arr[3] as $k1 => $v1) {
                        $where['id'] = $v1;
                        $item_filter_info = $item_filter->where($where)->find();
                        if (mb_strpos($item_filter_info['name'], 'pm2.5') === 0) {
                            if (mb_strpos($item_filter_info['name'], 'pm2.5-') === 0) {
                            } else {
                                unset($arr[3][$k1]);
                            }
                        }
                    }
                    $arr_new = array(3 => $arr[3]);
                    array_replace($arr, $arr_new);
                    $item_set = serialize($arr);
                    $where6['model_id'] = $v['model_id'];
                    $where7['item_set'] = $item_set;
                    $int3 = $carmodel->where($where6)->save($where7);
                    echo $int3;
                }
            }
        }
    }
    //绑定空调配件id（0,8元套餐）
    //的数据才是真是的
    function item_air_condition_set_ff()
    {
        $carmodel = D('carmodel');
        $item_filter = D('item_filter');
        //$id["model_id"] =1779;
        $carmodel_info = $carmodel->order('model_id asc')->select();   //limit(5)->order('model_id asc')
        $i = $i2 = 0;
        foreach ($carmodel_info as $k => $v) {
            if ($v['item_set']) {
                $arr = unserialize($v['item_set']);
                if ($arr[3]) {
                    foreach ($arr[3] as $k1 => $v1) {
                        $where['id'] = $v1;
                        $item_filter_info = $item_filter->where($where)->find();
                        if (substr($item_filter_info['name'], 0, 6) == "马勒") {
                            $i++;
                            $where2['type_id'] = 4;
                            $where2['name'] = "pm2.5-" . $item_filter_info['name'] . "(8元装)";
                            $where2['price'] = 8;
                            $where2['brand_id'] = 5;
                            $info = $item_filter->where($where2)->find();
                            if ($info) {
                                $int1 = $info["id"];
                            } else {
                                $int1 = $item_filter->add($where2);
                            }
                            $where3['type_id'] = 4;
                            $where3['name'] = "pm2.5-" . $item_filter_info['name'] . "(0元装)";
                            $where3['price'] = 0;
                            $where3['brand_id'] = 5;
                            $info2 = $item_filter->where($where3)->find();
                            if ($info2) {
                                $int2 = $info2["id"];
                            } else {
                                $int2 = $item_filter->add($where3);
                            }
                            echo array_push($arr[3], $int1, $int2);  //将一个或多个单元压入数组的末尾（入栈）
                            break;
                        }
                        if (substr($item_filter_info['name'], 0, 6) == "曼牌") {
                            $i2++;
                            $where4['type_id'] = 4;
                            $where4['name'] = "pm2.5-" . $item_filter_info['name'] . "(8元装)";
                            $where4['price'] = 8;
                            $where4['brand_id'] = 8;
                            $info3 = $item_filter->where($where4)->find();
                            if ($info3) {
                                $int1 = $info3["id"];
                            } else {
                                $int1 = $item_filter->add($where4);
                            }
                            $where5['type_id'] = 4;
                            $where5['name'] = "pm2.5-" . $item_filter_info['name'] . "(0元装)";
                            $where5['price'] = 0;
                            $where5['brand_id'] = 8;
                            $info4 = $item_filter->where($where5)->find();
                            if ($info4) {
                                $int2 = $info4["id"];
                            } else {
                                $int2 = $item_filter->add($where5);
                            }
                            echo array_push($arr[3], $int1, $int2);
                        }
                    }
                    $arr_new = array(3 => $arr[3]);
                    array_replace($arr, $arr_new);
                    $item_set = serialize($arr);
                    $where6['model_id'] = $v['model_id'];
                    $where7['item_set'] = $item_set;
                    $int3 = $carmodel->where($where6)->save($where7);
                    echo $int3;
                }
            }
        }
        echo "马勒" . $i;
        echo "曼牌" . $i2;
    }

    //再次导入刹车片价格
    function  import_trw_price2_fuck()
    {
        $i = 0;
        $item_filter = D('item_filter');
        $trw_price = D('trw_price2');
        $map_item['type_id'] = array('in', '7,8');
        $item_filter_info = $item_filter->where($map_item)->select();
        foreach ($item_filter_info as $k => $v) {
            $map['name'] = $v['name'];
            $trw_price_info = $trw_price->where($map)->find();
            if ($trw_price_info) {
                $data['price'] = round($trw_price_info['price2']);
                $int = $item_filter->where($map)->save($data);
                if ($int) {
                    $i++;
                }
            } else {
                echo $v['name'] . "<br>";
            }
        }
        echo $i;
    }

    //导入刹车片价格
    function  import_trw_price1_fuck()
    {
        $i = 0;
        $item_filter = D('item_filter');
        $trw_price = D('trw_price');
        $trw_price_info = $trw_price->select();
        foreach ($trw_price_info as $k => $v) {
            $map['name'] = $v['name'];
            $map['type_id'] = $v['type_id'];
            $item_info = $item_filter->where($map)->find();
            if ($item_info) {
                $data['price'] = 0;
                $int = $item_filter->where($map)->save($data);
                if ($int) {
                    $i++;
                }
            }
        }
        echo $i;
    }

    //查询车品牌，车系，车型 配件的俄所有信息
    function get_item_set()
    {
        $i1 = $i2 = $i3 = $i4 = $i5 = 0;
        $carmodel = D('carmodel_bak2');
        $carbrandModel = D('carbrand');//保险类订单表
        $carseriesModel = D('carseries');//保险类订单表
        $carModel = D('carmodel');//保险类订单表
        $list = $carmodel->order('model_id')->select();
//		print_r($list);
        echo "<table><tr><th>编号</th><th>车名</th><th>机滤</th><th>空气滤</th><th>空调滤</th><th>雨刷</th><th>刹车片</th></tr>";
        foreach ($list as $k => $v) {
            //$catmodel = $carModel->where(array('model_id'=>$v['model_id']))->find();
            $carseries = $carseriesModel->where(array('series_id' => $v['series_id']))->find();
            $carbrand = $carbrandModel->where(array('brand_id' => $carseries['brand_id']))->find();
            $data[$k]['series_name'] = $carseries['series_name'];
            $data[$k]['model_name'] = $carseries['model_name'];
            $car_name = $carbrand['brand_name'] . $carseries['series_name'] . $v['model_name'];
            if ($v['item_set']) {
                $arr_item = unserialize($v['item_set']);
                if ($arr_item[1]) {
                    $str_filter = '有';
                    $i1++;

                } else {
                    $str_filter = '无';
                }
                if ($arr_item[2]) {
                    $str_filter2 = '有';
                    $i2++;
                } else {
                    $str_filter2 = '无';
                }
                if ($arr_item[3]) {
                    $str_filter3 = '有';
                    $i3++;
                } else {
                    $str_filter3 = '无';
                }
                if ($arr_item[4]) {
                    $str_filter4 = '有';
                    $i4++;
                } else {
                    $str_filter4 = '无';
                }
                if ($arr_item[5]) {
                    $str_filter5 = '有';
                    $i5++;
                } else {
                    $str_filter5 = '无';
                }

                echo "<tr><td>" . $v['model_id'] . "</td><td>" . $car_name . "</td><td>" . $str_filter . "</td><td>" . $str_filter2 . "</td><td>" . $str_filter3 . "</td><td>" . $str_filter4 . "</td><td>" . $str_filter5 . "</td></tr>";

            } else {
                echo "<tr><td>" . $v['model_id'] . "</td><td>" . $car_name . "</td><td>" . '无' . "</td><td>" . '无' . "</td><td>" . '无' . "</td><td>" . '无' . "</td><td>" . '无' . "</td></tr>";

            }

        }
        echo "</table>";
        echo "机滤：" . $i1 . "<br/>";
        echo "空气滤:" . $i2 . "<br/>";
        echo "空调滤：" . $i3 . "<br/>";
        echo "雨刷：" . $i4 . "<br/>";
        echo "刹车：" . $i5 . "<br/>";


    }

    //配件序列化  雨刷
    function item_set1111fqcd()
    {
        $carmodel = D('carmodel_bak2');
        $carwiper = D('carwiper_bak');
        $cartrw = D('modeltrw_bak');
        $item_filter = D('item_filter_bak2');
        /*$carmodel = D('carmodel');
		$carwiper = D('carwiper');
		$cartrw = D('modeltrw');
		$item_filter = D('item_filter');*/
        $info = $carwiper->order('id')->select();
        $info2 = $cartrw->select();

        foreach ($info as $k => $row2) {
            if ($row2['godwing']) {
                $map['name'] = $row2['godwing'];
                $map['type_id'] = 11;
                $info_item = $item_filter->where($map)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['maingw']) {
                $map2['name'] = $row2['maingw'];
                $map2['type_id'] = 12;
                $info_item = $item_filter->where($map2)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['vicegw']) {
                $map3['name'] = $row2['vicegw'];
                $map3['type_id'] = 13;
                $info_item = $item_filter->where($map3)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['windwing']) {
                $map4['name'] = $row2['windwing'];
                $map4['type_id'] = 14;
                $info_item = $item_filter->where($map4)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['vicewd']) {
                $map5['name'] = $row2['vicewd'];
                $map5['type_id'] = 15;
                $info_item = $item_filter->where($map5)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['firewing']) {
                $map6['name'] = $row2['firewing'];
                $map6['type_id'] = 16;
                $info_item = $item_filter->where($map6)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['vicefw']) {
                $map7['name'] = $row2['vicefw'];
                $map7['type_id'] = 17;
                $info_item = $item_filter->where($map7)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['special']) {
                $map8['name'] = $row2['special'];
                $map8['type_id'] = 18;
                $info_item = $item_filter->where($map8)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['mainfour']) {
                $map9['name'] = $row2['mainfour'];
                $map9['type_id'] = 19;
                $info_item = $item_filter->where($map9)->find();
                $id[] = $info_item['id'];
            }
            if ($row2['vicefour']) {
                $mapo['name'] = $row2['vicefour'];
                $mapo['type_id'] = 20;
                $info_item = $item_filter->where($mapo)->find();
                $id[] = $info_item['id'];
            }
            $arr_add = $id;

            if (!$row2['maingw'] && !$row2['vicegw'] && !$row2['windwing'] && !$row2['vicewd'] && !$row2['firewing'] && !$row2['vicefw'] && !$row2['special'] && !$row2['mainfour'] && !$row2['vicefour']) {
                $arr_add = array();
            }
            $mapp['model_name'] = $row2['carmadel'];

            $info_carmodel = $carmodel->where($mapp)->find();
            if ($info_carmodel['item_set']) {
                $arr_item = unserialize($info_carmodel['item_set']);
                $arr_item[4] = $arr_add;
                //array_push ( $arr_item ,  $arr_add );

                $data['item_set'] = serialize($arr_item);

                $pp['model_name'] = $row2['carmadel'];
                $int = $carmodel->where($pp)->save($data);
                if ($int) {
                    echo '111';
                }
            }
            unset($id);
            unset($arr_add);
        }
    }

//配件序列化刹车
    function item_set2222fqcd()
    {
        /*$carmodel = D('carmodel_bak2');
		$carwiper = D('carwiper_bak');
		$cartrw = D('modeltrw_bak');
		$item_filter = D('item_filter_bak2');*/
        $carmodel = D('carmodel');
        $carwiper = D('carwiper');
        $cartrw = D('modeltrw');
        $item_filter = D('item_filter');
//		$info = $carwiper->order('id')->limit(5)->select();
        $info2 = $cartrw->order('id')->select();

        foreach ($info2 as $k1 => $v) {
            if ($v['beforetrw']) {
                $mapa['name'] = $v['beforetrw'];
                $mapa['type_id'] = 7;
                $info_item = $item_filter->where($mapa)->find();
                $id2[] = $info_item['id'];
            }
            if ($v['aftertrw']) {
                $maps['name'] = $v['aftertrw'];
                $maps['type_id'] = 8;
                $info_item = $item_filter->where($maps)->find();
                $id2[] = $info_item['id'];
            }
            if (!$v['beforetrw'] && !$v['aftertrw']) {
                $id2 = array();
            }
            $mappp['model_name'] = $v['carmodel'];
            $info_carmodel = $carmodel->where($mappp)->find();
            if ($info_carmodel['item_set']) {
                $arr_item = unserialize($info_carmodel['item_set']);
                $arr_item[5] = $id2;
//				array_push ( $arr_item ,  $arr_add2 );
                $data2['item_set'] = serialize($arr_item);
                $int1 = $carmodel->where($mappp)->save($data2);
                if ($int1) {
                    echo '222';
                }
            }
            unset($id2);
        }

    }

    function insertDataToStaffOracle()
    {

        $model = M('tp_xieche.staff_oracle', 'xc_');
        $update = array(
            '1' => '6002',//admin
            '223' => '6002',//王俊炜
            '171' => '6003',//彭晓文
            '267' => '6004',//朱迎春
            '259' => '6005',//乔敬超
            '234' => '6006',//张美婷
            '243' => '6007',//黄美琴
            '242' => '6008',//李宝峰
            '266' => '6010',//黄赟
            '252' => '6009',//周祥金
            '268' => '6001',//周洋
            '269' => '6005',//寇建学
            '273' => '6011'//杨超
        );
        foreach ($update as $id => $val) {
            $model->where(array('admin_id' => $id))->save(array('staff_id' => $val));
        }
        exit;
        $staff = array(
            1 => array(
                'id' => '2014102900000025',
                'name' => 'admin'
            ),
            266 => array(
                'id' => '2014102900000041',
                'name' => '黄赟'
            ),
            252 => array(
                'id' => '2014102900000039',
                'name' => '周祥金'
            ),
            251 => array(
                'id' => '2014102900000041',
                'name' => '庄玉成'
            ),
            242 => array(
                'id' => '2014102900000037',
                'name' => '李宝峰'
            ),
            243 => array(
                'id' => '2014102900000035',
                'name' => '黄美琴'
            ),
            234 => array(
                'id' => '2014102900000033',
                'name' => '张美婷'
            ),
            259 => array(
                'id' => '2014102900000031',
                'name' => '乔敬超'
            ),
            182 => array(
                'id' => '2014102900000029',
                'name' => '王宇晨'
            ),
            171 => array(
                'id' => '2014102900000027',
                'name' => '张丹红'
            ),
            223 => array(
                'id' => '2014102900000025',
                'name' => '王俊炜'
            ),
            274 => array(
                'id' => '2014102900000029',
                'name' => '王宇晨'
            ),
            268 => array(
                'id' => '2014102900000023',
                'name' => '周洋'
            ),
            269 => array(
                'id' => '2014102900000031',
                'name' => '寇建学'
            ),
            273 => array(
                'id' => '2014102900000043',
                'name' => '杨超'
            ),
        );
        ksort($staff);
        //var_dump($staff);exit;
        foreach ($staff as $admin_id => $val) {
            $data = array(
                'admin_id' => $admin_id,
                'oracle_id' => $val['id'],
                'name' => $val['name']
            );
            $model->add($data);
        }
        exit;

    }

    function imsertData()
    {
        $image_model = M('tp_xieche.spreadpic', 'xc_');
        for ($i = 0; $i <= 10000; $i++) {
            $data = array(
                'nickname' => '保留号',
                'creat_time' => time()
            );
            $image_model->add($data);
        }
        echo 'over';
        exit;
    }

    function testWeixin()
    {
        echo 'weixinapi';
    }

    function fixOil()
    {
        return false;
        $res = $this->reservation_order_model->where(array('id' => 4314))->find();
        $item = unserialize($res['item']);

        foreach ($item as $key => &$val) {
            if ($key == 'oil_detail') {
                unset($val[50]);
            }
            if ($key == 'price') {
                unset($val['oil'][50]);
            }
            unset($val);
        }
        $data['item'] = serialize($item);
        $this->reservation_order_model->where(array('id' => 4314))->save($data);
    }

    //2014年数据整理：来源区域分布，每个月回头客数量和比列，订单来源渠道和不同渠道来源比例
    function dataFor2014()
    {
        //上门保养数据:
        $this->_carservice();
        //4s预约保养
        $this->_shopservice();
// 		//团购
// 		$this->_tuangou();
// 		//事故车
// 		$this->_shiguche();
    }

    function _tuangou()
    {
        $membercoupon = D('membercoupon');
        $data['pa'] = 0;
        $data['create_time'] = array(array('egt', '1388505600'), array('elt', '1420041600'));
        $a = $membercoupon->where($data)->count();
        $data['pa'] = 1;
        $b = $membercoupon->where($data)->count();
        $data['pa'] = 2;
        $c = $membercoupon->where($data)->count();
        $data['pa'] = 3;
        $d = $membercoupon->where($data)->count();
        $data['pa'] = 4;
        $f = $membercoupon->where($data)->count();
        $data['pa'] = 5;
        $g = $membercoupon->where($data)->count();
        $data['pa'] = 6;
        $h = $membercoupon->where($data)->count();
        unset($data['pa']);
        $all = $membercoupon->where($data)->count();
        echo "<b>团购券客户来源统计：</b></br>网站下单：" . $a . "&nbsp;&nbsp;平安：" . $b . "&nbsp;&nbsp;平安WAP：" . $c . "&nbsp;&nbsp;网站WAP：" . $d . "&nbsp;&nbsp;百车宝：" . $f . "&nbsp;&nbsp;大众点评：" . $g . "&nbsp;&nbsp;美女爆款：" . $h . "</br>合计：" . $all . "</br>";
        echo "<table><tr><th>团购每月回头客数据</th></tr><tr><th>月份</th><th>回头客</th><th>总数</th><th>百分比</th></tr>";

        for ($n = 1; $n < 13; $n++) {
            $year = substr(201401, 0, 4);
            $month = substr(201401, 4, 2);
            $S = date('Ymd', mktime(0, 0, 0, $month + $n - 1, 1, $year));
            $E = date('Ymd', mktime(0, 0, 0, $month + $n, 1, $year));
            $start = strtotime($S);
            $end = strtotime($E);
            $map['create_time'] = array(array('egt', $start), array('elt', $end));
            $uid = $membercoupon->where($map)->Distinct(true)->field('uid')->select();
            //echo $membercoupon->getLastsql();
            $buyed_count = 0;
            foreach ($uid as $k => $v) {
                if ($v['uid'] == '6591') {
                    unset($uid[$k]);
                }
                $mad['create_time'] = array('lt', $start);
                $mad['uid'] = $v['uid'];
                $buyed = $membercoupon->where($mad)->count();
                //echo $membercoupon->getLastsql();
                if ($buyed > 0) {
                    $buyed_count++;
                }
            }
            //获取每月用户合计
            $monthly_count = count($uid);
            $m = $month + $n - 1;
            $percent = $buyed_count / $monthly_count;
            echo '<tr><td>' . $m . '</td><td>' . $buyed_count . '</td><td>' . $monthly_count . '</td><td>' . (sprintf("%.4f", $percent) * 100) . '%</td></tr>';
            //echo '_回头率：'.(sprintf("%.2f", $percent)*100).'%</br>';

        }
        echo "</table>";
    }

    function _shiguche()
    {
        echo "<table><tr><th>事故车每月回头客数据</th></tr><tr><th>月份</th><th>回头客</th><th>总数</th><th>百分比</th></tr>";
        $bidorder = D('bidorder');
        for ($n = 1; $n < 13; $n++) {
            $year = substr(201401, 0, 4);
            $month = substr(201401, 4, 2);
            $S = date('Ymd', mktime(0, 0, 0, $month + $n - 1, 1, $year));
            $E = date('Ymd', mktime(0, 0, 0, $month + $n, 1, $year));
            $start = strtotime($S);
            $end = strtotime($E);
            $map['create_time'] = array(array('egt', $start), array('elt', $end));
            $uid = $bidorder->where($map)->Distinct(true)->field('uid')->select();
            //echo $membercoupon->getLastsql();
            $buyed_count = 0;
            foreach ($uid as $k => $v) {
                if ($v['uid'] == '6591') {
                    unset($uid[$k]);
                }
                $mad['create_time'] = array('lt', $start);
                $mad['uid'] = $v['uid'];
                $buyed = $bidorder->where($mad)->count();
                //echo $membercoupon->getLastsql();
                if ($buyed > 0) {
                    $buyed_count++;
                }
            }
            //获取每月用户合计
            $monthly_count = count($uid);
            $m = $month + $n - 1;
            $percent = $buyed_count / $monthly_count;
            echo '<tr><td>' . $m . '</td><td>' . $buyed_count . '</td><td>' . $monthly_count . '</td><td>' . (sprintf("%.4f", $percent) * 100) . '%</td></tr>';
            //echo '_回头率：'.(sprintf("%.2f", $percent)*100).'%</br>';

        }
        echo "</table>";
    }


    function month_date()
    {

    }

    function _shopservice()
    {
        //来源分布：
        echo '<h1>4s店预约订单统计</h1>';
        $where['truename'] = $where2['truename'] = $where3['truename'] = array('notlike', '%测%');
        $where['create_time'] = $where2['create_time'] = $where3['create_time'] = array('egt', 1388505600);
        $where['remark'] = $where2['remark'] = $where3['remark'] = array('notlike', '%脚本订单,不要打电话%');
        echo '来源分布统计：&nbsp;&nbsp;';
        $countAll = $this->order_model->where($where)->count();
        echo '总订单：' . $countAll . '&nbsp;&nbsp;';
        //手机
        $where['is_app'] = array('eq', 1);
        $countWeixin = $this->order_model->where($where)->count();
        //echo $this->reservation_order_model->getLastSql();exit;
        echo '手机来源订单：' . $countWeixin . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($countWeixin / $countAll * 100) . '%</font>&nbsp;&nbsp;';
        unset($where['is_app']);
        //后台
        $where['operator_id'] = array('neq', 0);
        $countDaixiadan = $this->order_model->where($where)->count();
        echo '后台带下单订单：' . $countDaixiadan . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($countDaixiadan / $countAll * 100) . '%</font>&nbsp;&nbsp;';
        //网站
        $countPc = $countAll - $countWeixin - $countDaixiadan;
        echo '网站来源订单：' . $countPc . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($countPc / $countAll * 100) . '%</font>&nbsp;&nbsp;' . '<br />';

        //来源渠道
        echo '来源渠道统计：&nbsp;&nbsp;';
        $where2['order_type'] = array('eq', 0);
        $baoyangPriceCount = $this->order_model->where($where2)->count();
        echo '普通券订单：' . $baoyangPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($baoyangPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 1);
        $jiancePriceCount = $this->order_model->where($where2)->count();
        echo '现金券订单：' . $jiancePriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($jiancePriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 2);
        $taobaoPriceCount = $this->order_model->where($where2)->count();
        echo '团购卷订单：' . $taobaoPriceCount . '比例：<font style="color:red">' . ($taobaoPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;' . '<br>';


        //每个月回头客数量
        $old_order = array();
        for ($n = 1; $n < 13; $n++) {
            $year = substr(201401, 0, 4);
            $month = substr(201401, 4, 2);
            $S = date('Ymd', mktime(0, 0, 0, $month + $n - 1, 1, $year));
            $E = date('Ymd', mktime(0, 0, 0, $month + $n, 1, $year));
            $start = strtotime($S);
            $end = strtotime($E);
            $where3['create_time'] = array(array('egt', $start), array('elt', $end));
            $data = $this->order_model->field('id,mobile')->where($where3)->select();
            if ($data) {
                $old_order[$n] = 0;
                foreach ($data as $val) {
                    $where4 = array(
                        'mobile' => $val['mobile'],
                        'create_time' => array('lt', $start),
                        'remark' => array('notlike', '%脚本订单,不要打电话%')
                    );
                    $isExist = $this->order_model->where($where4)->count();
                    //echo $this->reservation_order_model->getLastSql();
                    if ($isExist) {
                        //var_dump($isExist) ;exit;
                        $old_order[$n] += 1;
                    }
                }
            }

        }
        if ($old_order) {
            foreach ($old_order as $mouth => $outnum) {
                echo '第' . $mouth . '月回头客统计：' . $outnum . '<br>';
            }
        }
    }

    function _carservice()
    {
        echo '<h1>上门保养统计</h1>';
        //来源分布：
        $where['truename'] = $where2['truename'] = $where3['truename'] = array('notlike', '%测%');
        //$where['create_time'] = $where2['create_time'] = $where3['create_time'] = array('egt',1388505600);
        echo '来源分布统计：&nbsp;&nbsp;';
        $countAll = $this->reservation_order_model->count();
        echo '总订单：' . $countAll . '&nbsp;&nbsp;';
        //微信
        $where['pa_id'] = array('gt', 1);
        $countWeixin = $this->reservation_order_model->where($where)->count();
        //echo $this->reservation_order_model->getLastSql();exit;
        echo '微信来源订单：' . $countWeixin . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($countWeixin / $countAll * 100) . '%</font>&nbsp;&nbsp;';
        unset($where['pa_id']);
        //后台
        $where['remark'] = array('like', '%代下单%');
        $countDaixiadan = $this->reservation_order_model->where($where)->count();
        //echo $this->reservation_order_model->getLastSql();exit;
        echo '后台带下单订单：' . $countDaixiadan . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($countDaixiadan / $countAll * 100) . '%</font>&nbsp;&nbsp;';
        //网站
        $countPc = $countAll - $countWeixin - $countDaixiadan;
        echo '网站来源订单：' . $countPc . '比例：<font style="color:red">' . ($countPc / $countAll * 100) . '</font>%&nbsp;&nbsp;' . '<br />';
        //来源渠道
        echo '来源渠道统计：&nbsp;&nbsp;';
        $where2['order_type'] = array('eq', 1);
        $baoyangPriceCount = $this->reservation_order_model->where($where2)->count();//上门保养
        echo '上门保养：' . $baoyangPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($baoyangPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 2);
        $jiancePriceCount = $this->reservation_order_model->where($where2)->count();//上门保养检测
        echo '上门检测：' . $jiancePriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($jiancePriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 3);
        $taobaoPriceCount = $this->reservation_order_model->where($where2)->count();//淘宝已支付订单价钱
        echo '淘宝已支付订单：' . $taobaoPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($taobaoPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 4);
        $discountPriceCount = $this->reservation_order_model->where($where2)->count();//淘宝免99服务费价钱
        echo '淘宝免99服务费：' . $discountPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($discountPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 7);
        $discountHuangPriceCount = $this->reservation_order_model->where($where2)->count();//黄喜力机油套餐
        echo '黄喜力机油套餐：' . $discountHuangPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($discountHuangPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 8);
        $discountLanPriceCount = $this->reservation_order_model->where($where2)->count();//蓝喜力机油套餐
        echo '蓝喜力机油套餐：' . $discountLanPriceCount . '&nbsp;&nbsp;' . '比例：<font style="color:red">' . ($discountLanPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;';

        $where2['order_type'] = array('eq', 9);
        $discountHuiPriceCount = $this->reservation_order_model->where($where2)->count();//灰喜力机油套餐
        echo '灰喜力机油套餐：' . $discountHuiPriceCount . '比例：<font style="color:red">' . ($discountHuiPriceCount / $countAll * 100) . '%</font>&nbsp;&nbsp;<br>';

        //每个月回头客数量
        $old_order = array();
        for ($n = 1; $n < 13; $n++) {
            $year = substr(201401, 0, 4);
            $month = substr(201401, 4, 2);
            $S = date('Ymd', mktime(0, 0, 0, $month + $n - 1, 1, $year));
            $E = date('Ymd', mktime(0, 0, 0, $month + $n, 1, $year));
            $start = strtotime($S);
            $end = strtotime($E);
            $where3['create_time'] = array(array('egt', $start), array('elt', $end));
            $data = $this->reservation_order_model->field('id,mobile')->where($where3)->select();
            if ($data) {
                $old_order[$n] = 0;
                foreach ($data as $val) {
                    $where4 = array(
                        'mobile' => $val['mobile'],
                        'create_time' => array('lt', $start)
                    );
                    $isExist = $this->reservation_order_model->where($where4)->count();
                    //echo $this->reservation_order_model->getLastSql();
                    if ($isExist) {
                        //var_dump($isExist) ;exit;
                        $old_order[$n] += 1;
                    }
                }
            }

        }
        if ($old_order) {
            foreach ($old_order as $mouth => $outnum) {
                echo '第' . $mouth . '月回头客统计：' . $outnum . '<br>';
            }
        }


    }

    function testOil()
    {

        $reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $where = array(
            'create_time' => array('gt', 1420819200),
            'order_type' => array('neq', 2)
        );

        $data = $reservation_order_model->where($where)->select();

        $carmodel_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        foreach ($data as $order_info) {
            $model_id = $order_info['model_id'];
            $style_param['model_id'] = $model_id;
            $car_style = $carmodel_model->where($style_param)->find();
            $oil_num = ceil($car_style['oil_mass']);
            if ($oil_num > 4) {
                $order_items = unserialize($order_info['item']);
                echo $order_info['id'] . '<br>';
                var_dump($order_items);

            }

        }
    }

    function inqd()
    {
        $rankprize = D("rankprize");
        $data['weixin_id'] = "oF49ruFQSgL5TGDy5yEq92skI1EQ";
        $data['type'] = 7;
        $data['num'] = 1;
        $data['username'] = "test50";
        $data['mobile'] = "15555555555";
        $data['address'] = "上海市";
        $data['carmodel'] = "宝马奥迪";
        $data['create_time'] = microtime(sec);
        $html = "";
        for ($i = 0; $i < $_GET['num']; $i++) {
            $rankprize->add($data);
        }
        echo $i . "条添加成功";
    }

    public function testBug()
    {
        $data = array(
            'code' => 'fqcd123223',
            'mobile' => 13111111111,
            'name' => iconv("utf-8", "gb2312//IGNORE", 'test'),
            'remark' => iconv("utf-8", "gb2312//IGNORE", 'test'),
            'staff_id' => '2014102900000025',
            'task' => 2
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://www.fqcd.3322.org/api.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:192.168.1.1, CLIENT-IP:192.168.1.1'));  //构造IP
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.sina.com.cn/');   //构造来路
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "999999999");
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_PROXY, $wk_ip[$jyi]);
        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        //curl_setopt ($ch, CURLOPT_TIMEOUT, 3);
        $out = curl_exec($ch);
        curl_close($ch);
        var_dump($out);
    }

    public function curl_test($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:192.168.1.1, CLIENT-IP:192.168.1.1'));  //构造IP
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.sina.com.cn/');   //构造来路
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "999999999");
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_PROXY, $wk_ip[$jyi]);
        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $out = curl_exec($ch);
        curl_close($ch);
        return $out;
    }


    function insertDataForCall()
    {
        //昨天车主下的订单已经被客服确认，且服务时间是明天及以后的
        $reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $model_operatelog = M("tp_xieche.operatelog", "xc_");


        $today = strtotime(date('Y-m-d', time()));
        $yesterday = $today - 3600 * 24;
        $tomorrow = $tomorrow + 3600 * 24;

        $staff = array(
            223 => '2014102900000025',//王俊炜
            171 => '2014102900000027',//彭晓文
            182 => '2014102900000029',//张丹红
            241 => '2014102900000031',//朱笑龙
            234 => '2014102900000033',//张美婷
            243 => '2014102900000035',//黄美琴
            242 => '2014102900000037',//李宝峰
            251 => '2014102900000041',//庄玉成
            252 => '2014102900000039'//周祥金
        );

        $where['create_time'] = array(array('egt', $yesterday), array('elt', $today));
        $where['order_time'] = array(array('egt', $tomorrow));
        $where['status'] = array('lt', 8);
        $datas = $reservation_order_model->where($where)->select();
        $return = array();
        if ($datas) {
            $url = 'http://www.fqcd.3322.org/api.php';
            foreach ($datas as $data) {
                $technician_id = $data['technician_id'];
                $remark = '订单号:' . $data['id'] . '车牌:' . $data['licenseplate'] . '地址:' . $data['address'] . '上门时间:' . date('Y-m-d-H:i:s', $data['order_time']);
                //技师
                if ($technician_id) {
                    $condition['id'] = $technician_id;
                    $technician_info = $technician_model->where($condition)->find();
                    $remark .= '技师姓名:' . $technician_info['truename'];
                }
                //客服信息
                if ($data['admin_id']) {
                    $operate_id = $data['admin_id'];
                } else {
                    $operate_info = $model_operatelog->where(array('oid' => $data['id']))->order('create_time asc')->find();
                    $operate_id = $operate_info['operate_id'];
                }

                $post = array(
                    'code' => 'fqcd123223',
                    'mobile' => $data['mobile'],
                    'name' => iconv("utf-8", "gb2312//IGNORE", $data['truename']),
                    'remark' => iconv("utf-8", "gb2312//IGNORE", $remark),
                    'staff_id' => $staff[$operate_id],
                    'code' => 'fqcd123223',
                    'task' => 1
                );

                if (!$operate_id || !$staff[$operate_id]) {
                    $return['error'][] = $data['mobile'];
                } else {
                    //var_dump($post);
                    //$return['send'][] = $this->curl($url,$post);
                    sleep(1);
                }
            }
        }
        //程序日志
        $this->addCodeLog('crontab', var_export($return, true));


        //插入客服信息到问答表里
        $mAsk = M("tp_xieche.ask", "xc_");
        $access_token = $this->getWeixinToken();

        $today = strtotime(date('Y-m-d'));
        $start_time = $today - 86400;
        $end_time = $today - 400;

        $data = '{
		"starttime" : ' . $start_time . ',
		"endtime" 	: ' . $end_time . ',
		"pagesize" 	: 500,
		"pageindex" : 1,		
		}';
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getrecord?access_token=' . $access_token;
        $ret = $this->curl($url, $data);

        if ($ret) {
            $ret = json_decode($ret, true);
            $list = $ret['recordlist'];
            $mPadatatest = M('tp_xieche.padatatest', 'xc_');
            if ($list) {
                foreach ($list as $val) {
                    $weixin_id = $val['openid'];
                    $weixiData = $mPadatatest->field('nickname')->where(array('FromUserName' => $weixin_id))->find();
                    $insert = array(
                        'weixin_id' => $weixin_id,
                        'nickname' => $weixiData['nickname'],
                        'worker' => $val['worker'],
                        'opercode' => $val['opercode'],
                        'text' => $val['text'],
                        'create_time' => $val['time'],
                    );
                    $mAsk->add($insert);
                    $this->addCodeLog('crontab', $mAsk->getLastSql());
                }
            }
        }

        $this->submitCodeLog('crontab');
        exit;
    }


    /*
	 * 查询价钱错误订单
	*/
    function findErrorOrder()
    {

        $reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $datas = $reservation_order_model->order('id desc')->select();
        $error = array();
        foreach ($datas as $data) {
            if ($data['status'] == 8) {    //作废订单
                continue;
            }
            if ($data['order_type'] == 2) {    //上门检测订单
                if ($data['amount'] > 0) {
                    $error[] = 'e:' . $data['id'];
                }
                continue;
            }
            $item = unserialize($data['item']);
            $amount = $data['amount'];
            $total = 99;
            //echo $total.'+';
            $oilPrice = $item['price']['oil'];
            $oilPriceStr = 0;
            foreach ($oilPrice as $val) {
                $total += $val;
                $oilPriceStr += $val;
                //	echo $val.'+';
            }
            $filterPrice = $item['price']['filter'];
            $filterPriceStr = 0;
            foreach ($filterPrice as $val) {
                $total += $val;
                $filterPriceStr += $val;
                //	echo $val.'+';
            }
            $kongqiPrice = $item['price']['kongqi'];
            foreach ($kongqiPrice as $val) {
                $total += $val;
                //	echo $val.'+';
            }
            $kongtiaoPrice = $item['price']['kongtiao'];
            foreach ($kongtiaoPrice as $val) {
                $total += $val;
                //	echo $val;
            }
            $replaceCode = $data['replace_code'];
            $sub = $this->get_codevalue($replaceCode);

            $total -= $sub;

            $dikou = $this->get_typeprice($data['order_type'], $oilPriceStr, $filterPriceStr);
            $total -= $dikou;

            //echo '-'.$sub.'-'.$dikou.'算出来的价格为：'.$total.'总价为：'.$amount.' ('.$data['id'].$data['operator_remark'].')<br>';

            if ($total != (int)$amount) {
                if ($data['order_type'] == 3 && $data['amount'] == 0) {    //淘宝价钱永远是0
                    continue;
                }
                $error[] = $data['id'];
            }
        }
        var_dump($error);
        exit;
    }


    /*
	 * 修复进销存bug
	* bright
	*/
    function fixStorehouse()
    {
        $storehouse_item_model = M('tp_xieche.storehouse_item', 'xc_');//仓库数据详情表
        $reservation_order_model = M('tp_xieche.reservation_order', 'xc_');  //预约订单
        $finance_model = M('tp_xieche.finance', 'xc_');  //财务表

        $order_lists = $reservation_order_model->select();
        $store_lists = $storehouse_item_model->field('order_id')->where(array('lock_status' => 2))->select();
        foreach ($store_lists as $store_list) {
            $order_ids[] = $store_list['order_id'];    //从库存表里查出所有存在的order_id
        }
        foreach ($order_lists as $order_list) {
            $remain_ids[] = $order_list['id'];    //订单里面剩余的order_id，除去删除掉的
        }
        $delete_arr = array_diff($order_ids, $remain_ids);
        $need_delete_order_ids = implode(',', $delete_arr);

        //var_dump($need_delete_order_ids);exit;
        //解绑删除了的测试订单
        $where = array(
            'order_id' => array('in', $need_delete_order_ids)
        );
        $res = $storehouse_item_model->where($where)->save(array('lock_status' => 1));
        var_dump($res);

        //修复财务数据
        $where1_1 = array(
            'oid' => array('in', $need_delete_order_ids)
        );
        $res1 = $finance_model->where($where1_1)->delete();
        var_dump($res1);

        //解帮作废的订单
        $where2 = array('status' => 8);
        $order_lists2 = $reservation_order_model->where($where2)->select();
        foreach ($order_lists2 as $order_list2) {
            $order_ids2[] = $order_list2['id'];
        }
        $where3 = array(
            'order_id' => array('in', implode(',', $order_ids2))
        );
        $res2 = $storehouse_item_model->where($where3)->save(array('lock_status' => 1));
        var_dump($res2);
        //修复财务数据
        $where4 = array(
            'oid' => array('in', implode(',', $order_ids2))
        );
        $res4 = $finance_model->where($where4)->delete();
        var_dump($res4);

    }

    /*
	 * 上门保养车型参数缓存的添加
	* bright
	*/
    function setCacheDataOfCarservice()
    {
        $car_brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $car_style_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $car_model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $brand_list = $car_brand_model->field('brand_id,brand_name')->select();
        $test = $brand_list;
        $top_name = array('奥迪', '别克', '大众', '丰田', '现代', '本田', '福特', '宝马', '日产', '起亚');
        foreach ($test as $k => $v) {
            if (in_array($v['brand_name'], $top_name)) {
                $condition['brand_id'] = $v['brand_id'];
                $car_style_list = $car_style_model->field('series_id,series_name')->where($condition)->select();
                $v['child'] = $car_style_list;
                foreach ($v['child'] as $key => $val) {
                    $condition2['series_id'] = $val['series_id'];
                    $car_model_list = $car_model_model->field('model_id,model_name')->where($condition2)->select();
                    //var_dump($this->car_model_model->getLastSql());exit;
                    $v['child'][$key]['child'] = $car_model_list;
                }
                $t[] = $v;
            }

        }
        echo json_encode($t);
        exit;
    }

    /*
	 * 模板消息接口测试
	 */
    function test_template()
    {
        $data = array(
            'wx_id' => 'oF49ruFQSgL5TGDy5yEq92skI1EQ',
            //'wx_id'=>'oF49ruNY7D9LmL97pIdRriwo5wZ4',//guigui
            //'wx_id'=>'oF49ruFl3_Q_9oMDW2ikD1nnGvEQ',//zz
            'return_url' => 'http://www.xieche.com.cn/mobile',
            'template_id' => 1,
            'title' => '您的爱车已完成38项爱车深度体检',
            'key1' => '车辆检测',
            'key2' => '38项爱车深度体检',
            'key3' => date('Y-m-d H:i:s', time()),
            'remark' => ''
        );
        $ret = $this->send_weixin_template_msg($data);
        $ret = json_decode($ret, true);
        if ($ret['errcode'] == 0) {
            echo 'success';
        }
    }

    /*
		跑脚本
	*/
    function test_coupon()
    {
        $coupon = D('coupon');
        $membercoupon = D('membercoupon');
        $data = $membercoupon->select();
        foreach ($data as $k => $v) {
            if (!$v['shop_ids']) {
                $coupon_data = $coupon->where(array('id' => $v['coupon_id']))->find();
                $membercoupon->where(array('membercoupon_id' => $v['membercoupon_id']))->save(array('shop_ids' => $coupon_data['shop_id']));
                echo $membercoupon->getlastSql();
            }
        }

    }

    function tt1()
    {
        $pwd = substr(md5('123'), 5, 20);
        echo $pwd;
    }

    /*
		跑脚本
	*/
    function test_coupon1()
    {
        $coupon = D('coupon');
        $membercoupon = D('membercoupon');
        $data = $membercoupon->where(array('membercoupon_id' => '2697'))->find();

        print_r($data);

    }

    /*
		PHP生成二维码
	*/
    function sc2()
    {
        include("./ThinkPHP/Extend/Library/ORG/Qrcode/phpqrcode.php");
        $data = 'www.xieche.com.cn';
        // 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'L';
        // 点的大小：1到10
        $matrixPointSize = 4;
        // 生成的文件名
        $path = "UPLOADS/Shop/Logo/";
        //if (!file_exists($path)){
        ///mkdir($path);
        //}

        $filename = $path . $errorCorrectionLevel . '.' . $matrixPointSize . '.png';
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        echo "<img src='$filename' />";
    }

    function Post_hundredcar()
    {
        $this->testModel = D('test');
        $this->HundredorderModel = D('hundredorder');
        //$Hundredorder = $this->HundredorderModel->where(array('order_id'=>$order_id))->find();
        if ($order_status == '1') {
            $status = '1';//已受理
        } elseif ($order_status == '2') {
            $status = '3';//已完成
        } elseif ($order_status == '-1') {
            $status = '0';//已取消
        }

        $data_car['boid'] = '20140416145626516';//订单号
        $data_car['status'] = '1';//状态
        $strmd5 = $this->sign('pn13jlf32h26im5vwkods8srjh67uq9m', $data_car);


        $header['appkey'] = 'lo175muov';
        $header['sign'] = $strmd5;
        $pa_result = $this->Api_toBCB("http://www.baichebao.com/update_order", $data_car, $header);


        $this->testModel->add(array('imNo' => $pa_result));
        echo $strmd5;
    }


    /*
		@author:chf
		@function:百车宝密码接口
		@time:2014-05-15
	*/
    function sign($secretKey, $params)
    {
        ksort($params);
        $string = '';
        foreach ($params as $key => $val) {
            $string .= "{$key}={$val}";
        }
        $string .= $secretKey;

        return strtoupper(md5($string));
    }

    /*  @author:chf
     * @function:发送数据(百车宝)
     * @time:2014-01-16;
     */
    function Api_toBCB($url, $data, $header)
    {
        $host = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    function test()
    {
        $a = file_get_contents('http://card.people.com.cn/forecast/exponentcast.jsp?expn=3');
        $b = iconv('GB2312', 'UTF-8', $a);
        $str = preg_replace("@<script(.*?)</script>@is", "", $b);
        $str = preg_replace("@<iframe(.*?)</iframe>@is", "", $str);
        $str = preg_replace("@<style(.*?)</style>@is", "", $str);
        $str = preg_replace("@<(.*?)>@is", "", $str);
        //先截取是哪个地理地理位置上海。。。。直道最后
        $find = "上海";
        $findLen = strlen($find);
        $tmp = stripos($str, $find);//得到上海开始的位数
        $city = mb_substr($str, $tmp, -1);//获取地理位置截取前面没用的
        //算出上海后的一个句号
        $find_F = "。";
        $findLen_F = strlen($find_F);
        $tmp_f = stripos($city, $find_F);
        //
        $lose = mb_substr($str, $tmp, $tmp_f);//得到开始的位数和结束的位数截取所需要的位数
        echo $lose . "。";
    }


    //帮平安100礼品券领取
    function paaddsale()
    {
        $this->memberModel = D('member');//用户表
        $this->membersalecouponModel = D('membersalecoupon');//领券表
        $this->PacarcodeModel = D('pacarcode');//领券表
        $this->SalecouponModel = D('salecoupon');//用户表
        $member = $this->memberModel->where(array('fromstatus' => '37'))->select();
        foreach ($member as $k => $v) {
            $salecount = $this->membersalecouponModel->where(array('uid' => $v['uid'], 'salecoupon_id' => '4', 'mobile' => $v['mobile']))->count();
            if ($salecount == '0') {
                $Pacarcode = $this->PacarcodeModel->where(array('status' => '0'))->find();
                $this->PacarcodeModel->where(array('id' => $Pacarcode['id']))->save(array('status' => '1'));
                $membersalesql['id'] = '4';//给平安发100元优惠券 红包ID
                $salecoupon = $this->SalecouponModel->where($membersalesql)->find();
                //插入membersalecoupon表
                $Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
                $Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
                $Membersalecoupon['coupon_type'] = $salecoupon['coupon_type'];
                $Membersalecoupon['mobile'] = $v['mobile'];
                $Membersalecoupon['money'] = $salecoupon['coupon_amount'];//红包金额
                $Membersalecoupon['create_time'] = time();
                $Membersalecoupon['start_time'] = $salecoupon['start_time'];
                $Membersalecoupon['end_time'] = $salecoupon['end_time'];
                $Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
                $Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];

                $Membersalecoupon['coupon_code'] = $Pacarcode['coupon_code'];

                $Membersalecoupon['from'] = 'pa';//来源
                $Membersalecoupon['uid'] = $v['uid'];
                $membersalecoupon_id = $this->membersalecouponModel->add($Membersalecoupon);
            }

        }
        echo 'ok';
        exit;

    }

    //平安礼品券
    function add_pa()
    {
        $this->CarcodeModel = D('pacarcode');
        //echo "第5批已经刷完";exit;
        for ($a = 1; $a <= 10000; $a++) {
            $coupon_code = rand(1234567891, 999999999);
            $coupon_code = $coupon_code;
            $count = $this->CarcodeModel->where(array('coupon_code' => $coupon_code))->count();
            if ($count == 0) {
                $data['coupon_code'] = $coupon_code;
                $data['create_time'] = time();
                $data['status'] = '0';
                $data['pici'] = '3';
                $this->CarcodeModel->add($data);
            } else {
                $a--;
            }
        }
        echo "ok";
    }

    //刮刮乐礼品券的脚本
    function add_gua()
    {
        $this->GuacodeModel = D('guacode');
        //echo "第5批已经刷完";exit;
        for ($a = 1; $a <= 10000; $a++) {
            //$coupon_code = $this->get_coupon_code();
            $coupon_code = rand(1234567891, 999999999);
            $coupon_code = $coupon_code;
            $count = $this->GuacodeModel->where(array('coupon_code' => $coupon_code))->count();
            if ($count == 0) {
                $data['coupon_code'] = $coupon_code;
                $data['create_time'] = time();
                $data['status'] = '0';
                $data['pici'] = '1';
                $this->GuacodeModel->add($data);
            } else {
                $a--;
            }
        }
        echo "ok";
    }

    //上门保养抵扣券的脚本
    function add_carservice()
    {
        $this->CarservicecodeModel = D('carservicecode');
//        echo "第81批已经刷完";exit;
        for ($a = 1; $a <= 200; $a++) {
            //$coupon_code = $this->get_coupon_code();
            $coupon_code = rand(12345678910, 99999999999);
            $coupon_code = $coupon_code;
            $count = $this->CarservicecodeModel->where(array('coupon_code' => $coupon_code))->count();
            if ($count == 0) {
                $data['coupon_code'] = $coupon_code;
                $data['create_time'] = time();
                $data['end_time'] = '1451577600';
                $data['status'] = '0';
                $data['pici'] = '109';
                $data['remark'] = '检测转保养首单免99元服务费';
                $this->CarservicecodeModel->add($data);
            } else {
                $a--;
            }
        }
        echo "ok";
    }

    //上门保养抵扣券的脚本
    function add_carservice_linshi()
    {
        $this->CarservicecodeModel = D('carservicecode');
        //echo "第63批已经刷完";exit;
        $array = array();
        for ($a = 1; $a <= 20; $a++) {
            //$coupon_code = $this->get_coupon_code();
            $coupon_code = rand(12345678910, 99999999999);
            $coupon_code = 'a' . $coupon_code;
            $array[] = $coupon_code;
            if (in_array($coupon_code, $array)) {
                echo $coupon_code . "</br>";
            } else {
                $a--;
            }
        }
        echo "ok";
    }


    //上门保养现金券的脚本,已规定位数必须11位
    function add_carservice_xianjin()
    {
        $this->CarservicecodeModel = D('carservicecode');
        //echo "第25批已经刷完";exit;
        for ($a = 1; $a <= 10000; $a++) {
            $b = 't';
            //$c = '268';//两位是面值，300就是03，1000就是10
            //$d = '1512';//有效期四位，1512就是15年12月底
            //9位随机数，拼上9个数字之和的个位数
            $e = rand(1234567890, 9999999999);
            //随机数各位数求和并且取最后一位
            //$check = str_split(array_sum(str_split($e)));
            //$f = $check['1'];
            $coupon_code = $b . $e;//拼接成券码
            //$coupon_code = rand(1234,9999);
            $count = $this->CarservicecodeModel->where(array('coupon_code' => $coupon_code))->count();
            if ($count == 0) {
                $data['coupon_code'] = $coupon_code;
                $data['create_time'] = time();
                $data['end_time'] = '';
                $data['status'] = '00';
                $data['pici'] = '128';
                $data['type'] = '2';
                $data['source_type'] = '0';
                $data['remark'] = '平安苏州金美孚套餐';
                $this->CarservicecodeModel->add($data);
            } else {
                $a--;
            }
        }
        echo "okkkk";
        exit;
    }

    //上门保养好车况推广套餐券码
    function add_carservice_haochekuang()
    {
        $this->CarservicecodeModel = D('carservicecode');
        //echo "第70批已经刷完";exit;
        for ($a = 8701761; $a <= 8703260; $a++) {
            $coupon_code = 'j' . $a;
            $data['coupon_code'] = $coupon_code;
            $data['create_time'] = time();
            $data['end_time'] = '1451577600';
            $data['status'] = '0';
            $data['pici'] = '114';
            $data['remark'] = '好车况-济南';
            $this->CarservicecodeModel->add($data);
        }
        echo "ok";
    }

    /*	
		@author:wwy
		@function:刮刮乐刮到之后领取礼品券的流程
		@time:2014-08-13
	*/
    function gualogion()
    {
        $this->MemberModel = D('member');//刮刮乐抵用码表
        $this->MembersalecouponModel = D('membersalecoupon');//刮刮乐抵用码表
        $this->SalecouponModel = D('salecoupon');//刮刮乐抵用码表
        $this->GuacodeModel = D('guacode');//刮刮乐抵用码表
        $this->model_sms = D('sms');//刮刮乐抵用码表
        $mobile = $_POST['mobile'];
        //判断验证码
        if ($mobile) {
            $map['mobile'] = $mobile;
            $res = $this->MemberModel->where(array('mobile' => $map['mobile'], 'status' => '1'))->find();
            if ($res) {
                $_SESSION['uid'] = $res['uid'];
            } else {
                $member_data['mobile'] = $mobile;
                $member_data['password'] = md5($_POST['mobile']);
                $member_data['reg_time'] = time();
                $member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
                $member_data['fromstatus'] = '38';//刮刮乐50元券
                $member['uid'] = $this->MemberModel->data($member_data)->add();
                /*
                $send_add_user_data = array(
                    'phones' => $mobile,
                    'content' => '您已注册成功，您可以使用您的手机号码' . $mobile . '，密码' . $_POST['mobile'] . '来登录携车网，客服4006602822。',
                );
                $this->curl_sms($send_add_user_data);*/

                // dingjb 2015-09-29 12:01:57 切换到云通讯
                $send_add_user_data = array(
                    'phones'  => $mobile,
                    'content' => array(
                        $mobile,
                        $_POST['mobile']
                    )
                );
                $this->curl_sms($send_add_user_data, null, 4, '37653');

                $send_add_user_data['sendtime'] = time();
                $this->model_sms->data($send_add_user_data)->add();

                $model_memberlog = D('Memberlog');
                $data['createtime'] = time();
                $data['mobile'] = $mobile;
                $data['memo'] = '用户注册';
                $model_memberlog->data($data)->add();
                $res = $this->MemberModel->where(array('mobile' => $mobile, 'status' => '1'))->find();
                $_SESSION['uid'] = $res['uid'];
            }

            $Membersalecount = $this->MembersalecouponModel->where(array('mobile' => $mobile, 'salecoupon_id' => '6'))->count();


            if ($Membersalecount == '0') {

                $Guacode = $this->GuacodeModel->where(array('status' => '0'))->find();

                $this->GuacodeModel->where(array('id' => $Guacode['id']))->save(array('status' => '1'));

                $membersalesql['id'] = '6';//给平安发100元优惠券 红包ID
                $salecoupon = $this->SalecouponModel->where($membersalesql)->find();

                //插入membersalecoupon表
                $time = time();        //修改start_time - 90天过期  @liuhui
                $expireTime = $time + 90 * 86400;
                $Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
                $Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
                $Membersalecoupon['coupon_type'] = $salecoupon['coupon_type'];
                $Membersalecoupon['mobile'] = $mobile;
                $Membersalecoupon['money'] = $salecoupon['coupon_amount'];//红包金额
                $Membersalecoupon['create_time'] = $time;
                $Membersalecoupon['start_time'] = $time;
                $Membersalecoupon['end_time'] = $expireTime;
                $Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
                $Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];

                $Membersalecoupon['coupon_code'] = $coupon_code = $Guacode['coupon_code'];

                $Membersalecoupon['from'] = 'gua';//来源
                $Membersalecoupon['uid'] = $this->GetUserId();
                $membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);

                $start_time = date('Y-m-d H:i', $salecoupon['start_time']);
                $end_time = date('Y-m-d', $salecoupon['end_time']);
                /*$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户,详情请咨询4006602822";

				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$this->curl_sms($send_verify);
				
				$send_verify['sendtime'] = time();
				$this->model_sms->add($send_verify);*/
                echo '3';//成功
                exit;
            } else {
                echo "2";//重复
                exit;
            }

        } else {
            echo "5";
            exit;
        }

    }

    //还原订单号
    public function get_true_orderid()
    {
        $orderid = substr(trim('213770'), 0, -1);
        $orderid = $orderid - 987;
        echo $orderid;
    }

    /*
	//肖鹏跑优惠券用的脚本
	function add_pa(){
		$this->CarcodeModel = D('pacarcode');
		//echo "第5批已经刷完";exit;
		for($a=1;$a<=10000;$a++){
			//$coupon_code = $this->get_coupon_code();
			$coupon_code = rand(1234567891,999999999);
			$coupon_code = $coupon_code;
			$count = $this->CarcodeModel->where(array('coupon_code'=>$coupon_code))->count();
			 if($count == 0){
				$data['coupon_code'] = $coupon_code;
				$data['create_time'] = time();
				z$data['from'] = '50元实体卡';
				$data['pici'] = '7';
				$this->CarcodeModel->add($data);
			}else{
				$a--;
			}
		}
		echo "ok";
	}
*/

    function ji()
    {
        set_time_limit(1800);
        $a = D('order');
        //$data['create_time'] = array(array('gt','1385827200'),array('lt','1388462400'),'and');//12月 122单
        //$data['create_time'] = array(array('gt','1388505600'),array('lt','1391180400'),'and');//1月 135单
        //$data['create_time'] = array(array('gt','1391266800'),array('lt','1393599600'),'and');//2月 139单
        //$data['create_time'] = array(array('gt','1393603200'),array('lt','1396281600'),'and');//3月 160单
        $data['complete_time'] = array(array('gt', '1393574400'), array('lt', '1396252800'), 'and');//3月 160单
        $data['order_des'] = array('neq', 'membernew');
        $data['order_state'] = '2';
        $count = $a->where($data)->count();
        echo $a->getlastSql();
        echo $count;
        /*$a = D('membersalecoupon');
		$b = D('carcode');	
		$c = 1;
		$map['pici']  = array(array('eq','6'),array('eq','3'),'or'); 
		$data = $b->select();
		$a_count = $b->where($map)->count();
		//echo $a_count;exit;
		foreach($data as $k=>$v){
			$count = $a->where(array('coupon_code'=>$v['coupon_code']))->count();
			if($count > 0){
				$c++;
			}
		}
		echo $c;*/

    }

    /*AJAX 分页测试*/
    function upload()
    {
        //$testM = D('test');//保险类订单表
        //$data = $testM->select();
        /*
		import("ORG.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类
		$memberModel = D('membernew');//保险类订单表
		$count = $memberModel->count(); //计算记录数
		$limitRows = 5; // 设置每页记录数
		$p = new AjaxPage($count, $limitRows,"test2"); //第三个参数是你需要调用换页的ajax函数名
		$limit_value = $p->firstRow . "," . $p->listRows;

		$data = $memberModel->order('uid desc')->limit($limit_value)->select(); // 查询数据
		$page = $p->show(); // 产生分页信息，AJAX的连接在此处生成
		
		$this->assign('list',$data);
		
		$this->display();*/
        $this->assign('data', $data);
        $this->display();
    }


    /*
		@author:chf
		@function:取出用户名
		@time:2013-07-03
	*/
    function car()
    {
        $model_carbrand = D('Carbrand');
        $model_carseries = D('Carseries');
        $model_carmodel = D('Carmodel');
        $brand = $model_carbrand->select();

        $a = 0;
        foreach ($brand as $bk => $bv) {
            $map_s['brand_id'] = $bv['brand_id'];
            $series = $model_carseries->where($map_s)->select();
            foreach ($series as $sk => $sv) {
                $map_m['series_id'] = $sv['series_id'];
                $model = $model_carmodel->where($map_m)->select();

                foreach ($model as $m_k => $m_v) {
                    $arr[$a]['brand_id'] = $bv['brand_id'];
                    $arr[$a]['brand_name'] = $bv['brand_name'];
                    $arr[$a]['series_id'] = $sv['series_id'];
                    $arr[$a]['series_name'] = $sv['series_name'];
                    $arr[$a]['series_name'] = $sv['series_name'];
                    $arr[$a]['type'] = $sv['type'];
                    $arr[$a]['sort'] = $sv['sort'];
                    $arr[$a]['start_year'] = $sv['start_year'];
                    $arr[$a]['start_year'] = $sv['end_year'];
                    $arr[$a]['model_id'] = $m_v['model_id'];
                    $arr[$a]['model_name'] = $m_v['model_name'];
                    $a++;
                }

            }
        }
        $this->assign('model', $arr);
        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        header("Content-Type:application/vnd.ms-excel;charset=gb2312");
        header("Content-Disposition:attachement;filename=百度统计表" . date('YmdHis') . ".xls");
        $this->display();


    }

    function uu()
    {
        $url = 'http://www.xieche.com.cn/test/ur';
        //$data = 'img-新花花-id-5'; 
        $data['img'] = "华华";
        $data['id'] = "5";
        $dd = $this->Api_toPA($url, $data);
        var_dump($dd);
    }


    function Api_toPA($url, $data)
    {
        $host = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


    /*
		@author:ysh
		@function:平安加密
		@time:2014/2/14
	*/
    function postdecrypt()
    {
        import("ORG.Util.MyAes");
        $keyStr = 'UIMN85UXUQC436IM'; //平安给的密钥
        $plainText = 'L84RQpF373kBEY6SOE0FmrItcQFxmF7cMV3vfqWwcj0='; // 原文
        $plainText = iconv('UTF-8', 'GBK', $plainText);
        $aes = new MyAes();
        $aes->set_key($keyStr);
        $aes->require_pkcs5();
        //$encText = $aes->encrypt($plainText);//加密
        $decString = $aes->decrypt($encText);//解密
        //$decString = iconv('UTF-8', 'GBK',$decString);//平安给我们的是GBK的 我们要转成UTF8
        echo $decString . "b";
    }


    /*
		@author:ysh
		@function:平安解码
		@time:2014/2/14
	*/
    function getdecrypt($encText)
    {
        import("ORG.Util.MyAes");
        $keyStr = 'UIMN85UXUQC436IM'; //平安给的密钥
        //$plainText = 'this is a string will be AES_Encrypt';  原文
        //$plainText =  iconv('UTF-8', 'GBK',$plainText) ;
        $aes = new MyAes();
        $aes->set_key($keyStr);
        $aes->require_pkcs5();
        //$encText = $aes->encrypt($plainText);//加密
        $decString = $aes->decrypt($encText);//解密
        //$decString = iconv('GBK', 'UTF-8',$decString);//平安给我们的是GBK的 我们要转成UTF8
        return $decString;
    }


    function ur()
    {
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $testM = D('test');//保险类订单表
        $_SESSION['tt'] = $data['imNo'] = $_POST['imNo'];
        $testM->data($data)->add();

    }

    function hehe()
    {
        echo phpinfo();

        /*
		$string = "本田";

		$hex = strtoupper( bin2hex( $string ) );
		v ar_dump( "十六进制是：".$hex );
		print "\r\n<br>";
		var_dump( "二进制是：".$this->h2b($hex) );
		print "\r\n<br>";
		var_dump( "源字符串是：".$this->hex2bin( $string ) );
		print "\r\n<br>";	
		*/
    }

    //加密方法
    function desEncryptStr($xml, $keyString)
    {
        $aes = new MyAes();
        $ct = $aes->encryptString($xml, $keyString);
        return $ct;
    }

    //解密方法 
    function DesDecryptStr($xml, $keyString)
    {
        $aes = new MyAes();
        $cpt = $aes->decryptString($xml, $keyString);
        return $cpt;
    }


    function h2b($code)
    {
        $bincode = "";
        $char = str_split($code);
        foreach ($char as $v) {
            $key = base_convert($v, 16, 2);
            $bincode .= $key;
        }
        return $bincode;
    }

    function hex2bin($data)
    {
        $len = strlen($data);
        return pack("H" . $len, $data);
    }


    function test_up()
    {
        $testM = D('test');//保险类订单表
        $path = C('UPLOAD_ROOT') . '/Shop/';
        for ($a = 0; $a < count($_FILES["coupon_pic"]); $a++) {
            $filetype = $_FILES['coupon_pic']['type'][$a];
            if ($filetype == 'image/jpeg') {
                $type = '.jpg';
            }
            if ($filetype == 'image/jpg') {
                $type = '.jpg';
            }
            if ($filetype == 'image/pjpeg') {
                $type = '.jpg';
            }
            if ($filetype == 'image/gif') {
                $type = '.gif';
            }

            if ($_FILES["coupon_pic"]["name"][$a]) {
                $today = date('YmdHis'); //获取时间并赋值给变量 
                $file2 = $path . $_FILES["coupon_pic"]["name"][$a]; //图片的完整路径 
                //$img = $_FILES["coupon_pic"]["name"][$a].$type; //图片名称 
                $flag = 1;
            }//END IF 
            $newname = md5($_FILES["coupon_pic"]["tmp_name"][$a]);
            if ($flag) $requlest = move_uploaded_file($_FILES["coupon_pic"]["tmp_name"][$a], $file2);
            $new['img'] = $newname;
            $testM->data($new)->add();
            //echo $_FILES["coupon_pic"]["tmp_name"][$a];
            //$uploadfile =$path.$_FILES['coupon_pic']['name']; //上传后文件所在的文件名和路径
        }
        echo "ok";
    }


    public function _upload_init($upload)
    {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Coupon/Logo/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'coupon1_,coupon2_';//coupon1_网站图片显示；coupon2_手机APP图片显示

        $resizeThumbSize_arr = array('299,99', '225,75');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }


    /*
		@author:chf
		@function:取出用户名
		@time:2013-07-03
	*/
    function runex()
    {
        $memberModel = D('membernew');//保险类订单表
        //$membercarModel = D('membercar');//保险类订单表
        $data = $memberModel->order('addtime asc')->select();


        foreach ($data as $k => $value) {

            $data[$k]['username'] = $value['truename'];
            $data[$k]['mobile'] = preg_replace("/(\d{3})(\d{4})/", "$1****", $value['mobile']);
            $data[$k]['reg_time'] = $value['addtime'];
            $data[$k]['memo'] = $value['memo'];

        }

        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        header("Content-Type:application/vnd.ms-excel;charset=gb2312");
        header("Content-Disposition:attachement;filename=用户名数据" . date('YmdHis') . ".xls");
        $this->assign('data', $data);
        $this->display();
    }

    /*
		
	*/
    function tt()
    {

        $memberModel = D('member');//
        $orderModel = D('order');//保险类订单表
        //1月
        $data1['reg_time'] = array(array('gt', '1356969600'), array('lt', '1359647940'));
        $member1 = $memberModel->where($data1)->select();
        $member_count1 = $memberModel->where($data1)->count();
        foreach ($member1 as $k => $v) {
            $order1['create_time'] = array(array('gt', '1356969600'), array('lt', '1359647940'));
            $order1['uid'] = $v['uid'];
            $ordercount1 += $orderModel->where($order1)->count();
            $ordercount1_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount1_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount1_now_new > 2) {
                $ordercount1_now_new4++;
            }
        }
        //2月
        $data2['reg_time'] = array(array('gt', '1359648000'), array('lt', '1362063600'));

        $member2 = $memberModel->where($data2)->select();
        $member_count2 = $memberModel->where($data2)->count();
        foreach ($member2 as $k => $v) {
            $order2['create_time'] = array(array('gt', '1359648000'), array('lt', '1362063600'));
            $order2['uid'] = $v['uid'];
            $ordercount2 += $orderModel->where($order2)->count();
            $ordercount2_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount2_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount2_now_new > 2) {
                $ordercount2_now_new4++;
            }
        }
        //3月	
        $data3['reg_time'] = array(array('gt', '1362067200'), array('lt', '1364742000'));
        $member3 = $memberModel->where($data3)->select();
        $member_count3 = $memberModel->where($data3)->count();
        foreach ($member3 as $k => $v) {
            $order3['create_time'] = array(array('gt', '1362067200'), array('lt', '1364742000'));
            $order3['uid'] = $v['uid'];
            $ordercount3 += $orderModel->where($order3)->count();
            $ordercount3_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount3_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount3_now_new > 2) {
                $ordercount3_now_new4++;
            }
        }

        //4月	
        $data4['reg_time'] = array(array('gt', '1364745600'), array('lt', '1367334000'));
        $member4 = $memberModel->where($data4)->select();
        $member_count4 = $memberModel->where($data4)->count();
        foreach ($member4 as $k => $v) {
            $order4['create_time'] = array(array('gt', '1364745600'), array('lt', '1367334000'));
            $order4['uid'] = $v['uid'];
            $ordercount4 += $orderModel->where($order4)->count();
            $ordercount4_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount4_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount4_now_new > 2) {
                $ordercount4_now_new4++;
            }
        }

        //5月	
        $data5['reg_time'] = array(array('gt', '1367337600'), array('lt', '1370012400'));
        $member5 = $memberModel->where($data5)->select();
        $member_count5 = $memberModel->where($data5)->count();
        foreach ($member5 as $k => $v) {
            $order5['create_time'] = array(array('gt', '1367337600'), array('lt', '1370012400'));
            $order5['uid'] = $v['uid'];
            $ordercount5 += $orderModel->where($order5)->count();
            $ordercount5_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount5_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount5_now_new > 2) {
                $ordercount5_now_new4++;
            }
        }

        //6月	
        $data6['reg_time'] = array(array('gt', '1370016000'), array('lt', '1372604400'));
        $member6 = $memberModel->where($data6)->select();
        $member_count6 = $memberModel->where($data6)->count();
        foreach ($member6 as $k => $v) {
            $order6['create_time'] = array(array('gt', '1370016000'), array('lt', '1372604400'));
            $order6['uid'] = $v['uid'];
            $ordercount6 += $orderModel->where($order6)->count();
            $ordercount6_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount6_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount6_now_new > 2) {
                $ordercount6_now_new4++;
            }
        }

        //7月	
        $data7['reg_time'] = array(array('gt', '1372608000'), array('lt', '1375282800'));
        $member7 = $memberModel->where($data7)->select();
        $member_count7 = $memberModel->where($data7)->count();
        foreach ($member7 as $k => $v) {
            $order7['create_time'] = array(array('gt', '1372608000'), array('lt', '1375282800'));
            $order7['uid'] = $v['uid'];
            $ordercount7 += $orderModel->where($order7)->count();
            $ordercount7_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount7_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount7_now_new > 2) {
                $ordercount7_now_new4++;
            }
        }

        //8月	
        $data8['reg_time'] = array(array('gt', '1375286400'), array('lt', '1377961200'));
        $member8 = $memberModel->where($data8)->select();
        $member_count8 = $memberModel->where($data8)->count();
        foreach ($member8 as $k => $v) {
            $order8['create_time'] = array(array('gt', '1375286400'), array('lt', '1377961200'));
            $order8['uid'] = $v['uid'];
            $ordercount8 += $orderModel->where($order8)->count();
            $ordercount8_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount8_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount8_now_new > 2) {
                $ordercount8_now_new4++;
            }
        }


        //9月	
        $data9['reg_time'] = array(array('gt', '1377964800'), array('lt', '1380553200'));
        $member9 = $memberModel->where($data9)->select();
        $member_count9 = $memberModel->where($data9)->count();
        foreach ($member9 as $k => $v) {
            $order9['create_time'] = array(array('gt', '1377964800'), array('lt', '1380553200'));
            $order9['uid'] = $v['uid'];
            $ordercount9 += $orderModel->where($order9)->count();
            $ordercount9_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount9_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount9_now_new > 2) {
                $ordercount9_now_new4++;
            }
        }

        //10月	
        $data10['reg_time'] = array(array('gt', '1380556800'), array('lt', '1382198400'));
        $member10 = $memberModel->where($data10)->select();
        $member_count10 = $memberModel->where($data10)->count();
        foreach ($member10 as $k => $v) {
            $order10['create_time'] = array(array('gt', '1380556800'), array('lt', '1382198400'));
            $order10['uid'] = $v['uid'];
            $ordercount10 += $orderModel->where($order10)->count();
            $ordercount10_now += $orderModel->where(array('uid' => $v['uid']))->count();
            $ordercount10_now_new = $orderModel->where(array('uid' => $v['uid']))->count();
            if ($ordercount10_now_new > 2) {
                $ordercount10_now_new4++;
            }
        }
        /*


*/


        echo '一月用户数:' . $member_count1 . "  一月下订单数:" . $ordercount1 . "  到现在的订单数:" . $ordercount1_now . "  到现在大于2张订单数:" . $ordercount1_now_new4 . "<br>";
        echo '二月用户数:' . $member_count2 . "  二月下订单数:" . $ordercount2 . "  到现在的订单数:" . $ordercount2_now . "  到现在大于2张订单数:" . $ordercount2_now_new4 . "<br>";
        echo '三月用户数:' . $member_count3 . "  三月下订单数:" . $ordercount3 . "  到现在的订单数:" . $ordercount3_now . "  到现在大于2张订单数:" . $ordercount3_now_new4 . "<br>";
        echo '四月用户数:' . $member_count4 . "  四月下订单数:" . $ordercount4 . "  到现在的订单数:" . $ordercount4_now . "  到现在大于2张订单数:" . $ordercount4_now_new4 . "<br>";
        echo '五月用户数:' . $member_count5 . "  五月下订单数:" . $ordercount5 . "  到现在的订单数:" . $ordercount5_now . "  到现在大于2张订单数:" . $ordercount5_now_new4 . "<br>";
        echo '六月用户数:' . $member_count6 . "  六月下订单数:" . $ordercount6 . "  到现在的订单数:" . $ordercount6_now . "  到现在大于2张订单数:" . $ordercount6_now_new4 . "<br>";
        echo '七月用户数:' . $member_count7 . "  七月下订单数:" . $ordercount7 . "  到现在的订单数:" . $ordercount7_now . "  到现在大于2张订单数:" . $ordercount7_now_new4 . "<br>";
        echo '八月用户数:' . $member_count8 . "  八月下订单数:" . $ordercount8 . "  到现在的订单数:" . $ordercount8_now . "  到现在大于2张订单数:" . $ordercount8_now_new4 . "<br>";
        echo '九月用户数:' . $member_count9 . "  九月下订单数:" . $ordercount9 . "  到现在的订单数:" . $ordercount9_now . "  到现在大于2张订单数:" . $ordercount9_now_new4 . "<br>";
        echo '十月用户数:' . $member_count10 . "  十月下订单数:" . $ordercount10 . "  到现在的订单数:" . $ordercount10_now . "  到现在大于2张订单数:" . $ordercount10_now_new4 . "<br>";

        //header("Cache-Control:no-cache,must-revalidate");
        //header("Pragma:no-cache");
        //header("Content-Type:application/vnd.ms-excel;charset=gb2312");
        //header("Content-Disposition:attachement;filename=用户名数据" . date('YmdHis') . ".xls");
        //$this->assign('data',$data);
        //$this->display();
    }

    //生成用户显示的订单号
    public function get_orderid($id)
    {
        $orderid = $id + 987;
        $sum = 0;
        for ($ii = 0; $ii < strlen($orderid); $ii++) {
            $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum % 10;
        return $orderid . $str;
    }

    /*
		@author:chf
		@function:取出用户名
		@time:2013-07-03
	*/
    function order()
    {

        $orderModel = D('order');//保险类订单表
        $carbrandModel = D('carbrand');//保险类订单表
        $carseriesModel = D('carseries');//保险类订单表
        $carModel = D('carmodel');//保险类订单表
        $shopModel = D('shop');//保险类订单表

        $data = $orderModel->select();

        foreach ($data as $k => $v) {

            $data[$k]['order_id'] = $this->get_orderid($v['id']);//订单ID
            $data[$k]['licenseplate'] = preg_replace("/(\d{2})(\d{2})/", "$107**", $v['licenseplate']);;//车牌号
            $data[$k]['mobile'] = preg_replace("/(\d{3})(\d{4})/", "$107**", $v['mobile']);
            $data[$k]['truename'] = $v['truename'];

            if ($v['order_state'] == '0') {
                $data[$k]['order_state'] = '等待处理';
            } elseif ($v['order_state'] == '1') {
                $data[$k]['order_state'] = '预约已确认';
            } elseif ($v['order_state'] == '2') {
                $data[$k]['order_state'] = '预约已完成';
            } elseif ($v['order_state'] == '-1') {
                $data[$k]['order_state'] = '作废预约';
            }
            $carbrand = $carbrandModel->where(array('brand_id' => $v['brand_id']))->find();
            $data[$k]['brand_name'] = $carbrand['brand_name'];
            $carseries = $carseriesModel->where(array('series_id' => $v['series_id']))->find();
            $data[$k]['series_name'] = $carseries['series_name'];
            $catmodel = $carModel->where(array('model_id' => $v['model_id']))->find();
            $data[$k]['model_name'] = $carseries['model_name'];
            $shop = $shopModel->where(array('id' => $v['shop_id']))->find();
            $data[$k]['shop_name'] = $shop['shop_name'];


        }


        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        header("Content-Type:application/vnd.ms-excel;charset=gb2312");
        header("Content-Disposition:attachement;filename=订单数据" . date('YmdHis') . ".xls");
        $this->assign('data', $data);
        $this->display();
    }

    /*
		@author:chf
		@function:取出用户名
		@time:2013-07-03
	*/
    function runadd()
    {
        set_time_limit(0);
        $memberModel = D('member');//保险类订单表
        $membernewModel = D('membernew');//保险类订单表
        //$membercarModel = D('membercar');//保险类订单表
        $data = $memberModel->limit(0, 1)->select();
        exit;

        foreach ($data as $k => $v) {
            $arr['mobile'] = $v['mobile'];
            $arr['truename'] = $v['username'];
            $arr['addtime'] = $v['reg_time'];
            $arr['memo'] = $v['memo'];

            $membernewModel->add($arr);

        }

        /* header("Cache-Control:no-cache,must-revalidate");
		header("Pragma:no-cache");
		header("Content-Type:application/vnd.ms-excel;charset=gb2312");
		header("Content-Disposition:attachement;filename=用户名数据" . date('YmdHis') . ".xls");
		$this->assign('data',$data);
		$this->display();*/
        echo "ok";
    }


    function index()
    {
        exit;
        echo '111';
        $str = file_get_contents('./normal.js');//读取JS文件的内容
        //echo $str;
        $js_rows = explode("\n", $str);

        $brand_add_data = array();//品牌插入值
        $series_add_data = array();//车系插入值
        $model_add_data = array();//车型插入值

        //按行读取处理数据
        foreach ($js_rows as $row) {
            //品牌
            if (substr($row, 0, 5) == "fct['") {//此行是品牌列表
                $_b = explode("'", $row);
                $_brand_str = $_b[3];//定义了品牌的整个内容
                $_brand_arr = explode(",", $_brand_str);//拆分品牌信息
                //$_brand_arr 偶数键值为品牌ID，奇数为品牌信息字符串
                if (!empty($_brand_arr)) {
                    foreach ($_brand_arr as $k => $binfo) {
                        if ($k % 2 == 0) {
                            $_id = $binfo;
                            $_info = $_brand_arr[++$k];
                            $_word = $_info{0};
                            $_brand_name = trim(substr($_info, 4));
                            $brand_add_data[] = array('brand_id' => $_id, 'word' => $_word, 'brand_name' => $_brand_name);
                        } else {
                            continue;
                        }
                    }
                }
            }

            //车系
            if (substr($row, 0, 4) == "br['") {
                $_s = explode("'", $row);
                $brand_id = $_s[1];//品牌ID
                //print_r($_s);exit;
                $_series_str = $_s[3];
                $_series_arr = explode(",", $_series_str);
                if (!empty($_series_arr)) {
                    foreach ($_series_arr as $k => $sinfo) {
                        if ($k % 2 == 0) {
                            $_id = $sinfo;
                            $_info = $_series_arr[++$k];
                            $_word = $_info{0};
                            $series_name = trim(substr($_info, 4));
                            $series_add_data[] = array('series_id' => $_id, 'word' => $_word, 'series_name' => $series_name, 'brand_id' => $brand_id);
                        } else {
                            continue;
                        }
                    }
                }
            }

            //车型
            if (substr($row, 0, 5) == "spl['") {
                $_m = explode("'", $row);
                $_model_str = $_m[3];
                $_model_arr = explode(",", $_model_str);
                if (!empty($_model_arr)) {
                    foreach ($_model_arr as $k => $minfo) {
                        if ($k % 2 == 0) {
                            $_id = $minfo;
                            $_model_name = $_model_arr[++$k];
                            $_year = substr($_model_name, 0, 4);
                            $model_add_data[] = array('model_id' => $_id, 'model_name' => $_model_name, 'year' => $_year, 'series_id' => null);
                        } else {
                            continue;
                        }
                    }
                }
            }
        }

        if (!empty($brand_add_data)) {
            //print_r($brand_add_data[0]);
            $bm = M("carbrand");
            if ($bm->addAll($brand_add_data)) {
                //echo $bm->getLastSql();
                echo '插入品牌信息成功！<br />';
            } else {
                echo '插入品牌信息失败，可能重复插入，主键冲突！<br />';
            }
        }

        if (!empty($series_add_data)) {
            //print_r($series_add_data[0]);
            $sm = M("carseries");
            if ($sm->addAll($series_add_data)) {
                echo '插入车系信息成功！';
                //echo $sm->getLastSql();
            } else {
                echo '插入车系信息失败，可能重复插入，主键冲突！<br />';
            }
        }

        if (!empty($model_add_data)) {
            //print_r($series_add_data[0]);
            $sm = M("carmodel");
            if ($sm->addAll($model_add_data)) {
                echo '插入车型信息成功！';
                //echo $sm->getLastSql();
            } else {
                echo '插入车型信息失败，可能重复插入，主键冲突！<br />';
            }
        }


    }

    //更新车型表里的车系字段
    function update_series()
    {
        $str = file_get_contents('./normal.js');//读取JS文件的内容
        //echo $str;
        $js_rows = explode("\n", $str);

        $series_year_arr = array();//车系-年份
        $year_model_arr = array();//年份-车型

        //按行读取处理数据
        foreach ($js_rows as $row) {
            //车系-年份
            if (substr($row, 0, 16) == "spec_year_name['") {
                $_m = explode("'", $row);
                $_model_str = $_m[3];
                $_model_arr = explode(",", $_model_str);
                if (!empty($_model_arr)) {
                    foreach ($_model_arr as $k => $minfo) {
                        if ($k % 2 == 0) {
                            $series_year_arr[] = array('series_id' => $_m[1], 'year_id' => $minfo);
                        } else {
                            continue;
                        }
                    }
                }
            }

            //年份-车型
            if (substr($row, 0, 5) == "spl['") {
                $_m = explode("'", $row);
                $_model_str = $_m[3];
                $_model_arr = explode(",", $_model_str);
                if (!empty($_model_arr)) {
                    foreach ($_model_arr as $k => $minfo) {
                        if ($k % 2 == 0) {
                            $year_model_arr[$_m[1]][] = $minfo;
                        } else {
                            continue;
                        }
                    }
                }
            }
        }
        $sm = M("carmodel");
        dump($year_model_arr);
        foreach ($series_year_arr as $row) {
            $s_id = $row['series_id'];
            if ($year_model_arr[$row['year_id']]) {
                $in = implode(',', $year_model_arr[$row['year_id']]);
                //$sm->where("model_id in ($in)")->data(array('series_id'=>$s_id))->save();
                //echo $sm->getLastSql().'<hr>';
            }
        }
        echo '终于成功了';


    }


    //短信接口
    function curl_sms($post = '', $charset = null)
    {

        $post['content'] = str_replace("联通", "联_通", $post['content']);

        $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh21007</account><password>49e96c9b07f0628fec558b11894a9e8f</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";

        $url = 'http://www.10690300.com/http/sms/Submit';
        $curl = curl_init($url);
        if (!is_null($charset)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
        }
        if (!empty($post)) {
            $xml_data = 'message=' . urlencode($xml_data);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        //var_dump($res);
        return $res;
    }

    function randString($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {//位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= self::msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
            }
        }
        return $str;
    }

    //计算优惠券验证码
    function get_coupon_code()
    {
        $orderid = $this->randString(9, 1);
        $sum = 0;
        for ($ii = 0; $ii < strlen($orderid); $ii++) {
            $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum % 10;
        $result = $orderid . $str;
        return $result;
    }

    /*
		读取EX文件里的信息
		发送订单里没拿抵用券
	*/
    function ex()
    {
        echo "已跑完";
        exit;
        $this->MemberModel = D('Member');//用户表
        $this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
        $this->model_sms = D('Sms');
        $this->LotteryModel = D('lottery');//抽奖监控表
        $this->LotteryloginModel = D('lotterylogin');//抽奖页登录监控表
        $this->LotteryappModel = D('lotteryapp');//下载APP登录监控表
        $a = 0;
        $handle = fopen("xc_order.csv", "r");
        while ($data = fgetcsv($handle, 1000, ",")) {
            $num = count($data);
            $b = 0;
            //$subject= iconv("GB2312","UTF-8",$data[$b]);
            for ($c = 0; $c < 1; $c++) {
                $data = explode(';', iconv("GB2312", "UTF-8", $data[$b]));
                //携车网注册ID号码
                $uid = $data[1];
                //手机号
                $mobile = $data[0];
                $Membersalecount = $this->MembersalecouponModel->where(array('mobile' => $mobile))->count();
                if ($Membersalecount == '0') {
                    $info = $this->MemberModel->where(array('uid' => $uid))->find();
                    if (!$info['fromstatus']) {
                        $this->MemberModel->where(array('uid' => $uid))->save(array('fromstatus' => 6));
                    }


                    $model_salecoupon = D('Salecoupon');
                    $salecoupon = $model_salecoupon->find('1');
                    //插入membersalecoupon表
                    $Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
                    $Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
                    $Membersalecoupon['mobile'] = $mobile;

                    $Membersalecoupon['create_time'] = time();
                    $Membersalecoupon['start_time'] = $salecoupon['start_time'];
                    $Membersalecoupon['end_time'] = $salecoupon['end_time'];
                    $Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
                    $Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
                    $coupon_code = $this->get_coupon_code();
                    $_SESSION['coupon_code'] = $coupon_code;
                    $Membersalecoupon['coupon_code'] = $coupon_code;

                    $Membersalecoupon['from'] = '6';//来源
                    $Membersalecoupon['uid'] = $uid;
                    $membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
                    //echo $this->MembersalecouponModel->getlastSql();
                    $start_time = date('Y-m-d H:i', $salecoupon['start_time']);
                    $end_time = date('Y-m-d', $salecoupon['end_time']);


                    $verify_str = "携车网感谢老用户的支持，推出“最惠养车”补贴计划，送你50元养车。50元现金抵用券已送达您的账户.请通过携车网预约后凭消费码:" . $coupon_code . "至指定4S店于有效期内(截至" . $end_time . ")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服电话4006602822";

                    $send_verify = array(
                        'phones' => $mobile,
                        'content' => $verify_str,
                    );
                    $this->curl_sms($send_verify);

                    $send_verify['sendtime'] = time();
                    $this->model_sms->add($send_verify);

                    $lottey['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
                    $lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
                    $lottey['mobile'] = $mobile;//时间
                    $lottey['create_time'] = time();//时间
                    $this->LotteryModel->data($lottey)->add();
                    $a++;
                }

            }
        }
        echo $a;
        fclose($handle);
    }

    /*
		@author:chf
		@function:提取百度搜来源关键字
		@time:2013-09-23
	*/
    function baidu()
    {
        $this->baiduModel = D('baidu');
        $data['wd'] = array('neq', '');
        $info = $this->baiduModel->where($data)->order('create_time DESC')->select();
        /*foreach($info as $k=>$value){
			if($value['from']=='baidu'){
				$from = '百度';
			}
			$row=$value['wd'].",".$from.",".date('Y-m-d',$value['create_time'])."\n";
			$row = mb_convert_encoding($row, 'GBK', 'UTF-8');
			echo $row;
		}*/
        //echo $this->baiduModel->getlastSql();
        //dump($info);
        $this->assign('info', $info);

        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        header("Content-Type:application/vnd.ms-excel;charset=gb2312");
        header("Content-Disposition:attachement;filename=百度统计表" . date('YmdHis') . ".xls");
        $this->display();
    }


    /*
		@author:ysh
		@function:修改店铺是否有团购券
		@time:2013/12/18
	*/
    function update_have_coupon()
    {
        $model_shop = D("Shop");
        $model_coupon = D("Coupon");

        $data['have_coupon'] = 0;
        $shop_infos = $model_shop->where(array('have_coupon' => 1))->save($data);


        $map["is_delete"] = 0;
        $map['show_s_time'] = array('lt', time());
        $map['show_e_time'] = array('gt', time());
        $coupon = $model_coupon->where($map)->group("shop_id")->field('shop_id')->select();
        print_r($coupon);
        if ($coupon) {
            foreach ($coupon as $key => $value) {
                $shop_ids[] = $value['shop_id'];
            }
        }
        print_r($shop_ids);

        $map_shop['id'] = array('in', $shop_ids);
        $data['have_coupon'] = 1;
        $shop_infos = $model_shop->where($map_shop)->save($data);
        print_r($shop_infos);
    }

    function new_sms()
    {
        error_reporting(-1);
        $post = array();
        $post['phones'] = '13661743916';
        $post['content'] = " 测试测试【携车网】";

        $client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
        $param = array("userCode" => "csml", "userPass" => "csml5103", "DesNo" => $post[phones], "Msg" => $post[content], "Channel" => "1");
        $p = $client->__soapCall('SendMsg', array('parameters' => $param));
        var_dump($p);
        exit();


    }

    function check_app_request()
    {
        $id = $_REQUEST['id'];
        $model_app_request = D("App_request");
        $result = $model_app_request->find($id);
        print_r(unserialize($result['request']));
    }

    /*
	*跨品牌 爆款fsid 根据series_id来存取
	*
	*/
    function update_fsid_across()
    {
        $coupon_model = D("Coupon");
        $series_model = D("Carseries");

        $map['coupon_across'] = 1;
        $coupon = $coupon_model->where($map)->select();
        foreach ($coupon as $key => $val) {
            $series_id = $val['series_id'];
            $map_series = array();
            $map_series['series_id'] = array('in', $series_id);
            $series = $series_model->where($map_series)->select();
            foreach ($series as $sk => $sv) {
                $fsid_series[] = $sv['fsid'];
            }
            $fsid_series = array_unique($fsid_series);
            $fsid_across = implode(",", $fsid_series);

            $data = array();
            $data['id'] = $val['id'];
            $data['fsid_across'] = $fsid_across;
            $coupon_model->save($data);
            unset($fsid_series);
        }
    }

    //跑订单数据
    function run_order_data()
    {
        $model_membernew = D('Membernew');
        $model_member = D('Member');
        $model_order = D('Order');
        $membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=1 AND is_order_insert=1 ORDER BY uid");
        $count = 0;
        foreach ($membernew as $k => $v) {
            $order = $model_order->query("SELECT *FROM `xc_order` WHERE `remark` LIKE '脚本订单,不要打电话' AND uid =15301 ORDER BY id DESC LIMIT 0 , 1");

            $data = array();
            //$data['password'] = md5(",,,,,,,,,,");
            $data['mobile'] = $v['mobile'];
            $data['fromstatus'] = '30';
            //$data['reg_time'] = time();
            //$data['memo'] = "短信用户  ".$v['text'];

            $member = $model_member->where($data)->find();
            $shop_id = $v['shop_id'];
            if ($v['shop_id'] == 54 || $v['shop_id'] == 56) {
                $shop_ids = "58,121,345,346,356,362,364,1345,1356,55,125,57,370,115,56,1330,348,1346";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($v['shop_id'] == 8) {
                $shop_ids = "10,87,297,304,306,9,11,298,299,118,289,291,1338,1344";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($v['shop_id'] == 62 || $v['shop_id'] == 103) {
                $shop_ids = "62,536,538,541,542,120,537";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($v['shop_id'] == 23) {
                $shop_ids = "21,23,110";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($v['shop_id'] == 71) {
                $shop_ids = "204,205";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }

            $lastorder = $model_order->where(array('shop_id' => $shop_id))->order('id DESC')->find();

            $order_data = array();
            $order_data['uid'] = $member['uid'];
            $order_data['shop_id'] = $shop_id;
            $order_data['brand_id'] = $v['brand_id'];
            $order_data['series_id'] = $v['series_id'];
            $order_data['model_id'] = $v['model_id'];
            $order_data['timesaleversion_id'] = isset($lastorder['timesaleversion_id']) ? $lastorder['timesaleversion_id'] : 0;
            $order_data['workhours_sale'] = isset($lastorder['workhours_sale']) ? $lastorder['workhours_sale'] : 0.70;
            $order_data['truename'] = $v['truename'];
            $order_data['mobile'] = $v['mobile'];
            $order_data['licenseplate'] = $v['licenseplate'];

            $model_order->where(array('id' => $order['0']['id']))->save($order_data);

            $model_membernew->where(array("uid" => $v['uid']))->save(array('is_insert' => 1, 'is_order_insert' => 2));
            $count = $count + 1;
            echo 'order_id=' . $order['0']['id'] . 'uid=' . $v['uid'] . '</br>';//exit;

        }
        echo $count;
    }

    function get_access_token()
    {
        $result = $this->getWeixinToken();
// 		$appid = "wx43430f4b6f59ed33";
// 		$secret = "e5f5c13709aa0ae7dad85865768855d6";

// 		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
// 		//$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
// 		echo $url;
// 		//ini_set('default_socket_timeout',15);   
// 		$result = file_get_contents($url);
// 		/*$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_POST, 1);
// 		curl_setopt($ch, CURLOPT_HEADER, 0);
// 		curl_setopt($ch, CURLOPT_URL,$url);
// 		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
// 		curl_setopt($ch, CURLOPT_TIMEOUT,15); 
// 		$result = curl_exec($ch);
// 		curl_close($ch);*/
// 		$result = json_decode($result,true);
// 		$access_token = $result['access_token'];
        print_r($result);
// 		echo $access_token;
    }

    //跑上门保养项目数据
    function run_data()
    {
        $this->linshiModel = D('linshi');
        $this->filterlModel = D('item_filter');
        $this->carmodelModel = D('carmodel');

        $list = $this->linshiModel->select();
        echo $this->linshiModel->getLastsql();

        foreach ($list as $k => $v) {
            $a2 = $this->filterlModel->where(array('name' => '曼牌 ' . $v['a2']))->find();
            $a4 = $this->filterlModel->where(array('name' => '曼牌 ' . $v['a4']))->find();
            $a6 = $this->filterlModel->where(array('name' => '曼牌 ' . $v['a6']))->find();
            $b2 = $this->filterlModel->where(array('name' => '马勒 ' . $v['b2']))->find();
            $b4 = $this->filterlModel->where(array('name' => '马勒 ' . $v['b4']))->find();
            $b6 = $this->filterlModel->where(array('name' => '马勒 ' . $v['b6']))->find();
            $item_set = array();
            $item_set['0'] = array();
            $item_set['1'] = array('0' => $a2['id'], '1' => $b2['id']);//jilv
            foreach ($item_set['1'] as $kk => $vv) {
                if ($vv == '') {
                    unset($item_set['1'][$kk]);
                }
            }
            $item_set['2'] = array('0' => $a4['id'], '1' => $b4['id']);//kongtiao
            foreach ($item_set['2'] as $kkk => $vvv) {
                if ($vvv == '') {
                    unset($item_set['2'][$kkk]);
                }
            }
            $item_set['3'] = array('0' => $a6['id'], '1' => $b6['id']);//kongqi
            foreach ($item_set['3'] as $kkkk => $vvvv) {
                if ($vvvv == '') {
                    unset($item_set['3'][$kkkk]);
                }
            }
            //print_r($item_set);
            $item_set = serialize($item_set);
            $map['model_id'] = $v['id'];
            $data['item_set'] = $item_set;
            $this->carmodelModel->where($map)->save($data);
            //echo $this->carmodelModel->getLastsql();
        }

    }

    //更新数据：机滤为空的is_show=2
    function update_carmodel()
    {
        $model_carmodel = D('carmodel');
        $list = $model_carmodel->select();
        foreach ($list as $key => $value) {
            $item_array = unserialize($value['item_set']);
            if (empty($item_array['1'])) {
                //print_r($item_array);
                $data['is_show'] = '2';
                $map['model_id'] = $value['model_id'];
                $model_carmodel->where($map)->save($data);
            }
        }
        //print_r($list);
    }

    //根据电话和检测报告自增ID定点推送检测报告
    function send_checktosomebody()
    {
        $model_paweixin = D('paweixin');
        $map['mobile'] = '18149767591';
        $map['type'] = 2;
        $weixin = $model_paweixin->where($map)->find();
        //echo $model_paweixin->getLastsql();
        if ($weixin == '') {
            return 1;
        }
        //$sql_weixin = "SELECT * FROM xc_paweixin WHERE mobile='".$mobile."' AND type=2 LIMIT 1";
        //$weixin = $mysql->query($sql_weixin,"assoc");
        $report_id = base64_encode('2305168');
        $weixin_data['touser'] = $weixin['wx_id'];
        $weixin_data['title'] = "携车网车辆电子体检报告";
        $weixin_data['description'] = "携车网温馨提醒：您的爱车38项免费检测现已完毕，请点击查看检测报告！";
        $weixin_data['url'] = "http://www.xieche.com.cn/mobile/check_report-report_id-{$report_id}";

        $result = $this->weixin_api($weixin_data);
        if ($result) {
            $this->addCodeLog('检测报告结果', var_export($result, true) . 'http://www.xieche.com.cn/mobile/check_report-report_id-' . $report_id);
            return $result;
        } else {
            return true;
        }
    }

    //跑跑订单的数据
    function run_membernew()
    {
        $model_membernew = D('membernew');
        $list = $model_membernew->where(array('is_insert' => '1'))->select();
        echo $model_membernew->getLastsql();
        exit;
        foreach ($list as $k => $v) {
            $map['uid'] = $v['uid'];
            $data['is_order_insert'] = '1';
            $model_membernew->where($map)->save($data);
        }
    }

    //跑客服代下单数据
    function run_customer()
    {
        $model_insurance = D('insurance');
        $map['operator_name'] = array('neq', '');   //不为空
        $list = $model_insurance->where($map)->select();
        //echo $model_insurance->getLastsql();exit;
        foreach ($list as $k => $v) {
            $model_user = M('tp_admin.user', 'xc_');
            $user_info = $model_user->where(array('nickname' => $v['operator_name']))->find();
            $model_bidorder = D('bidorder');
            $umap['insurance_id'] = $v['id'];
            $data['customer_id'] = $user_info['id'];
            $model_bidorder->where($umap)->save($data);
        }
    }

    //订单明细数据
    function run_orderdata()
    {
        $model_order = D('order');
        $map['remark'] = array('neq', '脚本订单,不要打电话');
        $map['order_time'] = array(array('elt', '1404144000'), array('egt', '1401552000'));
        $list = $model_order->where($map)->group('mobile')->select();
        echo "<table><tr><td>姓名</td><td>电话</td><td>车型</td><td>4S店</td><td>下单时间</td></tr>";
        //echo $model_order->getLastsql();exit;
        foreach ($list as $k => $v) {
            $model_carmodel = D('carmodel');
            $model_info = $model_carmodel->where(array('model_id' => $v['model_id']))->find();
            $model_carseries = D('carseries');
            $series_info = $model_carseries->where(array('series_id' => $model_info['series_id']))->find();
            $model_carbrand = D('carbrand');
            $brand_info = $model_carbrand->where(array('brand_id' => $series_info['brand_id']))->find();
            $v['car'] = $brand_info['brand_name'] . $series_info['series_name'] . $model_info['model_name'];
            $model_shop = D('shop');
            $shop_info = $model_shop->where(array('id' => $v['shop_id']))->find();
            $v['shop_name'] = $shop_info['shop_name'];
            $v['order_time'] = date('Y-m-d H:i:s', $v['order_time']);
            echo "<tr><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . $v['car'] . "</td><td>" . $v['shop_name'] . "</td><td>" . $v['order_time'] . "</td></tr>";

        }
        echo "</table>";
    }

    //获取做过检测的数据
    function get_info()
    {
        $model_checkreport_total = D('checkreport_total');
        $map['reservation_id'] = array('exp', ' is NULL');
        $info = $model_checkreport_total->where($map)->Distinct(true)->field('mobile')->select();
        echo $model_checkreport_total->getLastsql();
        //print_r($info);
        $model_check_report = D('check_report');
        echo "<table><tr><th>姓名</th><th>电话</th><th>车型</th><th>车牌号</th></tr>";
        foreach ($info as $K => $v) {
            $m_info = $model_check_report->where(array('a1' => $v['mobile']))->find();
            $v['car'] = $m_info['a29'] . ' ' . $m_info['a28'] . ' ' . $m_info['a49'];
            //print_r($m_info);
            echo "<tr><td>" . $m_info['a22'] . "</td><td>" . $v['mobile'] . "</td><td>" . $v['car'] . "</td><td>" . $m_info['a20'] . "</td></tr>";
        }
        echo "</table>";
    }

    //跑支付数据
    function get_payinfo()
    {
        $report_id = base64_encode('3075168');
        $report_id1 = base64_encode('2590168');
        $report_id2 = base64_encode('2591168');
        echo 'http://www.xieche.com.cn/mobile/check_report-report_id-' . $report_id . '</br>';
        echo 'http://www.xieche.com.cn/mobile/check_report-report_id-' . $report_id1 . '</br>';
        echo 'http://www.xieche.com.cn/mobile/check_report-report_id-' . $report_id2;
        exit;
        $membercoupon = D('membercoupon');
        $map['is_pay'] = 1;
        $map['is_use'] = 1;
        $map['coupon_amount'] = 0;
        $info = $membercoupon->where($map)->select();
        echo $membercoupon->getLastsql();
        //print_r($info);
        $count1 = 0;
        $count2 = 0;
        foreach ($info as $k => $v) {
            $txcouponpay = D('txcouponpay');
            $t_info = $txcouponpay->where(array('membercoupon_ids' => $v['membercoupon_id']))->find();
            print_r($t_info);
            $chinapaycouponpay = D('chinapaycouponpay');
            $w_info = $chinapaycouponpay->where(array('membercoupon_ids' => $v['membercoupon_id']))->find();
            print_r($w_info);
            if ($t_info) {
                $count1 = $count1 + 1;
                $array1[] = $v['membercoupon_id'];
            } elseif ($w_info) {
                $count2 = $count2 + 1;
            } else {
                $array[] = $v['membercoupon_id'];
            }
        }
        echo 'count1=' . $count1 . 'count2=' . $count2;
        //print_r($array1);
        print_r($array);
    }

    //测试微信接口：生成带参数的二维码
    function get_QRcode()
    {
        //获取二维码ticket
        $access_token = $this->getWeixinToken();
        //echo "1111111";
        //echo $access_token.'</br>';exit;
        $host = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $array = array(
            "action_name" => "QR_LIMIT_SCENE",
            "action_info" => array("scene" => array("scene_id" => "1132"))
        );
        $json_body = json_encode($array);
        //print_r($json_body);
        //echo $host;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);
        //print_r($result);

        //通过ticket换取二维码
        $ticket = $result['ticket'];
        //echo "111</br>";
        //echo $ticket;exit;
        //$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHv7zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL3JVTlNncFBtaF96X1ZlWmE5bS1UAAIEBZtxVAMEAAAAAA==";
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
        header("Content-type: image/jpeg");
        echo file_get_contents($url);
    }

    /*
		*@name 得到微信的access_token
		*@author chf
		*@time 2014/3/26
	*/
    function get_weixinkey()
    {
        $appid = "wx43430f4b6f59ed33";
        $secret = "e5f5c13709aa0ae7dad85865768855d6";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        return $result['access_token'];
    }

    /*
		PHP生成二维码
	*/
    function sctest($data)
    {
        echo "data=" . $data;
        include("./ThinkPHP/Extend/Library/ORG/Qrcode/phpqrcode.php");
        // 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'L';
        // 点的大小：1到10
        $matrixPointSize = 4;
        // 生成的文件名
        $path = "../UPLOADS/Shop/Logo/";
        //if (!file_exists($path)){
        ///mkdir($path);
        //}

        $filename = $path . $errorCorrectionLevel . '.' . $matrixPointSize . '.png';
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        echo "<img src='$filename' />";
    }

    //测试上传图片
    function upload_pic()
    {
        $appid = "wx43430f4b6f59ed33";
        $secret = "e5f5c13709aa0ae7dad85865768855d6";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $result = file_get_contents($url);
        $result = json_decode($result, true);

        $type = "image";
        $filepath = "UPLOADS/test1.jpg";

        $filedata = array("media" => "@" . $filepath);
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$result['access_token']}&type=image";

        $result2 = $this->curl($url, $filedata);
        $result2 = json_decode($result2, true);
        //print_r($result2);exit;

        //发图片消息给某个OPEN_ID
        $host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$result['access_token']}";
        $array = array(
            "touser" => "oF49ruGwcpz1RtvYpG4xG_5gkoCo",
            "msgtype" => "image",
            "image" => array("media_id" => $result2['media_id'])
        );
        /*$array = array(
			"touser"=>"oF49ruGwcpz1RtvYpG4xG_5gkoCo",
			"msgtype"=>"text",
			"content"=>"I LOVE YOU"
		);*/
        $json_body = json_encode($array);
        //print_r($json_body);
        //echo $host;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);
        print_r($result);

    }

    function https_request($url, $post = null)
    {
        //$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
        $cookieFile = NULL;
        $hCURL = curl_init();
        curl_setopt($hCURL, CURLOPT_URL, $url);
        curl_setopt($hCURL, CURLOPT_TIMEOUT, 30);
        curl_setopt($hCURL, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($hCURL, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($hCURL, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($hCURL, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($hCURL, CURLOPT_ENCODING, "gzip,deflate");
        //curl_setopt($hCURL, CURLOPT_HTTPHEADER,$host);
        if ($post) {
            curl_setopt($hCURL, CURLOPT_POST, 1);
            curl_setopt($hCURL, CURLOPT_POSTFIELDS, $post);
        }
        $sContent = curl_exec($hCURL);
        var_dump(curl_error($hCURL));
        if ($sContent === FALSE) {
            $error = curl_error($hCURL);
            curl_close($hCURL);

            throw new \Exception($error . ' Url : ' . $url);
        } else {
            curl_close($hCURL);
            return $sContent;
        }
    }

    //用固定OPEN_ID获取头像
    function get_weixininfobyopenid()
    {
        $res = $this->get_weixininfo('oF49ruGwcpz1RtvYpG4xG_5gkoCo');
        print_r($res);
        echo substr($res['headimgurl'], 0, -1) . '46';
    }

    //跑7月份下单用户数据
    function get_memberstatus()
    {
        $o_model = D('reservation_order');
        $m_model = D('member');
        $b_model = D('carbrand');
        $s_model = D('carseries');
        $shop_model = D('shop');
        $model_model = D('carmodel');
        $o_map['status'] = 9;
        //$o_map['create_time'] = array(array('gt','1404144000'),array('lt','1406822400'));
        $o_info = $o_model->where($o_map)->group("uid")->select();
        echo $o_model->getLastsql();
        echo "<table>";
        foreach ($o_info as $k => $v) {
            $item = unserialize($v['item']);
            if (!empty($item['oil_id']) and $item['oil_id'] > 0 and !empty($item['filter_id']) and $item['filter_id'] > 0 and (empty($item['kongqi_id']) or $item['kongqi_id'] < 0) and (empty($item['kongtiao_id']) or $item['kongtiao_id'] < 0)) {

                $model = $model_model->where(array('model_id' => $v['model_id']))->find();
                $series = $s_model->where(array('series_id' => $model['series_id']))->find();
                $brand = $b_model->where(array('brand_id' => $series['brand_id']))->find();

                $o_info[$k]['brand'] = $brand['brand_name'];
                $o_info[$k]['series'] = $series['series_name'];
                $o_info[$k]['model'] = $model['model_name'];
                $o_info[$k]['shop_name'] = $shop['shop_name'];
                echo "<tr><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . $o_info[$k]['brand'] . "</td><td>" . $$o_info[$k]['series'] . "</td><td>" . $o_info[$k]['model'] . "</td><td>" . date('Y-m-d H:i:s', $o_info[$k]['create_time']) . "</td><td>" . $o_info[$k]['licenseplate'] . "</td></tr>";
            } else {
                //print_r($item);
                continue;
            }
        }
        echo "</table>";
    }

    //跑5-7月份事故车用户数据
    function get_bidstatus()
    {
        $o_model = D('bidorder');
        $i_model = D('insurance');
        $b_model = D('carbrand');
        $s_model = D('carseries');
        $model_model = D('carmodel');
        //$o_map['remark'] = array('neq','脚本订单,不要打电话');
        $o_map['create_time'] = array(array('gt', '1398873600'), array('lt', '1406822400'));
        $o_info = $o_model->where($o_map)->group("uid")->select();
        echo $o_model->getLastsql();
        echo "<table>";
        foreach ($o_info as $k => $v) {
            $insurance = $i_model->where(array('id' => $v['insurance_id']))->find();
            $brand = $b_model->where(array('brand_id' => $insurance['brand_id']))->find();
            $series = $s_model->where(array('series_id' => $insurance['series_id']))->find();
            $model = $model_model->where(array('model_id' => $insurance['model_id']))->find();
            $o_info[$k]['brand'] = $brand['brand_name'];
            $o_info[$k]['series'] = $series['series_name'];
            $o_info[$k]['model'] = $model['model_name'];
            echo "<tr><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . $o_info[$k]['brand'] . "</td><td>" . $$o_info[$k]['series'] . "</td><td>" . $o_info[$k]['model'] . "</td><td>" . $o_info[$k]['licenseplate'] . "</td><td>" . date('Y-m-d H:i:s', $o_info[$k]['tostore_time']) . "</td></tr>";
        }
        echo "</table>";
    }

    function test_pipei()
    {
        echo "111111111//";
        $object = '马勒 /';
        $pattern = '马勒';
        $a = strstr($object, $pattern);
        print_r($a);
        echo "//232222222";
    }

    function run_adminid()
    {
        $model_model = D('reservation_order');
        $log_model = D('operatelog');
        //$o_map['remark'] = array('neq','脚本订单,不要打电话');
        $o_map['admin_id'] = array('EQ', 'NULL');
        $o_info = $model_model->where($o_map)->select();
        echo $model_model->getLastsql();
        foreach ($o_info as $k => $v) {
            $log_info = $log_model->where(array('oid' => $v['id']))->order('id asc')->find();
            echo $log_info['operate_id'] . '</br>';
            $data['admin_id'] = $log_info['operate_id'];
            $model_model->where(array('id' => $v['id']))->save($data);
        }
    }

    function get_memberinfoByshop()
    {
        $shop_id = 299;
        $member_array = array();
        $map['shop_id'] = $shop_id;
        $map['create_time'] = array('egt', '1388505600');
        $brand = D('carbrand');
        $model = D('carmodel');
        $order = D('order');
        $membercoupon = D('membercoupon');
        $order_info = $order->where($map)->select();
        echo "<table><tr><td>姓名</td><td>电话</td><td>车牌号</td><td>车型</td><td>优惠券</td><td>到店时间</td></tr>";
        foreach ($order_info as $k => $v) {
            $brand_info = $brand->where(array('brand_id' => $v['brand_id']))->find();
            $model_info = $model->where(array('model_id' => $v['model_id']))->find();
            $car = $brand_info['brand_name'] . ' ' . $model_info['model_name'];
            $membercoupon_info = $membercoupon->where(array('uid' => $v['uid']))->select();
            if ($membercoupon_info) {
                foreach ($membercoupon_info as $kk => $vv) {
                    $order_info[$k]['coupon'] = $order_info[$k]['coupon'] . '</br>' . $vv['coupon_name'];
                }
            } else {
                $order_info[$k]['coupon'] = '没有绑定优惠券';
            }
            echo "<tr><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . $v['licenseplate'] . "</td><td>" . $car . "</td><td>" . $order_info[$k]['coupon'] . "</td><td>" . date('Y-m-d H:i:s', $v['order_time']) . "</td></tr>";
        }
        echo "</table>";
        $bidorder = D('bidorder');
        $bidorder_info = $bidorder->where($map)->select();
        echo "<table><tr><td>姓名</td><td>电话</td><td>车牌号</td><td>车型</td><td>定损金额</td><td>到店时间</td></tr>";
        $insurance = D('insurance');
        foreach ($bidorder_info as $kkk => $vvv) {
            $insurance_info = $insurance->where(array('id' => $vvv['insurance_id']))->find();
            $brand_info = $brand->where(array('brand_id' => $insurance_info['brand_id']))->find();
            $model_info = $model->where(array('model_id' => $insurance_info['model_id']))->find();
            $car = $brand_info['brand_name'] . ' ' . $model_info['model_name'];
            echo "<tr><td>" . $vvv['truename'] . "</td><td>" . $vvv['mobile'] . "</td><td>" . $vvv['licenseplate'] . "</td><td>" . $car . "</td><td>" . $insurance_info['loss_price'] . "</td><td>" . date('Y-m-d H:i:s', $vvv['tostore_time']) . "</td></tr>";
        }
        echo $bidorder->getLastsql();
    }

    //刷坐标
    function updatezuobiao()
    {
        $reservation_order_model = D('reservation_order');
        $map['id'] = array('egt', '1644');
        $order_info = $reservation_order_model->where($map)->select();
        //print_r($order_info);exit;

        foreach ($order_info as $k => $v) {
            $res = $this->geocoder($v['address']);
            $res = json_decode($res, true);
            if ($res['status'] == 0) {
                $update['longitude'] = $res['result']['location']['lng'];
                $update['latitude'] = $res['result']['location']['lat'];
            }
            $reservation_order_model->where(array('id' => $v['id']))->save($update);
            echo $reservation_order_model->getLastsql();
        }
    }

    /**
     * 百度Geocoding API
     */
    private function geocoder($address)
    {
        $res = file_get_contents("http://api.map.baidu.com/geocoder/v2/?address={$address}&output=json&ak=3db05159a3e3c55937fbf0160e2d8933");

        return $res;
    }


    //微信扫码支付模式二测试
    function test_pay()
    {
        $appid = 'wx43430f4b6f59ed33';
        $PartnerKey = "4cc5e45c2dc9fb3fdcc5517598f7059d";
        $attach = '支付测试';
        $body = 'NATIVE支付测试';
        $mch_id = '1219569401';
        $nonce_str = $this->create_noncestr();
        $notify_url = 'http://www.xieche.com.cn/weixinpaytest/notify_url.php';
        $out_trade_no = '3926|3927|3928';
        $spbill_create_ip = '14.23.150.211';
        $total_fee = '1';
        $trade_type = 'NATIVE';
        $string1 = 'appid=' . $appid . '&attach=' . $attach . '&body=' . $body . '&mch_id=' . $mch_id . '&nonce_str=' . $nonce_str . '&notify_url=' . $notify_url . '&out_trade_no=' . $out_trade_no . '&spbill_create_ip=' . $spbill_create_ip . '&total_fee=' . $total_fee . '&trade_type=' . $trade_type . '&key=' . $PartnerKey;
        echo 'string1=' . $string1;
        $sign = strtoupper(md5($string1));
        echo 'sign=' . $sign;
        $post = "<xml>
		<appid>{$appid}</appid>
		<attach>{$attach}</attach>
		<body>{$body}</body>
		<mch_id>{$mch_id}</mch_id>
		<nonce_str>{$nonce_str}</nonce_str>
		<notify_url>{$notify_url}</notify_url>
		<out_trade_no>{$out_trade_no}</out_trade_no>
		<spbill_create_ip>{$spbill_create_ip}</spbill_create_ip>
		<total_fee>{$total_fee}</total_fee>
		<trade_type>NATIVE</trade_type>
		<sign>{$sign}</sign>
		</xml>";

        $host = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $output = curl_exec($ch);
        var_dump($output);
        curl_close($ch);

        //解析返回xml
        $dom = new DOMDocument;
        $dom->loadXML($output);

        $retCode = $dom->getElementsByTagName('code_url');
        $retCode_value = $retCode->item(0)->nodeValue;

        echo "交易返回码：" . $retCode_value . "<br>";

        include("./ThinkPHP/Extend/Library/ORG/Qrcode/phpqrcode.php");
        $data = $retCode_value;
        //$data = 'http://www.xieche.com.cn';
        // 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'L';
        // 点的大小：1到10
        $matrixPointSize = 4;
        // 生成的文件名
        $path = "../UPLOADS/Shop/Logo/";
        //$path = "../UPLOADS/spread/weixin/";
        //if (!file_exists($path)){
        ///mkdir($path);
        //}

        $filename = $path . $errorCorrectionLevel . '.' . $matrixPointSize . '.png';
        echo 'url=' . $filename;
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        echo "<!DOCTYPE html>
				<html>
				<head>
					<meta charset=\"utf-8\">
					<title>微信支付</title>
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/reset.css\">
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/weixinpay_s.css\">
					<script type=\"text/javascript\" src=\"http://statics.xieche.com.cn/common/js/libs/jquery/jquery-1.11.min.js\"></script>
				</head>
				<body>
					<div id=\"container\">
						<div id=\"qr-code-contianer\">
							<img src=\"$filename\" alt=\"\" width=\"330\" height=\"330\" id=\"qr-code-img\" >
							<div id=\"qr-code-sign\" class=\"icon\"></div>
						</div>
					</div>
				</body>
				</html>";

    }

    function create_noncestr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
        }
        return $str;
    }

    function get_items()
    {
        $reservation_order_model = D('reservation_order');
        if ($_REQUEST['type'] == 1) {
            $map['order_time'] = array(array('egt', '1422288000'), array('elt', '1422374400'));
        } elseif ($_REQUEST['type'] == 2) {
            $map['order_time'] = array(array('egt', '1422374400'), array('elt', '1422460800'));
        } elseif ($_REQUEST['type'] == 3) {
            $map['order_time'] = array(array('egt', '1422460800'), array('elt', '1422547200'));
        }
        if ($_REQUEST['date']) {
            $start = strtotime($_REQUEST['date'] . " 0:00:00");
            $end = strtotime($_REQUEST['date'] . " 23:59:59");
            $map['order_time'] = array(array('egt', $start), array('elt', $end));
        }
        if ($_REQUEST['time']) {
            $start1 = strtotime($_REQUEST['time'] . " 0:00:00");
            $end1 = strtotime($_REQUEST['time'] . " 23:59:59");
            $map['create_time'] = array(array('egt', $start1), array('elt', $end1));
        }
        $map['city_id'] = 2;
        $map['status'] = array('neq', '8');
        $order_info = $reservation_order_model->where($map)->select();
        echo $reservation_order_model->getLastsql();
        //print_r($order_info);exit;

        foreach ($order_info as $k => $v) {
            //获取用户指定检测项目信息
            $item = unserialize($v['item']);
            foreach ($item['oil_detail'] as $_k => $_v) {
                if (!in_array($k, $oil_type)) {
                    $oil_type[] = $_k;
                    $oil[$_k] += $_v;
                } else {
                    $oil[$_k] += $_v;
                }
            }
            if ($item['filter_id'] != '' and !in_array($item['filter_id'], $filter_type)) {
                $filter_type[] = $item['filter_id'];
                $filter[$item['filter_id']]++;
            } else {
                $filter[$item['filter_id']]++;
            }

            if ($item['kongqi_id'] != '' and !in_array($item['kongqi_id'], $kongqi_type)) {
                $kongqi_type[] = $item['kongqi_id'];
                $kongqi[$item['kongqi_id']]++;
            } else {
                $kongqi[$item['kongqi_id']]++;
            }

            if ($item['kongtiao_id'] != '' and !in_array($item['kongtiao_id'], $kongtiao_type)) {
                $kongtiao_type[] = $item['kongtiao_id'];
                $kongtiao[$item['kongtiao_id']]++;
            } else {
                $kongtiao[$item['kongtiao_id']]++;
            }
        }
        //print_r($order);
        //print_r($oil);
        echo "</br></br></br><b>机油配件</b>：</br>";
        foreach ($oil as $kk => $vv) {
            if (!$kk) {
                continue;
            }
            $omodel = D('item_oil');
            $info = $omodel->where(array('id' => $kk))->find();
            echo "</br>" . $info['name'] . ' ' . $info['norms'] . 'L :  ' . $vv;
        }
        echo "</br></br></br><b>机滤配件</b>：</br>";
        foreach ($filter as $kkk => $vvv) {
            if (!$kkk) {
                continue;
            }
            $fmodel = D('item_filter');
            $info = $fmodel->where(array('id' => $kkk))->find();
            echo "</br>" . $info['name'] . ':  ' . $vvv;
        }
        //print_r($kongqi);
        echo "</br></br></br><b>空气滤配件</b>：</br>";
        foreach ($kongqi as $kkkk => $vvvv) {
            if (!$kkkk) {
                continue;
            }
            $fmodel = D('item_filter');
            $info = $fmodel->where(array('id' => $kkkk))->find();
            echo "</br>" . $info['name'] . ':  ' . $vvvv;
        }
        //print_r($kongtiao);
        echo "</br></br></br><b>空调滤配件</b>：</br>";
        foreach ($kongtiao as $kkkkk => $vvvvv) {
            if (!$kkkkk or $kkkkk == '-1') {
                continue;
            }
            $fmodel = D('item_filter');
            $info = $fmodel->where(array('id' => $kkkkk))->find();
            echo "</br>" . $info['name'] . ':  ' . $vvvvv;
        }
    }

    //获取车辆数据
    function get_car()
    {
        $o_model = D('order');
        $r_model = D('reservation_order');
        $i_model = D('insurance');
        $membercoupon_model = D('membercoupon');
        $member_model = D('member');
        $b_model = D('carbrand');
        $s_model = D('carseries');
        $m_model = D('carmodel');
        $map['create_time'] = array(array('egt', '1404144000'), array('elt', time()));
        $map['order_state'] = array('neq', '-1');
        $order = $o_model->where($map)->field("count(*) as count,model_id")->group("model_id")->select();
        //echo $o_model->getLastsql();
        foreach ($order as $k => $v) {
            $o_info[$v['model_id']] = $v['count'];
        }

        unset($map['order_state']);
        $map['truename'] = array('notlike', '测%');
        $map['status'] = array('neq', '8');
        $reservation_order = $r_model->where($map)->field("count(*) as count,model_id")->group("model_id")->select();
        //echo $r_model->getLastsql();
        foreach ($reservation_order as $kk => $vv) {
            $r_info[$vv['model_id']] = $vv['count'];
        }
        $hebin1 = $this->array_add($o_info, $r_info);
        //print_r($hebin1);

        unset($map['status']);
        unset($map['truename']);
        $insurance = $i_model->where($map)->field("count(*) as count,model_id")->group("model_id")->select();
        //echo $i_model->getLastsql();
        foreach ($insurance as $_k => $_v) {
            $i_info[$_v['model_id']] = $_v['count'];
        }
        $hebin2 = $this->array_add($hebin1, $i_info);
        //print_r($hebin2);
        echo "<table><tr><th>车型</th><th>数量</th></tr>";
        foreach ($hebin2 as $y => $z) {
            $model_info = $m_model->where(array('model_id' => $y))->find();
            $series_info = $s_model->where(array('series_id' => $model_info['series_id']))->find();
            $brand_info = $b_model->where(array('brand_id' => $series_info['brand_id']))->find();
            echo '<tr><td>' . $brand_info['brand_name'] . ' ' . $series_info['series_name'] . ' ' . $model_info['model_name'] . '</td><td>' . $z . '</td></tr>';
        }
        echo "</table>";
    }

    //键值相同数组求和
    function array_add($a, $b)
    {
        //根据键名获取两个数组的交集
        $arr = array_intersect_key($a, $b);
        //遍历第二个数组，如果键名不存在与第一个数组，将数组元素增加到第一个数组
        foreach ($b as $key => $value) {
            if (!array_key_exists($key, $a)) {
                $a[$key] = $value;
            }
        }
        //计算键名相同的数组元素的和，并且替换原数组中相同键名所对应的元素值
        foreach ($arr as $key => $value) {
            $a[$key] = $a[$key] + $b[$key];
        }
        //返回相加后的数组
        return $a;
    }

    function get_noorder_checkreport()
    {
        $r_model = D('reservation_order');
        $c1_model = D('checkreport_total');
        $c2_model = D('check_report');
        $m_model = D('member');
        $car_model = D('carmodel');
        //$info = $m_model->where(array('fromstatus'=>'52'))->Distinct(true)->field('mobile')->select();
        //$sql="SELECT DISTINCT `mobile` FROM `xc_member` WHERE (`fromstatus` = '52')AND mobile NOT IN (SELECT mobile FROM xc_reservation_order)";
        //$sql="SELECT mobile FROM `xc_reservation_order` WHERE 1 and remark like '检测报告转检测订单'";
        //$sql = "SELECT * FROM `xc_reservation_order` WHERE 1 AND order_time>1412092800 AND order_time<1420041600 AND STATUS =9 AND order_type =1 AND amount!=0 ORDER BY rand()";
        $sql = "SELECT * FROM `xc_reservation_order` WHERE 1 AND order_time >1412092800 AND order_time <1420041600 AND STATUS =9 AND order_type =2 ORDER BY create_time ASC LIMIT 200";
        $list = $m_model->query($sql);

        $num = 0;
        foreach ($list as $k => $v) {
            /*
			$c1_info = $c1_model->where(array('mobile'=>$v['mobile']))->find();
			echo $c1_model->getLastsql();
			$c2_info = $c2_model->where(array('a1'=>$v['mobile']))->find();
			//print_r(unserialize($c1_info['data']));
			$info = unserialize($c1_info['data']);
			*/
            $data['truename'] = $v['truename'];
            $data['uid'] = $v['uid'];
            $data['mobile'] = $v['mobile'];
            $data['pay_type'] = 1;//现金
            $data['pay_status'] = 1;//已支付
            $data['status'] = 9;//服务完成
            $data['order_type'] = 1;
            $data['amount'] = 368;
            $data['technician_id'] = rand(33, 34);
            //$data['item'] = $v['item'];
            $data['dikou_amount'] = $v['dikou_amount'];
            $data['address'] = $v['address'];
            $data['model_id'] = $v['model_id'];
            $data['licenseplate'] = $v['licenseplate'];
            $data['longitude'] = $v['longitude'];
            $data['latitude'] = $v['latitude'];
            $data['car_reg_time'] = $v['car_reg_time'];
            /*$model = $car_model->where(array('model_name'=>$info['car_model']))->find();
			echo $car_model->getLastsql();
			if($model){
				$data['model_id'] = $model['model_id'];
				print_r($data);
			}*/
            $copy_info = $r_model->where(array('id' => '3302'))->find();
            $data['item'] = $copy_info['item'];

            $info = $r_model->where(array('remark' => '脚本订单,不要打电话'))->order('rand()')->find();

            //$data['remark'] = '检测报告转检测订单';
            //$data['remark'] = '检测报告转368保养订单';
            //$data['remark'] = '10-12月回头客';
            $data['remark'] = '10-12月回头客转368';
            $map['id'] = $info['id'];
            $r_model->where($map)->save($data);
            echo $r_model->getLastsql();
            //绑定检测报告订单号
            //$c1_model->where(array('mobile'=>$v['mobile']))->save(array('reservation_id'=>$info['id']));
            //echo $c1_model->getLastsql();
            $num++;

        }
        echo '更新数据:' . $num;
    }

    function get_201501_data()
    {
        $r_model = D('reservation_order');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        $b_model = D('carbrand');
        $s_model = D('carseries');
        $model_model = D('carmodel');
        $map['order_time'] = array(array('egt', '1441036800'), array('elt', '1443628800'));
        $map['status'] = 9;
        //$map['city_id'] = 1;
        $map['order_type'] = array('neq', '2');
        $map['amount'] = array('egt', '0');

        //$map['pay_type'] = 4;
        $info = $r_model->where($map)->select();
        

        //echo $r_model->getLastsql();
        echo "<table><tr><th>订单号</th><th>城市</th><th>上门日期</th><th>支付方式</th><th>机油</th><th>价格</th><th>机滤</th><th>价格</th><th>空气滤</th><th>价格</th><th>空调滤</th><th>价格</th><th>服务费</th><th>总价</th><th>姓名</th><th>车型</th><th>手机号码</th><th>用户备注</th><th>客服备注</th></tr>";
        $i = 0;
        foreach ($info as $k => $v) {
            /*$map_r['order_time'] = array('egt','1420041600');
			$map_r['mobile'] = $v['mobile'];
			$count = $r_model->where($map_r)->count();
			//echo $r_model->getLastsql();exit;
			if($count>0){
				continue;
			}*/
            $i++;
            $v['order_time'] = date('Y-m-d H:i:s', $v['order_time']);
            if ($v['pay_type'] == 1) {
                $v['pay_type'] = '现金支付';
            }
            if ($v['pay_type'] == 2) {
                $v['pay_type'] = '在线支付';
            }
            if ($v['pay_type'] == 3) {
                $v['pay_type'] = 'POS支付';
            }
            if ($v['pay_type'] == 4) {
                $v['pay_type'] = '淘宝支付';
            }
            if ($v['city_id'] == 1) {
                $v['city_id'] = '上海';
            }
            if ($v['city_id'] == 2) {
                $v['city_id'] = '杭州';
            }
            $item = unserialize($v['item']);
            $oil_data = $item['oil_detail'];
            //print_r($item['price']);
            $oil = '';
            $oil_price = '';
            $filter = '';
            $filter_price = '';
            $kongqi = '';
            $kongqi_price = '';
            $kongtiao = '';
            $kongtiao_price = '';
            $value = '';
            $norms = '';
            foreach ($item['price']['oil'] as $kk => $vv) {
                if ($vv > 0) {
                    $o_info = $o_model->where(array('id' => $kk))->find();
                    $oil_price += $vv;
                }
            }
            foreach ($oil_data as $_id => $num) {
                if ($num > 0) {
                    $res = $o_model->field('name,price,type,norms')->where(array('id' => $_id))->find();
                    $norms += $res['norms'] * $num;
                    $oil = $res['name'] . ' ' . $norms . 'L';
                }
            }
            foreach ($item['price']['filter'] as $a => $b) {
                if ($b > 0) {
                    $f_info = $f_model->where(array('id' => $a))->find();
                    $filter = $f_info['name'];
                    $filter_price = $b;
                }
            }
            foreach ($item['price']['kongqi'] as $aa => $bb) {
                if ($bb > 0) {
                    $f_info = $f_model->where(array('id' => $aa))->find();
                    $kongqi = $f_info['name'];
                    $kongqi_price = $bb;
                }
            }
            foreach ($item['price']['kongtiao'] as $_k => $_v) {
                if ($_v > 0) {
                    $f_info = $f_model->where(array('id' => $_k))->find();
                    $kongtiao = $f_info['name'];
                    $kongtiao_price = $_v;
                }
            }

            if (!empty($v['replace_code'])) {
                $value = 99 - $this->get_codevalue($v['replace_code']);
            } elseif (empty($v['replace_code']) and $v['order_type'] > 1) {
                $value = 0;
            } elseif ($v['order_type'] == 1) {
                $value = 99;
            }

            $model = $model_model->where(array('model_id' => $v['model_id']))->find();
            $series = $s_model->where(array('series_id' => $model['series_id']))->find();
            $brand = $b_model->where(array('brand_id' => $series['brand_id']))->find();

            echo "<tr><td>" . $v['id'] . "</td><td>" . $v['city_id'] . "</td><td>" . $v['order_time'] . "</td><td>" . $v['pay_type'] . "</td><td>" . $oil . "</td><td>" . $oil_price . "</td><td>" . $filter . "</td><td>" . $filter_price . "</td><td>" . $kongqi . "</td><td>" . $kongqi_price . "</td><td>" . $kongtiao . "</td><td>" . $kongtiao_price . "</td><td>" . $value . "</td><td>" . $v['amount'] . "</td><td>" . $v['truename'] . "</td><td>" . $brand['brand_name'] . $series['series_name'] . $model['model_name'] . "</td><td>" . $v['mobile'] . "</td><td>" . $v['remark'] . "</td><td>" . $v['operator_remark'] . "</td></tr>";
            //exit;
        }
        echo "</table>";
        echo 'i=' . $i;
    }

    function get_one()
    {
        $r_model = D('reservation_order');
        $map['status'] = 9;
        $map['order_time'] = array(array('egt', '1420041600'), array('elt', '1422720000'));
        $count = $r_model->where($map)->count();
        $count_sum = $r_model->where($map)->sum('amount');

        $map['status'] = 9;
        $map['operator_remark'] = array('like', '%IB%');
        //$map['order_time'] = array(array('egt','1422720000'),array('elt','1425139200'));
        $IB = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $IB_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%OB%');
        $OB = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $OB_sum = $r_model->where($map)->sum('amount');

        unset($map['operator_remark']);
        $map['order_type'] = 2;
        $jiance = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';

        unset($map['order_type']);
        $map['admin_id'] = array('exp', 'is null');
        $huodong = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $huodong_sum = $r_model->where($map)->sum('amount');
        echo "111111111111111";
        $map['admin_id'] = array('exp', 'is not null');
        $map['remark'] = array('notlike', '%代下单%');
        $map['pa_id'] = array('exp', 'is null');
        $pc = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $pc_sum = $r_model->where($map)->sum('amount');

        unset($map['pa_id']);
        $map['remark'] = array('like', '%代下单%');
        $admin = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $admin_sum = $r_model->where($map)->sum('amount');

        unset($map['remark']);
        $map['pa_id'] = array('exp', 'is not null');
        $weixin = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $weixin_sum = $r_model->where($map)->sum('amount');

        unset($map['pa_id']);
        $map['operator_remark'] = array('like', '%三星%');
        $sanxing1 = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $sanxing_sum1 = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%3星%');
        $sanxing2 = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $sanxing_sum2 = $r_model->where($map)->sum('amount');

        $sanxing = $sanxing1 + $sanxing2;
        $sanxing_sum = $sanxing_sum1 + $sanxing_sum2;

        $map['operator_remark'] = array('like', '%百度%');
        $baidu = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $baidu_sum = $r_model->where($map)->sum('amount');

        unset($map['operator_remark']);
        $map['order_type'] = array('in', '3,13,14,15,16,17,18,19');
        $taobao = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $taobao_sum = $r_model->where($map)->sum('amount');

        unset($map['order_type']);
        $map['operator_remark'] = array('like', '%微信%');
        $wx = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $wx_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%微信活动%');
        $wxhd = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $wxhd_sum = $r_model->where($map)->sum('amount');

        $wx = $wx - $wxhd;
        $wx_sum = $wx_sum - $wxhd_sum;

        $map['operator_remark'] = array('like', '%小区%');
        $xiaoqu = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $xiaoqu_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%事故车%');
        $shiguche = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $shiguche_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%介绍%');
        $jieshao = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $jieshao_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%平安好车%');
        $pingan = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $pingan_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%微博%');
        $weibo = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $weibo_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%搜索%');
        $sousuo = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $sousuo_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%朋友圈%');
        $pengyouquan = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $pengyouquan_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%加油站%');
        $jiayouzhan = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $jiayouzhan_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%宝驾%');
        $baojia = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $baojia_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%订单编号%');
        $map['order_type'] = 2;
        $tb = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $tb_sum = $r_model->where($map)->sum('amount');
        echo "####" . $tb;
        $taobao = $taobao + $tb;
        $taobao_sum = $taobao_sum + $tb_sum;

        unset($map['order_type']);
        $map['operator_remark'] = array('like', '%e袋洗%');
        $yidaixi = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $yidaixi_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%老客户%');
        $laokehu = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $laokehu_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%养车点点%');
        $yangchediandian = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $yangchediandian_sum = $r_model->where($map)->sum('amount');

        $map['operator_remark'] = array('like', '%驴妈妈%');
        $lvmama = $r_model->where($map)->count();
        echo $r_model->getLastsql();
        echo '</br>';
        $lvmama_sum = $r_model->where($map)->sum('amount');

        $sql = "SELECT sum(amount) as sum,count(*) as count FROM `xc_reservation_order` WHERE (`status` =9) AND ((`order_time` >= '1420041600') AND (`order_time` <= '1422720000')) AND replace_code IS NOT NULL AND operator_remark NOT LIKE '%IB%' AND operator_remark NOT LIKE '%OB%' AND operator_remark NOT LIKE '%淘宝%' AND operator_remark NOT LIKE '%3星%' AND operator_remark NOT LIKE '%三星%' AND operator_remark NOT LIKE '%百度%' and order_type not in(3,13,14,15,16,17,18,19)";
        $code_sum = $r_model->query($sql);
        $sql = "SELECT sum(amount) as sum,count(*) as count FROM `xc_reservation_order` WHERE (`status` =9) AND ((`order_time` >= '1420041600') AND (`order_time` <= '1422720000')) AND replace_code IS NULL AND operator_remark NOT LIKE '%IB%' AND operator_remark NOT LIKE '%OB%' AND operator_remark NOT LIKE '%淘宝%' AND operator_remark NOT LIKE '%3星%' AND operator_remark NOT LIKE '%三星%' AND operator_remark NOT LIKE '%百度%' and order_type not in(3,13,14,15,16,17,18,19)";
        $qita_sum = $r_model->query($sql);
        print_r($qita_sum);

        $qita = $qita_sum['0']['count'] - $wx - $wxhd - $xiaoqu - $shiguche - $jieshao - $pingan - $weibo - $sousuo - $pengyouquan - $jiayouzhan - $baojia - $yidaixi - $laokehu - $yangchediandian - $lvmama - $tb;
        $qita_sum = $qita_sum['0']['sum'] - $wx_sum - $wxhd_sum - $xiaoqu_sum - $shiguche_sum - $jieshao_sum - $pingan_sum - $weibo_sum - $sousuo_sum - $pengyouquan_sum - $jiayouzhan_sum - $baojia_sum - $yidaixi_sum - $laokehu_sum - $yangchediandian_sum - $lvmama_sum - $tb_sum;

        echo "合计：" . $count . "(￥" . $count_sum . ")</br>现场活动:" . $huodong . "(￥" . $huodong_sum . ")</br>网站:" . $pc . "(￥" . $pc_sum . ")</br>后台代下单:" . $admin . "(￥" . $admin_sum . ")</br>微信:" . $weixin . "(￥" . $weixin_sum . ")</br></br></br>IB:" . $IB . "(￥" . $IB_sum . ")</br>OB:" . $OB . "(￥" . $OB_sum . ")</br>三星:" . $sanxing . "(￥" . $sanxing_sum . ")</br>百度:" . $baidu . "(￥" . $baidu_sum . ")</br>淘宝:" . $taobao . "(￥" . $taobao_sum . ")</br>优惠券:" . $code_sum['0']['count'] . "(￥" . $code_sum['0']['sum'] . ")</br>微信:" . $wx . "(￥" . $wx_sum . ")</br>微信活动:" . $wxhd . "(￥" . $wxhd_sum . ")</br>小区:" . $xiaoqu . "(￥" . $xiaoqu_sum . ")</br>事故车:" . $shiguche . "(￥" . $shiguche_sum . ")</br>介绍:" . $jieshao . "(￥" . $jieshao_sum . ")</br>平安好车:" . $pingan . "(￥" . $pingan_sum . ")</br>微博:" . $weibo . "(￥" . $weibo_sum . ")</br>搜索:" . $sousuo . "(￥" . $sousuo_sum . ")</br>朋友圈:" . $pengyouquan . "(￥" . $pengyouquan_sum . ")</br>加油站:" . $jiayouzhan . "(￥" . $jiayouzhan_sum . ")</br>宝驾:" . $baojia . "(￥" . $baojia_sum . ")</br>e袋洗:" . $yidaixi . "(￥" . $yidaixi_sum . ")</br>老客户:" . $laokehu . "(￥" . $laokehu_sum . ")</br>养车点点:" . $yangchediandian . "(￥" . $yangchediandian_sum . ")</br>驴妈妈:" . $lvmama . "(￥" . $lvmama_sum . ")</br>其他:" . $qita . "(￥" . $qita_sum . ")";
    }

    //跑1200个订单数据
    function run_order()
    {
        exit;
        $model_membernew = D('Membernew');
        $model_member = D('Member');
        $model_order = D('Order');

        $membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=1 AND is_order_insert=1 ORDER BY uid");
        $i = 0;
        foreach ($membernew as $key => $val) {
            if ($i == 223) {
                echo $i;
                exit;
            }
            $data = array();
            //$data['password'] = md5(",,,,,,,,,,");
            $data['mobile'] = $val['mobile'];
            $data['fromstatus'] = '30';
            //$data['reg_time'] = time();
            //$data['memo'] = "短信用户  ".$val['text'];

            $member = $model_member->where($data)->find();
            $shop_id = $val['shop_id'];

            if ($val['shop_id'] == 54 || $val['shop_id'] == 56) {
                $shop_ids = "58,121,345,346,356,362,364,1345,1356,55,125,57,370,115,56,1330,348,1346";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($val['shop_id'] == 8) {
                $shop_ids = "10,87,297,304,306,9,11,298,299,118,289,291,1338,1344";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($val['shop_id'] == 62 || $val['shop_id'] == 103) {
                $shop_ids = "62,536,538,541,542,120,537";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($val['shop_id'] == 23) {
                $shop_ids = "21,23,110";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }
            if ($val['shop_id'] == 71) {
                $shop_ids = "204,205";
                $shop_ids_arr = explode(",", $shop_ids);
                $arr_index = array_rand($shop_ids_arr);
                $shop_id = $shop_ids_arr[$arr_index];
            }

            $lastorder = $model_order->where(array('shop_id' => $shop_id))->order('id DESC')->find();

            $order_data = array();
            $order_data['order_des'] = "membernew";
            $order_data['uid'] = $member['uid'];
            $order_data['shop_id'] = $shop_id;
            $order_data['brand_id'] = $val['brand_id'];
            $order_data['series_id'] = $val['series_id'];
            $order_data['model_id'] = $val['model_id'];
            $order_data['timesaleversion_id'] = isset($lastorder['timesaleversion_id']) ? $lastorder['timesaleversion_id'] : 0;
            $order_data['service_ids'] = 9;
            $order_data['product_sale'] = 0.00;
            $order_data['workhours_sale'] = isset($lastorder['workhours_sale']) ? $lastorder['workhours_sale'] : 0.70;
            $order_data['truename'] = $val['truename'];
            $order_data['mobile'] = $val['mobile'];
            $order_data['licenseplate'] = $val['licenseplate'];
            $order_data['total_price'] = rand(350, 800);

            //$order_data['order_time'] = 	mktime(rand(8,17),0,0,date("m",time()),date("d",time())+rand(1,3),date("Y",time()));
            $order_data['create_time'] = mktime(rand(8, 17), 0, 0, 2, rand(1, 15), date("Y", time()));
            $order_data['order_time'] = $order_data['create_time'] + 86400;
            $order_data['order_state'] = 2;
            $order_data['complete_time'] = $order_data['order_time'] + 3600;
            $order_data['iscomment'] = 1;
            $order_data['remark'] = "脚本订单,不要打电话";

            $model_order->add($order_data);


            $model_membernew->where(array("uid" => $val['uid']))->save(array('is_insert' => 1, 'is_order_insert' => 2));
            $i++;
        }
        echo 'i=' . $i;
        echo "ok";
    }

    //跑40个券
    function run_membercoupon()
    {
        exit;
        $model_coupon = D('Coupon');
        $model_membercoupon = D('Membercoupon');
        //随机取40张价格在400-600间的券
        for ($a = 1; $a <= 40; $a++) {
            $map_coupon['coupon_amount'] = array(array('egt', '400'), array('elt', '600'));
            $map_coupon['end_time'] = array('gt', time());
            $coupon = $model_coupon->where($map_coupon)->order('rand()')->find();
            echo $model_coupon->getLastsql();
            $data['uid'] = 6591;
            $data['coupon_id'] = $coupon['id'];
            $data['coupon_name'] = $coupon['coupon_name'];
            $data['shop_ids'] = $coupon['shop_id'];
            $data['mobile'] = 13661743916;
            $data['end_time'] = $coupon['end_time'];
            $data['start_time'] = $coupon['start_time'];
            $data['coupon_type'] = $coupon['coupon_type'];
            $data['coupon_amount'] = $coupon['coupon_amount'];
            $data['create_time'] = mktime(rand(8, 17), 0, 0, 2, rand(1, 15), date("Y", time()));
            $membercoupon_id = $model_membercoupon->add($data);
        }
        echo 'OK';
    }

    function get_carstyle()
    {
        //exit;
        $car_brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $car_series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $car_model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $filter_model = M('tp_xieche.item_filter', 'xc_');  //车型号

        $brand_info = $car_brand_model->select();
        echo "<table>";
        foreach ($brand_info as $x => $y) {
            $series_info = $car_series_model->where(array('brand_id' => $y['brand_id']))->select();
            foreach ($series_info as $xx => $yy) {
                $model_info = $car_model_model->where(array('series_id' => $yy['series_id']))->select();


                foreach ($model_info as $kkk => $vvv) {
                    $item_set = array();
                    $item_set_new = array();
                    $condition['model_id'] = $vvv['model_id'];
                    $style_info = $car_model_model->where($condition)->find();
                    $set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
                    if ($set_id_arr) {
                        foreach ($set_id_arr as $k => $v) {
                            if (is_array($v)) {
                                foreach ($v as $_k => $_v) {
                                    $item_condition['id'] = $_v;
                                    if (!$_SESSION['order_type'] or !in_array($_SESSION['order_type'], array(47, 49))) {
                                        $item_condition['name'] = array('notlike', '%pm2%');
                                    }
                                    if (in_array($_SESSION['order_type'], array(47, 49))) {
                                        $item_condition['name'] = array('like', '%pm2%');
                                    }
                                    $item_info_res = $filter_model->where($item_condition)->find();
                                    if ($item_info_res) {
                                        $item_info['id'] = $item_info_res['id'];
                                        $item_info['name'] = $item_info_res['name'];
                                        $item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
                                        $item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
                                        $item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
                                        $item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
                                        $item_set[$k][$_k] = $item_info;
                                        //排除数组中缺乏元素页面选项空白的问题
                                        if (!$item_set[$k][0] and $item_set[$k][1]) {
                                            $item_set[$k][0] = $item_set[$k][1];
                                            unset($item_set[$k][1]);
                                        }
                                    } else {
                                        continue;
                                    }
                                }
                                foreach ($item_set[$k] as $kk => $vv) {
                                    if ($item_set[$k][$kk]['price'] < $item_set[$k][$kk - 1]['price']) {
                                        $item_set_new[$k][$kk - 1] = $item_set[$k][$kk];
                                        $item_set_new[$k][$kk] = $item_set[$k][$kk - 1];
                                    } else {
                                        $item_set_new[$k][$kk] = $item_set[$k][$kk];
                                    }
                                }
                                $item_set = $item_set_new;
                            }
                        }
                    }

                    unset($vvv['jilv']);
                    unset($vvv['kongqi']);
                    unset($vvv['kongtiao']);
                    foreach ($item_set[1] as $a => $b) {
                        $vvv['jilv'][] = $b['price'];
                    }
                    foreach ($item_set[2] as $aa => $bb) {
                        $vvv['kongqi'][] = $bb['price'];
                    }
                    foreach ($item_set[3] as $aaa => $bbb) {
                        $vvv['kongtiao'][] = $bbb['price'];
                    }
                    echo '<tr><td>' . $y['brand_name'] . '</td><td>' . $yy['series_name'] . '</td><td>' . $vvv['model_name'] . '</td><td>' . $vvv['oil_mass'] . '</td><td>' . min($vvv['jilv']) . '</td><td>' . max($vvv['jilv']) . '</td><td>' . min($vvv['kongtiao']) . '</td><td>' . max($vvv['kongtiao']) . '</td><td>' . (min($vvv['jilv']) + min($vvv['kongtiao']) + min($vvv['kongtiao'])) . '</td><td>' . (max($vvv['jilv']) + max($vvv['kongtiao']) + max($vvv['kongtiao'])) . '</td></tr>';
                    //exit;
                }
            }
        }
        echo "</table>";
    }

    private function _curl($url, $post = NULL, $host = NULL)
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
        $cookieFile = NULL;
        $hCURL = curl_init();
        curl_setopt($hCURL, CURLOPT_URL, $url);
        curl_setopt($hCURL, CURLOPT_TIMEOUT, 30);
        curl_setopt($hCURL, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($hCURL, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($hCURL, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($hCURL, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($hCURL, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($hCURL, CURLOPT_HTTPHEADER, $host);
        if ($post) {
            curl_setopt($hCURL, CURLOPT_POST, 1);
            curl_setopt($hCURL, CURLOPT_POSTFIELDS, $post);
        }

        $sContent = curl_exec($hCURL);
        // var_dump(curl_getinfo($hCURL));exit;
        if ($sContent === FALSE) {
            $error = curl_error($hCURL);
            curl_close($hCURL);

            throw new \Exception ($error . ' Url : ' . $url);
        } else {
            curl_close($hCURL);
            return $sContent;
        }
    }

    function alitest222()
    {
        $url = 'http://www.xieche.com.cn/alipay_fuwuchuang/ali_test.php';
        $post = array(
            "title" => "支付宝服务窗推送测试-标题",
            "desc" => "支付宝服务窗推送测试-简介",
            "url" => "http://www.xieche.com.cn",
            "imageUrl" => "http://www.xieche.com.cn/Public_new/images/index/logo.png",
            "authType" => "loginAuth",
            "toUserId" => "20881049034784181452509782219886"
        );
        $data = $this->_curl($url, $post);
        var_dump($data);
    }

    function copy_order()
    {
        if ($_REQUEST['password'] != '123223fqcd') {
            exit();
        }
        echo "<form method=\"post\" action=\"http://www.xieche.com.cn/test/copy_order\">
		原单号：<input type=\"text\" name=\"old_id\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;目标单号：<input type=\"text\" name=\"new_id\">
		<input type=\"radio\" name=\"type\" value=\"1\">清洗节气门
		<input type=\"radio\" name=\"type\" value=\"2\">上门检测
		<input type=\"radio\" name=\"type\" value=\"3\">机油</br>
		<input type=\"hidden\" name=\"password\" value=\"123223fqcd\">
		<input type=\"submit\">
		</form>";
        print_r($_REQUEST);
        if (!empty($_REQUEST) and (!$_REQUEST['old_id'] or !$_REQUEST['new_id'] or !$_REQUEST['type'] or $_REQUEST['password'] != '123223fqcd')) {
            exit('参数不全');
        } else {
            $r_model = D('reservation_order');
            //节气门套餐
            if ($_REQUEST['type'] == 1) {
                //8708
                $info = $r_model->where(array('id' => $_REQUEST['old_id']))->find();
                $data['uid'] = $info['uid'];
                $data['truename'] = $info['truename'];
                $data['address'] = $info['address'];
                $data['mobile'] = $info['mobile'];
                $data['model_id'] = $info['model_id'];
                $data['amount'] = 68;
                $data['dikou_amount'] = 31;
                $item_content = array(
                    'oil_id' => $_SESSION['item_0'],
                    'oil_detail' => $_SESSION['oil_detail'],
                    'filter_id' => $_SESSION['item_1'],
                    'kongqi_id' => $_SESSION['item_2'],
                    'kongtiao_id' => $_SESSION['item_3'],
                    'price' => array(
                        'oil' => array(
                            $oil_1_id => $oil_1_price,
                            $oil_2_id => $oil_2_price
                        ),
                        'filter' => array(
                            $filter_id => $filter_price
                        ),
                        'kongqi' => array(
                            $kongqi_id => $kongqi_price
                        ),
                        'kongtiao' => array(
                            $kongtiao_id => $kongtiao_price
                        )
                    )
                );

                $data['item'] = serialize($item_content);
                $data['pay_type'] = 1;
                $data['pay_status'] = 1;
                $data['order_type'] = 15;
                $data['remark'] = '代下单: 抵扣套餐价格￥31;好动力套餐:节气门清洗+38项全车检测+7项细节养护';
                $data['admin_id'] = 1;
                $data['status'] = 9;
                $data['licenseplate'] = $info['licenseplate'];
                $data['technician_id'] = $info['technician_id'];
                $data['order_time'] = $info['order_time'];
                $data['origin'] = 4;//刷单
            }
            $count = $r_model->where(array('id' => $_REQUEST['new_id']))->count();
            if ($count > 0) {
                $res = $r_model->where(array('id' => $_REQUEST['new_id']))->save($data);
                echo $r_model->getLastsql();
                if ($res !== false) {
                    echo "更新成功";
                }
            } else {
                $data['id'] = $_REQUEST['new_id'];
                $data['create_time'] = $info['create_time'];
                $res = $r_model->add($data);
                echo $r_model->getLastsql();
                if ($res) {
                    echo "插入成功";
                }
            }
        }
    }

    //跑数据
    function run_4s()
    {
        //从事故车过来下4S单
        $o_model = D('order');
        $b_model = D('bidorder');
        $map['xc_bidorder.create_time'] = array(array('egt', '1425139200'), array('elt', '1427817600'));
        //$map['xc_bidorder.status']=1;
        //$map['xc_bidorder.order_status']=array('neq',3);
        $bidorder = $b_model->join('xc_insurance ON xc_insurance.id = xc_bidorder.insurance_id')->where($map)->select();
        //echo $b_model->getLastsql();
        //print_r($bidorder);
        $count = 0;
        foreach ($bidorder as $k => $v) {
            $o_map['create_time'] = array('egt', $v['create_time']);
            $o_map['order_des'] = 'membernew';

            $info = $o_model->where($o_map)->order('create_time asc')->find();
            //echo $o_model->getLastsql();
            $data['uid'] = $v['uid'];
            $data['shop_id'] = $v['shop_id'];
            $data['brand_id'] = $v['brand_id'];
            $data['series_id'] = $v['series_id'];
            $data['model_id'] = $v['model_id'];
            $data['timesaleversion_id'] = '';
            $data['service_ids'] = 30;
            $data['workhours_sale'] = '';
            $data['truename'] = $v['truename'];
            $data['mobile'] = $v['mobile'];
            $data['licenseplate'] = $v['licenseplate'];
            $data['total_price'] = $v['loss_price'];
            $data['remark'] = '';
            $data['order_time'] = $v['tostore_time'];
            $data['order_state'] = 2;
            $data['order_des'] = 'membernew_done';
            //print_r($v);
            print_r($data);
            exit;
            //print_r($info);
            $res = $o_model->where(array('id' => $info['id']))->save($data);
            echo $o_model->getLastsql();
            echo "</br>";
            if ($res !== false) {
                $count++;
            }
        }
        echo "count=" . $count;
    }

    //跑数据
    function run_4s_bidlist()
    {
        exit;
        //从事故车过来下4S单
        $o_model = D('order');
        $b_model = D('api_record');
        $bid_model = D('bidorder');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        //$map['xc_bidorder.create_time'] = array(array('egt','1425139200'),array('elt','1427817600'));
        //$map['xc_bidorder.status']=1;
        //$map['xc_bidorder.order_status']=array('neq',3);
        $map['remark'] = array('like', '%-%');
        $bidorder = $b_model->where($map)->select();
        //echo $b_model->getLastsql();
        //print_r($bidorder);
        $count = 0;
        $b_count = 0;
        $c_count = 0;
        $d_count = 0;
        $b_info = array();
        $s_info = array();
        $m_info = array();
        $data = array();
        foreach ($bidorder as $k => $v) {
            $bid_count = $bid_model->where(array('mobile' => $v['mobile']))->find();
            if ($bid_count > 0) {
                continue;
            }
            //$count++;
            $licenseplate = substr($v['remark'], 0, strrpos($v['remark'], '备'));
            $licenseplate = substr($licenseplate, -9);
            //echo 'licenseplate='.$licenseplate;
            $newa = substr($v['remark'], 0, strrpos($v['remark'], '-'));
            $find = substr($newa, 30);
            $panduan = substr($find, 0, 2);
            //echo 'panduan='.$panduan;
            if (ctype_alnum($panduan)) {
                //什么都没匹配到，随机给品牌往下取
                $suiji = array(1, 4, 8, 11, 17, 23, 25, 54, 55, 67);
                $kkk = array_rand($suiji, 1);
                $b_info['brand_id'] = $suiji[$kkk];
                $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
            } else {
                $b_info = $brand_model->where(array('brand_name' => array('like', '%' . $find . '%')))->find();
                //echo $brand_model->getLastsql();
                if (!$b_info) {
                    $find = substr($find, 6);
                    $b_info = $brand_model->where(array('brand_name' => array('like', '%' . $find . '%')))->find();
                    //echo $brand_model->getLastsql();
                }
                if ($b_info) {
                    $b_count++;
                    $after = substr($v['remark'], strrpos($v['remark'], '-') + 1, 6);
                    $s_info = $series_model->where(array('series_name' => array('like', '%' . $after . '%')))->find();
                    //echo $series_model->getLastsql();
                    if ($s_info) {
                        $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                    } else {
                        $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                    }
                }
                if ($s_info) {
                    $c_count++;
                    $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                    if (!$b_info) {
                        $b_info = $model_model->where(array('brand_id' => $s_info['brand_id']))->find();
                        //echo $model_model->getLastsql();
                    }
                } else {
                    //$after = substr($v['remark'],strrpos($v['remark'],'-')+1);
                    //$m_info = $model_model->where(array('model_name'=>array('like','%'.$after.'%')))->find();
                    //echo $model_model->getLastsql();
                    //if($m_info){ 
                    //$d_count++;
                    //$s_info = $series_model->where(array('series_id'=>$m_info['series_id']))->find();
                    //}
                }
                //直接品牌往下取
                if (!$s_info and $b_info) {
                    //echo "1111111111111";
                    $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                    $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                }
                //往上取
                /*if(!$b_info and $s_info){
					$b_info = $series_model->where(array('brand_id'=>$s_info['brand_id']))->find();
					$m_info = $model_model->where(array('series_id'=>$s_info['series_id']))->find();
				}*/
            }
            //$o_map['create_time'] = array('egt',$v['create_time']);
            $o_map['order_des'] = 'membernew';

            $info = $o_model->where($o_map)->order('create_time asc')->find();
            //echo $o_model->getLastsql();
            //$data['uid'] = $v['uid'];
            //$data['shop_id'] = $v['shop_id'];

            if ($b_info['brand_id'] == 0) {
                echo "2222222222";
                $suiji = array(1, 4, 8, 11, 17, 23, 25, 54, 55, 67);
                $kk = array_rand($suiji, 1);
                $b_info['brand_id'] = $suiji[$kk];
                //echo $b_info['brand_id'];
                $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
            }
            $data['brand_id'] = $b_info['brand_id'];
            $data['series_id'] = $s_info['series_id'];
            $data['model_id'] = $m_info['model_id'];
            //$data['timesaleversion_id'] = '';
            //$data['service_ids'] = 30;
            //$data['workhours_sale'] = '';
            $data['truename'] = $v['name'];
            $data['mobile'] = $v['mobile'];
            $data['licenseplate'] = $licenseplate;
            //$data['total_price'] = $v['loss_price'];
            $data['remark'] = '';
            //$data['order_time'] = $v['tostore_time'];
            $data['order_state'] = 2;
            $data['order_des'] = 'membernew_done_2';
            //print_r($v);
            //print_r($data);//exit;
            //print_r($info);
            /**/
            $res = $o_model->where(array('id' => $info['id']))->save($data);
            echo $o_model->getLastsql();
            echo "</br>";
            if ($res !== false) {
                $count++;
            }
        }
        echo "count=" . $count;
        //echo "count=".$count."b_count=".$b_count."c_count=".$c_count."d_count=".$d_count;
    }

    //跑数据
    function run_4s_bidlist2()
    {
        //echo $this->get_true_orderid('94722');exit;
        exit;
        //从事故车过来下4S单
        $o_model = D('order');
        $b_model = D('api_record');
        $bid_model = D('bidorder');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        //$map['xc_bidorder.create_time'] = array(array('egt','1425139200'),array('elt','1427817600'));
        //$map['xc_bidorder.status']=1;
        //$map['xc_bidorder.order_status']=array('neq',3);
        $map['remark'] = array('notlike', '%-%');
        $bidorder = $b_model->where($map)->select();
        //echo $b_model->getLastsql();exit;
        //print_r($bidorder);
        $count = 0;
        $b_count = 0;
        $c_count = 0;
        $d_count = 0;
        $b_info = array();
        $s_info = array();
        $m_info = array();
        $data = array();
        foreach ($bidorder as $k => $v) {
            $bid_count = $bid_model->where(array('mobile' => $v['mobile']))->find();
            if ($bid_count > 0) {
                continue;
            }
            //$count++;
            $licenseplate = substr($v['remark'], 0, strrpos($v['remark'], '备'));
            $licenseplate = substr($licenseplate, -9);
            //echo 'licenseplate='.$licenseplate;
            $newa = substr($v['remark'], strrpos($v['remark'], '注') + 3);
            //echo 'newa='.$newa;
            $find = mb_substr($v['remark'], strrpos($v['remark'], '注') + 6);
            if (ctype_alnum($find) or $find == '：' or $find == '') {
                //echo "0000000000";
                continue;
            } else {
                $b_info = $brand_model->where(array('brand_name' => array('like', '%' . $find . '%')))->find();
                //echo $brand_model->getLastsql();
                if (!$b_info) {
                    $find1 = substr($find, 6);
                    if (strlen($find1) != 0) {
                        $b_info = $brand_model->where(array('brand_name' => array('like', '%' . $find1 . '%')))->find();
                        //echo $brand_model->getLastsql();
                    }
                }
                if ($b_info) {
                    //echo "333333333";
                    $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                    $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                } else {
                    //echo "444444444";
                    $s_info = $series_model->where(array('series_name' => array('like', '%' . $find . '%')))->find();
                    //echo $series_model->getLastsql();
                }
                if ($s_info) {
                    //echo "55555555555";
                    $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                    //echo $model_model->getLastsql();
                    if (!$b_info) {
                        $b_info = $brand_model->where(array('brand_id' => $s_info['brand_id']))->find();

                    }
                } else {
                    continue;
                }
                //直接品牌往下取
                if (!$s_info and $b_info) {
                    //echo "1111111111111";
                    $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                    $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
                }
            }
            $o_map['create_time'] = array('egt', '1420041600');
            $o_map['order_des'] = 'membernew';

            $info = $o_model->where($o_map)->order('create_time asc')->find();
            //echo $o_model->getLastsql();exit;
            //$data['uid'] = $v['uid'];
            //$data['shop_id'] = $v['shop_id'];

            if ($b_info['brand_id'] == 0) {
                //echo "2222222222";
                $suiji = array(1, 4, 8, 11, 17, 23, 25, 54, 55, 67);
                $kk = array_rand($suiji, 1);
                $b_info['brand_id'] = $suiji[$kk];
                //echo $b_info['brand_id'];
                $s_info = $series_model->where(array('brand_id' => $b_info['brand_id']))->find();
                $m_info = $model_model->where(array('series_id' => $s_info['series_id']))->find();
            }
            $data['brand_id'] = $b_info['brand_id'];
            $data['series_id'] = $s_info['series_id'];
            $data['model_id'] = $m_info['model_id'];
            //$data['timesaleversion_id'] = '';
            //$data['service_ids'] = 30;
            //$data['workhours_sale'] = '';
            $data['truename'] = $v['name'];
            $data['mobile'] = $v['mobile'];
            $data['licenseplate'] = $licenseplate;
            //$data['total_price'] = $v['loss_price'];
            $data['remark'] = '';
            //$data['order_time'] = $v['tostore_time'];
            $data['order_state'] = 2;
            $data['order_des'] = 'membernew_done_3';
            //print_r($v);
            //print_r($data);if($k==200){exit;}
            //print_r($info);
            /*$res = $o_model->where(array('id'=>$info['id']))->save($data);
			echo $o_model->getLastsql();
			echo "</br>";
			if($res!==false){
				$count++;
			}*/
        }
        echo "count=" . $count;
        //echo "count=".$count."b_count=".$b_count."c_count=".$c_count."d_count=".$d_count;
    }

    function update_item()
    {
        $r_model = D('reservation_order');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        $model_model = D('carmodel');
        $map['order_time'] = array('egt', '1427644800');
        $map['status'] = array('neq', '8');
        $map['order_type'] = array('egt', '47');
        $info = $r_model->where($map)->select();
        echo $r_model->getLastsql();
        $count = 0;
        $item = array();
        $set_id_arr = array();
        foreach ($info as $aa => $bb) {
            $item = unserialize($bb['item']);
            //$array = array('725','764','765');
            //echo $item['kongtiao_id'].' ';
            //if(in_array($item['kongtiao_id'],$array)){
            $condition['model_id'] = $bb['model_id'];
            $style_info = $model_model->where($condition)->find();
            $set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();

            if ($set_id_arr) {
                foreach ($set_id_arr as $k => $v) {
                    if (is_array($v)) {
                        $item_set = array();
                        foreach ($v as $_k => $_v) {
                            $item_condition['id'] = $_v;
                            $item_info_res = $f_model->where($item_condition)->find();
                            $item_info['id'] = $item_info_res['id'];
                            $item_info['name'] = $item_info_res['name'];
                            $item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
                            $item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
                            $item_info['price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
                            $item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
                            $item_set[$k][$_k] = $item_info;
                            //print_r($item_set);
                            foreach ($item_set[3] as $a => $b) {
                                if ($b['id'] > 710) {
                                    $item['kongtiao_id'] = $b['id'];
                                } else {
                                    $item['kongtiao_id'] = '725';
                                }
                                $data['item'] = serialize($item);
                                $r_model->where(array('id' => $bb['id']))->save($data);
                                echo $r_model->getLastsql();
                                echo "</br>";
                                $count++;
                            }
                        }
                        /*foreach($item_set[$k] as $kk=>$vv){
								if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
									$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
									$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
								}else{
									$item_set_new[$k][$kk] = $item_set[$k][$kk];
								}
							}
							$item_set = $item_set_new;*/
                    }
                }
            }

            //}
        }
        echo 'count=' . $count;
    }

    function get_oil()
    {
        $r_model = D('reservation_order');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        $model_model = D('carmodel');
        $map['origin'] = 4;
        $map['remark'] = '代下单: 补机油';
        $info = $r_model->where($map)->select();
        $oil = array();
        $oil_detail = $o_model->field('id')->select();
        foreach ($oil_detail as $k => $v) {
            $oil[$v['id']] = 0;
        }
        print_r($oil);
        foreach ($info as $k => $v) {
            $item = unserialize($v['item']);
            foreach ($item['oil_detail'] as $id => $num) {
                foreach ($oil as $kk => $vv) {
                    if ($kk == $id) {
                        $oil[$kk] += $num;
                    }
                }
            }
        }
        print_r($oil);
        foreach ($oil as $a => $b) {
            $info = $o_model->where(array('id' => $a))->find();
            $oil1[] = array($info['name'] . ' ' . $info['norms'] . 'L' => $b);
        }
        print_r($oil1);
    }

    function get_jxc()
    {
        $r_model = D('reservation_order');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        $model_model = D('carmodel');
        $map['origin'] = 4;
        $map['remark'] = '代下单: 补机油';
        $info = $r_model->where($map)->select();
        echo "<table>";
        foreach ($info as $k => $v) {
            $item = unserialize($v['item']);
            foreach ($item['oil_detail'] as $id => $num) {
                if ($num > 0) {
                    $info = $o_model->where(array('id' => $id))->find();
                    echo '<tr><td>id:' . $v['id'] . '</td>&nbsp;&nbsp;&nbsp;&nbsp;<td>' . $v['truename'] . '</td>&nbsp;&nbsp;&nbsp;&nbsp;<td>' . $info['name'] . '</td>&nbsp;&nbsp;<td>' . $info['norms'] . 'L</td>&nbsp;&nbsp;&nbsp;&nbsp;<td>' . $num . '个</td><td>' . date('Y-m-d', $v['order_time']) . '</td><td>' . $v['amount'] . '</td></tr>';
                }
            }
        }
        echo "</table>";
    }

    //跑券码使用状态
    function run_coupon_use()
    {
        exit;
        $model_membercoupon = D('membercoupon');
        $map['membercoupon_id'] = array('in', '4255,4254,4253,4252,4251,4250,4249,4248,4247,4246,4245,4244,4243,4242,4241,4240,4239,4238,4237,4236,4235,4234,4233,4232,4231,4230,4229,4223,4222,4221,4220,4219,4218,4217,4216,211,4210,4209,4155,4154,4153,4152,4151,4150,4032,4031,3765,3766,3767,3768,3769,3770,3771,3772,3773,3764');
        $info = $model_membercoupon->where($map)->Distinct(true)->field('licenseplate')->select();
        //echo $model_membercoupon->getLastsql();'
        //print_r($info);
        //exit;
        $i = 0;
        foreach ($info as $k => $v) {
            $mad['membercoupon_id'] = array('in', '4255,4254,4253,4252,4251,4250,4249,4248,4247,4246,4245,4244,4243,4242,4241,4240,4239,4238,4237,4236,4235,4234,4233,4232,4231,4230,4229,4223,4222,4221,4220,4219,4218,4217,4216,211,4210,4209,4155,4154,4153,4152,4151,4150,4032,4031,3765,3766,3767,3768,3769,3770,3771,3772,3773,3764');
            $mad['licenseplate'] = $v['licenseplate'];
            $list = $model_membercoupon->where($mad)->find();
            $r = rand(9, 17);
            $use_time = mktime($r, date('i', $list['pay_time']), date('s', $list['pay_time']), date('m', $list['pay_time']), date('d', $list['pay_time']), date('Y', $list['pay_time']));
            //$data['is_use'] = 1;
            $data['use_time'] = $use_time;
            $res = $model_membercoupon->where($mad)->save($data);
            echo $model_membercoupon->getLastsql();
            echo '</br>';
            if ($res !== FALSE) {
                $i++;
            }
        }
        echo 'i=' . $i;
    }

    function run_check()
    {
        exit;
        $r_model = D('reservation_order');
        $c_model = D('checkreport_total');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $map['origin'] = 4;
        $map['id'] = array('in', '1377,2327,2331,2419,2542,2549,2551,2758,2775,2785,2848,4708,5070,5181,5215,6068,6218,6222,6232,6257,6445,6444,6310,6326,6330,6340,6344,6346,6368,6378,6383,6388,6389,6392,6394,6426,6477,6478,6497,6507,6518,6547,6551,6555,6556,6588,6590,7558,7557,6653,6654,6665,6699,6702,6757,6760,6968,6969,6978,6979,6990,6991,7034,7035,7056,7057,7147,7148,7296,7297,7410,7411,7439,7441,7503,7499,7849,7855,8053,8054,8055,8191,8192,9040,9042');
        $list = $r_model->where($map)->select();
        //echo $r_model->getLastsql();
        //exit;
        $i = 0;
        foreach ($list as $k => $v) {
            //找以前的订单，然后找到检查报告
            $info = $c_model->where(array('reservation_id' => '9264'))->find();
            //echo $r_model->getLastsql();echo '</br>';
            if ($info) {
                $data['reservation_id'] = $v['id'];
                $data['mobile'] = $v['mobile'];
                //$data['data'] = $info['data'];
                $data1 = unserialize($info['data']);
                $data['create_time'] = $v['order_time'] + 1800;
                $model = $model_model->where(array('model_id' => $v['model_id']))->find();
                $series = $series_model->where(array('series_id' => $model['series_id']))->find();
                $brand = $brand_model->where(array('brand_id' => $series['brand_id']))->find();
                $data1['car_brand'] = $brand['brand_name'];
                $data1['car_series'] = $series['series_name'];
                $data1['car_model'] = $model['model_name'];
                $data1['phone'] = $v['mobile'];
                $data1['date'] = date('Y-m-d', $v['order_time']);
                $data['data'] = serialize($data1);
                //print_r($data);
                $res = $c_model->where(array('reservation_id' => $v['id']))->save($data);
                echo $c_model->getLastsql();
                //exit;
                if ($res !== FALSE) {
                    $i++;
                }
            }
        }
        echo 'i=' . $i;
    }

    function run_step()
    {
        //exit;
        $r_model = D('reservation_order');
        $c_model = D('check_step');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $map['origin'] = 4;
        $list = $r_model->where($map)->select();
        //echo $r_model->getLastsql();
        //exit;
        $i = 0;
        foreach ($list as $k => $v) {
            $info = $c_model->where(array('order_id' => $v['id']))->select();
            //echo $c_model->getLastsql();
            //exit;
            $create_time = 0;
            foreach ($info as $kk => $vv) {
                if ($kk == 0) {
                    $addtime = rand(3, 55);
                } elseif ($kk == 1) {
                    $addtime = rand(1800, 2700);
                } elseif ($kk == 2) {
                    $addtime = rand(65, 298);
                } elseif ($kk == 3) {
                    $addtime = rand(1800, 2700);
                } elseif ($kk == 4) {
                    $addtime = rand(65, 298);
                } elseif ($kk == 5) {
                    $addtime = rand(65, 298);
                } elseif ($kk == 6) {
                    $addtime = rand(65, 298);
                }
                if ($kk == 0) {
                    $data['create_time'] = $create_time = $vv['create_time'] + $addtime;
                } else {
                    $data['create_time'] = $create_time + $addtime;
                    $create_time = $data['create_time'];
                }
                //echo $create_time.',';
                $id = $c_model->where(array('id' => $vv['id']))->save($data);
                //echo $c_model->getLastsql();
                if ($id) {
                    $i++;
                }
            }
            //exit;
        }
        echo 'i=' . $i;
    }

    function run_different()
    {
        $r_model = D('reservation_order');
        $c_model = D('checkreport_total');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $map['origin'] = 4;
        $list = $r_model->where($map)->select();
        //echo $r_model->getLastsql();
        //exit;
        $i = 0;
        foreach ($list as $k => $v) {
            $info = $c_model->where(array('reservation_id' => $v['id']))->find();
            $data1 = unserialize($info['data']);
            $mad['model_id'] = $v['model_id'];
            $model = $model_model->where($mad)->find();
            //echo $model_model->getLastsql();

            //exit;
            if ($model['model_name'] != $data1['car_model']) {
                $series = $series_model->where(array('series_id' => $model['series_id']))->find();
                $brand = $brand_model->where(array('brand_id' => $series['brand_id']))->find();
                $data1['car_brand'] = $brand['brand_name'];
                $data1['car_series'] = $series['series_name'];
                $data1['car_model'] = $model['model_name'];
                $data['data'] = serialize($data1);
                //print_r($data);exit;
                $res = $c_model->where(array('id' => $info['id']))->save($data);
            }
        }

    }

    function get_car_paiming()
    {
        $r_model = D('reservation_order');
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $sql = "SELECT model_id, count(*) FROM `xc_reservation_order` WHERE 1 AND STATUS !=8 GROUP BY model_id ORDER BY count( * ) DESC ";
        $info = $r_model->query($sql, 'all');
        //print_r($info);
        foreach ($info as $k => $v) {
            //echo 'model_id='.$v['model_id'];
            $model = $model_model->where(array('model_id' => $v['model_id']))->find();
            $series = $series_model->where(array('series_id' => $model['series_id']))->find();
            $brand = $brand_model->where(array('brand_id' => $series['brand_id']))->find();
            echo $brand['brand_name'] . ' ' . $series['series_name'] . ' ' . $model['model_name'] . ': ' . $v['count(*)'] . '</br>';
            if ($k == 11) {
                exit;
            }
        }
    }

    function get_membercoupon()
    {
        $m_model = D('membercoupon');
        $s_model = D('shop');
        $c_model = D('coupon');
        $map['membercoupon_id'] = array('egt','2281');
        //$map['use_time'] = array('between', array(strtotime('2014-10-1'), strtotime('2015-5-1')));
        //$map['coupon_amount'] = array('egt', '1');
        //$map['is_delete'] = 0;
        //$map['is_refund'] = 0;
        //$map['is_pay'] = 1;
        $list = $m_model->where($map)->select();
        echo $m_model->getLastsql();
        echo "<table><tr><th>订单号</th><th>券号</th><th>券名</th><th>原价</th><th>现价</th><th>4S店</th><th>支付时间</th><th>使用时间</th><th>使用状态</th><th>支付方式</th></tr>";
        foreach ($list as $k => $v) {
            if ($v['shop_id']) {
                $shop = $s_model->where(array('id' => $v['shop_id']))->find();
            } else {
                $shop = $s_model->where(array('id' => $v['shop_ids']))->find();
            }

            $coupon = $c_model->where(array('id' => $v['coupon_id']))->find();
            $v['shop_name'] = $shop['shop_name'];
            $v['coupon_amount1'] = $coupon['coupon_amount'];
            $v['cost_price'] = $coupon['cost_price'];
            if ($v['is_use'] == 1) {
                $v['is_use'] = '已使用';
            } else {
                $v['is_use'] = '未使用';
            }
            if ($v['coupon_type'] == 1) {
                $v['coupon_type'] = '现金券';
            } else {
                $v['coupon_type'] = '团购券';
            }
            if ($v['pay_type'] == 1) {
                $v['pay_type'] = '财付通';
            }
            if ($v['pay_type'] == 2) {
                $v['pay_type'] = '银联';
            }
            if ($v['pay_type'] == 3) {
                $v['pay_type'] = '支付宝';
            }
            if ($v['pay_type'] == 4) {
                $v['pay_type'] = '现金';
            }
            if ($v['pay_type'] == 5) {
                $v['pay_type'] = '微信支付';
            }
            echo "<tr><td>" . $v['membercoupon_id'] . "</td><td>" . $v['coupon_id'] . "</td><td>" . $v['coupon_name'] . "</td><td>" . $v['cost_price'] . "</td><td>" . $v['coupon_amount1'] . "</td><td>" . $v['shop_name'] . "</td><td>" . date('Y-m-d H:i:s', $v['pay_time']) . "</td><td>" . date('Y-m-d H:i:s', $v['use_time']) . "</td><td>" . $v['is_use'] . "</td><td>" . $v['pay_type'] . "</td><td>" . $v['coupon_type'] . "</td></tr>";
        }
    }

    //技师返配件日志数据
    function get_itemback()
    {
        $itemback = D('itemback_log');
        $t_model = D('technician');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        if ($_REQUEST['start_date']) {
            $start = strtotime($_REQUEST['start_date']);
        } else {
            $start = strtotime(date('Y-m-d'));
        }
        if ($_REQUEST['end_date']) {
            $end = strtotime($_REQUEST['end_date'] . ' 23:59:59');
        } else {
            $end = strtotime(date('Y-m-d')) + 86400;
        }

        $map['create_time'] = array(array('egt', $start), array('elt', $end));
        $list = $itemback->where($map)->select();
        //echo $itemback->getLastsql();
        echo "<table width='1000'><tr><th>订单号</th><th>发生时间</th><th>技师</th><th>机油</th><th>机滤</th><th>空气滤</th><th>空调滤</th></tr>";
        foreach ($list as $k => $v) {
            $t_info = $t_model->where(array('id' => $v['technician_id']))->find();
            $v['name'] = $t_info['truename'];
            $item = unserialize($v['item']);
            //print_r($item);
            foreach ($item['oil_detail'] as $id => $num) {
                if ($num > 0) {
                    $o_info = $o_model->where(array('id' => $id))->find();
                    $v['oil'] .= $o_info['name'] . ' ' . $o_info['norms'] . 'L ' . $num . '个</br>';
                }
            }
            if ($item['filter_id'] > 0) {
                $f_info = $f_model->where(array('id' => $item['filter_id']))->find();
                $v['filter'] = $f_info['name'];
            }
            if ($item['kongqi_id'] > 0) {
                $f_info = $f_model->where(array('id' => $item['kongqi_id']))->find();
                $v['kongqi'] = $f_info['name'];
            }
            if ($item['kongtiao_id'] > 0) {
                $f_info = $f_model->where(array('id' => $item['kongtiao_id']))->find();
                $v['kongtiao'] = $f_info['name'];
            }

            //$t_info = $t_model->where(array('id'=>$v['technician_id']))->find();
            echo "<tr><td>" . $v['order_id'] . "</td><td>" . date('Y-m-d H:i:s', $v['create_time']) . "</td><td>" . $v['name'] . "</td><td>" . $v['oil'] . "</td><td>" . $v['filter'] . "</td><td>" . $v['kongqi'] . "</td><td>" . $v['kongtiao'] . "</td></tr>";
        }
        echo "</table>";
    }

    //获取当天视频数据
    function get_viewdata()
    {
        if ($_REQUEST['pass'] != 'fqcd1213') {
            exit;
        }
        $r_model = D('reservation_order');
        $technician_model = M('tp_xieche.technician', 'xc_');  //技师表

        if ($_REQUEST['start_date']) {
            $start = strtotime($_REQUEST['start_date']);
        } else {
            $start = strtotime(date('Y-m-d'));
        }
        if ($_REQUEST['end_date']) {
            $end = strtotime($_REQUEST['end_date'] . ' 23:59:59');
        } else {
            $end = strtotime(date('Y-m-d')) + 86400;
        }

        $map['order_time'] = array(array('egt', $start), array('elt', $end));
        $map['status'] = 9;
        $list = $r_model->where($map)->select();
        //echo $r_model->getLastsql();
        foreach ($list as $k => $v) {
            $order_ids .= $v['id'] . ',';
        }
        $order_ids = substr($order_ids, 0, -1);
        //获取播放链接
        $url = "http://s.2xq.com:9615/get_video_by_orders?order_ids=" . $order_ids;
        $strm = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 15)));
        $result = file_get_contents($url, false, $strm);
        $result = json_decode($result, true);
        echo "<table><tr><th>订单号</th><th>拍摄情况</th></tr>";
        foreach ($result as $kk => $vv) {
            $o_info = $r_model->where(array('id' => $kk))->find();
            $t_info = $technician_model->where(array('id' => $o_info['technician_id']))->find();
            if ($vv == true) {
                echo "<tr><td>" . $kk . "</td><td>√</td><td>" . $t_info['truename'] . "</td><td>" . date('Y-m-d H:i:s', $o_info['order_time']) . "</td></tr>";
            } else {
                echo "<tr><td>" . $kk . "</td><td>x</td><td>" . $t_info['truename'] . "</td><td>" . date('Y-m-d H:i:s', $o_info['order_time']) . "</td></tr>";
            }
        }
    }

    //获取待结算团购券
    function get_membercouponjiesuan()
    {
        $m_model = D('membercoupon');
        $sql = "SELECT * FROM `xc_membercoupon` LEFT JOIN xc_coupon on xc_coupon.id=xc_membercoupon.coupon_id WHERE ( xc_coupon.coupon_across = 0 ) AND ( xc_membercoupon.is_delete = 0 ) AND ( xc_membercoupon.is_jiesuan = 0 ) AND ( xc_membercoupon.is_use = 1 ) AND ( xc_membercoupon.use_time >= 1425139200 ) AND ( xc_membercoupon.use_time <= 1427817600 ) AND ( xc_membercoupon.uid!=1 ) ORDER BY membercoupon_id DESC";
        $list = $m_model->query($sql);
        print_r($list);
        echo "<table><tr><th>订单号</th><th>券名</th><th>原价</th><th>现价</th><th>类别</th><th>使用时间</th></tr>";
        foreach ($list as $k => $v) {
            if ($v['coupon_type'] == 1) {
                $v['coupon_type'] = '现金券';
            } else {
                $v['coupon_type'] = '团购券';
            }
            echo "<tr><td>" . $v['membercoupon_id'] . "</td><td>" . $v['coupon_name'] . "</td><td>" . $v['cost_price'] . "</td><td>" . $v['coupon_amount'] . "</td><td>" . $v['coupon_type'] . "</td><td>" . date('Y-m-d H:i:s', $v['use_time']) . "</td></tr>";
        }
        echo "</table>";
    }

    //百度逆向地址接口
    function get_address()
    {
        $host = "http://api.map.baidu.com/geocoder/v2/?ak=6af4266c4310701eb7d7ae0564209287&callback=renderReverse&location=31.213726744022,121.49291855932&output=json&pois=0";
        $array = array(
            "location" => "39.983424,116.322987",
            "output" => "xml",
            "pois" => "1",
        );
        $json_body = json_encode($array);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
        $output = curl_exec($ch);
        var_dump($output);
        curl_close($ch);
        $result = json_decode($output, true);
        print_r($result);
    }

    //获取套餐卡使用数据
    function get_carservice()
    {
        $brand_model = M('tp_xieche.carbrand', 'xc_');  //车品牌
        $series_model = M('tp_xieche.carseries', 'xc_');  //车系号
        $model_model = M('tp_xieche.carmodel', 'xc_');  //车型号
        $this->CarservicecodeModel = D('carservicecode');
        $map['pay_type'] = array('neq', '4');
        $map['status'] = 9;
        $map['order_type'] = array('not in', '2,13,14,15,16,17,18,19,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,50,51,53');
        $map['business_source'] = array('not in', '5,27,30');
        $list = $this->reservation_order_model->where($map)->select();
        echo $this->reservation_order_model->getLastsql();
        $i = 0;
        echo "<table><tr><th>订单号</th><th>优惠券</th><th>渠道</th><th>车型</th><th>用户名</th><th>车牌号</th><th>手机号</th><th>上门时间</th></tr>";
        foreach ($list as $k => $v) {
            $a = array('7', '8', '9');
            if ($v['replace_code'] == '') {
                continue;
            }
            $info = $this->CarservicecodeModel->where(array('coupon_code' => $v['replace_code']))->find();
            if ($info['remark'] == '三星') {
                continue;
            }
            $model = $model_model->where(array('model_id' => $v['model_id']))->find();
            $series = $series_model->where(array('series_id' => $model['series_id']))->find();
            $brand = $brand_model->where(array('brand_id' => $series['brand_id']))->find();
            $pattern1 = "/(1\d{1,2})\d\d(\d{0,3})/";
            $replacement1 = "\$1*****\$3";
            $v['mobile'] = preg_replace($pattern1, $replacement1, $v['mobile']);

            echo "<tr><td>" . $v['id'] . "</td><td>" . $v['replace_code'] . "</td><td>" . $info['remark'] . "</td><td>" . $brand['brand_name'] . $series['series_name'] . $model['model_name'] . "</td><td>" . $v['truename'] . "</td><td>" . $v['licenseplate'] . "</td><td>" . $v['mobile'] . "</td><td>" . date('Y-m-d H:i:s', $v['order_time']) . "</td></tr>";
            $i++;
        }
        echo $i;
    }

    //删除冗余的步骤数据
    function del_uselessstep()
    {
        exit();
        $model = D('check_step');
        $sql = "SELECT DISTINCT (a.order_id) FROM `xc_check_step` a, `xc_check_step` b WHERE a.order_id =b.order_id AND a.step_id = b.step_id AND a.id != b.id";
        $list = $model->query($sql);
        foreach ($list as $k => $v) {
            $info = $model->where(array('order_id' => $v['order_id']))->select();
            $array = array();
            foreach ($info as $kk => $vv) {
                if (!in_array($vv['step_id'], $array)) {
                    $array[] = $vv['step_id'];
                } else {
                    echo $vv['id'] . "</br>";
                    //$model->where(array('id'=>$vv['id']))->delete();
                }
            }
            //exit;
        }
    }

    function upload_file()
    {
        echo "
			<form action=\"doupload/\" method=\"post\" enctype=\"multipart/form-data\">	
			<table>
				<tr class=\"row\">
					<td style=\"text-align:left;\">
						<input type='file'  name='doc1'>
					</td>
				</tr>
				<tr>
					<td align=\"center\" ></td>
					<td  style=\"text-align:left;\">
						<input type=\"submit\" id=\"btn_ok\" name=\"btn_ok\" value=\"上传\">
					</td>
				</tr>
			</table>
			</form>
		";
    }

    function doupload()
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        var_dump($upload);
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'xlsx');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Driving/';
        $upload->saveRule = 'uniqid';
        $upload->saveName = '';
        // 上传文件 
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
           $this->error($upload->getError());
       } else {// 上传成功
            echo "上传成功";
           //$this->success('上传成功！');
       }

        $file = fopen(WEB_ROOT.'UPLOADS/Driving/meiya.xlsx','r');
        while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
            print_r($data); exit;//此为一个数组，要获得每一个数据，访问数组下标即可
            //事故车时间，姓名，电话，定损点来源，来源方式，分配客服必填
            //print_r($data);exit;
//            if(!$data['0'] or !$data['1'] or !$data['4'] or !$data['6'] or !$data['7'] or !$data['8'] ){
//                echo '数据不全无法生成';
//            }else{
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
//            }
        }
        fclose($file);
    }

    function upload_item()
    {
        $carmodel = D('carmodel');
        $list = $carmodel->select();
        //print_r($list);
        foreach ($list as $k => $v) {
            print_r(unserialize($v['item_set']));
            exit;
        }
    }
    
    
    //单量，费用，引流等数据
    function get_dataPrivateorderforzzc_bak()
    {
        $r_model = D('reservation_order');
        echo "<style type='text/css'>
		table{
			border: 1px solid gray;
			border-collapse: collapse;
			float: left;
			margin: 3px 0;
			padding: 8px;
			text-align: left;
			width: 100%;
			border-bottom: 1px solid silver;
			border-left: 1px solid silver;
		}
		</style>
		<table border='1'><tr><th></th><th>总订单量</th><th>保养订单量</th><th>保养订单人工费总计</th><th>保养订单零件费总计</th><th>保养订单占比</th><th>保养订单每单人工费</th><th>保养订单人工费折扣率</th><th>保养订单每单零件费</th><th>保养订单配件自带率</th><th>引流订单量</th><th>引流订单占比</th><th>引流订单人工费总计</th><th>引流订单零件费总计</th></tr>";
        for ($n = 9; $n < 21; $n++) {
            
            $year = substr(201401, 0, 4);
            $month = substr(201401, 4, 2);
            $S = date('Ymd', mktime(0, 0, 0, $month + $n - 1, 1, $year));
            $E = date('Ymd', mktime(0, 0, 0, $month + $n, 1, $year));
            //echo 's='.$S.';e='.$E.'</br>';
            $month = substr($S, 4, 2);
            echo '<tr><td>' . $month . '月</td>';
            $start = strtotime($S);
            $end = strtotime($E);
            $map['order_time'] = array(array('egt', $start), array('elt', $end));
            $map['status'] = 9;
            $map['city_id'] =  4  ; 
            $list = $r_model->where($map)->select();
            echo $r_model->getLastsql();
            echo '<br>' ;
            
            $by = 0;
            $yl = 0;
            $zdpj = 0;
            $peijian = 0;
            $rengong = 0;
            $yl_amount = 0;
            $by_amount = 0;
            $yl_rengong = 0;
            $by_rengong = 0;
            foreach ($list as $k => $v) {
                $item = unserialize($v['item']);
                $oil = 0;
                $filter = 0;
                $kongqi = 0;
                $kongtiao = 0;
                foreach ($item['price']['oil'] as $a => $b) {
                    $oil = $oil + $b;
                }
                foreach ($item['price']['filter'] as $aa => $bb) {
                    $filter = $bb;
                }
                foreach ($item['price']['kongqi'] as $aaa => $bbb) {
                    $kongqi = $bbb;
                }
                foreach ($item['price']['kongtiao'] as $_a => $_b) {
                    $kongtiao = $_b;
                }
                //print_r($item);
                $peijian = $oil + $filter + $kongqi + $kongtiao;
                $rengong = $v['amount'] - $peijian;

                $array = array('7', '8', '9', '10', '34');
                if ($v['amount'] < 100 and !in_array($v['order_type'], $array)) {
                    $yl++;
                    $yl_amount = $yl_amount + $peijian;
                    $yl_rengong = $yl_rengong + $rengong;
                } else {
                    //if($n==17){ echo 'order_id='.$v['id'].',order_type='.$v['order_type'].',amount='.$v['amount'].',rengong='.$rengong.'</br>'; }
                    if ($item['oil_id'] == -1 or $item['filter_id'] == -1 or $item['kongqi_id'] == -1 or $item['kongtiao_id'] == -1) {
                        $zdpj++;
                    }
                    $by++;
                    $by_amount = $by_amount + $peijian;
                    $by_rengong = $by_rengong + $rengong;
                }
            }

            // 计算价格
            $priceInfo = array();
            //计算总价
            $countPrice = $r_model->where($map)->sum('amount');
            if ($countPrice) {
                $countPriceCount = $r_model->where($map)->count();
                $priceInfo[] = array(
                    'name' => '全部' . $countPriceCount . '个订单, 总价:',
                    'value' => $countPrice
                );
            }
            //计算其他订单价钱，给财务看的
            $where2 = $map;
            for ($i = 1; $i <= 35; $i++) {
                $name = $this->_carserviceConf($i);
                if ($name) {
                    $where2['order_type'] = array('eq', $i);
                    $baoyangPrice = $r_model->where($where2)->sum('amount');//上门保养订单价钱
                    if ($baoyangPrice) {
                        $baoyangPriceCount = $r_model->where($where2)->count();
                        $priceInfo[] = array(
                            'name' => $name . $baoyangPriceCount . '个订单, 总价:',
                            'value' => $baoyangPrice
                        );
                    }
                }

            }

            echo '<td>' . $countPriceCount . '</td><td>' . $by . '</td><td>' . round($by_rengong, 2) . '</td><td>' . $by_amount . '</td><td>' . round(($by / $countPriceCount) * 100, 2) . '%' . '</td><td>' . round($by_rengong / $by, 2) . '</td><td>' . round((round($by_rengong / $by, 2) / 99) * 100, 2) . '%' . '</td><td>' . round($by_amount / $by, 2) . '</td><td>' . round(($zdpj / $by) * 100, 2) . '%' . '</td><td>' . $yl . '</td><td>' . round(($yl / $countPriceCount) * 100, 2) . '%' . '</td><td>' . $yl_rengong . '</td><td>' . $yl_amount . '</td>';
            /*echo "<td>";
			foreach($priceInfo as $kk=>$vv){
				echo $vv['name'].'<font color=red>'.$vv['value'].'</font>';
			}
			echo "</td>";*/
        }
        echo "</tr></table>";
    }

    

    //单量，费用，引流等数据
    function get_dataPrivateorderforzzc()
    {
        //http验证设置
        if(!isset($_SERVER['PHP_AUTH_USER'])) 
        { 
            Header("WWW-Authenticate: Basic realm='My Realm'"); 
            Header("HTTP/1.0 401 Unauthorized"); 
            echo "Text to send if user hits Cancel buttonn"; 
            exit; 
        }else if ( !($_SERVER['PHP_AUTH_USER']=="admin" && $_SERVER['PHP_AUTH_PW']=="fqcd123223") ){ 
            // 如果是错误的用户名称/密码对，强制再验证 
            Header("WWW-Authenticate: Basic realm='My Realm'"); 
            Header("HTTP/1.0 401 Unauthorized"); 
            echo "ERROR : ".$_SERVER['PHP_AUTH_USER']/$_SERVER['PHP_AUTH_PW']." is invalid."; 
            exit; 
        }
        
        //开始获取和处理订单数据 。。
        $data_order_model = D('dataprivateorder');

        $cityArray = array(
          'data_total'=>'0',
          'data_sh'=>'1' ,   //上海
          'data_hz'=>'2' ,   //杭州
          'data_sz'=>'3' ,   //苏州
          'data_cd'=>'4' ,   //成都
          'data_jn'=>'5' ,   //济南
          'data_fz'=>'6' ,   //福州 
        );

        $cityName = array(
          '0'=>'合计' ,
          '1'=>'上海' ,
          '2'=>'杭州' ,
          '3'=>'苏州' ,
          '4'=>'成都' ,
          '5'=>'济南' ,
          '6'=>'福州' 
        );


      //获取当前月份 ，查出当前月份数据 。
      //如果没有 ，插入 ，如果有 ，更新。
       $firstday = date('Y-m-01 00:00:00', time());
       $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day")); 
       $start = strtotime($firstday);           
       $end = strtotime($lastday);

        $map = array();
        $map['order_time'] = array(array('egt', $start), array('elt', $end));
        $map['status'] = 9;

        //城市循环
        foreach ($cityArray as $key => $value) {
            $year_and_month =  date('Y年m月', time());
            $city = $cityName[$value] ;
            if($key == 'data_total'){
               $data[$key] = $this->get_order_data($map,$year_and_month,$city);
            }else{
               $map['city_id'] = $value ;
               $data[$key] = $this->get_order_data($map,$year_and_month,$city);
            }

        }

       //print_r($data);exit ;

        foreach($data as $k => $v){
            //首先判断这一条是否存在，不存在插入，存在更新。
            $con = array();
            $con['year_and_month'] = $v['year_and_month'] ;
            $con['city_name'] = $v['city_name'] ;
            $results = $data_order_model->where($con)->find();
            if(is_array($results)){ //更新数据                
               $save_data = array();
               $save_data['year_and_month'] = $v['year_and_month'] ;
               $save_data['city_name'] = $v['city_name'] ;
               $save_data['countPriceCount'] = $v['countPriceCount'] ;
               $save_data['by_nums'] = $v['by_nums'] ;
               $save_data['by_rengong'] = $v['by_rengong'] ;
               $save_data['by_amount'] = $v['by_amount'] ;
               $save_data['by_percent'] = $v['by_percent'] ;
               $save_data['by_rengong_perorder'] = $v['by_rengong_perorder'] ;
               $save_data['by_rengong_discount'] = $v['by_rengong_discount'] ;
               $save_data['by_peijian'] = $v['by_peijian'] ;
               $save_data['zdpj_percent'] = $v['zdpj_percent'] ;
               $save_data['yl'] = $v['yl'] ;
               $save_data['yl_percent'] = $v['yl_percent'] ;
               $save_data['yl_rengong'] = $v['yl_rengong'] ;
               $save_data['yl_amount'] = $v['yl_amount'] ;
               $data_order_model->where('id='.$results['id'])->save($save_data);  
            }else{  //插入数据
                $add_data = array();
                $add_data['year_and_month'] = $v['year_and_month'] ;
                $add_data['city_name'] = $v['city_name'] ;
                $add_data['countPriceCount'] = $v['countPriceCount'] ;
                $add_data['by_nums'] = $v['by_nums'] ;
                $add_data['by_rengong'] = $v['by_rengong'] ;
                $add_data['by_amount'] = $v['by_amount'] ;
                $add_data['by_percent'] = $v['by_percent'] ;
                $add_data['by_rengong_perorder'] = $v['by_rengong_perorder'] ;
                $add_data['by_rengong_discount'] = $v['by_rengong_discount'] ;
                $add_data['by_peijian'] = $v['by_peijian'] ;
                $add_data['zdpj_percent'] = $v['zdpj_percent'] ;
                $add_data['yl'] = $v['yl'] ;
                $add_data['yl_percent'] = $v['yl_percent'] ;
                $add_data['yl_rengong'] = $v['yl_rengong'] ;
                $add_data['yl_amount'] = $v['yl_amount'] ;
                $data_order_model->add($add_data); 
                //echo  $data_order_model->getLastSql();
                //echo '<br>' ;
            }    
        };



        $data_order = $data_order_model->select() ;
        //print_r($data_order);
        $this->assign('data_order',$data_order);
        $this->display('get_dataPrivateorderforzzc');
   

 
    }
    
    //获取符合条件的订单数据
    public function get_order_data($map,$year_and_month,$city) {
        $r_model = D('reservation_order');
        
        
        $list = $r_model->where($map)->select();
       // echo $r_model->getLastsql();
       // echo  '<br>' ;
        
        $by = 0;
        $yl = 0;
        $zdpj = 0;
        $peijian = 0;
        $rengong = 0;
        $yl_amount = 0;
        $by_amount = 0;
        $yl_rengong = 0;
        $by_rengong = 0;
        foreach ($list as $k => $v) {
            $item = unserialize($v['item']);
            $oil = 0;
            $filter = 0;
            $kongqi = 0;
            $kongtiao = 0;
            foreach ($item['price']['oil'] as $a => $b) {
                $oil = $oil + $b;
            }
            foreach ($item['price']['filter'] as $aa => $bb) {
                $filter = $bb;
            }
            foreach ($item['price']['kongqi'] as $aaa => $bbb) {
                $kongqi = $bbb;
            }
            foreach ($item['price']['kongtiao'] as $_a => $_b) {
                $kongtiao = $_b;
            }
            //print_r($item);
            $peijian = $oil + $filter + $kongqi + $kongtiao;
            $rengong = $v['amount'] - $peijian;

            $array = array('7', '8', '9', '10', '34');
            if ($v['amount'] < 100 and !in_array($v['order_type'], $array)) {
                $yl++;
                $yl_amount = $yl_amount + $peijian;
                $yl_rengong = $yl_rengong + $rengong;
            } else {
                //if($n==17){ echo 'order_id='.$v['id'].',order_type='.$v['order_type'].',amount='.$v['amount'].',rengong='.$rengong.'</br>'; }
                if ($item['oil_id'] == -1 or $item['filter_id'] == -1 or $item['kongqi_id'] == -1 or $item['kongtiao_id'] == -1) {
                    $zdpj++;
                }
                $by++;
                $by_amount = $by_amount + $peijian;
                $by_rengong = $by_rengong + $rengong;
            }
        }

        // 计算价格
        $priceInfo = array();
        //计算总价
        $countPrice = $r_model->where($map)->sum('amount');
        if ($countPrice) {
            $countPriceCount = $r_model->where($map)->count();
            $priceInfo[] = array(
                'name' => '全部' . $countPriceCount . '个订单, 总价:',
                'value' => $countPrice
            );
        }
        //计算其他订单价钱，给财务看的
        $where2 = $map;
        for ($i = 1; $i <= 35; $i++) {
            $name = $this->_carserviceConf($i);
            if ($name) {
                $where2['order_type'] = array('eq', $i);
                $baoyangPrice = $r_model->where($where2)->sum('amount');//上门保养订单价钱
                if ($baoyangPrice) {
                    $baoyangPriceCount = $r_model->where($where2)->count();
                    $priceInfo[] = array(
                        'name' => $name . $baoyangPriceCount . '个订单, 总价:',
                        'value' => $baoyangPrice
                    );
                }
            }

        }
        
        $ret = array();
        
        $ret['year_and_month'] =  $year_and_month ;    //保养月份
        $ret['city_name'] =  $city ;    //保养城市
        
        $ret['countPriceCount'] =  isset($countPriceCount)?$countPriceCount:0 ;  //总订单量
        $ret['by_nums'] =  $by ;    //保养订单量
        $ret['by_rengong'] =  round($by_rengong, 2) ;  //保养订单人工费总计
        $ret['by_amount'] =  $by_amount ;   //保养订单零件费总计
        $ret['by_percent'] =  round(($by / $countPriceCount) * 100, 2) ; //保养订单占比
        $ret['by_rengong_perorder'] =  round($by_rengong / $by, 2) ;   //保养订单每单人工费
        $ret['by_rengong_discount'] =  round((round($by_rengong / $by, 2) / 99) * 100, 2) ;  //保养订单人工费折扣率
        $ret['by_peijian']  =  round($by_amount / $by, 2) ;    //保养订单每单零件费
        $ret['zdpj_percent'] =   round(($zdpj / $by) * 100, 2) ;  //保养订单配件自带率
        $ret['yl'] =  $yl ;   //引流订单量
        $ret['yl_percent'] =  round(($yl / $countPriceCount) * 100, 2) ; //引流订单占比
        $ret['yl_rengong'] =  $yl_rengong ;   //引流订单人工费总计
        $ret['yl_amount'] =  $yl_amount ;  //引流订单零件费总计
        
        return  $ret  ;
    }

    
    
    
    
    
    private function _carserviceConf($type)
    {
        switch ($type) {
            case 1:
                $name = '无套餐保养订单';
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
                $name = '光大168';
                break;
            case 23:
                $name = '光大268';
                break;
            case 24:
                $name = '光大368';
                break;
            case 25:
                $name = '浦发199';
                break;
            case 26:
                $name = '浦发299';
                break;
            case 27:
                $name = '浦发399';
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
        }
        return $name;
    }

    function run_didicode()
    {
        exit;
        $a = array('223D8QCV',
            '223K3UYR',
            '22268636',
            '2227JUC2',
            '223YUY8E',
            '223V77NM',
            '223R6SY7',
            '223H2JLU',
            '22473L5J',
            '223T7WRS',
            '223JVHFU',
            '2248SYDN',
            '2246MSPB',
            '2244SMAR',
            '223LCQPG',
            '223GUHT6',
            '223WN4TW',
            '225BBAAQ',
            '523P446V',
            '5242ZPYK',
            '5246ZAWN',
            '523LV4CJ',
            '523K7H7X',
            '523WRSSY',
            '523X5LKM',
            '525C3WMM',
            '5244SKRA',
            '523WPDX2',
            '5227UTDG',
            '523JHZSF',
            '5225N4YX',
            '523V8EGF',
            '525FBVF7',
            '523UF5B7',
            '5246A8J2',
            '525DCH4Z',
            '5223SXRM',
            '523EDGRJ',
            '823AHKNZ',
            '825DD2LP',
            '825FN4K7',
            '823EG422',
            '8248CRTC',
            '823K5Y23',
            '82265Z85',
            '823QACNA',
            '823K3EU6',
            '823LP7PJ',
            '8244CJWN',
            '823UUCNH',
            '823SRRWN',
            '823CGWGY',
            '823WHYFA',
            '823LYSVV',
            '82458LSR',
            '8223R2VY',
            '823SJ7CD',
            '825G3RGF',
            'B227NGPY',
            'B23EZQEV',
            'B25DUQB7',
            'B246HP8J',
            'B2286YG4',
            'B23YCBY2',
            'B23WJ2DJ',
            'B2458GWX',
            'B23SZL7K',
            'B225HYDY',
            'B25DD4HP',
            'B23K5RL5',
            'B25DJPMU',
            'B23SVA8V',
            'B25BRW28',
            'B225Q3TG',
            'B2223US7',
            'B23SPSQ2',
            'B227YJ7H',
            'B23CGLK7',
            'E25FZQF4',
            'E25C3EEX',
            'E23LSKCN',
            'E242ZVUM',
            'E227GTZK',
            'E23Z57JD',
            'E23QK788',
            'E23SXWXB',
            'E2456PC6',
            'E2242MMQ',
            'E25BY6A6',
            'E23F3XNC',
            'E2242PDA',
            'E244UWRU',
            'E25A57M8',
            'E23Z4KSM',
            'E23YEWUU',
            'E23H8FE3',
            'E246HYGY',
            'E2458NLH',
            'H23CQYD7',
            'H25BY385',
            'H24384RQ',
            'H23ADXHF',
            'H23NKA33',
            'H23QSGZK',
            'H25A74LE',
            'H25C3WC7',
            'H23T3ULA',
            'H2267W4G',
            'H23P3RU2',
            'H23LAMXZ',
            'H23K5E4J',
            'H23Z6P8G',
            'H23LNZVY',
            'H246HUDF',
            'H25FBDR2',
            'H25C7EFP',
            'H23T5DPJ',
            'H225DSYP',
            'L23SFWF7',
            'L244LNEP',
            'L23V6G4W',
            'L23YZ3SE',
            'L25BPM46',
            'L23R6ERK',
            'L223SBRD',
            'L25BBNPF',
            'L23D4GC5',
            'L246DX2W',
            'L225HQCQ',
            'L25C5BB3',
            'L23QACV7',
            'L23R2AAF',
            'L23H8DFQ',
            'L223YUJ2',
            'L246FVBB',
            'L23K86CR',
            'L23APGW5',
            'L23CGJQQ',
            'P25DSTX7',
            'P25BZBZ3',
            'P23QJZ56',
            'P25BZTS2',
            'P23APUSH',
            'P23ARDUL',
            'P23K5TQY',
            'P246N4TN',
            'P23CLD6G',
            'P225RCLH',
            'P23SFLHJ',
            'P23SW5EC',
            'P23JU2GV',
            'P225DCRN',
            'P223AXLW',
            'P23EU3GT',
            'P23F5SXB',
            'P23CD6YV',
            'P23V8JVE',
            'P2242PHL',
            'S23QV5SM',
            'S23GAAMV',
            'S25DNPUL',
            'S23ADFWH',
            'S23UM4SZ',
            'S23NN35J',
            'S23JN82E',
            'S23V2MLR',
            'S23JHFJR',
            'S246XPEK',
            'S23NXXJP',
            'S25DNMPG',
            'S23NZAVE',
            'S23LUD5L',
            'S223P85C',
            'S23YT2B5',
            'S25BHVUK',
            'S23JZC7V',
            'S23UERQ3',
            'S225XHUS',
            'W25FBDQJ',
            'W242KZ22',
            'W248SLPG',
            'W25FRUNT',
            'W25BFZ25',
            'W225C657',
            'W25DWFHK',
            'W225BN63',
            'W246DF2J',
            'W23YUJ6D',
            'W25C7P7A',
            'W23YYKYC',
            'W23EVSVP',
            'W25DGCYS',
            'W244QGZV',
            'W23QCL3R',
            'W23SZG3G',
            'W246U5R8',
            'W248EGPS',
            'W23F5QD3',
            'W23SPF55',
        );
        $model = M('tp_didi.code', 'didi_');
        //var_dump($model);
        foreach ($a as $k => $v) {
            echo $v . '</br>';
            $data['dcode'] = $v;
            $data['yxq'] = '05/26';
            $data['orderid'] = 0;
            $data['ordermobile'] = 0;
            $data['created'] = '0000-00-00 00:00:00';
            $model->add($data);
        }

    }

    //抓取平安，人保，中银，联想，商飞 4,5月数据
    function get_dataforjj()
    {
        $order_model = D('reservation_order');
        $map['id'] = array('in', '21869,21888,21826,21868,21895,21896,21833,21901');
        $list = $order_model->where($map)->select();
        echo $order_model->getLastsql();
        echo "<table><tr><th>ID</th><th>用户名</th><th>电话</th><th>上门时间</th></tr>";
        $pa = 0;
        foreach ($list as $k => $v) {
            echo "<tr><td>" . $v['id'] . "</td><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . date('Y-m-d H:i:s', $v['order_time']) . "</td></tr>";
        }
        echo "</table>";
    }


    //获取一个月的订单数据
    function getOrderData()
    {
        //echo '11111' ;
        $order_model = D('reservation_order');
        $start = strtotime('2015-8-1');
        $end = strtotime('2015-9-1');
        $map['create_time'] = array(array('egt', $start), array('elt', $end));
        $map['status'] = array('neq', '8');
        $list = $order_model->where($map)->select();
        echo "<table><tr><th>订单号</th><th>下单时间</th><th>预约时间</th><th>金额</th><th>用户备注</th><th>客服备注</th></tr>";
        foreach ($list as $k => $v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['order_time'] = date('Y-m-d H:i:s', $v['order_time']);
            echo "<th>" . $v['id'] . "</th><th>" . $v['create_time'] . "</th><th>" . $v['order_time'] . "</th><th>" . $v['amount'] . "</th><th>" . $v['remark'] . "</th><th>" . $v['operator_remark'] . "</th> </tr>";
        }
        echo "</table>";

    }

    function test_array()
    {

        $a = '[{"file":"order_18942_1433293433180_MV2TQ1RHU5.mp4","size":47675316,"ctime":1433295914896,"mtime":1433295914896,"live_url":"http://s.2xq.com:1935/live/order_18942_1433293433180_MV2TQ1RHU5/playlist.m3u8","vod_url":"http://s.2xq.com:1935/vod/mp4:order_18942_1433293433180_MV2TQ1RHU5.mp4/playlist.m3u8"},{"file":"order_18942_1433293433180_MV2TQ1RHU5_0.mp4","size":805362,"ctime":1433293555186,"mtime":1433293492476,"live_url":"http://s.2xq.com:1935/live/order_18942_1433293433180_MV2TQ1RHU5_0/playlist.m3u8","vod_url":"http://s.2xq.com:1935/vod/mp4:order_18942_1433293433180_MV2TQ1RHU5_0.mp4/playlist.m3u8"}]';
        $result = json_decode($a, true);
        echo count($result);
        $get_max = array();
        if (count($result) > 0) {
            foreach ($result as $k => $v) {
                $get_max[] = $v['size'];
            }
        }
        $max = max($get_max);
        echo $max . '</br>';
        foreach ($result as $k => $v) {
            if ($v['size'] == $max) {
                $live_url = $v['live_url'];
                $vod_url = $v['vod_rul'];
            }
        }
        echo $live_url . '</br>' . $vod_url;
    }

    //给金晶的数据
    function dataforjj()
    {
        $model = D('dataforjj');
        $r_model = D('reservation_order');
        $c_model = D('check_report');
        $list = $model->where(array('vin' => ''))->select();
        echo $model->getLastsql();
        //exit;
        foreach ($list as $k => $v) {
            $map['mobile'] = $v['mobile'];
            //$map['licenseplate'] = $v['chepai'];
            $info = $r_model->where($map)->select();
            $c_info = array();
            foreach ($info as $kk => $vv) {
                if (!$c_info) {
                    $c_info = $c_model->where(array('reservation_id' => $vv['id']))->find();
                }
            }
            echo $c_model->getLastsql();
            //print_r($c_info);
            $data['vin'] = $c_info['a11'];
            $data['licheng'] = $c_info['a17'];
            $data['chuchangshijian'] = $c_info['a45'];
            print_r($data);
            $model->where(array('id' => $v['id']))->save($data);
            echo $model->getLastsql();
            //exit;
        }
    }

    //获取未下单人员名单
    function get_noorder()
    {
        $s_model = D('api_record');
        $r_model = D('reservation_order');
        $o_model = D('order');
        $m_model = D('member');
        $b_model = D('bidorder');
        //$s_map['create_time'] = array('egt','1429200000');
        $list = $s_model->where()->field('mobile')->Distinct(true)->select();

        foreach ($list as $k => $v) {
            $s_list[] = $v['mobile'];
        }

        $list = $m_model->where()->field('mobile')->Distinct(true)->select();

        foreach ($list as $k => $v) {
            if (strlen($v['mobile']) != 11) {
                continue;
            } else {
                $m_list[] = $v['mobile'];
            }
        }
        //print_r($m_list);
        $list = array_merge($s_list, $m_list);
        //print_r($list);
        foreach ($list as $k => $v) {
            $map['mobile'] = $v;
            $map['status'] = array('neq', '8');
            $map['create_time'] = array('egt', '1429200000');
            $count = $r_model->where($map)->count();
            //echo $r_model->getLastsql();
            if ($count > 0) {
                unset($list[$k]);
            }
        }
        $list = array_unique($list);
        echo "<table>";
        foreach ($list as $k => $v) {
            if ($k > 73541) {
                echo "<tr><td>" . $k . "</td><td>" . $v . "</td></tr>";
            }
        }
        echo "</table>";
    }

    //签名计算并返回参数字符串  wql@20150701
    public function combinedSign($data)
    {
        //拼接签名字符串
        $sign = '';
        //点评提供的密钥
        $appKey = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456';
        //对参数数组升序排序
        ksort($data);
        //循环拼接
        foreach ($data as $k => $v) {
            $sign .= $k . $data[$k];
        }
        //拼接appkey
        $sign .= $appKey;
        //md5加密转为大写
        $sign = strtoupper(md5($sign));
        $data['sign'] = $sign;

        //返回参数字符串
        $ret = '';
        foreach ($data as $k => $v) {
            $ret .= $k . '=' . $v . '&';
        }
        $ret = substr($ret, 0, -1);
        return $ret;
    }


    function updateDianping()
    {
        $id = $_REQUEST['id'];
        //curl更新点评信息
        $uri = "http://m.api.51ping.com/tohome/openapi/xieche/updateOrderStatus";
        // 参数数组
        $data = array(
            'orderId' => $id,   //传递临时表id
            'status' => '5', //服务完成
            'methodName' => 'updateOrderStatus',
            'version' => '1.0',
            'partnerId' => 'xieche'
        );
        //计算签名，并返回参数字符串
        $data = $this->combinedSign($data);

        $ch = curl_init();
        // print_r($ch);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        curl_close($ch);
        //将通知结果记录入库
        //echo  $ret ; exit ;
        $dphome = json_decode($ret, true);
        print_r($dphome);
        exit;
        $add_data['code'] = $dphome['code'];
        $add_data['msg'] = $dphome['msg'];
        $add_data['orderid'] = $id;
        $add_data['type'] = 1;
        $interface_log->add($add_data);
    }

    function edit_code()
    {

        $model = D('carservicecode');
        $map['pici'] = '69';
        $list = $model->where($map)->select();
        echo $model->getLastsql();
        foreach ($list as $k => $v) {
            $data['coupon_code'] = substr($v['coupon_code'], 0, 7);
            //print_r($data['coupon_code']);exit;
            $model->where(array('id' => $v['id']))->save($data);
        }
    }

    function test_weixin_noshare()
    {
        //$a = file_get_contents('https://jinshuju.net/f/8HJQs3');
        //echo $a;
        echo "
			<html>
			<head>
			</head>
			<body>
			<div>
			<iframe id='goldendata_form_8HJQs3' src='https://jinshuju.net/f/8HJQs3' width='100%' frameborder=0 allowTransparency='true' height='607'></iframe>
			</div>
			<script>
			function onBridgeReady(){
			WeixinJSBridge.call('hideOptionMenu');
			}

			if (typeof WeixinJSBridge == \"undefined\"){
			if( document.addEventListener ){
			document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
			}else if (document.attachEvent){
			document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
			document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
			}
			}else{
			onBridgeReady();
			}
			</script>
			</body>
			</html>
			";
    }

    function get_member_sh()
    {
        $s_model = D('api_record');
        $r_model = D('reservation_order');
        $o_model = D('order');
        $m_model = D('member');
        $b_model = D('bidorder');
        $o_list = $o_model->field('mobile')->Distinct(true)->select();
        //echo $o_model->getLastsql();
        $b_list = $b_model->field('mobile')->Distinct(true)->select();
        //echo $b_model->getLastsql();
        $r_list = $r_model->where(array('city_id' => '1'))->field('mobile')->Distinct(true)->select();
//echo $r_model->getLastsql();
        foreach ($o_list as $k => $v) {
            $list[] = $v['mobile'];
        }
        foreach ($b_list as $kk => $vv) {
            $list[] = $vv['mobile'];
        }
        foreach ($r_list as $kkk => $vvv) {
            $list[] = $vvv['mobile'];
        }
        $list = array_unique($list);
        echo '<table>';
        foreach ($list as $k => $v) {
            echo '<tr><td>' . $v . '</td></tr>';
        }
        echo '<tr><td>13611673358</td></tr>';
        echo '<tr><td>18516200892</td></tr>';
        echo '</table>';
        //print_r($list);exit;
    }

    function test_jianpeijian()
    {
        $a = array('a' => '1', 'b' => '1');
        $b = array('a' => '1');
        foreach ($b as $id => $num) {
            foreach ($a as $_id => $_num) {
                if ($id == $_id) {
                    $cut_num = $_num - $num;
                    if ($cut_num > 0) {
                        $cut['oil_detail'][$id] = $cut_num;
                    }
                } elseif ($a[$_id] > 0 and !$b[$_id]) {
                    $cut['oil_detail'][$_id] = $_num;
                }
            }
        }
        print_r($cut);
    }

    function get_xiechengdata()
    {
        exit;
        $r_model = D('reservation_order');
        $map['xc_reservation_order.replace_code'] = array('like', '%n%');
        $r_list = $r_model->join('xc_carservicecode on xc_carservicecode.coupon_code=xc_reservation_order.replace_code')->where($map)->field('xc_reservation_order.*')->select();
        //echo $r_model->getLastsql();
        //print_r($r_list);
        echo "<table>";
        foreach ($r_list as $k => $v) {
            echo "<tr><td>" . $v['id'] . "</td><td>" . $v['truename'] . "</td><td>" . $v['mobile'] . "</td><td>" . date('Y-m-d H:i:s', $v['create_time']) . "</td></tr>>";
        }
    }

    function get_sm_order()
    {
        //exit;
        $r_model = D('reservation_order');
        $t_model = D('technician');
        $c_model = D('city');
        $s_model = D('check_step');

        $map['order_time'] = array(array('egt','1441036800'),array('elt','1443456000'));
        $map['licenseplate'] = array('not in','苏EV18Z7,苏EV30Z6,苏EV03Z6,苏EV18Z1,苏EV28Z9,苏EV07Z0,苏EV07Z6,苏EV29Z6,苏EV05Z7,苏EV07Z8,苏EV10Z0,苏EV11Z9,苏EV20Z8,苏EV28Z1,苏EV09Z0,苏EV29Z9,苏EV03Z9,苏EV22Z5,苏EV22Z8,苏EV26Z9,苏EV28Z8,苏EV07Z7,苏EV22Z2,苏EV28Z0,苏EV28Z2,苏EV28Z5,苏EV19Z7,苏EV02Z9,苏EV06Z2,苏EV30Z0');
        $r_list = $r_model->where($map)->order('create_time asc')->select();
        echo $r_model->getLastSql();
        echo "<table><th>订单号</th><th>客服姓名</th><th>原价</th><th>折扣</th><th>现价</th><th>完成时间</th><th>优惠券</th><th>下单时间</th><th>订单状态</th><th>付款方式</th><th>订单类型</th><th>技师ID</th><th>城市ID</th>";
        foreach ($r_list as $k => $v) {
            $list[$k]['yj'] = $v['amount'] + $v['dikou_amount'];
            //$list[$k]['order_time'] = date('Y-m-d H:i:s', $v['order_time']+rand(3000,5000));
            $mad['order_id'] = $v['id'];
            $mad['step_id'] = 6;
            $s_info = $s_model->where($mad)->find();
            $list[$k]['order_time'] = date('Y-m-d H:i:s',$s_info['create_time']);
            $list[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $list[$k]['status'] = $this->get_status($v['status']);
            $list[$k]['pay_type'] = $this->get_paytype($v['pay_type']);
            $t_info = $t_model->where(array('id'=>$v['technician_id']))->find();
            $c_info = $c_model->where(array('id'=>$v['city_id']))->find();
            echo "<tr><td>".$v['id']."</td><td>".$v['truename']."</td><td>".$list[$k]['yj']."</td><td>".$v['dikou_amount']."</td><td>".$v['amount']."</td><td>".$list[$k]['order_time']."</td><td>".$v['replace_code']."</td><td>".$list[$k]['create_time']."</td><td>".$list[$k]['status']."</td><td>".$list[$k]['pay_type']."</td><td>".$v['order_type']."</td><td>".$t_info['truename']."</td><td>".$c_info['name']."</td></tr>";
        }
        echo "</table>";
    }

    function get_status($status){
        switch ($status) {
            case 0:
                $chars = '等待处理';
                break;
            case 1:
                $chars = '预约确认';
                break;
            case 2:
                $chars = '分配技师';
                break;
            case 7:
                $chars = '用户终止';
                break;
            case 8:
                $chars = '订单作废';
                break;
            case 9:
                $chars = '订单完成';
                break;

        }
        return $chars;
    }

    function get_paytype($pay_type){
        switch ($pay_type) {
            case 0:
                $chars = '现金';
                break;
            case 1:
                $chars = '现金';
                break;
            case 2:
                $chars = '微信支付';
                break;
            case 3:
                $chars = 'pos机支付';
                break;
            case 4:
                $chars = '淘宝支付';
                break;
            case 5:
                $chars = '大众点评';
                break;
            case 6:
                $chars = '建行支付';
                break;
            case 7:
                $chars = '京东';
                break;
            case 8:
                $chars = '养车点点';
                break;
            case 9:
                $chars = '支付宝扫码';
                break;
            case 10:
                $chars = '点评到家';
                break;
            case 11:
                $chars = '支付宝WAP';
                break;
        }
        return $chars;
    }

    function get_bidorder(){
        $b_model = D('bidorder');
        $r_list = $b_model->where($map)->order('create_time asc')->select();
        echo $b_model->getLastSql();
        echo "<table><th>订单号</th><th>定损金额</th><th>4S店</th><th>完成时间</th><th>下单时间</th>";
        foreach ($r_list as $k => $v) {
            $model_insurance = D('insurance');
            $shop = D('shop');
            $info = $model_insurance->where(array('id'=>$v['insurance_id']))->find();
            $s_info = $shop->where(array('id'=>$v['shop_id']))->find();
            $v['loss_price'] = $info['loss_price'];
            $v['shop_name'] = $s_info['shop_name'];
            $v['complete_time'] = date('Y-m-d H:i:s', $v['complete_time']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            echo "<tr><td>".$v['id']."</td><td>".$v['loss_price']."</td><td>".$v['shop_name']."</td><td>".$v['complete_time']."</td><td>".$v['create_time']."</td></tr>";
        }
        echo "</table>";
    }

    function get_operator(){
        $model = M('tp_admin.user', 'xc_');
        $list = $model->select();
        echo $model->getLastsql();
        echo "<table><th>昵称</th><th>用户名</th><th>注册时间</th><th>是否启用</th>";
        foreach($list as $k=>$v){
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            if($v['status']==1){
                $v['status']='启用';
            }else{
                $v['status']='停用';
            }
            echo "<tr><td>".$v['account']."</td><td>".$v['nickname']."</td><td>".$v['create_time']."</td><td>".$v['status']."</td></tr>";
        }
        echo "</table>";
    }

    function get_role(){
        $r_model = M('tp_admin.role', 'xc_');
        $rs_model = M('tp_admin.role_user', 'xc_');
        $n_model = M('tp_admin.node', 'xc_');
        $a_model = M('tp_admin.access', 'xc_');
        $r_list = $r_model->select();
        $rs_list = $rs_model->select();
        $n_list = $n_model->select();
        $a_list = $a_model->select();
        echo "<table><th>用户组ID</th><th>用户组名</th>";
        foreach($r_list as $k=>$v){
            echo "<tr><td>".$v['id']."</td><td>".$v['name']."</td></tr>";
        }
        echo "</table>";
        echo "<table><th>用户组ID</th><th>用户ID</th>";
        foreach($rs_list as $k=>$v){
            echo "<tr><td>".$v['role_id']."</td><td>".$v['user_id']."</td></tr>";
        }
        echo "</table>";
        echo "<table><th>模块ID</th><th>模块名</th>";
        foreach($n_list as $a=>$b){
            echo "<tr><td>".$b['id']."</td><td>".$b['title']."</td></tr>";
        }
        echo "</table>";
        echo "<table><th>用户组ID</th><th>模块ID</th>";
        foreach($a_list as $aa=>$bb){
            echo "<tr><td>".$bb['role_id']."</td><td>".$bb['node_id']."</td></tr>";
        }
        echo "</table>";
    }

    function get_paduser(){
    $t_model = D('technician');
    $t_list = $t_model->select();

    echo "<table><th>技师名</th><th>添加时间</th>";
    foreach($t_list as $k=>$v){
        echo "<tr><td>".$v['truename']."</td><td>".date('Y-m-d H:i:s',$v['create_time'])."</td></tr>";
    }


}

    function get_yinliu(){
        $r_model = D('reservation_order');
        $map['order_time'] = array(array('egt','1439395200'),array('elt','1439481600'));
        $map['status'] = array('neq','8');
        $r_list = $r_model->where($map)->select();
        echo $r_model->getLastsql();
        $count = 0;
        foreach($r_list as $k=>$v){
            $m_count = $r_model->where(array('mobile'=>$v['mobile']))->count();
            if($m_count==1 and $v['amount']<100){
                $count++;
            }
        }
        echo $count;
    }

    function get_addressbyip(){
        $model = M('tp_admin.user_log', 'xc_');
        $map['login_time'] = array('egt','1437235200');
        $map['address'] = '';
        $list = $model ->where($map)->order('id desc')->select();
        echo $model ->getLastSql();//exit;
        include("./ThinkPHP/Extend/Library/ORG/Qrcode/IP.class.php");
        foreach($list as $k=>$v){
            /*$host ="freeapi.ipip.net/".$v['login_ip'];
            $ch = curl_init();
            var_dump($ch);
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$host);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT,30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
            $output = curl_exec($ch);
            curl_close($ch);*/
            $output = IP::find($v['login_ip']);
            //print_r($output);exit;

            $data['address'] = $output[0].'-'.$output[1].'-'.$output[2].'-'.$output[3];
            $model ->where(array('id'=>$v['id']))->save($data);
            //sleep(20);
        }
    }


    function update_address(){
        $model = M('tp_admin.user_log', 'xc_');
        $map['login_time'] = array('egt','1437235200');
        $map['address'] = array('neq','');
        $list = $model ->where($map)->order('id desc')->field('login_ip,address')->group('login_ip')->select();
        echo $model ->getLastSql();

        foreach($list as $k=>$v){
            $data['address'] = $v['address'];
            $model ->where(array('login_ip'=>$v['login_ip']))->save($data);
            //echo $model ->getLastSql();exit;
        }
    }

    function alluser(){
        $map['create_time'] = array('lt','1438358400');
        //$map['status'] = array('neq','8');
        $list = $this->reservation_order_model->where($map)->field('mobile')->select();
        //echo $this->reservation_order_model->getLastSql();
        foreach($list as $k=>$v){
            echo $v['mobile'].'</br>';
        }
    }
    //跑券码时间
    function get_code_time(){
        $model = D('carservicecode');
        $c_model = D('check_step');
        $sql = "SELECT a.coupon_code,b.id,from_unixtime(b.order_time) as create_time
FROM  xc_carservicecode AS a, xc_reservation_order AS b
WHERE 1
AND a.coupon_code = b.replace_code
AND a.status =1";
        $list = $model->query($sql);
        //print_r($list);
        echo "<table>";
        foreach($list as $k=>$v){
            $map['order_id'] = $v['id'];
            $map['step_id'] = 6;
            $info = $c_model->where($map)->find();
            if(!$info['create_time']){
                $info['create_time'] = $v['create_time'];
            }else{
                $info['create_time'] = date('Y-m-d H:i:s',$info['create_time']);
            }
            echo "<tr><td>".$v['coupon_code']."</td><td>".$info['create_time']."</td></tr>";
        }
        echo "</table>";
    }

    function send_sms()
    {
        exit;
        $model = D('carservicecode_1');
        $map['pici'] = 6;
        $map['mobile'] = array('neq', '');
        $info = $model->where($map)->select();

        foreach ($info as $k=>$v) {
            $post['phones'] = $v['mobile'];
            $post['content'] = '尊敬的美亚车险客户，携车网府上养车将为您提供上门服务费包年，在保单有效期内，凭包年服务码'.$v['coupon_code'].'，享上门小保养人工服务费全免（保养周期需间隔5000KM或6个月）。关注携车网微信公众号或4006602822下单。【携车网】';
            $client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
            $param = array("userCode" => "csml", "userPass" => "csml5103", "DesNo" => $post["phones"], "Msg" => $post["content"], "Channel" => "1");
            $res = $client->__soapCall('SendMsg', array('parameters' => $param));

            $this->addCodeLog('发送短信', '返回值:' . var_export($res, true));
            $this->submitCodeLog('发送短信');
        }
    }
    
    
    function get_daycomplete(){
        $model = D('daycomplete');
        $r_model = D('reservation_order');
        $info = $model->where(array('id'=>'150'))->find();
        $order_ids = unserialize($info['order_ids']);
        echo "<table><tr><th>id</th><th>状态</th></tr>";
        foreach($order_ids as $k=>$v){
            $order = $r_model->where(array('id'=>$v['id']))->find();
            $time = date('Y-m-d H:i:s',$order['order_time']);
            echo "<tr><td>".$v['id']."</td><td>".$order['status']."</td><td>".$time."</td><td>".$order['city_id']."</td><td>".$order['operator_remark']."</td></tr>";
        }
        echo "</table>";
    }

    function get_baoxian()
    {
        $r_model = D('reservation_order');
        $o_model = D('item_oil');
        $f_model = D('item_filter');
        $b_model = D('carbrand');
        $s_model = D('carseries');
        $model_model = D('carmodel');
        $map['order_time'] = array(array('egt', '1435680000'), array('elt', '1443628800'));
        $map['status'] = 9;
        //$map['city_id'] = 1;
        $map['replace_code'] = array(array('like', '%v%'),'or');
        $map['amount'] = array('egt', '0');
        $map['order_type'] = array('neq', '34');
        //$map['pay_type'] = 4;
        //$info = $r_model->where($map)->select();
        $sql = "SELECT a . *
FROM `xc_reservation_order` AS a, xc_carservicecode_1 AS b
WHERE a.replace_code = b.coupon_code
AND b.pici =6";
        $info = $r_model->query($sql);


        echo $r_model->getLastsql();
        echo "<table><tr><th>订单号</th><th>城市</th><th>上门日期</th><th>支付方式</th><th>机油</th><th>价格</th><th>机滤</th><th>价格</th><th>空气滤</th><th>价格</th><th>空调滤</th><th>价格</th><th>抵扣价</th><th>总价</th><th>姓名</th><th>车型</th><th>手机号码</th><th>用户备注</th><th>客服备注</th></tr>";
        $i = 0;
        foreach ($info as $k => $v) {
            /*$map_r['order_time'] = array('egt','1420041600');
			$map_r['mobile'] = $v['mobile'];
			$count = $r_model->where($map_r)->count();
			//echo $r_model->getLastsql();exit;
			if($count>0){
				continue;
			}*/
            $i++;
            $v['order_time'] = date('Y-m-d H:i:s', $v['order_time']);
            if ($v['pay_type'] == 1) {
                $v['pay_type'] = '现金支付';
            }
            if ($v['pay_type'] == 2) {
                $v['pay_type'] = '在线支付';
            }
            if ($v['pay_type'] == 3) {
                $v['pay_type'] = 'POS支付';
            }
            if ($v['pay_type'] == 4) {
                $v['pay_type'] = '淘宝支付';
            }
            if ($v['city_id'] == 1) {
                $v['city_id'] = '上海';
            }
            if ($v['city_id'] == 2) {
                $v['city_id'] = '杭州';
            }
            $item = unserialize($v['item']);
            $oil_data = $item['oil_detail'];
            //print_r($item['price']);
            $oil = '';
            $oil_price = '';
            $filter = '';
            $filter_price = '';
            $kongqi = '';
            $kongqi_price = '';
            $kongtiao = '';
            $kongtiao_price = '';
            $value = '';
            $norms = '';
            foreach ($item['price']['oil'] as $kk => $vv) {
                if ($vv > 0) {
                    $o_info = $o_model->where(array('id' => $kk))->find();
                    $oil_price += $vv;
                }
            }
            foreach ($oil_data as $_id => $num) {
                if ($num > 0) {
                    $res = $o_model->field('name,price,type,norms')->where(array('id' => $_id))->find();
                    $norms += $res['norms'] * $num;
                    $oil = $res['name'] . ' ' . $norms . 'L';
                }
            }
            foreach ($item['price']['filter'] as $a => $b) {
                if ($b > 0) {
                    $f_info = $f_model->where(array('id' => $a))->find();
                    $filter = $f_info['name'];
                    $filter_price = $b;
                }
            }
            foreach ($item['price']['kongqi'] as $aa => $bb) {
                if ($bb > 0) {
                    $f_info = $f_model->where(array('id' => $aa))->find();
                    $kongqi = $f_info['name'];
                    $kongqi_price = $bb;
                }
            }
            foreach ($item['price']['kongtiao'] as $_k => $_v) {
                if ($_v > 0) {
                    $f_info = $f_model->where(array('id' => $_k))->find();
                    $kongtiao = $f_info['name'];
                    $kongtiao_price = $_v;
                }
            }

            if($v['dikou_amount']==99){ $v['dikou_amount']=68; }

            $model = $model_model->where(array('model_id' => $v['model_id']))->find();
            $series = $s_model->where(array('series_id' => $model['series_id']))->find();
            $brand = $b_model->where(array('brand_id' => $series['brand_id']))->find();

            echo "<tr><td>" . $v['id'] . "</td><td>" . $v['city_id'] . "</td><td>" . $v['order_time'] . "</td><td>" . $v['pay_type'] . "</td><td>" . $oil . "</td><td>" . $oil_price . "</td><td>" . $filter . "</td><td>" . $filter_price . "</td><td>" . $kongqi . "</td><td>" . $kongqi_price . "</td><td>" . $kongtiao . "</td><td>" . $kongtiao_price . "</td><td>" . $v['dikou_amount'] . "</td><td>" . $v['amount'] . "</td><td>" . $v['truename'] . "</td><td>" . $brand['brand_name'] . $series['series_name'] . $model['model_name'] . "</td><td>" . $v['mobile'] . "</td><td>" . $v['remark'] . "</td><td>" . $v['replace_code'] . "</td></tr>";
            //exit;
        }
        echo "</table>";
        echo 'i=' . $i;
    }
    
    //昨天订单数据
    public function yesterday_order() {
        $order_model = D('reservation_order');
        $carbrand_model = D('carbrand');
        $carseries_model = D('carseries');
        $carmodel_model = D('carmodel');
        $city_model = D('city');
        $technician_model = D('technician');
        $daycomplete_model = D('daycomplete');
        
        $map['id'] = 158 ;
        $order =  $daycomplete_model->where($map)->find() ;
        $order_ids =  unserialize($order['order_ids']) ;
        //print_r($order_ids);
        
        echo  '<table><tr> <td>订单号</td> <td>城市</td> <td>技师</td> <td>车型</td><td>项目</td><td>状态</td></tr>' ;
        foreach ($order_ids as $k => $v) {
            $con['id'] = $v['id'] ;
            $order_info = $order_model->where($con)->find();
            if($order_info['status'] == 8){
               $status  = '作废' ;
            }else{
               $status  = '推迟' ; 
            }
            $city = $city_model->where('id ='.$order_info['city_id'])->find();
            $technician = $technician_model->where(' id='.$order_info['technician_id'])->find();
            //车型
            $con2['model_id'] = $order_info['model_id'];
            $carmodel = $carmodel_model->where('model_id='.$order_info[model_id])->find();
            //追加车系
            $carseries = $carseries_model->where('series_id='.$carmodel['series_id'])->find();
            //追加品牌
            $carbrand = $carbrand_model->where('brand_id='.$carseries['brand_id'])->find();
            
            $name = $this->_carserviceConf($order_info[order_type]);
             
            echo  '<tr> <td> '.$order_info[model_id].' </td> <td>'.$city[name].'</td> <td>'.$technician[truename].'</td> <td>'.$carbrand[brand_name].'--'.$carseries[series_name].'--'.$carmodel[model_name].'</td> <td>'.$name.'</td><td>'.$status.'</td></tr>' ;  

        }
   
    }
    
    
    

}
?>
 