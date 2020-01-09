<?php
namespace mylib;

class Assistant
{
    /**
    *  获取周一和周日 getdays('2017-03-18')
    *  Array
    *	(
    *	    [0] => 2017-03-13
    *	    [1] => 2017-03-19
    *	)
    */
    public function getdays($day)
    {
        $lastday = date('Y-m-d', strtotime("$day Sunday"));
        $firstday = date('Y-m-d', strtotime("$lastday -6 days"));
        return array($firstday, $lastday);
    }

    /**
     *  获取月初和月末 getmonths('2017-2-2')
     *  Array
     *    (
     *        [0] => 2017-02-01
     *        [1] => 2017-02-28
     *    )
     */
    public function getmonths($day)
    {
        $firstday = date('Y-m-01', strtotime($day));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
        return array($firstday, $lastday);
    }

    //POST请求
    public function request_by_curl($remote_server, $post_string, $znv = FALSE)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        if($znv == FALSE){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function checkPassword($pwd) {
        if ($pwd == null) {
            return ['code' => 1, 'data' => '', 'msg' => '密码不能为空'];
        }
        $pwd = trim($pwd);
        if (!strlen($pwd) >= 6) {//必须大于6个字符
            return ['code' => 1, 'data' => '', 'msg' => '密码必须大于6字符'];
        }
        if (preg_match("/(0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){2}\d/", $pwd)){
            return ['code' => 1, 'data' => '', 'msg' => '密码不能包含连续数字'];
        }
        if (preg_match("/^[0-9]+$/", $pwd)) { //必须含有特殊字符
            return ['code' => 1, 'data' => '', 'msg' => '密码不能全是数字，请包含数字，字母大小写或者特殊字符'];
        }
        if (preg_match("/^[a-zA-Z]+$/", $pwd)) {
            return ['code' => 1, 'data' => '', 'msg' => '密码不能全是字母，请包含数字，字母大小写或者特殊字符'];
        }
        // if (preg_match("/^[0-9A-Z]+$/", $pwd)) {
        //     return ['code' => 1, 'data' => '', 'msg' => '请包含数字，字母大小写或者特殊字符'];
        // }
        // if (preg_match("/^[0-9a-z]+$/", $pwd)) {
        //     return ['code' => 1, 'data' => '', 'msg' => '请包含数字，字母大小写或者特殊字符'];
        // }
        return ['code' => 0, 'data' => '', 'msg' => '密码复杂度通过验证'];
    }

}
