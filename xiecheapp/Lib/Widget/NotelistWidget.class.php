<?php
class NotelistWidget extends Widget{
    public function render($data){
        $Model = D("Notemain");
       	$map['note_type'] = array('EQ',$data['note_type']);
       	$map['main_del'] = 0;
			if(is_array($data)){
				foreach($data AS $key=>$val){
					switch ($key) {
						case 'note_type':
							$map[$key] = array('EQ',$val);
						break;
						case 'u_c_id':
							$map[$key] = array('EQ',$val);
						break;
					}
				}
			}
			//dump($map);
        $data["list"] = $Model->order("happen_time desc")->where($map)->limit(10)->select();
        $content = $this->renderFile('notelist',$data);
        //echo '<pre>';print_r($data);
        return $content;
    }
}
?>