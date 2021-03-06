<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>账号管理 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <blockquote class="layui-elem-quote">
        <div class="layui-inline layui-float-right">
            <a class="layui-btn layui-btn-sm layui-btn-normal addNews_btn" href="javascript:void(0);">添加管理账号</a>
        </div>
        <div class="layui-inline">
            <span class="layui-breadcrumb">
                <a href="javascript:void(0);">账号管理</a>
                <a><cite>账号列表</cite></a>
            </span>
        </div>
    </blockquote>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="200">
            <col width="">
            <col width="100">
            <col width="200">
            <col width="150">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'username', width:200}">用户名</th>
                <th lay-data="{field:'nickname'}">昵称</th>
                <th lay-data="{field:'status', width:100}">状态</th>
                <th lay-data="{field:'addtime', width:200}">添加时间</th>
                <th lay-data="{field:'_event',fixed:'right', width:150, align:'center'}">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td><?php echo $vo['am_user'];?></td>
                <td><?php echo $vo['am_nickname'];?></td>
                <td><?php if ($vo['am_status'] == 1) { ?>正常<?php } else { ?>关停<?php } ?></td>
                <td><?php echo $vo['am_addtime'];?></td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_edit" href="javascript:void(0);" data-am_id="<?php echo $vo['am_id'];?>">编辑</a>
                    <a class="layui-btn layui-btn-xs layui-btn-danger admin_delete" href="javascript:void(0);" data-am_id="<?php echo $vo['am_id'];?>">删除</a>
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
        var layer = layui.layer;
        var $ = layui.$;

        $(".addNews_btn").click(function(){
            var index = layui.layer.open({
                title : "添加账号",
                type : 2,
                content : "index.php?c=system&a=add",
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
            })
        })

        laypage.render({
            elem: 'page', // 分页容器
            count: <?php echo $count;?>,   // 总页数
            skip: true, //是否开启跳页
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
            //height: 315, //设置高度
            limit: 10 //注意：请务必确保 limit 参数（默认：10）是与你服务端限定的数据条数一致
            //支持所有基础参数
        });
        $('.admin_delete').on('click',function(){
            var _this = $(this);
            var am_id = _this.data('am_id');
            layer.open({
                content:'确定删除该管理账号么？删除后不可恢复',
                btn: ['确定', '取消'],
                yes: function(){
                    var index = layer.load(1);
                    $.get('index.php?c=system&a=delete&id=' + am_id,function (data) {
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
            var am_id = $(this).data('am_id');
            var index = layui.layer.open({
                title : "编辑账号",
                type : 2,
                content : "index.php?c=system&a=edit&id="+am_id,
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