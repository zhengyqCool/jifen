<?php if (!defined('IN_FW')) exit('Access Denied');?>
<div class="layui-header header ">
    <div class="layui-main">
        <a class="layui-logo" href="index.php">管理后台</a>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <?php echo $_SESSION['am_nickname'];?>
                </a>
                <dl class="layui-nav-child">
                    <dd>
                        <a href="index.php?c=system&a=edit">账号资料</a>
                    </dd>
                    <dd>
                        <a href="index.php?c=system&a=resetpd">修改密码</a>
                    </dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="index.php?a=logout">安全退出</a></li>
        </ul>
    </div>
</div>
<div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
        <ul class="layui-nav layui-nav-tree"  lay-filter="test">
            <li class="layui-nav-item <?php if (CONTROLLER_NAME == 'Activity') { ?>layui-this<?php } ?>">
                <a href="index.php?c=activity&a=lists">活动列表</a>
            </li>
            <li class="layui-nav-item <?php if (CONTROLLER_NAME == 'Case') { ?>layui-this<?php } ?>">
                <a href="index.php?c=case&a=lists">活动方案</a>
            </li>
            <li class="layui-nav-item <?php if (CONTROLLER_NAME == 'User') { ?>layui-this<?php } ?>">
                <a href="index.php?c=user&a=lists">用户列表</a>
            </li>
            <li class="layui-nav-item <?php if (CONTROLLER_NAME == 'System') { ?>layui-nav-itemed<?php } ?>">
                <a href="javascript:;">系统设置</a>
                <dl class="layui-nav-child">
                    <dd class="<?php if (CONTROLLER_NAME == 'System' && ACTION_NAME == 'sms') { ?>layui-this<?php } ?>">
                        <a href="index.php?c=system&a=sms">短信接口</a>
                    </dd>
                    <dd class="<?php if (CONTROLLER_NAME == 'System' && ACTION_NAME != 'sms') { ?>layui-this<?php } ?>">
                        <a href="index.php?c=system&a=lists">账号管理</a>
                    </dd>
                </dl>
            </li>
        </ul>
    </div>
</div>