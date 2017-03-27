<?php

/*
 */

class ArticleAction extends CommonAction {
    function _filter(&$map) {
		
        $map['status'] = array('neq',2);
		/*@author:chf 咨询按照时间倒叙排列条件*/
		$map['ArticleType'] = 'yes';

        if ($_POST['create_time']){
            $s_time = strtotime($_POST['create_time']."00:00:00");
            $e_time = strtotime($_POST['create_time']."23:59:59");
            $map['create_time'] =  array(array('gt',$s_time),array('lt',$e_time));
            $_REQUEST['listRows'] = 1000;
        }
        if ($_POST['title']){
            $map['title'] = array('like', "%" . $_POST['title'] . "%");
        }
        if ($_POST['shop_name']){
            $map['shop_name'] = array('like', "%" . $_POST['shop_name'] . "%");
        }
    }
    function _trans_article_data($list){
        if ($list) {
            $model_shop = D(GROUP_NAME.'/Shop');
        	foreach ($list as $k=>$v){
        	    if ($v['shop_id']){
        	        $map['id'] = $v['shop_id'];
        	        $shop = $model_shop->where($map)->find();
        	        $list[$k]['shop_name'] = $shop['shop_name'];
        	    }
        	    if ($v['status']==0){
        	        $list[$k]['status_name'] = '未审核';
        	    }
        	    if ($v['status']==1){
        	        $list[$k]['status_name'] = '已审核';
        	    }
        	    if ($v['status']==2){
        	        $list[$k]['status_name'] = '已删除';
        	    }
        	}
        }
        //echo '<pre>';print_r($list);
        return $list;
    }
    function shenhe(){
        $id = $_REQUEST['id'];
        $modle_article = D(GROUP_NAME.'/Article');
        $map['id'] = $id;
        $data['status'] = 1;
        if($modle_article->where($map)->save($data)){
             $this->success('审核成功！');
        }else{
            $this->error('审核失败');
        }
    }
    function checkPass(){
        $id = $_REQUEST['id'];
        $modle_article = D(GROUP_NAME.'/Article');
        $map['id'] = array('in',$id);
        $data['status'] = 1;
        if($modle_article->where($map)->save($data)){
             $this->success('审核成功！');
        }else{
            $this->error('审核失败');
        }
    }
    function delete(){
        $id = $_REQUEST['id'];
        $modle_article = D(GROUP_NAME.'/Article');
        $map['id'] = array('in',$id);
        $data['status'] = 2;
        if($modle_article->where($map)->save($data)){
             $this->success('删除成功！');
        }else{
            $this->error('删除失败');
        }
    }
    function delete_bak(){
        $id = $_REQUEST['id'];
        $modle_article = D(GROUP_NAME.'/Article');
        $map['id'] = $id;
        $data['status'] = 2;
        if($modle_article->where($map)->save($data)){
             $this->success('删除成功！');
        }else{
            $this->error('删除失败');
        }
    }
    function resume(){
        $id = $_REQUEST['id'];
        $modle_article = D(GROUP_NAME.'/Article');
        $map['id'] = $id;
        $data['status'] = 0;
        if($modle_article->where($map)->save($data)){
             $this->success('审核成功！');
        }else{
            $this->error('审核失败');
        }
    }
    
    function _before_add() {
		
        $citys = C('CITYS');
	    $this->assign('citys',$citys);
	    //品牌fs
	    $model_fs = D('Fs');
	    $fs = $model_fs->select();
	    $this->assign('fs',$fs);
		$this->assign('time',time());
        $this->prepare_data();
    }

    function _before_index() {
		
		//dump(C('SHOP_CALSS'));
        //$this->assign('shop_class', C('SHOP_CLASS'));

       // $this->assign('classfy', include($this->getCacheFilename('Classfy')));
    }
	function _before_edit(){
	    $model_shop_fs_relation = D('Shop_fs_relation');
	    $fs_arr = $model_shop_fs_relation->where("shopid=$_GET[id]")->select();
	    if (!empty($fs_arr)){
	        $fsids_arr = array();
	        foreach ($fs_arr as $v){
	            $fsids_arr[] = $v['fsid'];
	        }
	    }
	    $fsids_str = implode(',',$fsids_arr);
	    $this->assign('fsids_str',$fsids_str);
	    $citys = C('CITYS');
	    $this->assign('citys',$citys);
	    //品牌fs
	    $model_fs = D('Fs');
	    $fs = $model_fs->select();
	    $this->assign('fs',$fs);
		$this->prepare_data();
	}

    function edit() {
	$model = D(GROUP_NAME . "/" . 'Article');
	$list =    $model->find($_GET['id']);

	$model_shop = D("Shop");
	$shop_info = $model_shop->find($list['shop_id']);

	$seo_title = "{$list[title]}-用车心得-携车网";

	$this->assign('vo',$list);
	$this->assign('seo_title',$seo_title);
	$this->display();
    }
    public function update() {
        $model = D(GROUP_NAME . "/" . 'Article');

		$data = $model->create();
		if($_REQUEST['create_time']){
			$data['create_time'] = strtotime($_REQUEST['create_time']);
		}
		$result =  $model->where("id=$_POST[id]")->save($data);
		if(false !== $result){
			S('article'.$_POST['id'] , NULL );
			$this->success(L('更新成功'));
		}else{
			 $this->error(L('更新失败'));
		}
    }

    public function _before_insert() {
        //$this->couponupload();
    }

    public function _before_update() {
        //$this->couponupload();
    }

    function prepare_data() {
		$province = R('Store/Region/getRegion');
		//dump($province);
		//exit;
        $this->assign('province',$province);
		$model = D(GROUP_NAME . "/" . 'Carbrand');
		$brand_arr = $model->select();
		$this->assign('brand_arr',$brand_arr);
		
		$model_fs = D(GROUP_NAME. "/" . 'fs');
		$fs_arr = $model_fs->select();
		$this->assign('fs_arr',$fs_arr);
    }

    function insert() {
        $article_model = D(GROUP_NAME . "/" . 'Article');
		$data = $article_model->create();
		if (false === $data) {
            $this->error($article_model->getError());
        }
		if($_REQUEST['create_time']){
			$data['create_time'] = strtotime($_REQUEST['create_time']);
		}
		if ($lastInsId = $article_model->add($data)) {
            $this->success(L('更新成功'));
        } else {
           $this->error(L('更新失败'));
        }
    }


	public function get_map(){
		$model = D(GROUP_NAME . "/" . $this->getActionName());
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->find($id);

		//$content = [{"name":"{$aaaaaaa}","address":"{$vo.shop_address}","tel":"{$vo.shop_phone}","point":"{$vo.shop_maps}","citycode":131}];
		$data = array(
			'name'=>$vo['shop_name'],
			'address'=>$vo['shop_address'],
			'tel'=>$vo['shop_maps'],
			'citycode'=>'131',
		
		);
		$data = '{"name":"'.$vo['shop_name'].'","address":"'.$vo['shop_address'].'","tel":"'.$vo['shop_maps'].'","point":"'.$vo['shop_maps'].'","citycode":131}';
		//$data = json_encode($data);
		$this->assign('data',$data);
        $this->display();
	}
	
    public function get_shops(){
        $fsid = isset($_POST['fsid'])?$_POST['fsid']:0;
        $shops = array();
        if ($fsid){
            $model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
            $map_sf['fsid'] = $fsid;
            $shop_fs_relation = $model_shop_fs_relation->where($map_sf)->select();
            if ($shop_fs_relation){
                foreach ($shop_fs_relation as $k=>$v){
                    $shop_id_arr[] = $v['shopid'];
                }
                $shop_id_str = implode(',',$shop_id_arr);
                $model_shop = D(GROUP_NAME.'/Shop');
                $map_shop['id'] = array('in',$shop_id_str);
                $shops = $model_shop->where($map_shop)->select();
            }
        }
        echo json_encode($shops);
        exit;
    }


    protected function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Shop/Logo/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        //$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
        $upload->thumbPrefix = 'thumb1_,thumb2_';//thumb1_网站图片显示；thumb2_手机APP图片显示
        $resizeThumbSize_arr = array('120,80','90,60');
		//$resizeThumbSize_arr = array('120,100','90,80');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }


}