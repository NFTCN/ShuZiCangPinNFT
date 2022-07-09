<?php

namespace addons\nft\library;

use addons\nft\model\UserCollection;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use think\helper\Str;

/**
 * 海报生成
 */
class Poster
{
    public $data = [];
    public $baseTpl = "";
    public $image = null;
    public $dirRoot = "";
    public $oss;


    protected $plant;
    /**
     * @var UserCollection|mixed
     */
    private $collection;
    /**
     * @var array|bool|\PDOStatement|string|\think\Model|null
     */
    private $userInfo;
    /**
     * @var ImageManager
     */
    private $manager;
    /**
     * @var mixed
     */
    private $filename;

    public function __construct($data)
    {
        $this->data = $data;
        if (empty($data['uid']) || empty($data['tpl']) || empty($data['filename'])) {
            return false;
        }
        $this->dirRoot = ROOT_PATH . "public";
        $this->baseTpl = $data['tpl'];
        $this->manager = new ImageManager(array('driver' => 'gd'));
        $this->userInfo = \app\admin\model\User::where('id', $this->data['uid'])->find();
        $this->filename = $data['filename'];
        $this->collection = $data['collection'] ?? new UserCollection();
    }

    /**
     * @param mixed $plant
     */
    public function setPlant($plant): Poster
    {
        $this->plant = $plant;
        return $this;
    }

    /**
     * 生成海报图
     */
    public function run()
    {
        $filePath = DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . $this->userInfo['id'];
        if (!is_dir(ROOT_PATH . 'public' . $filePath)) {
            @mkdir(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . 'qrcode', 0777, true);
            @mkdir(ROOT_PATH . 'public' . $filePath, 0777, true);
        }
        $filePath .= DIRECTORY_SEPARATOR.$this->filename.'.png';
        if (!file_exists(ROOT_PATH.'public'.$filePath)){
            $poster = $this->getPoster();
            file_put_contents(ROOT_PATH.'public'.$filePath, $poster->stream('jpg', 85)->getContents());
        }
        return $filePath;
    }

    /**
     * 生成海报入口
     * @return Image|string
     * @throws Exception
     */
    public function getPoster()
    {
        $poster = $this->{$this->plant}();
        if(!empty($this->data['qrcode_url'])){
            $poster = $this->updateQrcode($poster);
        }
        return $poster;
    }

    /**
     * 添加二维码(右下角)
     */
    private function updateQrcode($poster)
    {
        $qrImage = $this->getQrImage($this->data['qrcode_url']);
        $poster->insert($qrImage, 'bottom-right', 20, 20);

        return $poster;
    }

    /**
     * @param $qrcode_url
     *
     * @return \Intervention\Image\Image
     * @throws Exception
     */
    public function getQrImage($qrcode_url)
    {
        $qrCode = new \Endroid\QrCode\QrCode($qrcode_url);
        $qrCode->setMargin(0);
        $qrCode->setSize(120);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium());
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrImage = $this->manager->make($result->getString());
        return $qrImage;
    }

    /**
     * 生成海报
     */
    private function generateTpl()
    {
        // 打开基础模板
        $goodsTpl = $this->manager->make($this->baseTpl)->resize(750, 1334);
        //邀请码
//        $invite_code = '邀请码:';
//        $goodsTpl->text($invite_code, 280, 1150, function ($font) {
//            $font->file(ROOT_PATH.'public/assets/fonts/YaHeiConsolasHybrid.ttf');
//            $font->size(28);
//            $font->color('#333333');
//            $font->valign('top');
//            $font->angle(0);
//        });
//        $goodsTpl->text($this->userInfo['invitecode'], 378, 1152, function ($font) {
//            $font->file(ROOT_PATH.'public/assets/fonts/YaHeiConsolasHybrid.ttf');
//            $font->size(32);
//            $font->color('#333333');
//            $font->valign('top');
//            $font->angle(0);
//        });


        return $goodsTpl;
    }

    /**
     * 生成收藏海报
     */
    private function collection()
    {
        // 打开基础模板
        $goodsTpl = $this->manager->make($this->baseTpl)->resize(750, 1514);
        $collection = (new ImageManager(array('driver' => 'gd')))->make(cdnurl($this->collection->image))->resize(470, 470);
        $goodsTpl->insert($collection, 'center', 0, -353);
        $title_length = mb_strlen($this->collection->title) * 40;
//        if($title_length < 200){
//            $title_length = 200;
//        }
        $line_limit = bcdiv(bcsub(750, ((93 * 2) + ((31 * 2) + $title_length) + 80)), 2);

        $title_left = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/left.png'))->resize(93, 47);
        $title_right = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/right.png'))->resize(93, 47);
        $left_x = $line_limit + 40;
        $right_x = $line_limit + 40 + $title_length + 93+(31 * 2);
        $goodsTpl->insert($title_left, 'top-left',$left_x , 720);

        /**
         * 藏品
         */
        $goodsTpl->text($this->collection->title, $left_x + 93 + 31, 755, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(40);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });
        $goodsTpl->insert($title_right, 'top-left',$right_x, 720);
        $author = '创作者:'.$this->collection->author;
        $author_length = mb_strlen($author) * 24;
        $line_limit = bcdiv(bcsub(750, $author_length), 2);

        /**
         * 作家
         */
        $goodsTpl->text($author, $line_limit, 820, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(24);
            $font->color('#CCCCCC');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });


        $goodsTpl->text('收藏者', 344, 930, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(24);
            $font->color('#CCCCCC');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });

        /**
         * 拥有人
         */
        $owner = $this->collection->owner;
        if(mb_strlen($owner) > 12){
            $owner = substr($owner, 0,12).'..';
        }
        $owner_length = mb_strlen($owner) * 36;

        $line_limit = bcdiv(bcsub(750, $owner_length), 2);
        $goodsTpl->text($owner, $line_limit+70, 990, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(36);
            $font->color('#FFFFFF');
            $font->align('left');
            $font->valign('middle');
            $font->angle(0);
        });

        $goodsTpl->text('收藏编号', 332, 1063, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(24);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });

        /**
         * 水印
         */
//        $chapter = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/chapter.png'))->resize(193, 193);
//        $goodsTpl->insert($chapter, 'top-left',474 , 1063);

        $label = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/label.png'))->resize(36, 36);
        $goodsTpl->insert($label, 'top-left',223 , 1094);

        $blank = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/blank.png'))->resize(300, 36);
        $goodsTpl->insert($blank, 'top-left',224 , 1095);
        //编号
        $no_length = mb_strlen($this->collection->no) *22;
        $additions = 0;
        if($no_length < 223){
            $additions = bcdiv(bcsub(223, $no_length), 2);
        }
        $goodsTpl->text($this->collection->no, 273 + $additions, 1112, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(22);
            $font->color('#FBC223');
            $font->align('left');
            $font->valign('middle');
            $font->angle(0);
        });

        $goodsTpl->text('生成时间', 332, 1176, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(24);
            $font->color('#CCCCCC');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });

        /**
         * 时间
         */
        $goodsTpl->text(date('Y-m-d H:i:s',$this->collection->createtime), 192, 1237, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(36);
            $font->color('#CCCCCC');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });
        /**
         * 文案
         */
        $goodsTpl->text('集头像合成限量藏品', 49, 1400, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(45);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });
        $goodsTpl->text('我已经合成了,你也来看看吧', 49, 1450, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(24);
            $font->color('#CCCCCC');
            $font->align('center');
            $font->valign('left');
            $font->angle(0);
        });
        return $goodsTpl;
    }

    private function collection_avatar(){
        // 打开基础模板
        $goodsTpl = $this->manager->make($this->baseTpl)->resize(800, 800);

        $blank = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/background.png'))->resize(300, 36);
        $goodsTpl->insert($blank, 'top-left',250 , 650);
        $label = (new ImageManager(array('driver' => 'gd')))->make(cdnurl('/assets/addons/nft/img/label-black.png'))->resize(24, 24);
        $goodsTpl->insert($label, 'top-left',265 , 655);
        //编号
        $no_length = mb_strlen($this->collection->no) *22;
        $additions = 0;
        if($no_length < 200){
            $additions = bcdiv(bcsub(200, $no_length), 2);
        }
        $goodsTpl->text($this->collection->no, 265+$additions, 665, function ($font) {
            $font->file(ROOT_PATH . 'public/assets/fonts/YaHeiConsolasHybrid.ttf');
            $font->size(22);
            $font->color('#000000');
            $font->align('left');
            $font->valign('middle');
            $font->angle(0);
        });

        return $goodsTpl;
    }
}
