<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>仓库车辆排队系统</title>
</head>
<body>
    <div class="logo">
        <img src="/Public/img/金田logo.png">
    </div>
    <hr>
    <div class="form">
        <h4 class="title">仓库车辆排队系统</h4>
        <div class="info">
            <span id="successInfo"></span>
            <span id="errorInfo"></span>
        </div>
        <div class="input-area">
            <form action="" method="POST">
                <div>
                    <label>车牌号码: </label>
                    <input type="text" name="carNo" placeholder="后5位车牌号" maxlength="5">
                </div>
                <div class="field">
                    <label>手机号码: </label>
                    <input type="text" name="phoneNo" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div class="field">
                    <label>客户名称: </label>
                    <input type="text" name="clientName">
                </div>
                <div class="field">
                    <label>货物类型: </label>
                    <select name="goodsType">
                        <option value="铜管">铜管</option>
                        <option value="铜带">铜带</option>
                        <option value="铜板">铜板</option>
                    </select>
                </div>
                <div class="field">
                    <label>车辆状态: </label>
                    <select name="carStates">
                        <option value="空车">空车</option>
                        <option value="重车">重车</option>
                    </select>
                </div>
            </form>
        </div>
        <button type="button" onclick="submitForm()">提交</button>
    </div>
</body>
</html>

<style>
    form {
        font-family: STXihei, "微软雅黑", "Microsoft YaHei", "微软雅黑";
    }
    .logo {
        background-image:url("/Public/img/background.jpg");
        height: 244px;
        text-align: center;
    }
    .title {
        color: rgb(10, 170, 170);
        font-size:20px;
    }
    .info {
        height: 5%;
    }
    .form {
        text-align: center;
    }
    .input-area {
        padding-top: 10px;
    }
    .field {
        height: 5%;
        margin: 20px 0px 20px 0px;
    }
    input {
        margin-left: 10px;
        text-align:center;
        border: 1px solid rgb(216, 216, 216);
    }
    input::-ms-input-placeholder {
        text-align: center;
    }
    input::-webkit-input-placeholder {
        text-align: center;
    }
    select {
        margin-left: 10px;
        padding-left: 35px;
        width: 130px;
        height: 22px;
        background: transparent;
        border: 1px solid rgb(216, 216, 216);
    }
    button {
        margin-top: 20px;
        background-color:royalblue;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        width: 50%;
        height: 30px;
    }
    #successInfo {
        color: green;
    }
    #errorInfo {
        color:orange;
    }
</style>

<script src="/Public/js/jquery-1.10.2.min.js"></script>
<script>
    function submitForm() {
        var carNo = $("input[name='carNo']").val()
        var phoneNo = $("input[name='phoneNo']").val()
        var clientName = $("input[name='clientName']").val()
        var goodsType = $("select[name='goodsType']").val()
        var carStates = $("select[name='carStates']").val()
        if (!carNo || carNo.length != 5) {
            alert("请输入正确的车牌号")
            return
        }
        if (!phoneNo || isNaN(phoneNo) || phoneNo.length != 11) {
            alert("请输入正确的手机号")
            return
        }
        if (!clientName) {
            alert("请输入客户名称")
            return
        }
        $.ajax({
            url: "/Home/queue/checkCar",
            type: "POST",
            data: {
                carNo: carNo,
                },
            dataType: "json",
            success: function (data) {
                var info = data.Data;
                if (data.Result == "1") {
                    $.ajax({
                        url: "/Home/queue/submit",
                        type: "POST",
                        data: {
                            carNo: carNo,
                            phoneNo: phoneNo,
                            clientName: clientName,
                            goodsType: goodsType,
                            carStates: carStates
                            },
                        dataType: "json",
                        success: function (data) {
                            $("input[name='carNo']").val('');
                            $("input[name='phoneNo']").val('');
                            $("input[name='clientName']").val('');
                            var info = data.Data;
                            if (data.Result == "1") {
                                $("#errorInfo").html('');
                                $("#successInfo").html(info.Message);
                            } else {
                                $("#successInfo").html('');
                                $("#errorInfo").html(info.Message);
                            }
                        }
                    });
                } else {
                    $("#successInfo").html('');
                    $("#errorInfo").html(info.Message);
                }
            }
        });
    }
</script>