<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-19
 * Time: 下午4:11
 * To change this template use File | Settings | File Templates.
 */


define('PIC_MIME', 'image/png');
function vc2str ($pic_content) {
    $res = imagecreatefromstring($pic_content); //png
    $res = imagerotate($res, 270, 0); //左旋270
    $size = array(imagesx($res), imagesy($res));
    $data = array();
    for($i=0; $i < $size[1]; ++$i) {
        for($j=0; $j < $size[0]; ++$j) {
            $rgb = imagecolorat($res,$j,$i);
            $rgbarray = imagecolorsforindex($res, $rgb);
            if($rgbarray['red'] != 0) {
                //$data[$i][$j]=1;
                @$data[$i] .= '1';
                echo '<span style="background-color: #002a80">1</span>';
            } else {
                //$data[$i][$j]=0;
                @$data[$i] .= '0';
                echo 0;
            }
        }
        echo "<br>\n";
    }
//    exit;
    imagedestroy($res);
    return $data;
}
/**
 * data uri生成
 * @param $contents
 * @param $mime
 * @return string
 */
function data_uri($contents, $mime)
{
    $base64   = base64_encode($contents);
    return ('data:' . $mime . ';base64,' . $base64);
}


$one_pic = file_get_contents('1.png');
//$one_pic = 'iVBORw0KGgoAAAANSUhEUgAAAC4AAAAUCAYAAADyWA/8AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAP9JREFUSEtjZNj//z/DkAQghw9FPCQdDU4lQzG0Rx0+ELGGK6nEVC3+f0tGFYwP69rA2djUw9Qhy2ETg8njkyM66eJSCDOcZ9tncKlDqkNIVU+0g2GxS8jhxITSoAlxkEOA5Q0Yg9joIQ5LRt3hJThjY9CFuPHMM2DPzPFKhldcMI/CHIvMJzZP0DSpwGIAlFkJZcRBFeKw5AOiHfr3o4Q4LEmRmydoGuIgx8HSN3KoE8qc6PJ0KQ6xhSAoU4JCvSqllajMuc42EKweFEsgDGIj5xGSQxtflU8olGCVkuSaZ1jLeHT9IMfDxCh29GhbZTC1VchKd/T0wKB3II7AAAB10gqk3BzEsQAAAABJRU5ErkJggg==';
//$one_pic = base64_decode($one_pic);
echo '<img src="' . data_uri($one_pic, PIC_MIME) . '"><br/>';

vc2str($one_pic);