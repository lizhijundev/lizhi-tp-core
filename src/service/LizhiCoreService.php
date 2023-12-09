<?php

namespace lztp_core\service;

use lztp_core\ExceptionHandle;
use lztp_core\Request;
use think\Service as BaseService;

class LizhiCoreService extends BaseService
{
    public function register()
    {
        $this->app->bind('think\Request', Request::class);
        $this->app->bind('think\exception\Handle', ExceptionHandle::class);
    }

}