<?php


namespace app\common\service\nft;


use addons\epay\library\Collection;
use addons\epay\library\RedirectResponse;
use addons\epay\library\Response;
use addons\epay\library\Service;
use addons\nft\library\Common;
use app\admin\model\nft\pay\Log as PayLog;
use app\common\model\User;
use think\Exception;
use think\Hook;
use think\Log;
use Yansongda\Pay\Pay;

class PayService extends Service
{
    // 支付状态
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_FAIL = -1;

    const TYPE_ALI_PAY = 'alipay';
    const TYPE_WX_PAY = 'wechat';
    const TYPE_BALANCE = 'balance';

    const PAY_TYPE = [
        1 => 'alipay',
        2 => 'wechat',
        3 => 'balance'
    ];

    /**
     * 提交订单
     *
     * @param $params
     *
     * @return Response|RedirectResponse|Collection
     * @throws Exception
     */
    public static function run($params)
    {
        if (empty($params['type']) || !in_array($params['type'], [self::TYPE_WX_PAY, self::TYPE_ALI_PAY, self::TYPE_BALANCE])) {
            throw new Exception('请选择正确的支付方式');
        }
        if (empty($params['order_no'])) {
            throw new Exception('订单号信息不存在');
        }
        $amount = $params['amount'];
        $type = $params['type'];
        $method = $params['method'] ?? 'web';
        $orderid = $params['orderid'] ?? date("YmdHis") . mt_rand(100000, 999999);
        $title = $params['title'] ?? "支付";
        $openid = $params['openid'] ?? '';
        $user_id = $params['user_id'] ?? 0;
        $ref_type = $params['ref_type'] ?? 'pay';

        $payLog = new PayLog();
        $payLog->user_id = $user_id;
        $payLog->type = $type;
        $payLog->ref_type = $ref_type;
        $payLog->out_trade_no = $params['order_no'];
        $payLog->order_id = $orderid;
        $payLog->amount = $amount;
        $payLog->title = $title;
        $payLog->status = self::STATUS_PENDING;
        $payLog->trade_create = time();
        $payLog->callback = json_encode($params['event']);
        if (!$payLog->save()) {
            throw new Exception('支付失败');
        }
        $result = null;
        if ($type == self::TYPE_BALANCE) {
            Log::info([-$params['amount'], $user_id, $title]);
            User::money(-$amount, $user_id, 'pay', $title);
            self::notify(['id' => $payLog->id, 'type' => $type, 'out_trade_no' => $payLog->order_id]);
        } else {
            $request = request();
            $notifyurl = $params['notifyurl'] ?? $request->root(true) . '/api/nft.pay/notify/paytype/' . $type;
            $returnurl = $params['returnurl'] ?? $request->root(true) . '/api/nft.pay/' . $type.'return';
            $result = self::submitOrder([
                'amount' => $payLog->amount,
                'orderid' => $payLog->order_id,
                'type' => $payLog->type,
                'title' => $payLog->title,
                'notifyurl' => $notifyurl,
                'method' => $method,
                'openid' => $openid,
                'returnurl'=>$returnurl
            ]);
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public static function notify($params)
    {
        try {
            if (in_array($params['type'], [self::TYPE_ALI_PAY, self::TYPE_WX_PAY], false)) {
                $config = self::getConfig($params['type']);
                if ($params['type'] == self::TYPE_ALI_PAY) {
                    $pay = Pay::alipay($config);
                } else {
                    $pay = Pay::wechat($config);
                }
                $data = $pay->verify();
                Log::info($data);
            }

            // 获取Model之前需要做的验证，以及获取订单号
            $msg = '';
            $payStatus = self::STATUS_PAID;
            // TODO 失败情况增加记录 状态
            if ($params['type'] === self::TYPE_ALI_PAY) {
                if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                    $msg = '支付未成功';
                    $payStatus = self::STATUS_FAIL;
                }
                $out_trade_no = $data->out_trade_no;
            } elseif ($params['type'] === self::TYPE_WX_PAY) {
                // 失败标识 FAIL
                if (!$data->result_code == 'SUCCESS') {
                    $msg = '支付未成功';
                    $payStatus = self::STATUS_FAIL;
                }
                $out_trade_no = $data->out_trade_no;
            } elseif ($params['type'] == self::TYPE_BALANCE) {
                $out_trade_no = $params['out_trade_no'];

            }
            $logModel = PayLog::get([
                'order_id' => $out_trade_no,
                'type' => $params['type'],
                'status' => self::STATUS_PENDING,
            ]);
            if (empty($logModel)) {
                throw new Exception('订单不存在');
            }

            // 获取Model之后需要做的验证，以及获取支付端订单号
            $tradeNo = '';
            if ($params['type'] === self::TYPE_ALI_PAY) {
                // 支付宝-验证支付回调金额一致、appId一致
                if ($logModel->amount != $data->total_amount || $data->auth_app_id != $config['app_id']) {
                    throw new Exception('订单不合法');
                }
                $tradeNo = $data->trade_no;
                $tradeCreate = $data->gmt_create;
                $tradePayment = $data->gmt_payment;
            } elseif ($params['type'] === self::TYPE_WX_PAY) {
                if ($data->total_fee != (int)($logModel->amount * 100) || $data->mch_id != $config['mch_id']) {
                    throw new Exception('订单不合法');
                }
                $tradeNo = $data->transaction_id;
                $tradeCreate = date('Y-m-d H:i:s', $logModel->created_at);
                $tradePayment = date('Y-m-d H:i:s', strtotime($data->time_end));
            } elseif ($params['type'] === self::TYPE_BALANCE) {
                $tradeNo = $logModel->out_trade_no;
                $tradeCreate = date('Y-m-d H:i:s');
                $tradePayment = date('Y-m-d H:i:s');
            }


            // 平台订单状态的修改
            $logModel->trade_no = $tradeNo;
            $logModel->trade_create = $tradeCreate ? strtotime($tradeCreate) : null;
            $logModel->trade_payment = $tradePayment ? strtotime($tradePayment) : null;
            $logModel->ori_data = !empty($data) ? json_encode($data) : null;
            $logModel->status = $payStatus;
            $logModel->msg = $msg;

            if (!$logModel->save()) {
                throw new Exception($logModel->getError());
            }

            if (!empty($logModel->callback)) {
                $event = json_decode($logModel->callback, true);
                $params = $logModel->toArray();
                Hook::exec($event[0], $event[1], $params);
            }

            if (in_array($params['type'], [self::TYPE_ALI_PAY, self::TYPE_WX_PAY], false)) {
                return $pay->success()->send();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 创建Token
     */
    public static function creatToken()
    {
        $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
        $key = "collection";
        $code = md5($key . substr(md5($code), 8, 10));
        return $code;
    }

    /**
     * 检查是否有库存 如果没有就设置
     *
     * @param string $key
     * @param int $num
     *
     * @return bool
     * @throws Exception
     */
    public static function setStock(string $key, int $num): bool
    {
        $redis = Common::redis();
        // 获取缓存数量
        $llen = $redis->llen($key);
        if (!$llen) {
            for ($i = 0; $i < $num; $i++) {
                $redis->lpush($key, 1);
            }
        }
        return true;
    }

    /**
     * 检查库存
     *
     * @param string $key 库存key
     * @param int $num 数量
     *
     * @return bool
     * @throws Exception
     */
    public static function checkoutStock(string $key, int $num): bool
    {
        $redis = Common::redis();
        return $num <= $redis->llen($key);
    }

    /**
     * 减少库存
     *
     * @param string $key
     * @param int $num
     *
     * @return bool
     * @throws Exception
     */
    public static function subStock(string $key, int $num): bool
    {
        $redis = Common::redis();
        for ($i = 0; $i < $num; $i++) {
            $redis->rpop($key);
        }
        return true;
    }

    public static function addStock(string $key, int $num): bool
    {
        $redis = Common::redis();
        // 获取缓存数量
        for ($i = 0; $i < $num; $i++) {
            $redis->lpush($key, 1);
        }
        return true;
    }

    /**
     * 删除指定库存
     *
     * @param string $key
     *
     * @throws Exception
     */
    public static function rmStock(string $key): void
    {
        $redis = Common::redis();
        $redis->del($key);
    }

    public static function getStock(string $key)
    {
        $redis = Common::redis();
        return $redis->llen($key);
    }

    /**
     * 企业付款
     *
     * @param $params
     *
     * @return array
     */
    public static function transfer($params,$payType,$method)
    {
        $type = '';
        if ($payType == 'alipay') {
            $type = 'alipay';
            $order = [
                'out_biz_no' => $params['trade_no'],
                'trans_amount' => $params['money'],
                'product_code' => 'TRANS_ACCOUNT_NO_PWD',
                'order_title' => '帐户提现',
                'payee_info' => [
                    'identity' => $params['account'],
                    'identity_type' => 'ALIPAY_LOGON_ID',
                    'name' => $params['name']
                ],
                'biz_scene' => 'DIRECT_TRANSFER',
                'remark' => '帐户提现'
            ];
        } else if ($payType == 'wechat') {
            $type = 'wechat';
            $order = [
                'partner_trade_no' => $params['trade_no'],              //商户订单号
                'openid' => $params['account'],                        //收款人的openid
                'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
                'amount' => $params['money'] * 100,                       //企业付款金额，单位为分
                'desc' => '帐户提现',                  //付款说明
                'type' => 'app',
            ];
            if ($params['method'] != 'app') {
                $order['type'] = 'miniapp';
            }
        }
        // 退款
        try {
            $wanlpay = Pay::{$type}(self::getConfig($type))->transfer($order);
            if ($wanlpay instanceof \WanlPay\Supports\Collection) {
                Log::debug('Wanlpay notify', $wanlpay->all());
                return ['code' => 200, 'msc' => '成功', 'data' => $wanlpay];
            } else {
                return ['code' => 10008, 'msc' => $wanlpay];
            }
        } catch (\Exception $e) {
            return ['code' => 10008, 'msg' => $e->getMessage()];
        }
    }

}
