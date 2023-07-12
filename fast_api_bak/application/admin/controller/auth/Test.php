<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;

/**
 * 角色组
 *
 * @icon   fa fa-group
 * @remark 角色组可以有多个,角色有上下级层级关系,如果子角色有角色组和管理员的权限则可以派生属于自己组别下级的角色组或管理员
 */
class Test extends Backend
{

    public function index()
    {
        var_dump(333);
    }

}