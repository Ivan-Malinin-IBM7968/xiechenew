<?php 
// 附件模块
class AttachAction extends CommonAction {
    protected function _upload_init($upload) {
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        $upload->allowExts = explode(',', strtolower(C('UPLOAD_FILE_EXT')));
        $upload->savePath =  C('UPLOAD_ROOT').'/Attach/';
        $upload->saveRule = 'uniqid';
        $upload->uploadReplace = false;
        return $upload;
    }
    

    


    function top() {
            //置顶指定记录
            $Attach = D(GROUP_NAME."/"."Attach");
            $id = $_REQUEST['id'];
            if (isset($id)) {
                $condition = array('id' => array('in', $id));
                if ($Attach->where($condition)->setField('is_top', array('exp', '1-is_top'))) {
                    $this->assign("jumpUrl", $this->getReturnUrl());
                    $this->success('置顶成功！');
                } else {
                    echo $Attach->_sql();exit;
                    $this->error('置顶失败');
                }
            } else {
                $this->error('非法操作');
            }
        }
}
?>