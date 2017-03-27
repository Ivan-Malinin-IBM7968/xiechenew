<?php

class AppnewController extends CommonAction {
	
	public function __construct() {
		parent::__construct();
		$this->IpcountModel = D('ipcount');//记录访问IP
		$this->RegionModel = D('region');//区域
		$this->ShopModel = D('shop');//商铺
		$this->CouponModel = D('coupon');//优惠券
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->CarbrandModel = D('carbrand');//
		$this->BaiduModel = D('baidu');//百度表
		$this->membercar_model = D('Membercar');
		$this->accesskey = 'fqcd67763332fqcd';
		
		if(ACTION_NAME !='getAccessKey'){	//验证key
			$this->_validAccessKey();	
		}
	}
	
	function test(){
		echo "1111111";
	}

	protected function _validAccessKey(){
		if ($this->accesskey != $_REQUEST['accesskey']) {
			$this->_ret(null,0,'error key');
		}
	}
	
	//公共返回方法
	protected function _ret($data=NULL, $status = 1, $msg='success'){
		$ret = array(
				'status' => $status,
				'msg'	=> $msg,
				'data'	=> $data
		);
		echo json_encode($ret);//特殊要返回放括号数组的情况
		exit;
	}
	
	//获取全局key
	public function getAccessKey(){
		$key = 'fqcd67763332fqcd';
		$this->_ret($key);	
	}
	
	//车品牌
	public function getCarBrand(){
		$mBrand = M('tp_xieche.carbrand','xc_');
		
// 		$data = $mBrand->where(array('isShow'=>1))->select();//TODO::测试环境暂时隐藏
		$data = $mBrand->select();
		$this->_ret($data);
	}
	//车系
	public function getCarSeries(){
		$brandId = @$_REQUEST['brand_id'];
		if (!$brandId) {
			$this->_ret(null,0,'品牌id不能为空');
		}
		$mCarseries = M('tp_xieche.carseries','xc_');
		$data = $mCarseries->where(array('brand_id'=>$brandId))->select();
		$this->_ret($data);
	}
	//车型号
	public function getCarModel(){
		$seriesId = @$_REQUEST['series_id'];
		if (!$seriesId) {
			$this->_ret(null,0,'车系id不能为空');
		}
		$mCarmodel = M('tp_xieche.carmodel','xc_');
		$data = $mCarmodel->field('item_set',true)->where(array('series_id'=>$seriesId))->select();
		$this->_ret($data);
	}
	
		
}