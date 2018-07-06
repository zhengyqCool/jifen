<?php if (!defined('IN_FW')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>修改活动 - 管理后台</title>
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/layui.css" media="all">
    <link rel="stylesheet" href="<?php echo __PUBLIC__;?>css/public.css" media="all" />
</head>
<body class="childrenBody">
<div class="layui-col-md12">
    <form class="layui-form" action="" method="post">
        <input name="ac_id" value="<?php echo $info['ac_id'];?>" type="hidden">
        <div class="layui-form-item">
            <label class="layui-form-label">活动名称</label>
            <div class="layui-input-inline">
                <input name="ac_name" value="<?php echo @iHtmlSpecialChars($info['ac_name']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">活动说明</label>
            <div class="layui-input-inline">
                <textarea name="ac_description" class="layui-textarea" ><?php echo @iHtmlSpecialChars($info['ac_description']);?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开奖方案</label>
            <div class="layui-input-inline">
                <select name="cs_id" lay-verify="required">
                    <option value=""></option>
                    <?php if(is_array($case)){foreach ((array)$case as $val) {?>
                    <option value="<?php echo $val['cs_id'];?>" <?php if ($info['cs_id'] == $val['cs_id']) { ?>selected<?php } ?>><?php echo $val['cs_name'];?></option>
                    <?php }} ?>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">报名积分</label>
            <div class="layui-input-inline">
                <input name="ac_point" value="<?php echo @iHtmlSpecialChars($info['ac_point']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">一等奖</label>
            <div class="layui-input-inline">
                <input name="ac_awards1" value="<?php echo @iHtmlSpecialChars($info['ac_awards1']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">% 必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">二等奖</label>
            <div class="layui-input-inline">
                <input name="ac_awards2" value="<?php echo @iHtmlSpecialChars($info['ac_awards2']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">% 必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">三等奖</label>
            <div class="layui-input-inline">
                <input name="ac_awards3" value="<?php echo @iHtmlSpecialChars($info['ac_awards3']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">% 必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">其他</label>
            <div class="layui-input-inline">
                <input name="ac_awards4" value="<?php echo @iHtmlSpecialChars($info['ac_awards4']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">% 必填</div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">开始时间</label>
            <div class="layui-input-inline">
                <input name="ac_starttime" id="ac_starttime" value="<?php echo @date('Y-m-d H:i:s',$info['ac_starttime']);?>" lay-verify="required" class="layui-input" type="text">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item layui-inline">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <input type="radio" name="ac_status" value="1" <?php if ($info['ac_status'] == 1) { ?>checked<?php } ?> title="进行中">
                <input type="radio" name="ac_status" value="0" <?php if ($info['ac_status'] == 0) { ?>checked<?php } ?> title="暂停">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">&nbsp;</label>
            <button class="layui-btn" lay-submit="" lay-filter="demo1">提交修改</button>
        </div>
    </form>
</div>
<script src="<?php echo __PUBLIC__;?>layui.js"></script>
<script>
    layui.use(['element','jquery','layer','form','laydate'], function(){
        var element = layui.element,$ = layui.$,layer = layui.layer,form = layui.form;
        var laydate = layui.laydate;
        laydate.render({
            elem: '#ac_starttime',
            type: 'datetime'
        });
        form.on('submit(demo1)', function(data){
            var index = layer.load(1);
            $.post('index.php?c=activity&a=edit',data.field,function (data) {
                if(data.status == 1){
                    layer.close(index);
                    layer.msg(data.msg,{
                        time: 3000,
                        icon:1
                    }, function(){
                        layer.closeAll("iframe");
                        parent.location.reload();
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