<?php
!defined('IN_FW') && exit('Access Denied');

class framework {
    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function start() {
        date_default_timezone_set('Asia/Chongqing');	//时区设置
        ini_set("session.cookie_httponly", 1);
        error_reporting(E_ERROR | E_WARNING | E_PARSE);		//定义PHP报错级别
        defined('IN_FW') or define('IN_FW', true);		//定义入口常量
        defined('COOKIE_PRE') or define('COOKIE_PRE', 'hnek_');		//定义cookie前缀
        defined('COOKIE_PATH') or define('COOKIE_PATH', '/');		//定义cookie作用域
        defined('COOKIE_DOMAIN') or define('COOKIE_DOMAIN', '');		//定义cookie作用路径
        defined('AUTHKEY') or define('AUTHKEY', '54c5733dfggbd9c8e');	//定义加密所需Key，请修改此处为唯一
        defined('IS_DEBUG') or define('IS_DEBUG', true);	//定义后台调试模式
        
        // 注册AUTOLOAD方法
        spl_autoload_register('framework::autoload');      
        // 设定错误和异常处理
        register_shutdown_function('framework::fatalError');
        set_error_handler('framework::appError');
        set_exception_handler('framework::appException');
        //加载配置文件
        require_once FW_PATH.'common/config.php';  // 加载配置文件
        config($config);
        //加载自定义配置文件
        if(is_file(ROOT_PATH.'common/config.php')){
            require_once ROOT_PATH.'common/config.php';
            config($config);
        }
        //加载自定义函数库
        if(is_file(ROOT_PATH.'common/function.php')){
            require_once ROOT_PATH.'common/function.php';
        }
        //自动开启session
        session_start();
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME',   (!empty($_GET['c'])? ucfirst($_GET['c']):config('DEFAULT_CONTROLLER')));
        defined('ACTION_NAME') or define('ACTION_NAME',       (!empty($_GET['a'])? $_GET['a']:config('DEFAULT_ACTION')));
        $controller_name = CONTROLLER_NAME . 'Controller';
        try{
            $reflectionMethod = new ReflectionMethod($controller_name, ACTION_NAME);
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $vars    =  array_merge($_GET,$_POST);
            }else{
                $vars    =  $_GET;
            }
            $reflectionMethod->invokeArgs(new $controller_name, array($vars));
        } catch (ReflectionException $e) {
            try{
                $reflectionMethod = new ReflectionMethod($controller_name,'__call');
                $reflectionMethod->invokeArgs(new $controller_name,array(ACTION_NAME,''));
            }catch (ReflectionException $e) {
                trigger_error('Method ' . CONTROLLER_NAME . 'Controller::' . ACTION_NAME . '() does not exist', E_USER_ERROR);
            }
        }
    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return boolean
     */
    public static function autoload($class) {
        $class = ucfirst($class);
        $name = substr($class,-10);
        if($name == 'Controller' && is_file(ROOT_PATH.'controller/'.$class.'.class.php')) {
            require_once ROOT_PATH.'controller/'.$class.'.class.php';
        }
        $name = substr($class,-5);
        if($name == 'Model' && is_file(ROOT_PATH.'model/'.$class.'.class.php')) {
            require_once ROOT_PATH.'model/'.$class.'.class.php';
        }
        if(is_file(FW_PATH.'class/'.$class.'.class.php')) {
            require_once FW_PATH.'class/'.$class.'.class.php';
        }elseif(is_file(ROOT_PATH.'class/'.$class.'.class.php')) {
            require_once ROOT_PATH.'class/'.$class.'.class.php';
        }
        return false;
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
        $error = array();
        $error['message']   =   $e->getMessage();
        $trace              =   $e->getTrace();
        if('E'==$trace[0]['function']) {
            $error['file']  =   $trace[0]['file'];
            $error['line']  =   $trace[0]['line'];
        }else{
            $error['file']  =   $e->getFile();
            $error['line']  =   $e->getLine();
        }
        self::halt($error);
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
        $e = array();
        $e['message']   = $errstr;
        $e['file']      = $errfile;
        $e['line']      = $errline;
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                self::halt($e);
                break;
        }
    }
    
    // 致命错误捕获
    static public function fatalError() {
        session_write_close();
        if ($e = error_get_last()) {
            switch($e['type']){
              case E_ERROR:
              case E_PARSE:
              case E_CORE_ERROR:
              case E_COMPILE_ERROR:
              case E_USER_ERROR:  
                ob_end_clean();
                self::halt($e);
                break;
            }
        }
    }
    
    static public function halt($error) {
        if (IS_DEBUG) {
            $e              = $error;
            $trace          = debug_backtrace();
            ob_start();
            debug_print_backtrace();
            $e['trace']     = ob_get_clean();
            $exceptionFile =  FW_PATH.'template/exception.html';
            include $exceptionFile;
            exit;
        } else {
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            $exceptionFile =  FW_PATH.'template/404.html';
            include $exceptionFile;
            exit;
        }
    }
}
