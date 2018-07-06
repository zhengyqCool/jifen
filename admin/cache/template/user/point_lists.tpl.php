<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>用户管理 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote">
        <div class="layui-inline layui-float-right">
            当前积分 <?php echo $info['us_point'];?>
            <a class="layui-btn layui-btn-sm layui-btn-normal adduser_btn" data-us_id="<?php echo $info['us_id'];?>" href="javascript:void(0);">调整积分</a>
        </div>
        <div class="layui-inline">
            <?php echo $info['us_phone'];?><?php if ($info['us_name']) { ?><?php echo $info['us_name'];?><?php } ?> 的积分调整历史
        </div>
    </blockquote>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="200">
            <col width="200">
            <col width="100">
            <col width="">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'phone', width:200}">项目</th>
                <th lay-data="{field:'name',width:200}">日期</th>
                <th lay-data="{field:'point', width:100}">积分</th>
                <th lay-data="{field:'status'}">说明</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td>
                    <?php if ($vo['log_action'] == 1) { ?>
                    注册
                    <?php } elseif ($vo['log_action'] == 2) { ?>
                    开奖
                    <?php } elseif ($vo['log_action'] == 3) { ?>
                    抽奖
                    <?php } elseif ($vo['log_action'] == 4) { ?>
                    后台
                    <?php } ?>
                </td>
                <td><?php echo $vo['log_time'];?></td>
                <td><?php echo $vo['log_point'];?></td>
                <td><?php echo $vo['log_description'];?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div id="page"></div>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['element','laypage','jquery','layer','table'], function(){
        var element = layui.element;
        var laypage = layui.laypage;
        var table = layui.table;
        var $ = layui.$;

        $(".adduser_btn").click(function(){
            var us_id = $(this).data('us_id');
            layer.prompt({
                formType: 0,
                value: '0',
                title: '积分调整(金额为正则增加，负为减少。)',
            }, function(value, index){
                layer.close(index);
                if(value != 0){
                    $.post('index.php?c=user&a=add_point',{value:value,us_id:us_id},function(data){
                        if(data.status == 1){
                            layer.msg(data.msg,{
                                time: 3000,
                                icon:1
                            }, function(){
                                parent.location.reload();
                            });
                        }else{
                            layer.msg(data.msg,{icon:5,shift:6});
                        }
                    });
                }else{
                    layer.msg('积分不能为空',{icon:5,shift:6});
                }
            });
        });

        laypage.render({
            elem: 'page',
            count: <?php echo $count;?>,
            skip: true,
            layout: ['prev', 'page','next','count'],
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
        table.init('admin-user', {
            limit: 10
        });

    });
</script>
</body>
</html>