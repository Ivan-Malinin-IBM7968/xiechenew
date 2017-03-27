<?php
require_once (dirname ( __FILE__ ) . "/config.php");

$filename = DEDEDATA.'/sqldata/'.date('Y-m-d').'.sql';
//if( !is_file($filename) ){
        
    $row = $dsql->GetOne("SELECT maxid FROM `#@__archives_maxid`");
    
	$dsql->SetQuery ( "SELECT * FROM `#@__archives` AS a LEFT JOIN `#@__addonarticle` AS b ON a.id=b.aid WHERE a.id>'".$row['maxid']."' " );
    $dsql->Execute ( 99 );
    $result_arr = array ();
    while ( $rows = $dsql->GetObject ( 99 ) ) {
        $result_arr[] = $rows;
    }
    $dsql->SetQuery("SELECT * FROM `#@__arctype` ");
    $dsql->Execute( 99 );
    $arctype_arr = array();
    while ($arctype = $dsql->GetObject( 99 )){
        $arctype_arr[$arctype->id] = $arctype->typename;
    }
	$delete_query = "TRUNCATE TABLE `linshiarticle` ";
	$dsql->ExecuteNoneQuery($delete_query);
    $data = array();
    $sql = '';
    $sql_key = 'INSERT INTO `linshiarticle` (`fsid`,`shop_id`,`city_name`,`title`,`summary`,`keyword`,`content`,`create_time`) VALUES ';
    $sql_v = '';
    $maxid = $newmaxid = $row['maxid'];
    if (!empty($result_arr)){
		$ii = 0;
        foreach ($result_arr as $val){
			$ii++;
            if ($newmaxid<$val->id){
                $newmaxid = $val->id;
            }
            
            $sql_v .= '("'.$val->fsid.'",'.'"'.$val->shopid.'",'.'"'.$val->city_name.'",'.'"'.addcslashes(trim($val->title),'"').'",'.'"'.addcslashes($val->description,'"').'",'.'"'.addcslashes($val->keywords,'"').'",'.'"'.addcslashes($val->body,'"').'",'.'"'.time().'"),' ;
			if($ii%10==0){
				$sql_v = substr($sql_v,0,-1);
				$sql = $sql_key.$sql_v;
				//$search_str = '/cms/uploads/allimg/c'.date('ymd').'/';
				//$replace_str = '/UPLOADS/Cms/'.date('Y').'/'.date('Y-m-d').'/';
				//$sql = str_replace($search_str, $replace_str, $sql);
				//$search_str = '<img ';
				//$replace_str = '<img onload=\"if(this.width>screen.width)this.width=(screen.width-20)\" ';
				//$sql = str_replace($search_str, $replace_str, $sql);

				$pattern = "/<img(.*?)src=.*?www.autoimg.cn.*?>/i";
				$replacement = "";
				$sql = preg_replace($pattern, $replacement, $sql);
				//echo $sql.'<br>-----------------------------------------';
				$dsql->ExecuteNoneQuery($sql);
				$sql_v = "";
				if ($maxid != $newmaxid){
					$query = "UPDATE `#@__archives_maxid` SET maxid='".$newmaxid."'";
					$dsql->ExecuteNoneQuery($query);
				}
			}
        }
		$sql_v = substr($sql_v,0,-1);
		$sql = $sql_key.$sql_v;
		
		//echo $sql.'<br>================================';
		//$search_str = '<img ';
		//$replace_str = '<img onload="if(this.width>screen.width)this.width=(screen.width-20)" ';
		//$sql = str_replace($search_str, $replace_str, $sql);

		$pattern = "/<img(.*?)src=.*?www.autoimg.cn.*?>/i";
		$replacement = "";
		$sql = preg_replace($pattern, $replacement, $sql);


		$dsql->ExecuteNoneQuery($sql);
		
		$sql_v = "";
		if ($maxid != $newmaxid){
			$query = "UPDATE `#@__archives_maxid` SET maxid='".$newmaxid."'";
			$dsql->ExecuteNoneQuery($query);
		}
        //$sql_v = substr($sql_v,0,-1);
        //$sql = $sql_key.$sql_v;
		
        //$dsql->ExecuteNoneQuery($sql);
        //$search_str = '/uploads/allimg/c'.date('ymd').'/';
        //$replace_str = 'http://img3.pinla.com/cmsimg/'.date('Y-m-d').'/';
        //$sql = str_replace($search_str, $replace_str, $sql);

        //file_put_contents($filename, $sql);

    }
//}

