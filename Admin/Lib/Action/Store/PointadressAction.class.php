<?php
class PointadressAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->PointAdressModel = D('pointadress');//用户兑换礼品表记录
		$this->RegionModel = D('region');//城市表
		$this->GiftModel = D('gift');//礼品表
	}

	/*
		@author:chf
		@function:显示用户积分换礼管理页
		@time:2013-04-17
	*/
	
    public function index(){
        $map['status'] = 0;
        // 计算总数
        $count = $this->PointAdressModel->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->PointAdressModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$prov = $this->RegionModel->where(array('id'=>$v['prov_id']))->find();
				$list[$k]['prov_name'] = $prov['region_name'];
				$city = $this->RegionModel->where(array('id'=>$v['city_id']))->find();
				$list[$k]['city_name'] = $city['region_name'];
				$area = $this->RegionModel->where(array('id'=>$v['area_id']))->find();
				$list[$k]['area_name'] = $area['region_name'];
				$giftinfo = $this->GiftModel->where(array('id'=>$v['gift_id']))->find();
				$list[$k]['gift_name'] = $giftinfo['title'];
				$list[$k]['img'] = $giftinfo['img'];
				$list[$k]['point'] = $giftinfo['point'];
			}
		}
		
        // 赋值赋值
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }
		
	

	 /*
		@author:chf
		@function:礼品显示状态修改为无效
		@time:2013-04-18
	*/
	 public function PointDel(){
		$data['id'] = $_REQUEST['id'];
		$this->GiftModel->where($data)->save(array('status'=>1));
		echo '1';
	 }

    
    public function _upload_init($upload) {
            //实例化上传类  
       
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Shop/Logo/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'Point_';//coupon1_网站图片显示；coupon2_手机APP图片显示
        //$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('300,200','300,200');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }
    
}
?>