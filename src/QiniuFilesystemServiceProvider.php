<?php namespace zgldh\QiniuStorage;

use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use zgldh\QiniuStorage\Plugins\DownloadUrl;
use zgldh\QiniuStorage\Plugins\ImageExif;
use zgldh\QiniuStorage\Plugins\ImageInfo;
use zgldh\QiniuStorage\Plugins\ImagePreviewUrl;
use zgldh\QiniuStorage\Plugins\PersistentFop;
use zgldh\QiniuStorage\Plugins\PersistentStatus;
use zgldh\QiniuStorage\Plugins\PrivateDownloadUrl;
use zgldh\QiniuStorage\Plugins\UploadToken;
use zgldh\QiniuStorage\Plugins\PutFile;
use zgldh\QiniuStorage\Plugins\Put;

class QiniuFilesystemServiceProvider extends ServiceProvider {

    public function boot()
    {
        class_alias('Illuminate\Support\Facades\Storage', 'Storage'); //for Lumen5.2
        \Storage::extend(
            'qiniu',
            function ($app, $config)
            {
                $qiniu_adapter = new QiniuAdapter(
                    $config['access_key'],
                    $config['secret_key'],
                    $config['bucket'],
                    $config['domain'],
                    $config['pipeline'],
                    $config['notify_url']
                );
                $file_system   = new Filesystem($qiniu_adapter);
                $file_system->addPlugin(new PrivateDownloadUrl());
                $file_system->addPlugin(new DownloadUrl());
                $file_system->addPlugin(new ImageInfo());
                $file_system->addPlugin(new ImageExif());
                $file_system->addPlugin(new ImagePreviewUrl());
                $file_system->addPlugin(new PersistentFop());
                $file_system->addPlugin(new PersistentStatus());
                $file_system->addPlugin(new UploadToken());
                $file_system->addPlugin(new PutFile());
                $file_system->addPlugin(new Put());

                return $file_system;
            }
        );
    }

    public function register()
    {
        //
    }
}
