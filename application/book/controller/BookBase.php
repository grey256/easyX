<?php
/**
 * @category    app
 * @package     book
 * @subpackage  controller
 * @author      grey256
 * @version     2017/10/11 14:33:25
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\book\controller;

use think\Controller;
use think\Config;
use app\book\validate;

/**
 * 书籍模块父类
 */
class BookBase extends Controller {

    /**
     * 声明：是否需要检查登录，未登录返回错误信息
     */
    protected $declareCheckLogin = false;

    /**
     * 声明：需要检查的权限；不需要检查则为false
     */
    protected $declarePrivilege  = false;

    /**
     * 声明：是否获取用户信息。默认为false。
     * 只有登录用户才能获取到用户信息
     */
    protected $declareUserInfo   = false;

    /**
     * 用户ID
     */
    protected $intUid   = 0;

    /**
     * 用户名
     */
    protected $strUname = '';

    /**
     * 初始化操作，加载配置文件
     *
     * @author  grey256
     * @version 2017/10/11 14:59:04
     * @param   void
     * @return  void
     */
    protected function _initialize() {
        try {
            echo 'base';
            $this->__declare();

            $this->_checkLogin();

            $this->_checkPrivilege();

            $this->_getUserInfo();

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * 声明变量
     *
     * @author  grey256
     * @version 2017/10/11 15:03:19
     * @param   void
     * @return  void
     */
    protected function __declare() {
        return;
    }

    /**
     * 登录检查
     *
     * @author  grey256
     * @version 2017/10/11 15:04:10
     * @param   void
     * @return  void
     */
    protected function _checkLogin() {
        if ($this->declareCheckLogin) {
            if (!is_login()) {
                throw new Exception('登录后才能查看页面', 10001);
            }
        }
    }

    /**
     * 权限检查
     *
     * @author  grey256
     * @version 2017/10/11 15:05:23
     * @param   void
     * @return  void
     */
    protected function _checkPrivilege() {
        if (!empty($this->declarePrivilege)) {
            //TODO:权限校验
            return;
        }
    }

    /**
     * 获取用户信息
     *
     * @author  grey256
     * @version 2017/10/11 15:05:49
     * @param   void
     * @return  void
     */
    protected function _getUserInfo() {
        if ($this->declareUserInfo) {
            //获取用户信息
            $arrUserInfo        = get_user_info();
            var_dump($arrUserInfo);exit;
            if (0 < $arrUserInfo['uid']) {
                $this->intUid      = intval($arrUserInfo['uid']);
                $this->strUname    = strval($arrUserInfo['username']);
            }
        }
    }

}