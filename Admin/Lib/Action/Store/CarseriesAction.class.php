<?php
class CarseriesAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->new_carbrand = D('new_carbrand');   //新品牌表
        $this->new_carseries = D('new_carseries');   //新车系表
	}
	
    public function index(){
        $condition['brand_id'] = isset($_GET['brand_id'])?$_GET['brand_id']:0;
        if (empty($condition['brand_id'])){
            $this->error("品牌ID错误！");exit;
        }else {
            $brandinfo = $this->new_carbrand->find($condition['brand_id']);
            $this->assign('brand_info',$brandinfo);   
        }
        if (isset($_REQUEST['series_name']) and !empty($_REQUEST['series_name'])){
            $condition['series_name'] = array('like',"%".$_REQUEST['series_name']."%");
            $this->assign('series_name',$_REQUEST['series_name']);
        }
        //echo '<pre>';print_r($map);
        // 计算总数
        $count = $this->new_carseries->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['series_name'] = $_REQUEST['series_name'];
        foreach($_REQUEST as $key=>$val) {  
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/".urlencode($val)."/";   
			}
        }
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->new_carseries->where($condition)->order('word ASC')->limit($p->firstRow.','.$p->listRows)->select();
        
        $model_fs = D(GROUP_NAME.'/Fs');
        $fs = $model_fs->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->assign('fs', $fs);
        //echo '<pre>';print_r($carseriesinfo);
        $this->display();
    }
    
    public function add_series(){
        $word = isset($_POST['word'])?$_POST['word']:'';
        $brand_id = isset($_POST['brand_id'])?$_POST['brand_id']:'0';
        //车系名称
        $series_name = isset($_POST['series_name'])?$_POST['series_name']:'';
        
        $data['series_name'] = $series_name;
        $data['word'] = $word;
        $data['brand_id'] = $brand_id;

        if ($series_name){
            if ($this->new_carseries->add($data)){
                $i = 1;
            }else{
                $i = -1;
            }
        }
        echo $i;exit;
    }
    
    public function save_series(){
        $word = isset($_POST['word'])?$_POST['word']:'';
        //车系名称
        $seriesname = isset($_POST['seriesname'])?$_POST['seriesname']:'';
        $series_id = isset($_POST['series_id'])?$_POST['series_id']:'0';

        if ($series_id){
            $data['series_name'] = $seriesname;
            $data['word'] = $word;
            $condition['series_id'] = $series_id;
            if ($this->new_carseries->where($condition)->save($data)){
                echo 1;exit;
            }
        }
        echo -1;exit;
    }
    
    public function delete_series(){
        $series_id = isset($_POST['series_id'])?$_POST['series_id']:'0';
        if ($series_id){
            if ($this->new_carseries->delete($series_id)){
                echo 1;
            }else {
                echo -1;
            }
        }else {
            echo -1;
        }
        exit;
    }
}
?>