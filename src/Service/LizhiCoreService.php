<?php

namespace Lizhijun\LizhiTpCore\Service;

use think\Service as BaseService;
use Lizhijun\LizhiTpCore\ExceptionHandle;

class LizhiCoreService extends BaseService
{
    public function register()
    {
        $this->app->bind('think\exception\Handle', ExceptionHandle::class);
    }

}