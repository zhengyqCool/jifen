<?php
!defined('IN_FW') && exit('Access Denied');
class Controller {
    protected $_tVar = array();
    protected $_template = '';
    protected $tplfile = '';
    protected $objfile = '';
    
    public function __construct() {
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
    }
    
    protected function ajaxReturn($data) {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
    
    protected function display($_template = '') {
        if ($_template == '') {
            $_template = strtolower(CONTROLLER_NAME) . '/' . ACTION_NAME;
        } else if (!strpos($_template, '/')) {
            $_template = strtolower(CONTROLLER_NAME) . '/' . $_template;
        }
        $this->_template = $this->template($_template);
        if(!$this->_template){
            trigger_error('File not found : ' . $this->tplfile , E_USER_ERROR);
        }
        extract($this->_tVar, EXTR_OVERWRITE);
        include $this->_template;
    }
    
    protected function assign($name,$value='') {
        if(is_array($name)) {
            $this->_tVar   =  array_merge($this->_tVar,$name);
        }else {
            $this->_tVar[$name] = $value;
        }
    }
    
    protected function _get($name){
        return $this->_tVar[$name];
    }
    
    protected function template($file) {
        config('__PUBLIC__') && (defined('__PUBLIC__') or define('__PUBLIC__', HTTP_HOST . '/' . config('__PUBLIC__')));
        $mtplfile = ROOT_PATH . config('__TEMPLATE__') . 'wap/'. $file.'.html';
        $mobjfile = ROOT_PATH . 'cache/template/wap/'.  $file.'.tpl.php';
        if(ismobile() && is_file($mtplfile)){
            $this->tplfile = $mtplfile;
            $this->objfile = $mobjfile;
        }else{
            $this->tplfile = ROOT_PATH . config('__TEMPLATE__') . $file.'.html';
            $this->objfile = ROOT_PATH . 'cache/template/'.  $file.'.tpl.php';
        }
        if(!is_file($this->tplfile)){
            return false;
        }
        if (IS_DEBUG || (@filemtime($this->tplfile) > @filemtime($this->objfile))) {
            $T = new template;
            $T->complie($this->tplfile, $this->objfile);
        }
        return $this->objfile;
    }
    
    public function __call($method,$args) {
        if( 0 === strcasecmp($method,ACTION_NAME)) {
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$args);
            }elseif($this->template(strtolower(CONTROLLER_NAME) . '/' . ACTION_NAME)){
                $this->display();
            }else{
                trigger_error('Method ' . CONTROLLER_NAME . 'Controller::' . ACTION_NAME . '() does not exist', E_USER_ERROR);
            } 
        }else{
            trigger_error('Method ' . CONTROLLER_NAME . 'Controller::' . $method . '() does not exist', E_USER_ERROR);
        }
    }
}
?>