<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

class Page {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow	;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =	array('prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','header'=>'条记录','theme'=>'%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%<li class=all><a>共%totalRow%  %header% <a>%nowPage%/%totalPage% 页</a></li>');
	protected $config2  =	array('prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','header'=>'条记录','theme'=>'%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%  [<a>共%totalRow%  %header% %nowPage%/%totalPage% 页</a>]');
	protected $configapp  =	array('prev'=>'上一页','header'=>'条记录','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>'%upPage%  <div id=totle-page> %nowPage%/%totalPage% 页</div> %downPage%  %first%  %prePage%  %linkPage%  %nextPage% %end%');  
    // 默认分页变量名
    protected $varPage;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows='',$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
		//echo $this->varPage;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
	
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_REQUEST[$this->varPage])?intval($_REQUEST[$this->varPage]):1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
	 * @此方法用于网站前台
     +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage/$this->rollPage);
		

		$url_p = explode("-p-",$_SERVER['REQUEST_URI']);
		if(substr($url_p[0],-1) == "/" ) {
			$url_p[0] = substr($url_p[0] , 0 , -1);
		}

		$url_array = explode("/",$_SERVER['REQUEST_URI']);
		if($url_array[1] == "order" || $url_array[1] == "coupon") {
			if($url_array[2] == "") {
				$is_method = "/index";	
			}
		}
		
		//伪静态.html去掉后加p 再加.html
		
		if(substr($url_p[0],-5) == ".html" ) {
			$url_p[0] = substr($url_p[0] , 0 , -5);
			$IS_HTML_SUFFIX = ".html";
		}
		if(substr($url_p[1],-5) == ".html") {
			$IS_HTML_SUFFIX = ".html";
		}
        $url  =  $url_p[0].$is_method."-".$this->parameter;
		//$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'/')?'':"/").$this->parameter;
		$parse = parse_url($url);

		$parse['path'] =  substr($parse['path'],0,-5);

         if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<li class='next'><a href='".$url.$p."-$upRow".$IS_HTML_SUFFIX."'><".$this->config['prev']."</a></li>";
        }else{
		 $upPage="<li class='nexthui'><a href='###'><".$this->config['prev']."</a></li>";//上一页变灰空链
        }

        if ($downRow <= $this->totalPages){
            $downPage="<li class='next'><a href='".$url.$p."-$downRow".$IS_HTML_SUFFIX."'>".$this->config['next']."></a></li>";
        }else{
            $downPage="<li class='nexthui'><a href='###'>".$this->config['next']."></a></li>";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            //$prePage = "<li class='pre preDisabled' title='现在是第一页'><a href='".$url.$p."-$preRow".$IS_HTML_SUFFIX."' >上".$this->rollPage."页</a></li>";
            $theFirst = "<li class='next'><a href='".$url.$p."-1".$IS_HTML_SUFFIX."' >".$this->config['first']."</a></li>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            //$nextPage = "<li class='next'><a href='".$url.$p."-$nextRow".$IS_HTML_SUFFIX."' >下".$this->rollPage."页</a></li>";
            $theEnd = "<li class='last'><a href='".$url.$p."-$theEndRow".$IS_HTML_SUFFIX."' >".$this->config['last']."</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
			
            if($page!=$this->nowPage){
				
                if($page<=$this->totalPages){
						$linkPage .= "<li ><a href='".$url.$p."-$page".$IS_HTML_SUFFIX."'>&nbsp;".$page."</a></li>";
                    
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
						$linkPage .= "<li class='ck'><a href='".$url.$p."-$page".$IS_HTML_SUFFIX."'>&nbsp;".$page."</a></li>";
                }
            }

        }
        $pageStr	 =	 str_replace(
            array('%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%','%header%'),
            array($this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd,$this->config['header']),$this->config['theme']);
        return $pageStr;
    }
	 
	 /***
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
	 * @author chf
	 * @此方法用于商家后台和网站后台
     +----------------------------------------------------------
     */
    public function show_admin() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
		
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
		
		if(substr($_SERVER['REQUEST_URI'],-1) != "/" ) {
			$str = "/";
		}
		$url_p = explode("/p/",$_SERVER['REQUEST_URI']);
		$url_array = explode("/",$_SERVER['REQUEST_URI']);

		if($url_array[1] == "order" || $url_array[1] == "coupon") {
			if($url_array[2] == "") {
				$is_method = "index/";	
			}
		}
        $url  =  $url_p[0].$str.$is_method.$this->parameter;
		//$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'/')?'':"/").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<a href='".$url.$p."/$upRow'>".$this->config['prev']."</a>";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="<a href='".$url.$p."/$downRow'>".$this->config['next']."</a>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url.$p."/$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url.$p."/1' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url.$p."/$nextRow' >下".$this->rollPage."页</a>";
            $theEnd = "<a href='".$url.$p."/$theEndRow' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
		
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "<a href='".$url.$p."/$page'>&nbsp;".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "<a href='".$url.$p."/$page'>&nbsp;".$page."</a>";
                }
            }
        }
        $pageStr	 =	 str_replace(
            array('%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%','%header%'),
            array($this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd,$this->config2['header']),$this->config2['theme']);
        return $pageStr;
    }

	/**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
	 * @此方法用于网站前台
     +----------------------------------------------------------
     */
    public function show_app() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage/$this->rollPage);
		

		$url_p = explode("-p-",$_SERVER['REQUEST_URI']);
		if(substr($url_p[0],-1) == "/" ) {
			$url_p[0] = substr($url_p[0] , 0 , -1);
		}

		$url_array = explode("/",$_SERVER['REQUEST_URI']);
		if($url_array[1] == "order" || $url_array[1] == "coupon") {
			if($url_array[2] == "") {
				$is_method = "/index";	
			}
		}
		
		//伪静态.html去掉后加p 再加.html
		
		if(substr($url_p[0],-5) == ".html" ) {
			$url_p[0] = substr($url_p[0] , 0 , -5);
			$IS_HTML_SUFFIX = ".html";
		}
		if(substr($url_p[1],-5) == ".html") {
			$IS_HTML_SUFFIX = ".html";
		}
        $url  =  $url_p[0].$is_method."-".$this->parameter;
		//$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'/')?'':"/").$this->parameter;
		$parse = parse_url($url);

		$parse['path'] =  substr($parse['path'],0,-5);

         if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<div id='prev-page' class='page-trigger'><a href='".$url.$p."-$upRow".$IS_HTML_SUFFIX."'rel='external'>".$this->config['prev']."</a></div>";
        }else{
		 $upPage="<div id='prev-page' class='page-trigger'><a href='###'>".$this->config['prev']."</a></div>";//上一页变灰空链
        }

        if ($downRow <= $this->totalPages){
            $downPage="<div id='next-page' class='page-trigger'><a href='".$url.$p."-$downRow".$IS_HTML_SUFFIX."'rel='external'>".$this->config['next']."</a></div>";
        }else{
            $downPage="<div id='next-page' class='page-trigger'><a href='###'>".$this->config['next']."></a></div>";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            //$prePage = "<li class='pre preDisabled' title='现在是第一页'><a href='".$url.$p."-$preRow".$IS_HTML_SUFFIX."' >上".$this->rollPage."页</a></li>";
            $theFirst = "<li id='totle-page'><a href='".$url.$p."-1".$IS_HTML_SUFFIX."'rel='external'>".$this->config['first']."</a></li>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            //$nextPage = "<li class='next'><a href='".$url.$p."-$nextRow".$IS_HTML_SUFFIX."' >下".$this->rollPage."页</a></li>";
          //  $theEnd = "<li class='last'><a href='".$url.$p."-$theEndRow".$IS_HTML_SUFFIX."' >".$this->config['last']."</a></li>";
        }
        // 1 2 3 4 5
       /*$linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
			
            if($page!=$this->nowPage){
				
                if($page<=$this->totalPages){
						$linkPage .= "<li ><a href='".$url.$p."-$page".$IS_HTML_SUFFIX."'>&nbsp;".$page."</a></li>";
                    
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
						$linkPage .= "<li class='ck'><a href='".$url.$p."-$page".$IS_HTML_SUFFIX."'>&nbsp;".$page."</a></li>";
                }
            }

        }*/

        $pageStr	 =	 str_replace(
            array('%nowPage%','%totalRow%','%totalPage%','%upPage%','%header%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->nowPage,$this->totalRows,$this->totalPages,$upPage,$this->config['header'],$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->configapp['theme']);
        return $pageStr;
    }

}