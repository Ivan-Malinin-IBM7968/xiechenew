<?php
/**
 * TaobaoservescheduleModel 淘宝预约服务日程模型
 *
 * @uses CommonModel
 * @modify_time 2015-09-30
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoservescheduleModel extends CommonModel
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
    protected $tableName = 'taobao_serve_schedule';

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
     * hasSchedule
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveRangeId
     * @access public
     * @return boolean
     */
    public function hasSchedule($serveRangeId)
    {
        return $this->getFieldByServeRangeId($serveRangeId, 'id') ? true : false;
    }

    /**
     * getScheduleInfo 获取某日程信息
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveRangeId
     * @access public
     * @return mixed
     */
    public function getScheduleInfo($serveRangeId)
    {
        $where = array(
            'serve_range_id' => $serveRangeId,
        );
        return $this->where($where)->find();
    }

    /**
     * getScheduleInfoByScheduleId 用schedule_id获取某日程信息
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveScheduleId
     * @access public
     * @return mixed
     */
    public function getScheduleInfoByScheduleId($serveScheduleId)
    {
        $where = array(
            'serve_schedule_id' => $serveScheduleId,
        );
        return $this->where($where)->find();
    }

    /**
     * setInventory
     *
     * @modify_time 2015-09-29
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveScheduleId
     * @access public
     * @return void
     */
    public function setInventory($serveScheduleId)
    {
        if ($this->getFieldByServeScheduleId($serveScheduleId, 'inventory_reset')) {
            $where = array(
                'serve_schedule_id' => $serveScheduleId,
            );
            $data = array(
                'inventory_reset' => 0,
            );
            $this->where($where)->save($data);
        }
    }

    /**
     * inSchedule 判断日期是否在日程内
     *
     * @modify_time 2015-09-29
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveScheduleId
     * @param mixed $date
     * @access public
     * @return boolean
     */
    public function inSchedule($serveScheduleId, $date)
    {
        $inventoryDate = new DateTime($date);
        $scheduleInfo = $this->getScheduleInfoByScheduleId($serveScheduleId);

        $beginTime = new DateTime();
        $endTime = new DateTime();
        $beginTime->setTimestamp($scheduleInfo['schedule_begin_time']);
        $endTime->setTimestamp($scheduleInfo['schedule_end_time']);

        if ($beginTime <= $inventoryDate && $inventoryDate <= $endTime) {
            return true;
        }
        return false;
    }
}
