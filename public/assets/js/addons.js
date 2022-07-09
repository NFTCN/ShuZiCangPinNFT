define([], function () {
    require.config({
    paths: {
		'vue': '../addons/nft/js/vue.min',
    },
    shim: {
        'vue': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        }
    }
});
require.config({
    paths: {
        'simditor': '../addons/simditor/js/simditor.min',
    },
    shim: {
        'simditor': [
            'css!../addons/simditor/css/simditor.min.css'
        ]
    }
});
require(['form'], function (Form) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        if ($(".editor", form).size() > 0) {
            //修改上传的接口调用
            require(['upload', 'simditor'], function (Upload, Simditor) {
                var editor, mobileToolbar, toolbar;
                Simditor.locale = 'zh-CN';
                Simditor.list = {};
                toolbar = ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', 'table', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment'];
                mobileToolbar = ["bold", "underline", "strikethrough", "color", "ul", "ol"];
                $(".editor", form).each(function () {
                    var id = $(this).attr("id");
                    editor = new Simditor({
                        textarea: this,
                        toolbarFloat: false,
                        toolbar: toolbar,
                        pasteImage: true,
                        defaultImage: Config.__CDN__ + '/assets/addons/simditor/images/image.png',
                        upload: {url: '/'}
                    });
                    editor.uploader.on('beforeupload', function (e, file) {
                        Upload.api.send(file.obj, function (data) {
                            var url = Fast.api.cdnurl(data.url);
                            editor.uploader.trigger("uploadsuccess", [file, {success: true, file_path: url}]);
                        });
                        return false;
                    });
                    editor.on("blur", function () {
                        this.textarea.trigger("blur");
                    });
                    Simditor.list[id] = editor;
                });
            });
        }
    }
});
if (Config.modulename === 'index' && Config.controllername === 'user' && ['login', 'register'].indexOf(Config.actionname) > -1 && $("#register-form,#login-form").size() > 0) {
    $('<style>.social-login{display:flex}.social-login a{flex:1;margin:0 2px;}.social-login a:first-child{margin-left:0;}.social-login a:last-child{margin-right:0;}</style>').appendTo("head");
    $("#register-form,#login-form").append('<div class="form-group social-login"></div>');
    if (Config.third.status.indexOf("wechat") > -1) {
        $('<a class="btn btn-success" href="' + Fast.api.fixurl('/third/connect/wechat') + '"><i class="fa fa-wechat"></i> 微信登录</a>').appendTo(".social-login");
    }
    if (Config.third.status.indexOf("qq") > -1) {
        $('<a class="btn btn-info" href="' + Fast.api.fixurl('/third/connect/qq') + '"><i class="fa fa-qq"></i> QQ登录</a>').appendTo(".social-login");
    }
    if (Config.third.status.indexOf("weibo") > -1) {
        $('<a class="btn btn-danger" href="' + Fast.api.fixurl('/third/connect/weibo') + '"><i class="fa fa-weibo"></i> 微博登录</a>').appendTo(".social-login");
    }
}

});