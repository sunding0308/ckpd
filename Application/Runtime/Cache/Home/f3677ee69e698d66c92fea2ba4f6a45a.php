<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>仓库车辆排队系统-<?php echo ($cks[$ck]); ?></title>
</head>
<body>
    <h1 class="title">仓库车辆排队系统-<?php echo ($cks[$ck]); ?></h1>
    <input type="hidden" id="ck" value="<?php echo ($ck); ?>">
    <div class="marquee">
        <marquee direction="up" behavior="scroll" scrollamount="3" scrolldelay="0" width="70%" height="600px" loop="-1" bgcolor="#F5F5F5">
            <ol id="car_list"></ol>
        </marquee>
        <div class="qr-code">
            <div>
                <img src="/Public/img/金田logo.png">
            </div>
            <div>
                <img src="/home/queue/qrcode?ck=<?php echo ($ck); ?>" width="300" height="300"/>
            </div>
            <div class="description"><span>扫描二维码，进入排队系统</span></div>
        </div>
    </div>
</body>
</html>

<style>
    .title {
        text-align: center;
        color: rgb(10, 170, 170);
        font-size:20px;
        font-size: xx-large;
    }
    .qr-code {
        text-align: center;
        float: right;
        width: 30%;
    }
    .description {
        text-align: center;
    }
</style>

<script src="/Public/js/jquery-1.10.2.min.js"></script>
<script>
    $(function () {
        LoadInfo();
    })

    var ck = $('#ck').val();

    function LoadInfo() {
        $.ajax({
            url: "/Home/Queue/GetQueueList?ck="+ck,
            type: "Get",
            dataType: "json",
            success: function (data) {
                if (data.Result == "1") {
                    var info = data.Data;
                    var html = "";
                    for (var i = 0; i < info.queues.length; i++) {
                        html += "<hr>";
                        var nowHtml = info.queues[i];
                        html +='<li>'
                                    + '<span style="float: left; margin-left:5%; width:10%">' + nowHtml.car_no + '</span>'
                                    + '<span style="float: left; margin-left:5%; width:10%">' + nowHtml.goods_type + '</span>';
                        if (nowHtml.states == 0) {
                            var States = '排队中';
                            var color = 'tomato';
                            html += '<span style="float: left; margin-left:10%;">已等待<B style="color:rgb(187, 16, 178)">' + WaitTime(nowHtml.queued_at) + '</B>分钟</span>';
                            if (nowHtml.priority == 1) {
                                html += '<span style="float: left;color:red;">(加急)</span>';
                            }
                        } else {
                            var States = '装车中';
                            var color = 'blue';
                        }
                        html += '<span style="float: right; margin-right: 40px">'
                                + '<span style="color: ' + color + ';">' + States + '</span>'
                                + '</span>'
                                + '</li>';
                    }
                    $("#car_list").html(html);
                }
            }
        });
    }

    function WaitTime(date1){
        var date1= date1;  //开始时间
        var date2 = new Date();    //结束时间
        var date3 = date2.getTime() - NewDate(date1).getTime();   //时间差的毫秒数 

        return Math.floor(date3/1000/60);
    }

    
    function NewDate(str) { //解决new Date()IE10不支持参数
            //首先将日期分隔 ，获取到日期部分 和 时间部分
            var day = str.split(' ');
            //获取日期部分的年月日
            var days = day[0].split('-');
            //获取时间部分的 时分秒
            var mi = day[day.length - 1].split(':');
            //获取当前date类型日期
            var date = new Date();
            //给date赋值  年月日
            date.setUTCFullYear(days[0], days[1] - 1, days[2]);
            //给date赋值 时分秒  首先转换utc时区 ：+8
            date.setUTCHours(mi[0] - 8, mi[1], mi[2]);
            return date;
    }

    setInterval(LoadInfo,30000);
    setInterval("window.location.reload()",600000);
</script>