define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
		// 系统设置
		config: function () {
		    Controller.api.bindevent();
		},
        api: {
			bindevent: function () {
				Form.api.bindevent($("form[role=form]"), function(data, ret){});
			}
        }
    };
    return Controller;
});