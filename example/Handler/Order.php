<?php

namespace Example\Handler;

use example\CustomAttribute\TestLog;
use Zorro\Context;
use Zorro\Validation\Between;

class OrderDeatilReq
{
    #[Between(1, 99, "orderid 必须大于0小于99")]
    public $order_id;
}

class Order
{
    /** @var OrderProduct */
    protected $orderProd;

    public function __construct(OrderProduct $orderProd)
    {
        $this->orderProd = $orderProd;
    }

    #[TestLog]
    public function detail(Context $context)
    {
        echo "detail \n";
        /** @var OrderDeatilReq $orderReq */
        $orderReq = $context->bindQuery(OrderDeatilReq::class);
        $res = $this->orderProd->prodList($orderReq->order_id);

        $context->json(200, $res);
    }
}