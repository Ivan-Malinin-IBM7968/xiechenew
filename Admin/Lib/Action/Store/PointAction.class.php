<?php
class PointAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->GiftModel = D('gift');
	}

	/*
		@author:chf
		@function:显示积分换礼页
		@time:2013-04-17
	*/
	
    public function index(){
        $map['status'] = 0;
        // 计算总数
        $count = $this->GiftModel->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->GiftModel->order('id ASC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }
		
	/*
		@author:chf
		@function:新增积分换礼页
		@time:2013-04-17
	*/
	 public function insert(){
		$data['title'] = $_REQUEST['title'];
		$data['point'] = $_REQUEST['point'];
		$data['img'] = $this->point();
		$data['create_time'] = time();
		$this->GiftModel->add($data);
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success(L('新增成功'));
	 }

	/*
		@author:chf
		@function:显示修改积分换礼页
		@time:2013-04-17
	*/
	 public function edit(){
		$data['id'] = $_REQUEST['id'];
		$list = $this->GiftModel->where($data)->find();
		$this->assign('list',$list);
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
   
	
	/*
		@author:chf
		@function:上传得到图片名
		@time:2013-04-18
	*/
    public function point()
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
		//设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Point/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'Point_';//coupon1_网站图片显示；coupon2_手机APP图片显示
        //$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('300,200','300,200');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $uploadList = $upload->getUploadFileInfo();
            if ($uploadList){
                if ($_FILES['image']['name']){
                   return  $uploadList[0]['savename'];
                }
				
            }
            
        }
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