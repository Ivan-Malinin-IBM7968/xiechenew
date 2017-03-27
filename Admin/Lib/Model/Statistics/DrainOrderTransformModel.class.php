<?php

/**
 * DrainOrderTransformModel 引流订单转化model
 */
class DrainOrderTransformModel extends CommonModel
{

    public function __construct()
    {
        parent::__construct();

        $this->business_source = D('business_source');
        $this->reservation_order = D('reservation_order');

        $this->drain_order = D('drain_order');
        $this->trans_order = D('trans_order');
    }

    /**
     * 通过business source表获取活动名称
     *
     * @return array
     */
    public function getEventByBussiness()
    {
        $map = array(
            'level' => 3
        );

        return $this->business_source->field('id, name')->where($map)->select() ?: array();
    }


    /**
     * 获取活动的开始时间，根据第一个下单时间
     *
     * @param $event_id
     * @return string
     */
    public function getEventDate($event_id)
    {
        $map = array(
            'business_source' => $event_id
        );

        $order = $this->reservation_order->field('create_time')->where($map)->order('create_time asc')->find();

        return isset($order['create_time']) ? $order['create_time'] : '';
    }

    /**
     * 获取活动一定时间范围内的引流订单
     *
     * @param $event_id
     * @param string $date_start
     * @param string $date_end
     * @return mixed
     */
    public function getDrainOrders($event_id, $date_start = '', $date_end = '')
    {
        $map = array(
            'business_source' => $event_id,
            'status' => 9,
            'amount' => array('lt', 50)
        );

        if ($date_start) {
            $map['create_time'] = array('egt', $date_start);
        }

        if ($date_end) {
            if (isset($map['create_time'])) {
                $map['create_time'] = array($map['create_time'], array('lt', $date_end));
            } else {
                $map['create_time'] = array('lt', $date_end);
            }
        }

        $orders = $this->reservation_order->field('id, uid, licenseplate, amount')->where($map)->select() ?: array();

        $licenses = $amounts = $uids = array();
        array_map(function($o) use(&$licenses, &$amounts, &$uids){
            $licenses[] =  $o['licenseplate'];
            $amounts[] = (float)$o['amount'];
            $uids[] = (int)$o['uid'];
        }, $orders);

        $return['count'] = count($orders);
        $return['license_count'] = count(array_unique($licenses));
        $return['amount'] = round(array_sum($amounts), 2);
        $return['uids'] = array_unique($uids);

        unset($orders, $licenses, $amounts, $uids);

        return $return;
    }

    /**
     * 获取活动一定时间范围内的转化订单
     *
     * @param $event_id
     * @param array $uids
     * @param int $drain_orders_count
     * @param string $date_start
     * @param string $date_end
     * @return mixed
     */
    public function getTransOrder($event_id, $uids = array(), $drain_orders_count = 0, $date_start = '', $date_end = '')
    {
        $map = array(
            'uid' => array('in', $uids),
            'status' => 9,
            'business_source' => array('neq', $event_id)
        );

        if ($date_start) {
            $map['create_time'] = array('egt', $date_start);
        }

        if ($date_end) {
            if (isset($map['create_time'])) {
                $map['create_time'] = array($map['create_time'], array('lt', $date_end));
            } else {
                $map['create_time'] = array('lt', $date_end);
            }
        }

        $orders = $this->reservation_order->field('id, amount')->where($map)->select() ?: array();

        $return['count'] = count($orders);
        $return['rate'] = round($return['count'] / $drain_orders_count * 100, 2).'%';
        $return['amount'] = array_sum(array_map(function($o){
            return (float)$o['amount'];
        }, $orders));

        return $return;
    }

    /**
     * 获取30、90及之后天数内的30天内引流订单的转化订单情况
     *
     * @param $event_id
     * @param array $uids
     * @param int $drain_orders_count
     * @param $date
     * @return array
     */
    public function getDayTransOrders($event_id, $uids = array(), $drain_orders_count = 0, $date)
    {
        $map = array(
            'uid' => array('in', $uids),
            'status' => 9,
            'create_time' => array('egt', $date),
            'business_source' => array('neq', $event_id)
        );

        $orders = $this->reservation_order->field('id, amount, create_time')->where($map)->select() ?: array();

        $get_day = function($d, $interval) { return strtotime(date('Y-m-d', $d).'+'.$interval.' day'); };

        $count_30 = $count_90 = $count_180 = 0;
        $amount_30 = $amount_90 = $amount_180 = 0;
        foreach ($orders as $order) {
            if ($order['create_time'] < $get_day($date, 31)) {if($event_id == 8)var_dump($order);
                $count_30 ++;
                $amount_30 += (float)$order['amount'];
            } elseif ($order['create_time'] < $get_day($date, 91)) {
                $count_90 ++;
                $amount_90 += (float)$order['amount'];
            } else {
                $count_180 ++;
                $amount_180 += (float)$order['amount'];
            }
        }

        $rate_30 = round($count_30 / $drain_orders_count * 100, 2).'%';
        $rate_90 = round($count_90 / $drain_orders_count * 100, 2).'%';
        $rate_180 = round($count_180 / $drain_orders_count * 100, 2).'%';

        return array(
            'trans_orders_30'=>array($count_30, $rate_30, $amount_30),
            'trans_orders_90'=>array($count_90, $rate_90, $amount_90),
            'trans_orders_180'=>array($count_180, $rate_180, $amount_180),
        );

    }


    public function getTransOrdersByThread($thread_id = '')
    {
        $map = array(
            'thread_id' => $thread_id
        );

        $trans_orders = $this->trans_order->where($map)->select();

        $orders = array();

        foreach($trans_orders as $k=>$trans_order) {
            $drain_order = $this->drain_order->field('year, month, event_time')->where(array('id'=>$trans_order['drain_order_id']))->find();

            $drain_order['month'] = $drain_order['month'] < 10 ? '0'.$drain_order['month'] : $drain_order['month'];
            $trans_order['month'] = $trans_order['month'] < 10 ? '0'.$trans_order['month'] : $trans_order['month'];

            $trans_order['drain_order_ym'] = (int)($drain_order['year'].$drain_order['month']);
            $trans_order['ym'] = (int)($trans_order['year'].$trans_order['month']);
            $trans_order['event_ym'] = (int)date('Ym', $drain_order['event_time']);

            $orders[$trans_order['drain_order_ym']][$trans_order['ym']] = $trans_order;

            unset($drain_order);
        }

        unset($trans_orders);

        return $orders;
    }
}
