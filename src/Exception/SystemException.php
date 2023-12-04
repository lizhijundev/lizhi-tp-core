<?php
declare (strict_types=1);

namespace Lizhijun\LizhiTpCore\Exception;


use think\Exception;

/**
 * 自定义异常类的基类
 * Class BaseException
 * @package cores\exception
 */
class SystemException extends BaseException
{

    public function __construct($message = '', $apiCodeKey = 'system_error', $data = [])
    {
        $apicode = ApiCode::getCode($apiCodeKey);
        parent::__construct([
            'code' => $apicode->code,
            'message' => $message ?? $apicode->message,
            'data' => $data
        ]);
    }
}

