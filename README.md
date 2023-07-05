<!--
 * @Author: 彭雨
 * @Date: 2019-10-24 11:08:49
 * @LastEditors  : 彭雨
 * @LastEditTime : 2020-01-09 12:35:37
 -->
# authorize

2023年7月5日更新(PHP8.2 测试通过)

1. 后端ThinkPHP更新到：ThinkPHP 8
2. 前端Layui更新到：2.8.10
3. 一些小问题的修正
4. 使用Layui原生treeTable重写了规则列表页面

#### 介绍

完整的权限控制系统，后端ThinkPHP 6 + 前端Layui 2.5.4，权限验证类使用5ini99/think-auth（已修改为适配ThinkPHP6）。

手上有套自己写的数据查询和数据图形化的系统，因为涉及到各个部门、各地办事处的用户权限不同，所以用了5ini99/think-auth权限控制类进行权限控制，发现还挺好用，所以特别将权限部分抽出来，适配最新版本ThinkPHP然后开源出来抛砖引玉，可用于二次开发。
GitHub项目地址：[https://github.com/fkueyu/authorize](https://github.com/fkueyu/authorize)
码云项目地址：[https://gitee.com/frxc/authorize](https://gitee.com/frxc/authorize)

#### 软件架构

开发环境： XAMPP 3.2.4（PHP 7.3.7+Mysql），ThinkPHP 6.0.1，Layui 2.5.4，jquery 3.4.1
后续ThinkPHP 6发布正式版本后会同步升级到最新版本。

#### 安装教程

1. 根据自身条件搭建环境，Linux环境和Windows Server环境建议使用宝塔面板。其它Windows可以选择XAMPP（以下内容以XAMPP为例）。
2. 安装composer （后续可以升级ThinkPHP6框架核心）
3. 切换到XAMPP的htdocs目录克隆本仓库
4. 运行XAMPP，修改apache配置文件中网站根目录到ThinkPHP的public目录，下面是开发环境示例：
找到配置文件中两行：
DocumentRoot "D:/xampp7/htdocs"
<Directory "D:/xampp7/htdocs">
修改为：
DocumentRoot "D:/xampp7/htdocs/authorize/public"
<Directory "D:/xampp7/htdocs/authorize/public">
5. 启动mysql数据库服务，参考ThinkPHP6官方手册配置数据库连接（[https://www.kancloud.cn/manual/thinkphp6_0/1037531](https://www.kancloud.cn/manual/thinkphp6_0/1037531) ）
6. 手动创建数据库，名称为fr_lab，字符集：utf8，排序规则：utf8_general_ci，导入sql文件fr_lab.sql，位于public目录
7. 修改根目录.example.env为.env并在其中配置数据库连接。
8. 启动apache服务即可访问（管理员账号：admin，密码：123456,若无法登陆，在代码中注释掉登陆验证后修改一次密码即可）

#### 使用说明

1. 若修改数据库名和表名需同步修改ThinkPHP数据库配置文件和think-auth验证类的源文件（位于\authorize\vendor\5ini99\think-auth\src\Auth.php）
2. 欢迎提出问题和建议（QQ交流群：18685945，加群请备注项目名称）
3. 查看代码前建议先仔细阅读，权限认证类功能特性（来自[https://github.com/5ini99/think-auth](https://github.com/5ini99/think-auth)）：

* 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
* $auth=new Auth();  $auth->check('规则名称','用户id')
* 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
* $auth=new Auth();  $auth->check('规则1,规则2','用户id','and')
* 第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
* 3，一个用户可以属于多个用户组(think_auth_group_access表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(think_auth_group 定义了用户组权限)
* 4，支持规则表达式。
* 在think_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。

#### 系统截图

![首页](https://images.gitee.com/uploads/images/2019/0731/151222_2c6411f1_1219033.png "QQ截图20190731151159.png")
![用户管理](https://images.gitee.com/uploads/images/2019/0731/151320_b1679188_1219033.png "QQ截图20190731151159.png")
![角色管理-角色成员](https://images.gitee.com/uploads/images/2019/0731/151422_4da967ef_1219033.png "QQ截图20190731151349.png")
![角色管理-角色权限](https://images.gitee.com/uploads/images/2019/0731/151740_ae445a4f_1219033.png "QQ截图20190731151717.png")
![规则管理](https://images.gitee.com/uploads/images/2019/0731/151823_fed7920b_1219033.png "QQ截图20190731151717.png")
![数据字典](https://images.gitee.com/uploads/images/2019/0731/170740_4d74217f_1219033.png "QQ截图20190731151717.png")
