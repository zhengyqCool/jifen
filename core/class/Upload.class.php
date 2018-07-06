<?php
!defined('IN_FW') && exit('Access Denied');
class Upload {
    private $config = array(
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'rootPath'      =>  './Uploads/', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
    );
    private $error = '';
    public function __get($name) {
        return $this->config[$name];
    }

    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    public function __isset($name){
        return isset($this->config[$name]);
    }
    
    public function __construct($config = array()){
        $this->config   =   array_merge($this->config, $config);
        if(!empty($this->config['exts'])){
            if (is_string($this->exts)){
                $this->config['exts'] = explode(',', $this->exts);
            }
            $this->config['exts'] = array_map('strtolower', $this->exts);
        }
    }
    
    /**
     * 上传文件
     * @param  $file 文件信息数组，通常是 $_FILES数组
     * @return bool
     */
    public function upload($file) {
        $file = $this->initFile($file);
        if (!$this->check($file)){
            return false;
        }
        if(!$this->checkSavePath($this->savePath)){
            return false;
        }
        if ($this->save($file)) {
            unset($file['error'], $file['tmp_name']);
            if(isset($finfo)){
                finfo_close($finfo);
            }
            return $file;
        }else{
            return false;
        }
    }
    private function save($file) {
        $filename = $this->rootPath . $file['savepath'] . $file['savename'];
        if (is_file($filename)) {
            $this->error = '存在同名文件' . $file['savename'];
            return false;
        }
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误！';
            return false;
        }
        return true;
    }
    private function initFile($file){
        if(function_exists('finfo_open')){
            $finfo   =  finfo_open ( FILEINFO_MIME_TYPE );
        }
        $file['savepath']  = $this->savePath;
        $file['name']  = strip_tags($file['name']);
        $file['ext']   = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(!isset($file['key']))   $file['key']    =   $key;
        $savename = $this->getSaveName($file);
        if(false == $savename){
            return false;
        } else {
            $file['savename'] = $savename;
        }
        return $file;
    }
    private function checkSavePath($savepath){
        if (!$this->mkdir($savepath)) {
            $this->error = '上传目录 ' . $savepath . ' 不可写！';
            return false;
        } else {
            if (!is_writable($this->rootPath . $savepath)) {
                $this->error = '上传目录 ' . $savepath . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
    }
    private function mkdir($savepath){
        $dir = $this->rootPath . $savepath;
        if(is_dir($dir)){
            return true;
        }
        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savepath} 创建失败！";
            return false;
        }
    }
    private function check($file) {
        if ($file['error']) {
            $this->error($file['error']);
            return false;
        }
        if (empty($file['name'])){
            $this->error = '未知上传错误！';
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }
        if (!$this->checkExt($file['ext'])) {
            $this->error = '上传文件后缀不允许';
            return false;
        }
        $ext = strtolower($file['ext']);
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png','swf'))) {
            $imginfo = getimagesize($file['tmp_name']);
            if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
                $this->error = '非法图像文件！';
                return false;
            }
        }
        return true;
    }
    private function error($errorNo) {
        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！';
                break;
            case 3:
                $this->error = '文件只有部分被上传！';
                break;
            case 4:
                $this->error = '没有文件被上传！';
                break;
            case 6:
                $this->error = '找不到临时文件夹！';
                break;
            case 7:
                $this->error = '文件写入失败！';
                break;
            default:
                $this->error = '未知上传错误！';
        }
    }
    private function checkSize($size) {
        return !($size > $this->maxSize) || (0 == $this->maxSize);
    }
    private function checkExt($ext) {
        return empty($this->config['exts']) ? true : in_array(strtolower($ext), $this->exts);
    }
    private function getSaveName($file) {
        $rule = empty($this->saveName) ? $this->saveName : 'uniqid';
        $savename = $this->getName($rule, $file['name']);
        if(empty($savename)){
            $this->error = '文件命名规则错误！';
            return false;
        }
        $ext = $file['ext'];
        return $savename . '.' . $ext;
    }
    private function getName($rule, $filename){
        $name = '';
        if(is_array($rule)){
            $func     = $rule[0];
            $param    = (array)$rule[1];
            foreach ($param as &$value) {
               $value = str_replace('__FILE__', $filename, $value);
            }
            $name = call_user_func_array($func, $param);
        } elseif (is_string($rule)){
            if(function_exists($rule)){
                $name = call_user_func($rule);
            } else {
                $name = $rule;
            }
        }
        return $name;
    }
    public function getError(){
        return $this->error;
    }
}
