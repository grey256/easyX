<?php
/**
 * @category    app
 * @package     book
 * @subpackage  controller
 * @author      grey256
 * @version     2017/10/11 15:23:30
 * @copyright   Copyright 2017 by grey256. All Rights Reserved.
 **/

namespace app\book\controller;

use think\Controller;
use think\Config;
use app\book\validate;

/**
 * 我读
 */
class Read extends BookBase {

    protected function __declare() {
        $this->declareUserInfo = true;
    }

    public function index() {
        echo $this->intUid;
    }

}