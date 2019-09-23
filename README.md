# thinkphp-jump

适用于thinkphp6.0的跳转扩展

## 安装

~~~php
composer require liliuwei/thinkphp-jump
~~~

## 用法示例

使用 use \liliuwei\think\Jump; 

在所需控制器内引用该扩展即可：

这里我写了个自定义Base基类继承框架自带的BaseController，Index类继承了Base类

~~~php
<?php

namespace app\admin\controller;
class Base extends \app\BaseController
{
    use \liliuwei\think\Jump; 
    public function initialize()
        {
            //业务代码
            //if (!session('admin_id')) {
                //$this->redirect('login/index');
            //} 
        }
}
~~~
~~~php
<?php
namespace app\admin\controller;
class Index extends Base
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