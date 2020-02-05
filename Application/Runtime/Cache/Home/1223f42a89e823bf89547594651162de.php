<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>文章详情</title>
    <link href="/Public/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/Public/css/iconfont.css" rel="stylesheet" />
    <link href="/Public/css/JTcultrue.css" rel="stylesheet" />
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.js"></script>
    <![endif]-->


    <style>
       footer p{margin-bottom:10px;text-align:center;}
       footer p span{text-align:center;display:inline-block;margin:0 5px;}
       .carousel-inner>.item>img, .carousel-inner>.item>a>img{width:100%;}
       #searchcontent {border:1px solid #999;border-radius:5px;background-color:#ddd;height:25px;}
       .footer p {text-align:center;}
       .clear { zoom:1; }
       .clear:after { content:''; display:block; clear:both; }
       .fl { float:left; }
       .fr { float:right; }
       .main_menu li a:before {background-color: transparent\9;}
       .left.carousel-control {filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000',endColorstr='#00000000',GradientType=1) }
       .right.carousel-control {filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000',endColorstr='#00000000',GradientType=1) }
       .banner_box {margin-top:-1px\9;}
    </style>
</head>
<body>
    <div style="height:6px;background: linear-gradient(to right, rgba(39, 168, 210, 0) 10%, #ababab 35%, rgba(39, 168, 210, 0) 100%)"></div>
    <script src="/Public/js/jquery-1.10.2.min.js"></script>
    <script src="/Public/js/bootstrap.min.js"></script>
    <script src="/Public/js/jquery.placeholder.min.js"></script>
    <script src="/Public/js/CommGkf.js"></script>
    <div class="body-content">
        


<style>
    .news_href {overflow:hidden\9;}
    .news_href .fls {float:left\9;}
    .news_href .frs {float:right\9;}
</style>

<div class="container no_padding">
    <div class="detial_box">

        <div style="height:2px;background: linear-gradient(to right, rgba(39, 168, 210, 0) 10%, #ababab 35%, rgba(39, 168, 210, 0) 100%);margin:18px 0;"></div>

        <div class="news_content">

        </div>
    </div>
</div>

<script>
    window.location.getParameter = function (param) { var query = window.location.search; var iLen = param.length; var iStart = query.indexOf(param); if (iStart == -1) return ""; iStart += iLen + 1; var iEnd = query.indexOf("&", iStart); if (iEnd == -1) return query.substring(iStart); return query.substring(iStart, iEnd); }

    var ArticlesID = window.location.getParameter("id");
    $(function () {
        LoadInfo();
    })
    function LoadInfo() {
        $.ajax({
            url: "/Home/Index/ShowDetail?id=" + ArticlesID,
            type: "Get",
            dataType: "json",
            success: function (data) {
                var info = data.Data;
                if (data.Result == "1") {
                    var html = "";
                    html +=
                    '<h2>' + info.ArticlesInfo.title + '</h2>'
                        + '<div class="news_tip">'
                        + '<span>作者：' + info.ArticlesInfo.creator + '</span>'
                        + '<span>点击：' + info.ArticlesInfo.click + '次</span>'
                        + '<span>更新时间：' + dateFormat(info.ArticlesInfo.created_at) + '</span>'
                        + '</div>'
                        + '<div class="news_text">'
                    + info.ArticlesInfo.text
                        + '</div>';
                    $(".news_content").html(html);
                }
            }
    });

    }

    function dateFormat(date) {
        return  date.split(' ')[0];
    }
</script>
    </div>

</body>
</html>