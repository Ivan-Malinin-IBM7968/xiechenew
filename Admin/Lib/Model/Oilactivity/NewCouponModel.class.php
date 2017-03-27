<?php

/**
 * NewCouponModel 新版优惠券模型
 *
 * @uses CommonModel
 * @version 2015-08-18
 * @author zxj <tenkanse@hotmail.com>
 */
class NewCouponModel extends CommonModel
{
    /**
     * __construct
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->couponCodeModel = D(GROUP_NAME.'/NewCouponCode');
    }

    /**
     * addOne 增加一种优惠活动
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $filtedFormInput
     * @access public
     * @return void
     */
    public function addOne($filtedFormInput)
    {
        $additionalInfo = array(
            'add_time' => time(),
        );
        $this->arrayToString($filtedFormInput['support_type']);
        $record = array_merge($filtedFormInput, $additionalInfo);
        if (!$couponId = $this->add($record)) {
            throw new \Exception($this->getError());
        }
        $this
            ->couponCodeModel
            ->addCoupon($couponId, $filtedFormInput['coupon_count'], $filtedFormInput['coupon_prefix']);
    }

    /**
     * arrayToString 将简单数组用',' 连接变成字符串
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function arrayToString(&$input)
    {
        if ($input) {
            if (!is_array($input)) {
                $input = (array)$input;
            }
            $input = implode(',', $input);
        }
    }

    /**
     * getList 返回优惠活动列表
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return mixed
     */
    public function getList()
    {
        $count = $this->count();
        $order = 'add_time DESC';

        // 导入分页类
        import("ORG.Util.Page");

        // 实例化分页类
        $p = new Page($count, 10);

        // 分页显示输出
        $page = $p->show_admin();

        // 当前页数据查询

        $list = $this
            ->order($order)
            ->limit($p->firstRow.','.$p->listRows)
            ->select();

        foreach ($list as &$info) {
            $this->formatOne($info);
        }
        return array($page, $list);
    }

    /**
     * getCouponUrl 生成优惠券领取活动链接
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access private
     * @return void
     */
    private function getCouponUrl($id)
    {
        $url = WEB_ROOT.'newcoupon-index-id-'.$id;
        return $url;
    }

    /**
     * getOne 根据主键获取某优惠活动信息
     *
     * @version 2015-08-18
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
        $info['url'] = $this->getCouponUrl($info['id']);
        $info['start_time'] = $this->formatTime($info['start_time']);
        $info['end_time'] = $this->formatTime($info['end_time']);
        $info['add_time'] = $this->formatTime($info['add_time']);
        $info['modify_time'] = $this->formatTime($info['modify_time']);
        $info['coupon_thumb'] = $info['coupon_thumb'] ? WEB_ROOT.$info['coupon_thumb'] : '';
        $info['typeName'] = $info['type'] ? '线下' : '线上';
        $info['statusName'] = $this->getStatus($info['id']);
        $info['qrcode'] = $this->getQrcode($info['id']);
        $this->stringToArray($info['support_type']);
        $info['supportTypeName'] = $this->getSupportTypeName($info['support_type']);
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
        $eventType = C('COUPON_EVENT_TYPE');
        foreach ($supportTypes as &$supportType) {
            $supportType = $eventType[$supportType - 1]['name'];
        }
        return implode('<br/>', $supportTypes);
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
     * getQrcode 获取活动二维码图片
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access private
     * @return string
     */
    private function getQrcode($id)
    {
        include_once("../ThinkPHP/Extend/Library/ORG/Qrcode/qrlib.php");
        $url = $this->getCouponUrl($id);
        $file = 'UPLOADS/Admin/coupon/Qrcode'.$id.'.png';
        \QRcode::png($url, '../'.$file, 'L', 4, 2);
        $imgurl = WEB_ROOT.$file;
        return $imgurl;
    }

    /**
     * getStatus 获取列表显示用的活动状态
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access private
     * @return string
     */
    private function getStatus($id)
    {
        if ($type = $this->getFieldById($id, 'type')) {
            return '线下';
        }
        switch ($status = $this->getFieldById($id, 'status')) {
            case '0':
                $statusName = '未开始';
                break;

            case '1':
                $beginTime = $this->getFieldById($id, 'begin_time');
                $statusName = '开始时间：<br/>'.$this->formatTime($beginTime);
                break;

            case '2':
                $beginTime = $this->getFieldById($id, 'begin_time');
                $stopTime = $this->getFieldById($id, 'stop_time');
                $statusName = '开始时间：<br/>'.$this->formatTime($beginTime)
                    .'<br/>结束时间：<br/>'.$this->formatTime($stopTime);
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
     * editOne 编辑优惠活动
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $filtedFormInput
     * @access public
     * @return void
     */
    public function editOne($filtedFormInput)
    {
        $numberChanged = $filtedFormInput['coupon_count']
            && $filtedFormInput['coupon_count'] != $this->getFieldById($filtedFormInput['id'], 'coupon_count');
        $prefixChanged = $filtedFormInput['coupon_prefix']
            && $filtedFormInput['coupon_prefix'] != $this->getFieldById($filtedFormInput['id'], 'coupon_prefix');
        $additionalInfo = array(
            'modify_time' => time(),
        );
        if ($filtedFormInput['support_type']) {
            $this->arrayToString($filtedFormInput['support_type']);
        }
        $record = array_merge($filtedFormInput, $additionalInfo);
        $this->save($record);
        if ($numberChanged || $prefixChanged) {
            $this->couponCodeModel->deleteCoupon($filtedFormInput['id']);
            $this
                ->couponCodeModel
                ->addCoupon(
                    $filtedFormInput['id'],
                    $filtedFormInput['coupon_count'],
                    $filtedFormInput['coupon_prefix']
                );
        }
    }

    /**
     * deleteCoupon 删除优惠券活动
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access public
     * @return void
     */
    public function deleteCoupon($id)
    {
        $this->couponCodeModel->deleteCoupon($id);
        $this->delete($id);
    }

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
        if ($orderType === 'all') {
            return;
        }
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
}
