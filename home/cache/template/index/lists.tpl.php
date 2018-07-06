<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>活动详情</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/style.css">
</head>
<body>
<div class="tab-header">
    <a href="index.php" class="header-a1">活动列表</a>
    <a href="index.php?c=user" class="header-a2">我参与的活动</a>
</div>
<div class="lb-box5">
    <div class="lf" style="width: 70%">
        <div class="lb-p8"><span><?php echo $activity['ac_name'];?> - 第<?php echo $info['ad_name'];?>期</span></div>
        <p class="lb-p4">开奖时间<br /><?php echo @date('H:i:s',$info['ad_lotterytime']);?></p>
    </div>
    <div class="rt lb-box4" style="width: 30%">
        <p class="lb-p6">参与积分</p>
        <p class="lb-p7"><?php echo $activity['ac_point'];?></p>
    </div>
    <div class="clearfloat"></div>
</div>
<div class="lb-box6">
    <h3>活动介绍</h3>
    <p>
        开始下注时间：<?php echo @date('H:i:s',$info['ad_betstarttime']);?>
    </p>
    <p>
        下注结束时间：<?php echo @date('H:i:s',$info['ad_betendtime']);?>
    </p>
    <p>
        开奖时间：<?php echo @date('H:i:s',$info['ad_lotterytime']);?>
    </p>
    <p>
        <?php echo $activity['ac_description'];?>
    </p>
</div>
<?php if ($info['ad_status'] == 1 && time() > $info['ad_betstarttime'] && time() < $info['ad_betendtime']) { ?>
<div class="lb-button" id="open">确定参与</div>
<?php } elseif ($info['ad_status'] != 3 && time() > $info['ad_betendtime']) { ?>
<div class="lb-button">等待开奖</div>
<?php } elseif ($info['ad_status'] == 3) { ?>
<div class="lb-button"><a href="index.php?a=betlog&id=<?php echo $info['ad_id'];?>">查看中奖名单</a></div>
<?php } else { ?>
<div class="lb-button">等待开始投注</div>
<?php } ?>
<div class="finel"></div>
<div class="open-window text-center">
    <div class="open-box1">
        <img src="<?php echo __PUBLIC__;?>images/tc1.gif" class="rt close" width="20">
        <img src="<?php echo __PUBLIC__;?>images/img1.jpg" width="50%">
        <p>请等待开奖</p>
    </div>
    <p class="lb-p6">参与成功，已经扣除<?php echo $activity['ac_point'];?>积分，请等待开奖时间。</p>
    <br>
    <a href="index.php" class="lb-a1">返回活动列表</a>
</div>
<img src="<?php echo __PUBLIC__;?>images/cy1_03.png" class="float-img">
<div class="foot" id="quit">退出当前账号</div>
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
        $("#open").click(function(){
            var index = layer.load(3);
            $.post('index.php?a=bet',{id:<?php echo $info['ad_id'];?>},function (data) {
                if(data.status == 1){
                    layer.close(index);
                    $(".finel,.open-window").fadeIn("500");
                }else{
                    layer.close(index);
                    layer.msg(data.msg,{icon:5,shift:6});
                }
            })
            return false;

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
