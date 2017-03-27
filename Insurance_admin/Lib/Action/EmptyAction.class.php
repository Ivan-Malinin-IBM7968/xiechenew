<?php

class EmptyAction extends Action {
    public function _empty($method) {
        $this->assign('message','000访问的页面不存在！');
        $this->display(C('TMPL_ACTION_ERROR'));
    }


}
?>