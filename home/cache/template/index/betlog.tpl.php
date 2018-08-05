<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>活动列表</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/style.css?t=001">
</head>
<body>
<header class="active">
    <a class="header__back" href="index.php?c=user"><span>&lsaquo;</span> 返回</a>
    <h1>中奖名单</h1>
</header>
<div class="text-center">
    <p class="hd-p1">第<?php echo $info['ad_name'];?>期</p>
    <?php if ($user_info) { ?>
    <p class="hd-p2">
        恭喜您：您中了<?php if ($user_info['log_awards'] == 0) { ?>参与<?php } else { ?><?php echo $user_info['log_awards'];?>等<?php } ?>奖：<?php echo $user_info['log_awards_point'];?>积分
    </p>
    <?php } ?>
    <div class="hd-box1">中奖列表</div>
    <table class="hd-table" cellspacing="0" >
        <thead>
            <tr class="hd-box2">
                <td width="20%">名次</td>
                <td width="52%">账号</td>
                <td width="28%">奖励积分</td>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($list)){foreach ((array)$list as $key=>$val) {?>
            <?php $k = $key + 1?>
            <tr>
                <td>
                    <span <?php if ($key < 3) { ?>class="hd-sp1"<?php } ?>><?php echo $k;?></span>
                </td>
                <td><?php echo @substr($val['us_phone'],0,3);?>XXXX<?php echo @substr($val['us_phone'],7);?></td>
                <td><?php echo $val['log_awards_point'];?></td>
            </tr>
            <?php }}?>
        </tbody>
    </table>
</div>
<img src="<?php echo __PUBLIC__;?>images/cy1_03.png" class="float-img">
<script src="<?php echo __PUBLIC__;?>style.js"></script>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.config({
        base: '<?php echo __PUBLIC__;?>'
    }).extend({
        numberRock: 'numberRock'
    });
    layui.use(['jquery','layer','numberRock'], function(){
        var $ = layui.$,layer = layui.layer,numberRock=layui.numberRock;
        $("#quit").click(function(){
            layer.open({
                type: 0,
                title: '提示',
                content: '确定退出当前登录吗?',
                icon:5,
                btn:['确定','取消'],
                yes: function(index, layero){
                    window.location.href='index.php?c=user&a=logout';
                }
            });
        });
        numberRock('#count',{
            lastNumber:<?php echo $user['us_point'];?>,
            duration:1000,
            easing:'swing',  //慢快慢
        });
    });
</script>
</body>
</html>
