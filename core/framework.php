<?php
/**********************************
*      基础框架
* @file          framework.php
* @package       framework
* @author        xhg
* @version       1.0.0
* @date          2016-02-22
* @link
**********************************/

//定义框架基本常量
define('IN_FW', TRUE);
//当前框架入口所在的文件路径
define('FW_PATH',     dirname(__FILE__) . '/');
if(!defined("SCRIPT_IN")){
    defined('ROOT_PATH')  or define('ROOT_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
    defined('SITE_PATH')  or define('SITE_PATH',       $_SERVER['DOCUMENT_ROOT'].'/');
    //正在被访问的文件URL
    define('HTTP_HOST', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0,
                strpos($_SERVER['SERVER_PROTOCOL'], '/')))).'://'.$_SERVER['HTTP_HOST']
                .substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
    //REQUEST_URI
    define('REQUEST_URI', isset($_SERVER['REQUEST_URI'])
                ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['argv'])
                ? $_SERVER['PHP_SELF'].'?'.$_SERVER['argv'][0]
                : $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING']));

    define('SELF_URL', HTTP_HOST.'/'.basename($_SERVER['PHP_SELF']));
}
//禁止对全局变量注入
if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
    exit('Request tainting attempted.');
}
// 加载核心framework类
require_once FW_PATH.'class/Framework.class.php';
require_once FW_PATH.'functions/function.php';  //系统函数库
// 应用初始化 
framework::start();
/**********************************

**********************************/