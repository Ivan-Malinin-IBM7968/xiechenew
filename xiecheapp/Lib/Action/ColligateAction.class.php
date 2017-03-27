<?php
	/*class: (综合)咨询 优惠 4S店类*/
class ColligateAction extends CommonAction{
	function __construct() {
		parent::__construct();
		$this->ArticleModel = D('Article');//咨询
		$this->NoticeModel = D('Notice');//优惠卷信息
		$this->ShopModel = D('Shop');//商铺
		$this->_empty();
		exit();
	}

	/*
		@author:chf
		@function:显示咨询 优惠 4S店页
		@time:2013-04-08
	*/
	function index(){
		/*获取咨询开始*/
		$ArticleMap['status'] = 1;
		$ArticleMap['city_name'] = array(array('eq',$this->city_name),array('eq','全部'),'or');
		$data['ArticleList'] = $this->ArticleModel->where($ArticleMap)->order("create_time DESC")->limit(10)->select();
	
		/*获取咨询结束*/
		
		/*获取优惠信息开始*/
		$NoticeMap['status'] = 1;
		$NoticeMap['city_name'] = $this->city_name;
		$data['Notice'] = $this->NoticeModel->where($NoticeMap)->order("update_time DESC")->limit(10)->select();
		/*获取优惠信息结束*/

		$ShopModel = D('Shop');
		/*获取上海4s店铺信息开始*/
		$sh_map['shop_city'] = 3306;
		$sh_map['status'] = 1;
		$data['SH_shoplist'] = $ShopModel->where($sh_map)->order("id DESC")->select();
		/*获取上海4s店铺信息结束*/

		/*获取北京4s店铺信息开始*/
		$bj_map['shop_city'] = 2912;
		$bj_map['status'] = 1;
		$data['BJ_shoplist'] = $ShopModel->where($bj_map)->order("id DESC")->limit(10)->select();
		/*获取北京4s店铺信息结束*/

		/*获取广州4s店铺信息开始*/
		$gz_map['shop_city'] = 2918;
		$gz_map['status'] = 1;
		$data['GZ_shoplist'] = $ShopModel->where($gz_map)->order("id DESC")->limit(10)->select();
		/*获取广州4s店铺信息结束*/
		$this->assign('data',$data);

		$this->display();
	
	}

	/*
		@author:chf
		@function:显示店铺信息页面
		@time:2013-04-08
	*/
	function ShopList(){
		$ShopModel = D('Shop');
		$map['shop_city'] = $_REQUEST['shop_city'];
		$map['status'] = 1;
		$data['shoplist'] = $ShopModel->where($map)->order("id DESC")->limit(20)->select();
		$count = $ShopModel->where($map)->count();
		 // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		$data['page'] = $p->show();
		$data['ShopList'] = $ShopModel->where($map)->limit($p->firstRow.','.$p->listRows)->order("id DESC")->select();
		if($data['ShopList']){
			$model_shop_fs_relation = D('Shop_fs_relation');
			foreach ($data['ShopList'] as $kk=>$vv){
				
				if (file_exists("UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$data['ShopList'][$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$data['ShopList'][$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
				
			}
		}
		
		$this->assign('data',$data);
		$this->display();
	}



	/*
		@author:chf
		@function:显示咨询信息
		@time:2013-04-08
	*/
	function article(){
		$city_name = $_REQUEST['city_name'];
		$count = $this->ArticleModel->where(array('city_name'=>'上海','status'=>1))->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $this->ArticleModel->where(array('city_name'=>'上海','status'=>1))->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	
	/*
		@author:chf
		@function:显示优惠信息
		@time:2013-04-08
	*/
	function Notice(){
		$map['city_name'] = $_REQUEST['city_name'];
		$map['city_name'] = '上海';
		$map['status'] = 1;
        // 导入分页类
        import("ORG.Util.Page");
		$count = $this->NoticeModel->where($map)->count();
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $this->NoticeModel->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display('Notice');
	}
	
	
	
}




?>