<?php
//提问
namespace Xiecheapp\Controller;

class AskController extends CommonController {
	function __construct() {
		parent::__construct();
		$this->assign('noshow',true);
		$this->assign('noclose',true);
	}
	
	public function index(){
		if($_SESSION['city']) {
			$city_model = D('city');
			$city_map['id'] = $_SESSION['city'];
			$city_info = $city_model->where($city_map)->find();
			$this->assign('new_city_name', $city_info['name']);
		}
		$this->assign ( 'title', "保养提问,汽车保养常识,汽车保养知识-携车网" );
		$this->assign ( 'meta_keyword', "汽车保养常识,汽车保养知识,汽车保养问题" );
		$this->assign ( 'description', "由谢师傅为代表的百家4S店专业师傅,在线为您解答爱车的种种疑问。" );
		$this->display('index');
	}
	


}