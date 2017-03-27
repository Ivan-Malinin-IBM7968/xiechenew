<?php
header("Content-type:text/html; charset=utf-8");
require "common.php";

$action=$_REQUEST[action];
$intro_pic=mt_rand(1,5);
$t->set("intro_pic",$intro_pic);
$t->files("index.htm");
//$t->files("cbf125.htm");

//$last_user_id=$db->query("SELECT qid FROM {$table_qg} ORDER BY qid DESC limit 1","1");

if($action=="login"){
    $username=$db->safe($_POST[username]);
    $password=md5($db->safe($_POST[password]));
    if (is_numeric($username)){
		    if (substr($username,0,1)==1){
		        $sql = "SELECT uid,username,mobile,cardid,email,password FROM tp_xieche.xc_member WHERE mobile='$username' AND status='1' LIMIT 1";
		    }else {
		        $sql = "SELECT uid,username,mobile,cardid,email,password FROM tp_xieche.xc_member WHERE cardid='$username' AND status='1' LIMIT 1";
		    }
		}else {
		    $sql = "SELECT uid,username,mobile,cardid,email,password FROM tp_xieche.xc_member WHERE username='$username' AND status='1' LIMIT 1";
		}
        $user_info=$db->query($sql,"assoc");
        if($user_info and $user_info[password]==$password){
			/*
            $_SESSION['uid'] = $user_info['uid'];
			$_SESSION['username'] = $user_info['username'];
			$_SESSION['mobile'] = $user_info['mobile'];
			$_SESSION['cardid'] = $user_info['cardid'];
			$_SESSION['mobile'] = $user_info['mobile'];
			$_SESSION['email'] = $user_info['email'];
			*/
            
			$mc->set($ip.$qgsid,$user_info,86400);
                js_top_go("登录成功！抢楼期间请不要关闭窗口，否则可能需要重新登录。","index.php#qg");
        }
                js_top_go("登录失败。","index.php#qg");
}

if($action=="save") {
    if(!$discuz_uid){
        js_top_go("请登录","index.php#qg");
    }
    
    $set_str = '';
    if($_POST[username]){
        $username=$db->safe($_POST[username]);
        if (empty($username)){
    	    js_top_go("用户名不能为空","index.php#qg");
    	}
        if ($username){
    	    if (preg_match("/^[0-9]\d*$/i", $username)){
    	        js_top_go("用户名不能以数字开头！","index.php#qg");
    	    }
    	    $sql_check_username = "SELECT * FROM tp_xieche.xc_member WHERE username='$username' AND uid!='$discuz_uid' LIMIT 1";
    	    if ($db->query($sql_check_username,"assoc")){
    	        js_top_go("用户名已存在，请重新填写！","index.php#qg");
    	    }
    	}
    	$set_str .= "username='$username',";
    }
    if($_POST[email]){
        $email=$db->safe($_POST[email]);
        if (empty($email)){
    	    js_top_go("邮箱不能为空！","index.php#qg");
    	}
        if ($email and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $email)){
    	    js_top_go("邮箱格式错误！","index.php#qg");
    	}
        if ($email){
            $sql_check_email = "SELECT * FROM tp_xieche.xc_member WHERE email='$email' AND uid!='$discuz_uid' LIMIT 1";
    	    if ($db->query($sql_check_email,"assoc")){
    	        js_top_go("邮箱号码已存在，请重新填写！","index.php#qg");
    	    }
    	}
    	$set_str .= "email='$email',";
    }
    if($_POST[mobile]){
    	$mobile=$db->safe($_POST[mobile]);
        if (empty($mobile)){
    	    js_top_go("手机号码不能为空！","index.php#qg");
    	}
    	if ($mobile and !eregi("^1[0-9]{10}$",$mobile)){
    	    js_top_go("手机号码格式错误！","index.php#qg");
    	}
        if ($mobile){
            $sql_check_mobile = "SELECT * FROM tp_xieche.xc_member WHERE mobile='$mobile' AND uid!='$discuz_uid' LIMIT 1";
    	    if ($db->query($sql_check_mobile,"assoc")){
    	        js_top_go("手机号码已存在，请重新填写！","index.php#qg");
    	    }
    	}
    	$set_str .= "mobile='$mobile',";
    }
    $set_str = substr($set_str,0,-1);
    if ($username || $email || $mobile){
        $sql = "UPDATE tp_xieche.xc_member SET $set_str WHERE uid='$discuz_uid' LIMIT 1";
        $db->query($sql);
    }
    
    $sql_sel = "SELECT uid,username,mobile,cardid,email,password FROM tp_xieche.xc_member WHERE uid='$discuz_uid' AND status='1' LIMIT 1";
    $user_info=$db->query($sql_sel,"assoc");
    if($user_info){
        /*
		$_SESSION['uid'] = $user_info['uid'];
		$_SESSION['username'] = $user_info['username'];
		$_SESSION['mobile'] = $user_info['mobile'];
		$_SESSION['cardid'] = $user_info['cardid'];
		$_SESSION['mobile'] = $user_info['mobile'];
		$_SESSION['email'] = $user_info['email'];
		*/
		$mc->set($ip.$qgsid,$user_info,86400);
    }
    
    $truename=$db->safe($_POST[truename]);
    $tel=$db->safe($_POST[mobile]);
    $brand_id=$db->safe($_POST[brand_id]);
    $series_id=$db->safe($_POST[series_id]);
    $model_id=$db->safe($_POST[model_id]);
    //$idcard=$db->safe($_POST[idcard]);
    //$address=$db->safe($_POST[address]);
    //$zipcode=$db->safe($_POST[zipcode]);
    //$acceptad=$db->safe($_POST[acceptad]);
    //$prov=$db->safe($_POST[prov]);
    //$city=$db->safe($_POST[city]);
    $now = time();
    $db->query("UPDATE {$table_user_info} SET
                            last_ip='{$ip}',
                            truename='{$truename}',
                            tel='{$tel}',
                            brand_id='{$brand_id}',
                            series_id='{$series_id}',
                            model_id='{$model_id}',
							update_times=update_times+1,
                            create_time='{$now}'
							WHERE user_id='$discuz_uid' LIMIT 1    ");
    if(!$db->a_rows()){
        $db->query("INSERT INTO {$table_user_info} SET
                                user_id='{$sess[user_id]}',
                                user_name='{$sess[user_name]}',
                                last_ip='{$ip}',
                                truename='{$truename}',
                                tel='{$tel}',
                                brand_id='{$brand_id}',
                                series_id='{$series_id}',
                                model_id='{$model_id}',
                                create_time='{$now}',
                                update_time='{$now}'
								");
    }
    js_top_go("保存资料成功。","index.php#qg");
}


if($action=="update") {
    
    session_start();
	$svcode=$_SESSION[verify];
	//unset($_SESSION[verify]);
	//session_destroy();
	if(!$svcode){
		js_top_go("验证码输入错误，请输入4位数字作为验证码。","index.php");
	}
	if(!trim($_POST["vcode$ipvcode"])){
		js_top_go("验证码输入错误，请输入4位数字作为验证码。","index.php");
	}
    if(md5(trim($_POST["vcode$ipvcode"]))!=$svcode){
		js_top_go("验证码输入错误，请输入4位数字作为验证码。","index.php");
    }
        
	if(!$discuz_uid){
				js_top_go("请登录","index.php");
	}
    if(!$sess[user_id]){
            js_top_go("请登录","index.php");
    }
    if(!$sess[user_name]){
                    js_top_go("请登录","index.php");
    }
	//
    if(!$have_profile){
        js_top_go("您的资料不完整，请填写完整您的资料","index.php");
    }
	if($qg_end){
		js_top_go("活动结束！","index.php");
	}
    if(!$qg_start){
        js_top_go("抢楼尚未开始","index.php");
    }
    
    if($joined){
        //js_top_go("您已经参加过本抢购","index.php");
    }

    $truename=$db->safe($_POST[truename]);
    $tel=$db->safe($_POST[tel]);

        $last_ip_qg_time=$db->query("SELECT qg_time FROM {$table_qg} WHERE user_id='{$sess[user_id]}' ORDER BY qid DESC LIMIT 1","1");
        if($last_ip_qg_time>$now-15){
                        js_top_go("一个人15秒内只能抢楼一次","index.php");
        }
        //$last_ip_qg_time=$db->query("SELECT qg_time FROM {$table_qg} WHERE last_ip='{$ip}' ORDER BY qid DESC LIMIT 1","1");
        /*if($last_ip_qg_time>$now-15){
                        js_top_go("一个人１５秒内只能抢楼一次","index.php#qg");
        }*/
        $db->query("INSERT INTO {$table_qg} SET
                                user_id='{$sess[user_id]}',
                                user_name='{$sess[user_name]}',
                                last_ip='{$ip}',
                                truename='{$truename}',
                                tel='{$tel}',
                                qg_time='{$now}',
                                qg_microtime='{$nowm}' ");

        js_top_go("参加成功！","index.php");
}


require_once('page_inc.php');
$pagenum='500';

if($show_list){
		$sql="SELECT * FROM {$table_qg_read_list} WHERE user_id='$sess[user_id]' ORDER BY qid DESC";
		$res=$db->query($sql);
		while($row=$db->fetch_assoc($res)){
				$sec=date("Y-m-d H:i:s",$row[qg_time]);
				$my_list.=$row[user_name]." 抢到{$row[qid]}楼， 时间是 $sec <br>";
		}


		$mpurl="index.php";
		$page=intval($_GET['page']);
								if($page){
										$start_limit = ($page - 1)*$pagenum;
								}else{
										$start_limit = 0;
										$page = 1;
								}
		$sql="SELECT count(*) FROM {$table_qg_read_list}";
		$num_rows=$db->query($sql,"1");
		$Paginationpage=Pagination($num_rows,$pagenum,$page,$mpurl);
		$sql="SELECT * FROM {$table_qg_read_list} ORDER BY qid DESC limit $start_limit,$pagenum";
		$res=$db->query($sql);
		while($row=$db->fetch_assoc($res)){
				$sec=date("Y-m-d H:i:s",$row[qg_time]);
				$now_list.=$row[user_name]." 抢到{$row[qid]}楼， 时间是 $sec <br>";
		}

}else{
$my_list="";
$now_list="";
$Paginationpage="";
}
$t->set("my_list",$my_list);
$t->set("now_list",$now_list);
$t->set("Paginationpage",$Paginationpage);
require "end.php";