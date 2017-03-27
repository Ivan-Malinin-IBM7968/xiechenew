<?php
$sql_execute_info="";
$total_sql_execute_info=0;
class _xq_mysqli {
    public $queries = 0;
    public $linkID;
    public $debug=0;
    public $host="";
    public $user="";
    public $pass="";
	public $stop_on_error=1;
	public $errors=0;

	public $sum_execute_time = 0;

	function _xq_mysqli() {
        $this->queries = 0;
        $this->linkID='';
	}

	function __destruct() {
        if($this->linkID){
            mysql_close($this->linkID);
        }
		$GLOBALS["total_sql_execute_info"].="|||||".$this->database.":".$this->queries.":".$this->sum_execute_time."|||||<br>";
	}

	function config($db_name=false){
        $this->debug=$config["debug"];

        $this->host='localhost';
        $this->user='root';
        $this->pass='';
		/*
		if($db_name){
        	$this->database=$db_name;
		}else{
			$this->database='tp_xieche';
		}
		*/
		$this->database='tp_xieche';
        $this->port='3306';
        $this->socket='';
        $this->charset='utf8';
		if($config[convert_charset]){
			list($from_charset,$to_charset)=explode(" ",$config[convert_charset]);
			$this->from_charset=$from_charset;
			$this->to_charset=$to_charset;
		}

	}

	function charset($dbcharset)
	{
		$this->connect();
		if(!$this->version) {
			$this->version = floatval(mysql_get_server_info($this->linkID));
		}
		$dbcharset='utf8';
		mysql_query($this->linkID,"set names utf8");
		/*
		if($this->version > 4.1) {

			mysql_query($this->linkID, "SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary") or $this->error("set names error");
			mysql_query($this->linkID,"SET sql_mode=''");
		}else{
			mysql_query($this->linkID,"set names '$dbcharset'");
		}
		*/
		$this->dbcharset=$dbcharset;
	}

    function connect() {
		if(!$this->linkID){

            $this->linkID=mysql_connect($this->host, $this->user, $this->pass) or die("connect error");
			mysql_select_db($this->database,$this->linkID);
			if(!$this->dbcharset) {
				$this->charset($this->charset);
			}

        }
    }
    function query($querysql = '', $returnType = '',$cache_time=0, $debug=false)
    {
		if (preg_match('/union|load_file/i',$querysql)){
			exit();
		}
		global $__xq_mc;
		$mc=$__xq_mc;
		$this->connect();

        if ($querysql != '') {

			$key=md5($querysql);
			//if($cache_time>0) $v=$mc->get($key);
			if($v=="NIL"){
				return "";
			}
		    mysql_query('set names utf8',$this->linkID);
			$result = mysql_query( $querysql,$this->linkID) or $this->error("{$querysql}");

            if ($returnType == '') {
                return $result;
            } elseif ($returnType == '1') {

				if(!$v){
					$row = $this->fetch_row($result);
					$v=$row["0"];
					//if($cache_time>0) $mc->set($key,$v,$cache_time);
					$this->free_result($result);
				}
				return $v;

            } elseif ($returnType == 'row') {
				if(!$v){

					$row=$this->fetch_row($result);
					$this->free_result($result);
					$v=$row;
					if($cache_time>0) $mc->set($key,$v,$cache_time);

				}
				return $v;


            } elseif ($returnType == 'assoc') {

				if(!$v){

					$row = $this->fetch_assoc($result);
					$this->free_result($result);
					$v=$row;
					//if($cache_time>0) $mc->set($key,$v,$cache_time);

				}
				return $v;


            } elseif( $returnType == 'all' ){

				if(!$v){
					 $i=0;
					 while ($row=$this->fetch_assoc($result)){
						 $v[$i]=$row;
						 $i++;
					 }//end while
					 $this->free_result($result);
					//if($cache_time>0) $mc->set($key,$v,$cache_time);
				}
				return $v;
			}
        } else {
            $this->error ('no SQL');
        }
    }

	function escape_string($s){
        $this->connect();
		if(get_magic_quotes_gpc())
		{
			$s=stripslashes($s);
		}
        return mysql_real_escape_string($this->linkID,$s);
    }
	function safe($s){
		return $this->escape_string($s);
	}

	function sql_query($s){
		return mysql_query($s);
    }

    function free_result($res)
    {
        return mysql_free_result($res);
    }

	function convert_array_charset($array)
	{
		if(!$array) {
			return $array;
		}
		if(!$this->to_charset or !$this->from_charset){
			return $array;
		}
		foreach($array as $key=>$value){
			$array_new[$key]=mb_convert_encoding($value,$this->to_charset,$this->from_charset);
		}
		return $array_new;
	}

    function fetch_row($res)
    {
        $row=mysql_fetch_row($res);
		$row=$this->convert_array_charset($row);
		return $row;
    }

    function fetch_assoc($res)
    {
        $row = mysql_fetch_assoc($res);
		$row=$this->convert_array_charset($row);
		return $row;
    }

    function n_rows($result)
    {
        return mysql_num_rows ($result);
    }

    function a_rows()
    {
        $this->connect();
        return mysql_affected_rows ($this->linkID);
    }

    function insert_id()
    {
        $this->connect();
        return mysql_insert_id($this->linkID);
    }

    function error($msg)
    {
		$this->errors++;
		exit();
		//exit("<br><br>".$msg."<br><br>".mysql_error($this->linkID));
    }

}
