<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 示例接口
 */
class Content extends Api
{

    protected $noNeedLogin = ['news', 'cate', 'product', 'product_detail', 'news_detail', 'headimg'];

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
        $page = $this->request->post("page");

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;
        //查询news
        $models = $model->where(['status' => 'normal']);

        $data = collection($models->order('weigh desc,id desc')->limit($offset,$limit)->field('id,title,litetitle,avatar,detail')->select())->toArray();
        //model被重置了
        $models = $model->where(['status' => 'normal']);
        $host = $_SERVER['HTTP_HOST']; 
        if ($data){
          foreach($data as &$item){
                $item['avatar'] = 'http://'.$host.$item['avatar'];
          }

        } 
        $num = $models->count();
        $this->success('返回成功', ['list' => $data ,'count' => $num, 'page' => $page]);
    }

    /**
     * 详情
     * /api/content/news_detail
     */
    public function news_detail(){
        $model = model('app\admin\model\content\News');

        $id = $this->request->get("id");
        if($this->request->isPost()){
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

        $cate = $this->request->post("cate");
        $pageSize = 10;
        $page = $this->request->post("page");

        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;
        //查询news
        $models = $model->where(['status' => 'normal']);
        if($cate){
            $models->where(['cateid' => $cate]);
        }

        $data = collection($models->order('weigh desc,id desc')->limit($offset,$limit)->field('id,cateid,title,litetitle,avatar,detail')->select())->toArray();
        //model这里重置了
        $models = $model->where(['status' => 'normal']);
        if($cate){
            $models->where(['cateid' => $cate]);
        }
        $num = $models->count();
        $this->success('返回成功', ['list' => $data ,'count' => $num, 'page' => $page]);
    }

    /**
     * 详情
     * /api/content/product_detail
     */
    public function product_detail(){
        $model = model('app\admin\model\content\Product');

        $id = $this->request->get("id");
        if($this->request->isPost()){
            $id = $this->request->post("id");
        }
        $models = $model->where(['status' => 'normal', 'id' => $id]);
        $data = $models->field('id,cateid,title,litetitle,avatar,detail')->find();


        $this->success('返回成功', $data ? $data->toArray() : []);
    }

    /**
     *
     * http://dev.fadmin.com/index.php/api/content/headimg
     */
    public function headimg(){

        $model = model('Attachment');

        $data = collection($model->where(['category' => 'categoryHead'])->order('weigh desc,id desc')->field('url,filename')->select())->toArray();

        $host = $_SERVER['HTTP_HOST']; 
        if ($data){
          foreach($data as &$item){
                $item['url'] = 'http://'.$host.$item['url'];
          }
        } 
        $this->success('返回成功', $data);
    }
}
