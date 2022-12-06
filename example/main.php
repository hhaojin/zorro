<?php

require "./../vendor/autoload.php";

$zorror = new \Zorro\Zorro();

$zorror->Use(new \Example\RecoveryMiddleware());

$zorror->Get("/order/detail", [\Example\Handler\Order::class, "detail"]);

$zorror->Get("/test/{name}", function (\Zorro\Context $context) {
    if ($context->getParam("name") == "exception") {
        throw new Exception("xxx");
    }
    \Zorro\BeanFactory::getBean(\Example\Handler\OrderProduct::class)->prodList(123);
    $context->json(200, ["hello" => $context->getParam("name")]);
});

$zorror->Run();
