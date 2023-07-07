function addUser() {
  layer.open({
    type: 2 //此处以iframe举例
    , title: '添加账号'
    , area: ['500px', '600px']
    , shade: 0
    , maxmin: true
    , content: '/authorize/addUser'
  });
}
var from;
layui.use(function () {
  form = layui.form;
  var element = layui.element
    , table = layui.table
    , layer = layui.layer;
  table.render({
    elem: '#userlist'
    , id: 'uReload'
    , maxHeight: 'full-163'
    , method: 'post'
    , url: '/authorize/getUsers' //数据接口
    , page: true //开启分页
    , limits: [30, 50, 100]
    , limit: 30
    , cols: [[ //表头
      { field: 'ids', title: '#', width: 50, type: 'numbers', fixed: 'left' }
      , { field: 'name', edit: 'text', title: '姓名', width: 100 }
      , { field: 'username', title: '账号', width: 100 }
      , { field: 'email', edit: 'text', title: '邮箱', width: 200 }
      , { field: 'tel', edit: 'text', title: '电话', width: 120, sort: true }
      , { field: 'department', edit: 'text', title: '部门', width: 160 }
      , { field: 'position', edit: 'text', title: '职位', width: 160 }
      , { field: 'group', title: '用户角色', width: 160 }
      , { field: 'update_time', title: '更新时间', width: 120, sort: true }
      , {
        field: 'status', title: '账户状态', width: 110, templet: function (d) {
          if (d.id === 1) {
            return '<input type="checkbox" name="lock" value="' + d.id + '" title="启用" lay-filter="lockstatus" checked="checked" disabled="" lay-skin="tag">';
          } else {
            if (d.status === 0) {
              return '<input type="checkbox" name="lock" value="' + d.id + '" title="启用" lay-filter="lockstatus" lay-skin="tag">';
            } else if (d.status === 1) {
              return '<input type="checkbox" name="lock" value="' + d.id + '" title="启用" lay-filter="lockstatus" checked="checked" lay-skin="tag">';
            } else {
              return d.status;
            }
          }
        }
      }
      , { fixed: 'right', width: 100, title: '操作', align: 'center', toolbar: '#toolbar' } //这里的toolbar值是模板元素的选择器
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
    $.post("/authorize/updateUser", { id: data.id, field: field, value: value }, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });
  //监听账户状态操作
  form.on('checkbox(lockstatus)', function (obj) {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    if (obj.elem.checked == false) {
      value = 0;
    } else {
      value = 1;
    }
    $.post("/authorize/updateUser", { id: this.value, field: 'status', value: value }, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });
  table.on('tool(user)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
    var data = obj.data; //获得当前行数据
    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    var tr = obj.tr; //获得当前行 tr 的DOM对象
    if (layEvent === 'del') { //删除
      layer.confirm('确认删除账号【' + obj.data.name + '】？', function (index) {
        //向服务端发送删除指令
        $.post("/authorize/delUser/", { id: obj.data.id }, function (data) {
          if (data.code === 0) {
            obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
          }
          layer.close(index);
          layer.msg(data.msg, { time: 2000 });
        });
      });
    } else if (layEvent === 'edit') { //编辑
      var index = layer.open({
        type: 1
        , title: '【' + obj.data.name + '】重置密码'
        , area: ['420px', '200px']
        , content: '<br><form id="new_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 50px;">重置为</label><div class="layui-inline" style="width: 300px;"><input class="layui-input" type="text" id="passwordn" value="123456" disabled></div></div></form>' //这里content是一个普通的String
        , btn: ['提交', '关闭']
        , yes: function () {
          //通过ajax提交数据
          $.post("/authorize/updateUser/", { id: obj.data.id, field: 'password_hash', value: $('#passwordn').val() }, function (data) {
            layer.close(index);
            layer.msg(data, { time: 2000 });
          });
        }
        , btn2: function () {
          layer.close(index);
        }
      });
    }
  });
});
//传入URL前缀及字段值
function operation(id) {
  var result = '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del"><i class="layui-icon">&#xe640;</i> 重置密码</a>';
  return result;
}