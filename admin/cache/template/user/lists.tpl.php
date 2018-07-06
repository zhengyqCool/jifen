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
    <form class="layui-form" action="" method="get">
        <input name="c" value="user" type="hidden">
        <input name="a" value="lists" type="hidden">
        <blockquote class="layui-elem-quote">
            <div class="layui-inline layui-float-right">
                <a class="layui-btn layui-btn-normal adduser_btn" href="javascript:void(0);">添加用户</a>
            </div>
            <div class="layui-inline">
                <span class="layui-breadcrumb">
                    <a href="javascript:void(0);">用户管理</a>
                    <a><cite>用户列表</cite></a>
                </span>
                <div class="layui-inline">
                    <label class="layui-form-label">用户手机号</label>
                    <div class="layui-input-inline">
                        <input class="layui-input searchVal" name="phone" value="<?php echo @iHtmlSpecialChars($_GET['phone']);?>" placeholder="请输入用户手机号" type="text">
                    </div>
                    <button class="layui-btn search_btn" type="submit">搜索</button>
                </div>
            </div>
        </blockquote>
    </form>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="200">
            <col width="">
            <col width="100">
            <col width="100">
            <col width="200">
            <col width="150">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'phone', width:200}">手机号</th>
                <th lay-data="{field:'name'}">备注</th>
                <th lay-data="{field:'point', width:100}">积分</th>
                <th lay-data="{field:'status', width:100}">状态</th>
                <th lay-data="{field:'addtime', width:200}">注册时间</th>
                <th lay-data="{field:'_event',fixed:'right', width:150, align:'center'}">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td><?php echo $vo['us_phone'];?></td>
                <td><?php echo $vo['us_name'];?></td>
                <td><?php echo $vo['us_point'];?></td>
                <td><?php if ($vo['us_status'] == 1) { ?>正常<?php } else { ?>关停<?php } ?></td>
                <td><?php echo $vo['us_addtime'];?></td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_edit" href="javascript:void(0);" data-us_id="<?php echo $vo['us_id'];?>">详情</a>
                    <a class="layui-btn layui-btn-xs layui-btn-danger point_lists" href="javascript:void(0);" data-us_id="<?php echo $vo['us_id'];?>">积分详情</a>
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

        $(".adduser_btn").click(function(){
            var index = layui.layer.open({
                title : "添加用户",
                type : 2,
                content : "index.php?c=user&a=add",
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

        $(".admin_edit").click(function(){
            var us_id = $(this).data('us_id');
            var index = layui.layer.open({
                title : "用户详情",
                type : 2,
                content : "index.php?c=user&a=edit&id="+us_id,
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

        $(".point_lists").click(function(){
            var us_id = $(this).data('us_id');
            var index = layui.layer.open({
                title : "积分变更列表",
                type : 2,
                content : "index.php?c=user&a=point_lists&id="+us_id
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