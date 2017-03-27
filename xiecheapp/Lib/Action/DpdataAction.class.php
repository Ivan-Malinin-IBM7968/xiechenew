<?php

class DpdataAction extends CommonAction
{
    function __construct() {
        parent::__construct();
        $this->dphome_linshi = D('dphome_linshi');  //点评数据临时表
        $this->city = D('city');  //城市表
        $this->interface_log = D('interface_log');   //返回信息记录表

    }
    
    function getDphomeData() {
	//header('Content-type: text/html;charset=UTF-8');
        //echo 'test';
        //exit ;
        
        $pwd =  Md5('fqcddph1234');
        //是否通过验证
        if(isset($_GET['pwd']) && $_GET['pwd']==$pwd){
            //echo '通过验证';
            //查询未下单数据倒叙显示
            $map['status'] = 0 ;
            //支付状态
            $map['pay_status'] = 1 ;
            $dpdata = $this->dphome_linshi->where($map)->order('create_time desc')->select() ;
            //print_r($dpdata);
            foreach ($dpdata as $k => $v) {
                $dpdata[$k]['city'] = $this->getCity($v['city_id']); 
                $dpdata[$k]['product'] = $this->getProInfo($v['productId']);
            }
            
            $this->assign('dpdata',$dpdata);
            $this->display('getDphomeData') ;    
        }else{
            exit ;    
        }
    }
    
    //获取城市
    public function getCity($city_id) {
        $map['id'] = $city_id ; 
        $rs = $this->city->where($map)->find();
        return  $rs['name'];
    }
    
    public function getProInfo($productId) {
        $proArray = array(
            '32'=>'经济车型上门小保养-黄壳4L机油+机滤+人工-199',
            '33'=>'中端车型上门小保养-蓝壳4L机油+机滤+人工-299',
            '34'=>'高端车型上门小保养-灰壳4L机油+机滤+人工-399',
            '4'=>'节气门清洗套餐-节气门清洗-68',
            '29'=>'发动机舱清洗套餐-发动机舱清洗-68',
            '38'=>'轮毂钢圈清洗套餐-38',
            '39'=>'空调清洗除菌套餐-38',
            '40'=>'汽车检测和细节养护套餐-9.8',
            '41'=>'品牌空调滤芯更换-68' ,
            '42'=>'上门汽车保养人工服务-79'
        );
        return $proArray[$productId] ;    
    }
    
    //ajax请求方法
    public function ajax_update_order() {
        $savedata['order_id'] = $_REQUEST['order_id'];
        $savedata['status'] = 1 ;
 
        $map['id'] = $_REQUEST['id'];
        
        $this->dphome_linshi->where($map)->save($savedata);
        //curl更新点评信息
        $uri = "http://m.api.dianping.com/tohome/openapi/xieche/updateOrderStatus";
        // 参数数组
        $data = array (
           'orderId' => $map['id'] ,   //传递临时表id
           'status' => '13',
           'methodName'=>'updateOrderStatus',
           'version'=>'1.0',
           'partnerId'=>'xieche'    
        );
        //计算签名，并返回参数字符串
        $data = $this->combinedSign($data);
        
        $ch = curl_init ();
        // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $ret = curl_exec($ch);
        curl_close ($ch);

        $return['errno'] = 0 ;
        $return['msg'] = '操作已完成' ;
        $return['dphome'] = json_decode($ret) ;
        $this->ajaxReturn( $return );
    }
    
    
    //取消订单，通知点评到家
    public function ajax_cancel_order() {
        $linshi_id = $_REQUEST['id'];
        
        //curl更新点评信息
        $uri = "http://m.api.dianping.com/tohome/openapi/xieche/partnerCancelOrder";
        // 参数数组
        $data = array (
           'orderId' => $linshi_id ,   //传递临时表id
           'methodName'=>'partnerCancelOrder',
           'version'=>'1.0',
           'partnerId'=>'xieche'    
        );
        //计算签名，并返回参数字符串
        $data = $this->combinedSign($data);
        
        $ch = curl_init ();
        // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $ret = curl_exec($ch);
        curl_close ($ch);

        $dphome = json_decode($ret,true) ;
        
        $return['errno'] = 0 ;
        if($dphome['code']==0){ //成功取消订单
            $return['msg'] = '点评到家已成功取消订单！' ;
            //更新临时表
            $savedata['status'] = 2 ;
            $map['id'] = $_REQUEST['id'];
            $this->dphome_linshi->where($map)->save($savedata);
        }else{  //取消订单失败
            $return['msg'] = '通知点评到家取消订单失败！' ;
        }
        
        
        $this->ajaxReturn( $return );
        
    }
    
    
    //通知点评订单已完成，用于刷单,  wql@20150805
    function ajax_complete_order(){
        $linshi_id = $_REQUEST['id'] ;
        //curl更新点评信息
        $uri = "http://m.api.dianping.com/tohome/openapi/xieche/updateOrderStatus";
        // 参数数组
        $data = array (
           'orderId' => $linshi_id ,   //传递临时表id
           'status' => '5', //服务完成
           'methodName'=>'updateOrderStatus',
           'version'=>'1.0',
           'partnerId'=>'xieche'    
        );
        //计算签名，并返回参数字符串
        $data = $this->combinedSign($data);
        //print_r($data);
        $ch = curl_init ();
        // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $ret = curl_exec($ch);
        curl_close ($ch);

        //print_r($ret);
        //将通知结果记录入库
        $dphome = json_decode($ret,true) ;
        $add_data['code'] = $dphome['code'] ;
        $add_data['msg'] = $dphome['msg'] ;
        $add_data['linshi_id'] = $linshi_id  ;
        $add_data['type'] = 1  ;
        $this->interface_log->add($add_data);
        //临时表订单状态改变
        $savedata['status'] = 3 ;
        $map['id'] = $linshi_id ;
        $this->dphome_linshi->where($map)->save($savedata);

    }
    
    
    //签名计算并返回参数字符串
    public function combinedSign($data) {
        //拼接签名字符串
        $sign = '';
        //点评提供的密钥
        //$appKey = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456' ;
		$appKey = '35046290D1F97B2531F7C6201E4192B8' ;
        //对参数数组升序排序
        ksort($data);
        //循环拼接
        foreach ($data as $k => $v) {
           // $sign .= $k . urldecode($data[$k]);
           $sign .= $k.$data[$k];
        }
        //拼接appkey
        $sign .= $appKey ;
        //md5加密转为大写
        $sign = strtoupper(md5($sign));
        $data['sign']= $sign ;
        
        //返回参数字符串
        $ret = '';
        foreach($data as $k=>$v){
            $ret .= $k.'='.$v.'&' ;
        }
        $ret = substr($ret,0,-1) ;
        return  $ret ;   
    }
    
    
    //导出全部订单  wql@20150806
    public function export_order() {
        $dpdata = $this->dphome_linshi->order('create_time desc')->select() ;
        //print_r($dpdata);
        $str = "临时id,城市,电话,地址,备注,产品id,产品价格,车型数据,服务开始时间,后台订单id,处理状态,支付状态,支付价格,创建时间\n";  
        foreach ($dpdata as $k => $v) {
            $v['city'] = $this->getCity($v['city_id']); 
            $v['product'] = $this->getProInfo($v['productId']);
            $v['order_time'] = date('Y-m-d H:i:s',$v['order_time']) ;
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']) ;
            
            //字符串需要的值,组织csv字符串
			$strArray = array($v['id'],$v['city'],$v['mobile'],$v['address'],$v['remark'],$v['product'],$v['price'],
              $v['car_info'],$v['order_time'],$v['order_id'],$v['status'],$v['pay_status'],$v['pay_price'],$v['create_time']);
            
            $str .= implode(',', $strArray)."\n";
        }
        
         //print_r($dpdata);
        $filename = date('Y-m-d').'-导出订单数据'.'.csv'; //设置文件名   
		$this->export_csv($filename,$str); //导出   
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
        echo $data;     
    } 
        
        
}

?>