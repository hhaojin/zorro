<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/2
 * Time: 17:17
 */


namespace Example\Handler;

use example\CustomAttribute\TestLog;

class OrderProduct
{
    #[TestLog]
    public function prodList($num)
    {
        echo "prodList ----->" . $num . PHP_EOL;

        return [
            "php从入门到放弃",
            "k8s从放弃到佛系"
        ];
    }

    #[TestLog]
    public function pppppppp2($num)
    {
        echo "prodList ----->" . $num . PHP_EOL;

        return [
            "php从入门到放弃",
            "k8s从放弃到佛系"
        ];
    }
}