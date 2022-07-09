<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"/www/wwwroot/www.masart.top/public/../application/admin/view/nft/client/config.html";i:1650339578;s:70:"/www/wwwroot/www.masart.top/application/admin/view/layout/default.html";i:1650339576;s:67:"/www/wwwroot/www.masart.top/application/admin/view/common/meta.html";i:1650339578;s:69:"/www/wwwroot/www.masart.top/application/admin/view/common/script.html";i:1650339578;}*/ ?>
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
                                <div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <div class="panel-lead"><em>系统设置</em>全局配置实时同步客户端，用户重启客户端后生效</div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#system" data-toggle="tab">系统配置</a></li>
            <li><a href="#order" data-toggle="tab">订单配置</a></li>
            <li><a href="#marketing" data-toggle="tab">营销</a></li>
            <li><a href="#protocol" data-toggle="tab">协议</a></li>
            <li><a href="#goods" data-toggle="tab">商品</a></li>
            <li><a href="#redis" data-toggle="tab">redis配置</a></li>
            <li><a href="#withdraw" data-toggle="tab">提现</a></li>
            <li><a href="#badge" data-toggle="tab">徽章</a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="system">
                <div class="widget-body no-padding">
                    <form id="ini-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>项目名称</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][name]', $nft['ini']['name']??'',
                                            ['data-rule'=>'required','data-tip'=>'请填写项目名称','placeholder'=>'在关于我们中显示']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>LOGO</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::image('row[ini][logo]',
                                            $nft['ini']['logo']??'', ['data-rule'=>'required']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>公司邮箱</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][company_mail]', $nft['ini']['company_mail']??'',
                                            ['data-rule'=>'required;email','data-tip'=>'公司邮箱','placeholder'=>'在关于我们中显示']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>app版本</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][versions]', $nft['ini']['versions']??'',
                                            ['data-rule'=>'required','data-tip'=>'app版本号','placeholder'=>'在关于我们中显示']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>客服电话</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][tel_phone]', $nft['ini']['tel_phone']??'',
                                            ['data-rule'=>'required:mobile','data-tip'=>'请填写客服电话','placeholder'=>'电话、手机号']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>客服邮箱</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][service_mail]', $nft['ini']['service_mail']??'',
                                            ['data-rule'=>'required;email','data-tip'=>'客服邮箱','placeholder'=>'在关于我们中显示']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>商务合作文案</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::textarea('row[ini][remind]',$nft['ini']['remind'] ?? '', ['rows'=>10]); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>工作时间</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][working_hours]',
                                            $nft['ini']['working_hours']??'',
                                            ['data-rule'=>'required','data-tip'=>'请填写工作时间','placeholder'=>'09:00~22:00']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>用户QQ群</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][shop_qun]',
                                            $nft['ini']['shop_qun']??'',
                                            ['data-rule'=>'qq','data-tip'=>'用户QQ交流群','placeholder'=>'']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>

                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="order">
                <div class="widget-body no-padding">
                    <form id="order-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>余额支付</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[ini][balance_pay]', $nft['ini']['balance_pay']??'0', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>微信支付</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[ini][wechat_pay]', $nft['ini']['wechat_pay']??'1', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>支付宝支付</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[ini][alipay_pay]', $nft['ini']['alipay_pay']??'1', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>转赠限制(小时)</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[ini][make_give]',
                                            $nft['ini']['make_give']??0, ['data-rule'=>'required','data-tip'=>'购买后X小时之后可转赠','placeholder'=>'转赠时间限制']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>支付限制(分钟)</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[ini][pay_limit_time]',
                                            $nft['ini']['pay_limit_time']??0, ['data-rule'=>'required','data-tip'=>'下单后X分钟之后订单失效','placeholder'=>'下单支付限制时间']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>实名认证</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[ini][identify]', $nft['ini']['identify']??'0', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>阿里实名认证app_code</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][identify_appcode]',
                                            $nft['ini']['identify_appcode']??'',['data-tip'=>'获取地址:https://market.aliyun.com/products/57000002/cmapi00048657.html?spm=5176.2020520132.101.2.66507218voO0IZ#sku=yuncode4265700001']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="marketing">
                <div class="widget-body no-padding">
                    <form id="badge-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>空投文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('text','row[marketing][air_drop]',
                                            $nft['marketing']['air_drop']??'', ['data-rule'=>'required','placeholder'=>'可多条文案 id之间用 , 隔开']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>盲盒活动规则</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][box]',
                                            $nft['marketing']['box']??'', ['data-rule'=>'required','placeholder'=>'盲盒玩法说明文案id']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>盲盒合成规则</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][compound]',
                                            $nft['marketing']['box']??'', ['data-rule'=>'required','placeholder'=>'合成玩法说明文案id']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>盲盒海报</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::image('row[ini][box_poster]',
                                            $nft['ini']['box_poster']??'', ['data-rule'=>'required','data-tip'=>'图片尺寸:750 * 1334']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>盲盒活动背景H5</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::image('row[ini][poster_h5]',
                                            $nft['ini']['poster_h5']??'', ['data-rule'=>'required','data-tip'=>'图片尺寸:750 * 1334']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>分享文案</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::text('row[ini][poster_text]', $nft['ini']['poster_text']??'',
                                            ['data-rule'=>'required','data-tip'=>'分享文案']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="protocol">
                <div class="widget-body no-padding">
                    <form id="protocol-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>隐私政策文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[config][privacy_protection]',
                                            $nft['config']['privacy_protection']??'', ['data-rule'=>'required','placeholder'=>'文案id请至文案管理创建文案获得']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>用户协议文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[config][user_agreement]',
                                            $nft['config']['user_agreement']??'', ['data-rule'=>'required','placeholder'=>'文案id请至文案管理创建文案获得']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>处罚公告文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('text','row[config][punish_protection]',
                                            $nft['config']['punish_protection']??'', ['data-rule'=>'required','placeholder'=>'可多条文案 id之间用 , 隔开']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>转赠文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('text','row[config][make_friend]',
                                            $nft['config']['make_friend']??'', ['data-rule'=>'required','placeholder'=>'文案id请至文案管理创建文案获得']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>充值说明文案ID</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[config][recharge]',
                                            $nft['config']['recharge']??'', ['data-rule'=>'required','placeholder'=>'文案id请至文案管理创建文案获得']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="goods">
                <div class="widget-body no-padding">
                    <form id="goods-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>顶级分类</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::fieldlist('row[config][category]',$nft['config']['category']?json_decode($nft['config']['category'],true):[], null, '', ['data-rule'=>'required']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>藏品购买规则</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::textarea('row[ini][warning]',$nft['ini']['warning'] ?? '', ['rows'=>10]); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="redis">
                <div class="widget-body no-padding">
                    <form id="redis-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>host</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('text','row[ini][REDIS_HOST]',
                                            $nft['ini']['REDIS_HOST']??'127.0.0.1', ['data-rule'=>'required']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>端口</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[ini][REDIS_PORT]',$nft['ini']['REDIS_PORT'] ?? 6379, ['data-rule'=>'required']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>密码</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[ini][REDIS_PASSWORD]',
                                            $nft['ini']['REDIS_PASSWORD']??'', []); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>库</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[ini][REDIS_DB]',
                                            $nft['ini']['REDIS_DB']??1, ['data-rule'=>'required']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="withdraw">
                <div class="widget-body no-padding">
                    <form id="withdraw-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>提现开关</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[withdraw][state]', $nft['ini']['state']??'0', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>手续费(%)</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[withdraw][servicefee]',
                                            $nft['withdraw']['servicefee']??'0', ['data-rule'=>'required','min'=>0,'step'=>'0.1','data-tip'=>'提现手续费百分比']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>提现金额限制</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[withdraw][minmoney]',
                                            $nft['withdraw']['minmoney']??'0', ['data-rule'=>'required','min'=>0,'step'=>'0.1','data-tip'=>'最小提现金额']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>月提现次数</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[withdraw][monthlimit]',
                                            $nft['withdraw']['monthlimit']??'0', ['data-rule'=>'required','min'=>0,'step'=>'1','data-tip'=>'每月最多提现次数']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>提现规则</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::textarea('row[withdraw][rule]',$nft['withdraw']['rule'] ?? '', ['rows'=>10]); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="badge">
                <div class="widget-body no-padding">
                    <form id="marketing-form" class="edit-form form-horizontal" role="form" data-toggle="validator"
                          method="POST" action="<?php echo url('nft.client/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%">配置项</th>
                                <th width="85%">参数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>活动开关</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[marketing][badge_no]', $nft['marketing']['badge_no']??'0', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>徽章转赠</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::switcher('row[marketing][give_status]', $nft['marketing']['give_status']??'0', ['color'=>'success']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>规则文案id</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][badge_rule]',
                                            $nft['marketing']['badge_rule']??'', ['data-rule'=>'required','placeholder'=>'徽章玩法说明文案id']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>玄铁徽章藏品id</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][base_badge_id]',
                                            $nft['marketing']['base_badge_id']??'', ['data-rule'=>'required','placeholder'=>'玄铁徽章藏品id']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>合成条件</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][badge_up_number]',
                                            $nft['marketing']['badge_up_number']??'', ['data-rule'=>'required','placeholder'=>'合成升级条件','data-tip'=>'需邀请人员数量']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>青铜徽章藏品id</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('number','row[marketing][up_badge_id]',
                                            $nft['marketing']['up_badge_id']??'', ['data-rule'=>'required','placeholder'=>'青铜徽章藏品id']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>升级提示</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php echo Form::input('text','row[marketing][up_badge_text]',
                                            $nft['marketing']['up_badge_text']??'', ['data-rule'=>'required','placeholder'=>'升级提示 请用XX 代替 徽章等级','data-tip'=>'升级提示 请用XX 代替 徽章等级']); ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>
                                    <button type="submit" class="btn btn-success btn-embossed">确定</button>
                                    <button type="reset" class="btn btn-default btn-embossed">重置</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    [v-cloak] {
        display: none;
    }

    @media (max-width: 375px) {
        .edit-form tr td input {
            width: 100%;
        }

        .edit-form tr th:first-child,
        .edit-form tr td:first-child {
            width: 20%;
        }

        .edit-form tr th:nth-last-of-type(-n+2),
        .edit-form tr td:nth-last-of-type(-n+2) {
            display: none;
        }
    }

    .edit-form table > tbody > tr td a.btn-delcfg {
        visibility: hidden;
    }

    .edit-form table > tbody > tr:hover td a.btn-delcfg {
        visibility: visible;
    }

    .input-group.url .input-group-addon {
        background-color: #e8edf0;
        color: #868686;
        border-color: #d2d6de;
    }
</style>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
