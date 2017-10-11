<?php
/**
 * @category    app
 * @package     usercore
 * @subpackage  model
 * @author      grey256
 * @version     2017/09/26 10:59:04
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\usercore\model;

use think\Model;

/**
 * 用户核心数据模型
 */
class UserToken extends Model {

    /**
     * 设置：表名
     */
    protected $table    = 'easy_user_token';

    /**
     * 设置：主键
     */
    protected $pk       = 'id';

    /**
     * 设置：只读字段
     */
    protected $readonly = ['uid'];

    /**
     * 设置：数据类型
     */
    protected $type     = [
        'id'          => 'integer',
        'uid'         => 'integer',
        'expire_time' => 'integer',
    ];



    /**
     * 用户信息核心操作：获取单个用户核心数据
     *
     * @author  grey256
     * @version 2017/09/22 19:24:14
     * @param   int     $intUid      用户ID
     * @return  array | bool         成功返回token，失败返回false；未找到则返回空数组
     */
    public static function getToken($intUid) {
        if (0 >= $intUid) {
            return false;
        }
        $objRet = self::get(['uid' => $intUid]);
        if ($objRet === false) {
            return false;
        }

        return is_null($objRet) ? array() : $objRet->toArray();
    }

    /**
     * 用户信息核心操作：根据用户ID列表获取用户核心数据
     *
     * @author  grey256
     * @version 2017/09/22 22:58:04
     * @param   int     $intUid        用户ID
     * @param   string  $strToken      token
     * @param   int     $intExpireTime 过期时间
     * @return  bool                   成功返回用户toke，失败返回false
     */
    public static function setToken($intUid, $strToken, $intExpireTime = 0) {
        $intUid = intval($intUid);
        if (0 >= $intUid || 0 >= strlen($strToken)) {
            return false;
        }
        $intExpireTime = 0 >= $intExpireTime ? time() + 604800 :intval($intExpireTime);
        $arrFields     = array(
            'token'       => $strToken,
            'expire_time' => $intExpireTime,
        );

        $objUserToken  = self::get(['uid' => $intUid]);
        if ($objUserToken === false) {
            return false;
        }
        if (empty($objUserToken)) {
            //创建用户token
            $arrFields['uid'] = $intUid;
            $objUserToken     = self::create($arrFields);
            $bolSet           = $objUserToken->id > 0 ? true : false;
        } else {
            //更新用户token
            $intSet = self::where('uid', $intUid)->update($arrFields);
            $bolSet = $intSet > 0 ? true : false;
        }

        return $bolSet;
    }

}