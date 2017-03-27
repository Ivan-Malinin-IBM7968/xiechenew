<?php

/**
 * DrainordertransformAction 引流订单转化
 */
class DrainordertransformAction extends CommonAction
{

    public function __construct()
    {
        parent::__construct();

        $this->drain_model = D(GROUP_NAME . '/DrainOrderTransform');
        $this->drain_order = D('drain_order');
        $this->trans_order = D('trans_order');
        $this->kv = D('kv');
    }

    /**
     * index page
     */
    public function index()
    {
        $events = $event_names = array();

        $is_show_all = (int)$_POST['show_all'];

        $drain_trans_orders = $this->getV('drain_trans_orders');

        foreach ($drain_trans_orders as $k=>$event) {
            $year_month = (int)date('Ym', $event['date']);
            $event['date'] = date('Y-m-d', $event['date']);

            $event_names[$k] = $event['name'];

            if ($is_show_all > 0 || ($is_show_all == 0 && $event['drain_order']['count'] > 10)) {
                $events[$year_month][] = $event;
            }
        }

        //冒泡排序
        krsort($events);
        foreach ($events as $key => $event) {
            $count = count($event);
            for ($i = 0; $i < $count; $i++) {
                for ($k = $count - 1; $k > $i; $k--) {
                    if($event[$k]['date'] > $event[$k-1]['date']) {
                        $tmp = $event[$k];
                        $event[$k] = $event[$k-1];
                        $event[$k-1] = $tmp;
                    }
                }
            }
            unset($events[$key]);
            $events[$key] = $event;
        }

        if ($_POST) {
            $event_id = (int)$_POST['event_id'];
            $thread_id = 'co_event_'.$event_id;

            $trans_orders = $this->drain_model->getTransOrdersByThread($thread_id);

            foreach ($trans_orders as $trans_order) {
                foreach ($trans_order as $tr) {
                    $event_ym = $tr['event_ym'];
                    break;
                }
            }

            $this->assign('trans_orders', $trans_orders);
            $this->assign('event_ym', $event_ym);
            $this->assign('ym', (int)date('Ym')+1);
            $this->assign('selected_event', $event_id);

        }

        $this->assign('event_names', $event_names);
        $this->assign('events', $events);

        unset($drain_trans_orders, $events);

        $this->display();
    }


    /**
     * 将引流订单及转化数据导入数据库
     *
     * todo 转为使用命令行导入数据
     */
    public function import()
    {
        $y = (int)date('Y');    //当前年份
        $m = (int)date('m');    //当前月份
        $ym = (int)date('Ym');  //当前年月

        $get_month = function($m, $interval) { return strtotime(date('Y-'.$m.'-01').'+'.$interval.' month'); };
        $get_day = function($d, $interval = 0) { return strtotime(date('Y-m-d', $d).'+'.$interval.' day'); };

        $event_arr = $this->drain_model->getEventByBussiness();

        //遍历每一个活动
        foreach ($event_arr as $k=>$event) {
            $event_id = $event['id'] = (int)$event['id'];
            $event_date = $this->drain_model->getEventDate($event['id']);
            $event['date'] = date('Y-m-d', $event_date);

            $event_m = (int)date('m', $event_date);
            $event_y = (int)date('Y', $event_date);

            if ($event_date) {
                //30天内引流订单
                $drain_trans_orders = $this->getV('drain_trans_orders');

                $drain_trans_orders[$event_id]['drain_order'] = $this->drain_model->getDrainOrders($event['id'], $event_date, $get_day($event_date, 31));

                $drain_trans_orders[$event_id]['name'] = $event['name'];
                $drain_trans_orders[$event_id]['date'] = $event_date;

                $drain_order_uids = $drain_trans_orders[$event_id]['drain_order']['uids'];
                if ($event_id == 8) {
                    //var_dump($drain_trans_orders[$event_id]['drain_order']['uids']);exit;
                }
                unset($drain_trans_orders[$event_id]['drain_order']['uids']);


                $drain_trans_orders[$event_id] = array_merge($drain_trans_orders[$event_id], $this->drain_model->getDayTransOrders($event_id, $drain_order_uids, $drain_trans_orders[$event_id]['drain_order']['count'], $event_date));
                if($event_id == 8) {
                    var_dump($drain_trans_orders[$event_id]);
                }

                $this->saveKV('drain_trans_orders', $drain_trans_orders);

                unset($drain_order_uids, $drain_trans_orders);

                //todo 判断年份
                for ($i = $event_m; $i <= $m; $i ++ ) {
                    $drain_order = $this->drain_model->getDrainOrders($event['id'], $get_month($i), $get_month($i, 1));

                    $map = array(
                        'thread_id' => 'co_event_'.$event['id'],
                        'month' => $i,
                        'year' => $event_y
                    );

                    $record = array(
                        'thread_id' => 'co_event_'.$event['id'],
                        'event_time' => $event_date,
                        'month' => $i,
                        'year' => $event_y,
                        'count' => $drain_order['count'],
                        'license_count' => $drain_order['license_count'],
                        'amount' => $drain_order['amount'],
                        'data' => serialize($drain_order['uids'])
                    );

                    $drain_order_id = $this->saveDrainOrder($map, $record);

                    //在各个月份的转化订单
                    for ($j = $i; $j <= $m; $j ++) {
                        $trans_start_date = $event_date > $get_month($j) ? $event_date : $get_month($j);
                        $trans_order = $this->drain_model->getTransOrder($event_id, $drain_order['uids'], $drain_order['count'], $trans_start_date, $get_month($j, 1));

                        $trans_map = array(
                            'thread_id' => 'co_event_'.$event['id'],
                            'month' => $j,
                            'year' => $event_y,
                            'drain_order_id' => $drain_order_id
                        );

                        $trans_record = array(
                            'thread_id' => 'co_event_'.$event['id'],
                            'month' => $j,
                            'year' => $event_y,
                            'drain_order_id' => $drain_order_id,
                            'rate' => $trans_order['rate'],
                            'amount' => $trans_order['amount'],
                            'count' => $trans_order['count']
                        );

                        $this->saveTransOrder($trans_map, $trans_record);

                        unset($trans_order);
                    }

                    unset($drain_order);

                }
            }

        }

        echo('success');
    }


    /**
     * 添加或更新drain_order表数据
     *
     * @param $map
     * @param $record
     */
    public function saveDrainOrder($map, $record)
    {
        if ($this->drain_order->where($map)->count()) {
            $this->drain_order->where($map)->save($record);
        } else {
            $this->drain_order->add($record);
        }

        $order = $this->drain_order->where($map)->find();
        return $order['id'];
    }

    /**
     * 添加或更新trans_order表数据
     *
     * @param $map
     * @param $record
     * @return mixed
     */
    public function saveTransOrder($map, $record)
    {
        if ($this->trans_order->where($map)->count()) {
            $this->trans_order->where($map)->save($record);
        } else {
            $this->trans_order->add($record);
        }

        $order = $this->trans_order->where($map)->find();
        return $order['id'];
    }

    public function saveKV($k, $v)
    {
        $map = array('k' => $k);
        $data = array(
            'k' => $k,
            'v' => serialize($v)
        );

        if ($this->kv->where($map)->count()) {
            $this->kv->where($map)->save($data);
        } else {
            $this->kv->add($data);
        }
    }

    public function getV($k)
    {
        $map = array('k' => $k);
        return @unserialize($this->kv->where($map)->getField('v')) ?: array();
    }

}
