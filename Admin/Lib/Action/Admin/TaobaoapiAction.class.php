<?php
/**
 * TaobaoapiAction 淘宝预约接口
 *
 * @uses CommonAction
 * @modify_time 2015-09-21
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoapiAction extends CommonAction
{
    /**
     * __construct
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        header("Content-type: text/html; charset=utf-8");
        $this->api = D('Taobao/Taobaoapi');
        $this->serviceKey = "OnDoorCleanCar";

        $this->areaModel = D('Taobao/Taobaoarea');
        $this->serveRangeModel = D('Taobao/Taobaoserverange');
        $this->serveScheduleModel = D('Taobao/Taobaoserveschedule');
    }

    /**
     * index
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function index()
    {
        //构建一个 北京 北京市 朝阳区 劲松一区 劲松街道 的服务范围，
        //并构建对应的日程和库存
        //110000 110100 110105 110105017
        
        D('Taobao/Taobaoapitest')->summary();
    }

    /**
     * mock
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $array
     * @access private
     * @return void
     */
    private function mock($array)
    {
        return json_encode((object)$array);
    }

    /**
     * toUnixTime 将日期插件生成的时间转换为timestamp
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $time
     * @access private
     * @return string
     */
    private function toUnixTime($time)
    {
        $dateObj = new DateTime($time);
        return $dateObj->getTimestamp();
    }

    /**
     * createAreaTable 生成淘宝区域表
     *
     * @modify_time 2015-09-22
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function createAreaTable()
    {
        $baseId = '1';//中国
        $list = $this->areaModel->getProvinceList();
        foreach ($list as $item) {
            $baseId = $item['area_id'];
            echo $baseId."<br/>";
            $this->addChildArea(array($baseId), 4);
            exit;
        }
    }

    /**
     * addChildArea 数据库添加子区域
     *
     * @modify_time 2015-09-22
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $childAreaIds 
     * @param mixed $levelLimit 区域层级限制
     * @access private
     * @return void
     */
    private function addChildArea($childAreaIds, $levelLimit)
    {
        foreach ($childAreaIds as $areaId) {
            $resp = $this->areaQuery($areaId)->value;
            $childAreaIds = $resp->child_area_ids->number;
            $serveAreaLevel = $resp->serve_area_level;

            echo $areaId.':'.$resp->name.',level:'.$serveAreaLevel.'<br/>';
            if (!$this->areaModel->getFieldByAreaId($areaId, 'id')) {
                $data = (array)$resp;
                unset($data['child_area_ids']);
                $this->areaModel->add($data);
            }

            if ($childAreaIds && $serveAreaLevel < $levelLimit) {
                $this->addChildArea($childAreaIds, $levelLimit);
            }
        }
    }

    /**
     * areaQuery 调用查询地区接口
     *
     * @modify_time 2015-09-23
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $areaId
     * @access private
     * @return mixed
     */
    private function areaQuery($areaId)
    {
        $params = array(
            'ParentAreaId' => $areaId,
        );
        $this->api->setName('AreaQuery');
        $this->api->setParams($params);
        return $this->api->request();
    }

    /**
     * rangeCreateAPI 创建服务范围API
     *
     * @modify_time 2015-09-23
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function rangeCreateAPI()
    {
        try {
            $serveRange = array(
                //范围组名
                "name" => $_REQUEST['group_name'],
                //市级区域ID
                "parent_area_id" => $_REQUEST['level_3_id'],
                //下属区域ID，4级ID表示支持全区，5级ID表示选择的乡/镇/街道ID。
                "child_area_ids" => is_array($_REQUEST['child_area_ids']) ?
                array_filter($_REQUEST['child_area_ids']) : array($_REQUEST['child_area_ids']),
            );

            $params = array(
                'ServiceKey' => $this->serviceKey,
                'ResourceRecyclable' => 'false',
                'ServeRange' => $this->mock($serveRange),
            );
            $this->api->setName('RangeCreate');
            $this->api->setParams($params);

            $resp = $this->api->request();
            $serveRange['serve_range_id'] = $resp->value->serve_range_id; 
            $serveRange['child_area_ids'] = json_encode($serveRange['child_area_ids']);
            $this->serveRangeModel->add($serveRange);
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * rangeUpdateAPI 更新服务范围组名API
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function rangeUpdateAPI()
    {
        try {
            $serveRangeId = $_REQUEST['serve_range_id'];
            $childAreaIds = $this->serveRangeModel->getFieldByServeRangeId($serveRangeId, 'child_area_ids');
            $serveRange = array(
                'name' => $_REQUEST['group_name'],
                'serve_range_id' => $serveRangeId,
                'parent_area_id' => $this->serveRangeModel->getFieldByServeRangeId($serveRangeId, 'parent_area_id'),
                'child_area_ids' => json_decode($childAreaIds, true),
            );

            $params = array(
                'ServiceKey' => $this->serviceKey,
                'ServeRange' => $this->mock($serveRange),
            );
            $this->api->setName('RangeUpdate');
            $this->api->setParams($params);

            $resp = $this->api->request();

            $where = array(
                'serve_range_id' => $serveRangeId, 
            );
            $this->serveRangeModel->where($where)->save($serveRange);

            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * rangeRemoveAPI 服务范围删除API
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function rangeRemoveAPI()
    {
        try {
            $serveRangeId = $_REQUEST['serve_range_id'];
            $params = array(
                'RangeId' => $serveRangeId, 
            );
            $this->api->setName('RangeRemove');
            $this->api->setParams($params);

            $resp = $this->api->request();
            if (!$resp->value) {
                throw new \Exception('删除错误');
            }
            $where = array(
                'serve_range_id' => $serveRangeId, 
            );
            $this->serveRangeModel->where($where)->delete();
            $this->serveScheduleModel->where($where)->delete();
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * scheduleCreateAPI 创建日程接口
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function scheduleCreateAPI()
    {
        try {
            $beginTime = $_REQUEST['start_time'];
            $endTime = $_REQUEST['end_time'];
            $serveSchedule = array(
                "serve_range_id" => $_REQUEST['serve_range_id'],
                "schedule_begin_time" => "$beginTime 00:00:00",
                "schedule_end_time" => "$endTime 00:00:00",
                "recyclable" => "false",
                "schedule_status" => "0",
            );

            $params = array(
                'ServeSchedule' => $this->mock($serveSchedule),
            );
            $this->api->setName('ScheduleCreate');
            $this->api->setParams($params);

            $resp = $this->api->request();
            $serveSchedule['schedule_begin_time'] = $this->toUnixTime($beginTime);
            $serveSchedule['schedule_end_time'] = $this->toUnixTime($endTime);
            $serveSchedule['serve_schedule_id'] = $resp->serve_schedule->serve_schedule_id;

            $this->serveScheduleModel->add($serveSchedule);
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * scheduleUpdateAPI 更新日程API
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function scheduleUpdateAPI()
    {
        try {
            $serveScheduleId = $_REQUEST['serve_schedule_id'];
            $beginTime = $_REQUEST['start_time'];
            $endTime = $_REQUEST['end_time'];
            $serveSchedule = array(
                "serve_schedule_id" => $serveScheduleId,
                "serve_range_id" => $this->serveScheduleModel->getFieldByServeScheduleId($serveScheduleId, 'serve_range_id'),
                "schedule_begin_time" => "$beginTime 00:00:00",
                "schedule_end_time" => "$endTime 00:00:00",
                "recyclable" => "false",
                "schedule_status" => "0",
            );

            $params = array(
                'ServeSchedule' => $this->mock($serveSchedule),
            );
            $this->api->setName('ScheduleUpdate');
            $this->api->setParams($params);

            $resp = $this->api->request();
            $serveSchedule['schedule_begin_time'] = $this->toUnixTime($beginTime);
            $serveSchedule['schedule_end_time'] = $this->toUnixTime($endTime);
            $serveSchedule['inventory_reset'] = 1;

            $where = array(
                'serve_schedule_id' => $serveScheduleId,
            );
            $this->serveScheduleModel->where($where)->save($serveSchedule);
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * scheduleRemoveAPI 删除日程API
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function scheduleRemoveAPI()
    {
        try {
            $serveScheduleId = $_REQUEST['serve_schedule_id'];

            $params = array(
                'ScheduleId' => $serveScheduleId,
            );
            $this->api->setName('ScheduleRemove');
            $this->api->setParams($params);

            $resp = $this->api->request();

            $where = array(
                'serve_schedule_id' => $serveScheduleId,
            );
            $this->serveScheduleModel->where($where)->delete();
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }

    /**
     * inventoryQueryAPI 获取淘宝预约库存API
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveScheduleId
     * @param mixed $serveTimeRange
     * @access public
     * @return void
     */
    public function inventoryQueryAPI($serveScheduleId, $serveTimeRange)
    {
        try {
            $params = array(
                'ServeScheduleId' => $serveScheduleId,
            );
            $this->api->setName('InventoryQuery');
            $this->api->setParams($params);

            $resp = $this->api->request()->day_inventory_list->day_inventory_top_dto;
            $isReset = $this->serveScheduleModel->getFieldByServeScheduleId($serveScheduleId, 'inventory_reset');

            if (!$resp || $isReset) {
                return $this->initializeInventoryList($serveScheduleId, $serveTimeRange);
            }
            return $this->getInventoryList($resp, $serveScheduleId);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * getInventoryList 格式化接口获取的库存列表
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $dayInventoryList
     * @param mixed $serveScheduleId
     * @access private
     * @return mixed
     */
    private function getInventoryList($dayInventoryList, $serveScheduleId)
    {
        foreach ($dayInventoryList as $dayInventory) {
            $pointInventoryList = $dayInventory->point_inventory_list->point_inventory_top_dto;
            foreach ($pointInventoryList as &$pointInventory) {
                $pointInventory = (array) $pointInventory;
            }
            if (!$this->serveScheduleModel->inSchedule($serveScheduleId, $dayInventory->date)) {
                continue;
            }
            $inventoryList[] = array(
                'date' => substr($dayInventory->date, 0, 10),
                'point_inventory_list' => $pointInventoryList,
            );
        }
        return $this->sortByKey($inventoryList, 'date');
    }

    /**
     * sortByKey
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $list 需要排序的列表
     * @param mixed $itemName 列表item排序依据的key名称
     * @access private
     * @return mixed
     */
    private function sortByKey($list, $itemName)
    {
        //子数组需要排序的item名新建一个数组
        $keyArray = $$itemName;
        foreach ($list as $key => $row) {
            $keyArray[$key]  = $row[$itemName];
            unset($row[$itemName]);
            //其余数据提出
            $others[$key] = $row;
        }
        //翻转后保留原排序号，再按item名下相应值排序
        $keyArray = array_flip($keyArray);
        ksort($keyArray, SORT_ASC);
        //建立排好序的原结构新数组
        foreach ($keyArray as $sortVal => $originalOrder) {
            $newItem = $others[$originalOrder];
            $newItem[$itemName] = $sortVal;
            $sortedList[] = $newItem;
        }
        return $sortedList;
    }

    /**
     * initializeInventoryList 初始化库存数据
     *
     * @modify_time 2015-09-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $serveScheduleId
     * @param mixed $serveTimeRange
     * @access private
     * @return mixed
     */
    private function initializeInventoryList($serveScheduleId, $serveTimeRange)
    {
        $scheduleInfo = $this->serveScheduleModel->getScheduleInfoByScheduleId($serveScheduleId);

        $beginTime = new DateTime();
        $endTime = new DateTime();
        $beginTime->setTimestamp($scheduleInfo['schedule_begin_time']);
        $endTime->setTimestamp($scheduleInfo['schedule_end_time']);

        $currentTime = $beginTime;
        while ($currentTime <= $endTime) {
            $inventoryList[] = array(
                'date' => $currentTime->format('Y-m-d'),
                'point_inventory_list' => $this->initializePointInventoryList($serveTimeRange),
            );
            $currentTime->add(new DateInterval('P1D'));
        }
        return $inventoryList;
    }

    /**
     * initializePointInventoryList 创建空的日库存数据
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $hourRange
     * @access private
     * @return mixed
     */
    private function initializePointInventoryList($hourRange)
    {
        foreach ($hourRange as &$hour) {
            $hour = array(
                'time' => $hour * 3600,
                'quantity' => 100,
            );
        }
        return $hourRange;
    }

    /**
     * createDayInventorys 生成接口用的库存数据
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $inventoryList
     * @access private
     * @return mixed
     */
    private function createDayInventorys($inventoryList)
    {
        foreach ($inventoryList as $inventory) {
            $date = $inventory['date'];
            unset($inventory['date']);
            $mid[$date][] = $inventory;
        }
        /*
        $mid = array(
            '2015-10-19' => array(
                array(
                    'time' => '',
                    'quantity' => '',
                ),
            ),
        );
        */
        foreach ($mid as $date => $pointInventoryList) {
            foreach ($pointInventoryList as &$pointInventory) {
                $pointInventory = (object) $pointInventory;
            }
            unset($pointInventory);
            $dayInventorys[] = (object) array(
                'date' => $date.' 00:00:00',
                'point_inventory_list' => $pointInventoryList,
            );
        }
        $dayInventorys = json_encode($dayInventorys);
        return $dayInventorys;
    }

    /**
     * inventoryUpdateAPI 更新库存接口
     *
     * @modify_time 2015-09-28
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function inventoryUpdateAPI()
    {
        try {
            $serveScheduleId = $_REQUEST['serve_schedule_id'];
            $inventoryList = $_REQUEST['inventory'];

            $params = array(
                'ServeScheduleId' => $serveScheduleId, 
                'DayInventorys' => $this->createDayInventorys($inventoryList),
            );
            $this->api->setName('InventoryUpdate');
            $this->api->setParams($params);

            $resp = $this->api->request();
            $this->serveScheduleModel->setInventory($serveScheduleId);
            $this->ajaxReturn($resp, 'success', 1);
        } catch (\Exception $e) {
            $this->ajaxReturn('', $e->getMessage(), 0);
        }
    }
}
