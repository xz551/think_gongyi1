<?php
namespace TApi\Controller;
use Think\Controller;
class ConcurController extends Controller{
    /**
     * 在访问服务时，判断是否已经过期，如果过期将其设置成结束状态，并拒绝所有请求
     * @param $id concur表id
     */
    public function endConcur($id){
        //获取互助项目信息
        $_w = array(
            'id'     => $id,
        );
        $result = M('Concur')->where($_w)->find();
        //判断concur是否已经全部结束
        if($result && $result['end_time']<= time() && $result['status'] != 888){
            //给互助项目的状态修改成结束
            $_d['status'] = 888;
            M('Concur')->where("id=%d",$id)->save($_d);       
            //拒绝所有还没有通过请求的用户，拒绝理由：该互助项目已经结束
            $_wService = array(
                'concur_id' => $id,
                'status'    => 0,
            );
            $_dService['status'] = -1;
            $_dService['reason'] = '该项目已经结束';
            //查询申请中的服务信息
            $ser = M('concurServiceApply')->where($_wService)->select();
            if($ser) {
                $this->sendMessage($ser,$result['title'],$id,$result['type'],0,$result['creator']);       
            }
            //查询申请中的物资信息
            $sup = M('ConcurSuppliesApply')->where($_wService)->select();
            if($sup){
                $this->sendMessage($sup,$result['title'],$id,$result['type'],1,$result['creator']);   
            }         
            //修改物资相关的请求状态为拒绝
            if($result['is_supplies']){
                M('ConcurSuppliesApply')->where($_wService)->save($_dService);
                $_cw = array('concur_id' => $id, 'supplies'  => '1');
                $_cd = array('supplies'=>-1);
                M('ConcurApply')->where($_cw)->save($_cd); 
            }
            //修改服务相关的请求状态为拒绝
            if($result['is_service']){
                M('concurServiceApply')->where($_wService)->save($_dService);
                $_sw = array('concur_id' => $id, 'service'  => '1');
                $_sd = array('service'=>-1);
                M('ConcurApply')->where($_sw)->save($_sd);
            }
        }
    } 
    
    /**
     * 
     */
    private function sendMessage($data,$title,$id,$type,$tag=0,$creator=0){
        $idName = $tag?'user_id':'apply_uid';
        foreach($data as $v){
            $uidArr[]=$v[$idName];
        }
        if($tag){
            $text = $type?'物资捐助已结束':'物资求助已结束';
        }else{
            $text = $type?'服务捐助已结束':'服务求助已结束';
        }
        //传递邮件模板的数据
        $arr = array(
            'id'    => $id,
            'title' => $title,
            'type'  => $type,
            'tag'   => $tag,
	    'text'  => $text,
            'uid'   => $creator,
        );
	D("T/Notification")->sendMsg($uidArr,'supplies_request_fail',$arr,1);        
    }
    
    
    
    
  
}

