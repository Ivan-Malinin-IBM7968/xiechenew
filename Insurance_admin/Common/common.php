<?php


// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}



/* 排序 */

function sortBy($list, $field, $sortby = 'asc') {
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc':
                asort($refer);
                break;
            case 'desc':
                arsort($refer);
                break;
            case 'nat':
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/* 数组搜索 */

function search($list, $condition) {
    if (is_string($condition))
        parse_str($condition, $condition);
    $resultSet = array();
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } elseif ($data[$field] == $value) {
                    $find = true;
                }
            }
        }
        if ($find)
            $resultSet[] = &$list[$key];
    }
    return $resultSet;
}




/* 数据缓存 */

function cache($name, $value = '', $expire = 60) {
    if ('' === $value) {
        return xcache_get($name);
    } else {
        if (is_null($value)) {
            xcache_unset($name);
        } else {
            xcache_set($name, $value, $expire);
        }
    }
}



/* 推荐 */

function getRecommend($type) {
    switch ($type) {
        case 1: $icon = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/brand.gif" BORDER="0" align="absmiddle" ALT="">';
            break;
        default:
            $icon = '';
    }
    return $icon;
}

/* 密码处理 */

function pwdHash($password, $type = 'md5') {
    return hash($type, $password);
}

/* 生成不重复数 */

function build_count_rand($number, $length = 4, $mode = 1) {
    if ($mode == 1 && $length < strlen($number)) {
        return false;
    }
    $rand = array();
    for ($i = 0; $i < $number; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $unqiue = array_unique($rand);
    if (count($unqiue) == count($rand)) {
        return $rand;
    }
    $count = count($rand) - count($unqiue);
    for ($i = 0; $i < $count * 3; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
}
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
/* 时间格式化 */

function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date(($format), $time);
}

/* 状态处理 */

function getStatus($status, $imageShow = true) {
    switch ($status) {
        case 0:
            $showText = '禁用';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
        case 2:
            $showText = '待审';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/checkin.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
            break;
        case -1:
            $showText = '删除';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
            break;
        case 1:
        default:
            $showText = '正常';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
    }
    return ($imageShow === true) ? ($showImg) : $showText;
}

/*商家申请状态*/
function shopapplyStatus($status, $id) {
    switch ($status) {
        case 0:$info = '申请';
            break;
        case 1:$info = '客服确认';
            break;
        case 2:$info = '成功';
            break;
		case 3:$info = '失败';
            break;
    }
    return $info;
}

/* 状态显示 */

function showStatus($status, $id) {
    switch ($status) {
        case 0:$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
            break;
        case 2:$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
            break;
        case 1:$info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
            break;
        case -1:$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
            break;
    }
    return $info;
}
/* 状态显示 */

function showAticleStatus($status, $id) {
    switch ($status) {
        case 0:$info = '<a href="javascript:shenhe(' . $id . ')">审核</a> | <a href="javascript:del(' . $id . ')">删除</a>';
            break;
        case 1:$info = '<a href="javascript:del(' . $id . ')">删除</a>';
            break;
        case 2:$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
            break;
    }
    return $info;
}
/* 状态显示 */

function Statusshow($status, $id) {
    switch ($status) {
        case 0:$info = '已被禁用';
            break;
        case 1:$info = '正常';
            break;
    }
    return $info;
}

/* 添加备注显示 */

function memo($id,$memo='') {
	if($memo){
		$display_1 = "";
		$display_2 = "style='display:none'";
	}else{
		$display_1 = "style='display:none'";
		$display_2 = "";
	}
    $info = "<span id='memotextarea_".$id."' ".$display_1."><textarea name='memo_".$id."' id='memo_".$id."' cols='15'>".$memo."</textarea><a href='###' onclick='savememo(".$id.");'>修改</a></span><span id='memotext_".$id."' ".$display_2."><a href='###' onclick='memo(".$id.")'>备注</a></span>";
    return $info;
}
/*密码重置*/

function resetpw($uid,$mobile=''){
    $info = '';
    if ($mobile){
        $info = '<a href="javascript:resetpw(' . $uid . ')">密码重置</a>';
    }
    return $info;
}
/* 状态显示 */

function showRecommend($recommend, $id) {
    switch ($recommend) {
        case 0:$info = '<a href="javascript:recommend(' . $id . ')">推荐</a>';
            break;
        case 1:$info = '<a href="javascript:unrecommend(' . $id . ')">取消推荐</a>';
            break;
    }
    return $info;
}

/* 组名 */

function getGroupName($id) {
    if ($id == 0) {
        return '无上级组';
    }
    if ($list = F('groupName')) {
        return $list[$id];
    }
    $dao = D("Role");
    $list = $dao->field('id,name')->select();
    foreach ($list as $vo) {
        $nameList[$vo['id']] = $vo['name'];
    }
    $name = $nameList[$id];
    F('groupName', $nameList);
    return $name;
}

function getNameById($id,$model,$field='name') {
    $dao = D($model);
    $pk = $dao->getPk();
    $data = $dao->where($pk."=".$id)->field($field)->select();
    return ($data[0][$field]);
}

/* 显示文件扩展名 */

function showExt($ext, $pic = true) {
    static $_extPic = array(
'dir' => "folder.gif",
 'doc' => 'msoffice.gif',
 'rar' => 'rar.gif',
 'zip' => 'zip.gif',
 'txt' => 'text.gif',
 'pdf' => 'pdf.gif',
 'html' => 'html.gif',
 'png' => 'image.gif',
 'gif' => 'image.gif',
 'jpg' => 'image.gif',
 'php' => 'text.gif',
    );
    static $_extTxt = array(
'dir' => '文件夹',
 'jpg' => 'JPEG图象',
    );
    if ($pic) {
        if (array_key_exists(strtolower($ext), $_extPic)) {
            $show = "<IMG SRC='" . __ROOT__.'/Public' . "/Images/extension/" . $_extPic[strtolower($ext)] . "' BORDER='0' alt='' align='absmiddle'>";
        } else {
            $show = "<IMG SRC='" . __ROOT__.'/Public' . "/Images/extension/common.gif' WIDTH='16' HEIGHT='16' BORDER='0' alt='文件' align='absmiddle'>";
        }
    } else {
        if (array_key_exists(strtolower($ext), $_extTxt)) {
            $show = $_extTxt[strtolower($ext)];
        } else {
            $show = $ext ? $ext : '文件夹';
        }
    }

    return $show;
}

/* 处理文件大小 */

function byte_format($size, $dec = 2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

/* 置顶 */

function getTop($type) {
    switch ($type) {
        case 1: $icon = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/top.gif" BORDER="0" align="absmiddle" ALT="">';
            break;
        default:
            $icon = '';
    }
    return $icon;
}

function getThumb($name) {
	return (dirname($name)."/thumb_".basename($name));	
}

function jsonString($str)
{
	return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
}

/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];

        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

//获取当前登录的user_id
//2012/5/31
function get_current_user_id(){
    return session(C('USER_AUTH_KEY'));
}
//获取当前登录的user_name
//2012/5/31
function get_current_user_name(){
    return session('loginAdminUserName');
}
//短信接口
function curl_sms($post = '' , $charset = null ){

	$datamobile = array('130','131','132','155','156','186','185');
	$submobile = substr($post['phones'],0,3);
	$post['content'] = str_replace("联通", "联_通", $post['content']);
	if(in_array($submobile,$datamobile)){
		$post['content'] = $post['content']."  回复TD退订";
	}
	if(C('SMS_METHOD')=='1'){
		$post['content'] .= " 【携车网】";
		$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
		$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post['phones'],"Msg"=>$post['content'],"Channel"=>"");
		$p = $client->__soapCall('SendMsg',array('parameters' => $param));
		$res = $p;
	}
	else{
		

		$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh21007</account><password>49e96c9b07f0628fec558b11894a9e8f</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";

		$url = 'http://www.10690300.com/http/sms/Submit';
		$curl = curl_init($url );
		if( !is_null( $charset ) ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
		}
		if( !empty( $post ) ){
			$xml_data = 'message='.urlencode($xml_data);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
		}
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($curl);
		curl_close($curl);
	}
	//var_dump($res);
	return $res;
}

	//短信接口
	function curl_sms_other($post = '' , $charset = null ){

		$datamobile = array('130','131','132','155','156','186','185');
		$submobile = substr($post['phones'],0,3);
		$post['content'] = str_replace("联通", "联_通", $post['content']);
		if(in_array($submobile,$datamobile)){
			$post['content'] = $post['content']."  回复TD退订";
		}

		$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh7173</account><password>587938bf9727bd6a5a21d0a6185f94c6</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";

		$url = 'http://3tong.net:8090/http/sms/Submit';
        $curl = curl_init($url );
        if( !is_null( $charset ) ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
        }
        if( !empty( $post ) ){
			$xml_data = 'message='.urlencode($xml_data);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
		curl_close($curl);
		//var_dump($res);
        return $res;
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
 /**
  * 
  *######
  * @desc 模板数据相乘
  * @return 
  * @example
  */
function template_multiplication($a,$b){
    echo(intval($a)*intval($b));
}
?>