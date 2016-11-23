<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Lib\Image;
use Think\Model;
use Lib\Image\UploadedFile;
use Lib\Helper;
/**
 * 征集 操作数据
 *
 * @author Administrator
 */
class EventModel extends Model{
    /**
     * 展示最新的征集
     */
    public function getHotEvent($limit=2,$test=0){
        $_key='event_model_getHotEvent'.$limit;
        $data=S($_key);
        if($data){
            return $data;
        }
        $data=M('Event')->order("id desc")->limit($limit)->select();
        foreach ($data as $key => $value) {
            $data[$key]['show_image']=UploadedFile::getFileUrl($value['show_image'], array(137, 130), 'event');
            $data[$key]['banner']=UploadedFile::getFileUrl($value['banner'], array(137, 130), 'event');
            $data[$key]['logo']=UploadedFile::getFileUrl($value['logo'], array(137, 130), 'event');
            //事件开始时间
            $begin_time=$value['event_begin_time'];
            //事件结束时间
            $end_time=$value['event_end_time'];
            //当前时间 
            //事件状态 开始的为1 未开始或已结束的为-1
            $data[$key]['statue']=$begin_time > time() || $end_time <= time() ? -1:1;
            $data[$key]['statue_text']=$begin_time > time() ?"未开始":( $end_time <= time()?"已结束":"进行中" );
            //
            $data[$key]['host']=Helper::Utf8Substr($data[$key]['host'],0,14);
            
            
            $data[$key]['event_begin_time']=date('Y/m', $begin_time);
            $data[$key]['event_end_time']=date('Y/m', $end_time);
            /***
             * event_begin_time"] => string(10) "1393862400"
                ["event_end_time"] => string(10) "1430064000
             */
        }
        S($_key,$data);
        return $data;
    }
}