<?php
namespace Xiecheapp\Model;

/**
 * NewCouponModel 优惠券批次模型
 *
 * @uses CommonModel
 * @modify_time 2015-08-20
 * @author zxj <tenkanse@hotmail.com>
 */
class NewCouponModel extends CommonModel
{
    /**
     * filterOrderType 过滤不符合的订单类型
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @param mixed $orderType
     * @access public
     * @return void
     */
    public function filterOrderType($couponId, $orderType)
    {
        if (!$orderType) {
            $orderType = 1;
        }
        $this->couponConfig = include('./Admin/Conf/coupon.php');
        $eventType = $this->couponConfig['COUPON_EVENT_TYPE'];
        $supportType = $this->getFieldById($couponId, 'support_type');
        $this->stringToArray($supportType);
        foreach ($supportType as $supportTypeId) {
            $supportOrderTypeArray[] = $eventType[$supportTypeId - 1]['ordertype'];
        }
        if (!in_array($orderType, $supportOrderTypeArray)) {
            throw new \Exception('该优惠券不支持这种订单类型');
        }
    }

    /**
     * filterActiveTime 过滤不在有效期内的优惠券
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return void
     */
    public function filterActiveTime($couponId)
    {
        $startTime = $this->getFieldById($couponId, 'start_time');
        $endTime = $this->getFieldById($couponId, 'end_time');
        $nowTime = time();
        if ($nowTime < $startTime || $nowTime > $endTime) {
            throw new \Exception('该优惠券已经过期');
        }
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
     * getUserActiveTime 获取我的优惠券页面有效期
     *
     * @modify_time 2015-09-02
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return string
     */
    public function getUserActiveTime($couponId)
    {
        $endTime = $this->getFieldById($couponId, 'end_time');
        return $this->formatTime($endTime).'止';
    }

    /**
     * getUserCouponAmount 获取我的优惠券的惠享价或抵扣金额
     *
     * @modify_time 2015-09-02
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access public
     * @return float
     */
    public function getUserCouponAmount($couponId)
    {
        $where = array(
            'id' => $couponId,
        );
        $info = $this->where($where)->find();
        $supportType = $info['support_type'];
        $couponAmount = $info['coupon_amount'];

        if ($supportType === '1') {
            return $couponAmount;
        }
        $this->couponConfig = include('./Admin/Conf/coupon.php');
        $eventType = $this->couponConfig['COUPON_EVENT_TYPE'];
        $totalAmount = $eventType[$supportType - 1]['amount'];
        return $totalAmount - $couponAmount;
    }

    /**
     * getOne 根据主键获取某优惠活动信息
     *
     * @version 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access public
     * @return mixed
     */
    public function getOne($id)
    {
        $where = array(
            'id' => $id,
        );
        $info = $this->where($where)->find(); 
        $this->formatOne($info);
        return $info;
    }

    /**
     * formatOne 格式化数据库数据，方便展现
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $info
     * @access private
     * @return void
     */
    private function formatOne(&$info)
    {
        $info['coupon_thumb'] = $info['coupon_thumb'] ? WEB_ROOT.$info['coupon_thumb'] : '';
        $info['coupon_title'] = $info['coupon_title'] ? : '';
        $info['coupon_subtitle'] = $info['coupon_subtitle'] ? : '';

        $this->stringToArray($info['support_type']);
        $info['order_url'] = $this->getOrderUrl($info['support_type']);
        $info['active_time'] = 
            $this->formatTime($info['start_time'])
            .'至'
            .$this->formatTime($info['end_time']);
        $info['coupon_detail_img'] = $info['coupon_detail_img'] ? $info['coupon_detail_img'] : '';
    }

    /**
     * getOrderUrl 获取用户下单url
     *
     * @modify_time 2015-08-31
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $supportTypes
     * @access private
     * @return void
     */
    private function getOrderUrl($supportTypes)
    {
        foreach ($supportTypes as $supportType) {
            if ($supportType == 1) {
                $url = WEB_ROOT.'/mobilecar-carservice.html?from=xiechewx';
            } else {
                $url = WEB_ROOT.'/mobilecar-carservice_base-ordertype-'.$this->getUrlOrderType($supportType);
            }
            $orderUrl[] = $url;
        }
        return $orderUrl[0];
    }

    /**
     * getUrlOrderType 获得下单链接ordertype
     *
     * @modify_time 2015-08-31
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $supportType
     * @access private
     * @return string
     */
    private function getUrlOrderType($supportType)
    {
        $this->couponConfig = include('./Admin/Conf/coupon.php');
        $eventType = $this->couponConfig['COUPON_EVENT_TYPE'];
        return $eventType[$supportType - 1]['urlOrderType'];
    }

    /**
     * getCouponMessage 生成用户领取优惠码后的短信消息
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @param mixed $couponCode
     * @access public
     * @return string
     */
    public function getCouponMessage($id, $couponCode)
    {
        $where = array(
            'id' => $id,
        );
        $info = $this->where($where)->find();
        $message = '优惠券领取成功，请关注"携车网"公众号，并至"我的优惠券"查看使用。';
        return $message;
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
        return $dateObj->format('Y年m月d日');
    }
}
