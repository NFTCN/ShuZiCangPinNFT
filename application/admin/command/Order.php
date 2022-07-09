<?php

namespace app\admin\command;

use app\common\service\nft\OrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Order extends Command
{
    protected function configure()
    {
        $this->setName('order')->setDescription('order overtime');
    }

    protected function execute(Input $input, Output $output)
    {
        OrderService::overtime();
        $output->writeln("订单超时检查完成");
    }

}