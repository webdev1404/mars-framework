<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class JsonTest extends Base
{
    protected $data = ['test', '123'];

    protected $expected = '["test","123"]';

    public function testEncode()
    {
        $str = $this->app->json->encode(null);
        $this->assertSame($str, 'null');

        $str = $this->app->json->encode($this->data);
        $this->assertSame($str, $this->expected);
    }

    public function testDecode()
    {
        $data = $this->app->json->decode($this->expected);
        $this->assertSame($data, $this->data);
    }

    public function testValidate()
    {
        $this->assertFalse($this->app->json->validate(''));
        $this->assertTrue($this->app->json->validate($this->expected));
        $this->assertFalse($this->app->json->validate('invalid json string'));
    }
}
