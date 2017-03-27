<form id="form1" name="form1" method="post" action="zz11.php?kfno=<?echo $_GET['kfno'];?>&staff_id=<?echo $_GET['staff_id'];?>">
    姓名：<input name="xname" type="text" /><Br>
    手机号：<input name="mobile" type="text" /><Br>
    车牌号：<input name="carno" type="text" /><Br>
    备注：<textarea name="beizhu" cols="45" rows="5"></textarea><Br>
    <input name="kfno" type="hidden" value="<?php if (isset($_GET["kfno"]) and $_GET["kfno"]!=""){echo $_GET["kfno"];}else{echo $_POST["kfno"];}?>" />
    <input name="" type="submit" value="提交" />
</form>
<?php
date_default_timezone_set('PRC');
include_once "../fsyc/class.db.php";
include_once "../fsyc/config.php";
$db=new mysql($dbhost,$dbuser,$dbpass,"",3306,"","utf8");

$kfname = array(
    1	=> 'admin',
    223 => '王俊炜',
    171 => '彭晓文',
    182 => '张丹红',
    241 => '朱笑龙',
    234 => '张美婷',
    243 => '黄美琴',
    242 => '李宝峰',
    251 => '庄玉成',
    252	=> '周祥金',
);

if (isset($_POST["mobile"]) and trim($_POST["mobile"])!="" and strlen($_POST["mobile"])==11){

    $wk_yg_id=$db->query("SELECT staff_id FROM tp_xieche.xc_api_record where mobile='".trim($_POST["mobile"])."' limit 1","1");

    if ($wk_yg_id){
        echo "此号码".$kfname[$wk_yg_id]."已经录入过了!";
        exit;
    }

    $data = array(
        'code'=> 'fqcd123223',
        'mobile'=> trim($_POST["mobile"]),
        'name'=>iconv("utf-8","gb2312//IGNORE",trim($_POST["xname"])),
        'remark'=>iconv("utf-8","gb2312//IGNORE","车牌：".trim($_POST["carno"])."备注：".trim($_POST["beizhu"])),
        'staff_id'=>$_POST["kfno"],
        'task'=>2
    );

    $db->query("insert into tp_xieche.xc_api_record set type=1,staff_id='".$_GET['staff_id']."',mobile='".trim($_POST["mobile"])."',name='".trim($_POST["xname"])."',remark='车牌：".trim($_POST["carno"])."备注：".trim($_POST["beizhu"])."',create_time='".time()."'");

  /*  $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'http://fqcd.3322.org:88/api.php');
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:192.168.1.1, CLIENT-IP:192.168.1.1'));  //构造IP  
//curl_setopt($ch, CURLOPT_REFERER, 'http://www.sina.com.cn/');   //构造来路
    curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_USERAGENT, "999999999");
    curl_setopt ( $ch, CURLOPT_POST, 1 );

    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 0 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
//curl_setopt($ch, CURLOPT_PROXY, $wk_ip[$jyi]);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    $out = curl_exec($ch);
    var_dump( curl_error($ch));//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    echo "我是分割线";
    curl_close($ch);
    print_r($out);
    print_r($data);
    echo "已添加";*/
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,'http://fqcd.3322.org:88/api.php');
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 0);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,1); // post传输数据
    curl_setopt($curl,CURLOPT_POSTFIELDS,$data);// post传输数据
    curl_setopt ($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt ($curl, CURLOPT_FRESH_CONNECT, 1);

    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl));//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);
    print_r($responseText);
}
?>