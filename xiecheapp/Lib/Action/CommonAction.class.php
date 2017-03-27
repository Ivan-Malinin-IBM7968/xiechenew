<?php
//公共模块
class CommonAction extends Action {

	//404
	function _empty(){
		header("HTTP/1.0 404 Not Found");
		$this->display('Empty:index');
	}

	function _initialize() {
        import('@.ORG.Util.Cookie');
        import('@.ORG.Util.Session');
        $uid = $this->GetUserId();
        if ($uid){
            $Xsession = M('Xsession');
            $data['login_time'] = time();
            $map['uid'] = $uid;
            $Xsession->where($map)->save($data);
        }
		
		$area = $this->getUrlAreaflag();
		//获取city_id
		$citys= C('CITYS');
		$citys_flip = array_flip($citys);
		$city_id = $citys_flip[$area['0']];
		if(!$city_id) {
			 $city_id = 3306;
			 $area = array('上海','shanghai');
		}
		if( !$_SESSION['area_info'] || ($_SESSION['area_info']  && $_SESSION['area_info'][0] != $area[0]) ) {
			$_SESSION['area_info'] = $area;
			$_SESSION['city_id'] = $city_id;
		}

		$this->city_id = $_SESSION['city_id'];
		$this->city_name = $_SESSION['area_info']['0'];
		
		$this->city = $_SESSION['area_info']['1'];
		$this->assign('areaname',$this->city_name);
	}
//代码日志跟踪
	function addCodeLog($type,$msg,$oid=''){
		if(!$type){
			return false;
		}
		$mCodeLog = D('logcode');
		$mErrorLog = D('logerror');
		if ( isset($_SESSION['codeLogType']) ) {
			if ($_SESSION['codeLogType'] == $type) {
				if ( !empty($_SESSION['codeLogId'] ) ) {
					$where = array('id'=>$_SESSION['codeLogId']);
					$res = $mCodeLog->field('log')->where($where)->find();
					$msg = $res['log'].'||'.date('Y-m-d H:i:s',time()).$msg;
					$data = array(
							'type'=>$type,
							'oid'=>$oid,
							'log'=>$msg
					);
					$mCodeLog->where($where)->save($data);
				}else{
					$data = array(
							'type'=>$type,
							'oid'=>$oid,
							'log'=>date('Y-m-d H:i:s',time()).'codeLogId empty: '.$msg,
							'insertTime'=>time()
					);
					$mErrorLog->add($data);
				}
			}else{
				$data = array(
						'type'=>$type,
						'oid'=>$oid,
						'log'=>date('Y-m-d H:i:s',time()).'type error: '.$msg,
						'insertTime'=>time()
				);
				$mErrorLog->add($data);
			}
		}else{
			//首次
			$_SESSION['codeLogType'] = $type;
			$data = array(
					'type'=>$type,
					'oid'=>$oid,
					'log'=>date('Y-m-d H:i:s',time()).$msg,
					'insertTime'=>time()
			);
				
			$codeId = $mCodeLog->add($data);
			if ($codeId) {
				$_SESSION['codeLogId'] = $codeId;
			}
				
		}
		return true;
	}
	//代码日志跟踪提交
	function submitCodeLog($type){
		unset($_SESSION['codeLogId'],$_SESSION['codeLogType']);
		return true;
	}
	
	//根据订单类型换算减免金额
	function get_typeprice($order_type,$oil_price,$jilv_price){
		$item_oil = D('item_oil');
		switch ($order_type) {
			//10元打车代金券
			case 4:
			$typeprice['typeprice'] = 0;
			break;
			//上门汽车保养专业服务
			case 6:
			$typeprice['typeprice'] = 99;
			break;
			//府上养车-黄壳机油套餐
			case 7:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'49'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-蓝壳机油套餐
			case 8:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'47'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-灰壳机油套餐
			case 9:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'45'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;
			//府上养车-金美孚机油套餐
			case 10:
			if($oil_price>0){
				$info = $item_oil->where(array('id'=>'50'))->find();
			}else{
				$info['price'] = 0;
			}
			$typeprice['typeprice'] = $info['price']+$jilv_price+99;
			break;

			//爱代驾高端车型小保养套餐
			case 11:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'50'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			//$typeprice = $oil_price+$jilv_price+99-$package_info['price'];
			//echo 'oil_price='.$oil_price.'jilv_price='.$jilv_price;exit;
			break;
			
			//爱代驾中端车型小保养套餐
			case 12:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//好车况套餐
			case 13:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好车况套餐:38项全车检测+7项细节养护';
			break;

			//好空气套餐
			case 14:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐:更换空调滤';
			break;

			//好动力套餐
			case 15:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好动力套餐:节气门清洗+38项全车检测+7项细节养护';
			break;

			//保养服务+检测+养护
			case 16:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '套餐:上门保养人工服务+38项全车检测+7项细节养护';
			break;

			//矿物质油保养套餐+检测+养护
			case 17:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;
			
			//半合成油保养套餐+检测+养护  
			case 18:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;

			//全合成油保养套餐+检测+养护
			case 19:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			$typeprice['remark'] = '套餐:7项细节养护';
			break;

			//38项检测+7项细节养护(光大)
			case 21:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '套餐:38项检测+7项细节养护';
			break;

			//光大168
			case 22:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//光大268 
			case 23:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//光大368
			case 24:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'50'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//浦发199
			case 25:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//浦发299
			case 26:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//浦发399
			case 27:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//全车检测38项(淘38)
			case 28:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '全车检测38项(淘38)';
			break;

			//细节养护7项(淘38)
			case 29:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '细节养护7项(淘38)';
			break;

			//更换空调滤工时(淘38)
			case 30:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '更换空调滤工时(淘38)';
			break;

			//更换雨刮工时(淘38)
			case 31:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '更换雨刮工时(淘38)';
			break;

			//小保养工时(淘38)
			case 32:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '小保养工时(淘38)';
			break;

			//好空气套餐(奥迪宝马奔驰)
			case 33:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐(奥迪宝马奔驰)';
			break;

			//补配件免人工订单
			case 34:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '补配件免人工订单';
			break;

			//好车况（市场部推广）
			case 35:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '好车况套餐:38项全车检测+7项细节养护';
			break;

			//大众点评199
			case 36:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//大众点评299
			case 37:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//大众点评399
			case 38:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//防雾霾1元
			case 48:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '防雾霾1元套餐';
			break;

			//防雾霾8元
			case 49:
			$typeprice['typeprice'] = 99;
			$typeprice['remark'] = '防雾霾8元套餐';
			break;

			//好车况套餐（大众点评）
			case 50:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '大众点评好车况套餐:38项全车检测+7项细节养护';
			break;

			//保养服务+检测+养护（大众点评）
			case 51:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '大众点评套餐:上门保养人工服务+38项全车检测+7项细节养护';
			break;

			//好空气套餐（平安财险）
			case 52:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐:更换空调滤';
			break;

			//好动力套餐（后付费）
			case 53:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '好动力套餐:节气门清洗+38项全车检测+7项细节养护';
			break;

			//发动机舱精洗套餐（淘）
			case 54:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = 99-$package_info['price'];
			$typeprice['remark'] = '发动机舱精洗套餐';
			break;


			//好空气套餐(奥迪宝马奔驰后付费)
			case 55:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $kongtiao_price+99-$package_info['price'];
			$typeprice['remark'] = '好空气套餐';
			break;
                        
                        //黄壳199套餐（预付费）
			case 56:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'49'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
			
			//蓝壳299套餐（预付费）
			case 57:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'47'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;

			//灰壳399套餐（预付费）
			case 58:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$info = $item_oil->where(array('id'=>'45'))->find();
			$typeprice['typeprice'] = $info['price']+$jilv_price+99-$package_info['price'];
			break;
                    
			//发动机机舱精洗套餐（预付费）
			case 59:
			$typeprice['typeprice'] = 99;
			break;
        
            //268大保养（预付费）
			case 60:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $oil_price+$jilv_price+$kongtiao_price+$kongqi_price+99-$package_info['price'];
			break;
            //378大保养（预付费）
			case 61:
			$package = D('package');
			$package_info = $package->where(array('order_type'=>$order_type))->find();
			$typeprice['typeprice'] = $oil_price+$jilv_price+$kongtiao_price+$kongqi_price+99-$package_info['price'];
			break;

			//空调清洗套餐（后付费）
            case 62:
            $package = D('package');
            $package_info = $package->where(array('order_type'=>$order_type))->find();
            $typeprice['typeprice'] = 99-$package_info['price'];
            $typeprice['remark'] = '空调清洗套餐';
            break;

            //空调清洗套餐（免费）
            case 63:
            $typeprice['typeprice'] = 99;
            $typeprice['remark'] = '空调清洗套餐';
            break;
       
            //好动力套餐（免费）
            case 64:
            $typeprice['typeprice'] = 99;
            $typeprice['remark'] = '好动力套餐';
            break;

			default:
			$typeitem = null;
			break;
		}
		return $typeprice;
	}
	

	function getUrlAreaflag() {
		$areaflag = C('AREAFLAG');
		$url = $_SERVER['SERVER_NAME'];
		$area  = explode(".",$url);
		return array($areaflag[$area[0]],$area[0]);
	}


	/*  @author:chf
		@function:发送数据(平安)($url:http://www.xieche.com.cn/test/ur)($data: $data['img']="测试平安";)
		@time:2014-01-16;
	*/
	function Api_toPA($url,$data){
		$host = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}



	/*  @author:chf
		@function:发送数据(百车宝)
		@time:2014-01-16;
	*/
	function Api_toBCB($url,$data,$header){
		$host = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	/*
		@author:chf
		@function:得到城市对应ID
		@time:2013-03-21
	*/
	public function GetCityId($CityName){
		switch($CityName){
			case '北京':
				$CityId = 2912;
				return $CityId;
			case '上海':
				$CityId = 3306;
				return $CityId;
			case '广州':
				$CityId = 2918;
				return $CityId;
		}
	}

	/*
		@author:chf
		@function:得到区域
		@time:2013-03-22
	*/
	function GetArea(){
		$area = $_SESSION['area_info'];
		if($area[0]){
			$map_s['shop_city'] = $this->GetCityId($area[0]);

			if($area[1] == 'c' || $area[1] == 'www' || $area[1] == 'x' || $area[1] == 'shanghai'){
				$area[1] = 'sh';
			}
			$this->assign('sessarea',$area[1]);
		}
		$Carea = c('AREAS');
		$this->assign('Carea',$Carea);
	
	}


	/*
		@author:chf
		@function:得到区域
		@time:2013-03-22
	*/
	function ReturnGetArea(){
		$area = $_SESSION['area_info'];
		if($area[0]){
			$map_s['shop_city'] = $this->GetCityId($area[0]);

			if($area[1] == 'c' || $area[1] == 'www' || $area[1] == 'x' || $area[1] == 'shanghai'){
				$area[1] = 'sh';
			}
			$this->assign('sessarea',$area[1]);
		}
		$Carea = c('AREAS');
		return $Carea[$area[1]];
	}

	public function index() {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }

    /**
      +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $name = $this->getActionName();
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $_pk = $model->getPk();       
        $count = $model->where($map)->count($_pk);
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
           // dump($_REQUEST);
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, 10);
            //分页查询数据
			/*@author:chf 咨询按照时间倒叙排列条件*/
			if($map['ArticleType'] == 'yes'){
				 $order = 'create_time';
			}
			unset($map['ArticleType']);
			/*@author:chf 咨询按照时间倒叙排列条件*/
			
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val) && $val != "" ) {
                    $p->parameter .= $key."/" . urlencode($val) . "/";
                }else {
                    $p->parameter .= $key."/" . urlencode($val[1]) . "/";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
			$this->assign("allcount", $count);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return $voList;
    }

    function insert($name='') {
        //B('FilterString');
		if(!$name){
			$name = $this->getActionName();
		}
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

    public function add() {
        $this->display();
    }

    function read() {
        $this->edit();
    }

    function edit() {
        $name = $this->getActionName();
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $p_key=$model->getPk();
        $p_key = 'getBy'.$p_key;
        $vo = $model->$p_key($id);
        if (isset($vo['car_number']) and !empty($vo['car_number'])){
            $car_number_arr = explode('_',$vo['car_number']);
            if (isset($car_number_arr[1])){
                $vo['s_pro'] = $car_number_arr[0];
                $vo['car_number'] = $car_number_arr[1];
            }else{
                $vo['s_pro'] = '';
                $vo['car_number'] = $car_number_arr[0];
            }
        }
        $this->assign('vo', $vo);
        $this->display();
    }

    function update() {
        //B('FilterString');
       
        $name = $this->getActionName();
        $model = D($name);
       // dump($_POST);
        //dump($model);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }

    /**
      +----------------------------------------------------------
     * 默认删除操作
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function delete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = M($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('status', - 1);
                if ($list !== false) {
                    $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    public function foreverdelete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()) {
                    //echo $model->getlastsql();
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }

    public function clear() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            if (false !== $model->where('status=1')->delete()) {
                $this->assign("jumpUrl", $this->getReturnUrl());
                $this->success(L('_DELETE_SUCCESS_'));
            } else {
                $this->error(L('_DELETE_FAIL_'));
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function forbid() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST [$pk];
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition);
        if ($list !== false) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态禁用成功');
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function checkPass() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->checkPass($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态批准成功！');
        } else {
            $this->error('状态批准失败！');
        }
    }

    public function recycle() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->recycle($condition)) {

            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态还原成功！');
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin() {
        $map = $this->_search();
        $map ['status'] = - 1;
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        //恢复指定记录
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态恢复成功！');
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $name = $this->getActionName();
            $model = D($name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }
    

    
	// 用户登录页面
	public function login() {
		if(!isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
		    if (isset($_REQUEST['tolken'])){
		        $tolken = $_REQUEST['tolken'];
		        $model_member = D('Member');
		        $membermap['tolken'] = $tolken;
		        $membermap['tolken_time'] = array('gt',time());
		        $member = $model_member->where($membermap)->find();
		        if (!$member){
		            $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
		            $xml_content .= "<status>1</status><tolken>".$tolken."</tolken><desc>账号未登陆</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
		        }else{
		            return true;
		        }
		    }
			$cookie_uid = Cookie::get('uid');
			$cookie_username =  Cookie::get('username');
			$cookie_x_id = Cookie::get('x_id');
			if($cookie_uid && $cookie_username && $cookie_x_id){
				$Xsession = D('Xsession');
				$res = $Xsession->where("uid='$cookie_uid' AND username='$cookie_username' AND x_id='$cookie_x_id'")->find();
				if($res){
					$_SESSION['uid'] = $res['uid'];
					$_SESSION['username'] = $res['username'];
					return TRUE;
				}else{
					redirect("/Public/login?jumpUrl=".urlencode($_SERVER['REQUEST_URI']));
				}			
			}else{
			    if (isset($_SESSION['uid'])){
            	    if (isset($_SESSION['r_data'])){
            	        $r_data = $_SESSION['r_data'];
            	        $r_data['brand_id'] = 0;
            	        $r_data['series_id'] = 0;
            	        $r_data['model_id'] = 0;
            	        if ($_SESSION['r_data']['brand_id']){
            	            $r_data['brand_id'] = $_SESSION['r_data']['brand_id'];
            	        }
            	        if ($_SESSION['r_data']['series_id']){
            	            $r_data['series_id'] = $_SESSION['r_data']['series_id'];
            	        }
            	        if ($_SESSION['r_data']['model_id']){
            	            $r_data['model_id'] = $_SESSION['r_data']['model_id'];
            	        }
            	    }else {
            	        $r_data['brand_id'] = 0;
            	        $r_data['series_id'] = 0;
            	        $r_data['model_id'] = 0;
            	    }
            	    unset($_SESSION['r_data']);
            	    return true;//TODO::暂时隐藏掉
//             	    $this->assign('r_data',$r_data);
//             	    $this->assign('uid',$_SESSION['uid']);
// 	                Cookie::set('_currentUrl_', __SELF__);
// 			        $this->display('Member:complete_member');
			    }else{
					redirect("/Public/login?jumpUrl=".urlencode($_SERVER['REQUEST_URI']));
			    }
			} 			
		}else{
			return TRUE;
		}
	}
	//验证码
	public function verify(){
		$type = isset($_GET['type'])?$_GET['type']:'gif';
		$width = isset($_GET['width'])?$_GET['width']:'100';
		$height = isset($_GET['height'])?$_GET['height']:'50';
        import("@.ORG.Util.Image");
        Image::buildImageVerify(4,1,$type,$width,$height);
    }
    public function action_tip($list){
    	if ($list !== false) { //保存成功
    	        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('成功!');
			} else {
				//失败提示
				$this->error('失败!');
			}	
    	
    }
	//返回用户ID
		public function GetUserId(){
			return isset($_SESSION[C('USER_ID')])?$_SESSION[C('USER_ID')]:0;
		}
    //返回用户ID
	public function appGetUserId(){
		if (isset($_GET['tolken'])){
		    $tolken = $_GET['tolken'];
		    $model_member = D("Member");
		    $member_map['tolken'] = $tolken;
		    $member_map['tolken_time'] = array('gt',time());
		    $member = $model_member->where($member_map)->find();
		    if ($member){
		        return $member['uid'];
		    }else {
		        return 0;
		    }
		}else {
		    return 0;
		}
	}
	//短信接口
	function curl_sms($post = '' , $charset = null, $num=1,$type = null ){
	
		$this->addCodeLog('发送短信', '开始,通道:'.$num);
	
		$datamobile = array('130','131','132','155','156','186','185');
		$mobile = $post['phones'];
		/*$submobile = substr($post['phones'],0,3);
			$content = $post['content'] = str_replace("联通", "联_通", $post['content']);
		if(in_array($submobile,$datamobile)){
		$content = $post['content'] = $post['content']."  回复TD退订";
		}*/

        // dingjb 2015-09-16 14:27:21 容联云短信通道
        // 首先检测是否为 容联云
        if ($num == 4) {
            // 第四通道：容联云短信

            // 检测手机号和短信内容
            if (!array_key_exists('phones', $post) || !array_key_exists('content', $post)) {
                return false;
            }

            $to     = $post['phones'];  // 目标手机
            $datas  = $post['content']; // 模板数据
            $templateId = $type == null ? 1 : $type; // 模板ID

            // 组装日志信息
            $this->addCodeLog('发送短信', '容联云模板:【'.$templateId.'】, 电话:'.$post['phones'].', 模板数据:'.json_encode($post['content']));
            // 发送信息
            $this->sendYunSMS($to, $datas, $templateId);
            // 删除发送 Session
            $this->submitCodeLog('发送短信');

            return true;
        }

		if($type!=1){
			$post['content'] .= " 【携车网】";
		}else{
			$post['content'] .= " 【携车网府上养车】";
		}
		$this->addCodeLog('发送短信', '内容:'.$post['content'].'电话：'.$mobile);
		if($num==1){
			//第一通道
			$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
			$res = $client->__soapCall('SendMsg',array('parameters' => $param));
				
			$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
			$this->submitCodeLog('发送短信');
		}elseif($num==2){
			//第二通道
			$url = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage';
			$header = array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8");
			$param = http_build_query(array(
					'account'=>'sdk_xieche',
					'password'=>'fqcd123223',
					'destmobile'=>$mobile,
					'msgText'=>$post['content'],
					'sendDateTime'=>''
			)
			);
				
			$res = $this->curl($url,$param,$header);
				
			$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
			$this->submitCodeLog('发送短信');
			return $res;
		}elseif($num == 3){
			//第三通道
			$url = 'http://58.83.147.92:8080/qxt/smssenderv2';
			$header = array("Content-Type: application/x-www-form-urlencoded;charset=GBK");
			$param = http_build_query(array(
					'user'=>'zs_donghua',
					'password'=>md5('121212'),
					'tele'=>$mobile,
					'msg'=>iconv("utf-8","gbk//IGNORE",$post['content']),
			)
			);
			$res = $this->curl($url,$param,$header);
				
			$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
			$this->submitCodeLog('发送短信');
			return $res;
		}else{
			/*
			 * 修改需求：验证码两个一起发
			* bright
			*/
			//$post['content'] .= " 【携车网】";
			$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
			$client->__soapCall('SendMsg',array('parameters' => $param));
				
				
			$url = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage';
			$header = array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8");
			$param = http_build_query(array(
					'account'=>'sdk_xieche',
					'password'=>'fqcd123223',
					'destmobile'=>$mobile,
					'msgText'=>$post['content'],
					'sendDateTime'=>''
			)
			);
			$res = $this->curl($url,$param,$header);
				
			$this->addCodeLog('发送短信', '返回值:'.var_export($res, true));
			$this->submitCodeLog('发送短信');
			return $res;
		}
	}

    /**
     * 容联云短信
     * @author dingjb
     * @date   2015-09-16 16:45:23
     *
     * @param $to       目标手机号
     * @param $datas    模板变量数据
     * @param $tempId   模板ID
     * @return bool     是否成功
     */
    public function sendYunSMS($to,$datas,$tempId)
    {
        // 导入 容联云 SDK
        vendor('YunSms.CCPRestSmsSDK', 'xiecheapp/Lib/Vendor/');

        // 初始化参数
        $accountSid     = C('accountSid');
        $accountToken   = C('accountToken');
        $appId          = C('appId');
        $serverIP       = C('serverIP');
        $serverPort     = C('serverPort');
        $softVersion    = C('softVersion');

        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);

        // Error: 无返回数据
        if($result == NULL ) {
            $this->addCodeLog('发送短信错误', '返回值:无返回数据');
            return false;
        }

        // Error: 发送错误
        if($result->statusCode != 0) {
            $this->addCodeLog('发送短信错误', '返回值:'.var_export($result, true));
            return false;
        } else { // Success: 发送成功
            $this->addCodeLog('发送短信', '返回值:'.var_export($result, true));
            return true;
        }
    }
        
    //生成用户显示的订单号
    public function get_orderid($id){
        $orderid = $id+987;
        $sum = 0;
	    for($ii=0;$ii<strlen($orderid);$ii++){
	        $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum%10;
        return $orderid.$str;
    }
    //还原订单号
    public function get_true_orderid($id){
        $orderid = substr(trim($id),0,-1);
        $orderid = $orderid-987;
        return $orderid;
    }
    //生成表格图片
	public function createtableimg($tables_arr,$folder,$img_name){
	    //$img_name = 'test33.png';
        import("@.ORG.Util.Image");
        $img_path = 'UPLOADS/Product/'.$folder;
        mk_dir($img_path);
        $img_name = $img_path.'/'.$img_name;
        Image::getimgtable($tables_arr,$img_name);
    }

	/*
		@author:chf
		@function:得到推荐优惠套餐券信息
		@time:2014-05-05
	*/
    function get_tuijian_coupon($shop_id='',$modle_id=''){
	    //现金券推荐
	    $model_coupon = D('Coupon');
		$OrderModel = D('order');
	    $map_tuijian_c['is_delete'] = 0;
	    $map_tuijian_c['show_s_time'] = array('lt',time());
	    $map_tuijian_c['show_e_time'] = array('gt',time());
	    $map_tuijian_c['coupon_type'] = 1;
		
		if($shop_id){ 
			$map_tuijian_c['shop_id'] = $shop_id;
		}
		if($modle_id){
			$map_tuijian_c['modle_id'] = Array('in',$modle_id);;
		}
		
	    $tuijian_coupon1 = $model_coupon->where($map_tuijian_c)->limit(5)->select();
		if( count($tuijian_coupon1) != '0' && count($tuijian_coupon1) != '5'){
			$tuijiancount_1 = 5 - count($tuijian_coupon1);
			unset($map_tuijian_c['shop_id']);
			unset($map_tuijian_c['modle_id']);
			$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
			$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);
			$sort = implode(",",$tuijian_coupon_ids);
			$arr_tuijian_1 = $model_coupon->where($map_tuijian_c)->order("field (id,{$sort})")->limit($tuijiancount_1)->select();
			$tuijian_coupon1 = array_merge($tuijian_coupon1,$arr_tuijian_1);
		}

		if( count($tuijian_coupon1) == '0'){
			unset($map_tuijian_c['shop_id']);
			unset($map_tuijian_c['modle_id']);
			$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
			$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);
			$sort = implode(",",$tuijian_coupon_ids);
			$tuijian_coupon1 = $model_coupon->where($map_tuijian_c)->order("field (id,{$sort})")->limit(5)->select();
		}
	    $this->assign('tuijian_coupon1',$tuijian_coupon1);
		 //现金券推荐结束

	    //套餐券推荐
		unset($map_tuijian_c['id']);
		if($shop_id){ 
			$map_tuijian_c['shop_id'] = $shop_id;
		}
		if($modle_id){
			$map_tuijian_c['modle_id'] = Array('in',$modle_id);;
		}
	    $map_tuijian_c['coupon_type'] = 2;
		$tuijian_coupon2 = $model_coupon->where($map_tuijian_c)->limit(5)->select();
		
		//不满5个用推荐的里去补充剩余的
		if( count($tuijian_coupon2) != '0' && count($tuijian_coupon2) != '5'){
			$tuijiancount_2 = 5 - count($tuijian_coupon2);
			unset($map_tuijian_c['shop_id']);
			unset($map_tuijian_c['modle_id']);
			$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
			$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);
			$sort = implode(",",$tuijian_coupon_ids);
			$arr_tuijian_2 = $model_coupon->where($map_tuijian_c)->order("field (id,{$sort})")->limit($tuijiancount_2)->select();

			$tuijian_coupon2 = array_merge($arr_tuijian_2,$tuijian_coupon2);
		}

		//一个没有用推荐5个来补充
		if(count($tuijian_coupon2) == 0){
			unset($map_tuijian_c['shop_id']);
			unset($map_tuijian_c['modle_id']);
			$tuijian_coupon_ids = C('TUIJIAN_COUPON2');
			$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);
			$sort = implode(",",$tuijian_coupon_ids);
			$tuijian_coupon2 = $model_coupon->where($map_tuijian_c)->order("field (id,{$sort})")->limit(5)->select();
		}
	    $this->assign('tuijian_coupon2',$tuijian_coupon2);
		
	}

	

	
    function get_relation_article($fsid){
	    //相关文章
	    $model_article = D('Article');
	    $map_a['fsid'] = $fsid;
	    $map_a['status'] = 1;
	    $article = $model_article->where($map_a)->order("id DESC")->limit(10)->select();
	    $this->assign('relation_article',$article);
	}
	
	/*
		@author:chf
		@function:得到搜索页面(优惠卷团|购卷推荐文章|相关顾问|等信息)
		@time:2013-03-21
	*/
    function get_ordermenu(){
	    $model_article = D('Article');
		
		$map_a['city_name'] = array(array('eq',$_SESSION['area_info'][0]),array('eq','全部'),'or');
        $map_a['status'] = 1;
        $article = $model_article->field('id,title,create_time')->where($map_a)->order("create_time DESC")->limit(12)->select();

        $this->assign('article',$article);
        $this->get_tuijian_coupon();
	
	}
	
	function get_expert_tuijian(){
	    $model_user = D('User');
	     //推荐专家列表
        $expert_ids = array(86,2,3);//专家ID
        $map_tuijianuser['uid'] = array('in',implode(',',$expert_ids));
        $map_tuijianuser['expert'] = 1;
        $expert_user_tuijian = $model_user->where($map_tuijianuser)->limit(4)->select();
        $this->assign('expert_tuijian',$expert_user_tuijian);
	}
    public function GetDistance($lat1, $lng1, $lat2, $lng2){  
        $EARTH_RADIUS = 6378.137;  
        $radLat1 = $this->rad($lat1);  
        //echo $radLat1;  
        $radLat2 = $this->rad($lat2);  
        $a = $radLat1 - $radLat2;  
        $b = $this->rad($lng1) - $this->rad($lng2);  
        $s = 2 * asin(sqrt(pow(sin($a/2),2) +  
        cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
        $s = $s *$EARTH_RADIUS;  
        $s = round($s * 10000) / 10; 
        return $s;  
    }
    function rad($d)  
    {  
        return $d * 3.1415926535898 / 180.0;  
    }


	/*
		@author:chf
		@function:得到客户端真实IP
		@time:2013-04-25
	*/
	function GetIP(){ 
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
			$ip = getenv("HTTP_CLIENT_IP"); 
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
			$ip = getenv("REMOTE_ADDR"); 
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
			$ip = $_SERVER['REMOTE_ADDR']; 
		else 
			$ip = "unknown"; 
		return($ip); 
	}
//公共模块--end
	/*
		@author:chf
		@function:接变量转换INT类型
		@time:2013-05-31
	*/
	function request_int($index = NULL, $xss_clean = FALSE)
	{
		// Check if a field has been provided
		if ($index === NULL AND ! empty($_POST))
		{
			$post = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_REQUEST) as $key)
			{
				$post[$key] = $this->_fetch_from_array_int($_REQUEST, $key, $xss_clean);
			}
			return $post;
		}
		
		return $this->_fetch_from_array_int($_REQUEST, $index, $xss_clean);
	}
	/*
		@author:chf
		@function:接变量转换INT类型
		@time:2013-05-31
	*/
	function _fetch_from_array_int(&$array, $index = '', $xss_clean = FALSE)
	{
		if ( ! isset($array[$index]))
		{
			return FALSE;
		}
		$array[$index] = htmlspecialchars($array[$index]);//转换html标签
		$array[$index] = addslashes($array[$index]);
		$array[$index] = intval($array[$index]);
		if ($xss_clean === TRUE)
		{
			return $this->security->xss_clean($array[$index]);
		}

		return $array[$index];
	}

	/*
		@author:chf
		@function:过滤变量
		@time:2013-05-31
	*/

	function request($index = NULL, $xss_clean = FALSE)
	{
		// Check if a field has been provided
		if ($index === NULL AND ! empty($_POST))
		{
			$post = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_REQUEST) as $key)
			{
				$post[$key] = $this->_fetch_from_array($_REQUEST, $key, $xss_clean);
			}
			return $post;
		}
		
		return $this->_fetch_from_array($_REQUEST, $index, $xss_clean);
	}
	/*
		@author:chf
		@function:过滤变量
		@time:2013-05-31
	*/
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
	{
		if ( ! isset($array[$index]))
		{
			return FALSE;
		}
		$array[$index] = htmlspecialchars($array[$index]);//转换html标签
		$array[$index] = addslashes($array[$index]);

		if ($xss_clean === TRUE)
		{
			return $this->security->xss_clean($array[$index]);
		}

		return $array[$index];
	}
	/*
		@author:chf
		@function:过滤变量
		@time:2013-05-31
	*/
	public function xss_clean($str, $is_image = FALSE)
	{
		/*
		 * Is the string an array?
		 *
		 */
		if (is_array($str))
		{
			while (list($key) = each($str))
			{
				$str[$key] = $this->xss_clean($str[$key]);
			}
			return $str;
		}
	}

	/*
		@author:ysh
		@function:得到汽车信息
		@time:2013/5/24
	*/
	function get_car_info($brand_id,$series_id,$model_id,$type='0') {
		if ($brand_id){
			$model_carbrand = D('Carbrand');
			$map_b['brand_id'] = $brand_id;
			$brand = $model_carbrand->where($map_b)->find();
			
		}
		if ($series_id){
			$model_carseries = D('Carseries');
			$map_s['series_id'] = $series_id;
			$series = $model_carseries->where($map_s)->find();
		}
		if ($model_id){
			$model_carmodel = D('Carmodel');
			$map_m['model_id'] = $model_id;
			$model = $model_carmodel->where($map_m)->find();
		}
		if($type){
			return $series['series_name']." ".$model['model_name'];
		}else{
			return $brand['brand_name']." ".$series['series_name']." ".$model['model_name'];
		}
	}


	
	/*
	*	小羊修改版
	*
	*/
	// URL组装 支持不同模式和路由
	function URL($url, $params=array(), $redirect=false, $suffix=true) {
		
		if (0 === strpos($url, '/'))
			$url = substr($url, 1);
		if (!strpos($url, '://')) // 没有指定项目名 使用当前项目名
			$url = APP_NAME . '://' . $url;
		if (stripos($url, '@?')) { // 给路由传递参数
			$url = str_replace('@?', '@think?', $url);
		} elseif (stripos($url, '@')) { // 没有参数的路由
			$url = $url . MODULE_NAME;
		}
		// 分析URL地址
		$array = parse_url($url);
		$app = isset($array['scheme']) ? $array['scheme'] : APP_NAME;
		$route = isset($array['user']) ? $array['user'] : '';
		if (defined('GROUP_NAME') && strcasecmp(GROUP_NAME, C('DEFAULT_GROUP')))
			$group = GROUP_NAME;
		if (isset($array['path'])) {
			$action = substr($array['path'], 1);
			if (!isset($array['host'])) {
				// 没有指定模块名
				$module = MODULE_NAME;
			} else {// 指定模块
				if (strpos($array['host'], '-')) {
					list($group, $module) = explode('-', $array['host']);
				} else {
					$module = $array['host'];
				}
			}
		} else { // 只指定操作
			$module = MODULE_NAME;
			$action = $array['host'];
		}
		if (isset($array['query'])) {
			parse_str($array['query'], $query);
			$params = array_merge($query, $params);
		}
		//对二级域名的支持,待完善对*号子域名的支持
		if (C('APP_SUB_DOMAIN_DEPLOY')) {
			foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
				if (in_array($group . "/", $rule))
					$flag = true;
				if (in_array($group . "/" . $module, $rule)) {
					$flag = true;
					unset($module);
				}
				if (!isset($group) && in_array(GROUP_NAME . "/" . $module, $rule) && in_array($key,array(SUB_DOMAIN,"*")))
					unset($module);
				if ($flag) {
					unset($group);
					if ($key != SUB_DOMAIN && $key != "*") {
						$sub_domain = $key;
					}
					break;
				}
			}
		}

		if (C('URL_MODEL') > 0) {
			
			$depr = C('URL_MODEL') == 2 ? C('URL_PATHINFO_DEPR') : '/';
			$str = $depr;

			foreach ($params as $var => $val){
				if($val) {
					$str .= $var . $depr . $val . $depr;
				}
			}
			$str = substr($str, 0, -1);
			$group = isset($group) ? $group . '/' : '';
			$module = isset($module) ? $module . '/' : "";
			if (!empty($route)) {
				$url = str_replace(APP_NAME, $app, __APP__) . '/' . $group . $route . $str;
			} else {
				$url = str_replace(APP_NAME, $app, __APP__) . '/' . $group . $module . $action . $str;
			}
			if ($suffix && C('URL_HTML_SUFFIX'))
				$url .= C('URL_HTML_SUFFIX');
		}else {
			$params = http_build_query($params);
			$params = !empty($params) ? '&' . $params : '';
			if (isset($group)) {
				$url = str_replace(APP_NAME, $app, __APP__) . '?' . C('VAR_GROUP') . '=' . $group . '&' . C('VAR_MODULE') . '=' . $module . '&' . C('VAR_ACTION') . '=' . $action . $params;
			} else {
				$url = str_replace(APP_NAME, $app, __APP__) . '?' . C('VAR_MODULE') . '=' . $module . '&' . C('VAR_ACTION') . '=' . $action . $params;
			}
		}
		if (isset($sub_domain)) {
			$domain = str_replace(SUB_DOMAIN, $sub_domain, $_SERVER['HTTP_HOST']);
			$url = "http://" . $domain . $url;
		}
		if ($redirect)
			redirect($url);
		else
			return $url;
	}
	
	/*
	 * 微信模板消息接口
	 * bright
	 * 2014-10-10
	 */
	function send_weixin_template_msg($data){
		if (!$data['wx_id'] || !$data['template_id']) {
			return 'param error';
		}
		//模板id放到配置文件里面的
		$config = C('WEIXIN_TEMPLATE');
		$template_id = $config[$data['template_id']];
		//$template_id = 'QD8Bb2ohPwUTYmR9faJWdxTlWd96Qnzd5v0Ov5AmFFc';
		$title = $data['title']?:'';
		$key1 = $data['key1']?:'';
		$key2 = $data['key2']?:'';
		$key3 = $data['key3']?:'';
		$remark = $data['remark']?:'';
		//发送模板1的模板
		$msg = '{
			"touser":"'.$data['wx_id'].'",
			"template_id":"'.$template_id.'",
			"url":"'.$data['return_url'].'",
			"topcolor":"#000000",
			"data":{
			"first": {
			"value":"'.$title.'",
			"color":"#000000"
			},
			"keyword1":{
			"value":"'.$key1.'",
			"color":"#000000"
			},
			"keyword2":{
			"value":"'.$key2.'",
			"color":"#000000"
			},
			"keyword3":{
			"value":"'.$key3.'",
			"color":"#000000"
			},
			"remark":{
			"value":"'.$remark.'",
			"color":"#000000"
			}
			}
		}';
		
		$access_token = $this->getWeixinToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
		return $this->curl($url,$msg);
	}
	
	public function curl($url, $post = NULL,$host=NULL) {
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$cookieFile = NULL;
		$hCURL = curl_init();
		curl_setopt($hCURL, CURLOPT_URL, $url);
		curl_setopt($hCURL, CURLOPT_TIMEOUT, 30);
		curl_setopt($hCURL, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($hCURL, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($hCURL, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($hCURL, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($hCURL, CURLOPT_ENCODING, "gzip,deflate");
		curl_setopt($hCURL, CURLOPT_HTTPHEADER,$host);
		if ($post) {
			curl_setopt($hCURL, CURLOPT_POST, 1);
			curl_setopt($hCURL, CURLOPT_POSTFIELDS, $post);
		}
		$sContent = curl_exec($hCURL);
		if ($sContent === FALSE) {
			$error = curl_error($hCURL);
			curl_close($hCURL);
	
			throw new \Exception($error . ' Url : ' . $url);
		} else {
			curl_close($hCURL);
			return $sContent;
		}
	}
	
	/*
	*@name 微信发送客服消息接口
	*@author ysh
	*@time 2014/3/26
	*/
	function weixin_api($data) {
		//$access_token = $this->get_weixinkey();
		$access_token = $this->getWeixinToken();

		/*$memcache_access_token = S('WEIXIN_access_token');
		if($memcache_access_token) {
			$access_token = $memcache_access_token;
		}else {
			$appid = "wx43430f4b6f59ed33";
			$secret = "e5f5c13709aa0ae7dad85865768855d6";
		
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
			//$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
			echo $url;
			//ini_set('default_socket_timeout',15);   
			//$result = file_get_contents($url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT,15); 
			$result = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($result,true);
			$access_token = $result['access_token'];
			S('WEIXIN_access_token',$access_token,7200);
		}*/
		if($data['msgtype'] == 'text') {
			$body = array(
				"touser"=>$data['touser'],
				"msgtype"=>"text", 
				
			);
		}

		if($data['msgtype'] == 'voice') {
			$body = array(
				"touser"=>$data['touser'],
				"msgtype"=>"voice", 
				
			);
		}
		if($data['msgtype'] == 'image') {
			$body = array(
				"touser"=>$data['touser'],
				"msgtype"=>"image", 
				"image" => array("media_id" =>$data['media_id']),
			);
		}
		if($data['msgtype'] == ''){
			$body = array(
				"touser"=>$data['touser'],
				"msgtype"=>"news", 
				"news" => array(
					"articles" => array(
												array(
															"title"=>"%title%", 
															"description"=>"%description%",
															"url"=>"%url%"
														),
												)
											),
			);
		}

		$json_body = json_encode($body);
		$search = array('%title%' , '%description%', '%url%');
		$replace = array($data['title'],$data['description'],$data['url']);
		$json_body = str_replace($search , $replace , $json_body);


		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		//print_r($result);exit;
		//if($result['errcode'] != 0) {
			//S('WEIXIN_access_token',NUll);
			//$this->weixin_api($data);
		//}else {
			return $result;
		//}
	}

	/*
		*@name 得到微信的access_token
		*@author chf
		*@time 2014/3/26
	*/
	function get_weixinkey(){
		//$memcache_access_token = S('WEIXIN_access_token');
		//if($memcache_access_token) {
			//$access_token = $memcache_access_token;
		//}else {
			$appid = "wx43430f4b6f59ed33";
			$secret = "e5f5c13709aa0ae7dad85865768855d6";
		
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
			$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
			$result = file_get_contents($url,false,$strm);
			$result = json_decode($result,true);
			$access_token = $result['access_token'];
			//S('WEIXIN_access_token',$access_token,7200);
		//}
		if($access_token){
			return $access_token;
		}else{
			$this->get_weixinkey();
		}
	}

	/*
		@author:chf
		@function:微信上传下载媒体GET方法只取body数据
		@time:2014-05-08
	*/
	function downloadWeixinFile($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);    
		curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		curl_close($ch);
		$imageAll = array_merge(array('header' => $httpinfo), array('body' => $package)); 
		return $imageAll;
	}
 
	/*
		@author:chf
		@function:从微信拿来的语音转换为mp3文件目前格式试过用快播播放
		@time:2014-05-08
	*/
	function saveWeixinFile($filename, $filecontent)
	{
		$local_file = fopen($filename, 'w');
		if (false !== $local_file){
			if (false !== fwrite($local_file, $filecontent)) {
				fclose($local_file);
			}
		}
	}

	//根据券码换算金额
	function get_codevalue($replace_code){
		if (is_numeric($replace_code)){
			if($replace_code==0){$codevalue = 0;}else{
				//4位券码判定
				if(count(str_split($replace_code))==4){
					$codevalue = 20; 
				}else{
					$codevalue = 99; 
				}
			}
		}else{
            $model = D('carservicecode');
            $info = $model->where(array('coupon_code'=>$replace_code))->find();
			$first = substr($replace_code,0,1);
            if($info['source_type'] == '1'){
                switch ($first) {
                    case b:
                        $codevalue = 68;
                        break;

                    default:
                        $codevalue = 99;
                        break;
                }
            }else {
                switch ($first) {
                    case a:
                        $codevalue = 300;
                        break;
                    case b:
                        $codevalue = 20;
                        break;
                    case g:
                        $codevalue = 20;
                        break;
                    case f:
                        $codevalue = 99;
                        break;
                    case k:
                        $codevalue = 100;
                        break;
                    case l:
                        $codevalue = 99;
                        break;
                    case n:
                        $codevalue = 99;
                        break;
                    default:
                        $codevalue = 99;
                        break;
                }
            }
		}
		if(!$replace_code){
			$codevalue = 0;
		}
		//通用码016888
		if($replace_code=='016888'){
			$codevalue = 20;
		}
		return $codevalue;
	}
	

	//根据订单服务类型返回服务费价格
	function get_servicevalue($order_type){
		switch ($order_type) {
			case 1:
			$servicevalue = 99;
			break;
			case 2:
			$servicevalue = 0;
			break;
			case 3:
			$servicevalue = 99;
			break;
			default:
			$servicevalue = 99;
			break;
		}

		return $servicevalue;
	}

	//微信获取用户基本信息接口
	function get_weixininfo($open_id){
		/*$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$result = file_get_contents($url);
		$result = json_decode($result,true);*/
		$result['access_token'] = $this->getWeixinToken();

		//echo $access_token.'</br>';
		$host = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$result['access_token']}&openid={$open_id}&lang=zh_CN";
		$array = array();
		$json_body = json_encode($array);
		//print_r($json_body);
		//echo $host;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result2 = json_decode($output,true);
		$result2['access_token'] = $result['access_token'];
		return $result2;
	}

	//上传图片并发送给固定的open_id
	function upload_pic($filepath,$open_id){
		
		/*$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$result = file_get_contents($url);
		$result = json_decode($result,true);*/
		$result['access_token'] = $this->getWeixinToken();

		$type="image";
		//$filepath = "UPLOADS/test1.jpg";
		
		$filedata = array("media"=>"@".$filepath);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$result['access_token']}&type=image";
		
		$result2 = $this->curl($url,$filedata);
		$result2 = json_decode($result2,true);
		//print_r($result2);exit;
		//发图片消息给某个OPEN_ID
		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$result['access_token']}";
		$array = array(
			"touser"=>$open_id,
			"msgtype"=>"image",
			"image"=>array("media_id"=>$result2['media_id'])
		);
		/*$array = array(
			"touser"=>"oF49ruGwcpz1RtvYpG4xG_5gkoCo",
			"msgtype"=>"text",
			"content"=>"I LOVE YOU"
		);*/
		$json_body = json_encode($array);
		//print_r($json_body);
		//echo $host;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
	}

	//发送文本消息给指定微信用户
	function send_weixintext($data){
		/*$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$result = file_get_contents($url);
		$result = json_decode($result,true);*/
		$result['access_token'] = $this->getWeixinToken();

		//发图片消息给某个OPEN_ID
		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$result['access_token']}";
		$json_body = '{"touser":"'.$data['open_id'].'","msgtype":"text","text":{"content":"'.$data['content'].'"}';

		//$json_body = json_encode($array);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		return $result;

	}
	
	/*获取微信token
	 * auth bright
	 */
	function getWeixinToken(){
		$mToken = D('weixin_token');
		$res = $mToken->field('token')->where( array(
				'id'=>1
		) )->find();
		if ($res) {
			return $res['token'];
		}else{
			return null;
		}
	}
	
	/*更新微信token
	 * auth bright
	*/
	function updateWeixinToken(){
		$this->addCodeLog('updateToken', 'start');
		$success = false;
		$appid = "wx43430f4b6f59ed33";
		$secret = "e5f5c13709aa0ae7dad85865768855d6";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$result = $this->curl($url);
		if ($result) {
			$result = json_decode($result,true);
			$mToken = D('weixin_token');
			if (isset($result['access_token'])) {
				$data = array(
						'token' => $result['access_token'],
				);
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$result['access_token']}&type=jsapi";
				$result1 = $this->curl($url);
				
				if ($result1) {
					$result1 = json_decode($result1, true);
					if (isset($result1['ticket'])) {
						$data['ticket'] = $result1['ticket'];
					}
				}
				
				$success = $mToken->where( array(
						'id'=>1
				) )->save($data);
			}
		}
		if($success){
			$this->addCodeLog('updateToken', 'success');
		}else{
			$this->addCodeLog('updateToken', 'failure:'.@var_export($result, true));
		}
		$this->submitCodeLog('updateToken');
	}

	/*配件回收记日志
	 * auth wwy
	 */
	function get_itemback_log($data){
		$log = D('itemback_log');
		$id = $log->add($data);
		//if($_SESSION['authId']==1){
			//echo $log->getLastsql();exit;
		//}
		if ($id) {
			return $id;
		}else{
			return null;
		}
	}
	//订单类型
	function carserviceConf($type){
		switch ($type){
			case 1:
				$name = '保养订单';
				break;
			case 2:
				$name = '免费检测订单';
				break;
			case 3:
				$name = '淘宝99元保养订单';
				break;
			case 4:
				$name = '1元检测订单';
				break;
			case 5:
				$name = '更换配件订单（火花塞、雨刷、刹车片）';
				break;
			case 6:
				$name = '免99元服务费订单';
				break;
			case 7:
				$name = '黄喜力套餐';
				break;
			case 8:
				$name = '蓝喜力套餐';
				break;
			case 9:
				$name = '灰喜力套餐';
				break;
			case 10:
				$name = '金美孚套餐';
				break;
			case 11:
				$name = '爱代驾高端保养';
				break;
			case 12:
				$name = '爱代驾中端保养';
				break;
			case 13:
				$name = '好车况';
				break;
			case 14:
				$name = '好空气';
				break;
			case 15:
				$name = '好动力';
				break;
			case 16:
				$name = '保养服务+检测+养护';
				break;
			case 17:
				$name = '矿物质油保养套餐+检测+养护';
				break;
			case 18:
				$name = '半合成油保养套餐+检测+养护';
				break;
			case 19:
				$name = '全合成油保养套餐+检测+养护';
				break;
			case 20:
				$name = '平安保险微信';
				break;
			case 22:
				$name = '光大168套餐';
				break;
			case 23:
				$name = '光大268套餐';
				break;
			case 24:
				$name = '光大368套餐';
				break;
			case 25:
				$name = '浦发199套餐';
				break;
			case 26:
				$name = '浦发299套餐';
				break;
			case 27:
				$name = '浦发399套餐';
				break;
			case 28:
				$name = '全车检测38项(淘38)';
				break;
			case 29:
				$name = '细节养护7项(淘38)';
				break;
			case 30:
				$name = '更换空调滤工时(淘38)';
				break;
			case 31:
				$name = '更换雨刮工时(淘38)';
				break;
			case 32:
				$name = '小保养工时(淘38)';
				break;
			case 33:
				$name = '好空气套餐(奥迪宝马奔驰)';
				break;
			case 34:
				$name = '补配件免人工订单';
				break;
			case 35:
				$name = '好车况套餐:38项全车检测+7项细节养护';
				break;
			case 36:
				$name = '大众点评199套餐';
				break;
			case 37:
				$name = '大众点评299套餐';
				break;
			case 38:
				$name = '大众点评399套餐';
				break;
			case 39:
				$name = '安盛天平50元检测套餐(不含机油配件)';
				break;
			case 40:
				$name = '安盛天平168套餐';
				break;
			case 41:
				$name = '安盛天平268套餐';
				break;
			case 42:
				$name = '安盛天平368套餐';
				break;
			case 43:
				$name = '平安银行50元检测套餐(不含机油配件)';
				break;
			case 44:
				$name = '平安银行168/199套餐';
				break;
			case 45:
				$name = '平安银行268/299套餐';
				break;
			case 46:
				$name = '平安银行368/399套餐';
				break;
			case 47:
				$name = '防雾霾空调滤活动';
				break;
			case 48:
				$name = '防雾霾1元';
				break;
			case 49:
				$name = '防雾霾8元';
				break;
			case 50:
				$name = '好车况套餐（大众点评)';
				break;
			case 51:
				$name = '保养服务+检测+养护（大众点评）';
				break;
			case 52:
				$name = '好空气套餐（平安财险）';
				break;
			case 53:
				$name = '好动力套餐（后付费)';
				break;
			case 54:
				$name = '好空气套餐(奥迪宝马奔驰后付费)';
				break;
			case 55:
				$name = '发动机舱精洗套餐（淘）';
				break;
			case 56:
				$name = '黄壳199套餐（预付费)';
				break;
			case 57:
				$name = '蓝壳299套餐（预付费）';
				break;
			case 58:
				$name = '灰壳399套餐（预付费）';
				break;
			case 59:
				$name = '发动机机舱精洗套餐（预付费）';
				break;
			case 60:
				$name = '268大保养（预付费）';
				break;
			case 61:
				$name = '378大保养（预付费）';
				break;
			case 62:
				$name = '空调清洗套餐（后付费）';
				break;
			case 63:
				$name = '空调清洗套餐（免费）';
				break;
			case 64:
				$name = '好动力套餐（免费）';
				break;
			case 65:
				$name = '轮毂清洗套餐（预付费）';
				break;
			case 66:
				$name = '空调清洗（点评到家）';
				break;
			case 67:
				$name = '汽车检测和细节养护套餐（点评到家）';
				break;
			case 68:
				$name = '好空气套餐(点评到家)';
				break;
			case 69:
				$name = '保养人工费工时套餐（点评到家）';
				break;
			case 70:
				$name = '9.8细节养护与检测（微信）';
				break;
			default:
				$name = ''.$type;
				break;
		}
		return $name;
	}

    //判断是否属手机
    function is_mobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

}
?>