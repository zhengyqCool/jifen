<?php
!defined('IN_FW') && exit('Access Denied');
class Model {
    protected $db;
    public function __construct() {
        $this->db = Mysql::connect();
    }
}