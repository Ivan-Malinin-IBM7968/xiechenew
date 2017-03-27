<?php

/*
 */

class ShopAction extends CommonAction {
    function _filter(&$map) {
		if($_REQUEST['shop_name']) {
			$map['shop_name'] = array('like', "%" . $_REQUEST['shop_name'] . "%");
			$this->assign("ShopName",$_REQUEST['shop_name']);
		}
		if($_REQUEST['safestate'] != "") {
			$map['safestate'] = $_REQUEST['safestate'];
			$this->assign("safestate",$_REQUEST['safestate']);
		}
    }

    function _before_add() {
        $this->prepare_data();
    }
    function _trans_shop_area($list){
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['area_name'] = $this->get_area_name($v['shop_area']);
            }
        }
        return $list;
    }
    function get_area_name($shop_area){
        $model_region = D('Region');
        $map['id'] = $shop_area;
        $region = $model_region->where($map)->find();
        $map_1['id'] = $region['parent_id'];
        $region_1 = $model_region->where($map_1)->find();
        return $region_1['region_name'].'-'.$region['region_name'];
    }

	//获取4S店品牌名
	//author:wuwenyu
	function _trans_shop_fs($list){
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['fsname'] = $this->get_fs_name($v['id']);
            }
        }
        return $list;
    }
    function get_fs_name($id){
        $model_fs = D('Fs');
        $map['xc_shop_fs_relation.shopid'] = $id;
		$list_fs = $model_fs->where($map)->join('xc_shop_fs_relation ON xc_fs.fsid = xc_shop_fs_relation.fsid')->field('fsname')->select();
		foreach($list_fs as $k=>$v){
			$list_fs['fsname']=$list_fs['fsname'].",".$v['fsname'];
		}
		$list_fs['fsname']=substr($list_fs['fsname'],1);

        return $list_fs['fsname'];
    }

	//获取用户名
	//author:wuwenyu
	function _trans_user_name($user_id){
        if ($user_id){
			$model_user = M('tp_admin.user','xc_');
			$map['id'] = $user_id;
			$user_name = $model_user->where($map)->field('nickname')->find();
        }else{
			$user_name = '--';
		}
        return $user_name['nickname'];
    }

    function _before_index() {
		
		//dump(C('SHOP_CALSS'));
		
		if($_REQUEST['shop_name']){
			$this->assign('ShopName',$_REQUEST['shop_name']);
		}
		if($_REQUEST['shop_city']){
			$this->assign('shop_city',$_REQUEST['shop_city']);
		}else {
			$_REQUEST['shop_city'] = 3306;
			$this->assign('shop_city',$_REQUEST['shop_city']);
		}

        $this->assign('shop_class', C('SHOP_CLASS'));
		
		$this->assign('city', C('CITYS'));
		
       // $this->assign('classfy', include($this->getCacheFilename('Classfy')));
    }
	
	function _before_edit(){
		
	    $model_shop_fs_relation = D('Shop_fs_relation');
		$ShopName = $_GET['ShopName'];
	    $fs_arr = $model_shop_fs_relation->where("shopid=$_GET[id]")->select();
	    if (!empty($fs_arr)){
	        $fsids_arr = array();
	        foreach ($fs_arr as $v){
	            $fsids_arr[] = $v['fsid'];
	        }
	    }
	    $fsids_str = implode(',',$fsids_arr);
	    $this->assign('fsids_str',$fsids_str);
		//获取店铺销售人员
		$model_shop = D('Shop');
	    $shop = $model_shop->where("id=$_GET[id]")->find();
	    $this->assign('saleman_str',$shop['user_id']);

		$this->prepare_data();
	}
	/*
        @author:
        @function:删除logo
        @time:2015-3-25
    */
	function del_logo(){
		$id = $_REQUEST['shop_id'];
		if($id){
			$shop_model = D('shop');
			$map['id'] = $id;
			$data['logo']='';
			$num = $shop_model->where($map)->save($data);
			if($num){
				$return['message'] = 'success';
			} else{
				$return['message'] = '0';
			}
			$this->ajaxReturn($return,'JSON');
		}
	}
	protected function ajaxReturn($data,$type='') {
		if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
		$type   =   strtoupper($type);
		if('JSON' == $type) {
			// 返回JSON数据格式到客户端 包含状态信息
			header('Content-Type:text/html; charset=utf-8');
			exit(json_encode($data));
		}elseif('XML' == $type){
			// 返回xml格式数据
			header('Content-Type:text/xml; charset=utf-8');
			exit(xml_encode($data));
		}elseif('EVAL' == $type){
			// 返回可执行的js脚本
			header('Content-Type:text/html; charset=utf-8');
			exit($data);
		}else{
			// TODO 增加其它格式
		}
	}

	/*
		@author:chf
		@function:商铺编辑页面
		@time:2013-3-18
	*/
    function edit() {
		
        $model = D(GROUP_NAME . "/" . 'Shop');
		
		$list = $model->relation(true)->find($_GET['id']);
		
        $model_shopnotice = D(GROUP_NAME . "/" . 'Shopnotice');
        $map_shopnotice['shop_id'] = $_GET['id'];
		//dump($list);
		$this->assign('list',$list);
		$this->assign('ShopName',$_GET['ShopName']);
		$this->display();
    }
     public function update() {
		 session_start();
//		dump($_SESSION); exit;;
		 $arr = array(171,182,223,234,242,243,251,252,259,266,268,269,273,274,267);
		 if(in_array($_SESSION["authId"],$arr)){
			 $id = $_POST['id'];
			 $map['id']=$id;
			 $shop_address=$_POST['shop_address'];
			 $val['shop_address'] = $shop_address;
			 $shop = M('shop');
			 $num = $shop->where($map)->save($val);
			 if($num){
				 $this->success(L('更新成功!!!!!!'));
			 }else{
				 $this->error(L('更新失败!!!!!!!'));
			 }

		 }else {
			 $model = D(GROUP_NAME . "/" . 'Shop');

			 $ShopdetailModel = M('Shopdetail');//修改内容

			 if (!$_POST['shop_prov'] || !$_POST['shop_city'] || !$_POST['shop_area']) {
				 unset($_POST['shop_prov']);
				 unset($_POST['shop_city']);
				 unset($_POST['shop_area']);
			 }
			 $data = $model->create();
			 $data['Shopdetail'] = array('shop_detail' => $_POST['shop_detail']);

			 $result = $model->relation(true)->where("id=$_POST[id]")->save($data);
			 $detail_count = $ShopdetailModel->where("shop_id=$_POST[id]")->count();//修改内容

			 if ($detail_count > 0) {//修改内容
				 $ShopdetailModel->where("shop_id=$_POST[id]")->save(array('shop_detail' => $_POST['shop_detail']));//修改内容
			 } else {
				 $ShopdetailModel->add(array('shop_detail' => $_POST['shop_detail'], 'shop_id' => $_POST['id']));//修改内容
			 }


			 $fsids = $_POST['fsids'];
			 if (!empty($fsids)) {
				 $fs_arr = array();
				 foreach ($fsids as $key => $fsid) {
					 $fs_arr[$key]['shopid'] = $_POST['id'];
					 $fs_arr[$key]['user_id'] = $_POST['user_id'];
					 $fs_arr[$key]['fsid'] = $fsid;
				 }

				 $model_shop_fs_relation = D('Shop_fs_relation');
				 $model_shop_fs_relation->where("shopid=$_POST[id]")->delete();
				 $model_shop_fs_relation->addAll($fs_arr);

			 }

			 if (false !== $result) {
				 $this->success(L('更新成功'));
			 } else {
				 $this->error(L('更新失败'));
			 }
		 }
		/*
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        if (false !== $model->save()) {
            unset($_POST['id']); //注销ID防止影响到shop_detail表更新
            $detail_model = D(GROUP_NAME . "/" . 'Shopdetail');
            $detail_model->create();
            $detail_model->where("shop_id=" . $_POST['shop_id'])->save();
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success(L('更新成功'));
        } else {
            //错误提示
            $this->error(L('更新失败'));
        }
		*/
    }


    public function _before_insert() {
        if ($_FILES['logo']['name']){
            $this->couponupload();
        }
    }

    public function _before_update() {
        if ($_FILES['logo']['name']){
            $this->couponupload();
        }
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

		$model_role_user = M('tp_admin.role_user','xc_');
		$map_ts['xc_role_user.role_id']=5;
		$saleman=$model_role_user->where($map_ts)->join("tp_admin.xc_user ON xc_role_user.user_id=xc_user.id")->select(); //根据role_id查询销售人员数据
		//echo $model_role_user->getlastSql();
		$this->assign('saleman',$saleman);
    }

    function insert() {
        $shop_model = D(GROUP_NAME . "/" . 'Shop');
		$data = $shop_model->create();
		if (false === $data) {
            $this->error($shop_model->getError());
        }
		$data['Shopdetail']=array(
			'shop_detail'=>$_POST['shop_detail'],	 
			 
		);
		
		if ($lastInsId = $shop_model->relation(true)->add($data)) {
			$fsids = $_POST['fsids'];
			if (!empty($fsids)){
			    $fs_arr = array();
			    foreach ($fsids as $key=>$fsid){
			        $fs_arr[$key]['shopid'] = $lastInsId;
			        $fs_arr[$key]['fsid'] = $fsid;
			    }
			    $model_shop_fs_relation = D('Shop_fs_relation');
			    $model_shop_fs_relation->addAll($fs_arr);
			}
            $this->success(L('更新成功'));
        } else {
           $this->error(L('更新失败'));
        }

    }

//	function delete() {
//		print_r($_REQUEST);exit();
//		$shop_model = D(GROUP_NAME . "/" . 'Shop');
//		$shop_fs_relation = D(GROUP_NAME ."/" . 'Shop_fs_relation');
//
//	}


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

	function contract() {
		$user_id = $_REQUEST['user_id'];
		if($user_id){
			$map['user_id']=$user_id;
			$this->assign('user_id',$user_id);
		}

		//只显示上海的数据
		$map['shop_prov'] = 3305;

		//销售数据
		$model_role_user = M('tp_admin.role_user','xc_');
		$map_ts['xc_role_user.role_id']=5;
		$saleman=$model_role_user->where($map_ts)->join("tp_admin.xc_user ON xc_role_user.user_id=xc_user.id")->select();
		//echo $model_role_user->getlastSql();
		$this->assign('saleman',$saleman);
		//print_r($saleman);

		$model = D(GROUP_NAME . "/" . $this->getActionName());
		$status = $_REQUEST['status'];
		if(!$status){
			$status='normal';
		}
		if($status=='normal'){
			$now=time();
			$reference_time=$now+5184000;
			$map['end_time']=array(array('neq','1421683200'),array('lt',$reference_time));
			$map['shop_class']= 1;
			$map['status']= 1;//店铺状态：1.正常；2.不正常
		}else{
			$map['shop_class']= 2;
			$map['end_time']=array(array('neq','1421683200'),array('gt','0'));
		}

		/**导出数据start**/
        $execl = $_REQUEST['execl'];
		if($execl){
			//导出数据处理
			$list_execl = $model->where($map)->order('end_time asc')->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			//数据转换
			if (method_exists($this, '_trans_data')) {
				$list_execl = $this->_trans_data($list_execl);
			}
			//数据转换
			if (method_exists($this, '_trans_article_data')) {
				$list_execl = $this->_trans_article_data($list_execl);
			}
			//数据转换
			if (method_exists($this, '_trans_shop_area')) {
				$list_execl = $this->_trans_shop_area($list_execl);
			}
			//数据转换
			if (method_exists($this, '_trans_shop_fs')) {
				$list_execl = $this->_trans_shop_fs($list_execl);
			}
			foreach($list_execl as $k=>$v){
				$last_date=floor(($v['end_time']-$now)/86400);
				$list_execl[$k]['last_date']="距续约还有".$last_date."天";
				$list_execl[$k]['end_time']=date('Y-m-d',$v['end_time']);
				$list_execl[$k]['user_name']= $this->_trans_user_name($v['user_id']);
			}
			$this->export_execl($list_execl,'续约.xls');
		}
		/**导出数据end**/

        //取得满足条件的记录数
        
        //创建分页对象
        if (!empty($_REQUEST['listRows'])) {
            $listRows = $_REQUEST['listRows'];
        } else {
            $listRows = '';
        }
       
		$count = $model->where($map)->count($model->getPk());
		
		import("ORG.Util.Page");
		$p = new Page($count, $listRows);
		
	//	$p = new Page($count, $listRows);
        //分页查询数据
		//dump($map);
		
		$list = $model->where($map)->order('end_time asc')->limit($p->firstRow . ',' . $p->listRows)->select();
		echo $model->getlastsql();
        //分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
        //数据转换
        if (method_exists($this, '_trans_data')) {
            $list = $this->_trans_data($list);
        }
        //数据转换
        if (method_exists($this, '_trans_article_data')) {
            $list = $this->_trans_article_data($list);
        }
        //数据转换
        if (method_exists($this, '_trans_shop_area')) {
            $list = $this->_trans_shop_area($list);
        }
		//数据转换
        if (method_exists($this, '_trans_shop_fs')) {
            $list = $this->_trans_shop_fs($list);
        }
		foreach($list as $k=>$v){
			$last_date=floor(($v['end_time']-$now)/86400);
			$list[$k]['last_date']="距续约还有".$last_date."天";
			$list[$k]['end_time']=date('Y-m-d',$v['end_time']);
			$list[$k]['user_name']= $this->_trans_user_name($v['user_id']);
		}
        //echo '<pre>';print_r($list);

        //分页显示
		$page = $p->show_admin();
		//dump($page);
        //列表排序显示
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
        $sort = $sort == 'desc' ? 1 : 0; //排序方式
        //echo '<pre>';print_r($list);exit;
        //模板赋值显示
        $this->assign('list', $list);
        $this->assign('sort', $sort);
        $this->assign('order', $order);
        $this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
        $this->assign("page", $page);
		$this->assign("map",$map);
        Cookie::set('_currentUrl_', __SELF__);

		$this->display();
	}

	function export_execl($list,$filename){
		if($list){
			foreach($list as $key=>$val){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table .= '<tr bgcolor='.$color.'><td>'.$val['id'].'</td><td>'.$val['fsname'].'</td><td>'.$val['area_name'].'</td><td>'.$val['shop_name'].'</td><td>'.$val['last_date'].'</td><td>'.$val['end_time'].'</td><td>'.$val['shop_phone'].'</td><td>'.$val['user_name'].'</td>';
			}
		}
		$color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename='{$filename}'");
        $str = '<table><tr bgcolor='.$color.'><td>编号</td><td>品牌</td><td>所在城市</td><td>商铺名称</td><td>签约状态</td><td>到期时间</td><td>电话</td><td>负责人</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}
}