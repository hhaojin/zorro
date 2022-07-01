<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:11
 */


namespace Zorro\Serialize;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Zorro\Serialize\Mapper\Mapper;
use Zorro\Validation\Eq;

class Address
{
    public $city;
}

class Person
{
    #[Eq(19)]
    public $age;
    public $name;
    /** @var Address */
    public $address;
}

class SerializerTest extends TestCase
{

    public function testJsonUnmarshal_Obejct()
    {
        $jsonContent = '{"age":19,"name":"foo","address":{"city":"xx"}}';
        $parser = new Parser([], [new JsonEncoder()]);
        $mapper = new Mapper();
        $s = new Serializer($mapper, $parser);
        $person = $s->jsonUnmarshal($jsonContent, Person::class);
        $this->assertEquals(19, $person->age);
        $this->assertEquals("foo", $person->name);
        $this->assertEquals("xx", $person->address->city);
    }

    public function testJsonUnmarshal_Obejcts()
    {
        $jsonContent = '[{"age":19,"name":"foo","address":{"city":"xx"}}]';
        $parser = new Parser([], [new JsonEncoder()]);
        $mapper = new Mapper();
        $s = new Serializer($mapper, $parser);
        $person = $s->jsonUnmarshal($jsonContent, Person::class);
        $this->assertEquals(19, $person[0]->age);
        $this->assertEquals("foo", $person[0]->name);
        $this->assertEquals("xx", $person[0]->address->city);
    }

    public function testYamlUnmarshal()
    {
        $str = <<<Yaml
- age: 19
  name: foo
  address:
    city: xx
- age: 20
  name: bar
  address:
    city: oo    
Yaml;
        $parser = new Parser([], [new YamlEncoder()]);
        $mapper = new Mapper();
        $s = new Serializer($mapper, $parser);
        $person = $s->yamlUnmarshal($str, Person::class);
        $this->assertEquals(19, $person[0]->age);
        $this->assertEquals("foo", $person[0]->name);
        $this->assertEquals("xx", $person[0]->address->city);
        $this->assertEquals(20, $person[1]->age);
        $this->assertEquals("bar", $person[1]->name);
        $this->assertEquals("oo", $person[1]->address->city);
    }

    public function testXmlUnmarshal()
    {
        $xmlstr = <<<XML
<person>
<age>19</age>
<name>foo</name>
<address>
<city>xx</city>
</address>
</person>
XML;
        $parser = new Parser([], [new XmlEncoder()]);
        $mapper = new Mapper();
        $s = new Serializer($mapper, $parser);
        $person = $s->xmlUnmarshal($xmlstr, Person::class);
        $this->assertEquals(19, $person->age);
        $this->assertEquals("foo", $person->name);
        $this->assertEquals("xx", $person->address->city);
    }
}
