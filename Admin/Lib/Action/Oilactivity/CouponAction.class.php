<?php

/**
 * CouponAction 后台新版优惠券控制器
 *
 * @uses CommonAction
 * @version 2015-08-18
 * @author zxj <tenkanse@hotmail.com>
 */
class CouponAction extends CommonAction
{
    /**
     * __construct 初始化模型等
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->couponModel = D(GROUP_NAME.'/NewCoupon');
        $this->couponCodeModel = D(GROUP_NAME.'/NewCouponCode');
    }

    /**
     * index 优惠券管理列表页面
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function index()
    {
        list($page, $list) = $this->couponModel->getList();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * exportCouponCode 导出优惠码
     *
     * @modify_time 2015-09-08
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function exportCouponCode()
    {
        $id = $this->inputFilter($_REQUEST['id']);
        $couponName = $this->inputFilter($_REQUEST['name']);
        $list = $this->couponCodeModel->getCouponCodeList($id);
        $csv = implode(",\n", $list);
        $this->exportCSV($couponName.'.csv', $csv);
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

    /**
     * add 添加优惠券页面
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function add()
    {
        $eventType = C('COUPON_EVENT_TYPE');
        $this->assign('eventType', $eventType);
        $this->display();
    }

    /**
     * addCoupon 添加优惠券操作
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function addCoupon()
    {
        try {
            $images = $this->upload('/UPLOADS/Admin/coupon/');
            $data = array(
                'type' => $this->inputFilter($_REQUEST['type']),
                'support_type' => $this->inputFilter($_REQUEST['support_type']),
                'coupon_name' => $this->inputFilter($_REQUEST['coupon_name']),
                'coupon_title' => $this->inputFilter($_REQUEST['coupon_title']),
                'coupon_subtitle' => $this->inputFilter($_REQUEST['coupon_subtitle']),
                'coupon_amount' => $this->inputFilter($_REQUEST['coupon_amount']),
                'coupon_count' => $this->inputFilter($_REQUEST['coupon_count']),
                'coupon_prefix' => $this->inputFilter($_REQUEST['coupon_prefix']),
                'coupon_summary' => $this->inputFilter($_REQUEST['coupon_summary']),
                'service_area' => $this->inputFilter($_REQUEST['service_area']),
                'start_time' => $this->toUnixTime($this->inputFilter($_REQUEST['start_time'])),
                'end_time' => $this->toUnixTime($this->inputFilter($_REQUEST['end_time'])),
                'coupon_thumb' => $images[0],
                'coupon_detail_img' => $images[1],
            );
            $this->existFilter($data['coupon_name'], '活动名称');
            $this->supportTypeFilter($data);
            $this->prefixFilter($data);
            $this->priceFilter($data);
            $this->timeFilter($data);
            $this->numberFilter($data);
            $this->couponModel->addOne($data);
            $this->success('新增成功');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * existFilter 检查参数是否存在
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @param mixed $name
     * @access private
     * @return void
     */
    private function existFilter($input, $name = '参数')
    {
        if (!$input) {
            throw new \Exception('未指明'.$name);
        }
    }

    /**
     * supportTypeFilter 过滤支持的服务
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function supportTypeFilter($input)
    {
        if (!$input['support_type']) {
            throw new \Exception('请至少选择一种支持的服务');
        }
    }

    /**
     * prefixFilter 优惠券码前缀过滤
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function prefixFilter($input)
    {
        $exp = '/^[a-z]{2,3}$/';
        if (!preg_match($exp, $input['coupon_prefix'])) {
            throw new \Exception('优惠券前缀为2～3个小写英文字母');
        }
    }

    /**
     * priceFilter 优惠价格过滤
     *
     * @modify_time 2015-08-25
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function priceFilter($input)
    {
        $minAmount = $this->getMinEventAmount($input['support_type']);
        if ($input['coupon_amount'] > $minAmount) {
            throw new \Exception('优惠券优惠价格不能超过'.$minAmount.'元');
        }
    }

    /**
     * getMinEventAmount 找到所有支持套餐中最便宜套餐价格
     *
     * @modify_time 2015-08-26
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $supportTypes
     * @access private
     * @return float
     */
    private function getMinEventAmount($supportTypes)
    {
        $eventType = C('COUPON_EVENT_TYPE');
        if (!is_array($supportTypes)) {
            $supportTypes = (array)$supportTypes;
        }
        foreach ($supportTypes as $supportType) {
            $amount[] = $eventType[$supportType - 1]['amount'];
        }
        return min($amount);
    }

    /**
     * numberFilter 过滤掉生成优惠券数目太多的活动
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function numberFilter($input)
    {
        if ($input['coupon_count'] > 5000) {
            throw new \Exception('优惠券数目不能超过5000');
        }
    }

    /**
     * timeFilter 过滤掉不符合的有效期时间
     *
     * @version 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return void
     */
    private function timeFilter($input)
    {
        if ($input['end_time'] < $input['start_time']) {
            throw new \Exception('有效期结束时间必须大于开始时间');
        }
    }

    /**
     * startEvent 活动开始操作
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function startEvent()
    {
        try {
            $data = array(
                'id' => $this->inputFilter($_REQUEST['id']),
                'begin_time' => time(),
                'status' => 1,
            );
            $this->statusFilter($data['id'], '0');//未开始的活动才能开始
            $this->couponModel->editOne($data);
            $this->ajaxReturn(1, '成功开始');
        } catch (\Exception $e) {
            $this->ajaxReturn(0, $e->getMessage());
        }
    }

    /**
     * endEvent 活动结束操作
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function endEvent()
    {
        try {
            $data = array(
                'id' => $this->inputFilter($_REQUEST['id']),
                'stop_time' => time(),
                'status' => 2,
            );
            $this->statusFilter($data['id'], '1');//已经开始的活动才能结束
            $this->couponModel->editOne($data);
            $this->ajaxReturn(1, '成功结束');
        } catch (\Exception $e) {
            $this->ajaxReturn(0, $e->getMessage());
        }
    }

    /**
     * statusFilter 过滤不符合的状态设置
     *
     * @modify_time 2015-08-21
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @param mixed $status
     * @access private
     * @return void
     */
    private function statusFilter($id, $status)
    {
        if ($this->couponModel->getFieldById($id, 'status') !== $status) {
            throw new \Exception('活动状态不符合');
        }
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
     * edit 编辑优惠券页面
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function edit()
    {
        $eventType = C('COUPON_EVENT_TYPE');
        $id = $this->inputFilter($_REQUEST['id']);
        $this->assign('eventType', $eventType);
        $this->assign('info', $this->couponModel->getOne($id));
        $this->display();
    }

    /**
     * editCoupon 编辑优惠券操作
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function editCoupon()
    {
        try {
            $data = array(
                'id' => $this->inputFilter($_REQUEST['id']),
                'type' => $this->inputFilter($_REQUEST['type']),
                'support_type' => $this->inputFilter($_REQUEST['support_type']),
                'coupon_name' => $this->inputFilter($_REQUEST['coupon_name']),
                'coupon_title' => $this->inputFilter($_REQUEST['coupon_title']),
                'coupon_subtitle' => $this->inputFilter($_REQUEST['coupon_subtitle']),
                'coupon_amount' => $this->inputFilter($_REQUEST['coupon_amount']),
                'coupon_count' => $this->inputFilter($_REQUEST['coupon_count']),
                'coupon_prefix' => $this->inputFilter($_REQUEST['coupon_prefix']),
                'coupon_summary' => $this->inputFilter($_REQUEST['coupon_summary']),
                'service_area' => $this->inputFilter($_REQUEST['service_area']),
                'start_time' => $this->toUnixTime($this->inputFilter($_REQUEST['start_time'])),
                'end_time' => $this->toUnixTime($this->inputFilter($_REQUEST['end_time'])),
            );
            $this->existFilter($data['coupon_name'], '活动名称');
            $this->supportTypeFilter($data);
            $this->prefixFilter($data);
            $this->priceFilter($data);
            $this->timeFilter($data);
            $this->numberFilter($data);
            $this->statusFilter($data['id'], '0');//未开始的活动才能编辑
            if ($images = $this->upload('/UPLOADS/Admin/coupon/')) {
                $data['coupon_thumb'] = $images[0];
                $data['coupon_detail_img'] = $images[1];
            }
            $this->couponModel->editOne($data);
            $this->success('编辑成功');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * deleteCoupon 删除优惠券活动
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function deleteCoupon()
    {
        try {
            $id = $this->inputFilter($_REQUEST['id']);
            $this->deleteFilter($id);
            $this->couponModel->deleteCoupon($id);
            $this->ajaxReturn(1, '删除成功');
        } catch (\Exception $e) {
            $this->ajaxReturn(0, $e->getMessage());
        }
    }

    /**
     * deleteFilter 删除过滤
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $id
     * @access private
     * @return void
     */
    private function deleteFilter($id)
    {
        $this->couponCodeModel->deleteFilter($id);
    }

    /**
     * inputFilter 输入基本过滤
     *
     * @version 2015-08-18
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $input
     * @access private
     * @return mixed
     */
    private function inputFilter($input)
    {
        return $input;
    }

    /**
     * upload 上传单张图片
     *
     * @version 2015-08-20
     * @author zxj <tenkanse@hotmail.com>
     * @param mixed $savePath 图片保存路径
     * @access public
     * @return string 图片地址
     */
    public function upload($savePath)
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath =  SITE_ROOT.$savePath;// 设置附件上传目录
        $upload->saveRule =  'uniqid';
        if ($upload->upload()) {//上传成功
            $info =  $upload->getUploadFileInfo();
            foreach ($info as $image) {
                $images[] = $savePath.$image['savename'];
            }
            return $images;
        }
        return;
    }

    /**
     * viewDetail 查看优惠券领取详情
     *
     * @modify_time 2015-08-24
     * @author zxj <tenkanse@hotmail.com>
     * @access public
     * @return void
     */
    public function viewDetail()
    {
        try {
            $data = array(
                'id' => $this->inputFilter($_REQUEST['id']),
                'coupon_code' => $this->inputFilter($_REQUEST['coupon_code']),
                'status' => $this->inputFilter($_REQUEST['status']),
            );

            list($page, $list) = $this->couponCodeModel->getList($data);
            
            $this->assign('statistics', $this->couponCodeModel->getStatistics($data['id']));
            $this->assign('info', $this->couponModel->getOne($data['id']));
            $this->assign('list', $list);
            $this->assign('page', $page);
            $this->display('detail');
        } catch (\Exception $e) {
        }
    }
}
