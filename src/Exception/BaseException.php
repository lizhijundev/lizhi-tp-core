<?php
declare (strict_types=1);

namespace Lizhijun\LizhiTpCore\Exception;


use think\Exception;

/**
 * 自定义异常类的基类
 * Class BaseException
 * @package cores\exception
 */
class BaseException extends Exception
{
    // 状态码
    public $code;

    // 错误信息
    public $message = '';

    // 输出的数据
    public $data = [];

    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含code、message、data，且不应该是空值
     */
    public function __construct($params = [])
    {
        parent::__construct();
        $apicode = ApiCode::getCode('system_error');
        $this->code = $params['code'] ?? $apicode->code;
        $this->message = $params['message'] ?? $apicode->message;
        $this->data = $params['data'] ?? [];
    }
}

