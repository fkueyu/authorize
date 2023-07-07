var treeTable;
layui.use(function () {
  treeTable = layui.treeTable;
  var layer = layui.layer;
  var dropdown = layui.dropdown
  var form = layui.form;
  // 渲染
  var inst = treeTable.render({
    elem: '#ID-treeTable',
    url: '/authorize/getSubRules',
    tree: {
      customName: {
        name: 'title'
      },
      // 异步加载子节点
      async: {
        enable: true,
        url: '/authorize/getSubRules',
        autoParam: ["pid=id"]
      },

    },
    maxHeight: 'full-163',
    toolbar: '#TPL-treeTable',
    limits: [30, 50, 100],
    limit: 30,
    cols: [[
      { field: 'id', title: 'ID', width: 55, fixed: 'left' },
      { field: 'title', title: '名称', width: 160 },
      { field: 'name', title: '控制器', width: 120 },
      { field: 'pid', title: '父ID', width: 50 },
      { field: 'navid', title: '排序', width: 50 },
      { field: 'condition', title: '条件', width: 120 },
      {
        field: 'type', title: '条件状态', width: 90, templet: function (item) {
          if (item.id != 1) {
            if (item.type == 1) {
              return '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="typeSwitch" checked="checked">'
            } else if (item.type == 'null') {
              return '';
            } else {
              return '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="typeSwitch">'
            }
          } else {
            return '<input type="checkbox" name="type" value="' + item.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="typeSwitch" checked="checked" disabled="">'
          }
        }
      },
      {
        field: 'status', title: '规则状态', width: 90, templet: function (d) {
          if (d.id != 1) {
            if (d.status == 1) {
              return '<input type="checkbox" name="type" value="' + d.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch" checked="checked">'
            } else if (d.status == 'null') {
              return '';
            } else {
              return '<input type="checkbox" name="type" value="' + d.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch">'
            }
          } else {
            return '<input type="checkbox" name="type" value="' + d.id + '" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch" checked="checked"  disabled="">'
          }
        }
      },
      { field: 'remarks', title: '备注', width: 80 },
      { fixed: "right", title: "操作", width: 150, align: "center", toolbar: '#TPL-treeTable-tools' }
    ]],
    page: true
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
  // 表头工具栏工具事件
  treeTable.on("toolbar(ID-treeTable)", function (obj) {
    var config = obj.config;
    var tableId = config.id;
    var status = treeTable.checkStatus(tableId);
    // 获取选中行
    if (obj.event === "closeAll") {
      treeTable.expandAll('ID-treeTable', false);
    } else if (obj.event === "openAll") {
      treeTable.expandAll('ID-treeTable', true);
    }
  });
  // 单元格工具事件
  treeTable.on('tool(' + inst.config.id + ')', function (obj) {
    var layEvent = obj.event; // 获得 lay-event 对应的值
    var trElem = obj.tr;
    var trData = obj.data;
    var tableId = obj.config.id;
    if (layEvent === "addChild") {
      var index = layer.open({
        type: 1
        , title: '添加规则'
        , area: ['460px', '410px']
        , content: '<br><form id="new_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">名称</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="title" id="title" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">控制器</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="name" id="name" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">条件</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="condition" id="condition" autocomplete="off" value="{status} === 1"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">备注</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="remarks" id="remarks" autocomplete="off"></div></div><input name="pid" id="pid" type="hidden" value="' + trData.id + '"></form>' //这里content是一个普通的String
        , btn: ['提交', '关闭']
        , yes: function () {
          if ($('#name').val() == '' || $('#title').val() == '') {
            layer.msg('名称和控制器字段不能为空', { time: 2000 });
          } else {
            $.post("/authorize/saveRules", $("#new_menu").serialize(), function (data) {
              if (data.code === 0) {
                var newdata = {
                  id: data.id,
                  name: $('#name').val(),
                  title: $('#title').val(),
                  condition: $('#condition').val(),
                  remarks: $('#remarks').val(),
                  type: 1,
                  status: 1,
                  pid: trData.id,
                  navid: 0
                };
                treeTable.addNodes(tableId, {
                  parentIndex: trData["LAY_DATA_INDEX"],
                  index: -1,
                  data: newdata
                });
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
    } else if (layEvent === "modify") {
      $.post("/authorize/getRules", { id: trData.id }, function (result) {
        var index = layer.open({
          type: 1
          , title: '修改规则'
          , area: ['480px', '530px']
          , content: '<br><form id="mod_menu" class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">父ID</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="pid" id="pid" readonly="" value="' + result.data.pid + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">名称</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="title" id="title" value="' + result.data.title + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">控制器</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="name" id="name" value="' + result.data.name + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">条件</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="condition" id="condition" value="' + result.data.condition + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">排序规则</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="navid" id="navid" value="' + result.data.navid + '" autocomplete="off"></div></div><div class="layui-form-item"><label class="layui-form-label" style="width: 60px;">备注</label><div class="layui-inline" style="width: 340px;"><input class="layui-input" name="remarks" id="remarks" value ="' + result.data.remarks + '"></div></div><input name="id" id="id" type="hidden" value="' + trData.id + '"></form>' //这里content是一个普通的String
          , btn: ['提交', '关闭']
          , yes: function () {
            $.post("/authorize/updateRules", $("#mod_menu").serialize(), function (data) {
              if (data.code === 0) {
                var newdata = {
                  id: data.id,
                  name: $('#name').val(),
                  title: $('#title').val(),
                  condition: $('#condition').val(),
                  remarks: $('#remarks').val(),
                  type: trData.type,
                  status: trData.status,
                  pid: trData.id,
                  navid: $('#navid').val()
                };
                treeTable.updateNode(tableId, obj.index, newdata);
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
    } else if (layEvent === "del") {
      layer.confirm("真的删除[" + trData.name + "]行么", function (index) {
        $.post("/authorize/delRules", { id: trData.id, pid: trData.pid }, function (data) {
          if (data.code == 0) {
            obj.del(); // 等效如下
            // treeTable.removeNode(tableId, trElem.attr('data-index'))
          }
          layer.close(index);
          layer.msg(data.msg, { time: 2000 });
        });
      });
    }
  });
});