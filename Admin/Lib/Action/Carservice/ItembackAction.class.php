<?php

class ItembackAction extends CommonAction {	
	function __construct() {
		parent::__construct();
        $this->itemback_log = M('tp_xieche.itemback_log','xc_');  //配件回收表
        $this->technician  = M('tp_xieche.technician','xc_');  //技师表
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
        
        $this->reservation_order = D('reservation_order');  //预约订单表
	}
    
    //显示回收配件
    function index() {
        if(!empty($_REQUEST['end_time'])&&!empty($_REQUEST['start_time'])){
            $start = strtotime($_REQUEST['start_time']);
            $end = strtotime($_REQUEST['end_time'].'23:59:59'); 
            $map['create_time'] = array(array('lt', $end),array('gt',$start),"AND");
        }else{  //默认
            $start = strtotime(date('Y-m-d 00:00:00'));
            $end = strtotime(date('Y-m-d 23:59:59')); 
            $map['create_time'] = array(array('lt', $end),array('gt',$start),"AND");
        } 

        $rs = $this->itemback_log->where($map)->select();
        //获取配件详情并返回二维数组
        $rs = $this->get_item_detail($rs);  
        $this->assign('rs',$rs);
        $this->assign('start_time',$_REQUEST['start_time']);
        $this->assign('end_time',$_REQUEST['end_time']);
        //print_r($rs);
        $this->display();
        
    }
    
    //回收数据导出
    function itemback_export() {
        //echo '111' ;
        if(!empty($_REQUEST['end_time'])&&!empty($_REQUEST['start_time'])){
            $start = strtotime($_REQUEST['start_time']);
            $end = strtotime($_REQUEST['end_time'].'23:59:59'); 
            $map['create_time'] = array(array('lt', $end),array('gt',$start),"AND");
		}
        $rs = $this->itemback_log->where($map)->select();
        //echo   $this->itemback_log->getLastSql();exit ;
        //获取配件详情并返回二维数组
        $rs = $this->get_item_detail($rs);
        
        
        //组织csv数据
        $str = "订单id,发生时间,技师,机油,机滤,空滤,空调滤\n";  
        foreach ($rs as $k => $v) {
            //字段值
			$id = $v['order_id'] ;  
            $create_time = date('Y-m-d H:i:s',$v['create_time']);  
			$truename = $v['truename']; //中文转码   
			$oil = $v['oil']; //中文转码
			$filter = $v['filter']; //中文转码  
			$kongqi = $v['kongqi']; //中文转码 
			$kongtiao = $v['kongtiao']; //中文转码  
            
            $str .= $id.",".$create_time.",".$truename.",".$oil.",".$filter.",".$kongqi.",".$kongtiao."\n"; //用英文逗号分开 	
        }
        
        $filename = $_REQUEST['start_time'].'-'.'回收配件'.'.csv'; //设置文件名   
    
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
        //print(chr(0xEF).chr(0xBB).chr(0xBF));//设置utf-8 + bom     
        echo $data;   
    }  
    
    
    function get_item_detail($rs) {
        foreach($rs as $k=>$v){
            //获取订单的创建时间
            $order_info = $this->reservation_order->where('id='.$v['order_id'])->find();
             //判断实例化新配件表品牌车型表还是老配件表品牌车型表
            //$this->getNeedModel($order_info['create_time']);
            
            //技师姓名
            $map['id'] = $v['technician_id'] ;
            $info = $this->technician->where($map)->find();
            $rs[$k]['truename'] = $info['truename'] ;
            
            //配件数据
			$order_items = unserialize($v['item']);
			$oil_data = $order_items['oil_detail'];
			$oil = '';
			foreach($oil_data as $_id=>$num){
				if($num>0){
					$info = $this->item_oil_model->where(array('id'=>$_id))->find();
					$oil = $oil.$info['name'].' '.$info['norms']."L : ".$num."件;";
				}
			}
            $rs[$k]['oil'] = $oil ;
			
			foreach($order_items as $key=>$value){
				if($key=='filter_id'||$key=='kongqi_id'||$key=='kongtiao_id')
				{
					$keyArr = explode('_',$key);
					$name = $keyArr[0] ;
					$info = $this->item_model->where(array('id'=>$value))->find();
					$rs[$k][$name] = $info['name'];
				}
			}
        }
        
        return $rs  ;   
    }
    
    
     //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150820
    public function getNeedModel($orderCreateTime) {
        $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
        if($orderCreateTime <= $new_filter_time){
            $this->item_oil_model = D('item_oil');  //保养机油
            $this->item_model = D('item_filter');  //保养项目
        }else{
            $this->item_oil_model = D('new_item_oil');  //保养机油
            $this->item_model = D('new_item_filter');  //保养项目
        }
        
    }
   
}
