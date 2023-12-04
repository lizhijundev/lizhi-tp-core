<?php

namespace Lizhijun\LizhiTpCore;

use think\facade\Env;

class Helper
{
    /**
     * 判斷是否是debug模式
     * @return bool
     */
    public static function is_debug(): bool
    {
        return (bool)Env::instance()->get('APP_DEBUG');
    }


    /**
     * 驼峰转下划线
     * @param string $camelCaps
     * @param string $separator
     * @return string
     */
    public static function uncamelize(string $camelCaps, string $separator = '_'): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

}