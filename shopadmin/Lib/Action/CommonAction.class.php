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
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $map['shop_id'] = $_SESSION['shop_id'];
        }
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
        //取得满足条件的记录数
        $count = $model->where($map)->count($model->getPk());
		import("ORG.Util.Page");
        //创建分页对象
        if (!empty($_REQUEST['listRows'])) {
            $listRows = $_REQUEST['listRows'];
        } else {
            $listRows = '';
        }
        $p = new Page($count, $listRows);
        //数据转换
        if (method_exists($this, '_get_order_sort')) {
            $order_sort_arr = $this->_get_order_sort();
            $order = $order_sort_arr['order'];
            $sort = $order_sort_arr['sort'];
        }
        
        //分页查询数据
		//dump($map);
        $list = $model->where($map)->order($order . ' ' . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
        //echo $model->getlastsql();
        //分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        //数据转换
        if (method_exists($this, '_trans_data')) {
            $list = $this->_trans_data($list);
        }

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
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success(L('更新成功'));
        } else {
            //错误提示
            $this->error(L('更新失败'));
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
}

?>