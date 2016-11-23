<?php
namespace T\Widget;
class EditWidget extends WController{
    public function textarea($data){
        $this->name=$data['name'];
        $this->value=$data['value'];
        $this->cols=$data['cols'];
        $this->rows=$data['rows'];
        $this->id=$data['id'];
        $this->display("Widget/Edit:textarea");
    }
}