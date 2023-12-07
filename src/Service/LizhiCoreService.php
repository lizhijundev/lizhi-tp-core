<?php

namespace Lizhijun\LizhiTpCore\Service;

use Lizhijun\LizhiTpCore\ExceptionHandle;
use Lizhijun\LizhiTpCore\Request;
use think\Service as BaseService;

class LizhiCoreService extends BaseService
{
    public function register()
    {
        $this->app->bind('think\Request', Request::class);
        $this->app->bind('think\exception\Handle', ExceptionHandle::class);
    }

}