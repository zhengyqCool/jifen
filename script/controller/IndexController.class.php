<?php
defined("SCRIPT_IN") or die('illegal origin');
class IndexController extends Controller {
    public function crontabone() {
        $this->updateActivity();
        $this->createActivity();
    }


    public function updateActivity() {
        $db = Mysql::connect();
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ad_status != 3');
        foreach($list as $val){
            //更新活动状态
            if($val['ad_betendtime'] < time() && $val['ad_status'] == 1){
                $sql = 'update ' . config('DB_TABLE_PRE') . 'activity_data set ad_status = 2 where ad_id = :ad_id';
                $db->exec($sql,array(':ad_id'=>$val['ad_id']));
            }
            //开奖
            if($val['ad_lotterytime'] < time() && $val['ad_status'] != 3){
                $db->startTrans();
                $sql = 'update ' . config('DB_TABLE_PRE') . 'activity_data set ad_status = 3 where ad_id = :ad_id';
                if($db->exec($sql,array(':ad_id'=>$val['ad_id']))){
                    $activity = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_id = :ac_id',array(':ac_id'=>$val['ac_id']));
                    $log = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'bet_log where ad_id = :ad_id',array(':ad_id'=>$val['ad_id']));
                    $total = count($log);
                    if($total > 0) {
                        $totle_point = 0;
                        $awards1_user = null;
                        $awards2_user = null;
                        $awards3_user = null;
                        foreach ($log as $k => $v) {
                            $totle_point += $v['log_point'];
                            if ($v['log_awards'] == 1) {
                                $awards1_user = $k;
                            }
                            if ($v['log_awards'] == 2) {
                                $awards2_user = $k;
                            }
                            if ($v['log_awards'] == 3) {
                                $awards3_user = $k;
                            }
                        }
                        $awards_user = $this->makeAwards(0, $total - 1);
                        $awards1_user = $awards1_user !== null && in_array($awards_user[0],array($awards2_user,$awards3_user))? $awards1_user : $awards_user[0];
                        $awards2_user = $awards2_user !== null && in_array($awards_user[1],array($awards1_user,$awards3_user))? $awards2_user : $awards_user[1];
                        $awards3_user = $awards3_user !== null && in_array($awards_user[2],array($awards1_user,$awards2_user))? $awards3_user : $awards_user[2];

                        $awards1_point = ceil($totle_point * $activity['ac_awards1'] / 100);
                        $awards2_point = ceil($totle_point * $activity['ac_awards2'] / 100);
                        $awards3_point = ceil($totle_point * $activity['ac_awards3'] / 100);
                        $awards4_point = ceil($totle_point * $activity['ac_awards4'] / 100);

                        foreach ($log as $k => $v) {
                            $awards = 0;
                            $totle_point += $v['log_point'];
                            if ($k == $awards1_user) {
                                $point = $awards1_point;
                                $awards = 1;
                            } elseif ($k == $awards2_user) {
                                $point = $awards2_point;
                                $awards = 2;
                            } elseif ($k == $awards3_user) {
                                $point = $awards3_point;
                                $awards = 3;
                            } else {
                                if($total > 3 ) {
                                    $point = floor($awards4_point / ($total - 3));
                                    $awards = 4;
                                }
                            }
                            if($awards) {
                                $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'user SET us_point = us_point + :us_point where us_id = :us_id';
                                if ($db->exec($sql, array(':us_point' => $point, ':us_id' => $v['us_id'])) !== false) {
                                    $sql = 'insert into ' . config('DB_TABLE_PRE') . 'user_log(us_id,log_action,log_point,log_description,log_time) values(:us_id,:log_action,:log_point,:log_description,:log_time)';
                                    $db->exec($sql, array(':us_id' => $v['us_id'], ':log_action' => '2', ':log_point' => $point, ':log_description' => $activity['ac_name'] . '第' . $val['ad_name'] . '期开奖', ':log_time' => date('Y-m-d H:i:s')));
                                    $sql = 'UPDATE ' . config('DB_TABLE_PRE') . 'bet_log SET log_awards = :log_awards, log_awards_point = :log_awards_point where log_id = :log_id';
                                    $db->exec($sql, array(':log_id' => $v['log_id'], ':log_awards' => $awards, ':log_awards_point' => $point));
                                } else {
                                    $db->rollback();
                                }
                            }
                        }
                    }
                    $db->commit();
                }else{
                    $db->rollback();
                }
            }
        }
		$db->close();
    }

    private function createActivity(){
        $db = Mysql::connect();
        $list = $db->getAll('select * from ' . config('DB_TABLE_PRE') . 'activity where ac_status = 1');
        foreach($list as $val){
            $data = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'activity_data where ac_id = :ac_id order by ad_lotterytime desc limit 1',array(':ac_id'=>$val['ac_id']));
            if(!$data || $data['ad_status'] == 3){
                $case = $db->getOne('select * from ' . config('DB_TABLE_PRE') . 'case where cs_id = :cs_id',array(':cs_id'=>$val['cs_id']));
                $param = array();
                $param['ac_id'] = $val['ac_id'];
                $param['ad_name'] = date('YmdHi');
                if($data && time() < ($data['ad_lotterytime']  + $case['cs_watttime'] * 60)){
                    $param['ad_betstarttime'] = $data['ad_lotterytime'] + $case['cs_watttime'] * 60;
                }elseif(time() < $val['ac_starttime']){
                    $param['ad_betstarttime'] = $val['ac_starttime'];
                }else{
                    $param['ad_betstarttime'] = time();
                }
                $param['ad_betendtime'] = $param['ad_betstarttime'] + $case['cs_bettime'] * 60;
                $param['ad_lotterytime'] = $param['ad_betendtime'] + $case['cs_lotterytime'] * 60;
                $sql = 'insert into ' . config('DB_TABLE_PRE') . 'activity_data(ac_id,ad_name,ad_betstarttime,ad_betendtime,ad_lotterytime,ad_status) values(:ac_id,:ad_name,:ad_betstarttime,:ad_betendtime,:ad_lotterytime,1)';
                $db->exec($sql,$param);
            }
        }
        $db->close();
    }

    private function makeAwards($min,$max){
        if($max - $min < 2){
            if($max-$min != 0) {
                return array($min, $max, null);
            }else{
                return array($min, null, null);
            }
        }
        $awards1 = rand($min,$max);
        $awards2 = rand($min,$max);
        $awards3 = rand($min,$max);
        while ($awards2 == $awards1) {
            $awards2 = rand($min,$max);
        }
        while ($awards2 == $awards3 || $awards1 == $awards3 ) {
            $awards3 = rand(1,$max);
        }
        return array($awards1,$awards2,$awards3);
    }
}