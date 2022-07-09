<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\console\Input;
use think\Db;
use think\Exception;
use think\Loader;

/**
 * 在线命令管理
 *
 * @icon fa fa-circle-o
 */
class Elfinder extends Backend
{
    protected $noNeedRight = ['view'];

    public function _initialize()
    {
        parent::_initialize();
        if (!config("app_debug")) {
            $this->error("只允许在开发环境下使用");
        }
        include_once ADDON_PATH . 'elfinder' . DS . 'library' . DS . 'elfinder' . DS . 'autoload.php';
    }


    public function index()
    {
        return $this->view->fetch();
    }

    public function view()
    {
        $config = get_addon_config('elfinder');
        $allowType = explode(',', $config['allow_upload']);
        $allNeedTypes = [];
        foreach ($allowType as $key => $type) {
            if (isset(self::$mimetypes[$type])) {
                $allNeedTypes[] = self::$mimetypes[$type];
            } else {
                $allNeedTypes[] = $type;
            }
        }
        $allWrite = explode(',', $config['allow_write']);
        $accessControl = 'roaccess';
        if (in_array($this->auth->id, $allWrite)) {
            $accessControl = 'rwaccess';
        }
        $allNeedTypes = array_values(array_unique($allNeedTypes));
        $opts = array(
            // 'debug' => true,
            'roots' => array(
                // Items volume
                array(
                    'driver'        => $config['driver'],           // driver for accessing file system (REQUIRED)
                    'path'          => $config['path'],                 // path to files (REQUIRED)
                    'URL'           => $config['url'], // URL to files (REQUIRED)
                    // 'trashHash'     => 't1_Lw',                     // elFinder's hash of trash folder
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny'    => ['all'],                // All Mimetypes not allowed to upload
                    'uploadAllow'   => $allNeedTypes,// Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => $accessControl         // disable and hide dot starting files (OPTIONAL)
                )
            )
        );

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
    }

    /**
     * default extensions/mimetypes for mimeDetect == 'internal'
     * 这个是Elfinder格式校验对应关系
     * @var array
     **/
    protected static $mimetypes = array(
        // applications
        'ai'       => 'application/postscript',
        'eps'      => 'application/postscript',
        'exe'      => 'application/x-executable',
        'doc'      => 'application/msword',
        'dot'      => 'application/msword',
        'xls'      => 'application/vnd.ms-excel',
        'xlt'      => 'application/vnd.ms-excel',
        'xla'      => 'application/vnd.ms-excel',
        'ppt'      => 'application/vnd.ms-powerpoint',
        'pps'      => 'application/vnd.ms-powerpoint',
        'pdf'      => 'application/pdf',
        'xml'      => 'application/xml',
        'swf'      => 'application/x-shockwave-flash',
        'torrent'  => 'application/x-bittorrent',
        'jar'      => 'application/x-jar',
        // open office (finfo detect as application/zip)
        'odt'      => 'application/vnd.oasis.opendocument.text',
        'ott'      => 'application/vnd.oasis.opendocument.text-template',
        'oth'      => 'application/vnd.oasis.opendocument.text-web',
        'odm'      => 'application/vnd.oasis.opendocument.text-master',
        'odg'      => 'application/vnd.oasis.opendocument.graphics',
        'otg'      => 'application/vnd.oasis.opendocument.graphics-template',
        'odp'      => 'application/vnd.oasis.opendocument.presentation',
        'otp'      => 'application/vnd.oasis.opendocument.presentation-template',
        'ods'      => 'application/vnd.oasis.opendocument.spreadsheet',
        'ots'      => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'odc'      => 'application/vnd.oasis.opendocument.chart',
        'odf'      => 'application/vnd.oasis.opendocument.formula',
        'odb'      => 'application/vnd.oasis.opendocument.database',
        'odi'      => 'application/vnd.oasis.opendocument.image',
        'oxt'      => 'application/vnd.openofficeorg.extension',
        // MS office 2007 (finfo detect as application/zip)
        'docx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'docm'     => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm'     => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm'     => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xltx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm'     => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlsb'     => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlam'     => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx'     => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm'     => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx'     => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm'     => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx'     => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm'     => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam'     => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx'     => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm'     => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        // archives
        'gz'       => 'application/x-gzip',
        'tgz'      => 'application/x-gzip',
        'bz'       => 'application/x-bzip2',
        'bz2'      => 'application/x-bzip2',
        'tbz'      => 'application/x-bzip2',
        'xz'       => 'application/x-xz',
        'zip'      => 'application/zip',
        'rar'      => 'application/x-rar',
        'tar'      => 'application/x-tar',
        '7z'       => 'application/x-7z-compressed',
        // texts
        'txt'      => 'text/plain',
        'php'      => 'text/x-php',
        'html'     => 'text/html',
        'htm'      => 'text/html',
        'js'       => 'application/javascript',
        'css'      => 'text/css',
        'rtf'      => 'text/rtf',
        'rtfd'     => 'text/rtfd',
        'py'       => 'text/x-python',
        'java'     => 'text/x-java-source',
        'rb'       => 'text/x-ruby',
        'sh'       => 'text/x-shellscript',
        'pl'       => 'text/x-perl',
        'xml'      => 'text/xml',
        'sql'      => 'text/x-sql',
        'c'        => 'text/x-csrc',
        'h'        => 'text/x-chdr',
        'cpp'      => 'text/x-c++src',
        'hh'       => 'text/x-c++hdr',
        'log'      => 'text/plain',
        'csv'      => 'text/csv',
        'md'       => 'text/x-markdown',
        'markdown' => 'text/x-markdown',
        // images
        'bmp'      => 'image/x-ms-bmp',
        'jpg'      => 'image/jpeg',
        'jpeg'     => 'image/jpeg',
        'gif'      => 'image/gif',
        'png'      => 'image/png',
        'tif'      => 'image/tiff',
        'tiff'     => 'image/tiff',
        'tga'      => 'image/x-targa',
        'psd'      => 'image/vnd.adobe.photoshop',
        //'ai'    => 'image/vnd.adobe.photoshop',
        'xbm'      => 'image/xbm',
        'pxm'      => 'image/pxm',
        //audio
        'mp3'      => 'audio/mpeg',
        'mid'      => 'audio/midi',
        'ogg'      => 'audio/ogg',
        'oga'      => 'audio/ogg',
        'm4a'      => 'audio/mp4',
        'wav'      => 'audio/wav',
        'wma'      => 'audio/x-ms-wma',
        // video
        'avi'      => 'video/x-msvideo',
        'dv'       => 'video/x-dv',
        'mp4'      => 'video/mp4',
        'mpeg'     => 'video/mpeg',
        'mpg'      => 'video/mpeg',
        'mov'      => 'video/quicktime',
        'wm'       => 'video/x-ms-wmv',
        'flv'      => 'video/x-flv',
        'mkv'      => 'video/x-matroska',
        'webm'     => 'video/webm',
        'ogv'      => 'video/ogg',
        'ogm'      => 'video/ogg',
        'm2ts'     => 'video/MP2T',
        'mts'      => 'video/MP2T',
        'ts'       => 'video/MP2T',
        'm3u8'     => 'application/x-mpegURL',
        'mpd'      => 'application/dash+xml'
    );

}
