<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/10
 * Time: 20:48
 */


namespace Zorro;

use Attribute;
use Zorro\Attribute\CustomAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
#[CustomAttribute(InjectorHandler::class)]
class Inject
{
    /** @var string */
    protected $bean;

    public function __construct(string $bean)
    {
        if (!BeanFactory::hasBean($bean)) {
            throw new \Exception(sprintf(" must inject an object that can be instantiated"));
        }
        $this->bean = $bean;
    }

}
