layui.define("jquery",function(exports){
    var $ = layui.$;
    exports('numberRock', function(ele,options){
        var defaults={
            lastNumber:100,
            duration:2000,
            easing:'swing'  //swing(默认 : 缓冲 : 慢快慢)  linear(匀速的)
        };
        var opts=$.extend({}, defaults, options);

        $(ele).animate({
            num : "numberRock",
        },{
            duration : opts.duration,
            easing : opts.easing,
            complete : function(){

            },
            step : function(a,b){  //可以检测我们定时器的每一次变化
                $(this).html(parseInt(b.pos * opts.lastNumber));
            }
        });
        return $;
    });
});