<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <style type="text/css">
            a {
                /*width: 220px;
                overflow: hidden;
                text-overflow: ellipsis;
                word-break: keep-all;*/
                white-space: nowrap; /*防止文字中有空格而换行显示*/
            }
        </style>
        <title>信息部技术专栏</title>
        <link href="/Public/css/oa.css" type="text/css" rel="STYLESHEET">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="keywords" content="keyword1,keyword2,keyword3">
        <meta http-equiv="description" content="This is my page">
    </head>
    <body style="overflow: scroll;">

        <table width="98%" class="Econtent" cellpadding="0" cellspacing="1">
            <tbody id="guanlijujiao">
            </tbody>
        </table>
        <script src="/Public/js/jquery-1.10.2.min.js"></script>
        <!-- <script src="Scripts/jquery-1.10.2.min.js"></script> -->
        <script>
            $(function () {
    
                LoadInfo();
    
            })
    
            function LoadInfo() {
                $.ajax({
                    url: "/Home/Index/GetArticlesList",
                    type: "Get",
                    dataType: "json",
                    success: function (data) {
                        if (data.Result == "1") {
                            var info = data.Data;
                            var html = "";
                            for (var i = 0; i < info.ArticlesList.length; i++) {
                                var nowHtml = info.ArticlesList[i];
                                html += '<tr class="row"><td class=itemIcon width=20 align=middle>&nbsp;&nbsp;</td><td width=80% class=subject><a href="/Home/Index/detail?id=' + nowHtml.id + '" target="_blank" title="' + nowHtml.title + '">' + '【' + info.Type[nowHtml.type] + '】 ' + nowHtml.title + isNew(nowHtml.created_at) + '</a></td><td width=20% class="s">' + dateFormat(nowHtml.created_at, "YYYY/mm/dd") + '</td></tr>'
        + '<tr height=1><td class="line" colspan=3 /></tr>';
                            }
                            html += '<tr>'
                            + '<td colspan=3 align="right" style="padding-right:10px">';
                            html += '</td>'
                            + '</tr>';
                            $("#guanlijujiao").html(html);
                        }
                    }
            });
            }

            function dateFormat(date,format) {
                return date.split(' ')[0];
            }

            function isNew(date) {
                //获取当前时间
                var currentTime = new Date();
                //自定义时间
                date = date.replace("-","/");//替换字符，变成标准格式  
                date = new Date(Date.parse(date));
                var day = parseInt((currentTime - date) / 1000 / 60 / 60 / 24);
                if (day < 3) {
                    return '<IMG border=0 src="http://192.168.61.9/Public/img/BDNew.gif" align=absBottom>';
                }

                return '';
            }
    
        </script>
    
    </body>
</html>