<?php
 
    define('DOMAIN', 'gy.com');
    
    define('IMAGE_SERVER_PATH', 'D:/HuaYun/HuaYun/1.0/yijuanupload');
    define('DB_HOST', 'localhost');
    define('OPERATION_EMAIL', '["wangwp@cyyun.com","fumm@cyyun.com","wangxd@cyyun.com","jiye@cyyun.com","zenglx@cyyun.com"]');

    define('ORGANIZATIONVIPIDS', '48628, 901, 461');


define('UCENTER', 'http://uc.' . DOMAIN);
define('IMAGE_SERVER_URL', 'http://fs.' . DOMAIN);
define('STATIC_SERVER_URL', 'http://static.' . DOMAIN);
define('SERVER_VISIT_URL', 'http://www.'. DOMAIN);
define('YI_JUAN', 'http://yijuan.'. DOMAIN);
define('JIA_SERVER_URL', 'http://jia.'. DOMAIN);

define('validationkey','5f5d9cf812e913e26b12b41e37858c1d');


define("ROOT", '../../../think_gongyi/App');
//sessionID cookie key
define('SESSIONKEY', 'SSID_' . md5(DOMAIN));
define('UCENTERKEY', 'UC_' . md5(DOMAIN));
define('MAP_SCRIPT_URL', 'http://webapi.amap.com/maps?v=1.2&key=0ae9a4c0feb0e451ca18193f3beeda7e');
define('STATIC_VERSION', '2.0.5');
define('API_URL','http://api.'.DOMAIN);