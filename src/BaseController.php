<?php
declare (strict_types=1);

namespace lztp_core;

use lztp_core\exception\ApiException;
use lztp_core\exception\SystemException;
use think\App;
use think\facade\Cache;
use think\Validate;
use think\exception\ValidateException;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    protected string $tokenKey = 'Authorization';
    protected $member = null;
    protected $memberPK = null;
    protected $allowAllAction = [];



    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];


    // 当前控制器名称
    protected $controller = '';

    // 当前方法名称
    protected $action = '';

    // 当前路由uri
    protected $routeUri = '';

    // 当前路由：分组名称
    protected $group = '';

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app, $_memberPK = null)
    {
        print_r($_memberPK);
        if (empty($_memberPK)) {
            throw new SystemException('【开发者注意】请在'.get_parent_class($this).'重载__construct方法');
        }
        $this->app = $app;
        $this->request = $this->app->request;
        $this->memberPK = $_memberPK;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 初始化请求路由
        $this->getRouteInfo();
        // 设置header授权信息
        $this->setMemberInfo();
        // 验证是否登录
        $this->checkLogin();
    }


    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteInfo()
    {
        // 控制器名称
        $this->controller = Helper::uncamelize($this->request->controller());
        // 方法名称
        $this->action = $this->request->action();
        // 控制器分组 (用于定义所属模块)
        $group = strstr($this->controller, '.', true);
        $this->group = $group !== false ? $group : $this->controller;
        // 当前uri
        $this->routeUri = "{$this->controller}/$this->action";
    }


    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 获取当前token
     * @return string
     */
    public function getToken()
    {
        // 获取请求中的token
        $token = request()->header($this->tokenKey);
        // 调试模式下可通过param
        if (empty($token) && Helper::is_debug()) {
            $token = request()->param($this->tokenKey);
        }
        // 不存在token报错
        if (empty($token)) {
            return false;
        }
        return $token;
    }


    protected function api(string|array|object $data = []): \think\response\Json
    {
        return json([
            'code' => 0,
            'message' => '',
            'data' => $data
        ]);
    }


    protected function apiList(array $arrList = [], int $page = 1, int $total = 0): \think\response\Json
    {
        return $this->api([
            'list' => $arrList,
            'page' => $page,
            'total' => $total
        ]);
    }


    private function checkLogin()
    {
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return;
        }
        if (empty($this->member) || !isset($this->member[$this->memberPK])) {
            throw new ApiException('请登录', 'not_logged');
        }
    }


    private function setMemberInfo() {
        $token = self::getToken();
        if ($token) {
            $this->member = Cache::get($token, null);
        }
    }

    protected function getMember() {
        return $this->member;
    }

    protected function getMemberPK() {
        return $this->member[$this->memberPK];
    }


}
