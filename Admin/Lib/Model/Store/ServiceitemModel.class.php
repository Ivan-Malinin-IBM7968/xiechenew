<?php

/*
 * 服务分类模型
 * time 2012/5/27
 */

class ServiceitemModel extends CommonModel {

    protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';
    
    protected $_auto = array(
        array('create_time', 'time', 3, 'function'),
        array('update_time', 'time', 2, 'function'),
    );
    
    public function get_servicename($service_ids){
        $map['id'] = array('in',$service_ids);
        $service_info = $this->where($map)->select();
        return $service_info;
    }
}

?>