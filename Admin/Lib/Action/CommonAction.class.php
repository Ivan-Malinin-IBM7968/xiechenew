<?php


class CommonAction extends Action
{

    public function _initialize()
    {
        //swfupload.....
        /*
            if (isset($_POST["PHPSESSID"])) {
                session_id($_POST["PHPSESSID"]);
            } else if (isset($_GET["PHPSESSID"])) {
                session_id($_GET["PHPSESSID"]);
            }
        */
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision(GROUP_NAME)) {
                //检查认证识别号
                if (!$_SESSION[C('USER_AUTH_KEY')]) {
                    //跳转到认证网关	
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                if(GROUP_NAME !='Admin'){
                    // 没有权限 抛出错误
                    if (C('RBAC_ERROR_PAGE')) {
                        // 定义权限错误页面
                        redirect(C('RBAC_ERROR_PAGE'));
                    } else {
                        if (C('GUEST_AUTH_ON')) {
                            $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                        }
                        $this->error(L('_VALID_ACCESS_'));
                    }
                }
            }else {
				if (!$_SESSION[C('USER_AUTH_KEY')] && !$_REQUEST["uploadify"]) {
                    //跳转到认证网关	
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
            }
        }

        // 读取系统配置参数
        if (!file_exists(DATA_PATH . '~config.php')) {
            $config = D(GROUP_NAME . "/" . "Config");
            $list = $config->getField('name,value');
            $savefile = DATA_PATH . '~config.php';
            // 所有配置参数统一为大写
            $content = "<?php\nreturn " . var_export(array_change_key_case($list, CASE_UPPER), true) . ";\n?>";
            if (!file_put_contents($savefile, $content)) {
                $this->error('配置缓存失败！');
            }
        }
        C(include_once DATA_PATH . '~config.php');
    }

    // 缓存文件
    public function cache($name = '', $fields = '')
    {
        $name = $name ? $name : $this->getActionName();
        $Model = D(GROUP_NAME . "/" . $name);
        $list = $Model->select();
        $data = array();
        foreach ($list as $key => $val) {
            if (empty($fields)) {
                $data[$val[$Model->getPk()]] = $val;
            } else {
                // 获取需要的字段
                if (is_string($fields)) {
                    $fields = explode(',', $fields);
                }
                if (count($fields) == 1) {
                    $data[$val[$Model->getPk()]] = $val[$fields[0]];
                } else {
                    foreach ($fields as $field) {
                        $data[$val[$Model->getPk()]][] = $val[$field];
                    }
                }
            }
        }
        $savefile = $this->getCacheFilename($name);
        // 所有参数统一为大写
        $content = "<?php\nreturn " . var_export(array_change_key_case($data, CASE_UPPER), true) . ";\n?>";
        if (file_put_contents($savefile, $content)) {
            $this->success('缓存生成成功！');
        } else {
            $this->error('缓存失败！');
        }
    }

    protected function getCacheFilename($name = '')
    {
        $name = $name ? $name : $this->getActionName();
        return DATA_PATH . '~' . strtolower($name) . '.php';
    }

    public function index()
    {
        //echo '<pre>';print_r($_REQUEST);
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
		//print_r($map);
        //$map['status'] = 1;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (method_exists($this, '_get_order_sort')) {
            $map['status'] = 1;
        }
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (!empty($model)) {
            $this->_list($model, $map);
        }
		
        $this->display();
        return;
    }

    /**
    +----------------------------------------------------------
     * 验证码显示
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    function verify()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }

    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param string $name 数据对象名称
    +----------------------------------------------------------
     * @return HashMap
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _search($name = '')
    {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $model = D(GROUP_NAME . "/" . $name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (substr($key, 0, 1) == '_')
                continue;
            if (isset($_REQUEST[$val]) && $_REQUEST[$val] != '') {
                if ($name == 'Order' and $val == 'id'){
                    $map[$val] = substr($_REQUEST[$val],0,-1)-987;
                }else{
                    $map[$val] = $_REQUEST[$val];
                }
            }
        }
		/*
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $map['shop_id'] = $_SESSION['shop_id'];
        }*/
        return $map;
    }

    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */

    protected function _list($model, $map = array(), $sortBy = '', $asc = false)
    {
        //排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
           // echo $order;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
      
        //创建分页对象
        if (!empty($_REQUEST['listRows'])) {
            $listRows = $_REQUEST['listRows'];
        } else {
            $listRows = '';
        }
		
        
        //数据转换
        if (method_exists($this, '_get_order_sort')) {
            $order_sort_arr = $this->_get_order_sort();
            $order = $order_sort_arr['order'];
            $sort = $order_sort_arr['sort'];
        }
		 if($map['ArticleType'] == 'yes'){
			
			 $order = 'create_time';
		}
		unset($map['ArticleType']);
		  //取得满足条件的记录数
        $count = $model->where($map)->count($model->getPk());
		import("ORG.Util.Page");
       $p = new Page($count, $listRows);

        //分页查询数据
		//dump($map);
        $list = $model->where($map)->order($order . ' ' . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
		//echo $model->getLastsql();

		//$map['__hash__'] = $_REQUEST['__hash__'];
		/*if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}*/

		//分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val) && $val != '') {
                $p->parameter .= "$key/" . urlencode($val) . "/";
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

        //分页显示
		$page = $p->show_admin();

		//dump($page);
        //列表排序显示
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
        $sort = $sort == 'desc' ? 1 : 0; //排序方式
        
        //模板赋值显示
        $this->assign('list', $list);
        $this->assign('sort', $sort);
		$this->assign('map', $map);
        $this->assign('order', $order);
        $this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
        $this->assign("page", $page);
        Cookie::set('_currentUrl_', __SELF__);
        return $list;
    }

    function getSelectData($name, $field = 'id,name', $status = 1)
    {
        $data_obj = D(GROUP_NAME . "/" . $name);
        $list = $data_obj->where("status=" . $status)->getField($field);
        return $list;
    }

    function insert()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (false === $model->create()) {
            $this->error($model->getError());
        }

        //保存当前数据对象
		
        if ($result = $model->add()) { //保存成功
            
            if (method_exists($this, 'card_login') and isset($_POST['cardid']) and !empty($_POST['cardid'])){
                $this->card_login($_POST['cardid']);
            }
            // 回调接口
            if (method_exists($this, '_tigger_insert')) {
                $model->id = $result;
                $this->_tigger_insert($model);
            }
            if (method_exists($this, '_version_insert')) {
                $model->id = $result;
                $this->_version_insert($model);
            }
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success(L('新增成功'));
        } else {
            //失败提示
            $this->error(L('新增失败'));
        }
    }

    public function add()
    {
        $this->display();
    }

    function read()
    {
        $this->edit();
    }

    function edit()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
		
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->find($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    function update()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //dump($model->create());
        // 更新数据
		$result = $model->save();
        if (false !== $result) {
            //echo $model->getlastsql();
            // 回调接口
            /*if (method_exists($this, '_tigger_update')) {
				$model->id = $result;
                $this->_tigger_update($model);
            }*/
            if (method_exists($this, '_product_img_update')) {
				$model->id = $result;
                $this->_product_img_update($model);
            }
            if (method_exists($this, '_save_orderdeallog')) {
				$model->id = $result;
                $this->_save_orderdeallog($model);
            }
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success("更新成功");
        } else {
            //错误提示
            $this->error("更新失败");
        }
    }

    /**
    +----------------------------------------------------------
     * 默认列表选择操作
     *
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    protected function select($fields = 'id,name', $title = '')
    {
        $map = $this->_search();
        //创建数据对象
        $Model = D(GROUP_NAME . "/" . $this->getActionName());
        //查找满足条件的列表数据
        $list = $Model->where($map)->getField($fields);
        $this->assign('selectName', $title);
        $this->assign('list', $list);
        $this->display();
        return;
    }

    public function delete()
    {
        //删除指定记录
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST[$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()) {
                    $this->success(L('删除成功'));
                } else {
                    $this->error(L('删除失败'));
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    // 通过审核
    public function pass()
    {
        //删除指定记录
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (!empty($model)) {
            $pk = $model->getPk();
            if (isset($_REQUEST[$pk])) {
                $id = $_REQUEST[$pk];
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->setField('status', 1)) {
                    $this->assign("jumpUrl", $this->getReturnUrl());
                    $this->success('审核通过！');
                } else {
                    $this->error('审核失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    /**
    +----------------------------------------------------------
     * 默认禁用操作
     *
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    public function forbid()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        $pk = $model->getPk();
        $id = $_GET['id'];
        $condition = array($pk => array('in', $id));
        if ($model->forbid($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态禁用成功！');
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function recycle()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        $pk = $model->getPk();
        $id = $_GET[$pk];
        $condition = array($pk => array('in', $id));
        if ($model->recycle($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态还原成功！');
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin()
    {
        $map = $this->_search();
        $map['status'] = -1;
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    // 检查是否是当前作者
    protected function checkAuthor($name = '')
    {
        if ($_SESSION[C('USER_AUTH_KEY')] != 1) {
            $id = $_GET['id'];
            $name = empty($name) ? $this->getActionName() : $name;
            $Model = D(GROUP_NAME . "/" . $name);
            $Model->find((int)$id);
            if ($Model->member_id != $_SESSION[C('USER_AUTH_KEY')]) {
                $this->error('没有权限！');
            }
        }
    }

	//检查是否符合操作权限
	//author:wwy
	function checkaccess($function,$class){
		$a = strtoupper($function);//方法名
		$b = strtoupper(substr($class,0,-6));//模块名
		foreach($_SESSION['_ACCESS_LIST'] as $k=>$v){
			if($k==strtoupper(GROUP_NAME)){
				$res = array_key_exists($a,$v[$b]);
				if(!$res){
					$this->error('您没有操作权限');
				}else{
					return true;
				}
			}
		}
	}

    /**
    +----------------------------------------------------------
     * 默认恢复操作
     *
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    function resume()
    {
        //恢复指定记录
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        $pk = $model->getPk();
        $id = $_GET['id'];
        $condition = array($pk => array('in', $id));
        if ($model->resume($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态恢复成功！');
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function recommend()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        $pk = $model->getPk();
        $id = $_GET[$pk];
        $condition = array($pk => array('in', $id));
        if ($model->recommend($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('推荐成功！');
        } else {
            $this->error('推荐失败！');
        }
    }

    function unrecommend()
    {
        $model = D(GROUP_NAME . "/" . $this->getActionName());
        $pk = $model->getPk();
        $id = $_GET[$pk];
        $condition = array($pk => array('in', $id));
        if ($model->unrecommend($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('取消推荐成功！');
        } else {
            $this->error('取消推荐失败！');
        }
    }

    /**
    +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    function getReturnUrl()
    {
        //return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
        return __ROOT__ . '?' . C('VAR_GROUP') . '=' . GROUP_NAME . '&' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
    +----------------------------------------------------------
     * Ajax上传页面返回信息
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param array $info 附件信息
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function ajaxUploadResult($info)
    {
        // Ajax方式附件上传提示信息设置
        // 默认使用mootools opacity效果
        $show = '<script language="JavaScript" src="' . WEB_ROOT . '/Public/Admin/Js/mootools.js"></script><script language="JavaScript" type="text/javascript">' . "\n";
        $show .= ' var parDoc = window.parent.document;';
        $show .= ' var result = parDoc.getElementById("' . $info['uploadResult'] . '");';
        if (isset($info['uploadFormId'])) {
            $show .= ' parDoc.getElementById("' . $info['uploadFormId'] . '").reset();';
        }
        $show .= ' result.style.display = "block";';
        $show .= " var myFx = new Fx.Style(result, 'opacity',{duration:600}).custom(0.1,1);";
        if ($info['success']) {
            // 提示上传成功
            $show .= 'result.innerHTML = "<div style=\"color:#3333FF\"><IMG SRC=\"' . APP_PUBLIC_PATH . '/images/ok.gif\" align=\"absmiddle\" BORDER=\"0\"> 文件上传成功！</div>";';
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if (isset($info['uploadResponse'])) {
                $show .= 'window.parent.' . $info['uploadResponse'] . '("' . $info['uploadId'] . '","' . $info['name'] . '","' . $info['savename'] . '");';
            }
        } else {
            // 上传失败
            // 提示上传失败
            $show .= 'result.innerHTML = "<div style=\"color:#FF0000\"><IMG SRC=\"' . APP_PUBLIC_PATH . '/images/update.gif\" align=\"absmiddle\" BORDER=\"0\"> 上传失败：' . $info['message'] . '</div>";';
        }
        $show .= "\n" . '</script>';
        //$this->assign('_ajax_upload_',$show);
        header("Content-Type:text/html; charset=utf-8");
        exit($show);
        return;
    }

    /**
    +----------------------------------------------------------
     * 下载附件
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    public function download()
    {
        $id = $_GET['id'];
        $Attach = D(GROUP_NAME . "/" . "Attach");
        if ($Attach->getById($id)) {
            $filename = $Attach->savepath . $Attach->savename;
            if (is_file($filename)) {
                $showname = auto_charset($Attach->name, 'utf-8', 'gbk');
                $Attach->where('id=' . $id)->setInc('download_count');
                import("ORG.Net.Http");
                Http::download($filename, $showname);
            }
        }
    }

    /**
    +----------------------------------------------------------
     * 默认删除附件操作
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    function delAttach()
    {
        //删除指定记录
        $attach = D(GROUP_NAME . "/" . "Attach");
        $pk = $attach->getPk();
        $id = $_REQUEST[$pk];
        $condition = array($pk => array('in', $id));
        if ($attach->where($condition)->delete()) {
            $this->ajaxReturn($id, L('_DELETE_SUCCESS_'), 1);
        } else {
            $this->error(L('_DELETE_FAIL_'));
        }
    }


    public function sort()
    {
        $node = D(GROUP_NAME . "/" . $this->getActionName());
        if (!empty($_GET['sortId'])) {
            $map = array();
            $map['id'] = array('in', $_GET['sortId']);
            $sortList = $node->where($map)->order('sort asc')->select();
        } else {
            $sortList = $node->order('sort asc')->select();
        }
        $this->assign("sortList", $sortList);
        $this->display();
        return;
    }

    public function saveSort()
    {
        $seqNoList = $_POST['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $model = D(GROUP_NAME . "/" . $this->getActionName());
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val[0];
                $model->sort = $val[1];
                $result = $model->save();
                if (false === $result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }

    protected function getAttach($module = '')
    {
        $module = empty($module) ? $this->getActionName() : $module;
        //读取附件信息
        $id = $_REQUEST['id'];
        $Attach = D(GROUP_NAME . "/" . 'Attach');
        $attachs = $Attach->where("module='" . $module . "' and record_id=$id")->select();
        //模板变量赋值
        $this->assign("attachs", $attachs);
    }


    /*
     *图片上传，整合了附件上传，编辑上传，可以多图片上传
     *modify by yyc
     *time 12/05/23
     */
    public function multiupload($module = '', $recordId = '')
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        // 自定义上传规则
        $upload = $this->_upload_init($upload);
        $uploadRecord = true;
        // 记录上传成功ID
        $uploadId = array();
        $savename = array();
        $return = array('err' => '', 'msg' => '');

        //执行上传操作
        if (!$upload->upload()) {
            $return['err'] = $upload->getErrorMsg();

        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $remark = $_POST['remark'];
            //保存附件信息到数据库
            if ($uploadRecord) {
                $Attach = D(GROUP_NAME . "/" . 'Attach');
                //启动事务
                $Attach->startTrans();
            }
            if (!empty($_POST['_uploadFileTable'])) {
                //设置附件关联数据表
                $module = $_POST['_uploadFileTable'];
            }
            if (!empty($_POST['_uploadRecordId'])) {
                //设置附件关联记录ID
                $recordId = $_POST['_uploadRecordId'];
            }
            if (!empty($_POST['_uploadFileVerify'])) {
                //设置附件验证码
                $verify = $_POST['_uploadFileVerify'];
            }
            if (!empty($_POST['_uploadUserId'])) {
                //设置附件上传用户ID
                $userId = $_POST['_uploadUserId'];
            } else {
                $userId = isset($_SESSION[C('USER_AUTH_KEY')]) ? $_SESSION[C('USER_AUTH_KEY')] : 0;
            }
            foreach ($uploadList as $key => $file) {
                $savename[] = $file['savename'];
                if ($uploadRecord) {
                    // 附件数据需要保存到数据库
                    //记录模块信息
                    unset($file['key']);
                    $file['module'] = $module;
                    $file['record_id'] = $recordId ? $recordId : 0;
                    $file['user_id'] = $userId;
                    $file['verify'] = $verify ? $verify : '0';
                    $file['remark'] = $remark[$key] ? $remark[$key] : ($remark ? $remark : '');
                    $file['status'] = 1;
                    $file['create_time'] = time();
                    if (empty($file['hash'])) {
                        unset($file['hash']);
                    }
                    //保存附件信息到数据库
                    $uploadId[] = $Attach->add($file);
                }
            }
            if ($uploadRecord) {
                //提交事务
                $Attach->commit();
            }
            $uploadSuccess = true;
            $ajaxMsg = '';
        }
        $info = $upload->getUploadFileInfo();
        $return['msg'] = '!' . C('ATTACH_DOMAIN') . '/' . 'Attach/' . $info[0]['savename'];
        if ($this->isAjax() && isset($_POST['_uploadFileResult'])) {
            // Ajax方式上传参数信息
            $info = Array();
            $info['success'] = $uploadSuccess;
            $info['message'] = $ajaxMsg;
            //设置Ajax上传返回元素Id
            $info['uploadResult'] = $_POST['_uploadFileResult'];
            if (isset($_POST['_uploadFormId'])) {
                //设置Ajax上传表单Id
                $info['uploadFormId'] = $_POST['_uploadFormId'];
            }
            if (isset($_POST['_uploadResponse'])) {
                //设置Ajax上传响应方法名称
                $info['uploadResponse'] = $_POST['_uploadResponse'];
            }
            if (!empty($uploadId)) {
                $info['uploadId'] = implode(',', $uploadId);
            }
            $info['savename'] = implode(',', $savename);
            $this->ajaxUploadResult($info);

        } else {
            echo json_encode($return);
        }
        return;
    }

/*
*普通上传
*
*/
//modify by yyc  2012/5/30
//修改默认为logo的错误
    public function couponupload()
    {
		
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload = $this->_upload_init($upload);
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            //$info = $upload->getUploadFileInfo();
            $uploadList = $upload->getUploadFileInfo();
            //echo '<pre>';print_r($_FILES);
            //echo '<pre>';print_r($uploadList);exit;
            if ($uploadList){
                if ($_FILES['coupon_logo']['name']){
                    $_POST['coupon_logo'] = $uploadList[0]['savename'];
                    if ($_FILES['coupon_pic']['name']){
                        $_POST['coupon_pic'] = $uploadList[1]['savename'];
                    }
                }else{
                    if ($_FILES['coupon_pic']['name']){
                        $_POST['coupon_pic'] = $uploadList[0]['savename'];
                    }
                    if ($_FILES['logo']['name']){
                        $_POST['logo'] = $uploadList[0]['savename'];
                    }
                }
            }
            //$_POST[$uploadName] = $info[0]['savename'];
        }
    }
    
    
    
/*
*普通上传
*
*/
//modify by yyc  2012/5/30
//修改默认为logo的错误
    public function upload($uploadName = 'logo')
    {
        if (!empty($_FILES[$uploadName]['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            $upload = $this->_upload_init($upload);
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            } else {
                $info = $upload->getUploadFileInfo();
                $_POST[$uploadName] = $info[0]['savename'];
            }
        }
    }

//获取自定义配置
    public function getCustomConfig($name)
    {
        $data = R('Design/ConfigType/getConfigData', array($name));
        return $data;
    }

//根据id获取name
    public function getInfoById($model, $id, $group = GROUP_NAME, $field = 'name')
    {
        $m = D($group . "/" . $model);
        $data = $m->where($m->getPk() . '=' . $id)->field($field)->find();
        return ($data);
    }
    
//生成用户显示的订单号
    public function get_orderid($id){
        $orderid = $id+987;
        $sum = 0;
	    for($ii=0;$ii<strlen($orderid);$ii++){
	        $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum%10;
        return $orderid.$str;
    }


 //还原订单号
    public function get_true_orderid($id){
        $orderid = substr(trim($id),0,-1);
        $orderid = $orderid-987;
        return $orderid;
    }
	
    //短信接口
    function curl_sms($post = '' , $charset = null, $num=1,$type = null){
    
    	$this->addCodeLog('发送短信', '开始,通道:'.$num);
    
    	$datamobile = array('130','131','132','155','156','186','185');
    	$mobile = $post['phones'];
    	/*$submobile = substr($post['phones'],0,3);
    	 $content = $post['content'] = str_replace("联通", "联_通", $post['content']);
    	if(in_array($submobile,$datamobile)){
    	$content = $post['content'] = $post['content']."  回复TD退订";
    	}*/

        // dingjb 2015-09-16 14:27:21 容联云短信通道
        // 首先检测是否为 容联云
        if ($num == 4) {
            // 第四通道：容联云短信

            // 检测手机号和短信内容
            if (!array_key_exists('phones', $post) || !array_key_exists('content', $post)) {
                return false;
            }

            $to     = $post['phones'];  // 目标手机
            $datas  = $post['content']; // 模板数据
            $templateId = $type == null ? 1 : $type; // 模板ID

            // 组装日志信息
            $this->addCodeLog('发送短信', '容联云模板:【'.$templateId.'】, 电话:'.$post['phones'].', 模板数据:'.json_encode($post['content']));
            // 发送信息
            $this->sendYunSMS($to, $datas, $templateId);
            // 删除发送 Session
            $this->submitCodeLog('发送短信');

            return true;
        }

        if($type!=1){
    		$post['content'] .= " 【携车网】";
    	}else{
    		$post['content'] .= " 【携车网府上养车】";
    	}
    	$this->addCodeLog('发送短信', '内容:'.$post['content'].'电话：'.$mobile);
    	if($num==1){
    		//第一通道
    		$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
    		$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
    		$res = $client->__soapCall('SendMsg',array('parameters' => $param));

    		$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
    		$this->submitCodeLog('发送短信');

        }elseif($num==2){
    		//第二通道
    		$url = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage';
    		$header = array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8");
    		$param = http_build_query(array(
    				'account'=>'sdk_xieche',
    				'password'=>'fqcd123223',
    				'destmobile'=>$mobile,
    				'msgText'=>$post['content'],
    				'sendDateTime'=>''
    		)
    		);
    
    		$res = $this->curl($url,$param,$header);
    
    		$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
    		$this->submitCodeLog('发送短信');
    		return $res;
    	}elseif($num == 3){
    		//第三通道
    		$url = 'http://58.83.147.92:8080/qxt/smssenderv2';
    		$header = array("Content-Type: application/x-www-form-urlencoded;charset=GBK");
    		$param = http_build_query(array(
    				'user'=>'zs_donghua',
    				'password'=>md5('121212'),
    				'tele'=>$mobile,
    				'msg'=>iconv("utf-8","gbk//IGNORE",$post['content']),
    		)
    		);
    		$res = $this->curl($url,$param,$header);
    
    		$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
    		$this->submitCodeLog('发送短信');
    		return $res;
    	} else {
    		/*
    		 * 修改需求：验证码两个一起发
    		* bright
    		*/
    		$post['content'] .= " 【携车网】";
    		$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
    		$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
    		$client->__soapCall('SendMsg',array('parameters' => $param));
    
    
    		$url = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage';
    		$header = array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8");
    		$param = http_build_query(array(
    				'account'=>'sdk_xieche',
    				'password'=>'fqcd123223',
    				'destmobile'=>$mobile,
    				'msgText'=>$post['content'],
    				'sendDateTime'=>''
    		)
    		);
    		$res = $this->curl($url,$param,$header);
    
    		$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
    		$this->submitCodeLog('发送短信');
    		return $res;
    	}
    }

    /**
     * 容联云短信发送
     * @author dingjb
     * @date  2015-09-16 14:07:19
     *
     * @param $to       目标手机
     * @param $datas    数据
     * @param $tempId   模板ID
     * @return boolean  是否成功
     */
    public function sendYunSMS($to,$datas,$tempId)
    {
        // 导入 容联云 SDK
        vendor('YunSms.CCPRestSmsSDK', 'Lib/Vendor/');

        // 初始化参数
        $accountSid     = C('accountSid');
        $accountToken   = C('accountToken');
        $appId          = C('appId');
        $serverIP       = C('serverIP');
        $serverPort     = C('serverPort');
        $softVersion    = C('softVersion');

        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);

        // Error: 无返回数据
        if($result == NULL ) {
            $this->addCodeLog('发送短信错误', '返回值:无返回数据');
            return false;
        }

        // Error: 发送错误
        if($result->statusCode != 0) {
            $this->addCodeLog('发送短信错误', '返回值:'.var_export($result, true));
            return false;
        } else { // Success: 发送成功
            $this->addCodeLog('发送短信', '返回值:'.var_export($result, true));
            return true;
        }
    }
    
    public function curl($url, $post = NULL,$host=NULL) {
    	$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
    	$cookieFile = NULL;
    	$hCURL = curl_init();
    	curl_setopt($hCURL, CURLOPT_URL, $url);
    	curl_setopt($hCURL, CURLOPT_TIMEOUT, 30);
    	curl_setopt($hCURL, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($hCURL, CURLOPT_USERAGENT, $userAgent);
    	curl_setopt($hCURL, CURLOPT_FOLLOWLOCATION, TRUE);
    	curl_setopt($hCURL, CURLOPT_AUTOREFERER, TRUE);
    	curl_setopt($hCURL, CURLOPT_ENCODING, "gzip,deflate");
    	curl_setopt($hCURL, CURLOPT_HTTPHEADER,$host);
    	if ($post) {
    		curl_setopt($hCURL, CURLOPT_POST, 1);
    		curl_setopt($hCURL, CURLOPT_POSTFIELDS, $post);
    	}
    	$sContent = curl_exec($hCURL);
    	if ($sContent === FALSE) {
    		$error = curl_error($hCURL);
    		curl_close($hCURL);
    
    		throw new \Exception($error . ' Url : ' . $url);
    	} else {
    		curl_close($hCURL);
    		return $sContent;
    	}
    }


		 //短信接口
     function curl_smstype($post='',$type,$charset = null ){
		$datamobile = array('130','131','132','155','156','186','185');
		$submobile = substr($post['phones'],0,3);
		$post['content'] = str_replace("联通", "联_通", $post['content']);
		if(in_array($submobile,$datamobile)){
			$post['content'] = $post['content']."  回复TD退订";
		}

		if($type=='2'){
			$post['content'] .= " 【携车网】";
			$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post['phones'],"Msg"=>$post['content'],"Channel"=>"1");
			$p = $client->__soapCall('SendMsg',array('parameters' => $param));
			$res = $p;
		}else{
			$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh21007</account><password>49e96c9b07f0628fec558b11894a9e8f</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";

			$url = 'http://www.10690300.com/http/sms/Submit';
			$curl = curl_init($url );
			if( !is_null( $charset ) ){
				curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
			}
			if( !empty( $post ) ){
				$xml_data = 'message='.urlencode($xml_data);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($curl);
			curl_close($curl);
		}
		
        return $res;
    }


//生成表格图片
	public function createtableimg($tables_arr,$folder,$img_name){
	    //$img_name = 'test33.png';
        import("@.ORG.Util.Image");
        $img_path = '../UPLOADS/Product/'.$folder;
        mk_dir($img_path);
        $img_name = $img_path.'/'.$img_name;
        Image::getimgtable($tables_arr,$img_name);
    }   
/*
*-------------------------end
*-----------------------------
*/

	function request($index = NULL, $xss_clean = FALSE)
	{
		// Check if a field has been provided
		if ($index === NULL AND ! empty($_POST))
		{
			$post = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_REQUEST) as $key)
			{
				$post[$key] = $this->_fetch_from_array($_REQUEST, $key, $xss_clean);
			}
			return $post;
		}
		
		return $this->_fetch_from_array($_REQUEST, $index, $xss_clean);
	}

	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
	{
		if ( ! isset($array[$index]))
		{
			return FALSE;
		}
//		$array[$index] = htmlspecialchars($array[$index]);//转换html标签
//		$array[$index] = addslashes($array[$index]);

		if ($xss_clean === TRUE)
		{
			return $this->security->xss_clean($array[$index]);
		}

		return $array[$index];
	}

	public function xss_clean($str, $is_image = FALSE)
	{
		/*
		 * Is the string an array?
		 *
		 */
		if (is_array($str))
		{
			while (list($key) = each($str))
			{
				$str[$key] = $this->xss_clean($str[$key]);
			}

			return $str;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function randString($len=6,$type='',$addChars='') {
		$str ='';
		switch($type) {
			case 0:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		if($type!=4) {
			$chars   =   str_shuffle($chars);
			$str     =   substr($chars,0,$len);
		}else{
			// 中文随机字
			for($i=0;$i<$len;$i++){
			  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
			}
		}
		return $str;
	}

	/*
		@author:ysh
		@function:得到汽车信息
		@time:2013/5/24
	*/
	function get_car_info($brand_id,$series_id,$model_id) {
		if ($brand_id){
			$model_carbrand = D('Carbrand');
			$map_b['brand_id'] = $brand_id;
			$brand = $model_carbrand->where($map_b)->find();
		}
		if ($series_id){
			$model_carseries = D('Carseries');
			$map_s['series_id'] = $series_id;
			$series = $model_carseries->where($map_s)->find();
		}
		if ($model_id){
			$model_carmodel = D('Carmodel');
			$map_m['model_id'] = $model_id;
			$model = $model_carmodel->where($map_m)->find();
		}
		return $brand['brand_name']." ".$series['series_name']." ".$model['model_name'] ;
	}

	/*
	*@name 微信发送客服消息接口
	*@author ysh
	*@time 2014/3/26
	*/
	function weixin_api($data) {

		/*$memcache_access_token = S('WEIXIN_access_token');
		if($memcache_access_token) {
			$access_token = $memcache_access_token;
		}else {
			$appid = "wx43430f4b6f59ed33";
			$secret = "e5f5c13709aa0ae7dad85865768855d6";
		
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
			$result = file_get_contents($url);
			$result = json_decode($result,true);
			$access_token = $result['access_token'];
			S('WEIXIN_access_token',$access_token,7200);
		}*/
		$access_token = $this->getWeixinToken();
		$body = array(
		"touser"=>$data['touser'],
			"msgtype"=>"news", 
			"news" => array(
			"articles" => array(
				array(
							"title"=>"%title%", 
							"description"=>"%description%",
							"url" =>$data['url'],
						),
				)
			),
		);
		$json_body = json_encode($body);
		$search = array('%title%' , '%description%');
		$replace = array($data['title'],$data['description']);
		$json_body = str_replace($search , $replace , $json_body);


		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);   //只需要设置一个秒的数量就可以
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		if($result['errcode'] != 0) {
			S('WEIXIN_access_token',NUll);
			$this->weixin_api($data);
		}else {
			return $result;
		}
	}


	/*  @author:chf
		@function:发送数据(平安)($url:http://www.xieche.com.cn/test/ur)($data: $data['img']="测试平安";)
		@time:2014-01-16;
	*/
	function Api_toPA($url,$data){
		$host = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	/*  @author:wwy
		@function:发送数据(百车宝)($url:http://www.xieche.com.cn/test/ur)($data: $data['img']="测试平安";)
		@time:2014-01-16;
	*/
	function Api_toHUN($url,$data,$header){
		$host = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
	//添加操作日志
	function addOperateLog($array){
		$url_array = explode('/',$_SERVER['REQUEST_URI']);
		$data['control'] = $url_array['4'];
		$function = explode('?',$url_array['5']);
		$data['function'] = $function['0'];
		$data['create_time'] = time();
		$data['log'] = date('Y-m-d H:i:s',time()).$_SESSION['loginAdminUserName'].$array['log'];
		$data['operate_id'] = $array['operate_id'];
		$data['oid'] = $array['oid'];
		//print_r($data);exit;
	
		$model_operatelog =  M("tp_xieche.operatelog","xc_");
		$a = $model_operatelog->data($data)->add();
		//var_dump($a);
		//echo $model_operatelog->getLastsql();
	}
	//获取操作日志列表
	function getloglist($array){
		if($array['control']){
			$map['control'] = $array['control'];
		}
		if($array['function']){
			$map['function'] = $array['function'];
		}
		if($array['operate_id']){
			$map['operate_id'] = $array['operate_id'];
		}
		if($array['oid']){
			$map['oid'] = $array['oid'];
		}
	
		$model_operatelog =  M("tp_xieche.operatelog","xc_");
		$list = $model_operatelog->where($map)->order('id ASC')->select();
		//print_r($list);
		//echo $model_operatelog->getLastsql();
		return $list;
	}
	//代码日志跟踪
	function addCodeLog($type,$msg,$oid=''){
		if(!$type){
			return false;
		}
		$mCodeLog = D('logcode');
		$mErrorLog = D('logerror');
		if ( isset($_SESSION['codeLogType']) ) { // 是否存在日志类型
			if ($_SESSION['codeLogType'] == $type) { // Session 中日志类型与当前操作的日志类型是否相同
				if ( !empty($_SESSION['codeLogId'] ) ) { // 日志 ID 是否存在
					$where = array('id'=>$_SESSION['codeLogId']);
					$res = $mCodeLog->field('log')->where($where)->find();
					$msg = $res['log'].'||'.date('Y-m-d H:i:s',time()).$msg;
					$data = array(
							'type'=>$type,
							'oid'=>$oid,
							'log'=>$msg
					);
					$mCodeLog->where($where)->save($data);
				}else{
					$data = array(
							'type'=>$type,
							'oid'=>$oid,
							'log'=>date('Y-m-d H:i:s',time()).'codeLogId empty: '.$msg,
							'insertTime'=>time()
					);
					$mErrorLog->add($data);
				}
			}else{
				$data = array(
						'type'=>$type,
						'oid'=>$oid,
						'log'=>date('Y-m-d H:i:s',time()).'type error: '.$msg,
						'insertTime'=>time()
				);
				$mErrorLog->add($data);
			}
		}else{
			//首次
			$_SESSION['codeLogType'] = $type;
			$data = array(
					'type'=>$type,
					'oid'=>$oid,
					'log'=>date('Y-m-d H:i:s',time()).$msg,
					'insertTime'=>time()
			);
				
			$codeId = $mCodeLog->add($data);
			if ($codeId) {
				$_SESSION['codeLogId'] = $codeId;
			}
				
		}
		return true;
	}
	//代码日志跟踪提交
	function submitCodeLog($type){
		unset($_SESSION['codeLogId'],$_SESSION['codeLogType']);
		return true;
	}

	//根据券码换算金额
	function get_codevalue($replace_code){
		if (is_numeric($replace_code)){
			if($replace_code==0){$codevalue = 0;}else{
				//4位券码判定
				if(count(str_split($replace_code))==4){
					$codevalue = 20; 
				}else{
					$codevalue = 99; 
				}
			}
		}else{
			$first = substr($replace_code,0,1);
			$first = strtolower($first);
			switch ($first) {
				case a:
					$codevalue = 300;
					break;
				case b:
					$codevalue = 20;
					break;
				case g:
					$codevalue = 20;
					break;
				case f:
					$codevalue = 99;
					break;
				case k:
					$codevalue = 100;
					break;
				case l:
					$codevalue = 99;
					break;
				case n:
					$codevalue = 99;
					break;
				default:
				$codevalue = 99;
				break;
			}
		}
		if(!$replace_code){
			$codevalue = 0;
		}
		//通用码016888
		if($replace_code=='016888'){
			$codevalue = 20;
		}
		return $codevalue;
	}

	//根据订单类型换算减免金额
	function get_typeprice($order_type,$oil_price,$jilv_price,$kongtiao_price,$kongqi_price){
		$item_oil = D('new_item_oil');
		switch ($order_type) {
			//10元打车代金券
			case 4:
			$typeprice['typeprice'] = 0;
			break;
			//上门汽车保养专业服务
			case 6:
			$typeprice['typeprice'] = 99;
			break;
			//府上养车-黄壳机油套餐
			case 7:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'49'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-蓝壳机油套餐
			case 8:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'47'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-灰壳机油套餐
			case 9:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'45'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-金美孚机油套餐
			case 10:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'50'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;

			//爱代驾高端车型小保养套餐
			case 11:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'50'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			//$typeprice = $oil_price+$jilv_price+99-$package_info['price'];
			//echo 'oil_price='.$oil_price.'jilv_price='.$jilv_price;exit;
			break;
			
			//爱代驾中端车型小保养套餐
			case 12:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//好车况套餐
			case 13:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好车况套餐:38项全车检测+7项细节养护';
			break;

			//好空气套餐
			case 14:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐:更换空调滤';
			break;

			//好动力套餐
			case 15:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好动力套餐:节气门清洗+38项全车检测+7项细节养护';
			break;

			//保养服务+检测+养护
			case 16:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '套餐:上门保养人工服务+38项全车检测+7项细节养护';
			break;

			//矿物质油保养套餐+检测+养护
			case 17:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;
			
			//半合成油保养套餐+检测+养护  
			case 18:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;

			//全合成油保养套餐+检测+养护
			case 19:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;

			//38项检测+7项细节养护(光大)
			case 21:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '套餐:38项检测+7项细节养护';
			break;

			//光大168
			case 22:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//光大268 
			case 23:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//光大368
			case 24:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'50'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//浦发199
			case 25:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//浦发299
			case 26:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//浦发399
			case 27:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//全车检测38项(淘38)
			case 28:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '全车检测38项(淘38)';
			break;

			//细节养护7项(淘38)
			case 29:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '细节养护7项(淘38)';
			break;

			//更换空调滤工时(淘38)
			case 30:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '更换空调滤工时(淘38)';
			break;

			//更换雨刮工时(淘38)
			case 31:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '更换雨刮工时(淘38)';
			break;

			//小保养工时(淘38)
			case 32:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '小保养工时(淘38)';
			break;

			//好空气套餐(奥迪宝马奔驰)
			case 33:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐(奥迪宝马奔驰)';
			break;

			//补配件免人工订单
			case 34:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '补配件免人工订单';
			break;

			//好车况（市场部推广）
			case 35:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '好车况套餐:38项全车检测+7项细节养护';
			break;

			//大众点评199
			case 36:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//大众点评299
			case 37:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//大众点评399
			case 38:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//防雾霾1元
			case 48:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '防雾霾1元套餐';
			break;

			//防雾霾8元
			case 49:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '防雾霾8元套餐';
			break;

			//好车况套餐（大众点评）
			case 50:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '大众点评好车况套餐:38项全车检测+7项细节养护';
			break;

			//保养服务+检测+养护（大众点评）
			case 51:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '大众点评套餐:上门保养人工服务+38项全车检测+7项细节养护';
			break;

			//好空气套餐（平安财险）
			case 52:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐:更换空调滤';
			break;

			//好动力套餐（后付费）
			case 53:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好动力套餐:节气门清洗+38项全车检测+7项细节养护';
			break;

			//发动机舱精洗套餐（淘）
			case 54:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '发动机舱精洗套餐';
			break;

			//好空气套餐(奥迪宝马奔驰后付费)
			case 55:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐';
			break;
                        
                        //黄壳199套餐（预付费）
			case 56:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//蓝壳299套餐（预付费）
			case 57:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//灰壳399套餐（预付费）
			case 58:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
                    
			//发动机机舱精洗套餐（预付费）
			case 59:
			$typeprice['typeprice'] = 99;
			break;
        
            //268大保养（预付费）
			case 60:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $oil_price+$jilv_price+$kongtiao_price+$kongqi_price+99-$package_info['price'];
			break;
            //378大保养（预付费）
			case 61:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $oil_price+$jilv_price+$kongtiao_price+$kongqi_price+99-$package_info['price'];
			break;
     
            //空调清洗套餐（后付费）
            case 62:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '空调清洗套餐';
            break;

            //空调清洗套餐（免费）
            case 63:
            $typeprice['typeprice'] = 99;
            $typeprice['remark'] = '空调清洗套餐';
            break;

            //好动力套餐（免费）
            case 64:
            $typeprice['typeprice'] = 99;
            $typeprice['remark'] = '好动力套餐';
            break;

            //轮毂清洗套餐（预付费）
            case 65:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '轮毂清洗套餐';
            break;

            //空调清洗（点评到家）
            case 66:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '空调清洗套餐';
            break;

            //汽车检测和细节养护套餐（点评到家）
            case 67:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '汽车检测和细节养护套餐';
            break;
        
            //好空气套餐(点评到家)
			case 68:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐:更换空调滤';
			break;

			// 保养人工费工时套餐（点评到家）
			case 69:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '套餐:上门保养人工服务+38项全车检测+7项细节养护';
			break;
			// 9.8细节养护与检测（微信）
			case 70:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '套餐:38项全车检测+7项细节养护';
			break;

            //空调清洗套餐（预付费）
            case 71:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '空调清洗套餐';
            break;

            //发动机舱除碳（预付费）
            case 72:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '发动机舱除碳';
            break;

            //发动机舱除碳（后付费）
            case 73:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '发动机舱除碳';
            break;

			default:
			$typeitem = null;
			break;
		}
		return $typeprice;
	}

	/*获取微信token
	 * auth bright
	 */
	function getWeixinToken(){
		$mToken = D('weixin_token');
		$res = $mToken->field('token')->where( array(
				'id'=>1
		) )->find();
		if ($res) {
			return $res['token'];
		}else{
			return null;
		}
	}

	/*配件回收记日志
	 * auth wwy
	 */
	function get_itemback_log($data){
		$log = D('itemback_log');
		$id = $log->add($data);
		if($_SESSION['authId']==1){
			//echo $log->getLastsql();exit;
		}
		if ($id) {
			return $id;
		}else{
			return null;
		}
	}
}

?>