<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-27
 * Time: 下午2:25
 * To change this template use File | Settings | File Templates.
 */
//include(dirname(__FILE__) . '/vc_recognize.php');
include_once(dirname(__FILE__) . '/include/JnuJwc.class.php');

$one_pic = file_get_contents('1.png');
$result = vc_recognize(vc2str($one_pic));
echo '<img src="' . data_uri($one_pic, PIC_MIME) . '">';
echo $result;
exit;

set_time_limit(0);
$count = 0;
$flag = true;
while($flag && $count < 100) {
    $j = new JnuJwc();
    $flag = $j->login('2010051933', 'h121008025');
    $count++;
    unset($j);
}
if (!$flag) {
    echo 'fail';
    $one_pic = file_get_contents('1.png');
    $result = vc_recognize(vc2str($one_pic));
    echo '<img src="' . data_uri($one_pic, PIC_MIME) . '">';
    echo $result;
} else {
    echo '100';
}

//$time = array();
//$pic_count = 0;
//for ($j=0; $j<20; $j++) {
//    for ($i=0; $i<5; $i++) {
//        $result = '';
//        do {$pic_count++;
//            $one_pic = file_get_contents('http://jwc.jnu.edu.cn/web/ValidateCode.aspx');
//            $time[$pic_count] = microtime();
//        } while (!$result = vc_recognize(vc2str($one_pic)));
//        $time[$pic_count] = microtime_float() - $time[$pic_count];
//
//        $result = vc_recognize(vc2str($one_pic));
//        echo '<img src="' . data_uri($one_pic, PIC_MIME) . '">';
//        echo $result;
//        echo '&nbsp;';
//    }
//    echo "<br/>\n";
//}
//
//
//echo '平均识别用时：'. (array_sum(array_filter($time, 'fail_time'))) / $pic_count . 's， 平均识别用图：' . $pic_count / 100;
//
//function fail_time($var) {
//    return $var < 2;
//}