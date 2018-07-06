<?php
defined("ADMIN_IN") or die('illegal origin');
class UserController extends Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['admin']) && $_SESSION['am_id'] == null) {
            header('Location: index.php?c=index&a=login');
        }
    }

    public function lists(){
        $phone = getGpc('phone','string','G');
        $param = array();
        $where = ' 1 = 1 ';
        if ($phone != ''){
            $where .= ' and us_phone = :us_phone ';
            $param[':us_phone'] = $phone;
        }
        $db = Mysql::connect();
        $count  = $db->getOne('select count(1) from '.config('DB_TABLE_PRE').'user where ' . $where,$param,'NUM');
        $Page  = new Page($count,10);
        $list  = $db->getAll('select * from '.config('DB_TABLE_PRE').'user where ' . $where  . ' order by us_addtime desc limit ' . $Page->firstRow . ',' . $Page->listRows,$param);
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('page',$Page->nowPage);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $db = Mysql::connect();
            $param = array();
            $param[':us_phone'] = getGpc('us_phone','string','P');

            if($param[':us_phone'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入用户名'));
            }
            if($db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone',array(':us_phone'=>$param[':us_phone']))){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机号已注册'));
            }
            $password = getGpc('password','string','P');
            $reg_password = getGpc('reg_password','string','P');
            if($password == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'密码不能为空'));
            }
            if($password != $reg_password){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $param[':us_name'] = getGpc('us_name','string','P');
            $param[':us_safecode'] = substr(md5(time()),8,4);
            $param[':us_password'] = md5(md5($password) . $param[':us_safecode']);
            $param[':us_point'] = getGpc('us_point','integer','P');
            $param[':us_status'] = getGpc('us_status','integer','P');
            $param[':us_addtime'] = date('Y-m-d H:i:s');
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user(us_phone,us_name,us_safecode,us_password,us_point,us_status,us_addtime) values(:us_phone,:us_name,:us_safecode,:us_password,:us_point,:us_status,:us_addtime)';
            if($db->exec($sql,$param) !== false){
                if($param[':us_point'] != 0){
                    $us_id = $db->insertId();
                    $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user_log(us_id,log_action,log_point,log_description,log_time) values(:us_id,:log_action,:log_point,:log_description,:log_time)';
                    $db->exec($sql,array(
                        ':us_id' => $us_id,
                        ':log_action' => '1',
                        ':log_point' => $param[':us_point'],
                        ':log_description' => '注册赠送初始积分',
                        ':log_time' => date('Y-m-d H:i:s')
                    ));
                }
                $this->ajaxReturn(array('status'=>1,'msg'=>'添加成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'添加失败'));
            }
        }else{
            $this->display();
        }
    }

    public function edit(){
        if(IS_POST){
            $db = Mysql::connect();
            $param[':us_id'] = getGpc('us_id','integer','P');
            $param[':us_phone'] = getGpc('us_phone','string','P');
            if($param[':us_phone'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入用户名'));
            }
            if($db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone and us_id != :us_id',array(':us_phone'=>$param[':us_phone'],':us_id'=>$param[':us_id']))){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机号已注册'));
            }
            $password = getGpc('password','string','P');
            $reg_password = getGpc('reg_password','string','P');
            if($password != $reg_password){
                $this->ajaxReturn(array('status'=>0,'msg'=>'两次密码输入不一致'));
            }
            $param[':us_name'] = getGpc('us_name','string','P');
            $param[':us_status'] = getGpc('us_status','integer','P');
            if(trim($password) != '') {
                $param[':us_safecode'] = substr(md5(time()), 8, 4);
                $param[':us_password'] = md5(md5($password) . $param[':us_safecode']);
                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'user SET us_phone = :us_phone ,us_name = :us_name ,us_status = :us_status ,us_safecode = :us_safecode ,us_password = :us_password where us_id = :us_id';
            }else{
                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'user SET us_phone = :us_phone ,us_name = :us_name ,us_status = :us_status  where us_id = :us_id';
            }
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $us_id = getGpc('id','integer','G');
            $db = Mysql::connect();
            $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_id = :us_id',array(':us_id'=>$us_id));
            $this->assign('info',$info);
            $this->display();
        }
    }

    public function point_lists(){
        $us_id = getGpc('id','integer','G');
        $db = Mysql::connect();
        $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_id = :us_id',array(':us_id'=>$us_id));
        $this->assign('info',$info);
        $count  = $db->getOne('select count(1) as count_num from '.config('DB_TABLE_PRE').'user_log where us_id = :us_id',array(':us_id'=>$us_id),'NUM');
        $Page  = new Page($count,10);
        $list  = $db->getAll('select * from '.config('DB_TABLE_PRE').'user_log where us_id = :us_id order by log_time desc limit ' . $Page->firstRow . ',' . $Page->listRows,array(':us_id'=>$us_id));
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('page',$Page->nowPage);
        $this->display();
    }

    public function add_point(){
        if(IS_POST){
            $db = Mysql::connect();
            $param[':us_id'] = getGpc('us_id','integer','P');
            $param[':us_point'] = getGpc('value','integer','P');
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'user SET us_point = us_point + :us_point where us_id = :us_id';
            if($db->exec($sql,$param) !== false){
                $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user_log(us_id,log_action,log_point,log_description,log_time) values(:us_id,:log_action,:log_point,:log_description,:log_time)';
                $db->exec($sql,array(
                    ':us_id' => $param[':us_id'],
                    ':log_action' => '4',
                    ':log_point' => $param[':us_point'],
                    ':log_description' => '后台人工操作',
                    ':log_time' => date('Y-m-d H:i:s')
                ));
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }
    }
}