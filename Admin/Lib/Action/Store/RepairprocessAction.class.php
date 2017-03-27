<?php
/*
 */
class RepairprocessAction extends CommonAction {
    protected $mRepireprocess;
    protected $BidorderModel;
	function __construct() {
		parent::__construct();
		$this->BidorderModel = D('bidorder');//事故车订单表
		$this->mRepireprocess = D('repairprocess');
	}
	/*
		@author:liuhui
		@function:事故车维修进度列表页
		@time:2014-08-19
	*/
    public function index(){
    	$map = array();
		if($_REQUEST['licenseplate']){
			$data['licenseplate'] = $map['licenseplate'] = $_REQUEST['licenseplate'];
		}
		// 计算总数
		$count = $this->mRepireprocess->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data = $this->mRepireprocess->where($map)->order("id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if ($data) {
			foreach ($data as $key=>&$val){
					$val['describe'] = unserialize($val['describe']);
					unset($val);
			}
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
		
    }
	/*
		@author:liuhui
		@function:添加维修进度信息
		@time:2014-08-19
	*/
    public function add(){
    	if ( !empty($_POST) ) {
    		if (empty($_POST['order_id']) ) {
    			$this->error('请选择车牌号');
    		}
    		$order_id = $_POST['order_id'];
    		$licenseplate = $_POST['licenseplate'];
    		$uid = $_POST['uid'];
    		$mobile = $_POST['mobile'];
    		//验证该订单是否存在
    		$exist = $this->BidorderModel->where(array(
    				'id'=>$order_id,
    				'licenseplate'=>$licenseplate
    		))->count();
    		if (!$exist) {
    			$this->error('该车牌号没有预订过事故车服务');
    		}
    		
    		$updateArr = array();
    		
    		for ($i=1;$i<=8;$i++){
    			if ( !empty($_POST['url-pic-'.$i]) ) {
    				$url = $_POST['url-pic-'.$i];
    				$des = @$_POST['describe-pic-'.$i];
    				$updateArr[] = array(
    						"url"=>$url,
    						"des"=>$des
    				);
    			}else{
    				continue;
    			}
    		}
    		if ( !empty($updateArr) ) {
    			
	    		$insert = array(
	    				'describe'=>serialize($updateArr),
	    				'up_time'=>time(),
	    				'order_id'=>$order_id,
	    				'licenseplate'=>$licenseplate,
	    				'uid'=>$uid,
	    				'mobile'=>$mobile
	    				
	    		);
	    		$save = $this->mRepireprocess->add($insert);
	    		if ($save) {
		    		$this->success('添加成功!',U('/Store/Repairprocess/index'));
	    		}else{
	    			$this->error('更新失败');
	    		}
    		}else{
    			$this->error('请选择图片');
    		}
    	}else{
    		//$licenseplates = $this->mRepireprocess->field('id,licenseplate')->select();
    		//$this->assign('licenseplates',$licenseplates);
    		$this->display();
    	}
    	
    }
    /* @author:liuhui
     * ajax模糊查询order_id 和车牌号
     * @time:2014-08-21
     */
    public function ajax_data(){
    	if ( !empty( $_POST['licenseplate']) ) {
    		$licenseplate = $_POST['licenseplate'];
    		$where['licenseplate'] = array('like','%'.$licenseplate.'%');
	    	$data = $this->BidorderModel->field('id,licenseplate,uid,mobile')->where($where)->select();
	    	$this->ajaxReturn($data,'',1);
    	}
    }
    /*
        @author:liuhui
		@function:上传图片
		@time:2014-08-20
     */
    
    public function pic_upload() {
    	if ($_FILES['pic']['name']){
    		$result = $this->upload('pic');
    		echo $_POST['pic'];
    	}
   
    }
    public function _upload_init($upload) {
    	//设置上传文件大小
    	$upload->maxSize = C('UPLOAD_MAX_SIZE');
    	//设置上传文件类型
    	$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
    	//设置附件上传目录
    	$upload->savePath = C('UPLOAD_ROOT') . '/Repairprocess/';
    	$upload->thumb = true;
    	$upload->saveRule = 'uniqid';
    	$upload->thumbPrefix = 'test_';//coupon1_网站图片显示；coupon2_手机APP图片显示
    	$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
    	$resizeThumbSize_arr = array('200','200');
    	$upload->thumbMaxWidth = $resizeThumbSize_arr[0];
    	$upload->thumbMaxHeight = $resizeThumbSize_arr[1];
    	return $upload;
    }
    public function del(){
    	$id = $_GET['id'];
    	$where = array('id'=>$id);
    	$res = $this->mRepireprocess->where($where)->delete();
    	if($res){
    		$this->success('删除成功',U('Store/Repairprocess/index/'));
    	}else{
    		$this->error('删除失败',U('Store/Repairprocess/index/'));
    	}
    }




	
    
}