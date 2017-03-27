<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC.'/dedecollection.class.php');
$todaytime = strtotime(date('Y-m-d 0:0:0',time()));
$dsql->SetQuery("SELECT * FROM `#@__co_note` WHERE cotime<'".$todaytime."' AND City_name='ÉÏº£'");
//$dsql->SetQuery("SELECT * FROM `#@__co_note` WHERE nid='4'");

//$dsql->SetQuery("SELECT * FROM `#@__co_note` WHERE nid='14'");
$dsql->Execute(99);
$nid_arr = array();
while($row = $dsql->GetObject(99)){
    $nid_arr[] = intval($row->nid);
}
$co = new DedeCollection();
if(!isset($islisten)) $islisten = 1;
if(!isset($glstart)) $glstart = 0;
if(!isset($totalnum)) $totalnum = 0;
if(!isset($notckpic)) $notckpic = 0;
$pagesize = 100;
if (!empty($nid_arr)){
    foreach ($nid_arr as $nid){
        $cotime_arr = $dsql->GetOne("SELECT cotime FROM `#@__co_note` WHERE nid='".$nid."'");
        $cotime = $cotime_arr['cotime'];
        if ($cotime >0){
            $islisten = 1;
        }else{
            $islisten = 2;
        }
        $co->LoadNote($nid);
        $co->GetSourceUrl($islisten, $glstart, $pagesize);
        $dsql->SetQuery("SELECT aid,nid,url,isdown,litpic FROM `#@__co_htmls` WHERE nid=$nid ");
        $dsql->Execute(99);
        while($row1 = $dsql->GetObject(99)){
            $co->DownUrl($row1->aid,$row1->url,$row1->litpic);
        }
        $dsql->ExecuteNoneQuery("UPDATE `#@__co_note` SET cotime='".time()."' WHERE nid='$nid'; ");
    }
}
