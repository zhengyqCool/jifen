<?php
!defined('IN_FW') && exit('Access Denied');
/**
 * Class Db
 */
abstract class Db{
    /**
     * @var int
     */
    public $mQueryCount = 0;
    /**
     * @var
     */
    public $mConn;
    /**
     * @var
     */
    public $mResult;
    /**
     * @var
     */
    public $affectedRows;
    /**
     * @var int
     */
    public $mRsType = PDO::FETCH_ASSOC;
    
    
    /**
     * 连接数据库
     * @return void
     */
    abstract static public function connect();

    /**
     * 关闭数据库连接
     *
     * @return null
     */
    public function close(){
        $this->mConn = null;
        return ;
    }

    /**
     * 发送查询语句
     *
     * @param string $sql
     * @param string $type ['ASSOC' | 'NUM' | 'BOTH']
     * @param array $param
     * @return boolean
     */
    public function query($sql, $param = array(),$type = "ASSOC") {
        $this->mResult->closeCursor();
        $this->mRsType = $type != "ASSOC" ? ($type == "NUM" ? PDO::FETCH_NUM : PDO::FETCH_BOTH) : PDO::FETCH_ASSOC;
        $this->mResult = $this->mConn->prepare($sql);
        foreach($param as $key=>$val){
            $this->mResult->bindValue($key, $val);
        }
        $this->mResult->execute();
        return $this->mResult;
        /**
         *
         *
         *   $this->mResult = $this->mConn->query($sql);
         *   $this->mQueryCount++;
         *   if (!$this->mResult)
         *   {
         *       return $this->errorMsg('Db Query Error', $sql);
         *   }
         *   else
         *   {
         *       return $this->mResult;
         *   }
         */

    }

    /**
     * 数据量比较大的情况下查询
     *
     * @param string $sql
     * @param string $type ['ASSOC' | 'NUM' | 'BOTH']
     * @param array $param
     * @return blooean
     */
    public function bigQuery($sql, $param = array(),$type = "ASSOC") {
        $this->mResult->closeCursor();
        $this->mConn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $this->mRsType = $type != "ASSOC" ? ($type == "NUM" ? PDO::FETCH_NUM : PDO::FETCH_BOTH) : PDO::FETCH_ASSOC;
        $this->mResult = $this->mConn->prepare($sql);
        foreach($param as $key=>$val){
            $this->mResult->bindValue($key, $val);
        }
        $this->mResult->execute();
        return $this->mResult;

        /**
        *   $this->mRsType = $type != "ASSOC" ? ($type == "NUM" ? PDO::FETCH_NUM : PDO::FETCH_BOTH) : PDO::FETCH_ASSOC;
        *   $this->mResult = $this->mConn->query($sql);
        *   $this->mQueryCount++;
        *   if (!$this->mResult)
        *   {
        *       return $this->errorMsg('Db Query Error', $sql);
        *   }
        *   else
        *   {
        *      return $this->mResult;
        *   }
        */
    }
    /**
     * 执行数据库查询
     *
     * @param string $sql
     * @param array $param
     * @return int
     */
    public function exec($sql,$param = array()){
        $this->mResult = $this->mConn->prepare($sql);
        foreach($param as $key=>$val){
            $this->mResult->bindValue($key, $val);
        }
        if($this->mResult->execute()) {
            $this->affectedRows = $this->mResult->rowCount();
            $this->mResult->closeCursor();
            return $this->affectedRows;
        }else{
            return false;
        }
        /*
        $this->affectedRows = $this->mConn->exec($sql);
        return $this->affectedRows;
        */
    }
    /**
     * 获取全部数据
     *
     * @param string $sql
     * @param array $param
     * @param bool $noCache
     * @return array
     */
    public function getAll($sql,$param = array(), $noCache = false) {
        $noCache ? $this->bigQuery($sql,$param) : $this->query($sql,$param);
        $rows = array();
        while ($row = $this->mResult->fetch($this->mRsType)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 获取单行数据
     *
     * @param string $sql
     * @param array $param
     * @param string $type
     * @return array
     */
    public function getOne($sql,$param = array(),$type = "ASSOC") {
        if(is_string($param) && $type == "ASSOC"){
            $type = $param;
            $this->query($sql,array(),$type);
        }else{
            $this->query($sql,$param,$type);
        }
        $rows = array();
        if($this->numFields() == 1 && $this->mRsType == PDO::FETCH_NUM){
            $row = $this->mResult->fetch($this->mRsType);
            return $row[0];
        }else{
            while ($row = $this->mResult->fetch($this->mRsType)) {
                $rows[] = $row;
            }
            return $rows[0];
        }
    }

    /**
     * 从结果集中取得一行作为关联数组，或数字数组
     *
     * @return array
     */
    public function fetchArray() {
        return $this->mResult->fetch($this->mRsType);
    }

    /**
     * 取得上一步 INSERT 操作产生的 ID
     *
     * @return integer
     */

    public function insertId() {
        return $this->mConn->lastInsertId();
    }

    /**
     * 取得行的数目
     *
     * @return integer
     */
    public function numRows() {
        return $this->mResult->rowCount();
    }

    /**
     * 取得结果集中字段的数目
     *
     * @return integer
     */
    public function numFields() {
        return $this->mResult->columnCount();
    }

    /**
     * 取得前一次 MySQL 操作所影响的记录行数
     *
     *
     * @return integer
     */
    public function affectedRows() {
        return $this->affectedRows;
    }

    /**
     * 取得结果中指定字段的字段名
     *
     * @param string $table
     * @return array
     */
    public function listFields($table){
        $select = $this->query('SELECT * FROM '.$table . ' limit 1');
        $total_column = $this->numFields($select);
        $column = array();
        for ($counter = 0; $counter <= $total_column; $counter ++) {
            $meta = $select->getColumnMeta($counter);
            $column[] = $meta['name'];
        }
        return $column;
    }

    /**
     * 列出数据库中的表
     *
     * @return array
     */
    public function listTables() {
        $sql    = 'SHOW TABLES ';
        $result = $this->getAll($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans(){
        $this->mConn->beginTransaction();
    }
    /**
     * 事务提交
     * @access public
     * @return void
     * @throws PDOException
     */
    public function commit() {
        $this->mConn->commit();
    }

    /**
     * 事务回滚
     * @access public
     * @return void
     * @throws PDOException
     */
    public function rollback() {
        $this->mConn->rollBack();
    }
    /**
     * 输出错误信息
     *
     * @param string $msg
     * @param string $sql
     * @return void
     */
    public function errorMsg($msg = '', $sql = '', $trace = true) {
        if (IS_DEBUG) {
            if ($msg) {
                echo "<b>ErrorMsg</b>:".$msg."<br />";
            }
            if ($sql) {
                echo "<b>SQL</b>:".htmlspecialchars($sql)."<br />";
            }
            if($trace){
                $error = $this->mConn->errorInfo();
                echo "<b>Error</b>:  " .htmlspecialchars($error[2])."<br />";
                echo "<b>Errno</b>: ".$error[1];
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            exit;
        }
        exit();
    }
}
/**********************************

**********************************/
?>