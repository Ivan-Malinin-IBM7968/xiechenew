<?php

/**
 * NewCouponCodeModel 优惠券生成领取记录表模型
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

        $this->cityModel = D('city');
    }

    /**
     * addCoupon 生成一批优惠券
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @param mixed $num
     * @param mixed $prefix
     * @access public
     * @return void
     */
    public function addCoupon($couponId, $num, $prefix)
    {
        for ($i = 1; $i <= $num; $i++) {
            $randomCode = rand(123456789, 999999999);
            $couponCode = $prefix.$randomCode;
            $data = array(
                'coupon_code' => $couponCode,
                'add_time' => time(),
                'coupon_id' => $couponId,
            );
            if (!$this->add($data)) {
                $i--;
            }
        }
    }

    /**
     * deleteCoupon 删除优惠券
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return void
     */
    public function deleteCoupon($couponId)
    {
        $where = array(
            'coupon_id' => $couponId,
        );
        $this->where($where)->delete();
    }

    /**
     * deleteFilter 后台删除过滤
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return void
     */
    public function deleteFilter($couponId)
    {
        $where = array(
            'coupon_id' => $couponId,
            'status' => array('neq', 0),//已使用或已领取
        );
        $hasUsed = $this->where($where)->find();
        if ($hasUsed) {
            throw new \Exception('有已经发出的优惠券，无法删除');
        }
    }

    /**
     * getList 获取优惠券领取使用详情列表
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return mixed
     */
    public function getList($condition)
    {
        $where = array(
            'coupon_id' => $condition['id'],
            'coupon_code' => array('like', '%'.$condition['coupon_code'].'%'),
        );
        if ($condition['status'] !== '' && $condition['status'] !== null) {
            $where['status'] = $condition['status'];
        }
        $order = 'get_time DESC';

        $count = $this->where($where)->count();

        // 导入分页类
        import("ORG.Util.Page");

        // 实例化分页类
        $p = new Page($count, 10);

        // 分页显示输出
        $page = $p->show_admin();

        // 当前页数据查询
        $list = $this
            ->where($where)
            ->order($order)
            ->limit($p->firstRow.','.$p->listRows)
            ->select();

        foreach ($list as &$info) {
            $info['cityName'] = $this->cityModel->getFieldById($info['city_id'], 'name');
            $info['statusName'] = $this->getStatusName($info['status']);
            $info['get_time'] = $this->formatTime($info['get_time']);
            $info['use_time'] = $this->formatTime($info['use_time']);
        }
        return array($page, $list);
    }

    /**
     * getStatusName 获取状态名称
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $status
     * @access private
     * @return string
     */
    private function getStatusName($status)
    {
        switch ($status) {
            case '0':
                $statusName = '未领取';
                break;

            case '1':
                $statusName = '已领取';
                break;
            
            case '2':
                $statusName = '已使用';
                break;

            default:
                $statusName = '';
                break;
        }
        return $statusName;
    }

    /**
     * formatTime 将timestamp格式化为2015-08-12这样的时间
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $timestamp
     * @access private
     * @return string
     */
    private function formatTime($timestamp)
    {
        if (!$timestamp) {
            return;
        }
        $dateObj = new DateTime();
        $dateObj->setTimestamp($timestamp);
        return $dateObj->format('Y-m-d');
    }

    /**
     * getStatistics 获取优惠券统计信息
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return mixed
     */
    public function getStatistics($couponId)
    {
        $totalCount = $this->getCountByStatus($couponId);
        $giveCount = $this->getCountByStatus($couponId, '1');//已领取
        $useCount = $this->getCountByStatus($couponId, '2');//已使用
        $data = array(
            'giveRate' => $this->formatPercentage(($giveCount + $useCount)/$totalCount),
            'useRate' => $this->formatPercentage($useCount/$totalCount),
        );
        return $data;
    }

    /**
     * formatPercentage 将数字格式化为百分数形式
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $number
     * @access private
     * @return string
     */
    private function formatPercentage($number)
    {
        $number = number_format($number, 4, '.', '');
        return $number * 100 . '%';
    }

    /**
     * getCountByStatus 根据某种优惠券使用状态获取其数目
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @param mixed $status
     * @access private
     * @return int
     */
    private function getCountByStatus($couponId, $status = null)
    {
        $where = array(
            'coupon_id' => $couponId,
        );
        if ($status) {
            $where['status'] = $status;
        }
        return $this->where($where)->count();
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
            $this->couponModel = D('Oilactivity/NewCoupon');
            $this->couponModel->filterOrderType($record['coupon_id'], $orderType);
            $this->couponModel->filterActiveTime($record['coupon_id']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        $this->couponModel = D('Oilactivity/NewCoupon');
        $amount = $this->couponModel->getFieldById($couponId, 'coupon_amount');
        return $amount;
    }

    /**
     * getOrderType 根据优惠码获取订单类型
     *
     * @modify_time 2015-09-08
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return int
     */
    public function getOrderType($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        $couponId = $this->where($where)->getField('coupon_id');
        $this->couponModel = D('Oilactivity/NewCoupon');
        $supportType = $this->couponModel->getFieldById($couponId, 'support_type');

        $eventType = C('COUPON_EVENT_TYPE');
        $orderType = $eventType[$supportType - 1]['ordertype'];
        return $orderType;
    }

    /**
     * getAmount 获取套餐价格
     *
     * @modify_time 2015-09-23
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return double
     */
    public function getAmount($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        $couponId = $this->where($where)->getField('coupon_id');
        $this->couponModel = D('Oilactivity/NewCoupon');
        $supportType = $this->couponModel->getFieldById($couponId, 'support_type');

        $eventType = C('COUPON_EVENT_TYPE');
        $amount = $eventType[$supportType - 1]['amount'];
        return $amount;
    }

    /**
     * getCouponName 根据优惠码获得优惠活动名称
     *
     * @modify_time 2015-09-10
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponCode
     * @access public
     * @return string
     */
    public function getCouponName($couponCode)
    {
        $where = array(
            'coupon_code' => $couponCode,
        );
        $couponId = $this->where($where)->getField('coupon_id');
        $this->couponModel = D('Oilactivity/NewCoupon');
        return $this->couponModel->getFieldById($couponId, 'coupon_name');
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

    /**
     * getCouponCodeList 获取某活动优惠券码列表
     *
     * @modify_time 2015-09-08
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return mixed
     */
    public function getCouponCodeList($couponId)
    {
        $where = array(
            'coupon_id' => $couponId,
            'status' => 0,//未领取
        );
        $field = array(
            'coupon_code',
        );
        $list = $this->where($where)->field($field)->select();
        foreach ($list as $item) {
            $codeList[] = $item['coupon_code'];
        }
        return $codeList;
    }
}
