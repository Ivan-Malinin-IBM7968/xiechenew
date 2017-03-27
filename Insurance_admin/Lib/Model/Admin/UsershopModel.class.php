<?php
// 用户模型
class UsershopModel extends CommonModel {
	public function getshoplist(){
	    $shop = $this->db(1,"DB_CONFIG1")->query("SELECT * FROM xc_shop WHERE status='1'");
	    return $shop;
	}
}
?>