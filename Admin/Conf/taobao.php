<?php
//淘宝接口相关配置
return array(
    
    //当前API使用环境
    'TAOBAO_API_ENV' => 'TAOBAO_API_SANDBOX',

    //沙箱测试环境
    'TAOBAO_API_SANDBOX' => array(
        'redirectUrl' => 'http://www.xieche.dev/Admin/index.php/Admin/Taobaoapi',
        'codeUrl' => 'https://oauth.tbsandbox.com/authorize',
        'accessTokenUrl' => 'https://oauth.tbsandbox.com/token',
        'gatewayUrl' => 'http://gw.api.tbsandbox.com/router/rest',
        'appkey' => '1023238733',
        'secretKey' => 'sandbox3cab30d33788e427706a90889',
        'refreshTime' => '3600',
    ),

    //正式环境
    'TAOBAO_API_REAL' => array(
        'redirectUrl' => 'http://www.xieche.com.cn/Admin/index.php/Admin/Taobaoapi',
        'codeUrl' => 'https://oauth.taobao.com/authorize',
        'accessTokenUrl' => 'https://oauth.taobao.com/token',
        'gatewayUrl' => 'http://gw.api.taobao.com/router/rest',
        'appkey' => '23238733',
        'secretKey' => 'f3b12ab3cab30d33788e427706a90889',
        'refreshTime' => '2592000',//一个月刷一次access_token
    ),

    //天猫环境
    'TMALL_API' => array(
        'redirectUrl' => 'http://www.xieche.dev/Admin/index.php/Admin/Tmallapi',
        'codeUrl' => 'https://oauth.taobao.com/authorize',
        'accessTokenUrl' => 'https://oauth.taobao.com/token',
        'gatewayUrl' => 'http://gw.api.taobao.com/router/rest',
        'appkey' => '23245015',
        'secretKey' => 'ab59020f7ceeb57eed4497f467ec2eab',
        'refreshTime' => '2592000',//一个月刷一次access_token

        'notifySecret' => '138b4318d51ae042ce54a1056f8c204e',//通知密钥
        'merchantId' => '2580729598',//码商ID
    ),
);
