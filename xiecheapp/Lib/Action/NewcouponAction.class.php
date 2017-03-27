<?php

/**
 * NewcouponAction 自动发券活动前台控制器
 *
 * @uses CommonAction
 * @modify_time 2015-08-20
 * @author zxj <tenkanse@hotmail.com>
 */
class NewcouponAction extends CommonAction
{
    /**
     * __construct
     *
     * @modify_time 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

        $this->openid = $this->wxGetOpenid();

        $this->PadataModel = D('Padatatest');//测试接收微信订单数据表
        $this->couponModel = D('NewCoupon');
        $this->couponCodeModel = D('NewCouponCode');
        $this->cityModel = D('city');
    }

    /**
     * test 测试用
     *
     * @modify_time 2015-08-28
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function test()
    {
        echo $this->openid;
        echo "<br/>";
        echo $this->getPaId();
    }

    /**
     * user 我的优惠券页面
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function user()
    {
        $couponList = $this->couponCodeModel->getUserCouponList($this->getPaId());
        //$couponList = $this->couponCodeModel->getUserCouponList(8888);
        $this->assign('couponList', $couponList);
        $this->display();
    }

    /**
     * detail 我的优惠券－优惠券使用详情页面
     *
     * @modify_time 2015-08-31
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function detail()
    {
        $couponCodeId = $this->inputFilter($_REQUEST['id']);
        $this->existFilter($couponCodeId);
        $detail = $this->couponCodeModel->getCouponCodeDetail($couponCodeId);
        $this->assign('detail', $detail);
        $this->display();
    }

    /**
     * getPaId 获取当前padatatest表的ID
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return string
     */
    private function getPaId()
    {
        $where = array(
            'FromUserName' => $this->openid,
            'type' => 2,//携车
        );
        if (!$info = $this->PadataModel->where($where)->find()) {
            $data = $where;
            $data['create_time'] = time();
            if (!$paId = $this->PadataModel->add($data)) {
                throw new \Exception('pa_id未添加成功');
            }
            return $paId;
        }
        return $info['id'];
    }

    /**
     * wxGetOpenid 获取openid
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function wxGetOpenid()
    {
        $this->appid = 'wx43430f4b6f59ed33';
        $this->secret = 'e5f5c13709aa0ae7dad85865768855d6';

        if (!$openid = session('openid')) {
            if (!$_GET['code']) {
                $this->wxGetCode();
            }
            $data = array(
                'appid' => $this->appid,
                'secret' => $this->secret,
                'code' => $_GET['code'],
                'grant_type' => 'authorization_code',//固定
            );
            $base = 'https://api.weixin.qq.com/sns/oauth2/access_token';
            $url = $this->parseURL($base, $data);
            $result = json_decode($this->curlGetContent($url), true);
            if (!$result['openid']) {
                throw new \Exception('openid获取失败');
            }
            session('openid', $result['openid']);
            return $result['openid'];
        }
        return $openid;
    }

    /**
     * parseURL 拼接url
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $base
     * @param mixed $data
     * @access private
     * @return string
     */
    private function parseURL($base, $data)
    {
        foreach ($data as $key => $value) {
            $segment[] = $key.'='.$value;
        }
        $url = $base.'?'.implode('&', $segment);
        return $url;
    }

    /**
     * curlGetContent 简单的curl拿信息
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $url
     * @access private
     * @return mixed
     */
    private function curlGetContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    /**
     * wxGetCode 获取openid第一步，拿code
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function wxGetCode()
    {
        $data = array(
            'appid' => $this->appid,
            'redirect_uri' => urlencode(WEB_ROOT.$_SERVER['REQUEST_URI']),
            'scope' => 'snsapi_base',
            'response_type' => 'code',//固定
            'state' => 1,//无用
        );
        $base = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        header("location:".$this->parseURL($base, $data));
        exit;
    }

    /**
     * index 优惠券发放活动首页
     *
     * @modify_time 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function index()
    {
        try {
            $id = $this->inputFilter($_REQUEST['id']);

            $this->existFilter($id);
            $this->typeFilter($id);
            $this->statusFilter($id, '1');//已开始的活动才能领取
            $this->assign('info', $this->couponModel->getOne($id));
            $this->assign('city_info', $this->getCityList());
            $this->display();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * existFilter 检查参数是否存在
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function existFilter($input)
    {
        if (!$input) {
            throw new \Exception('未指明参数');
        }
    }

    /**
     * getCoupon 用户领取优惠券
     *
     * @modify_time 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function getCoupon()
    {
        try {
            $data = array(
                'coupon_id' => $this->inputFilter($_REQUEST['coupon_id']),
                'mobile' => $this->inputFilter($_REQUEST['mobile']),
                'city_id' => $this->inputFilter($_REQUEST['city_id']),
                'pa_id' => $this->getPaId(),
            );
            $this->typeFilter($data['coupon_id']);
            $this->statusFilter($data['coupon_id'], '1');//已开始的活动才能领取
            $this->mobileFilter($data);
            $this->paIdFilter($data);

            $message = $this->couponCodeModel->giveOneCoupon($data);
            //$this->sendSMS($data['mobile'], $message);
            $this->ajaxReturn(1, $message);
        } catch (\Exception $e) {
            $this->ajaxReturn(0, $e->getMessage());
        }
    }

    /**
     * paIdFilter 过滤已经领过券的微信号
     *
     * @modify_time 2015-08-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $data
     * @access private
     * @return void
     */
    private function paIdFilter($data)
    {
        $where = array(
            'pa_id' => $data['pa_id'],
            'coupon_id' => $data['coupon_id'],
        );
        if ($this->couponCodeModel->where($where)->count()) {
            throw new \Exception('你已经领过优惠券');
        }
    }

    /**
     * sendSMS 调用发短信接口
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $mobile
     * @param mixed $content
     * @access private
     * @return void
     */
    private function sendSMS($mobile, $content)
    {
        $sendData = array(
            'phones' => $mobile,
            'content'=> $content,
        );
        $this->curl_sms($sendData);
    }

    /**
     * typeFilter 过滤线下发放的优惠券
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $couponId
     * @access private
     * @return void
     */
    private function typeFilter($couponId)
    {
        if ($this->couponModel->getFieldById($couponId, 'type') === '1') {// 线下
            throw new \Exception('该类优惠券不支持线上领取');
        }
    }

    /**
     * mobileFilter 手机号相关验证
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $data
     * @access private
     * @return void
     */
    private function mobileFilter($data)
    {
        $this->checkMobileFormat($data['mobile']);
        $where = array(
            'mobile' => $data['mobile'],
            'coupon_id' => $data['coupon_id'],
        );
        if ($this->couponCodeModel->where($where)->count()) {
            throw new \Exception('你已经领过优惠券');
        }
    }

    /**
     * checkMobileFormat 验证手机号格式
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $mobile
     * @access private
     * @return void
     */
    private function checkMobileFormat($mobile)
    {
        $exp = "/^1[3-9]\d{9}$/";
        if (!preg_match($exp, $mobile)) {
            throw new \Exception('手机号格式错误');
        }
    }

    /**
     * statusFilter 过滤不符合的状态设置
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @param mixed $status
     * @access private
     * @return void
     */
    private function statusFilter($id, $status)
    {
        if ($this->couponModel->getFieldById($id, 'status') !== $status) {
            throw new \Exception('活动状态不符合');
        }
    }

    /**
     * getCityList 获取城市列表
     *
     * @modify_time 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return mixed
     */
    private function getCityList()
    {
        $where['is_show'] = 1;
        return $this->cityModel->where($where)->select();
    }

    /**
     * inputFilter 输入基本过滤
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return mixed
     */
    private function inputFilter($input)
    {
        return $input;
    }
}
