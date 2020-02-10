<?php if (!defined('THINK_PATH')) exit();?><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>仓库车辆排队系统</title>
</head>
<body>
    <h1 class="title">仓库车辆排队系统</h1>

    <div class="navigation">
        <span>前台页面>></span>
        <span><a href="/home/queue?ck=01" target="_blank">【铜管|铜带|铜板】</a></span>
        <span><a href="/home/queue?ck=02" target="_blank">【铜棒】</a></span>
        <span><a href="/home/queue?ck=03" target="_blank">【电材】</a></span>
        <span><a href="/home/queue?ck=04" target="_blank">【棒线】</a></span>
        <span><a href="/home/queue?ck=05" target="_blank">【铜排】</a></span>
        <hr>
        <span>后台页面>></span>
        <span><a href="/home/queue/list?ck=01" target="_blank">【铜管|铜带|铜板】</a></span>
        <span><a href="/home/queue/list?ck=02" target="_blank">【铜棒】</a></span>
        <span><a href="/home/queue/list?ck=03" target="_blank">【电材】</a></span>
        <span><a href="/home/queue/list?ck=04" target="_blank">【棒线】</a></span>
        <span><a href="/home/queue/list?ck=05" target="_blank">【铜排】</a></span>
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
    .navigation {
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -moz-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        -o-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }
    span {
        font-size:xx-large;
    }
    h3 {
        color: black;
    }
</style>