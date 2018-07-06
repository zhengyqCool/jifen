<?php
class IndexController extends Controller {
    public function __construct(){
        parent::__construct();
        if(!isset($_SESSION['user']) && $_SESSION['us_id'] == null ){
            header('Location: index.php?c=user&a=login');
        }
        $db = Mysql::connect();
        $user = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'user where us_id = :us_id',array(':us_id'=>$_SESSION['us_id']));
        $this->assign('user', $user);
    }

    public function index(){
        $db = Mysql::connect();
        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'activity_data where ad_status = 1', 'NUM');
        $Page = new Page($count, 10);
        $show  = $Page->show();
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ad_status = 1 limit ' . $Page->firstRow . ',' . $Page->listRows);
        foreach($list as $key => $val){
            $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$val['ac_id']));
            $list[$key] = array_merge($val,$activity);
        }
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->assign('pages',$show);
        $this->display();
    }

    public function lists(){
        $ac_id = getGpc('id','integer','G');
        $db = Mysql::connect();
        $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$ac_id));
        if(!$activity){
            header('Location: index.php');
        }
        $info = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ac_id = :ac_id order by ad_lotterytime desc limit 1',array(':ac_id'=>$ac_id));
        if(!$info){
            header('Location: index.php');
        }
        if($db->getOne('select * from ' . config('DB_TABLE_PRE') . 'bet_log where ad_id = :ad_id and us_id = :us_id',array(':ad_id'=>$info['ad_id'],':us_id'=>$_SESSION['us_id']))){
            $info['status'] = 1;
        }
        $this->assign('info', $info);
        $this->assign('activity', $activity);
        $this->display();
    }

    public function betlog(){
        $ad_id = getGpc('id','integer','G');
        $db = Mysql::connect();
        $info = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ad_id = :ad_id',array(':ad_id'=>$ad_id));
        if(!$info){
            header('Location: index.php');
        }
        if(time() < $info['ad_lottertime']){
            header('Location: index.php');
        }
        $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$info['ac_id']));
        if(!$activity){
            header('Location: index.php');
        }
        $list = $db->getAll('select l.*,u.us_phone from ' . config('DB_TABLE_PRE') . 'bet_log l left join ' . config('DB_TABLE_PRE') . 'user u on l.us_id = u.us_id where l.ad_id = :ad_id order by l.log_awards_point desc limit 100',array(':ad_id'=>$ad_id));
        $user_info = array();
        foreach($list as $val){
            if($val['us_id'] == $_SESSION['us_id']){
                $user_info = $val;
            }
        }
        $user_info = $user_info ? $user_info : $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'bet_log where ad_id = :ad_id and user_id = :user_id',array(':ad_id'=>$ad_id,':us_id'=>$_SESSION['us_id']));

        $this->assign('activity', $activity);
        $this->assign('info', $info);
        $this->assign('user_info', $user_info);
        $this->assign('list', $list);
        $this->display();
    }

    public function bet(){
        $ad_id = getGpc('id','integer','P');
        $db = Mysql::connect();
        $info = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ad_id = :ad_id',array(':ad_id'=>$ad_id));
        if(!$info){
            $this->ajaxReturn(array('status'=>0,'msg'=>'参数不合法'));
        }
        if(time() < $info['ad_betstarttime']){
            $this->ajaxReturn(array('status'=>0,'msg'=>'活动报名未开始'));
        }
        if(time() > $info['ad_betendtime']){
            $this->ajaxReturn(array('status'=>0,'msg'=>'活动报名已结束'));
        }
        $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$info['ac_id']));
        if(!$activity || $activity['ac_status'] == 0){
            $this->ajaxReturn(array('status'=>0,'msg'=>'活动已暂停'));
        }
        $log = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'bet_log where ad_id = :ad_id and us_id = :us_id',array(':ad_id'=>$ad_id,':us_id'=>$_SESSION['us_id']));
        if($log){
            $this->ajaxReturn(array('status'=>0,'msg'=>'不可重复报名'));
        }
        $us_point = $db->getOne('select us_point from ' . config('DB_TABLE_PRE') . 'user where us_id = :us_id',array(':us_id'=>$_SESSION['us_id']),'NUM');
        if($us_point < $activity['ac_point']){
            $this->ajaxReturn(array('status'=>0,'msg'=>'积分不足'));
        }
        $param[':ad_id'] = $ad_id;
        $param[':us_id'] = $_SESSION['us_id'];
        $param[':log_point'] = $activity['ac_point'];
        $param[':log_awards'] = 0;
        $param[':log_awards_point'] = 0;
        $param[':log_addtime'] = time();
        $sql = 'insert into ' . config('DB_TABLE_PRE') . 'bet_log(ad_id,us_id,log_point,log_awards,log_awards_point,log_addtime) values(:ad_id,:us_id,:log_point,:log_awards,:log_awards_point,:log_addtime)';
        if($db->exec($sql,$param) !== false){
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user_log(us_id,log_action,log_point,log_description,log_time) values(:us_id,:log_action,:log_point,:log_description,:log_time)';
            $db->exec($sql,array(
                ':us_id' => $_SESSION['us_id'],
                ':log_action' => '3',
                ':log_point' => $activity['ac_point'] * -1,
                ':log_description' => '报名'.$activity['ac_name'] . '第' . $info['ad_name'] .'期',
                ':log_time' => date('Y-m-d H:i:s')
            ));
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'user SET us_point = us_point - :us_point where us_id = :us_id';
            $db->exec($sql,array(':us_point'=>$activity['ac_point'],':us_id'=>$_SESSION['us_id']));
            $this->ajaxReturn(array('status'=>1,'msg'=>'报名成功'));
        } else {
            $this->ajaxReturn(array('status'=>0,'msg'=>'报名失败'));
        }
    }
}
