layui.use(function () {
  var form = layui.form
    , layer = layui.layer;
  // 后端获取角色列表并允许多选
  $(function () {
    $.ajax({
      type: 'get',
      url: '/Common/groupList',
      dataType: 'json',
      success: function (dataDictList) {
        var obj = eval(dataDictList);
        var objLength = obj.length;
        if (objLength > 0) {
          $(obj).each(function (i) {
            $("#group").append('<option value="' + obj[i].id + '">' + obj[i].title + '</option>');
            form.render('select');
          });
        }
      }
    });
  });
  //监听提交
  form.on('submit(submit)', function (data) {
    //通过ajax提交数据
    $.post("/authorize/saveUser", $("#user").serialize(), function (data) {
      layer.msg(data, {
        time: 10000, //20s后自动关闭
        btn: ['确定', '继续添加']
        , yes: function () {
          window.parent.location.reload();    //刷新父页面
        }
        , btn2: function () {
          $("#reset").click()
        }
      });
    });
    return false;
  });
});