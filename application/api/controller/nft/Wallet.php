<?php

namespace app\api\controller\nft;

use addons\nft\model\Alipay;
use addons\nft\model\MoneyLog;
use addons\nft\model\Withdraw;
use app\admin\model\Third;
use app\admin\model\User;
use app\common\controller\NftApi;
use think\Db;
use think\Exception;

/**
 * 钱包接口
 */
class Wallet extends NftApi
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    private $transfer_key = 'transfer_user';


    /**
     * 钱包首页
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $user = User::get($this->auth->id);
        $this->success('获取成功', [
            'money' => $user->money,
        ]);
    }

    /**
     * 提现规则
     */
    public function rule()
    {
        $config = get_addon_config('nft');
        $this->success('获取成功', [
            'rule' => $config['ini']['withdrawal_rule']??''
        ]);
    }

    /**
     * 提现配置
     * @throws \think\exception\DbException
     */
    public function withdrawalConfig()
    {
        $config = get_addon_config('nft');

        $alipay = Alipay::get(['user_id' => $this->auth->id]);
        $wechat = Third::get(['user_id' => $this->auth->id, 'platform' =>'wechat']);
        $this->success('获取成功', [
            'alipay' => $alipay ? true : false,
            'wechat' => $wechat ? true : false,
            'free' => $config['withdraw']['servicefee'],  //手续费
        ]);
    }

    /**
     * 提现
     * @ApiMethod (POST)
     * @throws Exception
     * @throws \think\exception\DbException
     * @ApiParams (name="password", type="string", required=true, description="支付密码")
     * @ApiParams (name="money", type="string", required=true, description="金额")
     * @ApiParams (name="withdrawal_type", type="string", required=true, description="提现方式")
     */
    public function payment()
    {
        $config = get_addon_config('nft');
        if ($config['withdraw']['state'] == 'N') {
            $this->error("系统该关闭提现功能，请联系平台客服");
        }
        $password = $this->request->post('password');

        if (!$this->auth->checkPayPassword($password)) {
            $this->error('支付密码错误');
        }
        // 金额
        $money = $this->request->post('money');
        // 提现方式
        $withdrawal_type = $this->request->post('withdrawal_type');
        if ($money <= 0 || !$money) {
            $this->error('提现金额不正确');
        }
        if ($money > $this->auth->money) {
            $this->error('提现金额超出可提现额度');
        }
        if (!$withdrawal_type || !in_array($withdrawal_type, [Withdraw::TYPE_ALIPAY, Withdraw::TYPE_WECHAT])) {
            $this->error("请选择提现方式");
        }
        if ($withdrawal_type == Withdraw::TYPE_WECHAT) {
            $withdrawal_account = Third::get(['user_id' => $this->auth->id, 'platform' => 'wechat']);
            $withdrawal_type = $withdrawal_account->platform;
            $withdrawal_account = [
                'account' => $withdrawal_account->openid,
            ];
        }
        if ($withdrawal_type == Withdraw::TYPE_ALIPAY) {
            $withdrawal_account = Alipay::get(['user_id' => $this->auth->id]);
        }
        if (empty($withdrawal_account)) {
            $this->error("提现账户不可用");
        }


        if (isset($config['withdraw']['minmoney']) && $money < $config['withdraw']['minmoney']  ) {
            $this->error('提现金额最少请输入' . $config['withdraw']['minmoney'] . '元');
        }
        $number = -1;
        if ($config['withdraw']['monthlimit']) {
            $count = model(Withdraw::class)->where('user_id', $this->auth->id)->whereTime('createtime', 'month')->count();
            if ($count >= $config['withdraw']['monthlimit']) {
                $this->error("已达到本月最大可提现次数");
            }
            $number = $config['withdraw']['monthlimit'] - $count;
        }
        // 计算提现手续费
        if ($config['withdraw']['servicefee'] && $config['withdraw']['servicefee'] > 0) {
            $servicefee = number_format($money * $config['withdraw']['servicefee'] / 1000, 2);
            $handingmoney = $money - number_format($money * $config['withdraw']['servicefee'] / 1000, 2);
        } else {
            $servicefee = 0;
            $handingmoney = $money;
        }
        Db::startTrans();
        try {
            $data = [
                'user_id' => $this->auth->id,
                'money' => $handingmoney,
                'handingfee' => $servicefee, // 手续费
                'type' => $withdrawal_type,
                'account' => $withdrawal_account['account'],
                'memo' => $withdrawal_account['name'] ?? '',
                'orderid' => date("Ymdhis") . sprintf("%08d", $this->auth->id) . mt_rand(1000, 9999)
            ];
            $withdraw = model(Withdraw::class)->create($data);
            //扣除用户的余额
            \addons\nft\model\User::money(bcmul(-1, $handingmoney), $this->auth->id, MoneyLog::WITHDRAW);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('提现申请成功！请等待后台审核', [
            'money' => $this->auth->money,
            'num' => $number,
        ]);

    }


//    /**
//     * 金额变化类型
//     */
//    public function changeType()
//    {
//        $this->success('获取成功', MoneyLog::getType());
//    }


    /**
     * 资产变更明细
     * @ApiMethod (POST)
     * @ApiParams (name="type", type="string", required=false, description="变更类型")
     * @ApiParams (name="time", type="string", required=false, description="时间2021-11")
     */
    public function moneyLog()
    {
        $where['user_id'] = $this->auth->id;
        $type = $this->request->post('type', '');
        if (!empty($type)) {
            $where['type'] = $type;
        }
        $time = $this->request->post('time', '');
        if (!empty($time)) {
            $timestamp = strtotime($time);
            $start_time = strtotime(date('Y-m-1 00:00:00', $timestamp));
            $mdays = date('t', $timestamp);
            $end_time = strtotime(date('Y-m-' . $mdays . ' 23:59:59', $timestamp));
            $where['createtime'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        $list = model(MoneyLog::class)
            ->where($where)
            ->order('createtime desc')
            ->paginate();
        $income = model(MoneyLog::class)
            ->where($where)->where('money', '>', 0)->sum('money');
        $expenditure = model(MoneyLog::class)
            ->where($where)->where('money', '<', 0)->sum('money');
        $data = $list->toArray();
        $data['income'] = $income;
        $data['expenditure'] = abs($expenditure);
        $this->success('ok', $data);

    }

}
