<?php 

// 配置类型模块
class ConfigTypeAction extends CommonAction {
    protected $data;
    protected $type = array('2'=>'boolean','4'=>'emnu');
    function getConfigData($name) {
        /*
        if (C($name)) {
            return C($name);
        }
        */
        $model = D('Store/Config');
        $data = $model->where("name='{$name}'")->field('id,name,type,value,extra')->select();
        $this->data = $data[0];
        $fun_name = ucwords($this->type[$this->data['type']])."Param";
        $result = $this->$fun_name();
        if (array_key_exists($this->data['type'],$this->type)) {
            $result = $this->$fun_name();
        }else{
            $result = $this->data['value'];
        }
        C($name,$result);
        return ($result);
    }
    function BooleanParam() {
        $var = explode(",",$this->data['extra']);
        if (count($var)==2) {
            $result = array($var[0]=>FALSE,$var[1]=>TRUE);
            return ($result);
        }else{
            return false;
        }
    }

    function EmnuParam() {

        $var = explode(",",$this->data['extra']);

        if (count($var)>0) {
            foreach ($var as $value) {
                $result[substr($value,0,strpos($value,':'))] = substr($value,strpos($value,':')+1);
            }
            return ($result);
        }else{
            return false;
        }
    }
}
?>