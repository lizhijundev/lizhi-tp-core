<?php

namespace Lizhijun\LizhiTpCore\Exception;

class ApiCode
{
    public $code = 0;
    public $message = '';

    public function __construct($_code = 0, $_message = '')
    {
        $this->code = $_code;
        $this->message = $_message;
    }

    /**
     * @param string $apicodeKey
     * @return ApiCode
     * @throws BaseException
     */
    public static function getCode($apicodeKey = 'system_error') : ApiCode {
        $config = config('apicode', []);
        if (!key_exists($apicodeKey, $config)) {
            return new ApiCode(500, '[开发者注意]apicode key ['.$apicodeKey.']不存在，请在config/apicode.php文件中定义');
        }
        $codeData = $config[$apicodeKey];
        if (!is_array($codeData) || sizeof($codeData) != 2) {
            return new ApiCode(500, '[开发者注意]apicode key ['.$apicodeKey.']，格式不正确');
        }
        return new ApiCode((int)$codeData[0], (string)$codeData[1]);
    }
}