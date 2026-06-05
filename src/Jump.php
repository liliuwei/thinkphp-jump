<?php
/**
 * Created by PhpStorm.
 * User: liliuwei
 * Date: 2026/06/05
 * Time: 10:43
 */

namespace liliuwei\think;

use think\exception\HttpResponseException;
use think\Response;

trait Jump
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request = null;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app = null;

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param  ?string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function success(mixed $msg = '', ?string $url = null, mixed $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : (string)($this->app ?? app())->route->buildUrl($url);
        }

        $result = [
            'code' => 1,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ('html' == strtolower($type)) {
            $type = 'view';
            $response = Response::create(($this->app ?? app())->config->get('jump.dispatch_success_tmpl'), $type)->assign($result)->header($header);
        } else {
            $response = Response::create($result, $type)->header($header);
        }

        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param  ?string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function error(mixed $msg = '', ?string $url = null, mixed $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = ($this->request ?? request())->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : (string)($this->app ?? app())->route->buildUrl($url);
        }

        $result = [
            'code' => 0,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();

        if ('html' == strtolower($type)) {
            $type = 'view';
            $response = Response::create(($this->app ?? app())->config->get('jump.dispatch_error_tmpl'), $type)->assign($result)->header($header);
        } else {
            $response = Response::create($result, $type)->header($header);
        }

        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param int $code 返回的code
     * @param mixed $msg 提示信息
     * @param string $type 返回数据格式
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function result(mixed $data, int $code = 0, mixed $msg = '', string $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * URL重定向
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param int $code http code
     * @param array $with 隐式传参
     * @return void
     */
    protected function redirect(string $url, int $code = 302, array $with = [])
    {
        $response = Response::create($url, 'redirect');

        $response->code($code)->with($with);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(): string
    {
        $request = $this->request ?? request();
        return $request->isJson() || $request->isAjax() ? 'json' : 'html';
    }
}
