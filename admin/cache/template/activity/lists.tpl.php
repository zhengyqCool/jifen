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
    <form class="layui-form" action="" method="get">
        <input name="c" value="activity" type="hidden">
        <input name="a" value="lists" type="hidden">
        <blockquote class="layui-elem-quote">
            <div class="layui-inline layui-float-right">
                <a class="layui-btn layui-btn-sm layui-btn-normal addcase_btn" href="javascript:void(0);">添加活动</a>
            </div>
            <div class="layui-inline">
                <span class="layui-breadcrumb">
                    <a href="javascript:void(0);">活动列表</a>
                    <a><cite>活动列表</cite></a>
                </span>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">活动名称</label>
                <div class="layui-input-inline">
                    <input class="layui-input searchVal" name="name" value="<?php echo @iHtmlSpecialChars($_GET['name']);?>" placeholder="请输入活动名称" type="text">
                </div>
                <button class="layui-btn search_btn" type="submit">搜索</button>
            </div>
        </blockquote>
    </form>
    <table class="layui-table" lay-filter="admin-user" lay-even>
        <colgroup>
            <col width="200">
            <col width="">
            <col width="200">
            <col width="100">
            <col width="100">
            <col width="100">
            <col width="100">
            <col width="100">
            <col width="200">
            <col width="100">
            <col width="200">
        </colgroup>
        <thead>
            <tr>
                <th lay-data="{field:'name', width:200}">活动名称</th>
                <th lay-data="{field:'description'}">活动说明</th>
                <th lay-data="{field:'cs_id', width:200}">开奖方案</th>
                <th lay-data="{field:'point', width:100}">报名积分</th>
                <th lay-data="{field:'awards1', width:100}">一等奖</th>
                <th lay-data="{field:'awards2', width:100}">二等奖</th>
                <th lay-data="{field:'awards3', width:100}">三等奖</th>
                <th lay-data="{field:'awards4', width:100}">其他用户</th>
                <th lay-data="{field:'addtime', width:200}">创建时间</th>
                <th lay-data="{field:'status', width:100}">状态</th>
                <th lay-data="{field:'_event',fixed:'right', width:200, align:'center'}">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $vo) {?>
            <tr>
                <td><?php echo $vo['ac_name'];?></td>
                <td><?php echo $vo['ac_description'];?></td>
                <td>
                    <?php if(is_array($case)){foreach ((array)$case as $val) {?>
                    <?php if ($val['cs_id'] == $vo['cs_id']) { ?>
                    <?php echo $val['cs_name'];?>
                    <?php } ?>
                    <?php }} ?>
                </td>
                <td><?php echo $vo['ac_point'];?></td>
                <td><?php echo $vo['ac_awards1'];?>%</td>
                <td><?php echo $vo['ac_awards2'];?>%</td>
                <td><?php echo $vo['ac_awards3'];?>%</td>
                <td><?php echo $vo['ac_awards4'];?>%</td>
                <td><?php echo $vo['ac_addtime'];?></td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_change" href="javascript:void(0);" data-ac_id="<?php echo $vo['ac_id'];?>">
                        <?php if ($vo['ac_status'] == 1) { ?>进行中<?php } else { ?>已暂停<?php } ?>
                    </a>
                </td>
                <td>
                    <a class="layui-btn layui-btn-xs admin_edit" href="javascript:void(0);" data-ac_id="<?php echo $vo['ac_id'];?>">详情</a>
                    <a class="layui-btn layui-btn-xs layui-btn-danger admin_delete" href="javascript:void(0);" data-ac_id="<?php echo $vo['ac_id'];?>">活动列表</a>
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
                title : "添加活动",
                type : 2,
                content : "index.php?c=activity&a=add",
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
            var ac_id = $(this).data('ac_id');
            var index = layui.layer.open({
                title : "活动列表",
                type : 2,
                content : "index.php?c=activity&a=log&id="+ac_id,
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
        $(".admin_edit").click(function(){
            var ac_id = $(this).data('ac_id');
            var index = layui.layer.open({
                title : "修改活动",
                type : 2,
                content : "index.php?c=activity&a=edit&id="+ac_id,
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
        $(".admin_change").click(function(){
            var ac_id = $(this).data('ac_id');
            var that = this;
            $.get('index.php?c=activity&a=change&id='+ac_id,function(data){
                if(data.status == 1){
                    $(that).html('进行中');
                }else{
                    $(that).html('已暂停');
                }
            });
        });
    });
</script>
</body>
</html>