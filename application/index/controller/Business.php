<?php

namespace app\index\controller;

use addons\nft\model\Business as BusinessModel;
use app\common\controller\Frontend;
use think\Validate;

class Business extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'business';

    public function index()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = input('post.row/a');
            if (!Validate::regex($params['mobile'], "^1\d{10}$")) {
                $this->error(__('手机格式不正确'));
            }
            $rule = [
                'account' => 'require|length:3,50',
                'people' => 'require',
                'mobile' => 'require',
                'email' => 'require|email',
                'ip' => 'require',
                'ip_name' => 'require',
                'source' => 'require',
            ];

            $msg = [
                'account.require' => 'Account can not be empty',
                'account.length' => 'Account must be 3 to 50 characters',
                'people.require' => 'Password can not be empty',
                'mobile.require' => '请填写联系方式(手机)',
                'email.require' => '请填写电子邮箱',
                'ip.require' => '请选择ip类型',
                'ip_name.require' => '请填写ip名称',
                'source.require' => '请选择来源',
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($params);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return false;
            }
            if (is_array($params['ip_platform'])) {
                $params['ip_platform'] = implode(',', $params['ip_platform']);
            }
            model(BusinessModel::class)->allowField(true)->data($params)->save();
            $this->success(__('感谢参加'), url('business/index'));
        }
        $config = get_addon_config('nft');
        $remind = $config['ini']['remind'] ?? '注意:请确认所有作品必须是您原创或代理,并且您提交的内容不存在侵权或未经授权的受版权保护的素材.';
        $this->view->assign('remind', $remind);
        $ip_type = [
            '博物馆' => '博物馆',
            '非遗' => '非遗',
            '景区' => '景区',
            '艺术' => '艺术',
            '体育' => '体育',
            '娱乐' => '娱乐',
            '音乐' => '音乐',
            '潮玩' => '潮玩',
            '动画.漫画.电子游戏' => '动画.漫画.电子游戏',
            '品牌' => '品牌',
            '其它' => '其它'
        ];
        $this->view->assign('ip_type', $ip_type);
        $ip_platform = [
            '微博' => '微博',
            '公众号' => '公众号',
            '微信视频号' => '微信视频号',
            '抖音' => '抖音',
            '快手' => '快手',
            'Twitter' => 'Twitter',
            'instagram' => 'instagram',
            'TiKTok' => 'TiKTok',
            '其它' => '其它'
        ];
        $this->view->assign('ip_platform', $ip_platform);

        $ip_relation = [
            '权利人' => '权利人',
            '被许可人/被授权方' => '被许可人/被授权方',
            'IP全权代理商' => 'IP全权代理商',
            '其它' => '其它',
        ];
        $this->view->assign('ip_relation', $ip_relation);

        $other = [
            '微信群.QQ群' => '微信群.QQ群',
            '朋友推荐' => '朋友推荐',
            '其它' => '其它'
        ];
        $this->view->assign('other', $other);
        //判断来源
        $this->view->assign('url', url('business/index'));
        $this->view->assign('title', '商务合作信息');
        return $this->view->fetch();
    }

}
