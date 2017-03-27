<?php

namespace Xiecheapp\Controller;

use Think\Controller;

class CommonController extends Controller {
	function _initialize() {
		$uid = $this->GetUserId();
		if ($uid){
			$mMember = $this->MemberModel = D('member');//用户表
			$info = $this->MemberModel->where(array('uid'=>$uid))->find();
			$username = $info['username'];
			if (!$username) {
				$username = $info['mobile'];
			}
			$_SESSION['username'] = $username;
			$this->assign('username',$username);
		}
		if( cookie('carHistory') ){
			$carHistory = cookie('carHistory');
			$carHistory = unserialize($carHistory[0]);
			//var_dump($carHistory);
			$this->assign('carHistory',$carHistory);
		}
		if ( session('carName') ) {
			$this->assign('carName',session('carName') );
		}elseif (  cookie('carName') ){
			$this->assign('carName',cookie('carName'));
		}
		else{
			$this->assign('choseCar',true);
		}
		if (!session('modelId') && cookie('modelId')) {
			session('modelId',cookie('modelId'));
		}
		
		//获取city_id
		$area = $this->getUrlAreaflag();
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


		if(!$_SESSION['city'] OR $_SESSION['city']==''){
			$_SESSION['city'] = 1;
			$this->assign('new_city_id',1);
		}else{
			$this->assign('new_city_id',$_SESSION['city']);
		}
		
		if($_SESSION['city']) {
            $city_model = D('city');
            $city_map['id'] = $_SESSION['city'];
            $city_info = $city_model->where($city_map)->find();
            $this->assign('new_city_name', $city_info['name']);
        }else{
			$this->assign('new_city_name','上海');
		}

		//var_dump($_SESSION['city']);

	}
	//获取app的uid和身份验证
	public function app_get_userid(){
		$request_headers = getallheaders();
		$uid = $request_headers['uid'];
		$session = $request_headers['session'];
		if($uid && $session){
			$app_user_session = D ( 'appusersession' );
			$app_user["session"] =$session;
			$app_user["uid"] =$uid;
			$use_info = $app_user_session->where($app_user)->find();
		}
		if ($use_info) {
			return $use_info["uid"];
		}else{
			$this->_rett(null,0,"登陆认证失败");exit;
		}
	}
	//公共返回方法
	function _rett($data=NULL, $status = 1, $msg='success'){
		$ret = array(
			'status' => $status,
			'msg'	=> $msg,
			'data'	=> $data
		);
		echo json_encode($ret);//特殊要返回放括号数组的情况
	}
	
	// 代码日志跟踪
	function addCodeLog($type, $msg) {
		if (! $type) {
			return false;
		}
		$mCodeLog = D ( 'logcode' );
		$mErrorLog = D ( 'logerror' );
		if (isset ( $_SESSION ['codeLogType'] )) {
			if ($_SESSION ['codeLogType'] == $type) {
				if (! empty ( $_SESSION ['codeLogId'] )) {
					$where = array (
							'id' => $_SESSION ['codeLogId'] 
					);
					$res = $mCodeLog->field ( 'log' )->where ( $where )->find ();
					$msg = $res ['log'] . '||' . $msg;
					$data = array (
							'type' => $type,
							'log' => $msg 
					);
					$mCodeLog->where ( $where )->save ( $data );
				} else {
					$data = array (
							'type' => $type,
							'log' => 'codeLogId empty: ' . $msg,
							'insertTime' => time () 
					);
					$mErrorLog->add ( $data );
				}
			} else {
				$data = array (
						'type' => $type,
						'log' => 'type error: ' . $msg,
						'insertTime' => time () 
				);
				$mErrorLog->add ( $data );
			}
		} else {
			// 首次
			$_SESSION ['codeLogType'] = $type;
			$data = array (
					'type' => $type,
					'log' => $msg,
					'insertTime' => time () 
			);
			
			$codeId = $mCodeLog->add ( $data );
			if ($codeId) {
				$_SESSION ['codeLogId'] = $codeId;
			}
		}
		return true;
	}
	// 代码日志跟踪提交
	function submitCodeLog($type) {
		unset ( $_SESSION ['codeLogId'], $_SESSION ['codeLogType'] );
		return true;
	}
	function getUrlAreaflag() {
		$areaflag = C ( 'AREAFLAG' );
		$url = $_SERVER ['SERVER_NAME'];
		$area = explode ( ".", $url );
		return array (
				$areaflag [$area [0]],
				$area [0] 
		);
	}
	
	/*
	 * @author:chf @function:发送数据(平安)($url:http://www.xieche.com.cn/test/ur)($data: $data['img']="测试平安";) @time:2014-01-16;
	 */
	function Api_toPA($url, $data) {
		$host = $url;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_URL, $host );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
	}
	
	/*
	 * @author:chf @function:得到城市对应ID @time:2013-03-21
	 */
	public function GetCityId($CityName) {
		switch ($CityName) {
			case '北京' :
				$CityId = 2912;
				return $CityId;
			case '上海' :
				$CityId = 3306;
				return $CityId;
			case '广州' :
				$CityId = 2918;
				return $CityId;
		}
	}
	
	/*
	 * @author:chf @function:得到区域 @time:2013-03-22
	 */
	function GetArea() {
		$area = $_SESSION ['area_info'];
		if ($area [0]) {
			$map_s ['shop_city'] = $this->GetCityId ( $area [0] );
			if ($area [1] == 'c' || $area [1] == 'www' || $area [1] == 'x' || $area [1] == 'shanghai') {
				$area [1] = 'sh';
			}
			$this->assign ( 'sessarea', $area [1] );
		}
		$Carea = c ( 'AREAS' );
		$this->assign ( 'Carea', $Carea );
	}
	public function index() {
		// 列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
	 * +----------------------------------------------------------
	 * 
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}
	
	/**
	 * +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * +----------------------------------------------------------
	 * 
	 * @access protected
	 *         +----------------------------------------------------------
	 * @param string $name
	 *        	数据对象名称
	 *        	+----------------------------------------------------------
	 * @return HashMap +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		// 生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName ();
		}
		$name = $this->getActionName ();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * +----------------------------------------------------------
	 * 
	 * @access protected
	 *         +----------------------------------------------------------
	 * @param Model $model
	 *        	数据对象
	 * @param HashMap $map
	 *        	过滤条件
	 * @param string $sortBy
	 *        	排序
	 * @param boolean $asc
	 *        	是否正序
	 *        	+----------------------------------------------------------
	 * @return void +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		// 排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		// 排序方式默认按照倒序排列
		// 接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		// 取得满足条件的记录数
		$_pk = $model->getPk ();
		$count = $model->where ( $map )->count ( $_pk );
		if ($count > 0) {
			import ( "Org.Util.Page" );
			// 创建分页对象
			// dump($_REQUEST);
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, 10 );
			// 分页查询数据
			/* @author:chf 咨询按照时间倒叙排列条件 */
			if ($map ['ArticleType'] == 'yes') {
				$order = 'create_time';
			}
			unset ( $map ['ArticleType'] );
			/* @author:chf 咨询按照时间倒叙排列条件 */
			
			$voList = $model->where ( $map )->order ( "`" . $order . "` " . $sort )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			// echo $model->getlastsql();
			// 分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val ) && $val != "") {
					$p->parameter .= $key . "/" . urlencode ( $val ) . "/";
				} else {
					$p->parameter .= $key . "/" . urlencode ( $val [1] ) . "/";
				}
			}
			// 分页显示
			$page = $p->show ();
			// 列表排序显示
			$sortImg = $sort; // 排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; // 排序提示
			$sort = $sort == 'desc' ? 1 : 0; // 排序方式
			                                 // 模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "allcount", $count );
		}
		cookie ( '_currentUrl_', __SELF__ );
		return $voList;
	}
	function insert($name = '') {
		// B('FilterString');
		if (! $name) {
			$name = $this->getActionName ();
		}
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 保存当前数据对象
		$list = $model->add ();
		if ($list !== false) { // 保存成功
			$this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
			$this->success ( '新增成功!' );
		} else {
			// 失败提示
			$this->error ( '新增失败!' );
		}
	}
	public function add() {
		$this->display ();
	}
	function read() {
		$this->edit ();
	}
	function edit() {
		$name = $this->getActionName ();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$p_key = $model->getPk ();
		$p_key = 'getBy' . $p_key;
		$vo = $model->$p_key ( $id );
		if (isset ( $vo ['car_number'] ) and ! empty ( $vo ['car_number'] )) {
			$car_number_arr = explode ( '_', $vo ['car_number'] );
			if (isset ( $car_number_arr [1] )) {
				$vo ['s_pro'] = $car_number_arr [0];
				$vo ['car_number'] = $car_number_arr [1];
			} else {
				$vo ['s_pro'] = '';
				$vo ['car_number'] = $car_number_arr [0];
			}
		}
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	function update() {
		// B('FilterString');
		$name = $this->getActionName ();
		$model = D ( $name );
		// dump($_POST);
		// dump($model);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list = $model->save ();
		if (false !== $list) {
			// 成功提示
			$this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
			$this->success ( '编辑成功!' );
		} else {
			// 错误提示
			$this->error ( '编辑失败!' );
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 默认删除操作
	 * +----------------------------------------------------------
	 * 
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function delete() {
		// 删除指定记录
		$name = $this->getActionName ();
		$model = M ( $name );
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array (
						$pk => array (
								'in',
								explode ( ',', $id ) 
						) 
				);
				$list = $model->where ( $condition )->setField ( 'status', - 1 );
				if ($list !== false) {
					$this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
					$this->success ( '删除成功！' );
				} else {
					$this->error ( '删除失败！' );
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function foreverdelete() {
		// 删除指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array (
						$pk => array (
								'in',
								explode ( ',', $id ) 
						) 
				);
				if (false !== $model->where ( $condition )->delete ()) {
					// echo $model->getlastsql();
					$this->success ( '删除成功！' );
				} else {
					$this->error ( '删除失败！' );
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}
	public function clear() {
		// 删除指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			if (false !== $model->where ( 'status=1' )->delete ()) {
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( '_DELETE_SUCCESS_' ) );
			} else {
				$this->error ( L ( '_DELETE_FAIL_' ) );
			}
		}
		$this->forward ();
	}
	
	/**
	 * +----------------------------------------------------------
	 * 默认禁用操作
	 *
	 * +----------------------------------------------------------
	 * 
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 * @throws FcsException +----------------------------------------------------------
	 */
	public function forbid() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array (
				$pk => array (
						'in',
						$id 
				) 
		);
		$list = $model->forbid ( $condition );
		if ($list !== false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error ( '状态禁用失败！' );
		}
	}
	public function checkPass() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array (
				$pk => array (
						'in',
						$id 
				) 
		);
		if (false !== $model->checkPass ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态批准成功！' );
		} else {
			$this->error ( '状态批准失败！' );
		}
	}
	public function recycle() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array (
				$pk => array (
						'in',
						$id 
				) 
		);
		if (false !== $model->recycle ( $condition )) {
			
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态还原成功！' );
		} else {
			$this->error ( '状态还原失败！' );
		}
	}
	public function recycleBin() {
		$map = $this->_search ();
		$map ['status'] = - 1;
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	/**
	 * +----------------------------------------------------------
	 * 默认恢复操作
	 *
	 * +----------------------------------------------------------
	 * 
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 * @throws FcsException +----------------------------------------------------------
	 */
	function resume() {
		// 恢复指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array (
				$pk => array (
						'in',
						$id 
				) 
		);
		if (false !== $model->resume ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
		}
	}
	function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			// 更新数据对象
			$name = $this->getActionName ();
			$model = D ( $name );
			$col = explode ( ',', $seqNoList );
			// 启动事务
			$model->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$model->id = $val [0];
				$model->sort = $val [1];
				$result = $model->save ();
				if (! $result) {
					break;
				}
			}
			// 提交事务
			$model->commit ();
			if ($result !== false) {
				// 采用普通方式跳转刷新页面
				$this->success ( '更新成功' );
			} else {
				$this->error ( $model->getError () );
			}
		}
	}
	
	// 用户登录页面
	public function login() {
		if (! isset ( $_SESSION ['uid'] ) || ! isset ( $_SESSION ['username'] )) {
			if (isset ( $_REQUEST ['tolken'] )) {
				$tolken = $_REQUEST ['tolken'];
				$model_member = D ( 'Member' );
				$membermap ['tolken'] = $tolken;
				$membermap ['tolken_time'] = array (
						'gt',
						time () 
				);
				$member = $model_member->where ( $membermap )->find ();
				if (! $member) {
					$xml_content = "<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
					$xml_content .= "<status>1</status><tolken>" . $tolken . "</tolken><desc>账号未登陆</desc>";
					$xml_content .= "</XML>";
					echo $xml_content;
					exit ();
				} else {
					return true;
				}
			}
			$cookie_uid = cookie ( 'uid' );
			$cookie_username = cookie ( 'username' );
			$cookie_x_id = cookie ( 'x_id' );
			if ($cookie_uid && $cookie_username && $cookie_x_id) {
				$Xsession = D ( 'Xsession' );
				$res = $Xsession->where ( "uid='$cookie_uid' AND username='$cookie_username' AND x_id='$cookie_x_id'" )->find ();
				if ($res) {
					$_SESSION ['uid'] = $res ['uid'];
					$_SESSION ['username'] = $res ['username'];
					return TRUE;
				} else {
					redirect ( "/Public/login?jumpUrl=" . urlencode ( $_SERVER ['REQUEST_URI'] ) );
				}
			} else {
				if (isset ( $_SESSION ['uid'] )) {
					if (isset ( $_SESSION ['r_data'] )) {
						$r_data = $_SESSION ['r_data'];
						$r_data ['brand_id'] = 0;
						$r_data ['series_id'] = 0;
						$r_data ['model_id'] = 0;
						if ($_SESSION ['r_data'] ['brand_id']) {
							$r_data ['brand_id'] = $_SESSION ['r_data'] ['brand_id'];
						}
						if ($_SESSION ['r_data'] ['series_id']) {
							$r_data ['series_id'] = $_SESSION ['r_data'] ['series_id'];
						}
						if ($_SESSION ['r_data'] ['model_id']) {
							$r_data ['model_id'] = $_SESSION ['r_data'] ['model_id'];
						}
					} else {
						$r_data ['brand_id'] = 0;
						$r_data ['series_id'] = 0;
						$r_data ['model_id'] = 0;
					}
					unset ( $_SESSION ['r_data'] );
					return true;
					// 暂时取消掉 TODO::暂时取消掉
					// $this->assign('r_data',$r_data);
					// $this->assign('uid',$_SESSION['uid']);
					// cookie('_currentUrl_', __SELF__);
					// $this->display('Member:complete_member');
				} else {
					redirect ( "/Public/login?jumpUrl=" . urlencode ( $_SERVER ['REQUEST_URI'] ) );
				}
			}
		} else {
			return TRUE;
		}
	}
	
	// 验证码
	public function verify() {
		$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : 'gif';
		$width = isset ( $_GET ['width'] ) ? $_GET ['width'] : '100';
		$height = isset ( $_GET ['height'] ) ? $_GET ['height'] : '50';
		import ( "Org.Util.Image" );
		\Image::buildImageVerify ( 4, 1, $type, $width, $height );
	}
	public function action_tip($list) {
		if ($list !== false) { // 保存成功
			$this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
			$this->success ( '成功!' );
		} else {
			// 失败提示
			$this->error ( '失败!' );
		}
	}
	// 返回用户ID
	public function GetUserId() {
		return isset ( $_SESSION ['uid'] ) ? $_SESSION ['uid'] : 0;
	}
	// 返回用户ID
	public function appGetUserId() {
		if (isset ( $_GET ['tolken'] )) {
			$tolken = $_GET ['tolken'];
			$model_member = D ( "Member" );
			$member_map ['tolken'] = $tolken;
			$member_map ['tolken_time'] = array (
					'gt',
					time () 
			);
			$member = $model_member->where ( $member_map )->find ();
			if ($member) {
				return $member ['uid'];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	
	// 生成用户显示的订单号
	public function get_orderid($id) {
		$orderid = $id + 987;
		$sum = 0;
		for($ii = 0; $ii < strlen ( $orderid ); $ii ++) {
			$orderid = ( string ) $orderid;
			$sum += intval ( $orderid [$ii] );
		}
		$str = $sum % 10;
		return $orderid . $str;
	}
	// 还原订单号
	public function get_true_orderid($id) {
		$orderid = substr ( trim ( $id ), 0, - 1 );
		$orderid = $orderid - 987;
		return $orderid;
	}
	// 生成表格图片
	public function createtableimg($tables_arr, $folder, $img_name) {
		// $img_name = 'test33.png';
		import ( "Org.Util.Image" );
		$img_path = 'UPLOADS/Product/' . $folder;
		mk_dir ( $img_path );
		$img_name = $img_path . '/' . $img_name;
		\Image::getimgtable ( $tables_arr, $img_name );
	}
	
	/*
	 * @author:chf @function:得到推荐优惠套餐券信息 @time:2014-05-05
	 */
	function get_tuijian_coupon($shop_id='',$model_id='', $limit = 4,$model_name = false){
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
		$fsid_str = '';
		if($model_id){
			$model_carmodel = D('Carmodel');
			$map_mo['model_id'] = $model_id;
			$model = $model_carmodel->where($map_mo)->find();
			if ($model) {
				$map_model_id = array("like","%{$model_id}%");
		
				$model_series = D('Carseries');
				$map_series_id = array("like","%{$model['series_id']}%");
				$map_se['series_id'] = $model['series_id'];
				$series = $model_series->where($map_se)->find();
				if ($series) {
					if ($series['brand_id']) {
						$map_brand_id = $series['brand_id'];
						$map_other_brand = array("like","%{$series['brand_id']},%");
					}
					$fsid_arr[] = $series['fsid'];
					$fsid_arr = array_unique($fsid_arr);
					$fsid_str = implode(',',$fsid_arr);
				}
			}
		}
		//var_dump($map_tuijian_c);
		if ($fsid_str) {
			$map_tuijian_c['fsid'] = array('in',$fsid_str); //现金卷用fsid查询
		}
		$tuijian_coupon1 = $model_coupon->where($map_tuijian_c)->limit($limit)->select();
		// 			$this->addCodeLog('couponlog', $model_coupon->getLastSql());
		$this->assign('tuijian_coupon1',$tuijian_coupon1);

	    //套餐券推荐本品牌
		unset($map_tuijian_c['id']);
		unset($map_tuijian_c['fsid']);
		
		if (isset($map_model_id)) {
			$map_tuijian_c['model_id'] = $map_model_id;
		}
		if (isset($map_series_id)) {
			$map_tuijian_c['series_id'] = $map_series_id;
		}
		if( isset($map_brand_id) ){
			$map_tuijian_c['brand_id'] = $map_brand_id;
			$map_tuijian_c['coupon_type'] = 2;
			$tuijian_coupon2 = $model_coupon->where($map_tuijian_c)->limit($limit)->select();
// 			$this->addCodeLog('couponlog', $model_coupon->getLastSql());
			$this->assign('tuijian_coupon2',$tuijian_coupon2);
			
			//跨品牌
			unset($map_tuijian_c['model_id']);
			unset($map_tuijian_c['series_id']);
			unset($map_tuijian_c['id']);
			$map_tuijian_c['brand_id'] = $map_other_brand;
			$map_tuijian_c['coupon_across'] = 1;
			$tuijian_coupon3 = $model_coupon->where ( $map_tuijian_c )->limit ( $limit )->select ();
// 			$this->addCodeLog('couponlog', $model_coupon->getLastSql());
// 			$this->submitCodeLog('couponlog');
			$this->assign('tuijian_coupon3',$tuijian_coupon3);
		}
		
	}
	
	function get_tuijian_coupon2($shop_id = '', $model_id = '', $limit = 5,$model_name = false) {
		$model_coupon = D ( 'Coupon' );
// 		if ($model_id) {
// 			$map_tuijian_c ['model_id'] = array (
// 					'in',$model_id
// 			);
// 		}
		$map_tuijian_c ['is_delete'] = 0;
		$map_tuijian_c ['show_s_time'] = array (
				'lt',
				time () 
		);
		$map_tuijian_c ['show_e_time'] = array (
				'gt',
				time () 
		);
		
		if ($shop_id) {
			$map_tuijian_c ['shop_id'] = $shop_id;
		}
		
		// 跨品牌
		$map_tuijian_c ['coupon_type'] = 2;
		$tuijian_coupon3 = $model_coupon->where ( $map_tuijian_c )->order('rand()')->limit ( $limit )->select ();
		
		$this->assign ( 'tuijian_coupon3', $tuijian_coupon3 );
		
		unset ( $map_tuijian_c ['shop_id'] );
		unset ( $map_tuijian_c ['model_id'] );
		
		// 现金券推荐
		$map_tuijian_c ['coupon_type'] = 1;
		if ($shop_id) {
			$map_tuijian_c ['shop_id'] = $shop_id;
			
		}
		if ($model_id) {
			$tuijian_coupon_ids = C('TUIJIAN_COUPON1');
			$map_tuijian_c['id'] = array('in',$tuijian_coupon_ids);;
		}
		if ($model_name) {
			$carName = cookie('brandName');
			if ($carName) {
				$map_tuijian_c ['coupon_name'] = array (
						"like","%$carName%"
				);
			}
		}
		
		$tuijian_coupon1 = $model_coupon->where ( $map_tuijian_c )->limit ( $limit )->select ();
		
		if (count ( $tuijian_coupon1 ) != '0' && count ( $tuijian_coupon1 ) != $limit) {
			$tuijiancount_1 = $limit - count ( $tuijian_coupon1 );
			unset ( $map_tuijian_c ['shop_id'] );
			unset ( $map_tuijian_c ['model_id'] );
			$tuijian_coupon_ids = C ( 'TUIJIAN_COUPON1' );
			$map_tuijian_c ['id'] = array (
					'in',
					$tuijian_coupon_ids 
			);
			$sort = implode ( ",", $tuijian_coupon_ids );
			$arr_tuijian_1 = $model_coupon->where ( $map_tuijian_c )->order ( "field (id,{$sort})" )->limit ( $tuijiancount_1 )->select ();
			$tuijian_coupon1 = array_merge ( $tuijian_coupon1, $arr_tuijian_1 );
		}
		
		if (count ( $tuijian_coupon1 ) == '0') {
			unset ( $map_tuijian_c ['shop_id'] );
			unset ( $map_tuijian_c ['model_id'] );
			$tuijian_coupon_ids = C ( 'TUIJIAN_COUPON1' );
			$map_tuijian_c ['id'] = array (
					'in',
					$tuijian_coupon_ids 
			);
			$sort = implode ( ",", $tuijian_coupon_ids );
			$tuijian_coupon1 = $model_coupon->where ( $map_tuijian_c )->order ( "field (id,{$sort})" )->limit ( $limit )->select ();
		}
		$this->assign ( 'tuijian_coupon1', $tuijian_coupon1 );
		// 现金券推荐结束
		
		unset ( $map_tuijian_c ['id'] );
		//本品牌
		$map_tuijian_c ['coupon_type'] = 2;
		if ($model_id) {

			$tuijian_coupon2 = $model_coupon->where ( $map_tuijian_c )->limit ( $limit )->select (); // 本品牌
			// var_dump($model_coupon->getLastSql());
			// 不满5个用推荐的里去补充剩余的
			if (count ( $tuijian_coupon2 ) != '0' && count ( $tuijian_coupon2 ) != $limit) {
				$tuijiancount_2 = $limit - count ( $tuijian_coupon2 );
				unset ( $map_tuijian_c ['shop_id'] );
				unset ( $map_tuijian_c ['model_id'] );
				$tuijian_coupon_ids = C ( 'TUIJIAN_COUPON2' );
				$map_tuijian_c ['id'] = array (
						'in',
						$tuijian_coupon_ids 
				);
				$sort = implode ( ",", $tuijian_coupon_ids );
				$arr_tuijian_2 = $model_coupon->where ( $map_tuijian_c )->order ( "field (id,{$sort})" )->limit ( $tuijiancount_2 )->select ();
				
				$tuijian_coupon2 = array_merge ( $arr_tuijian_2, $tuijian_coupon2 );
			}
			
			// 一个没有用推荐5个来补充
			if (count ( $tuijian_coupon2 ) == 0) {
				unset ( $map_tuijian_c ['shop_id'] );
				unset ( $map_tuijian_c ['model_id'] );
				$tuijian_coupon_ids = C ( 'TUIJIAN_COUPON2' );
				$map_tuijian_c ['id'] = array (
						'in',
						$tuijian_coupon_ids 
				);
				$sort = implode ( ",", $tuijian_coupon_ids );
				$tuijian_coupon2 = $model_coupon->where ( $map_tuijian_c )->order ( "field (id,{$sort})" )->limit ( $limit )->select ();
			}
			$this->assign ( 'tuijian_coupon2', $tuijian_coupon2 );
		}
	}
	function get_relation_article($fsid) {
		// 相关文章
		$model_article = D ( 'Article' );
		$map_a ['fsid'] = $fsid;
		$map_a ['status'] = 1;
		$article = $model_article->where ( $map_a )->order ( "id DESC" )->limit ( 10 )->select ();
		$this->assign ( 'relation_article', $article );
	}
	
	/*
	 * @author:chf @function:得到搜索页面(优惠卷团|购卷推荐文章|相关顾问|等信息) @time:2013-03-21
	 */
	function get_ordermenu() {
		$model_article = D ( 'Article' );
		
		$map_a ['city_name'] = array (
				array (
						'eq',
						$_SESSION ['area_info'] [0] 
				),
				array (
						'eq',
						'全部' 
				),
				'or' 
		);
		$map_a ['status'] = 1;
		$article = $model_article->field ( 'id,title,create_time' )->where ( $map_a )->order ( "create_time DESC" )->limit ( 12 )->select ();
		// echo $model_article->getlastSql();
		// echo '<pre>';print_r($article);
		$this->assign ( 'article', $article );
		$this->get_tuijian_coupon ();
		/*
		 * $model_coupon = D('Coupon'); $map_c['is_delete'] = 0; $map_c['show_s_time'] = array('lt',time()); $map_c['show_e_time'] = array('gt',time()); $map_c['coupon_type'] = 1; $coupon1 = $model_coupon->field('id,coupon_name,cost_price,coupon_amount,pay_count,coupon_logo,coupon_summary')->where($map_c)->order("id DESC")->limit(3)->select(); $this->assign('coupon1',$coupon1); $map_c['coupon_type'] = 2; $coupon2 = $model_coupon->field('id,coupon_name,cost_price,coupon_amount,pay_count,coupon_logo,coupon_summary')->where($map_c)->order("id DESC")->limit(3)->select(); $this->assign('coupon2',$coupon2);
		 */
	}
	function get_expert_tuijian() {
		$model_user = D ( 'User' );
		// 推荐专家列表
		$expert_ids = array (
				86,
				2,
				3 
		); // 专家ID
		$map_tuijianuser ['uid'] = array (
				'in',
				implode ( ',', $expert_ids ) 
		);
		$map_tuijianuser ['expert'] = 1;
		$expert_user_tuijian = $model_user->where ( $map_tuijianuser )->limit ( 4 )->select ();
		$this->assign ( 'expert_tuijian', $expert_user_tuijian );
	}
	public function GetDistance($lat1, $lng1, $lat2, $lng2) {
		$EARTH_RADIUS = 6378.137;
		$radLat1 = $this->rad ( $lat1 );
		// echo $radLat1;
		$radLat2 = $this->rad ( $lat2 );
		$a = $radLat1 - $radLat2;
		$b = $this->rad ( $lng1 ) - $this->rad ( $lng2 );
		$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) * cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
		$s = $s * $EARTH_RADIUS;
		$s = round ( $s * 10000 ) / 10;
		return $s;
	}
	function rad($d) {
		return $d * 3.1415926535898 / 180.0;
	}
	
	//MD5加密
	function pwdHash($password, $type = 'md5') {
		return hash ( $type, $password );
	}
	function _valid($data,$msg){
		if (!$data) {
			$this->error($msg,'',true);
		}
	}
	function _error($msg){
		$this->error($msg,'',true);
	}	
	/*
	 * 普通上传
	 */
	// modify by yyc 2012/5/30
	// 修改默认为logo的错误
	public function upload($uploadName = 'logo') {
		if (! empty ( $_FILES [$uploadName] ['name'] )) {
			import ( "ORG.Net.UploadFile" );
			$upload = new UploadFile ();
			$upload = $this->_upload_init ( $upload );
			if (! $upload->upload ()) {
				$this->error ( $upload->getErrorMsg () );
			} else {
				$info = $upload->getUploadFileInfo ();
				$_POST [$uploadName] = $info [0] ['savename'];
			}
		}
	}
	
	/*
	 * 车辆照片上传
	 */
	// modify by ysh 2013/5/7
	// 修改批量上传
	public function car_upload($uploadName = 'logo') {
		if (! empty ( $_FILES [$uploadName] ['name'] )) {
			import ( "ORG.Net.UploadFile" );
			$upload = new UploadFile ();
			$upload = $this->_upload_init ( $upload );
			if (! $upload->upload ()) {
				$this->error ( $upload->getErrorMsg () );
			} else {
				$uploadList = $upload->getUploadFileInfo ();
				if ($uploadList) {
					foreach ( $upload_list as $key => $val ) {
					}
					if ($_FILES ['coupon_logo'] ['name']) {
						$_POST ['coupon_logo'] = $uploadList [0] ['savename'];
						if ($_FILES ['coupon_pic'] ['name']) {
							$_POST ['coupon_pic'] = $uploadList [1] ['savename'];
						}
					} else {
						if ($_FILES ['coupon_pic'] ['name']) {
							$_POST ['coupon_pic'] = $uploadList [0] ['savename'];
						}
						if ($_FILES ['logo'] ['name']) {
							$_POST ['logo'] = $uploadList [0] ['savename'];
						}
					}
				}
			}
		}
	}
	
	/*
	 * @author:ysh @function:得到汽车信息 @time:2013/5/24
	 */
	function get_car_info($brand_id, $series_id, $model_id, $type = '0') {
		if ($brand_id) {
			$model_carbrand = D ( 'Carbrand' );
			$map_b ['brand_id'] = $brand_id;
			$brand = $model_carbrand->where ( $map_b )->find ();
		}
		if ($series_id) {
			$model_carseries = D ( 'Carseries' );
			$map_s ['series_id'] = $series_id;
			$series = $model_carseries->where ( $map_s )->find ();
		}
		if ($model_id) {
			$model_carmodel = D ( 'Carmodel' );
			$map_m ['model_id'] = $model_id;
			$model = $model_carmodel->where ( $map_m )->find ();
		}
		if ($type) {
			return $series ['series_name'] . " " . $model ['model_name'];
		} else {
			return $brand ['brand_name'] . " " . $series ['series_name'] . " " . $model ['model_name'];
		}
	}
	
	// 公共模块--end
	
	/*
	 * @author:chf @function:接变量转换INT类型 @time:2013-05-31
	 */
	function request_int($index = NULL, $xss_clean = FALSE) {
		// Check if a field has been provided
		if ($index === NULL and ! empty ( $_POST )) {
			$post = array ();
			
			// Loop through the full _POST array and return it
			foreach ( array_keys ( $_REQUEST ) as $key ) {
				$post [$key] = $this->_fetch_from_array_int ( $_REQUEST, $key, $xss_clean );
			}
			return $post;
		}
		
		return $this->_fetch_from_array_int ( $_REQUEST, $index, $xss_clean );
	}
	/*
	 * @author:chf @function:接变量转换INT类型 @time:2013-05-31
	 */
	function _fetch_from_array_int(&$array, $index = '', $xss_clean = FALSE) {
		if (! isset ( $array [$index] )) {
			return FALSE;
		}
		$array [$index] = htmlspecialchars ( $array [$index] ); // 转换html标签
		$array [$index] = addslashes ( $array [$index] );
		$array [$index] = intval ( $array [$index] );
		if ($xss_clean === TRUE) {
			return $this->security->xss_clean ( $array [$index] );
		}
		
		return $array [$index];
	}
	
	/*
	 * @author:chf @function:过滤变量 @time:2013-05-31
	 */
	function request($index = NULL, $xss_clean = FALSE) {
		// Check if a field has been provided
		if ($index === NULL and ! empty ( $_POST )) {
			$post = array ();
			
			// Loop through the full _POST array and return it
			foreach ( array_keys ( $_REQUEST ) as $key ) {
				$post [$key] = $this->_fetch_from_array ( $_REQUEST, $key, $xss_clean );
			}
			return $post;
		}
		
		return $this->_fetch_from_array ( $_REQUEST, $index, $xss_clean );
	}
	/*
	 * @author:chf @function:过滤变量 @time:2013-05-31
	 */
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE) {
		if (! isset ( $array [$index] )) {
			return FALSE;
		}
		$array [$index] = htmlspecialchars ( $array [$index] ); // 转换html标签
		$array [$index] = addslashes ( $array [$index] );
		
		if ($xss_clean === TRUE) {
			return $this->security->xss_clean ( $array [$index] );
		}
		
		return $array [$index];
	}
	/*
	 * @author:chf @function:过滤变量 @time:2013-05-31
	 */
	public function xss_clean($str, $is_image = FALSE) {
		/*
		 * Is the string an array?
		 */
		if (is_array ( $str )) {
			while ( list ( $key ) = each ( $str ) ) {
				$str [$key] = $this->xss_clean ( $str [$key] );
			}
			return $str;
		}
	}
	
	//短信接口
	function curl_sms($post = '' , $charset = null, $num=1 ){
		$datamobile = array('130','131','132','155','156','186','185');
		$mobile = $post['phones'];
		$submobile = substr($post['phones'],0,3);
		$content = $post['content'] = str_replace("联通", "联_通", $post['content']);
		if(in_array($submobile,$datamobile)){
			$content = $post['content'] = $post['content']."  回复TD退订";
		}
		if($num==1){
			$post['content'] .= " 【携车网】";
			$client = new \SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
			$res = $client->__soapCall('SendMsg',array('parameters' => $param));
			$this->addCodeLog('测试回调', '返回值:'.var_export($res, true));
    		$this->submitCodeLog('测试回调');
		}elseif($num==2){
			//第三通道
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
			return $res;
		}else{
	
			/*
			 * 修改需求：验证码两个一起发
			* bright
			*/
			$post['content'] .= " 【携车网】";
			$client = new \SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post["phones"],"Msg"=>$post["content"],"Channel"=>"1");
			$client->__soapCall('SendMsg',array('parameters' => $param));
				
			//第三通道
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
			return $res;
		}
	}
	public function curl($url, $post = NULL, $host = NULL) {
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$cookieFile = NULL;
		$hCURL = curl_init ();
		curl_setopt ( $hCURL, CURLOPT_URL, $url );
		curl_setopt ( $hCURL, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $hCURL, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_USERAGENT, $userAgent );
		curl_setopt ( $hCURL, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt ( $hCURL, CURLOPT_AUTOREFERER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_ENCODING, "gzip,deflate" );
		curl_setopt ( $hCURL, CURLOPT_HTTPHEADER, $host );
		if ($post) {
			curl_setopt ( $hCURL, CURLOPT_POST, 1 );
			curl_setopt ( $hCURL, CURLOPT_POSTFIELDS, $post );
		}
		$sContent = curl_exec ( $hCURL );
		if ($sContent === FALSE) {
			$error = curl_error ( $hCURL );
			curl_close ( $hCURL );
			
			throw new \Exception ( $error . ' Url : ' . $url );
		} else {
			curl_close ( $hCURL );
			return $sContent;
		}
	}
	
	/*
	 * @name 微信发送客服消息接口 @author ysh @time 2014/3/26
	 */
	function weixin_api($data) {
		/*$memcache_access_token = S ( 'WEIXIN_access_token' );
		if ($memcache_access_token) {
			$access_token = $memcache_access_token;
		} else {
			$appid = "wx43430f4b6f59ed33";
			$secret = "e5f5c13709aa0ae7dad85865768855d6";
			
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
			$result = file_get_contents ( $url );
			$result = json_decode ( $result, true );
			$access_token = $result ['access_token'];
			S ( 'WEIXIN_access_token', $access_token, 7200 );
		}*/
		$access_token = $this->getWeixinToken();
		if ($data ['msgtype'] == 'text') {
			$body = array (
					"touser" => $data ['touser'],
					"msgtype" => "text" 
			)
			;
		}
		
		if ($data ['msgtype'] == 'voice') {
			$body = array (
					"touser" => $data ['touser'],
					"msgtype" => "voice" 
			)
			;
		}
		if ($data ['msgtype'] == 'image') {
			$body = array (
					"touser" => $data ['touser'],
					"msgtype" => "image",
					"image" => array (
							"media_id" => $data ['media_id'] 
					) 
			);
		}
		if ($data ['msgtype'] == '') {
			$body = array (
					"touser" => $data ['touser'],
					"msgtype" => "news",
					"news" => array (
							"articles" => array (
									array (
											"title" => "%title%",
											"description" => "%description%",
											"url" => "%url%" 
									) 
							) 
					) 
			);
		}
		
		$json_body = json_encode ( $body );
		$search = array (
				'%title%',
				'%description%',
				'%url%' 
		);
		$replace = array (
				$data ['title'],
				$data ['description'],
				$data ['url'] 
		);
		$json_body = str_replace ( $search, $replace, $json_body );
		
		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		// echo $host;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_URL, $host );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $json_body );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$result = json_decode ( $output, true );
		if ($result ['errcode'] != 0) {
			S ( 'WEIXIN_access_token', NUll );
			$this->weixin_api ( $data );
		} else {
			return $result;
		}
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
			$first = substr($replace_code,0,1);
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
				default:
					$codevalue = 99;
					break;
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
}