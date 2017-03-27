<?php
/**
 * TaobaoserverangeModel 淘宝预约服务范围模型
 *
 * @uses CommonModel
 * @modify_time 2015-09-30
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoserverangeModel extends CommonModel
{
    /**
     * dbName 
     * 
     * @var string
     * @access protected
     */
    protected $dbName = 'tp_xieche';

    /**
     * tablePrefix
     *
     * @var string
     * @access protected
     */
    protected $tablePrefix = 'xc_';

    /**
     * tableName
     *
     * @var string
     * @access protected
     */
    protected $tableName = 'taobao_serve_range';

    /**
     * __construct
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->areaModel = D('Admin/Taobaoarea');
    }

    /**
     * getCityList 获取当前服务范围市区列表
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return mixed
     */
    public function getCityList()
    {
        $join = array(
            'LEFT JOIN tp_xieche.xc_taobao_area b ON a.parent_area_id = b.area_id',
        );
        $group = 'a.parent_area_id';
        $field = array(
            'a.parent_area_id',
            'a.serve_range_id',
            'b.name',
        );
        $cityList = $this
            ->alias('a')
            ->join($join)
            ->group($group)
            ->field($field)
            ->select();
        return $cityList;
    }

    /**
     * getChildAreaList 获取当前市所有分组
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $parentAreaId
     * @access public
     * @return mixed
     */
    public function getChildAreaList($parentAreaId)
    {
        $where = array(
            'parent_area_id' => $parentAreaId,
        );
        $field = array(
            'name',
            'serve_range_id' => 'area_id',
        );
        return $this->where($where)->field($field)->select();
    }

    /**
     * getStreetList
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveRangeId
     * @access public
     * @return mixed
     */
    public function getStreetList($serveRangeId)
    {
        $childAreaIds = $this->getFieldByServeRangeId($serveRangeId, 'child_area_ids');
        $childAreaIds = json_decode($childAreaIds, true);
        foreach ($childAreaIds as $childAreaId) {
            $list[] = $this->areaModel->getFieldByAreaId($childAreaId, 'name');
        }
        return $list;
    }
}
