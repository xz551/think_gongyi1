<?php
/**
 * 域名部署配置
 * 格式1: '子域名或泛域名或IP'=> '模块名[/控制器名]';
 * 格式2: '子域名或泛域名或IP'=> array('模块名[/控制器名]','var1=a&var2=b&var3=*');
*/
return array(
    'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名或者IP配置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        MOBILE_DOMAIN=>'M'
    )
);