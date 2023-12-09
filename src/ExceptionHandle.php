<?php

namespace lztp_core;

use lztp_core\exception\ApiCode;
use lztp_core\exception\ApiException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\exception\PDOException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Request;
use think\Response;
use think\response\Json;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    // 状态码
    private $code;

    // 错误信息
    private $message;

    // 附加数据
    public $data = [];


    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];


    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof PDOException) {
            // 数据库异常
            $apicode = ApiCode::getCode('db_error');
            $this->code = $apicode->code;
            $this->message = $apicode->message;
            return $this->outputDebug($e, [
                'sql' => $e->getData()['Database Status']['Error SQL']
            ]);
        } else if ($e instanceof ApiException) {
            $this->code = $e->code;
            $this->message = $e->message;
            $this->data = $e->data;
        } else {
            // 系统运行的异常
            $apicode = ApiCode::getCode('system_error');
            $this->code = $apicode->code;
            $this->message = $e->getMessage() ?: $apicode->message;
            $this->data = [];
            return $this->outputDebug($e);
        }

        // 如果是debug模式, 输出调试信息
        if (Helper::is_debug()) {
            return $this->outputDebug($e);
        }
        return $this->output([]);
    }

    /**
     * 返回json格式数据
     * @param array $extend 扩展的数据
     * @return Json
     */
    private function output(array $extend = []): Json
    {
        $jsonData = [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ];

        if (Helper::is_debug()) {
            return json(array_merge($jsonData, $extend));
        }
        return json($jsonData);
    }

    /**
     * 返回json格式数据 (debug模式)
     * @param Throwable $e
     * @return Json
     */
    private function outputDebug(Throwable $e, array $extend = []): Json
    {
        $debug = array_merge($extend, [
            'name' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $this->getCode($e),
            'message' => $this->getMessage($e),
            'trace' => $e->getTrace(),
            'source' => $this->getSourceCode($e),
            'ip' => Request::ip(),
            'url' => Request::url(true),
            'method' => Request::method(),
            'tables'  => [
                'Header' => Request::header(),
                'GET Data' => $this->app->request->get(),
                'POST Data' => $this->app->request->post(),
                'Cookies' => $this->app->request->cookie(),
                'Session' => $this->app->exists('session') ? $this->app->session->all() : [],
                'Server/Request Data' => $this->app->request->server(),
            ],
        ]);
        $this->errorLog($debug);
        return $this->output(['debug' => $debug]);
    }

    /**
     * 将异常写入日志
     * @param Throwable $e
     */
    private function errorLog(array $traceData = [])
    {
        $log = "[{$traceData['code']}] {$traceData['message']}" . PHP_EOL . json_encode($traceData);
        // 写入日志文件
        // Log::record($log, 'error');
        $this->app->log->record($log, 'error');
    }


    /**
     * 记录异常信息（包括日志或者其它方式记录）
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 停用tp异常记录
    }


}
