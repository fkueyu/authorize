<?php

namespace app\controller;

use think\facade\{Session, View, Db};
use think\auth\Auth;

class Common
{

    //输出角色列表
    public function groupList()
    {
        $result = Db::name('auth_group')->field('id,title')->select();

        return json($result);
    }

    //数据字典
    public function sql()
    {
        $auth = Auth::instance();
        // 检测权限
      if ($auth->check('sql', Session::get('id'))) {// 第一个参数是规则名称,第二个参数是用户UID
          $database = 'fr_lab';
          $table_result = Db::query('show tables');
          $no_show_table = array();    //不需要显示的表
          $no_show_field = array();   //不需要显示的字段
          //取得所有的表名
          foreach ($table_result as $row) {
              if (!in_array($row, $no_show_table)) {
                  $tables[]['TABLE_NAME'] = $row['Tables_in_fr_lab'];
              }
          }
          //循环取得所有表的备注及表中列消息
          foreach ($tables as $k => $v) {
              $sql = 'SELECT * FROM ';
              $sql .= 'INFORMATION_SCHEMA.TABLES ';
              $sql .= 'WHERE ';
              $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";
              $table_result = Db::query($sql);
              foreach ($table_result as $t) {
                  $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
              }

              $sql = 'SELECT * FROM ';
              $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
              $sql .= 'WHERE ';
              $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";

              $fields = array();
              $field_result = Db::query($sql);
              foreach ($field_result as $t) {
                  $fields[] = $t;
              }
              $tables[$k]['COLUMN'] = $fields;
          }
          $html = '';
          //循环所有表
          foreach ($tables as $k => $v) {
              $html .= '	<h3>'.($k + 1).'、'.$v['TABLE_COMMENT'].'  （'.$v['TABLE_NAME'].'）</h3>'."\n";
              $html .= '	<table border="1" cellspacing="0" cellpadding="0" width="100%">'."\n";
              $html .= '		<tbody>'."\n";
              $html .= '			<tr>'."\n";
              $html .= '				<th>字段名</th>'."\n";
              $html .= '				<th>数据类型</th>'."\n";
              $html .= '				<th>默认值</th>'."\n";
              $html .= '				<th>允许非空</th>'."\n";
              $html .= '				<th>自动递增</th>'."\n";
              $html .= '				<th>备注</th>'."\n";
              $html .= '			</tr>'."\n";
              foreach ($v['COLUMN'] as $f) {
                  if (@!is_array($no_show_field[$v['TABLE_NAME']])) {
                      $no_show_field[$v['TABLE_NAME']] = array();
                  }
                  if (!in_array($f['COLUMN_NAME'], $no_show_field[$v['TABLE_NAME']])) {
                      $html .= '			<tr>'."\n";
                      $html .= '				<td class="c1">'.$f['COLUMN_NAME'].'</td>'."\n";
                      $html .= '				<td class="c2">'.$f['COLUMN_TYPE'].'</td>'."\n";
                      $html .= '				<td class="c3">'.$f['COLUMN_DEFAULT'].'</td>'."\n";
                      $html .= '				<td class="c4">'.$f['IS_NULLABLE'].'</td>'."\n";
                      $html .= '				<td class="c5">'.('auto_increment' == $f['EXTRA'] ? '是' : ' ').'</td>'."\n";
                      $html .= '				<td class="c6">'.$f['COLUMN_COMMENT'].'</td>'."\n";
                      $html .= '			</tr>'."\n";
                  }
              }
              $html .= '		</tbody>'."\n";
              $html .= '	</table>'."\n";
          }

          View::assign('html', $html);

          return View::fetch('/sql');
      }
        return '无权访问';
    }
}
