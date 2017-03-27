<?php
/**
 * TaobaoAction 淘宝预约设置后台
 *
 * @uses CommonAction
 * @modify_time 2015-09-25
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoAction extends CommonAction
{	
    /**
     * __construct
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
	public function __construct() {
		parent::__construct();
        $this->areaModel = D('Taobao/Taobaoarea');
        $this->serveRangeModel = D('Taobao/Taobaoserverange');
        $this->serveScheduleModel = D('Taobao/Taobaoserveschedule');
	}

    /**
     * index 淘宝预约设置首页
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function index()
    {
        $this->assign('level2', $this->areaModel->getProvinceList());
        $this->assign('cityList', $this->serveRangeModel->getCityList());
        $this->display('range');
    }

    /**
     * getChildAreaAPI 获取子地区列表
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function getChildAreaAPI()
    {
        $this->ajaxReturn($this->areaModel->getChildAreaList($_REQUEST['areaId']), 'success', 1);
    }

    /**
     * getChildGroupAPI 获取城市分组列表
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function getChildGroupAPI()
    {
        $this->ajaxReturn($this->serveRangeModel->getChildAreaList($_REQUEST['areaId']), 'success', 1);
    }

    /**
     * getSchedulePage 获取服务日程页面接口
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function getSchedulePage()
    {
        $this->changeViewFilter();

        $serveRangeId = $_REQUEST['serve_range_id'];
        $streetList = $this->serveRangeModel->getStreetList($serveRangeId);
        $hasSchedule = $this->serveScheduleModel->hasSchedule($serveRangeId);
        $scheduleInfo = $this->serveScheduleModel->getScheduleInfo($serveRangeId);
        $serveTimeRange = range(9, 17);//目前测试有效服务时段（营业时间）
        $inventoryList = A('Admin/Taobaoapi')->inventoryQueryAPI($scheduleInfo['serve_schedule_id'], $serveTimeRange);

        $this->assign('streetList', $streetList);
        $this->assign('hasSchedule', $hasSchedule);
        $this->assign('scheduleInfo', $scheduleInfo);
        $this->assign('inventoryList', $inventoryList);
        $this->assign('serveTimeRange', $serveTimeRange);
        $html = $this->fetch('schedule');
        $this->ajaxReturn($html, 'success', 1);
    }

    /**
     * changeViewFilter 去view_filter调试信息
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function changeViewFilter()
    {
        $filter = array(
            'ContentReplace', // 模板输出替换
            'TokenBuild',   // 表单令牌
            'WriteHtmlCache', // 写入静态缓存
        );
        C('extends.view_filter', $filter);
    }
}
