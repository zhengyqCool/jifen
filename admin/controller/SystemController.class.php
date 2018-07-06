<?php
defined("ADMIN_IN") or die('illegal origin');
class SystemController extends Controller {
    public function __construct(){
        parent::__construct();
        if(!isset($_SESSION['admin']) && $_SESSION['am_id'] == null ){
            header('Location: index.php?c=index&a=login');
        }
    }

    public function save_config(){
        if(IS_POST){
            $config = getGpc('config','array','P');
            $return = false;
            $db = Mysql::connect();
            foreach($config as $key=>$val){
                if(in_array(gettype($val['value']),array('array','object','resource'))){
                    $val['value'] = serialize($val['value']);
                }
                $param = array(
                    ':sc_value' => $val['value'],
                    ':sc_type' => $val['type'],
                    ':sc_name' => $key
                );
                if($db->getOne('select * from '.config('DB_TABLE_PRE').'config where sc_name = "' . $key . '"')){
                    $sql = 'update ' . config('DB_TABLE_PRE').'config set sc_value = :sc_value , sc_type = :sc_type where sc_name = :sc_name';
                }else{
                    $sql = 'insert into '.config('DB_TABLE_PRE').'config(sc_value,sc_type,sc_name) values(:sc_value,:sc_type,:sc_name)';
                }
                $return = $db->exec($sql,$param) || $return;
            }
            if($return){
                $this->cache_config();
                $this->ajaxReturn(array('status'=>1,'msg'=>保存成功));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            header('Location: index.php');
        }
    }

    public function _empty($a,$param){
        $db = Mysql::connect();
        $config = $db->getAll('select * from '.config('DB_TABLE_PRE').'config');
        $info = array();
        foreach($config as $val){
            $info[$val['sc_name']] = unserialize($val['sc_value']) ? unserialize($val['sc_value']) : $val['sc_value'];
        }
        $this->assign('info',$info);
        $this->display($a);
    }

    public function lists(){
        $db = Mysql::connect();
        $count  = $db->getOne('select count(1) as count_num from '.config('DB_TABLE_PRE').'admin','NUM');
        $Page  = new Page($count,10);
        $list  = $db->getAll('select * from '.config('DB_TABLE_PRE').'admin order by am_addtime desc limit ' . $Page->firstRow . ',' . $Page->listRows);
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('page',$Page->nowPage);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $am_user = getGpc('am_user','string','P');
            $am_nickname = getGpc('am_nickname','string','P');
            $am_status = getGpc('am_status','integer','P');
            $password = getGpc('password','string','P');
            $reg_password = getGpc('reg_password','string','P');
            if($password == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'密码不能为空'));
            }
            if($password != $reg_password){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $db = Mysql::connect();
            $param = array();
            $param[':am_user'] = $am_user;
            $param[':am_nickname'] = $am_nickname;
            $param[':am_safecode'] = substr(md5(time()),8,4);
            $param[':am_password'] = md5(md5($password) . $param[':am_safecode']);
            $param[':am_status'] = $am_status;
            $param[':am_lastlogintime'] = '0000.00.00 00:00:00';
            $param[':am_lastloginip'] = '0.0.0.0';
            $param[':am_addtime'] = date('Y-m-d H:i:s');
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'admin(am_user,am_nickname,am_password,am_safecode,am_status,am_lastlogintime,am_lastloginip,am_addtime) values(:am_user,:am_nickname,:am_password,:am_safecode,:am_status,:am_lastlogintime,:am_lastloginip,:am_addtime)';
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $this->display();
        }
    }

    public function edit(){
        if(IS_POST){
            $am_id = getGpc('am_id','integer','P');
            $am_nickname = getGpc('am_nickname','string','P');
            $am_status = getGpc('am_status','integer','P');
            $password = getGpc('password','string','P');
            $reg_password = getGpc('reg_password','string','P');
            if($password != $reg_password){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $db = Mysql::connect();
            $param = array(':am_id'=>$am_id);
            $param[':am_nickname'] = $am_nickname;
            $param[':am_status'] = $am_status;
            if(trim($password) != '') {
                $param[':am_safecode'] = substr(md5(time()),8,4);
                $param[':am_password'] = md5(md5($password) . $param[':am_safecode']);
                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'admin SET am_nickname = :am_nickname ,am_status = :am_status ,am_safecode = :am_safecode ,am_password = :am_password where am_id = :am_id';
            }else{
                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'admin SET am_nickname = :am_nickname ,am_status = :am_status where am_id = :am_id';
            }
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $am_id = getGpc('id','integer','G');
            $db = Mysql::connect();
            $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'admin where am_id = :am_id',array(':am_id'=>$am_id));
            $this->assign('info',$info);
            $this->display();
        }
    }

    public function delete(){
        $am_id = getGpc('id','integer','G');
        $db = Mysql::connect();
        $user_num  = $db->getOne('select count(1) from '.config('DB_TABLE_PRE').'admin where am_id != :am_id',array(':am_id'=>$am_id),'NUM');
        if($user_num == 0){
            $this->ajaxReturn(array('status'=>0,'msg'=>'唯一账号不能删除'));
        }
        if($am_id == $_SESSION['am_id']){
            $this->ajaxReturn(array('status'=>0,'msg'=>'不能删除自己哦'));
        }
        $param = array(':am_id'=>$am_id);
        $sql = 'delete from ' . config('DB_TABLE_PRE') . 'admin where am_id = :am_id';
        if($db->exec($sql,$param) !== false){
            $this->ajaxReturn(array('status'=>1,'msg'=>'操作成功'));
        } else {
            $this->ajaxReturn(array('status'=>0,'msg'=>'操作失败'));
        }
    }

    public function info(){
        if(IS_POST){
            $am_id = $_SESSION['am_id'];
            $am_nickname = getGpc('am_nickname','string','P');
            $db = Mysql::connect();
            $param = array(':am_id'=>$am_id);
            $param[':am_nickname'] = $am_nickname;
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'admin SET am_nickname = :am_nickname where am_id = :am_id';
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $am_id = $_SESSION['am_id'];
            $db = Mysql::connect();
            $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'admin where am_id = :am_id',array(':am_id'=>$am_id));
            $this->assign('info',$info);
            $this->display();
        }
    }

    public function resetpd(){
        if(IS_POST){
            $oldPw = getGpc('oldPw','string','P');
            $newPw = getGpc('newPw','string','P');
            $regPw = getGpc('regPw','string','P');
            if($newPw == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'密码不能为空'));
            }
            if($newPw != $regPw){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $db = Mysql::connect();
            $info = $db->getOne('select am_safecode,am_password from '.config('DB_TABLE_PRE').'admin where am_id = "' . $_SESSION['am_id'] . '"');
            if(md5(md5($oldPw) . $info['am_safecode']) == $info['am_password']){
                $param = array(':am_id'=> $_SESSION['am_id']);
                $param[':am_safecode'] = substr(md5(time()),8,4);
                $param[':am_password'] = md5(md5($newPw) . $param[':am_safecode']);
                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'admin SET am_safecode = :am_safecode ,am_password = :am_password where am_id = :am_id';
                if($db->exec($sql,$param)){
                    $_SESSION = null;
                    session_unset();
                    session_destroy();
                    $this->ajaxReturn(array('status'=>1,'msg'=>'修改成功，请重新登陆'));
                } else {
                    $this->ajaxReturn(array('status'=>0,'msg'=>'修改失败'));
                }
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'密码输入错误'));
            }
        }else{
            $this->display();
        }
    }

    private function cache_config(){
        $db = Mysql::connect();
        $config = $db->getAll('select * from '.config('DB_TABLE_PRE').'config where sc_type = 0');
        $info = array();
        foreach($config as $val){
            $info[$val['sc_name']] = unserialize($val['sc_value']) ? unserialize($val['sc_value']) : $val['sc_value'];
        }
        return writeFile(SITE_PATH .'data/cache/config.txt',json_encode($info));
    }
}
