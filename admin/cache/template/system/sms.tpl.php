<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>系统设置 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote title">
        <span class="layui-breadcrumb">
            <a href="javascript:void(0);">系统设置</a>
            <a><cite>短信接口配置</cite></a>
        </span>
    </blockquote>
    <div class="layui-elem-quote layui-quote-nm magb0">
        <form class="layui-form" action="" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label" >应用KEY</label>
                <div class="layui-input-inline">
                    <input name="config[dayu_appkey][value]" value="<?php echo @iHtmlSpecialChars($info['dayu_appkey']);?>" class="layui-input" type="text">
                    <input name="config[dayu_appkey][type]" value="1" type="hidden">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">应用密钥</label>
                <div class="layui-input-inline">
                    <input name="config[dayu_secretKey][value]" value="<?php echo @iHtmlSpecialChars($info['dayu_secretKey']);?>" class="layui-input" type="text">
                    <input name="config[dayu_secretKey][type]" value="1" type="hidden">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" >短信签名</label>
                <div class="layui-input-inline">
                    <input name="config[dayu_sign][value]" value="<?php echo @iHtmlSpecialChars($info['dayu_sign']);?>" class="layui-input" type="text">
                    <input name="config[dayu_sign][type]" value="1" type="hidden">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" >模板CODE</label>
                <div class="layui-input-inline">
                    <input name="config[dayu_template][value]" value="<?php echo @iHtmlSpecialChars($info['dayu_template']);?>" class="layui-input" type="text">
                    <input name="config[dayu_template][type]" value="1" type="hidden">
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
            $.post('index.php?c=system&a=save_config',data.field,function (data) {
                if(data.status == 1){
                    layer.close(index);
                    layer.msg(data.msg);
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