<?php

require_once("function.php");


$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
$postData = (array) $postObj;

save_notice(serialize($postData));

echo "success";
exit;