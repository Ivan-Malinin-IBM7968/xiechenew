<?php
namespace Xiecheapp\Controller;

class IndexController extends CommonController {
	
	public function __construct() {
		parent::__construct();
		if($_SERVER["REMOTE_ADDR"]=='182.118.33.6'){ exit; }
		$this->IpcountModel = D('ipcount');//记录访问IP
		$this->RegionModel = D('region');//区域
		$this->ShopModel = D('shop');//商铺
		$this->CouponModel = D('coupon');//优惠券
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->CarbrandModel = D('carbrand');//
		$this->BaiduModel = D('baidu');//百度表
		$this->membercar_model = D('Membercar');
	}
	public function index(){
		$tuijian_coupon_ids = C('tuijian_money_coupon');
		$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);
		$sort = implode(",",$tuijian_coupon_ids);
		$couponLists = $this->CouponModel -> where($map_tuijian_c)->order("field (id,{$sort})")->limit(4)->select();
		$this->assign('list', $couponLists);
		$this->assign('title',"携车网-上海汽车上门保养,4S店折扣优惠,事故车维修预约");
		$this->assign('meta_keyword',"上门保养,上海上门保养,汽车上门保养,汽车保养,汽车维修,4S店预约保养,事故车维修");
		$this->assign('description',"携车网:让养车省钱又省事的汽车售后服务网站.【府上养车】99元享4S品质上门汽车保养服务,机油配件京东同价.【4S店折扣】预约工时优 惠:【维修返利】出险事故车预约维修返利");

		$this->assign('noshow',true);
		$this->assign('noclose',true);
		if($_SESSION['city']){
			$city_model = D('city');
			$city_map['id'] =$_SESSION['city'];
			$city_info = $city_model->where($city_map)->find();
			$this->assign('new_city_name',$city_info['name']);
			if($_SESSION['city'] == 1){
				$this->display('index_new');
			}elseif ($_SESSION['city'] >=2) {
				$this->display('index_suhang');
			}else{
				$this->display('index_new');
			}
		}else{
			$this->display('index_new');
		}
		
	}
	//获取汽车品牌
	public function getCarBrand(){
		$mBrand = D('new_carbrand');
        $mCarseries = D('new_carseries');
        
		$brandLists = $mBrand->where(array('is_show'=>1))->order('word asc,brand_name asc')->select();
		$ret = array();
		foreach ($brandLists as $key => $val){
            $word = $val['word'];
			unset($val['word']);
			$ret[$word][] = $val;
            
		}
		echo json_encode($ret);
	}
    
    
    
	//车系
	public function getCarSeries(){
		$brandId = @$_POST['brand_id'];
		if (!$brandId) {
			return false;
		}
		$mCarseries = D('new_carseries');
        
        $map['brand_id'] = $brandId ;
        $map['is_show'] = 1 ;  
		$ret = $mCarseries->where($map)->order('series_name asc')->select();

		echo json_encode($ret);
	}
    

	//车型号
	public function getCarModel(){
		$seriesId = @$_POST['series_id'];
		if (!$seriesId) {
			return false;
		}
		$mCarmodel = D('new_carmodel');
        $mFiltermodel = D('new_item_filter');
         //机油为空车型不取出来
        //$cond['oil_mass'] = array('neq',' ');
        // $cond['oil_type'] = array('neq',' ');
        $cond['is_show'] =  1  ;
        $cond['series_id'] =  $seriesId ; 
        
		$ret = $mCarmodel->where($cond)->select();

		echo json_encode($ret);
	}
    
    
	//保存我的车型
	public function saveCarData(){
		$brandId = @$_POST['brand_id'];
		$seriesId = @$_POST['series_id'];
		$modelId = @$_POST['model_id'];
		$carName = @$_POST['car_name'];
	
		if(!$brandId || !$seriesId || !$modelId || !$carName){
			echo json_encode(array('status'=>0,'msg'=>'param error'));
			exit;
		}
		//cookie('carHistory',null);
		$history[] = array(
				'brandId' => $brandId,
				'seriesId'=> $seriesId,
				'modelId' => $modelId,
				'carName' => $carName
		);
		$isRepeat = false;//判断是否重复
		if ( cookie('carHistory') ) {
			$cookieHistory = cookie('carHistory');
			$cookieHistory = $cookieHistory[0];
			$tmp_arr = unserialize($cookieHistory);
			foreach ( $tmp_arr as $val ){
				if($val['modelId'] == $modelId){	//已经记录过了
					$isRepeat = true;
				}else{
					$history[] = $val;
				}
			}
		}
		$carHistory[] = serialize($history);
		if(!$isRepeat){
			cookie('carHistory',$carHistory,86400);
		}
		cookie('carName',$carName,86400);
		cookie('modelId',$modelId,86400);
		$mBrand = M('tp_xieche.carbrand','xc_');  //车品牌
		$brandRes = $mBrand->field('brand_name')->where(array('brand_id'=>$brandId))->find();
		if ($brandRes) {
			cookie('brandName',$brandRes['brand_name']);
		}
		$_SESSION['brandId'] = $brandId;
		$_SESSION['seriesId'] = $seriesId;
		$_SESSION['modelId'] = $modelId;
		$_SESSION['carName'] = $carName;
		
		//用户登录以后就绑定车型
		$uid = $this->GetUserId();
		if ($uid) {
			$where = array(
					'uid'=>$uid,
					'brand_id'=>$brandId,
					'series_id' => $seriesId,
					'model_id' => $modelId
			);
			$membercar = $this->membercar_model->where($where)->select();
			$data = array(
					'uid'=>$uid,
					'brand_id'=>$brandId,
					'series_id' => $seriesId,
					'model_id' => $modelId,
					'car_name' => $carName,
					'car_identification_code'=> '',
					'avgoil_type'=>1,
					'status'=>1,
					'create_time'=> time(),
					'is_default' => 1
			);
			if (!$membercar){
				$this->membercar_model->add($data);
			}else{
				$this->membercar_model->where($where)->save($data);
			}
		}
		echo json_encode(array('status'=>1,'msg'=>'success'));
	}
	
	function checkCar(){
		if ( isset( $_SESSION['carHistory'] ) ) {
			echo json_encode(array('status'=>1));
		}else{
			echo json_encode(array('status'=>0));
		}
	}
}