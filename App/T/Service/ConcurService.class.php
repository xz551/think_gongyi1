<?php
namespace T\Service;
use T\Model\ConcurModel;
use Lib\UserSession;

/**
 * concur业务逻辑
 *
 * @author Administrator
 */
class ConcurService {
     //验证是否为当前用户发的求助
    static function checkIsOwnConcur($id) {
        $_w = array('id' => I('id'), 'creator' => UserSession::getUser('uid'));
        $r = M('Concur')->where($_w)->find();
        if (!$r) {
            return array('status'=>-1,'message'=>'禁止操作');  //禁止操作
        }else{
            if($r['status'] == ConcurModel::STATUS_VERIFY_DENY || $r['status']==ConcurModel::STATUS_WAITFORCHECK ||  $r['status']==ConcurModel::STATUS_EDITING){
                return $r;
            }else{
                return array('status'=>-1,'message'=>'项目不可修改'); 
            }
        }
    }
    
     /**
     * 物资的添加或修改
     * @param $update   需要更新的数组键值对
     * @param $cState   物资是否添加或修改成功的状态值
     * @param $adsState     地址是否添加或修改成功的状态值
     * @param $csup         用于处理事务
     */
    static function suppliesHandle(&$update,&$cState,&$adsState,&$csup){
           
           
        
            $update['is_supplies'] = 1;
            //获取物资数据
            foreach (I('hp_cont') as $k => $v) {
                $num = intval($_POST['hp_num'][$k]);
                $data[] = array('concur_id' => I('id'), 'name' => $v, 'num' => $num);
            }
            //添加物资
            $cState = $csup->addAll($data);
            if (!$cState) {
                $csup->rollback();
                return array('status'=>-1,'message'=>$csup->getError());
            }
            //查询是求助还是资源
            $r = D('Concur')->getConcurById(I('id'));
     
            
            if($r['type'] == 0){
                //添加物资地址
                $address = D('ConcurSuppliesAddress');
                if ($address->create()) {                                  
                    if (D('ConcurSuppliesAddress')->where("concur_id=%d", I('id'))->count()) {
                        $adsState = $address->where("concur_id=%d", I('id'))->save();                      
                    } else {                  
                        $adsState = $address->add(); 
                    }
                    
                } else {
                    $csup->rollback();
                    return array('status'=>-1,'message'=>$address->getError());
                }
            }
    }
    
    
    /**
     * 服务添加或修改
     * @param $update           需要更新的数组键值对
     * @param $serverState      服务是否添加或修改成功的状态值
     * @param $csup             用于处理事务
     */
    static function serviceHandle(&$update,&$serverState,&$csup){
        $service = D('ConcurService');
        if ($service->create()) {
            if ($service->where('concur_id=%d', I('id'))->count()) {
                $serverState = $service->where('concur_id=%d', I('id'))->save();
            } else {
                $serverState = $service->add();
                $update['is_service'] = 1;
            }
        } else {
            $csup->rollback();
            return array('status'=>-1,'message'=>$service->getError());
        }       
    }
    
    /**
     * 事务提交
     */
    static function workSub($status,$update,&$csup){
         if ($status) {
            $map['id'] = intval(I('id'));  	    	 	    
            $update['update_date'] = time();
	    if (M('Concur')->where($map)->save($update)) {
                $csup->commit();
                $name = 'concur_' . I('id');    //清空缓存
                S($name, null);
                return array('status'=>1,'url'=>U('t/concur/concurThree', array('id' => I('id'))));
            } else {
                $csup->rollback();
                return array('status'=>-1,'message'=>M('Concur')->getError());
            }
        } else {
            $csup->rollback();
            return array('status'=>-1,'message'=>'操作失败');
        }
    }


    
    
}
