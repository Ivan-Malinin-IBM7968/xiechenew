<?php
return array(
	'URL_DISPATCH_ON'    =>1,
    'AUTO_BUILD_HTML'    =>0,
    //'SHOW_PAGE_TRACE'    =>1,
    'LIKE_MATCH_FIELDS'  =>'name|title|remark',
    'TAG_NESTED_LEVEL'   =>3,
    'UPLOAD_FILE_RULE'	 =>'uniqid',                //  文件上传命名规则 例如 time uniqid com_create_guid 等 支持自定义函数 仅适用于内置的UploadFile类
    'DB_HOST'           => 'localhost',
    'DB_USER'           =>'root',
    'DB_PWD'            =>'123456',
    'DB_NAME'           =>'tp_bank_admin',
    'DB_PORT'           =>'3306',
    'DB_PREFIX'         =>'xc_',
    'DB_CONFIG1'        =>'mysql://root:123456@localhost/tp_xieche',
);
?>
