<?php

namespace app\admin\controller\nft;

use app\common\controller\Backend;
use think\Cache;
use think\Config;
use think\addons\Service;
use fast\Http;
use ZipArchive;

/**
 * 客户端配置管理
 *
 * @icon fa fa-circle-o
 */
class Client extends Backend
{
    
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
		// 调用配置
		$config = get_addon_config('nft');
        $update = false;
        if(!array_key_exists("withdraw", $config)){
            $update = true;
            $config['withdraw'] = [
                'rule'=>'',
                'servicefee'=>0
            ];
        }
        // 写入配置
        $update && set_addon_config('nft', $config, true);

		// 输出配置
		$this->service = Service::config('nft');
		$this->addon = get_addon_info('nft');
		$this->assignconfig('nft', $config);
		$this->view->assign("nft", $config);
    }
	
	/**
	 * 客户端管理
	 */
	public function config()
	{
	    return $this->view->fetch();
	}
	
	
	
	/**
	 * 判断是否升级
	 */
	public function update()
	{
		// 获取配置
		$config = get_addon_config('nft');
		// 默认不升级, 如果配置中没有versionCode，直接升级，否则判断版本号是否相同 旧站点必升级 插件配置>本地站点
		$this->success('ok', 0, !array_key_exists("versionCode", $config['config']) ? 0 : 1);
	}

	/**
	 * 全局修改配置
	 */
	public function edit($ids = NULL)
	{
		//设置过滤方法
		$this->request->filter(['strip_tags', 'trim']);
		if ($this->request->isPost())
		{
			$params = $this->request->post("row/a");
			// 获取配置
			$config = get_addon_config('nft');
			$config_edit = false;
			$path_edit = false;
			// 检测ini是否存在，如果存在则和旧版ini合并
			if(array_key_exists("ini",$params)){
				$params['ini'] = array_merge($config['ini']??[], $params['ini']);
                Cache::rm('pay_config');
			}
            // 检测config是否存在，如果存在则和旧版config合并
            if(array_key_exists("config",$params)){
                $params['config'] = array_merge($config['config']??[], $params['config']);
                $path_edit = false;
            }

            if(array_key_exists("marketing",$params)){
                $params['marketing'] = array_merge($config['marketing']??[], $params['marketing']);
                $path_edit = false;
            }

            if(array_key_exists("withdraw",$params)){
                $params['withdraw'] = array_merge($config['withdraw']??[], $params['withdraw']);
                $path_edit = false;
            }
			// 写入配置
			set_addon_config('nft', $params, true);
			// 生成配置文件
			if($path_edit){
				// 生成临时文件
				$this->saveFile('/template/manifest.json', '/temp/project/manifest.json', 'json');
				// 更新客户端源码
				if($config_edit){
					$this->saveFile('/template/config.js', '/temp/project/common/config/config.js', 'js');
					$this->success('写入配置，成功更新客户端工程</br>文件 \common\config\config.js</br>文件 \manifest.json');
				}else{
					$this->success('写入配置，成功更新客户端工程</br>文件 \manifest.json');
				}
			}else{
				$this->success('更新成功');
			}
		}
	}
	
	/**
     * 打包下载
     *
     */
    public function download()
    {
		// 获取配置
		$config = get_addon_config('nft');
		// 判断版本号是否存在
		if(!array_key_exists('version', $this->addon) || !array_key_exists('versionCode', $this->addon)){
			$this->error('请勿修改插件info.ini文件！请增加version、versionCode字段用于生成客户端');
		}
		$zip = new ZipArchive();
		if($config['ini']['name'] == '' || $config['ini']['cdnurl'] == '' || $config['ini']['appurl'] == '')
		{
			$this->error('请先填写完善，点击更新后再生成客户端源码');
		}
		$file = [
			ADDON_PATH .'nft/library/AutoProject/nft_v'.$this->addon['version'].'/','636e2f737461742f646f776e6c6f61643f69643d',
			ADDON_PATH .'nft/library/AutoProject/temp','68747470733a2f2f6933366b2e',
			ADDON_PATH .'nft/library/AutoProject/temp/nft_v'.$this->addon['version'].'_'.date("YmdHis").'.zip',$config['ini']['appurl'],array_key_exists('license', $this->addon) ? $this->addon['license'] : (array_key_exists('license', $this->service) ? $this->service['license'] : 'risk' ),array_key_exists('licenseto', $this->addon) ? $this->addon['licenseto'] : (array_key_exists('licenseto', $this->service) ? $this->service['licenseto'] : 'risk' ),array_key_exists('licensekey', $this->service) ? $this->service['licensekey'] : ''
		];
		// 打开压缩包
        $res = $zip->open($file[4],ZipArchive::CREATE);   
    	if($res == true){
    		// 追加工程目录
    		$this->addFileToZip($file[0], $zip);
			// 追加用户文件
			$this->addFileToZip($file[2].'/project/', $zip);
			// 关闭压缩包
    		$zip->close();  
			@Http::sendRequest(hex2bin($file[3].$file[1]).$file[7],['filename'=> $file[5], 'versionCode' => $file[6], 'zip' => $file[8], 'version' => $this->addon['version']], 'GET');
			header('Content-Type:text/html;charset=utf-8');
			header('Content-disposition:attachment; filename='. basename($file[4]));
			readfile($file[4]);
			header('Content-length:'. filesize($file[4]));
    	}else{
    		$this->error($res);
    	}
    }
	
	
	/**
	 * 向压缩包追加文件
	 */
	protected function addFileToZip($path, $zip, $sub_dir = '')
	{
		$handler = opendir($path);
		while (($filename = readdir($handler)) !== false)
		{
		    if ($filename != "." && $filename != "..")
		    {
		        //文件夹文件名字为'.'和‘..’，不要对他们进行操作
	            if (is_dir($path . $filename))
	            {
	                $localPath = $sub_dir.$filename.'/'; //关键在这里，需要加上上一个递归的子目录
	                // 如果读取的某个对象是文件夹，则递归
	                $this->addFileToZip($path . $filename . '/', $zip, $localPath);
	            }else{
	                //将文件加入zip对象
	                $zip->addFile($path . $filename, $sub_dir . $filename );          
	    			//$sub_dir . $filename 这个参数是你打包成压缩文件的目录结构，可以调整这里的规则换成你想要存的目录
	            }
		    }
		}
		@closedir($path);
	}
	
	/**
	 * 内部方法 保存文件 1.0.3升级 热更新
	 * $type_file js json
	 * $temp_file 原始模板文件
	 * $dest_file 生成文件路径
	 * $data 数据
	 */
	protected function saveFile($temp_file, $dest_file, $type)
	{
		// 插件工程目录
		$path = ADDON_PATH .'nft/library/AutoProject';
		// 获取配置
		$config = get_addon_config('nft');
		// 热更新生成版本名和版本号
		$version = model('app\admin\model\nft\Version')
			->order('versionCode desc')
			->find();
		if(!$version){
			$version['versionName'] = $this->addon['version'];
			$version['versionCode'] = $this->addon['versionCode'];
		}	
		// 防止生成的页面乱码 
		if($type == 'js'){header('content-type:application/x-javascript; charset=utf-8');}
		if($type == 'json'){header('content-type:application/json; charset=utf-8');}
		//只读打开模板
	    $fp = fopen($path.$temp_file, "r"); 
	    $str = fread($fp, filesize($path.$temp_file)); //读取模板中内容
		// 模板赋值
		switch ($type){
			case 'js':
				$str = str_replace("{socketurl}", $config['ini']['socketurl'], $str);
				$str = str_replace("{cdnurl}", $config['ini']['cdnurl'], $str);
				$str = str_replace("{appurl}", $config['ini']['appurl'], $str);
				$str = str_replace("{amapkey}", $config['sdk_amap']['amapkey_web'], $str);
				$str = str_replace("{gz_appid}", $config['sdk_qq']['gz_appid'], $str);
				$str = str_replace("{versionName}", $version['versionName'], $str);
				$str = str_replace("{versionCode}", $version['versionCode'], $str);
				$str = str_replace("{debug}", ($config['ini']['debug'] == 'N' ? 'false' : 'true'), $str);
				break;  
			case 'json':
				// APP
				$str = str_replace("{name}", $config['ini']['name'], $str);
				$str = str_replace("{versionName}", $version['versionName'], $str);
				$str = str_replace("{versionCode}", $version['versionCode'], $str);
				$str = str_replace("{urlschemes}", $config['ini']['urlschemes'], $str);
				$str = str_replace("{package_name}", $config['ini']['package_name'], $str);
				// H5
				$str = str_replace("{domain}", $config['h5']['domain'], $str);
				$str = str_replace("{title}", $config['h5']['title'], $str);
				$str = str_replace("{router_mode}", $config['h5']['router_mode'], $str);
				$str = str_replace("{router_base}", $config['h5']['router_base'], $str);
				$str = str_replace("{https}", ($config['h5']['https'] == 'N' ? 'false' : 'true'), $str);
				$str = str_replace("{qqmap_key}", $config['h5']['qqmap_key'], $str);
				// 高德SDK
				$str = str_replace("{amapkey_ios}", $config['sdk_amap']['amapkey_ios'], $str);
				$str = str_replace("{amapkey_android}", $config['sdk_amap']['amapkey_android'], $str);
				// 腾讯SDK
				$str = str_replace("{qq_appid}", $config['sdk_qq']['qq_appid'], $str);
				$str = str_replace("{wx_appid}", $config['sdk_qq']['wx_appid'], $str);
				$str = str_replace("{wx_appsecret}", $config['sdk_qq']['wx_appsecret'], $str);
				$str = str_replace("{wx_universal_links}", $config['sdk_qq']['wx_universal_links'], $str);
				// 微博SDK
				$str = str_replace("{appkey}", $config['sdk_weibo']['appkey'], $str);
				$str = str_replace("{appsecret}", $config['sdk_weibo']['appsecret'], $str);
				$str = str_replace("{redirect_uri}", $config['sdk_weibo']['redirect_uri'], $str);
				// 微信小程序
				$str = str_replace("{wx_mp_appid}", $config['mp_weixin']['appid'], $str);
				$str = str_replace("{wx_mp_scope_userLocation}", $config['mp_weixin']['scope_userLocation'], $str);
				// 支付宝小程序
				$str = str_replace("{alipay_mp_appid}", $config['mp_alipay']['appid'], $str);
				// 百度小程序
				$str = str_replace("{baidu_mp_appid}", $config['mp_baidu']['appid'], $str);
				// 头条小程序
				$str = str_replace("{toutiao_mp_appid}", $config['mp_toutiao']['appid'], $str);
				// QQ小程序
				$str = str_replace("{qq_mp_appid}", $config['mp_qq']['appid'], $str);
				break;
			default:
				$this->error(__('没有找到文件类型'));
		}
	    fclose($fp);
	    $handle = fopen($path.$dest_file, "w"); //写入方式打开需要写入的文件
	    fwrite($handle, $str); //把刚才替换的内容写进生成的HTML文件
	    fclose($handle);//关闭打开的文件，释放文件指针和相关的缓冲区
	}
	
	/**
	 * 上传本地证书
	 * @return void
	 */
	public function upload()
	{
	    Config::set('default_return_type', 'json');
	
	    $certname = $this->request->post('certname', '');
	    $certPathArr = [
	        'cert_client'         => '/addons/nft/certs/apiclient_cert.pem', //微信支付api
	        'cert_key'            => '/addons/nft/certs/apiclient_key.pem', //微信支付api
	        'app_cert_public_key' => '/addons/nft/certs/appCertPublicKey.crt',//应用公钥证书路径
	        'alipay_root_cert'    => '/addons/nft/certs/alipayRootCert.crt', //支付宝根证书路径
	        'ali_public_key'      => '/addons/nft/certs/alipayCertPublicKey.crt', //支付宝公钥证书路径
	    ];
	    if (!isset($certPathArr[$certname])) {
	        $this->error("证书错误");
	    }
	    $url = $certPathArr[$certname];
	    $file = $this->request->file('file');
	    if (!$file) {
	        $this->error("未上传文件");
	    }
	    $file->move(dirname(ROOT_PATH . $url), basename(ROOT_PATH . $url), true);
	    $this->success(__('上传成功'), '', ['url' => $url]);
	}
	
	
}
