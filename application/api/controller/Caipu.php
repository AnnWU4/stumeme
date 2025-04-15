<?php

namespace app\api\controller;

use app\admin\model\Like;
use app\common\controller\Api;

class Caipu extends Api
{
    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['lists', 'detail', 'send', 'like', 'likeList', 'getColor', 'delete', 'update'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';

    /**
     * 菜谱列表
     */
    public function lists(){
        // type:1=菜谱列表，2=我发布的菜谱，3=随机
        $type = $this->request->param('type', 1);
        $userId = $this->request->param('user_id', 0);

        $caipuModel = new \app\admin\model\Caipu();

        switch ($type){
            case 2:
                $list = $caipuModel->where('user_id', '=', $userId)->order('id desc')->select();
                break;
            case 3:
                $list = $caipuModel->where('status', '=', 1)->orderRaw('rand()')->limit(6)->select();
                break;
            default:
                $list = $caipuModel->where('status', '=', 1)->order('id desc')->select();
        }
        $data = array();
        foreach ($list as &$item){
            $item['avatar'] = cdnurl($item['avatar'], true);
            $item['image'] = cdnurl($item['image'], true);
            array_push($data, $item);
        }
        $this->success('OK', $data);
    }

    /**
     * 菜谱详情
     */
    public function detail(){
        $id = $this->request->param('id/d', 0);
        $user_id = $this->request->param('user_id/d', 0);

        if (!$id){
            $this->error('数据不存在');
        }
        $caipuModel = new \app\admin\model\Caipu();

        $row = $caipuModel->where('id', '=', $id)->find();

        if ($row){
            $row['avatar'] = cdnurl($row['avatar'], true);
            $row['image'] = cdnurl($row['image'], true);
            // 收藏
            $row['is_like'] = 0;
            $like = Like::where('user_id', '=', $user_id)->where('caipu_id', '=', $id)->find();
            if ($like){
                $row['is_like'] = 1;
            }
            $this->success('OK', $row);
        }
        $this->error('数据不存在');
    }

    public function delete(){
        $id = $this->request->param('id/d', 0);
        $caipuModel = new \app\admin\model\Caipu();

        $res = $caipuModel->where('id', '=', $id)->delete();

        $this->success('删除成功');
    }


    /**
     * 收藏
     */
    public function like(){
        $userId = $this->request->param('user_id/d', 0);
        $id = $this->request->param('id/d', 0);
        $is_like = $this->request->param('is_like/d', 0);
        $likeModel = new \app\admin\model\Like();
        if ($is_like){
            $row = $likeModel->where('user_id', '=', $userId)->where('caipu_id', '=', $id)->delete();
            if ($row){
                $this->error('已取消收藏');
            }
        }else{

            $likeModel->allowField(true)->save([
                'user_id' => $userId,
                'caipu_id' => $id,
                'createtime' => time(),
            ]);
            $this->success('收藏成功');
        }

    }

    /**
     * 发布出餐
     */
    public function send(){

        $id = $this->request->param('id', 0);
        $user_id = $this->request->param('user_id', 0);
        $content = $this->request->param('content', '');
        $image = $this->request->param('image', '');

        if (!$user_id){
            $this->error('未登录');
        }
        if (!$content){
            $this->error('文字不能为空');
        }
        if (!$image){
            $this->error('图片不能为空');
        }

        $user = \app\admin\model\User::get($user_id);
        $caipuModel = new \app\admin\model\Caipu();
        if ($id){
            $row = $caipuModel->where('id', '=', $id)->find();
            $row->allowField(true)->save([
                'user_id' => $user_id,
                'nickname' => $user['nickname'],
                'avatar' => $user['avatar'],
                'content' => $content,
                'image' => $image,
                'status' => 0,
                'reason' => null
            ]);
        }else{
            $caipuModel->allowField(true)->save([
                'user_id' => $user_id,
                'nickname' => $user['nickname'],
                'avatar' => $user['avatar'],
                'content' => $content,
                'image' => $image,
                'createtime' => time(),
            ]);
        }
        $this->success('OK');
    }

    public function likeList(){

        $user_id = $this->request->param('user_id', 0);

        $likeIds = Like::where('user_id', '=', $user_id)->order('id desc')->column('caipu_id', 'id');

        $list = \app\admin\model\Caipu::where('id', 'in', $likeIds)->select();

        $data = array();
        foreach ($list as &$item){
            $item['avatar'] = cdnurl($item['avatar'], true);
            $item['image'] = cdnurl($item['image'], true);
            array_push($data, $item);
        }
        $this->success('ok', $data);
    }

    public function getColor(){
        $color = ['#000000', '#ffffff', 'rgb(255, 0, 0)', 'rgb(0, 255, 0)',
            'rgb(0, 0, 255)', '#3d2c2c', '#8d3d3d', '#214acf',
            '#9c2626', '#d7da27', '#14f127', '#5d0ae2'];

        $this->success('OK', $color);
    }

}