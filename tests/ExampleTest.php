<?php
namespace Tests;

use Example\Example;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_that_the_example_part_one()
    {
        $json = json_encode(["one"=>1, "two"=>2, "three"=>3]);
        $array = [1,2,3,4,5];

        $this->assertCount(5, [1,2,3,4,5]);

        $this->assertJson($json);

        $this->assertIsArray($array);
        $this->assertSameSize($array, [1,2,3,4,5]);
    }

    public function test_that_the_example_part_two()
    {
        $this->assertTrue(true);
        $this->assertFalse(false);

        $this->assertStringContainsString("test", "This is a test");
    }

    public function test_that_the_example_part_three()
    {
        $expected = "Value";

        $this->assertEquals($expected, "Value");
        $this->assertNotEquals($expected, "Nope");
        $this->assertIsString($expected);
    }

}
