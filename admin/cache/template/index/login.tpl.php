<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html class="loginHtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all">
</head>
<body class="loginHtml">
<div class="loginBody">
<form class="layui-form">
    <div class="login_face"><img src="<?php echo __PUBLIC__;?>images/logo.jpg" class="userAvatar"></div>
    <div class="layui-form-item input-item">
        <label for="userName">用户名</label>
        <input type="text" name="username" placeholder="请输入用户名" autocomplete="off" id="userName" class="layui-input" lay-verify="required">
    </div>
    <div class="layui-form-item input-item">
        <label for="password">密码</label>
        <input type="password" name="password" placeholder="请输入密码" autocomplete="off" id="password" class="layui-input" lay-verify="required">
    </div>
    <div class="layui-form-item input-item" id="imgCode">
        <label for="code">验证码</label>
        <input type="text" name="checkcode" placeholder="请输入验证码" autocomplete="off" id="code" class="layui-input">
        <a href="javascript:document.getElementById('code_img').src='./index.php?c=common&a=checkcode&time='+Math.random();void(0);">
            <img id="code_img" src="./index.php?c=common&a=checkcode" style="width:90px;" />
        </a>
    </div>
    <div class="layui-form-item">
        <button class="layui-btn layui-block" lay-filter="login" lay-submit>登录</button>
    </div>
</form>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['jquery','layer','form'], function(){
        var $ = layui.$,layer = layui.layer,form = layui.form;
        form.on('submit(login)', function(data){
            var index = layer.load(1);
            $.post('index.php?a=login',data.field,function (data) {
                if(data.status == 1){
                    window.location.href = data.url;
                }else{
                    layer.close(index);
                    layer.msg(data.msg);
                }
            })
            return false;
        });
        $(".loginBody .input-item").click(function(e){
            e.stopPropagation();
            $(this).addClass("layui-input-focus").find(".layui-input").focus();
        })
        $(".loginBody .layui-form-item .layui-input").focus(function(){
            $(this).parent().addClass("layui-input-focus");
        })
        $(".loginBody .layui-form-item .layui-input").blur(function(){
            $(this).parent().removeClass("layui-input-focus");
            if($(this).val() != ''){
                $(this).parent().addClass("layui-input-active");
            }else{
                $(this).parent().removeClass("layui-input-active");
            }
        })
    });
</script>
</body>
</html>