<?php
class root {

    function __construct(){
                global $_root_init_class,$_root_init_table;
                foreach($_root_init_class as $key=>$value){
                        $this->$key=$value;
                }
                foreach($_root_init_table as $key=>$value){
                        $this->$key=$value;
                }
    }

    function __destruct(){
    }


}

?>