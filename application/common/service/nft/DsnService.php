<?php


namespace app\common\service\nft;


use addons\nft\model\UserCollectionLog;

class DsnService
{
    /**
     * @var DsnService
     */
    protected static $instance = null;
    public $error = '';
    protected $config = [];
    protected $options = [];
    protected $hashMethod = 'md5';
    /**
     * @var string
     */
    private $tokenId;
    /**
     * @var mixed|null
     */

    private $hash = '';

    public function __construct($options = [])
    {
        $this->options = array_merge($this->config, $options);
    }

    /**
     * @param array $options
     *
     * @return NftService
     */
    public static function instance($options = []): ?NftService
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * @param string $tokenId
     */
    public function setTokenId(string $tokenId): NftService
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    /**
     * 生成唯一标识
     *
     * @param string $salt 随机字符串
     * @param string $author 作者
     * @param string $nft 艺术品Link
     */
    public function createTokenId(string $salt, string $author, string $nft): NftService
    {
        $nft = hash_file($this->hashMethod, cdnurl($nft,true));
        $this->tokenId = ($this->hashMethod)($salt . $author . $nft);
        return $this;

    }

    public function createHash($name)
    {
        if (empty($this->hash)) {
            $this->hash = ($this->hashMethod)($this->tokenId . $name);
        } else {
            $this->hash = ($this->hashMethod)($this->hash . $name);
        }
        $this->saveLog($name);
        return $this;
    }

    /**
     * 生成日志
     *
     * @param $name
     * @param $salt
     */
    public function saveLog($name)
    {
        UserCollectionLog::create([
            'tokenId' => $this->tokenId,
            'owner' => $name,
            'hash_no' => $this->hash,
        ]);
    }

    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed|null $hash
     */
    public function setHash($hash): bool
    {

        if ($this->checkHash()) {
            if ($this->hash === $hash) {
                return true;
            }
        }
        $this->setError('链路已破坏');
        return false;
    }

    /**
     * 检查链路是否正确
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkHash($name = ''): bool
    {
        $list = UserCollectionLog::where('tokenId', $this->tokenId)->order('createtime desc')->select();
        $hash = '';
        foreach ($list as $item) {
            if (empty($this->hash)) {
                $hash = ($this->hashMethod)($this->tokenId . $item->owner);
            } else {
                $hash = ($this->hashMethod)($this->hash . $item->owner);
            }
            if ($hash == $item->hash_no) {
                $this->hash = $hash;
            }
        }
        //最后一条记录
//        $info = UserCollectionLog::where('tokenId', $this->tokenId)->order('id desc')->limit(2)->select();
//        if (empty($info)) {
//            return false;
//        }
//        if (count($info) > 1) {
//            $this->hash = ($this->hashMethod)($info[1]->hash_no . $info[0]->owner);
//        } else {
//            $this->hash = ($this->hashMethod)($this->tokenId . $info[0]->owner);
//        }
        return true;

    }

    /**
     * @param string $error
     */
    public function setError(string $error): NftService
    {
        $this->error = $error;
        return $this;
    }


}
