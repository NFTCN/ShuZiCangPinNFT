CREATE TABLE IF NOT EXISTS `__PREFIX__nft_pay_log`
(
    `id`            int(11)                                                       NOT NULL AUTO_INCREMENT,
    `user_id`       int(11)                                                       NOT NULL DEFAULT '0' COMMENT '用户ID',
    `type`          varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT '0' COMMENT '支付方式',
    `ref_type`      varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL COMMENT '业务类型',
    `order_id`      int(11)                                                       NOT NULL COMMENT '订单id',
    `out_trade_no`  varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
    `amount`        decimal(10, 2)                                                NOT NULL DEFAULT '0.00' COMMENT '支付金额',
    `title`         varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付标题',
    `status`        tinyint(4)                                                    NOT NULL DEFAULT '0' COMMENT '状态',
    `msg`           varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付信息',
    `trade_no`      varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付平台订单号',
    `trade_create`  int(11)                                                                DEFAULT NULL COMMENT '交易创建时间',
    `trade_payment` int(11)                                                                DEFAULT NULL COMMENT '交易付款时间',
    `callback`      varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内部事件',
    `ori_data`      text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '回调数据',
    `lack_ver`      int(11)                                                                DEFAULT NULL,
    `created_at`    int(11)                                                                DEFAULT NULL,
    `updated_at`    int(11)                                                                DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='支付记录';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_order_goods`
(
    `id`           int(10) unsigned                                              NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `order_id`     int(10) unsigned                                              NOT NULL    DEFAULT '0' COMMENT '订单ID',
    `goods_id`     int(10)                                                       NOT NULL    DEFAULT '0' COMMENT '商品ID',
    `title`        text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL COMMENT '产品标题',
    `image`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL    DEFAULT '' COMMENT '商品主图',
    `price`        decimal(10, 2) unsigned                                       NOT NULL    DEFAULT '0.00' COMMENT '价格',
    `number`       int(10) unsigned                                              NOT NULL    DEFAULT '0' COMMENT '数量',
    `createtime`   int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`   int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`   int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `status`       enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    `fee`          decimal(11, 2) unsigned                                       NOT NULL    DEFAULT '0.00' COMMENT '费率',
    `goods_status` tinyint(1)                                                                DEFAULT '0' COMMENT '链交易状态:0-等待,1-完成',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='订单商品表';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_order`
(
    `id`             int(11)                                                       NOT NULL AUTO_INCREMENT,
    `user_id`        int(11)                                                       NOT NULL DEFAULT '0' COMMENT '用户id',
    `order_no`       varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '订单号',
    `remarks`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户备注',
    `original_price` decimal(10, 2) unsigned                                       NOT NULL DEFAULT '0.00' COMMENT '订单原始价格',
    `pay_price`      decimal(10, 2) unsigned                                       NOT NULL DEFAULT '0.00' COMMENT '订单支付价格',
    `pay_status`     tinyint(1)                                                    NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付,2-待支付,3-支付失败',
    `pay_time`       datetime                                                               DEFAULT NULL COMMENT '支付时间',
    `pay_type`       tinyint(1)                                                    NOT NULL DEFAULT '0' COMMENT '支付类型:1-支付宝,2-微信,3-余额',
    `created_at`     int(11)                                                                DEFAULT NULL COMMENT '创建时间',
    `updated_at`     int(11)                                                                DEFAULT NULL COMMENT '更新时间',
    `is_delete`      tinyint(1)                                                    NOT NULL DEFAULT '0' COMMENT '是否删除',
    `order_status`   tinyint(1)                                                             DEFAULT '0' COMMENT '订单状态:0=待支付,1-交易中,2-已完成',
    `order_type`     tinyint(1)                                                             DEFAULT '1' COMMENT '订单类型:1=平台交易,2=市场交易',
    `pay_limit_time` int(11)                                                                DEFAULT '0' COMMENT '支付截止时间',
    `deleteTime`     int(11)                                                                DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `order_no` (`order_no`) USING BTREE,
    KEY `user_id` (`user_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT ='订单表';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_air_drop`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `collection_id` int(10)                                                                   DEFAULT NULL COMMENT '非卖品藏品id',
    `user_id`       int(10)                                                                   DEFAULT '1' COMMENT '用户id',
    `limit_time`    int(11)                                                                   DEFAULT '0' COMMENT '领取限时',
    `createtime`    int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`    int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`    int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `weigh`         int(10)                                                                   DEFAULT '0' COMMENT '权重',
    `status`        enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    `state`         enum ('0','1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci       DEFAULT '0' COMMENT '领取状态:0=未领取,1=已领取,2=已失效',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='空投记录';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_collection_log`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `tokenId`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '藏品唯一标识',
    `owner`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所有者',
    `hash_no`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易哈希值',
    `createtime` int(10)                                                       DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)                                                       DEFAULT NULL COMMENT '更新时间',
    `deletetime` int(10)                                                       DEFAULT NULL COMMENT '删除时间',
    `status`     tinyint(1)                                                    DEFAULT '1' COMMENT '状态 1-有效 0-无效',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `tokenId` (`tokenId`(191)) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='藏品交易记录';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_collection`
(
    `id`           int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `author_id`    int(10) unsigned                                                              DEFAULT '0' COMMENT '作者id',
    `issuer_id`    int(10)                                                                       DEFAULT NULL COMMENT '发行方id',
    `category_id`  int(10)                                                                       DEFAULT NULL COMMENT '系列id',
    `type`         enum ('market','unsaleable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型:unsaleable=非卖品,market=收藏品',
    `title`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT '' COMMENT '藏品名',
    `image`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT '' COMMENT '缩略图',
    `master_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT NULL COMMENT '主图',
    `description`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT '' COMMENT '描述',
    `price`        decimal(10, 2) unsigned                                                       DEFAULT '0.00' COMMENT '价格',
    `stock`        int(11)                                                                       DEFAULT '0' COMMENT '限量',
    `level`        enum ('0','1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci       DEFAULT '0' COMMENT '等级:0=普通,1=普通,2=传奇,3=史诗',
    `market`       int(11)                                                                       DEFAULT '0' COMMENT '销售数量',
    `views`        int(10) unsigned                                                              DEFAULT '0' COMMENT '点击',
    `startdate`    date                                                                          DEFAULT NULL COMMENT '开始日期',
    `times`        time                                                                          DEFAULT NULL COMMENT '时间',
    `refreshtime`  int(10)                                                                       DEFAULT NULL COMMENT '刷新时间(int)',
    `createtime`   int(10)                                                                       DEFAULT NULL COMMENT '创建时间',
    `updatetime`   int(10)                                                                       DEFAULT NULL COMMENT '更新时间',
    `deletetime`   int(10)                                                                       DEFAULT NULL COMMENT '删除时间',
    `weigh`        int(10)                                                                       DEFAULT '0' COMMENT '权重',
    `status`       enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     DEFAULT 'normal' COMMENT '状态',
    `state`        enum ('0','1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci           DEFAULT '0' COMMENT '状态值:0=仓库,1=上架,2=售罄',
    `text_color`   varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT NULL COMMENT '文本框颜色',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='收藏品';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_collection`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id`       int(10)                                                       DEFAULT '0' COMMENT '接收人id',
    `title`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '藏品名称',
    `image`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片',
    `owner`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所有者',
    `tokenId`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '藏品的哈希值',
    `author`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '作家',
    `level`         varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '等级',
    `no`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '编号',
    `collection_id` int(11)                                                       DEFAULT NULL COMMENT '藏品id',
    `createtime`    int(10)                                                       DEFAULT NULL COMMENT '创建时间',
    `updatetime`    int(10)                                                       DEFAULT NULL COMMENT '更新时间',
    `deletetime`    int(10)                                                       DEFAULT NULL COMMENT '删除时间',
    `status`        tinyint(1)                                                    DEFAULT '1' COMMENT '状态 1-有效 0-无效',
    `hash_no`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '最新的交易哈希',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `tokenId` (`tokenId`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='用户藏品';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_box`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `collection_id` int(10)                                                                   DEFAULT NULL COMMENT '非卖品藏品id',
    `level`         enum ('0','1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci   DEFAULT '1' COMMENT '等级:1=普通,2=传奇,3=史诗',
    `people_limit`  tinyint(1) unsigned                                                       DEFAULT '0' COMMENT '获取人数限制',
    `market`        int(11)                                                                   DEFAULT '0' COMMENT '销售数量',
    `createtime`    int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`    int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`    int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `weigh`         int(10)                                                                   DEFAULT '0' COMMENT '权重',
    `status`        enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='盲盒';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_article`
(
    `id`         int(11) unsigned                                                NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `admin_id`   int(11) unsigned                                                NOT NULL COMMENT '发布人',
    `title`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL COMMENT '标题',
    `content`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci           NOT NULL COMMENT '内容',
    `image`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL COMMENT '缩略图',
    `view_num`   int(11) unsigned                                                NOT NULL DEFAULT '0' COMMENT '浏览次数',
    `status`     enum ('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '展示状态:1=展示,0=隐藏',
    `deletetime` int(11) unsigned                                                         DEFAULT NULL COMMENT '删除时间',
    `createtime` int(11) unsigned                                                         DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(11) unsigned                                                         DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT ='文章表';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_message`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id`    int(10)    DEFAULT '0' COMMENT '接收人id',
    `link_id`    int(10)    DEFAULT NULL COMMENT '公告id',
    `createtime` int(10)    DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)    DEFAULT NULL COMMENT '更新时间',
    `deletetime` int(10)    DEFAULT NULL COMMENT '删除时间',
    `status`     tinyint(1) DEFAULT '1' COMMENT '状态 1-有效 0-无效',
    `is_view`    tinyint(1) DEFAULT '0' COMMENT '是否展示过',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='消息通知';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_identify`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id`    int(10)                                                                   DEFAULT '0' COMMENT '会员ID',
    `identify`   char(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT '0' COMMENT '身份证',
    `link_md5`   char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                 DEFAULT NULL COMMENT '链地址',
    `createtime` int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime` int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `switch`     tinyint(1)                                                                DEFAULT '0' COMMENT '开关',
    `status`     enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    `salt`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci             DEFAULT NULL COMMENT '盐',
    `name`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci             DEFAULT NULL COMMENT '实名',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='实名认证表';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_notice`
(
    `id`         int(11) unsigned                                                NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `admin_id`   int(11) unsigned                                                NOT NULL COMMENT '发布人',
    `title`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL COMMENT '标题',
    `content`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci           NOT NULL COMMENT '内容',
    `image`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL COMMENT '缩略图',
    `view_num`   int(11) unsigned                                                NOT NULL DEFAULT '0' COMMENT '浏览次数',
    `status`     enum ('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '展示状态:1=展示,0=隐藏',
    `deletetime` int(11) unsigned                                                         DEFAULT NULL COMMENT '删除时间',
    `createtime` int(11) unsigned                                                         DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(11) unsigned                                                         DEFAULT NULL COMMENT '更新时间',
    `is_send`    tinyint(1)                                                               DEFAULT '0' COMMENT '是否发送广播 1-是 0-否',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT ='公告';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_issuer`
(
    `id`          int(10) unsigned                                              NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL    DEFAULT '' COMMENT '名称',
    `image`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci             DEFAULT '' COMMENT '图片',
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci             DEFAULT '' COMMENT '描述',
    `createtime`  int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`  int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`  int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `weigh`       int(10)                                                                   DEFAULT '0' COMMENT '权重',
    `status`      enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='发行方';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_category`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `pid`         int(10) unsigned NOT NULL                                                        DEFAULT '0' COMMENT '父ID',
    `type`        varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                     DEFAULT '' COMMENT '栏目类型',
    `name`        varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                     DEFAULT '',
    `nickname`    varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                     DEFAULT '',
    `flag`        set ('hot','index','recommend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
    `image`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                    DEFAULT '' COMMENT '图片',
    `keywords`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                    DEFAULT '' COMMENT '关键字',
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                    DEFAULT '' COMMENT '描述',
    `diyname`     varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                     DEFAULT '' COMMENT '自定义名称',
    `createtime`  int(10)                                                                          DEFAULT NULL COMMENT '创建时间',
    `updatetime`  int(10)                                                                          DEFAULT NULL COMMENT '更新时间',
    `weigh`       int(10)          NOT NULL                                                        DEFAULT '0' COMMENT '权重',
    `status`      varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                     DEFAULT '' COMMENT '状态',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `weigh` (`weigh`, `id`) USING BTREE,
    KEY `pid` (`pid`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='分类信息表';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_banner`
(
    `id`         int(11) unsigned                                                             NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `article_id` int(11) unsigned                                                             NOT NULL COMMENT '文章',
    `image`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci                NOT NULL COMMENT '图片',
    `status`     enum ('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci              NOT NULL DEFAULT '1' COMMENT '展示状态:1=展示,0=隐藏',
    `createtime` int(11) unsigned                                                                      DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(11) unsigned                                                                      DEFAULT NULL COMMENT '更新时间',
    `pages`      enum ('activity','article') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'article' COMMENT '落地页:activity=盲盒,article=公告',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT ='轮播图广告';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_friend`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `pid`        int(10) unsigned NOT NULL DEFAULT '0' COMMENT '好友id',
    `user_id`    int(10)          NOT NULL DEFAULT '0' COMMENT '用户id',
    `createtime` int(10)                   DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)                   DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `pid` (`pid`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='用户好友关系';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_collection_give_log`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id`    int(10)                                                       DEFAULT '0' COMMENT '接收人id',
    `owner`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '接收者',
    `tokenId`    varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '藏品的哈希值',
    `createtime` int(10)                                                       DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)                                                       DEFAULT NULL COMMENT '更新时间',
    `deletetime` int(10)                                                       DEFAULT NULL COMMENT '删除时间',
    `status`     tinyint(1)                                                    DEFAULT '1' COMMENT '状态 1-有效 0-无效',
    `hash_no`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易哈希值',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `user_id` (`user_id`) USING BTREE,
    KEY `tokenId` (`tokenId`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='藏品赠送记录';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_box_log`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `collection_id` int(10)                                                                   DEFAULT NULL COMMENT '非卖品藏品id',
    `user_id`       int(10)                                                                   DEFAULT NULL COMMENT '用户id',
    `box_id`        int(11)                                                                   DEFAULT '1' COMMENT '盒子id',
    `createtime`    int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`    int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`    int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `status`        enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    `state`         tinyint(1)                                                                DEFAULT '1' COMMENT '状态:1=待领取,2-已领取',
    `level`         tinyint(1)                                                                DEFAULT NULL COMMENT '等级:1=普通,2=史诗,3=传说',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='用户盲盒';


CREATE TABLE IF NOT EXISTS `__PREFIX__nft_user_activity`
(
    `id`         int(10) unsigned                                              NOT NULL AUTO_INCREMENT,
    `user_id`    int(10)                                                       NOT NULL DEFAULT '0' COMMENT '用户id',
    `num`        int(10)                                                       NOT NULL DEFAULT '0' COMMENT '活动次数',
    `type`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'box' COMMENT '活动类型',
    `createtime` int(10)                                                                DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(10)                                                                DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='用户好友关系';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_notice_people`
(
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id`    int(11) unsigned NOT NULL COMMENT '用户id',
    `notice_id`  int(11)          NOT NULL COMMENT '公告id',
    `deletetime` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `createtime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT ='公告接受人';

CREATE TABLE IF NOT EXISTS `__PREFIX__nft_author`
(
    `id`          int(10) unsigned                                              NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL    DEFAULT '' COMMENT '名称',
    `avatar`      varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL    DEFAULT '' COMMENT '图片',
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL    DEFAULT '' COMMENT '描述',
    `genderdata`  enum ('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci   DEFAULT 'male' COMMENT '性别(单选):male=男,female=女',
    `createtime`  int(10)                                                                   DEFAULT NULL COMMENT '创建时间',
    `updatetime`  int(10)                                                                   DEFAULT NULL COMMENT '更新时间',
    `deletetime`  int(10)                                                                   DEFAULT NULL COMMENT '删除时间',
    `weigh`       int(10)                                                                   DEFAULT '0' COMMENT '权重',
    `status`      enum ('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '状态',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='创作者';