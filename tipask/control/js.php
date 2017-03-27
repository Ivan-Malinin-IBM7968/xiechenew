<?php

!defined('IN_TIPASK') && exit('Access Denied');

class jscontrol extends base {


    function jscontrol(& $get,& $post) {
        $this->base( & $get,& $post);
        $this->load('question');
        $this->load('datacall');
    }

    function onview() {
        $id = intval($this->get[2]);
        $datacall = $_ENV['datacall']->get($id);
        !$datacall && exit(" document.write('非法调用!') ");
        $expressionarr = unserialize($datacall['expression']);
        $jscache=$this->cache->read('js_'.$id,$expressionarr['cachelife']);
        if(!$jscache) {
            $tpl = stripslashes(base64_decode($expressionarr['tpl']));
            $cid=0;
            if('' != $expressionarr['category']) {
                $category =explode(":",substr($expressionarr['category'],0,-1));
                foreach ($category as $cat) {
                    $cat && $cid=$cat;
                }
            }
            $cfield = '';
            if(isset($this->category[$cid])) {
                $category=$this->category[$cid];
                $cfield='cid'.$category['grade'];
            }
            $questionlist = $_ENV['question']->list_by_cfield_cvalue_status($cfield,$cid,$expressionarr['status'],$expressionarr['start'],$expressionarr['limit']);
            $jscache='';
            foreach ($questionlist as $question) {
            	$replaces = array();
            	foreach($question as $qkey => $qval){
            		$replaces["[$qkey]"]=$qval;
            	}
                $replaces['[title]']=cutstr($question['title'],$expressionarr['maxbyte']);
            	$replaces['[qid]']=$question['id'];
      		$replaces['[time]']=tdate($question['time']);
                $jscache.=$this->replacesitevar($tpl,$replaces);
            }
            $this->cache->write('js_'.$id,$jscache);
        }
        echo 'document.write(\''.$jscache.'\')'; 

    }


    function replacesitevar($string, $replaces = array()) {
        $sitevars = array(
            '[site_name]' => $this->setting['site_name'],
            '[site_url]' => SITE_URL,
            '[admin_email]' => $this->setting['admin_email']
        );
        $replaces = array_merge($sitevars, $replaces);
        return str_replace(array_keys($replaces), array_values($replaces), $string);
    }

}
?>