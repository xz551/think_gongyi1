<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/26
 * Time: 14:45
 */

namespace M\Widget;


class ActiveWidget extends WController
{
    public function item($data){
        $this->data=$data;
        $this->display("Widget/Active:item");
    }

}