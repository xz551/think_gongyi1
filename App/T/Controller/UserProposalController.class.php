<?php

namespace T\Controller;

use Think\Controller;
use M\Controller\CodeController;

class UserProposalController extends Controller {

    public function proposal() {
        //检查验证码
        $result = CodeController::check_verify(I("post.yzmcode"));
        if (!$result) {
            $this->ajaxReturn(array("check_verify" => -1));
        }
        $Proposal = D("UserProposal");
        if ($Proposal->create()) {
            $rs = $Proposal->add();
            if ($rs) {
                $this->ajaxReturn(array("result" => 1));
            } else {
                $this->ajaxReturn(array("result" => 0));
            }
        } else {
            echo "0";
        }
    }
}
