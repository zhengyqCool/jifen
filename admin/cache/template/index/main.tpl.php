<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-row layui-col-space10">
    <div class="layui-col-lg6 layui-col-md12">
        <blockquote class="layui-elem-quote title">近30天数据统计</blockquote>
        <div class="layui-elem-quote layui-quote-nm magb0">
            <div class="" id="index_echar" style="height:383px"></div>
        </div>
    </div>
    <div class="layui-col-lg6 layui-col-md12">
        <blockquote class="layui-elem-quote title">系统基本参数</blockquote>
        <table class="layui-table magt0">
            <colgroup>
                <col width="150">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <td>当前登录账号</td>
                    <td class="version"><?php echo $_SESSION['am_user'];?></td>
                </tr>
                <tr>
                    <td>上次登录时间</td>
                    <td class="author"><?php echo $_SESSION['am_lastlogintime'];?></td>
                </tr>
                <tr>
                    <td>上次登录IP</td>
                    <td class="homePage"><?php echo $_SESSION['am_lastloginip'];?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script src="<?php echo __PUBLIC__;?>echarts.min.js"></script>
<script>
    layui.use(['element','laypage','jquery'], function(){
        var element = layui.element;
        var $ = layui.$;
        var myChart = echarts.init(document.getElementById('index_echar'));
        window.onresize = myChart.resize;
        var colors = ['#5793f3', '#d14a61', '#675bba'];
        $.get('index.php?c=index&a=index_data',function (data) {
            myChart.setOption({
                title : {
                    show: false
                },
                color: colors,
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                grid: {
                    right: '20%'
                },
                legend: {
                    data:['新增用户数'],
                    left: 'center'
                },
                xAxis: [
                    {
                        type: 'category',
                        axisLabel:{rotate:-45,interval:1},
                        data: data['xaxis']
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '新增用户数',
                        position: 'left',
                        minInterval: 1,
                        axisLine: {
                            lineStyle: {
                                color: colors[0]
                            }
                        },
                        axisLabel: {
                            formatter: '{'+'value}'
                        }
                    }
                ],
                series: [
                    {
                        name:'新增用户数',
                        type:'bar',
                        data:data['yaxis']
                    }
                ]
            });
        });
    });
</script>
</body>
</html>