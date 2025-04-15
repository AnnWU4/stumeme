<?php

namespace app\api\controller;

use app\common\controller\Api;


class Wechat extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['login'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';

    public function login(){
        $code = $this->request->param('code');
        $appid = 'wx254f716f458328b2';    // 小程序appid
        $secret = '6e94f4bf54e26550e3da949fff040554';    // 小程序AppSecret

        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $result = file_get_contents($url);
        $data = json_decode($result, true);
        if (isset($data['openid'])){
            // 判断是否有账号
            $userModel = (new \app\admin\model\User());
            $user = $userModel->where('openid', '=', $data['openid'])->find();
            if ($user){
                $this->success('ok', ['user_id' => $user['id']]);
            }else{
                $userId = $userModel->insertGetId([
                    'openid' => $data['openid'],
                    'createtime' => time()
                ]);
                $this->success('ok', ['user_id' => $userId]);
            }
        }
        $this->error('小程序授权出错');
    }

}