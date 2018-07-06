<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>修改密码 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote title">
        <span class="layui-breadcrumb">
            <a href="javascript:void(0);">系统设置</a>
            <a><cite>个人资料</cite></a>
        </span>
    </blockquote>
    <div class="layui-elem-quote layui-quote-nm magb0">
        <form class="layui-form" action="" method="post">
            <input name="am_id" value="<?php echo $info['am_id'];?>" type="hidden">
            <div class="layui-form-item">
                <label class="layui-form-label">登录账号</label>
                <div class="layui-input-inline">
                    <input value="<?php echo @iHtmlSpecialChars($info['am_user']);?>" lay-verify="required" class="layui-input layui-disabled" type="text">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">昵称</label>
                <div class="layui-input-inline">
                    <input name="am_nickname" value="<?php echo @iHtmlSpecialChars($info['am_nickname']);?>" lay-verify="required" class="layui-input" type="text">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">&nbsp;</label>
                <button class="layui-btn" lay-submit="" lay-filter="demo1">提交修改</button>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['element','jquery','layer','form'], function(){
        var element = layui.element,$ = layui.$,layer = layui.layer,form = layui.form;
        form.on('submit(demo1)', function(data){
            var index = layer.load(1);
            $.post('index.php?c=system&a=info',data.field,function (data) {
                if(data.status == 1){
                    layer.close(index);
                    layer.msg(data.msg, {
                        time: 3000
                    }, function(){
                        window.location.href = 'index.php?c=index&a=info';
                    });
                }else{
                    layer.close(index);
                    layer.msg(data.msg,{icon:5,shift:6});
                }
            });
            return false;
        });
    });
</script>
</body>
</html>