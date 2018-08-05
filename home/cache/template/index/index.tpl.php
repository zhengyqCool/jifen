<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>首页</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/style.css?t=008">
</head>
<body>
    <div class="model-bg"></div>
    <div class="menu">
        <div class="menu-head">
            <p>账户：</p>
            <span><?php echo $user['us_phone'];?></span>
        </div>
        <ul class="menu-list">
            <li><a href="index.php?c=user">购买记录</a></li>
            <li><a href="javascript:;">充值记录</a></li>
            <li><a href="javascript:;">提现记录</a></li>
        </ul>
        <div class="sign-out" id="quit">
            <button>退出账号</button>
        </div>
    </div>
    <header>
        <h1>首页</h1>
    </header>
    <div class="banner">
        <div class="banner__user" id="user">
            <img src="../../../static/images/index_user-ico.png" alt="user">
        </div>
        <!-- <div class="banner__top">
            <a href="javascript:;">排行榜</a>
        </div> -->
    </div>
    <div class="news-list">
        <!-- <ul>
            <li>
                <i class="news-list__ico"></i><span>玩家 ***836，开出400积分，恭喜、恭喜！</span>
            </li>
            <li>
                <i class="news-list__ico"></i><span>玩家 ***836，开出400积分，恭喜、恭喜！</span>
            </li>
            <li>
                <i class="news-list__ico"></i><span>玩家 ***836，开出400积分，恭喜、恭喜！</span>
            </li>
        </ul> -->
        <div class="lf lb-width" style="border-right: 1px solid #dfdfdd"><p class="lb-p1" id="count"></p><p class="lb-p2">个人积分</p></div>
        <div class="rt lb-width"><p class="lb-p1"><?php echo $count;?></p><p class="lb-p2">正在进行的活动</p></div>
    </div>
    <div class="tips">
        <h2>游戏规则</h2>
        <p>开奖后会产生，一等奖、二等奖、... <a href="javascript:;">游戏规则？</a></p>
    </div>
    <?php if(is_array($list)){foreach ((array)$list as $val) {?>
    <div class="number-periods">
        <p class=""record>第<?php echo $val['ad_name'];?>期</p>
    </div>
    <div class="timer">
        <p><span><?php echo @date('H:i:s',$val['ad_lotterytime']);?></span> 开奖</p>
    </div>
    <div class="red-envelopes">
        <a href="index.php?c=user">
            <img src="../../../static/images/index__red.png" alt="">
            <span class="red-envelopes__status">查看开奖</span>
        </a>
    </div>
    <a class="start-button" href="index.php?a=lists&id=<?php echo $val['ac_id'];?>">
        <span>参与积分</span>
        <span><?php echo $val['ac_point'];?></span>
        &nbsp;立即参与
    </a>
    <?php }} ?>
    <div id="nullDom" class="null-dom hide">
        <p>已截止下注</p>
        <p>稍后请刷新从试</p>
        <div class="null-img">
            <img src="../../../static/images/null.png" alt="">
        </div>
    </div>
<!-- <div class="tab-header">
    <a href="index.php" class="header-a1">活动列表</a>
    <a href="index.php?c=user" class="header-a2">我参与的活动</a>
</div>
<div class="lb-box text-center">
    <div class="lf lb-width"><p class="lb-p1" id="count">0</p><p class="lb-p2">个人积分</p></div>
    <div class="rt lb-width" ><p class="lb-p1"><?php echo $count;?></p><p class="lb-p2">正在进行的活动</p></div>
    <div class="clearfloat"></div>
</div>
<?php if(is_array($list)){foreach ((array)$list as $val) {?>
<div class="lb-box2">
    <div class="lf lb-p3">
        <span><?php echo $val['ac_name'];?> <br /> 第<?php echo $val['ad_name'];?>期</span>
    </div>
    <div class="rt lb-p4">
        <span>开奖时间<br><?php echo @date('H:i:s',$val['ad_lotterytime']);?></span>
    </div>
    <div class="clearfloat"></div>
    <div class="lb-box3">
        <div class="lf lb-p5">
            <a href="">活动介绍：<?php echo $val['ac_description'];?></a>
        </div>
        <div class="lf lb-box4">
            <p class="lb-p6">参与积分</p>
            <p class="lb-p7"><?php echo $val['ac_point'];?></p>
            <a href="index.php?a=lists&id=<?php echo $val['ac_id'];?>" class="lb-a1">立即报名参与</a>
        </div>
        <div class="clearfloat"></div>
    </div>
</div>
<?php }} ?>
<div class="page hide" id="page"></div>
<img src="<?php echo __PUBLIC__;?>images/cy1_03.png" class="float-img" style="bottom: 0px"> -->
<script src="<?php echo __PUBLIC__;?>style.js"></script>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.config({
        base: '<?php echo __PUBLIC__;?>'
    }).extend({
        numberRock: 'numberRock'
    });
    layui.use(['jquery','layer','laypage','numberRock'], function(){
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

        var $ = layui.$,layer = layui.layer,numberRock=layui.numberRock;

        $(function() {
            if ($('#Off').length > 0) {
                $('#nullDom').hide()
            } else {
                $('#nullDom').show()
            }
        })

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
        $('#user').click(function() {
            $('.model-bg').fadeIn('100')
            $('.menu').addClass('menu-show')
        })
        $('.model-bg').click(function() {
            $(this).fadeOut('100')
            $('.menu').removeClass('menu-show')
        })
    });
</script>
</body>
</html>
