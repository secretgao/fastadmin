<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 示例接口
 */
class Content extends Api
{

    protected $noNeedLogin = ['news', 'cate', 'product', 'product_detail', 'news_detail', 'headimg','submit_message'];

    /**
     *
     * http://dev.fadmin.com/index.php/api/content/news
     * 返回
     * {"code":1,"msg":"返回成功","time":"1688736554","data":{"data":{"list":[{"title":"33","litetitle":"12","avatar":"123","detail":"<p>333545<\/p>"},{"title":"123","litetitle":"123","avatar":"123","detail":null},{"title":"1","litetitle":"1","avatar":"","detail":null}],"count":3,"page":1}}}
     * @author xiaoyu37@leju.com  7/4/23
     */
    public function news()
    {

        //分页列表
        $model = model('app\admin\model\content\News');

        $pageSize = 10;
        $page = $this->request->get("page");

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;
        //查询news
        $models = $model->where(['status' => 'normal']);

        $data = collection($models->order('weigh desc,id desc')->limit($offset, $limit)->field('id,title,litetitle,avatar,detail')->select())->toArray();
        //model被重置了
        $models = $model->where(['status' => 'normal']);
        $host = $_SERVER['HTTP_HOST'];
        if ($data) {
            foreach ($data as &$item) {
                $item['avatar'] = 'http://' . $host . $item['avatar'];
            }

        }
        $num = $models->count();
        $this->success('返回成功', ['list' => $data, 'count' => $num, 'page' => $page]);
    }

    /**
     * 详情
     * /api/content/news_detail
     */
    public function news_detail()
    {
        $model = model('app\admin\model\content\News');

        $id = $this->request->get("id");
        if ($this->request->isPost()) {
            $id = $this->request->post("id");
        }
        $models = $model->where(['status' => 'normal', 'id' => $id]);
        $data = $models->field('id,title,litetitle,avatar,detail')->find();


        $this->success('返回成功', $data ? $data->toArray() : []);
    }

    /**
     * 所有分类
     *
     * http://dev.fadmin.com/index.php/api/content/cate
     *
     * @author xiaoyu37@leju.com  7/4/23
     */
    public function cate()
    {

        $model = model('app\admin\model\content\Category');


        $data = collection($model->where(['status' => 'normal'])->order('weigh desc,id desc')->select())->toArray();

        $res = [];
        foreach ($data as $key => $val) {
            $res[] = ['id' => $val['id'], 'name' => $val['name']];
        }
        $this->success('返回成功', $res);
    }

    /**
     * 产品列表
     * @author xiaoyu37@leju.com  7/4/23
     */
    public function product()
    {
        $model = model('app\admin\model\content\Product');

        $cate = $this->request->get("cate");
        $pageSize = 10;
        $page = $this->request->get("page");

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;
        //查询news
        $models = $model->where(['status' => 'normal']);
        if ($cate) {
            $models->where(['cateid' => $cate]);
        }

        $data = collection($models->order('weigh desc,id desc')->limit($offset, $limit)->field('id,cateid,title,litetitle,avatar,detail')->select())->toArray();
        //model这里重置了
        $models = $model->where(['status' => 'normal']);
        if ($cate) {
            $models->where(['cateid' => $cate]);
        }
        $host = $_SERVER['HTTP_HOST'];
        if ($data) {
            foreach ($data as &$item) {
                $item['img'] = 'http://' . $host . $item['avatar'];
            }

        }
        $num = $models->count();
        $this->success('返回成功', ['list' => $data, 'count' => $num, 'page' => $page]);
    }

    /**
     * 详情
     * /api/content/product_detail
     * {"code":1,"msg":"返回成功","time":"1689682165","data":{"id":5,"cateid":1,"title":"1","litetitle":"1","avatar":"1","detail":"<p>3<\/p>","mes_avatar":"","mes":"33"}}
     */
    public function product_detail()
    {
        $model = model('app\admin\model\content\Product');

        $id = $this->request->get("id");
        if ($this->request->isPost()) {
            $id = $this->request->post("id");
        }
        $models = $model->where(['status' => 'normal', 'id' => $id]);
        $data = $models->field('id,cateid,title,litetitle,avatar,detail,mes_avatar,mes')->find();


        $this->success('返回成功', $data ? $data->toArray() : []);
    }

    /**
     *
     * http://dev.fadmin.com/index.php/api/content/headimg
     */
    public function headimg()
    {

        $model = model('Attachment');

        $data = collection($model->where(['category' => 'categoryHead'])->order('weigh desc,id desc')->field('url,filename')->select())->toArray();

        $host = $_SERVER['HTTP_HOST'];
        if ($data) {
            foreach ($data as &$item) {
                $item['url'] = 'http://' . $host . $item['url'];
            }
        }
        $this->success('返回成功', $data);
    }


    /**
     *
     * 最好加个频次限制 单个ip频繁提交
     * 也可以前端加个临时的限制
     *
     * var form = new FormData();
        form.append("name", "1");
        form.append("email", "1");
        form.append("message", "1");

        var settings = {
        "url": "http://dev.fadmin.com/index.php/api/content/submit_message",
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": form
        };

        $.ajax(settings).done(function (response) {
        console.log(response);
        });
     */
    public function submit_message()
    {

        $model = model('app\admin\model\content\Message');

        $name = $this->request->post("name");
        if (!$name) {
            $this->error(__('请填写完整信息'));
        }
        $email = $this->request->post("email");
        if (!$email) {
            $this->error(__('请填写完整信息'));
        }
        $message = $this->request->post("message");
        if (!$message) {
            $this->error(__('请填写完整信息'));
        }


        $result = $model->allowField(true)->save(['name' => $name, 'email' => $email, 'message' => $message]);
        $this->success('返回成功', $result);

    }
}
