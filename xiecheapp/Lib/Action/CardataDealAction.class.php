<?php
//订单
class CardataDealAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
        $this->old_filter = D('item_filter');//旧机滤表
        $this->old_carmodel = D('carmodel');//旧车型库
        
        $this->new_filter = D('new_item_filter');//新机滤表
         $this->new_filter_bak = D('new_item_filter_bak');//新机滤表
         
        $this->new_oil = D('new_item_oil');//新机油表
        $this->new_carmodel = D('new_carmodel');//新车型库
        $this->new_carseries = D('new_carseries');//新车型库
        $this->new_carbrand  = D('new_carbrand');//新品牌表
        
        
        $this->liyang_filter = D('liyang_filter');//车型库与三滤搭配表
        
        $this->liyang_carfit = D('liyang_carfit');//车型库与配件搭配表 。。
        
	}
	
	public function  index(){	
            //echo 'CardataDeal.php';
            //echo  C('SITE_ROOT'); 
            $this->display();
		
	}
        
        //附件上传
        public function uploadcsv() {
             //防止内存超过限制。
            set_time_limit(0);
            //setlocale(LC_ALL,'null');
            
            if ($_FILES['csv']['name']){
                import("ORG.Net.UploadFile");
                $upload = new UploadFile();
                $upload = $this->_upload_init($upload);
                if (!$upload->upload()) {
                        $this->error($upload->getErrorMsg());
                } else {
                        $uploadList = $upload->getUploadFileInfo();
                }
                //处理csv ,插入数据库。
                $file = fopen($uploadList[0]['savepath'].$uploadList[0]['savename'],'r');
                while ($data = fgetcsv($file)) { 
                    $cardata[] = $data ;
                }
                
                unset($cardata[0]);
                
                //print_r($cardata) ;
                foreach ($cardata as $k => $v) {
                    $data = array();
                    $map = array() ;
                    
                    $map['model_id'] =  $v[0] ;
                    $data['oil_mass'] = $v[1] ;
                    $data['oil_type'] = $v[2] ;
                    //更新车型表
                     $this->new_carmodel->where($map)->save($data);
                    echo   $this->new_carmodel->getLastSql() ;
                    echo '<br>' ;
                }
                
                echo  '更新完成，请核对！' ;

               

                exit ;
                
                //需要重新生成微信端json数据 。。
                
                //echo  count($newfitArr);
                //print_r($newfitArr);exit ;
                
//                foreach ($newfitArr as $k => $v) {
//                    $data = array();
//                    if(strpos($v,'/')){
//                        $expArray = explode('/',$v);
//                        $data['name'] = $v ;
//                        $data['inter_type'] = str_replace(array('神翼','法雷奥','电装'), '', $expArray[0]) ;
//                        $data['size1'] = $expArray[1] ;
//                        $data['size2'] = $expArray[2] ;
//                    }elseif(strpos($v,'@')&&strstr($v,'海拉主雨刮')){
//                        $expArray = explode('@',$v);
//                        $data['name'] = str_replace(array('海拉主雨刮'), array('海拉'), $expArray[0]) ;
//                        $data['size1'] = $expArray[1];
//                        $data['inter_type'] = $expArray[2];
//                    }elseif(strpos($v,'@')&&strstr($v,'海拉副雨刮')){
//                        $expArray = explode('@',$v);
//                        $data['name'] = str_replace(array('海拉副雨刮'), array('海拉'), $expArray[0]) ;
//                        $data['size2'] = $expArray[1];
//                        $data['inter_type'] = $expArray[2];
//                    }
//                    
//                    $data['type_id'] = 14;
//                    //print_r($data) ;
//                    //echo '<br>' ;
//                    $this->new_filter->add($data);
//                }


                $this->success('导入数据完成', U('/CardataDeal-index'));
            }else{
                $this->error('请选择您要导入的CSV文件！');
            }
        }
        
        
        public function _upload_init($upload) {
            
            
            //设置上传文件大小
            $upload->maxSize = C('UPLOAD_MAX_SIZE');
            //设置上传文件类型
            $upload->allowExts = explode(',', 'csv');
            //设置附件上传目录
            //$upload->savePath = C('UPLOAD_ROOT') . '/Bidsource/';
            $upload->savePath =  'UPLOADS/cardata/';
            $upload->saveRule = 'uniqid';

            $upload->uploadReplace = false;
            //$this->watermark = 1;水印
            return $upload;
	}
        
        
        public function getBrand() {
            //去重查询
            $rs = $this->model_cardata->field('id,brand2,is_show')->group('brand2,is_show')->select();
            foreach($rs as $k=>$v){
                $data['word'] = $this->chineseFirst($v['brand2']); 
                $data['brand_name'] = $v['brand2'] ;
                $data['isShow'] = $v['is_show'] ;
                $brand_id = $this->model_carbrand->add($data) ;
                //将插入的品牌id插入临时车型数据表,用于关联车系。
                $map['brand2'] = $v['brand2'] ;
                $map['is_show'] = $v['is_show'] ;
                $save_data['brand_id'] = $brand_id ; 
                $this->model_cardata->where($map)->save($save_data);
                
            }
            //print_r($rs) ;    
        }
        
        //获取车系
        public function getSeries() {
            //去重查询.按照车系，公司两个字段去重
            $rs = $this->model_cardata->field('brand2,serie2,brand_id')->group('serie2,brand2,is_show')->select();
            foreach($rs as $k=>$v){
                $data['brand_id'] = $v['brand_id'] ;
                $data['series_name'] = $v['serie2'] ;
                $this->model_carseries->add($data) ; 
            }
            //print_r($rs);
        }
        
        
        
//获取车型
public function getModel() {
     //全部查询
    $rs = $this->model_cardata->select();
    //echo   $this->model_cardata->getLastSql();
    //exit ;
    //print_r();
    foreach ($rs as $k => $v) {
        $map['series_name'] = $v['serie2'] ;
        $map['brand_id'] = $v['brand_id'] ;
        $info = $this->model_carseries->where($map)->find();
        
        $data['series_id'] = $info['series_id'] ;
        
        $data['model_name'] = $v['model_last'] ;
        //$data['output'] = $v['output'] ;
        //$data['gearbox'] = $v['gearbox'] ;
        //$data['car_style'] = $v['car_style'] ;
        $data['liyang_id'] = $v['liyang_id'] ;
        $data['old_modelid'] = $v['model_id'] ;
        
        $this->model_carmodel->add($data) ;
        
    }
    //print_r($rs) ;
}



//更新品牌logo


public function  UpdateModel() {
    $carmodel = $this->model_carmodel->select() ;
    foreach($carmodel as $k=>$v){
       if(strpos($v['model_name'], '<br>')){
           $modelArr = explode('<br>', $v['model_name']) ;
           $data['model_name'] = $modelArr[0].' '.'<br>'.$modelArr[1] ;
           $map['model_id'] = $v['model_id'] ;
           $this->model_carmodel->where($map)->save($data);
       }    
    }   
}



    //从旧机滤表更新价格
    public function updateFilter() {
        $filterData = $this->old_filter->select();
        //print_r($filterData);
        foreach($filterData as $k=>$v){
            $map['name'] = array('like','%'.$v['name'].'%') ;
            $rs = $this->new_filter->where($map)->find();
            if(is_array($rs)){
                $data['price'] = $v['price'] ;
                $data['code'] = $v['code'] ;
                $this->new_filter->where($map)->save($data);
            }
            
        }   
    }
    
    
    //更新item_set2
    public function getItemset() {
        //防止内存超过限制。
        set_time_limit(0);
        
        $liyangData = $this->liyang_carfit->select();
        foreach ($liyangData as $k => $v) {
            $item_set2 = array();
            $data = array();
            $map = array();
            
            $retArr_0 = $this->get_fit_arr($v['front_disc'],6);  //前制动盘
            $retArr_1 = $this->get_fit_arr($v['end_disc'],7);   //后制动盘
            
            $retArr_2 = $this->get_fit_arr($v['front_catch'],8); //前制动片
            $retArr_3 = $this->get_fit_arr($v['end_catch'],9);   //后制动片
            
            $retArr_4 = $this->get_fit_arr($v['brakedrum'],10);  //后制动鼓
            $retArr_5 = $this->get_fit_arr($v['trighoof'],11);   //后制动蹄
            
            $retArr_6 = $this->get_fit_arr($v['spark'],12);   //火花塞
            $retArr_7 = $this->get_fit_arr($v['battery'],13); //蓄电池
            $retArr_8 = $this->get_fit_arr($v['wiper'],14);   //雨刮片
           
            
            //$retArr_2['key'] = $k ;
            //print_r($retArr_2);
            $item_set2 = array(
               '0'=> $retArr_0,
               '1'=> $retArr_1 ,
               '2'=> $retArr_2 ,
               '3'=> $retArr_3 ,
               '4'=> $retArr_4,
               '5'=> $retArr_5 ,
               '6'=> $retArr_6 ,
               '7'=> $retArr_7 ,
               '8'=> $retArr_8 
            );
            
            //print_r($item_set2) ;exit ;
            
            $data['item_set2'] = serialize($item_set2) ;
            $map['liyang_id'] = $v['liyang_id'] ;
            $this->new_carmodel->where($map)->save($data);
            
           
            //print_r($item_set); 
        }
        
         echo  '更新完成，请核对！' ;
          
    }
    

    public function get_fit_arr($fit_str,$type_id) {
        
        $fitArr = array(); 
        $map = array();
        $cond = array();
        
        if($fit_str == 'NULL'){
            $fitArr = array(); 
        }elseif(strpos($fit_str, '#')) {
           $fit_array = explode('#', $fit_str) ;
           foreach ($fit_array as $key => $value) {
               $map['name'] = $value ;
               $map['type_id'] = $type_id ;
               $rs = $this->new_filter->where($map)->find();
               if($rs == NULL){
                   echo  '1';
                   echo '<br>';
                   echo  $this->new_filter->getLastSql();
                   echo '<br>';
               }
               $fitArr[] = $rs['id'] ;
           }
        }elseif($fit_str != 'NULL' && !strpos($fit_str, '#')) {
            $cond['name'] = $fit_str ;
            $cond['type_id'] = $type_id ;
            $rs = $this->new_filter->where($cond)->find();
            if($rs == NULL){
                   echo '2';
                   echo '<br>';
                   echo  $this->new_filter->getLastSql();
                   echo '<br>';
            }
            $fitArr[] = $rs['id'] ;
        }
        
        return  $fitArr ;     
    }
                

    public function updateSerisWord() {
        $carseries = $this->new_carseries->select() ;
        
        foreach ($carseries as $k => $v) {
            $data = array();
            $map = array();
            $data['word'] = $this->chineseFirst($v['series_name']);
            $map['series_id'] = $v['series_id'] ;
            $this->new_carseries->where($map)->save($data);  
            //echo  $this->new_carseries->getLastSql();
            
        }
           
    }
    
    
    //更新is_show字段
    public function   updateIsShow() {
        $carmodel = $this->new_carmodel->select() ;
        foreach ($carmodel as $k => $v) {
            $itemArr = array();
            $filterArr = array(); 
            $map = array();
            $data = array(); 
            
            //print_r($filterArr);
            if($v['oil_mass']=='' && $v['oil_type']==''){
                $map['model_id'] = $v['model_id'] ;
                $data['is_show'] = 2 ;
                $this->new_carmodel->where($map)->save($data); 
                //echo $this->new_carmodel->getLastSql();
            }else{
                $map['model_id'] = $v['model_id'] ;
                $data['is_show'] = 1 ;
                $this->new_carmodel->where($map)->save($data);
                //echo  $this->new_carmodel->getLastSql();
            }
            
        }
        
        
    }
    
    
    //更新替换变速器
    public function  updateGearBox1() {
        $carmodel =  $this->new_carmodel->field('model_id,model_name')->select();
        //print_r($carmodel);
        $findArr = array(
          'AMT',
          'CVT',
          'DCG',
          'DCT',
          'DSG',
          'DSS',
          'EMT',
          'EVT',
          'GTD',
          'IMT',
          'ISR',
          'LDF',
          'PDK',
          'RMT',
          'SMG',
          'SSG',
          'TST');
        
        //循环替换
        foreach ($carmodel as $k => $v) {
            $data = array();
            $map = array();
            $data['model_name'] = str_replace($findArr,'AT',$v['model_name']);
            $map['model_id'] = $v['model_id']  ;
            $this->new_carmodel->where($map)->save($data);
            
        }
         
        
    }
    
    
    
    //获取热门品牌数据,用于微信端首页选取车型
    public function  getHotBrand() {
        $hotData = array(
            array(
               'brand_id'=>'87,88',
               'brand_name' => '奥迪'
            ),
            array(
               'brand_id'=>'93,94',
               'brand_name' => '宝马'
            ),
            array(
               'brand_id'=>'116,117,118',
               'brand_name' => '本田'
            ),
            array(
               'brand_id'=>'46',
               'brand_name' => '别克'
            ),
            array(
               'brand_id'=>'73,74,75',
               'brand_name' => '大众'
            ),
            array(
               'brand_id'=>'25,26,27',
               'brand_name' => '丰田'
            ),
            array(
               'brand_id'=>'149,150',
               'brand_name' => '福特'
            ),
            array(
               'brand_id'=>'170',
               'brand_name' => '起亚'
            ),
            array(
               'brand_id'=>'18,113,114',
               'brand_name' => '日产'
            ),
            array(
               'brand_id'=>'49,144',
               'brand_name' => '现代'
            )
          
        );
        
        $comArr = array(
            '87'=>'(一汽奥迪)',
            '88'=>'(进口奥迪)',
            '93'=>'(华晨宝马)',
            '94'=>'(进口宝马)',
            '116'=>'(东风本田)',
            '117'=>'(广汽本田)',
            '118'=>'(进口本田)',
            '46'=>'(别克)',
             '73'=>'(一汽大众)',
            '74'=>'(上海大众)',
            '75'=>'(进口大众)',
             '25'=>'(一汽丰田)',
            '26'=>'(广汽丰田)',
            '27'=>'(进口丰田)',
            '149'=>'(长安福特)',
            '150'=>'(进口福特)',
             '169'=>'(东风起亚)',
            '170'=>'(进口起亚)',
            '18'=>'(东风日产)',
             '113'=>'(郑州日产)',
            '114'=>'(进口日产)',
             '49'=>'(北京现代)',
            '144'=>'(进口现代)' 
        );
        
        foreach($hotData as $k=>$v){
            $con = array() ;
            $con['brand_id'] =array('in',$v['brand_id']);
            //取可显示车系
            $con['is_show'] = 1 ;
            
            $hotData[$k]['child'] = $this->new_carseries->field('brand_id,series_id,series_name')->where($con)->select() ; 
            foreach($hotData[$k]['child'] as $key => $value){
                //车系名称处理
                $hotData[$k]['child'][$key]['series_name'] .= $comArr[$value['brand_id']];
                $map = array() ;
                $map['series_id'] = $value['series_id'] ;
                //取可显示车型
                $map['is_show'] = 1 ;
                
                $hotData[$k]['child'][$key]['child'] = $this->new_carmodel->field('model_id,model_name')->where($map)->select() ; 
                
                //对车型数据稍作处理
                if($hotData[$k]['child'][$key]['child']==null){
                  //不作处理
                }else{
                    foreach($hotData[$k]['child'][$key]['child'] as $k2=>$v2){
                        //br处理
                        if(strstr($v2['model_name'], '<br>')){
                           $hotData[$k]['child'][$key]['child'][$k2]['model_name'] = str_replace('<br>', ' ', $v2['model_name']); 
                        }  
                    }   
                }    
            }
        }
        
        
        //第一级brand_id变为-1
        foreach ($hotData as $key => $value) {
            $hotData[$key]['brand_id'] = "-1" ;
        }
        
        echo  json_encode($hotData,JSON_UNESCAPED_UNICODE);  
        
        //print_r($hotData) ; 
    }
    
    
    
    //年款日期替换到后面。。
//    public function updateModelname(){
//        $carmodel =  $this->new_carmodel->select();
//        foreach($carmodel as $k=>$v){
//            $modelArr = array();
//            $modelNameArr = array();
//            $data1 = array();
//            $data2 = array();
//            $map = array() ;
//            $con = array() ;
//            
//            if($v['model_id']<=4679){
//                if(strpos($v['model_name'], '<br>')){
//                    $modelArr = explode('<br>', $v['model_name']) ;  
//                    $modelNameArr =  explode(' ',$modelArr[1]);
//                    $data1['model_name'] = $modelArr[0].'<br>'.$modelNameArr[1].' '.$modelNameArr[2].' '.$modelNameArr[0].' '.$modelNameArr[3] ;
//                    $map['model_id'] =  $v['model_id'] ;
//                    
//                    $this->new_carmodel->where($map)->save($data1) ;
//
//                }else{
//                    $modelNameArr =  explode(' ',$v['model_name']);
//                    $data2['model_name'] = $modelNameArr[1].' '.$modelNameArr[2].' '.$modelNameArr[0].' '.$modelNameArr[3] ;
//                    $con['model_id'] =  $v['model_id'] ;
//                    
//                    $this->new_carmodel->where($con)->save($data2) ;
//                }
//
//            }
//            
//            
//        }
//    }
    
    
    //删除多余的两厢三厢之类
//    public function  update_model() {
//        $carmodel =  $this->new_carmodel->select();
//        foreach($carmodel as $k=>$v){
//            $data = array();
//            $map = array() ;
//            if(strpos($v['model_name'], '<br>')){
//                if(substr_count($v['model_name'],'厢')==2){
//                    $data['model_name'] = substr($v['model_name'],0,-7) ;
//                     //echo  '<br>' ;
//                    $map['model_id'] = $v['model_id'] ;
//                    $this->new_carmodel->where($map)->save($data) ;    
//                }
//            }       
//        }    
//    }
    
//    //获取热门品牌数据,用于微信端首页选取车型
//    public function  getHotBrand() {
//        $hotData = array(
//            array(
//               'brand_id'=>'87,88',
//               'brand_name' => '奥迪'
//            ),
//            array(
//               'brand_id'=>'93,94',
//               'brand_name' => '宝马'
//            ),
//            array(
//               'brand_id'=>'116,117,118',
//               'brand_name' => '本田'
//            ),
//            array(
//               'brand_id'=>'46',
//               'brand_name' => '别克'
//            ),
//            array(
//               'brand_id'=>'73,74,75',
//               'brand_name' => '大众'
//            ),
//            array(
//               'brand_id'=>'25,26,27',
//               'brand_name' => '丰田'
//            ),
//            array(
//               'brand_id'=>'149,150',
//               'brand_name' => '福特'
//            ),
//            array(
//               'brand_id'=>'170',
//               'brand_name' => '起亚'
//            ),
//            array(
//               'brand_id'=>'18,113,114',
//               'brand_name' => '日产'
//            ),
//            array(
//               'brand_id'=>'49,144',
//               'brand_name' => '现代'
//            )
//          
//        );
//        
//        $comArr = array(
//            '87'=>'(一汽奥迪)',
//            '88'=>'(进口奥迪)',
//            '93'=>'(华晨宝马)',
//            '94'=>'(进口宝马)',
//            '116'=>'(东风本田)',
//            '117'=>'(广汽本田)',
//            '118'=>'(进口本田)',
//            '46'=>'(别克)',
//             '73'=>'(一汽大众)',
//            '74'=>'(上海大众)',
//            '75'=>'(进口大众)',
//             '25'=>'(一汽丰田)',
//            '26'=>'(广汽丰田)',
//            '27'=>'(进口丰田)',
//            '149'=>'(长安福特)',
//            '150'=>'(进口福特)',
//             '169'=>'(东风起亚)',
//            '170'=>'(进口起亚)',
//            '18'=>'(东风日产)',
//             '113'=>'(郑州日产)',
//            '114'=>'(进口日产)',
//             '49'=>'(北京现代)',
//            '144'=>'(进口现代)' 
//        );
//        
//        foreach($hotData as $k=>$v){
//            $con = array() ;
//            $con['brand_id'] =array('in',$v['brand_id']);
//            $hotData[$k]['child'] = $this->new_carseries->field('brand_id,series_id,series_name')->where($con)->select() ; 
//            foreach($hotData[$k]['child'] as $key => $value){
//                //车系名称处理
//                $hotData[$k]['child'][$key]['series_name'] .= $comArr[$value['brand_id']];
//                $map = array() ;
//                $map['series_id'] = $value['series_id'] ;
//                $map['is_show'] = 1 ;
//                $map['oil_mass'] = array('neq','') ;
//                $map['oil_type'] = array('neq','') ;
//                $hotData[$k]['child'][$key]['child'] = $this->new_carmodel->field('model_id,model_name,item_set')->where($map)->select() ; 
//                
//                //对车型数据稍作处理
//                if($hotData[$k]['child'][$key]['child']==null){
//                    unset($hotData[$k]['child'][$key]);
//                }else{
//                    foreach($hotData[$k]['child'][$key]['child'] as $k2=>$v2){
//                        //br处理
//                        if(strstr($v2['model_name'], '<br>')){
//                           $hotData[$k]['child'][$key]['child'][$k2]['model_name'] = str_replace('<br>', ' ', $v2['model_name']); 
//                        }
//                        //判断机滤价格是否全为零。全为零，删除车型。wql@20150825
//                        $item_set = array();
//                        $filterArr = array();
//                        $filterPrice = array();
//
//                        $item_set = unserialize($v2['item_set']) ;
//                        $filterArr = $item_set[1] ;
//                        foreach ($filterArr as $k3 => $v3) {
//                           $filter_info = $this->new_filter->where('id='.$v3)->find();
//                           $filterPrice[] = $filter_info['price'] ;
//                        }
//                        $filter_sum = array_sum($filterPrice);
//                        if($filter_sum == 0){
//                            unset($hotData[$k]['child'][$key]['child'][$k2]);
//                        }
//
//                        //删除临时需要用得item_set
//                        if($hotData[$k]['child'][$key]['child'][$k2]){
//                            unset($hotData[$k]['child'][$key]['child'][$k2]['item_set']);
//                        }
//
//                        //判断车型数组是否为空,为空的话，将车系删除
//                        if(count($hotData[$k]['child'][$key]['child'])==0){
//                           unset($hotData[$k]['child'][$key]);
//                        }    
//                    }   
//                }
//                
//            }
//   
//        }
//        
//        
//        //第一级brand_id变为-1
//        foreach ($hotData as $key => $value) {
//            $hotData[$key]['brand_id'] = "-1" ;
//        }
//        
//        echo  json_encode($hotData,JSON_UNESCAPED_UNICODE);  
//        
//        //print_r($hotData) ; 
//    }
    
    
    
    //查询更新品牌，车系，车型是否需要显示。。此函数有问题，请勿再用。。
//    function  updateShowStatus() {
//        $brand = $this->new_carbrand->where('is_show=1')->select();
//        foreach ($brand as $k => $v) {
//            $con = array();
//            $con['brand_id'] = $v['brand_id'];
//            $brand[$k]['child'] = $this->new_carseries->where($con)->select() ;
//            foreach($brand[$k]['child'] as $key=>$value){
//                $map = array() ;
//                $map['series_id'] = $value['series_id'] ;
//                $map['is_show'] = 1 ;
//                
//                $carmodel = $this->new_carmodel->where($map)->select() ; 
//                if($carmodel){
//                    $brand[$k]['child'][$key]['child'] =  $carmodel ;
//                }else{
//                    $brand[$k]['child'][$key]['child'] =  array();
//                }
//                
//                //更新操作
//                if(count($brand[$k]['child'][$key]['child']) == 0){     
//                }else{
//                    foreach ($brand[$k]['child'][$key]['child'] as $k1 => $v1) {
//                        //判断机滤价格是否全为零。全为零，删除车型。wql@20150825
//                        $item_set = array();
//                        $filterArr = array();
//                        $filterPrice = array();
//
//                        $item_set = unserialize($v1['item_set']) ;
//                        $filterArr = $item_set[1] ;
//                        foreach ($filterArr as $k2 => $v2) {
//                           $filter_info = $this->new_filter->where('id='.$v2)->find();
//                           $filterPrice[] = $filter_info['price'] ;
//                        }
//                        $filter_sum = array_sum($filterPrice);
//                        if($filter_sum == 0){
//                            echo '机滤和为0';
//                            echo '<br>' ;
//                            //更新车型库状态为3 ，即所有机滤价格为0，不显示
//                            $data1 = array();
//                            $data1['is_show'] = 3 ;
//                            $this->new_carmodel->where('model_id='.$v1['model_id'])->save($data1);
//                            unset($brand[$k]['child'][$key]['child'][$k1]);
//                        }    
//                    }    
//                }
//                
//                //车系下车型库为空，更新车系不显示
//                if(count($brand[$k]['child'][$key]['child'])==0){
//                    $data2 = array();
//                    $data2['is_show'] = 0 ;
//                    $this->new_carseries->where('series_id='.$value['series_id'])->save($data2);
//                    unset($brand[$k]['child'][$key]);   
//                }
//                
//            }
//            
//            //如果品牌对应的车系为空 ，删除品牌
//            if(count($brand[$k]['child'])==0){
//                $data3 = array();
//                $data3['is_show'] = 0 ;
//                $this->new_carbrand->where('brand_id='.$v['brand_id'])->save($data3); 
//            }
//            
//        }
//        
//        //print_r($brand);
//    }
    
    
    
    //每更改一批机滤的价格，执行此函数，更新车型，车系，品牌的显示状态 。
    public function  changePriceQuery() {
        set_time_limit(0) ; 
         
        $con['is_show'] = 3 ;
        $carmodel = $this->new_carmodel->where($con)->select() ;
        
        $seriesArr = array();
        foreach($carmodel as $key => $val){
            //判断机滤价格是否全为零。不为零，更新状态。wql@20150831
            $item_set = array();
            $filterArr = array();
            $filterPrice = array();

            $item_set = unserialize($val['item_set']) ;
            $filterArr = $item_set[1] ;
            foreach ($filterArr as $k => $v) {
               $filter_info = $this->new_filter->where('id='.$v)->find();
               $filterPrice[] = $filter_info['price'] ;
            }
            $filter_sum = array_sum($filterPrice);
            
            if($filter_sum > 0){
                //更新车型的状态为1 ,显示。。
                $map = array();
                $data = array();
                $map['model_id'] = $val['model_id'] ;
                $data['is_show'] =  1 ;
                $this->new_carmodel->where($map)->save($data) ;
                $seriesArr[]  =  $val['series_id'] ;
                //调试
                echo  $val['model_id']  ;
                echo  '<br>' ;
            }
        }
        
        //车系id去重 。
        $seriesArr = array_unique($seriesArr);
        //调试
        print_r($seriesArr);
        echo  '<br>' ;
        
        $brandArr = array();
        foreach($seriesArr as $k=>$v){
            //更新车系为1 ，显示 。
            $data1['is_show'] = 1 ;
            $this->new_carseries->where('series_id = '.$v)->save($data1); 
            $carseries_info =  $this->new_carseries->where('series_id = '.$v)->find(); 
            $brandArr[] = $carseries_info['brand_id'] ;
        }
        
        //品牌去重更新 。
        $brandArr = array_unique($brandArr);
        //调试 
        print_r($brandArr);
        echo  '<br>' ;
        
        foreach($brandArr as $k=>$v){
           //更新品牌为1 ，显示 。
           $data2['is_show'] = 1 ;
           $this->new_carbrand->where('brand_id='.$v)->save($data2); 
        }
        
        echo  '更新成功，请核对！';
           
    }
    

               
    //获取中文字符串首字母函数
    function chineseFirst($str){
        $str= iconv("UTF-8","gb2312", $str);    //如果程序是gbk的，此行就要注释掉

        //判断字符串是否全都是中文
        if (preg_match("/^[\x7f-\xff]/", $str))
        {
            $fchar=ord($str{0});  
            if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0});
            $a = $str;
            $val=ord($a{0})*256+ord($a{1})-65536;
            if($val>=-20319 and $val<=-20284)return "A";  
            if($val>=-20283 and $val<=-19776)return "B";  
            if($val>=-19775 and $val<=-19219)return "C";  
            if($val>=-19218 and $val<=-18711)return "D";  
            if($val>=-18710 and $val<=-18527)return "E";  
            if($val>=-18526 and $val<=-18240)return "F";  
            if($val>=-18239 and $val<=-17923)return "G";  
            if($val>=-17922 and $val<=-17418)return "H";
            if($val>=-17417 and $val<=-16475)return "J";                
            if($val>=-16474 and $val<=-16213)return "K";                
            if($val>=-16212 and $val<=-15641)return "L";                
            if($val>=-15640 and $val<=-15166)return "M";                
            if($val>=-15165 and $val<=-14923)return "N";                
            if($val>=-14922 and $val<=-14915)return "O";                
            if($val>=-14914 and $val<=-14631)return "P";                
            if($val>=-14630 and $val<=-14150)return "Q";                
            if($val>=-14149 and $val<=-14091)return "R";                
            if($val>=-14090 and $val<=-13319)return "S";                
            if($val>=-13318 and $val<=-12839)return "T";                
            if($val>=-12838 and $val<=-12557)return "W";                
            if($val>=-12556 and $val<=-11848)return "X";                
            if($val>=-11847 and $val<=-11056)return "Y";                
            if($val>=-11055 and $val<=-10247)return "Z";
        }else{
            return strtoupper(substr($str, 0,1));
            //return false;
        }
    }
    
    
    //海拉雨刮片数据重新拼接 ，为了车型和配件的匹配。。
//    public function HellaDeal() {
//        $map['name'] = array('like','%海拉%');
//        $map['type_id'] = 14 ;
//        
//        $rs = $this->new_filter->where($map)->select();
//        //print_r($rs);
//        foreach ($rs as $k => $v) {
//            $data = array();
//            $cond = array() ;
//            
//            if($v['size1']){
//                $data['name'] = str_replace('海拉','海拉主雨刮', $v['name']);
//            }else{
//                $data['name'] = str_replace('海拉','海拉副雨刮', $v['name']);
//            }
//            //echo $data['name'] ;
//           // echo '<br>' ;
//            $cond['id'] = $v['id'] ;
//            
//            $this->new_filter->where($cond)->save($data);
//            echo  $this->new_filter->getLastSql();
//            echo '<br>' ;
//        }
//        
//    }
    
    
    
    //获取没有机油的车型 。。
    public function   get_model_data() {
        $map['model_id'] = array('elt',4679) ;
        $car_model = $this->new_carmodel->where($map)->select();
        
        echo  '<table><tr> <td>数据库id</td> <td>品牌</td> <td>车系</td> <td>车型名称</td>  </tr>' ;
        foreach ($car_model as $k => $v) {
            $con = array();
            $con['series_id'] = $v['series_id'];
            $info_1 = $this->new_carseries->where($con)->find();
            //追加车系
            $v['series_name'] = $info_1['series_name'] ;
            $v['series_show'] = $info_1['is_show'] ;
            //追加品牌
            $info_2 = $this->new_carbrand->where('brand_id='.$info_1['brand_id'])->find();
            $v['brand_name'] = $info_2['brand_name'] ;
            //车型名称
            if(strpos($v[model_name],'<br>')){
               $v[model_name] = str_replace('<br>', '@', $v[model_name]) ;
            }
            
            echo  '<tr> <td> '.$v[model_id].' </td> <td>'.$v[brand_name].'</td> <td>'.$v[series_name].'</td> <td>'.$v[model_name].'</td> </tr>' ;  
        }
        
        echo '</table>' ;
        //print_r($car_model);
    }
    
    
    
    
    //根据旧配件表更新新的配件表
//    public function update_filter() {
//       $old_filter = $this->old_filter->select();
//       
//       //print_r($old_filter) ;exit ;
//       //$upfilter = array();
//       //$addfilter = array();
//       foreach ($old_filter as $k => $v) {
//            $map = array();
//            $cond_1 = array();
//            $data_1 = array();
//            $data_2 = array();
//            
//            //$map['name'] =array('like','%'.$v['name'].'%')  ;
//            $map['name'] = $v['name'] ;
//            
//            $rs = $this->new_filter->where($map)->find();
//            if(is_array($rs)){
//                //echo  $k ;
//                //echo  '<br>' ;
//                //更新新的机滤表
//                $cond_1['name'] = $v['name'] ;
//                $data_1['old_id'] = $v['id'] ;
//                $this->new_filter->where($cond_1)->save($data_1);
//            }elseif ($rs == NULL) {
//                //插入新的机滤表
//                $data_2['old_id'] = $v['id'] ;
//                $data_2['name'] = $v['name'] ;
//                $data_2['type_id'] = $v['type_id'] ;
//                $data_2['price'] = $v['price'] ;
//                $data_2['code'] = $v['code'] ;                
//                $this->new_filter->add($data_2); 
//            }
//               
//        }
//        //print_r($addfilter);
//    }
    
    
     //更新item_set_test  20150925@wql
    public function get_item_set_test() {
        //防止内存超过限制。
        set_time_limit(0);
        
        $liyangData = $this->liyang_filter->select();
        foreach ($liyangData as $k => $v) {
            $item_set_test = array();
            $data = array();
            $map = array();
            
            $retArr_0 = array() ;  
            $retArr_1 = $this->get_filter($v['filter'],2);   //机滤
            $retArr_2 = $this->get_filter($v['kongqi_filter'],3); //空气滤
            $retArr_3 = $this->get_filter($v['kongtiao_filter'],4);   //空调滤

            //$retArr_2['key'] = $k ;
            //print_r($retArr_2);
            $item_set_test = array(
               '0'=> $retArr_0,
               '1'=> $retArr_1 ,
               '2'=> $retArr_2 ,
               '3'=> $retArr_3 
            );
            
            //print_r($item_set2) ;exit ;
            
            $data['item_set'] = serialize($item_set_test) ;
            $map['liyang_id'] = $v['liyang_id'] ;
            $this->new_carmodel->where($map)->save($data);
            
            //print_r($item_set); 
        }
        
         echo  '更新完成，请核对！' ;
          
    }
    
    
    public function get_filter($fit_str,$type_id) {
        
        $fitArr = array(); 
        $map = array();
        $cond = array();
        
        if($fit_str == ''){
            $fitArr = array(); 
        }elseif(strpos($fit_str, '@')) {
           $fit_array = explode('@', $fit_str) ;
           foreach ($fit_array as $key => $value) {
               $map['name'] = $value ;
               $map['type_id'] = $type_id ;
               $rs = $this->new_filter->where($map)->find();
               if($rs == NULL){
                   echo  '1';
                   echo '<br>';
                   echo  $this->new_filter->getLastSql();
                   echo '<br>';
               }
               $fitArr[] = $rs['id'] ;
           }
        }elseif($fit_str != '' && !strpos($fit_str, '@')) {
            $cond['name'] = $fit_str ;
            $cond['type_id'] = $type_id ;
            $rs = $this->new_filter->where($cond)->find();
            if($rs == NULL){
                   echo '2';
                   echo '<br>';
                   echo  $this->new_filter->getLastSql();
                   echo '<br>';
            }
            $fitArr[] = $rs['id'] ;
        }
        
        return  $fitArr ;     
    }
    
    
      //新旧车型库配件对比更新
    public function update_model_filter() {
        set_time_limit(0) ; 
        
        //查询old_modelid 不为空的数据
        $map['old_modelid'] = array('neq',0);
        $map['model_id'] = array('elt',4679);
        
        $carmodel = $this->new_carmodel->where($map)->select() ;
        foreach ($carmodel as $k => $v) {
            $con  = array() ;
            $retArr_1 = array();
            $retArr_2 = array();
            $retArr_3 =array();
            $save_item_set = array();
            $data = array();
            $condition = array() ;
            
            //新的配件绑定关系
            $new_item = unserialize($v['item_set']);
            //获取旧的绑定关系
            $con['model_id'] = $v['old_modelid'] ;
            $rs = $this->old_carmodel->where($con)->find();
            $old_item = unserialize($rs['item_set']);
            //获取新机滤数组
            $retArr_1 =  $this->get_new_filter_arr($new_item[1],$old_item[1]);
            $retArr_2 =  $this->get_new_filter_arr($new_item[2],$old_item[2]);
            $retArr_3 =  $this->get_new_filter_arr($new_item[3],$old_item[3]);
            $save_item_set = array($new_item[0],$retArr_1,$retArr_2,$retArr_3);
            
            //更新数据
            $data['item_set'] = serialize($save_item_set) ;
            
            if(strstr($data['item_set'] , 'N')){
                 echo  $v['old_modelid'] ;
                 echo '<br>' ;
                 print_r( $data['item_set']);
                 echo '<br>' ;
            }
            //print_r($save_item_set) ;

            
            $condition['model_id']  =  $v['model_id']  ;
            $this->new_carmodel->where($condition)->save($data) ;

        }
        
       
        
        echo  '更新完成' ;
    }
    
    
    //旧配件id数组转为新配件id数组 ,并且返回合并后的配件数组
    public function get_new_filter_arr($newitem_arr ,$olditem_arr) {
        
        if(is_array($newitem_arr) && $olditem_arr == ''){
            $retArray  =   $newitem_arr ; 
        }elseif(is_array($newitem_arr) && is_array($olditem_arr)){
            //转为新配件id数组
            $switch_arr = array();
            if(count($olditem_arr)==0){
                $switch_arr =  $olditem_arr ;
            }else{ 
                foreach ($olditem_arr as $k => $v) {
                    $rs = array();
                    $rs = $this->new_filter->where('old_id='.$v)->find();
                    $switch_arr[] = $rs['id'] ;
                } 
            }
            //合并处理。
            $lastArray =  array_merge($newitem_arr ,$switch_arr); 
            //去重处理并重建索引
            $retArray  =  array_values(array_unique($lastArray)) ;    
        }
        
        
        //检测数组中是否有空元素，有则删掉
        foreach ($retArray as $k => $v) {
            if($v == ''){
                unset($retArray[$k]);
            }    
        }
        $retArray  =  array_values($retArray) ; 

        return   $retArray ;
    }
    
    
    //去重旧机滤表
//    public function get_old_filter() {
//        
//        set_time_limit(0) ;
//       
//        $old_filter = $this->old_filter->field('count(name) as nums,id,name')->group('name')->select();
//        //print_r($old_filter);
//        //exit ;
//        $filter_arr = array();
//        foreach ($old_filter as $k=>$v) {
//            if($v['nums']>1){
//                $filter_arr[] = $old_filter[$k] ;
//            }
//        }
//        //print_r($filter_arr);
//        //根据名称去查询重复的id
//        $filterIdArray = array();
//        
//        foreach ($filter_arr as $k => $v) {
//            $rs = array();
//            $results = array();
//            
//            $rs = $this->old_filter->field('id')->where("name='".$v['name']."'")->select();
//            foreach ($rs as $key => $value) {
//                $results[] = $value['id'] ;
//            }
//            $filterIdArray[] =  $results ;
//            
//        }
//        
//        //再次循环，变成需要的形式。
//        $lastArr = array();
//        foreach ($filterIdArray as $k => $v) {
//            //$index = array_pop($v);  
//            //$replace = implode(',', $v);
//            $index = array_shift($v) ;
//            $lastArr[$index] = $v ;
//        }
//        
//        //变换为一维数组
//        $lastArr_2 = array();
//        foreach ($lastArr as $k => $v) {
//            foreach ($v as $key => $value) {
//                $lastArr_2[$value] = $k ;
//            }   
//        }
//        
//       // print_r($lastArr_2);exit ;
//        //删除重复的名称id
////        $delArr = array_keys($lastArr_2);
////        $del_id_arr = implode(',', $delArr) ;
////        //print_r($del_id_arr) ;
////        
////        $this->old_filter->where('id in('.$del_id_arr.')')->delete();
////        
////        echo  $this->old_filter->getLastSql();
//    }
    
    
//	public function bak($param) {
                //车型库里面替换
//        $carmodel = $this->old_carmodel->field('model_id,item_set')->select();
//        foreach ($carmodel as $k => $v) {
//            //echo  $v['item_set'] ;
//            //echo  '<br>' ; 
//            $data_1 = array();
//            $map_1 = array();   
//            
//            if($v['item_set']){
//                $data_1['item_set'] = str_replace(array_keys($lastArr_2), array_values($lastArr_2), $v['item_set']);
//            }else{
//                $data_1['item_set'] =  $v['item_set'] ;
//            }
//           
//            $map_1['model_id'] =  $v['model_id'] ;
//            $this->old_carmodel->where($map_1)->save($data_1);
//            //echo  $this->old_carmodel->getLastSql();
//            //echo '<br>' ;
//        }
        
//    }
    
    // itemset为空处理 。
    public function test() {
        $new_filter = $this->new_filter->select() ;
        
        foreach ($new_filter as $k => $v) {
            $map = array() ;
            $data = array() ; 
            
            $map['id']  = $v['id'] ;
            $info = $this->new_filter_bak ->where($map)->find() ;
            $data['old_id'] = $info['old_id'] ;
            $this->new_filter->where($map)->save($data) ;    
        }
   
    }
    
    
    
     //重新计算和更新车型，车系，品牌的显示状态 。 wql@20150928
    public function  change_isshow() {
        set_time_limit(0) ; 
         
        $con['_string'] = "oil_mass != 0 and oil_type != ''" ;
        $con['model_id'] = array('elt',4679) ;
        
        $carmodel = $this->new_carmodel->where($con)->select() ;
        //echo  $this->new_carmodel->getLastSql() ;
       // exit ;
        
        $seriesArr = array();
        foreach($carmodel as $key => $val){
            //判断机滤价格是否全为零。不为零，更新状态。wql@20150831
            $item_set = array();
            $filterArr = array();
            $filterPrice = array();

            $item_set = unserialize($val['item_set']) ;
            $filterArr = $item_set[1] ;
            foreach ($filterArr as $k => $v) {
               $filter_info = $this->new_filter->where('id='.$v)->find();
               $filterPrice[] = $filter_info['price'] ;
            }
            $filter_sum = array_sum($filterPrice);
            
            if($filter_sum > 0){
                //更新车型的状态为1 ,显示。。
                $map = array();
                $data = array();
                $map['model_id'] = $val['model_id'] ;
                $data['is_show'] =  1 ;
                $this->new_carmodel->where($map)->save($data) ;
                $seriesArr[]  =  $val['series_id'] ;
                //调试
                echo  $val['model_id']  ;
                echo  '<br>' ;
            }
        }
        
        //车系id去重 。
        $seriesArr = array_unique($seriesArr);
        //调试
        print_r($seriesArr);
        echo  '<br>' ;
        
        $brandArr = array();
        foreach($seriesArr as $k=>$v){
            //更新车系为1 ，显示 。
            $data1['is_show'] = 1 ;
            $this->new_carseries->where('series_id = '.$v)->save($data1); 
            $carseries_info =  $this->new_carseries->where('series_id = '.$v)->find(); 
            $brandArr[] = $carseries_info['brand_id'] ;
        }
        
        //品牌去重更新 。
        $brandArr = array_unique($brandArr);
        //调试 
        print_r($brandArr);
        echo  '<br>' ;
        
        foreach($brandArr as $k=>$v){
           //更新品牌为1 ，显示 。
           $data2['is_show'] = 1 ;
           $this->new_carbrand->where('brand_id='.$v)->save($data2); 
        }
        
        echo  '更新成功，请核对！';
           
    }
    
     
    
    
	
}