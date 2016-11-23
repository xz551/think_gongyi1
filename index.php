<?php
header("Content-type:text/html;charset=utf-8");
if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('require PHP > 5.3.0 !'); 
define('APP_DEBUG', false); 
define('APP_PATH', 'App/');
define('BUILD_DIR_SECURE', false);
define("__CSS__", "Public/css");
define("__JS__", "Public/js"); 
define("__STATIC_URL__", "http://static.yiii.org");
require '../ThinkPHP/ThinkPHP.php';