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