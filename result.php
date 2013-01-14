<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-14
 * Time: 下午9:03
 * To change this template use File | Settings | File Templates.
 */

include_once(dirname(__FILE__) . '/JnuJwc.class.php');

$jwc = new JnuJwc();
if (!isset($_POST['stu_id']) || !isset($_POST['stu_pass']) || empty($_POST['stu_id']) || empty($_POST['stu_pass'])) {
    err('Post error!');
}
if (!$jwc->login($_POST['stu_id'], $_POST['stu_pass'])) {
    err('Login error!');
}

$s = $jwc->get_score();

$rsp = new stdClass();
$rsp->stu_id = $jwc->current_stu;
$rsp->stu_name = JnuJwc::_2utf8($jwc->current_stu_name);
$rsp->stu_major = JnuJwc::_2utf8($jwc->current_stu_major);
$rsp->stu_score = $s;

echo json_encode($rsp);

function err($ErrMsg) {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type:text/plain; charset=utf-8');
    echo $ErrMsg;
    exit;
}