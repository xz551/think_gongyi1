<?php
namespace T\Model;
use Lib\User;
use Think\Model;
use Lib\UserSession;
class ConcurApplyModel extends Model {
    /**
     * int $tag 请求的类型 1-supplies(物资),2-service(服务)，3-money(款项)
     */
    public function addApply($concur_id, $tag,$uid='') {
        $uid = $uid?$uid:UserSession::getUser('uid');
        $_w['concur_id'] = $concur_id;
        $_w['userid'] = $uid;
        $apply = $this->where($_w)->find();
        if ($apply) {
            $this->saveApply($apply['id'], $tag);
        } else {
            $this->addNewApply($concur_id, $tag,$uid);
        }
    }

    private function addNewApply($concur_id, $tag,$uid) {
        $_data = $this->getApplyData($concur_id, $tag,$uid);
        $this->add($_data);
    }

    private function saveApply($id, $tag) {
        $_data[$this->checkTag($tag)] = 1;
        $_w['id'] = $id;
        $this->where($_w)->save($_data);
    }

    private function getApplyData($concur_id, $tag,$uid) {
        $_data['userid'] = $uid;
        $_data['concur_id'] = $concur_id;
        $_data[$this->checkTag($tag)] = 1;
        $_data['datetime'] = date('Y-m-d H:i:s', time());
        $_data['updatetime'] = date('Y-m-d H:i:s', time());
        return $_data;
    }

    private function checkTag($tag) {
        switch ($tag) {
            case 1: return 'supplies';
            case 2: return 'service';
            case 3: return 'money';
        }
    }

}
