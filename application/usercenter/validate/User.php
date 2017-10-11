<?php
/**
 * @category    app
 * @package     usercenter
 * @subpackage  validate
 * @author      grey256
 * @version     2017/09/22 14:20:40
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\usercenter\validate;

use think\Validate;
use app\usercore\model\UserInfo;

/**
 * 用户注册/登录信息验证
 */
class User extends Validate {

    /**
     * 验证规则
     */
    protected $rule    = [
        'username' => 'require|min:4|max:32|unique:userinfo|checkDenyMember|checkUserName',
        'password' => 'require|min:6|max:30|checkPassword',
        'repasswd' => 'require|confirm:password',
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'username.require'         => '用户名不能为空',
        'username.min'             => '用户名长度不能低于4个字符',
        'username.max'             => '用户名长度不能超过32个字符',
        'username.unique'          => '用户名已被占用，请更换一个',
        'username.checkDenyMember' => '抱歉该用户名不能被注册',
        'username.checkUsername'   => '抱歉该用户名不符合要求',
        'username.checkIsMember'   => '抱歉该用户名不存在，请注册',
        'password.require'         => '密码不能为空',
        'password.min'             => '密码长度不能低于6位',
        'password.max'             => '密码长度不能超过30位',
        'password.checkPassword'   => '密码不符合规范',
        'repasswd.require'         => '重复输入密码不能为空',
        'repasswd.checkRepassWd'   => '两次密码不一致',
    ];

    /**
     * 验证场景
     */
    protected $scene  = [
        'register' => ['username', 'password', 'repassword'],
        'login'    => ['username' => 'require|min:4|max:32|checkDenyMember|checkUserName|checkIsMember', 'password'],
    ];

    /**
     * 用户黑名单校验
     *
     * @author  grey256
     * @version 2017/09/22 14:27:41
     * @param   string     $value      待校验的用户名
     * @param   mixed      $rule       校验规则
     * @param   array      $data       全部数据
     * @return  bool
     */
    protected function checkDenyMember($value, $rule, $data) {
        //TODO:暂时没有用户黑名单
        return true;
    }

    /**
     * 用户名格式校验
     *
     * @author  grey256
     * @version 2017/09/22 14:29:19
     * @param   string     $value      待校验的用户名
     * @param   mixed      $rule       校验规则
     * @param   array      $data       全部数据
     * @return  bool
     */
    protected function checkUsername($value, $rule, $data) {
        if (strpos($value, ' ') !== false) {
            //不允许用户名中间存在空格
            return false;
        }

        //用户名必须为数字、字母、下划线组成
        preg_match("/^[a-zA-Z0-9_]{4,32}$/", $value, $arrMatches);
        if (!$arrMatches) {
            return false;
        }

        return true;
    }

    /**
     * 用户名格式校验
     *
     * @author  grey256
     * @version 2017/09/23 15:26:32
     * @param   string     $value      待校验的用户名
     * @param   mixed      $rule       校验规则
     * @param   array      $data       全部数据
     * @return  bool
     */
    protected function checkIsMember($value, $rule, $data) {
        $intUid = UserInfo::getUserAttrByName($value, 'uid');
        if (0 < $intUid) {
            return true;
        }

        return false;
    }

    /**
     * 密码格式校验
     *
     * @author  grey256
     * @version 2017/09/22 14:29:41
     * @param   string     $value      待校验的密码
     * @param   mixed      $rule       校验规则
     * @param   array      $data       全部数据
     * @return  bool
     */
    protected function checkPassword($value, $rule, $data) {
        //TODO:暂时没有强制要求密码格式
        return true;
    }

    /**
     * 校验重复输入的密码
     *
     * @author  grey256
     * @version 2017/09/22 14:29:41
     * @param   string     $value      待校验的重复密码
     * @param   string     $rule       密码
     * @param   array      $data       全部数据
     * @return  bool
     */
    protected function checkRepassWd($value, $rule, $data) {
        return $value === $rule ? true : false;
    }

}