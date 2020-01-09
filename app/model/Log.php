<?php

namespace app\model;

use think\Model;

class Log extends Model
{
  /**
   * 新增LOG数据
   * @access public
   * @return json
   */
  public function saveLog($log)
  {
    $this->create($log);
    return 0;
  }
}
