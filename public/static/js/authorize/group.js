var from, tree, layer, util;
layui.use(['element', 'layer', 'table', 'form', 'tree'], function () {
  form = layui.form
    , layer = layui.layer
    , util = layui.util
    , tree = layui.tree;
  var element = layui.element
    , table = layui.table;
  table.render({
    elem: '#grouplist'
    , id: 'uReload'
    , height: 'full-220'
    , method: 'post'
    , url: '/authorize/getGroup' //数据接口
    , page: true //开启分页
    , limits: [15, 50, 100]
    , limit: 15
    , cols: [[ //表头
      { field: 'ids', title: '#', width: 50, type: 'numbers', fixed: 'left' }
      , { field: 'id', title: 'ID', width: 60 }
      , { field: 'title', edit: 'text', title: '角色名', width: 200 }
      , { field: 'status', title: '状态', width: 110, templet: "<div>{{ status(d.status,d.id)}}</div>" }
      , { field: 'operation', title: '操作', width: 323, templet: "<div>{{ operation(d.id,d.title)}}</div>" }
    ]]
  });
  var $ = layui.$, active = {
    reload: function () {
      var title = $('#title');
      //执行重载
      table.reload('uReload', {
        page: {
          curr: 1 //重新从第 1 页开始
        }
        , where: {
          title: title.val(),
        }
      });
    },
    addGroup: function () {
      var title = $('#title').val();
      if (title == '') {
        layer.msg('请输入角色名称', { time: 2000 });
      } else {
        var e = layer.load(2, { shade: [0.2, '#2F4056'] });
        $.post("/authorize/saveGroup/", { title: title }, function (msg) {
          layer.close(e);
          layer.msg(msg.msg, { time: 1000 });
          if (msg.code == 0) {
            //执行重载
            table.reload('uReload', {
              page: {
                curr: 1 //重新从第 1 页开始
              }
            });
          }
        });
      }
    }
  };
  $('.groupTable .layui-btn').on('click', function () {
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
  //监听单元格编辑
  table.on('edit(group)', function (obj) {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    var value = obj.value //得到修改后的值
      , data = obj.data //得到所在行所有键值
      , field = obj.field; //得到字段
    $.post("/authorize/updateGroup", { id: data.id, field: field, value: value }, function (msg) {
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
    $.post("/authorize/updateGroup", { id: this.value, field: 'status', value: value }, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });

  table.on('tool(group)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    if (layEvent === 'del') { //删除
      layer.confirm('确认删除该角色【' + obj.data.title + '】？', function (index) {
        //向服务端发送删除指令
        $.post("/authorize/delGroup/", { id: obj.data.id }, function (data) {
          layer.close(index);
          if (data.code === 0) {
            obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
          }
          layer.msg(data.msg, { time: 2000 });
        });
      });
    }
  });
});

//传入URL前缀及字段值
function status(vals, id) {
  if (id === 1) {
    result = '<input type="checkbox" name="lock" value="' + id + '" title="启用" lay-filter="lockstatus" checked="checked" disabled="">';
  } else {
    if (vals === 0) {
      result = '<input type="checkbox" name="lock" value="' + id + '" title="启用" lay-filter="lockstatus">';
    } else if (vals === 1) {
      result = '<input type="checkbox" name="lock" value="' + id + '" title="启用" lay-filter="lockstatus" checked="checked">';
    } else {
      result = vals;
    }
  }

  return result;
}
//传入URL前缀及字段值
function operation(id, title) {
  var result_mod = '<a class="layui-btn layui-btn-sm" onclick="groupUser(' + id + ",\'" + title + '\')"><i class="layui-icon">&#xe66f;</i> 角色成员</a><a class="layui-btn layui-btn-warm layui-btn-sm" onclick="userMenu(' + id + ",\'" + title + '\')"><i class="layui-icon">&#xe679;</i> 角色权限</a>';
  var result_del = '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del"><i class="layui-icon">&#xe640;</i> 删除角色</a>';
  if (id == 1) {
    return result_mod;
  } else {
    return result_mod + result_del;
  }
}

function userMenu(id, title) {
  $('#userMenu').html("");
  var index = layer.open({
    type: 1
    , title: '编辑权限-' + title
    , area: ['360px', '600px']
    , content: '<br><form class="layui-form"><div id="userMenu"></div></form>' //这里content是一个普通的String
    , btn: ['提交', '关闭']
    , yes: function () {
      layer.close(index);
      var e = layer.load(2, { shade: [0.2, '#2F4056'] });
      var ruless = '';
      var checkedData = tree.getChecked('userMenu'); //获取选中节点的数据
      if (checkedData != '') {
        ruless = checkedData[0]['id'] + ',';  //获取根节点id
        checkedData = checkedData[0]["children"];
        //获取第一节节点ID
        var dataLength = checkedData.length;
        for (let index = 0; index < dataLength; index++) {
          ruless += checkedData[index]['id'] + ',';
          var temp = checkedData[index]['children'].length
          //判断是否有第二节节点
          if (temp > 0) {
            //获取第二节节点ID
            for (let i = 0; i < temp; i++) {
              ruless += checkedData[index]['children'][i]['id'] + ',';
            }
          }
        }
        ruless = ruless.substr(0, ruless.length - 1);
      }
      $.post("/authorize/updateGroup", { id: id, field: 'rules', value: ruless }, function (msg) {
        layer.close(e);
        layer.msg(msg, { time: 1000 });
      });
    }
    , btn2: function () {
      layer.close(index);
    }
  });
  var e = layer.load(2, { shade: [0.2, '#2F4056'] });
  //data数据中仅需要末节点设置选中状态，其它节点无需设置，否则出错
  $.post('/authorize/getUserRules', { id, id }, function (data) {
    layer.close(e);
    tree.render({
      elem: '#userMenu'
      , data: data
      , showCheckbox: true  //是否显示复选框
      , id: 'userMenu'
    });
  });
}

function groupUser(id, title) {
  $('#groupUser').html("");
  var index = layer.open({
    type: 1
    , title: '编辑成员-' + title
    , area: ['360px', '600px']
    , content: '<br><form class="layui-form"><div id="groupUser"></div></form>' //这里content是一个普通的String
    , btn: ['提交', '关闭']
    , yes: function () {
      layer.close(index);
      var e = layer.load(2, { shade: [0.2, '#2F4056'] });
      var users = '';
      var checkedData = tree.getChecked('groupUser'); //获取选中节点的数据
      if (checkedData != '') {
        checkedData = checkedData[0]["children"];    //取出子节点
        var dataLength = checkedData.length;
        for (let index = 0; index < dataLength; index++) {
          users += checkedData[index]['id'] + ',';
        }
        users = users.substr(0, users.length - 1);
      }
      $.post("/authorize/updateGroupUser/", { id: id, users: users }, function (msg) {
        layer.close(e);
        layer.msg(msg.msg, { time: 1000 });
      });
    }
    , btn2: function () {
      layer.close(index);
    }
  });
  //读取接口数据并渲染tree
  var e = layer.load(2, { shade: [0.2, '#2F4056'] });
  //data数据中仅需要末节点设置选中状态，其它节点无需设置，否则出错
  $.post('/authorize/getUserGroup', { id: id }, function (data) {
    layer.close(e);
    tree.render({
      elem: '#groupUser'
      , data: data
      , showCheckbox: true  //是否显示复选框
      , id: 'groupUser'
    });
  });
}