<?php
//订单
class DianpinghomeAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //订单表
		$this->dphome_linshi = M('tp_xieche.dphome_linshi','xc_');  //订单表
	}


	/*
		@author:wwy
		@function:入口
		@parameter:
		@time:2015/5/27
	*/
	function index(){
		$body =array("msg"=>"FuncionName and parameter must be set");
		$this->result_json('1','failure',$body);
	}

	/*
		@author:wwy
		@function:点评到家下单接口
		@parameter:
		@time:2015/5/27
	*/
	function createOrder(){
		if($_REQUEST['cityId']){
			$data['city_id'] = $this->get_cityid($_REQUEST['cityId']);//城市ID
		}else{
			$body =array("msg"=>"cityId must be set");
			$this->result_json('1','failure',$body);
		}
		if($_REQUEST['cityId']!=1 and $_REQUEST['productId']==29){
			$body =array("msg"=>"product does not exists in this city");
			$this->result_json('1','failure',$body);
		}
		$data['order_time'] = strtotime(htmlspecialchars(addslashes($_REQUEST['serviceTime'])));//服务开始时间
		$data['address'] = htmlspecialchars(addslashes($_REQUEST['serviceAddress']));//服务地址
		$data['mobile'] = htmlspecialchars(addslashes($_REQUEST['mobile']));//用户电话
		$data['productId'] = intval(trim($_REQUEST['productId']));//产品ID
		$data['remark'] = htmlspecialchars(addslashes($_REQUEST['comment']));//用户备注
		$data['car_info'] = htmlspecialchars(addslashes($_REQUEST['car_info']));//车型数据
		//print_r($data);
		$id = $this->dphome_linshi->add($data);
		if($id){
			$body =array("id"=>$id);
			$this->result_json('0','success',$body);
		}else{
			$body =array("msg"=>"create failure");
			$this->result_json('1','failure',$body);
		}
	}

	//转换点评城市ID
	function get_cityid($cityid){
		$cityid = intval(trim($cityid));
		$cityid_transfer = array('1'=>'1',//上海
								'3'=>'2',//杭州
								'6'=>'3',//苏州
								'8'=>'4' //成都
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
		return  json_encode($array);
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
        //可预约天数
        if($_REQUEST['queryDayNum']){
            $queryDayNum = intval(trim($_REQUEST['queryDayNum'])); 
        }else{
            $queryDayNum = 7 ;
        }
        
        //是否传递城市id
        if($_REQUEST['cityId']){
            //城市id 
            $cityId = intval(trim($_REQUEST['cityId']));
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
            //print_r($dateArray) ;
            $code = '0' ;
            $msg = 'success' ;
            $body = array('timeList'=>$dateArray);
            echo  $this->result_json($code,$msg,$body); 		   
        }else{  
            $code = '1' ;
            $msg = 'failure' ;
            $body = array('timeList'=>'');
            echo  $this->result_json($code,$msg,$body);
        }
           
    }
    
    
    public function getTimeslot($dateArray,$cityId) {
        foreach ($dateArray as $key => $value){
            $dateArray[$key]['timeslot']= '';
            for($h=0;$h<=23;$h++){
                //$dateArray[$key]['time'][] = date('Y-m-d H:i:s',strtotime($value['date'].'+'.$h.' hours')) ;
                if($h>=0 && $h<8){
                  $dateArray[$key]['timeslot'].= '00';  
                }elseif($h>=8 && $h<22){
                    if($cityId==1){   //如果是上海，查询判断是否过分下单
                        $order_time =  date('Y-m-d H:i:s',strtotime($value['date'].'+'.$h.' hours')) ;
                        $order_num = $this->getOrderNum($order_time);
                        if($order_num>=12){
                           $dateArray[$key]['timeslot'].= '00';  
                        }else{
                           $dateArray[$key]['timeslot'].= '11'; 
                        }   
                    }else{
                        $dateArray[$key]['timeslot'].= '11'; 
                    }          
                }elseif($h>=22){
                  $dateArray[$key]['timeslot'].= '00';    
                }
            }    
        }
        
        return $dateArray ;    
    }
    
    //查询返回某个时间点的订单总数
    function getOrderNum($order_time){
        $map['order_time'] = strtotime($order_time);
        $map['status'] = array('lt','8');
        $map['truename'] = array('notlike','%测试%');
        $map['city_id'] = 1;
        
        $list = $this->reservation_order_model->where($map)->Distinct(true)->field('mobile')->select();
        $order_num=0;
        foreach($list as $k=>$v){
            $order_num++;
        }
        return  $order_num ;     
    }
    
    
    //订单已支付接口
    public function orderPaied(){
        if($_REQUEST['orderId'] && $_REQUEST['settlePrice'] ){
            $settlePrice = floatval(trim($_REQUEST['settlePrice']));
            //获取真实订单id
            $orderId = intval(trim($_REQUEST['orderId']));
            $map['id'] = $this->get_true_orderid($orderId);
            $amountArr = $this->reservation_order_model->where($map)->field('amount')->find();
            //判断金额是否相等，然后返回json数组。
            if($settlePrice == $amountArr['amount']){
                $data['pay_status'] = 1 ;
                $this->reservation_order_model->where($map)->save($data);
                $code = '0' ;
                $msg = 'success' ;
                echo  $this->result_json($code,$msg);
            }else{
                $code = '1' ;
                $msg = 'failure' ;
                echo  $this->result_json($code,$msg);	
            }  
        }else{  
            $code = '1' ;
            $msg = 'failure' ;
            echo  $this->result_json($code,$msg);		
        }
    }
	

    //取消订单接口
    public function cancelOrder() {
        if($_REQUEST['orderId']){
            //获取真实订单id
            $orderId = intval(trim($_REQUEST['orderId']));
            $map['id'] = $this->get_true_orderid($orderId); 
            //更新订单
            $data['status'] = 8;
            $this->reservation_order_model->where($map)->save($data);
            $code = '0' ;
            $msg = 'success' ;
            echo  $this->result_json($code,$msg);		
        }else{
            $code = '1' ;
            $msg = 'failure' ;
            echo  $this->result_json($code,$msg);	
        }    
    }
	
    //签名验证方法
    public function signAuthen() {
        $para['methodName'] = trim($_REQUEST['methodName']) ;
        $para['version'] = trim($_REQUEST['version']) ;
        $para['partnerId'] = trim($_REQUEST['partnerId']) ;
        $para['productId'] = trim($_REQUEST['productId']) ;
        $para['serviceAddress'] = trim($_REQUEST['serviceAddress']) ;
        $sign = trim($_REQUEST['sign']) ;
        if($para['methodName']&&$para['version']&&$para['partnerId']&&$para['productId']&&$para['serviceAddress']&&$sign)
        {
            //拼接签名字符串
            $combinedSign = '';
            //点评提供的密钥
            $appKey = '5631F4283BDF08CFD98129A287090239' ;
            //对参数数组升序排序
            ksort($para);
            //循环拼接
            foreach ($para as $k => $v) {
                $combinedSign .= $k . urldecode($para[$k]);
            }
            //拼接appkey
            $combinedSign .= $appKey ;
            //md5加密转为大写
            $combinedSign = strtoupper(md5($combinedSign));
            
            //比较签名是否相等
            if($combinedSign == $sign){
                return true ;
            }else{
                return false ;
            }   
        }else{
            return false ;
        }      
    }
	
}