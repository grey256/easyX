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
 * �û���������ģ��
 */
class UserInfo extends Model {

    /**
     * ���ã�����
     */
    protected $table    = 'easy_userinfo';

    /**
     * ���ã�����
     */
    protected $pk       = 'uid';

    /**
     * ���ã�ֻ���ֶ�
     */
    protected $readonly = ['uid'];

    /**
     * ���ã��Զ����
     */
    protected $auto     = ['update_time', 'last_login_ip', 'last_login_time',];

    /**
     * ���ã��Զ����
     */
    protected $insert   = ['status' => 0, 'reg_ip', 'reg_time',];

    /**
     * ���ã���������
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
     * �û���Ϣ���Ĳ�������ȡ�����û���������
     *
     * @author  grey256
     * @version 2017/09/22 19:24:14
     * @param   int     $intUid      �û�ID
     * @param   mixed   $mixInfo     ��ȡ���û����ԣ���ֵ������
     * @return  array | bool         �ɹ������û����ԣ�ʧ�ܷ���false��δ�ҵ��򷵻ؿ�����
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
     * �û���Ϣ���Ĳ����������û�ID�б��ȡ�û���������
     *
     * @author  grey256
     * @version 2017/09/22 22:58:04
     * @param   int     $intUid      �û�ID
     * @param   mixed   $mixInfo     ��ȡ���û����ԣ���ֵ������
     * @return  array | bool         �ɹ������û����ԣ�ʧ�ܷ���false��δ�ҵ����ؿ�����
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
     * �û���Ϣ���Ĳ����������û�����ȡ�û���������
     *
     * @author  grey256
     * @version 2017/09/23 15:34:19
     * @param   string  $strUserName �û���
     * @param   mixed   $mixInfo     ��ȡ���û����ԣ���ֵ������
     * @return  array | bool         �ɹ������û����ԣ�ʧ�ܷ���false��δ�ҵ��򷵻ؿ�����
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
     * �û���Ϣ���Ĳ����������û�
     *
     * @author  grey256
     * @version 2017/09/23 07:57:51
     * @param   array   $arrFields   �û�����
     * @return  int | bool           �ɹ����������û�ID��ʧ�ܷ���false
     */
    public static function addUser($arrFields) {
        if (empty($arrFields) || !is_array($arrFields) || isset($arrFields['uid'])) {
            return false;
        }

        //��ʽ����������
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

        //Ϊ�յ��ֶβ����
        foreach ($arrData as $strKey => $mixItem) {
            if (is_null($mixItem)) {
                unset($arrData[$strKey]);
            }
        }

        //�����û�
        $objUser = self::create($arrData);
        $intUid  = $objUser->uid;
        if (!$intUid > 0) {
            return false;
        }

        return $intUid;
    }

    /**
     * �û���Ϣ���Ĳ����������û���Ϣ
     *
     * @author  grey256
     * @version 2017/09/26 10:07:14
     * @param   int     $intUid      �û�ID
     * @param   array   $arrFields   �û�����
     * @return  bool                 �ɹ����������û�true��ʧ�ܷ���false
     */
    public static function updateUserAttrById($intUid, $arrFields = null) {
        if (0 >= $intUid) {
            return false;
        }

        $arrData = array();
        if (!empty($arrFields) && is_array($arrFields)) {
            //��ʽ����������
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

            //Ϊ�յ��ֶβ����
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
     * ��ѯ��Χ��ȫ�ֲ�ѯ
     *
     * @author  grey256
     * @version 2017/09/24 05:17:45
     * @param   object     $query     ģ�Ͷ���
     * @return  void
     */
    protected function base($query)
    {
        $query->where('status', 0);
    }

    /**
     * �Զ���ɣ����ø���ʱ��
     *
     * @author  grey256
     * @version 2017/09/23 08:45:18
     * @param   void
     * @return  int                   ��ǰʱ��
     */
    protected function setUpdateTimeAttr() {
        return time();
    }

    /**
     * �Զ���ɣ���������½IP
     *
     * @author  grey256
     * @version 2017/09/23 08:45:52
     * @param   void
     * @return  string                ����IP
     */
    protected function setLastLoginIpAttr() {
        return request()->ip();
    }

    /**
     * �Զ���ɣ���������½ʱ��
     *
     * @author  grey256
     * @version 2017/09/23 08:46:09
     * @param   void
     * @return  int                   ��ǰʱ��
     */
    protected function setLastLoginTimeAttr() {
        return time();
    }

    /**
     * �Զ���ɣ�����ע��IP
     *
     * @author  grey256
     * @version 2017/09/23 08:46:22
     * @param   void
     * @return  string                ע��IP
     */
    protected function setRegIpAttr() {
        return request()->ip();
    }

    /**
     * �Զ���ɣ�����ע��IP
     *
     * @author  grey256
     * @version 2017/09/23 08:46:43
     * @param   void
     * @return  int                   ע��ʱ��
     */
    protected function setRegTimeAttr() {
        return time();
    }
}