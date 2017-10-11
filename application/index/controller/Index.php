<?php
/**
 * @category    app
 * @package     index
 * @subpackage  controller
 * @author      grey256
 * @version     2017/09/20 11:09:14
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\index\controller;

use think\Controller;
use think\Config;
use app\index\validate;
use think\Request;

/**
 * 首页
 */
class Index extends Controller {

    public function index() {
        $this->assign('meta_title', 'EasyX 树屋');
        return $this->fetch();
    }

}
