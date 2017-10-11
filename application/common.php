<?php

/**
 * @category    app
 * @package     usercore
 * @author      grey256
 * @version     2017/09/26 10:25:15
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

/*
 * 应用公共文件
 */

/**
 * 公共函数：判断用户是否登录
 *
 * @author  grey256
 * @version 2017/10/11 14:52:31
 * @param   void
 * @return  int                  用户已登录则返回用户ID，否则返回0
 */
function is_login()
{
    $arrUserInfo = session('user_auth');
    if (empty($arrUserInfo)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($arrUserInfo) ? $arrUserInfo['uid'] : 0;
    }
}

/**
 * 公共函数：获取用户信息（从session中获取）
 *
 * @author  grey256
 * @version 2017/10/11 14:55:07
 * @param   void
 * @return  array                用户已登录则返回用户信息，否则返回null
 */
function get_user_info() {
    $arrUserInfo = session('user_auth');
    if (empty($arrUserInfo)) {
        return null;
    } else {
        return session('user_auth_sign') == data_auth_sign($arrUserInfo) ? $arrUserInfo : null;
    }
}

/**
 * 公共函数：uid转uk
 * uk作为系统对外提供的用户标识（唯一），禁止直接通过接口或模板输出uid
 * uk转uid参考：{@link uk2uid()}
 *
 * @author  grey256
 * @version 2017/09/26 10:28:42
 * @param   int     $intUid      用户ID
 * @return  string | bool        成功返回用户KEY，失败返回false
 */
function uid2uk($intUid) {
    $strChars = '0123456789abcdef';

    $arrValue   = array();
    $arrValue[] = $intUid  & 0x000000ff;
    $arrValue[] = ($intUid & 0x0000ff00) >> 8;
    $arrValue[] = ($intUid & 0x00ff0000) >> 16;
    $arrValue[] = ($intUid >> 24) & 0x000000ff;

    $strCode  = $strChars[$arrValue[0] >> 4] . $strChars[$arrValue[0] & 15];
    $strCode .= $strChars[$arrValue[1] >> 4] . $strChars[$arrValue[1] & 15];

    $strCode .= $strChars[$arrValue[2] >> 4] . $strChars[$arrValue[2] & 15];
    $strCode .= $strChars[$arrValue[3] >> 4] . $strChars[$arrValue[3] & 15];

    return $strCode;
}

/**
 * 公共函数：uk转uid
 * uid转uk参考：{@link uid2uk()}
 *
 * @author  grey256
 * @version 2017/09/26 10:28:42
 * @param   string  $strUk       用户KEY
 * @return  int | bool           成功返回用户ID，失败返回false
 */
function uk2uid($strUk) {
    $intLen = strlen($strUk);
    
    if($intLen < 8) {
        return false;
    }

    $intUid = hexdec($strUk[$intLen - 2] . $strUk[$intLen - 1]);
    $intUid = ($intUid << 8) + hexdec($strUk[$intLen - 4] . $strUk[$intLen - 3]);
    $intUid = ($intUid << 8) + hexdec($strUk[2] . $strUk[3]);
    $intUid = ($intUid << 8) + hexdec($strUk[0] . $strUk[1]);

    return $intUid;
}

/**
 * 公共函数：数据签名认证
 *
 * @author  grey256
 * @version 2017/09/26 10:48:36
 * @param   array   $data        被认证的数据
 * @return  string               签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $strCode = http_build_query($data); //url编码并生成query字符串
    $strSign = sha1($strCode); //生成签名

    return $strSign;
}
