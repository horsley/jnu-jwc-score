<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-14
 * Time: 下午9:03
 * To change this template use File | Settings | File Templates.
 */

include_once(dirname(__FILE__) . '/include/JnuJwc.class.php');
include_once(dirname(__FILE__) . '/include/functions.php');

$jwc = new JnuJwc();
if (!isset($_POST['stu_id']) || !isset($_POST['stu_pass']) || empty($_POST['stu_id']) || empty($_POST['stu_pass'])) {
    redirect(get_baseurl());
}
if (!$jwc->login($_POST['stu_id'], $_POST['stu_pass'])) {
    redirect(get_baseurl(), 3, '密码错误！3秒后跳回登陆页重试');
}

$s = $jwc->get_score();

$rsp = new stdClass();
$rsp->stu_id = $jwc->current_stu;
$rsp->stu_name = JnuJwc::_2utf8($jwc->current_stu_name);
$rsp->stu_major = JnuJwc::_2utf8($jwc->current_stu_major);
$rsp->stu_score = $s;

//echo json_encode($rsp);



echo render_tmpl(array('main_area'=>'result_area', 'rsp' => $rsp), dirname(__FILE__) . '/tmpl/page_frame.php');
?>
