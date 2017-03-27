<?php
class TmallapiAction extends Action
{
    /**
     * __construct
     *
     * @modify_time 2015-09-30
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        header("Content-type: text/html; charset=utf-8");
        $this->api = D('Taobao/Tmallapi');
    }

    public function index()
    {
        try {
            $params = array(
                'OrderId' => 0,
            );
            $this->api->setName('StoreGet');
            $this->api->setParams($params);
            $resp = $this->api->render();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function tmallNotify()
    {
        try {
            $secret = $this->api->config['notifySecret'];
            $merchantId = $this->api->config['merchantId'];

            $tmallMessage = file_get_contents('php://input');
            
            //Log::write($tmallMessage, '', '', 'Runtime/Logs/tmall.log');

            $this->parseMessage($tmallMessage);
            $this->tmallAjaxReturn(200);
        } catch (\Exception $e) {
            $this->tmallAjaxReturn(500);
        }
    }

    private function parseMessage(&$message)
    {
        if (!$this->verifySign($message)) {
            throw new \Exception('验签失败');
        }
    }

    private function verifySign($message)
    {
        $message = 'valid_ends=2015-04-30 23:59:59, outer_iid=23, item_title=八大处游乐玩套票, taobao_sid=244122, order_id=92555987, sku_properties=出发月份:3月;出发日期:任意1天;门票类别:优惠票;酒店房型:公寓, timestamp=2015-03-24 14:39:46, send_type=2, consume_type=0, num=1, valid_start=2015-03-23 00:00:00, token=e05a6fc4082c518e2a222e145b10aebf, sub_method=1, method=send, sms_template=验证码$code.您已成功订购银科环企提供的八大处游乐玩套票,有效期2015/03/23至2015/04/30,消费时请出示本短信以验证.如有疑问,请联系卖家., num_iid=442320, seller_nick=测试专用, mobile=185747533, sub_outer_iid=23';
        $message = explode(', ', $message);
        var_dump($message);exit;

        $this->filter($message);//过滤空参数和sign本身
        $this->sortAsc($message);
        $this->concat($message);
        $this->prefixSecret($message);
        $this->md5($message);//计算前确认是GBK编码
        $this->toUpperCase($message);
        $this->checkSignEqual();
    }

    private function tmallAjaxReturn($code)
    {
        $data = array(
            'code' => $code,
        );
        $message = json_encode($data);
        echo $message;
    }
}
