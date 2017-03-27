<?php
/*
 *   上门保养自动对账功能 。
 *    wql@20150525
 */
class CheckaccountAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->checkaccount = D('checkaccount');//对账结果表
		$this->reservation_order = D('reservation_order');//订单表
        $this->invoice_model = D('invoice');//发票表
		
	}
	
	public function index(){
        //echo '111'; 
        
        $this->display() ;
	}
        
        //上传csv文件方法
        public function uploadcsv(){
            //防止内存超过限制。
            set_time_limit(0);
            
            if ($_FILES['csv']['name']){
                import("ORG.Net.UploadFile");
                $upload = new UploadFile();
                $upload = $this->_upload_init($upload);
                if (!$upload->upload()) {
                        $this->error($upload->getErrorMsg());
                } else {
                        $uploadList = $upload->getUploadFileInfo();
                }
                
                //解析csv文件,获取csv数据。
                $file = fopen($uploadList[0]['savepath'].$uploadList[0]['savename'],'r');
                while ($data = fgetcsv($file)) { 
                    $account_data[] = $data ;
                }
                unset($account_data[0]);
                
                //生成时间查询条件
                //当月时间区间生成
                $firstDay = date($_REQUEST['start_time'] .' 00:00:00');
                $lastDay =  date($_REQUEST['end_time'] .' 23:59:59');
                //获取以前月份数据并插入数据库。
                $this->past_data($firstDay);
                               
                //echo  '1111' ;
                //exit ;
                
                
                //查询数据中最大匹配次数+1,并存入session
                $matchnum = $this->checkaccount->max('matchnum') ;
                $_SESSION['matchnum'] = $matchnum + 1 ;
                
                $map['pay_type'] = $_REQUEST['pay_type'];
                //在以前数据里面查询进行核对。
                $this->past_check($account_data,$map);
                
                //exit ;
                
                
                //获取本月数据并插入数据库。
                $firstDay = strtotime($firstDay);
                $lastDay  = strtotime($lastDay); 
                $this->curr_data($firstDay,$lastDay,$_REQUEST['pay_type']) ;
                
             
                //本月时间区间
                $map['order_time'] = array(array('elt', $lastDay),array('egt',$firstDay),"AND"); 
                //在当前月份数据里面查询。
                $this->curr_check($account_data,$map,$lastDay);
                
                
                //将需要的几个参数存到session里面.下面的函数里面会使用，免得传参了。
                $_SESSION['firstDay'] = $firstDay ;
                $_SESSION['lastDay']  = $lastDay ;
                $_SESSION['pay_type'] = $_REQUEST['pay_type'];
                /*
                 * 如何获取后台存在，淘宝不存在数据 type=4
                 */
                //获取淘宝id数组
                $tbidArr = $this->getTbidArr($account_data,$map);
                //print_r($tbidArr);
                //echo '<br>' ;
                
                //获取后台订单ID数组 。
                $rs =  $this->getDbidArr();
                //print_r($rs) ;exit ;
                
                //执行两个数组对比函数，获取后台存在，淘宝不存在数据 ，type=4 .
                $this->getType4Data($rs,$tbidArr);
                
                $this->success('完成对账，正在转向结果页面', U('/Balance/Checkaccount/results'));
            }else{
                $this->error('请选择您要导入的CSV文件！',U('/Balance/Checkaccount/index'));
            }
             
        }
        
        
        
        //查询对账结果
        public function results(){
             //当月时间区间
            $firstDay = $_SESSION['firstDay'] ;
            $lastDay  = $_SESSION['lastDay']  ;
            /*
             * 查询本次匹配到的以前月份的数据
             */
            $map['order_time'] = array('lt',$firstDay) ;
            $map['pay_type'] = $_SESSION['pay_type']  ;
            $map['matchnum'] = $_SESSION['matchnum']  ;
            $map['status'] =  1  ;
            $past_match = $this->checkaccount->where($map)->select();
            echo    $this->checkaccount->getLastSql() ;
            
            //订单详情路径
            $url = '/Admin/index.php/Carservice/carserviceorder/detail?id=' ;
            
            
            foreach ($past_match as $k => $v) {
                //导出csv
                $past_match[$k]['orderid_db_nolink'] = $v['orderid_db'] ;
                //多个订单id处理
                if(strpos($v['orderid_db'],',')){
                   $exArr = explode(',', $v['orderid_db']);
                   foreach ($exArr as $key => $value) {
                       $show_id = $this->get_orderid($value); 
                       $newurl = $url.$show_id ;
                       $exArr[$key] = "<a href='$newurl' target='_blank'>$value</a>";
                   }
                   $past_match[$k]['orderid_db'] = implode(',', $exArr);
                }else{
                    $v['show_id'] = $this->get_orderid($v['orderid_db']); 
                    $newurl = $url.$v['show_id'] ;
                    $past_match[$k]['orderid_db']="<a href='$newurl' target='_blank'>$v[orderid_db]</a>";
                }
                
                if($v['type']==2){
                    $past_type2[] = $past_match[$k] ;
                }elseif($v['type']==3) {
                    $past_type3[] = $past_match[$k] ;
                }  
            }
            
            /*
             * 查询在选择月份中核对的结果
             */
            //当月月份区间
            $con['order_time'] = array(array('elt', $lastDay),array('egt',$firstDay),"AND");
            $con['pay_type'] = $_SESSION['pay_type']  ;
            $con['matchnum'] = $_SESSION['matchnum']  ;
            $curr_match = $this->checkaccount->where($con)->select();
            
            //print_r($curr_match);
            
            
            foreach ($curr_match as $k => $v) {
                //导出csv
                $curr_match[$k]['orderid_db_nolink'] = $v['orderid_db'] ;
                //多个订单id处理
                if(strpos($v['orderid_db'],',')){
                   $exArr = explode(',', $v['orderid_db']);
                   foreach ($exArr as $key => $value) {
                       //获取是否开过发票
                       if($key==0){
                           $invoice = $this->invoice_model->where('order_id='.$value)->find();
                       }
                       $show_id = $this->get_orderid($value); 
                       $newurl = $url.$show_id ;
                       $exArr[$key] = "<a href='$newurl' target='_blank'>$value</a>";
                   }
                   //已开发票标识
                   if(is_array($invoice)){
                       $curr_match[$k]['invoice'] = 1 ;
                   }else{
                       $curr_match[$k]['invoice'] = 0 ;
                   }
                   $curr_match[$k]['orderid_db'] = implode(',', $exArr);
                }else{
                    if($v['orderid_db']){
                        //获取是否开过发票
                        $invoice = $this->invoice_model->where('order_id='.$v['orderid_db'])->find();
                        //已开发票标识
                       if(is_array($invoice)){
                           $curr_match[$k]['invoice'] = 1 ;
                       }else{
                           $curr_match[$k]['invoice'] = 0 ;
                       }
                       $v['show_id'] = $this->get_orderid($v['orderid_db']); 
                       $newurl = $url.$v['show_id'] ;
                       $curr_match[$k]['orderid_db']="<a href='$newurl' target='_blank'>$v[orderid_db]</a>";
                    } 
                }
                     
                if($v['type']==1){
                    $curr_type1[] = $curr_match[$k] ;
                }elseif($v['type']==2) {
                    $curr_type2[] = $curr_match[$k] ;
                }elseif($v['type']==3) {
                    $curr_type3[] = $curr_match[$k] ;
                }elseif($v['type']==4) {
                    $curr_type4[] = $curr_match[$k] ;
                }  
            }
            
            
            //print_r($curr_match) ;
            
            //统计每种情况的数量;
            $data['past_type2_num'] = count($past_type2);
            $data['past_type3_num'] = count($past_type3);
            $data['curr_type1_num'] = count($curr_type1);
            $data['curr_type2_num'] = count($curr_type2);
            $data['curr_type3_num'] = count($curr_type3);
            $data['curr_type4_num'] = count($curr_type4);
            
            $this->assign('data' ,$data);
            
            //print_r($curr_type3) ;
            
            //根据type进行csv导出
            $csvArr = array(
              '1'=>$past_type2,
              '2'=>$past_type3,
              '3'=>$curr_type1,
              '4'=>$curr_type2,
              '5'=>$curr_type3,
              '6'=>$curr_type4
            );
            
            if($_REQUEST['type']){
                $type = $_REQUEST['type'] ;
                $csvdata = $csvArr[$type] ;
                if($csvdata == $curr_type1){ //淘宝存在，后台不存在数据
                   $str = "淘宝订单号,淘宝金额\n"; 
                   foreach ($csvdata as $k => $v) {
                        $str .= "\t".$v['orderid_tb'].",".  $v['account_tb']."\n" ;
                    }
                }else{  //其他情况数据
                    $str = "淘宝订单号,淘宝金额,后台订单号,后台金额\n"; 
                    foreach ($csvdata as $k => $v) {
                        if(strpos($v['orderid_db'],','))
                        {
                           $v['orderid_db_nolink'] = str_replace(',', '##', $v['orderid_db_nolink']) ;
                        }
                        if(strpos($v['orderid_db'],','))
                        {
                           $v['account_db'] = str_replace(',', '##', $v['account_db']) ;
                        }
                        $str .= "\t".$v['orderid_tb'].",".  $v['account_tb'].",". $v['orderid_db_nolink'].",". $v['account_db']."\n" ;
                    }
                }
                $filename = date('Y-m-d').'.csv'; //设置文件名  
                $this->export_csv($filename,$str); //导出 
                exit ;
            }
            
            
            //  总金额 ，开票金额，
            $invoice['num_1'] = $this->countInvoice($past_type2);
            $invoice['num_2'] = $this->countInvoice($past_type3);
            //$invoice['num_3'] = $this->countInvoice($curr_type1);
            $invoice['num_4'] = $this->countInvoice($curr_type2);
            $invoice['num_5'] = $this->countInvoice($curr_type3);
            // print_r($invoice);
            $this->assign('invoice' ,$invoice);
            //$invoice['num_6'] = $this->countInvoice($curr_type4);
            //淘宝京东不存在
            $total_mount = 0 ;
            //已开发票
            $total_invoice = 0 ;
            foreach ($curr_type4 as $k => $v) {
                if($v['invoice']){
                    $total_invoice += $v['account_db'] ; 
                }
                $total_mount += $v['account_db'] ;   

            }
            $total_no_invoice = $total_mount- $total_invoice; 
            $curr_type4_num = array(
               'total_invoice' => $total_invoice ,
               'total_mount' => $total_mount ,
               'total_no_invoice' => $total_no_invoice ,
            );
            
            //print_r($curr_type4_num) ;
            $this->assign('curr_type4_num' ,$curr_type4_num);
           
           
            
            $this->assign('past_type2' ,$past_type2);
            $this->assign('past_type3' ,$past_type3);
            $this->assign('curr_type1' ,$curr_type1);
            $this->assign('curr_type2' ,$curr_type2);
            $this->assign('curr_type3' ,$curr_type3);
            $this->assign('curr_type4' ,$curr_type4);

            $this->display();
        }
        
        
        /*
         * 上传初始化函数
         */
        
        public function _upload_init($upload) {
            //设置上传文件大小
            $upload->maxSize = C('UPLOAD_MAX_SIZE');
            //设置上传文件类型
            $upload->allowExts = explode(',', 'csv');
            //设置附件上传目录
            //$upload->savePath = C('UPLOAD_ROOT') . '/Bidsource/';
            $upload->savePath = SITE_ROOT . 'UPLOADS/checkaccount/';
            $upload->saveRule = 'uniqid';

            $upload->uploadReplace = false;
            //$this->watermark = 1;水印
            return $upload;
	}
        
    
     //导出数据为csv  wql@20150709
    function export_csv($filename,$data)   
    {   
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8'); 
        header("Content-Disposition:attachment;filename=".$filename);   
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        header('Expires:0');   
        header('Pragma:public'); 
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        //print(chr(0xEF).chr(0xBB).chr(0xBF));//设置utf-8 + bom     
        echo $data;   
    }
    
    
    //统计金额
    public function countInvoice($data) {
        //总金额
        $rs['total_mount'] = 0 ;
        //已开发票
        $rs['total_invoice'] = 0 ;
        foreach ($data as $k => $v) {
            if($v['invoice']){
                $rs['total_invoice'] += $v['account_tb'] ; 
            }
            $rs['total_mount'] += $v['account_tb'] ;   

        }
        $rs['total_no_invoice'] = $rs['total_mount']- $rs['total_invoice']; 
        return  $rs ;
    }
    
        
        /*
         *  获取以前月份的数据并插入数据库
         */
        
        public function  past_data($firstDay){
              // echo "111111";
            //判断是否是第一次插入，如果是，才执行当月以前本年度月份数据插入
            if($this->checkaccount->count() == 0){
               //当月以前本年度月份区间生成    
                $frontStart = date('Y-01-01 00:00:00');
                $frontEnd  =  date('Y-m-d 23:59:59',  strtotime($firstDay.'-1 day'));
                
                //将时间转成时间戳
                $frontStart = strtotime($frontStart);
                $frontEnd   = strtotime($frontEnd); 

                //支付方式
                $cond['pay_type'] = array('in','4,7');
                //支付完成
                $cond['pay_status'] = 1 ;
                //过滤掉作废订单
                $cond['status'] = array('neq',8) ;
                //当月以前本年度月份区间
                $cond['order_time'] = array(array('elt', $frontEnd),array('egt',$frontStart),"AND");
                //查询符合条件的数据,插入对账结果表
                $this->add_checkaccount($cond);
            }
            
        }
        
        
        /*
         * 获取本月数据并插入对账结果表 。
         */
        public function curr_data($firstDay,$lastDay,$pay_type) {
            $cond['order_time'] = array(array('lt', $lastDay),array('gt',$firstDay),"AND");
            //支付方式
            $cond['pay_type'] = $pay_type ;
            if($this->checkaccount->where($cond)->count() == 0){
                //支付完成
                $cond['pay_status'] = 1 ;
                //过滤掉作废订单
                $cond['status'] = array('neq',8) ;
                //查询符合条件的数据,插入对账结果表
                $this->add_checkaccount($cond);
            }
            
        }
        
        /*
         *  使用csv数据在以前月份数据中查询
         */
        public function past_check($account_data,$map) {
            //过滤type=1数据 。因为type1代表淘宝京东csv存在，后台不存在数据。不过滤，会产生错误。
            //$map['type'] =  array('neq',1); 
            //只在type=0和4的数据里面查询 。type等于1,2,3不必查询。
            $map['type'] =  array('in','0,4');
            
             foreach ($account_data as $k => $v) {
                if($map['pay_type'] == 7){ //京东取值
                    $map['orderid_tb'] = trim($v[2]); 
                    $account_tb = $v[8];
                 }else{  //淘宝取值 ,id从T200P后面开始截取
                    $map['orderid_tb'] = trim(substr($v[2],5)) ; 
                    $account_tb = $v[6]; 
                }
                $rs = $this->checkaccount->where($map)->find();
                // echo $k ;
//                 if($map['orderid_tb']==1168280392211466) {
//                      echo  $this->checkaccount->getLastSql();exit ;
//                 }
                // var_dump($rs) ;
                // echo '<br>' ;
                //如果查询出来是一个数组
                if(is_array($rs)){
                     //核对完成状态
                     $data['status'] = 1 ;
                     //匹配的匹配次数
                     $data['matchnum'] = $_SESSION['matchnum'] ;
                     //淘宝金额
                     $data['account_tb'] = $account_tb ;
                     //后台金额处理
                     if(strpos($rs['account_db'], ',')){
                        $account_db_array = explode(',', $rs['account_db']);
                        $account_db = array_sum($account_db_array);
                     }else{
                        $account_db =  $rs['account_db'] ;
                     }
                     if($account_db != $account_tb){
                        //查询结果是一个数组,金额不对的，type=2 。
                        $data['type'] = 2; 
                     }elseif($account_db == $account_tb) {
                        //查询结果是一个数组,金额无误的，type=3 。
                        $data['type'] = 3;  
                     }
                     //更新条件
                    $where['orderid_tb'] = $rs['orderid_tb'] ;
                    //更新对账结果表。
                    $this->checkaccount->where($where)->save($data);
                    //echo  $this->checkaccount->getLastSql();
                }
            }
            
        }


        /*
         * 使用csv数据在当前月份数据里查询
         */
        public function curr_check($account_data,$map,$lastDay){
            foreach ($account_data as $k => $v) {
                if($map['pay_type'] == 7){ //京东取值
                   $map['orderid_tb'] = trim($v[2]); 
                   $con['orderid_tb'] = trim($v[2]); 
                   $account_tb = $v[8];
                }else{   //淘宝取值 ,id从T200P后面开始截取
                   $map['orderid_tb'] = trim(substr($v[2],5)) ; 
                   $con['orderid_tb'] = trim(substr($v[2],5)) ; 
                   $account_tb = $v[6]; 
                }  
                
                //检查csv数据是否完成核对,完成的话,更新其匹配次数，进入下一轮循环 wql@20150731
                $con['status'] = 1 ;
                $results = $this->checkaccount->where($con)->find();
                if(is_array($results)){
                    $update_array['matchnum'] = $_SESSION['matchnum'] ;
                    $update_array['status'] = 1 ;
                    $this->checkaccount->where('id='.$results['id'])->save($update_array);
                    //echo  $this->checkaccount->getLastSql();
                    //exit ;
                    //continue;
                    //echo '111' ;
                    continue;
                }
                
                
                $rs = $this->checkaccount->where($map)->find();
                //echo  $this->checkaccount->getLastSql();
                //echo  '<br>' ;
                //exit ;
               
                //如果查询结果为空，那就是淘宝京东有,后台无,type =1 .
                if($rs == NULL){
                    //匹配的匹配次数
                    $data['matchnum'] = $_SESSION['matchnum'] ;
                    //淘宝订单
                     $data['orderid_tb'] = $map['orderid_tb'] ;
                    //淘宝金额
                    $data['account_tb'] = $account_tb ;
                    //来源平台
                    $data['pay_type'] =  $map['pay_type'];
                    //异常类型
                    $data['type'] =  1;
                    //订单时间。暂时先用选取月份的最后一天。
                    $data['order_time'] = $lastDay  ;
                    //插入对账结果表。
                    $this->checkaccount->add($data);
                    //echo  $this->checkaccount->getLastSql();
                   // echo '<br>' ;
                }elseif(is_array($rs)&&$rs['type']!=1){
                    //核对完成状态
                    $save_data['status'] = 1 ;
                    //匹配的匹配次数
                    $save_data['matchnum'] = $_SESSION['matchnum'] ;
                    //淘宝金额
                    $save_data['account_tb'] = $account_tb ;
                    //后台金额处理
                    if(strpos($rs['account_db'], ',')){
                        $account_db_array = explode(',', $rs['account_db']);
                        $account_db = array_sum($account_db_array);
                    }else{
                        $account_db = $rs['account_db'] ; 
                    }
                    if($account_db != $account_tb){
                        //金额不对的，type=2 。
                        $save_data['type'] = 2; 
                    }elseif($account_db == $account_tb){
                        //金额无误的，type=3 。
                        $save_data['type'] = 3;  
                    }
                    //更新条件
                    $where['orderid_tb'] = $rs['orderid_tb'] ;
                    //更新对账结果表。
                    $this->checkaccount->where($where)->save($save_data);
                    //echo   $this->checkaccount->getLastSql();
                   // echo '<br>' ;
                }elseif(is_array($rs)&&$rs['type']==1){  
                 //可能会重复对某个月份的账目 。那么以前对到的type=1会进到这个分支。
                    //匹配的匹配次数
                    $updateArr['matchnum'] = $_SESSION['matchnum'] ;  
                     //更新条件
                    $condition['orderid_tb'] = $rs['orderid_tb'] ;
                    //更新对账结果表。
                    $this->checkaccount->where($condition)->save($updateArr); 
                    //echo   $this->checkaccount->getLastSql();
                   // echo '<br>' ;
                }
            } 
            
            //exit ;
            
        }
        
        
        
        /*
         * 获取淘宝id数组函数
         */
        
        function getTbidArr($account_data,$map) {
            foreach ($account_data as $k => $v) {
                if($map['pay_type'] == 7){  //京东取值
                   $map['orderid_tb'] = trim($v[2]); 
                }else{  //淘宝取值 ,id从T200P后面开始截取
                   $map['orderid_tb'] = trim(substr($v[2],5)) ; 
                }
                //存一个淘宝id数组,用于查询后台有，淘宝没有的数据。
                $tbidArr[] = $map['orderid_tb'] ; 
            }
            
            return  $tbidArr ; 
        }
        
        /*
         * 获取后台订单id数组集合
         */
        
        public function getDbidArr() {
            //条件组合
            $firstDay =  $_SESSION['firstDay'] ;
            $lastDay =  $_SESSION['lastDay'] ;
            $pay_type =  $_SESSION['pay_type'] ;
            
            $cond['order_time'] = array(array('lt', $lastDay),array('gt',$firstDay),"AND");
            $cond['pay_type'] = $pay_type ;
            $cond['pay_status'] = 1 ;
            //过滤掉作废订单
            $cond['status'] = array('neq',8) ;
            //后台订单id数组
            $rs = $this->reservation_order->field('taobao_id')->where($cond)->distinct(true)->select();
            
            //echo  $this->reservation_order->getLastSql();
            return  $rs ;  
        }
        
        
        /*
         * 获取后台存在 ，淘宝京东不存在数据，type =4 .
         */
        
        public function getType4Data($rs,$tbidArr){
            
            
            foreach ($rs as $k => $v) {
                //跳过完成核对的
                if(!in_array(trim($v['taobao_id']), $tbidArr))
                {
                    //更新对账结果表
                    $data['type'] = 4 ;
                    $data['matchnum'] = $_SESSION['matchnum'] ;
                    //更新条件
                    $where['orderid_tb'] = $v['taobao_id'] ;
                    $this->checkaccount->where($where)->save($data) ;
                    //echo  $this->checkaccount->getLastSql();
                }
            }
            //exit ;
        }
        

        /*
         * 查询符合条件的数据并插入对账结果表
         */
        
        public function add_checkaccount($cond){
            $rs = $this->reservation_order->field('id,taobao_id,amount,order_time,pay_type')->where($cond)->select();

            //echo '<br>' ;
            //插入对账结果表
            foreach ($rs as $k => $v) {

               //先检查是否有相同的订单号已经插入，如果有，更新该订单号。
               $exist = $this->checkaccount->where('orderid_tb='.$v['taobao_id'])->find();
               if(is_array($exist)){
                    $savedata['orderid_db'] = $exist['orderid_db'].','.$v['id'] ;
                    $savedata['account_db'] = $exist['account_db'].','.$v['amount'] ;
                    //test  
                    $savedata['type'] = 0 ;
                    
                    $savedata['order_time'] = $v['order_time'] ;
                    
                    $savemap['orderid_tb']  = $v['taobao_id'] ;
                    $this->checkaccount->where($savemap)->save($savedata);   
               }else{
                    $data['orderid_tb'] = trim($v['taobao_id']) ;
                    $data['orderid_db'] = $v['id'] ;
                    $data['account_db'] = $v['amount'] ;
                    $data['order_time'] = $v['order_time'] ;
                    $data['pay_type'] = $v['pay_type'] ;
                    $this->checkaccount->add($data);
//                   if($v['taobao_id']==1230060516615830){
//                       echo $this->checkaccount->getLastSql();
//                   }
               } 
            }
        }
	

}