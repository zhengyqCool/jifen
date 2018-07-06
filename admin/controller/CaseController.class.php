<?php
defined("ADMIN_IN") or die('illegal origin');
class CaseController extends Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['admin']) && $_SESSION['am_id'] == null) {
            header('Location: index.php?c=index&a=login');
        }
    }

    public function lists() {
        $db = Mysql::connect();
        $count = $db->getOne('select count(1) as count_num from ' . config('DB_TABLE_PRE') . 'case', 'NUM');
        $Page = new Page($count, 10);
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'case order by cs_id desc limit ' . $Page->firstRow . ',' . $Page->listRows);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $Page->nowPage);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $db = Mysql::connect();
            $param = array();
            $param[':cs_name'] = getGpc('cs_name','string','P');
            if($param[':cs_name'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入方案名称'));
            }
            $param[':cs_bettime'] = getGpc('cs_bettime','integer','P');
            if($param[':cs_bettime'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'下注时长不能为零'));
            }
            $param[':cs_lotterytime'] = getGpc('cs_lotterytime','integer','P');
            $param[':cs_waittime'] = getGpc('cs_waittime','integer','P');
            $sql = 'insert into ' . config('DB_TABLE_PRE') . 'case(cs_name,cs_bettime,cs_lotterytime,cs_waittime) values(:cs_name,:cs_bettime,:cs_lotterytime,:cs_waittime)';
            if($db->exec($sql,$param) !== false){
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
            $param[':cs_id'] = getGpc('cs_id','integer','P');
            $param[':cs_name'] = getGpc('cs_name','string','P');
            if($param[':cs_name'] == ''){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入方案名称'));
            }
            $param[':cs_bettime'] = getGpc('cs_bettime','integer','P');
            if($param[':cs_bettime'] == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'下注时长不能为零'));
            }
            $param[':cs_lotterytime'] = getGpc('cs_lotterytime','integer','P');
            $param[':cs_waittime'] = getGpc('cs_waittime','integer','P');
            $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'case SET cs_name = :cs_name ,cs_bettime = :cs_bettime ,cs_lotterytime = :cs_lotterytime ,cs_waittime = :cs_waittime  where cs_id = :cs_id';

            if($db->exec($sql,$param) !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存失败'));
            }
        }else{
            $cs_id = getGpc('id','integer','G');
            $db = Mysql::connect();
            $info  = $db->getOne('select * from '.config('DB_TABLE_PRE').'case where cs_id = :cs_id',array(':cs_id'=>$cs_id));
            $this->assign('info',$info);
            $this->display();
        }
    }
    public function delete(){
        $cs_id = getGpc('id','integer','G');
        $db = Mysql::connect();
        $user_num  = $db->getOne('select count(1) from '.config('DB_TABLE_PRE').'activity where cs_id == :cs_id',array(':cs_id'=>$cs_id),'NUM');
        if($user_num != 0){
            $this->ajaxReturn(array('status'=>0,'msg'=>'当前方案正在使用中'));
        }
        $param = array(':cs_id'=>$cs_id);
        $sql = 'delete from ' . config('DB_TABLE_PRE') . 'case where cs_id = :cs_id';
        if($db->exec($sql,$param) !== false){
            $this->ajaxReturn(array('status'=>1,'msg'=>'操作成功'));
        } else {
            $this->ajaxReturn(array('status'=>0,'msg'=>'操作失败'));
        }
    }
}