<?php
class TuangouAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
	
    public function index(){
        Cookie::set('_currentUrl_', __SELF__);
        $model_tuangou = D(GROUP_NAME.'/Tuangou');
        // 计算总数
        $count = $model_tuangou->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_tuangou->order('id ASC')->limit($p->firstRow.','.$p->listRows)->select();
        
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();
    }
    
    public function _before_add(){
        //载入服务项目MODEL
        $model_serviceitem = D(GROUP_NAME.'/serviceitem');
		//获取所有的服务分类
		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$this->assign('list_si_level_0',$list_si_level_0);
    	$this->assign('list_si_level_1',$list_si_level_1);
    	
		$model_shop = D(GROUP_NAME.'/Shop');
		$shops = $model_shop->where("status=1")->select();	
		$this->assign('shops',$shops);
    }
    public function _before_edit(){
        //载入服务项目MODEL
        
        $model_serviceitem = D(GROUP_NAME.'/serviceitem');
		//获取所有的服务分类
		$list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$this->assign('list_si_level_0',$list_si_level_0);
    	$this->assign('list_si_level_1',$list_si_level_1);
    	
		$model_shop = D(GROUP_NAME.'/Shop');
		$shops = $model_shop->where("status=1")->select();	
		$this->assign('shops',$shops);
    }
    
    public function _before_insert(){
        if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
        if (!empty($_POST['select_services'])){
            $_POST['service_ids'] = implode(',',$_POST['select_services']);
        }
        //echo '<pre>';print_r($_POST);exit;
    }
    public function _before_update(){
        if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
        if (!empty($_POST['select_services'])){
            $_POST['service_ids'] = implode(',',$_POST['select_services']);
        }
        //echo '<pre>';print_r($_POST);exit;
    }
    
    public function del(){
        $map['id'] = $_POST['id'];
        $data['is_delete'] = 1;
        $model_coupon = D(GROUP_NAME.'/Tuangou');
        if($model_coupon->where($map)->save($data)){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }
    public function send(){
        $data['uid'] = $_POST['uid'];
        $data['coupon_id'] = $_POST['id'];
        $data['create_time'] = time();
        $model_coupon = D(GROUP_NAME.'/Membercoupon');
        if($model_coupon->add($data)){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }
    public function get_shops(){
        $series_id = isset($_POST['series_id'])?$_POST['series_id']:0;
        $shops = array();
        if ($series_id){
            $model_carseries = D(GROUP_NAME.'/Carseries');
            $series = $model_carseries->find($series_id);
            $fsid = $series['fsid'];
            $model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
            $map['fsid'] = $fsid;
            $shop_fs_relation = $model_shop_fs_relation->where($map)->select();
            $shop_id_arr = array();
            if ($shop_fs_relation){
                foreach ($shop_fs_relation as $k=>$v){
                    $shop_id_arr[] = $v['shopid'];
                }
                $shop_id_str = implode(',',$shop_id_arr);
                $model_shop = D(GROUP_NAME.'/Shop');
                $map_shop['id'] = array('in',$shop_id_str);
				$map_shop['shop_city'] = 3306;
                $shops = $model_shop->where($map_shop)->select();
            }
        }
		
        echo json_encode($shops);
        exit;
    }
}
?>