<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-15
 * Time: 下午7:15
 * To change this template use File | Settings | File Templates.
 */

session_start();
include_once(dirname(__FILE__) . '/include/JnuJwc.class.php');
include(dirname(__FILE__) . '/include/functions.php');
echo render_tmpl(array('main_area'=>'login_form'), dirname(__FILE__) . '/tmpl/page_frame.php');
