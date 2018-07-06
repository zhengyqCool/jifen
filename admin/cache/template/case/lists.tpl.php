<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>活动方案 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote">
        <div class="layui-inline layui-float-right">
            <a class="layui-btn layui-btn-sm layui-btn-normal addcase_btn" href="javascript:void(0);">添加方案</a>
        </div>
        <div class="layui-inline">
            <span class="layui-breadcrumb">
                <a href="javascript:void(0);">活动方案</a>
                <a><cite>方案列表</cite></a>
            </span>
        </div>
    </blockquote>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="">
            <col width="200">
            <col width="250">
            <col width="250">
            <col width="200">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'name'}">方案名称</th>
                <th lay-data="{field:'bettime', width:200}">下注时长（分钟）</th>
                <th lay-data="{field:'lotterytime', width:250}">下注后等待开奖时长（分钟）</th>
                <th lay-data="{field:'waittime', width:250}">开奖后等待下注时长（分钟）</th>
                <th lay-data="{field:'_event',fixed:'right', width:200, align:'center'}">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td><?php echo $vo['cs_name'];?></td>
                <td><?php echo $vo['cs_bettime'];?></td>
                <td><?php echo $vo['cs_lotterytime'];?></td>
                <td><?php echo $vo['cs_waittime'];?></td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_edit" href="javascript:void(0);" data-cs_id="<?php echo $vo['cs_id'];?>">详情</a>
                    <a class="layui-btn layui-btn-xs layui-btn-danger admin_delete" href="javascript:void(0);" data-cs_id="<?php echo $vo['cs_id'];?>">删除</a>
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

        $(".addcase_btn").click(function(){
            var index = layui.layer.open({
                title : "添加活动方案",
                type : 2,
                content : "index.php?c=case&a=add",
                success : function(){
                    setTimeout(function(){
                        layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },500)
                }
            });
            layui.layer.full(index);
            $(window).on("resize",function(){
                layui.layer.full(index);
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
        $('.admin_delete').on('click',function(){
            var _this = $(this);
            var cs_id = _this.data('cs_id');
            layer.open({
                content:'确定删除该活动方案么？删除后不可恢复',
                btn: ['确定', '取消'],
                yes: function(){
                    var index = layer.load(1);
                    $.get('index.php?c=case&a=delete&id=' + cs_id,function (data) {
                        if(data.status == 1){
                            layer.close(index);
                            layer.msg(data.msg, {
                                time: 3000,
                                icon:1
                            }, function(){
                                _this.parents('tr').remove();
                            });
                        }else{
                            layer.close(index);
                            layer.msg(data.msg,{icon:5,shift:6});
                        }
                    });
                    return true;
                },
                btn2: function(){
                    return true;
                }
            });
        });
        $(".admin_edit").click(function(){
            var cs_id = $(this).data('cs_id');
            var index = layui.layer.open({
                title : "修改活动方案",
                type : 2,
                content : "index.php?c=case&a=edit&id="+cs_id,
                success : function(){
                    setTimeout(function(){
                        layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },500)
                }
            });
            layui.layer.full(index);
            $(window).on("resize",function(){
                layui.layer.full(index);
            });
        });
    });
</script>
</body>
</html>