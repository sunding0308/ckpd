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
        <span><a href="/home/queue" target="_blank">前台</a></span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        <span><a href="/home/queue/list" target="_blank">后台</a></span>
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