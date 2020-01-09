<?php

namespace app\model;

use think\Model;
use app\model\{AuthGroupAccess};

class AuthGroup extends Model
{
  /**
   * 新增角色组
   * @access public
   * @param string $title 角色组名称
   * @return json
   */
  public function saveGroup($title)
  {
    if ('' == $title) {
      $result = ['code' => -1, 'msg' => '请输入角色名'];
    } else {
      $group = array();
      $group['title'] = $title;
      $row = AuthGroup::where('title', $title)->count();
      if ($row > 0) {
        $result = ['code' => -2, 'msg' => '添加失败，角色【' . $title . '】已存在'];
      } else {
        $this->create($group);
        $result = ['code' => 0, 'msg' => '角色【' . $title . '】添加成功'];
      }
    }
    return $result;
  }

  /**
   * 更新用户组
   * @access public
   * @param int $id 用户组id
   * @param string $field 需修改的字段
   * @param string $value 修改为
   * @return string
   */
  public function updateGroup($id, $field, $value = '')
  {
    $updateGroup = AuthGroup::find($id);
    $updateGroup->setAttr($field, $value);
    $updateGroup->save();
    return '更新成功';
  }

  /**
   * 删除用户组
   * @access public
   * @param int $id 用户组id
   * @return json
   */
  public function delGroup($id)
  {
    if (1 == $id) {
      $result = ['code' => -1, 'msg' => '角色【系统管理员】禁止删除'];
    } else {
      $group = AuthGroup::find($id);
      if ($group) {
        AuthGroupAccess::where('group_id', $id)->delete();  //删除角色与用户关系
        $group->delete();
        $result = ['code' => 0, 'msg' => '删除成功'];
      } else {
        $result = ['code' => -1, 'msg' => '删除的角色不存在'];
      }
    }
    return $result;
  }
}
