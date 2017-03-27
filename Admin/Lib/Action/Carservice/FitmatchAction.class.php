<?php
//配件分配
class FitmatchAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->reservation_order = D('reservation_order');  //订单表
        $this->Technician = D('technician'); //技师表	
        $this->item_oil = D('new_item_oil');  //保养机油
        $this->item_filter = D('new_item_filter');  //保养项目中的机油滤清，空气滤清，空调滤清
	}
	
	function  index(){
            //明天此时的时间戳
           // echo  $current = strtotime(date('Y-m-d h:i:s')) + 3600*24 ;

            $tomorrow_start = strtotime(date('Y-m-d 00:00:00')) + 3600*24 ;
            $tomorrow_end   = strtotime(date('Y-m-d 23:59:59')) + 3600*24 ;
            //明天时间区间
            $map['order_time'] = array(array('elt', $tomorrow_end),array('egt',$tomorrow_start),"AND");
            //订单状态（已分配技师）
            $map['status']= 2 ;
            
            //查询明天符合要求的技师id数组
            $technicianAndOrder = $this->reservation_order->field('technician_id')->where($map)->distinct(true)->select();
            //echo  $this->reservation_order->getLastSql();
            foreach ($technicianAndOrder as $key => $value) {
                //获取技师名称
                $technician_name = $this->get_technician_name($value['technician_id']);
                //追加到数组
                $technicianAndOrder[$key]['truename'] = $technician_name ;
                
                //获取当前技师的明天订单
                $map['technician_id'] = $value['technician_id'];
                $order = $this->reservation_order->where($map)->select();
                //获取配件名称 和  二维码 ,价格
                foreach( $order as $k => $v) {
                    
                    $order_item = unserialize($v[item]) ;
                    $fitnameAndCode = $this->get_fitname($order_item) ;
                    $order[$k]['fitnameAndCode'] = $fitnameAndCode ;   
                }
                //追加到技师数组
                $technicianAndOrder[$key]['order'] = $order ;
                
            }
            
            /*
             * 查询获取明天未分配技师订单
             */
            
             //明天时间区间
            $cond['order_time'] = array(array('elt', $tomorrow_end),array('egt',$tomorrow_start),"AND");
            $cond['status'] = array('in','0,1');
            $unAllocateOrder = $this->reservation_order->where($cond)->select();
            //获取配件名称 和  二维码 ,价格
            foreach($unAllocateOrder as $k => $v) {
                $order_item = unserialize($v[item]) ;
                $fitnameAndCode = $this->get_fitname($order_item) ;
                $unAllocateOrder[$k]['fitnameAndCode'] = $fitnameAndCode ;   
            }
            
            //已分配技师订单  
            $this->assign('technicianAndOrder',$technicianAndOrder);
            //未分配技师订单
            $this->assign('unAllocateOrder',$unAllocateOrder);
            
            $this->display();
	}
        
        
        /*
         * 
         * 通过配件id获取配件名称 和  配件二维码
         */
        
        function  get_fitname($item){
            //删除不必要字段
            unset($item['oil_detail'],$item['price']);
                       
            $fitnameAndCode = array() ;
            foreach($item as $k => $v)
            {
                //查询条件
                $map['id'] = $v ;
                if($k=='oil_id'){
                    $fitinfo = $this->item_oil->where($map)->find();
                }elseif($k=='filter_id'||$k=='kongqi_id'||$k=='kongtiao_id') {
                    $fitinfo = $this->item_filter->where($map)->find();  
                }
                
                //配件id 
                $fitnameAndCode[$k] = $fitinfo['id'] ;
                
                //配件名称
                $key_name = str_replace('id', 'name', $k) ;
                $fitnameAndCode[$key_name] = $fitinfo['name'] ;
                //配件二维码
                $key_code = str_replace('id', 'code', $k) ;
                $fitnameAndCode[$key_code] = $fitinfo['code'] ;
                //配件价格
                $key_price = str_replace('id', 'price', $k) ;
                $fitnameAndCode[$key_price] = $fitinfo['price'] ;
                
                
            }
            
            return  $fitnameAndCode ;
        }
        
        
        /*
         * 根据技师id获取技师真实姓名
         */
        
        function  get_technician_name($id){
            $map['id'] = $id ;
            $technician = $this->Technician->where($map)->find();
            return $technician['truename'] ;  
        }
        
        
        
        /*
         * 
         * 错件 和 漏件 信息处理
         */
        
        function  error_fit(){
            
            /*
             * 错件信息处理
             */
            $error_fit  = $_REQUEST['error_fit'] ;
            
            //判断字符串是否为空
            if(strlen($error_fit)){
                //截掉最后一个@
                $error_fit = substr($error_fit,0,-1);
                //分割为数组
                $errorfit = explode('@', $error_fit);

                //查询数据库获取配件名称
                foreach ($errorfit as $k => $v) {
                    $map['code'] = $v ;
                    //先查机油表，再查机滤表
                    $rs = $this->item_oil->field(array('name','code'))->where($map)->find();
                    if(!$rs){
                        $rs = $this->item_filter->field(array('name','code'))->where($map)->find();
                    }
                    //获取配件名称 和 二维码
                    $errorfitArr[] = $rs ;

                }
                
            }
            

            /*
             * 漏件信息处理
             */
            $lack_fit  = $_REQUEST['lack_fit'] ;
             
            if(strlen($lack_fit)){
                //去掉最后一个逗号
                $lack_fit = substr($lack_fit,0,-1);
                //分割为数组
                $lack_fit = explode(',', $lack_fit); 
                foreach ($lack_fit as $key => $value) {
                    //去掉最后一个分隔符
                    $value = substr($value,0,-1);
                     //分割为数组
                    $value = explode('|', $value); 

                    //此处分离配件名和二维码
                    foreach ($value as $k => $v) {
                        if(strpos($v, '@')){
                            $v = explode('@', $v); 
                            $value[$k] = $v ;
                        }   
                    }
                    $lackfitArr[] = $value ;

                }
             
            } 
             
             
            $this->assign('errorfitArr',$errorfitArr);
            $this->assign('lackfitArr',$lackfitArr); 
            $this->display();
        }
        
        
        function  nobind_fit(){
            $nobind_fit  = $_REQUEST['nobind_fit'] ;
            $nobind_fit = substr($nobind_fit,0,-1);
            $nobindArr = explode(',',$nobind_fit) ;
            
           $nobindArr = array_unique($nobindArr);
           
           foreach($nobindArr as $key => $value){
               $value = explode('@',$value);
               $map['id'] = $value[0];
               //去数据库查找二维码,并将数组分为四个数组
               if($value[2]=='oil'){
                   $rs = $this->item_oil->where($map)->find();
                   $value[] = $rs['code'] ; 
                   $oilArr[] = $value ;
               }else{
                   $rs = $this->item_filter->where($map)->find();
                   $value[] = $rs['code'] ;
                   if($value[2]=='filter')$filterArr[] = $value ;
                   if($value[2]=='kongqi_filter')$kongqiFilterArr[] = $value ;
                   if($value[2]=='kongtiao_filter')$kongtiaoFilterArr[] = $value ;
               }
                   
           }
           
           //print_r($kongqiFilterArr);    
          
           $this->assign('oilArr',$oilArr);
           $this->assign('filterArr',$filterArr);
           $this->assign('kongqiFilterArr',$kongqiFilterArr);
           $this->assign('kongtiaoFilterArr',$kongtiaoFilterArr);
           
           
           
           $this->display();
            
        }
        
        /*
         * 单个绑定
         */
        public function ajax_bindcode(){
            $id = intval($_POST['id']);
            $code = trim($_POST['code']);
            $type = trim($_POST['type']);
            
            $save_data['code'] = $code;
            $condition['id'] = $id;
            //根据传过来的配件类型更新不同表
            if($type=='oil'){
                $this->item_oil->where($condition)->save($save_data);  
            }else{
                $this->item_filter->where($condition)->save($save_data);  
            }

            $return['errno'] = "0";
            $return['msg'] = "操作成功";
            $this->ajaxReturn( $return );
        }
        
        
        /*
         * 批量绑定
         */
        public function ajax_bindAll(){
            //这三个都是数组
            $id = $_POST['id'];
            $code = $_POST['code'];
            $type = $_POST['type'];
            
            //print_r($type);
            //循环type数组 ，去更新不同的表.oil,机油表 ；filter,机滤表
            foreach ($type as $key => $value) {
                $save_data['code'] = $code[$key];
                $condition['id'] = $id[$key];
 
                if($value == 'oil'){
                    $this->item_oil->where($condition)->save($save_data);
                }else{
                    $this->item_filter->where($condition)->save($save_data);
                } 
            }

            $return['errno'] = "0";
            $return['msg'] = "操作成功";
            $this->ajaxReturn( $return );
        }
        
        
    
        

}
?>