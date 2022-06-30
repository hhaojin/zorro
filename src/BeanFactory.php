<?php

namespace Zorro;


use Core\Annotations\Bean;
use Core\Annotations\Db;
use Core\Annotations\Lock;
use Core\Annotations\RedisMapping;
use Core\Annotations\RequestMapping;
use Core\Annotations\Value;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BeanFactory
{
    private static $env;
    /**
     * @var Container
     */
    private static $container;
    private static $handllers = [];
    private static $annotations = [
        'getPropertyAnnotations',
        'getMethodAnnotations',
    ];

    private static $scans = [
        ROOT_PATH . "/Core/Init" => "Core\\Init",
    ];

    const METHOD_ANNOTATIONS = "getMethodAnnotations";
    const PROPERTY_ANNOTATIONS = "getPropertyAnnotations";

    /**
     * @throws \Exception
     */
    public static function _init()
    {
        //初始化自动加载
        $loader = require __DIR__ . "/../vendor/autoload.php";
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        //读取配置文件
        self::$env = parse_ini_file(ROOT_PATH . "/env");

        //初始化bean容器
        $build = new ContainerBuilder();
        $build->useAnnotations(true);
        self::$container = $build->build();

        //加载处理注解的handler类
        $handlerFiles = glob(ROOT_PATH . "/Core/AnnotationHandlers/*.php");
        foreach ($handlerFiles as $handlerFile) {
            $handler = require $handlerFile;
            self::$handllers = array_merge(self::$handllers, $handler);
        }

        //初始化装载空间
        $scanDir = ROOT_PATH . '/App';
        $scanNamespace = 'App\\';
        self::$scans[$scanDir] = $scanNamespace;

        //开始装载
        foreach (self::$scans as $scanDir => $namespace) {
            try {
                self::scanBeans($scanDir, $namespace);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    private static function getAnnotationFile($dir)
    {
        $files = glob($dir . "/*");
        $arr = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                $arr = array_merge(self::getAnnotationFile($file), $arr);
            } elseif (pathinfo($file)['extension'] == 'php') {
                $arr[] = $file;
            }
        }

        return $arr;
    }

    /**
     * 自动装载
     * @throws \Exception
     */
    public static function scanBeans($path, $namespace)
    {
        $files = self::getAnnotationFile($path);
        foreach ($files as $file) {
            require_once $file;
        }

        $reader = new AnnotationReader();
        $classes = get_declared_classes();//获取所有已经声明的类
        foreach ($classes as $class) {
            if (strstr($class, $namespace)) {
                //装载类
                try {
                    $rc = new \ReflectionClass($class);
                } catch (\ReflectionException $e) {
                    throw new \Exception($e->getMessage());
                }
                $annotations = $reader->getClassAnnotations($rc);//获取所有类注解
                foreach ($annotations as $annotation) {
                    $instance = self::$container->get($rc->getName());
                    self::getHandler($instance, $rc, $reader);
                    self::classHandler($instance, $annotation);
                }
            }
        }
    }

    /** 处理注解 */
    private static function getHandler(&$instance, \ReflectionClass $class, AnnotationReader $reader): void
    {
        foreach (self::$annotations as $type) {
            switch ($type) {
                case static::METHOD_ANNOTATIONS:
                    $objs = $class->getMethods();
                    break;
                case static::PROPERTY_ANNOTATIONS:
                    $objs = $class->getProperties();
            }
            foreach ($objs as $obj) {
                $annotations = $reader->$type($obj);
                foreach ($annotations as $annotation) {
                    if (!in_array(get_class($annotation), [Value::class, Db::class, RequestMapping::class,
                        RedisMapping::class, Lock::class
                    ])) {
                        continue;
                    }
                    $handller = self::$handllers[get_class($annotation)];
                    $instance = $handller($obj, $instance, $annotation);
                }
            }
        }
    }

    /**
     * 处理类注解
     */
    private static function classHandler($instance, $annotation): void
    {
        if (!in_array(get_class($annotation), [Bean::class])) {
            return;
        }
        $handller = self::$handllers[get_class($annotation)]; //获取bean类handller
        $handller($instance, self::$container, $annotation);
    }

    public static function getBean($beanName)
    {
        try {
            return self::$container->get($beanName);
        } catch (\Exception $e) {
            var_dump("not fount $beanName");
            return false;
        }
    }

    public static function setBean($beanName, $bean)
    {
        self::$container->set($beanName, $bean);
    }

    public static function getEnv($key)
    {
        return self::$env[$key];
    }
}