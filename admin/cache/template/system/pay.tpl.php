<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>系统设置 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/global.css" media="all">
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <?php include $this->template("public/header"); ?>
    <div class="layui-body">
        <div class="hnek-panel">
            <div class="layui-row">
                <div class="hnek-breadcrumb layui-col-xs9">
                    <span class="layui-breadcrumb">
                        <a href="javascript:void(0);">系统设置</a>
                        <a><cite>支付配置</cite></a>
                    </span>
                </div>
            </div>
            <hr>
            <form class="layui-form" action="" method="post">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:185px;">小程序ID（APPID）</label>
                    <div class="layui-input-inline">
                        <div class="layui-input-inline">
                            <input name="config[app_id][value]" value="<?php echo @iHtmlSpecialChars($info['app_id']);?>" class="layui-input" type="text">
                            <input name="config[app_id][type]" value="1" type="hidden">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:185px;">小程序密钥（APPSECRET）</label>
                    <div class="layui-input-inline">
                        <input name="config[app_secret][value]" value="<?php echo @iHtmlSpecialChars($info['app_secret']);?>" class="layui-input" type="text">
                        <input name="config[app_secret][type]" value="1" type="hidden">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:185px;">商户号（MCHID）</label>
                    <div class="layui-input-inline">
                        <input name="config[mch_id][value]" value="<?php echo @iHtmlSpecialChars($info['mch_id']);?>" class="layui-input" type="text">
                        <input name="config[mch_id][type]" value="1" type="hidden">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:185px;">支付秘钥（KEY）</label>
                    <div class="layui-input-inline">
                        <input name="config[pay_key][value]" value="<?php echo @iHtmlSpecialChars($info['pay_key']);?>" class="layui-input" type="text">
                        <input name="config[pay_key][type]" value="1" type="hidden">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">&nbsp;</label>
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">提交修改</button>
                </div>
            </form>
        </div>
    </div>
    <?php include $this->template("public/footer"); ?>
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
                    layer.msg(data.msg);
                }
            });
            return false;
        });
    });
</script>
</body>
</html>