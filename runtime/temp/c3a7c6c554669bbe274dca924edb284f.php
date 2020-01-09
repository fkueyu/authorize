<?php /*a:3:{s:53:"D:\xampp7\htdocs\authorize\app\view\header\index.html";i:1565082902;s:54:"D:\xampp7\htdocs\authorize\app\view\public\header.html";i:1565082902;s:56:"D:\xampp7\htdocs\authorize\app\view\public\leftside.html";i:1565082902;}*/ ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Authorize</title>
  <link rel="stylesheet" href="/static/layui/css/layui.css">
  <link rel="stylesheet" href="/static/css/font.css">
  <script src="//cdn.bootcss.com/jquery/3.4.1/jquery.min.js" charset="utf-8"></script>
  <script src="/static/layui/layui.js" charset="utf-8"></script>
  <script>
    //JavaScript代码区域
    layui.use(['element', 'layer'], function () {
      var element = layui.element
        , layer = layui.layer;
    });
  </script>
</head>

<body class="layui-layout-body" style="background-color: #F2F2F2;">
  <div class="layui-layout layui-layout-admin">
    <?php
use think\auth\Auth;
$auth = Auth::instance();
?>
<div class="layui-header">
  <div class="layui-logo" style="color:#e2e2e2; font-size:25px">Authorize</div>
  <!-- 头部区域（可配合layui已有的水平导航） -->
  <?php if((app('request')->session('intensity') == 0)): ?>
  <ul class="layui-nav layui-layout-left">
    <li class="layui-nav-item">
      <i onclick="State();" title="关闭左侧栏" class="iconfont">&#xe671;</i>
    </li>
    <?php if(($auth->check('header_index',app('request')->session('id')))): ?>
    <li class="layui-nav-item layui-this">
      <a href="/index">总览</a>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>
  <ul class="layui-nav layui-layout-right">
    <li class="layui-nav-item">
      <a href="javascript:;">
        <img src="/static/images/user.png" class="layui-nav-img"> <?php echo htmlentities(app('request')->session('user')); ?>
      </a>
      <dl class="layui-nav-child">
        <dd>
          <a href="/authorize/personal">
            <i class="iconfont">&#xe612;</i>&nbsp;&nbsp;个人信息</a>
        </dd> <?php if(($auth->check('sql', app('request')->session('id')))): ?>
        <dd>
          <a href="/common/sql" target="_blank">
            <i class="iconfont">&#xe62e;</i>&nbsp;&nbsp;数据字典</a>
        </dd>
        <?php endif; ?>
      </dl>
    </li>
    <li class="layui-nav-item">
      <a href="javascript:;" onclick="LogOut();">退出</a>
    </li>
    <li class="layui-nav-item">
      <a href="mailto:fkueyu@gmail.com">问题反馈</a>
    </li>
  </ul>
</div>
<script>
  function State() {
    if ($('.layui-side').css('left') == '0px') {
      $('.layui-side').animate({
        left: '-200px'
      }, 100);
      $('.layui-body').animate({
        left: '0px'
      }, 100);
      $('.layui-footer').animate({
        left: '0px'
      }, 0);
    } else {
      $('.layui-side').animate({
        left: '0px'
      }, 100);
      $('.layui-body').animate({
        left: '200px'
      }, 100);
      $('.layui-footer').animate({
        left: '200px'
      }, 0);
    }
  }
  function LogOut() {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    $.post("/index/logOut", function (data) {
      if (data.code == 0) {
        layer.close(e);
        layer.msg(data.msg
          , { time: 2000 }
          , function () {
            location.reload();
          });
      } else {
        layer.close(e);
        layer.msg(data.msg, { time: 2000 });
      }
    });
  }
</script> <?php if((app('request')->session('intensity') == 0)): ?>
<div class="layui-side layui-bg-black" style="left: 0px;">
  <div class="layui-side-scroll">
    <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
    <ul class="layui-nav layui-nav-tree" lay-filter="test">
      <?php if(($auth->check('user', app('request')->session('id')))): ?>
      <li class="layui-nav-item [usermenu]">
        <a class="" href="javascript:;">
          <i class="iconfont">&#xe672;</i>&nbsp;&nbsp;权限管理</a>
        <dl class="layui-nav-child">
          <?php if(($auth->check('user_list', app('request')->session('id')))): ?>
          <dd class="[userlist]">
            <a href="/authorize/user">&nbsp;&nbsp;
              <i class="iconfont">&#xe770;</i>&nbsp;&nbsp;用户管理</a>
          </dd>
          <?php endif; if(($auth->check('group_list', app('request')->session('id')))): ?>
          <dd class="[grouplist]">
            <a href="/authorize/group">&nbsp;&nbsp;
              <i class="iconfont">&#xe66f;</i>&nbsp;&nbsp;角色管理</a>
          </dd>
          <?php endif; if(($auth->check('menu_list', app('request')->session('id')))): ?>
          <dd class="[menulist]">
            <a href="/authorize/rules">&nbsp;&nbsp;
              <i class="iconfont">&#xe64e;</i>&nbsp;&nbsp;规则管理</a>
          </dd>
          <?php endif; ?>
        </dl>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<div class="layui-body" style="left: 200px;">
  <?php endif; ?>
    <!-- 内容主体区域 -->
    <br>&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="layui-breadcrumb">
      <a id="collect" title="查看明细">系统说明</a>
    </span>
    <div style="padding: 20px;">
      正文
    </div>
  </div>
</body>

</html>