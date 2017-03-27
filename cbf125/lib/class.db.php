<?php

class mysql {
    public $queries = 0;
    private $linkID;
    private $debug=0;
    private $host="";
    private $user="";
    private $pass="";
	private $stop_on_error=1;
	private $errors=0;

	function __construct($db_config) {
        $this->debug = 1;
        $this->queries = 0;
        $this->linkID='';
        $this->host=$db_config["host"];
        $this->user=$db_config["user"];
        $this->pass=$db_config["pass"];
        $this->database=$db_config["database"];
        $this->port=$db_config["port"];
        $this->socket=$db_config["socket"];
	}

	function __destruct() {
        if($this->linkID){
            mysqli_close($this->linkID);
        }
	}

    function connect() {
        if(!$this->linkID){
            $this->linkID=mysqli_connect($this->host, $this->user, $this->pass,$this->database,$this->port,$this->socket) or die("<div style=\"text-align:left;padding:100px;background:#ffffe1;border:1px solid #ccc;color:blue;font-size: 9pt;\">服务器忙: 无法连接到数据库服务器。{$this->host}</div>");

            $dbcharset="utf8";
            $this->version = floatval(mysqli_get_server_info($this->linkID));
                if($this->version > 4.1) {
                mysqli_query($this->linkID, "SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary") or $this->error("set names error");
                mysqli_query($this->linkID,"SET sql_mode=''");
            }else{
                mysqli_query($this->linkID,"set names '$dbcharset'");
            }

        }
    }

    function query($querysql = '', $returnType = '')
    {
        if($GLOBALS[sess][user_id]==47233){
                //echo "<!-- ".$querysql." -->\r\n";
        }
        $this->connect();
        if ($querysql != '') {
            $start_time=explode(" ",microtime());
            $start_time=floatval($start_time[0])+floatval($start_time[1]);
            $result = mysqli_query($this->linkID, $querysql) or $this->error("{$querysql}");
            $this->queries++;
            $end_time=explode(" ",microtime());
            $end_time=floatval($end_time[0])+floatval($end_time[1]);
            $execute_time=$end_time-$start_time;
            if($GLOBALS[sess][user_id]==47233){
                //echo "$querysql - $execute_time<br>\r\n";
            }

            if ($returnType == '') {
                return $result;
            } elseif ($returnType == '1') {
                $row = $this->fetch_row($result);
                $this->free_result($result);
                return $row["0"];
            } elseif ($returnType == 'row') {
                $row=$this->fetch_row($result);
                $this->free_result($result);
                return $row;
            } elseif ($returnType == 'assoc') {
                $row = $this->fetch_assoc($result);
                $this->free_result($result);
                return $row;
            } elseif( $returnType == 'all' ){
				 $i=0;
				 while ($row=$this->fetch_assoc($result)){
				 $a[$i]=$row;
				 $i++;
				 }//end while
                 $this->free_result($result);
				 return $a;
			}
        } else {
            $this->error ('no SQL');
        }
    }

    function many_query($q){
        $this->connect();
        if(is_array($q)){
            foreach($q as $qs){
                $qs=trim($qs);
                if($qs){
                    mysqli_query($this->linkID,$qs) or $this->error("error : {$qs}");
                    $this->queries++;
                }
            }
        }
        else{
            $this->error ('no SQL');
        }
    }

	function escape_string($s){
        $this->connect();
        return mysqli_real_escape_string($this->linkID,$s);
    }
	function safe($s){
        if(get_magic_quotes_gpc()){
            $s=stripslashes($s);
        }
		return $this->escape_string($s);
	}

	function sql_query($s){
    return mysqli_query($s);
    }

    function free_result($res)
    {
        return mysqli_free_result($res);
    }

    function fetch_row($res)
    {
        return mysqli_fetch_row($res);
    }

    function fetch_assoc($res)
    {
        return mysqli_fetch_assoc($res);
    }

    function fetch_object($res)
    {
        return mysqli_fetch_object($res);
    }

    function n_rows($result)
    {
        return mysqli_num_rows ($result);
    }

    function a_rows()
    {
        $this->connect();
        return mysqli_affected_rows ($this->linkID);
    }

    function insert_id()
    {
        $this->connect();
        return mysqli_insert_id($this->linkID);
    }

    function error($msg)
    {
        $this->connect();
		$this->errors++;
        exit("数据库错误。记住你现在在做什么，在访问什么页面，帮助我们纠正错误，谢谢~<br>".$msg."<br>".mysqli_error($this->linkID));

    }
}
