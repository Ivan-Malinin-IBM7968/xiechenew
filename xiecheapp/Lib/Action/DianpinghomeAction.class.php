<?php
//订单
class DianpinghomeAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //订单表
		$this->dphome_linshi = M('tp_xieche.dphome_linshi','xc_');  //订单表
        $this->package = M('tp_xieche.package','xc_');  //套餐表
		$this->city_package = M('tp_xieche.city_package','xc_');  //城市可用套餐表
	}


	/*
		@author:wwy
		@function:入口
		@parameter:
		@time:2015/5/27
	*/
	function index(){
		$body =array("msg"=>"FunctionName and Parameter must be set");
		echo $this->result_json('1','failure',$body);
	}

	/*
		@author:wwy
		@function:点评到家下单接口
		@parameter:
		@time:2015/5/27
	*/
	function createOrder(){
                //先执行签名验证
                $rs = $this->signAuthen() ;
                if($rs){
                    if($_REQUEST['cityId']){
                        $data['city_id'] = $this->get_cityid($_REQUEST['cityId']);//城市ID
                    }else{
                        $body =array("msg"=>"cityId must be set");
                        $this->result_json('1','failure',$body);
                    }
                    
//                    if($_REQUEST['cityId']!=1 and $_REQUEST['productId']==29){
//                            $body =array("msg"=>"product does not exists in this city");
//                            $this->result_json('1','failure',$body);
//                    }
                    
                    $data['order_time'] = strtotime(htmlspecialchars(addslashes($_REQUEST['serviceTime'])));//服务开始时间
                    $data['address'] = htmlspecialchars(addslashes($_REQUEST['serviceAddress']));//服务地址
                    $data['housenumber'] = htmlspecialchars(addslashes($_REQUEST['houseNumber']));//门牌号
                    
                    $data['mobile'] = htmlspecialchars(addslashes($_REQUEST['cellphone']));//用户电话
                    $data['productId'] = intval(trim($_REQUEST['productId']));//产品ID
                    $data['price'] = floatval(trim($_REQUEST['price']));//产品价格
                    $data['remark'] = htmlspecialchars(addslashes($_REQUEST['comment']));//用户备注
                    $data['car_info'] = htmlspecialchars(addslashes($_REQUEST['carInfo']));//车型数据
                    $data['create_time'] = time();  //创建时间
                    //print_r($data);
                    $id = $this->dphome_linshi->add($data);
                    if($id){
                            $body =array("orderId"=>"$id");
                            $this->result_json('0','success',$body);
                    }else{
                            $body =array("msg"=>"create failure");
                            $this->result_json('1','failure',$body);
                    }    
                }else{
                    $body =array("msg"=>"signature verification failed");
                    $this->result_json('10001','failure',$body);
                }
	}

	//转换点评城市ID
	function get_cityid($cityid){
		$cityid = intval(trim($cityid));
		$cityid_transfer = array('1'=>'1',//上海
								'3'=>'2',//杭州
								'6'=>'3',//苏州
								'8'=>'4', //成都
                                '22'=>'5',  //济南  
                                '14'=>'6'  //福州  
								);
		$city_id = $cityid_transfer[$cityid];
		return $city_id;
	}

	//返回JSON
	function result_json($code,$msg,$body=''){
		$array = array(
			'code'=>$code,
			'msg'=>$msg,
			'body'=>$body
		);
                //print_r($array) ;
		echo   json_encode($array);
               
	}

	//根据套餐ID返回配件以及价格数据
	function get_serviceinfo($cityid){
		
	}
	
    /*
    * wql@2015/5/27
    */
        
    //获取产品的可预约时间列表 
    public function  queryAvailableTimeslots()
    {
        //先执行签名验证
        $rs = $this->signAuthen() ;
        if($rs){
            //可预约天数
            if($_REQUEST['queryDayNum']){
                $queryDayNum = intval(trim($_REQUEST['queryDayNum'])); 
            }else{
                $queryDayNum = 7 ;
            }

            //是否传递城市id
            if($_REQUEST['cityId'] && $_REQUEST['latitude'] && $_REQUEST['longitude']){
                //转换点评城市id为携车城市id
                $cityId = $this->get_cityid(intval(trim($_REQUEST['cityId']))) ;
                $lat1 = trim($_REQUEST['latitude']);
                $long1 = trim($_REQUEST['longitude']);
                //判断是否在服务范围内
                    //定义城市中心坐标
                $cityCenter = array(
                    '1'=>array('lat'=>'31.152531','long'=>'121.460402','length'=>30), //上海
                    '2'=>array('lat'=>'30.280256','long'=>'120.125449','length'=>16.5), //杭州
                    '3'=>array('lat'=>'31.351095','long'=>'120.657245','length'=>20), //苏州
                    '4'=>array('lat'=>'30.679878','long'=>'104.072657','length'=>12.5), //成都
                    '5'=>array('lat'=>'36.660583','long'=>'117.034269','length'=>12), //济南
                    '6'=>array('lat'=>'26.047634','long'=>'119.287092','length'=>10), //福州
                );
                $centerArr = $cityCenter[$cityId] ;
                $lat2 = $centerArr['lat'] ;
                $long2 = $centerArr['long'] ;
                //服务范围长度
                $length = $centerArr['length'] ;
                //计算两个经纬度的距离
                $distance =  $this->GetDistance($lat1,$long1,$lat2,$long2);
                $distance = round($distance/1000,2);
                if($distance<$length){  //服务范围
                    //日期数组生成
                    $dateArray = array();
                    $date_start = date('Y-m-d ',strtotime('+1 day')) ;
                    for($i=0 ;$i<$queryDayNum;$i++){
                        $dateArray[]['date'] = date('Y-m-d',strtotime($date_start.'+'.$i .' day')) ;    
                    }

                    if($cityId==1){
                        //echo '上海' ;
                        //追加timeslot字段
                        $dateArray = $this->getTimeslot($dateArray,$cityId);   
                    }else{
                        //echo '其他城市';
                        //追加timeslot字段
                        $dateArray = $this->getTimeslot($dateArray,$cityId);         
                    }
                    $code = '0' ;
                    $msg = 'success' ;
                    $body = array('timeList'=>$dateArray);
                    $this->result_json($code,$msg,$body); 
                    
                }else{ //不可服务范围
                    $body =array("msg"=>"not in service area !");
                    $this->result_json('20001','failure',$body);
                }
                  		   
            }else{  
                $code = '1' ;
                $msg = 'failure' ;
                //$body = array('timeList'=>'');
                $this->result_json($code,$msg);
            }
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        }
         
    }
    
    
    public function getTimeslot($dateArray,$cityId) {
        foreach ($dateArray as $key => $value){
            //如果时间大于今天16点，那么第二天都无法预约，其他日期可以预约
            if($key==0){ //第二天
                $fourtime =  strtotime(date('Y-m-d 16:00:00'));
                if(time()>$fourtime){
                    for($h=0;$h<=23;$h++){
                        $dateArray[$key]['timeslot'].= '00';
                    }
                }else{ 
                   //获取可预约时间 
                   $dateArray[$key]['timeslot'] = $this->getAbleTime($key,$value,$cityId) ; 
                }
                
            }else{
                //获取可预约时间
                $dateArray[$key]['timeslot'] = $this->getAbleTime($key,$value,$cityId) ;
            }
        }
        
        return $dateArray ;    
    }
    
    
    function getAbleTime($key,$value,$cityId) {
        $ableTimeStr = '';
        //判断所传日期是不是周一，周一下午两点不能下单 ,城市为上海
        $week = date('w',strtotime($value['date'])) ;
        
        if($week == 1 && $cityId==1){  //周一 ，上海
            $ableTimeStr = $this->getTimeStr(9,14);
        }else{
            for($h=0;$h<=23;$h++){
                //$dateArray[$key]['time'][] = date('Y-m-d H:i:s',strtotime($value['date'].'+'.$h.' hours')) ;
                if($h>=0 && $h<9){
                  $ableTimeStr.= '00';  
                }elseif($h>=9 && $h<=19){
                    $order_time =  date('Y-m-d H:i:s',strtotime($value['date'].'+'.$h.' hours')) ;
                    //获取当前循环小时内是否可预约字符串
                    $ableTimeStr.= $this->getCityTimeStr($cityId,$order_time);          
                }elseif($h>19){
                  $ableTimeStr.= '00';    
                }
            }  
        }
        
        //返回可预约时间字符串
        return $ableTimeStr ;
    }
    
    
    //返回城市所传时间是否可以预约。。
    function getCityTimeStr($cityId,$order_time) {
        //前面是城市id ，后面是订单限制数
        $cityNums = array(
            '1'=> '24',   //上海
            '2'=> '8',  //杭州
            '3'=> '8',  //苏州
            '4'=> '3',  //成都
            '5'=> '3',  //济南
            '6'=> '8',   //福州
        );
        

        $order_num = $this->getOrderNum($order_time,$cityId);
        if($order_num>=$cityNums[$cityId]){
           $ableTimeStr.= '00';  
        }else{
           $ableTimeStr.= '10'; 
        } 
        
        return  $ableTimeStr ;
        
    }
    
    
    //根据传过来的参数返回可预约时间
    //开始时间，结束时间。
    function getTimeStr($start,$end) {
        $ableTimeStr = '';
        if($start==0 && $end==0){
            for($h=0;$h<=23;$h++){
                $ableTimeStr.= '00';  
            }  
        }else{
            for($h=0;$h<=23;$h++){
                if($h>=$start && $h<=$end){
                   $ableTimeStr.= '10'; 
                }else{
                   $ableTimeStr.= '00';  
                }   
            }  
        }
        
        return  $ableTimeStr  ;
    }
    
    //查询返回某个时间点的订单总数
//    function getOrderNum($order_time){
//        $map['order_time'] = strtotime($order_time);
//        $map['status'] = array('lt','8');
//        $map['truename'] = array('notlike','%测试%');
//        $map['city_id'] = 1;
//        
//        $list = $this->reservation_order_model->where($map)->Distinct(true)->field('mobile')->select();
//        $order_num=0;
//        foreach($list as $k=>$v){
//            $order_num++;
//        }
//        return  $order_num ;     
//    }
    
    //查询返回某个城市某个时间点的订单总数
    function getOrderNum($order_time,$cityId){
        $order_num=0;
        
        //获取订单表中的订单数
        $map['order_time'] = strtotime($order_time);
        $map['status'] = array('lt','8');
        $map['truename'] = array('notlike','%测试%');
        $map['city_id'] = $cityId ;
        $list = $this->reservation_order_model->where($map)->Distinct(true)->field('mobile')->select();
        
        foreach($list as $k=>$v){
            $order_num++;
        }
        
        //获取临时订单表的订单数
        $con['order_time'] = strtotime($order_time);
        $con['status'] = 0 ;
        $con['pay_status'] = 1 ;
        $con['city_id'] = $cityId ;
        $dphome_list = $this->dphome_linshi->where($con)->Distinct(true)->field('mobile')->select();
        
        foreach($dphome_list as $k=>$v){
            $order_num++;
        }
        
        return  $order_num ;     
    }
    
    
    //获取产品列表函数  wql@20150612
    public function queryProductList() {
        //先执行签名验证
        $rs = $this->signAuthen() ;
        
        if($rs){
            if($_REQUEST['pageSize']){
                $pageSize = intval(trim($_REQUEST['pageSize']));
            }else{
                $pageSize = 50 ;
            }

            if($_REQUEST['currentPage'] && $_REQUEST['cityId']){
                $currentPage = intval(trim($_REQUEST['currentPage']));
				//转换点评城市id为携车城市id
				$cityId = $this->get_cityid(intval(trim($_REQUEST['cityId']))) ;
                if($currentPage>0&&$cityId>0){
                    //根据城市和分页参数,对接平台获取城市可用的产品
                    $offSet = ($currentPage-1)*$pageSize;
                    $map['cityid'] = $cityId;
                    $map['platform_type'] = array('like','%1%');
                    $package = $this->city_package->where($map)->order('show_order asc')->limit(''.$offSet.','.$pageSize.'')->select();
                    $results = array();
                    foreach($package as $k=>$v){
                            $con['id'] = $v['packid'] ;
                            $results[] = $this->package->where($con)->find();
                    }
					
					//print_r($results);
                    //echo $this->package->getLastSql();
                    //获取需要的产品数组
                    $i = 0 ;
                    foreach ($results as $k => $v) {
                        $productList[$k]['productId'] = $v['id'] ;
                        $productList[$k]['productName'] = $v['dianping_name'] ;
                        //根据产品id和城市拼接产品描述
                        $productList[$k]['description']  =  '';
                        $productList[$k]['orderAmount'] = 0 ;
                        $productList[$k]['duration'] = 0 ;
                        $productList[$k]['abstract'] = $v['abstract'] ;
                        $productList[$k]['originalPrice'] = 0 ;
                        $productList[$k]['settlePrice'] = (float)$v['dp_price'] ;
                        $productList[$k]['minOrders'] = 1 ;
                        $productList[$k]['maxOrders'] = 1 ;
                        $productList[$k]['imageUrls'] = array($v['large_img']) ; //产品图片
                        $productList[$k]['thumbUrl'] = $v['small_img'] ;  //产品列表页图片
                        //图文混排数组生成
                        $productList[$k]['details'] =  $this->get_description_array($v['id'],$cityId);
                       //$productList[$k]['details'] = json_decode($v['imgTxt'],true) ;  //图文混排
                        //统计二维数组个数
                        $i++ ;
                    }
                    $totalSize = $i ;
					//print_r($productList);
					
                    //组合数组，转为json
                    $code = '0' ;
                    $msg = 'success' ;
                    $body = array(
                        'totalSize' => $totalSize ,
                        'productList' => $productList        
                    );
                    $this->result_json($code,$msg,$body); 

                }else{
                    $code = '1' ;
                    $msg = '分页参数和城市id不能为0或负数!' ;
                    $body = array();
                    $this->result_json($code,$msg,$body);   
                }  
            }else{
                $code = '1' ;
                $msg = '请传递当前分页参数和城市id!' ;
                $body = array();
                $this->result_json($code,$msg,$body);    
            }
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        }

    }
    
    

     //获取产品的服务描述 ,每次返回一个数组   wql@20150911  ...
    public function get_description_array($pro_id,$cityId) {
        if($pro_id==32){  //黄喜力套餐
            $description = array(
                array('type' => 2, 'content'=>  '服务内容
                1.矿物机油4L    ￥148
                壳牌黄喜力、嘉实多金嘉护，二选一
                2.品牌机滤1件    ￥40
                3.上门保养工时    ￥99
                4.汽车38项安全检测和6项细节养护    ￥98
                总价值    ￥385
                优惠价    ￥248

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                温馨提示
                套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

                场地要求：需要您提供1个停车位大小的场地 
                ')
            );  
        }elseif($pro_id==33){  //蓝喜力套餐
            $description =array(
                array('type' => 2, 'content'=>   '服务内容
                1.半合成机油4L    ￥248
                壳牌蓝喜力、嘉实多磁护，二选一
                2.品牌机滤1件    ￥50
                3.上门保养工时    ￥99
                4.汽车38项安全检测和6项细节养护    ￥98
                总价值    ￥495
                优惠价    ￥348

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                温馨提示
                套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

                场地要求：需要您提供1个停车位大小的场地 
                ')
            );  
        }elseif($pro_id==34){  //灰喜力套餐
            $description = array(
                array('type' => 2, 'content'=>  '服务内容
                1.全合成机油4L    ￥388
                金美孚、壳牌灰喜力、嘉实多极护、银美孚，四选一
                2.品牌机滤1件    ￥80
                3.上门保养工时    ￥99
                4.汽车38项安全检测和6项细节养护    ￥98
                总价值    ￥665
                优惠价    ￥498

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                温馨提示
                套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

                场地要求：需要您提供1个停车位大小的场地 
                 ' )
            ); 

        }elseif($pro_id==4){  //节气门清洗
            $description = array(
                array('type' => 2, 'content'=>      '服务内容
                1.清洗去除节气门积碳    ￥168
                2.汽车38项安全检测和6项细节养护    ￥98
                总价值    ￥266
                优惠价    ￥98

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                场地要求：需要您提供1个停车位大小的场地 
                ')   
            ); 

        }elseif($pro_id==29){ //发动机舱精洗
            $description = array(
                array('type' => 2, 'content'=>   '服务内容
                    1.发动机舱精洗线路元件养护    ￥168
                    2.汽车38项安全检测和6项细节养护    ￥98
                    总价值    ￥266
                    优惠价    ￥98

                    服务流程
                    1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                    2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                    3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                    携车网客服电话：400 660 2822 

                    场地要求：需要您提供1个停车位大小的场地 
                ')   
            ); 
                
        }elseif($pro_id==38){ //轮毂钢圈清洗套餐
            $description =array(
                array('type' => 2, 'content'=>    '服务内容
                1.清洗轮毂钢圈，去除砂石泥垢和油污  ￥138
                2.汽车38项安全检测和6项细节养护  ￥99
                总价值    ￥236
                优惠价    ￥98

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                场地要求：需要您提供1个停车位大小的场地  
                ')
            ); 
                 
        }elseif($pro_id==39){ //空调清洗除菌套餐
            $description = array(
                array('type' => 2, 'content'=>    '
                    建议搭配【品牌空调滤芯】一起使用，让车厢空气持久清新 
                    
                    服务内容
                    1.空调管道清洗和除菌    ￥138
                    2.汽车38项安全检测和6项细节养护    ￥98
                    总价值    ￥236
                    优惠价    ￥98

                    服务流程
                    1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                    2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                    3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                    携车网客服电话：400 660 2822 

                    场地要求：需要您提供1个停车位大小的场地 
                ')
            ); 
                   
        }elseif($pro_id==40){ //汽车检测和细节养护套餐
            $description = array(
                array('type' => 2, 'content'=> '服务内容
                1.7项细节养护：前挡风玻璃防雾处理、车门铰链润滑养护、车窗槽润滑养护、雨刮水补充、防冻液补充、发动机舱表面清洗、胎压调节和补充、
                2.38项汽车安全检测：包含制动系统、底盘部件、汽车油液的全方位安全检测
                优惠价    ￥38

                服务流程
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822

                场地要求：需要您提供1个停车位大小的场地 
                ')
            ); 
                
        }elseif($pro_id==41){ //品牌空调滤芯更换
            $description = array(
                array('type' => 2, 'content'=>    ' 注意事项：
                该产品仅适合30万以下车型使用
                携车网坚持为用户提供国际知名品牌，马勒、曼牌的空调滤芯，拒绝使用国内非知名小厂代工空调滤芯
                若您的车没有马勒、曼牌相匹配的配件，我们将优先选用原厂品牌的配件
                
                建议搭配【空调清洗除菌套餐】一起使用，让车厢空气持久清新 
                
                服务内容
                1.品牌空调滤    ￥98
                2.汽车38项安全检测和6项细节养护    ￥98
                总价值    ￥196
                优惠价    ￥68

                服务说明
                更换品牌空调滤芯，可高效过滤PM2.5等杂质，杀菌消毒除去异味，吸附车内甲醛等有机气体，保护车内人员健康，还您洁净空气
                汽车安全检测和细节养护，发送检测结果与保养建议，让您及时了解爱车状况

                服务流程：
                1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                携车网客服电话：400 660 2822
                ')
            ); 
   
        }elseif($pro_id==42){ //上门汽车保养人工服务
            $description =array(
                array('type' => 2, 'content'=> '服务内容
                    1.上门汽车保养人工服务    ￥99
                    4.汽车38项安全检测和6项细节养护    ￥99
                    总价值    ￥198
                    优惠价    ￥79

                    服务说明
                    上门人工保养服务，适合机油配件自备的用户，可为您更换机油、机滤、空调滤、空气滤
                    汽车安全检测和细节养护，发送检测结果与保养建议，让您及时了解爱车状况

                    服务流程：
                    1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
                    2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
                    3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

                    携车网客服电话：400 660 2822
                ')
            );         
        }
        
        //追加城市信息
        $serviceArr = array(
            '1'=>'上海：郊环线A30或G1501以内 ',
            '2'=>'上门服务范围：
            杭州：滨江区，江干区，山城区，下城区，西湖区，拱墅区，西溪阿里园区',
            '3'=>'上门服务范围：
            苏州：姑苏区（金阊区，沧浪区，平江区），吴中区，相城区，工业园区，虎丘区，苏州新区',
            '4'=>'上门服务范围：
            成都：绕城高速以内及高新区',
            '5'=>'济南：南至二环南路以内，北至小清河，东至国际会展中心，西至济南高铁西站 '    
        );
        $service_area = $serviceArr[$cityId];
        $description[]= array('type' => 2, 'content'=>$service_area);
        
        //追加图片信息
        $picArr = array(
            '32'=>array(  //黄喜力套餐
                'http://xieche.com.cn/Public_new/images/product/B001.jpg',
                'http://xieche.com.cn/Public_new/images/product/B002.jpg',
                'http://xieche.com.cn/Public_new/images/product/B003.jpg',
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '33'=>array(  //蓝喜力套餐
                'http://xieche.com.cn/Public_new/images/product/B001.jpg',
                'http://xieche.com.cn/Public_new/images/product/B002.jpg',
                'http://xieche.com.cn/Public_new/images/product/B003.jpg',
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '34'=>array(   //灰喜力套餐
                'http://xieche.com.cn/Public_new/images/product/B001.jpg',
                'http://xieche.com.cn/Public_new/images/product/B002.jpg',
                'http://xieche.com.cn/Public_new/images/product/B003.jpg',
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),          
            '4'=>array(  //节气门清洗
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '29'=>array(  //发动机舱精洗
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '38'=>array( //轮毂钢圈清洗套餐
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ), 
            '39'=>array(  //空调清洗除菌套餐
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '40'=>array(   //汽车检测和细节养护套餐
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '41'=>array(   //品牌空调滤芯更换
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                ),
            '42'=>array(   //上门汽车保养人工服务
                'http://xieche.com.cn/Public_new/images/product/B004.jpg',
                'http://xieche.com.cn/Public_new/images/product/B005.jpg',
                'http://xieche.com.cn/Public_new/images/product/B006.jpg'
                )
        );
        
        $proid_array = $picArr[$pro_id];
        foreach($proid_array as $k=>$v){
            $description[]= array('type' => 1, 'content'=>$v);
        }
        
        return  $description ;
    }
    
    
    
    //获取产品的服务描述
    public function get_description($pro_id,$cityId) {
        if($pro_id==32){  //黄喜力套餐
            $description = '服务内容
            1.矿物机油4L    ￥148
            壳牌黄喜力、嘉实多金嘉护，二选一
            2.品牌机滤1件    ￥40
            3.上门保养工时    ￥99
            4.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥385
            优惠价    ￥199

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            温馨提示
            套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

            场地要求：需要您提供1个停车位大小的场地 
             ' ;
        }elseif($pro_id==33){  //蓝喜力套餐
            $description = '服务内容
            1.半合成机油4L    ￥248
            壳牌蓝喜力、嘉实多磁护，二选一
            2.品牌机滤1件    ￥50
            3.上门保养工时    ￥99
            4.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥495
            优惠价    ￥299

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            温馨提示
            套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

            场地要求：需要您提供1个停车位大小的场地 
            ' ;
        }elseif($pro_id==34){  //灰喜力套餐
            $description = '服务内容
            1.全合成机油4L    ￥388
            金美孚、壳牌灰喜力、嘉实多极护、银美孚，四选一
            2.品牌机滤1件    ￥80
            3.上门保养工时    ￥99
            4.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥665
            优惠价    ￥399

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            温馨提示
            套餐内标配的产品和服务是不另行收费。如需加机油使用量或其他配件，可在客服致电您时一并预约。

            场地要求：需要您提供1个停车位大小的场地 
             ' ;
        }elseif($pro_id==4){  //节气门清洗
            $description = '服务内容
            1.清洗去除节气门积碳    ￥168
            2.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥266
            优惠价    ￥68

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            场地要求：需要您提供1个停车位大小的场地 
            ' ;
        }elseif($pro_id==29){ //发动机舱精洗
            $description = '服务内容
            1.发动机舱精洗线路元件养护    ￥168
            2.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥266
            优惠价    ￥68

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822 
            
            场地要求：需要您提供1个停车位大小的场地 
            ' ;
        }elseif($pro_id==38){ //轮毂钢圈清洗套餐
            $description = '服务内容
            1.清洗轮毂钢圈，去除砂石泥垢和油污  ￥138
            2.汽车38项安全检测和6项细节养护  ￥99
            总价值    ￥236
            优惠价    ￥38

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            场地要求：需要您提供1个停车位大小的场地  
            ' ;
        }elseif($pro_id==39){ //空调清洗除菌套餐
            $description = '服务内容
            1.空调管道清洗和除菌    ￥138
            2.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥236
            优惠价    ￥38

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822 
            
            场地要求：需要您提供1个停车位大小的场地 
            ' ;
        }elseif($pro_id==40){ //汽车检测和细节养护套餐
            $description = '服务内容
            1.7项细节养护：前挡风玻璃防雾处理、车门铰链润滑养护、车窗槽润滑养护、雨刮水补充、防冻液补充、发动机舱表面清洗、胎压调节和补充、
            2.38项汽车安全检测：包含制动系统、底盘部件、汽车油液的全方位安全检测
            总价值    ￥99
            优惠价    ￥38

            服务流程
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            
            场地要求：需要您提供1个停车位大小的场地 
            ' ;
        }elseif($pro_id==41){ //品牌空调滤芯更换
            $description = ' 注意事项：
            该产品仅适合30万以下车型使用
            携车网坚持为用户提供国际知名品牌，马勒、曼牌的空调滤芯，拒绝使用国内非知名小厂代工空调滤芯
            若您的车没有马勒、曼牌相匹配的配件，我们将优先选用原厂品牌的配件

            服务内容
            1.品牌空调滤    ￥98
            2.汽车38项安全检测和6项细节养护    ￥98
            总价值    ￥196
            优惠价    ￥68

            服务说明
            更换品牌空调滤芯，可高效过滤PM2.5等杂质，杀菌消毒除去异味，吸附车内甲醛等有机气体，保护车内人员健康，还您洁净空气
            汽车安全检测和细节养护，发送检测结果与保养建议，让您及时了解爱车状况

            服务流程：
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            ' ;            
        }elseif($pro_id==42){ //上门汽车保养人工服务
            $description = '服务内容
            1.上门汽车保养人工服务    ￥99
            4.汽车38项安全检测和6项细节养护    ￥99
            总价值    ￥198
            优惠价    ￥79

            服务说明
            上门人工保养服务，适合机油配件自备的用户，可为您更换机油、机滤、空调滤、空气滤
            汽车安全检测和细节养护，发送检测结果与保养建议，让您及时了解爱车状况

            服务流程：
            1.下单后，携车网客服在1-2个工作日内致电您，确认上门服务信息
            2.携车网服务技师携设备及配件，在预定时间到达，提供上门服务
            3.服务完成后，爱车交付车主验收。技师向车主礼貌告别

            携车网客服电话：400 660 2822
            ';    
        }
        
        
        $serviceArr = array(
            '1'=>'上海：郊环线A30或G1501以内 ',
            '2'=>'上门服务范围：
            杭州：滨江区，江干区，山城区，下城区，西湖区，拱墅区，西溪阿里园区',
            '3'=>'上门服务范围：
            苏州：姑苏区（金阊区，沧浪区，平江区），吴中区，相城区，工业园区，虎丘区，苏州新区',
            '4'=>'上门服务范围：
            成都：绕城高速以内及高新区',
            '5'=>'济南：南至二环南路以内，北至小清河，东至国际会展中心，西至济南高铁西站 '    
        );
        
        $service_area = $serviceArr[$cityId];
        
        return  $description .= $service_area ;
        
    }
    
    
    //订单已支付接口
//    public function orderPaied(){
//        if($_REQUEST['orderId'] && $_REQUEST['settlePrice'] ){
//            $settlePrice = floatval(trim($_REQUEST['settlePrice']));
//            //获取真实订单id
//            $orderId = intval(trim($_REQUEST['orderId']));
//            $map['id'] = $this->get_true_orderid($orderId);
//            $amountArr = $this->reservation_order_model->where($map)->field('amount')->find();
//            //判断金额是否相等，然后返回json数组。
//            if($settlePrice == $amountArr['amount']){
//                $data['pay_status'] = 1 ;
//                $this->reservation_order_model->where($map)->save($data);
//                $code = '0' ;
//                $msg = 'success' ;
//                $this->result_json($code,$msg);
//            }else{
//                $code = '1' ;
//                $msg = 'failure' ;
//                $this->result_json($code,$msg);	
//            }  
//        }else{  
//            $code = '1' ;
//            $msg = 'failure' ;
//            $this->result_json($code,$msg);		
//        }
//    }
    
    //订单已支付接口 ，临时使用。
    public function orderPaied(){
        //先执行签名验证
        $rs = $this->signAuthen() ;
        if($rs){
            $map['id'] = intval(trim($_REQUEST['orderId']));
            $resluts = $this->dphome_linshi->where($map)->find();
            if(is_array($resluts)){
                $data['pay_status'] = 1 ;
                $data['pay_price']  =  $_REQUEST['settlePrice'] ;
                $this->dphome_linshi->where($map)->save($data);
                $code = '0' ;               
                $msg = 'success' ;
                $this->result_json($code,$msg);  
            }else{
                $code = '1' ;
                $msg = 'failure' ;
                $this->result_json($code,$msg);    
            }
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        } 
    }
    
	

    //取消订单接口
//    public function cancelOrder() {
//        if($_REQUEST['orderId']){
//            //获取真实订单id
//            $orderId = intval(trim($_REQUEST['orderId']));
//            $map['id'] = $this->get_true_orderid($orderId); 
//            //更新订单
//            $data['status'] = 8;
//            $this->reservation_order_model->where($map)->save($data);
//            $code = '0' ;
//            $msg = 'success' ;
//            $this->result_json($code,$msg);		
//        }else{
//            $code = '1' ;
//            $msg = 'failure' ;
//            $this->result_json($code,$msg);	
//        }    
//    }
    
    
      //取消订单接口 ,临时使用
    public function cancelOrder() {
        //先执行签名验证
        $rs = $this->signAuthen() ;
        if($rs){
            $map['id'] = intval(trim($_REQUEST['orderId']));
            $results = $this->dphome_linshi->where($map)->find();
            if(is_array($results)){
                if($results['order_id']){
                  //更新订单表
                  $data['status'] = 8;
                  $where['id'] = $results['order_id'] ;
                  $this->reservation_order_model->where($where)->save($data);  
                }else{
                  //更新临时表
                  $savedata['pay_status'] = 0 ;
                  $savedata['status'] = 2 ;
                  $this->dphome_linshi->where($map)->save($savedata);
                }
                $code = '0' ;               
                $msg = 'success' ;
                $this->result_json($code,$msg); 
            }else{
                $code = '1' ;
                $msg = 'failure' ;
                $this->result_json($code,$msg);    
            }
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        }    
    }
    
    //获取技师接口
    public function queryAvailableTechnicians() {
        //先执行签名验证
        $rs = $this->signAuthen() ;
        if($rs){
            $code = '0' ;               
            $msg = 'success' ;
            $body = array(
                'technicianIds'=>array("0")             
            );
            $this->result_json($code,$msg,$body);
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        }
        
    }
    
    
    //获取技师列表接口
    public function queryTechnicianList() {
        //先执行签名验证
        $rs = $this->signAuthen() ;
        if($rs){
            $code = '20003' ;               
            $msg = '该服务无技师列表！' ;
            $this->result_json($code,$msg);
        }else{
            $body =array("msg"=>"signature verification failed");
            $this->result_json('10001','failure',$body);
        }
        
    }
    
    
	
    //签名验证方法
    public function signAuthen() {
        unset($_REQUEST['_URL_']) ;
        $para = $_REQUEST ;
        //print_r($para);
       
        //拼接签名字符串
        $combinedSign = '';
        //点评提供的密钥
        //$appKey = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456' ;
		$appKey = '35046290D1F97B2531F7C6201E4192B8' ;
        //对参数数组升序排序
        ksort($para);
        //循环拼接
        foreach ($para as $k => $v) {
            if( $k != 'sign'){
                $combinedSign .= $k.$para[$k];
            }    
        }
        //拼接appkey
        $combinedSign .= $appKey ;
        //echo  '<br>' ;
        
        //md5加密转为大写
        $combinedSign = strtoupper(md5($combinedSign));
        //比较签名是否相等
        if($combinedSign == $para['sign']){
            return 1 ;
        }else{
            return 0 ;
        }   
      
    }
    
	
}