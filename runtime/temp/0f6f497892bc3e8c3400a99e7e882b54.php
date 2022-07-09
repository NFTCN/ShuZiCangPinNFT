<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:51:"/www/wwwroot/www.masart.top/addons/epay/config.html";i:1650339576;s:70:"/www/wwwroot/www.masart.top/application/admin/view/layout/default.html";i:1650339576;s:67:"/www/wwwroot/www.masart.top/application/admin/view/common/meta.html";i:1650339578;s:69:"/www/wwwroot/www.masart.top/application/admin/view/common/script.html";i:1650339578;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="config-form" class="edit-form form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="panel panel-default panel-intro">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-group">
                <li class="active"><a href="#wechat" data-toggle="tab">微信支付</a></li>
                <li><a href="#alipay" data-toggle="tab">支付宝</a></li>
            </ul>
        </div>

        <div class="panel-body">
            <div id="myTabContent" class="tab-content">
                <?php foreach($addon['config'] as $item): if($item['name']=='wechat'): ?>
                <div class="tab-pane fade active in" id="wechat">
                    <table class="table table-striped table-config">
                        <tbody>
                        <tr>
                            <td width="15%">APP appid</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][appid]" value="<?php echo (isset($item['value']['appid']) && ($item['value']['appid'] !== '')?$item['value']['appid']:''); ?>" class="form-control" data-rule="" data-tip="APP应用中支付时使用"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>公众号的app_id</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][app_id]" value="<?php echo (isset($item['value']['app_id']) && ($item['value']['app_id'] !== '')?$item['value']['app_id']:''); ?>" class="form-control" data-rule="" data-tip="公众号支付必须"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>公众号的app_secret</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][app_secret]" value="<?php echo (isset($item['value']['app_secret']) && ($item['value']['app_secret'] !== '')?$item['value']['app_secret']:''); ?>" class="form-control" data-rule="" data-tip="仅在需要获取Openid时使用，一般情况下为空"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>小程序的app_id</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][miniapp_id]" value="<?php echo (isset($item['value']['miniapp_id']) && ($item['value']['miniapp_id'] !== '')?$item['value']['miniapp_id']:''); ?>" class="form-control" data-rule="" data-tip="仅在小程序支付时使用"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>微信支付商户号ID</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][mch_id]" value="<?php echo (isset($item['value']['mch_id']) && ($item['value']['mch_id'] !== '')?$item['value']['mch_id']:''); ?>" class="form-control" data-rule="" data-tip=""/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>微信支付商户的密钥</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][key]" value="<?php echo (isset($item['value']['key']) && ($item['value']['key'] !== '')?$item['value']['key']:''); ?>" class="form-control" data-rule="" data-tip=""/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>支付模式</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <?php echo Form::radios('row[wechat][mode]',['normal'=>'正式环境','dev'=>'沙箱环境','service'=>'服务商模式'],$item['value']['mode']??'normal'); ?>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr data-type="service" class="<?php echo $item['value']['mode']!='service'?'hidden':''; ?>">
                            <td>子商户商户号ID</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][sub_mch_id]" value="<?php echo (isset($item['value']['sub_mch_id']) && ($item['value']['sub_mch_id'] !== '')?$item['value']['sub_mch_id']:''); ?>" class="form-control" data-rule="" data-tip="如果未用到子商户，请勿填写"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr data-type="service" class="<?php echo $item['value']['mode']!='service'?'hidden':''; ?>">
                            <td>子商户 APP appid</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][sub_appid]" value="<?php echo (isset($item['value']['sub_appid']) && ($item['value']['sub_appid'] !== '')?$item['value']['sub_appid']:''); ?>" class="form-control" data-rule="" data-tip="如果未用到子商户，请勿填写"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr data-type="service" class="<?php echo $item['value']['mode']!='service'?'hidden':''; ?>">
                            <td>子商户公众号的appid</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][sub_app_id]" value="<?php echo (isset($item['value']['sub_app_id']) && ($item['value']['sub_app_id'] !== '')?$item['value']['sub_app_id']:''); ?>" class="form-control" data-rule="" data-tip="如果未用到子商户，请勿填写"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr data-type="service" class="<?php echo $item['value']['mode']!='service'?'hidden':''; ?>">
                            <td>子商户小程序的appid</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][sub_miniapp_id]" value="<?php echo (isset($item['value']['sub_miniapp_id']) && ($item['value']['sub_miniapp_id'] !== '')?$item['value']['sub_miniapp_id']:''); ?>" class="form-control" data-rule="" data-tip="如果未用到子商户，请勿填写"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>回调通知地址</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[wechat][notify_url]" value="<?php echo (isset($item['value']['notify_url']) && ($item['value']['notify_url'] !== '')?$item['value']['notify_url']:''); ?>" class="form-control" data-rule="" data-tip="请勿随意修改，实际以逻辑代码中请求的为准"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>微信支付API证书cert</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="input-group">
                                            <input id="c-cert_client" class="form-control" size="50" name="row[wechat][cert_client]" type="text" value="<?php echo htmlentities($item['value']['cert_client']); ?>" data-tip="可选, 仅在退款、红包等情况时需要用到">
                                            <div class="input-group-addon no-border no-padding">
                                                <span><button type="button" id="faupload-cert_client" class="btn btn-danger faupload" data-url="epay/upload" data-multipart='{"certname":"cert_client"}' data-mimetype="pem" data-input-id="c-cert_client" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                            </div>
                                            <span class="msg-box n-right" for="c-cert_client"></span>
                                        </div>
                                        <div style="margin-top:5px;"><a href="https://pay.weixin.qq.com" target="_blank"><i class="fa fa-question-circle"></i> 如何获取微信支付API证书?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>微信支付API证书key</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="input-group">
                                            <input id="c-cert_key" class="form-control" size="50" name="row[wechat][cert_key]" type="text" value="<?php echo htmlentities($item['value']['cert_key']); ?>" data-tip="可选, 仅在退款、红包等情况时需要用到">
                                            <div class="input-group-addon no-border no-padding">
                                                <span><button type="button" id="faupload-cert_key" class="btn btn-danger faupload" data-url="epay/upload" data-multipart='{"certname":"cert_key"}' data-mimetype="pem" data-input-id="c-cert_key" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                            </div>
                                            <span class="msg-box n-right" for="c-cert_key"></span>
                                        </div>
                                        <div style="margin-top:5px;"><a href="https://pay.weixin.qq.com" target="_blank"><i class="fa fa-question-circle"></i> 如何获取微信支付API证书?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>记录日志</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <?php echo Form::radios('row[wechat][log]',['1'=>'开启','0'=>'关闭'],$item['value']['log']); ?>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php elseif($item['name']=='alipay'): ?>
                <div class="tab-pane fade" id="alipay">
                    <table class="table table-striped table-config">
                        <tbody>
                        <tr>
                            <td width="15%">应用ID(app_id)</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[alipay][app_id]" value="<?php echo (isset($item['value']['app_id']) && ($item['value']['app_id'] !== '')?$item['value']['app_id']:''); ?>" class="form-control" data-rule="" data-tip=""/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>支付模式</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <?php echo Form::radios('row[alipay][mode]',['normal'=>'正式环境','dev'=>'沙箱环境'],$item['value']['mode']??'normal'); ?>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>回调通知地址</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[alipay][notify_url]" value="<?php echo (isset($item['value']['notify_url']) && ($item['value']['notify_url'] !== '')?$item['value']['notify_url']:''); ?>" class="form-control" data-rule="" data-tip="请勿随意修改，实际以逻辑代码中请求的为准"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>支付跳转地址</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[alipay][return_url]" value="<?php echo (isset($item['value']['return_url']) && ($item['value']['return_url'] !== '')?$item['value']['return_url']:''); ?>" class="form-control" data-rule="" data-tip="请勿随意修改，实际以逻辑代码中请求的为准"/>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>应用私钥(private_key)</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <input type="text" name="row[alipay][private_key]" value="<?php echo (isset($item['value']['private_key']) && ($item['value']['private_key'] !== '')?$item['value']['private_key']:''); ?>" class="form-control" data-rule="" />
                                        <div style="margin-top:5px;"><a href="https://opensupport.alipay.com/support/helpcenter/207/201602469554" target="_blank"><i class="fa fa-question-circle"></i> 如何获取应用私钥?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>支付宝公钥路径(ali_public_key)</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="input-group">
                                            <input id="c-ali_public_key" class="form-control" size="50" name="row[alipay][ali_public_key]" type="text" value="<?php echo htmlentities((isset($item['value']['ali_public_key']) && ($item['value']['ali_public_key'] !== '')?$item['value']['ali_public_key']:'')); ?>" placeholder="公钥请直接粘贴，公钥证书请点击右侧的上传">
                                            <div class="input-group-addon no-border no-padding">
                                                <span><button type="button" id="faupload-ali_public_key" class="btn btn-danger faupload" data-url="epay/upload" data-multipart='{"certname":"ali_public_key"}' data-mimetype="crt" data-input-id="c-ali_public_key" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                            </div>
                                            <span class="msg-box n-right" for="c-ali_public_key"></span>
                                        </div>
                                        <div style="margin-top:5px;"><a href="javascript:" data-toggle="tooltip" data-title="如果要使用转账、提现功能，则必须使用公钥证书"> <i class="fa fa-info-circle"></i> 公钥和公钥证书说明</a> <a href="https://opensupport.alipay.com/support/helpcenter/207/201602471154" target="_blank"><i class="fa fa-question-circle"></i> 如何获取支付宝公钥证书?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>应用公钥证书路径(app_cert_public_key)</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="input-group">
                                            <input id="c-app_cert_public_key" class="form-control" size="50" name="row[alipay][app_cert_public_key]" type="text" value="<?php echo htmlentities((isset($item['value']['app_cert_public_key']) && ($item['value']['app_cert_public_key'] !== '')?$item['value']['app_cert_public_key']:'')); ?>">
                                            <div class="input-group-addon no-border no-padding">
                                                <span><button type="button" id="faupload-app_cert_public_key" class="btn btn-danger faupload" data-url="epay/upload" data-multipart='{"certname":"app_cert_public_key"}' data-mimetype="crt" data-input-id="c-app_cert_public_key" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                            </div>
                                            <span class="msg-box n-right" for="c-app_cert_public_key"></span>
                                        </div>
                                        <div style="margin-top:5px;"><a href="https://opensupport.alipay.com/support/helpcenter/207/201602469554" target="_blank"><i class="fa fa-question-circle"></i> 如何获取应用公钥证书?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>支付宝根证书路径(alipay_root_cert)</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="input-group">
                                            <input id="c-alipay_root_cert" class="form-control" size="50" name="row[alipay][alipay_root_cert]" type="text" value="<?php echo htmlentities((isset($item['value']['alipay_root_cert']) && ($item['value']['alipay_root_cert'] !== '')?$item['value']['alipay_root_cert']:'')); ?>">
                                            <div class="input-group-addon no-border no-padding">
                                                <span><button type="button" id="faupload-alipay_root_cert" class="btn btn-danger faupload" data-url="epay/upload" data-multipart='{"certname":"alipay_root_cert"}' data-mimetype="crt" data-input-id="c-alipay_root_cert" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                            </div>
                                            <span class="msg-box n-right" for="c-alipay_root_cert"></span>
                                        </div>
                                        <div style="margin-top:5px;"><a href="https://opensupport.alipay.com/support/helpcenter/207/201602469554" target="_blank"><i class="fa fa-question-circle"></i> 如何获取支付宝证书?</a></div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>记录日志</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <?php echo Form::radios('row[alipay][log]',['1'=>'开启','0'=>'关闭'],$item['value']['log']); ?>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>PC端使用扫码支付</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <?php echo Form::radios('row[alipay][scanpay]',['1'=>'开启','0'=>'关闭'],$item['value']['scanpay']??0); ?>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; endforeach; ?>
                <div class="form-group layer-footer">
                    <label class="control-label col-xs-12 col-sm-2"></label>
                    <div class="col-xs-12 col-sm-8">
                        <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
                        <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    document.querySelectorAll("input[name='row[wechat][mode]']").forEach(function (i, j) {
        i.addEventListener("click", function () {
            document.querySelectorAll("#wechat table tr[data-type]").forEach(function (m, n) {
                m.classList.add("hidden");
            });
            document.querySelectorAll("#wechat table tr[data-type='" + this.value + "']").forEach(function (m, n) {
                m.classList.remove("hidden");
            });
        });
    });
</script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
