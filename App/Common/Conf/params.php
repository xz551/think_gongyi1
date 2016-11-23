<?php

return array(
    'CALLBACK'=>SERVER_VISIT_URL.'/Oauth/callback',
    'front_url' => SERVER_VISIT_URL,
    //资源服务器地址
    'static_url' => STATIC_SERVER_URL,
    //用户中心URL
    'ucenter_url' => UCENTER,
    'sessionKey' => SESSIONKEY,
    'ucenterKey' => UCENTERKEY,
    //上传图片服务器配置
    'uploadImages' => array(
        'server' => array(
            'fs' => IMAGE_SERVER_URL,
        ),
        'path' => array(
            //项目相关图片
            'project' => IMAGE_SERVER_PATH . '/project',
            'user' => IMAGE_SERVER_PATH . '/user',
            'idcard' => IMAGE_SERVER_PATH . '/idcard',
            'organization' => IMAGE_SERVER_PATH . '/organization',
            'team' => IMAGE_SERVER_PATH . '/team',
            'event' => IMAGE_SERVER_PATH . '/event',
            'support' => IMAGE_SERVER_PATH . '/support',
            'banner' => IMAGE_SERVER_PATH . '/banner',
            'active' => IMAGE_SERVER_PATH . '/active'
        ),
        'size' => array(
            //百度编辑器 项目简介图片设置默认尺寸
            'project' => array('RESIZE_BY_WIDTH', array(686, 0)),
            'active' => array('RESIZE_BY_WIDTH', array(686, 0))
        ),
        'placeholder' => array(
            'project' => STATIC_SERVER_URL . '/public/images/project_default.png',
            'user' => STATIC_SERVER_URL . '/public/images/user_default.png',
            'user_girl' => STATIC_SERVER_URL . '/public/images/user_default_girl.png',
            'event' => STATIC_SERVER_URL . '/public/images/project_default.png',
            'banner' => STATIC_SERVER_URL . '/public/images/banner_default.jpg',
            'active' => STATIC_SERVER_URL . '/public/images/project_default.png',
            'default' => "javascript:;",
        ),
        'maxFileSize' => 1024 * 1024,
        'enableExt' => array('jpg', 'jpeg', 'png')
    ),
    'video' => array(
        'charset' => 'utf-8',
        'user_id' => '90C1920E4883E0D7',
        'key' => 'OQzF4lgFAmK4HQk2lZLGhz8LguImQWXi',
        'api_videos' => 'http://spark.bokecc.com/api/videos',
        'api_user' => 'http://spark.bokecc.com/api/user',
        'api_playcode' => 'http://spark.bokecc.com/api/video/playcode',
        'api_deletevideo' => 'http://spark.bokecc.com/api/video/delete',
        'api_editvideo' => 'http://spark.bokecc.com/api/video/update',
        'api_video' => 'http://spark.bokecc.com/api/video',
        'api_category' => 'http://spark.bokecc.com/api/video/category',
        'notify_url' => SERVER_VISIT_URL . '/video/notify',
        'default_image' => IMAGE_SERVER_URL . '/video/default.png'
    ),
    'operationEmail' => OPERATION_EMAIL
);
