<?php
return array(
    'APP_AUTOLOAD_PATH' =>'@ORG.Uitl',
    'URL_MODEL'         =>1,
	'URL_CASE_INSENSITIVE' =>true,
    'APP_GROUP_LIST'    => 'Store,Admin,Shop',
    'DEFAULT_GROUP'     =>'Admin',
    'DB_FIELDTYPE_CHECK'=>true,
    'TMPL_STRIP_SPACE'  => true,
	'DB_TYPE'           => 'mysql',
    'VAR_PAGE'          =>'p',
    'USER_AUTH_ON'       =>true,
    'USER_AUTH_TYPE'	 =>1,                       // 默认认证类型 1 登录认证 2 实时认证
    'RBAC_ROLE_TABLE'    =>'xc_role',
    'RBAC_USER_TABLE'    =>'xc_role_user',
    'RBAC_ACCESS_TABLE'  =>'xc_access',
    'RBAC_NODE_TABLE'    =>'xc_node',
    'USER_AUTH_KEY'		 =>'shopadmin_authId',                // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'	 =>'administrator',
    'USER_AUTH_MODEL'	 =>'User',                  // 默认验证数据表模型
    'AUTH_PWD_ENCODER'	 =>'md5',                   // 用户认证密码加密方式
    'USER_AUTH_GATEWAY'	 =>'/Admin/Public/login',	// 默认认证网关
    'NOT_AUTH_MODULE'	 =>'Public,Attach',                // 默认无需认证模块
    'REQUIRE_AUTH_MODULE'=>'',                      // 默认需要认证模块
    'NOT_AUTH_ACTION'	 =>'',                      // 默认无需认证操作
    'REQUIRE_AUTH_ACTION'=>'',                      // 默认需要认证操作
    'GUEST_AUTH_ON'      =>false,                  // 是否开启游客授权访问
    'GUEST_AUTH_ID'      =>0,                       // 游客的用户ID
    'SHOW_RUN_TIME'      =>false,                    // 运行时间显示
    'SHOW_ADV_TIME'      =>false,                    // 显示详细的运行时间
    'SHOW_DB_TIMES'      =>false,                    // 显示数据库查询和写入次数
    'SHOW_CACHE_TIMES'   =>false,                    // 显示缓存操作次数
    'SHOW_USE_MEM'       =>false,                    // 显示内存开销
    'UPLOAD_ROOT'        =>SITE_ROOT.'UPLOADS',		 //upload root path
    'ATTACH_DOMAIN'		 =>WEB_ROOT.'UPLOADS', //attach domain
    'TMPL_PARSE_STRING' =>array(
    '__PUBLIC__' => WEB_ROOT.'Public/Admin', // 更改默认癿__PUBLIC__ 替换觃则
    '__JS__' => WEB_ROOT.'Public/Admin/Js', // 增加新癿JS 类库路徂替换觃则
    '__CSS__' => WEB_ROOT.'Public/Admin/Css', // 增加新癿css类库路徂替换觃则
    '__IMG__' => WEB_ROOT.'Public/Admin/Images', // 增加新癿image类库路徂替换觃则
    '__UPLOAD__' => WEB_ROOT.'UPLOADS', // 增加新癿上传路徂替换觃则
    ),

	//'shop_salecoupon' => '370,356,57,482,490,1325,1338,298,139,65,1342,1333,246,21,72,218,5,116,73,1339,1341,54,536,542,1345,313,79,9,291,41,25',
);
?>
