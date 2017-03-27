<?php
// 本文档自动生成，仅供测试运行
class CarexportAction extends Action
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
        $this->display(THINK_PATH.'/Tpl/Autoindex/hello.html');
    }

    /**
    +----------------------------------------------------------
    * 探针模式
    +----------------------------------------------------------
    */
    public function checkEnv()
    {
        load('pointer',THINK_PATH.'/Tpl/Autoindex');//载入探针函数
        $env_table = check_env();//根据当前函数获取当前环境
        echo $env_table;
    }

	public function index2(){

//JS中的ft变量是所有品牌

        $brand_model = M('Carbrand');
		$brands = $brand_model->order("word asc,brand_id asc")->select();
		$brand_array = array();
		foreach($brands as $b){
			$brand_array[] = $b['brand_id'].','.$b['word'].'　'.$b['brand_name'];
		}
		$str = "var fct=new Array();\n";
		$str .= "fct['0'] = '".implode(',',$brand_array)."';\n"; 
//echo $str;

//br 以品牌ID为索引，品牌下车系为值
		$series_model = M('Carseries');
		$series = $series_model->order("brand_id asc")->select();
//dump($series);
//brands_array[品牌ID][] = 车系字符串
		$brands_array = array();
		foreach($series as $s){
			$brands_array[$s['brand_id']][] = $s['series_id'].','.$s['word'].'　'.$s['series_name'];
		}

		$str .= "var br=new Array();\n";

		foreach($brands_array as $b_id => $ser){
			$str .= "br['".$b_id."']='".implode(',',$ser)."';\n";
		}

		file_put_contents('./car.js',$str);
		echo '生成成功';
    }

	function insert_data(){
		$str = file_get_contents('./normal.js');//读取JS文件的内容
		//echo $str;
		$js_rows = explode("\n",$str);

		$brand_add_data = array();//品牌插入值
		$series_add_data = array();//车系插入值

		//按行读取处理数据
		foreach($js_rows as $row){
			//品牌
			if(substr($row,0,5) == "fct['"){//此行是品牌列表
				$_b = explode("'",$row);
				$_brand_str = $_b[3];//定义了品牌的整个内容
				$_brand_arr = explode(",",$_brand_str);//拆分品牌信息
				//$_brand_arr 偶数键值为品牌ID，奇数为品牌信息字符串
				if(!empty($_brand_arr)){
					foreach($_brand_arr as $k => $binfo){
						if($k % 2 == 0){
							$_id = $binfo;
							$_info = $_brand_arr[++$k];
							$_word = $_info{0};
							$_brand_name = trim(substr($_info,4));
							$brand_add_data[] = array('brand_id' => $_id,'word' => $_word, 'brand_name' => $_brand_name);
						}else{
							continue;	
						}
					}
				}
			}

			//车系
			if(substr($row,0,4) == "br['"){
				$_s = explode("'",$row);
				$brand_id = $_s[1];//品牌ID
				//print_r($_s);exit;
				$_series_str = $_s[3];
				$_series_arr = explode(",",$_series_str);
				if(!empty($_series_arr)){
					foreach($_series_arr as $k => $sinfo){
						if($k % 2 == 0){
							$_id = $sinfo;
							$_info = $_series_arr[++$k];
							$_word = $_info{0};
							$series_name = trim(substr($_info,4));
							$series_add_data[] = array('series_id' => $_id,'word' => $_word, 'series_name' => $series_name ,'brand_id' => $brand_id);
						}else{
							continue;	
						}
					}
				}
				
			}
		}

		if(!empty($brand_add_data)){
			//print_r($brand_add_data[0]);
			$bm = M("carbrand");
			if($bm->addAll($brand_add_data)){
			//echo $bm->getLastSql();
				echo '插入品牌信息成功！<br />';
			}else{
				echo '插入品牌信息失败，可能重复插入，主键冲突！<br />';
			}
		}

		if(!empty($series_add_data)){
			//print_r($series_add_data[0]);
			$sm = M("carseries");
			if($sm->addAll($series_add_data)){
				echo '插入车系信息成功！';
			//echo $sm->getLastSql();
			}else{
				echo '插入车系信息失败，可能重复插入，主键冲突！<br />';
			}
		}
		
	}

}
?>