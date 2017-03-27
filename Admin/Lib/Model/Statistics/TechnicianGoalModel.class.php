<?php

/**
 * TechnicianGoalModel
 *
 * @uses CommonModel
 * @modify_time 2015-09-07
 * @author zxj <tenkanse@hotmail.com>
 */
class TechnicianGoalModel extends CommonModel
{
    /**
     * __construct
     *
     * @modify_time 2015-09-07
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * saveGoal
     *
     * @modify_time 2015-09-07
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $data
     * @access public
     * @return void
     */
    public function saveGoal($data)
    {
        for ($i = 0; $i < 12; $i++) {
            $where = array(
                'city_id' => $data['city_id'],
                'year' => $data['year'],
                'month' => $i + 1,
            );
            $goal = array(
                'care' => $data['care_goal'][$i],
                'non_care' => $data['non_care_goal'][$i],
            );
            $record = array(
                'city_id' => $data['city_id'],
                'year' => $data['year'],
                'month' => $i + 1,
                'goal' => json_encode($goal),
                'update_time' => time(),
            );
            if ($this->where($where)->count()) {
                $this->where($where)->save($record);
            } else {
                $this->add($record);
            }
        }
    }

    /**
     * getList 获取城市年度月目标列表
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $search
     * @access public
     * @return mixed
     */
    public function getList($search)
    {
        $order = 'month';
        $list = $this->where($search)->order($order)->select();
        foreach ($list as &$item) {
            $goal = json_decode($item['goal'], true);
            $item['care_goal'] = $goal['care'];
            $item['non_care_goal'] = $goal['non_care'];
        }
        if (!$list) {
            $list = $this->createEmptyList();
        }
        return $list;
    }

    /**
     * createEmptyList 用于初始无数据时显示列表
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return mixed
     */
    private function createEmptyList()
    {
        for ($i = 0; $i < 12; $i++) {
            $list[] = array(
                'month' => $i + 1,
                'care_goal' => 0,
                'non_care_goal' => 0,
            );
        }
        return $list;
    }
}
