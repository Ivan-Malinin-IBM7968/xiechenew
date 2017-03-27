<?php
function js_top_go($msg,$goto) {
$msg=stripslashes($msg);
$msg=str_replace("'","",$msg);
$msg=str_replace("\"","",$msg);
$goto=stripslashes($goto);
$goto=str_replace("'","",$goto);
$goto=str_replace("\"","",$goto);
exit("
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
    alert('$msg');
    top.location='$goto';
//-->
</SCRIPT>
");
}

function js_back($msg){
$msg=stripslashes($msg);
$msg=str_replace("'","",$msg);
$msg=str_replace("\"","",$msg);
exit("
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
    alert('$msg');
    history.go(-1);
//-->
</SCRIPT>
");
}

function js_goto($msg,$goto){
$msg=stripslashes($msg);
$msg=str_replace("'","",$msg);
$msg=str_replace("\"","",$msg);
$goto=stripslashes($goto);
$goto=str_replace("'","",$goto);
$goto=str_replace("\"","",$goto);
exit("
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
    alert('$msg');
    window.location='$goto';
//-->
</SCRIPT>
");
}

function location($url){
exit("�����У�����ˢ�¡�<SCRIPT LANGUAGE=\"JavaScript\">window.location='$url';</SCRIPT><meta http-equiv=\"refresh\" content=\"1; url=$url\">");
}






function form_radio($elname,$nowvalue,$d=array('��'=>1,'��'=>0)){
$x="";
if(count($d)<2){
exit('��������');
}
if(is_array($d)){
    foreach($d as $k=>$v){
        $checked="";
        if($v==$nowvalue){
            $checked=" checked";
        }
        $x.="<INPUT TYPE=\"radio\" NAME=\"$elname\" VALUE='$v' style='border: 0px'$checked>$k &nbsp; ";
    }
}
return $x;
}

function form_textarea($elname,$nowvalue,$rows=8,$cols=60,$readonly=0){
if($readonly){
$readonly=" readonly";
}else{
	$readonly="";
}
return "<TEXTAREA NAME=\"$elname\" ROWS=\"$rows\" COLS=\"$cols\"$readonly>$nowvalue</TEXTAREA>";
}

function form_text($elname,$nowvalue,$size=20,$readonly=0){
if($readonly){
$readonly=" readonly";
}else{
	$readonly="";
}
return "<INPUT TYPE=\"text\" NAME=\"$elname\" size=$size VALUE='$nowvalue'$readonly>";
}

    function gen_space_photo_url($id,$ext,$s=true) {
		global $photo_file_url;
		if($s){
			$s=".s";
		}else{
			$s="";
		}
        $attach_id = strval(floatval($id) + floatval(100000000));
        $dir=$attach_id[0] . $attach_id[1] . $attach_id[2] . "/" . $attach_id[3] . $attach_id[4] . $attach_id[5];
		return $photo_file_url."/".$dir."/".$id."{$s}.".$ext;
    }


function pages($align="center",$pageName,$queryString,$total,$ppp,$page,$botton=null,$input_pagenum=null){
	$totalPage=ceil($total/$ppp);
	if($totalPage==0) $totalPage=1;
	if($page>$totalPage) $page=$totalPage;
	if($page<1) $page=1;
	$pages="<table border=\"0\" width=98% align=center cellpadding=\"0\" cellspacing=\"0\">";
	if(!$botton){
		$pages.="<form name=\"form_$pageName\" METHOD=POST ACTION=\"{$queryString}\">";
	}
	$pages.="<tr><td nowrap align=$align>�� $totalPage ҳ";
	$m=$page-1;if($m<1) $m=1;if($m>$totalPage) $m=$totalPage;
	$n=$page+1;if($n<1) $n=1;if($n>$totalPage) $n=$totalPage;
	if($page>4) $pages.=" <a href=\"{$queryString}&{$pageName}=1\" class=\"bluelink\">&laquo;��ҳ</a>&nbsp;";
		for($i=$page-3;$i<=$page+5;$i++){
			if($i>0 && $i<=$totalPage){
				if($i==$page){
				$c1="<u>";
				$c2="</u>";
				}
				else {
				$c1="";
				$c2="";
				}
			$pages.="&nbsp;<a href=\"{$queryString}&{$pageName}=$i\" class=\"bluelink\">{$c1}{$i}{$c2}</a>";
			}
		}
	if($page<$totalPage-5) $pages.="&nbsp;<a href=\"{$queryString}&{$pageName}=$totalPage\" class=\"bluelink\" >ĩҳ&raquo;</a> ";
	if(!$botton){
		$pages.=" &nbsp;��<INPUT TYPE=\"text\" NAME=\"$pageName\" VALUE=\"$page\" SIZE=3 id=\"pagenum\">ҳ </td></tr></form></table>";
	}else{
		$pages.=" &nbsp;��<INPUT TYPE=\"text\" NAME=\"$pageName\" VALUE=\"$page\" SIZE=3 id=\"$input_pagenum\">ҳ $botton</td></tr></table>";
	
	}
	return $pages;
}







function show_content($s) {
	$s = str_replace(array("\r", "\n", "  "), array("", "<br>", " &nbsp;"), $s);
	return $s;
}
function no_script($s) {
    $s=str_ireplace("document.write","",$s);
    $s=str_ireplace("script","",$s);
    return $s;
}
function no_html($s) {
	$s = str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $s);
    return $s;
}
function cuturl($url) {
	$urllink = "<a href=\"".(substr(strtolower($url), 0, 4) == 'www.' ? "http://$url" : $url).'" target="_blank">'.$url.'</a>';
	return $urllink;
}
function parsebb($string) {

	$pattern = array(
                "/\[quote\](.*?)\[\/quote\]/is",
                "/\[quoter\](.*?)\[\/quoter\]/is",
	);
	$replacement = array(
	            "<table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"#CCCCFF\" align=center><tr><td bgcolor=#FFFFFF>\\1</td></tr></table>",
	            "<div style=\"background-color: #F4F4FF\">\\1</div>",
	);

    $string=preg_replace($pattern,$replacement," ".$string);
    $pattern = array(
        		"/(?<=[^\]a-z0-9-=\"'\\/])(http:\/\/[a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+\.(jpg|gif|png|bmp|jpeg))/is",
			    "/(?<=[^\]\)a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/is",

				"/\[url\]\s*(www.|https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|bctp:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/ies",
				"/\[url=www.([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[url=(https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[url=([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[color=([^\[\<]+?)\]/is",
				"/\[size=(\d+?)\]/is",
				"/\[size=(\d+(\.\d+)?(px|pt|in|cm|mm|pc|em|ex|%)+?)\]/is",
				"/\[font=([^\[\<]+?)\]/is",
				"/\[align=([^\[\<]+?)\]/is",
				"/\[float=([^\[\<]+?)\]/is",
                "/\[fly](.*?)\[\/fly]/is",
                "/\[move](.*?)\[\/move]/is",
	);
	$replacement = array(
                "[img]\\1[/img]",
                "[url]\\1\\3[/url]",
				"cuturl('\\1\\2')",
				"<a href=\"http://www.\\1\" target=\"_blank\">\\2</a>",
				"<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>",
				"<a href=\"\\1\" target=\"_blank\">\\2</a>",
				"<font color=\"\\1\">",
				"<font size=\"\\1\">",
				"<font style=\"font-size: \\1\">",
				"<font face=\"\\1\">",
				"<p align=\"\\1\">",
				"<br style=\"clear: both\"><span style=\"float: \\1;\">",
                "<MARQUEE scrollAmount=3 behavior=alternate width=100%>\\1</MARQUEE>",
                "<MARQUEE scrollAmount=3 width=100%>\\1</MARQUEE>",
	);

    $string=preg_replace($pattern,$replacement,$string,400);

	$img_to="<img onLoad=\"if(this.width>600){this.width=600;}\" src=\\1 border=0 />";
    $pattern="/\[img\](.*?)\[\/img\]/is";
    $string=preg_replace($pattern,$img_to,$string,120);


    $searcharray = array(
        '[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]',
        '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[list]', '[list=1]', '[list=a]',
        '[list=A]', '[*]', '[/list]', '[indent]', '[/indent]', '[/float]',
        '[hr]', '[br]',
    );
    $replacearray = array(
        '</font>', '</font>', '</font>', '</p>', '<b>', '</b>', '<i>',
        '</i>', '<u>', '</u>', '<s>', '</s>', '<ul>', '<ul type=1>', '<ul type=a>',
        '<ul type=A>', '<li>', '</ul>', '<blockquote>', '</blockquote>', '</span>',
        '<hr width=40% align=left siez=1>','<br>',
    );

    $string =str_ireplace($searcharray,$replacearray,$string);

	global $smiles_txt,$smiles_replaceto;
	$string=str_replace($smiles_txt,$smiles_replaceto,$string);

	return $string;
}


