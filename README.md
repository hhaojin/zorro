[![OSCS Status](https://www.oscs1024.com/platform/badge/hhaojin/zorro.svg?size=small)](https://www.oscs1024.com/project/hhaojin/zorro?ref=badge_small)
# Zorro
zorro是一款轻量灵活的高性能http框架，支持路由分组，中间件，自定义注解，自定义验证器，参数绑定.

## install
```text
composer require hhaojin/zorro
```

## quick start
### 1、路由
```php
<?php
require "./vendor/autoload.php";

$zorror = new \Zorro\Zorro();

//注册路由， curl -x POST http://localhost:8080/test/xxx -d '{"order_id": "world"}'
$zorror->Post("/test/{name}", function (\Zorro\Context $context) {
    $context->json(200, ["hello" => "world"]); //使用json响应
});

$zorror->Run(8080); //启动服务， 监听8080端口 

//使用workerman启动 php main.php
//使用swoole启动 php main.php ZORRO_SERVER=swoole
```

### 2、路由分组
```php
<?php
require "./vendor/autoload.php";

$zorror = new \Zorro\Zorro();

$zorror->Use(new \Example\RecoveryMiddleware()); //全局中间件

$orderGroup := $zorror->Group("/order"); //分组路由 
{
    //curl http://localhost:8080/order/detail?order_id=100
    $orderGroup->Get("/detail", [\Example\Handler\Order::class, "detail"]); 
}

```

### 3、参数绑定
```php
<?php
require "./vendor/autoload.php";

$zorror = new \Zorro\Zorro();

//注册路由， curl -x POST http://localhost:8080/test/xxx -d '{"order_id": 123}'
$zorror->Post("/test/{name}", function (\Zorro\Context $context) {
    //对body里面的参数进行校验，并映射到实体里
    /**@var \Example\Handler\OrderDeatilReq $requestParam */
    $requestParam := $context->bindJson(\Example\Handler\OrderDeatilReq::class) 

    $context->json(200, ["order_id" => $requestParam->order_id)]); //使用json响应
});

```


## 一、自定义注解
1、定义注解处理类
```php
namespace Example\CustomAttribute;

use Zorro\Aspect\JoinPoint;
use Zorro\Attribute\CustomAttributeInterface;

class LogHandler implements CustomAttributeInterface
{
    public function handle($reflect, $instance, $attribute): \Closure
    {
        return function (JoinPoint $joinPoint, array $args) use ($reflect) {
            echo "before -> " . $reflect->getName() . PHP_EOL;
            $joinPoint->process($args);
            echo "after -> " . $reflect->getName() . PHP_EOL;
        };
    }
}
```
2、定义注解类
```php
#[Attribute]
#[CustomAttribute(LogHandler::class)]
class TestLog
{
    public $name;
    public function __construct($name)
    {
        $this->name = $name
    }
}
```
3、使用注解
```php
class OrderProduct
{
    #[TestLog("prodList")]
    public function prodList($num)
    {
        echo "prodList ----->" . $num . PHP_EOL;
        return [
            "php从入门到放弃",
            "k8s从放弃到佛系"
        ];
    }
}
//在框架启动的时候扫描注解，会使用切面来处理，在handle中使用，会输出以下内容
\Zorro\BeanFactory::getBean(OrderProduct::class)->prodList(123);
/**
before -> prodList
prodList -----> 123
after -> prodList
 * /
```

## 二、验证器
1、使用

```php

class OrderDeatilReq
{
    #[\Zorro\Validation\Validate("intType;between=50,100;")]
    public $order_id;
}
//在控制器中直接使用bind方法, order_id必须是数字,并且50<= order_id <=100
/**@var \Zorro\Context $context*/
$req = $context->bindJson(OrderDeatilReq::class);
var_dump($req);
```
2、自定义规则
```php
class EqTag extends Zorro\Validation\CustomTagAbstract
{
    protected $tag = "eq";
    
    public function validate($input, $value): bool
    {
        var_dump($input,$value);
        if ($input === $value) {
            return true;
        }
        return false;
    }
}
\Zorro\Validation\Validator::registerValidation(new \Example\CustomAttribute\EqTag());

//使用, 匿名类的order_id 属性必须在50-100区间，并且必须等于88
$obj = new class {
    #[Validate("between=50,100;eq=88")]
    public $order_id = 100;
};

\Zorro\Validation\Validator::validate($obj);
// Zorro\Validation\ValidateException: invalid argument, property=order_id, 
// tag=eq in D:\code\program\zorro\src\Validation\Validator.php:78
```