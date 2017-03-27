<?php
//xieche 库member模型
class XiecheMemberModel extends Model {
	protected $tableName = 'member';
	protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';

	protected $_validate = array(
		//array('username','require','用户名必须'),
		//array('password','require','密码必须！'),
		//array('repassword','require','确认密码必须！'),
		
		//array('repassword','password','确认密码不正确',0,'confirm'),
		//array('email','email','邮箱错误！'),
		//array('mobile','require','手机号必须！'),
		
		//array('username','','帐号名称已经存在！',0,'unique',1),
		//array('email','','邮箱已经存在！',0,'unique',1),
		//array('mobile','','手机号已经存在！',0,'unique',1),

	);
	protected $_auto = array ( 
		array('password','pwdHash',self::MODEL_BOTH,'callback') , 
		array('reg_time','time',self::MODEL_INSERT,'function'), // 对create_time字段在更新的时候写入当前时间戳
		array('ip','get_client_ip',self::MODEL_INSERT,'function'), // 对create_time字段在更新的时候写入当前时间戳
	);

	/*
	* hash加密
	* 
	*/
	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return pwdHash($_POST['password']);
		}else{
			return false;
		}
	}

}
?>