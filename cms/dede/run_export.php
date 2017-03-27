<?php
require_once (dirname ( __FILE__ ) . "/config.php");
require_once (DEDEINC . '/dedecollection.class.php');
$co = new DedeCollection ();

$dsql->SetQuery ( "SELECT * FROM `#@__co_note` " );
$dsql->Execute ( 99 );
$noto_arr = array ();
while ( $row = $dsql->GetObject ( 99 ) ) {
    $noto_arr [$row->nid] ['channelid'] = intval ( $row->channelid );
    $noto_arr [$row->nid] ['fsid'] = intval ( $row->fsid );
    $noto_arr [$row->nid] ['shopid'] = intval ( $row->shopid );
    $noto_arr [$row->nid] ['city_name'] = $row->city_name;
}

$channelid = isset ( $channelid ) && is_numeric ( $channelid ) ? $channelid : 0;
$fsid = isset($fsid) && is_numeric($fsid) ? $fsid : 0;
$shopid = isset($shopid) && is_numeric($shopid) ? $shopid : 0;
$city_name = isset($city_name)? $city_name : '上海';
$typeid = isset ( $typeid ) && is_numeric ( $typeid ) ? $typeid : 0;
$pageno = isset ( $pageno ) && is_numeric ( $pageno ) ? $pageno : 1;
$startid = isset ( $startid ) && is_numeric ( $startid ) ? $startid : 0;
$endid = isset ( $endid ) && is_numeric ( $endid ) ? $endid : 0;
$arcrank = isset ( $arcrank ) ? $arcrank : 0;
$pagesize = isset ( $pagesize ) ? $pagesize : 999999999;
if(! isset ( $makehtml ))
    $makehtml = 0;
if(! isset ( $onlytitle ))
    $onlytitle = 0;
if(! isset ( $usetitle ))
    $usetitle = 0;
if(! isset ( $autotype ))
    $autotype = 1;

if(! empty ( $noto_arr )) {
    foreach ( $noto_arr as $nid => $val ) {
        $channelid = $val ['channelid'];
        $fsid = $val ['fsid'];
		$shopid = $val ['shopid'];
		$city_name = $val ['city_name'];
        $co->LoadNote ( $nid );
        $orderway = (($co->noteInfos ['cosort'] == 'desc' || $co->noteInfos ['cosort'] == 'asc') ? $co->noteInfos ['cosort'] : 'desc');
        
        if($channelid == 0) {
            $row = $dsql->GetOne ( "SELECT ch.* FROM `#@__arctype` tp LEFT JOIN `#@__channeltype` ch ON ch.id=tp.channeltype WHERE tp.id='$typeid'; " );
        }
        else {
            $row = $dsql->GetOne ( "SELECT * FROM `#@__channeltype` WHERE id='$channelid'; " );
        }
        //分析规则，并生成临时的SQL语句
        $channelid = $row ['id'];
        $maintable = $row ['maintable'];
        $addtable = $row ['addtable'];
        if(empty ( $maintable ))
            $maintable = '#@__archives';
        if(empty ( $addtable )) {
            echo "找不主表配置信息，无法完成操作！";
            continue;
        }
        
        $adminid = $cuserLogin->getUserID ();
        
        //微索引表
        $indexSqlTemplate = "INSERT INTO `#@__arctiny`(`arcrank`,`typeid`,`channel`,`senddate`,`sortrank`) VALUES ('$arcrank','@typeid@' ,'$channelid','@senddate@', '@sortrank@'); ";
        
        //基本信息主表
        $mainSqlTemplate = "INSERT INTO `$maintable`(id,typeid,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,color,writer,source,litpic,pubdate,senddate,mid,description,keywords,fsid,shopid,city_name)
               VALUES ('@aid@','@typeid@','@sortrank@','@flag@','0','$channelid','$arcrank','0','0','@title@','','','@writer@','@source@','@litpic@','@pubdate@','@senddate@','$adminid','@description@','@keywords@','$fsid','$shopid','$city_name'); ";
        
        //生成附加表插入的SQL语句
        $inadd_f = $inadd_v = '';
        $dtp = new DedeTagParse ();
        $dtp->SetNameSpace ( 'field', '<', '>' );
        $dtp->LoadString ( $row ['fieldset'] );
        foreach ( $dtp->CTags as $ctag ) {
            $tname = $ctag->GetTagName ();
            $inadd_f .= ",`$tname`";
            $notsend = $ctag->GetAtt ( 'notsend' );
            $fieldtype = $ctag->GetAtt ( 'type' );
            if($notsend == 1) {
                //对不同类型设置默认值
                if($ctag->GetAtt ( 'default' ) != '') {
                    $dfvalue = $ctag->GetAtt ( 'default' );
                }
                else if($fieldtype == 'int' || $fieldtype == 'float' || $fieldtype == 'number') {
                    $dfvalue = '0';
                }
                else if($fieldtype == 'dtime') {
                    $dfvalue = time ();
                }
                else {
                    $dfvalue = '';
                }
                $inadd_v .= ",'$dfvalue'";
            }
            else {
                $inadd_v .= ",'@$tname@'";
            }
        }
        $addSqlTemplate = "INSERT INTO `{$addtable}`(`aid`,`typeid`{$inadd_f}) Values('@aid@','@typeid@'{$inadd_v})";
        
        //导出数据的SQL操作
        $dtp = new DedeTagParse ();
        $totalpage = $totalcc / $pagesize;
        $startdd = ($pageno - 1) * $pagesize;
		
        if(! empty ( $nid )) {
            $dsql->SetQuery ( "SELECT * FROM `#@__co_htmls` WHERE nid='$nid' AND isdown='1' AND isexport='0' ORDER BY aid $orderway LIMIT $startdd,$pagesize" );
        }
        else {
            $dsql->SetQuery ( "SELECT * FROM `#@__co_htmls` WHERE isdown='1' AND isexport='0' ORDER BY aid $orderway LIMIT $startdd,$pagesize" );
        }
        $dsql->Execute ();
        while ( $row = $dsql->GetObject () ) {
            if(trim ( $row->result == '' ))
                continue;
                
            //$addSqlTemplate,$mainSqlTemplate,$indexSqlTemplate
            $ntypeid = ($autotype == 1 && $row->typeid != 0) ? $row->typeid : $typeid;
            $indexSql = str_replace ( '@typeid@', $ntypeid, $indexSqlTemplate );
            $mainSql = str_replace ( '@typeid@', $ntypeid, $mainSqlTemplate );
            $addSql = str_replace ( '@typeid@', $ntypeid, $addSqlTemplate );
            $dtp->LoadString ( $row->result );
            $exid = $row->aid;
            if(! is_array ( $dtp->CTags ))
                continue;
            
            //获取时间和标题
            $pubdate = $sortrank = time ();
            $title = $row->title;
            $litpic = '';
            foreach ( $dtp->CTags as $ctag ) {
                $itemName = $ctag->GetAtt ( 'name' );
                if($itemName == 'title' && $usetitle == 0) {
                    $title = trim ( $ctag->GetInnerText () );
                    if($title == '') {
                        $title = $row->title;
                    }
                }
                else if($itemName == 'pubdate') {
                    $pubdate = trim ( $ctag->GetInnerText () );
                    if(preg_match ( "#[^0-9]#", $pubdate )) {
                        $pubdate = $sortrank = GetMkTime ( $pubdate );
                    }
                    else {
                        $pubdate = $sortrank = time ();
                    }
                }
                else if($itemName == 'litpic') {
                    $litpic = trim ( $ctag->GetInnerText () );
                }
            }
            
            //检测重复标题
            $title = addslashes ( $title );
            if($onlytitle) {
                $testrow = $dsql->GetOne ( "SELECT COUNT(ID) AS dd FROM `$maintable` WHERE title LIKE '$title'" );
                if($testrow ['dd'] > 0) {
                    echo "数据库已存在标题为: {$title} 的文档，程序阻止了此本条内容导入<br />\r\n";
                    continue;
                }
            }
            
            //替换固定的项目
            $senddate = time ();
            $flag = '';
            if($litpic != '')
                $flag = 'p';
                
            //随机推荐
            if($randcc > 0) {
                $rflag = mt_rand ( 1, $randcc );
                if($rflag == $randcc) {
                    $flag = ($flag == '' ? 'c' : $flag . ',c');
                }
            }
            $indexSql = str_replace ( '@senddate@', $senddate, $indexSql );
            $indexSql = str_replace ( '@sortrank@', $sortrank, $indexSql );
            $mainSql = str_replace ( '@flag@', $flag, $mainSql );
            $mainSql = str_replace ( '@sortrank@', $sortrank, $mainSql );
            $mainSql = str_replace ( '@pubdate@', $pubdate, $mainSql );
            $mainSql = str_replace ( '@senddate@', $senddate, $mainSql );
            $mainSql = str_replace ( '@title@', cn_substr ( $title, 1000 ), $mainSql );
            $addSql = str_replace ( '@sortrank@', $sortrank, $addSql );
            $addSql = str_replace ( '@senddate@', $senddate, $addSql );
            
            //替换模型里的其它字段
            foreach ( $dtp->CTags as $ctag ) {
                if($ctag->GetName () != 'field') {
                    continue;
                }
                $itemname = $ctag->GetAtt ( 'name' );
                $itemvalue = addslashes ( trim ( $ctag->GetInnerText () ) );
                $mainSql = str_replace ( "@$itemname@", $itemvalue, $mainSql );
                $addSql = str_replace ( "@$itemname@", $itemvalue, $addSql );
            }
			
            //插入数据库
            $rs = $dsql->ExecuteNoneQuery ( $indexSql );
            if($rs) {
                $aid = $dsql->GetLastID ();
                $mainSql = str_replace ( '@aid@', $aid, $mainSql );
                $addSql = str_replace ( '@aid@', $aid, $addSql );
                $mainSql = preg_replace ( "#@([a-z0-9]{1,})@#", '', $mainSql );
                $addSql = preg_replace ( "#@([a-z0-9]{1,})@#", '', $addSql );
				
                $rs = $dsql->ExecuteNoneQuery ( $mainSql );
                if(! $rs) {
                    echo "导入 '$title' 时错误：" . $dsql->GetError () . "<br />";
                    $dsql->ExecuteNoneQuery ( "DELETE FROM `#@__arctiny` WHERE id='$aid' " );
                }
                else {
                    $rs = $dsql->ExecuteNoneQuery ( $addSql );
                    if(! $rs) {
                        echo "导入 '$title' 时错误：" . $dsql->GetError () . "<br />";
                        $dsql->ExecuteNoneQuery ( "DELETE FROM `#@__arctiny` WHERE id='$aid' " );
                        $dsql->ExecuteNoneQuery ( "DELETE FROM `$maintable` WHERE id='$aid' " );
                    }
                }
            }
            $dsql->ExecuteNoneQuery ( "UPDATE `#@__co_htmls` SET isexport=1 WHERE aid='$exid' " );
        }
    
    }
}
