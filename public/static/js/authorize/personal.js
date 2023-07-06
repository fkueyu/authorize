layui.use(function () {
  var element = layui.element
    , table = layui.table
    , layer = layui.layer;
  table.render({
    elem: '#user'
    , id: 'uReload'
    , maxHeight: 'full-163'
    , method: 'post'
    , url: '/authorize/getPersonal' //数据接口
    , cols: [[ //表头
      { field: 'ids', title: '#', width: 50, type: 'numbers', fixed: 'left' }
      , { field: 'name', edit: 'text', title: '姓名', width: 130 }
      , { field: 'username', title: '账号', width: 130 }
      , { field: 'email', edit: 'text', title: '邮箱', width: 220 }
      , { field: 'tel', edit: 'text', title: '电话', width: 130, sort: true }
      , { field: 'department', edit: 'text', title: '部门', width: 155 }
      , { field: 'position', edit: 'text', title: '职位', width: 155 }
      , { field: 'update_time', title: '更新时间', width: 200, sort: true }
      , { fixed: 'right', width: 120, title: '操作', align: 'center', toolbar: '#toolbar' } //这里的toolbar值是模板元素的选择器
    ]]
  });
  var $ = layui.$, active = {
    reload: function () {
      var name = $('#name');
      var tel = $('#tel');
      //执行重载
      table.reload('uReload', {
        page: {
          curr: 1 //重新从第 1 页开始
        }
        , where: {
          name: name.val(),
          tel: tel.val(),
        }
      });
    }
  };
  $('.userTable .layui-btn').on('click', function () {
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
  //监听单元格编辑
  table.on('edit(user)', function (obj) {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    var value = obj.value //得到修改后的值
      , data = obj.data //得到所在行所有键值
      , field = obj.field; //得到字段
    $.post("/authorize/updatePersonal/" + data.id + "/" + field + "/" + value, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });
  table.on('tool(user)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
    var data = obj.data; //获得当前行数据
    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    var tr = obj.tr; //获得当前行 tr 的DOM对象
    if (layEvent === 'edit') { //编辑
      var index = layer.open({
        type: 1
        , title: '修改密码'
        , area: ['420px', '320px']
        , content: '<br><form id="new_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 50px;">旧密码</label><div class="layui-inline" style="width: 300px;"><input class="layui-input" type="password" name="password" id="password" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 50px;">新密码</label><div class="layui-inline" style="width: 300px;"><input class="layui-input" type="password" name="passwordn" id="passwordn" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 50px;">重复新密码</label><div class="layui-inline" style="width: 300px;"><input class="layui-input" type="password" name="passwordn1" id="passwordn1" autocomplete="off"></div></div></form>' //这里content是一个普通的String
        , btn: ['提交', '关闭']
        , yes: function () {
          //通过ajax提交数据
          if ($('#passwordn').val() != $('#passwordn1').val()) {
            layer.msg('两次输入密码不同，请检查', { time: 2000 });
          } else {
            $.post("/authorize/updatePersonal/", { id: obj.data.id, field: 'password_hash', value: $('#passwordn').val(), opassword: $('#password').val() }, function (data) {
              if (data.code == 0) {
                layer.close(index);
                layer.msg(data.msg
                  , { time: 3000 }
                  , function () {
                    location.replace(data.data);
                  });
              } else {
                layer.msg(data.msg, { time: 3000 });
              }
            });
          }
        }
        , btn2: function () {
          layer.close(index);
        }
      });
    }
  });
});