<?php
/**
 * ApibaseModel 淘宝API调用基类
 *
 * @uses CommonModel
 * @modify_time 2015-09-30
 * @author zxj <tenkanse@hotmail.com>
 */
class ApibaseModel extends CommonModel
{
    /**
     * dbName
     *
     * @var string
     * @access protected
     */
    protected $dbName = 'tp_xieche';

    /**
     * tablePrefix
     *
     * @var string
     * @access protected
     */
    protected $tablePrefix = 'xc_';

    /**
     * tableName
     *
     * @var string
     * @access protected
     */
    protected $tableName = 'taobao_token';

    /**
     * category API类别
     * 
     * @var string
     * @access protected
     */
    protected $category = '';

    /**
     * apiId 用于区别淘宝或天猫
     * 
     * @var string
     * @access protected
     */
    protected $apiId = '';

    /**
     * __construct
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $name
     * @param mixed $params
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Vendor('TaobaoSDK.TopSdk');
    }

    /**
     * setName 设定接口名称
     *
     * @modify_time 2015-09-22
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $name
     * @access public
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * setParams 设定接口参数
     *
     * @modify_time 2015-09-22
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $params
     * @access public
     * @return void
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * exec 接口请求实行
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access protected
     * @return mixed
     */
    protected function exec()
    {
        if (!$this->name || !$this->params) {
            throw new \Exception('请指定接口名称或参数');
        }
        $requestObjectName = "{$this->category}{$this->name}Request";
        $req = new $requestObjectName;
        foreach ($this->params as $key => $value) {
            $setAction = "set{$key}";
            $req->$setAction($value);
        }
        return $this->c->execute($req, $this->sessionKey);
    }

    /**
     * request 调用淘宝接口
     *
     * @modify_time 2015-09-22
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function request()
    {
        $resp = $this->exec();
        if ($resp->code) {
            $errorMsg = 'code:'
                .$resp->code
                .'msg:'
                .$resp->msg
                .'sub_code:'
                .$resp->sub_code
                .'sub_msg:'
                .$resp->sub_msg;
            throw new \Exception($errorMsg);
        }
        return $resp;
    }

    /**
     * getCode 获取token第一步，拿code
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access protected
     * @return void
     */
    protected function getCode()
    {
        if (!$code = $_REQUEST['code']) {
            $authUrl = $this->config['codeUrl']
                ."?response_type=code&client_id={$this->c->appkey}&redirect_uri="
                .urlencode($this->config['redirectUrl']);
            header("Location: $authUrl");
            exit;
        }
        return $code;
    }

    /**
     * getAccessToken 获取token
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access protected
     * @return void
     */
    protected function getAccessToken()
    {
        $post = array(
            'code' => $this->getCode(),
            'grant_type' => 'authorization_code',
            'client_id' => $this->c->appkey,
            'client_secret' => $this->c->secretKey,
            'redirect_uri' => urlencode($this->config['redirectUrl']),
        );

        $result = $this->c->curl($this->config['accessTokenUrl'], $post);
        $result = json_decode($result, true);
        $this->updateToken($result['access_token'], $result['refresh_token']);
    }

    /**
     * refreshAccessToken 用refresh_token刷新
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access protected
     * @return void
     */
    protected function refreshAccessToken()
    {
        $post = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->c->appkey,
            'client_secret' => $this->c->secretKey,
            'refresh_token' => $this->getFieldById($this->apiId, 'refresh_token'),
            'redirect_uri' => urlencode($this->config['redirectUrl']),
        );
        $result = $this->c->curl($this->config['accessTokenUrl'], $post);
        $result = json_decode($result, true);
        $this->updateToken($result['access_token'], $result['refresh_token']);
    }

    /**
     * updateToken 数据库操作，更新token
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $accessToken
     * @param mixed $refreshToken
     * @access protected
     * @return void
     */
    protected function updateToken($accessToken, $refreshToken)
    {
        $data = array(
            'id' => $this->apiId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'update_time' => time(),
        );
        $this->save($data);
        $this->sessionKey = $this->getFieldById($this->apiId, 'access_token');
    }

    /**
     * auth 获取sessionKey
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $checkSessionKey
     * @access protected
     * @return void
     */
    protected function auth($checkSessionKey = null)
    {
        try {
            $updateTime = $this->getFieldById($this->apiId, 'update_time');
            $expired = (time() - $updateTime) > $this->config['refreshTime'];
            if ($expired) {
                $this->refreshAccessToken();
            } else {
                $this->sessionKey = $this->getFieldById($this->apiId, 'access_token');
            }
            if ($checkSessionKey) {
                $this->checkSessionKeyValidity();
            }
        } catch (\Exception $e) {
            $this->getAccessToken();
        }
    }
}
