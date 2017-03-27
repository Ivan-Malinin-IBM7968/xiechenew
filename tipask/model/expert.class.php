<?php

!defined('IN_TIPASK') && exit('Access Denied');

class expertmodel {

    var $db;
    var $base;

    function expertmodel(&$base) {
        $this->base = $base;
        $this->db = $base->db;
    }

    function get_list($showquestion=0, $start=0, $limit=3) {
        $expertlist = array();
        $query = $this->db->query("SELECT distinct u.uid,u.username,u.credit1,u.lastlogin,u.answers,u.questions,u.expert FROM " . DB_TABLEPRE . "expert as e," . DB_TABLEPRE . "user as u WHERE u.uid=e.uid ORDER BY u.credit1 DESC LIMIT $start ,$limit");
        while ($expert = $this->db->fetch_array($query)) {
            $expert['avatar'] = get_avatar_dir($expert['uid']);
            $expert['lastlogin'] = tdate($expert['lastlogin']);
            $expert['categoryname'] = $this->get_category($expert['uid']);
            $showquestion && $expert['bestanswer'] = $this->get_solve_answer($expert['uid'], 0, 10);
            $expertlist[] = $expert;
        }
        return $expertlist;
    }
	function get_list_by_order($showquestion=0, $order='credit1', $start=0, $limit=10) {
        $expertlist = array();
		
        $query = $this->db->query("SELECT distinct u.uid,u.username,u.credit1,u.lastlogin,u.answers,u.questions,u.expert FROM " . DB_TABLEPRE . "expert as e," . DB_TABLEPRE . "user as u WHERE u.uid=e.uid ORDER BY u.$order DESC LIMIT $start ,$limit");
        while ($expert = $this->db->fetch_array($query)) {
            $expert['avatar'] = get_avatar_dir($expert['uid']);
            $expert['lastlogin'] = tdate($expert['lastlogin']);
            $expert['categoryname'] = $this->get_category($expert['uid']);
            $showquestion && $expert['bestanswer'] = $this->get_solve_answer($expert['uid'], 0, 10);
            $expertlist[] = $expert;
        }
		//echo '<pre>';print_r($expertlist);exit;
        return $expertlist;
    }
    function get_expertlist(){
        $brand_ids = "1,2,3,4,5";
        $result_arr = array();
        $query = $this->db->query("SELECT * FROM " . DB_TABLEPRE . "category WHERE `brand_id` in ($brand_ids) and series_id=0");
		$brand_num = 0;
		$expert_num = 0;
        while ($category = $this->db->fetch_array($query)) {
            $cid=$category['id'];
            $query1 = $this->db->query("SELECT * FROM " . DB_TABLEPRE . "expert WHERE `cid`=$cid");
            while ($expert = $this->db->fetch_array($query1)) {
				$expert_num += 1;
                $uid = $expert['uid'];
                $user = $this->db->fetch_array($this->db->query("SELECT * FROM " . DB_TABLEPRE . "user WHERE `uid`=$uid"));
                $user['shop_id'] = $expert['shop_id'];
                $this->db->select_db("tp_xiechetest");
                $member = $this->db->fetch_array($this->db->query("SELECT * FROM xc_member WHERE `uid`=$uid"));
                $this->db->select_db("tipask");
                $user['comment_rate'] = $member['comment_rate'];
                $user['comment_number'] = $member['comment_number'];
                $category['user'][] = $user;
            }
            
			if($category['user']){
			    $count = count($category['user']);
                for($i = 0; $i < $count; $i ++) {
                    for($j = $count - 1; $j > $i; $j --) {
                        if ($category['user'][$j]['comment_rate'] > $category['user'][$j - 1]['comment_rate']) {
                            //交换两个数据的位置
                            $temp = $category['user'] [$j];
                            $category['user'] [$j] = $category['user'] [$j - 1];
                            $category['user'] [$j - 1] = $temp;
                        }
                    }
                }
			}
			if($category['user']){
				$brand_num += 1;
				$result_arr['category'][] = $category;
				$result_arr['brand_num'] = $brand_num;
				$result_arr['expert_num'] = $expert_num;
			}
        }
        //echo '<pre>';print_r($result_arr);exit;
        return $result_arr;
    }
	function get_brand_num(){

	}
    function get_by_uid($uid) {
        return $this->db->fetch_array($this->db->query("SELECT * FROM " . DB_TABLEPRE . "expert WHERE `uid`=$uid"));
    }

    function get_by_username($username) {
        return $this->db->fetch_array($this->db->query("SELECT * FROM " . DB_TABLEPRE . "expert WHERE `username`='$username'"));
    }

    function get_by_cid($cid,$start=0,$limit=10) {
        $expertlist = array();
        $query =($cid=='all')?$this->db->query("SELECT uid,username,credit3,answers FROM " . DB_TABLEPRE . "user WHERE uid IN (SELECT uid FROM " . DB_TABLEPRE . "expert) ORDER BY answers DESC LIMIT $start,$limit"):$this->db->query("SELECT uid,username,credit3,answers FROM " . DB_TABLEPRE . "user WHERE uid IN (SELECT uid FROM " . DB_TABLEPRE . "expert WHERE cid=$cid) ORDER BY answers DESC  LIMIT $start,$limit");
        while ($expert = $this->db->fetch_array($query)) {
            $expert['avatar'] = get_avatar_dir($expert['uid']);
            $expertlist[] = $expert;
        }
        return $expertlist;
    }

    function add($uid, $cids, $shop_id) {
        $sql = "INSERT INTO " . DB_TABLEPRE . "expert(`uid`,`cid`, `shop_id`) VALUES ";
        foreach ($cids as $cid) {
            $sql .= "($uid,$cid,$shop_id),";
        }
        $this->db->query(substr($sql, 0, -1));
        $this->db->query("UPDATE " . DB_TABLEPRE . "user SET `expert`=1 WHERE uid=$uid");
    }

    function remove($uids) {
        $this->db->query("DELETE FROM " . DB_TABLEPRE . "expert WHERE uid IN ($uids)");
        $this->db->query("UPDATE " . DB_TABLEPRE . "user SET `expert`=0 WHERE uid IN ($uids)");
    }

    function get_category($uid) {
        $categoryname = array();
        $query = $this->db->query("SELECT c.name,c.id FROM " . DB_TABLEPRE . "category as c," . DB_TABLEPRE . "expert as e WHERE c.id=e.cid AND e.uid=$uid");
        while ($category = $this->db->fetch_array($query)) {
            $categoryname[] = "<a href='index.php?category/view/$category[id].html' target='_blank'>" . $category['name'] . '</a>';
        }
        return implode(",", $categoryname);
    }

    function get_solves($start=0, $limit=20) {
        $solvelist = array();
        $query = $this->db->query("SELECT a.qid,a.title FROM " . DB_TABLEPRE . "answer  as a ,`" . DB_TABLEPRE . "expert` as f WHERE a.authorid=f.uid ORDER BY a.time DESC LIMIT $start ,$limit");
        while ($solve = $this->db->fetch_array($query)) {
            $solvelist[] = $solve;
        }
        return $solvelist;
    }

    function get_solve_answer($uid, $start=0, $limit=3) {
        $solvelist = array();
        $query = $this->db->query("SELECT * FROM `" . DB_TABLEPRE . "answer` WHERE `authorid`=" . $uid . "  ORDER BY `adopttime` DESC,`support` DESC LIMIT $start,$limit");
        while ($solve = $this->db->fetch_array($query)) {
            $solvelist[] = $solve;
        }
        return $solvelist;
    }

}

?>
