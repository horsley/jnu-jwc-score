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
    foreach ($rsp->stu_score as $si) {
        if($si['score'] == '&nbsp;') {
            $un_pub[] = $si;
        }
    }
    if (count($un_pub)) echo $rsp->stu_name. '同学，你还有 '. count($un_pub) .' 科成绩未出，分别为 ';
    foreach ($un_pub as &$si) {
        $si = $si['name'];
    }
    echo implode(', ', $un_pub);
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