<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-14
 * Time: 下午2:15
 * To change this template use File | Settings | File Templates.
 */
include_once(dirname(__FILE__) . '/JnuJwc.class.php');

$a = new JnuJwc();
$a->login('2010051933', '121008025');
$a->get_score();