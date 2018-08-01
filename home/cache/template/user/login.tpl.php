<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>登录</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/style.css">
</head>
<body>
<div class="login">
    <div class="text-center">
        <p class="login-p1">welcome</p>
        <p class="login-p2">积分平台</p>
        <p class="login-p3">登录</p>
        <p class="login-p4">the login</p>
    </div>
</div>
<div class="login-box">
    <form class="layui-form" action="" method="post">
        <input type="text" name="username" autocomplete="off" placeholder="用户名/手机号" lay-verify="required" class="inp1">
        <input type="password" name="password" autocomplete="off" placeholder="密码" lay-verify="required" class="inp3">
        <button class="inp5" lay-filter="login" lay-submit>立即登录</button>
    </form>
    <p class="login-p5">现在不是会员 ？<a href="index.php?c=user&a=register">去注册</a></p>
</div>
<img src="<?php echo __PUBLIC__;?>images/bg2.png" class="float-img2">
<img src="<?php echo __PUBLIC__;?>images/cy1_03.png" class="float-img" style="bottom: 0px">
<script src="<?php echo __PUBLIC__;?>style.js"></script>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['jquery','layer','form'], function(){
        var $ = layui.$,layer = layui.layer,form = layui.form;
        form.on('submit(login)', function(data){
            console.log(data.field);
            var index = layer.load(3);
            $.post('index.php?c=user&a=login',data.field,function (data) {
                if(data.status == 1){
                    window.location.href = 'index.php';
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