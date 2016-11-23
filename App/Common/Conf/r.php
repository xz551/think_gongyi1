<?php
C('require',array('db_yijuan','db_user_conter','db_cms','define','params','tmpl_parse','send_mail'));
C('THINK_PUBLIC','D:/HuaYun/HuaYun/1.0/think_public');
return require_once(C('THINK_PUBLIC').'/c/index.php');