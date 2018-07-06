<?php
class UserController extends Controller {
    public function __construct(){
        parent::__construct();
        $no_login = array(
            'login',
            'register',
            'register_code'
        );
        if((!isset($_SESSION['user']) && $_SESSION['us_id'] == null ) && !in_array(ACTION_NAME,$no_login)){
            header('Location: index.php?c=user&a=login');
        }
        if(!in_array(ACTION_NAME,$no_login)){
            $db = Mysql::connect();
            $user = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'user where us_id = :us_id',array(':us_id'=>$_SESSION['us_id']));
            $this->assign('user', $user);
        }
    }

    public function index(){
        $db = Mysql::connect();
        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'bet_log where us_id = :us_id',array(':us_id'=>$_SESSION['us_id']), 'NUM');
        $Page = new Page($count, 10);
        $show  = $Page->show();
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'bet_log where us_id = :us_id order by log_addtime desc limit ' . $Page->firstRow . ',' . $Page->listRows,array(':us_id'=>$_SESSION['us_id']));
        foreach($list as $key => $val){
            $info = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ad_id = :ad_id',array(':ad_id'=>$val['ad_id']));
            $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$info['ac_id']));
            $list[$key] = array_merge($val,$info,$activity);
        }
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->assign('pages',$show);
        $this->display();
    }

    public function login(){
        if(isset($_SESSION['user']) && $_SESSION['us_id'] != null ){
            if(IS_POST) {
                $this->ajaxReturn(array('status'=>1,'url'=>'index.php'));
            }else{
                header('Location: index.php');
            }
        }
        if(IS_POST){
            $username = getGpc('username','string','P');
            $password = getGpc('password','string','P');
            if($username == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入用户名'));
            }
            if($password == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入密码'));
            }
            $db = Mysql::connect();
            $info = $db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone',array(':us_phone'=>$username));
            if($info){
                if($info['us_status'] == 0){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'该账号已被禁用'));
                }
                if(md5(md5($password) . $info['us_safecode']) == $info['us_password']){
                    $_SESSION['us_id'] = $info['us_id'];
                    $_SESSION['us_name'] = $info['us_name'];
                    $_SESSION['us_phone'] = $info['us_phone'];
                    $_SESSION['us_point'] = $info['us_point'];
                    $_SESSION['user'] = true;
                    $this->ajaxReturn(array('status'=>1,'msg'=>'index.php'));
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

    public function register(){
        if(isset($_SESSION['user']) && $_SESSION['us_id'] != null ){
            if(IS_POST) {
                $this->ajaxReturn(array('status'=>1,'url'=>'index.php'));
            }else{
                header('Location: index.php');
            }
        }
        if(IS_POST){
            $username = getGpc('username','string','P');
            $password = getGpc('password','string','P');
            $reg_password = getGpc('reg_password','string','P');
            $checkcode = getGpc('checkcode','string','P');
            if($_SESSION['checkcode'] == '' || strtolower($checkcode) != strtolower($_SESSION['checkcode']) || $_SESSION['phone'] == '' || strtolower($username) != $_SESSION['phone']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
            }else{
                $_SESSION['checkcode'] = null;
                $_SESSION['phone'] = null;
            }
            if($username == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入用户名'));
            }
            $db = Mysql::connect();
            if($db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone',array(':us_phone'=>$username))){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机号已注册'));
            }
            if($password == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'密码不能为空'));
            }
            if($password != $reg_password){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $param = array();
            $param[':us_phone'] = $username;
            $param[':us_name'] = '';
            $param[':us_safecode'] = substr(md5(time()),8,4);
            $param[':us_password'] = md5(md5($password) . $param[':us_safecode']);
            $param[':us_point'] = 0;
            $param[':us_status'] = '1';
            $param[':us_addtime'] = date('Y-m-d H:i:s');
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user(us_phone,us_name,us_safecode,us_password,us_point,us_status,us_addtime) values(:us_phone,:us_name,:us_safecode,:us_password,:us_point,:us_status,:us_addtime)';
            if($db->exec($sql,$param) !== false){
                $us_id = $db->insertId();
                $_SESSION['us_id'] = $us_id;
                $_SESSION['us_name'] = $param[':us_name'];
                $_SESSION['us_phone'] = $param[':us_phone'];
                $_SESSION['us_point'] = $param[':us_point'];
                $_SESSION['user'] = true;
                $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'注册失败'));
            }
        }else{
            $this->display();
        }
    }

    public function register_code(){
        if(IS_POST){
            $username = getGpc('username','string','P');
            if($username == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入手机号'));
            }
            if(!is_phone($username)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机号格式不正确'));
            }
            if(time() - $_SESSION['checkcode_time'] < 60){
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送过于频繁'));
            }
            $db = Mysql::connect();
            if($db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone',array(':us_phone'=>$username))){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机号已注册'));
            }
            $shop_config = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'config');
            foreach($shop_config as $val){
                if($val['sc_name'] == 'dayu_appkey'){
                    config('dayu_appkey',$val['sc_value']);
                }
                if($val['sc_name'] == 'dayu_secretKey'){
                    config('dayu_secretKey',$val['sc_value']);
                }
                if($val['sc_name'] == 'dayu_sign'){
                    config('dayu_sign',$val['sc_value']);
                }
                if($val['sc_name'] == 'dayu_template'){
                    config('dayu_template',$val['sc_value']);
                }
            }
            $checkcode = getRandID(6,false);
            if(sendSMS($username,array('code'=>$checkcode))){
                $_SESSION['checkcode'] = $checkcode;
                $_SESSION['checkcode_time'] = time();
                $_SESSION['phone'] = $username;
                $this->ajaxReturn(array('status'=>1,'msg'=>''));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败，请稍后再试'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'系统错误'));
        }
    }

    public function logout(){
        $_SESSION = null;
        session_unset();
        session_destroy();
        header('Location: index.php?c=index&a=login');
    }
}