<?php

namespace app\admin\controller\content;

use app\admin\model\AuthGroup;
use app\admin\model\content\Product;
use app\common\controller\Backend;
use think\Db;

/**
 * 分类商品详情
 *
 * @icon fa fa-user
 */
class Detail extends Backend
{

    /**
     * @var \app\admin\model\Admin
     */
    protected $model = null;
    protected $selectpageFields = 'id,username,nickname,avatar';
    protected $searchFields = 'id,username,nickname';
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\admin\model\content\Product');

        $allcategorymod = model('app\admin\model\content\Category');

        $result = collection($allcategorymod->select())->toArray();
        $allcategory = [];
        foreach ($result as $k => $v) {
            $allcategory[$v['id']] = $v['name'];
        }
        $this->view->assign('allcategory', $allcategory);

    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);

        //
        $cateModel = model('app\admin\model\content\Category');
        $cate = collection($cateModel->select())->toArray();
        $cates = [];
        foreach ($cate as $k => $v){
            $cates[$v['id']] = $v['name'];
        }

        $this->assignconfig('cates', $cates);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                //->field(['password', 'salt', 'token'], true)
                ->order($sort, $order)
                ->paginate($limit);


            foreach ($list as $k => &$v) {
                if($v['cateid']){
                    $v['cateid'] = $cateModel->where(['id' => $v['cateid']])->value('name');
                }else{
                    $v['cateid'] = '';
                }
//                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
//                $v['groups'] = implode(',', array_keys($groups));
//                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();


    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        //$this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        return parent::edit($ids);
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->delete();
        $this->success();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 下拉搜索
     */
    public function selectpage()
    {
        $this->dataLimit = 'auth';
        $this->dataLimitField = 'id';
        return parent::selectpage();
    }
}