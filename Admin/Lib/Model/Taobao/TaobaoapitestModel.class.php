<?php
/**
 * TaobaoapitestModel 淘宝预约接口测试
 *
 * @uses CommonModel
 * @modify_time 2015-09-21
 * @author zxj <tenkanse@hotmail.com>
 */
class TaobaoapitestModel extends CommonModel
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
        $this->serviceKey = "OnDoorCleanCar";
        //$this->serviceKey = "HouseholdCleaning";
    }

    /**
     * summary
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function summary()
    {
        $testApi = array(
            //success
            //'AreaQuery',
            //'RangeQuery',
            //'InventoryQuery',
            //'ScheduleQuery',
            //'WorkerCreate',
            //'WorkerUpdate',
            //'WorkerRemove',
            //'RangeCreate',
            //'RangeUpdate',
            //'RangeRemove',
            //'ScheduleCreate',
            //'ScheduleUpdate',
            //'ScheduleRemove',
            'InventoryUpdate',
            //'InventoryRollback',
            //'OrderInfoBatchQuery',
            //'OrderAccept',
            //'WorkerDispatch',

            //fail
            //'InventoryBooking',
        );
        foreach ($testApi as $name) {
            $mock = "mock".$name;
            $api = new TaobaoapiModel();
            $api->setName($name);
            $api->setParams($this->$mock());
            $api->render();
        }
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
     * mockWorkerCreate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockWorkerCreate()
    {
        $serveWorker = array(
            "region_code" => "330110",
            "id_card" => "423333333333333333",
            "worker_education" => "1",
            "job_status" => "1",
            "male" => "true",
            "province_code" => "330000",
            "city_code" => "330100",
            "service_count" => "2",
            "bad_count" => "2",
            "good_count" => "1",
            "birthplace" => "330000",
            "avatar_url" => "YmFzZTY0Y29kZQ",
            "name" => "张三",
            "job_experience" => "1",
            "contact_mobile" => "13222222222",
            "contact_address" => "浙江省杭州市余杭区淘宝城969号",
        );
        return array(
            'ServiceKey' => $this->serviceKey,
            'ServeWorker' => $this->mock($serveWorker),
        );
    }

    /**
     * mockWorkerUpdate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockWorkerUpdate()
    {
        $serveWorker = array(
            "worker_id" => "17390",
            "region_code" => "330110",
            "id_card" => "423333333333333333",
            "worker_education" => "1",
            "job_status" => "1",
            "male" => "true",
            "province_code" => "330000",
            "city_code" => "330100",
            "service_count" => "2",
            "bad_count" => "2",
            "good_count" => "1",
            "birthplace" => "330000",
            "avatar_url" => "YmFzZTY0Y29kZQ",
            "name" => "张三",
            "job_experience" => "1",
            "contact_mobile" => "13222222222",
            "contact_address" => "浙江省杭州市余杭区淘宝城969号",
        );
        return array(
            'ServeWorker' => $this->mock($serveWorker),
        );
    }

    /**
     * mockWorkerRemove
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockWorkerRemove()
    {
        return array(
            'WorkerId' => '17409',
        );
    }

    /**
     * mockAreaQuery
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockAreaQuery()
    {
        return array(
            'ParentAreaId' => '110105',
        );
    }

    /**
     * mockRangeQuery
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockRangeQuery()
    {
        return array(
            'ParentAreaId' => '330100',
            'ServiceKey' => $this->serviceKey,
        );
    }

    /**
     * mockRangeCreate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockRangeCreate()
    {
        $serveRange = array(
            "name" => "测试多日程",
            "parent_area_id" => "110100",
            "child_area_ids" => '110102',
        );
        return array(
            'ServiceKey' => $this->serviceKey,
            'ResourceRecyclable' => 'true',
            'ServeRange' => $this->mock($serveRange),
        );
    }

    /**
     * mockRangeUpdate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockRangeUpdate()
    {
        $serveRange = array(
            "serve_range_id" => "17392",
            "name" => "test街道",
            "parent_area_id" => "110100",
            "child_area_ids" =>  array('110105017'),
        );
        return array(
            'ServiceKey' => $this->serviceKey,
            'ServeRange' => $this->mock($serveRange),
        );
    }

    /**
     * mockRangeRemove
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockRangeRemove()
    {
        return array(
            'RangeId' => '17368',
        );
    }

    /**
     * mockScheduleCreate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockScheduleCreate()
    {
        $serveSchedule = array(
            "serve_range_id" => "17560",
            "schedule_begin_time" => "2015-10-17 00:00:00",
            "schedule_end_time" => "2015-10-20 00:00:00",
            "recyclable" => "false",
            "schedule_status" => "0",
        );
        return array(
            'ServeSchedule' => $this->mock($serveSchedule),
        );
    }

    /**
     * mockScheduleQuery
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockScheduleQuery()
    {
        return array(
            'ServeRangeId' => '17594',
        );
    }


    /**
     * mockScheduleUpdate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockScheduleUpdate()
    {
        $serveSchedule = array(
            "serve_schedule_id" => "17370",
            "serve_range_id" => "17366",
            "schedule_begin_time" => "2015-09-29 00:00:00",
            "schedule_end_time" => "2015-10-20 00:00:00",
            "recyclable" => "false",
            "schedule_status" => "0",
        );
        return array(
            'ServeSchedule' => $this->mock($serveSchedule),
        );
    }

    /**
     * mockScheduleRemove
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockScheduleRemove()
    {
        return array(
            'ScheduleId' => '17424',
        );
    }

    /**
     * mockInventoryQuery
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockInventoryQuery()
    {
        return array(
            'ServeScheduleId' => '17598',
        );
    }

    /**
     * mockInventoryUpdate
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockInventoryUpdate()
    {
        $pointInventoryList = array(
            (object)array(
                'time' => '36000',
                'quantity' => '1',
            ),
        );
        $dayInventorys = array(
            (object)array(
                'date' => '2015-09-30 00:00:00',
                'point_inventory_list' => $pointInventoryList,
            ),
        );
        $string = json_encode($dayInventorys);
        return array(
            'ServeScheduleId' => '17611',
            'DayInventorys' => $string,
        );
    }

    /**
     * mockInventoryBooking
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockInventoryBooking()
    {
        $serveLocation = array(
            "district_area_id" => "330110",
            "city_area_id" => "330100",
            "street_area_id" => "330110102",
            "address_detail" => "浙江省杭州市余杭区测试",
            "lng" => "120.3",
            "lat" => "30.2",
            "serve_range_id" => "17594",
        );
        $bookingInventory = array(
            "book_quantity" => "2",
            "book_begin_time" => "2015-09-29 10:00:00",
            "book_end_time" => "2015-09-29 11:00:00",
            "serve_location" =>  (object) $serveLocation,
            "unique_order_id" => "4E01A2D7987B96027E6FFC2FB406FD36",
            "serve_schedule_id" => "17595",
        );
        return array(
            'ServiceKey' => $this->serviceKey,
            'BookingInventory' => $this->mock($bookingInventory),
        );
    }

    /**
     * mockInventoryRollback
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockInventoryRollback()
    {
        $serveLocation = array(
            "district_area_id" => "330110",
            "city_area_id" => "330100",
            "street_area_id" => "330100105",
            "address_detail" => "浙江省杭州市余杭区文一西路96号",
            "lng" => "120.3",
            "lat" => "30.2",
            "serve_range_id" => "17375",
        );
        $bookingInventory = array(
            "book_quantity" => "2",
            "book_begin_time" => "2015-10-21 15:00:00",
            "book_end_time" => "2015-10-21 16:00:00",
            "serve_location" =>  (object)$serveLocation,
            "unique_order_id" => "9E01A2D7987B93027E6FFC2FB406FD36",
            "serve_schedule_id" => "17376",
        );
        return array(
            'ServiceKey' => $this->serviceKey,
            'BookingInventory' => $this->mock($bookingInventory),
        );
    }

    /**
     * mockOrderInfoBatchQuery
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockOrderInfoBatchQuery()
    {
        $beginTime = new DateTime('2015-09-16 00:00:00');
        $endTime = new DateTime('2015-10-16 00:00:00');
        return array(
            'BeginTime' => $beginTime->getTimestamp() * 1000,
            'EndTime' => $endTime->getTimestamp() * 1000,
            'PageNo' => 1,
            'ServiceKey' => $this->serviceKey,
            'OrderStatus' => 0,
        );
    }

    /**
     * mockOrderAccept
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockOrderAccept()
    {
        return array(
            'OrderId' => '193765211506244',
        );
    }

    /**
     * mockWorkerDispatch
     *
     * @modify_time 2015-09-21
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return void
     */
    private function mockWorkerDispatch()
    {
        return array(
            'WorkerIds' => '17372',
            'TbOrderId' => '193765211506244',
        );
    }
}
