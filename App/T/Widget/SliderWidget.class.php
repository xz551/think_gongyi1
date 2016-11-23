<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Widget;
use Lib\Image\UploadedFile;
use T\Model\BannerModel;
/**
 * Description of SliderWidget
 *
 * @author Administrator
 */
class SliderWidget extends WController {

    //put your code here
    public function init() {
     layout(false);
        $now = time();
        $key='slider_init';
        $data=S($key);
        if(!$data) {
            $data = M('banner')->
            where('start_time <=' . $now . ' and (end_time >= ' . $now . ' or end_time is null or end_time = "") and status=' . BannerModel::BANNER_STATUS_SHOW)
                ->order('sort desc,create_date desc,id desc')
                ->select();
            $data=$this->imagePath($data);
            S($key,$data);
        }
        $this->assign('data', $data);
        $this->display("Widget/Slider:index");
    }
    private function imagePath($data){
        foreach ($data as $key => $value) {
            $image=$value['image'];
            $data[$key]['image']= UploadedFile::getFileUrl($image,array(760,400),'banner');
        }
        return $data;
    }

}
