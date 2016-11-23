<?php

return array(
    'URL_MODEL' => '2', //URL模式
    'LAYOUT_ON' => true,
    'DATA_CACHE_PATH'=>'D:/HuaYun/HuaYun/1.0/think_gongyi/App/Runtime/Temp/',
    'LAYOUT_NAME' => 'layout',
    'LOAD_EXT_CONFIG' => 'r,domain',
    'MULTI_MODULE'          =>true,
    'MODULE_ALLOW_LIST'   =>    array('T','M','Zhuanti','Api','Admin','TApi','Uc','Wx'),
    'DEFAULT_MODULE'       =>    'T',
    'DATA_CACHE_TIME'=>300,
    'URL_KEY'=>'ZnDI73TywuRXfGv8Ozjte51dwh7gvxau',
  //  'ER_WEI_MA_LOGO'=>false,
    'API_TOKEN'=>'POLkfoGJRHgjg0215JJGIMM',
//    'LOG_RECORD' => true, // 开启日志记录
//    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
//    'ERROR_MESSAGE'=>'请求错误',
//    'SHOW_ERROR_MSG'=>true,
//    'ERROR_PAGE' =>'/t/tpl/dispatch_jump.html',
//    'TMPL_ACTION_ERROR' => '../../T/View/Tpl:dispatch_jump',
//    'TMPL_ACTION_SUCCESS' => '../../T/View/Tpl:dispatch_jump',
	'URL_ROUTER_ON'   => true, //开启路由
	'COOKIE_DOMAIN'=>'gy.com',
    'SHOW_PAGE_TRACE' =>true
);