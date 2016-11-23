<?php
return array(
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'interimCache'=>array(
        'label'=>'label-list',  //获取及存放类别标签（社区服务等）的标识
        'labelTime'=>86400,     //类别标签缓存存放的时间
        'area'=>'area-list',
        'areaTime'=>86400,
    ),
    'ERROR_MESSAGE'=>'请求错误',
    'SHOW_ERROR_MSG'=>false,
    'ERROR_PAGE' =>'/m/tpl/dispatch_jump.html',
    'TMPL_ACTION_ERROR' => '../../M/View/tpl:dispatch_jump',
    'TMPL_ACTION_SUCCESS' => '../../M/View/tpl:dispatch_jump',
);