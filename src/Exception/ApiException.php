<?php

namespace Lizhijun\LizhiTpCore\Exception;

class ApiException extends BaseException
{
    public function __construct($message = '', $apiCodeKey = 'business_error', $data = [])
    {
        $apicode = ApiCode::getCode($apiCodeKey);
        parent::__construct([
            'code' => $apicode->code,
            'message' => $message ?? $apicode->message,
            'data' => $data
        ]);
    }


}