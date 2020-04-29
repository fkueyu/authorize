<?php

namespace app\controller;

use think\facade\{Session, Request, View};
use app\model\{AuthGroup, AuthRule, User, AuthGroupAccess};
use think\auth\Auth;

class Authorize
{
  private $auth;

  //检查是否登录
  public function __construct()
  {
    if ('' == Session::get('id')) {
      echo View::fetch('/login');
      exit;
    } else {
      $this->auth = Auth::instance();
    }
  }

  //用户管理
  public function User()
  {
    if ($this->auth->check('user_list', Session::get('id'))) {
      return View::fetch('/authorize/user');
    }
  }

  //个人信息
  public function personal()
  {
    return View::fetch('/authorize/personal');
  }

  //添加用户
  public function addUser()
  {
    if ($this->auth->check('user_list', Session::get('id'))) {
      return View::fetch('/authorize/add_user');
    }
  }

  //角色管理
  public function group()
  {
    if ($this->auth->check('group_list', Session::get('id'))) {
      return View::fetch('/authorize/group');
    }
  }

  //菜单（规则）管理
  public function rules()
  {
    if ($this->auth->check('menu_list', Session::get('id'))) {
      return View::fetch('/authorize/rules');
    }
  }

  /**
   * 修改个人信息
   * @access public
   * @param int $id 个人id
   * @param string $field 修改的字段名
   * @param string $value 修改为该值
   * @param string $opassword 修改密码时需提供旧密码验证
   * @return json
   */
  public function updatePersonal($id, $field, $value, $opassword = '')
  {
    $User = new User;
    $result = $User->updatePersonal($id, $field, $value, $opassword);
    return json($result);
  }

  /**
   * 读取个人数据
   * @access public
   * @return json
   */
  public function getPersonal()
  {
    $User = new User;
    $result = $User->getPersonal();
    return json($result);
  }

  // 新增用户数据
  public function saveUser()
  {
    if ($this->auth->check('user_list', Session::get('id'))) {
      $user = [
        'name' => input('name'),
        'username' => input('username'),
        'password_hash' => password_hash(input('password'), PASSWORD_ARGON2I),
        'email' => input('email'),
        'tel' => input('tel'),
        'position' => input('position'),
        'department' => input('department'),
      ];
      $User = new User;
      return $User->saveUser($user, input('group'));
    }
  }

  //读取所有用户数据
  public function getUsers()
  {
    if ($this->auth->check('user_list', Session::get('id'))) {
      $rows = input('limit') ?? 10;
      $page = input('page') ?? 1;
      $page -= 1;
      $offset = ($page) * $rows;
      $result = array();
      $result['code'] = 0;
      $result['msg'] = '';
      $result['count'] = User::where('name', 'like', '%' . input('name') . '%')->where('tel', 'like', '%' . input('tel') . '%')->count();
      $result_temp = User::field('id,name,username,email,tel,department,position,status,update_time,create_time')->where('name', 'like', '%' . input('name') . '%')->where('tel', 'like', '%' . input('tel') . '%')->limit($offset, $rows)->select()->toArray();
      $i = 0;
      foreach ($result_temp as $value) {
        $group_ids = AuthGroupAccess::where('uid', $value['id'])->select();
        $group_title = '';
        if ($group_ids) {
          foreach ($group_ids as $group_id) {
            $group_titles = AuthGroup::field('title')->where('id', $group_id['group_id'])->select()->toArray();
            $group_title .= $group_titles[0]['title'] . ',';
          }
          $group_title = substr($group_title, 0, strlen($group_title) - 1);
          $result_temp[$i]['group'] = $group_title;
        }
        ++$i;
      }
      $result['data'] = $result_temp;

      return json($result);
    }
  }

  /**
   * 删除用户
   * @access public
   * @param int $id 个人id
   * @return json
   */
  public function delUser($id)
  {
    if ($this->auth->check('user_list', Session::get('id'))) {
      $User = new User;
      return json($User->delUser($id));
    }
  }

  //修改用户数据
  public function updateUser()
  {
    $id = Request::param('id');
    $field = Request::param('field');
    $value = Request::param('value');
    if ($this->auth->check('user_list', Session::get('id'))) {
      if ('password_hash' == $field) {
        $value = password_hash($value, PASSWORD_ARGON2I);
      }
      if (1 == $id && 'status' == $field && 1 != $value) {
        return '系统管理员无法禁用';
      }
      $user = User::find($id);
      $user->$field = $value;

      return $user->save() ? '更新成功' : '更新失败';
    }
  }

  /**
   * 新增角色组
   * @access public
   * @param string $title 角色组名称
   * @return json
   */
  public function saveGroup()
  {
    $title = Request::param('title');
    if ($this->auth->check('group_list', Session::get('id'))) {
      $AuthGroup = new AuthGroup;
      return json($AuthGroup->saveGroup($title));
    }
  }

  //读取用户组数据
  public function getGroup()
  {
    if ($this->auth->check('group_list', Session::get('id'))) {
      $rows = input('limit') ?? 10;
      $page = input('page') ?? 1;
      $page -= 1;
      $offset = ($page) * $rows;
      $result['code'] = 0;
      $result['msg'] = '';
      $result['count'] = AuthGroup::where('title', 'like', '%' . input('title') . '%')->count();
      $result['data'] = AuthGroup::where('title', 'like', '%' . input('title') . '%')->limit($offset, $rows)->select();

      return json($result);
    }
  }

  /**
   * 更新用户组
   * @access public
   * @param int $id 用户组id
   * @param string $field 需修改的字段
   * @param string $value 修改为
   * @return string
   */
  public function updateGroup()
  {
    $id = Request::param('id');
    $field = Request::param('field');
    $value = Request::param('value');
    if ($this->auth->check('group_list', Session::get('id'))) {
      $AuthGroup = new AuthGroup;
      return $AuthGroup->updateGroup($id, $field, $value);
    }
  }

  /**
   * 删除用户组
   * @access public
   * @param int $id 用户组id
   * @return json
   */
  public function delGroup()
  {
    $id = Request::param('id');
    if ($this->auth->check('group_list', Session::get('id'))) {
      $AuthGroup = new AuthGroup;
      return json($AuthGroup->delGroup($id));
    }
  }

  /**
   * 读取用户组规则
   * @access public
   * @param int $id 用户组id
   * @return json
   */
  public function getUserRules()
  {
    $id = Request::param('id');
    if ($this->auth->check('group_list', Session::get('id'))) {
      $userRules = AuthGroup::where('id', $id)->field('title,rules')->select()->toArray();
      $rules = explode(',', $userRules[0]['rules']);
      //获取根节点
      $result = AuthRule::where('id', 1)->field('title,id')->select()->toArray();
      //获取一级节点
      $children = AuthRule::where('pid', $result[0]['id'])->field('title,id')->order('navid')->select()->toArray();
      //获取二级节点
      $y = 0;
      foreach ($children as $value) {
        $children1 = AuthRule::where('pid', $value['id'])->field('title,id')->order('navid')->select()->toArray();
        if (empty($children1)) {
          $children[$y]['children'] = array();  //二节节点不存在时将children设置为空数组
          if (in_array($value['id'], $rules)) {
            $children[$y]['checked'] = true;  //一节节点为末节点且有权限时设置选择状态
          }
        } else {
          //二节节点存在时设置为展开状态
          $children[$y]['spread'] = true;
          $i = 0;
          //向二级节点数组增加checked状态并设置children为空
          foreach ($children1 as $ch) {
            if (in_array($ch['id'], $rules)) {
              $children1[$i]['checked'] = true;
            }
            $children1[$i]['children'] = array();
            ++$i;
          }
          $children[$y]['children'] = $children1;
        }
        ++$y;
      }
      //设置根节点为展开状态，并添加children
      $result[0]['spread'] = true;
      $result[0]['children'] = $children;

      return json($result);
    }
  }

  // 读取组所包含用户
  public function getUserGroup($id = '')
  {
    if ($this->auth->check('group_list', Session::get('id'))) {
      $groupUser = AuthGroupAccess::where('group_id', $id)->field('uid')->select()->toArray();
      $users = array();
      if ($groupUser != array()) {
        foreach ($groupUser as $value) {
          $users[] = $value['uid'];
        }
      }
      //根节点
      $row[0]['title'] = '全选';
      $row[0]['id'] = 0;
      $result = User::field('name,username,id')->where('status', 1)->select()->toArray();
      $y = 0;
      //向一级节点数组增加title、checked状态并设置children为空
      foreach ($result as $value) {
        $result[$y]['title'] = $result[$y]['name'] . ' [' . $result[$y]['username'] . ']';
        if (in_array($value['id'], $users)) {
          $result[$y]['checked'] = true;
        }
        $result[$y]['children'] = array();
        ++$y;
      }
      $row[0]['spread'] = true;
      $row[0]['children'] = $result;

      return json($row);
    }
  }

  /**
   * 更新用户组所包含的账户
   * @access public
   * @param int $id 用户组id
   * @param string $users 用户ID（逗号分隔字符串）
   * @return json
   */
  public function updateGroupUser()
  {
    $id = Request::param('id');
    $users = Request::param('users');
    if ($this->auth->check('group_list', Session::get('id'))) {
      $AuthGroupAccess = new AuthGroupAccess;
      return json($AuthGroupAccess->updateGroupUser($id, $users));
    }
  }

  /**
   * 读取规则
   * @access public
   * @param int $id 规则id
   * @return json
   */
  public function getRules()
  {
    $id = Request::param('id');
    if ($this->auth->check('menu_list', Session::get('id'))) {
      $AuthRule = new AuthRule;
      return json($AuthRule->getRules($id));
    }
  }

  /**
   * 更新规则单个字段（状态字段）
   * @access public
   * @param int $id 规则id
   * @param string $field 需修改的字段
   * @param string $value 修改为
   * @return string
   */
  public function updateRulesState()
  {
    $id = Request::param('id');
    $field = Request::param('field');
    $value = Request::param('value');
    if ($this->auth->check('menu_list', Session::get('id'))) {
      $AuthRule = new AuthRule;
      return $AuthRule->updateRulesState($id, $field, $value) ? '更新成功' : '更新失败';
    }
  }

  /**
   * 删除规则
   * @access public
   * @param int $id 规则id
   * @return string
   */
  public function delRules()
  {
    $id = Request::param('id');
    if ($this->auth->check('menu_list', Session::get('id'))) {
      $AuthRule = new AuthRule;
      return json($AuthRule->delRules($id));
    }
  }

  /**
   * 新增规则
   * @access public
   * @return string
   */
  public function saveRules()
  {
    if ($this->auth->check('menu_list', Session::get('id'))) {
      $rules = [
        'pid' => input('pid'),
        'name' => input('name'),
        'title' => input('title'),
        'condition' => input('condition'),
        'remarks' => input('remarks')
      ];
      $AuthRule = new AuthRule;
      return json($AuthRule->saveRules($rules));
    }
  }

  /**
   * 更新规则
   * @access public
   * @return string
   */
  public function updateRules()
  {
    if ($this->auth->check('menu_list', Session::get('id'))) {
      $rules = [
        'id' => input('id'),
        'pid' => input('pid'),
        'name' => input('name'),
        'title' => input('title'),
        'navid' => input('navid'),
        'condition' => input('condition'),
        'remarks' => input('remarks')
      ];
      $AuthRule = new AuthRule;
      return json($AuthRule->updateRules($rules));
    }
  }
}
