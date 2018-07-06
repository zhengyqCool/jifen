<?php
defined("ADMIN_IN") or die('illegal origin');
class ActivityController extends Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['admin']) && $_SESSION['am_id'] == null) {
            header('Location: index.php?c=index&a=login');
        }
    }

    public function lists() {
        $name = getGpc('name','string','G');
        $param = array();
        $where = ' 1 = 1 ';
        if ($name != ''){
            $where .= ' and ac_name like :ac_name ';
            $param[':ac_name'] = '%' . $name . '%';
        }
        $db = Mysql::connect();
        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'activity where ' . $where,$param, 'NUM');
        $Page = new Page($count, 10);
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'activity where ' . $where .' order by ac_id desc limit ' . $Page->firstRow . ',' . $Page->listRows,$param);
        $case = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'case ');
        $this->assign('case', $case);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $db = Mysql::connect();
            $param = array();
            $param[':ac_name'] = getGpc('ac_name','string','P');
            if($param[':ac_name'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入活动名称'));
            }
            $param[':ac_description'] = getGpc('ac_description','string','P');
            $param[':cs_id'] = getGpc('cs_id','integer','P');
            if($param[':cs_id'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请选择活动方案'));
            }
            $param[':ac_point'] = getGpc('ac_point','integer','P');
            if($param[':ac_point'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置报名所需积分'));
            }
            $param[':ac_awards1'] = getGpc('ac_awards1','integer','P');
            if($param[':ac_awards1'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置一等奖奖项比例'));
            }
            $param[':ac_awards2'] = getGpc('ac_awards2','integer','P');
            if($param[':ac_awards2'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置二等奖奖项比例'));
            }
            $param[':ac_awards3'] = getGpc('ac_awards3','integer','P');
            if($param[':ac_awards3'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设三等奖奖项比例'));
            }
            $param[':ac_awards4'] = getGpc('ac_awards4','integer','P');
            if($param[':ac_awards4'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置其他奖项比例'));
            }
            $param[':ac_starttime'] = getGpc('ac_starttime','string','P');
            $param[':ac_starttime'] = strtotime($param[':ac_starttime']);
            if($param[':ac_starttime'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置活动开始时间'));
            }
            $param[':ac_status'] = getGpc('ac_status','integer','P');
            $param[':ac_addtime'] = date('Y-m-d H:i:s');
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'activity(ac_name,ac_description,cs_id,ac_point,ac_awards1,ac_awards2,ac_awards3,ac_awards4,ac_status,ac_starttime,ac_addtime) values(:ac_name,:ac_description,:cs_id,:ac_point,:ac_awards1,:ac_awards2,:ac_awards3,:ac_awards4,:ac_status,:ac_starttime,:ac_addtime)';
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'添加成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'添加失败'));
            }
        }else{
            $db = Mysql::connect();
            $case = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'case ');
            $this->assign('case', $case);
            $this->display();
        }
    }

    public function edit(){
        if(IS_POST){
            $db = Mysql::connect();
            $param[':ac_id'] = getGpc('ac_id','integer','P');
            $param[':ac_name'] = getGpc('ac_name','string','P');
            if($param[':ac_name'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入活动名称'));
            }
            $param[':ac_description'] = getGpc('ac_description','string','P');
            $param[':cs_id'] = getGpc('cs_id','integer','P');
            if($param[':cs_id'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请选择活动方案'));
            }
            $param[':ac_point'] = getGpc('ac_point','integer','P');
            if($param[':ac_point'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置报名所需积分'));
            }
            $param[':ac_awards1'] = getGpc('ac_awards1','integer','P');
            if($param[':ac_awards1'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置一等奖奖项比例'));
            }
            $param[':ac_awards2'] = getGpc('ac_awards2','integer','P');
            if($param[':ac_awards2'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置二等奖奖项比例'));
            }
            $param[':ac_awards3'] = getGpc('ac_awards3','integer','P');
            if($param[':ac_awards3'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设三等奖奖项比例'));
            }
            $param[':ac_awards4'] = getGpc('ac_awards4','integer','P');
            if($param[':ac_awards4'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置其他奖项比例'));
            }
            $param[':ac_starttime'] = getGpc('ac_starttime','string','P');
            $param[':ac_starttime'] = strtotime($param[':ac_starttime']);
            if($param[':ac_starttime'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请设置活动开始时间'));
            }
            $param[':ac_status'] = getGpc('ac_status','integer','P');
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'activity SET ac_name = :ac_name ,ac_description = :ac_description ,cs_id = :cs_id,ac_point = :ac_point,ac_awards1 = :ac_awards1 ,ac_awards2 = :ac_awards2 ,ac_awards3 = :ac_awards3 ,ac_awards4 = :ac_awards4 ,ac_status = :ac_status,ac_starttime=:ac_starttime where ac_id = :ac_id';
            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $ac_id = getGpc('id','integer','G');
            $db = Mysql::connect();
            $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity where ac_id = :ac_id',array(':ac_id'=>$ac_id));
            $this->assign('info',$info);
            $case = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'case ');
            $this->assign('case', $case);
            $this->display();
        }
    }

    public function change(){
        $db = Mysql::connect();
        $param[':ac_id'] = getGpc('id','integer','G');
        $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity where ac_id = :ac_id',$param);
        if($info['ac_status'] == 1) {
            $param[':ac_status'] = 0;
        }else{
            $param[':ac_status'] = 1;
        }
        $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'activity SET ac_status = :ac_status where ac_id = :ac_id';
        if($db->exec($sql,$param) !== false){
            $this->ajaxReturn(array('status'=>$param[':ac_status']));
        } else {
            $this->ajaxReturn(array('status'=>$info['ac_status']));
        }
    }

    public function log(){
        $ac_id = getGpc('id','integer','G');
        $where = ' ac_id = :ac_id ';
        $param = array(':ac_id' => $ac_id);
        $db = Mysql::connect();
        $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity where ac_id = :ac_id',$param);
        $this->assign('info',$info);
        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'activity_data where ' . $where,$param, 'NUM');
        $Page = new Page($count, 10);
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ' . $where .' order by ad_id desc limit ' . $Page->firstRow . ',' . $Page->listRows,$param);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->display();
    }

    public function betlog(){
        $ad_id = getGpc('id','integer','G');
        $where = ' b.ad_id = :ad_id ';
        $param = array(':ad_id' => $ad_id);
        $db = Mysql::connect();
        $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity_data where ad_id = :ad_id',$param);
        $this->assign('info',$info);
        $activity  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity where ac_id = :ac_id',array(':ac_id'=>$info['ac_id']));
        $this->assign('activity',$activity);
        $phone = getGpc('phone','string','G');
        if ($phone != ''){
            $user  = $db->getOne('select * from '.config('DB_TABLE_PRE').'user where us_phone = :us_phone',array(':us_phone'=>$phone));
            $where .= ' and b.us_id = :us_id ';
            $param[':us_id'] = $user['us_id'];
        }

        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'bet_log b where ' . $where,$param, 'NUM');
        $Page = new Page($count, 10);
        $list = $db->getAll('select b.*,u.us_phone from ' . config('DB_TABLE_PRE') . 'bet_log b left join ' . config('DB_TABLE_PRE') . 'user u on b.us_id = u.us_id where ' . $where .' order by b.log_awards_point desc ,b.log_id desc limit ' . $Page->firstRow . ',' . $Page->listRows,$param);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->display();
    }

    public function betlogsave(){
        $ad_id = getGpc('id','integer','G');
        $log_awards = getGpc('log_awards','array','P');
        $db = Mysql::connect();
        $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'activity_data where ad_id = :ad_id',array(':ad_id'=>$ad_id));
        if($info['status'] == 3) {
            $this->ajaxReturn(array('status'=>0,'msg'=>'活动已开奖'));
        }
        $db->startTrans();
        foreach($log_awards as $key => $val){
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'bet_log SET log_awards = :log_awards where log_id = :log_id';
            $param = array(
                ':log_awards'=> $val,
                ':log_id' => $key
            );
            if($db->exec($sql,$param) === false){
                $db->rollback();
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }
        $db->commit();
        $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
    }
}