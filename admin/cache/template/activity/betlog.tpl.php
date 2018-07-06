<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>活动报名列表 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <form class="layui-form" action="" method="get">
        <input name="c" value="activity" type="hidden">
        <input name="a" value="betlog" type="hidden">
        <input name="id" value="<?php echo $info['ad_id'];?>" type="hidden">
        <blockquote class="layui-elem-quote title">
            <div class="layui-inline">
                <span class="layui-breadcrumb" lay-separator="-">
                    <a><cite>报名列表</cite></a>
                    <a><cite>第<?php echo $info['ad_name'];?>期</cite></a>
                    <a><cite><?php echo $activity['ac_name'];?></cite></a>
                </span>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">用户手机号</label>
                <div class="layui-input-inline">
                    <input class="layui-input searchVal" name="phone" value="<?php echo @iHtmlSpecialChars($_GET['phone']);?>" placeholder="请输入用户手机号" type="text">
                </div>
                <button class="layui-btn search_btn" type="submit">搜索</button>
            </div>
        </blockquote>
    </form>
    <form class="layui-form" action="" method="post">
        <input name="id" value="<?php echo $info['ad_id'];?>" type="hidden">
        <table class="layui-table layui-form" lay-even>
            <colgroup>
                <col width="150">
                <col width="200">
                <col width="100">
                <col width="100">
            </colgroup>
            <thead>
                <tr>
                    <th lay-data="{field:'phone', width:150}">用户</th>
                    <th lay-data="{field:'add_time', width:200}">报名时间</th>
                    <th lay-data="{field:'awards_point_log', width:100}">奖项</th>
                    <th lay-data="{field:'awards_point', width:100}">奖励积分</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $vo) {?>
                <tr>
                    <td><?php echo $vo['us_phone'];?></td>
                    <td><?php echo @date('Y-m-d H:i:s',$vo['log_addtime']);?></td>
                    <td>
                        <?php if ($info['ad_status'] == 3) { ?>
                        <?php if ($vo['log_awards'] == 0) { ?>参与奖<?php } ?>
                        <?php if ($vo['log_awards'] == 1) { ?>一等奖<?php } ?>
                        <?php if ($vo['log_awards'] == 2) { ?>二等奖<?php } ?>
                        <?php if ($vo['log_awards'] == 3) { ?>三等奖<?php } ?>
                        <?php } else { ?>
                        <select name="log_awards[<?php echo $vo['log_id'];?>]">
                            <option value="0" <?php if ($vo['log_awards'] == 0) { ?>selected<?php } ?>>默认</option>
                            <option value="1" <?php if ($vo['log_awards'] == 1) { ?>selected<?php } ?>>一等奖</option>
                            <option value="2" <?php if ($vo['log_awards'] == 2) { ?>selected<?php } ?>>二等奖</option>
                            <option value="3" <?php if ($vo['log_awards'] == 3) { ?>selected<?php } ?>>三等奖</option>
                        </select>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $vo['log_awards_point'];?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if ($info['ad_status'] != 3) { ?>
        <div class="layui-inline">
            <label class="layui-form-label">&nbsp;</label>
            <button class="layui-btn" type="submit" lay-submit="" lay-filter="betlogsave">保存</button>
        </div>
        <?php } ?>
    </form>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['element','jquery','layer','form'], function(){
        var element = layui.element;
        var $ = layui.jquery;
        var form = layui.form;
        form.on('submit(betlogsave)', function(data){
            var index = layer.load(1);
            $.post('index.php?c=activity&a=betlogsave',data.field,function (data) {
                if(data.status == 1){
                    layer.close(index);
                    layer.msg(data.msg,{
                        time: 3000,
                        icon:1
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