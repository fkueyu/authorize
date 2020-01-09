<?php /*a:1:{s:42:"D:\xampp7\htdocs\authorize\view\login.html";i:1565082902;}*/ ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Authorize 后台登录</title>
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="/static/css/radmin.css">
  <script src="/static/layui/layui.js" charset="utf-8"></script>
  <script src="//cdn.bootcss.com/jquery/3.4.1/jquery.min.js" charset="utf-8"></script>
</head>

<body class="login-bg">
  <div class="login">
    <div class="message">Authorize 权限控制</div>
    <div id="darkbannerwrap"></div>
    <form id="user" class="layui-form">
      <input name="username" placeholder="用户名" type="text" lay-verify="required" class="layui-input">
      <hr class="hr15">
      <input name="password" id="password" lay-verify="required" placeholder="密码" type="password" class="layui-input">
      <hr class="hr15">
      <hr class="hr15">
      <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
      <hr class="hr20">
    </form>
  </div>
  <script>
    $(function () {
      layui.use('form', function () {
        var form = layui.form;
      });
    })
    $("#user").on("submit", function (ev) {
      var e = layer.load(2, { shade: [0.2, '#2F4056'] });
      ev.preventDefault();
      $.post("/index/login", $("#user").serialize(), function (data) {
        if (data.code == 0) {
          layer.close(e);
          layer.msg(data.msg
            , { time: 3000 }
            , function () {
              location.replace(data.data);
            });
        } else {
          $("#password").val('');
          layer.close(e);
          layer.msg(data.msg, { time: 3000 });
        }
      });
    });

  </script>
  <!-- 底部结束 -->
</body>

</html>