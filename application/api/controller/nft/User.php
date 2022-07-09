<?php

namespace app\api\controller\nft;

use addons\nft\model\AirDrop;
use addons\nft\model\Alipay;
use addons\nft\model\Identify;
use addons\nft\model\UserCollection;
use addons\nft\model\UserCollectionGiveLog;
use addons\nft\model\UserCollectionLog;
use addons\nft\model\UserFriend;
use addons\nft\model\UserMessage;
use app\common\controller\NftApi;
use app\common\library\Ems;
use app\common\library\Safrvcert;
use app\common\library\Sms;
use app\common\service\nft\PayService;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Validate;

/**
 * 会员接口
 */
class User extends NftApi
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

        Hook::add('user_register_successed', function ($user) {
            //新注册用户 需把有效通知发送发用户
            $list = \app\admin\model\nft\article\Notice::where('status', 'normal')->field('id')->select();
            $dataSet = [];
            foreach ($list as $item) {
                $dataSet[] = [
                    'user_id' => $user->id,
                    'link_id' => $item,
                    'status' => 1
                ];
            }
            model(UserMessage::class)->saveAll($dataSet);
        });

        //实名后事件
        Hook::add('safrv_auth', function ($user) {
            //邀请活动添加 抽奖次数
            if (!empty($user->activity)) {
                $user->activity->setInc('num', 1);
            } else {
                $user->activity()->save([
                    'user_id' => $user->id,
                    'num' => 1
                ]);
            }

            $config = get_addon_config('nft');
            if (!empty($config['marketing']['badge_no'])) {
                //邀请活动添加 徽章升级次数
                if (!empty($user->activitybadge)) {
                    $user->activitybadge->setInc('num', 1);
                } else {
                    $user->activitybadge()->save([
                        'user_id' => $user->id,
                        'num' => 1,
                        'type' => 'badge'
                    ]);
                }
                //送出基础徽章
                if (!empty($config['marketing']['base_badge_id'])) {
                    $key = 'collection_' . $config['marketing']['base_badge_id'];
                    if (PayService::checkoutStock($key, 1)) {
                        UserCollection::addLog($config['marketing']['base_badge_id'], $this->auth->id);
                    }
                }
            }
        });
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    public function refresh()
    {
        $this->success('', $this->auth->getUserinfo());
    }

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->post('account');
        $password = $this->request->post('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        $p_id = $this->request->post('qid', 0);
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, ['p_id' => $p_id]);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param string $code 验证码
     */
    public function register()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $email = $this->request->post('email');
        $mobile = $this->request->post('mobile');
        $code = $this->request->post('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @ApiMethod (POST)
     * @param string $avatar 头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio 个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        unset($user['identify_status'], $user['message_status'], $user['user_md5_link']);
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        if ($nickname) {
            $exists = \app\common\model\User::where('nickname', $nickname)
                ->where('id', '<>', $this->auth->id)
                ->find();
            if ($exists) {
                $this->error(__('Nickname already exists'));
            }
            $user->nickname = $nickname;
        }
        if (!empty($avatar)) {
            $user->avatar = $avatar;
        }
        $user->bio = $bio;
        $user->allowField(true)->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @param string $email 邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @param string $platform 平台名称
     * @param string $code Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->post("platform");
        $code = $this->request->post("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo' => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 绑定支付宝
     *
     * @param string $mobile 手机号
     * @param string $name 姓名
     * @param string $alipayNumber 支付宝账号
     * @param string $captcha 验证码--获取验证码时事件event值为:bindAlipay
     *
     * @throws \think\exception\DbException
     */
    public function bindAlipay()
    {
        $name = $this->request->post('name');
        $aliNumber = $this->request->post('alipayNumber');
        $mobile = $this->request->post('mobile');
        $smsCode = $this->request->post('captcha');

        if (empty($name) || mb_strlen($name) > 30 || preg_match('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/',
                $name)) {
            $this->error('请输入正确的姓名');
        }
        if (!Validate::regex($aliNumber, "^1\d{10}$") && !Validate::is($aliNumber, "email")) {
            $this->error(__('请填写正确的支付宝账号'));
        }


//        if (!Validate::regex($mobile, "^1\d{10}$")) {
//            $this->error(__('Mobile is incorrect'));
//        }
//
//        if (!Sms::check($mobile, $smsCode, 'bindAlipay')) {
//            $this->error(__('Captcha is incorrect'));
//        }

        Db::startTrans();
        try {
            $alipay = Alipay::get(['user_id' => $this->auth->id]);
            if (empty($alipay)) {
                $alipay = new Alipay();
                $alipay->insert([
                    'user_id' => $this->auth->id,
                    'name' => $name,
                    'account' => $aliNumber,
                ]);
            } else {
                $alipay->name = $name;
                $alipay->account = $aliNumber;
                $alipay->save();
            }
            Db::commit();
        } catch (Exception$e) {
            Db::rollback();
            $this->error('绑定失败');
        }

        $this->success('绑定成功');
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $type = $this->request->post("type", 'mobile');
        $mobile = $this->request->post("mobile");
        $email = $this->request->post("email");
        $newpassword = $this->request->post("newpassword");
        $captcha = $this->request->post("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        //验证Token
        if (!Validate::make()->check(['newpassword' => $newpassword], ['newpassword' => 'require|regex:\S{6,30}'])) {
            $this->error(__('Password must be 6 to 30 characters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 实名认证
     */
    public function safrv_auth()
    {
        $identifyNum = input('identifyNum', '');
        $userName = input('userName', '');
        if (!$identifyNum || !$userName) {
            $this->error(__('请输入需要的信息'));
        }
        if (!Validate::regex($identifyNum, "/^[1-9]\d{5}(?:18|19|20)\d{2}(?:0\d|10|11|12)(?:0[1-9]|[1-2]\d|30|31)\d{3}[\dXx]$/")) {
            $this->error(__('请输入正确的身份证号'));
        }
        $server = Safrvcert::instance();
        $res = $server->setIdentifyNum($identifyNum)->setUserName($userName)->auth();
        if ($res) {
            //认证成功 生成用户的hash_math 标识
            $ret = $this->auth->createIdentify($identifyNum, $userName);
            if ($ret) {
                $friend = UserFriend::where('user_id', $this->auth->id)->find();
                if (!empty($friend)) {
                    $p_user = \addons\nft\model\User::where('id', $friend->pid)->find();
                    Hook::listen("safrv_auth", $p_user);
                }
                $this->success('认证成功');
            } else {
                $this->error($this->auth->getError());
            }
        }
        $this->error($server->getError());
    }

    /**
     * 用户消息
     * @throws \think\exception\DbException
     */
    public function message()
    {
        $ids = [];
        $list = model(UserMessage::class)->where('user_id', $this->auth->id)
            ->field('id,link_id,status,is_view,createtime')
            ->where('status', 1)
            ->order('createtime', 'desc')
            ->paginate(input('limit', 10))->each(function ($item) use (&$ids) {
                $item->append(['message', 'createtime_text']);
                $ids[] = $item->id;
                return $item;
            });
        model(UserMessage::class)->where('id', 'in', $ids)->update(['is_view' => 1]);
        $this->success('获取成功', $list);
    }

    /**
     * 空投信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function air_drop()
    {
        $config = get_addon_config('nft');
        $config = $config['marketing'];
        $ids = [];
        if (is_string($config['air_drop'])) {
            $ids = explode(',', $config['air_drop']);
        }
        $article = \addons\nft\model\Article::where('id', 'in', $ids)
            ->where('status', '1')
            ->field('id,title,content')->select();
        $air_count = AirDrop::where('user_id', $this->auth->id)->where('limit_time', '>', time())->where('state', 0)->count();
        $this->success('获取成功', ['air_drop' => $article, 'user' => $air_count]);
    }

    public function my_air_drop()
    {
        $list = AirDrop::with('collection')
            ->where('user_id', $this->auth->id)
            ->where('state', 0)
            ->where('limit_time', '>', time())
            ->select();
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'collection_id' => $item->collection_id,
                'limit_time_text' => $item->limit_time_text,
                'limit_time' => bcsub($item->limit_time, time()),
                'collection' => [
                    'title' => $item->collection->title,
                    'image' => cdnurl($item->collection->image, true),
                ]
            ];
        }
        $this->success('获取成功', $data);
    }

    public function my_friend()
    {
        $friend = UserFriend::where('pid', $this->auth->id)->column('user_id');
        $list = \addons\nft\model\User::hasWhere('identify')
            ->where('User.id', 'in', $friend)
            ->select();
        $people = [];
        foreach ($list as $item) {
            $people[] = [
                'nickname' => $item->nickname,
                'avatar' => $item->avatar,
                'mobile' => $item->mobile,
            ];
        }
        $this->success('获取成功', $people);
    }

    /**
     * 我的藏品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function my_collection_list()
    {
        $list = UserCollection::where('user_id', $this->auth->id)->select();
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'image' => cdnurl($item->image, true),
                'title' => $item->title,
                'author' => [
                    'avatar' => '',
                    'name' => $item->author
                ],
                'link_md5' => $item['tokenId'],
                'user' => $item['owner'],
                'user_collection_id' => $item->id,
                'type' => \addons\nft\model\Collection::where('id', $item->collection_id)->value('type')
            ];
        }
        $this->success('获取成功', ['list' => $data, 'total' => count($list)]);
    }


    /**
     * 藏品详情
     *
     * @param string|null $link_md5 藏品标识
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function my_collection_detail($link_md5 = null)
    {
        $config = get_addon_config('nft');
        $info = UserCollection::where('tokenId', $link_md5)->where('user_id', $this->auth->id)->find();
        if (empty($info)) {
            $this->error('未拥有藏品');

        }
        $give_limit_time = bcadd($info['createtime'], (3600 * ($config['ini']['make_give'] ?? 0)));
        $give_status = 1;
        $prescription = 0;
        if ($give_limit_time > time()) {
            $give_status = 0;
            $prescription = bcsub($give_limit_time, time());
        }
        //收藏时间
        $data = [
            'image' => cdnurl($info->image, true),
            'master_image' => cdnurl($info->image, true),
            'title' => $info->title,
            'author' => [
                'avatar' => '',
                'name' => $info->author
            ],
            'link_md5' => $info['tokenId'],
            'no' => $info['no'],
            'user' => $info['owner'],
            'give_status' => $give_status,
            'give_limit_time' => $info['updatetime'],
            'prescription' => $prescription,
            'collection_log' => UserCollectionLog::where('tokenId', $link_md5)->order('id asc')->select()
        ];
        $this->success('获取成功', $data);
    }

    /**
     * 转赠记录
     * @throws \think\exception\DbException
     */
    public function my_give_live()
    {
        $list = UserCollectionGiveLog::with('usercollection')->where('user_id', $this->auth->id)->paginate(input('limit', 10));
        $item_list = [];
        if ($list->count() > 0) {
            $items = $list->items();
            foreach ($items as $item) {
                $item_list[] = [
                    'image' => cdnurl($item->usercollection->image, true),
                    'title' => $item->usercollection->title,
                    'give_time' => date('Y-m-d H:i:s', $item->createtime)
                ];
            }
        }

        $data = [
            'total' => $list->total(),
            'per_page' => $list->listRows(),
            'current_page' => $list->currentPage(),
            'last_page' => $list->lastPage(),
            'data' => $item_list
        ];
        $this->success('获取成功', $data);
    }

    /**
     * 赠送藏品
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function give_as_present()
    {
        $user_hash = input('user_hash');
        $tokenId = input('link_md5');
        $password = input('password');
        if ($this->auth->getEncryptPassword($password, $this->auth->getUser()->salt) !== $this->auth->getUser()->password) {
            $this->error('密码错误');
        }
        //查找赠与方
        $user_id = 0;
        if (Validate::regex($user_hash, "^1\d{10}$")) {
            //输入的是手机号
            $user = \addons\nft\model\User::where('mobile',$user_hash)->find();
            if(!empty($user->id)){
                $user_id = $user->id;
            }
        }else{
            $identity = Identify::where('link_md5', $user_hash)->find();
            if(!empty($identity->user_id)){
                $user_id = $identity->user_id;
            }

        }

        if (empty($user_id)) {
            $this->error('未找到被赠与方');
        }
        $user_collection = UserCollection::where('tokenId', $tokenId)->where('user_id', $this->auth->id)->find();
        if (empty($user_collection)) {
            $this->error('未拥有藏品');
        }
        if ($user_id == $this->auth->id) {
            $this->error('不可自送');
        }
        //检查此商品是否是徽章  如果是徽章要判断是否开启徽章转赠
        $collection = \addons\nft\model\Collection::where('id',$user_collection->collection_id)->find();
        if(!empty($collection) && $collection->type == 'badge'){
            $config = get_addon_config('nft');
            if(empty($config['marketing']['give_status'])){
                $this->error('徽章暂时不可转赠');
            }
        }
        $ret = UserCollection::addGiveLog($tokenId, $user_id);
        if ($ret) {
            $this->success('赠送成功');
        }
        $this->error('赠送失败,请稍后尝试');
    }


}
