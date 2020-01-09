<?php

namespace app\controller;

use think\facade\{Session, View, Request};
use app\model\{User, Log};

class Index
{

  public function login()
  {
    $username = Request::param('username');
    $password = Request::param('password');
    if ('' != $username || '' != Session::get('user')) {
      if (Session::get('user')) {
        $this->redirect('index');
      } else {
        $User = new User;
        return json($User->login($username, $password));
      }
    } else {
      return View::fetch('/login');
    }
  }

  //首页
  public function index()
  {
    if ('' != Session::get('id')) {
      return View::fetch('/header/index');
    } else {
      return View::fetch('/login');
    }
  }

  //注销登录
  public function logout()
  {
    $username =  Session::get('username');
    if ('' != $username) {
      $log_data = ['userid' => $username, 'operation' => 'logout', 'login_city' => ''];
      $Log = new Log;
      $Log->saveLog($log_data);
    }
    Session::clear();
    return json(['code' => 0, 'msg' => '您已安全退出', 'data' => '']);
  }
}
