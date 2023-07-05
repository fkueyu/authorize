<?php

namespace app\controller;

use app\BaseController;
use think\facade\{Session, View};
use app\model\{User, Log};

class Index extends BaseController
{
    //首页
  public function index()
  {
    if ('' != Session::get('id')) {
      return View::fetch('/header/index');
    } else {
      return View::fetch('/login');
    }
  }
  public function login()
  {
    $username = $this->request->param('username');
    $password = $this->request->param('password');
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
  //注销登录
  public function logout()
  {
    $username =  Session::get('username');
    if ('' != $username) {
      $log_data = ['userid' => $username, 'operation' => 'logout', 'create_time' => date('Y-m-d H:i:s'), 'login_city' => ''];
      $Log = new Log;
      $Log->saveLog($log_data);
    }
    Session::clear();
    return json(['code' => 0, 'msg' => '您已安全退出', 'data' => '']);
  }
}
