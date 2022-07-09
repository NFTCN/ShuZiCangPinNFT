define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'nft.pay.log/index' + location.search,
                    add_url: 'nft.pay.log/add',
                    edit_url: 'nft.pay.log/edit',
                    del_url: 'nft.pay.log/del',
                    multi_url: 'nft.pay.log/multi',
                    import_url: 'nft.pay.log/import',
                    table: 'pay_log',
                }
            });

            var table = $("#table");
            console.log(Config.statusList)

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'type', title: __('Type'), operate: 'LIKE',searchList:Config.typeList,formatter: Table.api.formatter.label},
                        {field: 'ref_type', title: __('Ref_type'), operate: 'LIKE'},
                        {field: 'order_id', title: __('Order_id')},
                        {field: 'out_trade_no', title: __('Out_trade_no'), operate: 'LIKE'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'),searchList:Config.statusList,formatter: Table.api.formatter.status},
                        {field: 'trade_no', title: __('Trade_no'), operate: 'LIKE'},
                        {field: 'trade_payment', title: __('Trade_payment'),formatter: Table.api.formatter.datetime},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
