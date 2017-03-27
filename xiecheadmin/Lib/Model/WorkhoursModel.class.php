<?php
// 工时模型
class WorkhoursModel extends CommonModel {
	
		//自动验证
		protected $_validate = array(
		/*
		array('u_c_id','require','名称必须'),
		//array('odometer','require','公里数必须'),
		array('total_cost','check_total_cost','总费用必须',1,'function'),
		array('odometer','check_odometer','公里数必须或填写的数值错误',0,'callback'),
		*/
		);
		//自动完成
		protected $_auto = array ( 
			array('add_time','time',3,'function'),
		);
	
}
		?>