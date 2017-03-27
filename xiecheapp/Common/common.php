<?php

/* 根据用户UID获得用户头像地址 */

function get_avatar_dir($uid) {
    
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $avatar_dir = "tipask/data/avatar/" . $dir1 . '/' . $dir2 . '/' . $dir3 . "/small_" . $uid;

    if (file_exists($avatar_dir . ".jpg"))
        return SITE_URL . $avatar_dir . ".jpg";
    if (file_exists($avatar_dir . ".jepg"))
        return SITE_URL . $avatar_dir . ".jepg";
    if (file_exists($avatar_dir . ".gif"))
        return SITE_URL . $avatar_dir . ".gif";
    if (file_exists($avatar_dir . ".png"))
        return SITE_URL . $avatar_dir . ".png";
//显示系统默认头像
    return SITE_URL . 'css/default/avatar.gif';
}

function getLmName($id) {
	if (empty ( $id )) {
		return '';
	}
	$Group = D ( "Lm" );
	$list = $Group->where("id=$id")->getField ( 'id,lm' );
	$name = $list [$id];
	return $name;
}

function getCompanyName($id) {
	if (empty ( $id )) {
		return '';
	}
	$Group = D ( "Company" );
	$list = $Group->where("id=$id")->getField ( 'id,c_name1' );
	$name = $list [$id];
	return $name;
}

function getCompanyList() {
    $name = 'company';
    $model = M($name);
    if ($_SESSION['bind_account']>0) {
        $company_map = C('COMPANY_WHERE_CONDITION');
        if ($company_map[strtolower($name)]) {
            $condition[$company_map[strtolower($name)]] = $_SESSION['bind_account'];
            $model->where($condition);
        }
    }
    $list	=	$model->field('id,c_name1')->select();
    return $list;
}
function getPic($pic) {
    echo "<img src='".__ROOT__."/".$pic."'>";
}

function get_Cuse($id) {
	$model  = D("pic");
	$list = $model->where("id=$id")->getField('id,c_use');
	$type = $list[$id];

	$str = '<SELECT class="bLeft" id="c_use_'.$id.'" name="c_use_'.$id.'" onchange="setuse('.$id.')">';
	foreach (C("ROOM_TYPE") as $key=>$value) {
		if ($type==$key) {
			$select=" selected='selected' ";
		}else{
			$select = '';
		}
		$str .= '<option '.$select.' value='.$key.'>'.$value.'</option>';
	}
	$str .= '</SELECT>';
	echo $str;
}
//公共函数
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}


// 缓存文件
function cmssavecache($name = '', $fields = '') {
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val ) {
		if (empty ( $fields )) {
			$data [$val [$Model->getPk ()]] = $val;
		} else {
			// 获取需要的字段
			if (is_string ( $fields )) {
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1) {
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else {
				foreach ( $fields as $field ) {
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}

function getDefaultStyle($style) {
	if (empty ( $style )) {
		return 'blue';
	} else {
		return $style;
	}

}
function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array ();
	if (isset ( $_ip [$ip] )) {
		return $_ip [$ip];
	} else {
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id) {
	if (Session::is_set ( 'nodeNameList' )) {
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}

function get_pawn($pawn) {
	if ($pawn == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}
function get_patent($patent) {
	if ($patent == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}


function getNodeGroupName($id) {
	if (empty ( $id )) {
		return '未分组';
	}
	if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}

function getCardStatus($status) {
	switch ($status) {
		case 0 :
			$show = '未启用';
			break;
		case 1 :
			$show = '已启用';
			break;
		case 2 :
			$show = '使用中';
			break;
		case 3 :
			$show = '已禁用';
			break;
		case 4 :
			$show = '已作废';
			break;
	}
	return $show;

}

function showStatus($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
			break;
	}
	return $info;
}
/*
function showorderstate($state){
		switch ($state){
			case 0 :
				$info = 
			
		}
	
}
*/
/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1) {
	return rand_string ( $length, $mode );
}


function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->findAll ( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}
function sort_by($array, $keyname = null, $sortby = 'asc') {
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree ) {
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree ) {
		$inarray [] = $array [$key];
	}
	return $inarray;
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
//MD5加密
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
//为0检测
function check_zero(){
		if($_POST['series_id'] && $_POST['brand_id'] && $_POST['model_id']){
			return true;
		}else{
			return false;
		}
}
//总费用插入前检测
function check_total_cost(){

	if(!empty($_POST['total_cost']) && $_POST['total_cost'] != '0.00'){
		return true;
		echo $_POST['total_cost'];
	}else{
		return false;		
	}
	
}
//js返回
function js_back($msg){
$msg=stripslashes($msg);
$msg=str_replace("'","",$msg);
$msg=str_replace("\"","",$msg);
header("Content-Type:text/html; charset=utf-8");
exit("
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
    alert('$msg');
    history.go(-1);
//-->
</SCRIPT>
");
}
//js跳转
function js_goto($msg,$goto){
$msg=stripslashes($msg);
$msg=str_replace("'","",$msg);
$msg=str_replace("\"","",$msg);
$goto=stripslashes($goto);
$goto=str_replace("'","",$goto);
$goto=str_replace("\"","",$goto);
header("Content-Type:text/html; charset=utf-8");
exit("
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
    alert('$msg');
    window.location='$goto';
//-->
</SCRIPT>
");
}

//记账统计时间按时间区间查询
function get_note_time($time=''){
	switch ($time) {
		case 3:
			$time = strtotime("-3 month");
			$happen_time = array('GT',$time); 
		break;					
		case 6:
			$time = strtotime("-6 month");
			$happen_time = array('GT',$time); 
		break;
		case 12:
			$time = strtotime("-1 year");
			$happen_time = array('GT',$time); 
		break;
		case 36:
			$time = strtotime("-3 yaer");
			$happen_time = array('GT',$time); 
		break;
			default:
			$happen_time = ''; 
			break;
	}
	return $happen_time;
}
/*
*检查星期几是否有不包含在里面的星期
*$week_str 表saletime取过来的对应数据
*
*/
	function sale_check($week_str){
		
		$week_arr = explode(',',$week_str);
		$week_num = C('WEEK_NUM');
		
		foreach($week_num AS $k=>$v){
			if(!in_array($v,$week_arr)){
				$tmp .= $v.',';
			}
		}
		
		$res = substr($tmp,0,-1);
		return $res;
	}
/*
*用于日期的单双月判断
*
*/
function double_or_single_Calendar(){
	$current_date=date('d',time());
	if(($current_date+16) >= 30){
		return 'true';
	}else{
		return 'false';
	}

}		

function get_hidden_mobile($mobile){
    $mobile = substr_replace($mobile, '****', 3, -4);
    return $mobile;
}
/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if($code == 'UTF-8')
    {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);

        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen));
    }
    else
    {
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';

        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen))
            {
                if(ord(substr($string, $i, 1))>129)
                {
                    $tmpstr.= substr($string, $i, 2);
                }
                else
                {
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
        return $tmpstr;
    }
}
function cut_str2($string, $length) {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info); 
        for($i=0; $i<count($info[0]); $i++) {
                $wordscut .= $info[0][$i];
                $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
                if ($j > $length - 3) {
                        return $wordscut." ...";
                }
        }
        return join('', $info[0]);
}

function g_substr($str, $len = 12, $dot = true) {
        $i = 0;
        $l = 0;
        $c = 0;
        $a = array();
        while ($l < $len) {
            $t = substr($str, $i, 1);
            if (ord($t) >= 224) {
                $c = 3;
                $t = substr($str, $i, $c);
                $l += 2;
            } elseif (ord($t) >= 192) {
                $c = 2;
                $t = substr($str, $i, $c);
                $l += 2;
            } else {
                $c = 1;
                $l++;
            }
            // $t = substr($str, $i, $c);
            $i += $c;
            if ($l > $len) break;
            $a[] = $t;
        }
        $re = implode('', $a);
        if (substr($str, $i, 1) !== false) {
            array_pop($a);
            ($c == 1) and array_pop($a);
            $re = implode('', $a);
            $dot and $re .= '...';
        }
        return $re;
    }
?>