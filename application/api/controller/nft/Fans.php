<?php

namespace app\api\controller\nft;

use addons\nft\library\Poster;
use addons\nft\model\UserCollection;
use app\common\controller\NftApi;

/**
 * 分享
 */
class Fans extends NftApi
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 盲盒海报
     */
    public function box_poster()
    {
        $config = get_addon_config('nft');
        $poster_image = $config['ini']['box_poster'] ?? '';
        $user = $this->auth->getUserinfo();
        $host = $this->request->host();
        $scheme = $this->request->scheme();
        $url = $scheme . '://' . $host . '/h5/index.html#/?qid=' . $this->auth->id;
        $pathInfo = pathinfo(cdnurl($poster_image, true));
        $poster = new Poster([
            'tpl' => cdnurl($poster_image, true),
            'qrcode_url' => $url,
            'uid' => $user['id'],
            'filename' => $pathInfo['filename']
        ]);
        $poster_image = $scheme . '://' . $host . $poster->setPlant('generateTpl')->run();

        $this->success('获取成功', [
            'url' => $url,
            'poster' => $poster_image,
            'title' => config('site.name'),
            'content' => $config['ini']['poster_text'],
            'logo' => cdnurl($config['ini']['logo'], true)
        ]);
    }

    /**
     * 藏品炫耀
     * @param null $token_id
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collection_poster($token_id = null)
    {
        $info = UserCollection::where('tokenId', $token_id)->where('user_id', $this->auth->id)->find();

        if (empty($info)) {
            $this->error('未找到相关藏品');
        }
        $config = get_addon_config('nft');

        $user = $this->auth->getUserinfo();
        $host = $this->request->host();
        $scheme = $this->request->scheme();
        $url = $scheme . '://' . $host . '/h5/index.html#/?qid=' . $this->auth->id;
        $poster = new Poster([
            'tpl' => cdnurl('/assets/addons/nft/img/collection_poter.png', true),
            'qrcode_url' => $url,
            'uid' => $user['id'],
            'filename' => $info['hash_no'],
            'collection' => $info
        ]);
        $poster_image = $scheme . '://' . $host . $poster->setPlant('collection')->run();
        $this->success('获取成功', [
            'url' => $url,
            'poster' => $poster_image,
            'title' => config('site.name'),
            'content' => $config['ini']['poster_text'],
            'logo' => cdnurl($config['ini']['logo'], true)
        ]);

    }

    /**
     * 藏品头像
     * @param null $token_id
     */
    public function collection_avatar($token_id = null)
    {
        $info = UserCollection::where('tokenId', $token_id)->where('user_id', $this->auth->id)->find();

        if (empty($info)) {
            $this->error('未找到相关藏品');
        }
        $config = get_addon_config('nft');

        $user = $this->auth->getUserinfo();
        $host = $this->request->host();
        $scheme = $this->request->scheme();
        $poster = new Poster([
            'tpl' => cdnurl($info->image, true),
            'qrcode_url' => '',
            'uid' => $user['id'],
            'filename' => $info['hash_no'],
            'collection' => $info
        ]);
        $poster_image = $scheme . '://' . $host . $poster->setPlant('collection_avatar')->run();
        $this->success('获取成功', [
            'url' => '',
            'poster' => $poster_image,
            'title' => config('site.name'),
            'content' => $config['ini']['poster_text'],
            'logo' => cdnurl($config['ini']['logo'], true)
        ]);
    }


}
