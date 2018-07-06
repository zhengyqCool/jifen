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
                        <a><cite>基本设置</cite></a>
                    </span>
                </div>
            </div>
            <hr>
            <form class="layui-form" action="" method="post">
                <div class="layui-form-item">
                    <label class="layui-form-label">店铺名称</label>
                    <div class="layui-input-inline">
                        <input name="config[shop_name][value]" value="<?php echo @iHtmlSpecialChars($info['shop_name']);?>" lay-verify="required" class="layui-input" type="text">
                        <input name="config[shop_name][type]" value="0" type="hidden">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">绑定域名</label>
                    <div class="layui-input-inline">
                        <input name="config[site_url][value]" value="<?php echo @iHtmlSpecialChars($info['site_url']);?>" lay-verify="required" class="layui-input" type="text">
                        <input name="config[site_url][type]" value="0" type="hidden">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">客服电话</label>
                    <div class="layui-input-inline">
                        <input name="config[shop_phone][value]" value="<?php echo @iHtmlSpecialChars($info['shop_phone']);?>" lay-verify="required" class="layui-input" type="text">
                        <input name="config[shop_phone][type]" value="0" type="hidden">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">基础运费</label>
                    <div class="layui-input-inline">
                        <input name="config[shippingfee][value]" value="<?php echo @iHtmlSpecialChars($info['shippingfee']);?>" class="layui-input" type="text">
                        <input name="config[shippingfee][type]" value="0" type="hidden">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">免运费最低消费</label>
                    <div class="layui-input-inline">
                        <input name="config[noshippingfee][value]" value="<?php echo @iHtmlSpecialChars($info['noshippingfee']);?>" class="layui-input" type="text">
                        <input name="config[noshippingfee][type]" value="0" type="hidden">
                    </div>
                    <div class="layui-form-mid layui-word-aux">为0则不免运费</div>
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