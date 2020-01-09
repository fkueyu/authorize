//使用treeTable扩展
layui.config({
  base: '/static/js/',
})
var treeTable, re, form;
layui.use(['element', 'treeTable', 'layer', 'code', 'form'], function () {
  treeTable = layui.treeTable;
  var o = layui.$,
    form = layui.form,
    layer = layui.layer;
  var e = layer.load(2, { shade: [0.2, '#2F4056'] }); //打开数据加载动画
  re = treeTable.render({
    elem: '#tree-table',
    url: '/authorize/getRules/',
    icon_key: 'title',// 必须
    cols: [
      {
        key: 'title',
        title: '控制器',
        width: '180px',
        template: function (item) {
          if (item.level == 0) {
            return '<span style="color:#FF5722;">' + item.title + '</span>';
          } else if (item.level == 1) {
            return '<span style="color:#009688;">' + item.title + '</span>';
          } else if (item.level == 2) {
            return '<span style="color:#5FB878;">' + item.title + '</span>';
          }
        }
      },
      {
        key: 'id',
        title: 'ID',
        width: '20px',
        align: 'center',
      },
      {
        key: 'pid',
        title: '父ID',
        width: '20px',
        align: 'center',
      },
      {
        key: 'navid',
        title: '排序',
        width: '20px',
        align: 'center',
      },
      {
        key: 'name',
        title: '名称',
        width: '80px',
        align: 'center',
      },
      {
        key: 'condition',
        title: '条件',
        width: '80px',
        align: 'center',
      },
      {
        key: 'type',
        title: '条件状态',
        width: '80px',
        align: 'center',
        template: function (item) {
          var result = '';
          if (item.id != 1) {
            if (item.type == 1) {
              result = '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="typeSwitch" checked="checked">'
            } else if (item.type == 'null') {
              result = '';
            } else {
              result = '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="typeSwitch">'
            }
          }

          return result;
        }
      },
      {
        key: 'remarks',
        title: '备注',
        width: '200px',
        align: 'center',
      },
      {
        key: 'status',
        title: '规则状态',
        width: '80px',
        align: 'center',
        template: function (item) {
          var result = '';
          if (item.id != 1) {
            if (item.status == 1) {
              result = '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch" checked="checked">'
            } else if (item.status == 'null') {
              result = '';
            } else {
              result = '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch">'
            }
          }
          return result;
        }
      },
      {
        title: '操作',
        align: 'left',
        width: '200px',
        template: function (item) {
          var add = '<a class="layui-btn layui-btn-sm" onclick="add(' + item.id + ')"><i class="layui-icon">&#xe654;</i>子规则</a>';
          var modify = '<a class="layui-btn layui-btn-normal layui-btn-sm" onclick="modify(' + item.id + ')"><i class="layui-icon">&#xe642;</i></a>';
          var del = '<a class="layui-btn layui-btn-danger layui-btn-sm" onclick="del(' + item.id + ')"><i class="layui-icon">&#xe640;</i></a>';
          if (item.level == 0) {
            return add + modify;
          } else if (item.level == 1) {
            return add + modify + del;
          } else {
            return modify + del;
          }
        }
      }
    ],
    end: function () {
      form.render();  //因表单动态生成需手动渲染表单元素
      layer.close(e); //关闭加载动画
    }
  });
  //监听指定开关
  form.on('switch(typeSwitch)', function (data) {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    if (this.checked == false) {
      value = 0;
    } else {
      value = 1;
    }
    $.post("/authorize/updateRulesState", { id: this.value, field: 'type', value: value }, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });
  //监听指定开关
  form.on('switch(statusSwitch)', function (data) {
    var e = layer.load(2, { shade: [0.2, '#2F4056'] });
    if (this.checked == false) {
      value = 0;
    } else {
      value = 1;
    }
    $.post("/authorize/updateRulesState", { id: this.value, field: 'status', value: value }, function (msg) {
      layer.close(e);
      layer.msg(msg, { time: 1000 });
    });
  });
  // 全部展开
  o('.open-all').click(function () {
    treeTable.openAll(re);
  })
  // 全部关闭
  o('.close-all').click(function () {
    treeTable.closeAll(re);
  })
})
function del(id) {
  layer.confirm('确认删除该规则？', function (index) {
    $.post("/authorize/delRules", { id: id }, function (data) {
      if (data.code === 0) {
        treeTable.render(re);
      }
      layer.close(index);
      layer.msg(data.msg, { time: 2000 });
    });
  });
}
function add(id) {
  var index = layer.open({
    type: 1
    , title: '添加规则'
    , area: ['460px', '410px']
    , content: '<br><form id="new_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">名称</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="title" id="title" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">控制器</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="name" id="name" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">条件</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="condition" id="condition" autocomplete="off" value="{status} === 1"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">备注</label><div class="layui-inline" style="width: 340px;"><textarea placeholder="请输入内容" name="remarks" id="remarks" class="layui-textarea"></textarea></div></div><input name="pid" id="pid" type="hidden" value="' + id + '"></form>' //这里content是一个普通的String
    , btn: ['提交', '关闭']
    , yes: function () {
      //通过ajax提交数据
      if ($('#name').val() == '' || $('#title').val() == '') {
        layer.msg('名称和控制器字段不能为空', { time: 2000 });
      } else {
        $.post("/authorize/saveRules", $("#new_menu").serialize(), function (data) {
          if (data.code === 0) {
            treeTable.render(re);
          }
          layer.close(index);
          layer.msg(data.msg, { time: 2000 });
        });
      }
    }
    , btn2: function () {
      layer.close(index);
    }
  });
}

function modify(id) {
  $.post("/authorize/getRules", { id: id }, function (result) {
    var index = layer.open({
      type: 1
      , title: '修改规则'
      , area: ['480px', '530px']
      , content: '<br><form id="mod_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">父ID</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="pid" id="pid" value="' + result[0].pid + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">名称</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="title" id="title" value="' + result[0].name + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">控制器</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="name" id="name" value="' + result[0].title + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">条件</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="condition" id="condition" value="' + result[0].condition + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">排序规则</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="navid" id="navid" value="' + result[0].navid + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">备注</label><div class="layui-inline" style="width: 340px;"><textarea class="layui-textarea" name="remarks" id="remarks">' + result[0].remarks + '</textarea></div></div><input name="id" id="id" type="hidden" value="' + id + '"></form>' //这里content是一个普通的String
      , btn: ['提交', '关闭']
      , yes: function () {
        //通过ajax提交数据
        $.post("/authorize/updateRules", $("#mod_menu").serialize(), function (data) {
          if (data.code === 0) {
            treeTable.render(re);
          }
          layer.close(index);
          layer.msg(data.msg, { time: 2000 });
        });
      }
      , btn2: function () {
        layer.close(index);
      }
    });
  });
}