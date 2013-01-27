<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-14
 * Time: 下午2:20
 * To change this template use File | Settings | File Templates.
 *
 * 参考 @link https://apidoc.sinaapp.com/sae/SaeFetchurl.html
 *      @link http://josephscott.org/archives/2010/03/php-helpers-curl_http_request/
 */
class HttpReq
{
    public $response = array();
    private $cookies = array();
    private $headers = array();
    private $curl_opt = array();

    function __construct() {
        $this->curl_opt = array(
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => 0,
        );
        $this->setConnectionTimeout(5); //默认连接超时 5s
        $this->setTotalTimeout(15); //执行超时15s
        $this->setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.106 Safari/535.2'); //默认ua

        //$this->setProxy('127.0.0.1:8888'); //fiddler debug
    }

    /**
     * 设置代理，如127.0.0.1:8888
     * @param $proxy
     */
    public function setProxy($proxy) {
        $this->curl_opt[CURLOPT_PROXY] = $proxy;
    }

    /**
     * 设置请求方法，如get post
     * @param string $method
     */
    public function setMethod($method = 'GET') {
        $this->curl_opt[CURLOPT_CUSTOMREQUEST] = $method;
        if ($method == 'POST') {
            $this->curl_opt[CURLOPT_POST] = true;
        } else if ( $method == 'HEAD' ) {
            $curl_opt[CURLOPT_NOBODY] = true;
        }
    }

    /**
     * 设置连接超时
     * @param $second
     */
    public function setConnectionTimeout($second) {
        $this->curl_opt[CURLOPT_CONNECTTIMEOUT] = $second;
    }

    /**
     * 设置执行超时
     * @param $second
     */
    public function setTotalTimeout($second) {
        $this->curl_opt[CURLOPT_TIMEOUT] = $second;
    }

    /**
     * 设置ua
     * @param $ua
     */
    public function setUserAgent($ua) {
        $this->curl_opt[CURLOPT_USERAGENT] = $ua;
    }

    /**
     * 批量设置cookie
     * @param $cookie_arr
     */
    public function setCookies($cookie_arr) {
        if ($cookie_arr) {
            foreach($cookie_arr as $k => $v) {
                $this->setCookie($k, $v);
            }
        }
    }

    /**
     * 设置一条cookie
     * @param $cookie_name
     * @param $cookie_value
     */
    public function setCookie($cookie_name, $cookie_value) {
        $this->cookies[$cookie_name] = $cookie_value;
    }

    /**
     * 设置一条header
     * @param $header_name
     * @param $header_value
     */
    public function setHeader($header_name, $header_value) {
        $this->headers[$header_name] = $header_value;
    }

    /**
     * 设置post提交值，会覆盖前面的设置
     * @param $post_arr
     * @param $multipart 是否为二进制数据
     */
    public function setPostData($post_arr, $multipart = false) {
        if (!$multipart) {
            foreach ($post_arr as $k => &$p) {
                $p = urlencode($p);
                $p = "$k=$p";
            }
            $this->curl_opt[CURLOPT_POSTFIELDS] = implode('&', $post_arr);
        } else {
            $this->curl_opt[CURLOPT_POSTFIELDS] = $post_arr;
        }
    }

    /**
     * 取已设置的post参数
     * @return array|string
     */
    public function getPostData() {

        $post_arr = $this->curl_opt[CURLOPT_POSTFIELDS];
        if (is_array($post_arr)) {
            foreach($post_arr as &$p) {
                $p = urldecode($p);
            }
            return $post_arr;
        } else if (is_string($post_arr)) {
            $post_arr = explode('&', $post_arr);
            $count = count($post_arr);
            for ($i = 0; $i < $count; $i++) {
                list($k, $v) = explode('=', $post_arr[$i], 2);
                unset($post_arr[$i]);
                $post_arr[$k] = $v;
            }
            return $post_arr;
        }
    }

    private function _prepare_custom_fields() {
        if (count($this->cookies) > 0) {    //cookies init
            $formatted = array();
            foreach($this->cookies as $k => $v) {
                $formatted[] = "$k=$v";
            }
            $this->curl_opt[CURLOPT_COOKIE] = implode( ';', $formatted );
        }

        if (count($this->headers) > 0) {    //headers init
            $formatted = array();
            foreach($this->headers as $k => $v) {
                $formatted[] = "$k: $v";
            }
            $this->curl_opt[CURLOPT_HTTPHEADER] = $formatted;
        }
    }
    /**
     * 抓取
     * @param $url
     * @return bool
     */
    public function fetch( $url ) {

        $this->_prepare_custom_fields();

        $curl = curl_init( $url );
        curl_setopt_array( $curl, $this->curl_opt );

        $this->response['body'] = curl_exec( $curl );
        $this->response['err_no'] = curl_errno( $curl );
        $this->response['err_msg'] = curl_error( $curl );
        $this->response['info'] = curl_getinfo( $curl );

        curl_close( $curl );

        //cut body and header
        $this->response['headers'] = trim( substr( $this->response['body'], 0, $this->response['info']['header_size'] ) );
        $this->response['body'] = substr( $this->response['body'], $this->response['info']['header_size'] );



//        //手动的跟踪302跳转
//        //参考http://php.net/manual/en/function.curl-setopt.php#102121
//        if ($info['http_code'] == 301 || $info['http_code'] == 302) {
//            $new_url = $headers['location'];
//            return $this->fetch($new_url);
//        }

        if ($this->response['err_no'] == 0) {
            return $this->response['body'];
        } else {
            return false;
        }
    }

    /**
     * 取得返回的http头
     * @param $parse
     * @return mixed|string
     */
    public function getHeaders($parse = true) {
        $headers = array_pop( explode( "\r\n", $this->response['headers'], 2 ) );

        if (!$parse) {
            return $headers;
        }

        $headers = explode("\r\n", $headers);
        $headers_new = array();
        foreach ( $headers as $line ) {
            @list( $k, $v ) = explode( ':', $line, 2 );
            if ( empty( $v ) ) {
                continue;
            }

            if ( strtolower( $k ) == 'set-cookie' ) {
                if (array_key_exists($k, $headers_new)) {
                    array_push($headers_new[$k], trim( $v ));
                } else {
                    $headers_new[$k] = array(trim( $v ));
                }
            } else {
                $headers_new[$k] = trim( $v );
            }
        }
        return $headers_new;
    }

    public function getCookies($all = true)
    {
        $header = $this->response['headers'];
        $matchs = array();
        $cookies = array();
        $kvs = array();
        if (preg_match_all('/Set-Cookie:\s([^\r\n]+)/i', $header, $matchs)) {
            foreach ($matchs[1] as $match) {
                $cookie = array();
                $items = explode(";", $match);
                foreach ($items as $_) {
                    $item = explode("=", trim($_));
                    if (count($item) == 2) {
                        $cookie[$item[0]]= $item[1];
                    }
                }
                array_push($cookies, $cookie);
                $kvs = array_merge($kvs, $cookie);
            }
        }
        if ($all) {
            return $cookies;
        } else {
            unset($kvs['path']);
            unset($kvs['max-age']);
            return $kvs;
        }
    }
}
