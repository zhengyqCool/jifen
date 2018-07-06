<?php
!defined('IN_FW') && exit('Access Denied');

class template {
    public $mVarRegexp = '@[a-zA-Z_]\w*\(.*?\)|\$[a-zA-Z_]\w*(?:\[[\w\-\.\"\'\[\]\$]+\])*';
    public $mVtagRegexp = '\<\?php echo (@[a-zA-Z_]\w*\(.*?\)|\$[a-zA-Z_]\w*(?:\[[\w\-\.\"\'\[\]\$]+\])*)\;\?\>';
    public $mConstRegexp = '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';

    function __construct(){
        
    }

    /**
    *  读模板页进行替换后写入到cache页里
    *
    * @param string $tplFile ：模板源文件地址
    * @param string $objFile ：模板cache文件地址
    * @return string
    */
    function complie($tplFile, $objFile) {
        $template = iReadFile($tplFile);
        $template = $this->parse($template);
        makeDir(dirname($objFile));
        writeFile($objFile, $template, $mod = 'w', TRUE);

    }

    /**
    *  解析模板标签
    *
    * @param string $template ：模板源文件内容
    * @return string
    */
    function parse($template) {
        $template = preg_replace("/\{($this->mVarRegexp)\}/", "<?php echo \\1;?>", $template);//替换带{}的变量
        $template = preg_replace("/\{($this->mConstRegexp)\}/", "<?php echo \\1;?>", $template);//替换带{}的常量
        $template = preg_replace("/\{php (.*?)\}/is", $this->stripVtag('<?php \\1?>'), $template);//替换php标签
        $template = preg_replace("/\{for (.*?)\}/is", $this->stripVtag('<?php for (\\1) {?>'), $template);//替换for标签
        $template = preg_replace("/\{foreach (.*?)\}/is", $this->stripVtag('<?php foreach (\\1) {?>'), $template);//替换foreach标签
        $template = preg_replace("/\{elseif\s+(.+?)\}/is", $this->stripVtag('<?php } elseif (\\1) { ?>'), $template);//替换elseif标签
        for ($i=0; $i<3; $i++) {
            $template = preg_replace("/\{loop\s+($this->mVarRegexp)\s+($this->mVarRegexp)\s+($this->mVarRegexp)\}(.+?)\{\/loop\}/is", $this->loopSection('\\1', '\\2', '\\3', '\\4'), $template);
            $template = preg_replace("/\{loop\s+($this->mVarRegexp)\s+($this->mVarRegexp)\}(.+?)\{\/loop\}/is", $this->loopSection('\\1', '', '\\2', '\\3'), $template);
        }
        $template = preg_replace("/\{if\s+(.+?)\}/is", $this->stripVtag('<?php if (\\1) { ?>'), $template);//替换if标签
        $template = preg_replace("/\{include\s+(.*?)\}/is", "<?php include \\1; ?>", $template);//替换include标签
        $template = preg_replace("/\{template\s+([\w\/\.]+?)\}/is", '<?php include $this->template("\\1"); ?>', $template);//替换template标签
        $template = preg_replace("/\{else\/\}/is", "<?php } else { ?>", $template);//替换else标签
        $template = preg_replace("/\{\/if\}/is", "<?php } ?>", $template);//替换/if标签
        $template = preg_replace("/\{\/for\}/is", "<?php } ?>", $template);//替换/for标签
        $template = preg_replace("/\{\/foreach\}/is", "<?php } ?>", $template);//替换/foreach标签
        $template = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $template);//将二维数组替换成带单引号的标准模式
        $template = "<?php if (!defined('IN_FW')) exit('Access Denied');?>\r\n$template";
        return $template;
    }

    /**
    * 正则表达式匹配替换
    *
    * @param string $s ：
    * @return string
    */
    function stripVtag($s) {
        return preg_replace("/$this->mVtagRegexp/is", "\\1", str_replace("\\\"", '"', $s));
    }

    function stripTagQuotes($expr) {
        $expr = preg_replace("/\<\?php echo (\\\$.+?);\?\>/s", "{\\1}", $expr);
        $expr = str_replace("\\\"", "\"", preg_replace("/\[\'([a-zA-Z0-9_\-\.\x7f-\xff]+)\'\]/s", "[\\1]", $expr));
        return $expr;
    }
    
    /**
    * 替换模板中的LOOP循环
    *
    * @param string $arr ：
    * @param string $k ：
    * @param string $v ：
    * @param string $statement ：
    * @return string
    */
    function loopSection($arr, $k, $v, $statement) {
        $arr = $this->stripVtag($arr);
        $k = $this->stripVtag($k);
        $v = $this->stripVtag($v);
        $statement = str_replace("\\\"", '"', $statement);
        return $k ? "<?php if(is_array($arr)){foreach ((array)$arr as $k=>$v) {?>$statement<?php }}?>" : "<?php if(is_array($arr)){foreach ((array)$arr as $v) {?>$statement<?php }} ?>";
    }
    /**
    * 替换模板中的内容调用
    *
    * @param string $arr ：
    * @param string $k ：
    * @param string $v ：
    * @param string $statement ：
    * @return string
    */
}
/**********************************

**********************************/