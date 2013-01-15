<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 12-11-21
 * Time: 下午4:59
 * To change this template use File | Settings | File Templates.
 */


/**
 * 获取本系统存放的目录 对应的url
 * 当本系统部署在非站点根目录的时候 需要使用本函数获取系统根目录对应url
 * 其后没有斜杠
 */
function get_baseurl() {
    $baseURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

    if (!$host = $_SERVER['HTTP_HOST']) {
        if (!$host = $_SERVER['SERVER_NAME']) {
            $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        }
    }
    $baseURL .= $host. ($_SERVER["SERVER_PORT"] == "80" ? '' : $_SERVER["SERVER_PORT"]);
    $baseURL .= get_basedir(); //去掉root目录和末尾的/index.php
    return $baseURL;
}

/**
 * 获取本系统存放的目录
 * 相对于站点根目录的相对目录
 * 只能是通过统一入口进入的调用
 * 返回的目录路径前面有杠，后面没杠
 * 如果部署在站点根目录，返回空文本
 */
function get_basedir() {
    return substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
}

/**
 * Simple function to replicate PHP 5 behaviour
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * 获取客户端IP，包括在非匿名代理之后的主机的IP
 * @return string
 */
function get_ip() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * 向浏览器输出错误信息，并停止解析
 * @param $ErrMsg
 */
function err($ErrMsg) {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type:text/plain; charset=utf-8');
    echo $ErrMsg;
    exit;
}

/**
 * 浏览器URL重定向
 * @param string $url  URL
 * @param int $delay 延时
 * @param string $msg 输出信息
 */
function redirect($url, $delay = 0, $msg = '') {
    if (!headers_sent()) {
        if (0 === $delay) {
            header('Location: ' . $url);
        } else {
            header("Content-type: text/plain; charset=UTF-8");
            header("refresh:{$delay};url={$url}");
            echo($msg);
        }
        exit;
    } else {
        $str = "<meta http-equiv='Refresh' content='{$delay};URL={$url}'>";
        if ($delay != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 模板渲染核心函数
 * @param $data
 * @param $tpl_path
 * @return string
 */
function render_tmpl($data, $tpl_path) {
    extract($data, EXTR_SKIP);

    ob_start();
    include $tpl_path;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}