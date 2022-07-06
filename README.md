# Zorro
zorro是一款轻量灵活的高性能http框架，支持路由分组，中间件，自定义注解，自定义验证器，参数绑定.

## install
```text
composer require hhaojin/zorro
```

## quick start
```php
require "./vendor/autoload.php";

$zorror = new \Zorro\Zorro();
//扫描指定命名空间下的注解，依赖注入，切面处理
$zorror->scanDir([__DIR__], ["Example"]); 

$zorror->Use(new \Example\RecoveryMiddleware()); //全局中间件捕获异常
$orderGroup := $zorror->Group("/order"); //分组路由 
{
    //curl http://localhost:8080/order/detail?order_id=1
    $orderGroup->Get("/detail", [\Example\Handler\Order::class, "detail"]); 
}

//注册路由， curl -x POST http://localhost:8080/test/xxx -d '{"order_id": 123}'
$zorror->Post("/test/{name}", function (\Zorro\Context $context) {
    //对body里面的参数进行校验，并映射到实体里
    /**@var \Example\Handler\OrderDeatilReq $requestParam */
    $requestParam := $context->bindJson(\Example\Handler\OrderDeatilReq::class) 
    if ($context->getParam("name") == "exception") {
        throw new Exception("xxx"); //如果参数是exception, 则抛出异常，由全局中间件捕获
    }
    var_dump($context->getParam("name"), $requestParam->order_id);
    $context->json(200, ["hello" => $context->getParam("name")]); //使用json响应
});

$zorror->Run(8080); //启动服务， 监听8080端口
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

## 二、自定义验证器
1、自定义注解名称，以及验证规则
```php
use Zorro\Validation\ValidatorAbstract

#[Attribute(Attribute::TARGET_PROPERTY)]
class Between extends ValidatorAbstract
{
    protected $min;
    protected $max;
    //判断数值是否在某个区间，message是自定义提示
    public function __construct(int $min, int $max, string $message = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->message = $message;
    }

    function check(string $name, $value): bool
    {
        return Validator::between($this->min, $this->max)->validate($value);
    }
}
```
2、使用
```php

class OrderDeatilReq
{
    #[Between(1, 99, "orderid 必须大于0小于99")]
    public $order_id;
}
/**@var \Zorro\Context $context*/
$req = $context->bindJson(OrderDeatilReq::class);
var_dump($req);
```
