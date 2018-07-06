<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>修改账号 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <form class="layui-form" action="" method="post">
        <input name="us_id" value="<?php echo $info['us_id'];?>" type="hidden">
        <div class="layui-form-item">
            <label class="layui-form-label">用户手机号</label>
            <div class="layui-input-inline">
                <input name="us_phone" value="<?php echo @iHtmlSpecialChars($info['us_phone']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-inline">
                <input name="us_name" value="<?php echo @iHtmlSpecialChars($info['us_name']);?>" class="layui-input" type="text">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">登录密码</label>
            <div class="layui-input-inline">
                <input name="password" value="" class="layui-input" type="password">
            </div>
            <div class="layui-form-mid layui-word-aux">不修改密码请勿填写</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-inline">
                <input name="reg_password" value="" class="layui-input" type="password">
            </div>
            <div class="layui-form-mid layui-word-aux">不修改密码请勿填写</div>
        </div>
        <div class="layui-form-item layui-inline">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <input type="checkbox" name="us_status" value="1" lay-skin="switch" <?php if ($info['us_status'] == 1) { ?>checked<?php } ?> lay-text="正常|关停">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">&nbsp;</label>
            <button class="layui-btn" lay-submit="" lay-filter="demo1">提交修改</button>
        </div>
    </form>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['element','jquery','layer','form'], function(){
        var element = layui.element,$ = layui.$,layer = layui.layer,form = layui.form;
        form.on('submit(demo1)', function(data){
            var index = layer.load(1);
            $.post('index.php?c=user&a=edit',data.field,function (data) {
                if(data.status == 1){
                    layer.close(index);
                    layer.msg(data.msg,{
                        time: 3000,
                        icon:1
                    }, function(){
                        layer.closeAll("iframe");
                        parent.location.reload();
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