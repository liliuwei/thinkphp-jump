# thinkphp-jump

适用于thinkphp6.0的跳转扩展

## 安装

~~~php
composer require liliuwei/thinkphp-jump
~~~

## 用法示例

使用 use \liliuwei\think\Jump; 

在所需控制器内引用该扩展即可：
~~~php
<?php
namespace app\controller;

class Index 
{
    use \liliuwei\think\Jump; 
    public function index()
    {
//      return $this->error('error');
//      return $this->success('success');
//      return $this->redirect('index/test');
      return $this->result(['username' => 'liliuwei', 'sex' => '男']);  
    }
}
~~~
下面示例我在框架自带的BaseController里引入，以后所有需要使用跳转的类继承自带的基类即可

以下是自带的基类
~~~php
<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
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

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
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
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
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
    use \liliuwei\think\Jump;
}

~~~

这里继承BaseController后即可使用success、error、redirect、result方法

~~~php
<?php

namespace app\admin\controller;
class Index extends \app\BaseController
{
    public function demo1()
        {
            return $this->success('success');
        }
        
        public function demo2()
        {
            return $this->error('error');
        }
            
        public function demo3()
        {
            return $this->redirect('index/index');
        }
                
        public function demo4()
        {
            return $this->result(['username' => 'liliuwei', 'sex' => '男']);  
        }
}
~~~

~~~