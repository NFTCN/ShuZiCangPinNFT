<?php

namespace app\admin\controller\nft;

use app\admin\model\nft\pay\Log as PayLog;
use app\common\controller\Backend;
use app\common\service\nft\PayService;

/**
 * 提现管理
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{

    /**
     * Withdraw模型对象
     * @var \app\admin\model\nft\Withdraw
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\nft\Withdraw;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 详情
     */
    public function detail($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 同意
     */
    public function agree($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($row['status'] == 'successed') {
            $this->error(__('已审核过，请不要重复审核！'));
        }
        if ($this->request->isPost()) {
            $result = false;
            Db::startTrans();
            try {
                //是否采用模型验证
                if ($this->modelValidate) {
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                    $row->validateFailException(true)->validate($validate);
                }
                // 审核通过
                $result = $row->allowField(true)->save([
                    'status' => 'successed',
                    'transfertime' => time()
                ]);
                $payInfo = [
                    'status' => 0,
                    'user_id' => $row->user_id,
                    'order_id' => $row->id,
                    'out_trade_no' => $row->orderid,
                    'type' => 'withdraw', // 1.0.8升级
                    'amount' => $row->money <= 0 ? 0.01 : $row->money,  // 支付价格，系统要求至少支付一分钱
                ];
                $id = PayLog::insert($payInfo, false, true);
                $method = 'app'; //app-应用 miniapp-微信小程序

                $params = [];
                if ($row->type == '支付宝' || $row->type == '支付宝账户') {
                    $payType = 'alipay';
                    $payInfo['pay_type'] = 1;
                    $params = [
                        'account' => $row->account,
                        'money' => $row->money,
                        'name' => $row->memo,
                        'trade_no' => PayLog::where('id', $id)->value('out_trade_no')
                    ];
                } else {
                    $payType = 'wechat';
                    if ($row->type == '微信小程序') {
                        $method = 'miniapp'; //微信小程序提现
                    }
                    $payInfo['pay_type'] = 2;
                    $params = [
                        'account' => $row->account,
                        'money' => $row->money,
                        'trade_no' => PayLog::where('id', $id)->value('out_trade_no')
                    ];
                }
                $data = PayService::transfer($params,$payType,$method);
                if ($data['code'] == 200) {
                    Db::commit();
                } else {
                    exception($data['msg']);
                }
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success();
            } else {
                $this->error(__('No rows were updated'));
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 拒绝
     */
    public function refuse($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $params['status'] = 'rejected';
                    $result = $row->allowField(true)->save($params);
                    // 更新用户金额
                    controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+bcadd($row['money'], $row['handingfee'], 2), $row['user_id'], '提现失败返回余额', 'withdraw', $row['id']);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}
