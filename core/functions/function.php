<?php
/**
 * @param null $name
 * @param null $value
 * @param null $default
 * @return array|mixed|null
 */
function config($name = null, $value = null, $default = null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value)) {
                return isset($_config[$name]) ? $_config[$name] : $default;
            }
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        if (is_null($value)) {
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        }
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}

/**
 * @param $string
 * @return bool
 */
function is_utf8($string) {
    if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $string) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $string) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $string) == true) {
        return true;
    } else {
        return false;
    }
}

/**
 * 取真实IP地址
 *
 * @return string;
 */
function getIp() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else {
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
                $ip = getenv("REMOTE_ADDR");
            } else {
                if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ip = "unknown";
                }
            }
        }
    }
    return ($ip);
}

/**
 * 获得一段随机代码
 *
 * @param integer $length - 字符长度
 * 描述：长度为1-x
 *
 * @return string 1位随机x进制字串
 *
 **/
function getRandID($length) {
    $pattern = '0123456789';
    $ranstr = '';
    for ($i = 0; $i < $length; $i++) {
        $ranstr .= $pattern{mt_rand(0, strlen($pattern) - 1)};    //生成php随机数
    }
    return $ranstr;
}

/**
 * 为变量或者数组添加转义
 *
 * @param string $value - 字符串或者数组变量
 * @return array
 */
function iAddslashes($value) {
    return $value = is_array($value) ? array_map('iAddslashes', $value) : addslashes($value);
}

/**
 * 为变量或者数组添加反转义
 *
 * @param string $value - 字符串或者数组变量
 * @return array
 */
function iStripslashes($value) {
    return $value = is_array($value) ? array_map('iStripslashes', $value) : stripslashes($value);
}

/**
 * 根据中文裁减字符串
 *
 * @param string $string - 待截取的字符串
 * @param integer $length - 截取字符串的长度
 * @param string $g_charset - 字符集
 * @param string $dot - 缩略后缀
 * @return string 返回带省略号被裁减好的字符串
 */
function iCutstr($string, $length, $g_charset = 'utf-8', $dot = ' ...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    $strcut = '';
    if (strtolower($g_charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {

            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    return $string == $strcut ? $strcut : $strcut . $dot;
}

/**
 * 获取GPC变量。对于type为integer的变量强制转化为数字型
 *
 * @param string $key - 权限表达式
 * @param string $type - integer 数字类型；string 字符串类型；array 数组类型
 * @param string $var - R $REQUEST变量；G $GET变量；P $POST变量；C $COOKIE变量
 * @return string 返回经过过滤或者初始化的GPC变量
 */
function getGpc($key, $type = 'integer', $var = 'R') {
    switch ($var) {
        case 'G':
            $var = &$_GET;
            break;
        case 'P':
            $var = &$_POST;
            break;
        case 'C':
            $var = &$_COOKIE;
            break;
        case 'R':
            $var = &$_REQUEST;
            break;
    }
    switch ($type) {
        case 'integer':
            $return = isset($var[$key]) ? intval($var[$key]) : 0;
            break;
        case 'string':
            $return = isset($var[$key]) ? $var[$key] : '';
            break;
        case 'array':
            $return = isset($var[$key]) ? $var[$key] : array();
            break;
        default:
            $return = isset($var[$key]) ? intval($var[$key]) : 0;
    }
    if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
        $return = iStripslashes($return);
    }
    return $return;
}

/**
 * 设置cookie
 *
 * @param mixed $var - 变量名
 * @param mixed $value - 变量值
 * @param mixed $life - 生命周期期
 * @param mixed $prefix - 前缀
 */
function iSetCookie($var, $value, $life = 0, $prefix = 1) {
    setcookie(($prefix ? FW_COOKIE_PRE : '') . $var, $value, $life ? time() + $life : 0, FW_COOKIE_PATH, FW_COOKIE_DOMAIN, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}


/**
 * PHP下递归创建目录的函数，使用示例fwMakeDir('D:\web\web/a/b/c/d/f');
 *
 * @param string $dir - 需要创建的目录路径，可以是绝对路径或者相对路径
 * @return boolean 返回是否写入成功
 */
function makeDir($dir) {
    return is_dir($dir) or (makeDir(dirname($dir)) and mkdir($dir, 0777));
}

/**
 * 读文件
 *
 * @param string $file - 需要读取的文件，系统的绝对路径加文件名
 * @param boolean $exit - 不能读入是否中断程序，默认为中断
 * @return boolean 返回文件的具体数据
 */
function iReadFile($file, $exit = TRUE) {
    if (!@$fp = @fopen($file, 'rb')) {
        if ($exit) {
            exit('File :<br>' . $file . '<br>Have no access to read!');
        } else {
            return false;
        }
    } else {
        @$data = fread($fp, filesize($file));
        fclose($fp);
        return $data;
    }
}

/**
 * 写文件
 *
 * @param string $file - 需要写入的文件，系统的绝对路径加文件名
 * @param string $content - 需要写入的内容
 * @param string $mod - 写入模式，默认为w
 * @param boolean $exit - 不能写入是否中断程序，默认为中断
 * @return boolean 返回是否写入成功
 */
function writeFile($file, $content, $mod = 'w', $exit = TRUE) {
    if (!@$fp = @fopen($file, $mod)) {
        if ($exit) {
            exit('File :<br>' . $file . '<br>Have no access to write!');
        } else {
            return false;
        }
    } else {
        @flock($fp, 2);
        @fwrite($fp, $content);
        @fclose($fp);
        return true;
    }
}

/**
 * 取消HTML代码
 *
 * @param mixed $string
 * @return mixed
 */
function iHtmlSpecialChars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = iHtmlSpecialChars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

/**
 * 生成简单验证码，需配合cookie验证或者session验证
 * @param string $text - 需要生成的验证码
 * @param integer $im_x - 宽
 * @param integer $im_y - 高
 * @return none 无返回
 */
function getSecode($text, $im_x = 100, $im_y = 40) {
    $im = imagecreatetruecolor($im_x, $im_y);
    $tmpC0 = mt_rand(100, 255);
    $tmpC1 = mt_rand(100, 255);
    $tmpC2 = mt_rand(100, 255);
    $buttum_c = ImageColorAllocate($im, $tmpC0, $tmpC1, $tmpC2);
    imagefill($im, 0, 0, $buttum_c);

    $array = array(-1, 1);

    for ($i = 0; $i < strlen($text); $i++) {
        $tmp = substr($text, $i, 1);
        $an = $array[array_rand($array)] * mt_rand(20, 20);//角度
        $size = 20;
        $text_c = ImageColorAllocate($im, mt_rand(0, 50), mt_rand(0, 100), mt_rand(0, 50));
        $font = FW_PATH . 'include/fonts/' . mt_rand(1, 3) . '.ttf';
        imagettftext($im, $size, $an, 5+$i * $size, 30, $text_c, $font, $tmp);
    }
    $distortion_im = imagecreatetruecolor($im_x, $im_y);
    imagefill($distortion_im, 16, 13, $buttum_c);
    for ($i = 0; $i < $im_x; $i++) {
        for ($j = 0; $j < $im_y; $j++) {
            $rgb = imagecolorat($im, $i, $j);
            if ((int)($i + 20 + sin($j / $im_y * 2 * M_PI) * 10) <= imagesx($distortion_im) && (int)($i + 20 + sin($j / $im_y * 2 * M_PI) * 10) >= 0) {
                imagesetpixel($distortion_im, (int)($i + 10 + sin($j / $im_y * 2 * M_PI - M_PI * 0.1) * 4), $j, $rgb);
            }
        }
    }
    //加入干扰象素;
    $count = 160;//干扰像素的数量
    for ($i = 0; $i < $count; $i++) {
        $randcolor = ImageColorallocate($distortion_im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imagesetpixel($distortion_im, mt_rand() % $im_x, mt_rand() % $im_y, $randcolor);
    }

    //设置文件头;
    Header("Content-type: image/JPEG");

    //以PNG格式将图像输出到浏览器或文件;
    ImagePNG($distortion_im);

    //销毁一图像,释放与image关联的内存;
    ImageDestroy($distortion_im);
    ImageDestroy($im);
}

/**
 * 判断是否手机浏览
 * @return bool true是，false否
 */
function ismobile() {
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset ($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) {
        return true;
    }
    if (isset ($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 检查字符串是否是邮箱格式
 * @param string $address 需要检查的字符串
 * @return bool true是，false否
 */
function is_phone($address) {
    return preg_match('/^1[34578][0-9]{9}$/', $address);
}
/**********************************
 **********************************/