<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-14
 * Time: 下午2:14
 * To change this template use File | Settings | File Templates.
 */
include_once(dirname(__FILE__) . '/HttpReq.class.php');

class JnuJwc {
    private $http;
    private $cookies = array();
    private $last_html;
    private $last_post_data = array();
    public $current_stu = '';
    public $current_stu_name = '';
    public  $current_stu_major = '';

    const JWC_LOGIN_URL = 'http://jwc.jnu.edu.cn/web/Login.aspx';
    const JWC_SCORE_URL = 'http://jwc.jnu.edu.cn/web/Secure/Cjgl/Cjgl_Cjcx_XsCxXqCj.aspx';

    function __construct() {
        $this->http = new HttpReq();
    }
    /**
     * 从代码中抽取EVENTVALIDATION 和 VIEWSTATE
     * @param $page_html
     * @return array
     */
    static function get_evvs($page_html) {
        if (preg_match('/id="__VIEWSTATE"\svalue="(.*)"/', $page_html, $result1)) {
            if (preg_match('/id="__EVENTVALIDATION"\svalue="(.*)"/', $page_html, $result2)) {
                return array(
                    '__VIEWSTATE' =>  $result1[1],
                    '__EVENTVALIDATION' => $result2[1]
                );
            }
        }

        return array();
    }

    /**
     * 取登陆页的验证码
     * @param $login_page_html
     * @return mixed
     */
    static function get_verifycode($login_page_html) {
        preg_match('/id="lblFJM".*>(\d+)</', $login_page_html, $result1);
        return $result1[1];
    }

    /**
     * 取学生姓名院系专业
     * @param $score_page_html
     * @return array
     */
    static function get_stuinfo($score_page_html) {
        preg_match('/<input\sname="txtXM".*value="(.*)"\sre/', $score_page_html, $result1);
        preg_match('/<input\sname="txtYXZY".*value="(.*)"\sre/', $score_page_html, $result2);

        return array(
            'txtXM' => $result1[1], //姓名
            'txtYXZY' => $result2[1], //学院 专业
        );
    }

    static function _2gbk($utf8_str) { //转换到gbk编码，不转的话服务器会500，弱爆了
        return @iconv('utf-8', 'gbk', $utf8_str);
    }

    static function _2utf8($gbk_str) {
        return @iconv('gbk', 'utf-8', $gbk_str);
    }

    static function _strMid($str, $left, $right) {
        $left = strlen($left)+strpos($str, $left);
        $right = strpos($str, $right, $left);
        return substr($str, $left, $right - $left);
    }

    function get_score() {
        //第一步取学生信息
        $this->http->setMethod('GET');
        $stuinfo = JnuJwc::get_stuinfo($this->_http_request(JnuJwc::JWC_SCORE_URL));
        //var_dump($stuinfo);
        $this->current_stu_name = $stuinfo['txtXM'];
        $this->current_stu_major = $stuinfo['txtYXZY'];

        //第二步 切换到 对应的学期时间段  （这个不是多余吗！）
        $this->http->setMethod('POST');
        $this->_http_setPostData(array_merge(array(
                '__EVENTTARGET' => 'dlstXndZ',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => '',
                'txtXH' => $this->current_stu,
                'txtXM' => '', //姓名
                'txtYXZY' => '', //学院 专业
                'dlstXndZ' => '2012-2013', //时间段
                'ddListXQ' => JnuJwc::_2gbk('下') //学期
            ), $stuinfo
        ));
        $this->_http_request(JnuJwc::JWC_SCORE_URL);

        //第三步 点查询
        $this->http->setMethod('POST');
        $this->_http_setPostData(array_merge(array(
                '__EVENTTARGET' => 'lbtnQuery',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => '',
                'txtXH' => $this->current_stu,
                'txtXM' => '', //姓名
                'txtYXZY' => '', //学院 专业
                'dlstXndZ' => '2012-2013', //时间段
                'ddListXQ' => JnuJwc::_2gbk('上') //学期
            ), $stuinfo
        ));
        $result = $this->_http_request(JnuJwc::JWC_SCORE_URL);

        $result = JnuJwc::_strMid($result, '<table class="DataGrid"', '</table>');
        $result = JnuJwc::_score_data_format($result);
        return $result;
    }

    /**
     * 登陆
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password) {
        $this->current_stu = $username;
        $verifycode = JnuJwc::get_verifycode($this->_http_request(JnuJwc::JWC_LOGIN_URL));

        $this->http->setMethod('POST');
        $this->_http_setPostData(array(
            'txtYHBS' => $username,
            'txtYHMM' => $password,
            'txtFJM' => $verifycode,
            'btnLogin' => JnuJwc::_2gbk('登 录(Login)')
        ));
        $this->_http_request(JnuJwc::JWC_LOGIN_URL);
        return $this->http->response['info']['http_code'] === 302;
    }

    /**
     * 格式化输出的成绩数据
     * @param $data
     * @return array
     */
    private function _score_data_format($data) {
        $ret = array();
        $data = JnuJwc::_2utf8($data);
        preg_match_all('/<td[^>]*>(.+?)<\/td>/si', $data, $result);

        //7个一行
        for ($i = 0; $i < count($result[1]) / 7; $i++) {
            if ($i == 0) continue;
            //"课程代码" "课程名称" "课程成绩" "绩点" "状态" "课程类别" "学分"
            $ret[] = array(
                'num' => $result[1][$i * 7 + 0],
                'name' => $result[1][$i * 7 + 1],
                'score' => $result[1][$i * 7 + 2],
                'gp' => $result[1][$i * 7 + 3],
                'status' => $result[1][$i * 7 + 4],
                'class' => $result[1][$i * 7 + 5],
                'credit' => $result[1][$i * 7 + 6],
            );
        }
        return $ret;
    }

    /**
     * 请求的封装，包括连续的cookies和evvs
     * @param $url
     * @param bool $evvs
     * @return bool
     */
    private function _http_request($url, $evvs = true) {
        $this->http->setCookies($this->cookies);

        if ($evvs) {
            $this->http->setPostData(array_merge($this->last_post_data, JnuJwc::get_evvs($this->last_html))); //@todo: 还要保存multipart状态吗？
        }
        //////// before hook ////////
        $ret = $this->http->fetch($url);
        //////// after hook ////////

        if ($ret) {
            $this->last_html = $ret;
            $this->_http_save_cookies();
        }

        return $ret;
    }

    /**
     * 设置post数据的封装，方便后续插入evvs
     * @param $post_arr
     * @param bool $multipart
     */
    private function _http_setPostData($post_arr, $multipart = false) {
        $this->last_post_data = $post_arr;
        $this->http->setPostData($post_arr, $multipart);
    }

    /**
     * cookies的保存，方便跟踪回话
     */
    private function _http_save_cookies() {
        $this->cookies = $this->http->getCookies(false);
    }


}





