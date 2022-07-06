<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/6
 * Time: 23:07
 */


namespace Example;

use Exception;

class RecoveryMiddleware implements \Zorro\HandleInterface{
    public function handle(\Zorro\Context $context){
        try {
            $context->next();
        } catch (Exception $e) {
            $context->abortJson(400, ["error" => $e->getMessage()]);
        }
    }
}
