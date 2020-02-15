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
    <div class="header">
        <div class="search">
            <input type="text" placeholder="请输入车牌号..." name="carNo" value=""/>
            <button onclick="doSearch()"><i>搜索</i></button>
        </div>
        <div class="droplist">
            <select name="states" id="selectStates">
                <option style="display: none;" disable>状态筛选</option>
                <option value="/Home/Queue/list/p/1.html?states=01&ck=<?php echo ($ck); ?>">装车中/排队中</option>
                <option value="/Home/Queue/list/p/1.html?states=2&ck=<?php echo ($ck); ?>">已发货</option>
            </select>
        </div>
    </div>
    <div class="table-list">
        <table>
            <tr>
                <th style="width: 10%;">车牌号</th>
                <th style="width: 10%;">手机号</th>
                <th style="width: 10%;">客户名称</th>
                <th style="width: 10%;">货物类型</th>
                <th style="width: 10%;">车辆状态</th>
                <th style="width: 10%;">排队时间</th>
                <th style="width: 10%;">装车时间</th>
                <th style="width: 10%;">发货时间</th>
                <th style="width: 10%;">排队状态</th>
                <th style="width: 10%;">操作</th>
            </tr>
            <?php if(is_array($queues)): foreach($queues as $key=>$queue): ?><tr>
                    <td><?php echo ($queue["car_no"]); ?></td>
                    <td><?php echo ($queue["phone_no"]); ?></td>
                    <td><?php echo ($queue["client_name"]); ?></td>
                    <td><?php echo ($queue["goods_type"]); ?></td>
                    <td><?php echo ($queue["car_states"]); ?></td>
                    <td><?php echo ($queue["queued_at"]); ?></td>
                    <td><?php echo ($queue["loaded_at"]); ?></td>
                    <td><?php echo ($queue["transmited_at"]); ?></td>
                    <?php if($queue["states"] == 0): ?><td style="color: tomato;" >排队中</td>
                        <td>
                            <input type="button" id="<?php echo ($queue["id"]); ?>" class="button button-load" onclick="states_confirm(this)" value="装车">
                            <?php if($queue["priority"] == 0): ?><input type="button" id="<?php echo ($queue["id"]); ?>" class="button button-priority" onclick="priority_confirm(this)" value="加急"><?php endif; ?>
                        </td>
                        <?php elseif($queue["states"] == 1): ?> 
                        <td style="color: blue;">装车中</td>
                        <td><input type="button" id="<?php echo ($queue["id"]); ?>" class=" button button-transmit" onclick="states_confirm(this)" value="完成"></td>
                        <?php else: ?> <td style="color: seagreen;">已发货</td><td></td><?php endif; ?>
                </tr><?php endforeach; endif; ?>
        </table>
    </div>
    <div class="paginate">
        <tfoot>
            <!--分页显示？-->
            <tr>
              <td textalign="center" cl nowrap="true" colspan="9" height="20">
               <div class="pages"><?php echo ($page); ?></div>
              </td>
            </tr>
        </tfoot>
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
    .search{
        float: right;
        width: 15%;
    }
    .search input{
        float: left;
        flex: 4;
        height: 30px;
        outline: none;
        box-sizing: border-box;
        padding-left: 10px;
    }
    .search button{
        flex: 1;
        height: 30px;
        background-color: rgb(10, 170, 170);
        color: white;
        border-style: none;
        outline: none;
    }
    .search button i{
        font-style: normal;
    }
    .search button:hover{
        font-size: 16px;
    }
    .droplist {
        float: right;
        margin-right: 2%;
        margin-bottom: 2%;
        width: 120px;
            height: 30px;
            overflow: hidden;
            background-color: #eeeeee;
    }
    .droplist select {
        border: none;
        padding-left: 10px;
        width: 120px;
        height: 100%;
        -webkit-appearance: none;   /* Safari 和 Chrome */
        -moz-appearance: none;   /* Firefox */
        background: transparent;
    }
    .paginate {
        margin-top: 10px;
    }
    .button {
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        height: 30px;
    }
    .button-load {
        background-color:royalblue;
    }
    .button-priority {
        background-color: red;
    }
    .button-transmit {
        background-color:rgb(8, 114, 13);
    }
    table,table tr th, table tr td { border:1px solid #0094ff; }
    table { width: 100%; min-height: 25px; line-height: 25px; text-align: center; border-collapse: collapse;} 
    .pages{float: right}
    .pages a,.pages span {
    display:inline-block;
    padding:2px 10px;
    border:1px solid #f0f0f0;
    -webkit-border-radius:3px;
    -moz-border-radius:3px;
    border-radius:3px;
    font-size: 14px;
    }
    .pages a,.pages li {
    display:inline-block;
    list-style: none;
    text-decoration:none; color:#58A0D3;
    }
    .pages a.first,.pages a.prev,.pages a.next,.pages a.end{
    margin:0 auto;
    }
    .pages a:hover{
    border-color:#50A8E6;
    }
    .pages span.current{
    background:#50A8E6;
    color:#FFF;
    font-weight:700;
    border-color:#50A8E6;
    }
</style>

<script src="/Public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
    function states_confirm(element){
        var r=confirm("你确定"+element.value+"吗?")
        if (r==true){
            if (element.value == '装车') {
                var states = 1;
            } else {
                var states = 2;
            }

            $.ajax({
                url: "/Home/Queue/ChangeStates",
                type: "Post",
                data: {
                    states: states,
                    id: element.id,
                },
                dataType: "json",
                success: function (data) {
                    if (data.Result == "1") {
                        window.location.reload();
                    } else {
                        console.log("操作失败")
                    }
                }
            });
        }
    }

    function priority_confirm(element){
        var r=confirm("你确定"+element.value+"吗?")
        if (r==true){
            $.ajax({
                url: "/Home/Queue/priority",
                type: "Post",
                data: {
                    id: element.id,
                },
                dataType: "json",
                success: function (data) {
                    if (data.Result == "1") {
                        window.location.reload();
                    } else {
                        console.log("操作失败")
                    }
                }
            });
        }
    }

    $('#selectStates').change(function(){
        self.location.href=this.options[this.selectedIndex].value
    });

    function doSearch(){
        var car_no = $('input[name="carNo"]').val()
        var ck = $('#ck').val();
        if (car_no){
            url = window.location.href
            if(url.indexOf("?") != -1){
                url = url.split("?")[0];
            }
            self.location.href=url+'?car_no='+car_no+'&ck='+ck;
        } else {
            self.location.href='/home/queue/list?ck='+ck;
        }
    }
</script>