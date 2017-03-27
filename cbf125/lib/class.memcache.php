<?php
class mc {
    public $counter=0;

    function __construct() {
        global $memcache_server;
        $this->link=@memcache_connect($memcache_server, 11211,1);
        //if(!$this->link) $this->link=@memcache_connect('10.1.1.21', 11211,5);
    }

    function __destruct() {
        if($GLOBALS["_my_session"]["sessionhash"]){
            $this->set("session_".$GLOBALS["sessionhash"],$GLOBALS["_my_session"],3600);
        }
        @memcache_close($this->link);
        $GLOBALS[debug_info].="\n<!-- mc r:{$this->gettimes} w:{$this->settimes} -->";
    }

    function set($key,$value,$expire=0) {
        $this->settimes++;
        if(!$expire || $expire>=2592000) $expire="2500000";
        @memcache_set($this->link, $key, $value, MEMCACHE_COMPRESSED,$expire);
    }

    function get($key) {
		$this->gettimes++;
        return @memcache_get($this->link, $key);
    }

    function del($key) {
        memcache_delete($this->link, $key);
    }
    function flush(){
        memcache_flush($this->link);
    }

	function increment($key,$num=1)	 {
		return memcache_increment($this->link,$key,$num);
	}

    function status() {
        global $memcache_server;
       $memcache_obj = new Memcache;
       $memcache_obj->addServer($memcache_server, 11211);
       $stats = $memcache_obj->getExtendedStats();
       print_r($stats);
    }

}//end class
$mc=new mc;

