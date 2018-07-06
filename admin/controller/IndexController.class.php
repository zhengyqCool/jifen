<?php
defined("ADMIN_IN") or die('illegal origin');
class IndexController extends Controller {
    public function __construct(){
        parent::__construct();
        if((!isset($_SESSION['admin']) && $_SESSION['am_id'] == null ) && ACTION_NAME != 'login'){
            header('Location: index.php?c=index&a=login');
        }
    }

    public function index(){
        $db = Mysql::connect();
        $param = array(':date'=>date('Y-m-d'));
        $day_user_num  = $db->getOne('select count(1) as count_num from '.config('DB_TABLE_PRE').'user where us_addtime like ":date"',$param,'NUM');
        $this->assign('day_user_num',$day_user_num);
        $this->display();
    }

    public function index_data(){
        $db = Mysql::connect();
        $data = array();
        for($date = strtotime(date('Y-m-d',strtotime('-29 day')));$date <= strtotime(date('Y-m-d'));$date += 24*60*60){
            $user_num = $db->getOne('select count(1) as count_num from '.config('DB_TABLE_PRE').'user where us_addtime between "'.date('Y-m-d H:i:s',$date) . '" and "'. date('Y-m-d H:i:s',$date + 24*60*60 - 1) .'"','NUM');
            $data['yaxis'][] = $user_num;
            $data['xaxis'][] = date('Y-m-d',$date);
        }
        $this->ajaxReturn($data);
    }

    public function login(){
        if(isset($_SESSION['admin']) && $_SESSION['am_id'] != null ){
            if(IS_POST) {
                $this->ajaxReturn(array('status'=>1,'url'=>'index.php?c=index&a=index'));
            }else{
                header('Location: index.php');
            }
        }
        if(IS_POST){
            $username = getGpc('username','string','P');
            $password = getGpc('password','string','P');
            $checkcode = getGpc('checkcode','string','P');
            if($_SESSION['checkcode'] == '' || strtolower($checkcode) != strtolower($_SESSION['checkcode'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
            }else{
                $_SESSION['checkcode'] = null;
            }
            if($username == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入用户名'));
            }
            if($password == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入密码'));
            }
            $db = Mysql::connect();
            $info = $db->getOne('select * from '.config('DB_TABLE_PRE').'admin where am_user = :am_user',array(':am_user'=>$username));
            if($info){
                if($info['am_status'] == 0){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'该账号已被禁用'));
                }
                if(md5(md5($password) . $info['am_safecode']) == $info['am_password']){
                    $_SESSION['am_id'] = $info['am_id'];
                    $_SESSION['am_lastlogintime'] = $info['am_lastlogintime'];
                    $_SESSION['am_lastloginip'] = $info['am_lastloginip'];
                    $_SESSION['am_nickname'] = $info['am_nickname'];
                    $_SESSION['am_user'] = $info['am_user'];
                    $_SESSION['admin'] = true;
                    $param = array(
                        ':am_id'=>$info['am_id'],
                        ':am_lastlogintime'=>date('Y-m-d H:i:s'),
                        ':am_lastloginip'=>getIp(),
                    );
                    $db->exec('update '.config('DB_TABLE_PRE').'admin set am_lastlogintime = :am_lastlogintime,am_lastloginip = :am_lastloginip where am_id = :am_id',$param);
                    $this->ajaxReturn(array('status'=>1,'url'=>'index.php'));
                } else {
                    $this->ajaxReturn(array('status'=>0,'msg'=>'密码错误'));
                }
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'用户不存在'));
            }
        }else{
            $this->display();
        }
    }

    public function logout(){
        $_SESSION = null;
        session_unset();
        session_destroy();
        header('Location: index.php?c=index&a=login');
    }
    
    public function tips(){
        $this->ajaxReturn(array());
    }
}
