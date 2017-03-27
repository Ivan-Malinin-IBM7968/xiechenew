<?php
header("Content-type:text/html; charset=utf-8");
require "common.php";


$intro_pic=mt_rand(1,5);
$t->set("intro_pic",$intro_pic);
$t->files("cbf125.htm");

$action = $_GET[action];
if($action=="save") {
    if(!$discuz_uid){
    js_top_go("请登录","index.php");
    }
    $truename=$db->safe($_POST[truename]);
    $tel=$db->safe($_POST[tel]);
    $idcard=$db->safe($_POST[idcard]);
    $address=$db->safe($_POST[address]);
    $zipcode=$db->safe($_POST[zipcode]);
    $acceptad=$db->safe($_POST[acceptad]);
    $db->query("UPDATE {$table_qg} SET 
                            last_ip='{$ip}',
                            truename='{$truename}',
                            tel='{$tel}',
                            idcard='{$idcard}',
                            address='{$address}',
                            acceptad='{$acceptad}',
                            zipcode='{$zipcode}',update_times=update_times+1 WHERE user_id='$discuz_uid' LIMIT 1
    ");
    if(!$db->a_rows()){
        $db->query("INSERT INTO {$table_qg} SET 
                                user_id='{$sess[user_id]}',
                                user_name='{$sess[user_name]}',
                                last_ip='{$ip}',
                                truename='{$truename}',
                                tel='{$tel}',
                                idcard='{$idcard}',
                                address='{$address}',acceptad='{$acceptad}',
                                zipcode='{$zipcode}' ");
    }
    js_top_go("保存资料成功。","index.php");
}


if($action=="update") {
    if(!$discuz_uid){
    js_top_go("请登录","index.php");
    }
    if(!$have_profile){
        js_top_go("您的资料不完整，请填写完整您的资料","index.php");
    }
    if(!$qg_start){
        js_top_go("抢购尚未开始","index.php");
    }
    if($joined){
        js_top_go("您已经参加过本抢购","index.php");
    }
    $db->query("UPDATE {$table_qg} SET 
                            last_ip='{$ip}',
                            qg_time='{$now}',
                            qg_microtime='{$nowm}' WHERE user_id='$discuz_uid' LIMIT 1
    ");
        js_top_go("参加成功！请注意及时汇款！","index.php");
}
require "end.php";