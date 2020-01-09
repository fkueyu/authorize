<?php

namespace app\model;

use think\Model;
use think\facade\{Session};
use app\model\{AuthGroupAccess, Log};

class User extends Model
{
  public function login($username, $password)
  {
    $user = User::where('username', $username)->find();
    if (password_verify($password, $user->password_hash)) {
      $this->assistant = new \mylib\Assistant();
      $time = date('Y-m-d H:i:s');
      Session::set('id', $user->getAttr('id'));
      Session::set('user', $user->getAttr('name'));
      Session::set('username', $user->getAttr('username'));
      Session::set('tel', $user->getAttr('tel'));
      $log_data = ['userid' => $user->getAttr('username'), 'operation' => 'login', 'create_time' => $time];
      $Log = new Log;
      $Log->saveLog($log_data);
      $intensity = $this->assistant->checkPassword(input('password'));
      if (0 == $intensity['code']) {
        Session::set('intensity', 0);
        $result = ['code' => 0, 'data' => $_SERVER['HTTP_REFERER'], 'msg' => '登录成功，正在跳转...'];
      } else {
        Session::set('intensity', 1);
        $result = ['code' => 0, 'data' => '/authorize/personal', 'msg' => '密码强度不足，即将跳转到密码修改页...'];
      }
    } else {
      $result = ['code' => -1, 'data' => '', 'msg' => '账号或密码错误,请重试'];
    }
    return $result;
  }

  /**
   * 修改个人信息
   * @access public
   * @param int $id 个人id
   * @param string $field 修改的字段名
   * @param string $value 修改为该值
   * @param string $opassword 修改密码时需提供旧密码验证
   * @param string $level 为1时可直接修改密码
   * @return json
   */
  public function updatePersonal($id, $field, $value, $opassword = '', $level = 0)
  {
    //更改密码时验证旧密码
    if ('password_hash' == $field && $level == 0) {
      $assistant = new \mylib\Assistant();
      $intensity = $assistant->checkPassword($value);
      if (1 == $intensity['code']) {
        $result = $intensity;
      } else {
        $password_hash = password_hash($value, PASSWORD_ARGON2I);
        $user = User::where('id', $id)->find();
        if (password_verify($opassword, $user->password_hash)) {
          $user->setAttr($field, $password_hash);
          $user->save();
          Session::set('intensity', 0);
          $result = ['code' => 0, 'data' => '/authorize/personal', 'msg' => '密码更新成功,即将刷新页面...'];
        } else {
          $result = ['code' => -1, 'data' => '', 'msg' => '旧密码错误，请检查'];
        }
      }
    } else {
      $personalInfo = User::find($id);
      $personalInfo->$field = $value;
      $personalInfo->save();
      $result = '更新成功';
    }
    return $result;
  }

  /**
   * 读取个人数据
   * @access public
   * @return json
   */
  public function getPersonal()
  {
    $result = array();
    $id = Session::get('id');
    $row = User::field('id,name,username,email,tel,department,position,status,update_time,create_time')
      ->find($id)->toArray();
    $result = ['code' => 0, 'msg' => '', 'count' => 1, 'data' => [$row]];
    return $result;
  }

  /**
   * 新增用户数据
   * @access public
   * @return json
   */
  public function saveUser($user, $group)
  {
    $row = User::where('username', $user['username'])->count();
    if ($row > 0) {
      return '添加失败，用户[ ' . $user['username'] . ' ]已存在';
    } else {
      $result = $this->create($user);
      $access = ['group_id' => $group, 'uid' => $result->id];
      if ('' != $access['group_id']) {
        AuthGroupAccess::create($access);
      }
      return '用户[ ' . $result->name . ' ]新增成功';
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
    if (1 == $id) {
      $result = ['code' => -1, 'msg' => '用户【系统管理员】禁止删除'];
    } else {
      $user = User::find($id);
      if ($user) {
        AuthGroupAccess::where('uid', $id)->delete(); //删除用户与角色关系
        $user->delete();
        $result = ['code' => 0, 'msg' => '删除成功', 'data' => ''];
      } else {
        $result = ['code' => -2, 'msg' => '删除的用户不存在', 'data' => ''];
      }
    }
    return $result;
  }
}
