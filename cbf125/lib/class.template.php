<?php
class template {
    // Configuration variables
    private $base_path = '';
    private $reset_vars = TRUE;

    // Delimeters for regular tags
    private $ldelim = '{$';
    private $rdelim = '}';

    // Delimeters for beginnings of loops
    private $BAldelim = '<!--';
    private $BArdelim = '-->';

    // Delimeters for ends of loops
    private $EAldelim = '<!--/';
    private $EArdelim = '-->';

    // Internal variables
    private $scalars = array();
    private $arrays  = array();
    private $carrays = array();
    private $ifs     = array();

    private $contents="";
    /*--------------------------------------------------------------*\
        Method: bTemplate()
        Simply sets the base path (if you don't set the default).
    \*--------------------------------------------------------------*/
    function __construct($base_path = NULL, $reset_vars = TRUE) {
        if($base_path) $this->base_path = $base_path;
        $this->reset_vars = $reset_vars;
    }

	function set_base_path($path) {
		$this->base_path = $path;
	}
    /*--------------------------------------------------------------*\
        Method: set()
        Sets all types of variables (scalar, loop, hash).
    \*--------------------------------------------------------------*/
    function set($tag, $var, $if = NULL) {
        if(is_array($var)) {
            $this->arrays[$tag] = $var;
            if($if) {
                $result = $var ? TRUE : FALSE;
                $this->ifs[] = $tag;
                $this->scalars[$tag] = $result;
            }
        }
        else {
            $this->scalars[$tag] = $var;
            if($if) $this->ifs[] = $tag;
        }
    }

    /*--------------------------------------------------------------*\
        Method: set_cloop()
        Sets a cloop (case loop).
    \*--------------------------------------------------------------*/
    function set_cloop($tag, $array, $cases) {
        $this->carrays[$tag] = array(
            'array' => $array,
            'cases' => $cases);
    }

    /*--------------------------------------------------------------*\
        Method: reset_vars()
        Resets the template variables.
    \*--------------------------------------------------------------*/
    function reset_vars($scalars, $arrays, $carrays, $ifs) {
        if($scalars) $this->scalars = array();
        if($arrays)  $this->arrays  = array();
        if($carrays) $this->carrays = array();
        if($ifs)     $this->ifs     = array();
    }
    function reset(){
        $this->reset_vars(true,true,true,true);
    }
    /*--------------------------------------------------------------*\
        Method: get_tags()
        Formats the tags & returns a two-element array.
    \*--------------------------------------------------------------*/
    function get_tags($tag, $directive) {
        $tags['b'] = $this->BAldelim . $directive . $tag . $this->BArdelim;
        $tags['e'] = $this->EAldelim . $directive . $tag . $this->EArdelim;
        return $tags;
    }

    /*--------------------------------------------------------------*\
        Method: get_tag()
        Formats a tag for a scalar.
    \*--------------------------------------------------------------*/
    function get_tag($tag) {
        return $this->ldelim . $tag . $this->rdelim;
    }

    /*--------------------------------------------------------------*\
        Method: get_statement()
        Extracts a portion of a template.
    \*--------------------------------------------------------------*/
    function get_statement($t, &$contents) {
        // Locate the statement
        $tag_length = strlen($t['b']);
        $fpos = strpos($contents, $t['b']) + $tag_length;
        $lpos = strpos($contents, $t['e']);
        $length = $lpos - $fpos;

        // Extract & return the statement
        return substr($contents, $fpos, $length);
    }

    /*--------------------------------------------------------------*\
        Method: parse()
        Parses all variables into the template.
    \*--------------------------------------------------------------*/
    function parse($contents) {
        // Process the ifs
        if(!empty($this->ifs)) {
            foreach($this->ifs as $value) {
                $contents = $this->parse_if($value, $contents);
            }
        }

        // Process the scalars
        foreach($this->scalars as $key => $value) {
            $contents = str_replace($this->get_tag($key), $value, $contents);
        }

        // Process the arrays
        foreach($this->arrays as $key => $array) {
            $contents = $this->parse_loop($key, $array, $contents);
        }

        // Process the carrays
        foreach($this->carrays as $key => $array) {
            $contents = $this->parse_cloop($key, $array, $contents);
        }

        // Reset the arrays
        if($this->reset_vars) $this->reset_vars(FALSE, TRUE, TRUE, FALSE);

        // Return the contents
        return $contents;
    }

    /*--------------------------------------------------------------*\
        Method: parse_if()
        Parses an if statement.  There is some weirdness here because
        the <else:tag> tag doesn't conform to convention, so some
        things have to be done manually.
    \*--------------------------------------------------------------*/
    function parse_if($tag, $contents) {
        // Get the tags

        // Get the entire statement
        $t = $this->get_tags($tag, 'if:');
        $num=substr_count($contents,$t[b]);
        if($num){
            for($i=0;$i<$num;$i++){
                $entire_statement = $this->get_statement($t, $contents);
                // Get the else tag
                $tags['b'] = NULL;
                $tags['e'] = $this->BAldelim . 'else:' . $tag . $this->BArdelim;

                // See if there's an else statement
                if(($else = strpos($entire_statement, $tags['e']))) {
                    // Get the if statement
                    $if = $this->get_statement($tags, $entire_statement);

                    // Get the else statement
                    $else = substr($entire_statement, $else + strlen($tags['e']));
                }
                else {
                    $else = NULL;
                    $if = $entire_statement;
                }

                // Process the if statement
                $this->scalars[$tag] ? $replace = $if : $replace = $else;

                // Parse & return the template
                $contents=str_replace($t['b'] . $entire_statement . $t['e'], $replace, $contents);
            }
        }
        return $contents;
    }

    /*--------------------------------------------------------------*\
        Method: parse_loop()
        Parses a loop (recursive function).
    \*--------------------------------------------------------------*/
    function parse_loop($tag, $array, $contents) {
        // Get the tags & loop
        $t = $this->get_tags($tag, 'loop:');
        $loop = $this->get_statement($t, $contents);
        $parsed = NULL;

        // Process the loop
        foreach($array as $key => $value) {
            if(is_numeric($key) && is_array($value)) {
                $i = $loop;
                foreach($value as $key2 => $value2) {
                    if(!is_array($value2)) {
                        // Replace associative array tags
                        $i = str_replace($this->get_tag($tag . '.' . $key2), $value2, $i);
                    }
                    else {
                        // Check to see if it's a nested loop
                        $i = $this->parse_loop($tag . '.' . $key2, $value2, $i);
                    }
                }
            }
            elseif(is_string($key) && !is_array($value)) {
                $contents = str_replace($this->get_tag($tag . '.' . $key), $value, $contents);
            }
            elseif(!is_array($value)) {
                $i = str_replace($this->get_tag($tag . '[]'), $value, $loop);
            }

            // Add the parsed iteration
            if(isset($i)) $parsed .= rtrim($i);
        }

        // Parse & return the final loop
        return str_replace($t['b'] . $loop . $t['e'], $parsed, $contents);
    }

    /*--------------------------------------------------------------*\
        Method: parse_cloop()
        Parses a cloop (case loop) (recursive function).
    \*--------------------------------------------------------------*/
    function parse_cloop($tag, $array, $contents) {
        // Get the tags & loop
        $t = $this->get_tags($tag, 'cloop:');
        $loop = $this->get_statement($t, $contents);

        // Set up the cases
        $array['cases'][] = 'default';
        $case_content = array();
        $parsed = NULL;

        // Get the case strings
        foreach($array['cases'] as $case) {
            $ctags[$case] = $this->get_tags($case, 'case:');
            $case_content[$case] = $this->get_statement($ctags[$case], $loop);
        }

        // Process the loop
        foreach($array['array'] as $key => $value) {
            if(is_numeric($key) && is_array($value)) {
                // Set up the cases
                if(isset($value['case'])) $current_case = $value['case'];
                else $current_case = 'default';
                unset($value['case']);
                $i = $case_content[$current_case];

                // Loop through each value
                foreach($value as $key2 => $value2) {
                    $i = str_replace($this->get_tag($tag . '[].' . $key2), $value2, $i);
                }
            }

            // Add the parsed iteration
            $parsed .= rtrim($i);
        }

        // Parse & return the final loop
        return str_replace($t['b'] . $loop . $t['e'], $parsed, $contents);
    }

    function files($filelist) {
        // Prepare the path
        $filelist=@explode(" ",trim($filelist));
        foreach($filelist as $file) {
            if($file){
                $this->cached_files++;
                if($GLOBALS[debug_status]==true){

                }else{
                    $cache=$GLOBALS[mc]->get($file.$this->tag);
                }
                if($cache) {
                $this->contents .= $cache;
                }else{
                $filef = $this->base_path . $file;
                $fp = fopen($filef, 'rb');
				$content="";
                if(!$fp){
                    exit("Can not read $file");
                }
				while (!feof($fp)) {
				  $content .= fread($fp, 8192);
				}

                //$GLOBALS[mc]->set($file.$this->tag,$content,180);
                $this->contents .=$content;
                fclose($fp);
                $this->readed_files++;
                }
            }
        }
    }


    function url_files($filelist) {
        // Prepare the path
        $filelist=@explode(" ",trim($filelist));
        foreach($filelist as $file) {
            if($file){
                $this->cached_files++;
                if($GLOBALS[debug_status]==true){

                }else{
                    $cache=$GLOBALS[mc]->get($file.$this->tag);
                }
                if($cache) {
                $this->contents .= $cache;
                }else{
                $filef = $file;
                $fp = fopen($filef, 'rb');
                if(!$fp){
                    exit("Can not read $file");
                }
				$content="";
				while (!feof($fp)) {
				  $content .= fread($fp, 8192);
				}

                $GLOBALS[mc]->set($file.$this->tag,$content,180);
                $this->contents .=$content;
                fclose($fp);
                $this->readed_files++;
                }
            }
        }
    }


    /*--------------------------------------------------------------*\
        Method: fetch()
        Returns the parsed contents of the specified template.
    \*--------------------------------------------------------------*/

    function output() {
        echo $this->parse($this->contents);
    }
    function get(){
        $x=$this->parse($this->contents);
        $this->reset();
        return $x;
    }
    function clear() {
        $this->contents="";
    }
    function join($s){
        $this->contents.=$s;
    }
    function set_file($tag,$file) {
        $tag=$this->get_tag($tag);
        $filef = $this->base_path . $file;

		$cache=$GLOBALS[mc]->get($file.$this->tag);
		if(!$cache){
			$fp = fopen($filef, 'rb');
			$content="";
			if(!$fp){
				exit("Can not read $file");
			}
			while (!feof($fp)) {
			  $content .= fread($fp, 8192);
			}
			fclose($fp);
			$GLOBALS[mc]->set($file.$this->tag,$content,180);
		}else{
			$content=$cache;
		}

        $this->contents=str_replace($tag,$content,$this->contents);
    }

    function set_url_file($tag,$file) {
        $tag=$this->get_tag($tag);
        $filef = $file;

		$cache=$GLOBALS[mc]->get($file.$this->tag);
		if(!$cache){
			$fp = fopen($filef, 'rb');
			$content="";
			if(!$fp){
				exit("Can not read $file");
			}
			while (!feof($fp)) {
			  $content .= fread($fp, 8192);
			}
			fclose($fp);
			$GLOBALS[mc]->set($file.$this->tag,$content,180);
		}else{
			$content=$cache;
		}

        $this->contents=str_replace($tag,$content,$this->contents);
    }
}


//plugin
//数组分割函数
// $key 返回的二维数组的key
//$tds 要分割的数组
//$num 每组分割成几个
//empty_array， 格式相当于$tds的某个元素， 当tds数组不能整除时，避免模板内容出现变量
function split_array($key,$tds,$num,$empty_array=array()){
    $i=0;$fi=0;
    $tds_size=count($tds);
    $array_num=ceil($tds_size/$num);
    if(is_array($tds)){
        foreach($tds  as $td){
            $xtd[]=$td;
            $i++;
            if($i%$num==0){
                $forum_row[$fi][$key]=$xtd;
                $fi++;
                $xtd="";
            }
        }
    }
    if($fi<$array_num){
        while(@count($xtd)<$num){
            $xtd[]=$empty_array;
        }
        $forum_row[$fi][$key]=$xtd;
    }
    return $forum_row;
}





$t=new template("tps/default/");
$t->tag="moto_index_templates";
$__t=clone $t;
