<?php

namespace app\model;

use think\Model;

class AuthRule extends Model
{
  /**
   * 读取规则
   * @access public
   * @param int $id 规则id
   * @return json
   */
  public function getRules($id)
  {
    if ('' == $id) {
      $result = AuthRule::field('id,title as name,pid,navid,name as title,type,status,condition,remarks')
        ->order('navid')
        ->select()
        ->toArray();
    } else {
      //修改规则的二次读取
      $result = AuthRule::where('id', $id)->field('id,title as name,pid,navid,name as title,type,status,condition,remarks')
        ->select()
        ->toArray();
    }
    return $result;
  }

  /**
   * 更新规则单个字段（状态字段）
   * @access public
   * @param int $id 规则id
   * @param string $field 需修改的字段
   * @param string $value 修改为
   * @return string
   */
  public function updateRulesState($id, $field, $value)
  {
    $AuthRule = AuthRule::find($id);
    $AuthRule->setAttr($field, $value);
    $AuthRule->save();
    return '更新成功';
  }

  /**
   * 删除规则
   * @access public
   * @param int $id 规则id
   * @return string
   */
  public function delRules($id)
  {
    if (1 === $id) {
      $result = ['code' => -1, 'msg' => '根节点禁止删除'];
    } else {
      $children = AuthRule::where('pid', $id)->count();
      if ($children > 0) {
        $result = ['code' => -2, 'msg' => '此节点存在子规则，需先删除子规则'];
      } else {
        if ($this->where('id', $id)->delete()) {
          $result = ['code' => 0, 'msg' => '删除成功'];
        } else {
          $result = ['code' => -3, 'msg' => '规则不存在'];
        }
      }
      return $result;
    }
  }

  /**
   * 新增规则
   * @access public
   * @return string
   */
  public function saveRules($rules)
  {
    if ('' == $rules['pid'] || '' == $rules['name'] || '' == $rules['title']) {
      $result = ['code' => -1, 'msg' => '缺少必要字段，请检查'];
    } else {
      $row = AuthRule::whereOr(['name' => $rules['name'], 'title' => $rules['title']])->count();
      if ($row) {
        $result = ['code' => -2, 'msg' => '添加失败，名称或控制器已存在'];
      } else {
        $this->create($rules);
        $result = ['code' => 0, 'msg' => '规则[ ' . $rules['title'] . ' ]添加成功'];
      }
    }
    return $result;
  }
  /**
   * 更新规则
   * @access public
   * @return string
   */
  public function updateRules($rules)
  {
    if ('' == $rules['id'] || '' == $rules['pid'] || '' == $rules['name'] || '' == $rules['title']) {
      $result = ['code' => -1, 'msg' => '缺少必要字段，请检查'];
    } else {
      $row = AuthRule::where("id <> {$rules['id']} AND (title = '{$rules['title']}' OR name = '{$rules['name']}')")->count();
      if ($row) {
        $result = ['code' => -2, 'msg' => '更新失败，名称或控制器已存在'];
      } else {
        $AuthRule = AuthRule::find($rules['id']);
        $AuthRule->setAttr('name', $rules['name']);
        $AuthRule->setAttr('title', $rules['title']);
        $AuthRule->setAttr('pid', $rules['pid']);
        $AuthRule->setAttr('navid', $rules['navid']);
        $AuthRule->setAttr('condition', $rules['condition']);
        $AuthRule->setAttr('remarks', $rules['remarks']);
        $AuthRule->save();
        $result = ['code' => 0, 'msg' => '规则[ ' . $rules['title'] . ' ]更新成功'];
      }
    }
    return $result;
  }
}
