<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-15
 * Time: 下午7:20
 * To change this template use File | Settings | File Templates.
 *
 * @param: $rsp
 */?>
<div class="well well-small">
    <?
    $un_pub = array();
    $total_gp = 0;
    $total_credit = 0;
    foreach ($rsp->stu_score as $si) {
        if($si['score'] == '&nbsp;') {
            $un_pub[] = $si;
        } else {
            $total_credit += $si['credit'];
            $total_gp += $si['gp'];
        }
    }
    if (count($un_pub)) {
        foreach ($un_pub as &$si) {
            $si = $si['name'];
        }
        if (count($un_pub) > 1) {
            echo $rsp->stu_name. '同学，你还有 '. count($un_pub) .' 科成绩未出，分别为 ';
            echo implode(', ', $un_pub);
        } else if (count($un_pub) == 1) {
            echo $rsp->stu_name. '同学，你还有 '. $un_pub[0] .' 一科成绩未出';
        }
        echo '，本学期已获平均绩点：' . round($total_gp / $total_credit, 2);
    } else {
        echo $rsp->stu_name. '同学，你本学期的全部成绩已出';
        echo '，已获平均绩点：' . round($total_gp / $total_credit, 2);
    }


    ?>
</div>
<table class="table table-striped">
    <thead><tr><th>课程名</th><th>成绩</th></tr></thead>
    <tbody>
    <? foreach ($rsp->stu_score as $si): ?>
        <? if($si['score'] == '&nbsp;') continue; ?>
    <tr>
        <td><?=$si['name']?></td>
        <td><?=$si['score'] == '&nbsp;' ? $si['status']:$si['score']?></td>
    </tr>
        <? endforeach;?>
    </tbody>
</table>