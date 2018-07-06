<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理后台</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/index.css" media="all">
</head>
<body class="main_body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-main mag0">
            <a href="#" class="logo">管理后台</a>
            <a href="javascript:;" class="seraph hideMenu icon-caidan"></a>
            <ul class="layui-nav top_menu">
                <li class="layui-nav-item" id="userInfo">
                    <a href="javascript:;"><img src="<?php echo __PUBLIC__;?>images/logo.jpg" class="layui-nav-img userAvatar" width="35" height="35"><cite class="adminName"><?php echo $_SESSION['am_nickname'];?></cite></a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-url="index.php?c=system&a=info"><i class="seraph icon-ziliao" data-icon="icon-ziliao"></i><cite>个人资料</cite></a></dd>
                        <dd><a href="javascript:;" data-url="index.php?c=system&a=resetpd"><i class="seraph icon-xiugai" data-icon="icon-xiugai"></i><cite>修改密码</cite></a></dd>
                        <dd><a href="index.php?a=logout" class="signOut"><i class="seraph icon-tuichu"></i><cite>退出</cite></a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-side layui-bg-black">
        <div class="user-photo">
            <a class="img" title="我的头像" ><img src="<?php echo __PUBLIC__;?>images/logo.jpg" class="userAvatar"></a>
            <p>你好！<span class="userName"><?php echo $_SESSION['am_nickname'];?></span>, 欢迎登录</p>
        </div>
        <div class="navBar layui-side-scroll" id="navBar">
            <ul class="layui-nav layui-nav-tree">
                <li class="layui-nav-item layui-this">
                    <a data-url="index.php?a=main" href="javascript:;"><cite>后台首页</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a data-url="index.php?c=activity&a=lists" href="javascript:;"><cite>活动列表</cite></a>
                </li>
                <li class="layui-nav-item ">
                    <a data-url="index.php?c=case&a=lists" href="javascript:;"><cite>活动方案</cite></a>
                </li>
                <li class="layui-nav-item ">
                    <a data-url="index.php?c=user&a=lists" href="javascript:;"><cite>用户列表</cite></a>
                </li>
                <li class="layui-nav-item ">
                    <a href="javascript:;"><cite>系统设置</cite></a>
                    <dl class="layui-nav-child">
                        <dd class="<?php if (CONTROLLER_NAME == 'System' && ACTION_NAME == 'sms') { ?>layui-this<?php } ?>">
                            <a data-url="index.php?c=system&a=sms" href="javascript:;"><cite>短信接口</cite></a>
                        </dd>
                        <dd class="<?php if (CONTROLLER_NAME == 'System' && ACTION_NAME != 'sms') { ?>layui-this<?php } ?>">
                            <a data-url="index.php?c=system&a=lists" href="javascript:;"><cite>账号管理</cite></a>
                        </dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-body layui-form">
        <div class="layui-tab mag0" lay-filter="bodyTab" id="top_tabs_box">
            <ul class="layui-tab-title top_tab" id="top_tabs">
                <li class="layui-this" lay-id=""><i class="layui-icon">&#xe68e;</i> <cite>后台首页</cite></li>
            </ul>
            <ul class="layui-nav closeBox">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon caozuo">&#xe643;</i> 页面操作</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" class="refresh refreshThis"><i class="layui-icon">&#x1002;</i> 刷新当前</a></dd>
                        <dd><a href="javascript:;" class="closePageOther"><i class="seraph icon-prohibit"></i> 关闭其他</a></dd>
                        <dd><a href="javascript:;" class="closePageAll"><i class="seraph icon-guanbi"></i> 关闭全部</a></dd>
                    </dl>
                </li>
            </ul>
            <div class="layui-tab-content clildFrame">
                <div class="layui-tab-item layui-show">
                    <iframe src="index.php?a=main"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-footer footer">
        <p><span>copyright @2018</span></p>
    </div>
</div>
<div class="site-tree-mobile"><i class="layui-icon">&#xe602;</i></div>
<div class="site-mobile-shade"></div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.config({
        base: '<?php echo __PUBLIC__;?>'
    }).extend({
        "bodyTab" : "bodyTab"
    });
</script>
<script src="<?php echo __PUBLIC__;?>index.js"></script>
</body>
</html>