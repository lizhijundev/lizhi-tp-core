<?php

namespace Lizhijun\LizhiTpCore\Service;

use think\Service as BaseService;

class ProjectService extends BaseService
{
    public function boot()
    {
        $this->commands([
            'gen' => command\GenAdmin::class,
//            'gen-app' => command\GenApp::class,
        ]);
    }
}