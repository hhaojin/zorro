<?php

require "./../vendor/autoload.php";

$zorror = new \Zorro\Zorro();
$zorror->scanDir([__DIR__], ["Example"]);

$zorror->Use(new \Example\RecoveryMiddleware());

$zorror->Get("/order/detail", [\Example\Handler\Order::class, "detail"]);

$zorror->Get("/test/{name}", function (\Zorro\Context $context) {
    if ($context->getParam("name") == "exception") {
        throw new Exception("xxx");
    }
    $context->json(200, ["hello" => $context->getParam("name")]);
});

$zorror->Run();
