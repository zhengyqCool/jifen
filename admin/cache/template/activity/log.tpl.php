<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>活动列表 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote">
        <div class="layui-inline">
            <span class="layui-breadcrumb">
                <a href="javascript:void(0);">活动列表</a>
                <a><cite>活动列表</cite></a>
            </span>
        </div>
    </blockquote>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="200">
            <col width="">
            <col width="200">
            <col width="200">
            <col width="200">
            <col width="100">
            <col width="200">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'name', width:200}">活动名称</th>
                <th lay-data="{field:'description'}">期数</th>
                <th lay-data="{field:'cs_id', width:200}">开始下注时间</th>
                <th lay-data="{field:'awards1', width:200}">下注结束时间</th>
                <th lay-data="{field:'awards2', width:200}">开奖时间</th>
                <th lay-data="{field:'status', width:100}">状态</th>
                <th lay-data="{field:'_event',fixed:'right', width:200, align:'center'}">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td><?php echo $info['ac_name'];?></td>
                <td><?php echo $vo['ad_name'];?></td>
                <td><?php echo @date('Y-m-d H:i:s',$vo['ad_betstarttime']);?></td>
                <td><?php echo @date('Y-m-d H:i:s',$vo['ad_betendtime']);?></td>
                <td><?php echo @date('Y-m-d H:i:s',$vo['ad_lotterytime']);?></td>
                <td>
                    <?php if ($vo['ad_status'] == 1 && time() > $vo['ad_betstarttime'] && time() < $vo['ad_betendtime']) { ?>
                    投注中
                    <?php } elseif ($vo['ad_status'] != 3 && time() > $vo['ad_betendtime']) { ?>
                    等待开奖
                    <?php } elseif ($vo['ad_status'] == 3) { ?>
                    已开奖
                    <?php } else { ?>
                    等待投注
                    <?php } ?>
                </td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_edit" href="javascript:void(0);" data-ad_status="<?php echo $vo['ad_status'];?>" data-ad_id="<?php echo $vo['ad_id'];?>">查看报名列表</a>
                </td>
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
        $(".admin_edit").click(function(){
            var ad_id = $(this).data('ad_id');
            var index = parent.layui.layer.open({
                title : "查看报名列表",
                type : 2,
                content : "index.php?c=activity&a=betlog&id="+ad_id
            });
            parent.layui.layer.full(index);
            $(window).on("resize",function(){
                parent.layui.layer.full(index);
            });
        });
    });
</script>
</body>
</html>