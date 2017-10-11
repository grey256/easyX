<?php
/**
 * @category    app
 * @package     usercore
 * @subpackage  model
 * @author      grey256
 * @version     2017/09/22 19:06:01
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\usercore\model;

use think\Model;

/**
 * 用户核心数据模型
 */
class UserInfo extends Model {

    /**
     * 设置：表名
     */
    protected $table    = 'easy_userinfo';

    /**
     * 设置：主键
     */
    protected $pk       = 'uid';

    /**
     * 设置：只读字段
     */
    protected $readonly = ['uid'];

    /**
     * 设置：自动完成
     */
    protected $auto     = ['update_time', 'last_login_ip', 'last_login_time',];

    /**
     * 设置：自动完成
     */
    protected $insert   = ['status' => 0, 'reg_ip', 'reg_time',];

    /**
     * 设置：数据类型
     */
    protected $type     = [
        'uid'             => 'integer',
        'reg_time'        => 'integer',
        'last_login_time' => 'integer',
        'update_time'     => 'integer',
        'status'          => 'integer',
        'ext_data'        => 'array',
    ];



    /**
     * 用户信息核心操作：获取单个用户核心数据
     *
     * @author  grey256
     * @version 2017/09/22 19:24:14
     * @param   int     $intUid      用户ID
     * @param   mixed   $mixInfo     获取的用户属性，单值或数组
     * @return  array | bool         成功返回用户属性，失败返回false；未找到则返回空数组
     */
    public static function getUserAttr($intUid, $mixInfo = null) {
        $strFields = null;
        if (!empty($mixInfo)) {
            $strFields = is_array($mixInfo) ? implode(',', $mixInfo) : $mixInfo;
        }

        $arrRet = self::where('uid', $intUid)->column($strFields);

        return $arrRet;
    }

    /**
     * 用户信息核心操作：根据用户ID列表获取用户核心数据
     *
     * @author  grey256
     * @version 2017/09/22 22:58:04
     * @param   int     $intUid      用户ID
     * @param   mixed   $mixInfo     获取的用户属性，单值或数组
     * @return  array | bool         成功返回用户属性，失败返回false；未找到返回空数组
     */
    public static function getUserAttrByIds($arrIds, $mixInfo = null) {
        if (!is_array($arrIds)) {
            return self::getUserAttr($arrIds);
        }

        $strFields = null;
        if (!empty($mixInfo)) {
            $strFields = is_array($mixInfo) ? implode(',', $mixInfo) : strval($mixInfo);
        }

        $arrRet = self::where('uid', 'in', $arrIds)->column($strFields);

        return $arrRet;
    }

    /**
     * 用户信息核心操作：根据用户名获取用户核心数据
     *
     * @author  grey256
     * @version 2017/09/23 15:34:19
     * @param   string  $strUserName 用户名
     * @param   mixed   $mixInfo     获取的用户属性，单值或数组
     * @return  array | bool         成功返回用户属性，失败返回false；未找到则返回空数组
     */
    public static function getUserAttrByName($strUserName, $mixInfo = null) {
        if (empty($strUserName) || !is_string($strUserName)) {
            return false;
        }

        $strFields = null;
        if (!empty($mixInfo)) {
            $strFields = is_array($mixInfo) ? implode(',', $mixInfo) : strval($mixInfo);
        }

        $arrRet = self::where('username', $strUserName)->field($strFields)->find();

        return $arrRet->toArray();
    }

    /**
     * 用户信息核心操作：新增用户
     *
     * @author  grey256
     * @version 2017/09/23 07:57:51
     * @param   array   $arrFields   用户属性
     * @return  int | bool           成功返回新增用户ID，失败返回false
     */
    public static function addUser($arrFields) {
        if (empty($arrFields) || !is_array($arrFields) || isset($arrFields['uid'])) {
            return false;
        }

        //格式化输入数据
        $arrData = array(
            'username'        => isset($arrFields['userName']) && strlen($arrFields['userName']) > 0 ? strval($arrFields['userName']) : null,
            'password'        => isset($arrFields['password']) && strlen($arrFields['password']) > 0 ? strval($arrFields['password']) : null,
            'reg_time'        => isset($arrFields['regTime']) && $arrFields['regTime'] > 0 ? intval($arrFields['regTime']) : null,
            'reg_ip'           => isset($arrFields['regIp']) && strlen($arrFields['regIp']) > 0 ? strval($arrFields['regIp']) : null,
            'last_login_time' => isset($arrFields['lastLoginTime']) && $arrFields['lastLoginTime'] > 0 ? intval($arrFields['lastLoginTime']) : null,
            'last_login_ip'   => isset($arrFields['lastLoginIp']) && strlen($arrFields['lastLoginIp']) > 0 ? strval($arrFields['lastLoginIp']) : null,
            'update_time'     => isset($arrFields['updateTime']) && $arrFields['updateTime'] > 0 ? intval($arrFields['updateTime']) : null,
            'status'          => isset($arrFields['status']) && $arrFields['status'] > 0 ? intval($arrFields['status']) : null,
            'ext_data'         => isset($arrFields['extData']) && !empty($arrFields['extData']) ? json_encode($arrFields['extData']) : null,
        );

        //为空的字段不添加
        foreach ($arrData as $strKey => $mixItem) {
            if (is_null($mixItem)) {
                unset($arrData[$strKey]);
            }
        }

        //新增用户
        $objUser = self::create($arrData);
        $intUid  = $objUser->uid;
        if (!$intUid > 0) {
            return false;
        }

        return $intUid;
    }

    /**
     * 用户信息核心操作：更新用户信息
     *
     * @author  grey256
     * @version 2017/09/26 10:07:14
     * @param   int     $intUid      用户ID
     * @param   array   $arrFields   用户属性
     * @return  bool                 成功返回新增用户true，失败返回false
     */
    public static function updateUserAttrById($intUid, $arrFields = null) {
        if (0 >= $intUid) {
            return false;
        }

        $arrData = array();
        if (!empty($arrFields) && is_array($arrFields)) {
            //格式化输入数据
            $arrData = array(
                'username'        => isset($arrFields['userName']) && strlen($arrFields['userName']) > 0 ? strval($arrFields['userName']) : null,
                'password'        => isset($arrFields['password']) && strlen($arrFields['password']) > 0 ? strval($arrFields['password']) : null,
                'reg_time'        => isset($arrFields['regTime']) && $arrFields['regTime'] > 0 ? intval($arrFields['regTime']) : null,
                'reg_ip'           => isset($arrFields['regIp']) && strlen($arrFields['regIp']) > 0 ? strval($arrFields['regIp']) : null,
                'last_login_time' => isset($arrFields['lastLoginTime']) && $arrFields['lastLoginTime'] > 0 ? intval($arrFields['lastLoginTime']) : null,
                'last_login_ip'   => isset($arrFields['lastLoginIp']) && strlen($arrFields['lastLoginIp']) > 0 ? strval($arrFields['lastLoginIp']) : null,
                'update_time'     => isset($arrFields['updateTime']) && $arrFields['updateTime'] > 0 ? intval($arrFields['updateTime']) : null,
                'status'          => isset($arrFields['status']) && $arrFields['status'] > 0 ? intval($arrFields['status']) : null,
                'ext_data'         => isset($arrFields['extData']) && !empty($arrFields['extData']) ? json_encode($arrFields['extData']) : null,
            );

            //为空的字段不添加
            foreach ($arrData as $strKey => $mixItem) {
                if (is_null($mixItem)) {
                    unset($arrData[$strKey]);
                }
            }
        }

        $intUpdate = self::where('uid', $intUid)->update($arrData);

        return $intUpdate > 0 ? true : false;
    }

    /**
     * 查询范围：全局查询
     *
     * @author  grey256
     * @version 2017/09/24 05:17:45
     * @param   object     $query     模型对象
     * @return  void
     */
    protected function base($query)
    {
        $query->where('status', 0);
    }

    /**
     * 自动完成：设置更新时间
     *
     * @author  grey256
     * @version 2017/09/23 08:45:18
     * @param   void
     * @return  int                   当前时间
     */
    protected function setUpdateTimeAttr() {
        return time();
    }

    /**
     * 自动完成：设置最后登陆IP
     *
     * @author  grey256
     * @version 2017/09/23 08:45:52
     * @param   void
     * @return  string                请求IP
     */
    protected function setLastLoginIpAttr() {
        return request()->ip();
    }

    /**
     * 自动完成：设置最后登陆时间
     *
     * @author  grey256
     * @version 2017/09/23 08:46:09
     * @param   void
     * @return  int                   当前时间
     */
    protected function setLastLoginTimeAttr() {
        return time();
    }

    /**
     * 自动完成：设置注册IP
     *
     * @author  grey256
     * @version 2017/09/23 08:46:22
     * @param   void
     * @return  string                注册IP
     */
    protected function setRegIpAttr() {
        return request()->ip();
    }

    /**
     * 自动完成：设置注册IP
     *
     * @author  grey256
     * @version 2017/09/23 08:46:43
     * @param   void
     * @return  int                   注册时间
     */
    protected function setRegTimeAttr() {
        return time();
    }
}