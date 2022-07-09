define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'nft.withdraw/index' + location.search,
                    add_url: '',
                    edit_url: '',
                    del_url: 'nft.withdraw/del',
                    multi_url: 'nft.withdraw/multi',
                    import_url: 'nft.withdraw/import',
                    table: 'withdraw',
                }
            });

            var table = $("#table");

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
                        {field: 'id', title: __('ID')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'handingfee', title: __('Handingfee'), operate:'BETWEEN'},
                        {field: 'taxes', title: __('Taxes'), operate:'BETWEEN'},
                        {field: 'type', title: __('Type')},
                        {field: 'account', title: __('Account')},
                        // {field: 'memo', title: __('Memo')},
                        // {field: 'orderid', title: __('Orderid')},
                        // {field: 'transactionid', title: __('Transactionid')},
                        {field: 'transfertime', title: __('Transfertime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"created":__('Status created'),"successed":__('Status successed'),"rejected":__('Status rejected')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [ {
                                name: 'agree',
                                title: __('同意提现申请'),
                                classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                icon: 'fa fa-check',
                                text: '同意',
                                confirm: '确认点击同意，通过提现申请？',
                                url: 'nft.withdraw/agree',
                                visible: function(row) {
                                    // 审核:0=提交资质,1=提交店铺,2=提交审核,3=通过,4=未通过
                                    if (row.status == 'created') {
                                        return true;
                                    }
                                },
                                success: function(data, ret) {
                                    table.bootstrapTable('refresh');
                                    return false;
                                },
                                error: function(data, ret) {
                                    console.log(data, ret);
                                    Layer.alert(ret.msg);
                                    return false;
                                }
                            }, {
                                name: 'refuse',
                                title: __('拒绝提现申请'),
                                classname: 'btn btn-xs btn-danger btn-dialog',
                                icon: 'fa fa-times',
                                text: '拒绝',
                                url: 'nft.withdraw/refuse',
                                visible: function(row) {
                                    if (row.status == 'created') {
                                        return true;
                                    }
                                },
                                extend: 'data-area=["500px","270px"]'
                            },{
                                name: 'detail',
                                title: __('详情'),
                                text: '详情',
                                classname: 'btn btn-xs btn-info btn-dialog',
                                icon: 'fa fa-eye',
                                url: 'nft.withdraw/detail'
                            }],
                            formatter: Table.api.formatter.operate,
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        refuse: function () {
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