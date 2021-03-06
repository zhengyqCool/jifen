<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>修改活动方案 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <form class="layui-form" action="" method="post">
        <input name="cs_id" value="<?php echo $info['cs_id'];?>" type="hidden">
        <div class="layui-form-item">
            <label class="layui-form-label">方案名称</label>
            <div class="layui-input-inline">
                <input name="cs_name" value="<?php echo @iHtmlSpecialChars($info['cs_name']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">下注时长</label>
            <div class="layui-input-inline">
                <input name="cs_bettime" value="<?php echo @iHtmlSpecialChars($info['cs_bettime']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">（分钟）必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">下注后等待开奖时长</label>
            <div class="layui-input-inline">
                <input name="cs_lotterytime" value="<?php echo @iHtmlSpecialChars($info['cs_lotterytime']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">（分钟）必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开奖后等待下注时长</label>
            <div class="layui-input-inline">
                <input name="cs_waittime" value="<?php echo @iHtmlSpecialChars($info['cs_waittime']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">（分钟）必填</div>
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
            $.post('index.php?c=case&a=edit',data.field,function (data) {
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