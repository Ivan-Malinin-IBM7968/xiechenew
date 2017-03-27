<?php
/*用户保险模型
*ysh
*2013/5/7
*/
class InsuranceModel extends Model {
	/*
	 * 自动验证
	 * 
	 */
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
	/*
	 * 自动填充
	 * 
	 */
	protected $_auto = array ( 
		array('create_time','time',1,'function'), 
	);
}
?>