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

    $result = AuthRule::find($id)->toArray();
    $count = count($result);
    return ['code' => 0, 'count' => $count, 'data' =>$result];
  }

  /**
   * 读取子规则
   * @access public
   * @param int $pid 父规则id
   * @return json
   */
  public function getSubRules($pid)
  {
    if(empty($pid)) $pid = 0;
    $result = AuthRule::field('id,title,pid,navid,name,type,status,condition,remarks,isParent')
        ->where('pid', $pid)
        ->order('navid')
        ->select()
        ->toArray();
    $count = count($result);
    return ['code' => 0, 'count' => $count, 'data' =>$result];
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
  public function delRules($id,$pid)
  {
    if (1 == $id) {
      $result = ['code' => -1, 'msg' => '根节点禁止删除'];
    } else {
      $children = AuthRule::where('pid', $id)->count();
      if ($children > 0) {
        $result = ['code' => -2, 'msg' => '此节点存在子规则，需先删除子规则'];
      } else {
        if ($this->where('id', $id)->delete()) {
          //查看是否还有同级子节点，若无修改父节点isParent为null
          $brothers = AuthRule::where('pid', $pid)->count();
          if($brothers == 0){
            $AuthRule = AuthRule::find($pid);
            $AuthRule->setAttr('isParent', null);
            $AuthRule->save();
          }
          $result = ['code' => 0, 'msg' => '删除成功'];
        } else {
          $result = ['code' => -3, 'msg' => '规则不存在'];
        }
      }
    }
    return $result;
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
        $AuthRule = AuthRule::find($rules['pid']);
        $AuthRule->setAttr('isParent', 'true');
        $AuthRule->save();
        $rules = $this->create($rules);
        $result = ['code' => 0, 'data' => $rules->id,'msg' => '规则[ ' . $rules->title . ' ]添加成功'];
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
