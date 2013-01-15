<?php
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-1-15
 * Time: 下午7:22
 * To change this template use File | Settings | File Templates.
 *
 * @param $main_area
 */?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>暨南大学成绩速查</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css" type="text/css">
    <style>
        body{font-family: "微软雅黑", "Helvetica Neue",Helvetica,Arial,sans-serif;}
        .input-box {margin-bottom: 9px}
        #login_panel {text-align: center;}
        @media (min-width: 768px) { /* desktop */
            .top-margin {margin-top: 5em;}
            input {width: 400px !important}
        }
        @media (max-width: 480px) {
            h1 {font-size: 20px}
            .top-margin {margin-top: 1em;}
        }
    </style>
</head>
<body>
<a href="https://github.com/horsley/jnu-jwc-score" class="hidden-phone">
    <img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub">
</a>
<div class="container top-margin">
    <div style="text-align: center;">
        <h1 style="font-weight: normal">暨南大学成绩速查 <small><!--一键速查，智能安抚--></small></h1>
    </div>
    <div id="login_panel" class="form-inline top-margin">
        <?include(dirname(__FILE__) . '/'. $main_area . '.php');?>
    </div>
</div>
<div class="container" style="text-align: center; color: #666; padding-bottom: 2em">
    Powered by <a href="http://weibo.com/horsley" title="开发者：horsley" target="_blank">Horsley</a> @
    <a href="http://weibo.com/jnuna" title="暨南大学网络协会" target="_blank">JNUNA</a>
</div>
</body>
</html>