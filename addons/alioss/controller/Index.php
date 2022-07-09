<?php

namespace addons\alioss\controller;

use app\common\exception\UploadException;
use app\common\library\Upload;
use app\common\model\Attachment;
use OSS\Core\OssException;
use OSS\OssClient;
use think\addons\Controller;
use think\Config;

/**
 * 阿里OSS云储存
 *
 */
class Index extends Controller
{

    public function _initialize()
    {
        //跨域检测
        check_cors_request();

        parent::_initialize();
        Config::set('default_return_type', 'json');
    }

    public function index()
    {
        Config::set('default_return_type', 'html');
        $this->error("当前插件暂无前台页面");
    }

    /**
     * 获取签名
     */
    public function params()
    {
        $this->check();
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $chunk = $this->request->post('chunk');

        $auth = new \addons\alioss\library\Auth();
        $params = $auth->params($name, $md5);
        $params['OSSAccessKeyId'] = $params['id'];
        $params['success_action_status'] = 200;
        $config = get_addon_config('alioss');

        if ($chunk) {
            $oss = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
            // 初始化
            $fileSize = $this->request->post('size');
            $chunkSize = $this->request->post('chunksize');
            $uploadId = $oss->initiateMultipartUpload($config['bucket'], $params['key']);
            $params['uploadId'] = $uploadId;
            $params['parts'] = $oss->generateMultiuploadParts($fileSize, $chunkSize);
            $params['partsAuthorization'] = [];
            $date = gmdate('D, d M Y H:i:s \G\M\T');
            foreach ($params['parts'] as $index => $part) {
                $partNumber = $index + 1;
                $signstr = "PUT\n\n\n{$date}\nx-oss-date:{$date}\n/{$config['bucket']}/{$params['key']}?partNumber={$partNumber}&uploadId={$uploadId}";
                $authorization = base64_encode(hash_hmac('sha1', $signstr, $config['accessKeySecret'], true));
                $params['partsAuthorization'][$index] = $authorization;
            }
            $params['date'] = $date;
        }

        $this->success('', null, $params);
        return;
    }

    /**
     * 服务器中转上传文件
     * 上传分片
     * 合并分片
     * @param bool $isApi
     */
    public function upload($isApi = false)
    {
        if ($isApi === true) {
            if (!$this->auth->isLogin()) {
                $this->error("请登录后再进行操作");
            }
        } else {
            $this->check();
        }
        $config = get_addon_config('alioss');
        $oss = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);

        //检测删除文件或附件
        $checkDeleteFile = function ($attachment, $upload, $force = false) use ($config) {
            //如果设定为不备份则删除文件和记录 或 强制删除
            if ((isset($config['serverbackup']) && !$config['serverbackup']) || $force) {
                if ($attachment && !empty($attachment['id'])) {
                    $attachment->delete();
                }
                if ($upload) {
                    //文件绝对路径
                    $filePath = $upload->getFile()->getRealPath() ?: $upload->getFile()->getPathname();
                    @unlink($filePath);
                }
            }
        };

        $chunkid = $this->request->post("chunkid");
        if ($chunkid) {
            $action = $this->request->post("action");
            $chunkindex = $this->request->post("chunkindex/d");
            $chunkcount = $this->request->post("chunkcount/d");
            $filesize = $this->request->post("filesize");
            $filename = $this->request->post("filename");
            $method = $this->request->method(true);
            $key = $this->request->post("key");
            $uploadId = $this->request->post("uploadId");

            if ($action == 'merge') {
                $attachment = null;
                $upload = null;
                //合并分片
                if ($config['uploadmode'] == 'server') {
                    //合并分片文件
                    try {
                        $upload = new Upload();
                        $attachment = $upload->merge($chunkid, $chunkcount, $filename);
                    } catch (UploadException $e) {
                        $this->error($e->getMessage());
                    }
                }

                $etags = $this->request->post("etags/a", []);
                if (count($etags) != $chunkcount) {
                    $checkDeleteFile($attachment, $upload, true);
                    $this->error("分片数据错误");
                }
                $listParts = [];
                for ($i = 0; $i < $chunkcount; $i++) {
                    $listParts[] = array("PartNumber" => $i + 1, "ETag" => $etags[$i]);
                }
                try {
                    $ret = $oss->completeMultipartUpload($config['bucket'], $key, $uploadId, $listParts);
                } catch (\Exception $e) {
                    $checkDeleteFile($attachment, $upload, true);
                    $this->error($e->getMessage());
                }

                $result = json_decode(json_encode(simplexml_load_string($ret['body'], "SimpleXMLElement", LIBXML_NOCDATA)), true);
                if (!isset($result['Key'])) {
                    $checkDeleteFile($attachment, $upload, true);
                    $this->error("上传失败");
                } else {
                    $checkDeleteFile($attachment, $upload);
                    $this->success("上传成功", '', ['url' => "/" . $key, 'fullurl' => cdnurl("/" . $key, true)]);
                }
            } else {
                //默认普通上传文件
                $file = $this->request->file('file');
                try {
                    $upload = new Upload($file);
                    $file = $upload->chunk($chunkid, $chunkindex, $chunkcount);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                try {
                    //上传分片到OSS
                    $ret = $oss->uploadPart($config['bucket'], $key, $uploadId, ['fileUpload' => $file->getRealPath(), 'partNumber' => $chunkindex + 1]);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }

                $this->success("上传成功", "", [], 3, ['ETag' => $ret]);
            }
        } else {
            $attachment = null;
            //默认普通上传文件
            $file = $this->request->file('file');
            try {
                $upload = new Upload($file);
                $attachment = $upload->upload();
            } catch (UploadException $e) {
                $this->error($e->getMessage());
            }

            //文件绝对路径
            $filePath = $upload->getFile()->getRealPath() ?: $upload->getFile()->getPathname();

            $url = $attachment->url;

            try {
                $ret = $oss->uploadFile($config['bucket'], ltrim($attachment->url, "/"), $filePath);
                //成功不做任何操作
            } catch (\Exception $e) {
                $checkDeleteFile($attachment, $upload, true);
                $this->error("上传失败");
            }
            $checkDeleteFile($attachment, $upload);

            $this->success("上传成功", '', ['url' => $url, 'fullurl' => cdnurl($url, true)]);
        }
        return;
    }

    /**
     * 回调
     */
    public function notify()
    {
        $this->check();
        $size = $this->request->post('size/d');
        $name = $this->request->post('name', '');
        $md5 = $this->request->post('md5', '');
        $type = $this->request->post('type', '');
        $url = $this->request->post('url', '');
        $width = $this->request->post('width/d');
        $height = $this->request->post('height/d');
        $category = $this->request->post('category', '');
        $category = array_key_exists($category, config('site.attachmentcategory') ?? []) ? $category : '';
        $suffix = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $suffix = $suffix && preg_match("/^[a-zA-Z0-9]+$/", $suffix) ? $suffix : 'file';
        $attachment = Attachment::where('url', $url)->where('storage', 'alioss')->find();
        if (!$attachment) {
            $params = array(
                'category'    => $category,
                'admin_id'    => (int)session('admin.id'),
                'user_id'     => (int)cookie('uid'),
                'filesize'    => $size,
                'filename'    => $name,
                'imagewidth'  => $width,
                'imageheight' => $height,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $type,
                'url'         => $url,
                'uploadtime'  => time(),
                'storage'     => 'alioss',
                'sha1'        => $md5,
            );
            Attachment::create($params, true);
        }
        $this->success();
        return;
    }

    /**
     * 检查签名是否正确或过期
     */
    protected function check()
    {
        $aliosstoken = $this->request->post('aliosstoken', '', 'trim');
        if (!$aliosstoken) {
            $this->error("参数不正确");
        }
        $config = get_addon_config('alioss');
        list($accessKeyId, $sign, $data) = explode(':', $aliosstoken);
        if (!$accessKeyId || !$sign || !$data) {
            $this->error("参数不能为空");
        }
        if ($accessKeyId !== $config['accessKeyId']) {
            $this->error("参数不正确");
        }
        if ($sign !== base64_encode(hash_hmac('sha1', base64_decode($data), $config['accessKeySecret'], true))) {
            $this->error("签名不正确");
        }
        $json = json_decode(base64_decode($data), true);
        if ($json['deadline'] < time()) {
            $this->error("请求已经超时");
        }
    }

}
