<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>我参与的活动</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/style.css">
</head>
<body>
<header class="active">
    <a class="header__back" href="index.php"><span>&lsaquo;</span> 返回</a>
    <h1>购买记录</h1>
</header>
<?php if(is_array($list)){ foreach ((array)$list as $val) {?>
<div class="hd-box4">
    <p class="hd-p3">
        第<?php echo $val['ad_name'];?>期</span>
        <span class="hd-sp2 rt"><?php if ($val['ad_status'] == 3) { ?>
            已开奖
            <?php } else { ?>
            等待开奖
            <?php } ?>
        </span>
        <div class="clearfloat"></div>
    </p>
    <?php if ($val['ad_status'] == 3) { ?>
    <p class="hd-p5"><span class="hd-sp4">中奖</span>恭喜您：您中了<?php if ($val['log_awards'] == 0) { ?>参与<?php } else { ?><?php echo $val['log_awards'];?>等<?php } ?>奖：<?php echo $val['log_awards_point'];?>积分</p>
    <a href="index.php?a=betlog&id=<?php echo $val['ad_id'];?>" class="hd-a1">查看中奖名单</a>
    <?php } else { ?>
    <p class="hd-p4">本次活动扣除<?php echo $val['ac_point'];?>积分</p>
    <?php } ?>
</div>
<?php }} ?>
<div class="page" id="page"></div>
<img src="<?php echo __PUBLIC__;?>images/cy1_03.png" class="float-img">
<script src="<?php echo __PUBLIC__;?>style.js"></script>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.config({
        base: '<?php echo __PUBLIC__;?>'
    }).extend({
        numberRock: 'numberRock'
    });
    layui.use(['jquery','layer','laypage','numberRock'], function(){
        var $ = layui.$,layer = layui.layer,numberRock=layui.numberRock;
        var laypage = layui.laypage;
        laypage.render({
            elem: 'page',
            count: <?php echo $count;?>,
            skip: true,
            layout: ['prev','next'],
            curr: function(){
                var page = <?php echo $page;?>;
                return page ? page : 1;
            }(),
            jump: function(e, first){
                if(!first){
                    if(!first){
                        var href = window.location.href.replace(/&page=\d*/,'');
                        window.location.href = href + '&page='+e.curr;
                    }
                }
            }
        });

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