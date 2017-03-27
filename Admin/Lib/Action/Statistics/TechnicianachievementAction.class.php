<?php

/**
 * TechnicianAchievementAction 技师业绩日报后台控制器
 *
 * @uses CommonAction
 * @modify_time 2015-09-06
 * @author zxj <tenkanse@hotmail.com>
 */
class TechnicianAchievementAction extends CommonAction
{

    /**
     * __construct
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->technician_achievement_model = D(GROUP_NAME.'/TechnicianAchievement');
        $this->technicianGoalModel = D(GROUP_NAME.'/TechnicianGoal');

        $this->user = D('tp_admin.user');
        $this->kv = D('kv');
    }

    /**
     * index
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function index()
    {
        $search = array(
            'city_id' => $_POST['city_id'],
            'date' => $_POST['date']
        );

        $search_info = array(
            'city_id' => $search['city_id'] ?: 1,
            'date' => $this->toUnixTime($search['date']) ?: strtotime(date('Y-m-d'))
        );

        $count_data = $this->technician_achievement_model->getV('technician_achievement');

        $get_day = function($d) { return strtotime(date('Y-m-d', $d)); };
        $get_month = function($d) { return strtotime(date('Y-m-01', $d)); };

        $key = $get_month($search_info['date']).'_'.$search_info['city_id'];
        $key_daily = $get_day($search_info['date']).'_'.$search_info['city_id'];

        if ($_POST['action'] == 'save') {
            $count_data['count_cars'][$key] = array('count' => $_POST['count_cars'], 'date'=>$get_month($search_info['date']), 'city_id'=>$search_info['city_id']);
            $count_data['count_cars_daily'][$key_daily] = array('count' => $_POST['count_cars_daily'], 'date'=>$get_day($search_info['date']), 'city_id'=>$search_info['city_id']);
            $count_data['count_people'][$key] = array('count' => $_POST['count_people'], 'date'=>$get_month($search_info['date']), 'city_id'=>$search_info['city_id']);
            $count_data['count_people_daily'][$key_daily] = array('count' => $_POST['count_people_daily'], 'date'=>$get_day($search_info['date']), 'city_id'=>$search_info['city_id']);

            $this->technician_achievement_model->saveKV('technician_achievement', $count_data);
        }

        $kv_data = array();
        if ($count_data) {
            $kv_data = array(
                'count_cars' => @$count_data['count_cars'][$key]['count'],
                'count_cars_daily' => @$count_data['count_cars_daily'][$key_daily]['count'],
                'count_people' => @$count_data['count_people'][$key]['count'],
                'count_people_daily' => @$count_data['count_people_daily'][$key_daily]['count']
            );
        }

        $is_show_all = true;    //是否显示全部城市标识
        if (!in_array($_SESSION['authId'], $this->technician_achievement_model->getV('technician_achievement_limit'))) {
            $is_show_all = false;
            $this->assign('show_city', $this->getCityById((int)$_SESSION['city_id']));
        }

        $achievement_data = $this->technician_achievement_model->getAchievementData($search_info['city_id'], $search_info['date']);

        $this->assign('is_show_all', $is_show_all);
        $this->assign('search', $search);
        $this->assign('cityList', $this->getCityList());
        $this->assign('data', $achievement_data);
        $this->assign('count_data', $kv_data);

        unset($achievement_data, $count_data, $kv_data, $search, $search_info);

        $this->display();
    }


    /**
     * 技师业绩日报查看权限设置
     */
    public function limit()
    {
        $users = $this->user->field('id,account,nickname')->select();

        $k = 'technician_achievement_limit';
        $sel_users = $this->technician_achievement_model->getV($k);

        if ($_POST) {
            $sel_users = $_POST['user_ids'];
            $this->technician_achievement_model->saveKV($k, $sel_users);
        }

        $this->assign('users', $users);
        $this->assign('sel_users', $sel_users);

        unset($sel_users, $users);

        $this->display();
    }


    /**
     * goal 设定月订单目标页面
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function goal()
    {
        $defaultYear = $this->getThisYear();//默认本年度
        $defaultCity = 1;//默认上海
        if ($_POST) {
            $data = array(
                'care_goal' => $_POST['care_goal'],
                'non_care_goal' => $_POST['non_care_goal'],
                'city_id' => $_POST['city_id'],
                'year' => $_POST['year'],
            );

            // 过滤$data
            
            $this->technicianGoalModel->saveGoal($data);
            $defaultYear = $data['year'];
            $defaultCity = $data['city_id'];
        }

        $search = array(
            'city_id' => $defaultCity,
            'year' => $defaultYear,
        );
        $this->assign('goalList', $this->technicianGoalModel->getList($search));
        $this->assign('defaultYear', $defaultYear);
        $this->assign('defaultCity', $defaultCity);
        $this->assign('yearList', $this->getYearList());
        $this->assign('cityList', $this->getCityList());
        $this->display();
    }

    public function exportMarchToMayOrderInfo ()
    {
        $order_model = D('reservation_order');
        $car_model_model = D('carmodel');

        $map = array(
            '_complex' => $this->technician_achievement_model->maintainOrderCondition(),
            'order_time' => array(array('egt', strtotime('2015-05-01 00:00:00')), array('lt', strtotime('2015-06-01 00:00:00')))
        );

        $orders = $order_model->where($map)->order('id asc')->getField('id, truename, mobile, licenseplate, model_id');

        $str = "订单号, 姓名, 联系方式, 车牌, 车型\n";
        foreach($orders as $k=>$o) {
            $m_name = $car_model_model->where(array('model_id'=>$o['model_id']))->getField('model_name');    //todo 优化
            $str .= $k.','.$o['truename'].','.$o['mobile'].','.$o['licenseplate'].','.$m_name."\n";
        }

        $this->exportCSV('3月保养订单.csv', $str);
    }

    /**
     * getGoalListAPI 获取月度目标列表接口
     *
     * @modify_time 2015-09-06
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function getGoalListAPI()
    {
        $search = array(
            'city_id' => $_POST['city_id'],
            'year' => $_POST['year'],
        );
        $list = $this->technicianGoalModel->getList($search);
        $this->ajaxReturn($list, 'success', 1);
    }

    /**
     * getThisYear 获取当前年份
     *
     * @modify_time 2015-09-07
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return string
     */
    private function getThisYear()
    {
        $dateObj = new DateTime();
        return $dateObj->format('Y');
    }

    /**
     * getYearList 获取年份列表
     *
     * @modify_time 2015-09-07
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return mixed
     */
    private function getYearList()
    {
        $thisYear = $this->getThisYear();
        return range($thisYear - 2, $thisYear + 3);
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
     * getCityList 获取城市列表
     *
     * @modify_time 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @access private
     * @return mixed
     */
    private function getCityList()
    {
        $cityModel = D('city');
        $where = array(
            'is_show' => 1,
        );
        return $cityModel->where($where)->select();
    }


    private function getCityById($city_id)
    {
        $cityModel = D('city');
        $where = array(
            'is_show' => 1,
            'id' => $city_id
        );
        return $cityModel->where($where)->find();
    }

    /**
     * exportCSV 导出csv文件
     *
     * @modify_time 2015-09-08
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $filename
     * @param mixed $data
     * @access private
     * @return void
     */
    private function exportCSV($filename, $data)
    {
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo $data;
    }

}
