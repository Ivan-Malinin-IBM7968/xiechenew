<?php


class TechnicianAchievementModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
        $this->reservation_order = M('tp_xieche.reservation_order','xc_');  //预约订单
        $this->technician_goal = M('tp_xieche.technician_goal', 'xc_');           //技师目标
        $this->kv = D('kv');    //kv表

        $this->coupon = D('new_coupon');
        $this->coupon_code = D('new_coupon_code');
        $this->carservicecode = D('carservicecode');
    }

    /**
     * 获取数据
     */
    public function getAchievementData($city_id = 0, $date = null)
    {
        $condition = array();          //查询条件
        $condition_daily = array();    //每日查询条件
        $count = array();        //数量
        $amount = array();       //产值
        $permeability = array(); //完成率
        $goal = array();         //目标
        $rate = array();         //达成率

        $permeability_order_types = array(); //需要计算渗透率的订单类型

        //获取日期当天、当月时间范围
        $d_condition = function($d) { return array(array('egt', $d), array('lt', $d+86400)); };
        $m_condition = function($d) { return array(array('egt', strtotime(date('Y-m-01', $d))), array('lt', strtotime(date('Y-m-01', $d).' +1 month'))); };

        //当前年份、月份 int
        $year = (int)date('Y');
        $month = (int)date('m');

        //搜索条件
        if ($city_id) {
            $condition['city_id'] = (int)$city_id;
            $condition_daily['city_id'] = (int)$city_id;
        }
        if ($date) {
            $condition['order_time'] = $m_condition($date);
            $condition_daily['order_time'] = $d_condition($date);
            $year = (int)date('Y', $date);
            $month = (int)date('m', $date);
        }

        //获取今天、本月时间范围
        $today = strtotime(date('Y-m-d 00:00:00'));
        $today_condition = $d_condition($today);
        $month_condition = $m_condition($today);

        //完成订单条件
        $condition['status'] = 9;
        $condition_daily['status'] = 9;


        /**
         * 所有订单
         */
        $type = 'order_all';
        $condition_all = array_merge(array('order_time'=>$month_condition), $condition);
        $condition_all_daily = array_merge(array('order_time'=>$today_condition), $condition_daily);

        $this->getOrderCount($count, $type, $condition_all, $condition_all_daily);
        $this->getOrderAmount($amount, $type, $condition_all, $condition_all_daily);

        $this->getAchievementGoal($goal, $type, $year, $month, $city_id);
        $this->getAchievementRate($rate, $type, $count, $goal);

        //客单价
        $amount['order_all_single'] = round($amount['order_all'] / $count['order_all'], 2);
        $amount['order_all_single_daily'] = round($amount['order_all_daily'] / $count['order_all_daily'], 2);


        $orders = $this->reservation_order->where($condition_all)->order('id desc')->select();
        $daily_orders = $this->reservation_order->where($condition_all_daily)->order('id desc')->select();


        /**
         * 保养订单
         */
        $type = 'order_maintain';
        $permeability_order_types[] = $type;

        $condition_maintain = array_merge(array(
            'order_type' => array('in', $this->maintainOrderTypes()),
            'order_time' => $month_condition
        ), $condition);
        $condition_maintain_daily = array_merge(array(
            'order_type' => array('in', $this->maintainOrderTypes()),
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_maintain, $condition_maintain_daily);
        $this->getOrderAmount($amount, $type, $condition_maintain, $condition_maintain_daily);

        $this->getAchievementGoal($goal, $type, $year, $month, $city_id);
        $this->getAchievementRate($rate, $type, $count, $goal);

        //保养订单客单价
        $amount['order_maintain_single'] = round($amount['order_maintain'] / $count['order_maintain'], 2);
        $amount['order_maintain_single_daily'] = round($amount['order_maintain_daily'] / $count['order_maintain_daily'], 2);


        /**
         * 节气门清洗作业
         *
         * order_type
         * 15 节气门清洗
         */
        $type = 'order_throttle_clean';
        $permeability_order_types[] = $type;

        $condition_throttle_clean = array_merge(array(
            'order_type'=>15,
            'order_time' => $month_condition
        ), $condition);
        $condition_throttle_clean_daily = array_merge(array(
            'order_type' => $condition_throttle_clean['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_throttle_clean, $condition_throttle_clean_daily);
        $this->getOrderAmount($amount, $type, $condition_throttle_clean, $condition_throttle_clean_daily);

        /*
         * 空调管道清洗单数
         *
         * order_type
         * 62 空调清洗套餐（后付费）
         * 63 空调清洗套餐（免费）
         * 66 空调清洗（点评到家）
         */
        $type = 'order_air_clean';

        $condition_air_clean = array_merge(array(
            'order_type' => array('in', array(62, 63, 66)),
            'order_time' => $month_condition
        ), $condition);
        $condition_air_clean_daily = array_merge(array(
            'order_type' => $condition_air_clean['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_air_clean, $condition_air_clean_daily);
        $this->getOrderAmount($amount, $type, $condition_air_clean, $condition_air_clean_daily);

        /*
         * 发动机除碳单数
         *
         * order_type
         * 72 发动机除碳（预付费）
         * 73 发动机除碳（后付费）
         */
        $type = 'order_engine_carbon';

        $condition_engine_carbon = array_merge(array(
            'order_type' => array('in', array(72, 73)),
            'order_time' => $month_condition
        ), $condition);
        $condition_engine_carbon_daily = array_merge(array(
            'order_type' => $condition_engine_carbon['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_engine_carbon, $condition_engine_carbon_daily);
        $this->getOrderAmount($amount, $type, $condition_engine_carbon, $condition_engine_carbon_daily);

        /**
         * 机舱清洗作业单数
         *
         * order_type
         * 55 发动机舱精洗套餐（淘）
         * 59 发动机机舱精洗套餐（预付费）
         */
        $type = 'order_engine_clean';
        $permeability_order_types[] = $type;

        $condition_engine_clean = array_merge(array(
            'order_type' => array('in', array(55, 59)),
            'order_time' => $month_condition
        ), $condition);
        $condition_engine_clean_daily = array_merge(array(
            'order_type' => $condition_engine_clean['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_engine_clean, $condition_engine_clean_daily);
        $this->getOrderAmount($amount, $type, $condition_engine_clean, $condition_engine_clean_daily);


        /**
         * 钢圈清洗作业单数
         *
         * order_type
         * 65 轮毂清洗套餐（预付费）
         */
        $type = 'order_rim_clean';
        $permeability_order_types[] = $type;

        $condition_rim_clean = array_merge(array(
            'order_type' => 65,
            'order_time' => $month_condition
        ), $condition);
        $condition_rim_clean_daily = array_merge(array(
            'order_type' => $condition_rim_clean['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_rim_clean, $condition_rim_clean_daily);
        $this->getOrderAmount($amount, $type, $condition_rim_clean, $condition_rim_clean_daily);


        /**
         * 1元检查
         *
         * order_type
         * 4 1元检测订单
         */
        $type = 'order_one_check';
        $condition_one_check = array_merge(array(
            'order_type' => 4,
            'order_time' => $month_condition
        ), $condition);
        $condition_one_check_daily = array_merge(array(
            'order_type' => $condition_one_check['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_one_check, $condition_one_check_daily);
        $this->getOrderAmount($amount, $type, $condition_one_check, $condition_one_check_daily);

        /**
         * 免费检查
         *
         * order_type
         * 2 免费检测订单
         */
        $type = 'order_free_check';
        $condition_free_check = array_merge(array(
            'order_type' => 2,
            'order_time' => $month_condition
        ), $condition);
        $condition_free_check_daily = array_merge(array(
            'order_type' => $condition_free_check['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_free_check, $condition_free_check_daily);
        $this->getOrderAmount($amount, $type, $condition_free_check, $condition_free_check_daily);

        //1元及免费检查订单产值
        $amount['order_one_free_check'] = $amount['order_one_check'] + $amount['order_free_check'];
        $amount['order_one_free_check_daily'] = $amount['order_one_check_daily'] + $amount['order_free_check_daily'];

        /**
         * 空调滤作业单数
         *
         * order_type
         * 47 防雾霾空调滤活动
         */
        $type = 'order_air_filter';
        $permeability_order_types[] = $type;

        $condition_air_filter = array_merge(array(
            'order_type' => 47,
            'order_time' => $month_condition
        ), $condition);
        $condition_air_filter_daily = array_merge(array(
            'order_type' => $condition_air_filter['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_air_filter, $condition_air_filter_daily);
        $this->getOrderAmount($amount, $type, $condition_air_filter, $condition_air_filter_daily);


        /**
         * 9.8好车况单数
         *
         * order_type
         * 13 好车况
         * 35 好车况套餐:38项全车检测+7项细节养护
         * 50 好车况（大众点评）
         */
        $type = 'order_good_condition';
        $condition_good_condition = array_merge(array(
            'order_type' => array('in', array(13, 35, 50)),
            'order_time' => $month_condition
        ), $condition);
        $condition_good_condition_daily = array_merge(array(
            'order_type' => $condition_good_condition['order_type'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_good_condition, $condition_good_condition_daily);
        $this->getOrderAmount($amount, $type, $condition_good_condition, $condition_good_condition_daily);

        /**
         * 补配件免人工单数
         *
         * order_type
         * 34 补配件免人工订单
         */
        $type = 'order_fitting';
        $permeability_order_types[] = $type;

        $condition_fitting = array_merge(array(
            '_complex' => $this->fittingOrderCondition(),
            '_logic' => 'and',
            'order_time' => $month_condition
        ), $condition);
        $condition_fitting_daily = array_merge(array(
            '_complex' => $this->fittingOrderCondition(),
            '_logic' => 'and',
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_fitting, $condition_fitting_daily);
        $this->getOrderAmount($amount, $type, $condition_fitting, $condition_fitting_daily);

        /**
         * 非保养订单
         */
        $type = 'order_except_maintain';
        $permeability_order_types[] = $type;

        $condition_except_maintain = array_merge(array(
            'order_type' => array('in', $this->exceptMaintainOrderTypes()),
            'order_time' => $month_condition
        ), $condition);
        $condition_except_maintain_daily = array_merge(array(
            'order_type' => array('in', $this->exceptMaintainOrderTypes()),
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_except_maintain, $condition_except_maintain_daily);
        $this->getOrderAmount($amount, $type, $condition_except_maintain, $condition_except_maintain_daily);

        $this->getAchievementGoal($goal, $type, $year, $month, $city_id);
        $this->getAchievementRate($rate, $type, $count, $goal);


        /**
         * 技师pad下单
         */
        $type = 'order_pad';
        $permeability_order_types[] = $type;

        $condition_pad = array_merge(array(
            'origin' => 6,
            'order_time' => $month_condition
        ), $condition);
        $condition_pad_daily = array_merge(array(
            'origin' => $condition_pad['origin'],
            'order_time' => $today_condition
        ), $condition_daily);

        $this->getOrderCount($count, $type, $condition_pad, $condition_pad_daily);

        /**
         * 大客户单（银行、保险）
         */
        $type = 'order_big_customer';
        $permeability_order_types[] = $type;

        $big_customer_orders = $daily_big_customer_orders = $big_customer_maintain_orders = $daily_big_customer_maintain_orders = $big_customer_except_maintain_orders = $daily_big_customer_except_maintain_orders = $big_customer_fitting_orders = $daily_big_customer_fitting_orders = array();

        $big_customer_orders_count = $daily_big_customer_orders_count = $big_customer_maintain_orders_count = $daily_big_customer_maintain_orders_count = $big_customer_except_maintain_orders_count = $daily_big_customer_except_maintain_orders_count = $big_customer_fitting_orders_count = $daily_big_customer_fitting_orders_count = 0;

        $big_customer_orders_amount = $daily_big_customer_orders_amount = $big_customer_maintain_orders_amount = $daily_big_customer_maintain_orders_amount = $big_customer_except_maintain_orders_amount = $daily_big_customer_except_maintain_orders_amount = $big_customer_fitting_orders_amount = $daily_big_customer_fitting_orders_amount = 0;

        $this->getBigCustomerOrders($orders, $big_customer_orders_count, $big_customer_orders_amount, $big_customer_maintain_orders_count, $big_customer_maintain_orders_amount, $big_customer_except_maintain_orders_count, $big_customer_except_maintain_orders_amount, $big_customer_fitting_orders_count, $big_customer_fitting_orders_amount, $big_customer_orders);

        $this->getBigCustomerOrders($daily_orders, $daily_big_customer_orders_count, $daily_big_customer_orders_amount, $daily_big_customer_maintain_orders_count, $daily_big_customer_maintain_orders_amount, $daily_big_customer_except_maintain_orders_count, $daily_big_customer_except_maintain_orders_amount, $daily_big_customer_fitting_orders_count, $daily_big_customer_fitting_orders_amount, $daily_big_customer_orders);

        //var_dump($daily_big_customer_orders);

        /*foreach ($daily_big_customer_orders as $o) {
            var_dump($o['id'], $o['truename'], $o['replace_code'], $o['operator_remark']);
            var_dump('<br/>');
        }*/

        $count['order_big_customer'] = $big_customer_orders_count;
        $count['order_big_customer_daily'] = $daily_big_customer_orders_count;
        $amount['order_big_customer'] = $big_customer_orders_amount;
        $amount['order_big_customer_daily'] = $daily_big_customer_orders_amount;

        $count['order_big_customer_maintain'] = $big_customer_maintain_orders_count;
        $count['order_big_customer_maintain_daily'] = $daily_big_customer_maintain_orders_count;
        $amount['order_big_customer_maintain'] = $big_customer_maintain_orders_amount;
        $amount['order_big_customer_maintain_daily'] = $daily_big_customer_maintain_orders_amount;

        $count['order_big_customer_except_maintain'] = $big_customer_except_maintain_orders_count;
        $count['order_big_customer_except_maintain_daily'] = $daily_big_customer_except_maintain_orders_count;
        $amount['order_big_customer_except_maintain'] = $big_customer_except_maintain_orders_amount;
        $amount['order_big_customer_except_maintain_daily'] = $daily_big_customer_except_maintain_orders_amount;

        $count['order_big_customer_fitting'] = $big_customer_fitting_orders_count;
        $count['order_big_customer_fitting_daily'] = $daily_big_customer_fitting_orders_count;
        $amount['order_big_customer_fitting'] = $big_customer_fitting_orders_amount;
        $amount['order_big_customer_fitting_daily'] = $daily_big_customer_fitting_orders_amount;

        /**
         * 不含大客户总收入
         */
        $amount['order_all_except_big_customer'] = $amount['order_all'] - $amount['order_big_customer'];
        $amount['order_all_except_big_customer_daily'] = $amount['order_all_daily'] - $amount['order_big_customer_daily'];

        /**
         * 渗透率
         */
        $permeability['order_one_free_check'] = round(($count['order_one_check'] + $count['order_free_check']) / $count['order_all'] * 100, 2).'%' ?: 0;
        $permeability['order_one_free_check_daily'] = round(($count['order_one_check_daily'] + $count['order_free_check_daily']) / $count['order_all_daily'] * 100, 2).'%' ?: 0;

        foreach ($permeability_order_types as $p_type) {
            $this->getPermeability($permeability, $p_type, $count);
        }

        unset($permeability_order_types);

        return array('count' => $count, 'amount' => $amount, 'permeability' => $permeability, 'goal' => $goal, 'rate' => $rate);
    }

    /**
     * 获取订单量数据
     *
     * @param string $type
     * @param array $condition
     * @param array $condition_daily
     * @return array
     */
    public function getOrderCount(&$count, $type = '', $condition = array(), $condition_daily = array())
    {
        $daily_type = $type.'_daily';

        $count[$type] = (int)$this->reservation_order->where($condition)->count();
        $count[$daily_type] = (int)$this->reservation_order->where($condition_daily)->count();
    }

    /**
     * 获取产值数据
     *
     * @param string $type
     * @param array $condition
     * @param array $condition_daily
     * @return array
     */
    public function getOrderAmount(&$amount, $type = '', $condition = array(), $condition_daily = array())
    {
        $daily_type = $type.'_daily';

        $amount[$type] = (float)$this->reservation_order->where($condition)->sum('amount');
        $amount[$daily_type] = (float)$this->reservation_order->where($condition_daily)->sum('amount');
    }


    /**
     * 获取完成率数据
     *
     * @param string $type
     * @param array $count
     * @return array
     */
    public function getPermeability(&$permeability, $type = '', $count = array())
    {
        $daily_type = $type.'_daily';

        $permeability[$type] = round($count[$type] / $count['order_all'] * 100, 2).'%';
        $permeability[$daily_type] = round($count[$daily_type] / $count['order_all_daily'] * 100, 2).'%';
    }

    /**
     * 获取每月目标数据
     *
     * @param string $type
     * @param int $year
     * @param int $month
     * @param int $city_id
     * @return array
     */
    public function getAchievementGoal(&$goal, $type = '', $year, $month, $city_id = null)
    {
        $condition = array(
            'year' => $year,
            'month' => $month,
        );

        if ($city_id) {
            $condition['city_id'] = $city_id;
        }

        $goals = $this->technician_goal->field('goal')->where($condition)->find();
        
        $goal_arr = json_decode($goals['goal'], true);

        if ($type == 'order_maintain') {
            $goal[$type] = (int)$goal_arr['care'];
        } elseif ($type == 'order_except_maintain') {
            $goal[$type] = (int)$goal_arr['non_care'];
        } else {
            $goal['order_all'] = (int)$goal_arr['care'] + (int)$goal_arr['non_care'];
        }
    }

    /**
     * 获取达成率数据
     *
     * @param string $type
     * @param array $count
     * @param array $goal
     * @return array
     */
    public function getAchievementRate(&$rate, $type = '', $count = array(), $goal = array())
    {
        $rate[$type] = round($count[$type] / $goal[$type] * 100, 2).'%';
    }

    /**
     * 查询保养订单条件
     * @return array
     */
    public function maintainOrderTypes()
    {
        /**
         * order_type
         *
         * 1 保养订单
         * 3 淘宝99元保养订单
         * 7 黄喜力套餐
         * 8 蓝喜力套餐
         * 9 灰喜力套餐
         * 10 金美孚套餐
         * 11 爱代驾高端保养
         * 12 爱代驾中端保养
         * 17 矿物质油保养套餐+检测+养护
         * 18 半合成油保养套餐+检测+养护
         * 19 全合成油保养套餐+检测+养护
         * 22 光大168套餐
         * 23 光大268套餐
         * 24 光大368套餐
         * 25 浦发199套餐
         * 26 浦发299套餐
         * 27 浦发399套餐
         * 36 大众点评199套餐
         * 37 大众点评299套餐
         * 38 大众点评399套餐
         * 40 安盛天平168套餐
         * 41 安盛天平268套餐
         * 42 安盛天平368套餐
         * 44 平安银行168/199套餐
         * 45 平安银行268/299套餐
         * 46 平安银行368/399套餐
         * 56 黄壳199套餐（预付费）
         * 57 蓝壳299套餐（预付费）
         * 58 灰壳399套餐（预付费）
         * 60 268大保养（预付费）
         * 61 378大保养（预付费）
         * 69 保养人工费工时套餐（点评到家）
         */
        return array(1, 3, 7, 8, 9, 10, 11, 12, 17, 18, 19, 22, 23, 24, 25, 26, 27, 36, 37, 38, 40, 41, 42, 44, 45, 46, 56, 57, 58, 60, 61, 69);
    }


    /**
     * 查询非保养订单条件
     */
    public function exceptMaintainOrderTypes()
    {
        /**
         * order_type
         *
         * 2 免费检测订单
         * 4 1元检测订单
         * 5 更换配件订单（火花塞、雨刷、刹车）
         * 13 好车况
         * 14 好空气
         * 15 好动力
         * 16 保养服务+检测+养护
         * 21 38项检测+7项细节养护（光大）
         * 28 全车检测38项（淘38）
         * 29 细节养护7项（淘38）
         * 30 更换空调滤工时（淘38）
         * 31 更换雨刮工时（淘38）
         * 32 小保养工时（淘38）
         * 33 好空气套餐（奥迪宝马奔驰）
         * 35 好车况套餐：38项全车检测+7项细节养护
         * 39 安盛天平50元检测套餐（不含机油配件）
         * 43 平安银行50元检测套餐（不含机油配）
         * 47 防雾霾空调滤活动
         * 48 防雾霾1元
         * 49 防雾霾8元
         * 50 好车况套餐（大众点评）
         * 51 保养服务+检测+养护（大众点评）
         * 52 好空气套餐（平安财险）
         * 53 好动力套餐（后付费）
         * 54 好空气套餐（奥迪宝马奔驰后付费）
         * 55 发动机舱清洗套餐（淘）
         * 59 发动机机舱清洗套餐（预付费）
         * 62 空调清洗套餐（后付费）
         * 63 空调清洗套餐（免费）
         * 64 好动力套餐（免费）
         * 65 轮觳清洗套餐（预付费）
         * 66 空调清洗（点评到家）
         * 67 汽车检测和细节养护套餐（点评到家）
         * 68 好空气套餐（点评道家）
         * 70 9.8细节养护与检测（微信）
         * 71 空调清洗套餐（预付费）
         * 72 发动机舱除碳（预付费）
         * 73 发动机舱除碳（后付费）
         */
        return array(2, 4, 5, 13, 14, 15, 16, 21, 28, 29, 30, 31, 32, 33, 35, 39, 43, 47, 48, 49, 50, 51, 52, 53, 54, 55, 59, 62, 63, 64, 65, 66, 67, 68, 70, 71, 72, 73);
    }

    /**
     * 查询大客户订单条件
     */
    public function bigCustomerOrderTypes()
    {
        /**
         * order_type
         *
         * 39 安盛天平50元检测套餐（不含机油配件）
         * 40 安盛天平168套餐
         * 41 安盛天平268套餐
         * 42 安盛天平368套餐
         *
         * 43 平安银行50元检测套餐（不含机油配件）
         * 44 平安银行168/199套餐
         * 45 平安银行268/299套餐
         * 46 平安银行368/399套餐
         *
         * 52 好空气套餐（平安财险）
         */
        return array(39, 40, 41, 42, 43, 44, 45, 46, 52);
    }


    /**
     * 补配件免人工条件
     *
     * @return array
     */
    public function fittingOrderCondition()
    {
        $order_type = 34;
        return array(
            '_logic' => 'or',
            'order_type' => $order_type,
            'remark|operator_remark' => array('like', '%补%'),
        );
    }

    /**
     * 获取大客户渠道相关订单信息
     *
     * @param array $orders
     * @param $big_customer_orders_count
     * @param $big_customer_orders_amount
     * @param $big_customer_maintain_orders_count
     * @param $big_customer_maintain_orders_amount
     * @param $big_customer_except_maintain_orders_count
     * @param $big_customer_except_maintain_orders_amount
     * @param $big_customer_fitting_orders_count
     * @param $big_customer_fitting_orders_amount
     */
    public function getBigCustomerOrders($orders = array(), &$orders_count, &$orders_amount, &$maintain_orders_count, &$maintain_orders_amount, &$except_maintain_orders_count, &$except_maintain_orders_amount, &$fitting_orders_count, &$fitting_orders_amount, &$big_customer_orders = array())
    {
        foreach ($orders as $order) {
            //大客户订单判断
            if (strpos($order['operator_remark'], '平安苏州') !== false || strpos($order['operator_remark'], '苏州平安') !== false || strpos($order['operator_remark'], '安盛天平') !== false || strpos($order['operator_remark'], '杭州人保') !== false || strpos($order['operator_remark'], '人保') !== false || strpos($order['operator_remark'], '天安财险') !== false) {
                $orders_count ++;
                $orders_amount += (float)$order['amount'];

                //$big_customer_orders[] = $order;

                //大客户保养订单判断
                if (in_array($order['order_type'], $this->maintainOrderTypes())) {
                    $maintain_orders_count ++;
                    $maintain_orders_amount += (float)$order['amount'];
                } elseif (in_array($order['order_type'], $this->exceptMaintainOrderTypes())) {
                    //大客户非保养订单判断
                    $except_maintain_orders_count ++;
                    $except_maintain_orders_amount += (float)$order['amount'];
                } elseif ($order['order_type'] == 34 || strpos($order['operator_remark'], '补')) {
                    //大客户补差价订单判断
                    $fitting_orders_count ++;
                    $fitting_orders_amount += (float)$order['amount'];
                }


            } else {
                if ($order['replace_code']) {
                    $r = $this->carservicecode->where(array('coupon_code'=>$order['replace_code']))->find();
                    $remark = $r['remark'];

                    if (strpos($remark, '平安苏州') !== false || strpos($remark, '苏州平安') !== false  || strpos($remark, '安盛天平') !== false || strpos($remark, '杭州人保') !== false || strpos($remark, '人保') !== false  || strpos($remark, '天安财险') !== false) {
                        $orders_count ++;
                        $orders_amount += (float)$order['amount'];

                        //$big_customer_orders[] = $order;

                        //大客户保养订单判断
                        if (in_array($order['order_type'], $this->maintainOrderTypes())) {
                            $maintain_orders_count ++;
                            $maintain_orders_amount += (float)$order['amount'];
                        } elseif (in_array($order['order_type'], $this->exceptMaintainOrderTypes())) {
                            //大客户非保养订单判断
                            $except_maintain_orders_count ++;
                            $except_maintain_orders_amount += (float)$order['amount'];
                        } elseif ($order['order_type'] == 34 || strpos($order['operator_remark'], '补')) {
                            //大客户补差价订单判断
                            $fitting_orders_count ++;
                            $fitting_orders_amount += (float)$order['amount'];
                        }

                    }
                }
            }


        }

    }




    /**
     * 获取kv表value
     *
     * @param $k
     * @return array
     */
    public function getV($k)
    {
        $map = array('k' => $k);
        return @unserialize($this->kv->where($map)->getField('v')) ?: array();
    }

    /**
     * 保存到kv表
     *
     * @param $k
     * @param $v
     */
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
}
