<?php
class CarbrandAction extends CommonAction {
    function __construct() {
		parent::__construct();
        $this->new_carbrand = D('new_carbrand');   //新品牌表
		
	}
    public function index(){
        //过滤携车网自有车型品牌
        $condition['is_show'] = array('neq',2) ;
        
        if (isset($_REQUEST['brand_name']) and !empty($_REQUEST['brand_name'])){
            $condition['brand_name'] = array('like',"%".$_REQUEST['brand_name']."%");
            $this->assign('brand_name',$_REQUEST['brand_name']);
        }
        //echo '<pre>';print_r($map);
        //$this->new_carbrand = D(GROUP_NAME.'/Carbrand');
        // 计算总数
        $count = $this->new_carbrand->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['brand_name'] = $_REQUEST['brand_name'];
        foreach($_REQUEST as $key=>$val) {  
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/".urlencode($val)."/";   
			}
        }
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->new_carbrand->where($condition)->order('word ASC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carbrandinfo);
        $this->display();
    }
    
    public function add_brand(){
        // echo '11111111111' ;
        $data = array();
        //图片上传
        if ($_FILES['brand_logo']['name']){
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            $upload = $this->_upload_init($upload);
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $uploadList = $upload->getUploadFileInfo();
            }
        }
        //插入数据
        $data['brand_name'] = $_REQUEST['brand_name'] ;
        $data['word'] = $_REQUEST['word'] ;
        $data['brand_logo'] =  $uploadList[0]['savename'] ;
        $data['is_show'] =  1  ;
        $this->new_carbrand->add($data);
        //网页跳转
        $this->success('添加品牌成功',U('Carbrand/index'));
         
    }
    
    public function save_brand(){
        $brand_name = isset($_POST['brand_name'])?$_POST['brand_name']:'';
        $word = isset($_POST['word'])?$_POST['word']:'';
        $brand_id = isset($_POST['brand_id'])?$_POST['brand_id']:'0';
        if ($brand_id){
            $data['brand_name'] = $brand_name;
            $data['word'] = $word;
            $condition['brand_id'] = $brand_id;
            if ($this->new_carbrand->where($condition)->save($data)){
                echo 1;exit;
            }
        }
        echo -1;exit;
    }
    public function delete_brand(){
    	$brand_id = $this->_post('brand_id');
    	$condition['brand_id'] = $brand_id;
    	$brand_name = $this->new_carbrand->field('brand_name')->where($condition)->find();
    	if ($this->new_carbrand->where($condition)->delete()){
    		//记录操作日志
    		$operate_log = array(
    				'oid'=>$brand_id,
    				'operate_id'=>$_SESSION['authId'],
    				'log'=>'操作删除车型，车型名称:'.$brand_name['brand_name']
    		);
    		$this->addOperateLog($operate_log);
    		echo 1;exit;
    	}
    	echo -1;
    }
    
    
    //上传品牌图片处理  wql@20150827
    function   upload_logo(){
        //上传处理  
        if ($_FILES['pinpai_logo']['name']){
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            $upload = $this->_upload_init($upload);
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $uploadList = $upload->getUploadFileInfo();
            }
        }
        //echo   $uploadList[0]['savepath'].$uploadList[0]['savename']  ;
        //更新品牌表
        $map['brand_id'] = intval($_REQUEST['pinpai_id']) ;
        $data['brand_logo'] =  $uploadList[0]['savename'] ;
        $this->new_carbrand->where($map)->save($data);
        //重定向
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    
     /*
      * 上传初始化函数
     */
        
    public function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        //$upload->allowExts = explode(',', 'jpg');
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        //设置附件上传目录
        //$upload->savePath = C('UPLOAD_ROOT') . '/Bidsource/';
        $upload->savePath = SITE_ROOT . 'UPLOADS/Brand/Logo/';
        $upload->saveRule = 'uniqid';

        $upload->uploadReplace = false;
        //$this->watermark = 1;水印
        return $upload;
	}
}
?>