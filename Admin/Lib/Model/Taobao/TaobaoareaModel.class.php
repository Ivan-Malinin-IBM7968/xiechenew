<?php
/**
 * TaobaoareaModel 淘宝区域模型
 *
 * @uses CommonModel
 * @modify_time 2015-09-25
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoareaModel extends CommonModel
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
    protected $tableName = 'taobao_area';

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
    }

    /**
     * getProvinceList 获取省列表
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return mixed
     */
    public function getProvinceList()
    {
        return $this->where('serve_area_level=2')->select();
    }

    /**
     * getChildAreaList 获取子区域列表
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
        return $this->where($where)->select();
    }
}
