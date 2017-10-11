<?php
/**
 * @category    app
 * @package     usercenter
 * @subpackage  controller
 * @author      grey256
 * @version     2017/09/22 14:20:40
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\usercenter\controller;

use think\Controller;
use think\Config;
use app\usercenter\validate;
use app\usercore\model\UserInfo;
use app\usercore\model\UserToken;

/**
 * 用户操作
 */
class Member extends Controller {

    /**
     * 前置操作列表
     */
    protected $beforeActionList = [
        'isRegister'    => ['only' => 'register'],
        'checkRegister' => ['only' => 'register'],
        'isLogin'       => ['only' => 'login'],
        'checkLogin'    => ['only' => 'login'],
        'formatParams',
    ];

    /**
     * 配置信息
     */
    private $_arrConf = array();

    /**
     * 用户名
     */
    private $_strUserName = '';

    /**
     * 密码
     */
    private $_strPassword = '';

    /**
     * 是否需要展示模板
     */
    private $_bolShowView = false;



    /**
     * 初始化操作，加载配置文件
     *
     * @author  grey256
     * @version 2017/09/22 13:27:03
     * @param   void
     * @return  void
     */
    protected function _initialize() {
        $arrConf         = array();
        $arrConf['user'] = Config::get('user');
        if (!empty($arrConf) && is_array($arrConf)) {
            $this->_arrConf = $arrConf;
        }

        //获取通用参数
        $this->_strUserName = strval(input('post.username'));
        $this->_strPassword = strval(input('post.password'));
    }

    /**
     * 用户注册
     *
     * @author  grey256
     * @version 2017/09/21 19:32:14
     * @param   void
     * @return  void
     */
    public function register() {
        if ($this->_bolShowView) {
            //展示注册页面
            return $this->fetch();
        }
        //能走到这里就表示校验成功，可以写入用户注册信息
        $arrData    = array(
            'userName'      => $this->_strUserName,
            'password'      => $this->_strPassword,
        );

        $intUid = UserInfo::addUser($arrData);
        if (false === $intUid) {
            $this->error('注册失败！');
        }

        $this->success('恭喜你，注册成功！', 'index/Index/index');
    }

    /**
     * 用户登录
     *
     * @author  grey256
     * @version 2017/09/23 14:59:29
     * @param   void
     * @return  void
     */
    public function login() {
        if ($this->_bolShowView) {
            //展示登录页面
            return $this->fetch();
        }

        $arrUserInfo = UserInfo::getUserAttrByName($this->_strUserName, array('uid', 'ext_data'));
        $intUid      = $arrUserInfo['uid'];
        $arrExtData  = $arrUserInfo['ext_data'];

        //更新用户信息
        $intNow             = time();
        $arrExtData['exp']  = isset($arrExtData['exp']) && 0 < $arrExtData['exp'] ? $arrExtData['exp'] + 1 : 1;
        $arrData            = array(
            'lastLoginTime' => $intNow,
            'extData'        => $arrExtData,
        );
        $bolUpdate = UserInfo::updateUserAttrById($intUid, $arrData);
        if ($bolUpdate === false) {
            $this->error('系统繁忙，请稍候再试！');
        }

        //获取用户token
        $arrToken = UserToken::getToken($intUid);
        if (false === $arrToken) {
            $this->error('系统繁忙，请稍候再试！');
        }

        //数字签名
        $arrAuth  = array(
            'uid'             => $intUid,
            'username'        => $this->_strUserName,
            'last_login_time' => $intNow,
        );
        $strAuth = data_auth_sign($arrAuth);
        //设置session
        session('user_auth', $arrAuth);
        session('user_auth_sign', $strAuth);

        //记住登录状态
        $strToken      = '';
        $bolForce      = false;
        $intExpireTime = 0;
        if ($this->_isRemember || empty($arrToken) || strlen($arrToken['token']) !== 40 || $arrToken['expire_time'] - time() <= 0) {
            //生成token
            $strToken = $strAuth;
            //用户token不存在或过期，重置token
            $intExpireTime = 604800;
            $bolSet        = UserToken::setToken($intUid, $strToken, $intNow+$intExpireTime);
            if (false === $bolSet) {
                $this->error('系统繁忙，请稍候再试！');
            }
            //强制刷新cookie
            $bolForce = true;
        } else {
            $intExpireTime = 1800;
            $strToken      = $arrToken['token'];
        }

        if ($bolForce || !$this->getCookieUid()) {
            //设置cookie
            $intExpireTime = $bolForce ? 604800 : 1800;
            $strUk         = uid2uk($intUid);
            cookie('EASYX_USER', $this->_encrypt($this->_change() . ".{$strUk}.{$strToken}"), $intExpireTime);
        }

        $this->success('恭喜登录成功', 'index/Index/index');
    }

    /**
     * 前置操作：判断是提交注册信息还是展示注册页面
     *
     * @author  grey256
     * @version 2017/09/23 14:37:14
     * @param   void
     * @return  void
     */
    protected function isRegister() {
        if (input('?post.username') && input('?post.password')) {
            //用户注册提交，什么也不做
            return true;
        } else {
            $this->_bolShowView = true;
            //输出注册页面数据
            $this->assign('meta_title', 'EasyX | 注册');
        }
    }

    /**
     * 前置操作：校验注册信息
     *
     * @author  grey256
     * @version 2017/09/22 15:58:35
     * @param   void
     * @return  void
     */
    protected function checkRegister() {
        if (!$this->_arrConf['user']['REG_SWITCH']) {
            $this->error('抱歉，注册已关闭！');
        }
        if ($this->_bolShowView) {
            return true;
        }

        $arrRegInfo = array(
            'username' => $this->_strUserName,
            'password' => $this->_strPassword,
            'repasswd' => strval(input('post.repasswd')),
        );

        $objValidateUser = validate('user');
        $bolRet          = $objValidateUser->scene('register')->check($arrRegInfo);
        if (!$bolRet) {
            $this->error($objValidateUser->getError());
        }
    }

    /**
     * 前置操作：判断是提交登录信息还是展示登录页面
     *
     * @author  grey256
     * @version 2017/09/23 15:01:24
     * @param   void
     * @return  void
     */
    protected function isLogin() {
        if (!input('?post.username') || !input('?post.password')) {
            $intUid = is_login();
            if (0 >= $intUid) {
                $this->_bolShowView = true;
                //输出登录页面数据
                $this->assign('meta_title', 'EasyX | 登录');
            } else {
                $this->success('恭喜登录成功', 'index/Index/index');
            }
        }

        return true;
    }

    /**
     * 前置操作：校验登录信息
     *
     * @author  grey256
     * @version 2017/09/23 15:07:20
     * @param   void
     * @return  void
     */
    protected function checkLogin() {
        if ($this->_bolShowView) {
            return true;
        }

        $arrLoginInfo = array(
            'username' => $this->_strUserName,
            'password' => $this->_strPassword,
        );

        $this->_isRemember = input('post.remember') ? true : false;

        $objValidateUser = validate('user');
        $bolRet          = $objValidateUser->scene('login')->check($arrLoginInfo);
        if (!$bolRet) {
            $this->error($objValidateUser->getError());
        }
    }

    /**
     * 前置操作：格式化输入参数
     *
     * @author  grey256
     * @version 2017/09/22 16:09:11
     * @param   void
     * @return  void
     */
    protected function formatParams() {
        //对密码进行md5加密，便于存储到数据库中
        $this->_strPassword = md5($this->_arrConf['user']['ENCRYPT_PASSWD_PREFIX'] . $this->_strPassword);
    }

    /**
     * 获取cookie中的用户信息
     *
     * @author  grey256
     * @version 2017/09/26 14:03:15
     * @param   void
     * @return  int | bool        成功返回uid，失败返回false
     */
    protected function getCookieUid() {
        static $intCookieUid = null;
        if (isset($intCookieUid) && $intCookieUid !== null) {
            return $intCookieUid;
        }
        $strCookie    = cookie('EASYX_USER');
        if (strlen($strCookie) <= 0) {
            return false;
        }

        $arrCookie    = explode(".", $this->_decrypt($strCookie));
        $intUid       = uk2uid($arrCookie[1]);
        $arrToken     = UserToken::getToken($intUid);
        $intCookieUid = ($arrCookie[0] != $this->_change()) || ($arrCookie[2] != $arrToken['token']) ? false : $intUid;
        $intCookieUid = $arrToken['expire_time'] - time() <= 0 ? false : $intUid;

        return $intCookieUid;
    }

    /**
     * 加密函数
     *
     * @author  grey256
     * @version 2017/09/26 13:48:34
     * @param  string    $txt      待加密的字符串
     * @param  string    $key      加密密钥
     * @return string              加密后的字符串
     */
    private function _encrypt($txt, $key = null)
    {
        empty($key) && $key = $this->_change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $nh    = rand(0, 64);
        $ch    = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt   = base64_encode($txt);
        $tmp   = '';
        $i     = 0;
        $j     = 0;
        $k     = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return $ch . $tmp;
    }

    /**
     * 解密函数
     *
     * @author  grey256
     * @version 2017/09/26 13:50:51
     * @param  string     $txt     待解密的字符串
     * @param  string     $key     解密密钥
     * @return string              解密后的字符串
     */
    private function _decrypt($txt, $key = null)
    {
        empty($key) && $key = $this->_change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $ch    = $txt[0];
        $nh    = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt   = substr($txt, 1);
        $tmp   = '';
        $i     = 0;
        $j     = 0;
        $k     = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }

        return base64_decode($tmp);
    }

    /**
     * 密钥
     *
     * @author  grey256
     * @version 2017/09/26 13:53:20
     * @param   void
     * @return  string              密钥
     */
    private function _change() {
        preg_match_all('/\w/', $this->_arrConf['user']['DATA_AUTH_KEY'], $sss);
        $str1 = '';
        foreach ($sss[0] as $v) {
            $str1 .= $v;
        }

        return $str1;
    }
}