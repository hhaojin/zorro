<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:11
 */


namespace Zorro\Serialize;

use PHPUnit\Framework\TestCase;
use Zorro\Attribute\AttributeCollector;
use Zorro\BeanFactory;
use Zorro\Validation\Validate;

class Address
{
    public $city;
}

class Person
{
    #[Validate("gt=19")]
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
        $person = Json::Unmarshal($jsonContent, Person::class);
        $this->assertEquals(19, $person->age);
        $this->assertEquals("foo", $person->name);
        $this->assertEquals("xx", $person->address->city);
    }

    public function testJsonUnmarshal_Obejcts()
    {
        $jsonContent = '[{"age":19,"name":"foo","address":{"city":"xx"}}]';
        $person = Json::Unmarshal($jsonContent, Person::class);
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
        $person = Yaml::Unmarshal($str, Person::class);
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
        $person = Xml::Unmarshal($xmlstr, Person::class);
        $this->assertEquals(19, $person->age);
        $this->assertEquals("foo", $person->name);
        $this->assertEquals("xx", $person->address->city);
    }
}
