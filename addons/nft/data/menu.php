<?php
$menu = [
    [
        'name'    => '收藏品',
        'title'   => '收藏品商城',
        'icon'    => 'fa fa-magic',
		'weigh'	  => '8000',
        'sublist' => [
			[
				'name'    => 'wanlshop/category/goods',
				'title'   => '电商类目',
				'icon'    => 'fa fa-list-ol',
				'weigh'	  => '5900',
				'remark'  => '用于管理类目，快速生成类目和管理类型下属性商品',
				'sublist' => [
					['name' => 'wanlshop/category/goods/index', 'title' => '查看'],
					['name' => 'wanlshop/category/add', 'title' => '添加'],
					['name' => 'wanlshop/category/edit', 'title' => '修改'],
					['name' => 'wanlshop/category/del', 'title' => '删除'],
					['name' => 'wanlshop/category/multi', 'title' => '批量更新'],
					['name' => 'wanlshop/category/create', 'title' => '生成菜单'],
					['name' => 'wanlshop/category/select', 'title' => '选择']
				]
			],
            [
                'name'    => 'nft/order',
                'title'   => '订单监管',
                'icon'    => 'fa fa-first-order',
    			'weigh'	  => '5800',
                'sublist' => [
                    ['name' => 'wanlshop/order', 'title' => '商品订单', 'weigh' => '804', 'ismenu' => 1, 'remark' => '仅用户监管商城订单，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/order/index', 'title' => '查看'],
    					['name' => 'wanlshop/order/detail', 'title' => '详情'],
    					['name' => 'wanlshop/order/relative', 'title' => '快递查询'],
    					['name' => 'wanlshop/order/add', 'title' => '添加'],
    					['name' => 'wanlshop/order/edit', 'title' => '修改'],
    					['name' => 'wanlshop/order/del', 'title' => '删除'],
    					['name' => 'wanlshop/order/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/order/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/order/restore", "title" => "还原"],
    					["name" => "wanlshop/order/destroy", "title" => "真实删除"]
    				]],
					['name' => 'wanlshop/groups/order', 'title' => '拼团订单', 'weigh' => '803', 'ismenu' => 1, 'remark' => '仅用户监管商城订单，具体操作请在商家中心管理', 'sublist' => [
						['name' => 'wanlshop/groups/order/index', 'title' => '查看'],
						['name' => 'wanlshop/groups/orderDetail', 'title' => '详情'],
						['name' => 'wanlshop/groups/orderRelative', 'title' => '快递查询'],
						['name' => 'wanlshop/groups/orderAdd', 'title' => '添加'],
						['name' => 'wanlshop/groups/orderEdit', 'title' => '修改'],
						['name' => 'wanlshop/groups/orderDel', 'title' => '删除'],
						['name' => 'wanlshop/groups/orderMulti', 'title' => '批量更新'],
						["name" => "wanlshop/groups/orderRecyclebin", "title" => "回收站"],
						["name" => "wanlshop/groups/orderRestore", "title" => "还原"],
						["name" => "wanlshop/groups/orderDestroy", "title" => "真实删除"]
					]],
                    ['name' => 'wanlshop/comment', 'title' => '评论管理', 'weigh' => '802', 'ismenu' => 1, 'remark' => '仅用户监管商城商家评论，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/comment/index', 'title' => '查看'],
    					['name' => 'wanlshop/comment/detail', 'title' => '详情'],
    					['name' => 'wanlshop/comment/add', 'title' => '添加'],
    					['name' => 'wanlshop/comment/edit', 'title' => '修改'],
    					['name' => 'wanlshop/comment/del', 'title' => '删除'],
    					['name' => 'wanlshop/comment/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/comment/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/comment/restore", "title" => "还原"],
    					["name" => "wanlshop/comment/destroy", "title" => "真实删除"]
    				]],
                    ['name' => 'wanlshop/refund', 'title' => '退款管理', 'weigh' => '801', 'ismenu' => 1, 'remark' => '仅用户监管商城退款，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/refund/index', 'title' => '查看'],
    					['name' => 'wanlshop/refund/detail', 'title' => '退款详情'],
    					['name' => 'wanlshop/refund/agree', 'title' => '同意退款'],
    					['name' => 'wanlshop/refund/refuse', 'title' => '平台判定拒绝退款'],
    					['name' => 'wanlshop/refund/del', 'title' => '删除'],
    					['name' => 'wanlshop/refund/multi', 'title' => '批量更新']
    				]]
                ]
            ],
    		[
    		    'name'    => 'nft/finance',
    		    'title'   => '财务管理',
    		    'icon'    => 'fa fa-paypal',
    			'weigh'	  => '5500',
    		    'sublist' => [
    		        ['name' => 'wanlshop/money', 'title' => '资金账单', 'weigh' => '502', 'ismenu' => 1, 'remark' => '用于查看平台商品交易、充值、提现、退款', 'sublist' => [
    					['name' => 'wanlshop/money/index', 'title' => '查看'],
    					['name' => 'wanlshop/money/detail', 'title' => '详情']
    				]],
    				['name' => 'wanlshop/withdraw', 'title' => '用户提现', 'weigh' => '501', 'ismenu' => 1, 'remark' => '用于管理用户提现审核、同意、拒绝', 'sublist' => [
    					['name' => 'wanlshop/withdraw/index', 'title' => '查看'],
    					['name' => 'wanlshop/withdraw/detail', 'title' => '详情'],
    					['name' => 'wanlshop/withdraw/agree', 'title' => '同意'],
    					['name' => 'wanlshop/withdraw/refuse', 'title' => '拒绝'],
    					['name' => 'wanlshop/withdraw/del', 'title' => '删除'],
    					['name' => 'wanlshop/withdraw/multi', 'title' => '批量更新']
    				]]
    		    ]
    		],
    		[
    		    'name'    => 'nft/article',
    		    'title'   => '内容管理',
    		    'icon'    => 'fa fa-pencil-square',
    			'weigh'	  => '5200',
    		    'sublist' => [
    		        ['name' => 'wanlshop/article', 'title' => '文章列表', 'weigh' => '202', 'ismenu' => 1, 'remark' => '用于管理客户端的新闻、协议、帮助、及智能客服相关解答', 'sublist' => [
    					['name' => 'wanlshop/article/index', 'title' => '查看'],
    					['name' => 'wanlshop/article/add', 'title' => '添加'],
    					['name' => 'wanlshop/article/edit', 'title' => '修改'],
    					['name' => 'wanlshop/article/del', 'title' => '删除'],
    					['name' => 'wanlshop/article/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/article/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/article/restore", "title" => "还原"],
    					["name" => "wanlshop/article/destroy", "title" => "真实删除"],
    					["name" => "wanlshop/article/select", "title" => "选择"]
    				]],
    		        ['name' => 'wanlshop/category/article', 'title' => '分类管理', 'weigh' => '201', 'ismenu' => 1, 'remark' => '用于管理客户端文章类目']
    		    ]
    		],
            [
                'name'    => 'nft/config',
                'title'   => '商城配置',
                'icon'    => 'fa fa-cog',
    			'weigh'	  => '4900',
                'sublist' => [
                    ['name' => 'wanlshop/client/config', 'title' => '系统设置', 'weigh' => '6', 'ismenu' => 1, 'remark' => '用于电商系统核心配置'],
                ]
            ],
        ]
    ]
];
return $menu;
