<?php

namespace app\model;

use think\Model;

class AuthGroupAccess extends Model
{
  /**
   * 更新用户组所包含的账户
   * @access public
   * @param int $id 用户组id
   * @param string $users 用户ID（逗号分隔字符串）
   * @return json
   */
  public function updateGroupUser($id, $users)
  {
    //取出数据库中当前角色与组关系，后续若写入失败用于恢复
    $oldInfo = AuthGroupAccess::where('group_id', $id)->select()->toArray();
    //清空该用户组与人员的对应关系
    $this->where('group_id', $id)->delete();
    //重新写入对应关系
    if ('' != $users) {
      $users = explode(',', $users);  //角色列表字符串转换为数组
      if (!in_array('1', $users) && 1 == $id) {
        $this->saveAll($oldInfo);
        return ['code' => -2, 'msg' => '系统管理员组必须包含管理员（admin）'];
      }
      $data = array();  //创建空数组用于保存用户组对应关系
      foreach ($users as $user) {
        $data[] = ['uid' => $user, 'group_id' => $id];
      }
      if ($this->saveAll($data)) {
        $rows = ['code' => 0, 'msg' => '角色成员更新成功'];
      } else {
        $this->saveAll($oldInfo);
        $rows = ['code' => -1, 'msg' => '角色成员更新出错'];
      }
    } else {
      if (1 == $id) {
        $this->saveAll($oldInfo);
        $rows = ['code' => -2, 'msg' => '系统管理员组必须包含管理员（admin）'];
      } else {
        $rows = ['code' => 0, 'msg' => '角色成员已全部清除'];
      }
    }
    return $rows;
  }
}
