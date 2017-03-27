<?php

/**
 * NewCouponCodeModel 优惠券生成领取表模型
 *
 * @uses CommonModel
 * @modify_time 2015-08-21
 * @author zxj <tenkanse@hotmail.com>
 */
class NewCouponCodeModel extends CommonModel
{
    /**
     * __construct
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->couponModel = D('NewCoupon');
    }

    /**
     * giveOneCoupon 用户填信息领取一张优惠券
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $data
     * @access public
     * @return string
     */
    public function giveOneCoupon($data)
    {
        $where = array(
            'coupon_id' => $data['coupon_id'],
            'status' => '0',
        );
        $data['id'] = $this->where($where)->getField('id');
        if (!$data['id']) {
            throw new \Exception('优惠券已经领完');
        }
        $data['status'] = 1;//已领取
        $data['get_time'] = time();//记录领取时间
        $this->save($data);

        $couponCode = $this->getFieldById($data['id'], 'coupon_code');
        return $this->couponModel->getCouponMessage($data['coupon_id'], $couponCode); 
    }

    /**
     * isValidNewCouponCode 验证是否为可用的新版优惠码
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @param mixed $orderType
     * @access public
     * @return boolean
     */
    public function isValidNewCouponCode($couponCode, $orderType)
    {
        try {
            $where = array(
                'coupon_code' => $couponCode,
                'status' => array('neq', 2),//非已使用
            );
            if (!$record = $this->where($where)->find()) {
                throw new \Exception('该优惠券不存在或已经使用');
            }
            $this->couponModel = D('NewCoupon');
            $this->couponModel->filterOrderType($record['coupon_id'], $orderType);
            $this->couponModel->filterActiveTime($record['coupon_id']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * getUserTypeCouponList 获取用户某活动有效优惠券列表
     *
     * @modify_time 2015-08-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $paId
     * @param mixed $orderType
     * @access public
     * @return mixed
     */
    public function getUserTypeCouponList($paId, $orderType)
    {
        $list = $this->getUserCouponList($paId);
        foreach ($list as &$coupon) {//筛选支持的订单类型
            $this->stringToArray($coupon['support_type']);
            if (in_array($orderType, $coupon['support_type'])) {
                $couponList[] = $coupon;
            }
        }
        /*
        $couponList = array(
            array(
                'coupon_name' => 'aaaa',
                'coupon_amount' => 10,
                'coupon_code' => 'ba836603822',
            ),
        );
         */
        return $couponList;
    }

    /**
     * getCouponCodeDetail 获取我的优惠券－优惠券使用详情
     *
     * @modify_time 2015-08-31
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCodeId
     * @access public
     * @return mixed
     */
    public function getCouponCodeDetail($couponCodeId)
    {
        /*
        $detail = array(
            'coupon_summary' => '',//使用说明第二条
            'active_time' => '',//活动时间全部
            'coupon_detail_img' => '',//活动详情图
            'service_area' => '',//服务范围
            'order_url' => '',//下单url
        );
         */
        $couponId = $this->getFieldById($couponCodeId, 'coupon_id');
        $_SESSION['new_coupon_id'] = $couponId;
        $_SESSION['new_coupon_code'] = $this->getFieldById($couponCodeId, 'coupon_code');
        $_SESSION['from_my_coupon'] = 1;
        return $this->couponModel->getOne($couponId);
    }
    
    /**
     * getUserCouponList 获取用户有效优惠券列表
     *
     * @modify_time 2015-08-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $paId
     * @access public
     * @return void
     */
    public function getUserCouponList($paId)
    {
        $where = array(
            'a.pa_id' => $paId,
            'a.status' => '1',
            'b.end_time' => array('gt', time() - 15 * 24 * 3600),//有效期过期15天内的所有券
        );
        $join = '__NEW_COUPON__ b ON a.coupon_id = b.id';
        $field = array(
            'a.coupon_id',
            'b.support_type',
            'b.end_time',
            'a.id' => 'coupon_code_id',
        );
        $order = 'b.end_time DESC';
        $list = $this
            ->alias('a')
            ->where($where)
            ->join($join)
            ->field($field)
            ->order($order)
            ->select();
        foreach ($list as &$coupon) {
            //是否为保养服务，保养服务则显示抵扣价，基础服务显示实际支付价格(惠享价)。未考虑支持服务多选情况
            $coupon['is_baoyang'] = ($coupon['support_type'] === '1') ? 1 : 0;
            $coupon['is_expired'] = ($coupon['end_time'] < time()) ? 1 : 0;

            $coupon['coupon_amount'] = $this->couponModel->getUserCouponAmount($coupon['coupon_id']);
            $this->stringToArray($coupon['support_type']);
            $coupon['support_type_name'] = $this->getSupportTypeName($coupon['support_type']);
            $coupon['active_time'] = $this->couponModel->getUserActiveTime($coupon['coupon_id']);
        }
        return $list;
    }

    /**
     * isBaoyang 根据优惠码判断优惠活动类型是否为保养
     *
     * @modify_time 2015-09-02
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return boolean
     */
    public function isBaoyang($couponCode)
    {
        $couponId = $this->getFieldByCouponCode($couponCode, 'couponId');
        $this->couponModel = D('NewCoupon');
        $supportType = $this->couponModel->getFieldById($supportType);
        if ($supportType == 1) {
            return true;
        }
        return false;
    }

    /**
     * getSupportTypeName 获取服务套餐名称
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $supportTypes
     * @access private
     * @return string
     */
    private function getSupportTypeName($supportTypes)
    {
        $this->couponConfig = include('./Admin/Conf/coupon.php');
        $eventType = $this->couponConfig['COUPON_EVENT_TYPE'];
        foreach ($supportTypes as &$supportType) {
            $supportType = $eventType[$supportType - 1]['name'];
        }
        return $supportTypes[0];
    }

    /**
     * stringToArray 将','连接的字符串变回数组
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function stringToArray(&$input)
    {
        $input = explode(',', $input);
    }

    /**
     * isNewCouponCode 是否为新版优惠券
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return void
     */
    public function isNewCouponCode($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        if ($this->where($where)->find()) {
            return true;
        }
        return false;
    }

    /**
     * getDiscountAmount 获得有效优惠券的优惠金额
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return double
     */
    public function getDiscountAmount($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        $couponId = $this->where($where)->getField('coupon_id');
        $this->couponModel = D('NewCoupon');
        $amount = $this->couponModel->getFieldById($couponId, 'coupon_amount');
        return $amount;
    }

    /**
     * useNewCoupon 使用新版优惠券
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function useNewCoupon($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        $data = array(
            'status' => 2,//已使用
            'use_time' => time(),
        );
        $this->where($where)->save($data);
        unset($_SESSION['new_coupon_id']);
        unset($_SESSION['new_coupon_code']);
        unset($_SESSION['from_my_coupon']);
    }
}
