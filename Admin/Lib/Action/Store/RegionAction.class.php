<?php



class RegionAction extends CommonAction {

    function _filter(&$map) {
        $map['region_name'] = array('like', "%" . $this->_request('region_name') . "%");
        $parent_id = $this->_request('parent_id', 'intval', 1);
        $parent_info = D('Store/Region')->find($parent_id); //父地区
        $this->assign('parent_info', $parent_info);
        $map['parent_id'] = $parent_id;
    }

    function _before_add() {
        $region_type = $this->_request('region_type', 'intval', 1);
        $region_parent = $this->_request('parent_id', 'intval', 0);
        $region_model = D('Store/Region');
        $parent_info = $region_model->find($region_parent); //父地区
        $this->assign('parent_info', $parent_info);
        $this->assign('region_type', $region_type);
        $this->assign('region_parent', $region_parent);
    }

    /**
      +----------------------------------------------------------
     * 获取地区列表
      +----------------------------------------------------------
     * @param int $type 地区类型，默认1为省级，2为市级，3为县区级
     * @param int $pid 上级地区ID，默认1为中国
     * @param string $fields 获取的字段
      +----------------------------------------------------------
     * @return array
      +----------------------------------------------------------

     */
    public function getRegion($type = 1, $pid = 0, $fields = 'id,region_name') {
        $region_model = D('Store/Region');
		//dump($region_model);
        $condition['parent_id'] = $pid;
        $condition['region_type'] = $type;
        $condition['status'] = 1;
        $region = $region_model->field($fields)->where($condition)->select();
		//echo $region_model->getlastsql();
        return $region;
	  // dump($region);
    }

    //ajax方式返回
    public function ajaxRegion() {
        if ($this->isAjax()) {
            $type = $this->_get('region_type', 'intval', 1);
            $pid = $this->_get('parent_id', 'intval', 0);
            $this->ajaxReturn($this->getRegion($type, $pid));
        } else {
            exit('not ajax request');
        }
    }

    //取得指定ID的地区名称
    public function getRegionName($region_id) {
        $region_cache = include($this->getCacheFilename('region'));
        return $region_cache[$region_id]['region_name'];
    }

}

?>
