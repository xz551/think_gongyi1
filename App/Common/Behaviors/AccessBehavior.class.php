<?php
namespace Common\Behaviors;
use Think\Behavior;
use Lib\UserSession;
class AccessBehavior  extends Behavior{
    /**
     * 进入控制器时，每个控制器都默认执行的行为
     */
    public function run(&$param){
        if (!IS_AJAX) {
		
		$u = UserSession::getUser();
		if($u){
	   		//dump($_SERVER['PHP_SELF']);
			//dump($u['uid']);
		}
            //$this->accessInfo();
            //session("statistics_access_id",$id);
        } 
    }
    
    /**
     * 获取用户访问信息
     */
    private function accessInfo(){
        //获取用户的IP
        $info['userip'] = $_SERVER['REMOTE_ADDR'];
        //获取上一层URL地址
        $info['reurl'] = $_SERVER['HTTP_REFERER'];
        //获取用户访问的URL
        $info['url'] = $this->get_url();
        //如果是登录用户，获取用户的ID
        $info['uid'] = \Lib\UserSession::getUser('uid');
        //获取访问的时间
        $info['time'] = time();
        //用户来源的类型，1-自己网站跳转， 0-直接打开，2-其他网站跳转
        if(strpos($info['reurl'],'http://www.719kj.com') === 0){
            $info['type'] = 1;
        }elseif(!$info['reurl']){
            $info['type'] = 0;
        }else{
            $info['type'] = 2;
        }
        
    }
    
     /**
    * 获取当前页面完整URL地址
    */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
    
    
    
}
