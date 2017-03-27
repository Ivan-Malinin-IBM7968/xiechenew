<?php
class CarmodelAction extends CommonAction {
    function __construct() {
		parent::__construct();
        $this->new_carseries = D('new_carseries');   //新车系表
        $this->new_carmodel = D('new_carmodel');   //新车型表
		
	}
	
    public function index(){
        $condition['series_id'] = isset($_GET['series_id'])?$_GET['series_id']:0;
        if (empty($condition['series_id'])){
            $this->error("车系ID错误！");exit;
        }else {
            $seriesinfo = $this->new_carseries->find($condition['series_id']);
            $this->assign('series_info',$seriesinfo);   
        }
        if (isset($_REQUEST['model_name']) and !empty($_REQUEST['model_name'])){
            $condition['model_name'] = array('like',"%".$_REQUEST['model_name']."%");
            $this->assign('model_name',$_REQUEST['model_name']);
        }
        //echo '<pre>';print_r($map);
        // 计算总数
        $count = $this->new_carmodel->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['model_name'] = $_REQUEST['model_name'];
        foreach($_REQUEST as $key=>$val) {  
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/".urlencode($val)."/";   
			}
        }
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->new_carmodel->where($condition)->order('model_id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carmodelinfo);
        $this->display();
    }
    
    public function add_model(){
        $model_name = isset($_POST['model_name'])?$_POST['model_name']:'';
        $series_id = isset($_POST['series_id'])?$_POST['series_id']:'0';
        
        $data['model_name'] = $model_name;
        $data['series_id'] = $series_id;
        if ($this->new_carmodel->add($data)){
            echo 1;exit;
        }else {
            echo -1;exit;
        }
    }
    
    public function save_model(){
        $model_name = isset($_POST['model_name'])?$_POST['model_name']:'';
        $model_id = isset($_POST['model_id'])?$_POST['model_id']:'0';
        if ($model_id){
            $data['model_name'] = $model_name;
            $condition['model_id'] = $model_id;
            if ($this->new_carmodel->where($condition)->save($data)){
                echo 1;exit;
            }
        }
        echo -1;exit;
    }
    
    public function delete_model(){
        $model_id = isset($_POST['model_id'])?$_POST['model_id']:'0';
        if ($model_id){
            if ($this->new_carmodel->delete($model_id)){
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